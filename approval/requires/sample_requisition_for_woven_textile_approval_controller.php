<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);

$permission=$_SESSION['page_permission'];

include('../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$menu_id=$_SESSION['menu_id'];

if($db_type==0)
{
	$select_year="year";
	$year_format="";
	$group_concat="group_concat";
}
else if ($db_type==2)
{
	$select_year="to_char";
	$year_format=",'YYYY'";
	$group_concat="wm_concat";
}


if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$sequence_no='';
	$cbo_company_name=str_replace("'","",$cbo_company_name);
    $txt_system_id=str_replace("'","",$txt_system_id);
    $txt_date_from=str_replace("'","",trim($txt_date_from));
	$txt_date_to=str_replace("'","",trim($txt_date_to));

    $system_id_cond="";
	if($txt_system_id!="") $system_id_cond=" and a.system_number_prefix_num=$txt_system_id";
	
	$date_cond='';
    if($txt_date_from !="" && $txt_date_to!="") $date_cond=" and a.requisition_date between '".$txt_date_from."' and '".$txt_date_to."'";

	$approval_type=str_replace("'","",$cbo_approval_type);
	if($previous_approved==1 && $approval_type==1)
	{
		$previous_approved_type=1;
	}
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	if($txt_alter_user_id!="")
	{		
		$user_id=$txt_alter_user_id;	
	}

	//echo $menu_id;die;

    $company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
    $supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
    $user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );

	$print_report_format=return_field_value("format_id","lib_report_template","template_name =".$cbo_company_name." and module_id=19 and report_id=206 and is_deleted=0 and status_active=1");
    $format_ids=explode(",",$print_report_format);

	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted = 0");

	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted = 0");
	
	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority.</font>";die;
	}

	if($previous_approved==1 && $approval_type==1)	//approval process with prevous approve start
	{
		$sequence_no_cond=" and b.sequence_no<'$user_sequence_no'";
	
		// $sql="SELECT a.id, a.wo_number_prefix_num, a.wo_number, a.company_name, $select_year(a.insert_date $year_format) as year, a.wo_date, c.id as approval_id, a.approved 
		// from wo_sample_requisition_mst a, approval_history c 
		// where a.id=c.mst_id  and c.entry_form=80 and a.company_name=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and a.approved=0 and a.ready_to_approved=1 $system_id_cond $date_cond 
		// group by a.id, a.wo_number_prefix_num, a.wo_number, a.company_name, a.insert_date, a.wo_date, c.id, a.approved
       	// order by a.id";
		// //echo "$sql";

		$sql="SELECT a.id, a.system_number_prefix_num as wo_number_prefix_num, a.system_number as wo_number, a.company_id as company_name,$select_year(a.insert_date $year_format) as year, a.requisition_date as wo_date, c.id     AS approval_id, a.approved
			FROM wo_sample_requisition_mst a, approval_history c
		WHERE     a.id = c.mst_id AND c.entry_form = 80 AND a.company_id = $cbo_company_name AND a.is_deleted = 0 AND a.status_active = 1 AND a.approved = 0 AND a.ready_to_approved = 1 $system_id_cond $date_cond 
		GROUP BY a.id, a.system_number_prefix_num, a.system_number, a.company_id , a.insert_date, a.requisition_date, c.id, a.approved
		ORDER BY a.id";
	}
	else if($approval_type==0) 
	{
		if($user_sequence_no==$min_sequence_no) // First user
		{
            $sql="SELECT a.id, a.system_number_prefix_num as wo_number_prefix_num, a.system_number as wo_number, a.company_id as company_name, $select_year(a.insert_date $year_format) as year, a.requisition_date as wo_date, '0' AS approval_id, a.approved 
            from wo_sample_requisition_mst a
            where a.company_id=$cbo_company_name  and a.is_deleted=0 and a.status_active=1 and a.approved=0 and a.ready_to_approved=1 $system_id_cond $date_cond 
            group by a.id, a.system_number_prefix_num, a.system_number, a.company_id, a.insert_date, a.requisition_date, a.approved
            order by a.id";	
			
	
		}
		else // Next user
		{
			$sequence_no=return_field_value("max(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted = 0");
			if($sequence_no=="") // bypass if previous user Yes
			{
                $seqSql="select LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no_by from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0";
                $seqData=sql_select($seqSql);
                
                $sequence_no_by=$seqData[0][csf('sequence_no_by')];

                $work_order_id=return_field_value("LISTAGG(b.mst_id, ',') WITHIN GROUP (ORDER BY b.mst_id) as work_order_id","wo_sample_requisition_mst a, approval_history b","a.id=b.mst_id  and a.company_id=$cbo_company_name and b.sequence_no in ($sequence_no_by) and a.ready_to_approved=1 and b.entry_form=80 and b.current_approval_status=1 and a.approved in (3,1)","work_order_id");

                $work_order_id=implode(",",array_unique(explode(",",$work_order_id)));

                $work_order_id_app_byuser=return_field_value("LISTAGG(b.mst_id, ',') WITHIN GROUP (ORDER BY b.mst_id) as work_order_id","wo_sample_requisition_mst a, approval_history b","a.id=b.mst_id  and a.company_id=$cbo_company_name and a.ready_to_approved=1  and b.sequence_no=$user_sequence_no  and b.entry_form=80 and b.current_approval_status=1 and a.approved in (3,1)","work_order_id");
                $work_order_id_app_byuser=implode(",",array_unique(explode(",",$work_order_id_app_byuser)));

				$result=array_diff(explode(',',$work_order_id),explode(',',$work_order_id_app_byuser));
				$work_order_id=implode(",",$result);

				if($work_order_id!="")
				{
					
					$sql=" SELECT x.* from  (SELECT a.id, a.system_number_prefix_num as wo_number_prefix_num, a.system_number as wo_number, a.company_id as company_name, $select_year(a.insert_date $year_format) as year, a.requisition_date as wo_date, '0' AS approval_id, a.approved  
					from wo_sample_requisition_mst a
					where a.company_id=$cbo_company_name  and a.is_deleted=0 and a.status_active=1 and a.approved in(0,3) and a.id in ($work_order_id) $system_id_cond $date_cond    
					GROUP by a.id, a.system_number_prefix_num, a.system_number, a.company_id, a.insert_date, a.requisition_date, a.approved 

					UNION ALL

					SELECT a.id, a.system_number_prefix_num as wo_number_prefix_num, a.system_number as wo_number, a.company_id as company_name, $select_year(a.insert_date $year_format) as year, a.requisition_date as wo_date, '0' AS approval_id, a.approved
					from wo_sample_requisition_mst a
					where a.id not in ($work_order_id)  and a.approved=$approval_type and a.company_id=$cbo_company_name  and a.is_deleted=0 and a.status_active=1 $system_id_cond $date_cond
					GROUP by a.id, a.system_number_prefix_num, a.system_number, a.company_id, a.insert_date, a.requisition_date, a.approved  ) x  order by x.id";
					//echo $sql;
				}
				else
				{ 
					$sql=" SELECT a.id, a.system_number_prefix_num as wo_number_prefix_num, a.system_number as wo_number, a.company_id as company_name, $select_year(a.insert_date $year_format) as year, a.requisition_date as wo_date, '0' AS approval_id, a.approved
					from wo_sample_requisition_mst a 
					where a.approved=$approval_type  and a.company_id=$cbo_company_name and a.is_deleted=0 and a.status_active=1 $system_id_cond $date_cond
					GROUP by a.id, a.system_number_prefix_num, a.system_number, a.company_id, a.insert_date, a.requisition_date, a.approved 
					order by a.id";
					//echo $sql;
				}
				//echo $sql;
			}			
			else // if previous user bypass No 
			{
				$user_sequence_no=$user_sequence_no-1;
				//echo $sequence_no;
				if($sequence_no==$user_sequence_no) 
				{
					$sequence_no_by_pass=$sequence_no;
					$sequence_no_cond=" and c.sequence_no in ($sequence_no_by_pass)";
				}
				else
				{
					if($db_type==0) 
					{
						$sequence_no_by_pass=return_field_value("group_concat(sequence_no) as sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1","sequence_no");
					}
					else if($db_type==2) 
					{
						$sequence_no_by_pass=return_field_value("listagg(sequence_no,',') within group (order by sequence_no) as sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1","sequence_no");
					}
					
					if($sequence_no_by_pass=="") $sequence_no_cond=" and c.sequence_no='$sequence_no'";
					else $sequence_no_cond=" and c.sequence_no in ($sequence_no_by_pass)";
				}

				$sql=" SELECT a.id, a.system_number_prefix_num as wo_number_prefix_num, a.system_number as wo_number, a.company_id as company_name, $select_year(a.insert_date $year_format) as year, a.requisition_date as wo_date, c.id AS approval_id, a.approved
				from wo_sample_requisition_mst a, approval_history c 
				where a.approved in (1,3) and a.id=c.mst_id and a.entry_form=484 and c.entry_form in(80) and a.company_id=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and c.current_approval_status=1 $sequence_no_cond  $system_id_cond $date_cond
				GROUP by a.id, a.system_number_prefix_num, a.system_number, a.company_id, a.insert_date, a.requisition_date, c.id, a.approved
				order by a.id";
				//echo $sql;
			}
		}
	}
	else
	{
		$sequence_no_cond=" and c.sequence_no='$user_sequence_no'";
		$sql=" SELECT a.id, a.system_number_prefix_num as wo_number_prefix_num, a.system_number as wo_number, a.company_id as company_name, $select_year(a.insert_date $year_format) as year, a.requisition_date as wo_date, c.id AS approval_id, a.approved
		from wo_sample_requisition_mst a, approval_history c 
		where a.approved in (1,3) and a.id=c.mst_id  and c.entry_form in(80) and a.company_id=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and c.current_approval_status=1 $sequence_no_cond $system_id_cond $date_cond
		GROUP by a.id, a.system_number_prefix_num, a.system_number, a.company_id, a.insert_date, a.requisition_date, c.id, a.approved
		order by a.id";

		//echo $sql;die;
	}
	//echo $sql;die;
	?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:450px; margin-top:10px">
        <legend>Service Work Order Approval</legend>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="430" class="rpt_table" >
                <thead>
                	<th width="50"></th>
                    <th width="40">SL</th>
                    <th width="100">Company</th>
                    <th width="120">Work Order No</th>
                    <th>Work Order Date</th>
                </thead>
            </table>
            <div style="width:430px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="410" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <?						
                            $i=1;
                            $nameArray=sql_select( $sql );
                           
                            foreach ($nameArray as $row)
                            {
								$approval_id=implode(",",array_unique(explode(",",$row[csf('approval_id')])));
								if ($i%2==0) $bgcolor="#E9F3FF";
								else $bgcolor="#FFFFFF";
								
								$value='';
								if($approval_type==0)
								{
									$value=$row[csf('id')];
								}
								else
								{
									$app_id=return_field_value("id","approval_history","mst_id ='".$row[csf('id')]."' order by id desc");
									$value=$row[csf('id')]."**".$app_id;
								}	

								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" align="center">
                                	<td width="50" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" />
                                        <input id="work_order_id_<? echo $i;?>" name="work_order_id[]" type="hidden" value="<? echo $value; ?>" />
                                        <input id="workOrder_id_<? echo $i;?>" name="workOrder_id[]" type="hidden" value="<? echo $row[csf('id')]; ?>" />
                                        <input id="approval_id_<? echo $i;?>" name="approval_id[]" type="hidden" value="<? echo $approval_id; ?>" />
                                        <input id="<? echo strtoupper($row[csf('wo_number_prefix_num')]); ?>" name="no_gate_pass[]" type="hidden" value="<? echo $i;?>" />
                                    </td>   
									<td width="40" align="center"><? echo $i; ?></td>
									<td width="100"><? echo $company_arr[$row[csf('company_name')]]; ?></td>
                                    <td width="120" align="center"><a href='##' onClick="generate_report('<?=$row[csf('company_name')];?>','<?=$row[csf('wo_number')];?>','<?=$row[csf('id')];?>','Sample Requisition')"><b><? echo $row[csf('wo_number')]; ?></b></a></td>
                                    <td align="center"><? echo change_date_format($row[csf('wo_date')]); ?></td>
								</tr>
								<?
								$i++;
							}
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="430" class="rpt_table">
				<tfoot>
                    <td width="50" align="center" ><input type="checkbox" id="all_check" onclick="check_all('all_check')" /></td>
                    <td colspan="2" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Approved"; else echo "Approved"; ?>" class="formbutton" style="width:100px" onclick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>)"/></td>
				</tfoot>
			</table>
        </fieldset>
    </form>         
	<?
	exit();	
}


if ($action=="approve")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//$user_id=23;
	//echo "10**".$operation;die;
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	if($txt_alter_user_id!="") $user_id_approval=$txt_alter_user_id;
	else $user_id_approval=$user_id;
	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","page_id=$menu_id and user_id=$user_id_approval and company_id = $cbo_company_name and is_deleted = 0");
	// echo $user_sequence_no;die;

	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","page_id=$menu_id and is_deleted = 0");

	$msg=''; $flag=''; $response='';	
	if($approval_type==0)
	{
		$response=$workOrder_ids;
		// echo $officeNote_ids;die;

		//echo "SELECT sequence_no from electronic_approval_setup where company_id=$cbo_importer_name and page_id=$menu_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0";die;

		$is_not_last_user=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0");
        // echo $is_not_last_user;die;

        if($is_not_last_user!="") $partial_approval=3; else $partial_approval=1;
        // echo $partial_approval;die;

        $max_approved_no_arr = return_library_array("SELECT mst_id, max(approved_no) as approved_no from approval_history where mst_id in($workOrder_ids) and entry_form=80 group by mst_id","mst_id","approved_no");
		
		$approved_status_arr = return_library_array("SELECT id, is_approved from inv_gate_pass_mst where id in($workOrder_ids)","id","approved");

		$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date"; 
		$id=return_next_id( "id","approval_history", 1 ) ;

		$workOrder_ids_all=explode(",",$workOrder_ids);
		//$officeNote_nos_all=explode(",",$officeNote_nos);
		
		// ======================================================================== New
		$work_order_id_arr = array();
		for($i=0;$i<count($workOrder_ids_all);$i++)
		{
			//$val=$officeNote_nos_all[$i];
			$workOrder_id=$workOrder_ids_all[$i];

			$approved_no=$max_approved_no_arr[$workOrder_id];
			$approved_status=$approved_status_arr[$workOrder_id];
			
			if($approved_status==0)
			{
				$approved_no=$approved_no+1;
				$approved_no_array[$workOrder_id]=$approved_no;
				$work_order_id_arr[$workOrder_id] = $workOrder_id;
			}
			
			if($data_array!="") $data_array.=",";
			
			$data_array.="(".$id.",80,".$workOrder_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$user_id.",'".$pc_date_time."')"; 
				
			$id=$id+1;
			
		}

		if(count($approved_no_array)>0)
		{
			$approved_string="";

			foreach($approved_no_array as $key=>$value)
			{
				$approved_string.=" WHEN $key THEN $value";
			}

			$approved_string_mst="CASE id ".$approved_string." END";
			$approved_string_dtls="CASE mst_id ".$approved_string." END";

			$book_nos = implode(",",$work_order_id_arr);

		  $sql_insert="insert into wo_sample_requisition_mst_history( id,approved_no,requisition_id,system_number_prefix,system_number_prefix_num,system_number,buyer_inquiry_no,buyer_inquiry_id,basis,company_id,location_id,style_refernce,style_ref_id,within_group,buyer_id ,brand_id,season ,season_year ,team_leader,dealing_marchant,delivery_date,ready_to_approved,approved,approved_by,approved_date,requisition_date,remarks,copy_system_number,status_active,insert_by,insert_date,update_by,update_date,is_deleted)
				select
				'', $approved_string_mst,id,system_number_prefix,system_number_prefix_num,system_number,buyer_inquiry_no,buyer_inquiry_id,basis,company_id,location_id,style_refernce,style_ref_id,within_group,buyer_id ,brand_id,season ,season_year ,team_leader,dealing_marchant,delivery_date,ready_to_approved,approved,approved_by,approved_date,requisition_date,remarks,copy_system_number,status_active,insert_by,insert_date,update_by,update_date,is_deleted from wo_sample_requisition_mst where id in ($book_nos)";

		 $sql_insert_dtls="insert into wo_sample_requisition_dtls_history( id,approved_no,requisition_id,requisition_dtls_id,inquiry_dtls_id,determination_id,constuction_id,product_type,composition_id,weave_design,finish_type ,color_id,fabric_weight,fabric_weight_type ,finish_width,cutable_width,wash_type,offer_qnty ,uom,dispo_no,hl_no ,buyer_target_price,amount,status_active ,insert_by,  insert_date,update_by,update_date , is_deleted)
			select
			'', $approved_string_dtls,mst_id, id,inquiry_dtls_id,determination_id,constuction_id,product_type,composition_id,weave_design,finish_type ,color_id,fabric_weight,fabric_weight_type ,finish_width,cutable_width,wash_type,offer_qnty ,uom,dispo_no,hl_no ,buyer_target_price,amount,status_active ,insert_by,  insert_date,update_by,update_date , is_deleted from wo_sample_requisition_dtls where mst_id in ($book_nos)";

			$sql_insert_cons="insert into wo_work_order_cons_dtls_history( id,approved_no,trim_cost_dtls_id,dtls_id,mst_id,job_no,po_break_down_id,item_size,cons,place,pcs,country_id,excess_per,tot_cons,ex_cons,item_number_id,color_number_id,item_color_number_id,size_number_id,rate,amount,gmts_pcs,color_size_table_id,job_id,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted,rate_cal_data,cons_pcs,item_ref,qnty)
			select
			'', $approved_string_dtls, trim_cost_dtls_id,dtls_id,mst_id,job_no,po_break_down_id,item_size,cons,place,pcs,country_id,excess_per,tot_cons,ex_cons,item_number_id,color_number_id,item_color_number_id,size_number_id,rate,amount,gmts_pcs,color_size_table_id,job_id,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted,rate_cal_data,cons_pcs,item_ref,qnty from wo_work_order_cons_dtls where mst_id in ($book_nos)";

			$sql_insert_service="insert into wo_work_order_service_dtls_history( id,approved_no,trim_cost_dtls_id,dtls_id,mst_id,job_no,po_break_down_id,trim_group,current_stock,receive_uom,converted_uom,issue_qnty,converted_to,job_id,product_id,description,remarks,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted,charge_unit,amount,store_id)
			select
			'', $approved_string_dtls,trim_cost_dtls_id,dtls_id,mst_id,job_no,po_break_down_id,trim_group,current_stock,receive_uom,converted_uom,issue_qnty,converted_to,job_id,product_id,description,remarks,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted,charge_unit,amount,store_id from wo_work_order_service_dtls where mst_id in ($book_nos)";
		}

		

		//$data = $partial_approval."*".$user_id."*'".$pc_date_time."'";
    	$rID=sql_multirow_update("wo_sample_requisition_mst","approved",$partial_approval,"id",$workOrder_ids,1);
	 
    	if($rID) $flag=1; else $flag=0;

    	if($approval_ids!="")
		{
			$rIDapp=sql_multirow_update("approval_history","current_approval_status",0,"id",$approval_ids,1);
			if($flag==1) 
			{
				if($rIDapp) $flag=1; else $flag=0; 
			} 
		}
		$rID2=sql_insert("approval_history",$field_array,$data_array,1);
		//echo $rID2;return;
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		}

		if(count($approved_no_array)>0)
		{
		//	echo "10**".$sql_insert;die;
			$rID3=execute_query($sql_insert,1);
			if($flag==1)
			{
				if($rID3) $flag=1; else $flag=0;
			}

			$rID4=execute_query($sql_insert_dtls,1);
			if($flag==1)
			{
				if($rID4) $flag=1; else $flag=0;
			}
		}

		
			//echo "10**".$sqlpo;die;
        //echo "10**$flag";die;
		if($flag==1) $msg='19'; else $msg='21';

		//echo "10**"."insert into approval_history($field_array) values".$data_array;die;
		//echo "10**".$rID."=".$rID2."=".$rID3."=".$rID4."=".$rIDapp;die;
	}
	else
	{
		// echo($officeNote_ids);die;
		$workOrder_ids_all=explode(",",$workOrder_ids);
		$workOrder_ids=''; $app_ids='';

		foreach($workOrder_ids_all as $value)
		{
			$data = explode('**',$value);
			$workOrder_id=$data[0];
			$app_id=$data[1];
			if($workOrder_ids=='') $workOrder_ids=$workOrder_id; else $workOrder_ids.=",".$workOrder_id;
			if($app_ids=='') $app_ids=$app_id; else $app_ids.=",".$app_id;
		}

		$rID=sql_multirow_update("wo_sample_requisition_mst","approved*ready_to_approved","0*0","id",$workOrder_ids,1);
		if($rID) $flag=1; else $flag=0;
		$rID2=sql_multirow_update("approval_history","current_approval_status",0,"id",$approval_ids,1);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
		// echo $app_ids.'=Tipu';
		$data=$user_id_approval."*'".$pc_date_time."'*".$user_id."*'".$pc_date_time."'";
		$rID3=sql_multirow_update("approval_history","un_approved_by*un_approved_date*updated_by*update_date",$data,"id",$app_ids,1);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		}

		// echo "10**".$rID."=".$rID2."=".$rID3;die;

		$response=$workOrder_ids;
		if($flag==1) $msg='20'; else $msg='22';
	}
	
	if($db_type==0)
	{ 
		if($flag==1)
		{
			mysql_query("COMMIT");  
			echo $msg."**".$response;
		}
		else
		{
			mysql_query("ROLLBACK"); 
			echo $msg."**".$response;
		}
	}
	//release lock table   oci_commit($con); oci_rollback($con); 
	if($db_type==2 || $db_type==1 )
	{
		if($flag==1)
		{
			oci_commit($con);  
			echo $msg."**".$response;
		}
		else
		{
			oci_rollback($con); 
			echo $msg."**".$response;
		}
	}
	disconnect($con);
	die;
}


if($action=='user_popup')
{
	echo load_html_head_contents("Popup Info","../../",1, 1,'',1,'');
	?>	

	<script>

	// flowing script for multy select data------------------------------------------------------------------------------start;
	  function js_set_value(id)
	  { 
	 // alert(id)
		document.getElementById('selected_id').value=id;
		  parent.emailwindow.hide();
	  }

	// avobe script for multy select data------------------------------------------------------------------------------end;

	</script>

	<form>
	        <input type="hidden" id="selected_id" name="selected_id" /> 
	       <?php
	        $custom_designation=return_library_array( "select id,custom_designation from lib_designation ",'id','custom_designation');	
			 $Department=return_library_array( "select id,department_name from  lib_department ",'id','department_name');	;
			 	//$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_importer_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
				$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id and valid=1 and a.id!=$user_id  and b.is_deleted=0  and b.page_id=$menu_id order by sequence_no";
			 $arr=array (2=>$custom_designation,3=>$Department);
			 echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,","630","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
			?>
	        
	</form>
	<script language="javascript" type="text/javascript">
	  setFilterGrid("tbl_style_ref");
	</script>


	<?
}

?>
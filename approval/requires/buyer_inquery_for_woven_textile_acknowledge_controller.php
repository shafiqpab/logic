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
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
    $txt_system_id=str_replace("'","",$txt_system_id);
    $txt_date_from=str_replace("'","",trim($txt_date_from));
	$txt_date_to=str_replace("'","",trim($txt_date_to));

    $system_id_cond="";
	if($txt_system_id!="") $system_id_cond=" and a.system_number_prefix_num=$txt_system_id";
	if($cbo_buyer_name > 0) $buyer_name_cond=" and a.buyer_id=$cbo_buyer_name";
	
	$date_cond='';
    if($txt_date_from !="" && $txt_date_to!="") $date_cond=" and a.inquery_date between '".$txt_date_from."' and '".$txt_date_to."'";

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
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$wash_types = array(1=>"Wash",2=>"Non-Wash",3=>"Garmnets Wash",4=>"Enzyme Wash");
	$fabric_construction_name_arr=return_library_array( "select id,fabric_construction_name from lib_fabric_construction", "id", "fabric_construction_name" );
	$finish_types = array(1=>"Regular",2=>"Peach",3=>"Brush");

 

	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted = 0");

	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted = 0");
	
	 

	if( $approval_type==1)	//approval process with prevous approve start
	{
		//$sequence_no_cond=" and b.sequence_no<'$user_sequence_no'";
	
		$sql="SELECT  a.id, a.system_number_prefix, a.system_number_prefix_num, a.system_number, a.company_id,a.buyer_id,
		a.style_description,a.inquery_date ,b.constuction_id,b.composition_id,b.grey_constuction_id,b.weave,b.design,
		b.wash_type,b.color_id,b.determination_id, c.id     AS approval_id, a.approved,b.id as dtls_id,b.cutable_width ,b.finish_type ,b.fabric_weight from wo_buyer_inquery a ,wo_buyer_inquery_dtls b,approval_history c where a.id = c.mst_id AND c.entry_form = 83 and a.approved =1 and c.current_approval_status=1 and a.id=b.mst_id and a.status_active=1 AND a.company_id = $cbo_company_name $system_id_cond $buyer_name_cond order by a.id desc";		 
	}
	else if($approval_type==0) 
	{
            $sql="SELECT  a.id,b.id as dtls_id , a.system_number_prefix, a.system_number_prefix_num, a.system_number, a.company_id,a.buyer_id,
			a.style_description,a.inquery_date ,b.constuction_id,b.composition_id,b.grey_constuction_id,b.weave,b.design,
			b.wash_type,b.color_id,b.determination_id,b.cutable_width ,b.finish_type ,b.fabric_weight  from wo_buyer_inquery a ,wo_buyer_inquery_dtls b where a.id=b.mst_id and a.status_active=1   and a.approved=0  and a.ready_to_approved=1 AND a.company_id = $cbo_company_name $system_id_cond $buyer_name_cond  order by a.id desc";		 
	}
	 
	//echo $sql;die;
	?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:2050px; margin-top:10px">
        <legend>Service Work Order Approval</legend>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2020" class="rpt_table" >
                <thead>
                	<th width="50"></th>
                    <th width="40">SL</th>
                    <th width="100">Company</th>
                    <th width="120">Inquiry No</th>
					<th width="100">Inquiry Date</th>
					<th width="200">Fin. Construction</th>
					<th width="200">Grey Construction</th>
					<th width="200"> Fab. Composition</th>
					<th width="100">Weave</th>
					<th width="100">Design</th>
					<th width="100">Wash</th>
					<th width="100">Fab. Color</th>
					<th width="100">Finish Type</th>
					<th width="100">GSM</th>
					<th width="100">Cutable Width</th>
					<th width="110">Actual Construction</th>
                    <th>Comment</th>
                </thead>
            </table>
            <div style="width:2030px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2020" class="rpt_table" id="tbl_list_search" align="left">
                    <tbody>
                        <?						
                            $i=1;
                            $nameArray=sql_select( $sql );
                           
                            foreach ($nameArray as $row)
                            {
								$constuction_id=$row[csf("constuction_id")];
								$fabric_construction=return_field_value("epi || '*' || ppi ||'*' || warp_count || '*' || wrap_spandex || '*' || weft_count || '*' || weft_spandex as fab_con", "lib_fabric_construction", "id =$constuction_id", "fab_con");
								$composition_str = "";
								if(!empty($row[csf('determination_id')]))
								{
									$composition_str = $composition_arr[$row[csf('determination_id')]];
								}
								else
								{
									$compos = explode(",",$row[csf('composition_id')]);
									foreach($compos as $comp)
									{
										if($composition_str != "") $composition_str .= ",";
										$composition_str .= $composition[$comp];
									}
								}
								
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
										<input id="fabConstructionId_<?=$row[csf('dtls_id')];?>" name="fabConstructionId[]" type="hidden" value="" />
										<input id="fabConstruction_<?=$row[csf('dtls_id')];?>" name="fabConstruction[]" type="hidden" value="" />
										<input id="yarnCountDeterminationId_<?=$row[csf('dtls_id')];?>" name="yarnCountDeterminationId[]" type="hidden" value="<?=$row[csf('determination_id')];?>" />
										<input id="hiddDtlsId_<?=$i;?>" name="hiddDtlsId[]" type="hidden" value="<?=$row[csf('dtls_id')];?>" />
                                    </td>   
									<td width="40" align="center"><? echo $i; ?></td>
									<td width="100"><? echo $company_arr[$row[csf('company_id')]]; ?></td>
                                    <td width="120" align="center"><? echo $row[csf('system_number')]; ?></td>
									<td width="100"><? echo change_date_format($row[csf('inquery_date')]); ?></td>
									<td width="200"><? echo $fabric_construction_name_arr[$row[csf('constuction_id')]];; ?></td>
									<td width="200"><? echo $fabric_construction_name_arr[$row[csf('grey_constuction_id')]]; ?></td>
									<td width="200"><? echo $composition_str; ?></td>
									<td width="100"><? echo $row[csf('weave')]; ?></td>
									<td width="100"><? echo $row[csf('design')]; ?></td>
									<td width="100"><? echo $wash_types[$row[csf('wash_type')]]; ?></td>
									<td width="100"><? echo $color_arr[$row[csf('color_id')]]; ?></td>
									<td width="100"><? echo $finish_types[$row[csf('finish_type')]]; ?></td>
									<td width="100"><? echo $row[csf('fabric_weight')]; ?></td>
									<td width="100"><? echo $row[csf('cutable_width')]; ?></td>
									<td width="110"><input style="width:100px;" type="text" class="text_boxes"  id="txtconstruction_<?=$row[csf('dtls_id')];?>" name="txtconstruction[]" placeholder="browse" onClick="openmypage_fabric_cons(<?=$row[csf('constuction_id')];?>,<?=$row[csf('dtls_id')];?>);" value=""/></td>
                                    <td align="center"><input id="txt_comment" name="txt_comment[]" type="text" value="" style="width:140px"  class="text_boxes"/></td>
								</tr>
								<?
								$i++;
							}
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="2030" class="rpt_table">
				<tfoot>
                    <td width="50" align="center" ><input type="checkbox" id="all_check" onclick="check_all('all_check')" /></td>
                    <td colspan="12" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Acknowledge"; else echo "Acknowledge"; ?>" class="formbutton" style="width:100px" onclick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>)"/></td>
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

        $max_approved_no_arr = return_library_array("SELECT mst_id, max(approved_no) as approved_no from approval_history where mst_id in($workOrder_ids) and entry_form=83 group by mst_id","mst_id","approved_no");
		
		 

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
			 
			$approved_no=$approved_no+1;
			$workOrder_id=$workOrder_ids_all[$i];
			$approved_no_array[$workOrder_id]=$approved_no;
			$work_order_id_arr[$workOrder_id] = $workOrder_id;

			if($data_array!="") $data_array.=",";
			
			$data_array.="(".$id.",83,".$workOrder_id.",'".$approved_no."','".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$user_id.",'".$pc_date_time."')"; 
				
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

			$sql_insert="insert into wo_buyer_inquery_mst_history( id,approved_no,buyer_inquiry_id,system_number_prefix,system_number_prefix_num,system_number,company_id,within_group,buyer_id,style_refernce,style_description,style_ref_id,inquery_date,season,season_year,brand_id,buyer_request,team_leader,dealing_marchant,est_ship_date,req_quotation_date,target_sam_sub_date,actual_sam_send_date,actual_req_quot_date,priority,status_active,concern_marchant,age_range,end_use,wash,light_source,remarks,copy_system_number,insert_by,insert_date,update_by,update_date,is_deleted,attention,sales_account,approved,ready_to_approved
				)
				select
				'', $approved_string_mst,id,system_number_prefix,system_number_prefix_num,system_number,company_id,within_group,buyer_id,style_refernce,style_description,style_ref_id,inquery_date,season,season_year,brand_id,buyer_request,team_leader,dealing_marchant,est_ship_date,req_quotation_date,target_sam_sub_date,actual_sam_send_date,actual_req_quot_date,priority,status_active,concern_marchant,age_range,end_use,wash,light_source,remarks,copy_system_number,insert_by,insert_date,update_by,update_date,is_deleted,attention,sales_account,approved,ready_to_approved from wo_buyer_inquery where id in ($book_nos)";

			$sql_insert_dtls="insert into wo_buyer_inquery_dtls_history( id,approved_no,inquiry_mst_id,inquiry_dtls_id,constuction_id,product_type,weave_design,finish_type,color_id,fabric_weight,fabric_weight_type,finish_width,cutable_width,wash_type,offer_qnty,uom,dispo_no,buyer_target_price,amount,status_active,insert_by,insert_date,update_by,update_date,is_deleted,composition_id,determination_id,warp_yarn_type,weft_yarn_type,weave,design,grey_width,grey_constuction_id,grey_determination_id,remarks)
			select
			'', $approved_string_dtls,mst_id,id,constuction_id,product_type,weave_design,finish_type,color_id,fabric_weight,fabric_weight_type,finish_width,cutable_width,wash_type,offer_qnty,uom,dispo_no,buyer_target_price,amount,status_active,insert_by,insert_date,update_by,update_date,is_deleted,composition_id,determination_id,warp_yarn_type,weft_yarn_type,weave,design,grey_width,grey_constuction_id,grey_determination_id,remarks from wo_buyer_inquery_dtls where mst_id in ($book_nos)";

				


		}
			//echo "10**".$sql_insert_dtls;die;
		//$data = $partial_approval."*".$user_id."*'".$pc_date_time."'";
    	$rID=sql_multirow_update("wo_buyer_inquery","approved",$partial_approval,"id",$workOrder_ids,1);
	 
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

		 if($flag==1){

				$field_array_up="constuction_id*update_by*update_date";

			for($i=1; $i< $total_row; $i++)
			{
	
				$hiddDtlsId="hiddDtlsId_".$i; 
				$hiddDtlsId=str_replace("'",'',$$hiddDtlsId);
				$fab_construction_id="fabConstructionId_".$hiddDtlsId; 
				$fab_construction_id=str_replace("'",'',$$fab_construction_id);
				$fabConstruction="fabConstruction_".$hiddDtlsId; 
				$fabConstruction=str_replace("'",'',$$fabConstruction);
				$txtconstruction="txtconstruction_".$hiddDtlsId; 
				$txtconstruction=str_replace("'",'',$$txtconstruction);
				 

				
				if ($hiddDtlsId!="")
				{
					if($fab_construction_id !="")
					{
						
						 
						$fab_construction_id=return_field_value("id", "lib_fabric_construction","fabric_construction_name='$txtconstruction'",  "id");
						if($fab_construction_id !="")
						{
							$fab_construction=explode("*",$fabConstruction);
							$fab_construction_id = return_next_id( "id", "lib_fabric_construction", 1 );

							$field_array1="id,fabric_construction_name,epi,ppi,warp_count,weft_count,wrap_spandex,weft_spandex,status_active,inserted_by,insert_date";
							$data_array1="(".$fab_construction_id.",'".$txtconstruction."','".$fab_construction[0]."','".$fab_construction[1]."','".$fab_construction[2]."','".$fab_construction[4]."','".$fab_construction[3]."','".$fab_construction[5]."',1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

							//echo "10**"."insert into lib_fabric_construction($field_array1) values".$data_array1;die;
							
							
							$rIDCon=sql_insert("lib_fabric_construction",$field_array1,$data_array1,1);
							if($rIDCon == false ) $flag = false;

							$wrap_details = explode(",",$fab_construction[2]);
							$fab_dts_id = return_next_id( "id", "lib_fab_construction_dtls", 1 );
							$field_details="id,mst_id,type,counts,count_type,yarn_composition_id";
							$data_details="";
							foreach($wrap_details as $wrap_d)
							{
								$wr_exp = explode("_",$wrap_d);
								if(!empty($data_details))
								{
									$data_details .=",";
								}
								$data_details.="(".$fab_dts_id.",".$fab_construction_id.",1,'".$wr_exp[0]."','".$wr_exp[1]."','".$wr_exp[2]."')";
								
								$fab_dts_id++;
							}
							$weft_details = explode(",",$fab_construction[4]);
							foreach($weft_details as $wrap_d)
							{
								$wr_exp = explode("_",$wrap_d);
								if(!empty($data_details))
								{
									$data_details .=",";
								}
								$data_details.="(".$fab_dts_id.",".$fab_construction_id.",2,'".$wr_exp[0]."','".$wr_exp[1]."','".$wr_exp[2]."')";
								$fab_dts_id++;
							}

							$rIDCon1=sql_insert("lib_fab_construction_dtls",$field_details,$data_details,0);
							if($rIDCon1 == false)
							{
								$flag = false;
							}
						}

						$id_arr[]=$hiddDtlsId;
						$data_array_up[$hiddDtlsId]=explode("*",("".$fab_construction_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
						 $rID4=execute_query(bulk_update_sql_statement("wo_buyer_inquery_dtls", "id",$field_array_up,$data_array_up,$id_arr ));
						// echo "10**".bulk_update_sql_statement("wo_buyer_inquery_dtls", "id",$field_array_up,$data_array_up,$id_arr );
					}

				}
				

		   } 
		   
		//    if(count($data_array_up)>0){
		// 	$rID4=execute_query(bulk_update_sql_statement("wo_buyer_inquery_dtls", "id",$field_array_up,$data_array_up,$id_arr ));
		//    }

			
		   
		    
		}

		
			
		if($flag==1) $msg='19'; else $msg='21';

		//echo "10**"."insert into approval_history($field_array) values".$data_array;die;
		//  echo "10**".$rID."=".$rID2."=".$rIDCon."=".$rIDCon1."=".$rID4."=>>".count($data_array_up);die;
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

		$rID=sql_multirow_update("wo_buyer_inquery","approved*ready_to_approved","0*0","id",$workOrder_ids,1);
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

		 
	
		 //echo "10**".$rID."=".$rID2."=".$rID3."=".$rID4;die;

		$response=$workOrder_ids;
		if($flag==1) $msg='20'; else $msg='22';
	}
	
	if($db_type==0)
	{ 
		if($rID2)
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
		if($rID2)
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


 


if ($action == "load_drop_down_buyer") {
	 
	$company_id = $data;
	echo create_drop_down("cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1)) group by buy.id, buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "", 0);

	exit();
}


if($action=="fabric_construction_popup")
{
	echo load_html_head_contents("Material Construction Info","../../", 1, 1, '','1','');
	extract($_REQUEST);	
	$lib_composition=return_library_array( "select id,composition_name from lib_composition_array where   status_active in(1,2)", "id", "composition_name");
	
	?>
	<script>
		var spandexarr = "";var compositionArr = "";
		<?
			$data_array= json_encode( $spandex_arr );
			echo "spandexarr = ". $data_array . ";\n";
			if(count($lib_composition)>0){
				$data_array2= json_encode( $lib_composition );
				echo "compositionArr = ". $data_array2 . ";\n";
			}
		?>
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		function js_set_value(id,name,epi,ppi,warp_count,warp_spandex,weft_count,weft_spandex,str)
		{
			console.log(id +'='+ name);
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			document.getElementById('hidfabconspid').value=id;
			document.getElementById('hidfabconsname').value=name;
			document.getElementById('txt_epi').value=epi;
			document.getElementById('txt_ppi').value=ppi;
			document.getElementById('txt_wrap_spandex').value=warp_spandex;
			document.getElementById('txt_weft_spandex').value=weft_spandex;

			warp_count = warp_count.split(",");
			var j = 1;
			for( let i = 0; i < warp_count.length; i++)
			{
				var wrap = warp_count[i].split("_");
				$("#txtWarpCount_"+j).val(wrap[0]);
				$("#cboWarpType_"+j).val(wrap[1]);
				$("#cboWarpCom_"+j).val(wrap[2]);
				if(j < warp_count.length)
				{
					add_wrap(j);
				}
				j++;

			}

			weft_count = weft_count.split(",");
			var j = 1;
			for( let i = 0; i < weft_count.length; i++)
			{
				var wrap = weft_count[i].split("_");
				$("#txtWeftCount_"+j).val(wrap[0]);
				$("#cboWeftType_"+j).val(wrap[1]);
				$("#cboWeftCom_"+j).val(wrap[2]);
				if(j < weft_count.length)
				{
					add_weft(j);
				}
				j++;
			}

			
			
			//parent.emailwindow.hide();
		}
		function ClosePopup()
		{
			var txt_epi = document.getElementById('txt_epi').value ;
			var txt_ppi = document.getElementById('txt_ppi').value ;
			var calculated_gsm = 0;
			var row_num=$('#tbl_warp_list tbody tr').length;
			var wrap_str = "";
			var wrap_id_str = "";
			var str_composition = "";
			var s1 = "<sub>";
			var s2 = "</sub>";
			for(var i = 1; i <= row_num; i++)
			{
				var txtWarpCount = $("#txtWarpCount_"+i).val() * 1;
				var cboWarpType  = $("#cboWarpType_"+i).val() * 1;
				var cboWarpCom  = $("#cboWarpCom_"+i).val() * 1;
				if( i > 1)
				{
					wrap_str += '+';
					wrap_id_str += ',';
					str_composition += ',';
				}
				wrap_str += txtWarpCount + 'x' + spandexarr[cboWarpType]+ "_"+compositionArr[cboWarpCom];
				wrap_id_str += txtWarpCount + "_"+cboWarpType+ "_"+cboWarpCom;
				str_composition += compositionArr[cboWarpCom];
				calculated_gsm += ( ( txt_epi * 1 ) / ( txtWarpCount * 1) ) * 23.25;
			}

			row_num=$('#tbl_weft_list tbody tr').length;
			var weft_str = "";
			var weft_id_str = "";

			for(var i = 1; i <= row_num; i++)
			{
				var txtWeftCount = $("#txtWeftCount_"+i).val() * 1;
				var cboWeftType  = $("#cboWeftType_"+i).val() * 1;
				var cboWeftCom  = $("#cboWeftCom_"+i).val() * 1;
				if( i > 1)
				{
					weft_str += '+';
					weft_id_str += ',';
				}
				if(str_composition !=""){
					str_composition += ',';
				}
				weft_str += txtWeftCount + 'x' + spandexarr[cboWeftType]+ "_"+compositionArr[cboWeftCom];;
				weft_id_str += txtWarpCount + "_"+cboWeftType+ "_"+cboWeftCom;;
				str_composition += compositionArr[cboWeftCom];
				calculated_gsm += ( ( txt_ppi * 1 ) / ( txtWeftCount * 1) ) * 23.25;
			}

			var txt_wrap_spandex = document.getElementById('txt_wrap_spandex').value;
			var wrap_spn = '';
			if(txt_wrap_spandex.length > 0 )
			{ 
				wrap_spn = "+" +txt_wrap_spandex + "D" ;
			}

			var txt_weft_spandex = document.getElementById('txt_weft_spandex').value;
			var weft_spn = '';
			if(txt_weft_spandex.length > 0 )
			{
				weft_spn = "+" +txt_weft_spandex + "D" ;
			}

			document.getElementById('hidfabconsname').value = txt_epi + "x" + txt_ppi + "/" + wrap_str + wrap_spn + "x" + weft_str + weft_spn;
			console.log(txt_epi + "x" + txt_ppi + "/" + wrap_str + wrap_spn + "x" + weft_str + weft_spn);
			document.getElementById('fab_construction').value = txt_epi + "*" + txt_ppi + "*" + wrap_id_str + "*" + txt_wrap_spandex + "*" + weft_id_str + "*" + txt_weft_spandex;
			document.getElementById('txt_calculated_gsm').value = calculated_gsm;
			parent.emailwindow.hide();
		}

		function add_wrap(i) 
		{
			var row_num=$('#tbl_warp_list tbody tr').length;
			if (row_num!=i)
			{
				return false;
			}
			else
			{
				i++;
				 $("#tbl_warp_list tbody tr:last").clone().find("input,select").each(function() {
					$(this).attr({
					  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					  'name': function(_, name) { return name + i },
					  'value': function(_, value) { return value }              
					});  
				  }).end().appendTo("#tbl_warp_list tbody");
				$('#wrapInc_'+i).removeAttr("onClick").attr("onClick","add_wrap("+i+");");
				$('#wrapDecre_'+i).removeAttr("onClick").attr("onClick","delete_wrap("+i+");");
			}
		}

		function delete_wrap(rowNo)
		{
			var index=rowNo-1;
			$("#tbl_warp_list tbody tr:eq("+index+")").remove();
			var numRow = $('#tbl_warp_list tbody tr').length;
			for(i = rowNo;i <= numRow;i++)
			{
				$("#tbl_warp_list tr:eq("+i+")").find("input,select").each(function() {
					$(this).attr({
						'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
						'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
						'value': function(_, value) { return value }
					});

					$('#weftInc_'+i).removeAttr("onClick").attr("onClick","add_weft("+i+");");
					$('#weftDecre_'+i).removeAttr("onClick").attr("onClick","delete_weft("+i+");");
				});
	        }
		}

		function add_weft(i)
		{

			var row_num=$('#tbl_weft_list tbody tr').length;
			if (row_num!=i)
			{
				return false;
			}
			else
			{
				i++;
				 $("#tbl_weft_list tbody tr:last").clone().find("input,select").each(function() {
					$(this).attr({
					  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					  'name': function(_, name) { return name + i },
					  'value': function(_, value) { return value }              
					});  
				  }).end().appendTo("#tbl_weft_list tbody");
				$('#weftInc_'+i).removeAttr("onClick").attr("onClick","add_weft("+i+");");
				$('#weftDecre_'+i).removeAttr("onClick").attr("onClick","delete_weft("+i+");");
			}
		}

		function delete_weft(rowNo)
		{
			var index=rowNo-1;
			$("#tbl_weft_list tbody tr:eq("+index+")").remove();
			var numRow = $('#tbl_weft_list tbody tr').length;
			for(i = rowNo;i <= numRow;i++)
			{
				$("#tbl_weft_list tr:eq("+i+")").find("input,select").each(function() {
					$(this).attr({
						'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
						'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
						'value': function(_, value) { return value }
					});

					$('#weftInc_'+i).removeAttr("onClick").attr("onClick","add_weft("+i+");");
					$('#weftDecre_'+i).removeAttr("onClick").attr("onClick","delete_weft("+i+");");
				});
	        }
		}
		


    </script>
	</head>
	<body>
		
		<fieldset style="width:850px;margin-left:10px">
	        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	        	<table cellpadding="0" cellspacing="0" border="1" rules="all" width="1000" class="rpt_table">
	        		<thead>
	        			<tr>
	        				<th>EPI</th>
	        				<th>PPI</th>
	        				<th>Warp Count</th>
	        				<th>Weft Count</th>
	        					<input type="hidden" name="hidfabconspid" id="hidfabconspid" value="" >
	                            <input type="hidden" name="hidfabconsname" id="hidfabconsname" value="" >
	                            <input type="hidden" name="fab_construction" id="fab_construction" value="" >
	                            <input type="hidden" name="txt_calculated_gsm" id="txt_calculated_gsm" value="" >
	        			</tr>
	        		</thead>
	        		<tbody>
	        			
	        			<tr>
	        				<td>
	        					<input type="text" name="txt_epi" class="text_boxes" id="txt_epi" value="" style="width:70px">
	        				</td>
	        				<td>
	        					<input type="text" name="txt_ppi" class="text_boxes" id="txt_ppi" value="" style="width:70px">
	        				</td>
	        				<td width="330">
	        					<table>
	        						<tr>
	        							<td>
	        								<table cellpadding="0" cellspacing="0" border="1" rules="all" width="330" class="rpt_table" id="tbl_warp_list">
	        									<thead>
	        										<tr>
	        											<th>Count</th>
	        											<th>Type</th>	
														<th>Composition</th>        											
	        											<th>Action</th>
	        										</tr>
	        									</thead>
	        									<tbody>
	        										<tr>
	        											<td>
	        												<input type="text" name="txtWarpCount_1" class="text_boxes" id="txtWarpCount_1" value="" style="width:70px">
	        											</td>
	        											<td>
	        												
	        												<? echo create_drop_down( "cboWarpType_1", 70, $spandex_arr, "",1," -- Select Count --", '', '','','' ); ?>
	        											</td>
														<td>
	        												
	        												<? echo create_drop_down( "cboWarpCom_1", 100, "select id, composition_name, yarn_category_type, status_active,is_fabric from lib_composition_array where status_active in(1,2) and is_deleted=0", "id,composition_name",1," -- Select Count --", '', '','','' ); ?>
	        											</td>
	        											
	        											<td width="80px">
	        												<input type="button" name="wrapInc_1" id="wrapInc_1" class="formbutton" value="+" onclick="add_wrap(1)" style="width:30px;">
	        												<input type="button" name="wrapDecre_1" id="wrapDecre_1" class="formbutton" value="-" onclick="delete_wrap(1)" style="width:30px;">
	        											</td>
	        										</tr>
	        									</tbody>
	        								</table>
	        							</td>
	        							<td>
	        								<table cellpadding="0" cellspacing="0" border="1" rules="all" width="90" class="rpt_table" >
	        									<thead>
	        										<tr>
	        											<th>Spandex</th>
	        										</tr>
	        									</thead>
	        									<tbody>
	        										<tr>
	        											<td>
	        												
	        												<input type="text" name="txt_wrap_spandex" id="txt_wrap_spandex" class="text_boxes" style="width:70px;">
	        											</td>
	        										</tr>
	        									</tbody>
	        								</table>
	        							</td>
	        						</tr>
	        					</table>
	        					
	        					
	        				</td>
	        				<td width="400">
	        					<table>
	        						<tr>
	        							<td>
	        								<table cellpadding="0" cellspacing="0" border="1" rules="all" width="330" class="rpt_table" id="tbl_weft_list">
	        									<thead>
	        										<tr>
	        											<th>Count</th>
	        											<th>Type</th>
														<th>Composition</th>
	        											<th>Action</th>
	        										</tr>
	        									</thead>
	        									<tbody>
	        										<tr>
	        											<td>
	        												<input type="text" name="txtWeftCount_1" class="text_boxes" id="txtWeftCount_1" value="" style="width:70px">
	        											</td>
	        											<td>
	        												<? echo create_drop_down( "cboWeftType_1", 70, $spandex_arr, "",1," -- Select Count --", '', '','','' ); ?>
	        											</td>
														<td>
	        												<? echo create_drop_down( "cboWeftCom_1", 100, "select id, composition_name, yarn_category_type, status_active,is_fabric from lib_composition_array where status_active in(1,2) and is_deleted=0", "id,composition_name",1," -- Select Count --", '', '','','' ); ?>
	        											</td>
	        											
	        											<td width="80px">
	        												<input type="button" name="weftInc_1" id="weftInc_1" class="formbutton" value="+" onclick="add_weft(1)" style="width:30px;">
	        												<input type="button" name="weftDecre_1" id="weftDecre_1" class="formbutton" value="-" onclick="delete_weft(1)" style="width:30px;">
	        											</td>
	        										</tr>
	        									</tbody>
	        								</table>
	        							</td>
	        							<td>
	        								<table cellpadding="0" cellspacing="0" border="1" rules="all" width="90" class="rpt_table" >
	        									<thead>
	        										<tr>
	        											<th>Spandex</th>
	        										</tr>
	        									</thead>
	        									<tbody>
	        										<tr>
	        											<td>
	        												
	        												<input type="text" name="txt_weft_spandex" id="txt_weft_spandex" class="text_boxes" style="width:70px;">
	        											</td>
	        										</tr>
	        									</tbody>
	        								</table>
	        							</td>
	        						</tr>
	        					</table>
	        					
		        			</td>
		        			
	        			</tr>
	        			
	        		</tbody>
	        	</table>  
	        </form>
	    </fieldset>
	    <table cellpadding="0" cellspacing="0" border="1" rules="all" width="830" class="rpt_table" >
        	<thead>
                <tr>
                	<th width="30">SL</th>
                	<th>Material Construction</th>
                </tr>
            </thead>
        </table>
        <div style="max-height:240px;overflow-y: scroll;width: 850px;">
	        <table cellpadding="0" cellspacing="0" border="1" rules="all" width="830" class="rpt_table" id="fab_cons_tbl">
	            <tbody >

	                <? 
	                
	                $fabric_construction = sql_select("select id, fabric_construction_name,epi,ppi,warp_count,weft_count,lakra,wrap_spandex,weft_spandex from  lib_fabric_construction where status_active=1 and is_deleted=0 order by fabric_construction_name");
	                $i=1; 
	                $epi = '';
	                $ppi = '';
	                $warp_count = '';
	                $weft_count = '';
	                $wrap_spandex = '';
	                $weft_spandex = '';
	                $fab_cons_name = '';
	                foreach($fabric_construction as $row) 
	                { 
	                	if($i%2==0) $bgcolor="#E9F3FF"; 
	                	else $bgcolor="#FFFFFF";
	                	$id= $row[csf('id')];
	                	
	           
	                	if($fab_construction_id == $id)
	                	{
	                		$bgcolor        ="yellow";
	                		$fab_cons_name  = $row[csf('fabric_construction_name')];
		                	$epi 			= $row[csf('epi')];
		                	$ppi 			= $row[csf('ppi')];
		                	$warp_count 	= $row[csf('warp_count')];
		                	$weft_count 	= $row[csf('weft_count')];
		                	$wrap_spandex 	= $row[csf('wrap_spandex')];
		                	$weft_spandex 	= $row[csf('weft_spandex')];
	                	} 
	                	$fab_cons 			= $row[csf('fabric_construction_name')];
	                	$repi 				= $row[csf('epi')];
	                	$rppi 				= $row[csf('ppi')];
	                	$rwarp_count 		= $row[csf('warp_count')];
	                	$rweft_count 		= $row[csf('weft_count')];
	                	$rwrap_spandex 		= $row[csf('wrap_spandex')];
	                	$rweft_spandex 		= $row[csf('weft_spandex')];
	                	?>
	                	<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $id; ?>,'<? echo $fab_cons; ?>','<? echo $repi; ?>','<? echo $rppi; ?>','<? echo $rwarp_count; ?>','<? echo $rwrap_spandex; ?>','<? echo $rweft_count; ?>','<? echo $rweft_spandex; ?>',<? echo $i;?>)">
	                        <td width="30"><? echo $i; ?></td>
	                        <td><? echo $fab_cons; ?> </td> 						
	                    </tr>
	                	<? 
	                	$i++; 
	            	} 
	            	?>
	            </tbody>
	    	</table>
	    </div>
    	<center><input type="button" value="Close" class="formbutton" onclick="ClosePopup()"></center>
    	
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>
    	setFilterGrid('fab_cons_tbl',-1);
    	<?
    		if(!empty($fab_construction_id))
    		{
    			?>
    			js_set_value('<? echo $fab_construction_id; ?>','<? echo $fab_cons_name; ?>','<? echo $epi; ?>','<? echo $ppi; ?>','<? echo $warp_count; ?>','<? echo $wrap_spandex; ?>','<? echo $weft_count; ?>','<? echo $weft_spandex; ?>');
    			<?
    		}
    	?>
    	
    	
    </script>
	</html>
	<?
	exit();
}
?>
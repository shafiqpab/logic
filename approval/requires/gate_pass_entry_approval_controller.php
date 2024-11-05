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
    $cbo_basis=str_replace("'","",$cbo_basis);
    $txt_system_id=str_replace("'","",$txt_system_id);
    $txt_date_from=str_replace("'","",trim($txt_date_from));
	$txt_date_to=str_replace("'","",trim($txt_date_to));


	$gate_pass_print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$cbo_company_name."' and module_id=6 and report_id=38 and is_deleted=0 and status_active=1");	
    $gate_format_ids=explode(",",$gate_pass_print_report_format);
    $print_btn=$gate_format_ids[0];

      $system_id_cond=$basis_cond="";
	if($txt_system_id!="") $system_id_cond=" and a.sys_number_prefix_num=$txt_system_id";
    if($cbo_basis>0) $basis_cond=" and a.basis=$cbo_basis";
	
	$date_cond='';
    if($txt_date_from !="" && $txt_date_to!="") $date_cond=" and a.out_date between '".$txt_date_from."' and '".$txt_date_to."'";

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

    $company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
    $supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
    $user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );


	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted = 0");

	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted = 0");
	
	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority.</font>";die;
	}

	if($previous_approved==1 && $approval_type==1)	//approval process with prevous approve start
	{
		$sequence_no_cond=" and b.sequence_no<'$user_sequence_no'";  
	
		$sql="SELECT a.id, a.sys_number_prefix_num, a.sys_number, a.basis, a.company_id, $select_year(a.insert_date $year_format) as year, a.out_date, c.id as approval_id, a.approved , a.com_location_id ,a.challan_no,a.returnable
		from inv_gate_pass_mst a, approval_history c 
		where a.id=c.mst_id and c.entry_form=59 and a.company_id=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and a.approved=0 and a.ready_to_approved=1 $basis_cond $system_id_cond $date_cond 
		group by a.id, a.sys_number_prefix_num, a.sys_number, a.basis, a.company_id, a.insert_date, a.out_date, c.id, a.approved,a.com_location_id ,a.challan_no,a.returnable
       	order by a.id";
		//echo "$sql";
	}
	else if($approval_type==0) 
	{
		if($user_sequence_no==$min_sequence_no) // First user
		{
            $sql="SELECT a.id, a.sys_number_prefix_num, a.sys_number, a.basis, a.company_id, $select_year(a.insert_date $year_format) as year, a.out_date, '0' AS approval_id, a.approved ,a.com_location_id ,a.challan_no,a.returnable
            from inv_gate_pass_mst a
            where a.company_id=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and a.approved=0 and a.ready_to_approved=1 $basis_cond $system_id_cond $date_cond 
            group by a.id, a.sys_number_prefix_num, a.sys_number, a.basis, a.company_id, a.insert_date, a.out_date, a.approved,a.com_location_id ,a.challan_no,a.returnable
            order by a.id";		 	
		 	//echo $sql;die;
		}
		else // Next user
		{
			$sequence_no=return_field_value("max(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted = 0");
			if($sequence_no=="") // bypass if previous user Yes
			{
                $seqSql="select LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no_by from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0";
                $seqData=sql_select($seqSql);
                
                $sequence_no_by=$seqData[0][csf('sequence_no_by')];

                $gate_pass_id=return_field_value("LISTAGG(b.mst_id, ',') WITHIN GROUP (ORDER BY b.mst_id) as gate_pass_id","inv_gate_pass_mst a, approval_history b","a.id=b.mst_id  and a.company_id=$cbo_company_name and b.sequence_no in ($sequence_no_by) and a.ready_to_approved=1 and b.entry_form=59 and b.current_approval_status=1 and a.approved in (3,1)","gate_pass_id");

                $gate_pass_id=implode(",",array_unique(explode(",",$gate_pass_id)));
                
                $gate_pass_id_app_byuser=return_field_value("LISTAGG(b.mst_id, ',') WITHIN GROUP (ORDER BY b.mst_id) as gate_pass_id","inv_gate_pass_mst a, approval_history b","a.id=b.mst_id and a.company_id=$cbo_company_name and a.ready_to_approved=1  and b.sequence_no=$user_sequence_no  and b.entry_form=59 and b.current_approval_status=1 and a.approved in (3,1)","gate_pass_id");
                $gate_pass_id_app_byuser=implode(",",array_unique(explode(",",$gate_pass_id_app_byuser)));

				$result=array_diff(explode(',',$gate_pass_id),explode(',',$gate_pass_id_app_byuser));
				$gate_pass_id=implode(",",$result);

				if($gate_pass_id!="")
				{
					$sql=" SELECT x.* from  (SELECT a.id, a.sys_number_prefix_num, a.sys_number, a.basis, a.company_id, $select_year(a.insert_date $year_format) as year,  a.out_date, '0' AS approval_id, a.approved, a.com_location_id ,a.challan_no,a.returnable
					from inv_gate_pass_mst a
					where a.company_id=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and a.approved in(0,3) and a.id in ($gate_pass_id) $basis_cond $system_id_cond $date_cond    
					GROUP by a.id, a.sys_number_prefix_num, a.sys_number, a.basis, a.company_id, a.insert_date, a.out_date, a.approved,a.com_location_id ,a.challan_no,a.returnable

					UNION ALL

					SELECT a.id, a.sys_number_prefix_num, a.sys_number, a.basis, a.company_id, $select_year(a.insert_date $year_format) as year,  a.out_date, '0' AS approval_id, a.approved ,a.com_location_id ,a.challan_no,a.returnable
					from inv_gate_pass_mst a
					where a.id not in ($gate_pass_id) and a.approved=$approval_type and a.company_id=$cbo_company_name  and a.is_deleted=0 and a.status_active=1 $basis_cond $system_id_cond $date_cond
					GROUP by a.id, a.sys_number_prefix_num, a.sys_number, a.basis, a.company_id, a.insert_date, a.out_date, a.approved, a.com_location_id ,a.challan_no,a.returnable  ) x  order by x.id";
					//echo $sql;
				}
				else
				{ 
					$sql=" SELECT a.id, a.sys_number_prefix_num, a.sys_number, a.basis, a.company_id, $select_year(a.insert_date $year_format) as year,  a.out_date, '0' AS approval_id, a.approved,a.com_location_id ,a.challan_no,a.returnable
					from inv_gate_pass_mst a 
					where a.approved=$approval_type and a.company_id=$cbo_company_name and a.is_deleted=0 and a.status_active=1 $basis_cond $system_id_cond $date_cond
					GROUP by a.id, a.sys_number_prefix_num, a.sys_number, a.basis, a.company_id, a.insert_date, a.out_date, a.approved ,a.com_location_id ,a.challan_no,a.returnable
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

				$sql=" SELECT a.id, a.sys_number_prefix_num, a.sys_number, a.basis, a.company_id, $select_year(a.insert_date $year_format) as year,  a.out_date, a.approved, c.id AS approval_id, a.com_location_id ,a.challan_no,a.returnable
				from inv_gate_pass_mst a, approval_history c 
				where a.approved in (1,3) and a.id=c.mst_id and c.entry_form in(59) and a.company_id=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and c.current_approval_status=1 $sequence_no_cond $basis_cond $system_id_cond $date_cond
				GROUP by a.id, a.sys_number_prefix_num, a.sys_number, a.basis, a.company_id, a.insert_date,  a.out_date, a.approved, c.id,a.com_location_id ,a.challan_no,a.returnable
				order by a.id";
				//echo $sql;
			}
		}
	}
	else
	{
		$sequence_no_cond=" and c.sequence_no='$user_sequence_no'";
		$sql=" SELECT a.id, a.sys_number_prefix_num, a.sys_number, a.basis, a.company_id, $select_year(a.insert_date $year_format) as year,  a.out_date, a.approved, c.id AS approval_id, a.com_location_id,a.challan_no,a.returnable
		from inv_gate_pass_mst a, approval_history c 
		where a.approved in (1,3) and a.id=c.mst_id and c.entry_form in(59) and a.company_id=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and c.current_approval_status=1 $sequence_no_cond $basis_cond $system_id_cond $date_cond
		GROUP by a.id, a.sys_number_prefix_num, a.sys_number, a.basis, a.company_id, a.insert_date,  a.out_date, a.approved, c.id, a.com_location_id, a.challan_no,a.returnable
		order by a.id";

		//echo $sql;die;
	}
	//echo $sql;die;


	
	?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:600px; margin-top:10px">
        <legend>Gate Pass List View</legend>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="580" class="rpt_table" >
                <thead>
                	<th width="50"></th>
                    <th width="40">SL</th>
                    <th width="100">Company</th>
                    <th width="120">Gate Pass Id</th>
                    <th width="150">Basis</th>
                    <th>Gate Pass Date</th>
                </thead>
            </table>
            <div style="width:580px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="560" class="rpt_table" id="tbl_list_search">
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
                                        <input id="gate_id_<? echo $i;?>" name="gate_id[]" type="hidden" value="<? echo $value; ?>" />
                                        <input id="gatePass_id_<? echo $i;?>" name="gatePass_id[]" type="hidden" value="<? echo $row[csf('id')]; ?>" />
                                        <input id="approval_id_<? echo $i;?>" name="approval_id[]" type="hidden" value="<? echo $approval_id; ?>" />
                                        <input id="<? echo strtoupper($row[csf('sys_number_prefix_num')]); ?>" name="no_gate_pass[]" type="hidden" value="<? echo $i;?>" />
                                    </td>   
									<td width="40" align="center"><? echo $i; ?></td>
									<td width="100"><? echo $company_arr[$row[csf('company_id')]]; ?></td>
                                    <td width="120" align="center"> <a href="##" onClick="generate_trims_print_report('<? echo $row[csf('company_id')]?>','<? echo $row[csf('sys_number')]?>','<? echo $print_btn ?>','<? echo $row[csf('com_location_id')]?>','<? echo $row[csf('challan_no')]?>','<? echo $row[csf('basis')]?>','<? echo $row[csf('returnable')]?>')"><?echo $row[csf('sys_number')]; ?></a></td>                                
                                    <td width="150" align="center"><? echo $get_pass_basis[$row[csf('basis')]]; ?></td>
                                    <td align="center"><? echo change_date_format($row[csf('out_date')]); ?></td>
								</tr>
								<?
								$i++;
							}
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="580" class="rpt_table">
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
		$response=$gatePass_ids;
		// echo $officeNote_ids;die;

		//echo "SELECT sequence_no from electronic_approval_setup where company_id=$cbo_importer_name and page_id=$menu_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0";die;

		$is_not_last_user=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0");
        // echo $is_not_last_user;die;

        if($is_not_last_user!="") $partial_approval=3; else $partial_approval=1;
        // echo $partial_approval;die;

        $max_approved_no_arr = return_library_array("SELECT mst_id, max(approved_no) as approved_no from approval_history where mst_id in($gatePass_ids) and entry_form=59 group by mst_id","mst_id","approved_no");
		
		$approved_status_arr = return_library_array("SELECT id, is_approved from inv_gate_pass_mst where id in($gatePass_ids)","id","approved");

		$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date"; 
		$id=return_next_id( "id","approval_history", 1 ) ;
		$appid=return_next_id( "id","approval_mst", 1 ) ;

		$gatePass_ids_all=explode(",",$gatePass_ids);
		//$officeNote_nos_all=explode(",",$officeNote_nos);
		
		// ======================================================================== New
		for($i=0;$i<count($gatePass_ids_all);$i++)
		{
			//$val=$officeNote_nos_all[$i];
			$gatePass_id=$gatePass_ids_all[$i];

			$approved_no=$max_approved_no_arr[$gatePass_id];
			$approved_status=$approved_status_arr[$gatePass_id];
			
			if($approved_status==0)
			{
				$approved_no=$approved_no+1;
				$approved_no_array[$val]=$approved_no;
			}
			
			if($data_array!="") $data_array.=",";
			
			$data_array.="(".$id.",59,".$gatePass_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$user_id.",'".$pc_date_time."')"; 
				
			$id=$id+1;


			//app mst data.......................
			if($app_data_array!=''){$app_data_array.=",";}
			$app_data_array.="(".$appid.",59,".$gatePass_id.",".$user_sequence_no.",".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."')"; 
			$appid++;

			
		}

		//echo $approval_ids;die;


		//$data = $partial_approval."*".$user_id."*'".$pc_date_time."'";
    	$rID=sql_multirow_update("inv_gate_pass_mst","approved",$partial_approval,"id",$gatePass_ids,1);
    	if($rID) $flag=1; else $flag=0;

    	if($gatePass_ids!="")
		{
			
			$query="update approval_history set current_approval_status=0  WHERE entry_form=59 and mst_id in ($gatePass_ids)";
			$rIDapp=execute_query($query,1); 
			//$rIDapp=sql_multirow_update("approval_history","current_approval_status",0,"mst_id",$gatePass_ids,1);
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

		if($flag==1) 
		{
			$field_array="id, entry_form, mst_id,  sequence_no,approved_by, approved_date,INSERTED_BY,INSERT_DATE,user_ip";
			$rID3=sql_insert("approval_mst",$field_array,$app_data_array,0);
			if($rID3) $flag=1; else $flag=0; 
		}

		if($flag==1) $msg='19'; else $msg='21';

		//echo "10**"."insert into approval_history($field_array) values".$data_array;die;
		//echo "10**".$rID."=".$rID2."=".$rIDapp;die;
	}
	else
	{
		// echo($officeNote_ids);die;
		$gatePass_ids_all=explode(",",$gatePass_ids);
		$gatePass_ids=''; $app_ids='';

		foreach($gatePass_ids_all as $value)
		{
			$data = explode('**',$value);
			$gatePass_id=$data[0];
			$app_id=$data[1];
			if($gatePass_ids=='') $gatePass_ids=$gatePass_id; else $gatePass_ids.=",".$gatePass_id;
			if($app_ids=='') $app_ids=$app_id; else $app_ids.=",".$app_id;
		}

		$rID=sql_multirow_update("inv_gate_pass_mst","approved*ready_to_approved","0*0","id",$gatePass_ids,1);
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

		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=59 and mst_id in ($gatePass_ids)";
			$rID4=execute_query($query,1); 
			if($rID4) $flag=1; else $flag=0; 
		}


		$response=$gatePass_ids;
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
<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
$permission=$_SESSION['page_permission'];
include('../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1", "id", "color_name");


	if($action=="washing_info_from_sample_populate")
	{  
		$dataArr=explode("_",$data);
		$booking_id=$dataArr[0];
		$booking_no=$dataArr[1]; 

		$res_mst = sql_select("SELECT b.id as req_id,a.id,b.company_id, a.booking_no_prefix_num, a.booking_no, b.company_id, b.buyer_name, b.style_ref_no, c.color_type_id,c.sample_type,c.fabric_color,c.lib_yarn_count_deter_id as deter_id,c.fabric_description,c.composition,c.gsm_weight,c.dia_width,c.dtls_id FROM sample_development_mst b, wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls c WHERE a.id=$booking_id and a.entry_form_id = 140 AND a.status_active = 1 AND a.booking_no=c.booking_no and b.id=c.style_id AND a.is_deleted = 0");
		
		foreach($res_mst as $result)
		{ 
			$booking_id=$result[csf('id')];
			$dtls_idArr[$result[csf('dtls_id')]]=$result[csf('dtls_id')];
			$req_id=$result[csf('req_id')];
			$booking_no=$result[csf('booking_no')];
			$company_id=$result[csf('company_id')];
			$style_ref_no=$result[csf('style_ref_no')];
			$buyer_name=$buyer_arr[$result[csf('buyer_name')]];
			$fabric_colorArr[$result[csf('fabric_color')]]=$color_arr[$result[csf('fabric_color')]];
			if($result[csf('sample_type')])
			{
				$sample_typeArr[$result[csf('sample_type')]]=$sample_type[$result[csf('sample_type')]];
			}
			$wash_batch_no_arr[$result[csf('')]]=$result[csf('booking_no')];
			
		}
		$fab_color_drop=create_drop_down( "cbo_fabric_wash_color_code", 270, $fabric_colorArr,"", 1, "-- select --","","fnc_wash_batch_load(this.value)" );
		echo "document.getElementById('fabric_wash_color_td').innerHTML = '".$fab_color_drop."';\n";
		
		exit();	
	}	
	if ($action=="load_drop_down_wash_batch")
	{

		$dataArr=explode("_",$data);
		$color_id=$dataArr[0];
		$booking_no=$dataArr[1]; 

		$sql_all_batch=sql_select("select id,batch_no from pro_batch_create_mst where booking_no='$booking_no' and color_id=$color_id and entry_form=0 and status_active = 1 and is_deleted = 0");
			foreach ($sql_all_batch as $row)
			{
				$wash_batch_no_arr[$row[csf('id')]]=$row[csf('batch_no')];
			}
			
			$wash_batch_no=create_drop_down( "cbo_batch_no_wash", 270, $wash_batch_no_arr,"", 1, "-- select --","");
			echo "document.getElementById('batch_no_td_wash').innerHTML = '".$wash_batch_no."';\n";
		exit();
	}

	if ($action=="save_update_delete_washing")
	{
		$process = array( &$_POST );
		extract(check_magic_quote_gpc( $process )); 
		//echo "10**=A";die;
		if ($operation==0)  // Insert Here
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$id_mst=return_next_id( "id", "sample_archive_washing_info", 1 ) ;
			//  ID*company_id*req_id*txt_booking_no*txt_booking_id*washing_update_id*txt_wash_mc_no*txt_wash_rpm*txt_chemical_name*txt_tumble_dryer_no*txt_process_name*txt_pre_treatment*txt_dyes_name**txt_extra_process*txt_wash_info_temperature*txt_recepi_no*txt_hydro_no*txt_wash_dry_process*txt_wash_remarks*INSERTED_BY*INSERT_DATE*UPDATED_BY*UPDATE_DATE*STATUS_ACTIVE*IS_DELETED
			$field_array="id,booking_id,booking_no,req_id,company_id,fabric_color_id,batch_id,mc_no,wash_rpm,chemical_name,tumble_dryer_no,process_name,pre_treatment,dyes_name,extra_process,wash_temperature,recepi_no,hydro_no,dry_process,remarks,inserted_by,insert_date,status_active,is_deleted"; 

			$data_array="(".$id_mst.",".$txt_booking_id.",".$txt_booking_no.",".$req_id.",".$company_id.",".$cbo_fabric_wash_color_code.",".$cbo_batch_no_wash.",".$txt_wash_mc_no.",".$txt_wash_rpm.",".$txt_chemical_name.",".$txt_tumble_dryer_no.",".$txt_process_name.",".$txt_pre_treatment.",".$txt_dyes_name.",".$txt_extra_process.",".$txt_wash_info_temperature.",".$txt_recepi_no.",".$txt_hydro_no.",".$txt_wash_dry_process.",".$txt_wash_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";

			$rID=sql_insert("sample_archive_washing_info",$field_array,$data_array,1);
			//echo "10**=A=insert into sample_archive_washing_info ($field_array) values $data_array";die;
			//echo $rID." data array ".$data_array; die;
			if($db_type==2 || $db_type==1 )
			{
				if($rID )
				{
					oci_commit($con); 
					echo "0"."**".$id_mst."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
				}
				else
				{
					oci_rollback($con);
					echo "10".$id_mst;
				}
			}
			disconnect($con);
			die;
		}
		else if ($operation==1)  // Update Here
		{	
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$field_array_up="fabric_color_id*batch_id*mc_no*wash_rpm*chemical_name*tumble_dryer_no*process_name*pre_treatment*dyes_name*extra_process*wash_temperature*recepi_no*hydro_no*dry_process*remarks*updated_by*update_date";

			$data_array_up="".$cbo_fabric_wash_color_code."*".$cbo_batch_no_wash."*".$txt_wash_mc_no."*".$txt_wash_rpm."*".$txt_chemical_name."*".$txt_tumble_dryer_no."*".$txt_process_name."*".$txt_pre_treatment."*".$txt_dyes_name."*".$txt_extra_process."*".$txt_wash_info_temperature."*".$txt_recepi_no."*".$txt_hydro_no."*".$txt_wash_dry_process."*".$txt_wash_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID=sql_update("sample_archive_washing_info",$field_array_up,$data_array_up,"id","".$washing_update_id."",1);
			//echo "10**=".$rID.'=';die;
			if($db_type==2 || $db_type==1 )
			{
				if($rID )
				{
					oci_commit($con);
					echo "1**".str_replace("'","",$washing_update_id)."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
				}
				else
				{
					oci_rollback($con);
					echo "10**".str_replace("'","",$washing_update_id)."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
				}
			}
			disconnect($con);
			die;
		}
		else if ($operation==2)  // Delete Here
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$field_array="updated_by*update_date*status_active*is_deleted";
			$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
			$rID1=sql_delete("sample_archive_washing_info",$field_array,$data_array,"id","".$washing_update_id."",0);
			
			if($db_type==0)
			{
				if($rID1)
				{
					mysql_query("COMMIT");  
					echo "2**".str_replace("'","",$washing_update_id)."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".str_replace("'","",$washing_update_id)."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($rID1)
				{
					oci_commit($con);
					echo "2**".str_replace("'","",$washing_update_id)."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
				}
				else
				{
					oci_rollback($con); 
					echo "10**".str_replace("'","",$washing_update_id)."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
				}
			}
			disconnect($con); die;
		}
	}
	function sql_update2($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit="",$return_query='')
	{

		$strQuery = "UPDATE ".$strTable." SET ";
		$arrUpdateFields=explode("*",$arrUpdateFields);
		$arrUpdateValues=explode("*",$arrUpdateValues);

		if(count($arrUpdateFields)!=count($arrUpdateValues)){
			return "0";
		}

		if(is_array($arrUpdateFields))
		{
			$arrayUpdate = array_combine($arrUpdateFields,$arrUpdateValues);
			$Arraysize = count($arrayUpdate);
			$i = 1;
			foreach($arrayUpdate as $key=>$value):
				$strQuery .= ($i != $Arraysize)? $key."=".$value.", ":$key."=".$value;
				$i++;
			endforeach;
		}
		else
		{
			$strQuery .= $arrUpdateFields."=".$arrUpdateValues;
		}
		$strQuery .=" WHERE ";

		$arrRefFields=explode("*",$arrRefFields);
		$arrRefValues=explode("*",$arrRefValues);
		if(is_array($arrRefFields))
		{
			$arrayRef = array_combine($arrRefFields,$arrRefValues);
			$Arraysize = count($arrayRef);
			$i = 1;
			foreach($arrayRef as $key=>$value):
				$strQuery .= ($i != $Arraysize)? $key."=".$value." AND ":$key."=".$value."";
				$i++;
			endforeach;
		}
		else
		{
			$strQuery .= $arrRefFields."=".$arrRefValues."";
		}
		echo $strQuery;die;
		if($return_query==1){return $strQuery ;}

			//return $strQuery;die;
		global $con;
		if( strpos($strQuery, "WHERE")==false)  return "0";
		$stid =  oci_parse($con, $strQuery);
		$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
		
		if ($exestd){user_activities($exestd);}
		if ($exestd)
			return "1";
		else
			return "0";

		die;
		if ( $commit==1 )
		{
			if (!oci_error($stid))
			{
				oci_commit($con);
				return "1";
			}
			else
			{
				oci_rollback($con);
				return "10";
			}
		}
		else
			return 1;
		die;
		exit();
	}
	if ($action=="listview_washing_info")
	{
		$booking_id=$data;
		$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst where status_active=1 and is_deleted=0", 'id', 'batch_no');

		$sql_result =sql_select("SELECT  id,booking_id,fabric_color_id,batch_id from sample_archive_washing_info   where  booking_id=$booking_id and  is_deleted=0  and  status_active=1  order by  id asc");
					
		?>
		<table width="600" cellspacing="0" border="1" rules="all" class="rpt_table" >
			<thead>
				
				<th width="290">Fab. Color/Code</th>
				<th width="290">Batch No</th>
				
			
			</thead>
		</table>
		<div style="width:600px; overflow-y:scroll; max-height:180px;">
			<table width="600" cellspacing="0" border="1" rules="all" class="rpt_table" id="tbl_details_washing">
				<?
					$i=1;
					foreach ($sql_result as $row)
					{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
						
						
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="get_php_form_data('<? echo $row[csf('id')]; ?>', 'populate_details_washing_form_data', 'requires/fabric_wash_info_entry_controller');"> 
							
							<td width="290"><? echo $color_arr[$row[csf('fabric_color_id')]]; ?></td>
							<td width="290" style="word-break:break-all"><p><? echo $batch_arr[$row[csf('batch_id')]]; ?></p></td>
						</tr>
					<? 
						$i++; 
					}
				?>
			</table>
		</div>
		<?
		exit();
	}
if($action=="populate_details_washing_form_data")
{  
 	
	$sql_result =sql_select("SELECT  id,booking_id,booking_no,req_id,company_id,fabric_color_id,batch_id,mc_no,wash_rpm,chemical_name,tumble_dryer_no,process_name,pre_treatment,dyes_name,extra_process,wash_temperature,recepi_no,hydro_no,dry_process,remarks from sample_archive_washing_info   where  id=$data and  is_deleted=0  and  status_active=1  order by  id asc");
	 
	foreach($sql_result as $row)
	{ 
 		$batch_no=$row[csf('batch_id')];
		echo "$('#washing_update_id').val('".$row[csf('id')]."');\n";
		echo "$('#cbo_fabric_wash_color_code').val('".$row[csf('fabric_color_id')]."');\n";
		
		
   	}
	  // fnc_wash_batch_load(this.value)
	   echo "fnc_wash_batch_load(".$row[csf('fabric_color_id')].");\n";
	   echo "$('#cbo_batch_no_wash').val('".$row[csf('batch_id')]."');\n";
	   echo "$('#txt_wash_mc_no').val('".$row[csf('mc_no')]."');\n";
	   echo "$('#txt_wash_rpm').val('".$row[csf('wash_rpm')]."');\n";
	   echo "$('#txt_chemical_name').val('".$row[csf('chemical_name')]."');\n";
	   echo "$('#txt_tumble_dryer_no').val('".$row[csf('tumble_dryer_no')]."');\n";
	   echo "$('#txt_process_name').val('".$row[csf('process_name')]."');\n";
	   echo "$('#txt_pre_treatment').val('".$row[csf('pre_treatment')]."');\n";
	   echo "$('#txt_dyes_name').val('".$row[csf('dyes_name')]."');\n";
	   echo "$('#txt_extra_process').val('".$row[csf('extra_process')]."');\n";
	   echo "$('#txt_wash_info_temperature').val('".$row[csf('wash_temperature')]."');\n";
	   echo "$('#txt_recepi_no').val('".$row[csf('recepi_no')]."');\n";
	   echo "$('#txt_hydro_no').val('".$row[csf('hydro_no')]."');\n";
	   echo "$('#txt_wash_dry_process').val('".$row[csf('dry_process')]."');\n";
	   echo "$('#txt_wash_remarks').val('".$row[csf('remarks')]."');\n";
	
	
	if(count($sql_result)>0)
	{
		
	echo "$('#save9').removeClass('formbutton').addClass('formbutton_disabled');\n"; //formbutton 
	echo "$('#save9').removeAttr('onclick').attr('onclick','fnc_washing_entry(0);')\n";
	echo "$('#update9').removeClass('formbutton_disabled').addClass('formbutton');\n"; //formbutton 
    echo "$('#update9').removeAttr('onclick').attr('onclick','fnc_washing_entry(1);')\n"; 
	echo "$('#Delete9').removeClass('formbutton_disabled').addClass('formbutton');\n"; //formbutton 
	echo "$('#Delete9').removeAttr('onclick').attr('onclick','fnc_washing_entry(2);')\n"; 
	
		
	}
	else
	{
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_washing_entry',9);\n"; 
	}
	 	 
	
		
   	unlink($sql_result);
 	exit();	
}	



?>
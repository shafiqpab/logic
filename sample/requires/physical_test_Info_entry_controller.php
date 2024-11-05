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
$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst where status_active=1 and is_deleted=0", 'id', 'batch_no');

if($action=="physicalTest_info_from_sample_populate")
{  
	$dataArr=explode("_",$data);
	$booking_id=$dataArr[0];
	$booking_no=$dataArr[1]; 
	 
	$res_mst = sql_select("SELECT b.id as req_id,a.id,b.company_id, a.booking_no_prefix_num, a.booking_no, b.company_id, b.buyer_name, b.style_ref_no, c.color_type_id,c.sample_type,c.fabric_color,c.lib_yarn_count_deter_id as deter_id,c.fabric_description,c.composition,c.gsm_weight,c.dia_width,c.dtls_id FROM sample_development_mst b, wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls c WHERE a.id=$booking_id and a.entry_form_id = 140 AND a.status_active = 1 AND a.booking_no=c.booking_no and b.id=c.style_id AND a.is_deleted = 0");
	 
	foreach($res_mst as $result)
	{ 
		 $booking_id=$result[csf('id')];
		 $booking_no=$result[csf('booking_no')];
		 $company_id=$result[csf('company_id')];
		 $fabric_colorArr[$result[csf('fabric_color')]]=$color_arr[$result[csf('fabric_color')]];
		
		 
	}
	 $fab_color_drop=create_drop_down( "cbo_physical_test_color_code", 270, $fabric_colorArr,"", 1, "-- select --","","fnc_physicalTest_batch_load(this.value)");
	 echo "document.getElementById('physical_test_color_td').innerHTML = '".$fab_color_drop."';\n";
	 exit();	
}
if ($action=="load_drop_down_physical_test_batch")
{

	$dataArr=explode("_",$data);
	$color_id=$dataArr[0];
	$booking_no=$dataArr[1]; 
	
	$sql_all_batch=sql_select("select id,batch_no from pro_batch_create_mst where booking_no='$booking_no' and color_id=$color_id and entry_form=0 and status_active = 1 and is_deleted = 0");

		foreach ($sql_all_batch as $row)
		{
			$batch_no_arr[$row[csf('id')]]=$row[csf('batch_no')];
		}
		$batch_no_Ptest=create_drop_down( "cbo_physical_batch_no", 270, $batch_no_arr,"", 1, "-- select --","");
		echo "document.getElementById('batch_physical_no_td').innerHTML = '".$batch_no_Ptest."';\n";
		exit();
}

if ($action=="save_update_delete_physicalTest")
{

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
 		$id_mst=return_next_id( "id", "sample_archive_physical_test_info", 1 ) ;

 		$field_array="id,req_id,company_id,booking_no,booking_id,fabric_color_id,batch_no,dry_process,actual_gsm,phenolic_yellowing,length,test_method,pilling,cf_to_light,width,req_dia,bursting_strength,cf_to_saliva,twisting,actual_dia,dry_rubbing,wpi,wash_temperature,req_gsm,wet_rubbing,cpi,acetate_1,acetate_2,acetate_3,acetate_4,acetate_5,acetate_6,water_1,water_2,water_3,water_4,water_5,water_6,perspiration_acid_1 ,perspiration_acid_2,perspiration_acid_3,perspiration_acid_4,perspiration_acid_5,perspiration_acid_6,perspiration_alkali_1,perspiration_alkali_2, perspiration_alkali_3,perspiration_alkali_4,perspiration_alkali_5,perspiration_alkali_6,remarks_phy,delivery_date,inserted_by, insert_date, status_active, is_deleted";


		$data_array="(".$id_mst.",".$req_id.",".$company_id.",".$txt_booking_no.",".$txt_booking_id.",".$cbo_physical_test_color_code.",".$cbo_physical_batch_no.",".$txt_dry_process.",".$txt_actual_gsm.",".$txt_phenolic_yellowing.",".$txt_length.",".$txt_test_method.",".$txt_pilling.",".$txt_cf_to_light.",".$txt_width.",".$txt_req_dia.",".$txt_bursting_strength.",".$txt_cf_to_saliva.",".$txt_twisting.",".$txt_actual_dia.",".$txt_dry_rubbing.",".$txt_wpi.",".$txt_wash_temperature.",".$txt_req_gsm.",".$txt_wet_rubbing.",".$txt_cpi.",".$txt_acetate_1.",".$txt_acetate_2.",".$txt_acetate_3.",".$txt_acetate_4.",".$txt_acetate_5.",".$txt_acetate_6.",".$txt_Water_1.",".$txt_Water_2.",".$txt_Water_3.",".$txt_Water_4.",".$txt_Water_5.",".$txt_Water_6.",".$txt_perspiration_acid_1.",".$txt_perspiration_acid_2.",".$txt_perspiration_acid_3.",".$txt_perspiration_acid_4.",".$txt_perspiration_acid_5.",".$txt_perspiration_acid_6.",".$txt_perspiration_alkali_1.",".$txt_perspiration_alkali_2.",".$txt_perspiration_alkali_3.",".$txt_perspiration_alkali_4.",".$txt_perspiration_alkali_5.",".$txt_perspiration_alkali_6.",".$txt_physicaltest_remarks.",".$txt_delivery_date.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		
		$rID=sql_insert("sample_archive_physical_test_info",$field_array,$data_array,1);
		// echo "10**=A=insert into SAMPLE_ARCHIVE_PHYSICAL_TEST_INFO ($field_array) values $data_array";die;
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
  		$field_array_up="fabric_color_id*batch_no*dry_process*actual_gsm*phenolic_yellowing*length*test_method*pilling*cf_to_light*width*req_dia*bursting_strength*cf_to_saliva*twisting*actual_dia*dry_rubbing*wpi*wash_temperature*req_gsm*wet_rubbing*cpi*acetate_1*acetate_2*acetate_3*acetate_4*acetate_5*acetate_6*Water_1*Water_2*Water_3*Water_4*Water_5*Water_6*perspiration_acid_1*perspiration_acid_2*perspiration_acid_3*perspiration_acid_4*perspiration_acid_5*perspiration_acid_6*perspiration_alkali_1*perspiration_alkali_2*perspiration_alkali_3*perspiration_alkali_4*perspiration_alkali_5*perspiration_alkali_6*remarks_phy*delivery_date*updated_by*update_date";

		$data_array_up="".$cbo_physical_test_color_code."*".$cbo_physical_batch_no."*".$txt_dry_process."*".$txt_actual_gsm."*".$txt_phenolic_yellowing."*".$txt_length."*".$txt_test_method."*".$txt_pilling."*".$txt_cf_to_light."*".$txt_width."*".$txt_req_dia."*".$txt_bursting_strength."*".$txt_cf_to_saliva."*".$txt_twisting."*".$txt_actual_dia."*".$txt_dry_rubbing."*".$txt_wpi."*".$txt_wash_temperature."*".$txt_req_gsm."*".$txt_wet_rubbing."*".$txt_cpi."*".$txt_acetate_1."*".$txt_acetate_2."*".$txt_acetate_3."*".$txt_acetate_4."*".$txt_acetate_5."*".$txt_acetate_6."*".$txt_Water_1."*".$txt_Water_2."*".$txt_Water_3."*".$txt_Water_4."*".$txt_Water_5."*".$txt_Water_6."*".$txt_perspiration_acid_1."*".$txt_perspiration_acid_2."*".$txt_perspiration_acid_3."*".$txt_perspiration_acid_4."*".$txt_perspiration_acid_5."*".$txt_perspiration_acid_6."*".$txt_perspiration_alkali_1."*".$txt_perspiration_alkali_2."*".$txt_perspiration_alkali_3."*".$txt_perspiration_alkali_4."*".$txt_perspiration_alkali_5."*".$txt_perspiration_alkali_6."*".$txt_physicaltest_remarks."*".$txt_delivery_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
 		$rID=sql_update("sample_archive_physical_test_info",$field_array_up,$data_array_up,"id","".$physicalTest_update_id."",1);
		// echo "10**=A=update into SAMPLE_ARCHIVE_PHYSICAL_TEST_INFO ($field_array) values $data_array";die;
		 	 //echo "10**". bulk_update_sql_statement( "sample_archive_physical_test_info", "id", $field_array_up, $data_array_up, $id_arr );die;
 		if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con);
 				echo "1**".str_replace("'","",$physicalTest_update_id)."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$physicalTest_update_id)."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
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
		$rID1=sql_delete("sample_archive_physical_test_info",$field_array,$data_array,"id","".$physicalTest_update_id."",0);
		 
		if($db_type==0)
		{
			if($rID1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$physicalTest_update_id)."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$physicalTest_update_id)."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID1)
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$physicalTest_update_id)."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
			}
			else
			{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$physicalTest_update_id)."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
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
if ($action=="listview_physicalTest_info")
{

	$dataArr=explode("_",$data);
	$booking_id=$dataArr[0];
	$booking_no=$dataArr[1]; 
	//$booking_id=$data;
	
	$sql_result =sql_select("SELECT id,fabric_color_id,batch_no from sample_archive_physical_test_info a where booking_id=$booking_id and is_deleted=0  and a.status_active=1  order by id asc");
	
				
	?>
    <table width="600" cellspacing="0" border="1" rules="all" class="rpt_table" >
        <thead>
			
            <th width="300">sl</th>
            <th width="300">Fab. Color/Code</th>
            
        </thead>
    </table>
    <div style="width:600px; overflow-y:scroll; max-height:180px;">
        <table width="600" cellspacing="0" border="1" rules="all" class="rpt_table" id="tbl_details_physicalTest">
			<?
				$i=1;
               	foreach ($sql_result as $row)
               	{
				   if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				   
				?>
            		<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="get_php_form_data('<? echo $row[csf('id')]; ?>', 'populate_details_physicalTest_form_data', 'requires/physical_test_Info_entry_controller');"> 
						
                		 <td width="150"><? echo $i; ?></td>
                         <td width="450" style="word-break:break-all"><p><? echo $color_arr[$row[csf('fabric_color_id')]]; ?></p></td>
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
if($action=="populate_details_physicalTest_form_data")
{  
 	
	 $sql_result =sql_select("SELECT  id,fabric_color_id,batch_no,req_id,company_id,booking_no,booking_id,dry_process,actual_gsm,phenolic_yellowing,length,test_method,pilling,cf_to_light,width,req_dia,bursting_strength,cf_to_saliva,twisting,actual_dia,dry_rubbing,wpi,wash_temperature,req_gsm,wet_rubbing,cpi,acetate_1,acetate_2,acetate_3,acetate_4,acetate_5,acetate_6,Water_1,Water_2,Water_3,Water_4,Water_5,Water_6,perspiration_acid_1 ,perspiration_acid_2,perspiration_acid_3,perspiration_acid_4,perspiration_acid_5,perspiration_acid_6,perspiration_alkali_1,perspiration_alkali_2, perspiration_alkali_3,perspiration_alkali_4,perspiration_alkali_5,perspiration_alkali_6,remarks_phy,delivery_date from sample_archive_physical_test_info   where  id=$data and  is_deleted=0  and  status_active=1  order by  id asc");

	foreach($sql_result as $row)
	{ 
		$batch_no=$row[csf('batch_no')];
		echo "$('#physicalTest_update_id').val('".$row[csf('id')]."');\n";
		echo "$('#cbo_physical_test_color_code').val('".$row[csf('fabric_color_id')]."');\n";
		
   	}
	   echo "fnc_physicalTest_batch_load(".$row[csf('fabric_color_id')].");\n";
	   echo "$('#cbo_physical_batch_no').val('".$row[csf('batch_no')]."');\n";
	   echo "$('#txt_dry_process').val('".$row[csf('dry_process')]."');\n";
	   echo "$('#txt_actual_gsm').val('".$row[csf('actual_gsm')]."');\n";
	   echo "$('#txt_phenolic_yellowing').val('".$row[csf('phenolic_yellowing')]."');\n";
	   echo "$('#txt_length').val('".$row[csf('length')]."');\n";
	   echo "$('#txt_test_method').val('".$row[csf('test_method')]."');\n";
	   echo "$('#txt_pilling').val('".$row[csf('pilling')]."');\n";
	   echo "$('#txt_cf_to_light').val('".$row[csf('cf_to_light')]."');\n";
	   echo "$('#txt_width').val('".$row[csf('width')]."');\n";
	   echo "$('#txt_req_dia').val('".$row[csf('req_dia')]."');\n";
	   echo "$('#txt_bursting_strength').val('".$row[csf('bursting_strength')]."');\n";
	   echo "$('#txt_cf_to_saliva').val('".$row[csf('cf_to_saliva')]."');\n";
	   echo "$('#txt_twisting').val('".$row[csf('twisting')]."');\n";
	   echo "$('#txt_actual_dia').val('".$row[csf('actual_dia')]."');\n";
	   echo "$('#txt_dry_rubbing').val('".$row[csf('dry_rubbing')]."');\n";
	   echo "$('#txt_wpi').val('".$row[csf('wpi')]."');\n";
	   echo "$('#txt_wash_temperature').val('".$row[csf('wash_temperature')]."');\n";
	   echo "$('#txt_req_gsm').val('".$row[csf('req_gsm')]."');\n";
	   echo "$('#txt_wet_rubbing').val('".$row[csf('wet_rubbing')]."');\n";
	   echo "$('#txt_cpi').val('".$row[csf('cpi')]."');\n";
	   echo "$('#txt_acetate_1').val('".$row[csf('acetate_1')]."');\n";
	   echo "$('#txt_acetate_2').val('".$row[csf('acetate_2')]."');\n";
	   echo "$('#txt_acetate_3').val('".$row[csf('acetate_3')]."');\n";
	   echo "$('#txt_acetate_4').val('".$row[csf('acetate_4')]."');\n";
	   echo "$('#txt_acetate_5').val('".$row[csf('acetate_5')]."');\n";
	   echo "$('#txt_acetate_6').val('".$row[csf('acetate_6')]."');\n";
	   echo "$('#txt_Water_1').val('".$row[csf('Water_1')]."');\n";
	   echo "$('#txt_Water_2').val('".$row[csf('Water_2')]."');\n";
	   echo "$('#txt_Water_3').val('".$row[csf('Water_3')]."');\n";
	   echo "$('#txt_Water_4').val('".$row[csf('Water_4')]."');\n";
	   echo "$('#txt_Water_5').val('".$row[csf('Water_5')]."');\n";
	   echo "$('#txt_Water_6').val('".$row[csf('Water_6')]."');\n";
	   echo "$('#txt_perspiration_acid_1').val('".$row[csf('perspiration_acid_1')]."');\n";
	   echo "$('#txt_perspiration_acid_2').val('".$row[csf('perspiration_acid_2')]."');\n";
	   echo "$('#txt_perspiration_acid_3').val('".$row[csf('perspiration_acid_3')]."');\n";
	   echo "$('#txt_perspiration_acid_4').val('".$row[csf('perspiration_acid_4')]."');\n";
	   echo "$('#txt_perspiration_acid_5').val('".$row[csf('perspiration_acid_5')]."');\n";
	   echo "$('#txt_perspiration_acid_6').val('".$row[csf('perspiration_acid_6')]."');\n";
	   echo "$('#txt_perspiration_alkali_1').val('".$row[csf('perspiration_alkali_1')]."');\n";
	   echo "$('#txt_perspiration_alkali_2').val('".$row[csf('perspiration_alkali_2')]."');\n";
	   echo "$('#txt_perspiration_alkali_3').val('".$row[csf('perspiration_alkali_3')]."');\n";
	   echo "$('#txt_perspiration_alkali_4').val('".$row[csf('perspiration_alkali_4')]."');\n";
	   echo "$('#txt_perspiration_alkali_5').val('".$row[csf('perspiration_alkali_5')]."');\n";
	   echo "$('#txt_perspiration_alkali_6').val('".$row[csf('perspiration_alkali_6')]."');\n";
	   echo "$('#txt_physicaltest_remarks').val('".$row[csf('remarks_phy')]."');\n";
	   echo "$('#txt_delivery_date').val('".$row[csf('delivery_date')]."');\n";

	if(count($sql_result)>0)
	{
		
	echo "$('#save8').removeClass('formbutton').addClass('formbutton_disabled');\n"; //formbutton 
	echo "$('#save8').removeAttr('onclick').attr('onclick','fnc_physicalTest_entry(0);')\n";
	echo "$('#update8').removeClass('formbutton_disabled').addClass('formbutton');\n"; //formbutton 
    echo "$('#update8').removeAttr('onclick').attr('onclick','fnc_physicalTest_entry(1);')\n";
	echo "$('#Delete8').removeClass('formbutton_disabled').addClass('formbutton');\n"; //formbutton 
	echo "$('#Delete8').removeAttr('onclick').attr('onclick','fnc_physicalTest_entry(2);')\n"; 
		
	}
	else
	{
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_physicalTest_entry',8);\n"; 
	}
	 	 
   	unlink($sql_result);
 	exit();	
}	



?>
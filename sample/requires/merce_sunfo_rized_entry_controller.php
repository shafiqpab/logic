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

	if($action=="merceSunfoRized_info_from_sample_populate")
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
			 if($result[csf('color_type_id')])
			 {
			   $colorTypeArr[$color_type[$result[csf('color_type_id')]]]=$color_type[$result[csf('color_type_id')]];
			   $colorTypeIdArr[$result[csf('color_type_id')]]=$result[csf('color_type_id')];
			 }
		   
			 
		}
		$fab_color_drop=create_drop_down( "cbo_msrce_sunfo_fab_color_code", 270, $fabric_colorArr,"", 1, "-- select --","","fnc_merceSunfoRized_batch_load(this.value)" );
		echo "document.getElementById('msrce_sunfo_color_td').innerHTML = '".$fab_color_drop."';\n";
		 exit();	
	}
	if ($action=="load_drop_down_merceSunfoRized_batch")
	{

		$dataArr=explode("_",$data);
		$color_id=$dataArr[0];
		$booking_no=$dataArr[1]; 

		$sql_all_batch=sql_select("select id,batch_no from pro_batch_create_mst where booking_no='$booking_no' and color_id=$color_id and entry_form=0 and status_active = 1 and is_deleted = 0");
			foreach ($sql_all_batch as $row)
			{
				$wash_batch_no_arr[$row[csf('id')]]=$row[csf('batch_no')];
			}
			$wash_batch_no=create_drop_down( "cbo_sunfo_batch_no", 270, $wash_batch_no_arr,"", 1, "-- select --","");
			echo "document.getElementById('batch_sunfo_no_td').innerHTML = '".$wash_batch_no."';\n";
			exit();
	}	


if ($action=="save_update_delete_merceSunfoRized")
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
 		$id_mst=return_next_id( "id", "sample_archive_mercerized_sunforized_info", 1 ) ;
        //  ID*BOOKING_ID*BOOKING_NO*REQ_ID*COMPANY_ID*cbo_msrce_sunfo_fab_color_code*cbo_sunfo_batch_no*TOTAL_LIQUARE*CAUSTIC_SOLUTION*SUNFORIZED_TEMPERATURE*TAFLON_PRESSURECOMPECTION*MERCERIZED_TEMPERATURE*MC_SPEED*OVER_FEED*SPEED*MERCERIZED_PH*NORMAL_WASH*STEAM*ACETIC_ACID*UNLOADING_PH*SUNFORIZED_REMARKS*MERCERIZED_REMARKS*INSERTED_BY*INSERT_DATE*UPDATED_BY*UPDATE_DATE*STATUS_ACTIVE*IS_DELETED
        $field_array="id,booking_id,booking_no,req_id,company_id,fabric_color_id,batch_no,total_liquare,caustic_solution,sunforized_temperature,taflon_pressurecompection,mercerized_temperature,mc_speed,over_feed,speed,mercerized_ph,normal_wash,steam,acetic_acid,unloading_ph,sunforized_remarks,mercerized_remarks,inserted_by,insert_date,status_active,is_deleted"; 
        // txt_booking_id*txt_booking_no*req_id*company_id*merceSunfoRized_update_id*txt_mer_total_liquare*txt_mer_caustic_solution*txt_sun_temperature*txt_sun_taflon_pressureCompection*txt_mer_temperature*txt_mer_mc_speed*txt_sun_over_feed*txt_sun_speed*txt_mer_mercerizedph*txt_mer_normalWash*txt_sun_steam*txt_mer_aceticAcid*txt_mer_unloadingPH*txt_sun_remarks*txt_mer_remarks
 		// $field_array="id,req_id,company_id,booking_no,booking_id,recipe_no,only_wash,scouring,reactive,both_part,enzyme,dyes_orginal,direct,remarks,dyes_add_top,desperse,white,inserted_by, insert_date, status_active, is_deleted";

		$data_array="(".$id_mst.",".$txt_booking_id.",".$txt_booking_no.",".$req_id.",".$company_id.",".$cbo_msrce_sunfo_fab_color_code.",".$cbo_sunfo_batch_no.",".$txt_mer_total_liquare.",".$txt_mer_caustic_solution.",".$txt_sun_temperature.",".$txt_sun_taflon_pressureCompection.",".$txt_mer_temperature.",".$txt_mer_mc_speed.",".$txt_sun_over_feed.",".$txt_sun_speed.",".$txt_mer_mercerizedph.",".$txt_mer_normalWash.",".$txt_sun_steam.",".$txt_mer_aceticAcid.",".$txt_mer_unloadingPH.",".$txt_sun_remarks.",".$txt_mer_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";

		$rID=sql_insert("sample_archive_mercerized_sunforized_info",$field_array,$data_array,1);
		//   echo "10**=A=insert into sample_archive_mercerized_sunforized_info ($field_array) values $data_array";die;
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
  		$field_array_up="fabric_color_id*batch_no*total_liquare*caustic_solution*sunforized_temperature*taflon_pressurecompection*mercerized_temperature*mc_speed*over_feed*speed*mercerized_ph*normal_wash*steam*acetic_acid*unloading_ph*sunforized_remarks*mercerized_remarks*updated_by*update_date";
        //   $data_array="(".$txt_mer_total_liquare.",".$txt_mer_caustic_solution.",".$txt_sun_temperature.",".$txt_sun_taflon_pressureCompection.",".$txt_mer_temperature.",".$txt_mer_mc_speed.",".$txt_sun_over_feed.",".$txt_sun_speed.",".$txt_mer_mercerizedph.",".$txt_mer_normalWash.",".$txt_sun_steam.",".$txt_mer_aceticAcid.",".$txt_mer_unloadingPH.",".$txt_sun_remarks.",".$txt_mer_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		$data_array_up="".$cbo_msrce_sunfo_fab_color_code."*".$cbo_sunfo_batch_no."*".$txt_mer_total_liquare."*".$txt_mer_caustic_solution."*".$txt_sun_temperature."*".$txt_sun_taflon_pressureCompection."*".$txt_mer_temperature."*".$txt_mer_mc_speed."*".$txt_sun_over_feed."*".$txt_sun_speed."*".$txt_mer_mercerizedph."*".$txt_mer_normalWash."*".$txt_sun_steam."*".$txt_mer_aceticAcid."*".$txt_mer_unloadingPH."*".$txt_sun_remarks."*".$txt_mer_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
 		$rID=sql_update("sample_archive_mercerized_sunforized_info",$field_array_up,$data_array_up,"id","".$merceSunfoRized_update_id."",1);
		 //echo "10**=".$rID.'=';die;
 		if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con);
 				echo "1**".str_replace("'","",$merceSunfoRized_update_id)."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$merceSunfoRized_update_id)."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
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
		$rID1=sql_delete("sample_archive_mercerized_sunforized_info",$field_array,$data_array,"id","".$merceSunfoRized_update_id."",0);
		 
		if($db_type==0)
		{
			if($rID1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$merceSunfoRized_update_id)."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$merceSunfoRized_update_id)."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID1)
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$merceSunfoRized_update_id)."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
			}
			else
			{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$merceSunfoRized_update_id)."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
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
if ($action=="listview_merceSunfoRized_info")
{
	$booking_id=$data;
	$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst where status_active=1 and is_deleted=0", 'id', 'batch_no');

	$sql_result =sql_select("SELECT  id,booking_id,fabric_color_id,batch_no from sample_archive_mercerized_sunforized_info   where  booking_id=$booking_id and  is_deleted=0  and  status_active=1  order by  id asc");
	
	?>
    <table width="600" cellspacing="0" border="1" rules="all" class="rpt_table" >
        <thead>
				<th width="290">Fab. Color/Code</th>
				<th width="290">Batch No</th>
        </thead>
    </table>
    <div style="width:620px; overflow-y:scroll; max-height:180px;">
        <table width="600" cellspacing="0" border="1" rules="all" class="rpt_table" id="tbl_details_merceSunfoRized">
			<?
				$i=1;
               	foreach ($sql_result as $row)
               	{
				   if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				   
				?>
            		<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="get_php_form_data('<? echo $row[csf('id')]; ?>', 'populate_details_mercesunforized_form_data', 'requires/merce_sunfo_rized_entry_controller');"> 
						
                		<td width="290" style="word-break:break-all"><? echo $color_arr[$row[csf('fabric_color_id')]]; ?></td>
                        <td width="290" style="word-break:break-all"><p><? echo $batch_arr[$row[csf('batch_no')]]; ?></p></td>
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
if($action=="populate_details_mercesunforized_form_data")
{  
 	
	$sql_result =sql_select("SELECT  id,booking_id,booking_no,req_id,company_id,fabric_color_id,batch_no,total_liquare,caustic_solution,sunforized_temperature,taflon_pressurecompection,mercerized_temperature,mc_speed,over_feed,speed,mercerized_ph,normal_wash,steam,acetic_acid,unloading_ph,sunforized_remarks,mercerized_remarks from sample_archive_mercerized_sunforized_info   where  id=$data and  is_deleted=0  and  status_active=1  order by  id asc");
	 
	//ID,BOOKING_ID,NORMAL_WASH*BOOKING_NO*REQ_ID*COMPANY_ID*TOTAL_LIQUARE*CAUSTIC_SOLUTION*SUNFORIZED_TEMPERATURE*SUNFORIZED_TEMPERATURE*MERCERIZED_TEMPERATURE*MC_SPEED*OVER_FEED*SPEED*MERCERIZED_PH*NORMAL_WASH*STEAM*ACETIC_ACID*UNLOADING_PH*SUNFORIZED_REMARKS*MERCERIZED_REMARKS*INSERTED_BY*INSERT_DATE*UPDATED_BY*UPDATE_DATE*STATUS_ACTIVE*IS_DELETED

    //txt_booking_id*txt_booking_no*req_id*company_id*merceSunfoRized_update_id*txt_mer_total_liquare*txt_mer_caustic_solution*txt_sun_temperature*txt_sun_taflon_pressureCompection*txt_mer_temperature*txt_mer_mc_speed*txt_sun_over_feed*txt_sun_speed*txt_mer_mercerizedph*txt_mer_normalWash*txt_sun_steam*txt_mer_aceticAcid*txt_mer_unloadingPH*txt_sun_remarks*txt_mer_remarks
	
	foreach($sql_result as $row)
	{
		$batch_no=$row[csf('batch_no')];
		echo "$('#mercesunforized_update_id').val('".$row[csf('id')]."');\n";
		echo "$('#cbo_msrce_sunfo_fab_color_code').val('".$row[csf('fabric_color_id')]."');\n";
		//echo "$('#cbo_sunfo_batch_no').val('".$row[csf('batch_no')]."');\n";
		
		
   	}
	   echo "fnc_merceSunfoRized_batch_load(".$row[csf('fabric_color_id')].");\n";
	   echo "$('#cbo_sunfo_batch_no').val('".$row[csf('batch_no')]."');\n";
	   echo "$('#txt_mer_total_liquare').val('".$row[csf('total_liquare')]."');\n";
	   echo "$('#txt_mer_caustic_solution').val('".$row[csf('caustic_solution')]."');\n";
	   echo "$('#txt_sun_temperature').val('".$row[csf('sunforized_temperature')]."');\n";
	   echo "$('#txt_sun_taflon_pressureCompection').val('".$row[csf('taflon_pressurecompection')]."');\n";
	   echo "$('#txt_mer_temperature').val('".$row[csf('mercerized_temperature')]."');\n";
	   echo "$('#txt_mer_mc_speed').val('".$row[csf('mc_speed')]."');\n";
	   echo "$('#txt_sun_over_feed').val('".$row[csf('over_feed')]."');\n";
	   echo "$('#txt_sun_speed').val('".$row[csf('speed')]."');\n";
	   echo "$('#txt_mer_mercerizedph').val('".$row[csf('mercerized_ph')]."');\n";
	   echo "$('#txt_mer_normalWash').val('".$row[csf('normal_wash')]."');\n";
	   echo "$('#txt_sun_steam').val('".$row[csf('steam')]."');\n";
	   echo "$('#txt_mer_aceticAcid').val('".$row[csf('acetic_acid')]."');\n";
	   echo "$('#txt_mer_unloadingPH').val('".$row[csf('unloading_ph')]."');\n";
	   echo "$('#txt_sun_remarks').val('".$row[csf('sunforized_remarks')]."');\n";
	   echo "$('#txt_mer_remarks').val('".$row[csf('mercerized_remarks')]."');\n";
	
	
	if(count($sql_result)>0)
	{
		
	echo "$('#save6').removeClass('formbutton').addClass('formbutton_disabled');\n"; //formbutton 
	echo "$('#save6').removeAttr('onclick').attr('onclick','fnc_merceSunfoRized_entry(0);')\n";
	echo "$('#update6').removeClass('formbutton_disabled').addClass('formbutton');\n"; //formbutton 
    echo "$('#update6').removeAttr('onclick').attr('onclick','fnc_merceSunfoRized_entry(1);')\n"; 
	echo "$('#Delete6').removeClass('formbutton_disabled').addClass('formbutton');\n"; //formbutton 
	echo "$('#Delete6').removeAttr('onclick').attr('onclick','fnc_merceSunfoRized_entry(2);')\n"; 
		
	}
	else
	{
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_merceSunfoRized_entry',6);\n"; 
	}
	 	 
	
		
   	unlink($sql_result);
 	exit();	
}	



?>
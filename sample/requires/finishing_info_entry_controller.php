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

if($action=="finishing_info_from_sample_populate")
{  
	$dataArr=explode("_",$data);
	$booking_id=$dataArr[0];
	$booking_no=$dataArr[1]; 

	$res_mst = sql_select("SELECT b.id as req_id,a.id,b.company_id, a.booking_no_prefix_num, a.booking_no, b.company_id, b.buyer_name, b.style_ref_no, c.color_type_id,c.sample_type,c.fabric_color,c.lib_yarn_count_deter_id as deter_id,c.fabric_description,c.composition,c.gsm_weight,c.dia_width,c.dtls_id FROM sample_development_mst b, wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls c WHERE a.id=$booking_id and a.entry_form_id = 140 AND a.status_active = 1 AND a.booking_no=c.booking_no and b.id=c.style_id AND a.is_deleted = 0");
	  
	foreach($res_mst as $result)
	{ 
		 $booking_id=$result[csf('id')];
		 $req_id=$result[csf('req_id')];
		 $booking_no=$result[csf('booking_no')];
		 $fabric_colorArr[$result[csf('fabric_color')]]=$color_arr[$result[csf('fabric_color')]];
		 if($result[csf('color_type_id')])
		 {
		  $colorTypeArr[$color_type[$result[csf('color_type_id')]]]=$color_type[$result[csf('color_type_id')]];
		   $colorTypeIdArr[$result[csf('color_type_id')]]=$result[csf('color_type_id')];
		 }

	}
	 
	$fab_color_drop=create_drop_down( "cbo_finishing_fab_color_code", 270, $fabric_colorArr,"", 1, "-- select --","","fnc_finishing_info_batch_load(this.value)" );
	echo "document.getElementById('finishing_color_td').innerHTML = '".$fab_color_drop."';\n";
	 exit();	
}	
if ($action=="load_drop_down_finishing_info_batch")
{

	$dataArr=explode("_",$data);
	$color_id=$dataArr[0];
	$booking_no=$dataArr[1]; 

	$sql_all_batch=sql_select("select id,batch_no from pro_batch_create_mst where booking_no='$booking_no' and color_id=$color_id and entry_form=0 and status_active = 1 and is_deleted = 0");
		foreach ($sql_all_batch as $row)
		{
			$finishing_batch_no_arr[$row[csf('id')]]=$row[csf('batch_no')];
		}
		
		$finishing_batch_no=create_drop_down( "cbo_finishing_batch_no", 270, $finishing_batch_no_arr,"", 1, "-- select --","");
		echo "document.getElementById('finishing_batch_no_td').innerHTML = '".$finishing_batch_no."';\n";
	exit();
}


if ($action=="save_update_delete_finishing")
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
 		$id_mst=return_next_id( "id", "sample_archive_finishing_info", 1 ) ;
		
		//cbo_finishing_fab_color_code,cbo_finishing_batch_no

 		$field_array="id,req_id,company_id,booking_no,booking_id,fabric_color_id,batch_no,slitting_machine_no,slitting_after_dia,peach_machine_no,peach_after_dia,slitting_process,slitting_remarks,peach_fabric_speed,peach_after_gsm,peach_drum_rpm,peach_tension,stenter_machine_no,stenter_dia_setting,peach_remarks,stenter_mc_brand,stenter_paddar_pressure,stenter_no_chamber,stenter_used_chemical,brushing_machine_no,brushing_pile_rpm_2nd,stenter_temperature,stenter_after_dia,brushing_fabric_speed,brushing_counter_pile_rpm_2nd,stenter_overfeed,stenter_after_gsm,brushing_tension,brushing_after_dia,stenter_speed,stenter_remarks,brushing_pile_rpm_1st,brushing_after_gsm,brushing_counter_pile_rpm_1st,brushing_remarks,dryer_machine_no,dryer_vibration,dryer_temperature,dryer_used_chemical,shearing_fabric_speed,shearing_drum_rpm,dryer_overfeed,dryer_after_dia,shearing_distance_blade,shearing_after_dia,dryer_speed,dryer_after_gsm,shearing_from_comber,shearing_after_gsm,dryer_dia_settings,dryer_remarks,shearing_remarks,stenter_machine_no_apb,stenter_dia_setting_apb,compacting_machine_no,compacting_steam_pressure,stenter_mc_brand_apb,stenter_padder_pressure_apb,compacting_mc_brand,compacting_blanket_pressure,stenter_no_chamber_apb,stenter_used_chemical_apb,compacting_temperature,compacting_after_dia,stenter_temperature_apb,stenter_after_dia_apb,compacting_speed,compacting_after_gsm,stenter_over_feed_apb,stenter_after_gsm_apb,compacting_over_feed,compacting_dia_setting,stenter_speed_apb,stenter_remarks_apb,compacting_remarks,fabwash_before_dia,fabwash_time,remarks,fabwash_before_gsm,fabwash_after_dia,fabwash_temperature,fabwash_after_gsm,fabwash_remarks,inserted_by, insert_date, status_active, is_deleted";
		
		$data_array="(".$id_mst.",".$req_id.",".$company_id.",".$txt_booking_no.",".$txt_booking_id.",".$cbo_finishing_fab_color_code.",".$cbo_finishing_batch_no.",".$txt_slitting_machine_no.",".$txt_slitting_after_dia.",".$txt_peach_machine_no.",".$txt_peach_after_dia.",".$txt_slitting_process.",".$txt_slitting_remarks.",".$txt_peach_fabric_speed.",".$txt_peach_after_gsm.",".$txt_peach_drum_rpm.",".$txt_peach_tension.",".$txt_stenter_machine_no.",".$txt_stenter_dia_setting.",".$txt_peach_remarks.",".$txt_stenter_mc_brand.",".$txt_stenter_paddar_pressure.",".$txt_stenter_no_chamber.",".$txt_stenter_used_chemical.",".$txt_brushing_machine_no.",".$txt_brushing_pile_rpm_2nd.",".$txt_stenter_temperature.",".$txt_stenter_after_dia.",".$txt_brushing_fabric_speed.",".$txt_brushing_counter_pile_rpm_2nd.",".$txt_stenter_overfeed.",".$txt_stenter_after_gsm.",".$txt_brushing_tension.",".$txt_brushing_after_dia.",".$txt_stenter_speed.",".$txt_stenter_remarks.",".$txt_brushing_pile_rpm_1st.",".$txt_brushing_after_gsm.",".$txt_brushing_counter_pile_rpm_1st.",".$txt_brushing_remarks.",".$txt_dryer_machine_no.",".$txt_dryer_vibration.",".$txt_dryer_temperature.",".$txt_dryer_used_chemical.",".$txt_shearing_fabric_speed.",".$txt_shearing_drum_rpm.",".$txt_dryer_overfeed.",".$txt_dryer_after_dia.",".$txt_shearing_distance_blade.",".$txt_shearing_after_dia.",".$txt_dryer_speed.",".$txt_dryer_after_gsm.",".$txt_shearing_from_comber.",".$txt_shearing_after_gsm.",".$txt_dryer_dia_settings.",".$txt_dryer_remarks.",".$txt_shearing_remarks.",".$txt_stenter_machine_no_apb.",".$txt_stenter_dia_setting_apb.",".$txt_compacting_machine_no.",".$txt_compacting_steam_pressure.",".$txt_stenter_mc_brand_apb.",".$txt_stenter_padder_pressure_apb.",".$txt_compacting_mc_brand.",".$txt_compacting_blanket_pressure.",".$txt_stenter_no_chamber_apb.",".$txt_stenter_used_chemical_apb.",".$txt_compacting_temperature.",".$txt_compacting_after_dia.",".$txt_stenter_temperature_apb.",".$txt_stenter_after_dia_apb.",".$txt_compacting_speed.",".$txt_compacting_after_gsm.",".$txt_stenter_overfeed_apb.",".$txt_stenter_after_gsm_apb.",".$txt_compacting_over_feed.",".$txt_compacting_dia_setting.",".$txt_stenter_speed_apb.",".$txt_stenter_remarks_apb.",".$txt_compacting_remarks.",".$txt_fabWash_before_dia.",".$txt_fabWash_time.",".$finish_remarks.",".$txt_fabWash_before_gsm.",".$txt_fabWash_after_dia.",".$txt_fabWash_temperature.",".$txt_fabWash_after_gsm.",".$txt_fabWash_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		$rID=sql_insert("sample_archive_finishing_info",$field_array,$data_array,1);
		//   echo "10**=A=insert into sample_archive_finishing_info ($field_array) values $data_array";die;
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
  		$field_array_up="fabric_color_id*batch_no*slitting_machine_no*slitting_after_dia*peach_machine_no*peach_after_dia*slitting_process*slitting_remarks*peach_fabric_speed*peach_after_gsm*peach_drum_rpm*peach_tension*stenter_machine_no*stenter_dia_setting*peach_remarks*stenter_mc_brand*stenter_paddar_pressure*stenter_no_chamber*stenter_used_chemical*brushing_machine_no*brushing_pile_rpm_2nd*stenter_temperature*stenter_after_dia*brushing_fabric_speed*brushing_counter_pile_rpm_2nd*stenter_overfeed*stenter_after_gsm*brushing_tension*brushing_after_dia*stenter_speed*stenter_remarks*brushing_pile_rpm_1st*brushing_after_gsm*brushing_counter_pile_rpm_1st*brushing_remarks*dryer_machine_no*dryer_vibration*dryer_temperature*dryer_used_chemical*shearing_fabric_speed*shearing_drum_rpm*dryer_overfeed*dryer_after_dia*shearing_distance_blade*shearing_after_dia*dryer_speed*dryer_after_gsm*shearing_from_comber*shearing_after_gsm*dryer_dia_settings*dryer_remarks*shearing_remarks*stenter_machine_no_apb*stenter_dia_setting_apb*compacting_machine_no*compacting_steam_pressure*stenter_mc_brand_apb*stenter_padder_pressure_apb*compacting_mc_brand*compacting_blanket_pressure*stenter_no_chamber_apb*stenter_used_chemical_apb*compacting_temperature*compacting_after_dia*stenter_temperature_apb*stenter_after_dia_apb*compacting_speed*compacting_after_gsm*stenter_over_feed_apb*stenter_after_gsm_apb*compacting_over_feed*compacting_dia_setting*stenter_speed_apb*stenter_remarks_apb*compacting_remarks*fabwash_before_dia*fabwash_time*remarks*fabwash_before_gsm*fabwash_after_dia*fabwash_temperature*fabwash_after_gsm*fabwash_remarks*updated_by*update_date";
		//$data_array="(".$id_mst."*".$txt_batch.",".$txt_greige_dia.",".$txt_dia_setting.",".$txt_dia_extension.",".$txt_speed_min.",".$txt_hs_dia.",".$txt_mc_brand.",".$txt_greige_gsm.",".$txt_temperature.",".$txt_hs_gsm.",".$txt_no_of_chamber.",".$txt_chemical.",".$txt_overfeed.",".$txt_remarks.",".$txt_no_burners.",".$txt_intensity.",".$txt_singeing.",".$txt_burner_distance.",".$txt_singeing_pos.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		//,".$txt_dia.",".$txt_greige_dia."
		$data_array_up="".$cbo_finishing_fab_color_code."*".$cbo_finishing_batch_no."*".$txt_slitting_machine_no."*".$txt_slitting_after_dia."*".$txt_peach_machine_no."*".$txt_peach_after_dia."*".$txt_slitting_process."*".$txt_slitting_remarks."*".$txt_peach_fabric_speed."*".$txt_peach_after_gsm."*".$txt_peach_drum_rpm."*".$txt_peach_tension."*".$txt_stenter_machine_no."*".$txt_stenter_dia_setting."*".$txt_peach_remarks."*".$txt_stenter_mc_brand."*".$txt_stenter_paddar_pressure."*".$txt_stenter_no_chamber."*".$txt_stenter_used_chemical."*".$txt_brushing_machine_no."*".$txt_brushing_pile_rpm_2nd."*".$txt_stenter_temperature."*".$txt_stenter_after_dia."*".$txt_brushing_fabric_speed."*".$txt_brushing_counter_pile_rpm_2nd."*".$txt_stenter_overfeed."*".$txt_stenter_after_gsm."*".$txt_brushing_tension."*".$txt_brushing_after_dia."*".$txt_stenter_speed."*".$txt_stenter_remarks."*".$txt_brushing_pile_rpm_1st."*".$txt_brushing_after_gsm."*".$txt_brushing_counter_pile_rpm_1st."*".$txt_brushing_remarks."*".$txt_dryer_machine_no."*".$txt_dryer_vibration."*".$txt_dryer_temperature."*".$txt_dryer_used_chemical."*".$txt_shearing_fabric_speed."*".$txt_shearing_drum_rpm."*".$txt_dryer_overfeed."*".$txt_dryer_after_dia."*".$txt_shearing_distance_blade."*".$txt_shearing_after_dia."*".$txt_dryer_speed."*".$txt_dryer_after_gsm."*".$txt_shearing_from_comber."*".$txt_shearing_after_gsm."*".$txt_dryer_dia_settings."*".$txt_dryer_remarks."*".$txt_shearing_remarks."*".$txt_stenter_machine_no_apb."*".$txt_stenter_dia_setting_apb."*".$txt_compacting_machine_no."*".$txt_compacting_steam_pressure."*".$txt_stenter_mc_brand_apb."*".$txt_stenter_padder_pressure_apb."*".$txt_compacting_mc_brand."*".$txt_compacting_blanket_pressure."*".$txt_stenter_no_chamber_apb."*".$txt_stenter_used_chemical_apb."*".$txt_compacting_temperature."*".$txt_compacting_after_dia."*".$txt_stenter_temperature_apb."*".$txt_stenter_after_dia_apb."*".$txt_compacting_speed."*".$txt_compacting_after_gsm."*".$txt_stenter_overfeed_apb."*".$txt_stenter_after_gsm_apb."*".$txt_compacting_over_feed."*".$txt_compacting_dia_setting."*".$txt_stenter_speed_apb."*".$txt_stenter_remarks_apb."*".$txt_compacting_remarks."*".$txt_fabWash_before_dia."*".$txt_fabWash_time."*".$finish_remarks."*".$txt_fabWash_before_gsm."*".$txt_fabWash_after_dia."*".$txt_fabWash_temperature."*".$txt_fabWash_after_gsm."*".$txt_fabWash_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
 		$rID=sql_update("sample_archive_finishing_info",$field_array_up,$data_array_up,"id","".$finishing_update_id."",1);
		 //echo "10**=".$rID.'=';die;
 		if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con);
 				echo "1**".str_replace("'","",$finishing_update_id)."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$finishing_update_id)."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
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
		$rID1=sql_delete("sample_archive_finishing_info",$field_array,$data_array,"id","".$finishing_update_id."",0);
		 
		if($db_type==0)
		{
			if($rID1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$finishing_update_id)."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$finishing_update_id)."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID1)
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$finishing_update_id)."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
			}
			else
			{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$finishing_update_id)."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
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
if ($action=="listview_finishing_info")
{
	$booking_id=$data;
	$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst where status_active=1 and is_deleted=0", 'id', 'batch_no');
	$sql_result =sql_select("SELECT id,req_id,company_id,booking_no,booking_id,fabric_color_id,batch_no,slitting_machine_no,slitting_after_dia,peach_machine_no,peach_after_dia,slitting_process,slitting_remarks,peach_fabric_speed,peach_after_gsm,peach_drum_rpm,peach_tension,stenter_machine_no,stenter_dia_setting,peach_remarks,stenter_mc_brand,stenter_paddar_pressure,stenter_no_chamber,stenter_used_chemical,brushing_machine_no,brushing_pile_rpm_2nd,stenter_temperature,stenter_after_dia,brushing_fabric_speed,brushing_counter_pile_rpm_2nd,stenter_overfeed,stenter_after_gsm,brushing_tension,brushing_after_dia,stenter_speed,stenter_remarks,brushing_pile_rpm_1st,brushing_after_gsm,brushing_counter_pile_rpm_1st,brushing_remarks,dryer_machine_no,dryer_vibration,dryer_temperature,dryer_used_chemical,shearing_fabric_speed,shearing_drum_rpm,dryer_overfeed,dryer_after_dia,shearing_distance_blade,shearing_after_dia,dryer_speed,dryer_after_gsm,shearing_from_comber,shearing_after_gsm,dryer_dia_settings,dryer_remarks,shearing_remarks,stenter_machine_no_apb,stenter_dia_setting_apb,compacting_machine_no,compacting_steam_pressure,stenter_mc_brand_apb,stenter_padder_pressure_apb,compacting_mc_brand,compacting_blanket_pressure,stenter_no_chamber_apb,stenter_used_chemical_apb,compacting_temperature,compacting_after_dia,stenter_temperature_apb,stenter_after_dia_apb,compacting_speed,compacting_after_gsm,stenter_over_feed_apb,stenter_after_gsm_apb,compacting_over_feed,compacting_dia_setting,stenter_speed_apb,stenter_remarks_apb,compacting_remarks,fabwash_before_dia,fabwash_time,remarks,fabwash_before_gsm,fabwash_after_dia,fabwash_temperature,fabwash_after_gsm,fabwash_remarks from sample_archive_finishing_info a where booking_id=$booking_id and is_deleted=0  and a.status_active=1  order by id asc");
				
	?>
    <table width="600" cellspacing="0" border="1" rules="all" class="rpt_table" >
        <thead>
			
				<th width="290">Fab. Color/Code</th>
				<th width="290">Batch No</th>
          
        </thead>
    </table>
    <div style="width:620px; overflow-y:scroll; max-height:180px;">
        <table width="600" cellspacing="0" border="1" rules="all" class="rpt_table" id="tbl_details_finishing">
			<?
				$i=1;
               	foreach ($sql_result as $row)
               	{
				   if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
            		<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="get_php_form_data('<? echo $row[csf('id')]; ?>', 'populate_details_finishing_form_data', 'requires/finishing_info_entry_controller');"> 

						<td width="290"><? echo $color_arr[$row[csf('fabric_color_id')]]; ?></td>
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
if($action=="populate_details_finishing_form_data")
{  
 	
	 $sql_result =sql_select("SELECT  id,req_id,company_id,booking_no,booking_id,fabric_color_id,batch_no,slitting_machine_no,slitting_after_dia,peach_machine_no,peach_after_dia,slitting_process,slitting_remarks,peach_fabric_speed,peach_after_gsm,peach_drum_rpm,peach_tension,stenter_machine_no,stenter_dia_setting,peach_remarks,stenter_mc_brand,stenter_paddar_pressure,stenter_no_chamber,stenter_used_chemical,brushing_machine_no,brushing_pile_rpm_2nd,stenter_temperature,stenter_after_dia,brushing_fabric_speed,brushing_counter_pile_rpm_2nd,stenter_overfeed,stenter_after_gsm,brushing_tension,brushing_after_dia,stenter_speed,stenter_remarks,brushing_pile_rpm_1st,brushing_after_gsm,brushing_counter_pile_rpm_1st,brushing_remarks,dryer_machine_no,dryer_vibration,dryer_temperature,dryer_used_chemical,shearing_fabric_speed,shearing_drum_rpm,dryer_overfeed,dryer_after_dia,shearing_distance_blade,shearing_after_dia,dryer_speed,dryer_after_gsm,shearing_from_comber,shearing_after_gsm,dryer_dia_settings,dryer_remarks,shearing_remarks,stenter_machine_no_apb,stenter_dia_setting_apb,compacting_machine_no,compacting_steam_pressure,stenter_mc_brand_apb,stenter_padder_pressure_apb,compacting_mc_brand,compacting_blanket_pressure,stenter_no_chamber_apb,stenter_used_chemical_apb,compacting_temperature,compacting_after_dia,stenter_temperature_apb,stenter_after_dia_apb,compacting_speed,compacting_after_gsm,stenter_over_feed_apb,stenter_after_gsm_apb,compacting_over_feed,compacting_dia_setting,stenter_speed_apb,stenter_remarks_apb,compacting_remarks,fabwash_before_dia,fabwash_time,remarks,fabwash_before_gsm,fabwash_after_dia,fabwash_temperature,fabwash_after_gsm,fabwash_remarks from sample_archive_finishing_info   where  id=$data and  is_deleted=0  and  status_active=1  order by  id asc");


	foreach($sql_result as $row)
	{ 
 		
		echo "$('#finishing_update_id').val('".$row[csf('id')]."');\n";
		$batch_no=$row[csf('batch_no')];
		echo "$('#cbo_finishing_fab_color_code').val('".$row[csf('fabric_color_id')]."');\n";
		
   	}
	   echo "fnc_finishing_info_batch_load(".$row[csf('fabric_color_id')].");\n";
	   echo "$('#cbo_finishing_batch_no').val('".$row[csf('batch_no')]."');\n";
	   echo "$('#txt_slitting_machine_no').val('".$row[csf('slitting_machine_no')]."');\n";
	   echo "$('#txt_slitting_after_dia').val('".$row[csf('slitting_after_dia')]."');\n";
	   echo "$('#txt_peach_machine_no').val('".$row[csf('peach_machine_no')]."');\n";
	   echo "$('#txt_peach_after_dia').val('".$row[csf('peach_after_dia')]."');\n";
	   echo "$('#txt_slitting_process').val('".$row[csf('slitting_process')]."');\n";
	   echo "$('#txt_slitting_remarks').val('".$row[csf('slitting_remarks')]."');\n";
	   echo "$('#txt_peach_fabric_speed').val('".$row[csf('peach_fabric_speed')]."');\n";
	   echo "$('#txt_peach_after_gsm').val('".$row[csf('peach_after_gsm')]."');\n";
	   echo "$('#txt_peach_drum_rpm').val('".$row[csf('peach_drum_rpm')]."');\n";
	   echo "$('#txt_peach_tension').val('".$row[csf('peach_tension')]."');\n";
	   echo "$('#txt_stenter_machine_no').val('".$row[csf('stenter_machine_no')]."');\n";
	   echo "$('#txt_stenter_dia_setting').val('".$row[csf('stenter_dia_setting')]."');\n";
	   echo "$('#txt_peach_remarks').val('".$row[csf('peach_remarks')]."');\n";
	   echo "$('#txt_stenter_mc_brand').val('".$row[csf('stenter_mc_brand')]."');\n";
	   echo "$('#txt_stenter_paddar_pressure').val('".$row[csf('stenter_paddar_pressure')]."');\n";
	   echo "$('#txt_stenter_no_chamber').val('".$row[csf('stenter_no_chamber')]."');\n";
	   echo "$('#txt_stenter_used_chemical').val('".$row[csf('stenter_used_chemical')]."');\n";
	   echo "$('#txt_brushing_machine_no').val('".$row[csf('brushing_machine_no')]."');\n";
	   echo "$('#txt_brushing_pile_rpm_2nd').val('".$row[csf('brushing_pile_rpm_2nd')]."');\n";
	   echo "$('#txt_stenter_temperature').val('".$row[csf('stenter_temperature')]."');\n";
	   echo "$('#txt_stenter_after_dia').val('".$row[csf('stenter_after_dia')]."');\n";
	   echo "$('#txt_brushing_fabric_speed').val('".$row[csf('brushing_fabric_speed')]."');\n";
	   echo "$('#txt_brushing_counter_pile_rpm_2nd').val('".$row[csf('brushing_counter_pile_rpm_2nd')]."');\n";
	   echo "$('#txt_stenter_overfeed').val('".$row[csf('stenter_overfeed')]."');\n";
	   echo "$('#txt_stenter_after_gsm').val('".$row[csf('stenter_after_gsm')]."');\n";
	   echo "$('#txt_brushing_tension').val('".$row[csf('brushing_tension')]."');\n";
	   echo "$('#txt_brushing_after_dia').val('".$row[csf('brushing_after_dia')]."');\n";
	   echo "$('#txt_stenter_speed').val('".$row[csf('stenter_speed')]."');\n";
	   echo "$('#txt_stenter_remarks').val('".$row[csf('stenter_remarks')]."');\n";
	   echo "$('#txt_brushing_pile_rpm_1st').val('".$row[csf('brushing_pile_rpm_1st')]."');\n";
	   echo "$('#txt_brushing_after_gsm').val('".$row[csf('brushing_after_gsm')]."');\n";
	   echo "$('#txt_brushing_counter_pile_rpm_1st').val('".$row[csf('brushing_counter_pile_rpm_1st')]."');\n";
	   echo "$('#txt_brushing_remarks').val('".$row[csf('brushing_remarks')]."');\n";
	   echo "$('#txt_dryer_machine_no').val('".$row[csf('dryer_machine_no')]."');\n";
	   echo "$('#txt_dryer_vibration').val('".$row[csf('dryer_vibration')]."');\n";
	   echo "$('#txt_dryer_temperature').val('".$row[csf('dryer_temperature')]."');\n";
	   echo "$('#txt_dryer_used_chemical').val('".$row[csf('dryer_used_chemical')]."');\n";
	   echo "$('#txt_shearing_fabric_speed').val('".$row[csf('shearing_fabric_speed')]."');\n";
	   echo "$('#txt_shearing_drum_rpm').val('".$row[csf('shearing_drum_rpm')]."');\n";
	   echo "$('#txt_dryer_overfeed').val('".$row[csf('dryer_overfeed')]."');\n";
	   echo "$('#txt_dryer_after_dia').val('".$row[csf('dryer_after_dia')]."');\n";
	   echo "$('#txt_shearing_distance_blade').val('".$row[csf('shearing_distance_blade')]."');\n";
	   echo "$('#txt_shearing_after_dia').val('".$row[csf('shearing_after_dia')]."');\n";
	   echo "$('#txt_dryer_speed').val('".$row[csf('dryer_speed')]."');\n";
	   echo "$('#txt_dryer_after_gsm').val('".$row[csf('dryer_after_gsm')]."');\n";
	   echo "$('#txt_shearing_from_comber').val('".$row[csf('shearing_from_comber')]."');\n";
	   echo "$('#txt_shearing_after_gsm').val('".$row[csf('shearing_after_gsm')]."');\n";
	   echo "$('#txt_dryer_dia_settings').val('".$row[csf('dryer_dia_settings')]."');\n";
	   echo "$('#txt_dryer_remarks').val('".$row[csf('dryer_remarks')]."');\n";
	   echo "$('#txt_shearing_remarks').val('".$row[csf('shearing_remarks')]."');\n";
	   echo "$('#txt_stenter_machine_no_apb').val('".$row[csf('stenter_machine_no_apb')]."');\n";
	   echo "$('#txt_stenter_dia_setting_apb').val('".$row[csf('stenter_dia_setting_apb')]."');\n";
	   echo "$('#txt_compacting_machine_no').val('".$row[csf('compacting_machine_no')]."');\n";
	   echo "$('#txt_compacting_steam_pressure').val('".$row[csf('compacting_steam_pressure')]."');\n";
	   echo "$('#txt_stenter_mc_brand_apb').val('".$row[csf('stenter_mc_brand_apb')]."');\n";
	   echo "$('#txt_stenter_padder_pressure_apb').val('".$row[csf('stenter_padder_pressure_apb')]."');\n";
	   echo "$('#txt_compacting_mc_brand').val('".$row[csf('compacting_mc_brand')]."');\n";
	   echo "$('#txt_compacting_blanket_pressure').val('".$row[csf('compacting_blanket_pressure')]."');\n";
	   echo "$('#txt_stenter_no_chamber_apb').val('".$row[csf('stenter_no_chamber_apb')]."');\n";
	   echo "$('#txt_stenter_used_chemical_apb').val('".$row[csf('stenter_used_chemical_apb')]."');\n";
	   echo "$('#txt_compacting_temperature').val('".$row[csf('compacting_temperature')]."');\n";
	   echo "$('#txt_compacting_after_dia').val('".$row[csf('compacting_after_dia')]."');\n";
	   echo "$('#txt_stenter_temperature_apb').val('".$row[csf('stenter_temperature_apb')]."');\n";
	   echo "$('#txt_stenter_after_dia_apb').val('".$row[csf('stenter_after_dia_apb')]."');\n";
	   echo "$('#txt_compacting_speed').val('".$row[csf('compacting_speed')]."');\n";
	   echo "$('#txt_compacting_after_gsm').val('".$row[csf('compacting_after_gsm')]."');\n";
	   echo "$('#txt_stenter_overfeed_apb').val('".$row[csf('stenter_over_feed_apb')]."');\n";
	   echo "$('#txt_stenter_after_gsm_apb').val('".$row[csf('stenter_after_gsm_apb')]."');\n";
	   echo "$('#txt_compacting_over_feed').val('".$row[csf('compacting_over_feed')]."');\n";
	   echo "$('#txt_compacting_dia_setting').val('".$row[csf('compacting_dia_setting')]."');\n";
	   echo "$('#txt_stenter_speed_apb').val('".$row[csf('stenter_speed_apb')]."');\n";
	   echo "$('#txt_stenter_remarks_apb').val('".$row[csf('stenter_remarks_apb')]."');\n";
	   echo "$('#txt_compacting_remarks').val('".$row[csf('compacting_remarks')]."');\n";
	   echo "$('#txt_fabWash_before_dia').val('".$row[csf('fabwash_before_dia')]."');\n";
	   echo "$('#txt_fabWash_time').val('".$row[csf('fabwash_time')]."');\n";
	   echo "$('#finish_remarks').val('".$row[csf('remarks')]."');\n";
	   echo "$('#txt_fabWash_before_gsm').val('".$row[csf('fabwash_before_gsm')]."');\n";
	   echo "$('#txt_fabWash_after_dia').val('".$row[csf('fabwash_after_dia')]."');\n";
	   echo "$('#txt_fabWash_temperature').val('".$row[csf('fabwash_temperature')]."');\n";
	   echo "$('#txt_fabWash_after_gsm').val('".$row[csf('fabwash_after_gsm')]."');\n";
	   echo "$('#txt_fabWash_remarks').val('".$row[csf('fabwash_remarks')]."');\n";

	if(count($sql_result)>0)
	{
		
	echo "$('#save7').removeClass('formbutton').addClass('formbutton_disabled');\n"; //formbutton 
	echo "$('#save7').removeAttr('onclick').attr('onclick','fnc_finishing_entry(0);')\n";
	echo "$('#update7').removeClass('formbutton_disabled').addClass('formbutton');\n"; //formbutton 
    echo "$('#update7').removeAttr('onclick').attr('onclick','fnc_finishing_entry(1);')\n";
	echo "$('#Delete7').removeClass('formbutton_disabled').addClass('formbutton');\n"; //formbutton 
	echo "$('#Delete7').removeAttr('onclick').attr('onclick','fnc_finishing_entry(2);')\n"; 
		
	}
	else
	{
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_finishing_entry',6);\n"; 
	}
	 	 
   	unlink($sql_result);
 	exit();	
}	



?>
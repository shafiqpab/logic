<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
$permission=$_SESSION['page_permission'];
include('../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
//$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
 $color_arr=return_library_array( "select id, color_name from lib_color where status_active=1", "id", "color_name");

	if($action=="batching_info_from_sample_populate")
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
	 
		$fab_color_drop=create_drop_down( "cbo_fabric_batch_color_code", 270, $fabric_colorArr,"", 1, "-- select --","","fnc_batching_info_batch_load(this.value)" );
		echo "document.getElementById('fabric_batch_color_td').innerHTML = '".$fab_color_drop."';\n";
		exit();		
	}	
	if ($action=="load_drop_down_batching_batch")
	{

		$dataArr=explode("_",$data);
		$color_id=$dataArr[0];
		$booking_no=$dataArr[1]; 

		$sql_all_batch=sql_select("select id,batch_no from pro_batch_create_mst where booking_no='$booking_no' and color_id=$color_id and entry_form=0 and status_active = 1 and is_deleted = 0");
		foreach ($sql_all_batch as $row)
		{
			$finishing_batch_no_arr[$row[csf('id')]]=$row[csf('batch_no')];
		}
		
		$finishing_batch_no=create_drop_down( "cbo_batch_no", 270, $finishing_batch_no_arr,"", 1, "-- select --","");
		echo "document.getElementById('batch_no_td').innerHTML = '".$finishing_batch_no."';\n";
		exit();
	}	
	
	// if($action=="knitting_info_from_sample_populate")
	// {  
	// 	//  $color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	// 	$dataArr=explode("_",$data);
	// 	$booking_id=$dataArr[0];
	// 	$booking_no=$dataArr[1];
		
		
	// 	$res_mst = sql_select("SELECT b.id as req_id,a.id,b.company_id, a.booking_no_prefix_num, a.booking_no, b.company_id, b.buyer_name, b.style_ref_no, c.color_type_id,c.sample_type,c.fabric_color,c.lib_yarn_count_deter_id as deter_id,c.fabric_description,c.composition,c.gsm_weight,c.dia_width,c.dtls_id FROM sample_development_mst b, wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls c WHERE a.id=$booking_id and a.entry_form_id = 140 AND a.status_active = 1 AND a.booking_no=c.booking_no and b.id=c.style_id AND a.is_deleted = 0");
		
		
	// 	foreach($res_mst as $result)
	// 	{ 
	// 		$booking_id=$result[csf('id')];
	// 		$dtls_idArr[$result[csf('dtls_id')]]=$result[csf('dtls_id')];
	// 		$req_id=$result[csf('req_id')];
	// 		$booking_no=$result[csf('booking_no')];
	// 		$company_id=$result[csf('company_id')];
	// 		$style_ref_no=$result[csf('style_ref_no')];
	// 		$buyer_name=$buyer_arr[$result[csf('buyer_name')]];
	// 		$fabric_colorArr[$result[csf('fabric_color')]]=$color_arr[$result[csf('fabric_color')]];
	// 		if($result[csf('sample_type')])
	// 		{
	// 			$sample_typeArr[$result[csf('sample_type')]]=$sample_type[$result[csf('sample_type')]];
	// 		}
	// 		$fabricDescArr[$result[csf('deter_id')]]=$result[csf('fabric_description')];
	// 		if($result[csf('color_type_id')])
	// 		{
	// 		$colorTypeArr[$color_type[$result[csf('color_type_id')]]]=$color_type[$result[csf('color_type_id')]];
	// 		$colorTypeIdArr[$result[csf('color_type_id')]]=$result[csf('color_type_id')];
	// 		}
		
			
	// 	}
	// 	$sql_plan="select b.id as prog_no from ppl_planning_info_entry_dtls b,ppl_planning_entry_plan_dtls c where b.id=c.dtls_id and c.booking_no='$booking_no' and b.status_active=1  and c.status_active=1";
	// 	$sql_plan=sql_select($sql_plan);
	// 	foreach($sql_plan as $row)
	// 	{ 
	// 	$prog_noArr[$row[csf('prog_no')]]=$row[csf('prog_no')];
	// 	}
		
		
		
		
	// 	$fab_color_drop=create_drop_down( "cbo_knit_fab_color_code", 150, $fabric_colorArr,"", 1, "-- select --" );
	// 	$prog_drop=create_drop_down( "cbo_prog_no", 400, $prog_noArr,"", 1, "-- select --","","fnc_knit_construction_load(this.value)" );
	// 		//load_drop_down( 'yarn_allocation_controller', this.value, 'load_drop_down_yarn_composition', 'yarn_composition_td1' )
		
	// 		echo "document.getElementById('knit_color_td').innerHTML = '".$fab_color_drop."';\n";
	// 		echo "document.getElementById('prog_no_td').innerHTML = '".$prog_drop."';\n";
	// 		// echo "$('#txt_yarn_color_type').val('".implode(",",$colorTypeArr)."');\n";
			
		
	// 	exit();	
	// }	
	// if ($action=="load_drop_down_knit_construction")
	// {
	// 	$data=explode("_",$data);

	// 	$booking_no=$data[0];
	// 	$color=$data[1];
	// 	$prog_no=$data[2];
		
	// 	$machine_arr = return_library_array("select id, machine_no from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0", 'id', 'machine_no');
		
	// 	$sql_cons=sql_select("select b.id ,b.construction,c.machine_dia,c.machine_gg,c.width_dia_type,c.machine_id from lib_yarn_count_determina_mst b,ppl_planning_entry_plan_dtls a,ppl_planning_info_entry_dtls c where b.id=a.determination_id   and a.booking_no='$booking_no'  and a.dtls_id=c.id  and a.dtls_id='$prog_no' and b.status_active=1  and a.status_active=1  and c.status_active=1");
		
	// 		$machine_noArr=array();
	// 		foreach ($sql_cons as $row)
	// 		{
	// 			if($row[csf('machine_id')])
	// 			{
	// 				$machine_idArr=array_unique(explode(",",$row[csf('machine_id')]));
	// 				foreach ($machine_idArr as $mid)
	// 				{
	// 						$machine_noArr[$mid]=$machine_arr[$mid];
	// 				}
	// 			}
				
	// 			if($row[csf('construction')])
	// 			{
	// 			$knit_constructionArr[$row[csf('id')]]=$row[csf('construction')];
	// 			}
	// 			if($row[csf('machine_dia')])
	// 			{
	// 			$knit_diaArr[$row[csf('machine_dia')]]=$row[csf('machine_dia')];
	// 			}
	// 			if($row[csf('machine_gg')])
	// 			{
	// 			$knit_machine_ggArr[$row[csf('machine_gg')]]=$row[csf('machine_gg')];
	// 			}
	// 			if($row[csf('width_dia_type')])
	// 			{
	// 			$knit_dia_typeArr[$fabric_typee[$row[csf('width_dia_type')]]]=$fabric_typee[$row[csf('width_dia_type')]];
	// 			}
				
	// 		}
			
	// 		$cbo_knit_construction=create_drop_down( "cbo_construction", 200, $knit_constructionArr,"", 1, "-- select --","","" );
	// 		echo "document.getElementById('construction_td').innerHTML = '".$cbo_knit_construction."';\n";
			
	// 		echo "$('#txt_dia_type').val('".implode(", ",array_unique($knit_dia_typeArr))."');\n";
	// 		echo "$('#txt_brand_dia_type').val('".implode(", ",array_unique($dia_typeArr))."');\n";
	// 		echo "$('#txt_mc_no').val('".implode(", ",array_unique($machine_noArr))."');\n";
	// 		echo "$('#txt_mc_gauge').val('".implode(", ",array_unique($knit_machine_ggArr))."');\n";
	// 		echo "$('#txt_brand_dia_type').val('".implode(", ",array_unique($knit_dia_typeArr))."');\n";
			
		
	// 	exit();
	// }

 

if ($action=="save_update_delete_batch")
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
 		$id_mst=return_next_id( "id", "sample_archive_batch_info", 1 ) ;
 		
 

 		$field_array="id, req_id,company_id,booking_no,booking_id,fabric_color_id,batch_no,greige_dia,dia_setting,dia_extension,speed,hs_dia,mc_name_brand,greige_gsm,temprature,hs_gsm,no_of_chamber,using_chemical,overfeed,remarks,no_of_burners,speed_min,intensity,singeing_type,burner_distance,position,inserted_by, insert_date, status_active, is_deleted";
		$data_array="(".$id_mst.",".$req_id.",".$company_id.",".$txt_booking_no.",".$txt_booking_id.",".$cbo_fabric_batch_color_code.",".$cbo_batch_no.",".$txt_batching_greige_dia.",".$txt_dia_setting.",".$txt_dia_extension.",".$txt_batching_speed.",".$txt_hs_dia.",".$txt_mc_name_brand.",".$txt_batching_greige_gsm.",".$txt_temperature.",".$txt_hs_gsm.",".$txt_no_of_chamber.",".$txt_chemical.",".$txt_overfeed.",".$txt_batching_remarks.",".$txt_no_burners.",".$txt_speed_min.",".$txt_intensity.",".$txt_singeing.",".$txt_burner_distance.",".$txt_singeing_pos.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		$rID=sql_insert("sample_archive_batch_info",$field_array,$data_array,1);
		   // echo "10**=A=insert into sample_archive_batch_info ($field_array) values $data_array";die;
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
  		$field_array_up="fabric_color_id*batch_no*greige_dia*dia_setting*dia_extension*speed*hs_dia*mc_name_brand*greige_gsm*temprature*hs_gsm*no_of_chamber*using_chemical*overfeed*remarks*no_of_burners*speed_min*intensity*singeing_type*burner_distance*position*updated_by*update_date";
		//$data_array="(".$id_mst."*".$txt_batch.",".$txt_batching_greige_dia.",".$txt_dia_setting.",".$txt_dia_extension.",".$txt_batching_speed.",".$txt_hs_dia.",".$txt_mc_brand.",".$txt_batching_greige_gsm.",".$txt_temperature.",".$txt_hs_gsm.",".$txt_no_of_chamber.",".$txt_chemical.",".$txt_overfeed.",".$txt_batching_remarks.",".$txt_no_burners.",".$txt_intensity.",".$txt_singeing.",".$txt_burner_distance.",".$txt_singeing_pos.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		//,".$txt_dia.",".$txt_batching_greige_dia."
		$data_array_up="".$cbo_fabric_batch_color_code."*".$cbo_batch_no."*".$txt_batching_greige_dia."*".$txt_dia_setting."*".$txt_dia_extension."*".$txt_batching_speed."*".$txt_hs_dia."*".$txt_mc_name_brand."*".$txt_batching_greige_gsm."*".$txt_temperature."*".$txt_hs_gsm."*".$txt_no_of_chamber."*".$txt_chemical."*".$txt_overfeed."*".$txt_batching_remarks."*".$txt_no_burners."*".$txt_speed_min."*".$txt_intensity."*".$txt_singeing."*".$txt_burner_distance."*".$txt_singeing_pos."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
 		$rID=sql_update("sample_archive_batch_info",$field_array_up,$data_array_up,"id","".$batchting_update_id."",1);
		 //echo "10**=".$rID.'=';die;
 		if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con);
 				echo "1**".str_replace("'","",$batchting_update_id)."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$batchting_update_id)."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
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
		$rID1=sql_delete("sample_archive_batch_info",$field_array,$data_array,"id","".$batchting_update_id."",0);
		 
		if($db_type==0)
		{
			if($rID1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$batchting_update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$batchting_update_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID1)
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$batchting_update_id)."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
			}
			else
			{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$batchting_update_id)."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
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
}
if ($action=="listview_batch_info")
{
	$booking_id=$data;
	$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst where status_active=1 and is_deleted=0", 'id', 'batch_no');
	
	$sql_result =sql_select("SELECT id,req_id,company_id,booking_no,booking_id,batch_no,greige_dia,dia_setting,dia_extension,speed,hs_dia,mc_name_brand,greige_gsm,temprature,hs_gsm,no_of_chamber,using_chemical,overfeed,remarks,no_of_burners,intensity from sample_archive_batch_info a where booking_id=$booking_id and is_deleted=0  and a.status_active=1  order by id asc");
	
	 
	//$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	//$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
				
	?>
    <table width="620" cellspacing="0" border="1" rules="all" class="rpt_table" >
        <thead>
			
            <th width="300">Batch No</th>
            <th width="300">M/C Name & Brand</th>
            
          
        </thead>
    </table>
    <div style="width:620px; overflow-y:scroll; max-height:180px;">
        <table width="600" cellspacing="0" border="1" rules="all" class="rpt_table" id="tbl_details_batch">
			<?
				$i=1;
               	foreach ($sql_result as $row)
               	{
				   if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
				?>
            		<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="get_php_form_data('<? echo $row[csf('id')]; ?>', 'populate_details_batch_form_data', 'requires/batchting_info_entry_controller');"> 
						
                		<td width="300"><? echo $batch_arr[$row[csf('batch_no')]]; ?></td>
                        <td width="300"><p><? echo $row[csf('mc_name_brand')];; ?></p></td>
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
if($action=="populate_details_batch_form_data")
{  

	$sql_result =sql_select("SELECT  id, req_id, company_id, booking_no, booking_id,fabric_color_id, batch_no,greige_dia,dia_setting,dia_extension,speed,speed_min,hs_dia,mc_name_brand,greige_gsm,temprature,hs_gsm,no_of_chamber,using_chemical,overfeed,remarks,no_of_burners,intensity,singeing_type,burner_distance,position from sample_archive_batch_info   where  id=$data and  is_deleted=0  and  status_active=1  order by  id asc");
	
	foreach($sql_result as $row)
	{ 
		echo "$('#batchting_update_id').val('".$row[csf('id')]."');\n";
		//echo "$('#txt_batch').val('".$row[csf('batch_no')]."');\n";
		$batch_no=$row[csf('batch_no')];
		echo "$('#cbo_fabric_batch_color_code').val('".$row[csf('fabric_color_id')]."');\n";
		
   	}
	   echo "fnc_batching_info_batch_load(".$row[csf('fabric_color_id')].");\n";
	   //echo "$('#txt_batch').val('".$row[csf('batch_no')]."');\n";
	   echo "$('#cbo_batch_no').val('".$row[csf('batch_no')]."');\n";
	   echo "$('#txt_dia_setting').val('".$row[csf('dia_setting')]."');\n";
	   echo "$('#txt_batching_speed').val('".$row[csf('speed')]."');\n";
	   echo "$('#txt_dia_extension').val('".$row[csf('dia_extension')]."');\n";
	   echo "$('#txt_batching_greige_dia').val('".$row[csf('greige_dia')]."');\n";
	   echo "$('#txt_hs_dia').val('".$row[csf('hs_dia')]."');\n";
	   echo "$('#txt_mc_name_brand').val('".$row[csf('mc_name_brand')]."');\n";
	   echo "$('#txt_batching_greige_gsm').val('".$row[csf('greige_gsm')]."');\n";
	   echo "$('#txt_temperature').val('".$row[csf('temprature')]."');\n";
	   echo "$('#txt_no_of_chamber').val('".$row[csf('no_of_chamber')]."');\n";
	   echo "$('#txt_hs_gsm').val('".$row[csf('hs_gsm')]."');\n";
	   echo "$('#txt_chemical').val('".$row[csf('using_chemical')]."');\n";
	   echo "$('#txt_overfeed').val('".$row[csf('overfeed')]."');\n";
	   echo "$('#txt_batching_remarks').val('".$row[csf('remarks')]."');\n";
	   echo "$('#txt_no_burners').val('".$row[csf('no_of_burners')]."');\n";
	   echo "$('#txt_speed_min').val('".$row[csf('speed_min')]."');\n";
	   echo "$('#txt_intensity').val('".$row[csf('intensity')]."');\n";
	   echo "$('#txt_singeing').val('".$row[csf('intensity')]."');\n";
	   echo "$('#txt_burner_distance').val('".$row[csf('burner_distance')]."');\n";
	   echo "$('#txt_singeing_pos').val('".$row[csf('position')]."');\n";
	
	
	if(count($sql_result)>0)
	{
		
	echo "$('#save4').removeClass('formbutton').addClass('formbutton_disabled');\n"; //formbutton 
	
	echo "$('#save4').removeAttr('onclick').attr('onclick','fnc_batch_entry(0);')\n";
	echo "$('#update4').removeClass('formbutton_disabled').addClass('formbutton');\n"; //formbutton 
    echo "$('#update4').removeAttr('onclick').attr('onclick','fnc_batch_entry(1);')\n";
	echo "$('#Delete4').removeClass('formbutton_disabled').addClass('formbutton');\n"; //formbutton 
	echo "$('#Delete4').removeAttr('onclick').attr('onclick','fnc_batch_entry(2);')\n"; 
		//echo "fnc_yarn_button_status(2);\n"; 
		
	}
	else
	{
		//echo "fnc_yarn_button_status(1);\n"; 
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_batch_entry',4);\n"; 
	}
	 	 
	
		
   	unlink($sql_result);
 	exit();	
}	



?>
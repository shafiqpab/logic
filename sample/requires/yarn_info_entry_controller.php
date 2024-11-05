<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
$permission=$_SESSION['page_permission'];
include('../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
 $color_arr=return_library_array( "select id, color_name from lib_color where status_active=1", "id", "color_name");
 
 if($action=="yarn_info_from_sample_populate")
{  
 	//  $color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
    $res_mst = sql_select("SELECT b.id as req_id,a.id,b.company_id, a.booking_no_prefix_num, a.booking_no, b.company_id, b.buyer_name, b.style_ref_no, c.color_type_id,c.sample_type,c.fabric_color,c.lib_yarn_count_deter_id as deter_id,c.fabric_description,c.composition,c.gsm_weight,c.dia_width,c.dtls_id FROM sample_development_mst b, wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls c WHERE a.id=$data and a.entry_form_id = 140 AND a.status_active = 1 AND a.booking_no=c.booking_no and b.id=c.style_id AND a.is_deleted = 0");
	 
	 
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
         $fabricDescArr[$result[csf('deter_id')]]=$result[csf('fabric_description')];
         if($result[csf('color_type_id')])
         {
          $colorTypeArr[$color_type[$result[csf('color_type_id')]]]=$color_type[$result[csf('color_type_id')]];
		   $colorTypeIdArr[$result[csf('color_type_id')]]=$result[csf('color_type_id')];
         }
       
         
    }
	 

	
	 $fab_color_drop=create_drop_down( "cbo_yarn_fab_color_code", 150, $fabric_colorArr,"", 1, "-- select --" );
     $fab_desc_drop=create_drop_down( "cbo_yarn_fabrication", 150, $fabricDescArr,"", 1, "-- select --","","fnc_compostion_load(this.value)" );
   //load_drop_down( 'yarn_allocation_controller', this.value, 'load_drop_down_yarn_composition', 'yarn_composition_td1' )
      
        echo "document.getElementById('yarn_color_td').innerHTML = '".$fab_color_drop."';\n";
        echo "document.getElementById('yarn_fabric_td').innerHTML = '".$fab_desc_drop."';\n";
	    echo "$('#txt_yarn_color_type').val('".implode(",",$colorTypeArr)."');\n";
		
	
 	exit();	
}	
if ($action=="load_drop_down_yarn_composition")
{

	$data=explode("_",$data);
	$deter_id=$data[0];
	$booking_no=$data[1];
	$sql_allo_comp=sql_select("select b.yarn_comp_type1st,b.yarn_comp_percent1st,c.color_type_id from product_details_master b,inv_material_allocation_dtls a,wo_non_ord_samp_booking_dtls c where b.id=a.item_id and a.booking_no=c.booking_no and a.booking_no='$booking_no' and b.item_category_id=1 and c.lib_yarn_count_deter_id=$deter_id group by b.yarn_comp_type1st,b.yarn_comp_percent1st,c.color_type_id");

	 	foreach ($sql_allo_comp as $row)
		{
			$yarn_compositionArr[$row[csf('yarn_comp_type1st')]]=$composition[$row[csf('yarn_comp_type1st')]];
			$colorTypeArr[$color_type[$row[csf('color_type_id')]]]=$color_type[$row[csf('color_type_id')]];
		}
		//print_r($colorTypeArr);
		
		$cbo_yarn_composition_1=create_drop_down( "cbo_yarn_composition_1", 100, $yarn_compositionArr,"", 1, "-- select --","","fnc_count_load(this.value,1)" );
		$cbo_yarn_composition_2=create_drop_down( "cbo_yarn_composition_2", 100, $yarn_compositionArr,"", 1, "-- select --","","fnc_count_load(this.value,2)" );
		$cbo_yarn_composition_3=create_drop_down( "cbo_yarn_composition_3", 100, $yarn_compositionArr,"", 1, "-- select --" ,"","fnc_count_load(this.value,3)");
		$cbo_yarn_composition_4=create_drop_down( "cbo_yarn_composition_4", 100, $yarn_compositionArr,"", 1, "-- select --" ,"","fnc_count_load(this.value,4)");

		$yarn_color_type_drop=create_drop_down( "txt_yarn_color_type", 150, $colorTypeArr,"", 1, "-- select --","","" );
		echo "document.getElementById('txt_yarn_color_type_td').innerHTML = '".$yarn_color_type_drop."';\n";
		//echo "$('#txt_yarn_color_type').val('".implode(",",$colorTypeArr)."');\n";
		echo "document.getElementById('yarn_composition_td1').innerHTML = '".$cbo_yarn_composition_1."';\n";
		echo "document.getElementById('yarn_composition_td2').innerHTML = '".$cbo_yarn_composition_2."';\n";
		echo "document.getElementById('yarn_composition_td3').innerHTML = '".$cbo_yarn_composition_3."';\n";
		echo "document.getElementById('yarn_composition_td4').innerHTML = '".$cbo_yarn_composition_4."';\n";
	 
	exit();
}
if ($action=="load_drop_down_yarn_count_brand")
{
	$data=explode("_",$data);
	
	$compositionId=$data[0];
	$booking_no=$data[1];
	$deter_id=$data[2];
	$type_id=$data[3];
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	
	$sql_allo_comp=sql_select("select b.yarn_count_id,b.brand,b.lot from product_details_master b,inv_material_allocation_dtls a,wo_non_ord_samp_booking_dtls c where b.id=a.item_id and a.booking_no=c.booking_no and a.booking_no='$booking_no' and b.yarn_comp_type1st=$compositionId
	 and b.item_category_id=1 and c.lib_yarn_count_deter_id=$deter_id group by b.yarn_count_id,b.brand,b.lot");

	 
	 	foreach ($sql_allo_comp as $row)
		{
			$yarn_countArr[$row[csf('yarn_count_id')]]=$count_arr[$row[csf('yarn_count_id')]];
			$yarn_brandArr[$row[csf('brand')]]=$brand_arr[$row[csf('brand')]];
			$lot=$row[csf('lot')];
			$cbo_yarn_brand_res=$row[csf('brand')];
		}
		if($type_id==1)
		{
		$cbo_yarn_count_1=create_drop_down( "cbo_yarn_count_1", 100, $yarn_countArr,"", 0, "-- select --","","" );
		echo "document.getElementById('yarn_count_td1').innerHTML = '".$cbo_yarn_count_1."';\n";
		echo "document.getElementById('cbo_yarn_brand_1').value = '".$brand_arr[$cbo_yarn_brand_res]."';\n";
		echo "document.getElementById('cbo_lot_1').value = '".$lot."';\n";
		}
		if($type_id==2)
		{
			$cbo_yarn_count_2=create_drop_down( "cbo_yarn_count_2", 100, $yarn_countArr,"", 0, "-- select --","","" );
			echo "document.getElementById('yarn_count_td2').innerHTML = '".$cbo_yarn_count_2."';\n";
			echo "document.getElementById('cbo_yarn_brand_2').value = '".$brand_arr[$cbo_yarn_brand_res]."';\n";
			echo "document.getElementById('cbo_lot_2').value = '".$lot."';\n";
		}
		if($type_id==3)
		{
			$cbo_yarn_count_3=create_drop_down( "cbo_yarn_count_3", 100, $yarn_countArr,"", 0, "-- select --" ,"","");
			echo "document.getElementById('yarn_count_td3').innerHTML = '".$cbo_yarn_count_3."';\n";
			echo "document.getElementById('cbo_yarn_brand_3').value = '".$brand_arr[$cbo_yarn_brand_res]."';\n";
			echo "document.getElementById('cbo_lot_3').value = '".$lot."';\n";
		}
		if($type_id==4)
		{
			$cbo_yarn_count_4=create_drop_down( "cbo_yarn_count_4", 100, $yarn_countArr,"", 0, "-- select --" ,"","");
			echo "document.getElementById('yarn_count_td4').innerHTML = '".$cbo_yarn_count_4."';\n";
			echo "document.getElementById('cbo_yarn_brand_4').value = '".$brand_arr[$cbo_yarn_brand_res]."';\n";
		    echo "document.getElementById('cbo_lot_4').value = '".$lot."';\n";
		}
	 
	exit();
}


if ($action=="save_update_delete_yarn")
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
 		$id_mst=return_next_id( "id", "sample_archive_yarn_info", 1 ) ;
 		
 

 		$field_array="id, req_id,company_id,booking_no,booking_id,fabric_color_id,color_type_id,deter_id,fabric_desc,compositiom_1,compositiom_2,compositiom_3, compositiom_4,count_1,count_2,count_3,count_4,brand_1,brand_2,brand_3,brand_4,lot_1,lot_2,lot_3,lot_4,ratio_1,ratio_2,ratio_3,ratio_4,actual_count_1,actual_count_2,actual_count_3,actual_count_4,inserted_by, insert_date, status_active, is_deleted";
		$data_array="(".$id_mst.",".$req_id.",".$company_id.",".$txt_booking_no.",".$txt_booking_id.",".$cbo_yarn_fab_color_code.",".$txt_yarn_color_type.",".$cbo_yarn_fabrication.",'".$fabric_desc."',".$cbo_yarn_composition_1.",".$cbo_yarn_composition_2.",".$cbo_yarn_composition_3.",".$cbo_yarn_composition_4.",".$cbo_yarn_count_1.",".$cbo_yarn_count_2.",".$cbo_yarn_count_3.",".$cbo_yarn_count_4.",".$cbo_yarn_brand_1.",".$cbo_yarn_brand_2.",".$cbo_yarn_brand_3.",".$cbo_yarn_brand_4.",".$cbo_lot_1.",".$cbo_lot_2.",".$cbo_lot_3.",".$cbo_lot_4.",".$txt_ratio_1.",".$txt_ratio_2.",".$txt_ratio_3.",".$txt_ratio_4.",".$txt_act_count_1.",".$txt_act_count_2.",".$txt_act_count_3.",".$txt_act_count_4.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		$rID=sql_insert("sample_archive_yarn_info",$field_array,$data_array,1);
		 // echo "10**insert into sample_archive_yarn_info ($field_array) values $data_array";die;
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
  		$field_array="fabric_color_id*color_type_id*deter_id*fabric_desc*compositiom_1*compositiom_2*compositiom_3* compositiom_4*count_1*count_2*count_3*count_4*brand_1*brand_2*brand_3*brand_4*lot_1*lot_2*lot_3*lot_4*ratio_1*ratio_2*ratio_3*ratio_4*actual_count_1*actual_count_2*actual_count_3*actual_count_4*updated_by*update_date";
		$data_array="".$cbo_yarn_fab_color_code."*".$txt_yarn_color_type."*".$cbo_yarn_fabrication."*'".$fabric_desc."'*".$cbo_yarn_composition_1."*".$cbo_yarn_composition_2."*".$cbo_yarn_composition_3."*".$cbo_yarn_composition_4."*".$cbo_yarn_count_1."*".$cbo_yarn_count_2."*".$cbo_yarn_count_3."*".$cbo_yarn_count_4."*".$cbo_yarn_brand_1."*".$cbo_yarn_brand_2."*".$cbo_yarn_brand_3."*".$cbo_yarn_brand_4."*".$cbo_lot_1."*".$cbo_lot_2."*".$cbo_lot_3."*".$cbo_lot_4."*".$txt_ratio_1."*".$txt_ratio_2."*".$txt_ratio_3."*".$txt_ratio_4."*".$txt_act_count_1."*".$txt_act_count_2."*".$txt_act_count_3."*".$txt_act_count_4."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
 		$rID=sql_update("sample_archive_yarn_info",$field_array,$data_array,"id","".$yarn_update_id."",1);
		 //echo "10**=".$rID.'=';die;
 		if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con);
 				echo "1**".str_replace("'","",$yarn_update_id)."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$yarn_update_id);
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
		$rID1=sql_delete("sample_archive_yarn_info",$field_array,$data_array,"id","".$yarn_update_id."",0);
		 
		if($db_type==0)
		{
			if($rID1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$yarn_update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$yarn_update_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID1)
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$yarn_update_id)."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
			}
			else
			{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$update_id);
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
if ($action=="listview_yarn_info")
{
	//$attach_id=return_library_array( "select id,attachment_name from lib_attachment where  STATUS_ACTIVE=1 and IS_DELETED=0",'id','attachment_name');
	//$operation_arr=return_library_array( "select id,operation_name from lib_sewing_operation_entry", "id","operation_name"  );
	//$data=explode("_",$data);
	$booking_id=$data;
	
	$sql_result =sql_select("SELECT a.id,a.req_id,a.company_id,a.booking_no,a.booking_id,a.fabric_color_id,a.color_type_id,a.fabric_desc,a.deter_id,a.count_1,a.count_2,a.count_3,a.count_4,a.lot_1,a.lot_2,a.lot_3,a.lot_4 from sample_archive_yarn_info a where a.booking_id=$booking_id and a.is_deleted=0  and a.status_active=1  order by a.id asc");
	
	 
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
				
	?>
    <table width="630" cellspacing="0" border="1" rules="all" class="rpt_table" >
        <thead>
			
            <th width="100">Fab. Color/Code</th>
            <th width="200">Fabrication</th>
            <th width="100">Color Type</th>
            <th width="100">Count</th>
            <th width="">Lot No</th>
          
        </thead>
    </table>
    <div style="width:630px; overflow-y:scroll; max-height:180px;">
        <table width="630" cellspacing="0" border="1" rules="all" class="rpt_table" id="tbl_details">
			<?
				$i=1;
               	foreach ($sql_result as $row)
               	{
				   if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				   if($row[csf('count_1')]) $count_str=$count_arr[$row[csf('count_1')]].',';else $count_str='';
				   if($row[csf('count_2')]) $count_str.=$count_arr[$row[csf('count_2')]].',';else $count_str='';
				   if($row[csf('count_3')]) $count_str.=$count_arr[$row[csf('count_3')]].',';else $count_str='';
				   if($row[csf('count_4')]) $count_str.=$count_arr[$row[csf('count_4')]].',';else $count_str='';
				   $count_str_all_data=rtrim($count_str,',');
				   $count_str_all_val=array_unique(explode(",", $count_str_all_data));
					
				   if($row[csf('lot_1')]) $lot_str=$row[csf('lot_1')].',';else $lot_str='';
				   if($row[csf('lot_2')]) $lot_str.=$row[csf('lot_2')].',';else $lot_str='';
				   if($row[csf('lot_3')]) $lot_str.=$row[csf('lot_3')].',';else $lot_str='';
				   if($row[csf('lot_4')]) $lot_str.=$row[csf('lot_4')].',';else $lot_str='';
				   $lot_strall_data=rtrim($lot_str,',');
				   $lot_str_all=array_unique(explode(", ", $lot_strall_data));
					
				?>
            		<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="get_php_form_data('<? echo $row[csf('id')]; ?>', 'populate_details_yarn_form_data', 'requires/yarn_info_entry_controller');"> 
						
                		<td width="100"><? echo $color_arr[$row[csf('fabric_color_id')]]; ?></td>
                         <td width="200" style="word-break:break-all"><p><? echo $row[csf('fabric_desc')]; ?></p></td>
                         <td width="100"style="word-break:break-all"><? echo $row[csf('color_type_id')]; ?>&nbsp;</td>
                        <td align="center" width="100" style="word-break:break-all"><p><? $count_res=implode(", ",$count_str_all_val); echo $count_str_all_data;//$count_arr[$count_res]; ?></p></td>
                        <td align="center" style="word-break:break-all"><p><? echo implode(", ",$lot_str_all); ?></p></td>
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
if($action=="populate_details_yarn_form_data")
{  
 	
	$sql_result =sql_select("SELECT a.id,a.req_id,a.company_id,a.booking_no,a.booking_id,a.fabric_color_id,a.color_type_id,a.fabric_desc,a.deter_id,a.compositiom_1,a.compositiom_2,a.compositiom_3, a.compositiom_4,a.count_1,a.count_2,a.count_3,a.count_4,a.brand_1,a.brand_2,a.brand_3,a.brand_4,a.lot_1,a.lot_2,a.lot_3,a.lot_4,a.ratio_1,a.ratio_2,a.ratio_3,a.ratio_4,a.actual_count_1,a.actual_count_2,a.actual_count_3,a.actual_count_4 from sample_archive_yarn_info a where a.id=$data and a.is_deleted=0  and a.status_active=1  order by a.id asc");
	
	
	foreach($sql_result as $row)
	{ 
 		//echo "load_drop_down( 'requires/sample_checklist_controller', '".$result[csf('id')]."', 'load_drop_down_gmts', 'gmts_td' );\n";
 		echo "$('#yarn_update_id').val('".$row[csf('id')]."');\n";
		echo "$('#cbo_yarn_fab_color_code').val('".$row[csf('fabric_color_id')]."');\n";
		echo "$('#cbo_yarn_fabrication').val('".$row[csf('deter_id')]."');\n";
		echo "$('#txt_yarn_color_type').val('".$row[csf('color_type_id')]."');\n";
		$deter_id=$row[csf('deter_id')]; 
		
   	}
	
	 
	echo "fnc_compostion_load(".$deter_id.");\n";
	echo "$('#txt_yarn_color_type').val('".$row[csf('color_type_id')]."');\n"; 
	echo "$('#cbo_yarn_composition_1').val('".$row[csf('compositiom_1')]."');\n";
	echo "$('#cbo_yarn_composition_2').val('".$row[csf('compositiom_2')]."');\n";
	echo "$('#cbo_yarn_composition_3').val('".$row[csf('compositiom_3')]."');\n";
	echo "$('#cbo_yarn_composition_4').val('".$row[csf('compositiom_4')]."');\n";
	
	echo "fnc_count_load(".$row[csf('compositiom_1')].",1);\n";
	echo "fnc_count_load(".$row[csf('compositiom_2')].",2);\n";
	echo "fnc_count_load(".$row[csf('compositiom_3')].",3);\n";
	echo "fnc_count_load(".$row[csf('compositiom_4')].",4);\n";
	
	echo "$('#cbo_yarn_count_1').val('".$row[csf('count_1')]."');\n";
	echo "$('#cbo_yarn_count_2').val('".$row[csf('count_2')]."');\n";
	echo "$('#cbo_yarn_count_3').val('".$row[csf('count_3')]."');\n";
	echo "$('#cbo_yarn_count_4').val('".$row[csf('count_4')]."');\n";

	echo "$('#txt_ratio_1').val('".$row[csf('ratio_1')]."');\n";
	echo "$('#txt_ratio_2').val('".$row[csf('ratio_2')]."');\n";
	echo "$('#txt_ratio_3').val('".$row[csf('ratio_3')]."');\n";
	echo "$('#txt_ratio_4').val('".$row[csf('ratio_4')]."');\n";

	echo "$('#cbo_lot_1').val('".$row[csf('lot_1')]."');\n";
	echo "$('#cbo_lot_2').val('".$row[csf('lot_2')]."');\n";
	echo "$('#cbo_lot_3').val('".$row[csf('lot_3')]."');\n";
	echo "$('#cbo_lot_4').val('".$row[csf('lot_4')]."');\n";
	
	echo "$('#cbo_yarn_brand_1').val('".$row[csf('brand_1')]."');\n";
	echo "$('#cbo_yarn_brand_2').val('".$row[csf('brand_2')]."');\n";
	echo "$('#cbo_yarn_brand_3').val('".$row[csf('brand_3')]."');\n";
	echo "$('#cbo_yarn_brand_4').val('".$row[csf('brand_4')]."');\n";
	
	echo "$('#txt_act_count_1').val('".$row[csf('actual_count_1')]."');\n";
	echo "$('#txt_act_count_2').val('".$row[csf('actual_count_2')]."');\n";
	echo "$('#txt_act_count_3').val('".$row[csf('actual_count_3')]."');\n";
	echo "$('#txt_act_count_4').val('".$row[csf('actual_count_4')]."');\n";
	if(count($sql_result)>0)
	{
		//echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_yarn_entry',1);\n";  
		//echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_yarn_entry',1);\n"; 
			echo "$('#save2').removeClass('formbutton').addClass('formbutton_disabled');\n"; //formbutton 
		//echo "$('#update2').removeClass('formbutton_disabled').addClass('formbutton');\n"; //formbutton
		 	echo "$('#update2').removeClass('formbutton_disabled').addClass('formbutton');\n"; //formbutton 
		    echo "$('#update2').removeAttr('onclick').attr('onclick','fnc_yarn_entry(1);')\n";
		    echo "$('#Delete2').removeClass('formbutton_disabled').addClass('formbutton');\n"; //formbutton 
		    echo "$('#Delete2').removeAttr('onclick').attr('onclick','fnc_yarn_entry(2);')\n"; 
		//echo "fnc_yarn_button_status(2);\n"; 
		
	}
	else
	{
		echo "fnc_yarn_button_status(1);\n"; 
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_yarn_entry',1);\n"; 
	}
	 	 
	
		
   	unlink($sql_result);
 	exit();	
}	



?>
<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="populate_data_from_breakdown")
{
	$sql= "SELECT id, buyer_id, style_ref, gmts_item_id, working_hour, system_no FROM ppl_gsd_entry_mst where id=$data";
	$data_array=sql_select($sql);
 	foreach ($data_array as $row)
	{
		echo "document.getElementById('txt_style_ref_tc').value			= '".$row[csf("style_ref")]."';\n";
		echo "document.getElementById('cbo_buyer_tc').value 			= '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('system_no_tc').value 			= '".$row[csf("system_no")]."';\n";
		echo "document.getElementById('breakdown_id3').value 			= '".$row[csf("id")]."';\n";

		$balanceData=sql_select("select id, body_size, thread_cons_date, input_uom, total_req, meter_per_gmts from ppl_balancing_mst_entry where gsd_mst_id='".$row[csf("id")]."' and balancing_page=4 and status_active=1 and is_deleted=0");
		
		echo "document.getElementById('txt_body_size').value			= '".$balanceData[0][csf("body_size")]."';\n";
		echo "document.getElementById('txt_cons_date').value 			= '".change_date_format($balanceData[0][csf("thread_cons_date")])."';\n";
		echo "document.getElementById('cbo_uom').value 					= '".$balanceData[0][csf("input_uom")]."';\n";
		echo "document.getElementById('txt_tot_required').value 		= '".number_format($balanceData[0][csf("total_req")],2,'.','')."';\n";
		echo "document.getElementById('txt_required_into_meter').value 	= '".number_format($balanceData[0][csf("meter_per_gmts")],2,'.','')."';\n";
		echo "document.getElementById('bl3_update_id').value 			= '".$balanceData[0][csf("id")]."';\n";
		
		exit();
	}
}

if ($action=="show_operation_list")
{
	$operation_arr=return_library_array( "select id,operation_name from lib_sewing_operation_entry", "id","operation_name"  );
	
	$not_machine_array=array(40,41,43,44,48,53,54,55,56,68,69,70,90);
	
	$sql="SELECT a.PROCESS_ID,b.id, b.row_sequence_no, b.lib_sewing_id, b.resource_gsd from PPL_GSD_ENTRY_MST a,ppl_gsd_entry_dtls b where a.id=b.mst_id and b.mst_id=$data and b.is_deleted=0 order by b.row_sequence_no asc";
	//echo $sql;die;
	$result=sql_select($sql);

	$production_resource_arr=return_library_array( "select RESOURCE_ID,RESOURCE_NAME from LIB_OPERATION_RESOURCE where is_deleted=0  and status_active=1 and PROCESS_ID = {$result[0]['PROCESS_ID']} order by RESOURCE_NAME", "RESOURCE_ID","RESOURCE_NAME"  );

	$arr=array(0=>$operation_arr,1=>$production_resource_arr);
	?>
    <table width="410" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table">
        <thead>
            <tr>
                <th width="50">SL</th>
                <th width="220">Operation Name</th>
                <th>Resource</th>
            </tr>
        </thead>
   </table>
    <div style="max-height:300px; width:408px; overflow-y:scroll">
        <table cellpadding="0" cellspacing="0" border="1" rules="all" width="388" class="rpt_table" id="list_view_tc">
            <?
			$i=1;
			foreach($result as $row)	
			{
				if($i%2==0) $bgcolor="#FFFFFF"; else $bgcolor="#E9F3FF";
				
				$is_machine=1;
				if(in_array($row[csf('resource_gsd')],$not_machine_array))
				{
					$is_machine=0;
				}
				
				$data=$row[csf('id')]."_".$row[csf('lib_sewing_id')]."_".$i;
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="trTc_<? echo $i; ?>" style="cursor:pointer" onclick="js_set_value_tc('<? echo $data; ?>');change_color_tr('<? echo $i; ?>','<? echo $bgcolor; ?>')">
					<td width="50"><? echo $i; ?><input type="hidden" name="is_machine[]" id="is_machine_<? echo $i; ?>" value="<? echo $is_machine; ?>"/></td>
					<td width="220"><p><? echo $operation_arr[$row[csf('lib_sewing_id')]]; ?></p></td>
					<td><p><? echo $production_resource_arr[$row[csf('resource_gsd')]]; ?></p></td>
				</tr>
			<?	
				$i++;
			}
			?>
        </table>
    </div>
    <?
	//echo create_list_view("list_view_tc", "Operation Name,Resource", "220","400","300",0, $sql, "js_set_value_tc", "id,lib_sewing_id", "", 1, "lib_sewing_id,resource_gsd", $arr, "lib_sewing_id,resource_gsd","","",'0,0') ;

	exit();
}

if($action=="populate_data_from_operation")
{
	$sql= "SELECT a.id, a.operation_name, a.resource_sewing, a.fabric_type, a.seam_length, b.resource_gsd FROM lib_sewing_operation_entry a, ppl_gsd_entry_dtls b where a.id=b.lib_sewing_id and b.id=$data";
	// echo $sql;die;
	$data_array=sql_select($sql);
 	foreach ($data_array as $row)
	{ 
		$sql = "SELECT id, resource_id, consumption_factor, needle_thread, bobbin_thread FROM LIB_OPERATION_RESOURCE WHERE PROCESS_ID=8 and resource_id=".$row[csf('resource_sewing')];
		$operation_resource=sql_select($sql);
		$operation_resource[0]['CONSUMPTION_FACTOR'];

		$comp='';
		if($row[csf('fabric_type')]>0)
		{
			$determination_sql=sql_select("select a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=".$row[csf('fabric_type')]);
			if($determination_sql[0][csf('construction')]!="")
			{
				$comp=$determination_sql[0][csf('construction')].", ";
			}
			
			foreach( $determination_sql as $d_row )
			{
				$comp.=$composition[$d_row[csf('copmposition_id')]]." ".$d_row[csf('percent')]."% ";
			}
		}
		 

		echo "document.getElementById('txt_operation_name').value			= '".$row[csf("operation_name")]."';\n";
		echo "document.getElementById('operation_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_seam_length').value 				= '".$row[csf("seam_length")]."';\n";
		echo "document.getElementById('cbo_resource_tc').value				= '".$row[csf("resource_gsd")]."';\n";
		echo "document.getElementById('txt_fabric_description').value 		= '".$comp."';\n";

		echo "document.getElementById('txt_consumption_factor').value 				= '".$operation_resource[0]["CONSUMPTION_FACTOR"]."';\n";
		echo "document.getElementById('txt_needle_thread').value 				= '".$operation_resource[0]["NEEDLE_THREAD"]."';\n";
		echo "document.getElementById('txt_bobbin_thread').value 				= '".$operation_resource[0]["BOBBIN_THREAD"]."';\n";
		
		echo "calculate_thread_all();\n";
		
		exit();
	}
}

if($action=="details_list_view")
{
	$operation_arr=return_library_array( "select id,operation_name from lib_sewing_operation_entry", "id","operation_name"  );
	$arr=array(0=>$operation_arr);
	
	
	$sql="select id, operation_id, seam_length, req_qty, gsd_dtls_id from ppl_thread_cons_dtls_entry where mst_id=$data and status_active=1 and is_deleted=0 order by id";
//	echo $sql;die;

	echo create_list_view("list_view_details", "Operation Name, Seam Length, Required", "400,170,100","850","300",0, $sql, "get_php_form_data", "id,operation_id,gsd_dtls_id,seam_length", "'load_php_data_from_tc'", 1, "operation_id,0,0", $arr, "operation_id,seam_length,req_qty","requires/thread_consumption_controller","",'0,2,2') ;

	exit();
}

if($action=="load_php_data_from_tc")
{
	$data=explode("_",$data);
	$dtls_id=$data[0];
	$operation_id=$data[1];
	$gsd_dtls_id=$data[2];
	$seam_length=$data[3];
	
	//$sql= "SELECT id, operation_name, resource_sewing, fabric_type, seam_length FROM lib_sewing_operation_entry where id=$operation_id";
	$sql= "SELECT a.id, a.operation_name, a.resource_sewing, a.fabric_type, a.seam_length, b.resource_gsd FROM lib_sewing_operation_entry a, ppl_gsd_entry_dtls b where a.id=b.lib_sewing_id and b.id=$gsd_dtls_id and a.id=$operation_id and b.is_deleted=0";
	$data_array=sql_select($sql);
 	foreach ($data_array as $row)
	{
		$comp='';
		if($row[csf('fabric_type')]>0)
		{
			$determination_sql=sql_select("select a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=".$row[csf('fabric_type')]);
			if($determination_sql[0][csf('construction')]!="")
			{
				$comp=$determination_sql[0][csf('construction')].", ";
			}
			
			foreach( $determination_sql as $d_row )
			{
				$comp.=$composition[$d_row[csf('copmposition_id')]]." ".$d_row[csf('percent')]."% ";
			}
		}
		
		if($seam_length<=0) { $seam_length=$row[csf("seam_length")];}
		
		echo "document.getElementById('txt_operation_name').value			= '".$row[csf("operation_name")]."';\n";
		echo "document.getElementById('operation_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_seam_length').value 				= '".$seam_length."';\n";
		echo "document.getElementById('cbo_resource_tc').value				= '".$row[csf("resource_gsd")]."';\n";
		echo "document.getElementById('txt_fabric_description').value 		= '".$comp."';\n";
		echo "document.getElementById('dtlsId_gsd').value 					= '".$gsd_dtls_id."';\n";
		echo "document.getElementById('update_dtlsId').value 				= '".$dtls_id."';\n";
	}

	//echo $dtls_id;die;

	$theardFormArr = array(1=>'Needle Thread', 2=>'Bobbin Thread'); 
	$sql= "SELECT id, thread_type, thread_desc, thread_length, allowance, req_thread, theard_form, frequency FROM ppl_thread_cons_op_dtls_entry where dtls_id=$dtls_id and status_active=1 and is_deleted=0";
	//echo $sql;die;

	$data_array=sql_select($sql);
	$i=0; $table='';
 	foreach ($data_array as $row)
	{
		$i++;
		if ($i%2==0) $bgcolor="#FFFFFF"; else $bgcolor="#E9F3FF";
		 
		$table=$table.'<tr align="center" id="tr_'.$i.'" bgcolor="'.$bgcolor.'"><td align="left">'.$i.'</td><td>'.create_drop_down( "cboThreadType_$i", 100,$size_color_sensitive,"",1, "-- Select --", $row[csf('thread_type')], '', 0, "1,3", "", "", "", "", "", "cboThreadType[]").'</td><td><input type="text" name="txtThreadDesc[]" id="txtThreadDesc_'.$i.'" placeholder="Write" class="text_boxes" style="width:130px" value="'.$row[csf('thread_desc')].'" onFocus="add_auto_complete('.$i.')" /></td>      <td>'.create_drop_down( "cboTheardForm_$i", 100, $theardFormArr,"",1, "-- Select --", $row[csf('theard_form')], '', 0, "", "", "", "", "", "", "cboTheardForm[]").'</td><td><input type="text" name="txtFrequency[]" id="txtFrequency_'.$i.'" placeholder="Write" class="text_boxes_numeric" style="width:90px" value="'.$row[csf('frequency')].'" onFocus="add_auto_complete('.$i.')" /></td>        <td><input type="text" name="txtThreadLength[]" id="txtThreadLength_'.$i.'" placeholder="Write" class="text_boxes_numeric" style="width:90px" onKeyUp="calculate_thread('.$i.')" value="'.$row[csf('thread_length')].'" /></td><td><input type="text" name="txtAllowance[]" id="txtAllowance_'.$i.'" placeholder="Write" class="text_boxes_numeric" style="width:90px" onKeyUp="calculate_thread('.$i.')" value="'.$row[csf('allowance')].'" /></td><td><input type="text" name="txtRequired[]" id="txtRequired_'.$i.'" class="text_boxes_numeric" style="width:90px" placeholder="Calculative" readonly="readonly" value="'.$row[csf('req_thread')].'" /></td><td><input type="button" id="increaseT_'.$i.'" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr('.$i.')"/><input type="button" id="decreaseT_'.$i.'" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow('.$i.');" /></td></tr>';
	}
	
	if($i<4)
	{
		for($i=$i+1;$i<=2;$i++)
		{
			if ($i%2==0) $bgcolor="#FFFFFF"; else $bgcolor="#E9F3FF";
			 
			$table=$table.'<tr align="center" id="tr_'.$i.'" bgcolor="'.$bgcolor.'"><td align="left">'.$i.'</td><td>'.create_drop_down( "cboThreadType_$i", 100,$size_color_sensitive,"",1, "-- Select --", 0, '', 0, "1,3", "", "", "", "", "", "cboThreadType[]").'</td><td><input type="text" name="txtThreadDesc[]" id="txtThreadDesc_'.$i.'" placeholder="Write" class="text_boxes" style="width:130px" value="" onFocus="add_auto_complete('.$i.')" /></td>     <td>'.create_drop_down( "cboTheardForm_$i", 100,$theardFormArr,"",1, "-- Select --", 0, '', 0, "", "", "", "", "", "", "cboTheardForm[]").'</td><td><input type="text" name="txtFrequency[]" id="txtFrequency_'.$i.'" placeholder="Write" class="text_boxes_numeric" style="width:90px" onFocus="add_auto_complete('.$i.')" /></td>       <td><input type="text" name="txtThreadLength[]" id="txtThreadLength_'.$i.'" placeholder="Write" class="text_boxes_numeric" style="width:90px" onKeyUp="calculate_thread('.$i.')" value="" /></td><td><input type="text" name="txtAllowance[]" id="txtAllowance_'.$i.'" placeholder="Write" class="text_boxes_numeric" style="width:90px" onKeyUp="calculate_thread('.$i.')" value="" /></td><td><input type="text" name="txtRequired[]" id="txtRequired_'.$i.'" class="text_boxes_numeric" style="width:90px" placeholder="Calculative" readonly="readonly" value="" /></td><td><input type="button" id="increaseT_'.$i.'" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr('.$i.')"/><input type="button" id="decreaseT_'.$i.'" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow('.$i.');" /></td></tr>';
		}
	}
	
	echo "$('#operation_details_thread').html('".$table."')".";\n";
	echo "calculate_total_req();\n";
	echo "set_button_status(1, '".$permission."', 'fnc_thread_consumption_entry',4);\n"; 
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$approved=0;
	$sql=sql_select("select approved from ppl_gsd_entry_mst where id=$breakdown_id3");
	foreach($sql as $row){
		$approved=$row[csf('approved')];
	}
	if($approved==3) $approved=1; else $approved=$approved;
	
	if($approved==1){
		echo "approved**".str_replace("'","",$breakdown_id3);
		die;
	}
	if ($operation==0)  // Insert Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$prev_req=0;
		if(str_replace("'", '', $bl3_update_id)=="")
		{
			$id=return_next_id( "id", "ppl_balancing_mst_entry", 1 ) ;
			$field_array="id,gsd_mst_id,body_size,thread_cons_date,input_uom,total_req,meter_per_gmts,balancing_page,inserted_by,insert_date,entry_form";
		}
		else
		{
			$id=str_replace("'", '', $bl3_update_id) ;
			$field_array="body_size*thread_cons_date*input_uom*total_req*meter_per_gmts*updated_by*update_date";
			
			$prev_data=sql_select("select input_uom from ppl_balancing_mst_entry where id=$bl3_update_id"); 
			if(str_replace("'","",$cbo_uom)!=$prev_data[0][csf('input_uom')])
			{
				echo "40**Multiple Input UOM Not Allow In Same Breakdown ID";disconnect($con);die;
			}
	
			$prev_req=return_field_value("sum(req_qty) as req_qty","ppl_thread_cons_dtls_entry","mst_id=$bl3_update_id and status_active=1 and is_deleted=0","req_qty");
			
			// validation off by resal issue no: 2015;
			/*if(is_duplicate_field("operation_id","ppl_thread_cons_dtls_entry","mst_id=$bl3_update_id and operation_id=$operation_id and status_active=1 and is_deleted=0")==1)
			{
				echo "11**0"; 
				die;			
			}*/
		}
		
		$dtls_id = return_next_id( "id", "ppl_thread_cons_dtls_entry", 1 );
		$field_array_dtls="id, gsd_mst_id, gsd_dtls_id, mst_id, operation_id, seam_length, req_qty, inserted_by, insert_date, entry_form";
		
		$dtls_id_tc = return_next_id( "id", "ppl_thread_cons_op_dtls_entry", 1 );
		$field_array_dtls_tc="id, gsd_mst_id, mst_id, dtls_id, thread_type, thread_desc, theard_form, frequency, thread_length, allowance, req_thread, entry_form";
		
		$totReq=0; $op_req_qty=0;
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$cboThreadType="cboThreadType".$j;
			$txtThreadDesc="txtThreadDesc".$j;

			$cboTheardForm="cboTheardForm".$j;
			$txtFrequency="txtFrequency".$j;

			$txtThreadLength="txtThreadLength".$j;
			$txtAllowance="txtAllowance".$j;
			$txtRequired="txtRequired".$j;
			
			if($data_array_dtls_tc!="") $data_array_dtls_tc.=",";
			$data_array_dtls_tc.="(".$dtls_id_tc.",".$breakdown_id3.",".$id.",".$dtls_id.",'".$$cboThreadType."','".$$txtThreadDesc."','".$$cboTheardForm."','".$$txtFrequency."','".$$txtThreadLength."','".$$txtAllowance."','".$$txtRequired."', 1)";
			
			$op_req_qty+=$$txtRequired;
			$dtls_id_tc = $dtls_id_tc+1;
		}
		
		$totReq=$op_req_qty+$prev_req;
		$req_per_gmts_into_meter=0;
		if(str_replace("'", '', $cbo_uom)==25)
		{
			$req_per_gmts_into_meter=$totReq/100;
		}
		else
		{
			$req_per_gmts_into_meter=$totReq/39.37;
		}
		
		if(str_replace("'", '', $bl3_update_id)=="")
		{
			$data_array="(".$id.",".$breakdown_id3.",".$txt_body_size.",".$txt_cons_date.",".str_replace("'","",$cbo_uom).",".$totReq.",".$req_per_gmts_into_meter.",4,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."', 1)";
		}
		else
		{
			$data_array=$txt_body_size."*".$txt_cons_date."*".str_replace("'","",$cbo_uom)."*".$totReq."*".$req_per_gmts_into_meter."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		}
		
		$data_array_dtls="(".$dtls_id.",".$breakdown_id3.",".$dtlsId_gsd.",".$id.",".$operation_id.",".$txt_seam_length.",'".$op_req_qty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
		
		if(str_replace("'", '', $bl3_update_id)=="")
		{
			//echo "10**insert into ppl_thread_cons_op_dtls_entry (".$field_array_dtls_tc.") values ".$data_array_dtls_tc;die;
			$rID = sql_insert("ppl_balancing_mst_entry", $field_array, $data_array, 0);
		}
		else
		{
			$rID = sql_update("ppl_balancing_mst_entry", $field_array, $data_array, "id", $bl3_update_id, 0);
		}
		//echo "10**insert into ppl_thread_cons_dtls_entry (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID2=sql_insert("ppl_thread_cons_dtls_entry", $field_array_dtls, $data_array_dtls, 1);
		$rID3=sql_insert("ppl_thread_cons_op_dtls_entry", $field_array_dtls_tc, $data_array_dtls_tc, 1);
		//echo "10**".$rID."&&".$rID2."&&".$rID3;die;
		
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3)
			{
				mysql_query("COMMIT");  
				echo "0**".$id."**".number_format($totReq,2,'.','')."**".number_format($req_per_gmts_into_meter,2,'.','');
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3)
			{
				oci_commit($con);  
				echo "0**".$id."**".number_format($totReq,2,'.','')."**".number_format($req_per_gmts_into_meter,2,'.','');
			}
			else
			{
				oci_rollback($con);
				echo "5**0**0";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$prev_data=sql_select("select a.id, a.input_uom, sum(b.req_qty) as req_qty, count(b.id) as dtls_row from ppl_balancing_mst_entry a, ppl_thread_cons_dtls_entry b where a.id=b.mst_id and a.id=$bl3_update_id and b.id!=$update_dtlsId and b.status_active=1 and b.is_deleted=0 group by a.id, a.input_uom"); 
		if($prev_data[0][csf('dtls_row')]>0)
		{
			if(str_replace("'","",$cbo_uom)!=$prev_data[0][csf('input_uom')])
			{
				echo "40**Multiple Input UOM Not Allow In Same Breakdown ID";disconnect($con);die;
			}
		}

		$prev_req=$prev_data[0][csf('req_qty')];
		
		if(is_duplicate_field("operation_id","ppl_thread_cons_dtls_entry","mst_id=$bl3_update_id and operation_id=$operation_id and b.id!=$update_dtlsId and status_active=1 and is_deleted=0")==1)
		{
			echo "11**0"; 
			disconnect($con);
			die;			
		}
		
		$dtls_id_tc = return_next_id( "id", "ppl_thread_cons_op_dtls_entry", 1 );
		$field_array_dtls_tc="id, gsd_mst_id, mst_id, dtls_id, thread_type, thread_desc, theard_form, frequency, thread_length, allowance, req_thread";
		
		$op_req_qty=0;
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$cboThreadType="cboThreadType".$j;
			$txtThreadDesc="txtThreadDesc".$j;

			$cboTheardForm="cboTheardForm".$j;
			$txtFrequency="txtFrequency".$j;

			$txtThreadLength="txtThreadLength".$j;
			$txtAllowance="txtAllowance".$j;
			$txtRequired="txtRequired".$j;
			
			if($data_array_dtls_tc!="") $data_array_dtls_tc.=",";

			$data_array_dtls_tc.="(".$dtls_id_tc.",".$breakdown_id3.",".$bl3_update_id.",".$update_dtlsId.",'".$$cboThreadType."','".$$txtThreadDesc."','".$$cboTheardForm."','".$$txtFrequency."','".$$txtThreadLength."','".$$txtAllowance."','".$$txtRequired."')";

 
			
			$op_req_qty+=$$txtRequired;
			$dtls_id_tc = $dtls_id_tc+1;
		}
		
		$totReq=$op_req_qty+$prev_req;
		$req_per_gmts_into_meter=0;
		
		if(str_replace("'", '', $cbo_uom)==25)
		{
			$req_per_gmts_into_meter=$totReq/100;
		}
		else
		{
			$req_per_gmts_into_meter=$totReq/39.37;
		}
		
		//echo $totReq."==".$req_per_gmts_into_meter;die;
		$field_array="body_size*thread_cons_date*input_uom*total_req*meter_per_gmts*updated_by*update_date";
		$data_array=$txt_body_size."*".$txt_cons_date."*".$cbo_uom."*".$totReq."*".$req_per_gmts_into_meter."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$field_array_dtls="gsd_dtls_id*operation_id*seam_length*req_qty*updated_by*update_date";
		$data_array_dtls=$dtlsId_gsd."*".$operation_id."*".$txt_seam_length."*'".$op_req_qty."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		//echo "10**".$field_array_dtls."==".$data_array_dtls;die;
		//echo "10**insert into ppl_thread_cons_op_dtls_entry (".$field_array_dtls_tc.") values ".$data_array_dtls_tc;die;
		$rID=sql_update("ppl_balancing_mst_entry",$field_array,$data_array,"id",$bl3_update_id,0);
		$rID2=sql_update("ppl_thread_cons_dtls_entry",$field_array_dtls,$data_array_dtls,"id",$update_dtlsId,0);
		$rID3=execute_query("delete from ppl_thread_cons_op_dtls_entry where dtls_id=$update_dtlsId",0);
		$rID4=sql_insert("ppl_thread_cons_op_dtls_entry",$field_array_dtls_tc,$data_array_dtls_tc,1);
		//echo "10**".$rID."&&".$rID2."&&".$rID3."&&".$rID4;die;

		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $bl3_update_id)."**".number_format($totReq,2,'.','')."**".number_format($req_per_gmts_into_meter,2,'.','');
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**".str_replace("'", '', $bl3_update_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $rID4)
			{
				oci_commit($con);  
				echo "1**".str_replace("'", '', $bl3_update_id)."**".number_format($totReq,2,'.','')."**".number_format($req_per_gmts_into_meter,2,'.','');
			}
			else
			{
				oci_rollback($con);
				echo "6**".str_replace("'", '', $bl3_update_id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$prev_data=sql_select("select a.input_uom, sum(b.req_qty) as req_qty from ppl_balancing_mst_entry a, ppl_thread_cons_dtls_entry b where a.id=b.mst_id and a.id=$bl3_update_id and b.id!=$update_dtlsId and b.status_active=1 and b.is_deleted=0 group by a.input_uom"); 
		$totReq=$prev_data[0][csf('req_qty')];
		$input_uom=$prev_data[0][csf('input_uom')];

		$req_per_gmts_into_meter=0;
		if($input_uom==25)
		{
			$req_per_gmts_into_meter=$totReq/100;
		}
		else
		{
			$req_per_gmts_into_meter=$totReq/39.37;
		}
		
		//echo "10**".$totReq."==".$req_per_gmts_into_meter;die;
		$field_array="total_req*meter_per_gmts*updated_by*update_date";
		$data_array=$totReq."*".$req_per_gmts_into_meter."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$field_array_dtls="status_active*is_deleted*updated_by*update_date";
		$data_array_dtls="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		//echo "10**".$field_array_dtls."==".$data_array_dtls;die;
		//echo "10**insert into ppl_thread_cons_op_dtls_entry (".$field_array_dtls_tc.") values ".$data_array_dtls_tc;die;
		$rID=sql_update("ppl_balancing_mst_entry",$field_array,$data_array,"id",$bl3_update_id,0);
		$rID2=sql_update("ppl_thread_cons_dtls_entry",$field_array_dtls,$data_array_dtls,"id",$update_dtlsId,0);
		$rID3=execute_query("delete from ppl_thread_cons_op_dtls_entry where dtls_id=$update_dtlsId",0);
		
		//oci_rollback($con);
		//echo "10**".$rID."&&".$rID2."&&".$rID3;die;

		if($db_type==0)
		{
			if($rID && $rID2 && $rID3)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'", '', $bl3_update_id)."**".$totReq."**".number_format($req_per_gmts_into_meter,2,'.','');
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "7**".str_replace("'", '', $bl3_update_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3)
			{
				oci_commit($con);  
				echo "2**".str_replace("'", '', $bl3_update_id)."**".$totReq."**".number_format($req_per_gmts_into_meter,2,'.','');
			}
			else
			{
				oci_rollback($con);
				echo "7**".str_replace("'", '', $bl3_update_id);
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="print")
{
	$mstDataArray=sql_select("select b.PROCESS_ID,a.body_size, a.thread_cons_date, a.input_uom, a.total_req, a.meter_per_gmts, b.style_ref, b.buyer_id from ppl_balancing_mst_entry a, ppl_gsd_entry_mst b where a.gsd_mst_id=b.id and a.id='".$data."' and a.balancing_page=4 and a.status_active=1 and a.is_deleted=0");
	$buyer_name=return_field_value("buyer_name","lib_buyer","id='".$mstDataArray[0][csf('buyer_id')]."'");


	$production_resource_arr=return_library_array( "select RESOURCE_ID,RESOURCE_NAME from LIB_OPERATION_RESOURCE where is_deleted=0  and status_active=1 and process_id = {$mstDataArray[0]['PROCESS_ID']} order by RESOURCE_NAME", "RESOURCE_ID","RESOURCE_NAME"  );

	
	$sql= "SELECT dtls_id, thread_type, thread_desc, thread_length, allowance, req_thread FROM ppl_thread_cons_op_dtls_entry where mst_id=$data order by id";
	$data_array=sql_select($sql);
	
	$thread_type_arr=array(); $thread_desc_arr=array(); $desc_arr=array(); $i=0; $uom=$mstDataArray[0][csf('input_uom')];
 	foreach ($data_array as $row)
	{
		$i++;
		$thread_desc=ucfirst(strtolower($row[csf('thread_desc')]));
		$thread_type_arr[$row[csf('thread_type')]].=$thread_desc.",";
		$thread_desc_arr[$row[csf('dtls_id')]][$row[csf('thread_type')]][$thread_desc]['tl']=$row[csf('thread_length')];
		$thread_desc_arr[$row[csf('dtls_id')]][$row[csf('thread_type')]][$thread_desc]['aw']=$row[csf('allowance')];
		$thread_desc_arr[$row[csf('dtls_id')]][$row[csf('thread_type')]][$thread_desc]['rt']=$row[csf('req_thread')];
		//$uom=$row[csf('input_uom')];
		$desc_arr[$thread_desc]=$thread_desc;
	}
	
	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
		}
	}
	
	$header=''; $column=0;
	foreach($thread_type_arr as $key=>$value)
	{
		$noOfCol=count(array_unique(explode(",",chop($value,','))))*3;
		$width=$noOfCol*70;
		$header.='<th width="'.$width.'" colspan="'.$noOfCol.'">'.$size_color_sensitive[$key].'</th>';
		$column+=$noOfCol;
	}
	
	$tableWidth=($column*70)+590;
?>
    <div style="width:<? echo $tableWidth;?>px">
        <table width="890" border="0">
            <tr>
                <td align="center" colspan="9"><strong><u>Thread Consumption</u></strong></td>
            </tr>
            <tr>
                <td width="140"><strong>Body size</strong></td>
                <td width="10"><strong>:</strong></td>
                <td width="110"><? echo $mstDataArray[0][csf('body_size')]; ?></td>
                <td width="160"><strong>Date</strong></td>
                <td width="10"><strong>:</strong></td>
                <td width="110"><? echo change_date_format($mstDataArray[0][csf('thread_cons_date')]); ?></td>
                <td width="110"><strong>Input UOM</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><? echo $unit_of_measurement[$mstDataArray[0][csf('input_uom')]]; ?></td>
            </tr>
            <tr>
                <td><strong>Style Ref.</strong></td>
                <td width="10"><strong>:</strong></td>
                <td style="padding-right:5px"><? echo $mstDataArray[0][csf('style_ref')]; ?></td>
                <td><strong>Buyer Name</strong></td>
                <td width="10"><strong>:</strong></td>
                <td style="padding-right:5px"><? echo $buyer_name; ?></td>
            </tr>
        </table>
        <br />
        <table width="100%" cellspacing="0"  border="1" rules="all">
            <thead>
            	<tr bgcolor="#dddddd" align="center">
                	<th width="40" rowspan="2">SL</th>
                    <th width="130" rowspan="2">Operation</th>
                    <th width="90" rowspan="2">Resource</th>
                    <th width="140" rowspan="2">Fabrication</th>
                    <th width="60" rowspan="2">S. Lenth</th>
                    <? echo $header; ?>
                    <th rowspan="2">G. Total</th>
                </tr>
                <tr bgcolor="#dddddd" align="center">
                	<? 
						foreach($thread_type_arr as $key=>$value)
						{
							$desc_array=array_unique(explode(",",chop($value,',')));
							foreach($desc_array as $desc)
							{
								echo '<th width="70">'.$desc.'</th>';
								echo '<th width="70">Allowance</th>';
								echo '<th width="70">Total</th>';
							}
						}
					?>
                </tr>
            </thead>
            <tbody>
            <?
				$sqlDtls="select a.id, a.req_qty, b.operation_name, b.resource_sewing, b.fabric_type, a.seam_length from ppl_thread_cons_dtls_entry a, lib_sewing_operation_entry b where a.operation_id=b.id and a.mst_id='".$data."' and a.status_active=1 and a.is_deleted=0 order by a.id";
				$data_array_dtls=sql_select($sqlDtls);
				$i=1; $gTotal=0; $tot_arr=array(); $desc_summary_arr=array();
				foreach($data_array_dtls as $slectResult)
				{
				?>
					<tr>
                    	<td><? echo $i; ?></td>
						<td style="word-wrap:break-word"><? echo $slectResult[csf('operation_name')]; ?></td>
						<td style="word-wrap:break-word"><? echo $production_resource_arr[$slectResult[csf('resource_sewing')]]; ?></td>
						<td style="word-wrap:break-word"><? echo $composition_arr[$slectResult[csf('fabric_type')]]; ?></td>
						<td align="right"><? echo $slectResult[csf('seam_length')]; ?></td>
						<?
						$total=0;
						foreach($thread_type_arr as $key=>$value)
						{
							$desc_array=array_unique(explode(",",chop($value,',')));
							foreach($desc_array as $desc)
							{
								$thread_length=$thread_desc_arr[$slectResult[csf('id')]][$key][$desc]['tl'];
								$allowance=$thread_desc_arr[$slectResult[csf('id')]][$key][$desc]['aw'];
								$req=$thread_desc_arr[$slectResult[csf('id')]][$key][$desc]['rt'];
								
								echo '<td align="right">'.$thread_length.'</td>';
								echo '<td align="right">'.$allowance.'</td>';
								echo '<td align="right">'.$req.'</td>';
								
								$total+=$req;
								$tot_arr[$key][$desc]+=$req;
							}
						}
						$gTotal+=$total;
						?>
                        <td align="right"><? echo number_format($total,2,'.',''); ?></td>
					</tr>
				<?	
					$i++;
				} 
			?>
            </tbody>
			<tfoot>
                <tr>
                    <th align="right" colspan="5">Total</th>
                    <?
                        foreach($thread_type_arr as $key=>$value)
                        {
                            $desc_array=array_unique(explode(",",chop($value,',')));
                            foreach($desc_array as $desc)
                            {
								
                                echo '<th>&nbsp;</th>';
                                echo '<th>&nbsp;</th>';
                                echo '<th align="right">'.number_format($tot_arr[$key][$desc],2,'.','').'</th>';
                            }
                        }
                    ?>
                    <th align="right"><? echo number_format($gTotal,2,'.',''); ?></th>
                </tr>
                <tr>
                    <th align="right" colspan="5">Total Meters</th>
                    <?
						$gTotal_meter=0;
                        foreach($thread_type_arr as $key=>$value)
                        {
                            $desc_array=array_unique(explode(",",chop($value,',')));
                            foreach($desc_array as $desc)
                            {
								$meter=0;
								if($uom==25)
								{
									$meter=$tot_arr[$key][$desc]/100;
								}
								else
								{
									$meter=$tot_arr[$key][$desc]/39.37;
								}
                                echo '<th>&nbsp;</th>';
                                echo '<th>&nbsp;</th>';
                                echo '<th align="right">'.number_format($meter,2,'.','').'</th>';
								$gTotal_meter+=$meter;
                            }
                        }
                    ?>
                    <th align="right"><? echo number_format($gTotal_meter,2,'.',''); ?></th>
                </tr>
                <tr bgcolor="#dddddd">
                    <th align="right" colspan="5">Total thread per body</th>
                    <th align="center" colspan="<? echo $column+1; ?>"><? echo number_format($gTotal_meter,2,'.','')." Meter"; ?></th>
                </tr>
			</tfoot>
		</table>
        <?
			$column=count($desc_arr)*2;
			$tableWidth=($column*80)+150;
		?>
        <br />
        <table width="<? echo $tableWidth; ?>" cellspacing="0"  border="1" rules="all">
            <thead>
            	<tr bgcolor="#dddddd" align="center">
                	<th width="40">SL</th>
                    <th>Thread Type</th>
                    <?
						foreach($desc_arr as $desc)
						{
							echo '<th width="80">'.$desc.'</th><th>(Meter)</th>';
						}
					?>
                </tr>
			</thead>
            <?
				$x=1; $desc_tot_arr=array();
				foreach($thread_type_arr as $key=>$value)
				{
					echo '<tr><td>'.$x.'</td><td>'.$size_color_sensitive[$key].'</td>';
					foreach($desc_arr as $desc)
					{
						$meter=0;
						if($uom==25)
						{
							$meter=$tot_arr[$key][$desc]/100;
						}
						else
						{
							$meter=$tot_arr[$key][$desc]/39.37;
						}
						
						echo '<td align="right">'.number_format($tot_arr[$key][$desc],2,'.','').'</td><td align="right">'.number_format($meter,2,'.','').'</td>';
						$desc_tot_arr[$desc]+=$tot_arr[$key][$desc];
					}
					echo '</tr>';
					$x++;
				}
			?>
            <tr bgcolor="#dddddd">
                <th align="right" colspan="2">Total(<? echo $unit_of_measurement[$uom]; ?>)</th>
                <?
					foreach($desc_arr as $desc)
					{
						echo '<th align="right">'.number_format($desc_tot_arr[$desc],2,'.','').'</th><th>&nbsp;</th>';
					}
				?>
            </tr>
            <tr bgcolor="#dddddd">
                <th align="right" colspan="2">Total(Meter)</th>
                <? 
					$gTotal_meter_summary=0;
					foreach($desc_arr as $desc)
					{
						$meter=0;
						if($uom==25)
						{
							$meter=$desc_tot_arr[$desc]/100;
						}
						else
						{
							$meter=$desc_tot_arr[$desc]/39.37;
						}
						$gTotal_meter_summary+=$meter;
						echo '<th align="right">'.number_format($meter,2,'.','').'</th><th>&nbsp;</th>';
					}
				?>
            </tr>
        </table>
        <b>Required Per Garments=<? echo number_format($gTotal_meter_summary,2,'.',''); ?> mtr.</b>
    </div>
<?
	exit();
}
?>
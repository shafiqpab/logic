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
 //$yes_no

	if($action=="dyeing_info_from_sample_populate")
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
			$company_id=$result[csf('company_id')];
			$fabric_colorArr[$result[csf('fabric_color')]]=$color_arr[$result[csf('fabric_color')]];
			if($result[csf('color_type_id')])
			{
				$colorTypeArr[$color_type[$result[csf('color_type_id')]]]=$color_type[$result[csf('color_type_id')]];
				$colorTypeIdArr[$result[csf('color_type_id')]]=$result[csf('color_type_id')];
			}
			
		}
		
		$fab_color_drop=create_drop_down( "cbo_dying_fab_color_code", 270, $fabric_colorArr,"", 1, "-- select --","","fnc_dyeing_info_batch_load(this.value)" );
		echo "document.getElementById('dying_color_td').innerHTML = '".$fab_color_drop."';\n";
		exit();	
	}
	if ($action=="load_drop_down_dyeing_info_batch")
	{

		$dataArr=explode("_",$data);
		$color_id=$dataArr[0];
		$booking_no=$dataArr[1]; 

		$sql_all_batch=sql_select("select id,batch_no from pro_batch_create_mst where booking_no='$booking_no' and color_id=$color_id and entry_form=0 and status_active = 1 and is_deleted = 0");
			foreach ($sql_all_batch as $row)
			{
				$dyeing_batch_no_arr[$row[csf('id')]]=$row[csf('batch_no')];
			}
			
			$dyeing_batch_no=create_drop_down( "cbo_dying_batch_no", 270, $dyeing_batch_no_arr,"", 1, "-- select --","","fnc_dyeing_info_recipe_load(this.value)");
			echo "document.getElementById('dying_batch_no_td').innerHTML = '".$dyeing_batch_no."';\n";
		exit();
	}
	if ($action=="load_drop_down_for_recipe_dyeing")
	{

		$dataArr=explode("_",$data);
		$batch_id=$dataArr[0];
		$booking_no=$dataArr[1]; 

		$sql_recipe=sql_select("select id,recipe_no from pro_recipe_entry_mst where batch_id='$batch_id' and status_active = 1 and is_deleted = 0");

			foreach ($sql_recipe as $row)
			{
				$dyeing_recipe_no_arr[$row[csf('id')]]=$row[csf('recipe_no')];
				$recipe_no=$row[csf('recipe_no')];
			}
			echo "$('#txt_dyeing_recipe_num').val('".$recipe_no."');\n";
			?>
				
				<?
			
		exit();
	}	
	if ($action=="save_update_delete_dyeing")
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
			$id_mst=return_next_id( "id", "sample_archive_dyeing_info", 1 ) ;
			//company_id*req_id*txt_booking_no*txt_booking_id*dyeing_update_id*cbo_dying_fab_color_code*cbo_dying_batch_no*txt_dyeing_scouring*txt_dyeing_recipe_num*cbo_dyeing_reactive*cbo_dyeing_both_part*txt_dyeing_enzyme_per*txt_dyeing_dyes_orginal*cbo_dyeing_direct*cbo_dyeing_wash*txt_dyeing_remarks*txt_dyeing_dyes_add*cbo_dyeing_disperse*cbo_dyeing_white

			$field_array="id,company_id,req_id,booking_no,booking_id,fabric_color_id,batch_no,scouring,recipe_no,reactive,both_part,enzyme,dyes_orginal,direct,only_wash,remarks,dyes_add_top,desperse,white,inserted_by, insert_date, status_active, is_deleted";
			$data_array="(".$id_mst.",".$company_id.",".$req_id.",".$txt_booking_no.",".$txt_booking_id.",".$cbo_dying_fab_color_code.",".$cbo_dying_batch_no.",".$txt_dyeing_scouring.",".$txt_dyeing_recipe_num.",".$cbo_dyeing_reactive.",".$cbo_dyeing_both_part.",".$txt_dyeing_enzyme_per.",".$txt_dyeing_dyes_orginal.",".$cbo_dyeing_direct.",".$cbo_dyeing_wash.",".$txt_dyeing_remarks.",".$txt_dyeing_dyes_add.",".$cbo_dyeing_disperse.",".$cbo_dyeing_white.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$rID=sql_insert("sample_archive_dyeing_info",$field_array,$data_array,1);
			//   echo "10**=A=insert into sample_archive_dyeing_info ($field_array) values $data_array";die;
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

			$field_array_up="fabric_color_id*batch_no*scouring*recipe_no*reactive*both_part*enzyme*dyes_orginal*direct*only_wash*remarks*dyes_add_top*desperse*white*updated_by*update_date";

			$data_array_up="".$cbo_dying_fab_color_code."*".$cbo_dying_batch_no."*".$txt_dyeing_scouring."*".$txt_dyeing_recipe_num."*".$cbo_dyeing_reactive."*".$cbo_dyeing_both_part."*".$txt_dyeing_enzyme_per."*".$txt_dyeing_dyes_orginal."*".$cbo_dyeing_direct."*".$cbo_dyeing_wash."*".$txt_dyeing_remarks."*".$txt_dyeing_dyes_add."*".$cbo_dyeing_disperse."*".$cbo_dyeing_white."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID=sql_update("sample_archive_dyeing_info",$field_array_up,$data_array_up,"id","".$dyeing_update_id."",1);
			//echo "10**=".$rID.'=';die;
			if($db_type==2 || $db_type==1 )
			{
				if($rID )
				{
					oci_commit($con);
					echo "1**".str_replace("'","",$dyeing_update_id)."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
				}
				else
				{
					oci_rollback($con);
					echo "10**".str_replace("'","",$dyeing_update_id)."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
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
			$rID1=sql_delete("sample_archive_dyeing_info",$field_array,$data_array,"id","".$dyeing_update_id."",0);
			
			if($db_type==0)
			{
				if($rID1)
				{
					mysql_query("COMMIT");  
					echo "2**".str_replace("'","",$dyeing_update_id)."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".str_replace("'","",$dyeing_update_id)."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($rID1)
				{
					oci_commit($con);
					echo "2**".str_replace("'","",$dyeing_update_id)."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
				}
				else
				{
					oci_rollback($con); 
					echo "10**".str_replace("'","",$dyeing_update_id)."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
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
	if ($action=="listview_dyeing_info")
	{
		$booking_id=$data;
		$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst where status_active=1 and is_deleted=0", 'id', 'batch_no');
		
		$sql_result =sql_select("SELECT id,req_id,company_id,booking_no,booking_id,fabric_color_id,batch_no,recipe_no,only_wash,scouring,reactive,both_part,enzyme,dyes_orginal,direct,remarks,dyes_add_top,desperse,white from sample_archive_dyeing_info a where booking_id=$booking_id and is_deleted=0  and a.status_active=1  order by id asc");
		
		?>
		<table width="620" cellspacing="0" border="1" rules="all" class="rpt_table" >
			<thead>
				<th width="150">Fab. Color/Code</th>
				<th width="150">Batch No</th>
				<th width="100">Scouring Type</th>
				<th width="100">Recipe No.</th>
			</thead>
		</table>
		<div style="width:620px; overflow-y:scroll; max-height:180px;">
			<table width="620" cellspacing="0" border="1" rules="all" class="rpt_table" id="tbl_details_dyeing"><table width="620" cellspacing="0" border="1" rules="all" class="rpt_table" >
				<?
					$i=1;
					foreach ($sql_result as $row)
					{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="get_php_form_data('<? echo $row[csf('id')]; ?>', 'populate_details_dyeing_form_data', 'requires/dyeing_info_entry_controller');"> 
							
							<td width="150"><? echo $color_arr[$row[csf('fabric_color_id')]]; ?></td>
							<td width="150" style="word-break:break-all"><p><? echo $batch_arr[$row[csf('batch_no')]]; ?></p></td>
							<td width="100"  style="word-break:break-all"><? echo $row[csf('scouring')]; ?>&nbsp;</td>
							<td width="100"><p><? echo $row[csf('recipe_no')];; ?></p></td>
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
	if($action=="populate_details_dyeing_form_data")
	{  
		
		$sql_result =sql_select("SELECT  id, req_id, company_id, booking_no, booking_id,fabric_color_id,batch_no,recipe_no,only_wash,scouring,reactive,both_part,enzyme,dyes_orginal,direct,remarks,dyes_add_top,desperse,white from sample_archive_dyeing_info   where  id=$data and  is_deleted=0  and  status_active=1  order by  id asc");
		
		foreach($sql_result as $row)
		{ 

			echo "$('#dyeing_update_id').val('".$row[csf('id')]."');\n";
			$batch_no=$row[csf('batch_no')];
			echo "$('#cbo_dying_fab_color_code').val('".$row[csf('fabric_color_id')]."');\n";
			echo "$('#txt_recipe_num').val('".$row[csf('recipe_no')]."');\n";
			

		}
		//company_id*req_id*txt_booking_no*txt_booking_id*dyeing_update_id*cbo_dying_fab_color_code*cbo_dying_batch_no*txt_dyeing_scouring*txt_dyeing_recipe_num*cbo_dyeing_reactive*cbo_dyeing_both_part*txt_dyeing_enzyme_per*txt_dyeing_dyes_orginal*cbo_dyeing_direct*cbo_dyeing_wash*txt_dyeing_remarks*txt_dyeing_dyes_add*cbo_dyeing_disperse*cbo_dyeing_white
		echo "fnc_dyeing_info_batch_load(".$row[csf('fabric_color_id')].");\n";
		echo "$('#cbo_dying_batch_no').val('".$row[csf('batch_no')]."');\n";
		echo "$('#txt_dyeing_recipe_num').val('".$row[csf('recipe_no')]."');\n";
		echo "$('#cbo_dyeing_wash').val('".$row[csf('only_wash')]."');\n";
		echo "$('#txt_dyeing_scouring').val('".$row[csf('scouring')]."');\n";		 
		echo "$('#cbo_dyeing_reactive').val('".$row[csf('reactive')]."');\n";
		echo "$('#cbo_dyeing_both_part').val('".$row[csf('both_part')]."');\n";
		echo "$('#txt_dyeing_enzyme_per').val('".$row[csf('enzyme')]."');\n";
		echo "$('#txt_dyeing_dyes_orginal').val('".$row[csf('dyes_orginal')]."');\n";
		echo "$('#cbo_dyeing_direct').val('".$row[csf('direct')]."');\n";
		echo "$('#txt_dyeing_remarks').val('".$row[csf('remarks')]."');\n";
		echo "$('#txt_dyeing_dyes_add').val('".$row[csf('dyes_add_top')]."');\n";
		echo "$('#cbo_dyeing_disperse').val('".$row[csf('desperse')]."');\n";
		echo "$('#cbo_dyeing_white').val('".$row[csf('white')]."');\n";
		
		
		if(count($sql_result)>0)
		{
			
		echo "$('#save5').removeClass('formbutton').addClass('formbutton_disabled');\n"; //formbutton 
		
		echo "$('#save5').removeAttr('onclick').attr('onclick','fnc_dyeing_entry(0);')\n";
		echo "$('#update5').removeClass('formbutton_disabled').addClass('formbutton');\n"; //formbutton 
		echo "$('#update5').removeAttr('onclick').attr('onclick','fnc_dyeing_entry(1);')\n";
		echo "$('#Delete5').removeClass('formbutton_disabled').addClass('formbutton');\n"; //formbutton 
		echo "$('#Delete5').removeAttr('onclick').attr('onclick','fnc_dyeing_entry(2);')\n"; 
			//echo "fnc_yarn_button_status(2);\n"; 
			
		}
		else
		{
			//echo "fnc_yarn_button_status(1);\n"; 
			echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_dyeing_entry',5);\n"; 
		}
			
		
			
		unlink($sql_result);
		exit();	
	}	



?>
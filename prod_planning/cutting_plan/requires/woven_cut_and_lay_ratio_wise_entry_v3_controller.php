<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if ($_SESSION['logic_erp']['user_id'] == "") {
	header("location:login.php");
	die;
}
$permission = $_SESSION['page_permission'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

//========== user credential start ========
$userCredential = sql_select("SELECT WORKING_UNIT_ID, unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd where id=$user_id");
$working_unit_id = $userCredential[0][csf('WORKING_UNIT_ID')];

$working_credential_cond = "";

if ($working_unit_id > 0) 
{
	$working_credential_cond = " and comp.id in($working_unit_id)";
}


if(!function_exists('fn_delete_dir_with_files'))
{
	function fn_delete_dir_with_files($dir) {
		foreach(glob($dir . '/*') as $file) {
			if(is_dir($file))
				fn_delete_dir_with_files($file);
			else
				unlink($file);
		}
		rmdir($dir);
	}
}

//--------------------------------------------------------------------------------------------
if ($action == "load_drop_down_location") 
{
	echo create_drop_down("cbo_location_name", 142, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name", "id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/woven_cut_and_lay_ratio_wise_entry_v3_controller', this.value, 'load_drop_down_floor', 'floor_td' )");
	// echo "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' $working_credential_cond order by location_name","id,location_name"  ; 
	exit();
}

if ($action == "load_drop_down_floor") 
{
	echo create_drop_down("cbo_floor_name", 135, "select id,floor_name from lib_prod_floor where production_process=1 and status_active =1 and is_deleted=0 and location_id='$data' order by floor_name", "id,floor_name", 1, "-- Select Floor --", $selected, "", 0);
}

if ($action == "load_drop_down_brand") 
{
	echo create_drop_down("cbo_brand_name", 100, "select id, brand_name from lib_buyer_brand where buyer_id='$data' and status_active =1 and is_deleted=0 $userbrand_idCond order by brand_name ASC", "id,brand_name", 1, "--Select--", "", "");
	exit();
}
if ($action == "load_drop_down_buyer_season") 
{
	echo create_drop_down("cbo_buyer_season_name", 100, "select season_name,id from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC", "id,season_name", 1, "--Select--", "", "");
	exit();
}

if ($db_type == 0) 
{
	$insert_year = "SUBSTRING_INDEX(b.insert_date, '-', 1)";
} else if ($db_type == 2) {
	$insert_year = "extract(year from b.insert_date)";
}

if ($action == "load_drop_down_buyer")
{
	$data = explode("**", $data);
	$sql = "select distinct c.id,c.buyer_name from  wo_po_break_down a,wo_po_details_master b,lib_buyer c where a.job_no_mst=b.job_no and b.company_name=" . $data[2] . "  and job_no_prefix_num='" . $data[0] . "' and $insert_year='" . $data[1] . "' and b.buyer_name=c.id and a.status_active=1 and c.status_active=1 and c.is_deleted=0 group by  c.id,c.buyer_name";
	$result = sql_select($sql);
	foreach ($result as $val) {
		$buyer_value = $val[csf('buyer_name')];
	}
	echo create_drop_down("txt_buyer_name", 140, $sql, "id,buyer_name", 0, "select Buyer", $buyer_value);
	exit();
}

if ($action == "load_drop_down_order_garment") 
{
	$ex_data = explode("_", $data);
	$gmt_item_arr = return_library_array("select gmts_item_id from wo_po_details_master where job_no='" . $ex_data[0] . "' and status_active=1", 'id', 'gmts_item_id');
	$gmt_item_id = implode(",", $gmt_item_arr);
	if (count($gmt_item_arr) == 1) {
		echo create_drop_down("cbogmtsitem_$ex_data[1]", 120, $garments_item, "", 1, "-- Select Item --", $gmt_item_id, "", "", $gmt_item_id);
	} else if (count($gmt_item_arr) > 1) {
		echo create_drop_down("cbogmtsitem_$ex_data[1]", 120, $garments_item, "", 1, "-- Select Item --", $selected, "", "", $gmt_item_id);
	} else if (count($gmt_item_arr) == 0) {
		echo create_drop_down("cbogmtsitem_$ex_data[1]", 120, $blank_array, "", 1, "-- Select Item --", $selected, "", "");
	}
	exit();
}

// if ($action == "load_drop_down_color") {
// 	$ex_data = explode("_", $data);
// 	$color_item_arr = return_library_array("SELECT a.id,a.color_name from lib_color a,wo_po_color_size_breakdown b,wo_po_break_down c  where  a.id=b.color_number_id  and c.id =b.po_break_down_id and b.job_no_mst='" . $ex_data[0] . "'  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  group by a.id,a.color_name", "id", "color_name");
// 	echo create_drop_down("cbocolor_$ex_data[1]", 100, $color_item_arr, "", 1, "select color", '', "reset_fld($ex_data[1])");
// 	exit();
// 	exit();
// }

if ($action == "load_drop_down_batch") {
	$ex_data = explode("_", $data);
	$batch_array = array();
	$sql = "select a.id, a.batch_no, a.extention_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.color_id='" . $ex_data[1] . "' and b.po_id in(" . $ex_data[0] . ") and b.status_active=1 and b.is_deleted=0 and a.entry_form in(0,7,37,66,68) group by a.id, a.batch_no, a.extention_no";
	$result = sql_select($sql);
	foreach ($result as $row) {
		$ext = '';
		if ($row[csf('extention_no')] > 0) {
			$ext = '-' . $row[csf('extention_no')];
		}
		$batch_array[$row[csf('id')]] = $row[csf('batch_no')] . $ext;
	}

	$sql = "select a.id, a.batch_no, a.extention_no from pro_batch_create_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.id=b.batch_id and b.id=c.dtls_id and c.color_id='" . $ex_data[1] . "' and c.po_breakdown_id in(" . $ex_data[0] . ") and b.status_active=1 and b.is_deleted=0 and c.entry_form in(14,15) and c.trans_type=5 group by a.id, a.batch_no, a.extention_no";
	$result = sql_select($sql);
	foreach ($result as $row) {
		$ext = '';
		if ($row[csf('extention_no')] > 0) {
			$ext = '-' . $row[csf('extention_no')];
		}
		$batch_array[$row[csf('id')]] = $row[csf('batch_no')] . $ext;
	}
	if (count($batch_array) > 0) {
		echo create_drop_down("cbobatch_$ex_data[2]", 100, $batch_array, "", 1, "select Batch", $selected, "batch_match(this.id,this.value)");
	} else {
		echo create_drop_down("cbobatch_$ex_data[2]", 100, $blank_array, "", 1, "select Batch", $selected, "batch_match(this.id,this.value)");
	}
	exit();
}
if ($action=="load_drop_down_order_qty_with_country")
{
	$ex_data = explode("_",$data);
	

	 $sql="select sum(plan_cut_qnty) as plan_qty from  wo_po_color_size_breakdown where po_break_down_id in (".$ex_data[0].") and item_number_id=".$ex_data[1]." and color_number_id in(".$ex_data[2].") and country_id in (".$ex_data[4].") and status_active=1 group by po_break_down_id,item_number_id,color_number_id ";
	$result=sql_select($sql);
	foreach($result as $row)
	{
		echo "document.getElementById('txtorderqty_$ex_data[3]').value  = '".($row[csf("plan_qty")])."';\n"; 
		$plan_qty=$row[csf("plan_qty")];
	}

	$sql_marker="select sum(b.size_qty) as mark_qty from  ppl_cut_lay_dtls a,ppl_cut_lay_bundle b where a.id=b.dtls_id and  a.order_ids=".$ex_data[0]." and a.gmt_item_id=".$ex_data[1]." and a.color_ids=".$ex_data[2]." and a.status_active=1 and b.country_id in (".$ex_data[4].") group by a.order_id,a.gmt_item_id,a.color_ids ";
 //echo $sql_marker;die;
	$result=sql_select($sql_marker);
	foreach($result as $rows)
	{
		
		echo "document.getElementById('txttotallay_$ex_data[3]').value  = '".$rows[csf("mark_qty")]."';\n"; 
		$marker_qty=$rows[csf("mark_qty")];
	}
	$lay_balance=$plan_qty-$marker_qty;
	echo "document.getElementById('txtlaybalanceqty_$ex_data[3]').value  = $lay_balance\n"; 
	
	exit();
}

if ($action == "load_drop_down_order_qty") 
{
	$ex_data = explode("_", $data);


	$sql_country="select a.country_id,b.country_name  from  wo_po_color_size_breakdown a,lib_country b where a.country_id=b.id and  a.po_break_down_id in(".$ex_data[0].") and a.item_number_id in(".$ex_data[1].") and a.color_number_id in (".$ex_data[2].") and a.status_active=1   ";
	// echo $sql_country;die;
	$result_country=sql_select($sql_country);
	foreach ($result_country as  $value) {
		$country_name_arr[$value[csf('country_id')]]=$value[csf('country_name')];
		$country_id_arr[$value[csf('country_id')]]=$value[csf('country_id')];
	}

	echo "document.getElementById('countryName_$ex_data[3]').value  = '".implode(",",$country_name_arr)."';\n";
	echo "document.getElementById('countryId_$ex_data[3]').value  = '".implode(",",$country_id_arr)."';\n";

	$sql = "SELECT sum(plan_cut_qnty) as plan_qty from  wo_po_color_size_breakdown where po_break_down_id in(" . $ex_data[0] . ") and item_number_id in(" . $ex_data[1] . ") and color_number_id in(" . $ex_data[2] . " )and status_active=1 and is_deleted=0";
	// echo $sql;die;
	$result = sql_select($sql);
	$plan_qty = 0;
	foreach ($result as $row) {
		$plan_qty += $row[csf("plan_qty")];
	}
	echo "document.getElementById('txtorderqty_$ex_data[3]').value  = '" . $plan_qty . "';\n";

	$sql_marker = "select sum(b.marker_qty) as mark_qty from  ppl_cut_lay_dtls a, ppl_cut_lay_size b where a.id=b.dtls_id and b.order_ids in(" . $ex_data[0] . ") and a.gmt_item_id=" . $ex_data[1] . " and a.color_ids in(" . $ex_data[2] . ") and a.status_active=1 and b.status_active=1 and b.is_deleted=0";
	// echo $sql_marker;die;

	$result = sql_select($sql_marker);
	$marker_qty = 0;
	foreach ($result as $rows) {
		$marker_qty += $rows[csf("mark_qty")];
	}
	echo "document.getElementById('txttotallay_$ex_data[3]').value  = '" . $marker_qty . "';\n";
	$lay_balance = $plan_qty - $marker_qty;
	echo "document.getElementById('txtlaybalanceqty_$ex_data[3]').value  = $lay_balance\n";

	exit();
}

if ($action == "tna_date_status") 
{
	$ex_data = explode("**", $data);
	$cut_start_date = $ex_data[0];
	$cut_end_date = $ex_data[1];
	$order_all = $ex_data[2];
	//echo $cut_start_date;die;
	//**********************************Tna Date*********************************************************************************************
	for ($sl = 1; $sl <= $row_num; $sl++) {
		$cbo_order_id = "cboorderno_" . $sl;
		if ($tna_order != "") $tna_order .= "," . $$cbo_order_id;
		else $tna_order .= $$cbo_order_id;
	}
	$tna_variable = return_field_value("tna_integrated", "variable_order_tracking", " company_name=$ex_data[3] AND variable_list=14");
	if ($tna_variable == 1) {
		$min_tna_date = return_field_value(" min(a.task_start_date) as min_start_date", "tna_process_mst a, lib_tna_task b", " b.task_name=a.task_number and po_number_id in ($order_all) and task_name=84", "min_start_date");
		$max_tna_date = return_field_value("max(a.task_finish_date) as max_end_date ", "tna_process_mst a, lib_tna_task b", " b.task_name=a.task_number and po_number_id in ($order_all) and task_name=84", "max_end_date");

		$order_number_arr = return_library_array("select id, po_number from wo_po_break_down where id in ($order_all)", 'id', 'po_number');

		//  $min_start_date=date("Y-m_d",strtotime($min_start_date));
		$max_end_date = date("Y-m_d", strtotime($max_tna_date));
		$cut_start_date = date("Y-m_d", strtotime($cut_start_date));
		$cut_end_date = date("Y-m_d", strtotime($cut_end_date));
		if ($cut_end_date > $max_end_date) {
			$sql_tna_date = sql_select("select a.po_number_id, a.task_start_date,a.task_finish_date	from  tna_process_mst a, lib_tna_task b where b.task_name=a.task_number and po_number_id in ($order_all) and task_name=84");
			if (count($sql_tna_date) > 0) {
				foreach ($sql_tna_date as $row) {
					if ($poNumber == "") {
						$poNumber = $order_number_arr[$row[csf('po_number_id')]];
						$po_st_date = $row[csf('task_start_date')];
						$po_en_date = $row[csf('task_finish_date')];
						$po_end_date = date("d-m-Y", strtotime($po_en_date));
						$po_start_date = date("d-m-Y", strtotime($po_st_date));
					} else {
						$poNumber = $poNumber . "**" . $order_number_arr[$row[csf('po_number_id')]];
						$po_st_date = $row[csf('task_start_date')];
						$po_en_date = $row[csf('task_finish_date')];
						$po_start_date = $po_start_date . "**" . date("d-m-Y", strtotime($po_st_date));
						$po_end_date = $po_end_date . "**" . date("d-m-Y", strtotime($po_en_date));
					}
				}
				$min_start_date = date("d-m-Y", strtotime($min_tna_date));
				$max_end_date = date("d-m-Y", strtotime($max_tna_date));
				echo "0##" . $poNumber . "##" . $po_start_date . "##" . $po_end_date . "##" . $min_start_date . "##" . $max_end_date;
				die;
			} else echo 1;
			die;
		}
		echo 1;
		die;
	} else echo 2;
	die;

	//***********************************End Tna date*******************************************************************************************
}


if ($action == "check_batch_no") 
{
	$data = explode("**", $data);
	$added_barcode_no = $data[2];
	$scanned_barcode_arr = array();
	$barcodeData = sql_select("select barcode_no from pro_roll_details where entry_form=715 and status_active=1 and is_deleted=0");

	foreach ($barcodeData as $row) {
		$scanned_barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
	}

	if ($added_barcode_no != '') 	$added_barcode_cond = " and c.barcode_no not in (" . $added_barcode_no . ")";
	else 						$added_barcode_cond = "";
	//print_r($scanned_barcode_arr);
	$sql = "select c.barcode_no from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in (64,37,72) and a.batch_no='" . trim($data[0]) . "' and b.po_id " . str_replace("'", "", trim($data[1])) . "' and a.is_deleted=0 and   a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $added_barcode_cond ";

	$data_array = sql_select($sql);
	$barcode_arr = array();
	if (count($data_array) > 0) {
		foreach ($data_array as $val) {
			if ($scanned_barcode_arr[$val[csf('barcode_no')]] == '') {
				$barcode_arr[$val[csf('barcode_no')]] = $val[csf('barcode_no')];
			}
		}
		//$barcode_arr=json_encode($barcode_arr);
		//echo $barcode_arr;
		echo trim(implode(",", $barcode_arr));
		//print_r($barcode_arr);die;
	} else {
		echo "0";
	}
	exit();
}

if ($action == "roll_popup") 
{
	echo load_html_head_contents("Plies Info Roll Wise", "../../../", 1, 1, '', '1', '');
	extract($_REQUEST);
	// echo "<pre>";print_r($_REQUEST);die;

	$color_id_arr = array_unique(array_filter(explode(",",$color)));
	//echo $color_id."<br>";
	
	//$order_no=str_replace("'","",$order_no);
	//echo $order_no;die;
	//$roll_maintained=1;

	if($rollData!="")
	{
		$color_id_arr = array();
		$plies_color_arr = explode("**",$rollData);
		foreach ($plies_color_arr as $key => $v) 
		{
			$v_ex = explode("=",$v);
			$color_id_arr[] = $v_ex[7];
		}

	}

	// echo "<pre>";print_r($plies_color_arr);die;

	$color_Arr_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
  ?>
	<script>
		var roll_maintained = <? echo $roll_maintained; ?>;
		var rollData = '<? echo $rollData; ?>';
		var scanned_barcode = new Array();
		var roll_details_array = new Array();
		var barcode_array = new Array();
		<?
		$scanned_barcode_array = array();
		$scanned_barcode_data = sql_select("select barcode_no from pro_roll_details where entry_form=93 and status_active=1 and is_deleted=0");
		//echo $scanned_barcode_data;die;
		foreach ($scanned_barcode_data as $row) {
			$scanned_barcode_array[] = $row[csf('barcode_no')];
		}
		$jsscanned_barcode_array = json_encode($scanned_barcode_array);
		echo "scanned_barcode = " . $jsscanned_barcode_array . ";\n";

		
		$data_array =sql_select("SELECT c.barcode_no, c.roll_id, c.roll_no, c.qnty from pro_grey_batch_dtls b, pro_roll_details c where b.id=c.dtls_id and c.entry_form=715 and c.po_breakdown_id in(" . str_replace("'", "", $order_no) . ") and b.color_id in ($color) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
		//echo $data_array;die;
		//echo $data_array;die;
		$roll_details_array = array();
		$barcode_array = array();
		foreach ($data_array as $row) {
			$roll_details_array[$row[csf("barcode_no")]]['roll_id'] = $row[csf("roll_id")];
			$roll_details_array[$row[csf("barcode_no")]]['roll_no'] = $row[csf("roll_no")];
			$roll_details_array[$row[csf("barcode_no")]]['qnty'] = $row[csf("qnty")];
			$barcode_array[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
		}
	
		$roll_details_array = array();
		$barcode_array = array();
		foreach ($data_array as $row) {
			$item_description_arr = explode(",", $row[csf('item_description')]);
			$roll_details_array[$row[csf("barcode_no")]]['roll_id'] = $row[csf("roll_id")];
			$roll_details_array[$row[csf("barcode_no")]]['roll_no'] = $row[csf("roll_no")];
			$roll_details_array[$row[csf("barcode_no")]]['qnty'] = $row[csf("qnty")];
			$roll_details_array[$row[csf("barcode_no")]]['batch_no'] = $row[csf("batch_no")];
			$roll_details_array[$row[csf("barcode_no")]]['gsm'] = $item_description_arr[2];
			$roll_details_array[$row[csf("barcode_no")]]['shade'] = $row[csf("shade")];
			$barcode_array[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
		}

		$jsroll_details_array = json_encode($roll_details_array);
		echo "var roll_details_array = " . $jsroll_details_array . ";\n";

		$jsbarcode_array = json_encode($barcode_array);
		echo "var barcode_array = " . $jsbarcode_array . ";\n";
		?>

		function openmypage_batch() {
			var row_num = $('#txt_tot_row').val();
			var added_barcode_no = '';
			for (var k = 1; k <= row_num; k++) {
				if ($('#barcodeNo_' + k).val() != "" && typeof($('#barcodeNo_' + k).val()) !== 'undefined') {
					if (added_barcode_no != "") added_barcode_no = added_barcode_no + "," + $('#barcodeNo_' + k).val();
					else added_barcode_no = $('#barcodeNo_' + k).val();
				}
			}

			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'woven_cut_and_lay_ratio_wise_entry_v3_controller.php?order_no=' + <? echo $order_no; ?> + '&color=' + <? echo $color; ?> + '&added_barcode_no=' + added_barcode_no + '&action=batch_popup', 'Batch Barcode Popup', 'width=580px,height=300px,center=1,resize=1,scrolling=0', '../../')
			emailwindow.onclose = function() {
				var theform = this.contentDoc.forms[0] //("search_order_frm"); //Access the form inside the modal window
				var barcode_nos = this.contentDoc.getElementById("hidden_barcode_nos").value; //Barcode Nos

				if (barcode_nos != "") {
					var barcode_upd = barcode_nos.split(",");
					var row_num = $('#txt_tot_row').val();
					for (var k = 0; k < barcode_upd.length; k++) {
						if ($('#barcodeNo_' + row_num).val() != "") {
							add_break_down_tr(row_num);
							row_num++;
						}

						var bar_code = barcode_upd[k];
						load_data(row_num, bar_code);
					}
				}
			}
		}

		$(document).ready(function(e) 
		{
			if (roll_maintained == 1) {
				$('#barcode_div').show();
				$('#batch_div').show();
			} else {
				$('#barcode_div').hide();
			}

			if (rollData != "") 
			{				
				let data = rollData.split("**");
				// alert(rollData);
				// alert( data.length);
				let i=1;
				for (let k = 0; k < data.length; k++) 
				{
					let datas = data[k].split("=");
					let barcode_no = datas[0];
					let rollNo = datas[1];
					let rollId = datas[2];
					let rollWgt = datas[3];
					let plies = datas[4];
				
					let batchNo = datas[5];
					let shade = datas[6];
					let color = datas[7];
					

					let row_num = $('#txt_tot_row').val();
					// alert(row_num);
					if ($('#barcodeNo_' + row_num).val() != "") {
						add_break_down_tr(row_num);
						row_num++;
					}
						// alert(row_num);

					$("#barcodeNo_" + i).val(barcode_no);
					$("#rollNo_" + i).val(rollNo);
					$("#rollId_" + i).val(rollId);
					$("#rollWgt_" + i).val(rollWgt);
					$("#plies_" + i).val(plies);
					$("#batchNo_" + i).val(batchNo);
					$("#txtshade_" + i).val(shade);
					$("#colorId_" + i).val(color);
					i++;
					console.log(barcode_no,rollNo,rollId,rollWgt,plies,batchNo,shade,color,i);
					
					if (jQuery.inArray(barcode_no, scanned_barcode) > -1) {
						scanned_barcode.push(barcode_no);
					}
				}
			}
		});

		

		function add_break_down_tr( i,tr )
		{
			var row_num=$('#tbl_list_search tr').length;
			
			
			var j=i;
			var index = $(tr).closest("tr").index();
			
			var i=row_num;
			i++;
			var tr=$("#tbl_list_search tr:eq("+index+")");
			
			var cl=$("#tbl_list_search tr:eq("+index+")").clone().find("input,select").each(function() 
			{
				$(this).attr({
				'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				'name': function(_, name) { return name },
				'value': function(_, value) { return value }              
				});
			}).end();
			tr.after(cl);
			$('#increase_' + i).removeAttr("value").attr("value", "+");
			$('#decrease_' + i).removeAttr("value").attr("value", "-");
			$('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+",this);");
			$('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",this);");

			var sl=1;
			$("#tbl_list_search").find('tr').each(function ()
			{
				$(this).removeAttr("id").attr("id","tr_"+sl+");");
				sl+=1;
			});
		
			set_all_onclick();
		}


		function fn_deletebreak_down_tr(rowNo,tr) 
		{
			var numRow = $('#tbl_list_search  tr').length; 
			
			if(rowNo==1 && numRow >1)
			{
				var index = $(tr).closest("tr").index();
				$("#tbl_list_search  tr:eq("+index+")").remove()
			
			}
			if(rowNo!=1)
			{				
				var index = $(tr).closest("tr").index();
				$("#tbl_list_search tr:eq("+index+")").remove()
				
			
			}

			var sl=1;
			$("#tbl_list_search").find('tr').each(function ()
			{
				$(this).removeAttr("id").attr("id","tr_"+sl+");");
				sl+=1;
			});
			
		}

		function roll_duplication_check(row_id) 
		{
			var row_num = $('#tbl_list_search tr').length;
			var roll_no = $('#rollNo_' + row_id).val();

			if (roll_no * 1 > 0) {
				for (var j = 1; j <= row_num; j++) {
					if (j == row_id) {
						continue;
					} else {
						var roll_no_check = $('#rollNo_' + j).val();
						if (roll_no == roll_no_check) {
							alert("Duplicate Roll No.");
							$('#rollNo_' + row_id).val('');
							return;
						}
					}
				}
			}
		}


		$('#txt_batch_no').live('keydown', function(e) 
		{
			if (e.keyCode === 13) {
				e.preventDefault();
				var batch_no = $('#txt_batch_no').val();
				var order_id = <?php echo $order_no; ?>;

				var row_num = $('#tbl_list_search').val();
				var added_barcode_no = '';
				for (var k = 1; k <= row_num; k++) {
					if ($('#barcodeNo_' + row_num).val() != "") {
						if (added_barcode_no != "") added_barcode_no = added_barcode_no + "," + $('#barcodeNo_' + k).val();
						else added_barcode_no = $('#barcodeNo_' + k).val();
					}
				}
				var response_data = return_global_ajax_value(batch_no + "**" + order_id + "**" + added_barcode_no, 'check_batch_no', '', 'woven_cut_and_lay_ratio_wise_entry_v3_controller');
				//alert(response_data);return;
				//var row_num=$('#txt_tot_row').val();
				if (response_data != 0) {
					response_data_arr = trim(response_data).split(",");
					//alert(response_data_arr.length);return;
					for (var i = 0; i < response_data_arr.length; i++) {
						var bar_code = response_data_arr[i];
						//alert(bar_code);return;
						if (jQuery.inArray(bar_code, scanned_barcode) > -1) {
							alert('Sorry! Barcode Already Scanned.');
							//$('#txt_bar_code_num').val('');
							return;
						}

						if (barcode_array[bar_code]) {
							if ($('#barcodeNo_' + row_num).val() != "") {
								add_break_down_tr(row_num);
								row_num++;
							}
							load_data(row_num, bar_code);
						}
					}
				}
			}
		});

		$('#txt_bar_code_num').live('keydown', function(e) 
		{
			if (e.keyCode === 13) {
				e.preventDefault();
				var bar_code = $('#txt_bar_code_num').val();

				if (!barcode_array[bar_code]) {
					alert('Barcode is Not Valid');
					$('#messagebox_main', window.parent.document).fadeTo(100, 1, function() //start fading the messagebox
						{
							$('#messagebox_main', window.parent.document).html('Barcode is Not Valid.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
						});
					$('#txt_bar_code_num').val('');
					return;
				}

				if (jQuery.inArray(bar_code, scanned_barcode) > -1) {
					alert('Sorry! Barcode Already Scanned.');
					$('#txt_bar_code_num').val('');
					return;
				}

				var row_num = $('#tbl_list_search').val();
				if ($('#barcodeNo_' + row_num).val() != "") {
					add_break_down_tr(row_num);
					row_num++;
				}
				load_data(row_num, bar_code);
			}
		});

		function openmypage_barcode() 
		{

			var row_num = $('#tbl_list_search').val();
			var added_barcode_no = '';
			for (var k = 1; k <= row_num; k++) {
				if ($('#barcodeNo_' + row_num).val() != "") {
					if (added_barcode_no != "") added_barcode_no = added_barcode_no + "," + $('#barcodeNo_' + k).val();
					else added_barcode_no = $('#barcodeNo_' + k).val();
				}
			}


			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'woven_cut_and_lay_ratio_wise_entry_v3_controller.php?order_no=' + <? echo $order_no; ?> + '&color=' + <? echo $color; ?> + '&action=barcode_popup', 'Barcode Popup', 'width=480px,height=300px,center=1,resize=1,scrolling=0', '../../')
			emailwindow.onclose = function() {
				var theform = this.contentDoc.forms[0] //("search_order_frm"); //Access the form inside the modal window
				var barcode_nos = this.contentDoc.getElementById("hidden_barcode_nos").value; //Barcode Nos

				if (barcode_nos != "") {
					var barcode_upd = barcode_nos.split(",");
					var row_num = $('#tbl_list_search').val();
					for (var k = 0; k < barcode_upd.length; k++) {
						if ($('#barcodeNo_' + row_num).val() != "") {
							add_break_down_tr(row_num);
							row_num++;
						}

						var bar_code = barcode_upd[k];
						load_data(row_num, bar_code);
					}
				}
			}
		}
		function load_data(row_num, bar_code) 
		{
			if (bar_code == "") bar_code = 0;
			$("#barcodeNo_" + row_num).val(bar_code);
			$("#rollNo_" + row_num).val(roll_details_array[bar_code]['roll_no']);
			$("#rollId_" + row_num).val(roll_details_array[bar_code]['roll_id']);
			$("#batchNo_" + row_num).val(roll_details_array[bar_code]['batch_no']);
			$("#rollWgt_" + row_num).val(roll_details_array[bar_code]['qnty']);
			$("#txtshade_" + row_num).val(roll_details_array[bar_code]['shade']);
			scanned_barcode.push(bar_code);
		}

		function fn_close()
		{
			
				let tot_row = $("#tbl_list_search tr").length;
				let tot_plies = '';
				let save_string ='';

				$("#tbl_list_search").find('tr').each(function() {
				let barcodeNo = $(this).find('input[name="barcodeNo[]"]').val();
			
				let color = $(this).find('input[name="colorId[]"]').val();	
				let rollNo = $(this).find('input[name="rollNo[]"]').val();
				let rollId = $(this).find('input[name="rollId[]"]').val();
				let rollWgt = $(this).find('input[name="rollWgt[]"]').val();
				let plies = $(this).find('input[name="plies[]"]').val();
				let batchNo = $(this).find('input[name="batchNo[]"]').val();
				let txtshade = $(this).find('input[name="txtshade[]"]').val();

				if (plies * 1 > 0) {
					tot_plies = tot_plies * 1 + plies * 1;
					if (barcodeNo == "") barcodeNo = 0;
					if (save_string == "") {
						save_string = barcodeNo + "=" + rollNo + "=" + rollId + "=" + rollWgt + "=" + plies + "=" + batchNo + "=" + txtshade+ "=" +color;
					} else {
						save_string += "**" + barcodeNo + "=" + rollNo + "=" + rollId + "=" + rollWgt + "=" + plies + "=" + batchNo + "=" + txtshade+ "=" +color;
					}
				}
			});
				$('#hide_data').val(save_string);
				$('#hide_plies').val(tot_plies);

			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<div align="center" style="width:100%; overflow-y:hidden;">
			<fieldset style="width:890px">
				<div style="margin-bottom:5px; display:none; float:left" id="batch_div">
					<strong>Batch No</strong>&nbsp;&nbsp;
					<input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:170px;" onDblClick="openmypage_batch()" placeholder="Browse/Write/scan" />
				</div>
				<div style="margin-bottom:5px; display:none" id="barcode_div">
					<strong>Barcode Number</strong>&nbsp;&nbsp;
					<input type="text" name="txt_bar_code_num" id="txt_bar_code_num" class="text_boxes_numeric" style="width:170px;" onDblClick="openmypage_barcode()" placeholder="Browse/Write/scan" />
				</div>
				<table width="890" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
					<thead>
					    <th>SL</th>
						<th>Color</th>
						<th>Roll Number</th>
						<th>Lot No.</th>
						<th>Shade</th>
						<th>Roll Length</th>
						<?
						$disbled = "";
						if ($roll_maintained == 1) {
							echo "<th>Barcode No</th>";
							$disbled = "disabled";
						}
						?>
						<th>Plies</th>
						<th></th>
					</thead>
					<tbody id="tbl_list_search">
						<?
					
							$i=1;
						
							foreach ($color_id_arr  as  $key=>$colorid) 
							{
								
								?>
									<tr id="tr_<?=$i; ?>">
									
										<td>
											<?=$i?>
										</td>
										
										<td>
											<input type="text" id="colorNo_<?=$i;?>" name="colorNo[]" class="text_boxes" value="<?= $color_Arr_library[$colorid] ?>" style="width:100px" disabled />
											<input type="hidden" id="colorId_<?=$i;?>" name="colorId[]" value="<?=$colorid?>" /> 
											
										</td>
										<td>
											<input type="text" id="rollNo_<?=$i;?>" name="rollNo[]" class="text_boxes_numeric" style="width:110px" />
											<input type="hidden" id="rollId_<?=$i;?>" name="rollId[]" value="" /><!--onBlur="roll_duplication_check(1);"-->
										</td>
										<td>
											<input type="text" id="batchNo_<?=$i;?>" name="batchNo[]" class="text_boxes" value="" style="width:100px" />
										</td>
										<td>
											<input type="text" id="txtshade_<?=$i;?>" name="txtshade[]" class="text_boxes" value="" style="width:100px" />
										</td>
										<td>
											<input type="text" id="rollWgt_<?=$i;?>" name="rollWgt[]" class="text_boxes_numeric" value="" style="width:100px" <? echo $disbled; ?> /></td>
											<? if ($roll_maintained == 1) {
											?>
												<td><input type="text" id="barcodeNo_<?=$i;?>" name="barcodeNo[]" class="text_boxes_numeric" value="" style="width:100px" disabled />
										</td>
											<?
											} else {
											?>
												<td style="display:none"><input type="text" id="barcodeNo_<?=$i;?>" name="barcodeNo[]" class="text_boxes_numeric" value="" style="width:100px" disabled /></td>
											<?
											}
											?>
										<td>
											<input type="text" id="plies_<?=$i;?>" name="plies[]" class="text_boxes_numeric" value="" style="width:100px" />
										</td>
										<td width="70">
											<? if ($roll_maintained != 1) {
											?>
												<input type="button" id="increase_" name="increase[]" style="width:30px" class="formbutton" value="+"onclick="javascript:add_break_down_tr(<?=$i;?> ,this );" />
											<?
											}
											?>
											<input type="button" id="decrease_" name="decrease[]" style="width:30px" class="formbutton" value="-" onclick="javascript:fn_deletebreak_down_tr(<?=$i;?> ,this );"  />
										</td>
									</tr>
								<?
								$i++;
							}
						?>	
					</tbody>
				</table>
				<div align="center" style="margin-top:10px">
					<input type="button" class="formbutton" onClick="fn_close();" value="Close" style="width:100px" />
					<input type="hidden" id="hide_plies" />
					<input type="hidden" id="hide_data" />
					<input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="1">

			
				</div>
			</fieldset>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
 <?
	exit();
}

if ($action == "barcode_popup")
{
	echo load_html_head_contents("Barcode Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
 ?>
	<script>
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search', -1);
		});

		var selected_id = new Array();

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function js_set_value(str) {
			toggle(document.getElementById('search' + str), '#FFFFCC');

			if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1) {
				selected_id.push($('#txt_individual_id' + str).val());
			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == $('#txt_individual_id' + str).val()) break;
				}
				selected_id.splice(i, 1);
			}
			var id = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
			}
			id = id.substr(0, id.length - 1);

			$('#hidden_barcode_nos').val(id);
		}

		function fnc_close() {
			parent.emailwindow.hide();
		}
	</script>

	</head>

	<body>
		<div align="center" style="width:450px;">
			<form name="searchwofrm" id="searchwofrm">
				<fieldset style="width:440px; margin-left:2px">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="420">
						<thead>
							<th width="50">SL</th>
							<th width="130">Barcode No</th>
							<th width="100">Roll No</th>
							<th>Roll Qty.</th>
						</thead>
					</table>
					<div style="width:420px; max-height:200px; overflow-y:scroll" id="list_container" align="left">
						<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="400" id="tbl_list_search">
							<?
							$scanned_barcode_arr = array();
							$barcodeData = sql_select("select barcode_no from pro_roll_details where entry_form=715 and status_active=1 and is_deleted=0");
							foreach ($barcodeData as $row) {
								$scanned_barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
							}
							if ($added_barcode_no != '') 	$added_barcode_cond = " and c.barcode_no not in (" . $added_barcode_no . ")";
							else 						$added_barcode_cond = "";

							$i = 1;
							$data_array = sql_select("select c.barcode_no, c.roll_id, c.roll_no, c.qnty from pro_grey_batch_dtls b, pro_roll_details c where b.id=c.dtls_id and c.entry_form=715 and c.po_breakdown_id in($order_no) and b.color_id=$color and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $added_barcode_cond");
							foreach ($data_array as $row) {
								if ($scanned_barcode_arr[$row[csf('barcode_no')]] == "") {
									if ($i % 2 == 0) $bgcolor = "#E9F3FF";
									else $bgcolor = "#FFFFFF";
							?>
									<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
										<td width="50">
											<? echo $i; ?>
											<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>" />
										</td>
										<td width="130">
											<p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p>
										</td>
										<td width="100"><? echo $row[csf('roll_no')]; ?></td>
										<td align="right"><? echo number_format($row[csf('qnty')], 2); ?></td>
									</tr>
							<?
									$i++;
								}
							}
							?>
						</table>
					</div>
					<table width="420">
						<tr>
							<td align="center">
								<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
								<input type="hidden" name="hidden_barcode_nos" id="hidden_barcode_nos">
							</td>
						</tr>
					</table>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
 <?
}

if ($action == "create_barcode_search_list_view") 
{
	$data = explode("_", $data);

	$search_string = "%" . trim($data[0]) . "%";
	$search_by = $data[1];
	$company_id = $data[2];

	if ($company_id == 0) {
		echo "Please Select Company First.";
		die;
	}

	$search_field_cond = "";
	if (trim($data[0]) != "") {
		if ($search_by == 1) $search_field_cond = "and d.po_number like '$search_string'";
	}

	$scanned_barcode_arr = array();
	$barcodeData = sql_select("select barcode_num from pro_grey_prod_delivery_dtls where entry_form=56 and status_active=1 and is_deleted=0");
	foreach ($barcodeData as $row) {
		$scanned_barcode_arr[$row[csf('barcode_num')]] = $row[csf('barcode_num')];
	}

	$sql = "SELECT a.recv_number, c.barcode_no, c.roll_no, c.qnty, d.po_number, d.pub_shipment_date, d.job_no_mst FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, wo_po_break_down d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and a.entry_form=715 and c.entry_form=715 and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 $search_field_cond";
	//echo $sql;//die;
	$result = sql_select($sql);
 ?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="120">System Id</th>
			<th width="110">Job No</th>
			<th width="110">Order No</th>
			<th width="80">Shipment Date</th>
			<th width="100">Barcode No</th>
			<th width="60">Roll No</th>
			<th>Roll Qty.</th>
		</thead>
	</table>
	<div style="width:740px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table" id="tbl_list_search">
			<?
			$i = 1;
			foreach ($result as $row) {
				if ($scanned_barcode_arr[$row[csf('barcode_no')]] == "") {
					if ($i % 2 == 0) $bgcolor = "#E9F3FF";
					else $bgcolor = "#FFFFFF";
			?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
						<td width="40">
							<? echo $i; ?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>" />
						</td>
						<td width="120">
							<p><? echo $row[csf('recv_number')]; ?></p>
						</td>
						<td width="110">
							<p><? echo $row[csf('job_no_mst')]; ?></p>
						</td>
						<td width="110">
							<p><? echo $row[csf('po_number')]; ?></p>
						</td>
						<td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
						<td width="100">
							<p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p>
						</td>
						<td width="60"><? echo $row[csf('roll_no')]; ?></td>
						<td align="right"><? echo number_format($row[csf('qnty')], 2); ?></td>
					</tr>
			<?
					$i++;
				}
			}
			?>
		</table>
	</div>
	<table width="720">
		<tr>
			<td align="center">
				<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
			</td>
		</tr>
	</table>
	<?
	exit();
}

if ($action == "roll_maintained") 
{
	$roll_maintained = return_field_value("fabric_roll_level", "variable_settings_production", "company_name ='$data' and variable_list=3 and item_category_id=51 and is_deleted=0 and status_active=1");
	if ($roll_maintained == 1) $roll_maintained = $roll_maintained;
	else $roll_maintained = 0;

	$rmg_no_creation = return_field_value("smv_source", "variable_settings_production", "company_name='$data' and variable_list=39 and is_deleted=0 and status_active=1");
	if ($rmg_no_creation == "") $rmg_no_creation = 2;
	else $rmg_no_creation = $rmg_no_creation;

	$process_loss_method = return_field_value("process_loss_method", "variable_order_tracking", "company_name=$data  and variable_list=18 and item_category_id=3 and status_active=1 and is_deleted=0");

	echo "document.getElementById('roll_maintained').value 					= '" . $roll_maintained . "';\n";
	echo "document.getElementById('rmg_no_creation').value 					= '" . $rmg_no_creation . "';\n";
	echo "document.getElementById('process_loss_method').value 				= '" . $process_loss_method . "';\n";
	exit();
}

 $size_arr = return_library_array("select id, size_name from lib_size", 'id', 'size_name');

 if ($action == "size_popup") 
 {
	 echo load_html_head_contents("Cut and bundle details", "../../../", 1, 1, '', '1', '');
	 extract($_REQUEST);
	//  print_r($_REQUEST);die;
	 if($hiddiscountryseq==1)
	 {
		 $excountry=explode(",",$country_id); $countrystr="";
		 foreach($excountry as $cseq)
		 {
			 $excountryseq=explode("!",$cseq);
			 if($countrystr=="") $countrystr=$excountryseq[0]; else $countrystr.=','.$excountryseq[0];
		 }
		 $country_id=$countrystr;
	 }
	 $country_is_blank_sql = sql_select("SELECT country_id FROM wo_po_color_size_breakdown where status_active=1 AND is_deleted=0 AND (country_id is null or country_id ='' or country_id=0) AND  po_break_down_id IN(" . str_replace("'", "", $order_id) . ")");
 
	 $print_report_format = return_field_value("format_id", "lib_report_template", "template_name ='" . $cbo_company_id . "' and module_id=4 and report_id=117 and is_deleted=0 and status_active=1");
	 $country_con = ($country_id != "") ? " and country_id in($country_id)" : "";
 
	 ?>
	 <script>
		 var permission = '<?= $permission; ?>';
		 var rmg_no_creation = '<?= $rmg_no_creation; ?>';
		 var without_country = '<?= count($country_is_blank_sql); ?>';
		 var size_wise_repeat_cut_no = '<?= $size_wise_repeat_cut_no; ?>';
 
		 var report_ids = '<?= $print_report_format; ?>';
 
		 function js_set_value(data) {
			 var data = data.split("_");
			 document.getElementById('hidden_batch_no').value = data[0];
			 document.getElementById('hidden_batch_id').value = data[1];
			 parent.emailwindow.hide();
		 }

 
		 function check_sizef_qty(value1, value2, id) {
			 var x = id.split('_');
			 var prev_qty = $("#txt_sizef_prev_qty_" + x[3]).val() * 1;
			 var value = (value1 * 1) * (value2 * 1);
			 var lay_value = $("#txt_layf_balance_" + x[3]).val() * 1;
			 // alert(value+'=='+lay_value+'='+prev_qty);
			 if (value > (lay_value * 1 + prev_qty * 1)) {
				 alert("Marker qty is geater than Lay Balance");
				 $("#txt_sizef_qty_" + x[3]).css({
					 "background-color": "red"
				 });
			 } else {
				 $("#txt_sizef_qty_" + x[3]).css({
					 "background-color": "white"
				 });
			 }
			 $("#txt_sizef_qty_" + x[3]).val(value);
			
			 var size_id = $("#hidden_sizef_id_" + x[3]).val();
			
			 distribute_qnty(size_id, value2);
			
			 distribute_qnty_bl_wise(size_id, value,);
			 calculate_size_wise_total();
			 total_size_qty();
		 }
 
		 function distribute_qnty(size_id, size_ratio) {
			 var row_num = $("#tbl_roll tbody tr").length;
			 for (var i = 1; i <= row_num; i++) {
				 var plies = $("#piles_" + i).val() * 1;
				 var qty = size_ratio * plies;
 
				 $("#sqty_"+size_id + "_" + i).val(qty);
			 }
		 }
		//  alert (distribute_qnty);
 
		 function distribute_qnty_bl_wise(size_id, size_qty) {
			 var row_num = $("#tbl_size_details tbody tr").length;
			 for (var i = 1; i <= row_num; i++) {
				 var lay_balance = $("#txt_lay_balance_" + i).val() * 1;
				 var curr_size_id = $("#hidden_size_id_" + i).val();
 
				 if (size_id == curr_size_id) {
					 $("#txt_excess_" + i).val('');
 
					 if (size_qty * 1 > 0 && lay_balance * 1 > 0) {
						// alert(size_qty+'-'+lay_balance);
						 var bl_size_qty = size_qty - lay_balance;
						 if (bl_size_qty > 0) {
							 $("#txt_size_qty_" + i).val(lay_balance);
							 size_qty = bl_size_qty;
						 } else {
							 $("#txt_size_qty_" + i).val(size_qty);
							 break;
						 }
					 } else {
						 $("#txt_size_qty_" + i).val('');
					 }
				 }
			 }
		 }
 
		 function calculate_total() {
			 var row_num = $("#tbl_size tbody tr").length;
			 var ratio_total = 0;
			 var qty_total = 0;
			 var distributed_total = 0;
			 for (var i = 1; i <= row_num; i++) {
				 ratio_total = ratio_total + $("#txt_sizef_ratio_" + i).val() * 1;
				 qty_total = qty_total + $("#txt_sizef_qty_" + i).val() * 1;
				 distributed_total = distributed_total + $("#txt_distributed_qty_" + i).val() * 1;
			 }
 
			 $('#total_sizef_ratio').text(ratio_total);
			 $('#total_sizef_qty').text(qty_total);
			 $('#total_distributed_qty').text(distributed_total);
		 }
 
		 function calculate_size_wise_total() {
			 var size_arr = [];
			 var row_num = $("#tbl_size_details tbody tr").length;
			 for (var i = 1; i <= row_num; i++) {
				 var size_id = $("#hidden_size_id_" + i).val();
				 var size_qty = $("#txt_size_qty_" + i).val();
				 if (size_arr[size_id] == undefined) size_arr[size_id] = 0;
				 size_arr[size_id] += size_qty * 1;
			 }
 
			 var row_num = $("#tbl_size tbody tr").length;
			 for (var i = 1; i <= row_num; i++) {
				 var size_id = $("#hidden_sizef_id_" + i).val();
				 $('#txt_distributed_qty_' + i).val(size_arr[size_id]);
			 }
			 calculate_total();
		 }
 
		 function total_size_qty() {
			 var row_num = $("#tbl_size_details tbody tr").length;
			 var tot_qty = 0;
			 for (var i = 1; i <= row_num; i++) {
				 tot_qty += ($("#txt_size_qty_" + i).val() != '') ? $("#txt_size_qty_" + i).val() * 1 : 0;
			 }
			 $('#total_size_qty').text(tot_qty);
		 }
 
		 function check_size_qty(i) {
			 var curr_size_qty = $("#txt_size_qty_" + i).val() * 1;
			 var curr_size_id = $("#hidden_size_id_" + i).val();
			 var tot_sizeQty = '';
 
			 var row_num = $("#tbl_size_details tbody tr").length;
			 for (var j = 1; j <= row_num; j++) {
				 var size_id = $("#hidden_size_id_" + j).val();
				 var size_qty = $("#txt_size_qty_" + j).val();
				 if (size_id == curr_size_id) {
					 tot_sizeQty = tot_sizeQty * 1 + size_qty * 1;
				 }
			 }
 
			 var row_num = $("#tbl_size tbody tr").length;
			 var sizef_qty = 0;
			 for (var j = 1; j <= row_num; j++) {
				 var size_id = $("#hidden_sizef_id_" + j).val();
				 if (size_id == curr_size_id) {
					 sizef_qty = $("#txt_sizef_qty_" + j).val();
				 }
			 }
 
			 if (tot_sizeQty > sizef_qty) {
				 alert("Marker Qty Exceeds Distributed Qty.");
				 $("#txt_size_qty_" + i).val('');
				 $("#txt_excess_" + i).val('');
			 }
			 calculate_size_wise_total();
			 total_size_qty();
		 }
 
		 function copy_perc(i) {
			 var value = $('#txt_excess_' + i).val();
			 var curr_size_id = $("#hidden_size_id_" + i).val();
 
			 if ($('#checkbox').is(':checked')) {
				 var row_num = $("#tbl_size tbody tr").length;
				 var sizef_qty = 0;
				 for (var j = 1; j <= row_num; j++) {
					 var size_id = $("#hidden_sizef_id_" + j).val();
					 if (size_id == curr_size_id) {
						 sizef_qty = $("#txt_sizef_qty_" + j).val();
					 }
				 }
 
				 var tot_sizeQty = 0;
				 for (var j = 1; j < i; j++) {
					 var size_id = $("#hidden_size_id_" + j).val();
					 var size_qty = $("#txt_size_qty_" + j).val();
					 if (size_id == curr_size_id) {
						 tot_sizeQty = tot_sizeQty * 1 + size_qty * 1;
					 }
				 }
 
				 var rowCount = $('#tbl_size_details tbody tr').length;
				 for (var j = i; j <= rowCount; j++) {
					 var size_id = $("#hidden_size_id_" + j).val();
					 var bl_qty = $('#txt_lay_balance_' + j).val() * 1;
 
					 if (bl_qty > 0 && size_id == curr_size_id) {
						 document.getElementById('txt_excess_' + j).value = value;
						 var excess_qty = Math.round(bl_qty * 1 + (value / 100) * bl_qty);
						 tot_sizeQty = tot_sizeQty * 1 + excess_qty * 1;
 
						 if (tot_sizeQty > sizef_qty) {
							 alert("Marker Qty Exceeds Distributed Qty.");
							 $("#txt_size_qty_" + j).val('');
							 $("#txt_excess_" + j).val('');
							 $("#txt_excess_" + j).focus();
							 break;
						 }
						 $('#txt_size_qty_' + j).val(Math.abs(excess_qty));
					 }
				 }
				 calculate_size_wise_total();
				 total_size_qty();
			 } else {
				 calculate_excess_qty(i)
			 }
		 }
 
		 function calculate_excess_qty(i) {
			 var bl_qty = $('#txt_lay_balance_' + i).val() * 1;
			 var excess_perc = $('#txt_excess_' + i).val() * 1;
			 if (bl_qty > 0) {
				 var excess_qty = Math.round(bl_qty * 1 + (excess_perc / 100) * bl_qty);
				 $('#txt_size_qty_' + i).val(Math.abs(excess_qty));
				 check_size_qty(i);
			 }
		 }
 
		 function calculate_perc(i) {
			 var bl_qty = $('#txt_lay_balance_' + i).val() * 1;
			 var size_qty = $('#txt_size_qty_' + i).val() * 1;
			 var excess_qty = size_qty - bl_qty;
			 if (excess_qty > 0) {
				 if (bl_qty == 0) {
					 $('#txt_excess_' + i).val(0);
				 } else {
					 var excess_perc = (excess_qty / bl_qty) * 100;
					 $('#txt_excess_' + i).val(excess_perc.toFixed(2));
				 }
			 } else {
				 $('#txt_excess_' + i).val('');
			 }
		 }
 
		 function fnc_cut_lay_size_info(operation) {
			 const btn = (operation == 0) ? document.getElementById('save1') : document.getElementById('update1');
 
			 // btn.addEventListener('dblclick', () => {
			 // 	alert('You just double clicked the button!');
			 // 	return;
			 // });
			 // alert(event.type);				
			 btn.disabled = true;
			 freeze_window(operation);
 
			 if (form_validation('txt_bundle_pcs', 'Pcs Per Bundle') == false) {
				 release_freezing();
				 const btn = (operation == 0) ? document.getElementById('save1') : document.getElementById('update1');
				 btn.disabled = false;
				 return;
			 }
			 if (trim(without_country) > 0) {
				 alert("Delivery Country Blank In Color Size Page");
				 release_freezing();
				 const btn = (operation == 0) ? document.getElementById('save1') : document.getElementById('update1');
				 btn.disabled = false;
				 return;
			 }
 
			 var plies = $("#txt_search_common").val() * 1;
			 var m = 1;
			 var tot_size = $("#size_ratio_tbl tr").length;
			 for (m = 1; m <= tot_size; m++) {
				 var ratio = $("#txt_sizef_ratio_" + m).val() * 1;
				 var sizeQty = $("#txt_sizef_qty_" + m).val() * 1;
				 var distributed_qty = $("#txt_distributed_qty_" + m).val() * 1;
				 if ((plies * ratio) != sizeQty) {
					 alert("Size qty miss match as per plies*ration. Please re-enter size ration.");
					 $("#txt_sizef_ratio_" + m).focus();
					 release_freezing();
					 const btn = (operation == 0) ? document.getElementById('save1') : document.getElementById('update1');
					 btn.disabled = false;
					 return false;
				 }
 
				 if (sizeQty != distributed_qty) {
					 alert("Size qty and distribute qty missmatch. Please re-enter size ration.");
					 $("#txt_sizef_ratio_" + m).focus();
					 release_freezing();
					 const btn = (operation == 0) ? document.getElementById('save1') : document.getElementById('update1');
					 btn.disabled = false;
					 return false;
				 }
			 }
			 // alert(tot_size);
 
 
			 var order_id = '<? echo $order_id; ?>';
			 var gmt_id = <? echo $cbo_gmt_id; ?>;
			 var color_id = '<? echo $cbo_color_id; ?>';
			 var mst_id = <? echo $mst_id; ?>;
			 var dtls_id = <? echo $details_id; ?>;
			 var cbo_company_id = <? echo $cbo_company_id; ?>;
			 var color_type_id = <? echo $cbo_color_type; ?>;
			 	//  alert(order_id);
 
 
 
			 var bundle_per_pcs = $("#txt_bundle_pcs").val();
			 var to_marker_qty = $("#total_sizef_qty").text() * 1;
			 var job_id = $("#hidden_update_job_id").val();
			 var cut_no = $("#hidden_update_cut_no").val();
			 var txt_plies = $("#txt_search_common").val();
			 var txt_bundle_pcs = $("#txt_bundle_pcs").val();
			 var total_distributed_qty = $("#total_distributed_qty").text() * 1;
			 if (to_marker_qty <= 0) {
				 alert("Please Insert Size Qty.");
				 release_freezing();
				 const btn = (operation == 0) ? document.getElementById('save1') : document.getElementById('update1');
				 btn.disabled = false;
				 return;
			 }
 
			 if (to_marker_qty != total_distributed_qty) {
				//  alert("Total Size Qty. and Total Distributed Qty. Should be same.");
				 release_freezing();
				 const btn = (operation == 0) ? document.getElementById('save1') : document.getElementById('update1');
				 btn.disabled = false;
				 return;
			 }
			 //alert(to_marker_qty+"**"+total_distributed_qty);return;
			 //var roll_data=$("#roll_data").val();	
 
			 var row_num = $('#tbl_size_details tbody tr').length;
			 //alert(data1);
			 var data1 = "action=save_update_delete_size&operation=" + operation + "&row_num=" + row_num + "&color_id=" + color_id + "&mst_id=" + mst_id + "&dtls_id=" + dtls_id + "&bundle_per_pcs=" + bundle_per_pcs + "&to_marker_qty=" + to_marker_qty + "&cbo_company_id=" + cbo_company_id + "&job_id=" + job_id + "&cut_no=" + cut_no + "&order_id=" + order_id + "&gmt_id=" + gmt_id + "&txt_plies=" + txt_plies + "&txt_bundle_pcs=" + txt_bundle_pcs + "&rmg_no_creation=" + rmg_no_creation + "&color_type_id=" + color_type_id + "&size_wise_repeat_cut_no=" + size_wise_repeat_cut_no;
			//   alert(data1);

			 var data2 = '';
			 var size_data = '';
			 var max_seq = 0;
			 var size_arr = [];
			 var roll_data = '';
 
			 var size_row_num = $('#tbl_size tbody tr').length;
			 for (var k = 1; k <= size_row_num; k++) {
				 var seq = $("#txt_bundle_" + k).val() * 1;
				 if (seq > max_seq) max_seq = seq;
				 // size_data+=get_submitted_data_string('txt_layf_balance_'+k+'*txt_sizef_ratio_'+k+'*txt_sizef_qty_'+k+'*hidden_sizef_id_'+k+'*txt_bundle_'+k,"../../../",k);
				 size_data += '&txt_layf_balance_' + k + '=' + $('#txt_layf_balance_' + k).val() + '&txt_sizef_ratio_' + k + '=' + $('#txt_sizef_ratio_' + k).val() + '&txt_sizef_qty_' + k + '=' + $('#txt_sizef_qty_' + k).val() + '&hidden_sizef_id_' + k + '=' + $('#hidden_sizef_id_' + k).val() + '&txt_bundle_' + k + '=' + $('#txt_bundle_' + k).val() + '&txt_sizef_name_' + k + '=' + $('#txt_sizef_name_' + k).val();
				 var size_id = $("#hidden_sizef_id_" + k).val();
				 //size_arr[]=size_id;
				 size_arr.push(size_id);
			 }
 
			 var roll_row_num = $("#tbl_roll tbody tr").length;
			 for (var i = 1; i <= roll_row_num; i++) {
				 var barcode_no = 0;
				 var roll_no = $("#rollNo_" + i).val() * 1;
				 var roll_id = $("#rollId_" + i).val() * 1;
				 var roll_wgt = $("#rollWgt_" + i).val() * 1;
				 var plies = $("#piles_" + i).val() * 1;
 
				 if (roll_data == "") {
					 roll_data = barcode_no + "=" + roll_no + "=" + roll_id + "=" + roll_wgt + "=" + plies;
				 } else {
					 roll_data += "|" + barcode_no + "=" + roll_no + "=" + roll_id + "=" + roll_wgt + "=" + plies;
				 }
 
				 for (var z = 0; z < size_arr.length; z++) {
					 var size_id = size_arr[z];
					 var qty = $("#sqty_" + size_id + "_" + i).val();
					 roll_data += "=" + qty
				 }
			 }
 
			 size_data = size_data + "&size_row_num=" + size_row_num + "&max_seq=" + max_seq + "&roll_data=" + roll_data;
 
			 for (var k = 1; k <= row_num; k++) {
				 // data2+=get_submitted_data_string('cboCountryType_'+k+'*cboCountry_'+k+'*txt_lay_balance_'+k+'*txt_excess_'+k+'*txt_size_qty_'+k+'*hidden_size_id_'+k+'*update_size_id_'+k+'*poId_'+k,"../../../",2);
 
				 data2 += '&cboCountryType_' + k + '=' + $('#cboCountryType_' + k).val() + '&cboCountry_' + k + '=' + $('#cboCountry_' + k).val() + '&txt_lay_balance_' + k + '=' + $('#txt_lay_balance_' + k).val() + '&txt_excess_' + k + '=' + $('#txt_excess_' + k).val() + '&txt_size_qty_' + k + '=' + $('#txt_size_qty_' + k).val() + '&hidden_size_id_' + k + '=' + $('#hidden_size_id_' + k).val() + '&update_size_id_' + k + '=' + $('#update_size_id_' + k).val() + '&poId_' + k + '=' + $('#poId_' + k).val();
			 }
			 var data = data1 + data2 + size_data;
			 // alert(size_data);return;
			 // freeze_window(operation);
			 http.open("POST", "woven_cut_and_lay_ratio_wise_entry_v3_controller.php", true);
			 http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			 http.send(data);
			 http.onreadystatechange = fnc_cut_lay_size_info_reponse;
		 }
 
		 function fnc_cut_lay_size_info_reponse() {
			 if (http.readyState == 4) {
				 //release_freezing(); return;
				 //alert(http.responseText);
				 var reponse = trim(http.responseText).split('**');
				 if (reponse[0] == 0 || reponse[0] == 1) {
					 if (reponse[0] == 0) {
						 $('#msg_box_popp').fadeTo(100, 1, function() //start fading the messagebox
							 {
								 $('#msg_box_popp').html("Data Save Sussessfully").removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
							 });
					 } else if (reponse[0] == 1) {
						 $('#msg_box_popp').fadeTo(100, 1, function() //start fading the messagebox
							 {
								 $('#msg_box_popp').html("Data Update Sussessfully").removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
							 });
					 }
 
					 show_list_view(reponse[1] + '**' + reponse[2] + '**' + reponse[3] + '**' + reponse[7], 'show_bundle_list_view', 'search_div', 'woven_cut_and_lay_ratio_wise_entry_v3_controller', 'setFilterGrid("list_view",-1)');
					 var update_size_id = reponse[3].split('_');
					 $("#hidden_plant_qty").val(reponse[4]);
					 $("#hidden_total_marker").val(reponse[5]);
					 $("#hidden_lay_balance").val(reponse[6]);
 
					 if (reponse[7] == 1) {
						 var update_data = reponse[3].split(',');
						 var dtlsId_array = new Array();
						 for (var k = 0; k < update_data.length; k++) {
							 var datas = update_data[k].split("__");
							 var index = datas[1];
							 dtlsId_array[index] = datas[0] + "**" + datas[2];
						 }
 
						 var row_num = $('#tbl_size tbody tr').length;
						 for (var i = 1; i <= row_num; i++) {
							 var index = $("#hidden_size_id_" + i).val();
							 var dtls_id = '';
							 var sequence_no = '';
							 if (dtlsId_array[index]) {
								 var datas = dtlsId_array[index].split("**");
								 dtls_id = datas[0];
								 sequence_no = datas[1];
							 }
							 $('#update_size_id_' + i).val(dtls_id);
							 $('#txt_bundle_' + i).val(sequence_no);
						 }
					 } else {
						 for (var i = 1; i <= update_size_id.length; i++) {
							 $('#update_size_id_' + i).val(update_size_id[i - 1]);
						 }
					 }
					 set_button_status(1, permission, 'fnc_cut_lay_size_info', 1, 1);
				 } else if (reponse[0] == 15) {
					 alert("No Data Found");
				 } else if (reponse[0] == 200) {
					 alert("Update Restricted. This information found in Cutting Qc Page Which System Id " + reponse[3] + ".");
				 } else if (reponse[0] == 201) {
					 alert("Save Restricted. This information found in Cutting Qc Page Which System Id " + reponse[3] + ".");
				 }
				 release_freezing();
				 const btn = (reponse[0] == 0) ? document.getElementById('save1') : document.getElementById('update1');
				 btn.disabled = false;
			 }
		 }
 
 
 
 
		 function fnc_print_bundle(type) {
			 var report_title = "Cut and Lay bundle ";
			 var country = $('#cboCountryBundle').val();
			 print_report(<? echo $cbo_company_id; ?> + '*' + <? echo $mst_id; ?> + '*' + <? echo $details_id; ?> + '*' + report_title + '*' + country + '*' + type, "cut_lay_bundle_print", "woven_cut_and_lay_ratio_wise_entry_v3_controller")
		 }
 
 
 
		 function fnc_print_bundle_lay(action, type) {
 
			 var report_title = "Cut and Lay bundle ";
			 var country = $('#cboCountryBundle').val();
			 freeze_window();
 
 
			 var data = "data=" + <? echo $cbo_company_id; ?> + '*' + <? echo $mst_id; ?> + '*' + <? echo $details_id; ?> + '*' + report_title + '*' + country + '*' + type + '&action=' + action;
			 //alert(data); return;
			 http.open("POST", "woven_cut_and_lay_ratio_wise_entry_v3_controller.php", true);
			 http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			 http.send(data);
			 http.onreadystatechange = fnc_print_bundle_lay_reponse;
 
		 }
 
		 function fnc_print_bundle_lay_reponse() {
 
			 if (http.readyState == 4 && http.status == 200) {
				 var response = http.responseText;
				 var file_data = http.responseText.split("****");
				 //alert(file_data[2]);
				 $('#print_report4').removeAttr('href').attr('href', trim(file_data[0]));
				 document.getElementById('print_report4').click();
				 var w = window.open("Surprise", "#");
				 var d = w.document.open();
				 d.write(response);
				 d.close();
 
 
			 }
 
 
		 }
 
		 function fnc_print_bundle_lay__22212(type) {
			 var report_title = "Cut and Lay bundle ";
			 var country = $('#cboCountryBundle').val();
			 print_report(<? echo $cbo_company_id; ?> + '*' + <? echo $mst_id; ?> + '*' + <? echo $details_id; ?> + '*' + report_title + '*' + country + '*' + type, "lay_bundle_print", "woven_cut_and_lay_ratio_wise_entry_v3_controller")
		 }
 
		 function fnc_print_qc_bundle(type) {
			 var report_title = "Cut. Panel Inspection Report";
			 var country = $('#cboCountryBundle').val();
			 if (type == 1) {
				 print_report(<? echo $cbo_company_id; ?> + '*' + <? echo $mst_id; ?> + '*' + <? echo $details_id; ?> + '*' + report_title + '*' + country + '*' + type, "cut_lay_qc_bundle_print", "woven_cut_and_lay_ratio_wise_entry_v3_controller")
			 } else if (type == 2) {
				 print_report(<? echo $cbo_company_id; ?> + '*' + <? echo $mst_id; ?> + '*' + <? echo $details_id; ?> + '*' + report_title + '*' + country + '*' + type, "cut_lay_qc_bundle_print_2", "woven_cut_and_lay_ratio_wise_entry_v3_controller")
			 } else if (type == 3) {
				 print_report(<? echo $cbo_company_id; ?> + '*' + <? echo $mst_id; ?> + '*' + <? echo $details_id; ?> + '*' + report_title + '*' + country + '*' + type, "cut_lay_qc_bundle_print_3", "woven_cut_and_lay_ratio_wise_entry_v3_controller")
			 }
		 }
 
		 function sequence_duplication_check(row_id) {
			 var row_num = $('#tbl_size_details tbody tr').length;
			 var sequence_no = $('#txt_bundle_' + row_id).val();
 
			 if (sequence_no * 1 > 0) {
				 for (var j = 1; j <= row_num; j++) {
					 if (j == row_id) {
						 continue;
					 } else {
						 var sequence_no_check = $('#txt_bundle_' + j).val();
 
						 if (sequence_no == sequence_no_check) {
							 alert("Duplicate Sequence No.");
							 $('#txt_bundle_' + row_id).val('');
							 return;
						 }
					 }
				 }
			 }
		 }
 
		 function clear_size_form() {
			 $("#txt_bundle_pcs").val('');
			 var row_num = $('#tbl_size_details tbody tr').length;
			 for (var i = 1; i <= row_num; i++) {
				 $('#txt_size_qty_' + i).val('');
				 $('#txt_bundle_' + i).val('');
			 }
		 }
 
		 function size_popup_close(id, marker, plan, tomarker, lay_balance) {
			 var pass_string = id + "**" + marker + "**" + plan + "**" + tomarker + "**" + lay_balance;
 
			 document.getElementById('hidden_marker_no_x').value = pass_string;
			 parent.emailwindow.hide();
		 }
 
		 function check_all_report() {
			 $("input[name=chk_bundle]").each(function(index, element) {
 
				 if ($('#check_all').prop('checked') == true)
					 $(this).attr('checked', 'true');
				 else
					 $(this).removeAttr('checked');
			 });
		 }
 
		 function fnc_bundle_report(column_list) {
			 var data = "";
			 var error = 1;
			 $("input[name=chk_bundle]").each(function(index, element) {
				 if ($(this).prop('checked') == true) {
 
					 error = 0;
					 var idd = $(this).attr('id').split("_");
					 if (data == "") data = $('#hiddenid_' + idd[2]).val();
					 else data = data + "," + $('#hiddenid_' + idd[2]).val();
				 }
			 });
 
			 if (error == 1) {
				 alert('No data selected');
				 return;
			 }
			 var job_id = $("#hidden_update_job_id").val();
			 data = data + "***" + job_id + '***' + <? echo $mst_id; ?> + '***' + <? echo $details_id; ?> + '***' + <? echo $cbo_gmt_id; ?> + '***' + <? echo $cbo_color_id; ?> + '***' + <? echo $order_id; ?>;
 
			 if (column_list == 6) {
				 var url = return_ajax_request_value(data, "print_report_bundle_barcode", "woven_cut_and_lay_ratio_wise_entry_v3_controller");
				 window.open(url, "##");
			 } else {
				 var url = return_ajax_request_value(data, "print_report_bundle_barcode_eight", "woven_cut_and_lay_ratio_wise_entry_v3_controller");
				 window.open(url, "##");
			 }
		 }
 
		 function fnc_bundle_report_eight() {
			 var data = "";
			 var error = 1;
			 $("input[name=chk_bundle]").each(function(index, element) {
				 if ($(this).prop('checked') == true) {
 
					 error = 0;
					 var idd = $(this).attr('id').split("_");
					 if (data == "") data = $('#hiddenid_' + idd[2]).val();
					 else data = data + "," + $('#hiddenid_' + idd[2]).val();
				 }
			 });
 
			 if (error == 1) {
				 alert('No data selected');
				 return;
			 }
			 var job_id = $("#hidden_update_job_id").val();
			 data = data + "***" + job_id + '***' + <? echo $mst_id; ?> + '***' + <? echo $details_id; ?> + '***' + <? echo $cbo_gmt_id; ?> + '***' + <? echo $cbo_color_id; ?> + '***' + <? echo $order_id; ?>;
 
			 var title = 'Search Job No';
			 var page_link = 'woven_cut_and_lay_ratio_wise_entry_v3_controller.php?data=' + data + '&action=print_report_bundle_barcode_eight';
			 emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=250px,center=1,resize=0,scrolling=0', '../../');
			 emailwindow.onclose = function() {
				 var theform = this.contentDoc.forms[0];
				 var prodID = this.contentDoc.getElementById("txt_selected_id").value;
				 data = data + '***' + prodID;
				 var url = return_ajax_request_value(data, "print_barcode_eight", "woven_cut_and_lay_ratio_wise_entry_v3_controller");
				 window.open(url, "##");
			 }
 
			 //var url=return_ajax_request_value(data, "print_report_bundle_barcode", "woven_cut_and_lay_ratio_wise_entry_v3_controller");
			 //window.open(url,"##");
		 }
 
		 function fnc_bundle_report_one() {
			 var data = "";
			 var error = 1;
			 $("input[name=chk_bundle]").each(function(index, element) {
				 if ($(this).prop('checked') == true) {
					 error = 0;
					 var idd = $(this).attr('id').split("_");
					 if (data == "") data = $('#hiddenid_' + idd[2]).val();
					 else data = data + "," + $('#hiddenid_' + idd[2]).val();
				 }
			 });
 
			 if (error == 1) {
				 alert('No data selected');
				 return;
			 }
			 var job_id = $("#hidden_update_job_id").val();
			 data = data + "***" + job_id + '***' + <? echo $mst_id; ?> + '***' + <? echo $details_id; ?> + '***' + <? echo $cbo_gmt_id; ?> + '***' + <? echo $cbo_color_id; ?> + '***' + <? echo $order_id; ?>;
 
			 var title = 'Search Job No';
			 var page_link = 'woven_cut_and_lay_ratio_wise_entry_v3_controller.php?data=' + data + '&action=print_report_bundle_barcode_eight';
			 emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=250px,center=1,resize=0,scrolling=0', '../../');
			 emailwindow.onclose = function() {
				 var theform = this.contentDoc.forms[0];
				 var prodID = this.contentDoc.getElementById("txt_selected_id").value;
				 data = data + '***' + prodID;
				 //var url=return_ajax_request_value(data, "print_barcode_one_pdf", "woven_cut_and_lay_ratio_wise_entry_v3_controller");
				 //window.open(url,"##");	
				 window.open("woven_cut_and_lay_ratio_wise_entry_v3_controller.php?data=" + data + '&action=print_barcode_one', true);
			 }
 
			 //var url=return_ajax_request_value(data, "print_report_bundle_barcode", "woven_cut_and_lay_ratio_wise_entry_v3_controller");
			 //window.open(url,"##");
		 }
 
		 //fnc_bundle_report_one_urmi
 
		 function fnc_bundle_report_one_urmi(type) {
			 //alert(type);
			 var data = "";
			 var error = 1;
			 $("input[name=chk_bundle]").each(function(index, element) {
				 if ($(this).prop('checked') == true) {
 
					 error = 0;
					 var idd = $(this).attr('id').split("_");
					 if (data == "") data = $('#hiddenid_' + idd[2]).val();
					 else data = data + "," + $('#hiddenid_' + idd[2]).val();
				 }
			 });
 
			 if (error == 1) {
				 alert('No data selected');
				 return;
			 }
			 var job_id = $("#hidden_update_job_id").val();
			 var order_id = '<?= $order_id; ?>';
			 data = data + "***" + job_id + '***' + <?= $mst_id; ?> + '***' + <?= $details_id; ?> + '***' + <?= $cbo_gmt_id; ?> + '***' + <?= $cbo_color_id; ?> + '***' + order_id;
 
			 var title = 'Search Job No';
			 var page_link = 'woven_cut_and_lay_ratio_wise_entry_v3_controller.php?data=' + data + '&action=print_report_bundle_barcode_eight';
			 emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=250px,center=1,resize=0,scrolling=0', '../../');
			 emailwindow.onclose = function() {
 
				 var theform = this.contentDoc.forms[0];
				 var prodID = this.contentDoc.getElementById("txt_selected_id").value;
				 data = data + '***' + prodID + '***' + type;
 
				 if (type != 4 && type != 5 && type != 6) {
					 var url = return_ajax_request_value(data, "print_barcode_one_urmi", "woven_cut_and_lay_ratio_wise_entry_v3_controller");
					 // alert(url);return;
					 window.open(url, "##");
				 } else if (type == 5) {
					 var url = return_ajax_request_value(data, "print_barcode_one_urmi_eg", "woven_cut_and_lay_ratio_wise_entry_v3_controller");
					 window.open(url, "##");
				 } else if (type == 6) {
					 var url = return_ajax_request_value(data, "print_barcode_one_urmi_eg_a4", "woven_cut_and_lay_ratio_wise_entry_v3_controller");
					 window.open(url, "##");
				 } else {
					 var url = return_ajax_request_value(data, "print_barcode_one_urmi_v5", "woven_cut_and_lay_ratio_wise_entry_v3_controller");
					 // alert(url);return;
					 window.open(url, "##");
				 }
				 //window.open("woven_cut_and_lay_ratio_wise_entry_v3_controller.php?data=" + data+'&action=print_barcode_one_urmi', true );
			 }
 
			 //var url=return_ajax_request_value(data, "print_report_bundle_barcode", "woven_cut_and_lay_ratio_wise_entry_v3_controller");
			 //window.open(url,"##");
		 }
 
		 function fnc_bundle_report_128() {
			 var data = "";
			 var error = 1;
			 $("input[name=chk_bundle]").each(function(index, element) {
				 if ($(this).prop('checked') == true) {
 
					 error = 0;
					 var idd = $(this).attr('id').split("_");
					 if (data == "") data = $('#hiddenid_' + idd[2]).val();
					 else data = data + "," + $('#hiddenid_' + idd[2]).val();
				 }
			 });
 
			 if (error == 1) {
				 alert('No data selected');
				 return;
			 }
			 var job_id = $("#hidden_update_job_id").val();
			 var order_id = '<? echo $order_id; ?>';
			 data = data + "***" + job_id + '***' + <? echo $mst_id; ?> + '***' + <? echo $details_id; ?> + '***' + <? echo $cbo_gmt_id; ?> + '***' + <? echo $cbo_color_id; ?> + '***' + order_id;
 
			 var title = 'Search Job No';
			 var page_link = 'woven_cut_and_lay_ratio_wise_entry_v3_controller.php?data=' + data + '&action=print_report_bundle_barcode_eight';
			 emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=250px,center=1,resize=0,scrolling=0', '../../');
			 emailwindow.onclose = function() {
				 var theform = this.contentDoc.forms[0];
				 var prodID = this.contentDoc.getElementById("txt_selected_id").value;
				 data = data + '***' + prodID;
				 var url = return_ajax_request_value(data, "print_barcode_one_128", "woven_cut_and_lay_ratio_wise_entry_v3_controller");
				 window.open(url, "##");
				 //window.open("woven_cut_and_lay_ratio_wise_entry_v3_controller.php?data=" + data+'&action=print_barcode_one_urmi', true );
			 }
 
			 //var url=return_ajax_request_value(data, "print_report_bundle_barcode", "woven_cut_and_lay_ratio_wise_entry_v3_controller");
			 //window.open(url,"##");
		 }
 
		 function fnc_bundle_report_129() {
			 var data = "";
			 var error = 1;
			 $("input[name=chk_bundle]").each(function(index, element) {
				 if ($(this).prop('checked') == true) {
 
					 error = 0;
					 var idd = $(this).attr('id').split("_");
					 if (data == "") data = $('#hiddenid_' + idd[2]).val();
					 else data = data + "," + $('#hiddenid_' + idd[2]).val();
				 }
			 });
 
			 if (error == 1) {
				 alert('No data selected');
				 return;
			 }
			 var job_id = $("#hidden_update_job_id").val();
			 var order_id = '<? echo $order_id; ?>';
			 data = data + "***" + job_id + '***' + <? echo $mst_id; ?> + '***' + <? echo $details_id; ?> + '***' + <? echo $cbo_gmt_id; ?> + '***' + <? echo $cbo_color_id; ?> + '***' + order_id;
			 var title = 'Search Job No';
			 var page_link = 'woven_cut_and_lay_ratio_wise_entry_v3_controller.php?data=' + data + '&action=print_report_bundle_barcode_eight';
			 emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=250px,center=1,resize=0,scrolling=0', '../../');
 
			 emailwindow.onclose = function() {
				 var theform = this.contentDoc.forms[0];
				 var prodID = this.contentDoc.getElementById("txt_selected_id").value;
				 data = data + '***' + prodID;
				 var url = return_ajax_request_value(data, "print_barcode_one_129", "woven_cut_and_lay_ratio_wise_entry_v3_controller");
				 window.open(url, "##");
				 //window.open("woven_cut_and_lay_ratio_wise_entry_v3_controller.php?data=" + data+'&action=print_barcode_one_urmi', true );
			 }
 
			 //var url=return_ajax_request_value(data, "print_report_bundle_barcode", "woven_cut_and_lay_ratio_wise_entry_v3_controller");
			 //window.open(url,"##");
		 }
 
		 function fnc_bundle_report_qrcode(work_comp, work_location) {
			 //alert(work_comp,work_location);
			 var report_title = "Cut and Lay bundle ";
			 var country = $('#cboCountryBundle').val();
			 var data = <?php echo $cbo_company_id; ?>;
			 var title = 'Search Job No';
			 var page_link = 'woven_cut_and_lay_ratio_wise_entry_v3_controller.php?data=' + data + '&action=print_report_bundle_list_popup';
			 emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=250px,center=1,resize=0,scrolling=0', '../../');
			 emailwindow.onclose = function() {
				 var theform = this.contentDoc.forms[0];
				 //var bundle_use_for=this.contentDoc.getElementById("txt_selected").value;
				 var prodID = this.contentDoc.getElementById("txt_selected_id").value;
				 fnc_bundle_display_qrccode(prodID);
 
				 //print_report(<? //echo $cbo_company_id; 
								 ?>+'*'+<? // echo $mst_id; 
																	 ?>+'*'+<? //echo $details_id; 
																							 ?>+'*'+report_title+'*'+country+'*'+bundle_use_for, "cut_lay_bundle_print", "woven_cut_and_lay_ratio_wise_entry_v3_controller")
			 }
		 }
 
 
		 function fnc_bundle_display_qrccode(bundle_use_for) {
			 var data = "";
			 var error = 1;
			 $("input[name=chk_bundle]").each(function(index, element) {
				 if ($(this).prop('checked') == true) {
 
					 error = 0;
					 var idd = $(this).attr('id').split("_");
					 if (data == "") data = $('#hiddenid_' + idd[2]).val();
					 else data = data + "," + $('#hiddenid_' + idd[2]).val();
				 }
			 });
 
			 if (error == 1) {
				 alert('No data selected');
				 return;
			 }
			 var job_id = $("#hidden_update_job_id").val();
			 var order_id = '<? echo $order_id; ?>';
			 data = data + "***" + job_id + '***' + <? echo $mst_id; ?> + '***' + <? echo $details_id; ?> + '***' + <? echo $cbo_gmt_id; ?> + '***' + <? echo $cbo_color_id; ?> + '***' + order_id + '***' + bundle_use_for;
			 data = data;
			 //window.open("yarn_lot_ratio_planning_controller.php?data=" + data+'&action=print_barcode_fivePointEight', true );//var data = $("#cbo_company_id").val()+"__"+$("#txt_update_id").val()*1+"__"+$("#txt_system_id").val();
 
			 http.open('POST', 'woven_cut_and_lay_ratio_wise_entry_v3_controller.php?action=print_qrcode&data=' + data);
			 http.onreadystatechange = response_pdf_data;
			 http.send(null);
 
			 // }	
		 }
 
		 function response_pdf_data() {
			 if (http.readyState == 4) {
				 //alert(response[1]);
				 var response = http.responseText.split('###');
				 window.open('' + response[1], '', '');
			 }
		 }
 
 
		 function fnc_send_printer_text() {
			 var data = "";
			 var error = 1;
			 $("input[name=chk_bundle]").each(function(index, element) {
				 if ($(this).prop('checked') == true) {
 
					 error = 0;
					 var idd = $(this).attr('id').split("_");
					 if (data == "") data = $('#hiddenid_' + idd[2]).val();
					 else data = data + "," + $('#hiddenid_' + idd[2]).val();
				 }
			 });
 
			 if (error == 1) {
				 alert('No data selected');
				 return;
			 }
			 var job_id = $("#hidden_update_job_id").val();
			 data = data + "***" + job_id + '***' + <? echo $mst_id; ?> + '***' + <? echo $details_id; ?> + '***' + <? echo $cbo_gmt_id; ?> + '***' + <? echo $cbo_color_id; ?> + '***' + <? echo $order_id; ?>;
			 var url = return_ajax_request_value(data, "report_bundle_text_file", "woven_cut_and_lay_ratio_wise_entry_v3_controller");
 
			 window.open(url + ".zip", "##");
 
 
		 }
 
		 function fnc_addRow(actual_id, i) {
			 var row_num = $('#trBundleListSave tr').length;
			 row_num++;
			 var clone = $("#trBundleListSave_" + actual_id).clone();
			 clone.attr({
				 id: "trBundleListSave_" + row_num,
			 });
 
			 clone.find("input,select").each(function() {
 
				 $(this).attr({
					 'id': function(_, id) {
						 var id = id.split("_");
						 return id[0] + "_" + row_num
					 },
					 'name': function(_, name) {
						 return name
					 },
					 'value': function(_, value) {
						 return value
					 }
				 });
 
			 }).end();
 
			 $("#trBundleListSave_" + i).after(clone);
			 $('#addButton_' + actual_id).removeAttr("onclick").attr("onclick", "fnc_addRow(" + actual_id + "," + row_num + ");");
			 $('#bundleSizeQty_' + row_num).removeAttr("onKeyUp").attr("onKeyUp", "bundle_calclution(" + row_num + ");");
 
			 //=================================================================================================================
			 $('#addButton_' + row_num).removeAttr("onclick").attr("onclick", "delete_bundle_row(" + actual_id + "," + row_num + ");");
			 $("#addButton_" + row_num).val('-');
			 //===================================================================================================================
			 $("#hiddenExtraTr_" + actual_id).val($("#hiddenExtraTr_" + actual_id).val() + "**" + row_num);
			 $("#bundleSizeQty_" + actual_id).attr("disabled", false);
			 $("#bundleSizeQty_" + row_num).attr("disabled", false);
			 $("#bundleNo_" + row_num).attr("disabled", false);
			 $("#bundleSizeQty_" + row_num).val('');
			 $("#serialNo_" + row_num).html('');
			 $("#bundleNo_" + row_num).val($("#bundleNo_" + actual_id).val() + "-");
			 $("#rmgNoStart_" + row_num).val('');
			 $("#rmgNoEnd_" + row_num).val('');
			 $("#hiddenUpdateValue_" + row_num).val('');
			 $("#hiddenUpdateFlag_" + actual_id).val(6);
			 $("#hiddenUpdateFlag_" + row_num).val(6);
			 serial_rearrange();
		 }
 
		 function delete_bundle_row(actual_id, rowNo) {
			 var total_add_id = $("#hiddenExtraTr_" + actual_id).val();
			 var countryId = $("#hiddenCountryB_" + rowNo).val();
			 var sizeId = $("#hiddenSizeId_" + rowNo).val();
			 var pattern = $("#patternNo_" + rowNo).val();
			 var rollId = $("#rollId_" + rowNo).val();
			 // alert(total_add_id);
			 var id_arr = total_add_id.split("**")
 
			 id_arr.splice(id_arr.indexOf(rowNo), 1);
			 // alert(id_arr.length)
			 if (id_arr.length == 1) $('#addButton_' + actual_id).removeAttr("onclick").attr("onclick", "fnc_addRow(" + actual_id + "," + actual_id + ");");
			 var new_id = id_arr.join("**");
			 $("#hiddenExtraTr_" + actual_id).val(new_id);
			 //alert( $("#hiddenExtraTr_"+actual_id).val())
			 $("#trBundleListSave_" + rowNo).remove();
			 bundle_calclution_on_dlt(countryId, sizeId, pattern, rollId);
			 serial_rearrange();
		 }
 
		 function bundle_calclution_on_dlt(countryId, sizeId, pattern, rollId) {
			 var min_rmg_no = 1;
			 $("#tbl_bundle_list_save").find('tbody tr').each(function() {
				 var qty = $(this).find('input[name="bundleSizeQty[]"]').val() * 1;
 
				 var countryIdC = parseInt($(this).find('input[name="hiddenCountryB[]"]').val());
				 var sizeIdC = parseInt($(this).find('input[name="hiddenSizeId[]"]').val());
				 var patternNoC = trim($(this).find('input[name="patternNo[]"]').val());
				 var rollIdC = parseInt($(this).find('input[name="rollId[]"]').val());
 
				 if (countryId == countryIdC && sizeId == sizeIdC && pattern == patternNoC && rollId == rollIdC) {
					 if (qty * 1 > 0) {
						 var from = min_rmg_no;
						 var to = min_rmg_no * 1 + qty * 1 - 1;
						 min_rmg_no += qty * 1;
						 $(this).find('input[name="rmgNoStart[]"]').val(from);
						 $(this).find('input[name="rmgNoEnd[]"]').val(to);
					 } else {
						 $(this).find('input[name="rmgNoStart[]"]').val('');
						 $(this).find('input[name="rmgNoEnd[]"]').val('');
					 }
				 }
			 });
		 }
 
		 function serial_rearrange() {
			 var k = 1;
			 $("#tbl_bundle_list_save").find('tbody tr').each(function() {
				 $(this).find('input[name="sirialNo[]"]').val(k);
				 //alert(k)
				 k++;
 
			 });
		 }
 
		 function fnc_updateRow(id_row) {
			 $("#bundleSizeQty_" + id_row).attr("disabled", false);
			 //$("#sizeName_"+id_row).attr("disabled",false);
			 //$("#cboCountryB_"+id_row).removeAttr("disabled","disabled");
			 $("#hiddenUpdateFlag_" + id_row).val(6);
		 }
 
		 function fnc_rearrange_rmg(id_num) {
			 var s = 0;
			 var first_rmg = $("#rmgNoStart_1").val();
			 var last_rmg = 0;
			 var bundle_qty = 0;
			 $("#tbl_bundle_list_save").find('tbody tr').each(function() {
				 bundle_qty = $(this).find('input[name="bundleSizeQty[]"]').val();
 
				 if (s == 0) {
					 $(this).find('input[name="rmgNoEnd[]"]').val(parseInt(bundle_qty) + parseInt(first_rmg) - 1);
					 last_rmg = parseInt(bundle_qty) + parseInt(first_rmg) - 1;
				 } else {
					 $(this).find('input[name="rmgNoStart[]"]').val(parseInt(last_rmg) + 1);
					 last_rmg = parseInt(last_rmg) + parseInt(bundle_qty);
					 $(this).find('input[name="rmgNoEnd[]"]').val(parseInt(last_rmg));
				 }
				 s++;
			 });
		 }
 
		 //function bundle_calclution(actual_id,row_id)
		 function bundle_calclution(rowNo) {
			 var countryId = $("#hiddenCountryB_" + rowNo).val();
			 var sizeId = $("#hiddenSizeId_" + rowNo).val();
			 var pattern = $("#patternNo_" + rowNo).val();
			 var rollId = $("#rollId_" + rowNo).val();
 
			 var min_rmg_no = 1;
			 //var min_rmg_no=$("#rmgNoStart_"+rowNo).val()*1;
			 $("#tbl_bundle_list_save").find('tbody tr').each(function() {
				 var qty = $(this).find('input[name="bundleSizeQty[]"]').val() * 1;
 
				 var countryIdC = parseInt($(this).find('input[name="hiddenCountryB[]"]').val());
				 var sizeIdC = parseInt($(this).find('input[name="hiddenSizeId[]"]').val());
				 var patternNoC = trim($(this).find('input[name="patternNo[]"]').val());
				 //var rollIdC=parseInt($(this).find('input[name="rollId[]"]').val()); && rollId==rollIdC
 
				 if (rmg_no_creation == 1) // size wise
				 {
					 if (sizeId == sizeIdC) {
						 if (qty * 1 > 0) {
							 var from = min_rmg_no;
							 var to = min_rmg_no * 1 + qty * 1 - 1;
							 min_rmg_no += qty * 1;
							 $(this).find('input[name="rmgNoStart[]"]').val(from);
							 $(this).find('input[name="rmgNoEnd[]"]').val(to);
						 } else {
							 $(this).find('input[name="rmgNoStart[]"]').val('');
							 $(this).find('input[name="rmgNoEnd[]"]').val('');
						 }
					 }
				 } else if (rmg_no_creation == 5) // size and pattern wise
				 {
					 if (sizeId == sizeIdC && pattern == patternNoC) {
						 if (qty * 1 > 0) {
							 var from = min_rmg_no;
							 var to = min_rmg_no * 1 + qty * 1 - 1;
							 min_rmg_no += qty * 1;
							 $(this).find('input[name="rmgNoStart[]"]').val(from);
							 $(this).find('input[name="rmgNoEnd[]"]').val(to);
						 } else {
							 $(this).find('input[name="rmgNoStart[]"]').val('');
							 $(this).find('input[name="rmgNoEnd[]"]').val('');
						 }
					 }
				 } else {
					 // if(countryId==countryIdC && sizeId==sizeIdC && pattern==patternNoC)
					 // {
					 if (qty * 1 > 0) {
						 var from = min_rmg_no;
						 var to = min_rmg_no * 1 + qty * 1 - 1;
						 min_rmg_no += qty * 1;
						 $(this).find('input[name="rmgNoStart[]"]').val(from);
						 $(this).find('input[name="rmgNoEnd[]"]').val(to);
					 } else {
						 $(this).find('input[name="rmgNoStart[]"]').val('');
						 $(this).find('input[name="rmgNoEnd[]"]').val('');
					 }
					 // }
				 }
 
 
			 });
 
			 /*$("#tbl_bundle_list_save").find('tbody tr').each(function()
			 {
				 var qty=$(this).find('input[name="bundleSizeQty[]"]').val()*1;
				 var from=min_rmg_no;
				 var to=min_rmg_no*1+qty*1-1;
				 min_rmg_no+=qty*1;
				 
				 $(this).find('input[name="rmgNoStart[]"]').val(from);
				 $(this).find('input[name="rmgNoEnd[]"]').val(to);
			 });*/
 
		 }
		 //**********************************************bundle update *****************************************************************************************
 
		 function fnc_cut_lay_bundle_info(operation) {
			 var cbo_color_type = <? echo $cbo_color_type; ?>;
 
			 if (operation == 2) {
				 show_msg('13');
				 return;
			 }
			 var dataString_bundle = "";
			 var j = 0;
			 var z = 0;
			 var tot_row = 0;
			 var sl = 0;
			 var error = 0;
			 var bundle_check_arr = new Array();
			 $("#tbl_bundle_list_save").find('tbody tr').each(function() {
				 var bundle_break = ($(this).find('input[name="bundleNo[]"]').val()).split('-');
				 var bundle_no_split_length = bundle_break.length;
				 if (bundle_no_split_length > 3) {
					 var check_bundle_prifix = bundle_no_split_length - 1;
					 if (bundle_break[check_bundle_prifix] == "") {
						 $(this).find('input[name="bundleNo[]"]').css({
							 "background-color": "red"
						 });
						 error = 1;
					 }
				 }
 
 
				 if (jQuery.inArray($(this).find('input[name="bundleNo[]"]').val(), bundle_check_arr) > -1) {
					 alert('Duplicate Bundle. Bundle No ' + $(this).find('input[name="bundleNo[]"]').val());
					 error = 1;
					 return;
				 }
 
 
				 bundle_check_arr.push($(this).find('input[name="bundleNo[]"]').val());
 
 
 
 
				 //bundle_check_arr[$(this).find('input[name="bundleNo[]"]').val()]=$(this).find('input[name="bundleNo[]"]').val();
				 /*var bundle_no=($(this).find('input[name="bundleNo[]"]').val()).match("/");
					 if(bundle_no=="/")
					 {
						  var bundle_break=($(this).find('input[name="bundleNo[]"]').val()).split('/');
						  if(bundle_break[1]=="")
						  {
						   $(this).find('input[name="bundleNo[]"]').css({"background-color":"red"});
						   error=1;
						  }
					 }*/
				 sl++;
			 });
			 if (error == 1) {
				 return;
			 }
 
			 $("#tbl_bundle_list_save").find('tbody tr').each(function() {
				 var bundle_no = $(this).find('input[name="bundleNo[]"]').val();
				 var bundle_size_qty = $(this).find('input[name="bundleSizeQty[]"]').val();
				 var bundle_from = $(this).find('input[name="rmgNoStart[]"]').val();
				 var bundle_to = $(this).find('input[name="rmgNoEnd[]"]').val();
				 var bundle_size_id = $(this).find('select[name="sizeName[]"]').val();
				 var hidden_size_id = $(this).find('input[name="hiddenSizeId[]"]').val();
				 var hidden_size_qty = $(this).find('input[name="hiddenSizeQty[]"]').val();
				 var hidden_update_flag = $(this).find('input[name="hiddenUpdateFlag[]"]').val();
				 var hiddenUpdateValue = $(this).find('input[name="hiddenUpdateValue[]"]').val();
				 var rollNo = $(this).find('input[name="rollNo[]"]').val();
				 var rollId = $(this).find('input[name="rollId[]"]').val();
				 var patternNo = $(this).find('input[name="patternNo[]"]').val();
				 var isExcess = $(this).find('input[name="isExcess[]"]').val();
 
				 var hiddenCountryType = $(this).find('input[name="hiddenCountryTypeB[]"]').val();
				 var cboCountry = $(this).find('select[name="cboCountryB[]"]').val();
				 var po_id = $(this).find('select[name="cboPoId[]"]').val();
				 var hiddenCountry = $(this).find('input[name="hiddenCountryB[]"]').val();
 
				 j++;
				 tot_row++;
				 dataString_bundle += '&txtBundleNo_' + j + '=' + bundle_no + '&txtBundleQty_' + j + '=' + bundle_size_qty + '&txtBundleFrom_' + j + '=' + bundle_from + '&txtBundleTo_' + j + '=' + bundle_to + '&txtSizeId_' + j + '=' + bundle_size_id + '&txtHiddenSizeId_' + j + '=' + hidden_size_id + '&hiddenSizeqty_' + j + '=' + hidden_size_qty + '&hiddenUpdateFlag_' + j + '=' + hidden_update_flag + '&hiddenUpdateValue_' + j + '=' + hiddenUpdateValue + '&hiddenCountryType_' + j + '=' + hiddenCountryType + '&hiddenCountry_' + j + '=' + hiddenCountry + '&cboCountry_' + j + '=' + cboCountry + '&rollNo_' + j + '=' + rollNo + '&rollId_' + j + '=' + rollId + '&patternNo_' + j + '=' + patternNo + '&isExcess_' + j + '=' + isExcess + '&cboPoId_' + j + '=' + po_id;
			 });
 
 
			 var bundle_mst_id = $("#hidden_mst_id").val();
			 var bundle_dtls_id = $("#hidden_detls_id").val();
			 //alert(bundle_dtls_id);return;
			 var hidden_cutting_no = $("#hidden_cutting_no").val();
			 var data = "action=save_update_delete_bundle&operation=" + operation + '&tot_row=' + tot_row + '&bundle_dtls_id=' + bundle_dtls_id + '&bundle_mst_id=' + bundle_mst_id + dataString_bundle + '&hidden_cutting_no=' + hidden_cutting_no + '&color_type_id=' + cbo_color_type;
			 //alert(data);return;hidden_cutting_no
			 freeze_window(operation);
			 http.open("POST", "woven_cut_and_lay_ratio_wise_entry_v3_controller.php", true);
			 http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			 http.send(data);
			 http.onreadystatechange = fnc_cut_lay_bundle_reply_info;
		 }
 
		 function fnc_cut_lay_bundle_reply_info() {
			 if (http.readyState == 4) {
				 var reponse = trim(http.responseText).split('**');
 
				 show_msg(trim(reponse[0]));
 
				 if ((reponse[0] == 0 || reponse[0] == 1)) {
					 $('#msg_box_popp').fadeTo(100, 1, function() //start fading the messagebox
						 {
							 $('#msg_box_popp').html("Data Update  Sussessfully").removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
						 });
					 set_button_status(1, permission, 'fnc_cut_lay_bundle_info', 2);
					 show_list_view(reponse[1] + '**' + reponse[2] + '**' + reponse[3], 'show_bundle_list_view', 'search_div', 'woven_cut_and_lay_ratio_wise_entry_v3_controller', 'setFilterGrid("list_view",-1)');
				 } else if (reponse[0] == 200) {
					 alert("Update Restricted.This information found in Cutting Qc Page Which System Id " + reponse[1] + ".");
				 }
				 release_freezing();
			 }
		 }
 
		 function fnc_rollWiseSizeQty() {
			 var size_row_num = $('#tbl_size tbody tr').length;
			 var size_data = '';
			 for (var k = 1; k <= size_row_num; k++) {
				 var hidden_sizef_id = $("#hidden_sizef_id_" + k).val();
				 var txt_sizef_ratio = $("#txt_sizef_ratio_" + k).val();
 
				 if (size_data == "") {
					 size_data = hidden_sizef_id + "_" + txt_sizef_ratio;
				 } else {
					 size_data += "|" + hidden_sizef_id + "_" + txt_sizef_ratio;
				 }
			 }
 
			 emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'woven_cut_and_lay_ratio_wise_entry_v3_controller.php?rollData=' + '<? echo $rollData; ?>' + '&size_data=' + size_data + '&action=rollSize_popup', 'Roll Popup', 'width=680px,height=300px,center=1,resize=1,scrolling=0', '../../')
			 emailwindow.onclose = function() {
				 var theform = this.contentDoc.forms[0] //("search_order_frm"); //Access the form inside the modal window
				 var roll_data = this.contentDoc.getElementById("hidden_roll_data").value; //Barcode Nos
				 $("#roll_data").val(roll_data);
			 }
		 }
 
		 function fnc_printBtnShowHide() {
			 var report_id = report_ids.split(",");
			 for (var k = 0; k < report_id.length; k++) {
				 if (report_id[k] == 294) $("#btn1").show();
				 if (report_id[k] == 295) $("#btn2").show();
				 if (report_id[k] == 296) $("#btn3").show();
				 if (report_id[k] == 297) $("#btn4").show();
				 if (report_id[k] == 298) $("#btn5").show();
				 if (report_id[k] == 299) $("#btn6").show();
				 if (report_id[k] == 300) $("#btn7").show();
				 if (report_id[k] == 301) $("#btn8").show();
				 if (report_id[k] == 302) $("#btn9").show();
				 if (report_id[k] == 303) $("#btn10").show();
				 if (report_id[k] == 714) $("#btn12").show();
				 if (report_id[k] == 722) $("#btn13").show();
				 if (report_id[k] == 366) $("#btn11").show();
				 if (report_id[k] == 367) $("#btn14").show();
				 if (report_id[k] == 371) $("#btn15").show();
				 if (report_id[k] == 431) $("#btn16").show();
				 if (report_id[k] == 434) $("#btn17").show();
				 if (report_id[k] == 439) $("#btn18").show();
				 if (report_id[k] == 442) $("#btn19").show();
				 if (report_id[k] == 827) $("#btn20").show();
				 if (report_id[k] == 828) $("#btn22").show();
				 if (report_id[k] == 838) $("#btn9v2").show();
			 }
		 }
 
		 function fnc_bundle_report_qrcode_v2(type) 
		 {
			 //alert(type);
			 var data = "";
			 var error = 1;
			 $("input[name=chk_bundle]").each(function(index, element) 
			 {
				 if ($(this).prop('checked') == true) {
					 error = 0;
					 var idd = $(this).attr('id').split("_");
					 if (data == "") data = $('#hiddenid_' + idd[2]).val();
					 else data = data + "," + $('#hiddenid_' + idd[2]).val();
				 }
			 });
 
			 if (error == 1) 
			 {
				 alert('No data selected');
				 return;
			 }
			 var job_id = $("#hidden_update_job_id").val();
			 var order_id = '<? echo $order_id; ?>';
			 data = data + "***" + job_id + '***' + <? echo $mst_id; ?> + '***' + <? echo $details_id; ?> + '***' + <? echo $cbo_gmt_id; ?> + '***' + <? echo $cbo_color_id; ?> + '***' + order_id;
 
			 var title = 'Search Job No';
 
			 var page_link = 'woven_cut_and_lay_ratio_wise_entry_v3_controller.php?data=' + data + '&action=print_report_bundle_barcode_eight';
			 emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=250px,center=1,resize=0,scrolling=0', '../../');
			 emailwindow.onclose = function() 
			 {
				 var theform = this.contentDoc.forms[0];
				 var prodID = this.contentDoc.getElementById("txt_selected_id").value;
				 data = data + '***' + prodID;
 
				 if (type == 1) 
				 {
					 var action = "print_qrcode_operation_69_38";
				 }
 
				 //var url=return_ajax_request_value(data, "print_barcode_operation", "cut_and_lay_ratio_wise_entry_controller_urmi");
				 http.open('POST', 'woven_cut_and_lay_ratio_wise_entry_v3_controller.php?action=' + action + '&data=' + data);
 
				 http.onreadystatechange = response_pdf_data;
				 http.send(null);
			 }
		 }
 
	 </script>
 
	 </head>
 
	 <body onLoad="set_hotkey()">
		 <div id="msg_box_popp" style=" height:15px; width:200px;  position:relative; left:250px "></div>
		 <div align="center" style="width:100%; overflow-y:hidden; position:absolute; top:5px;">
			 <input type="hidden" id="hidden_cutting_no" name="hidden_cutting_no" value="<? echo $cutting_no; ?>" />
			 <div style="display:none"><?= load_freeze_divs("../../../", $permission); ?></div>
			 <?
			 //$color_name_arr = return_field_value("color_name", "lib_color", "id='" . $cbo_color_id . "'");
			 $color_name_arr = return_library_array("select id, color_name from lib_color where id in ( $cbo_color_id)", 'id', 'color_name');
			//  $color_id_arr = return_library_array("select id, id from lib_color where id in ( $cbo_color_id)", 'id', 'id');
			//  $color_name=implode(expolde(',',($color_id_arr)));
			//$bodypart_id = array_unique(explode(",", $cbo_color_id));
			// print_r($bodypart_id);die;
			 $country_arr = return_library_array("select id, country_name from lib_country", 'id', 'country_name');
			 $pcs_per_bundle = return_field_value("pcs_per_bundle", "ppl_cut_lay_dtls", "id=$details_id ", "pcs_per_bundle");
			 ?>
			 <form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				 <fieldset style="width:450px;">
					 <table cellpadding="0" cellspacing="0" width="450" class="" id="tbl_bundle_size">
						 <thead>
							 <tr>
								 <!-- <td><strong>Color</strong></td> -->
								 <!-- <td>
									 <input type="text" style="width:80px" class="text_boxes" name="txt_show_color" id="txt_show_color" value="<? echo $color_name; ?>" disabled readonly />
									 <input type="hidden" id="hidden_update_job_id" name="hidden_update_job_id" value="<? echo $job_id; ?>" />
									 <input type="hidden" id="hidden_update_cut_no" name="hidden_update_cut_no" value="<? echo $cutting_no; ?>" />
								 </td> -->
								 <td><strong>Plies</strong></td>
								 <td>
									 <input type="text" style="width:80px" class="text_boxes" name="txt_search_common" id="txt_search_common" value="<?php echo $txt_piles; ?>" disabled readonly />
									 <input type="hidden" id="hidden_update_job_id" name="hidden_update_job_id" value="<? echo $job_id; ?>" />
									 <input type="hidden" id="hidden_update_cut_no" name="hidden_update_cut_no" value="<? echo $cutting_no; ?>" />
								 </td>
								 <td class="must_entry_caption"><strong>Pcs Per Bundle</strong></td>
								 <td><input type="text" style="width:50px" class="text_boxes_numeric" name="txt_bundle_pcs" id="txt_bundle_pcs" value="<? echo $pcs_per_bundle; ?>" /></td>
							 </tr>
						 </thead>
					 </table>
				 </fieldset>
				 <br />
				 <fieldset style="width:905px;">
					 <?
						 
						 
						 $master_contry_cond="";
						 $size_contry_cond="";
						 if($country_id) $master_contry_cond=" and country_id in (".$country_id.")";
						 if($country_id) $master_contryseq_cond=" and a.country_id in (".$country_id.")";
						 if($country_id) $size_contry_cond=" and a.country_id in (".$country_id.")";
					 $po_no_arr = return_library_array("select id, po_number from wo_po_break_down where id in($order_id)", 'id', 'po_number');
					 $po_country_array = array();
					 $size_order_arr = array();
					 $poArr = array();
					 if($hiddiscountryseq==1 || $hiddisorderseq==1)
					 {
						 $seq_tbl = ($hiddisorderseq==1) ? "ppl_cut_lay_order_seq_dtls" : "ppl_cut_lay_country_seq_dtls";// if both variable is yes then use order seq table
						 $sql_cond = ($hiddisorderseq==1) ? "and a.po_break_down_id=b.order_id" : "and a.country_id=b.country_id";
						 $sql_query=sql_select("SELECT a.country_type, a.country_id, a.size_number_id, a.plan_cut_qnty, a.country_ship_date, a.size_order,a.po_break_down_id, b.sequence_no from wo_po_color_size_breakdown a, $seq_tbl b where a.item_number_id=$cbo_gmt_id and a.po_break_down_id in($order_id) and a.color_number_id in($cbo_color_id)  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $master_contryseq_cond and b.dtls_id=$details_id $sql_cond group by  a.country_type, a.country_id, a.size_number_id, a.plan_cut_qnty, a.country_ship_date, a.size_order,a.po_break_down_id, b.sequence_no, a.id order by b.sequence_no, a.size_order");
					 }
					 else
					 {
						 $sql_query=sql_select("SELECT country_type, country_id, po_break_down_id,size_number_id,color_number_id, plan_cut_qnty, country_ship_date, size_order, 0 as sequence_no from wo_po_color_size_breakdown where item_number_id=$cbo_gmt_id and po_break_down_id in($order_id) and color_number_id in ($cbo_color_id) and status_active=1 and is_deleted=0 $master_contry_cond order by size_order, country_ship_date, country_type");
					 }
					// echo $sql_query;die;
					 // $sql_query = sql_select("SELECT po_break_down_id, country_type, country_id, size_number_id, plan_cut_qnty, country_ship_date, size_order from wo_po_color_size_breakdown where item_number_id=$cbo_gmt_id and po_break_down_id in($order_id) and color_number_id=$cbo_color_id $country_con and status_active=1 and is_deleted=0 order by size_order,country_ship_date, country_type");
					 //echo "select country_type, country_id, size_number_id, plan_cut_qnty, country_ship_date, size_order from wo_po_color_size_breakdown where item_number_id=$cbo_gmt_id and po_break_down_id=$order_id and color_number_id=$cbo_color_id and status_active=1 and is_deleted=0 order by size_order, country_ship_date, country_type, id";
					 $size_details = array();
					 $sizeId_arr = array();
					 $shipDate_arr = array();
					 $distributed_qty_arr = array();
					 $planQty = 0;
					 foreach ($sql_query as $row) {
						 //if($row[csf('country_type')]==1) $country_id=0; else $country_id=$row[csf('country_id')];
						 $po_id = $row[csf('po_break_down_id')];
						 $country_id = $row[csf('country_id')];
						 if ($row[csf('country_type')] == "") {
							 $row[csf('country_type')] = 1;
						 }
						 if ($row[csf('sequence_no')] == "") {
							 $row[csf('sequence_no')] = 1;
						 }
						 $size_details[$row[csf('sequence_no')]][$po_id][$row[csf('country_type')]][$country_id][$row[csf('color_number_id')]][$row[csf('size_number_id')]] += $row[csf("plan_cut_qnty")];
						 
						 $sizeId_arr[$row[csf('size_number_id')]] += $row[csf("plan_cut_qnty")];
						//  $sizeId_arr[$row[csf('color_number_id')]]['color_number_id']= $row[csf("color_number_id")];
						 $shipDate_arr[$po_id][$row[csf('country_type')]][$country_id] = $row[csf("country_ship_date")];
						 $po_country_array[$country_id] = $country_arr[$country_id];
 
						 $size_order_arr[$row[csf('size_number_id')]] = $row[csf("size_order")];
 
						 $planQty += $row[csf("plan_cut_qnty")];
					 }
					//  echo"<pre>";print_r($sizeId_arr);die;
 
					 $size_wise_arr = array();
					 $sizeWiseData = sql_select("select size_ratio, size_id, marker_qty,color_id, bundle_sequence from ppl_cut_lay_size_dtls where mst_id=" . $mst_id . " and dtls_id=" . $details_id . " and status_active=1");
					 foreach ($sizeWiseData as $value) 
					 {
						 $size_wise_arr[$value[csf('size_id')]]['ratio'] = $value[csf('size_ratio')];
						 $size_wise_arr[$value[csf('size_id')]]['marker_qty'] = $value[csf('marker_qty')];
						 $size_wise_ar[$value[csf('size_id')]]['seq'] = $value[csf('bundle_sequence')];
					 }
 
					 $sizeDaraArr = array();
					 $manualSizeArr = array();
					 $sizeData =sql_select("SELECT a.id, a.size_ratio, a.size_id,a.color_id, a.marker_qty, a.bundle_sequence, a.order_id, a.country_type, a.country_id, a.excess_perc,a.manual_size_name from ppl_cut_lay_size a where a.mst_id=" . $mst_id . " and a.dtls_id=" . $details_id . " and a.status_active=1");
					// echo  $sizeData;die;
					 if (count($sizeData) > 0) {
						 $is_update = 1;
						 foreach ($sizeData as $value) {
							 $sizeDaraArr[$value[csf('order_id')]][$value[csf('country_type')]][$value[csf('country_id')]][$value[csf('color_id')]][$value[csf('size_id')]] = $value[csf('size_ratio')] . "**" . $value[csf('marker_qty')] . "**" . $value[csf('bundle_sequence')] . "**" . $value[csf('id')] . "**" . $value[csf('excess_perc')];
							 $manualSizeArr[$value[csf('size_id')]] = $value[csf('manual_size_name')];
						 }
					 } else {
						 $is_update = 0;
					 }
 
					//  $distributed_qty_arr = array();
					 $distributed_qty_arr = sql_select("SELECT sum(a.size_qty) as marker_qty, a.size_id,b.color_ids from ppl_cut_lay_bundle a, ppl_cut_lay_dtls b where b.id=a.dtls_id and a.order_id in($order_id) and b.gmt_item_id=$cbo_gmt_id and b.color_ids in('$cbo_color_id') $size_contry_cond and a.status_active=1 and a.mst_id<>" . $mst_id . " and a.dtls_id<>" . $details_id . "  group by a.size_id,b.color_ids");
					//  print_r($distributed_qty_arr);die;

					
 
					 $lay_bl_qty_arr = array();
					 $lay_blData = sql_select("SELECT a.order_id, sum(a.size_qty) as marker_qty, a.country_type, a.country_id, a.size_id from ppl_cut_lay_bundle a, ppl_cut_lay_dtls b where b.id=a.dtls_id and a.order_id in($order_id) and b.gmt_item_id=$cbo_gmt_id and b.color_ids  in($cbo_color_id) $size_contry_cond and a.status_active=1 group by a.order_id, a.country_type,a.country_id, a.size_id");
						// echo $lay_blData;die;
					 foreach ($lay_blData as $value) {
						 $lay_bl_qty_arr[$value[csf('order_id')]][$value[csf('country_type')]][$value[csf('country_id')]][$value[csf('size_id')]] = $value[csf('marker_qty')];
						 $lay_bl_qty_size_arr[$value[csf('size_id')]] += $value[csf('marker_qty')];
					 }
 
					 $size_bl_qty_arr = return_library_array("SELECT sum(a.size_qty) as size_qty, a.size_id from ppl_cut_lay_bundle a, ppl_cut_lay_dtls b where b.id=a.dtls_id and a.order_id in($order_id) and b.gmt_item_id=$cbo_gmt_id and b.color_ids in($cbo_color_id) $country_con and a.status_active=1 group by a.size_id", 'size_id', 'size_qty');
 
					 $size_bl_prev_qty_arr = return_library_array("SELECT sum(a.size_qty) as size_qty, a.size_id from ppl_cut_lay_bundle a, ppl_cut_lay_dtls b where b.id=a.dtls_id and a.order_id in($order_id) and b.gmt_item_id=$cbo_gmt_id and b.color_ids in('$cbo_color_id') $country_con and a.status_active=1 and a.mst_id<>" . $mst_id . " and a.dtls_id<>" . $details_id . "  group by a.size_id", 'size_id', 'size_qty');
					 
					//  echo "SELECT sum(a.size_qty) as size_qty, a.size_id from ppl_cut_lay_bundle a, ppl_cut_lay_dtls b where b.id=a.dtls_id and a.order_id in($order_id) and b.gmt_item_id=$cbo_gmt_id and b.color_ids in('$cbo_color_id') $country_con and a.status_active=1 and a.mst_id<>" . $mst_id . " and a.dtls_id<>" . $details_id . "  group by a.size_id";die;

					// $color_id=implode(',',$cbo_color_id);
					// echo $color_id;die;
					 ?>
					 <table cellpadding="0" cellspacing="0" width="530" id="tbl_size">
						 <thead class="form_table_header">
						 	
							 <th>Size</th>
							 <th>Lay Balance</th>
							 <th>Size Ratio</th>
							 <th>Size Qty.</th>
							 <th>Bundle Priority</th>
							 <th>Distributed Qty.</th>
							 <th title="Manual Entry">Cutting Size Name</th>
						 </thead>
						 <tbody>
							 <?
							// print_r($size_bl_prev_qty_arr);die;
							 $i = 1;
							 $total_layf_balance = 0;
							 $total_markerf_qty = 0;
							 $total_sizef_ratio = 0;
							 $sizeDataArray = array();
							 //asort($size_order_arr);
							 //foreach($size_order_arr as $size_id=>$size_order)
							 foreach ($sizeId_arr as $size_id => $plan_cut_qty) 
							 {
							
								
								
								//  echo $plan_cut_qty."-".
								//  $size_bl_prev_qty_arr[$color_id][$size_id]."<br>";
								 //$lay_balance=$plan_cut_qty-$lay_bl_qty_size_arr[$size_id]+$size_wise_arr[$size_id]['marker_qty'];
								 //$plan_cut_qty=$sizeId_arr[$size_id];
								 // $lay_balance=$plan_cut_qty-$size_bl_qty_arr[$size_id];
								
								 $lay_balance = $plan_cut_qty - $size_bl_prev_qty_arr[$size_id];
								 $total_layf_balance += $lay_balance;
 
								 $total_markerf_qty += $size_wise_arr[$size_id]['marker_qty'];
								 $total_distributed_qty += $distributed_qty_arr[$size_id];
								 $total_sizef_ratio += $size_wise_arr[$size_id]['ratio'];
 
								 $sizeDataArray[$size_id] = $size_wise_arr[$size_id]['ratio'];
								 ?>
								 <tr id="size_<? echo $i; ?>">
									 
									 <td align="center">
										 <input type="text" style="width:80px" class="text_boxes" name="txt_sizef_<? echo $i; ?>" id="txt_sizef_<? echo $i; ?>" value="<? echo $size_arr[$size_id]; ?>" disabled readonly />
										 <input type="hidden" id="hidden_sizef_id_<? echo $i; ?>" name="hidden_sizef_id_<? echo $i; ?>" value="<? echo $size_id; ?>">
										 <input type="hidden" id="hidden_txt_color_<? echo $i; ?>" name="hidden_txt_color_<? echo $i; ?>" value="<?=$cbo_color_id ?>">
									 </td>
									 <td align="center">
										 <input type="text" style="width:80px" class="text_boxes_numeric" name="txt_layf_balance_<? echo $i; ?>" id="txt_layf_balance_<? echo $i; ?>" value="<? echo $lay_balance; ?>" disabled />
									 </td>
									 <td align="center">
										 <input type="text" style="width:80px" class="text_boxes_numeric" onKeyUp="check_sizef_qty(<? echo $txt_piles; ?>,this.value,this.id)" name="txt_sizef_ratio_<? echo $i; ?>" id="txt_sizef_ratio_<? echo $i; ?>" value="<? echo $size_wise_arr[$size_id]['ratio']; ?>" />
									 </td>
									 <td align="center">
										 <input type="text" style="width:80px" class="text_boxes_numeric" name="txt_sizef_qty_<? echo $i; ?>" id="txt_sizef_qty_<? echo $i; ?>" value="<? echo $size_wise_arr[$size_id]['marker_qty']; ?>" disabled readonly />
										 <input type="hidden" name="txt_sizef_prev_qty_<? echo $i; ?>" id="txt_sizef_prev_qty_<? echo $i; ?>" value="<? echo $size_wise_arr[$size_id]['marker_qty']; ?>" />
									 </td>
									 <td align="center">
										 <input type="text" style="width:60px" class="text_boxes_numeric" name="txt_bundle_<? echo $i; ?>" id="txt_bundle_<? echo $i; ?>" onKeyUp="sequence_duplication_check(<? echo $i; ?>)" value="<? echo $size_wise_arr[$size_id]['seq']; ?>" />
									 </td>
									 <td align="center">
										 <input type="text" style="width:100px" class="text_boxes_numeric" name="txt_distributed_qty_<? echo $i; ?>" id="txt_distributed_qty_<? echo $i; ?>" value="<? echo $distributed_qty_arr[$size_id]; ?>" disabled readonly />
									 </td>
									 <td align="center">
										 <input type="text" style="width:50px" class="text_boxes_numeric" name="txt_sizef_name_<? echo $i; ?>" id="txt_sizef_name_<? echo $i; ?>" value="<?= $manualSizeArr[$size_id]; ?>" />
									 </td>
								 </tr>
							 <?
								 $i++;
								
							 }
							//  print_r($rollData);die;
							 $allData = $rollData;
							 ?>
						 </tbody>
						 <tfoot>
							 <tr class="form_table_header">
							 	
								 <th>Total</th>
								 <th align="right"><? echo $total_layf_balance; ?></th>
								 <th id="total_sizef_ratio" align="right"><? echo $total_sizef_ratio; ?></th>
								 <th id="total_sizef_qty" align="right"><? echo $total_markerf_qty; ?>
									 <input type='hidden' id="hidden_size_marker_qty" name="hidden_size_marker_qty" value="<? echo $total_markerf_qty; ?>" />
								 </th>
								 <th>&nbsp;<input type='hidden' id="roll_data" name="roll_data" value="<? //echo chop($allData,'|'); 
																										 ?>" /></th>
								 <th align="right" id="total_distributed_qty"><? echo $total_distributed_qty; ?></th>
								 <th></th>
							 </tr>
						 </tfoot>
					 </table>
					 <br>
					 <div style="width:780px;overflow-x: auto;">
						 <!--<input type="button" style="width:150px" value="Roll Wise Size Qty" name="btn" id="btn" class="formbuttonplasminus" onClick="fnc_rollWiseSizeQty();"/>-->
						 <fieldset style="width:780px;">
							 <legend>Roll Wise Size Qty</legend>
							 <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="770" id="tbl_roll">
								 <thead>
								 	<th width="80">Color Name</th>
									 <th width="60">Roll No</th>
									 <th width="70">Roll Length</th>
									 <th width="60">Plies</th>
									 <?
									 foreach ($sizeDataArray as $key => $value) {
										 echo '<th>' . $size_arr[$key] . '</th>';
									 }
									 ?>
								 </thead>
								 <?
								// print_r($allData);die;
								 $i = 1;
								 $rollDatas = explode("**", $allData);
								 	// print_r($rollDatas);die;

								 foreach ($rollDatas as $data)
								  {
									 $datas = explode("=", $data);
									 if ($i % 2 == 0) $bgcolor = "#E9F3FF";
									 else $bgcolor = "#FFFFFF";
								 	?>
									 <tr bgcolor="<? echo $bgcolor; ?>">
										 <td>
											 <input type="text" id="colorid_<? echo $i; ?>" name="colorid[]" style="width:50px" class="text_boxes_numeric" value="<? echo $color_name_arr[$datas[7]]; ?>" disabled>
											 <input type="hidden" id="hidden_txt_color_<? echo $i; ?>" name="hidden_txt_color_<? echo $i; ?>" value="<? echo $datas[7]; ?>">
										 </td>
										 <td>
											 <input type="text" id="rollNo_<? echo $i; ?>" name="rollNo[]" style="width:50px" class="text_boxes_numeric" value="<? echo $datas[1]; ?>" disabled>
											 <input type="hidden" id="rollId_<? echo $i; ?>" name="rollId[]" value="<? echo $datas[2]; ?>">
										 </td>
										 <td><input type="text" id="rollWgt_<? echo $i; ?>" name="rollWgt[]" style="width:60px" class="text_boxes_numeric" value="<? echo $datas[3]; ?>" disabled></td>
										 <td><input type="text" id="piles_<? echo $i; ?>" name="piles[]" style="width:50px" class="text_boxes_numeric" value="<? echo $datas[4]; ?>" disabled></td>
										 <?
										 foreach ($sizeDataArray as $key => $value) {
										 ?>
											 <td align="center"><input type="text" id="sqty_<? echo $key . "_" . $i; ?>" name="sqty[]" style="width:50px" class="text_boxes_numeric" value="<? if ($value * $datas[4] > 0) echo $value * $datas[4]; ?>" disabled></td>
										 <?
										 }
										 ?>
									 </tr>
									 <?
									 $i++;
								 }
								 ?>
							 </table>
							 <table>
								 <tr>
									 <td align="center" valign="middle" colspan="5">
										 <? echo load_submit_buttons($permission, "fnc_cut_lay_size_info", $is_update, 0, "clear_size_form()", 1); ?>
									 </td>
								 </tr>
								 <tr>
									 <td align="center" colspan="7">
										 <input type="button" id="close_size_id" name="close_size_id" class="formbutton" style="width:50px" onClick="size_popup_close(<? echo $size; ?>,$('#total_sizef_qty').text(),$('#hidden_plant_qty').val(),$('#total_distributed_qty').text(),$('#hidden_lay_balance').val())" value="Close" />
										 <? echo create_drop_down("cboCountryBundle", 120, $po_country_array, '', 1, '-- ALL Country --', '', '', 0); ?>
										 <input type="button" id="btn1" name="btn1" value="Bundle" class="formbutton" onClick="fnc_print_bundle(1);" style="display:none" />
										 <input type="button" id="btn20" name="btn20" value="Bundle V2" class="formbutton" onClick="fnc_print_bundle(3);" style="display:none" />
										 <input type="button" id="btn15" name="btn15" value="Lay Bundle" class="formbutton" onClick="fnc_print_bundle_lay('lay_bundle_print',1);" style="display:none" />
										 &nbsp;<a id="print_report4" href="" style="text-decoration:none" download hidden></a>
 
										 <input type="button" id="btn12" name="btn12" value="QC Bundle" class="formbutton" onClick="fnc_print_qc_bundle(1);" style="display:none" />
										 <input type="button" id="btn18" name="btn18" value="QC Bundle-2" class="formbutton" onClick="fnc_print_qc_bundle(2);" style="display:none" />
										 <input type="button" id="btn23" name="btn23" value="QC Bundle-3" class="formbutton" onClick="fnc_print_qc_bundle(3);" />
										 <input type="button" id="btn2" name="btn2" value="List Shade" class="formbutton" onClick="fnc_print_bundle(2);" style="display:none" />
										 <input type="button" id="btn3" name="btn3" value="Sticker 6/Page" class="formbutton" onClick="fnc_bundle_report(6);" style="display:none" />
										 <input type="button" id="btn4" name="btn4" value="Sticker 8/Page" class="formbutton" onClick="fnc_bundle_report_eight();" style="display:none" />
										 <input type="button" id="btn5" name="btn5" value="Sticker 1/Page" class="formbutton" onClick="fnc_bundle_report_one();" style="display:none" />
										 <input type="button" id="btn6" name="btn6" value="Sticker 1/Page V2" class="formbutton" onClick="fnc_bundle_report_one_urmi(1);" style="display:none" />
										 <input type="button" id="btn7" name="btn7" value="Send To Printer" class="formbutton" onClick="fnc_send_printer_text();" style="display:none" />
										 <input type="button" id="btn8" name="btn8" value="Sticker 128" class="formbutton" onClick="fnc_bundle_report_128();" style="display:none" />
										 <input type="button" id="btn13" name="btn13" value="Sticker 128 V2" class="formbutton" onClick="fnc_bundle_report_129();" style="display:none" />
										 <input type="button" id="btn9" name="btn9" value="Qr Code" class="formbutton" onClick="fnc_bundle_report_qrcode();" style="display:none" />
										 <input type="button" id="btn9v2" name="" value="QR 69x38" class="formbutton" onClick="fnc_bundle_report_qrcode_v2(1);" style="display:none"/>
 
										 <input type="button" id="btn10" name="btn10" value="Sticker 1/Page V3" class="formbutton" onClick="fnc_bundle_report_one_urmi(2);" style="display:none" />
 
										 <input type="button" id="btn11" name="btn11" value="Sticker 1/Page V4" class="formbutton" onClick="fnc_bundle_report_one_urmi(3);" style="display:none" />
										 <input type="button" id="btn19" name="btn19" value="Sticker 1/Page V6" class="formbutton" onClick="fnc_bundle_report_one_urmi(7);" style="display:none" />
 
										 <input type="button" id="btn22" name="btn22" value="Sticker 1/Page V7" class="formbutton" onClick="fnc_bundle_report_one_urmi(8);" style="display:none" />
 
										 <input type="button" id="btn14" name="btn14" value="Sticker 1/Page V5" class="formbutton" onClick="fnc_bundle_report_one_urmi(4);" style="display:none" />
 
										 <input type="button" id="btn16" name="btn16" value="EG Sticker" class="formbutton" onClick="fnc_bundle_report_one_urmi(5);" style="display:none" />
 
										 <input type="button" id="btn17" name="btn17" value="EG Sticker A4" class="formbutton" onClick="fnc_bundle_report_one_urmi(6);" style="display:none" />
 
										 <input type='hidden' id="hidden_marker_no_x" name="hidden_marker_no_x" />
 
										 <input type='hidden' id="hidden_total_marker" name="hidden_total_marker" />
										 <input type='hidden' id="hidden_lay_balance" name="hidden_lay_balance" />
										 <input type='hidden' id="hidden_plant_qty" name="hidden_plant_qty" value="<?= $planQty; ?>" />
									 </td>
								 </tr>
							 </table>
						 </fieldset>
					 </div>
					 <script>
						 fnc_printBtnShowHide();
					 </script>
					 <br>
					 <h3 align="left" id="accordion_h1" style="width:810px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> +Country & Size Wise Lay Balance</h3>
					 <div id="content_search_panel"> <!-- style="display:none"-->
						 <table cellpadding="0" cellspacing="0" width="800" class="" rules="all" border="1" id="tbl_size_details">
							 <? if($hiddiscountryseq==1 && $hiddisorderseq==1)
							 {
								 ?>
								 <caption style="color: red;">Order sequence and country sequence both are active, for that Order sequence will work. </caption>
								 <?
							 }
							 ?>
							 <thead class="form_table_header">
								 <th>Order No.</th>
								 <th>Country Type</th>
								 <th>Country Name</th>
								 <th>Country Ship. Date</th>
								 <th>Color</th>
								 <th>Size</th>
								 <th>Lay Balance</th>
								 <th>Copy&nbsp;<input type="checkbox" name="checkbox" id="checkbox"><br>&nbsp;Excess %</th>
								 <th>Qty.</th>
							 </thead>
							 <tbody>
								 <?
								 $i = 1;
								 $total_lay_balance = 0;
								 $total_marker_qty = 0;
								 $total_size_ratio = 0;
								 // echo "<pre>";print_r($size_details);
								 // ksort($size_details);
								 foreach ($size_details as $seq => $seq_val) {
									 foreach ($seq_val as $po_id => $po_val) {
										 foreach ($po_val as $country_type_id => $country_val) {
											 foreach ($country_val as $country_id => $country_data) {
												foreach ($country_data as $color_id => $color_val) {
												 foreach ($color_val as $size_id => $plan_cut_qnty) {
													 $data = explode("**", $sizeDaraArr[$po_id][$country_type_id][$country_id][$color_id][$size_id]);
													//  echo "<pre>";print_r($sizeDaraArr);die;
													 $lay_balance = $plan_cut_qnty - $lay_bl_qty_arr[$po_id][$country_type_id][$country_id][$color_id][$size_id] + $data[1];
													 // echo $size_id."==".$plan_cut_qnty.'=='.$lay_bl_qty_arr[$po_id][$country_type_id][$country_id][$size_id].'=='.$data[1].'<br>';
													 // echo "$po_id**$country_type_id**$country_id**$size_id<br>";
													 $total_lay_balance += $lay_balance;
													 $total_marker_qty += $data[1];
													 $total_size_ratio += $data[0];
									 ?>
													 <tr id="gsd_<? echo $i; ?>">
														 <td align="center">
															 <input type="text" style="width:100px" class="text_boxes" name="poNo_<? echo $i; ?>" id="poNo_<? echo $i; ?>" value="<? echo $po_no_arr[$po_id]; ?>" disabled />
															 <input type="hidden" name="poId_<? echo $i; ?>" id="poId_<? echo $i; ?>" value="<? echo $po_id; ?>" />
														 </td>
														 <td align="center">
															 <?
															 echo create_drop_down("cboCountryType_" . $i, 100, $country_type, '', 0, '', $country_type_id, '', 1);
															 ?>
														 </td>
														 <td align="center" title="<?=$seq."=".$country_id;?>">
															 <?
															 echo create_drop_down("cboCountry_" . $i, 110, $country_arr, '', 1, '', $country_id, '', 1);
															 ?>
														 </td>
														 <td align="center">
															 <input type="text" style="width:80px" class="datepicker" name="shipdate_<? echo $i; ?>" id="shipdate_<? echo $i; ?>" value="<? echo change_date_format($shipDate_arr[$po_id][$country_type_id][$country_id]); ?>" disabled readonly />
														 </td>
														 <td align="center">
															 <input type="text" style="width:80px" class="datepicker" name="shipdate_<? echo $i; ?>" id="shipdate_<? echo $i; ?>" value="<?=$color_name_arr[$color_id]; ?>" disabled readonly />
														 </td>
														 <td align="center">
															 <input type="text" style="width:80px" class="text_boxes" name="txt_size_<? echo $i; ?>" id="txt_size_<? echo $i; ?>" value="<? echo $size_arr[$size_id]; ?>" disabled readonly />
															 <input type="hidden" id="hidden_size_id_<? echo $i; ?>" name="hidden_size_id_<? echo $i; ?>" value="<? echo $size_id; ?>">
															 <input type="hidden" id="update_size_id_<? echo $i; ?>" name="update_size_id_<? echo $i; ?>" value="<? echo $data[3]; ?>">
														 </td>
														 <td align="center">
															 <input type="text" style="width:80px" class="text_boxes_numeric" name="txt_lay_balance_<? echo $i; ?>" id="txt_lay_balance_<? echo $i; ?>" value="<? echo $lay_balance; ?>" disabled readonly />
														 </td>
														 <td align="center">
															 <input type="text" style="width:50px" class="text_boxes_numeric" onKeyUp="copy_perc(<? echo $i; ?>);" name="txt_excess_<? echo $i; ?>" id="txt_excess_<? echo $i; ?>" value="<? echo $data[4]; ?>" />
														 </td>
														 <td align="center">
															 <input type="text" style="width:80px" class="text_boxes_numeric" name="txt_size_qty_<? echo $i; ?>" id="txt_size_qty_<? echo $i; ?>" value="<? echo $data[0]; ?>" onKeyUp="calculate_perc(<? echo $i; ?>);" onBlur="check_size_qty(<? echo $i; ?>);" />
														 </td>
													 </tr>
									 <?
													 $i++;
												 }
												}
											 }
										 }
									 }
								 }
								 ?>
							 </tbody>
							 <tfoot>
								 <tr class="form_table_header">
									 <th colspan="5" align="right">Total</th>
									 <th align="right"><? echo $total_lay_balance; ?>&nbsp;</th>
									 <th>&nbsp;</th>
									 <th align="right" id="total_size_qty"><? echo $total_marker_qty; ?>&nbsp;</th>
								 </tr>
							 </tfoot>
						 </table>
					 </div>
				 </fieldset>
			 </form>
			 <form name="searchorderfrm_2" id="searchorderfrm_2" autocomplete="off">
				 <br />
				 <div id="search_div" style="margin-top:10px">
					 <?
					 $sql_size_name = sql_select("select size_id from ppl_cut_lay_size where mst_id=" . $mst_id . " and dtls_id=" . $details_id . " and status_active=1 and is_deleted=0");
					 $size_colour_arr = array();
					 foreach ($sql_size_name as $asf) {
						 $size_colour_arr[$asf[csf("size_id")]] = $size_arr[$asf[csf("size_id")]];
					 }
					 $i = 1;
					 $bundle_data = sql_select("SELECT a.id,a.bundle_no,a.size_id,a.number_start,a.number_end,a.size_qty,a.update_flag,a.update_value, a.country_type, a.country_id, a.roll_no, a.roll_id, a.pattern_no,a.barcode_no, a.is_excess, a.order_id from ppl_cut_lay_bundle a where a.mst_id=" . $mst_id . " and a.dtls_id=" . $details_id . " and a.status_active=1 and a.is_deleted=0 order by a.id ASC");
					 if (count($bundle_data) > 0) {
					 ?>
						 <fieldset style="width:960px">
							 <legend>Bundle No and RMG qty details</legend>
							 <table cellpadding="0" cellspacing="0" width="950" rules="all" border="1" class="rpt_table" id="tbl_bundle_list_save">
								 <thead class="form_table_header">
									 <th></th>
									 <th></th>
									 <th></th>
									 <th></th>
									 <th></th>
									 <th></th>
									 <th></th>
									 <th></th>
									 <th></th>
									 <th colspan="2">RMG Number</th>
									 <th>
										 <input type="hidden" id="hidden_mst_id" name="hidden_mst_id" value="<? echo $mst_id; ?>" />
										 <input type="hidden" id="hidden_detls_id" name="hidden_detls_id" value="<? echo $details_id; ?>" />
									 </th>
									 <th>Report &nbsp;</th>
								 </thead>
								 <thead class="form_table_header">
									 <th>SL No</th>
									 <th>Order No.</th>
									 <th>Country Type</th>
									 <th>Country Name</th>
									 <th>Size</th>
									 <th>Pattern</th>
									 <th>Roll No</th>
									 <th>Bundle No</th>
									 <th>Quantity</th>
									 <th>From</th>
									 <th>To</th>
									 <th></th>
									 <th width="40"><input type="checkbox" name="check_all" id="check_all" onClick="check_all_report()"></th>
								 </thead>
								 <tbody id="trBundleListSave">
									 <?
									 foreach ($bundle_data as $row) {
										 $update_f_value = "";
										 if (str_replace("'", "", $row[csf('update_flag')]) == 1) {
											 $update_f_value = explode("**", $row[csf('update_value')]);
										 }
									 ?>
										 <tr id="trBundleListSave_<? echo $i;  ?>">
											 <td align="center" id="">
												 <input type="text" id="sirialNo_<? echo $i; ?>" name="sirialNo[]" style="width:25px;" class="text_boxes" value="<? echo $i; ?>" disabled />
												 <input type="hidden" id="hiddenExtraTr_<? echo $i;  ?>" name="hiddenExtraTr[]" value="<? echo $i;  ?>" />
												 <input type="hidden" id="hiddenUpdateFlag_<? echo $i;  ?>" name="hiddenUpdateFlag[]" value="<? echo $row[csf('update_flag')]; ?> " />
												 <input type="hidden" id="hiddenUpdateValue_<? echo $i;  ?>" name="hiddenUpdateValue[]" value="<? echo $row[csf('update_value')]; ?> " />
											 </td>
											 <td align="center">
												 <?
												 echo create_drop_down("cboPoId_" . $i, 130, $po_no_arr, '', 0, '', $row[csf('order_id')], '', 1, '', '', '', '', '', '', 'cboPoId[]');
												 ?>
											 </td>
											 <td align="center">
												 <?
												 echo create_drop_down("cboCountryTypeB_" . $i, 70, $country_type, '', 0, '', $row[csf('country_type')], '', 1);
												 ?>
												 <input type="hidden" id="hiddenCountryTypeB_<? echo $i;  ?>" name="hiddenCountryTypeB[]" value="<? echo $row[csf('country_type')]; ?> " />
											 </td>
											 <td align="center">
												 <?
												 echo create_drop_down("cboCountryB_" . $i, 80, $po_country_array, '', 1, '', $row[csf('country_id')], '', 1, '', '', '', '', '', '', 'cboCountryB[]');
												 ?>
												 <input type="hidden" id="hiddenCountryB_<? echo $i; ?>" name="hiddenCountryB[]" value="<? echo $row[csf('country_id')]; ?> " />
											 </td>
											 <td align="center" id="update_sizename_<? echo $i;  ?>">
												 <select name="sizeName[]" id="sizeName_<? echo $i;  ?>" class="text_boxes" style="width:60px; text-align:center; <? if ($update_f_value[1] != "") echo "background-color:#F3F;"; ?> " disabled>
													 <?
													 // $l=1;
													 foreach ($sql_size_name as $asf) {
														 if ($asf[csf("size_id")] == $row[csf('size_id')]) $select_text = "selected";
														 else $select_text = "";
													 ?>
														 <option value="<? echo $asf[csf("size_id")]; ?>" <? echo $select_text;  ?>><? echo $size_arr[$asf[csf("size_id")]]; ?> </option>
													 <?
													 }
													 ?>
												 </select>
												 <input type="hidden" name="hiddenSizeId[]" id="hiddenSizeId_<? echo $i;  ?>" value="<? echo $row[csf('size_id')];  ?>" />
											 </td>
											 <td align="center"><input type="text" name="patternNo[]" id="patternNo_<? echo $i; ?>" value="<? echo $row[csf('pattern_no')]; ?>" class="text_boxes" style="width:35px; text-align:center" disabled /><input type="hidden" name="isExcess[]" id="isExcess_<? echo $i; ?>" value="<? echo $row[csf('is_excess')]; ?>" /></td>
											 <td align="center">
												 <input type="text" name="rollNo[]" id="rollNo_<? echo $i; ?>" value="<? echo $row[csf('roll_no')]; ?>" class="text_boxes" style="width:40px;  text-align:center" disabled />
												 <input type="hidden" name="rollId[]" id="rollId_<? echo $i; ?>" value="<? echo $row[csf('roll_id')]; ?>" />
											 </td>
											 <td align="center" title="">
												 <input type="text" name="bundleNo[]" id="bundleNo_<? echo $i;  ?>" value="<? echo $row[csf('bundle_no')];  ?>" class="text_boxes" style="width:120px; text-align:center" disabled title="<?php echo $row[csf('barcode_no')]; ?>" />
											 </td>
											 <td align="center">
												 <input type="text" name="bundleSizeQty[]" id="bundleSizeQty_<? echo $i;  ?>" onKeyUp="bundle_calclution(<? echo $i;  ?>)" value="<? echo $row[csf('size_qty')];  ?>" style="width:40px; text-align:right; <? if ($update_f_value[0] != "") echo "background-color:#F3F;"; ?>" class="text_boxes" disabled />
												 <input type="hidden" name="hiddenSizeQty[]" id="hiddenSizeQty_<? echo $i;  ?>" value="<? echo $row[csf('size_qty')];  ?>" disabled />
											 </td>
											 <td align="center">
												 <input type="text" name="rmgNoStart[]" id="rmgNoStart_<? echo $i;  ?>" value="<? echo $row[csf('number_start')];  ?>" style="width:40px; text-align:right" class="text_boxes" disabled />
											 </td>
											 <td align="center">
												 <input type="text" name="rmgNoEnd[]" id="rmgNoEnd_<? echo $i;  ?>" value="<? echo $row[csf('number_end')];  ?>" style="width:40px; text-align:right" class="text_boxes" disabled />
											 </td>
											 <td align="center">
												 <input type="button" value="+" name="addButton[]" id="addButton_<? echo $i;  ?>" style="width:30px" class="formbuttonplasminus" onClick="fnc_addRow('<? echo $i;  ?>','<? echo $i;  ?>')" />
												 <input type="button" value="Adj." name="rowUpdate[]" id="rowUpdate_<? echo $i;  ?>" style="width:40px;" class="formbuttonplasminus" onClick="fnc_updateRow('<? echo $i;  ?>')" />
											 </td>
											 <td align="center">
												 <input id="chk_bundle_<? echo $i;  ?>" type="checkbox" name="chk_bundle">
												 <input type="hidden" id="hiddenid_<? echo $i; ?>" name="hiddenid_<? echo $i; ?>" value="<? echo $row[csf('id')];  ?>" style="width:15px;" class="text_boxes" />
											 </td>
										 </tr>
									 <?
										 $i++;
									 }
									 ?>
								 </tbody>
							 </table>
							 <table cellpadding="0" cellspacing="0" width="700" border="1" rules="all">
								 <tr>
									 <td colspan="13" align="center" class="button_container">
										 <? echo load_submit_buttons($permission, "fnc_cut_lay_bundle_info", 1, 0, "clear_size_form()", 1); ?>
									 </td>
								 </tr>
							 </table>
						 </fieldset>
					 <?
					 }
					 ?>
				 </div>
			 </form>
		 </div>
	 </body>
	 <script src="../../../includes/functions_bottom_noselect.js" type="text/javascript"></script>
	 <script>
		 $('#cboCountryBundle').val(0);
	 </script>
 
	 </html>
 <?
 }

if ($action == "rollSize_popup") {
	echo load_html_head_contents("Roll Size Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);

	$sizeDataArray = array();
	$size_datas = explode("|", $size_data);
	foreach ($size_datas as $data) {
		$datas = explode("_", $data);
		$sizeDataArray[$datas[0]] = $datas[1];
	}
 ?>
	<script>
		function fnc_close() {
			parent.emailwindow.hide();
		}
	</script>

	</head>

	<body>
		<div align="center" style="width:650px;">
			<form name="searchwofrm" id="searchwofrm">
				<fieldset style="width:640px; margin-left:2px">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="620">
						<thead>
							<th width="60">Roll No</th>
							<th width="70">Roll Length</th>
							<th width="60">Plies</th>
							<?
							foreach ($sizeDataArray as $key => $value) {
								echo '<th>' . $size_arr[$key] . '</th>';
							}
							?>
						</thead>
						<?
						$rollDatas = explode("**", $rollData);
						$allData = '';
						foreach ($rollDatas as $data) {
							$datas = explode("=", $data);
							if ($i % 2 == 0) $bgcolor = "#E9F3FF";
							else $bgcolor = "#FFFFFF";
							$allData .= $data;
						?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td>
									<input type="text" id="rollNo_<? echo $i; ?>" name="rollNo[]" style="width:50px" class="text_boxes_numeric" value="<? echo $datas[1]; ?>">
									<input type="hidden" id="rollId_<? echo $i; ?>" name="rollId[]" value="<? echo $datas[2]; ?>">
								</td>
								<td><input type="text" id="rollWgt_<? echo $i; ?>" name="rollWgt[]" style="width:60px" class="text_boxes_numeric" value="<? echo $datas[3]; ?>"></td>
								<td><input type="text" id="piles_<? echo $i; ?>" name="piles[]" style="width:50px" class="text_boxes_numeric" value="<? echo $datas[4]; ?>"></td>
								<?
								foreach ($sizeDataArray as $key => $value) {
									$allData .= "=" . $value * $datas[4];
								?>
									<td align="center"><input type="text" id="sqty_<? echo $key . "_" . $i; ?>" name="sqty[]" style="width:50px" class="text_boxes_numeric" value="<? echo $value * $datas[4]; ?>"></td>
								<?
								}
								$allData .= "|";
								?>
							</tr>
						<?
							$i++;
						}
						?>
					</table>
					<table width="620">
						<tr>
							<td align="center">
								<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
								<input type="hidden" name="hidden_roll_data" id="hidden_roll_data" value="<? echo chop($allData, '|'); ?>" />
							</td>
						</tr>
					</table>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
 <?
}

if ($action == "save_update_delete_bundle") 
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	if ($operation == 1)  // Insert Here=======================================================
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		$cutting_qc_no = return_field_value("cutting_qc_no", "pro_gmts_cutting_qc_mst", "status_active=1 and is_deleted=0 and cutting_no='" . $hidden_cutting_no . "'");
		//echo $cutting_qc_no;die;
		if ($cutting_qc_no != "") {
			echo "200**" . $cutting_qc_no;
			disconnect($con);
			die;
		}

		$previous_barcode_data = sql_select("select bundle_no,barcode_no,barcode_year,barcode_prifix from ppl_cut_lay_bundle where mst_id=" . $bundle_mst_id . "  and  dtls_id=" . $bundle_dtls_id . " and status_active=1 and is_deleted=0 ");
		foreach ($previous_barcode_data as $b_val) {
			$previous_barcode_arr[$b_val[csf("bundle_no")]]['year'] = $b_val[csf("barcode_year")];
			$previous_barcode_arr[$b_val[csf("bundle_no")]]['prifix'] = $b_val[csf("barcode_prifix")];
			$previous_barcode_arr[$b_val[csf("bundle_no")]]['barcode'] = $b_val[csf("barcode_no")];
		}


		$id = return_next_id("id", "ppl_cut_lay_bundle", 1);


		$field_array = "id,mst_id,dtls_id,size_id,bundle_num_prefix,bundle_num_prefix_no,bundle_no,barcode_year,barcode_prifix,barcode_no,number_start,number_end,size_qty,update_flag,update_value,country_type,country_id,roll_id,roll_no,pattern_no,is_excess,order_id,color_type_id,inserted_by,insert_date,status_active,is_deleted";

		$year_id = date('Y', time());
		if (strlen($year_id) == 4) $year_id = substr($year_id, 2, 2);
		$barcode_suffix_no = return_field_value("max(barcode_prifix) as suffix_no", "ppl_cut_lay_bundle", "barcode_year=$year_id", "suffix_no");

		for ($j = 1; $j <= $tot_row; $j++) {
			$new_bundle_no = "txtBundleNo_" . $j;
			$new_bundle_qty = "txtBundleQty_" . $j;
			$hidden_bundle_qty = "hiddenSizeqty_" . $j;
			$new_bundle_from = "txtBundleFrom_" . $j;
			$new_bundle_to = "txtBundleTo_" . $j;
			$new_bundle_size_id = "txtSizeId_" . $j;
			$new_update_flag = "hiddenUpdateFlag_" . $j;
			$hidden_size_id = "txtHiddenSizeId_" . $j;
			$new_update_value = "hiddenUpdateValue_" . $j;
			$hiddenCountry = "cboCountry_" . $j;
			$hiddenCountryType = "hiddenCountryType_" . $j;
			$rollId = "rollId_" . $j;
			$rollNo = "rollNo_" . $j;
			$patternNo = "patternNo_" . $j;
			$isExcess = "isExcess_" . $j;
			$cboPoId = "cboPoId_" . $j;
			$bundle_prif = explode("-", $$new_bundle_no);
			$new_bundle_prif_no = explode('-', $bundle_prif[3]);
			$new_bundle_prifix = $bundle_prif[0] . "-" . $bundle_prif[1] . "-" . $bundle_prif[2];
			$update_flag = 0;
			$update_flag_value = "";
			//echo $$new_update_flag."**".$$new_update_value;die;
			if (str_replace("'", "", $$new_update_flag) != 1) {
				if (str_replace("'", "", $$new_update_flag) == 6) {
					if (trim($$hidden_bundle_qty) != trim($$new_bundle_qty)) {
						$update_flag_value = "" . str_replace("'", "", $$hidden_bundle_qty) . "";
						$update_flag = 1;
					} else {
						$update_flag_value = "";
					}
					if (trim($$hidden_size_id) != trim($$new_bundle_size_id)) {
						$update_flag_value .= "**" . str_replace("'", "", $$new_bundle_size_id) . "";
						$update_flag = 1;
					} else {
						$update_flag_value .= "**";
					}
				}
			} else {
				$update_flag = 1;
				$update_flag_value = $$new_update_value;
			}

			if (empty($previous_barcode_arr[str_replace("'", "", $$new_bundle_no)])) {
				$barcode_suffix_no = $barcode_suffix_no + 1;
				$up_barcode_suffix = $barcode_suffix_no;
				$up_barcode_year = $year_id;
				$barcode_no = $year_id . "99" . str_pad($barcode_suffix_no, 10, "0", STR_PAD_LEFT);
			} else {
				$up_barcode_suffix = $previous_barcode_arr[str_replace("'", "", $$new_bundle_no)]['prifix'];
				$up_barcode_year = $previous_barcode_arr[str_replace("'", "", $$new_bundle_no)]['year'];
				$barcode_no = $previous_barcode_arr[str_replace("'", "", $$new_bundle_no)]['barcode'];
			}


			//echo $update_flag_value."***";die;
			if ($data_array != "") $data_array .= ",";
			$data_array .= "(" . $id . "," . $bundle_mst_id . "," . $bundle_dtls_id . "," . $$new_bundle_size_id . ",'" . $new_bundle_prifix . "','" . $new_bundle_prif_no[0] . "','" . $$new_bundle_no . "','" . $up_barcode_year . "','" . $up_barcode_suffix . "','" . $barcode_no . "','" . $$new_bundle_from . "','" . $$new_bundle_to . "','" . $$new_bundle_qty . "'," . $update_flag . ",'" . $update_flag_value . "','" . str_replace("'", "", $$hiddenCountryType) . "','" . str_replace("'", "", $$hiddenCountry) . "','" . str_replace("'", "", $$rollId) . "','" . str_replace("'", "", $$rollNo) . "','" . str_replace("'", "", $$patternNo) . "','" . str_replace("'", "", $$isExcess) . "','" . str_replace("'", "", $$cboPoId) . "'," . $color_type_id . ",'" . $user_id . "','" . $pc_date_time . "',1,0)";
			$id = $id + 1;
		}
		//echo $data_array;die;	
		//echo "10**insert into ppl_cut_lay_bundle($field_array) values".$data_array;die;
		$rID = execute_query("delete from ppl_cut_lay_bundle where mst_id=" . $bundle_mst_id . " and dtls_id=" . $bundle_dtls_id . "", 0);
		$rID1 = sql_insert("ppl_cut_lay_bundle", $field_array, $data_array, 1);
		//echo "10**".$rID.$rID1;die;
		if ($db_type == 0) {
			if ($rID && $rID1) {
				mysql_query("COMMIT");
				echo "0**" . $bundle_mst_id . "**" . $bundle_dtls_id;
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . $bundle_mst_id . "**" . $bundle_dtls_id;
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rID1) {
				oci_commit($con);
				echo "0**" . $bundle_mst_id . "**" . $bundle_dtls_id;
			} else {
				oci_rollback($con);
				echo "10**" . $bundle_mst_id . "**" . $bundle_dtls_id;
			}
		}

		disconnect($con);
		die;
	} else if ($operation == 2)   // Delete Here=======================================================================
	{
	}
}

if ($action == "report_bundle_printer") {
	$data = explode("***", $data);
	$con = connect();
	if ($db_type == 0) {
		mysql_query("BEGIN");
	}
	$buyer_library = return_library_array("select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
	$table_no_library = return_library_array("select id,table_no  from  lib_cutting_table", "id", "table_no");
	$color_sizeID_arr = sql_select("select id,size_id,bundle_no,number_start,number_end,size_qty from ppl_cut_lay_bundle
	                              where mst_id=$data[1] and dtls_id=$data[2] order by id");  //where id in ($data)
	$color_sizeID_arr = sql_select("select id,size_id,bundle_no,number_start,number_end,size_qty from ppl_cut_lay_bundle where id in ( $data[0] ) ");  //where id in ($data)

	$sql_name = sql_select("select b.buyer_name,b.style_ref_no,b.product_dept,a.po_number from wo_po_details_master b,wo_po_break_down a 
	                      where a.job_no_mst=b.job_no and a.id=$data[5]");
	foreach ($sql_name as $value) {
		$product_dept_name = $value[csf('product_dept')];
		$style_name = $value[csf('style_ref_no')];
		$buyer_name = $value[csf('buyer_name')];
		$po_number = $value[csf('po_number')];
	}
	$sql_cut_name = sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[1]");
	foreach ($sql_cut_name as $cut_value) {
		$table_name = $table_no_library[$cut_value[csf('table_no')]];
		$cut_date = $cut_value[csf('entry_date')];
		$cut_prifix = $cut_value[csf('cut_num_prefix_no')];
		$company_id = $cut_value[csf('company_id')];
		$batch_no = $cut_value[csf('batch_id')];
		$comp_name = return_field_value("company_short_name", "lib_company", "id=$company_id");
		$new_cut_no = $comp_name . "-" . $cut_prifix;
		$bundle_title = "";
	}
	$bundle_calculate_id = return_next_id("id", "ppl_cut_lay_bundle_history", 1);
	$field_array_bundle = "I1,I2,I3,I4,I5,I6,I7,I8,I9,I10";
	$field_array = "id,order_id,mst_id,detls_id,total_bundle,inserted_by,insert_date";
	$data_array_print = "";
	$i = 1;
	foreach ($color_sizeID_arr as $val) {
		$field1 = $val[csf("bundle_no")];
		$field2 = $new_cut_no . "," . $cut_date;
		$field3 = $buyer_library[$buyer_name] . "," . $po_number;
		$field4 = $style_name;
		$field5 = $garments_item[$data[3]];
		$field6 = $color_library[$data[4]];
		$field7 = $size_arr[$val[csf("size_id")]] . "," . $val[csf("bundle_no")];
		$field8 = $val[csf("size_qty")] . "," . $val[csf("number_start")] . "-" . $val[csf("number_end")];
		$field9 = $batch_no;
		if (trim($data_array_print) != "") $data_array_print .= ",";
		$data_array_print .= "('" . $field1 . "','" . $field2 . "','" . $field3 . "','" . $field4 . "','" . $field5 . "','" . $field6 . "','" . $field7 . "','" . $field8 . "','" . $field9 . "','" . $table_name . "')";
		$i++;
	}
	$total_bundle = $i - 1;
	$data_array = "(" . $bundle_calculate_id . ",'" . $data[5] . "','" . $data[1] . "','" . $data[2] . "','" . $total_bundle . "','" . $user_id . "','" . $pc_date_time . "')";
	// echo $data_array;die;
	$rID = sql_insert("ppl_cut_lay_bundle_history", $field_array, $data_array, 1);
	$rID1 = sql_insert("LABEL_OUT", $field_array_bundle, $data_array_print, 1);
	if ($db_type == 0) {
		if ($rID && $rID1) {
			mysql_query("COMMIT");
			echo 0;
		} else {
			mysql_query("ROLLBACK");
			echo 10;
		}
	}
	if ($db_type == 2 || $db_type == 1) {
		if ($rID && $rID1) {
			oci_commit($con);
			echo 0;
		} else {
			oci_rollback($con);
			echo 10;
		}
	}
	disconnect($con);
	die;
}
//bundle_bar_code stiker****************************************************************************************************************************************************

if ($action == "report_bundle_text_file") {
	$data = explode("***", $data);
	$buyer_library = return_library_array("select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
	$table_no_library = return_library_array("select id,table_no  from  lib_cutting_table", "id", "table_no");
	$color_sizeID_arr = sql_select("select id,size_id,bundle_no,number_start,number_end,size_qty from ppl_cut_lay_bundle where id in ( $data[0] ) ");  //where id in ($data)
	$bundle_array = array();
	$sql_name = sql_select("select b.buyer_name,b.style_ref_no,b.product_dept,a.po_number from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.id=$data[6]");
	foreach ($sql_name as $value) {
		$product_dept_name = $value[csf('product_dept')];
		$style_name = $value[csf('style_ref_no')];
		$buyer_name = $value[csf('buyer_name')];
		$po_number = $value[csf('po_number')];
	}
	$sql_cut_name = sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	foreach ($sql_cut_name as $cut_value) {
		$table_name = $cut_value[csf('table_no')];
		$cut_date = change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix = $cut_value[csf('cut_num_prefix_no')];
		$company_id = $cut_value[csf('company_id')];
		$batch_no = $cut_value[csf('batch_id')];
		$comp_name = return_field_value("company_short_name", "lib_company", "id=$company_id");
		$new_cut_no = $comp_name . "-" . $cut_prifix;
		$bundle_title = "";
	}
	foreach (glob("" . "*.zip") as $filename) {
		@unlink($filename);
	}
	$i = 1;
	$zip = new ZipArchive();			// Load zip library	
	$filename = str_replace(".sql", ".zip", 'norsel_bundle.sql');			// Zip name
	if ($zip->open($filename, ZIPARCHIVE::CREATE) !== TRUE) {		// Opening zip file to load files
		$error .=  "* Sorry ZIP creation failed at this time<br/>";
		echo $error;
	}
	$batch_number = "";
	if ($batch_no != "") $batch_number = "(" . $batch_no . ")";
	foreach ($color_sizeID_arr as $val) {
		$file_name = "NORSEL-IMPORT_" . $i;
		$myfile = fopen($file_name . ".txt", "w") or die("Unable to open file!");
		$txt = "Norsel_imp\r\n1\r\n";
		$txt .= $val[csf("bundle_no")] . "\r\n";
		$txt .= "Bundle: " . $val[csf("bundle_no")] . "" . $batch_number . "\r\n";
		$txt .= "Cut No " . $new_cut_no . ", " . $cut_date . "\r\n";
		$txt .= $buyer_library[$buyer_name] . ", Ord: " . $po_number . "\r\n";
		$txt .= "Style " . $style_name . "\r\n";
		$txt .= $garments_item[$data[4]] . "\r\n";
		$txt .= "Color " . trim($color_library[$data[5]]) . "\r\n";
		$txt .= "Size " . $size_arr[$val[csf("size_id")]] . ", Table " . $table_no_library[$table_name] . "\r\n";
		$txt .= "Gmts Qty. " . $val[csf("size_qty")];
		$txt .= ", SL No " . $val[csf("number_start")] . "-" . $val[csf("number_end")];


		fwrite($myfile, $txt);
		fclose($myfile);
		$i++;
	}
	foreach (glob("" . "*.txt") as $filenames) {
		$zip->addFile($file_folder . $filenames);			// Adding files into zip
	}
	$zip->close();

	foreach (glob("" . "*.txt") as $filename) {
		@unlink($filename);
	}
	echo "norsel_bundle";
	exit();
}

if ($action == "print_report_bundle_barcode_eight") {
	echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
	//define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	//require('../../../ext_resource/pdf/code39.php');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');
	$data_all = $data;
	$data = explode("***", $data);
 ?>
	<script>
		var selected_id = new Array;
		var selected_name = new Array;

		function check_all_data() {
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 0;
			for (var i = 1; i <= tbl_row_count; i++) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value(functionParam);
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function set_all() {
			var old = document.getElementById('txt_str').value;
			// alert(old);
			if (old != "") {
				old = old.split("**");

				for (var k = 0; k < old.length; k++) {
					// alert(old[k])
					js_set_value(old[k]);
				}
			}
		}

		function js_set_value(strCon) {
			var splitSTR = strCon.split("_");
			var str = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			//$('#txt_individual_id' + str).val(splitSTR[1]);
			//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');

			toggle(document.getElementById('tr_' + str), '#FFFFCC');

			if (jQuery.inArray(selectID, selected_id) == -1) {
				selected_id.push(selectID);
				selected_name.push(selectDESC);
			} else {
				for (var i = 0; i < selected_id.length; i++) {
					// alert(selected_id[i]+'=='+selectID);
					if (selected_id[i] == selectID) break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
			}
			// alert(selected_id);
			var id = '';
			var name = '';
			var job = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);

			$('#txt_selected_id').val(id);
			$('#txt_selected').val(name);

		}
	</script>
	<?
	$sql_cut_name = sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	foreach ($sql_cut_name as $cut_value) {
		$table_name = $cut_value[csf('table_no')];
		$cut_date = change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix = $cut_value[csf('cut_num_prefix_no')];
		$company_id = $cut_value[csf('company_id')];
		$batch_no = $cut_value[csf('batch_id')];
		$comp_name = return_field_value("company_short_name", "lib_company", "id=$company_id");
		$new_cut_no = $comp_name . "-" . $cut_prifix;
		$bundle_title = "";
	}
	// $sql_bundle_copy="select id,bundle_use_for from ppl_bundle_title where company_id=$company_id";
	// echo  create_list_view("tbl_list_search", "Bundle Use For", "240","240","180",0, $sql_bundle_copy, "js_set_value", "id,bundle_use_for", "", 1, "0", $arr, "bundle_use_for", "","setFilterGrid('tbl_list_search',-1)",'0',"",1);

	$job_id = return_field_value("id", "wo_po_details_master", " job_no='$data[1]' and status_active=1 and is_deleted=0 ", "id");
	$sqlb = "select BODYPART_ID from ppl_cut_lay_bundle_use_for where job_id=$job_id";
	$resb = sql_select($sqlb);
	$selected_bodypart_array = array();
	foreach ($resb as $val) {
		$selected_bodypart_array[$val['BODYPART_ID']] = $val['BODYPART_ID'];
	}

	$sql = "select ID,BUNDLE_USE_FOR from ppl_bundle_title where company_id=$company_id";
	$res = sql_select($sql);
	?>
	<table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="240" id="tbl_list_search">
		<thead>
			<th width="60">Sl</th>
			<th width="180">Bundle Use For</th>
		</thead>
		<tbody id="tbl_body">
			<?
			$i = 1;
			$selected_id = "";
			$selected_name = "";
			$str2 = "";
			foreach ($res as $val) {
				$str = $i . "_" . $val['ID'] . "_" . $val['BUNDLE_USE_FOR'];
				if (!empty($selected_bodypart_array[$val['ID']])) {
					// $bgcolor="yellow";
					$selected_id .= ($selected_id == "") ? $val['ID'] : "," . $val['ID'];
					$selected_name .= ($selected_name == "") ? $val['BUNDLE_USE_FOR'] : "," . $val['BUNDLE_USE_FOR'];
					$str2 .= ($str2 == "") ? $i . "_" . $val['ID'] . "_" . $val['BUNDLE_USE_FOR'] : "**" . $i . "_" . $val['ID'] . "_" . $val['BUNDLE_USE_FOR'];
				} else {
					// $bgcolor="white";
				}

				if ($i % 2 == 0) $bgcolor = "#E9F3FF";
				else $bgcolor = "#FFFFFF";
			?>
				<tr bgcolor="<?= $bgcolor ?>" style="cursor:pointer" id="tr_<?= $i; ?>" onClick="js_set_value('<?= $str; ?>')">
					<td><?= $i; ?></td>
					<td><?= $val['BUNDLE_USE_FOR']; ?></td>
				</tr>
			<?
				$i++;
			}
			?>
		</tbody>
	</table>
	<?
	echo "<input type='hidden' id='txt_selected_id'  />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_str' value='" . $str2 . "'/>";
	?>
	<div class="check_all_container">
		<div style="width:100%">
			<div style="width:50%; float:left" align="left">
				<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()"> Check / Uncheck All
			</div>
			<div style="width:50%; float:left" align="left">
				<input type="button" name="close" id="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px">
			</div>
		</div>
	</div>
	<script type="text/javascript">
		set_all();
	</script>
	<script type="text/javascript">
		var tableFilters = {}
		setFilterGrid("tbl_body", -1, tableFilters);
	</script>
 <?
	exit();
}

if ($action == "print_report_bundle_list_popup") {
	echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
	//define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	//require('../../../ext_resource/pdf/code39.php');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');
 ?>
	<script>
		var selected_id = new Array;
		var selected_name = new Array;

		function check_all_data() {
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 0;
			for (var i = 1; i <= tbl_row_count; i++) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value(functionParam);
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function js_set_value(strCon) {
			var splitSTR = strCon.split("_");
			var str = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			//$('#txt_individual_id' + str).val(splitSTR[1]);
			//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');

			toggle(document.getElementById('tr_' + str), '#FFFFCC');

			if (jQuery.inArray(selectID, selected_id) == -1) {
				selected_id.push(selectID);
				selected_name.push(selectDESC);
			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == selectID) break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
			}
			var id = '';
			var name = '';
			var job = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);

			$('#txt_selected_id').val(id);
			$('#txt_selected').val(name);

		}
	</script>
 <?

	$sql_bundle_copy = "select id,bundle_use_for from ppl_bundle_title where company_id=$data";
	echo  create_list_view("tbl_list_search", "Bundle Use For", "240", "240", "180", 0, $sql_bundle_copy, "js_set_value", "id,bundle_use_for", "", 1, "0", $arr, "bundle_use_for", "", "setFilterGrid('tbl_list_search',-1)", '0', "", 1);
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}

if ($action == "print_qrcode_operation_69_38") // QR 69X38
{
	//echo "1000".$data;die;
	$data = explode("***", $data);
	$detls_id = $data[3];
	$garments_item_name = $garments_item[$data[4]];
	
	// ================================== BUNDLE USE FOR ENTRY ============================
	if ($data[7] != "") // when body part exist
	{
		$con = connect();
		$job_id = return_field_value("id", "wo_po_details_master", " job_no='$data[1]' and status_active=1 and is_deleted=0 ", "id");

		// $delStatus = sql_delete("ppl_cut_lay_bundle_use_for","status_active*is_deleted","0*1",'job_id',$job_id,1);
		$delStatus = execute_query("delete from ppl_cut_lay_bundle_use_for where job_id=$job_id", 0);

		$field_array = "id,job_id,bodypart_id,inserted_by,insert_date";
		$bodypart_id = array_unique(explode(",", $data[7]));
		$data_array = "";
		$j = 0;
		$id = return_next_id("id", "ppl_cut_lay_bundle_use_for", 1);
		foreach ($bodypart_id as $val) {
			if ($j == 0) $data_array = "(" . $id . "," . $job_id . "," . $val . "," . $user_id . ",'" . $pc_date_time . "')";
			else $data_array .= ",(" . $id . "," . $job_id . "," . $val . "," . $user_id . ",'" . $pc_date_time . "')";
			$id = $id + 1;
			$j++;
		}

		$insertStatus = sql_insert("ppl_cut_lay_bundle_use_for", $field_array, $data_array, 0);

		// echo "10**insert into ppl_cut_lay_bundle_use_for (".$field_array.") values ".$data_array;die;

		if ($db_type == 0) {
			if ($insertStatus) {
				mysql_query("COMMIT");
				// echo "1**".str_replace("'","",$hidden_po_break_down_id);
			} else {
				mysql_query("ROLLBACK");
				// echo "10**".str_replace("'","",$hidden_po_break_down_id);
			}
		} else {
			if ($insertStatus) {
				oci_commit($con);
				disconnect($con);
				// echo "1**".str_replace("'","",$hidden_po_break_down_id);
			} else {
				oci_rollback($con);
				disconnect($con);
				// echo "10**".str_replace("'","",$hidden_po_break_down_id);
			}
		}
	}
	// =========================== end =================================
	$color_library = return_library_array("SELECT id, color_name from lib_color where id=$data[5]", "id", "color_name");
	// $country_arr = return_library_array("SELECT id, country_name from lib_country", 'id', 'country_name');
	// $brand_arr = return_library_array("SELECT id, brand_name from lib_buyer_brand", 'id', 'brand_name');
	$shade_arr = return_library_array("SELECT id, shade from PRO_ROLL_DETAILS where mst_id=$data[2] and entry_form=715", 'id', 'shade');
	$working_comp_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$size_arr = return_library_array("select id, size_name from lib_size", 'id', 'size_name');


	$color_sizeID_arr = sql_select("SELECT a.id,a.size_id,a.bundle_no,a.barcode_no,a.order_id,a.number_start,a.number_end,a.size_qty,a.country_id,a.pattern_no,a.roll_no,b.color_id,a.roll_id,b.order_cut_no
	from ppl_cut_lay_bundle a, ppl_cut_lay_dtls b
	where a.dtls_id=b.id and a.status_active=1 and b.status_active=1  and a.id in ($data[0])
	order by b.color_id,a.id");

	//  echo "SELECT a.id,a.size_id,a.bundle_no,a.barcode_no,a.order_id,a.number_start,a.number_end,a.size_qty,a.country_id,a.pattern_no,a.roll_no,b.bundle_sequence,b.color_id
	//  from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b
	//  where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0])
	//  order by b.bundle_sequence,a.id";
	foreach ($color_sizeID_arr as $val_qty) {
		$total_cut_qty += $val_qty[csf('size_qty')];
		$total_size_qty_arr[$val_qty[csf('size_id')]] += $val_qty[csf('size_qty')];
		$order_id_arr[$val_qty[csf('order_id')]] = $val_qty[csf('order_id')];

		$ratio_arr = $val_qty[csf('pattern_no')];
	}

	$bundle_array = array();
	//return_library_array( "select id,short_name from lib_buyer where id=$buyer_name ", "id", "short_name");
	$sql_cut_name = ("SELECT a.entry_date, a.table_no, a.cut_num_prefix_no,a.batch_id,a.company_id,a.cutting_no,b.order_cut_no,b.gmt_item_id,b.color_id,a.floor_id,b.roll_data,working_company_id
	from ppl_cut_lay_mst a, ppl_cut_lay_dtls b
	where a.id=b.mst_id and a.id=$data[2] and a.status_active=1 and b.status_active=1");
	//echo $sql_cut_name;die;

	$item_id_arr = array();
	$color_id_arr = array();
	foreach (sql_select($sql_cut_name) as $cut_value) {
		$ful_cut_no 		= $cut_value[csf('cutting_no')];
		$table_id 			= $cut_value[csf('table_no')];
		$cut_date 			= ($cut_value[csf('entry_date')]);
		$cut_prifix 		= $cut_value[csf('cut_num_prefix_no')];
		$company_id 		= $cut_value[csf('company_id')];
		$working_company    = $working_comp_library[$cut_value[csf('working_company_id')]];
		$batch_no 			= $cut_value[csf('batch_id')];

		$order_cut_no		= $cut_value[csf('order_cut_no')];
		$floor_id		= $cut_value[csf('floor_id')];
		$comp_name 			= return_field_value("company_short_name", "lib_company", "id=$company_id");
		$new_cut_no 		= $comp_name . "-" . $cut_prifix;
		$bundle_title 		= "";
		$item_id_arr[$cut_value[csf('gmt_item_id')]] = $cut_value[csf('gmt_item_id')];
		$color_id_arr[$cut_value[csf('color_id')]] = $cut_value[csf('color_id')];
	}
	$item_id_cond = where_con_using_array($item_id_arr, 0, "c.item_number_id");
	$color_id_cond = where_con_using_array($color_id_arr, 0, "c.color_number_id");
	$order_id_cond = where_con_using_array($order_id_arr, 0, "c.po_break_down_id");

	$floor_arr = return_library_array("SELECT id, floor_name from lib_prod_floor", 'id', 'floor_name');

	$sql_name = sql_select("SELECT b.buyer_name, b.style_ref_no, b.product_dept, a.po_number,a.grouping, a.id,b.brand_id, b.fit_id, c.article_number
	from wo_po_details_master b, wo_po_break_down a, wo_po_color_size_breakdown c
	where a.job_id=b.id and a.id=c.po_break_down_id and a.job_no_mst='" . $data[1] . "' $item_id_cond $color_id_cond $order_id_cond ");
	foreach ($sql_name as $value) {
		$product_dept_name 						= $value[csf('product_dept')];
		$style_name 							= $value[csf('style_ref_no')];
		$buyer_name 							= $value[csf('buyer_name')];
		$brand_id 								= $value[csf('brand_id')];
		$article_number 						= $value[csf('article_number')];
		$fit 									= $value[csf('fit_id')];
		$po_number_arr[$value[csf('id')]] 		= $value[csf('po_number')];
		$int_ref_arr[$value[csf('id')]] 		= $value[csf('grouping')];
	}
	$buyer_short_name = return_field_value("short_name", "lib_buyer", "id=$buyer_name ");

	$table_no = return_field_value("table_no", "lib_cutting_table", "id=$table_id ");
	//return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	?>


	<?
	$PNG_TEMP_DIR = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'qrcode_image' . DIRECTORY_SEPARATOR . $ful_cut_no . DIRECTORY_SEPARATOR;
	$PNG_WEB_DIR = 'qrcode_image/' . $ful_cut_no . '/';

	foreach (glob($PNG_WEB_DIR . "*.png") as $filename) {
		@unlink($filename);
	}

	if (!file_exists($PNG_TEMP_DIR)) mkdir($PNG_TEMP_DIR);

	$filename = $PNG_TEMP_DIR . 'test.png';
	$errorCorrectionLevel = 'L';
	$matrixPointSize = 4;

	include "../../../ext_resource/phpqrcode/qrlib.php";
	require_once("../../../ext_resource/mpdf60/mpdf.php");

	$mpdf = new mPDF(
		'',    // mode - default ''
		array(38, 69),		// array(65,210),    // format - A4, for example, default ''
		5,     // font size - default 0
		'',    // default font family
		2,    // margin_left
		2,    // margin right
		2,     // margin top
		1,    // margin bottom
		0,     // margin header
		0,     // margin footer
		'L'
	);

	if ($data[7] == "") $data[7] = 0;
	$i 		= 1;
	$html 	= '';
	$total_number_of_bundle = count($color_sizeID_arr);
	$sql_bundle_copy = sql_select("SELECT id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
	$bp_library = return_library_array("SELECT id, bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])", 'id', 'bundle_use_for');
	$bp_arr = explode(",",$data[7]);
	foreach ($bp_arr as $key => $r) 
	{
		if (count($sql_bundle_copy) != 0) 
		{
			// foreach ($sql_bundle_copy as $inf) 
			// {
				foreach ($color_sizeID_arr as $val) 
				{
					$filename = $PNG_TEMP_DIR . 'test' . md5($val[csf("barcode_no")]) . '.png';
					QRcode::png($val[csf("barcode_no")], $filename, $errorCorrectionLevel, $matrixPointSize, 2);
					$po_number = $po_number_arr[$val[csf('order_id')]];
					$int_ref = $int_ref_arr[$val[csf('order_id')]];
					$country_name = $country_arr[$val[csf('country_id')]];
					$bundle_array[$i] = $val[csf("barcode_no")];
					$size_array = $val[csf("size_id")];

					$mpdf->AddPage(
						'',    // mode - default ''
						array(38, 69),		// array(65,210),    // format - A4, for example, default ''
						3,     // font size - default 0
						'',    // default font family
						1,    // margin_left
						1,    // margin right
						1,     // margin top
						1,    // margin bottom
						1,     // margin header
						1,     // margin footer
						'L'
					);

					$html .= '
						<table width="100%" cellpadding="0" cellspacing="0" class="" style="font-size:9.5px; font-weight:bold;margin:0px" rules="all" id="" border="1" align="center">

							<tr>
								<td width="55%">' . $val[csf("barcode_no")] . '</td>
								<td rowspan="7"  width=""  align="center">

									<div>
										<img src="' . $PNG_WEB_DIR . basename($filename) . '" height="90" width="">
									</div>
								</td>

							</tr>

							<tr>
								<td>BY:' . $buyer_short_name . '</td>
							</tr>
							<tr>
								<td>STY:' . substr($style_name, 0, 13) . ' </td>
							</tr>
							<tr>

								<td>CUT NO:' . $val[csf("order_cut_no")] . '</td>
							</tr>

							<tr>
								<td>SH:'. $shade_arr[$val[csf("roll_id")]] . '</td>
							</tr>

							<tr>
								<td>BUN:' . $val[csf("bundle_no")] . '</td>
							</tr>

							<tr>
								<td>P/N:' . $bp_library[$r] . '</td>
							</tr>					

							<tr>
								<td>SIZE:' . substr($size_arr[$val[csf("size_id")]] ,0,13) .'('.$val[csf("pattern_no")]. ')</td>
								<td>STKR:' . substr($val[csf("number_start")], 0, 20) . '-' . substr($val[csf("number_end")], 0, 20) . '</td>
							</tr>

							<tr>
								<td>QTY:' . $val[csf("size_qty")] . '</td>
								<td>PO:' . substr($po_number, 0, 18) . '</td>
							</tr>

							<tr>
								<td colspan="2"  style="font-size: 9px; border:0px solid #000;">COLOR:' . substr($color_library[$data[5]], 0, 35) . '</td>
							</tr>
						</table>';
					$mpdf->WriteHTML($html);
					$html = '';
					$i++;
				}
			// }
		} 
		else 
		{
			foreach ($color_sizeID_arr as $val) 
			{

				$filename = $PNG_TEMP_DIR . 'test' . md5($val[csf("barcode_no")]) . '.png';
				QRcode::png($val[csf("barcode_no")], $filename, $errorCorrectionLevel, $matrixPointSize, 2);
				$po_number = $po_number_arr[$val[csf('order_id')]];
				$int_ref = $int_ref_arr[$val[csf('order_id')]];
				$country_name = $country_arr[$val[csf('country_id')]];
				$bundle_array[$i] = $val[csf("barcode_no")];
				$size_array = $val[csf("size_id")];

				$mpdf->AddPage(
					'',    // mode - default ''
					array(38, 69),		// array(65,210),    // format - A4, for example, default ''
					3,     // font size - default 0
					'',    // default font family
					1,    // margin_left
					1,    // margin right
					1,     // margin top
					1,    // margin bottom
					1,     // margin header
					1,     // margin footer
					'L'
				);

				$html .= '
					<table width="100%" cellpadding="0" cellspacing="0" class="" style="font-size:9.5px; font-weight:bold;margin:0px" rules="all" id="" border="1" align="center">

						<tr>
							<td width="55%">' . $val[csf("barcode_no")] . '</td>
							<td rowspan="7"  width=""  align="center">

								<div>
									<img src="' . $PNG_WEB_DIR . basename($filename) . '" height="90" width="">
								</div>
							</td>

						</tr>

						<tr>
							<td>BY:' . $buyer_short_name . '</td>
						</tr>
						<tr>
							<td>STY:' . substr($style_name, 0, 13) . ' </td>
						</tr>
						<tr>

							<td>CUT NO:' . $val[csf("order_cut_no")] . '</td>
						</tr>

						<tr>
							<td>SH:'. $shade_arr[$val[csf("roll_id")]] . '</td>
						</tr>

						<tr>
							<td>BUN:' . $val[csf("bundle_no")] . '</td>
						</tr>

						<tr>
							<td>P/N:' . $inf["BUNDLE_USE_FOR"] . '</td>
						</tr>					

						<tr>
							<td>SIZE:' . substr($size_arr[$val[csf("size_id")]] ,0,13) .'('.$val[csf("pattern_no")]. ')</td>
							<td>STKR:' . substr($val[csf("number_start")], 0, 20) . '-' . substr($val[csf("number_end")], 0, 20) . '</td>
						</tr>

						<tr>
							<td>QTY:' . $val[csf("size_qty")] . '</td>
							<td>PO:' . substr($po_number, 0, 18) . '</td>
						</tr>

						<tr>
							<td colspan="2"  style="font-size: 9px; border:0px solid #000;">COLOR:' . substr($color_library[$data[5]], 0, 35) . '</td>
						</tr>
					</table>';
				$mpdf->WriteHTML($html);
				$html = '';
				$i++;
			}
		}
	}
	//$mpdf->WriteHTML($html);
	foreach (glob("*.pdf") as $filename) {
		@unlink($filename);
	}
	$name = 'lotRatioEntry_' . date('j-M-Y_h-iA') . '_' . $user_id . '.pdf';
	$mpdf->Output($name, 'F');
	echo "1###$name";
	fn_delete_dir_with_files("qrcode_image/".$ful_cut_no);

	exit();
}

if ($action == "print_barcode_eight") {
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	require('../../../ext_resource/pdf/code39.php');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');

	$data = explode("***", $data);
	$detls_id = $data[3];

	// ================================== BUNDLE USE FOR ENTRY ============================
	if ($data[7] != "") // when body part exist
	{
		$con = connect();
		$job_id = return_field_value("id", "wo_po_details_master", " job_no='$data[1]' and status_active=1 and is_deleted=0 ", "id");

		// $delStatus = sql_delete("ppl_cut_lay_bundle_use_for","status_active*is_deleted","0*1",'job_id',$job_id,1);
		$delStatus = execute_query("delete from ppl_cut_lay_bundle_use_for where job_id=$job_id", 0);

		$field_array = "id,job_id,bodypart_id,inserted_by,insert_date";
		$bodypart_id = array_unique(explode(",", $data[7]));
		$data_array = "";
		$j = 0;
		$id = return_next_id("id", "ppl_cut_lay_bundle_use_for", 1);
		foreach ($bodypart_id as $val) {
			if ($j == 0) $data_array = "(" . $id . "," . $job_id . "," . $val . "," . $user_id . ",'" . $pc_date_time . "')";
			else $data_array .= ",(" . $id . "," . $job_id . "," . $val . "," . $user_id . ",'" . $pc_date_time . "')";
			$id = $id + 1;
			$j++;
		}

		$insertStatus = sql_insert("ppl_cut_lay_bundle_use_for", $field_array, $data_array, 0);

		// echo "10**insert into ppl_cut_lay_bundle_use_for (".$field_array.") values ".$data_array;die;

		if ($db_type == 0) {
			if ($insertStatus) {
				mysql_query("COMMIT");
				// echo "1**".str_replace("'","",$hidden_po_break_down_id);
			} else {
				mysql_query("ROLLBACK");
				// echo "10**".str_replace("'","",$hidden_po_break_down_id);
			}
		} else {
			if ($insertStatus) {
				oci_commit($con);
				// echo "1**".str_replace("'","",$hidden_po_break_down_id);
			} else {
				oci_rollback($con);
				// echo "10**".str_replace("'","",$hidden_po_break_down_id);
			}
		}
	}
	// =========================== end =================================


	$buyer_library = return_library_array("select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
	$table_no_library = return_library_array("select id,table_no  from  lib_cutting_table", "id", "table_no");
	$country_arr = return_library_array("select id, country_name from lib_country", 'id', 'country_name');
	// $sub_process_id=return_field_value2("group_concat(b.sub_process_id)  as sub_process_id ","pro_recipe_entry_dtls b, pro_recipe_entry_mst a",
	$order_cut_no = return_field_value(" order_cut_no ", "ppl_cut_lay_dtls", " id=$detls_id and status_active=1 and is_deleted=0 ", "order_cut_no");
	//echo $order_cut_no;
	$pdf = new PDF_Code39();
	//$pdf->SetFont('Times', '', 20);
	$pdf->AddPage();

	$color_sizeID_arr = sql_select("select a.id,a.size_id,a.bundle_no,a.number_start,a.number_end,a.size_qty,a.country_id,a.roll_no,b.bundle_sequence, a.pattern_no 
	from ppl_cut_lay_bundle  a,ppl_cut_lay_size_dtls b
	where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0]) order by b.bundle_sequence,a.id");
	$i = 10;
	$j = 12;
	$k = 0;
	$bundle_array = array();
	$br = 0;
	$n = 0;
	$sql_name = sql_select("select b.buyer_name,b.style_ref_no,b.product_dept,a.po_number from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.id=$data[6]");
	foreach ($sql_name as $value) {
		$product_dept_name = $value[csf('product_dept')];
		$style_name = $value[csf('style_ref_no')];
		$buyer_name = $value[csf('buyer_name')];
		$po_number = $value[csf('po_number')];
	}

	$sql_cut_name = sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	foreach ($sql_cut_name as $cut_value) {
		$table_name = $cut_value[csf('table_no')];
		$cut_date = change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix = $cut_value[csf('cut_num_prefix_no')];
		$company_id = $cut_value[csf('company_id')];
		$batch_no = $cut_value[csf('batch_id')];
		$comp_name = return_field_value("company_short_name", "lib_company", "id=$company_id");
		$new_cut_no = $comp_name . "-" . $cut_prifix;
		$bundle_title = "";
	}
	if ($data[7] == "") $data[7] = 0;
	$sql_bundle_copy = sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
	$cope_page = 1;
	if (count($sql_bundle_copy) != 0) {
		foreach ($color_sizeID_arr as $val) {
			if ($br == 8) {
				$pdf->AddPage();
				$br = 0;
				$i = 10;
				$j = 12;
				$k = 0;
			}
			foreach ($sql_bundle_copy as $inf) {
				if ($br == 8) {
					$pdf->AddPage();
					$br = 0;
					$i = 10;
					$j = 12;
					$k = 0;
				}

				if ($k > 0 && $k < 2) {
					$i = $i + 105;
				}
				$pdf->Code39($i, $j, $val[csf("bundle_no")]);
				$pdf->Code39($i + 45, $j - 4, "Bundle Card", $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true);
				$pdf->Code39($i + 45, $j + 1,  $inf[csf("bundle_use_for")], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true);
				$pdf->Code39($i + 45, $j + 6, "Roll No: " . $val[csf("roll_no")], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true);
				$pdf->Code39($i, $j + 6, "Cut Sys No: " . $new_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
				$pdf->Code39($i + 38, $j + 6, "Cut Date	 : " . $cut_date, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
				$pdf->Code39($i, $j + 11,  "Buyer : " . $buyer_library[$buyer_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
				$pdf->Code39($i + 38, $j + 11, "Ord:" . $po_number, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
				$pdf->Code39($i, $j + 16,  "Style Ref  :  " . $style_name, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
				$pdf->Code39($i + 38, $j + 21, "Item :  " . $garments_item[$data[4]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
				//$pdf->Code39($i+40, $j+21, "Table No :  ".$table_no_library[$table_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i, $j + 21, "Size :  " . $size_arr[$val[csf("size_id")]] . "(" . $val[csf("pattern_no")] . ")", $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
				$pdf->Code39($i, $j + 26, "Table No :  " . $table_no_library[$table_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
				$pdf->Code39($i + 38, $j + 26, "Color	:  " . $color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
				$pdf->Code39($i + 38, $j + 31, "Bundle No:" . $val[csf("bundle_no")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
				$pdf->Code39($i, $j + 31, "Batch No: " . $batch_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
				//$pdf->Code39($i+38, $j+36, "Dept : ".$product_dept[$product_dept_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i + 38, $j + 36, "Gmts. No :  " . $val[csf("number_start")] . "-" . $val[csf("number_end")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
				$pdf->Code39($i, $j + 36, "Gmts. Qnty : " . $val[csf("size_qty")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
				$pdf->Code39($i, $j + 42, "Order Cut No: " . $order_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
				$pdf->Code39($i + 38, $j + 42, "Country: " . $country_arr[$val[csf("country_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
				$k++;

				if ($k == 2) {
					$k = 0;
					$i = 10;
					$j = $j + 67;
				}
				$br++;
				$cope_page++;
			}
			// $br=8;

		}
	} else {
		foreach ($color_sizeID_arr as $val) {
			if ($br == 8) {
				$pdf->AddPage();
				$br = 0;
				$i = 10;
				$j = 12;
				$k = 0;
			}
			if ($k > 0 && $k < 2) {
				$i = $i + 105;
			}
			$pdf->Code39($i, $j, $val[csf("bundle_no")]);
			$pdf->Code39($i + 45, $j - 4, "Roll No: " . $val[csf("roll_no")], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true);
			$pdf->Code39($i + 45, $j + 1,  $inf[csf("bundle_use_for")], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true);
			$pdf->Code39($i, $j + 6, "Cutting No: " . $new_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
			$pdf->Code39($i + 38, $j + 6, "Cut Date	 : " . $cut_date, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
			$pdf->Code39($i, $j + 11,  "Buyer : " . $buyer_library[$buyer_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
			$pdf->Code39($i + 38, $j + 11, "Ord:" . $po_number, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
			$pdf->Code39($i, $j + 16,  "Style Ref  :  " . $style_name, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
			$pdf->Code39($i + 38, $j + 21, "Item :  " . $garments_item[$data[4]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
			//$pdf->Code39($i+40, $j+21, "Table No :  ".$table_no_library[$table_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i, $j + 21, "Size :  " . $size_arr[$val[csf("size_id")]] . "(" . $val[csf("pattern_no")] . ")", $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
			$pdf->Code39($i, $j + 26, "Table No :  " . $table_no_library[$table_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
			$pdf->Code39($i + 38, $j + 26, "Color	:  " . $color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
			$pdf->Code39($i + 38, $j + 31, "Bundle No:" . $val[csf("bundle_no")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);

			$pdf->Code39($i, $j + 31, "Batch No: " . $batch_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);

			//$pdf->Code39($i+38, $j+36, "Dept : ".$product_dept[$product_dept_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i + 38, $j + 36, "Gmts. No :  " . $val[csf("number_start")] . "-" . $val[csf("number_end")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
			$pdf->Code39($i, $j + 36, "Gmts. Qnty : " . $val[csf("size_qty")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);

			$k++;

			if ($k == 2) {
				$k = 0;
				$i = 10;
				$j = $j + 67;
			}
			$br++;
		}
	}
	foreach (glob("" . "*.pdf") as $filename) {
		@unlink($filename);
	}
	$name = 'bundle_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output("" . $name, 'F');
	echo $name;
	exit();
}

if ($action == "print_barcode_one_urmi_real") {
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	require('../../../ext_resource/pdf/code39.php');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');//order_cut_no

	$data = explode("***", $data);
	$detls_id = $data[3];
	$batch_id = return_field_value("batch_id", "ppl_cut_lay_dtls", "id=$detls_id");
	$buyer_library = return_library_array("select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
	$table_no_library = return_library_array("select id,table_no  from  lib_cutting_table", "id", "table_no");
	$country_arr = return_library_array("select id, short_name from lib_country", 'id', 'short_name');
	$order_cut_no = return_field_value("order_cut_no", "ppl_cut_lay_dtls", "id=$detls_id and status_active=1 and is_deleted=0", "order_cut_no");
	//echo $order_cut_no;
	$pdf = new PDF_Code39('P', 'mm', 'a10');
	//$pdf=new PDF_Code39(); 
	//$pdf->SetFont('Arial', '', 1000);
	$pdf->AddPage();


	$bacth_array = array();
	$batchData = sql_select("select id, batch_no, extention_no from pro_batch_create_mst where entry_form in(0,7,37,66,68)");
	foreach ($batchData as $row) {
		$ext = '';
		if ($row[csf('extention_no')] > 0) {
			$ext = '-' . $row[csf('extention_no')];
		}
		$bacth_array[$row[csf('id')]] = $row[csf('batch_no')] . $ext;
	}


	$color_sizeID_arr = sql_select("select a.id, a.size_id, a.bundle_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no
	from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0]) order by b.bundle_sequence,a.id");

	foreach ($color_sizeID_arr as $row) {
		$test_data[$row[csf("roll_id")]] = $row[csf("roll_id")];
	}
	//print_r($test_data);die;
	//echo $data[6].jahid;die;
	$i = 2;
	$j = 2;
	$k = 0;
	$bundle_array = array();
	$br = 0;
	$n = 0;
	$sql_name = sql_select("select b.buyer_name,b.style_ref_no,b.product_dept,a.po_number, a.id as po_id from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.id in(" . $data[6] . ")");
	$po_data_arr = array();
	foreach ($sql_name as $value) {
		/*$product_dept_name=$value[csf('product_dept')];
		$style_name=$value[csf('style_ref_no')];
		$buyer_name=$value[csf('buyer_name')];
		$po_number=$value[csf('po_number')];*/

		$po_data_arr[$value[csf('po_id')]]["style_ref_no"] = $value[csf('style_ref_no')];
		$po_data_arr[$value[csf('po_id')]]["buyer_name"] = $value[csf('buyer_name')];
		$po_data_arr[$value[csf('po_id')]]["po_number"] = $value[csf('po_number')];
	}
	unset($sql_name);

	$roll_sql = sql_select("select roll_id, batch_no from pro_roll_details where entry_form=715 and status_active=1");
	$roll_data_arr = array();
	foreach ($roll_sql as $row) {
		$roll_data_arr[$row[csf("roll_id")]] = $row[csf("batch_no")];
	}
	unset($roll_sql);

	//print_r($roll_data_arr);die;

	$sql_cut_name = sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	foreach ($sql_cut_name as $cut_value) {
		$table_name = $cut_value[csf('table_no')];
		$cut_date = change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix = $cut_value[csf('cut_num_prefix_no')];
		$company_id = $cut_value[csf('company_id')];
		//$batch_no=$cut_value[csf('batch_id')];
		$comp_name = return_field_value("company_short_name", "lib_company", "id=$company_id");
		$new_cut_no = $comp_name . "-" . $cut_prifix;
		$bundle_title = "";
	}
	if ($data[7] == "") $data[7] = 0;
	$sql_bundle_copy = sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
	if (count($sql_bundle_copy) != 0) {
		foreach ($sql_bundle_copy as $inf) {
			if ($br == 1) {
				$pdf->AddPage();
				$br = 0;
				$i = 2;
				$j = 2;
				$k = 0;
			}
			foreach ($color_sizeID_arr as $val) {
				if ($br == 1) {
					$pdf->AddPage();
					$br = 0;
					$i = 2;
					$j = 2;
					$k = 0;
				}

				//BNDL
				$style_name = $po_data_arr[$val[csf('order_id')]]["style_ref_no"];
				$buyer_name = $po_data_arr[$val[csf('order_id')]]["buyer_name"];
				$po_number = $po_data_arr[$val[csf('order_id')]]["po_number"];
				$batch_no = $roll_data_arr[$val[csf('roll_id')]];
				$bundle_no_prifix = $val[csf("bundle_num_prefix_no")];

				if ($val[csf('is_excess')] == 1) $country = "EXCESS";
				else $country = $country_arr[$val[csf("country_id")]];

				$pdf->Code40($i, $j - 2, $buyer_library[$buyer_name] . "  COUN# " . $country . "  QTY# " . $val[csf("size_qty")], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true, 8);
				$pdf->Code40($i, $j + 1.4, "STKR# " . $val[csf("number_start")] . "-" . $val[csf("number_end")] . "  STY# " . $style_name, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true, 8);
				$pdf->Code40($i, $j + 4.8, $val[csf("bundle_no")] . "  PO# " . $po_number, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true, 8);
				$pdf->Code40($i, $j + 8.2, "COLOR# " . $color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true, 8);
				$pdf->Code40($i, $j + 11.6, "PART# " . $inf[csf("bundle_use_for")] . "  Batch# " . $batch_no, $ext = true, $cks = false, $w = 0.1, $h = 1, $wide = true, true, 8);
				$pdf->Code40($i, $j + 15, "CUT & ROLL# " . $order_cut_no . " & " . $val[csf("roll_no")] . "  SIZE# " . $size_arr[$val[csf("size_id")]] . "(" . $val[csf("pattern_no")] . ")", $ext = true, $cks = false, $w = 0.1, $h = 1, $wide = true, true, 8);
				$pdf->Code39($i, $j + 21, $val[csf("bundle_no")]);
				//$pdf->Code40($i, $j+22, $val[csf("bundle_no")],$w =0.2, $h = 8);


				$k++;
				$i = 2;
				$j = $j + 21;
				/*if($k==2)
				{
					$k=0; $i=10; $j=$j+75;
				}*/

				$br++;
			}
		}
	} else {
		foreach ($color_sizeID_arr as $val) {
			if ($br == 1) {
				$pdf->AddPage();
				$br = 0;
				$i = 2;
				$j = 2;
				$k = 0;
			}

			$style_name = $po_data_arr[$val[csf('order_id')]]["style_ref_no"];
			$buyer_name = $po_data_arr[$val[csf('order_id')]]["buyer_name"];
			$po_number = $po_data_arr[$val[csf('order_id')]]["po_number"];
			$batch_no = $roll_data_arr[$val[csf('roll_id')]];
			$bundle_no_prifix = $val[csf("bundle_num_prefix_no")];
			/*$bundle_no_arr=explode("-",$val[csf("bundle_no")]);
			foreach ( $bundle_no_arr as $key=>$bdl_value)
			{
				if($key>=3) {
					if( $bundle_no_prifix!='') $bundle_no_prifix.="-";
					$bundle_no_prifix.=$bdl_value;
				}
			}*/

			if ($val[csf('is_excess')] == 1) $country = "EXCESS";
			else $country = $country_arr[$val[csf("country_id")]];

			$pdf->Code40($i, $j - 2, $buyer_library[$buyer_name] . "  COUN# " . $country . "  QTY# " . $val[csf("size_qty")], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true, 8);
			$pdf->Code40($i, $j + 1.4, "STKR# " . $val[csf("number_start")] . "-" . $val[csf("number_end")] . "  STY# " . $style_name, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true, 8);
			$pdf->Code40($i, $j + 4.8, $val[csf("bundle_no")] . "  PO# " . $po_number, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true, 8);
			$pdf->Code40($i, $j + 8.2, "COLOR# " . $color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true, 8);
			$pdf->Code40($i, $j + 11.6, "PART# " . $inf[csf("bundle_use_for")] . "  Batch# " . $batch_no, $ext = true, $cks = false, $w = 0.1, $h = 1, $wide = true, true, 8);
			$pdf->Code40($i, $j + 15, "CUT & ROLL# " . $order_cut_no . " & " . $val[csf("roll_no")] . "  SIZE# " . $size_arr[$val[csf("size_id")]] . "(" . $val[csf("pattern_no")] . ")", $ext = true, $cks = false, $w = 0.1, $h = 1, $wide = true, true, 8);
			//$pdf->Code40($i, $j+22, $val[csf("bundle_no")],$w =0.2, $h = 8);
			$pdf->Code39($i, $j + 21, $val[csf("bundle_no")]);
			$k++;
			$i = 2;
			$j = $j + 20;
			/*if($k==2)
			{
				$k=0; $i=10; $j=$j+75;
			}*/

			$br++;
		}
	}

	foreach (glob("" . "*.pdf") as $filename) {
		@unlink($filename);
	}
	$name = 'bundle_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output("" . $name, 'F');
	echo $name;
	exit();
}

if ($action == "print_barcode_one_urmi_bk") {
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	require('../../../ext_resource/pdf/code39.php');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');//order_cut_no

	$data = explode("***", $data);


	// ================================== BUNDLE USE FOR ENTRY ============================
	if ($data[7] != "") // when body part exist
	{
		$con = connect();
		$job_id = return_field_value("id", "wo_po_details_master", " job_no='$data[1]' and status_active=1 and is_deleted=0 ", "id");

		// $delStatus = sql_delete("ppl_cut_lay_bundle_use_for","status_active*is_deleted","0*1",'job_id',$job_id,1);
		$delStatus = execute_query("delete from ppl_cut_lay_bundle_use_for where job_id=$job_id", 0);

		$field_array = "id,job_id,bodypart_id,inserted_by,insert_date";
		$bodypart_id = array_unique(explode(",", $data[7]));
		$data_array = "";
		$j = 0;
		$id = return_next_id("id", "ppl_cut_lay_bundle_use_for", 1);
		foreach ($bodypart_id as $val) {
			if ($j == 0) $data_array = "(" . $id . "," . $job_id . "," . $val . "," . $user_id . ",'" . $pc_date_time . "')";
			else $data_array .= ",(" . $id . "," . $job_id . "," . $val . "," . $user_id . ",'" . $pc_date_time . "')";
			$id = $id + 1;
			$j++;
		}

		$insertStatus = sql_insert("ppl_cut_lay_bundle_use_for", $field_array, $data_array, 0);

		// echo "10**insert into ppl_cut_lay_bundle_use_for (".$field_array.") values ".$data_array;die;

		if ($db_type == 0) {
			if ($insertStatus) {
				mysql_query("COMMIT");
				// echo "1**".str_replace("'","",$hidden_po_break_down_id);
			} else {
				mysql_query("ROLLBACK");
				// echo "10**".str_replace("'","",$hidden_po_break_down_id);
			}
		} else {
			if ($insertStatus) {
				oci_commit($con);
				// echo "1**".str_replace("'","",$hidden_po_break_down_id);
			} else {
				oci_rollback($con);
				// echo "10**".str_replace("'","",$hidden_po_break_down_id);
			}
		}
	}
	// =========================== end =================================

	$detls_id = $data[3];
	$batch_id = return_field_value("batch_id", "ppl_cut_lay_dtls", "id=$detls_id");
	$buyer_library = return_library_array("select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
	$table_no_library = return_library_array("select id,table_no  from  lib_cutting_table", "id", "table_no");
	$country_arr = return_library_array("select id, short_name from lib_country", 'id', 'short_name');
	$lib_season_arr = return_library_array("select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0", 'id', 'season_name');
	$order_cut_no = return_field_value("order_cut_no", "ppl_cut_lay_dtls", "id=$detls_id and status_active=1 and is_deleted=0", "order_cut_no");
	$pdf = new PDF_Code39('P', 'mm', 'a10');
	$pdf->AddPage();


	$bacth_array = array();
	$batchData = sql_select("select id, batch_no, extention_no from pro_batch_create_mst where entry_form in(0,7,37,66,68)");
	foreach ($batchData as $row) {
		$ext = '';
		if ($row[csf('extention_no')] > 0) {
			$ext = '-' . $row[csf('extention_no')];
		}
		$bacth_array[$row[csf('id')]] = $row[csf('batch_no')] . $ext;
	}


	$color_sizeID_arr = sql_select("SELECT a.id, a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no
	from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0])
	group by a.id, a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no order by b.bundle_sequence,a.id");
	foreach ($color_sizeID_arr as $row) {
		$test_data[$row[csf("roll_id")]] = $row[csf("roll_id")];
	}
	$i = 2;
	$j = 0;
	$k = 0;
	$bundle_array = array();
	$br = 0;
	$n = 0;
	$sql_name = sql_select("SELECT b.buyer_name, b.client_id, b.style_ref_no, b.product_dept, b.season_buyer_wise as season_matrix, a.po_number, a.id as po_id,a.t_year from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.id in(" . $data[6] . ")");
	$po_data_arr = array();
	foreach ($sql_name as $value) {
		$po_data_arr[$value[csf('po_id')]]["style_ref_no"] = $value[csf('style_ref_no')];
		$po_data_arr[$value[csf('po_id')]]["buyer_name"] = $value[csf('buyer_name')];
		$po_data_arr[$value[csf('po_id')]]["client_id"] = $value[csf('client_id')];
		$po_data_arr[$value[csf('po_id')]]["po_number"] = $value[csf('po_number')];
		$po_data_arr[$value[csf('po_id')]]["t_year"] = $value[csf('t_year')];
		$matrix_season = $value[csf('season_matrix')];
	}
	unset($sql_name);
	//echo "select roll_id, batch_no, shade from pro_roll_details where mst_id='$data[2]' and entry_form=289 and status_active=1";
	$roll_sql = sql_select("select roll_id, batch_no, shade from pro_roll_details where mst_id='$data[2]' and entry_form=715 and status_active=1");
	$roll_data_arr = array();
	foreach ($roll_sql as $row) {
		$roll_data_arr[$row[csf("roll_id")]]['batch'] = $row[csf("batch_no")];
		$roll_data_arr[$row[csf("roll_id")]]['shade'] = $row[csf("shade")];
	}
	//print_r($roll_data_arr); die;
	unset($roll_sql);

	$sql_cut_name = sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	foreach ($sql_cut_name as $cut_value) {
		$table_name = $cut_value[csf('table_no')];
		$cut_date = change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix = $cut_value[csf('cut_num_prefix_no')];
		$company_id = $cut_value[csf('company_id')];
		$comp_name = return_field_value("company_short_name", "lib_company", "id=$company_id");
		$new_cut_no = $comp_name . "-" . $cut_prifix;
		$bundle_title = "";
	}
	if ($data[7] == "") $data[7] = 0;
	$seq = explode(",", $data[7]);
	$seq_first = $seq[0];

	$sql_bundle_copy = sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
	$bundle_use_for = "";
	foreach ($sql_bundle_copy as $val) {
		$bundle_use_for .= ($bundle_use_for == "") ? $val[csf('bundle_use_for')] : "," . $val[csf('bundle_use_for')];
	}


	if ($data[8] == 1) // bitton Sticker 1/Page V2
	{
		if (count($sql_bundle_copy) != 0) {
			foreach ($sql_bundle_copy as $inf) {
				if ($br == 1) {
					$pdf->AddPage();
					$br = 0;
					$i = 2;
					$j = 0;
					$k = 0;
				}
				foreach ($color_sizeID_arr as $val) {
					if ($br == 1) {
						$pdf->AddPage();
						$br = 0;
						$i = 2;
						$j = 0;
						$k = 0;
					}
					if ($seq_first == $inf[csf("id")]) $symb = "@@";
					else $symb = "";
					//BNDL
					$style_name = $po_data_arr[$val[csf('order_id')]]["style_ref_no"];
					$buyer_name = $po_data_arr[$val[csf('order_id')]]["buyer_name"];
					$client_id = $po_data_arr[$val[csf('order_id')]]["client_id"];
					$po_number = $po_data_arr[$val[csf('order_id')]]["po_number"];
					$batch_no = $roll_data_arr[$val[csf('roll_id')]]['batch'];
					$shade = $roll_data_arr[$val[csf('roll_id')]]['shade'];

					$bundle_no_prifix = $val[csf("bundle_num_prefix_no")];
					$buyer_name_str = "";
					if ($client_id != 0) $buyer_name_str = $buyer_library[$buyer_name] . '-' . $buyer_library[$client_id];
					else $buyer_name_str = $buyer_library[$buyer_name];
					//$dbl_no="17910000000012";
					if ($val[csf('is_excess')] == 1) $country = "EXCESS";
					else $country = $country_arr[$val[csf("country_id")]];

					$pdf->Code40($i, $j - 2, $symb . " " . $buyer_name_str . "  COUN# " . $country . "  QTY# " . $val[csf("size_qty")], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i, $j + 1.2, "STKR# " . $val[csf("number_start")] . "-" . $val[csf("number_end")] . "  STY# " . $style_name, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i, $j + 4.4, $val[csf("bundle_no")] . "  PO# " . $po_number, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i, $j + 7.6, "COLOR# " . $color_library[$data[5]] . ",Shade " . $shade, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i, $j + 10.8, $inf[csf("bundle_use_for")] . "  B# " . $batch_no . "  S# " . $lib_season_arr[$matrix_season], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i, $j + 14, "CUT & ROLL# " . $order_cut_no . " & " . $val[csf("roll_no")] . "  SIZE# " . $size_arr[$val[csf("size_id")]] . "(" . $val[csf("pattern_no")] . ")", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i + 1.3, $j + 17.2, $val[csf("barcode_no")], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
					//$pdf->Code40($i, $j+22, $val[csf("bundle_no")],$w =0.2, $h = 8);
					$pdf->Code39($i + 1.3, $j + 23, $val[csf("barcode_no")]);
					$k++;
					$i = 2;
					$j = $j + 23;
					$br++;
				}
			}
		} else {
			foreach ($color_sizeID_arr as $val) {
				if ($br == 1) {
					$pdf->AddPage();
					$br = 0;
					$i = 2;
					$j = 0;
					$k = 0;
				}
				if ($seq_first == $inf[csf("id")]) $symb = "@@";
				else $symb = "";
				$style_name = $po_data_arr[$val[csf('order_id')]]["style_ref_no"];
				$buyer_name = $po_data_arr[$val[csf('order_id')]]["buyer_name"];
				$po_number = $po_data_arr[$val[csf('order_id')]]["po_number"];
				$batch_no = $roll_data_arr[$val[csf('roll_id')]]['batch'];
				$shade = $roll_data_arr[$val[csf('roll_id')]]['shade'];
				$bundle_no_prifix = $val[csf("bundle_num_prefix_no")];

				if ($val[csf('is_excess')] == 1) $country = "EXCESS";
				else $country = $country_arr[$val[csf("country_id")]];

				$pdf->Code40($i, $j - 2, $symb . " " . $buyer_library[$buyer_name] . "  COUN# " . $country . "  QTY# " . $val[csf("size_qty")], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i, $j + 1.2, "STKR# " . $val[csf("number_start")] . "-" . $val[csf("number_end")] . "  STY# " . $style_name, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i, $j + 4.4, $val[csf("bundle_no")] . "  PO# " . $po_number, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i, $j + 7.6, "COLOR# " . $color_library[$data[5]] . ",Shade " . $shade, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i, $j + 10.8, $inf[csf("bundle_use_for")] . "  B# " . $batch_no . "  S# " . $lib_season_arr[$matrix_season], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i, $j + 14, "CUT & ROLL# " . $order_cut_no . " & " . $val[csf("roll_no")] . "  SIZE# " . $size_arr[$val[csf("size_id")]] . "(" . $val[csf("pattern_no")] . ")", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i + 1.3, $j + 17.2, $val[csf("barcode_no")], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
				//$pdf->Code40($i, $j+22, $val[csf("bundle_no")],$w =0.2, $h = 8);
				$pdf->Code39($i + 1.3, $j + 23, $val[csf("barcode_no")]);
				$k++;
				$i = 2;
				$j = $j + 23;

				$br++;
			}
		}
	} else if ($data[8] == 2) // bitton Sticker 1/Page V3
	{
		if (count($sql_bundle_copy) != 0) {
			foreach ($sql_bundle_copy as $inf) {
				if ($br == 1) {
					$pdf->AddPage();
					$br = 0;
					$i = 2;
					$j = 0;
					$k = 0;
				}
				foreach ($color_sizeID_arr as $val) {
					if ($br == 1) {
						$pdf->AddPage();
						$br = 0;
						$i = 2;
						$j = 0;
						$k = 0;
					}
					if ($seq_first == $inf[csf("id")]) $symb = "@@";
					else $symb = "";
					//BNDL
					$style_name = $po_data_arr[$val[csf('order_id')]]["style_ref_no"];
					$buyer_name = $po_data_arr[$val[csf('order_id')]]["buyer_name"];
					$client_id = $po_data_arr[$val[csf('order_id')]]["client_id"];
					$po_number = $po_data_arr[$val[csf('order_id')]]["po_number"];
					$batch_no = $roll_data_arr[$val[csf('roll_id')]]['batch'];
					$shade = $roll_data_arr[$val[csf('roll_id')]]['shade'];
					$bundle_no_prifix = $val[csf("bundle_num_prefix_no")];
					$buyer_name_str = "";
					if ($client_id != 0) $buyer_name_str = $buyer_library[$buyer_name] . '-' . $buyer_library[$client_id];
					else $buyer_name_str = $buyer_library[$buyer_name];
					//$dbl_no="17910000000012";
					if ($val[csf('is_excess')] == 1) $country = "EXCESS";
					else $country = $country_arr[$val[csf("country_id")]];
					$pdf->Code40($i, $j - 2, $symb . " " . $shade . "  Shade", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i, $j + 1.2, $buyer_name_str . "  COUN# " . $country . "  QTY# " . $val[csf("size_qty")], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i, $j + 4.2, "STKR# " . $val[csf("number_start")] . "-" . $val[csf("number_end")] . "  STY# " . $style_name, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i, $j + 7.3, $val[csf("bundle_no")] . "  PO# " . $po_number, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i, $j + 10, "COLOR# " . $color_library[$data[5]], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i, $j + 12.6, $inf[csf("bundle_use_for")] . "  B# " . $batch_no . "  S# " . $lib_season_arr[$matrix_season], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i, $j + 15.2, "CUT & ROLL# " . $order_cut_no . " & " . $val[csf("roll_no")] . "  SIZE# " . $size_arr[$val[csf("size_id")]] . "(" . $val[csf("pattern_no")] . ")", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i + 1.3, $j + 17.5, $val[csf("barcode_no")], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
					//$pdf->Code40($i, $j+22, $val[csf("bundle_no")],$w =0.2, $h = 8);
					$pdf->Code39($i + 1.3, $j + 23, $val[csf("barcode_no")]);
					$k++;
					$i = 2;
					$j = $j + 23;
					$br++;
				}
			}
		} else {
			foreach ($color_sizeID_arr as $val) {
				if ($br == 1) {
					$pdf->AddPage();
					$br = 0;
					$i = 2;
					$j = 0;
					$k = 0;
				}
				if ($seq_first == $inf[csf("id")]) $symb = "@@";
				else $symb = "";
				$style_name = $po_data_arr[$val[csf('order_id')]]["style_ref_no"];
				$buyer_name = $po_data_arr[$val[csf('order_id')]]["buyer_name"];
				$po_number = $po_data_arr[$val[csf('order_id')]]["po_number"];
				$batch_no = $roll_data_arr[$val[csf('roll_id')]]['batch'];
				$shade = $roll_data_arr[$val[csf('roll_id')]]['shade'];
				$bundle_no_prifix = $val[csf("bundle_num_prefix_no")];

				if ($val[csf('is_excess')] == 1) $country = "EXCESS";
				else $country = $country_arr[$val[csf("country_id")]];
				$pdf->Code40($i, $j - 2, $symb . " " . $shade . "  Shade", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i, $j + 1.2, $buyer_library[$buyer_name] . "  COUN# " . $country . "  QTY# " . $val[csf("size_qty")], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i, $j + 4.4, "STKR# " . $val[csf("number_start")] . "-" . $val[csf("number_end")] . "  STY# " . $style_name, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i, $j + 7.3, $val[csf("bundle_no")] . "  PO# " . $po_number, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i, $j + 10, "COLOR# " . $color_library[$data[5]], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i, $j + 12.6, $inf[csf("bundle_use_for")] . "  B# " . $batch_no . "  S# " . $lib_season_arr[$matrix_season], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i, $j + 15.2, "CUT & ROLL# " . $order_cut_no . " & " . $val[csf("roll_no")] . "  SIZE# " . $size_arr[$val[csf("size_id")]] . "(" . $val[csf("pattern_no")] . ")", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i + 1.3, $j + 17.5, $val[csf("barcode_no")], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
				//$pdf->Code40($i, $j+22, $val[csf("bundle_no")],$w =0.2, $h = 8);
				$pdf->Code39($i + 1.3, $j + 23, $val[csf("barcode_no")]);
				$k++;
				$i = 2;
				$j = $j + 23;

				$br++;
			}
		}
	} else if ($data[8] == 3) // bitton Sticker 1/Page V4
	{
		if (count($sql_bundle_copy) != 0) {
			foreach ($sql_bundle_copy as $inf) {
				if ($br == 1) {
					$pdf->AddPage();
					$br = 0;
					$i = 2;
					$j = 0;
					$k = 0;
				}
				foreach ($color_sizeID_arr as $val) {
					if ($br == 1) {
						$pdf->AddPage();
						$br = 0;
						$i = 2;
						$j = 0;
						$k = 0;
					}
					if ($seq_first == $inf[csf("id")]) $symb = "@@";
					else $symb = "";
					//BNDL
					$style_name = $po_data_arr[$val[csf('order_id')]]["style_ref_no"];
					$buyer_name = $po_data_arr[$val[csf('order_id')]]["buyer_name"];
					$client_id = $po_data_arr[$val[csf('order_id')]]["client_id"];
					$po_number = $po_data_arr[$val[csf('order_id')]]["po_number"];
					$t_year = $po_data_arr[$val[csf('order_id')]]["t_year"];
					$batch_no = $roll_data_arr[$val[csf('roll_id')]]['batch'];
					$shade = $roll_data_arr[$val[csf('roll_id')]]['shade'];
					$bundle_no_prifix = $val[csf("bundle_num_prefix_no")];
					$buyer_name_str = "";
					if ($client_id != 0) $buyer_name_str = $buyer_library[$buyer_name] . '-' . $buyer_library[$client_id];
					else $buyer_name_str = $buyer_library[$buyer_name];
					//$dbl_no="17910000000012";
					if ($val[csf('is_excess')] == 1) $country = "EXCESS";
					else $country = $country_arr[$val[csf("country_id")]];

					$pdf->Code40($i, $j - 2, "BY#" . $buyer_name_str, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i + 30, $j - 2, "Size# " . $size_arr[$val[csf("size_id")]] . "(" . $val[csf("pattern_no")] . ")", $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

					$pdf->Code40($i, $j + 1.2, "STY# " . $style_name, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i + 30, $j + 1.2, "QTY# " . $val[csf("size_qty")], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

					$pdf->Code40($i, $j + 4.2, "CUT & ROLL# " . $order_cut_no . " & " . $val[csf("roll_no")], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i + 30, $j + 4.2, "SH# " . $shade, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

					$pdf->Code40($i, $j + 7.3, "B/NO#" . $batch_no, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i + 30, $j + 7.3, "P/N# " . $inf[csf('bundle_use_for')], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

					$pdf->Code40($i, $j + 10, "BUN# " . $val[csf("bundle_no")], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i + 30, $j + 10, "STKR#" . $val[csf("number_start")] . "-" . $val[csf("number_end")], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

					$pdf->Code40($i, $j + 12.6, "COLOR#" . $color_library[$data[5]], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i + 30, $j + 12.6, "COUN# " . $country, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

					$pdf->Code40($i, $j + 15.2, "S&Y# " . $lib_season_arr[$matrix_season] . "-" . substr($t_year, 2), $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i + 30, $j + 15.2, "PO# " . $po_number, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

					$pdf->Code40($i, $j + 17.5, $val[csf("barcode_no")], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
					//$pdf->Code40($i, $j+22, $val[csf("bundle_no")],$w =0.2, $h = 8);
					$pdf->Code39($i, $j + 23, $val[csf("barcode_no")]);

					$k++;
					$i = 2;
					$j = $j + 23;
					$br++;
				}
			}
		} else {
			foreach ($color_sizeID_arr as $val) {
				if ($br == 1) {
					$pdf->AddPage();
					$br = 0;
					$i = 2;
					$j = 0;
					$k = 0;
				}
				if ($seq_first == $inf[csf("id")]) $symb = "@@";
				else $symb = "";
				$style_name = $po_data_arr[$val[csf('order_id')]]["style_ref_no"];
				$buyer_name = $po_data_arr[$val[csf('order_id')]]["buyer_name"];
				$client_id = $po_data_arr[$val[csf('order_id')]]["client_id"];
				$po_number = $po_data_arr[$val[csf('order_id')]]["po_number"];
				$t_year = $po_data_arr[$val[csf('order_id')]]["t_year"];
				$batch_no = $roll_data_arr[$val[csf('roll_id')]]['batch'];
				$shade = $roll_data_arr[$val[csf('roll_id')]]['shade'];
				$bundle_no_prifix = $val[csf("bundle_num_prefix_no")];
				$buyer_name_str = "";
				if ($client_id != 0) $buyer_name_str = $buyer_library[$buyer_name] . '-' . $buyer_library[$client_id];
				else $buyer_name_str = $buyer_library[$buyer_name];

				if ($val[csf('is_excess')] == 1) $country = "EXCESS";
				else $country = $country_arr[$val[csf("country_id")]];
				$pdf->Code40($i, $j - 2, "BY#" . $buyer_name_str, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i + 30, $j - 2, "Size# " . $size_arr[$val[csf("size_id")]] . "(" . $val[csf("pattern_no")] . ")", $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

				$pdf->Code40($i, $j + 1.2, "STY# " . $style_name, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i + 30, $j + 1.2, "QTY# " . $val[csf("size_qty")], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

				$pdf->Code40($i, $j + 4.2, "CUT & ROLL# " . $order_cut_no . " & " . $val[csf("roll_no")], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i + 30, $j + 4.2, "SH# " . $shade, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

				$pdf->Code40($i, $j + 7.3, "B/NO#" . $batch_no, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i + 30, $j + 7.3, "P/N# " . $sql_bundle_copy[0][csf('bundle_use_for')], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

				$pdf->Code40($i, $j + 10, "BUN# " . $val[csf("bundle_no")], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i + 30, $j + 10, "STKR#" . $val[csf("number_start")] . "-" . $val[csf("number_end")], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

				$pdf->Code40($i, $j + 12.6, "COLOR#" . $color_library[$data[5]], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i + 30, $j + 12.6, "COUN# " . $country, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

				$pdf->Code40($i, $j + 15.2, "S&Y# " . $lib_season_arr[$matrix_season] . "-" . substr($t_year, 2), $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i + 30, $j + 15.2, "PO# " . $po_number, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

				$pdf->Code40($i, $j + 17.5, $val[csf("barcode_no")], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
				//$pdf->Code40($i, $j+22, $val[csf("bundle_no")],$w =0.2, $h = 8);
				$pdf->Code39($i, $j + 23, $val[csf("barcode_no")]);
				$k++;
				$i = 2;
				$j = $j + 23;

				$br++;
			}
		}
	}
	foreach (glob("" . "*.pdf") as $filename) {
		@unlink($filename);
	}
	$name = 'bundle_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output("" . $name, 'F');
	echo $name;
	exit();
}

if ($action == "print_barcode_one_urmi") {
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	require('../../../ext_resource/pdf/code39.php');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');//order_cut_no

	$data = explode("***", $data);
	// echo "<pre>"; print_r($data);
	// ================================== BUNDLE USE FOR ENTRY ============================
	if ($data[7] != "") // when body part exist
	{
		$con = connect();
		$job_id = return_field_value("id", "wo_po_details_master", " job_no='$data[1]' and status_active=1 and is_deleted=0 ", "id");

		// $delStatus = sql_delete("ppl_cut_lay_bundle_use_for","status_active*is_deleted","0*1",'job_id',$job_id,1);
		$delStatus = execute_query("delete from ppl_cut_lay_bundle_use_for where job_id=$job_id", 0);

		$field_array = "id,job_id,bodypart_id,inserted_by,insert_date";
		$bodypart_id = array_unique(explode(",", $data[7]));
		$data_array = "";
		$j = 0;
		$id = return_next_id("id", "ppl_cut_lay_bundle_use_for", 1);
		foreach ($bodypart_id as $val) {
			if ($j == 0) $data_array = "(" . $id . "," . $job_id . "," . $val . "," . $user_id . ",'" . $pc_date_time . "')";
			else $data_array .= ",(" . $id . "," . $job_id . "," . $val . "," . $user_id . ",'" . $pc_date_time . "')";
			$id = $id + 1;
			$j++;
		}

		$insertStatus = sql_insert("ppl_cut_lay_bundle_use_for", $field_array, $data_array, 0);

		// echo "10**insert into ppl_cut_lay_bundle_use_for (".$field_array.") values ".$data_array;die;

		if ($db_type == 0) {
			if ($insertStatus) {
				mysql_query("COMMIT");
				// echo "1**".str_replace("'","",$hidden_po_break_down_id);
			} else {
				mysql_query("ROLLBACK");
				// echo "10**".str_replace("'","",$hidden_po_break_down_id);
			}
		} else {
			if ($insertStatus) {
				oci_commit($con);
				// echo "1**".str_replace("'","",$hidden_po_break_down_id);
			} else {
				oci_rollback($con);
				// echo "10**".str_replace("'","",$hidden_po_break_down_id);
			}
		}
	}
	// =========================== end =================================

	$detls_id = $data[3];
	$batch_id = return_field_value("batch_id", "ppl_cut_lay_dtls", "id=$detls_id");
	$buyer_library = return_library_array("select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
	$table_no_library = return_library_array("select id,table_no  from  lib_cutting_table", "id", "table_no");
	$country_arr = return_library_array("select id, short_name from lib_country", 'id', 'short_name');
	$lib_season_arr = return_library_array("select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0", 'id', 'season_name');
	$order_cut_no = return_field_value("order_cut_no", "ppl_cut_lay_dtls", "id=$detls_id and status_active=1 and is_deleted=0", "order_cut_no");
	$pdf = new PDF_Code39('P', 'mm', 'a10');
	$pdf->AddPage();


	$bacth_array = array();
	$batchData = sql_select("select id, batch_no, extention_no from pro_batch_create_mst where entry_form in(0,7,37,66,68)");
	foreach ($batchData as $row) {
		$ext = '';
		if ($row[csf('extention_no')] > 0) {
			$ext = '-' . $row[csf('extention_no')];
		}
		$bacth_array[$row[csf('id')]] = $row[csf('batch_no')] . $ext;
	}
	$sql_color = "SELECT a.id,a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no,c.batch_no
	from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b,pro_roll_details c
	where a.mst_id=b.mst_id 
	and a.dtls_id=b.dtls_id 
	and c.id=a.roll_id
	and a.size_id=b.size_id 
	and a.id in ($data[0])
	group by a.id,c.batch_no,a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no order by b.bundle_sequence,a.id";
	//echo $sql_color;

	$color_sizeID_arr = sql_select($sql_color);

	foreach ($color_sizeID_arr as $row) {
		$test_data[$row[csf("roll_id")]] = $row[csf("roll_id")];
	}
	$i = 2;
	$j = 0;
	$k = 0;
	$bundle_array = array();
	$br = 0;
	$n = 0;
	$sql_name = sql_select("SELECT b.buyer_name, b.client_id, b.style_ref_no, b.product_dept, b.season_buyer_wise as season_matrix, a.po_number, a.id as po_id,a.t_year from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.id in(" . $data[6] . ")");
	$po_data_arr = array();
	foreach ($sql_name as $value) {
		$po_data_arr[$value[csf('po_id')]]["style_ref_no"] = $value[csf('style_ref_no')];
		$po_data_arr[$value[csf('po_id')]]["buyer_name"] = $value[csf('buyer_name')];
		$po_data_arr[$value[csf('po_id')]]["client_id"] = $value[csf('client_id')];
		$po_data_arr[$value[csf('po_id')]]["po_number"] = $value[csf('po_number')];
		$po_data_arr[$value[csf('po_id')]]["t_year"] = $value[csf('t_year')];
		$matrix_season = $value[csf('season_matrix')];
	}
	unset($sql_name);
	//echo "select roll_id, batch_no, shade from pro_roll_details where mst_id='$data[2]' and entry_form=289 and status_active=1";
	$roll_sql = sql_select("select roll_id, batch_no, shade from pro_roll_details where mst_id='$data[2]' and entry_form=715 and status_active=1");
	$roll_data_arr = array();
	foreach ($roll_sql as $row) {
		$roll_data_arr[$row[csf("roll_id")]]['batch'] = $row[csf("batch_no")];
		$roll_data_arr[$row[csf("roll_id")]]['shade'] = $row[csf("shade")];
	}
	//print_r($roll_data_arr); die;
	unset($roll_sql);

	$sql_cut_name = sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	foreach ($sql_cut_name as $cut_value) {
		$table_name = $cut_value[csf('table_no')];
		$cut_date = change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix = $cut_value[csf('cut_num_prefix_no')];
		$company_id = $cut_value[csf('company_id')];
		$comp_name = return_field_value("company_short_name", "lib_company", "id=$company_id");
		$new_cut_no = $comp_name . "-" . $cut_prifix;
		$bundle_title = "";
	}
	if ($data[7] == "") $data[7] = 0;
	$seq = explode(",", $data[7]);
	$seq_first = $seq[0];

	$sql_bundle_copy = sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
	$bundle_use_for = "";
	foreach ($sql_bundle_copy as $val) {
		$bundle_use_for .= ($bundle_use_for == "") ? $val[csf('bundle_use_for')] : "," . $val[csf('bundle_use_for')];
	}


	if ($data[8] == 1) // bitton Sticker 1/Page V2
	{
		if (count($sql_bundle_copy) != 0) {
			foreach ($sql_bundle_copy as $inf) {
				if ($br == 1) {
					$pdf->AddPage();
					$br = 0;
					$i = 2;
					$j = 0;
					$k = 0;
				}
				foreach ($color_sizeID_arr as $val) {
					if ($br == 1) {
						$pdf->AddPage();
						$br = 0;
						$i = 2;
						$j = 0;
						$k = 0;
					}
					if ($seq_first == $inf[csf("id")]) $symb = "@@";
					else $symb = "";
					//BNDL
					$style_name = $po_data_arr[$val[csf('order_id')]]["style_ref_no"];
					$buyer_name = $po_data_arr[$val[csf('order_id')]]["buyer_name"];
					$client_id = $po_data_arr[$val[csf('order_id')]]["client_id"];
					$po_number = $po_data_arr[$val[csf('order_id')]]["po_number"];
					$batch_no = $roll_data_arr[$val[csf('roll_id')]]['batch'];
					$shade = $roll_data_arr[$val[csf('roll_id')]]['shade'];

					$bundle_no_prifix = $val[csf("bundle_num_prefix_no")];
					$buyer_name_str = "";
					if ($client_id != 0) $buyer_name_str = $buyer_library[$buyer_name] . '-' . $buyer_library[$client_id];
					else $buyer_name_str = $buyer_library[$buyer_name];
					//$dbl_no="17910000000012";
					if ($val[csf('is_excess')] == 1) $country = "EXCESS";
					else $country = $country_arr[$val[csf("country_id")]];

					$pdf->Code40($i, $j - 2, $symb . " " . $buyer_name_str . "  COUN# " . $country . "  QTY# " . $val[csf("size_qty")], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i, $j + 1.2, "STKR# " . $val[csf("number_start")] . "-" . $val[csf("number_end")] . "  STY# " . $style_name, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i, $j + 4.4, $val[csf("bundle_no")] . "  PO# " . $po_number, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i, $j + 7.6, "COLOR# " . $color_library[$data[5]] . ",Shade " . $shade, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i, $j + 10.8, $inf[csf("bundle_use_for")] . "  B# " . $batch_no . "  S# " . $lib_season_arr[$matrix_season], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i, $j + 14, "CUT & ROLL# " . $order_cut_no . " & " . $val[csf("roll_no")] . "  SIZE# " . $size_arr[$val[csf("size_id")]] . "(" . $val[csf("pattern_no")] . ")", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i + 1.3, $j + 17.2, $val[csf("barcode_no")], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
					//$pdf->Code40($i, $j+22, $val[csf("bundle_no")],$w =0.2, $h = 8);
					$pdf->Code39($i + 1.3, $j + 23, $val[csf("barcode_no")]);
					$k++;
					$i = 2;
					$j = $j + 23;
					$br++;
				}
			}
		} else {
			foreach ($color_sizeID_arr as $val) {
				if ($br == 1) {
					$pdf->AddPage();
					$br = 0;
					$i = 2;
					$j = 0;
					$k = 0;
				}
				if ($seq_first == $inf[csf("id")]) $symb = "@@";
				else $symb = "";
				$style_name = $po_data_arr[$val[csf('order_id')]]["style_ref_no"];
				$buyer_name = $po_data_arr[$val[csf('order_id')]]["buyer_name"];
				$po_number = $po_data_arr[$val[csf('order_id')]]["po_number"];
				$batch_no = $roll_data_arr[$val[csf('roll_id')]]['batch'];
				$shade = $roll_data_arr[$val[csf('roll_id')]]['shade'];
				$bundle_no_prifix = $val[csf("bundle_num_prefix_no")];

				if ($val[csf('is_excess')] == 1) $country = "EXCESS";
				else $country = $country_arr[$val[csf("country_id")]];

				$pdf->Code40($i, $j - 2, $symb . " " . $buyer_library[$buyer_name] . "  COUN# " . $country . "  QTY# " . $val[csf("size_qty")], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i, $j + 1.2, "STKR# " . $val[csf("number_start")] . "-" . $val[csf("number_end")] . "  STY# " . $style_name, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i, $j + 4.4, $val[csf("bundle_no")] . "  PO# " . $po_number, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i, $j + 7.6, "COLOR# " . $color_library[$data[5]] . ",Shade " . $shade, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i, $j + 10.8, $inf[csf("bundle_use_for")] . "  B# " . $batch_no . "  S# " . $lib_season_arr[$matrix_season], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i, $j + 14, "CUT & ROLL# " . $order_cut_no . " & " . $val[csf("roll_no")] . "  SIZE# " . $size_arr[$val[csf("size_id")]] . "(" . $val[csf("pattern_no")] . ")", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i + 1.3, $j + 17.2, $val[csf("barcode_no")], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
				//$pdf->Code40($i, $j+22, $val[csf("bundle_no")],$w =0.2, $h = 8);
				$pdf->Code39($i + 1.3, $j + 23, $val[csf("barcode_no")]);
				$k++;
				$i = 2;
				$j = $j + 23;

				$br++;
			}
		}
	} else if ($data[8] == 2) // bitton Sticker 1/Page V3
	{
		if (count($sql_bundle_copy) != 0) {
			foreach ($sql_bundle_copy as $inf) {
				if ($br == 1) {
					$pdf->AddPage();
					$br = 0;
					$i = 2;
					$j = 0;
					$k = 0;
				}
				foreach ($color_sizeID_arr as $val) {
					if ($br == 1) {
						$pdf->AddPage();
						$br = 0;
						$i = 2;
						$j = 0;
						$k = 0;
					}
					if ($seq_first == $inf[csf("id")]) $symb = "@@";
					else $symb = "";
					//BNDL
					$style_name = $po_data_arr[$val[csf('order_id')]]["style_ref_no"];
					$buyer_name = $po_data_arr[$val[csf('order_id')]]["buyer_name"];
					$client_id = $po_data_arr[$val[csf('order_id')]]["client_id"];
					$po_number = $po_data_arr[$val[csf('order_id')]]["po_number"];
					$batch_no = $roll_data_arr[$val[csf('roll_id')]]['batch'];
					$shade = $roll_data_arr[$val[csf('roll_id')]]['shade'];
					$bundle_no_prifix = $val[csf("bundle_num_prefix_no")];
					$buyer_name_str = "";
					if ($client_id != 0) $buyer_name_str = $buyer_library[$buyer_name] . '-' . $buyer_library[$client_id];
					else $buyer_name_str = $buyer_library[$buyer_name];
					//$dbl_no="17910000000012";
					if ($val[csf('is_excess')] == 1) $country = "EXCESS";
					else $country = $country_arr[$val[csf("country_id")]];
					$pdf->Code40($i, $j - 2, $symb . " " . $shade . "  Shade", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i, $j + 1.2, $buyer_name_str . "  COUN# " . $country . "  QTY# " . $val[csf("size_qty")], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i, $j + 4.2, "STKR# " . $val[csf("number_start")] . "-" . $val[csf("number_end")] . "  STY# " . $style_name, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i, $j + 7.3, $val[csf("bundle_no")] . "  PO# " . $po_number, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i, $j + 10, "COLOR# " . $color_library[$data[5]], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i, $j + 12.6, $inf[csf("bundle_use_for")] . "  B# " . $batch_no . "  S# " . $lib_season_arr[$matrix_season], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i, $j + 15.2, "CUT & ROLL# " . $order_cut_no . " & " . $val[csf("roll_no")] . "  SIZE# " . $size_arr[$val[csf("size_id")]] . "(" . $val[csf("pattern_no")] . ")", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i + 1.3, $j + 17.5, $val[csf("barcode_no")], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
					//$pdf->Code40($i, $j+22, $val[csf("bundle_no")],$w =0.2, $h = 8);
					$pdf->Code39($i + 1.3, $j + 23, $val[csf("barcode_no")]);
					$k++;
					$i = 2;
					$j = $j + 23;
					$br++;
				}
			}
		} else {
			foreach ($color_sizeID_arr as $val) {
				if ($br == 1) {
					$pdf->AddPage();
					$br = 0;
					$i = 2;
					$j = 0;
					$k = 0;
				}
				if ($seq_first == $inf[csf("id")]) $symb = "@@";
				else $symb = "";
				$style_name = $po_data_arr[$val[csf('order_id')]]["style_ref_no"];
				$buyer_name = $po_data_arr[$val[csf('order_id')]]["buyer_name"];
				$po_number = $po_data_arr[$val[csf('order_id')]]["po_number"];
				$batch_no = $roll_data_arr[$val[csf('roll_id')]]['batch'];
				$shade = $roll_data_arr[$val[csf('roll_id')]]['shade'];
				$bundle_no_prifix = $val[csf("bundle_num_prefix_no")];

				if ($val[csf('is_excess')] == 1) $country = "EXCESS";
				else $country = $country_arr[$val[csf("country_id")]];
				$pdf->Code40($i, $j - 2, $symb . " " . $shade . "  Shade", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i, $j + 1.2, $buyer_library[$buyer_name] . "  COUN# " . $country . "  QTY# " . $val[csf("size_qty")], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i, $j + 4.4, "STKR# " . $val[csf("number_start")] . "-" . $val[csf("number_end")] . "  STY# " . $style_name, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i, $j + 7.3, $val[csf("bundle_no")] . "  PO# " . $po_number, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i, $j + 10, "COLOR# " . $color_library[$data[5]], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i, $j + 12.6, $inf[csf("bundle_use_for")] . "  B# " . $batch_no . "  S# " . $lib_season_arr[$matrix_season], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i, $j + 15.2, "CUT & ROLL# " . $order_cut_no . " & " . $val[csf("roll_no")] . "  SIZE# " . $size_arr[$val[csf("size_id")]] . "(" . $val[csf("pattern_no")] . ")", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i + 1.3, $j + 17.5, $val[csf("barcode_no")], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
				//$pdf->Code40($i, $j+22, $val[csf("bundle_no")],$w =0.2, $h = 8);
				$pdf->Code39($i + 1.3, $j + 23, $val[csf("barcode_no")]);
				$k++;
				$i = 2;
				$j = $j + 23;

				$br++;
			}
		}
	} else if ($data[8] == 3) // bitton Sticker 1/Page V4
	{
		if (count($sql_bundle_copy) != 0) {
			foreach ($sql_bundle_copy as $inf) {
				if ($br == 1) {
					$pdf->AddPage();
					$br = 0;
					$i = 2;
					$j = 0;
					$k = 0;
				}
				foreach ($color_sizeID_arr as $val) {
					if ($br == 1) {
						$pdf->AddPage();
						$br = 0;
						$i = 2;
						$j = 0;
						$k = 0;
					}
					if ($seq_first == $inf[csf("id")]) $symb = "@@";
					else $symb = "";
					//BNDL
					$style_name = $po_data_arr[$val[csf('order_id')]]["style_ref_no"];
					$buyer_name = $po_data_arr[$val[csf('order_id')]]["buyer_name"];
					$client_id = $po_data_arr[$val[csf('order_id')]]["client_id"];
					$po_number = $po_data_arr[$val[csf('order_id')]]["po_number"];
					$t_year = $po_data_arr[$val[csf('order_id')]]["t_year"];
					$batch_no = $roll_data_arr[$val[csf('roll_id')]]['batch'];
					$shade = $roll_data_arr[$val[csf('roll_id')]]['shade'];
					$bundle_no_prifix = $val[csf("bundle_num_prefix_no")];
					$barcode = $val[csf("barcode_no")];

					$buyer_name_str = "";
					if ($client_id != 0) $buyer_name_str = $buyer_library[$buyer_name] . '-' . $buyer_library[$client_id];
					else $buyer_name_str = $buyer_library[$buyer_name];
					//$dbl_no="17910000000012";
					if ($val[csf('is_excess')] == 1) $country = "EXCESS";
					else $country = $country_arr[$val[csf("country_id")]];

					$pdf->Code40($i, $j - 2, "BY#" . $buyer_name_str, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

					$pdf->Code40($i + 30, $j - 2, "Size# " . $size_arr[$val[csf("size_id")]] . "(" . $val[csf("pattern_no")] . ")", $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

					$pdf->Code40($i, $j + 1.2, "STY# " . substr($style_name, 0, 12), $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

					$pdf->Code40($i + 30, $j + 1.2, "QTY# " . $val[csf("size_qty")], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

					$pdf->Code40($i, $j + 4.2, "CUT & ROLL# " . $order_cut_no . " & " . $val[csf("roll_no")], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i + 30, $j + 4.2, "SH# " . $shade, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

					$pdf->Code40($i, $j + 7.3, "B/NO#" . $batch_no, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i + 30, $j + 7.3, "P/N# " . $inf[csf('bundle_use_for')], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

					$pdf->Code40($i, $j + 10, "BUN# " . $val[csf("bundle_no")], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i + 30, $j + 10, "STKR#" . $val[csf("number_start")] . "-" . $val[csf("number_end")], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

					$pdf->Code40($i, $j + 12.6, "COLOR#" . $color_library[$data[5]], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i + 30, $j + 12.6, "COUN# " . $country, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

					$pdf->Code40($i, $j + 15.2, "S&Y# " . $lib_season_arr[$matrix_season] . "-" . substr($t_year, 2), $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i + 30, $j + 15.2, "PO# " . $po_number, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

					$pdf->Code40($i, $j + 17.5, $val[csf("barcode_no")], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
					//$pdf->Code40($i, $j+22, $val[csf("bundle_no")],$w =0.2, $h = 8);
					$pdf->Code39($i, $j + 23, $val[csf("barcode_no")]);

					$k++;
					$i = 2;
					$j = $j + 23;
					$br++;
				}
			}
		} else {
			foreach ($color_sizeID_arr as $val) {
				if ($br == 1) {
					$pdf->AddPage();
					$br = 0;
					$i = 2;
					$j = 0;
					$k = 0;
				}
				if ($seq_first == $inf[csf("id")]) $symb = "@@";
				else $symb = "";
				$style_name = $po_data_arr[$val[csf('order_id')]]["style_ref_no"];
				$buyer_name = $po_data_arr[$val[csf('order_id')]]["buyer_name"];
				$client_id = $po_data_arr[$val[csf('order_id')]]["client_id"];
				$po_number = $po_data_arr[$val[csf('order_id')]]["po_number"];
				$t_year = $po_data_arr[$val[csf('order_id')]]["t_year"];
				$batch_no = $roll_data_arr[$val[csf('roll_id')]]['batch'];
				$shade = $roll_data_arr[$val[csf('roll_id')]]['shade'];
				$bundle_no_prifix = $val[csf("bundle_num_prefix_no")];
				$buyer_name_str = "";
				if ($client_id != 0) $buyer_name_str = $buyer_library[$buyer_name] . '-' . $buyer_library[$client_id];
				else $buyer_name_str = $buyer_library[$buyer_name];

				if ($val[csf('is_excess')] == 1) $country = "EXCESS";
				else $country = $country_arr[$val[csf("country_id")]];
				$pdf->Code40($i, $j - 2, "BY#" . $buyer_name_str, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i + 30, $j - 2, "Size# " . $size_arr[$val[csf("size_id")]] . "(" . $val[csf("pattern_no")] . ")", $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

				$pdf->Code40($i, $j + 1.2, "STY# " . substr($style_name, 0, 12), $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i + 30, $j + 1.2, "QTY# " . $val[csf("size_qty")], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

				$pdf->Code40($i, $j + 4.2, "CUT & ROLL# " . $order_cut_no . " & " . $val[csf("roll_no")], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i + 30, $j + 4.2, "SH# " . $shade, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

				$pdf->Code40($i, $j + 7.3, "B/NO#" . $batch_no, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i + 30, $j + 7.3, "P/N# " . $sql_bundle_copy[0][csf('bundle_use_for')], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

				$pdf->Code40($i, $j + 10, "BUN# " . $val[csf("bundle_no")], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i + 30, $j + 10, "STKR#" . $val[csf("number_start")] . "-" . $val[csf("number_end")], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

				$pdf->Code40($i, $j + 12.6, "COLOR#" . $color_library[$data[5]], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i + 30, $j + 12.6, "COUN# " . $country, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

				$pdf->Code40($i, $j + 15.2, "S&Y# " . $lib_season_arr[$matrix_season] . "-" . substr($t_year, 2), $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i + 30, $j + 15.2, "PO# " . $po_number, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

				$pdf->Code40($i, $j + 17.5, $val[csf("barcode_no")], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
				//$pdf->Code40($i, $j+22, $val[csf("bundle_no")],$w =0.2, $h = 8);
				$pdf->Code39($i, $j + 23, $val[csf("barcode_no")]);
				$k++;
				$i = 2;
				$j = $j + 23;

				$br++;
			}
		}
	} else if ($data[8] == 7) // bitton Sticker 1/Page V6
	{
		if (count($sql_bundle_copy) != 0) {
			foreach ($sql_bundle_copy as $inf) {
				if ($br == 1) {
					$pdf->AddPage();
					$br = 0;
					$i = 2;
					$j = 0;
					$k = 0;
				}
				foreach ($color_sizeID_arr as $val) {
					if ($br == 1) {
						$pdf->AddPage();
						$br = 0;
						$i = 2;
						$j = 0;
						$k = 0;
					}
					if ($seq_first == $inf[csf("id")]) $symb = "@@";
					else $symb = "";
					//BNDL
					$style_name = $po_data_arr[$val[csf('order_id')]]["style_ref_no"];
					$buyer_name = $po_data_arr[$val[csf('order_id')]]["buyer_name"];
					$client_id = $po_data_arr[$val[csf('order_id')]]["client_id"];
					$po_number = $po_data_arr[$val[csf('order_id')]]["po_number"];
					$t_year = $po_data_arr[$val[csf('order_id')]]["t_year"];
					$batch_no = $roll_data_arr[$val[csf('roll_id')]]['batch'];
					$shade = $roll_data_arr[$val[csf('roll_id')]]['shade'];
					$bundle_no_prifix = $val[csf("bundle_num_prefix_no")];
					$gold_seal = $val[csf("batch_no")];
					$barcode = $val[csf("barcode_no")];
					$bundle_no = $val[csf("bundle_no")];

					$buyer_name_str = "";
					if ($client_id != 0) $buyer_name_str = $buyer_library[$buyer_name] . '-' . $buyer_library[$client_id];
					else $buyer_name_str = $buyer_library[$buyer_name];
					//$dbl_no="17910000000012";
					if ($val[csf('is_excess')] == 1) $country = "EXCESS";
					else $country = $country_arr[$val[csf("country_id")]];

					$pdf->Code40($i, $j - 2, "BY#" . $buyer_name_str, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i + 30, $j - 2, "Size# " . $size_arr[$val[csf("size_id")]] . "(" . $val[csf("pattern_no")] . ")", $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i, $j + 1.3, "G. SL.# " . $gold_seal, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i + 30, $j + 1.3, "QTY# " . $val[csf("size_qty")], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

					$pdf->Code40($i, $j + 4.3, "STY# " . substr($style_name, 0, 12), $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i + 30, $j + 4.3, "SH# " . $shade, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

					$pdf->Code40($i, $j + 7.4, "BUN# " . $bundle_no, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i + 30, $j + 7.4, "PO# " . $po_number, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
					// $pdf->Code40($i+30, $j+7.4, "P/N# ".$inf[csf('bundle_use_for')], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true,7) ;

					$pdf->Code40($i, $j + 10.2, "P/N# " . $inf[csf('bundle_use_for')], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i + 30, $j + 10.2, "SN# " . $val[csf("number_start")] . "-" . $val[csf("number_end")], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

					//$pdf->Code40($i+30, $j+10.2, "SN# ".$val[csf("number_start")]."-".$val[csf("number_end")], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide=true,true,7);

					$pdf->Code40($i, $j + 12.9, "CUT# " . $order_cut_no, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i + 30, $j + 12.9, "COUN# " . $country, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

					$pdf->Code40($i, $j + 15.2, "COLOR# " . $color_library[$data[5]], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

					$pdf->Code40($i, $j + 17.5, $val[csf("barcode_no")], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i + 30, $j + 17.5, "S&Y# " . $lib_season_arr[$matrix_season] . "-" . substr($t_year, 2), $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

					//$pdf->Code40($i, $j+22, $val[csf("bundle_no")],$w =0.2, $h = 8);
					$pdf->Code39($i, $j + 23, $val[csf("barcode_no")]);
					$k++;
					$i = 2;
					$j = $j + 23;
					$br++;
				}
			}
		} else {
			foreach ($color_sizeID_arr as $val) {
				if ($br == 1) {
					$pdf->AddPage();
					$br = 0;
					$i = 2;
					$j = 0;
					$k = 0;
				}
				if ($seq_first == $inf[csf("id")]) $symb = "@@";
				else $symb = "";
				$style_name = $po_data_arr[$val[csf('order_id')]]["style_ref_no"];
				$buyer_name = $po_data_arr[$val[csf('order_id')]]["buyer_name"];
				$client_id = $po_data_arr[$val[csf('order_id')]]["client_id"];
				$po_number = $po_data_arr[$val[csf('order_id')]]["po_number"];
				$t_year = $po_data_arr[$val[csf('order_id')]]["t_year"];
				$batch_no = $roll_data_arr[$val[csf('roll_id')]]['batch'];
				$shade = $roll_data_arr[$val[csf('roll_id')]]['shade'];
				$bundle_no_prifix = $val[csf("bundle_num_prefix_no")];
				$gold_seal = $val[csf("batch_no")];
				$buyer_name_str = "";
				if ($client_id != 0) $buyer_name_str = $buyer_library[$buyer_name] . '-' . $buyer_library[$client_id];
				else $buyer_name_str = $buyer_library[$buyer_name];

				if ($val[csf('is_excess')] == 1) $country = "EXCESS";
				else $country = $country_arr[$val[csf("country_id")]];


				$pdf->Code40($i, $j - 2, "BY#" . $buyer_name_str, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i + 30, $j - 2, "Size# " . $size_arr[$val[csf("size_id")]] . "(" . $val[csf("pattern_no")] . ")", $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i, $j + 1.3, "G. SL.# " . $gold_seal, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i + 30, $j + 1.3, "QTY# " . $val[csf("size_qty")], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

				$pdf->Code40($i, $j + 4.3, "STY# " . substr($style_name, 0, 12), $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i + 30, $j + 4.3, "SH# " . $shade, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

				$pdf->Code40($i, $j + 7.4, "BUN# " . $bundle_no, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i + 30, $j + 7.4, "PO# " . $po_number, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
				// $pdf->Code40($i+30, $j+7.4, "P/N# ".$inf[csf('bundle_use_for')], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true,7) ;

				$pdf->Code40($i, $j + 10.2, "P/N# " . $inf[csf('bundle_use_for')], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i + 30, $j + 10.2, "SN# " . $val[csf("number_start")] . "-" . $val[csf("number_end")], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

				//$pdf->Code40($i+30, $j+10.2, "SN# ".$val[csf("number_start")]."-".$val[csf("number_end")], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide=true,true,7);

				$pdf->Code40($i, $j + 12.9, "CUT# " . $order_cut_no, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i + 30, $j + 12.9, "COUN# " . $country, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

				$pdf->Code40($i, $j + 15.2, "COLOR# " . $color_library[$data[5]], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

				$pdf->Code40($i, $j + 17.5, $val[csf("barcode_no")], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i + 30, $j + 17.5, "S&Y# " . $lib_season_arr[$matrix_season] . "-" . substr($t_year, 2), $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

				//$pdf->Code40($i, $j+22, $val[csf("bundle_no")],$w =0.2, $h = 8);
				$pdf->Code39($i, $j + 23, $val[csf("barcode_no")]);
				$k++;
				$i = 2;
				$j = $j + 23;
				$br++;
			}
		}
	} else if ($data[8] == 8) // bitton Sticker 1/Page V7
	{
		if (count($sql_bundle_copy) != 0) {
			foreach ($sql_bundle_copy as $inf) {
				if ($br == 1) {
					$pdf->AddPage();
					$br = 0;
					$i = 2;
					$j = 0;
					$k = 0;
				}
				foreach ($color_sizeID_arr as $val) {
					if ($br == 1) {
						$pdf->AddPage();
						$br = 0;
						$i = 2;
						$j = 0;
						$k = 0;
					}
					if ($seq_first == $inf[csf("id")]) $symb = "@@";
					else $symb = "";
					//BNDL
					$style_name = $po_data_arr[$val[csf('order_id')]]["style_ref_no"];
					$buyer_name = $po_data_arr[$val[csf('order_id')]]["buyer_name"];
					$client_id = $po_data_arr[$val[csf('order_id')]]["client_id"];
					$po_number = $po_data_arr[$val[csf('order_id')]]["po_number"];
					$t_year = $po_data_arr[$val[csf('order_id')]]["t_year"];
					$batch_no = $roll_data_arr[$val[csf('roll_id')]]['batch'];
					$shade = $roll_data_arr[$val[csf('roll_id')]]['shade'];
					$bundle_no_prifix = $val[csf("bundle_num_prefix_no")];
					$barcode = $val[csf("barcode_no")];

					$buyer_name_str = "";
					if ($client_id != 0) $buyer_name_str = $buyer_library[$buyer_name] . '-' . $buyer_library[$client_id];
					else $buyer_name_str = $buyer_library[$buyer_name];
					//$dbl_no="17910000000012";
					if ($val[csf('is_excess')] == 1) $country = "EXCESS";
					else $country = $country_arr[$val[csf("country_id")]];

					$pdf->Code40($i, $j - 2, "BY- " . $buyer_name_str, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i + 30, $j - 2, "SIZE- " . $size_arr[$val[csf("size_id")]] . "(" . $val[csf("pattern_no")] . ")", $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

					$pdf->Line(0, 0.2, 59.7, 0.2); // top
					$pdf->Line(0, 3.9, 59.7, 3.9);
					$pdf->Line(30, 0, 30, 16.7); // verticale border
					$pdf->Line(30, 19.6, 30, 22.1); // verticale border
					$pdf->Line(0.2, 0, 0.2, 33); // left border
					$pdf->Line(59.7, 0, 59.7, 33); // right border
					$pdf->Line(0, 22.4, 59.7, 22.4); // before barcode
					$pdf->Line(0, 33.3, 59.7, 33.3); // bottom

					$pdf->Code40($i, $j + 1.6, "STY- " . substr($style_name, 0, 12), $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i + 30, $j + 1.6, "PO QTY- " . $val[csf("size_qty")], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

					$pdf->Line(0, 7.4, 59.7, 7.4);

					$pdf->Code40($i, $j + 5.1, "CUT NO- " . $order_cut_no, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i + 30, $j + 5.1, "B/NO- " . $batch_no, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

					$pdf->Line(0, 10.7, 59.7, 10.7);

					$pdf->Code40($i, $j + 8.2, "SH- " . $shade, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i + 30, $j + 8.2, "P/N- " . $inf[csf('bundle_use_for')], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

					$pdf->Line(0, 13.8, 59.7, 13.8);

					$pdf->Code40($i, $j + 11.2, "BUN- " . $val[csf("bundle_no")], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i + 30, $j + 11.2, "STKR- " . $val[csf("number_start")] . "-" . $val[csf("number_end")], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

					$pdf->Line(0, 16.7, 59.7, 16.7);

					$pdf->Code40($i, $j + 14, "COLOR- " . $color_library[$data[5]], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

					$pdf->Line(0, 19.6, 59.7, 19.6);

					$pdf->Code40($i, $j + 17, $val[csf("barcode_no")], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i + 30, $j + 17, "PO- " . $po_number, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
					//$pdf->Code40($i, $j+22, $val[csf("bundle_no")],$w =0.2, $h = 8);
					$pdf->Code39($i, $j + 23, $val[csf("barcode_no")]);

					$k++;
					$i = 2;
					$j = $j + 23;
					$br++;
				}
			}
		} else {
			foreach ($color_sizeID_arr as $val) {
				if ($br == 1) {
					$pdf->AddPage();
					$br = 0;
					$i = 2;
					$j = 0;
					$k = 0;
				}
				if ($seq_first == $inf[csf("id")]) $symb = "@@";
				else $symb = "";
				$style_name = $po_data_arr[$val[csf('order_id')]]["style_ref_no"];
				$buyer_name = $po_data_arr[$val[csf('order_id')]]["buyer_name"];
				$client_id = $po_data_arr[$val[csf('order_id')]]["client_id"];
				$po_number = $po_data_arr[$val[csf('order_id')]]["po_number"];
				$t_year = $po_data_arr[$val[csf('order_id')]]["t_year"];
				$batch_no = $roll_data_arr[$val[csf('roll_id')]]['batch'];
				$shade = $roll_data_arr[$val[csf('roll_id')]]['shade'];
				$bundle_no_prifix = $val[csf("bundle_num_prefix_no")];
				$buyer_name_str = "";
				if ($client_id != 0) $buyer_name_str = $buyer_library[$buyer_name] . '-' . $buyer_library[$client_id];
				else $buyer_name_str = $buyer_library[$buyer_name];

				if ($val[csf('is_excess')] == 1) $country = "EXCESS";
				else $country = $country_arr[$val[csf("country_id")]];
				$pdf->Code40($i, $j - 2, "BY#" . $buyer_name_str, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i + 30, $j - 2, "Size# " . $size_arr[$val[csf("size_id")]] . "(" . $val[csf("pattern_no")] . ")", $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

				$pdf->Code40($i, $j + 1.2, "STY# " . substr($style_name, 0, 12), $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i + 30, $j + 1.2, "QTY# " . $val[csf("size_qty")], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

				$pdf->Code40($i, $j + 4.2, "CUT & ROLL# " . $order_cut_no . " & " . $val[csf("roll_no")], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i + 30, $j + 4.2, "SH# " . $shade, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

				$pdf->Code40($i, $j + 7.3, "B/NO#" . $batch_no, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i + 30, $j + 7.3, "P/N# " . $sql_bundle_copy[0][csf('bundle_use_for')], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

				$pdf->Code40($i, $j + 10, "BUN# " . $val[csf("bundle_no")], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i + 30, $j + 10, "STKR#" . $val[csf("number_start")] . "-" . $val[csf("number_end")], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

				$pdf->Code40($i, $j + 12.6, "COLOR#" . $color_library[$data[5]], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i + 30, $j + 12.6, "COUN# " . $country, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

				$pdf->Code40($i, $j + 15.2, "S&Y# " . $lib_season_arr[$matrix_season] . "-" . substr($t_year, 2), $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i + 30, $j + 15.2, "PO# " . $po_number, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

				$pdf->Code40($i, $j + 17.5, $val[csf("barcode_no")], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
				//$pdf->Code40($i, $j+22, $val[csf("bundle_no")],$w =0.2, $h = 8);
				$pdf->Code39($i, $j + 23, $val[csf("barcode_no")]);
				$k++;
				$i = 2;
				$j = $j + 23;

				$br++;
			}
		}
	}

	foreach (glob("" . "*.pdf") as $filename) {
		@unlink($filename);
	}
	$name = 'bundle_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output("" . $name, 'F');
	echo $name;
	exit();
}
if ($action == "print_barcode_one_urmi_eg") {
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	require('../../../ext_resource/pdf/code39.php');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');//order_cut_no

	$data = explode("***", $data);

	// ================================== BUNDLE USE FOR ENTRY ============================
	if ($data[7] != "") // when body part exist
	{
		$con = connect();
		$job_id = return_field_value("id", "wo_po_details_master", " job_no='$data[1]' and status_active=1 and is_deleted=0 ", "id");

		// $delStatus = sql_delete("ppl_cut_lay_bundle_use_for","status_active*is_deleted","0*1",'job_id',$job_id,1);
		$delStatus = execute_query("delete from ppl_cut_lay_bundle_use_for where job_id=$job_id", 0);

		$field_array = "id,job_id,bodypart_id,inserted_by,insert_date";
		$bodypart_id = array_unique(explode(",", $data[7]));
		$data_array = "";
		$j = 0;
		$id = return_next_id("id", "ppl_cut_lay_bundle_use_for", 1);
		foreach ($bodypart_id as $val) {
			if ($j == 0) $data_array = "(" . $id . "," . $job_id . "," . $val . "," . $user_id . ",'" . $pc_date_time . "')";
			else $data_array .= ",(" . $id . "," . $job_id . "," . $val . "," . $user_id . ",'" . $pc_date_time . "')";
			$id = $id + 1;
			$j++;
		}

		$insertStatus = sql_insert("ppl_cut_lay_bundle_use_for", $field_array, $data_array, 0);

		// echo "10**insert into ppl_cut_lay_bundle_use_for (".$field_array.") values ".$data_array;die;

		if ($db_type == 0) {
			if ($insertStatus) {
				mysql_query("COMMIT");
				// echo "1**".str_replace("'","",$hidden_po_break_down_id);
			} else {
				mysql_query("ROLLBACK");
				// echo "10**".str_replace("'","",$hidden_po_break_down_id);
			}
		} else {
			if ($insertStatus) {
				oci_commit($con);
				// echo "1**".str_replace("'","",$hidden_po_break_down_id);
			} else {
				oci_rollback($con);
				// echo "10**".str_replace("'","",$hidden_po_break_down_id);
			}
		}
	}
	// =========================== end =================================

	$detls_id = $data[3];
	$batch_id = return_field_value("batch_id", "ppl_cut_lay_dtls", "id=$detls_id");
	$buyer_library = return_library_array("select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
	$table_no_library = return_library_array("select id,table_no  from  lib_cutting_table", "id", "table_no");
	$country_arr = return_library_array("select id, short_name from lib_country", 'id', 'short_name');
	$size_arr = return_library_array("select id, size_name from lib_size", 'id', 'size_name');
	$lib_season_arr = return_library_array("select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0", 'id', 'season_name');
	$order_cut_no = return_field_value("order_cut_no", "ppl_cut_lay_dtls", "id=$detls_id and status_active=1 and is_deleted=0", "order_cut_no");

	$manualSizeArr = return_library_array("select size_id, manual_size_name from ppl_cut_lay_size where dtls_id=$detls_id", 'size_id', 'manual_size_name');
	$pdf = new PDF_Code39('P', 'mm', array(59, 51));
	$pdf->AddPage();

	$bacth_array = array();
	$batchData = sql_select("select id, batch_no, extention_no from pro_batch_create_mst where entry_form in(0,7,37,66,68)");
	foreach ($batchData as $row) {
		$ext = '';
		if ($row[csf('extention_no')] > 0) {
			$ext = '-' . $row[csf('extention_no')];
		}
		$bacth_array[$row[csf('id')]] = $row[csf('batch_no')] . $ext;
	}
	$color_sizeID_arr = sql_select("SELECT a.id, a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no, c.table_no,b.marker_qty 
	from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b, ppl_cut_lay_mst c
	where a.mst_id=b.mst_id 
	and a.dtls_id=b.dtls_id 
	and a.size_id=b.size_id 
	and a.mst_id=c.id
	and a.id in ($data[0])
	group by a.id, a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no,c.table_no,b.marker_qty order by b.bundle_sequence,a.id");

	foreach ($color_sizeID_arr as $row) {
		$test_data[$row[csf("roll_id")]] = $row[csf("roll_id")];
	}
	$i = 2;
	$j = 0;
	$k = 0;
	$bundle_array = array();
	$br = 0;
	$n = 0;
	$sql_name = sql_select("SELECT b.buyer_name, b.client_id, b.style_ref_no, b.product_dept, b.season_buyer_wise as season_matrix, a.po_number, a.id as po_id,a.t_year,b.gmts_item_id,b.job_no from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.id in(" . $data[6] . ")");
	$po_data_arr = array();
	foreach ($sql_name as $value) {
		$po_data_arr[$value[csf('po_id')]]["style_ref_no"] = $value[csf('style_ref_no')];
		$po_data_arr[$value[csf('po_id')]]["gmts_item_id"] = $value[csf('gmts_item_id')];
		$po_data_arr[$value[csf('po_id')]]["job_no"] = $value[csf('job_no')];

		$po_data_arr[$value[csf('po_id')]]["buyer_name"] = $value[csf('buyer_name')];
		$po_data_arr[$value[csf('po_id')]]["client_id"] = $value[csf('client_id')];
		$po_data_arr[$value[csf('po_id')]]["po_number"] = $value[csf('po_number')];
		$po_data_arr[$value[csf('po_id')]]["t_year"] = $value[csf('t_year')];
		$matrix_season = $value[csf('season_matrix')];
	}
	unset($sql_name);
	//echo "select roll_id, batch_no, shade from pro_roll_details where mst_id='$data[2]' and entry_form=289 and status_active=1";
	$roll_sql = sql_select("select roll_id, batch_no, shade from pro_roll_details where mst_id='$data[2]' and entry_form=715 and status_active=1");
	//echo "select roll_id, batch_no, shade from pro_roll_details where mst_id='$data[2]' and entry_form=289 and status_active=1";
	$roll_data_arr = array();
	foreach ($roll_sql as $row) {
		$roll_data_arr[$row[csf("roll_id")]]['batch'] = $row[csf("batch_no")];
		$roll_data_arr[$row[csf("roll_id")]]['shade'] = $row[csf("shade")];
	}
	//print_r($roll_data_arr); die;
	unset($roll_sql);

	$sql_cut_name = sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	foreach ($sql_cut_name as $cut_value) {
		$table_name = $cut_value[csf('table_no')];
		$cut_date = change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix = $cut_value[csf('cut_num_prefix_no')];
		$company_id = $cut_value[csf('company_id')];
		$comp_name = return_field_value("company_short_name", "lib_company", "id=$company_id");
		$new_cut_no = $comp_name . "-" . $cut_prifix;
		$bundle_title = "";
	}
	if ($data[7] == "") $data[7] = 0;
	$seq = explode(",", $data[7]);
	$seq_first = $seq[0];
	$bundle_copy_lib = return_library_array("SELECT id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])", 'id', 'bundle_use_for');
	$sql_bundle_copy = sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
	$bundle_use_for = "";
	foreach ($sql_bundle_copy as $val) {
		$bundle_use_for .= ($bundle_use_for == "") ? $val[csf('bundle_use_for')] : "," . $val[csf('bundle_use_for')];
	}
	// EG Sticker
	$body_part = explode(",", $data[7]);

	if (count($body_part) != 0) {
		foreach ($body_part as $inf) {
			if ($br == 1) {
				$pdf->AddPage();
				$br = 0;
				$i = 2;
				$j = 0;
				$k = 0;
			}
			foreach ($color_sizeID_arr as $val) {
				if ($br == 1) {
					$pdf->AddPage();
					$br = 0;
					$i = 2;
					$j = 0;
					$k = 0;
				}
				if ($seq_first == $inf[csf("id")]) $symb = "@@";
				else $symb = "";
				//BNDL
				$style_name = $po_data_arr[$val[csf('order_id')]]["style_ref_no"];

				$buyer_name = $po_data_arr[$val[csf('order_id')]]["buyer_name"];
				$job_no = $po_data_arr[$val[csf('order_id')]]["job_no"];
				$item_name = $po_data_arr[$val[csf('order_id')]]["gmts_item_id"];


				$client_id = $po_data_arr[$val[csf('order_id')]]["client_id"];
				$po_number = $po_data_arr[$val[csf('order_id')]]["po_number"];
				$t_year = $po_data_arr[$val[csf('order_id')]]["t_year"];

				$batch_no = $roll_data_arr[$val[csf('roll_id')]]['batch'];

				$shade = $roll_data_arr[$val[csf('roll_id')]]['shade'];
				$bundle_no_prifix = $val[csf("bundle_num_prefix_no")];

				$barcode = $val[csf("barcode_no")];
				$bundle = $val[csf("bundle_no")];
				$pattern = $val[csf("pattern_no")];
				$table_no = $val[csf("table_no")];
				$size_name = $size_arr[$val[csf("size_id")]];
				$gmts_no_start = $val[csf("number_start")];
				$gmts_no_end = $val[csf("number_end")];
				$cut_qty = $val[csf("marker_qty")];

				$buyer_name_str = "";
				if ($client_id != 0) $buyer_name_str = $buyer_library[$buyer_name] . '-' . $buyer_library[$client_id];
				else $buyer_name_str = $buyer_library[$buyer_name];
				//$dbl_no="17910000000012"; My Work
				if ($val[csf('is_excess')] == 1) $country = "EXCESS";
				else $country = $country_arr[$val[csf("country_id")]];

				$pdf->Code40($i, $j - 2, $barcode . " P. Date: " . date("j F, g:i a"), $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

				$pdf->Code40($i, $j + 1.2, $job_no . " , " . $bundle, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

				$pdf->Code40($i, $j + 4.2, "Cut Qty " . $cut_qty . ", " . $bundle_copy_lib[$inf] . "(" . $pattern . ")", $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

				$pdf->Code40($i, $j + 7.2, "Table No: " . $table_no . ", OCN: " . $order_cut_no . ", " . $cut_date, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

				$pdf->Code40($i, $j + 10.2, "Buyer Name: " . $buyer_name_str . ", Order No: " . $po_number, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

				$pdf->Code40($i, $j + 13.2, "Style No: " . $style_name, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

				$pdf->Code40($i, $j + 16.2, "Item: " . $garments_item[$item_name] . ", Country: " . $country, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

				$pdf->Code40($i, $j + 19.2, "Color: " . $color_library[$data[5]], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

				$pdf->Code40($i, $j + 22.2, "Size No: " . $size_name . "[" . $manualSizeArr[$val[csf("size_id")]] . "],", $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i + 17.2, $j + 22.2, "Batch: " . $batch_no . ", Shade: " . $shade, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

				$pdf->Code40($i, $j + 25.2, "Gmts No: " . $gmts_no_start . "-" . $gmts_no_end . ", Gmts Qty: " . $val[csf("size_qty")], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

				$pdf->Code39($i - 1, $j + 33.2, $val[csf("barcode_no")], $ext = true, $cks = false, $w = 0.22, $h = 17, $wide = true, $textonly = false, $fontSize = 11);

				$k++;
				$i = 2;
				$j = $j + 23;
				$br++;
			}
		}
	} else {
		foreach ($color_sizeID_arr as $val) {
			if ($br == 1) {
				$pdf->AddPage();
				$br = 0;
				$i = 2;
				$j = 0;
				$k = 0;
			}
			if ($seq_first == $inf[csf("id")]) $symb = "@@";
			else $symb = "";
			//BNDL
			$style_name = $po_data_arr[$val[csf('order_id')]]["style_ref_no"];

			$buyer_name = $po_data_arr[$val[csf('order_id')]]["buyer_name"];
			$job_no = $po_data_arr[$val[csf('order_id')]]["job_no"];
			$item_name = $po_data_arr[$val[csf('order_id')]]["gmts_item_id"];


			$client_id = $po_data_arr[$val[csf('order_id')]]["client_id"];
			$po_number = $po_data_arr[$val[csf('order_id')]]["po_number"];
			$t_year = $po_data_arr[$val[csf('order_id')]]["t_year"];

			$batch_no = $roll_data_arr[$val[csf('roll_id')]]['batch'];

			$shade = $roll_data_arr[$val[csf('roll_id')]]['shade'];
			$bundle_no_prifix = $val[csf("bundle_num_prefix_no")];

			$barcode = $val[csf("barcode_no")];
			$bundle = $val[csf("bundle_no")];
			$pattern = $val[csf("pattern_no")];
			$table_no = $val[csf("table_no")];
			$size_name = $size_arr[$val[csf("size_id")]];
			$gmts_no_start = $val[csf("number_start")];
			$gmts_no_end = $val[csf("number_end")];
			$cut_qty = $val[csf("marker_qty")];

			$buyer_name_str = "";
			if ($client_id != 0) $buyer_name_str = $buyer_library[$buyer_name] . '-' . $buyer_library[$client_id];
			else $buyer_name_str = $buyer_library[$buyer_name];
			//$dbl_no="17910000000012"; My Work
			if ($val[csf('is_excess')] == 1) $country = "EXCESS";
			else $country = $country_arr[$val[csf("country_id")]];

			$pdf->Code40($i, $j - 2, $barcode . " P. Date: " . date("j F, g:i a"), $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

			$pdf->Code40($i, $j + 1.2, $job_no . " , " . $bundle, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

			$pdf->Code40($i, $j + 4.2, "Cut Qty " . $cut_qty . ", Front Part " . "(" . $pattern . ")", $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

			$pdf->Code40($i, $j + 7.2, "Table No: " . $table_no . ", OCN: " . $order_cut_no . ", " . $cut_date, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

			$pdf->Code40($i, $j + 10.2, "Buyer Name: " . $buyer_name_str . ", Order No: " . $po_number, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

			$pdf->Code40($i, $j + 13.2, "Style No: " . $style_name, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

			$pdf->Code40($i, $j + 16.2, "Item: " . $garments_item[$item_name] . ", Country: " . $country, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

			$pdf->Code40($i, $j + 19.2, "Color: " . $color_library[$data[5]], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

			$pdf->Code40($i, $j + 22.2, "Size No: " . $size_name . "[" . $manualSizeArr[$val[csf("size_id")]] . "],", $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
			$pdf->Code40($i + 17.2, $j + 22.2, "Batch: " . $batch_no . ", Shade: " . $shade, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

			$pdf->Code40($i, $j + 25.2, "Gmts No: " . $gmts_no_start . "-" . $gmts_no_end . ", Gmts Qty: " . $val[csf("size_qty")], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

			$pdf->Code39($i - 1, $j + 33.2, $val[csf("barcode_no")], $ext = true, $cks = false, $w = 0.22, $h = 17, $wide = true, $textonly = false, $fontSize = 11);

			$k++;
			$i = 2;
			$j = $j + 23;
			$br++;
		}
	}

	foreach (glob("" . "*.pdf") as $filename) {
		@unlink($filename);
	}
	$name = 'bundle_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output("" . $name, 'F');
	echo $name;
	exit();
}
if ($action == "print_barcode_one_urmi_eg_a4") {
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	require('../../../ext_resource/pdf/code39.php');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');

	$data = explode("***", $data);
	$detls_id = $data[3];


	$buyer_library = return_library_array("select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
	$table_no_library = return_library_array("select id,table_no  from  lib_cutting_table", "id", "table_no");
	//$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name'); 
	$country_arr = return_library_array("select id, short_name from lib_country", 'id', 'short_name');
	// $sub_process_id=return_field_value2("group_concat(b.sub_process_id)  as sub_process_id ","pro_recipe_entry_dtls b, pro_recipe_entry_mst a",
	$order_cut_no = return_field_value(" order_cut_no ", "ppl_cut_lay_dtls", " id=$detls_id and status_active=1 and is_deleted=0 ", "order_cut_no");
	$manualSizeArr = return_library_array("select size_id, manual_size_name from ppl_cut_lay_size where dtls_id=$detls_id", 'size_id', 'manual_size_name');
	//echo $order_cut_no;
	$pdf = new PDF_Code39();
	//$pdf->SetFont('Times', '', 20);
	$pdf->AddPage();

	$sql_mst = "SELECT a.id,a.size_id,a.bundle_no,a.number_start,a.number_end,a.size_qty,a.country_id,a.roll_id,a.roll_no,b.bundle_sequence, a.pattern_no,a.barcode_no,b.marker_qty 
	from ppl_cut_lay_bundle  a,ppl_cut_lay_size_dtls b
	where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0]) order by b.bundle_sequence,a.id";
	//echo $sql_mst;
	$color_sizeID_arr = sql_select($sql_mst);

	$i = 8;
	$j = 4;
	$k = 0;
	$bundle_array = array();
	$br = 0;
	$n = 0;
	$sql_roll = "select roll_id, batch_no, shade from pro_roll_details where mst_id='$data[2]' and entry_form=715 and status_active=1";

	//echo $sql_roll;die;
	$roll_sql = sql_select($sql_roll);
	$roll_data_arr = array();
	foreach ($roll_sql as $row) {
		$roll_data_arr[$row[csf("roll_id")]]['batch'] = $row[csf("batch_no")];
		$roll_data_arr[$row[csf("roll_id")]]['shade'] = $row[csf("shade")];
	}

	$sql_name = sql_select("SELECT b.buyer_name,b.style_ref_no,b.product_dept,a.po_number,b.job_no from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.id=$data[6]");
	foreach ($sql_name as $value) {
		$product_dept_name = $value[csf('product_dept')];
		$style_name = $value[csf('style_ref_no')];
		$buyer_name = $value[csf('buyer_name')];
		$po_number = $value[csf('po_number')];
		$job_no = $value[csf('job_no')];
	}

	$sql_cut_name = sql_select("SELECT entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	foreach ($sql_cut_name as $cut_value) {
		$table_name = $cut_value[csf('table_no')];
		$cut_date = change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix = $cut_value[csf('cut_num_prefix_no')];
		$company_id = $cut_value[csf('company_id')];
		//$batch_no=$cut_value[csf('batch_id')];
		$comp_name = return_field_value("company_short_name", "lib_company", "id=$company_id");
		$new_cut_no = $comp_name . "-" . $cut_prifix;
		$bundle_title = "";
	}

	if ($data[7] == "") $data[7] = 0;
	$bundle_copy_lib = return_library_array("SELECT id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])", 'id', 'bundle_use_for');
	$cope_page = 1;
	$body_part = explode(",", $data[7]);
	if (count($body_part) != 0) {
		foreach ($body_part as $inf) {
			foreach ($color_sizeID_arr as $val) {
				$batch_no = $roll_data_arr[$val[csf("roll_id")]]['batch'];
				$shade = $roll_data_arr[$val[csf("roll_id")]]['shade'];
				$pattern = $val[csf("pattern_no")];
				// bottom Right side page no show
				$pdf->Code40(190, 288, 'Page No: ' . $cope_page, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 6);
				if ($br == 24) {
					$cope_page++;
					$pdf->AddPage();
					$br = 0;
					$i = 8;
					$j = 4;
					$k = 0;
				}
				if ($k > 0 && $k < 4) {
					$i = $i + 50;
				}

				$pdf->Code40($i, $j - 1.5, $val[csf("barcode_no")] . " P. Date: " . date("j F, g:i a"), $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true, 5);
				$pdf->Code40($i, $j + 1.2, $job_no . " , " . $val[csf("bundle_no")], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true, 5);
				$pdf->Code40($i, $j + 4.2, "Cut Qty " . $val[csf("marker_qty")] . "," . $bundle_copy_lib[$inf] . "(" . $pattern . ")", $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true, 5);
				$pdf->Code40($i, $j + 7.2, "Table No: " . $table_no_library[$table_name] . ", OCN: " . $order_cut_no . ", " . $cut_date, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true, 5);
				$pdf->Code40($i, $j + 10.2, "Buyer Name: " . $buyer_library[$buyer_name] . ", Order No: " . $po_number, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true, 5);
				$pdf->Code40($i, $j + 13.2, "Style No: " . $style_name, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true, 4);
				$pdf->Code40($i, $j + 16.2, "Item: " . $garments_item[$data[4]] . ", Country: " . $country_arr[$val[csf("country_id")]], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true, 5);
				$pdf->Code40($i, $j + 19.2, "Color: " . $color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true, 5);
				$pdf->Code40($i, $j + 22.2, "Size No: " . $size_arr[$val[csf("size_id")]] . "[" . $manualSizeArr[$val[csf("size_id")]] . "], Batch: " . $batch_no . ", Shade: " . $shade, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true, 5);
				$pdf->Code40($i, $j + 25.2, "Gmts. No :" . $val[csf("number_start")] . "-" . $val[csf("number_end")] . ", Gmts Qty: " . $val[csf("size_qty")], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true, 5);

				$pdf->Code39($i - 1, $j + 32.2, $val[csf("barcode_no")], $ext = true, $cks = false, $w = 0.16, $h = 8, $wide = true, $textonly = false, $fontSize = 6);
				$k++;
				if ($k == 4) {
					$k = 0;
					$i = 8;
					$j = $j + 48;
				}
				$br++;
			}
		}
	} else {
		foreach ($color_sizeID_arr as $val) {
			if ($br == 12) {
				$cope_page++;
				$pdf->AddPage();
				$br = 0;
				$i = 10;
				$j = 12;
				$k = 0;
			}
			if ($k > 0 && $k < 3) {
				$i = $i + 60;
			}

			$batch_no = $roll_data_arr[$val[csf("roll_id")]]['batch'];
			$shade = $roll_data_arr[$val[csf("roll_id")]]['shade'];

			$pdf->Code40($i, $j - 2, $val[csf("barcode_no")] . " P. Date: " . date("j F, g:i a"), $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
			$pdf->Code40($i, $j + 1.2, $job_no . " , " . $val[csf("bundle_no")], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

			$pdf->Code40($i, $j + 4.2, "Cut Qty " . $val[csf("marker_qty")] . ",  " . "" . $inf[csf("bundle_use_for")] . "", $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true, 5);
			$pdf->Code40($i, $j + 7.2, "Table No: " . $table_no_library[$table_name] . ", OCN: " . $order_cut_no . ", " . $cut_date, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
			$pdf->Code40($i, $j + 10.2, "Buyer Name: " . $buyer_library[$buyer_name] . ", Order No: " . $po_number, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
			$pdf->Code40($i, $j + 13.2, "Style No: " . $style_name, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
			$pdf->Code40($i, $j + 16.2, "Item: " . $garments_item[$data[4]] . ", Country: " . $country_arr[$val[csf("country_id")]], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
			$pdf->Code40($i, $j + 19.2, "Color: " . $color_library[$data[5]], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
			$pdf->Code40($i, $j + 22.2, "Size No: " . $size_arr[$val[csf("size_id")]] . ", Batch: " . $batch_no . ", Shade: " . $shade, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
			$pdf->Code40($i, $j + 25.2, "Gmts. No :" . $val[csf("number_start")] . "-" . $val[csf("number_end")] . ", Gmts Qty: " . $val[csf("size_qty")], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

			//$pdf->Code39($i-1, $j+35.2, $val[csf("barcode_no")]);

			$pdf->Code39($i - 1, $j + 34.2, $val[csf("barcode_no")], $ext = true, $cks = false, $w = 0.22, $h = 17, $wide = true, $textonly = false, $fontSize = 11);
			$k++;

			if ($k == 2) {
				$k = 0;
				$i = 10;
				$j = $j + 67;
			}
			$br++;
		}
	}
	foreach (glob("" . "*.pdf") as $filename) {
		@unlink($filename);
	}
	$name = 'bundle_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output("" . $name, 'F');
	echo $name;
	exit();
}

if ($action == "print_barcode_one_urmi_v5_bk") {
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	require('../../../ext_resource/pdf/code39.php');
	//require('../../../ext_resource/pdf/code128.php');


	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');//order_cut_no

	$data = explode("***", $data);


	// ================================== BUNDLE USE FOR ENTRY ============================
	if ($data[7] != "") // when body part exist
	{
		$con = connect();
		$job_id = return_field_value("id", "wo_po_details_master", " job_no='$data[1]' and status_active=1 and is_deleted=0 ", "id");

		// $delStatus = sql_delete("ppl_cut_lay_bundle_use_for","status_active*is_deleted","0*1",'job_id',$job_id,1);
		$delStatus = execute_query("delete from ppl_cut_lay_bundle_use_for where job_id=$job_id", 0);

		$field_array = "id,job_id,bodypart_id,inserted_by,insert_date";
		$bodypart_id = array_unique(explode(",", $data[7]));
		$data_array = "";
		$j = 0;
		$id = return_next_id("id", "ppl_cut_lay_bundle_use_for", 1);
		foreach ($bodypart_id as $val) {
			if ($j == 0) $data_array = "(" . $id . "," . $job_id . "," . $val . "," . $user_id . ",'" . $pc_date_time . "')";
			else $data_array .= ",(" . $id . "," . $job_id . "," . $val . "," . $user_id . ",'" . $pc_date_time . "')";
			$id = $id + 1;
			$j++;
		}

		$insertStatus = sql_insert("ppl_cut_lay_bundle_use_for", $field_array, $data_array, 0);

		// echo "10**insert into ppl_cut_lay_bundle_use_for (".$field_array.") values ".$data_array;die;

		if ($db_type == 0) {
			if ($insertStatus) {
				mysql_query("COMMIT");
				// echo "1**".str_replace("'","",$hidden_po_break_down_id);
			} else {
				mysql_query("ROLLBACK");
				// echo "10**".str_replace("'","",$hidden_po_break_down_id);
			}
		} else {
			if ($insertStatus) {
				oci_commit($con);
				// echo "1**".str_replace("'","",$hidden_po_break_down_id);
			} else {
				oci_rollback($con);
				// echo "10**".str_replace("'","",$hidden_po_break_down_id);
			}
		}
	}
	// =========================== end =================================

	$detls_id = $data[3];
	$batch_id = return_field_value("batch_id", "ppl_cut_lay_dtls", "id=$detls_id");
	$buyer_library = return_library_array("select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
	$table_no_library = return_library_array("select id,table_no  from  lib_cutting_table", "id", "table_no");
	$country_arr = return_library_array("select id, short_name from lib_country", 'id', 'short_name');
	$lib_season_arr = return_library_array("select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0", 'id', 'season_name');
	$order_cut_no = return_field_value("order_cut_no", "ppl_cut_lay_dtls", "id=$detls_id and status_active=1 and is_deleted=0", "order_cut_no");
	$pdf = new PDF_Code39('P', 'mm', array(65, 35)); //'a10'
	$pdf->AddPage();


	$bacth_array = array();
	$batchData = sql_select("select id, batch_no, extention_no from pro_batch_create_mst where entry_form in(0,7,37,66,68)");
	foreach ($batchData as $row) {
		$ext = '';
		if ($row[csf('extention_no')] > 0) {
			$ext = '-' . $row[csf('extention_no')];
		}
		$bacth_array[$row[csf('id')]] = $row[csf('batch_no')] . $ext;
	}

	$color_sizeID_arr = sql_select("SELECT a.id, a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no
	from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0])
	group by a.id, a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no order by b.bundle_sequence,a.id");
	$col_size_data_arr = array();
	foreach ($color_sizeID_arr as $row) {
		$test_data[$row[csf("roll_id")]] = $row[csf("roll_id")];
		$col_size_data_arr[$row[csf("barcode_no")]] = $row[csf("size_qty")];
	}
	$i = 2;
	$j = 0;
	$k = 0;
	$bundle_array = array();
	$br = 0;
	$n = 0;
	$sql_name = sql_select("SELECT b.buyer_name, b.client_id, b.style_ref_no, b.product_dept, b.season_buyer_wise as season_matrix, a.po_number, a.id as po_id,a.t_year from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.id in(" . $data[6] . ")");
	$po_data_arr = array();
	foreach ($sql_name as $value) {
		$po_data_arr[$value[csf('po_id')]]["style_ref_no"] = $value[csf('style_ref_no')];
		$po_data_arr[$value[csf('po_id')]]["buyer_name"] = $value[csf('buyer_name')];
		$po_data_arr[$value[csf('po_id')]]["client_id"] = $value[csf('client_id')];
		$po_data_arr[$value[csf('po_id')]]["po_number"] = $value[csf('po_number')];
		$po_data_arr[$value[csf('po_id')]]["t_year"] = $value[csf('t_year')];
		$matrix_season = $value[csf('season_matrix')];
	}
	unset($sql_name);
	//echo "select roll_id, batch_no, shade from pro_roll_details where mst_id='$data[2]' and entry_form=289 and status_active=1";
	$roll_sql = sql_select("SELECT roll_id, batch_no, shade from pro_roll_details where mst_id='$data[2]' and entry_form=715 and status_active=1");
	$roll_data_arr = array();
	foreach ($roll_sql as $row) {
		$pro_roll_data_arr[$row[csf("roll_id")]]['batch'] = $row[csf("batch_no")];
		$pro_roll_data_arr[$row[csf("roll_id")]]['shade'] = $row[csf("shade")];
	}
	//print_r($pro_roll_data_arr); die;
	unset($roll_sql);

	$sql_cut_name = sql_select("SELECT a.entry_date,a.table_no,a.cut_num_prefix_no,a.batch_id,a.company_id,a.cutting_no,b.order_cut_no,b.roll_data from ppl_cut_lay_mst a,ppl_cut_lay_dtls b where a.id=b.mst_id and a.id=$data[2] and b.status_active=1 and b.is_deleted=0");
	foreach ($sql_cut_name as $cut_value) {
		$table_name = $cut_value[csf('table_no')];
		$cut_date = change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix = $cut_value[csf('cut_num_prefix_no')];
		$company_id = $cut_value[csf('company_id')];
		$cutting_no = $cut_value[csf('cutting_no')];
		$order_cut_no = $cut_value[csf('order_cut_no')];
		$roll_data_arr = explode("=", $cut_value[csf('roll_data')]);
		$lot_no = $roll_data_arr[5];
		$comp_name = return_field_value("company_short_name", "lib_company", "id=$company_id");
		$new_cut_no = $comp_name . "-" . $cut_prifix;
		$bundle_title = "";
	}
	if ($data[7] == "") $data[7] = 0;
	$seq = explode(",", $data[7]);
	$seq_first = $seq[0];

	$sql_bundle_copy = sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
	$bundle_use_for_arr = array();
	foreach ($sql_bundle_copy as $val) {
		// $bundle_use_for .= ($bundle_use_for=="") ? $val[csf('bundle_use_for')] : ",".$val[csf('bundle_use_for')];
		$bundle_use_for_arr[$val[csf('id')]] = $val[csf('bundle_use_for')];
	}

	if ($data[7] != "") {
		$bundleUseForArray = explode(",", $data[7]);
	}

	$data_array = array();
	if (count($sql_bundle_copy) != 0) {
		foreach ($sql_bundle_copy as $inf) {
			foreach ($color_sizeID_arr as $val) {
				$data_array[$val[csf("pattern_no")]][$inf[csf('bundle_use_for')]][$val[csf('barcode_no')]]['order_id'] = $val[csf("order_id")];
				$data_array[$val[csf("pattern_no")]][$inf[csf('bundle_use_for')]][$val[csf('barcode_no')]]['roll_id'] = $val[csf("roll_id")];
				$data_array[$val[csf("pattern_no")]][$inf[csf('bundle_use_for')]][$val[csf('barcode_no')]]['bundle_num_prefix_no'] = $val[csf("bundle_num_prefix_no")];
				$data_array[$val[csf("pattern_no")]][$inf[csf('bundle_use_for')]][$val[csf('barcode_no')]]['is_excess'] = $val[csf("is_excess")];
				$data_array[$val[csf("pattern_no")]][$inf[csf('bundle_use_for')]][$val[csf('barcode_no')]]['country_id'] = $val[csf("country_id")];
				$data_array[$val[csf("pattern_no")]][$inf[csf('bundle_use_for')]][$val[csf('barcode_no')]]['bundle_no'] = $val[csf("bundle_no")];
				$data_array[$val[csf("pattern_no")]][$inf[csf('bundle_use_for')]][$val[csf('barcode_no')]]['size_id'] = $val[csf("size_id")];
				$data_array[$val[csf("pattern_no")]][$inf[csf('bundle_use_for')]][$val[csf('barcode_no')]]['number_start'] = $val[csf("number_start")];
				$data_array[$val[csf("pattern_no")]][$inf[csf('bundle_use_for')]][$val[csf('barcode_no')]]['number_end'] = $val[csf("number_end")];
				$data_array[$val[csf("pattern_no")]][$inf[csf('bundle_use_for')]][$val[csf('barcode_no')]]['id'] = $val[csf("id")];
				$data_array[$val[csf("pattern_no")]][$inf[csf('bundle_use_for')]][$val[csf('barcode_no')]]['barcode_no'] = $val[csf("barcode_no")];
			}
		}
	}

	ksort($data_array);

	// echo "<pre>";print_r($data_array);die;

	if (count($sql_bundle_copy) != 0) {
		foreach ($data_array as $pattern_no => $pattern_data) {
			// if($br==1) { $pdf->AddPage(); $br=0; $i=2; $j=0; $k=0; }
			foreach ($pattern_data as $bundle_use_for => $bundle_use_for_data) {
				foreach ($bundle_use_for_data as $barcode => $val) {
					if ($br == 1) {
						$pdf->AddPage();
						$br = 0;
						$i = 2;
						$j = 0;
						$k = 0;
					}
					if ($seq_first == $val["id"]) $symb = "@@";
					else $symb = "";
					//BNDL
					// print_r($pro_roll_data_arr); die;
					$style_name = $po_data_arr[$val['order_id']]["style_ref_no"];
					$buyer_name = $po_data_arr[$val['order_id']]["buyer_name"];
					$client_id = $po_data_arr[$val['order_id']]["client_id"];
					$po_number = $po_data_arr[$val['order_id']]["po_number"];
					$t_year = $po_data_arr[$val['order_id']]["t_year"];
					$batch_no = $pro_roll_data_arr[$val['roll_id']]['batch'];
					$shade = $pro_roll_data_arr[$val['roll_id']]['shade'];
					$bundle_no_prifix = $val["bundle_num_prefix_no"];
					$bndle_ex = explode("-", $val["bundle_no"]);
					$buyer_name_str = "";
					if ($client_id != 0) $buyer_name_str = $buyer_library[$buyer_name] . '-' . $buyer_library[$client_id];
					else $buyer_name_str = $buyer_library[$buyer_name];
					//$dbl_no="17910000000012";
					if ($val['is_excess'] == 1) $country = "EXCESS";
					else $country = $country_arr[$val["country_id"]];

					$pdf->Code40($i, $j - 2, "Byr# " . $buyer_name_str, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i + 35, $j - 2, "Bun# " . $bndle_ex[3], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

					$pdf->Code40($i, $j + 2, "Sty# " . substr($style_name, 0, 25), $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i + 45, $j + 2, "Coun# " . $country, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);


					$pdf->Code40($i, $j + 6, "PO# " . $po_number, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i + 45, $j + 6, "SH# " . $shade, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

					$pdf->Code40($i, $j + 10, "Color# " . $color_library[$data[5]], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i + 45, $j + 10, "T/N# " . $table_no_library[$table_name], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

					$pdf->Code40($i, $j + 14, "P/N# " . $bundle_use_for, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i + 45, $j + 14, "Cut/N# " . $order_cut_no, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

					$pdf->Code40($i, $j + 18, "Stkr# " . $val["number_start"] . "-" . $val["number_end"], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i + 20, $j + 18, "Size# " . $size_arr[$val["size_id"]] . "(" . $pattern_no . ")", $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i + 50, $j + 18, "Qty#" . $col_size_data_arr[$barcode], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

					//$pdf->Code40($i, $j+19.5, "P/N# ".$bundle_use_for, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide=true,true,7);
					//$pdf->Code40($i+45, $j+19.5, "T/N# ".$table_no_library[$table_name], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide=true,true,7);

					//$pdf->Code40($i, $j+23.8, "Stkr# ".$val["number_start"]."-".$val["number_end"], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true,7) ;		
					//$pdf->Code40($i+35, $j+23.8, "Cut/N# ".$order_cut_no, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true,7) ;

					$pdf->Code40($i, $j + 22, $val['barcode_no'], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
					$pdf->Code40($i + 35, $j + 22, "Cut/N# " . $cutting_no, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
					//$pdf->Code40($i+35, $j+28.1, "Coun# ".$country, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide=true,true,7);


					// $pdf->Code40($i, $j+19, $val[csf("barcode_no")], $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true,7) ;
					//$pdf->Code40($i, $j+22, $val[csf("bundle_no")],$w =0.2, $h = 8);
					$pdf->Code40($i, $j + 28, $val['barcode_no'], true, false, $w = 0.23, $h = 6);


					// $pdf->Code39($i+1.4, $j+28, $barcode);


					$k++;
					$i = 2;
					$j = $j + 28.1;
					$br++;
				}
			}
		}
	} else {
		foreach ($color_sizeID_arr as $val) {
			if ($br == 1) {
				$pdf->AddPage();
				$br = 0;
				$i = 2;
				$j = 0;
				$k = 0;
			}
			if ($seq_first == $inf[csf("id")]) $symb = "@@";
			else $symb = "";
			//BNDL
			$style_name = $po_data_arr[$val[csf('order_id')]]["style_ref_no"];
			$buyer_name = $po_data_arr[$val[csf('order_id')]]["buyer_name"];
			$client_id = $po_data_arr[$val[csf('order_id')]]["client_id"];
			$po_number = $po_data_arr[$val[csf('order_id')]]["po_number"];
			$t_year = $po_data_arr[$val[csf('order_id')]]["t_year"];
			$batch_no = $pro_roll_data_arr[$val[csf('roll_id')]]['batch'];
			$shade = $pro_roll_data_arr[$val[csf('roll_id')]]['shade'];
			$bundle_no_prifix = $val[csf("bundle_num_prefix_no")];
			$buyer_name_str = "";
			if ($client_id != 0) $buyer_name_str = $buyer_library[$buyer_name] . '-' . $buyer_library[$client_id];
			else $buyer_name_str = $buyer_library[$buyer_name];
			//$dbl_no="17910000000012";
			if ($val[csf('is_excess')] == 1) $country = "EXCESS";
			else $country = $country_arr[$val[csf("country_id")]];

			/*$pdf->Code40($i, $j-2, "Byr#".$buyer_name_str, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true,7);
				$pdf->Code40($i+35, $j-2, "Bun# ".$bndle_ex[3], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true,7);

				$pdf->Code40($i, $j+2.3, "Sty# ".$style_name, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true,7) ;
				

				$pdf->Code40($i, $j+6.6, "PO# ".$po_number, $ext = true, $cks = false, $w =0.7, $h = 1, $wide = true, true,7) ;
				$pdf->Code40($i+45, $j+6.6, "SH# ". $shade, $ext = true, $cks = false, $w =0.7, $h = 1, $wide = true, true,7) ;

				$pdf->Code40($i, $j+10.9, "Color#".$color_library[$data[5]], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true,7) ;
				$pdf->Code40($i+45, $j+10.9, "L/N# ".$lot_no, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true,7) ;

				// $pdf->Code40($i, $j+12, "Cut/N# ".$order_cut_no, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true,7) ;
				$pdf->Code40($i, $j+15.2, "Size#".$size_arr[$val[csf("size_id")]]."(".$val[csf("pattern_no")].")", $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true,7) ;
				$pdf->Code40($i+35, $j+15.2, "Qty#".$col_size_data_arr[$val[csf("barcode_no")]], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true,7) ;

				$pdf->Code40($i, $j+19.5, "P/N#".$inf[csf('bundle_use_for')], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide=true,true,7);
				$pdf->Code40($i+45, $j+19.5, "T/N# ".$table_no_library[$table_name], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide=true,true,7);
			
				$pdf->Code40($i, $j+23.8, "Stkr# ".$val[csf("number_start")]."-".$val[csf("number_end")], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true,7) ;		
				$pdf->Code40($i+35, $j+23.8, "Cut/N# ".$order_cut_no, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true,7) ;		
				$pdf->Code40($i, $j+28.1, "Cut/N# ".$cutting_no, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide=true,true,7);
				$pdf->Code40($i+35, $j+28.1, "Coun# ".$country, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide=true,true,7);*/

			$pdf->Code40($i, $j - 2, "Byr# " . $buyer_name_str, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
			$pdf->Code40($i + 35, $j - 2, "Bun# " . $bndle_ex[3], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

			$pdf->Code40($i, $j + 2, "Sty# " . substr($style_name, 0, 25), $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
			$pdf->Code40($i + 45, $j + 2, "Coun# " . $country, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);


			$pdf->Code40($i, $j + 6, "PO# " . $po_number, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
			$pdf->Code40($i + 45, $j + 6, "SH# " . $shade, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

			$pdf->Code40($i, $j + 10, "Color# " . $color_library[$data[5]], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
			$pdf->Code40($i + 45, $j + 10, "T/N# " . $table_no_library[$table_name], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

			$pdf->Code40($i, $j + 14, "P/N# " . $bundle_use_for, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
			$pdf->Code40($i + 45, $j + 14, "Cut/N# " . $order_cut_no, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

			$pdf->Code40($i, $j + 18, "Stkr# " . $val["number_start"] . "-" . $val["number_end"], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
			$pdf->Code40($i + 20, $j + 18, "Size# " . $size_arr[$val["size_id"]] . "(" . $pattern_no . ")", $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);
			$pdf->Code40($i + 50, $j + 18, "Qty#" . $col_size_data_arr[$barcode], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true, 7);

			//$pdf->Code40($i, $j+19.5, "P/N# ".$bundle_use_for, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide=true,true,7);
			//$pdf->Code40($i+45, $j+19.5, "T/N# ".$table_no_library[$table_name], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide=true,true,7);

			//$pdf->Code40($i, $j+23.8, "Stkr# ".$val["number_start"]."-".$val["number_end"], $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true,7) ;		
			//$pdf->Code40($i+35, $j+23.8, "Cut/N# ".$order_cut_no, $ext = true, $cks = false, $w = 0.7, $h = 1, $wide = true, true,7) ;

			$pdf->Code40($i, $j + 22, $val['barcode_no'], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
			$pdf->Code40($i + 35, $j + 22, "Cut/N# " . $cutting_no, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
			//$pdf->Code40($i+35, $j+28.1, "Coun# ".$country, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide=true,true,7);

			$pdf->Code39($i, $j + 28, $val['barcode_no'], true, false, $w = 0.24, $h = 6);

			$k++;
			$i = 2;
			$j = $j + 28.1;
			$br++;
		}
	}

	foreach (glob("" . "*.pdf") as $filename) {
		@unlink($filename);
	}
	$name = 'bundle_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output("" . $name, 'F');
	echo $name;
	exit();
}

if ($action == "print_barcode_one_urmi_v5") {
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	// require('../../../ext_resource/pdf/code39.php');
	require('../../../ext_resource/pdf/code128.php');


	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');//order_cut_no

	$data = explode("***", $data);


	// ================================== BUNDLE USE FOR ENTRY ============================
	if ($data[7] != "") // when body part exist
	{
		$con = connect();
		$job_id = return_field_value("id", "wo_po_details_master", " job_no='$data[1]' and status_active=1 and is_deleted=0 ", "id");

		// $delStatus = sql_delete("ppl_cut_lay_bundle_use_for","status_active*is_deleted","0*1",'job_id',$job_id,1);
		$delStatus = execute_query("delete from ppl_cut_lay_bundle_use_for where job_id=$job_id", 0);

		$field_array = "id,job_id,bodypart_id,inserted_by,insert_date";
		$bodypart_id = array_unique(explode(",", $data[7]));
		$data_array = "";
		$j = 0;
		$id = return_next_id("id", "ppl_cut_lay_bundle_use_for", 1);
		foreach ($bodypart_id as $val) {
			if ($j == 0) $data_array = "(" . $id . "," . $job_id . "," . $val . "," . $user_id . ",'" . $pc_date_time . "')";
			else $data_array .= ",(" . $id . "," . $job_id . "," . $val . "," . $user_id . ",'" . $pc_date_time . "')";
			$id = $id + 1;
			$j++;
		}

		$insertStatus = sql_insert("ppl_cut_lay_bundle_use_for", $field_array, $data_array, 0);

		// echo "10**insert into ppl_cut_lay_bundle_use_for (".$field_array.") values ".$data_array;die;

		if ($db_type == 0) {
			if ($insertStatus) {
				mysql_query("COMMIT");
				// echo "1**".str_replace("'","",$hidden_po_break_down_id);
			} else {
				mysql_query("ROLLBACK");
				// echo "10**".str_replace("'","",$hidden_po_break_down_id);
			}
		} else {
			if ($insertStatus) {
				oci_commit($con);
				// echo "1**".str_replace("'","",$hidden_po_break_down_id);
			} else {
				oci_rollback($con);
				// echo "10**".str_replace("'","",$hidden_po_break_down_id);
			}
		}
	}
	// =========================== end =================================

	$detls_id = $data[3];
	$batch_id = return_field_value("batch_id", "ppl_cut_lay_dtls", "id=$detls_id");
	$buyer_library = return_library_array("select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
	$table_no_library = return_library_array("select id,table_no  from  lib_cutting_table", "id", "table_no");
	$country_arr = return_library_array("select id, short_name from lib_country", 'id', 'short_name');
	$lib_season_arr = return_library_array("select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0", 'id', 'season_name');
	$order_cut_no = return_field_value("order_cut_no", "ppl_cut_lay_dtls", "id=$detls_id and status_active=1 and is_deleted=0", "order_cut_no");

	// $pdf=new PDF_Code39('P','mm',array(65,35));//'a10'
	// $pdf->AddPage();

	$pdf = new PDF_Code128('P', 'mm', array(65, 45));
	$pdf->AddPage();
	$pdf->SetFont('Arial', 'B', 7);


	$bacth_array = array();
	$batchData = sql_select("select id, batch_no, extention_no from pro_batch_create_mst where entry_form in(0,7,37,66,68)");
	foreach ($batchData as $row) {
		$ext = '';
		if ($row[csf('extention_no')] > 0) {
			$ext = '-' . $row[csf('extention_no')];
		}
		$bacth_array[$row[csf('id')]] = $row[csf('batch_no')] . $ext;
	}

	$color_sizeID_arr = sql_select("SELECT a.id, a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no
	from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0])
	group by a.id, a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no order by b.bundle_sequence,a.id");
	$col_size_data_arr = array();
	foreach ($color_sizeID_arr as $row) {
		$test_data[$row[csf("roll_id")]] = $row[csf("roll_id")];
		$col_size_data_arr[$row[csf("barcode_no")]] = $row[csf("size_qty")];
	}

	$sql_name = sql_select("SELECT b.buyer_name, b.client_id, b.style_ref_no, b.product_dept, b.season_buyer_wise as season_matrix, a.po_number, a.id as po_id,a.t_year from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.id in(" . $data[6] . ")");
	$po_data_arr = array();
	foreach ($sql_name as $value) {
		$po_data_arr[$value[csf('po_id')]]["style_ref_no"] = $value[csf('style_ref_no')];
		$po_data_arr[$value[csf('po_id')]]["buyer_name"] = $value[csf('buyer_name')];
		$po_data_arr[$value[csf('po_id')]]["client_id"] = $value[csf('client_id')];
		$po_data_arr[$value[csf('po_id')]]["po_number"] = $value[csf('po_number')];
		$po_data_arr[$value[csf('po_id')]]["t_year"] = $value[csf('t_year')];
		$matrix_season = $value[csf('season_matrix')];
	}
	unset($sql_name);
	//echo "select roll_id, batch_no, shade from pro_roll_details where mst_id='$data[2]' and entry_form=289 and status_active=1";
	$roll_sql = sql_select("SELECT roll_id, batch_no, shade from pro_roll_details where mst_id='$data[2]' and entry_form=715 and status_active=1");
	$roll_data_arr = array();
	foreach ($roll_sql as $row) {
		$pro_roll_data_arr[$row[csf("roll_id")]]['batch'] = $row[csf("batch_no")];
		$pro_roll_data_arr[$row[csf("roll_id")]]['shade'] = $row[csf("shade")];
	}
	//print_r($pro_roll_data_arr); die;
	unset($roll_sql);

	$sql_cut_name = sql_select("SELECT a.entry_date,a.table_no,a.cut_num_prefix_no,a.batch_id,a.company_id,a.cutting_no,b.order_cut_no,b.roll_data from ppl_cut_lay_mst a,ppl_cut_lay_dtls b where a.id=b.mst_id and a.id=$data[2] and b.status_active=1 and b.is_deleted=0");
	foreach ($sql_cut_name as $cut_value) {
		$table_name = $cut_value[csf('table_no')];
		$cut_date = change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix = $cut_value[csf('cut_num_prefix_no')];
		$company_id = $cut_value[csf('company_id')];
		$cutting_no = $cut_value[csf('cutting_no')];
		$order_cut_no = $cut_value[csf('order_cut_no')];
		$roll_data_arr = explode("=", $cut_value[csf('roll_data')]);
		$lot_no = $roll_data_arr[5];
		$comp_name = return_field_value("company_short_name", "lib_company", "id=$company_id");
		$new_cut_no = $comp_name . "-" . $cut_prifix;
		$bundle_title = "";
	}
	if ($data[7] == "") $data[7] = 0;
	$seq = explode(",", $data[7]);
	$seq_first = $seq[0];

	$sql_bundle_copy = sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
	$bundle_use_for_arr = array();
	foreach ($sql_bundle_copy as $val) {
		// $bundle_use_for .= ($bundle_use_for=="") ? $val[csf('bundle_use_for')] : ",".$val[csf('bundle_use_for')];
		$bundle_use_for_arr[$val[csf('id')]] = $val[csf('bundle_use_for')];
	}

	if ($data[7] != "") {
		$bundleUseForArray = explode(",", $data[7]);
	}

	$data_array = array();
	if (count($sql_bundle_copy) != 0) {
		foreach ($sql_bundle_copy as $inf) {
			foreach ($color_sizeID_arr as $val) {
				$data_array[$val[csf("pattern_no")]][$inf[csf('bundle_use_for')]][$val[csf('barcode_no')]]['order_id'] = $val[csf("order_id")];
				$data_array[$val[csf("pattern_no")]][$inf[csf('bundle_use_for')]][$val[csf('barcode_no')]]['roll_id'] = $val[csf("roll_id")];
				$data_array[$val[csf("pattern_no")]][$inf[csf('bundle_use_for')]][$val[csf('barcode_no')]]['bundle_num_prefix_no'] = $val[csf("bundle_num_prefix_no")];
				$data_array[$val[csf("pattern_no")]][$inf[csf('bundle_use_for')]][$val[csf('barcode_no')]]['is_excess'] = $val[csf("is_excess")];
				$data_array[$val[csf("pattern_no")]][$inf[csf('bundle_use_for')]][$val[csf('barcode_no')]]['country_id'] = $val[csf("country_id")];
				$data_array[$val[csf("pattern_no")]][$inf[csf('bundle_use_for')]][$val[csf('barcode_no')]]['bundle_no'] = $val[csf("bundle_no")];
				$data_array[$val[csf("pattern_no")]][$inf[csf('bundle_use_for')]][$val[csf('barcode_no')]]['size_id'] = $val[csf("size_id")];
				$data_array[$val[csf("pattern_no")]][$inf[csf('bundle_use_for')]][$val[csf('barcode_no')]]['number_start'] = $val[csf("number_start")];
				$data_array[$val[csf("pattern_no")]][$inf[csf('bundle_use_for')]][$val[csf('barcode_no')]]['number_end'] = $val[csf("number_end")];
				$data_array[$val[csf("pattern_no")]][$inf[csf('bundle_use_for')]][$val[csf('barcode_no')]]['id'] = $val[csf("id")];
				$data_array[$val[csf("pattern_no")]][$inf[csf('bundle_use_for')]][$val[csf('barcode_no')]]['barcode_no'] = $val[csf("barcode_no")];
			}
		}
	}

	ksort($data_array);

	// echo "<pre>";print_r($data_array);die;
	$i = 2;
	$j = 4;
	$k = 0;
	$bundle_array = array();
	$br = 0;
	$n = 0;
	if (count($sql_bundle_copy) != 0) {
		foreach ($data_array as $pattern_no => $pattern_data) {
			// if($br==1) { $pdf->AddPage(); $br=0; $i=2; $j=2; $k=0; }
			foreach ($pattern_data as $bundle_use_for => $bundle_use_for_data) {
				foreach ($bundle_use_for_data as $barcode => $val) {
					if ($br == 1) {
						$pdf->AddPage();
						$br = 0;
						$i = 2;
						$j = 4;
						$k = 0;
					}
					if ($seq_first == $val["id"]) $symb = "@@";
					else $symb = "";
					//BNDL
					// print_r($pro_roll_data_arr); die;
					$style_name = $po_data_arr[$val['order_id']]["style_ref_no"];
					$buyer_name = $po_data_arr[$val['order_id']]["buyer_name"];
					$client_id = $po_data_arr[$val['order_id']]["client_id"];
					$po_number = $po_data_arr[$val['order_id']]["po_number"];
					$t_year = $po_data_arr[$val['order_id']]["t_year"];
					$batch_no = $pro_roll_data_arr[$val['roll_id']]['batch'];
					$shade = $pro_roll_data_arr[$val['roll_id']]['shade'];
					$bundle_no_prifix = $val["bundle_num_prefix_no"];
					$bndle_ex = explode("-", $val["bundle_no"]);
					$buyer_name_str = "";
					if ($client_id != 0) $buyer_name_str = $buyer_library[$buyer_name] . '-' . $buyer_library[$client_id];
					else $buyer_name_str = $buyer_library[$buyer_name];
					//$dbl_no="17910000000012";
					if ($val['is_excess'] == 1) $country = "EXCESS";
					else $country = $country_arr[$val["country_id"]];

					$pdf->SetXY($i, $j);
					$pdf->Write(0,  "Byr# " . $buyer_name_str);
					$pdf->SetXY($i + 40, $j);
					$pdf->Write(0, "T/N# " . $table_no_library[$table_name]);

					$pdf->SetXY($i, $j + 3);
					$pdf->Write(0,  "Sty# " . substr($style_name, 0, 22));
					$pdf->SetXY($i + 40, $j + 3);
					$pdf->Write(0, "Coun# " . $country);

					$pdf->SetXY($i, $j + 6);
					$pdf->Write(0,  "PO# " . substr($po_number, 0, 22));
					$pdf->SetXY($i + 40, $j + 6);
					$pdf->Write(0, "SH# " . $shade);

					$pdf->SetXY($i, $j + 9);
					$pdf->Write(0,  "Color# " . substr($color_library[$data[5]], 0, 22));
					$pdf->SetXY($i + 40, $j + 9);
					$pdf->Write(0, "Bun# " . $bndle_ex[3]);

					$pdf->SetXY($i, $j + 12);
					$pdf->Write(0,  "P/N# " . substr($bundle_use_for, 0, 22));
					$pdf->SetXY($i + 40, $j + 12);
					$pdf->Write(0, "Cut/N# " . $order_cut_no);

					$pdf->SetXY($i, $j + 15);
					$pdf->Write(0,  "Stkr# " . $val["number_start"] . "-" . $val["number_end"]);
					$pdf->SetXY($i + 20, $j + 15);
					$pdf->Write(0, "Size# " . $size_arr[$val["size_id"]] . "(" . $pattern_no . ")");
					$pdf->SetXY($i + 45, $j + 15);
					$pdf->Write(0, "Qty#" . $col_size_data_arr[$barcode]);

					$pdf->SetXY($i, $j + 18);
					$pdf->Write(0,  $val['barcode_no']);
					$pdf->SetXY($i + 30, $j + 18);
					$pdf->Write(0, "Cut/N# " . $cutting_no);

					$pdf->Code128($i + 1, $j + 21, $val["barcode_no"], 60, 8);


					$k++;
					$i = 2;
					$j = 4;
					$br++;
				}
			}
		}
	} else {
		foreach ($color_sizeID_arr as $val) {
			if ($br == 1) {
				$pdf->AddPage();
				$br = 0;
				$i = 2;
				$j = 0;
				$k = 0;
			}
			if ($seq_first == $inf[csf("id")]) $symb = "@@";
			else $symb = "";
			//BNDL
			$style_name = $po_data_arr[$val[csf('order_id')]]["style_ref_no"];
			$buyer_name = $po_data_arr[$val[csf('order_id')]]["buyer_name"];
			$client_id = $po_data_arr[$val[csf('order_id')]]["client_id"];
			$po_number = $po_data_arr[$val[csf('order_id')]]["po_number"];
			$t_year = $po_data_arr[$val[csf('order_id')]]["t_year"];
			$batch_no = $pro_roll_data_arr[$val[csf('roll_id')]]['batch'];
			$shade = $pro_roll_data_arr[$val[csf('roll_id')]]['shade'];
			$bundle_no_prifix = $val[csf("bundle_num_prefix_no")];
			$buyer_name_str = "";
			if ($client_id != 0) $buyer_name_str = $buyer_library[$buyer_name] . '-' . $buyer_library[$client_id];
			else $buyer_name_str = $buyer_library[$buyer_name];
			//$dbl_no="17910000000012";
			if ($val[csf('is_excess')] == 1) $country = "EXCESS";
			else $country = $country_arr[$val[csf("country_id")]];

			$pdf->SetXY($i, $j);
			$pdf->Write(0,  "Byr# " . $buyer_name_str);
			$pdf->SetXY($i + 40, $j);
			$pdf->Write(0, "T/N# " . $table_no_library[$table_name]);

			$pdf->SetXY($i, $j + 3);
			$pdf->Write(0,  "Sty# " . substr($style_name, 0, 22));
			$pdf->SetXY($i + 40, $j + 3);
			$pdf->Write(0, "Coun# " . $country);

			$pdf->SetXY($i, $j + 6);
			$pdf->Write(0,  "PO# " . substr($po_number, 0, 22));
			$pdf->SetXY($i + 40, $j + 6);
			$pdf->Write(0, "SH# " . $shade);

			$pdf->SetXY($i, $j + 9);
			$pdf->Write(0,  "Color# " . substr($color_library[$data[5]], 0, 22));
			$pdf->SetXY($i + 40, $j + 9);
			$pdf->Write(0, "Bun# " . $bndle_ex[3]);

			$pdf->SetXY($i, $j + 12);
			$pdf->Write(0,  "P/N# " . substr($bundle_use_for, 0, 22));
			$pdf->SetXY($i + 40, $j + 12);
			$pdf->Write(0, "Cut/N# " . $order_cut_no);

			$pdf->SetXY($i, $j + 15);
			$pdf->Write(0,  "Stkr# " . $val["number_start"] . "-" . $val["number_end"]);
			$pdf->SetXY($i + 20, $j + 15);
			$pdf->Write(0, "Size# " . $size_arr[$val["size_id"]] . "(" . $pattern_no . ")");
			$pdf->SetXY($i + 45, $j + 15);
			$pdf->Write(0, "Qty#" . $col_size_data_arr[$barcode]);

			$pdf->SetXY($i, $j + 18);
			$pdf->Write(0,  $val['barcode_no']);
			$pdf->SetXY($i + 30, $j + 18);
			$pdf->Write(0, "Cut/N# " . $cutting_no);

			$pdf->Code128($i + 1, $j + 21, $val["barcode_no"], 60, 8);

			$k++;
			$i = 2;
			$j = 4;
			$br++;
		}
	}

	foreach (glob("" . "*.pdf") as $filename) {
		@unlink($filename);
	}
	$name = 'bundle_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output("" . $name, 'F');
	echo $name;
	exit();
}

if ($action == "print_barcode_one_128ddddddddddd") {
	require('../../../ext_resource/pdf/code128.php');
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');//order_cut_no

	$data = explode("***", $data);
	$detls_id = $data[3];
	$batch_id = return_field_value("batch_id", "ppl_cut_lay_dtls", "id=$detls_id");
	$buyer_library = return_library_array("select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
	$table_no_library = return_library_array("select id,table_no  from  lib_cutting_table", "id", "table_no");
	$country_arr = return_library_array("select id, short_name from lib_country", 'id', 'short_name');
	$lib_season_arr = return_library_array("select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0", 'id', 'season_name');
	$order_cut_no = return_field_value("order_cut_no", "ppl_cut_lay_dtls", "id=$detls_id and status_active=1 and is_deleted=0", "order_cut_no");
	//$pdf=new PDF_Code39('P','mm','a10');
	//$pdf->AddPage();


	$pdf = new PDF_Code128('P', 'mm', 'a9');
	$pdf->AddPage();
	$pdf->SetFont('Arial', '', 8);

	$bacth_array = array();
	$batchData = sql_select("select id, batch_no, extention_no from pro_batch_create_mst where entry_form in(0,7,37,66,68)");
	foreach ($batchData as $row) {
		$ext = '';
		if ($row[csf('extention_no')] > 0) {
			$ext = '-' . $row[csf('extention_no')];
		}
		$bacth_array[$row[csf('id')]] = $row[csf('batch_no')] . $ext;
	}


	$color_sizeID_arr = sql_select("select a.id, a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no
	from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0])
	group by a.id, a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no order by b.bundle_sequence,a.id");
	foreach ($color_sizeID_arr as $row) {
		$test_data[$row[csf("roll_id")]] = $row[csf("roll_id")];
	}
	$i = 2;
	$j = 0;
	$k = 0;
	$bundle_array = array();
	$br = 0;
	$n = 0;
	$sql_name = sql_select("select b.buyer_name, b.client_id, b.style_ref_no, b.product_dept, b.season_matrix, a.po_number, a.id as po_id from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.id in(" . $data[6] . ")");
	$po_data_arr = array();
	foreach ($sql_name as $value) {
		$po_data_arr[$value[csf('po_id')]]["style_ref_no"] = $value[csf('style_ref_no')];
		$po_data_arr[$value[csf('po_id')]]["buyer_name"] = $value[csf('buyer_name')];
		$po_data_arr[$value[csf('po_id')]]["client_id"] = $value[csf('client_id')];
		$po_data_arr[$value[csf('po_id')]]["po_number"] = $value[csf('po_number')];
		$matrix_season = $value[csf('season_matrix')];
	}
	unset($sql_name);

	$roll_sql = sql_select("select roll_id, batch_no from pro_roll_details where entry_form=715 and status_active=1");
	$roll_data_arr = array();
	foreach ($roll_sql as $row) {
		$roll_data_arr[$row[csf("roll_id")]] = $row[csf("batch_no")];
	}

	unset($roll_sql);
	$sql_cut_name = sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	foreach ($sql_cut_name as $cut_value) {
		$table_name = $cut_value[csf('table_no')];
		$cut_date = change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix = $cut_value[csf('cut_num_prefix_no')];
		$company_id = $cut_value[csf('company_id')];
		$comp_name = return_field_value("company_short_name", "lib_company", "id=$company_id");
		$new_cut_no = $comp_name . "-" . $cut_prifix;
		$bundle_title = "";
	}
	if ($data[7] == "") $data[7] = 0;
	$seq = explode(",", $data[7]);
	$seq_first = $seq[0];
	$i = 2;
	$j = 2;
	$k = 0;
	$br = 0;
	$n = 0;
	$sql_bundle_copy = sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
	if (count($sql_bundle_copy) != 0) {
		foreach ($sql_bundle_copy as $inf) {
			if ($br == 1) {
				$pdf->AddPage();
				$br = 0;
				$i = 2;
				$j = 2;
				$k = 0;
			}
			foreach ($color_sizeID_arr as $val) {
				if ($br == 1) {
					$pdf->AddPage();
					$br = 0;
					$i = 2;
					$j = 2;
					$k = 0;
				}
				if ($seq_first == $inf[csf("id")]) $symb = "@@";
				else $symb = "";
				//BNDL
				$style_name = $po_data_arr[$val[csf('order_id')]]["style_ref_no"];
				$buyer_name = $po_data_arr[$val[csf('order_id')]]["buyer_name"];
				$client_id = $po_data_arr[$val[csf('order_id')]]["client_id"];
				$po_number = $po_data_arr[$val[csf('order_id')]]["po_number"];
				$batch_no = $roll_data_arr[$val[csf('roll_id')]];
				$bundle_no_prifix = $val[csf("bundle_num_prefix_no")];
				$buyer_name_str = "";
				if ($client_id != 0) $buyer_name_str = $buyer_library[$buyer_name] . '-' . $buyer_library[$client_id];
				else $buyer_name_str = $buyer_library[$buyer_name];
				//$dbl_no="17910000000012";
				if ($val[csf('is_excess')] == 1) $country = "EXCESS";
				else $country = $country_arr[$val[csf("country_id")]];

				$pdf->SetXY($i, $j);
				$pdf->Write(0, $symb . " " . $buyer_name_str . "  COUN# " . $country . "  QTY# " . $val[csf("size_qty")]);

				$pdf->SetXY($i, $j + 3.2);
				$pdf->Write(0, "STKR# " . $val[csf("number_start")] . "-" . $val[csf("number_end")] . "  STY# " . $style_name);
				$pdf->SetXY($i, $j + 6.4);
				$pdf->Write(0, $val[csf("bundle_no")] . "  PO# " . $po_number);
				$pdf->SetXY($i, $j + 9.6);
				$pdf->Write(0, "COLOR# " . $color_library[$data[5]]);

				$pdf->SetXY($i, $j + 12.8);
				$pdf->Write(0, $inf[csf("bundle_use_for")] . "  B# " . $batch_no . "  S# " . $lib_season_arr[$matrix_season]);

				$pdf->SetXY($i, $j + 16);
				$pdf->Write(0, "CUT & ROLL# " . $order_cut_no . " & " . $val[csf("roll_no")] . "  SIZE# " . $size_arr[$val[csf("size_id")]] . "(" . $val[csf("pattern_no")] . ")");

				//$pdf->SetXY($i, $j+14.2);
				//$pdf->Write(0, $val[csf("barcode_no")]);

				//$pdf->Code128($i,$j+25,$val[csf("bundle_no")],40,8);

				$k++;
				//$i=2; $j=$j+25;
				$br++;
			}
		}
	} else {
		foreach ($color_sizeID_arr as $val) {
			if ($br == 1) {
				$pdf->AddPage();
				$br = 0;
				$i = 2;
				$j = 0;
				$k = 0;
			}
			if ($seq_first == $inf[csf("id")]) $symb = "@@";
			else $symb = "";
			$style_name = $po_data_arr[$val[csf('order_id')]]["style_ref_no"];
			$buyer_name = $po_data_arr[$val[csf('order_id')]]["buyer_name"];
			$po_number = $po_data_arr[$val[csf('order_id')]]["po_number"];
			$batch_no = $roll_data_arr[$val[csf('roll_id')]];
			$bundle_no_prifix = $val[csf("bundle_num_prefix_no")];

			if ($val[csf('is_excess')] == 1) $country = "EXCESS";
			else $country = $country_arr[$val[csf("country_id")]];

			$pdf->Code40($i, $j - 2, $symb . " " . $buyer_library[$buyer_name] . "  COUN# " . $country . "  QTY# " . $val[csf("size_qty")], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
			$pdf->Code40($i, $j + 1.2, "STKR# " . $val[csf("number_start")] . "-" . $val[csf("number_end")] . "  STY# " . $style_name, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
			$pdf->Code40($i, $j + 4.4, $val[csf("bundle_no")] . "  PO# " . $po_number, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true, 7);
			$pdf->Code40($i, $j + 7.6, "COLOR# " . $color_library[$data[5]], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
			$pdf->Code40($i, $j + 10.8, $inf[csf("bundle_use_for")] . "  B# " . $batch_no . "  S# " . $lib_season_arr[$matrix_season], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
			$pdf->Code40($i, $j + 14, "CUT & ROLL# " . $order_cut_no . " & " . $val[csf("roll_no")] . "  SIZE# " . $size_arr[$val[csf("size_id")]] . "(" . $val[csf("pattern_no")] . ")", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
			$pdf->Code40($i + 1.3, $j + 17.2, $val[csf("barcode_no")], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
			//$pdf->Code40($i, $j+22, $val[csf("bundle_no")],$w =0.2, $h = 8);
			$pdf->Code39($i + 1.3, $j + 23, $val[csf("barcode_no")]);
			$k++;
			$i = 2;
			$j = $j + 23;

			$br++;
		}
	}

	foreach (glob("" . "*.pdf") as $filename) {
		@unlink($filename);
	}
	$name = 'bundle_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output("" . $name, 'F');
	echo $name;
	exit();
}

//bundle_bar_code ****************************************************************************************
if ($action == "print_qrcode") {

	$data = explode("***", $data);
	$detls_id = $data[3];
	$batch_id = return_field_value("batch_id", "ppl_cut_lay_dtls", "id=$detls_id");
	$buyer_library = return_library_array("select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
	$table_no_library = return_library_array("select id,table_no  from  lib_cutting_table", "id", "table_no");
	$country_arr = return_library_array("select id, short_name from lib_country", 'id', 'short_name');
	$lib_season_arr = return_library_array("select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0", 'id', 'season_name');
	$order_cut_no = return_field_value("order_cut_no", "ppl_cut_lay_dtls", "id=$detls_id and status_active=1 and is_deleted=0", "order_cut_no");
	//echo $order_cut_no;
	//$pdf=new PDF_Code39('P','mm','a10');
	//$pdf=PDF_Code128();
	//$pdf=new PDF_Code39(); 
	//$pdf->SetFont('Arial', '', 1000);
	//$pdf->AddPage();


	$bacth_array = array();
	$batchData = sql_select("select id, batch_no, extention_no from pro_batch_create_mst where entry_form in(0,7,37,66,68)");
	foreach ($batchData as $row) {
		$ext = '';
		if ($row[csf('extention_no')] > 0) {
			$ext = '-' . $row[csf('extention_no')];
		}
		$bacth_array[$row[csf('id')]] = $row[csf('batch_no')] . $ext;
	}


	$color_sizeID_arr = sql_select("select c.gmt_item_id,c.color_id,a.id, a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no
	from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b, ppl_cut_lay_dtls c where c.id=a.dtls_id and a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0])
	group by a.id, a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no,c.gmt_item_id,c.color_id order by b.bundle_sequence,a.id");
	/*echo "select c.gmt_item_id,c.color_id,a.id, a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no
	from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b, ppl_cut_lay_dtls c where c.id=a.dtls_id and a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0])
	group by a.id, a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no,c.gmt_item_id,c.color_id order by b.bundle_sequence,a.id";*/
	foreach ($color_sizeID_arr as $row) {
		$test_data[$row[csf("roll_id")]] = $row[csf("roll_id")];
	}

	$sql_name = sql_select("select b.buyer_name, b.client_id, b.style_ref_no, b.product_dept, b.season_matrix, a.po_number, a.id as po_id from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.id in(" . $data[6] . ")");
	$po_data_arr = array();
	foreach ($sql_name as $value) {
		$po_data_arr[$value[csf('po_id')]]["style_ref_no"] = $value[csf('style_ref_no')];
		$po_data_arr[$value[csf('po_id')]]["buyer_name"] = $value[csf('buyer_name')];
		$po_data_arr[$value[csf('po_id')]]["client_id"] = $value[csf('client_id')];
		$po_data_arr[$value[csf('po_id')]]["po_number"] = $value[csf('po_number')];
		$matrix_season = $value[csf('season_matrix')];
	}
	unset($sql_name);


	$sql_article = sql_select("select article_number, po_break_down_id, item_number_id, color_number_id, size_number_id, country_id from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 and po_break_down_id in(" . $data[6] . ")");
	$po_article_data_arr = array();
	foreach ($sql_article as $value) {
		$po_article_data_arr[$value[csf('po_break_down_id')]][$value[csf('item_number_id')]][$value[csf('country_id')]][$value[csf('color_number_id')]][$value[csf('size_number_id')]] = $value[csf('article_number')];
	}
	unset($sql_article);

	$roll_sql = sql_select("select roll_id, batch_no, shade from pro_roll_details where entry_form=715 and status_active=1");
	$roll_data_arr = array();
	foreach ($roll_sql as $row) {
		$roll_data_arr[$row[csf("roll_id")]]['batchno'] = $row[csf("batch_no")];
		$roll_data_arr[$row[csf("roll_id")]]['shade'] = $row[csf("shade")];
	}

	unset($roll_sql);
	$sql_cut_name = sql_select("select entry_date,cutting_no,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	foreach ($sql_cut_name as $cut_value) {
		$ful_cut_no = $cut_value[csf('cutting_no')];
		$table_name = $cut_value[csf('table_no')];
		$cut_date = change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix = $cut_value[csf('cut_num_prefix_no')];
		$company_id = $cut_value[csf('company_id')];
		$comp_name = return_field_value("company_short_name", "lib_company", "id=$company_id");
		$new_cut_no = $comp_name . "-" . $cut_prifix;
		$bundle_title = "";
	}

	$PNG_TEMP_DIR = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'qrcode_image' . DIRECTORY_SEPARATOR . $ful_cut_no . DIRECTORY_SEPARATOR;
	$PNG_WEB_DIR = 'qrcode_image/' . $ful_cut_no . '/';

	foreach (glob($PNG_WEB_DIR . "*.png") as $filename) {
		@unlink($filename);
	}

	if (!file_exists($PNG_TEMP_DIR)) mkdir($PNG_TEMP_DIR);

	$filename = $PNG_TEMP_DIR . 'test.png';
	$errorCorrectionLevel = 'L';
	$matrixPointSize = 4;

	include "../../../ext_resource/phpqrcode/qrlib.php";
	require_once("../../../ext_resource/mpdf60/mpdf.php");

	$mpdf = new mPDF(
		'',    // mode - default ''
		array(50, 80),    // format - A4, for example, default ''
		7,     // font size - default 0
		'',    // default font family
		8,    // margin_left
		0,    // margin right
		3,     // margin top
		0,    // margin bottom
		0,     // margin header
		0,     // margin footer
		'L'
	);

	if ($data[7] == "") $data[7] = 0;
	$seq = explode(",", $data[7]);
	$i = 2;
	$j = 3;
	$k = 0;
	$bundle_array = array();
	$br = 0;
	$n = 0;
	$seq_first = $seq[0];
	$sql_bundle_copy = sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
	//echo "select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])";
	//print_r($sql_bundle_copy);die;
	$i = 1;

	if (count($sql_bundle_copy) != 0) {
		foreach ($sql_bundle_copy as $inf) {
			foreach ($color_sizeID_arr as $val) {
				$article_no = $po_article_data_arr[$val[csf('order_id')]][$val[csf('gmt_item_id')]][$val[csf('country_id')]][$val[csf('color_id')]][$val[csf('size_id')]];
				if ($seq_first == $inf[csf("id")]) $symb = "@@";
				else $symb = "";
				//BNDL
				$style_name = $po_data_arr[$val[csf('order_id')]]["style_ref_no"];
				$buyer_name = $po_data_arr[$val[csf('order_id')]]["buyer_name"];
				$client_id = $po_data_arr[$val[csf('order_id')]]["client_id"];
				$po_number = $po_data_arr[$val[csf('order_id')]]["po_number"];
				$batch_no = $roll_data_arr[$val[csf('roll_id')]]['batchno'];
				$shade = $roll_data_arr[$val[csf("roll_id")]]['shade'];
				$bundle_no_prifix = $val[csf("bundle_num_prefix_no")];
				$buyer_name_str = "";
				$buyer_name_str = $buyer_library[$buyer_name];
				// if($client_id!=0) $buyer_name_str=$buyer_library[$buyer_name].'-'.$buyer_library[$client_id]; else $buyer_name_str=$buyer_library[$buyer_name];
				if ($val[csf('is_excess')] == 1) $country = "EXCESS";
				else $country = $country_arr[$val[csf("country_id")]];

				$html = '';
				//if($i!=1) $mpdf->AddPage();
				$filename = $PNG_TEMP_DIR . 'test' . md5($val[csf("barcode_no")]) . '.png';
				QRcode::png($val[csf("barcode_no")], $filename, $errorCorrectionLevel, $matrixPointSize, 2);
				//$po_number=$po_number_arr[$val[csf('order_id')]];
				$bundle_array[$i] = $val[csf("barcode_no")];
				$html .= '<table  width="310" style="font-size:12px; " border="0" cellpadding="0" cellspacing="0">';

				$html .= '<tr><td><strong>' . $symb . '' . $buyer_name_str . ',' . $country . ', QTY# ' . $val[csf("size_qty")] . '</strong></td></tr>';
				$html .= '<tr><td><strong> STK# ' . $val[csf("number_start")] . '-' . $val[csf("number_end")] . '' . $inf[csf("bundle_use_for")] . '</strong></td></tr>';
				$html .= '<tr><td><strong>' . $val[csf("bundle_no")] . ' B# ' . $batch_no . '</strong></td></tr>';
				$html .= '<tr><td><strong> STY# ' . substr($style_name, 0, 30) . ' S# ' . $shade . '</strong></td></tr>';
				$html .= '<tr><td><strong> Ar.# ' . $article_no . ' ,PO# ' . substr($po_number, 0, 25) . '</strong></td></tr>';
				$html .= '<tr><td><strong>Color# ' . substr($color_library[$data[5]], 0, 30) . '</strong></td></tr>';
				$html .= '<tr><td ><strong> CUT#  ' . $order_cut_no . ' ROLL# ' . $val[csf("roll_no")] . ' SIZE# ' . $size_arr[$val[csf("size_id")]] . '(' . $val[csf("pattern_no")] . ')</strong></td></tr>';
				$html .= '<tr><td ><div id="div_' . $i . '">
					<img src="' . $PNG_WEB_DIR . basename($filename) . '" height="80" width="90">
				</div><div> <strong>' . $val[csf("barcode_no")] . '</strong></div></td></tr>';
				$html .= '</table>';
				$mpdf->WriteHTML($html);
				$i++;
			}
		}
	}

	foreach (glob("*.pdf") as $filename) {
		@unlink($filename);
	}
	$name = 'lotRatioEntry_' . date('j-M-Y_h-iA') . '_' . $user_id . '.pdf';
	$mpdf->Output($name, 'F');
	echo "1###$name";
	fn_delete_dir_with_files("qrcode_image/".$ful_cut_no);
	exit();
}

if ($action == "print_barcode_one_128") {
	//define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	//require('../../../ext_resource/pdf/code128.php');

	require('../../../ext_resource/pdf/code128.php');
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');//order_cut_no

	$data = explode("***", $data);
	$detls_id = $data[3];
	$batch_id = return_field_value("batch_id", "ppl_cut_lay_dtls", "id=$detls_id");
	$buyer_library = return_library_array("select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
	$table_no_library = return_library_array("select id,table_no  from  lib_cutting_table", "id", "table_no");
	$country_arr = return_library_array("select id, short_name from lib_country", 'id', 'short_name');
	$lib_season_arr = return_library_array("select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0", 'id', 'season_name');
	$order_cut_no = return_field_value("order_cut_no", "ppl_cut_lay_dtls", "id=$detls_id and status_active=1 and is_deleted=0", "order_cut_no");
	//echo $order_cut_no;
	//$pdf=new PDF_Code39('P','mm','a10');
	//$pdf=PDF_Code128();
	//$pdf=new PDF_Code39(); 
	//$pdf->SetFont('Arial', '', 1000);
	//$pdf->AddPage();


	$bacth_array = array();
	$batchData = sql_select("select id, batch_no, extention_no from pro_batch_create_mst where entry_form in(0,7,37,66,68)");
	foreach ($batchData as $row) {
		$ext = '';
		if ($row[csf('extention_no')] > 0) {
			$ext = '-' . $row[csf('extention_no')];
		}
		$bacth_array[$row[csf('id')]] = $row[csf('batch_no')] . $ext;
	}

	//echo "select c.gmt_item_id,c.color_id,a.id, a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no
	//from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b,ppl_cut_lay_dtls c  where c.id=a.dtls_id and a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0])
	//	group by a.id, a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no,c.gmt_item_id,c.color_id order by b.bundle_sequence,a.id";die;
	$color_sizeID_arr = sql_select("select c.gmt_item_id,c.color_id,a.id, a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no
	from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b,ppl_cut_lay_dtls c  where c.id=a.dtls_id and a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0])
	group by a.id, a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no,c.gmt_item_id,c.color_id order by b.bundle_sequence,a.id");
	foreach ($color_sizeID_arr as $row) {
		$test_data[$row[csf("roll_id")]] = $row[csf("roll_id")];
	}

	$sql_name = sql_select("select b.buyer_name, b.client_id, b.style_ref_no, b.product_dept, b.season_matrix, a.po_number, a.id as po_id from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.id in(" . $data[6] . ")");
	$po_data_arr = array();
	foreach ($sql_name as $value) {
		$po_data_arr[$value[csf('po_id')]]["style_ref_no"] = $value[csf('style_ref_no')];
		$po_data_arr[$value[csf('po_id')]]["buyer_name"] = $value[csf('buyer_name')];
		$po_data_arr[$value[csf('po_id')]]["client_id"] = $value[csf('client_id')];
		$po_data_arr[$value[csf('po_id')]]["po_number"] = $value[csf('po_number')];
		$matrix_season = $value[csf('season_matrix')];
	}
	unset($sql_name);


	$sql_article = sql_select("select article_number,po_break_down_id,item_number_id,color_number_id,size_number_id,country_id       from wo_po_color_size_breakdown where status_active=1 and is_deleted=0   and po_break_down_id in(" . $data[6] . ")");
	$po_article_data_arr = array();
	foreach ($sql_article as $value) {
		$po_article_data_arr[$value[csf('po_break_down_id')]][$value[csf('item_number_id')]][$value[csf('country_id')]][$value[csf('color_number_id')]][$value[csf('size_number_id')]] = $value[csf('article_number')];
	}
	unset($sql_article);

	$roll_sql = sql_select("select roll_id, batch_no from pro_roll_details where entry_form=99 and status_active=1");
	$roll_data_arr = array();
	foreach ($roll_sql as $row) {
		$roll_data_arr[$row[csf("roll_id")]] = $row[csf("batch_no")];
	}

	unset($roll_sql);
	$sql_cut_name = sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	foreach ($sql_cut_name as $cut_value) {
		$table_name = $cut_value[csf('table_no')];
		$cut_date = change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix = $cut_value[csf('cut_num_prefix_no')];
		$company_id = $cut_value[csf('company_id')];
		$comp_name = return_field_value("company_short_name", "lib_company", "id=$company_id");
		$new_cut_no = $comp_name . "-" . $cut_prifix;
		$bundle_title = "";
	}


	$pdf = new PDF_Code128('P', 'mm', 'a128');
	$pdf->AddPage();
	$pdf->SetFont('Arial', 'B', 8);
	//$pdf->SetRightMargin(0);

	if ($data[7] == "") $data[7] = 0;
	$seq = explode(",", $data[7]);
	$i = 2;
	$j = 3;
	$k = 0;
	$bundle_array = array();
	$br = 0;
	$n = 0;
	$seq_first = $seq[0];
	$sql_bundle_copy = sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
	if (count($sql_bundle_copy) != 0) {
		foreach ($sql_bundle_copy as $inf) {
			if ($br == 1) {
				$pdf->AddPage();
				$br = 0;
				$i = 2;
				$j = 3;
				$k = 0;
			}
			foreach ($color_sizeID_arr as $val) {
				if ($br == 1) {
					$pdf->AddPage();
					$br = 0;
					$i = 2;
					$j = 3;
					$k = 0;
				}

				$article_no = $po_article_data_arr[$val[csf('order_id')]][$val[csf('gmt_item_id')]][$val[csf('country_id')]][$val[csf('color_id')]][$val[csf('size_id')]];
				if ($seq_first == $inf[csf("id")]) $symb = "@@";
				else $symb = "";
				//BNDL
				$style_name = $po_data_arr[$val[csf('order_id')]]["style_ref_no"];
				$buyer_name = $po_data_arr[$val[csf('order_id')]]["buyer_name"];
				$client_id = $po_data_arr[$val[csf('order_id')]]["client_id"];
				$po_number = $po_data_arr[$val[csf('order_id')]]["po_number"];
				$batch_no = $roll_data_arr[$val[csf('roll_id')]];
				$bundle_no_prifix = $val[csf("bundle_num_prefix_no")];
				$buyer_name_str = "";
				if ($client_id != 0) $buyer_name_str = $buyer_library[$buyer_name] . '-' . $buyer_library[$client_id];
				else $buyer_name_str = $buyer_library[$buyer_name];
				if ($val[csf('is_excess')] == 1) $country = "EXCESS";
				else $country = $country_arr[$val[csf("country_id")]];



				$pdf->SetXY($i, $j);
				$pdf->Write(0, $symb . " " . $buyer_name_str . ", " . $country . ", QTY# " . $val[csf("size_qty")]);

				$pdf->SetXY($i, $j + 3);
				$pdf->Write(0, " STK# " . $val[csf("number_start")] . "-" . $val[csf("number_end")] . " " . $inf[csf("bundle_use_for")]); //24

				$pdf->SetXY($i, $j + 6);
				$pdf->Write(0, " " . $val[csf("bundle_no")] . " B# " . $batch_no);


				$pdf->SetXY($i, $j + 9);
				$pdf->Write(0, " STY# " . substr($style_name, 0, 40)); //24 $style_name


				$pdf->SetXY($i, $j + 12);
				$pdf->Write(0, " Ar.# " . $article_no . " ,PO# " . substr($po_number, 0, 35));
				//$pdf->Write(0, " ".$val[csf("bundle_no")] ." B# ".$batch_no);

				$pdf->SetXY($i, $j + 15);
				$pdf->Write(0, " Color# " . substr($color_library[$data[5]], 0, 30)); //$color_library[$data[5]]

				$pdf->SetXY($i, $j + 18);
				$pdf->Write(0, " CUT#  " . $order_cut_no . " ROLL# " . $val[csf("roll_no")] . " SIZE# " . $size_arr[$val[csf("size_id")]] . "(" . $val[csf("pattern_no")] . ")");

				$pdf->Code128($i + 1, $j + 21, $val[csf("barcode_no")], 50, 8);

				$k++;
				$br++;
			}
		}
	} else {
		foreach ($color_sizeID_arr as $val) {
			if ($br == 1) {
				$pdf->AddPage();
				$br = 0;
				$i = 2;
				$j = 3;
				$k = 0;
			}

			$article_no = $po_article_data_arr[$val[csf('order_id')]][$val[csf('gmt_item_id')]][$val[csf('country_id')]][$val[csf('color_id')]][$val[csf('size_id')]];
			if ($seq_first == $inf[csf("id")]) $symb = "@@";
			else $symb = "";
			//BNDL
			$style_name = $po_data_arr[$val[csf('order_id')]]["style_ref_no"];
			$buyer_name = $po_data_arr[$val[csf('order_id')]]["buyer_name"];
			$client_id = $po_data_arr[$val[csf('order_id')]]["client_id"];
			$po_number = $po_data_arr[$val[csf('order_id')]]["po_number"];
			$batch_no = $roll_data_arr[$val[csf('roll_id')]];
			$bundle_no_prifix = $val[csf("bundle_num_prefix_no")];
			$buyer_name_str = "";
			if ($client_id != 0) $buyer_name_str = $buyer_library[$buyer_name] . '-' . $buyer_library[$client_id];
			else $buyer_name_str = $buyer_library[$buyer_name];
			//$dbl_no="17910000000012";
			if ($val[csf('is_excess')] == 1) $country = "EXCESS";
			else $country = $country_arr[$val[csf("country_id")]];

			$pdf->SetXY($i, $j);
			$pdf->Write(0, $symb . " " . $buyer_name_str . "  COUN# " . $country . "  QTY# " . $val[csf("size_qty")]);

			$pdf->SetXY($i, $j + 3);
			//$pdf->Write(0, "STKR# ".$val[csf("number_start")]."-".$val[csf("number_end")]."  STY# ". $style_name);
			$pdf->Write(0, " STK# " . $val[csf("number_start")] . "-" . $val[csf("number_end")] . " " . $inf[csf("bundle_use_for")]); //24

			$pdf->SetXY($i, $j + 6);
			$pdf->Write(0, " " . $val[csf("bundle_no")] . " B# " . $batch_no);

			$pdf->SetXY($i, $j + 9);
			$pdf->Write(0, " STY# " . substr($style_name, 0, 40)); //24 $style_name

			$pdf->SetXY($i, $j + 12);
			$pdf->Write(0, " Ar.# " . $article_no . " ,PO# " . substr($po_number, 0, 35));

			$pdf->SetXY($i, $j + 15);
			$pdf->Write(0, " Color# " . substr($color_library[$data[5]], 0, 40)); //$color_library[$data[5]]

			$pdf->SetXY($i, $j + 18);
			$pdf->Write(0, " CUT#  " . $order_cut_no . " ROLL# " . $val[csf("roll_no")] . " SIZE# " . $size_arr[$val[csf("size_id")]] . "(" . $val[csf("pattern_no")] . ")");

			$pdf->Code128($i + 1, $j + 22, $val[csf("barcode_no")], 50, 8);
			$k++;
			$br++;
		}
	}

	foreach (glob("" . "*.pdf") as $filename) {
		@unlink($filename);
	}
	$name = 'bundle_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output("" . $name, 'F');
	echo $name;
	exit();
}
if ($action == "print_barcode_one_129") {
	//define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	//require('../../../ext_resource/pdf/code128.php');

	require('../../../ext_resource/pdf/code128.php');
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');//order_cut_no

	$data = explode("***", $data);

	// ================================== BUNDLE USE FOR ENTRY ============================
	if ($data[7] != "") // when body part exist
	{
		$con = connect();
		$job_id = return_field_value("id", "wo_po_details_master", " job_no='$data[1]' and status_active=1 and is_deleted=0 ", "id");

		// $delStatus = sql_delete("ppl_cut_lay_bundle_use_for","status_active*is_deleted","0*1",'job_id',$job_id,1);
		$delStatus = execute_query("delete from ppl_cut_lay_bundle_use_for where job_id=$job_id", 0);

		$field_array = "id,job_id,bodypart_id,inserted_by,insert_date";
		$bodypart_id = array_unique(explode(",", $data[7]));
		$data_array = "";
		$j = 0;
		$id = return_next_id("id", "ppl_cut_lay_bundle_use_for", 1);
		foreach ($bodypart_id as $val) {
			if ($j == 0) $data_array = "(" . $id . "," . $job_id . "," . $val . "," . $user_id . ",'" . $pc_date_time . "')";
			else $data_array .= ",(" . $id . "," . $job_id . "," . $val . "," . $user_id . ",'" . $pc_date_time . "')";
			$id = $id + 1;
			$j++;
		}

		$insertStatus = sql_insert("ppl_cut_lay_bundle_use_for", $field_array, $data_array, 0);

		// echo "10**insert into ppl_cut_lay_bundle_use_for (".$field_array.") values ".$data_array;die;

		if ($db_type == 0) {
			if ($insertStatus) {
				mysql_query("COMMIT");
				// echo "1**".str_replace("'","",$hidden_po_break_down_id);
			} else {
				mysql_query("ROLLBACK");
				// echo "10**".str_replace("'","",$hidden_po_break_down_id);
			}
		} else {
			if ($insertStatus) {
				oci_commit($con);
				// echo "1**".str_replace("'","",$hidden_po_break_down_id);
			} else {
				oci_rollback($con);
				// echo "10**".str_replace("'","",$hidden_po_break_down_id);
			}
		}
	}
	// =========================== end =================================

	$detls_id = $data[3];
	$batch_id = return_field_value("batch_id", "ppl_cut_lay_dtls", "id=$detls_id");
	$buyer_library = return_library_array("select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
	$table_no_library = return_library_array("select id,table_no  from  lib_cutting_table", "id", "table_no");
	$country_arr = return_library_array("select id, short_name from lib_country", 'id', 'short_name');
	$lib_season_arr = return_library_array("select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0", 'id', 'season_name');
	$order_cut_no = return_field_value("order_cut_no", "ppl_cut_lay_dtls", "id=$detls_id and status_active=1 and is_deleted=0", "order_cut_no");
	//echo $order_cut_no;
	//$pdf=new PDF_Code39('P','mm','a10');
	//$pdf=PDF_Code128();
	//$pdf=new PDF_Code39(); 
	//$pdf->SetFont('Arial', '', 1000);
	//$pdf->AddPage();


	$bacth_array = array();
	$batchData = sql_select("select id, batch_no, extention_no from pro_batch_create_mst where entry_form in(0,7,37,66,68)");
	foreach ($batchData as $row) {
		$ext = '';
		if ($row[csf('extention_no')] > 0) {
			$ext = '-' . $row[csf('extention_no')];
		}
		$bacth_array[$row[csf('id')]] = $row[csf('batch_no')] . $ext;
	}

	//echo "select c.gmt_item_id,c.color_id,a.id, a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no
	//from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b,ppl_cut_lay_dtls c  where c.id=a.dtls_id and a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0])
	//	group by a.id, a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no,c.gmt_item_id,c.color_id order by b.bundle_sequence,a.id";die;
	$color_sizeID_arr = sql_select("select c.gmt_item_id,c.color_id,a.id, a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no
	from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b,ppl_cut_lay_dtls c  where c.id=a.dtls_id and a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0])
	group by a.id, a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no,c.gmt_item_id,c.color_id order by b.bundle_sequence,a.id");
	foreach ($color_sizeID_arr as $row) {
		$test_data[$row[csf("roll_id")]] = $row[csf("roll_id")];
	}

	$sql_name = sql_select("select b.buyer_name, b.client_id, b.style_ref_no, b.product_dept, b.season_matrix, a.po_number, a.id as po_id from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.id in(" . $data[6] . ")");
	$po_data_arr = array();
	foreach ($sql_name as $value) {
		$po_data_arr[$value[csf('po_id')]]["style_ref_no"] = $value[csf('style_ref_no')];
		$po_data_arr[$value[csf('po_id')]]["buyer_name"] = $value[csf('buyer_name')];
		$po_data_arr[$value[csf('po_id')]]["client_id"] = $value[csf('client_id')];
		$po_data_arr[$value[csf('po_id')]]["po_number"] = $value[csf('po_number')];
		$matrix_season = $value[csf('season_matrix')];
	}
	unset($sql_name);


	$sql_article = sql_select("select article_number,po_break_down_id,item_number_id,color_number_id,size_number_id,country_id       from wo_po_color_size_breakdown where status_active=1 and is_deleted=0   and po_break_down_id in(" . $data[6] . ")");
	$po_article_data_arr = array();
	foreach ($sql_article as $value) {
		$po_article_data_arr[$value[csf('po_break_down_id')]][$value[csf('item_number_id')]][$value[csf('country_id')]][$value[csf('color_number_id')]][$value[csf('size_number_id')]] = $value[csf('article_number')];
	}
	unset($sql_article);

	$roll_sql = sql_select("select roll_id, batch_no from pro_roll_details where entry_form=99 and status_active=1");
	$roll_data_arr = array();
	foreach ($roll_sql as $row) {
		$roll_data_arr[$row[csf("roll_id")]] = $row[csf("batch_no")];
	}

	unset($roll_sql);
	$sql_cut_name = sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	foreach ($sql_cut_name as $cut_value) {
		$table_name = $cut_value[csf('table_no')];
		$cut_date = change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix = $cut_value[csf('cut_num_prefix_no')];
		$company_id = $cut_value[csf('company_id')];
		$comp_name = return_field_value("company_short_name", "lib_company", "id=$company_id");
		$new_cut_no = $comp_name . "-" . $cut_prifix;
		$bundle_title = "";
	}


	$pdf = new PDF_Code128('P', 'mm', 'a128');
	$pdf->AddPage();
	$pdf->SetFont('Arial', 'B', 8);
	//$pdf->SetRightMargin(0);

	if ($data[7] == "") $data[7] = 0;
	$seq = explode(",", $data[7]);
	$i = 2;
	$j = 3;
	$k = 0;
	$bundle_array = array();
	$br = 0;
	$n = 0;
	$seq_first = $seq[0];
	$sql_bundle_copy = sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
	if (count($sql_bundle_copy) != 0) {
		foreach ($sql_bundle_copy as $inf) {
			if ($br == 1) {
				$pdf->AddPage();
				$br = 0;
				$i = 2;
				$j = 3;
				$k = 0;
			}
			foreach ($color_sizeID_arr as $val) {
				if ($br == 1) {
					$pdf->AddPage();
					$br = 0;
					$i = 2;
					$j = 3;
					$k = 0;
				}

				$article_no = $po_article_data_arr[$val[csf('order_id')]][$val[csf('gmt_item_id')]][$val[csf('country_id')]][$val[csf('color_id')]][$val[csf('size_id')]];
				if ($seq_first == $inf[csf("id")]) $symb = "@@";
				else $symb = "";
				//BNDL
				$style_name = $po_data_arr[$val[csf('order_id')]]["style_ref_no"];
				$buyer_name = $po_data_arr[$val[csf('order_id')]]["buyer_name"];
				$client_id = $po_data_arr[$val[csf('order_id')]]["client_id"];
				$po_number = $po_data_arr[$val[csf('order_id')]]["po_number"];
				$batch_no = $roll_data_arr[$val[csf('roll_id')]];
				$bundle_no_prifix = $val[csf("bundle_num_prefix_no")];
				$bundle_no = $val[csf("bundle_no")];
				$buyer_name_str = "";
				if ($client_id != 0) $buyer_name_str = $buyer_library[$buyer_name] . '-' . $buyer_library[$client_id];
				else $buyer_name_str = $buyer_library[$buyer_name];
				if ($val[csf('is_excess')] == 1) $country = "EXCESS";
				else $country = $country_arr[$val[csf("country_id")]];



				$pdf->SetXY($i, $j);
				$pdf->Write(0, " Buy# " . " " . $buyer_name_str);

				$pdf->SetXY($i, $j + 3);
				$pdf->Write(0, " STY# " . substr($style_name, 0, 40));

				$pdf->SetXY($i, $j + 6);
				$pdf->Write(0, " PO# " . substr($po_number, 0, 35) . " SIZE# " . $size_arr[$val[csf("size_id")]] . "(" . $val[csf("pattern_no")] . ")");

				$pdf->SetXY($i, $j + 9);
				$pdf->Write(0, " CUT#  " . $order_cut_no . " Bun# " . $bundle_no);


				$pdf->SetXY($i, $j + 12);
				$pdf->Write(0, " STK# " . $val[csf("number_start")] . "-" . $val[csf("number_end")]);

				$pdf->SetXY($i, $j + 15);
				$pdf->Write(0, " Color# " . substr($color_library[$data[5]], 0, 30)); //$color_library[$data[5]]

				$pdf->SetXY($i, $j + 18);
				$pdf->Write(0, " QTY# " . $val[csf("size_qty")] . " " . $inf[csf("bundle_use_for")]);

				$pdf->Code128($i + 1, $j + 21, $val[csf("barcode_no")], 50, 8);

				$k++;
				$br++;
			}
		}
	} else {
		foreach ($color_sizeID_arr as $val) {
			if ($br == 1) {
				$pdf->AddPage();
				$br = 0;
				$i = 2;
				$j = 3;
				$k = 0;
			}

			$article_no = $po_article_data_arr[$val[csf('order_id')]][$val[csf('gmt_item_id')]][$val[csf('country_id')]][$val[csf('color_id')]][$val[csf('size_id')]];
			if ($seq_first == $inf[csf("id")]) $symb = "@@";
			else $symb = "";
			//BNDL
			$style_name = $po_data_arr[$val[csf('order_id')]]["style_ref_no"];
			$buyer_name = $po_data_arr[$val[csf('order_id')]]["buyer_name"];
			$client_id = $po_data_arr[$val[csf('order_id')]]["client_id"];
			$po_number = $po_data_arr[$val[csf('order_id')]]["po_number"];
			$batch_no = $roll_data_arr[$val[csf('roll_id')]];
			$bundle_no_prifix = $val[csf("bundle_num_prefix_no")];
			$bundle_no = $val[csf("bundle_no")];
			$buyer_name_str = "";
			if ($client_id != 0) $buyer_name_str = $buyer_library[$buyer_name] . '-' . $buyer_library[$client_id];
			else $buyer_name_str = $buyer_library[$buyer_name];
			//$dbl_no="17910000000012";
			if ($val[csf('is_excess')] == 1) $country = "EXCESS";
			else $country = $country_arr[$val[csf("country_id")]];

			$pdf->SetXY($i, $j);
			$pdf->Write(0, " Buy# " . " " . $buyer_name_str);

			$pdf->SetXY($i, $j + 3);
			$pdf->Write(0, " STY# " . substr($style_name, 0, 40));

			$pdf->SetXY($i, $j + 6);
			$pdf->Write(0, " PO# " . substr($po_number, 0, 35) . " SIZE# " . $size_arr[$val[csf("size_id")]] . "(" . $val[csf("pattern_no")] . ")");

			$pdf->SetXY($i, $j + 9);
			$pdf->Write(0, " CUT#  " . $order_cut_no . " Bun# " . $bundle_no);

			$pdf->SetXY($i, $j + 12);
			$pdf->Write(0, " STK# " . $val[csf("number_start")] . "-" . $val[csf("number_end")]);;

			$pdf->SetXY($i, $j + 15);
			$pdf->Write(0, " Color# " . substr($color_library[$data[5]], 0, 30)); //$color_library[$data[5]]

			$pdf->SetXY($i, $j + 18);
			$pdf->Write(0, " QTY# " . $val[csf("size_qty")] . " " . $inf[csf("bundle_use_for")]);

			$pdf->Code128($i + 1, $j + 22, $val[csf("barcode_no")], 50, 8);
			$k++;
			$br++;
		}
	}

	foreach (glob("" . "*.pdf") as $filename) {
		@unlink($filename);
	}
	$name = 'bundle_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output("" . $name, 'F');
	echo $name;
	exit();
}

if ($action == "print_barcode_one_urmi_40") {
	//define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	//require('../../../ext_resource/pdf/code128.php');

	require('../../../ext_resource/pdf/code128.php');
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');//order_cut_no

	$data = explode("***", $data);
	$detls_id = $data[3];
	$batch_id = return_field_value("batch_id", "ppl_cut_lay_dtls", "id=$detls_id");
	$buyer_library = return_library_array("select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
	$table_no_library = return_library_array("select id,table_no  from  lib_cutting_table", "id", "table_no");
	$country_arr = return_library_array("select id, short_name from lib_country", 'id', 'short_name');
	$order_cut_no = return_field_value("order_cut_no", "ppl_cut_lay_dtls", "id=$detls_id and status_active=1 and is_deleted=0", "order_cut_no");
	//echo $order_cut_no;
	//$pdf=new PDF_Code39('P','mm','a10');
	//$pdf=PDF_Code128();
	//$pdf=new PDF_Code39(); 
	//$pdf->SetFont('Arial', '', 1000);
	//$pdf->AddPage();


	$bacth_array = array();
	$batchData = sql_select("select id, batch_no, extention_no from pro_batch_create_mst where entry_form in(0,7,37,66,68)");
	foreach ($batchData as $row) {
		$ext = '';
		if ($row[csf('extention_no')] > 0) {
			$ext = '-' . $row[csf('extention_no')];
		}
		$bacth_array[$row[csf('id')]] = $row[csf('batch_no')] . $ext;
	}


	$color_sizeID_arr = sql_select("select a.id, a.size_id, a.bundle_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id
	from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0]) order by b.bundle_sequence,a.id");
	foreach ($color_sizeID_arr as $row) {
		$test_data[$row[csf("roll_id")]] = $row[csf("roll_id")];
	}
	//print_r($test_data);die;
	//echo $data[6].jahid;die;
	$i = 2;
	$j = 2;
	$k = 0;
	$bundle_array = array();
	$br = 0;
	$n = 0;
	$sql_name = sql_select("select b.buyer_name,b.style_ref_no,b.product_dept,a.po_number, a.id as po_id from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.id in(" . $data[6] . ")");
	$po_data_arr = array();
	foreach ($sql_name as $value) {
		/*$product_dept_name=$value[csf('product_dept')];
		$style_name=$value[csf('style_ref_no')];
		$buyer_name=$value[csf('buyer_name')];
		$po_number=$value[csf('po_number')];*/

		$po_data_arr[$value[csf('po_id')]]["style_ref_no"] = $value[csf('style_ref_no')];
		$po_data_arr[$value[csf('po_id')]]["buyer_name"] = $value[csf('buyer_name')];
		$po_data_arr[$value[csf('po_id')]]["po_number"] = $value[csf('po_number')];
	}
	unset($sql_name);

	$roll_sql = sql_select("select roll_id, batch_no from pro_roll_details where entry_form=99 and status_active=1");
	$roll_data_arr = array();
	foreach ($roll_sql as $row) {
		$roll_data_arr[$row[csf("roll_id")]] = $row[csf("batch_no")];
	}
	unset($roll_sql);

	//print_r($roll_data_arr);die;

	$sql_cut_name = sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	foreach ($sql_cut_name as $cut_value) {
		$table_name = $cut_value[csf('table_no')];
		$cut_date = change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix = $cut_value[csf('cut_num_prefix_no')];
		$company_id = $cut_value[csf('company_id')];
		//$batch_no=$cut_value[csf('batch_id')];
		$comp_name = return_field_value("company_short_name", "lib_company", "id=$company_id");
		$new_cut_no = $comp_name . "-" . $cut_prifix;
		$bundle_title = "";
	}



	$pdf = new PDF_Code128('P', 'mm', 'a9');
	$pdf->AddPage();
	$pdf->SetFont('Arial', '', 8);

	if ($data[7] == "") $data[7] = 0;
	$sql_bundle_copy = sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
	if (count($sql_bundle_copy) != 0) {
		foreach ($sql_bundle_copy as $inf) {
			if ($br == 1) {
				$pdf->AddPage();
				$br = 0;
				$i = 2;
				$j = 2;
				$k = 0;
			}
			foreach ($color_sizeID_arr as $val) {
				if ($br == 1) {
					$pdf->AddPage();
					$br = 0;
					$i = 2;
					$j = 2;
					$k = 0;
				}

				//BNDL
				$style_name = $po_data_arr[$val[csf('order_id')]]["style_ref_no"];
				$buyer_name = $po_data_arr[$val[csf('order_id')]]["buyer_name"];
				$po_number = $po_data_arr[$val[csf('order_id')]]["po_number"];
				$batch_no = $roll_data_arr[$val[csf('roll_id')]];

				$bundle_no_arr = explode("-", $val[csf("bundle_no")]);

				if ($val[csf('is_excess')] == 1) $country = "EXCESS";
				else $country = $country_arr[$val[csf("country_id")]];

				$pdf->SetXY($i, $j);
				$pdf->Write(0, $buyer_library[$buyer_name] . "  COUN# " . $country);

				$pdf->SetXY($i, $j + 3);
				$pdf->Write(0, "STYLE# " . $style_name);

				$pdf->SetXY($i, $j + 6);
				$pdf->Write(0, "PO# " . $po_number . "  BNDL# " . $bundle_no_arr[2]);
				$pdf->SetXY($i, $j + 9);
				$pdf->Write(0, "COLOR# " . $color_library[$data[5]]);

				$pdf->SetXY($i, $j + 12);
				$pdf->Write(0, "PART# " . $inf[csf("bundle_use_for")] . "  Batch# " . $batch_no);

				$pdf->SetXY($i, $j + 15);
				$pdf->Write(0, "CUT & ROLL# " . $order_cut_no . " & " . $val[csf("roll_no")] . "  SIZE# " . $size_arr[$val[csf("size_id")]] . "(" . $val[csf("pattern_no")] . ")");
				$pdf->Code128($i, $j + 20, $val[csf("bundle_no")], 40, 8);
				$i = 2;
				$j = $j + 20;
				$k++;
				$br++;
			}
		}
	} else {
		foreach ($color_sizeID_arr as $val) {
			if ($br == 1) {
				$pdf->AddPage();
				$br = 0;
				$i = 2;
				$j = 2;
				$k = 0;
			}

			$style_name = $po_data_arr[$val[csf('order_id')]]["style_ref_no"];
			$buyer_name = $po_data_arr[$val[csf('order_id')]]["buyer_name"];
			$po_number = $po_data_arr[$val[csf('order_id')]]["po_number"];
			$batch_no = $roll_data_arr[$val[csf('roll_id')]];

			$bundle_no_arr = explode("-", $val[csf("bundle_no")]);
			if ($val[csf('is_excess')] == 1) $country = "EXCESS";
			else $country = $country_arr[$val[csf("country_id")]];

			$pdf->SetXY($i, $j);
			$pdf->Write(0, $buyer_library[$buyer_name] . "  COUN# " . $country);

			$pdf->SetXY($i, $j + 3);
			$pdf->Write(0, "STYLE# " . $style_name);

			$pdf->SetXY($i, $j + 6);
			$pdf->Write(0, "PO# " . $po_number . "  BNDL# " . $bundle_no_arr[2]);
			$pdf->SetXY($i, $j + 9);
			$pdf->Write(0, "COLOR# " . $color_library[$data[5]]);

			$pdf->SetXY($i, $j + 12);
			$pdf->Write(0, "PART# " . $inf[csf("bundle_use_for")] . "  Batch# " . $batch_no);

			$pdf->SetXY($i, $j + 15);
			$pdf->Write(0, "CUT & ROLL# " . $order_cut_no . " & " . $val[csf("roll_no")] . "  SIZE# " . $size_arr[$val[csf("size_id")]] . "(" . $val[csf("pattern_no")] . ")");
			$pdf->Code128($i, $j + 20, $val[csf("bundle_no")], 40, 8);
			$k++;
			$i = 2;
			$j = $j + 20;
			/*if($k==2)
			{
				$k=0; $i=10; $j=$j+75;
			}*/

			$br++;
		}
	}


	//$pdf->Output(); 

	foreach (glob("" . "*.pdf") as $filename) {
		@unlink($filename);
	}
	$name = 'bundle_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output("" . $name, 'F');
	echo $name;
	exit();
}
if ($action == "print_barcode_one_pdf") {
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	require('../../../ext_resource/pdf/code39.php');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');

	$data = explode("***", $data);
	$detls_id = $data[3];
	$buyer_library = return_library_array("select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
	$table_no_library = return_library_array("select id,table_no  from  lib_cutting_table", "id", "table_no");
	$country_arr = return_library_array("select id, country_name from lib_country", 'id', 'country_name');
	$order_cut_no = return_field_value("order_cut_no", "ppl_cut_lay_dtls", "id=$detls_id and status_active=1 and is_deleted=0", "order_cut_no");
	//echo $order_cut_no;
	$pdf = new PDF_Code39('P', 'mm', 'a6');
	//$pdf=new PDF_Code39(); 
	//$pdf->SetFont('Arial', '', 1000);
	$pdf->AddPage();


	$color_sizeID_arr = sql_select("select a.id,a.size_id,a.bundle_no,a.number_start,a.number_end,a.size_qty,a.country_id,a.roll_no,b.bundle_sequence 
	from ppl_cut_lay_bundle  a,ppl_cut_lay_size_dtls b
	where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0]) order by b.bundle_sequence,a.id");

	$i = 8;
	$j = 8;
	$k = 0;
	$bundle_array = array();
	$br = 0;
	$n = 0;
	$sql_name = sql_select("select b.buyer_name,b.style_ref_no,b.product_dept,a.po_number from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.id=$data[6]");
	foreach ($sql_name as $value) {
		$product_dept_name = $value[csf('product_dept')];
		$style_name = $value[csf('style_ref_no')];
		$buyer_name = $value[csf('buyer_name')];
		$po_number = $value[csf('po_number')];
	}

	$sql_cut_name = sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	foreach ($sql_cut_name as $cut_value) {
		$table_name = $cut_value[csf('table_no')];
		$cut_date = change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix = $cut_value[csf('cut_num_prefix_no')];
		$company_id = $cut_value[csf('company_id')];
		$batch_no = $cut_value[csf('batch_id')];
		$comp_name = return_field_value("company_short_name", "lib_company", "id=$company_id");
		$new_cut_no = $comp_name . "-" . $cut_prifix;
		$bundle_title = "";
	}
	if ($data[7] == "") $data[7] = 0;
	$sql_bundle_copy = sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
	if (count($sql_bundle_copy) != 0) {
		foreach ($color_sizeID_arr as $val) {
			if ($br == 1) {
				$pdf->AddPage();
				$br = 0;
				$i = 8;
				$j = 8;
				$k = 0;
			}
			foreach ($sql_bundle_copy as $inf) {
				if ($br == 1) {
					$pdf->AddPage();
					$br = 0;
					$i = 8;
					$j = 8;
					$k = 0;
				}

				//if( $k>0 && $k<2 ) { $i=$i+105; }
				$pdf->Code39($i, $j, $val[csf("bundle_no")]);
				$pdf->Code39($i + 45, $j - 4, "Bundle Card", $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true);
				$pdf->Code39($i + 45, $j + 1,  $inf[csf("bundle_use_for")], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true);
				$pdf->Code39($i + 45, $j + 6, "Roll No: " . $val[csf("roll_no")], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true);
				$pdf->Code39($i, $j + 6, "Cut Sys No: " . $new_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
				$pdf->Code39($i + 40, $j + 6, "Cut Date: " . $cut_date, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
				$pdf->Code39($i, $j + 11,  "Buyer: " . $buyer_library[$buyer_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
				$pdf->Code39($i + 40, $j + 11, "PO: " . $po_number, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
				$pdf->Code39($i, $j + 16,  "Style Ref: " . $style_name, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
				$pdf->Code39($i + 40, $j + 21, "Item: " . $garments_item[$data[4]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
				$pdf->Code39($i, $j + 21, "Size: " . $size_arr[$val[csf("size_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
				$pdf->Code39($i, $j + 26, "Batch No: " . $batch_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
				$pdf->Code39($i + 40, $j + 26, "Color: " . $color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
				$pdf->Code39($i + 40, $j + 31, "Bundle No: " . $val[csf("bundle_no")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
				$pdf->Code39($i, $j + 31, "Gmts. No: " . $val[csf("number_start")] . "-" . $val[csf("number_end")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
				$pdf->Code39($i + 40, $j + 36, "Country: " . $country_arr[$val[csf("country_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
				$pdf->Code39($i, $j + 36, "Gmts. Qnty: " . $val[csf("size_qty")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);

				$k++;
				$i = 8;
				$j = $j + 60;
				/*if($k==2)
				{
					$k=0; $i=10; $j=$j+75;
				}*/

				$br++;
			}
		}
	} else {
		foreach ($color_sizeID_arr as $val) {
			if ($br == 1) {
				$pdf->AddPage();
				$br = 0;
				$i = 8;
				$j = 8;
				$k = 0;
			}

			$pdf->Code39($i, $j, $val[csf("bundle_no")]);
			$pdf->Code39($i + 45, $j - 4, "Roll No: " . $val[csf("roll_no")], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true);
			$pdf->Code39($i + 45, $j + 1,  $inf[csf("bundle_use_for")], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true);
			$pdf->Code39($i, $j + 6, "Cutting No: " . $new_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
			$pdf->Code39($i + 40, $j + 6, "Cut Date: " . $cut_date, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
			$pdf->Code39($i, $j + 11,  "Buyer: " . $buyer_library[$buyer_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
			$pdf->Code39($i + 40, $j + 11, "PO: " . $po_number, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
			$pdf->Code39($i, $j + 16,  "Style Ref: " . $style_name, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
			$pdf->Code39($i + 40, $j + 21, "Item: " . $garments_item[$data[4]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
			$pdf->Code39($i, $j + 21, "Size: " . $size_arr[$val[csf("size_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
			$pdf->Code39($i, $j + 26, "Batch No: " . $batch_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
			$pdf->Code39($i + 40, $j + 26, "Color: " . $color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
			$pdf->Code39($i + 40, $j + 31, "Bundle No: " . $val[csf("bundle_no")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
			$pdf->Code39($i, $j + 31, "Gmts. No: " . $val[csf("number_start")] . "-" . $val[csf("number_end")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
			$pdf->Code39($i + 40, $j + 36, "Country: " . $country_arr[$val[csf("country_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
			$pdf->Code39($i, $j + 36, "Gmts. Qnty: " . $val[csf("size_qty")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);

			$i = 8;
			$j = $j + 60;

			/*$k++;
			if($k==2)
			{ $k=0; $i=10; $j=$j+75; }*/
			$br++;
		}
	}

	foreach (glob("" . "*.pdf") as $filename) {
		@unlink($filename);
	}
	$name = 'bundle_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output("" . $name, 'F');
	echo $name;
	exit();
}

if ($action == "print_barcode_one") {
 ?>
	<style type="text/css" media="print">
		p {
			page-break-after: always;
		}
	</style>
	<?
	$data = explode("***", $data);
	$detls_id = $data[3];
	$batch_id = return_field_value("batch_id", "ppl_cut_lay_dtls", "id=$detls_id");
	$buyer_library = return_library_array("select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
	$table_no_library = return_library_array("select id,table_no  from  lib_cutting_table", "id", "table_no");
	$country_arr = return_library_array("select id, country_name from lib_country", 'id', 'country_name');
	$order_cut_no = return_field_value("order_cut_no", "ppl_cut_lay_dtls", "id=$detls_id and status_active=1 and is_deleted=0", "order_cut_no");

	$bacth_array = array();
	$batchData = sql_select("select id, batch_no, extention_no from pro_batch_create_mst where entry_form in(0,7,37,66,68)");
	foreach ($batchData as $row) {
		$ext = '';
		if ($row[csf('extention_no')] > 0) {
			$ext = '-' . $row[csf('extention_no')];
		}
		$bacth_array[$row[csf('id')]] = $row[csf('batch_no')] . $ext;
	}

	$color_sizeID_arr = sql_select("select a.id,a.size_id,a.bundle_no,a.number_start,a.number_end,a.size_qty,a.country_id,a.roll_no,b.bundle_sequence 
	from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0]) order by b.bundle_sequence,a.id");

	$bundle_array = array();
	$sql_name = sql_select("select b.buyer_name,b.style_ref_no,b.product_dept,a.po_number from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.id=$data[6]");
	foreach ($sql_name as $value) {
		$product_dept_name = $value[csf('product_dept')];
		$style_name = $value[csf('style_ref_no')];
		$buyer_name = $value[csf('buyer_name')];
		$po_number = $value[csf('po_number')];
	}
	$sql_cut_name = sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	foreach ($sql_cut_name as $cut_value) {
		$table_name = $cut_value[csf('table_no')];
		$cut_date = change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix = $cut_value[csf('cut_num_prefix_no')];
		$company_id = $cut_value[csf('company_id')];
		$batch_no = $cut_value[csf('batch_id')];
		$comp_name = return_field_value("company_short_name", "lib_company", "id=$company_id");
		$new_cut_no = $comp_name . "-" . $cut_prifix;
		$bundle_title = "";
	}

	if ($data[7] == "") $data[7] = 0;
	$i = 1;
	$sql_bundle_copy = sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
	if (count($sql_bundle_copy) != 0) {
		foreach ($color_sizeID_arr as $val) {
			foreach ($sql_bundle_copy as $inf) {
				$bundle_array[$i] = $val[csf("bundle_no")];
				echo '<table style="width: 4.0in;" border="0" cellpadding="0" cellspacing="0">';
				$bundle = "&nbsp;&nbsp;" . $val[csf("bundle_no")];
				$title = "Bundle Card<br>" . $inf[csf("bundle_use_for")] . "<br>" . "Roll No: " . $val[csf("roll_no")];
				/*
				$pdf->Code39($i, $j+11,  "Buyer: ".$buyer_library[$buyer_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i+40, $j+11,"PO: ".$po_number, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i, $j+16,  "Style Ref: ".$style_name, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i+40, $j+21, "Item: ".$garments_item[$data[4]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i, $j+21, "Size: ".$size_arr[$val[csf("size_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i, $j+26, "Batch No: ".$batch_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i+40, $j+26, "Color: ".$color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i+40, $j+31, "Bundle No: ".$val[csf("bundle_no")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i, $j+31, "Gmts. No: ".$val[csf("number_start")]."-".$val[csf("number_end")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i+40, $j+36, "Country: ".$country_arr[$val[csf("country_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i, $j+36, "Gmts. Qnty: ".$val[csf("size_qty")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;*/
				echo '<tr><td style="padding-left:5px;padding-top:10px;padding-bottom:5px"><div id="div_' . $i . '"></div>' . $bundle . '</td><td>' . $title . '</td></tr>';
				echo '<tr><td style="padding-left:5px;">&nbsp;&nbsp;Cut Sys No: ' . $new_cut_no . '</td><td>Cut Date: ' . $cut_date . '</td></tr>';
				echo '<tr><td style="padding-left:5px;">&nbsp;&nbsp;Buyer: ' . $buyer_library[$buyer_name] . '</td><td>PO: ' . $po_number . '</td></tr>';
				echo '<tr><td style="padding-left:5px;" colspan="2">&nbsp;&nbsp;Style Ref: ' . $style_name . '</td></tr>';
				echo '<tr><td style="padding-left:5px;">&nbsp;&nbsp;Size: ' . $size_arr[$val[csf("size_id")]] . '</td><td>Item: ' . $garments_item[$data[4]] . '</td></tr>';
				echo '<tr><td style="padding-left:5px;">&nbsp;&nbsp;Batch No: ' . $bacth_array[$batch_id] . '</td><td>Color: ' . $color_library[$data[5]] . '</td></tr>';
				echo '<tr><td style="padding-left:5px;">&nbsp;&nbsp;Gmts. No: ' . $val[csf("number_start")] . "-" . $val[csf("number_end")] . '</td><td>Bundle No: ' . $val[csf("bundle_no")] . '</td></tr>';
				echo '<tr><td style="padding-left:5px;">&nbsp;&nbsp;Gmts. Qnty: ' . $val[csf("size_qty")] . '</td><td>Country: ' . $country_arr[$val[csf("country_id")]] . '</td></tr>';
				echo '</table><p></p>';
				$i++;
			}
		}
	} else {
		foreach ($color_sizeID_arr as $val) {
			/*if($br==1) { $pdf->AddPage(); $br=0; $i=8; $j=8; $k=0;}
			
			$pdf->Code39($i, $j, $val[csf("bundle_no")]);
			$pdf->Code39($i+45, $j-4, "Roll No: ". $val[csf("roll_no")], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;
			$pdf->Code39($i+45, $j+1,  $inf[csf("bundle_use_for")], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;						
			$pdf->Code39($i, $j+6, "Cutting No: ".$new_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i+40, $j+6, "Cut Date: ".$cut_date, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i, $j+11,  "Buyer: ".$buyer_library[$buyer_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i+40, $j+11,"PO: ".$po_number, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i, $j+16,  "Style Ref: ".$style_name, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i+40, $j+21, "Item: ".$garments_item[$data[4]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i, $j+21, "Size: ".$size_arr[$val[csf("size_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i, $j+26, "Batch No: ".$batch_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i+40, $j+26, "Color: ".$color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i+40, $j+31, "Bundle No: ".$val[csf("bundle_no")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i, $j+31, "Gmts. No: ".$val[csf("number_start")]."-".$val[csf("number_end")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i+40, $j+36, "Country: ".$country_arr[$val[csf("country_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10,$wide = true,true) ;
			$pdf->Code39($i, $j+36, "Gmts. Qnty: ".$val[csf("size_qty")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;*/

			$bundle_array[$i] = $val[csf("bundle_no")];
			echo '<table style="width: 4.0in;" border="0" cellpadding="0" cellspacing="0">';
			$bundle = "&nbsp;&nbsp;" . $val[csf("bundle_no")];
			$title = "Roll No: " . $val[csf("roll_no")];
			echo '<tr><td style="padding-left:5px;padding-top:10px;padding-bottom:5px"><div id="div_' . $i . '"></div>' . $bundle . '</td><td>' . $title . '</td></tr>';
			echo '<tr><td style="padding-left:5px;">&nbsp;&nbsp;Cut Sys No: ' . $new_cut_no . '</td><td>Cut Date: ' . $cut_date . '</td></tr>';
			echo '<tr><td style="padding-left:5px;">&nbsp;&nbsp;Buyer: ' . $buyer_library[$buyer_name] . '</td><td>PO: ' . $po_number . '</td></tr>';
			echo '<tr><td style="padding-left:5px;" colspan="2">&nbsp;&nbsp;Style Ref: ' . $style_name . '</td></tr>';
			echo '<tr><td style="padding-left:5px;">&nbsp;&nbsp;Size: ' . $size_arr[$val[csf("size_id")]] . '</td><td>Item: ' . $garments_item[$data[4]] . '</td></tr>';
			echo '<tr><td style="padding-left:5px;">&nbsp;&nbsp;Batch No: ' . $bacth_array[$batch_id] . '</td><td>Color: ' . $color_library[$data[5]] . '</td></tr>';
			echo '<tr><td style="padding-left:5px;">&nbsp;&nbsp;Gmts. No: ' . $val[csf("number_start")] . "-" . $val[csf("number_end")] . '</td><td>Bundle No: ' . $val[csf("bundle_no")] . '</td></tr>';
			echo '<tr><td style="padding-left:5px;">&nbsp;&nbsp;Gmts. Qnty: ' . $val[csf("size_qty")] . '</td><td>Country: ' . $country_arr[$val[csf("country_id")]] . '</td></tr>';
			echo '</table><p></p>';
			$i++;
		}
	}

	?>

	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		var barcode_array = <? echo json_encode($bundle_array); ?>;

		function generateBarcode(td_no, valuess) {
			var value = valuess; //$("#barcodeValue").val();
			//alert(value)
			var btype = 'code39'; //$("input[name=btype]:checked").val();
			var renderer = 'bmp'; // $("input[name=renderer]:checked").val();

			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {
				code: value,
				rect: false
			};

			$("#div_" + td_no).show().barcode(value, btype, settings);
		}

		for (var i in barcode_array) {
			generateBarcode(i, barcode_array[i]);
		}
	</script>
 <?
	exit();
}

if ($action == "print_report_bundle_barcode") {
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	require('../../../ext_resource/pdf/code39.php');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');
	$data = explode("***", $data);

	//$ext_data=explode("__",$data[1]);
	//$cs_data=explode("__",$data[2]);
	$buyer_library = return_library_array("select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
	$table_no_library = return_library_array("select id,table_no  from  lib_cutting_table", "id", "table_no");
	$country_arr = return_library_array("select id, country_name from lib_country", 'id', 'country_name');
	$pdf = new PDF_Code39();
	//$pdf->SetFont('Times', '', 20);
	$pdf->AddPage();
	$color_sizeID_arr = sql_select("select id,size_id,bundle_no,number_start,number_end,size_qty,country_id from ppl_cut_lay_bundle where id in ( $data[0] ) ");  //where id in ($data)
	$i = 5;
	$j = 10;
	$k = 0;
	$bundle_array = array();
	$br = 0;
	$n = 0;
	$sql_name = sql_select("select b.buyer_name,b.style_ref_no,b.product_dept,a.po_number from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.id=$data[6]");
	foreach ($sql_name as $value) {
		$product_dept_name = $value[csf('product_dept')];
		$style_name = $value[csf('style_ref_no')];
		$buyer_name = $value[csf('buyer_name')];
		$po_number = $value[csf('po_number')];
	}

	$sql_cut_name = sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	foreach ($sql_cut_name as $cut_value) {
		$table_name = $cut_value[csf('table_no')];
		$cut_date = change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix = $cut_value[csf('cut_num_prefix_no')];
		$company_id = $cut_value[csf('company_id')];
		$batch_no = $cut_value[csf('batch_id')];
		$comp_name = return_field_value("company_short_name", "lib_company", "id=$company_id");
		$new_cut_no = $comp_name . "-" . $cut_prifix;
		$bundle_title = "";
	}
	$sql_bundle_copy = sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id");

	$cope_page = 1;
	if (count($sql_bundle_copy) != 0) {
		foreach ($sql_bundle_copy as $inf) {
			if ($br == 6) {
				$pdf->AddPage();
				$br = 0;
				$i = 5;
				$j = 10;
				$k = 0;
			}
			foreach ($color_sizeID_arr as $val) {

				if ($br == 6) {
					$pdf->AddPage();
					$br = 0;
					$i = 5;
					$j = 10;
					$k = 0;
				}

				if ($k > 0 && $k < 2) {
					$i = $i + 100;
				}

				$pdf->Code39($i, $j, $val[csf("bundle_no")]);
				$pdf->Code39($i + 45, $j - 4, "Bundle Card ", $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true);
				$pdf->Code39($i + 45, $j, "Country: " . $country_arr[$val[csf("country_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
				$pdf->Code39($i + 45, $j + 1,  $inf[csf("bundle_use_for")], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true);
				$pdf->Code39($i, $j + 6, "Cutting No: " . $new_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
				$pdf->Code39($i + 38, $j + 6, "Cut Date	 : " . $cut_date, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
				$pdf->Code39($i, $j + 12,  "Buyer : " . $buyer_library[$buyer_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
				$pdf->Code39($i + 38, $j + 12, "Ord:" . $po_number, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
				$pdf->Code39($i, $j + 18,  "Style Ref  :  " . $style_name, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
				$pdf->Code39($i, $j + 24, "Item :  " . $garments_item[$data[4]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
				$pdf->Code39($i, $j + 30, "Table No :  " . $table_no_library[$table_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
				$pdf->Code39($i, $j + 36, "Color	:  " . $color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
				$pdf->Code39($i + 38, $j + 30, "Size :  " . $size_arr[$val[csf("size_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
				$pdf->Code39($i + 38, $j + 42, "Dept : " . $product_dept[$product_dept_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);

				$pdf->Code39($i, $j + 42, "Bundle No:" . $val[csf("bundle_no")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
				$pdf->Code39($i, $j + 48, "Gmts. No :  " . $val[csf("number_start")] . "-" . $val[csf("number_end")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
				$pdf->Code39($i + 38, $j + 48, "Gmts. Qnty : " . $val[csf("size_qty")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
				$pdf->Code39($i, $j + 54, "Batch No: " . $batch_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
				$k++;

				if ($k == 2) {
					$k = 0;
					$i = 5;
					$j = $j + 90;
				}
				$br++;
			}
			$br = 6;
			$cope_page++;
		}
	} else {
		foreach ($color_sizeID_arr as $val) {
			if ($br == 6) {
				$pdf->AddPage();
				$br = 0;
				$i = 5;
				$j = 10;
				$k = 0;
			}
			if ($k > 0 && $k < 2) {
				$i = $i + 100;
			}
			$pdf->Code39($i, $j, $val[csf("bundle_no")]);
			//$pdf->Code39($i+45, $j, "Bundle Card ".$bundle_title, $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;
			$pdf->Code39($i, $j + 6, "Cutting No: " . $new_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
			$pdf->Code39($i + 38, $j + 6, "Cut Date	 : " . $cut_date, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
			$pdf->Code39($i, $j + 12,  "Buyer : " . $buyer_library[$buyer_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
			$pdf->Code39($i + 38, $j + 12, "Ord:" . $po_number, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
			$pdf->Code39($i, $j + 18,  "Style Ref  :  " . $style_name, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
			$pdf->Code39($i, $j + 24, "Item :  " . $garments_item[$data[4]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
			$pdf->Code39($i, $j + 30, "Table No :  " . $table_no_library[$table_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
			$pdf->Code39($i, $j + 36, "Color	:  " . $color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
			$pdf->Code39($i + 38, $j + 30, "Size :  " . $size_arr[$val[csf("size_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
			$pdf->Code39($i + 42, $j + 42, "Dept : " . $product_dept[$product_dept_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);

			$pdf->Code39($i, $j + 42, "Bundle No:" . $val[csf("bundle_no")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
			$pdf->Code39($i, $j + 48, "Gmts. No :  " . $val[csf("number_start")] . "-" . $val[csf("number_end")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
			$pdf->Code39($i + 38, $j + 48, "Gmts. Qnty : " . $val[csf("size_qty")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
			$pdf->Code39($i, $j + 54, "Batch No: " . $batch_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true);
			$k++;

			if ($k == 2) {
				$k = 0;
				$i = 5;
				$j = $j + 90;
			}
			$br++;
		}
	}
	foreach (glob("" . "*.pdf") as $filename) {
		@unlink($filename);
	}
	$name = 'bundle_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output("" . $name, 'F');
	echo $name;
	exit();
}
//----------------------------------bundle qty update finish---------------------------------------------------------------------------------



if ($action == "save_update_delete_size") 
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	if ($operation == 0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		$cutting_qc_no = return_field_value("cutting_qc_no", "pro_gmts_cutting_qc_mst", "status_active=1 and is_deleted=0 and cutting_no='" . $cut_no . "'");
		if ($cutting_qc_no != "") {
			echo "201**" . $mst_id . "**" . $dtls_id . "**" . $cutting_qc_no;
			disconnect($con);
			die;
		}

		// ================================ get variable setting ======================================
		$variable_setting_for_bundle_creation = return_field_value("smv_source", "variable_settings_production", "status_active=1 and is_deleted=0 and company_name=$cbo_company_id and variable_list=37");

		if ($variable_setting_for_bundle_creation == 0) $variable_setting_for_bundle_creation = 1;

		if ($variable_setting_for_bundle_creation == 2) // job wise
		{
			$cutData = sql_select("select job_no_prefix_num, job_no from wo_po_details_master where job_no = '$job_id'");
			$cut_on_prifix = $cutData[0][csf('job_no_prefix_num')];
			$job_no = $cutData[0][csf('job_no')];
		} else // cutting wise
		{
			$cutData = sql_select("select cut_num_prefix_no, job_no from ppl_cut_lay_mst where cutting_no = '" . $cut_no . "'");
			$cut_on_prifix = $cutData[0][csf('cut_num_prefix_no')];
			$job_no = $cutData[0][csf('job_no')];
		}

		/*$cutData=sql_select("select cut_num_prefix_no, job_no from ppl_cut_lay_mst where cutting_no = '".$cut_no."'");
		$cut_on_prifix=$cutData[0][csf('cut_num_prefix_no')];
		$job_no=$cutData[0][csf('job_no')];*/


		/*======================================================================/
		/					Variable Setting for Pattern No Creation 			/
		/======================================================================*/
		$pattern_sequence = return_field_value("batch_maintained", "variable_settings_production", "status_active=1 and is_deleted=0 and company_name=$cbo_company_id and variable_list=155");
		$pattern_sequence = ($pattern_sequence == "") ? 1 : $pattern_sequence;
		// echo "10**".$pattern_sequence;die();


		$plan_qty = return_field_value("sum(plan_cut_qnty) as plan_qty", "wo_po_color_size_breakdown", "po_break_down_id in(" . $order_id . ") and item_number_id=" . $gmt_id . " and color_number_id in(" . $color_id . ") and status_active=1", "plan_qty");

		$total_marker_qty_prev = return_field_value("sum(b.marker_qty) as mark_qty", "ppl_cut_lay_dtls a, ppl_cut_lay_size b", "a.id=b.dtls_id and b.order_id in(" . $order_id . ") and a.gmt_item_id=" . $gmt_id . " and a.color_ids=" . $color_id . " and a.status_active=1 and b.status_active=1 and b.is_deleted=0", "mark_qty");
		// echo $total_marker_qty_prev;die;

		if ($size_wise_repeat_cut_no == 1) {
			$sql_bundle = sql_select("SELECT size_id, max(number_end) as last_rmg FROM ppl_cut_lay_bundle WHERE mst_id='" . $mst_id . "' group by size_id");
			foreach ($sql_bundle as $row) {
				$bundle_no_array[$row[csf('size_id')]] = $row[csf('last_rmg')];
			}
		}

		$sizeRatioArr = array();
		$sizeQtyArr = array();
		$sizeQtyArrForC = array();
		$sizeIdAgainstSeq = array();
		$seqDatas = '';
		$sizeChkArr = array();
		$manualSizeArr = array();
		$id_size = return_next_id("id", "ppl_cut_lay_size_dtls", 1);
		$field_array_size = "id,mst_id,dtls_id,color_id,size_id,size_ratio,marker_qty,bundle_sequence,inserted_by,insert_date";
		for ($i = 1; $i <= $size_row_num; $i++) {
			$hidden_txt_color = "hidden_txt_color_" . $i;
			$hidden_sizef_id = "hidden_sizef_id_" . $i;
			$txt_layf_balance = "txt_layf_balance_" . $i;
			$txt_sizef_ratio = "txt_sizef_ratio_" . $i;
			$txt_sizef_qty = "txt_sizef_qty_" . $i;
			$bundle_sequence = "txt_bundle_" . $i;
			$sizef_name = "txt_sizef_name_" . $i;
			$manualSizeArr[$$hidden_sizef_id] = $$sizef_name;
			// if($sizeChkArr[str_replace("'",'',$$hidden_sizef_id)]=="")
			// {
			if (str_replace("'", '', $$txt_sizef_qty) > 0) {
				if (str_replace("'", '', $$bundle_sequence) > 0) {
					$seq = str_replace("'", '', $$bundle_sequence);
				} else {
					$max_seq++;
					$seq = $max_seq;
				}
			
				$dataSize = $mst_id . "," . $dtls_id .",". $hidden_txt_color .","  . $$hidden_sizef_id . "," . $$txt_sizef_ratio . "," . $$txt_sizef_qty . "," . $seq;
				$data_array_up[$seq] = $dataSize;
				$sizeIdAgainstSeq[$seq] = str_replace("'", '', $$hidden_sizef_id);

				$sizeRatioArr[str_replace("'", '', $$hidden_sizef_id)] = str_replace("'", '', $$txt_sizef_ratio);
				$sizeQtyArr[str_replace("'", '', $$hidden_sizef_id)] = str_replace("'", '', $$txt_sizef_qty);
				$sizeQtyArrForC[str_replace("'", '', $$hidden_sizef_id)] = str_replace("'", '', $$txt_sizef_qty);
				$sizeSeqArr[str_replace("'", '', $$hidden_sizef_id)] = $seq;
			}
			// 	$sizeChkArr[str_replace("'",'',$$hidden_sizef_id)] = str_replace("'",'',$$hidden_sizef_id);
			// }
		}

		// echo "<pre>"; print_r($sizeSeqArr);echo "</pre>";die();
		ksort($data_array_up);
		foreach ($data_array_up as $sequence => $data) {
			if ($data_array_size != "") $data_array_size .= ",";
			$data_array_size .= "(" . $id_size . "," . $data . "," . $user_id . ",'" . $pc_date_time . "')";
			$seqDatas .= $id_size . "__" . $sizeIdAgainstSeq[$sequence] . "__" . $sequence . ",";
			$id_size++;
		}

		$roll_size_arr = array();
		$roll_no_arr = array();
		$rollsizeBl = array();
		$rollDatas = explode("|", $roll_data);
		$rollDtls_id = return_next_id("id", "ppl_cut_lay_roll_dtls", 1);
		$field_array_roll_dtls = "id,mst_id,dtls_id,roll_id,roll_no,roll_wgt,plies,size_id,size_qty,inserted_by,insert_date";
		foreach ($rollDatas as $data) {
			$datas = explode("=", $data);
			$roll_no = $datas[1];
			$roll_id = $datas[2];
			$roll_wgt = $datas[3];
			$plies = $datas[4];

			if ($roll_id == "" || $roll_id == 0) $roll_id = $rollDtls_id;

			// $roll_no_arr[$roll_id]=$roll_no;
			foreach ($sizeRatioArr as $size_id => $size_ratio) {
				$roll_no_arr[$size_id][$roll_id] = $roll_no;
				$size_qty = $size_ratio * $plies;
				if ($data_array_roll_dtls != "") $data_array_roll_dtls .= ",";
				$data_array_roll_dtls .= "(" . $rollDtls_id . "," . $mst_id . "," . $dtls_id . ",'" . $roll_id . "','" . $roll_no . "','" . $roll_wgt . "','" . $plies . "','" . $size_id . "'," . $size_qty . "," . $user_id . ",'" . $pc_date_time . "')";

				$rollsizeBl[$size_id][$roll_id] = $size_qty;
				$rollPliesArr[$size_id][$roll_id] = $plies;
				$rollDtls_id++;
			}
		}

		$bundle_no_array = array();
		$bundle_id = return_next_id("id", "ppl_cut_lay_bundle", 1);

		$field_array_bundle = "id,mst_id,dtls_id,size_id,bundle_num_prefix,bundle_num_prefix_no,bundle_no,barcode_year,barcode_prifix,barcode_no,number_start,number_end,size_qty,country_type,country_id,roll_id,roll_no,pattern_no,order_id,is_excess,color_type_id,inserted_by,insert_date";

		// =================== getting last bundle no ======================
		if ($variable_setting_for_bundle_creation == 2) // job wise
		{
			$bundleNoData = sql_select("select max(b.bundle_num_prefix_no) as last_bundle_no from ppl_cut_lay_mst a, ppl_cut_lay_bundle b where a.id=b.mst_id and a.job_no='$job_id' order by b.id desc");
			$last_bundle_no = $bundleNoData[0][csf('last_bundle_no')];
		} else {
			$bundleNoData = sql_select("select max(number_end) as last_rmg, max(bundle_num_prefix_no) as last_prefix from ppl_cut_lay_bundle where mst_id=$mst_id");
			$last_bundle_no = $bundleNoData[0][csf('last_prefix')];
		}

		$bundleData = sql_select("select max(number_end) as last_rmg, max(bundle_num_prefix_no) as last_prefix from ppl_cut_lay_bundle where mst_id=$mst_id");
		$last_rmg_no = $bundleData[0][csf('last_rmg')];
		// $last_bundle_no=$bundleData[0][csf('last_prefix')];



		$update_id = '';
		$tot_marker_qnty_curr = 0;
		$bundle_prif_no = $last_bundle_no;
		$size_country_array = array();
		$country_type_array = array();
		$sizeRatioBlArr = array();
		$sizeChkArr = array();
		$data_array = "";

		$id = return_next_id("id", "ppl_cut_lay_size", 1);
		$field_array = "id,mst_id,dtls_id,size_id,size_ratio,marker_qty,country_type,country_id,excess_perc,order_id,manual_size_name,size_wise_repeat,inserted_by,insert_date";
		for ($i = 1; $i <= $row_num; $i++) {
			$txt_size_id = "hidden_size_id_" . $i;
			$txt_lay_balance = "txt_lay_balance_" . $i;
			$cboCountryType = "cboCountryType_" . $i;
			$cboCountry = "cboCountry_" . $i;
			$excess_perc = "txt_excess_" . $i;
			$po_id = "poId_" . $i;
			$txt_size_qty = "txt_size_qty_" . $i;

			$marker_qty = 0;
			$order_id = str_replace("'", '', $$po_id);
			$size_id = str_replace("'", '', $$txt_size_id);
			$lay_balance = str_replace("'", '', $$txt_lay_balance);
			$marker_qty = str_replace("'", '', $$txt_size_qty);
			$country_type_array[$order_id][str_replace("'", '', $$cboCountry)] = str_replace("'", '', $$cboCountryType);
			// if($sizeChkArr[$size_id]=="")
			// {
			if ($marker_qty > 0) {
				$size_country_array[$size_id][$order_id][str_replace("'", '', $$cboCountry)] += $marker_qty;
				$tot_marker_qnty_curr += $marker_qty;

				if ($data_array != "") $data_array .= ",";
				$data_array .= "(" . $id . "," . $mst_id . "," . $dtls_id ."," . $$txt_size_id . ",0," . $marker_qty . "," . $$cboCountryType . "," . $$cboCountry . ",'" . $$excess_perc . "'," . $$po_id . ",'" . $manualSizeArr[$$txt_size_id] . "','" . $size_wise_repeat_cut_no . "'," . $user_id . ",'" . $pc_date_time . "')";
				$id = $id + 1;
			}
			// 	$sizeChkArr[$size_id] = $size_id;
			// }
		}

		$company_sort_name = explode("-", $cut_no);
		$bundle_per_pcs = str_replace("'", "", $bundle_per_pcs);
		asort($sizeSeqArr);


		foreach ($sizeSeqArr as $size_id => $size_seq) {
			$size_ratio = $sizeRatioArr[$size_id];
			$size_qty = $sizeQtyArr[$size_id];

			$pattern_no = "A";
			for ($k = 1; $k <= $size_ratio; $k++) {
				foreach ($roll_no_arr[$size_id] as $rollId => $rollNo) {
					$sizeRatioBlArr[$size_id][$pattern_no][$rollId] = $rollPliesArr[$size_id][$rollId];
				}

				$pattern_no++;
			}
		}
		// echo "10**<pre>"; print_r($sizeRatioBlArr);echo "</pre>";die();

		/*======================================================================/
		/								RMG No Creation 						/
		/======================================================================*/
		$erange = 0;
		if ($rmg_no_creation == 2) // cutting wise
		{
			$erange = return_field_value("max(number_end) as last_rmg", "ppl_cut_lay_bundle", "mst_id=$mst_id and dtls_id!=$dtls_id and status_active=1 and is_deleted=0", "last_rmg");
		} else if ($rmg_no_creation == 3) // job wise
		{
			$erange = return_field_value("max(a.number_end) as last_rmg", "ppl_cut_lay_bundle a, ppl_cut_lay_mst b", "a.mst_id=b.id and b.entry_form=715 and b.job_no='$job_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "last_rmg");
		} else if ($rmg_no_creation == 4) // order wise
		{
			$erange = return_field_value("max(a.number_end) as last_rmg", "ppl_cut_lay_bundle a, ppl_cut_lay_dtls b", "a.dtls_id=b.id and a.order_id=$order_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "last_rmg");
		} else if ($rmg_no_creation == 6) // up to 999
		{
			$max_erange = 999;
			$erange = return_field_value("a.number_end as last_rmg", "ppl_cut_lay_bundle a, ppl_cut_lay_mst b", "a.mst_id=b.id and b.entry_form=715 and b.job_no='$job_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id desc", "last_rmg");
			$erange = ($erange > $max_erange) ? 0 : $erange;
		} else if ($rmg_no_creation == 7) // up to 9999
		{
			$max_erange = 9999;
			$erange = return_field_value("a.number_end as last_rmg", "ppl_cut_lay_bundle a, ppl_cut_lay_mst b", "a.mst_id=b.id and b.entry_form=715 and b.job_no='$job_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id desc", "last_rmg");
			$erange = ($erange > $max_erange) ? 0 : $erange;
		} else if ($rmg_no_creation == 8) // Size wise multi color
		{
			$sql = "SELECT b.size_id, max(b.number_end) as last_rmg FROM ppl_cut_lay_dtls a, ppl_cut_lay_bundle b WHERE a.id = b.dtls_id and a.mst_id=$mst_id group by  b.size_id order by  b.size_id";
			$res = sql_select($sql);
			$erange_arr = array();
			foreach ($res as $val) {
				$erange_arr[$val['SIZE_ID']] = $val['LAST_RMG'];
			}
		}
		// echo "10**$rmg_no_creation=".$erange;die();

		// echo "10**$rmg_no_creation**select max(a.number_end) as last_rmg from ppl_cut_lay_bundle a, ppl_cut_lay_dtls b where a.dtls_id=b.id and a.order_id='$order_id' and a.status_active=1 and a.is_deleted=0";die();
		$cutNo = str_replace("'", "", $cut_no);
		$cutNoEx = explode("-", $cutNo);
		$year_id = $cutNoEx[1];
		// $year_id=date('Y',time());
		if (strlen($year_id) == 4) $year_id = substr($year_id, 2, 2);
		$barcode_suffix_no = return_field_value("max(barcode_prifix) as suffix_no", "ppl_cut_lay_bundle", "barcode_year=$year_id", "suffix_no");

		$pattern_sec = "A";
		$bundle_qty_check = true;
		foreach ($sizeSeqArr as $size_id => $size_seq) {
			/*if($size_wise_repeat_cut_no==1)
			{
				$last_rmg_no=$bundle_no_array[$size_id];
				$erange=$last_rmg_no;
			}*/


			$size_ratio = $sizeRatioArr[$size_id];
			$size_qty = $sizeQtyArr[$size_id];
			if ($pattern_sequence == 1) {
				$pattern_no = "A";
				$pattern_sec = "A";
			}
			if ($pattern_sequence == 2) {
				$pattern_no = "A";
			}
			if ($rmg_no_creation == 1) {
				$erange = 0;
			}
			if ($rmg_no_creation == 8) {
				$erange = $erange_arr[$size_id];
			}
			for ($k = 1; $k <= $size_ratio; $k++) {
				/*if($size_wise_repeat_cut_no!=1)
				{
					$erange=0;
				}*/
				// echo "10**kakku<br>";
				if ($rmg_no_creation == 5) {
					$erange = 0;
				}
				$plies = $txt_plies;
				$tmp_bl_arr = array();
				foreach ($roll_no_arr[$size_id] as $rollId => $rollNo) {
					foreach ($size_country_array[$size_id] as $order_id => $order_data) {
						foreach ($order_data as $country_id => $size_country_qty) {

							if ($sizeRatioBlArr[$size_id][$pattern_no][$rollId] > 0) {
								$temp_bal_flag = 1;
								// echo $sizeRatioBlArr[$size_id][$pattern_no][$rollId];die();
								$bl_size_qty = $size_country_array[$size_id][$order_id][$country_id];
								if ($plies > 0 && $tmp_bl_arr[$size_id][$rollId][1] > 0 && $bl_size_qty > 0) {
									$temp_bal_flag = 0;
									// $bundle_qty2=$tmp_bl_arr[$size_id][$rollId][1]; 
									if ($tmp_bl_arr[$size_id][$rollId][1] > $bl_size_qty) {
										$bundle_qty2 = $bl_size_qty;
										$tmp_bl_arr[$size_id][$rollId][1] -= $bundle_qty2; //echo $bundle_qty2."==".$bl_size_qty."==".$country_id;
										$tmp_bl_arr[$size_id][$rollId][2] -= $bundle_qty2;
										$bl_roll_plies = $tmp_bl_arr[$size_id][$rollId][1];
									} else {
										$bundle_qty2 = $tmp_bl_arr[$size_id][$rollId][1];
										$tmp_bl_arr[$size_id][$rollId][1] = 0;
										$tmp_bl_arr[$size_id][$rollId][2] = 0;
										$bl_roll_plies = 0;
									}
									// if($bundle_qty2>0)
									// {
									$tmp_bl_arr[$size_id][$rollId][1] = 0;
									$bundle_prif = $company_sort_name[0] . "-" . $year_id . "-" . $cut_on_prifix;
									$bundle_prif_no = $bundle_prif_no + 1;
									$bundle_no = $bundle_prif . "-" . $bundle_prif_no;
									if ($rmg_no_creation == 6) // chk variable setting
									{
										$erange = ($erange >= 999) ? 0 : $erange;
									}
									if ($rmg_no_creation == 7) // chk variable setting
									{
										$erange = ($erange >= 9999) ? 0 : $erange;
									}
									$srange = $erange + 1;
									$erange = $srange + $bundle_qty2 - 1;
									$tot_bundle_qty += $bundle_qty2;

									if ($rmg_no_creation == 6) // chk variable setting
									{
										$erange = ($erange > 999) ? 999 : $erange;
										$bundle_qty2 = ($erange >= 999) ? 999 - $srange + 1 : $bundle_qty2;
										$tot_bundle_qty += $bundle_qty2;
									}

									if ($rmg_no_creation == 7) // chk variable setting
									{
										$erange = ($erange > 9999) ? 9999 : $erange;
										$bundle_qty2 = ($erange >= 9999) ? 9999 - $srange + 1 : $bundle_qty2;
										$tot_bundle_qty += $bundle_qty2;
									}

									$barcode_suffix_no = $barcode_suffix_no + 1;
									$barcode_no = $year_id . "99" . str_pad($barcode_suffix_no, 10, "0", STR_PAD_LEFT);

									$size_country_array[$size_id][$order_id][$country_id] -= $bundle_qty2;
									$plies -= $bundle_qty2;

									$country_type = $country_type_array[$order_id][$country_id];
									// echo "10**1st string**$bl_size_qty<br>";
									if ($data_array_bundle != "") $data_array_bundle .= ",";
									$data_array_bundle .= "(" . $bundle_id . "," . $mst_id . "," . $dtls_id . "," . $size_id . ",'" . $bundle_prif . "','" . $bundle_prif_no . "','" . $bundle_no . "','" . $year_id . "','" . $barcode_suffix_no . "','" . $barcode_no . "'," . $srange . "," . $erange . "," . $bundle_qty2 . ",'" . $country_type . "'," . $country_id . ",'" . $rollId . "'," . $rollNo . ",'" . $pattern_sec . "','" . $order_id . "',0," . $color_type_id . "," . $user_id . ",'" . $pc_date_time . "')";
									$bundle_id = $bundle_id + 1;
									// $bl_roll_plies=$rollPliesArr[$size_id][$rollId]-($bundle_qty2+$tmp_bl_arr[$size_id][$rollId][2]); 
									// $tmp_bl_arr[$size_id][$rollId][2]=0;
									$sizeRatioBlArr[$size_id][$pattern_no][$rollId] -= $bundle_qty2;

									// echo "$pattern_no=$bundle_no=$bundle_qty2<br>";								

									if ($bundle_qty2 < 1) {
										$bundle_qty_check = false;
									}
									// }
								} else {
									$bl_roll_plies = $rollPliesArr[$size_id][$rollId];
								}

								if ($plies > 0 && $bl_roll_plies > 0 && $bl_size_qty > 0 && $temp_bal_flag == 1) {
									if ($bl_roll_plies >= $bundle_per_pcs) {
										$bundle_per_size = ceil($bl_roll_plies / $bundle_per_pcs);
										for ($z = 1; $z <= $bundle_per_size; $z++) {
											$bl_size_qty = $size_country_array[$size_id][$order_id][$country_id];
											if ($bl_size_qty > 0 && $sizeRatioBlArr[$size_id][$pattern_no][$rollId] > 0) {
												if ($bl_roll_plies > $bundle_per_pcs) {
													$bundle_qty = $bundle_per_pcs;
												} else {
													$bundle_qty = $bl_roll_plies;
												}

												if ($bundle_qty > $bl_size_qty) {
													$bundle_qty = $bl_size_qty;
												}

												if ($bundle_qty > $plies) {
													$bundle_qty = $plies;
												}

												if ($bundle_qty > $sizeRatioBlArr[$size_id][$pattern_no][$rollId]) {
													$bundle_qty = $sizeRatioBlArr[$size_id][$pattern_no][$rollId];
												}
												// echo "10**2nd string**$bl_size_qty<br>";
												if ($bundle_qty > 0) {
													$bl_roll_plies -= $bundle_qty;

													$bundle_prif = $company_sort_name[0] . "-" . $year_id . "-" . $cut_on_prifix;
													$bundle_prif_no = $bundle_prif_no + 1;
													$bundle_no = $bundle_prif . "-" . $bundle_prif_no;
													if ($rmg_no_creation == 6) // chk variable setting
													{
														$erange = ($erange >= 999) ? 0 : $erange;
													}
													if ($rmg_no_creation == 7) // chk variable setting
													{
														$erange = ($erange >= 9999) ? 0 : $erange;
													}
													$srange = $erange + 1;
													$erange = $srange + $bundle_qty - 1;
													$tot_bundle_qty += $bundle_qty;

													if ($rmg_no_creation == 6) // chk variable setting
													{
														$erange = ($erange > 999) ? 999 : $erange;
														$bundle_qty = ($erange >= 999) ? 999 - $srange : $bundle_qty;
														$tot_bundle_qty += $bundle_qty;
														$rest_of_bndl_qty = 0;
														$rest_of_bndl_qty = $bundle_per_pcs - $bundle_qty;
													}

													if ($rmg_no_creation == 7) // chk variable setting
													{
														$erange = ($erange > 9999) ? 9999 : $erange;
														$bundle_qty = ($erange >= 9999) ? 9999 - $srange + 1 : $bundle_qty;
														$tot_bundle_qty += $bundle_qty;
														$rest_of_bndl_qty = 0;
														$rest_of_bndl_qty = $bundle_per_pcs - $bundle_qty;
													}

													$barcode_suffix_no = $barcode_suffix_no + 1;
													$barcode_no = $year_id . "99" . str_pad($barcode_suffix_no, 10, "0", STR_PAD_LEFT);

													$size_country_array[$size_id][$order_id][$country_id] -= $bundle_qty;
													$plies -= $bundle_qty;

													$country_type = $country_type_array[$order_id][$country_id];


													if ($data_array_bundle != "") $data_array_bundle .= ",";
													$data_array_bundle .= "(" . $bundle_id . "," . $mst_id . "," . $dtls_id . "," . $size_id . ",'" . $bundle_prif . "','" . $bundle_prif_no . "','" . $bundle_no . "','" . $year_id . "','" . $barcode_suffix_no . "','" . $barcode_no . "'," . $srange . "," . $erange . "," . $bundle_qty . ",'" . $country_type . "'," . $country_id . ",'" . $rollId . "'," . $rollNo . ",'" . $pattern_sec . "','" . $order_id . "',0," . $color_type_id . "," . $user_id . ",'" . $pc_date_time . "')";
													$bundle_id = $bundle_id + 1;
													$sizeRatioBlArr[$size_id][$pattern_no][$rollId] -= $bundle_qty;

													if ($rest_of_bndl_qty > 0 && $sizeRatioBlArr[$size_id][$pattern_no][$rollId] > 0) {
														// $rest_of_bndl_qty=$rest_of_bndl_qty;	

														// echo "10**bundle qty=$pattern_no=".$rest_of_bndl_qty."=".$sizeRatioBlArr[$size_id][$pattern_no][$rollId]."<br>";
														// $bl_roll_plies-=$rest_of_bndl_qty;

														//$bundle_prif=$company_sort_name[0]."-".$cut_on_prifix;
														$bundle_prif = $company_sort_name[0] . "-" . $year_id . "-" . $cut_on_prifix;
														$bundle_prif_no = $bundle_prif_no + 1;
														$bundle_no = $bundle_prif . "-" . $bundle_prif_no;
														if ($rmg_no_creation == 6) // chk variable setting
														{
															$erange = ($erange >= 999) ? 0 : $erange;
														}
														if ($rmg_no_creation == 7) // chk variable setting
														{
															$erange = ($erange >= 9999) ? 0 : $erange;
														}
														$srange = $erange + 1;
														$erange = $srange + $rest_of_bndl_qty - 1;
														$tot_bundle_qty += $rest_of_bndl_qty;

														if ($rmg_no_creation == 6) // chk variable setting
														{
															$erange = ($erange > 999) ? 999 : $erange;
															// $rest_of_bndl_qty = ($erange>=999) ? 999 - $srange : $rest_of_bndl_qty;
															// $tot_bundle_qty+=$rest_of_bndl_qty;
														}

														if ($rmg_no_creation == 7) // chk variable setting
														{
															$erange = ($erange > 9999) ? 9999 : $erange;
															// $rest_of_bndl_qty = ($erange>=9999) ? 9999 - $srange : $rest_of_bndl_qty;
															// $tot_bundle_qty+=$rest_of_bndl_qty;
														}

														if (empty($previous_barcode_arr[$bundle_no])) {
															$barcode_suffix_no = $barcode_suffix_no + 1;
															$up_barcode_suffix = $barcode_suffix_no;
															$up_barcode_year = $year_id;
															$barcode_no = $year_id . "99" . str_pad($barcode_suffix_no, 10, "0", STR_PAD_LEFT);
														} else {
															$up_barcode_suffix = $previous_barcode_arr[$bundle_no]['prifix'];
															$up_barcode_year = $previous_barcode_arr[$bundle_no]['year'];
															$barcode_no = $previous_barcode_arr[$bundle_no]['barcode'];
														}

														//$bl_size_qty-=$rest_of_bndl_qty;
														$size_country_array[$size_id][$order_id][$country_id] -= $rest_of_bndl_qty;
														$plies -= $rest_of_bndl_qty;

														$country_type = $country_type_array[$order_id][$country_id];

														if ($data_array_bundle != "") $data_array_bundle .= ",";
														$data_array_bundle .= "(" . $bundle_id . "," . $mst_id . "," . $dtls_id . "," . $size_id . ",'" . $bundle_prif . "','" . $bundle_prif_no . "','" . $bundle_no . "','" . $up_barcode_year . "','" . $up_barcode_suffix . "','" . $barcode_no . "'," . $srange . "," . $erange . "," . $rest_of_bndl_qty . ",'" . $country_type . "'," . $country_id . ",'" . $rollId . "'," . $rollNo . ",'" . $pattern_sec . "','" . $order_id . "',0," . $color_type_id . "," . $user_id . ",'" . $pc_date_time . "')";
														$bundle_id = $bundle_id + 1;
														$sizeRatioBlArr[$size_id][$pattern_no][$rollId] -= $rest_of_bndl_qty;
														// echo "10**new bundle<br>";

													}
													// echo "string$bundle_no";die();
													if ($pattern_sequence == 2) {
														// $pattern_sec++;
													}
												}
											}
										}
										// ======== create bundle of balance qty for 999 or 9999 setup ============
										// $bl_size_qty=$size_country_array[$size_id][$order_id][$country_id];
										/* if($sizeRatioBlArr[$size_id][$pattern_no][$rollId]>0)
										{
											// echo "10**bundle qty=$pattern_no=".$bundle_qty=$bl_size_qty."<br>";
											$bundle_qty=$sizeRatioBlArr[$size_id][$pattern_no][$rollId];							

											if($bundle_per_pcs>$sizeRatioBlArr[$size_id][$pattern_no][$rollId])
											{
												// echo "10**bundle qty=$pattern_no=".$bundle_qty."=".$sizeRatioBlArr[$size_id][$pattern_no][$rollId]."<br>";
												$bl_roll_plies-=$bundle_qty;
												
												//$bundle_prif=$company_sort_name[0]."-".$cut_on_prifix;
												$bundle_prif=$company_sort_name[0]."-".$year_id."-".$cut_on_prifix;
												$bundle_prif_no=$bundle_prif_no+1;
												$bundle_no=$bundle_prif."-".$bundle_prif_no;
												if($rmg_no_creation==6) // chk variable setting
												{
													$erange = ($erange>=999) ? 0 : $erange;
												}
												if($rmg_no_creation==7)// chk variable setting
												{
													$erange = ($erange>=9999) ? 0 : $erange;
												}
												$srange=$erange+1;
												$erange=$srange+$bundle_qty-1;
												$tot_bundle_qty+=$bundle_qty;

												if($rmg_no_creation==6)// chk variable setting
												{
													$erange = ($erange>999) ? 999 : $erange;
													$bundle_qty = ($erange>=999) ? 999 - $srange : $bundle_qty;
													$tot_bundle_qty+=$bundle_qty;
												}

												if($rmg_no_creation==7)// chk variable setting
												{
													$erange = ($erange>9999) ? 9999 : $erange;
													$bundle_qty = ($erange>=9999) ? 9999 - $srange : $bundle_qty;
													$tot_bundle_qty+=$bundle_qty;
												}
												
												if(empty($previous_barcode_arr[$bundle_no]))
												{
													$barcode_suffix_no=$barcode_suffix_no+1;
													$up_barcode_suffix=$barcode_suffix_no;
													$up_barcode_year=$year_id;
													$barcode_no=$year_id."99".str_pad($barcode_suffix_no,10,"0",STR_PAD_LEFT);
												}
												else
												{
													$up_barcode_suffix=$previous_barcode_arr[$bundle_no]['prifix'];
													$up_barcode_year=$previous_barcode_arr[$bundle_no]['year'];
													$barcode_no=$previous_barcode_arr[$bundle_no]['barcode'];
												}
												
												//$bl_size_qty-=$bundle_qty;
												$size_country_array[$size_id][$order_id][$country_id]-=$bundle_qty;
												$plies-=$bundle_qty;
												
												$country_type=$country_type_array[$order_id][$country_id];
												
												if($data_array_bundle!="") $data_array_bundle.= ",";
												$data_array_bundle.="(".$bundle_id.",".$mst_id.",".$dtls_id.",".$size_id.",'".$bundle_prif."','".$bundle_prif_no."','".$bundle_no."','".$up_barcode_year."','".$up_barcode_suffix."','".$barcode_no."',".$srange.",".$erange.",".$bundle_qty.",'".$country_type."',".$country_id.",'".$rollId."',".$rollNo.",'".$pattern_sec."','".$order_id."',0,".$color_type_id.",".$user_id.",'".$pc_date_time."')";
												$bundle_id=$bundle_id+1;
												$sizeRatioBlArr[$size_id][$pattern_no][$rollId]-=$bundle_qty;
												// echo "10**".$data_array_bundle;die;
											}
											
										} */
									} else {

										/*if(($bl_roll_plies>$plies) &&($plies<$bl_size_qty) )
										{
											$bundle_qty2=$plies; 
											$bl_roll_plies=$bl_roll_plies-$plies;
										}
										else if($bl_roll_plies>=$bl_size_qty)
										{
											$bundle_qty2=$bl_size_qty; 
											$bl_roll_plies=$bl_roll_plies-$bl_size_qty;
										}
										else 
										{
											$bundle_qty2=$bl_roll_plies; 
											$bl_roll_plies=0;
										}*/

										if ($bl_roll_plies > $plies) {
											$bundle_qty2 = $plies;
											$bl_roll_plies = $bl_roll_plies - $plies;
										} else {
											$bundle_qty2 = $bl_roll_plies;
											$bl_roll_plies = 0;
										}


										// if($bundle_qty2>0)
										// {
										if ($bundle_qty2 > $bl_size_qty) {
											$tmp_bl_arr[$size_id][$rollId][1] = $bundle_qty2 - $bl_size_qty;
											//echo $bundle_qty2."==".$bl_size_qty."==".$country_id;
											$tmp_bl_arr[$size_id][$rollId][2] = $bl_size_qty;
											$bundle_qty2 = $bl_size_qty;
										} else {
											$tmp_bl_arr[$size_id][$rollId][1] = 0;
											$tmp_bl_arr[$size_id][$rollId][2] = 0;
										}
										$bundle_prif = $company_sort_name[0] . "-" . $year_id . "-" . $cut_on_prifix;
										$bundle_prif_no = $bundle_prif_no + 1;
										$bundle_no = $bundle_prif . "-" . $bundle_prif_no;
										if ($rmg_no_creation == 6) // chk variable setting
										{
											$erange = ($erange >= 999) ? 0 : $erange;
										}
										if ($rmg_no_creation == 7) // chk variable setting
										{
											$erange = ($erange >= 9999) ? 0 : $erange;
										}
										$srange = $erange + 1;
										$erange = $srange + $bundle_qty2 - 1;
										$tot_bundle_qty += $bundle_qty2;

										if ($rmg_no_creation == 6) // chk variable setting
										{
											$erange = ($erange > 999) ? 999 : $erange;
											$bundle_qty2 = ($erange >= 999) ? 999 - $srange : $bundle_qty2;
											$tot_bundle_qty += $bundle_qty2;
										}

										if ($rmg_no_creation == 7) // chk variable setting
										{
											$erange = ($erange > 9999) ? 9999 : $erange;
											$bundle_qty2 = ($erange >= 9999) ? 9999 - $srange + 1 : $bundle_qty2;
											$tot_bundle_qty += $bundle_qty2;
										}

										$barcode_suffix_no = $barcode_suffix_no + 1;
										$barcode_no = $year_id . "99" . str_pad($barcode_suffix_no, 10, "0", STR_PAD_LEFT);
										$size_country_array[$size_id][$order_id][$country_id] -= $bundle_qty2;
										$plies -= $bundle_qty2;

										$country_type = $country_type_array[$order_id][$country_id];
										// echo "10**3rd string**$bl_size_qty<br>";
										if ($data_array_bundle != "") $data_array_bundle .= ",";
										$data_array_bundle .= "(" . $bundle_id . "," . $mst_id . "," . $dtls_id . "," . $size_id . ",'" . $bundle_prif . "','" . $bundle_prif_no . "','" . $bundle_no . "','" . $year_id . "','" . $barcode_suffix_no . "','" . $barcode_no . "'," . $srange . "," . $erange . "," . $bundle_qty2 . ",'" . $country_type . "'," . $country_id . ",'" . $rollId . "'," . $rollNo . ",'" . $pattern_sec . "','" . $order_id . "',0," . $color_type_id . "," . $user_id . ",'" . $pc_date_time . "')";
										$bundle_id = $bundle_id + 1;
										$sizeRatioBlArr[$size_id][$pattern_no][$rollId] -= $bundle_qty2;

										if ($bundle_qty2 < 1) {
											$bundle_qty_check = false;
										}

										// }
									}
								}
								if ($pattern_sequence == 2) {
									// $pattern_no++;
									// $pattern_sec++;
								}
							}
						}
					}
				}
				/*if($pattern_sequence==2) 
				{ 
					$pattern_no++;
					$pattern_sec++;
				}
				if($pattern_sequence==1) 
				{*/
				$pattern_no++;
				$pattern_sec++;
				// }
			}
		}
		if (!$bundle_qty_check) {
			echo "10**Something happend wrong.Please check bundle qty.";
			die;
		}
		// echo "10**insert into ppl_cut_lay_bundle($field_array_bundle) values".$data_array_bundle;die;

		//echo "10**insert into ppl_cut_lay_size_dtls($field_array_size)values".$data_array_size; die;
		// echo "10**insert into ppl_cut_lay_size($field_array)values".$data_array;die;
		// echo "10**insert into ppl_cut_lay_bundle($field_array_bundle)values".$data_array_bundle; die;
		$rID = sql_insert("ppl_cut_lay_size", $field_array, $data_array, 0);
		$rID_size = sql_insert("ppl_cut_lay_size_dtls", $field_array_size, $data_array_size, 0);
		//echo "10**insert into ppl_cut_lay_roll_dtls($field_array_roll_dtls) values".$data_array_roll_dtls;die;
		$rID2 = sql_insert("ppl_cut_lay_bundle", $field_array_bundle, $data_array_bundle, 0);
		$rID3 = sql_insert("ppl_cut_lay_roll_dtls", $field_array_roll_dtls, $data_array_roll_dtls, 0);
		$field_array_up = "marker_qty*pcs_per_bundle*updated_by*update_date";
		$data_array_up = $to_marker_qty . "*'" . $txt_bundle_pcs . "'*'" . $user_id . "'*'" . $pc_date_time . "'";
		$rID4 = sql_update("ppl_cut_lay_dtls", $field_array_up, $data_array_up, "id", $dtls_id, 0);
		// echo "10**".$rID."&&".$rID_size."&&".$rID2."&&".$rID3."&&".$rID4;die;

		$total_marker_qty = $total_marker_qty_prev + $tot_marker_qnty_curr;
		$lay_balance = $plan_qty - $total_marker_qty;
		//echo "10**".$seqDatas;die;
		if ($db_type == 0) {
			if ($rID && $rID_size && $rID2 && $rID3 && $rID4) {
				mysql_query("COMMIT");
				echo "0**" . $mst_id . "**" . $dtls_id . "**" . substr($seqDatas, 0, -1) . "**" . $plan_qty . "**" . $total_marker_qty . "**" . $lay_balance;
			} else {
				mysql_query("ROLLBACK");
				echo "10**";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rID_size && $rID2 && $rID3 && $rID4) {
				oci_commit($con);
				echo "0**" . $mst_id . "**" . $dtls_id . "**" . substr($seqDatas, 0, -1) . "**" . $plan_qty . "**" . $total_marker_qty . "**" . $lay_balance;
			} else {
				oci_rollback($con);
				echo "10**";
			}
		}

		disconnect($con);
		die;
	} else if ($operation == 1) // Update Here----------------------------------------------------------
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		$cutting_qc_no = return_field_value("cutting_qc_no", "pro_gmts_cutting_qc_mst", "status_active=1 and is_deleted=0 and cutting_no='" . $cut_no . "'");

		// ================================ get variable setting ======================================
		$variable_setting_for_bundle_creation = return_field_value("smv_source", "variable_settings_production", "status_active=1 and is_deleted=0 and company_name=$cbo_company_id and variable_list=37");
		// echo "10**$variable_setting_for_bundle_creation";die();
		if ($variable_setting_for_bundle_creation == 0) $variable_setting_for_bundle_creation = 1;

		if ($variable_setting_for_bundle_creation == 2) // job wise
		{
			$cutData = sql_select("select job_no_prefix_num, job_no from wo_po_details_master where job_no = '$job_id'");
			$cut_on_prifix = $cutData[0][csf('job_no_prefix_num')];
			$job_no = $cutData[0][csf('job_no')];
		} elseif ($variable_setting_for_bundle_creation == 1) // cutting wise
		{
			$cutData = sql_select("select cut_num_prefix_no, job_no from ppl_cut_lay_mst where cutting_no = '" . $cut_no . "'");
			$cut_on_prifix = $cutData[0][csf('cut_num_prefix_no')];
			$job_no = $cutData[0][csf('job_no')];
		}

		// $cutData=sql_select("select cut_num_prefix_no, job_no from ppl_cut_lay_mst where cutting_no = '".$cut_no."'");
		// $cut_on_prifix=$cutData[0][csf('cut_num_prefix_no')];
		// $job_no=$cutData[0][csf('job_no')];

		/*======================================================================/
		/					Variable Setting for Pattern No Creation 			/
		/======================================================================*/
		$pattern_sequence = return_field_value("batch_maintained", "variable_settings_production", "status_active=1 and is_deleted=0 and company_name=$cbo_company_id and variable_list=155");
		$pattern_sequence = ($pattern_sequence == "") ? 1 : $pattern_sequence;
		// echo "10**".$pattern_sequence;die();

		//echo $cutting_qc_no."jkjkj";die;
		if ($cutting_qc_no != "") {
			echo "200**" . $mst_id . "**" . $dtls_id . "**" . $cutting_qc_no;
			disconnect($con);
			die;
		}

		$previous_barcode_data = sql_select("select bundle_no,barcode_no,barcode_year,barcode_prifix from ppl_cut_lay_bundle where mst_id=" . $mst_id . "  and  dtls_id=" . $dtls_id . " and status_active=1 and is_deleted=0 ");
		foreach ($previous_barcode_data as $b_val) {
			$previous_barcode_arr[$b_val[csf("bundle_no")]]['year'] = $b_val[csf("barcode_year")];
			$previous_barcode_arr[$b_val[csf("bundle_no")]]['prifix'] = $b_val[csf("barcode_prifix")];
			$previous_barcode_arr[$b_val[csf("bundle_no")]]['barcode'] = $b_val[csf("barcode_no")];
		}
		//print_r($previous_barcode_arr);die; 


		$plan_qty = return_field_value("sum(plan_cut_qnty) as plan_qty", "wo_po_color_size_breakdown", "po_break_down_id in(" . $order_id . ") and item_number_id=" . $gmt_id . " and color_number_id in(" . $color_id . " )and status_active=1", "plan_qty");

		$total_marker_qty_prev = return_field_value("sum(b.marker_qty) as mark_qty", "ppl_cut_lay_dtls a, ppl_cut_lay_size b", "a.id=b.dtls_id and a.id!=$dtls_id and  b.order_id in(" . $order_id . ") and a.gmt_item_id=" . $gmt_id . " and a.color_id=" . $color_id . " and a.status_active=1 and b.status_active=1 and b.is_deleted=0", "mark_qty");

		if ($size_wise_repeat_cut_no == 1) {
			$sql_bundle = sql_select("SELECT size_id, max(number_end) as last_rmg FROM ppl_cut_lay_bundle WHERE mst_id='" . $mst_id . "' group by size_id");
			foreach ($sql_bundle as $row) {
				$bundle_no_array[$row[csf('size_id')]] = $row[csf('last_rmg')];
			}
		}

		$sizeRatioArr = array();
		$sizeQtyArr = array();
		$sizeQtyArrForC = array();
		$sizeIdAgainstSeq = array();
		$sizeChkArr = array();
		$manualSizeArr = array();
		$id_size = return_next_id("id", "ppl_cut_lay_size_dtls", 1);
		$field_array_size = "id,mst_id,dtls_id,size_id,size_ratio,marker_qty,bundle_sequence,inserted_by,insert_date";
		for ($i = 1; $i <= $size_row_num; $i++) {
			$hidden_txt_color_ = "hidden_sizef_id_" . $i;
			$txt_layf_balance = "txt_layf_balance_" . $i;
			$txt_sizef_ratio = "txt_sizef_ratio_" . $i;
			$txt_sizef_qty = "txt_sizef_qty_" . $i;
			$bundle_sequence = "txt_bundle_" . $i;
			$sizef_name = "txt_sizef_name_" . $i;
			$manualSizeArr[$$hidden_sizef_id] = $$sizef_name;
			// if($sizeChkArr[str_replace("'",'',$$hidden_sizef_id)]=="")
			// {
			if (str_replace("'", '', $$txt_sizef_qty) > 0) {
				if (str_replace("'", '', $$bundle_sequence) > 0) {
					$seq = str_replace("'", '', $$bundle_sequence);
				} else {
					$max_seq++;
					$seq = $max_seq;
				}

				$dataSize = $mst_id . "," . $dtls_id ."," . $$hidden_sizef_id . "," . $$txt_sizef_ratio . "," . $$txt_sizef_qty . "," . $seq;
				$data_array_up[$seq] = $dataSize;
				$sizeIdAgainstSeq[$seq] = str_replace("'", '', $$hidden_sizef_id);

				$sizeRatioArr[str_replace("'", '', $$hidden_sizef_id)] = str_replace("'", '', $$txt_sizef_ratio);
				$sizeQtyArr[str_replace("'", '', $$hidden_sizef_id)] = str_replace("'", '', $$txt_sizef_qty);
				$sizeQtyArrForC[str_replace("'", '', $$hidden_sizef_id)] = str_replace("'", '', $$txt_sizef_qty);
				$sizeSeqArr[str_replace("'", '', $$hidden_sizef_id)] = $seq;
			}
			// 	$sizeChkArr[str_replace("'",'',$$hidden_sizef_id)]=str_replace("'",'',$$hidden_sizef_id);
			// }
		}
		// echo "10**";	print_r($manualSizeArr);die;
		$seqDatas = '';
		ksort($data_array_up);
		foreach ($data_array_up as $sequence => $data) {
			if ($data_array_size != "") $data_array_size .= ",";
			$data_array_size .= "(" . $id_size . "," . $data . "," . $user_id . ",'" . $pc_date_time . "')";
			$seqDatas .= $id_size . "__" . $sizeIdAgainstSeq[$sequence] . "__" . $sequence . ",";
			$id_size++;
		}

		$roll_size_arr = array();
		$roll_no_arr = array();
		$rollsizeBl = array();
		$rollPliesArr = array();
		$sizeRatioBlArr = array();
		$rollDatas = explode("|", $roll_data);
		$rollDtls_id = return_next_id("id", "ppl_cut_lay_roll_dtls", 1);
		$field_array_roll_dtls = "id,mst_id,dtls_id,roll_id,roll_no,roll_wgt,plies,size_id,size_qty,inserted_by,insert_date";
		foreach ($rollDatas as $data) {
			$datas = explode("=", $data);
			$roll_no = $datas[1];
			$roll_id = $datas[2];
			$roll_wgt = $datas[3];
			$plies = $datas[4];

			if ($roll_id == "" || $roll_id == 0) $roll_id = $rollDtls_id;

			// $roll_no_arr[$roll_id]=$roll_no;
			foreach ($sizeRatioArr as $size_id => $size_ratio) {
				$roll_no_arr[$size_id][$roll_id] = $roll_no;
				$size_qty = $size_ratio * $plies;
				if ($data_array_roll_dtls != "") $data_array_roll_dtls .= ",";
				$data_array_roll_dtls .= "(" . $rollDtls_id . "," . $mst_id . "," . $dtls_id . ",'" . $roll_id . "','" . $roll_no . "','" . $roll_wgt . "','" . $plies . "','" . $size_id . "'," . $size_qty . "," . $user_id . ",'" . $pc_date_time . "')";

				$rollsizeBl[$size_id][$roll_id] = $size_qty;
				$rollPliesArr[$size_id][$roll_id] = $plies;

				$rollDtls_id++;
			}
		}
		// echo "<pre>";print_r($rollPliesArr);die;
		$bundle_no_array = array();
		$last_rmg_no = '';
		$bundle_id = return_next_id("id", "ppl_cut_lay_bundle", 1);
		$field_array_bundle = "id,mst_id,dtls_id,size_id,bundle_num_prefix,bundle_num_prefix_no,bundle_no,barcode_year,barcode_prifix,barcode_no,number_start,number_end,size_qty,country_type,country_id,roll_id,roll_no,pattern_no,order_id,is_excess,color_type_id,inserted_by,insert_date";

		// =================== getting last bundle no ======================
		if ($variable_setting_for_bundle_creation == 2) // job wise
		{
			$bundleNoData = sql_select("select max(b.bundle_num_prefix_no) as last_bundle_no from ppl_cut_lay_mst a, ppl_cut_lay_bundle b where a.id=b.mst_id and a.job_no='$job_id' order by b.id desc");
			$last_bundle_no = $bundleNoData[0][csf('last_bundle_no')];
		} else {
			$bundleNoData = sql_select("select max(number_end) as last_rmg, max(bundle_num_prefix_no) as last_prefix from ppl_cut_lay_bundle where mst_id=$mst_id and dtls_id!=$dtls_id");
			$last_bundle_no = $bundleNoData[0][csf('last_prefix')];
		}

		$bundleData = sql_select("select max(number_end) as last_rmg, max(bundle_num_prefix_no) as last_prefix from ppl_cut_lay_bundle where mst_id=$mst_id and dtls_id!=$dtls_id");
		$last_rmg_no = $bundleData[0][csf('last_rmg')];
		// $last_bundle_no=$bundleData[0][csf('last_prefix')];

		$tot_marker_qnty_curr = 0;
		$bundle_prif_no = $last_bundle_no;
		$size_country_array = array();
		$country_type_array = array();
		//echo "10**"."SELECT size_id, max(number_end) as last_rmg FROM ppl_cut_lay_bundle WHERE mst_id='".$mst_id."' and dtls_id!='".$dtls_id."' group by size_id";die;
		$sizeChkArr = array();
		$id = return_next_id("id", "ppl_cut_lay_size", 1);
		$field_array = "id,mst_id,dtls_id,size_id,size_ratio,marker_qty,country_type,country_id,excess_perc,order_id,manual_size_name,size_wise_repeat,inserted_by,insert_date";
		for ($i = 1; $i <= $row_num; $i++) {
			$txt_size_id = "hidden_size_id_" . $i;
			$txt_lay_balance = "txt_lay_balance_" . $i;
			$cboCountryType = "cboCountryType_" . $i;
			$cboCountry = "cboCountry_" . $i;
			$excess_perc = "txt_excess_" . $i;
			$po_id = "poId_" . $i;
			$txt_size_qty = "txt_size_qty_" . $i;

			$marker_qty = 0;
			$order_id = str_replace("'", '', $$po_id);
			$size_id = str_replace("'", '', $$txt_size_id);
			$lay_balance = str_replace("'", '', $$txt_lay_balance);
			//$size_qty=$sizeQtyArrForC[$size_id];
			$marker_qty = str_replace("'", '', $$txt_size_qty);
			$country_type_array[$order_id][str_replace("'", '', $$cboCountry)] = str_replace("'", '', $$cboCountryType);
			// if($sizeChkArr[$size_id]=="")
			// {
			if ($marker_qty > 0) {
				$size_country_array[$size_id][$order_id][str_replace("'", '', $$cboCountry)] += $marker_qty;
				$tot_marker_qnty_curr += $marker_qty;

				if ($data_array != "") $data_array .= ",";
				$data_array .= "(" . $id . "," . $mst_id . "," . $dtls_id . "," . $$txt_size_id . ",0," . $marker_qty . "," . $$cboCountryType . "," . $$cboCountry . ",'" . $$excess_perc . "'," . $$po_id . ",'" . $manualSizeArr[$$txt_size_id] . "','" . $size_wise_repeat_cut_no . "'," . $user_id . ",'" . $pc_date_time . "')";
				$id = $id + 1;
			}
			// 	$sizeChkArr[$size_id] = $size_id;
			// }
		}

		//echo "10**";
		//print_r($sizeRatioBlArr);die;
		$company_sort_name = explode("-", $cut_no);
		$bundle_per_pcs = str_replace("'", "", $bundle_per_pcs);
		asort($sizeSeqArr);
		//foreach($sizeQtyArr as $size_id=>$size_qty)
		// $pattern_no="A";
		foreach ($sizeSeqArr as $size_id => $size_seq) {
			$size_ratio = $sizeRatioArr[$size_id];
			$size_qty = $sizeQtyArr[$size_id];

			$pattern_no = "A";

			for ($k = 1; $k <= $size_ratio; $k++) {
				foreach ($roll_no_arr[$size_id] as $rollId => $rollNo) {
					$sizeRatioBlArr[$size_id][$pattern_no][$rollId] = $rollPliesArr[$size_id][$rollId];
				}
				$pattern_no++;
			}
		}
		// echo "<pre>";print_r($sizeRatioBlArr);die;
		$erange = 0;
		if ($rmg_no_creation == 2) //cutting wise
		{
			$erange = return_field_value("max(number_end) as last_rmg", "ppl_cut_lay_bundle", "mst_id=$mst_id and dtls_id!=$dtls_id and status_active=1 and is_deleted=0", "last_rmg");
		} else if ($rmg_no_creation == 3) // job wise
		{
			$erange = return_field_value("max(a.number_end) as last_rmg", "ppl_cut_lay_bundle a, ppl_cut_lay_mst b, ppl_cut_lay_dtls c", "a.mst_id=b.id and b.id=c.mst_id and b.entry_form=715 and b.job_no='$job_id' and c.id!=$dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "last_rmg");
		} else if ($rmg_no_creation == 4) {
			$erange = return_field_value("max(a.number_end) as last_rmg", "ppl_cut_lay_bundle a, ppl_cut_lay_dtls b", "a.dtls_id=b.id and a.order_id='$order_id' and b.id!=$dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "last_rmg");
		} else if ($rmg_no_creation == 6) // up to 999
		{
			$max_erange = 999;
			$erange = return_field_value("a.number_end as last_rmg", "ppl_cut_lay_bundle a, ppl_cut_lay_mst b, ppl_cut_lay_dtls c", "a.mst_id=b.id and b.id=c.mst_id and b.entry_form=715 and b.job_no='$job_id' and c.id!=$dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id desc", "last_rmg");
			$erange = ($erange > $max_erange) ? 0 : $erange;
		} else if ($rmg_no_creation == 7) // up to 9999
		{
			$max_erange = 9999;
			$erange = return_field_value("a.number_end as last_rmg", "ppl_cut_lay_bundle a, ppl_cut_lay_mst b, ppl_cut_lay_dtls c", "a.mst_id=b.id and b.id=c.mst_id and b.entry_form=715 and b.job_no='$job_id' and c.id!=$dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by a.id desc", "last_rmg");
			$erange = ($erange > $max_erange) ? 0 : $erange;
		} else if ($rmg_no_creation == 8) // Size wise multi color
		{
			$sql = "SELECT b.size_id, max(b.number_end) as last_rmg FROM ppl_cut_lay_dtls a, ppl_cut_lay_bundle b WHERE a.id = b.dtls_id and a.mst_id=$mst_id and a.id!=$dtls_id group by  b.size_id order by  b.size_id";
			$res = sql_select($sql);
			$erange_arr = array();
			foreach ($res as $val) {
				$erange_arr[$val['SIZE_ID']] = $val['LAST_RMG'];
			}
		}
		// echo "10**$rmg_no_creation=".$erange;die();
		// echo "<pre>";print_r($size_country_array);die;
		//foreach($sizeQtyArr as $size_id=>$size_qty)
		//$year_id=date('Y',time());
		//if (strlen($year_id)==4) $year_id=substr($year_id,2,2);
		$cut_year_ex = explode("-", $cut_no);
		$year_id = $cut_year_ex[1];

		$barcode_suffix_no = return_field_value("max(barcode_prifix) as suffix_no", "ppl_cut_lay_bundle", "barcode_year=$year_id", "suffix_no");

		$pattern_sec = "A";
		$bundle_qty_check = true;
		$v = 1;
		foreach ($sizeSeqArr as $size_id => $size_seq) {
			/*if($size_wise_repeat_cut_no==1)
			{
				$last_rmg_no=$bundle_no_array[$size_id];
				$erange=$last_rmg_no;
			}*/

			$size_ratio = $sizeRatioArr[$size_id];
			$size_qty = $sizeQtyArr[$size_id];

			if ($pattern_sequence == 1) {
				$pattern_no = "A";
				$pattern_sec = "A";
			}
			if ($pattern_sequence == 2) {
				$pattern_no = "A";
			}
			if ($rmg_no_creation == 1) {
				$erange = 0;
			}
			if ($rmg_no_creation == 8) {
				$erange = $erange_arr[$size_id];
			}
			for ($k = 1; $k <= $size_ratio; $k++) {
				// if($size_wise_repeat_cut_no!=1) $erange=0;
				if ($rmg_no_creation == 5) {
					$erange = 0;
				}
				$plies = $txt_plies;
				$tmp_bl_arr = array();
				foreach ($roll_no_arr[$size_id] as $rollId => $rollNo) {
					foreach ($size_country_array[$size_id] as $order_id => $order_data) {
						foreach ($order_data as $country_id => $size_country_qty) {
							if ($sizeRatioBlArr[$size_id][$pattern_no][$rollId] > 0) {
								$temp_bal_flag = 1;
								$bl_size_qty = $size_country_array[$size_id][$order_id][$country_id];
								// echo $plies."=".$tmp_bl_arr[$size_id][$rollId][1]."=".$bl_size_qty.'<br>';
								if ($plies > 0 && $tmp_bl_arr[$size_id][$rollId][1] > 0 && $bl_size_qty > 0) {
									$temp_bal_flag = 0;
									// if($bundle_qty2>0)
									// {
									// $bundle_qty2=$tmp_bl_arr[$size_id][$rollId][1]; 
									// $tmp_bl_arr[$size_id][$rollId][1]=0;
									if ($tmp_bl_arr[$size_id][$rollId][1] > $bl_size_qty) {
										$bundle_qty2 = $bl_size_qty;
										$tmp_bl_arr[$size_id][$rollId][1] -= $bundle_qty2; //echo $bundle_qty2."==".$bl_size_qty."==".$country_id;
										$tmp_bl_arr[$size_id][$rollId][2] -= $bundle_qty2;
										$bl_roll_plies = $tmp_bl_arr[$size_id][$rollId][1];
									} else {
										$bundle_qty2 = $tmp_bl_arr[$size_id][$rollId][1];
										$tmp_bl_arr[$size_id][$rollId][1] = 0;
										$tmp_bl_arr[$size_id][$rollId][2] = 0;
										$bl_roll_plies = 0;
									}

									$bundle_prif = $company_sort_name[0] . "-" . $year_id . "-" . $cut_on_prifix;
									$bundle_prif_no = $bundle_prif_no + 1;
									$bundle_no = $bundle_prif . "-" . $bundle_prif_no;
									if ($rmg_no_creation == 6) // chk variable setting
									{
										$erange = ($erange >= 999) ? 0 : $erange;
									}
									if ($rmg_no_creation == 7) // chk variable setting
									{
										$erange = ($erange >= 9999) ? 0 : $erange;
									}
									$srange = $erange + 1;
									$erange = $srange + $bundle_qty2 - 1;
									$tot_bundle_qty += $bundle_qty2;

									if ($rmg_no_creation == 6) // chk variable setting
									{
										$erange = ($erange > 999) ? 999 : $erange;
										$bundle_qty2 = ($erange >= 999) ? 999 - $srange + 1 : $bundle_qty2;
										$tot_bundle_qty += $bundle_qty2;
									}

									if ($rmg_no_creation == 7) // chk variable setting
									{
										$erange = ($erange > 9999) ? 9999 : $erange;
										$bundle_qty2 = ($erange >= 9999) ? 9999 - $srange + 1 : $bundle_qty2;
										$tot_bundle_qty += $bundle_qty2;
									}

									if (empty($previous_barcode_arr[$bundle_no])) {
										$barcode_suffix_no = $barcode_suffix_no + 1;
										$up_barcode_suffix = $barcode_suffix_no;
										$up_barcode_year = $year_id;
										$barcode_no = $year_id . "99" . str_pad($barcode_suffix_no, 10, "0", STR_PAD_LEFT);
									} else {
										$up_barcode_suffix = $previous_barcode_arr[$bundle_no]['prifix'];
										$up_barcode_year = $previous_barcode_arr[$bundle_no]['year'];
										$barcode_no = $previous_barcode_arr[$bundle_no]['barcode'];
									}
									//$bl_size_qty-=$bundle_qty2;
									$size_country_array[$size_id][$order_id][$country_id] -= $bundle_qty2;
									$plies -= $bundle_qty2;
									// echo $bundle_no."--".$bundle_qty2.'<br>';
									$country_type = $country_type_array[$order_id][$country_id];

									if ($data_array_bundle != "") $data_array_bundle .= ",";
									$data_array_bundle .= "(" . $bundle_id . "," . $mst_id . "," . $dtls_id . "," . $size_id . ",'" . $bundle_prif . "','" . $bundle_prif_no . "','" . $bundle_no . "','" . $up_barcode_year . "','" . $up_barcode_suffix . "','" . $barcode_no . "'," . $srange . "," . $erange . "," . $bundle_qty2 . ",'" . $country_type . "'," . $country_id . ",'" . $rollId . "'," . $rollNo . ",'" . $pattern_sec . "','" . $order_id . "',0," . $color_type_id . "," . $user_id . ",'" . $pc_date_time . "')";
									$bundle_id = $bundle_id + 1;
									// $bl_roll_plies=$rollPliesArr[$size_id][$rollId]-($bundle_qty2+$tmp_bl_arr[$size_id][$rollId][2]); $tmp_bl_arr[$size_id][$rollId][2]=0;
									$sizeRatioBlArr[$size_id][$pattern_no][$rollId] -= $bundle_qty2;

									if ($bundle_qty2 < 1) {
										$bundle_qty_check = false;
									}
									// }

								} else {
									$bl_roll_plies = $rollPliesArr[$size_id][$rollId];
								}
								// echo $bl_roll_plies."**".$bl_size_qty."**".$plies."pp<br>";
								//$bl_size_qty=$size_country_array[$size_id][$country_id];
								if ($plies > 0 && $bl_roll_plies > 0 && $bl_size_qty > 0 && $temp_bal_flag == 1) {
									// echo "10**".$bl_roll_plies.">=".$bundle_per_pcs."==".$bundle_prif_no."<br>";
									if ($bl_roll_plies >= $bundle_per_pcs) {
										$bundle_per_size = ceil($bl_roll_plies / $bundle_per_pcs);
										// echo $bundle_per_size."=dd";
										for ($z = 1; $z <= $bundle_per_size; $z++) {
											$bl_size_qty = $size_country_array[$size_id][$order_id][$country_id];
											if ($bl_size_qty > 0 && $sizeRatioBlArr[$size_id][$pattern_no][$rollId] > 0) {
												if ($bl_roll_plies > $bundle_per_pcs) {
													$bundle_qty = $bundle_per_pcs;
												} else {
													$bundle_qty = $bl_roll_plies;
												}

												if ($bundle_qty > $bl_size_qty) {
													$bundle_qty = $bl_size_qty;
												}

												if ($bundle_qty > $plies) {
													$bundle_qty = $plies;
												}

												if ($bundle_qty > $sizeRatioBlArr[$size_id][$pattern_no][$rollId]) {
													$bundle_qty = $sizeRatioBlArr[$size_id][$pattern_no][$rollId];
												}


												if ($bundle_qty > 0) {
													$bl_roll_plies -= $bundle_qty;

													//$bundle_prif=$company_sort_name[0]."-".$cut_on_prifix;
													$bundle_prif = $company_sort_name[0] . "-" . $year_id . "-" . $cut_on_prifix;
													$bundle_prif_no = $bundle_prif_no + 1;
													$bundle_no = $bundle_prif . "-" . $bundle_prif_no;
													if ($rmg_no_creation == 6) // chk variable setting
													{
														$erange = ($erange >= 999) ? 0 : $erange;
													}
													if ($rmg_no_creation == 7) // chk variable setting
													{
														$erange = ($erange >= 9999) ? 0 : $erange;
													}
													$srange = $erange + 1;
													$erange = $srange + $bundle_qty - 1;
													$tot_bundle_qty += $bundle_qty;

													if ($rmg_no_creation == 6) // chk variable setting
													{
														$erange = ($erange > 999) ? 999 : $erange;
														$bundle_qty = ($erange >= 999) ? 999 - $srange : $bundle_qty;
														$tot_bundle_qty += $bundle_qty;
														$rest_of_bndl_qty = 0;
														$rest_of_bndl_qty = $bundle_per_pcs - $bundle_qty;
													}

													if ($rmg_no_creation == 7) // chk variable setting
													{
														$erange = ($erange > 9999) ? 9999 : $erange;
														$bundle_qty = ($erange >= 9999) ? 9999 - $srange + 1 : $bundle_qty;
														$tot_bundle_qty += $bundle_qty;
														$rest_of_bndl_qty = 0;
														$rest_of_bndl_qty = $bundle_per_pcs - $bundle_qty;
														// echo "10** $bundle_no= rest_of_bndl_qty=".$rest_of_bndl_qty."==".$bundle_per_pcs ."-". $bundle_qty."<br>";
													}

													if (empty($previous_barcode_arr[$bundle_no])) {
														$barcode_suffix_no = $barcode_suffix_no + 1;
														$up_barcode_suffix = $barcode_suffix_no;
														$up_barcode_year = $year_id;
														$barcode_no = $year_id . "99" . str_pad($barcode_suffix_no, 10, "0", STR_PAD_LEFT);
													} else {
														$up_barcode_suffix = $previous_barcode_arr[$bundle_no]['prifix'];
														$up_barcode_year = $previous_barcode_arr[$bundle_no]['year'];
														$barcode_no = $previous_barcode_arr[$bundle_no]['barcode'];
													}

													//$bl_size_qty-=$bundle_qty;
													$size_country_array[$size_id][$order_id][$country_id] -= $bundle_qty;
													$plies -= $bundle_qty;

													$country_type = $country_type_array[$order_id][$country_id];

													if ($data_array_bundle != "") $data_array_bundle .= ",";
													$data_array_bundle .= "(" . $bundle_id . "," . $mst_id . "," . $dtls_id . "," . $size_id . ",'" . $bundle_prif . "','" . $bundle_prif_no . "','" . $bundle_no . "','" . $up_barcode_year . "','" . $up_barcode_suffix . "','" . $barcode_no . "'," . $srange . "," . $erange . "," . $bundle_qty . ",'" . $country_type . "'," . $country_id . ",'" . $rollId . "'," . $rollNo . ",'" . $pattern_sec . "','" . $order_id . "',0," . $color_type_id . "," . $user_id . ",'" . $pc_date_time . "')";
													$bundle_id = $bundle_id + 1;
													$sizeRatioBlArr[$size_id][$pattern_no][$rollId] -= $bundle_qty;
													// echo "10**$bundle_no $bundle_qty <br>";

													if ($rest_of_bndl_qty > 0 && $sizeRatioBlArr[$size_id][$pattern_no][$rollId] > 0) {
														// $rest_of_bndl_qty=$rest_of_bndl_qty;	

														// echo "10**bundle qty=$pattern_no=".$rest_of_bndl_qty."=".$sizeRatioBlArr[$size_id][$pattern_no][$rollId]."<br>";
														// $bl_roll_plies-=$rest_of_bndl_qty;

														//$bundle_prif=$company_sort_name[0]."-".$cut_on_prifix;
														$bundle_prif = $company_sort_name[0] . "-" . $year_id . "-" . $cut_on_prifix;
														$bundle_prif_no = $bundle_prif_no + 1;
														$bundle_no = $bundle_prif . "-" . $bundle_prif_no;
														// echo $bundle_no."<br>";
														if ($rmg_no_creation == 6) // chk variable setting
														{
															$erange = ($erange >= 999) ? 0 : $erange;
														}
														if ($rmg_no_creation == 7) // chk variable setting
														{
															$erange = ($erange >= 9999) ? 0 : $erange;
														}
														$srange = $erange + 1;
														$erange = $srange + $rest_of_bndl_qty - 1;
														$tot_bundle_qty += $rest_of_bndl_qty;

														if ($rmg_no_creation == 6) // chk variable setting
														{
															$erange = ($erange > 999) ? 999 : $erange;
															// $rest_of_bndl_qty = ($erange>=999) ? 999 - $srange : $rest_of_bndl_qty;
															// $tot_bundle_qty+=$rest_of_bndl_qty;
														}

														if ($rmg_no_creation == 7) // chk variable setting
														{
															$erange = ($erange > 9999) ? 9999 : $erange;
															// $rest_of_bndl_qty = ($erange>=9999) ? 9999 - $srange : $rest_of_bndl_qty;
															// $tot_bundle_qty+=$rest_of_bndl_qty;
														}

														if (empty($previous_barcode_arr[$bundle_no])) {
															$barcode_suffix_no = $barcode_suffix_no + 1;
															$up_barcode_suffix = $barcode_suffix_no;
															$up_barcode_year = $year_id;
															$barcode_no = $year_id . "99" . str_pad($barcode_suffix_no, 10, "0", STR_PAD_LEFT);
														} else {
															$up_barcode_suffix = $previous_barcode_arr[$bundle_no]['prifix'];
															$up_barcode_year = $previous_barcode_arr[$bundle_no]['year'];
															$barcode_no = $previous_barcode_arr[$bundle_no]['barcode'];
														}

														//$bl_size_qty-=$rest_of_bndl_qty;
														$size_country_array[$size_id][$order_id][$country_id] -= $rest_of_bndl_qty;
														$plies -= $rest_of_bndl_qty;

														$country_type = $country_type_array[$order_id][$country_id];

														if ($data_array_bundle != "") $data_array_bundle .= ",";
														$data_array_bundle .= "(" . $bundle_id . "," . $mst_id . "," . $dtls_id . "," . $size_id . ",'" . $bundle_prif . "','" . $bundle_prif_no . "','" . $bundle_no . "','" . $up_barcode_year . "','" . $up_barcode_suffix . "','" . $barcode_no . "'," . $srange . "," . $erange . "," . $rest_of_bndl_qty . ",'" . $country_type . "'," . $country_id . ",'" . $rollId . "'," . $rollNo . ",'" . $pattern_sec . "','" . $order_id . "',0," . $color_type_id . "," . $user_id . ",'" . $pc_date_time . "')";
														$bundle_id = $bundle_id + 1;
														$sizeRatioBlArr[$size_id][$pattern_no][$rollId] -= $rest_of_bndl_qty;
														// echo "10**$bundle_no $rest_of_bndl_qty <br>";

													}
												}
											}
										}
										// ======== create bundle of balance qty for 999 or 9999 setup ============
										// $bl_size_qty=$size_country_array[$size_id][$order_id][$country_id];
										/* if($sizeRatioBlArr[$size_id][$pattern_no][$rollId]>0)
										{
											// echo "10**bundle qty=$pattern_no=".$bundle_qty=$bl_size_qty."<br>";
											$bundle_qty=$sizeRatioBlArr[$size_id][$pattern_no][$rollId];							

											if($bundle_per_pcs>$sizeRatioBlArr[$size_id][$pattern_no][$rollId])
											{
												// echo "10**bundle qty=$pattern_no=".$bundle_qty."=".$sizeRatioBlArr[$size_id][$pattern_no][$rollId]."<br>";
												$bl_roll_plies-=$bundle_qty;
												
												//$bundle_prif=$company_sort_name[0]."-".$cut_on_prifix;
												$bundle_prif=$company_sort_name[0]."-".$year_id."-".$cut_on_prifix;
												$bundle_prif_no=$bundle_prif_no+1;
												$bundle_no=$bundle_prif."-".$bundle_prif_no;
												if($rmg_no_creation==6) // chk variable setting
												{
													$erange = ($erange>=999) ? 0 : $erange;
												}
												if($rmg_no_creation==7)// chk variable setting
												{
													$erange = ($erange>=9999) ? 0 : $erange;
												}
												$srange=$erange+1;
												$erange=$srange+$bundle_qty-1;
												$tot_bundle_qty+=$bundle_qty;

												if($rmg_no_creation==6)// chk variable setting
												{
													$erange = ($erange>999) ? 999 : $erange;
													$bundle_qty = ($erange>=999) ? 999 - $srange : $bundle_qty;
													$tot_bundle_qty+=$bundle_qty;
												}

												if($rmg_no_creation==7)// chk variable setting
												{
													$erange = ($erange>9999) ? 9999 : $erange;
													$bundle_qty = ($erange>=9999) ? 9999 - $srange : $bundle_qty;
													$tot_bundle_qty+=$bundle_qty;
												}
												
												if(empty($previous_barcode_arr[$bundle_no]))
												{
													$barcode_suffix_no=$barcode_suffix_no+1;
													$up_barcode_suffix=$barcode_suffix_no;
													$up_barcode_year=$year_id;
													$barcode_no=$year_id."99".str_pad($barcode_suffix_no,10,"0",STR_PAD_LEFT);
												}
												else
												{
													$up_barcode_suffix=$previous_barcode_arr[$bundle_no]['prifix'];
													$up_barcode_year=$previous_barcode_arr[$bundle_no]['year'];
													$barcode_no=$previous_barcode_arr[$bundle_no]['barcode'];
												}
												
												//$bl_size_qty-=$bundle_qty;
												$size_country_array[$size_id][$order_id][$country_id]-=$bundle_qty;
												$plies-=$bundle_qty;
												
												$country_type=$country_type_array[$order_id][$country_id];
												
												if($data_array_bundle!="") $data_array_bundle.= ",";
												$data_array_bundle.="(".$bundle_id.",".$mst_id.",".$dtls_id.",".$size_id.",'".$bundle_prif."','".$bundle_prif_no."','".$bundle_no."','".$up_barcode_year."','".$up_barcode_suffix."','".$barcode_no."',".$srange.",".$erange.",".$bundle_qty.",'".$country_type."',".$country_id.",'".$rollId."',".$rollNo.",'".$pattern_sec."','".$order_id."',0,".$color_type_id.",".$user_id.",'".$pc_date_time."')";
												$bundle_id=$bundle_id+1;
												$sizeRatioBlArr[$size_id][$pattern_no][$rollId]-=$bundle_qty;
												// echo "10**".$data_array_bundle;die;
											}
											
										} */
									} else {
										/*if(($bl_roll_plies>$plies) &&($plies<$bl_size_qty) )
										{
											$bundle_qty2=$plies; 
											$bl_roll_plies=$bl_roll_plies-$plies;
										}
										else if($bl_roll_plies>=$bl_size_qty)
										{
											$bundle_qty2=$bl_size_qty; 
											$bl_roll_plies=$bl_roll_plies-$bl_size_qty;
										}
										else 
										{
											$bundle_qty2=$bl_roll_plies; 
											$bl_roll_plies=0;
										}*/

										if ($bl_roll_plies > $plies) {
											$bundle_qty2 = $plies;
											$bl_roll_plies = $bl_roll_plies - $plies;
											//echo $bundle_no."=".$bundle_qty2."=".$bl_size_qty."=".$bl_roll_plies.'<br>';
										} else {
											$bl_roll_plies2 = 0;
											$bl_roll_plies2 = $bl_roll_plies;
											$bundle_qty2 = $bl_roll_plies;
											$bl_roll_plies = 0;
										}

										if ($bundle_qty2 > 0) {
											if ($bundle_qty2 > $bl_size_qty) {
												$tmp_bl_arr[$size_id][$rollId][1] = $bundle_qty2 - $bl_size_qty;
												// echo $bundle_qty2."==".$bl_size_qty."==".$country_id."fff<br>";
												$tmp_bl_arr[$size_id][$rollId][2] = $bl_size_qty;
												$bundle_qty2 = $bl_size_qty;
											} else {
												$tmp_bl_arr[$size_id][$rollId][1] = 0;
												$tmp_bl_arr[$size_id][$rollId][2] = 0;
											}

											$bundle_prif = $company_sort_name[0] . "-" . $year_id . "-" . $cut_on_prifix;
											$bundle_prif_no = $bundle_prif_no + 1;
											$bundle_no = $bundle_prif . "-" . $bundle_prif_no;
											if ($rmg_no_creation == 6) // chk variable setting
											{
												$erange = ($erange >= 999) ? 0 : $erange;
											}
											if ($rmg_no_creation == 7) // chk variable setting
											{
												$erange = ($erange >= 9999) ? 0 : $erange;
											}
											$srange = $erange + 1;
											$erange = $srange + $bundle_qty2 - 1;
											$tot_bundle_qty += $bundle_qty2;

											if ($rmg_no_creation == 6) // chk variable setting
											{
												$erange = ($erange > 999) ? 999 : $erange;
												$bundle_qty2 = ($erange >= 999) ? 999 - $srange : $bundle_qty2;
												$tot_bundle_qty += $bundle_qty2;
											}

											if ($rmg_no_creation == 7) // chk variable setting
											{
												$erange = ($erange > 9999) ? 9999 : $erange;
												$bundle_qty2 = ($erange >= 9999) ? 9999 - $srange + 1 : $bundle_qty2;
												$tot_bundle_qty += $bundle_qty2;
											}

											if (empty($previous_barcode_arr[$bundle_no])) {
												$barcode_suffix_no = $barcode_suffix_no + 1;
												$up_barcode_suffix = $barcode_suffix_no;
												$up_barcode_year = $year_id;
												$barcode_no = $year_id . "99" . str_pad($barcode_suffix_no, 10, "0", STR_PAD_LEFT);
											} else {
												$up_barcode_suffix = $previous_barcode_arr[$bundle_no]['prifix'];
												$up_barcode_year = $previous_barcode_arr[$bundle_no]['year'];
												$barcode_no = $previous_barcode_arr[$bundle_no]['barcode'];
											}

											//$bl_size_qty-=$bundle_qty2;
											$size_country_array[$size_id][$order_id][$country_id] -= $bundle_qty2;
											$plies -= $bundle_qty2;

											$country_type = $country_type_array[$order_id][$country_id];

											if ($data_array_bundle != "") $data_array_bundle .= ",";
											$data_array_bundle .= "(" . $bundle_id . "," . $mst_id . "," . $dtls_id . "," . $size_id . ",'" . $bundle_prif . "','" . $bundle_prif_no . "','" . $bundle_no . "','" . $up_barcode_year . "','" . $up_barcode_suffix . "','" . $barcode_no . "'," . $srange . "," . $erange . "," . $bundle_qty2 . ",'" . $country_type . "'," . $country_id . ",'" . $rollId . "'," . $rollNo . ",'" . $pattern_sec . "','" . $order_id . "',0," . $color_type_id . "," . $user_id . ",'" . $pc_date_time . "')";
											$bundle_id = $bundle_id + 1;
											$sizeRatioBlArr[$size_id][$pattern_no][$rollId] -= $bundle_qty2;
											//echo $rollNo.",".$srange.",".$erange;

											if ($bundle_qty2 < 1) {
												$bundle_qty_check = false;
											}
										}
									}
								}
							}
						}
					}
				}
				$pattern_no++;
				$pattern_sec++;
			}
		}

		if (!$bundle_qty_check) {
			echo "10**Something happend wrong.Please check bundle qty.";
			die;
		}
		// die("10**");
		// echo "10**".$data_array_bundle;die;
		//die;
		// echo "10**<pre>";print_r($sizeRatioBlArr);die;
		// echo "10**insert into ppl_cut_lay_bundle($field_array_bundle)values".$data_array_bundle;die;
		//echo "10**insert into ppl_cut_lay_size($field_array)values".$data_array;die;
		//die;
		$delete = execute_query("delete from ppl_cut_lay_size where mst_id=" . $mst_id . " and dtls_id=" . $dtls_id . "", 0);
		$delete_size = execute_query("delete from ppl_cut_lay_size_dtls where mst_id=" . $mst_id . " and dtls_id=" . $dtls_id . "", 0);
		$delete_bundle = execute_query("delete from ppl_cut_lay_bundle where mst_id=" . $mst_id . " and dtls_id=" . $dtls_id . "", 0);
		$delete_roll = execute_query("delete from ppl_cut_lay_roll_dtls where mst_id=" . $mst_id . " and dtls_id=" . $dtls_id . "", 0);

		$rID = sql_insert("ppl_cut_lay_size", $field_array, $data_array, 0);
		$rID_size = sql_insert("ppl_cut_lay_size_dtls", $field_array_size, $data_array_size, 0);
		$rID2 = sql_insert("ppl_cut_lay_bundle", $field_array_bundle, $data_array_bundle, 0);
		//echo "10**insert into ppl_cut_lay_bundle($field_array_bundle) values".$data_array_bundle."**".$rID2;die;
		$rID3 = sql_insert("ppl_cut_lay_roll_dtls", $field_array_roll_dtls, $data_array_roll_dtls, 0);
		$field_array_up = "marker_qty*pcs_per_bundle*updated_by*update_date";
		$data_array_up = "" . $to_marker_qty . "*'" . $txt_bundle_pcs . "'*'" . $user_id . "'*'" . $pc_date_time . "'";
		$rID4 = sql_update("ppl_cut_lay_dtls", $field_array_up, $data_array_up, "id", $dtls_id, 0);

		// echo "10**".$rID ."**". $rID_size ."**". $rID2 ."**". $rID3 ."**". $rID4 ."**". $delete ."**". $delete_size."**".$delete_bundle."**".$delete_roll;die;	

		$total_marker_qty = $total_marker_qty_prev + $tot_marker_qnty_curr;
		$lay_balance = $plan_qty - $total_marker_qty;
		//echo "10**".$lay_balance."**".$total_marker_qty."**".$total_marker_qty_prev."**".$tot_marker_qnty_curr;die;

		if ($db_type == 0) {
			if ($rID && $rID_size && $rID2 && $rID3 && $rID4 && $delete && $delete_size && $delete_bundle && $delete_roll) {
				mysql_query("COMMIT");
				echo "1**" . $mst_id . "**" . $dtls_id . "**" . substr($seqDatas, 0, -1) . "**" . $plan_qty . "**" . $total_marker_qty . "**" . $lay_balance;
			} else {
				mysql_query("ROLLBACK");
				echo "10**";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rID_size && $rID2 && $rID3 && $rID4 && $delete && $delete_size && $delete_bundle && $delete_roll) {
				oci_commit($con);
				echo "1**" . $mst_id . "**" . $dtls_id . "**" . substr($seqDatas, 0, -1) . "**" . $plan_qty . "**" . $total_marker_qty . "**" . $lay_balance;
			} else {
				oci_rollback($con);
				echo "10**";
			}
		}

		disconnect($con);
		die;
	} else if ($operation == 2) // Delete Here----------------------------------------------------------
	{
		exit();
	}
}

if ($action == "show_bundle_list_view") {
	$ex_data = explode("**", $data);
	$mst_id = $ex_data[0];
	$dtls_id = $ex_data[1];

	$country_arr = return_library_array("select id, country_name from lib_country", 'id', 'country_name');
	$po_country_array = array();
	$sql_query = sql_select("select distinct a.country_id as country_id from wo_po_color_size_breakdown a, ppl_cut_lay_dtls b, ppl_cut_lay_size c where a.item_number_id=b.gmt_item_id and a.po_break_down_id=c.order_id and b.id=c.dtls_id and a.color_number_id=b.color_id and b.mst_id=$mst_id and b.id=$dtls_id and a.status_active=1 and a.is_deleted=0");
	$size_details = array();
	$sizeId_arr = array();
	$shipDate_arr = array();
	foreach ($sql_query as $row) {
		$po_country_array[$row[csf('country_id')]] = $country_arr[$row[csf('country_id')]];
	}

	$po_no_arr = return_library_array("select a.id, a.po_number from wo_po_break_down a, ppl_cut_lay_size b where a.id=b.order_id and b.mst_id=$mst_id and b.dtls_id=$dtls_id and b.status_active=1 and b.is_deleted=0 group by a.id, a.po_number", 'id', 'po_number');

 ?>
	<fieldset style="width:960px">
		<legend>Bundle No and RMG qty details</legend>
		<table cellpadding="0" cellspacing="0" width="950" rules="all" border="1" class="rpt_table" id="tbl_bundle_list_save">
			<thead class="form_table_header">
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th colspan="2">RMG Number</th>
				<th>
					<input type="hidden" id="hidden_mst_id" name="hidden_mst_id" value="<? echo $mst_id; ?>" />
					<input type="hidden" id="hidden_detls_id" name="hidden_detls_id" value="<? echo $dtls_id; ?>" />
				</th>
				<th>Report &nbsp;</th>
			</thead>
			<thead class="form_table_header">
				<th>SL No</th>
				<th>Order No.</th>
				<th>Country Type</th>
				<th>Country Name</th>
				<th>Size</th>
				<th>Pattern</th>
				<th>Roll No</th>
				<th>Bundle No</th>
				<th>Quantity</th>
				<th>From</th>
				<th>To</th>
				<th></th>
				<th width="40"><input type="checkbox" name="check_all" id="check_all" onClick="check_all_report()"></th>
			</thead>
			<tbody id="trBundleListSave">
				<?
				$sql_size_name = sql_select("select size_id from ppl_cut_lay_size where mst_id=" . $mst_id . " and dtls_id=" . $dtls_id . "");
				$size_colour_arr = array();
				foreach ($sql_size_name as $asf) {
					$size_colour_arr[$asf[csf("size_id")]] = $size_arr[$asf[csf("size_id")]];
				}

				$bundle_data = sql_select("select a.id,a.bundle_no,a.size_id,a.number_start,a.number_end,a.size_qty,a.update_flag,a.update_value, a.country_type, a.country_id, a.roll_no, a.roll_id, a.pattern_no, a.is_excess,a.barcode_no, a.order_id from ppl_cut_lay_bundle a where a.mst_id=" . $mst_id . " and a.dtls_id=" . $dtls_id . " order by a.id ASC");
				$i = 1;
				foreach ($bundle_data as $row) {
					$update_f_value = "";
					if (str_replace("'", "", $row[csf('update_flag')]) == 1) {
						$update_f_value = explode("**", $row[csf('update_value')]);
					}
				?>
					<tr id="trBundleListSave_<? echo $i;  ?>">
						<td align="center" id="">
							<input type="text" id="sirialNo_<? echo $i;  ?>" name="sirialNo[]" style="width:25px;" class="text_boxes" value="<? echo $i;  ?>" disabled />
							<input type="hidden" id="hiddenExtraTr_<? echo $i;  ?>" name="hiddenExtraTr[]" value="<? echo $i;  ?>" />
							<input type="hidden" id="hiddenUpdateFlag_<? echo $i; ?>" name="hiddenUpdateFlag[]" value="<? echo $row[csf('update_flag')]; ?> " />
							<input type="hidden" id="hiddenUpdateValue_<? echo $i; ?>" name="hiddenUpdateValue[]" value="<? echo $row[csf('update_value')]; ?> " />
						</td>
						<td align="center">
							<?
							echo create_drop_down("cboPoId_" . $i, 130, $po_no_arr, '', 0, '', $row[csf('order_id')], '', 1, '', '', '', '', '', '', 'cboPoId[]');
							?>
						</td>
						<td align="center">
							<?
							echo create_drop_down("cboCountryTypeB_" . $i, 70, $country_type, '', 0, '', $row[csf('country_type')], '', 1);
							?>
							<input type="hidden" id="hiddenCountryTypeB_<? echo $i;  ?>" name="hiddenCountryTypeB[]" value="<? echo $row[csf('country_type')]; ?> " />
						</td>
						<td align="center">
							<?
							echo create_drop_down("cboCountryB_" . $i, 80, $po_country_array, '', 1, '', $row[csf('country_id')], '', 1, '', '', '', '', '', '', 'cboCountryB[]');
							?>
							<input type="hidden" id="hiddenCountryB_<? echo $i;  ?>" name="hiddenCountryB[]" value="<? echo $row[csf('country_id')]; ?> " />
						</td>
						<td align="center" id="update_sizename_<? echo $i;  ?>">
							<select name="sizeName[]" id="sizeName_<? echo $i;  ?>" class="text_boxes" style="width:60px; text-align:center;  <? if ($update_f_value[1] != "") echo "background-color:#F3F;"; ?> " disabled>
								<?
								// $l=1;
								foreach ($sql_size_name as $asf) {
									if ($asf[csf("size_id")] == $row[csf('size_id')]) $select_text = "selected";
									else $select_text = "";
								?>
									<option value="<? echo $asf[csf("size_id")]; ?> " <? echo $select_text;  ?>><? echo $size_arr[$asf[csf("size_id")]]; ?> </option>
								<?
								}
								?>
							</select>
							<input type="hidden" name="hiddenSizeId[]" id="hiddenSizeId_<? echo $i;  ?>" value="<? echo $row[csf('size_id')];  ?>" />
						</td>
						<td align="center"><input type="text" name="patternNo[]" id="patternNo_<? echo $i; ?>" value="<? echo $row[csf('pattern_no')]; ?>" class="text_boxes" style="width:35px; text-align:center" disabled /><input type="hidden" name="isExcess[]" id="isExcess_<? echo $i; ?>" value="<? echo $row[csf('is_excess')]; ?>" /></td>
						<td align="center">
							<input type="text" name="rollNo[]" id="rollNo_<? echo $i;  ?>" value="<? echo $row[csf('roll_no')];  ?>" class="text_boxes" style="width:40px;  text-align:center" disabled />
							<input type="hidden" name="rollId[]" id="rollId_<? echo $i; ?>" value="<? echo $row[csf('roll_id')]; ?>" />
						</td>
						<td align="center">
							<input type="text" name="bundleNo[]" id="bundleNo_<? echo $i;  ?>" value="<? echo $row[csf('bundle_no')];  ?>" class="text_boxes" style="width:120px;  text-align:center" disabled title="<?php echo $row[csf('barcode_no')]; ?>" />
						</td>
						<td align="center">
							<input type="text" name="bundleSizeQty[]" id="bundleSizeQty_<? echo $i;  ?>" onKeyUp="bundle_calclution(<? echo $i;  ?>)" value="<? echo $row[csf('size_qty')];  ?>" style="width:40px; text-align:right; <? if ($update_f_value[0] != "") echo "background-color:#F3F;"; ?>" class="text_boxes" disabled />
							<input type="hidden" name="hiddenSizeQty[]" id="hiddenSizeQty_<? echo $i;  ?>" value="<? echo $row[csf('size_qty')];  ?>" disabled />
						</td>
						<td align="center">
							<input type="text" name="rmgNoStart[]" id="rmgNoStart_<? echo $i;  ?>" value="<? echo $row[csf('number_start')];  ?>" style="width:40px; text-align:right" class="text_boxes" disabled />
						</td>
						<td align="center">
							<input type="text" name="rmgNoEnd[]" id="rmgNoEnd_<? echo $i;  ?>" value="<? echo $row[csf('number_end')];  ?>" style="width:40px; text-align:right" class="text_boxes" disabled />
						</td>
						<td align="center">
							<input type="button" value="+" name="addButton[]" id="addButton_<? echo $i;  ?>" style="width:30px" class="formbuttonplasminus" onClick="fnc_addRow('<? echo $i;  ?>','<? echo $i;  ?>')" />
							<input type="button" value="Adj." name="rowUpdate[]" id="rowUpdate_<? echo $i;  ?>" style="width:40px;" class="formbuttonplasminus" onClick="fnc_updateRow('<? echo $i;  ?>')" />
						</td>
						<td align="center">
							<input id="chk_bundle_<? echo $i;  ?>" type="checkbox" name="chk_bundle">
							<input type="hidden" id="hiddenid_<? echo $i; ?>" name="hiddenid_<? echo $i; ?>" value="<? echo $row[csf('id')];  ?>" style="width:15px;" class="text_boxes" />
						</td>
					</tr>
				<?
					$i++;
				}
				?>
			</tbody>
		</table>
		<table cellpadding="0" cellspacing="0" width="700">
			<tr>
				<td colspan="10" align="center" class="button_container">
					<? echo load_submit_buttons($permission, "fnc_cut_lay_bundle_info", 1, 0, "clear_size_form()", 1); ?>
				</td>
			</tr>
		</table>
	</fieldset>
 <?
	exit();
}

if ($action == "cut_lay_bundle_print") {
	extract($_REQUEST);
	$data = explode('*', $data);
	// echo "<pre>"; print_r($data); die;
	$btnType = $data[5];
	$country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");

	$company_library = array();
	$company_short_arr = array();
	$comapny_data = sql_select("select id, company_short_name, company_name from lib_company");
	foreach ($comapny_data as $comR) {
		$company_library[$comR[csf('id')]] = $comR[csf('company_name')];
		$company_short_arr[$comR[csf('id')]] = $comR[csf('company_short_name')];
	}

	$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$working_comp_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$working_location_library = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$working_floor_library = return_library_array("select id, floor_name from lib_prod_floor", "id", "floor_name");
	$table_no_library = return_library_array("select id,table_no  from  lib_cutting_table", "id", "table_no");
	$countryCodeArr = return_library_array("select id, ultimate_country_code from lib_country_loc_mapping", "id", "ultimate_country_code");
	$manualSizeArr = return_library_array("select size_id, manual_size_name from ppl_cut_lay_size where dtls_id=$data[2]", 'size_id', 'manual_size_name');


	$sql = "SELECT a.cut_num_prefix_no, a.table_no,a.cutting_no, a.job_no, a.entry_date,a.marker_length, a.marker_width, a.fabric_width, a.gsm, a.batch_id as batch_no,a.location_id,a.floor_id,a.working_company_id, b.order_id, b.color_id, b.gmt_item_id, b.plies, b.marker_qty, b.order_qty, b.order_ids, b.batch_id, b.order_cut_no from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.id='$data[1]' and a.company_id='$data[0]' and b.id='$data[2]' ";
	$dataArray = sql_select($sql);
	$sql_buyer = sql_select("select buyer_name,style_ref_no as ref,style_description as des from wo_po_details_master where job_no='" . $dataArray[0][csf('job_no')] . "' and company_name=$data[0]");
	$style_ref = $sql_buyer[0][csf('ref')];
	$style_desc = $sql_buyer[0][csf('des')];
	$cut_no_prifix = $dataArray[0][csf('cut_num_prefix_no')];
	$cut_no = $dataArray[0][csf('cutting_no')];
	$order_cut_no = $dataArray[0][csf('order_cut_no')];

	$po_arr = return_library_array("select id, po_number from wo_po_break_down where id in(" . $dataArray[0][csf('order_ids')] . ")", "id", "po_number");
	$batch_no = $dataArray[0][csf('batch_no')];

	$poCodeIdArr = array();

	$sqlCode = "Select po_break_down_id, country_id, size_number_id, code_id from wo_po_color_size_breakdown where po_break_down_id in (" . $dataArray[0][csf('order_ids')] . ") and status_active=1 and is_deleted=0 ";
	$sqlCodeData = sql_select($sqlCode);
	foreach ($sqlCodeData as $crow) {
		$poCodeIdArr[$crow[csf('po_break_down_id')]][$crow[csf('country_id')]][$crow[csf('size_number_id')]] = $countryCodeArr[$crow[csf('code_id')]];
	}
	unset($sqlCodeData);
	if ($btnType == 1) {
		$tblwidth = "1200";
		$totTrSpan = "8";
	} else if ($btnType == 2) {
		$tblwidth = "1340";
		$totTrSpan = "10";
	} else if ($btnType == 3) {
		$tblwidth = "1060";
		$totTrSpan = "5";
	}
 ?>
	<div style="width:1000px; " align="center">
		<? if ($btnType == 1 || $btnType == 2) : ?>
			<table width="990" cellspacing="0" align="center">
				<tr>
					<td colspan="6" align="center" style="font-size:20px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
				</tr>
				<tr>
					<td colspan="6" align="center" style="font-size:16px"><strong><u>Lay and Bundle Information</u></strong></td>
				</tr>
				<tr>
					<td width="130"><strong>Cut No:</strong></td>
					<td width="200"><? echo $cut_no; ?></td>
					<td width="130"><strong>Table No :</strong></td>
					<td width="200"><? echo $table_no_library[$dataArray[0][csf('table_no')]]; ?></td>
					<td width="130"><strong>Job No :</strong></td>
					<td><? echo $dataArray[0][csf('job_no')]; ?></td>
				</tr>
				<tr>
					<td><strong>Buyer:</strong></td>
					<td><? echo $buyer_library[$sql_buyer[0][csf('buyer_name')]]; ?></td>
					<td><strong>Batch No:</strong></td>
					<td><? echo $batch_no; ?></td>
				</tr>
				<tr>
					<td><strong>Gmt Item:</strong></td>
					<td><? echo $garments_item[$dataArray[0][csf('gmt_item_id')]]; ?></td>
					<td><strong>Color :</strong></td>
					<td><? echo $color_library[$dataArray[0][csf('color_id')]]; ?></td>
					<td><strong>Marker Length :</strong></td>
					<td><? echo $dataArray[0][csf('marker_length')]; ?></td>
				</tr>
				<tr>
					<td><strong>Marker Width :</strong></td>
					<td><? echo $dataArray[0][csf('marker_width')]; ?></td>
					<td><strong>Fabric Width:</strong></td>
					<td><? echo $dataArray[0][csf('fabric_width')]; ?></td>
					<td><strong>Gsm:</strong></td>
					<td><? echo $dataArray[0][csf('gsm')]; ?></td>
				</tr>
				<tr>
					<td><strong>Order Cut No:</strong></td>
					<td><? echo $order_cut_no; ?></td>
					<td><strong>Plies:</strong></td>
					<td><? echo $dataArray[0][csf('plies')]; ?></td>
					<td><strong>Cut Date:</strong></td>
					<td><? echo $dataArray[0][csf('entry_date')]; ?></td>
				</tr>
				<tr>
					<td><strong>Style Ref:</strong></td>
					<td><? echo $style_ref; ?></td>
					<td><strong>Style Desc.:</strong></td>
					<td><? echo $style_desc; ?></td>
					<td align="left" colspan="2" id="barcode_img_id"></td>
				</tr>
				<tr>
					<td><strong>W. Company:</strong></td>
					<td><? echo $working_comp_library[$dataArray[0][csf('working_company_id')]]; ?></td>
					<td><strong>W. Location:</strong></td>
					<td><? echo $working_location_library[$dataArray[0][csf('location_id')]]; ?></td>
					<td><strong>W. Floor:</strong></td>
					<td><? echo $working_floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
				</tr>
				<tr>
					<td><strong>Cutting Part:</strong></td>
					<td colspan="5"><? echo $data[5]; ?></td>
				</tr>
			</table>
		<? elseif ($btnType == 3) : ?>
			<table width="990" cellspacing="0" align="center">
				<tr>
					<td colspan="6" align="center" style="font-size:20px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
				</tr>
				<tr>
					<td colspan="6" align="center" style="font-size:16px"><strong><u>Lay and Bundle Information</u></strong></td>
				</tr>
				<tr>
					<td width="130"><strong>Cut No:</strong></td>
					<td width="200"><? echo $cut_no; ?></td>
					<td width="130"><strong>Table No :</strong></td>
					<td width="200"><? echo $table_no_library[$dataArray[0][csf('table_no')]]; ?></td>
					<td width="130"><strong>Job No :</strong></td>
					<td><? echo $dataArray[0][csf('job_no')]; ?></td>
				</tr>
				<tr>
					<td><strong>Buyer:</strong></td>
					<td><? echo $buyer_library[$sql_buyer[0][csf('buyer_name')]]; ?></td>
					<td><strong>Gmt Item:</strong></td>
					<td><? echo $garments_item[$dataArray[0][csf('gmt_item_id')]]; ?></td>
					<td><strong>Color :</strong></td>
					<td><? echo $color_library[$dataArray[0][csf('color_id')]]; ?></td>
				</tr>
				<tr>
					<td><strong>Order Cut No:</strong></td>
					<td><? echo $order_cut_no; ?></td>
					<td><strong>Plies:</strong></td>
					<td><? echo $dataArray[0][csf('plies')]; ?></td>
					<td><strong>Cut Date:</strong></td>
					<td><? echo $dataArray[0][csf('entry_date')]; ?></td>
				</tr>
				<tr>
					<td><strong>Style Ref:</strong></td>
					<td><? echo $style_ref; ?></td>
					<td><strong>Style Desc.:</strong></td>
					<td><? echo $style_desc; ?></td>
					<td align="left" colspan="2" id="barcode_img_id"></td>
				</tr>
				<tr>
					<td><strong>W. Company:</strong></td>
					<td><? echo $working_comp_library[$dataArray[0][csf('working_company_id')]]; ?></td>
					<td><strong>W. Location:</strong></td>
					<td><? echo $working_location_library[$dataArray[0][csf('location_id')]]; ?></td>
					<td><strong>W. Floor:</strong></td>
					<td><? echo $working_floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
				</tr>
				<tr>
					<td><strong>Cutting Part:</strong></td>
					<td colspan="5"><? echo $data[5]; ?></td>
				</tr>
			</table>
		<? endif; ?>
		<br>
		<script type="text/javascript" src="../../../js/jquery.js"></script>
		<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
		<script>
			function generateBarcode(valuess) {

				var value = valuess;
				var btype = 'code39';
				var renderer = 'bmp';
				var settings = {
					output: renderer,
					bgColor: '#FFFFFF',
					color: '#000000',
					barWidth: 1,
					barHeight: 30,
					moduleSize: 5,
					posX: 10,
					posY: 20,
					addQuietZone: 1
				};
				value = {
					code: value,
					rect: false
				};
				$("#barcode_img_id").show().barcode(value, btype, settings);
			}
			generateBarcode('<? echo $dataArray[0][csf('cutting_no')]; ?>');
		</script>
		<div style="width:<?= $tblwidth; ?>px;">
			<table align="center" cellspacing="0" width="<?= $tblwidth - 20; ?>" border="1" rules="all" class="rpt_table">
				<thead bgcolor="#dddddd" align="center">
					<tr>
						<th width="30" rowspan="2">SL</th>
						<th width="100" rowspan="2">Cut No</th>
						<th width="90" rowspan="2">Order No</th>
						<? if ($btnType == 1 || $btnType == 2) : ?>
							<th width="100" rowspan="2">Country</th>
						<? endif; ?>
						<? if ($btnType == 2) { ?>
							<th width="70" rowspan="2">Country Code</th>
						<? } ?>
						<th width="70" rowspan="2">Pattern No</th>
						<? if ($btnType == 2) { ?>
							<th width="50" rowspan="2">Shade</th>
						<? } ?>
						<? if ($btnType == 1 || $btnType == 2) : ?>
							<th width="60" rowspan="2">Roll No</th>
							<th width="80" rowspan="2">Batch No</th>
						<? endif; ?>
						<th width="80" rowspan="2">Bundle No</th>
						<th width="80" rowspan="2">Barcode</th>
						<th width="70" rowspan="2">Bundle Qty.</th>

						<th colspan="2">RMG Number</th>
						<th colspan="3">QC</th>
						<th rowspan="2">Remarks</th>
					</tr>
					<tr bgcolor="#dddddd" align="center">
						<th width="70">From</th>
						<th width="70">To</th>
						<th width="80">Size</th>
						<th width="40">REJ</th>
						<th width="40">REP</th>
					</tr>
				</thead>
				<tbody>
					<?
					$batchShadeNo_arr = array();

					$sqlRoll = sql_select("SELECT a.id, a.batch_no, a.shade from pro_roll_details a, ppl_cut_lay_bundle b where a.id=b.roll_id and a.mst_id='$data[1]' and a.dtls_id='$data[2]' and a.entry_form=99");
					foreach ($sqlRoll as $rrow) {
						$batchShadeNo_arr[$rrow[csf('id')]]['batch'] = $rrow[csf('batch_no')];
						$batchShadeNo_arr[$rrow[csf('id')]]['shade'] = $rrow[csf('shade')];
					}
					unset($sqlRoll);

					if ($data[4] == 0) $country_cond = "";
					else $country_cond = " and a.country_id='" . $data[4] . "'";
					$size_data = sql_select("select a.id,a.size_id,a.bundle_sequence,a.marker_qty from ppl_cut_lay_size_dtls a where a.mst_id='$data[1]' and a.dtls_id='$data[2]'  order by a.bundle_sequence ASC");
					$j = 1;
					foreach ($size_data as $size_val) {
						$total_marker_qty_size = 0;
						$bundle_data = sql_select("SELECT a.id,a.bundle_no as bundle_no,a.bundle_num_prefix_no,a.size_id,a.number_start,a.number_end,a.size_qty, a.country_id, a.roll_no, a.roll_id, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no,a.barcode_no from ppl_cut_lay_bundle a where a.mst_id='$data[1]' and a.dtls_id='$data[2]' and a.size_id=" . $size_val[csf('size_id')] . " $country_cond order by a.id ASC");
						//echo "select a.id,a.bundle_no as bundle_no,a.bundle_num_prefix_no,a.size_id,a.number_start,a.number_end,a.size_qty, a.country_id, a.roll_no, a.roll_id, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no from ppl_cut_lay_bundle a where a.mst_id='$data[1]' and a.dtls_id='$data[2]' and a.size_id=".$size_val[csf('size_id')]." $country_cond order by a.id ASC";
						foreach ($bundle_data as $row) {
					?>
							<tr>
								<td align="center"><? echo $j;  ?></td>
								<td align="center"><? echo $cut_no; ?></td>
								<td style="word-wrap:break-word"><? echo $po_arr[$row[csf('order_id')]]; ?></td>
								<? if ($btnType == 1 || $btnType == 2) : ?>
									<td style="word-wrap:break-word"><? echo $country_arr[$row[csf('country_id')]]; ?></td>
								<? endif; ?>
								<? if ($btnType == 2) { ?>
									<td style="word-wrap:break-word"><?= $poCodeIdArr[$row[csf('order_id')]][$row[csf('country_id')]][$row[csf('size_id')]]; ?></td>
								<? } ?>
								<td align="center"><? echo $row[csf('pattern_no')]; ?></td>
								<? if ($btnType == 2) { ?>
									<td align="center" style="word-wrap:break-word"><?= $batchShadeNo_arr[$row[csf('roll_id')]]['shade']; ?></td>
								<? } ?>
								<? if ($btnType == 1 || $btnType == 2) : ?>
									<td align="center"><? echo $row[csf('roll_no')]; ?></td>
									<td style="word-wrap:break-word"><?= $batchShadeNo_arr[$row[csf('roll_id')]]['batch']; ?></td>
								<? endif; ?>
								<td align="center"><? echo $row[csf('bundle_num_prefix_no')]; //$row[csf('bundle_num_prefix_no')];  
													?></td>
								<td align="center"><? echo $row[csf('barcode_no')]; ?></td>
								<td align="center"><? echo $row[csf('size_qty')];  ?></td>
								<td align="center"><? echo $row[csf('number_start')];  ?></td>
								<td align="center"><? echo $row[csf('number_end')];  ?></td>
								<? if ($btnType == 1 || $btnType == 2) : ?>
									<td align="center"><? echo $size_arr[$row[csf('size_id')]] . "[" . $manualSizeArr[$row[csf('size_id')]] . "]";  ?></td>
								<? elseif ($btnType == 3) : ?>
									<td align="center"><? echo $size_arr[$row[csf('size_id')]];  ?></td>
								<? endif; ?>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
							</tr>
						<?
							$j++;
							$total_marker_qty_size += $row[csf('size_qty')];
							$total_marker_qty += $row[csf('size_qty')];
						}
						//  $total_marker_qty+=$size_val[csf('marker_qty')];
						?>
						<tr bgcolor="#eeeeee">
							<td align="center"></td>
							<? if ($btnType == 1 || $btnType == 2) : ?>
								<td colspan="<?= $totTrSpan; ?>" align="right"><? echo $size_arr[$row[csf('size_id')]] . "[" . $manualSizeArr[$row[csf('size_id')]] . "]"; ?> Size Total</td>
							<? elseif ($btnType == 3) : ?>
								<td colspan="<?= $totTrSpan; ?>" align="right"><? echo $size_arr[$row[csf('size_id')]]; ?> Size Total</td>
							<? endif; ?>
							<td align="center"><? echo $total_marker_qty_size;  ?></td>
							<td align="center">&nbsp;</td>
							<td align="center">&nbsp;</td>
							<td align="center">&nbsp;</td>
							<td align="center">&nbsp;</td>
							<td align="center">&nbsp;</td>
							<td align="center">&nbsp;</td>
						</tr>
					<?
					}
					?>
					<tr bgcolor="#BBBBBB">
						<td align="center"></td>
						<td colspan="<?= $totTrSpan; ?>" align="right"> Total marker qty.</td>
						<td align="center"><? echo $total_marker_qty;  ?></td>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td>
					</tr>
				</tbody>
			</table>
			<br>
			<? echo signature_table(9, $data[0], "900px"); ?>
		</div>
	</div>
 <?
	exit();
}


if ($action == "lay_bundle_print") {

	//echo "under constration"; die;
	extract($_REQUEST);
	$data = explode('*', $data);
	//print_r($data);
	$btnType = $data[5];
	$country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");

	$company_library = array();
	$company_short_arr = array();
	$comapny_data = sql_select("select id, company_short_name, company_name from lib_company");
	foreach ($comapny_data as $comR) {
		$company_library[$comR[csf('id')]] = $comR[csf('company_name')];
		$company_short_arr[$comR[csf('id')]] = $comR[csf('company_short_name')];
	}

	$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$working_comp_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$working_location_library = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$working_floor_library = return_library_array("select id, floor_name from lib_prod_floor", "id", "floor_name");
	$table_no_library = return_library_array("select id,table_no  from  lib_cutting_table", "id", "table_no");
	$countryCodeArr = return_library_array("select id, ultimate_country_code from lib_country_loc_mapping", "id", "ultimate_country_code");



	$sql = "select a.id, a.cut_num_prefix_no, a.table_no,a.cutting_no, a.job_no, a.entry_date,a.marker_length, a.marker_width, a.fabric_width, a.gsm, a.batch_id as batch_no,a.location_id,a.floor_id,a.working_company_id, b.order_id, b.color_id, b.gmt_item_id, b.plies, b.marker_qty, b.order_qty, b.order_ids, b.batch_id, b.order_cut_no from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.id='$data[1]' and a.company_id='$data[0]' and b.id='$data[2]' ";
	$dataArray = sql_select($sql);
	$sql_buyer = sql_select("select buyer_name,style_ref_no as ref,style_description as des from wo_po_details_master where job_no='" . $dataArray[0][csf('job_no')] . "' and company_name=$data[0]");
	$style_ref = $sql_buyer[0][csf('ref')];
	$style_desc = $sql_buyer[0][csf('des')];
	$cut_no_prifix = $dataArray[0][csf('cut_num_prefix_no')];
	$cut_no = $dataArray[0][csf('cutting_no')];
	$order_cut_no = $dataArray[0][csf('order_cut_no')];

	$po_arr = return_library_array("select id, po_number from wo_po_break_down where id in(" . $dataArray[0][csf('order_ids')] . ")", "id", "po_number");
	$batch_no = $dataArray[0][csf('batch_no')];

	$poCodeIdArr = array();

	$sqlCode = "Select po_break_down_id, country_id, size_number_id, code_id from wo_po_color_size_breakdown where po_break_down_id in (" . $dataArray[0][csf('order_ids')] . ") and status_active=1 and is_deleted=0 ";
	$sqlCodeData = sql_select($sqlCode);
	foreach ($sqlCodeData as $crow) {
		$poCodeIdArr[$crow[csf('po_break_down_id')]][$crow[csf('country_id')]][$crow[csf('size_number_id')]] = $countryCodeArr[$crow[csf('code_id')]];
	}
	unset($sqlCodeData);


	$sql_color = "select id, color_id, order_cut_no from ppl_cut_lay_dtls where id='$data[2]'";
	//echo $sql_color; die;
	$sql_color_data = sql_select($sql_color);
	$color_id_arr = array();
	$order_cut_arr = array();
	foreach ($sql_color_data as $rrow) {
		$color_id_arr[$rrow[csf('id')]]['color_id'] = $rrow[csf('color_id')];
		$order_cut_arr[$rrow[csf('id')]]['order_cut_no'] = $rrow[csf('order_cut_no')];
	}
	unset($sql_color_data);


	/*if($btnType==1) { $tblwidth="1200"; $totTrSpan="8"; }
	else if($btnType==2) { $tblwidth="1340"; $totTrSpan="10"; }*/
	ob_start();
 ?>
	<html>
	<div style="width:1000px; " align="center">
		<table width="990" cellspacing="0" align="center">
			<tr>
				<td colspan="6" align="center" style="font-size:20px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:16px"><strong><u>Lay and Bundle Information</u></strong></td>
			</tr>

			<tr>
				<td width="130"><strong>Buyer:</strong></td>
				<td width="200" align="left"><? echo $buyer_library[$sql_buyer[0][csf('buyer_name')]]; ?></td>
				<td width="130"><strong>Style Ref :</strong></td>
				<td width="200"><? echo $style_ref; ?></td>
				<td width="130"><strong>Job No :</strong></td>
				<td><? echo $dataArray[0][csf('job_no')]; ?></td>
			</tr>
			<tr>
				<td><strong>Lay Qty:</strong></td>
				<td align="left"><? echo $dataArray[0][csf('plies')]; ?></td>
				<td><strong>Sys Cut No:</strong></td>
				<td><? echo $cut_no; ?></td>
				<td><strong>Gmt Item:</strong></td>
				<td><? echo $garments_item[$dataArray[0][csf('gmt_item_id')]]; ?></td>
			</tr>
			<tr>
				<td><strong>W. Company:</strong></td>
				<td align="left"><? echo $working_comp_library[$dataArray[0][csf('working_company_id')]]; ?></td>
				<td><strong>W. Location:</strong></td>
				<td><? echo $working_location_library[$dataArray[0][csf('location_id')]]; ?></td>
				<td><strong>W. Floor:</strong></td>
				<td><? echo $working_floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
			</tr>
		</table>
		<br>
		<script type="text/javascript" src="../../../js/jquery.js"></script>
		<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
		<script>
			function generateBarcode(valuess) {

				var value = valuess;
				var btype = 'code39';
				var renderer = 'bmp';
				var settings = {
					output: renderer,
					bgColor: '#FFFFFF',
					color: '#000000',
					barWidth: 1,
					barHeight: 30,
					moduleSize: 5,
					posX: 10,
					posY: 20,
					addQuietZone: 1
				};
				value = {
					code: value,
					rect: false
				};
				$("#barcode_img_id").show().barcode(value, btype, settings);
			}
			generateBarcode('<? echo $dataArray[0][csf('cutting_no')]; ?>');
		</script>
		<div style="width:1050px;">
			<table align="center" cellspacing="0" width="1030;" border="1" rules="all" class="rpt_table">
				<thead bgcolor="#dddddd" align="center">
					<tr>
						<th width="30">SL</th>
						<th width="80">Bundle No</th>
						<th width="80">Cut No</th>
						<th width="100">Size/Group</th>
						<th width="100">Color</th>
						<th width="80">Shade No</th>
						<th width="100">Bundle Qty.</th>
						<th width="200" colspan="2">Sticker No</th>
						<th width="140">Order No</th>
						<th width="">Country</th>
				</thead>
				<tbody>
					<?
					$batchShadeNo_arr = array();
					//$sqlRoll=sql_select("select a.id, a.batch_no, a.shade from pro_roll_details a, ppl_cut_lay_bundle b, ppl_cut_lay_mst c where a.id=b.roll_id and a.mst_id='$data[1]' and a.dtls_id='$data[2]' and a.entry_form=c.entry_form");
					//$sql_shade="select a.id, a.batch_no, a.shade from pro_roll_details a, ppl_cut_lay_bundle b where a.id=b.roll_id and a.mst_id='$data[1]' and a.dtls_id='$data[2]' and a.entry_form=99";
					$sql_shade = "select a.id, a.batch_no, a.shade from pro_roll_details a, ppl_cut_lay_bundle b, ppl_cut_lay_mst c where a.id=b.roll_id and a.mst_id='$data[1]' and a.dtls_id='$data[2]' and a.entry_form=c.entry_form";
					$sqlRoll = sql_select($sql_shade);
					foreach ($sqlRoll as $rrow) {
						$batchShadeNo_arr[$rrow[csf('id')]]['batch'] = $rrow[csf('batch_no')];
						$batchShadeNo_arr[$rrow[csf('id')]]['shade'] = $rrow[csf('shade')];
					}
					unset($sqlRoll);

					if ($data[4] == 0) $country_cond = "";
					else $country_cond = " and a.country_id='" . $data[4] . "'";
					$size_data = sql_select("select a.id,a.size_id,a.bundle_sequence,a.marker_qty from ppl_cut_lay_size_dtls a where a.mst_id='$data[1]' and a.dtls_id='$data[2]'  order by a.bundle_sequence ASC");
					$j = 1;
					foreach ($size_data as $size_val) {
						$total_marker_qty_size = 0;
						$bundle_data = sql_select("select a.id,a.dtls_id,a.bundle_no as bundle_no,a.bundle_num_prefix_no,a.size_id,a.number_start,a.number_end,a.size_qty, a.country_id, a.roll_no, a.roll_id, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no,a.barcode_no from ppl_cut_lay_bundle a where a.mst_id='$data[1]' and a.dtls_id='$data[2]' and a.size_id=" . $size_val[csf('size_id')] . " $country_cond order by a.id ASC");
						//echo "select a.id,a.bundle_no as bundle_no,a.bundle_num_prefix_no,a.size_id,a.number_start,a.number_end,a.size_qty, a.country_id, a.roll_no, a.roll_id, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no from ppl_cut_lay_bundle a where a.mst_id='$data[1]' and a.dtls_id='$data[2]' and a.size_id=".$size_val[csf('size_id')]." $country_cond order by a.id ASC";
						foreach ($bundle_data as $row) {
					?>
							<tr>
								<td align="center"><? echo $j;  ?></td>
								<td align="center"><? echo $row[csf('bundle_num_prefix_no')]; //$row[csf('bundle_num_prefix_no')];  
													?></td>
								<td align="center"><? echo $order_cut_arr[$row[csf('dtls_id')]]['order_cut_no']; //$cut_no; //$order_cut_no; 
													?></td>
								<td align="center"><? echo $size_arr[$row[csf('size_id')]] . "-" . $row[csf('pattern_no')];  ?></td>
								<td align="center"><? echo $color_library[$color_id_arr[$row[csf('dtls_id')]]['color_id']]; ?></td>
								<td align="center" style="word-wrap:break-word"><?= $batchShadeNo_arr[$row[csf('roll_id')]]['shade']; ?></td>
								<td align="center"><? echo $row[csf('size_qty')];  ?></td>
								<td align="center"><? echo $row[csf('number_start')];  ?></td>
								<td align="center"><? echo $row[csf('number_end')];  ?></td>
								<td style="word-wrap:break-word" align="center"><? echo $po_arr[$row[csf('order_id')]]; ?></td>
								<td style="word-wrap:break-word"><? echo $country_arr[$row[csf('country_id')]]; ?></td>

								<!-- 
                             
                               <td style="word-wrap:break-word"><? //=$poCodeIdArr[$row[csf('order_id')]][$row[csf('country_id')]][$row[csf('size_id')]]; 
																?></td>
                               <td align="center"><? //echo $row[csf('pattern_no')]; 
													?></td>
                               <td align="center"><? //echo $row[csf('roll_no')]; 
													?></td>
                               <td style="word-wrap:break-word"><? //=$batchShadeNo_arr[$row[csf('roll_id')]]['batch']; 
																?></td>
                               <td align="center"><? //echo $row[csf('barcode_no')];
													?></td>
                               
                               
                               <td align="center">&nbsp;</td>
                               <td align="center">&nbsp;</td>
                               <td align="center">&nbsp;</td> -->
							</tr>
						<?
							$j++;
							$total_marker_qty_size += $row[csf('size_qty')];
							$total_marker_qty += $row[csf('size_qty')];
						}
						//  $total_marker_qty+=$size_val[csf('marker_qty')];
						?>
						<tr bgcolor="#eeeeee">
							<td align="center"></td>
							<td colspan="5" align="right"><strong><? echo $size_arr[$row[csf('size_id')]]; ?> Size Total</strong></td>
							<td align="center"><strong><? echo $total_marker_qty_size;  ?></strong></td>
							<td align="center" colspan="4">&nbsp;</td>
						</tr>
					<?
					}
					?>
					<tr bgcolor="#BBBBBB">
						<td align="center"></td>
						<td colspan="5" align="right"><strong> Total marker qty.</strong></td>
						<td align="center"><strong><? echo $total_marker_qty;  ?></strong></td>
						<td align="center" colspan="4">&nbsp;</td>
					</tr>
				</tbody>
			</table>
			<br>
			<? echo signature_table(9, $data[0], "900px"); ?>
		</div>
	</div>

	</html>
	<?

	$user_id = $_SESSION['logic_erp']['user_id'];
	$report_cat = 100;
	$html = ob_get_contents();
	ob_clean();
	//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
	foreach (glob("tb*.xls") as $filename) {
		//if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name = time();
	$filename = "tb" . $user_id . "_" . $name . ".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$filename****$html****$report_cat";

	exit();
}



if ($action == "cut_lay_qc_bundle_print") {
	extract($_REQUEST);
	$data = explode('*', $data);
	//print_r($data);
	$btnType = $data[5];
	$country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");

	$company_library = array();
	$company_short_arr = array();
	$comapny_data = sql_select("select id, company_short_name, company_name from lib_company");
	foreach ($comapny_data as $comR) {
		$company_library[$comR[csf('id')]] = $comR[csf('company_name')];
		$company_short_arr[$comR[csf('id')]] = $comR[csf('company_short_name')];
	}

	$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$working_comp_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$working_location_library = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$working_floor_library = return_library_array("select id, floor_name from lib_prod_floor", "id", "floor_name");
	$table_no_library = return_library_array("select id,table_no  from  lib_cutting_table", "id", "table_no");
	$countryCodeArr = return_library_array("select id, ultimate_country_code from lib_country_loc_mapping", "id", "ultimate_country_code");



	$sql = "select a.cut_num_prefix_no, a.table_no,a.cutting_no, a.job_no, a.entry_date,a.marker_length, a.marker_width, a.fabric_width, a.gsm, a.batch_id as batch_no,a.location_id,a.floor_id,a.working_company_id, b.order_id, b.color_id, b.gmt_item_id, b.plies, b.marker_qty, b.order_qty, b.order_ids, b.batch_id, b.order_cut_no from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.id='$data[1]' and a.company_id='$data[0]' and b.id='$data[2]' ";
	// echo $sql;
	$dataArray = sql_select($sql);
	$sql_buyer = sql_select("select buyer_name,style_ref_no as ref,style_description as des from wo_po_details_master where job_no='" . $dataArray[0][csf('job_no')] . "' and company_name=$data[0]");
	$style_ref = $sql_buyer[0][csf('ref')];
	$style_desc = $sql_buyer[0][csf('des')];
	$cut_no_prifix = $dataArray[0][csf('cut_num_prefix_no')];
	$cut_no = $dataArray[0][csf('cutting_no')];
	$order_cut_no = $dataArray[0][csf('order_cut_no')];

	$po_arr = return_library_array("select id, po_number from wo_po_break_down where id in(" . $dataArray[0][csf('order_ids')] . ")", "id", "po_number");
	$batch_no = $dataArray[0][csf('batch_no')];

	$poCodeIdArr = array();

	$sqlCode = "Select po_break_down_id, country_id, size_number_id, code_id from wo_po_color_size_breakdown where po_break_down_id in (" . $dataArray[0][csf('order_ids')] . ") and status_active=1 and is_deleted=0 ";
	$sqlCodeData = sql_select($sqlCode);
	foreach ($sqlCodeData as $crow) {
		$poCodeIdArr[$crow[csf('po_break_down_id')]][$crow[csf('country_id')]][$crow[csf('size_number_id')]] = $countryCodeArr[$crow[csf('code_id')]];
	}
	unset($sqlCodeData);
	if ($btnType == 1) {
		$tblwidth = "1200";
		$totTrSpan = "8";
	} else if ($btnType == 2) {
		$tblwidth = "1340";
		$totTrSpan = "10";
	}
	?>
	<div style="width:1000px; " align="center">
		<table width="990" cellspacing="0" align="center">
			<tr>
				<td colspan="6" align="center" style="font-size:20px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:16px"><strong><u>Cut. Panel Inspection Report</u></strong></td>
			</tr>
			<tr>
				<td width="130"><strong>Cut No:</strong></td>
				<td width="200"><? echo $cut_no; ?></td>
				<td width="130"><strong>Table No :</strong></td>
				<td width="200"><? echo $table_no_library[$dataArray[0][csf('table_no')]]; ?></td>
				<td width="130"><strong>Job No :</strong></td>
				<td><? echo $dataArray[0][csf('job_no')]; ?></td>
			</tr>
			<tr>
				<td><strong>Buyer:</strong></td>
				<td><? echo $buyer_library[$sql_buyer[0][csf('buyer_name')]]; ?></td>
				<td><strong>Batch No:</strong></td>
				<td><? echo $batch_no; ?></td>
			</tr>
			<tr>
				<td><strong>Gmt Item:</strong></td>
				<td><? echo $garments_item[$dataArray[0][csf('gmt_item_id')]]; ?></td>
				<td><strong>Color :</strong></td>
				<td><? echo $color_library[$dataArray[0][csf('color_id')]]; ?></td>
				<td><strong>Marker Length :</strong></td>
				<td><? echo $dataArray[0][csf('marker_length')]; ?></td>
			</tr>
			<tr>
				<td><strong>Marker Width :</strong></td>
				<td><? echo $dataArray[0][csf('marker_width')]; ?></td>
				<td><strong>Fabric Width:</strong></td>
				<td><? echo $dataArray[0][csf('fabric_width')]; ?></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td><strong>Order Cut No:</strong></td>
				<td><? echo $order_cut_no; ?></td>
				<td><strong>Plies:</strong></td>
				<td><? echo $dataArray[0][csf('plies')]; ?></td>
				<td><strong>Cut Date:</strong></td>
				<td><? echo $dataArray[0][csf('entry_date')]; ?></td>
			</tr>
			<tr>
				<td><strong>Style Ref:</strong></td>
				<td><? echo $style_ref; ?></td>
				<td><strong>Style Desc.:</strong></td>
				<td><? echo $style_desc; ?></td>
				<td align="left" colspan="2" id="barcode_img_id"></td>
			</tr>
			<tr>
				<td><strong>W. Company:</strong></td>
				<td><? echo $working_comp_library[$dataArray[0][csf('working_company_id')]]; ?></td>
				<td><strong>W. Location:</strong></td>
				<td><? echo $working_location_library[$dataArray[0][csf('location_id')]]; ?></td>
				<td><strong>W. Floor:</strong></td>
				<td><? echo $working_floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Cutting Part:</strong></td>
				<td colspan="5"><? echo $data[5]; ?></td>
			</tr>
		</table>
		<br>
		<script type="text/javascript" src="../../../js/jquery.js"></script>
		<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
		<script>
			function generateBarcode(valuess) {

				var value = valuess;
				var btype = 'code39';
				var renderer = 'bmp';
				var settings = {
					output: renderer,
					bgColor: '#FFFFFF',
					color: '#000000',
					barWidth: 1,
					barHeight: 30,
					moduleSize: 5,
					posX: 10,
					posY: 20,
					addQuietZone: 1
				};
				value = {
					code: value,
					rect: false
				};
				$("#barcode_img_id").show().barcode(value, btype, settings);
			}
			generateBarcode('<? echo $dataArray[0][csf('cutting_no')]; ?>');
		</script>
		<div style="width:<?= $tblwidth; ?>px;">
			<table align="center" cellspacing="0" width="<?= $tblwidth - 20; ?>" border="1" rules="all" class="rpt_table">
				<thead bgcolor="#dddddd" align="center">
					<tr>
						<th width="30" rowspan="3">SL</th>
						<th width="100" rowspan="3">Cut No</th>
						<th width="110" rowspan="3">Order No</th>
						<th width="100" rowspan="3">Country</th>
						<th width="70" rowspan="3">Pattern No</th>
						<th width="60" rowspan="3">Roll No</th>
						<th width="60" rowspan="3">Shade no</th>
						<th width="60" rowspan="3">Bundle No</th>
						<th width="80" rowspan="3">Barcode</th>
						<th width="70" rowspan="3">Bundle Qty.</th>

						<th colspan="2">RMG Number</th>
						<th colspan="4">QC</th>
						<th width="120" rowspan="3">Remarks</th>
					</tr>
					<tr bgcolor="#dddddd" align="center">
						<th width="70" rowspan="2">From</th>
						<th width="70" rowspan="2">To</th>
						<th width="80" rowspan="2">Size</th>
						<th colspan="2">REJ</th>
						<th width="40" rowspan="2">REP</th>
					</tr>
					<tr bgcolor="#dddddd" align="center">
						<th>Front</th>
						<th>Back</th>
					</tr>
				</thead>
				<tbody>
					<?
					$batchShadeNo_arr = array();

					$sqlRoll = sql_select("select a.id, a.batch_no, a.shade from pro_roll_details a, ppl_cut_lay_bundle b, ppl_cut_lay_mst c where a.id=b.roll_id and a.mst_id='$data[1]' and a.dtls_id='$data[2]' and a.entry_form=c.entry_form");
					//  var_dump($sqlRoll);
					foreach ($sqlRoll as $rrow) {
						$batchShadeNo_arr[$rrow[csf('id')]]['batch'] = $rrow[csf('batch_no')];
						$batchShadeNo_arr[$rrow[csf('id')]]['shade'] = $rrow[csf('shade')];
					}

					if ($data[4] == 0) $country_cond = "";
					else $country_cond = " and a.country_id='" . $data[4] . "'";
					$size_data = sql_select("select a.id,a.size_id,a.bundle_sequence,a.marker_qty from ppl_cut_lay_size_dtls a where a.mst_id='$data[1]' and a.dtls_id='$data[2]'  order by a.bundle_sequence ASC");
					$j = 1;
					foreach ($size_data as $size_val) {
						$total_marker_qty_size = 0;
						$bundle_data = sql_select("select a.id,a.bundle_no as bundle_no,a.bundle_num_prefix_no,a.size_id,a.number_start,a.number_end,a.size_qty, a.country_id, a.roll_no, a.roll_id, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no,a.barcode_no from ppl_cut_lay_bundle a where a.mst_id='$data[1]' and a.dtls_id='$data[2]' and a.size_id=" . $size_val[csf('size_id')] . " $country_cond order by a.id ASC");
						//var_dump ($bundle_data);
						//echo "select a.id,a.bundle_no as bundle_no,a.bundle_num_prefix_no,a.size_id,a.number_start,a.number_end,a.size_qty, a.country_id, a.roll_no, a.roll_id, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no from ppl_cut_lay_bundle a where a.mst_id='$data[1]' and a.dtls_id='$data[2]' and a.size_id=".$size_val[csf('size_id')]." $country_cond order by a.id ASC";
						foreach ($bundle_data as $row) {
					?>
							<tr>
								<td align="center"><? echo $j;  ?></td>
								<td align="center"><? echo $cut_no; ?></td>
								<td style="word-wrap:break-word"><? echo $po_arr[$row[csf('order_id')]]; ?></td>
								<td style="word-wrap:break-word"><? echo $country_arr[$row[csf('country_id')]]; ?></td>
								<td align="center"><? echo $row[csf('pattern_no')]; ?></td>
								<td align="center"><? echo $row[csf('roll_no')]; ?></td>
								<td align="center" style="word-wrap:break-word"><?= $batchShadeNo_arr[$row[csf('roll_id')]]['shade']; ?></td>
								<td align="center"><? echo $row[csf('bundle_num_prefix_no')]; //$row[csf('bundle_num_prefix_no')];  
													?></td>
								<td align="center"><? echo $row[csf('barcode_no')]; ?></td>
								<td align="center"><? echo $row[csf('size_qty')];  ?></td>
								<td align="center"><? echo $row[csf('number_start')];  ?></td>
								<td align="center"><? echo $row[csf('number_end')];  ?></td>
								<td align="center"><? echo $size_arr[$row[csf('size_id')]];  ?></td>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
							</tr>
						<?
							$j++;
							$total_marker_qty_size += $row[csf('size_qty')];
							$total_marker_qty += $row[csf('size_qty')];
						}
						//  $total_marker_qty+=$size_val[csf('marker_qty')];
						?>
						<tr bgcolor="#eeeeee">
							<td align="center"></td>
							<td colspan="<?= $totTrSpan; ?>" align="right"><? echo $size_arr[$row[csf('size_id')]]; ?> Size Total</td>
							<td align="center"><? echo $total_marker_qty_size;  ?></td>
							<td align="center">&nbsp;</td>
							<td align="center">&nbsp;</td>
							<td align="center">&nbsp;</td>
							<td align="center">&nbsp;</td>
							<td align="center">&nbsp;</td>
							<td align="center">&nbsp;</td>
						</tr>
					<?
					}
					?>
					<tr bgcolor="#BBBBBB">
						<td align="center"></td>
						<td colspan="<?= $totTrSpan; ?>" align="right"> Total marker qty.</td>
						<td align="center"><? echo $total_marker_qty;  ?></td>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td>
					</tr>
				</tbody>
			</table>
			<br>
			<? echo signature_table(221, $data[0], "900px"); ?>
		</div>
	</div>
 <?
	exit();
}
if ($action == "cut_lay_qc_bundle_print_2") //[QC Bundle-2
{
	extract($_REQUEST);
	$data = explode('*', $data);
	//print_r($data[5]);
	$btnType = $data[5];
	$country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");

	$company_library = array();
	$company_short_arr = array();
	$comapny_data = sql_select("select id, company_short_name, company_name from lib_company");
	foreach ($comapny_data as $comR) {
		$company_library[$comR[csf('id')]] = $comR[csf('company_name')];
		$company_short_arr[$comR[csf('id')]] = $comR[csf('company_short_name')];
	}

	$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$working_comp_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$working_location_library = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$working_floor_library = return_library_array("select id, floor_name from lib_prod_floor", "id", "floor_name");
	$table_no_library = return_library_array("select id,table_no  from  lib_cutting_table", "id", "table_no");
	$countryCodeArr = return_library_array("select id, ultimate_country_code from lib_country_loc_mapping", "id", "ultimate_country_code");

	$sql = "SELECT a.cut_num_prefix_no, a.table_no,a.cutting_no, a.job_no, a.entry_date,a.marker_length, a.marker_width, a.fabric_width, a.gsm, a.batch_id as batch_no,a.location_id,a.floor_id,a.working_company_id, b.order_id, b.color_id, b.gmt_item_id, b.plies, b.marker_qty, b.order_qty, b.order_ids, b.batch_id, b.order_cut_no
	from ppl_cut_lay_mst a, ppl_cut_lay_dtls b
	where a.id='$data[1]' 
	and a.company_id='$data[0]' 
	and b.id='$data[2]'
	
	 ";
	//echo $sql;
	$dataArray = sql_select($sql);
	$sql_buyer = sql_select("select buyer_name,style_ref_no as ref,style_description as des from wo_po_details_master where job_no='" . $dataArray[0][csf('job_no')] . "' and company_name=$data[0]");
	$style_ref = $sql_buyer[0][csf('ref')];
	$style_desc = $sql_buyer[0][csf('des')];
	$cut_no_prifix = $dataArray[0][csf('cut_num_prefix_no')];
	$cut_no = $dataArray[0][csf('cutting_no')];
	$order_cut_no = $dataArray[0][csf('order_cut_no')];

	$sql_lot = "SELECT a.batch_no
	FROM pro_roll_details a, ppl_cut_lay_bundle b, ppl_cut_lay_mst c
	where a.id=b.ROLL_ID
	and b.MST_ID=c.id
	and a.DTLS_ID=$data[2]
	and a.status_active=1 
	and a.is_deleted=0 
	and b.status_active=1 
	and b.is_deleted=0
	and c.status_active=1 
	and c.is_deleted=0 ";
	//echo $sql_lot;
	$data_lotArray = sql_select($sql_lot);
	$lot_no = $data_lotArray[0][csf("batch_no")];


	$po_arr = return_library_array("select id, po_number from wo_po_break_down where id in(" . $dataArray[0][csf('order_ids')] . ")", "id", "po_number");
	$batch_no = $dataArray[0][csf('batch_no')];
	$poCodeIdArr = array();
	$sqlCode = "SELECT po_break_down_id, country_id, size_number_id, code_id from wo_po_color_size_breakdown where po_break_down_id in (" . $dataArray[0][csf('order_ids')] . ") and status_active=1 and is_deleted=0 ";
	$sqlCodeData = sql_select($sqlCode);
	foreach ($sqlCodeData as $crow) {
		$poCodeIdArr[$crow[csf('po_break_down_id')]][$crow[csf('country_id')]][$crow[csf('size_number_id')]] = $countryCodeArr[$crow[csf('code_id')]];
	}
	unset($sqlCodeData);




	if ($btnType == 2) {
		$tblwidth = "1605";
		$totTrSpan = "7";
	}
	// else if($btnType==1) { $tblwidth="1340"; $totTrSpan="10"; }
 ?>
	<style>
		@page {
			size: auto;
			/* auto is the initial value */
			/* this affects the margin in the printer settings */
			margin-top: 18mm;
			margin-bottom: 18mm;
			margin-right: 2mm;
			margin-left: 2mm;
		}
	</style>
	<div style="width:1370px; " align="center">
		<table width="1350" cellspacing="0" align="center">
			<tr>
				<td colspan="8" align="center" style="font-size:20px">
					<strong><? echo $company_library[$data[0]]; ?></strong>
				</td>
			</tr>
			<tr>
				<td colspan="8" align="center" style="font-size:16px"><strong><u>100% Cut. Panel Inspection Report (Components Parts)</u></strong></td>
				<td width="60">&nbsp;</td>
				<td width="60">&nbsp;</td>
				<td align="left" style="margin-left:25px;font-size:20px;"><strong>OSL-QAD-072<br>V-0<br>This is Control/Doc.</strong></td>
			</tr>
			<tr>
				<td width="130"><strong>Cut No:</strong></td>
				<td width="200"><? echo $cut_no; ?></td>
				<td width="130"><strong>Table No :</strong></td>
				<td width="200"><? echo $table_no_library[$dataArray[0][csf('table_no')]]; ?></td>
				<td width="130"><strong>Job No :</strong></td>
				<td><? echo $dataArray[0][csf('job_no')]; ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><strong>Buyer:</strong></td>
				<td><? echo $buyer_library[$sql_buyer[0][csf('buyer_name')]]; ?></td>
				<td><strong>Batch No:</strong></td>
				<td><? echo $batch_no; ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><strong>Gmt Item:</strong></td>
				<td><? echo $garments_item[$dataArray[0][csf('gmt_item_id')]]; ?></td>
				<td><strong>Color :</strong></td>
				<td><? echo $color_library[$dataArray[0][csf('color_id')]]; ?></td>
				<td><strong>Marker Length :</strong></td>
				<td><? echo $dataArray[0][csf('marker_length')]; ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><strong>Marker Width :</strong></td>
				<td><? echo $dataArray[0][csf('marker_width')]; ?></td>
				<td><strong>Fabric Width:</strong></td>
				<td><? echo $dataArray[0][csf('fabric_width')]; ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><strong>Order Cut No:</strong></td>
				<td><? echo $order_cut_no; ?></td>
				<td><strong>Plies:</strong></td>
				<td><? echo $dataArray[0][csf('plies')]; ?></td>
				<td><strong>Cut Date:</strong></td>
				<td><? echo $dataArray[0][csf('entry_date')]; ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><strong>Style Ref:</strong></td>
				<td><? echo $style_ref; ?></td>
				<td><strong>Style Desc.:</strong></td>
				<td><? echo $style_desc; ?></td>
				<td align="left" colspan="2" id="barcode_img_id"></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><strong>W. Company:</strong></td>
				<td><? echo $working_comp_library[$dataArray[0][csf('working_company_id')]]; ?></td>
				<td><strong>W. Location:</strong></td>
				<td><? echo $working_location_library[$dataArray[0][csf('location_id')]]; ?></td>
				<td><strong>W. Floor:</strong></td>
				<td><? echo $working_floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><strong>Gold Seal: </strong></td>
				<td><? echo $lot_no; ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>

			</tr>
		</table>
		<br>
		<script type="text/javascript" src="../../../js/jquery.js"></script>
		<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
		<script>
			function generateBarcode(valuess) {

				var value = valuess;
				var btype = 'code39';
				var renderer = 'bmp';
				var settings = {
					output: renderer,
					bgColor: '#FFFFFF',
					color: '#000000',
					barWidth: 1,
					barHeight: 30,
					moduleSize: 5,
					posX: 10,
					posY: 20,
					addQuietZone: 1
				};
				value = {
					code: value,
					rect: false
				};
				$("#barcode_img_id").show().barcode(value, btype, settings);
			}
			generateBarcode('<? echo $dataArray[0][csf('cutting_no')]; ?>');
		</script>
		<div style="width:<?= $tblwidth; ?>px;">
			<table align="center" cellspacing="0" width="<?= $tblwidth - 20; ?>" border="1" rules="all" class="rpt_table">
				<thead bgcolor="#dddddd" align="center">
					<tr>
						<th width="30" rowspan="3">Sl</th>
						<th width="150" rowspan="3">Order No</th>
						<th width="60" rowspan="3">Country</th>
						<th width="40" rowspan="3">Pattern NO</th>
						<th width="30" rowspan="3">Roll No</th>
						<th width="40" rowspan="3">Shade No</th>
						<th width="40" rowspan="3">Bundle No</th>
						<th width="15" rowspan="3">Barcode</th>
						<th width="60" rowspan="3">Bundle Qty.</th>
						<th rowspan="2" colspan="2" style="border-top:2px solid black;border-left:2px solid black;border-right:2px solid black;">RMG Number</th>
						<th width="40" rowspan="3">Size</th>
						<th rowspan="2" colspan="9" style="border-top:2px solid black;border-left:2px solid black;border-right:2px solid black;">Type of Defect</th>
						<th width="240" rowspan="3">Rejection Panel No</th>
						<th colspan="5" style="border-top:2px solid black;border-left:2px solid black;border-right:2px solid black;">Reject</th>
						<th width="60" rowspan="3">REP</th>
						<th rowspan="3">Signature</th>
					</tr>
					<tr>
						<th width="20" colspan="2" style="border-left:2px solid black;">Front</th>
						<th width="20" colspan="2">Sleeve</th>
						<th rowspan="2" style="border-bottom:2px solid black;border-right:2px solid black;">Back</th>
					</tr>
					<tr>
						<th width="20" style="border-bottom:2px solid black;border-left:2px solid black;">From</th>
						<th width="20" style="border-bottom:2px solid black;border-right:2px solid black;">to</th>

						<th style="font-size:12px; border-bottom:2px solid black;border-left:2px solid black;transform: rotate(-90deg);width: 40px;">Shading</th>

						<th style="font-size:12px; transform:rotate(-90deg); height:60px; width: 40px; border-bottom:2px solid black;">Slub</th>
						<th style="font-size:12px; transform:rotate(-90deg); height:60px; width: 40px; border-bottom:2px solid black;">Knot</th>
						<th style="font-size:12px; transform:rotate(-90deg); height:60px; width: 40px; border-bottom:2px solid black;">Hole</th>
						<th style="font-size:12px; transform:rotate(-90deg); height:60px; width: 40px; border-bottom:2px solid black;">End Out</th>
						<th style="font-size:12px; transform:rotate(-90deg); height:60px; width: 40px; border-bottom:2px solid black;">Spot</th>
						<th style="font-size:12px; transform:rotate(-90deg); height:60px; width: 40px; border-bottom:2px solid black;">Color Yarn</th>
						<th style="font-size:12px; transform:rotate(-90deg); height:60px; width: 40px; border-bottom:2px solid black;">Miss Yarn</th>
						<th style="font-size:12px; transform:rotate(-90deg); height:60px; width: 40px; border-bottom:2px solid black;border-right:2px solid black;">Think Yarn</th>

						<th width="40" style="border-bottom:2px solid black;border-left:2px solid black;">L</th>
						<th width="40" style="border-bottom:2px solid black;">R</th>
						<th width="40" style="border-bottom:2px solid black;">L</th>
						<th width="40" style="border-bottom:2px solid black;">R</th>
					</tr>
					<!-- <tr>
					<th width="30" rowspan="4">SL</th>
					<th width="150" rowspan="4">Order No</th>
					<th width="60" rowspan="4">Country</th>
					<th width="40" rowspan="4">Pattern No</th>
					<th width="30" rowspan="4">Roll No</th>
					<th width="40" rowspan="4">Shade no</th>
					<th width="40" rowspan="4">Bundle No</th>
					<th width="15" rowspan="4">Barcode</th>
					<th width="60" rowspan="4">Bundle Qty.</th>
					<th width="80" colspan="2" style="border-top:2px solid black;border-left:2px solid black;border-right:2px solid black;">RMG Number</th>
					<th width="40" rowspan="4">Size</th>
					<th rowspan="3" colspan="9" style="border-top:2px solid black;border-left:2px solid black;border-right:2px solid black;">Type of Defect	</th>
					<th width="240" rowspan="4" >Rejection Panel No</th>
					<th width="80" colspan="5" rowspan="2" style="border-top:2px solid black;border-left:2px solid black;border-right:2px solid black;">Reject</th>
					<th width="60" rowspan="4">REP</th>
					<th rowspan="4">Signature</th>
            	</tr>
              	<tr bgcolor="#dddddd" align="center">
					<th width="40" rowspan="3" style="border-bottom:2px solid black;border-left:2px solid black;">From</th>
					<th width="40" rowspan="3" style="border-bottom:2px solid black;border-right:2px solid black;">To</th>
                </tr>
				<tr bgcolor="#dddddd" align="center">				
					<th width="20" colspan="2" style="border-left:2px solid black;">Front</th>
					<th width="20" colspan="2">Sleeve</th>
					<th width="60" rowspan="2" style="border-bottom:2px solid black;border-right:2px solid black;">Back</th>
                </tr>
				<tr bgcolor="#dddddd" align="center" >				
					<th style="font-size:12px; border-bottom:2px solid black;border-left:2px solid black;transform: rotate(-90deg); width: 30px;">Shading</th>
					<th style="font-size:12px; transform:rotate(-90deg); height:60px; width: 40px; border-bottom:2px solid black;">Slub</th>
					<th style="font-size:12px; transform:rotate(-90deg); height:60px; width: 40px; border-bottom:2px solid black;">Knot</th>
					<th style="font-size:12px; transform:rotate(-90deg); height:60px; width: 40px; border-bottom:2px solid black;">Hole</th>
					<th style="font-size:12px; transform:rotate(-90deg); height:60px; width: 40px; border-bottom:2px solid black;">End Out</th>
					<th style="font-size:12px; transform:rotate(-90deg); height:60px; width: 40px; border-bottom:2px solid black;">Spot</th>
					<th style="font-size:12px; transform:rotate(-90deg); height:60px; width: 40px; border-bottom:2px solid black;">Color Yarn</th>
					<th style="font-size:12px; transform:rotate(-90deg); height:60px; width: 40px; border-bottom:2px solid black;">Miss Yarn</th>
					<th style="font-size:12px; transform:rotate(-90deg); height:60px; width: 40px; border-bottom:2px solid black;border-right:2px solid black;">Think Yarn</th>

					<th width="40" style="border-bottom:2px solid black;border-left:2px solid black;">L</th>
					<th width="40" style="border-bottom:2px solid black;">R</th>
					<th width="40" style="border-bottom:2px solid black;">L</th>
					<th width="40" style="border-bottom:2px solid black;">R</th>
                </tr> -->
				</thead>
				<tbody>
					<?
					$batchShadeNo_arr = array();

					$sqlRoll = sql_select("select a.id, a.batch_no, a.shade from pro_roll_details a, ppl_cut_lay_bundle b, ppl_cut_lay_mst c where a.id=b.roll_id and a.mst_id='$data[1]' and a.dtls_id='$data[2]' and a.entry_form=c.entry_form");
					//  var_dump($sqlRoll);
					foreach ($sqlRoll as $rrow) {
						$batchShadeNo_arr[$rrow[csf('id')]]['batch'] = $rrow[csf('batch_no')];
						$batchShadeNo_arr[$rrow[csf('id')]]['shade'] = $rrow[csf('shade')];
					}

					if ($data[4] == 0) $country_cond = "";
					else $country_cond = " and a.country_id='" . $data[4] . "'";
					$size_data = sql_select("select a.id,a.size_id,a.bundle_sequence,a.marker_qty from ppl_cut_lay_size_dtls a where a.mst_id='$data[1]' and a.dtls_id='$data[2]'  order by a.bundle_sequence ASC");
					$j = 1;
					foreach ($size_data as $size_val) {
						$total_marker_qty_size = 0;
						$bundle_data = sql_select("select a.id,a.bundle_no as bundle_no,a.bundle_num_prefix_no,a.size_id,a.number_start,a.number_end,a.size_qty, a.country_id, a.roll_no, a.roll_id, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no,a.barcode_no from ppl_cut_lay_bundle a where a.mst_id='$data[1]' and a.dtls_id='$data[2]' and a.size_id=" . $size_val[csf('size_id')] . " $country_cond order by a.id ASC");
						//var_dump ($bundle_data);
						//echo "select a.id,a.bundle_no as bundle_no,a.bundle_num_prefix_no,a.size_id,a.number_start,a.number_end,a.size_qty, a.country_id, a.roll_no, a.roll_id, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no from ppl_cut_lay_bundle a where a.mst_id='$data[1]' and a.dtls_id='$data[2]' and a.size_id=".$size_val[csf('size_id')]." $country_cond order by a.id ASC";
						foreach ($bundle_data as $row) {
					?>
							<tr>
								<td align="center"><? echo $j;  ?></td>
								<!-- <td align="center"><? //echo $cut_no; 
														?></td> -->
								<td style="word-wrap:break-word"><? echo $po_arr[$row[csf('order_id')]]; ?></td>
								<td style="word-wrap:break-word"><? echo $country_arr[$row[csf('country_id')]]; ?></td>
								<td align="center"><? echo $row[csf('pattern_no')]; ?></td>
								<td align="center"><? echo $row[csf('roll_no')]; ?></td>
								<td align="center" style="word-wrap:break-word"><?= $batchShadeNo_arr[$row[csf('roll_id')]]['shade']; ?></td>
								<td align="center"><? echo $row[csf('bundle_num_prefix_no')]; //$row[csf('bundle_num_prefix_no')];  
													?></td>
								<td align="center"><? echo $row[csf('barcode_no')]; ?></td>
								<td align="center"><? echo $row[csf('size_qty')];  ?></td>

								<td align="center"><? echo $row[csf('number_start')];  ?></td>
								<td align="center"><? echo $row[csf('number_end')];  ?></td>
								<td align="center"><? echo $size_arr[$row[csf('size_id')]];  ?></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
							</tr>
						<?
							$j++;
							$total_marker_qty_size += $row[csf('size_qty')];
							$total_marker_qty += $row[csf('size_qty')];
						}
						//  $total_marker_qty+=$size_val[csf('marker_qty')];
						?>
						<tr bgcolor="#eeeeee">
							<td align="center"></td>
							<td colspan="<?= $totTrSpan; ?>" align="right"><? echo $size_arr[$row[csf('size_id')]]; ?> Size Total</td>
							<td align="center"><? echo $total_marker_qty_size;  ?></td>
							<td align="center">&nbsp;</td>
							<td align="center">&nbsp;</td>
							<td align="center">&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td align="center">&nbsp;</td>
							<td align="center">&nbsp;</td>
							<td align="center">&nbsp;</td>
							<td align="center">&nbsp;</td>

							<td align="center">&nbsp;</td>
							<td align="center">&nbsp;</td>
						</tr>
					<?
					}
					?>
					<tr bgcolor="#BBBBBB">
						<td align="center"></td>
						<td colspan="<?= $totTrSpan; ?>" align="right"> Total marker qty.</td>
						<td align="center"><? echo $total_marker_qty;  ?></td>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td>
					</tr>
				</tbody>
			</table>

			<? echo signature_table(221, $data[0], "900px", "", "50"); ?>
		</div>
	</div>
 <?
	exit();
}

if ($action == "cut_lay_qc_bundle_print_3") //[QC Bundle-3
{
	extract($_REQUEST);
	$data = explode('*', $data);
	//print_r($data[5]);
	$btnType = $data[5];
	$country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");

	$company_library = array();
	$company_short_arr = array();
	$comapny_data = sql_select("select id, company_short_name, company_name from lib_company");
	foreach ($comapny_data as $comR) {
		$company_library[$comR[csf('id')]] = $comR[csf('company_name')];
		$company_short_arr[$comR[csf('id')]] = $comR[csf('company_short_name')];
	}

	$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$working_comp_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$working_location_library = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$working_floor_library = return_library_array("select id, floor_name from lib_prod_floor", "id", "floor_name");
	$table_no_library = return_library_array("select id,table_no  from  lib_cutting_table", "id", "table_no");
	$countryCodeArr = return_library_array("select id, ultimate_country_code from lib_country_loc_mapping", "id", "ultimate_country_code");

	$sql = "SELECT a.cut_num_prefix_no, a.table_no,a.cutting_no, a.job_no, a.entry_date,a.marker_length, a.marker_width, a.fabric_width, a.gsm, a.batch_id as batch_no,a.location_id,a.floor_id,a.working_company_id, b.order_id, b.color_id, b.gmt_item_id, b.plies, b.marker_qty, b.order_qty, b.order_ids, b.batch_id, b.order_cut_no
	from ppl_cut_lay_mst a, ppl_cut_lay_dtls b
	where a.id='$data[1]' 
	and a.company_id='$data[0]' 
	and b.id='$data[2]'
	
	 ";
	//echo $sql;
	$dataArray = sql_select($sql);
	$sql_buyer = sql_select("select buyer_name,style_ref_no as ref,style_description as des from wo_po_details_master where job_no='" . $dataArray[0][csf('job_no')] . "' and company_name=$data[0]");
	$style_ref = $sql_buyer[0][csf('ref')];
	$style_desc = $sql_buyer[0][csf('des')];
	$cut_no_prifix = $dataArray[0][csf('cut_num_prefix_no')];
	$cut_no = $dataArray[0][csf('cutting_no')];
	$order_cut_no = $dataArray[0][csf('order_cut_no')];

	$sql_lot = "SELECT a.batch_no
	FROM pro_roll_details a, ppl_cut_lay_bundle b, ppl_cut_lay_mst c
	where a.id=b.ROLL_ID
	and b.MST_ID=c.id
	and a.DTLS_ID=$data[2]
	and a.status_active=1 
	and a.is_deleted=0 
	and b.status_active=1 
	and b.is_deleted=0
	and c.status_active=1 
	and c.is_deleted=0 ";
	//echo $sql_lot;
	$data_lotArray = sql_select($sql_lot);
	$lot_no = $data_lotArray[0][csf("batch_no")];


	$po_arr = return_library_array("select id, po_number from wo_po_break_down where id in(" . $dataArray[0][csf('order_ids')] . ")", "id", "po_number");
	$batch_no = $dataArray[0][csf('batch_no')];
	$poCodeIdArr = array();
	$sqlCode = "SELECT po_break_down_id, country_id, size_number_id, code_id from wo_po_color_size_breakdown where po_break_down_id in (" . $dataArray[0][csf('order_ids')] . ") and status_active=1 and is_deleted=0 ";
	$sqlCodeData = sql_select($sqlCode);
	foreach ($sqlCodeData as $crow) {
		$poCodeIdArr[$crow[csf('po_break_down_id')]][$crow[csf('country_id')]][$crow[csf('size_number_id')]] = $countryCodeArr[$crow[csf('code_id')]];
	}
	unset($sqlCodeData);




	if ($btnType == 2) {
		$tblwidth = "1605";
		$totTrSpan = "7";
	}else{
		$totTrSpan = "7";
	}
	// else if($btnType==1) { $tblwidth="1340"; $totTrSpan="10"; }
 ?>
	<style>
		@page {
			size: auto;
			/* auto is the initial value */
			/* this affects the margin in the printer settings */
			margin-top: 18mm;
			margin-bottom: 18mm;
			margin-right: 2mm;
			margin-left: 2mm;
		}
	</style>
	<div style="width:1500px; " align="center">
		<table width="1500" cellspacing="0" align="center">
		<tr>
				<td colspan="8" align="center" style="font-size:20px">
					<strong><? echo $company_library[$data[0]]; ?></strong>
				</td>
			</tr>
			<tr>
				<td colspan="8" align="center" style="font-size:16px"><strong><u>100% Cut. Panel Inspection Report (Components Parts)</u></strong></td>
				<td width="60">&nbsp;</td>
				<td width="60">&nbsp;</td>
				<td align="left"><div id="barcode_img_id"></div></td>
			</tr>
			<tr>
				<td width="130"><strong>Cut No:</strong></td>
				<td width="200"><? echo $cut_no; ?></td>
				<td width="130"><strong>Table No :</strong></td>
				<td width="200"><? echo $table_no_library[$dataArray[0][csf('table_no')]]; ?></td>
				<td width="130"><strong>Job No :</strong></td>
				<td><? echo $dataArray[0][csf('job_no')]; ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><strong>Buyer:</strong></td>
				<td><? echo $buyer_library[$sql_buyer[0][csf('buyer_name')]]; ?></td>
				<td><strong>Batch No:</strong></td>
				<td><? echo $batch_no; ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><strong>Gmt Item:</strong></td>
				<td><? echo $garments_item[$dataArray[0][csf('gmt_item_id')]]; ?></td>
				<td><strong>Color :</strong></td>
				<td><? echo $color_library[$dataArray[0][csf('color_id')]]; ?></td>
				<td><strong>Marker Length :</strong></td>
				<td><? echo $dataArray[0][csf('marker_length')]; ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><strong>Marker Width :</strong></td>
				<td><? echo $dataArray[0][csf('marker_width')]; ?></td>
				<td><strong>Fabric Width:</strong></td>
				<td><? echo $dataArray[0][csf('fabric_width')]; ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><strong>Order Cut No:</strong></td>
				<td><? echo $order_cut_no; ?></td>
				<td><strong>Plies:</strong></td>
				<td><? echo $dataArray[0][csf('plies')]; ?></td>
				<td><strong>Cut Date:</strong></td>
				<td><? echo $dataArray[0][csf('entry_date')]; ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><strong>Style Ref:</strong></td>
				<td><? echo $style_ref; ?></td>
				<td><strong>Style Desc.:</strong></td>
				<td><? echo $style_desc; ?></td>
				<td align="left" colspan="2" ></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><strong>W. Company:</strong></td>
				<td><? echo $working_comp_library[$dataArray[0][csf('working_company_id')]]; ?></td>
				<td><strong>W. Location:</strong></td>
				<td><? echo $working_location_library[$dataArray[0][csf('location_id')]]; ?></td>
				<td><strong>W. Floor:</strong></td>
				<td><? echo $working_floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><strong>Gold Seal: </strong></td>
				<td><? echo $lot_no; ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>

			</tr>
			
		</table>
		<br>
		<script type="text/javascript" src="../../../js/jquery.js"></script>
		<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
		<script>
			function generateBarcode(valuess) {

				var value = valuess;
				var btype = 'code39';
				var renderer = 'bmp';
				var settings = {
					output: renderer,
					bgColor: '#FFFFFF',
					color: '#000000',
					barWidth: 1,
					barHeight: 30,
					moduleSize: 5,
					posX: 10,
					posY: 20,
					addQuietZone: 1
				};
				value = {
					code: value,
					rect: false
				};
				$("#barcode_img_id").show().barcode(value, btype, settings);
			}
			generateBarcode('<? echo $dataArray[0][csf('cutting_no')]; ?>');
		</script>
		<div style="width:<?= $tblwidth; ?>px;">
			<table align="center" cellspacing="0" width="<?= $tblwidth - 20; ?>" border="1" rules="all" class="rpt_table">
				<thead bgcolor="#dddddd" align="center">
					<tr>
						<th width="30" rowspan="3">Sl</th>
						<th width="150" rowspan="3">Order No</th>
						<th width="60" rowspan="3">Country</th>
						<th width="40" rowspan="3">Pattern NO</th>
						<th width="30" rowspan="3">Roll No</th>
						<th width="40" rowspan="3">Shade No</th>
						<th width="40" rowspan="3">Bundle No</th>
						<th width="15" rowspan="3">Barcode</th>
						<th width="60" rowspan="3">Bundle Qty.</th>
						<th rowspan="2" colspan="2" style="border-top:2px solid black;border-left:2px solid black;border-right:2px solid black;">RMG Number</th>
						<th width="40" rowspan="3">Size</th>
						<th rowspan="2" colspan="16" style="border-top:2px solid black;border-left:2px solid black;border-right:2px solid black;">Type of Defect</th>
						<th width="240" rowspan="3">Rejection Panel No</th>
						<th colspan="5" style="border-top:2px solid black;border-left:2px solid black;border-right:2px solid black;">Reject</th>
						<th width="60" rowspan="3" style="transform:rotate(-90deg);">Replace</th>
						<th rowspan="3">Signature</th>
					</tr>
					<tr>
						<th width="20" colspan="2" style="border-left:2px solid black;">Front</th>
						<th width="20" colspan="2">Back</th>
						<th rowspan="2" style="border-bottom:2px solid black;border-right:2px solid black;">Sleeve</th>
					</tr>
					<tr>
						<th width="20" style="border-bottom:2px solid black;border-left:2px solid black;">From</th>
						<th width="20" style="border-bottom:2px solid black;border-right:2px solid black;">to</th>

						<th style="font-size:12px; border-bottom:2px solid black;border-left:2px solid black;transform: rotate(-90deg);width: 40px;">Knot</th>
						<th style="font-size:12px; transform:rotate(-90deg); height:60px; width: 40px; border-bottom:2px solid black;">Slub</th>
						<th style="font-size:12px; transform:rotate(-90deg); height:60px; width: 40px; border-bottom:2px solid black;">Line Mark</th>
						<th style="font-size:12px; transform:rotate(-90deg); height:60px; width: 40px; border-bottom:2px solid black;">Miss Yarn</th>
						<th style="font-size:12px; transform:rotate(-90deg); height:60px; width: 40px; border-bottom:2px solid black;">Fly Yarn</th>

						<th style="font-size:12px; transform:rotate(-90deg); height:60px; width: 40px; border-bottom:2px solid black;">Loose Yarn</th>
						<th style="font-size:12px; transform:rotate(-90deg); height:60px; width: 40px; border-bottom:2px solid black;">Spot</th>
						<th style="font-size:12px; transform:rotate(-90deg); height:60px; width: 40px; border-bottom:2px solid black;">Stop Mark</th>
						<th style="font-size:12px; transform:rotate(-90deg); height:60px;  border-bottom:2px solid black;"><p style="width: 30px;">Cut Problem</p></th>
						<th style="font-size:12px; transform:rotate(-90deg); height:60px; width: 40px; border-bottom:2px solid black;">End Out</th>
						<th style="font-size:12px; transform:rotate(-90deg); height:60px; width: 40px; border-bottom:2px solid black;">Hole</th>
						<th style="font-size:12px; transform:rotate(-90deg); height:60px; width: 40px; border-bottom:2px solid black;"><p style="width: 30px;">Drop Needle</p></th>
						<th style="font-size:12px; transform:rotate(-90deg); height:60px; width: 40px; border-bottom:2px solid black;"><p style="width: 30px;">Shading</p></th>
						<th style="font-size:12px; transform:rotate(-90deg); height:60px; width: 40px; border-bottom:2px solid black;">Color Yarn</th>
						<th style="font-size:12px; transform:rotate(-90deg); height:60px; width: 40px; border-bottom:2px solid black;">Think Yarn</th>
						<th style="font-size:12px; transform:rotate(-90deg); height:60px; width: 40px; border-bottom:2px solid black;border-right:2px solid black;">Others</th>

						<th width="40" style="border-bottom:2px solid black;border-left:2px solid black;">R</th>
						<th width="40" style="border-bottom:2px solid black;">L</th>
						<th width="40" style="border-bottom:2px solid black;">R</th>
						<th width="40" style="border-bottom:2px solid black;">L</th>
					</tr>
				</thead>
				<tbody>
					<?
					$batchShadeNo_arr = array();

					$sqlRoll = sql_select("select a.id, a.batch_no, a.shade from pro_roll_details a, ppl_cut_lay_bundle b, ppl_cut_lay_mst c where a.id=b.roll_id and a.mst_id='$data[1]' and a.dtls_id='$data[2]' and a.entry_form=c.entry_form");
					//  var_dump($sqlRoll);
					foreach ($sqlRoll as $rrow) {
						$batchShadeNo_arr[$rrow[csf('id')]]['batch'] = $rrow[csf('batch_no')];
						$batchShadeNo_arr[$rrow[csf('id')]]['shade'] = $rrow[csf('shade')];
					}

					if ($data[4] == 0) $country_cond = "";
					else $country_cond = " and a.country_id='" . $data[4] . "'";
					$size_data = sql_select("select a.id,a.size_id,a.bundle_sequence,a.marker_qty from ppl_cut_lay_size_dtls a where a.mst_id='$data[1]' and a.dtls_id='$data[2]'  order by a.bundle_sequence ASC");
					$j = 1;
					foreach ($size_data as $size_val) {
						$total_marker_qty_size = 0;
						$bundle_data = sql_select("select a.id,a.bundle_no as bundle_no,a.bundle_num_prefix_no,a.size_id,a.number_start,a.number_end,a.size_qty, a.country_id, a.roll_no, a.roll_id, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no,a.barcode_no from ppl_cut_lay_bundle a where a.mst_id='$data[1]' and a.dtls_id='$data[2]' and a.size_id=" . $size_val[csf('size_id')] . " $country_cond order by a.id ASC");
						//var_dump ($bundle_data);
						//echo "select a.id,a.bundle_no as bundle_no,a.bundle_num_prefix_no,a.size_id,a.number_start,a.number_end,a.size_qty, a.country_id, a.roll_no, a.roll_id, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no from ppl_cut_lay_bundle a where a.mst_id='$data[1]' and a.dtls_id='$data[2]' and a.size_id=".$size_val[csf('size_id')]." $country_cond order by a.id ASC";
						foreach ($bundle_data as $row) {
					?>
							<tr>
								<td align="center"><? echo $j;  ?></td>
								<!-- <td align="center"><? //echo $cut_no; 
														?></td> -->
								<td style="word-wrap:break-word"><? echo $po_arr[$row[csf('order_id')]]; ?></td>
								<td style="word-wrap:break-word"><? echo $country_arr[$row[csf('country_id')]]; ?></td>
								<td align="center"><? echo $row[csf('pattern_no')]; ?></td>
								<td align="center"><? echo $row[csf('roll_no')]; ?></td>
								<td align="center" style="word-wrap:break-word"><?= $batchShadeNo_arr[$row[csf('roll_id')]]['shade']; ?></td>
								<td align="center"><? echo $row[csf('bundle_num_prefix_no')]; //$row[csf('bundle_num_prefix_no')];  
													?></td>
								<td align="center"><? echo $row[csf('barcode_no')]; ?></td>
								<td align="center"><? echo $row[csf('size_qty')];  ?></td>

								<td align="center"><? echo $row[csf('number_start')];  ?></td>
								<td align="center"><? echo $row[csf('number_end')];  ?></td>
								<td align="center"><? echo $size_arr[$row[csf('size_id')]];  ?></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>

								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
							</tr>
						<?
							$j++;
							$total_marker_qty_size += $row[csf('size_qty')];
							$total_marker_qty += $row[csf('size_qty')];
						}
						//  $total_marker_qty+=$size_val[csf('marker_qty')];
						?>
						<tr bgcolor="#eeeeee">
							<td align="center"></td>
							<td colspan="<?= $totTrSpan; ?>" align="right"><? echo $size_arr[$row[csf('size_id')]]; ?> Size Total</td>
							<td align="center"><? echo $total_marker_qty_size;  ?></td>
							<td align="center">&nbsp;</td>
							<td align="center">&nbsp;</td>
							<td align="center">&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>

							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<!-- <td>&nbsp;</td>
							<td align="center">&nbsp;</td>
							<td align="center">&nbsp;</td>
							<td align="center">&nbsp;</td>
							<td align="center">&nbsp;</td>

							<td align="center">&nbsp;</td>
							<td align="center">&nbsp;</td> -->
						</tr>
					<?
					}
					?>
					<tr bgcolor="#BBBBBB">
						<td align="center"></td>
						<td colspan="<?= $totTrSpan; ?>" align="right"> Total marker qty.</td>
						<td align="center"><? echo $total_marker_qty;  ?></td>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>

						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<!-- <td>&nbsp;</td>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td> -->
					</tr>
				</tbody>
			</table>

			<? echo signature_table(221, $data[0], "900px", "", "50"); ?>
		</div>
	</div>
 <?
	exit();
}


if ($action == "job_search_popup") 
{
	echo load_html_head_contents("Batch Info", "../../../", 1, 1, '', '1', '');
	extract($_REQUEST);

 ?>
	<script>
		function js_set_order(strCon) {
			document.getElementById('hidden_job_no').value = strCon;
			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<div align="center" style="width:100%; overflow-y:hidden;">
			<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				<table width="1320" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
					<thead>
						<tr>
							<th width="150">Company name</th>
							<th width="150">Buyer name</th>
							<th width="100">Brand</th>
							<th width="100">Season</th>
							<th width="100">Season Year</th>
							<th width="60">Job No</th>
							<th width="100">Style Ref.</th>
							<th width="100">Order No</th>
							<th width="100">File No</th>
							<th width="100">Internal Ref. No</th>
							<th width="220">Date Range</th>
							<th width=""><input type="reset" name="re_button" id="re_button" value="Reset" style="width:70px" class="formbutton" /></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<?
								echo create_drop_down("cbo_company_name", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0  order by company_name", "id,company_name", 1, "-- Select Company --", $cbo_company_id, "", 1);
								?>
							</td>
							<td align="center" width="150">
								<?
								$sql = "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and b.tag_company=$cbo_company_id and status_active=1 and is_deleted=0 order by a.buyer_name";
								echo create_drop_down("cbo_buyer_name", 140, $sql, "id,buyer_name", 1, "-- Select --", 0, "load_drop_down( 'woven_cut_and_lay_ratio_wise_entry_v3_controller',this.value, 'load_drop_down_brand', 'brand_td');load_drop_down( 'woven_cut_and_lay_ratio_wise_entry_v3_controller',this.value, 'load_drop_down_buyer_season', 'buyer_season_td')", 0, "5,6,7", "", "", "");
								?>
								<input type="hidden" id="hidden_job_qty" name="hidden_job_qty" />
								<input type="hidden" id="hidden_sip_date" name="hidden_sip_date" />
								<input type="hidden" id="hidden_prifix" name="hidden_prifix" />
								<input type="hidden" id="hidden_job_no" name="hidden_job_no" />
							</td>
							<td id="brand_td">
								<?
								echo create_drop_down("cbo_brand_name", 100, $blank_array, "", 1, "-- Select Brand --", $selected, "", 0, "");
								?>
							</td>
							<td id="buyer_season_td">
								<?
								echo create_drop_down("cbo_buyer_season_name", 100, $blank_array, "", 1, "-- Select Brand --", $selected, "", 0, "");
								?>
							</td>
							<td width="100">
								<?
								echo create_drop_down("cbo_year", 100, $year, "", 1, "--Year--", 0, "", 0);
								?>
							</td>
							<td width="60">
								<input style="width:50px;" type="text" class="text_boxes" name="txt_job_prifix" id="txt_job_prifix" />
							</td>
							<td width="100">
								<input style="width:90px;" type="text" class="text_boxes" name="txt_style_no" id="txt_style_no" />
							</td>
							<td width="100">
								<input style="width:90px;" type="text" class="text_boxes" name="txt_po_no" id="txt_po_no" />
							</td>
							<td width="100">
								<input style="width:80px;" type="text" class="text_boxes" name="txt_file_no" id="txt_file_no" />
							</td>
							<td width="100">
								<input style="width:80px;" type="text" class="text_boxes" name="txt_internal_ref" id="txt_internal_ref" />
							</td>
							<td align="center" width="220">
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
							</td>
							<td align="center">
								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_po_no').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_style_no').value+'_'+document.getElementById('cbo_brand_name').value+'_'+document.getElementById('cbo_buyer_season_name').value+'_'+document.getElementById('cbo_year').value, 'create_job_search_list_view', 'search_div', 'woven_cut_and_lay_ratio_wise_entry_v3_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
							</td>
						</tr>
						<tr>
							<td align="center" height="40" valign="middle" colspan="12">
								<? echo load_month_buttons(1);  ?>
							</td>
						</tr>
					</tbody>
					</tr>
				</table>
				<div align="center" valign="top" id="search_div"> </div>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
 <?
}
if ($action == "create_job_search_list_view") 
{
	$ex_data = explode("_", $data);
	// echo "<pre>";
	// print_r($ex_data);
	$company = $ex_data[0];
	$buyer = $ex_data[1];
	$from_date = $ex_data[2];
	$to_date = $ex_data[3];
	$job_prifix = $ex_data[4];
	$job_year = $ex_data[5];
	$po_no = $ex_data[6];
	$file_no = $ex_data[7];
	$internal_reff = $ex_data[8];
	$style_reff = $ex_data[9];
	$brand_name = $ex_data[10];
	$season_name = $ex_data[11];
	$season_year = $ex_data[12];
	$job_cond = "";

	if (str_replace("'", "", $company) == "") $conpany_cond = "";
	else $conpany_cond = "and b.company_name=" . str_replace("'", "", $company) . "";
	if (str_replace("'", "", $buyer) == 0) $buyer_cond = "";
	else $buyer_cond = "and b.buyer_name=" . str_replace("'", "", $buyer) . "";
	if ($db_type == 2) $year_cond = " and extract(year from b.insert_date)=$job_year";
	if ($db_type == 0) $year_cond = " and SUBSTRING_INDEX(b.insert_date, '-', 1)=$job_year";
	if (str_replace("'", "", $job_prifix) != "")  $job_cond = "and b.job_no_prefix_num=" . str_replace("'", "", $job_prifix) . "  $year_cond";
	if (str_replace("'", "", $po_no) != "")  $order_cond = "and a.po_number like '%" . str_replace("'", "", $po_no) . "%' ";
	else $order_cond = "";

	if (str_replace("'", "", $file_no) != "")  $file_cond = "and a.file_no like '%" . str_replace("'", "", $file_no) . "%' ";
	else $file_cond = "";

	if (str_replace("'", "", $style_reff) != "")  $style_cond = "and b.style_ref_no like '%" . str_replace("'", "", $style_reff) . "%' ";
	else $style_cond = "";
	if (str_replace("'", "", $internal_reff) != "")  $internal_reff_cond = " and a.grouping like '%" . str_replace("'", "", $internal_reff) . "%' ";
	else $internal_reff_cond = "";

	if (str_replace("'", "", $brand_name) == 0) $brand_name_cond = "";
	else $brand_name_cond = "and b.brand_id=" . str_replace("'", "", $brand_name) . "";
	if (str_replace("'", "", $season_name) == 0) $season_name_cond = "";
	else $season_name_cond = "and b.season_buyer_wise=" . str_replace("'", "", $season_name) . "";
	if (str_replace("'", "", $season_year) == 0) $season_year_cond = "";
	else $season_year_cond = "and b.season_year=" . str_replace("'", "", $season_year) . "";

	// Last Process Production Controll variable setting
	$gmts_prod_confirmation = return_field_value("is_control", "variable_settings_production", "variable_list=33 and preceding_page_id=288 and company_name=$company");

	if ($db_type == 0) {
		if ($from_date != "" && $to_date != "") $sql_cond = " and a.pub_shipment_date  between '" . change_date_format($from_date, 'yyyy-mm-dd') . "' and '" . change_date_format($to_date, 'yyyy-mm-dd') . "'";
		$sql_order = "SELECT b.job_no,b.buyer_name,a.po_number,a.pub_shipment_date,b.style_ref_no,b.job_no_prefix_num as job_prefix,SUBSTRING_INDEX(b.insert_date, '-', 1) as year from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no $buyer_cond  $sql_cond $conpany_cond $job_cond $order_cond $file_cond $internal_reff_cond $style_cond $brand_name_cond $season_name_cond $season_year_cond and a.shiping_status<>3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by b.buyer_name,b.job_no,a.po_number ";
	}

	if ($db_type == 2) {
		if (str_replace("'", "", $from_date) != "" && str_replace("'", "", $to_date) != "") {
			$sql_cond = " and a.pub_shipment_date  between '" . date("j-M-Y", strtotime($from_date)) . "' and '" . date("j-M-Y", strtotime($to_date)) . "'";
		}

		if ($gmts_prod_confirmation == 1) //Garments Production Confirmation
		{
			$sql_order = "SELECT b.id,b.job_no,b.buyer_name,b.brand_id,a.po_number,a.pub_shipment_date,b.style_ref_no,b.job_no_prefix_num as job_prefix,extract(year from b.insert_date) as year,a.file_no,a.grouping from wo_po_details_master b,wo_po_break_down a,gmts_production_confirmation c where a.job_id=b.id and a.id=c.po_id and c.confirm_status=1 $buyer_cond  $sql_cond $conpany_cond $job_cond $order_cond $file_cond $internal_reff_cond $style_cond $brand_name_cond $season_name_cond $season_year_cond $job_cond and a.shiping_status<>3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by  b.id,b.job_no,b.buyer_name,b.brand_id, a.po_number,a.pub_shipment_date,b.style_ref_no,b.job_no_prefix_num, b.insert_date,a.file_no,a.grouping order by b.id DESC";
		} else {

			$sql_order = "SELECT b.job_no,b.buyer_name,a.po_number,a.pub_shipment_date,b.style_ref_no,b.job_no_prefix_num as job_prefix,extract(year from b.insert_date) as year,a.file_no,a.grouping, b.brand_id,b.season_year,b.season_buyer_wise,b.body_wash_color,a.id from wo_po_details_master b,wo_po_break_down a where a.job_id=b.id $buyer_cond  $sql_cond $conpany_cond $job_cond $order_cond $file_cond $internal_reff_cond $style_cond $brand_name_cond $season_name_cond $season_year_cond  and a.shiping_status<>3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by  b.job_no,b.buyer_name, a.po_number,a.pub_shipment_date,b.style_ref_no,b.job_no_prefix_num, b.insert_date,a.file_no,a.grouping, b.brand_id,b.season_year,b.season_buyer_wise,b.body_wash_color,a.id order by  job_no_prefix_num";
		}
	}
	// echo $sql_order;
	$order_id_arr = array();
	foreach (sql_select($sql_order) as $val) {
		$order_id_arr[$val['ID']] = $val['ID'];
	}
	$order_id_cond = where_con_using_array($order_id_arr, 0, "po_break_down_id");
	$actual_sql = sql_select("SELECT po_break_down_id, acc_po_no from  wo_po_acc_po_info where status_active=1 and is_deleted=0 $order_id_cond");
	$actual_po_arr = array();
	foreach ($actual_sql as $v) {
		$actual_po_arr[$v['PO_BREAK_DOWN_ID']] .= ($actual_po_arr[$v['PO_BREAK_DOWN_ID']] == "") ? $v['ACC_PO_NO'] : "," . $v['ACC_PO_NO'];
	}
	// print_r($actual_po_arr);
	$buyer_arr = return_library_array("select id, buyer_name from  lib_buyer", 'id', 'buyer_name');
	$buyer_brand_arr = return_library_array("select id, brand_name from lib_buyer_brand", 'id', 'brand_name');
	$buyer_season_arr = return_library_array("select id, season_name from  lib_buyer_season", 'id', 'season_name');
	$body_wash_color_arr = return_library_array("select id, color_name from  lib_color", 'id', 'color_name');

	$arr = array(1 => $buyer_brand_arr, 2 => $buyer_season_arr, 6 => $body_wash_color_arr, 7 => $buyer_arr, 11 => $actual_po_arr);

	echo create_list_view("list_view", "Job NO,Brand, Season, Season Year,Year,Style Ref,Body/Wash Color,Buyer Name,File No,Internal Ref. No, Order No,Acc PO,Shipment Date", "50,100,100,40,40,150,100,150,50,100,100,70,40", "1220", "270", 0, $sql_order, "js_set_order", "job_no,buyer_name,year", "", 1, "0,brand_id,season_buyer_wise,0,0,0,body_wash_color,buyer_name,0,0,0,id,0,0", $arr, "job_prefix,brand_id,season_buyer_wise,season_year,year,style_ref_no,body_wash_color,buyer_name,file_no,grouping,po_number,id,pub_shipment_date", "", "setFilterGrid('list_view',-1)");
}

if($action=="country_popup")
{
	echo load_html_head_contents("Country Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	//print_r($_REQUEST);die;
	//$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name'); 
	//$time_weight_panel;
	//echo $hidden_body_partstring;die;
	$yes_no_query="SELECT b.company_name,b.variable_list,b.country_seq from variable_settings_production b WHERE b.variable_list=161  and b.company_name=$company_id ";
		// echo $yes_no_query;die();

		$yes_no=sql_select($yes_no_query);
	?>
		<script>
			var hiddiscountryseq='<?=$yes_no[0]["COUNTRY_SEQ"]; ?>';
			var isSeqUse='<?=$isSeqUse; ?>';
			//alert(isSeqUse)
			$(document).ready(function(e) {
				setFilterGrid('tbl_list_search',-1);
			});
			
			var selected_id = new Array(); var selected_name = new Array(); var selected_seq = new Array();
			
			function check_all_data() 
			{
				/*var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 
				//alert(tbl_row_count)
				tbl_row_count = tbl_row_count-1;
				//alert(tbl_row_count)
				for( var i = 1; i <= tbl_row_count; i++ ) 
				{
					//var country_id=$('#txt_individual_id'+i).val();
					alert(i)
					js_set_value( i );
				}*/
				document.getElementById('chk_is_seq').checked=false;
				document.getElementById('chk_is_seq').value=0;
				
				$("#tbl_list_search tr").each(function() {
					var valTP=$(this).attr("id");
					if( valTP!=undefined )
					{
						//alert(valTP)
						$("#"+valTP).click();
					}
				});
			}
			
			function toggle( x, origColor ) {
				var newColor = 'yellow';
				if ( x.style ) {
					x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
				}
			}
			
			function set_all()
			{
				var old=document.getElementById('txt_po_row_id').value; 
				
				if(hiddiscountryseq==1 || isSeqUse==1)
				{
					document.getElementById('chk_is_seq').checked=true;
					document.getElementById('chk_is_seq').value=1;
					
					if(old!="")
					{   
						old=old.split(",");
						for(var k=0; k<old.length; k++)
						{  
							var seqdata=old[k].split("!");
							
							if(typeof(seqdata[1])!= 'undefined')
							{
								js_set_value( seqdata[0] );
							}
						} 
					}
				}
				else
				{
					document.getElementById('chk_is_seq').checked=false;
					document.getElementById('chk_is_seq').value=0;
					
					if(old!="")
					{   
						old=old.split(",");
						for(var k=0; k<old.length; k++)
						{  
							js_set_value(old[k]);
						} 
					}
				}
			}
			
			function js_set_value( str ) 
			{
				//alert($('#chk_is_seq').val());
				if($('#chk_is_seq').val()==0)
				{
					toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
					if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
						selected_id.push( $('#txt_individual_id' + str).val() );
						selected_name.push( $('#txt_individual' + str).val() );
						//selected_seq.push( $('#txtseqno_' + str).val() );
					}
					else {
						for( var i = 0; i < selected_id.length; i++ ) {
							if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
						}
						selected_id.splice( i, 1 );
						selected_name.splice( i, 1 );
						//selected_seq.splice( i, 1 );
					}
					
					var id = ''; var name = ''; //var seq = '';
					for( var i = 0; i < selected_id.length; i++ ) {
						id += selected_id[i] + ',';
						name += selected_name[i] + ',';
						//seq += selected_seq[i] + ',';
					}
					
					id = id.substr( 0, id.length - 1 );
					name = name.substr( 0, name.length - 1 );
					//seq = seq.substr( 0, seq.length - 1 );
					
					$('#hidden_search_id').val(id);
					$('#hidden_search_name').val(name);
				}
				else if($('#chk_is_seq').val()==1)
				{
					var seqno=$('#txtseqno_'+str).val()*1;
					//alert(seqno)
					if(seqno>0 )
					{
						toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
						
						var countryid_seq=$('#txt_individual_id' + str).val()+'!'+seqno;
						//alert(countryid_seq)
						
						if( jQuery.inArray( countryid_seq, selected_id ) == -1 ) {
							selected_id.push( countryid_seq );
							selected_name.push( $('#txt_individual' + str).val() );
							//selected_seq.push( $('#txtseqno_' + str).val() );
						}
						else {
							for( var i = 0; i < selected_id.length; i++ ) {
								if( selected_id[i] == countryid_seq ) break;
							}
							selected_id.splice( i, 1 );
							selected_name.splice( i, 1 );
							//$('#txtseqno_'+str).val('')
							//selected_seq.splice( i, 1 );
						}
						var id = ''; var name = ''; //var seq = '';
						for( var i = 0; i < selected_id.length; i++ ) {
							id += selected_id[i] + ',';
							name += selected_name[i] + ',';
							//seq += selected_seq[i] + ',';
						}
						
						id = id.substr( 0, id.length - 1 );
						name = name.substr( 0, name.length - 1 );
						//seq = seq.substr( 0, seq.length - 1 );
						//alert(id,name)
						$('#hidden_search_id').val(id);
						$('#hidden_search_name').val(name);

					}
					else
					{
						toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
					}
				}
			}
			
			function fnc_seq()
			{
				if(document.getElementById('chk_is_seq').checked==false) document.getElementById('chk_is_seq').value=0;
				else if(document.getElementById('chk_is_seq').checked==true) document.getElementById('chk_is_seq').value=1;
			}
	    </script>

	</head>
	<?
		
		$yes_no_arr=array();
		foreach($yes_no as $val)
		{
			$country_seq=$val[csf('country_seq')];
		}
		// echo $country_seq;die();
		if($country_seq!=1){$disable="disabled";}
		if($country_seq==1) {$checked = "checked";$val=1;}
	?>
	<body>
	<div align="center">
		<fieldset style="width:400px;margin-left:10px">
	    	<input type="hidden" name="hidden_search_id" id="hidden_search_id" class="text_boxes" value="">
	        <input type="hidden" name="hidden_search_name" id="hidden_search_name" class="text_boxes" value="">
	        <form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
	            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="390" class="rpt_table" >
	                <thead>
	                    <th width="40">SL</th>
	                    <th width="200">Country name</th>
                        <th width="70">Country Ship Date</th>
						
                        <th>Seq.<input disabled <?=$checked; ?> type="checkbox" name="chk_is_seq" id="chk_is_seq"  style="width:12px;" ></th>
					
	                </thead>
	            </table>
	            <div style="width:390px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
	                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="370" class="rpt_table" id="tbl_list_search" >
	                <?
	                    $i=1;
						

						if($country_seq==1)
						{
							// $sql_country = "SELECT a.id, a.country_name,b.country_ship_date,b.country_id from lib_country a, wo_po_color_size_breakdown b where a.id=b.country_id and b.job_no_mst='$txt_job' and b.color_number_id='$cbocolor' and b.item_number_id='$gmt_id' and b.po_break_down_id in($poId) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   group by a.id, a.country_name,b.country_ship_date,b.country_id";
							$sql_country="SELECT a.country_id, b.country_name, a.country_ship_date from  wo_po_color_size_breakdown a,lib_country b where a.country_id=b.id and  a.po_break_down_id in($poId) and a.item_number_id=".$gmt_id." and a.color_number_id in($cbocolor) and a.status_active=1 order by a.country_ship_date ASC";
						}
						else 
						{
							$sql_country = "SELECT a.id, a.country_name,b.country_ship_date,b.country_id from lib_country a, wo_po_color_size_breakdown b where a.id=b.country_id and b.job_no_mst='$txt_job' and b.color_number_id in($cbocolor) and b.item_number_id='$gmt_id' and b.po_break_down_id in($poId) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by a.id, a.country_name,b.country_ship_date,b.country_id order by b.country_ship_date";
						}
	                //    echo $sql_country;die;
						$result_country=sql_select($sql_country);
						foreach ($result_country as  $value) {
							$country_id_arr[$value[csf('country_id')]]['cname']=$value[csf('country_name')];
							$country_id_arr[$value[csf('country_id')]]['cshipdate']=$value[csf('country_ship_date')];
						}
						unset($result_country);
						
						$excountry=explode(",",$hidden_country_id);
						$countrySeqArr=array();
						foreach($excountry as $cexdata)
						{
							$exseq=explode("!",$cexdata);
							$countrySeqArr[$exseq[0]]=$exseq[1];
						}
						
	                    foreach($country_id_arr as $country_id=>$countryval)
	                    {
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$country_id; ?>" onClick="js_set_value(<?=$country_id; ?>);"> 
								<td width="40" align="center" ><?=$i; ?>
									<input type="hidden" id="txt_individual_id<?=$country_id; ?>" name="txt_individual_id<?=$country_id; ?>" value="<?=$country_id; ?>" />
									<input type="hidden" id="txt_individual<?=$country_id; ?>" name="txt_individual<?=$country_id; ?>" value="<?=$countryval['cname']; ?>" />
								</td>	
								<td width="200" style="word-break:break-all"><?=$countryval['cname']; ?></td>
                                <td width="70" style="word-break:break-all"><?=change_date_format($countryval['cshipdate']); ?></td>
								
                                <td><input <?=$disable;?> type="text" id="txtseqno_<?=$country_id; ?>" name="txtseqno_<?=$country_id; ?>" class="text_boxes_numeric" onBlur="js_set_value(<?=$country_id; ?>);" value="<?=$countrySeqArr[$country_id]; ?>" style="width:30px" /></td>
								
							</tr>
							<?
							$i++;
	                    }
	                ?>
	                </table>
	            </div>
	             <table width="370" cellspacing="0" cellpadding="0" style="border:none" align="center">
	                <tr>
	                    <td align="center" height="30" valign="bottom">
	                        <div style="width:100%"> 
                            	<div style="width:50%; float:left" align="left">
                                    <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data();" /> Check / Uncheck All
                                </div>
	                            <div style="width:50%; float:left" align="left">
	                                <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" /><!---->
	                                <input type="hidden" name="txt_po_row_id" id="txt_po_row_id" value="<?=$hidden_country_id; ?>"/>
	                            </div>
	                        </div>
	                    </td>
	                </tr>
	            </table>
	        </form>
	    </fieldset>
	</div>    
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		set_all();
	</script>
	</html>
	<?
	exit();
}

if ($action == "po_popup") 
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1, '', '1', '');
	extract($_REQUEST);
	//echo "<pre>";print_r($_REQUEST);
	$po_sec = explode(",",$posecno);
	// $po_id_arr = array();
	$po_id_sec_arr = array();
	foreach ($po_sec as $v) 
	{
		$ex_d = explode("!",$v);
		// $po_id_arr[$ex_d[0]] = $ex_d[0];
		$po_id_sec_arr[$ex_d[0]] = $ex_d[1];
	}
	//echo "<pre>";print_r($_REQUEST);
	// $poId = implode(",",$po_id_arr);
	$yes_no_query="SELECT b.order_priority from variable_settings_production b WHERE b.variable_list=162  and b.company_name=$company_id ";
		// echo $yes_no_query;die();

	$yes_no=sql_select($yes_no_query);
	?>
	<script>
		
		var hiddisorderseq='<?=$yes_no[0]["ORDER_PRIORITY"]; ?>';
		var isSeqUse='<?=$isSeqUse; ?>';

		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search', -1);
		});

		var selected_id = new Array();
		var selected_name = new Array();
		var selected_seq = new Array();

		function check_all_data() {
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;

			tbl_row_count = tbl_row_count - 1;
			for (var i = 1; i <= tbl_row_count; i++) {
				js_set_value(i);
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function set_all() 
		{
			var old = document.getElementById('txt_po_row_id').value;
			if(hiddisorderseq==1 || isSeqUse==1)
			{
				document.getElementById('chk_is_seq').checked=true;
				document.getElementById('chk_is_seq').value=1;
				if (old != "") {
					var oldd = old.split(",");
					var n = 0;
					for (var k = 0; k < oldd.length; k++) 
					{
						// var seqdata=oldd[k].split("!");
						// if(typeof(seqdata[1])!= 'undefined')
						// {
							js_set_value(oldd[k])
						// 	//alert(oldd[n]+'--'+k)
						// 	n++;
						// }
					}
				}
			}
			else
			{		
				document.getElementById('chk_is_seq').checked=false;
				document.getElementById('chk_is_seq').value=0;

				if (old != "") {
					var oldd = old.split(",");
					var n = 0;
					for (var k = 0; k < oldd.length; k++) {
						js_set_value(oldd[k])
						//alert(oldd[n]+'--'+k)
						n++;
					}
				}
			}
		}

		function js_set_value(str) 
		{
			if($('#chk_is_seq').val()==0)
			{
				toggle(document.getElementById('search' + str), '#FFFFCC');

				if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1) {
					selected_id.push($('#txt_individual_id' + str).val());
					selected_name.push($('#txt_individual' + str).val());

				} else {
					for (var i = 0; i < selected_id.length; i++) {
						if (selected_id[i] == $('#txt_individual_id' + str).val()) break;
					}
					selected_id.splice(i, 1);
					selected_name.splice(i, 1);
				}

				var id = '';
				var name = '';
				for (var i = 0; i < selected_id.length; i++) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ',';
				}

				id = id.substr(0, id.length - 1);
				name = name.substr(0, name.length - 1);

				$('#po_id').val(id);
				$('#po_no').val(name);
			}
			else if($('#chk_is_seq').val()==1)
			{
				// alert($('#chk_is_seq').val());return;
				var seqno=$('#txtseqno_'+str).val()*1;
				if(seqno>0 )
				{
					toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
						
					var countryid_seq=$('#txt_individual_id' + str).val()+'!'+seqno;
					// alert(countryid_seq)
					
					if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
						selected_id.push( $('#txt_individual_id' + str).val() );
						selected_name.push( $('#txt_individual' + str).val() );
						selected_seq.push( countryid_seq );
					}
					else {
						for( var i = 0; i < selected_id.length; i++ ) {
							if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
						}
						selected_id.splice( i, 1 );
						selected_name.splice( i, 1 );
						//$('#txtseqno_'+str).val('')
						selected_seq.splice( i, 1 );
					}
					var id = ''; var name = ''; var seq = '';
					for( var i = 0; i < selected_id.length; i++ ) {
						id += selected_id[i] + ',';
						name += selected_name[i] + ',';
						seq += selected_seq[i] + ',';
					}
					
					id = id.substr( 0, id.length - 1 );
					name = name.substr( 0, name.length - 1 );
					seq = seq.substr( 0, seq.length - 1 );
					//alert(id,name)

					$('#po_id').val(id);
					$('#po_no').val(name);
					$('#po_sec_no').val(seq);
				}
				else
				{
					toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
				}
			}
		}		
			
		function fnc_seq()
		{
			if(document.getElementById('chk_is_seq').checked==false) document.getElementById('chk_is_seq').value=0;
			else if(document.getElementById('chk_is_seq').checked==true) document.getElementById('chk_is_seq').value=1;
		}
	</script>

	</head>
	<?
		
		$yes_no_arr=array();
		foreach($yes_no as $val)
		{
			$order_seq=$val[csf('order_priority')];
		}
		// echo $country_seq;die();
		if($order_seq!=1){$disable="disabled";}
		if($order_seq==1) {$checked = "checked";}
	?>
	<body>
		<div align="center">
			<fieldset style="width:400px;margin-left:10px">
				<input type="hidden" name="po_id" id="po_id" class="text_boxes" value="">
				<input type="hidden" name="po_no" id="po_no" class="text_boxes" value="">
				<input type="hidden" name="po_sec_no" id="po_sec_no" class="text_boxes" value="">
				<form name="searchprocessfrm_1" id="searchprocessfrm_1" autocomplete="off">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="390" class="rpt_table">
						<thead>
							<th width="40">SL</th>
							<th width="150">PO No.</th>
							<th width="100">Shipment Date</th>
                        	<th>Seq.<input <?=$checked;?> disabled type="checkbox" name="chk_is_seq" id="chk_is_seq" onClick="fnc_seq();"  style="width:12px;" ></th>
						</thead>
					</table>
					<div style="width:390px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="370" class="rpt_table" id="tbl_list_search">
							<?
							$is_projected_po_allow = return_field_value("production_entry", "variable_settings_production", "variable_list=58 and company_name=$company_id");
							$projected_po_cond = ($is_projected_po_allow == 2) ? " and a.is_confirmed=1" : "";

							$i = 1;
							$po_row_id = '';
							$poIdArr = explode(",", $poId);
							$sql = "SELECT a.id, a.po_number, a.pub_shipment_date from wo_po_break_down a, wo_po_color_size_breakdown b where a.id=b.po_break_down_id and a.job_no_mst='$txt_job' and b.color_number_id in($cbocolor) and b.item_number_id='$gmt_id' $projected_po_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.po_number, a.pub_shipment_date order by a.pub_shipment_date";
							// echo $sql;die;
							$result = sql_select($sql);
							foreach ($result as $row) {
								if ($i % 2 == 0) $bgcolor = "#E9F3FF";
								else $bgcolor = "#FFFFFF";

								if (in_array($row[csf('id')], $poIdArr)) {
									if ($po_row_id == "") $po_row_id = $i;
									else $po_row_id .= "," . $i;
								}
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
									<td width="40" align="center"><?php echo "$i"; ?>
										<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[csf('id')]; ?>" />
										<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row[csf('po_number')]; ?>" />
									</td>
									<td width="150">
										<p><? echo $row[csf('po_number')]; ?></p>
									</td>
									<td  width="100" align="center">
										<p><? echo change_date_format($row[csf('pub_shipment_date')]); ?></p>
									</td>
									<td><input <?=$disable;?> type="text" id="txtseqno_<?=$i; ?>" name="txtseqno_<?=$i; ?>" class="text_boxes_numeric" onBlur="js_set_value(<? echo $i; ?>);" value="<?=$po_id_sec_arr[$row[csf('id')]]; ?>" style="width:30px" /></td>
								</tr>
							<?
								$i++;
							}
							?>
							<input type="hidden" name="txt_po_row_id" id="txt_po_row_id" value="<?php echo $po_row_id; ?>" />
						</table>
					</div>
					<table width="370" cellspacing="0" cellpadding="0" style="border:none" align="center">
						<tr>
							<td align="center" height="30" valign="bottom">
								<div style="width:100%">
									<div style="width:50%; float:left" align="left">
										<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
									</div>
									<div style="width:50%; float:left" align="left">
										<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
									</div>
								</div>
							</td>
						</tr>
					</table>
				</form>
			</fieldset>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		fnc_seq();
		set_all();
	</script>

	</html>
	<?
	exit();
}

if ($action == "color_popup") 
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1, '', '1', '');
	extract($_REQUEST);
	//echo "<pre>";print_r($_REQUEST);
	
	?>
	<script>
		
		// var hiddisorderseq='<?=$yes_no[0]["ORDER_PRIORITY"]; ?>';
		// var isSeqUse='<?=$isSeqUse; ?>';

		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search', -1);
		});

		var selected_id = new Array();
		var selected_name = new Array();
		//var selected_seq = new Array();

		function check_all_data() {
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;

			tbl_row_count = tbl_row_count - 1;
			for (var i = 1; i <= tbl_row_count; i++) {
				js_set_value(i);
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function set_all() 
		{
			var old = document.getElementById('txt_po_row_id').value;
			
			
				if (old != "") {
					var oldd = old.split(",");
					var n = 0;
					for (var k = 0; k < oldd.length; k++) 
					{
						// var seqdata=oldd[k].split("!");
						// if(typeof(seqdata[1])!= 'undefined')
						// {
							js_set_value(oldd[k])
						// 	//alert(oldd[n]+'--'+k)
						// 	n++;
						// }
					}
				}
			
		}

		function js_set_value(str) 
		{
			
				toggle(document.getElementById('search' + str), '#FFFFCC');

				if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1) {
					selected_id.push($('#txt_individual_id' + str).val());
					selected_name.push($('#txt_individual' + str).val());

				} else {
					for (var i = 0; i < selected_id.length; i++) {
						if (selected_id[i] == $('#txt_individual_id' + str).val()) break;
					}
					selected_id.splice(i, 1);
					selected_name.splice(i, 1);
				}

				var id = '';
				var name = '';
				for (var i = 0; i < selected_id.length; i++) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ',';
				}

				id = id.substr(0, id.length - 1);
				name = name.substr(0, name.length - 1);
				// alert(id,name)

				$('#hidden_search_id').val(id);
				$('#hidden_search_name').val(name);
			}
				
			
		// function fnc_seq()
		// {
		// 	if(document.getElementById('chk_is_seq').checked==false) document.getElementById('chk_is_seq').value=0;
		// 	else if(document.getElementById('chk_is_seq').checked==true) document.getElementById('chk_is_seq').value=1;
		// }
	</script>

	</head>
	<?
		
		$yes_no_arr=array();
		foreach($yes_no as $val)
		{
			$order_seq=$val[csf('order_priority')];
		}
		// echo $country_seq;die();
		if($order_seq!=1){$disable="disabled";}
		if($order_seq==1) {$checked = "checked";}
	?>
	<body>
		<div align="center">
			<fieldset style="width:400px;margin-left:10px">
			<input type="hidden" name="hidden_search_id" id="hidden_search_id" class="text_boxes" value="">
				<input type="hidden" name="hidden_search_name" id="hidden_search_name" class="text_boxes" value="">
				<!-- <input type="hidden" name="po_sec_no" id="po_sec_no" class="text_boxes" value=""> -->
				<form name="searchprocessfrm_1" id="searchprocessfrm_1" autocomplete="off">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="390" class="rpt_table">
						<thead>
							<th width="40">SL</th>
							<th width="150">Color Name.</th>
							
						</thead>
					</table>
					<div style="width:390px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="370" class="rpt_table" id="tbl_list_search">
							<?
							
							$i = 1;
							$po_row_id = '';
							$colorIdArr = explode(",", $cbocolor);
							// print_r($colorIdArr);die;
							$sql_color ="SELECT a.id,a.color_name from lib_color a,wo_po_color_size_breakdown b,wo_po_break_down c  where  a.id=b.color_number_id  and c.id =b.po_break_down_id and b.job_no_mst='$txt_job'  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  group by a.id,a.color_name";
							// echo $sql_color;die;


							// echo $sql;die;
							$result = sql_select($sql_color);
							foreach ($result as $row) {
								if ($i % 2 == 0) $bgcolor = "#E9F3FF";
								else $bgcolor = "#FFFFFF";

								if (in_array($row[csf('id')], $colorIdArr)) {
									if ($po_row_id == "") $po_row_id = $i;
									else $po_row_id .= "," . $i;
								}
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
									<td width="40" align="center"><?php echo "$i"; ?>
										<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[csf('id')]; ?>" />
										<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row[csf('color_name')]; ?>" />
									</td>
									<td width="150">
										<p><? echo $row[csf('color_name')]; ?></p>
									</td>
									
								</tr>
							<?
								$i++;
							}
							?>
							<input type="hidden" name="txt_po_row_id" id="txt_po_row_id" value="<?php echo $po_row_id; ?>" />
						</table>
					</div>
					<table width="370" cellspacing="0" cellpadding="0" style="border:none" align="center">
						<tr>
							<td align="center" height="30" valign="bottom">
								<div style="width:100%">
									<div style="width:50%; float:left" align="left">
										<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
									</div>
									<div style="width:50%; float:left" align="left">
										<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
									</div>
								</div>
							</td>
						</tr>
					</table>
				</form>
			</fieldset>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		//fnc_seq();
		set_all();
	</script>

	</html>
	<?
	exit();
}



//master data save update delete here------------------------------//
if ($action == "save_update_delete") 
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	if ($operation == 0) // Insert Here----------------------------------------------------------
	{
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}

		$prev_cut_no_arr = array();
		$dataArrayMst = sql_select("select a.cutting_no, b.color_ids, b.order_cut_no from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.company_id=" . $cbo_company_name . " and a.entry_form=715 and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.job_no=$txt_job_no");
		foreach ($dataArrayMst as $row) {
			$prev_cut_no_arr[$row[csf('color_ids')]][$row[csf('order_cut_no')]] = $row[csf('cutting_no')];
		}
		//print_r($prev_cut_no_arr);die;
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		$sql_table = return_field_value("id", "lib_cutting_table", "company_id =$cbo_company_name AND location_id =$cbo_location_name AND floor_id =$cbo_floor_name AND table_no = $txt_table_no");
		if ($sql_table != "") {
			$tbl_id = $sql_table;
		} else {
			$tbl_id = return_next_id("id", "lib_cutting_table", 1);
			$field_array_table = "id, table_no, company_id, working_company_id, location_id, floor_id, inserted_by, insert_date, status_active, is_deleted";
			$data_array_table = "(" . $tbl_id . "," . $txt_table_no . "," . $cbo_company_name . "," . $cbo_working_company_name . "," . $cbo_location_name . "," . $cbo_floor_name . ",'" . $user_id . "','" . $pc_date_time . "',1,0)";
		}

		$job_prifix = return_field_value("job_no_prefix_num", "wo_po_details_master", "job_no=$txt_job_no");
		$new_sys_number = explode("*", return_next_id_by_sequence("", "ppl_cut_lay_mst", $con, 1, $cbo_company_name, '', 0, date("Y", time()), 0, 0, 0, 0, 0));

		$cut_no_prifix[] = $new_sys_number[2]; 

		$comp_prefix = return_field_value("company_short_name", "lib_company", "id=$cbo_company_name");
		$cut_no = str_pad((int) $cut_no_prifix[0], 6, "0", STR_PAD_LEFT);
		$year_id = date('Y', time());
		if (strlen($year_id) == 4) $year_id = substr($year_id, 2, 2);
		$new_cutting_number = str_replace("--", "-", $new_sys_number[1]) . $cut_no;
		$new_cutting_prifix = str_replace("--", "-", $new_sys_number[1]);
		$id = return_next_id_by_sequence("ppl_cut_lay_mst_seq",  "ppl_cut_lay_mst", $con);

		$field_array = "id, entry_form, cut_num_prefix, cut_num_prefix_no, cutting_no, table_no, job_no, batch_id, company_id, working_company_id, location_id, floor_id, entry_date, start_time, end_date, end_time, marker_length, marker_width, fabric_width, gsm, width_dia, cad_marker_cons,cad_marker_excess,cad_marker_total, inserted_by, insert_date, status_active, is_deleted";
		$start_time = str_replace("'", "", $txt_in_time_hours) . ":" . str_replace("'", "", $txt_in_time_minuties);
		$end_time = str_replace("'", "", $txt_out_time_hours) . ":" . str_replace("'", "", $txt_out_time_minuties);
		$data_array = "(" . $id . ",715,'" . $new_cutting_prifix . "'," . $cut_no_prifix[0] . ",'" . $new_cutting_number . "'," . $tbl_id . "," . $txt_job_no . "," . $txt_batch_no . "," . $cbo_company_name . "," . $cbo_working_company_name . "," . $cbo_location_name . "," . $cbo_floor_name . "," . $txt_entry_date . ",'" . $start_time . "'," . $txt_end_date . ",'" . $end_time . "'," . $txt_marker_length . "," . $txt_marker_width . "," . $txt_fabric_width . "," . $txt_gsm . "," . $cbo_width_dia . "," . $txt_marker_cons . "," . $txt_marker_excess . "," . $txt_marker_total . ",'" . $user_id . "','" . $pc_date_time . "',1,0)";

		$detls_id = return_next_id_by_sequence("ppl_cut_lay_dtls_seq",  "ppl_cut_lay_dtls", $con);
		$field_array1 = "id, mst_id, order_ids,country_ids, iscountry_seq,color_type_id, order_cut_no, color_ids, batch_id, gmt_item_id, plies, order_qty, roll_data,isorder_seq,order_seq_data, inserted_by, insert_date, status_active, is_deleted";

		$field_array_roll = "id, barcode_no, mst_id, dtls_id, po_ids, entry_form, qnty, roll_id, roll_no, plies, batch_no, shade, inserted_by, insert_date";
		$add_comma = 0;

		$duplicateMsg = '';
		$duplicateStatus = true;
		$field_array_country_seq="id, mst_id, dtls_id, country_id, sequence_no, inserted_by, insert_date, status_active, is_deleted";
		$field_array_order_seq="id, mst_id, dtls_id, order_id, sequence_no, inserted_by, insert_date, status_active, is_deleted";
		$dataarr_country_seq="";
		for ($i = 1; $i <= $row_num; $i++) 
		{
			$cbo_order_id = "poId_" . $i;
			$cbo_country_id = "countryId_" . $i;
			$txt_ship_date = "txtshipdate_" . $i;
			$cbocolor = "colorId_" . $i;
			$cbo_gmt_id = "cbogmtsitem_" . $i;
			$order_qty = "txtorderqty_" . $i;
			$txt_plics = "txtplics_" . $i;
			$update_details_id = "updateDetails_" . $i;
			$order_cut_no = "orderCutNo_" . $i;
			$rollData = "rollData_" . $i;
			$cbobatch = "cbobatch_" . $i;
			$cbobatch = "cbobatch_" . $i;
			$cboColorType = "cboColorType_" . $i;
			$hiddiscountryseq="hiddiscountryseq_".$i;
			$hiddisorderseq="hiddisorderseq_".$i;
			$posecno="posecno_".$i;

			$prev_cut_no = $prev_cut_no_arr[str_replace("'", '', $$cbocolor)][str_replace("'", '', $$order_cut_no)];
			if (str_replace("'", '', $$order_cut_no) != ""  && $prev_cut_no != "") {
				$duplicateStatus = false;
				$duplicateMsg .= "Cutting No: " . $prev_cut_no . " Found Against Order Cut No-" . str_replace("'", '', $$order_cut_no);
			}
			//echo"<pre>"; print_r($$rollData);die;
			$save_string = explode("**", str_replace("'", '', $$rollData));
			// echo"<pre>"; print_r($save_string);die;
			$response_data = '';
			for ($x = 0; $x < count($save_string); $x++) 
			{
				$roll_dtls = explode("=", $save_string[$x]);
				$barcode_no = $roll_dtls[0];
				$roll_no = $roll_dtls[1];
				$roll_id = $roll_dtls[2];
				$roll_qnty = $roll_dtls[3];
				$plies = $roll_dtls[4];
				$batchNo = $roll_dtls[5];
				$shade = $roll_dtls[6];
				$color = $roll_dtls[7];
				// echo $roll_no;

				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
				if (str_replace("'", '', $$roll_maintained) != 1) $roll_id = $id_roll;

				if ($data_array_roll != "") $data_array_roll .= ",";
				$data_array_roll .= "(" . $id_roll . "," . $barcode_no . "," . $id . "," . $detls_id . "," . $$cbo_order_id . ",715,'" . $roll_qnty . "','" . $roll_id . "','" . $roll_no . "','" . $plies . "','" . $batchNo . "','" . $shade ."'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				$response_data .= $barcode_no . "=" . $roll_no . "=" . $roll_id . "=" . $roll_qnty . "=" . $plies . "=" . $batchNo . "=" . $shade ."=" . $color . "**";
			}
			

			$response_data = substr($response_data, 0, -2);

			if ($add_comma != 0) {
				$data_array1 .= ",";
				$detls_id_array .= "_";
			}

			 
			$data_array1 .= "(" . $detls_id . "," . $id . "," . $$cbo_order_id . "," . $$cbo_country_id ."," . $$hiddiscountryseq . "," . $$cboColorType . "," . $$order_cut_no . "," . $$cbocolor . "," . $$cbobatch . "," . $$cbo_gmt_id . "," . $$txt_plics . "," . $$order_qty . ",'" . $response_data . "'," . $$hiddisorderseq . "," . $$posecno . ",'" . $user_id . "','" . $pc_date_time . "',1,0)";
			$detls_id_array .= $detls_id . "#" . str_replace("'", '', $$order_cut_no);
			$dId=$detls_id;
			$detls_id = return_next_id_by_sequence("ppl_cut_lay_dtls_seq",  "ppl_cut_lay_dtls", $con);
			$add_comma++;
			

			if(str_replace("'",'',$$hiddiscountryseq)==1)
			{
				$cosuntrydata=array_filter(explode(",",str_replace("'",'',$$cbo_country_id)));
				foreach($cosuntrydata as $cdata)
				{
					$excountryseq=array_filter(explode("!",$cdata));
					$cseqdtls_id= return_next_id_by_sequence("ppl_cut_lay_country_seq_dtls_SEQ",  "ppl_cut_lay_country_seq_dtls", $con );
					if($dataarr_country_seq!="") $dataarr_country_seq.= ",";
					$dataarr_country_seq.="(".$cseqdtls_id.",".$id.",".$dId.",'".$excountryseq[0]."','".$excountryseq[1]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				}
			}
			

			if(str_replace("'",'',$$hiddisorderseq)==1)
			{
				$orderdataarr=array_filter(explode(",",str_replace("'",'',$$posecno)));
				foreach($orderdataarr as $cdata)
				{
					$excountryseq=array_filter(explode("!",$cdata));
					$cseqdtls_id= return_next_id_by_sequence("ppl_cut_lay_order_seq_dtls_SEQ",  "ppl_cut_lay_order_seq_dtls", $con );
					if($dataarr_order_seq!="") $dataarr_order_seq.= ",";
					$dataarr_order_seq.="(".$cseqdtls_id.",".$id.",".$dId.",'".$excountryseq[0]."','".$excountryseq[1]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				}
			}	
		}
		// echo"<pre>"; print_r($data_array_roll);die;
		// echo "10**$data_array1";die;
		if ($duplicateStatus == false) {
			echo "13**" . $duplicateMsg;
			disconnect($con);
			die;
		}

		$rID = true;
		$rID3 = true;
		$rID4 = true;
		$rID5=true;

		if ($sql_table == "") {
			$rID = sql_insert("lib_cutting_table", $field_array_table, $data_array_table, 0);
		}

		$rID2 = sql_insert("ppl_cut_lay_mst", $field_array, $data_array, 0);

		if ($data_array1 != "") {
			$rID3 = sql_insert("ppl_cut_lay_dtls", $field_array1, $data_array1, 0);
		}

		if ($data_array_roll != "") {
			$rID4 = sql_insert("pro_roll_details", $field_array_roll, $data_array_roll, 0);
		}
		// echo "10**insert into pro_roll_details( $field_array_roll) values".$data_array_roll;die;
		if($dataarr_country_seq!="")
		{
			// ppl_cut_lay_mst
			// $rIDydel=execute_query( "update ppl_cut_lay_country_seq_dtls set updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."', status_active=0, is_deleted=1 where mst_id=".$master_id." and dtls_id=".implode(",",$updateID_array)." and status_active=1 and is_deleted=0",1);
			$rID5=sql_insert("ppl_cut_lay_country_seq_dtls",$field_array_country_seq,$dataarr_country_seq,0);
		}
		if($dataarr_order_seq!="")
		{
			$rID5=sql_insert("ppl_cut_lay_order_seq_dtls",$field_array_order_seq,$dataarr_order_seq,0);
		}
		// echo "10**insert into ppl_cut_lay_dtls( $field_array1) values".$data_array1;die;
		// echo "10**".$rID ."**". $rID2 ."**". $rID3 ."**". $rID4 ."**".$rID5;die;
		// echo "10**".$rID4;die;
		if ($db_type == 0) {
			if ($rID && $rID2 && $rID3 && $rID4 && $rID5) {
				mysql_query("COMMIT");
				echo "0**" . $id . "**" . $new_cutting_number . "**" . str_replace("'", "", $tbl_id) . "**" . $detls_id_array;
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . $rID;
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rID2 && $rID3 && $rID4  && $rID5) {
				oci_commit($con);
				echo "0**" . $id . "**" . $new_cutting_number . "**" . str_replace("'", "", $tbl_id) . "**" . $detls_id_array;
			} else {
				oci_rollback($con);
				echo "10**" . $rID;
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
	} 
	else if ($operation == 1) // Update Here----------------------------------------------------------
	{
		$prev_cut_no_arr = array();
		$dataArrayMst = sql_select("select a.cutting_no, b.color_ids, b.order_cut_no from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.company_id=" . $cbo_company_name . " and a.entry_form=715 and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.job_no=$txt_job_no and a.id!=$update_id");
		foreach ($dataArrayMst as $row) {
			//$prev_cut_no_arr[$row[csf('color_id')]][1].=$row[csf('order_cut_no')].",";
			//$prev_cut_no_arr[$row[csf('color_id')]][2]=$row[csf('cutting_no')];
			$prev_cut_no_arr[$row[csf('color_ids')]][$row[csf('order_cut_no')]] = $row[csf('cutting_no')];
		}
	//print_r($prev_cut_no_arr);die;
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		$cutting_qc_no = return_field_value("cutting_qc_no", " pro_gmts_cutting_qc_mst", "status_active=1 and is_deleted=0 and cutting_no=" . $txt_cutting_no . "");
		if ($cutting_qc_no != "") {
			echo "200**" . $cutting_qc_no;
			disconnect($con);
			die;
		}

		// ================== check color, item and order =======================
		$sql = "SELECT a.id, a.color_ids, a.gmt_item_id,a.order_ids,a.country_ids from ppl_cut_lay_dtls a, ppl_cut_lay_bundle b where a.id=b.dtls_id and b.status_active=1 and b.is_deleted=0 and a.mst_id=$update_id";
		// echo $sql;die;
		$res = sql_select($sql);

		$is_bundle_created = 0;
		if (count($res) > 0) {
			$prev_color_item_po_array = array();
			$bundle_created_dtls_id_array = array();
			foreach ($res as $val) {
				$prev_color_item_po_array[$val['ID']]['color_id'] = $val['COLOR_IDS'];
				$prev_color_item_po_array[$val['ID']]['gmt_item_id'] = $val['GMT_ITEM_ID'];
				$prev_color_item_po_array[$val['ID']]['order_ids'] = $val['ORDER_IDS'];
				$prev_color_item_po_array[$val['ID']]['country_ids'] = $val['COUNTRY_IDS'];
				$bundle_created_dtls_id_array[$val['ID']] = $val['ID'];
			}
			$is_bundle_created = 1;
		}


		$sql_table = return_field_value("id", "lib_cutting_table", "company_id =$cbo_company_name AND location_id =$cbo_location_name AND floor_id =$cbo_floor_name AND table_no = $txt_table_no");
		$rID = true;
		$rID2 = true;
		$rID3 = true;
		$rID4 = true;
		$rID5 = true;
		$rID6 = true;
		$status_change = true;
		if ($sql_table != "") {
			$tbl_id = $sql_table;
		} else {
			$tbl_id = return_next_id("id", "lib_cutting_table", 1);
			$field_array_table = "id,table_no,company_id,working_company_id,location_id,floor_id,inserted_by,insert_date,status_active,is_deleted";
			$data_array_table = "(" . $tbl_id . "," . $txt_table_no . "," . $cbo_company_name . "," . $cbo_working_company_name . "," . $cbo_location_name . "," . $cbo_floor_name . ",'" . $user_id . "','" . $pc_date_time . "',1,0)";
			//echo "insert into  ppl_cut_lay_table_no($field_array_table) values".$data_array_table;
			//$rID=sql_insert("lib_cutting_table",$field_array_table,$data_array_table,0);	
		}
		//master table update*********************************************************************
		$field_array = "table_no*job_no*batch_id*company_id*working_company_id*location_id*floor_id*entry_date*start_time*end_date*end_time*marker_length*marker_width*fabric_width*gsm*width_dia*cad_marker_cons*cad_marker_excess*cad_marker_total*updated_by*update_date";
		$start_time = str_replace("'", "", $txt_in_time_hours) . ":" . str_replace("'", "", $txt_in_time_minuties);
		$end_time = str_replace("'", "", $txt_out_time_hours) . ":" . str_replace("'", "", $txt_out_time_minuties);
		$data_array = "" . $tbl_id . "*" . $txt_job_no . "*" . $txt_batch_no . "*" . $cbo_company_name . "*" . $cbo_working_company_name . "*" . $cbo_location_name . "*" . $cbo_floor_name . "*" . $txt_entry_date . "*'" . $start_time . "'*" . $txt_end_date . "*'" . $end_time . "'*" . $txt_marker_length . "*" . $txt_marker_width . "*" . $txt_fabric_width . "*" . $txt_gsm . "*" . $cbo_width_dia . "*" . $txt_marker_cons . "*" . $txt_marker_excess . "*" . $txt_marker_total . "*'" . $user_id . "'*'" . $pc_date_time . "'";


		//$detls_id=return_next_id("id", " ppl_cut_lay_dtls", 1);
		$detls_id = return_next_id_by_sequence("ppl_cut_lay_dtls_seq",  "ppl_cut_lay_dtls", $con);
		$field_array1 = "id, mst_id,order_ids,country_ids,iscountry_seq,color_type_id,order_cut_no,ship_date,color_ids,batch_id,gmt_item_id,plies,order_qty,roll_data,isorder_seq,order_seq_data,inserted_by,insert_date,status_active,is_deleted";
		$field_array_up = "order_ids*country_ids*iscountry_seq*color_type_id*order_cut_no*ship_date*color_ids*batch_id*gmt_item_id*plies*order_qty*roll_data*isorder_seq*order_seq_data*updated_by*update_date";
		$field_array_roll = "id, barcode_no, mst_id, dtls_id, po_ids, entry_form, qnty, roll_id, roll_no, plies, batch_no, shade, inserted_by, insert_date";
		//$id_roll = return_next_id( "id", "pro_roll_details", 1 );
		$add_comma = 0;

		$duplicateMsg = '';
		$duplicateStatus = true;
		//$order_cut_no_arr=return_library_array( "select order_id,max(order_cut_no) as order_cut_no from ppl_cut_lay_dtls group by order_id", "order_id", "order_cut_no"  );
		//$field_array_roll="id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, roll_id, roll_no, plies, inserted_by, insert_date";
		
		$field_array_country_seq="id, mst_id, dtls_id, country_id, sequence_no, inserted_by, insert_date, status_active, is_deleted";
		$field_array_order_seq="id, mst_id, dtls_id, order_id, sequence_no, inserted_by, insert_date, status_active, is_deleted";
		$dataarr_country_seq="";
		$dataarr_order_seq="";

		for ($i = 1; $i <= $row_num; $i++) {
			$cbo_order_id = "poId_" . $i;
			$cbo_country_id = "countryId_" . $i;
			$orderCutNo = "orderCutNo_" . $i;
			$txt_ship_date = "txtshipdate_" . $i;
			$cbocolor = "colorId_" . $i;
			$cbo_gmt_id = "cbogmtsitem_" . $i;
			$order_qty = "txtorderqty_" . $i;
			$txt_plics = "txtplics_" . $i;
			$order_cut_no = "orderCutNo_" . $i;
			$update_details_id = "updateDetails_" . $i;
			$rollData = "rollData_" . $i;
			$cbobatch = "cbobatch_" . $i;
			$cboColorType = "cboColorType_" . $i;
			$hiddiscountryseq="hiddiscountryseq_".$i;
			$hiddisorderseq="hiddisorderseq_".$i;
			$posecno="posecno_".$i;

			$prev_cut_no = $prev_cut_no_arr[str_replace("'", '', $$cbocolor)][str_replace("'", '', $$order_cut_no)];
			if (str_replace("'", '', $$order_cut_no) != ""  && $prev_cut_no != "") {
				$duplicateStatus = false;
				$duplicateMsg .= "Cutting No: " . $prev_cut_no . " Found Against Order Cut No-" . str_replace("'", '', $$order_cut_no);
			}

			if (str_replace("'", "", $update_id) != "") $msster_id = $update_id;
			else $msster_id = $id;

			if (str_replace("'", '', $$update_details_id) != "") $dtlsId = str_replace("'", '', $$update_details_id);
			else $dtlsId = $detls_id;

			
			if(str_replace("'","",$update_id)!="") $msster_id=$update_id;
			else $msster_id=$id;  

			$save_string = explode("**", str_replace("'", '', $$rollData));
			$response_data = '';
			for ($x = 0; $x < count($save_string); $x++) {
				$roll_dtls = explode("=", $save_string[$x]);
				$barcode_no = $roll_dtls[0];
				$roll_no = $roll_dtls[1];
				$roll_id = $roll_dtls[2];
				$roll_qnty = $roll_dtls[3];
				$plies = $roll_dtls[4];
				$batchNo = $roll_dtls[5];
				$shade = $roll_dtls[6];
				$color = $roll_dtls[7];

				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
				if (str_replace("'", '', $$roll_maintained) != 1) $roll_id = $id_roll;

				if ($data_array_roll != "") $data_array_roll .= ",";
				$data_array_roll .= "(" . $id_roll . "," . $barcode_no . "," . $msster_id . "," . $dtlsId . "," . $$cbo_order_id . ",715,'" . $roll_qnty . "','" . $roll_id . "','" . $roll_no . "','" . $plies . "','" . $batchNo . "','" . $shade . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				$response_data .= $barcode_no . "=" . $roll_no . "=" . $roll_id . "=" . $roll_qnty . "=" . $plies . "=" . $batchNo . "=" . $shade ."=" . $color . "**";
				//$id_roll = $id_roll+1;
			}

			$response_data = substr($response_data, 0, -2);

			if (str_replace("'", '', $$update_details_id) != "") 
			{
				$updateID_array[] = str_replace("'", '', $$update_details_id);
				$data_array_up[str_replace("'", '', $$update_details_id)] = explode("_", ("" . $$cbo_order_id . "_" . $$cbo_country_id . "_".$$hiddiscountryseq."_" . $$cboColorType . "_" . $$order_cut_no . "_" . $$txt_ship_date . "_" . $$cbocolor . "_" . $$cbobatch . "_" . $$cbo_gmt_id . "_" . $$txt_plics . "_" . $$order_qty . "_'" . $response_data . "'_" . $$hiddisorderseq . "_" . $$posecno . "_'" . $user_id . "'_'" . $pc_date_time . "'_1_0"));
				//$dtlsId=str_replace("'",'',$$update_details_id); 

				if ($add_comma != 0) $detls_id_array .= "_";
				$detls_id_array .= str_replace("'", '', $$update_details_id) . "#" . str_replace("'", '', $$order_cut_no);
				$add_comma++;
				$dId=str_replace("'",'',$$update_details_id); 
			} 
			else 
			{
				if ($data_array1) {
					$data_array1 .= ",";
					$detls_id_array .= "_";
				}
				$data_array1 .= "(" . $detls_id . "," . $msster_id . "," . $$cbo_order_id . "," . $$cbo_country_id . ",".$$hiddiscountryseq.",".$$cboColorType . "," . $$order_cut_no . ",'" . $$txt_ship_date . "'," . $$cbocolor . "," . $$cbobatch . "," . $$cbo_gmt_id . "," . $$txt_plics . "," . $$order_qty . ",'" . $response_data . "'," . $$hiddisorderseq . "," . $$posecno . "," . $user_id . ",'" . $pc_date_time . "',1,0)";
				$detls_id_array .= $detls_id . "#" . str_replace("'", '', $$order_cut_no);
				//$dtlsId=$detls_id; 
				//$detls_id=$detls_id+1;
				$dId=$detls_id; 
				$detls_id = return_next_id_by_sequence("ppl_cut_lay_dtls_seq",  "ppl_cut_lay_dtls", $con);
				$add_comma++;
			}
		}

		if(str_replace("'",'',$$hiddiscountryseq)==1)
		{
			$cosuntrydata=array_filter(explode(",",str_replace("'",'',$$cbo_country_id)));
			foreach($cosuntrydata as $cdata)
			{
				$excountryseq=array_filter(explode("!",$cdata));
				$cseqdtls_id= return_next_id_by_sequence("ppl_cut_lay_country_seq_dtls_SEQ",  "ppl_cut_lay_country_seq_dtls", $con );
				if($dataarr_country_seq!="") $dataarr_country_seq.= ",";
				$dataarr_country_seq.="(".$cseqdtls_id.",".$update_id.",'".$dId."','".$excountryseq[0]."','".$excountryseq[1]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			}
		}		

		if(str_replace("'",'',$$hiddisorderseq)==1)
		{
			$orderdataarr=array_filter(explode(",",str_replace("'",'',$$posecno)));
			foreach($orderdataarr as $cdata)
			{
				$excountryseq=array_filter(explode("!",$cdata));
				$cseqdtls_id= return_next_id_by_sequence("ppl_cut_lay_order_seq_dtls_SEQ",  "ppl_cut_lay_order_seq_dtls", $con );
				if($dataarr_order_seq!="") $dataarr_order_seq.= ",";
				$dataarr_order_seq.="(".$cseqdtls_id.",".$update_id.",'".$dId."','".$excountryseq[0]."','".$excountryseq[1]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			}
		}
		//echo "10**insert into ppl_cut_lay_dtls( $field_array1) values".$data_array1;die;	
		if ($duplicateStatus == false) {
			echo "13**" . $duplicateMsg;
			disconnect($con);
			die;
		}
		//  echo "10**";
		//$detls_id_update.=implode("_",$updateID_array);
		//echo "10**insert into lib_cutting_table( $field_array_table) values".$data_array_table;die;
		if ($sql_table == "") {
			$rID = sql_insert("lib_cutting_table", $field_array_table, $data_array_table, 0);
		}

		$rID1 = sql_update("ppl_cut_lay_mst", $field_array, $data_array, "id", $update_id, 0);
		//echo $rID1 ;die;

		$detls_id_update .= $detls_id_array;
		if (count($updateID_array) > 0) 
		{
			$rID2 = execute_query(bulk_update_sql_statement("ppl_cut_lay_dtls", "id", $field_array_up, $data_array_up, $updateID_array), 1);
		}
		//echo "10**".bulk_update_sql_statement("ppl_cut_lay_dtls","id",$field_array_up,$data_array_up,$updateID_array); die;

		//  delete those dtls entry which bundle is not create yet
		$prev_dtls_id_array = return_library_array("select id, id from ppl_cut_lay_dtls where mst_id=$update_id", 'id', 'id');
		$deleted_id_arr = array_diff($prev_dtls_id_array, $updateID_array);
		// print_r($deleted_id_arr);die();
		$deleted_id = "";
		if (count($deleted_id_arr) > 0) {
			foreach ($prev_dtls_id_array as $key => $val) {
				if ($bundle_created_dtls_id_array[$val] == "") {
					if ($deleted_id_arr[$val]) {
						$deleted_id .= ($deleted_id == "") ? $val : "," . $val;
					}
				}
			}
			// echo $deleted_id."mmm<br>";die();
			if ($deleted_id != "") {
				$field_array_status = "updated_by*update_date*status_active*is_deleted";
				$data_array_status = $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*0*1";
				$status_change = sql_multirow_update("ppl_cut_lay_dtls", $field_array_status, $data_array_status, "id", $deleted_id, 0);
			}
		}
	

		if ($data_array1 != "") {
			$rID3 = sql_insert("ppl_cut_lay_dtls", $field_array1, $data_array1, 1);
		}

		$delete_roll = execute_query("delete from pro_roll_details where mst_id=$msster_id and entry_form=715", 0);
		if ($data_array_roll != "") {
			$rID4 = sql_insert("pro_roll_details", $field_array_roll, $data_array_roll, 0);
		}
		// echo "10**".$rID ."**". $rID1 ."**". $rID2 ."**". $rID3 ."**". $rID4."**".$delete_roll;die;
	
		if($dataarr_country_seq!="")
		{
			
			$rIDydel=execute_query( "delete from ppl_cut_lay_country_seq_dtls where mst_id=$msster_id");
			$rID5=sql_insert("ppl_cut_lay_country_seq_dtls",$field_array_country_seq,$dataarr_country_seq,0);
		}
	
		if($dataarr_order_seq!="")
		{
			
			$rIDydel=execute_query( "delete from ppl_cut_lay_order_seq_dtls where mst_id=$msster_id");
			$rID6=sql_insert("ppl_cut_lay_order_seq_dtls",$field_array_order_seq,$dataarr_order_seq,0);
		}
		// echo "10**".$rID ."**". $rID1 ."**". $rID2 ."**". $rID3 ."**". $rID4."**".$delete_roll;die;
		//echo "10**$delete_roll";die;
		if ($db_type == 0) {
			if ($rID && $rID1 && $rID2 && $rID3 && $rID4 &&	$rID5 && $delete_roll) {
				mysql_query("COMMIT");
				echo "1**" . str_replace("'", "", $update_id) . "**" . str_replace("'", "", $txt_cutting_no) . "**" . str_replace("'", "", $tbl_id) . "**" . $detls_id_update;
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . str_replace("'", "", $txt_cutting_no);
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rID1 && $rID2 && $rID3 && $rID4 && $rID5 && $delete_roll) {
				oci_commit($con);
				echo "1**" . str_replace("'", "", $update_id) . "**" . str_replace("'", "", $txt_cutting_no) . "**" . str_replace("'", "", $tbl_id) . "**" . $detls_id_update;
			} else {
				oci_rollback($con);
				echo "10**" . str_replace("'", "", $txt_cutting_no);
			}
		}
		disconnect($con);
		die;
	} else if ($operation == 2) // Delete Here----------------------------------------------------------
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		$cutting_qc_no = return_field_value("cutting_qc_no", " pro_gmts_cutting_qc_mst", "status_active=1 and is_deleted=0 and cutting_no=" . $txt_cutting_no . "");
		if ($cutting_qc_no != "") {
			echo "200**" . $cutting_qc_no;
			disconnect($con);
			die;
		}

		// echo "2**200**".$txt_cutting_no;
		$rID = $rID2 = $rID3 = $rID4 = $rID5 = $rID6 = 1;
		$rID = sql_delete("ppl_cut_lay_mst", "updated_by*update_date*status_active*is_deleted", "" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*0*1", 'id ', $update_id, 1);
		$rID2 = sql_delete("ppl_cut_lay_dtls", "updated_by*update_date*status_active*is_deleted", "" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*0*1", 'mst_id ', $update_id, 1);
		$rID3 = sql_delete("ppl_cut_lay_bundle", "updated_by*update_date*status_active*is_deleted", "" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*0*1", 'mst_id ', $update_id, 1);
		$rID4 = sql_delete("ppl_cut_lay_size", "updated_by*update_date*status_active*is_deleted", "" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*0*1", 'mst_id ', $update_id, 1);
		$rID5 = sql_delete("ppl_cut_lay_size_dtls", "updated_by*update_date*status_active*is_deleted", "" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*0*1", 'mst_id ', $update_id, 1);
		$rID6 = sql_delete("ppl_cut_lay_roll_dtls", "updated_by*update_date*status_active*is_deleted", "" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*0*1", 'mst_id ', $update_id, 1);

		// echo "10**$rID ** $rID2 ** $rID3 ** $rID4 ** $rID5 ** $rID6";die();
		if ($db_type == 0) {
			if ($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6) {
				mysql_query("COMMIT");
				echo "2**" . str_replace("'", "", $update_id);
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . str_replace("'", "", $txt_cutting_no);
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6) {
				oci_commit($con);
				echo "2**" . str_replace("'", "", $update_id);
			} else {
				oci_rollback($con);
				echo "10**" . str_replace("'", "", $txt_cutting_no);
			}
		}
		disconnect($con);
		die;
	}
}

if ($action == "cutting_number_popup") 
{
	echo load_html_head_contents("Cutting Info", "../../../", 1, 1, '', '1', '');
	extract($_REQUEST);
	?>
	<script>
		function js_set_cutting_value(strCon) {

			document.getElementById('update_mst_id').value = strCon;
			parent.emailwindow.hide();
		}

		function fn_item_search(str) {
			// console.log("Hlo");
			var field_type = "";
			$('#search_by_td').html('');
			$('#search_by_td_up').html('');
			if (str == 1) {
				field_type = '<input type="text" style="width:80px" class="text_boxes"  name="txt_job_search" id="txt_job_search" />';
				$('#search_by_td_up').html('Enter Job No');
			} else if (str == 2) {
				field_type = '<input type="text" style="width:80px" class="text_boxes"  name="txt_job_search" id="txt_job_search" />';
				$('#search_by_td_up').html('Enter Style');
			}
			$('#search_by_td').html(field_type);
		}
	</script>
	</head>

	<body>
		<div align="center" style="width:100%; overflow-y:hidden;">


			<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				<table width="1050" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
					<thead>
						<tr>
							<th width="140">Company name</th>
							<th width="140">Working Company</th>
							<th width="80">Cutting No</th>
							<th width="130">Search By</th>
							<th width="80" id="search_by_td_up">Job No</th>
							<th width="100">Year</th>
							<th width="130" style="display:none">Order No</th>
							<th width="150">Date Range</th>
							<th width="100" align="left"><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton" /></th>
						</tr>
					</thead>
					<tbody>
						<tr class="general">
							<td>
								<?
								echo create_drop_down("cbo_company_name", 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 order by company_name", "id,company_name", 0, "-- Select Company --", $company_id, "", 1);
								?>
							</td>
							<td>
								<?
								echo create_drop_down("cbo_wo_company_name", 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $working_credential_cond order by company_name", "id,company_name", 1, "-- Select Company --", 0, "", 0);
								?>
							</td>

							<td align="center">
								<input type="text" id="txt_cut_no" name="txt_cut_no" style="width:80px" class="text_boxes_numeric" />
								<input type="hidden" id="update_mst_id" name="update_mst_id" />
							</td>
							<td align="center">
								<?
								$search_by = array(1 => 'Job Wise', 2 => 'Style Wise');
								$dd = "";
								echo create_drop_down("cbo_search_by", 120, $search_by, "", 0, "--Select--", "", "fn_item_search(this.value);", 0);
								?>
							</td>
							<td align="center" id="search_by_td">
								<input name="txt_job_search" id="txt_job_search" class="text_boxes_numeric" style="width:80px" />
							</td>
							<td align="center">
								<? echo create_drop_down("txt_year", 100, $year, "", 1, "-- Select year --", $selected, ""); ?>
							</td>
							<td align="center" style="display:none">
								<input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:120px" />
							</td>
							<td align="center" width="250">
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
							</td>
							<td align="left">
								<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_cut_no').value+'_'+document.getElementById('txt_job_search').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_year').value+'_'+document.getElementById('cbo_wo_company_name').value, 'create_cutting_search_list_view', 'search_div', 'woven_cut_and_lay_ratio_wise_entry_v3_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td align="center" height="40" valign="middle" colspan="9">
								<? echo load_month_buttons(1);  ?>
							</td>
						</tr>
					</tbody>
					</tr>
				</table>
				<div align="center" valign="top" id="search_div"> </div>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
 <?
}

if ($action == "create_cutting_search_list_view") 
{
	$ex_data = explode("_", $data);
	//var_dump($data);
	$company = $ex_data[0];
	$cutting_no = $ex_data[1];
	$job_no = $ex_data[2];
	$from_date = $ex_data[3];
	$to_date = $ex_data[4];
	$cut_year = $ex_data[5];
	$order_no = $ex_data[6];
	$search_by = $ex_data[7];
	$job_year = $ex_data[8];
	$wo_company = $ex_data[9];
	if ($db_type == 2) {
		$year_cond = " and extract(year from a.insert_date)=$cut_year";
		$year = " extract(year from a.insert_date) as year ";
	}
	if ($db_type == 0) {
		$year_cond = " and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cut_year";
		$year = " SUBSTRING_INDEX(a.insert_date, '-', 1) as year ";
	}

	if (str_replace("'", "", $company) == 0) $conpany_cond = "";
	else $conpany_cond = "and a.company_id=" . str_replace("'", "", $company) . "";
	if (str_replace("'", "", $wo_company) == 0) $wo_conpany_cond = "";
	else $wo_conpany_cond = "and a.working_company_id=" . str_replace("'", "", $wo_company) . "";
	if (str_replace("'", "", $cutting_no) == "") $cut_cond = "";
	else $cut_cond = "and a.cut_num_prefix_no='" . str_replace("'", "", $cutting_no) . "'  $year_cond";
	//	if(str_replace("'","",$job_no)=="") $job_cond=""; else $job_cond="and b.job_no_prefix_num='".str_replace("'","",$job_no)."'";
	//if(str_replace("'","",$order_no)=="") $order_cond=""; else $order_cond="and c.po_number like '%".trim($order_no)."%' ";

	if ($job_year != 0) {
		if ($db_type == 2) {
			$job_year_cond = " and extract(year from b.insert_date)=$job_year";
		}
		if ($db_type == 0) {
			$job_year_cond = " and SUBSTRING_INDEX(b.insert_date, '-', 1)=$job_year";
		}
	}

	$job_cond = '';
	if (trim($job_no) != '') {
		if (trim($search_by) == 1) // for Job No
		{
			$job_cond = " and  b.job_no_prefix_num LIKE '%$job_no%'";
		} else if (trim($search_by) == 2) // for Style Ref.
		{
			$job_cond = " and b.style_ref_no LIKE '%$job_no%'";
		}
	}
	if ($from_date != "" && $to_date != "") {
		if ($db_type == 0) {
			$sql_cond = " and entry_date  between '" . change_date_format($from_date, 'yyyy-mm-dd') . "' and '" . change_date_format($to_date, 'yyyy-mm-dd') . "'";
		}
		if ($db_type == 2) {
			$sql_cond = " and entry_date  between '" . change_date_format($from_date, 'yyyy-mm-dd', '-', 1) . "' and '" . change_date_format($to_date, 'yyyy-mm-dd', '-', 1) . "'";
		}
	}

	//$sql_order="select a.id,a.cut_num_prefix_no, a.table_no, a.job_no, a.batch_id, a.entry_date, a.marker_length, a.marker_width, a.fabric_width, c.po_number, d.order_id,d.color_id,$year FROM ppl_cut_lay_mst a,wo_po_details_master b,wo_po_break_down c,ppl_cut_lay_dtls d where a.id=d.mst_id and a.job_no=b.job_no and b.job_no=c.job_no_mst and c.id=d.order_id and a.entry_form=715 $conpany_cond $cut_cond $job_cond $sql_cond $order_cond order by id";
	$buyer_library = return_library_array('SELECT id, buyer_name FROM lib_buyer', 'id', 'buyer_name');
 ?>
	<script type="text/javascript">
		var buyerName = <? echo json_encode($buyer_library); ?>;
	</script>
	<?
	$sql_order = "SELECT a.id,a.cut_num_prefix_no, a.table_no, a.job_no, a.batch_id, a.entry_date, a.marker_length, a.marker_width, a.fabric_width,c.color_ids, c.marker_qty, c.order_cut_no,$year,b.buyer_name,b.style_ref_no FROM ppl_cut_lay_mst a,wo_po_details_master b,ppl_cut_lay_dtls c where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  c.mst_id=a.id and a.job_no=b.job_no and a.entry_form=715 $conpany_cond $wo_conpany_cond $cut_cond $job_cond $sql_cond $job_year_cond order by id desc";

	//echo $sql_order;die;

	$table_no_arr = return_library_array("select id,table_no from lib_cutting_table", 'id', 'table_no');
	$color_arr = return_library_array("select id,color_name from lib_color", "id", "color_name");
	//echo $sql_order;
	$arr = array(3 => $table_no_arr, 6 => $buyer_library, 7 => $color_arr); //,4=>$order_number_arr,5=>$color_arr,Order NO,Color 
	echo create_list_view("list_view", "Cut No,Year,Order Cut No,Table No,Job No,Style Ref.,Buyer Name,Color,Marker Qty,Marker Length,Markar Width,Fabric Width,Entry Date", "60,50,70,60,90,100,100,100,80,90,90,100,120", "1150", "270", 0, $sql_order, "js_set_cutting_value", "id", "", 1, "0,0,0,table_no,0,0,buyer_name,color_ids,0,0,0,0,0,0", $arr, "cut_num_prefix_no,year,order_cut_no,table_no,job_no,style_ref_no,buyer_name,color_ids,marker_qty,marker_length,marker_width,fabric_width,entry_date", "", "setFilterGrid('list_view',-1)", "0,0,0,0,0,0,0,0,0,0,0,0,3");
	exit();
}

if ($action == "load_php_mst_form") 
{
	$sql_data = sql_select("SELECT b.id as   tbl_id,b.table_no,b.location_id,b.floor_id,a.id,a.job_no,a.company_id,a.working_company_id,a.entry_date,end_date,a.marker_length,a.marker_width,a.fabric_width,a.gsm,a.width_dia,a.cad_marker_cons,a.cad_marker_excess,a.cad_marker_total,a.cutting_no,a.batch_id,a.start_time,a.end_time
	from ppl_cut_lay_mst a, lib_cutting_table b
	where a.table_no=b.id and a.id=" . $data . " ");

	foreach ($sql_data as $val) {
		$start_time = explode(":", $val[csf("start_time")]);
		$end_time = explode(":", $val[csf("end_time")]);
		echo "document.getElementById('cbo_company_name').value = '" . ($val[csf("company_id")]) . "';\n";
		echo "document.getElementById('cbo_working_company_name').value = '" . ($val[csf("working_company_id")]) . "';\n";
		echo "load_drop_down( 'requires/woven_cut_and_lay_ratio_wise_entry_v3_controller','" . $val[csf("working_company_id")] . "', 'load_drop_down_location', 'location_td') ;";
		echo "document.getElementById('txt_table_no').value = '" . ($val[csf("table_no")]) . "';\n";
		echo "$('#txt_entry_date').val('" . change_date_format($val[csf("entry_date")]) . "');\n";
		echo "$('#txt_end_date').val('" . change_date_format($val[csf("end_date")]) . "');\n";
		echo "document.getElementById('txt_marker_length').value  = '" . ($val[csf("marker_length")]) . "';\n";
		echo "document.getElementById('txt_marker_width').value  = '" . ($val[csf("marker_width")]) . "';\n";
		echo "document.getElementById('txt_fabric_width').value = '" . ($val[csf("fabric_width")]) . "';\n";
		echo "document.getElementById('txt_gsm').value  = '" . ($val[csf("gsm")]) . "';\n";
		echo "document.getElementById('cbo_width_dia').value  = '" . ($val[csf("width_dia")]) . "';\n";
		echo "document.getElementById('txt_marker_cons').value  = '" . ($val[csf("cad_marker_cons")]) . "';\n";
		echo "document.getElementById('txt_marker_excess').value  = '" . ($val[csf("cad_marker_excess")]) . "';\n";
		echo "document.getElementById('txt_marker_total').value  = '" . ($val[csf("cad_marker_total")]) . "';\n";
		echo "document.getElementById('cbo_location_name').value  = '" . ($val[csf("location_id")]) . "';\n";
		echo "document.getElementById('txt_batch_no').value = '" . ($val[csf("batch_id")]) . "';\n";
		echo "document.getElementById('txt_job_no').value = '" . ($val[csf("job_no")]) . "';\n";
		echo "document.getElementById('update_tbl_id').value  = '" . ($val[csf("tbl_id")]) . "';\n";
		echo "document.getElementById('update_id').value = '" . ($val[csf("id")]) . "';\n";
		echo "document.getElementById('txt_cutting_no').value = '" . ($val[csf("cutting_no")]) . "';\n";
		echo "document.getElementById('cbo_floor_name').value  = '" . ($val[csf("floor_id")]) . "';\n";
		echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_cut_lay_info',1);\n";
		echo "document.getElementById('txt_in_time_hours').value  = '" . ($start_time[0]) . "';\n";
		echo "document.getElementById('txt_in_time_minuties').value = '" . ($start_time[1]) . "';\n";
		echo "document.getElementById('txt_out_time_hours').value = '" . ($end_time[0]) . "';\n";
		echo "document.getElementById('txt_out_time_minuties').value  = '" . ($end_time[1]) . "';\n";
		echo "document.getElementById('cbo_floor_name').value  = '" . ($val[csf("floor_id")]) . "';\n";
		if ($db_type == 0) {
			$insert_year = "SUBSTRING_INDEX(b.insert_date, '-', 1) as year";
		}
		if ($db_type == 2) {
			$insert_year = "extract(year from b.insert_date) as year";
		}
		$sql = sql_select("select distinct c.id,c.buyer_name,$insert_year from  wo_po_break_down a,wo_po_details_master b,lib_buyer c where  a.job_no_mst=b.job_no and b.job_no='" . $val[csf("job_no")] . "' and b.buyer_name=c.id and a.status_active=1");

		foreach ($sql as $row) {
			echo "document.getElementById('txt_buyer_name').value = '" . $row[csf("id")] . "';\n";
			echo "document.getElementById('txt_job_year').value = '" . $row[csf("year")] . "';\n";
		}
	}
	exit();
}

if ($action == "order_details_list") 
{
	// $sql_gmt_arr="select ";
	$tbl_row = 0;

	//echo "SELECT a.id, a.order_ids,a.country_ids, a.ship_date, a.color_id, a.color_type_id, a.batch_id, a.gmt_item_id, a.plies, a.marker_qty,a.order_qty, a.total_lay_qty, a.lay_balance_qty, b.job_no, b.job_year, b.company_id, a.order_cut_no, a.roll_data,a.iscountry_seq,a.isorder_seq,A.order_seq_data from ppl_cut_lay_dtls a, ppl_cut_lay_mst b where b.id=a.mst_id and mst_id=" . $data . " and a.status_active=1 and a.is_deleted=0 order by a.id";die;


	$sql_dtls = sql_select("SELECT a.id, a.order_ids,a.country_ids, a.ship_date, a.color_ids, a.color_type_id, a.batch_id, a.gmt_item_id, a.plies, a.marker_qty,a.order_qty, a.total_lay_qty, a.lay_balance_qty, b.job_no, b.job_year, b.company_id, a.order_cut_no, a.roll_data,a.iscountry_seq,a.isorder_seq,A.order_seq_data from ppl_cut_lay_dtls a, ppl_cut_lay_mst b where b.id=a.mst_id and mst_id=" . $data . " and a.status_active=1 and a.is_deleted=0 order by a.id");
	//echo $sql_dtls;die;

	$gmt_item_arr = return_library_array("SELECT gmts_item_id from wo_po_details_master where job_no='" . $sql_dtls[0][csf('job_no')] . "' and status_active=1", 'id', 'gmts_item_id');
	$gmt_item_id = implode(",", $gmt_item_arr);

	$color_item_arr = return_library_array("SELECT a.id,a.color_name from lib_color a,wo_po_color_size_breakdown b,wo_po_break_down c  where a.id=b.color_number_id and c.id=b.po_break_down_id and b.job_no_mst='" . $sql_dtls[0][csf('job_no')] . "' and c.status_active=1 and b.status_active=1 group by a.id,a.color_name", "id", "color_name");

	$po_arr = return_library_array("SELECT id, po_number from wo_po_break_down where job_no_mst='" . $sql_dtls[0][csf('job_no')] . "'", "id", "po_number");
	$country_arr = return_library_array("select id, country_name from lib_country where status_active=1", "id", "country_name");


	$color_type_arr = array();
	$sql = "SELECT b.color_type_id from  wo_po_break_down a,wo_pre_cost_fabric_cost_dtls b,wo_pre_cos_fab_co_avg_con_dtls c where a.job_no_mst=b.job_no  and b.id=c.pre_cost_fabric_cost_dtls_id and a.id=c.po_break_down_id and b.job_no=c.job_no  and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id in(" . $sql_dtls[0][csf('order_ids')] . ") and c.cons>0  group by b.color_type_id";
	foreach (sql_select($sql) as $vals) {
		$color_type_arr[$vals[csf("color_type_id")]] = $color_type[$vals[csf("color_type_id")]];
	}


	foreach ($sql_dtls as $val) {
		if ($val[csf("country_ids")] != "") 
		{
			$country_ex = explode(",",$val[csf("country_ids")]);
			$country_ids = ""; 
			foreach ($country_ex as $v) 
			{
				$v_ex = explode("!",$v);
				$country_ids .= ($country_ids=="") ? $v_ex[0] : ",".$v_ex[0];
			}

			$sql = "select sum(plan_cut_qnty) as plan_qty from wo_po_color_size_breakdown where po_break_down_id in(" . $val[csf("order_ids")] . ") and country_id in($country_ids) and item_number_id=" . $val[csf("gmt_item_id")] . " and color_number_id in(" . $val[csf("color_ids")] . ") and status_active=1";
		} 
		else 
		{
			$sql = "select sum(plan_cut_qnty) as plan_qty from wo_po_color_size_breakdown where po_break_down_id in(" . $val[csf("order_ids")] . ") and item_number_id=" . $val[csf("gmt_item_id")] . " and color_number_id in(" . $val[csf("color_ids")] . ") and status_active=1";
		}
		//echo $sql;die;
		$result = sql_select($sql);
		$plan_qty = 0;
		foreach ($result as $row) {
			$plan_qty += $row[csf("plan_qty")];
		}
		if ($val[csf("country_ids")] != "") {
			$sql_marker = "SELECT sum(b.size_qty) as mark_qty from ppl_cut_lay_dtls a,ppl_cut_lay_bundle b where a.id=b.dtls_id and b.order_id in(" . $val[csf("order_ids")] . ") and b.country_id in($country_ids) and a.gmt_item_id=" . $val[csf("gmt_item_id")] . " and a.color_ids in(" . $val[csf("color_ids")] . ") and a.status_active=1";
		} else {
			$sql_marker = "select sum(b.size_qty) as mark_qty from ppl_cut_lay_dtls a, ppl_cut_lay_bundle b where a.id=b.dtls_id and b.order_id in(" . $val[csf("order_ids")] . ") and a.gmt_item_id=" . $val[csf("gmt_item_id")] . " and a.color_ids in(" . $val[csf("color_ids")] . ") and a.status_active=1";
		}
		//echo $sql_marker;die;
		$result = sql_select($sql_marker);
		foreach ($result as $rows) {
			$total_marker_qty = $rows[csf("mark_qty")];
		}
		$lay_balance = $plan_qty - $total_marker_qty;
		// echo $plan_qty ."-". $total_marker_qty."<br>";die;

		$po_no = '';
		$po_ids = explode(",", $val[csf('order_ids')]);
		foreach ($po_ids as $poId) {
			$po_no .= $po_arr[$poId] . ",";
		}

		$country_name_arr=array();
		foreach (explode(",", $val[csf('country_ids')]) as  $value) {
			if($val[csf('iscountry_seq')]==0)
			{
				$country_name_arr[$value]=$country_arr[$value];
			}
			else
			{
				$excountryseq=explode("!",$value);
				$country_name_arr[$value]=$country_arr[$excountryseq[0]];
			}
			// print_r ($country_name_arr);die;
		}

		$order_name_arr=array();
		foreach (explode(",", $val[csf('order_ids')]) as  $value) 
		{
			$order_name_arr[$value]=$po_arr[$value];
		}

		$color_arr=array();
		foreach (explode(",", $val[csf('color_ids')]) as  $v) 
		{
			$color_arr[$v]=$color_item_arr[$v];
		}

		//echo "<pre>";print_r($color_arr);die;
		$tbl_row++;
		
	?>
		<tr class="" id="tr_<? echo $tbl_row; ?>" style="height:10px;">
			<td align="center" id="color_<? echo $tbl_row; ?>">
				<input type="text" name="<? echo $tbl_row; ?>" id="cbocolor_<? echo $tbl_row; ?>" class="text_boxes" style="width:100px;" placeholder="Double Click to Search" onDblClick="openmypage_color(<? echo $tbl_row; ?>);" value="<?php echo implode(",", $color_arr);?>" readonly />		 
                <input type="hidden" name="colorId_<? echo $tbl_row; ?>"  id="colorId_<? echo $tbl_row; ?>"  value="<?php echo $val[csf('color_ids')];?>" />
			</td>
			<td align="center" id="garment_<? echo $tbl_row; ?>">
				<?
				echo create_drop_down("cbogmtsitem_" . $tbl_row, 120, $garments_item, "", 1, "-- Select Item --", $val[csf('gmt_item_id')], "", "", $gmt_item_id);
				?>
			</td>
			<td align="center" id="orderId_<? echo $tbl_row; ?>">
				<input type="text" name="cboPoNo_<? echo $tbl_row; ?>" id="cboPoNo_<? echo $tbl_row; ?>" class="text_boxes" style="width:100px;" placeholder="Double Click to Search" onDblClick="openmypage_po(<? echo $tbl_row; ?>)" value="<?php echo implode(",", $order_name_arr);?>" readonly />
				<input type="hidden" name="poId_<? echo $tbl_row; ?>" id="poId_<? echo $tbl_row; ?>" value="<?php echo $val[csf('order_ids')];?>" />
                <input class="text_boxes" type="hidden" name="hiddisorderseq_<?=$tbl_row; ?>" id="hiddisorderseq_<?=$tbl_row; ?>" value="<?=$val[csf('isorder_seq')];?>" />
                <input class="text_boxes" type="hidden" name="posecno_<?=$tbl_row; ?>" id="posecno_<?=$tbl_row; ?>" value="<?=$val[csf('order_seq_data')];?>" />
			</td>
			<td align="center">
                <input style="width:70px;" class="text_boxes" type="text" name="countryName_1" id="countryName_1" placeholder="Browse"  onDblClick="openmypage_country(<? echo $tbl_row; ?>)" value="<?php echo implode(",", $country_name_arr);?> "/>
                <input class="text_boxes" type="hidden" name="countryId_<? echo $tbl_row; ?>" id="countryId_<? echo $tbl_row; ?>" value="<?php echo $val[csf('country_ids')];?>" />
                <input class="text_boxes" type="hidden" name="hiddiscountryseq_<?=$tbl_row; ?>" id="hiddiscountryseq_<?=$tbl_row; ?>" value="<?=$val[csf('iscountry_seq')];?>" />
            </td>

			<td align="center" id="colorTypeId_<? echo $tbl_row; ?>">
				<?
				echo create_drop_down("cboColorType_" . $tbl_row, 100, $color_type_arr, "", 1, "--Select--", $val[csf('color_type_id')], "", 1, 0);
				?>
			</td>



			<td align="center" id="cutNo_<? echo $tbl_row; ?>">
				<input style="width:60px;" class="text_boxes_numeric" type="text" name="orderCutNo_<? echo $tbl_row; ?>" id="orderCutNo_<? echo $tbl_row; ?>" placeholder="" value="<? echo $val[csf('order_cut_no')]; ?>" onBlur="cut_no_duplication_check(<? echo $tbl_row; ?>);" />
			</td>
			<td align="center" style="display:none;" ; id="batch_<? echo $tbl_row; ?>">
				<?
				$sql = "select a.id, a.batch_no, a.extention_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.color_id in('" . $val[csf('color_id')] . "')
				 and b.po_id in(" . $val[csf('order_ids')] . ") and a.entry_form in(0,7,37,66,68) and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.extention_no";
				//  echo $sql;die;
				$result = sql_select($sql);
				foreach ($result as $row) {
					$ext = '';
					if ($row[csf('extention_no')] > 0) {
						$ext = '-' . $row[csf('extention_no')];
					}
					$batch_array[$row[csf('id')]] = $row[csf('batch_no')] . $ext;
				}

				$sql = "select a.id, a.batch_no, a.extention_no from pro_batch_create_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.id=b.batch_id and b.id=c.dtls_id and c.color_id in('" . $val[csf('color_id')] . "') and c.po_breakdown_id in(" . $val[csf('order_ids')] . ") and b.status_active=1 and b.is_deleted=0 and c.entry_form in(14,15) and c.trans_type=5 group by a.id, a.batch_no, a.extention_no";

				// echo $sql;die;
				$result = sql_select($sql);
				foreach ($result as $row) {
					$ext = '';
					if ($row[csf('extention_no')] > 0) {
						$ext = '-' . $row[csf('extention_no')];
					}
					$batch_array[$row[csf('id')]] = $row[csf('batch_no')] . $ext;
				}

				if (count($batch_array) > 0) {
					echo create_drop_down("cbobatch_" . $tbl_row, 100, $batch_array, "", 1, "select Batch",  $val[csf('batch_id')], "");
				} else {
					echo create_drop_down("cbobatch_" . $tbl_row, 100, $blank_array, "", 1, "select Batch",  $val[csf('batch_id')], "");
				}
				?>
			</td>
			<td align="center">
				<input type="text" name="txtplics_<? echo $tbl_row; ?>" id="txtplics_<? echo $tbl_row; ?>" class="text_boxes_numeric" style="width:60px" value="<? echo $val[csf('plies')]; ?>" placeholder="Double Click" onDblClick="openmypage_roll(<? echo $tbl_row; ?>)" readonly />
				<input type="hidden" name="updateDetails_<? echo $tbl_row; ?>" id="updateDetails_<? echo $tbl_row; ?>" value="<? echo $val[csf('id')]; ?>" />
				<input type="hidden" name="rollData_<? echo $tbl_row; ?>" id="rollData_<? echo $tbl_row; ?>" class="text_boxes" value="<? echo $val[csf('roll_data')]; ?>" />
			</td>
			<td align="center">
				<input type="text" name="txtsizeratio_<? echo $tbl_row; ?>" id="txtsizeratio_<? echo $tbl_row; ?>" class="text_boxes_numeric" onDblClick="openmypage_sizeNo(this.id);" placeholder="Browse" style="width:50px" onKeyDown="if (event.keyCode == 13) document.getElementById(this.id).onclick(1)" readonly />
			</td>
			<td align="center" id="marker_<? echo $tbl_row; ?>">
				<input type="text" name="txtmarkerqty_<? echo $tbl_row; ?>" id="txtmarkerqty_<? echo $tbl_row; ?>" class="text_boxes_numeric" placeholder="Display" style="width:60px" value="<? echo $val[csf('marker_qty')]; ?>" disabled />
			</td>
			<td align="center" id="order_<? echo $tbl_row; ?>">
				<input type="text" name="txtorderqty_<? echo $tbl_row; ?>" id="txtorderqty_<? echo $tbl_row; ?>" class="text_boxes_numeric" placeholder="Display" style="width:60px" value="<? echo $plan_qty; ?>" disabled />
			</td>
			<td align="center">
				<input type="text" name="txttotallay_<? echo $tbl_row; ?>" id="txttotallay_<? echo $tbl_row; ?>" class="text_boxes_numeric" placeholder="Display" style="width:60px" value="<? echo $total_marker_qty; ?>" disabled />
			</td>
			<td align="center">
				<input type="text" name="txtlaybalanceqty_<? echo $tbl_row; ?>" id="txtlaybalanceqty_<? echo $tbl_row; ?>" class="text_boxes_numeric" placeholder="Display" style="width:60px" value="<? echo $lay_balance; ?>" disabled />
			</td>
			<td width="70">
				<input type="button" id="increase_<? echo $tbl_row; ?>" name="increase_<? echo $tbl_row; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $tbl_row; ?>)" onKeyDown="if (event.keyCode == 13) document.getElementById(this.id).onclick()" />
				<input type="button" id="decrease_<? echo $tbl_row; ?>" name="decrease_<? echo $tbl_row; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $tbl_row; ?>);" />
			</td>
		</tr>
	<?
	}
	exit();
}

if ($action == "cut_lay_entry_report_print") {
	// extract($_REQUEST);
	$data = explode('*', $data);
	//print_r($data);
	$sql = sql_select("SELECT id,job_no,cut_num_prefix_no,table_no,marker_length,marker_width,fabric_width,gsm,width_dia,cad_marker_cons,batch_id,company_id,cad_marker_total from ppl_cut_lay_mst where cutting_no='" . $data[0] . "' ");
	foreach ($sql as $val) {
		$mst_id = $val[csf('id')];
		$company_id = $val[csf('company_id')];
		$cut_prifix = $val[csf('cut_num_prefix_no')];
		$table_no = $val[csf('table_no')];
		$marker_length = $val[csf('marker_length')];
		$marker_with = $val[csf('marker_width')];
		$fabric_with = $val[csf('fabric_width')];
		$gsm = $val[csf('gsm')];
		$dia_width = $val[csf('width_dia')];
		$txt_batch = $val[csf('batch_id')];
		$cad_marker_cons = $val[csf('cad_marker_cons')];
		$cad_marker_total = $val[csf('cad_marker_total')];
		$job_no = $val[csf('job_no')];
	}

	$costing_per = return_field_value("costing_per", "wo_pre_cost_mst", "job_no='$job_no'");
	if ($costing_per == 1) {
		$costing_per_qty = 12;
	} else if ($costing_per == 2) {
		$costing_per_qty = 1;
	} else if ($costing_per == 3) {
		$costing_per_qty = 24;
	} else if ($costing_per == 4) {
		$costing_per_qty = 36;
	} else if ($costing_per == 5) {
		$costing_per_qty = 48;
	}

	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
	$comp_name = return_field_value("company_short_name", "lib_company", "id=$company_id");
	$sql_buyer_arr = sql_select("select buyer_name,style_ref_no from  wo_po_details_master where job_no='$data[1]'");
	$sql_order = sql_select("select order_ids,gmt_item_id,order_qty from ppl_cut_lay_dtls where mst_id='$mst_id'");
	$buyer_arr = return_library_array("select id, buyer_name from  lib_buyer", 'id', 'buyer_name');
	$color_arr = return_library_array("select id,color_name  from  lib_color", "id", "color_name");
	$order_number_arr = return_library_array("select id, po_number from wo_po_break_down where job_no_mst='$data[1]'", 'id', 'po_number');
	$table_no_library = return_library_array("select id,table_no  from  lib_cutting_table", "id", "table_no");
	//print_r($sql_order);
	$order_number = "";
	$order_id = '';
	foreach ($sql_order as $order_val) {
		$item_name = $order_val[csf('gmt_item_id')];
		$order_qty += $order_val[csf('order_qty')];
		if ($order_id != "") {
			$order_id .= "," . $order_val[csf('order_ids')];
		} else {
			$order_id = $order_val[csf('order_ids')];
		}
	}
	$order_ids = array_unique(explode(",", $order_id));
	foreach ($order_ids as $poId) {
		if ($order_number != "") {
			$order_number .= "," . $order_number_arr[$poId];
		} else {
			$order_number = $order_number_arr[$poId];
		}
	}

	?>
	<div style="width:1100px; position:relative">
		<div style=" width:500; height:200px; position:absolute; left:300px; top:0; ">
			<table width="500" cellspacing="0" align="center">
				<tr>
					<td align="center" style="font-size:22px; font-weight:bold;"><strong><? echo $company_library[$company_id]; ?></strong></td>
				</tr>
				<tr>
					<td align="center" style="font-size:18px; font-weight:bold;"><strong>LAY CHART & CONSUMPTION REPORT</strong></td>
				</tr>
			</table>

		</div>
		<div style=" width:200; height:40px; position:absolute; right:0; top:50px; ">Date: ......../......../.......... </div>
		<div style=" width:200; height:60px; position:absolute; right:0; top:90px; " id="barcode_img_id"> </div>
		<div style=" top:80px; width:270; height:200px; position:absolute; left:0; ">
			<table border="1" cellspacing="0" width="260" class="rpt_table" rules="all">
				<tr>
					<td width="80">Buyer</td>
					<td width="180" align="center"><? echo $buyer_arr[$sql_buyer_arr[0][csf('buyer_name')]]; ?></td>
				</tr>
				<tr>
					<td>Job No</td>
					<td align="center"> <? echo $data[1]; ?></td>
				</tr>
				<tr>
					<td>Style</td>
					<td align="center"> <? echo $sql_buyer_arr[0][csf('style_ref_no')]; ?></td>
				</tr>
				<tr>
					<td>Item Name</td>
					<td align="center"><? echo $garments_item[$item_name]; ?></td>
				</tr>
				<tr>
					<td>Order No</td>
					<td align="center">
						<p> <? echo $order_number; ?></p>
					</td>
				</tr>
				<tr>
					<td>Order Qty</td>
					<td align="right"><? echo $order_qty; ?></td>
				</tr>

			</table>
		</div>
		<div style="width:550; position:absolute; height:30px; top:70px; left:280px">
			<table>
				<tr>
					<td><b>Working Company: </b></td>
					<td width="260"><? echo $company_library[$data[2]]; ?> </td>
					<td><b>Location: </b></td>
					<td><? echo $location_arr[$data[3]]; ?> </td>
				</tr>
			</table>


		</div>
		<div style="width:250; position:absolute; height:30px; top:118px; left:280px">
			<table border="1" cellpadding="1" cellspacing="1" width="220" class="rpt_table" rules="all">
				<tr>
					<td width="170"> CAD Fabric Width/Dia</td>
					<td width="50" align="center" colspan="2"><? echo $fabric_with; ?></td>
				</tr>
			</table>
		</div>


		<div style="width:250; position:absolute; height:30px; top:160px; left:280px">
			<table border="1" cellpadding="1" cellspacing="1" width="220" class="rpt_table" rules="all">
				<tr>
					<td width="170">CAD GSM</td>
					<td width="50" align="center" colspan="2"><? echo $gsm; ?></td>
				</tr>
			</table>
		</div>
		<div style="width:300; position:absolute; height:100px; top:280px; left:280px">
			<table border="1" cellpadding="1" cellspacing="1" width="300" class="rpt_table" rules="all">
				<tr height="20">
					<td width="80">Table No</td>
					<td width="75" align="center"><? echo $table_no_library[$table_no]; ?></td>
					<td width="75" align="center">Batch No </td>
					<td width="80" align="center">Dia(Tube<br>/Open)</td>
				</tr>
				<tr height="30">
					<td width="80">Cutting No</td>
					<td width="75" align="center"><? echo $comp_name . "-" . $cut_prifix; ?></td>
					<td width="75" align="center"> <? echo $txt_batch; ?></td>
					<td width="80" align="center"> <? echo $fabric_typee[$dia_width]; ?></td>
				</tr>
			</table>
		</div>

		<div style="width:200; position:absolute; height:400px; top:164px; left:580px">
			<table border="1" cellpadding="1" cellspacing="1" width="200" class="rpt_table" rules="all">
				<tr height="30">
					<td width="90">Sperading Operators</td>
					<td width="100" align="center"></td>
				</tr>
				<tr height="30">
					<td width="">Checked by Marker Man</td>
					<td width="" align="center"></td>
				</tr>
				<tr height="30">
					<td width="90">Cutter Man-1</td>
					<td width="100" align="center"></td>
				</tr>
				<tr height="43">
					<td width="">Cutter Man-2</td>
					<td width="" align="center"></td>
				</tr>
				<tr height="30">
					<td width="">Cutter Man-3</td>
					<td width="" align="center"></td>
				</tr>
			</table>
		</div>


		<div style=" width:300; position:absolute; top:175px; right:0px; ">
			<table border="1" cellpadding="1" cellspacing="1" width="300" class="rpt_table" rules="all">
				<tr height="30">
					<td width="100"><strong>Line Q.I</strong></td>
					<td width="200" align="center" colspan="2"></td>
				</tr>
				<tr height="30">
					<td><strong>Jr. DQ.C</strong></td>
					<td align="center" colspan="2"></td>
				</tr>
				<tr height="30">
					<td><strong>Checked By Q.C</strong></td>
					<td align="center" colspan="2"></td>
				</tr>
				<tr height="30">
					<td>Start Time</td>
					<td align="center" width="100"></td>
					<td align="center" width="100"><strong>Total Time Taken</strong></td>
				</tr>
				<tr height="30">
					<td>End Time</td>
					<td align="center" width="100"></td>
					<td align="center" width="100"></td>
				</tr>

			</table>
		</div>
		<div style=" width:270; position:absolute; top:250px;  ">
			<div style=" float:left; text-align:center; margin-top:20px; width:80px;"><Strong>STEP LAY DETAILS</Strong></div>
			<div style=" float:right;width:190px;">
				<div style="  width:90px; background-color:#666666; color:white;"><Strong>Step-1</Strong></div>
				<table border="1" cellpadding="1" cellspacing="1" width="180" class="rpt_table" rules="all">
					<tr height="30">
						<td width="80">CAD Marker Length</td>
						<td width="80" align="center"><? echo $marker_length;  ?></td>
					</tr>
					<tr height="30">
						<td>CAD Marker Width</td>
						<td align="center"><? echo $marker_with;  ?></td>
					</tr>


				</table>
			</div>
		</div>
	</div>

	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {

			var value = valuess; //$("#barcodeValue").val();
			// alert(value)
			var btype = 'code39'; //$("input[name=btype]:checked").val();
			var renderer = 'bmp'; // $("input[name=renderer]:checked").val();

			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 30,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {
				code: value,
				rect: false
			};

			$("#barcode_img_id").show().barcode(value, btype, settings);

		}
		generateBarcode('<? echo $data[0]; ?>');
	</script>


	<div style=" width:1100px; position:absolute; top:385px; ">
		<style type="text/css">
			.block_div {
				width: auto;
				height: auto;
				text-wrap: normal;
				font-size: 10.5px;
				vertical-align: bottom;
				display: block;

				-webkit-transform: rotate(-90deg);
				-moz-transform: rotate(-90deg);
			}
		</style>
		<?

		$sql_size_ration = sql_select("select a.id,b.size_id,b.size_ratio from   
     ppl_cut_lay_size_dtls b,ppl_cut_lay_dtls a where a.id=b.dtls_id  and a.mst_id=$mst_id and b.status_active=1  and  b.is_deleted=0 and a.status_active=1
     and a.is_deleted=0 ");
		$detali_data_arr = array();
		$plice_data_arr = array();
		$size_id_arr = array();
		$total_gmt_qty = array();
		$grand_total = 0;
		$size_qty = 0;
		$size_ratio_arr = array();
		foreach ($sql_size_ration as $size_val) {
			$size_ratio_arr[$size_val[csf('id')]][$size_val[csf('size_id')]] = $size_val[csf('size_ratio')];
		}

		$sql_main_qry = sql_select("select c.id,a.id,a.color_id,c.size_id,c.roll_no,sum(c.roll_wgt) as roll_weight,c.plies, sum(c.size_qty) as size_qty,c.roll_id 
	 from  ppl_cut_lay_roll_dtls c,ppl_cut_lay_dtls a 
	 where  a.id=c.dtls_id and a.mst_id=$mst_id and c.status_active=1  and  c.is_deleted=0 and a.status_active=1 and a.is_deleted=0
	 group by c.id,a.id,a.color_id,c.size_id,c.roll_no,c.plies,c.roll_id
	 order by a.id,c.id");
		$detali_data_arr = array();
		$plice_data_arr = array();
		$size_id_arr = array();
		$total_gmt_qty = array();
		$grand_total = 0;
		$size_qty = 0;
		foreach ($sql_main_qry as $main_val) {
			$size_id_arr[$main_val[csf('size_id')]] = $main_val[csf('size_id')];
			$plice_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]]['plies'] = $main_val[csf('plies')];
			$detali_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]][$main_val[csf('size_id')]]['size'] = $main_val[csf('size_id')];
			$detali_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]][$main_val[csf('size_id')]]['marker_qty'] = $main_val[csf('size_qty')];
			$total_gmt_qty[$main_val[csf('id')]][$main_val[csf('roll_id')]]['gmt_qty'] += $main_val[csf('size_qty')];
			$grand_total += $main_val[csf('marker_qty')];
			$size_qty = return_field_value("size_qty", "ppl_cut_lay_bundle", "mst_id =$mst_id AND dtls_id =" . $main_val[csf('id')] . " ");
			$plice_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]]['bundle_qty'] = $size_qty;
			$plice_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]]['color'] = $main_val[csf('color_id')];
			$plice_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]]['roll_no'] = $main_val[csf('roll_no')];
			$plice_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]]['roll_weight'] = $main_val[csf('roll_weight')];
		}
		//print_r($plice_data_arr);die;
		$col_span = count($size_id_arr);
		$td_width = 450 / $col_span;

		// echo $td_width;die;

		?>
		<table border="1" cellpadding="1" cellspacing="1" width="1100" class="rpt_table" rules="all">
			<tr height="30">
				<td width="30">SL</td>
				<td width="60" align="center">Roll No </td>
				<td width="60" align="center">Roll Yrds </td>
				<td width="50" align="center">Color </td>
				<td width="70"> Plies & Pcs/Bundle</td>
				<td width="80">Particulars</td>
				<td width="470" align="center" colspan="<? echo $col_span; ?>">Size, Ratio and Garments Qty.</td>
				<td width="50" align="center">Total Gmts</td>
				<td width="70" align="center">Per Roll Cons</td>
				<td width="60">Cut Out Faults</td>
				<td width="60" align="center">End of Roll Length</td>
				<td width="60" align="center">Total Unused Length </td>
			</tr>
			<?
			$i = 1;
			$tot_gmts = 0;
			$tot_roll_wght = 0;
			foreach ($plice_data_arr as $dtls_id => $dtls_val) {
				foreach ($dtls_val as $plice_id => $plice_val) {
					$tot_roll_wght += $plice_val['roll_weight'];
			?>
					<tr height="20">
						<td width="" rowspan="4"><? echo $i;  ?></td>
						<td width="" align="center" rowspan="4"><? echo $plice_val['roll_no']; ?> </td>
						<td width="" align="center" rowspan="4"><? echo $plice_val['roll_weight']; ?></td>
						<td width="" align="center" rowspan="4" style="vertical-align:middle">
							<div class="block_div"><? echo $color_arr[$plice_val['color']];  ?></div>
						</td>
						<td width="" align="left" rowspan="2"><? echo $plice_val['plies'] . " Plies";  ?></td>
						<td width="">Size</td>

						<?
						foreach ($size_id_arr as $size_id => $size_val) {
						?>
							<td width="<? echo $td_width; ?>" align="center"><? echo $size_arr[$detali_data_arr[$dtls_id][$plice_id][$size_id]['size']];  ?> </td>

						<?
						}
						?>
						<td width="" align="right" valign="bottom"></td>
						<td width="" align="center"></td>
						<td width=""></td>
						<td width="" align="center"></td>
						<td width="" align="center"> </td>
					</tr>
					<tr height="20">
						<td width="">CAD Ratio</td>
						<?
						foreach ($size_id_arr as $size_id => $size_val) {
							$total_size_ratio += $size_ratio_arr[$dtls_id][$size_id]['size_ratio'];
						?>
							<td width="<? echo $td_width; ?>" align="center"><? echo $size_ratio_arr[$dtls_id][$size_id]['size_ratio'];  ?> </td>
						<?
						}
						?>
						<td width="" align="right" valign="bottom"><? echo $total_size_ratio;
																	$total_size_ratio = 0;  ?></td>
						<td width="" align="center"></td>
						<td width=""></td>
						<td width="" align="center"></td>
						<td width="" align="center"> </td>
					</tr>
					<tr height="20">
						<td width="" align="left" rowspan="2"><? echo $plice_val['bundle_qty'] . "/Bundle";  ?></td>
						<td width=""> Gmts Qty.
						</td>
						<?
						foreach ($size_id_arr as $size_id => $size_val) {
							$total_gmt_qty_roll += $detali_data_arr[$dtls_id][$plice_id][$size_id]['marker_qty'];
						?>
							<td width="<? echo $td_width; ?>" align="center"><? echo $detali_data_arr[$dtls_id][$plice_id][$size_id]['marker_qty'];  ?> </td>

						<?
						}
						$tot_gmts += $total_gmt_qty_roll;
						?>
						<td width="" align="right" valign="bottom"><? echo $total_gmt_qty_roll;
																	$total_gmt_qty_roll = 0;  ?></td>
						<td width="" align="center"></td>
						<td width=""></td>
						<td width="" align="center"></td>
						<td width="" align="center"> </td>
					</tr>
					</tr>
					<tr height="20">
						<td width="">Bundle Qty.</td>
						<?
						foreach ($size_id_arr as $size_id => $size_val) {
						?>
							<td width="<? echo $td_width; ?>" align="center" style="font-size:14px;">
								<?
								$bdl_qty = floor($detali_data_arr[$dtls_id][$plice_id][$size_id]['marker_qty'] / $plice_val['bundle_qty']);
								$extra_bdl = ($detali_data_arr[$dtls_id][$plice_id][$size_id]['marker_qty'] % $plice_val['bundle_qty']);
								if ($extra_bdl != 0) $bdl_qty = $bdl_qty . " Full & one $extra_bdl  pcs";

								echo $bdl_qty;
								?>
							</td>
						<?
						}
						?>
						<td width="" align="center"></td>
						<td width=""></td>
						<td width="" align="center"></td>
						<td width="" align="center"> </td>
					</tr>

			<?
					$i = $i + 1;
				}
			}

			?>


		</table>
		<?
		$table_height = 30 + ($i + 1) * 20;
		//echo $table_height;die;
		$div_position = $table_height + 420;

		$color_size_qty_arr = array();
		$color_size_sql = sql_select("SELECT po_break_down_id,item_number_id,size_number_id,color_number_id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown 
	where is_deleted=0 and status_active=1 and po_break_down_id in (" . $order_id . ") group by po_break_down_id,item_number_id,size_number_id,color_number_id");
		foreach ($color_size_sql as $s_id) {
			$color_size_qty_arr[$s_id[csf('po_break_down_id')]][$s_id[csf('item_number_id')]][$s_id[csf('color_number_id')]][$s_id[csf('size_number_id')]] += $s_id[csf('plan_cut_qnty')];
			//$tot_plan_qty+=$s_id[csf('plan_cut_qnty')];
		}

		$sql_sewing = sql_select("SELECT b.po_break_down_id,a.item_number_id,a.body_part_id,b.color_number_id,b.gmts_sizes,sum( b.cons ) AS conjumction
   FROM wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a
   WHERE a.id = b.pre_cost_fabric_cost_dtls_id  AND b.po_break_down_id in (" . $order_id . ") and b.cons!=0 and a.body_part_id in (1,20,125)
   GROUP BY b.po_break_down_id,a.item_number_id,a.body_part_id,b.color_number_id,b.gmts_sizes");
		$con_per_dzn = array();
		$po_item_qty_arr = array();
		$color_size_conjumtion = array();
		foreach ($sql_sewing as $row_sew) {
			$color_size_conjumtion[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]]['conjum'] = str_replace("'", "", $row_sew[csf("conjumction")]);

			$color_size_conjumtion[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]]['plan_cut_qty'] = $color_size_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]];
			$po_item_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('body_part_id')]]['plan_cut_qty'] += $color_size_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]];

			$tot_plan_qty += $color_size_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]];
		}
		//print_r($color_size_conjumtion);
		$con_qnty = 0;
		foreach ($color_size_conjumtion as $p_id => $p_value) {
			foreach ($p_value as $i_id => $i_value) {
				foreach ($i_value as $c_id => $c_value) {
					foreach ($c_value as $s_id => $s_value) {
						foreach ($s_value as $b_id => $b_value) {
							$order_color_size_qty = $b_value['plan_cut_qty'];
							// $order_qty=$po_item_qty_arr[$p_id][$i_id][$c_id][$b_id]['plan_cut_qty'];
							$order_qty = $tot_plan_qty;
							$order_color_size_qty_per = ($order_color_size_qty / $order_qty) * 100;
							$conjunction_per = ($b_value['conjum'] * $order_color_size_qty_per / 100);
							$con_per_dzn[$p_id][$c_id] += $conjunction_per;
							$con_qnty += $conjunction_per;
						}
					}
				}
			}
		}

		$con_qnty = ($con_qnty / $costing_per_qty) * 12;
		$net_cons = ($tot_roll_wght / $tot_gmts) * 12;
		$loss_gain = '&nbsp;';
		$gain = '&nbsp;';
		$loss = '&nbsp;';
		/*$cons_balance=$cad_marker_cons-$net_cons;
	if($cad_marker_cons>$net_cons) 
	{
		$loss_gain='Gain';
		$gain=number_format($cons_balance,4);
	}
	else if($cad_marker_cons<$net_cons) 
	{
		$loss_gain='Loss';
		$loss=number_format(abs($cons_balance),4);
	}*/

		$cons_balance = $con_qnty - $net_cons;
		if ($con_qnty > $net_cons) {
			$loss_gain = 'Gain';
			$gain = number_format($cons_balance, 4);
		} else if ($con_qnty < $net_cons) {
			$loss_gain = 'Loss';
			$loss = number_format(abs($cons_balance), 4);
		}
		?>


		<div style=" width:160px; position:absolute; margin-top:20px;   ">
			<table border="1" cellpadding="1" cellspacing="1" width="200" class="rpt_table" rules="all">
				<tr height="30">
					<td width="100">Booking<br>Consumption <br>Per Dzn</td>
					<td width="100" align="center"><? echo number_format($con_qnty, 4); ?></td>
				</tr>
			</table>
		</div>

		<div style=" width:160px; position:absolute; left:220px; margin-top:20px;   ">
			<table border="1" cellpadding="1" cellspacing="1" width="200" class="rpt_table" rules="all">
				<tr height="30">
					<td width="100">CAD Marker<br>Consumption <br>Per Dzn</td>
					<td width="100" align="center"><? echo number_format($cad_marker_total, 2); ?></td>
				</tr>
			</table>
		</div>
		<div style=" width:180px; position:absolute; left:440px; margin-top:20px;   ">
			<table border="1" cellpadding="1" cellspacing="1" width="180" class="rpt_table" rules="all">
				<tr height="30">
					<td width="40" rowspan="2">Net<br>KGS <br>Used</td>
					<td width="70" align="center">KGs</td>
					<td width="70" align="center">G.Qty</td>
				</tr>
				<tr height="30">

					<td width="70" align="center"><? echo $tot_roll_wght; ?></td>
					<td width="70" align="center"><? echo $tot_gmts; ?></td>
				</tr>
			</table>
		</div>

		<div style=" width:230px; position:absolute; right:191px; margin-top:20px;   ">
			<table border="1" cellpadding="1" cellspacing="1" width="220" class="rpt_table" rules="all">
				<!--<tr height="20">
                       <td width="80" rowspan="2">Net<br>Composition <br>Per Dzn</td>
                       <td width="70" align="center" >Net</td>
                       <td width="70" align="center" ></td>
                  </tr>
                   <tr height="20">
                       <td width="70" align="center" ><? echo number_format($net_cons, 4); ?></td>
                       <td width="70" align="center" ></td>
                  </tr>-->
				<tr height="20">
					<td width="80" rowspan="2">Net<br>Consumption <br>Per Dzn</td>
					<td width="70" align="center">Net</td>
					<td width="70" align="center">Loss</td>
					<td width="70" align="center">Gain</td>
				</tr>
				<tr height="20">
					<td width="70" align="center"><? echo number_format($net_cons, 4); ?></td>
					<td width="70" align="center"><? echo $loss; ?></td>
					<td width="70" align="center"><? echo $gain; ?></td>
				</tr>
			</table>
		</div>
		<div style="width:180px; position:absolute; right:0; margin-top:20px;   ">
			<table border="1" cellpadding="1" cellspacing="1" width="180" class="rpt_table" rules="all">
				<tr>
					<td width="100">Lay<br>Loss/Gain</td>
					<td width="80" align="center"><? echo $loss_gain; ?></td>
				</tr>
			</table>
		</div>
		<br><br><br>
		<? echo signature_table(221, $company_id, "1100px"); ?>
	</div>
 <?
	exit();
}

if ($action == "size_wise_repeat_cut_no") {
	$size_wise_repeat_cut_no = return_field_value("gmt_num_rep_sty", "variable_order_tracking", "company_name='$data' and variable_list=28 and is_deleted=0 and status_active=1");
	if ($size_wise_repeat_cut_no == 1) $size_wise_repeat_cut_no = $size_wise_repeat_cut_no;
	else $size_wise_repeat_cut_no = 0;

	echo "document.getElementById('size_wise_repeat_cut_no').value 			= '" . $size_wise_repeat_cut_no . "';\n";
	exit();
}


if ($action == "load_drop_down_color_type") 
{
	list($po_id, $row_no, $color, $gmt_id) = explode('_', $data);

	$sql_dtls = sql_select("select color_type_id from ppl_cut_lay_dtls where order_ids in(" . $po_id . ") and color_ids=$color and gmt_item_id=$gmt_id");
	

	$color_type_arr = array();
	$sql = "SELECT b.color_type_id from  wo_po_break_down a,wo_pre_cost_fabric_cost_dtls b,wo_pre_cos_fab_co_avg_con_dtls c where a.job_id=b.job_id  and b.id=c.pre_cost_fabric_cost_dtls_id and a.id=c.po_break_down_id and b.job_id=c.job_id  and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id in($po_id) and c.cons>0  group by b.color_type_id";
	foreach (sql_select($sql) as $vals) {
		$color_type_arr[$vals[csf("color_type_id")]] = $color_type[$vals[csf("color_type_id")]];
	}
	$status = ($sql_dtls[0][csf('color_type_id')]) ? 1 : 0;

	echo create_drop_down("cboColorType_" . $row_no, 100, $color_type_arr, "", 1, "--Select--", $sql_dtls[0][csf('color_type_id')], "", $status, 0);


	exit();
}


if ($action == "batch_popup") 
{
	echo load_html_head_contents("Barcode Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	// print_r($_REQUEST);die;

	//echo $added_barcode_no;
 ?>
	<script>
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search', -1);
		});

		var selected_id = new Array();

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function js_set_value(str) {
			toggle(document.getElementById('search' + str), '#FFFFCC');

			if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1) {
				selected_id.push($('#txt_individual_id' + str).val());
			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == $('#txt_individual_id' + str).val()) break;
				}
				selected_id.splice(i, 1);
			}
			var id = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
			}
			id = id.substr(0, id.length - 1);

			$('#hidden_barcode_nos').val(id);
		}

		function fnc_close() {
			parent.emailwindow.hide();
		}
	</script>

	</head>

	<body>
		<div align="center" style="width:550px;">
			<form name="searchwofrm" id="searchwofrm">
				<fieldset style="width:540px; margin-left:2px">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="520">
						<thead>
							<th width="50">SL</th>
							<th width="100">Batch No</th>
							<th width="130">Barcode No</th>
							<th width="100">Roll No</th>
							<th width="55">Roll Qty.</th>
							<th>GSM</th>
						</thead>
					</table>
					<div style="width:520px; max-height:200px; overflow-y:scroll" id="list_container" align="left">
						<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="500" id="tbl_list_search">
							<?
							$scanned_barcode_arr = array();
							$barcodeData = sql_select("select barcode_no from pro_roll_details where entry_form=715 and status_active=1 and is_deleted=0");

							foreach ($barcodeData as $row) {
								$scanned_barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
							}
							if ($added_barcode_no != '') 	$added_barcode_cond = " and c.barcode_no not in (" . $added_barcode_no . ")";
							else 						$added_barcode_cond = "";
							//echo "select a.batch_no,c.barcode_no, c.roll_id, c.roll_no, c.qnty,b.item_description from pro_batch_create_mst a, pro_batch_create_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in (64,37) and c.po_breakdown_id=$order_no  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $added_barcode_cond group by a.batch_no,c.barcode_no, c.roll_id, c.roll_no, c.qnty,b.item_description";

							$data_array = sql_select("select a.batch_no,c.barcode_no, c.roll_id, c.roll_no, c.qnty,b.item_description from pro_batch_create_mst a, pro_batch_create_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in (64,37) and c.po_breakdown_id in(" . str_replace("'", "", $order_no) . ") and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $added_barcode_cond group by a.batch_no,c.barcode_no, c.roll_id, c.roll_no, c.qnty,b.item_description"); // change by subbir and b.color_id=$color
							// echo $data_array ;die;
							$i = 1;
							foreach ($data_array as $row) {
								if ($scanned_barcode_arr[$row[csf('barcode_no')]] == "") {
									$item_description_arr = explode(",", $row[csf('item_description')]);
									if ($i % 2 == 0) $bgcolor = "#E9F3FF";
									else $bgcolor = "#FFFFFF";
							?>
									<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
										<td width="50">
											<? echo $i; ?>
											<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>" />
										</td>
										<td width="100"><? echo $row[csf('batch_no')]; ?></td>
										<td width="130">
											<p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p>
										</td>
										<td width="100"><? echo $row[csf('roll_no')]; ?></td>
										<td align="right" width="55"><? echo number_format($row[csf('qnty')], 2); ?></td>
										<td><? echo $item_description_arr[2]; ?></td>
									</tr>
							<?
									$i++;
								}
							}
							?>
						</table>
					</div>
					<table width="520">
						<tr>
							<td align="center">
								<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
								<input type="hidden" name="hidden_barcode_nos" id="hidden_barcode_nos">
							</td>
						</tr>
					</table>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
 <?
}

?>
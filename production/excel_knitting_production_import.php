<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../includes/common.php');

echo load_html_head_contents("Order Import","../", 1, 1, $unicode,1,'');

$txt_job_no=$_POST["txt_job_no"];

include( 'excel_reader.php' );//echo "hi";die;
$output = `uname -a`;
if( isset( $_POST["submit"] ) )
{	
	//error_reporting(E_ALL);
	//ini_set('display_errors', '1');
	
	extract($_REQUEST);
	
	foreach (glob("files/"."*.xls") as $filename){			
		@unlink($filename);
	}
	foreach (glob("files/"."*.xlsx") as $filename){			
		@unlink($filename);
	}
	//die;
	$source = $_FILES['uploadfile']['tmp_name'];
	$targetzip ='files/'.$_FILES['uploadfile']['name'];
	$file_name=$_FILES['uploadfile']['name'];
	unset($_SESSION['excel']);


	$company_arr = return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0","company_name","id");
	$supplier_arr = return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name","supplier_name","id");
	$location_arr = return_library_array("select id, location_name from lib_location where status_active =1 and is_deleted=0","location_name","id");
	$booking_id_arr = return_library_array("select id, booking_no from wo_booking_mst where status_active =1 and is_deleted=0","booking_no","id");

	$booking_sql = sql_select("select a.po_break_down_id, a.booking_no, a.booking_mst_id from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id=b.id and a.booking_mst_id !=0 and a.booking_type in (1,4) and a.status_active=1 group by a.po_break_down_id, a.booking_no, a.booking_mst_id order by a.booking_mst_id asc, a.po_break_down_id desc");
	foreach ($booking_sql as  $row) {
		$booking_info[$row[csf("booking_no")]]['po'] = $row[csf("po_break_down_id")];
		$booking_info[$row[csf("booking_no")]]['id'] = $row[csf("booking_mst_id")];
	}


	$buyer_id_arr = return_library_array("select id, buyer_name from lib_buyer where status_active =1 and is_deleted=0","buyer_name","id");
	$store_id_arr = return_library_array("select id, store_name from lib_store_location where status_active =1 and is_deleted=0","store_name","id");
	$color_id_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0","color_name","id");
	$operator_id_arr = return_library_array("select id, first_name from lib_employee where status_active =1 and is_deleted=0","first_name","id");
	$machine_id_arr = return_library_array("select id, machine_no from lib_machine_name where status_active =1 and is_deleted=0","machine_no","id");
	$prod_floor_id_arr = return_library_array("select id, floor_name from lib_prod_floor where status_active =1 and is_deleted=0","floor_name","id");
	$brand_id_arr = return_library_array("select id, brand_name from lib_brand where status_active =1 and is_deleted=0 and brand_name is not null","brand_name","id");
	$yarn_count_id_arr = return_library_array("select id, yarn_count from lib_yarn_count where status_active =1 and is_deleted=0","yarn_count","id");
	 
	

	
	
	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and b.status_active=1 group by a.id, a.construction, b.copmposition_id, b.percent, b.id order by b.id";
	$deter_array = sql_select($sql_deter);
	if (count($deter_array) > 0) {
		foreach ($deter_array as $row) {
			if (array_key_exists($row[csf('id')], $composition_arr)) {
				$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
			} else {
				$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
			}
		}
	}

	$color_range = array_flip($color_range);
	$body_part = array_flip($body_part);
	
	$composition_arr = array_flip($composition_arr); 
	$unit_of_measurement = array_flip($unit_of_measurement); 

	if (move_uploaded_file($source, $targetzip)) 
	{
		$excel = new Spreadsheet_Excel_Reader($targetzip);  
		//$excel->read($targetzip);
		$card_colum=0; $m=1; $style_data_array=array(); $po_data_array=array(); $country_data_array=array(); $style_all_data_arr=array();
		for ($i = 1; $i <= $excel->sheets[0]['numRows']; $i++) 
		{
			if($m==1)
			{
				for ($j = 1; $j <= $excel->sheets[0]['numCols']; $j++) 
				{
					//$k++;
					//echo "\"".$data->sheets[0]['cells'][$i][4]."\",";
					//$card_colum=$excel->sheets[0]['cells'][$i][$j];
					
					//echo $card_colum.'=='.$i.'=='.$j.'<br>';
					/*$date_fld2=$data->sheets[0]['cells'][$i][$date_fld];
					$in_out_time=$data->sheets[0]['cells'][$i][$time_fld_len[0]].",".$data->sheets[0]['cells'][$i][$time_fld_len[1]];*/
					//print_r($in_out_time_arr);
					//$date_time_colum=$data->sheets[0]['cells'][$i][4];
				}
				$m++;
			}
			else
			{ 
				$all_data='';
				$str_rep=array("/", "&", "*", "(", ")", "=","'",",","\r", "\n",'"','#');
				$str_rep_2=array( "&", "*", "(", ")", "=","'","\r", "\n",'"','#');
				//$style_ref=str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][10]);
				//$style_description=str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][11]);
				
				

				//master_part_data
				
				$company_id				=	$company_arr[$excel->sheets[0]['cells'][$i][1]];
				$production_basis_id	=	1;
				$txt_receive_date		=	date('d-M-y');//$excel->sheets[0]['cells'][$i][3];
				$company_location_id	=	$location_arr[$excel->sheets[0]['cells'][$i][4]];
				$booking_no				=	$excel->sheets[0]['cells'][$i][5];
				$booking_id				= 	$booking_info[$excel->sheets[0]['cells'][$i][5]]['id'];
				$po_id					= 	$booking_info[$excel->sheets[0]['cells'][$i][5]]['po'];
				$receive_challan_no		= 	$excel->sheets[0]['cells'][$i][6];
				$knitting_source_id		=	3;
				$supplier_id			=	$supplier_arr[$excel->sheets[0]['cells'][$i][8]];
				$knitting_location_id	=	"0";
				$yarn_issue_challan_no	= 	$excel->sheets[0]['cells'][$i][10];
				$yarn_issued			= 	$excel->sheets[0]['cells'][$i][11];
				$job_no					= 	$excel->sheets[0]['cells'][$i][12];
				$buyer_name				= 	$buyer_id_arr[$excel->sheets[0]['cells'][$i][13]];
				$service_booking_basejob_nod	= 	$excel->sheets[0]['cells'][$i][14];
				$store_name				= 	$store_id_arr[$excel->sheets[0]['cells'][$i][15]];
				$remarks				= 	$excel->sheets[0]['cells'][$i][16];
				$sub_contact			= 	$excel->sheets[0]['cells'][$i][17];

				$sl_no					= 	$excel->sheets[0]['cells'][$i][47];

				//$arrr = array(42=>42,43=>43,44=>44,45=>45,   597=>597,598=>598,599=>599,600=>600);
//69=>69, 163=>163,164=>164,657=>657,667=>667,683=>683,699=>699,741=>741,759=>759,760=>760,562=>562, 327=>327,328=>328,329=>329,330=>330,331=>331,332=>332,333=>333,
				//if($arrr[$sl_no]=="")
				//{
					if($company_id*1 ==0 || $txt_receive_date=="" || $company_location_id*1==0 || $booking_no =="" || $booking_id*1 ==0 || $supplier_id*1 ==0 || $store_name*1 ==0 || $po_id*1==0)
					{
						echo "Some master part mandatory field data are missing. SL no: $sl_no. <br> $company_id*1 ==0 || $txt_receive_date=='' || $company_location_id*1==0 || $booking_no =='' || $booking_id*1 ==0 || $supplier_id*1 ==0 || $store_name*1 ==0 || $po_id*1==0";
						die;
					}


					$master_part_str = $company_id."*".$company_location_id."*".$booking_no."*".$booking_id."*".$receive_challan_no."*".$supplier_id."*".$knitting_location_id."*".$yarn_issue_challan_no."*".$yarn_issued."*".$job_no."*".$buyer_name."*".$buyer_service_booking_basejob_nod."*".$store_name."*".$remarks."*".$txt_receive_date."*".$sub_contact;
					//details_part_data

					$body_part_id			= 	$body_part[$excel->sheets[0]['cells'][$i][18]];
					$uom					= 	12;//$unit_of_measurement[$excel->sheets[0]['cells'][$i][19]];

					$constructions			= 	$excel->sheets[0]['cells'][$i][20];
					$compositions			= 	$excel->sheets[0]['cells'][$i][21];
					$deter_id  				= 	$composition_arr[$constructions. ", " .$compositions];
					$txt_determination  	= 	$constructions. ", " .$compositions;

					$gsm 					= 	$excel->sheets[0]['cells'][$i][22];
					$old_gsm 				= 	$excel->sheets[0]['cells'][$i][23];
					$dia_width 				= 	$excel->sheets[0]['cells'][$i][24];
					$old_dia_width 			= 	$excel->sheets[0]['cells'][$i][25];
					$brand_name 			= 	$brand_id_arr[trim(str_replace($str_rep_2,' ',$excel->sheets[0]['cells'][$i][26]))];
					$shift_id 				= 	$shift_name[$excel->sheets[0]['cells'][$i][27]];
					$stitch_length 			= 	trim($excel->sheets[0]['cells'][$i][28]);
					$machine_guage 			= 	str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][29]);
					$machine_dia 			= 	str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][30]);
					$product_floor_no 		= 	$prod_floor_id_arr[$excel->sheets[0]['cells'][$i][31]];
					$no_of_roll 			= 	$excel->sheets[0]['cells'][$i][32];
					$machine_no 			= 	$machine_id_arr[$excel->sheets[0]['cells'][$i][33]];
					$grey_production_qnty	= 	$excel->sheets[0]['cells'][$i][34];
					$operator				= 	$operator_id_arr[$excel->sheets[0]['cells'][$i][35]];
					$rate					= 	$excel->sheets[0]['cells'][$i][36];
					$store_floor			= 	$excel->sheets[0]['cells'][$i][37];
					$fabric_color			= 	$color_id_arr[$excel->sheets[0]['cells'][$i][38]];
					$room					= 	$excel->sheets[0]['cells'][$i][39];
					$color_range_id			= 	$color_range[$excel->sheets[0]['cells'][$i][40]];
					$rack					= 	$excel->sheets[0]['cells'][$i][41];
					$reject_fabric_receive	= 	$excel->sheets[0]['cells'][$i][42];
					$shelf					= 	$excel->sheets[0]['cells'][$i][43];
					$yarn_lot				= 	str_replace($str_rep_2,' ',$excel->sheets[0]['cells'][$i][44]);
					$size					= 	str_replace($str_rep_2,' ',$excel->sheets[0]['cells'][$i][45]);
					$yarn_count				= 	str_replace($str_rep_2,' ',$excel->sheets[0]['cells'][$i][46]);

					

					$yCountArr = explode(",",$yarn_count);
					$yarn_count_ids="";
					foreach ($yCountArr as $ycount) {
						$yarn_count_ids .= $yarn_count_id_arr[$ycount].",";
					}

					$yarn_count_ids =chop($yarn_count_ids,",");
					



					if($body_part_id*1 ==0 || $deter_id*1==0 || $gsm*1 ==0 || $dia_width =="" || $stitch_length =="" || $grey_production_qnty*1 ==0  || $color_range_id*1 ==0)
					{
						echo "Some mandatory field data are missing.  : SL no: $sl_no <br> $body_part_id*1 ==0 || $deter_id*1==0 || $gsm*1 ==0 || $dia_width =='' || $stitch_length =='' || $grey_production_qnty*1 ==0 || $color_range_id*1     <br>$txt_determination";
						echo $grey_production_qnty;die;
						die;
					}




					$dtls_part_str = $body_part_id."*".$uom."*".$deter_id."*".$gsm."*".$old_gsm."*".$dia_width."*".$old_dia_width."*".$brand_name."*".$shift_id."*".$stitch_length."*".$machine_guage."*".$machine_dia."*".$product_floor_no."*".$no_of_roll."*".$machine_no."*".$grey_production_qnty."*".$operator."*".$rate."*".$store_floor."*".$fabric_color."*".$room."*".$color_range_id."*".$rack."*".$reject_fabric_receive."*".$shelf."*".$yarn_lot."*".$size."*".$yarn_count_ids."*".$po_id."*".$txt_determination."___";


					//$master_part_str = $companay_arr[$excel->sheets[0]['cells'][$i][1]]."**".$location_arr[$excel->sheets[0]['cells'][$i][4]]."**".$location_arr[$excel->sheets[0]['cells'][$i][5]];

					$knitting_data[$master_part_str] .=$dtls_part_str;
				//}
			}
		}
		//$_SESSION['excel']=$style_all_data_arr;
		/* echo "<pre>";
		print_r($knitting_data);
		die;  */

		if(empty($knitting_data))
		{
			echo "data not found";
			die;
		}

		$operation=0;
		$con = connect();
		if ($operation == 0)  // Insert Here
		{
			if ($db_type == 0) {
				mysql_query("BEGIN");
			}


			$booking_without_order = 0;
			$cbo_receive_basis=1;
			$cbo_knitting_source=3;
			$roll_maintained=0;
			$within_group=0;
			$service_booking_without_order=0;
			$fabric_store_auto_update=1;
			$is_salesOrder = 0;

			foreach ($knitting_data as $mstString => $dtlsStr) 
			{
				//$master_part_str = $company_id."*".$company_location_id."*".$booking_no."*".$booking_id."*".$receive_challan_no."*".$supplier_id."*".$knitting_location_id."*".$yarn_issue_challan_no."*".$yarn_issued."*".$job_no."*".$buyer_name."*".$buyer_service_booking_basejob_nod."*".$store_name."*".$remarks."*".$txt_receive_date."*".$sub_contact;

				$mst_data_arr = explode("*",$mstString);
				$cbo_company_id 			= $mst_data_arr[0];
				$cbo_com_location_name 		= $mst_data_arr[1];
				$txt_booking_no 			= $mst_data_arr[2];
				$txt_booking_no_id 			= $mst_data_arr[3];
				$txt_receive_chal_no 		= $mst_data_arr[4];
				$cbo_knitting_company 		= $mst_data_arr[5];
				$cbo_location_name 			= $mst_data_arr[6];
				$txt_yarn_issue_challan_no 	= $mst_data_arr[7];
				$txt_yarn_issued 			= $mst_data_arr[8];
				$txt_job_no 				= $mst_data_arr[9];
				$cbo_buyer_name 			= $mst_data_arr[10];
				$txt_service_booking 		= $mst_data_arr[11];
				$cbo_store_name 			= $mst_data_arr[12];
				$txt_remarks 				= $mst_data_arr[13];
				$txt_receive_date 			= $mst_data_arr[14];
				$cbo_sub_contract 			= $mst_data_arr[15];



				$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);
				$new_grey_recv_system_id = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master",$con,1,$cbo_company_id,'GPE',2,date("Y",time()),13 ));
				$field_array = "id, recv_number_prefix, recv_number_prefix_num, recv_number, entry_form, item_category, receive_basis, company_id, receive_date, challan_no, booking_id, booking_no, booking_without_order, store_id, location_id, knitting_source, knitting_company,knitting_location_id, buyer_id, yarn_issue_challan_no, sub_contract, remarks, roll_maintained, within_group,service_booking_without_order,service_booking_no, exchange_rate, currency_id, inserted_by, insert_date";
				$data_array[$id] = "(" . $id . ",'" . $new_grey_recv_system_id[1] . "'," . $new_grey_recv_system_id[2] . ",'" . $new_grey_recv_system_id[0] . "',2,13," . $cbo_receive_basis . "," . $cbo_company_id . ",'" . $txt_receive_date . "','" . $txt_receive_chal_no . "'," . $txt_booking_no_id . ",'" . $txt_booking_no . "'," . $booking_without_order . "," . $cbo_store_name . "," . $cbo_com_location_name . "," . $cbo_knitting_source . "," . $cbo_knitting_company . ",'".$cbo_location_name."'," . $cbo_buyer_name . ",'" . $txt_yarn_issue_challan_no . "','" . $cbo_sub_contract . "','" . $txt_remarks . "'," . $roll_maintained . "," . $within_group . "," . $service_booking_without_order . ",'" . $txt_service_booking . "','" .$exchange_rate_ref[0][csf('conversion_rate')]. "','" .$currency_ref_id . "',999,'" . $pc_date_time . "')";
				$grey_recv_num = $new_grey_recv_system_id[0];
				$grey_update_id = $id;

				$all_production_system[$grey_recv_num]=$grey_recv_num;

				//$dtls_part_str .= $body_part_id."*".$uom."*".$deter_id."*".$gsm."*".$old_gsm."*".$dia_width."*".$old_dia_width."*".$brand_name."*".$shift_id."*".$stitch_length."*".$machine_guage."*".$machine_dia."*".$product_floor_no."*".$no_of_roll."*".$machine_no."*".$grey_production_qnty."*".$operator."*".$rate."*".$store_floor."*".$fabric_color."*".$room."*".$color_range_id."*".$rack."*".$reject_fabric_receive."*".$shelf."*".$yarn_lot."*".$size."*".$yarn_count."*".$po_id."*".$txt_determination."___";
				
				$dtlsStringPartArray = explode("___",chop($dtlsStr,"___"));
				foreach ($dtlsStringPartArray as $dtlsStringArr) 
				{
					$dtlsString = explode("*",$dtlsStringArr);
					$cbo_body_part 			= $dtlsString[0];
					$cbo_uom 				= $dtlsString[1];
					$fabric_desc_id 		= $dtlsString[2];
					$txt_gsm 				= $dtlsString[3];
					$txt_original_gsm 		= $dtlsString[4];
					$txt_width 				= $dtlsString[5];
					$txt_original_dia_width	= $dtlsString[6];
					$brand_id 				= $dtlsString[7];
					$txt_shift_name 		= $dtlsString[8];
					$txt_stitch_length		= $dtlsString[9];
					$txt_machine_gg 		= $dtlsString[10];
					$txt_machine_dia 		= $dtlsString[11];
					$cbo_floor_id 			= $dtlsString[12];
					$txt_roll_no 			= $dtlsString[13];
					$cbo_machine_name 		= $dtlsString[14];
					$txt_receive_qnty 		= $dtlsString[15];
					$operator_name 			= $dtlsString[16];
					$rate 					= $dtlsString[17];
					$cbo_floor 				= $dtlsString[18];
					$color_id 				= $dtlsString[19];
					$cbo_room 				= $dtlsString[20];
					$cbo_color_range 		= $dtlsString[21];
					$txt_rack 				= $dtlsString[22];
					$txt_reject_fabric_recv_qnty= $dtlsString[23];
					$txt_shelf 				= $dtlsString[24];
					$txt_yarn_lot 			= $dtlsString[25];
					$txt_coller_cuff_size	= $dtlsString[26];
					$cbo_yarn_count			= $dtlsString[27];
					$all_po_id				= $dtlsString[28];
					$txt_fabric_description	= $dtlsString[29];


					$receive_qnty = str_replace("'", "", $txt_receive_qnty);

					if($txt_rack==""){$txt_rack=0;}
					if($txt_shelf==""){$txt_shelf=0;}
					if($cbo_room==""){$cbo_room=0;}
					if($cbo_floor==""){$cbo_floor=0;}

					$row_prod = sql_select("select id, current_stock, stock_value from product_details_master where company_id=$cbo_company_id and item_category_id=13 and detarmination_id=$fabric_desc_id and gsm=$txt_gsm and dia_width=$txt_width and status_active=1 and is_deleted=0");

					if (count($row_prod) > 0  || $new_prod_ref_arr[$cbo_company_id."**".$fabric_desc_id."**".$txt_gsm."**".$txt_width."**13"] != "")
					{
						//$field_array_prod_update = "store_id*avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
						//$data_array_prod_update[$prod_id] = $cbo_store_name . "*" . $avg_rate_per_unit . "*" . $receive_qnty . "*" . $curr_stock_qnty . "*" . $curr_stock_value . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

						if(count($row_prod) > 0)
						{
							$prod_id = $row_prod[0][csf('id')];
							//$curr_value = ($yarn_rate_taka + $knitting_charge_taka) * $receive_qnty;
							$curr_value = $rate  * $receive_qnty;

							$prod_id = $row_prod[0][csf('id')];
							$product_id_update_parameter[$prod_id]['qnty']+=$receive_qnty;
							$product_id_update_parameter[$prod_id]['amount']+=$curr_value;
							$update_to_prod_id[$prod_id]=$prod_id;
						}
						else
						{
							$curr_value = $rate  * $receive_qnty;
							$prod_id = $new_prod_ref_arr[$cbo_company_id."**".$fabric_desc_id."**".$txt_gsm."**".$txt_width."**13"];
							$product_id_insert_parameter[$prod_id."**".$fabric_desc_id."**".$txt_gsm."**".$txt_width."**".$txt_fabric_description."**".$cbo_company_id."**13"]+= $receive_qnty;
							$product_id_insert_amount[$prod_id."**".$fabric_desc_id."**".$txt_gsm."**".$txt_width."**".$txt_fabric_description."**".$cbo_company_id."**13"]+=$curr_value;
						}
						
					}
					else
					{
						$prod_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
						$curr_value = $rate * $receive_qnty;

						$product_id_insert_parameter[$prod_id."**".$fabric_desc_id."**".$txt_gsm."**".$txt_width."**".$txt_fabric_description."**".$cbo_company_id."**13"]+= $receive_qnty;
						$product_id_insert_amount[$prod_id."**".$fabric_desc_id."**".$txt_gsm."**".$txt_width."**".$txt_fabric_description."**".$cbo_company_id."**13"]+=$curr_value;
						$new_prod_ref_arr[$cbo_company_id."**".$fabric_desc_id."**".$txt_gsm."**".$txt_width."**13"] = $prod_id;


						//$field_array_prod = "id, company_id, store_id, item_category_id, detarmination_id, item_description, product_name_details, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, brand, gsm, dia_width, lot, inserted_by, insert_date";

						//$data_array_prod[$prod_id] = "(" . $prod_id . "," . $cbo_company_id . "," . $cbo_store_name . ",13," . $fabric_desc_id . ",'" . $txt_fabric_description . "','" . $prod_name_dtls . "'," . $cbo_uom . ",'" . $avg_rate_per_unit . "'," . $last_purchased_qnty . "," . $stock_qnty . "," . $stock_value . "," . $brand_id . "," . $txt_gsm . "," . $txt_width . ",'" . $txt_yarn_lot . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
					}



					$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
					$order_rate=0;
					if($exchange_rate_ref[0][csf("conversion_rate")] > 0)
					{
						$order_rate = ($yarn_rate_taka + $knitting_charge_taka) / $exchange_rate_ref[0][csf("conversion_rate")];
					}
					$order_rate = number_format($order_rate,4,".","");

					$order_amount = $order_rate * $receive_qnty;
					$order_amount = number_format($order_amount,4,".","");

					$cons_rate = $rate; //($yarn_rate_taka + $knitting_charge_taka);
					$cons_rate = number_format($cons_rate,4,".","");

					$cons_amount = $cons_rate*$receive_qnty;
					$cons_amount = number_format($cons_amount,4,".","");

					//echo "10**($yarn_rate_taka + $knitting_charge_taka) , ex=".$exchange_rate_ref[0][csf("conversion_rate")];die;

					$field_array_trans = "id, mst_id, receive_basis, pi_wo_batch_no, booking_without_order, company_id, prod_id, item_category, transaction_type, transaction_date, store_id, brand_id, order_uom, order_qnty, order_rate, order_amount, cons_uom, cons_quantity, cons_reject_qnty, cons_rate, cons_amount, balance_qnty, balance_amount, floor_id, machine_id, room, rack, self,production_floor, inserted_by, insert_date";

					$data_array_trans[$id_trans] = "(" . $id_trans . "," . $grey_update_id . "," . $cbo_receive_basis . "," . $txt_booking_no_id . "," . $booking_without_order . "," . $cbo_company_id . "," . $prod_id . ",13,1,'" . $txt_receive_date . "'," . $cbo_store_name . "," . $brand_id . "," . $cbo_uom . "," . $receive_qnty . "," . $order_rate . "," . $order_amount . "," . $cbo_uom . "," . $receive_qnty . ",'" . $txt_reject_fabric_recv_qnty . "'," . $cons_rate . "," . $cons_amount . "," . $receive_qnty . "," . $cons_amount . ",'" . $cbo_floor . "','" . $cbo_machine_name . "','" . $cbo_room . "','" . $txt_rack . "','" . $txt_shelf . "','" . $cbo_floor_id . "', 999 ,'" . $pc_date_time . "')";

					



					$id_dtls = return_next_id_by_sequence("PRO_GREY_PROD_DTLS_PK_SEQ", "pro_grey_prod_entry_dtls", $con);

					$cbo_yarn_count = explode(",", str_replace("'", "", $cbo_yarn_count));
					asort($cbo_yarn_count);
					$cbo_yarn_count = implode(",", $cbo_yarn_count);

					$txt_yarn_lot = explode(",", str_replace("'", "", $txt_yarn_lot));
					//asort($txt_yarn_lot);
					$txt_yarn_lot = implode(",", $txt_yarn_lot);

					$yarn_prod_id = explode(",", str_replace("'", "", $yarn_prod_id));
					asort($yarn_prod_id);
					$yarn_prod_id = implode(",", $yarn_prod_id);
					$operator_name = str_replace("'", "", $txt_operator_id);
					$hdn_rate = str_replace("'", "", $hdn_rate);
					$hdn_rate_string = str_replace("'", "", $hdn_rate_string);
					$rate = $cons_rate;
					$amount = $cons_amount;

					$field_array_dtls = "id, mst_id, trans_id, prod_id, body_part_id, febric_description_id, gsm, width, original_gsm, original_width, no_of_roll, order_id, grey_receive_qnty,grey_receive_qnty_pcs,coller_cuff_size, reject_fabric_receive, rate, amount, uom, yarn_lot, yarn_count, brand_id, shift_name, floor_id, machine_no_id, room, rack, self, store_floor, color_id, color_range_id, stitch_length,machine_dia,machine_gg,order_yarn_rate,order_knitting_charge,yarn_rate,kniting_charge, inserted_by, insert_date,operator_name,yarn_prod_id,production_rate,production_rate_string";

					$data_array_dtls[$id_dtls] = "(" . $id_dtls . "," . $grey_update_id . "," . $id_trans . "," . $prod_id . "," . $cbo_body_part . "," . $fabric_desc_id . "," . $txt_gsm . ",'" . $txt_width . "','" . $txt_original_gsm . "','" . $txt_original_dia_width . "','" . $txt_roll_no . "','" . $all_po_id . "','" . $txt_receive_qnty . "','" . $txt_receive_qnty_pcs . "','".$txt_coller_cuff_size."','" . $txt_reject_fabric_recv_qnty . "'," . $rate . ",'" . $amount . "','" . $cbo_uom . "','" . $txt_yarn_lot . "','" . $cbo_yarn_count . "','" . $brand_id . "','" . $txt_shift_name . "','" . $cbo_floor_id . "','" . $cbo_machine_name . "','" . $cbo_room . "','" . $txt_rack . "','" . $txt_shelf . "','" . $cbo_floor . "','" . $color_id . "','" . $cbo_color_range . "','" . $txt_stitch_length . "','" . $txt_machine_dia . "','" . $txt_machine_gg . "','" . $yarn_rate_usd . "','" . $knitting_charge_usd . "','" . $yarn_rate_taka . "','" . $knitting_charge_taka . "',999,'" . $pc_date_time . "','" . $operator_name . "','" . $yarn_prod_id . "','" . $hdn_rate . "','" . $hdn_rate_string . "')";



					$field_array_proportionate = "id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity,quantity_pcs, returnable_qnty, is_sales,coller_cuff_size, inserted_by, insert_date";
					$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);

					$data_array_prop[$id_prop] = "(" . $id_prop . "," . $id_trans . ",1,2," . $id_dtls . "," . $all_po_id . "," . $prod_id . ",'" . $txt_receive_qnty . "','" . $order_qnty_pcs . "','" . $txt_reject_fabric_recv_qnty . "','" . $is_salesOrder . "','" . $order_coller_cuff . "',999,'" . $pc_date_time . "')";

				
				}

			}

			if(!empty($product_id_insert_parameter))
			{
				foreach ($product_id_insert_parameter as $key => $val)
				{
					$prod_description_arr = explode("**", $key);
					$prod_id = $prod_description_arr[0];
					$fabric_desc_id = $prod_description_arr[1];
					$txt_gsm = $prod_description_arr[2];
					$txt_width = $prod_description_arr[3];
					$cons_compo = $prod_description_arr[4];
					$cbo_company_id = $prod_description_arr[5];

					$roll_amount = $product_id_insert_amount[$key];
					$avg_rate_per_unit = $roll_amount/$val;
					$prod_name_dtls = trim($cons_compo) . ", " . trim($txt_gsm) . ", " . trim($txt_width);

					// if Qty is zero then rate & value will be zero
					if ($val<=0) 
					{
						$roll_amount=0;
						$avg_rate_per_unit=0;
					}			

                   	$data_array_prod[$prod_id] = "(" . $prod_id . "," . $cbo_company_id . ",13," . $fabric_desc_id . ",'" . $cons_compo . "','" . $prod_name_dtls . "'," . "12" . "," . $avg_rate_per_unit . "," . $val . "," . $val . "," . $roll_amount . "," . $txt_gsm . ",'" . $txt_width . "',999,'" . $pc_date_time . "')";
				}
			}

			if(!empty($update_to_prod_id))
			{
				$prod_id_array=array();
				$up_to_prod_ids=implode(",",array_unique($update_to_prod_id));

				$toProdIssueResult=sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id in($up_to_prod_ids) ");
				foreach($toProdIssueResult as $row)
				{
					$stock_qnty = $product_id_update_parameter[$row[csf("id")]]['qnty'] + $row[csf("current_stock")];
					$stock_value = $product_id_update_parameter[$row[csf("id")]]['amount'] + $row[csf("stock_value")];
					

					if ($stock_qnty>0) 
					{
						$avg_rate_per_unit = $stock_value/$stock_qnty;
						$stock_value = $avg_rate_per_unit*$stock_qnty;
					}
					else
					{
						$avg_rate_per_unit = 0;
						$stock_value = 0;
					}
					// if Qty is zero then rate & value will be zero
					if ($stock_qnty<=0) 
					{
						$stock_value=0;
						$avg_rate_per_unit=0;
					}
					
					// echo "10**".$avg_rate_per_unit.'==='.$stock_value.'############';
					$prod_id_array[]=$row[csf('id')];
					$data_array_prod_update[$row[csf('id')]]=explode("*",("'".$stock_qnty."'*'".$avg_rate_per_unit."'*'".$stock_value."'*999*'".$pc_date_time."'"));
				}
				unset($toProdIssueResult);
			}

			


			/*
			|--------------------------------------------------------------------------
			| inv_receive_master
			| data inserting here
			|--------------------------------------------------------------------------
			|
			*/
			$field_array = "id, recv_number_prefix, recv_number_prefix_num, recv_number, entry_form, item_category, receive_basis, company_id, receive_date, challan_no, booking_id, booking_no, booking_without_order, store_id, location_id, knitting_source, knitting_company,knitting_location_id, buyer_id, yarn_issue_challan_no, sub_contract, remarks, roll_maintained, within_group,service_booking_without_order,service_booking_no, exchange_rate, currency_id, inserted_by, insert_date";
			$data_array_set=array_chunk($data_array,200);
			foreach( $data_array_set as $setRows)
			{
				//echo "10** insert into inv_receive_master ($field_array) values ".implode(",",$setRows);oci_rollback($con);die;
				$rID1=sql_insert("inv_receive_master",$field_array,implode(",",$setRows),0);
				if($rID1==1)
					$flag=1;
				else if($rID1==0)
				{
					$flag=0;
					oci_rollback($con);
					echo "Data Not Inserted => 1";
					disconnect($con);
					die;
				}
			}


			/*
			|--------------------------------------------------------------------------
			| inv_transaction
			| data inserting here
			|--------------------------------------------------------------------------
			|
			*/
			$data_array_trans_set=array_chunk($data_array_trans,200);
			foreach( $data_array_trans_set as $setRows)
			{
				//echo "10** insert into inv_transaction ($field_array_trans) values ".implode(",",$setRows);oci_rollback($con);disconnect($con);die;
				$rID2=sql_insert("inv_transaction",$field_array_trans,implode(",",$setRows),0);
				if($rID2==1)
					$flag=1;
				else if($rID2==0)
				{
					$flag=0;
					oci_rollback($con);
					echo "Data Not Inserted => 2<br>";
					echo "10** insert into inv_transaction ($field_array_trans) values ".implode(",",$setRows);oci_rollback($con);disconnect($con);die;
					disconnect($con);
					die;
				}
			}

		


			/*
			|--------------------------------------------------------------------------
			| pro_grey_prod_entry_dtls
			| data inserting here
			|--------------------------------------------------------------------------
			|
			*/
			$data_array_dtls_set=array_chunk($data_array_dtls,200);
			foreach( $data_array_dtls_set as $setRows)
			{
				//echo "10** insert into pro_grey_prod_entry_dtls ($field_array_dtls) values ".implode(",",$setRows);oci_rollback($con);die;
				$rID3=sql_insert("pro_grey_prod_entry_dtls",$field_array_dtls,implode(",",$setRows),0);
				if($rID3==1)
					$flag=1;
				else if($rID3==0)
				{
					$flag=0;
					oci_rollback($con);
					echo "Data Not Inserted => 3<br>";
					echo "10** insert into pro_grey_prod_entry_dtls ($field_array_dtls) values ".implode(",",$setRows);oci_rollback($con);die;
					disconnect($con);
					die;
				}
			}


			/*
			|--------------------------------------------------------------------------
			| order_wise_pro_details
			| data inserting here
			|--------------------------------------------------------------------------
			|
			*/
			$data_array_prop_set=array_chunk($data_array_prop,200);
			foreach( $data_array_prop_set as $setRows)
			{
				//echo "10** insert into order_wise_pro_details ($field_array_proportionate) values ".implode(",",$setRows);oci_rollback($con);die;
				$rID4=sql_insert("order_wise_pro_details",$field_array_proportionate,implode(",",$setRows),0);
				if($rID4==1)
					$flag=1;
				else if($rID4==0)
				{
					$flag=0;
					oci_rollback($con);
					echo "Data Not Inserted => 4";
					disconnect($con);
					die;
				}
			}


			/*
			|--------------------------------------------------------------------------
			| product_details_master
			| data inserting here
			|--------------------------------------------------------------------------
			|
			*/
			$field_array_prod = "id, company_id, item_category_id, detarmination_id, item_description, product_name_details, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, gsm, dia_width, inserted_by, insert_date";
			$data_array_prod_set=array_chunk($data_array_prod,200);
			foreach( $data_array_prod_set as $setRows)
			{
				//echo "10** insert into product_details_master ($field_array_prod) values ".implode(",",$setRows);oci_rollback($con);die;
				$rID5=sql_insert("product_details_master",$field_array_prod,implode(",",$setRows),0);
				if($rID5==1)
					$flag=1;
				else if($rID5==0)
				{
					$flag=0;
					oci_rollback($con);
					echo "Data Not Inserted => 5";
					disconnect($con);
					die;
				}
			}



			/*
			|--------------------------------------------------------------------------
			| product_details_master
			| data updating here
			|--------------------------------------------------------------------------
			|
			*/
			if(!empty($data_array_prod_update))
			{
				$field_array_prod_update = "current_stock*avg_rate_per_unit*stock_value*updated_by*update_date";
				$data_array_prod_update_chunk=array_chunk($data_array_prod_update,50,true);
				$prod_id_array_chunk=array_chunk($prod_id_array,50,true);
				$count_up_rolls=count($prod_id_array_chunk);
				for ($i=0;$i<$count_up_rolls;$i++)
				{
					$rID6=execute_query(bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update_chunk[$i], array_values($prod_id_array_chunk[$i] )),1);
					if($rID6 != "1" )
					{
						$flag=0;
						oci_rollback($con);
						echo "Data Not Inserted => 6";
						disconnect($con);
						die;
					}
				}
			}
			
			echo "10**".$flag."**".$rID6."**".$rID5."**".$rID4."**".$rID3."**".$rID2."**".$rID1;oci_rollback($con);disconnect($con);die;

			if ($db_type == 0) {
				if ($flag == 1) {
					mysql_query("COMMIT");
					echo "Success";
				} else {
					mysql_query("ROLLBACK");
					echo "Failed";
				}
			} else if ($db_type == 2 || $db_type == 1) {
				if ($flag == 1) {
					oci_commit($con);
					echo "Success. Production No : ". implode(", ",$all_production_system);
				} else {
					oci_rollback($con);
					echo "Failed";
				}
			}


			disconnect($con);
			die;
		}



	
	}
	else
	{
		echo "Failed";	
	}
	die;
}
?>

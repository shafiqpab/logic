<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../includes/common.php');

$permission=$_SESSION['page_permission'];
$user_id=$_SESSION['logic_erp']['user_id'];

function return_domestic_rate($rate,$ile_cost,$exchange_rate,$conversion_factor){
	$rate_ile=$rate+$ile_cost;
	$rate_ile_exchange=$rate_ile*$exchange_rate;
	$doemstic_rate=$rate_ile_exchange/$conversion_factor;
	return $doemstic_rate;	
}

//$specialCharactersArr = array(0=>'*', 1=>'\'', 2=>'£', 3=>'$', 4=>'&', 5=>'(', 6=>')', 7=>'#', 8=>'~', 9=>'|', 10=>'=', 11=>'_', 12=>'"', 13=>'`', 14=>'^', 15=>'\\');
		
function check_special_character($string)
{
	$special_charater='*\'£$&()#~|=_"`^\\';
	$specialCharactersArr = str_split($special_charater);
	$splitStringArr=str_split($string);
	$result=array_diff($specialCharactersArr,$splitStringArr);
	if (count($result)<count($specialCharactersArr)) return 1;
	else return 0;
}

$cdate=date("d-m-Y");
include('excel_reader.php');
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

	$source_excel = $_FILES['uploadfile']['tmp_name'];
	$targetzip ='files/'.$_FILES['uploadfile']['name'];
	$file_name=$_FILES['uploadfile']['name'];
    //echo $source_excel.'**'.$targetzip.'**'.$file_name;die;
	unset($_SESSION['excel']);
	if (move_uploaded_file($source_excel, $targetzip)) 
	{
		$excel = new Spreadsheet_Excel_Reader($targetzip); 
		$card_colum=0; $m=1; 
		$all_data_arr=array(); 

		for ($i = 1; $i <= $excel->sheets[0]['numRows']; $i++) 
		{
			if($m==1)
			{
				for ($j = 1; $j <= $excel->sheets[0]['numCols']; $j++) 
				{
				}
				$m++;
			}
			else
			{
				$company=$issue_purpose=$issue_date=$product_id=$issue_qty=$store_name=$challan_no='';
				$attention=$returnable_qnty=$using_item=$no_of_bag=$weight_per_bag='';
				$wght_cone=$no_of_cone=$floor=$room=$rack=$shelf=$bin_box='';
				$remarks='';
				//echo '<pre>';print_r($excel->sheets[0]['cells'][$i]);die;
				
				$str_rep=array("*",  "=", "\r", "\n", "#");
				//$str_rep= preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $excel->sheets[0]['cells'][$i]); 
				if (isset($excel->sheets[0]['cells'][$i][1])) $company = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][1]);
				if (isset($excel->sheets[0]['cells'][$i][2])) $issue_purpose= str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][2]);
				if (isset($excel->sheets[0]['cells'][$i][3])) $issue_date = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][3]);
				if (isset($excel->sheets[0]['cells'][$i][4])) $product_id = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][4]);
				if (isset($excel->sheets[0]['cells'][$i][5])) $issue_qty = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][5]);
				if (isset($excel->sheets[0]['cells'][$i][6])) $store_name = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][6]);
				if (isset($excel->sheets[0]['cells'][$i][7])) $challan_no = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][7]);
				if (isset($excel->sheets[0]['cells'][$i][8])) $attention= str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][8]);
				if (isset($excel->sheets[0]['cells'][$i][9])) $returnable_qnty = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][9]);
				if (isset($excel->sheets[0]['cells'][$i][10])) $using_item = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][10]);
				if (isset($excel->sheets[0]['cells'][$i][11])) $no_of_bag = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][11]);
				if (isset($excel->sheets[0]['cells'][$i][12])) $weight_per_bag = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][12]);
				if (isset($excel->sheets[0]['cells'][$i][13])) $wght_cone = $excel->sheets[0]['cells'][$i][13];
				if (isset($excel->sheets[0]['cells'][$i][14])) $no_of_cone = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][14]);	
				if (isset($excel->sheets[0]['cells'][$i][15])) $floor = $excel->sheets[0]['cells'][$i][15];
				if (isset($excel->sheets[0]['cells'][$i][16])) $room = $excel->sheets[0]['cells'][$i][16];
				if (isset($excel->sheets[0]['cells'][$i][17])) $rack = $excel->sheets[0]['cells'][$i][17];
				if (isset($excel->sheets[0]['cells'][$i][18])) $shelf = $excel->sheets[0]['cells'][$i][18];
				if (isset($excel->sheets[0]['cells'][$i][19])) $bin_box = $excel->sheets[0]['cells'][$i][19];
				if (isset($excel->sheets[0]['cells'][$i][20])) $remarks = $excel->sheets[0]['cells'][$i][20];
				
				$all_data_arr[$i][1]['company']=trim($company);
				$all_data_arr[$i][2]['issue_purpose']=trim($issue_purpose);
				$all_data_arr[$i][3]['issue_date']=trim($issue_date);
				$all_data_arr[$i][4]['product_id']=trim($product_id);
				$all_data_arr[$i][5]['issue_qty']=trim($issue_qty);
				$all_data_arr[$i][6]['store_name']=trim($store_name);
				$all_data_arr[$i][7]['challan_no']=trim($challan_no);
				$all_data_arr[$i][8]['attention']=trim($attention);
				$all_data_arr[$i][9]['returnable_qnty']=trim($returnable_qnty);
				$all_data_arr[$i][10]['using_item']=trim($using_item);
				$all_data_arr[$i][11]['no_of_bag']=trim($no_of_bag);
				$all_data_arr[$i][12]['weight_per_bag']=trim($weight_per_bag);
				$all_data_arr[$i][13]['wght_cone']=trim($wght_cone);
				$all_data_arr[$i][14]['no_of_cone']=trim($no_of_cone);
				$all_data_arr[$i][15]['floor']=trim($floor);
				$all_data_arr[$i][16]['room']=trim($room);
				$all_data_arr[$i][17]['rack']=trim($rack);
				$all_data_arr[$i][18]['shelf']=trim($shelf);
				$all_data_arr[$i][19]['bin_box']=trim($bin_box);
				$all_data_arr[$i][20]['remarks']=trim($remarks);

			}
		}
				
		$current_date = date("Y-m-d");
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$company_library=return_library_array("select company_name, id from lib_company where status_active=1 and is_deleted=0","company_name","id");
		$yarn_count_library=return_library_array("select yarn_count, id from lib_yarn_count where status_active=1 and is_deleted=0","yarn_count","id");
		$color_library=return_library_array("select color_name, id from lib_color where status_active=1 and is_deleted=0","color_name","id");
		$brand_library=return_library_array( "select id, brand_name from lib_brand where status_active=1 and is_deleted=0",'id','brand_name');

		$sql_supplier=sql_select("select c.id as ID, c.supplier_name as SUPPLIER_NAME, a.tag_company as COMPANY_ID from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id=b.supplier_id and b.party_type=2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name,a.tag_company order by supplier_name");
		$supplier_arr=array();
		foreach ($sql_supplier as $val) {
			$supplier_arr[$val['COMPANY_ID']][$val['SUPPLIER_NAME']] = $val['ID'];
		}	

		$sql_store=sql_select("select a.id as ID, a.store_name as STORE_NAME, a.company_id as COMPANY_ID from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.status_active=1 and a.is_deleted=0 and b.category_type=1");
		$store_arr=array();
		foreach ($sql_store as $val) {
			$store_arr[$val['COMPANY_ID']][$val['STORE_NAME']] = $val['ID'];
		}

		$yarn_issue_purpose = array_flip($yarn_issue_purpose);		
	
		$sql_prod_res=sql_select("select id as ID, company_id as COMPANY_ID, item_category_id as ITEM_CATEGORY_ID, supplier_id as SUPPLIER_ID, yarn_count_id as YARN_COUNT_ID, yarn_comp_type1st as YARN_COMP_TYPE1ST, yarn_comp_percent1st as YARN_COMP_PERCENT1ST, yarn_comp_type2nd as YARN_COMP_TYPE2ND, yarn_comp_percent2nd as YARN_COMP_PERCENT2ND, yarn_type as YARN_TYPE, color as COLOR, current_stock as CURRENT_STOCK, lot as LOT, avg_rate_per_unit as AVG_RATE_PER_UNIT, stock_value as STOCK_VALUE, conversion_factor as CONVERSION_FACTOR, allocated_qnty as ALLOCATED_QNTY,available_qnty as AVAILABLE_QNTY from product_details_master where status_active=1 and is_deleted=0 and item_category_id=1");
		$product_id_data_arr=array();

		foreach ($sql_prod_res as $val)
		{	
			$product_id_data_arr[$val['ID']]['CURRENT_STOCK']=$val['CURRENT_STOCK'];
			$product_id_data_arr[$val['ID']]['ALLOCATED_QNTY']=$val['ALLOCATED_QNTY'];
			$product_id_data_arr[$val['ID']]['AVAILABLE_QNTY']=$val['AVAILABLE_QNTY'];
			$product_id_data_arr[$val['ID']]['AVG_RATE_PER_UNIT']=$val['AVG_RATE_PER_UNIT'];
			$product_id_data_arr[$val['ID']]['STOCK_VALUE']=$val['STOCK_VALUE'];			
			$product_id_data_arr[$val['ID']]['SUPPLIER_ID']=$val['SUPPLIER_ID'];
		}
		//echo '<pre>';print_r($product_data_arr);die;

		$field_array_issue = "id,issue_number_prefix, issue_number_prefix_num, issue_number, issue_basis, issue_purpose, entry_form, item_category, company_id, supplier_id, issue_date, challan_no, store_id, inserted_by, insert_date";

		$field_array_trans = "id,mst_id,receive_basis,company_id,supplier_id,prod_id,item_category,transaction_type,transaction_date,store_id,cons_uom,cons_quantity,return_qnty,cons_rate,cons_amount,no_of_bags,cone_per_bag,weight_per_bag,weight_per_cone,floor_id,room,rack,self,bin_box,using_item,remarks,is_excel,inserted_by,insert_date";

		$field_array_mrr = "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date"; // mrr
		$update_array_trans = "balance_qnty*balance_amount*updated_by*update_date"; // transcation blance update

		$field_array_prod_update = "last_issued_qnty*current_stock*stock_value*available_qnty*updated_by*update_date";

		$data_array_issue=$data_array_trans=$data_array_prod='';
		$duplicate_product_arr=array();
		$row_num_excel=1;
		$is_excel=1;
		$prod_id_update_from_excel=2;

		//echo '<pre>';print_r($all_data_arr);die;

		foreach($all_data_arr as $column_val)
		{
			$row_num_excel++;

			$company_id=$company_library[$column_val[1]['company']];

			if ( $column_val[1]['company']=="" || $company_id=="" )
			{
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Company Name ['.$column_val[1]["company"].'] and Excel row number ['.$row_num_excel.']</p>';
				oci_rollback($con); disconnect($con); die;
			}

			$issue_purpose=$column_val[2]['issue_purpose'];
			if ($issue_purpose ==""){
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Issue purpose ['.$issue_purpose.'] and Excel row number ['.$row_num_excel.']</p>';
				oci_rollback($con); disconnect($con); die;
			}

			$issue_date=$column_val[3]['issue_date'];
			if ($issue_date <= 0 || $issue_date==""){
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill Up Correct Issue Date ['.$column_val[3]['issue_date'].'] and Excel row number ['.$row_num_excel.']</p>';
				oci_rollback($con); disconnect($con); die;
			}


			$product_id=$column_val[4]['product_id'];
			if ($product_id ==""){
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up product id ['.$lot_batch.'] and Excel row number ['.$row_num_excel.']</p>';
				oci_rollback($con); disconnect($con); die;
			}

			$issue_qty=$column_val[5]['issue_qty'];

			if ($issue_qty <= 0 || $issue_qty=="")
			{
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Issue Quantity can not Zero ['.$column_val[5]['issue_qty'].'] and Excel row number ['.$row_num_excel.']</p>';
				oci_rollback($con); disconnect($con); die;
			}

			$store_id=$store_arr[$company_id][$column_val[6]['store_name']];
			if ($column_val[6]['store_name']=="" || $store_id==""){
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Store Name ['.$column_val[6]["store_name"].'] and Excel row number ['.$row_num_excel.']</p>';
				oci_rollback($con); disconnect($con); die;
			}	

			$challan_no=$column_val[7]['challan_no'];
			$attention=$column_val[8]['attention'];
			$return_qnty=$column_val[9]['returnable_qnty'];
			$using_item=$column_val[10]['using_item'];
			$no_of_bag=$column_val[11]['no_of_bag'];
			$weight_per_bag=$column_val[12]['weight_per_bag'];
			$wght_cone=$column_val[13]['wght_cone'];
			$no_of_cone=$column_val[14]['no_of_cone'];
			$floor=$column_val[15]['floor'];
			$room=$column_val[16]['room'];
			$rack=$column_val[17]['rack'];
			$shelf=$column_val[18]['shelf'];
			$bin_box=$column_val[19]['bin_box'];
			$remarks=$column_val[20]['remarks'];

			if ($db_type==0) 
			{	
				$issue_date=change_date_format($issue_date, "Y-m-d", "-",1);
			}
			else
			{
				$issue_date=change_date_format($issue_date, "d-M-y", "-",1);
			}

			$issue_basis=2; 

			$supplier_id = $product_id_data_arr[$product_id]['SUPPLIER_ID'];
			
			$presentStock=$presentStockValue=$presentAvgRate=0;

			$presentStock		= $product_id_data_arr[$product_id]['CURRENT_STOCK'];
			$allocated_qnty		= $product_id_data_arr[$product_id]['ALLOCATED_QNTY'];
			$available_qnty		= $product_id_data_arr[$product_id]['AVAILABLE_QNTY'];
			$presentStockValue	= $product_id_data_arr[$product_id]['STOCK_VALUE'];
			$presentAvgRate		= $product_id_data_arr[$product_id]['AVG_RATE_PER_UNIT'];			
			$issue_stock_value  = $presentAvgRate * $issue_qty;	

			//."**".$supplier_id.

			$master_key = $issue_basis."**".$yarn_issue_purpose[$issue_purpose]."**".$issue_date."**".$challan_no;

			$prepare_data_arr[$company_id][$master_key][$product_id][prod_id] = $product_id;
			$prepare_data_arr[$company_id][$master_key][$product_id][supplier_id] = $supplier_id;
			$prepare_data_arr[$company_id][$master_key][$product_id][store_id] = $store_id;
			$prepare_data_arr[$company_id][$master_key][$product_id][cons_quantity] = $issue_qty;
			$prepare_data_arr[$company_id][$master_key][$product_id][return_qnty] = $return_qnty;
			$prepare_data_arr[$company_id][$master_key][$product_id][cons_rate] = $presentAvgRate;
			$prepare_data_arr[$company_id][$master_key][$product_id][cons_amount] = $issue_stock_value;
			$prepare_data_arr[$company_id][$master_key][$product_id][no_of_bags] = $no_of_bag;
			$prepare_data_arr[$company_id][$master_key][$product_id][cone_per_bag] = $weight_per_bag;
			$prepare_data_arr[$company_id][$master_key][$product_id][weight_per_bag] = $wght_cone;
			$prepare_data_arr[$company_id][$master_key][$product_id][weight_per_cone] = $no_of_cone;		
			$prepare_data_arr[$company_id][$master_key][$product_id][floor_id] = $floor;
			$prepare_data_arr[$company_id][$master_key][$product_id][room] = $room;
			$prepare_data_arr[$company_id][$master_key][$product_id][rack] = $rack;
			$prepare_data_arr[$company_id][$master_key][$product_id]['self'] = $shelf;
			$prepare_data_arr[$company_id][$master_key][$product_id][bin_box] = $bin_box;
			$prepare_data_arr[$company_id][$master_key][$product_id][using_item] = $using_item;
			$prepare_data_arr[$company_id][$master_key][$product_id][remarks] = $remarks;
			$prepare_data_arr[$company_id][$master_key][$product_id][attention] = $attention;
			$prepare_data_arr[$company_id][$master_key][$product_id][is_excel] = 1;
			$prepare_data_arr[$company_id][$master_key][$product_id][current_stock] = $presentStock;
			$prepare_data_arr[$company_id][$master_key][$product_id][current_stock_value] = $presentStockValue;
			$prepare_data_arr[$company_id][$master_key][$product_id][current_avg_rate] = $presentAvgRate;
			$prepare_data_arr[$company_id][$master_key][$product_id][allocated_qnty] = $allocated_qnty;
			$prepare_data_arr[$company_id][$master_key][$product_id][available_qnty] = $available_qnty;
			
			$max_trans_query = sql_select("SELECT max(case when transaction_type in (1,4,5) then transaction_date else null end) as max_date, max(id) as max_id from inv_transaction where prod_id =$product_id and store_id=$store_id and item_category=1 and status_active=1 and transaction_type in (1,4,5)");	

			if(!empty($max_trans_query))
			{
				$max_recv_date = $max_trans_query[0][csf('max_date')];
				
				if($max_recv_date!="")
				{
					$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
					$issueDate = date("Y-m-d", strtotime($issue_date));

					if ($issueDate < $max_recv_date)
					{
						echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Issue Date Can not Be Less Than Last Receive Date Of This Lot.\nIssue Date $issueDate \nReceived date' .$max_recv_date. '['.$product_id.'] and Excel row number ['.$row_num_excel.']</p>'; 
						oci_rollback($con); disconnect($con); die;
					}
				}
			}

			if($presentStock>0) // currtent stock qty should be greater zero
			{
				$issue_qty = ( $issue_qty > $presentStock )?$presentStock:$issue_qty;
				
				if ( $issue_qty > $presentStock ) // Stocking checking 
				{
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Issue Quantity can not be greater than Current Stock quantity of this product product id = '. $product_id. 'Current Stock = ' . $presentStock.'</p>';
					disconnect($con);die;
				}


				$issue_qty = ( $issue_qty > $available_qnty )?$available_qnty:$issue_qty;

				//echo $issue_qty."--".$available_qnty."---".$presentStock; die();

				if ( $issue_qty > $available_qnty)
				{
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Issue Quantity can not be greater than available quantity of this product product id = '. $product_id. ' Available Quantity = ' . $available_qnty.'</p>';
					disconnect($con);die;
				}	
			}
			else
			{
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Current Stock quantity of this product is zero. product id = '. $product_id. ' Current Stock = ' . $presentStock.'</p>';
					disconnect($con);die;
			}

			$prepare_data_arr[$company_id][$master_key][$product_id][cons_quantity] = $issue_qty;
						
		}

		//echo "10**<pre>";
		//print_r($prepare_data_arr); die();

		foreach ($prepare_data_arr as $company_id => $msterDataArr) 
		{
			foreach ($msterDataArr as $master_key=>$dataArr)
			{
				$id = return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master", $con);
				$new_mrr_number = explode("*", return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master",$con,1,$company_id,'YIS',3,date("Y",time()),1 ));

				list($issue_basis,$issue_purpose,$issue_date,$challan_no) = explode("**",$master_key);

				if ($data_array_issue != '') $data_array_issue .=",";
					$data_array_issue .= "(".$id.",'".$new_mrr_number[1]."','".$new_mrr_number[2]."','".$new_mrr_number[0]."','".$issue_basis."','".$issue_purpose."',3,1,'".$company_id."','".$supplier_id."','".$issue_date."','".$challan_no."','".$store_id."','".$user_id."','".$pc_date_time."')";

				foreach ($dataArr as $productId=>$details_row)
				{
					$details_row[floor_id]=$details_row[room]=$details_row[rack]=$details_row['self']=$details_row[bin_box]=0;					
					$trans_id = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);

					if ($data_array_trans != '') $data_array_trans .=",";
					$data_array_trans .= "(".$trans_id.",".$id.",'".$issue_basis."','".$company_id."','".$details_row[supplier_id]."','".$productId."',1,2,'".$issue_date."','".$details_row[store_id]."','12','".$details_row[cons_quantity]."','".$details_row[return_qnty]."','".$details_row[cons_rate]."','".number_format($details_row[cons_amount], $dec_place[4], '.', '')."','".$details_row[no_of_bags]."','".$details_row[cone_per_bag]."','".$details_row[weight_per_bag]."','".$details_row[weight_per_cone]."','".$details_row[floor_id]."','".$details_row[room]."','".$details_row[rack]."','".$details_row['self']."','".$details_row[bin_box]."','".$details_row[using_item]."','".$details_row[remarks]."','".$details_row[is_excel]."','".$user_id."','".$pc_date_time."')";

					$isLIFOfifo = '';
					$check_allocation = '';
					$sql_variable = sql_select("select store_method,variable_list from variable_settings_inventory where company_name=$company_id and variable_list in(17) and item_category_id=1 and status_active=1 and is_deleted=0");
					foreach ($sql_variable as $row) 
					{
						$isLIFOfifo = $row[csf('store_method')];
					}

					if ($isLIFOfifo == 2) $cond_lifofifo = " DESC"; else $cond_lifofifo = " ASC";
					$sql = sql_select("select id,cons_rate,balance_qnty,balance_amount from inv_transaction where prod_id=$productId and store_id=".$details_row[store_id]." and balance_qnty>0 and transaction_type in (1,4,5) and item_category=1 and status_active=1 order by transaction_date,id $cond_lifofifo");

					$mrr_data_array = "";
					$updateID_array = array();
					$update_data_trans = array();
					$cons_rate = 0;

					$issueQnty = $details_row[cons_quantity];

					foreach ($sql as $result) 
					{
						$recv_trans_id = $result[csf("id")]; // this row will be updated
						$balance_qnty = $result[csf("balance_qnty")];
						$balance_amount = $result[csf("balance_amount")];
						$cons_rate = $result[csf("cons_rate")];
						$issueQntyBalance = $balance_qnty - $issueQnty; // minus issue qnty
						$issueStockBalance = $balance_amount - ($issueQnty * $cons_rate);

						if ($issueQntyBalance >= 0) 
						{
							$amount = $issueQnty * $cons_rate;
							//for insert
							$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
							if ($mrr_data_array != "") $mrr_data_array .= ",";
							$mrr_data_array .= "(" . $mrrWiseIsID . "," . $recv_trans_id . "," . $trans_id . ",3," . $productId . "," . $issueQnty . "," . $cons_rate . "," . number_format($amount, $dec_place[4], '.', '') . ",'" . $user_id . "','" . $pc_date_time . "')";
							//for update

							$updateID_array[] = $recv_trans_id;
							$update_data_trans[$recv_trans_id] = explode("*", ("" . $issueQntyBalance . "*" . $issueStockBalance . "*'" . $user_id . "'*'" . $pc_date_time . "'"));
							break;
						} 
						else if ($issueQntyBalance < 0) 
						{
							$issueQntyBalance = $issue_qty - $balance_qnty;
							$issueQnty = $balance_qnty;
							$amount = $issue_qty * $cons_rate;

							//for insert
							$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
							if ($mrr_data_array != "") $mrr_data_array .= ",";
							$mrr_data_array .= "(" . $mrrWiseIsID . "," . $recv_trans_id . "," . $trans_id . ",3," . $product_id . "," . $balance_qnty . "," . $cons_rate . "," . number_format($amount, $dec_place[4], '.', '') . ",'" . $user_id . "','" . $pc_date_time . "')";
							//for update
							$updateID_array[] = $recv_trans_id;
							$update_data_trans[$recv_trans_id] = explode("*", ("0*0*'" . $user_id . "'*'" . $pc_date_time . "'"));
							$issueQnty = $issueQntyBalance;
						}
					}//end foreach

					$newCurrentStock = $details_row[current_stock] - $issueQnty;
					$newStockValue = $details_row[current_stock_value] - ($issueQnty * $details_row[current_avg_rate]);
					$newAvailable_qnty = ($newCurrentStock-$details_row[allocated_qnty]);	

					$updateProdID_array[] = $productId;
					$update_product_data[$productId]=explode("*",("".$issueQnty."*".$newCurrentStock."*".number_format($newStockValue, $dec_place[4], '.', '')."*".$newAvailable_qnty."*".$user_id."*'".$pc_date_time."'"));
				} // end prod id loop
			}
		}


		$flag=1;
		if ($data_array_issue != ""){
			$rID1=sql_insert("inv_issue_master",$field_array_issue,$data_array_issue,0);
			if ($flag==1){
				if ($rID1) $flag=1; else $flag=0;
			}
		}

		
		if ($data_array_trans != ""){
			$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
			if ($flag==1){
				if ($rID2) $flag=1; else $flag=0;
			}
		}

		if ($mrr_data_array != ""){
			$rID3 = sql_insert("inv_mrr_wise_issue_details", $field_array_mrr, $mrr_data_array, 0);
			if ($flag==1){
				if ($rID3) $flag=1; else $flag=0;
			}
		}		

		if (!empty($update_data_trans)){
			$upTrID = execute_query(bulk_update_sql_statement("inv_transaction", "id", $update_array_trans, $update_data_trans, $updateID_array),0);

			if ($flag==1){
				if ($upTrID) $flag=1; else $flag=0;
			}
		}

		if (!empty($update_product_data)){
			$prodUpdate=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_prod_update,$update_product_data,$updateProdID_array));
			if ($flag==1){
				if ($prodUpdate) $flag=1; else $flag=0;
			}
		}

		//echo "10** insert into inv_issue_master ($field_array_issue) values $data_array_issue";die;
		//echo "10** insert into inv_transaction ($field_array_trans) values $data_array_trans";die;
		//echo "10** insert into inv_mrr_wise_issue_details ($field_array_mrr) values $mrr_data_array"; die();
		//echo "10**".bulk_update_sql_statement("inv_transaction", "id", $update_array_trans, $update_data_trans, $updateID_array); die();
		//echo "10**".bulk_update_sql_statement("product_details_master","id",$field_array_prod_update,$update_product_data,$updateProdID_array); die();

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Excel File Upload Successfully...</p><br/>';
			}
			else
			{
				mysql_query("ROLLBACK");
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Excel File is not Uploaded...</p><br/>';
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Excel File Upload Successfully...</p><br/>';
			}
			else
			{
				oci_rollback($con);
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Excel File is not Uploaded...</p><br/>';		
			}
		}
		disconnect($con);
		die;
	}
	else
	{
		echo "Failed";	
	}
	die;
}

?>	
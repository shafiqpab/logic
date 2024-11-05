<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
$permission=$_SESSION['page_permission'];

function return_domestic_rate($rate,$ile_cost,$exchange_rate,$conversion_factor){
	$rate_ile=$rate+$ile_cost;
	$rate_ile_exchange=$rate_ile*$exchange_rate;
	$doemstic_rate=$rate_ile_exchange/$conversion_factor;
	return $doemstic_rate;	
}


if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	extract($_REQUEST);
	$flag=1;

	if ($operation==0)  // Insert Here
	{	
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		//table lock here 	
		//$item_group_library=array();
		$company_library=return_library_array("select company_name, id from lib_company where status_active=1 and is_deleted=0","company_name","id");
		//$item_group_library=return_library_array("select item_name, id from lib_item_group where status_active=1 and is_deleted=0","item_name","id");
		$item_category_library=return_library_array("select short_name, category_id from lib_item_category_list where status_active=1 and is_deleted=0", "short_name", "category_id");
		$supplier_library=return_library_array("select supplier_name, id from lib_supplier where status_active=1 and is_deleted=0", "supplier_name", "id");
		$country_library=return_library_array("select country_name, id from lib_country where status_active=1 and is_deleted=0","country_name","id");

		$sql_store=sql_select("select a.id, a.store_name, a.company_id from lib_store_location a where a.status_active=1 and a.is_deleted=0");
		$store_arr=array();
		foreach ($sql_store as $val) {
			$store_arr[$val[csf('company_id')]][$val[csf('store_name')]] = $val[csf('id')];
		}

		$sql_item_group=sql_select("select id, item_name, item_category from lib_item_group where status_active=1 and is_deleted=0");
		$item_group_arr=array();
		foreach ($sql_item_group as $val) {
			$item_group_arr[$val[csf('item_category')]][$val[csf('item_name')]]=$val[csf('id')];
		}

		$sql_sub_group=sql_select("select id, item_category_id, item_group_id, sub_group_code, sub_group_name from lib_item_sub_group where status_active=1 and is_deleted=0");
		$sub_group_arr=array();
		foreach ($sql_sub_group as $val) {
			$sub_group_arr[$val[csf('item_category_id')]][$val[csf('item_group_id')]][$val[csf('sub_group_name')]]['item_sub_group_id'] = $val[csf('id')];
			$sub_group_arr[$val[csf('item_category_id')]][$val[csf('item_group_id')]][$val[csf('sub_group_name')]]['sub_group_code'] = $val[csf('sub_group_code')];
			$sub_group_arr[$val[csf('item_category_id')]][$val[csf('item_group_id')]][$val[csf('sub_group_name')]]['sub_group_name'] = $val[csf('sub_group_name')];
		}

		$unit_of_measurement_arr = array_flip($unit_of_measurement);
		$currency_arr = array_flip($currency);
		$pay_mode_arr = array_flip($pay_mode);
		$source_arr = array_flip($source);
		$fixed_asset_arr = array_flip($yes_no);		

		$field_array="id,entry_form,company_id,item_category_id,item_group_id,item_sub_group_id,sub_group_code,sub_group_name,item_code,item_description,product_name_details,item_size,re_order_label,minimum_label,maximum_label,unit_of_measure,order_uom,conversion_factor,item_account,item_number,brand_name,model,origin,fixed_asset,current_stock,avg_rate_per_unit,stock_value,supplier_id,store_id, status_active,is_deleted,inserted_by,insert_date";

		$field_array_update="current_stock*avg_rate_per_unit*stock_value*supplier_id*store_id*updated_by*update_date";
		
		$field_array1="id, recv_number_prefix, recv_number_prefix_num, recv_number, entry_form, company_id, receive_basis, receive_date, booking_id, booking_no, challan_no, store_id, exchange_rate, currency_id, supplier_id, lc_no, pay_mode, source, supplier_referance,receive_purpose,loan_party, remarks, store_sl_no, rcvd_book_no,addi_challan_date,bill_no,bill_date,purchaser_name,carried_by,qc_check_by,receive_by,gate_entry_by,gate_entry_date,addi_rcvd_date,gate_entry_no, inserted_by, insert_date";
		
		$field_array_trams = "id,mst_id,receive_basis,pi_wo_batch_no,company_id,supplier_id,prod_id,item_category,transaction_type,transaction_date,store_id,order_uom,order_qnty,order_rate,order_ile,order_ile_cost,order_amount,cons_uom,cons_quantity,cons_rate,cons_ile,cons_ile_cost,cons_amount,balance_qnty,balance_amount,floor_id,room,rack,self,bin_box,expire_date,remarks,inserted_by,insert_date,batch_lot";
		//echo 'system';
		//echo '<pre>';print_r($item_category_library);		
		for($i=1; $i<=$total_row; $i++)
		{
			//$item_group='';$item_group_id='';
			$company="company_".$i;
			$company_id=$company_library[$$company];
			//$item_category_val = $item_category_arr[$column_val[1]['item_category_val']];
			if ($$company=="" || $company_id==""){
				echo "10**Please Fill-Up Correct Company Name ['".$$company."']...";
				disconnect($con);die;
			}

			$item_ategory="item_ategory_".$i;
			$item_ategory_id=$item_category_library[$$item_ategory];
			if ($$item_ategory=="" || $item_ategory_id==""){
				echo "10**Please Fill-Up Correct Item Category ['".$$item_ategory."']...";
				disconnect($con);die;
			}

			$item_group="item_group_".$i;
			$item_group_id=$item_group_arr[$item_ategory_id][$$item_group];
			if ($$item_group=="" || $item_group_id==""){
				echo "10**Please Fill-Up Correct Item Group ['".$$item_group."']...";
				disconnect($con);die;
			}

			$sub_group_code="sub_group_code_".$i;
			$sub_group_name="sub_group_name_".$i;
			$item_sub_group_id=$sub_group_arr[$item_ategory_id][$item_group_id][$$sub_group_name]['item_sub_group_id'];
			$sub_group_code_val=$sub_group_arr[$item_ategory_id][$item_group_id][$$sub_group_name]['sub_group_code'];			
			$sub_group_name_val=$sub_group_arr[$item_ategory_id][$item_group_id][$$sub_group_name]['sub_group_name'];

			$item_code="item_code_".$i;			
			$item_description="item_description_".$i;
			$item_size="item_size_".$i;
			$re_order_level="re_order_level_".$i;
			$min_level="min_level_".$i;
			$max_level="max_level_".$i;

			$order_uom="order_uom_".$i;
			$order_uom_id=$unit_of_measurement_arr[$$order_uom];
			if ($$order_uom=="" || $order_uom_id==""){
				echo "10**Please Fill-Up Correct Order UOM ['".$$order_uom."']...";
				disconnect($con);die;
			}

			$cons_uom="cons_uom_".$i;
			$cons_uom_id=$unit_of_measurement_arr[$$cons_uom];
			if ($$cons_uom=="" || $cons_uom_id==""){
				echo "10**Please Fill-Up Correct Cons UOM ['".$$cons_uom."']...";
				disconnect($con);die;
			}

			$conversion_factor="conversion_factor_".$i;
			$item_account="item_account_".$i;
			$brand="brand_".$i;

			$origin="origin_".$i;
			$origin_id=$country_library[$$origin];
			if ($$origin !=""){
				if ($origin_id==""){
					echo "10**Please Fill-Up Correct Origin ['".$$origin."']...";
					disconnect($con);die;
				}				
			}

			$fixed_asset="fixed_asset_".$i;
			$fixed_asset_id=$fixed_asset_arr[$$fixed_asset];
			if ($$fixed_asset !=""){
				if ($fixed_asset_id==""){
					echo "10**Please Fill-Up Correct Fixed Asset ['".$$fixed_asset."']...";
					disconnect($con);die;
				}				
			}

			$item_number="item_number_".$i;
			$model="model_".$i;

			$stock_qty="stock_qty_".$i;
			$rate="rate_".$i;

			$supplier="supplier_".$i;
			$supplier_id=$supplier_library[$$supplier];
			if ($$supplier !=""){
				if ($supplier_id==""){
					echo "10**Please Fill-Up Correct Supplier ['".$$supplier."']...";
					disconnect($con);die;
				}				
			}

			$currency="currency_".$i;
			$currencyr_id=$currency_arr[$$currency];
			if ($$currency !=""){
				if ($currencyr_id==""){
					echo "10**Please Fill-Up Correct currency ['".$$currency."']...";
					disconnect($con);die;
				}				
			}

			$store_name="store_name_".$i;
			$store_name_id=$store_arr[$company_id][$$store_name];
			if ($$store_name !=""){
				if ($store_name_id==""){
					echo "10**Please Fill-Up Correct Store Name ['".$$store_name."']...";
					disconnect($con);die;
				}				
			}

			$pay_mode="pay_mode_".$i;
			$pay_mode_id=$pay_mode_arr[$$pay_mode];
			if ($$pay_mode !=""){
				if ($pay_mode_id==""){
					echo "10**Please Fill-Up Correct Pay Mode ['".$$pay_mode."']...";
					disconnect($con);die;
				}				
			}

			$source="source_".$i;
			$source_id=$source_arr[$$source];
			if ($$source !=""){
				if ($source_id==""){
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Source['.$$source.']...</p>';
					echo "10**Please Fill-Up Correct Source ['".$$source."']...";
					disconnect($con);die;
				}				
			}


			

			if ($$stock_qty != '')
			{
				$sql_prod_res=sql_select("select ID, COMPANY_ID, ITEM_CATEGORY_ID, ITEM_GROUP_ID, SUB_GROUP_NAME, ITEM_DESCRIPTION, ITEM_SIZE, MODEL, ITEM_NUMBER, ITEM_CODE, CURRENT_STOCK from product_details_master where current_stock=0 and status_active=1 and is_deleted=0");
				foreach ($sql_prod_res as $val)
				{
					$key=$val['COMPANY_ID'].'**'.$val['ITEM_CATEGORY_ID'].'**'.$val['ITEM_GROUP_ID'].'**'.$val['SUB_GROUP_NAME'].'**'.$val['ITEM_DESCRIPTION'].'**'.$val['ITEM_SIZE'].'**'.$val['MODEL'].'**'.$val['ITEM_NUMBER'].'**'.$val['ITEM_CODE'];
					$product_data_arr[$key] = $val['ID'];
					//$product_company_arr[$val['ID']]=$val['COMPANY_ID'];
				}

				$product_data=$company_id.'**'.$item_ategory_id.'**'.$item_group_id.'**'.$item_sub_group_id.'**'.$$item_description.'**'.$$item_size.'**'.$$model.'**'.$$item_number.'**'.$$item_code;
				$product_id="";
				if (array_key_exists($product_data,$product_data_arr)){
					$product_id= $product_data_arr[$product_data];
				}

				// if stock zero then update product details master else insert				
				if ($product_id != "")
				{
					$stock_value=$$stock_qty*$$rate;
					$updateID_array[] = $product_id;
					$update_product_data[$product_id]=explode("*",("".$$stock_qty."*".$$rate."*".$stock_value."*".$supplier_id."*".$store_name_id."*".$user_id."*'".$pc_date_time."'"));

					$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);
					$dtlsid = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
					$new_recv_number = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master",$con,1,str_replace("'","",$company_id),'GIR',20,date("Y",time())));

					$receive_basis=4;
					if ($db_type==0) $receive_date = date("Y-m-d", strtotime(str_replace("'", "",  date("Y-m-d"))));
					else $receive_date = date("d-M-Y", strtotime(str_replace("'", "",  date("Y-m-d"))));
					$txt_wo_pi_req_id=0;
					$txt_wo_pi_req=0;
					$txt_challan_no='';

					if ($db_type==0) $conversion_date=change_date_format($receive_date, "Y-m-d", "-",1);
					else $conversion_date=change_date_format($receive_date, "d-M-y", "-",1);
					$exchange_rate=set_conversion_rate( $currencyr_id, $conversion_date );

					$hidden_lc_id=0;
					$txt_sup_ref='';
					$receive_purpose=0;
					$loan_party=0;
					$txt_remarks=$txt_store_sl_no=$txt_book_no=$txt_challan_date='';
					$txt_bill_no=$txt_bill_date=$cbo_purchaser_name=$cbo_carried_by='';
					$cbo_qc_check_by=$cbo_receive_by=$cbo_gate_entry_by='';
					$txt_gate_entry_date=$txt_addi_rcvd_date=$txt_gate_entry_no='';

					if ($data_array1 != '') $data_array1 .=",";
					$data_array1 .="(".$id.",'".$new_recv_number[1]."','".$new_recv_number[2]."','".$new_recv_number[0]."',20,".$company_id.",".$receive_basis.",'".$receive_date."',".$txt_wo_pi_req_id.",".$txt_wo_pi_req.",'".$txt_challan_no."','".$store_name_id."','".$exchange_rate."','".$currencyr_id."','".$supplier_id."','".$hidden_lc_id."','".$pay_mode_id."','".$source_id."','".$txt_sup_ref."','".$receive_purpose."','".$loan_party."','".$txt_remarks."','".$txt_store_sl_no."','".$txt_book_no."','".$txt_challan_date."','".$txt_bill_no."','".$txt_bill_date."','".$cbo_purchaser_name."','".$cbo_carried_by."','".$cbo_qc_check_by."','".$cbo_receive_by."','".$cbo_gate_entry_by."','".$txt_gate_entry_date."','".$txt_addi_rcvd_date."','".$txt_gate_entry_no."','".$user_id."','".$pc_date_time."')";

					// data array Transaction Table
					$txt_receive_qty=$$stock_qty;
					$txt_rate=$$rate;
					$txt_amount=$txt_receive_qty*$txt_rate;
					$ile=$ile_cost=0;
					//echo $txt_rate.'**'.$ile_cost.'**'.$exchange_rate.'**'.$$conversion_factor;
					$domestic_rate = return_domestic_rate($txt_rate,$ile_cost,$exchange_rate,$$conversion_factor);
					//echo $domestic_rate.'system';
					$cons_rate = number_format($domestic_rate,$dec_place[3],".","");
					$con_quantity = $$conversion_factor*$txt_receive_qty;
					$con_amount = $cons_rate*$con_quantity;

					$con_ile=$con_ile_cost=0;
					$cbo_floor=$cbo_room=$txt_rack=$txt_shelf=$cbo_bin=0;
					$txt_warranty_date=$txt_referance=$txt_lot='';

					if ($data_array_trans != '') $data_array_trans .=",";
					$data_array_trans .= "(".$dtlsid.",".$id.",".$receive_basis.",".$txt_wo_pi_req_id.",".$company_id.",".$supplier_id.",".$product_id.",".$item_ategory_id.",1,'".$receive_date."','".$store_name_id."','".$order_uom_id."','".$txt_receive_qty."','".$txt_rate."','".$ile."','".$ile_cost."','".$txt_amount."','".$cons_uom_id."','".$con_quantity."','".$cons_rate."','".$con_ile."','".$con_ile_cost."','".$con_amount."','".$con_quantity."','".$con_amount."','".$cbo_floor."','".$cbo_room."','".$txt_rack."','".$txt_shelf."','".$cbo_bin."','".$txt_warranty_date."','".$txt_referance."','".$user_id."','".$pc_date_time."','".$txt_lot."')";
				}
				else
				{
					if($db_type==2)
					{
						$duplicate_cond='';
						if ($sub_group_name_val=='') $duplicate_cond.=" and sub_group_name is null"; else $duplicate_cond.=" and sub_group_name='".$sub_group_name_val."'";				
						if ($$item_description=='') $duplicate_cond .=" and item_description is null"; else $duplicate_cond.=" and item_description='".$$item_description."'";
						if ($$item_size=='') $duplicate_cond .=" and item_size is null"; else $duplicate_cond.=" and item_size='".$$item_size."'";
						if ($$model=='') $duplicate_cond .=" and model is null"; else $duplicate_cond.=" and model='".$$model."'";
						$duplicate = is_duplicate_field("id","product_details_master","company_id=$company_id and item_category_id=$item_ategory_id and item_group_id=$item_group_id $duplicate_cond and is_deleted=0 ");
						//echo $duplicate.'system';
						$duplicate_cond='';
						if ($sub_group_name_val=='') $duplicate_cond.=" and sub_group_name is null"; else $duplicate_cond.=" and sub_group_name='".$sub_group_name_val."'";
						if ($$item_number=='') $duplicate_cond .=" and item_number is null"; else $duplicate_cond.=" and item_number='".$txt_item_no."'";
						if ($$item_code=='') $duplicate_cond .=" and item_code is null"; else $duplicate_cond.=" and item_code='".$item_code."'";				
						$duplicate_item = is_duplicate_field("id","product_details_master","company_id=$company_id and item_category_id=$item_ategory_id and item_group_id=$item_group_id $duplicate_cond and is_deleted=0 ");
					}
					else
					{

						$duplicate = is_duplicate_field("id","product_details_master","company_id=$company_id and item_category_id=$item_ategory_id and item_group_id=$item_group_id and sub_group_name='".$sub_group_name_val."' and item_description='".$$item_description."' and item_size='".$$item_size."' and model='".$$model."' and is_deleted=0");
						$duplicate_item = is_duplicate_field("id","product_details_master","company_id=$company_id and item_category_id=$item_ategory_id and item_group_id=$item_group_id and sub_group_name='".$sub_group_name_val."' and item_number='".$$item_number."' and item_code='".$$item_code."' and is_deleted=0");
					}

					if ($$item_number=='')  $duplicate_item='';
					if($duplicate==1)
					{
						echo "11**Dublicate Product is Not Allow.";
						disconnect($con);
						die;
					}
					if($duplicate_item==1)
					{
						echo "11**Dublicate Item Number is Not Allow.";
						disconnect($con);
						die;
					}

					$productname = $$item_group." ".$$item_description." ".$$item_size;
					if ($item_ategory_id==4) $entry_form_lib=20; else $entry_form_lib=0;
					// data array Product Details Master Table
					$prod_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
					$dtlsid = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
					$stock_value=$$stock_qty*$$rate;
					if ($data_array != '') $data_array .=",";				
					$data_array.="(".$prod_id.",".$entry_form_lib.",".$company_id.",".$item_ategory_id.",".$item_group_id.",'".$item_sub_group_id."','".$sub_group_code_val."','".$sub_group_name_val."','".$$item_code."','".$$item_description."','".$productname."','".$$item_size."','".$$re_order_level."','".$$min_level."','".$$max_level."','".$order_uom_id."','".$cons_uom_id."','".$$conversion_factor."','".$$item_account."','".$$item_number."','".$$brand."','".$$model."','".$origin_id."','".$fixed_asset_id."','".$$stock_qty."','".$$rate."','".$stock_value."','".$supplier_id."','".$store_name_id."',1,0,'".$user_id."','".$pc_date_time."')";
					//$prod_id=$prod_id+1;


					// data array inv receive master Table
					$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);
					$new_recv_number = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master",$con,1,str_replace("'","",$company_id),'GIR',20,date("Y",time())));

					$receive_basis=4;
					if ($db_type==0) $receive_date = date("Y-m-d", strtotime(str_replace("'", "",  date("Y-m-d"))));
					else $receive_date = date("d-M-Y", strtotime(str_replace("'", "",  date("Y-m-d"))));
					$txt_wo_pi_req_id=0;
					$txt_wo_pi_req=0;
					$txt_challan_no='';

					if ($db_type==0) $conversion_date=change_date_format($receive_date, "Y-m-d", "-",1);
					else $conversion_date=change_date_format($receive_date, "d-M-y", "-",1);
					$exchange_rate=set_conversion_rate( $currencyr_id, $conversion_date );

					$hidden_lc_id=0;
					$txt_sup_ref='';
					$receive_purpose=0;
					$loan_party=0;
					$txt_remarks=$txt_store_sl_no=$txt_book_no=$txt_challan_date='';
					$txt_bill_no=$txt_bill_date=$cbo_purchaser_name=$cbo_carried_by='';
					$cbo_qc_check_by=$cbo_receive_by=$cbo_gate_entry_by='';
					$txt_gate_entry_date=$txt_addi_rcvd_date=$txt_gate_entry_no='';

					if ($data_array1 != '') $data_array1 .=",";
					$data_array1 .="(".$id.",'".$new_recv_number[1]."','".$new_recv_number[2]."','".$new_recv_number[0]."',20,".$company_id.",".$receive_basis.",'".$receive_date."',".$txt_wo_pi_req_id.",".$txt_wo_pi_req.",'".$txt_challan_no."','".$store_name_id."','".$exchange_rate."','".$currencyr_id."','".$supplier_id."','".$hidden_lc_id."','".$pay_mode_id."','".$source_id."','".$txt_sup_ref."','".$receive_purpose."','".$loan_party."','".$txt_remarks."','".$txt_store_sl_no."','".$txt_book_no."','".$txt_challan_date."','".$txt_bill_no."','".$txt_bill_date."','".$cbo_purchaser_name."','".$cbo_carried_by."','".$cbo_qc_check_by."','".$cbo_receive_by."','".$cbo_gate_entry_by."','".$txt_gate_entry_date."','".$txt_addi_rcvd_date."','".$txt_gate_entry_no."','".$user_id."','".$pc_date_time."')";

					// data array Transaction Table
					$txt_receive_qty=$$stock_qty;
					$txt_rate=$$rate;
					$txt_amount=$txt_receive_qty*$txt_rate;
					$ile=$ile_cost=0;
					//echo $txt_rate.'**'.$ile_cost.'**'.$exchange_rate.'**'.$$conversion_factor;
					$domestic_rate = return_domestic_rate($txt_rate,$ile_cost,$exchange_rate,$$conversion_factor);
					//echo $domestic_rate.'system';
					$cons_rate = number_format($domestic_rate,$dec_place[3],".","");
					$con_quantity = $$conversion_factor*$txt_receive_qty;
					$con_amount = $cons_rate*$con_quantity;

					$con_ile=$con_ile_cost=0;
					$cbo_floor=$cbo_room=$txt_rack=$txt_shelf=$cbo_bin=0;
					$txt_warranty_date=$txt_referance=$txt_lot='';

					if ($data_array_trans != '') $data_array_trans .=",";
					$data_array_trans .= "(".$dtlsid.",".$id.",".$receive_basis.",".$txt_wo_pi_req_id.",".$company_id.",".$supplier_id.",".$prod_id.",".$item_ategory_id.",1,'".$receive_date."','".$store_name_id."','".$order_uom_id."','".$txt_receive_qty."','".$txt_rate."','".$ile."','".$ile_cost."','".$txt_amount."','".$cons_uom_id."','".$con_quantity."','".$cons_rate."','".$con_ile."','".$con_ile_cost."','".$con_amount."','".$con_quantity."','".$con_amount."','".$cbo_floor."','".$cbo_room."','".$txt_rack."','".$txt_shelf."','".$cbo_bin."','".$txt_warranty_date."','".$txt_referance."','".$user_id."','".$pc_date_time."','".$txt_lot."')";
				}	

			}
			else
			{
				if($db_type==2)
				{
					$duplicate_cond='';
					if ($sub_group_name_val=='') $duplicate_cond.=" and sub_group_name is null"; else $duplicate_cond.=" and sub_group_name='".$sub_group_name_val."'";				
					if ($$item_description=='') $duplicate_cond .=" and item_description is null"; else $duplicate_cond.=" and item_description='".$$item_description."'";
					if ($$item_size=='') $duplicate_cond .=" and item_size is null"; else $duplicate_cond.=" and item_size='".$$item_size."'";
					if ($$model=='') $duplicate_cond .=" and model is null"; else $duplicate_cond.=" and model='".$$model."'";
					$duplicate = is_duplicate_field("id","product_details_master","company_id=$company_id and item_category_id=$item_ategory_id and item_group_id=$item_group_id $duplicate_cond and is_deleted=0 ");
					//echo $duplicate.'system';
					$duplicate_cond='';
					if ($sub_group_name_val=='') $duplicate_cond.=" and sub_group_name is null"; else $duplicate_cond.=" and sub_group_name='".$sub_group_name_val."'";
					if ($$item_number=='') $duplicate_cond .=" and item_number is null"; else $duplicate_cond.=" and item_number='".$txt_item_no."'";
					if ($$item_code=='') $duplicate_cond .=" and item_code is null"; else $duplicate_cond.=" and item_code='".$item_code."'";				
					$duplicate_item = is_duplicate_field("id","product_details_master","company_id=$company_id and item_category_id=$item_ategory_id and item_group_id=$item_group_id $duplicate_cond and is_deleted=0 ");
				}
				else
				{

					$duplicate = is_duplicate_field("id","product_details_master","company_id=$company_id and item_category_id=$item_ategory_id and item_group_id=$item_group_id and sub_group_name='".$sub_group_name_val."' and item_description='".$$item_description."' and item_size='".$$item_size."' and model='".$$model."' and is_deleted=0");
					$duplicate_item = is_duplicate_field("id","product_details_master","company_id=$company_id and item_category_id=$item_ategory_id and item_group_id=$item_group_id and sub_group_name='".$sub_group_name_val."' and item_number='".$$item_number."' and item_code='".$$item_code."' and is_deleted=0");
				}

				if ($$item_number=='')  $duplicate_item='';
				if($duplicate==1)
				{
					echo "11**Dublicate Product is Not Allow.";
					disconnect($con);
					die;
				}
				if($duplicate_item==1)
				{
					echo "11**Dublicate Item Number is Not Allow.";
					disconnect($con);
					die;
				}

				$productname = $$item_group." ".$$item_description." ".$$item_size;
				if ($item_ategory_id==4) $entry_form_lib=20; else $entry_form_lib=0;

				$stock_qty=0;
				$rate=0;
				$stock_value=0;
				$supplier_id=0;
				$store_name_id=0;
				$prod_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
				if ($data_array != '') $data_array .=",";
				$data_array.="(".$prod_id.",".$entry_form_lib.",".$company_id.",".$item_ategory_id.",".$item_group_id.",'".$item_sub_group_id."','".$sub_group_code_val."','".$sub_group_name_val."','".$$item_code."','".$$item_description."','".$productname."','".$$item_size."','".$$re_order_level."','".$$min_level."','".$$max_level."','".$order_uom_id."','".$cons_uom_id."','".$$conversion_factor."','".$$item_account."','".$$item_number."','".$$brand."','".$$model."','".$origin_id."','".$fixed_asset_id."','".$stock_qty."','".$rate."','".$stock_value."','".$supplier_id."','".$store_name_id."',1,0,'".$user_id."','".$pc_date_time."')";
			}	

		}
		//echo "10** insert into product_details_master ($field_array) values $data_array";die;
		//echo "10** insert into inv_receive_master ($field_array1) values $data_array1";
		//echo "10** insert into inv_transaction ($field_array_trams) values $data_array_trans";die;
		$flag=1;
		if ($data_array != ""){
			$rID=sql_insert("product_details_master",$field_array,$data_array,0);
			if ($flag==1){
				if ($rID) $flag=1; else $flag=0;
			}
		}


		if (!empty($update_product_data)){
			$prodUpdate=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_update,$update_product_data,$updateID_array));
			if ($flag==1){
				if ($prodUpdate) $flag=1; else $flag=0;
			}
		}

		

		if ($data_array1 != ''){
			$rID2=sql_insert("inv_receive_master",$field_array1,$data_array1,0);
			if ($flag==1){
				if ($rID2) $flag=1; else $flag=0;
			}
		}


		
		if ($data_array_trans != ''){
			$rID3=sql_insert("inv_transaction",$field_array_trams,$data_array_trans,0);
			if ($flag==1){
				if ($rID3) $flag=1; else $flag=0;
			}
		}

		//echo "10**$flag";die;		

		$commit_msg="Data is Saveed!!";
		$roll_back_msg="Data is not saved!!";

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$commit_msg;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$roll_back_msg;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);  
				echo "0**".$commit_msg;
			}
			else
			{
				oci_rollback($con); 
				echo "10**".$roll_back_msg;
			}
		}
		disconnect($con);
		die;
	}
}

?>
<?php
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == '' ) { header('location:login.php'); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$userCredential = sql_select("select unit_id as company_id, item_cate_id, company_location_id, store_location_id from user_passwd where id=$user_id");
$store_credential_id = $userCredential[0][csf('store_location_id')];

if ($store_credential_id !='') {
    $store_credential_cond = "and a.id in($store_credential_id)";
}

if ($action=='load_drop_down_store')
{
	$data = explode('_',$data);
	$companyIds = $data[0];
	$categoryTypes = $data[1];
	$storeConds = " and a.company_id in($companyIds)";

	if($categoryTypes != '') {
		$storeConds .= " and b.category_type in($categoryTypes)";
	}

 	echo create_drop_down( 'cbo_store_name', 170, "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 $storeConds $store_credential_cond group by a.id,a.store_name order by a.store_name", 'id,store_name', 0, '-- Select Store --', '', 0, '', 1 );
	exit();
}

if ($action=='item_account_popup')
{
	echo load_html_head_contents('Popup Info', '../../../../', 1, 1, '');
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data);  
	?>	
    <script>
		var selected_id = new Array(); selected_name = new Array(); selected_attach_id = new Array();
		function check_all_data()
		{
			var tbl_row_count = document.getElementById('list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
			}
		}
		 
		function toggle( x, origColor ) {
				var newColor = 'yellow';
				if ( x.style ) {
					x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
				}
			}
			
		function js_set_value(id)
		{
			//alert (id)
			var str=id.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
			var strdt=str[2];
			str=str[1];
		
			if( jQuery.inArray(  str , selected_id ) == -1 ) {
				selected_id.push( str );
				selected_name.push( strdt );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str  ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i,1 );
			}
			var id = '';
			var ddd='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				ddd += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			ddd = ddd.substr( 0, ddd.length - 1 );
			$('#item_account_id').val( id );
			$('#item_account_val').val( ddd );
		} 
		  
	</script>
     <input type="hidden" id="item_account_id" />
     <input type="hidden" id="item_account_val" />
 <?php	
	$sql="select id, item_category_id, product_name_details from product_details_master where item_category_id in($data[1]) and status_active=1 and is_deleted=0"; 
	$arr=array(0=>$item_category);
	echo create_list_view('list_view', 'Item Category,Fabric Description,Product ID', '120,250,60', '490', '300', 0, $sql, 'js_set_value', 'id,product_name_details', '', 1, 'item_category_id,0,0', $arr, 'item_category_id,product_name_details,id', '', "setFilterGrid('list_view',-1);", '0,0,0', '', 1) ;
	//echo create_list_view("tbl_list_search", "Party Name", "380","380","270",0, $sql , "js_set_value", "id,party_name", "", 1, "0", $arr , "party_name", "",'setFilterGrid("tbl_list_search",-1);','0','',1) ;

	exit();
}

if($action == 'generate_report_2') {
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	
	$year_start_date = "01-01-$cbo_year";
	$year_end_date = "31-12-$cbo_year";
	$toDay=date("Y-m-d");
	$tmpType = 985;
	$material_placement_arr = array(1=>'Top Floor', 2=>'Bulding Side', 3=>'Old Store', 4=>'Tin Shade');

	$product_library = return_library_array("select id, product_name_details from product_details_master where status_active=1 and is_deleted=0", 'id', 'product_name_details');
	$store_library = return_library_array("select a.id, a.store_name from lib_store_location a where a.status_active=1 and a.is_deleted=0", 'id', 'store_name');
	$buyer_library = return_library_array("select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($cbo_company_id) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", 'id', 'buyer_name');
	$supplier_library = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$color_library = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	
	// echo $year_end_date;die;
	// if($from_date !='' && $to_date !=''){
	// 	if($db_type==0)
	// 	{
	// 		if ($from_date!="" &&  $to_date!="") $receive_date_cond  = "and a.receive_date between '".change_date_format($from_date, "yyyy-mm-dd", "-")."' and '".change_date_format($to_date, "yyyy-mm-dd", "-")."'"; else $receive_date_cond ="";
	// 	}
	// 	if($db_type==2)
	// 	{
	// 		if ($from_date!="" &&  $to_date!="") $receive_date_cond  = "and a.receive_date between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'"; else $receive_date_cond ="";
	// 	}
	// 	if($db_type==0) {
	// 		$receive_date_cond = "and a.receive_date between '".change_date_format($year_start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($to_date, "yyyy-mm-dd", "-")."'";
	// 		$issue_date_cond =  "and a.selling_date between '".change_date_format($year_start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($to_date, "yyyy-mm-dd", "-")."'";
	// 	}
	// 	if($db_type==2) {
	// 		$receive_date_cond = "and a.receive_date between '".change_date_format($year_start_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";
	// 		$issue_date_cond = "and a.selling_date between '".change_date_format($year_start_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";
	// 	}
	// }
	if($from_date !='' && $to_date !=''){

		if($db_type==0) {
			$receive_date_cond = "and a.receive_date between '".change_date_format($from_date, "yyyy-mm-dd", "-")."' and '".change_date_format($to_date, "yyyy-mm-dd", "-")."'";
			$issue_date_cond =  "and a.selling_date between '".change_date_format($from_date, "yyyy-mm-dd", "-")."' and '".change_date_format($to_date, "yyyy-mm-dd", "-")."'";
		}
		if($db_type==2) {
			$receive_date_cond = "and a.receive_date between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";
			$issue_date_cond = "and a.selling_date between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";
		}
	}else{
		if($db_type==0) {
			$receive_date_cond = "and a.receive_date between '".change_date_format($year_start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($year_end_date, "yyyy-mm-dd", "-")."'";
			$issue_date_cond =  "and a.selling_date between '".change_date_format($year_start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($year_end_date, "yyyy-mm-dd", "-")."'";
		}
		if($db_type==2) {
			$receive_date_cond = "and a.receive_date between '".change_date_format($year_start_date,'','',1)."' and '".change_date_format($year_end_date,'','',1)."'";
			$issue_date_cond = "and a.selling_date between '".change_date_format($year_start_date,'','',1)."' and '".change_date_format($year_end_date,'','',1)."'";
		}
	}




	$company_cond = " and a.company_id in($cbo_company_id)";

	if($cbo_item_category != '') {
		$item_cond_rcv = " and a.item_category_id in($cbo_item_category)";
		$item_cond_issue = " and a.item_category in($cbo_item_category)";
	}

	if($cbo_mat_placement != '') {
		$item_cond_rcv = " and b.material_placement in($cbo_mat_placement)";
		// $item_cond_issue = " and a.item_category in($cbo_item_category)";
	}

	if($cbo_receive_basis != 0) {
		$receive_basis_cond = " and a.receive_basis=$cbo_receive_basis";
	}
	if($cbo_store_name != 0) {
		$store_cond = " and a.store_id in($cbo_store_name)";
	}
	if($txt_product_id_des != '') {
		$product_id_cond = " and b.product_id in($txt_product_id_des)";
		$prod_id_cond = " and b.prod_id in($txt_product_id_des)";
	}

	if($txt_trans_ref_no != '') {
		$trans_ref_no_cond = " and b.trans_ref like '%".trim($txt_trans_ref_no)."%'";
	}

	/*$sql_rcv = "select a.item_category_id, b.uom, b.receive_qnty, a.receive_date, d.sales_qty, c.selling_date, c.purpose
		from inv_scrap_receive_mst a, inv_scrap_receive_dtls b, inv_scrap_sales_mst c, inv_scrap_sales_dtls d
		where a.id=b.mst_id and c.id=d.mst_id and b.product_id = d.prod_id $receive_date and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1";*/

	$sql_rcv = "select a.id as scrap_receive_mst_id, a.item_category_id, b.uom, b.color, b.receive_qnty, a.receive_date, b.product_id, a.sys_receive_no, a.store_id, b.material_placement, b.buyer_id, b.supplier_id, b.trans_ref
  				from inv_scrap_receive_mst a, inv_scrap_receive_dtls b
 				where a.id = b.mst_id $company_cond $item_cond_rcv $receive_basis_cond $receive_date_cond $store_cond $product_id_cond $trans_ref_no_cond and a.status_active = 1 and b.status_active = 1
 				order by a.receive_date desc";

	// echo $sql_rcv;die;
	$rcv_result = sql_select($sql_rcv);

	$rcv_arr = array();
	$buyer_summery_arr = array();
	$material_placement_summery_arr = array();
	$rcv_dtls_arr = array();

	$product_arr = array();
	foreach ($rcv_result as $row) {
		// $receiveDate = $row[csf('receive_date')];
		$receiveDate = strtoupper( change_date_format($row[csf('receive_date')], 'd-M-Y', '-', 1) );

		// for the summery table
		if( isset($rcv_arr[$row[csf('item_category_id')]]) ) {
			$rcv_arr[$row[csf('item_category_id')]]['total_receive_qnty'] += $row[csf('receive_qnty')];
		} else {
			$rcv_arr[$row[csf('item_category_id')]]['total_receive_qnty'] = $row[csf('receive_qnty')];
		}
		$rcv_arr[$row[csf('item_category_id')]]['product_id'] = $row[csf('product_id')];
		$rcv_arr[$row[csf('item_category_id')]]['item_category_id'] = $row[csf('item_category_id')];
		$rcv_arr[$row[csf('item_category_id')]]['uom'] = $row[csf('uom')];
		$rcv_arr[$row[csf('item_category_id')]]['receive_date'] = $receiveDate;

		// for buyer summery table
		if( $row[csf('buyer_id')] == '' ) {
			$buyer_summery_arr[0]['total_receive_qnty'] += $row[csf('receive_qnty')];
		} else {
			$buyer_summery_arr[$row[csf('buyer_id')]]['total_receive_qnty'] += $row[csf('receive_qnty')];
		}

		// for material placement summery table
		if( $row[csf('material_placement')] == '' ) {
			$material_placement_summery_arr[0]['total_receive_qnty'] += $row[csf('receive_qnty')];
		} else {
			$material_placement_summery_arr[$row[csf('material_placement')]]['total_receive_qnty'] += $row[csf('receive_qnty')];
		}

		// if receive date is before selected date
		// $toDay=date();

		if( datediff('d', $toDay, $receiveDate) < 0 ) {
			$rcv_arr[$row[csf('item_category_id')]]['prev_receive_qnty'] += $row[csf('receive_qnty')];
		} else {
			$rcv_arr[$row[csf('item_category_id')]]['daterange_receive_qnty'] += $row[csf('receive_qnty')];
		}

		$product_arr[$row[csf('product_id')]] = $row[csf('product_id')];

		// for the details table
		$rcv_dtls_arr[$row[csf('product_id')]][$row[csf('item_category_id')]][$receiveDate]['product_id'] = $row[csf('product_id')];
		$rcv_dtls_arr[$row[csf('product_id')]][$row[csf('item_category_id')]][$receiveDate]['receive_date'] = $receiveDate;
		$rcv_dtls_arr[$row[csf('product_id')]][$row[csf('item_category_id')]][$receiveDate]['sys_receive_no'] = $row[csf('sys_receive_no')];
		$rcv_dtls_arr[$row[csf('product_id')]][$row[csf('item_category_id')]][$receiveDate]['item_category_id'] = $row[csf('item_category_id')];
		$rcv_dtls_arr[$row[csf('product_id')]][$row[csf('item_category_id')]][$receiveDate]['uom'] = $row[csf('uom')];
		$rcv_dtls_arr[$row[csf('product_id')]][$row[csf('item_category_id')]][$receiveDate]['color'] = $row[csf('color')];
		$rcv_dtls_arr[$row[csf('product_id')]][$row[csf('item_category_id')]][$receiveDate]['sales_rate'] = $row[csf('sales_rate')];
		$rcv_dtls_arr[$row[csf('product_id')]][$row[csf('item_category_id')]][$receiveDate]['store_id'] = $row[csf('store_id')];
		$rcv_dtls_arr[$row[csf('product_id')]][$row[csf('item_category_id')]][$receiveDate]['material_placement'] = $row[csf('material_placement')];
		$rcv_dtls_arr[$row[csf('product_id')]][$row[csf('item_category_id')]][$receiveDate]['buyer_id'] = $row[csf('buyer_id')];
		$rcv_dtls_arr[$row[csf('product_id')]][$row[csf('item_category_id')]][$receiveDate]['supplier_id'] = $row[csf('supplier_id')];
		$rcv_dtls_arr[$row[csf('product_id')]][$row[csf('item_category_id')]][$receiveDate]['scrap_receive_mst_id'] = $row[csf('scrap_receive_mst_id')];
		$rcv_dtls_arr[$row[csf('product_id')]][$row[csf('item_category_id')]][$receiveDate]['trans_ref'] = $row[csf('trans_ref')];

		// if receive date is before selected date
		if( datediff('d', $toDay, $receiveDate) < 0 ) {
			$rcv_dtls_arr[$row[csf('product_id')]][$row[csf('item_category_id')]][$receiveDate]['prev_receive_qnty'] += $row[csf('receive_qnty')];
		} else {
			$rcv_dtls_arr[$row[csf('product_id')]][$row[csf('item_category_id')]][$receiveDate]['daterange_receive_qnty'] += $row[csf('receive_qnty')];
		}
		
		if( isset($rcv_dtls_arr[$row[csf('product_id')]][$row[csf('item_category_id')]][$receiveDate]) ) {
			$rcv_dtls_arr[$row[csf('product_id')]][$row[csf('item_category_id')]][$receiveDate]['total_receive_qnty'] += $row[csf('receive_qnty')];
		} else {
			$rcv_dtls_arr[$row[csf('product_id')]][$row[csf('item_category_id')]][$receiveDate]['total_receive_qnty'] = $row[csf('receive_qnty')];
		}
	}
	// echo '<pre>';print_r($product_arr);die;
	
	unset($rcv_result);



	// ============================== data store to gbl table ==================================
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from=1 and ENTRY_FORM=69");
	oci_commit($con);
	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 69, 1, $product_arr, $empty_arr);//PROD_ID

	$int_ref_sql = "SELECT b.grouping as ref, o.prod_id
	from wo_po_break_down b, order_wise_pro_details o, gbl_temp_engine tmp
	where b.id=o.po_breakdown_id and o.entry_form in (7,37) and o.trans_id <> 0 and tmp.entry_form=69 and tmp.ref_from=1 and tmp.user_id = $user_id and o.prod_id = tmp.ref_val";
	// echo $int_ref_sql;
	$int_ref_result = sql_select($int_ref_sql);
	$int_ref_arr = array();
	foreach ($int_ref_result as $row) 
	{
		$int_ref_arr[$row["PROD_ID"]] = $row["REF"];
	}
	// echo '<pre>';print_r($int_ref_arr);


	// =================================== delete data ========================================
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from=1 and ENTRY_FORM=69");
	oci_commit($con);
	disconnect($con);





    // unset($product_arr);

    /*$sql_issue = "select c.sys_challan_no, d.sales_qty, c.selling_date, c.purpose, d.prod_id, c.item_category, d.sales_amount
  				from inv_scrap_sales_mst c, inv_scrap_sales_dtls d, tmp_poid e
  				where c.id=d.mst_id and e.poid=d.prod_id and c.status_active=1 and d.status_active=1";*/

  	$sql_issue = "select a.id as issue_mst_id, a.sys_challan_no, b.sales_qty, a.selling_date, a.purpose, b.prod_id, a.item_category, b.sales_amount, b.sales_rate
  			from inv_scrap_sales_mst a, inv_scrap_sales_dtls b
  			where a.id=b.mst_id $company_cond $item_cond_issue $issue_date_cond $prod_id_cond $store_cond and a.status_active=1 and b.status_active=1";

	// echo $sql_issue;
	$issue_result = sql_select($sql_issue);
	$issue_arr = array();
	$issue_dtls_arr = array();
	foreach ($issue_result as $row) {
		$issueDate = $row[csf('selling_date')];

		// if( isset($issue_arr[$row[csf('item_category')]]) ) {
		// 	if( $row[csf('purpose')] == 1 ) {
		// 		$issue_arr[$row[csf('item_category')]]['total_sales_qty'] += $row[csf('sales_qty')];
		// 	} else if( $row[csf('purpose')] == 2 ) {
		// 		$issue_arr[$row[csf('item_category')]]['total_disposal_qty'] += $row[csf('sales_qty')];
		// 	}
		// } else {
		// 	if( $row[csf('purpose')] == 1 ) {
		// 		$issue_arr[$row[csf('item_category')]]['total_sales_qty'] = $row[csf('sales_qty')];
		// 	} else if( $row[csf('purpose')] == 2 ) {
		// 		$issue_arr[$row[csf('item_category')]]['total_disposal_qty'] = $row[csf('sales_qty')];
		// 	}
		// }
		$issue_arr[$row[csf('item_category')]]['product_id'] = $row[csf('prod_id')];
		$issue_arr[$row[csf('item_category')]]['item_category'] = $row[csf('item_category')];
		// $issue_arr[$row[csf('item_category')]]['amount'] = $row[csf('sales_amount')];
		$issue_arr[$row[csf('item_category')]]['selling_date'] = $issueDate;

		// if( datediff('d', $toDay, $issueDate) < 0 ) {
		// 	$issue_arr[$row[csf('item_category')]]['prev_issue_qnty'] += $row[csf('sales_qty')];
		// }

		$issue_arr[$row[csf('item_category')]]['total_issue_qnty'] += $row[csf('sales_qty')];

		// for buyer summery table
		// $buyer_summery_arr[$row[csf('buyer_id')]]['sales_qty'] += $row[csf('sales_qty')];

		// for details table
		if( isset($issue_dtls_arr[$row[csf('prod_id')]][$row[csf('item_category')]]) ) {
			if( $row[csf('purpose')] == 1 ) {
				$issue_dtls_arr[$row[csf('prod_id')]][$row[csf('item_category')]]['total_sales_qty'] += $row[csf('sales_qty')];
				$issue_dtls_arr[$row[csf('prod_id')]][$row[csf('item_category')]]['sales_rate'] += $row[csf('sales_rate')];
			} else if( $row[csf('purpose')] == 2 ) {
				$issue_dtls_arr[$row[csf('prod_id')]][$row[csf('item_category')]]['total_disposal_qty'] += $row[csf('sales_qty')];
			}
		} else {
			if( $row[csf('purpose')] == 1 ) {
				$issue_dtls_arr[$row[csf('prod_id')]][$row[csf('item_category')]]['total_sales_qty'] = $row[csf('sales_qty')];
				$issue_dtls_arr[$row[csf('prod_id')]][$row[csf('item_category')]]['sales_rate'] = $row[csf('sales_rate')];
			} else if( $row[csf('purpose')] == 2 ) {
				$issue_dtls_arr[$row[csf('prod_id')]][$row[csf('item_category')]]['total_disposal_qty'] = $row[csf('sales_qty')];
			}
		}		

		if( datediff('d', $toDay, $issueDate) < 0 ) {
			$issue_dtls_arr[$row[csf('prod_id')]][$row[csf('item_category')]]['prev_issue_qnty'] += $row[csf('sales_qty')];
		}

		$issue_dtls_arr[$row[csf('prod_id')]][$row[csf('item_category')]]['issue_mst_id'] = $row[csf('issue_mst_id')];
	}

	foreach ($rcv_dtls_arr as $productId => $productIdArr) {
		foreach ($productIdArr as $itemCategoryId => $itemCategoryArr) {
			foreach ($itemCategoryArr as $key => $value) {
				if( $value['buyer_id'] == '' ) {
					$buyer_summery_arr[0]['total_issue_qnty'] += $issue_dtls_arr[$productId][$itemCategoryId]['total_sales_qty'];
					$buyer_summery_arr[0]['total_issue_qnty'] += $issue_dtls_arr[$productId][$itemCategoryId]['total_disposal_qty'];
				} else {
					$buyer_summery_arr[$value['buyer_id']]['total_issue_qnty'] += $issue_dtls_arr[$productId][$itemCategoryId]['total_sales_qty'];
					$buyer_summery_arr[$value['buyer_id']]['total_issue_qnty'] += $issue_dtls_arr[$productId][$itemCategoryId]['total_disposal_qty'];
				}
				if( $value['material_placement'] == '' ) {
					$material_placement_summery_arr[0]['total_issue_qnty'] += $issue_dtls_arr[$productId][$itemCategoryId]['total_sales_qty'];
					$material_placement_summery_arr[0]['total_issue_qnty'] += $issue_dtls_arr[$productId][$itemCategoryId]['total_disposal_qty'];
				} else {
					$material_placement_summery_arr[$value['material_placement']]['total_issue_qnty'] += $issue_dtls_arr[$productId][$itemCategoryId]['total_sales_qty'];
					$material_placement_summery_arr[$value['material_placement']]['total_issue_qnty'] += $issue_dtls_arr[$productId][$itemCategoryId]['total_disposal_qty'];
				}
				$issue_arr[$row[csf('item_category')]]['total_sales_qty'] += $issue_dtls_arr[$productId][$itemCategoryId]['total_sales_qty'];
				$issue_arr[$row[csf('item_category')]]['total_disposal_qty'] += $issue_dtls_arr[$productId][$itemCategoryId]['total_disposal_qty'];
				$issueDate=$issue_arr[$row[csf('item_category')]]['selling_date'];
				if( datediff('d', $toDay, $issueDate) < 0 ) {
					$issue_arr[$row[csf('item_category')]]['prev_issue_qnty'] += $issue_dtls_arr[$productId][$itemCategoryId]['prev_issue_qnty'];
				}
				$totalSales = $issue_dtls_arr[$productId][$itemCategoryId]['total_sales_qty'];
				$salesRate = $issue_dtls_arr[$productId][$itemCategoryId]['sales_rate'];
				$issue_arr[$row[csf('item_category')]]['amount'] += $totalSales * $salesRate;
			}
		}
	}

	ob_start();
	?>
	<div class="summery-area" style="display: inline-flex;">
	<table class="rpt_table" style="width: 50%;" border="1" cellpadding="2" cellspacing="5" rules="all">
		<thead>
			<tr>
				<th colspan="11" align="center">Catagery Wise Summery</th>
			</tr>
			<tr>
				<th>Category</th>
				<th>UOM</th>
				<th>Pre Receive</th>
				<th>Today Receive</th>
				<th>Total Rcv</th>
				<th>Pre Issue</th>
				<th>Sales</th>
				<th>Disposal</th>
				<th>Total Issue</th>
				<th>Balance</th>
				<th>Sales Values</th>
			</tr>
		</thead>
		<tbody>
			<?php
				foreach ($rcv_arr as $row) {
					$itemCategoryId = $row['item_category_id'];
					$prodId = $row['product_id'];
					$preRcv = $row['prev_receive_qnty'];
					$todayRcv = $row['daterange_receive_qnty'];
					$totalRcv = $preRcv + $todayRcv;
					$preIssue = $issue_arr[$itemCategoryId]['prev_issue_qnty'];
					$sales = $issue_arr[$itemCategoryId]['total_sales_qty'];
					$disposal = $issue_arr[$itemCategoryId]['total_disposal_qty'];
					$totalIssue = ($sales + $disposal);

					$balance = $totalRcv - $totalIssue;
					?>
					<tr>
						<td><?php echo $item_category[$itemCategoryId]; ?></td>
						<td><?php echo $unit_of_measurement[$row['uom']]; ?></td>
						<td><?php echo number_format($preRcv,4); ?></td>
						<td><?php echo number_format($todayRcv,4); ?></td>
						<td><?php echo number_format($totalRcv,4); ?></td>
						<td><?php echo number_format($preIssue,4); ?></td>
						<td><?php echo number_format($sales,4); ?></td>
						<td><?php echo number_format($disposal,4); ?></td>
						<td><?php echo number_format($totalIssue,4); ?></td>
						<td><?php echo number_format($balance,4); ?></td>
						<td><?php echo number_format($issue_arr[$itemCategoryId]['amount'],4); ?></td>
					</tr>
					<?php
				}
			?>
		</tbody>
	</table>

	<table class="rpt_table" style="width: 25%; margin-left: 10px;" border="1" cellpadding="2" cellspacing="5" rules="all">
		<thead>
			<tr>
				<th colspan="4" align="center">Buyer Wise Summery</th>
			</tr>
			<tr>
				<th>Buyer name</th>
				<th>Receive</th>
				<th>Issue</th>
				<th>Balance</th>
			</tr>
		</thead>
		<tbody>
			<?php
				foreach ($buyer_summery_arr as $key => $value) {
					?>
					<tr>
						<td><?php echo $buyer_library[$key]; ?></td>
						<td><?php echo number_format($value['total_receive_qnty'],4); ?></td>
						<td><?php echo number_format($value['total_issue_qnty'],4); ?></td>
						<td><?php echo number_format($value['total_receive_qnty']-$value['total_issue_qnty'],4); ?></td>
					</tr>
					<?php
				}
			?>
		</tbody>
	</table>



	<table class="rpt_table" style="width: 25%; margin-left: 10px;" border="1" cellpadding="2" cellspacing="5" rules="all">
		<thead>
			<tr>
				<th colspan="4" align="center">Meterial Placement Wise Summery</th>
			</tr>
			<tr>
				<th>Meterial Place</th>
				<th>Receive</th>
				<th>Issue</th>
				<th>Balance</th>
			</tr>
		</thead>
		<tbody>
			<?php
				foreach ($material_placement_summery_arr as $key => $value) {
					?>
					<tr>
						<td><?php echo $material_placement_arr[$key]; ?></td>
						<td><?php echo number_format($value['total_receive_qnty'],4); ?></td>
						<td><?php echo number_format($value['total_issue_qnty'],4); ?></td>
						<td><?php echo number_format($value['total_receive_qnty']-$value['total_issue_qnty'],4); ?></td>
					</tr>
					<?php
				}
			?>
		</tbody>
	</table>
	</div>
	
	<div style="margin: 40px auto;" >
	<table class="rpt_table" style="width: 1660px;" border="1" cellpadding="2" cellspacing="5" rules="all">
		<thead>
			<tr>
				<th rowspan="2" style="width: 30px;">SL</th>
				<th colspan="11" style="width: 720px;">Transaction Description</th>
				<th colspan="4" style="width: 210px;">Receive</th>
				<th colspan="4" style="width: 280px;">Issue</th>
				<th rowspan="2" style="width: 70px;">Balance</th>
				<th rowspan="2" style="width: 70px;">Sales Rate</th>
				<th rowspan="2" style="width: 70px;">Sales Values</th>
				<th rowspan="2" style="width: 70px;">Store Name</th>
			</tr>
			<tr>
				<th style="width: 70px;">Prod.ID</th>
				<th style="width: 70px;">Transaction Date</th>
				<th style="width: 70px;">Transaction Number</th>
				<th style="width: 70px;">Transaction Ref</th>
				<th style="width: 70px;">Int. Ref</th>
				<th style="width: 70px;">Item Category</th>
				<th style="width: 90px;">Item Description</th>
				<th style="width: 70px;">Meterial Placement</th>
				<th style="width: 70px;">Buyer</th>
				<th style="width: 70px;">Supplier</th>
				<th style="width: 70px;">UoM</th>
				<th style="width: 70px;">Color</th>
				<th style="width: 70px;">Pre Rec</th>
				<th style="width: 70px;">Today Rec</th>
				<th style="width: 70px;">Total Rec</th>
				<th style="width: 70px;">Pre Issue</th>
				<th style="width: 70px;">Sales Issue</th>
				<th style="width: 70px;">Disposal Issue</th>
				<th style="width: 70px;">Total Issue</th>
			</tr>
		</thead>
	</table>
	<div style="width:1660px; overflow-y: scroll; max-height:250px;" id="scroll_body">
		<table class="rpt_table" style="width: 1660px; overflow-y: scroll; max-height:290px;" border="1" cellpadding="2" cellspacing="5" rules="all" id="report_body">
			<tbody>
				<?php
					$sl=1;
					$preRcvGross += 0;
					$todayRcvGross += 0;
					$totalRcvGross += 0;
					$preIssueGross += 0;
					$totalSalesGross += 0;
					$totalDisposalGross += 0;
					$totalIssueGross += 0;
					$totalBalanceGross += 0;
					$totalSalesValueGross += 0;
					$totalSalesRateGross += 0;

					foreach ($rcv_dtls_arr as $productId => $productIdArr) {
						foreach ($productIdArr as $itemCategoryId => $itemCategoryArr) {
							foreach ($itemCategoryArr as $key => $value) {
								$preRcv = $value['prev_receive_qnty'];
								$todayRcv = $value['daterange_receive_qnty'];
								$totalRcv = $preRcv + $todayRcv;

								$issueId = $issue_dtls_arr[$productId][$itemCategoryId]['issue_mst_id'];
								$preIssue = $issue_dtls_arr[$productId][$itemCategoryId]['prev_issue_qnty'];
								// $totalSales = $issue_dtls_arr[$productId][$itemCategoryId]['total_sales_qty'] - $preIssue;
								$totalSales = $issue_dtls_arr[$productId][$itemCategoryId]['total_sales_qty'];
								$totalDisposal = $issue_dtls_arr[$productId][$itemCategoryId]['total_disposal_qty'];
								$totalIssue = $totalSales + $totalDisposal;

								$balance = $totalRcv - $totalIssue;
								$salesRate = $issue_dtls_arr[$productId][$itemCategoryId]['sales_rate'];
								$salesValue = $totalSales * $salesRate;

								if ($sl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<?php echo $bgcolor; ?>" onClick="change_color('tr_<?php echo $sl; ?>','<?php echo $bgcolor; ?>')" id="tr_<?php echo $sl; ?>">
									<td style="width: 30px;"><?php echo $sl; ?></td>
									<td style="width: 70px;"><?php echo $value['product_id']; ?></td>
									<td style="width: 70px;"><?php echo $value['receive_date']; ?></td>
									<td style="width: 70px;"><?php echo $value['sys_receive_no']; ?></td>
									<td style="width: 70px;"><?php echo $value['trans_ref']; ?></td>
									<td style="width: 70px;"><?php echo $int_ref_arr[$value['product_id']]; ?></td>
									<td style="width: 70px;"><?php echo $item_category[$itemCategoryId]; ?></td>
									<td style="width: 90px;"><?php echo $product_library[$value['product_id']]; ?></td>
									<td style="width: 70px;"><?php echo $material_placement_arr[$value['material_placement']]; ?></td>
									<td style="width: 70px;"><?php echo $buyer_library[$value['buyer_id']]; ?></td>
									<td style="width: 70px;"><?php echo $supplier_library[$value['supplier_id']]; ?></td>
									<td style="width: 70px;"><?php echo $unit_of_measurement[$value['uom']]; ?></td>
									<td style="width: 70px;"><?php echo $color_library[$value['color']]; ?></td>
									<td style="width: 70px;"><?php echo number_format($preRcv,4); ?></td>
									<td style="width: 70px;"><?php echo number_format($todayRcv,4); ?></td>
									<td style="width: 70px;">
										<?php
											if( $itemCategoryId == 1 || $itemCategoryId == 2 || $itemCategoryId == 13 ) {
										?>
										<p><a href="#report_popup" onClick="report_popup(<?php echo $itemCategoryId; ?>, 1, <?php echo $value["scrap_receive_mst_id"]; ?>, <?php echo $value["product_id"]; ?>, 'Receive Details', '750px')"><?php echo number_format($totalRcv,4); ?></a></p>
										<?php
											} else {
										?>
										<p><?php echo number_format($totalRcv,4); ?></p>
										<?php
											}
										?>
									</td>
									<td style="width: 70px;"><?php echo number_format($preIssue,4); ?></td>
									<td style="width: 70px;"><?php echo number_format($totalSales,4); ?></td>
									<td style="width: 70px;"><?php echo number_format($totalDisposal,4); ?></td>
									<td style="width: 70px;">
										<?php
											if( $itemCategoryId == 1 || $itemCategoryId == 2 || $itemCategoryId == 13 ) {
										?>
										<p><a href="#report_popup" onClick="report_popup(<?php echo $itemCategoryId; ?>, 2, <?php echo $issueId; ?>, <?php echo $value["product_id"]; ?>, 'Issue Details', '750px')"><?php echo $totalIssue; ?></a></p>
										<?php
											} else {
										?>
										<p><?php echo number_format($totalIssue,4); ?></p>
										<?php
											}
										?>
									</td>
									<td style="width: 70px;"><?php echo number_format($balance,4); ?></td>
									<td style="width: 70px;"><?php echo number_format($salesRate,4); ?></td>
									<td style="width: 70px;"><?php echo number_format($salesValue,4); ?></td>
									<td style="width: 70px;"><?php echo $store_library[$value['store_id']]; ?></td>
								</tr>
							<?php
								$sl++;
								$preRcvGross += $preRcv;
								$todayRcvGross += $todayRcv;
								$totalRcvGross += $totalRcv;
								$preIssueGross += $preIssue;
								$totalSalesGross += $totalSales;
								$totalDisposalGross += $totalDisposal;
								$totalIssueGross += $totalIssue;
								$totalBalanceGross += $balance;
								$totalSalesValueGross += $salesValue;
								$totalSalesRateGross += $salesRate;
							}
						}
					}
				?>
			</tbody>
		</table>
	</div>
	<table cellspacing="0" border="1" class="rpt_table" width="1660px" rules="all">
		<tfoot>
			<tr>
				<!-- <th style="width: 906px">Total</th> -->
				<th style="width: 30px">&nbsp;</th>
				<th style="width: 70px">&nbsp;</th>
				<th style="width: 70px">&nbsp;</th>
				<th style="width: 70px">&nbsp;</th>
				<th style="width: 70px">&nbsp;</th>
				<th style="width: 70px">&nbsp;</th>
				<th style="width: 70px">&nbsp;</th>
				<th style="width: 90px">&nbsp;</th>
				<th style="width: 70px">&nbsp;</th>
				<th style="width: 70px">&nbsp;</th>
				<th style="width: 70px">&nbsp;</th>
				<th style="width: 70px">&nbsp;</th>
				<th style="width: 70px">Total</th>
				<th style="width: 70px" id="preRcvGross"><?php echo number_format($preRcvGross,4); ?></th>
				<th style="width: 70px" id="todayRcvGross"><?php echo number_format($todayRcvGross,4); ?></th>
				<th style="width: 70px" id="totalRcvGross"><?php echo number_format($totalRcvGross,4); ?></th>
				<th style="width: 70px" id="preIssueGross"><?php echo number_format($preIssueGross,4); ?></th>
				<th style="width: 70px" id="totalSalesGross"><?php echo number_format($totalSalesGross,4); ?></th>
				<th style="width: 70px" id="totalDisposalGross"><?php echo number_format($totalDisposalGross,4); ?></th>
				<th style="width: 70px" id="totalIssueGross"><?php echo number_format($totalIssueGross,4); ?></th>
				<th style="width: 70px" id="totalBalanceGross"><?php echo number_format($totalBalanceGross,4); ?></th>
				<th style="width: 70px"><?php echo number_format(($totalSalesRateGross / ($sl-1)), 2); ?></th>
				<th style="width: 70px" id="totalSalesValueGross"><?php echo number_format($totalSalesValueGross,4); ?></th>
				<th style="width: 70px"></th>
			</tr>  
		</tfoot>
	</table>
	</div>
	<?php
	/*$r_id3=execute_query("delete from tmp_poid where userid=$user_id and type in($tmpType)");
    if($db_type==0) {
        if($r_id3) {
            mysql_query("COMMIT");  
        }
    }
    if($db_type==2 || $db_type==1 ) {
        if($r_id3) {
            oci_commit($con);  
        }
    }*/

	/*disconnect($con);
    die;*/    $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    	@unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$rpt_type"; 
	// echo "$html**$rpt_type";
	exit();
}

if($action == 'generate_report_3')
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$tmpType = 985;
	$material_placement_arr = array(1=>'Top Floor', 2=>'Bulding Side', 3=>'Old Store', 4=>'Tin Shade');

	$product_library = return_library_array("select id, product_name_details from product_details_master where status_active=1 and is_deleted=0", 'id', 'product_name_details');
	$company_array = return_library_array("select id,company_name from lib_company where is_deleted=0","id","company_name");
	$store_library = return_library_array("select a.id, a.store_name from lib_store_location a where a.status_active=1 and a.is_deleted=0", 'id', 'store_name');
	$buyer_library = return_library_array("select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($cbo_company_id) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", 'id', 'buyer_name');
	$supplier_library = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	if($from_date !='' && $to_date !=''){
		if($db_type==0) {
			$receive_date_cond = "and a.receive_date between '".change_date_format($from_date, "yyyy-mm-dd", "-")."' and '".change_date_format($to_date, "yyyy-mm-dd", "-")."'";
			$issue_date_cond =  "and a.selling_date between '".change_date_format($from_date, "yyyy-mm-dd", "-")."' and '".change_date_format($to_date, "yyyy-mm-dd", "-")."'";
		}
		if($db_type==2) {
			$receive_date_cond = "and a.receive_date between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";
			$issue_date_cond = "and a.selling_date between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";
		}
	}

	$company_cond = " and a.company_id in($cbo_company_id)";

	if($cbo_item_category != '') {
		$item_cond_rcv = " and a.item_category_id in($cbo_item_category)";
		$item_cond_issue = " and a.item_category in($cbo_item_category)";
	}

	if($cbo_mat_placement !=0 ) {
		$item_cond_rcv = " and b.material_placement in($cbo_mat_placement)";
		// $item_cond_issue = " and a.item_category in($cbo_item_category)";
	}

	if($cbo_receive_basis != 0) {
		$receive_basis_cond = " and a.receive_basis=$cbo_receive_basis";
	}
	if($cbo_store_name != '') {
		$store_cond = " and a.store_id in($cbo_store_name)";
	}

	if($txt_product_id_des != '') {
		$product_id_cond = " and b.product_id in($txt_product_id_des)";
		$prod_id_cond = " and b.prod_id in($txt_product_id_des)";
	}

	if($txt_trans_ref_no != '') {
		$trans_ref_no_cond = " and b.trans_ref like '%".trim($txt_trans_ref_no)."%'";
	}
	$sql_rcv = "select  a.company_id as COMPANY_ID, a.item_category_id as ITEM_CATEGORY_ID, b.uom as UOM, b.receive_qnty as RECEIVE_QNTY, a.receive_date as RECEIVE_DATE, b.product_id as PRODUCT_ID, a.store_id as STORE_ID, b.material_placement as MATERIAL_PLACEMENT, b.buyer_id as BUYER_ID, b.supplier_id as SUPPLIER_ID
	from inv_scrap_receive_mst a, inv_scrap_receive_dtls b
   where a.id = b.mst_id $company_cond $item_cond_rcv $receive_basis_cond $store_cond $product_id_cond $trans_ref_no_cond and a.status_active = 1 and b.status_active = 1
   order by b.product_id desc";
	// echo $sql_rcv;
   $rcv_result = sql_select($sql_rcv);
   $rcv_arr = array();
   $rcv_dtls_arr = array();
   $store_id_arr = '';
   foreach ($rcv_result as $row) {
	$receiveDate = change_date_format($row['RECEIVE_DATE']);
	$rcv_arr[$row['COMPANY_ID']."_".$row['ITEM_CATEGORY_ID']]['item_category_id'] = $row['ITEM_CATEGORY_ID'];
	$rcv_arr[$row['COMPANY_ID']."_".$row['ITEM_CATEGORY_ID']]['uom'] = $row['UOM'];
	$rcv_arr[$row['COMPANY_ID']."_".$row['ITEM_CATEGORY_ID']]['company_id'] = $row['COMPANY_ID'];
	$rcv_dtls_arr[$row['COMPANY_ID']."_".$row['ITEM_CATEGORY_ID']."_".$row['PRODUCT_ID']]['item_category_id'] = $row['ITEM_CATEGORY_ID'];
	$rcv_dtls_arr[$row['COMPANY_ID']."_".$row['ITEM_CATEGORY_ID']."_".$row['PRODUCT_ID']]['company_id'] = $row['COMPANY_ID'];
	$rcv_dtls_arr[$row['COMPANY_ID']."_".$row['ITEM_CATEGORY_ID']."_".$row['PRODUCT_ID']]['product_id'] = $row['PRODUCT_ID'];
	$rcv_dtls_arr[$row['COMPANY_ID']."_".$row['ITEM_CATEGORY_ID']."_".$row['PRODUCT_ID']]['uom'] = $row['UOM'];
	$rcv_dtls_arr[$row['COMPANY_ID']."_".$row['ITEM_CATEGORY_ID']."_".$row['PRODUCT_ID']]['material_placement'] .= $row['MATERIAL_PLACEMENT'].",";
	$rcv_dtls_arr[$row['COMPANY_ID']."_".$row['ITEM_CATEGORY_ID']."_".$row['PRODUCT_ID']]['buyer_id'] .= $row['BUYER_ID'].",";
	$rcv_dtls_arr[$row['COMPANY_ID']."_".$row['ITEM_CATEGORY_ID']."_".$row['PRODUCT_ID']]['supplier_id'] .= $row['SUPPLIER_ID'].",";
	$store_id_arr .=$row['STORE_ID'].',';
	if($from_date!='' && $to_date!=''){
		//echo "l__". datediff('d', strtotime($from_date), strtotime($receiveDate));
	   	$diff1 =   round(abs(strtotime($from_date) - strtotime($receiveDate))/86400);
		$diff2 =   round(abs(strtotime($to_date) - strtotime($receiveDate))/86400);

		//if(datediff('d', $from_date, $receiveDate) > 0 && datediff('d', $to_date, $receiveDate) < 0 ) {
		if($diff1 > 0 && $diff2 < 0 ) {
			$rcv_arr[$row['COMPANY_ID']."_".$row['ITEM_CATEGORY_ID']]['rcv_qnty'] += $row['RECEIVE_QNTY'];
			$rcv_dtls_arr[$row['COMPANY_ID']."_".$row['ITEM_CATEGORY_ID']."_".$row['PRODUCT_ID']]['rcv_qnty'] += $row['RECEIVE_QNTY'];
			$rcv_dtls_arr[$row['COMPANY_ID']."_".$row['ITEM_CATEGORY_ID']."_".$row['PRODUCT_ID']][$row['STORE_ID']]['store_qnty'] += $row['RECEIVE_QNTY'];
		} 
		//else if( datediff('d', $from_date, $receiveDate) < 1 ) {
		else if( $diff1 < 1 ) {
			$rcv_arr[$row['COMPANY_ID']."_".$row['ITEM_CATEGORY_ID']]['pre_rcv_qnty'] += $row['RECEIVE_QNTY'];
			$rcv_dtls_arr[$row['COMPANY_ID']."_".$row['ITEM_CATEGORY_ID']."_".$row['PRODUCT_ID']]['pre_rcv_qnty'] += $row['RECEIVE_QNTY'];
			$rcv_dtls_arr[$row['COMPANY_ID']."_".$row['ITEM_CATEGORY_ID']."_".$row['PRODUCT_ID']][$row['STORE_ID']]['store_qnty'] += $row['RECEIVE_QNTY'];
		}
	}else{
		$rcv_arr[$row['COMPANY_ID']."_".$row['ITEM_CATEGORY_ID']]['rcv_qnty'] += $row['RECEIVE_QNTY'];
		$rcv_dtls_arr[$row['COMPANY_ID']."_".$row['ITEM_CATEGORY_ID']."_".$row['PRODUCT_ID']]['rcv_qnty'] += $row['RECEIVE_QNTY'];
		$rcv_dtls_arr[$row['COMPANY_ID']."_".$row['ITEM_CATEGORY_ID']."_".$row['PRODUCT_ID']][$row['STORE_ID']]['store_qnty'] += $row['RECEIVE_QNTY'];
	}
	
   }

   $sql_issue = "select a.company_id as COMPANY_ID, b.sales_qty as SALES_QTY, a.selling_date as SELLING_DATE, a.store_id as STORE_ID, b.prod_id as PROD_ID, a.item_category as ITEM_CATEGORY, b.sales_amount as SALES_AMOUNT, b.sales_rate as SALES_RATE,b.id as ISSUE_DTLS_ID
   from inv_scrap_sales_mst a, inv_scrap_sales_dtls b
   where a.id=b.mst_id  $company_cond $item_cond_issue $prod_id_cond $store_cond and a.status_active=1 and b.status_active=1";
	// echo $sql_issue;
   $issue_result = sql_select($sql_issue);
   $issue_dtls_arr = array();
   foreach ($issue_result as $row) {
	$issueDate = change_date_format($row['SELLING_DATE']);

	if($from_date!='' && $to_date!=''){
		if( datediff('d', $from_date, $issueDate) > 0 && datediff('d', $to_date, $issueDate) < 0 ) {
			$issue_dtls_arr[$row['COMPANY_ID']."_".$row['ITEM_CATEGORY']."_".$row['PROD_ID']]['issue_dtls_id'] .= $row['ISSUE_DTLS_ID'].",";
			$issue_dtls_arr[$row['COMPANY_ID']."_".$row['ITEM_CATEGORY']."_".$row['PROD_ID']]['sales_rate'] += $row['SALES_RATE'];
			$issue_dtls_arr[$row['COMPANY_ID']."_".$row['ITEM_CATEGORY']."_".$row['PROD_ID']]['sales_amount'] += $row['SALES_AMOUNT'];
			$issue_dtls_arr[$row['COMPANY_ID']."_".$row['ITEM_CATEGORY']."_".$row['PROD_ID']]['issue_qnty'] += $row['SALES_QTY'];
			$issue_dtls_arr[$row['COMPANY_ID']."_".$row['ITEM_CATEGORY']."_".$row['PROD_ID']][$row['STORE_ID']]['store_qnty'] += $row['SALES_QTY'];
		} else if( datediff('d', $from_date, $issueDate) < 1 ) {
			$issue_dtls_arr[$row['COMPANY_ID']."_".$row['ITEM_CATEGORY']."_".$row['PROD_ID']]['issue_dtls_id'] .= $row['ISSUE_DTLS_ID'].",";
			$issue_dtls_arr[$row['COMPANY_ID']."_".$row['ITEM_CATEGORY']."_".$row['PROD_ID']]['sales_rate'] += $row['SALES_RATE'];
			$issue_dtls_arr[$row['COMPANY_ID']."_".$row['ITEM_CATEGORY']."_".$row['PROD_ID']]['sales_amount'] += $row['SALES_AMOUNT'];
			$issue_dtls_arr[$row['COMPANY_ID']."_".$row['ITEM_CATEGORY']."_".$row['PROD_ID']]['pre_issue_qnty'] += $row['SALES_QTY'];
			$issue_dtls_arr[$row['COMPANY_ID']."_".$row['ITEM_CATEGORY']."_".$row['PROD_ID']][$row['STORE_ID']]['store_qnty'] += $row['SALES_QTY'];
		}
	}else{
		$issue_dtls_arr[$row['COMPANY_ID']."_".$row['ITEM_CATEGORY']."_".$row['PROD_ID']]['issue_dtls_id'] .= $row['ISSUE_DTLS_ID'].",";
		$issue_dtls_arr[$row['COMPANY_ID']."_".$row['ITEM_CATEGORY']."_".$row['PROD_ID']]['sales_rate'] += $row['SALES_RATE'];
		$issue_dtls_arr[$row['COMPANY_ID']."_".$row['ITEM_CATEGORY']."_".$row['PROD_ID']]['sales_amount'] += $row['SALES_AMOUNT'];
		$issue_dtls_arr[$row['COMPANY_ID']."_".$row['ITEM_CATEGORY']."_".$row['PROD_ID']]['issue_qnty'] += $row['SALES_QTY'];
		$issue_dtls_arr[$row['COMPANY_ID']."_".$row['ITEM_CATEGORY']."_".$row['PROD_ID']][$row['STORE_ID']]['store_qnty'] += $row['SALES_QTY'];
	}
	
   }
   $store_id_arr=implode(",",array_unique(explode(",",chop($store_id_arr,","))));
   $store_nam_arr=explode(",",$store_id_arr);
   $store_id_count=count($store_nam_arr);

   $tbl_width=1550+($store_id_count*70)."px";
   ob_start();
   ?>

	<div style="margin: 40px auto;" >
		<table class="rpt_table" style="width: <?=$tbl_width;?>;" border="1" cellpadding="2" cellspacing="5" rules="all">
			<thead>
				<tr>
					<th rowspan="2" style="width: 30px;">SL</th>
					<th rowspan="2" style="width: 100px;">Company</th>
					<th colspan="7" style="width: 510px; word-break: break-all;">Transaction Description</th>
					<th colspan="3" style="width: 210px;">Receive</th>
					<th colspan="3" style="width: 210px;">Issue</th>
					<th rowspan="2" style="width: 70px;">Balance</th>
					<th rowspan="2" style="width: 70px;">Avg. Sales Rate</th>
					<th rowspan="2" style="width: 70px;">Sales Values</th>
					<th colspan="<? echo $store_id_count;?>">Store Name</th>
				</tr>
				<tr>
					<th style="width: 70px;">Prod.ID</th>
					<th style="width: 70px;">Item Category</th>
					<th style="width: 90px;">Item Description</th>
					<th style="width: 70px;">Meterial Placement</th>
					<th style="width: 70px;">Buyer</th>
					<th style="width: 70px;">Supplier</th>
					<th style="width: 70px;">UoM</th>
					<th style="width: 70px;">Pre Rec</th>
					<th style="width: 70px;">Current Rec</th>
					<th style="width: 70px;">Total Rec</th>
					<th style="width: 70px;">Pre Issue</th>
					<th style="width: 70px;">Current Issue</th>
					<th style="width: 70px;">Total Issue</th>
					<?
						foreach($store_nam_arr as $key=>$value){
							?>
							 <th style="width: 70px;"><?= $store_library[$value];?></th>
							<?
						}
					?>
					
				</tr>
			</thead>
		</table>
		<div style="width: <?=$tbl_width;?>; overflow-y: scroll; max-height:250px;" id="scroll_body">
			<table class="rpt_table" style="width:  <?=$tbl_width;?>; overflow-y: scroll; max-height:290px;" border="1" cellpadding="2" cellspacing="5" rules="all" id="report_body">
				<tbody>
					<?php
						$sl=1;
						$total_store=array();
						foreach ($rcv_dtls_arr as $value) {
							// echo "<pre>";
							// print_r($value);
							if ($sl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$material_placement=array_unique(explode(",",$value['material_placement']));
								$material_arr='';
								foreach($material_placement as $rows=>$row){
									if($material_arr !=''){
										$material_arr .= ", ".$material_placement_arr[$row];
									}else{
										$material_arr= $material_placement_arr[$row];
									}
								}
								$buyer_id_arr=array_unique(explode(",",$value['buyer_id']));
								$buyer_name='';
								foreach($buyer_id_arr as $rows=>$row){
									if($buyer_name !=''){
										$buyer_name .= ", ".$buyer_library[$row];
									}else{
										$buyer_name= $buyer_library[$row];
									}
								}
								$supplier_id_arr=array_unique(explode(",",$value['supplier_id']));
								$supplier_name='';
								foreach($supplier_id_arr as $rows=>$row){
									if($supplier_name !=''){
										$supplier_name .= ", ".$supplier_library[$row];
									}else{
										$supplier_name= $supplier_library[$row];
									}
								}
								$totalRCV=$value['pre_rcv_qnty']+$value['rcv_qnty'];
								$preIssue=$issue_dtls_arr[$value['company_id'].'_'.$value['item_category_id']."_".$value['product_id']]['pre_issue_qnty'];
								$currentIssue=$issue_dtls_arr[$value['company_id'].'_'.$value['item_category_id']."_".$value['product_id']]['issue_qnty'];
								$totalIssue=$preIssue+$currentIssue;
								$balance=$totalRCV-$totalIssue;
								$salesAmountIssue=$issue_dtls_arr[$value['company_id'].'_'.$value['item_category_id']."_".$value['product_id']]['sales_amount'];
								$salesRateIssue=$issue_dtls_arr[$value['company_id'].'_'.$value['item_category_id']."_".$value['product_id']]['sales_rate'];
								$salesDtlsID=$issue_dtls_arr[$value['company_id'].'_'.$value['item_category_id']."_".$value['product_id']]['issue_dtls_id'];
								$salesDtlsID_arr=implode(",",array_unique(explode(",",chop($salesDtlsID,","))));
   								$salesDtlsID_count=count(explode(",",$salesDtlsID_arr));
								$issue_arr[$value['company_id'].'_'.$value['item_category_id']]['sales_amount'] += $salesAmountIssue;
								$issue_arr[$value['company_id'].'_'.$value['item_category_id']]['issue_qnty'] += $currentIssue;;
								$issue_arr[$value['company_id'].'_'.$value['item_category_id']]['pre_issue_qnty'] += $preIssue
							?>
							<tr bgcolor="<?php echo $bgcolor; ?>" onClick="change_color('tr_<?php echo $sl; ?>','<?php echo $bgcolor; ?>')" id="tr_<?php echo $sl; ?>">
								<td style="width: 30px;"><?php echo $sl; ?></td>
								<td style="width: 100px;" style="word-break: break-all;"><?php echo $company_array[$value['company_id']]; ?></td>
								<td style="width: 70px;"><?php echo $value['product_id']; ?></td>
								<td style="width: 70px;"><?php echo $item_category[$value['item_category_id']]; ?></td>
								<td style="width: 90px;"><?php echo $product_library[$value['product_id']]; ?></td>
								<td style="width: 70px;"><?php echo $material_arr; ?></td>
								<td style="width: 70px;"><?php echo $buyer_name; ?></td>
								<td style="width: 70px;"><?php echo $supplier_name; ?></td>
								<td style="width: 70px;"><?php echo $unit_of_measurement[$value['uom']]; ?></td>
								<td style="width: 70px;"><?php echo number_format($value['pre_rcv_qnty'],4); ?></td>
								<td style="width: 70px;"><?php echo number_format($value['rcv_qnty'],4); ?></td>
								<td style="width: 70px;">
									<p><a href="#report_popup" onClick="total_report_popup(<?php echo $value['item_category_id']; ?>, 1, <?php echo $value["product_id"]; ?>, 'Receive Details', '750px')"><?php echo number_format($totalRCV,4); ?></a></p>
								</td>
								<td style="width: 70px;"><?php echo $preIssue; ?></td>
								<td style="width: 70px;"><?php echo number_format($currentIssue,4); ?></td>
								<td style="width: 70px;">
									<p><a href="#report_popup" onClick="total_report_popup(<?php echo $value['item_category_id']; ?>, 2, <?php echo $value["product_id"]; ?>, 'Issue Details', '750px')"><?php echo $totalIssue; ?></a></p>
								</td>
								<td style="width: 70px;"><?php echo number_format($balance,4); ?></td>
								<td style="width: 70px;"><?php 
									$avgRate=$salesRateIssue/$salesDtlsID_count;
									echo number_format($avgRate,3); ?></td>
								<td style="width: 70px;"><?php echo number_format($salesAmountIssue,3); ?></td>
								<?
									foreach($store_nam_arr as $key=>$row){
										$store_rcv=$value[$row]['store_qnty'];
										$store_issue=$issue_dtls_arr[$value['company_id'].'_'.$value['item_category_id']."_".$value['product_id']][$row]['store_qnty'];
										$store_blance=$store_rcv-$store_issue;
										$total_store[$row] += $store_blance;
										?>
										<td style="width: 70px;"><? echo number_format($store_blance,4);?></td>
										<?
									}
								?>
							</tr>
							<?php
							$sl++;
							$totalPreRcv+=$value['pre_rcv_qnty'];
							$totalCurrentRcv+=$value['rcv_qnty'];
							$GrandTotalRcv+=$totalRCV;
							$totalPreIssue+=$preIssue;
							$totalCurrentIssue+=$currentIssue;
							$GrandTotalIssue+=$totalIssue;
							$totalBalance+=$balance;
							$totalAvgRate+=$salesRateIssue/$salesDtlsID_count;
							$totalSalesAmount+=$salesAmountIssue;
						}
					?>
				</tbody>
			</table>
		</div>
		<table cellspacing="0" border="1" class="rpt_table" width=" <?=$tbl_width;?>" rules="all">
			<tfoot>
				<tr>
					<th style="width: 30px">&nbsp;</th>
					<th style="width: 100px">&nbsp;</th>
					<th style="width: 70px">&nbsp;</th>
					<th style="width: 70px">&nbsp;</th>
					<th style="width: 90px">&nbsp;</th>
					<th style="width: 70px">&nbsp;</th>
					<th style="width: 70px">&nbsp;</th>
					<th style="width: 70px">&nbsp;</th>
					<th style="width: 70px">Total</th>
					<th style="width: 70px" id="totalPreRcv" ><?php echo number_format($totalPreRcv,4); ?></th>
					<th style="width: 70px" id="totalCurrentRcv" ><?php echo number_format($totalCurrentRcv,4); ?></th>
					<th style="width: 70px" id="GrandTotalRcv" ><?php echo number_format($GrandTotalRcv,4); ?></th>
					<th style="width: 70px" id="totalPreIssue" ><?php echo number_format($totalPreIssue,4); ?></th>
					<th style="width: 70px" id="totalCurrentIssue" ><?php echo number_format($totalCurrentIssue,4); ?></th>
					<th style="width: 70px" id="GrandTotalIssue" ><?php echo number_format($GrandTotalIssue,4); ?></th>
					<th style="width: 70px" id="totalBalance" ><?php echo number_format($totalBalance,4); ?></th>
					<th style="width: 70px" id="totalAvgRate" ><?php echo number_format($totalAvgRate,3); ?></th>
					<th style="width: 70px" id="totalSalesAmount" ><?php echo number_format($totalSalesAmount,3); ?></th>
					<?
						foreach($store_nam_arr as $key=>$value){
							?>
							 <th style="width: 70px;"><?php echo number_format($total_store[$value],4); ?></th>
							<?
						}
					?>
				</tr>  
			</tfoot>
		</table>
	</div>
     <br>
	<div class="summery-area" style="display: inline-flex;">
		<table class="rpt_table" style="width: 100%;" border="1" cellpadding="2" cellspacing="5" rules="all" align="center">
			<thead>
				<tr>
					<th colspan="11" align="center">Catagery Wise Summery</th>
				</tr>
				<tr>
					<th style="width: 100px;">Company</th>
					<th style="width: 70px;">Category</th>
					<th style="width: 70px;">UOM</th>
					<th style="width: 70px;">Pre Receive</th>
					<th style="width: 70px;">Today Receive</th>
					<th style="width: 70px;">Total Rcv</th>
					<th style="width: 70px;">Pre Issue</th>
					<th style="width: 70px;">Sales</th>
					<th style="width: 70px;">Total Issue</th>
					<th style="width: 70px;">Balance</th>
					<th>Sales Values</th>
				</tr>
			</thead>
			<tbody>
				<?php
					foreach ($rcv_arr as $row) {
						$itemCategoryId = $row['item_category_id'];
						$preRcv = $row['pre_rcv_qnty'];
						$currentRcv = $row['rcv_qnty'];
						$totalRcv = $preRcv + $currentRcv;
						$preIssue = $issue_arr[$row['company_id'].'_'.$itemCategoryId]['pre_issue_qnty'];
						$currentIssue = $issue_arr[$row['company_id'].'_'.$itemCategoryId]['issue_qnty'];
						$totalIssue = ($preIssue + $currentIssue);
						$balance = $totalRcv - $totalIssue;
						$salesValue = $issue_arr[$row['company_id'].'_'.$itemCategoryId]['sales_amount'];
						?>
						<tr>
							<td style="word-break: break-all;"><?php echo $company_array[$row['company_id']]; ?></td>
							<td><?php echo $item_category[$itemCategoryId]; ?></td>
							<td><?php echo $unit_of_measurement[$row['uom']]; ?></td>
							<td><?php echo number_format($preRcv,4); ?></td>
							<td><?php echo number_format($currentRcv,4); ?></td>
							<td><?php echo number_format($totalRcv,4); ?></td>
							<td><?php echo number_format($preIssue,4); ?></td>
							<td><?php echo number_format($currentIssue,4); ?></td>
							<td><?php echo number_format($totalIssue,4); ?></td>
							<td><?php echo number_format($balance,4); ?></td>
							<td><?php echo number_format($salesValue,3); ?></td>
						</tr>
						<?php
					}
				?>
			</tbody>
		</table>
	</div>
	
   <?
//    echo $rpt_type."_____";
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    	@unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$rpt_type"; 
	// echo "$html**$rpt_type";
	exit();
}

if($action=='total_receive_popup')
{
	echo load_html_head_contents('Report Info', '../../../../', 1, 1, $unicode, '', '');
	extract($_REQUEST);

	$sql_rcv = "select a.sys_receive_no, a.receive_date, a.item_category_id, b.product_id, b.uom, b.receive_qnty, b.trans_ref
  				from inv_scrap_receive_mst a, inv_scrap_receive_dtls b
 				where a.id = b.mst_id and a.item_category_id = $garmentsItemId and b.product_id=$product_id and a.status_active = 1 and b.status_active = 1
 				order by a.receive_date desc";

 	$product_library = return_library_array("select id, product_name_details from product_details_master where status_active=1 and is_deleted=0", 'id', 'product_name_details');

 	$sql_rcv_res = sql_select($sql_rcv);
	?>
    <div>
        <table width="99%" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
            <caption><strong>Total Receive Details</strong></caption>
			<thead>
			    <tr>
			        <th width="100">Receive Date</th>
			        <th width="120">Transaction Number</th>
			        <th width="120">Transaction Ref</th>
			        <th width="100">Category</th>
			        <th width="120">Item Description</th>
			        <th width="80">Qty</th>
			        <th>UoM</th>
			    </tr>
            </thead>
	        <tbody id="table_body_popup">
                <?php
				//echo $sql_scrap;die;		
	            $sql_scrap_res=sql_select($sql_scrap);
				$k=1;
				$tot_rcv_qty=0;
				foreach($sql_rcv_res as $row) {
					if($k%2==0)$bgcolor="#E9F3FF";  
					else $bgcolor="#FFFFFF";
                ?>
		        <tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $stylecolor; ?> onclick="change_color('tr_<?php echo $k; ?>','<?php echo $bgcolor; ?>')" id="tr_<?php echo $k; ?>">
		            <td width="100" align="center"><p><?php echo change_date_format($row[csf('receive_date')]); ?></p></td>
					<td width="120" align="center"><p><?php echo $row[csf('sys_receive_no')]; ?></p></td>
					<td width="120" align="center"><p><?php echo $row[csf('trans_ref')]; ?></p></td>
		            <td width="100" align="center"><p><?php echo $item_category[$row[csf('item_category_id')]]; ?></p></td> 
		            <td width="120" align="center"><p><?php echo $product_library[$row[csf('product_id')]]; ?></p></td> 
		            <td width="80" align="right"><p><?php echo $row[csf('receive_qnty')]; ?></p></td> 
		            <td align="center"><p><?php echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
		        </tr>
		        <?php
					$k++;
					$tot_rcv_qty   += $row[csf('receive_qnty')];
				}
				?>
	            <tfoot>
	                <tr>
	                    <th colspan="5">Total&nbsp;</th>
	                    <th align="right"><?php echo number_format($tot_rcv_qty,2); ?></th>
	                    <th>&nbsp;</th>
	                </tr>
	            </tfoot>
		    </tbody>
        </table>
        <script>setFilterGrid("table_body_popup",-1);</script>
    </div>
    <?php
}

if($action=='total_issue_popup')
{
	echo load_html_head_contents('Report Info', '../../../../', 1, 1, $unicode, '', '');
	extract($_REQUEST);

	$sql_issue = "select a.sys_challan_no, b.sales_qty, a.selling_date, b.prod_id, a.item_category,b.sales_rate, b.sales_uom, b.rej_uom
  		from inv_scrap_sales_mst a, inv_scrap_sales_dtls b
  		where a.id=b.mst_id and a.item_category=$garmentsItemId and b.prod_id=$product_id and a.status_active=1 and b.status_active=1";
  	// echo $sql_issue;
 	$product_library = return_library_array("select id, product_name_details from product_details_master where status_active=1 and is_deleted=0", 'id', 'product_name_details');

 	$sql_issue_res = sql_select($sql_issue);
	?>
    <div>
        <table width="99%" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
            <caption><strong>Total Issue Details</strong></caption>
			<thead>
			    <tr>
			        <th width="100">Issue ID</th>
			        <th width="120">Issue Date</th>
			        <th width="100">Category</th>
			        <th width="150">Item Description</th>
			        <th width="80">Qty</th>
			        <th width="80">Rate</th>
			        <th>UoM</th>
			    </tr>
            </thead>
	        <tbody id="table_body_popup">
                <?php
				//echo $sql_scrap;die;		
	            $sql_scrap_res=sql_select($sql_scrap);
				$k=1;
				$tot_issue_qty=0;
				
				foreach($sql_issue_res as $row) {
					if($k%2==0)$bgcolor="#E9F3FF";
					else $bgcolor="#FFFFFF";
					$uom = $row[csf('sales_uom')] ? $row[csf('sales_uom')] : $row[csf('rej_uom')];
                ?>
		        <tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $stylecolor; ?> onclick="change_color('tr_<?php echo $k; ?>','<?php echo $bgcolor; ?>')" id="tr_<?php echo $k; ?>">
		            <td width="100" align="center"><p><?php echo $row[csf('sys_challan_no')]; ?></p></td>
		            <td width="120" align="center"><p><?php echo change_date_format($row[csf('selling_date')]); ?></p></td>
		            <td width="100" align="center"><p><?php echo $item_category[$row[csf('item_category')]]; ?></p></td> 
		            <td width="150" align="center"><p><?php echo $product_library[$row[csf('prod_id')]]; ?></p></td> 
		            <td width="80" align="right"><p><?php echo $row[csf('sales_qty')]; ?></p></td> 
		            <td width="80" align="right"><p><?php echo $row[csf('sales_rate')]; ?></p></td> 
		            <td align="center"><p><?php echo $unit_of_measurement[$uom]; ?></p></td>
		        </tr>
		        <?php
					$k++;
					$tot_issue_qty   += $row[csf('sales_qty')];
				}
				?>
	            <tfoot>
	                <tr>
	                    <th colspan="4">Total&nbsp;</th>
	                    <th align="right"><?php echo number_format($tot_issue_qty,2); ?></th>
	                    <th>&nbsp;</th>
	                    <th>&nbsp;</th>
	                </tr>
	            </tfoot>
		    </tbody>
        </table>
        <script>setFilterGrid("table_body_popup",-1);</script>
    </div>
    <?php
}

if($action=='total_yarn_receive_popup')
{
	echo load_html_head_contents('Report Info', '../../../../', 1, 1, $unicode, '', '');
	extract($_REQUEST);

	$sql_rcv = "select a.sys_receive_no, a.receive_date, a.item_category_id, b.product_id, b.uom, b.receive_qnty
  				from inv_scrap_receive_mst a, inv_scrap_receive_dtls b
 				where a.id = b.mst_id and a.id = $mst_id and b.product_id=$product_id and a.status_active = 1 and b.status_active = 1
 				order by a.receive_date desc";

 	$product_library = return_library_array("select id, product_name_details from product_details_master where status_active=1 and is_deleted=0", 'id', 'product_name_details');

 	$sql_rcv_res = sql_select($sql_rcv);
	?>
    <div>
        <table width="99%" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
            <caption><strong>Total Yarn Receive Details</strong></caption>
			<thead>
			    <tr>
			        <th width="100">Receive ID</th>
			        <th width="120">Receive Date</th>
			        <th width="100">Category</th>
			        <th width="100">Item Description</th>
			        <th width="80">Qty</th>
			        <th>UoM</th>
			    </tr>
            </thead>
	        <tbody id="table_body_popup">
                <?php
				//echo $sql_scrap;die;		
	            $sql_scrap_res=sql_select($sql_scrap);
				$k=1;
				$tot_rcv_qty=0;
				foreach($sql_rcv_res as $row) {
					if($k%2==0)$bgcolor="#E9F3FF";  
					else $bgcolor="#FFFFFF";
                ?>
		        <tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $stylecolor; ?> onclick="change_color('tr_<?php echo $k; ?>','<?php echo $bgcolor; ?>')" id="tr_<?php echo $k; ?>">
		            <td width="100" align="center"><p><?php echo $row[csf('sys_receive_no')]; ?></p></td>
		            <td width="120" align="center"><p><?php echo change_date_format($row[csf('receive_date')]); ?></p></td>
		            <td width="100" align="center"><p><?php echo $item_category[$row[csf('item_category_id')]]; ?></p></td> 
		            <td width="100" align="center"><p><?php echo $product_library[$row[csf('product_id')]]; ?></p></td> 
		            <td width="80" align="right"><p><?php echo $row[csf('receive_qnty')]; ?></p></td> 
		            <td align="center"><p><?php echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
		        </tr>
		        <?php
					$k++;
					$tot_rcv_qty   += $row[csf('receive_qnty')];
				}
				?>
	            <tfoot>
	                <tr>
	                    <th colspan="4">Total&nbsp;</th>
	                    <th align="right"><?php echo number_format($tot_rcv_qty,2); ?></th>
	                    <th>&nbsp;</th>
	                </tr>
	            </tfoot>
		    </tbody>
        </table>
        <script>setFilterGrid("table_body_popup",-1);</script>
    </div>
    <?php
}

if($action=='total_yarn_issue_popup')
{
	echo load_html_head_contents('Report Info', '../../../../', 1, 1, $unicode, '', '');
	extract($_REQUEST);

	$sql_issue = "select a.sys_challan_no, b.sales_qty, a.selling_date, a.purpose, b.prod_id, a.item_category, b.sales_amount, b.sales_rate, b.sales_uom, b.rej_uom
  		from inv_scrap_sales_mst a, inv_scrap_sales_dtls b
  		where a.id=b.mst_id and b.prod_id=$product_id and a.status_active=1 and b.status_active=1";
  	// echo $sql_issue;
 	$product_library = return_library_array("select id, product_name_details from product_details_master where status_active=1 and is_deleted=0", 'id', 'product_name_details');

 	$sql_issue_res = sql_select($sql_issue);
	?>
    <div>
        <table width="99%" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
            <caption><strong>Total Yarn Issue Details</strong></caption>
			<thead>
			    <tr>
			        <th width="100">Issue ID</th>
			        <th width="120">Issue Date</th>
			        <th width="100">Category</th>
			        <th width="200">Item Description</th>
			        <th width="80">Qty</th>
			        <th>UoM</th>
			    </tr>
            </thead>
	        <tbody id="table_body_popup">
                <?php
				//echo $sql_scrap;die;		
	            $sql_scrap_res=sql_select($sql_scrap);
				$k=1;
				$tot_issue_qty=0;
				
				foreach($sql_issue_res as $row) {
					if($k%2==0)$bgcolor="#E9F3FF";
					else $bgcolor="#FFFFFF";
					$uom = $row[csf('sales_uom')] ? $row[csf('sales_uom')] : $row[csf('rej_uom')];
                ?>
		        <tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $stylecolor; ?> onclick="change_color('tr_<?php echo $k; ?>','<?php echo $bgcolor; ?>')" id="tr_<?php echo $k; ?>">
		            <td width="100" align="center"><p><?php echo $row[csf('sys_challan_no')]; ?></p></td>
		            <td width="120" align="center"><p><?php echo change_date_format($row[csf('selling_date')]); ?></p></td>
		            <td width="100" align="center"><p><?php echo $item_category[$row[csf('item_category')]]; ?></p></td> 
		            <td width="200" align="center"><p><?php echo $product_library[$row[csf('prod_id')]]; ?></p></td> 
		            <td width="80" align="right"><p><?php echo $row[csf('sales_qty')]; ?></p></td> 
		            <td align="center"><p><?php echo $unit_of_measurement[$uom]; ?></p></td>
		        </tr>
		        <?php
					$k++;
					$tot_issue_qty   += $row[csf('sales_qty')];
				}
				?>
	            <tfoot>
	                <tr>
	                    <th colspan="4">Total&nbsp;</th>
	                    <th align="right"><?php echo number_format($tot_issue_qty,2); ?></th>
	                    <th>&nbsp;</th>
	                </tr>
	            </tfoot>
		    </tbody>
        </table>
        <script>setFilterGrid("table_body_popup",-1);</script>
    </div>
    <?php
}

if($action=='total_grey_fabric_receive_popup')
{
	echo load_html_head_contents('Report Info', '../../../../', 1, 1, $unicode, '', '');
	extract($_REQUEST);

	$sql_rcv = "select a.sys_receive_no, a.receive_date, a.item_category_id, b.product_id, b.uom, b.receive_qnty
  				from inv_scrap_receive_mst a, inv_scrap_receive_dtls b
 				where a.id = b.mst_id and a.id = $mst_id and b.product_id=$product_id and a.status_active = 1 and b.status_active = 1
 				order by a.receive_date desc";

 	$product_library = return_library_array("select id, product_name_details from product_details_master where status_active=1 and is_deleted=0", 'id', 'product_name_details');

 	$sql_rcv_res = sql_select($sql_rcv);
	?>
    <div>
        <table width="99%" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
            <caption><strong>Total Yarn Receive Details</strong></caption>
			<thead>
			    <tr>
			        <th width="100">Receive ID</th>
			        <th width="120">Receive Date</th>
			        <th width="100">Category</th>
			        <th width="100">Item Description</th>
			        <th width="80">Qty</th>
			        <th>UoM</th>
			    </tr>
            </thead>
	        <tbody id="table_body_popup">
                <?php
				//echo $sql_scrap;die;		
	            $sql_scrap_res=sql_select($sql_scrap);
				$k=1;
				$tot_rcv_qty=0;
				foreach($sql_rcv_res as $row) {
					if($k%2==0)$bgcolor="#E9F3FF";  
					else $bgcolor="#FFFFFF";
                ?>
		        <tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $stylecolor; ?> onclick="change_color('tr_<?php echo $k; ?>','<?php echo $bgcolor; ?>')" id="tr_<?php echo $k; ?>">
		            <td width="100" align="center"><p><?php echo $row[csf('sys_receive_no')]; ?></p></td>
		            <td width="120" align="center"><p><?php echo change_date_format($row[csf('receive_date')]); ?></p></td>
		            <td width="100" align="center"><p><?php echo $item_category[$row[csf('item_category_id')]]; ?></p></td> 
		            <td width="100" align="center"><p><?php echo $product_library[$row[csf('product_id')]]; ?></p></td> 
		            <td width="80" align="right"><p><?php echo $row[csf('receive_qnty')]; ?></p></td> 
		            <td align="center"><p><?php echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
		        </tr>
		        <?php
					$k++;
					$tot_rcv_qty   += $row[csf('receive_qnty')];
				}
				?>
	            <tfoot>
	                <tr>
	                    <th colspan="4">Total&nbsp;</th>
	                    <th align="right"><?php echo number_format($tot_rcv_qty,2); ?></th>
	                    <th>&nbsp;</th>
	                </tr>
	            </tfoot>
		    </tbody>
        </table>
        <script>setFilterGrid("table_body_popup",-1);</script>
    </div>
    <?php
}

if($action=='total_grey_fabric_issue_popup')
{
	echo load_html_head_contents('Report Info', '../../../../', 1, 1, $unicode, '', '');
	extract($_REQUEST);

	$sql_issue = "select a.sys_challan_no, b.sales_qty, a.selling_date, a.purpose, b.prod_id, a.item_category, b.sales_amount, b.sales_rate, b.sales_uom, b.rej_uom
  		from inv_scrap_sales_mst a, inv_scrap_sales_dtls b
  		where a.id=b.mst_id and b.prod_id=$product_id and a.status_active=1 and b.status_active=1";
  	// echo $sql_issue;
 	$product_library = return_library_array("select id, product_name_details from product_details_master where status_active=1 and is_deleted=0", 'id', 'product_name_details');

 	$sql_issue_res = sql_select($sql_issue);
	?>
    <div>
        <table width="99%" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
            <caption><strong>Total Yarn Issue Details</strong></caption>
			<thead>
			    <tr>
			        <th width="100">Issue ID</th>
			        <th width="120">Issue Date</th>
			        <th width="100">Category</th>
			        <th width="200">Item Description</th>
			        <th width="80">Qty</th>
			        <th>UoM</th>
			    </tr>
            </thead>
	        <tbody id="table_body_popup">
                <?php
				//echo $sql_scrap;die;		
	            $sql_scrap_res=sql_select($sql_scrap);
				$k=1;
				$tot_issue_qty=0;
				
				foreach($sql_issue_res as $row) {
					if($k%2==0)$bgcolor="#E9F3FF";
					else $bgcolor="#FFFFFF";
					$uom = $row[csf('sales_uom')] ? $row[csf('sales_uom')] : $row[csf('rej_uom')];
                ?>
		        <tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $stylecolor; ?> onclick="change_color('tr_<?php echo $k; ?>','<?php echo $bgcolor; ?>')" id="tr_<?php echo $k; ?>">
		            <td width="100" align="center"><p><?php echo $row[csf('sys_challan_no')]; ?></p></td>
		            <td width="120" align="center"><p><?php echo change_date_format($row[csf('selling_date')]); ?></p></td>
		            <td width="100" align="center"><p><?php echo $item_category[$row[csf('item_category')]]; ?></p></td> 
		            <td width="200" align="center"><p><?php echo $product_library[$row[csf('prod_id')]]; ?></p></td> 
		            <td width="80" align="right"><p><?php echo $row[csf('sales_qty')]; ?></p></td> 
		            <td align="center"><p><?php echo $unit_of_measurement[$uom]; ?></p></td>
		        </tr>
		        <?php
					$k++;
					$tot_issue_qty   += $row[csf('sales_qty')];
				}
				?>
	            <tfoot>
	                <tr>
	                    <th colspan="4">Total&nbsp;</th>
	                    <th align="right"><?php echo number_format($tot_issue_qty,2); ?></th>
	                    <th>&nbsp;</th>
	                </tr>
	            </tfoot>
		    </tbody>
        </table>
        <script>setFilterGrid("table_body_popup",-1);</script>
    </div>
    <?php
}

if($action=='total_finish_fabric_receive_popup')
{
	echo load_html_head_contents('Report Info', '../../../../', 1, 1, $unicode, '', '');
	extract($_REQUEST);

	$sql_rcv = "select a.sys_receive_no, a.receive_date, a.item_category_id, b.product_id, b.uom, b.receive_qnty
  				from inv_scrap_receive_mst a, inv_scrap_receive_dtls b
 				where a.id = b.mst_id and a.id = $mst_id and b.product_id=$product_id and a.status_active = 1 and b.status_active = 1
 				order by a.receive_date desc";

 	$product_library = return_library_array("select id, product_name_details from product_details_master where status_active=1 and is_deleted=0", 'id', 'product_name_details');

 	$sql_rcv_res = sql_select($sql_rcv);
	?>
    <div>
        <table width="99%" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
            <caption><strong>Total Yarn Receive Details</strong></caption>
			<thead>
			    <tr>
			        <th width="100">Receive ID</th>
			        <th width="120">Receive Date</th>
			        <th width="100">Category</th>
			        <th width="100">Item Description</th>
			        <th width="80">Qty</th>
			        <th>UoM</th>
			    </tr>
            </thead>
	        <tbody id="table_body_popup">
                <?php
				//echo $sql_scrap;die;		
	            $sql_scrap_res=sql_select($sql_scrap);
				$k=1;
				$tot_rcv_qty=0;
				foreach($sql_rcv_res as $row) {
					if($k%2==0)$bgcolor="#E9F3FF";  
					else $bgcolor="#FFFFFF";
                ?>
		        <tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $stylecolor; ?> onclick="change_color('tr_<?php echo $k; ?>','<?php echo $bgcolor; ?>')" id="tr_<?php echo $k; ?>">
		            <td width="100" align="center"><p><?php echo $row[csf('sys_receive_no')]; ?></p></td>
		            <td width="120" align="center"><p><?php echo change_date_format($row[csf('receive_date')]); ?></p></td>
		            <td width="100" align="center"><p><?php echo $item_category[$row[csf('item_category_id')]]; ?></p></td> 
		            <td width="100" align="center"><p><?php echo $product_library[$row[csf('product_id')]]; ?></p></td> 
		            <td width="80" align="right"><p><?php echo $row[csf('receive_qnty')]; ?></p></td> 
		            <td align="center"><p><?php echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
		        </tr>
		        <?php
					$k++;
					$tot_rcv_qty   += $row[csf('receive_qnty')];
				}
				?>
	            <tfoot>
	                <tr>
	                    <th colspan="4">Total&nbsp;</th>
	                    <th align="right"><?php echo number_format($tot_rcv_qty,2); ?></th>
	                    <th>&nbsp;</th>
	                </tr>
	            </tfoot>
		    </tbody>
        </table>
        <script>setFilterGrid("table_body_popup",-1);</script>
    </div>
    <?php
}

if($action=='total_finish_fabric_issue_popup')
{
	echo load_html_head_contents('Report Info', '../../../../', 1, 1, $unicode, '', '');
	extract($_REQUEST);

	$sql_issue = "select a.sys_challan_no, b.sales_qty, a.selling_date, a.purpose, b.prod_id, a.item_category, b.sales_amount, b.sales_rate, b.sales_uom, b.rej_uom
  		from inv_scrap_sales_mst a, inv_scrap_sales_dtls b
  		where a.id=b.mst_id and b.prod_id=$product_id and a.status_active=1 and b.status_active=1";
  	// echo $sql_issue;
 	$product_library = return_library_array("select id, product_name_details from product_details_master where status_active=1 and is_deleted=0", 'id', 'product_name_details');

 	$sql_issue_res = sql_select($sql_issue);
	?>
    <div>
        <table width="99%" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
            <caption><strong>Total Yarn Issue Details</strong></caption>
			<thead>
			    <tr>
			        <th width="100">Issue ID</th>
			        <th width="120">Issue Date</th>
			        <th width="100">Category</th>
			        <th width="200">Item Description</th>
			        <th width="80">Qty</th>
			        <th>UoM</th>
			    </tr>
            </thead>
	        <tbody id="table_body_popup">
                <?php
				//echo $sql_scrap;die;		
	            $sql_scrap_res=sql_select($sql_scrap);
				$k=1;
				$tot_issue_qty=0;
				
				foreach($sql_issue_res as $row) {
					if($k%2==0)$bgcolor="#E9F3FF";
					else $bgcolor="#FFFFFF";
					$uom = $row[csf('sales_uom')] ? $row[csf('sales_uom')] : $row[csf('rej_uom')];
                ?>
		        <tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $stylecolor; ?> onclick="change_color('tr_<?php echo $k; ?>','<?php echo $bgcolor; ?>')" id="tr_<?php echo $k; ?>">
		            <td width="100" align="center"><p><?php echo $row[csf('sys_challan_no')]; ?></p></td>
		            <td width="120" align="center"><p><?php echo change_date_format($row[csf('selling_date')]); ?></p></td>
		            <td width="100" align="center"><p><?php echo $item_category[$row[csf('item_category')]]; ?></p></td> 
		            <td width="200" align="center"><p><?php echo $product_library[$row[csf('prod_id')]]; ?></p></td> 
		            <td width="80" align="right"><p><?php echo $row[csf('sales_qty')]; ?></p></td> 
		            <td align="center"><p><?php echo $unit_of_measurement[$uom]; ?></p></td>
		        </tr>
		        <?php
					$k++;
					$tot_issue_qty   += $row[csf('sales_qty')];
				}
				?>
	            <tfoot>
	                <tr>
	                    <th colspan="4">Total&nbsp;</th>
	                    <th align="right"><?php echo number_format($tot_issue_qty,2); ?></th>
	                    <th>&nbsp;</th>
	                </tr>
	            </tfoot>
		    </tbody>
        </table>
        <script>setFilterGrid("table_body_popup",-1);</script>
    </div>
    <?php
}

?>
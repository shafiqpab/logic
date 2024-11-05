<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------

if ($action=="load_drop_down_store")
{
	$data=explode("**",$data);
	if($data[1]==2) $disable=1; else $disable=0;
	echo create_drop_down( "cbo_store_name", 100, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and  a.status_active=1 and a.is_deleted=0 and a.company_id=$data[0] and b.category_type in(1)  order by a.store_name","id,store_name", 1, "-- All Store--", 0, "",$disable );
	exit();
}

//load drop down supplier
if ($action=="load_drop_down_supplier")
{
	echo create_drop_down( "cbo_supplier", 120, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data' and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name","id,supplier_name",0, "-- Select --", 0, "",0 );
	exit();
}

if ($action=="eval_multi_select")
{
 	echo "set_multiselect('cbo_supplier','0','0','','0');\n";
	exit();
}

if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_dyed_type=str_replace("'","",$cbo_dyed_type);

	$companyArr = return_library_array("select id,company_name from lib_company","id","company_name");
	$companyArr[0]="All Company";
	$supplierArr = return_library_array("select id, short_name from lib_supplier","id","short_name");
	$yarn_count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$color_name_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$buy_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');

	if(str_replace("'","",$cbo_company_name)){
		$company_cond= " and a.company_id=$cbo_company_name";
	}
	if(str_replace("'","",$cbo_yarn_count)){
		$search_cond.=" and a.yarn_count_id in(".str_replace("'","",$cbo_yarn_count).")";
	}
	if(str_replace("'","",$cbo_yarn_type)){
		$search_cond.=" and a.yarn_type =$cbo_yarn_type";
	}

	if(str_replace("'","",$cbo_supplier)){
		$search_cond.=" and  a.supplier_id in($cbo_supplier)";
	}

	if(str_replace("'","",$txt_lot_no)){
		$search_cond.=" and  a.lot like ('%".str_replace("'","",$txt_lot_no)."%')";
	}
	if(str_replace("'","",$txt_composition)!=""){
		$search_cond .= " and a.product_name_details like '%".trim($txt_composition)."%'";
	}

	if($cbo_dyed_type==1){
		$search_cond .= " and c.receive_purpose=2";
		$search_cond2 = " and c.receive_purpose=2";
	}
	if($cbo_dyed_type==2){
		$search_cond .= " and c.receive_purpose=2";
		$search_cond2 = " and c.receive_purpose=2";
	}

	if(str_replace("'","",$cbo_store_name)!=0){
		$search_cond .= " and c.store_id=$cbo_store_name";
	}


	/*
	if($db_type==0)
	{
		$exchange_rate=return_field_value("conversion_rate","currency_conversion_rate","currency=2 and status_active=1 and is_deleted=0 order by id DESC limit 0,1");
		$from_date=change_date_format($from_date,'yyyy-mm-dd');
		$to_date=change_date_format($to_date,'yyyy-mm-dd');
	}
	else if($db_type==2)
	{
		$exchange_rate=return_field_value("conversion_rate","(SELECT conversion_rate FROM currency_conversion_rate where currency=2 and status_active=1 and is_deleted=0 ORDER BY id DESC)","ROWNUM = 1");
		$from_date=change_date_format($from_date,'','',1);
		$to_date=change_date_format($to_date,'','',1);
	}
	else
	{
		$from_date=""; $to_date=""; $exchange_rate=1;
	}

	$txt_lot_no=trim($txt_lot_no);
	$txt_composition=trim($txt_composition);

	$search_cond="";

	if($cbo_dyed_type==0) $search_cond .= "";
	else if($cbo_dyed_type==1) $search_cond .= " and c.receive_purpose=2";
	else if($cbo_dyed_type==2) $search_cond .= " and c.receive_purpose!=2";

	if($cbo_yarn_type==0) $search_cond .= ""; else $search_cond .= " and a.yarn_type in ($cbo_yarn_type)";
	if($txt_count=="") $search_cond .= ""; else $search_cond .= " and a.yarn_count_id in($txt_count)";
	if($txt_lot_no=="") $search_cond .= ""; else $search_cond .= " and a.lot='".trim($txt_lot_no)."'";
	if($value_with==0) $search_cond .=""; else $search_cond .= "  and a.current_stock>0";
	if($cbo_supplier==0) $search_cond .=""; else $search_cond .= "  and a.supplier_id in($cbo_supplier)";
	if($txt_composition=="") $search_cond .= ""; else $search_cond .= " and a.product_name_details like '%".trim($txt_composition)."%'";

	if($show_val_column==1)
	{
		$value_width=300;
		$span=3;
		$column='<th rowspan="2" width="90">Avg. Rate (Tk)</th><th rowspan="2" width="110">Stock Value</th><th rowspan="2" width="100">Stock Value (USD)</th>';
	}
	else
	{
		$value_width=0;
		$span=0;
		$column='';
	}

	if($store_wise==1)
	{
		if($store_name==0) $store_cond .=""; else $store_cond .= " and a.store_id=$store_name";
		$table_width='2510'+$value_width;
		$colspan='28'+$span;

		if($db_type==0) $select_field="group_concat(distinct(a.store_id))";
		else if($db_type==2) $select_field="listagg(a.store_id,',') within group (order by a.store_id)";

		$trans_criteria_cond=" and c.transfer_criteria in(1,2)";
		$store_arr=return_library_array("select id, store_name from lib_store_location",'id','store_name');
	}
	else
	{
		$select_field="0";
		$table_width='2610'+$value_width;
		$colspan='29'+$span;
		$trans_criteria_cond=" and c.transfer_criteria=1";
	}

	if($cbo_company_name==0)
	{
		$company_cond="";
		$nameArray=sql_select("select allocation from variable_settings_inventory where item_category_id=1 and variable_list=18 and status_active=1 and is_deleted=0");
	}
	else
	{
		$company_cond= " and a.company_id=$cbo_company_name";
		$nameArray=sql_select("select allocation from variable_settings_inventory where company_name=$cbo_company_name and item_category_id=1 and variable_list=18 and status_active=1 and is_deleted=0");
	}
	$allocated_qty_variable_settings=$nameArray[0][csf('allocation')];
	//$allocated_qty_variable_settings=0;

	$receive_array=array();
	$sql_receive="Select a.prod_id, $select_field as store_id, max(a.weight_per_bag) as weight_per_bag, max(a.weight_per_cone) as weight_per_cone,
		sum(case when a.transaction_type in (1,4) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
		sum(case when a.transaction_type in (1) and c.receive_purpose<>5 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as purchase,
		sum(case when a.transaction_type in (1) and c.receive_purpose=5 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as rcv_loan,
		sum(case when a.transaction_type=4 and c.knitting_source=1 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as rcv_inside_return,
		sum(case when a.transaction_type=4 and c.knitting_source!=1 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as rcv_outside_return
		from inv_transaction a, inv_receive_master c where a.mst_id=c.id and a.transaction_type in (1,4) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_cond $store_cond group by a.prod_id";
	$result_sql_receive = sql_select($sql_receive);
	foreach($result_sql_receive as $row)
	{
		$receive_array[$row[csf("prod_id")]]['store_id']=$row[csf("store_id")];
		$receive_array[$row[csf("prod_id")]]['rcv_total_opening']=$row[csf("rcv_total_opening")];
		$receive_array[$row[csf("prod_id")]]['purchase']=$row[csf("purchase")];
		$receive_array[$row[csf("prod_id")]]['rcv_loan']=$row[csf("rcv_loan")];
		$receive_array[$row[csf("prod_id")]]['rcv_inside_return']=$row[csf("rcv_inside_return")];
		$receive_array[$row[csf("prod_id")]]['rcv_outside_return']=$row[csf("rcv_outside_return")];
		$receive_array[$row[csf("prod_id")]]['weight_per_bag']=$row[csf("weight_per_bag")];
		$receive_array[$row[csf("prod_id")]]['weight_per_cone']=$row[csf("weight_per_cone")];
	}

	unset($result_sql_receive);



	$issue_array=array();
	$sql_issue="select a.prod_id, $select_field as store_id,
		sum(case when a.transaction_type in (2,3) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as issue_total_opening,
		sum(case when a.transaction_type=2 and c.knit_dye_source=1 and c.issue_purpose<>5 and a.transaction_date  between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as issue_inside,
		sum(case when a.transaction_type=2 and c.knit_dye_source!=1 and c.issue_purpose<>5 and a.transaction_date  between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as issue_outside,
		sum(case when a.transaction_type=3 and c.entry_form=8 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as rcv_return,
		sum(case when a.transaction_type=2 and c.issue_purpose=5 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as issue_loan
		from inv_transaction a, inv_issue_master c
		where a.mst_id=c.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $store_cond group by a.prod_id";
	$result_sql_issue=sql_select($sql_issue);
	foreach($result_sql_issue as $row)
	{
		$issue_array[$row[csf("prod_id")]]['store_id']=$row[csf("store_id")];
		$issue_array[$row[csf("prod_id")]]['issue_total_opening']=$row[csf("issue_total_opening")];
		$issue_array[$row[csf("prod_id")]]['issue_inside']=$row[csf("issue_inside")];
		$issue_array[$row[csf("prod_id")]]['issue_outside']=$row[csf("issue_outside")];
		$issue_array[$row[csf("prod_id")]]['rcv_return']=$row[csf("rcv_return")];
		$issue_array[$row[csf("prod_id")]]['issue_loan']=$row[csf("issue_loan")];
	}

	unset($result_sql_issue);


	$transfer_qty_array=array();
	$sql_transfer="select a.prod_id,
	 	sum(case when a.transaction_type=6 and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as trans_out_total_opening,
		sum(case when a.transaction_type=5 and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as trans_in_total_opening,
		sum(case when a.transaction_type=6 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as transfer_out_qty,
		sum(case when a.transaction_type=5 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as transfer_in_qty
		from inv_transaction a, inv_item_transfer_mst c where a.mst_id=c.id and a.transaction_type in (5,6) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $trans_criteria_cond $store_cond group by a.prod_id";
	$result_sql_transfer = sql_select($sql_transfer);
	foreach($result_sql_transfer as $transRow)
	{
		$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_out_qty']=$transRow[csf("transfer_out_qty")];
		$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_in_qty']=$transRow[csf("transfer_in_qty")];
		$transfer_qty_array[$transRow[csf("prod_id")]]['trans_out_total_opening']=$transRow[csf("trans_out_total_opening")];
		$transfer_qty_array[$transRow[csf("prod_id")]]['trans_in_total_opening']=$transRow[csf("trans_in_total_opening")];
	}
	//var_dump($transfer_qty_array);
	unset($result_sql_transfer);


	if($db_type==0)
	{
		$yarn_allo_sql=sql_select("select product_id, group_concat(buyer_id) as buyer_id, group_concat(allocate_qnty) as allocate_qnty from com_balk_yarn_allocate where status_active=1 group by product_id");
		//LISTAGG(CAST( a.lc_sc_id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.lc_sc_id) as lc_id
	}
	else if($db_type==2)
	{
		$yarn_allo_sql=sql_select("select product_id, LISTAGG(CAST(buyer_id as VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY buyer_id) as buyer_id, LISTAGG(CAST(allocate_qnty AS VARCHAR(4000)),',') WITHIN GROUP(ORDER BY allocate_qnty) as allocate_qnty from com_balk_yarn_allocate where status_active=1 group by product_id");
	}
	$yarn_allo_arr=array();
	foreach($yarn_allo_sql as $row)
	{
		$yarn_allo_arr[$row[csf("product_id")]]['product_id']=$row[csf("product_id")];
		$yarn_allo_arr[$row[csf("product_id")]]['buyer_id']=implode(",",array_unique(explode(",",$row[csf("buyer_id")])));
		$yarn_allo_arr[$row[csf("product_id")]]['allocate_qnty']=implode(",",array_unique(explode(",",$row[csf("allocate_qnty")])));
	}

	unset($yarn_allo_sql);



	*/

	//------------------------------ageing colum
	$curr_date=str_replace("'","",$txt_date_from);
	$range=str_replace("'","",$txt_range);
	$colum=str_replace("'","",$txt_no_col);
	$c=0;
	for($col=1; $col<=$colum; $col++){
		$to=$range*$col;
		$from=$range*$c;
		if($colum==1){$caption = 'Above '.$from.' Days';}
		else if($col==1){$caption = $from.'-'.$to.' Days';}
		else if($col==$colum){$caption = 'Above '.$from.' Days';}
		else{$caption =  ($from+1).'-'.$to.' Days';}
		//$caption_arr[$caption]=$caption;
		$caption_arr[$to]=$caption;
		$c++;
	}
	$end_key = end(array_keys($caption_arr));
	//----------------------------------


	if($type==1)
	{

		$date_array=array();
		$returnRes_date="select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where is_deleted=0 and status_active=1 and item_category=1 group by prod_id";
		$result_returnRes_date = sql_select($returnRes_date);
		foreach($result_returnRes_date as $row)
		{
			$date_array[$row[csf("prod_id")]]['min_date']=$row[csf("min_date")];
			$date_array[$row[csf("prod_id")]]['max_date']=$row[csf("max_date")];
		}



	$receive_array=array();
	$sql_receive="Select a.mst_id, a.prod_id,a.transaction_date, max(a.weight_per_bag) as weight_per_bag, max(a.weight_per_cone) as weight_per_cone,sum(a.order_amount) as order_amount,

		sum(case when a.transaction_type =1  then a.cons_quantity else 0 end) as receive,
		sum(case when a.transaction_type =3  then a.cons_quantity else 0 end) as receive_return,
		sum(case when a.transaction_type =2  then a.cons_quantity else 0 end) as issue,
		sum(case when a.transaction_type =4  then a.cons_quantity else 0 end) as issue_return,

		sum(case when a.transaction_type =5  then a.cons_quantity else 0 end) as transfer_in,
		sum(case when a.transaction_type =6  then a.cons_quantity else 0 end) as transfer_out,

		sum(case when a.transaction_type =1  then a.cons_amount else 0 end) as receive_amt,
		sum(case when a.transaction_type =3  then a.cons_amount else 0 end) as receive_return_amt,
		sum(case when a.transaction_type =2  then a.cons_amount else 0 end) as issue_amt,
		sum(case when a.transaction_type =4  then a.cons_amount else 0 end) as issue_return_amt,

		sum(case when a.transaction_type =5  then a.cons_amount else 0 end) as transfer_in_amt,
		sum(case when a.transaction_type =6  then a.cons_amount else 0 end) as transfer_out_amt

		from inv_transaction a, product_details_master c where a.prod_id=c.id and a.transaction_type in (1,2,3,4,5,6) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond2 group by a.prod_id, a.transaction_date, a.mst_id"; //,c.exchange_rate,c.receive_date //, inv_receive_master c
		//echo $sql_receive;
	$result_sql_receive = sql_select($sql_receive);
	foreach($result_sql_receive as $row)
	{
		$ageOfDays = datediff("d",$row[csf("transaction_date")],$curr_date);
			$receiv_blance=($row[csf("receive")]+$row[csf("issue_return")]);
			$issue_blance =($row[csf("receive_return")]+$row[csf("issue")]);
			$stok_blance  =($row[csf("receive")]+$row[csf("issue_return")]+$row[csf("transfer_in")])-($row[csf("receive_return")]+$row[csf("issue")]+$row[csf("transfer_out")]);
			$stock_cons_amount_balance  =($row[csf("receive_amt")]+$row[csf("issue_return_amt")]+$row[csf("transfer_in_amt")])-($row[csf("receive_return_amt")]+$row[csf("issue_amt")]+$row[csf("transfer_out_amt")]);
			$stock[$row[csf("prod_id")]]+=$stok_blance;
			$age_qty_issue_arr[$row[csf("prod_id")]]+=$issue_blance;
			//$stock_amount[$row[csf("prod_id")]]+=$row[csf("order_amount")];
			$stock_cons_amount[$row[csf("prod_id")]]+=$stock_cons_amount_balance;
			$stock_amount_usd[$row[csf("prod_id")]]+=$row[csf("order_amount")]/$exchange_rate_arr[$row[csf("mst_id")]];

		foreach($caption_arr as $key=>$value){
			$start=($key-$range);
			if($start <= $ageOfDays && $key >= $ageOfDays){
				$age_qty_arr[$row[csf("prod_id")]][$key]+=$receiv_blance;
				break;
			}
			elseif($end_key < $ageOfDays){
				$age_qty_arr[$row[csf("prod_id")]][$end_key]+=$receiv_blance;
				break;
			}

		}

	}

	//$age_qty_issue_arr=array(6602=>270);
	foreach($age_qty_issue_arr as $pid=>$issueValue)
	{
		$restValue=$issueValue;
		foreach(array_reverse($caption_arr,true) as $key=>$value){

			if($age_qty_arr[$pid][$key]<$restValue && $age_qty_arr[$pid][$key]>0){
				$restValue=$restValue-$age_qty_arr[$pid][$key];
				$age_qty_arr[$pid][$key]=0;
			}
			else if($age_qty_arr[$pid][$key])
			{
				$age_qty_arr[$pid][$key]-=$restValue;break;
			}

		}


	}









	unset($result_sql_receive);

	 //var_dump($age_qty_arr);
	//echo $sql_receive;

		/*if($cbo_dyed_type==0)
		{
			$sql="select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit from product_details_master a
		where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit order by a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.id";
		}
		else
		{
			$sql="select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit,c.remarks from product_details_master a, inv_transaction b, inv_receive_master c
		where a.id=b.prod_id and b.mst_id=c.id and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.entry_form=1 and b.transaction_type=1 $company_cond $search_cond
		group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit,c.remarks order by a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd,a.yarn_type,a.id";
		}*/
		 //echo $sql;
		//die;//echo count($result);

		$sql="select a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit,listagg(c.remarks,',') within group(order by c.id)  as remarks  from product_details_master a, inv_transaction b, inv_receive_master c
		where a.id=b.prod_id and b.mst_id=c.id and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.entry_form=1 and b.transaction_type=1 $company_cond $search_cond
		group by a.id, a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit order by a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd,a.yarn_type,a.id";

		$result = sql_select($sql);
		foreach($result as $row)
		{
			$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]]." ".$row[csf("yarn_comp_percent1st")]."%\n";
			if($row[csf("yarn_comp_type2nd")]!=0) $compositionDetails.=$composition[$row[csf("yarn_comp_type2nd")]]." ".$row[csf("yarn_comp_percent2nd")]."%";
			$key=$row[csf("yarn_count_id")].$compositionDetails.$row[csf("yarn_type")];


		   $yarn_data_arr[$key][]=array(
		   	'product_id'	=>$row[csf("id")],
			'yarn_count_id'	=>$row[csf("yarn_count_id")],
		    'yarn_type'		=>$row[csf("yarn_type")],
		  	'composition'	=>$compositionDetails,
			'color'			=>$row[csf("color")],
			'lot'			=>$row[csf("lot")],
			'supplier_id'	=>$row[csf("supplier_id")],
			'remarks'		=>$row[csf("remarks")],

		   );

		}

	$table_width=($colum*50)+1500;
	$colspan=15+$colum;
	ob_start();
	?>
		<style>
			.wrd_brk{word-break: break-all;word-wrap: break-word;}
		</style>
        <table width="<? echo $table_width; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" >
            <thead>
                <tr class="form_caption" style="border:none;">
                    <td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:16px; font-weight:bold" >Daily Yarn Stock </td>
                </tr>
                <tr style="border:none;">
                    <td colspan="<? echo $colspan; ?>" align="center" style="border:none; font-size:14px;">
                        Company Name : <? echo $companyArr[str_replace("'","",$cbo_company_name)]; ?>
                    </td>
                </tr>
                <tr style="border:none;">
                    <td colspan="<? echo $colspan; ?>" align="center" style="border:none;font-size:12px; font-weight:bold">
                        <? if($from_date!="" && $to_date!="") echo "From ".change_date_format($from_date,'dd-mm-yyyy')." To ".change_date_format($to_date,'dd-mm-yyyy')."" ;?>
                    </td>
                </tr>
                <tr>
                    <th rowspan="2" width="30">SL</th>
                    <th colspan="7">Description</th>
                    <th rowspan="2" width="100">Wgt. Bag/Cone</th>
                    <th rowspan="2" width="100">Stock</th>
                    <th colspan="<? echo $colum;?>">Ageing Range</th>
                    <th rowspan="2" width="100">Avg. Rate</th>
                    <th rowspan="2" width="100">Stock Value</th>
                    <th rowspan="2" width="100">Stock Value USD</th>
                    <!--<th rowspan="2" width="100">Allocated to Order</th>
                    <th rowspan="2" width="100">Un Allocated Qty.</th>-->
                    <th rowspan="2" width="100">DOH</th>
                    <th rowspan="2">Remarks</th>
                </tr>
                <tr>
                    <th width="60">Prod.ID</th>
                    <th width="60">Count</th>
                    <th width="100">Composition</th>
                    <th width="100">Yarn Type</th>
                    <th width="80">Color</th>
                    <th width="100">Lot</th>
                    <th width="100">Supplier</th>
                    <? foreach($caption_arr as $fill_value){ ?>
                    <th width="50"><? echo $fill_value;?></th>
                    <? } ?>

                </tr>
            </thead>
        </table>


		<div style="width:<? echo $table_width+17; ?>px; overflow-y:scroll; max-height:310px; font-size:12px;" id="scroll_body" >
		<table width="<? echo $table_width; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
         <?
		 	$i=1;
		 	foreach($yarn_data_arr as $yarn_key=>$yaray_rows){?>
			 <? foreach($yaray_rows as $row){
				 $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
				 $daysOnHand = datediff("d",$date_array[$row["product_id"]]['max_date'],date("Y-m-d"));
				 ?>
             <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                <td width="30" class="wrd_brk"><? echo $i; ?></td>
                <td width="60" class="wrd_brk"><? echo $row["product_id"]; ?></td>
                <td width="60" class="wrd_brk"><p><? echo $yarn_count_arr[$row["yarn_count_id"]]; ?>&nbsp;</p></td>
                <td width="100" class="wrd_brk"><p><? echo $row["composition"]; ?>&nbsp;</p></td>
                <td width="100" class="wrd_brk"><p><? echo $yarn_type[$row["yarn_type"]]; ?>&nbsp;</p></td>
                <td width="80" class="wrd_brk"><p><? echo $color_name_arr[$row["color"]]; ?>&nbsp;</p></td>
                <td width="100" class="wrd_brk"><p><? echo $row["lot"]; ?>&nbsp;</p></td>
                <td width="100" class="wrd_brk"><p><? echo $supplierArr[$row["supplier_id"]]; ?>&nbsp;</p></td>
                <td width="100" class="wrd_brk"><p><? echo 'Bg:'.$receive_array[$row["product_id"]]['weight_per_bag'].'; '.'Cn:'.$receive_array[$row["product_id"]]['weight_per_cone']; ?>&nbsp;</p></td>

                <td width="100" align="right" class="wrd_brk"><? echo round($stock[$row["product_id"]]); $sub_tot_stok[$yarn_key]+=$stock[$row["product_id"]]; ?></td>
               <? foreach($caption_arr as $key=>$fill_value){ ?>
                <td width="50" align="right" class="wrd_brk"><p>
					<?
						echo round($age_qty_arr[$row["product_id"]][$key]);
						$sub_tot_age_qty_arr[$yarn_key][$key]+=$age_qty_arr[$row["product_id"]][$key];
					?>
                </p></td>
               <? } ?>

                <td width="100" align="right" class="wrd_brk"><p>
				<?
				 	//echo $row["product_id"]." == ".$stock_amount[$row["product_id"]]." == ".$stock[$row["product_id"]]." _ ";
					//$aveg_rage=$stock_amount[$row["product_id"]]/$stock[$row["product_id"]];
					if($stock[$row["product_id"]])
					{
						$aveg_rage=$stock_cons_amount[$row["product_id"]]/$stock[$row["product_id"]];
						echo number_format($aveg_rage,4);
					}
					else
					{
						echo "0.0000";
					}

				?>
                </p></td>
                <td width="100" align="right" class="wrd_brk"><p>
				<?
					$stock_value = $stock_cons_amount[$row["product_id"]];
					echo number_format($stock_value,2);
					$sub_tot_stock_value[$yarn_key]+=$stock_value;
				?>
                </p></td>
                <td width="100" align="right" class="wrd_brk"><p>
				<?
					$stock_value_usd = $stock_amount_usd[$row["product_id"]];
					if(number_format($stock_value_usd,2) >0.00)
					{
						echo number_format($stock_value_usd,2);
						$sub_tot_stock_value_usd[$yarn_key]+=$stock_value_usd;
					}
					else
					{
						echo "0.00";
					}

				?>
                </p></td>
                <!--<td width="100"><p></p></td>
                <td width="100"><p></p></td> -->
                <td width="100" align="center" class="wrd_brk"><? echo $daysOnHand;?></td>
                <td class="wrd_brk"><p><? echo $row["remarks"];?></p></td>
         </tr>
	   <? $i++;} ?>
            <tr style="background:#DDD">
                <td colspan="9" class="wrd_brk">Sub Total</td>
                <td width="100" align="right" class="wrd_brk"><? echo round($sub_tot_stok[$yarn_key]);$grand_tot_stok+=$sub_tot_stok[$yarn_key]; ?></td>
                <? foreach($caption_arr as $key=>$fill_value){ ?>
                <td width="50" align="right" class="wrd_brk"><p>
					<?
						echo round($sub_tot_age_qty_arr[$yarn_key][$key]);
						$grand_tot_age_qty_arr[$key]+=$sub_tot_age_qty_arr[$yarn_key][$key];
					?>
                </p></td>
               <? } ?>
                <td width="100" class="wrd_brk"><p></p></td>
                <td width="100" align="right" class="wrd_brk"><p>
					<? echo number_format($sub_tot_stock_value[$yarn_key],2);$grand_tot_stock_value+=$sub_tot_stock_value[$yarn_key]?>
                </p></td>
                <td width="100" align="right" class="wrd_brk"><p>
					<? echo number_format($sub_tot_stock_value_usd[$yarn_key],2);$grand_tot_stock_value_usd+=$sub_tot_stock_value_usd[$yarn_key];?>
                </p></td>
                <!--<td width="100"><p></p></td>
                <td width="100"><p></p></td>-->
                <td width="100"><p></p></td>
                <td><p></p></td>
            </tr>
   <? } ?>

   </table>
   </div>

   <table width="<? echo $table_width; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
        <tfoot>
            <th colspan="9" class="wrd_brk">Grand Total</th>
            <th width="100" align="right" class="wrd_brk"><? echo round($grand_tot_stok); ?></th>
            <? foreach($caption_arr as $key=>$fill_value){ ?>
            <th width="50" align="right" class="wrd_brk"><p><? echo round($grand_tot_age_qty_arr[$key]);?></p></th>
           <? } ?>
            <th width="100" class="wrd_brk"><p></p></th>
            <th width="100" align="right" class="wrd_brk"><p><? echo number_format($grand_tot_stock_value,2);?></p></th>
            <th width="100" align="right" class="wrd_brk"><p><? echo number_format($grand_tot_stock_value_usd,2);?></p></th>
            <!--<th width="100"><p></p></th>
            <th width="100"><p></p></th>-->
            <th width="100"><p></p></th>
            <th width="244"><p></p></th>
        </tfoot>
   </table>

    <?

	}



    $html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename";
    exit();
}

if($action=="allocation_popup")
{
	echo load_html_head_contents("Allocation Statement", "../../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $prod_id;
	$issue_array=array();
	$sql_issue="select c.po_breakdown_id, sum(c.quantity) as issue_qty
	from inv_issue_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and a.issue_purpose in(1,2) and c.trans_type=2 and a.entry_form=3 and b.item_category=1 and c.prod_id=$prod_id group by c.po_breakdown_id";
	$result_issue = sql_select($sql_issue);
	foreach($result_issue as $row)
	{
		$issue_array[$row[csf("po_breakdown_id")]]=$row[csf("issue_qty")];
	}

	$issue_return_array=array();
	$sql_return="Select c.po_breakdown_id, sum(c.quantity) as issue_return_qty from inv_receive_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and c.trans_type=4 and a.entry_form=9 and b.id=c.trans_id and b.item_category=1 and c.issue_purpose in(1,2) and c.prod_id=$prod_id group by c.po_breakdown_id";
	$result_return = sql_select($sql_return);
	foreach($result_return as $row)
	{
		$issue_return_array[$row[csf("po_breakdown_id")]]=$row[csf("issue_return_qty")];
	}
	//var_dump($issue_array);
	$sql_allocation="select item_id, job_no, po_break_down_id, booking_no, sum(qnty) as allocate_qty from inv_material_allocation_dtls where status_active=1 and is_deleted=0 and item_id='$prod_id' group by item_id, job_no, po_break_down_id, booking_no";
	?>
	<div align="center">
        <table width="870" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
            <thead>
                <th width="25">SL</th>
                <th width="110">Job No.</th>
                <th width="100">Order No.</th>
                <th width="100">File No.</th>
                <th width="100">Ref. No.</th>
                <th width="110">Booking No.</th>
                <th width="70">Allocated Qty</th>
                <th width="70">Issue Qty</th>
                <th width="60">Issue Rtn Qty</th>
                <th width="">Cumul. Balance</th>
            </thead>
        </table>
        <div style="width:870px; overflow-y:scroll; max-height:300px" id="scroll_body">
            <table width="852" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
            <?
			$po_number_arr=array();
            $po_sql=sql_select( "select id, file_no, grouping, po_number from wo_po_break_down");
			foreach($po_sql as $row)
			{
				$po_number_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
				$po_number_arr[$row[csf("id")]]['file']=$row[csf("file_no")];
				$po_number_arr[$row[csf("id")]]['ref']=$row[csf("grouping")];
			}
            $i=1;
            $result_allocation = sql_select($sql_allocation);
            $balance='';
            foreach($result_allocation as $row)
            {
                if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
                $issue_qty=$issue_array[$row[csf("po_break_down_id")]];
                $return_qty=$issue_return_array[$row[csf("po_break_down_id")]];
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="25"><? echo $i; ?></td>
                    <td width="110"><div style="word-wrap:break-word; width:110px"><? echo $row[csf("job_no")]; ?></div></td>
                    <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $po_number_arr[$row[csf("po_break_down_id")]]['po']; ?></div></td>
                    <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $po_number_arr[$row[csf("po_break_down_id")]]['file']; ?></div></td>
                    <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $po_number_arr[$row[csf("po_break_down_id")]]['ref']; ?></div></td>
                    <td width="110"><div style="word-wrap:break-word; width:110px"><? echo $row[csf("booking_no")]; ?></div></td>
                    <td width="70" align="right"><? echo number_format($row[csf("allocate_qty")],2); ?>&nbsp;</td>
                    <td width="70" align="right"><? echo number_format($issue_qty,2); ?>&nbsp;</td>
                    <td width="60" align="right"><? echo number_format($return_qty,2); ?>&nbsp;</td>
                    <td align="right"><? $balance=$balance+$row[csf("allocate_qty")]-$issue_qty+$return_qty;  echo $balance; ?>&nbsp;</td>
                </tr>
                <?
                $i++;
            }
            ?>
            </table>
        </div>
    </div>
<?
	exit();
}

if($action=="stock_popup")
{
	echo load_html_head_contents("Stock Details", "../../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);

	?>
	<fieldset style="width:720px">
    	<legend>Yarn Receive Details</legend>
        <table width="720" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
            <thead>
                <th width="50">SL</th>
                <th width="120">MRR No.</th>
                <th width="100">Receive Date</th>
                <th width="110">Receive Qty.</th>
                <th width="110">Receive Basis</th>
                <th>BTB LC No.</th>
            </thead>
        </table>
        <div style="width:720px; overflow-y:scroll; max-height:300px" id="scroll_body">
            <table width="700" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
            <?
            $i=1; $tot_recv_qnty='';
			$btblc_arr=return_library_array("select b.pi_id, a.lc_number from com_btb_lc_master_details a, com_btb_lc_pi b where a.id=b.com_btb_lc_master_details_id and a.item_category_id=1",'pi_id','lc_number');
			$sql="select c.id, c.recv_number, c.receive_date, c.receive_basis, c.booking_id, sum(b.cons_quantity) as qnty from inv_transaction b, inv_receive_master c
		where b.mst_id=c.id and b.prod_id=$prod_id and c.entry_form=1 and b.transaction_type=1 and b.item_category=1 and b.status_active=1 and b.is_deleted=0 group by c.id, c.recv_number, c.receive_date, c.receive_basis, c.booking_id order by c.id";
            $result = sql_select($sql);
            foreach($result as $row)
            {
                if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";

                $receive_qty=$row[csf("qnty")];
				$tot_recv_qnty+=$receive_qty;
				$btblc_no='';
				if($row[csf("receive_basis")]==1)
				{
					$btblc_no=$btblc_arr[$row[csf("booking_id")]];
				}
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="50"><? echo $i; ?></td>
                    <td width="120"><p><? echo $row[csf("recv_number")]; ?></p></td>
                    <td width="100" align="center"><? echo change_date_format($row[csf("receive_date")]); ?>&nbsp;</td>
                    <td width="110" align="right"><? echo number_format($receive_qty,2); ?>&nbsp;</td>
                    <td width="110"><p><? echo $receive_basis_arr[$row[csf("receive_basis")]]; ?></p></td>
                    <td><p><? echo $btblc_no; ?>&nbsp;</p></td>
                </tr>
                <?
                $i++;
            }
            ?>
            <tfoot>
            	<th colspan="3">Total</th>
                <th><? echo number_format($tot_recv_qnty,2); ?>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tfoot>
            </table>
        </div>
    </fieldset>
<?
	exit();
}

if($action=="transferPopup")
{
	echo load_html_head_contents("Transfer Details", "../../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);

	if($store_name==0) $store_cond=""; else $store_cond= " and a.store_id=$store_name";

	$sql_transfer="select
					sum(case when c.transfer_criteria=1 then a.cons_quantity else 0 end) as com_trans_qty,
					sum(case when c.transfer_criteria=2 then a.cons_quantity else 0 end) as store_trans_qty
					from inv_transaction a, inv_item_transfer_mst c where a.mst_id=c.id and a.transaction_date between '".$from_date."' and '".$to_date."' and a.transaction_type in (5,6) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.transaction_type=$trans_type and c.transfer_criteria in(1,2) and a.prod_id=$prod_id $store_cond";
	$transferData = sql_select($sql_transfer);
	$com_trans_qty=$transferData[0][csf('com_trans_qty')];
	$store_trans_qty=$transferData[0][csf('store_trans_qty')];

	$cap_arr=array(5=>"In",6=>"Out");
	?>
    <div align="center">
	<fieldset style="width:420px; margin-left:7px;">
    	<legend>Transfer <? echo $cap_arr[$trans_type]; ?> Details</legend>
        <table width="400" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
            <thead>
                <th width="50">SL</th>
                <th width="150">Transfer Type</th>
                <th>Transfer Qty.</th>
            </thead>
            <tr bgcolor="#E9F3FF" onclick="change_color('tr_1','#E9F3FF')" id="tr_1">
                <td width="50">1</td>
                <td width="150">Company To Company</td>
                <td align="right"><? echo number_format($com_trans_qty,2); ?>&nbsp;</td>
            </tr>
            <tr bgcolor="#FFFFFF" onclick="change_color('tr_2','#FFFFFF')" id="tr_2">
                <td width="50">2</td>
                <td width="150">Store To Store</td>
                <td align="right"><? echo number_format($store_trans_qty,2); ?>&nbsp;</td>
            </tr>
            <tfoot>
            	<th colspan="2">Total</th>
                <th><? echo number_format($com_trans_qty+$store_trans_qty,2); ?>&nbsp;</th>
            </tfoot>
		</table>
    </fieldset>
    </div>
<?
	exit();
}
?>

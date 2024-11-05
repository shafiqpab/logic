<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
if ($action=="load_drop_down_store")
{
	$data=explode('_',$data);
	if($db_type==0)
     {
		echo create_drop_down( "cbo_store_name", 120, "select id,store_name from lib_store_location where status_active=1 and is_deleted=0 and FIND_IN_SET(".$data[0].",company_id) and FIND_IN_SET(".$data[1].",item_category_id) order by store_name","id,store_name", 1, "--Select Store--", 0, "",0 );
	 }
 if($db_type==2 || $db_type==1)
     { $find_inset_item=" ',' || item_category_id || ',' LIKE '%,$data[1],%' "; $find_inset_company=" ',' || company_id || ',' LIKE '%,$data[0],%' ";

		//echo "select id,store_name from lib_store_location where status_active=1 and is_deleted=0 and $find_inset_company and $find_inset_item order by store_name";die;
		echo create_drop_down( "cbo_store_name", 120, "select id,store_name from lib_store_location where status_active=1 and is_deleted=0 and $find_inset_company and $find_inset_item order by store_name","id,store_name", 1, "--Select Store--", 1, "",0 );
		

	 }
	 die;
}

if ($action=="item_account_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data);
	?>
    <script>
	 var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();

	function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

	function js_set_value(id)
	{
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
 <?
		$itemgroupArr = return_library_array("select id,item_name from  lib_item_group where status_active=1 and is_deleted=0","id","item_name");
		$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
		if ($data[2]==0) $item_name =""; else $item_name =" and item_group_id in($data[2])";

	$sql="SELECT id, item_account, item_category_id, item_group_id, item_description, supplier_id, sub_group_name from product_details_master where company_id=$data[0] and item_category_id='$data[1]' $item_name and  status_active=1 and is_deleted=0";
		$arr=array(1=>$item_category,2=>$itemgroupArr,5=>$supplierArr);
		echo  create_list_view("list_view", "Item Account,Item Category,Item Group,Item Sub Group,Item Description,Supplier,Product ID", "70,120,120,100,170,100","780","400",0, $sql , "js_set_value", "id,item_description", "", 0, "0,item_category_id,item_group_id,0,0,supplier_id,0", $arr , "item_account,item_category_id,item_group_id,sub_group_name,item_description,supplier_id,id", "",'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0','',1) ;
		die;
}

if ($action=="item_group_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data);
?>
    <script>
		  function js_set_value(id)
		  {
			  document.getElementById('item_name_id').value=id;
			  parent.emailwindow.hide();
		  }
	</script>
        <input type="hidden" id="item_name_id" />
    <?
	if ($data[1]==0) $item_category =""; else $item_category =" and item_category in($data[1])";
	// $item_category;
	$sql="SELECT id,item_name from  lib_item_group where status_active=1 and is_deleted=0 $item_category"; //id=$data[1] and

	echo  create_list_view("list_view", "Item Name", "350","500","330",0, $sql , "js_set_value", "id,item_name", "", 1, "0", $arr , "item_name", "periodical_purchase_report_controller",'setFilterGrid("list_view",-1);','0') ;
	die;
}

if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$report_type=str_replace("'","",$report_type);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
	$item_account_id=str_replace("'","",$item_account_id);
	$item_group_id=str_replace("'","",$item_group_id);
	$cbo_store_name=str_replace("'","",$cbo_store_name);

	


	if($report_type==2)
	{
		$company_cond="";
		if ($cbo_company_name>0) $company_cond =" and company_id=$cbo_company_name";
		$mrr_rate_sql=sql_select("select prod_id, sum(cons_quantity) as cons_quantiy, sum(cons_amount) as cons_amount from inv_transaction where status_active=1 and is_deleted=0 and item_category in(5,6,7,23) and transaction_type in(1,4,5) $company_cond group by prod_id");
		$mrr_rate_arr=array();
		foreach($mrr_rate_sql as $row)
		{
			$mrr_rate_arr[$row[csf("prod_id")]]=$row[csf("cons_amount")]/$row[csf("cons_quantiy")];
		}
		$sql_cond="";
		if ($cbo_company_name>0) $sql_cond =" and a.company_id=$cbo_company_name";


		if ($cbo_item_category_id>0) $sql_cond.=" and a.item_category_id='$cbo_item_category_id'";  else $sql_cond.=" and a.item_category_id in(5,6,7,23)";
		if ($item_account_id!="") $sql_cond.=" and a.id in ($item_account_id)";
		if ($item_group_id>0) $sql_cond.=" and a.item_group_id='$item_group_id'";
		if ($cbo_store_name>0)  $sql_cond.=" and b.store_id=$cbo_store_name ";


		if($db_type==0)
		{
			//$exchange_rate=return_field_value("conversion_rate","currency_conversion_rate","currency=2 and status_active=1 and is_deleted=0 order by id DESC limit 0,1");
			$from_date=change_date_format($from_date,'yyyy-mm-dd');
			$to_date=change_date_format($to_date,'yyyy-mm-dd');
		}
		else if($db_type==2)
		{
			//$exchange_rate=return_field_value("conversion_rate","(SELECT conversion_rate FROM currency_conversion_rate where currency=2 and status_active=1 and is_deleted=0 ORDER BY id DESC)","ROWNUM = 1");
			$from_date=change_date_format($from_date,'','',1);
			$to_date=change_date_format($to_date,'','',1);
		}
		else
		{
			$from_date=""; $to_date="";
		}
		$search_cond="";
		//if($value_with==0) $search_cond =""; else $search_cond= "  and b.current_stock>0";

		$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
		$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
		$storeArr = return_library_array("select id,store_name from lib_store_location where status_active=1 and is_deleted=0","id","store_name");
		$itemnameArr = return_library_array("select id,item_name from lib_item_creation where status_active=1 and is_deleted=0","id","item_name");
		$itemgroupArr = return_library_array("select id,item_name from  lib_item_group where status_active=1 and is_deleted=0","id","item_name");


		/*if($db_type==0)
		{
			$sql="Select b.id as prod_id,
			sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
			sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as iss_total_opening,
			sum(case when a.transaction_type=1 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as purchase,
			sum(case when a.transaction_type=2 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as issue,
			sum(case when a.transaction_type in(4) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as issue_return,
			sum(case when a.transaction_type in(3) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as receive_return,

			sum(case when a.transaction_type in(6) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as item_transfer_issue,
			sum(case when a.transaction_type in(5) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as item_transfer_receive,
			sum(case when a.transaction_type in(1,4) and a.receive_basis in(1,2) then a.cons_quantity else 0 end) as rcv_total_wo,
			group_concat(a.batch_lot) as lot_no
			from inv_transaction a, product_details_master b
			where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and  b.is_deleted=0 and a.transaction_type in (1,2,3,4,5,6) and b.item_category_id in (5,6,7) and  a.item_category in (5,6,7) and a.order_id=0 $company_id $group_id $prod_cond $store_id $item_account $search_cond  group by b.id order by b.id ASC";
		}
		else if($db_type==2)
		{
			$sql="Select b.id as prod_id,
			sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
			sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as iss_total_opening,
			sum(case when a.transaction_type=1 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as purchase,
			sum(case when a.transaction_type=2 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as issue,
			sum(case when a.transaction_type in(4) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as issue_return,
			sum(case when a.transaction_type in(3) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as receive_return,

			sum(case when a.transaction_type in(6) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as item_transfer_issue,
			sum(case when a.transaction_type in(5) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as item_transfer_receive,
			sum(case when a.transaction_type in(1,4) and a.receive_basis in(1,2) then a.cons_quantity else 0 end) as rcv_total_wo,
			listagg(cast(a.batch_lot as varchar(4000)), ',') within group (order by a.batch_lot)  as lot_no
			from inv_transaction a, product_details_master b
			where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and  b.is_deleted=0 and a.transaction_type in (1,2,3,4,5,6) and b.item_category_id in (5,6,7) and  a.item_category in (5,6,7) and a.order_id=0 $company_id $group_id $prod_cond $store_id $item_account $search_cond  group by b.id order by b.id ASC";
		}
		//echo $sql;die;
		$trnasactionData = sql_select($sql);
		$data_array=array();
		foreach($trnasactionData as $row_p)
		{
			$data_array[$row_p[csf("prod_id")]]['rcv_total_opening']=$row_p[csf("rcv_total_opening")];
			$data_array[$row_p[csf("prod_id")]]['iss_total_opening']=$row_p[csf("iss_total_opening")];
			$data_array[$row_p[csf("prod_id")]]['purchase']=$row_p[csf("purchase")];
			$data_array[$row_p[csf("prod_id")]]['issue']=$row_p[csf("issue")];
			$data_array[$row_p[csf("prod_id")]]['issue_return']=$row_p[csf("issue_return")];
			$data_array[$row_p[csf("prod_id")]]['receive_return']=$row_p[csf("receive_return")];
			$data_array[$row_p[csf("prod_id")]]['item_transfer_issue']=$row_p[csf("item_transfer_issue")];
			$data_array[$row_p[csf("prod_id")]]['item_transfer_receive']=$row_p[csf("item_transfer_receive")];
			$data_array[$row_p[csf("prod_id")]]['rcv_total_wo']=$row_p[csf("rcv_total_wo")];
			$data_array[$row_p[csf("prod_id")]]['lot_no']=implode(",",array_unique(explode(",",$row_p[csf("lot_no")])));
		} //var_dump($data_array);die;

		$sql="select b.id, b.item_code, b.item_category_id,b.item_group_id,b.unit_of_measure,b.item_description,b.sub_group_name, b.current_stock,avg_rate_per_unit
		from product_details_master b where b.status_active=1 and b.is_deleted=0 and b.company_id='$cbo_company_name' $item_category_id $group_id $prod_cond $item_account $search_cond order by b.id";*/

		if($to_date!="")
		{
			if($to_date!="") $mrr_date_cond=" and a.transaction_date<='$to_date'";
			if($to_date!="") $rcv_date_cond=" and b.transaction_date<='$to_date'";
		}

		$issue_qnty_arr=sql_select("select b.recv_trans_id, b.issue_qnty from  inv_transaction a,  inv_mrr_wise_issue_details b where a.id=b.issue_trans_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type in(2,3,6) and a.item_category in(5,6,7,23) $mrr_date_cond");
		$mrr_issue_qnty_arr=array();
		foreach($issue_qnty_arr as $row)
		{
			$mrr_issue_qnty_arr[$row[csf("recv_trans_id")]]+=$row[csf("issue_qnty")];
		}



		if($db_type==0)
		{

			$sql="select a.id, a.company_id, a.supplier_id,a.re_order_label, a.item_code, a.item_category_id, a.item_group_id, a.unit_of_measure, a.item_description, a.sub_group_name,  group_concat(b.id) as trans_id, sum(b.cons_quantity) as cons_quantity, sum(b.cons_amount) as cons_amount, max(b.transaction_date) as transaction_date, b.batch_lot as lot, c.recv_number, c.receive_date,b.expire_date
			from product_details_master a, inv_transaction b, inv_receive_master c
			where a.id=b.prod_id and b.mst_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.entry_form=4 and b.transaction_type in(1) and b.company_id='$cbo_company_name' $sql_cond $rcv_date_cond
			group by a.id, a.company_id, a.supplier_id,a.re_order_label, a.item_code, a.item_category_id, a.item_group_id, a.unit_of_measure, a.item_description, a.sub_group_name, c.recv_number, c.receive_date, b.batch_lot,b.expire_date
			order by id, recv_number, receive_date";
		}
		else
		{
			$sql="select a.id, a.company_id, a.supplier_id,a.re_order_label, a.item_code, a.item_category_id, a.item_group_id, a.unit_of_measure, a.item_description, a.sub_group_name,  listagg(cast(b.id as varchar(4000)),',') within group(order by b.id) as trans_id, sum(b.cons_quantity) as cons_quantity, sum(b.cons_amount) as cons_amount, max(b.transaction_date) as transaction_date, b.batch_lot as lot, c.recv_number, c.receive_date,b.expire_date
			from product_details_master a, inv_transaction b, inv_receive_master c
			where a.id=b.prod_id and b.mst_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.entry_form=4 and b.transaction_type in(1) and b.company_id='$cbo_company_name' $sql_cond $rcv_date_cond
			group by a.id, a.company_id, a.supplier_id,a.re_order_label, a.item_code, a.item_category_id, a.item_group_id, a.unit_of_measure, a.item_description, a.sub_group_name, c.recv_number, c.receive_date, b.batch_lot,b.expire_date
			order by id, recv_number, receive_date";
		}

		//echo $sql;die;

		ob_start();
		?>
		<div style="width:1340px; border:1px solid white;">
			<table width="1320" cellpadding="0" cellspacing="0" id="caption"  align="left">
				<thead>
					<tr style="border:none;">
						<td colspan="16" align="center" class="form_caption" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td>
					</tr>
					<tr style="border:none;">
						<td colspan="16" class="form_caption" align="center" style="border:none; font-size:14px;">
						   <b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>
						</td>
					</tr>
				</thead>
			</table>
			<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="1320" rules="all" id="rpt_table_header"  align="left">
				<thead>
					<tr>
						<th width="30">SL</th>
						<th width="50">Prod. ID</th>
						<th width="110">Item Category</th>
						<th width="100">Item Group</th>
						<th width="100">Item Sub-group</th>
                        <th width="150">Item Description</th>
                        <th width="60">Lot</th>
                        <th width="60">UOM</th>
						<th width="100">Closing Stock</th>
						<th width="80">Avg. Rate</th>
						<th width="110">Stock Value</th>
                        <th width="110">MRR No.</th>
                        <th width="70">Receive Date</th>
                        <th width="70">Expire Date</th>
						<th width="60">Age (Days)</th>
						<th>DOH</th>
					</tr>
				</thead>
			</table>
			<div style="width:1340px; max-height:250px; overflow-y:scroll;" id="scroll_body">
			<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="1320" rules="all" align="left">
			<?

				$result = sql_select($sql);
				$i=1;
				foreach($result as $row)
				{

					if( $row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==4 )
						$stylecolor='color:#A61000;';
					else
						$stylecolor='color:#000000;';
					$ageOfDays = datediff("d",$row[csf("receive_date")],date("Y-m-d"));
					$daysOnHand = datediff("d",$row[csf("transaction_date")],date("Y-m-d"));

				   /* $issue_sql = "select
							sum(case when a.transaction_date<'".change_date_format($from_date,'yyyy-mm-dd')."' then a.cons_quantity else 0 end) as issue_total_opening,
							sum(case when a.transaction_type=2 and a.transaction_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."' then a.cons_quantity else 0 end) as issue,
							sum(case when a.transaction_type=3 and a.transaction_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."' then a.cons_quantity else 0 end) as receive_return
							from inv_transaction a, inv_issue_master c
							where a.mst_id=c.id and a.transaction_type in (2,3) and a.prod_id=".$row[csf("prod_id")]." and a.item_category in (5,6,7) and c.entry_form in (5,28) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
				if($db_type==2)
					{
					  $issue_sql = "select
							sum(case when a.transaction_date<'".change_date_format($from_date,'','',1)."' then a.cons_quantity else 0 end) as issue_total_opening,
							sum(case when a.transaction_type=2 and a.transaction_date  between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."' then a.cons_quantity else 0 end) as issue,
							sum(case when a.transaction_type=3 and a.transaction_date  between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."' then a.cons_quantity else 0 end) as receive_return
							from inv_transaction a, inv_issue_master c
							where a.mst_id=c.id and a.transaction_type in (2,3) and a.prod_id=".$row[csf("prod_id")]." and a.item_category in (5,6,7) and c.entry_form in (5,28) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
					}*/
					//echo $issue_sql;
					//$issue_result = sql_select($issue_sql);
					//$data_array[$row_p[csf("prod_id")]]['issue']

					//echo $row[csf("id")];
					//echo $issue_qty;
					//$totalIssue = $issue_result[0][csf('issue')]+$issue_result[0][csf('receive_return')];
					//$totalIssue = $issue_result[0][csf('issue')]+$data_array[$row[csf("id")]]['receive_return'];

					//$data_array[$row[csf("id")]]['receive_return'];
					  //$opening=$data_array[$row[csf("id")]]['rcv_total_opening']-$data_array[$row[csf("id")]]['iss_total_opening'];
					//echo $row[csf("id")];
					//echo $data_array[$row[csf("id")]]['purchase'];
					//echo $get_upto;die;
					//$data_array[$row_p[csf("prod_id")]]['item_transfer_issue']

					/*$issue_qty=$data_array[$row[csf("id")]]['issue'];
					$transfer_out_qty=$data_array[$row[csf("id")]]['item_transfer_issue'];
					$transfer_in_qty=$data_array[$row[csf("id")]]['item_transfer_receive'];

					$openingBalance = $data_array[$row[csf("id")]]['rcv_total_opening']-$data_array[$row[csf("id")]]['iss_total_opening'];
					$totalReceive = $data_array[$row[csf("id")]]['purchase']+$data_array[$row[csf("id")]]['issue_return'];//+$openingBalance
					$totalIssue = $data_array[$row[csf("id")]]['issue']+$data_array[$row[csf("id")]]['receive_return'];
					$closingStock=$openingBalance+$totalReceive-$totalIssue+$data_array[$row[csf("id")]]['item_transfer_receive']-$data_array[$row[csf("id")]]['item_transfer_issue'];
				*/

					$totalRcv=$row[csf("cons_quantity")];
					$totalIssue=$stockInHand=$stock_value=0;
					$trans_id_arr=array_unique(explode(",",$row[csf("trans_id")]));
					foreach($trans_id_arr as $tr_id)
					{
						$totalIssue+=$mrr_issue_qnty_arr[$tr_id];
					}

					$stockInHand=$totalRcv-$totalIssue;

					//$closingStock=$opening+$totalReceive-$totalIssue;
					if((($get_upto==1 && $ageOfDays>$txt_days) || ($get_upto==2 && $ageOfDays<$txt_days) || ($get_upto==3 && $ageOfDays>=$txt_days) || ($get_upto==4 && $ageOfDays<=$txt_days) || ($get_upto==5 && $ageOfDays==$txt_days) || $get_upto==0) && (($get_upto_qnty==1 && $stockInHand>$txt_qnty) || ($get_upto_qnty==2 && $stockInHand<$txt_qnty) || ($get_upto_qnty==3 && $stockInHand>=$txt_qnty) || ($get_upto_qnty==4 && $stockInHand<=$txt_qnty) || ($get_upto_qnty==5 && $stockInHand==$txt_qnty) || $get_upto_qnty==0))
					{
						$re_order_label = $row[csf('re_order_label')];
                		if($stockInHand <= $re_order_label){$bgcolor="red";}elseif($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
						// if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";

						/*$stockValue=$closingStock*$row[csf("avg_rate_per_unit")];
						$totalStockValue+=$stockValue;

						$pipeLine_qty=$wo_qty_arr[$row[csf("id")]]+$pi_qty_arr[$row[csf("id")]]-$data_array[$row[csf("id")]]['rcv_total_wo'];
						if($pipeLine_qty<0) $pipeLine_qty=0;
						$totalpipeLine_qty+=$pipeLine_qty;*/

						$avg_rate=$mrr_rate_arr[$row[csf("id")]];
						$stock_value=$stockInHand*$avg_rate;
						if($value_with==1)
						{
							if(number_format($stockInHand,2)>0.0000)
							{
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style=" word-wrap:break-word; <? echo $stylecolor; ?> "  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="30" align="center"><? echo $i; ?></td>
									<td width="50" align="center"><? echo $row[csf("id")]; ?></td>
									<td width="110"><p><? echo $item_category[$row[csf("item_category_id")]]; ?>&nbsp;</p></td>
									<td width="100"><p><? echo $itemgroupArr[$row[csf("item_group_id")]]; ?>&nbsp;</p></td>
									<td width="100"><p><? echo $row[csf("sub_group_name")]; ?>&nbsp;</p></td>
									<td width="150"><p><? echo $row[csf("item_description")]; ?>&nbsp;</p></td>
									<td width="60" align="center"><p><? echo $row[csf("lot")]; ?>&nbsp;</p></td>
									<td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?>&nbsp;</p></td>
									<td width="100" align="right" title="<? echo $totalIssue; ?>"><p><? echo number_format($stockInHand,3); $tot_stock+=$stockInHand; ?></p></td>
									<td width="80" align="right"><p><? if(number_format($stockInHand,3)>0) echo number_format($avg_rate,3) ; else echo "0.00";?></p></td>
									<td width="110" align="right"><p><? if(number_format($stockInHand,3)>0) echo number_format($stock_value,3); else echo "0.00"; $tot_stock_value+=$stock_value; ?></p></td>
									<td width="110"><p><? echo $row[csf("recv_number")]; ?></p></td>
									<td width="70" align="center"><p><? echo change_date_format($row[csf("receive_date")]); ?></p></td>
                                    <td width="70" align="center"><p><? echo change_date_format($row[csf("expire_date")]); ?></p></td>
									<td width="60" align="center"><p><? echo $ageOfDays; ?></p></td>
									<td align="center"><p><? echo $daysOnHand;?>&nbsp;</p></td>
								</tr>
								<?
								$i++;
							}
						}
						else
						{
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" style=" word-wrap:break-word; <? echo $stylecolor; ?> "  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30" align="center"><? echo $i; ?></td>
								<td width="50" align="center"><? echo $row[csf("id")]; ?></td>
								<td width="110"><p><? echo $item_category[$row[csf("item_category_id")]]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $itemgroupArr[$row[csf("item_group_id")]]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $row[csf("sub_group_name")]; ?>&nbsp;</p></td>
								<td width="150"><p><? echo $row[csf("item_description")]; ?>&nbsp;</p></td>
								<td width="60" align="center"><p><? echo $row[csf("lot")]; ?>&nbsp;</p></td>
								<td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?>&nbsp;</p></td>
								<td width="100" align="right" title="<? echo $totalIssue; ?>"><p><? echo number_format($stockInHand,3); $tot_stock+=$stockInHand; ?></p></td>
								<td width="80" align="right"><p><? if(number_format($stockInHand,3)>0) echo number_format($avg_rate,3); else echo "0.00";?></p></td>
								<td width="110" align="right"><p><? if(number_format($stockInHand,3)>0)echo number_format($stock_value,3); else echo "0.00"; $tot_stock_value+=$stock_value; ?></p></td>
								<td width="110"><p><? echo $row[csf("recv_number")]; ?></p></td>
								<td width="70" align="center"><p><? echo change_date_format($row[csf("receive_date")]); ?></p></td>
                                 <td width="70" align="center"><p><? echo change_date_format($row[csf("expire_date")]); ?></p></td>
								<td width="60" align="center"><p><? echo $ageOfDays; ?></p></td>
								<td align="center"><p><? echo $daysOnHand;?>&nbsp;</p></td>
							</tr>
							<?
							$i++;
						}

					}
				}
			?>
			</table>
			</div>
			<table width="1320" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer" align="left">
				<tfoot>
					<tr>
						<th width="30">&nbsp;</th>
						<th width="50">&nbsp;</th>
						<th width="110">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
                        <th width="150">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">Total:</th>
						<th width="100" id="value_tot_stock"><? echo number_format($tot_stock,2);?></th>
						<th width="80">&nbsp;</th>
						<th width="110" id="value_tot_stock_value"><? echo number_format($tot_stock_value,2);?></th>
                        <th width="110">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="70">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th>&nbsp;</th>
					</tr>
				</tfoot>
			</table>
        </div>
        <?
	}
	else if($report_type==3)
	{
		$company_cond="";
		if ($cbo_company_name>0) $company_cond =" and company_id=$cbo_company_name";
		$mrr_rate_sql=sql_select("select prod_id, sum(cons_quantity) as cons_quantiy, sum(cons_amount) as cons_amount from inv_transaction where status_active=1 and is_deleted=0 and item_category in(5,6,7,23) and transaction_type in(1,4,5) $company_cond group by prod_id");
		$mrr_rate_arr=array();
		foreach($mrr_rate_sql as $row)
		{
			$mrr_rate_arr[$row[csf("prod_id")]]=$row[csf("cons_amount")]/$row[csf("cons_quantiy")];
		}

		if ($cbo_company_name==0) $company_id =""; else $company_id =" and a.company_id='$cbo_company_name'";
		if ($cbo_item_category_id==0) $item_category_id=" and b.item_category_id in(5,6,7,23)"; else $item_category_id=" and b.item_category_id='$cbo_item_category_id'";
		if ($item_account_id==0) $item_account=""; else $item_account=" and b.id in ($item_account_id)";
		if ($item_group_id==0) $group_id=""; else $group_id=" and b.item_group_id='$item_group_id'";
		if ($cbo_store_name==0)  $store_id="";  else  $store_id=" and a.store_id=$cbo_store_name ";


		if($db_type==0)
		{
			$from_date=change_date_format($from_date,'yyyy-mm-dd');
			$to_date=change_date_format($to_date,'yyyy-mm-dd');
		}
		else if($db_type==2)
		{
			$from_date=change_date_format($from_date,'','',1);
			$to_date=change_date_format($to_date,'','',1);
		}
		else
		{
			$from_date=""; $to_date="";
		}
		$search_cond="";
		//if($value_with==0) $search_cond =""; else $search_cond= "  and b.current_stock>0";


		$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
		$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
		$storeArr = return_library_array("select id,store_name from lib_store_location where status_active=1 and is_deleted=0","id","store_name");
		$itemnameArr = return_library_array("select id,item_name from lib_item_creation where status_active=1 and is_deleted=0","id","item_name");
		$itemgroupArr = return_library_array("select id,item_name from  lib_item_group where status_active=1 and is_deleted=0","id","item_name");

		/*if($db_type==0)
		{
		$sql="Select a.prod_id,b.avg_rate_per_unit,b.id,b.store_id,b.item_category_id,b.item_group_id,b.sub_group_name,b.item_description,b.item_size,b.unit_of_measure,
			sum(case when a.transaction_date<'".change_date_format($from_date,'yyyy-mm-dd')."' then a.cons_quantity else 0 end) as rcv_total_opening,
			sum(case when a.transaction_type=1 and a.transaction_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."' then a.cons_quantity else 0 end) as purchase,
			sum(case when a.transaction_type=4 and a.transaction_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."' then a.cons_quantity else 0 end) as issue_return


			from inv_transaction a, product_details_master b, inv_receive_master c where a.prod_id=b.id and a.mst_id=c.id and a.transaction_type in (1,2,3,4) and b.item_category_id in (5,6,7) and  a.company_id=c.company_id and c.entry_form in (4,29) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_id $item_category_id $group_id $store_id $item_account group by a.prod_id  order by a.prod_id,b.store_id, b.item_category_id, b.item_group_id ASC";
		}
		if($db_type==2)
		{
		$sql="Select a.prod_id,b.avg_rate_per_unit,b.item_category_id,b.item_group_id,b.sub_group_name,b.item_description,b.item_size,b.unit_of_measure,
			sum(case when a.transaction_date<'".change_date_format($from_date,'','',1)."' then a.cons_quantity else 0 end) as rcv_total_opening,
			sum(case when a.transaction_type=1 and a.transaction_date  between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."' then a.cons_quantity else 0 end) as purchase,
			sum(case when a.transaction_type=4 and a.transaction_date  between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."' then a.cons_quantity else 0 end) as issue_return

			from inv_transaction a, product_details_master b, inv_receive_master c where a.prod_id=b.id and a.mst_id=c.id and a.transaction_type in (1,2,3,4) and b.item_category_id in (5,6,7) and  a.company_id=c.company_id and c.entry_form in (4,29) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_id $item_category_id $group_id $store_id $item_account group by a.prod_id,b.store_id,b.item_category_id,b.item_group_id,b.sub_group_name,b.item_description,b.item_size,b.unit_of_measure,b.avg_rate_per_unit order by a.prod_id, b.item_category_id,b.item_group_id ASC";
		}*/
		//sum(case when a.transaction_type in(6) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as iss_total_opening,
		//nd a.item_category in (6)//55

		$sql_loan_cond="";

		if ($cbo_company_name>0) $sql_loan_cond =" and a.company_id='$cbo_company_name'";
		if ($cbo_item_category_id==0) $sql_loan_cond.=" and a.item_category in(5,6,7,23)"; else $sql_loan_cond.=" and a.item_category='$cbo_item_category_id'";
		if ($item_account_id>0) $sql_loan_cond.=" and a.prod_id in ($item_account_id)";
		if ($cbo_store_name>0)  $sql_loan_cond.=" and a.store_id=$cbo_store_name ";

		if($from_date!="" && $to_date!="")
		{
			$sql_loan="Select a.prod_id as prod_id, sum(a.cons_quantity) as cons_quantity, 1 as type
			from inv_transaction a, inv_receive_master b
			where a.mst_id=b.id and a.transaction_type=1 and b.receive_purpose=5 and a.transaction_date between '".$from_date."' and '".$to_date."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_loan_cond  group by a.prod_id
			union all
			Select a.prod_id as prod_id, sum(a.cons_quantity) as cons_quantity, 2 as type
			from inv_transaction a, inv_issue_master b
			where a.mst_id=b.id and a.transaction_type=2 and b.issue_purpose=5 and a.transaction_date between '".$from_date."' and '".$to_date."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_loan_cond  group by a.prod_id
			order by prod_id ASC";
		}
		//echo $sql_loan;//die;
		$sql_loan_result=sql_select($sql_loan);
		$loan_data=array();
		foreach($sql_loan_result as $row)
		{
			if($row[csf("type")]==1)
			{
				$loan_data[$row[csf("prod_id")]]["loan_rcv_qnty"]=$row[csf("cons_quantity")];
			}
			else
			{
				$loan_data[$row[csf("prod_id")]]["loan_issue_qnty"]=$row[csf("cons_quantity")];
			}


		}
		//var_dump($loan_data);die;
		unset($sql_loan_result);


		if($db_type==0)
		{
			$sql="Select b.id as prod_id,b.re_order_label,
			sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
			sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as iss_total_opening,
			sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as rcv_total_opening_amt,
			sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as iss_total_opening_amt,
			sum(case when a.transaction_type=1 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as purchase,
                        sum(case when a.transaction_type in(1,4,5) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as purchase_amount,
                        sum(case when a.transaction_type in(2,3,6) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as issue_amount,
			sum(case when a.transaction_type=2 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as issue,
			sum(case when a.transaction_type in(4) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as issue_return,
			sum(case when a.transaction_type in(3) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as receive_return,

			sum(case when a.transaction_type in(6) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as item_transfer_issue,
			sum(case when a.transaction_type in(5) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as item_transfer_receive,
			sum(case when a.transaction_type in(1,4) and a.receive_basis in(1,2) then a.cons_quantity else 0 end) as rcv_total_wo,
			group_concat(a.batch_lot) as lot_no
			from inv_transaction a, product_details_master b
			where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and  b.is_deleted=0 and a.transaction_type in (1,2,3,4,5,6) and b.item_category_id in (5,6,7,23) and  a.item_category in (5,6,7,23) and a.order_id=0 $company_id $group_id $prod_cond $store_id $item_account $search_cond  group by b.id order by b.id ASC";
		}
		else if($db_type==2)
		{
			$sql="Select b.id as prod_id,b.re_order_label,
			sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
			sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as iss_total_opening,
			sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as rcv_total_opening_amt,
			sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as iss_total_opening_amt,
			sum(case when a.transaction_type=1 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as purchase,
                        sum(case when a.transaction_type in(1,4,5) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as purchase_amount,
                        sum(case when a.transaction_type in(2,3,6) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as issue_amount,
			sum(case when a.transaction_type=2 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as issue,
			sum(case when a.transaction_type in(4) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as issue_return,
			sum(case when a.transaction_type in(3) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as receive_return,

			sum(case when a.transaction_type in(6) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as item_transfer_issue,
			sum(case when a.transaction_type in(5) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as item_transfer_receive,
			sum(case when a.transaction_type in(1,4) and a.receive_basis in(1,2) then a.cons_quantity else 0 end) as rcv_total_wo,
			listagg(cast(a.batch_lot as varchar(4000)), ',') within group (order by a.batch_lot)  as lot_no
			from inv_transaction a, product_details_master b
			where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and  b.is_deleted=0 and a.transaction_type in (1,2,3,4,5,6) and b.item_category_id in (5,6,7,23) and  a.item_category in (5,6,7,23) and a.order_id=0 $company_id $group_id $prod_cond $store_id $item_account $search_cond  group by b.id order by b.id ASC";
		}
		//echo $sql;die;
		$trnasactionData = sql_select($sql);
		$data_array=array();
		foreach($trnasactionData as $row_p)
		{
			$data_array[$row_p[csf("prod_id")]]['rcv_total_opening']=$row_p[csf("rcv_total_opening")];
			$data_array[$row_p[csf("prod_id")]]['iss_total_opening']=$row_p[csf("iss_total_opening")];
			$data_array[$row_p[csf("prod_id")]]['rcv_total_opening_amt']=$row_p[csf("rcv_total_opening_amt")];
			$data_array[$row_p[csf("prod_id")]]['iss_total_opening_amt']=$row_p[csf("iss_total_opening_amt")];
			$data_array[$row_p[csf("prod_id")]]['purchase']=$row_p[csf("purchase")];
			$data_array[$row_p[csf("prod_id")]]['issue']=$row_p[csf("issue")];
			$data_array[$row_p[csf("prod_id")]]['issue_return']=$row_p[csf("issue_return")];
			$data_array[$row_p[csf("prod_id")]]['receive_return']=$row_p[csf("receive_return")];
			$data_array[$row_p[csf("prod_id")]]['item_transfer_issue']=$row_p[csf("item_transfer_issue")];
			$data_array[$row_p[csf("prod_id")]]['item_transfer_receive']=$row_p[csf("item_transfer_receive")];
			$data_array[$row_p[csf("prod_id")]]['rcv_total_wo']=$row_p[csf("rcv_total_wo")];
			$data_array[$row_p[csf("prod_id")]]['lot_no']=implode(",",array_unique(explode(",",$row_p[csf("lot_no")])));
                        $data_array[$row_p[csf("prod_id")]]['purchase_amount']=$row_p[csf("purchase_amount")];
                        $data_array[$row_p[csf("prod_id")]]['issue_amount']=$row_p[csf("issue_amount")];
		} //var_dump($data_array);die;
		//print_r($data_array);die;
		//echo $data_array[4103]['rcv_total_wo'].Fuad;
		$returnRes_date="select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where is_deleted=0 and status_active=1 and item_category in(5,6,7,23) group by prod_id";
		$result_returnRes_date = sql_select($returnRes_date);
		foreach($result_returnRes_date as $row)
		{
			$date_array[$row[csf("prod_id")]]['min_date']=$row[csf("min_date")];
			$date_array[$row[csf("prod_id")]]['max_date']=$row[csf("max_date")];
		}


		$wo_qty_arr=return_library_array("select b.item_id, sum(b.supplier_order_quantity) as qty from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.item_category in (5,6,7,23) and a.pay_mode<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by item_id","item_id","qty");

		$pi_qty_arr=return_library_array("select b.item_prod_id, sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id in (5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by item_prod_id","item_prod_id","qty");
		$i=1;
		ob_start();
		?>
		<div align="center" style="height:auto; width:auto; margin:0 auto; padding:0; width:2750px">
			<table width="2750px" cellpadding="0" cellspacing="0" id="caption" align="center">
				<thead>
					<tr style="border:none;">
						<td colspan="20" align="center" class="form_caption" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td>
					</tr>
					<tr style="border:none;">
						<td colspan="20" class="form_caption" align="center" style="border:none; font-size:14px;">
						   <b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="20" align="center" class="form_caption" style="border:none;font-size:12px; font-weight:bold">
							<? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date,'dd-mm-yyyy')." To : ".change_date_format($to_date,'dd-mm-yyyy')."" ;?>
						</td>
					</tr>
				</thead>
			</table>
			<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="2750" rules="all" id="rpt_table_header">
				<thead>
					<tr>
						<th rowspan="2" width="50">SL</th>
						<th colspan="8">Description</th>
						<th rowspan="2" width="100"> Opneing Rate</th>
                        <th rowspan="2" width="100">Opening Stock</th>
                        <th rowspan="2" width="110">Opening Value</th>
						<th colspan="6">Receive</th>
						<th colspan="6">Issue</th>
						<th rowspan="2" width="100">Closing Stock</th>
						<th rowspan="2" width="80">Avg. Rate</th>
						<th rowspan="2" width="120">Stock Value</th>
						<th rowspan="2" width="60">DOH</th>
						<th rowspan="2" width="100">Pipe Line</th>
						<th rowspan="2" width="120">Lot</th>
					</tr>
					<tr>
						<th width="60">Prod. ID</th>
						<th width="70">Item Code</th>
						<th width="100">Item Category</th>
						<th width="100">Item Group</th>
						<th width="100">Item Sub-group</th>
						<th width="180">Item Description</th>
						<th width="70">Item Size</th>
						<th width="60">UOM</th>
						<th width="80">Purchase</th>
                        <th width="80">Loan receive</th>
						<th width="80">Transfer In</th>
						<th width="80">Issue Return</th>
						<th width="100">Total Received</th>
                        <th width="100">Received Value</th>
						<th width="80">Issue</th>
                        <th width="80">Loan Issue</th>
						<th width="80">Transfer Out</th>
						<th width="80">Receive Return</th>
						<th>Total Issue</th>
                        <th>Issue Value</th>
					</tr>
				</thead>
			</table>
			<div style="width:2750px; max-height:250px; overflow-y:scroll; overflow-x:hidden;" id="scroll_body">
			<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="2730" rules="all" align="left">
			<?
			 	$sql="select b.id, b.item_code, b.item_category_id,b.item_group_id,b.unit_of_measure,b.item_description,b.sub_group_name,b.re_order_label, b.current_stock,avg_rate_per_unit from product_details_master b where b.status_active=1 and b.is_deleted=0 and b.company_id='$cbo_company_name' $item_category_id $group_id $prod_cond $item_account $search_cond order by b.id";
				$result = sql_select($sql);
				foreach($result as $row)
				{

					// if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
					if( $row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==4 )
						$stylecolor='color:#A61000;';
					else
						$stylecolor='color:#000000;';
					$ageOfDays = datediff("d",$date_array[$row[csf("id")]]['min_date'],date("Y-m-d"));
					$daysOnHand = datediff("d",$date_array[$row[csf("id")]]['max_date'],date("Y-m-d"));


					$transfer_out_qty=$data_array[$row[csf("id")]]['item_transfer_issue'];
					$transfer_in_qty=$data_array[$row[csf("id")]]['item_transfer_receive'];

					$openingBalance = $data_array[$row[csf("id")]]['rcv_total_opening']-$data_array[$row[csf("id")]]['iss_total_opening'];
                    $openingBalanceValue=$openingRate=0;
                    if($data_array[$row[csf("id")]]['rcv_total_opening'] >0)
                    {
                         $openingRate = $data_array[$row[csf("id")]]['rcv_total_opening_amt']/$data_array[$row[csf("id")]]['rcv_total_opening'];
                    }
                    $openingBalanceValue = $openingBalance*$openingRate;

					//$openingBalanceValue = $data_array[$row[csf("id")]]['rcv_total_opening_amt']-$data_array[$row[csf("id")]]['iss_total_opening_amt'];
					//$openingRate=$openingBalanceValue/$openingBalance;

					$purchase_qnty=$data_array[$row[csf("id")]]['purchase']-$loan_data[$row[csf("id")]]["loan_rcv_qnty"];
					$loan_receive=$loan_data[$row[csf("id")]]["loan_rcv_qnty"];
					$issue_qnty=$data_array[$row[csf("id")]]['issue']-$loan_data[$row[csf("id")]]["loan_issue_qnty"];
					$loan_issue=$loan_data[$row[csf("id")]]["loan_issue_qnty"];

					$totalReceive = $purchase_qnty+$loan_receive+$data_array[$row[csf("id")]]['issue_return']+$transfer_in_qty;//+$openingBalance
					$totalIssue = $issue_qnty+$loan_issue+$data_array[$row[csf("id")]]['receive_return']+$transfer_out_qty;
					$closingStock=$openingBalance+$totalReceive-$totalIssue;
					//$closingStock=$opening+$totalReceive-$totalIssue;
					if((($get_upto==1 && $ageOfDays>$txt_days) || ($get_upto==2 && $ageOfDays<$txt_days) || ($get_upto==3 && $ageOfDays>=$txt_days) || ($get_upto==4 && $ageOfDays<=$txt_days) || ($get_upto==5 && $ageOfDays==$txt_days) || $get_upto==0) && (($get_upto_qnty==1 && $closingStock>$txt_qnty) || ($get_upto_qnty==2 && $closingStock<$txt_qnty) || ($get_upto_qnty==3 && $closingStock>=$txt_qnty) || ($get_upto_qnty==4 && $closingStock<=$txt_qnty) || ($get_upto_qnty==5 && $closingStock==$txt_qnty) || $get_upto_qnty==0))
					{
						$avg_rate=$mrr_rate_arr[$row[csf("id")]];
						$stockValue=$closingStock*$avg_rate;


						$pipeLine_qty=$wo_qty_arr[$row[csf("id")]]+$pi_qty_arr[$row[csf("id")]]-$data_array[$row[csf("id")]]['rcv_total_wo'];
						if($pipeLine_qty<0) $pipeLine_qty=0;
						$re_order_label = $row[csf('re_order_label')];
                		if($closingStock <= $re_order_label){$bgcolor="red";}elseif($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
						if($value_with==1)
						{
							if(number_format($closingStock,3)>0.000 || number_format($openingBalance,3)>0.000)
							{

							?>
							<tr bgcolor="<? echo $bgcolor; ?>" style=" word-wrap:break-word; <? echo $stylecolor; ?> "  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="50" align="center"><? echo $i; ?></td>
								<td width="60"><? echo $row[csf("id")]; ?></td>
								<td width="70"><p><? echo $row[csf("item_code")]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $item_category[$row[csf("item_category_id")]]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $itemgroupArr[$row[csf("item_group_id")]]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $row[csf("sub_group_name")]; ?>&nbsp;</p></td>
								<td width="180"><p><? echo $row[csf("item_description")]; ?>&nbsp;</p></td>
								<td width="70"><p><? echo $row[csf("item_size")]; ?>&nbsp;</p></td>
								<td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?>&nbsp;</p></td>
								<td width="100" align="right"><p><? echo number_format($openingRate,3); ?></p></td>
                                <td width="100" align="right"><p><? echo number_format($openingBalance,3); $tot_open_bl+=$openingBalance; ?></p></td>
                                <td width="110" align="right"><p><? echo number_format($openingBalanceValue,3); $tot_open_bl_amt+=$openingBalanceValue; ?></p></td>
								<td width="80" align="right"><p><? echo number_format($purchase_qnty,3); $tot_purchase+=$purchase_qnty; ?></p></td>
                                <td width="80" align="right"><p><? echo number_format($loan_receive,3); $tot_loan_rcv+=$loan_receive; ?></p></td>
								<td width="80" align="right"><p><? echo number_format($transfer_in_qty,3); $tot_transfer_in_qty+=$transfer_in_qty; ?></p></td>
								<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['issue_return'],3); $tot_issue_return+=$data_array[$row[csf("id")]]['issue_return'];//$row[csf("issue_return")]; ?></p></td>
                                                                <td width="100" align="right"><p><? echo number_format($totalReceive,3); $tot_total_receive+=$totalReceive; ?></p></td>
                                                                <td width="100" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['purchase_amount'],3); $tot_purchase_amount+=$data_array[$row[csf("id")]]['purchase_amount'];?></p></td>
								<td width="80" align="right"><p><? echo number_format($issue_qnty,3); $tot_issue+=$issue_qnty; ?></p></td>
                                <td width="80" align="right"><p><? echo number_format($loan_issue,3); $tot_loan_issue+=$loan_issue; ?></p></td>
								<td width="80" align="right"><p><? echo number_format($transfer_out_qty,3); $tot_transfer_out_qty+=$transfer_out_qty; ?></p></td>
								<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['receive_return'],3); $tot_rec_return+=$data_array[$row[csf("id")]]['receive_return'];//$issue_result[0][csf('receive_return')]; ?></p></td>
								<td width="100" align="right"><p><? echo number_format($totalIssue,3); $tot_total_issue+=$totalIssue; ?></p></td>
                                                                <td width="100" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['issue_amount'],3); $tot_issue_amount+=$data_array[$row[csf("id")]]['issue_amount'];?></p></td>
								<td width="100" align="right"><p><? echo number_format($closingStock,3); $tot_closing_stock+=$closingStock; ?></p></td>
								<td width="80" align="right"><p><? if(number_format($closingStock,3)>0) echo number_format($avg_rate,2); else echo "0.00"; //number_format($row[csf("avg_rate")],2); $tot_avg_rate+=$row[csf("avg_rate")]; ?></p></td>
								<td width="120" align="right"><p><? if(number_format($closingStock,3)>0) echo number_format($stockValue,2); else echo "0.00"; $tot_stock_value+=$stockValue; ?></p></td>
								<td width="60" align="center"><p><? echo $daysOnHand;//$daysOnHand; ?>&nbsp;</p></td>
								<td width="100" align="right" title="<? echo $wo_qty_arr[$row[csf("id")]]."==".$pi_qty_arr[$row[csf("id")]]; ?>"><a href="##" onclick="fnc_pipeLine_details('<? echo $row[csf("id")];?>','pipe_line_popup')"><p><? echo number_format($pipeLine_qty,2); ?></p></a></td>
								<td><p><? echo $data_array[$row[csf("id")]]['lot_no']; ?>&nbsp;</p></td>
							</tr>
							<?
							$i++;
							$totalStockValue+=$stockValue;
							$totalpipeLine_qty+=$pipeLine_qty;
						}
						}
						else
						{
							if((number_format($openingBalance,3)>0.000) || (number_format($totalReceive,3)>0.000) || (number_format($totalIssue,3)>0.000) || (number_format($closingStock,3)>0.000) )
							{
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" style=" word-wrap:break-word; <? echo $stylecolor; ?> "  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="50" align="center"><? echo $i; ?></td>
								<td width="60"><? echo $row[csf("id")]; ?></td>
								<td width="70"><p><? echo $row[csf("item_code")]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $item_category[$row[csf("item_category_id")]]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $itemgroupArr[$row[csf("item_group_id")]]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $row[csf("sub_group_name")]; ?>&nbsp;</p></td>
								<td width="180"><p><? echo $row[csf("item_description")]; ?>&nbsp;</p></td>
								<td width="70"><p><? echo $row[csf("item_size")]; ?>&nbsp;</p></td>
								<td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?>&nbsp;</p></td>
								<td width="100" align="right"><p><? echo number_format($openingRate,3); ?></p></td>
                                <td width="100" align="right"><p><? echo number_format($openingBalance,3); $tot_open_bl+=$openingBalance; ?></p></td>
                                <td width="110" align="right"><p><? echo number_format($openingBalanceValue,3); $tot_open_bl_amt+=$openingBalanceValue; ?></p></td>
								<td width="80" align="right"><p><? echo number_format($purchase_qnty,3); $tot_purchase+=$purchase_qnty; ?></p></td>
                                <td width="80" align="right"><p><? echo number_format($loan_receive,3); $tot_loan_rcv+=$loan_receive; ?></p></td>
								<td width="80" align="right"><p><? echo number_format($transfer_in_qty,3); $tot_transfer_in_qty+=$transfer_in_qty; ?></p></td>
								<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['issue_return'],3); $tot_issue_return+=$data_array[$row[csf("id")]]['issue_return'];//$row[csf("issue_return")]; ?></p></td>
								<td width="100" align="right"><p><? echo number_format($totalReceive,3); $tot_total_receive+=$totalReceive; ?></p></td>
                                                                <td width="100" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['purchase_amount'],3);$tot_purchase_amount+=$data_array[$row[csf("id")]]['purchase_amount'];?></p></td>
                                                                <td width="80" align="right"><p><? echo number_format($issue_qnty,3); $tot_issue+=$issue_qnty; ?></p></td>
                                <td width="80" align="right"><p><? echo number_format($loan_issue,3); $tot_loan_issue+=$loan_issue; ?></p></td>
								<td width="80" align="right"><p><? echo number_format($transfer_out_qty,3); $tot_transfer_out_qty+=$transfer_out_qty; ?></p></td>
								<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['receive_return'],3); $tot_rec_return+=$data_array[$row[csf("id")]]['receive_return'];//$issue_result[0][csf('receive_return')]; ?></p></td>
								<td width="100" align="right"><p><? echo number_format($totalIssue,3); $tot_total_issue+=$totalIssue; ?></p></td>
                                                                <td width="100" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['issue_amount'],3); $tot_issue_amount+=$data_array[$row[csf("id")]]['issue_amount'];?></p></td>
								<td width="100" align="right"><p><? echo number_format($closingStock,3); $tot_closing_stock+=$closingStock; ?></p></td>
								<td width="80" align="right"><p><? if(number_format($closingStock,3)>0) echo number_format($avg_rate,2); else echo "0.00"; //number_format($row[csf("avg_rate")],2); $tot_avg_rate+=$row[csf("avg_rate")]; ?></p></td>
								<td width="120" align="right"><p><? if(number_format($closingStock,3)>0) echo number_format($stockValue,2); else echo "0.00"; $tot_stock_value+=$stockValue; ?></p></td>
								<td width="60" align="center"><p><? echo $daysOnHand;//$daysOnHand; ?>&nbsp;</p></td>
								<td width="100" align="right" title="<? echo $wo_qty_arr[$row[csf("id")]]."==".$pi_qty_arr[$row[csf("id")]]; ?>"><a href="##" onclick="fnc_pipeLine_details('<? echo $row[csf("id")];?>','pipe_line_popup')"><p><? echo number_format($pipeLine_qty,2); ?></p></a></td>
								<td><p><? echo $data_array[$row[csf("id")]]['lot_no']; ?>&nbsp;</p></td>
							</tr>
							<?
							$i++;
							$totalStockValue+=$stockValue;
							$totalpipeLine_qty+=$pipeLine_qty;
						}
						}


					}
				}
			?>
			</table>
			</div>
			<table width="2750" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer">
				<tfoot>
					<tr>
						<th width="50">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="180">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="100">Total:</th>
                        <th width="100" align="right" id="value_tot_open_bl"><? echo number_format($tot_open_bl,3); ?></th>
                        <th width="110" align="right" id="value_tot_open_bl_amt"><? echo number_format($tot_open_bl_amt,3); ?></th>
						<th width="80" align="right" id="value_tot_purchase"><? echo number_format($tot_purchase,3); ?></th>
                        <th width="80" align="right" id="value_tot_loan_rcv"><? echo number_format($tot_loan_rcv,3); ?></th>
						<th width="80" align="right" id="value_tot_transfer_in_qty"><? echo number_format($tot_transfer_in_qty,3); ?></th>
						<th width="80" align="right" id="value_tot_issue_return"><? echo number_format($tot_issue_return,3); ?></th>
						<th width="100" align="right" id="value_tot_total_receive"><? echo number_format($tot_total_receive,3); ?></th>
                                                <th width="100"><? echo number_format($tot_purchase_amount,3);?></th>
                                                <th width="80" align="right" id="value_tot_issue"><? echo number_format($tot_issue,3); ?></th>
                        <th width="80" align="right" id="value_tot_loan_issue"><? echo number_format($tot_loan_issue,3); ?></th>
						<th width="80" align="right" id="value_tot_transfer_out_qty"><? echo number_format($tot_transfer_out_qty,3); ?></th>
						<th width="80" align="right" id="value_tot_rec_return"><? echo number_format($tot_rec_return,3); ?></th>
						<th width="100" align="right" id="value_tot_total_issue"><? echo number_format($tot_total_issue,3); ?></th>
						<th width="100"><? echo number_format($tot_issue_amount,3);?></th>
                                                <th width="100" align="right" id="value_tot_closing_stock"><? echo number_format($tot_closing_stock,3); ?></th>
						<th width="80">&nbsp;</th>
						<th width="120" align="right" id="value_tot_stock_value"><p><? echo number_format($tot_stock_value,2); ?></p></th>
						<th width="60">&nbsp;</th>
						<th width="100" align="right" id="value_totalpipeLine_qty"><? echo number_format($totalpipeLine_qty,2); ?></th>
						<th>&nbsp;</th>
					</tr>
				</tfoot>
			</table>
        </div>
        <?

	}
	else if($report_type==4)
	{
		$company_cond="";
		if ($cbo_company_name>0) $company_cond =" and company_id=$cbo_company_name";
		$mrr_rate_sql=sql_select("select prod_id, sum(cons_quantity) as cons_quantiy, sum(cons_amount) as cons_amount from inv_transaction where status_active=1 and is_deleted=0 and item_category in(5,6,7,23) and transaction_type in(1,4,5) $company_cond group by prod_id");
		$mrr_rate_arr=array();
		foreach($mrr_rate_sql as $row)
		{
			$mrr_rate_arr[$row[csf("prod_id")]]=$row[csf("cons_amount")]/$row[csf("cons_quantiy")];
		}
		if ($cbo_company_name==0) $company_id =""; else $company_id =" and a.company_id='$cbo_company_name'";
		if ($cbo_item_category_id==0) $item_category_id=" and b.item_category_id in(5,6,7,23)"; else $item_category_id=" and b.item_category_id='$cbo_item_category_id'";
		if ($item_account_id==0) $item_account=""; else $item_account=" and b.id in ($item_account_id)";
		if ($item_group_id==0) $group_id=""; else $group_id=" and b.item_group_id='$item_group_id'";
		if ($cbo_store_name==0)  $store_id="";  else  $store_id=" and a.store_id=$cbo_store_name ";

		if($db_type==0)
		{
			$from_date=change_date_format($from_date,'yyyy-mm-dd');
			$to_date=change_date_format($to_date,'yyyy-mm-dd');
		}
		else if($db_type==2)
		{
			$from_date=change_date_format($from_date,'','',1);
			$to_date=change_date_format($to_date,'','',1);
		}
		else
		{
			$from_date=""; $to_date="";
		}
		$search_cond="";
		//if($value_with==0) $search_cond =""; else $search_cond= "  and b.current_stock>0";


		$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
		$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
		$storeArr = return_library_array("select id,store_name from lib_store_location where status_active=1 and is_deleted=0","id","store_name");
		$itemnameArr = return_library_array("select id,item_name from lib_item_creation where status_active=1 and is_deleted=0","id","item_name");
		$itemgroupArr = return_library_array("select id,item_name from  lib_item_group where status_active=1 and is_deleted=0","id","item_name");

		/*if($db_type==0)
		{
		$sql="Select a.prod_id,b.avg_rate_per_unit,b.id,b.store_id,b.item_category_id,b.item_group_id,b.sub_group_name,b.item_description,b.item_size,b.unit_of_measure,
			sum(case when a.transaction_date<'".change_date_format($from_date,'yyyy-mm-dd')."' then a.cons_quantity else 0 end) as rcv_total_opening,
			sum(case when a.transaction_type=1 and a.transaction_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."' then a.cons_quantity else 0 end) as purchase,
			sum(case when a.transaction_type=4 and a.transaction_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."' then a.cons_quantity else 0 end) as issue_return


			from inv_transaction a, product_details_master b, inv_receive_master c where a.prod_id=b.id and a.mst_id=c.id and a.transaction_type in (1,2,3,4) and b.item_category_id in (5,6,7) and  a.company_id=c.company_id and c.entry_form in (4,29) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_id $item_category_id $group_id $store_id $item_account group by a.prod_id  order by a.prod_id,b.store_id, b.item_category_id, b.item_group_id ASC";
		}
		if($db_type==2)
		{
		$sql="Select a.prod_id,b.avg_rate_per_unit,b.item_category_id,b.item_group_id,b.sub_group_name,b.item_description,b.item_size,b.unit_of_measure,
			sum(case when a.transaction_date<'".change_date_format($from_date,'','',1)."' then a.cons_quantity else 0 end) as rcv_total_opening,
			sum(case when a.transaction_type=1 and a.transaction_date  between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."' then a.cons_quantity else 0 end) as purchase,
			sum(case when a.transaction_type=4 and a.transaction_date  between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."' then a.cons_quantity else 0 end) as issue_return

			from inv_transaction a, product_details_master b, inv_receive_master c where a.prod_id=b.id and a.mst_id=c.id and a.transaction_type in (1,2,3,4) and b.item_category_id in (5,6,7) and  a.company_id=c.company_id and c.entry_form in (4,29) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_id $item_category_id $group_id $store_id $item_account group by a.prod_id,b.store_id,b.item_category_id,b.item_group_id,b.sub_group_name,b.item_description,b.item_size,b.unit_of_measure,b.avg_rate_per_unit order by a.prod_id, b.item_category_id,b.item_group_id ASC";
		}*/
		//sum(case when a.transaction_type in(6) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as iss_total_opening,
		//nd a.item_category in (6)//55

		if($db_type==0)
		{
			$sql="Select b.id as prod_id,
			sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
			sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as iss_total_opening,
			sum(case when a.transaction_type=1 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as purchase,
			sum(case when a.transaction_type=2 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as issue,
			sum(case when a.transaction_type in(4) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as issue_return,
			sum(case when a.transaction_type in(3) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as receive_return,

			sum(case when a.transaction_type in(6) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as item_transfer_issue,
			sum(case when a.transaction_type in(5) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as item_transfer_receive,
			sum(case when a.transaction_type in(1,4) and a.receive_basis in(1,2) then a.cons_quantity else 0 end) as rcv_total_wo,
			group_concat(a.batch_lot) as lot_no
			from inv_transaction a, product_details_master b
			where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and  b.is_deleted=0 and a.transaction_type in (1,2,3,4,5,6) and b.item_category_id in (5,6,7,23) and  a.item_category in (5,6,7,23) and a.order_id=0 $company_id $group_id $prod_cond $store_id $item_account $search_cond  group by b.id order by b.id ASC";
		}
		else if($db_type==2)
		{
			$sql="Select b.id as prod_id,
			sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
			sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as iss_total_opening,
			sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as rcv_total_opening_amt,
			sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as iss_total_opening_amt,
			sum(case when a.transaction_type=1 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as purchase,
			sum(case when a.transaction_type=2 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as issue,
			sum(case when a.transaction_type in(4) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as issue_return,
			sum(case when a.transaction_type in(3) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as receive_return,

			sum(case when a.transaction_type in(6) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as item_transfer_issue,
			sum(case when a.transaction_type in(5) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as item_transfer_receive,
			sum(case when a.transaction_type in(1,4) and a.receive_basis in(1,2) then a.cons_quantity else 0 end) as rcv_total_wo,
			listagg(cast(a.batch_lot as varchar(4000)), ',') within group (order by a.batch_lot)  as lot_no
			from inv_transaction a, product_details_master b
			where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and  b.is_deleted=0 and a.transaction_type in (1,2,3,4,5,6) and b.item_category_id in (5,6,7,23) and  a.item_category in (5,6,7,23) and a.order_id=0 $company_id $group_id $prod_cond $store_id $item_account $search_cond  group by b.id order by b.id ASC";
		}
		//echo $sql;die;
		$trnasactionData = sql_select($sql);
		$data_array=array();
		foreach($trnasactionData as $row_p)
		{
			$data_array[$row_p[csf("prod_id")]]['rcv_total_opening']=$row_p[csf("rcv_total_opening")];
			$data_array[$row_p[csf("prod_id")]]['iss_total_opening']=$row_p[csf("iss_total_opening")];
			$data_array[$row_p[csf("prod_id")]]['rcv_total_opening_amt']=$row_p[csf("rcv_total_opening_amt")];
			$data_array[$row_p[csf("prod_id")]]['iss_total_opening_amt']=$row_p[csf("iss_total_opening_amt")];
			$data_array[$row_p[csf("prod_id")]]['purchase']=$row_p[csf("purchase")];
			$data_array[$row_p[csf("prod_id")]]['issue']=$row_p[csf("issue")];
			$data_array[$row_p[csf("prod_id")]]['issue_return']=$row_p[csf("issue_return")];
			$data_array[$row_p[csf("prod_id")]]['receive_return']=$row_p[csf("receive_return")];
			$data_array[$row_p[csf("prod_id")]]['item_transfer_issue']=$row_p[csf("item_transfer_issue")];
			$data_array[$row_p[csf("prod_id")]]['item_transfer_receive']=$row_p[csf("item_transfer_receive")];
			$data_array[$row_p[csf("prod_id")]]['rcv_total_wo']=$row_p[csf("rcv_total_wo")];
			$data_array[$row_p[csf("prod_id")]]['lot_no']=implode(",",array_unique(explode(",",$row_p[csf("lot_no")])));
		} //var_dump($data_array);die;
		//print_r($data_array);die;
		//echo $data_array[4103]['rcv_total_wo'].Fuad;
		$returnRes_date="select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where is_deleted=0 and status_active=1 and item_category in(5,6,7,23) group by prod_id";
		$result_returnRes_date = sql_select($returnRes_date);
		foreach($result_returnRes_date as $row)
		{
			$date_array[$row[csf("prod_id")]]['min_date']=$row[csf("min_date")];
			$date_array[$row[csf("prod_id")]]['max_date']=$row[csf("max_date")];
		}


		$wo_qty_arr=return_library_array("select b.item_id, sum(b.supplier_order_quantity) as qty from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.item_category in (5,6,7,23) and a.pay_mode<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by item_id","item_id","qty");

		$pi_qty_arr=return_library_array("select b.item_prod_id, sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id in (5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by item_prod_id","item_prod_id","qty");
		$i=1;
		ob_start();
		?>
		<div align="center" style="height:auto; width:auto; margin:0 auto; padding:0; width:2388">
			<table width="2388px" cellpadding="0" cellspacing="0" id="caption" align="center">
				<thead>
					<tr style="border:none;">
						<td colspan="20" align="center" class="form_caption" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td>
					</tr>
					<tr style="border:none;">
						<td colspan="20" class="form_caption" align="center" style="border:none; font-size:14px;">
						   <b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="20" align="center" class="form_caption" style="border:none;font-size:12px; font-weight:bold">
							<? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date,'dd-mm-yyyy')." To : ".change_date_format($to_date,'dd-mm-yyyy')."" ;?>
						</td>
					</tr>
				</thead>
			</table>
			<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="2388" rules="all" id="rpt_table_header">
				<thead>
					<tr>
						<th rowspan="2" width="50">SL</th>
						<th colspan="8">Description</th>
						<th rowspan="2" width="100"> Opneing Rate</th>
                        <th rowspan="2" width="100">Opening Stock</th>
                        <th rowspan="2" width="110">Opening Value</th>
						<th colspan="4">Receive</th>
						<th colspan="4">Issue</th>
						<th rowspan="2" width="100">Closing Stock</th>
						<th rowspan="2" width="80">Avg. Rate</th>
						<th rowspan="2" width="120">Stock Value</th>
						<th rowspan="2" width="60">DOH</th>
						<th rowspan="2" width="100">Pipe Line</th>
						<th rowspan="2" width="120">Lot</th>
					</tr>
					<tr>
						<th width="60">Prod. ID</th>
						<th width="70">Item Code</th>
						<th width="100">Item Category</th>
						<th width="100">Item Group</th>
						<th width="100">Item Sub-group</th>
						<th width="180">Item Description</th>
						<th width="70">Item Size</th>
						<th width="60">UOM</th>
						<th width="80">Purchase</th>
						<th width="80">Transfer In</th>
						<th width="80">Issue Return</th>
						<th width="100">Total Received</th>
						<th width="80">Issue</th>
						<th width="80">Transfer Out</th>
						<th width="80">Receive Return</th>
						<th>Total Issue</th>
					</tr>
				</thead>
			</table>
			<div style="width:2388px; max-height:250px; overflow-y:scroll; overflow-x:hidden;" id="scroll_body">
			<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="2370" rules="all" align="left">
			<?
			 	$sql="select b.id, b.item_code, b.re_order_label, b.item_category_id,b.item_group_id,b.unit_of_measure,b.item_description,b.sub_group_name, b.current_stock,avg_rate_per_unit from product_details_master b where b.status_active=1 and b.is_deleted=0 and b.company_id='$cbo_company_name' $item_category_id $group_id $prod_cond $item_account $search_cond order by b.id";
				$result = sql_select($sql);
				foreach($result as $row)
				{
					// if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
					if( $row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==4 )
						$stylecolor='color:#A61000;';
					else
						$stylecolor='color:#000000;';
					$ageOfDays = datediff("d",$date_array[$row[csf("id")]]['min_date'],date("Y-m-d"));
					$daysOnHand = datediff("d",$date_array[$row[csf("id")]]['max_date'],date("Y-m-d"));

					$issue_qty=$data_array[$row[csf("id")]]['issue'];
					$transfer_out_qty=$data_array[$row[csf("id")]]['item_transfer_issue'];
					$transfer_in_qty=$data_array[$row[csf("id")]]['item_transfer_receive'];

					$openingBalance = $data_array[$row[csf("id")]]['rcv_total_opening']-$data_array[$row[csf("id")]]['iss_total_opening'];
					$openingBalanceValue = $data_array[$row[csf("id")]]['rcv_total_opening_amt']-$data_array[$row[csf("id")]]['iss_total_opening_amt'];
					$openingRate=$openingBalanceValue/$openingBalance;
					$totalReceive = $data_array[$row[csf("id")]]['purchase']+$data_array[$row[csf("id")]]['issue_return']+$transfer_in_qty;//+$openingBalance
					$totalIssue = $data_array[$row[csf("id")]]['issue']+$data_array[$row[csf("id")]]['receive_return']+$transfer_out_qty;
					$closingStock=$openingBalance+$totalReceive-$totalIssue;
					//$closingStock=$opening+$totalReceive-$totalIssue;
					if((($get_upto==1 && $ageOfDays>$txt_days) || ($get_upto==2 && $ageOfDays<$txt_days) || ($get_upto==3 && $ageOfDays>=$txt_days) || ($get_upto==4 && $ageOfDays<=$txt_days) || ($get_upto==5 && $ageOfDays==$txt_days) || $get_upto==0) && (($get_upto_qnty==1 && $closingStock>$txt_qnty) || ($get_upto_qnty==2 && $closingStock<$txt_qnty) || ($get_upto_qnty==3 && $closingStock>=$txt_qnty) || ($get_upto_qnty==4 && $closingStock<=$txt_qnty) || ($get_upto_qnty==5 && $closingStock==$txt_qnty) || $get_upto_qnty==0))
					{
						$avg_rate=$mrr_rate_arr[$row[csf("id")]];
						$stockValue=$closingStock*$avg_rate;

						$re_order_label = $row[csf('re_order_label')];
                		if($closingStock <= $re_order_label){$bgcolor="red";}elseif($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}

						$pipeLine_qty=$wo_qty_arr[$row[csf("id")]]+$pi_qty_arr[$row[csf("id")]]-$data_array[$row[csf("id")]]['rcv_total_wo'];
						if($pipeLine_qty<0) $pipeLine_qty=0;
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" style=" word-wrap:break-word; <? echo $stylecolor; ?> "  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="50" align="center"><? echo $i; ?></td>
                            <td width="60"><? echo $row[csf("id")]; ?></td>
                            <td width="70"><p><? echo $row[csf("item_code")]; ?>&nbsp;</p></td>
                            <td width="100"><p><? echo $item_category[$row[csf("item_category_id")]]; ?>&nbsp;</p></td>
                            <td width="100"><p><? echo $itemgroupArr[$row[csf("item_group_id")]]; ?>&nbsp;</p></td>
                            <td width="100"><p><? echo $row[csf("sub_group_name")]; ?>&nbsp;</p></td>
                            <td width="180"><p><? echo $row[csf("item_description")]; ?>&nbsp;</p></td>
                            <td width="70"><p><? echo $row[csf("item_size")]; ?>&nbsp;</p></td>
                            <td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?>&nbsp;</p></td>
                            <td width="100" align="right"><p><? if($openingBalance) echo number_format($openingRate,3); else echo "0.000"; ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($openingBalance,3); $tot_open_bl+=$openingBalance; ?></p></td>
                            <td width="110" align="right"><p><? echo number_format($openingBalanceValue,3); $tot_open_bl_amt+=$openingBalanceValue; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['purchase'],3); $tot_purchase+=$data_array[$row[csf("id")]]['purchase']; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($transfer_in_qty,3); $tot_transfer_in_qty+=$transfer_in_qty; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['issue_return'],3); $tot_issue_return+=$data_array[$row[csf("id")]]['issue_return'];//$row[csf("issue_return")]; ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($totalReceive,3); $tot_total_receive+=$totalReceive; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($issue_qty,3); $tot_issue+=$issue_qty; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($transfer_out_qty,3); $tot_transfer_out_qty+=$transfer_out_qty; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['receive_return'],3); $tot_rec_return+=$data_array[$row[csf("id")]]['receive_return'];//$issue_result[0][csf('receive_return')]; ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($totalIssue,3); $tot_total_issue+=$totalIssue; ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($closingStock,3); $tot_closing_stock+=$closingStock; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($avg_rate,2); //number_format($row[csf("avg_rate")],2); $tot_avg_rate+=$row[csf("avg_rate")]; ?></p></td>
                            <td width="120" align="right"><p><? echo number_format($stockValue,2); $tot_stock_value+=$stockValue; ?></p></td>
                            <td width="60" align="center"><p><? echo $daysOnHand;//$daysOnHand; ?>&nbsp;</p></td>
                            <td width="100" align="right" title="<? echo $wo_qty_arr[$row[csf("id")]]."==".$pi_qty_arr[$row[csf("id")]]; ?>"><a href="##" onclick="fnc_pipeLine_details('<? echo $row[csf("id")];?>','pipe_line_popup')"><p><? echo number_format($pipeLine_qty,2); ?></p></a></td>
                            <td><p><? echo $data_array[$row[csf("id")]]['lot_no']; ?>&nbsp;</p></td>
                        </tr>
                        <?
                        $i++;
                        $totalStockValue+=$stockValue;
                        $totalpipeLine_qty+=$pipeLine_qty;

					}
				}
			?>
			</table>
			</div>
			<table width="2388" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer">
				<tfoot>
					<tr>
						<th width="50">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="180">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="100">Total:</th>
                        <th width="100" align="right" id="value_tot_open_bl"><? echo number_format($tot_open_bl,3); ?></th>
                        <th width="110" align="right" id="value_tot_open_bl_amt"><? echo number_format($tot_open_bl_amt,3); ?></th>
						<th width="80" align="right" id="value_tot_purchase"><? echo number_format($tot_purchase,3); ?></th>
						<th width="80" align="right" id="value_tot_transfer_in_qty"><? echo number_format($tot_transfer_in_qty,3); ?></th>
						<th width="80" align="right" id="value_tot_issue_return"><? echo number_format($tot_issue_return,3); ?></th>
						<th width="100" align="right" id="value_tot_total_receive"><? echo number_format($tot_total_receive,3); ?></th>
						<th width="80" align="right" id="value_tot_issue"><? echo number_format($tot_issue,3); ?></th>
						<th width="80" align="right" id="value_tot_transfer_out_qty"><? echo number_format($tot_transfer_out_qty,3); ?></th>
						<th width="80" align="right" id="value_tot_rec_return"><? echo number_format($tot_rec_return,3); ?></th>
						<th width="100" align="right" id="value_tot_total_issue"><? echo number_format($tot_total_issue,3); ?></th>
						<th width="100" align="right" id="value_tot_closing_stock"><? echo number_format($tot_closing_stock,3); ?></th>
						<th width="80">&nbsp;</th>
						<th width="120" align="right" id="value_tot_stock_value"><p><? echo number_format($tot_stock_value,2); ?></p></th>
						<th width="60">&nbsp;</th>
						<th width="100" align="right" id="value_totalpipeLine_qty"><? echo number_format($totalpipeLine_qty,2); ?></th>
						<th>&nbsp;</th>
					</tr>
				</tfoot>
			</table>
        </div>
        <?
	}
	else if($report_type==5)
	{
		$company_cond="";
		if ($cbo_company_name>0) $company_cond =" and company_id=$cbo_company_name";
		$mrr_rate_sql=sql_select("select prod_id, sum(cons_quantity) as cons_quantiy, sum(cons_amount) as cons_amount from inv_transaction where status_active=1 and is_deleted=0 and item_category in(5,6,7,23) and transaction_type in(1,4,5) $company_cond group by prod_id");
		$mrr_rate_arr=array();
		foreach($mrr_rate_sql as $row)
		{
			$mrr_rate_arr[$row[csf("prod_id")]]=$row[csf("cons_amount")]/$row[csf("cons_quantiy")];
		}
		if ($cbo_company_name==0) $company_id =""; else $company_id =" and a.company_id='$cbo_company_name'";
		if ($cbo_company_name==0) $company_cond =""; else $company_cond =" and b.company_id='$cbo_company_name'";
		if ($cbo_item_category_id==0) $item_category_id=" and b.item_category_id in(5,6,7,23)"; else $item_category_id=" and b.item_category_id='$cbo_item_category_id'";
		if ($item_account_id==0) $item_account=""; else $item_account=" and b.id in ($item_account_id)";
		if ($item_group_id==0) $group_id=""; else $group_id=" and b.item_group_id='$item_group_id'";
		if ($cbo_store_name==0)  $store_id="";  else  $store_id=" and a.store_id=$cbo_store_name ";

		if($db_type==0)
		{
			$from_date=change_date_format($from_date,'yyyy-mm-dd');
			$to_date=change_date_format($to_date,'yyyy-mm-dd');
		}
		else if($db_type==2)
		{
			$from_date=change_date_format($from_date,'','',1);
			$to_date=change_date_format($to_date,'','',1);
		}
		else
		{
			$from_date=""; $to_date="";
		}
		$search_cond="";
		//if($value_with==0) $search_cond =""; else $search_cond= "  and b.current_stock>0";


		$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");

		if($db_type==0)
		{
			$sql="Select a.expire_date, b.id as prod_id,
			sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
			sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as iss_total_opening,
			sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as rcv_total_opening_amt,
			sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as iss_total_opening_amt,
			sum(case when a.transaction_type=1 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as purchase,
			sum(case when a.transaction_type=2 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as issue,
			sum(case when a.transaction_type in(4) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as issue_return,
			sum(case when a.transaction_type in(3) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as receive_return,

			sum(case when a.transaction_type in(6) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as item_transfer_issue,
			sum(case when a.transaction_type in(5) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as item_transfer_receive,
			sum(case when a.transaction_type in(1,4) and a.receive_basis in(1,2) then a.cons_quantity else 0 end) as rcv_total_wo,
			group_concat(a.batch_lot) as lot_no
			from inv_transaction a, product_details_master b
			where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and  b.is_deleted=0 and a.transaction_type in (1,2,3,4,5,6) and b.item_category_id in (5,6,7,23) and  a.item_category in (5,6,7,23) and a.order_id=0 $company_id $group_id $prod_cond $store_id $item_account $search_cond  group by a.expire_date, b.id order by b.id ASC";
		}
		else if($db_type==2)
		{
			$sql="Select a.expire_date, b.id as prod_id,
			sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
			sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as iss_total_opening,
			sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as rcv_total_opening_amt,
			sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as iss_total_opening_amt,
			sum(case when a.transaction_type=1 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as purchase,
			sum(case when a.transaction_type=1 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as purchase_amt,
			sum(case when a.transaction_type=2 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as issue,
			sum(case when a.transaction_type=2 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as issue_amt,
			sum(case when a.transaction_type in(4) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as issue_return,
			sum(case when a.transaction_type in(4) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_amount else 0 end) as issue_return_amt,
			sum(case when a.transaction_type in(3) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as receive_return,
			sum(case when a.transaction_type in(3) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_amount else 0 end) as receive_return_amt,

			sum(case when a.transaction_type in(6) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as item_transfer_issue,
			sum(case when a.transaction_type in(6) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_amount else 0 end) as item_transfer_issue_amt,
			sum(case when a.transaction_type in(5) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as item_transfer_receive,
			sum(case when a.transaction_type in(5) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_amount else 0 end) as item_transfer_receive_amt,
			sum(case when a.transaction_type in(1,4) and a.receive_basis in(1,2) then a.cons_quantity else 0 end) as rcv_total_wo,
			listagg(cast(a.batch_lot as varchar(4000)), ',') within group (order by a.batch_lot)  as lot_no
			from inv_transaction a, product_details_master b
			where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and  b.is_deleted=0 and a.transaction_type in (1,2,3,4,5,6) and b.item_category_id in (5,6,7,23) and  a.item_category in (5,6,7,23) and a.order_id=0 $company_id $group_id $prod_cond $store_id $item_account $search_cond  group by a.expire_date, b.id order by b.id ASC";
		}
		//echo $sql;die;
		$trnasactionData = sql_select($sql);
		$data_array=array();
		foreach($trnasactionData as $row_p)
		{
			$data_array[$row_p[csf("prod_id")]]['rcv_total_opening']=$row_p[csf("rcv_total_opening")];
			$data_array[$row_p[csf("prod_id")]]['iss_total_opening']=$row_p[csf("iss_total_opening")];
			$data_array[$row_p[csf("prod_id")]]['rcv_total_opening_amt']=$row_p[csf("rcv_total_opening_amt")];
			$data_array[$row_p[csf("prod_id")]]['iss_total_opening_amt']=$row_p[csf("iss_total_opening_amt")];
			$data_array[$row_p[csf("prod_id")]]['purchase']=$row_p[csf("purchase")];
			$data_array[$row_p[csf("prod_id")]]['purchase_amt']=$row_p[csf("purchase_amt")];
			$data_array[$row_p[csf("prod_id")]]['issue']=$row_p[csf("issue")];
			$data_array[$row_p[csf("prod_id")]]['issue_amt']=$row_p[csf("issue_amt")];
			$data_array[$row_p[csf("prod_id")]]['issue_return']=$row_p[csf("issue_return")];
			$data_array[$row_p[csf("prod_id")]]['issue_return_amt']=$row_p[csf("issue_return_amt")];
			$data_array[$row_p[csf("prod_id")]]['receive_return']=$row_p[csf("receive_return")];
			$data_array[$row_p[csf("prod_id")]]['receive_return_amt']=$row_p[csf("receive_return_amt")];
			$data_array[$row_p[csf("prod_id")]]['item_transfer_issue']=$row_p[csf("item_transfer_issue")];
			$data_array[$row_p[csf("prod_id")]]['item_transfer_issue_amt']=$row_p[csf("item_transfer_issue_amt")];
			$data_array[$row_p[csf("prod_id")]]['item_transfer_receive']=$row_p[csf("item_transfer_receive")];
			$data_array[$row_p[csf("prod_id")]]['item_transfer_receive_amt']=$row_p[csf("item_transfer_receive_amt")];
			$data_array[$row_p[csf("prod_id")]]['rcv_total_wo']=$row_p[csf("rcv_total_wo")];
			$data_array[$row_p[csf("prod_id")]]['expire_date']=$row_p[csf("expire_date")];
			$data_array[$row_p[csf("prod_id")]]['lot_no']=implode(",",array_unique(explode(",",$row_p[csf("lot_no")])));
		} //var_dump($data_array);die;
		//print_r($data_array);die;
		//echo $data_array[4103]['rcv_total_wo'].Fuad;
		$returnRes_date="select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where is_deleted=0 and status_active=1 and item_category in(5,6,7,23) group by prod_id";
		$result_returnRes_date = sql_select($returnRes_date);
		foreach($result_returnRes_date as $row)
		{
			$date_array[$row[csf("prod_id")]]['min_date']=$row[csf("min_date")];
			$date_array[$row[csf("prod_id")]]['max_date']=$row[csf("max_date")];
		}


		//$wo_qty_arr=return_library_array("select b.item_id, sum(b.supplier_order_quantity) as qty from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.item_category in (5,6,7,23) and a.pay_mode<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by item_id","item_id","qty");

		//$pi_qty_arr=return_library_array("select b.item_prod_id, sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id in (5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by item_prod_id","item_prod_id","qty");


		$sql="select b.id, b.item_code,b.re_order_label, b.item_category_id,b.item_group_id,b.unit_of_measure,b.item_description,b.sub_group_name, b.current_stock,avg_rate_per_unit,b.company_id from product_details_master b where b.status_active=1 and b.is_deleted=0 $company_cond $item_category_id $group_id $prod_cond $item_account $search_cond order by b.id";
				$result = sql_select($sql);
				foreach($result as $row)
				{

					$ageOfDays = datediff("d",$date_array[$row[csf("id")]]['min_date'],date("Y-m-d"));
					$daysOnHand = datediff("d",$date_array[$row[csf("id")]]['max_date'],date("Y-m-d"));

					$issue_qty=$data_array[$row[csf("id")]]['issue'];
					$issue_qty_amt=$data_array[$row[csf("id")]]['issue_amt'];
					$transfer_out_qty=$data_array[$row[csf("id")]]['item_transfer_issue'];
					$transfer_out_qty_amt=$data_array[$row[csf("id")]]['item_transfer_issue_amt'];

					$transfer_in_qty=$data_array[$row[csf("id")]]['item_transfer_receive'];
					$transfer_in_qty_amt=$data_array[$row[csf("id")]]['item_transfer_receive_amt'];
					$expire_date=$data_array[$row[csf("id")]]['expire_date'];

					//$openingBalance = $data_array[$row[csf("id")]]['rcv_total_opening']-$data_array[$row[csf("id")]]['iss_total_opening'];
					//$openingBalanceValue = $data_array[$row[csf("id")]]['rcv_total_opening_amt']-$data_array[$row[csf("id")]]['iss_total_opening_amt'];
					//$openingRate=$openingBalanceValue/$openingBalance;


					$openingBalance = $data_array[$row[csf("id")]]['rcv_total_opening']-$data_array[$row[csf("id")]]['iss_total_opening'];
                	$openingBalanceValue=$openingRate=0;
                    if($data_array[$row[csf("id")]]['rcv_total_opening'] >0)
                    {
                         $openingRate = $data_array[$row[csf("id")]]['rcv_total_opening_amt']/$data_array[$row[csf("id")]]['rcv_total_opening'];
                    }
                    $openingBalanceValue = $openingBalance*$openingRate;




					$totalReceive = $data_array[$row[csf("id")]]['purchase']+$data_array[$row[csf("id")]]['issue_return']+$transfer_in_qty;
					$totalReceive_amt = $data_array[$row[csf("id")]]['purchase_amt']+$data_array[$row[csf("id")]]['issue_return_amt']+$transfer_in_qty_amt;

					$totalIssue = $data_array[$row[csf("id")]]['issue']+$data_array[$row[csf("id")]]['receive_return']+$transfer_out_qty;
					$totalIssue_amt = $data_array[$row[csf("id")]]['issue_amt']+$data_array[$row[csf("id")]]['receive_return_amt']+$transfer_out_qty_amt;

					$closingStock=$openingBalance+$totalReceive-$totalIssue;
					$closingStock_amt=$openingBalanceValue+$totalReceive_amt-$totalIssue_amt;

					//$closingStock=$opening+$totalReceive-$totalIssue;
					if((($get_upto==1 && $ageOfDays>$txt_days) || ($get_upto==2 && $ageOfDays<$txt_days) || ($get_upto==3 && $ageOfDays>=$txt_days) || ($get_upto==4 && $ageOfDays<=$txt_days) || ($get_upto==5 && $ageOfDays==$txt_days) || $get_upto==0) && (($get_upto_qnty==1 && $closingStock>$txt_qnty) || ($get_upto_qnty==2 && $closingStock<$txt_qnty) || ($get_upto_qnty==3 && $closingStock>=$txt_qnty) || ($get_upto_qnty==4 && $closingStock<=$txt_qnty) || ($get_upto_qnty==5 && $closingStock==$txt_qnty) || $get_upto_qnty==0))
					{

						if(($value_with ==1 && (number_format($closingStock,3)>0.000 || number_format($openingBalance,3)>0.000)) || ($value_with ==0 && (number_format($closingStock,3)>0.000 || number_format($openingBalance,3)>0.000 || number_format($totalReceive,3)>0.000 || number_format($totalIssue,3)>0.000)))
						{


							$avg_rate=$mrr_rate_arr[$row[csf("id")]];
							$stockValue=$closingStock*$avg_rate;

							$summary_data[$row[csf("company_id")]][$row[csf("item_category_id")]]["openingBalanceValue"] += $openingBalanceValue;

							$summary_data[$row[csf("company_id")]][$row[csf("item_category_id")]]["purchase"] += $data_array[$row[csf("id")]]['purchase_amt'];
							$summary_data[$row[csf("company_id")]][$row[csf("item_category_id")]]["transfer_in_qty"] += $transfer_in_qty_amt;
							$summary_data[$row[csf("company_id")]][$row[csf("item_category_id")]]["issue_return"] += $data_array[$row[csf("id")]]['issue_return_amt'];
							$summary_data[$row[csf("company_id")]][$row[csf("item_category_id")]]["totalReceive"] += $totalReceive_amt;


							$summary_data[$row[csf("company_id")]][$row[csf("item_category_id")]]["issue_qty"] += $issue_qty_amt;
							$summary_data[$row[csf("company_id")]][$row[csf("item_category_id")]]["transfer_out_qty"] += $transfer_out_qty_amt;
							$summary_data[$row[csf("company_id")]][$row[csf("item_category_id")]]["receive_return"] += $data_array[$row[csf("id")]]['receive_return_amt'];
							$summary_data[$row[csf("company_id")]][$row[csf("item_category_id")]]["totalIssue"] += $totalIssue_amt;

							$summary_data[$row[csf("company_id")]][$row[csf("item_category_id")]]["stockValue"] += $stockValue;
							$summary_data[$row[csf("company_id")]][$row[csf("item_category_id")]]["re_order_label"] = $row[csf('re_order_label')];
						}
					}
				}
			/*	echo "<pre>";
				print_r($summary_data);die;*/

				$company_row_span_arr = array();
				foreach ($summary_data as $companyId => $companyData)
				{
					$category_row=0;
					foreach ($companyData as $category_id => $row)
					{
						$category_row++;
					}
					$company_row_span_arr[$companyId] =$category_row;
				}


		ob_start();
		?>
		<div align="center" style="height:auto; width:auto; margin:0 auto; padding:0; width:1400px">
			<table width="1400px" cellpadding="0" cellspacing="0" id="caption" align="center">
				<thead>
					<tr style="border:none;">
						<td colspan="12" align="center" class="form_caption" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td>
					</tr>
					<tr style="border:none;">
						<td colspan="12" class="form_caption" align="center" style="border:none; font-size:14px;">
						   <b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="12" align="center" class="form_caption" style="border:none;font-size:12px; font-weight:bold">
							<? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date,'dd-mm-yyyy')." To : ".change_date_format($to_date,'dd-mm-yyyy')."" ;?>
						</td>
					</tr>
				</thead>
			</table>
			<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="1360" rules="all" id="rpt_table_header" align="left">
				<thead>
					<tr>
						<th rowspan="2" width="50">SL</th>
						<th rowspan="2" width="120">Company Names</th>
						<th rowspan="2" width="120">Item Category</th>
                        <th rowspan="2" width="120">Opening Value Tk</th>
						<th colspan="4">Receive</th>
						<th colspan="4">Issue</th>
						<th rowspan="2" width="">Closing Stock</th>
					</tr>
					<tr>
						<th width="100">Purchase Value TK</th>
						<th width="100">Transfer In Value TK</th>
						<th width="100">Issue Return Value TK</th>
						<th width="100">Total Rcv Value TK</th>

						<th width="100">Issue Value TK</th>
						<th width="100">Transfer Out Value TK</th>
						<th width="100">Receive Return Value TK</th>
						<th width="100">Total Issue Value TK</th>
					</tr>
				</thead>
			</table>
			<div style="width:1380px; max-height:250px; overflow-y:scroll; overflow-x:hidden; float: left;" id="scroll_body">
			<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="1360" rules="all" align="left">
			<?
			$i=1;
			foreach ($summary_data as $comp_id => $company_data)
			{
				$y=1;$show=false;
				$tot_open_bl_amt=$tot_purchase=$tot_transfer_in_qty=$tot_issue_return=$tot_total_receive=$tot_issue=$tot_transfer_out_qty=$tot_rec_return=$tot_total_issue=$tot_stock_value=0;
				foreach($company_data as $category_id =>$row)
				{
					$show=true;
					// if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
					$re_order_label = $row[csf('re_order_label')];
					if($closingStock <= $re_order_label){$bgcolor="red";}elseif($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style=" word-wrap:break-word; <? echo $stylecolor; ?> "  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    	<?
                    	if($y==1)
                    	{?>
                        	<td width="50" align="center" rowspan="<? echo $company_row_span_arr[$comp_id];?>" title="<? echo $comp_id;?>"><? echo $i; ?></td>
                        	<td width="120" rowspan="<? echo $company_row_span_arr[$comp_id];?>"><? echo $companyArr[$comp_id] ?></td>
                        <?}?>
                        <td width="120"><p><? echo $item_category[$category_id]; ?>&nbsp;</p></td>
                        <td width="120" align="right"><p style="word-break: break-all;"><? echo number_format($row["openingBalanceValue"],3); $tot_open_bl_amt+=$row["openingBalanceValue"]; ?></p></td>
                        <td width="100" align="right"><p><? echo number_format($row['purchase'],3); $tot_purchase+=$row['purchase']; ?></p></td>
                        <td width="100" align="right"><p><? echo number_format($row["transfer_in_qty"],3); $tot_transfer_in_qty+=$row["transfer_in_qty"];?></p></td>
                        <td width="100" align="right"><p style="word-break: break-all;"><? echo number_format($row['issue_return'],3); $tot_issue_return+=$row['issue_return'];?></p></td>
                        <td width="100" align="right"><p style="word-break: break-all;"><? echo number_format($row["totalReceive"],3); $tot_total_receive+=$row["totalReceive"]; ?></p></td>

                        <td width="100" align="right"><p style="word-break: break-all;"><? echo number_format($row["issue_qty"],3); $tot_issue+=$row["issue_qty"]; ?></p></td>
                        <td width="100" align="right"><p style="word-break: break-all;"><? echo number_format($row["transfer_out_qty"],3); $tot_transfer_out_qty+=$row["transfer_out_qty"]; ?></p></td>
                        <td width="100" align="right"><p style="word-break: break-all;"><? echo number_format($row['receive_return'],3); $tot_rec_return+=$row['receive_return']; ?></p></td>
                        <td width="100" align="right"><p style="word-break: break-all;"><? echo number_format($row["totalIssue"],3); $tot_total_issue+=$row["totalIssue"]; ?></p></td>
                        <td align="right"><p style="word-break: break-all;word-wrap: break-word;"><? echo number_format($row["stockValue"],3); $tot_stock_value+=$row["stockValue"]; ?></p></td>
                    </tr>
                    <?
                    $i++;$y++;
					$grand_tot_openingBalanceValue += $row["openingBalanceValue"];
					$grand_tot_purchase +=$row["purchase"];
					$grand_tot_transfer_in_qty +=$row["transfer_in_qty"];
					$grand_tot_issue_return +=$row["issue_return"];
					$grand_tot_totalReceive +=$row["totalReceive"];
					$grand_tot_issue_qty +=$row["issue_qty"];
					$grand_tot_transfer_out_qty +=$row["transfer_out_qty"];
					$grand_tot_receive_return +=$row["receive_return"];
					$grand_tot_totalIssue +=$row["totalIssue"];
					$grand_tot_stock_value+=$row["stockValue"];

				}
				if($show == true)
				{
					?>
						<tr style="background-color: #eee">
							<td colspan="3" align="right"><b>Company Total =</b></td>
	                        <td align="right" ><? echo number_format($tot_open_bl_amt,3); ?></td>
							<td align="right" ><? echo number_format($tot_purchase,3); ?></td>
							<td align="right" ><? echo number_format($tot_transfer_in_qty,3); ?></td>
							<td align="right" ><? echo number_format($tot_issue_return,3); ?></td>
							<td align="right" ><? echo number_format($tot_total_receive,3); ?></td>
							<td align="right" ><? echo number_format($tot_issue,3); ?></td>
							<td align="right" ><? echo number_format($tot_transfer_out_qty,3); ?></td>
							<td align="right" ><? echo number_format($tot_rec_return,3); ?></td>
							<td align="right"><? echo number_format($tot_total_issue,3); ?></td>
							<td align="right" ><p style="word-break: break-all;word-wrap: break-word;"><? echo number_format($tot_stock_value,2); ?></p></td>
						</tr>
					<?
				}
			}
			?>
			</table>
			</div>
			 <table width="1360" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" align="left">
				<tfoot>
					<tr>
						<th width="50"></th>
						<th width="120"></th>
						<th width="120" align="right">Grand Total =</th>
                        <th width="120" align="right" id=""><p style="word-break: break-all;"><? echo number_format($grand_tot_openingBalanceValue,3); ?></p></th>
						<th width="100" align="right" id=""><p style="word-break: break-all;"><? echo number_format($grand_tot_purchase,3); ?></p></th>
						<th width="100" align="right" id=""><p style="word-break: break-all;"><? echo number_format($grand_tot_transfer_in_qty,3); ?></p></th>
						<th width="100" align="right" id=""><p style="word-break: break-all;"><? echo number_format($grand_tot_issue_return,3); ?></p></th>
						<th width="100" align="right" id=""><p style="word-break: break-all;"><? echo number_format($grand_tot_totalReceive,3); ?></p></th>

						<th width="100" align="right" id=""><p style="word-break: break-all;"><? echo number_format($grand_tot_issue_qty,3); ?></p></th>
						<th width="100" align="right" id=""><p style="word-break: break-all;"><? echo number_format($grand_tot_transfer_out_qty,3); ?></p></th>
						<th width="100" align="right" id=""><p style="word-break: break-all;"><? echo number_format($grand_tot_receive_return,3); ?></p></th>
						<th width="100" align="right" id=""><p style="word-break: break-all;"><? echo number_format($grand_tot_totalIssue,3); ?></p></th>

						<th width="" align="right" id=""><p style="word-break: break-all;word-wrap: break-word;"><? echo number_format($grand_tot_stock_value,2); ?></p></th>
					</tr>
				</tfoot>
			</table>
        </div>
        <?
	}
	else if($report_type==6)
	{

		if ($cbo_company_name==0) $company_id =""; else $company_id =" and a.company_id='$cbo_company_name'";
		if ($cbo_item_category_id==0) $item_category_id=" and b.item_category_id in(5,6,7,23)"; else $item_category_id=" and b.item_category_id='$cbo_item_category_id'";
		if ($cbo_item_category_id==0) $item_category_cond=" and a.item_category in(5,6,7,23)"; else $item_category_cond=" and a.item_category='$cbo_item_category_id'";
		if ($item_account_id==0) $item_account=""; else $item_account=" and a.prod_id in ($item_account_id)";
		if ($item_account_id==0) $item_account_cond=""; else $item_account_cond=" and b.id in ($item_account_id)";
		if ($cbo_store_name==0)  $store_id="";  else  $store_id=" and a.store_id=$cbo_store_name ";


		if($db_type==0)
		{
			$from_date=change_date_format($from_date,'yyyy-mm-dd');
			$to_date=change_date_format($to_date,'yyyy-mm-dd');
		}
		else if($db_type==2)
		{
			$from_date=change_date_format($from_date,'','',1);
			$to_date=change_date_format($to_date,'','',1);
		}
		else
		{
			$from_date=""; $to_date="";
		}
		$search_cond="";
		//if($value_with==0) $search_cond =""; else $search_cond= "  and b.current_stock>0";


		$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
		//$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
		//$storeArr = return_library_array("select id,store_name from lib_store_location where status_active=1 and is_deleted=0","id","store_name");
		//$itemnameArr = return_library_array("select id,item_name from lib_item_creation where status_active=1 and is_deleted=0","id","item_name");
		$itemgroupArr = return_library_array("select id,item_name from  lib_item_group where status_active=1 and is_deleted=0","id","item_name");
		
		$trans_sql="SELECT b.id as PROD_ID, a.transaction_date as TRANSACTION_DATE, a.transaction_type as TRANSACTION_TYPE, a.cons_quantity as CONS_QUANTITY, a.cons_amount as CONS_AMOUNT, a.batch_lot as LOT_NO from inv_transaction a, product_details_master b
		where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id $group_id $prod_cond $store_id $item_account $search_cond $item_category_id $item_category_cond order by b.id ASC";
		
		//echo $trans_sql;die;
		$trnasactionData = sql_select($trans_sql);
		//echo count($trnasactionData).jahid;die;
		$data_array=array();
		foreach($trnasactionData as $row_p)
		{
			if($row_p["TRANSACTION_TYPE"]==1)
			{
				if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
				{
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['rcv_total_opening']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['rcv_total_opening_amt']+=$row_p["CONS_AMOUNT"];
				}
				else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
				{
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['purchase']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['purchase_amt']+=$row_p["CONS_AMOUNT"];
				}
			}
			else if($row_p["TRANSACTION_TYPE"]==2)
			{
				if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
				{
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['iss_total_opening']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['iss_total_opening_amt']+=$row_p["CONS_AMOUNT"];
				}
				else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
				{
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['issue']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['issue_amt']+=$row_p["CONS_AMOUNT"];
				}
			}
			else if($row_p["TRANSACTION_TYPE"]==3)
			{
				if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
				{
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['iss_total_opening']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['iss_total_opening_amt']+=$row_p["CONS_AMOUNT"];
				}
				else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
				{
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['receive_return']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['receive_return_amt']+=$row_p["CONS_AMOUNT"];
				}
			}
			else if($row_p["TRANSACTION_TYPE"]==4)
			{
				if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
				{
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['rcv_total_opening']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['rcv_total_opening_amt']+=$row_p["CONS_AMOUNT"];
				}
				else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
				{
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['issue_return']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['issue_return_amt']+=$row_p["CONS_AMOUNT"];
				}
			}
			else if($row_p["TRANSACTION_TYPE"]==5)
			{
				if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
				{
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['rcv_total_opening']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['rcv_total_opening_amt']+=$row_p["CONS_AMOUNT"];
				}
				else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
				{
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['item_transfer_receive']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['item_transfer_receive_amt']+=$row_p["CONS_AMOUNT"];
				}
			}
			else if($row_p["TRANSACTION_TYPE"]==6)
			{
				if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
				{
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['iss_total_opening']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['iss_total_opening_amt']+=$row_p["CONS_AMOUNT"];
				}
				else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
				{
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['item_transfer_issue']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['item_transfer_issue_amt']+=$row_p["CONS_AMOUNT"];
				}
			}
		}
		
		//echo "test2";die;
		//var_dump($data_array);die;
		//print_r($data_array);die;
		//echo $data_array[4103]['rcv_total_wo'].Fuad;
		if ($cbo_item_category_id==0) $item_category_cond=" and item_category in(5,6,7,23)"; else $item_category_cond=" and item_category='$cbo_item_category_id'";
		$returnRes_date="select prod_id as PROD_ID, batch_lot as LOT, min(transaction_date) as MIN_DATE, max(transaction_date) as MAX_DATE from inv_transaction where is_deleted=0 and status_active=1 $item_category_cond group by prod_id, batch_lot";
		$result_returnRes_date = sql_select($returnRes_date);
		foreach($result_returnRes_date as $row)
		{
			$date_array[$row["PROD_ID"]][$row["LOT"]]['min_date']=$row["MIN_DATE"];
			$date_array[$row["PROD_ID"]][$row["LOT"]]['max_date']=$row["MAX_DATE"];
		}
		

		$wo_qty_arr=return_library_array("select b.item_id, sum(b.supplier_order_quantity) as qty from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.item_category in (5,6,7,23) and a.pay_mode<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by item_id","item_id","qty");

		$pi_qty_arr=return_library_array("select b.item_prod_id, sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id in (5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by item_prod_id","item_prod_id","qty");
		$i=1;
		ob_start();
		?>
		<div align="center" style="height:auto; width:auto; margin:0 auto; padding:0; width:2388">
			<table width="2388px" cellpadding="0" cellspacing="0" id="caption" align="center">
				<thead>
					<tr style="border:none;">
						<td colspan="20" align="center" class="form_caption" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td>
					</tr>
					<tr style="border:none;">
						<td colspan="20" class="form_caption" align="center" style="border:none; font-size:14px;">
						   <b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="20" align="center" class="form_caption" style="border:none;font-size:12px; font-weight:bold">
							<? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date,'dd-mm-yyyy')." To : ".change_date_format($to_date,'dd-mm-yyyy')."" ;?>
						</td>
					</tr>
				</thead>
			</table>
			<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="2388" rules="all" id="rpt_table_header">
				<thead>
					<tr>
						<th rowspan="2" width="50">SL</th>
						<th colspan="9">Description</th>
						<th rowspan="2" width="100"> Opneing Rate</th>
                        <th rowspan="2" width="100">Opening Stock</th>
                        <th rowspan="2" width="110">Opening Value</th>
						<th colspan="4">Receive</th>
						<th colspan="4">Issue</th>
						<th rowspan="2" width="100">Closing Stock</th>
						<th rowspan="2" width="80">Avg. Rate</th>
						<th rowspan="2" width="120">Stock Value</th>
						<th rowspan="2" width="60">DOH</th>
						<th rowspan="2">Pipe Line</th>
					</tr>
					<tr>
						<th width="60">Prod. ID</th>
                        <th width="100">Lot</th>
						<th width="70">Item Code</th>
						<th width="100">Item Category</th>
						<th width="100">Item Group</th>
						<th width="100">Item Sub-group</th>
						<th width="180">Item Description</th>
						<th width="70">Item Size</th>
						<th width="60">UOM</th>
						<th width="80">Purchase</th>
						<th width="80">Transfer In</th>
						<th width="80">Issue Return</th>
						<th width="100">Total Received</th>
						<th width="80">Issue</th>
						<th width="80">Transfer Out</th>
						<th width="80">Receive Return</th>
						<th width="100">Total Issue</th>
					</tr>
				</thead>
			</table>
			<div style="width:2388px; max-height:250px; overflow-y:scroll; overflow-x:hidden;" id="scroll_body">
			<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="2370" rules="all" align="left">
			<?
				$rcv_qnty_array=return_library_array("SELECT b.prod_id, sum(b.cons_quantity) as cons_quantity from  inv_receive_master a,  inv_transaction b where a.id=b.mst_id and a.receive_basis in(1,2) and b.transaction_type=1 and b.status_active=1 group by b.prod_id","prod_id","cons_quantity");
				
			 	$sql="SELECT b.id, b.item_code, b.item_category_id, b.item_group_id, b.unit_of_measure, b.item_description, b.sub_group_name, b.current_stock, b.avg_rate_per_unit, c.lot
				from product_details_master b, inv_store_wise_qty_dtls c
				where b.id=c.prod_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.company_id='$cbo_company_name' $item_category_id $group_id $prod_cond $item_account_cond $search_cond
				group by b.id, b.item_code, b.item_category_id, b.item_group_id, b.unit_of_measure, b.item_description, b.sub_group_name, b.current_stock, b.avg_rate_per_unit, c.lot
				order by b.id";
				//echo $sql;die;
				$result = sql_select($sql);
				foreach($result as $row)
				{
					if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
					if( $row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==4 )
						$stylecolor='color:#A61000;';
					else
						$stylecolor='color:#000000;';
					$ageOfDays = datediff("d",$date_array[$row[csf("id")]][$row[csf("lot")]]['min_date'],date("Y-m-d"));
					$daysOnHand = datediff("d",$date_array[$row[csf("id")]][$row[csf("lot")]]['max_date'],date("Y-m-d"));


					$issue_qty=$data_array[$row[csf("id")]][$row[csf("lot")]]['issue'];
					$transfer_out_qty=$data_array[$row[csf("id")]][$row[csf("lot")]]['item_transfer_issue'];
					$transfer_in_qty=$data_array[$row[csf("id")]][$row[csf("lot")]]['item_transfer_receive'];

					$transfer_out_qty_amt=$data_array[$row[csf("id")]][$row[csf("lot")]]['item_transfer_issue_amt'];
					$transfer_in_qty_amt=$data_array[$row[csf("id")]][$row[csf("lot")]]['item_transfer_receive_amt'];

					$openingBalance = $data_array[$row[csf("id")]][$row[csf("lot")]]['rcv_total_opening']-$data_array[$row[csf("id")]][$row[csf("lot")]]['iss_total_opening'];
					$openingBalanceValue = $data_array[$row[csf("id")]][$row[csf("lot")]]['rcv_total_opening_amt']-$data_array[$row[csf("id")]][$row[csf("lot")]]['iss_total_opening_amt'];
					if($openingBalanceValue>0 && $openingBalance>0) $openingRate=$openingBalanceValue/$openingBalance; else $openingRate=0;
					$totalReceive = $data_array[$row[csf("id")]][$row[csf("lot")]]['purchase']+$data_array[$row[csf("id")]][$row[csf("lot")]]['issue_return']+$transfer_in_qty;//+$openingBalance
					$totalIssue = $data_array[$row[csf("id")]][$row[csf("lot")]]['issue']+$data_array[$row[csf("id")]][$row[csf("lot")]]['receive_return']+$transfer_out_qty;

					$totalReceive_amt = $data_array[$row[csf("id")]][$row[csf("lot")]]['purchase_amt']+$data_array[$row[csf("id")]][$row[csf("lot")]]['issue_return_amt']+$transfer_in_qty_amt;
					$totalIssue_amt = $data_array[$row[csf("id")]][$row[csf("lot")]]['issue_amt']+$data_array[$row[csf("id")]][$row[csf("lot")]]['receive_return_amt']+$transfer_out_qty_amt;

					$closingStock=$openingBalance+$totalReceive-$totalIssue;
					$stockValue=$openingBalanceValue+$totalReceive_amt-$totalIssue_amt;
					if($stockValue>0 && $closingStock>0) $avg_rate=$stockValue/$closingStock; else $avg_rate=0;
					//$closingStock=$opening+$totalReceive-$totalIssue;
					if((($get_upto==1 && $ageOfDays>$txt_days) || ($get_upto==2 && $ageOfDays<$txt_days) || ($get_upto==3 && $ageOfDays>=$txt_days) || ($get_upto==4 && $ageOfDays<=$txt_days) || ($get_upto==5 && $ageOfDays==$txt_days) || $get_upto==0) && (($get_upto_qnty==1 && $closingStock>$txt_qnty) || ($get_upto_qnty==2 && $closingStock<$txt_qnty) || ($get_upto_qnty==3 && $closingStock>=$txt_qnty) || ($get_upto_qnty==4 && $closingStock<=$txt_qnty) || ($get_upto_qnty==5 && $closingStock==$txt_qnty) || $get_upto_qnty==0))
					{
						$pipeLine_qty=$wo_qty_arr[$row[csf("id")]]+$pi_qty_arr[$row[csf("id")]]-$rcv_qnty_array[$row[csf("id")]];
						if($pipeLine_qty<0) $pipeLine_qty=0;
						if($value_with==1)
						{
							if(number_format($closingStock,2)>0.00 || number_format($openingBalance,2)>0.00 )
							{
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style=" word-wrap:break-word; <? echo $stylecolor; ?> "  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="50" align="center"><? echo $i; ?></td>
									<td width="60"><? echo $row[csf("id")]; ?></td>
                                    <td width="100"><p><? echo $row[csf("lot")]; ?>&nbsp;</p></td>
									<td width="70"><p><? echo $row[csf("item_code")]; ?>&nbsp;</p></td>
									<td width="100"><p><? echo $item_category[$row[csf("item_category_id")]]; ?>&nbsp;</p></td>
									<td width="100"><p><? echo $itemgroupArr[$row[csf("item_group_id")]]; ?>&nbsp;</p></td>
									<td width="100"><p><? echo $row[csf("sub_group_name")]; ?>&nbsp;</p></td>
									<td width="180"><p><? echo $row[csf("item_description")]; ?>&nbsp;</p></td>
									<td width="70"><p><? echo $row[csf("item_size")]; ?>&nbsp;</p></td>
									<td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?>&nbsp;</p></td>
									<td width="100" align="right"><p><? echo number_format($openingRate,3); ?></p></td>
									<td width="100" align="right"><p><? echo number_format($openingBalance,3); $tot_open_bl+=$openingBalance; ?></p></td>
									<td width="110" align="right"><p><? echo number_format($openingBalanceValue,3); $tot_open_bl_amt+=$openingBalanceValue; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['purchase'],3); $tot_purchase+=$data_array[$row[csf("id")]]['purchase']; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($transfer_in_qty,3); $tot_transfer_in_qty+=$transfer_in_qty; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['issue_return'],3); $tot_issue_return+=$data_array[$row[csf("id")]]['issue_return'];//$row[csf("issue_return")]; ?></p></td>
									<td width="100" align="right"><p><? echo number_format($totalReceive,3); $tot_total_receive+=$totalReceive; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($issue_qty,3); $tot_issue+=$issue_qty; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($transfer_out_qty,3); $tot_transfer_out_qty+=$transfer_out_qty; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['receive_return'],3); $tot_rec_return+=$data_array[$row[csf("id")]]['receive_return'];//$issue_result[0][csf('receive_return')]; ?></p></td>
									<td width="100" align="right"><p><? echo number_format($totalIssue,3); $tot_total_issue+=$totalIssue; ?></p></td>
									<td width="100" align="right"><p><? echo number_format($closingStock,3); $tot_closing_stock+=$closingStock; ?></p></td>
									<td width="80" align="right"><p><? if(number_format($closingStock,3)>0) echo number_format($avg_rate,3); else echo "0.00"; //number_format($row[csf("avg_rate")],2); $tot_avg_rate+=$row[csf("avg_rate")]; ?></p></td>
									<td width="120" align="right"><p><? if(number_format($closingStock,3)>0) echo number_format($stockValue,3); else echo "0.00"; $tot_stock_value+=$stockValue; ?></p></td>
									<td width="60" align="center"><p><? echo $daysOnHand;//$daysOnHand; ?>&nbsp;</p></td>
                                    <?
									if($prod_check[$row[csf("id")]]=="")
									{
										$prod_check[$row[csf("id")]]=$row[csf("id")];
										?>
                                        <td width="100" align="right" title="<? echo $wo_qty_arr[$row[csf("id")]]."==".$pi_qty_arr[$row[csf("id")]]."--".$rcv_qnty_array[$row[csf("id")]]; ?>"><a href="##" onclick="fnc_pipeLine_details('<? echo $row[csf("id")];?>','pipe_line_popup')"><p><? echo number_format($pipeLine_qty,2); ?></p></a></td>
                                        <?									}
									else
									{
										?>
                                        <td align="right" ><p>&nbsp;</p></td>	
                                        <?	
									}
									?>
								</tr>
								<?
								$i++;
								$totalStockValue+=$stockValue;
								$totalpipeLine_qty+=$pipeLine_qty;
							}
						}
						else
						{
							if(number_format($closingStock,3)>0.000 || number_format($openingBalance,3)>0.000 || number_format($totalReceive,3)>0.000 || number_format($totalIssue,3)>0.000)
							{
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style=" word-wrap:break-word; <? echo $stylecolor; ?> "  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="50" align="center"><? echo $i; ?></td>
									<td width="60"><? echo $row[csf("id")]; ?></td>
                                    <td width="100"><p><? echo $row[csf("lot")]; ?>&nbsp;</p></td>
									<td width="70"><p><? echo $row[csf("item_code")]; ?>&nbsp;</p></td>
									<td width="100"><p><? echo $item_category[$row[csf("item_category_id")]]; ?>&nbsp;</p></td>
									<td width="100"><p><? echo $itemgroupArr[$row[csf("item_group_id")]]; ?>&nbsp;</p></td>
									<td width="100"><p><? echo $row[csf("sub_group_name")]; ?>&nbsp;</p></td>
									<td width="180"><p><? echo $row[csf("item_description")]; ?>&nbsp;</p></td>
									<td width="70"><p><? echo $row[csf("item_size")]; ?>&nbsp;</p></td>
									<td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?>&nbsp;</p></td>
									<td width="100" align="right"><p><? echo number_format($openingRate,3); ?></p></td>
									<td width="100" align="right"><p><? echo number_format($openingBalance,3); $tot_open_bl+=$openingBalance; ?></p></td>
									<td width="110" align="right"><p><? echo number_format($openingBalanceValue,3); $tot_open_bl_amt+=$openingBalanceValue; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['purchase'],3); $tot_purchase+=$data_array[$row[csf("id")]]['purchase']; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($transfer_in_qty,3); $tot_transfer_in_qty+=$transfer_in_qty; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['issue_return'],3); $tot_issue_return+=$data_array[$row[csf("id")]]['issue_return'];//$row[csf("issue_return")]; ?></p></td>
									<td width="100" align="right"><p><? echo number_format($totalReceive,3); $tot_total_receive+=$totalReceive; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($issue_qty,3); $tot_issue+=$issue_qty; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($transfer_out_qty,3); $tot_transfer_out_qty+=$transfer_out_qty; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['receive_return'],3); $tot_rec_return+=$data_array[$row[csf("id")]]['receive_return'];//$issue_result[0][csf('receive_return')]; ?></p></td>
									<td width="100" align="right"><p><? echo number_format($totalIssue,3); $tot_total_issue+=$totalIssue; ?></p></td>
									<td width="100" align="right"><p><? echo number_format($closingStock,3); $tot_closing_stock+=$closingStock; ?></p></td>
									<td width="80" align="right"><p><? if(number_format($closingStock,3)>0) echo number_format($avg_rate,3); else echo "0.00";//number_format($row[csf("avg_rate")],2); $tot_avg_rate+=$row[csf("avg_rate")]; ?></p></td>
									<td width="120" align="right"><p><? if(number_format($closingStock,3)>0) echo number_format($stockValue,3); else echo "0.00"; $tot_stock_value+=$stockValue; ?></p></td>
									<td width="60" align="center"><p><? echo $daysOnHand;//$daysOnHand; ?>&nbsp;</p></td>
                                    <?
									if($prod_check[$row[csf("id")]]=="")
									{
										$prod_check[$row[csf("id")]]=$row[csf("id")];
										?>
                                        <td align="right" title="<? echo $wo_qty_arr[$row[csf("id")]]."==".$pi_qty_arr[$row[csf("id")]]."--".$rcv_qnty_array[$row[csf("id")]]; ?>"><a href="##" onclick="fnc_pipeLine_details('<? echo $row[csf("id")];?>','pipe_line_popup')"><p><? echo number_format($pipeLine_qty,2); ?></p></a></td>	
                                        <?									}
									else
									{
										?>
                                        <td align="right" ><p>&nbsp;</p></td>	
                                        <?	
									}
									?>
																	
								</tr>
								<?
								$i++;
								$totalStockValue+=$stockValue;
								$totalpipeLine_qty+=$pipeLine_qty;
							}
						}
					}
				}
			?>
			</table>
			</div>
			<table width="2388" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer">
				<tfoot>
					<tr>
						<th width="50">&nbsp;</th>
						<th width="60">&nbsp;</th>
                        <th width="100">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="180">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="100">Total:</th>
                        <th width="100" align="right" id="value_tot_open_bl"><? echo number_format($tot_open_bl,3); ?></th>
                        <th width="110" align="right" id="value_tot_open_bl_amt"><? echo number_format($tot_open_bl_amt,3); ?></th>
						<th width="80" align="right" id="value_tot_purchase"><? echo number_format($tot_purchase,3); ?></th>
						<th width="80" align="right" id="value_tot_transfer_in_qty"><? echo number_format($tot_transfer_in_qty,3); ?></th>
						<th width="80" align="right" id="value_tot_issue_return"><? echo number_format($tot_issue_return,3); ?></th>
						<th width="100" align="right" id="value_tot_total_receive"><? echo number_format($tot_total_receive,3); ?></th>
						<th width="80" align="right" id="value_tot_issue"><? echo number_format($tot_issue,3); ?></th>
						<th width="80" align="right" id="value_tot_transfer_out_qty"><? echo number_format($tot_transfer_out_qty,3); ?></th>
						<th width="80" align="right" id="value_tot_rec_return"><? echo number_format($tot_rec_return,3); ?></th>
						<th width="100" align="right" id="value_tot_total_issue"><? echo number_format($tot_total_issue,3); ?></th>
						<th width="100" align="right" id="value_tot_closing_stock"><? echo number_format($tot_closing_stock,3); ?></th>
						<th width="80">&nbsp;</th>
						<th width="120" align="right" id="value_tot_stock_value"><p><? echo number_format($tot_stock_value,2); ?></p></th>
						<th width="60">&nbsp;</th>
						<th align="right" id="value_totalpipeLine_qty"><? echo number_format($totalpipeLine_qty,2); ?></th>
					</tr>
				</tfoot>
			</table>
        </div>
        <?
	}
	else
	{
		if ($cbo_company_name==0) $company_id =""; else $company_id =" and a.company_id='$cbo_company_name'";
		if ($cbo_item_category_id==0) $item_category_id=" and b.item_category_id in(5,6,7,23)"; else $item_category_id=" and b.item_category_id='$cbo_item_category_id'";
		if ($cbo_item_category_id==0) $item_category_cond=" and a.item_category in(5,6,7,23)"; else $item_category_cond=" and a.item_category='$cbo_item_category_id'";
		if ($item_account_id==0) $item_account=""; else $item_account=" and a.prod_id in ($item_account_id)";
		if ($item_account_id==0) $item_account_cond=""; else $item_account_cond=" and b.id in ($item_account_id)";
		if ($cbo_store_name==0)  $store_id="";  else  $store_id=" and a.store_id=$cbo_store_name ";


		if($db_type==0)
		{
			$from_date=change_date_format($from_date,'yyyy-mm-dd');
			$to_date=change_date_format($to_date,'yyyy-mm-dd');
		}
		else if($db_type==2)
		{
			$from_date=change_date_format($from_date,'','',1);
			$to_date=change_date_format($to_date,'','',1);
		}
		else
		{
			$from_date=""; $to_date="";
		}
		$search_cond="";
		//if($value_with==0) $search_cond =""; else $search_cond= "  and b.current_stock>0";


		$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
		//$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
		//$storeArr = return_library_array("select id,store_name from lib_store_location where status_active=1 and is_deleted=0","id","store_name");
		//$itemnameArr = return_library_array("select id,item_name from lib_item_creation where status_active=1 and is_deleted=0","id","item_name");
		$itemgroupArr = return_library_array("select id,item_name from lib_item_group where status_active=1 and is_deleted=0 and item_category in(5,6,7,23)","id","item_name");
		
		$sql_period="select a.ID, a.PERIOD_STARTING_DATE, a.PERIOD_ENDING_DATE, a.FINANCIAL_PERIOD, a.PERIOD_LOCKED, a.IS_LOCKED
 		from LIB_AC_PERIOD_DTLS a, LIB_AC_PERIOD_MST b 
		where a.mst_id=b.id and a.PERIOD_LOCKED=0 and a.IS_LOCKED=0 and b.COMPANY_ID=$cbo_company_name order by a.ID";
		//echo $sql_period;die;
		$sql_period_result=sql_select($sql_period);
		if(count($sql_period_result)>0)
		{
			$period_starting_date=change_date_format($sql_period_result[0]["PERIOD_STARTING_DATE"],"","",1);
		}
		else
		{
			$sql_period="select max(a.PERIOD_ENDING_DATE) as PERIOD_ENDING_DATE
			from LIB_AC_PERIOD_DTLS a, LIB_AC_PERIOD_MST b 
			where a.mst_id=b.id and b.COMPANY_ID=$cbo_company_name";
			$sql_period_result=sql_select($sql_period);
			$period_starting_date=change_date_format(date('d-m-Y',strtotime($sql_period_result[0]["PERIOD_ENDING_DATE"])+86400),"","",1);
			//echo $period_starting_date;die;
		}
		
		//echo $period_starting_date;die;
		
		
		$prev_close_item_ref="select a.id as ID, a.prod_id as PROD_ID, a.ref_type as REF_TYPE, a.ref_id as REF_ID, a.closing as CLOSING, a.last_rate as LAST_RATE, a.closing_receive as CLOSING_RECEIVE, a.closing_issue as CLOSING_ISSUE, a.closing_receive_rtn as CLOSING_RECEIVE_RTN, a.closing_issue_rtn as CLOSING_ISSUE_RTN, a.closing_transfer_in as CLOSING_TRANSFER_IN, a.closing_transfer_out as CLOSING_TRANSFER_OUT 
		from year_close_item_ref a, product_details_master b
		where a.prod_id=b.id and a.company_id=$cbo_company_name and b.item_category_id in(5,6,7,23) and b.status_active=1 and b.is_deleted=0 and a.REF_TYPE=1";		
		//echo $prev_close_item_ref;die;
		$prev_close_item_ref_result=sql_select($prev_close_item_ref);
		$closing_data=array();
		foreach($prev_close_item_ref_result as $row)
		{
			$closing_data[$row["PROD_ID"]]["REF_TYPE"]=$row["REF_TYPE"];
			$closing_data[$row["PROD_ID"]]["REF_ID"]=$row["REF_ID"];
			$closing_data[$row["PROD_ID"]]["CLOSING"]+=number_format($row["CLOSING"],6,".","");
			$closing_data[$row["PROD_ID"]]["LAST_RATE"]=$row["LAST_RATE"];
			if(number_format($row["CLOSING"],6,".","")!=0) $CLOSING_AMT=$row["CLOSING"]*$row["LAST_RATE"]; else $CLOSING_AMT=0;
			$closing_data[$row["PROD_ID"]]["CLOSING_AMT"]+=$CLOSING_AMT;
			$closing_data[$row["PROD_ID"]]["CLOSING_RECEIVE"]+=number_format($row["CLOSING_RECEIVE"],6,".","");
			if(number_format($row["CLOSING_RECEIVE"],6,".","")!=0) $CLOSING_RECEIVE_AMT=$row["CLOSING_RECEIVE"]*$row["LAST_RATE"]; else $CLOSING_RECEIVE_AMT=0;
			$closing_data[$row["PROD_ID"]]["CLOSING_RECEIVE_AMT"]+=$CLOSING_RECEIVE_AMT;
			$closing_data[$row["PROD_ID"]]["CLOSING_ISSUE"]+=number_format($row["CLOSING_ISSUE"],6,".","");
			if(number_format($row["CLOSING_ISSUE"],6,".","")!=0) $CLOSING_ISSUE_AMT=$row["CLOSING_ISSUE"]*$row["LAST_RATE"]; else $CLOSING_ISSUE_AMT=0;
			$closing_data[$row["PROD_ID"]]["CLOSING_ISSUE_AMT"]+=$CLOSING_ISSUE_AMT;
			$closing_data[$row["PROD_ID"]]["CLOSING_RECEIVE_RTN"]+=number_format($row["CLOSING_RECEIVE_RTN"],6,".","");
			if(number_format($row["CLOSING_RECEIVE_RTN"],6,".","")!=0) $CLOSING_RECEIVE_RTN_AMT=$row["CLOSING_RECEIVE_RTN"]*$row["LAST_RATE"]; else $CLOSING_RECEIVE_RTN_AMT=0;
			$closing_data[$row["PROD_ID"]]["CLOSING_RECEIVE_RTN_AMT"]+=$CLOSING_RECEIVE_RTN_AMT;
			$closing_data[$row["PROD_ID"]]["CLOSING_ISSUE_RTN"]+=number_format($row["CLOSING_ISSUE_RTN"],6,".","");
			if(number_format($row["CLOSING_ISSUE_RTN"],6,".","")!=0) $CLOSING_ISSUE_RTN_AMT=$row["CLOSING_ISSUE_RTN"]*$row["LAST_RATE"]; else $CLOSING_ISSUE_RTN_AMT=0;
			$closing_data[$row["PROD_ID"]]["CLOSING_ISSUE_RTN_AMT"]+=$CLOSING_ISSUE_RTN_AMT;
			$closing_data[$row["PROD_ID"]]["CLOSING_TRANSFER_IN"]+=number_format($row["CLOSING_TRANSFER_IN"],6,".","");
			if(number_format($row["CLOSING_TRANSFER_IN"],6,".","")!=0) $CLOSING_TRANSFER_IN_AMT=$row["CLOSING_TRANSFER_IN"]*$row["LAST_RATE"]; else $CLOSING_TRANSFER_IN_AMT=0;
			$closing_data[$row["PROD_ID"]]["CLOSING_TRANSFER_IN_AMT"]+=$CLOSING_TRANSFER_IN_AMT;
			$closing_data[$row["PROD_ID"]]["CLOSING_TRANSFER_OUT"]+=number_format($row["CLOSING_TRANSFER_OUT"],6,".","");
			if(number_format($row["CLOSING_TRANSFER_OUT"],6,".","")!=0) $CLOSING_TRANSFER_OUT_AMT=$row["CLOSING_TRANSFER_OUT"]*$row["LAST_RATE"]; else $CLOSING_TRANSFER_OUT_AMT=0;
			$closing_data[$row["PROD_ID"]]["CLOSING_TRANSFER_OUT_AMT"]+=$CLOSING_TRANSFER_OUT_AMT;
		}
		unset($prev_close_item_ref_result);
		//echo "<pre>";print_r($closing_data[10820]);die;
		
		$trans_sql="SELECT b.id as PROD_ID, a.transaction_date as TRANSACTION_DATE, a.transaction_type as TRANSACTION_TYPE, a.cons_quantity as CONS_QUANTITY, a.cons_amount as CONS_AMOUNT, a.batch_lot as LOT_NO from inv_transaction a, product_details_master b
		where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id $group_id $prod_cond $store_id $item_account $search_cond $item_category_id $item_category_cond and a.insert_date >= '$period_starting_date'";
		
		//echo $trans_sql;//die;
		$trnasactionData = sql_select($trans_sql);
		//echo count($trnasactionData).jahid;die;
		$data_array=array();
		foreach($trnasactionData as $row_p)
		{
			if($row_p["TRANSACTION_TYPE"]==1)
			{
				if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
				{
					$data_array[$row_p["PROD_ID"]]['rcv_total_opening']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['rcv_total_opening_amt']+=$row_p["CONS_AMOUNT"];
				}
				else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
				{
					$data_array[$row_p["PROD_ID"]]['purchase']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['purchase_amt']+=$row_p["CONS_AMOUNT"];
				}
			}
			else if($row_p["TRANSACTION_TYPE"]==2)
			{
				if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
				{
					$data_array[$row_p["PROD_ID"]]['iss_total_opening']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['iss_total_opening_amt']+=$row_p["CONS_AMOUNT"];
				}
				else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
				{
					$data_array[$row_p["PROD_ID"]]['issue']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['issue_amt']+=$row_p["CONS_AMOUNT"];
				}
			}
			else if($row_p["TRANSACTION_TYPE"]==3)
			{
				if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
				{
					$data_array[$row_p["PROD_ID"]]['iss_total_opening']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['iss_total_opening_amt']+=$row_p["CONS_AMOUNT"];
				}
				else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
				{
					$data_array[$row_p["PROD_ID"]]['receive_return']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['receive_return_amt']+=$row_p["CONS_AMOUNT"];
				}
			}
			else if($row_p["TRANSACTION_TYPE"]==4)
			{
				if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
				{
					$data_array[$row_p["PROD_ID"]]['rcv_total_opening']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['rcv_total_opening_amt']+=$row_p["CONS_AMOUNT"];
				}
				else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
				{
					$data_array[$row_p["PROD_ID"]]['issue_return']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['issue_return_amt']+=$row_p["CONS_AMOUNT"];
				}
			}
			else if($row_p["TRANSACTION_TYPE"]==5)
			{
				if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
				{
					$data_array[$row_p["PROD_ID"]]['rcv_total_opening']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['rcv_total_opening_amt']+=$row_p["CONS_AMOUNT"];
				}
				else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
				{
					$data_array[$row_p["PROD_ID"]]['item_transfer_receive']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['item_transfer_receive_amt']+=$row_p["CONS_AMOUNT"];
				}
			}
			else if($row_p["TRANSACTION_TYPE"]==6)
			{
				if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
				{
					$data_array[$row_p["PROD_ID"]]['iss_total_opening']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['iss_total_opening_amt']+=$row_p["CONS_AMOUNT"];
				}
				else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
				{
					$data_array[$row_p["PROD_ID"]]['item_transfer_issue']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['item_transfer_issue_amt']+=$row_p["CONS_AMOUNT"];
				}
			}
			if($batch_lot_check[$row_p["PROD_ID"]][$row_p["LOT_NO"]]=="" && $row_p["LOT_NO"] !="")
			{
				$batch_lot_check[$row_p["PROD_ID"]][$row_p["LOT_NO"]]=$row_p["LOT_NO"];
				$data_array[$row_p["PROD_ID"]]['lot_no'].=$row_p["LOT_NO"].",";
			}
		}
		unset($trnasactionData);
		//echo "test2";die;
		//var_dump($data_array);die;
		//print_r($data_array);die;
		//echo $data_array[4103]['rcv_total_wo'].Fuad;
		if ($cbo_item_category_id==0) $item_category_cond=" and item_category in(5,6,7,23)"; else $item_category_cond=" and item_category='$cbo_item_category_id'";
		$returnRes_date="select prod_id as PROD_ID, min(transaction_date) as MIN_DATE, max(transaction_date) as MAX_DATE from inv_transaction where is_deleted=0 and status_active=1 $item_category_cond group by prod_id";
		$result_returnRes_date = sql_select($returnRes_date);
		foreach($result_returnRes_date as $row)
		{
			$date_array[$row["PROD_ID"]]['min_date']=$row["MIN_DATE"];
			$date_array[$row["PROD_ID"]]['max_date']=$row["MAX_DATE"];
		}
		//echo "<pre>";print_r($date_array);die;

		$wo_qty_arr=return_library_array("select b.item_id, sum(b.supplier_order_quantity) as qty from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.item_category in (5,6,7,23) and a.pay_mode<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by item_id","item_id","qty");

		$pi_qty_arr=return_library_array("select b.item_prod_id, sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id in (5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by item_prod_id","item_prod_id","qty");
		$i=1;
		ob_start();
		?>
		<div align="center" style="height:auto; width:auto; margin:0 auto; padding:0; width:2388">
			<table width="2388px" cellpadding="0" cellspacing="0" id="caption" align="center">
				<thead>
					<tr style="border:none;">
						<td colspan="20" align="center" class="form_caption" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td>
					</tr>
					<tr style="border:none;">
						<td colspan="20" class="form_caption" align="center" style="border:none; font-size:14px;">
						   <b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="20" align="center" class="form_caption" style="border:none;font-size:12px; font-weight:bold">
							<? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date,'dd-mm-yyyy')." To : ".change_date_format($to_date,'dd-mm-yyyy')."" ;?>
						</td>
					</tr>
				</thead>
			</table>
			<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="2388" rules="all" id="rpt_table_header">
				<thead>
					<tr>
						<th rowspan="2" width="50">SL</th>
						<th colspan="8">Description</th>
						<th rowspan="2" width="100"> Opneing Rate</th>
                        <th rowspan="2" width="100">Opening Stock</th>
                        <th rowspan="2" width="110">Opening Value</th>
						<th colspan="4">Receive</th>
						<th colspan="4">Issue</th>
						<th rowspan="2" width="100">Closing Stock</th>
						<th rowspan="2" width="80">Avg. Rate</th>
						<th rowspan="2" width="120">Stock Value</th>
						<th rowspan="2" width="60">DOH</th>
						<th rowspan="2" width="100">Pipe Line</th>
						<th rowspan="2" width="120">Lot</th>
					</tr>
					<tr>
						<th width="60">Prod. ID</th>
						<th width="70">Item Code</th>
						<th width="100">Item Category</th>
						<th width="100">Item Group</th>
						<th width="100">Item Sub-group</th>
						<th width="180">Item Description</th>
						<th width="70">Item Size</th>
						<th width="60">UOM</th>
						<th width="80">Purchase</th>
						<th width="80">Transfer In</th>
						<th width="80">Issue Return</th>
						<th width="100">Total Received</th>
						<th width="80">Issue</th>
						<th width="80">Transfer Out</th>
						<th width="80">Receive Return</th>
						<th>Total Issue</th>
					</tr>
				</thead>
			</table>
			<div style="width:2388px; max-height:250px; overflow-y:scroll; overflow-x:hidden;" id="scroll_body">
			<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="2370" rules="all" align="left">
			<?
				if ($cbo_item_category_id==0) $rcv_category_cond=" and b.item_category in(5,6,7,23)"; else $rcv_category_cond=" and b.item_category='$cbo_item_category_id'";
				$rcv_qnty_array=return_library_array("SELECT b.prod_id, sum(b.cons_quantity) as cons_quantity from  inv_receive_master a,  inv_transaction b where a.id=b.mst_id and a.receive_basis in(1,2) and b.transaction_type=1 and b.status_active=1 $rcv_category_cond group by b.prod_id","prod_id","cons_quantity");
				
			 	$sql="SELECT b.id, b.item_code, b.item_category_id,b.item_group_id,b.unit_of_measure,b.item_description,b.sub_group_name, b.current_stock,avg_rate_per_unit from product_details_master b where b.status_active=1 and b.is_deleted=0 and b.company_id='$cbo_company_name' $item_category_id $group_id $prod_cond $item_account_cond $search_cond order by b.id";
				//echo $sql;die;
				$result = sql_select($sql);
				foreach($result as $row)
				{
					if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
					if( $row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==4 )
						$stylecolor='color:#A61000;';
					else
						$stylecolor='color:#000000;';
					$ageOfDays = datediff("d",$date_array[$row[csf("id")]]['min_date'],date("Y-m-d"));
					$daysOnHand = datediff("d",$date_array[$row[csf("id")]]['max_date'],date("Y-m-d"));


					$issue_qty=$data_array[$row[csf("id")]]['issue'];

					$transfer_out_qty=$data_array[$row[csf("id")]]['item_transfer_issue'];
					$transfer_in_qty=$data_array[$row[csf("id")]]['item_transfer_receive'];

					$transfer_out_qty_amt=$data_array[$row[csf("id")]]['item_transfer_issue_amt'];
					$transfer_in_qty_amt=$data_array[$row[csf("id")]]['item_transfer_receive_amt'];

					$openingBalance = (($data_array[$row[csf("id")]]['rcv_total_opening']+$closing_data[$row[csf("id")]]["CLOSING"])-$data_array[$row[csf("id")]]['iss_total_opening']);
					$openingBalanceValue = (($data_array[$row[csf("id")]]['rcv_total_opening_amt']+$closing_data[$row[csf("id")]]["CLOSING_AMT"])-$data_array[$row[csf("id")]]['iss_total_opening_amt']);
					if($openingBalanceValue>0 && $openingBalance>0) $openingRate=$openingBalanceValue/$openingBalance; else $openingRate=0;
					$totalReceive = $data_array[$row[csf("id")]]['purchase']+$data_array[$row[csf("id")]]['issue_return']+$transfer_in_qty;//+$openingBalance
					$totalIssue = $data_array[$row[csf("id")]]['issue']+$data_array[$row[csf("id")]]['receive_return']+$transfer_out_qty;

					$totalReceive_amt = $data_array[$row[csf("id")]]['purchase_amt']+$data_array[$row[csf("id")]]['issue_return_amt']+$transfer_in_qty_amt;
					$totalIssue_amt = $data_array[$row[csf("id")]]['issue_amt']+$data_array[$row[csf("id")]]['receive_return_amt']+$transfer_out_qty_amt;

					$closingStock=$openingBalance+$totalReceive-$totalIssue;
					$stockValue=$openingBalanceValue+$totalReceive_amt-$totalIssue_amt;
					if($stockValue>0 && $closingStock>0) $avg_rate=$stockValue/$closingStock; else $avg_rate=0;
					//$closingStock=$opening+$totalReceive-$totalIssue;
					if((($get_upto==1 && $ageOfDays>$txt_days) || ($get_upto==2 && $ageOfDays<$txt_days) || ($get_upto==3 && $ageOfDays>=$txt_days) || ($get_upto==4 && $ageOfDays<=$txt_days) || ($get_upto==5 && $ageOfDays==$txt_days) || $get_upto==0) && (($get_upto_qnty==1 && $closingStock>$txt_qnty) || ($get_upto_qnty==2 && $closingStock<$txt_qnty) || ($get_upto_qnty==3 && $closingStock>=$txt_qnty) || ($get_upto_qnty==4 && $closingStock<=$txt_qnty) || ($get_upto_qnty==5 && $closingStock==$txt_qnty) || $get_upto_qnty==0))
					{
						//$stockValue=$closingStock*$avg_rate;
						$pipeLine_qty=$wo_qty_arr[$row[csf("id")]]+$pi_qty_arr[$row[csf("id")]]-$data_array[$row[csf("id")]]['rcv_total_wo']-$rcv_qnty_array[$row[csf("id")]];

						if($pipeLine_qty<0) $pipeLine_qty=0;
						//echo $closingStock."=".$openingBalance."<br>";
						if($value_with==1)
						{
							if(number_format($closingStock,2)>0.00 || number_format($openingBalance,2)>0.00 )
							{
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style=" word-wrap:break-word; <? echo $stylecolor; ?> "  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="50" align="center"><? echo $i; ?></td>
									<td width="60"><? echo $row[csf("id")]; ?></td>
									<td width="70"><p><? echo $row[csf("item_code")]; ?>&nbsp;</p></td>
									<td width="100"><p><? echo $item_category[$row[csf("item_category_id")]]; ?>&nbsp;</p></td>
									<td width="100"><p><? echo $itemgroupArr[$row[csf("item_group_id")]]; ?>&nbsp;</p></td>
									<td width="100"><p><? echo $row[csf("sub_group_name")]; ?>&nbsp;</p></td>
									<td width="180"><p><? echo $row[csf("item_description")]; ?>&nbsp;</p></td>
									<td width="70"><p><? echo $row[csf("item_size")]; ?>&nbsp;</p></td>
									<td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?>&nbsp;</p></td>
									<td width="100" align="right"><p><? echo number_format($openingRate,3); ?></p></td>
									<td width="100" align="right"><p><? echo number_format($openingBalance,3); $tot_open_bl+=$openingBalance; ?></p></td>
									<td width="110" align="right"><p><? echo number_format($openingBalanceValue,3); $tot_open_bl_amt+=$openingBalanceValue; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['purchase'],3); $tot_purchase+=$data_array[$row[csf("id")]]['purchase']; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($transfer_in_qty,3); $tot_transfer_in_qty+=$transfer_in_qty; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['issue_return'],3); $tot_issue_return+=$data_array[$row[csf("id")]]['issue_return'];//$row[csf("issue_return")]; ?></p></td>
									<td width="100" align="right"><p><? echo number_format($totalReceive,3); $tot_total_receive+=$totalReceive; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($issue_qty,3); $tot_issue+=$issue_qty; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($transfer_out_qty,3); $tot_transfer_out_qty+=$transfer_out_qty; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['receive_return'],3); $tot_rec_return+=$data_array[$row[csf("id")]]['receive_return'];//$issue_result[0][csf('receive_return')]; ?></p></td>
									<td width="100" align="right"><p><? echo number_format($totalIssue,3); $tot_total_issue+=$totalIssue; ?></p></td>
									<td width="100" align="right"><p><? echo number_format($closingStock,3); $tot_closing_stock+=$closingStock; ?></p></td>
									<td width="80" align="right"><p><? if(number_format($closingStock,3)>0) echo number_format($avg_rate,3); else echo "0.00"; //number_format($row[csf("avg_rate")],2); $tot_avg_rate+=$row[csf("avg_rate")]; ?></p></td>
									<td width="120" align="right"><p><? if(number_format($closingStock,3)>0) echo number_format($stockValue,3); else echo "0.00"; $tot_stock_value+=$stockValue; ?></p></td>
									<td width="60" align="center"><p><? echo $daysOnHand;//$daysOnHand; ?>&nbsp;</p></td>
									<td width="100" align="right" title="<? echo $wo_qty_arr[$row[csf("id")]]."==".$pi_qty_arr[$row[csf("id")]]."--".$rcv_qnty_array[$row[csf("id")]]; ?>"><a href="##" onclick="fnc_pipeLine_details('<? echo $row[csf("id")];?>','pipe_line_popup')"><p><? echo number_format($pipeLine_qty,2); ?></p></a></td>
									<td><p><? echo chop($data_array[$row[csf("id")]]['lot_no'],","); ?>&nbsp;</p></td>
								</tr>
								<?
								$i++;
								$totalStockValue+=$stockValue;
								$totalpipeLine_qty+=$pipeLine_qty;
							}
						}
						else
						{
							if(number_format($closingStock,3)>0.000 || number_format($openingBalance,3)>0.000 || number_format($totalReceive,3)>0.000 || number_format($totalIssue,3)>0.000)
							{
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style=" word-wrap:break-word; <? echo $stylecolor; ?> "  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="50" align="center"><? echo $i; ?></td>
									<td width="60"><? echo $row[csf("id")]; ?></td>
									<td width="70"><p><? echo $row[csf("item_code")]; ?>&nbsp;</p></td>
									<td width="100"><p><? echo $item_category[$row[csf("item_category_id")]]; ?>&nbsp;</p></td>
									<td width="100"><p><? echo $itemgroupArr[$row[csf("item_group_id")]]; ?>&nbsp;</p></td>
									<td width="100"><p><? echo $row[csf("sub_group_name")]; ?>&nbsp;</p></td>
									<td width="180"><p><? echo $row[csf("item_description")]; ?>&nbsp;</p></td>
									<td width="70"><p><? echo $row[csf("item_size")]; ?>&nbsp;</p></td>
									<td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?>&nbsp;</p></td>
									<td width="100" align="right"><p><? echo number_format($openingRate,3); ?></p></td>
									<td width="100" align="right"><p><? echo number_format($openingBalance,3); $tot_open_bl+=$openingBalance; ?></p></td>
									<td width="110" align="right"><p><? echo number_format($openingBalanceValue,3); $tot_open_bl_amt+=$openingBalanceValue; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['purchase'],3); $tot_purchase+=$data_array[$row[csf("id")]]['purchase']; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($transfer_in_qty,3); $tot_transfer_in_qty+=$transfer_in_qty; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['issue_return'],3); $tot_issue_return+=$data_array[$row[csf("id")]]['issue_return'];//$row[csf("issue_return")]; ?></p></td>
									<td width="100" align="right"><p><? echo number_format($totalReceive,3); $tot_total_receive+=$totalReceive; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($issue_qty,3); $tot_issue+=$issue_qty; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($transfer_out_qty,3); $tot_transfer_out_qty+=$transfer_out_qty; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['receive_return'],3); $tot_rec_return+=$data_array[$row[csf("id")]]['receive_return'];//$issue_result[0][csf('receive_return')]; ?></p></td>
									<td width="100" align="right"><p><? echo number_format($totalIssue,3); $tot_total_issue+=$totalIssue; ?></p></td>
									<td width="100" align="right"><p><? echo number_format($closingStock,3); $tot_closing_stock+=$closingStock; ?></p></td>
									<td width="80" align="right"><p><? if(number_format($closingStock,3)>0) echo number_format($avg_rate,3); else echo "0.00";//number_format($row[csf("avg_rate")],2); $tot_avg_rate+=$row[csf("avg_rate")]; ?></p></td>
									<td width="120" align="right"><p><? if(number_format($closingStock,3)>0) echo number_format($stockValue,3); else echo "0.00"; $tot_stock_value+=$stockValue; ?></p></td>
									<td width="60" align="center"><p><? echo $daysOnHand;//$daysOnHand; ?>&nbsp;</p></td>
									<td width="100" align="right" title="<? echo $wo_qty_arr[$row[csf("id")]]."==".$pi_qty_arr[$row[csf("id")]]."--".$rcv_qnty_array[$row[csf("id")]]; ?>"><a href="##" onclick="fnc_pipeLine_details('<? echo $row[csf("id")];?>','pipe_line_popup')"><p><? echo number_format($pipeLine_qty,2); ?></p></a></td>
									<td><p><? echo chop($data_array[$row[csf("id")]]['lot_no'],","); ?>&nbsp;</p></td>
								</tr>
								<?
								$i++;
								$totalStockValue+=$stockValue;
								$totalpipeLine_qty+=$pipeLine_qty;
							}
						}
					}
				}
			?>
			</table>
			</div>
			<table width="2388" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer">
				<tfoot>
					<tr>
						<th width="50">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="180">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="100">Total:</th>
                        <th width="100" align="right" id="value_tot_open_bl"><? echo number_format($tot_open_bl,3); ?></th>
                        <th width="110" align="right" id="value_tot_open_bl_amt"><? echo number_format($tot_open_bl_amt,3); ?></th>
						<th width="80" align="right" id="value_tot_purchase"><? echo number_format($tot_purchase,3); ?></th>
						<th width="80" align="right" id="value_tot_transfer_in_qty"><? echo number_format($tot_transfer_in_qty,3); ?></th>
						<th width="80" align="right" id="value_tot_issue_return"><? echo number_format($tot_issue_return,3); ?></th>
						<th width="100" align="right" id="value_tot_total_receive"><? echo number_format($tot_total_receive,3); ?></th>
						<th width="80" align="right" id="value_tot_issue"><? echo number_format($tot_issue,3); ?></th>
						<th width="80" align="right" id="value_tot_transfer_out_qty"><? echo number_format($tot_transfer_out_qty,3); ?></th>
						<th width="80" align="right" id="value_tot_rec_return"><? echo number_format($tot_rec_return,3); ?></th>
						<th width="100" align="right" id="value_tot_total_issue"><? echo number_format($tot_total_issue,3); ?></th>
						<th width="100" align="right" id="value_tot_closing_stock"><? echo number_format($tot_closing_stock,3); ?></th>
						<th width="80">&nbsp;</th>
						<th width="120" align="right" id="value_tot_stock_value"><p><? echo number_format($tot_stock_value,2); ?></p></th>
						<th width="60">&nbsp;</th>
						<th width="100" align="right" id="value_totalpipeLine_qty"><? echo number_format($totalpipeLine_qty,2); ?></th>
						<th>&nbsp;</th>
					</tr>
				</tfoot>
			</table>
        </div>
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
    echo "$html**$filename**$report_type";
    exit();
}


if($action=="pipe_line_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$product_sql=sql_select("select id,  item_category_id, item_group_id, sub_group_name, item_description, product_name_details, item_size from product_details_master where id=$prod_id");
	$item_group_name = return_field_value("item_name","lib_item_group","id=".$product_sql[0][csf("item_group_id")],"item_name");	?>
    <table width="630" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
    	<thead>
        	<tr>
            	<th width="50">Product Id</th>
                <th width="120">Item Category</th>
                <th width="100">Item Group</th>
                <th width="100">Item Sub-group</th>
                <th width="150">Item Description</th>
                <th >Item Size</th>
            </tr>
        </thead>
        <tbody>
        	<tr>
            	<td align="center"><p><? echo $product_sql[0][csf("id")]; ?>&nbsp;</p></td>
                <td><p><? echo $item_category[$product_sql[0][csf("item_category_id")]]; ?>&nbsp;</p></td>
                <td><p><? echo $item_group_name; ?>&nbsp;</p></td>
                <td><p><? echo $product_sql[0][csf("sub_group_name")]; ?>&nbsp;</p></td>
                <td><p><? echo $product_sql[0][csf("item_description")]; ?>&nbsp;</p></td>
                <td><p><? echo $product_sql[0][csf("item_size")]; ?>&nbsp;</p></td>
            </tr>
        </tbody>
    </table>
    <br />
    <table width="680" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
    	<thead>
        	<tr>
            	<th width="30">SL</th>
                <th width="70">WO/PI Date</th>
                <th width="100">WO/PI No</th>
                <th width="30">Type</th>
                <th width="70">Pay Mode</th>
                <th width="70">UOM</th>
                <th width="80">WO/PI Qty.</th>
                <th width="80">Rcv. Qnty</th>
                <th width="80">Balance</th>
                <th >Remarks</th>
            </tr>
        </thead>
        <tbody>
		<?
		
		$rcv_qnty_array=return_library_array("SELECT a.booking_id, sum(b.cons_quantity) as cons_quantity from  inv_receive_master a,  inv_transaction b where a.id=b.mst_id and a.receive_basis in(1,2) and b.transaction_type=1 and b.prod_id=$prod_id and b.status_active=1 group by a.booking_id","booking_id","cons_quantity");
		$details_sql="SELECT b.id as wo_po_id, b.wo_number as wo_po_no, b.wo_date as wo_po_date, b.pay_mode as wo_po_mode, max(c.uom) as wo_po_uom, sum(c.supplier_order_quantity) as wo_po_qnty, 1 as type from wo_non_order_info_mst b,  wo_non_order_info_dtls c where b.id=c.mst_id and c.item_id=$prod_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and b.item_category in (5,6,7) and b.pay_mode<>2 and c.is_deleted=0 group by b.id, b.wo_number, b.wo_date, b.pay_mode
		union all
		SELECT b.id as wo_po_id, b.pi_number as wo_po_no, b.pi_date as wo_po_date, 0 as wo_po_mode, max(c.uom) as wo_po_uom, sum(c.quantity) as wo_po_qnty, 2 as type from com_pi_master_details b, com_pi_item_details c where b.id=c.pi_id and b.item_category_id in (5,6,7) and c.item_prod_id=$prod_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, b.pi_number, b.pi_date";
		//echo $details_sql;
		$sql_result=sql_select($details_sql);
		$i=1;
		foreach($sql_result as $row)
		{
			if ($i%2==0)
			$bgcolor="#E9F3FF";
			else
			$bgcolor="#FFFFFF";
			$rcv_qnty=$rcv_qnty_array[$row[csf("wo_po_id")]];
			$balance=$row[csf("wo_po_qnty")]-$rcv_qnty;
			if($row[csf("type")]==1) $type="WO"; else $type="PI";

        	?>
        	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td align="center"><p><? echo $i; ?>&nbsp;</p></td>
                <td><p><? if($row[csf("wo_po_date")]!="" && $row[csf("wo_po_date")]!="0000-00-00") echo change_date_format($row[csf("wo_po_date")]); ?>&nbsp;</p></td>
                <td><p><? echo $row[csf("wo_po_no")]; ?>&nbsp;</p></td>
                <td align="center"><p><? echo $type; ?>&nbsp;</p></td>
                <td><p><? echo $pay_mode[$row[csf("wo_po_mode")]]; ?>&nbsp;</p></td>
                <td><p><? echo $unit_of_measurement[$row[csf("wo_po_uom")]]; ?>&nbsp;</p></td>
                <td align="right"><p><? echo number_format($row[csf("wo_po_qnty")],0); $total_wo_qnty+=$row[csf("wo_po_qnty")]; ?></p></td>
                <td align="right" title="<? echo $row[csf("wo_po_id")]; ?>"><p><? echo number_format($rcv_qnty,0); $total_rcv_qnty+=$rcv_qnty; ?></p></td>
                <td align="right"><p><? echo number_format($balance,0); $total_bal_qnty+=$balance;  ?></p></td>
                <td><p><? echo $row[csf("")]; ?>&nbsp;</p></td>
            </tr>
            <?
			$i++;
		}
		?>
        </tbody>
        <tfoot>
        	<tr>
            	<th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >Total</th>
                <th ><? echo number_format($total_wo_qnty,0); ?></th>
                <th ><? echo number_format($total_rcv_qnty,0); ?></th>
                <th ><? echo number_format($total_bal_qnty,0); ?></th>
                <th >&nbsp;</th>
            </tr>
        </tfoot>
    </table>
    <?

}
?>

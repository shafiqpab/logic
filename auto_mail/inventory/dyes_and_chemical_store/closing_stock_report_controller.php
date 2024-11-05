<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');
require('../../setting/mail_setting.php');
require('../../../ext_resource/mpdf60/mpdf.php');

$user_id = 3000;
 
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action='generate_report';

if($action=="print_button_variable_setting")
{
    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=6 and report_id=139 and is_deleted=0 and status_active=1");
   	$printButton=explode(',',$print_report_format);

	foreach($printButton as $id){

		if($id==256)$buttonHtml.='<input type="button" name="search" id="search" value="Report2" onClick="generate_report(3)" style="width:80px" class="formbutton" /> ';
        if($id==267)$buttonHtml.='<input type="button" name="search" id="search" value="Report3" onClick="generate_report(7)" style="width:80px" class="formbutton" /> ';
        if($id==715)$buttonHtml.='<input type="button" name="search" id="search" value="All Data" onClick="generate_report(4)" style="width:80px" class="formbutton" /> ';
		if($id==716)$buttonHtml.='<input type="button" name="search" id="search" value="Stock Value" onClick="generate_report(5)" style="width:80px" class="formbutton" /> ';
		if($id==717)$buttonHtml.='<input type="button" name="search" id="search" value="Lot Wise" onClick="generate_report(6)" style="width:70px" class="formbutton" />';
		if($id==438)$buttonHtml.='<input type="button" name="search" id="search" value="QTY And VALUE" onClick="generate_report(8)" style="width:80px" class="formbutton" /> ';

		
	}

   echo "document.getElementById('button_data_panel').innerHTML = '".$buttonHtml."';\n";
    exit();
}

if ($action=="load_drop_down_store")
{
	//extract($_REQUEST);
	$data=explode('_',$data);
	$company_id=$data[0];
	$item_cat_id=$data[1];
	$store_wish=$data[2];
	// echo $item_cat_id."**".$store_wish;
	if($store_wish==1){
		if($item_cat_id>0){
		$sql= "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and  a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and b.category_type in($item_cat_id) group by a.id, a.store_name";
		// echo $sql;
		}else{
		$sql= "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and  a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id  group by a.id, a.store_name";
		// echo $sql;

		}
     	echo create_drop_down( "cbo_store_name", 120, $sql,"id,store_name", 1, "--Select--", 0, "",0 );
	}
	else{
	    echo create_drop_down( "cbo_store_name", 120, $blank,"id,store_name", 1, "--Select--", 0, "",0 );
	}
	exit();
}

if ($action=="item_account_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
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
		exit();
}

if ($action=="item_group_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data);
    ?>
    <script>
	var selected_id = new Array, selected_name = new Array(); 
	selected_attach_id = new Array();

	function check_all_data() {
		var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
		tbl_row_count = tbl_row_count - 1;
		for( var i = 1; i <= tbl_row_count; i++ ) {
			var onclickString = $('#tr_' + i).attr('onclick');
			var paramArr = onclickString.split("'");
			var functionParam = paramArr[1];
			js_set_value( functionParam );
			
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
		$('#item_name_id').val( id );
		$('#item_name_val').val( ddd );
	}
	</script>
    <input type="hidden" id="item_name_id"/>
    <input type="hidden" id="item_name_val"/>
    <?
	if ($data[1]==0) $item_category =""; else $item_category =" and item_category in($data[1])";
	// $item_category;
	$sql="SELECT id,item_name from  lib_item_group where status_active=1 and is_deleted=0 $item_category"; //id=$data[1] and

	echo  create_list_view("list_view", "Item Name", "350","480","330",0, $sql , "js_set_value", "id,item_name", "", 1, "0", $arr , "item_name", "periodical_purchase_report_controller",'setFilterGrid("list_view",-1);','0','',1);
	exit();
}

if($action=="generate_report")
{
	
	$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);
    $previous_date = change_date_format(date('d-m-Y', strtotime('-1 day', strtotime($current_date))),'','',1);
	
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	foreach($companyArr as $cbo_company_name=>$company_name){	
	
		$cbo_store_wise=	"2";
		$cbo_store_name=	"";
		$cbo_item_category_id=	"0";
		$from_date=	$previous_date;
		$to_date=	$previous_date;
		$item_account_id=	"";
		$item_group_id=	"";
		$value_with=	"0";
		$get_upto=	"0";
		$txt_days=	"";
		$get_upto_qnty=	"0";
		$txt_qnty=	"";
		$cbo_compliance=	"0";
		$report_title=	"Closing Stock DnC";
		$report_type=	"6";



		$report_type=str_replace("'","",$report_type);
		$cbo_company_name=str_replace("'","",$cbo_company_name);
		$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
		$item_account_id=str_replace("'","",$item_account_id);
		$item_group_id=str_replace("'","",$item_group_id);
		$cbo_store_name=str_replace("'","",$cbo_store_name);
		$cbo_store_wise=str_replace("'","",$cbo_store_wise);
		$cbo_compliance=str_replace("'","",$cbo_compliance);
	
	//echo $cbo_store_name."=".test;die;
	

		if($report_type==2)
		{
			$company_cond="";
			if ($cbo_company_name>0) $company_cond =" and company_id=$cbo_company_name";
			if ($cbo_item_category_id>0) $category_cond=" and item_category='$cbo_item_category_id'";  else $category_cond=" and item_category in(5,6,7,23)";
			$mrr_rate_sql=sql_select("select prod_id, sum(cons_quantity) as cons_quantiy, sum(cons_amount) as cons_amount from inv_transaction where status_active=1 and is_deleted=0 and transaction_type in(1,4,5) $category_cond $company_cond group by prod_id");
			$mrr_rate_arr=array();
			foreach($mrr_rate_sql as $row)
			{
				$mrr_rate_arr[$row[csf("prod_id")]]=$row[csf("cons_amount")]/$row[csf("cons_quantiy")];
			}
			//echo "<pre>";print_r($mrr_rate_arr);die;
			$sql_cond="";
			if ($cbo_company_name>0) $sql_cond =" and a.company_id=$cbo_company_name";
			if ($cbo_item_category_id>0) $sql_cond.=" and a.item_category_id='$cbo_item_category_id'";  else $sql_cond.=" and a.item_category_id in(5,6,7,23)";
			if ($item_account_id!="") $sql_cond.=" and a.id in ($item_account_id)";
			if ($item_group_id>0) $sql_cond.=" and a.item_group_id in($item_group_id)";
			if ($cbo_store_name != "")  $sql_cond.=" and b.store_id in($cbo_store_name) ";
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

			if($to_date!="")
			{
				if($to_date!="") $mrr_date_cond=" and a.transaction_date<='$to_date'";
				if($to_date!="") $rcv_date_cond=" and b.transaction_date<='$to_date'";
			}

			$issue_qnty_arr=sql_select("select b.recv_trans_id, b.issue_qnty from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.issue_trans_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type in(2,3,6) and a.item_category in(5,6,7,23) $mrr_date_cond");
			$mrr_issue_qnty_arr=array();
			foreach($issue_qnty_arr as $row)
			{
				$mrr_issue_qnty_arr[$row[csf("recv_trans_id")]]+=$row[csf("issue_qnty")];
			}



			if($db_type==0)
			{

				$sql="select a.id, a.company_id, a.supplier_id,a.re_order_label, a.item_code, a.item_category_id, a.item_group_id, a.unit_of_measure, a.item_description, a.sub_group_name,  group_concat(b.id) as trans_id, sum(b.cons_quantity) as cons_quantity, sum(b.cons_amount) as cons_amount, max(b.transaction_date) as transaction_date, b.batch_lot as lot, c.recv_number, c.receive_date, b.expire_date, c.supplier_id
				from product_details_master a, inv_transaction b, inv_receive_master c
				where a.id=b.prod_id and b.mst_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.entry_form=4 and b.transaction_type in(1) and b.company_id='$cbo_company_name' $sql_cond $rcv_date_cond
				group by a.id, a.company_id, a.supplier_id,a.re_order_label, a.item_code, a.item_category_id, a.item_group_id, a.unit_of_measure, a.item_description, a.sub_group_name, c.recv_number, c.receive_date, b.batch_lot,b.expire_date, c.supplier_id
				union all
				select a.id, a.company_id, a.supplier_id,a.re_order_label, a.item_code, a.item_category_id, a.item_group_id, a.unit_of_measure, a.item_description, a.sub_group_name,  group_concat(b.id) as trans_id, sum(b.cons_quantity) as cons_quantity, sum(b.cons_amount) as cons_amount, max(b.transaction_date) as transaction_date, b.batch_lot as lot, c.transfer_system_id as recv_number, c.transfer_date as receive_date,b.expire_date, 0 as supplier_id
				from product_details_master a, inv_transaction b, inv_item_transfer_mst c
				where a.id=b.prod_id and b.mst_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.entry_form=4 and b.transaction_type in(1) and b.company_id='$cbo_company_name' $sql_cond $rcv_date_cond
				group by a.id, a.company_id, a.supplier_id,a.re_order_label, a.item_code, a.item_category_id, a.item_group_id, a.unit_of_measure, a.item_description, a.sub_group_name, c.transfer_system_id, c.transfer_date, b.batch_lot,b.expire_date
				order by id, recv_number, receive_date";
			}
			else
			{
				$sql="select a.id, a.company_id, a.supplier_id,a.re_order_label, a.item_code, a.item_category_id, a.item_group_id, a.unit_of_measure, a.item_description, a.sub_group_name,  listagg(cast(b.id as varchar(4000)),',') within group(order by b.id) as trans_id, sum(b.cons_quantity) as cons_quantity, sum(b.cons_amount) as cons_amount, max(b.transaction_date) as transaction_date, b.batch_lot as lot, c.recv_number, c.receive_date,b.expire_date, c.supplier_id
				from product_details_master a, inv_transaction b, inv_receive_master c
				where a.id=b.prod_id and b.mst_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.entry_form=4 and b.transaction_type in(1,4) and b.company_id='$cbo_company_name' $sql_cond $rcv_date_cond
				group by a.id, a.company_id, a.supplier_id,a.re_order_label, a.item_code, a.item_category_id, a.item_group_id, a.unit_of_measure, a.item_description, a.sub_group_name, c.recv_number, c.receive_date, b.batch_lot,b.expire_date, c.supplier_id
				union all
				select a.id, a.company_id, a.supplier_id,a.re_order_label, a.item_code, a.item_category_id, a.item_group_id, a.unit_of_measure, a.item_description, a.sub_group_name,  listagg(cast(b.id as varchar(4000)),',') within group(order by b.id) as trans_id, sum(b.cons_quantity) as cons_quantity, sum(b.cons_amount) as cons_amount, max(b.transaction_date) as transaction_date, b.batch_lot as lot, c.transfer_system_id as recv_number, c.transfer_date as receive_date,b.expire_date, 0 as supplier_id
				from product_details_master a, inv_transaction b, inv_item_transfer_mst c
				where a.id=b.prod_id and b.mst_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.entry_form=4 and b.transaction_type in(5) and b.company_id='$cbo_company_name' $sql_cond $rcv_date_cond
				group by a.id, a.company_id, a.supplier_id,a.re_order_label, a.item_code, a.item_category_id, a.item_group_id, a.unit_of_measure, a.item_description, a.sub_group_name, c.transfer_system_id, c.transfer_date, b.batch_lot,b.expire_date
				order by id, recv_number, receive_date";
			}

			//echo $sql;//die;

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
							<th width="40">Prod. ID</th>
							<th width="100">Item Category</th>
							<th width="100">Item Group</th>
							<th width="80">Item Sub-group</th>
							<th width="140">Item Description</th>
							<th width="60">Lot</th>
							<th width="60">UOM</th>
							<th width="80">Closing Stock</th>
							<th width="70">Avg. Rate</th>
							<th width="80">Stock Value</th>
							<th width="110">Supplier Name</th>
							<th width="100">MRR No.</th>
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
										<td width="40" align="center"><? echo $row[csf("id")]; ?></td>
										<td width="100"><p><? echo $item_category[$row[csf("item_category_id")]]; ?></p></td>
										<td width="100"><p><? echo $itemgroupArr[$row[csf("item_group_id")]]; ?></p></td>
										<td width="80"><p><? echo $row[csf("sub_group_name")]; ?></p></td>
										<td width="140"><p><? echo $row[csf("item_description")]; ?></p></td>
										<td width="60" align="center"><p><? echo $row[csf("lot")]; ?></p></td>
										<td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></p></td>
										<td width="80" align="right" title="<? echo $totalRcv."=".$totalIssue; ?>"><p><? echo number_format($stockInHand,4); $tot_stock+=$stockInHand; ?></p></td>
										<td width="70" align="right"><p><? if(number_format($stockInHand,4)>0) echo number_format($avg_rate,4) ; else echo "0.00";?></p></td>
										<td width="80" align="right"><p><? if(number_format($stockInHand,4)>0) echo number_format($stock_value,3); else echo "0.00"; $tot_stock_value+=$stock_value; ?></p></td>
										<td width="110"><p><? echo $supplierArr[$row[csf("supplier_id")]]; ?></p></td>
										<td width="100"><p><? echo $row[csf("recv_number")]; ?></p></td>
										<td width="70" align="center"><p><? echo change_date_format($row[csf("receive_date")]); ?></p></td>
										<td width="70" align="center"><p><? echo change_date_format($row[csf("expire_date")]); ?></p></td>
										<td width="60" align="center"><p><? echo $ageOfDays; ?></p></td>
										<td align="center"><p><? echo $daysOnHand;?></p></td>
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
									<td width="40" align="center"><? echo $row[csf("id")]; ?></td>
									<td width="100"><p><? echo $item_category[$row[csf("item_category_id")]]; ?></p></td>
									<td width="100"><p><? echo $itemgroupArr[$row[csf("item_group_id")]]; ?></p></td>
									<td width="80"><p><? echo $row[csf("sub_group_name")]; ?></p></td>
									<td width="140"><p><? echo $row[csf("item_description")]; ?></p></td>
									<td width="60" align="center"><p><? echo $row[csf("lot")]; ?></p></td>
									<td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></p></td>
									<td width="80" align="right" title="<? echo $totalRcv."=".$totalIssue; ?>"><p><? echo number_format($stockInHand,4); $tot_stock+=$stockInHand; ?></p></td>
									<td width="70" align="right"><p><? if(number_format($stockInHand,4)>0) echo number_format($avg_rate,4); else echo "0.00";?></p></td>
									<td width="80" align="right"><p><? if(number_format($stockInHand,4)>0)echo number_format($stock_value,3); else echo "0.00"; $tot_stock_value+=$stock_value; ?></p></td>
									<td width="110"><p><? echo $supplierArr[$row[csf("supplier_id")]]; ?></p></td>
									<td width="100"><p><? echo $row[csf("recv_number")]; ?></p></td>
									<td width="70" align="center"><p><? echo change_date_format($row[csf("receive_date")]); ?></p></td>
									<td width="70" align="center"><p><? echo change_date_format($row[csf("expire_date")]); ?></p></td>
									<td width="60" align="center"><p><? echo $ageOfDays; ?></p></td>
									<td align="center"><p><? echo $daysOnHand;?></p></td>
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
							<th width="40">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="140">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">Total:</th>
							<th width="80" id="value_tot_stock"><? echo number_format($tot_stock,4);?></th>
							<th width="70">&nbsp;</th>
							<th width="80" id="value_tot_stock_value"><? echo number_format($tot_stock_value,2);?></th>
							<th width="110">&nbsp;</th>
							<th width="100">&nbsp;</th>
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
			if ($cbo_item_category_id>0) $category_cond=" and a.item_category_id='$cbo_item_category_id'";  else $category_cond=" and a.item_category_id in(5,6,7,23)";
			
			//$mrr_rate_sql=sql_select("select prod_id, sum(cons_quantity) as cons_quantiy, sum(cons_amount) as cons_amount from inv_transaction where status_active=1 and is_deleted=0 and transaction_type in(1,4,5) $category_cond $company_cond group by prod_id");
			//		$mrr_rate_arr=array();
			//		foreach($mrr_rate_sql as $row)
			//		{
			//			$mrr_rate_arr[$row[csf("prod_id")]]=$row[csf("cons_amount")]/$row[csf("cons_quantiy")];
			//		}
			
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
			

			$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
			$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
			$storeArr = return_library_array("select id,store_name from lib_store_location where status_active=1 and is_deleted=0","id","store_name");
			//$itemnameArr = return_library_array("select id,item_name from lib_item_creation where status_active=1 and is_deleted=0","id","item_name");
			$itemgroupArr = return_library_array("select id,item_name from  lib_item_group where status_active=1 and is_deleted=0 and item_category in(5,6,7,23)","id","item_name");

			$sql_loan_cond="";
			if ($cbo_company_name>0) $sql_loan_cond =" and a.company_id='$cbo_company_name'";
			if ($cbo_item_category_id==0) $sql_loan_cond.=" and a.item_category in(5,6,7,23)"; else $sql_loan_cond.=" and a.item_category='$cbo_item_category_id'";
			if ($item_account_id>0) $sql_loan_cond.=" and a.prod_id in ($item_account_id)";
			//if ($cbo_store_name>0)  $sql_loan_cond.=" and a.store_id=$cbo_store_name ";
			if ($cbo_store_name != "")  $sql_loan_cond.=" and a.store_id in($cbo_store_name) ";

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
			//echo $sql_loan;die;
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
			$search_cond="";
			if ($cbo_company_name > 0) $search_cond =" and a.company_id='$cbo_company_name'";
			if ($cbo_item_category_id == 0) $search_cond.=" and b.item_category_id in(5,6,7,23)"; else $search_cond.=" and b.item_category_id='$cbo_item_category_id'";
			if ($cbo_item_category_id == 0) $search_cond.=" and a.item_category in(5,6,7,23)"; else $search_cond.=" and a.item_category='$cbo_item_category_id'";
			if ($item_account_id > 0) $search_cond.=" and b.id in ($item_account_id)";
			if ($item_group_id > 0) $search_cond.=" and b.item_group_id in($item_group_id)";
			//if ($cbo_store_name > 0) $search_cond.=" and a.store_id=$cbo_store_name ";
			
			if ($cbo_store_name != "")  $search_cond.=" and a.store_id in($cbo_store_name) ";


			//if($db_type==0)
			//		{
			//			$sql="Select b.id as prod_id,b.re_order_label,
			//			sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
			//			sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as iss_total_opening,
			//			sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as rcv_total_opening_amt,
			//			sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as iss_total_opening_amt,
			//			sum(case when a.transaction_type=1 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as purchase,
			//            sum(case when a.transaction_type in(1,4,5) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as purchase_amount,
			//            sum(case when a.transaction_type in(2,3,6) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as issue_amount,
			//			sum(case when a.transaction_type=2 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as issue,
			//			sum(case when a.transaction_type in(4) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as issue_return,
			//			sum(case when a.transaction_type in(3) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as receive_return,
			//
			//			sum(case when a.transaction_type in(6) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as item_transfer_issue,
			//			sum(case when a.transaction_type in(5) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as item_transfer_receive,
			//			sum(case when a.transaction_type in(1,4) and a.receive_basis in(1,2) then a.cons_quantity else 0 end) as rcv_total_wo,
			//			group_concat(a.batch_lot) as lot_no
			//			from inv_transaction a, product_details_master b
			//			where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type in (1,2,3,4,5,6) and a.order_id=0 $search_cond  
			//			group by b.id, b.re_order_label 
			//			order by b.id ASC";
			//		}
			//		else if($db_type==2)
			//		{
			//			$sql="Select b.id as prod_id,b.re_order_label,
			//			sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
			//			sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as iss_total_opening,
			//			sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as rcv_total_opening_amt,
			//			sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as iss_total_opening_amt,
			//			sum(case when a.transaction_type=1 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as purchase,
			//            sum(case when a.transaction_type in(1,4,5) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as purchase_amount,
			//            sum(case when a.transaction_type in(2,3,6) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as issue_amount,
			//			sum(case when a.transaction_type=2 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as issue,
			//			sum(case when a.transaction_type in(4) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as issue_return,
			//			sum(case when a.transaction_type in(3) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as receive_return,
			//
			//			sum(case when a.transaction_type in(6) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as item_transfer_issue,
			//			sum(case when a.transaction_type in(5) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as item_transfer_receive,
			//			sum(case when a.transaction_type in(1,4) and a.receive_basis in(1,2) then a.cons_quantity else 0 end) as rcv_total_wo,
			//			listagg(cast(a.batch_lot as varchar(4000)), ',') within group (order by a.batch_lot)  as lot_no
			//			from inv_transaction a, product_details_master b
			//			where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and  b.is_deleted=0 and a.transaction_type in (1,2,3,4,5,6) and a.order_id=0 $search_cond 
			//			group by b.id, b.re_order_label 
			//			order by b.id ASC";
			//		}
			//		//echo $sql;die;
			//		$trnasactionData = sql_select($sql);
			//		$data_array=array();
			//		foreach($trnasactionData as $row_p)
			//		{
			//			$data_array[$row_p[csf("prod_id")]]['rcv_total_opening']=$row_p[csf("rcv_total_opening")];
			//			$data_array[$row_p[csf("prod_id")]]['iss_total_opening']=$row_p[csf("iss_total_opening")];
			//			$data_array[$row_p[csf("prod_id")]]['rcv_total_opening_amt']=$row_p[csf("rcv_total_opening_amt")];
			//			$data_array[$row_p[csf("prod_id")]]['iss_total_opening_amt']=$row_p[csf("iss_total_opening_amt")];
			//			$data_array[$row_p[csf("prod_id")]]['purchase']=$row_p[csf("purchase")];
			//			$data_array[$row_p[csf("prod_id")]]['issue']=$row_p[csf("issue")];
			//			$data_array[$row_p[csf("prod_id")]]['issue_return']=$row_p[csf("issue_return")];
			//			$data_array[$row_p[csf("prod_id")]]['receive_return']=$row_p[csf("receive_return")];
			//			$data_array[$row_p[csf("prod_id")]]['item_transfer_issue']=$row_p[csf("item_transfer_issue")];
			//			$data_array[$row_p[csf("prod_id")]]['item_transfer_receive']=$row_p[csf("item_transfer_receive")];
			//			$data_array[$row_p[csf("prod_id")]]['rcv_total_wo']=$row_p[csf("rcv_total_wo")];
			//			$data_array[$row_p[csf("prod_id")]]['lot_no']=implode(",",array_unique(explode(",",$row_p[csf("lot_no")])));
			//			$data_array[$row_p[csf("prod_id")]]['purchase_amount']=$row_p[csf("purchase_amount")];
			//			$data_array[$row_p[csf("prod_id")]]['issue_amount']=$row_p[csf("issue_amount")];
			//		}
			
			
			$trans_sql="SELECT b.id as PROD_ID, a.transaction_date as TRANSACTION_DATE, a.transaction_type as TRANSACTION_TYPE, a.cons_quantity as CONS_QUANTITY, a.cons_amount as CONS_AMOUNT, a.batch_lot as LOT_NO, a.receive_basis as RECEIVE_BASIS  from inv_transaction a, product_details_master b
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
				
				if($row_p["TRANSACTION_TYPE"]==1 &&($row_p["RECEIVE_BASIS"]==1 || $row_p["RECEIVE_BASIS"]==2))
				{
					$data_array[$row_p["PROD_ID"]]['rcv_total_wo']+=$row_p["CONS_QUANTITY"];
				}
				
				if(strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date))
				{
					if($row_p["TRANSACTION_TYPE"]==1 || $row_p["TRANSACTION_TYPE"]==4 || $row_p["TRANSACTION_TYPE"]==5)
					{
						$data_array[$row_p["PROD_ID"]]['tot_stock_qnty']+=$row_p["CONS_QUANTITY"];
						$data_array[$row_p["PROD_ID"]]['tot_stock_amount']+=$row_p["CONS_AMOUNT"];
					}
					else
					{
						$data_array[$row_p["PROD_ID"]]['tot_stock_qnty']-=$row_p["CONS_QUANTITY"];
						$data_array[$row_p["PROD_ID"]]['tot_stock_amount']-=$row_p["CONS_AMOUNT"];
					}
				}
			}
			unset($trnasactionData);
			
			//echo "<pre>";print_r($data_array);die;
			$returnRes_date="select prod_id as PROD_ID, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where is_deleted=0 and status_active=1 and item_category in(5,6,7,23) group by prod_id";
			//echo $returnRes_date;die;
			$result_returnRes_date = sql_select($returnRes_date);
			foreach($result_returnRes_date as $row)
			{
				$date_array[$row["PROD_ID"]]['min_date']=$row["MIN_DATE"];
				$date_array[$row["PROD_ID"]]['max_date']=$row["MAX_DATE"];
			}


			$wo_qty_arr=return_library_array("select b.item_id, sum(b.supplier_order_quantity) as qty from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.item_category in (5,6,7,23) and a.pay_mode<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by item_id","item_id","qty");

			$pi_qty_arr=return_library_array("select b.item_prod_id, sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id in (5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by item_prod_id","item_prod_id","qty");
			$i=1;
			//echo "<pre>";print_r($data_array);die;
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
					$pord_cond="";
					if ($cbo_item_category_id == 0) $pord_cond.=" and b.item_category_id in(5,6,7,23)"; else $pord_cond.=" and b.item_category_id='$cbo_item_category_id'";
					if ($item_account_id > 0) $pord_cond.=" and b.id in ($item_account_id)";
					if ($item_group_id > 0) $pord_cond.=" and b.item_group_id in($item_group_id)";
					$sql="select b.id, b.item_code, b.item_category_id,b.item_group_id,b.unit_of_measure,b.item_description,b.sub_group_name,b.re_order_label, b.current_stock,avg_rate_per_unit 
					from product_details_master b where b.status_active=1 and b.is_deleted=0 and b.company_id='$cbo_company_name' $pord_cond
					order by b.id";
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
						//if($data_array[$row[csf("id")]]['rcv_total_opening'] >0)
						//{
							//$openingRate = $data_array[$row[csf("id")]]['rcv_total_opening_amt']/$data_array[$row[csf("id")]]['rcv_total_opening'];
						//}
						//$openingBalanceValue = $openingBalance*$openingRate;
						$openingBalanceValue =$data_array[$row[csf("id")]]['rcv_total_opening_amt']-$data_array[$row[csf("id")]]['iss_total_opening_amt'];
						if($openingBalance==0)
						{
							$openingBalanceValue=0;
							$openingRate =0;
						}
						else
						{
							if($openingBalanceValue!=0 && $openingBalance !=0) $openingRate = $openingBalanceValue/$openingBalance; else $openingRate =0;
						}
						

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
							//$stockValue=$closingStock*$avg_rate;
							$stockValue=(($openingBalanceValue+$data_array[$row[csf("id")]]['purchase_amt']+$data_array[$row[csf("id")]]['issue_return_amt']+$data_array[$row[csf("id")]]['item_transfer_receive_amt'])-($data_array[$row[csf("id")]]['issue_amt']+$data_array[$row[csf("id")]]['receive_return_amt']+$data_array[$row[csf("id")]]['item_transfer_issue_amt']));
							if($closingStock==0)
							{
								$closing_rate=0;
								$stockValue=0;
							}
							else
							{
								if($stockValue!=0 && $closingStock!=0) $closing_rate=$stockValue/$closingStock; else $closing_rate=0;
							}
							
							

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
										<td width="70"><p><? echo $row[csf("item_code")]; ?></p></td>
										<td width="100"><p><? echo $item_category[$row[csf("item_category_id")]]; ?></p></td>
										<td width="100"><p><? echo $itemgroupArr[$row[csf("item_group_id")]]; ?></p></td>
										<td width="100"><p><? echo $row[csf("sub_group_name")]; ?></p></td>
										<td width="180"><p><? echo $row[csf("item_description")]; ?></p></td>
										<td width="70"><p><? echo $row[csf("item_size")]; ?></p></td>
										<td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></p></td>
										<td width="100" align="right"><p><? echo number_format($openingRate,3); ?></p></td>
										<td width="100" align="right"><p><? echo number_format($openingBalance,3); $tot_open_bl+=$openingBalance; ?></p></td>
										<td width="110" align="right"><p><? echo number_format($openingBalanceValue,3); $tot_open_bl_amt+=$openingBalanceValue; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($purchase_qnty,3); $tot_purchase+=$purchase_qnty; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($loan_receive,3); $tot_loan_rcv+=$loan_receive; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($transfer_in_qty,3); $tot_transfer_in_qty+=$transfer_in_qty; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['issue_return'],3); $tot_issue_return+=$data_array[$row[csf("id")]]['issue_return'];//$row[csf("issue_return")]; ?></p></td>
										<td width="100" align="right"><p><? echo number_format($totalReceive,3); $tot_total_receive+=$totalReceive; ?></p></td>
										<td width="100" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['purchase_amt'],3); $tot_purchase_amount+=$data_array[$row[csf("id")]]['purchase_amt'];?></p></td>
										<td width="80" align="right"><p><? echo number_format($issue_qnty,3); $tot_issue+=$issue_qnty; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($loan_issue,3); $tot_loan_issue+=$loan_issue; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($transfer_out_qty,3); $tot_transfer_out_qty+=$transfer_out_qty; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['receive_return'],3); $tot_rec_return+=$data_array[$row[csf("id")]]['receive_return'];//$issue_result[0][csf('receive_return')]; ?></p></td>
										<td width="100" align="right"><p><? echo number_format($totalIssue,3); $tot_total_issue+=$totalIssue; ?></p></td>
										<td width="100" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['issue_amt'],3); $tot_issue_amount+=$data_array[$row[csf("id")]]['issue_amt'];?></p></td>
										<td width="100" align="right"><p><? echo number_format($closingStock,3); $tot_closing_stock+=$closingStock; ?></p></td>
										<td width="80" align="right"><p><? if(number_format($closingStock,3)>0) echo number_format($closing_rate,2); else echo "0.00"; //number_format($row[csf("avg_rate")],2); $tot_avg_rate+=$row[csf("avg_rate")]; ?></p></td>
										<td width="120" align="right"><p><? if(number_format($closingStock,3)>0) echo number_format($stockValue,2); else echo "0.00"; $tot_stock_value+=$stockValue; ?></p></td>
										<td width="60" align="center"><p><? echo $daysOnHand;//$daysOnHand; ?></p></td>
										<td width="100" align="right" title="<? echo $wo_qty_arr[$row[csf("id")]]."==".$pi_qty_arr[$row[csf("id")]]; ?>"><a href="##" onclick="fnc_pipeLine_details('<? echo $row[csf("id")];?>','pipe_line_popup')"><p><? echo number_format($pipeLine_qty,2); ?></p></a></td>
										<td><p><? echo $data_array[$row[csf("id")]]['lot_no']; ?></p></td>
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
										<td width="70"><p><? echo $row[csf("item_code")]; ?></p></td>
										<td width="100"><p><? echo $item_category[$row[csf("item_category_id")]]; ?></p></td>
										<td width="100"><p><? echo $itemgroupArr[$row[csf("item_group_id")]]; ?></p></td>
										<td width="100"><p><? echo $row[csf("sub_group_name")]; ?></p></td>
										<td width="180"><p><? echo $row[csf("item_description")]; ?></p></td>
										<td width="70"><p><? echo $row[csf("item_size")]; ?></p></td>
										<td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></p></td>
										<td width="100" align="right"><p><? echo number_format($openingRate,3); ?></p></td>
										<td width="100" align="right"><p><? echo number_format($openingBalance,3); $tot_open_bl+=$openingBalance; ?></p></td>
										<td width="110" align="right"><p><? echo number_format($openingBalanceValue,3); $tot_open_bl_amt+=$openingBalanceValue; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($purchase_qnty,3); $tot_purchase+=$purchase_qnty; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($loan_receive,3); $tot_loan_rcv+=$loan_receive; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($transfer_in_qty,3); $tot_transfer_in_qty+=$transfer_in_qty; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['issue_return'],3); $tot_issue_return+=$data_array[$row[csf("id")]]['issue_return'];//$row[csf("issue_return")]; ?></p></td>
										<td width="100" align="right"><p><? echo number_format($totalReceive,3); $tot_total_receive+=$totalReceive; ?></p></td>
										<td width="100" align="right" title="<?= $row[csf("id")];?>"><p><? echo number_format($data_array[$row[csf("id")]]['purchase_amt'],3);$tot_purchase_amount+=$data_array[$row[csf("id")]]['purchase_amt'];?></p></td>
										<td width="80" align="right"><p><? echo number_format($issue_qnty,3); $tot_issue+=$issue_qnty; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($loan_issue,3); $tot_loan_issue+=$loan_issue; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($transfer_out_qty,3); $tot_transfer_out_qty+=$transfer_out_qty; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['receive_return'],3); $tot_rec_return+=$data_array[$row[csf("id")]]['receive_return'];//$issue_result[0][csf('receive_return')]; ?></p></td>
										<td width="100" align="right"><p><? echo number_format($totalIssue,3); $tot_total_issue+=$totalIssue; ?></p></td>
										<td width="100" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['issue_amt'],3); $tot_issue_amount+=$data_array[$row[csf("id")]]['issue_amt'];?></p></td>
										<td width="100" align="right"><p><? echo number_format($closingStock,3); $tot_closing_stock+=$closingStock; ?></p></td>
										<td width="80" align="right"><p><? if(number_format($closingStock,3)>0) echo number_format($closing_rate,2); else echo "0.00"; //number_format($row[csf("avg_rate")],2); $tot_avg_rate+=$row[csf("avg_rate")]; ?></p></td>
										<td width="120" align="right"><p><? if(number_format($closingStock,3)>0) echo number_format($stockValue,2); else echo "0.00"; $tot_stock_value+=$stockValue; ?></p></td>
										<td width="60" align="center"><p><? echo $daysOnHand;//$daysOnHand; ?></p></td>
										<td width="100" align="right" title="<? echo $wo_qty_arr[$row[csf("id")]]."==".$pi_qty_arr[$row[csf("id")]]; ?>"><a href="##" onclick="fnc_pipeLine_details('<? echo $row[csf("id")];?>','pipe_line_popup')"><p><? echo number_format($pipeLine_qty,2); ?></p></a></td>
										<td><p><? echo $data_array[$row[csf("id")]]['lot_no']; ?></p></td>
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
			if ($cbo_item_category_id>0) $category_cond=" and a.item_category_id='$cbo_item_category_id'";  else $category_cond=" and a.item_category_id in(5,6,7,23)";
			$mrr_rate_sql=sql_select("select prod_id, sum(cons_quantity) as cons_quantiy, sum(cons_amount) as cons_amount from inv_transaction where status_active=1 and is_deleted=0 and transaction_type in(1,4,5) $category_cond $company_cond group by prod_id");
			$mrr_rate_arr=array();
			foreach($mrr_rate_sql as $row)
			{
				$mrr_rate_arr[$row[csf("prod_id")]]=$row[csf("cons_amount")]/$row[csf("cons_quantiy")];
			}
			if ($cbo_company_name==0) $company_id =""; else $company_id =" and a.company_id='$cbo_company_name'";
			if ($cbo_item_category_id==0) $item_category_id=" and b.item_category_id in(5,6,7,23)"; else $item_category_id=" and b.item_category_id='$cbo_item_category_id'";
			if ($item_account_id==0) $item_account=""; else $item_account=" and b.id in ($item_account_id)";
			if ($item_group_id==0) $group_id=""; else $group_id=" and b.item_group_id in($item_group_id)";
			//if ($cbo_store_name==0)  $store_id="";  else  $store_id=" and a.store_id=$cbo_store_name ";
			$store_id=""; 
			if ($cbo_store_name != "")  $store_id.=" and a.store_id in($cbo_store_name) ";

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
				sum(case when a.transaction_type in(1,4,5) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as rcv_total_amt,
				sum(case when a.transaction_type in(2,3,6) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as iss_total_amt,
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
				sum(case when a.transaction_type in(1,4,5) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as rcv_total_amt,
				sum(case when a.transaction_type in(2,3,6) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as iss_total_amt,
				sum(case when a.transaction_type in(6) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as item_transfer_issue,
				sum(case when a.transaction_type in(5) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as item_transfer_receive,
				sum(case when a.transaction_type in(1,4) and a.receive_basis in(1,2) then a.cons_quantity else 0 end) as rcv_total_wo,
				listagg(cast(a.batch_lot as varchar(4000)), ',') within group (order by a.batch_lot)  as lot_no
				from inv_transaction a, product_details_master b
				where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and  b.is_deleted=0 and a.transaction_type in (1,2,3,4,5,6) and b.item_category_id in (5,6,7,23) and  a.item_category in (5,6,7,23) and a.order_id=0 $company_id $group_id $prod_cond $store_id $item_account $search_cond  
				group by b.id 
				order by b.id ASC";
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
				$data_array[$row_p[csf("prod_id")]]['rcv_total_amt']=$row_p[csf("rcv_total_amt")];
				$data_array[$row_p[csf("prod_id")]]['iss_total_amt']=$row_p[csf("iss_total_amt")];
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
						if($openingBalance==0)
						{
							$openingBalanceValue=0;
							$openingRate=0;
						}
						else
						{
							if($openingBalance!=0 && $openingBalanceValue!=0) $openingRate=$openingBalanceValue/$openingBalance; else $openingRate=0;
						}
						
						$totalReceive = $data_array[$row[csf("id")]]['purchase']+$data_array[$row[csf("id")]]['issue_return']+$transfer_in_qty;//+$openingBalance
						$totalIssue = $data_array[$row[csf("id")]]['issue']+$data_array[$row[csf("id")]]['receive_return']+$transfer_out_qty;
						$closingStock=$openingBalance+$totalReceive-$totalIssue;
						$stockValue=$openingBalanceValue+$data_array[$row[csf("id")]]['rcv_total_amt']-$data_array[$row[csf("id")]]['iss_total_amt'];
						if($closingStock==0)
						{
							$stockValue=0;
							$avg_rate=0;
						}
						else
						{
							if($closingStock!=0 && $stockValue!=0) $avg_rate=$stockValue/$closingStock; else $avg_rate=0;
						}
						
						//$closingStock=$opening+$totalReceive-$totalIssue;
						if((($get_upto==1 && $ageOfDays>$txt_days) || ($get_upto==2 && $ageOfDays<$txt_days) || ($get_upto==3 && $ageOfDays>=$txt_days) || ($get_upto==4 && $ageOfDays<=$txt_days) || ($get_upto==5 && $ageOfDays==$txt_days) || $get_upto==0) && (($get_upto_qnty==1 && $closingStock>$txt_qnty) || ($get_upto_qnty==2 && $closingStock<$txt_qnty) || ($get_upto_qnty==3 && $closingStock>=$txt_qnty) || ($get_upto_qnty==4 && $closingStock<=$txt_qnty) || ($get_upto_qnty==5 && $closingStock==$txt_qnty) || $get_upto_qnty==0))
						{
							//$avg_rate=$mrr_rate_arr[$row[csf("id")]];
							//$stockValue=$closingStock*$avg_rate;

							$re_order_label = $row[csf('re_order_label')];
							if($closingStock <= $re_order_label){$bgcolor="red";}elseif($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}

							$pipeLine_qty=$wo_qty_arr[$row[csf("id")]]+$pi_qty_arr[$row[csf("id")]]-$data_array[$row[csf("id")]]['rcv_total_wo'];
							if($pipeLine_qty<0) $pipeLine_qty=0;
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" style=" word-wrap:break-word; <? echo $stylecolor; ?> "  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="50" align="center"><? echo $i; ?></td>
								<td width="60"><? echo $row[csf("id")]; ?></td>
								<td width="70"><p><? echo $row[csf("item_code")]; ?></p></td>
								<td width="100"><p><? echo $item_category[$row[csf("item_category_id")]]; ?></p></td>
								<td width="100"><p><? echo $itemgroupArr[$row[csf("item_group_id")]]; ?></p></td>
								<td width="100"><p><? echo $row[csf("sub_group_name")]; ?></p></td>
								<td width="180"><p><? echo $row[csf("item_description")]; ?></p></td>
								<td width="70"><p><? echo $row[csf("item_size")]; ?></p></td>
								<td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></p></td>
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
								<td width="60" align="center"><p><? echo $daysOnHand;//$daysOnHand; ?></p></td>
								<td width="100" align="right" title="<? echo $wo_qty_arr[$row[csf("id")]]."==".$pi_qty_arr[$row[csf("id")]]; ?>"><a href="##" onclick="fnc_pipeLine_details('<? echo $row[csf("id")];?>','pipe_line_popup')"><p><? echo number_format($pipeLine_qty,2); ?></p></a></td>
								<td><p><? echo $data_array[$row[csf("id")]]['lot_no']; ?></p></td>
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
			if ($cbo_item_category_id>0) $category_cond=" and a.item_category_id='$cbo_item_category_id'";  else $category_cond=" and a.item_category_id in(5,6,7,23)";
			$mrr_rate_sql=sql_select("select prod_id, sum(cons_quantity) as cons_quantiy, sum(cons_amount) as cons_amount from inv_transaction where status_active=1 and is_deleted=0 and transaction_type in(1,4,5) $category_cond $company_cond group by prod_id");
			$mrr_rate_arr=array();
			foreach($mrr_rate_sql as $row)
			{
				$mrr_rate_arr[$row[csf("prod_id")]]=$row[csf("cons_amount")]/$row[csf("cons_quantiy")];
			}
			if ($cbo_company_name==0) $company_id =""; else $company_id =" and a.company_id='$cbo_company_name'";
			if ($cbo_company_name==0) $company_cond =""; else $company_cond =" and b.company_id='$cbo_company_name'";
			if ($cbo_item_category_id==0) $item_category_id=" and b.item_category_id in(5,6,7,23)"; else $item_category_id=" and b.item_category_id='$cbo_item_category_id'";
			if ($item_account_id==0) $item_account=""; else $item_account=" and b.id in ($item_account_id)";
			if ($item_group_id==0) $group_id=""; else $group_id=" and b.item_group_id in($item_group_id)";
			//if ($cbo_store_name==0)  $store_id="";  else  $store_id=" and a.store_id=$cbo_store_name ";
			$store_id="";
			if ($cbo_store_name != "")  $store_id.=" and a.store_id in($cbo_store_name) ";

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
				//if($data_array[$row[csf("id")]]['rcv_total_opening'] >0)
				//{
					//$openingRate = $data_array[$row[csf("id")]]['rcv_total_opening_amt']/$data_array[$row[csf("id")]]['rcv_total_opening'];
				//}
				$openingBalanceValue =  $data_array[$row[csf("id")]]['rcv_total_opening_amt']-$data_array[$row[csf("id")]]['iss_total_opening_amt'];




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
						//$stockValue=$closingStock*$avg_rate;

						$summary_data[$row[csf("company_id")]][$row[csf("item_category_id")]]["openingBalanceValue"] += $openingBalanceValue;

						$summary_data[$row[csf("company_id")]][$row[csf("item_category_id")]]["purchase"] += $data_array[$row[csf("id")]]['purchase_amt'];
						$summary_data[$row[csf("company_id")]][$row[csf("item_category_id")]]["transfer_in_qty"] += $transfer_in_qty_amt;
						$summary_data[$row[csf("company_id")]][$row[csf("item_category_id")]]["issue_return"] += $data_array[$row[csf("id")]]['issue_return_amt'];
						$summary_data[$row[csf("company_id")]][$row[csf("item_category_id")]]["totalReceive"] += $totalReceive_amt;


						$summary_data[$row[csf("company_id")]][$row[csf("item_category_id")]]["issue_qty"] += $issue_qty_amt;
						$summary_data[$row[csf("company_id")]][$row[csf("item_category_id")]]["transfer_out_qty"] += $transfer_out_qty_amt;
						$summary_data[$row[csf("company_id")]][$row[csf("item_category_id")]]["receive_return"] += $data_array[$row[csf("id")]]['receive_return_amt'];
						$summary_data[$row[csf("company_id")]][$row[csf("item_category_id")]]["totalIssue"] += $totalIssue_amt;

						$summary_data[$row[csf("company_id")]][$row[csf("item_category_id")]]["stockValue"] += $closingStock_amt;
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
							<th rowspan="2" width="">Closing Value Tk</th>
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
							<td width="120"><p><? echo $item_category[$category_id]; ?></p></td>
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
			if ($item_group_id==0) $group_id=""; else $group_id=" and b.item_group_id in($item_group_id)";
			if ($item_account_id==0) $item_account=""; else $item_account=" and a.prod_id in ($item_account_id)";
			if ($item_account_id==0) $item_account_cond=""; else $item_account_cond=" and b.id in ($item_account_id)";
			//if ($cbo_store_name==0)  $store_id="";  else  $store_id=" and a.store_id=$cbo_store_name ";
			$store_id="";
			if ($cbo_store_name != "")  $store_id.=" and a.store_id in($cbo_store_name) ";


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
			
			$trans_sql="SELECT b.id as PROD_ID, a.transaction_date as TRANSACTION_DATE, a.transaction_type as TRANSACTION_TYPE, a.cons_quantity as CONS_QUANTITY, a.cons_amount as CONS_AMOUNT, a.batch_lot as LOT_NO from inv_transaction a, product_details_master b
			where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id $group_id $prod_cond $store_id $item_account $search_cond $item_category_id $item_category_cond order by b.id, a.transaction_date ASC";
			
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
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['last_receive_date']=$row_p["TRANSACTION_DATE"];
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
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['last_issue_date']=$row_p["TRANSACTION_DATE"];
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
			$table_width=2648;
			ob_start();
			?>
		
			<div align="center" style="height:auto; width:auto; margin:0 auto; padding:0; width:<? echo $table_width; ?>">
				<table width="<? echo $table_width; ?>px" cellpadding="0" cellspacing="0" id="caption" align="center">
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
				<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $table_width; ?>" rules="all" id="rpt_table_header">
					<thead>
						<tr>
							<th rowspan="2" width="50">SL</th>
							<th colspan="9">Description</th>
							<th rowspan="2" width="100"> Opneing Rate</th>
							<th rowspan="2" width="100">Opening Stock</th>
							<th rowspan="2" width="110">Opening Value</th>
							<th colspan="5">Receive</th>
							<th colspan="5">Issue</th>
							<th rowspan="2" width="100">Closing Stock</th>
							<th rowspan="2" width="80">Avg. Rate</th>
							<th rowspan="2" width="120">Stock Value</th>
							<th rowspan="2" width="60">Age</th>
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
							<th width="100">Last Receive Date</th>
							<th width="80">Issue</th>
							<th width="80">Transfer Out</th>
							<th width="80">Receive Return</th>
							<th width="100">Total Issue</th>
							<th width="100">Last issue Date</th>
						</tr>
					</thead>
	
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

						$last_receive_date=change_date_format($data_array[$row[csf("id")]][$row[csf("lot")]]['last_receive_date']);
						$last_issue_date=change_date_format($data_array[$row[csf("id")]][$row[csf("lot")]]['last_issue_date']);

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
										<td width="100"><p><? echo $row[csf("lot")]; ?></p></td>
										<td width="70"><p><? echo $row[csf("item_code")]; ?></p></td>
										<td width="100"><p><? echo $item_category[$row[csf("item_category_id")]]; ?></p></td>
										<td width="100"><p><? echo $itemgroupArr[$row[csf("item_group_id")]]; ?></p></td>
										<td width="100"><p><? echo $row[csf("sub_group_name")]; ?></p></td>
										<td width="180"><p><? echo $row[csf("item_description")]; ?></p></td>
										<td width="70"><p><? echo $row[csf("item_size")]; ?></p></td>
										<td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></p></td>
										<td width="100" align="right"><p><? echo number_format($openingRate,4); ?></p></td>
										<td width="100" align="right"><p><? echo number_format($openingBalance,4); $tot_open_bl+=$openingBalance; ?></p></td>
										<td width="110" align="right"><p><? echo number_format($openingBalanceValue,3); $tot_open_bl_amt+=$openingBalanceValue; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['purchase'],3); $tot_purchase+=$data_array[$row[csf("id")]]['purchase']; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($transfer_in_qty,3); $tot_transfer_in_qty+=$transfer_in_qty; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['issue_return'],3); $tot_issue_return+=$data_array[$row[csf("id")]]['issue_return'];//$row[csf("issue_return")]; ?></p></td>
										<td width="100" align="right"><p><? echo number_format($totalReceive,3); $tot_total_receive+=$totalReceive; ?></p></td>
										<td width="100" align="right"><p><? echo $last_receive_date; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($issue_qty,3); $tot_issue+=$issue_qty; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($transfer_out_qty,3); $tot_transfer_out_qty+=$transfer_out_qty; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['receive_return'],3); $tot_rec_return+=$data_array[$row[csf("id")]]['receive_return'];//$issue_result[0][csf('receive_return')]; ?></p></td>
										<td width="100" align="right"><p><? echo number_format($totalIssue,3); $tot_total_issue+=$totalIssue; ?></p></td>
										<td width="100" align="right"><p><? echo $last_issue_date; ?></p></td>
										<td width="100" align="right"><p><? echo number_format($closingStock,4); $tot_closing_stock+=$closingStock; ?></p></td>
										<td width="80" align="right"><p><? if(number_format($closingStock,4)>0) echo number_format($avg_rate,4); else echo "0.00"; //number_format($row[csf("avg_rate")],2); $tot_avg_rate+=$row[csf("avg_rate")]; ?></p></td>
										<td width="120" align="right"><p><? if(number_format($closingStock,4)>0) echo number_format($stockValue,3); else echo "0.00"; $tot_stock_value+=$stockValue; ?></p></td>
										<td width="60" align="center"><p><? echo $ageOfDays; ?>&nbsp;</p></td>
										<td width="60" align="center"><p><? echo $daysOnHand;//$daysOnHand; ?></p></td>
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
											<td align="right" ><p></p></td>	
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
										<td align="center"><? echo $i; ?></td>
										<td><? echo $row[csf("id")]; ?></td>
										<td ><p><? echo $row[csf("lot")]; ?></p></td>
										<td><p><? echo $row[csf("item_code")]; ?></p></td>
										<td ><p><? echo $item_category[$row[csf("item_category_id")]]; ?></p></td>
										<td ><p><? echo $itemgroupArr[$row[csf("item_group_id")]]; ?></p></td>
										<td ><p><? echo $row[csf("sub_group_name")]; ?></p></td>
										<td><p><? echo $row[csf("item_description")]; ?></p></td>
										<td><p><? echo $row[csf("item_size")]; ?></p></td>
										<td align="center"><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></p></td>
										<td  align="right"><p><? echo number_format($openingRate,4); ?></p></td>
										<td  align="right"><p><? echo number_format($openingBalance,4); $tot_open_bl+=$openingBalance; ?></p></td>
										<td align="right"><p><? echo number_format($openingBalanceValue,3); $tot_open_bl_amt+=$openingBalanceValue; ?></p></td>
										<td align="right"><p><? echo number_format($data_array[$row[csf("id")]]['purchase'],3); $tot_purchase+=$data_array[$row[csf("id")]]['purchase']; ?></p></td>
										<td align="right"><p><? echo number_format($transfer_in_qty,3); $tot_transfer_in_qty+=$transfer_in_qty; ?></p></td>
										<td align="right"><p><? echo number_format($data_array[$row[csf("id")]]['issue_return'],3); $tot_issue_return+=$data_array[$row[csf("id")]]['issue_return'];//$row[csf("issue_return")]; ?></p></td>
										<td  align="right"><p><? echo number_format($totalReceive,3); $tot_total_receive+=$totalReceive; ?></p></td>
										<td  align="right"><p><? echo $last_receive_date; ?></p></td>
										<td align="right"><p><? echo number_format($issue_qty,3); $tot_issue+=$issue_qty; ?></p></td>
										<td align="right"><p><? echo number_format($transfer_out_qty,3); $tot_transfer_out_qty+=$transfer_out_qty; ?></p></td>
										<td align="right"><p><? echo number_format($data_array[$row[csf("id")]]['receive_return'],3); $tot_rec_return+=$data_array[$row[csf("id")]]['receive_return'];//$issue_result[0][csf('receive_return')]; ?></p></td>
										<td  align="right"><p><? echo number_format($totalIssue,3); $tot_total_issue+=$totalIssue; ?></p></td>
										<td  align="right"><p><? echo $last_issue_date; ?></p></td>
										<td  align="right"><p><? echo number_format($closingStock,4); $tot_closing_stock+=$closingStock; ?></p></td>
										<td align="right"><p><? if(number_format($closingStock,4)>0) echo number_format($avg_rate,4); else echo "0.00";//number_format($row[csf("avg_rate")],2); $tot_avg_rate+=$row[csf("avg_rate")]; ?></p></td>
										<td align="right"><p><? if(number_format($closingStock,4)>0) echo number_format($stockValue,3); else echo "0.00"; $tot_stock_value+=$stockValue; ?></p></td>
										<td align="center"><p><? echo $ageOfDays; ?>&nbsp;</p></td>
										<td align="center"><p><? echo $daysOnHand;//$daysOnHand; ?></p></td>
										<?
										if($prod_check[$row[csf("id")]]=="")
										{
											$prod_check[$row[csf("id")]]=$row[csf("id")];
											?>
											<td align="right" title="<? echo $wo_qty_arr[$row[csf("id")]]."==".$pi_qty_arr[$row[csf("id")]]."--".$rcv_qnty_array[$row[csf("id")]]; ?>"><? echo number_format($pipeLine_qty,2); ?></td>	
											<?									}
										else
										{
											?>
											<td align="right" ><p></p></td>	
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
	
					<tfoot>
						<tr>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th>Total:</th>
							<th align="right" id="value_tot_open_bl"><? echo number_format($tot_open_bl,4); ?></th>
							<th align="right" id="value_tot_open_bl_amt"><? echo number_format($tot_open_bl_amt,3); ?></th>
							<th align="right" id="value_tot_purchase"><? echo number_format($tot_purchase,3); ?></th>
							<th align="right" id="value_tot_transfer_in_qty"><? echo number_format($tot_transfer_in_qty,3); ?></th>
							<th align="right" id="value_tot_issue_return"><? echo number_format($tot_issue_return,3); ?></th>
							<th  align="right" id="value_tot_total_receive"><? echo number_format($tot_total_receive,3); ?></th>
							<th  align="right"></th>
							<th align="right" id="value_tot_issue"><? echo number_format($tot_issue,3); ?></th>
							<th align="right" id="value_tot_transfer_out_qty"><? echo number_format($tot_transfer_out_qty,3); ?></th>
							<th align="right" id="value_tot_rec_return"><? echo number_format($tot_rec_return,3); ?></th>
							<th align="right" id="value_tot_total_issue"><? echo number_format($tot_total_issue,3); ?></th>
							<th align="right"></th>
							<th align="right" id="value_tot_closing_stock"><? echo number_format($tot_closing_stock,4); ?></th>
							<th>&nbsp;</th>
							<th align="right" id="value_tot_stock_value"><p><? echo number_format($tot_stock_value,2); ?></p></th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th align="right" id="value_totalpipeLine_qty"><? echo number_format($totalpipeLine_qty,2); ?></th>
						</tr>
					</tfoot>
				</table>
			</div>
			<?
		}
		else if($report_type==7)
		{

			if ($cbo_company_name==0) $company_id =""; else $company_id =" and a.company_id='$cbo_company_name'";
			if ($cbo_item_category_id==0) $item_category_id=" and b.item_category_id in(5,6,7,23)"; else $item_category_id=" and b.item_category_id='$cbo_item_category_id'";
			if ($cbo_item_category_id==0) $item_category_cond=" and a.item_category in(5,6,7,23)"; else $item_category_cond=" and a.item_category='$cbo_item_category_id'";
			if ($item_group_id==0) $group_id=""; else $group_id=" and b.item_group_id in($item_group_id)";
			if ($item_account_id==0) $item_account=""; else $item_account=" and a.prod_id in ($item_account_id)";
			if ($item_account_id==0) $item_account_cond=""; else $item_account_cond=" and b.id in ($item_account_id)";
			//if ($cbo_store_name==0)  $store_id="";  else  $store_id=" and a.store_id=$cbo_store_name ";
			$store_id="";
			if ($cbo_store_name != "")  $store_id.=" and a.store_id in($cbo_store_name) ";

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
			// $supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
			$storeArr = return_library_array("select id,store_name from lib_store_location where status_active=1 and is_deleted=0","id","store_name");
			//$itemnameArr = return_library_array("select id,item_name from lib_item_creation where status_active=1 and is_deleted=0","id","item_name");
			$itemgroupArr = return_library_array("select id,item_name from lib_item_group where status_active=1 and is_deleted=0 and item_category in(5,6,7,23)","id","item_name");

			$trans_sql="SELECT b.id as PROD_ID, a.transaction_date as TRANSACTION_DATE, a.transaction_type as TRANSACTION_TYPE, a.cons_quantity as CONS_QUANTITY, a.cons_amount as CONS_AMOUNT, a.batch_lot as LOT_NO, a.receive_basis as RECEIVE_BASIS, a.STORE_ID from inv_transaction a, product_details_master b
			where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id $group_id $prod_cond $store_id $item_account $search_cond $item_category_id $item_category_cond order by b.id,a.id ASC";

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

				if($row_p["TRANSACTION_TYPE"]==1 &&($row_p["RECEIVE_BASIS"]==1 || $row_p["RECEIVE_BASIS"]==2))
				{
					$data_array[$row_p["PROD_ID"]]['rcv_total_wo']+=$row_p["CONS_QUANTITY"];
				}
				if($row_p["STORE_ID"]){ $data_array[$row_p["PROD_ID"]]['store_id']=$row_p["STORE_ID"];}
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


			$wo_qty_arr=return_library_array("select b.item_id, sum(b.supplier_order_quantity) as qty from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.item_category in (5,6,7,23) and a.pay_mode<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by item_id","item_id","qty");

			$pi_qty_arr=return_library_array("select b.item_prod_id, sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id in (5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by item_prod_id","item_prod_id","qty");

			if($cbo_store_wise==1)
			{
				$tbl_width="3000";
			}
			else
			{
				$tbl_width="2900";
			}
			$i=1;
			ob_start();
			$last30DaysConsumptionArr = array();
			$last90DaysConsumptionArr = array();
			$last365DaysConsumptionArr = array();
			$last30Data = sql_select("select a.prod_id as PROD_ID, round(sum(a.cons_quantity), 3) as CONS_QUANTITY, a.transaction_type as TRANSACTION_TYPE from inv_transaction a inner join product_details_master b on b.id = a.prod_id where a.transaction_date >= to_date('$from_date', 'dd-mm-yyyy') - 30 and a.transaction_date <= to_date('$from_date', 'dd-mm-yyyy') and a.transaction_type in (2,4) and b.status_active=1 and b.is_deleted=0 and b.company_id='$cbo_company_name' $item_category_id $group_id $prod_cond $item_account_cond $search_cond group by a.prod_id, a.transaction_type");
			if(count($last30Data)){
				foreach ($last30Data as $condata){
					if($condata["TRANSACTION_TYPE"] == 2){
						$last30DaysConsumptionArr[$condata["PROD_ID"]] += $condata["CONS_QUANTITY"];
					}else if($condata["TRANSACTION_TYPE"] == 4){
						$last30DaysConsumptionArr[$condata["PROD_ID"]] += (0 - $condata["CONS_QUANTITY"]);
					}
				}
			}
			$last90Data = sql_select("select a.prod_id as PROD_ID, round(sum(a.cons_quantity), 3) as CONS_QUANTITY, a.transaction_type as TRANSACTION_TYPE from inv_transaction a inner join product_details_master b on b.id = a.prod_id where a.transaction_date >= to_date('$from_date', 'dd-mm-yyyy') - 90 and a.transaction_date <= to_date('$from_date', 'dd-mm-yyyy') and a.transaction_type in (2,4) and b.status_active=1 and b.is_deleted=0 and b.company_id='$cbo_company_name' $item_category_id $group_id $prod_cond $item_account_cond $search_cond group by a.prod_id, a.transaction_type");
			if(count($last90Data)){
				foreach ($last90Data as $condata){
					if($condata["TRANSACTION_TYPE"] == 2){
						$last90DaysConsumptionArr[$condata["PROD_ID"]] += $condata["CONS_QUANTITY"];
					}else if($condata["TRANSACTION_TYPE"] == 4){
						$last90DaysConsumptionArr[$condata["PROD_ID"]] += (0 - $condata["CONS_QUANTITY"]);
					}
				}
			}
			$last365Data = sql_select("select a.prod_id as PROD_ID, round(sum(a.cons_quantity), 3) as CONS_QUANTITY, a.transaction_type as TRANSACTION_TYPE from inv_transaction a inner join product_details_master b on b.id = a.prod_id where a.transaction_date >= to_date('$from_date', 'dd-mm-yyyy') - 365 and a.transaction_date <= to_date('$from_date', 'dd-mm-yyyy') and a.transaction_type in (2,4) and b.status_active=1 and b.is_deleted=0 and b.company_id='$cbo_company_name' $item_category_id $group_id $prod_cond $item_account_cond $search_cond group by a.prod_id, a.transaction_type");
			if(count($last365Data)){
				foreach ($last365Data as $condata){
					if($condata["TRANSACTION_TYPE"] == 2){
						$last365DaysConsumptionArr[$condata["PROD_ID"]] += $condata["CONS_QUANTITY"];
					}else if($condata["TRANSACTION_TYPE"] == 4){
						$last365DaysConsumptionArr[$condata["PROD_ID"]] += (0 - $condata["CONS_QUANTITY"]);
					}
				}
			}
			?>
	
	

			<div align="center" style="height:auto;  margin:0 auto; padding:0; width:2348">
				<table width="2788px" cellpadding="0" cellspacing="0" id="caption" align="center">
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
				<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<?=$tbl_width;?>" rules="all" id="rpt_table_header">
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
						<?
						if($cbo_store_wise==1)
						{
							?>
							<th rowspan="2" width="100">Store Name</th>
							<?
						}
						?>
						<th rowspan="2" width="60">DOH</th>
						<th rowspan="2" width="100">Pipe Line</th>
						<th colspan="3">Consumption</th>
						<th rowspan="2" width="100">Days Coverage</th>
					</tr>
					<tr>
						<th width="60">Prod. ID</th>
						<th width="70">Item Code</th>
						<th width="100">Item Category</th>
						<th width="100">Item Group</th>
						<th width="100">Item Sub-group</th>
						<th width="180">Item Description</th>
						<th width="150">Lot</th>
						<th width="70">Item Size</th>
						<th width="70">UOM</th>
						<th width="80">Purchase</th>
						<th width="80">Transfer In</th>
						<th width="80">Issue Return</th>
						<th width="100">Total Received</th>
						<th width="80">Issue</th>
						<th width="80">Transfer Out</th>
						<th width="80">Receive Return</th>
						<th width="100">Total Issue</th>
						<th width="90">Last 1 Month</th>
						<th width="90">Last 3 Month</th>
						<th width="90">Last 1 Year</th>
					</tr>
					</thead>
				</table>
				<div style="width:<?=$tbl_width+18;?>px; max-height:250px; overflow-y:scroll; overflow-x:hidden;" id="scroll_body">
					<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="<?=$tbl_width;?>" rules="all" align="left">
						<?
						if ($cbo_item_category_id==0) $rcv_category_cond=" and b.item_category in(5,6,7,23)"; else $rcv_category_cond=" and b.item_category='$cbo_item_category_id'";
						//$rcv_qnty_array=return_library_array("SELECT b.prod_id, sum(b.cons_quantity) as cons_quantity from  inv_receive_master a,  inv_transaction b where a.id=b.mst_id and a.receive_basis in(1,2) and b.transaction_type=1 and b.status_active=1 $rcv_category_cond group by b.prod_id","prod_id","cons_quantity");

						$sql="SELECT b.id, b.item_code, b.item_category_id,b.item_group_id,b.unit_of_measure,b.item_description,b.sub_group_name, b.current_stock,avg_rate_per_unit, b.re_order_label from product_details_master b where b.status_active=1 and b.is_deleted=0 and b.company_id='$cbo_company_name' $item_category_id $group_id $prod_cond $item_account_cond $search_cond order by b.id";

						$result = sql_select($sql);
						$last30DaysConsumptionSum = 0;
						$last30DaysConsumptionSum = 0;
						$last30DaysConsumptionSum = 0;
						$daysCoverageSum = 0;
						foreach($result as $row)
						{
							$last30DaysConsumption = 0;
							$last90DaysConsumption = 0;
							$last365DaysConsumption = 0;

							if(isset($last30DaysConsumptionArr[$row[csf("id")]])){
								$last30DaysConsumption = $last30DaysConsumptionArr[$row[csf("id")]];
							}
							if(isset($last90DaysConsumptionArr[$row[csf("id")]])){
								$last90DaysConsumption = $last90DaysConsumptionArr[$row[csf("id")]];
							}
							if(isset($last365DaysConsumptionArr[$row[csf("id")]])){
								$last365DaysConsumption = $last365DaysConsumptionArr[$row[csf("id")]];
							}

							$re_order_label=$row[csf("re_order_label")];
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

							$openingBalance = $data_array[$row[csf("id")]]['rcv_total_opening']-$data_array[$row[csf("id")]]['iss_total_opening'];
							$openingBalanceValue = $data_array[$row[csf("id")]]['rcv_total_opening_amt']-$data_array[$row[csf("id")]]['iss_total_opening_amt'];
							if($openingBalance==0)
							{
								$openingBalanceValue=0;
								$openingRate=0;
							}
							else
							{
								if($openingBalanceValue>0 && $openingBalance>0) $openingRate=$openingBalanceValue/$openingBalance; else $openingRate=0;
							}

							$totalReceive = $data_array[$row[csf("id")]]['purchase']+$data_array[$row[csf("id")]]['issue_return']+$transfer_in_qty;//+$openingBalance
							$totalIssue = $data_array[$row[csf("id")]]['issue']+$data_array[$row[csf("id")]]['receive_return']+$transfer_out_qty;

							$totalReceive_amt = $data_array[$row[csf("id")]]['purchase_amt']+$data_array[$row[csf("id")]]['issue_return_amt']+$transfer_in_qty_amt;
							$totalIssue_amt = $data_array[$row[csf("id")]]['issue_amt']+$data_array[$row[csf("id")]]['receive_return_amt']+$transfer_out_qty_amt;

							$closingStock=$openingBalance+$totalReceive-$totalIssue;
							$stockValue=$openingBalanceValue+$totalReceive_amt-$totalIssue_amt;
							if($closingStock==0)
							{
								$stockValue=0;
								$avg_rate=0;
							}
							else
							{
								if($stockValue>0 && $closingStock>0) $avg_rate=$stockValue/$closingStock; else $avg_rate=0;
							}

							//$closingStock=$opening+$totalReceive-$totalIssue;
							if((($get_upto==1 && $ageOfDays>$txt_days) || ($get_upto==2 && $ageOfDays<$txt_days) || ($get_upto==3 && $ageOfDays>=$txt_days) || ($get_upto==4 && $ageOfDays<=$txt_days) || ($get_upto==5 && $ageOfDays==$txt_days) || $get_upto==0) && (($get_upto_qnty==1 && $closingStock>$txt_qnty) || ($get_upto_qnty==2 && $closingStock<$txt_qnty) || ($get_upto_qnty==3 && $closingStock>=$txt_qnty) || ($get_upto_qnty==4 && $closingStock<=$txt_qnty) || ($get_upto_qnty==5 && $closingStock==$txt_qnty) || $get_upto_qnty==0))
							{
								//$avg_rate=$mrr_rate_arr[$row[csf("id")]];
								//$stockValue=$closingStock*$avg_rate;
								$pipeLine_qty=$wo_qty_arr[$row[csf("id")]]+$pi_qty_arr[$row[csf("id")]]-$data_array[$row[csf("id")]]['rcv_total_wo'];

								if($pipeLine_qty<0) $pipeLine_qty=0;
								//echo $closingStock."=".$openingBalance."<br>";
								if($value_with==1)
								{
									if(number_format($closingStock,2)>0.00 || number_format($openingBalance,2)>0.00 )
									{
										if($closingStock <= $re_order_label){$bgcolor="red";}elseif($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" style=" word-wrap:break-word; <? echo $stylecolor; ?> "  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td width="50" class="wrd_brk" align="center"><? echo $i; ?></td>
											<td width="60" class="wrd_brk"><? echo $row[csf("id")]; ?></td>
											<td width="70" class="wrd_brk"><p><? echo $row[csf("item_code")]; ?></p></td>
											<td width="100" class="wrd_brk"><p><? echo $item_category[$row[csf("item_category_id")]]; ?></p></td>
											<td width="100" class="wrd_brk"><p><? echo $itemgroupArr[$row[csf("item_group_id")]]; ?></p></td>
											<td width="100" class="wrd_brk"><p><? echo $row[csf("sub_group_name")]; ?></p></td>
											<td width="180" class="wrd_brk"><p><? echo $row[csf("item_description")]; ?></p></td>
											<td class="wrd_brk" width="150"><p><? echo chop($data_array[$row[csf("id")]]['lot_no'],","); ?></p></td>
											<td width="70" class="wrd_brk"><p><? echo $row[csf("item_size")]; ?></p></td>
											<td width="70" class="wrd_brk" align="center"><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></p></td>
											<td width="100" class="wrd_brk" align="right"><p><? echo number_format($openingRate,4); ?></p></td>
											<td width="100" class="wrd_brk" align="right"><p><? echo number_format($openingBalance,4); $tot_open_bl+=$openingBalance; ?></p></td>
											<td width="110" class="wrd_brk" align="right"><p><? echo number_format($openingBalanceValue,3); $tot_open_bl_amt+=$openingBalanceValue; ?></p></td>
											<td width="80" class="wrd_brk" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['purchase'],3); $tot_purchase+=$data_array[$row[csf("id")]]['purchase']; ?></p></td>
											<td width="80" class="wrd_brk" align="right"><p><? echo number_format($transfer_in_qty,3); $tot_transfer_in_qty+=$transfer_in_qty; ?></p></td>
											<td width="80" class="wrd_brk" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['issue_return'],3); $tot_issue_return+=$data_array[$row[csf("id")]]['issue_return'];//$row[csf("issue_return")]; ?></p></td>
											<td width="100" class="wrd_brk" align="right"><p><? echo number_format($totalReceive,3); $tot_total_receive+=$totalReceive; ?></p></td>
											<td width="80" class="wrd_brk" align="right"><p><? echo number_format($issue_qty,3); $tot_issue+=$issue_qty; ?></p></td>
											<td width="80" class="wrd_brk" align="right"><p><? echo number_format($transfer_out_qty,3); $tot_transfer_out_qty+=$transfer_out_qty; ?></p></td>
											<td width="80" class="wrd_brk" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['receive_return'],3); $tot_rec_return+=$data_array[$row[csf("id")]]['receive_return'];//$issue_result[0][csf('receive_return')]; ?></p></td>
											<td width="100" class="wrd_brk" align="right"><p><? echo number_format($totalIssue,3); $tot_total_issue+=$totalIssue; ?></p></td>
											<td width="100" class="wrd_brk" align="right"><p><? echo number_format($closingStock,4); $tot_closing_stock+=$closingStock; ?></p></td>
											<td width="80" class="wrd_brk" align="right"><p><? if(number_format($closingStock,4)>0) echo number_format($avg_rate,4); else echo "0.00"; //number_format($row[csf("avg_rate")],2); $tot_avg_rate+=$row[csf("avg_rate")]; ?></p></td>
											<td width="120" class="wrd_brk" align="right"><p><? if(number_format($closingStock,4)>0) echo number_format($stockValue,3); else echo "0.00"; $tot_stock_value+=$stockValue; ?></p></td>
											<?
											if($cbo_store_wise==1)
											{
												?>
												<td width="100" class="wrd_brk"><p><? echo $storeArr[$data_array[$row[csf("id")]]['store_id']];?></p></td>
												<?
											}
											?>
											<td width="60" class="wrd_brk" align="center"><p><? echo $daysOnHand;//$daysOnHand; ?></p></td>
											<td width="100" class="wrd_brk" align="right" title="<? echo $wo_qty_arr[$row[csf("id")]]."==".$pi_qty_arr[$row[csf("id")]]."--".$rcv_qnty_array[$row[csf("id")]]; ?>"><a href="##" onclick="fnc_pipeLine_details('<? echo $row[csf("id")];?>','pipe_line_popup')"><p><? echo number_format($pipeLine_qty,2); ?></p></a></td>
											<td width="90" class="wrd_brk" align="right"><?=number_format($last30DaysConsumption, 3)?></td>
											<td width="90" class="wrd_brk" align="right"><?=number_format($last90DaysConsumption, 3)?></td>
											<td width="90" class="wrd_brk" align="right"><?=number_format($last365DaysConsumption, 3)?></td>
											<td width="100" class="wrd_brk" align="right"><?=is_infinite($closingStock/($last365DaysConsumption/365)) ? 0.00 : number_format($closingStock/($last365DaysConsumption/365), 3)?></td>
										</tr>
										<?
										$i++;
										$totalStockValue+=$stockValue;
										$totalpipeLine_qty+=$pipeLine_qty;
										$last30DaysConsumptionSum += $last30DaysConsumption;
										$last90DaysConsumptionSum += $last90DaysConsumption;
										$last365DaysConsumptionSum += $last365DaysConsumption;
										$daysCoverageSum += is_infinite($closingStock/($last365DaysConsumption/365)) ? 0 : $closingStock/($last365DaysConsumption/365);
									}
								}
								else
								{
									if(number_format($closingStock,3)>0.000 || number_format($openingBalance,3)>0.000 || number_format($totalReceive,3)>0.000 || number_format($totalIssue,3)>0.000)
									{
										if($closingStock <= $re_order_label){$bgcolor="red";}elseif($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" style=" word-wrap:break-word; <? echo $stylecolor; ?> "  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td width="50" align="center"><? echo $i; ?></td>
											<td width="60" class="wrd_brk"><? echo $row[csf("id")]; ?></td>
											<td width="70" class="wrd_brk"><p><? echo $row[csf("item_code")]; ?></p></td>
											<td width="100" class="wrd_brk"><p><? echo $item_category[$row[csf("item_category_id")]]; ?></p></td>
											<td width="100" class="wrd_brk"><p><? echo $itemgroupArr[$row[csf("item_group_id")]]; ?></p></td>
											<td width="100" class="wrd_brk"><p><? echo $row[csf("sub_group_name")]; ?></p></td>
											<td width="180" class="wrd_brk"><p><? echo $row[csf("item_description")]; ?></p></td>
											<td class="wrd_brk" width="150"><p><? echo chop($data_array[$row[csf("id")]]['lot_no'],","); ?></p></td>
											<td width="70" class="wrd_brk"><p><? echo $row[csf("item_size")]; ?></p></td>
											<td width="70" class="wrd_brk" align="center"><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></p></td>
											<td width="100" class="wrd_brk" align="right"><p><? echo number_format($openingRate,4); ?></p></td>
											<td width="100" class="wrd_brk" align="right"><p><? echo number_format($openingBalance,4); $tot_open_bl+=$openingBalance; ?></p></td>
											<td width="110" class="wrd_brk" align="right"><p><? echo number_format($openingBalanceValue,3); $tot_open_bl_amt+=$openingBalanceValue; ?></p></td>
											<td width="80" class="wrd_brk" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['purchase'],3); $tot_purchase+=$data_array[$row[csf("id")]]['purchase']; ?></p></td>
											<td width="80" class="wrd_brk" align="right"><p><? echo number_format($transfer_in_qty,3); $tot_transfer_in_qty+=$transfer_in_qty; ?></p></td>
											<td width="80" class="wrd_brk" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['issue_return'],3); $tot_issue_return+=$data_array[$row[csf("id")]]['issue_return'];//$row[csf("issue_return")]; ?></p></td>
											<td width="100" class="wrd_brk" align="right"><p><? echo number_format($totalReceive,3); $tot_total_receive+=$totalReceive; ?></p></td>
											<td width="80" class="wrd_brk" align="right"><p><? echo number_format($issue_qty,3); $tot_issue+=$issue_qty; ?></p></td>
											<td width="80" class="wrd_brk" align="right"><p><? echo number_format($transfer_out_qty,3); $tot_transfer_out_qty+=$transfer_out_qty; ?></p></td>
											<td width="80" class="wrd_brk" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['receive_return'],3); $tot_rec_return+=$data_array[$row[csf("id")]]['receive_return'];//$issue_result[0][csf('receive_return')]; ?></p></td>
											<td width="100" class="wrd_brk" align="right"><p><? echo number_format($totalIssue,3); $tot_total_issue+=$totalIssue; ?></p></td>
											<td width="100" class="wrd_brk" align="right"><p><? echo number_format($closingStock,4); $tot_closing_stock+=$closingStock; ?></p></td>
											<td width="80" class="wrd_brk" align="right"><p><? if(number_format($closingStock,4)>0) echo number_format($avg_rate,4); else echo "0.00";//number_format($row[csf("avg_rate")],2); $tot_avg_rate+=$row[csf("avg_rate")]; ?></p></td>
											<td width="120" class="wrd_brk" align="right"><p><? if(number_format($closingStock,4)>0) echo number_format($stockValue,3); else echo "0.00"; $tot_stock_value+=$stockValue; ?></p></td>
											<?
											if($cbo_store_wise==1)
											{
												?>
												<td width="100" class="wrd_brk"><p><? echo $storeArr[$data_array[$row[csf("id")]]['store_id']];?></p></td>
												<?
											}
											?>
											<td width="60" class="wrd_brk" align="center"><p><? echo $daysOnHand;//$daysOnHand; ?></p></td>
											<td width="100" class="wrd_brk" align="right" title="<? echo $wo_qty_arr[$row[csf("id")]]."==".$pi_qty_arr[$row[csf("id")]]."--".$rcv_qnty_array[$row[csf("id")]]; ?>"><a href="##" onclick="fnc_pipeLine_details('<? echo $row[csf("id")];?>','pipe_line_popup')"><p><? echo number_format($pipeLine_qty,2); ?></p></a></td>
											<td width="90" class="wrd_brk" align="right"><?=number_format($last30DaysConsumption, 3)?></td>
											<td width="90" class="wrd_brk" align="right"><?=number_format($last90DaysConsumption, 3)?></td>
											<td width="90" class="wrd_brk" align="right"><?=number_format($last365DaysConsumption, 3)?></td>
											<td width="100" class="wrd_brk" align="right"><?=is_infinite($closingStock/($last365DaysConsumption/365)) ? 0.00 : number_format($closingStock/($last365DaysConsumption/365), 3 ) ?></td>
										</tr>
										<?
										$i++;
										$totalStockValue+=$stockValue;
										$totalpipeLine_qty+=$pipeLine_qty;
										$last30DaysConsumptionSum += $last30DaysConsumption;
										$last90DaysConsumptionSum += $last90DaysConsumption;
										$last365DaysConsumptionSum += $last365DaysConsumption;
										$daysCoverageSum += is_infinite($closingStock/($last365DaysConsumption/365)) ? 0 : $closingStock/($last365DaysConsumption/365);
									}
								}
							}
						}
						?>
					</table>
				</div>
				<table width="<?=$tbl_width;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer">
					<tfoot>
					<tr>
						<th width="50">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="180">&nbsp;</th>
						<th width="150">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="100">Total:</th>
						<th width="100" class="wrd_brk" align="right" id="value_tot_open_bl"><? echo number_format($tot_open_bl,4); ?></th>
						<th width="110" class="wrd_brk" align="right" id="value_tot_open_bl_amt"><? echo number_format($tot_open_bl_amt,3); ?></th>
						<th width="80" class="wrd_brk" align="right" id="value_tot_purchase"><? echo number_format($tot_purchase,3); ?></th>
						<th width="80" class="wrd_brk" align="right" id="value_tot_transfer_in_qty"><? echo number_format($tot_transfer_in_qty,3); ?></th>
						<th width="80" class="wrd_brk" align="right" id="value_tot_issue_return"><? echo number_format($tot_issue_return,3); ?></th>
						<th width="100" class="wrd_brk" align="right" id="value_tot_total_receive"><? echo number_format($tot_total_receive,3); ?></th>
						<th width="80" class="wrd_brk" align="right" id="value_tot_issue"><? echo number_format($tot_issue,3); ?></th>
						<th width="80" class="wrd_brk" align="right" id="value_tot_transfer_out_qty"><? echo number_format($tot_transfer_out_qty,3); ?></th>
						<th width="80" class="wrd_brk" align="right" id="value_tot_rec_return"><? echo number_format($tot_rec_return,3); ?></th>
						<th width="100" class="wrd_brk" align="right" id="value_tot_total_issue"><? echo number_format($tot_total_issue,3); ?></th>
						<th width="100" class="wrd_brk" align="right" id="value_tot_closing_stock"><? echo number_format($tot_closing_stock,4); ?></th>
						<th width="80" class="wrd_brk">&nbsp;</th>
						<th width="120" class="wrd_brk" align="right" id="value_tot_stock_value"><p><? echo number_format($tot_stock_value,2); ?></p></th>
						<?
						if($cbo_store_wise==1)
						{
							?>
							<th width="100">&nbsp;</th>
							<?
						}
						?>
						<th width="60" class="wrd_brk">&nbsp;</th>
						<th width="100" class="wrd_brk" align="right" id="value_totalpipeLine_qty"><? echo number_format($totalpipeLine_qty,2); ?></th>
						<td width="90" class="wrd_brk" align="right"><?=number_format($last30DaysConsumptionSum, 3)?></td>
						<td width="90" class="wrd_brk" align="right"><?=number_format($last90DaysConsumptionSum, 3)?></td>
						<td width="90" class="wrd_brk" align="right"><?=number_format($last365DaysConsumptionSum, 3)?></td>
						<td width="100" class="wrd_brk" align="right"><?=number_format($daysCoverageSum, 3)?></td>
					</tr>
					</tfoot>
				</table>
			</div>
			<?
		}
		else if($report_type==8)
		{
			$company_cond="";
			if ($cbo_company_name>0) $company_cond =" and company_id=$cbo_company_name";
			if ($cbo_item_category_id>0) $category_cond=" and a.item_category_id='$cbo_item_category_id'";  else $category_cond=" and a.item_category_id in(5,6,7,23)";
			
			//$mrr_rate_sql=sql_select("select prod_id, sum(cons_quantity) as cons_quantiy, sum(cons_amount) as cons_amount from inv_transaction where status_active=1 and is_deleted=0 and transaction_type in(1,4,5) $category_cond $company_cond group by prod_id");
			//		$mrr_rate_arr=array();
			//		foreach($mrr_rate_sql as $row)
			//		{
			//			$mrr_rate_arr[$row[csf("prod_id")]]=$row[csf("cons_amount")]/$row[csf("cons_quantiy")];
			//		}
			
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
			

			$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
			$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
			$storeArr = return_library_array("select id,store_name from lib_store_location where status_active=1 and is_deleted=0","id","store_name");
			//$itemnameArr = return_library_array("select id,item_name from lib_item_creation where status_active=1 and is_deleted=0","id","item_name");
			$itemgroupArr = return_library_array("select id,item_name from  lib_item_group where status_active=1 and is_deleted=0 and item_category in(5,6,7,23)","id","item_name");

			$sql_loan_cond="";
			if ($cbo_company_name>0) $sql_loan_cond =" and a.company_id='$cbo_company_name'";
			if ($cbo_item_category_id==0) $sql_loan_cond.=" and a.item_category in(5,6,7,23)"; else $sql_loan_cond.=" and a.item_category='$cbo_item_category_id'";
			if ($item_account_id>0) $sql_loan_cond.=" and a.prod_id in ($item_account_id)";
			//if ($cbo_store_name>0)  $sql_loan_cond.=" and a.store_id=$cbo_store_name ";
			if ($cbo_store_name != "")  $sql_loan_cond.=" and a.store_id in($cbo_store_name) ";

			if($from_date!="" && $to_date!="")
			{
				$sql_loan="Select a.prod_id as prod_id, sum(a.cons_quantity) as cons_quantity , sum(a.cons_amount) as cons_amount, 1 as type
				from inv_transaction a, inv_receive_master b
				where a.mst_id=b.id and a.transaction_type=1 and b.receive_purpose=5 and a.transaction_date between '".$from_date."' and '".$to_date."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_loan_cond  group by a.prod_id
				union all
				Select a.prod_id as prod_id, sum(a.cons_quantity) as cons_quantity , sum(a.cons_amount) as cons_amount, 2 as type
				from inv_transaction a, inv_issue_master b
				where a.mst_id=b.id and a.transaction_type=2 and b.issue_purpose=5 and a.transaction_date between '".$from_date."' and '".$to_date."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_loan_cond  group by a.prod_id
				order by prod_id ASC";
			}
			//echo $sql_loan;die;
			$sql_loan_result=sql_select($sql_loan);
			$loan_data=array();
			foreach($sql_loan_result as $row)
			{
				if($row[csf("type")]==1)
				{
					$loan_data[$row[csf("prod_id")]]["loan_rcv_qnty"]+=$row[csf("cons_quantity")];
					$loan_data[$row[csf("prod_id")]]["loan_rcv_value"]+=$row[csf("cons_amount")];
				}
				else
				{
					$loan_data[$row[csf("prod_id")]]["loan_issue_qnty"]+=$row[csf("cons_quantity")];
					$loan_data[$row[csf("prod_id")]]["loan_issue_value"]+=$row[csf("cons_amount")];
				}
			}
			//var_dump($loan_data);die;
			unset($sql_loan_result);
			$search_cond="";
			if ($cbo_company_name > 0) $search_cond =" and a.company_id='$cbo_company_name'";
			if ($cbo_item_category_id == 0) $search_cond.=" and b.item_category_id in(5,6,7,23)"; else $search_cond.=" and b.item_category_id='$cbo_item_category_id'";
			if ($cbo_item_category_id == 0) $search_cond.=" and a.item_category in(5,6,7,23)"; else $search_cond.=" and a.item_category='$cbo_item_category_id'";
			if ($item_account_id > 0) $search_cond.=" and b.id in ($item_account_id)";
			if ($item_group_id > 0) $search_cond.=" and b.item_group_id in($item_group_id)";
			//if ($cbo_store_name > 0) $search_cond.=" and a.store_id=$cbo_store_name ";
			if ($cbo_store_name != "")  $search_cond.=" and a.store_id in($cbo_store_name) ";


			//if($db_type==0)
			//		{
			//			$sql="Select b.id as prod_id,b.re_order_label,
			//			sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
			//			sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as iss_total_opening,
			//			sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as rcv_total_opening_amt,
			//			sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as iss_total_opening_amt,
			//			sum(case when a.transaction_type=1 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as purchase,
			//            sum(case when a.transaction_type in(1,4,5) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as purchase_amount,
			//            sum(case when a.transaction_type in(2,3,6) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as issue_amount,
			//			sum(case when a.transaction_type=2 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as issue,
			//			sum(case when a.transaction_type in(4) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as issue_return,
			//			sum(case when a.transaction_type in(3) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as receive_return,
			//
			//			sum(case when a.transaction_type in(6) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as item_transfer_issue,
			//			sum(case when a.transaction_type in(5) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as item_transfer_receive,
			//			sum(case when a.transaction_type in(1,4) and a.receive_basis in(1,2) then a.cons_quantity else 0 end) as rcv_total_wo,
			//			group_concat(a.batch_lot) as lot_no
			//			from inv_transaction a, product_details_master b
			//			where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type in (1,2,3,4,5,6) and a.order_id=0 $search_cond  
			//			group by b.id, b.re_order_label 
			//			order by b.id ASC";
			//		}
			//		else if($db_type==2)
			//		{
			//			$sql="Select b.id as prod_id,b.re_order_label,
			//			sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
			//			sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as iss_total_opening,
			//			sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as rcv_total_opening_amt,
			//			sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as iss_total_opening_amt,
			//			sum(case when a.transaction_type=1 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as purchase,
			//            sum(case when a.transaction_type in(1,4,5) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as purchase_amount,
			//            sum(case when a.transaction_type in(2,3,6) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as issue_amount,
			//			sum(case when a.transaction_type=2 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as issue,
			//			sum(case when a.transaction_type in(4) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as issue_return,
			//			sum(case when a.transaction_type in(3) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as receive_return,
			//
			//			sum(case when a.transaction_type in(6) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as item_transfer_issue,
			//			sum(case when a.transaction_type in(5) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as item_transfer_receive,
			//			sum(case when a.transaction_type in(1,4) and a.receive_basis in(1,2) then a.cons_quantity else 0 end) as rcv_total_wo,
			//			listagg(cast(a.batch_lot as varchar(4000)), ',') within group (order by a.batch_lot)  as lot_no
			//			from inv_transaction a, product_details_master b
			//			where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and  b.is_deleted=0 and a.transaction_type in (1,2,3,4,5,6) and a.order_id=0 $search_cond 
			//			group by b.id, b.re_order_label 
			//			order by b.id ASC";
			//		}
			//		//echo $sql;die;
			//		$trnasactionData = sql_select($sql);
			//		$data_array=array();
			//		foreach($trnasactionData as $row_p)
			//		{
			//			$data_array[$row_p[csf("prod_id")]]['rcv_total_opening']=$row_p[csf("rcv_total_opening")];
			//			$data_array[$row_p[csf("prod_id")]]['iss_total_opening']=$row_p[csf("iss_total_opening")];
			//			$data_array[$row_p[csf("prod_id")]]['rcv_total_opening_amt']=$row_p[csf("rcv_total_opening_amt")];
			//			$data_array[$row_p[csf("prod_id")]]['iss_total_opening_amt']=$row_p[csf("iss_total_opening_amt")];
			//			$data_array[$row_p[csf("prod_id")]]['purchase']=$row_p[csf("purchase")];
			//			$data_array[$row_p[csf("prod_id")]]['issue']=$row_p[csf("issue")];
			//			$data_array[$row_p[csf("prod_id")]]['issue_return']=$row_p[csf("issue_return")];
			//			$data_array[$row_p[csf("prod_id")]]['receive_return']=$row_p[csf("receive_return")];
			//			$data_array[$row_p[csf("prod_id")]]['item_transfer_issue']=$row_p[csf("item_transfer_issue")];
			//			$data_array[$row_p[csf("prod_id")]]['item_transfer_receive']=$row_p[csf("item_transfer_receive")];
			//			$data_array[$row_p[csf("prod_id")]]['rcv_total_wo']=$row_p[csf("rcv_total_wo")];
			//			$data_array[$row_p[csf("prod_id")]]['lot_no']=implode(",",array_unique(explode(",",$row_p[csf("lot_no")])));
			//			$data_array[$row_p[csf("prod_id")]]['purchase_amount']=$row_p[csf("purchase_amount")];
			//			$data_array[$row_p[csf("prod_id")]]['issue_amount']=$row_p[csf("issue_amount")];
			//		}
			
			
			$trans_sql="SELECT b.id as PROD_ID, a.transaction_date as TRANSACTION_DATE, a.transaction_type as TRANSACTION_TYPE, a.cons_quantity as CONS_QUANTITY, a.cons_amount as CONS_AMOUNT, a.batch_lot as LOT_NO, a.receive_basis as RECEIVE_BASIS  from inv_transaction a, product_details_master b
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
				
				if($row_p["TRANSACTION_TYPE"]==1 &&($row_p["RECEIVE_BASIS"]==1 || $row_p["RECEIVE_BASIS"]==2))
				{
					$data_array[$row_p["PROD_ID"]]['rcv_total_wo']+=$row_p["CONS_QUANTITY"];
				}
			}
			unset($trnasactionData);
			
			//echo "<pre>";print_r($data_array);die;
			$returnRes_date="select prod_id as PROD_ID, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where is_deleted=0 and status_active=1 and item_category in(5,6,7,23) group by prod_id";
			//echo $returnRes_date;die;
			$result_returnRes_date = sql_select($returnRes_date);
			foreach($result_returnRes_date as $row)
			{
				$date_array[$row["PROD_ID"]]['min_date']=$row["MIN_DATE"];
				$date_array[$row["PROD_ID"]]['max_date']=$row["MAX_DATE"];
			}


			$wo_qty_arr=return_library_array("select b.item_id, sum(b.supplier_order_quantity) as qty from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.item_category in (5,6,7,23) and a.pay_mode<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by item_id","item_id","qty");

			$pi_qty_arr=return_library_array("select b.item_prod_id, sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id in (5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by item_prod_id","item_prod_id","qty");
			$i=1;
			//echo "<pre>";print_r($data_array);die;
			ob_start();
			?>

		
			<div align="center" style="height:auto; width:auto; margin:0 auto; padding:0; width: 3350px">
				<table width="3350px" cellpadding="0" cellspacing="0" id="caption" align="center">
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
							<td colspan="22" align="center" class="form_caption" style="border:none;font-size:12px; font-weight:bold">
								<? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date,'dd-mm-yyyy')." To : ".change_date_format($to_date,'dd-mm-yyyy')."" ;?>
							</td>
						</tr>
					</thead>
				</table>
				<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="3350" rules="all" id="rpt_table_header">
					<thead>
						<tr>
							<th rowspan="2" width="50">SL</th>
							<th colspan="8">Description</th>
							<th rowspan="2" width="100"> Opneing Rate</th>
							<th rowspan="2" width="100">Opening Stock</th>
							<th rowspan="2" width="110">Opening Value</th>
							<th colspan="10">Receive</th>
							<th colspan="10">Issue</th>
							<th rowspan="2" width="100">Closing Stock</th>
							<th rowspan="2" width="80">Avg. Rate</th>
							<th rowspan="2" width="120">Stock Value</th>
							<th rowspan="2" width="60">DOH</th>
							<th rowspan="2" width="100">Pipe Line</th>
							<th rowspan="2" >Lot</th>
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
							<th width="80">Purchase Value</th>
							<th width="80">Loan receive</th>
							<th width="80">Loan receive Value</th>
							<th width="80">Transfer In</th>
							<th width="80">Transfer In Value</th>
							<th width="80">Issue Return</th>
							<th width="80">Issue Return Value</th>
							<th width="100">Total Received</th>
							<th width="100">Received Value</th>
							<th width="80">Issue</th>
							<th width="80">Issue Value</th>
							<th width="80">Loan Issue</th>
							<th width="80">Loan Issue Value</th>
							<th width="80">Transfer Out</th>
							<th width="80">Transfer Out Value</th>
							<th width="80">Receive Return</th>
							<th width="80">Receive Return Value</th>
							<th width="100">Total Issue</th>
							<th width="100">Total Issue Value</th>
						</tr>
					</thead>
				</table>
				<div style="width:3350px; max-height:250px; overflow-y:scroll; overflow-x:hidden;" id="scroll_body">
				<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="3350" rules="all" align="left">
				<?
					$pord_cond="";
					if ($cbo_item_category_id == 0) $pord_cond.=" and b.item_category_id in(5,6,7,23)"; else $pord_cond.=" and b.item_category_id='$cbo_item_category_id'";
					if ($item_account_id > 0) $pord_cond.=" and b.id in ($item_account_id)";
					if ($item_group_id > 0) $pord_cond.=" and b.item_group_id in($item_group_id)";
					$sql="select b.id, b.item_code, b.item_category_id,b.item_group_id,b.unit_of_measure,b.item_description,b.sub_group_name,b.re_order_label, b.current_stock,avg_rate_per_unit 
					from product_details_master b where b.status_active=1 and b.is_deleted=0 and b.company_id='$cbo_company_name' $pord_cond
					order by b.id";
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
						$transfer_value=$data_array[$row[csf("id")]]['item_transfer_issue_amt'];
						$transfer_in_qty=$data_array[$row[csf("id")]]['item_transfer_receive'];

						$openingBalance = $data_array[$row[csf("id")]]['rcv_total_opening']-$data_array[$row[csf("id")]]['iss_total_opening'];
						$openingBalanceValue=$openingRate=0;
						//if($data_array[$row[csf("id")]]['rcv_total_opening'] >0)
						//{
							//$openingRate = $data_array[$row[csf("id")]]['rcv_total_opening_amt']/$data_array[$row[csf("id")]]['rcv_total_opening'];
						//}
						//$openingBalanceValue = $openingBalance*$openingRate;
						$openingBalanceValue =$data_array[$row[csf("id")]]['rcv_total_opening_amt']-$data_array[$row[csf("id")]]['iss_total_opening_amt'];
						if($openingBalance==0)
						{
							$openingBalanceValue=0;
							$openingRate =0;
						}
						else
						{
							if($openingBalanceValue!=0 && $openingBalance !=0) $openingRate = $openingBalanceValue/$openingBalance; else $openingRate =0;
						}
						

						//$openingBalanceValue = $data_array[$row[csf("id")]]['rcv_total_opening_amt']-$data_array[$row[csf("id")]]['iss_total_opening_amt'];
						//$openingRate=$openingBalanceValue/$openingBalance;

						$purchase_qnty=$data_array[$row[csf("id")]]['purchase']-$loan_data[$row[csf("id")]]["loan_rcv_qnty"];
						$purchase_value=$data_array[$row[csf("id")]]['purchase_amt']-$loan_data[$row[csf("id")]]["loan_rcv_value"];
						$loan_receive=$loan_data[$row[csf("id")]]["loan_rcv_qnty"];
						$loan_rcveive_value=$loan_data[$row[csf("id")]]["loan_rcv_value"];
						$issue_qnty=$data_array[$row[csf("id")]]['issue']-$loan_data[$row[csf("id")]]["loan_issue_qnty"];
						$issue_qnty_value=$data_array[$row[csf("id")]]['issue_amt']-$loan_data[$row[csf("id")]]["loan_issue_value"];
						$loan_issue=$loan_data[$row[csf("id")]]["loan_issue_qnty"];
						$loan_issu_value=$loan_data[$row[csf("id")]]["loan_issue_value"];

						$totalReceive = $purchase_qnty+$loan_receive+$data_array[$row[csf("id")]]['issue_return']+$transfer_in_qty;//+$openingBalance
						$totalIssue = $issue_qnty+$loan_issue+$data_array[$row[csf("id")]]['receive_return']+$transfer_out_qty;
						$closingStock=$openingBalance+$totalReceive-$totalIssue;
						//$closingStock=$opening+$totalReceive-$totalIssue;
						if((($get_upto==1 && $ageOfDays>$txt_days) || ($get_upto==2 && $ageOfDays<$txt_days) || ($get_upto==3 && $ageOfDays>=$txt_days) || ($get_upto==4 && $ageOfDays<=$txt_days) || ($get_upto==5 && $ageOfDays==$txt_days) || $get_upto==0) && (($get_upto_qnty==1 && $closingStock>$txt_qnty) || ($get_upto_qnty==2 && $closingStock<$txt_qnty) || ($get_upto_qnty==3 && $closingStock>=$txt_qnty) || ($get_upto_qnty==4 && $closingStock<=$txt_qnty) || ($get_upto_qnty==5 && $closingStock==$txt_qnty) || $get_upto_qnty==0))
						{
							$avg_rate=$mrr_rate_arr[$row[csf("id")]];
							//$stockValue=$closingStock*$avg_rate;
							$stockValue=(($openingBalanceValue+$data_array[$row[csf("id")]]['purchase_amt'])-$data_array[$row[csf("id")]]['issue_amt']);
							if($closingStock==0)
							{
								$closing_rate=0;
								$stockValue=0;
							}
							else
							{
								if($stockValue!=0 && $closingStock!=0) $closing_rate=$stockValue/$closingStock; else $closing_rate=0;
							}
							
							

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
										<td width="70"><p><? echo $row[csf("item_code")]; ?></p></td>
										<td width="100"><p><? echo $item_category[$row[csf("item_category_id")]]; ?></p></td>
										<td width="100"><p><? echo $itemgroupArr[$row[csf("item_group_id")]]; ?></p></td>
										<td width="100"><p><? echo $row[csf("sub_group_name")]; ?></p></td>
										<td width="180"><p><? echo $row[csf("item_description")]; ?></p></td>
										<td width="70"><p><? echo $row[csf("item_size")]; ?></p></td>
										<td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></p></td>
										<td width="100" align="right"><p><? echo number_format($openingRate,3); ?></p></td>
										<td width="100" align="right"><p><? echo number_format($openingBalance,3); $tot_open_bl+=$openingBalance; ?></p></td>
										<td width="110" align="right"><p><? echo number_format($openingBalanceValue,3); $tot_open_bl_amt+=$openingBalanceValue; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($purchase_qnty,3); $tot_purchase+=$purchase_qnty; ?></p></td>
										<td width="80" align="right"><p><? echo  number_format($purchase_value,3); $total_purchase_value+=$purchase_value;  ?></p></td>
										<td width="80" align="right"><p><? echo number_format($loan_receive,3); $tot_loan_rcv+=$loan_receive; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($loan_rcveive_value,3); $total_loan_rcveive_value+=$loan_rcveive_value;  ?></p></td>
										<td width="80" align="right"><p><? echo number_format($transfer_in_qty,3); $tot_transfer_in_qty+=$transfer_in_qty; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['item_transfer_receive_amt'],3); $item_transfer_receive_amt+=$data_array[$row[csf("id")]]['item_transfer_receive_amt']; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['issue_return'],3); $tot_issue_return+=$data_array[$row[csf("id")]]['issue_return'];//$row[csf("issue_return")]; ?></p></td>
										<td width="80" align="right"><p><?echo number_format($data_array[$row[csf("id")]]['issue_return_amt'],3); $issue_return_amt+=$data_array[$row[csf("id")]]['issue_return_amt']; ?></p></td>
										<td width="100" align="right"><p><? echo number_format($totalReceive,3); $tot_total_receive+=$totalReceive; ?></p></td>
										<td width="100" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['purchase_amt'],3); $tot_purchase_amount+=$data_array[$row[csf("id")]]['purchase_amt'];?></p></td>
										<td width="80" align="right"><p><? echo number_format($issue_qnty,3); $tot_issue+=$issue_qnty; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($issue_qnty_value,3); $issue_qnty_values+=$issue_qnty_value; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($loan_issue,3); $tot_loan_issue+=$loan_issue; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($loan_issu_value,3); $loan_issue_values+=$loan_issu_value; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($transfer_out_qty,3); $tot_transfer_out_qty+=$transfer_out_qty; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($transfer_value,3); $tot_transfer_out_value+=$transfer_value; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['receive_return'],3); $tot_rec_return+=$data_array[$row[csf("id")]]['receive_return'];//$issue_result[0][csf('receive_return')]; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['receive_return_amt'],3); $tot_rec_return_value+=$data_array[$row[csf("id")]]['receive_return_amt']; ?></p></td>
										<td width="100" align="right"><p><? echo number_format($totalIssue,3); $tot_total_issue+=$totalIssue; ?></p></td>
										<td width="100" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['issue_amt'],3); $tot_issue_amount+=$data_array[$row[csf("id")]]['issue_amt'];?></p></td>
										<td width="100" align="right"><p><? echo number_format($closingStock,3); $tot_closing_stock+=$closingStock; ?></p></td>
										<td width="80" align="right"><p><? if(number_format($closingStock,3)>0) echo number_format($closing_rate,2); else echo "0.00"; //number_format($row[csf("avg_rate")],2); $tot_avg_rate+=$row[csf("avg_rate")]; ?></p></td>
										<td width="120" align="right"><p><? if(number_format($closingStock,3)>0) echo number_format($stockValue,2); else echo "0.00"; $tot_stock_value+=$stockValue; ?></p></td>
										<td width="60" align="center"><p><? echo $daysOnHand;//$daysOnHand; ?></p></td>
										<td width="100" align="right" title="<? echo $wo_qty_arr[$row[csf("id")]]."==".$pi_qty_arr[$row[csf("id")]]; ?>"><a href="##" onclick="fnc_pipeLine_details('<? echo $row[csf("id")];?>','pipe_line_popup')"><p><? echo number_format($pipeLine_qty,2); ?></p></a></td>
										<td ><p><? echo $data_array[$row[csf("id")]]['lot_no']; ?></p></td>
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
										<td width="70"><p><? echo $row[csf("item_code")]; ?></p></td>
										<td width="100"><p><? echo $item_category[$row[csf("item_category_id")]]; ?></p></td>
										<td width="100"><p><? echo $itemgroupArr[$row[csf("item_group_id")]]; ?></p></td>
										<td width="100"><p><? echo $row[csf("sub_group_name")]; ?></p></td>
										<td width="180"><p><? echo $row[csf("item_description")]; ?></p></td>
										<td width="70"><p><? echo $row[csf("item_size")]; ?></p></td>
										<td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></p></td>
										<td width="100" align="right"><p><? echo number_format($openingRate,3); ?></p></td>
										<td width="100" align="right"><p><? echo number_format($openingBalance,3); $tot_open_bl+=$openingBalance; ?></p></td>
										<td width="110" align="right"><p><? echo number_format($openingBalanceValue,3); $tot_open_bl_amt+=$openingBalanceValue; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($purchase_qnty,3); $tot_purchase+=$purchase_qnty; ?></p></td>
										<td width="80" align="right"><p><? echo  number_format($purchase_value,3); $total_purchase_value+=$purchase_value; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($loan_receive,3); $tot_loan_rcv+=$loan_receive; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($loan_rcveive_value,3); $total_loan_rcveive_value+=$loan_rcveive_value; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($transfer_in_qty,3); $tot_transfer_in_qty+=$transfer_in_qty; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['item_transfer_receive_amt'],3); $item_transfer_receive_amt+=$data_array[$row[csf("id")]]['item_transfer_receive_amt']; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['issue_return'],3); $tot_issue_return+=$data_array[$row[csf("id")]]['issue_return'];//$row[csf("issue_return")]; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['issue_return_amt'],3); $issue_return_amt+=$data_array[$row[csf("id")]]['issue_return_amt']; ?></p></td>
										<td width="100" align="right"><p><? echo number_format($totalReceive,3); $tot_total_receive+=$totalReceive; ?></p></td>
										<td width="100" align="right" title="<?= $row[csf("id")];?>"><p><? echo number_format($data_array[$row[csf("id")]]['purchase_amt'],3);$tot_purchase_amount+=$data_array[$row[csf("id")]]['purchase_amt'];?></p></td>
										<td width="80" align="right"><p><? echo number_format($issue_qnty,3); $tot_issue+=$issue_qnty; ?></p></td>
										<td width="80" align="right"><p><?echo number_format($issue_qnty_value,3); $issue_qnty_values+=$issue_qnty_value; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($loan_issue,3); $tot_loan_issue+=$loan_issue; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($loan_issu_value,3); $loan_issue_values+=$loan_issu_value; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($transfer_out_qty,3); $tot_transfer_out_qty+=$transfer_out_qty; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($transfer_value,3); $tot_transfer_out_value+=$transfer_value; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['receive_return'],3); $tot_rec_return+=$data_array[$row[csf("id")]]['receive_return'];//$issue_result[0][csf('receive_return')]; ?></p></td>
										<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['receive_return_amt'],3); $tot_rec_return_value+=$data_array[$row[csf("id")]]['receive_return_amt']; ?></p></td>
										<td width="100" align="right"><p><? echo number_format($totalIssue,3); $tot_total_issue+=$totalIssue; ?></p></td>
										<td width="100" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['issue_amt'],3); $tot_issue_amount+=$data_array[$row[csf("id")]]['issue_amt'];?></p></td>
										<td width="100" align="right"><p><? echo number_format($closingStock,3); $tot_closing_stock+=$closingStock; ?></p></td>
										<td width="80" align="right"><p><? if(number_format($closingStock,3)>0) echo number_format($closing_rate,2); else echo "0.00"; //number_format($row[csf("avg_rate")],2); $tot_avg_rate+=$row[csf("avg_rate")]; ?></p></td>
										<td width="120" align="right"><p><? if(number_format($closingStock,3)>0) echo number_format($stockValue,2); else echo "0.00"; $tot_stock_value+=$stockValue; ?></p></td>
										<td width="60" align="center"><p><? echo $daysOnHand;//$daysOnHand; ?></p></td>
										<td width="100" align="right" title="<? echo $wo_qty_arr[$row[csf("id")]]."==".$pi_qty_arr[$row[csf("id")]]; ?>"><a href="##" onclick="fnc_pipeLine_details('<? echo $row[csf("id")];?>','pipe_line_popup')"><p><? echo number_format($pipeLine_qty,2); ?></p></a></td>
										<td><p><? echo $data_array[$row[csf("id")]]['lot_no']; ?></p></td>
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
				<table width="3350" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer">
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
							<th width="80" align="right" id="total_purchase_value"><? echo number_format($total_purchase_value,3); ?></th>
							<th width="80" align="right" id="value_tot_loan_rcv"><? echo number_format($tot_loan_rcv,3); ?></th>
							<th width="80" align="right" id="total_loan_rcveive_value"><? echo number_format($total_loan_rcveive_value,3); ?></th>
							<th width="80" align="right" id="value_tot_transfer_in_qty"><? echo number_format($tot_transfer_in_qty,3); ?></th>
							<th width="80" align="right" id="tot_transfer_in_qty_value"><? echo number_format($item_transfer_receive_amt,3); ?></th>
							<th width="80" align="right" id="value_tot_issue_return"><? echo number_format($tot_issue_return,3); ?></th>
							<th width="80" align="right" id="value_issue_return_amt"><? echo number_format($issue_return_amt,3); ?></th>
							<th width="100" align="right" id="value_tot_total_receive"><? echo number_format($tot_total_receive,3); ?></th>
							<th width="100"  align="right" id="tot_total_receive_value"><? echo number_format($tot_purchase_amount,3);?></th>
							<th width="80" align="right" id="value_tot_issue"><? echo number_format($tot_issue,3); ?></th>
							<th width="80" align="right" id="tot_issue_value"><? echo number_format($issue_qnty_values,3); ?></th>
							<th width="80" align="right" id="value_tot_loan_issue"><? echo number_format($tot_loan_issue,3); ?></th>
							<th width="80" align="right" id="tot_loan_issue_value"><? echo number_format($loan_issue_values,3); ?></th>
							<th width="80" align="right" id="value_tot_transfer_out_qty"><? echo number_format($tot_transfer_out_qty,3); ?></th>
							<th width="80" align="right" id="tot_transfer_out_qty_value"><? echo number_format($tot_transfer_out_value,3); ?></th>
							<th width="80" align="right" id="value_tot_rec_return"><? echo number_format($tot_rec_return,3); ?></th>
							<th width="80" align="right" id="tot_rec_return_value"><? echo number_format($tot_rec_return_value,3); ?></th>
							<th width="100" align="right" id="value_tot_total_issue"><? echo number_format($tot_total_issue,3); ?></th>
							<th width="100"  align="right" id="tot_total_issue_value"><? echo number_format($tot_issue_amount,3);?></th>
							<th width="100" align="right" id="value_tot_closing_stock"><? echo number_format($tot_closing_stock,3); ?></th>
							<th width="80">&nbsp;</th>
							<th width="120" align="right" id="value_tot_stock_value"><p><? echo number_format($tot_stock_value,2); ?></p></th>
							<th width="60">&nbsp;</th>
							<th width="100" align="right" id="value_totalpipeLine_qty"><? echo number_format($totalpipeLine_qty,2); ?></th>
							<th >&nbsp;</th>
						</tr>
					</tfoot>
				</table>
			</div>
			<?

		}
		else if($report_type==9)
		{

			if ($cbo_company_name==0) $company_id =""; else $company_id =" and a.company_id='$cbo_company_name'";
			if ($cbo_item_category_id==0) $item_category_id=" and b.item_category_id in(5,6,7,23)"; else $item_category_id=" and b.item_category_id='$cbo_item_category_id'";
			if ($cbo_item_category_id==0) $item_category_cond=" and a.item_category in(5,6,7,23)"; else $item_category_cond=" and a.item_category='$cbo_item_category_id'";
			if ($item_group_id==0) $group_id=""; else $group_id=" and b.item_group_id in($item_group_id)";
			if ($item_account_id==0) $item_account=""; else $item_account=" and a.prod_id in ($item_account_id)";
			if ($item_account_id==0) $item_account_cond=""; else $item_account_cond=" and b.id in ($item_account_id)";
			if ($cbo_compliance>0) $item_account_cond.=" and b.is_compliance = $cbo_compliance";
			
			//if ($cbo_store_name==0)  $store_id="";  else  $store_id=" and a.store_id=$cbo_store_name ";
			$store_id="";
			if ($cbo_store_name != "")  $store_id.=" and a.store_id in($cbo_store_name) ";


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
			$storeArr = return_library_array("select id,store_name from lib_store_location where status_active=1 and is_deleted=0","id","store_name");
			//$itemnameArr = return_library_array("select id,item_name from lib_item_creation where status_active=1 and is_deleted=0","id","item_name");
			$itemgroupArr = return_library_array("select id,item_name from lib_item_group where status_active=1 and is_deleted=0 and item_category in(5,6,7,23)","id","item_name");
			
			$trans_sql="SELECT b.id as PROD_ID, a.transaction_date as TRANSACTION_DATE, a.transaction_type as TRANSACTION_TYPE, a.cons_quantity as CONS_QUANTITY, a.cons_amount as CONS_AMOUNT, a.batch_lot as LOT_NO, a.receive_basis as RECEIVE_BASIS, a.STORE_ID, b.ITEM_CATEGORY_ID, b.ITEM_GROUP_ID, b.SUB_GROUP_NAME, b.ITEM_DESCRIPTION, b.ITEM_SIZE, b.UNIT_OF_MEASURE, b.is_compliance as IS_COMPLIANCE 
			from inv_transaction a, product_details_master b
			where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id $group_id $prod_cond $store_id $item_account $search_cond $item_category_id $item_category_cond $item_account_cond
			order by b.id ";
			
			//echo $trans_sql;die;
			$trnasactionData = sql_select($trans_sql);
			//echo count($trnasactionData).jahid;die;
			//echo "<pre>";print_r($trnasactionData);die;
			$data_array=array();
			foreach($trnasactionData as $row_p)
			{
				//."*".$row_p["IS_COMPLIANCE"]
				$item_key=$row_p["ITEM_CATEGORY_ID"]."_".$row_p["ITEM_GROUP_ID"]."_".$row_p["SUB_GROUP_NAME"]."_".$row_p["ITEM_DESCRIPTION"]."_".$row_p["ITEM_SIZE"]."_".$row_p["UNIT_OF_MEASURE"];
				
				
				if($row_p["TRANSACTION_TYPE"]==1)
				{
					if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
					{
						$data_array[$item_key]['rcv_total_opening']+=$row_p["CONS_QUANTITY"];
						$data_array[$item_key]['rcv_total_opening_amt']+=$row_p["CONS_AMOUNT"];
					}
					else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
					{
						$data_array[$item_key]['purchase']+=$row_p["CONS_QUANTITY"];
						$data_array[$item_key]['purchase_amt']+=$row_p["CONS_AMOUNT"];
					}
				}
				else if($row_p["TRANSACTION_TYPE"]==2)
				{
					if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
					{
						$data_array[$item_key]['iss_total_opening']+=$row_p["CONS_QUANTITY"];
						$data_array[$item_key]['iss_total_opening_amt']+=$row_p["CONS_AMOUNT"];
					}
					else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
					{
						$data_array[$item_key]['issue']+=$row_p["CONS_QUANTITY"];
						$data_array[$item_key]['issue_amt']+=$row_p["CONS_AMOUNT"];
					}
				}
				else if($row_p["TRANSACTION_TYPE"]==3)
				{
					if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
					{
						$data_array[$item_key]['iss_total_opening']+=$row_p["CONS_QUANTITY"];
						$data_array[$item_key]['iss_total_opening_amt']+=$row_p["CONS_AMOUNT"];
					}
					else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
					{
						$data_array[$item_key]['receive_return']+=$row_p["CONS_QUANTITY"];
						$data_array[$item_key]['receive_return_amt']+=$row_p["CONS_AMOUNT"];
					}
				}
				else if($row_p["TRANSACTION_TYPE"]==4)
				{
					if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
					{
						$data_array[$item_key]['rcv_total_opening']+=$row_p["CONS_QUANTITY"];
						$data_array[$item_key]['rcv_total_opening_amt']+=$row_p["CONS_AMOUNT"];
					}
					else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
					{
						$data_array[$item_key]['issue_return']+=$row_p["CONS_QUANTITY"];
						$data_array[$item_key]['issue_return_amt']+=$row_p["CONS_AMOUNT"];
					}
				}
				else if($row_p["TRANSACTION_TYPE"]==5)
				{
					if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
					{
						$data_array[$item_key]['rcv_total_opening']+=$row_p["CONS_QUANTITY"];
						$data_array[$item_key]['rcv_total_opening_amt']+=$row_p["CONS_AMOUNT"];
					}
					else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
					{
						$data_array[$item_key]['item_transfer_receive']+=$row_p["CONS_QUANTITY"];
						$data_array[$item_key]['item_transfer_receive_amt']+=$row_p["CONS_AMOUNT"];
					}
				}
				else if($row_p["TRANSACTION_TYPE"]==6)
				{
					if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
					{
						$data_array[$item_key]['iss_total_opening']+=$row_p["CONS_QUANTITY"];
						$data_array[$item_key]['iss_total_opening_amt']+=$row_p["CONS_AMOUNT"];
					}
					else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
					{
						$data_array[$item_key]['item_transfer_issue']+=$row_p["CONS_QUANTITY"];
						$data_array[$item_key]['item_transfer_issue_amt']+=$row_p["CONS_AMOUNT"];
					}
				}
				$data_array[$item_key]['IS_COMPLIANCE']=$row_p["IS_COMPLIANCE"];
				
				if($batch_lot_check[$item_key][$row_p["LOT_NO"]]=="" && $row_p["LOT_NO"] !="")
				{
					$batch_lot_check[$item_key][$row_p["LOT_NO"]]=$row_p["LOT_NO"];
					$data_array[$item_key]['lot_no'].=$row_p["LOT_NO"].",";
				}
				
				if($row_p["TRANSACTION_TYPE"]==1 &&($row_p["RECEIVE_BASIS"]==1 || $row_p["RECEIVE_BASIS"]==2))
				{
					$data_array[$item_key]['rcv_total_wo']+=$row_p["CONS_QUANTITY"];
				}
				if($row_p["STORE_ID"]){ $data_array[$item_key]['store_id']=$row_p["STORE_ID"];}
			}
			unset($trnasactionData);
			//echo "<pre>";print_r($data_array);die;
			$tbl_width="1970";
			$i=1;
			ob_start();
			?>
	
			<div align="center" style="height:auto; width:auto; margin:0 auto; padding:0; width:1300px;">
				<table width="1300px" cellpadding="0" cellspacing="0" id="caption" align="center">
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
				<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<?=$tbl_width;?>" rules="all" id="rpt_table_header">
					<thead>
						<tr>
							<th rowspan="2" width="30">SL</th>
							<th colspan="6">Description</th>
							<th rowspan="2" width="100"> Opneing Rate</th>
							<th rowspan="2" width="100">Opening Stock</th>
							<th rowspan="2" width="110">Opening Value</th>
							<th colspan="4">Receive</th>
							<th colspan="4">Issue</th>
							<th rowspan="2" width="100">Closing Stock</th>
							<th rowspan="2" width="80">Avg. Rate</th>
							<th rowspan="2" width="100">Stock Value</th>
							<th rowspan="2">Zero Discharge</th>
						</tr>
						<tr>
							<th width="100">Item Category</th>
							<th width="100">Item Group</th>
							<th width="80">Item Sub-group</th>
							<th width="180">Item Description</th>
							<th width="70">Item Size</th>
							<th width="50">UOM</th>
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
				<div style="width:<?=$tbl_width+18;?>px; max-height:250px; overflow-y:scroll; overflow-x:hidden;" id="scroll_body">
				<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="<?=$tbl_width;?>" rules="all" align="left">
				<?
					if ($cbo_item_category_id==0) $rcv_category_cond=" and b.item_category in(5,6,7,23)"; else $rcv_category_cond=" and b.item_category='$cbo_item_category_id'";
					//$item_key=$row_p["ITEM_CATEGORY_ID"]."*".$row_p["ITEM_GROUP_ID"]."*".$row_p["SUB_GROUP_NAME"]."*".$row_p["ITEM_DESCRIPTION"]."*".$row_p["ITEM_SIZE"]."*".$row_p["UNIT_OF_MEASURE"];, b.is_compliance, b.is_compliance
					
					$sql="SELECT max(b.id) as id, max(b.item_code) as item_code, b.item_category_id, b.item_group_id, b.sub_group_name, b.item_description, b.item_size, b.unit_of_measure, sum(b.current_stock) as current_stock, avg(b.avg_rate_per_unit) as avg_rate_per_unit
					from product_details_master b 
					where b.status_active=1 and b.is_deleted=0 and b.company_id='$cbo_company_name' $item_category_id $group_id $prod_cond $item_account_cond $search_cond 
					group by b.item_category_id, b.item_group_id, b.sub_group_name, b.item_description, b.item_size, b.unit_of_measure";
					//echo $sql;die;
					$result = sql_select($sql);
					foreach($result as $row)
					{
						//if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";."*".$row[csf("is_compliance")]
						$item_key=$row[csf("item_category_id")]."_".$row[csf("item_group_id")]."_".$row[csf("sub_group_name")]."_".$row[csf("item_description")]."_".$row[csf("item_size")]."_".$row[csf("unit_of_measure")];
						$re_order_label=$row[csf("re_order_label")];
						if( $row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==4 )
							$stylecolor='color:#A61000;';
						else
							$stylecolor='color:#000000;';

						$issue_qty=$data_array[$item_key]['issue'];
						$transfer_out_qty=$data_array[$item_key]['item_transfer_issue'];
						$transfer_in_qty=$data_array[$item_key]['item_transfer_receive'];

						$transfer_out_qty_amt=$data_array[$item_key]['item_transfer_issue_amt'];
						$transfer_in_qty_amt=$data_array[$item_key]['item_transfer_receive_amt'];

						$openingBalance = $data_array[$item_key]['rcv_total_opening']-$data_array[$item_key]['iss_total_opening'];
						$openingBalanceValue = $data_array[$item_key]['rcv_total_opening_amt']-$data_array[$item_key]['iss_total_opening_amt'];
						if($openingBalance==0)
						{
							$openingBalanceValue=0;
							$openingRate=0;
						}
						else
						{
							if($openingBalanceValue>0 && $openingBalance>0) $openingRate=$openingBalanceValue/$openingBalance; else $openingRate=0;
						}
						
						$totalReceive = $data_array[$item_key]['purchase']+$data_array[$item_key]['issue_return']+$transfer_in_qty;//+$openingBalance
						$totalIssue = $data_array[$item_key]['issue']+$data_array[$item_key]['receive_return']+$transfer_out_qty;

						$totalReceive_amt = $data_array[$item_key]['purchase_amt']+$data_array[$item_key]['issue_return_amt']+$transfer_in_qty_amt;
						$totalIssue_amt = $data_array[$item_key]['issue_amt']+$data_array[$item_key]['receive_return_amt']+$transfer_out_qty_amt;

						$closingStock=$openingBalance+$totalReceive-$totalIssue;
						$stockValue=$openingBalanceValue+$totalReceive_amt-$totalIssue_amt;
						if($closingStock==0)
						{
							$stockValue=0;
							$avg_rate=0;
						}
						else
						{
							if($stockValue>0 && $closingStock>0) $avg_rate=$stockValue/$closingStock; else $avg_rate=0;
						}
						
						//$closingStock=$opening+$totalReceive-$totalIssue;
						if((($get_upto==1 && $ageOfDays>$txt_days) || ($get_upto==2 && $ageOfDays<$txt_days) || ($get_upto==3 && $ageOfDays>=$txt_days) || ($get_upto==4 && $ageOfDays<=$txt_days) || ($get_upto==5 && $ageOfDays==$txt_days) || $get_upto==0) && (($get_upto_qnty==1 && $closingStock>$txt_qnty) || ($get_upto_qnty==2 && $closingStock<$txt_qnty) || ($get_upto_qnty==3 && $closingStock>=$txt_qnty) || ($get_upto_qnty==4 && $closingStock<=$txt_qnty) || ($get_upto_qnty==5 && $closingStock==$txt_qnty) || $get_upto_qnty==0))
						{

							if($value_with==1)
							{
								if(number_format($closingStock,2)>0.00 || number_format($openingBalance,2)>0.00 )
								{
									if($closingStock <= $re_order_label){$bgcolor="red";}elseif($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" style=" word-wrap:break-word; <? echo $stylecolor; ?> "  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="30" class="wrd_brk" align="center"><? echo $i; ?></td>
										<td width="100" class="wrd_brk"><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
										<td width="100" class="wrd_brk" title="<?= $row[csf("item_group_id")];?>"><? echo $itemgroupArr[$row[csf("item_group_id")]]; ?></td>
										<td width="80" class="wrd_brk"><? echo $row[csf("sub_group_name")]; ?></td>
										<td width="180" class="wrd_brk"><? echo $row[csf("item_description")]; ?></td>
										<td width="70" class="wrd_brk"><? echo $row[csf("item_size")]; ?></td>
										<td width="50" class="wrd_brk" align="center"><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></td>
										<td width="100" class="wrd_brk" align="right"><? echo number_format($openingRate,4); ?></td>
										<td width="100" class="wrd_brk" align="right"><? echo number_format($openingBalance,4); $tot_open_bl+=$openingBalance; ?></td>
										<td width="110" class="wrd_brk" align="right"><? echo number_format($openingBalanceValue,3); $tot_open_bl_amt+=$openingBalanceValue; ?></td>
										<td width="80" class="wrd_brk" align="right"><? echo number_format($data_array[$item_key]['purchase'],3); $tot_purchase+=$data_array[$item_key]['purchase']; ?></td>
										<td width="80" class="wrd_brk" align="right"><? echo number_format($transfer_in_qty,3); $tot_transfer_in_qty+=$transfer_in_qty; ?></td>
										<td width="80" class="wrd_brk" align="right"><? echo number_format($data_array[$item_key]['issue_return'],3); $tot_issue_return+=$data_array[$item_key]['issue_return'];//$row[csf("issue_return")]; ?></td>
										<td width="100" class="wrd_brk" align="right"><? echo number_format($totalReceive,3); $tot_total_receive+=$totalReceive; ?></td>
										<td width="80" class="wrd_brk" align="right"><? echo number_format($issue_qty,3); $tot_issue+=$issue_qty; ?></td>
										<td width="80" class="wrd_brk" align="right"><? echo number_format($transfer_out_qty,3); $tot_transfer_out_qty+=$transfer_out_qty; ?></td>
										<td width="80" class="wrd_brk" align="right"><? echo number_format($data_array[$item_key]['receive_return'],3); $tot_rec_return+=$data_array[$item_key]['receive_return'];//$issue_result[0][csf('receive_return')]; ?></td>
										<td width="100" class="wrd_brk" align="right"><? echo number_format($totalIssue,3); $tot_total_issue+=$totalIssue; ?></td>
										<td width="100" class="wrd_brk" align="right"><? echo number_format($closingStock,4); $tot_closing_stock+=$closingStock; ?></td>
										<td width="80" class="wrd_brk" align="right"><? if(number_format($closingStock,4)>0) echo number_format($avg_rate,4); else echo "0.00"; //number_format($row[csf("avg_rate")],2); $tot_avg_rate+=$row[csf("avg_rate")]; ?></td>
										<td width="100" align="right"><p><? if(number_format($closingStock,4)>0) echo number_format($stockValue,3); else echo "0.00"; $tot_stock_value+=$stockValue; ?></p></td>
										<td align="center"><p><? echo $compliance_arr[$data_array[$item_key]['IS_COMPLIANCE']];?></p></td>
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
									if($closingStock <= $re_order_label){$bgcolor="red";}elseif($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" style=" word-wrap:break-word; <? echo $stylecolor; ?> "  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="30" align="center"><? echo $i; ?></td>
										<td width="100" class="wrd_brk"><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
										<td width="100" class="wrd_brk" title="<?= $row[csf("item_group_id")];?>"><? echo $itemgroupArr[$row[csf("item_group_id")]]; ?></td>
										<td width="80" class="wrd_brk"><? echo $row[csf("sub_group_name")]; ?></td>
										<td width="180" class="wrd_brk"><? echo $row[csf("item_description")]; ?></td>
										<td width="70" class="wrd_brk"><? echo $row[csf("item_size")]; ?></td>
										<td width="50" align="center"><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></td>
										<td width="100" align="right"><? echo number_format($openingRate,4); ?></td>
										<td width="100" align="right"><? echo number_format($openingBalance,4); $tot_open_bl+=$openingBalance; ?></td>
										<td width="110" align="right"><? echo number_format($openingBalanceValue,3); $tot_open_bl_amt+=$openingBalanceValue; ?></td>
										<td width="80" align="right"><? echo number_format($data_array[$item_key]['purchase'],3); $tot_purchase+=$data_array[$item_key]['purchase']; ?></td>
										<td width="80" align="right"><? echo number_format($transfer_in_qty,3); $tot_transfer_in_qty+=$transfer_in_qty; ?></td>
										<td width="80" align="right"><? echo number_format($data_array[$item_key]['issue_return'],3); $tot_issue_return+=$data_array[$item_key]['issue_return'];//$row[csf("issue_return")]; ?></td>
										<td width="100" align="right"><? echo number_format($totalReceive,3); $tot_total_receive+=$totalReceive; ?></td>
										<td width="80" align="right"><? echo number_format($issue_qty,3); $tot_issue+=$issue_qty; ?></td>
										<td width="80" align="right"><? echo number_format($transfer_out_qty,3); $tot_transfer_out_qty+=$transfer_out_qty; ?></td>
										<td width="80" align="right"><? echo number_format($data_array[$item_key]['receive_return'],3); $tot_rec_return+=$data_array[$item_key]['receive_return'];//$issue_result[0][csf('receive_return')]; ?></td>
										<td width="100" align="right"><? echo number_format($totalIssue,3); $tot_total_issue+=$totalIssue; ?></td>
										<td width="100" align="right"><? echo number_format($closingStock,4); $tot_closing_stock+=$closingStock; ?></td>
										<td width="80" align="right"><? if(number_format($closingStock,4)>0) echo number_format($avg_rate,4); else echo "0.00";//number_format($row[csf("avg_rate")],2); $tot_avg_rate+=$row[csf("avg_rate")]; ?></td>
										<td width="100" align="right"><? if(number_format($closingStock,4)>0) echo number_format($stockValue,3); else echo "0.00"; $tot_stock_value+=$stockValue; ?></td>
										<td align="center"><p><? echo $compliance_arr[$data_array[$item_key]['IS_COMPLIANCE']];?></p></td>
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
				<table width="<?=$tbl_width;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer">
					<tfoot>
						<tr>
							<th width="30">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="180">&nbsp;</th>
							<th width="70">&nbsp;</th>
							<th width="50">&nbsp;</th>
							<th width="100">Total:</th>
							<th width="100" align="right" id="value_tot_open_bl"><? echo number_format($tot_open_bl,4); ?></th>
							<th width="110" align="right" id="value_tot_open_bl_amt"><? echo number_format($tot_open_bl_amt,3); ?></th>
							<th width="80" align="right" id="value_tot_purchase"><? echo number_format($tot_purchase,3); ?></th>
							<th width="80" align="right" id="value_tot_transfer_in_qty"><? echo number_format($tot_transfer_in_qty,3); ?></th>
							<th width="80" align="right" id="value_tot_issue_return"><? echo number_format($tot_issue_return,3); ?></th>
							<th width="100" align="right" id="value_tot_total_receive"><? echo number_format($tot_total_receive,3); ?></th>
							<th width="80" align="right" id="value_tot_issue"><? echo number_format($tot_issue,3); ?></th>
							<th width="80" align="right" id="value_tot_transfer_out_qty"><? echo number_format($tot_transfer_out_qty,3); ?></th>
							<th width="80" align="right" id="value_tot_rec_return"><? echo number_format($tot_rec_return,3); ?></th>
							<th width="100" align="right" id="value_tot_total_issue"><? echo number_format($tot_total_issue,3); ?></th>
							<th width="100" align="right" id="value_tot_closing_stock"><? echo number_format($tot_closing_stock,4); ?></th>
							<th width="80">&nbsp;</th>
							<th width="100" align="right" id="value_tot_stock_value"><? echo number_format($tot_stock_value,2); ?></th>
							<th>&nbsp;</th>
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
			if ($item_group_id==0) $group_id=""; else $group_id=" and b.item_group_id in($item_group_id)";
			if ($item_account_id==0) $item_account=""; else $item_account=" and a.prod_id in ($item_account_id)";
			if ($item_account_id==0) $item_account_cond=""; else $item_account_cond=" and b.id in ($item_account_id)";
			if ($cbo_compliance>0) $item_account_cond.=" and b.is_compliance = $cbo_compliance";
			//if ($cbo_store_name==0)  $store_id="";  else  $store_id=" and a.store_id=$cbo_store_name ";
			$store_id="";
			if ($cbo_store_name != "")  $store_id.=" and a.store_id in($cbo_store_name) ";


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
			// $supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
			$storeArr = return_library_array("select id,store_name from lib_store_location where status_active=1 and is_deleted=0","id","store_name");
			//$itemnameArr = return_library_array("select id,item_name from lib_item_creation where status_active=1 and is_deleted=0","id","item_name");
			$itemgroupArr = return_library_array("select id,item_name from lib_item_group where status_active=1 and is_deleted=0 and item_category in(5,6,7,23)","id","item_name");
			
			$trans_sql="SELECT b.id as PROD_ID, a.transaction_date as TRANSACTION_DATE, a.transaction_type as TRANSACTION_TYPE, a.cons_quantity as CONS_QUANTITY, a.cons_amount as CONS_AMOUNT, a.batch_lot as LOT_NO, a.receive_basis as RECEIVE_BASIS, a.STORE_ID from inv_transaction a, product_details_master b
			where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id $group_id $prod_cond $store_id $item_account $search_cond $item_category_id $item_category_cond $item_account_cond order by b.id,a.id ASC";
			
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
				
				if($row_p["TRANSACTION_TYPE"]==1 &&($row_p["RECEIVE_BASIS"]==1 || $row_p["RECEIVE_BASIS"]==2))
				{
					$data_array[$row_p["PROD_ID"]]['rcv_total_wo']+=$row_p["CONS_QUANTITY"];
				}
				if($row_p["STORE_ID"]){ $data_array[$row_p["PROD_ID"]]['store_id']=$row_p["STORE_ID"];}
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
			

			$wo_qty_arr=return_library_array("select b.item_id, sum(b.supplier_order_quantity) as qty from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.item_category in (5,6,7,23) and a.pay_mode<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by item_id","item_id","qty");

			$pi_qty_arr=return_library_array("select b.item_prod_id, sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id in (5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by item_prod_id","item_prod_id","qty");

			if($cbo_store_wise==1)
			{
				$tbl_width="2530";
			}
			else
			{
				$tbl_width="2430";
			}
			$i=1;
			ob_start();
			?>
			<style>
				.wrd_brk{word-break: break-all;}
			</style>

			<p style="color:#FF0000; font-size:16px; font-weight:bold;">Value Will Be Match Only For Item Level.</p>
			<div align="center" style="height:auto; width:auto; margin:0 auto; padding:0; width:2348">
				<table width="2448px" cellpadding="0" cellspacing="0" id="caption" align="center">
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
				<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<?=$tbl_width;?>" rules="all" id="rpt_table_header">
					<thead>
						<tr>
							<th rowspan="2" width="30">SL</th>
							<th colspan="8">Description</th>
							<th rowspan="2" width="100"> Opneing Rate</th>
							<th rowspan="2" width="100">Opening Stock</th>
							<th rowspan="2" width="110">Opening Value</th>
							<th colspan="4">Receive</th>
							<th colspan="4">Issue</th>
							<th rowspan="2" width="100">Closing Stock</th>
							<th rowspan="2" width="80">Avg. Rate</th>
							<th rowspan="2" width="120">Stock Value</th>
							<?
								if($cbo_store_wise==1)
								{
									?>
										<th rowspan="2" width="100">Store Name</th>
									<?
								}
							?>
							<th rowspan="2" width="60">DOH</th>
							<th rowspan="2" width="100">Pipe Line</th>
							<th width="150" rowspan="2">Lot</th>
							<th rowspan="2">Zero Discharge</th>
						</tr>
						<tr>
							<th width="60">Prod. ID</th>
							<th width="70">Item Code</th>
							<th width="100">Item Category</th>
							<th width="100">Item Group</th>
							<th width="80">Item Sub-group</th>
							<th width="180">Item Description</th>
							<th width="70">Item Size</th>
							<th width="50">UOM</th>
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
				<div style="width:<?=$tbl_width+18;?>px; max-height:250px; overflow-y:scroll; overflow-x:hidden;" id="scroll_body">
				<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="<?=$tbl_width;?>" rules="all" align="left">
				<?
					if ($cbo_item_category_id==0) $rcv_category_cond=" and b.item_category in(5,6,7,23)"; else $rcv_category_cond=" and b.item_category='$cbo_item_category_id'";
					//$rcv_qnty_array=return_library_array("SELECT b.prod_id, sum(b.cons_quantity) as cons_quantity from  inv_receive_master a,  inv_transaction b where a.id=b.mst_id and a.receive_basis in(1,2) and b.transaction_type=1 and b.status_active=1 $rcv_category_cond group by b.prod_id","prod_id","cons_quantity");
					
					$sql="SELECT b.id, b.item_code, b.item_category_id,b.item_group_id,b.unit_of_measure,b.item_description,b.sub_group_name, b.current_stock,avg_rate_per_unit, b.re_order_label, b.is_compliance from product_details_master b where b.status_active=1 and b.is_deleted=0 and b.company_id='$cbo_company_name' $item_category_id $group_id $prod_cond $item_account_cond $search_cond order by b.id";
					//echo $sql;die;
					$result = sql_select($sql);
					foreach($result as $row)
					{
						//if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
						$re_order_label=$row[csf("re_order_label")];
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

						$openingBalance = $data_array[$row[csf("id")]]['rcv_total_opening']-$data_array[$row[csf("id")]]['iss_total_opening'];
						$openingBalanceValue = $data_array[$row[csf("id")]]['rcv_total_opening_amt']-$data_array[$row[csf("id")]]['iss_total_opening_amt'];
						if($openingBalance==0)
						{
							$openingBalanceValue=0;
							$openingRate=0;
						}
						else
						{
							if($openingBalanceValue>0 && $openingBalance>0) $openingRate=$openingBalanceValue/$openingBalance; else $openingRate=0;
						}
						
						$totalReceive = $data_array[$row[csf("id")]]['purchase']+$data_array[$row[csf("id")]]['issue_return']+$transfer_in_qty;//+$openingBalance
						$totalIssue = $data_array[$row[csf("id")]]['issue']+$data_array[$row[csf("id")]]['receive_return']+$transfer_out_qty;

						$totalReceive_amt = $data_array[$row[csf("id")]]['purchase_amt']+$data_array[$row[csf("id")]]['issue_return_amt']+$transfer_in_qty_amt;
						$totalIssue_amt = $data_array[$row[csf("id")]]['issue_amt']+$data_array[$row[csf("id")]]['receive_return_amt']+$transfer_out_qty_amt;

						$closingStock=$openingBalance+$totalReceive-$totalIssue;
						$stockValue=$openingBalanceValue+$totalReceive_amt-$totalIssue_amt;
						if($closingStock==0)
						{
							$stockValue=0;
							$avg_rate=0;
						}
						else
						{
							if($stockValue>0 && $closingStock>0) $avg_rate=$stockValue/$closingStock; else $avg_rate=0;
						}
						
						//$closingStock=$opening+$totalReceive-$totalIssue;
						if((($get_upto==1 && $ageOfDays>$txt_days) || ($get_upto==2 && $ageOfDays<$txt_days) || ($get_upto==3 && $ageOfDays>=$txt_days) || ($get_upto==4 && $ageOfDays<=$txt_days) || ($get_upto==5 && $ageOfDays==$txt_days) || $get_upto==0) && (($get_upto_qnty==1 && $closingStock>$txt_qnty) || ($get_upto_qnty==2 && $closingStock<$txt_qnty) || ($get_upto_qnty==3 && $closingStock>=$txt_qnty) || ($get_upto_qnty==4 && $closingStock<=$txt_qnty) || ($get_upto_qnty==5 && $closingStock==$txt_qnty) || $get_upto_qnty==0))
						{
							//$avg_rate=$mrr_rate_arr[$row[csf("id")]];
							//$stockValue=$closingStock*$avg_rate;
							$pipeLine_qty=$wo_qty_arr[$row[csf("id")]]+$pi_qty_arr[$row[csf("id")]]-$data_array[$row[csf("id")]]['rcv_total_wo'];

							if($pipeLine_qty<0) $pipeLine_qty=0;
							//echo $closingStock."=".$openingBalance."<br>";
							if($value_with==1)
							{
								if(number_format($closingStock,2)>0.00 || number_format($openingBalance,2)>0.00 )
								{
									if($closingStock <= $re_order_label){$bgcolor="red";}elseif($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" style=" word-wrap:break-word; <? echo $stylecolor; ?> "  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="30" class="wrd_brk" align="center"><? echo $i; ?></td>
										<td width="60" class="wrd_brk"><? echo $row[csf("id")]; ?></td>
										<td width="70" class="wrd_brk"><p><? echo $row[csf("item_code")]; ?></p></td>
										<td width="100" class="wrd_brk"><p><? echo $item_category[$row[csf("item_category_id")]]; ?></p></td>
										<td width="100" class="wrd_brk"><p><? echo $itemgroupArr[$row[csf("item_group_id")]]; ?></p></td>
										<td width="80" class="wrd_brk"><p><? echo $row[csf("sub_group_name")]; ?></p></td>
										<td width="180" class="wrd_brk"><p><? echo $row[csf("item_description")]; ?></p></td>
										<td width="70" class="wrd_brk"><p><? echo $row[csf("item_size")]; ?></p></td>
										<td width="50" class="wrd_brk" align="center"><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></p></td>
										<td width="100" class="wrd_brk" align="right"><p><? echo number_format($openingRate,4); ?></p></td>
										<td width="100" class="wrd_brk" align="right"><p><? echo number_format($openingBalance,4); $tot_open_bl+=$openingBalance; ?></p></td>
										<td width="110" class="wrd_brk" align="right"><p><? echo number_format($openingBalanceValue,3); $tot_open_bl_amt+=$openingBalanceValue; ?></p></td>
										<td width="80" class="wrd_brk" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['purchase'],3); $tot_purchase+=$data_array[$row[csf("id")]]['purchase']; ?></p></td>
										<td width="80" class="wrd_brk" align="right"><p><? echo number_format($transfer_in_qty,3); $tot_transfer_in_qty+=$transfer_in_qty; ?></p></td>
										<td width="80" class="wrd_brk" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['issue_return'],3); $tot_issue_return+=$data_array[$row[csf("id")]]['issue_return'];//$row[csf("issue_return")]; ?></p></td>
										<td width="100" class="wrd_brk" align="right"><p><? echo number_format($totalReceive,3); $tot_total_receive+=$totalReceive; ?></p></td>
										<td width="80" class="wrd_brk" align="right"><p><? echo number_format($issue_qty,3); $tot_issue+=$issue_qty; ?></p></td>
										<td width="80" class="wrd_brk" align="right"><p><? echo number_format($transfer_out_qty,3); $tot_transfer_out_qty+=$transfer_out_qty; ?></p></td>
										<td width="80" class="wrd_brk" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['receive_return'],3); $tot_rec_return+=$data_array[$row[csf("id")]]['receive_return'];//$issue_result[0][csf('receive_return')]; ?></p></td>
										<td width="100" class="wrd_brk" align="right"><p><? echo number_format($totalIssue,3); $tot_total_issue+=$totalIssue; ?></p></td>
										<td width="100" class="wrd_brk" align="right"><p><? echo number_format($closingStock,4); $tot_closing_stock+=$closingStock; ?></p></td>
										<td width="80" class="wrd_brk" align="right"><p><? if(number_format($closingStock,4)>0) echo number_format($avg_rate,4); else echo "0.00"; //number_format($row[csf("avg_rate")],2); $tot_avg_rate+=$row[csf("avg_rate")]; ?></p></td>
										<td width="120" class="wrd_brk" align="right"><p><? if(number_format($closingStock,4)>0) echo number_format($stockValue,3); else echo "0.00"; $tot_stock_value+=$stockValue; ?></p></td>
										<?
											if($cbo_store_wise==1)
											{
												?>
													<td width="100" class="wrd_brk"><p><? echo $storeArr[$data_array[$row[csf("id")]]['store_id']];?></p></td>
												<?
											}
										?>
										<td width="60" class="wrd_brk" align="center"><p><? echo $daysOnHand;//$daysOnHand; ?></p></td>
										<td width="100" class="wrd_brk" align="right" title="<? echo $wo_qty_arr[$row[csf("id")]]."==".$pi_qty_arr[$row[csf("id")]]."--".$rcv_qnty_array[$row[csf("id")]]; ?>"><a href="##" onclick="fnc_pipeLine_details('<? echo $row[csf("id")];?>','pipe_line_popup')"><p><? echo number_format($pipeLine_qty,2); ?></p></a></td>
										<td width="150" class="wrd_brk"><p><? echo chop($data_array[$row[csf("id")]]['lot_no'],","); ?></p></td>
										<td class="wrd_brk" align="center"><p><? echo $compliance_arr[$row[csf("is_compliance")]]; ?></p></td>
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
									if($closingStock <= $re_order_label){$bgcolor="red";}elseif($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" style=" word-wrap:break-word; <? echo $stylecolor; ?> "  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="30" align="center"><? echo $i; ?></td>
										<td width="60" class="wrd_brk"><? echo $row[csf("id")]; ?></td>
										<td width="70" class="wrd_brk"><p><? echo $row[csf("item_code")]; ?></p></td>
										<td width="100" class="wrd_brk"><p><? echo $item_category[$row[csf("item_category_id")]]; ?></p></td>
										<td width="100" class="wrd_brk"><p><? echo $itemgroupArr[$row[csf("item_group_id")]]; ?></p></td>
										<td width="80" class="wrd_brk"><p><? echo $row[csf("sub_group_name")]; ?></p></td>
										<td width="180" class="wrd_brk"><p><? echo $row[csf("item_description")]; ?></p></td>
										<td width="70" class="wrd_brk"><p><? echo $row[csf("item_size")]; ?></p></td>
										<td width="50" class="wrd_brk" align="center"><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></p></td>
										<td width="100" class="wrd_brk" align="right"><p><? echo number_format($openingRate,4); ?></p></td>
										<td width="100" class="wrd_brk" align="right"><p><? echo number_format($openingBalance,4); $tot_open_bl+=$openingBalance; ?></p></td>
										<td width="110" class="wrd_brk" align="right"><p><? echo number_format($openingBalanceValue,3); $tot_open_bl_amt+=$openingBalanceValue; ?></p></td>
										<td width="80" class="wrd_brk" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['purchase'],3); $tot_purchase+=$data_array[$row[csf("id")]]['purchase']; ?></p></td>
										<td width="80" class="wrd_brk" align="right"><p><? echo number_format($transfer_in_qty,3); $tot_transfer_in_qty+=$transfer_in_qty; ?></p></td>
										<td width="80" class="wrd_brk" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['issue_return'],3); $tot_issue_return+=$data_array[$row[csf("id")]]['issue_return'];//$row[csf("issue_return")]; ?></p></td>
										<td width="100" class="wrd_brk" align="right"><p><? echo number_format($totalReceive,3); $tot_total_receive+=$totalReceive; ?></p></td>
										<td width="80" class="wrd_brk" align="right"><p><? echo number_format($issue_qty,3); $tot_issue+=$issue_qty; ?></p></td>
										<td width="80" class="wrd_brk" align="right"><p><? echo number_format($transfer_out_qty,3); $tot_transfer_out_qty+=$transfer_out_qty; ?></p></td>
										<td width="80" class="wrd_brk" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['receive_return'],3); $tot_rec_return+=$data_array[$row[csf("id")]]['receive_return'];//$issue_result[0][csf('receive_return')]; ?></p></td>
										<td width="100" class="wrd_brk" align="right"><p><? echo number_format($totalIssue,3); $tot_total_issue+=$totalIssue; ?></p></td>
										<td width="100" class="wrd_brk" align="right"><p><? echo number_format($closingStock,4); $tot_closing_stock+=$closingStock; ?></p></td>
										<td width="80" class="wrd_brk" align="right"><p><? if(number_format($closingStock,4)>0) echo number_format($avg_rate,4); else echo "0.00";//number_format($row[csf("avg_rate")],2); $tot_avg_rate+=$row[csf("avg_rate")]; ?></p></td>
										<td width="120" class="wrd_brk" align="right"><p><? if(number_format($closingStock,4)>0) echo number_format($stockValue,3); else echo "0.00"; $tot_stock_value+=$stockValue; ?></p></td>
										<?
											if($cbo_store_wise==1)
											{
												?>
													<td width="100" class="wrd_brk"><p><? echo $storeArr[$data_array[$row[csf("id")]]['store_id']];?></p></td>
												<?
											}
										?>
										<td width="60" class="wrd_brk" align="center"><p><? echo $daysOnHand;//$daysOnHand; ?></p></td>
										<td width="100" class="wrd_brk" align="right" title="<? echo $wo_qty_arr[$row[csf("id")]]."==".$pi_qty_arr[$row[csf("id")]]."--".$rcv_qnty_array[$row[csf("id")]]; ?>"><a href="##" onclick="fnc_pipeLine_details('<? echo $row[csf("id")];?>','pipe_line_popup')"><p><? echo number_format($pipeLine_qty,2); ?></p></a></td>
										<td width="150" class="wrd_brk"><p><? echo chop($data_array[$row[csf("id")]]['lot_no'],","); ?></p></td>
										<td class="wrd_brk" align="center"><p><? echo $compliance_arr[$row[csf("is_compliance")]]; ?></p></td>
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
				<table width="<?=$tbl_width;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer">
					<tfoot>
						<tr>
							<th width="30">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="70">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="180">&nbsp;</th>
							<th width="70">&nbsp;</th>
							<th width="50">&nbsp;</th>
							<th width="100">Total:</th>
							<th width="100" class="wrd_brk" align="right" id="value_tot_open_bl"><? echo number_format($tot_open_bl,4); ?></th>
							<th width="110" class="wrd_brk" align="right" id="value_tot_open_bl_amt"><? echo number_format($tot_open_bl_amt,3); ?></th>
							<th width="80" class="wrd_brk" align="right" id="value_tot_purchase"><? echo number_format($tot_purchase,3); ?></th>
							<th width="80" class="wrd_brk" align="right" id="value_tot_transfer_in_qty"><? echo number_format($tot_transfer_in_qty,3); ?></th>
							<th width="80" class="wrd_brk" align="right" id="value_tot_issue_return"><? echo number_format($tot_issue_return,3); ?></th>
							<th width="100" class="wrd_brk" align="right" id="value_tot_total_receive"><? echo number_format($tot_total_receive,3); ?></th>
							<th width="80" class="wrd_brk" align="right" id="value_tot_issue"><? echo number_format($tot_issue,3); ?></th>
							<th width="80" class="wrd_brk" align="right" id="value_tot_transfer_out_qty"><? echo number_format($tot_transfer_out_qty,3); ?></th>
							<th width="80" class="wrd_brk" align="right" id="value_tot_rec_return"><? echo number_format($tot_rec_return,3); ?></th>
							<th width="100" class="wrd_brk" align="right" id="value_tot_total_issue"><? echo number_format($tot_total_issue,3); ?></th>
							<th width="100" class="wrd_brk" align="right" id="value_tot_closing_stock"><? echo number_format($tot_closing_stock,4); ?></th>
							<th width="80" class="wrd_brk">&nbsp;</th>
							<th width="120" class="wrd_brk" align="right" id="value_tot_stock_value"><p><? echo number_format($tot_stock_value,2); ?></p></th>
							<?
								if($cbo_store_wise==1)
								{
									?>
										<th width="100">&nbsp;</th>
									<?
								}
							?>
							<th width="60" class="wrd_brk">&nbsp;</th>
							<th width="100" class="wrd_brk" align="right" id="value_totalpipeLine_qty"><? echo number_format($totalpipeLine_qty,2); ?></th>
							<th width="150">&nbsp;</th>
							<th>&nbsp;</th>
						</tr>
					</tfoot>
				</table>
			</div>
			<?
		}

		$html = ob_get_contents();
		ob_clean();
	
		foreach (glob("../../tmp/"."dnc_*.pdf") as $filename) {			
			@unlink($filename);
		}
		$att_file_arr=array();
		$mpdf = new mPDF();
		$mpdf->WriteHTML($html,2);
		$REAL_FILE_NAME = 'dnc_closing_stock_report_controller_' . date('d-M-Y_h-iA',strtotime($previous_date)) . '.pdf';
		$mpdf->Output('../../tmp/' . $REAL_FILE_NAME, 'F');
		$att_file_arr[]='../../tmp/'.$REAL_FILE_NAME.'**'.$REAL_FILE_NAME;

 
		$mail_item=128;
		$to="";	
		$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=$mail_item and a.company_id=".$cbo_company_name." and b.mail_user_setup_id=c.id AND a.MAIL_TYPE=1 and a.IS_DELETED=0 and a.STATUS_ACTIVE=1  and b.IS_DELETED=0 and b.STATUS_ACTIVE=1   and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";//and 
		$mail_sql=sql_select($sql);
		foreach($mail_sql as $row)
		{
			if($row[csf('email_address')]){$toMailArr[]=$row[csf('email_address')]; }
		}
		
		$to=implode(',',$toMailArr);
		$subject = "DNC Closing stock auto mail";
		$message="<b>Sir,</b><br>Please check DNC Closing stock att. file";
		

		$header=mailHeader();
		

		if($_REQUEST['isview']==1){
			if($to){
				echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
			}else{
				echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
			}
			echo  $message."<br>".$html;
		}
		else{
			if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail,$att_file_arr);}
		}

		unset($html);
	}




    exit();
}


if($action=="pipe_line_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
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

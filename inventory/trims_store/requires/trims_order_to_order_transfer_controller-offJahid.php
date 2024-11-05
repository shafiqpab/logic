<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//========== user credential start ========
$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id,store_location_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];

if ($company_id !='') {
    $company_credential_cond = "and comp.id in($company_id)";
}
if ($store_location_id !='') {
    $store_location_credential_cond = "and a.id in($store_location_id)"; 
}

// ========== user credential end ==========

if ($action=="load_room_rack_self_bin")
{
	load_room_rack_self_bin("requires/trims_order_to_order_transfer_controller",$data);
}

if ($action=="upto_variable_settings")
{
	extract($_REQUEST);
	echo $variable_inventory=return_field_value("store_method","variable_settings_inventory","company_name='$cbo_company_id' and item_category_id=4 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	exit();
}

/*if ($action=="load_drop_down_store")
{

	echo create_drop_down( "cbo_store_name", 160, "select a.id,a.store_name from lib_store_location a,lib_store_location_category  b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data and b.category_type in (4) $store_location_credential_cond group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", 0, "",0 );  	 
	exit();
}

if ($action=="load_drop_down_store_to")
{
	echo create_drop_down( "cbo_store_name_to", 160, "select a.id,a.store_name from lib_store_location a,lib_store_location_category  b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data and b.category_type in (4) $store_location_credential_cond group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", 0, "",0 );  	 
	exit();
}
*/

$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
$item_name_arr=return_library_array( "select id,item_name from lib_item_group where item_category=4 and status_active=1 and is_deleted=0 order by item_name",'id','item_name');
$color_arr=return_library_array( "select id,color_name from  lib_color where status_active=1 and is_deleted=0 order by color_name",'id','color_name');
if ($action=="order_popup")
{
	echo load_html_head_contents("Order Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 
	<script>
		function js_set_value(data)
		{
			$('#order_id').val(data);
			parent.emailwindow.hide();
		}
    </script>
</head>
<body>
<div align="center" style="width:880px;">
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:870px;margin-left:10px">
        <legend>Enter search words</legend>
            <table cellpadding="0" cellspacing="0" width="800" class="rpt_table">
                <thead>
                    <th>Buyer Name</th>
                    <th>Order No</th>
                    <th width="230">Shipment Date Range</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
                        <input type="hidden" name="order_id" id="order_id" class="text_boxes" value="">
                    </th>
                </thead>
                <tr class="general">
                    <td>
						<?
							echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",$data[0] );
						?>
                    </td>
                    <td>
                        <input type="text" style="width:130px;" class="text_boxes" name="txt_order_no" id="txt_order_no" />
                    </td>
                    <td>
                    	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" readonly>
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" readonly>
                    </td>
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_order_no').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $type; ?>', 'create_po_search_list_view', 'search_div', 'trims_order_to_order_transfer_controller', 'setFilterGrid(\'list_view\',-1);')" style="width:100px;" />
                    </td>
                </tr>
                <tr>
                    <td colspan="4" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
            </table>
        	<div style="margin-top:10px" id="search_div"></div> 
		</fieldset>
	</form>
</div>    
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=='create_po_search_list_view')
{
	$data=explode('_',$data);
	if ($data[0]==0) $buyer="%%"; else $buyer=$data[0];
	$search_string="%".trim($data[1])."%";
	$company_id=$data[2];
	if ($data[3]!="" &&  $data[4]!="")
	{
		if($db_type==0)
		{
			$shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3],'','',1)."' and '".change_date_format($data[4],'','',1)."'";
		} 
	}
	else $shipment_date ="";
	$type=$data[5]; 
	$arr=array (2=>$company_arr,3=>$buyer_arr);
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	if($type=="from") $status_cond=" and b.status_active in(1,3)"; else $status_cond=" and b.status_active=1";
	$sql= "select a.job_no_prefix_num, $year_field, a.job_no,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_id and a.buyer_name like '$buyer' and b.po_number like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $status_cond $shipment_date order by b.id, b.pub_shipment_date";  
	 
	echo create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No,Job Qty.,PO number,PO Quantity,Shipment Date", "70,60,70,80,120,90,110,90,80","850","200",0, $sql , "js_set_value", "id", "", 1, "0,0,company_name,buyer_name,0,0,0,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,job_quantity,po_number,po_quantity,shipment_date", "",'','0,0,0,0,0,1,0,1,3');
	exit();
}

if($action=='populate_data_from_order')
{
	$data=explode("**",$data);
	$po_id=$data[0];
	$which_order=$data[1];
	//$data_array=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$po_id");
	
	
	if($which_order=='from')
	{
		$data_array=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$po_id");
	}
	else
	{
		$data_array=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$po_id");
	}
	
	
	
	foreach ($data_array as $row)
	{ 
		$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
		foreach($gmts_item_id as $item_id)
		{
			if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
		}
		echo "document.getElementById('txt_".$which_order."_order_id').value 			= '".$po_id."';\n";
		echo "document.getElementById('txt_".$which_order."_order_no').value 			= '".$row[csf("po_number")]."';\n";
		echo "document.getElementById('txt_".$which_order."_po_qnty').value 			= '".$row[csf("po_quantity")]."';\n";
		echo "document.getElementById('cbo_".$which_order."_buyer_name').value 			= '".$row[csf("buyer_name")]."';\n";
		echo "document.getElementById('txt_".$which_order."_style_ref').value 			= '".$row[csf("style_ref_no")]."';\n";
		echo "document.getElementById('txt_".$which_order."_job_no').value 				= '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('txt_".$which_order."_gmts_item').value 			= '".$gmts_item."';\n";
		echo "document.getElementById('txt_".$which_order."_shipment_date').value 		= '".change_date_format($row[csf("shipment_date")])."';\n";

		exit();
	}
}

if($action=="load_drop_down_item_desc")
{
	$item_description=array();
	$sql="select a.id, a.product_name_details from product_details_master a, order_wise_pro_details b where a.id=b.prod_id and b.po_breakdown_id=$data and b.entry_form in(24,78,12) and b.trans_type in(1,5) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0";
	$dataArray=sql_select($sql);	
	foreach($dataArray as $row)
	{
		$item_description[$row[csf('id')]]=$row[csf('product_name_details')];
	}
	echo create_drop_down( "cbo_item_desc", 403, $item_description,'', 1, "--Select Item Description--",'0','','1');  
	exit();
}
if($action=="show_ile_load_uom")
{
	$data=explode("_",$data);
	$uom=$trim_group_arr[$data[0]]['uom'];
	echo "document.getElementById('cbo_uom').value 	= '".$data[1]."';\n";
	exit();	
}

if($action=="show_dtls_list_view")
{
	 // $sql_data="select  b.dtls_id,c.receive_qnty from order_wise_pro_details b, inv_trims_entry_dtls c where  b.dtls_id=c.id   and b.entry_form in(24) and b.po_breakdown_id=$data and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	  // select id, from_prod_id, transfer_qnty, item_category, uom from inv_item_transfer_dtls where mst_id='153' and status_active = '1' and is_deleted = '0'
	 // $data2=" select id, from_order_id from inv_item_transfer_mst where from_order_id=$data and transfer_criteria=4 and entry_form=78 and status_active ='1' and is_deleted ='0' ";
	/* $sql_trims=sql_select("select  LISTAGG(cast(b.prod_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.prod_id) as prod_id
			from 			
				product_details_master a, order_wise_pro_details b, inv_trims_entry_dtls c
			where  
				a.id=b.prod_id and b.dtls_id=c.id and a.item_category_id=4 and b.entry_form in(24,25,73,49,78) and b.po_breakdown_id=$data and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0");	
				$items_id=$sql_trims[0][csf('prod_id')];*/
				
	$data_ref=explode("__",$data);
	$order_id=$data_ref[0];
	$store_id=$data_ref[1];
	$item_group_sql=sql_select("select a.id, b.conversion_factor, b.order_uom from product_details_master a, lib_item_group b where a.item_group_id=b.id and a.item_category_id=4 and a.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach($item_group_sql as $row)
	{
		$conversion_factor[$row[csf("id")]]=$row[csf("conversion_factor")];
		$group_order_uom[$row[csf("id")]]=$row[csf("order_uom")];
	}
	unset($item_group_sql);
				
	/*$sql_trim = "select b.po_breakdown_id,a.id,
	sum(case when b.entry_form in(25) and b.trans_type in(2)  then b.quantity else 0 end) as issue_qty,
	sum(case when b.entry_form in(73) and b.trans_type in(4)  then b.quantity else 0 end) as issue_return_qty,
	sum(case when b.entry_form in(49)  and b.trans_type in(3) then b.quantity else 0 end) as recv_return_qty,
	sum(case when b.entry_form in(78,112) and b.trans_type in(6)  then b.quantity else 0 end) as item_transfer_issue,
	sum(case when b.entry_form in(78,112)  and b.trans_type in(5) then b.quantity else 0 end) as item_transfer_receive
	from product_details_master a, order_wise_pro_details b, inv_transaction c
	where  a.id=b.prod_id and b.trans_id=c.id and a.item_category_id=4 and c.item_category=4 and b.entry_form in(25,49,73,78,112) and b.trans_type in(2,3,4,6) and c.transaction_type in(2,3,4,6) and b.po_breakdown_id in($order_id) and c.store_id in($store_id) and c.status_active=1  and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id,a.id";
	*/
	
				
	$sql_trim = "select b.po_breakdown_id,a.id,
	sum(case when b.entry_form in(25) and b.trans_type in(2)  then b.quantity else 0 end) as issue_qty,
	sum(case when b.entry_form in(73) and b.trans_type in(4)  then b.quantity else 0 end) as issue_return_qty,
	sum(case when b.entry_form in(49)  and b.trans_type in(3) then b.quantity else 0 end) as recv_return_qty,
	sum(case when b.entry_form in(78,112) and b.trans_type in(6)  then b.quantity else 0 end) as item_transfer_issue,
	sum(case when b.entry_form in(78,112)  and b.trans_type in(5) then b.quantity else 0 end) as item_transfer_receive
	from product_details_master a, order_wise_pro_details b, inv_transaction c
	where a.id=b.prod_id and b.trans_id=c.id and a.id=c.prod_id and a.item_category_id=4 and c.item_category=4 and a.entry_form=24 and b.entry_form in(25,49,73,78,112) and b.trans_type in(2,3,4,6) and c.transaction_type in(2,3,4,6) and b.po_breakdown_id in($order_id) and c.store_id in($store_id) and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 group by b.po_breakdown_id,a.id";
	
	//echo $sql_trim;//die;
	
	/*$sql_trim = "select po_breakdown_id, prod_id,
	sum(case when entry_form=73 and trans_type=4 then quantity else 0 end) as issue_return_qty,
	sum(case when entry_form=49 and trans_type=3 then quantity else 0 end) as recv_return_qty,
	
	sum(case when entry_form=78 and trans_type=6 then quantity else 0 end) as item_transfer_issue,
	sum(case when entry_form=78 and b.trans_type=5 then quantity else 0 end) as item_transfer_receive
	from order_wise_pro_details
	where entry_form in (78,73,49) and status_active=1 and is_deleted=0 group by po_breakdown_id, prod_id";	*/
	
	$data_array=sql_select($sql_trim);
	$trims_qty_array=array();
	foreach($data_array as $row)
	{
		$trims_qty_array[$row[csf('id')]]['issue_qty']=$row[csf('issue_qty')]*$conversion_factor[$row[csf('id')]];
		$trims_qty_array[$row[csf('id')]]['issue_return_qty']=$row[csf('issue_return_qty')]*$conversion_factor[$row[csf('id')]];
		$trims_qty_array[$row[csf('id')]]['recv_return_qty']=$row[csf('recv_return_qty')]*$conversion_factor[$row[csf('id')]];
		$trims_qty_array[$row[csf('id')]]['item_transfer_issue']=$row[csf('item_transfer_issue')]*$conversion_factor[$row[csf('id')]];
	}
				
	 
   /*$data2="select  a.dtls_id as id,a.prod_id,a.po_breakdown_id as po_id,
	sum(CASE WHEN a.entry_form ='78' and a.trans_type=5 THEN a.quantity ELSE 0 END) AS transfer_out_qnty,
	sum(CASE WHEN a.entry_form ='78' and a.trans_type=6 THEN a.quantity ELSE 0 END) AS transfer_in_qnty
	from order_wise_pro_details a,inv_item_transfer_dtls b where a.dtls_id=b.id and a.po_breakdown_id=$data  and a.status_active=1 and a.is_deleted=0 group by a.po_breakdown_id,a.dtls_id,a.prod_id";
  $data_result2=sql_select($data2);
 
 //$mst_id=$data_result2[0][csf('id')];
 $mst_id_cond='';$transfer_prev_qty=array();
 foreach($data_result2 as $row)
 {
	 $transfer_prev_qty[$row[csf('po_id')]][$row[csf('prod_id')]]['transfer_out_qnty']=$row[csf('transfer_out_qnty')];
	$transfer_prev_qty[$row[csf('po_id')]][$row[csf('prod_id')]]['transfer_in_qnty']=$row[csf('transfer_in_qnty')];
	 //if( $mst_id_cond=="") $mst_id_cond=$row[csf('id')];else $mst_id_cond.=",".$row[csf('id')];
 }
	
	$tr_iss_arr=array();
	$sql_iss = "select po_breakdown_id, prod_id, sum(case when entry_form in(25) then quantity else 0 end) as issue_qty
			from 			
				order_wise_pro_details
			where  
				entry_form=25 and status_active=1 and is_deleted=0
				group by po_breakdown_id, prod_id";	
	$sql_iss_res=sql_select($sql_iss);
	foreach($sql_iss_res as $row)	
	{
		$tr_iss_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]= $row[csf('issue_qty')];
	}*/
	
	$sql_order_rate="select c.id as trans_id, b.po_breakdown_id, b.prod_id, c.cons_quantity, c.cons_amount
	from product_details_master a, order_wise_pro_details b, inv_transaction c
	where a.id=b.prod_id and b.trans_id=c.id and a.item_category_id='4' and a.entry_form=24 and b.trans_type in(1,5) and b.entry_form in(24,78,112) and b.po_breakdown_id in ($order_id) and c.store_id=$store_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.current_stock>0";
	//echo $sql_order_rate;die;
	$sql_order_rate_result=sql_select($sql_order_rate);
	$order_item_data=array();
	foreach($sql_order_rate_result as $row)
	{
		if($trans_id_check[$row[csf("trans_id")]]=="")
		{
			$trans_id_check[$row[csf("trans_id")]]=$row[csf("trans_id")];
			$order_item_data[$row[csf("prod_id")]]["cons_quantity"]+=$row[csf("cons_quantity")];
			$order_item_data[$row[csf("prod_id")]]["cons_amount"]+=$row[csf("cons_amount")];
		}
	}
	
	$sql = "select a.id, a.product_name_details, b.po_breakdown_id as po_id, a.item_group_id, a.unit_of_measure, a.item_color, sum(b.quantity) as recv_qty, max(c.order_uom) as order_uom
	from product_details_master a, order_wise_pro_details b, inv_transaction c
	where a.id=b.prod_id and b.trans_id=c.id and a.item_category_id=4 and c.item_category=4 and a.entry_form=24 and b.entry_form in(24,78,112) and b.trans_type in(1,5) and c.transaction_type in(1,5) and b.po_breakdown_id in($order_id) and c.store_id in($store_id) and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0
	group by b.po_breakdown_id, a.id, a.item_group_id, a.product_name_details, a.unit_of_measure, a.item_color
	order by a.item_group_id";	
	
	//echo $sql;
	$data_array=sql_select($sql);
	?>
    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="540">
    	<thead>
            <th width="40">Prod Id</th>
            <th width="160">Item Description</th>
            <th width="100">Item Name</th>
            <th width="60">UOM</th>
            <th width="100">GMTS Color</th>
            <th width="">Current Stock</th>
        </thead>
    </table>
    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="540" id="tbl_list_search">
        <tbody>
            <? 
            $i=1;
            foreach($data_array as $row)
            {  
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				//$receive_qnty=$row[csf('recv_qty')]*$conversion_factor[$row[csf('id')]];
				$receive_qnty=$row[csf('recv_qty')];
				$issue_qty=$trims_qty_array[$row[csf('id')]]['issue_qty'];
				$issue_return_qty=$trims_qty_array[$row[csf('id')]]['issue_return_qty'];
				$recv_return_qty=$trims_qty_array[$row[csf('id')]]['recv_return_qty'];
				$transfer_out_qty=$trims_qty_array[$row[csf('id')]]['item_transfer_issue'];
				
				$order_rate=($order_item_data[$row[csf("id")]]["cons_amount"]/$order_item_data[$row[csf("id")]]["cons_quantity"]);
				
				$order_uom=$row[csf('order_uom')];
				if($order_uom=="")
				{
					$order_uom=$group_order_uom[$row[csf("id")]];
				}
			 
				$current_stock_qty=($receive_qnty+$issue_return_qty)-($issue_qty+$recv_return_qty+$transfer_out_qty);
				if($current_stock_qty>0)
				{
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $row[csf('id')]."**".$row[csf('product_name_details')]."**".$row[csf('item_group_id')]."**".$row[csf('unit_of_measure')]."**".$current_stock_qty."**".$order_rate; ?>");change_color("tr_<? echo $i; ?>","<? echo $bgcolor;?>");' id="tr_<? echo $i; ?>" style="cursor:pointer">
						<td width="40"><p><? echo $row[csf('id')]; ?>&nbsp;</p></td>
                        <td width="160"><p><? echo $row[csf('product_name_details')]; ?>&nbsp;</p></td>
						<td width="100"><p>&nbsp;<? echo $item_name_arr[$row[csf('item_group_id')]]; ?></p></td>
						<td width="60" align="center"><p>&nbsp;<? echo $unit_of_measurement[$order_uom]; ?></p></td>
						<td width="100"><p>&nbsp;<? echo $color_arr[$row[csf('item_color')]]; ?></p></td>
						<td align="right"><p>&nbsp;<? echo number_format($current_stock_qty,4);//number_format ?></p></td>
					</tr>
					<? 
					$i++; 
				}
            } 
            ?>
        </tbody>
    </table>
	<?
	exit();
}
if ($action=="orderToorderTransfer_popup")
{
	echo load_html_head_contents("Order To Order Transfer Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 

	<script>
		
		function js_set_value(data)
		{
			var id = data.split("_");
			$('#transfer_id').val(id[0]);
			$('#hidden_posted_in_account').val(id[1]);
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
<div align="center" style="width:800px;">
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:800px;margin-left:10px">
        <legend>Enter search words</legend>
            <table cellpadding="0" cellspacing="0" width="800" class="rpt_table">
                <thead>
                    <th width="200">Search By</th>
                    <th width="200" id="search_by_td_up">Please Enter Transfer ID</th>
                    <th width="250">Date Range</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
                        <input type="hidden" name="transfer_id" id="transfer_id" class="text_boxes" value="">
                        <input type="hidden" name="hidden_posted_in_account" id="hidden_posted_in_account" class="text_boxes" value="">
                    </th>
                </thead>
                <tr class="general">
                    <td>
						<?
							$search_by_arr=array(1=>"Transfer ID",2=>"Challan No.");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../../') ";
							echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>
                    <td id="search_by_td">
                        <input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                    </td>
                    <td>
                        <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px">To
                        <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px">
                    </td>
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value, 'create_transfer_search_list_view', 'search_div', 'trims_order_to_order_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                    </td>
                </tr>
                <tr>
                	<td colspan="4" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
            </table>
        	<div style="margin-top:10px" id="search_div"></div> 
		</fieldset>
	</form>
</div>    
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=='create_transfer_search_list_view')
{
	$data = explode("_",$data);
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$company_id =$data[2];
	$date_form=$data[3];
	$date_to =$data[4];
	$year_id=$data[5];
	
	if($db_type==0)
	{
		$date_form=change_date_format($date_form,'yyyy-mm-dd');
		$date_to=change_date_format($date_to,'yyyy-mm-dd');
	}
	else
	{
		$date_form=change_date_format($date_form,'','',1);
		$date_to=change_date_format($date_to,'','',1);
	}
	
	if($date_form!="" && $date_to!="") $date_cond=" and transfer_date between '$date_form' and '$date_to'";
	
	//echo $date_form."=".$date_to."=".$year_id;die;
	
	
	if($search_by==1)
		$search_field="transfer_system_id";	
	else
		$search_field="challan_no";
	
	if($db_type==0) $year_field="YEAR(insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	if($year_id>0)
	{
		if($db_type==0)
		{
			$year_condition=" and YEAR(insert_date)='$year_id'";
		}
		else
		{
			$year_condition=" and to_char(insert_date,'YYYY')='$year_id'";
		}
	}
	
 	$sql="select id, transfer_prefix_number, transfer_system_id, $year_field, challan_no, company_id, transfer_date, transfer_criteria, item_category, is_posted_account from inv_item_transfer_mst where item_category=4 and company_id=$company_id and $search_field like '$search_string' and transfer_criteria=4 and status_active=1 and is_deleted=0 $date_cond $year_condition";
	
	$arr=array(3=>$company_arr,5=>$item_transfer_criteria,6=>$item_category);

	echo  create_list_view("tbl_list_search", "Transfer ID,Year,Challan No,Company,Transfer Date,Transfer Criteria,Item Category", "80,70,100,110,90,120","760","250",0, $sql, "js_set_value", "id,is_posted_account", "", 1, "0,0,0,company_id,0,transfer_criteria,item_category", $arr, "transfer_prefix_number,year,challan_no,company_id,transfer_date,transfer_criteria,item_category", '','','0,0,0,0,3,0,0');
	
	exit();
}

if($action=='populate_data_from_transfer_master')
{
	$data_array=sql_select("select a.transfer_system_id, a.challan_no, a.company_id, a.transfer_date, a.item_category, a.from_order_id,a.to_order_id, a.from_store_id, a.to_store_id,b.from_store,b.to_store,b.floor_id,b.room,b.rack,b.shelf,b.bin_box,b.to_floor_id,b.to_room,b.to_rack,b.to_shelf,b.to_bin_box from inv_item_transfer_mst a,inv_item_transfer_dtls b where a.id='$data' and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	
	$company_id=$data_array[0][csf("company_id")];
	$variable_inventory_sql=sql_select("select store_method, rack_balance from variable_settings_inventory  where company_name=$company_id and item_category_id=4 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	$store_method=$variable_inventory_sql[0][csf("store_method")];

	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('update_id').value 					= '".$data."';\n";
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("transfer_system_id")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_company_id_to').value 			= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('store_update_upto').value 			= '".$store_method."';\n";


		echo "load_room_rack_self_bin('requires/trims_order_to_order_transfer_controller*4', 'store','from_store_td', '".$row[csf('company_id')]."','"."',this.value);\n";
		echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("from_store")]."';\n";
		echo "load_room_rack_self_bin('requires/trims_order_to_order_transfer_controller*4', 'floor','floor_td', '".$row[csf('company_id')]."','"."','".$row[csf('from_store')]."',this.value);\n";
		echo "document.getElementById('cbo_floor').value 				= '".$row[csf("floor_id")]."';\n";
		echo "load_room_rack_self_bin('requires/trims_order_to_order_transfer_controller*4', 'room','room_td', '".$row[csf('company_id')]."','"."','".$row[csf('from_store')]."','".$row[csf('floor_id')]."',this.value);\n";
		echo "document.getElementById('cbo_room').value 				= '".$row[csf("room")]."';\n";
		echo "load_room_rack_self_bin('requires/trims_order_to_order_transfer_controller*4', 'rack','rack_td', '".$row[csf('company_id')]."','"."','".$row[csf('from_store')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."',this.value);\n";
		echo "document.getElementById('txt_rack').value 				= '".$row[csf("rack")]."';\n";
		echo "load_room_rack_self_bin('requires/trims_order_to_order_transfer_controller*4', 'shelf','shelf_td', '".$row[csf('company_id')]."','"."','".$row[csf('from_store')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."',this.value);\n";
		echo "document.getElementById('txt_shelf').value 				= '".$row[csf("shelf")]."';\n";
		echo "load_room_rack_self_bin('requires/trims_order_to_order_transfer_controller*4', 'bin','bin_td', '".$row[csf('company_id')]."','"."','".$row[csf('from_store')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."','".$row[csf('shelf')]."',this.value);\n";
		echo "document.getElementById('cbo_bin').value 				= '".$row[csf("bin_box")]."';\n";


		echo "load_room_rack_self_bin('requires/trims_order_to_order_transfer_controller*4*cbo_store_name_to', 'store','to_store_td', '".$row[csf('company_id')]."','"."',this.value);\n";
		echo "document.getElementById('cbo_store_name_to').value 				= '".$row[csf("to_store")]."';\n";

		echo "load_room_rack_self_bin('requires/trims_order_to_order_transfer_controller*4*cbo_floor_to', 'floor','floor_td_to', '".$row[csf('company_id')]."','"."','".$row[csf('to_store')]."',this.value);\n";
		echo "document.getElementById('cbo_floor_to').value 			= '".$row[csf("to_floor_id")]."';\n";
		echo "load_room_rack_self_bin('requires/trims_order_to_order_transfer_controller*4*cbo_room_to', 'room','room_td_to', '".$row[csf('company_id')]."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."',this.value);\n";
		echo "document.getElementById('cbo_room_to').value 				= '".$row[csf("to_room")]."';\n";
		echo "load_room_rack_self_bin('requires/trims_order_to_order_transfer_controller*4*txt_rack_to', 'rack','rack_td_to', '".$row[csf('company_id')]."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."','".$row[csf('to_room')]."',this.value);\n";
		echo "document.getElementById('txt_rack_to').value 				= '".$row[csf("to_rack")]."';\n";
		echo "load_room_rack_self_bin('requires/trims_order_to_order_transfer_controller*4*txt_shelf_to', 'shelf','shelf_td_to', '".$row[csf('company_id')]."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."','".$row[csf('to_room')]."','".$row[csf('to_rack')]."',this.value);\n";
		echo "document.getElementById('txt_shelf_to').value 			= '".$row[csf("to_shelf")]."';\n";
		echo "load_room_rack_self_bin('requires/trims_order_to_order_transfer_controller*4*cbo_bin_to', 'bin','bin_td_to', '".$row[csf('company_id')]."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."','".$row[csf('to_room')]."','".$row[csf('to_rack')]."','".$row[csf('to_shelf')]."',this.value);\n";
		echo "document.getElementById('cbo_bin_to').value 			= '".$row[csf("to_bin_box")]."';\n";

		echo "$('#cbo_store_name').attr('disabled','disabled');\n";
		echo "$('#cbo_floor').attr('disabled','disabled');\n";
		echo "$('#cbo_room').attr('disabled','disabled');\n";
		echo "$('#txt_rack').attr('disabled','disabled');\n";
		echo "$('#txt_shelf').attr('disabled','disabled');\n";
		echo "$('#cbo_bin').attr('disabled','disabled');\n";


		echo "$('#cbo_store_name_to').attr('disabled','disabled');\n";
		echo "$('#cbo_floor_to').attr('disabled','disabled');\n";
		echo "$('#cbo_room_to').attr('disabled','disabled');\n";
		echo "$('#txt_rack_to').attr('disabled','disabled');\n";
		echo "$('#txt_shelf_to').attr('disabled','disabled');\n";
		echo "$('#cbo_bin_to').attr('disabled','disabled');\n";
		//echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("from_store_id")]."';\n";
		//echo "document.getElementById('cbo_store_name_to').value 				= '".$row[csf("to_store_id")]."';\n";

		echo "document.getElementById('txt_transfer_date').value 			= '".change_date_format($row[csf("transfer_date")])."';\n";
		
		echo "get_php_form_data('".$row[csf("from_order_id")]."**from'".",'populate_data_from_order','requires/trims_order_to_order_transfer_controller');\n";
		echo "get_php_form_data('".$row[csf("to_order_id")]."**to'".",'populate_data_from_order','requires/trims_order_to_order_transfer_controller');\n";
		echo "$('#cbo_company_id').attr('disabled','disabled');\n";
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_trims_transfer_entry',1,1);\n"; 
		exit();
	}
}

if($action=="show_transfer_listview")
{
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=4 and status_active=1 and is_deleted=0","id","product_name_details");
	
	$sql="select id, from_prod_id, transfer_qnty, item_category, uom from inv_item_transfer_dtls where mst_id='$data' and status_active = '1' and is_deleted = '0'";
	
	$arr=array(0=>$item_category,1=>$product_arr,3=>$unit_of_measurement);
	 
	echo  create_list_view("list_view", "Item Category,Item Description,Transfered Qnty,UOM", "120,250,100","650","200",0, $sql, "get_php_form_data", "id", "'populate_transfer_details_form_data'", 0, "item_category,from_prod_id,0,uom", $arr, "item_category,from_prod_id,transfer_qnty,uom", "requires/trims_order_to_order_transfer_controller",'','0,0,2,0');
	exit();
}

if($action=='populate_transfer_details_form_data')
{
	
	//$data_array=sql_select("select transfer_system_id, challan_no, company_id, transfer_date, item_category, from_order_id,to_order_id, from_store_id, to_store_id from inv_item_transfer_mst where id='$data' and status_active=1 and is_deleted=0");
	
	$data_array=sql_select("select a.from_order_id, a.to_order_id, a.from_store_id, a.to_store_id, b.id, b.mst_id, b.item_group, b.from_prod_id, b.transfer_qnty, b.item_category, b.uom from inv_item_transfer_mst a, inv_item_transfer_dtls b 
	where a.id=b.mst_id and b.id='$data'");
	foreach ($data_array as $row)
	{ 
		
		//echo "select from_order_id from inv_item_transfer_mst where id=".$row[csf('mst_id')]." and  status_active=1 and is_deleted=0 ";
		
		
		echo "document.getElementById('update_dtls_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_item_desc').value 				= '".$row[csf("from_prod_id")]."';\n";
		echo "document.getElementById('txt_transfer_qnty').value 			= '".$row[csf("transfer_qnty")]."';\n";
		echo "document.getElementById('cbo_item_category').value 			= '".$row[csf("item_category")]."';\n";
		echo "document.getElementById('txt_item_id').value 					= '".$row[csf("item_group")]."';\n";
		echo "document.getElementById('cbo_uom').value 						= '".$row[csf("uom")]."';\n";
		
		
		/* $sql_sk =sql_select( "select 
		 				sum(case when b.entry_form in(24) then b.quantity else 0 end) as recv_qty,
		 				sum(case when b.entry_form in(25) then b.quantity else 0 end) as issue_qty
						from product_details_master a, order_wise_pro_details b, inv_transaction c
			where  
				a.id=b.prod_id and b.trans_id=c.id and a.item_category_id=4 and b.entry_form in(24,25) and b.po_breakdown_id=".$cond_po_id." and b.prod_id='".$row[csf("from_prod_id")]."'  and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0");	*/
				
		
		$conversion_factor=return_field_value("b.conversion_factor as conversion_factor","product_details_master a, lib_item_group b","a.item_group_id=b.id and a.item_category_id=4 and a.entry_form=24 and a.id='".$row[csf("from_prod_id")]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","conversion_factor");
				
		$sql_trim = sql_select("select 
		sum((case when b.entry_form in(24,73,78,112) and b.trans_type in(1,4,5) then b.quantity else 0 end)-(case when b.entry_form in(25,49,78,112) and b.trans_type in(2,3,6) then b.quantity else 0 end)) as balance_qnty 
		from product_details_master a, order_wise_pro_details b, inv_transaction c
		where  a.id=b.prod_id and b.trans_id=c.id and a.item_category_id=4 and c.item_category=4 and b.entry_form in(24,25,78,73,49,112) and b.trans_type in(1,2,3,4,5,6) and c.transaction_type in(1,2,3,4,5,6) and c.status_active=1  and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.po_breakdown_id=".$row[csf("from_order_id")]." and b.prod_id='".$row[csf("from_prod_id")]."' and c.store_id ='".$row[csf("from_store_id")]."' ");
		
		
		$curr_stock=($sql_trim[0][csf('balance_qnty')]*$conversion_factor)+$row[csf("transfer_qnty")];
		echo "document.getElementById('txt_current_stock').value 			= '".$curr_stock."';\n";
		$sql_trans=sql_select("select trans_id from order_wise_pro_details where dtls_id=".$row[csf('id')]." and entry_form=78 and trans_type in(5,6) order by trans_type DESC");
		echo "document.getElementById('update_trans_issue_id').value 		= '".$sql_trans[0][csf("trans_id")]."';\n";
		echo "document.getElementById('update_trans_recv_id').value 		= '".$sql_trans[1][csf("trans_id")]."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_trims_transfer_entry',1,1);\n"; 
		
		exit();
	}
}

//data save update delete here------------------------------//
if($action=="save_update_delete")
{	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
        

	$update_trans_issue_id=str_replace("'","",$update_trans_issue_id);
	$update_trans_recv_id=str_replace("'","",$update_trans_recv_id); 
	
	$up_tr_cond="";
	if($update_trans_issue_id >0 && $update_trans_recv_id >0)
	{
		$up_tr_cond=" and id not in($update_trans_issue_id,$update_trans_recv_id)";
		$trans_sql=sql_select("select sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as bal
		from inv_transaction where status_active=1 and is_deleted=0 and prod_id=$cbo_item_desc and store_id=$cbo_store_name_to $up_tr_cond");
		$stockQnty=$trans_sql[0][csf("bal")]*1;
		$trnsQnty=str_replace("'","",$txt_transfer_qnty);
		if($stockQnty < 0 )
		{
			 echo "20**Transfer Quantity Not Allow Over Stock Quantity.";disconnect($con);die;
		}
	}
	
	$trans_sql=sql_select("select sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as bal
	from inv_transaction where status_active=1 and is_deleted=0 and prod_id=$cbo_item_desc and store_id=$cbo_store_name $up_tr_cond");
	$stockQnty=$trans_sql[0][csf("bal")]*1;
	$trnsQnty=str_replace("'","",$txt_transfer_qnty);
	if($trnsQnty > $stockQnty)
	{
		 echo "20**Transfer Quantity Not Allow Over Stock Quantity.";disconnect($con);die;
	}
	
	if($operation!=2) 
	{
		//------------Check Transfer In Date with last Transaction Date-----------------
		$is_update_cond_for_rcv = ($operation==1)? " and id not in ( $update_trans_recv_id , $update_trans_issue_id ) ": "";
		$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$cbo_item_desc and store_id=$cbo_store_name_to $is_update_cond_for_rcv and status_active = 1", "max_date");      
		if($max_recv_date != "")
		{
			$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
			$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
			if ($transfer_date < $max_recv_date) 
			{
			  echo "20**Transfer in Date Can not Be Less Than Last Transaction Date Of This Item";
				die;
			}
		}
		
		//------------Check Transfer Out Date with last Receive Date--------------------
		$is_update_cond_for_iss = ($operation==1)? " and id <> $update_trans_recv_id ": "";
		$max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$cbo_item_desc and store_id = $cbo_store_name and transaction_type in (1,4,5) $is_update_cond_for_iss and status_active = 1 and is_deleted=0", "max_date");      
		if($max_issue_date != "")
		{
			$max_issue_date = date("Y-m-d", strtotime($max_issue_date));
			$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
			if ($transfer_date < $max_issue_date) 
			{
			   echo "20**Transfer Out Date Can not Be Less Than Last Receive Date Of This Item";
				die;
			}
		}
	}
	
        
	$conversion_factor=return_field_value("b.conversion_factor as conversion_factor","product_details_master a, lib_item_group b","a.item_group_id=b.id and a.item_category_id=4 and a.entry_form=24 and a.id=$cbo_item_desc and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","conversion_factor");
	$rate=return_field_value("avg_rate_per_unit","product_details_master","id=".$cbo_item_desc,"avg_rate_per_unit");
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		
		
		$transfer_recv_num=''; $transfer_update_id='';
		$sql_budge_check=sql_select("select a.id from wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b where a.id=b.wo_pre_cost_trim_cost_dtls_id and a.trim_group=$txt_item_id and b.po_break_down_id=$txt_from_order_id");
		if(count($sql_budge_check)<1)
		{
			echo "11**This Item Not Found In Budget";
			disconnect($con);
			die;
		}
		
		$sqlCon="";	
		$store_update_upto=str_replace("'","",$store_update_upto);
		if($store_update_upto > 1)
		{
			if($store_update_upto==6)
			{
				if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
				if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and c.room=$cbo_room" ;}
				if(str_replace("'","",$txt_rack)!=0){$sqlCon.= " and c.rack=$txt_rack" ;}
				if(str_replace("'","",$txt_shelf)!=0){$sqlCon.= " and c.self=$txt_shelf" ;}
				if(str_replace("'","",$cbo_bin)!=0){$sqlCon.= " and c.bin_box=$cbo_bin" ;}
			}
			else if($store_update_upto==5)
			{
				if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
				if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and c.room=$cbo_room" ;}
				if(str_replace("'","",$txt_rack)!=0){$sqlCon.= " and c.rack=$txt_rack" ;}
				if(str_replace("'","",$txt_shelf)!=0){$sqlCon.= " and c.self=$txt_shelf" ;}
			}
			else if($store_update_upto==4)
			{
				if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
				if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and c.room=$cbo_room" ;}
				if(str_replace("'","",$txt_rack)!=0){$sqlCon.= " and c.rack=$txt_rack" ;}
			}
			else if($store_update_upto==3)
			{
				if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
				if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and c.room=$cbo_room" ;}
			}
			else if($store_update_upto==2)
			{
				if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
			}
		}
		
		$sql_trim = sql_select("select sum((case when b.entry_form in(24,73,78,112) and b.trans_type in(1,4,5)  then b.quantity else 0 end)-(case when b.entry_form in(25,49,78,112) and b.trans_type in(2,3,6)  then b.quantity else 0 end)) as balance 
		from order_wise_pro_details b, inv_transaction c
		where  b.trans_id=c.id and c.item_category=4 and c.company_id=$cbo_company_id and c.store_id =$cbo_store_name $sqlCon and b.po_breakdown_id =$txt_from_order_id and b.prod_id=$cbo_item_desc  and c.status_active=1  and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		$trim_stock=$sql_trim[0][csf("balance")];
		if($conversion_factor>0) $trim_stock=$trim_stock*$conversion_factor; 
		
		
		if(str_replace("'","",$txt_transfer_qnty)>$trim_stock)
		{
			echo "11**Transfer Quantity Not Allow Over Stock.";
			disconnect($con);
			die;
		}
		
		
		
		
		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
			
			//	$new_transfer_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'TSOTOTE', date("Y",time()), 5, "select transfer_prefix, transfer_prefix_number from inv_item_transfer_mst where company_id=$cbo_company_id and transfer_criteria=4 and item_category=$cbo_item_category and $year_cond=".date('Y',time())." order by id desc ", "transfer_prefix", "transfer_prefix_number" ));
		
			$id = return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst", $con);
			$new_transfer_system_id = explode("*", return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst",$con,1,$cbo_company_id,'TSOTOTE',78,date("Y",time()) ));
		
			//$id=return_next_id( "id", "inv_item_transfer_mst", 1 ) ;
			
			$field_array="id, transfer_prefix, transfer_prefix_number, transfer_system_id, company_id, challan_no, transfer_date, transfer_criteria, to_company,entry_form, from_order_id, to_order_id, from_store_id, to_store_id, item_category, inserted_by, insert_date";
			
			$data_array="(".$id.",'".$new_transfer_system_id[1]."',".$new_transfer_system_id[2].",'".$new_transfer_system_id[0]."',".$cbo_company_id.",".$txt_challan_no.",".$txt_transfer_date.",4,0,78,".$txt_from_order_id.",".$txt_to_order_id.",".$cbo_store_name.",".$cbo_store_name_to.",".$cbo_item_category.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			//echo "insert into inv_item_transfer_mst (".$field_array.") values ".$data_array;die;
			$transfer_recv_num=$new_transfer_system_id[0];
			$transfer_update_id=$id;
		}
		else
		{
			$field_array_update="challan_no*transfer_date*from_order_id*to_order_id*updated_by*update_date";
			$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$txt_from_order_id."*".$txt_to_order_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			/*$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0; */
			
			$transfer_recv_num=str_replace("'","",$txt_system_id);
			$transfer_update_id=str_replace("'","",$update_id);
		}
		
		$amount=str_replace("'","",$txt_transfer_qnty)*str_replace("'","",$txt_rate);
		
		//$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
		$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		
		$field_array_trans="id, mst_id, company_id, prod_id, item_category, transaction_type, transaction_date, store_id,floor_id,room,rack,self,bin_box, order_id, cons_uom, cons_quantity, cons_rate, cons_amount, inserted_by, insert_date";
		
		$data_array_trans="(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",".$cbo_item_desc.",".$cbo_item_category.",6,".$txt_transfer_date.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$txt_from_order_id.",".$cbo_uom.",".$txt_transfer_qnty.",".$txt_rate.",'".$amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		//$id_trans_recv=$id_trans+1;
		$id_trans_recv=return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);;
		$data_array_trans.=",(".$id_trans_recv.",".$transfer_update_id.",".$cbo_company_id.",".$cbo_item_desc.",".$cbo_item_category.",5,".$txt_transfer_date.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$txt_to_order_id.",".$cbo_uom.",".$txt_transfer_qnty.",".$txt_rate.",'".$amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		//echo "insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;
		/*$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; txt_item_id
		} */
		
		//$id_dtls=return_next_id( "id", "inv_item_transfer_dtls", 1 ) ;
		$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
		$field_array_dtls="id, mst_id, from_prod_id, from_store,floor_id,room,rack,shelf,bin_box, to_store,to_floor_id,to_room,to_rack,to_shelf,to_bin_box, item_category, item_group, transfer_qnty, rate, transfer_value, uom, inserted_by, insert_date";
		
		$data_array_dtls="(".$id_dtls.",".$transfer_update_id.",".$cbo_item_desc.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",".$txt_item_id.",".$txt_transfer_qnty.",".$txt_rate.",'".$amount."',".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		//echo "insert into inv_item_transfer_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		/*$rID3=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} */
		
		$color_id=return_field_value("color","product_details_master","id=$cbo_item_desc");
		
		//$id_prop = return_next_id( "id", "order_wise_pro_details", 1 );
		$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, order_rate, order_amount, inserted_by, insert_date";
		$field_array_ord_prod="id, company_id, category_id, prod_id, po_breakdown_id, stock_quantity, last_rcv_qnty, avg_rate, stock_amount, inserted_by, insert_date";
		$field_array_ord_prod_update="last_issue_qnty*stock_quantity*stock_amount*updated_by*update_date";
		$field_array_ord_prod_update_to="avg_rate*last_rcv_qnty*stock_quantity*stock_amount*updated_by*update_date";
		$data_array_ord_prod="";
		
		$order_trans_qnty=(str_replace("'","",$txt_transfer_qnty)/$conversion_factor);
		if($order_trans_qnty=="") $order_trans_qnty=0;
		
		$row_prod_order=sql_select( "select id, stock_quantity, avg_rate, stock_amount from order_wise_stock where prod_id=$cbo_item_desc and po_breakdown_id=$txt_from_order_id and status_active=1 and is_deleted=0" );
		$item_order_id=$row_prod_order[0][csf("id")];
		$avg_rate=$row_prod_order[0][csf("avg_rate")];
		$order_amount=str_replace("'","",$order_trans_qnty)*$avg_rate;
		
		$prev_item_order_stock=$row_prod_order[0][csf("stock_quantity")];
		$prev_item_order_amount=$row_prod_order[0][csf("stock_amount")];
		$item_order_current_stock=$prev_item_order_stock-str_replace("'","",$order_trans_qnty);
		$item_order_current_amount=$prev_item_order_amount-$order_amount;
		
		$data_array_prop="(".$id_prop.",".$id_trans.",6,78,".$id_dtls.",".$txt_from_order_id.",".$cbo_item_desc.",'".$color_id."',".$order_trans_qnty.",'".$avg_rate."','".$order_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		$ord_prod_id_arr=array();
		if($item_order_id!="")
		{
			$ord_prod_id_arr[]=$item_order_id;
			$data_array_ord_prod_update[$item_order_id]=explode("*",("".$order_trans_qnty."*".$item_order_current_stock."*".$item_order_current_amount."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		}
		
		unset($row_prod_order);
		//$id_prop=$id_prop+1;
		$id_prop=return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
		$row_prod_order_to=sql_select( "select id, stock_quantity, avg_rate, stock_amount from order_wise_stock where prod_id=$cbo_item_desc and po_breakdown_id=$txt_to_order_id and status_active=1 and is_deleted=0" );
		$ord_prod_id_arr_to=array();
		if(count($row_prod_order_to)>0)
		{
			$item_order_id_to=$row_prod_order_to[0][csf("id")];
			$avg_rate_to=$row_prod_order_to[0][csf("avg_rate")];
			$order_amount_to=str_replace("'","",$order_trans_qnty)*$avg_rate_to;
			$curr_ord_stock_qnty=$row_prod_order_to[0][csf('stock_quantity')]+str_replace("'","",$order_trans_qnty);
			$curr_ord_stock_value=$row_prod_order_to[0][csf('stock_amount')]+$order_amount_to;
			$avg_ord_rate=number_format($curr_ord_stock_value/$curr_ord_stock_qnty,$dec_place[3],'.','');
			
			
			$data_array_prop.=",(".$id_prop.",".$id_trans_recv.",5,78,".$id_dtls.",".$txt_to_order_id.",".$cbo_item_desc.",'".$color_id."',".$order_trans_qnty.",'".$avg_rate_to."','".$order_amount_to."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$ord_prod_id_arr_to[]=$item_order_id_to;
			$data_array_ord_prod_update_to[$item_order_id_to]=explode("*",("".$avg_ord_rate."*".$order_trans_qnty."*".$curr_ord_stock_qnty."*".$curr_ord_stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		}
		else
		{
			$avg_rate_to=$avg_rate;
			$order_amount_to=str_replace("'","",$order_trans_qnty)*$avg_rate_to;
			$data_array_prop.=",(".$id_prop.",".$id_trans_recv.",5,78,".$id_dtls.",".$txt_to_order_id.",".$cbo_item_desc.",'".$color_id."',".$order_trans_qnty.",'".$avg_rate_to."','".$order_amount_to."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$ord_prod_id = return_next_id_by_sequence("ORDER_WISE_STOCK_PK_SEQ", "order_wise_stock", $con);
			if($data_array_ord_prod!="") $data_array_ord_prod.=",";
			$data_array_ord_prod.="(".$ord_prod_id.",".$cbo_company_id.",4,".$cbo_item_desc.",".$txt_to_order_id.",'".$order_trans_qnty."','".$order_trans_qnty."','".$avg_rate_to."','".$order_amount_to."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		}
		
		
		
		//unset($row_prod_order_to);
		
		
		
		$rID=$rID2=$rID3=$rID4=$ordProdUpdateTo=$ordProdUpdate=$ordProdInsert=true;
		
		if(str_replace("'","",$update_id)=="")
		{
			$rID=sql_insert("inv_item_transfer_mst",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;
		}
		else
		{
			$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0; 
		}
		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
		
		$rID3=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} 
		//echo "insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;
		$rID4=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1);
		if($flag==1) 
		{
			if($rID4) $flag=1; else $flag=0; 
		}
		
		if(count($ord_prod_id_arr_to)>0)
		{
			$ordProdUpdateTo=execute_query(bulk_update_sql_statement("order_wise_stock","id",$field_array_ord_prod_update_to,$data_array_ord_prod_update_to,$ord_prod_id_arr_to));
			if($flag==1) 
			{
				if($ordProdUpdateTo) $flag=1; else $flag=0; 
			}
		}
		
		if($data_array_ord_prod!="")
		{
			$ordProdInsert=sql_insert("order_wise_stock",$field_array_ord_prod,$data_array_ord_prod,0);
			if($flag==1) 
			{
				if($ordProdInsert) $flag=1; else $flag=0; 
			}
		}
		
		
		if(count($ord_prod_id_arr)>0)
		{
			$ordProdUpdate=execute_query(bulk_update_sql_statement("order_wise_stock","id",$field_array_ord_prod_update,$data_array_ord_prod_update,$ord_prod_id_arr));
			if($flag==1) 
			{
				if($ordProdUpdate) $flag=1; else $flag=0; 
			}
		}
		
		
		//echo "10**$rID=$rID2=$rID3=$rID4=$ordProdUpdateTo=$ordProdInsert=$ordProdUpdate";oci_rollback($con);die;
		
		 
		$txt_from_order_id=str_replace("'","",$txt_from_order_id);
		//echo $flag;die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$transfer_update_id."**".$transfer_recv_num."**".$txt_from_order_id."**".str_replace("'","",$cbo_store_name);
			}
			else
			{
				mysql_query("ROLLBACK"); 

				echo "5**0**"."&nbsp;"."**".$txt_from_order_id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);   
				echo "0**".$transfer_update_id."**".$transfer_recv_num."**".$txt_from_order_id."**".str_replace("'","",$cbo_store_name);
			}
			else
			{
				oci_rollback($con);
				echo "5**0**"."&nbsp;"."**".$txt_from_order_id;
			}
		}
		
		disconnect($con);
		die;
	}	
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$all_trans_id=$update_trans_issue_id.",".$update_trans_recv_id;
		
		$sqlCon="";	
		$store_update_upto=str_replace("'","",$store_update_upto);
		if($store_update_upto > 1)
		{
			if($store_update_upto==6)
			{
				if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
				if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and c.room=$cbo_room" ;}
				if(str_replace("'","",$txt_rack)!=0){$sqlCon.= " and c.rack=$txt_rack" ;}
				if(str_replace("'","",$txt_shelf)!=0){$sqlCon.= " and c.self=$txt_shelf" ;}
				if(str_replace("'","",$cbo_bin)!=0){$sqlCon.= " and c.bin_box=$cbo_bin" ;}
			}
			else if($store_update_upto==5)
			{
				if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
				if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and c.room=$cbo_room" ;}
				if(str_replace("'","",$txt_rack)!=0){$sqlCon.= " and c.rack=$txt_rack" ;}
				if(str_replace("'","",$txt_shelf)!=0){$sqlCon.= " and c.self=$txt_shelf" ;}
			}
			else if($store_update_upto==4)
			{
				if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
				if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and c.room=$cbo_room" ;}
				if(str_replace("'","",$txt_rack)!=0){$sqlCon.= " and c.rack=$txt_rack" ;}
			}
			else if($store_update_upto==3)
			{
				if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
				if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and c.room=$cbo_room" ;}
			}
			else if($store_update_upto==2)
			{
				if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
			}
		}

		$sql_trim = sql_select("select sum((case when b.entry_form in(24,73,78,112) and b.trans_type in(1,4,5)  then b.quantity else 0 end)-(case when b.entry_form in(25,49,78,112) and b.trans_type in(2,3,6)  then b.quantity else 0 end)) as balance 
		from order_wise_pro_details b, inv_transaction c
		where  b.trans_id=c.id and c.item_category=4 and c.company_id=$cbo_company_id and c.store_id =$cbo_store_name $sqlCon and b.po_breakdown_id =$txt_from_order_id and b.prod_id=$cbo_item_desc  and c.status_active=1 and c.id not in($all_trans_id) and  b.trans_id not in($all_trans_id) and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		$trim_stock=$sql_trim[0][csf("balance")];
		if($conversion_factor>0) $trim_stock=$trim_stock*$conversion_factor; 
		
		
		if(str_replace("'","",$txt_transfer_qnty)>$trim_stock)
		{
			echo "11**Transfer Quantity Not Allow Over Stock.";
			disconnect($con);
			die;
		}
		
		

		$field_array_update="challan_no*transfer_date*from_order_id*to_order_id*updated_by*update_date";
		$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$txt_from_order_id."*".$txt_to_order_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		/*$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0;*/
		
		$field_array_trans="prod_id*transaction_date*order_id*cons_uom*cons_quantity*cons_rate*cons_amount*store_id*floor_id*room*rack*self*bin_box*updated_by*update_date";
		$updateTransID_array=array();
		$update_trans_issue_id=str_replace("'","",$update_trans_issue_id);
		$update_trans_recv_id=str_replace("'","",$update_trans_recv_id); 
		 
		$amount=str_replace("'","",$txt_transfer_qnty)*str_replace("'","",$txt_rate);
		
		$updateTransID_array[]=$update_trans_issue_id; 
		$updateTransID_data[$update_trans_issue_id]=explode("*",("".$cbo_item_desc."*".$txt_transfer_date."*".$txt_from_order_id."*".$cbo_uom."*".$txt_transfer_qnty."*".$txt_rate."*'".$amount."'*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		
		$updateTransID_array[]=$update_trans_recv_id; 
		$updateTransID_data[$update_trans_recv_id]=explode("*",("".$cbo_item_desc."*".$txt_transfer_date."*".$txt_to_order_id."*".$cbo_uom."*".$txt_transfer_qnty."*".$txt_rate."*'".$amount."'*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$cbo_bin_to."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		
		//print_r($updateTransID_data);die;
		$field_array_dtls="from_prod_id*item_group*transfer_qnty*rate*transfer_value*uom*updated_by*update_date";
		
		$data_array_dtls=$cbo_item_desc."*".$txt_item_id."*".$txt_transfer_qnty."*".$txt_rate."*'".$amount."'*".$cbo_uom."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		//print_r($data_array_dtls);
		$color_id=return_field_value("color","product_details_master","id=$cbo_item_desc");
		//$id_prop = return_next_id( "id", "order_wise_pro_details", 1 );
		$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, order_rate, order_amount, inserted_by, insert_date";
		$field_array_ord_prod_update="last_issue_qnty*stock_quantity*stock_amount*updated_by*update_date";
		$field_array_ord_prod_update_to="avg_rate*last_rcv_qnty*stock_quantity*stock_amount*updated_by*update_date";
		
		$order_trans_qnty=(str_replace("'","",$txt_transfer_qnty)/$conversion_factor);
		if($order_trans_qnty=="") $order_trans_qnty=0;
		$sql_prop_prev=sql_select( "select id, quantity, order_amount from order_wise_pro_details where prod_id=$cbo_item_desc and po_breakdown_id=$txt_from_order_id and trans_id=$update_trans_issue_id and dtls_id=$update_dtls_id and status_active=1 and is_deleted=0" );
		$row_prod_order=sql_select( "select id, stock_quantity, avg_rate, stock_amount from order_wise_stock where prod_id=$cbo_item_desc and po_breakdown_id=$txt_from_order_id and status_active=1 and is_deleted=0" );
		
		$item_order_id=$row_prod_order[0][csf("id")];
		$avg_rate=$row_prod_order[0][csf("avg_rate")];
		$order_amount=str_replace("'","",$order_trans_qnty)*$avg_rate;
		
		$prev_item_order_stock=$row_prod_order[0][csf("stock_quantity")]+$sql_prop_prev[0][csf("quantity")];
		$prev_item_order_amount=$row_prod_order[0][csf("stock_amount")]+$sql_prop_prev[0][csf("order_amount")];
		$item_order_current_stock=$prev_item_order_stock-str_replace("'","",$order_trans_qnty);
		$item_order_current_amount=$prev_item_order_amount-$order_amount;
		
		$data_array_prop="(".$id_prop.",".$update_trans_issue_id.",6,78,".$update_dtls_id.",".$txt_from_order_id.",".$cbo_item_desc.",'".$color_id."',".$order_trans_qnty.",'".$avg_rate."','".$order_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		$ord_prod_id_arr=array();
		if($item_order_id!="")
		{
			$ord_prod_id_arr[]=$item_order_id;
			$data_array_ord_prod_update[$item_order_id]=explode("*",("".$order_trans_qnty."*".$item_order_current_stock."*".$item_order_current_amount."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		}
		
		
		//$id_prop=$id_prop+1;
		$id_prop=return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
		$sql_prop_prev_to=sql_select( "select id, quantity, order_amount from order_wise_pro_details where prod_id=$cbo_item_desc and po_breakdown_id=$txt_to_order_id and trans_id=$update_trans_recv_id and dtls_id=$update_dtls_id and status_active=1 and is_deleted=0" );
		$row_prod_order_to=sql_select( "select id, stock_quantity, avg_rate, stock_amount from order_wise_stock where prod_id=$cbo_item_desc and po_breakdown_id=$txt_to_order_id and status_active=1 and is_deleted=0" );
		
		$item_order_id_to=$row_prod_order_to[0][csf("id")];
		$avg_rate_to=$row_prod_order_to[0][csf("avg_rate")];
		$order_amount_to=str_replace("'","",$order_trans_qnty)*$avg_rate_to;
		
		$curr_ord_stock_qnty=$row_prod_order_to[0][csf('stock_quantity')]+str_replace("'","",$order_trans_qnty)-$sql_prop_prev_to[0][csf("quantity")];
		$curr_ord_stock_value=$row_prod_order_to[0][csf('stock_amount')]+$order_amount_to-$sql_prop_prev_to[0][csf("order_amount")];
		$avg_ord_rate=number_format($curr_ord_stock_value/$curr_ord_stock_qnty,$dec_place[3],'.','');
		
		$data_array_prop.=",(".$id_prop.",".$update_trans_recv_id.",5,78,".$update_dtls_id.",".$txt_to_order_id.",".$cbo_item_desc.",'".$color_id."',".$order_trans_qnty.",'".$avg_rate_to."','".$order_amount_to."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		$ord_prod_id_arr_to=array();
		if($item_order_id_to!="")
		{
			$ord_prod_id_arr_to[]=$item_order_id_to;
			$data_array_ord_prod_update_to[$item_order_id_to]=explode("*",("".$avg_ord_rate."*".$order_trans_qnty."*".$curr_ord_stock_qnty."*".$curr_ord_stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		}
		
		
		$rID=$rID2=$rID3=$rID4=$ordProdUpdateTo=$ordProdUpdate=true;
		$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0;
		
		$rID2=execute_query(bulk_update_sql_statement("inv_transaction","id",$field_array_trans,$updateTransID_data,$updateTransID_array));
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		}
		
		$rID3=sql_update("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,"id",$update_dtls_id,1);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		}
		$query = execute_query("DELETE FROM order_wise_pro_details WHERE dtls_id=$update_dtls_id and entry_form=78");
		{
			if($query) $flag=1; else $flag=0; 
		} 
		//echo "insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;
		$rID4=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1);
		if($flag==1) 
		{
			if($rID4) $flag=1; else $flag=0; 
		}
		
		if(count($ord_prod_id_arr_to)>0)
		{
			$ordProdUpdateTo=execute_query(bulk_update_sql_statement("order_wise_stock","id",$field_array_ord_prod_update_to,$data_array_ord_prod_update_to,$ord_prod_id_arr_to));
			if($flag==1) 
			{
				if($ordProdUpdateTo) $flag=1; else $flag=0; 
			}
		}
		
		
		if(count($ord_prod_id_arr)>0)
		{
			$ordProdUpdate=execute_query(bulk_update_sql_statement("order_wise_stock","id",$field_array_ord_prod_update,$data_array_ord_prod_update,$ord_prod_id_arr));
			if($flag==1) 
			{
				if($ordProdUpdate) $flag=1; else $flag=0; 
			}
		}
		
		
		//echo "10**$rID=$rID2=$rID3=$rID4=$ordProdUpdateTo=$ordProdUpdate";oci_rollback($con);die;
		 
		$txt_from_order_id=str_replace("'","",$txt_from_order_id);
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id)."**".$txt_from_order_id."**".str_replace("'","",$cbo_store_name);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**0**"."&nbsp;"."**1"."**".$txt_from_order_id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);   
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id)."**".$txt_from_order_id."**".str_replace("'","",$cbo_store_name);
			}
			else
			{
				oci_rollback($con);
				echo "6**0**"."&nbsp;"."**1";
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
		
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		$update_id=str_replace("'","",$update_id);
		$previous_prod_id=str_replace("'","",$cbo_item_desc);
		$update_dtls_id=str_replace("'","",$update_dtls_id);
		$update_trans_issue_id=str_replace("'","",$update_trans_issue_id);
		$update_trans_recv_id=str_replace("'","",$update_trans_recv_id);
		$all_trans_id=$update_trans_issue_id.",".$update_trans_recv_id;
		$txt_to_order_id=str_replace("'","",$txt_to_order_id);
		
		//echo "10**$update_id=$previous_prod_id=$update_dtls_id=$update_trans_issue_id=$update_trans_recv_id";die;
		if($update_id>0 && $previous_prod_id>0 && $update_dtls_id>0 && $update_trans_issue_id>0 && $update_trans_recv_id>0)
		{
			
			/*if($db_type==0) $row_count_cond=" limit 1"; else $row_count_cond=" and rownum<2";
			$next_operation_check=sql_select("select id as next_id, mst_id as mst_id, transaction_type as transaction_type from inv_transaction where id > $update_trans_recv_id and prod_id=$previous_prod_id and status_active=1 $row_count_cond");
			if(count($next_operation_check)>0)
			{
				$next_id=$next_operation_check[0][csf("next_id")];
				$next_mst_id=$next_operation_check[0][csf("mst_id")];
				$next_transaction_type=$next_operation_check[0][csf("transaction_type")];

				if($next_transaction_type==1 || $next_transaction_type==4)
				{
					$next_mrr=return_field_value("recv_number as next_mrr_number","inv_receive_master","id=$next_mst_id","next_mrr_number");
				}
				else if($next_transaction_type==2 || $next_transaction_type==3)
				{
					$next_mrr=return_field_value("issue_number as next_mrr_number","inv_issue_master","id=$next_mst_id","next_mrr_number");
				}
				else
				{
					$next_mrr=return_field_value("transfer_system_id as next_mrr_number","inv_item_transfer_mst","id=$next_mst_id","next_mrr_number");
				}
				echo "20**Next Operation No:- $next_mrr  Found, Delete Not Allow.";
				disconnect($con);die;
				//check_table_status( $_SESSION['menu_id'],0);
			}*/
			
			$store_stock=sql_select("select sum((case when b.trans_type in(1,4,5) then b.quantity else 0 end)-(case when b.trans_type in(2,3,6) then b.quantity else 0 end)) as store_stock_qnty 
			from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.prod_id=$previous_prod_id and a.store_id=$cbo_store_name_to and b.po_breakdown_id=$txt_to_order_id and b.entry_form in(24,25,49,73,78,112) and a.status_active=1 and b.status_active=1");
			$store_stock_qnty=$store_stock[0][csf("store_stock_qnty")];
			
			if($store_stock_qnty <= 0)
			{
				echo "20**Order Store Wise Stock Less Then Zero, \n Please Delete Next Issue Or Receive Return, \n More Information Please See Order Wise Trims Receive Issue And Stock.";
				disconnect($con);die;
			}
			
			
			$row_propotionate=sql_select( "select id, po_breakdown_id, quantity, order_rate, order_amount, trans_type
			from order_wise_pro_details where trans_id in($all_trans_id) and status_active=1 and is_deleted=0" );
			$propotionate_data=array();
			foreach($row_propotionate as $row)
			{
				$all_order_id.=$row[csf("po_breakdown_id")].",";
				if($row[csf("trans_type")]==6)
				{
					$propotionate_data[$row[csf("po_breakdown_id")]]["quantity_issue"]+=$row[csf("quantity")];
					$propotionate_data[$row[csf("po_breakdown_id")]]["order_amount_issue"]+=$row[csf("order_amount")];
				}
				else
				{
					$propotionate_data[$row[csf("po_breakdown_id")]]["quantity_rcv"]+=$row[csf("quantity")];
					$propotionate_data[$row[csf("po_breakdown_id")]]["order_amount_rcv"]+=$row[csf("order_amount")];
				}
				
			}
			$all_order_id=chop($all_order_id,",");
			$field_array_prod_ord_update="avg_rate*stock_quantity*stock_amount*updated_by*update_date";
			if($all_order_id!="")
			{
				$prod_order_stock=sql_select("select id, po_breakdown_id, stock_quantity, stock_amount 
				from order_wise_stock where prod_id=$previous_prod_id and po_breakdown_id in($all_order_id) and status_active=1 and is_deleted=0 ");
				foreach($prod_order_stock as $row)
				{
					
					if($propotionate_data[$row[csf("po_breakdown_id")]]["quantity_issue"]>0)
					{
						$current_stock_qnty=$row[csf('stock_quantity')]+$propotionate_data[$row[csf("po_breakdown_id")]]["quantity_issue"];
						$current_stock_value=$row[csf('stock_amount')]+$propotionate_data[$row[csf("po_breakdown_id")]]["order_amount_issue"];
					}
					if($propotionate_data[$row[csf("po_breakdown_id")]]["quantity_rcv"]>0)
					{
						$current_stock_qnty=$row[csf('stock_quantity')]-$propotionate_data[$row[csf("po_breakdown_id")]]["quantity_rcv"];
						$current_stock_value=$row[csf('stock_amount')]-$propotionate_data[$row[csf("po_breakdown_id")]]["order_amount_rcv"];
					}
					
					if($current_stock_value>0 && $current_stock_qnty>0)
					{
						$current_avg_rate=number_format($current_stock_value/$current_stock_qnty,$dec_place[3],'.','');
					}
					else
					{
						$current_avg_rate=0;
					}
					
					
					$ord_prod_id_arr[]=$row[csf('id')];
					$data_array_prod_ord_update[$row[csf('id')]]=explode("*",("".$current_avg_rate."*".$current_stock_qnty."*".$current_stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				}
			}
			
			
			$field_arr="status_active*is_deleted*updated_by*update_date";
			$data_arr="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID=$rID2=$rID3=$ordProdUpdate=true;
			
			if(count($ord_prod_id_arr)>0)
			{
				$ordProdUpdate=execute_query(bulk_update_sql_statement("order_wise_stock","id",$field_array_prod_ord_update,$data_array_prod_ord_update,$ord_prod_id_arr));
			}
			$rID=execute_query("update inv_transaction set status_active=0, is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where id in($all_trans_id)");
			$rID2=sql_update("inv_item_transfer_dtls",$field_arr,$data_arr,"id",$update_dtls_id,1);
			$rID3=execute_query("update order_wise_pro_details set status_active=0, is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where trans_id in($all_trans_id)");
			
			
			//echo "10** $rID && $rID2 && $rID3 && $ordProdUpdate";oci_rollback($con);disconnect($con);die;
			if($db_type==0)
			{
				if($rID && $rID2 && $rID3 && $ordProdUpdate)
				{
					mysql_query("COMMIT");  
					echo "2**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id)."**0";
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "7**0**0**1";
				}
			}
	
			if($db_type==2 || $db_type==1 )
			{
				if($rID && $rID2 && $rID3 && $ordProdUpdate)
				{
					oci_commit($con);  
					echo "2**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id)."**0";
				}
				else
				{
					oci_rollback($con);
					echo "7**0**0**1";
				}
			}
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);
			die;
		}
	}
}


if ($action=="trims_store_order_to_order_transfer_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	$sql="select id, transfer_system_id, transfer_date, challan_no, from_order_id, to_order_id, item_category from inv_item_transfer_mst a where id='$data[1]' and company_id='$data[0]'";
	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	
	$po_array=array();
	$sql_po=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, b.po_number, b.po_quantity, b.pub_shipment_date, b.id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$data[0]'");
	foreach($sql_po as $row_po)
	{
		$po_array[$row_po[csf('id')]]['no']=$row_po[csf('po_number')];
		$po_array[$row_po[csf('id')]]['job']=$row_po[csf('job_no')];
		$po_array[$row_po[csf('id')]]['buyer']=$row_po[csf('buyer_name')];
		$po_array[$row_po[csf('id')]]['qnty']=$row_po[csf('po_quantity')];
		$po_array[$row_po[csf('id')]]['date']=$row_po[csf('pub_shipment_date')];
		$po_array[$row_po[csf('id')]]['style']=$row_po[csf('style_ref_no')];
	}
	
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=4 and status_active=1 and is_deleted=0","id","product_name_details");
?>
<div style="width:930px;">
    <table width="900" cellspacing="0" align="right">
        <tr>
            <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:14px">  
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
					foreach ($nameArray as $result)
					{ 
					?>
						Plot No: <? echo $result['plot_no']; ?> 
						Level No: <? echo $result['level_no']?>
						Road No: <? echo $result['road_no']; ?> 
						Block No: <? echo $result['block_no'];?> 
						City No: <? echo $result['city'];?> 
						Zip Code: <? echo $result['zip_code']; ?> 
						Province No: <?php echo $result['province'];?> 
						Country: <? echo $country_arr[$result['country_id']]; ?><br> 
						Email Address: <? echo $result['email'];?> 
						Website No: <? echo $result['website'];
					}
                ?> 
            </td>  
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo $data[2]; ?> Report</u></strong></td>
        </tr>
        <tr>
        	<td width="125"><strong>Transfer ID :</strong></td><td width="175px"><? echo $dataArray[0][csf('transfer_system_id')]; ?></td>
            <td width="125"><strong>Transfer Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
            <td width="125"><strong>Challan No.:</strong></td><td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
        </tr>
        <tr>
            <td><strong>From order No:</strong></td> <td width="175px"><? echo $po_array[$dataArray[0][csf('from_order_id')]]['no']; ?></td>
            <td><strong>From ord Qnty:</strong></td> <td width="175px"><? echo $po_array[$dataArray[0][csf('from_order_id')]]['qnty']; ?></td>
            <td><strong>From ord Buyer:</strong></td> <td width="175px"><? echo $buyer_library[$po_array[$dataArray[0][csf('from_order_id')]]['buyer']]; ?></td>
        </tr>
        <tr>
            <td><strong>From Style Ref.:</strong></td> <td width="175px"><? echo $po_array[$dataArray[0][csf('from_order_id')]]['style']; ?></td>
            <td><strong>From Job No:</strong></td> <td width="175px"><? echo $po_array[$dataArray[0][csf('from_order_id')]]['job']; ?></td>
            <td><strong>From Ship. Date:</strong></td> <td width="175px"><? echo change_date_format($po_array[$dataArray[0][csf('from_order_id')]]['date']); ?></td>
        </tr>
        <tr>
            <td><strong>To order No:</strong></td> <td width="175px"><? echo $po_array[$dataArray[0][csf('to_order_id')]]['no']; ?></td>
            <td><strong>To ord Qnty:</strong></td> <td width="175px"><? echo $po_array[$dataArray[0][csf('to_order_id')]]['qnty']; ?></td>
            <td><strong>To ord Buyer:</strong></td> <td width="175px"><? echo $buyer_library[$po_array[$dataArray[0][csf('to_order_id')]]['buyer']]; ?></td>
        </tr>
        <tr>
            <td><strong>To Style Ref.:</strong></td> <td width="175px"><? echo $po_array[$dataArray[0][csf('to_order_id')]]['style']; ?></td>
            <td><strong>To Job No:</strong></td> <td width="175px"><? echo $po_array[$dataArray[0][csf('to_order_id')]]['job']; ?></td>
            <td><strong>To Ship. Date:</strong></td> <td width="175px"><? echo change_date_format($po_array[$dataArray[0][csf('to_order_id')]]['date']); ?></td>
        </tr>
    </table>
        <br>
    <div style="width:100%;">
    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="120" >Item Category</th>
            <th width="250" >Item Description</th>
            <th width="70" >UOM</th>
            <th width="100" >Transfered Qnty</th>
        </thead>
        <tbody> 
   
<?
	$sql_dtls="select id, item_category, item_group, from_prod_id, transfer_qnty, uom from inv_item_transfer_dtls where mst_id='$data[1]' and status_active=1 and is_deleted=0";
	$sql_result= sql_select($sql_dtls);
	$i=1;
	foreach($sql_result as $row)
	{
		if ($i%2==0)  
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";
			
			$transfer_qnty=$row[csf('transfer_qnty')];
			$transfer_qnty_sum += $transfer_qnty;
			
		?>
			<tr bgcolor="<? echo $bgcolor; ?>">
                <td align="center"><? echo $i; ?></td>
                <td><? echo $item_category[$row[csf("item_category")]]; ?></td>
                <td><? echo $product_arr[$row[csf("from_prod_id")]]; ?></td>
                <td align="center"><? echo $unit_of_measurement[$row[csf("uom")]]; ?></td>
                <td align="right"><? echo number_format($row[csf("transfer_qnty")],2); ?></td>
			</tr>
			<? $i++; } ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" align="right"><strong>Total :</strong></td>
                <td align="right"><?php echo number_format($transfer_qnty_sum,2); ?></td>
            </tr>                           
        </tfoot>
      </table>
        <br>
		 <?
            //echo signature_table(24, $data[0], "900px");
         ?>
      </div>
   </div>   
 <?
 exit();	
}
?>

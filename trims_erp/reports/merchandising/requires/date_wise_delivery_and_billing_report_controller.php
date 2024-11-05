<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];




if($action=="print_button_variable_setting")
{

    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=17 and report_id=293 and is_deleted=0 and status_active=1");
	$printButton=explode(',',$print_report_format);

	 foreach($printButton as $id){
		if($id==147)$buttonHtml.='<input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:80px" class="formbutton" />';
		if($id==195)$buttonHtml.='<input type="button" name="search" id="search" value="Show 2" onClick="generate_report(2)" style="width:80px" class="formbutton" />';
		if($id==242)$buttonHtml.='<input type="button" name="search" id="search3" value="Show 3" onClick="generate_report(3)" style="width:80px" class="formbutton" />';
 	 }
	 echo "document.getElementById('button_data_panel').innerHTML = '".$buttonHtml."';\n";

    exit();
}


if ($action=="load_drop_down_location") {	
	echo create_drop_down( 'cbo_location_name', 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_buyer")
{ 
	list($company,$type)=explode("_",$data);
	if($type==1)
	{
		echo create_drop_down( "cbo_customer_name", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $company, "$load_function");
	}
	else
	{
		echo create_drop_down( "cbo_customer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", "", "" );
	}	
	exit();	 
} 


if ($action=="order_popup")
{	
	echo load_html_head_contents("Order Search","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
	function js_set_value(id)
	{
		//alert(booking_no); 
		document.getElementById('hidd_booking_data').value=id;
		parent.emailwindow.hide();
	}
	
</script>
</head>
<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                <thead>
                    <tr>                	 
                        <th width="120">Delv Chlln No</th>
                        <th colspan="2" width="160">Delv Chlln Date</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                            <input type="hidden" id="hidd_booking_data">
                        </th>
                    </tr>                                 
                </thead>
                <tr class="general">
                    <td><input name="txt_order_number" id="txt_order_number" class="text_boxes" style="width:90px"></td>
                    <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From"></td>
                    <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To"></td> 
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_order_number').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company;?>+'_'+<? echo $party_name;?>+'_'+<? echo $customer_source;?>, 'create_booking_search_list_view', 'search_div', 'date_wise_delivery_and_billing_report_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                    </td>
                </tr>
            </table>
            <div id="search_div"></div>   
        </form>
    </div>
	</body>           
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}
if($action=="create_booking_search_list_view")
{	
	$data=explode('_',$data);
	//print_r($data);
	
	if ($data[3]!=0) $company=" and a.company_id='$data[3]'";
	if ($data[4]!=0) $party=" and a.party_id='$data[4]'";
	if($db_type==0)
	{
		if ($data[1]!="" &&  $data[2]!="") $delivery_date = "and a.delivery_date between '".change_date_format($data[1], "yyyy-mm-dd", "-")."' and '".change_date_format($data[2], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	else if($db_type==2)
	{
		if ($data[1]!="" &&  $data[2]!="")  $delivery_date= "and a.delivery_date between '".change_date_format($data[1], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	} 
	
	if ($data[0]!="") $woorder_cond=" and a.trims_del like '%$data[0]%' "; 
	
	$sql= "select a.id, a.trims_del, a.del_no_prefix, a.del_no_prefix_num, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.currency_id,  a.delivery_date, a.received_id, a.order_id, a.challan_no, a.gate_pass_no from trims_delivery_mst a, trims_delivery_dtls b where a.entry_form=208 and a.id=b.mst_id and a.status_active=1 and b.status_active=1 $delivery_date $woorder_cond  $company $party_id_cond $withinGroup $search_com $withinGroup group by a.id, a.trims_del, a.del_no_prefix, a.del_no_prefix_num, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.currency_id, a.delivery_date, a.received_id, a.order_id, a.challan_no, a.gate_pass_no order by a.id DESC";
	
	

	$data_array=sql_select($sql);
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="440" >
        <thead>
            <th width="30">SL</th>
            <th width="60">Delv Chlln No</th>
            <th width="70">Delv Chlln Date</th>
        </thead>
        </table>
        <div style="width:440px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="420" class="rpt_table" id="list_view">
        <tbody>
            <? 
            $i=1;
            foreach($data_array as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('trims_del')].'_'.$row[csf('currency_id')].'_'.$row[csf('within_group')]; ?>")' style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="60" align="center"><? echo $row[csf('trims_del')]; ?></td>
                    <td width="70"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
                </tr>
				<? 
                $i++; 
            } 
            ?>
        </tbody>
    </table>
	<?
	exit();
}

if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	////////////////////////////////////////////////
	$cbo_company_id=str_replace("'","", $cbo_company_id);
	$cbo_location_name=str_replace("'","", $cbo_location_name);
	$cbo_customer_source=str_replace("'","", $cbo_customer_source);
	$cbo_customer_name=str_replace("'","", $cbo_customer_name);
	$txt_order_no=str_replace("'","", $txt_order_no);
	$hid_order_id=str_replace("'","", $hid_order_id);
	$cbo_bill_status=str_replace("'","", $cbo_bill_status);
	$txt_date_from=str_replace("'","", $txt_date_from);
	$txt_date_to=str_replace("'","", $txt_date_to);
	
	
	if($cbo_company_id){$where_con.=" and a.company_id='$cbo_company_id'";} 
	
	if($cbo_customer_source){
		$where_con.=" and a.within_group='$cbo_customer_source'";
		$where_con_ord.=" and a.within_group='$cbo_customer_source'";
	} 
	if($cbo_customer_name){
		$where_con.=" and a.party_id='$cbo_customer_name'";
		$where_con_ord.=" and a.party_id='$cbo_customer_name'";
	} 
	if($cbo_location_name){$where_con.=" and a.location_id='$cbo_location_name'";} 
	
	//////////////////////////////////////////////////////////////////
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 ","id","buyer_name");
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 ","id","supplier_name");
	$trimsGroupArr = return_library_array("select id, item_name from lib_item_group where item_category=4 and status_active=1","id","item_name");
	$colorNameArr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$item_name_arr = return_library_array("select id, item_name from lib_item_group where item_category=4 and status_active=1 and is_deleted=0","id","item_name");
	//////////////////////////////////////////////////////////////////
	
		if($txt_date_from!="" and $txt_date_to!="")
		{	
			$where_con.=" and a.delivery_date between '$txt_date_from' and '$txt_date_to'";
		}
		
		
	if(trim($txt_order_no)!="")
	{
			$sql_cond="and a.trims_del like '%$txt_order_no%'";
	}

	if(trim($hid_order_id)!="")
	{
			$sql_cond_ord="and c.mst_id= '$hid_order_id'";
	}
			
	
	$currency_rate=return_field_value( "conversion_rate", "currency_conversion_rate", "currency=2 and status_active=1 and con_date=(select max(con_date) as con_date from currency_conversion_rate)" , "conversion_rate" );


	if($db_type==0) 
	{
		$ins_year_cond="year(e.insert_date)";
	}
	else if($db_type==2)
	{
		$ins_year_cond="TO_CHAR(e.insert_date,'YYYY')";
	}
	
	
	//echo $currency_rate; die;

	//main query

	 $trims_deli_sql= "SELECT a.id,a.trims_del,a.delivery_date, a.within_group, a.party_id, b.received_id, b.receive_dtls_id, b.order_no,b.delevery_qty, b.item_group, b.order_uom, c.buyer_buyer, c.buyer_po_no,c.job_no_mst, d.style, $ins_year_cond as year, e.currency_id, c.rate, e.delivery_point, b.section,e.subcon_job, e.buyer_tb,b.order_receive_rate
	From trims_delivery_mst a, trims_delivery_dtls b,subcon_ord_dtls c, subcon_ord_breakdown d, subcon_ord_mst e
	where e.id=c.mst_id and c.id=d.mst_id and d.id=b.break_down_details_id and a.id=b.mst_id and c.id=b.receive_dtls_id and b.delevery_qty>0 and a.entry_form=208 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sql_cond $where_con  $company  order by a.id DESC"; //e.exchange_rate,c.booked_conv_fac,

	//echo $trims_deli_sql; //die;
	  
		$result = sql_select($trims_deli_sql);
        $date_array=array();
		$deli_id_arr=array();
        foreach($result as $row)
        {
			$deli_id_arr[$row[csf("id")]]=$row[csf("id")];
			
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['id']=$row[csf('id')];
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['trims_del']=$row[csf('trims_del')];
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['subcon_job']=$row[csf('subcon_job')];
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['buyer_tb']=$row[csf('buyer_tb')];
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['delivery_date']=$row[csf('delivery_date')];
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['party_id']=$row[csf('party_id')];
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['received_id']=$row[csf('received_id')];
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['receive_dtls_id']=$row[csf('receive_dtls_id')];
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['order_no']=$row[csf('order_no')];
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['delevery_qty']+=$row[csf('delevery_qty')];
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['item_group']=$row[csf('item_group')];
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['order_uom']=$row[csf('order_uom')];
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['buyer_buyer']=$row[csf('buyer_buyer')];
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['job_no_mst']=$row[csf('job_no_mst')];
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['style'].=$row[csf('style')].',';
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['within_group']=$row[csf('within_group')];
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['year']=$row[csf('year')];

       	 	//$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['exchange_rate']=$row[csf('exchange_rate')];
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['currency_id']=$row[csf('currency_id')];
       	 	//$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['booked_conv_fac']=$row[csf('booked_conv_fac')];
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['rate']=$row[csf('rate')];
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['delivery_point']=$row[csf('delivery_point')];
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['section']=$row[csf('section')];
			
			$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['buyer_po_no'].=$row[csf('buyer_po_no')].',';
			$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['order_receive_rate']=$row[csf('order_receive_rate')];
			
			
			$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['order_amount']+=$row[csf('delevery_qty')]*$row[csf('order_receive_rate')];
       	 	
 		}

 		$deli_id_arr = array_unique($deli_id_arr);
 
 
 	/*echo "<pre>";
	print_r($date_array);
 	echo "</pre>"; die;*/

	$delivery_con=where_con_using_array($deli_id_arr,0,"c.mst_id");

	$trims_bill_sql = "select a.id, a.trims_bill, a.within_group, a.currency_id, a.exchange_rate, a.bill_date, b.quantity, b.bill_rate, b.bill_amount, c.mst_id, c.item_group, c.order_uom, d.rate as wo_rate,a.discount,a.net_bill_amount
	from trims_bill_mst a, trims_bill_dtls b, trims_delivery_dtls c, subcon_ord_dtls d where  a.entry_form=276 and a.id=b.mst_id and c.id=b.production_dtls_id and d.id=c.receive_dtls_id $delivery_con and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.id ASC"; //e.id=c.mst_id and //trims_delivery_mst e //e.delivery_date

	//echo $trims_bill_sql; die;

	$result_bill_sql=sql_select($trims_bill_sql);
	$trims_bill_data_arr=array();
	foreach($result_bill_sql as $row)
	{
		$disc=TRIM($row[csf('description')]);
		$trims_bill_data_arr[$row[csf('mst_id')]][$row[csf('item_group')]][$row[csf('order_uom')]]['trims_bill'].=$row[csf('trims_bill')].',';
		$trims_bill_data_arr[$row[csf('mst_id')]][$row[csf('item_group')]][$row[csf('order_uom')]]['within_group']=$row[csf('within_group')];
		$trims_bill_data_arr[$row[csf('mst_id')]][$row[csf('item_group')]][$row[csf('order_uom')]]['currency_id']=$row[csf('currency_id')];
		$trims_bill_data_arr[$row[csf('mst_id')]][$row[csf('item_group')]][$row[csf('order_uom')]]['exchange_rate']=$row[csf('exchange_rate')];
		$trims_bill_data_arr[$row[csf('mst_id')]][$row[csf('item_group')]][$row[csf('order_uom')]]['bill_date']=$row[csf('bill_date')];
		$trims_bill_data_arr[$row[csf('mst_id')]][$row[csf('item_group')]][$row[csf('order_uom')]]['quantity']+=$row[csf('quantity')];
		$trims_bill_data_arr[$row[csf('mst_id')]][$row[csf('item_group')]][$row[csf('order_uom')]]['bill_amount']+=$row[csf('bill_amount')];
		 
		$trims_bill_data_arr[$row[csf('mst_id')]][$row[csf('item_group')]][$row[csf('order_uom')]]['discount']+=$row[csf('discount')];
		$trims_bill_data_arr[$row[csf('mst_id')]][$row[csf('item_group')]][$row[csf('order_uom')]]['net_bill_amount']+=$row[csf('net_bill_amount')];
		//$trims_bill_data_arr[$row[csf('mst_id')]][$row[csf('item_group')]][$row[csf('order_uom')]]['bill_rate']=$row[csf('bill_rate')];
		$trims_bill_data_arr[$row[csf('mst_id')]][$row[csf('item_group')]][$row[csf('order_uom')]]['wo_rate']=$row[csf('wo_rate')];
		$trims_bill_data_arr[$row[csf('mst_id')]][$row[csf('item_group')]][$row[csf('order_uom')]]['bill_rate']=$row[csf('bill_amount')]/$row[csf('quantity')];

		//[$row[csf('bill_date')]]
	}
 
	/*echo "<pre>";
	print_r($trims_bill_data_arr);
	echo "</pre>"; die;*/
 
 
	
	$width=2885;
	ob_start();
	?>	
	<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:0;">
    	<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="caption" align="center">
				<thead class="form_caption" >
					<tr>
						<td colspan="22" align="center" style="font-size:20px;"><? echo $companyArr[$cbo_company_id]; ?></td>
					</tr>
					<tr>
						<td colspan="22" align="center" style="font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
					</tr>
					<tr>
						<td colspan="22" align="center" style="font-size:14px; font-weight:bold">
							<? echo " From : ".change_date_format($txt_date_from) ." To : ". change_date_format($txt_date_to) ;?>
						</td>
					</tr>
				</thead>
			</table>
            <table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
				<thead>
                    <th width="35">SL</th>
                    <th width="100">Challan No</th>
                    <th width="100">Challan Date</th>
                    <th width="100"> Job No</th>
                    <th width="100">Work Order No</th>
                    <th width="60">WO Year</th>
                    <th width="80">Buyers TB</th>
                    <th width="120">Party Name</th>
                    <th width="150">Delivery Point</th>
                    <th width="100">Buyer PO</th>
                    <th width="160">Style</th>
                    <th width="100">Buyer</th>
                    <th width="100">Section</th>
                    <th width="100">Item Name</th>
                    <th width="60">WO UOM</th>
                    <th width="100">Delivery Qty</th>
                    <th width="100">Total Delv.Value($)</th>
                    <th width="100">Bill Number</th>
                    <th width="100">Bill Date</th>
                    <th width="100">Bill Qty</th>
                    <th width="100">Bill Rate[$]</th>
                    <th width="100">Bill Rate[TK]</th>
                    <th width="100">Bill Amount[TK]</th>
                    <th width="100">Bill Amount[$]</th>
                    <th width="100">Discount Amount</th>
                    <th width="100">Net Bill Value</th>
                    <th width="100">Partial Qty</th>
                    <th width="100">Partial Value[$]</th>
                    <th>Billng Status</th>
				</thead>
			</table>
        <div style="width:<? echo $width+18;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
        	<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="<? echo $width;?>" rules="all" align="left">
            <? 
			$i=1;
			$total_bill_amount_usd=0;
			$total_bill_amount_tk=0;
			$total_deli_amount_usd=0;
			$total_partial_value_usd=0;
			foreach($date_array as $mst_id=>$mst_id_data)
			{
				foreach($mst_id_data as $delivery_date=>$delivery_date_data)
				{
					foreach($delivery_date_data as $item_group=>$item_group_data)
					{
						foreach($item_group_data as $order_uom=>$row)
						{
							$delevery_qty=$row['delevery_qty'];
							$bill_qty=$trims_bill_data_arr[$mst_id][$item_group][$order_uom]['quantity'];

							if ($cbo_bill_status==1) {

								if ($bill_qty>0) continue;
								
							}else if($cbo_bill_status==2){

								if ($bill_qty==$delevery_qty || $bill_qty==0) continue;

							}else if($cbo_bill_status==3){

								if ($bill_qty!=$delevery_qty && $bill_qty>=0) continue;
							}
							
						 
							$discount=$trims_bill_data_arr[$mst_id][$item_group][$order_uom]['discount'];
							$net_bill_amount=$trims_bill_data_arr[$mst_id][$item_group][$order_uom]['net_bill_amount'];
							
							
							
							
							$bill_amount=$trims_bill_data_arr[$mst_id][$item_group][$order_uom]['bill_amount'];
							$bill_quantity=$trims_bill_data_arr[$mst_id][$item_group][$order_uom]['quantity'];

							$currency_id=$trims_bill_data_arr[$mst_id][$item_group][$order_uom]['currency_id'];
							//$rate=number_format($trims_bill_data_arr[$mst_id][$item_group][$order_uom]['bill_rate'],4);
							//$rate=number_format($trims_bill_data_arr[$mst_id][$item_group][$order_uom]['wo_rate'],4);

							$takarate=0;
							$usdrate=0;
							$bill_amount_taka=0;
							$bill_amount_usd=0;

							$rate=number_format($bill_amount/$bill_quantity,4);
							
							if($currency_id==1)
							{
								 $takarate=$rate;
								 $usdrate=number_format($rate/$currency_rate,4);

								 //$bill_amount_taka=$bill_amount;
								 $bill_amount_taka=$bill_quantity*$takarate;
								 //$bill_amount_usd=$bill_amount/$currency_rate;
								 $bill_amount_usd=$bill_quantity*$usdrate;
							}
							elseif($currency_id==2)
							{
								$takarate=number_format($rate*$currency_rate,4);
								$usdrate=$rate;

								//$bill_amount_taka=$bill_amount*$currency_rate;
								$bill_amount_taka=$bill_quantity*$takarate;
								//$bill_amount_usd=$bill_amount;
								$bill_amount_usd=$bill_quantity*$usdrate;
							}


							$currency_deli_id=$row['currency_id'];
							//$deli_rate=$row['rate'];
							$deli_rate=$row['order_receive_rate'];
							$deli_usdrate=0;
							$delevery_valu_usd=0;
 							//echo $currency_deli_id; die;
							if($currency_deli_id==1)
							{
								 $deli_usdrate=number_format($deli_rate/$currency_rate,4);
								 $delevery_valu_usd=$row['delevery_qty']*$deli_usdrate;
							}
							elseif($currency_deli_id==2)
							{
								$deli_usdrate=$deli_rate;
								$delevery_valu_usd=$row['delevery_qty']*$deli_usdrate;
								
							}

							$trims_bill=$trims_bill_data_arr[$mst_id][$item_group][$order_uom]['trims_bill'];
							$trims_bill_no=implode(",",array_unique(explode(",",chop($trims_bill,','))));
							$trims_order_style=implode(",",array_unique(explode(",",chop($row['style'],','))));
							$buyer_po_no=implode(",",array_unique(explode(",",chop($row['buyer_po_no'],','))));
							
							?>
			                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
			                	<td width="35"  align="center"><? echo $i;?></td>
			                	<td width="100" style="word-break: break-all;" align="center"><? echo $row['trims_del'];; ?></td>
			                	<td width="100" style="word-break: break-all;" align="center"><? echo change_date_format($row['delivery_date']); ?></td>
			                	<td width="100" style="word-break: break-all;" align="center"><? echo $row['subcon_job']; //$row['job_no_mst']; ?></td>
			                	<td width="100" style="word-break: break-all;" align="center"><? echo $row['order_no']; //$row['job_no_mst']; ?></td>
			                	<td width="60" style="word-break: break-all;" align="center"><? echo $row['year']; ?></td>
			                	<td width="80" style="word-break: break-all;" align="center"><? echo $row['buyer_tb']; ?></td>
			                	<td width="120" style="word-break: break-all;" align="left"><? echo $party=($row['within_group']==1)?$companyArr[$row['party_id']]:$buyerArr[$row['party_id']]; ?></td>
			                	<td width="150" style="word-break: break-all;" align="left"><? echo $row['delivery_point']; ?></td>
			                	<td width="100" style="word-break: break-all;" align="left"><? echo $buyer_po_no; ?></td>
                                <td width="160" style="word-break: break-all;" align="left"><? echo $trims_order_style; ?></td>
			                	<td width="100" style="word-break: break-all;" align="left"><? echo $buyer_buyer=($row['within_group']==1)?$buyerArr[$row['buyer_buyer']]:$row['buyer_buyer'];?></td>
			                	<td width="100" style="word-break: break-all;" align="left"><? echo $trims_section[$row['section']]; ?></td>
			                	<td width="100" style="word-break: break-all;" align="center"><? echo $item_name_arr[$item_group]; ?></td>
			                	<td width="60" style="word-break: break-all;" align="center"><? echo $unit_of_measurement[$row['order_uom']];?></td> 

			                	<td width="100" style="word-break: break-all;" align="right"><? echo number_format($row['delevery_qty'],2); ?></td>
			                	<td width="100" title="<? echo  " currency_deli_id ".$currency_deli_id." currency_rate ".$currency_rate." deli_rate ".$deli_rate." deli_usdrate ".$deli_usdrate; ?>" style="word-break: break-all;"   align="right"><? echo  number_format($row['order_amount'],2);//number_format($delevery_valu_usd,4); ?></td>

			                	<td width="100" style="word-break: break-all;" align="center"><? echo $trims_bill_no; ?></td>
			                	<td width="100" style="word-break: break-all;" align="center"><? echo change_date_format($trims_bill_data_arr[$mst_id][$item_group][$order_uom]['bill_date']);?></td>

			                	<td width="100" style="word-break: break-all;" align="right"><? echo number_format($trims_bill_data_arr[$mst_id][$item_group][$order_uom]['quantity'],2);?></td>

			                	<td width="100" style="word-break: break-all;" align="right"><? echo number_format($usdrate,4); ?></td>
			                	<td width="100" style="word-break: break-all;" align="right"><? echo number_format($takarate,4); ?></td>
			                	<td width="100" style="word-break: break-all;" align="right"><? echo number_format($bill_amount_taka,4);?></td>
			                	<td width="100" style="word-break: break-all;" align="right"><? echo number_format($bill_amount_usd,4);?></td>
                                <td width="100" style="word-break: break-all;" align="right"><? echo number_format($discount,4);?></td>
                                <td width="100" style="word-break: break-all;" align="right"><? echo number_format($net_bill_amount,4);?></td>
 			                	<td width="100" style="word-break: break-all;" align="right"><? 
			                	$partial_qty=$row['delevery_qty']-$trims_bill_data_arr[$mst_id][$item_group][$order_uom]['quantity'];
			                	 echo number_format($partial_qty,4);?></td>
			                	<td width="100" style="word-break: break-all;" align="right"><? 
			                	$partial_amount=$partial_qty*$usdrate;
			                	 echo number_format($partial_amount,4);?></td>
			                	<td width="" style="word-break: break-all;" align="center"><? 
			                	
								if ($bill_qty==0) {
									$bill_status="Fully Pending";
								}else if ($bill_qty!=$delevery_qty && $bill_qty>=0) {
									$bill_status="Partial Pending";
								}else if ($bill_qty==$delevery_qty) {
									$bill_status="Bill Done";
								}
			                	 echo $bill_status;?></td>
			                </tr>
			                <? 
							$i++;
							$total_bill_amount_tk+=$bill_amount_taka;
							$total_bill_amount_usd+=$bill_amount_usd;
							$total_deli_amount_usd+=$delevery_valu_usd;
							$total_partial_value_usd+=$partial_amount;

						}
					}
				}
			}
		
			?>    
       	</table>
        </div>
        <table width="<? echo $width;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer" align="left">
				<tfoot>
                    <th width="35"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="60"></th>
                    <th width="80"></th>
                    <th width="120"></th>
                    <th width="150"></th>
                    <th width="100"></th>
                    <th width="160"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="60"></th>
                    <th width="100">Total:</th>
                    <th width="100"><? echo number_format($total_deli_amount_usd,4);?></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"><? echo number_format($total_bill_amount_tk,4);?></th>
                    <th width="100"><? echo number_format($total_bill_amount_usd,4);?></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"><? echo number_format($total_partial_value_usd,4);?></th>
                    <th></th>
				</tfoot>
			</table>
    </div>
	<?
	
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


if($action=="generate_report_average_rate_wise")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	////////////////////////////////////////////////
	$cbo_company_id=str_replace("'","", $cbo_company_id);
	$cbo_location_name=str_replace("'","", $cbo_location_name);
	$cbo_customer_source=str_replace("'","", $cbo_customer_source);
	$cbo_customer_name=str_replace("'","", $cbo_customer_name);
	$txt_order_no=str_replace("'","", $txt_order_no);
	$hid_order_id=str_replace("'","", $hid_order_id);
	$cbo_bill_status=str_replace("'","", $cbo_bill_status);
	$txt_date_from=str_replace("'","", $txt_date_from);
	$txt_date_to=str_replace("'","", $txt_date_to);
	$cbo_section_id=str_replace("'","", $cbo_section_id);
	
	
	if($cbo_company_id){$where_con.=" and a.company_id='$cbo_company_id'";} 
	
	if($cbo_customer_source){
		$where_con.=" and a.within_group='$cbo_customer_source'";
		$where_con_ord.=" and a.within_group='$cbo_customer_source'";
	} 
	if($cbo_customer_name){
		$where_con.=" and a.party_id='$cbo_customer_name'";
		$where_con_ord.=" and a.party_id='$cbo_customer_name'";
	} 
	if($cbo_location_name){$where_con.=" and a.location_id='$cbo_location_name'";} 
	
	//////////////////////////////////////////////////////////////////
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 ","id","buyer_name");
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 ","id","supplier_name");
	$trimsGroupArr = return_library_array("select id, item_name from lib_item_group where item_category=4 and status_active=1","id","item_name");
	$colorNameArr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$item_name_arr = return_library_array("select id, item_name from lib_item_group where item_category=4 and status_active=1 and is_deleted=0","id","item_name");
	//////////////////////////////////////////////////////////////////
	
		if($txt_date_from!="" and $txt_date_to!="")
		{	
			$where_con.=" and a.delivery_date between '$txt_date_from' and '$txt_date_to'";
		}
		
		
	if(trim($txt_order_no)!="")
	{
			$sql_cond="and a.trims_del like '%$txt_order_no%'";
	}

	if(trim($hid_order_id)!="")
	{
			$sql_cond_ord="and c.mst_id= '$hid_order_id'";
	}
	if($cbo_section_id != 0)
	{
		$sectionCond=" and b.section= '$cbo_section_id'";
	}
		
	
	$currency_rate=return_field_value( "conversion_rate", "currency_conversion_rate", "currency=2 and status_active=1 and con_date=(select max(con_date) as con_date from currency_conversion_rate)" , "conversion_rate" );


	if($db_type==0) 
	{
		$ins_year_cond="year(e.insert_date)";
	}
	else if($db_type==2)
	{
		$ins_year_cond="TO_CHAR(e.insert_date,'YYYY')";
	}
	
	
	//echo $currency_rate; die;

	//main query

	$trims_deli_sql= "SELECT a.id,a.trims_del,a.delivery_date, a.within_group, a.party_id, b.received_id, b.receive_dtls_id, b.order_no,b.delevery_qty, b.item_group, b.order_uom, c.buyer_buyer,c.job_no_mst, d.style, $ins_year_cond as year, e.currency_id, c.rate, e.delivery_point, b.section,e.subcon_job, e.buyer_tb,b.order_quantity,c.amount,b.order_receive_rate
	From trims_delivery_mst a, trims_delivery_dtls b,subcon_ord_dtls c, subcon_ord_breakdown d, subcon_ord_mst e
	where e.id=c.mst_id and c.id=d.mst_id and d.id=b.break_down_details_id and a.id=b.mst_id and c.id=b.receive_dtls_id and b.delevery_qty>0 and a.entry_form=208 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sql_cond $where_con $sectionCond $company  order by a.id DESC"; //e.exchange_rate,c.booked_conv_fac,

	//echo $trims_deli_sql; //die;
	  
		$result = sql_select($trims_deli_sql);
        $date_array=array();
		$deli_id_arr=array();
        foreach($result as $row)
        {
			$deli_id_arr[$row[csf("id")]]=$row[csf("id")];
			
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['id']=$row[csf('id')];
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['trims_del']=$row[csf('trims_del')];
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['subcon_job']=$row[csf('subcon_job')];
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['buyer_tb']=$row[csf('buyer_tb')];
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['delivery_date']=$row[csf('delivery_date')];
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['party_id']=$row[csf('party_id')];
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['received_id']=$row[csf('received_id')];
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['receive_dtls_id']=$row[csf('receive_dtls_id')];
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['order_no']=$row[csf('order_no')];
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['delevery_qty']+=$row[csf('delevery_qty')];
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['item_group']=$row[csf('item_group')];
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['order_uom']=$row[csf('order_uom')];
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['buyer_buyer']=$row[csf('buyer_buyer')];
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['job_no_mst']=$row[csf('job_no_mst')];
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['style'].=$row[csf('style')].',';
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['within_group']=$row[csf('within_group')];
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['year']=$row[csf('year')];

       	 	//$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['exchange_rate']=$row[csf('exchange_rate')];
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['currency_id']=$row[csf('currency_id')];
       	 	//$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['booked_conv_fac']=$row[csf('booked_conv_fac')];
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['rate']=$row[csf('rate')];
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['delivery_point']=$row[csf('delivery_point')];
       	 	$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['section']=$row[csf('section')];
			
			//$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['order_quantity']+=$row[csf('order_quantity')]*$row[csf('order_receive_rate')];
			
			$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['order_amount']+=$row[csf('delevery_qty')]*$row[csf('order_receive_rate')];
			//$date_array[$row[csf('id')]][$row[csf('delivery_date')]][$row[csf('item_group')]][$row[csf('order_uom')]]['amount']+=$row[csf('amount')];
			
       	 	
 		}

 		$deli_id_arr = array_unique($deli_id_arr);
 
 
 	/*echo "<pre>";
	print_r($date_array);
 	echo "</pre>"; die;*/

	$delivery_con=where_con_using_array($deli_id_arr,0,"c.mst_id");

	$trims_bill_sql = "select a.id, a.trims_bill, a.within_group, a.currency_id, a.exchange_rate, a.bill_date, b.quantity, b.bill_rate, b.bill_amount, c.mst_id, c.item_group, c.order_uom, d.rate as wo_rate
	from trims_bill_mst a, trims_bill_dtls b, trims_delivery_dtls c, subcon_ord_dtls d where  a.entry_form=276 and a.id=b.mst_id and c.id=b.production_dtls_id and d.id=c.receive_dtls_id $delivery_con and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.id ASC"; //e.id=c.mst_id and //trims_delivery_mst e //e.delivery_date

	//echo $trims_bill_sql; die;

	$result_bill_sql=sql_select($trims_bill_sql);
	$trims_bill_data_arr=array();
	foreach($result_bill_sql as $row)
	{
		$disc=TRIM($row[csf('description')]);
		$trims_bill_data_arr[$row[csf('mst_id')]][$row[csf('item_group')]][$row[csf('order_uom')]]['trims_bill'].=$row[csf('trims_bill')].',';
		$trims_bill_data_arr[$row[csf('mst_id')]][$row[csf('item_group')]][$row[csf('order_uom')]]['within_group']=$row[csf('within_group')];
		$trims_bill_data_arr[$row[csf('mst_id')]][$row[csf('item_group')]][$row[csf('order_uom')]]['currency_id']=$row[csf('currency_id')];
		$trims_bill_data_arr[$row[csf('mst_id')]][$row[csf('item_group')]][$row[csf('order_uom')]]['exchange_rate']=$row[csf('exchange_rate')];
		$trims_bill_data_arr[$row[csf('mst_id')]][$row[csf('item_group')]][$row[csf('order_uom')]]['bill_date']=$row[csf('bill_date')];
		$trims_bill_data_arr[$row[csf('mst_id')]][$row[csf('item_group')]][$row[csf('order_uom')]]['quantity']+=$row[csf('quantity')];
		$trims_bill_data_arr[$row[csf('mst_id')]][$row[csf('item_group')]][$row[csf('order_uom')]]['bill_amount']+=$row[csf('bill_amount')];
		//$trims_bill_data_arr[$row[csf('mst_id')]][$row[csf('item_group')]][$row[csf('order_uom')]]['bill_rate']=$row[csf('bill_rate')];
		$trims_bill_data_arr[$row[csf('mst_id')]][$row[csf('item_group')]][$row[csf('order_uom')]]['wo_rate']=$row[csf('wo_rate')];
		$trims_bill_data_arr[$row[csf('mst_id')]][$row[csf('item_group')]][$row[csf('order_uom')]]['bill_rate']=$row[csf('bill_amount')]/$row[csf('quantity')];

		//[$row[csf('bill_date')]]
	}
 
	/*echo "<pre>";
	print_r($trims_bill_data_arr);
	echo "</pre>"; die;*/
 
 
	
	$width=1645;
	ob_start();
	?>	
	<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:0;">
    	<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="caption" align="center">
				<thead class="form_caption" >
					<tr>
						<td colspan="22" align="center" style="font-size:20px;"><? echo $companyArr[$cbo_company_id]; ?></td>
					</tr>
					<tr>
						<td colspan="22" align="center" style="font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
					</tr>
					<tr>
						<td colspan="22" align="center" style="font-size:14px; font-weight:bold">
							<? echo " From : ".change_date_format($txt_date_from) ." To : ". change_date_format($txt_date_to) ;?>
						</td>
					</tr>
				</thead>
			</table>
            <table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
				<thead>
                    <th width="35">SL</th>
                    <th width="100">Challan No</th>
                    <th width="100">Challan Date</th>
                    <th width="100"> Job No</th>
                    <th width="100">Work Order No</th>
                    <th width="60">WO Year</th>
                    <th width="80">Buyers TB</th>
                    <th width="120">Party Name</th>
                    <th width="150">Delivery Point</th>
                    <th width="160">Style</th>
                    <th width="100">Buyer</th>
                    <th width="100">Section</th>
                    <th width="100">Item Name</th>
                    <th width="60">WO UOM</th>
                    <th width="100">Delivery Qty</th>
                    <th>Total Delv.Value($)</th>
                    
				</thead>
			</table>
        <div style="width:<? echo $width+18;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
        	<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="<? echo $width;?>" rules="all" align="left">
            <? 
			$i=1;
			$total_bill_amount_usd=0;
			$total_bill_amount_tk=0;
			$total_deli_amount_usd=0;
			$total_partial_value_usd=0;
			foreach($date_array as $mst_id=>$mst_id_data)
			{
				foreach($mst_id_data as $delivery_date=>$delivery_date_data)
				{
					foreach($delivery_date_data as $item_group=>$item_group_data)
					{
						foreach($item_group_data as $order_uom=>$row)
						{
							$delevery_qty=$row['delevery_qty'];
							$bill_qty=$trims_bill_data_arr[$mst_id][$item_group][$order_uom]['quantity'];

							if ($cbo_bill_status==1) {

								if ($bill_qty>0) continue;
								
							}else if($cbo_bill_status==2){

								if ($bill_qty==$delevery_qty || $bill_qty==0) continue;

							}else if($cbo_bill_status==3){

								if ($bill_qty!=$delevery_qty && $bill_qty>=0) continue;
							}

							$bill_amount=$trims_bill_data_arr[$mst_id][$item_group][$order_uom]['bill_amount'];
							$bill_quantity=$trims_bill_data_arr[$mst_id][$item_group][$order_uom]['quantity'];

							$currency_id=$trims_bill_data_arr[$mst_id][$item_group][$order_uom]['currency_id'];
							//$rate=number_format($trims_bill_data_arr[$mst_id][$item_group][$order_uom]['bill_rate'],4);
							//$rate=number_format($trims_bill_data_arr[$mst_id][$item_group][$order_uom]['wo_rate'],4);

							$takarate=0;
							$usdrate=0;
							$bill_amount_taka=0;
							$bill_amount_usd=0;

							$rate=number_format($bill_amount/$bill_quantity,4);
							
							if($currency_id==1)
							{
								 $takarate=$rate;
								 $usdrate=number_format($rate/$currency_rate,4);

								 //$bill_amount_taka=$bill_amount;
								 $bill_amount_taka=$bill_quantity*$takarate;
								 //$bill_amount_usd=$bill_amount/$currency_rate;
								 $bill_amount_usd=$bill_quantity*$usdrate;
							}
							elseif($currency_id==2)
							{
								$takarate=number_format($rate*$currency_rate,4);
								$usdrate=$rate;

								//$bill_amount_taka=$bill_amount*$currency_rate;
								$bill_amount_taka=$bill_quantity*$takarate;
								//$bill_amount_usd=$bill_amount;
								$bill_amount_usd=$bill_quantity*$usdrate;
							}


							$currency_deli_id=$row['currency_id'];
							$deli_rate=$row['rate'];
							$deli_usdrate=0;
							$delevery_valu_usd=0;
 
							if($currency_deli_id==1)
							{
								 $deli_usdrate=number_format($deli_rate/$currency_rate,4);
								 $delevery_valu_usd=$row['delevery_qty']*$deli_usdrate;
							}
							elseif($currency_deli_id==2)
							{
								$deli_usdrate=$deli_rate;
								$delevery_valu_usd=$row['delevery_qty']*$deli_usdrate;
								
							}

							$trims_bill=$trims_bill_data_arr[$mst_id][$item_group][$order_uom]['trims_bill'];
							$trims_bill_no=implode(",",array_unique(explode(",",chop($trims_bill,','))));
							$trims_order_style=implode(",",array_unique(explode(",",chop($row[style],','))));
							
						 
							$order_quantity=number_format($row['order_quantity'],2); 
							$order_amount=number_format($row['amount'],2);
							$avarage_rate=$order_amount/$order_quantity;
							$delevery_value=number_format($row['delevery_qty'],2)*$avarage_rate;
							?>
			                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
			                	<td width="35"  align="center"><? echo $i;?></td>
			                	<td width="100" style="word-break: break-all;" align="center"><? echo $row['trims_del'];; ?></td>
			                	<td width="100" style="word-break: break-all;" align="center"><? echo change_date_format($row['delivery_date']); ?></td>
			                	<td width="100" style="word-break: break-all;" align="center"><? echo $row['subcon_job']; //$row['job_no_mst']; ?></td>
			                	<td width="100" style="word-break: break-all;" align="center"><? echo $row['order_no']; //$row['job_no_mst']; ?></td>
			                	<td width="60" style="word-break: break-all;" align="center"><? echo $row['year']; ?></td>
			                	<td width="80" style="word-break: break-all;" align="center"><? echo $row['buyer_tb']; ?></td>
			                	<td width="120" style="word-break: break-all;" align="left"><? echo $party=($row['within_group']==1)?$companyArr[$row['party_id']]:$buyerArr[$row['party_id']]; ?></td>
			                	<td width="150" style="word-break: break-all;" align="left"><? echo $row['delivery_point']; ?></td>
			                	<td width="160" style="word-break: break-all;" align="left"><? echo $trims_order_style; ?></td>
			                	<td width="100" style="word-break: break-all;" align="left"><? echo $buyer_buyer=($row['within_group']==1)?$buyerArr[$row['buyer_buyer']]:$row['buyer_buyer'];?></td>
			                	<td width="100" style="word-break: break-all;" align="left"><? echo $trims_section[$row['section']]; ?></td>
			                	<td width="100" style="word-break: break-all;" align="center"><? echo $item_name_arr[$item_group]; ?></td>
			                	<td width="60" style="word-break: break-all;" align="center"><? echo $unit_of_measurement[$row['order_uom']];?></td> 

			                	<td width="100" style="word-break: break-all;" align="right"><? echo number_format($row['delevery_qty'],2); ?></td>
			                	<td title="<? //echo "order_quantity".$order_quantity."order_amount".$order_amount."avarage_rate".$avarage_rate; ?>" style="word-break: break-all;" align="right"><? echo  number_format($row['order_amount'],4);//number_format($delevery_value,4); ?></td>
 
			                </tr>
			                <? 
							$i++;
							$total_bill_amount_tk+=$bill_amount_taka;
							$total_bill_amount_usd+=$bill_amount_usd;
							$total_deli_amount_usd+=$row['order_amount'];//$delevery_value;
							$total_partial_value_usd+=$partial_amount;

						}
					}
				}
			}
		
			?>    
       	</table>
        </div>
        <table width="<? echo $width;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer" align="left">
				<tfoot>
                    <th width="35"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="60"></th>
                    <th width="80"></th>
                    <th width="120"></th>
                    <th width="150"></th>
                    <th width="160"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="60"></th>
                    <th width="100">Total:</th>
                    <th id="value_totDeliveryQty"><? echo number_format($total_deli_amount_usd,4);?></th>   
				</tfoot>
			</table>
    </div>
	<?
	
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


if($action=="generate_report_show3")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	////////////////////////////////////////////////
	$cbo_company_id=str_replace("'","", $cbo_company_id);
	$cbo_location_name=str_replace("'","", $cbo_location_name);
	$cbo_customer_source=str_replace("'","", $cbo_customer_source);
	$cbo_customer_name=str_replace("'","", $cbo_customer_name);
	$txt_order_no=str_replace("'","", $txt_order_no);
	$hid_order_id=str_replace("'","", $hid_order_id);
	$cbo_bill_status=str_replace("'","", $cbo_bill_status);
	$txt_date_from=str_replace("'","", $txt_date_from);
	$txt_date_to=str_replace("'","", $txt_date_to);
	$cbo_section_id=str_replace("'","", $cbo_section_id);
	
	
	if($cbo_company_id){$where_con.=" and a.company_id='$cbo_company_id'";} 
	
	if($cbo_customer_source){
		$where_con.=" and a.within_group='$cbo_customer_source'";
		$where_con_ord.=" and a.within_group='$cbo_customer_source'";
	} 
	if($cbo_customer_name){
		$where_con.=" and a.party_id='$cbo_customer_name'";
		$where_con_ord.=" and a.party_id='$cbo_customer_name'";
	} 
	if($cbo_location_name){$where_con.=" and a.location_id='$cbo_location_name'";} 
	
	//////////////////////////////////////////////////////////////////
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 ","id","buyer_name");
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 ","id","supplier_name");
	$trimsGroupArr = return_library_array("select id, item_name from lib_item_group where item_category=4 and status_active=1","id","item_name");
	$colorNameArr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$item_name_arr = return_library_array("select id, item_name from lib_item_group where item_category=4 and status_active=1 and is_deleted=0","id","item_name");
	//////////////////////////////////////////////////////////////////
	
		if($txt_date_from!="" and $txt_date_to!="")
		{	
			$where_con.=" and a.delivery_date between '$txt_date_from' and '$txt_date_to'";
		}
		
		
	if(trim($txt_order_no)!="")
	{
			$sql_cond="and a.trims_del like '%$txt_order_no%'";
	}

	if(trim($hid_order_id)!="")
	{
			$sql_cond_ord="and c.mst_id= '$hid_order_id'";
	}
	if($cbo_section_id != 0)
	{
		$sectionCond=" and b.section= '$cbo_section_id'";
	}
		
	
	$currency_rate=return_field_value( "conversion_rate", "currency_conversion_rate", "currency=2 and status_active=1 and con_date=(select max(con_date) as con_date from currency_conversion_rate)" , "conversion_rate" );


	if($db_type==0) 
	{
		$ins_year_cond="year(e.insert_date)";
	}
	else if($db_type==2)
	{
		$ins_year_cond="TO_CHAR(e.insert_date,'YYYY')";
	}
	
	
	//echo $currency_rate; die;

	//main query

	/* $trims_deli_sql= "SELECT a.id as challan_mst_id,a.delivery_date, a.within_group, a.party_id, b.received_id, b.receive_dtls_id, b.order_no,b.item_group, b.order_uom, c.buyer_buyer,c.job_no_mst, d.style, $ins_year_cond as year, e.currency_id, d.rate, e.delivery_point, b.section,e.subcon_job, e.buyer_tb,b.order_quantity,d.amount,b.order_receive_rate, e.PARTY_WISE_GRADE, c.BUYER_STYLE_REF, c.BUYER_PO_NO, d.color_id, d.SIZE_NAME, d.qnty as SUBCON_ORD_QTY,(d.qnty*d.rate) AS SUBCON_ORD_VAL, a.TRIMS_DEL as CHALLAN_NO, E.ID AS JOB_ID, C.ID AS JOB_DTLS_ID, (b.DELEVERY_QTY) as DELEVERY_QTY,  (b.DELEVERY_QTY*d.rate) as DELEVERY_VAL,b.DELEVERY_STATUS as DELIVERY_STATUS, b.REMARKS
	FROM subcon_ord_dtls c
		inner join subcon_ord_breakdown d on c.id=d.mst_id and d.is_deleted=0 and d.status_active=1
		inner join subcon_ord_mst e on e.id=c.mst_id and e.is_deleted=0 and e.status_active=1
		left  join trims_delivery_dtls b on d.id=b.break_down_details_id and c.id=b.receive_dtls_id  and e.is_deleted=0 and e.status_active=1
		left  join trims_delivery_mst a on a.id=b.mst_id and e.is_deleted=0 and e.status_active=1
	where b.delevery_qty>0 and a.entry_form=208 and c.is_deleted=0 and c.status_active=1 $sql_cond $where_con $sectionCond $company"; 
 */

	$trims_deli_sql= "SELECT a.id as challan_mst_id, a.trims_del,a.delivery_date, a.within_group, a.party_id, b.received_id, b.receive_dtls_id, b.order_no,b.delevery_qty, b.item_group, b.order_uom, c.buyer_buyer,c.job_no_mst, d.style, $ins_year_cond as year, e.currency_id, c.rate, e.delivery_point, b.section,e.subcon_job, e.buyer_tb,b.order_quantity,c.amount,b.order_receive_rate,e.PARTY_WISE_GRADE,c.BUYER_STYLE_REF,c.BUYER_PO_NO,d.color_id, d.SIZE_NAME,d.qnty as SUBCON_ORD_QTY,(d.qnty*d.rate) AS SUBCON_ORD_VAL,a.TRIMS_DEL as CHALLAN_NO,E.ID AS JOB_ID,C.ID AS JOB_DTLS_ID,(b.DELEVERY_QTY*b.order_receive_rate) as DELEVERY_VAL,b.DELEVERY_STATUS as DELIVERY_STATUS,b.REMARKS
	FROM trims_delivery_mst a, trims_delivery_dtls b,subcon_ord_dtls c, subcon_ord_breakdown d, subcon_ord_mst e
	where e.id=c.mst_id and c.id=d.mst_id and d.id=b.break_down_details_id and a.id=b.mst_id and c.id=b.receive_dtls_id and b.delevery_qty>0 and a.entry_form=208 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sql_cond $where_con $sectionCond $company  order by a.id DESC";

	$result = sql_select($trims_deli_sql);
	$date_array=array();
	$rowSpan = array();
	$deli_id_arr=array();
	$sectionArray = array();
	$summery = array();
	$spanData = array();
	foreach($result as $row)
	{
		$deli_id_arr[$row[csf("CHALLAN_MST_ID")]]=$row[csf("CHALLAN_MST_ID")];
		// foreach($dataCols as $col)
		// {
		// 	$date_array[$row['JOB_ID']][$row['JOB_DTLS_ID']][$row['ITEM_GROUP']][$row['COLOR_ID']][$row['SIZE_NAME']][$row['CHALLAN_MST_ID']][$col]=$row[$col];
		// }

		$date_array[$row['JOB_ID']][$row['JOB_DTLS_ID']][$row['ITEM_GROUP']][$row['COLOR_ID']][$row['SIZE_NAME']][$row['CHALLAN_MST_ID']]=$row;

	

		$rowSpan[$row['JOB_ID']][$row['JOB_DTLS_ID']][$row['ITEM_GROUP']][$row['COLOR_ID']][$row['SIZE_NAME']]['CHALLAN_COUNT'][$row['CHALLAN_MST_ID']]=$row['CHALLAN_MST_ID'];

		$summery[$row['PARTY_ID']][$row['SECTION']] += $row['DELEVERY_VAL'];
		$sectionArray[$row["SECTION"]] = $row["SECTION"];

 
		$spanData[$row['JOB_ID']][$row['JOB_DTLS_ID']][$row['ITEM_GROUP']][$row['COLOR_ID']][$row['SIZE_NAME']]['totalDelvVal'] += $row['DELEVERY_VAL'];
		$spanData[$row['JOB_ID']][$row['JOB_DTLS_ID']][$row['ITEM_GROUP']][$row['COLOR_ID']][$row['SIZE_NAME']]['totalDelvQty'] += $row['DELEVERY_QTY'];

		$spanData[$row['JOB_ID']][$row['JOB_DTLS_ID']][$row['ITEM_GROUP']][$row['COLOR_ID']][$row['SIZE_NAME']]['delvBalanceQty'] += ($row['SUBCON_ORD_QTY']-$row['DELEVERY_QTY']);
		$spanData[$row['JOB_ID']][$row['JOB_DTLS_ID']][$row['ITEM_GROUP']][$row['COLOR_ID']][$row['SIZE_NAME']]['delvBalanceValue'] += ($row['SUBCON_ORD_VAL']-$row['DELEVERY_VAL']);


	}





	//  echo "<pre>"; print_r($summery); 
	//  echo "<pre>"; print_r($date_array2);  exit();
	 
	

	
	foreach($date_array__________ as $jobId => $row1)
	{
		foreach($row1 as $jobDtlsId => $row2)
		{
			foreach($row2 as $itemGroup => $row3)
			{
				foreach($row3 as $colorId => $row4)
				{
					foreach($row4 as $size => $row5)
					{ $ordQty=0;
						foreach($row5 as $challanMST => $row)
						{
							$ordQty 			+= $row['SUBCON_ORD_QTY'];
							//$rate 				= $row['RATE'];
							//$currDelvVal 		= $row['DELEVERY_QTY'] * $row['RATE'];
							$spanData[$jobId][$jobDtlsId][$itemGroup][$colorId][$size]['totalDelvVal'] 	+= $row['DELEVERY_VAL'];
							$spanData[$jobId][$jobDtlsId][$itemGroup][$colorId][$size]['totalDelvQty'] 	+= $row['DELEVERY_QTY'];

							$spanData[$jobId][$jobDtlsId][$itemGroup][$colorId][$size]['delvBalanceQty'] += ($row['SUBCON_ORD_QTY']-$row['DELEVERY_QTY']);
							$spanData[$jobId][$jobDtlsId][$itemGroup][$colorId][$size]['delvBalanceValue'] += ($row['SUBCON_ORD_VAL']-$row['DELEVERY_VAL']);

						}
						//$spanData[$jobId][$jobDtlsId][$itemGroup][$colorId][$size]['delvBalanceQty'] 	+= ($ordQty - $spanData[$jobId][ $jobDtlsId][$itemGroup][$colorId][$size]['totalDelvQty']);
						
						//$spanData[$jobId][$jobDtlsId][$itemGroup][$colorId][$size]['delvBalanceValue'] += (($spanData[$jobId][$jobDtlsId][$itemGroup][$colorId][$size]['totalOrdVal']- $spanData[$jobId][$jobDtlsId][$itemGroup][$colorId][$size]['totalDelvVal']));
					}
				}
			}
		}
	}


	$party_wise_grade_arr=array(1=>"A",2=>"B",3=>"C",4=>"D",5=>"E",6=>"F",7=>"G");
 
	$section_num = count($sectionArray);

	$summaryWidth=300+100*$section_num;
	$width=2385;
	ob_start();
	?>	
	<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:0;">
		<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="caption" align="center">
			<thead class="form_caption" >
				<tr>
					<td colspan="22" align="center" style="font-size:20px;"><? echo $companyArr[$cbo_company_id]; ?></td>
				</tr>
				<tr>
					<td colspan="22" align="center" style="font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
				</tr>
				<tr>
					<td colspan="22" align="center" style="font-size:14px; font-weight:bold">
						<? echo " From : ".change_date_format($txt_date_from) ." To : ". change_date_format($txt_date_to) ;?>
					</td>
				</tr>
			</thead>
		</table>
		<div style="width:<? echo $width+20;?>px;">
			<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header">
				<thead>
					<th width="35"><p>SL</p></th>
					<th width="100"><p>Job No</p></th>
					<th width="100"><p>Work Order No</p></th>
					<th width="100"><p>Party Name</p></th>
					<th width="50"><p>Party Grade</p></th>
					<th width="100"><p>Buyer Style</p></th>
					<th width="100"><p>Buyer PO</p></th>
					<th width="100"><p>Section</p></th>
					<th width="100"><p>Item Description</p></th>
					<th width="100"><p>Item Color</p></th>
					<th width="200"><p>Item Size</p></th>
					<th width="50"><p>Order UOM</p></th>
					<th width="100"><p>Job Order Qty</p></th>
					<th width="50"><p>U/Price $</p></th>
					<th width="100"><p>Order Value($)</p></th>
					<th width="100"><p>Challan NO</p></th>
					<th width="100"><p>Challan Date</p></th>
					<th width="100"><p>Current Delv Qty</p></th>
					<th width="100"><p>Curr. Delv Value</p></th>
					<th width="100"><p>Total Delv Qty</p></th>
					<th width="100"><p>Total Delv Value</p></th>
					<th width="100"><p>Delv. Bal. Qty</p></th>
					<th width="100"><p>Delv. Bal. Value</p></th>
					<th width="100"><p>Delv. Status</p></th>
					<th width="100"><p>Remarks</p></th>
					
				</thead>
			</table>
		</div>
		<div style="width:<? echo $width+20;?>px; max-height:250px; overflow-y:scroll;" id="scroll_body">
			<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="<? echo $width;?>" rules="all"> 
			<tbody>
				<?php 
					$serial = 1;
					foreach($date_array as $jobId => $row)
					{
						foreach($row as $jobDtlsId => $row)
						{
							foreach($row as $itemGroup => $row)
							{
								foreach($row as $colorId => $row)
								{
									foreach($row as $size => $row)
									{
										$k = 0;
										foreach($row as $challanMST => $row)
										{
											$currDelvVal 		= $row['DELEVERY_QTY'] * $row['RATE'];
											if ($serial%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											?>
												<tr onClick="change_color('tr_<? echo $serial; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $serial; ?>">
													<td width="35"><?=$serial?></td>
													<td width="100"><p><?=$row['SUBCON_JOB']?></p></td>
													<td width="100"><p><?=$row['ORDER_NO']?></p></td>
													<td width="100"><p><?=$buyerArr[$row['PARTY_ID']]?></p></td>
													<td width="50"><p><?=$party_wise_grade_arr[$row['PARTY_WISE_GRADE']]?></p></td>
													<td width="100"><p><?=$row['BUYER_STYLE_REF']?></p></td>
													<td width="100"><p><?=$row['BUYER_PO_NO']?></p></td>
													<td width="100"><p><?=$trims_section[$row['SECTION']]?></p></td>
													<td width="100"><p><?=$trimsGroupArr[$row['ITEM_GROUP']]?></p></td>
													<td width="100"><p><?=$colorNameArr[$row['COLOR_ID']]?></p></td>
													<td width="200"><p><?=$row['SIZE_NAME']?></p></td>
													<td width="50"><p><?=$unit_of_measurement[$row['ORDER_UOM']]?></p></td>
													<? if($k==0) { ?>
													<td width="100" style="text-align:center; vertical-align: middle;" rowspan="<?=count($rowSpan[$jobId][$jobDtlsId][$itemGroup][$colorId][$size]['CHALLAN_COUNT'])?>"><p><?=$row['SUBCON_ORD_QTY']?></p></td>
													<td width="50" style="text-align:center; vertical-align: middle;" rowspan="<?=count($rowSpan[$jobId][$jobDtlsId][$itemGroup][$colorId][$size]['CHALLAN_COUNT'])?>"><p><?=$row['RATE']?></p></td>
													<td width="100" style="text-align:center; vertical-align: middle;" rowspan="<?=count($rowSpan[$jobId][$jobDtlsId][$itemGroup][$colorId][$size]['CHALLAN_COUNT'])?>"><p><?=$row['AMOUNT']?></p></td>
													<? } ?>
													
													<td width="100"><p><?=$row['CHALLAN_NO']?></p></td>
													<td width="100"><p><?=$row['DELIVERY_DATE']?></p></td>
													<td width="100" style="text-align:center; vertical-align: middle;"><p><?=number_format($row['DELEVERY_QTY'],2)?></p></td>
													<td width="100" style="text-align:center; vertical-align: middle;"><p><?=number_format($currDelvVal,2)?></p></td>
													<? if($k==0) { ?>
													<td width="100" style="text-align:center; vertical-align: middle;" rowspan="<?=count($rowSpan[$jobId][$jobDtlsId][$itemGroup][$colorId][$size]['CHALLAN_COUNT'])?>"><p><?php
														$totalDelvQty = $spanData[$jobId][$jobDtlsId][$itemGroup][$colorId][$size]['totalDelvQty'];
														$sumDelvQty += $totalDelvQty;
														echo number_format($totalDelvQty,2);
														?></p></td>
													<td width="100" style="text-align:center; vertical-align: middle;" rowspan="<?=count($rowSpan[$jobId][$jobDtlsId][$itemGroup][$colorId][$size]['CHALLAN_COUNT'])?>">
														<p><?php 
														$totalDelvVal = $spanData[$jobId][$jobDtlsId][$itemGroup][$colorId][$size]['totalDelvVal'];
														$sumDelvVal += $totalDelvVal;
														echo number_format($totalDelvVal,2);
														?></p>
													</td>
													<td width="100" style="text-align:center; vertical-align: middle;" rowspan="<?=count($rowSpan[$jobId][$jobDtlsId][$itemGroup][$colorId][$size]['CHALLAN_COUNT'])?>"><p>
													<?php 
														$delvBalanceQty = $spanData[$jobId][ $jobDtlsId][$itemGroup][$colorId][$size]['delvBalanceQty'];
														$sumDelvBalQty += $delvBalanceQty;
														echo number_format($delvBalanceQty,2); 
														?>
													</p></td>
													<td width="100" style="text-align:center; vertical-align: middle;" rowspan="<?=count($rowSpan[$jobId][$jobDtlsId][$itemGroup][$colorId][$size]['CHALLAN_COUNT'])?>"><p><?php 
														$delvBalanceValue = $spanData[$jobId][ $jobDtlsId][$itemGroup][$colorId][$size]['delvBalanceValue'];
														$sumDelvBalanceValue += $delvBalanceValue;
														echo number_format($delvBalanceValue,2);
														?></p></td>
													<? } ?>
													
													<td width="100"><p><?=$delivery_status[$row['DELIVERY_STATUS']]?></p></td>
													<td width="100"><p><?=$row['REMARKS']?></p></td>
												</tr> 
											<?
											$sumCurrentDeliveryQty += $row['DELEVERY_QTY'];
											$sumCurrentDeliveryValue += $currDelvVal;
											$serial++;
											$k++;
										}
									}
								}
							}
						}
					}
				?>
			</tbody>
			
				
			</table>
		</div>
		<div style="width:<? echo $width+20;?>px;">
			<table width="<? echo $width;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_foot" >
				<tfoot>
					<th width="35"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="50"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="200"></th>
					<th width="50"></th>
					<th width="100"></th>
					<th width="50"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"><p>Total:</p></th>
					<th width="100"><p><?=number_format($sumCurrentDeliveryQty,2)?></p></th>
					<th width="100"><p><?=number_format($sumCurrentDeliveryValue,2)?></p></th>
					<th width="100"><p><?=number_format($sumDelvQty,2)?></p></th>
					<th width="100"><p><?=number_format($sumDelvVal,2)?></p></th>
					<th width="100"><p><?=number_format($sumDelvBalQty,2)?></p></th>
					<th width="100"><p><?=number_format($sumDelvBalanceValue,2)?></p></th>
					<th width="100"></th>
					<th width="100"></th>   
				</tfoot>
			</table>
		</div>
		
	</div>

	<div align="center" style="margin-top: 30px; margin-bottom:30px;">
		<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $summaryWidth;?>" rules="all" >
			<!-- <caption>Summery</caption> -->
			<thead>
				<tr>
					<th width="35" rowspan="2">SL</th>
	                <th width="200" rowspan="2">Customer</th>
	                <th width="<?php echo 100*$section_num;?>" colspan="<?php echo $section_num; ?>">Section</th>
	                <th width="100" rowspan="2">Total ($)</th>
				</tr>
				<tr>
					<?php
						foreach($sectionArray as $data)
						{
							?>
								<th width="100"><?php echo $trims_section[$data]; ?></th>
							<?
						}
					?>
				</tr>
			</thead>
			<tbody id="summery_id">
				<?php 
					$serial = 1;
					$sectionTotalArray = array();
					foreach($summery as $party => $row)
					{
						$total = 0;
						?>
							<tr>
								<td><?php echo $serial; ?></td>
								<td><?php echo $buyerArr[$party]; ?></td>
								<? foreach($sectionArray as $data) {
									$total += $row[$data];
									$sectionTotalArray[$data] += $row[$data];
									?>
									<td width="100"><?php echo !empty($row[$data]) ? number_format($row[$data], 2) : ''; ?></td>
								<? } ?>
								<td><?php echo number_format($total, 2); ?></td>
							</tr>
						<?
						$serial++;

					}
						?>
				<tr style="background-color: #f4f4f4; font-weight:bolder; color:black;">
					<td></td>
					<td>Total:</td>
					<? foreach($sectionArray as $data) {
						$finalTotal += $sectionTotalArray[$data]
						?>
						<td width="100"><?php echo number_format($sectionTotalArray[$data], 2); ?></td>
					<? } ?>
					<td id="value_total"><?php echo number_format($finalTotal, 2); ?></td>
				</tr>
				
			</tbody>
		</table>
		<table>
			
		</table>
	</div>
	<?
	
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
?>
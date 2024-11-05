<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action == "load_drop_down_buyer") {
	$data = explode("_", $data);
	$company_id = $data[1];

	if ($company_id == 0) {
		echo create_drop_down("cbo_buyer_name", 162, $blank_array, "", 1, "--Select Buyer--", 0, "");
	} else {
		if ($data[0] == 1) {
			echo create_drop_down("cbo_buyer_name", 162, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Buyer --", "0", "", 1);
		} else if ($data[0] == 2) {
			echo create_drop_down("cbo_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90,80)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "", 0);
		}else{
            echo create_drop_down("cbo_buyer_name", 162, $blank_array, "", 1, "--Select Buyer--", 0, "");
        }
	}
	exit();
}

if ($action == "load_drop_down_cust_buyer")
{
	$data = explode("_", $data);
	$company_id = $data[1];

	if ($company_id == 0)
	{
		echo create_drop_down("cbo_cust_buyer_name", 162, $blank_array, "", 1, "--Select Buyer--", 0, "");
	}
	else
	{
		if ($data[0] == 1)
		{
			echo create_drop_down("cbo_cust_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90,80)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "", 0);
		}
		else
		{
			echo create_drop_down("cbo_cust_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90,80)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "", 0);
		}
	}
	exit();
}



if ($action=="load_drop_down_location") {	
	echo create_drop_down( 'cbo_location_name', 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );
	exit();
}

if ($action=="order_popup")
{	
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
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
                        <th width="120">W/O No</th>
                        <th colspan="2" width="160">W/O Date Range</th>
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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_order_number').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<?php  echo $company;?>+'_'+<?php echo $cbo_cust_buyer_name;?>+'_'+<?php  echo $cbo_within_group;?>,'create_booking_search_list_view', 'search_div', 'fabric_sales_order_wise_export_pi_and_lc_info_report_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                    </td>
                </tr>
            </table>
            <div id="search_div"></div>   
        </form>
    </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}
if($action=="create_booking_search_list_view")
{	
	$data=explode('_',$data);
	// print_r($data);die;
	
	if ($data[5]!=0) $within_group=" and a.WITHIN_GROUP='$data[5]'";
	if ($data[1]!="" &&  $data[2]!="") $booking_date = "and a.booking_date between '".change_date_format($data[1], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	if ($data[0]!="") $woorder_cond=" and a.sales_booking_no like '%$data[0]%' "; 
	
	$sql= "SELECT a.id, a.sales_booking_no, a.booking_date from  fabric_sales_order_mst a where a.status_active=1  and a.company_id=$data[3] $booking_date $woorder_cond $within_group order by a.id desc";  
	$data_array=sql_select($sql);
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="440" >
        <thead>
            <th width="30">SL</th>
            <th width="60">W/O No</th>
            <th width="70">W/O Date</th>
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
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('sales_booking_no')]; ?>")' style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="60" align="center"><? echo $row[csf('sales_booking_no')]; ?></td>
                    <td width="70"><? echo change_date_format($row[csf('booking_date')]); ?></td>
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

	$cbo_company_id=str_replace("'","", $cbo_company_id);
	$cbo_location_name=str_replace("'","", $cbo_location_name);
	$cbo_buyer_name=str_replace("'","", $cbo_buyer_name);
	$cbo_within_group=str_replace("'","", $cbo_within_group);
	$txt_order_no=str_replace("'","", $txt_order_no);
	$hid_order_id=str_replace("'","", $hid_order_id);
	$cbo_cust_buyer_name=str_replace("'","", $cbo_cust_buyer_name);
	$txt_date_from=str_replace("'","", $txt_date_from);
	$txt_date_to=str_replace("'","", $txt_date_to);
	$report_type=str_replace("'","", $report_type);
	$date_range_type=str_replace("'","", $date_range_all);
	$report_type_all=str_replace("'","", $report_type_all);
	
	if($cbo_cust_buyer_name){$where_con.=" and a.customer_buyer='$cbo_cust_buyer_name'";} 
	if($cbo_within_group){$where_con.=" and a.within_group='$cbo_within_group'";} 
	if($cbo_buyer_name){$where_con.=" and a.buyer_id='$cbo_buyer_name'";} 
	if($cbo_location_name){$where_con.=" and a.location_id = $cbo_location_name";}
	
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');

	if($date_range_type==1){
	   if($txt_date_from!="" and $txt_date_to!="")
	   {	
		 $date_con.=" and a.BOOKING_DATE between '$txt_date_from' and '$txt_date_to'";
	   }
    }elseif($date_range_type==2){
		$date_con.=" and d.pi_date between '$txt_date_from' and '$txt_date_to'";
	}
	elseif($date_range_type==3){
		$date_con.=" and f.lc_date between '$txt_date_from' and '$txt_date_to'";
	}
	
	if(trim($txt_order_no)!="")
	{
			$sql_order_no="and a.job_no like '%$txt_order_no%'";
	}
	$currency_rate=return_field_value( "conversion_rate", "currency_conversion_rate", "currency=2 and status_active=1  and id in(select max(id) from currency_conversion_rate where currency=2 and is_deleted=0 and status_active=1)" , "conversion_rate" ); 

	$width=2700;
	ob_start();
	if ($report_type==1) {

		if($report_type_all==1){
			$order_sql="SELECT a.job_no, sales_booking_no, a.buyer_id, a.booking_date, a.customer_buyer, a.currency_id,a.booking_approval_date, a.delivery_date, sum(b.finish_qty) as order_qty, sum(b.amount)/sum(b.finish_qty) as avg_rate, sum(b.amount) as order_value, d.pi_number,d.pi_date, sum(c.amount) as pi_amount, f.export_lc_no, f.lc_date, f.lc_value, e.attached_qnty as attached_qnty
			FROM fabric_sales_order_mst a
			left join fabric_sales_order_dtls b on a.id=b.mst_id 
			left join com_export_pi_dtls c on c.WORK_ORDER_DTLS_ID=b.id and c.status_active=1
			left join com_export_pi_mst d on d.id=c.pi_id and d.status_active=1 and d.item_category_id=10       
			left join com_export_lc_order_info e on a.id=e.WO_PO_BREAK_DOWN_ID and e.status_active=1 and e.EXPORT_ITEM_CATEGORY=10
			left join com_export_lc f on f.id=e.COM_EXPORT_LC_ID and f.export_item_category=10
			WHERE a.id=b.mst_id and (c.WORK_ORDER_ID <> '' OR c.WORK_ORDER_ID IS NULL) and (e.wo_po_break_down_id <> '' OR e.wo_po_break_down_id IS NULL) and a.entry_form=109 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.company_id =$cbo_company_id $sql_order_no $where_con $date_con group by a.job_no, sales_booking_no, a.buyer_id, a.booking_date, a.customer_buyer ,a.currency_id, a.booking_approval_date, a.delivery_date, d.pi_number,d.pi_date, f.export_lc_no, f.lc_date, f.lc_value, e.attached_qnty";
		}
		elseif($report_type_all==2){
			$order_sql="SELECT a.job_no, sales_booking_no, a.buyer_id, a.booking_date, a.customer_buyer, a.currency_id,a.booking_approval_date, a.delivery_date, sum(b.finish_qty) as order_qty, sum(b.amount)/sum(b.finish_qty) as avg_rate, sum(b.amount) as order_value, d.pi_number,d.pi_date, sum(c.amount) as pi_amount, f.export_lc_no, f.lc_date, f.lc_value, e.attached_qnty as attached_qnty
			FROM fabric_sales_order_mst a
			left join fabric_sales_order_dtls b on a.id=b.mst_id 
			left join com_export_pi_dtls c on c.WORK_ORDER_DTLS_ID=b.id and c.status_active=1
			left join com_export_pi_mst d on d.id=c.pi_id and d.status_active=1  and d.item_category_id=10        
			left join com_export_lc_order_info e on a.id=e.WO_PO_BREAK_DOWN_ID and e.status_active=1 and e.EXPORT_ITEM_CATEGORY=10
			left join com_export_lc f on f.id=e.COM_EXPORT_LC_ID and f.export_item_category=10
			WHERE a.id=b.mst_id and (c.WORK_ORDER_ID <> '' OR c.WORK_ORDER_ID IS NULL) and a.entry_form=109 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.company_id =$cbo_company_id $sql_order_no $where_con $date_con group by a.job_no, sales_booking_no, a.buyer_id, a.booking_date, a.customer_buyer ,a.currency_id, a.booking_approval_date, a.delivery_date, d.pi_number,d.pi_date, f.export_lc_no, f.lc_date, f.lc_value, e.attached_qnty";
		}
		elseif($report_type_all==3){	

			$order_sql="SELECT a.job_no, sales_booking_no, a.buyer_id, a.booking_date, a.customer_buyer, a.currency_id,a.booking_approval_date, a.delivery_date, sum(b.finish_qty) as order_qty, sum(b.amount)/sum(b.finish_qty) as avg_rate, sum(b.amount) as order_value, d.pi_number,d.pi_date, sum(c.amount) as pi_amount, f.export_lc_no, f.lc_date, f.lc_value, e.attached_qnty as attached_qnty
			FROM fabric_sales_order_mst a
			left join fabric_sales_order_dtls b on a.id=b.mst_id 
			left join com_export_pi_dtls c on c.WORK_ORDER_DTLS_ID=b.id and c.status_active=1
			left join com_export_pi_mst d on d.id=c.pi_id and d.status_active=1 and d.item_category_id=10         
			left join com_export_lc_order_info e on a.id=e.WO_PO_BREAK_DOWN_ID and e.status_active=1 and e.EXPORT_ITEM_CATEGORY=10
			left join com_export_lc f on f.id=e.COM_EXPORT_LC_ID and f.export_item_category=10
			WHERE a.id=b.mst_id and (e.wo_po_break_down_id <> '' OR e.wo_po_break_down_id IS NULL) and a.entry_form=109 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.company_id =$cbo_company_id $sql_order_no $where_con $date_con group by a.job_no, sales_booking_no, a.buyer_id, a.booking_date, a.customer_buyer ,a.currency_id, a.booking_approval_date, a.delivery_date, d.pi_number,d.pi_date, f.export_lc_no, f.lc_date, f.lc_value, e.attached_qnty";	
		}
		elseif($report_type_all==4){
			$order_sql="SELECT a.job_no, sales_booking_no, a.buyer_id, a.booking_date, a.customer_buyer, a.currency_id,a.booking_approval_date, a.delivery_date, sum(b.finish_qty) as order_qty, sum(b.amount)/sum(b.finish_qty) as avg_rate, sum(b.amount) as order_value, d.pi_number,d.pi_date, sum(c.amount) as pi_amount, f.export_lc_no, f.lc_date, f.lc_value, e.attached_qnty as attached_qnty
			FROM fabric_sales_order_mst a
			left join fabric_sales_order_dtls b on a.id=b.mst_id 
			left join com_export_pi_dtls c on c.WORK_ORDER_DTLS_ID=b.id and c.status_active=1
			left join com_export_pi_mst d on d.id=c.pi_id and d.status_active=1 and d.item_category_id=10          
			left join com_export_lc_order_info e on a.id=e.WO_PO_BREAK_DOWN_ID and e.status_active=1 and e.EXPORT_ITEM_CATEGORY=10
			left join com_export_lc f on f.id=e.COM_EXPORT_LC_ID and f.export_item_category=10
			WHERE a.id=b.mst_id and e.WO_PO_BREAK_DOWN_ID is not null and c.WORK_ORDER_ID is not null and a.entry_form=109 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.company_id =$cbo_company_id $sql_order_no $where_con $date_con group by a.job_no, sales_booking_no, a.buyer_id, a.booking_date, a.customer_buyer ,a.currency_id, a.booking_approval_date, a.delivery_date, d.pi_number,d.pi_date, f.export_lc_no, f.lc_date, f.lc_value, e.attached_qnty";
		}
        else{
           $order_sql="SELECT a.job_no, sales_booking_no, a.buyer_id, a.booking_date, a.customer_buyer, a.currency_id,a.booking_approval_date, a.delivery_date, sum(b.finish_qty) as order_qty, sum(b.amount)/sum(b.finish_qty) as avg_rate, sum(b.amount) as order_value, d.pi_number,d.pi_date, sum(c.amount) as pi_amount, f.export_lc_no, f.lc_date, f.lc_value, e.attached_qnty as attached_qnty
           FROM fabric_sales_order_mst a
           left join fabric_sales_order_dtls b on a.id=b.mst_id 
		   left join com_export_pi_dtls c on c.WORK_ORDER_DTLS_ID=b.id and c.status_active=1 
           left join com_export_pi_mst d on d.id=c.pi_id and d.status_active=1 and d.item_category_id=10      
           left join com_export_lc_order_info e on a.id=e.WO_PO_BREAK_DOWN_ID and e.status_active=1 and e.EXPORT_ITEM_CATEGORY=10
           left join com_export_lc f on f.id=e.COM_EXPORT_LC_ID and f.export_item_category=10
           WHERE a.id=b.mst_id and a.entry_form=109 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.company_id =$cbo_company_id $sql_order_no $where_con $date_con group by a.job_no, sales_booking_no, a.buyer_id, a.booking_date, a.customer_buyer ,a.currency_id, a.booking_approval_date, a.delivery_date, d.pi_number,d.pi_date, f.export_lc_no, f.lc_date, f.lc_value, e.attached_qnty";
		}
		//  echo $order_sql;
		$order_sql_result = sql_select($order_sql);
		?>	
		<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:0;">
	    	<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="caption" align="center">
					<thead class="form_caption" >
						<tr>
							<td colspan="20" align="center" style="font-size:20px;"><? echo $companyArr[$cbo_company_id]; ?></td>
						</tr>
						<tr>
							<td colspan="20" align="center" style="font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
						</tr>
						<tr>
							<td colspan="20" align="center" style="font-size:14px; font-weight:bold">
								<? echo " From : ".change_date_format($txt_date_from) ." To : ". change_date_format($txt_date_to) ;?>
							</td>
						</tr>
					</thead>
				</table>
	            <table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
				<div style="text-align:center;" class="search_type"><? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --",4,"","","2,3,4" ); ?></div>
					<thead>
	                    <th width="35">SL</th>
	                    <th width="100">Fso No</th>
	                    <th width="100">Booking No</th>
	                    <th width="100">Buyer/Unit</th>
	                    <th width="100">Fso Booking Date</th>
	                    <th width="100">Cust Buyer</th>
	                    <th width="100">Receive Date</th>
	                    <th width="100">Delevery Date</th>
	                    <th width="100">Total Order Qty</th>
	                	<th width="100">Avg rate</th>
	                    <th width="100">Total Order Value</th>
	                    <th width="100">Currency</th>
	                    <th width="100">Export PI No</th>
	                    <th width="150">PI Date</th>
	                    <th width="100">PI Value</th>
	                    <th width="100">LC No</th>
	                    <th width="60">LC Date</th>
	                    <th width="60">LC Value</th>
	                    <th width="100">Attached Qty</th>
					</thead>
				</table>
	        <div style="width:<? echo $width+18;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
	        	<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="<? echo $width;?>" rules="all" align="left">
	            <? 
					$i=1;
					$total_order_qty=0;$total_order_val=0;$total_booked_qty=0;$total_production_qty=0;
					foreach($order_sql_result as $row)
					{
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="35"  align="center"><? echo $i;?></td>
							<td width="100" style="word-break: break-all;" align="center"><? echo $row[csf('job_no')];?></td>
							<td width="100" style="word-break: break-all;" align="center"><? echo $row[csf('sales_booking_no')];?></td>
							<td width="100" style="word-break: break-all;" align="center"><? echo $party=($row[csf('within_group')]==1)?$buyer_arr[$row[csf('buyer_id')]]:$buyer_arr[$row[csf('buyer_id')]];?></td>										
							<td width="100" style="word-break: break-all;" align="center"><? echo $row[csf('booking_date')];?></td>
							<td width="100" style="word-break: break-all;" align="center"><? echo  $buyer_arr[$row[csf('customer_buyer')]];?></td>
							<td width="100" style="word-break: break-all;" align="center"><? echo $row[csf('booking_approval_date')];?></td>
							<td width="100" style="word-break: break-all;" align="center"><? echo $row[csf('delivery_date')] ;?></td>
							<td width="100" style="word-break: break-all;" align="center"><?php echo $row[csf('order_qty')] ?></td>
							<td width="100" align="center" style="word-break: break-all;" align="left"><?php echo number_format($row[csf('avg_rate')],2) ?></td>
							<td width="100" align="center" style="word-break: break-all;" align="left"><? echo $row[csf('order_value')];?></td>
							<td width="100" align="center" style="word-break: break-all;" align="left"><? echo $currency[$row[csf('currency_id')]]?></td>
							<td width="100" align="center" style="word-break: break-all;" align="left" style="word-break:break-all;"><? echo $row[csf('pi_number')] ?></td>
							<td width="150" align="center" style="word-break: break-all;" align="left"><? echo $row[csf('pi_date')]?></td>
							<td width="100" align="center" style="word-break: break-all;" align="right"><? echo $row[csf('pi_amount')] ?></td>
							<td width="100" style="word-break: break-all;" align="center">&nbsp;<? echo $row[csf('export_lc_no')] ?></td>  
							<td width="60" style="word-break: break-all;" align="right"><? echo $row[csf('lc_date')] ?></td>
							<td width="60" style="word-break: break-all;" align="right"><? echo $row[csf('lc_value')]?></td>
							<td width="100" style="word-break: break-all;" align="right"><? echo $row[csf('attached_qnty')] ?></td>
						</tr>
						<?$i++;
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
	                <th width="100"></th>
	                <th width="100"></th>
	                <th width="100"></th>
	                <th width="100"></th>
	                <th width="100"></th>
	                <th width="100"></th>
	                <th width="100"></th>
	                <th width="100"></th>
	                <th width="150"></th>
	                <th width="100"></th>
	                <th width="100"></th>
	                <th width="60"></th>
	                <th width="60"> </th>
	                <th width="100"></th>
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

?>
<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');

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
							echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",$data[0] );
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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_order_no').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $type; ?>', 'create_po_search_list_view', 'search_div', 'grey_fabric_sample_to_order_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
	else 
		$shipment_date ="";
	
	$type=$data[5];
	$arr=array (2=>$company_arr,3=>$buyer_arr);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	if($type=="from") $status_cond=" and b.status_active in(1,3)"; else $status_cond=" and b.status_active=1";
	$sql= "select a.job_no, $year_field, a.job_no_prefix_num,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_id and a.buyer_name like '$buyer' and b.po_number like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $status_cond $shipment_date order by b.id, b.pub_shipment_date";  
	 
	echo create_list_view("tbl_list_search", "Job No,Year,Company,Buyer Name,Style Ref. No,Job Qty.,PO number,PO Quantity,Shipment Date", "70,60,70,80,120,90,110,90,80","850","200",0, $sql , "js_set_value", "id", "", 1, "0,0,company_name,buyer_name,0,0,0,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,job_quantity,po_number,po_quantity,shipment_date", "",'','0,0,0,0,0,1,0,1,3');
	
	exit();
}

if($action=='populate_data_from_order')
{
	$data=explode("**",$data);
	$po_id=$data[0];
	$which_order=$data[1];
	if($which_order=="from")
	{
		/*$data_array=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$po_id");
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
		}*/
		
		$style_name_array=return_library_array( "select id, style_ref_no from sample_development_mst",'id','style_ref_no');
		$data_array=sql_select("select a.id as booking_id, a.booking_no, a.booking_no_prefix_num, a.buyer_id, b.style_id, b.body_part, b.grey_fabric
		from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
		where a.booking_no=b.booking_no and a.id=$po_id");
		foreach ($data_array as $row)
		{ 
			
			echo "document.getElementById('txt_sam_book_no').value 			= '".$row[csf("booking_no")]."';\n";
			echo "document.getElementById('txt_sam_book_id').value 			= '".$row[csf("booking_id")]."';\n";
			echo "document.getElementById('txt_sam_booking_qnty').value 			= '".$row[csf("grey_fabric")]."';\n";
			echo "document.getElementById('cbo_to_buyer_name').value 			= '".$row[csf("buyer_id")]."';\n";
			echo "document.getElementById('txt_to_style_ref').value 			= '".$style_name_array[$row[csf("style_id")]]."';\n";
			echo "document.getElementById('cbo_garments_item').value 			= '".$row[csf("body_part")]."';\n";
			exit();
		}
	}
	else
	{
		$data_array=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$po_id");
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
	
	
}

if ($action=="sample_popup")
{
	echo load_html_head_contents("sample Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 
	<script>
		function js_set_value(data)
		{
			$('#sample_id').val(data);
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
                    <th>Booking No</th>
                    <th width="230">Booking Date Range</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
                        <input type="hidden" name="sample_id" id="sample_id" class="text_boxes" value="">
                    </th>
                </thead>
                <tr class="general">
                    <td>
						<?
							echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",$data[0] );
						?>
                    </td>
                    <td>
                        <input type="text" style="width:130px;" class="text_boxes" name="txt_sample_no" id="txt_sample_no" />
                    </td>
                    <td>
                    	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" readonly>
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" readonly>
                    </td>
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_sample_no').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_sample_search_list_view', 'search_div', 'grey_fabric_sample_to_order_transfer_controller', 'setFilterGrid(\'tbl_list_sample\',-1);')" style="width:100px;" />
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

if($action=='create_sample_search_list_view')
{
	$data=explode('_',$data);
	
	if ($data[0]==0) $buyer="%%"; else $buyer=$data[0];
	$search_string="%".trim($data[1])."%";
	$company_id=$data[2];
	
	if ($data[3]!="" &&  $data[4]!="") 
	{
		if($db_type==0)
		{
			$booking_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$booking_date = "and b.pub_shipment_date between '".change_date_format($data[3],'','',1)."' and '".change_date_format($data[4],'','',1)."'";
		}
	}
	else 
		$booking_date ="";
	
	$style_name_array=return_library_array( "select id, style_ref_no from sample_development_mst",'id','style_ref_no');
	$arr=array (2=>$company_arr,3=>$buyer_arr,4=>$style_name_array,5=>$body_part);
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	$sql= "select a.id as booking_id, a.booking_no, $year_field, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.booking_date, a.delivery_date, b.style_id, b.fabric_description, b.body_part
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
	where a.booking_no=b.booking_no and a.booking_type=4 and a.company_id=$company_id and a.buyer_id like '$buyer' and b.booking_no like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $booking_date order by a.id, b.id";
	
	 
	echo create_list_view("tbl_list_sample", "Booking No,Year,Company,Buyer Name,Style Ref. No,Garments Item,Style Description,Booking Date", "60,60,80,80,120,130,160","850","200",0, $sql , "js_set_value", "booking_id", "", 1, "0,0,company_id,buyer_id,style_id,body_part,0,0", $arr , "booking_no_prefix_num,year,company_id,buyer_id,style_id,body_part,fabric_description,booking_date", "",'','0,0,0,0,0,0,0,3');
	
	exit();
}




if($action=="load_drop_down_item_desc")
{
	$item_description=array();
	$sql="select a.id, a.product_name_details, 1 as type from product_details_master a,  inv_transaction b,  inv_receive_master c where a.id=b.prod_id and b.mst_id=c.id  and c.booking_id=$data and b.transaction_type=1 and c.receive_basis=2 and b.status_active=1 and b.is_deleted=0 and c.booking_without_order=1 group by a.id, a.product_name_details
	union all
	select a.id, a.product_name_details, 2 as type from product_details_master a,  inv_transaction b,  inv_receive_master c where a.id=b.prod_id and b.mst_id=c.id  and c.booking_id in (select m.id as id from inv_receive_master m where m.booking_id=$data and m.entry_form=2 and M.BOOKING_WITHOUT_ORDER=1) and b.transaction_type=1 and c.receive_basis=9 and b.status_active=1 and b.is_deleted=0 and c.booking_without_order=1 group by a.id, a.product_name_details";
	//echo $sql;die;
	$dataArray=sql_select($sql);	
	foreach($dataArray as $row)
	{
		$item_description[$row[csf('id')]]=$row[csf('product_name_details')];
	}
	echo create_drop_down( "cbo_item_desc", 368, $item_description,'', 1, "--Select Item Description--",'0','','1');  
	exit();
}

if($action=="populate_data_from_sample")
{
	$style_name_array=return_library_array( "select id, style_ref_no from sample_development_mst",'id','style_ref_no');
	$data_array=sql_select("select a.id as booking_id, a.booking_no, a.booking_no_prefix_num, a.buyer_id, b.style_id, b.body_part, b.grey_fabric
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
	where a.booking_no=b.booking_no and a.id=$data");
	foreach ($data_array as $row)
	{ 
		
		echo "document.getElementById('txt_sam_book_no').value 			= '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('txt_sam_book_id').value 			= '".$row[csf("booking_id")]."';\n";
		echo "document.getElementById('txt_sam_booking_qnty').value 			= '".$row[csf("grey_fabric")]."';\n";
		echo "document.getElementById('cbo_to_buyer_name').value 			= '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('txt_to_style_ref').value 			= '".$style_name_array[$row[csf("style_id")]]."';\n";
		echo "document.getElementById('cbo_garments_item').value 			= '".$row[csf("body_part")]."';\n";
		exit();
	}
}

if($action=="show_dtls_list_view")
{
	/*"select a.id, a.product_name_details, c.yarn_count, c.brand_id, c.yarn_lot, c.rack, c.self, c.stitch_length, d.receive_basis, d.booking_id, d.booking_no,d.entry_form 	 
			from 			
				product_details_master a, inv_transaction b, pro_grey_prod_entry_dtls c, inv_receive_master d
			where  
				a.id=b.prod_id and b.id=c.trans_id and c.mst_id=d.id and a.item_category_id=13 and d.entry_form in(2,22) and d.booking_id=$data  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  d.receive_basis=(CASE WHEN d.entry_form = 22 THEN 2 WHEN d.entry_form = 2 THEN 1 ELSE 0 END)
				group by a.id, a.product_name_details, d.receive_basis, d.booking_id, d.booking_no,d.entry_form , c.yarn_count, c.brand_id, c.yarn_lot, c.rack, c.self, c.stitch_length
				
			union all
			
			select a.id, a.product_name_details, c.yarn_count, c.brand_id, c.yarn_lot, c.rack, c.self, c.stitch_length, d.receive_basis, d.booking_id, d.booking_no,d.entry_form 	 
			from 			
				product_details_master a, inv_transaction b, pro_grey_prod_entry_dtls c, inv_receive_master d
			where  
				a.id=b.prod_id and b.id=c.trans_id and c.mst_id=d.id and a.item_category_id=13 and d.entry_form =22 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.receive_basis=1 and d.booking_id in(select p.id from com_pi_master_details p, com_pi_item_details q where p.id=q.pi_id and q.work_order_id=$data)
				group by a.id, a.product_name_details, d.receive_basis, d.booking_id, d.booking_no,d.entry_form , c.yarn_count, c.brand_id, c.yarn_lot, c.rack, c.self, c.stitch_length
				
			union all
			
			select a.id, a.product_name_details, c.yarn_count, c.brand_id, c.yarn_lot, c.rack, c.self, c.stitch_length, d.receive_basis, d.booking_id, d.booking_no,d.entry_form 	 
			from 			
				product_details_master a, inv_transaction b, pro_grey_prod_entry_dtls c, inv_receive_master d
			where  
				a.id=b.prod_id and b.id=c.trans_id and c.mst_id=d.id and a.item_category_id=13 and d.entry_form =22 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.receive_basis=9 and d.booking_id in(select p.id from inv_receive_master p where p.booking_id=$data and p.entry_form=2)
				group by a.id, a.product_name_details, d.receive_basis, d.booking_id, d.booking_no,d.entry_form , c.yarn_count, c.brand_id, c.yarn_lot, c.rack, c.self, c.stitch_length";	*/
	
	$sql = "select a.id, a.product_name_details, c.yarn_count, c.brand_id, c.yarn_lot, c.rack, c.self, c.stitch_length, d.receive_basis, d.booking_id, d.booking_no,d.entry_form, 1 as type	 
			from 			
				product_details_master a, inv_transaction b, pro_grey_prod_entry_dtls c, inv_receive_master d
			where  
				a.id=b.prod_id and b.id=c.trans_id and c.mst_id=d.id and a.item_category_id=13 and d.entry_form in(2,22) and d.booking_id=$data  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  d.receive_basis=(CASE WHEN d.entry_form = 22 THEN 2 WHEN d.entry_form = 2 THEN 1 ELSE 0 END)
				group by a.id, a.product_name_details, d.receive_basis, d.booking_id, d.booking_no,d.entry_form , c.yarn_count, c.brand_id, c.yarn_lot, c.rack, c.self, c.stitch_length
				
			union all
			
			select a.id, a.product_name_details, c.yarn_count, c.brand_id, c.yarn_lot, c.rack, c.self, c.stitch_length, d.receive_basis, d.booking_id, d.booking_no,d.entry_form, 2 as type 	 
			from 			
				product_details_master a, inv_transaction b, pro_grey_prod_entry_dtls c, inv_receive_master d
			where  
				a.id=b.prod_id and b.id=c.trans_id and c.mst_id=d.id and a.item_category_id=13 and d.entry_form =22 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.receive_basis=1 and d.booking_id in(select p.id from com_pi_master_details p, com_pi_item_details q where p.id=q.pi_id and q.work_order_id=$data)
				group by a.id, a.product_name_details, d.receive_basis, d.booking_id, d.booking_no,d.entry_form , c.yarn_count, c.brand_id, c.yarn_lot, c.rack, c.self, c.stitch_length
				
			union all
			
			select a.id, a.product_name_details, c.yarn_count, c.brand_id, c.yarn_lot, c.rack, c.self, c.stitch_length, d.receive_basis, d.booking_id, d.booking_no,d.entry_form, 3 as type	 
			from 			
				product_details_master a, inv_transaction b, pro_grey_prod_entry_dtls c, inv_receive_master d
			where  
				a.id=b.prod_id and b.id=c.trans_id and c.mst_id=d.id and a.item_category_id=13 and d.entry_form =22 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.receive_basis=9 and d.booking_id in(select p.id from inv_receive_master p where p.booking_id=$data and p.entry_form=2 and p.receive_basis=1 and p.booking_without_order=1)
				group by a.id, a.product_name_details, d.receive_basis, d.booking_id, d.booking_no,d.entry_form , c.yarn_count, c.brand_id, c.yarn_lot, c.rack, c.self, c.stitch_length";	
	
	//echo $sql;
	$booking_id=$data;
	$data_array=sql_select($sql);	
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');	
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');


	?>
    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="550">
        <thead>
            <th>Fabric Description</th>
            <th width="40">Y/count</th>
            <th width="40">Y/Brand</th>
            <th width="40">Y/Lot</th>
            <th width="45">Stitch Length</th>
            <th width="40">Rack</th>
            <th width="40">Shelf</th>
        </thead>
        <tbody>
            <? 
            $i=1;$booking_no="";
            foreach($data_array as $row)
            {  
                if ($i%2==0)  
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";
				$ycount='';
				$count_id=explode(',',$row[csf('yarn_count')]);
				foreach($count_id as $count)
				{
					if ($ycount=='') $ycount=$count_arr[$count]; else $ycount.=",".$count_arr[$count];
				}
				
				if($row[csf('entry_form')]==2)
				{
					if($row[csf('receive_basis')]==2)
					{
						$knit_palan_no=$row[csf('booking_no')]; 
					}
					else 
					{
						$knit_palan_no="";
					}
				}
				else
				{
					if($row[csf('receive_basis')]==9)
					{

						$book_sql=sql_select("select booking_id, booking_no from inv_receive_master where id='".$row[csf('booking_id')]."' and status_active=1 and is_deleted=0 and entry_form in(2)");
						$booking_no=$book_sql[0][csf("booking_no")];
						$booking_id=$book_sql[0][csf("booking_id")];
						$knit_palan_no=$book_sql[0][csf("booking_id")];

					}
					else
					{
						$booking_no=$row[csf('booking_no')];
						$booking_id=$row[csf('booking_id')];
					}
				}
					
             ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $row[csf('id')]."**".$ycount."**".$row[csf('yarn_count')]."**"
                                                                                   .$brand_arr[$row[csf('brand_id')]]."**".$row[csf('brand_id')]."**".$row[csf('yarn_lot')]."**".$row[csf('rack')]."**".$row[csf('self')]."**".$knit_palan_no."**".$row[csf('stitch_length')]."**".$booking_id."**".$row[csf('product_name_details')];?>")' style="cursor:pointer">
                    <td><p><? echo $row[csf('product_name_details')]; ?></p></td>
                    <td><p><? echo $ycount; ?>&nbsp;</p></td>
                    <td><p><? echo $brand_arr[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
                    <td><p><? echo $row[csf('yarn_lot')]; ?>&nbsp;</p></td>
                    <td><p><? echo $row[csf('stitch_length')]; ?>&nbsp;</p></td>
                    <td><p><? echo $row[csf('rack')]; ?>&nbsp;</p></td>
                    <td><p><? echo $row[csf('self')]; ?>&nbsp;</p></td>
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

if($action=="populate_stock_data")
{
	$data=explode("**",$data);
	$booking_id=$data[1];
	$prod_id=$data[0];
	$recv_id='';
	$recv_id='';
	if($db_type==0)
	{
		$recv_id=return_field_value("group_concat(distinct(id)) as id","inv_receive_master","booking_without_order=1 and booking_id=$booking_id and status_active=1 and is_deleted=0 and entry_form in(2)","id");
	}
	else
	{
		$recv_id=return_field_value("LISTAGG(id, ',') WITHIN GROUP (ORDER BY id) as id","inv_receive_master","booking_without_order=1 and booking_id=$booking_id and status_active=1 and is_deleted=0 and entry_form in(2)","id");
	}
	
	if($recv_id=="") $recv_id=0;
	$all_booking_id=$booking_id.",".$recv_id;
	$sql = "select sum(qnty) as qnty, sum(qnty2) as qnty2 from 
			(
				select sum(case when b.receive_basis!=9 and b.booking_id=$booking_id then c.grey_receive_qnty else 0 end ) as qnty, sum(case when b.receive_basis=9 and b.booking_id in($recv_id) then c.grey_receive_qnty else 0 end) as qnty2 from inv_receive_master b, pro_grey_prod_entry_dtls c where b.id=c.mst_id and b.entry_form in(2,22) and b.booking_without_order=1 and c.trans_id!=0 and b.status_active=1 and b.is_deleted=0 and b.booking_id in ($all_booking_id) and c.prod_id=$prod_id
				union all
				select sum(c.cons_quantity) as qnty, 0 as qnty2 from inv_receive_master b, inv_transaction c where b.id=c.mst_id and b.entry_form=51 and b.receive_purpose=8 and c.item_category=13 and c.transaction_type=4 and b.booking_id=$booking_id and b.status_active=1 and b.is_deleted=0 and c.prod_id=$prod_id
				union all
				select sum(c.transfer_qnty) as qnty, 0 as qnty2 from inv_item_transfer_mst b, inv_item_transfer_dtls c where b.id=c.mst_id and c.item_category=13 and b.transfer_criteria=6 and b.to_order_id=$booking_id and b.status_active=1 and b.is_deleted=0 and c.from_prod_id=$prod_id
			) product_details_master";
			
	$result=sql_select($sql);
	
	$iss_sql="select sum(qnty) as qnty from
			(
				select sum(c.issue_qnty) as qnty from inv_issue_master b, inv_grey_fabric_issue_dtls c where b.id=c.mst_id and b.item_category=13 and b.entry_form=16 and b.issue_purpose in(3,8,26,29,30,31) and b.issue_basis=1 and b.booking_id=$booking_id and c.prod_id=$prod_id and b.status_active=1 and b.is_deleted=0
				union all
				select sum(c.cons_quantity) as qnty from inv_issue_master b, inv_transaction c, inv_receive_master d where b.id=c.mst_id and b.received_id=d.id and ((d.booking_id=$booking_id and d.receive_basis!=9) or (d.booking_id in ($recv_id) and d.receive_basis=9)) and d.booking_without_order=1 and b.item_category=13 and b.entry_form=45 and c.transaction_type=3 and c.item_category=13 and c.prod_id=$prod_id 
				union all
				select sum(c.transfer_qnty) as qnty from inv_item_transfer_mst b, inv_item_transfer_dtls c where b.id=c.mst_id and b.transfer_criteria=7 and c.item_category=13 and b.from_order_id=$booking_id and b.status_active=1 and b.is_deleted=0 and c.from_prod_id=$prod_id
			) inv_issue_master";
	$result_iss=sql_select($iss_sql);
			
	$grey_fabric_recv=$result[0][csf('qnty')]+$result[0][csf('qnty2')];
	$grey_fabric_issued=$result_iss[0][csf('qnty')];
	$current_stock=$grey_fabric_recv-$grey_fabric_issued;
	echo "document.getElementById('txt_current_stock').value 			= '".$current_stock."';\n";
	exit;
	
}

if ($action=="orderToorderTransfer_popup")
{
	echo load_html_head_contents("Order To Order Transfer Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 
	<script>
		function js_set_value(data)
		{
			$('#transfer_id').val(data);
			parent.emailwindow.hide();
		}
    </script>
</head>
<body>
<div align="center" style="width:780px;">
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:760px;margin-left:10px">
        <legend>Enter search words</legend>
            <table cellpadding="0" cellspacing="0" width="550" class="rpt_table">
                <thead>
                    <th>Search By</th>
                    <th width="240" id="search_by_td_up">Please Enter Transfer ID</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
                        <input type="hidden" name="transfer_id" id="transfer_id" class="text_boxes" value="">
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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>, 'create_transfer_search_list_view', 'search_div', 'grey_fabric_sample_to_order_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                    </td>
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
	
	if($search_by==1)
		$search_field="transfer_system_id";	
	else
		$search_field="challan_no";
	
	if($db_type==0) $year_field="YEAR(insert_date) as year,"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year,";
	else $year_field="";//defined Later
	
 	$sql="select id, transfer_prefix_number, $year_field transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria, item_category from inv_item_transfer_mst where item_category=13 and company_id=$company_id and $search_field like '$search_string' and transfer_criteria=7 and status_active=1 and is_deleted=0";
	
	$arr=array(3=>$company_arr,5=>$item_transfer_criteria,6=>$item_category);

	echo  create_list_view("tbl_list_search", "Transfer ID,Year,Challan No,Company,Transfer Date,Transfer Criteria,Item Category", "80,70,100,110,90,130","760","250",0, $sql, "js_set_value", "id", "", 1, "0,0,0,company_id,0,transfer_criteria,item_category", $arr, "transfer_prefix_number,year,challan_no,company_id,transfer_date,transfer_criteria,item_category", '','','0,0,0,0,3,0,0');
	
	exit();
}

if($action=='populate_data_from_transfer_master')
{
	$data_array=sql_select("select transfer_system_id,challan_no, company_id, transfer_date, item_category, from_order_id, to_order_id from inv_item_transfer_mst where id='$data'");
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('update_id').value 					= '".$data."';\n";
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("transfer_system_id")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_transfer_date').value 			= '".change_date_format($row[csf("transfer_date")])."';\n";
		echo "get_php_form_data('".$row[csf("from_order_id")]."**from'".",'populate_data_from_order','requires/grey_fabric_sample_to_order_transfer_controller');\n";
		echo "get_php_form_data('".$row[csf("to_order_id")]."**to'".",'populate_data_from_order','requires/grey_fabric_sample_to_order_transfer_controller');\n";
		echo "$('#cbo_company_id').attr('disabled','disabled');\n";
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_yarn_transfer_entry',1,1);\n"; 
		exit();
	}
}

if($action=="show_transfer_listview")
{
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=13","id","product_name_details");
	
	//$sql="select id, from_prod_id, transfer_qnty, item_category, uom, to_rack as rack, to_shelf as shelf from inv_item_transfer_dtls where mst_id='$data' and status_active = '1' and is_deleted = '0'";
	
	$sql="select a.from_order_id, b.id, b.from_prod_id, b.transfer_qnty, b.item_category, b.uom, b.to_rack as rack, b.to_shelf as shelf from inv_item_transfer_mst a,  inv_item_transfer_dtls b where a.id=b. mst_id and a.id='$data' and b.status_active = '1' and b.is_deleted = '0'";
	
	$arr=array(0=>$item_category,1=>$product_arr,3=>$unit_of_measurement);
	 
	echo create_list_view("list_view", "Item Category,Item Description,Transfered Qnty,UOM, Rack, Shelf", "120,250,100,70,80","730","200",0, $sql, "get_php_form_data", "id,from_order_id,from_prod_id", "'populate_transfer_details_form_data'", 0, "item_category,from_prod_id,0,uom,0,0", $arr, "item_category,from_prod_id,transfer_qnty,uom,rack,shelf", "requires/grey_fabric_sample_to_order_transfer_controller",'','0,0,2,0,0,0');
	
	exit();
}

if($action=='populate_transfer_details_form_data')
{
	
	$data=explode("_",$data);
	$booking_id=$data[1];
	$prod_id=$data[2];
	$dtls_id=$data[0];
	$recv_id='';
	$recv_id='';
	if($db_type==0)
	{
		$recv_id=return_field_value("group_concat(distinct(id)) as id","inv_receive_master","booking_without_order=1 and booking_id=$booking_id and status_active=1 and is_deleted=0 and entry_form in(2)","id");
	}
	else
	{
		$recv_id=return_field_value("LISTAGG(id, ',') WITHIN GROUP (ORDER BY id) as id","inv_receive_master","booking_without_order=1 and booking_id=$booking_id and status_active=1 and is_deleted=0 and entry_form in(2)","id");
	}
	
	if($recv_id=="") $recv_id=0;
	$all_booking_id=$booking_id.",".$recv_id;
	$sql = "select sum(qnty) as qnty, sum(qnty2) as qnty2 from 
			(
				select sum(case when b.receive_basis!=9 and b.booking_id=$booking_id then c.grey_receive_qnty else 0 end ) as qnty, sum(case when b.receive_basis=9 and b.booking_id in($recv_id) then c.grey_receive_qnty else 0 end) as qnty2 from inv_receive_master b, pro_grey_prod_entry_dtls c where b.id=c.mst_id and b.entry_form in(2,22) and b.booking_without_order=1 and c.trans_id!=0 and b.status_active=1 and b.is_deleted=0 and b.booking_id in ($all_booking_id) and c.prod_id=$prod_id
				union all
				select sum(c.cons_quantity) as qnty, 0 as qnty2 from inv_receive_master b, inv_transaction c where b.id=c.mst_id and b.entry_form=51 and b.receive_purpose=8 and c.item_category=13 and c.transaction_type=4 and b.booking_id=$booking_id and b.status_active=1 and b.is_deleted=0 and c.prod_id=$prod_id
				union all
				select sum(c.transfer_qnty) as qnty, 0 as qnty2 from inv_item_transfer_mst b, inv_item_transfer_dtls c where b.id=c.mst_id and c.item_category=13 and b.transfer_criteria=6 and b.to_order_id=$booking_id and b.status_active=1 and b.is_deleted=0 and c.from_prod_id=$prod_id
			) product_details_master";
			
	$result=sql_select($sql);
	
	$iss_sql="select sum(qnty) as qnty from
			(
				select sum(c.issue_qnty) as qnty from inv_issue_master b, inv_grey_fabric_issue_dtls c where b.id=c.mst_id and b.item_category=13 and b.entry_form=16 and b.issue_purpose in(3,8,26,29,30,31) and b.issue_basis=1 and b.booking_id=$booking_id and c.prod_id=$prod_id and b.status_active=1 and b.is_deleted=0
				union all
				select sum(c.cons_quantity) as qnty from inv_issue_master b, inv_transaction c, inv_receive_master d where b.id=c.mst_id and b.received_id=d.id and ((d.booking_id=$booking_id and d.receive_basis!=9) or (d.booking_id in ($recv_id) and d.receive_basis=9)) and d.booking_without_order=1 and b.item_category=13 and b.entry_form=45 and c.transaction_type=3 and c.item_category=13 and c.prod_id=$prod_id 
				union all
				select sum(c.transfer_qnty) as qnty from inv_item_transfer_mst b, inv_item_transfer_dtls c where b.id=c.mst_id and b.transfer_criteria=7 and c.item_category=13 and b.from_order_id=$booking_id and b.status_active=1 and b.is_deleted=0 and c.from_prod_id=$prod_id
			) inv_issue_master";
	$result_iss=sql_select($iss_sql);
			
	$grey_fabric_recv=$result[0][csf('qnty')]+$result[0][csf('qnty2')];
	$grey_fabric_issued=$result_iss[0][csf('qnty')];
	$current_stock=$grey_fabric_recv-$grey_fabric_issued;
	
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');	
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');	
	$data_array=sql_select("select id, mst_id, from_prod_id, transfer_qnty, roll, item_category, uom, y_count, yarn_lot, brand_id, to_rack, to_shelf, rack, shelf,from_program,to_program,stitch_length from inv_item_transfer_dtls where id='$dtls_id'");
	foreach ($data_array as $row)
	{ 
		$ycount='';
		$count_id=explode(',',$row[csf('y_count')]);
		foreach($count_id as $count)
		{
			if ($ycount=='') $ycount=$count_arr[$count]; else $ycount.=",".$count_arr[$count];
		}
	
		echo "document.getElementById('update_dtls_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_item_desc').value 				= '".$row[csf("from_prod_id")]."';\n";
		echo "document.getElementById('txt_prod_id').value 				= '".$row[csf("from_prod_id")]."';\n";
		echo "document.getElementById('txt_transfer_qnty').value 			= '".$row[csf("transfer_qnty")]."';\n";
		echo "document.getElementById('previous_trans_qnty').value 			= '".$row[csf("transfer_qnty")]."';\n";
		$current_stock=$current_stock+$row[csf("transfer_qnty")];
		echo "document.getElementById('txt_current_stock').value 			= '".$current_stock."';\n";
		echo "document.getElementById('cbo_item_category').value 			= '".$row[csf("item_category")]."';\n";
		echo "document.getElementById('cbo_uom').value 						= '".$row[csf("uom")]."';\n";
		echo "document.getElementById('txt_roll').value 						= '".$row[csf("roll")]."';\n";
		echo "document.getElementById('txt_ycount').value 					= '".$ycount."';\n";
		echo "document.getElementById('hid_ycount').value 					= '".$row[csf("y_count")]."';\n";
		echo "document.getElementById('txt_ybrand').value 					= '".$brand_arr[$row[csf('brand_id')]]."';\n";
		echo "document.getElementById('hid_ybrand').value 					= '".$row[csf("brand_id")]."';\n";
		echo "document.getElementById('txt_ylot').value 					= '".$row[csf("yarn_lot")]."';\n";
		echo "document.getElementById('txt_torack').value 					= '".$row[csf("to_rack")]."';\n";
		echo "document.getElementById('txt_toshelf').value 					= '".$row[csf("to_shelf")]."';\n";
		echo "document.getElementById('txt_rack').value 					= '".$row[csf("rack")]."';\n";
		echo "document.getElementById('txt_shelf').value 					= '".$row[csf("shelf")]."';\n";
		echo "document.getElementById('txt_form_prog').value 				= '".$row[csf("from_program")]."';\n";
		echo "document.getElementById('txt_to_prog').value 					= '".$row[csf("to_program")]."';\n";
		echo "document.getElementById('stitch_length').value 				= '".$row[csf("stitch_length")]."';\n";
		
		//$sql_trans=sql_select("select id, transaction_type from inv_transaction where mst_id=".$row[csf('mst_id')]." and prod_id=".$row[csf('from_prod_id')]." and item_category=13 and transaction_type in(5,6) order by id asc");
		$sql_trans=sql_select("select b.id as trans_id, b.transaction_type  from  inv_item_transfer_dtls a, inv_transaction b where a.mst_id=b.mst_id and a.id=".$row[csf('id')]." and b.transaction_type in(5,6) order by b.transaction_type DESC");
		echo "document.getElementById('update_trans_issue_id').value 		= '".$sql_trans[0][csf("trans_id")]."';\n";
		echo "document.getElementById('update_trans_recv_id').value 		= '".$sql_trans[1][csf("trans_id")]."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_yarn_transfer_entry',1,1);\n"; 
		exit();
	}
}

if ($action=="orderInfo_popup")
{
	echo load_html_head_contents("Order Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 

</head>

<body>
<div align="center" style="width:770px;">
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:760px;margin-left:15px">
        <legend><? echo ucfirst($type); ?> Order Info</legend>
        	<br>
            <table cellpadding="0" cellspacing="0" width="100%">
                <tr bgcolor="#FFFFFF">
                    <td align="center"><? echo ucfirst($type); ?> Order No: <b><? echo $txt_order_no; ?></b></td>
                </tr>
            </table>
            <br>
            <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="750" align="center">
                <thead>
                    <th width="40">SL</th>
                    <th width="100">Required</th>
                    <?
					if($type=="from")
					{ 
					?>
                        <th width="100">Knitted</th>
                        <th width="100">Issue to dye</th>
                    	<th width="100">Issue Return</th>
                        <th width="100">Transfer Out</th>
                        <th width="100">Transfer In</th>
                        <th>Remaining</th>
                    <?
					}
					else
					{
					?>
                        <th width="80">Yrn. Issued</th>
                        <th width="80">Yrn. Issue Rtn</th>
                        <th width="80">Knitted</th>
                        <th width="90">Issue Rtn.</th>
                        <th width="100">Transf. Out</th>
                        <th width="100">Transf. In</th>
                        <th>Shortage</th>
                    <?	
					}
					?>
                    
                </thead>
                <?
					$req_qty=return_field_value("sum(b.grey_fab_qnty) as grey_req_qnty","wo_booking_mst a, wo_booking_dtls b","a.booking_no=b.booking_no and a.item_category in(2,13) and b.po_break_down_id=$txt_order_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1","grey_req_qnty");
					
					$sql="select 
								sum(CASE WHEN entry_form ='3' THEN quantity ELSE 0 END) AS issue_qnty,
								sum(CASE WHEN entry_form ='5' THEN quantity ELSE 0 END) AS dye_issue_qnty,
								sum(CASE WHEN entry_form ='9' THEN quantity ELSE 0 END) AS return_qnty,
								sum(CASE WHEN entry_form ='81' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_out_qnty,
								sum(CASE WHEN entry_form ='81' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_in_qnty,
								sum(CASE WHEN trans_id<>0 and entry_form in(2,22) THEN quantity ELSE 0 END) AS knit_qnty
							from order_wise_pro_details where po_breakdown_id=$txt_order_id and status_active=1 and is_deleted=0";
					$dataArray=sql_select($sql);
					$remaining=0; $shoratge=0;
				?>
                <tr bgcolor="#EFEFEF">
                    <td>1</td>
                    <td align="right"><? echo number_format($req_qty,2); ?>&nbsp;</td>
                    <?
					if($type=="from")
					{
						$remaining=$dataArray[0][csf('issue_qnty')]-$dataArray[0][csf('return_qnty')]-$dataArray[0][csf('transfer_out_qnty')]+$dataArray[0][csf('transfer_in_qnty')]-$dataArray[0][csf('knit_qnty')];
					?>
                        <td align="right"><? echo number_format($dataArray[0][csf('knit_qnty')],2); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($dataArray[0][csf('dye_issue_qnty')],2); ?></td>
                        <td align="right"><? echo number_format($dataArray[0][csf('return_qnty')],2); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($dataArray[0][csf('transfer_in_qnty')],2); ?></td>
                    	<td align="right"><? echo number_format($dataArray[0][csf('transfer_out_qnty')],2); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($remaining,2); ?>&nbsp;</td>
                    <?
					}
					else
					{
						$shoratge=$req_qty-$dataArray[0][csf('issue_qnty')]-$dataArray[0][csf('return_qnty')]+$dataArray[0][csf('transfer_out_qnty')]-$dataArray[0][csf('transfer_in_qnty')];
					?>
                        <td align="right"><? echo number_format($dataArray[0][csf('issue_qnty')],2); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($dataArray[0][csf('return_qnty')],2); ?></td>
                        <td align="right"><? echo number_format($dataArray[0][csf('knit_qnty')],2); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($dataArray[0][csf('return_qnty')],2); ?></td>
                    	<td align="right"><? echo number_format($dataArray[0][csf('transfer_in_qnty')],2); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($dataArray[0][csf('transfer_out_qnty')],2); ?>&nbsp;</td>
                    	<td align="right"><? echo number_format($shoratge,2); ?>&nbsp;</td>
                    <?	
					}

					?>
                </tr>
            </table>
            <table>
				<tr>
                    <td align="center" >
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="parent.emailwindow.hide();" style="width:100px" />
                    </td>
                </tr>
			</table>
		</fieldset>
	</form>
</div>    
</body>           
</html>
<?
exit();
}

//data save update delete here------------------------------//
if($action=="save_update_delete")
{	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$booking_id=str_replace("'","",$txt_sam_book_id); 
	$prod_id=str_replace("'","",$cbo_item_desc); 
	$trans_date=str_replace("'","",$txt_transfer_date); 
	
    $max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$cbo_item_desc and transaction_type in (1,4,5)", "max_date");
	$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
	$trans_date = date("Y-m-d", strtotime($trans_date));
	if ($trans_date < $max_recv_date) 
        {
            echo "20**Transfer Date Can not Be Less Than Last Receive Date Of This Lot";
            die;
	}
        
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$transfer_recv_num=''; $transfer_update_id='';
		$first_rcv_date=return_field_value("min(b.transaction_date) as transaction_date","inv_receive_master a, inv_transaction b","a.id=b.mst_id and b.transaction_type=1 and a.booking_id=$booking_id and b.prod_id=$prod_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1","transaction_date");
		$first_rcv_date=strtotime($first_rcv_date);
		$trans_date=strtotime($trans_date);
		if($trans_date<$first_rcv_date)
		{
			echo "40**Transfer Date Lower Then First Received Date";disconnect($con);die;
		}
		
		$recv_id='';
		$recv_id='';
		if($db_type==0)
		{
			$recv_id=return_field_value("group_concat(distinct(id)) as id","inv_receive_master","booking_without_order=1 and booking_id=$booking_id and status_active=1 and is_deleted=0 and entry_form in(2)","id");
		}
		else
		{
			$recv_id=return_field_value("LISTAGG(id, ',') WITHIN GROUP (ORDER BY id) as id","inv_receive_master","booking_without_order=1 and booking_id=$booking_id and status_active=1 and is_deleted=0 and entry_form in(2)","id");
		}
		
		if($recv_id=="") $recv_id=0;
		$all_booking_id=$booking_id.",".$recv_id;
		$sql = "select sum(qnty) as qnty, sum(qnty2) as qnty2 from 
				(
					select sum(case when b.receive_basis!=9 and b.booking_id=$booking_id then c.grey_receive_qnty else 0 end ) as qnty, sum(case when b.receive_basis=9 and b.booking_id in($recv_id) then c.grey_receive_qnty else 0 end) as qnty2 from inv_receive_master b, pro_grey_prod_entry_dtls c where b.id=c.mst_id and b.entry_form in(2,22) and b.booking_without_order=1 and c.trans_id!=0 and b.status_active=1 and b.is_deleted=0 and b.booking_id in ($all_booking_id) and c.prod_id=$prod_id
					union all
					select sum(c.cons_quantity) as qnty, 0 as qnty2 from inv_receive_master b, inv_transaction c where b.id=c.mst_id and b.entry_form=51 and b.receive_purpose=8 and c.item_category=13 and c.transaction_type=4 and b.booking_id=$booking_id and b.status_active=1 and b.is_deleted=0 and c.prod_id=$prod_id
					union all
					select sum(c.transfer_qnty) as qnty, 0 as qnty2 from inv_item_transfer_mst b, inv_item_transfer_dtls c where b.id=c.mst_id and c.item_category=13 and b.transfer_criteria=6 and b.to_order_id=$booking_id and b.status_active=1 and b.is_deleted=0 and c.from_prod_id=$prod_id
				) product_details_master";
				
		$result=sql_select($sql);
		
		$iss_sql="select sum(qnty) as qnty from
				(
					select sum(c.issue_qnty) as qnty from inv_issue_master b, inv_grey_fabric_issue_dtls c where b.id=c.mst_id and b.item_category=13 and b.entry_form=16 and b.issue_purpose in(3,8,26,29,30,31) and b.issue_basis=1 and b.booking_id=$booking_id and c.prod_id=$prod_id and b.status_active=1 and b.is_deleted=0
					union all
					select sum(c.cons_quantity) as qnty from inv_issue_master b, inv_transaction c, inv_receive_master d where b.id=c.mst_id and b.received_id=d.id and ((d.booking_id=$booking_id and d.receive_basis!=9) or (d.booking_id in ($recv_id) and d.receive_basis=9)) and d.booking_without_order=1 and b.item_category=13 and b.entry_form=45 and c.transaction_type=3 and c.item_category=13 and c.prod_id=$prod_id 
					union all
					select sum(c.transfer_qnty) as qnty from inv_item_transfer_mst b, inv_item_transfer_dtls c where b.id=c.mst_id and b.transfer_criteria=7 and c.item_category=13 and b.from_order_id=$booking_id and b.status_active=1 and b.is_deleted=0 and c.from_prod_id=$prod_id
				) inv_issue_master";
		$result_iss=sql_select($iss_sql);
				
		$grey_fabric_recv=$result[0][csf('qnty')]+$result[0][csf('qnty2')];
		$grey_fabric_issued=$result_iss[0][csf('qnty')];
		$current_stock=$grey_fabric_recv-$grey_fabric_issued;
		$trans_qnty=str_replace("'","",$txt_transfer_qnty);
		if($trans_qnty>$current_stock)
		{
			echo "5**Transfer Quantity Not Allow More Then Order Stock quantity";disconnect($con);die;
		}
		
		
		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
			
			//$new_transfer_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'GFSTOTE', date("Y",time()), 5, "select transfer_prefix, transfer_prefix_number from inv_item_transfer_mst where company_id=$cbo_company_id and transfer_criteria=7 and item_category=$cbo_item_category and $year_cond=".date('Y',time())." order by id desc ", "transfer_prefix", "transfer_prefix_number" ));
		 	
			//$id=return_next_id( "id", "inv_item_transfer_mst", 1 ) ;
			
			$id = return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst", $con);
                   			//print_r($id); die;
            $new_transfer_system_id = explode("*", return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst",$con,1,$cbo_company_id,'GFSTOTE',81,date("Y",time()),13 ));
					 
			$field_array="id, transfer_prefix, transfer_prefix_number, transfer_system_id, company_id, challan_no, transfer_date, entry_form, transfer_criteria, to_company, from_order_id, to_order_id, item_category, inserted_by, insert_date";
			
			$data_array="(".$id.",'".$new_transfer_system_id[1]."',".$new_transfer_system_id[2].",'".$new_transfer_system_id[0]."',".$cbo_company_id.",".$txt_challan_no.",".$txt_transfer_date.",81,7,0,".$txt_sam_book_id.",".$txt_to_order_id.",".$cbo_item_category.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			//echo "insert into inv_item_transfer_mst (".$field_array.") values ".$data_array;die;
			/*$rID=sql_insert("inv_item_transfer_mst",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;*/
			
			$transfer_recv_num=$new_transfer_system_id[0];
			$transfer_update_id=$id;
		}
		else
		{
			$field_array_update="challan_no*transfer_date*from_order_id*to_order_id*updated_by*update_date";
			$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$txt_sam_book_id."*".$txt_to_order_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			/*$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0; */
			
			$transfer_recv_num=str_replace("'","",$txt_system_id);
			$transfer_update_id=str_replace("'","",$update_id);
		}
		
		$rate=0; $amount=0;
		//$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
		$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		$field_array_trans="id, mst_id, company_id, prod_id, item_category, transaction_type, transaction_date, order_id, cons_uom, cons_quantity, cons_rate, cons_amount, rack, self, program_no, stitch_length, inserted_by, insert_date";
		
		$data_array_trans="(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",".$cbo_item_desc.",".$cbo_item_category.",6,".$txt_transfer_date.",".$txt_sam_book_id.",".$cbo_uom.",".$txt_transfer_qnty.",'".$rate."','".$amount."',".$txt_rack.",".$txt_shelf.",".$txt_form_prog.",".$stitch_length.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		//$id_trans_recv=$id_trans+1;
		$id_trans_recv=return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		$data_array_trans.=",(".$id_trans_recv.",".$transfer_update_id.",".$cbo_company_id.",".$cbo_item_desc.",".$cbo_item_category.",5,".$txt_transfer_date.",".$txt_to_order_id.",".$cbo_uom.",".$txt_transfer_qnty.",'".$rate."','".$amount."',".$txt_torack.",".$txt_toshelf.",".$txt_to_prog.",".$stitch_length.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		//echo "insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;
		/*$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} */
		//$id_dtls=return_next_id( "id", "inv_item_transfer_dtls", 1 ) ; 
		$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
		$field_array_dtls="id, mst_id, from_prod_id, item_category, transfer_qnty, roll, rate, transfer_value, uom, y_count, brand_id, yarn_lot, rack, shelf, to_rack, to_shelf, from_program, to_program, stitch_length, inserted_by, insert_date";
		
		$data_array_dtls="(".$id_dtls.",".$transfer_update_id.",".$cbo_item_desc.",".$cbo_item_category.",".$txt_transfer_qnty.",".$txt_roll.",'".$rate."','".$amount."',".$cbo_uom.",".$hid_ycount.",".$hid_ybrand.",".$txt_ylot.",".$txt_rack.",".$txt_shelf.",".$txt_torack.",".$txt_toshelf.",".$txt_form_prog.",".$txt_to_prog.",".$stitch_length.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		//echo "insert into inv_item_transfer_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		/*$rID3=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} */

		//$id_prop = return_next_id( "id", "order_wise_pro_details", 1 );
		$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, inserted_by, insert_date";
		
		/*$data_array_prop="(".$id_prop.",".$id_trans.",6,81,".$id_dtls.",".$txt_sam_book_id.",".$cbo_item_desc.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		$id_prop=$id_prop+1;*/
		
		$data_array_prop="(".$id_prop.",".$id_trans_recv.",5,81,".$id_dtls.",".$txt_to_order_id.",".$cbo_item_desc.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		if(str_replace("'","",$update_id)=="")
		{
		    //echo "10** insert into inv_item_transfer_mst ($field_array ) values $data_array";die;
			$rID=sql_insert("inv_item_transfer_mst",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;
		}
		else
		{
			$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0; 
		}
        //echo "10** insert into inv_transaction ($field_array_trans ) values $data_array_trans";die;
		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
		//echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;
		//echo "10**".$rID2;die;
		//echo "insert into inv_item_transfer_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID3=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} 
		//echo $flag;die;
		//echo "insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;
		$rID4=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1);
		if($flag==1) 
		{
			if($rID4) $flag=1; else $flag=0; 
		} 
		//echo $flag;die;
        //echo "10** $rID=$rID2=$rID3=$rID4";die;
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$transfer_update_id."**".$transfer_recv_num."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**"."&nbsp; sumon"."**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);  
				echo "0**".$transfer_update_id."**".$transfer_recv_num."**0";
			}
			else
			{
				oci_rollback($con);
				echo "5**0**"."&nbsp; fiq"."**0";
			}
		}
		disconnect($con);
		die;
	}	
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$first_rcv_date=return_field_value("min(b.transaction_date) as transaction_date","inv_receive_master a, inv_transaction b","a.id=b.mst_id and b.transaction_type=1 and a.booking_id=$booking_id and b.prod_id=$prod_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1","transaction_date");
		$first_rcv_date=strtotime($first_rcv_date);
		$trans_date=strtotime($trans_date);
		if($trans_date<$first_rcv_date)
		{
			echo "40**Transfer Date Lower Then First Received Date";disconnect($con);die;
		}
		
		$recv_id='';
		$recv_id='';
		if($db_type==0)
		{
			$recv_id=return_field_value("group_concat(distinct(id)) as id","inv_receive_master","booking_without_order=1 and booking_id=$booking_id and status_active=1 and is_deleted=0 and entry_form in(2)","id");
		}
		else
		{
			$recv_id=return_field_value("LISTAGG(id, ',') WITHIN GROUP (ORDER BY id) as id","inv_receive_master","booking_without_order=1 and booking_id=$booking_id and status_active=1 and is_deleted=0 and entry_form in(2)","id");
		}
		
		if($recv_id=="") $recv_id=0;
		$all_booking_id=$booking_id.",".$recv_id;
		$sql = "select sum(qnty) as qnty, sum(qnty2) as qnty2 from 
				(
					select sum(case when b.receive_basis!=9 and b.booking_id=$booking_id then c.grey_receive_qnty else 0 end ) as qnty, sum(case when b.receive_basis=9 and b.booking_id in($recv_id) then c.grey_receive_qnty else 0 end) as qnty2 from inv_receive_master b, pro_grey_prod_entry_dtls c where b.id=c.mst_id and b.entry_form in(2,22) and b.booking_without_order=1 and c.trans_id!=0 and b.status_active=1 and b.is_deleted=0 and b.booking_id in ($all_booking_id) and c.prod_id=$prod_id
					union all
					select sum(c.cons_quantity) as qnty, 0 as qnty2 from inv_receive_master b, inv_transaction c where b.id=c.mst_id and b.entry_form=51 and b.receive_purpose=8 and c.item_category=13 and c.transaction_type=4 and b.booking_id=$booking_id and b.status_active=1 and b.is_deleted=0 and c.prod_id=$prod_id
					union all
					select sum(c.transfer_qnty) as qnty, 0 as qnty2 from inv_item_transfer_mst b, inv_item_transfer_dtls c where b.id=c.mst_id and c.item_category=13 and b.transfer_criteria=6 and b.to_order_id=$booking_id and b.status_active=1 and b.is_deleted=0 and c.from_prod_id=$prod_id
				) product_details_master";
				
		$result=sql_select($sql);
		
		$iss_sql="select sum(qnty) as qnty from
				(
					select sum(c.issue_qnty) as qnty from inv_issue_master b, inv_grey_fabric_issue_dtls c where b.id=c.mst_id and b.item_category=13 and b.entry_form=16 and b.issue_purpose in(3,8,26,29,30,31) and b.issue_basis=1 and b.booking_id=$booking_id and c.prod_id=$prod_id and b.status_active=1 and b.is_deleted=0
					union all
					select sum(c.cons_quantity) as qnty from inv_issue_master b, inv_transaction c, inv_receive_master d where b.id=c.mst_id and b.received_id=d.id and ((d.booking_id=$booking_id and d.receive_basis!=9) or (d.booking_id in ($recv_id) and d.receive_basis=9)) and d.booking_without_order=1 and b.item_category=13 and b.entry_form=45 and c.transaction_type=3 and c.item_category=13 and c.prod_id=$prod_id 
					union all
					select sum(c.transfer_qnty) as qnty from inv_item_transfer_mst b, inv_item_transfer_dtls c where b.id=c.mst_id and b.transfer_criteria=7 and c.item_category=13 and b.from_order_id=$booking_id and b.status_active=1 and b.is_deleted=0 and c.from_prod_id=$prod_id
				) inv_issue_master";
		$result_iss=sql_select($iss_sql);
				
		$grey_fabric_recv=$result[0][csf('qnty')]+$result[0][csf('qnty2')];
		$grey_fabric_issued=$result_iss[0][csf('qnty')];
		$current_stock=$grey_fabric_recv-$grey_fabric_issued;
		$trans_qnty=str_replace("'","",$txt_transfer_qnty);
		$previous_trans_qnty=str_replace("'","",$previous_trans_qnty);
		$current_stock=$current_stock+$previous_trans_qnty;
		if($trans_qnty>$current_stock)
		{
			echo "5**Transfer Quantity Not Allow More Then Order Stock";disconnect($con);die;
		}

        /**
         * List of fields that will not change/update on update button event
         * fields=> from_order_id*to_order_id*
         * data=> $txt_sam_book_id."*".$txt_to_order_id."*".
         */
		$field_array_update="challan_no*transfer_date*updated_by*update_date";
		$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		/*$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0;*/
		
		$field_array_trans="prod_id*transaction_date*order_id*cons_uom*cons_quantity*cons_rate*cons_amount*rack*self*program_no*stitch_length*updated_by*update_date";
		$updateTransID_array=array();
		$update_trans_issue_id=str_replace("'","",$update_trans_issue_id);
		$update_trans_recv_id=str_replace("'","",$update_trans_recv_id); 
		 
		$rate=0; $amount=0;
		$updateTransID_array[]=$update_trans_issue_id; 
		$updateTransID_data[$update_trans_issue_id]=explode("*",("".$cbo_item_desc."*".$txt_transfer_date."*".$txt_sam_book_id."*".$cbo_uom."*".$txt_transfer_qnty."*'".$rate."'*'".$amount."'*".$txt_rack."*".$txt_shelf."*".$txt_form_prog."*".$stitch_length."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		
		$updateTransID_array[]=$update_trans_recv_id; 
		$updateTransID_data[$update_trans_recv_id]=explode("*",("".$cbo_item_desc."*".$txt_transfer_date."*".$txt_to_order_id."*".$cbo_uom."*".$txt_transfer_qnty."*'".$rate."'*'".$amount."'*".$txt_torack."*".$txt_toshelf."*".$txt_to_prog."*".$stitch_length."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		
		/*$rID2=execute_query(bulk_update_sql_statement("inv_transaction","id",$field_array_trans,$updateTransID_data,$updateTransID_array));
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		}*/
		
		$field_array_dtls="from_prod_id*transfer_qnty*roll*rate*transfer_value*uom*y_count*brand_id*yarn_lot*rack*shelf*to_rack*to_shelf*from_program*to_program*stitch_length*updated_by*update_date";
		$data_array_dtls=$cbo_item_desc."*".$txt_transfer_qnty."*".$txt_roll."*'".$rate."'*'".$amount."'*".$cbo_uom."*".$hid_ycount."*".$hid_ybrand."*".$txt_ylot."*".$txt_rack."*".$txt_shelf."*".$txt_torack."*".$txt_toshelf."*".$txt_form_prog."*".$txt_to_prog."*".$stitch_length."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		/*$rID3=sql_update("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,"id",$update_dtls_id,1);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		}
		$query = execute_query("DELETE FROM order_wise_pro_details WHERE dtls_id=$update_dtls_id and entry_form=13");
		{
			if($query) $flag=1; else $flag=0; 
		} */
		
		//$id_prop = return_next_id( "id", "order_wise_pro_details", 1 );
		$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, inserted_by, insert_date";
		
		/*$data_array_prop="(".$id_prop.",".$update_trans_issue_id.",6,81,".$update_dtls_id.",".$txt_sam_book_id.",".$cbo_item_desc.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		$id_prop=$id_prop+1;*/
		
		$data_array_prop="(".$id_prop.",".$update_trans_recv_id.",5,81,".$update_dtls_id.",".$txt_to_order_id.",".$cbo_item_desc.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
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
		$query = execute_query("DELETE FROM order_wise_pro_details WHERE dtls_id=$update_dtls_id and entry_form=81");
		{
			if($query) $flag=1; else $flag=0; 
		} 
		//echo "insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;
		$rID4=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1);
		if($flag==1) 
		{
			if($rID4) $flag=1; else $flag=0; 
		} 
		//echo "5**$rID**$rID2**$rID3**$query**$rID4";die;
		//echo $flag;die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id)."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**0**"."&nbsp;"."**1";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);   
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id)."**0";
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
}

if ($action=="grey_fabric_order_to_order_transfer_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	
	$sql="select id, transfer_system_id, transfer_date, challan_no, from_order_id, to_order_id, item_category from inv_item_transfer_mst a where id='$data[1]' and company_id='$data[0]'";
	//echo $sql;die;
	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$job_arr = return_library_array("select id, job_no from wo_po_details_master","id","job_no");
	$po_arr = return_library_array("select id, po_number from wo_po_break_down","id","po_number");
	$qnty_arr = return_library_array("select id, po_quantity from wo_po_break_down","id","po_quantity");
	$buyer_arr = return_library_array("select id, buyer_name from wo_po_details_master","id","buyer_name");
	$style_arr = return_library_array("select id, style_ref_no from wo_po_details_master","id","style_ref_no");
	$ship_date_arr = return_library_array("select id, pub_shipment_date from wo_po_break_down","id","pub_shipment_date");
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=13","id","product_name_details");
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
            <td><strong>From order No:</strong></td> <td width="175px"><? echo $po_arr[$dataArray[0][csf('from_order_id')]]; ?></td>
            <td><strong>From ord Qnty:</strong></td> <td width="175px"><? echo $qnty_arr[$dataArray[0][csf('from_order_id')]]; ?></td>
            <td><strong>From ord Buyer:</strong></td> <td width="175px"><? echo $buyer_library[$buyer_arr[$dataArray[0][csf('from_order_id')]]]; ?></td>
        </tr>
        <tr>
            <td><strong>From Style Ref.:</strong></td> <td width="175px"><? echo $style_arr[$dataArray[0][csf('from_order_id')]]; ?></td>
            <td><strong>From Job No:</strong></td> <td width="175px"><? echo $job_arr[$dataArray[0][csf('from_order_id')]]; ?></td>
            <td><strong>From Ship. Date:</strong></td> <td width="175px"><? echo change_date_format($ship_date_arr[$dataArray[0][csf('from_order_id')]]); ?></td>
        </tr>
        <tr>
            <td><strong>To order No:</strong></td> <td width="175px"><? echo $po_arr[$dataArray[0][csf('to_order_id')]]; ?></td>
            <td><strong>To ord Qnty:</strong></td> <td width="175px"><? echo $qnty_arr[$dataArray[0][csf('to_order_id')]]; ?></td>
            <td><strong>To ord Buyer:</strong></td> <td width="175px"><? echo $buyer_library[$buyer_arr[$dataArray[0][csf('to_order_id')]]]; ?></td>
        </tr>
        <tr>
            <td><strong>To Style Ref.:</strong></td> <td width="175px"><? echo $style_arr[$dataArray[0][csf('to_order_id')]]; ?></td>
            <td><strong>To Job No:</strong></td> <td width="175px"><? echo $job_arr[$dataArray[0][csf('to_order_id')]]; ?></td>
            <td><strong>To Ship. Date:</strong></td> <td width="175px"><? echo change_date_format($ship_date_arr[$dataArray[0][csf('to_order_id')]]); ?></td>
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
                <td align="right"><? echo $row[csf("transfer_qnty")]; ?></td>
			</tr>
			<? $i++; } ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" align="right"><strong>Total :</strong></td>
                <td align="right"><?php echo $transfer_qnty_sum; ?></td>
            </tr>                           
        </tfoot>
      </table>
        <br>
		 <?
            echo signature_table(19, $data[0], "900px");
         ?>
      </div>
   </div>   
 <?	
 exit();
}
?>

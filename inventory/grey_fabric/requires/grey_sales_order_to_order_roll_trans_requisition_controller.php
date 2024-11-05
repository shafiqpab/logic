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

	if ($action == "load_drop_down_buyer") 
	{
		$data = explode("_", $data);
		$with_in_group=$data[0];
		$company_id = $data[1];

		if ($company_id == 0)
		{
			echo create_drop_down("cbo_buyer_name", 150, $blank_array, "", 1, "--Select Buyer--", 0, "");
		} 
		else 
		{
			if ($with_in_group== 1) 
			{
				echo create_drop_down("cbo_buyer_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--Select Buyer--", "0", "", "");
			} 
			else if ($with_in_group== 2) 
			{
				echo create_drop_down("cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by  buy.id, buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "--Select Buyer--", $selected, "", 0);
			}
			else
			{
				echo create_drop_down("cbo_buyer_name", 150, $blank_array, "", 1, "--Select Buyer--", 0, "");
			}
		}
		
		exit();
	}

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
		<div align="center" style="width:750px;">
			<form name="searchdescfrm"  id="searchdescfrm">
				<fieldset style="width:750px;">
					<table cellpadding="0" cellspacing="0" width="750" class="rpt_table" border="1" rules="all">
						<thead>
							<th>Within Group</th>
							<th>Buyer Name</th>
							<th>Sales Order No</th>
							<th width="170">Delivery Date Range</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:80px;" class="formbutton" />
								<input type="hidden" name="order_id" id="order_id" class="text_boxes" value="">
							</th>
						</thead>
						<tr class="general">
							<td>
								<?
								echo create_drop_down("cbo_within_group", 70, $yes_no, "", 1, "--Select--", 0, "load_drop_down( 'grey_sales_order_to_order_roll_trans_requisition_controller', this.value+'_'+".$cbo_company_id.", 'load_drop_down_buyer', 'buyer_td' );");
								?>
							</td>
							<td id="buyer_td"><? echo create_drop_down("cbo_buyer_name", 150, $blank_array, "", 1, "--Select Buyer--", 0, ""); ?></td>
							<td>
								<input type="text" style="width:80px;" class="text_boxes" name="txt_order_no" id="txt_order_no" placeholder="Enter Order No" />
							</td>
							<td>
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" readonly>
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date" readonly>
							</td>
							<td>
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_order_no').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $type; ?>'+'_'+document.getElementById('cbo_within_group').value+'_'+'<? echo $txt_from_order_id; ?>', 'create_po_search_list_view', 'search_div', 'grey_sales_order_to_order_roll_trans_requisition_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:80px;" />
							</td>
						</tr>
						<tr>
							<td colspan="7" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
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
	$company_id=$data[2];
	$fromOrderId=$data[7];
	
	//$po_buyer_arr=return_library_array( "select id, buyer_id from wo_booking_mst",'id','buyer_id');
	//$sales_buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	//$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	
	if ($data[3]!="" &&  $data[4]!="") 
	{
		if($db_type==0)
		{
			$delivery_date_cond = "and a.delivery_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$delivery_date_cond = "and a.delivery_date between '".change_date_format($data[3],'','',1)."' and '".change_date_format($data[4],'','',1)."'";
		}
	}
	else 
		$delivery_date_cond ="";
	
	$type=$data[5];
	$arr=array(2=>$company_arr);//2=>$company_arr,
	
	$with_in_group=$data[6];
	if($type=="to") { $orderIdOmitCond = "and a.id not in($fromOrderId)";}
	if($data[0]==0) $buyer_cond=""; else $buyer_cond="and a.buyer_id='$data[0]'";
	if($data[1]!="") $po_cond="and a.job_no_prefix_num='$data[1]'"; else $po_cond="";
	if($with_in_group!=0) $within_group_cond="and a.within_group='$with_in_group'"; else $within_group_cond="";
	
	if($type=="from") $status_cond=" and b.status_active in(1,3)"; else $status_cond=" and b.status_active=1";
	?>
	<div style="width:100%;">
		<table cellspacing="0" border="1" cellpadding="0" rules="all" width="750" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="50">Sales Order No</th>
				<th width="50">Year</th>
				<th width="60">With in Group</th>
				<th width="110">Sales Order Buyer</th>
				<th width="110">Booking No</th>
				<th width="100">PO Buyer</th>
				<th width="100">Style Ref.</th>
				<th width="70">PO Qty</th>
				<th>Delivery Date</th>
			</thead>
		</table>
	</div>
	<div style="width:750;max-height:180px; overflow-y:scroll" id="sewing_production_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="730" class="rpt_table" id="tbl_list_search">
			<?
			if($db_type==0)
			{
				$sql= "select a.id, a.job_no, a.job_no_prefix_num, YEAR(a.insert_date) as year, a.delivery_date, a.style_ref_no, a.buyer_id, a.booking_id, a.sales_booking_no,a.po_buyer, a.within_group, group_concat(b.item_number_id) as item_number_id, sum(b.grey_qty) as order_qty from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $buyer_cond $po_cond $delivery_date_cond $orderIdOmitCond $within_group_cond group by a.id, a.job_no, a.job_no_prefix_num, a.insert_date, a.delivery_date, a.style_ref_no, a.buyer_id, a.booking_id, a.sales_booking_no,a.po_buyer, a.within_group order by a.id DESC ";
			}
			else if($db_type==2)
			{
				$sql= "select a.id, a.job_no, a.job_no_prefix_num, to_char(a.insert_date,'YYYY') as year, a.delivery_date, a.style_ref_no, a.buyer_id, a.booking_id, a.sales_booking_no,a.po_buyer, a.within_group, listagg(b.item_number_id,',') within group (order by b.item_number_id) as item_number_id, sum(b.grey_qty) as order_qty from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $buyer_cond $po_cond $delivery_date_cond $orderIdOmitCond $within_group_cond group by a.id, a.job_no, a.job_no_prefix_num, a.insert_date, a.delivery_date, a.style_ref_no, a.buyer_id, a.booking_id, a.sales_booking_no,a.po_buyer, a.within_group order by a.id DESC ";
			}
        // echo  $sql; die;
			$i=1; $sql_result=sql_select($sql);
			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($row[csf('within_group')]==1) $buyer_name=$company_arr[$row[csf('buyer_id')]];
				else if($row[csf('within_group')]==2) $buyer_name=$buyer_arr[$row[csf('buyer_id')]];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]; ?>');" > 
					<td width="30"><? echo $i; ?></td>
					<td width="50"><? echo $row[csf('job_no_prefix_num')]; ?></td>
					<td width="50"><? echo $row[csf('year')]; ?></td>
					<td width="60"><? echo $yes_no[$row[csf('within_group')]]; ?></td>
					<td width="110"><? echo $buyer_name; ?></td>
					<td width="110"><? echo $row[csf('sales_booking_no')]; ?></td>
					<td width="100"><? echo $buyer_arr[$row[csf('po_buyer')]]; ?></td>
					<td width="100"><? echo $row[csf('style_ref_no')]; ?></td>
					<td width="70" align="right"><? echo number_format($row[csf('order_qty')],2); ?></td>
					<td><? echo change_date_format($row[csf('delivery_date')]); ?></td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
	</div>
	<?
	exit();
}

if($action=='populate_data_from_order')
{
	$data=explode("**",$data);
	$po_id=$data[0];
	$which_order=$data[1];
	//$po_buyer_arr=return_library_array( "select id, buyer_id from wo_booking_mst",'id','buyer_id');
	//$po_comp_arr=return_library_array( "select id, company_id from wo_booking_mst",'id','company_id');
	//$data_array=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$po_id");
	
	$data_array= sql_select("SELECT a.job_no, a.job_no_prefix_num, a.company_id, a.style_ref_no, a.sales_booking_no,a.po_company_id,a.po_buyer, a.buyer_id, a.booking_id, listagg(b.item_number_id,',') within group (order by b.item_number_id) as item_number_id 
	from fabric_sales_order_mst a, fabric_sales_order_dtls b 
	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id='$po_id' group by a.job_no, a.job_no_prefix_num, a.company_id, a.style_ref_no, a.sales_booking_no,a.po_company_id,a.po_buyer, a.buyer_id, a.booking_id");
	//echo "select a.job_no, a.job_no_prefix_num, a.delivery_date, a.style_ref_no, a.buyer_id, listagg(b.item_number_id,',') within group (order by b.item_number_id) as item_number_id, sum(b.grey_qty) as order_qty from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id='$po_id' group by a.job_no, a.job_no_prefix_num, a.delivery_date, a.style_ref_no, a.buyer_id";
	
	foreach ($data_array as $row)
	{ 
		$gmts_item_id=array_unique(explode(",",$row[csf('item_number_id')]));
		foreach($gmts_item_id as $item_id)
		{
			if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
		}
		
		echo "document.getElementById('txt_".$which_order."_order_id').value 			= '".$po_id."';\n";
		echo "document.getElementById('txt_".$which_order."_order_no').value 			= '".$row[csf("job_no_prefix_num")]."';\n";
		echo "document.getElementById('txt_".$which_order."_booking_no').value 			= '".$row[csf("sales_booking_no")]."';\n";
		echo "document.getElementById('cbo_".$which_order."_company').value 			= '".$row[csf("po_company_id")]."';\n";
		echo "document.getElementById('cbo_".$which_order."_buyer_name').value 			= '".$row[csf("po_buyer")]."';\n";
		echo "document.getElementById('txt_".$which_order."_style_ref').value 			= '".$row[csf("style_ref_no")]."';\n";
		//echo "document.getElementById('txt_".$which_order."_job_no').value 				= '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('txt_".$which_order."_gmts_item').value 			= '".$gmts_item."';\n";
		//echo "document.getElementById('txt_".$which_order."_shipment_date').value 		= '".change_date_format($row[csf("delivery_date")])."';\n";
		exit();
	}
}

if($action=="show_dtls_list_view")
{
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');	
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');	
	$product_arr=return_library_array( "select id, product_name_details from product_details_master where item_category_id=13",'id','product_name_details');
	
	//$trans_arr=return_library_array("select barcode_no, mst_id from pro_roll_details where entry_form=2", "barcode_no", "mst_id");
	
	//$issued_barcode_arr=return_library_array("select barcode_no from pro_roll_details where entry_form=61 and status_active=1 and is_deleted=0 and po_breakdown_id=$data and is_returned !=1 ","barcode_no", "barcode_no");
	
	$programArr=return_library_array("SELECT a.id, a.booking_id from inv_receive_master a, pro_roll_details b where a.id=b.mst_id and a.entry_form=2 and a.receive_basis=2 and b.entry_form=2 and b.po_breakdown_id=$data and b.status_active=1 and b.is_deleted=0", "id", "booking_id");
	
	$sql="SELECT a.id, a.entry_form, a.receive_basis, a.booking_id, b.prod_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 1 as type,c.booking_no,a.store_id
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58,84) and c.entry_form in(2,22,58,84) and c.is_transfer!=6 and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=$data and c.barcode_no not in(select barcode_no from inv_item_transfer_requ_dtls where entry_form=352 and from_order_id=$data and status_active=1 and is_deleted=0) and c.is_sales=1 and c.is_service=0
	UNION ALL
	SELECT a.id, a.entry_form, 0 as receive_basis, 0 as booking_id, b.from_prod_id as prod_id, b.yarn_lot, b.y_count as yarn_count, b.stitch_length, b.brand_id, b.to_shelf as self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 2 as type, c.booking_no, b.to_store as store_id
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(133) and c.entry_form in(133) and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=$data and c.po_breakdown_id not in(select from_order_id from inv_item_transfer_requ_dtls where entry_form=133 and status_active=1 and is_deleted=0) and c.is_service=0
	order by barcode_no"; 
	// and c.po_breakdown_id not in(select from_order_id from inv_item_transfer_requ_dtls where entry_form=352 and status_active=1 and is_deleted=0)
	//echo $sql;
	$data_array=sql_select($sql);	

	foreach($data_array as $row)
	{ 
		$ref_barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
	}

    $ref_barcode_arr = array_filter($ref_barcode_arr);
    if(count($ref_barcode_arr)>0)
    {
	    $all_ref_barcode_nos = implode(",", $ref_barcode_arr);
	    $all_ref_barcode_no=""; $barCond=""; 
	    $all_ref_barcode_no_1=""; $barCond_1=""; 
	    if($db_type==2 && count($ref_barcode_arr)>999)
	    {
	    	$ref_barcode_arr_chunk=array_chunk($ref_barcode_arr,999) ;
	    	foreach($ref_barcode_arr_chunk as $chunk_arr)
	    	{
	    		$chunk_arr_value=implode(",",$chunk_arr);	
	    		$barCond.="  barcode_no in($chunk_arr_value) or ";	
	    		$barCond_1.="  barcode_num in($chunk_arr_value) or ";	
	    	}

	    	$all_ref_barcode_no.=" and (".chop($barCond,'or ').")";	
	    	$all_ref_barcode_no_1.=" and (".chop($barCond_1,'or ').")";	
	    }
	    else
	    {
	    	$all_ref_barcode_no=" and barcode_no in($all_ref_barcode_nos)";	 
	    	$all_ref_barcode_no_1=" and barcode_num in($all_ref_barcode_nos)";	 
	    }

	    $issued_barcode_arr=return_library_array("select barcode_no from pro_roll_details where entry_form=61 and status_active=1 and is_deleted=0 $all_ref_barcode_no and is_returned !=1 ","barcode_no", "barcode_no");

	    $delv_arr=return_library_array("select barcode_num, grey_sys_id from pro_grey_prod_delivery_dtls where entry_form=56 $all_ref_barcode_no_1", "barcode_num", "grey_sys_id");
	    //echo "select barcode_num, grey_sys_id from pro_grey_prod_delivery_dtls where entry_form=56 $all_ref_barcode_no_1";
	}


	$i=1;
	foreach($data_array as $row)
	{  
		if($issued_barcode_arr[$row[csf('barcode_no')]]=="")
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			
			$ycount='';
			$count_id=explode(',',$row[csf('yarn_count')]);
			foreach($count_id as $count)
			{
				if ($ycount=='') $ycount=$count_arr[$count]; else $ycount.=",".$count_arr[$count];
			}
			
			$transRollId=$row[csf('roll_id')];
			$program_no='';
			if($row[csf('entry_form')]==2)
			{
				if($row[csf('receive_basis')]==2) $program_no=$row[csf('booking_id')];
			}
			else if($row[csf('entry_form')]==58)
			{
				$program_no=$programArr[$delv_arr[$row[csf('barcode_no')]]];
				$row[csf('roll_id')]=$row[csf('roll_id_prev')];
			}
			else if($row[csf('entry_form')]==133)
			{
				$program_no=$row[csf('booking_no')];
				$row[csf('roll_id')]=$row[csf('roll_id_prev')];
			}
			else if($row[csf('entry_form')]==22)
			{
				$program_no=$row[csf('booking_no')];
			}
			
			?>
			<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
				<td width="40" align="center" valign="middle"><input type="checkbox" id="tbl_<? echo $i;?>" name="check[]" onClick="show_selected_total('<? echo $i;?>')"/></td> 
				<td width="40"><? echo $i; ?></td>
				<td width="80"><? echo $row[csf('barcode_no')]; ?></td>
				<td width="50"><? echo $row[csf('roll_no')]; ?></td>
				<td width="70"><p><? echo $program_no; ?>&nbsp;</p></td>
				<td width="60"><p><? echo $row[csf('prod_id')]; ?></p></td>
				<td width="180"><p><? echo $product_arr[$row[csf('prod_id')]]; ?>&nbsp;</p></td>
				<td width="80"><p><? echo $ycount; ?>&nbsp;</p></td>
				<td width="70"><p><? echo $brand_arr[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
				<td width="80"><p><? echo $row[csf('yarn_lot')]; ?>&nbsp;</p></td>
				<td width="55"><p><? echo $row[csf('rack')]; ?>&nbsp;</p></td>
				<td width="55"><p><? echo $row[csf('self')]; ?>&nbsp;</p></td>
				<td width="80"><p><? echo $row[csf('stitch_length')]; ?>&nbsp;</p></td>
				<td width="80" align="right" style="padding-right:2px"><? echo number_format($row[csf('qnty')],2); ?>
					<input type="hidden" name="barcodeNo[]" id="barcodeNo_<? echo $i; ?>" value="<? echo $row[csf('barcode_no')]; ?>"/>
					<input type="hidden" name="rollNo[]" id="rollNo_<? echo $i; ?>" value="<? echo $row[csf('roll_no')]; ?>"/>
					<input type="hidden" name="progId[]" id="progId_<? echo $i; ?>" value="<? echo $program_no; ?>"/>
					<input type="hidden" name="productId[]" id="productId_<? echo $i; ?>" value="<? echo $row[csf('prod_id')]; ?>"/>
					<input type="hidden" name="rollId[]" id="rollId_<? echo $i; ?>" value="<? echo $row[csf('roll_id')]; ?>"/>
					<input type="hidden" name="rollWgt[]" id="rollWgt_<? echo $i; ?>" value="<? echo $row[csf('qnty')]; ?>"/>
					<input type="hidden" name="yarnLot[]" id="yarnLot_<? echo $i; ?>" value="<? echo $row[csf('yarn_lot')]; ?>"/>
					<input type="hidden" name="yarnCount[]" id="yarnCount_<? echo $i; ?>" value="<? echo $row[csf('yarn_count')]; ?>"/>
					<input type="hidden" name="stichLn[]" id="stichLn_<? echo $i; ?>" value="<? echo $row[csf('stitch_length')]; ?>"/>
					<input type="hidden" name="brandId[]" id="brandId_<? echo $i; ?>" value="<? echo $row[csf('brand_id')]; ?>"/>
					<input type="hidden" name="rack[]" id="rack_<? echo $i; ?>" value="<? echo $row[csf('rack')]; ?>"/>
					<input type="hidden" name="shelf[]" id="shelf_<? echo $i; ?>" value="<? echo $row[csf('self')]; ?>"/>
					<input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>" value=""/>
					<input type="hidden" name="transIdFrom[]" id="transIdFrom_<? echo $i; ?>" value=""/>
					<input type="hidden" name="transIdTo[]" id="transIdTo_<? echo $i; ?>" value=""/>
					<input type="hidden" name="rolltableId[]" id="rolltableId_<? echo $i; ?>" value=""/>
					<input type="hidden" name="transRollId[]" id="transRollId_<? echo $i; ?>" value="<? echo $transRollId ?>"/>
					<input type="hidden" name="storeId[]" id="storeId_<? echo $i; ?>" value="<? echo $row[csf('store_id')]; ?>"/></td>
				<td>
					<input class="text_boxes" type="text" name="txtRemarks[]" id="txtRemarks_<? echo $i; ?>">
				</td>
			</tr>
			<? 
			$i++; 
		}
	} 

	exit();
}

if($action=="show_transfer_listview")
{
	$data=explode("**",$data);
	$mst_id=$data[0];
	$order_id=$data[1];
	
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');	
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');	
	$product_arr=return_library_array( "select id, product_name_details from product_details_master where item_category_id=13",'id','product_name_details');

	$re_trans_arr=return_library_array("select barcode_no from pro_roll_details where entry_form=352 and re_transfer=1 and status_active=1 and is_deleted=0 and mst_id=$mst_id ", "barcode_no", "barcode_no");

	/*$transfer_arr=array();
	$transfer_dataArray=sql_select("SELECT b.id, b.trans_id, b.to_trans_id, b.from_roll_id as roll_id, b.barcode_no from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b where a.id=b.mst_id and b.mst_id=$mst_id and b.entry_form=352 and a.transfer_criteria=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach($transfer_dataArray as $row)
	{
		$transfer_arr[$row[csf('barcode_no')]]['dtls_id']=$row[csf('id')];
		$transfer_arr[$row[csf('barcode_no')]]['from_trans_id']=$row[csf('trans_id')];
		$transfer_arr[$row[csf('barcode_no')]]['to_trans_id']=$row[csf('to_trans_id')];
		$transfer_arr[$row[csf('barcode_no')]]['rolltableId']=$row[csf('roll_id')];
	}*/
	
	$programArr=return_library_array("SELECT a.id, a.booking_id from inv_receive_master a, pro_roll_details b where a.id=b.mst_id and a.entry_form=2 and a.receive_basis=2 and b.entry_form=2 and b.po_breakdown_id=$order_id and b.status_active=1 and b.is_deleted=0 group by a.id, a.booking_id", "id", "booking_id");
	
	/*$sql_qry="SELECT a.id, a.entry_form, 0 as receive_basis, 0 as booking_id, b.from_prod_id as prod_id, b.yarn_lot, b.y_count as yarn_count, b.stitch_length, b.brand_id, b.to_rack as rack, b.to_shelf as self, b.to_store as store_id, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.booking_no, c.roll_id as roll_id_prev, 3 as type
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(352) and c.entry_form in(352) and c.status_active=1 and c.is_deleted=0 and c.mst_id=$mst_id
	order by barcode_no";*/
	$sql_qry="SELECT a.id, a.entry_form, 0 as receive_basis, 0 as booking_id, b.from_prod_id as prod_id, b.yarn_lot, b.y_count as yarn_count, b.stitch_length, b.brand_id, b.to_rack as rack, b.to_shelf as self, b.to_store as store_id, b.from_roll_id as roll_id, b.barcode_no, b.to_order_id as po_breakdown_id, b.roll as roll_no, b.transfer_qnty as qnty, b.from_program as booking_no, b.roll_id as roll_id_prev, 3 as type, b.id as dtls_id, b.trans_id as from_trans_id, b.to_trans_id, b.remarks
	from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b 
	WHERE a.id=b.mst_id and a.entry_form in(352) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.mst_id=$mst_id
	order by barcode_no";
	// c.id as roll_id = b.roll_id, b.barcode_no, c.po_breakdown_id = b.to_order_id, c.roll_no = b.roll, 
	// c.qnty = b.transfer_qnty, c.booking_no = b.from_program, c.roll_id as roll_id_prev = b.roll_id, 
	// c.id as roll_id = b.FROM_ROLL_ID
	// C.ROLL_ID=$$rollId is PRO_ROLL_DETAILS.C WHERE C.ROLL_ID
	// C.ID as ROLL_ID=FROM_ROLL_ID=$$transRollId is PRO_ROLL_DETAILS.C WHERE C.ID
	
	//echo $sql_qry;
	
	$data_arr=sql_select( $sql_qry );
	//echo "tipu";  print_r ( $data_arr );	die;
	$barcodeNos="";
	foreach($data_arr as $vals)
	{ 
		$barcodeNos.=$vals[csf('barcode_no')].",";
	}

	$barcodeNos=chop($barcodeNos,",");
	 $barcodeNos;

	$sql_issue_barcode=sql_select("select barcode_no from  pro_roll_details  where  entry_form in(61) and status_active=1 and is_deleted=0 and barcode_no in($barcodeNos) and is_returned !=1 order by barcode_no");
	foreach($sql_issue_barcode as $barcodeNO)
	{ 
		$barcode_arr[$barcodeNO[csf('barcode_no')]]['barcode']=$barcodeNO[csf('barcode_no')];
	}

	$splited_roll_sql=sql_select("select barcode_no,split_from_id from pro_roll_split where status_active =1 and barcode_no in ($barcodeNos) ");

	foreach($splited_roll_sql as $bar)
	{ 
		$splited_roll_ref[$bar[csf('barcode_no')]][$bar[csf('split_from_id')]]=$bar[csf('barcode_no')];
	}

	$i=1;
	foreach($data_arr as $rows)
	{ 
		
		//if($issued_barcode_arr[$row[csf('barcode_no')]]=="")
		//{
		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		
		$ycount='';
		$count_id=explode(',',$rows[csf('yarn_count')]);
		foreach($count_id as $count)
		{
			if ($ycount=='') $ycount=$count_arr[$count]; else $ycount.=",".$count_arr[$count];
		}
		
		$transRollId=$rows[csf('roll_id')];
		$program_no='';
		
		$program_no=$rows[csf('booking_no')];
		$rows[csf('roll_id')]=$rows[csf('roll_id_prev')];
		
		/*if($transfer_arr[$rows[csf('barcode_no')]]['dtls_id']=="")
		{
			$checked=""; 	
		}*/
		$checked="checked"; 
		
		if($re_trans_arr[$rows[csf('barcode_no')]]=="")
		{
			$disabled=""; 	
		}
		else $disabled="disabled";
		//check issued barcode
		if ($barcode_arr[$rows[csf('barcode_no')]]['barcode']==$rows[csf('barcode_no')]) {
			$disabled="disabled";
		}

		if($splited_roll_ref[$rows[csf('barcode_no')]][$transRollId] !="")
		{
			$disabled="disabled";
		}

		/*$dtls_id=$transfer_arr[$rows[csf('barcode_no')]]['dtls_id'];
		$from_trans_id=$transfer_arr[$rows[csf('barcode_no')]]['from_trans_id'];
		$to_trans_id=$transfer_arr[$rows[csf('barcode_no')]]['to_trans_id'];
		$rolltableId=$transfer_arr[$rows[csf('barcode_no')]]['rolltableId'];*/
		?>
		<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
			<td width="40" align="center" valign="middle"><input type="checkbox" id="tbl_<? echo $i;?>" <? echo $checked." ".$disabled; ?> name="check[]" onClick="show_selected_total('<? echo $i;?>')"/></td> 
			<td width="40"><? echo $i; ?></td>
			<td width="80"><? echo $rows[csf('barcode_no')]; ?></td>
			<td width="50"><? echo $rows[csf('roll_no')]; ?></td>
			<td width="70"><p><? echo $program_no; ?>&nbsp;</p></td>
			<td width="60"><p><? echo $rows[csf('prod_id')]; ?></p></td>
			<td width="180"><p><? echo $product_arr[$rows[csf('prod_id')]]; ?>&nbsp;</p></td>
			<td width="80"><p><? echo $ycount; ?>&nbsp;</p></td>
			<td width="70"><p><? echo $brand_arr[$rows[csf('brand_id')]]; ?>&nbsp;</p></td>
			<td width="80"><p><? echo $rows[csf('yarn_lot')]; ?>&nbsp;</p></td>
			<td width="55"><p><? echo $rows[csf('rack')]; ?>&nbsp;</p></td>
			<td width="55"><p><? echo $rows[csf('self')]; ?>&nbsp;</p></td>
			<td width="80"><p><? echo $rows[csf('stitch_length')]; ?>&nbsp;</p></td>
			<td width="80" align="right" style="padding-right:2px"><? echo number_format($rows[csf('qnty')],2); ?>
				<input type="hidden" name="barcodeNo[]" id="barcodeNo_<? echo $i; ?>" value="<? echo $rows[csf('barcode_no')]; ?>"/>
				<input type="hidden" name="rollNo[]" id="rollNo_<? echo $i; ?>" value="<? echo $rows[csf('roll_no')]; ?>"/>
				<input type="hidden" name="progId[]" id="progId_<? echo $i; ?>" value="<? echo $program_no; ?>"/>
				<input type="hidden" name="productId[]" id="productId_<? echo $i; ?>" value="<? echo $rows[csf('prod_id')]; ?>"/>
				<input type="hidden" name="rollId[]" id="rollId_<? echo $i; ?>" value="<? echo $rows[csf('roll_id')]; ?>"/>
				<input type="hidden" name="rollWgt[]" id="rollWgt_<? echo $i; ?>" value="<? echo $rows[csf('qnty')]; ?>"/>
				<input type="hidden" name="yarnLot[]" id="yarnLot_<? echo $i; ?>" value="<? echo $rows[csf('yarn_lot')]; ?>"/>
				<input type="hidden" name="yarnCount[]" id="yarnCount_<? echo $i; ?>" value="<? echo $rows[csf('yarn_count')]; ?>"/>
				<input type="hidden" name="stichLn[]" id="stichLn_<? echo $i; ?>" value="<? echo $rows[csf('stitch_length')]; ?>"/>
				<input type="hidden" name="brandId[]" id="brandId_<? echo $i; ?>" value="<? echo $rows[csf('brand_id')]; ?>"/>
				<input type="hidden" name="rack[]" id="rack_<? echo $i; ?>" value="<? echo $rows[csf('rack')]; ?>"/>
				<input type="hidden" name="shelf[]" id="shelf_<? echo $i; ?>" value="<? echo $rows[csf('self')]; ?>"/>
				<input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>" value="<? echo $rows[csf('dtls_id')]; ?>"/>
				<input type="hidden" name="transIdFrom[]" id="transIdFrom_<? echo $i; ?>" value="<? echo $rows[csf('from_trans_id')]; ?>"/>
				<input type="hidden" name="transIdTo[]" id="transIdTo_<? echo $i; ?>" value="<? echo $rows[csf('to_trans_id')]; ?>"/>
				<input type="hidden" name="rolltableId[]" id="rolltableId_<? echo $i; ?>" value="<? echo $rows[csf('roll_id')]; ?>"/>
				<input type="hidden" name="transRollId[]" id="transRollId_<? echo $i; ?>" value="<? echo $transRollId ?>"/>
				<input type="hidden" name="storeId[]" id="storeId_<? echo $i; ?>" value="<? echo $rows[csf('store_id')]; ?>"/>
			</td>
			<td>
				<input class="text_boxes" type="text" name="txtRemarks[]" id="txtRemarks_<? echo $i; ?>" value="<?echo $rows[csf('remarks')]; ?>">
			</td>
		</tr>
		<? 
		$i++; 
		//}
	} 
	exit();
}

if($action=="populate_data_about_order")
{
	$data=explode("**",$data);
	$order_id=$data[0];
	$prod_id=$data[1];
	
	$sql=sql_select("select 
		sum(case when entry_form in(2,22) then quantity end) as grey_fabric_recv, 
		sum(case when entry_form in(16) then quantity end) as grey_fabric_issued,
		sum(case when entry_form=45 then quantity end) as grey_fabric_recv_return, 
		sum(case when entry_form=51 then quantity end) as grey_fabric_issue_return,
		sum(case when entry_form in(13,81) and trans_type=5 then quantity end) as grey_fabric_trans_recv, 
		sum(case when entry_form in(13,80) and trans_type=6 then quantity end) as grey_fabric_trans_issued
		from order_wise_pro_details where trans_id<>0 and prod_id=$prod_id and po_breakdown_id=$order_id and is_deleted=0 and status_active=1");
	
	$grey_fabric_recv=$sql[0][csf('grey_fabric_recv')]+$sql[0][csf('grey_fabric_trans_recv')]+$sql[0][csf('grey_fabric_issue_return')];
	$grey_fabric_issued=$sql[0][csf('grey_fabric_issued')]+$sql[0][csf('grey_fabric_trans_issued')]+$sql[0][csf('grey_fabric_recv_return')];
	$yet_issue=$grey_fabric_recv-$grey_fabric_issued;

	echo "$('#txt_stock').val('".$yet_issue."');\n"; 
	
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
							<th width="240" id="search_by_td_up">Please Enter Requisition ID</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
								<input type="hidden" name="transfer_id" id="transfer_id" class="text_boxes" value="">
							</th>
						</thead>
						<tr class="general">
							<td>
								<?
								$search_by_arr=array(1=>"Requisition ID",2=>"Challan No.");
								$dd="change_search_event(this.value, '0*0', '0*0', '../../../') ";
								echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
								?>
							</td>
							<td id="search_by_td">
								<input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
							</td>
							<td>
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>, 'create_transfer_search_list_view', 'search_div', 'grey_sales_order_to_order_roll_trans_requisition_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
	
	$sql="SELECT id, transfer_prefix_number, $year_field transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria, item_category from inv_item_transfer_requ_mst where item_category=13 and company_id=$company_id and $search_field like '$search_string' and transfer_criteria=4 and entry_form=352 and status_active=1 and is_deleted=0 order by id";
	
	$arr=array(3=>$company_arr,5=>$item_transfer_criteria,6=>$item_category);

	echo  create_list_view("tbl_list_search", "Transfer ID,Year,Challan No,Company,Transfer Date,Transfer Criteria,Item Category", "80,70,100,80,90,130","750","250",0, $sql, "js_set_value", "id", "", 1, "0,0,0,company_id,0,transfer_criteria,item_category", $arr, "transfer_prefix_number,year,challan_no,company_id,transfer_date,transfer_criteria,item_category", '','','0,0,0,0,3,0,0');
	
	exit();
}

if($action=='populate_data_from_transfer_master')
{
	$data_array=sql_select("SELECT transfer_system_id,challan_no, company_id, transfer_date, item_category, from_order_id, to_order_id, ready_to_approve, is_approved from inv_item_transfer_requ_mst where id='$data'");
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('update_id').value 					= '".$data."';\n";
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("transfer_system_id")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value 		= '".$row[csf("ready_to_approve")]."';\n";
		echo "document.getElementById('txt_transfer_date').value 			= '".change_date_format($row[csf("transfer_date")])."';\n";
		echo "get_php_form_data('".$row[csf("from_order_id")]."**from'".",'populate_data_from_order','requires/grey_sales_order_to_order_roll_trans_requisition_controller');\n";
		echo "get_php_form_data('".$row[csf("to_order_id")]."**to'".",'populate_data_from_order','requires/grey_sales_order_to_order_roll_trans_requisition_controller');\n";
		echo "$('#cbo_company_id').attr('disabled','disabled');\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_grey_transfer_entry',1,1);\n"; 

		echo "document.getElementById('is_approved').value 		= '".$row[csf("is_approved")]."';\n";
		if($row[csf("is_approved")] == 1)	
		{
			echo "$('#approved').text('Approved');\n";
		}
		elseif($row[csf("is_approved")] == 3)	
		{
			echo "$('#approved').text('Partial Approved');\n";
		}
		else
		{
			echo "$('#approved').text('');\n";
	  	}
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
					sum(CASE WHEN entry_form ='13' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_out_qnty,
					sum(CASE WHEN entry_form ='13' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_in_qnty,
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

	if(str_replace("'","",$update_id)!="")
	{
		$approve_valid = return_field_value("id", "approval_history", "mst_id =$update_id and entry_form=40 and current_approval_status=1", "id"); 
		if ($approve_valid!="")
		{
			echo "20**Requisition is Approved. So Change Not Allowed";
			die;
		}
	}
	
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$transfer_recv_num=''; $transfer_update_id='';
		
		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
			
			$id = return_next_id_by_sequence("ITEM_TRAN_REQU_MST_PK_SEQ", "inv_item_transfer_requ_mst", $con);
            //print_r($id); die;
			$new_transfer_system_id = explode("*", return_next_id_by_sequence("ITEM_TRAN_REQU_MST_PK_SEQ", "inv_item_transfer_requ_mst",$con,1,$cbo_company_id,'GSTSTR',352,date("Y",time()),13 ));
			
			$field_array="id, transfer_prefix, transfer_prefix_number, transfer_system_id, company_id, challan_no, transfer_date, entry_form, transfer_criteria, to_company, from_order_id, to_order_id, ready_to_approve, item_category, inserted_by, insert_date";
			
			$data_array="(".$id.",'".$new_transfer_system_id[1]."',".$new_transfer_system_id[2].",'".$new_transfer_system_id[0]."',".$cbo_company_id.",".$txt_challan_no.",".$txt_transfer_date.",352,4,0,".$txt_from_order_id.",".$txt_to_order_id.",".$cbo_ready_to_approved.",13,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			//echo "insert into inv_item_transfer_requ_mst (".$field_array.") values ".$data_array;die;
			/*$rID=sql_insert("inv_item_transfer_requ_mst",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;*/
			
			$transfer_recv_num=$new_transfer_system_id[0];
			$transfer_update_id=$id;
		}
		else
		{
			$field_array_update="challan_no*transfer_date*from_order_id*to_order_id*ready_to_approve*updated_by*update_date";
			$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$txt_from_order_id."*".$txt_to_order_id."*".$cbo_ready_to_approved."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			/*$rID=sql_update("inv_item_transfer_requ_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0; */
			
			$transfer_recv_num=str_replace("'","",$txt_system_id);
			$transfer_update_id=str_replace("'","",$update_id);
		}
		
		$rate=0; $amount=0;
		
		//$id_dtls=return_next_id( "id", "inv_item_transfer_requ_dtls", 1 ) ; 
		$field_array_dtls="id, mst_id, trans_id, to_trans_id, from_prod_id, item_category, transfer_qnty, roll, rate, transfer_value, uom, y_count, brand_id, yarn_lot, rack, shelf, to_rack, to_shelf, from_program, to_program, stitch_length, from_store, to_store, barcode_no, roll_id, from_roll_id, from_order_id, to_order_id, entry_form,remarks, inserted_by, insert_date";
		
		$rollIds='';
		for($j=1;$j<=$total_row;$j++)
		{
			$barcodeNo="barcodeNo_".$j;
			$rollNo="rollNo_".$j;
			$progId="progId_".$j;
			$productId="productId_".$j;
			$rollId="rollId_".$j;
			$rollWgt="rollWgt_".$j;
			$yarnLot="yarnLot_".$j;
			$yarnCount="yarnCount_".$j;
			$stichLn="stichLn_".$j;
			$brandId="brandId_".$j;
			$rack="rack_".$j;
			$shelf="shelf_".$j;
			$rollNo="rollNo_".$j;
			$transRollId="transRollId_".$j;
			$storeId="storeId_".$j;
			$txtRemarks="txtRemarks_".$j;
			
			$rollIds.=$$transRollId.",";

			$id_dtls = return_next_id_by_sequence("ITEM_TRAN_REQU_DTLS_PK_SEQ", "inv_item_transfer_requ_dtls", $con);
			
			if($data_array_dtls!="") $data_array_dtls.=",";
			$data_array_dtls.="(".$id_dtls.",".$transfer_update_id.",0,0,".$$productId.",13,".$$rollWgt.",".$$rollNo.",".$rate.",".$amount.",12,".$$yarnCount.",".$$brandId.",".$$yarnLot.",".$$rack.",".$$shelf.",".$$rack.",".$$shelf.",".$$progId.",".$$progId.",".$$stichLn.",".$$storeId.",".$$storeId.",".$$barcodeNo.",".$$rollId.",".$$transRollId.",".$txt_from_order_id.",".$txt_to_order_id.",352,".$$txtRemarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			//echo $$rollId.'**'.$$transRollId;
			//C.ROLL_ID=$$rollId is PRO_ROLL_DETAILS.C WHERE C.ROLL_ID
			//C.ID as ROLL_ID=FROM_ROLL_ID=$$transRollId is PRO_ROLL_DETAILS.C WHERE C.ID
		}

		if(str_replace("'","",$update_id)=="")
		{
			$rID=sql_insert("inv_item_transfer_requ_mst",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;
		}
		else
		{
			$rID=sql_update("inv_item_transfer_requ_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0; 
		}

		// echo "insert into inv_item_transfer_requ_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;		
		$rID2=sql_insert("inv_item_transfer_requ_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		}		
		
		// echo "10**$rID##$rID2";die;
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
				echo "5**0**"."&nbsp;"."**0";
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
				echo "5**0**"."&nbsp;"."**0";
			}
		}
		disconnect($con);
		die;
	}	
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }

        /**
         * list of fields that will not change/update on update button event
         * fields=> from_order_id*to_order_id*
         * data => $txt_from_order_id."*".$txt_to_order_id."*".
         */
		$field_array_update="challan_no*transfer_date*ready_to_approve*updated_by*update_date";
		$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$cbo_ready_to_approved."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rate=0; $amount=0;

		$field_array_dtls="id, mst_id, trans_id, to_trans_id, from_prod_id, item_category, transfer_qnty, roll, rate, transfer_value, uom, y_count, brand_id, yarn_lot, rack, shelf, to_rack, to_shelf, from_program, to_program, stitch_length, from_store, to_store, barcode_no, roll_id, from_roll_id, from_order_id, to_order_id, entry_form, remarks, inserted_by, insert_date";

		$field_array_dtls_update="from_prod_id*transfer_qnty*roll*rate*transfer_value*y_count*brand_id*yarn_lot*rack*shelf*to_rack*to_shelf*from_program*to_program*stitch_length*remarks*updated_by*update_date";
		
		$rollIds=''; $update_dtls_id='';
		for($j=1;$j<=$total_row;$j++)
		{ 	
			$barcodeNo="barcodeNo_".$j;
			$rollNo="rollNo_".$j;
			$progId="progId_".$j;
			$productId="productId_".$j;
			$rollId="rollId_".$j;
			$rollWgt="rollWgt_".$j;
			$yarnLot="yarnLot_".$j;
			$yarnCount="yarnCount_".$j;
			$stichLn="stichLn_".$j;
			$brandId="brandId_".$j;
			$rack="rack_".$j;
			$shelf="shelf_".$j;
			$rollNo="rollNo_".$j;
			$dtlsId="dtlsId_".$j;
			$transIdFrom="transIdFrom_".$j;
			$transIdTo="transIdTo_".$j;
			$rolltableId="rolltableId_".$j;
			$transRollId="transRollId_".$j;
			$storeId="storeId_".$j;		
			$txtRemarks="txtRemarks_".$j;
						
			if(str_replace("'","",$$rolltableId)>0) // Update
			{
				$update_dtls_id.=str_replace("'","",$$dtlsId).",";
				
				$dtlsId_arr[]=str_replace("'","",$$dtlsId);
				$data_array_update_dtls[str_replace("'","",$$dtlsId)]=explode("*",($$productId."*".$$rollWgt."*".$$rollNo."*".$rate."*".$amount."*".$$yarnCount."*".$$brandId."*".$$yarnLot."*".$$rack."*".$$shelf."*".$$rack."*".$$shelf."*".$$progId."*".$$progId."*".$$stichLn."*".$$txtRemarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				
				$dtlsIdProp=str_replace("'","",$$dtlsId);
				//$transIdfromProp=str_replace("'","",$$transIdFrom);
				//$transIdtoProp=str_replace("'","",$$transIdTo);
			}
			else // Inser new roll
			{
				//$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$id_dtls = return_next_id_by_sequence("ITEM_TRAN_REQU_DTLS_PK_SEQ", "inv_item_transfer_requ_dtls", $con);
				//$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
				
				$rollIds.=$$transRollId.",";

				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$id_dtls.",".$transfer_update_id.",0,0,".$$productId.",13,".$$rollWgt.",".$$rollNo.",".$rate.",".$amount.",12,".$$yarnCount.",".$$brandId.",".$$yarnLot.",".$$rack.",".$$shelf.",".$$rack.",".$$shelf.",".$$progId.",".$$progId.",".$$stichLn.",".$$storeId.",".$$storeId.",".$$barcodeNo.",".$$rollId.",".$$transRollId.",".$txt_from_order_id.",".$txt_to_order_id.",352,".$$txtRemarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

				$dtlsIdProp=$id_dtls;
				//$id_dtls=$id_dtls+1;
				$all_trans_roll_id.=$$transRollId.",";
			}
		}
		//echo "10**".$txt_deleted_id;die; 
		if($txt_deleted_id!="") // Delete Here--------------
		{
			//echo "10**5**Tipu".$txt_deleted_id;die;
			$deletedIds=explode(",",$txt_deleted_id); $dtlsIDDel=''; $transIDDel=''; $rollIDDel=''; $rollIDactive=''; $delBarcodeNo='';
			foreach($deletedIds as $delIds)
			{
				$delIds=explode("_",$delIds);
				if($dtlsIDDel=="")
				{
					$dtlsIDDel=$delIds[0];
				}
				else
				{
					$dtlsIDDel.=",".$delIds[0];
				}
			}
			// print_r($dtlsIDDel);

			$field_array_status="updated_by*update_date*status_active*is_deleted";
			$data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
			$statusChangeDtls=sql_multirow_update("inv_item_transfer_requ_dtls",$field_array_status,$data_array_status,"id",$dtlsIDDel,0);			
			if($flag==1) 
			{
				if($statusChangeDtls) $flag=1; else $flag=0; 
			} 
		}
		//echo "10**fail";die;
		
		/*if($dtlsIDDel=="")
		{
			$update_dtls_id=chop($update_dtls_id,',');
		}
		else
		{
			$update_dtls_id=$update_dtls_id.$dtlsIDDel;
		}*/
		
		$rID=sql_update("inv_item_transfer_requ_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0;
		
		if(count($data_array_update_dtls)>0)
		{
			$rID3=execute_query(bulk_update_sql_statement("inv_item_transfer_requ_dtls","id",$field_array_dtls_update,$data_array_update_dtls,$dtlsId_arr));
			if($flag==1) 
			{
				if($rID3) $flag=1; else $flag=0; 
			}
		}
		
		if($data_array_dtls!="")
		{
			$rIDDtls=sql_insert("inv_item_transfer_requ_dtls",$field_array_dtls,$data_array_dtls,0);
			if($flag==1) 
			{
				if($rIDDtls) $flag=1; else $flag=0; 
			}
		}
		
		/*$rollIds=chop($rollIds,',');
		if($rollIds!="")
		{
			$rID5=sql_multirow_update("pro_roll_details","is_transfer*transfer_criteria*re_transfer","6*4*1","id",$rollIds,0);
			if($flag==1) 
			{
				if($rID5) $flag=1; else $flag=0; 
			} 
		}*/	
		
		//echo "10**5**insert into inv_item_transfer_requ_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		//echo "10**$rID##$rID3##$rIDDtls";die;
		
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

if ($action=="grey_fabric_fso_to_fso_requisition_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	
	$sql="select id, transfer_system_id, transfer_date, challan_no, from_order_id, to_order_id, item_category from inv_item_transfer_requ_mst a where id='$data[1]' and company_id='$data[0]'";
	//echo $sql;die;
	
	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=13","id","product_name_details");
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');	
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');	
	
	$po_buyer_arr=return_library_array( "select id, buyer_id from wo_booking_mst",'id','buyer_id');
	$po_comp_arr=return_library_array( "select id, company_id from wo_booking_mst",'id','company_id');
	$po_break_down_arr=return_library_array( "select id, po_break_down_id from wo_booking_mst",'id','po_break_down_id');

	$data_sql=sql_select("select a.id,a.grouping from wo_po_break_down a where a.status_active=1 and a.is_deleted=0");

	foreach($data_sql as $inv)
	{
		$grouping_arr[$inv[csf("id")]] .=$inv[csf("grouping")];
	}
	unset($data_sql);
	
	$poDataArray= sql_select("SELECT a.id, a.job_no, a.job_no_prefix_num, a.company_id, a.style_ref_no, a.sales_booking_no, a.buyer_id, a.booking_id, sum(b.grey_qty) as qty from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 group by a.id, a.job_no, a.job_no_prefix_num, a.company_id, a.style_ref_no, a.sales_booking_no, a.buyer_id, a.booking_id");
	$job_array=array(); //$all_job_id='';
	foreach($poDataArray as $row)
	{
		$job_array[$row[csf('id')]]['no']=$row[csf('job_no')];
		$job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_id')];
		$job_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
		$job_array[$row[csf('id')]]['qty']=$row[csf('qty')];
		$job_array[$row[csf('id')]]['company']=$row[csf('company_id')];
		$job_array[$row[csf('id')]]['booking']=$row[csf('sales_booking_no')];
		$job_array[$row[csf('id')]]['booking_id']=$row[csf('booking_id')];
	} 
	unset($poDataArray);
	?>
	<div style="width:1030px;">
		<table width="1000" cellspacing="0" align="right">
			<tr>
				<td colspan="6" align="center" style="font-size:20px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:14px">  
					<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
					foreach ($nameArray as $result)
					{ 
						?>
						Plot No: <? echo $result[csf('plot_no')]; ?> 
						Level No: <? echo $result[csf('level_no')]?>
						Road No: <? echo $result[csf('road_no')]; ?> 
						Block No: <? echo $result[csf('block_no')];?> 
						City No: <? echo $result[csf('city')];?> 
						Zip Code: <? echo $result[csf('zip_code')]; ?> 
						Email Address: <? echo $result[csf('email')];?> 
						Website No: <? echo $result[csf('website')];
					}
					?> 
				</td>  
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:18px"><strong><u><? echo $data[2]; ?> Report</u></strong></td>
			</tr>
			<tr>
				<td width="125"><strong>Transfer ID :</strong></td><td width="175px"><? echo $dataArray[0][csf('transfer_system_id')]; ?></td>
				<td width="125"><strong>Transfer Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
				<td width="125"><strong>Challan No.:</strong></td><td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
			</tr>
		</table>
		<table width="1000" cellspacing="0" align="right" style="margin-top:5px;">
			<tr>
				<td width="450">
					<table width="100%" cellspacing="0" align="right" style="font-size:12px">
						<tr>
							<td colspan="4" align="center" style="font-weight:bold; font-size:14px;"><u>From Order</u></td>
						</tr>
						<tr>
							<td width="100">Sales Order No:</td>
							<td width="120">&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['no']; ?></td>
							<td width="100">Quantity:</td>
							<td>&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['qty']; ?></td>
						</tr>
						<tr>
							<td>Po Buyer:</td>
							<td>&nbsp;<? echo $buyer_arr[$po_buyer_arr[$job_array[$dataArray[0][csf('from_order_id')]]['booking_id']]]; ?></td>
							<td>Po Company:</td>
							<td>&nbsp;<? echo $company_library[$po_comp_arr[$job_array[$dataArray[0][csf('from_order_id')]]['booking_id']]]; ?></td>
						</tr>
						<tr>
							<td>Style Ref:</td>
							<td>&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['style']; ?></td>
							<td>Booking No:</td>
							<td>&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['booking']; ?></td>
						</tr> 
					</table>
				</td>
				<td>
					<table width="100%" cellspacing="0" align="right" style="font-size:12px">
						<tr>
							<td colspan="4" align="center" style="font-weight:bold; font-size:14px;"><u>To Order</u></td>
						</tr>
						<tr>
							<td width="100">Sales Order No:</td>
							<td width="120">&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['no']; ?></td>
							<td width="100">Quantity:</td>
							<td>&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['qty']; ?></td>
						</tr>
						<tr>
							<td>Po Buyer:</td>
							<td>&nbsp;<? echo $buyer_arr[$po_buyer_arr[$job_array[$dataArray[0][csf('to_order_id')]]['booking_id']]]; ?></td>
							<td>Po Company:</td>
							<td>&nbsp;<? echo $company_library[$po_comp_arr[$job_array[$dataArray[0][csf('to_order_id')]]['booking_id']]]; ?></td>
						</tr>
						<tr>
							<td>Style Ref:</td>
							<td>&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['style']; ?></td>
							<td>Booking No:</td>
							<td>&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['booking']; ?></td>
						</tr> 
					</table>
				</td>
			</tr>
		</table>
		<br>
		<div style="width:100%;">
			<table align="right" cellspacing="0" width="1000"  border="1" rules="all" class="rpt_table" style="font-size:13px" >
				<thead bgcolor="#dddddd" align="center">
					<th width="30">SL</th>
					<th width="80">Barcode No</th>
					<th width="60">Roll No</th>
					<th width="60">Prog. No</th>
					<th width="100" title="Internal Ref. of From Order">Internal Ref.</th>
					<th width="160">Fabric Description</th>
					<th width="80">Y/Count</th>
					<th width="70">Y/Brand</th>
					<th width="80">Y/Lot</th>
					<th width="50">Rack</th>
					<th width="50">Shelf</th>
					<th width="70">Stitch Length</th>
					<th width="50">UOM</th>
					<th width="50">Transfered Qty</th>
					<th>Remarks</th>
				</thead>
				<tbody> 
					<?
					$sql_dtls="SELECT b.from_prod_id, b.transfer_qnty, b.uom, b.y_count, b.brand_id, b.yarn_lot, b.rack, b.shelf, b.stitch_length, b.barcode_no, b.roll, b.from_program, b.remarks
					from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b where a.id=b.mst_id and b.entry_form=352 and a.id='$data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.barcode_no";
					//echo $sql_dtls;
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
						
						$ycount='';
						$count_id=explode(',',$row[csf('y_count')]);
						foreach($count_id as $count)
						{
							if ($ycount=='') $ycount=$count_arr[$count]; else $ycount.=",".$count_arr[$count];
						}   
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td align="center"><? echo $i; ?></td>
							<td><? echo $row[csf("barcode_no")]; ?></td>
							<td><? echo $row[csf("roll")]; ?></td>
							<td><? echo $row[csf("from_program")]; ?></td>
							<td><?php echo $grouping_arr[$po_break_down_arr[$job_array[$dataArray[0][csf('from_order_id')]]['booking_id']]];?></td>
							<td><? echo $product_arr[$row[csf("from_prod_id")]]; ?></td>
							<td><? echo $ycount; ?></td>
							<td><? echo $brand_arr[$row[csf("brand_id")]]; ?></td>
							<td><? echo $row[csf("yarn_lot")]; ?></td>
							<td><? echo $row[csf("rack")]; ?></td>
							<td><? echo $row[csf("shelf")]; ?></td>
							<td><? echo $row[csf("stitch_length")]; ?></td>
							<td align="center"><? echo $unit_of_measurement[$row[csf("uom")]]; ?></td>
							<td align="right"><? echo $row[csf("transfer_qnty")]; ?></td>
							<td align=""><? echo $row[csf("remarks")]; ?></td>
						</tr>
						<? 
						$i++; 
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="13" align="right"><strong>Total </strong></td>
						<td align="right"><?php echo $transfer_qnty_sum; ?></td>
						<td></td>
					</tr>                           
				</tfoot>
			</table>
			<br>
			<?
			echo signature_table(111, $data[0], "900px");
			?>
		</div>
	</div>   
	<?	
	exit();
}
?>

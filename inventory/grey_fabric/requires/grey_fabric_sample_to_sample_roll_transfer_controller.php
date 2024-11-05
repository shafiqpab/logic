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
$sample_arr=return_library_array( "select id,sample_name from lib_sample where is_deleted=0 and status_active=1 order by sample_name",'id','sample_name');
$color_arr=return_library_array( "select id,color_name from lib_color where is_deleted=0 and status_active=1 order by color_name",'id','color_name');

// Transfer from 
if ($action=="transfer_from_sample_popup")
{
	echo load_html_head_contents("Sample Information", "../../../", 1, 1,'','','');
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
<div align="center" style="width:1180px;">
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:1170px;margin-left:10px">
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
							echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",$data[0] );
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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_sample_no').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_sample_search_list_view', 'search_div', 'grey_fabric_sample_to_sample_roll_transfer_controller', 'setFilterGrid(\'tbl_list_sample\',-1);')" style="width:100px;" />
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

// Transfer To
if ($action=="transfer_to_sample_popup")
{
	echo load_html_head_contents("Sample Information", "../../../", 1, 1,'','','');
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
<div align="center" style="width:1180px;">
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:1170px;margin-left:10px;">
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
							echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",$data[0] );
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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_sample_no').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_sample_search_list_view', 'search_div', 'grey_fabric_sample_to_sample_roll_transfer_controller', 'setFilterGrid(\'tbl_list_sample\',-1);')" style="width:100px;" />
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
			$booking_date = "and a.booking_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$booking_date = "and a.booking_date between '".change_date_format($data[3],'','',1)."' and '".change_date_format($data[4],'','',1)."'";
		}
	}
	else 
		$booking_date ="";
	
	$style_name_array=return_library_array( "select id, style_ref_no from sample_development_mst",'id','style_ref_no');
	$arr=array (2=>$company_arr,3=>$buyer_arr,4=>$style_name_array,5=>$sample_arr,6=>$body_part,8=>$color_arr);
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	$sql= "select a.id as booking_id, b.id as dtls_id,a.booking_no, $year_field, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.booking_date, a.delivery_date, b.style_id, b.fabric_description,b.sample_type,b.fabric_color,b.grey_fabric, b.body_part
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
	where a.booking_no=b.booking_no  and a.booking_type=4 and a.company_id=$company_id and a.buyer_id like '$buyer' and b.booking_no like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $booking_date order by a.id, b.id";


	echo create_list_view("tbl_list_sample", "Booking No,Year,Company,Buyer Name,Style Ref. No,Sample,Body Part,Style Description,Fab. Color,Booking Date, Grey Qnty", "60,60,80,80,100,100,130,160,80,80,60","1100","200",0, $sql , "js_set_value", "booking_id,dtls_id", "", 1, "0,0,company_id,buyer_id,style_id,sample_type,body_part,0,fabric_color,0,0", $arr , "booking_no_prefix_num,year,company_id,buyer_id,style_id,sample_type,body_part,fabric_description,fabric_color,booking_date,grey_fabric", "",'','0,0,0,0,0,0,0,0,0,3');
	
	exit();
}

if($action=="populate_data_to_sample_transfer_from")
{
	$data=explode("**",$data);
	$sample_id=$data[0];
	
	$style_name_array=return_library_array( "select id, style_ref_no from sample_development_mst",'id','style_ref_no');	
	
	$data_array=sql_select("select a.id as booking_id, a.booking_no, a.booking_no_prefix_num, a.buyer_id, b.id as dtls_id, b.style_id, b.body_part, b.grey_fabric
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b  
	where a.booking_no=b.booking_no and b.id=$sample_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	 
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('txt_trans_from_sam_book_no').value 		= '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('txt_trans_from_sam_book_id').value 		= '".$row[csf("booking_id")]."';\n";
		echo "document.getElementById('txt_trans_from_sam_book_dtls_id').value 	= '".$row[csf("dtls_id")]."';\n";
		echo "document.getElementById('txt_trans_from_sam_booking_qnty').value 	= '".$row[csf("grey_fabric")]."';\n";
		echo "document.getElementById('cbo_trans_from_buyer_name').value 		= '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('txt_trans_from_style_ref').value 		= '".$style_name_array[$row[csf("style_id")]]."';\n";		echo "document.getElementById('cbo_trans_from_garments_item').value 	= '".$row[csf("body_part")]."';\n";
		exit();
	}
}

if($action=="populate_data_to_sample_transfer_to")
{
	$data=explode("**",$data);
	$sample_id=$data[0];
	
	$style_name_array=return_library_array( "select id, style_ref_no from sample_development_mst",'id','style_ref_no');	
		
	$data_array=sql_select("select a.id as booking_id, a.booking_no, a.booking_no_prefix_num, a.buyer_id, b.id as dtls_id, b.style_id, b.body_part, b.grey_fabric
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b  
	where a.booking_no=b.booking_no and b.id=$sample_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	 
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('txt_trans_to_sam_book_no').value 	= '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('txt_trans_to_sam_book_id').value 	= '".$row[csf("booking_id")]."';\n";
		echo "document.getElementById('txt_trans_to_sam_book_dtls_id').value = '".$row[csf("dtls_id")]."';\n";
		echo "document.getElementById('txt_trans_to_sam_booking_qnty').value = '".$row[csf("grey_fabric")]."';\n";
		echo "document.getElementById('cbo_trans_to_buyer_name').value 		= '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('txt_trans_to_style_ref').value 		= '".$style_name_array[$row[csf("style_id")]]."';\n";		
		echo "document.getElementById('cbo_trans_to_garments_item').value 	= '".$row[csf("body_part")]."';\n";
		exit();
	}
}

if($action=='populate_data_from_transfer_master')
{
	$data_array=sql_select("select transfer_system_id,challan_no, company_id, transfer_date, item_category, from_order_id, to_order_id, from_samp_dtls_id, to_samp_dtls_id from inv_item_transfer_mst where id='$data'");
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('update_id').value 					= '".$data."';\n";
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("transfer_system_id")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_transfer_date').value 			= '".change_date_format($row[csf("transfer_date")])."';\n";
		//echo "get_php_form_data('".$row[csf("from_order_id")]."**from'".",'populate_data_to_sample_transfer_from','requires/grey_fabric_sample_to_sample_roll_transfer_controller');\n";
		//echo "get_php_form_data('".$row[csf("to_order_id")]."','populate_data_to_sample_transfer_to','requires/grey_fabric_sample_to_sample_roll_transfer_controller');\n";
		echo "get_php_form_data('".$row[csf("from_samp_dtls_id")]."**from'".",'populate_data_to_sample_transfer_from','requires/grey_fabric_sample_to_sample_roll_transfer_controller');\n";
		echo "get_php_form_data('".$row[csf("to_samp_dtls_id")]."','populate_data_to_sample_transfer_to','requires/grey_fabric_sample_to_sample_roll_transfer_controller');\n";

		echo "$('#cbo_company_id').attr('disabled','disabled');\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_grey_transfer_entry',1,1);\n"; 
		exit();
	}
}

if($action=="show_dtls_list_view")
{
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');	
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');	
	$product_arr=return_library_array( "select id, product_name_details from product_details_master where item_category_id=13",'id','product_name_details');
	//$delv_arr=return_library_array("select barcode_num, grey_sys_id from pro_grey_prod_delivery_dtls where entry_form=56 and order_id=$data", "barcode_num", "grey_sys_id");
	
	//$issued_barcode_arr=return_library_array("select barcode_no from pro_roll_details where entry_form=61 and status_active=1 and is_deleted=0 and po_breakdown_id=$data and booking_without_order=1 and is_returned =0","barcode_no", "barcode_no");
	
	$programArr=return_library_array("select a.id, a.booking_id from inv_receive_master a, pro_roll_details b where a.id=b.mst_id and a.entry_form=2 and a.receive_basis=2 and b.entry_form=2 and b.po_breakdown_id=$data and b.status_active=1 and b.is_deleted=0", "id", "booking_id");
	
	$sql="select a.id, a.entry_form, a.receive_basis, a.booking_id, b.prod_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, d.rack, b.self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 1 as type, a.store_id
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, inv_transaction d 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id=d.id and a.entry_form in(2,22,58) and b.trans_id<>0 and c.entry_form in(2,22,58) and c.re_transfer =0 and c.booking_without_order=1 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=$data
	union all
	select a.id,a.entry_form, 0 as receive_basis, 0 as booking_id, b.from_prod_id as prod_id, b.yarn_lot, b.y_count as yarn_count, b.stitch_length, b.brand_id, b.to_rack as rack, b.to_shelf as self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 3 as type , b.to_store as store_id
    from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c 
    where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(110,180) and c.entry_form in(110,180) and c.status_active=1 and c.is_deleted=0 
    and a.to_order_id =$data and c.booking_without_order=1 and c.re_transfer =0
    union all
    select a.id,a.entry_form, 0 as receive_basis, 0 as booking_id, b.from_prod_id as prod_id, b.yarn_lot, b.y_count as yarn_count, b.stitch_length, b.brand_id, b.to_rack as rack, b.to_shelf as self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 3 as type , b.to_store as store_id
    from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c 
    where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(82) and c.entry_form in(82) and c.status_active=1 and c.is_deleted=0 
    and b.to_order_id =$data and c.booking_without_order=1 and c.re_transfer =0
    order by barcode_no";
	/*
		22=>'Knit Grey Fabric Receive',
		2=>"Grey Receive",
		58=>"Knit Grey Fabric Receive Roll",
		61=>"Grey Fabric Issue Roll Wise",
		51=>"Knit Grey Fabric Issue Return",
		84=>"Roll wise Grey Fabric Issue Return"
	*/
	//echo $sql;
	//booking_without_order=1

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
	    if($db_type==2 && count($ref_barcode_arr)>999)
	    {
	    	$ref_barcode_arr_chunk=array_chunk($ref_barcode_arr,999) ;
	    	foreach($ref_barcode_arr_chunk as $chunk_arr)
	    	{
	    		$chunk_arr_value=implode(",",$chunk_arr);	
	    		$barCond.="  barcode_no in($chunk_arr_value) or ";	
	    	}

	    	$all_ref_barcode_no.=" and (".chop($barCond,'or ').")";	
	    }
	    else
	    {
	    	$all_ref_barcode_no=" and barcode_no in($all_ref_barcode_nos)";	 
	    }

	    $issued_barcode_arr=return_library_array("select barcode_no from pro_roll_details where entry_form=61 and status_active=1 and is_deleted=0 $all_ref_barcode_no and is_returned !=1 ","barcode_no", "barcode_no");

	    $delv_arr=return_library_array("select barcode_num, grey_sys_id from pro_grey_prod_delivery_dtls where entry_form=56 $all_ref_barcode_no", "barcode_num", "grey_sys_id");
	}


	$i=1;
	//print_r($data_array);die;
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
			if($row[csf('entry_form')]==2) // 2=>"Grey Receive",
			{
				if($row[csf('receive_basis')]==2) $program_no=$row[csf('booking_id')];
			}
			else if($row[csf('entry_form')]==58 || $row[csf('entry_form')]==110 || $row[csf('entry_form')]==180) // 58=>"Knit Grey Fabric Receive Roll",
			{
				$program_no=$programArr[$delv_arr[$row[csf('barcode_no')]]];
				$row[csf('roll_id')]=$row[csf('roll_id_prev')];
			}			
		?>
			<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
				<td width="40" align="center" valign="middle"><input type="checkbox" id="tbl_<? echo $i;?>" name="check[]" checked/></td> 
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
				<td align="right" style="padding-right:2px"><? echo $row[csf('qnty')]; ?>
                	<input type="hidden" name="barcodeNo[]" id="barcodeNo_<? echo $i; ?>" value="<? echo $row[csf('barcode_no')]; ?>"/>					<input type="hidden" name="rollNo[]" id="rollNo_<? echo $i; ?>" value="<? echo $row[csf('roll_no')]; ?>"/>
                    <input type="hidden" name="progId[]" id="progId_<? echo $i; ?>" value="<? echo $program_no; ?>"/>
                    <input type="hidden" name="productId[]" id="productId_<? echo $i; ?>" value="<? echo $row[csf('prod_id')]; ?>"/>
                    <input type="hidden" name="rollId[]" id="rollId_<? echo $i; ?>" value="<? echo $row[csf('roll_id')]; ?>"/>
                    <input type="hidden" name="rollWgt[]" id="rollWgt_<? echo $i; ?>" value="<? echo $row[csf('qnty')]; ?>"/>
                    <input type="hidden" name="yarnLot[]" id="yarnLot_<? echo $i; ?>" value="<? echo $row[csf('yarn_lot')]; ?>"/>
                    <input type="hidden" name="yarnCount[]" id="yarnCount_<? echo $i; ?>" value="<? echo $row[csf('yarn_count')]; ?>"/>					<input type="hidden" name="stichLn[]" id="stichLn_<? echo $i; ?>" value="<? echo $row[csf('stitch_length')]; ?>"/>					<input type="hidden" name="brandId[]" id="brandId_<? echo $i; ?>" value="<? echo $row[csf('brand_id')]; ?>"/>
                    <input type="hidden" name="rack[]" id="rack_<? echo $i; ?>" value="<? echo $row[csf('rack')]; ?>"/>
                    <input type="hidden" name="shelf[]" id="shelf_<? echo $i; ?>" value="<? echo $row[csf('self')]; ?>"/>
                    <input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>" value=""/>
                    <input type="hidden" name="transIdFrom[]" id="transIdFrom_<? echo $i; ?>" value=""/>
                    <input type="hidden" name="transIdTo[]" id="transIdTo_<? echo $i; ?>" value=""/>
                    <input type="hidden" name="rolltableId[]" id="rolltableId_<? echo $i; ?>" value=""/>
                    <input type="hidden" name="transRollId[]" id="transRollId_<? echo $i; ?>" value="<? echo $transRollId ?>"/>
                    <input type="hidden" name="storeId[]" id="storeId_<? echo $i; ?>" value="<? echo $row[csf('store_id')] ?>"/>
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
	
	$delv_arr=return_library_array("select barcode_num, grey_sys_id from pro_grey_prod_delivery_dtls where entry_form=56 and order_id=$order_id", "barcode_num", "grey_sys_id");
	$trans_arr=return_library_array("select barcode_no, mst_id from pro_roll_details where entry_form=2", "barcode_no", "mst_id");

	$issued_barcode_arr=return_library_array("select barcode_no from pro_roll_details where entry_form=61 and status_active=1 and is_deleted=0 and po_breakdown_id=$order_id and is_returned = 0","barcode_no", "barcode_no");
	
	$transfer_arr=array();
	$transfer_dataArray=sql_select("select a.id, a.trans_id, a.to_trans_id, b.id as roll_id, b.barcode_no,b.re_transfer from inv_item_transfer_dtls a, pro_roll_details b where a.id=b.dtls_id and a.mst_id=$mst_id and b.mst_id=$mst_id and b.entry_form=180 and b.transfer_criteria=8 and b.status_active=1 and b.is_deleted=0");
	foreach($transfer_dataArray as $row)
	{
		$transfer_arr[$row[csf('barcode_no')]]['dtls_id']=$row[csf('id')];
		$transfer_arr[$row[csf('barcode_no')]]['from_trans_id']=$row[csf('trans_id')];
		$transfer_arr[$row[csf('barcode_no')]]['to_trans_id']=$row[csf('to_trans_id')];
		$transfer_arr[$row[csf('barcode_no')]]['rolltableId']=$row[csf('roll_id')];
		$re_trans_arr[$row[csf('barcode_no')]] = $row[csf('re_transfer')];
	}
	
	$programArr=return_library_array("select a.id, a.booking_id from inv_receive_master a, pro_roll_details b where a.id=b.mst_id and a.entry_form=2 and a.receive_basis=2 and b.entry_form=2 and b.po_breakdown_id=$order_id and b.status_active=1 and b.is_deleted=0", "id", "booking_id");
		
	$sql="select a.id, a.entry_form, 0 as receive_basis, 0 as booking_id, b.from_prod_id as prod_id, b.yarn_lot, b.y_count as yarn_count, b.stitch_length, b.brand_id, b.to_rack as rack, b.to_shelf as self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 3 as type , b.to_store as store_id
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(180) and c.entry_form in(180) and c.status_active=1 and c.is_deleted=0 and c.mst_id=$mst_id
	order by barcode_no";
	
	//echo $sql;
	
	$data_array=sql_select($sql);	
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
			if($transfer_arr[$row[csf('barcode_no')]]['dtls_id']=="")
			{
				$checked=""; 	
			}
			else $checked="checked"; 
			
			if($re_trans_arr[$row[csf('barcode_no')]]==0)
			{
				$disabled=""; 	
			}
			else $disabled="disabled";
			
			$dtls_id=$transfer_arr[$row[csf('barcode_no')]]['dtls_id'];
			$from_trans_id=$transfer_arr[$row[csf('barcode_no')]]['from_trans_id'];
			$to_trans_id=$transfer_arr[$row[csf('barcode_no')]]['to_trans_id'];
			$rolltableId=$transfer_arr[$row[csf('barcode_no')]]['rolltableId'];
		?>
			<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
				<td width="40" align="center" valign="middle"><input type="checkbox" id="tbl_<? echo $i;?>" <? echo $checked." ".$disabled; ?> name="check[]" /></td> 
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
				<td align="right" style="padding-right:2px"><? echo $row[csf('qnty')]; ?>
                	<input type="hidden" name="barcodeNo[]" id="barcodeNo_<? echo $i; ?>" value="<? echo $row[csf('barcode_no')]; ?>"/>					<input type="hidden" name="rollNo[]" id="rollNo_<? echo $i; ?>" value="<? echo $row[csf('roll_no')]; ?>"/>
                    <input type="hidden" name="progId[]" id="progId_<? echo $i; ?>" value="<? echo $program_no; ?>"/>
                    <input type="hidden" name="productId[]" id="productId_<? echo $i; ?>" value="<? echo $row[csf('prod_id')]; ?>"/>
                    <input type="hidden" name="rollId[]" id="rollId_<? echo $i; ?>" value="<? echo $row[csf('roll_id')]; ?>"/>
                    <input type="hidden" name="rollWgt[]" id="rollWgt_<? echo $i; ?>" value="<? echo $row[csf('qnty')]; ?>"/>
                    <input type="hidden" name="yarnLot[]" id="yarnLot_<? echo $i; ?>" value="<? echo $row[csf('yarn_lot')]; ?>"/>
                    <input type="hidden" name="yarnCount[]" id="yarnCount_<? echo $i; ?>" value="<? echo $row[csf('yarn_count')]; ?>"/>		          	<input type="hidden" name="stichLn[]" id="stichLn_<? echo $i; ?>" value="<? echo $row[csf('stitch_length')]; ?>"/>					<input type="hidden" name="brandId[]" id="brandId_<? echo $i; ?>" value="<? echo $row[csf('brand_id')]; ?>"/>
                    <input type="hidden" name="rack[]" id="rack_<? echo $i; ?>" value="<? echo $row[csf('rack')]; ?>"/>
                    <input type="hidden" name="shelf[]" id="shelf_<? echo $i; ?>" value="<? echo $row[csf('self')]; ?>"/>
                    <input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>" value="<? echo $dtls_id; ?>"/>
                    <input type="hidden" name="transIdFrom[]" id="transIdFrom_<? echo $i; ?>" value="<? echo $from_trans_id; ?>"/>
                    <input type="hidden" name="transIdTo[]" id="transIdTo_<? echo $i; ?>" value="<? echo $to_trans_id; ?>"/>
                    <input type="hidden" name="rolltableId[]" id="rolltableId_<? echo $i; ?>" value="<? echo $rolltableId; ?>"/>
                    <input type="hidden" name="transRollId[]" id="transRollId_<? echo $i; ?>" value="<? echo $transRollId ?>"/>
                    <input type="hidden" name="storeId[]" id="storeId_<? echo $i; ?>" value="<? echo $row[csf('store_id')]; ?>"/>
                </td>
			</tr>
		<? 
			$i++; 
		}
	} 
	exit();
}

if ($action=="sampleToSampleTransfer_popup")
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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>, 'create_transfer_search_list_view', 'search_div', 'grey_fabric_sample_to_sample_roll_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
	
 	$sql="select id, transfer_prefix_number, $year_field transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria, item_category from inv_item_transfer_mst where item_category=13 and company_id=$company_id and $search_field like '$search_string' and transfer_criteria=8 and entry_form=180 and status_active=1 and is_deleted=0 order by id";
	
	$arr=array(3=>$company_arr,5=>$item_transfer_criteria,6=>$item_category);

	echo  create_list_view("tbl_list_search", "Transfer ID,Year,Challan No,Company,Transfer Date,Transfer Criteria,Item Category", "80,70,100,80,90,130","750","250",0, $sql, "js_set_value", "id", "", 1, "0,0,0,company_id,0,transfer_criteria,item_category", $arr, "transfer_prefix_number,year,challan_no,company_id,transfer_date,transfer_criteria,item_category", '','','0,0,0,0,3,0,0');
	
	exit();
}

//data save update delete here------------------------------//
if($action=="save_update_delete")
{	 
	$process = array( &$_POST );	
	
	extract(check_magic_quote_gpc( $process )); 
		
	for($k=1;$k<=$total_row;$k++)
	{ 
		$productId = "productId_".$k;
		$prod_ids.=$$productId.",";
	}
	
	$prod_ids = implode(",",array_unique(explode(",",chop($prod_ids,','))));

	$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id in ($prod_ids) and transaction_type in (1,4,5)", "max_date");		     
	
	$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
	
	$trans_date = date("Y-m-d", strtotime(str_replace("'","",$txt_transfer_date)));
	
	$tdate = "'".date("d-M-Y",strtotime($trans_date))."'"; 

	if ($trans_date < $max_recv_date) 
	{
		echo "20**Transfer Date Can not Be Less Than Last Receive Date Of These Lot";
		die;
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
	
			$id = return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst", $con);
			$new_transfer_system_id = explode("*", return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst",$con,1,$cbo_company_id,"GFSTSTE",180,date("Y",time()),13 ));
					 
			$field_array="id, transfer_prefix, transfer_prefix_number, transfer_system_id, company_id, challan_no, transfer_date, entry_form, transfer_criteria, to_company, from_order_id, to_order_id,from_samp_dtls_id,to_samp_dtls_id, item_category, inserted_by, insert_date";
			
			//echo $from_sameple."===".$to_sameple."===".$cbo_company_id."==".$txt_challan_no; die;
			
			$data_array="(".$id.",'".$new_transfer_system_id[1]."',".$new_transfer_system_id[2].",'".$new_transfer_system_id[0]."',".$cbo_company_id.",".$txt_challan_no.",".$tdate.",180,8,0,".$from_sameple.",".$to_sameple.",".$txt_trans_from_sam_book_dtls_id.",".$txt_trans_to_sam_book_dtls_id.",13,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
						
			$transfer_recv_num=$new_transfer_system_id[0];
			$transfer_update_id=$id;
		}
		else
		{
			$field_array_update="challan_no*transfer_date*from_order_id*to_order_id*from_samp_dtls_id*to_samp_dtls_id*updated_by*update_date";
			$data_array_update=$txt_challan_no."*".$tdate."*".$from_sameple."*".$to_sameple."*".$txt_trans_from_sam_book_dtls_id."*".$txt_trans_to_sam_book_dtls_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
						
			$transfer_recv_num=str_replace("'","",$txt_system_id);
			$transfer_update_id=str_replace("'","",$update_id);
		}
		
		$rate=0; $amount=0;
		//$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
		$field_array_trans="id, mst_id, company_id, prod_id, item_category, transaction_type, transaction_date, order_id, cons_uom, cons_quantity, cons_rate, cons_amount, rack, self, program_no, stitch_length, store_id, inserted_by, insert_date";
		
		//$id_dtls=return_next_id( "id", "inv_item_transfer_dtls", 1 ) ; 
		$field_array_dtls="id, mst_id, trans_id, to_trans_id, from_prod_id, item_category, transfer_qnty, roll, rate, transfer_value, uom, y_count, brand_id, yarn_lot, rack, shelf, to_rack, to_shelf, from_program, to_program, stitch_length, from_store, to_store, inserted_by, insert_date";		
		
		//$id_roll = return_next_id( "id", "pro_roll_details", 1 );
		$field_array_roll="id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, roll_no, roll_id, from_roll_id, is_transfer, transfer_criteria, booking_without_order, booking_no, inserted_by, insert_date";
				
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
			
			$rollIds.=$$transRollId.",";
			
			$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
			$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
			
			if($data_array_trans!="") $data_array_trans.=",";
			$data_array_trans.="(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",".$$productId.",13,6,".$tdate.",".$from_sameple.",12,".$$rollWgt.",".$rate.",".$amount.",".$$rack.",".$$shelf.",".$$progId.",".$$stichLn.",".$$storeId.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			$from_trans_id=$id_trans;
			//$id_trans=$id_trans+1;
			$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			$data_array_trans.=",(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",".$$productId.",13,5,".$tdate.",".$to_sameple.",12,".$$rollWgt.",".$rate.",".$amount.",".$$rack.",".$$shelf.",".$$progId.",".$$stichLn.",".$$storeId.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			if($data_array_dtls!="") $data_array_dtls.=",";
			$data_array_dtls.="(".$id_dtls.",".$transfer_update_id.",".$from_trans_id.",".$id_trans.",".$$productId.",13,".$$rollWgt.",".$$rollNo.",".$rate.",".$amount.",12,".$$yarnCount.",".$$brandId.",".$$yarnLot.",".$$rack.",".$$shelf.",".$$rack.",".$$shelf.",".$$progId.",".$$progId.",".$$stichLn.",".$$storeId.",".$$storeId.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			if($data_array_roll!="") $data_array_roll.= ",";
			$data_array_roll.="(".$id_roll.",".$$barcodeNo.",".$transfer_update_id.",".$id_dtls.",".$to_sameple.",180,".$$rollWgt.",".$$rollNo.",".$$rollId.",".$$transRollId.",5,8,1,'".$to_sample_booking_no."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$all_trans_roll_id.=$$transRollId.",";
		}
		
		if(str_replace("'","",$update_id)=="")
		{
			$rID=sql_insert("inv_item_transfer_mst",$field_array,$data_array,0);
		}
		else
		{
			$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		}
	
		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		$rID3=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);
		$rID4=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,1);
		$rollIds=chop($rollIds,',');
		$rID5=sql_multirow_update("pro_roll_details","is_transfer*transfer_criteria*re_transfer","6*8*1","id",$rollIds,0);
		
		/*
		$all_trans_roll_id=chop($all_trans_roll_id,',');
		//echo "10** $rollIds == $all_trans_roll_id";die;
		if($all_trans_roll_id!="")
		{
			$rID6=sql_multirow_update("pro_roll_details","re_transfer","1","id",$all_trans_roll_id,0);
		}
		*/
		
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5)
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
			if($rID && $rID2 && $rID3 && $rID4 && $rID5)
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
         * List of fields that will not change/update on update button event
         * fields=> from_order_id*to_order_id*
         * data=> $from_sameple."*".$to_sameple."*".
         */
		$field_array_update="challan_no*transfer_date*updated_by*update_date";
		$data_array_update=$txt_challan_no."*".$tdate."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rate=0; $amount=0;
		//$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
		$field_array_trans="id, mst_id, company_id, prod_id, item_category, transaction_type, transaction_date, order_id, cons_uom, cons_quantity, cons_rate, cons_amount, rack, self, program_no, stitch_length, store_id, inserted_by, insert_date";
		$field_array_trans_update="prod_id*transaction_date*order_id*cons_quantity*cons_rate*cons_amount*rack*self*program_no*stitch_length*updated_by*update_date";
		
		//$id_dtls=return_next_id( "id", "inv_item_transfer_dtls", 1 ) ; 
		$field_array_dtls="id, mst_id, trans_id, to_trans_id, from_prod_id, item_category, transfer_qnty, roll, rate, transfer_value, uom, y_count, brand_id, yarn_lot, rack, shelf, to_rack, to_shelf, from_program, to_program, stitch_length, from_store, to_store, inserted_by, insert_date";		
		$field_array_dtls_update="from_prod_id*transfer_qnty*roll*rate*transfer_value*y_count*brand_id*yarn_lot*rack*shelf*to_rack*to_shelf*from_program*to_program*stitch_length*updated_by*update_date";
		
		//$id_roll = return_next_id( "id", "pro_roll_details", 1 );
		$field_array_roll="id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, roll_no, roll_id, from_roll_id, is_transfer, transfer_criteria, booking_without_order, booking_no, inserted_by, insert_date";
		$field_array_updateroll="qnty*roll_no*updated_by*update_date";
		
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
			
			$rollIds.=$$transRollId.",";
			
			if(str_replace("'","",$$rolltableId)>0)
			{
				$update_dtls_id.=str_replace("'","",$$dtlsId).",";
				
				$transId_arr[]=str_replace("'","",$$transIdFrom);
				$data_array_update_trans[str_replace("'","",$$transIdFrom)]=explode("*",($$productId."*".$tdate."*".$from_sameple."*".$$rollWgt."*".$rate."*".$amount."*".$$rack."*".$$shelf."*".$$progId."*".$$stichLn."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				
				$transId_arr[]=str_replace("'","",$$transIdTo);
				$data_array_update_trans[str_replace("'","",$$transIdTo)]=explode("*",($$productId."*".$tdate."*".$to_sameple."*".$$rollWgt."*".$rate."*".$amount."*".$$rack."*".$$shelf."*".$$progId."*".$$stichLn."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				
				$dtlsId_arr[]=str_replace("'","",$$dtlsId);
				$data_array_update_dtls[str_replace("'","",$$dtlsId)]=explode("*",($$productId."*".$$rollWgt."*".$$rollNo."*".$rate."*".$amount."*".$$yarnCount."*".$$brandId."*".$$yarnLot."*".$$rack."*".$$shelf."*".$$rack."*".$$shelf."*".$$progId."*".$$progId."*".$$stichLn."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				
				$rollId_arr[]=str_replace("'","",$$rolltableId);
				$data_array_update_roll[str_replace("'","",$$rolltableId)]=explode("*",($$rollWgt."*".$$rollNo."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				
				$dtlsIdProp=str_replace("'","",$$dtlsId);
				$transIdfromProp=str_replace("'","",$$transIdFrom);
				$transIdtoProp=str_replace("'","",$$transIdTo);
			}
			else
			{
				$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
				$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
				
				if($data_array_trans!="") $data_array_trans.=",";
				$data_array_trans.="(".$id_trans.",".$update_id.",".$cbo_company_id.",".$$productId.",13,6,".$tdate.",".$from_sameple.",12,".$$rollWgt.",".$rate.",".$amount.",".$$rack.",".$$shelf.",".$$progId.",".$$stichLn.",".$$storeId.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				$transIdfromProp=$id_trans;
				//$id_trans=$id_trans+1;
				$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$transIdtoProp=$id_trans;
				$data_array_trans.=",(".$id_trans.",".$update_id.",".$cbo_company_id.",".$$productId.",13,5,".$txt_transfer_date.",".$to_sameple.",12,".$$rollWgt.",".$rate.",".$amount.",".$$rack.",".$$shelf.",".$$progId.",".$$stichLn.",".$$storeId.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$id_dtls.",".$update_id.",".$transIdfromProp.",".$transIdtoProp.",".$$productId.",13,".$$rollWgt.",".$$rollNo.",".$rate.",".$amount.",12,".$$yarnCount.",".$$brandId.",".$$yarnLot.",".$$rack.",".$$shelf.",".$$rack.",".$$shelf.",".$$progId.",".$$progId.",".$$stichLn.",".$$storeId.",".$$storeId.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				if($data_array_roll!="") $data_array_roll.= ",";
				$data_array_roll.="(".$id_roll.",".$$barcodeNo.",".$update_id.",".$id_dtls.",".$to_sameple.",180,".$$rollWgt.",".$$rollNo.",".$$rollId.",".$$transRollId.",5,8,1,'".$to_sample_booking_no."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				//$id_roll=$id_roll+1;
				//$id_trans=$id_trans+1;
				$dtlsIdProp=$id_dtls;
				//$id_dtls=$id_dtls+1;
				$all_trans_roll_id.=$$transRollId.",";
			}
		}
		
		if($txt_deleted_id!="")
		{
			//echo "10**5**didar";die;
			$deletedIds=explode(",",$txt_deleted_id); $dtlsIDDel=''; $transIDDel=''; $rollIDDel=''; $rollIDactive='';$delBarcodeNo='';
			foreach($deletedIds as $delIds)
			{
				$delIds=explode("_",$delIds);
				if($dtlsIDDel=="")
				{
					$dtlsIDDel=$delIds[0];
					$transIDDel=$delIds[1].",".$delIds[2];
					$rollIDDel=$delIds[3];
					$rollIDactive=$delIds[4];
					$delBarcodeNo=$delIds[5];
				}
				else
				{
					$dtlsIDDel.=",".$delIds[0];
					$transIDDel.=",".$delIds[1].",".$delIds[2];
					$rollIDDel.=",".$delIds[3];
					$rollIDactive.=",".$delIds[4];
					$delBarcodeNo.=",".$delIds[5];
				}
			}
			
			$prev_rol_id_sql=sql_select("select from_roll_id from pro_roll_details where id in($rollIDDel)");
			$prev_rol_id="";
			foreach($prev_rol_id_sql as $row)
			{
				$prev_rol_id.=$row[csf("from_roll_id")].",";
			}


			if($delBarcodeNo != "")
			{
				$check_sql=sql_select("SELECT a.barcode_no , b.issue_number as system_no, a.entry_form, 'Issue' as msg_source from pro_roll_details a, inv_issue_master b where a.mst_id = b.id and a.entry_form = 61 and b.entry_form = 61 and a.is_returned != 1 and  a.status_active=1 and  b.status_active=1 and a.barcode_no in ($delBarcodeNo) 
				union all 
				select a.barcode_no , b.transfer_system_id as system_no, a.entry_form, 'Transfer' as msg_source from pro_roll_details a, inv_item_transfer_mst b where a.mst_id = b.id and a.entry_form = 180 and b.entry_form = 180 and a.re_transfer = 0 and  a.status_active=1 and  b.status_active=1 and a.barcode_no in ($delBarcodeNo) and a.id not in ($rollIDDel) ");

				$msg = "";
				foreach ($check_sql as $val) 
				{
					$msg .= $val[csf("msg_source")]." Found. Barcode :".$val[csf("barcode_no")]." chalan no: ".$val[csf("system_no")]."\n";
				}

				if($msg)
				{
					echo "20**".$msg;
					disconnect($con);
					die;
				}


				$splited_roll_sql=sql_select("select barcode_no,split_from_id from pro_roll_split where status_active =1 and barcode_no in ($delBarcodeNo)");

				foreach($splited_roll_sql as $bar)
				{ 
					$splited_roll_ref[$bar[csf('barcode_no')]][$bar[csf('split_from_id')]]=$bar[csf('barcode_no')];
				}

				$child_split_sql=sql_select("select barcode_no, id from pro_roll_details where roll_split_from >0 and barcode_no in ($delBarcodeNo) and entry_form = 180 order by barcode_no");
				foreach($child_split_sql as $bar)
				{ 
					$child_split_arr[$bar[csf('barcode_no')]][$bar[csf('id')]]=$bar[csf('barcode_no')];
				}
				
				foreach($deletedIds as $delIds)
				{
					$delIds=explode("_",$delIds);
					if($splited_roll_ref[$delIds[5]][$delIds[3]] !="" || $child_split_arr[$delIds[5]][$delIds[3]] !="")
					{
						echo "20**"."Split Found. barcode no: ".$delIds[5];
						disconnect($con);
						die;
					}
				}
				
			}


		}
		//echo "10**fail";die;
		$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0;
		
		if(count($data_array_update_roll)>0)
		{
			$rID2=execute_query(bulk_update_sql_statement("inv_transaction","id",$field_array_trans_update,$data_array_update_trans,$transId_arr));
			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=0; 
			}
			
			$rID3=execute_query(bulk_update_sql_statement("inv_item_transfer_dtls","id",$field_array_dtls_update,$data_array_update_dtls,$dtlsId_arr));
			if($flag==1) 
			{
				if($rID3) $flag=1; else $flag=0; 
			}
			
			$rID4=execute_query(bulk_update_sql_statement("pro_roll_details","id",$field_array_updateroll,$data_array_update_roll,$rollId_arr));
			if($flag==1) 
			{
				if($rID4) $flag=1; else $flag=0; 
			}
			
		}
		
		if($data_array_dtls!="")
		{
			$rIDinv=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
			if($flag==1) 
			{
				if($rIDinv) $flag=1; else $flag=0; 
			} 
			
			$rIDDtls=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);
			if($flag==1) 
			{
				if($rIDDtls) $flag=1; else $flag=0; 
			} 
			
			$rIDRoll=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,1);
			if($flag==1) 
			{
				if($rIDRoll) $flag=1; else $flag=0; 
			} 
		}
		
		/*
		As new roll are not added and existing rolls not required to change these field so this block is commented 

		$rollIds=chop($rollIds,',');
		if($rollIds!="")
		{
			$rID5=sql_multirow_update("pro_roll_details","is_transfer*transfer_criteria*re_transfer","6*8*1","id",$rollIds,0);
			if($flag==1) 
			{
				if($rID5) $flag=1; else $flag=0; 
			} 
		}
		*/
		
		/*$all_trans_roll_id=chop($all_trans_roll_id,',');
		if($all_trans_roll_id!="")
		{
			$rID6=sql_multirow_update("pro_roll_details","re_transfer","1","id",$all_trans_roll_id,0);
			if($flag==1) 
			{
				if($rID6) $flag=1; else $flag=0; 
			} 
		}*/
		
		if($txt_deleted_id!="")
		{
			$prev_rol_id=chop($prev_rol_id,",");
			//echo "10**5##select from_roll_id from pro_roll_details where id in($rollIDDel)";die;
			$field_array_status="updated_by*update_date*status_active*is_deleted";
			$data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
			$statusChangeTrans=sql_multirow_update("inv_transaction",$field_array_status,$data_array_status,"id",$transIDDel,0);
			$statusChangeDtls=sql_multirow_update("inv_item_transfer_dtls",$field_array_status,$data_array_status,"id",$dtlsIDDel,0);
			$statusChangeRoll=sql_multirow_update("pro_roll_details",$field_array_status,$data_array_status,"id",$rollIDDel,0);
			$activeRoll=sql_multirow_update("pro_roll_details","is_transfer*transfer_criteria*re_transfer","0*0*0","id",$prev_rol_id,0);
			
			if($flag==1) 
			{
				if($statusChangeTrans && $statusChangeDtls && $statusChangeRoll && $activeRoll) $flag=1; else $flag=0; 
			} 
		}
		
		
		if($dtlsIDDel=="")
		{
			$update_dtls_id=chop($update_dtls_id,',');
		}
		else
		{
			$update_dtls_id=$update_dtls_id.$dtlsIDDel;
		}
		
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

if ($action=="grey_fabric_sample_to_sample_transfer_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	
	$sql="select id, transfer_system_id, transfer_date, challan_no, from_order_id, to_order_id, item_category from inv_item_transfer_mst a where id='$data[1]' and company_id='$data[0]'";
	//echo $sql;die;
	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=13","id","product_name_details");
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');	
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');	
	
	$poDataArray=sql_select("select b.id,a.buyer_name,a.style_ref_no,a.job_no,b.po_number,b.pub_shipment_date, b.file_no, b.grouping as ref_no, (a.total_set_qnty*b.po_quantity) as qty from wo_po_break_down b,wo_po_details_master a where  a.job_no=b.job_no_mst and a.company_name=$data[0] and b.status_active=1 and b.is_deleted=0 ");
	$job_array=array(); //$all_job_id='';
	foreach($poDataArray as $row)
	{
		$job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
		$job_array[$row[csf('id')]]['job']=$row[csf('job_no')];
		$job_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
		$job_array[$row[csf('id')]]['no']=$row[csf('po_number')];
		$job_array[$row[csf('id')]]['date']=$row[csf('pub_shipment_date')];
		$job_array[$row[csf('id')]]['qty']=$row[csf('qty')];
		$job_array[$row[csf('id')]]['file']=$row[csf('file_no')];
		$job_array[$row[csf('id')]]['ref']=$row[csf('ref_no')];
	} 
	
	$style_name_array=return_library_array( "select id, style_ref_no from sample_development_mst",'id','style_ref_no');
	
	$sampledata_from_array=sql_select("select a.id as booking_id, a.booking_no, a.booking_no_prefix_num, a.buyer_id, b.style_id, b.body_part, b.grey_fabric
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
	where a.booking_no=b.booking_no and a.id=".$dataArray[0][csf('from_order_id')]."");
	
	$sampledata_to_array=sql_select("select a.id as booking_id, a.booking_no, a.booking_no_prefix_num, a.buyer_id, b.style_id, b.body_part, b.grey_fabric
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
	where a.booking_no=b.booking_no and a.id=".$dataArray[0][csf('to_order_id')]."");

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
            <td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo $data[2]; ?> Report</u></strong></td>
        </tr>
        <tr>
        	<td width="125"><strong>Transfer ID :</strong></td><td width="175px"><? echo $dataArray[0][csf('transfer_system_id')]; ?></td>
            <td width="125"><strong>Transfer Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
            <td width="125"><strong>Challan No.:</strong></td><td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
        </tr>
    </table>
    <table width="900" cellspacing="0" align="right" style="margin-top:5px;">
    	<tr>
        	<td>
                <table width="100%" cellspacing="0" align="right">
                 	<tr>
                    	<td colspan="4" align="center" style="font-weight:bold; font-size:18px;"><u>From Sample</u></td>
                    </tr>
                    <tr>
                    	<td width="100">Booking No:</td>
                        <td width="340" colspan="3">&nbsp;<? echo $sampledata_from_array[0][csf('booking_no')]; ?></td>
                       
                    </tr>
                    <tr>
                    	<td>Buyer:</td>
                        <td >&nbsp;<? echo $buyer_library[$sampledata_from_array[0][csf('buyer_id')]]; ?></td>
                        <td width="100">Quantity:</td>
                        <td>&nbsp;<? echo $sampledata_to_array[0][csf('grey_fabric')]; ?></td>
                        
                    </tr>
                    <tr>
                    	<td>Style Ref. :</td>
                        <td colspan="3">&nbsp;<? echo $style_name_array[$sampledata_from_array[0][csf('style_id')]]; ?></td>
                        
                    </tr> 
                    <tr>
                    	<td>Body Part:</td>
                        <td colspan="3">&nbsp;<? echo $body_part[$sampledata_from_array[0][csf('body_part')]]; ?></td>
              
                    </tr>
                </table>
            </td>
            <td>
                <table width="100%" cellspacing="0" align="right">
                 	<tr>
                    	<td colspan="4" align="center" style="font-weight:bold; font-size:18px;"><u>To Sample</u></td>
                    </tr>
                    <tr>
                    	<td width="100">Booking No:</td>
                        <td width="340" colspan="3">&nbsp;<? echo $sampledata_to_array[0][csf('booking_no')]; ?></td>
                       
                    </tr>
                    <tr>
                    	<td>Buyer:</td>
                        <td >&nbsp;<? echo $buyer_library[$sampledata_to_array[0][csf('buyer_id')]]; ?></td>
                        <td width="100">Quantity:</td>
                        <td>&nbsp;<? echo $sampledata_to_array[0][csf('grey_fabric')]; ?></td>
                        
                    </tr>
                    <tr>
                    	<td>Style Ref. :</td>
                        <td colspan="3">&nbsp;<? echo $style_name_array[$sampledata_to_array[0][csf('style_id')]]; ?></td>
                        
                    </tr> 
                    <tr>
                    	<td>Body Part:</td>
                        <td colspan="3">&nbsp;<? echo $body_part[$sampledata_to_array[0][csf('body_part')]]; ?></td>
              
                    </tr>
                </table>
            </td>
        </tr>
    </table>
	<br>
    <div style="width:100%;">
        <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="80">Barcode No</th>
                <th width="60">Roll No</th>
                <th width="180">Fabric Description</th>
                <th width="80">Y/Count</th>
                <th width="70">Y/Brand</th>
                <th width="80">Y/Lot</th>
                <th width="55">Rack</th>
                <th width="55">Shelf</th>
                <th width="70">Stitch Length</th>
                <th width="60">UOM</th>
                <th width="100">Transfered Qnty</th>
            </thead>
            <tbody> 
			<?
            $sql_dtls="select a.from_prod_id, a.transfer_qnty, a.uom, a.y_count, a.brand_id, a.yarn_lot, a.to_rack, a.to_shelf, a.stitch_length, b.barcode_no, b.roll_no from inv_item_transfer_dtls a, pro_roll_details b where a.id=b.dtls_id and b.entry_form=180 and a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.barcode_no";
            
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
                        <td><? echo $row[csf("roll_no")]; ?></td>
                        <td><? echo $product_arr[$row[csf("from_prod_id")]]; ?></td>
                        <td><? echo $ycount; ?></td>
                        <td><? echo $brand_arr[$row[csf("brand_id")]]; ?></td>
                        <td><? echo $row[csf("yarn_lot")]; ?></td>
                        <td><? echo $row[csf("to_rack")]; ?></td>
                        <td><? echo $row[csf("to_shelf")]; ?></td>
                        <td><? echo $row[csf("stitch_length")]; ?></td>
                        <td align="center"><? echo $unit_of_measurement[$row[csf("uom")]]; ?></td>
                        <td align="right"><? echo $row[csf("transfer_qnty")]; ?></td>
                    </tr>
			<? 
            	$i++; 
            }
            ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="11" align="right"><strong>Total </strong></td>
                    <td align="right"><?php echo $transfer_qnty_sum; ?></td>
                </tr>                           
            </tfoot>
        </table>
        <br>
         
         <div style="margin-left:30px;">
		 <?
            echo signature_table(19, $data[0], "900px");
         ?>
         </div>
        
	</div>
</div>   
 <?	
 exit();
}
?>
<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name' );
$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name' );
$sample_arr=return_library_array( "select id,sample_name from lib_sample where is_deleted=0 and status_active=1 order by sample_name",'id','sample_name' );
$color_arr=return_library_array( "select id,color_name from lib_color where is_deleted=0 and status_active=1 order by color_name",'id','color_name' );
$style_name_array=return_library_array( "select id, style_ref_no from sample_development_mst",'id','style_ref_no' );
$machine_arr=return_library_array( "select id, machine_no from lib_machine_name",'id','machine_no');	


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
<div align="center" style="width:1214px;">
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
							echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",$data[0] );
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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_sample_no').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value, 'create_sample_search_list_view', 'search_div', 'finish_fabric_sample_to_sample_roll_transfer_controller', 'setFilterGrid(\'tbl_list_sample\',-1);')" style="width:100px;" />
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
<div align="center" style="width:1214px;">
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
							echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",$data[0] );
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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_sample_no').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value, 'create_sample_search_list_view', 'search_div', 'finish_fabric_sample_to_sample_roll_transfer_controller', 'setFilterGrid(\'tbl_list_sample\',-1);')" style="width:100px;" />
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
	$year  = $data[5];
	
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
	{		
		$booing_date = "";

		if ($db_type == 0)
		{
			$year_cond = "and YEAR(a.insert_date)=$year";
		} 
		else if ($db_type == 2) 
		{
			$year_cond = "and to_char(a.insert_date,'YYYY')=$year";
		}
	}
		
	$arr=array (2=>$company_arr,3=>$buyer_arr,4=>$style_name_array,5=>$sample_arr,6=>$body_part,8=>$color_arr);
	
	if($db_type==0) 
	{
		$year_field="YEAR(a.insert_date) as year";
	}
	else if($db_type==2) 
	{
		$year_field="to_char(a.insert_date,'YYYY') as year";
	}else {
		$year_field="";//defined Later
	}
	
	$sql= "select a.id as booking_id, b.id,a.booking_no, $year_field, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.booking_date, a.delivery_date, b.style_id, b.fabric_description,b.sample_type,b.fabric_color,b.grey_fabric, b.body_part
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
	where a.booking_no=b.booking_no and a.booking_type=4 and a.company_id=$company_id and a.buyer_id like '$buyer' and b.booking_no like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $booking_date $year_cond  order by a.id, b.id";

	echo create_list_view("tbl_list_sample", "Booking No,Year,Company,Buyer Name,Style Ref. No,Sample,Body Part,Style Description,Fab. Color,Booking Date, Grey Qnty", "60,60,80,80,100,100,130,160,80,80,60","1100","200",0, $sql , "js_set_value", "booking_id", "", 1, "0,0,company_id,buyer_id,style_id,sample_type,body_part,0,fabric_color,0,0", $arr , "booking_no_prefix_num,year,company_id,buyer_id,style_id,sample_type,body_part,fabric_description,fabric_color,booking_date,grey_fabric", "",'','0,0,0,0,0,0,0,0,0,3');
	
	exit();
}

if($action=="populate_data_to_sample_transfer_from")
{
	$data=explode("**",$data);
	$sample_id=$data[0];
		
	$data_array=sql_select("select a.id as booking_id, a.booking_no, a.booking_no_prefix_num, a.buyer_id, b.style_id, b.body_part, b.grey_fabric
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b  
	where a.booking_no=b.booking_no and a.id=$sample_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	 
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('txt_trans_from_sam_book_no').value 		= '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('txt_trans_from_sam_book_id').value 		= '".$row[csf("booking_id")]."';\n";
		echo "document.getElementById('txt_trans_from_sam_booking_qnty').value 	= '".$row[csf("grey_fabric")]."';\n";
		echo "document.getElementById('cbo_trans_from_buyer_name').value 		= '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('txt_trans_from_style_ref').value 		= '".$style_name_array[$row[csf("style_id")]]."';\n";		echo "document.getElementById('cbo_trans_from_garments_item').value 	= '".$row[csf("body_part")]."';\n";
		echo "$('#txt_trans_from_sam_book_no').attr('disabled','disabled');\n";
		exit();
	}
}

if($action=="populate_data_to_sample_transfer_to")
{
	$data=explode("**",$data);
	$sample_id=$data[0];
	
	$data_array=sql_select("select a.id as booking_id, a.booking_no, a.booking_no_prefix_num, a.buyer_id, b.style_id, b.body_part, b.grey_fabric
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b  
	where a.booking_no=b.booking_no and a.id=$sample_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	 
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('txt_trans_to_sam_book_no').value 	= '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('txt_trans_to_sam_book_id').value 	= '".$row[csf("booking_id")]."';\n";
		echo "document.getElementById('txt_trans_to_sam_booking_qnty').value = '".$row[csf("grey_fabric")]."';\n";
		echo "document.getElementById('cbo_trans_to_buyer_name').value 		= '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('txt_trans_to_style_ref').value 		= '".$style_name_array[$row[csf("style_id")]]."';\n";		echo "document.getElementById('cbo_trans_to_garments_item').value 			= '".$row[csf("body_part")]."';\n";
		echo "$('#txt_trans_to_sam_book_no').attr('disabled','disabled');\n";
		exit();
	}
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
		echo "get_php_form_data('".$row[csf("from_order_id")]."**from'".",'populate_data_to_sample_transfer_from','requires/finish_fabric_sample_to_sample_roll_transfer_controller');\n";
		echo "get_php_form_data('".$row[csf("to_order_id")]."','populate_data_to_sample_transfer_to','requires/finish_fabric_sample_to_sample_roll_transfer_controller');\n";
		echo "$('#cbo_company_id').attr('disabled','disabled');\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_grey_transfer_entry',1,1);\n"; 
		exit();
	}
}

if($action=="show_dtls_list_view")
{
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	if (count($data_array) > 0) {
		foreach ($data_array as $row) {
			if (array_key_exists($row[csf('id')], $composition_arr)) {
				$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
			} else {
				$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
			}
		}
	}
	
	$delv_arr=return_library_array("select barcode_num, grey_sys_id from pro_grey_prod_delivery_dtls where entry_form=67 and order_id=$data", "barcode_num", "grey_sys_id");
	
	$issued_barcode_arr=return_library_array("select barcode_no from pro_roll_details where entry_form=71 and status_active=1 and is_deleted=0 and po_breakdown_id=$data and booking_without_order=1 and barcode_no not in(select barcode_no from pro_roll_details where entry_form in(52,126) and po_breakdown_id=$data and status_active=1 and is_deleted=0 and booking_without_order=1)","barcode_no", "barcode_no");
	
	$batch_arr=return_library_array( "select a.id, a.batch_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and b.po_id=$data",'id','batch_no');
	
	$sql="SELECT a.id, a.entry_form, a.receive_basis, a.booking_id, b.color_id,b.batch_id, b.prod_id,b.fabric_description_id, b.shelf_no as self,  b.rack_no as rack, b.gsm,b.width, b.machine_no_id,c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 1 as type
	from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.item_category=2 and a.entry_form in(7,37,68) and b.trans_id<>0 and c.entry_form in(7,37,68,214) and c.is_transfer!=6 and c.booking_without_order=1 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=$data
	union all
	select a.id, a.entry_form, 0 as receive_basis, 0 as booking_id, b.color_id, 0 as batch_id, b.from_prod_id as prod_id, b.feb_description_id as fabric_description_id, b.shelf as self, b.rack, b.gsm, b.dia_width as width, 0 as machine_no_id,
	c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 1 as type
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0  and c.entry_form in(216) and c.re_transfer!=1 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=$data";
	
	//and b.trans_id<>0
	//echo $sql;
	/*PRO_GREY_PROD_ENTRY_DTLS
		7=>"Finish Fabric Production Entry"
		37=>"Finish Fabric Receive Entry",
		68=>"Finish Fabric Roll Receive By Store",
		181=>"Roll wise Finish Fabric sample To sample Transfer Entry"
		214=>"Roll wise Finish Fabric sample To sample Transfer Entry"
		216=>"Knit Finish Fabric Order To Sample Transfer Roll Wise"
	*/
	//echo $sql;
	//booking_without_order=1

	$data_array=sql_select($sql);	
	$i=1;
	//print_r($data_array);die;
	foreach($data_array as $row)
	{  
		if($issued_barcode_arr[$row[csf('barcode_no')]]=="")
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		
			$color='';
			$color_id=explode(',',$row[csf('color_id')]);
			
			foreach($color_id as $colorId)
			{
				if ($color=='') $color=$color_arr[$colorId]; else $color.=",".$color_arr[$colorId];
			}

			if ($row[csf('fabric_description_id')]>0) {
				$fabric_desc = $composition_arr[$row[csf('fabric_description_id')]];
			} 

			$transRollId=$row[csf('roll_id')];
			
			if($row[csf('entry_form')]==58) // 58=>"Knit Grey Fabric Receive Roll",
			{
				$row[csf('roll_id')]=$row[csf('roll_id_prev')];
			}			
			?>
			<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
				<td width="40" align="center" valign="middle"><input type="checkbox" id="tbl_<? echo $i;?>" name="check[]" checked/></td> 
				<td width="40"><? echo $i; ?></td>
                <td width="80">Batch Number</td>
				<td width="80"><? echo $row[csf('barcode_no')]; ?></td>
				<td width="50"><? echo $row[csf('roll_no')]; ?></td>
				<td width="60"><p><? echo $row[csf('prod_id')]; ?></p></td>
				<td width="180"><p><? echo $fabric_desc; ?>&nbsp;</p></td>
				<td width="50"><? echo $color; ?></td>
				<td width="50" align="center"><? echo $row[csf('gsm')]; ?></td>
				<td width="50" align="center"><? echo $row[csf('width')]; ?></td>
				<td width="55"><p><? echo $row[csf('rack')]; ?>&nbsp;</p></td>
				<td width="55"><p><? echo $row[csf('self')]; ?>&nbsp;</p></td>				
				<td width="50" align="right" style="padding-right:2px"><? echo $row[csf('qnty')]; ?>
                	<input type="hidden" name="barcodeNo[]" id="barcodeNo_<? echo $i; ?>" value="<? echo $row[csf('barcode_no')]; ?>"/>	
                	<input type="hidden" name="rollNo[]" id="rollNo_<? echo $i; ?>" value="<? echo $row[csf('roll_no')]; ?>"/>
                    <input type="hidden" name="productId[]" id="productId_<? echo $i; ?>" value="<? echo $row[csf('prod_id')]; ?>"/>
                    <input type="hidden" name="rollId[]" id="rollId_<? echo $i; ?>" value="<? echo $row[csf('roll_id')]; ?>"/>
                    <input type="hidden" name="rollWgt[]" id="rollWgt_<? echo $i; ?>" value="<? echo $row[csf('qnty')]; ?>"/>
                    <input type="hidden" name="rack[]" id="rack_<? echo $i; ?>" value="<? echo $row[csf('rack')]; ?>"/>
                    <input type="hidden" name="shelf[]" id="shelf_<? echo $i; ?>" value="<? echo $row[csf('self')]; ?>"/>
                    <input type="hidden" name="feb_description_id_[]" id="feb_description_id_<? echo $i; ?>" value="<? echo $row[csf('fabric_description_id')]; ?>"/>
                    <input type="hidden" name="color_id[]" id="color_id_<? echo $i; ?>" value="<? echo $row[csf('color_id')]; ?>"/>
                    <input type="hidden" name="gsm[]" id="gsm_<? echo $i; ?>" value="<? echo $row[csf('gsm')]; ?>"/>
                    <input type="hidden" name="dia_width[]" id="dia_width_<? echo $i; ?>" value="<? echo $row[csf('width')]; ?>"/>
                    <input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>" value=""/>
                    <input type="hidden" name="transIdFrom[]" id="transIdFrom_<? echo $i; ?>" value=""/>
                    <input type="hidden" name="transIdTo[]" id="transIdTo_<? echo $i; ?>" value=""/>
                    <input type="hidden" name="rolltableId[]" id="rolltableId_<? echo $i; ?>" value=""/>
                    <input type="hidden" name="transRollId[]" id="transRollId_<? echo $i; ?>" value="<? echo $transRollId ?>"/>
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
	$transData = explode("**", $data);

	$mst_id = $transData[0];
	$fromOrder = $transData[1];
	$toOrder = $transData[2];

	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	if (count($data_array) > 0) {
		foreach ($data_array as $row) {
			if (array_key_exists($row[csf('id')], $composition_arr)) {
				$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
			} else {
				$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
			}
		}
	}

	$issued_barcode_arr=return_library_array("select barcode_no from pro_roll_details where entry_form=71 and status_active=1 and is_deleted=0 and po_breakdown_id='$toOrder' and booking_without_order=1 and barcode_no not in(select barcode_no from pro_roll_details where entry_form in(52,126) and po_breakdown_id='$toOrder' and status_active=1 and is_deleted=0 and booking_without_order=1)","barcode_no", "barcode_no");
	
	$transfer_arr=array();
	$transfer_dataArray=sql_select("select a.id, a.trans_id, a.to_trans_id, b.id as roll_id, b.barcode_no from inv_item_transfer_dtls a, pro_roll_details b where a.id=b.dtls_id and a.mst_id=$mst_id and b.mst_id=$mst_id and b.entry_form=214 and b.transfer_criteria=8 and b.status_active=1 and b.is_deleted=0");
	foreach($transfer_dataArray as $row)
	{
		$transfer_arr[$row[csf('barcode_no')]]['dtls_id']=$row[csf('id')];
		$transfer_arr[$row[csf('barcode_no')]]['from_trans_id']=$row[csf('trans_id')];
		$transfer_arr[$row[csf('barcode_no')]]['to_trans_id']=$row[csf('to_trans_id')];
		$transfer_arr[$row[csf('barcode_no')]]['rolltableId']=$row[csf('roll_id')];
	}
		
	$sql="select a.id, a.entry_form, b.from_prod_id as prod_id, b.to_rack as rack, b.to_shelf as self,b.feb_description_id,b.color_id,b.gsm,b.dia_width,c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(214) and c.entry_form in(214) and c.status_active=1 and c.is_deleted=0 and c.mst_id=$mst_id
	order by barcode_no";

	// pro_finish_fabric_rcv_dtls

	//echo $sql;
	
	$data_array=sql_select($sql);	
	$i=1;
	foreach($data_array as $row)
	{  
		if($issued_barcode_arr[$row[csf('barcode_no')]]=="")
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		
			$color='';
			$color_id=explode(',',$row[csf('color_id')]);
			
			foreach($color_id as $colorId)
			{
				if ($color=='') $color=$color_arr[$colorId]; else $color.=",".$color_arr[$colorId];
			}

			if ($row[csf('feb_description_id')]>0) {
				$fabric_desc = $composition_arr[$row[csf('feb_description_id')]];
			} 

			$transRollId=$row[csf('roll_id')];
			
			if($row[csf('entry_form')]==58) // 58=>"Knit Grey Fabric Receive Roll",
			{
				$row[csf('roll_id')]=$row[csf('roll_id_prev')];
			}	

			if($transfer_arr[$row[csf('barcode_no')]]['dtls_id']=="")
			{
				$checked=""; 	
			}
			else $checked="checked"; 
	
			$dtls_id=$transfer_arr[$row[csf('barcode_no')]]['dtls_id'];
			$from_trans_id=$transfer_arr[$row[csf('barcode_no')]]['from_trans_id'];
			$to_trans_id=$transfer_arr[$row[csf('barcode_no')]]['to_trans_id'];
			$rolltableId=$transfer_arr[$row[csf('barcode_no')]]['rolltableId'];
		?>
			<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
				<td width="40" align="center" valign="middle"><input type="checkbox" id="tbl_<? echo $i;?>" <? echo $checked." ".$disabled; ?> name="check[]" /></td> 
				<td width="40"><? echo $i; ?></td>
				<td width="80">Batch Number</td>
				<td width="80"><? echo $row[csf('barcode_no')]; ?></td>
				<td width="50"><? echo $row[csf('roll_no')]; ?></td>
				<td width="60"><p><? echo $row[csf('prod_id')]; ?></p></td>
				<td width="180"><p><? echo $fabric_desc; ?>&nbsp;</p></td>
				<td width="50"><? echo $color; ?></td>
				<td width="50" align="center"><? echo $row[csf('gsm')]; ?></td>
				<td width="50" align="center"><? echo $row[csf('dia_width')]; ?></td>
				<td width="55"><p><? echo $row[csf('rack')]; ?>&nbsp;</p></td>
				<td width="55"><p><? echo $row[csf('self')]; ?>&nbsp;</p></td>
				<td align="right" style="padding-right:2px"><? echo $row[csf('qnty')]; ?>
                	<input type="hidden" name="barcodeNo[]" id="barcodeNo_<? echo $i; ?>" value="<? echo $row[csf('barcode_no')]; ?>"/>	
                	<input type="hidden" name="rollNo[]" id="rollNo_<? echo $i; ?>" value="<? echo $row[csf('roll_no')]; ?>"/>
                	<input type="hidden" name="rollId[]" id="rollId_<? echo $i; ?>" value="<? echo $row[csf('roll_id')]; ?>"/>
                	<input type="hidden" name="rollWgt[]" id="rollWgt_<? echo $i; ?>" value="<? echo $row[csf('qnty')]; ?>"/>
                    <input type="hidden" name="productId[]" id="productId_<? echo $i; ?>" value="<? echo $row[csf('prod_id')]; ?>"/>
                    <input type="hidden" name="rack[]" id="rack_<? echo $i; ?>" value="<? echo $row[csf('rack')]; ?>"/>
                    <input type="hidden" name="shelf[]" id="shelf_<? echo $i; ?>" value="<? echo $row[csf('self')]; ?>"/>
                    
                    <input type="hidden" name="feb_description_id_[]" id="feb_description_id_<? echo $i; ?>" value="<? echo $row[csf('feb_description_id')]; ?>"/>
                    <input type="hidden" name="color_id[]" id="color_id_<? echo $i; ?>" value="<? echo $row[csf('color_id')]; ?>"/>
                    <input type="hidden" name="gsm[]" id="gsm_<? echo $i; ?>" value="<? echo $row[csf('gsm')]; ?>"/>
                    <input type="hidden" name="dia_width[]" id="dia_width_<? echo $i; ?>" value="<? echo $row[csf('dia_width')]; ?>"/>
                    <input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>" value="<? echo $dtls_id; ?>"/>
                    <input type="hidden" name="transIdFrom[]" id="transIdFrom_<? echo $i; ?>" value="<? echo $from_trans_id; ?>"/>
                    <input type="hidden" name="transIdTo[]" id="transIdTo_<? echo $i; ?>" value="<? echo $to_trans_id; ?>"/>
                    <input type="hidden" name="rolltableId[]" id="rolltableId_<? echo $i; ?>" value="<? echo $rolltableId; ?>"/>
                    <input type="hidden" name="transRollId[]" id="transRollId_<? echo $i; ?>" value="<? echo $transRollId ?>"/>
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
			var expData = data.split("_");
			$('#transfer_id').val(expData[0]);
			$('#from_order_id').val(expData[1]);
			$('#to_order_id').val(expData[2]);
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
                        <input type="hidden" name="from_order_id" id="from_order_id" class="text_boxes" value="">
                        <input type="hidden" name="to_order_id" id="to_order_id" class="text_boxes" value="">
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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>, 'create_transfer_search_list_view', 'search_div', 'finish_fabric_sample_to_sample_roll_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
	
 	$sql="select id, transfer_prefix_number, $year_field transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria, item_category,from_order_id,to_order_id from inv_item_transfer_mst where item_category=2 and company_id=$company_id and $search_field like '$search_string' and transfer_criteria=8 and entry_form=214 and status_active=1 and is_deleted=0 order by id";
	
	$arr=array(3=>$company_arr,5=>$item_transfer_criteria,6=>$item_category);

	echo  create_list_view("tbl_list_search", "Transfer ID,Year,Challan No,Company,Transfer Date,Transfer Criteria,Item Category", "80,70,100,80,90,130","750","250",0, $sql, "js_set_value", "id,from_order_id,to_order_id", "", 1, "0,0,0,company_id,0,transfer_criteria,item_category", $arr, "transfer_prefix_number,year,challan_no,company_id,transfer_date,transfer_criteria,item_category", '','','0,0,0,0,3,0,0');
	
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
	
	//$tdate = "'".date("d-M-Y",strtotime($trans_date))."'"; 
	if($db_type == 2) $tdate = "'".date("d-M-Y",strtotime($trans_date))."'"; else $tdate = "'".date("Y-m-d",strtotime($trans_date))."'"; 

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
			$new_transfer_system_id = explode("*", return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst",$con,1,$cbo_company_id,"FFSTSTE",214,date("Y",time()),2 ));
					 
			$field_array="id, transfer_prefix, transfer_prefix_number, transfer_system_id, company_id, challan_no, transfer_date, entry_form, transfer_criteria, to_company, from_order_id, to_order_id, item_category, inserted_by, insert_date";
			
			//echo $from_sameple."===".$to_sameple."===".$cbo_company_id."==".$txt_challan_no; die;
			
			$data_array="(".$id.",'".$new_transfer_system_id[1]."',".$new_transfer_system_id[2].",'".$new_transfer_system_id[0]."',".$cbo_company_id.",'".$txt_challan_no."',".$tdate.",214,8,0,".$from_sameple.",".$to_sameple.",2,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
						
			$transfer_recv_num=$new_transfer_system_id[0];
			$transfer_update_id=$id;
		}
		else
		{
			$field_array_update="challan_no*transfer_date*from_order_id*to_order_id*updated_by*update_date";
			$data_array_update=$txt_challan_no."*".$tdate."*".$from_sameple."*".$to_sameple."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
						
			$transfer_recv_num=str_replace("'","",$txt_system_id);
			$transfer_update_id=str_replace("'","",$update_id);
		}
		
		$rate=0; $amount=0;
		//$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
		$field_array_trans="id, mst_id, company_id, prod_id, item_category, transaction_type, transaction_date, order_id, cons_uom, cons_quantity, cons_rate, cons_amount, rack, self, inserted_by, insert_date";
		
		//$id_dtls=return_next_id( "id", "inv_item_transfer_dtls", 1 ) ; 
		$field_array_dtls="id, mst_id, trans_id, to_trans_id, from_prod_id, item_category, transfer_qnty, roll, rate, transfer_value, uom, rack, shelf, to_rack, to_shelf,feb_description_id,color_id,gsm,dia_width, inserted_by, insert_date";		
		
		//$id_roll = return_next_id( "id", "pro_roll_details", 1 );
		$field_array_roll="id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, roll_no, roll_id, from_roll_id, is_transfer, transfer_criteria, booking_without_order, booking_no, inserted_by, insert_date";
				
		$rollIds='';
		for($j=1;$j<=$total_row;$j++)
		{ 	
			$barcodeNo="barcodeNo_".$j;
			$rollNo="rollNo_".$j;
			$productId="productId_".$j;
			$rollId="rollId_".$j;
			$rollWgt="rollWgt_".$j;
			$rollNo="rollNo_".$j;
			$rack="rack_".$j;
			$shelf="shelf_".$j;
			$color_id="color_id_".$j;
			$gsm="gsm_".$j;
			$dia_width="dia_width_".$j;
			$feb_description_id = "feb_description_id_".$j;
			$transRollId="transRollId_".$j;
			
			$rollIds.=$$transRollId.",";
			
			$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
			$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
			
			if($data_array_trans!="") $data_array_trans.=",";
			$data_array_trans.="(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",".$$productId.",2,6,".$tdate.",".$from_sameple.",12,".$$rollWgt.",".$rate.",".$amount.",".$$rack.",".$$shelf.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			$from_trans_id=$id_trans;
			//$id_trans=$id_trans+1;
			$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			$data_array_trans.=",(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",".$$productId.",2,5,".$tdate.",".$to_sameple.",12,".$$rollWgt.",".$rate.",".$amount.",".$$rack.",".$$shelf.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			if($data_array_dtls!="") $data_array_dtls.=",";
			$data_array_dtls.="(".$id_dtls.",".$transfer_update_id.",".$from_trans_id.",".$id_trans.",".$$productId.",2,".$$rollWgt.",".$$rollNo.",".$rate.",".$amount.",12,".$$rack.",".$$shelf.",".$$rack.",".$$shelf.", ".$$feb_description_id.", ".$$color_id.", ".$$gsm.", ".$$dia_width.", ".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			if($data_array_roll!="") $data_array_roll.= ",";
			$data_array_roll.="(".$id_roll.",".$$barcodeNo.",".$transfer_update_id.",".$id_dtls.",".$to_sameple.",214,".$$rollWgt.",".$$rollNo.",".$$rollId.",".$$transRollId.",5,8,1,'".$to_sample_booking_no."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
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
	

		// echo "10**insert into inv_item_transfer_mst (".$field_array.") Values ".$data_array."";die;
		//echo "5**insert into inv_item_transfer_dtls (".$field_array_dtls.") Values ".$data_array_dtls."";die;
		//echo "5**insert into pro_roll_details (".$field_array_roll.") Values ".$data_array_roll."";die;
		//echo "5**insert into inv_transaction (".$field_array_trans.") Values ".$data_array_trans."";die;

		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		$rID3=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);
		$rID4=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
		$rollIds=chop($rollIds,',');
		$rID5=sql_multirow_update("pro_roll_details","is_transfer*transfer_criteria*re_transfer","6*8*1","id",$rollIds,0);
		
		
		$all_trans_roll_id=chop($all_trans_roll_id,',');
		//echo "10** $rollIds == $all_trans_roll_id";die;
		if($all_trans_roll_id!="")
		{
			$rID6=sql_multirow_update("pro_roll_details","re_transfer","1","id",$all_trans_roll_id,0);
		}
		

		// echo "10**$rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6";oci_rollback($con);disconnect($con);die;
		
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
		
		$field_array_update="challan_no*transfer_date*from_order_id*to_order_id*updated_by*update_date";
		$data_array_update="'".$txt_challan_no."'*".$tdate."*".$from_sameple."*".$to_sameple."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rate=0; $amount=0;
		//$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
		$field_array_trans="id, mst_id, company_id, prod_id, item_category, transaction_type, transaction_date, order_id, cons_uom, cons_quantity, cons_rate, cons_amount, rack, self, inserted_by, insert_date";
		$field_array_trans_update="prod_id*transaction_date*order_id*cons_quantity*cons_rate*cons_amount*rack*self*updated_by*update_date";
		
		//$id_dtls=return_next_id( "id", "inv_item_transfer_dtls", 1 ) ; 
		$field_array_dtls="id, mst_id, trans_id, to_trans_id, from_prod_id, item_category, transfer_qnty, roll, rate, transfer_value, uom,rack, shelf, to_rack, to_shelf,inserted_by, insert_date";		
		$field_array_dtls_update="from_prod_id*transfer_qnty*roll*rate*transfer_value*rack*shelf*to_rack*to_shelf*updated_by*update_date";
		
		//$id_roll = return_next_id( "id", "pro_roll_details", 1 );
		$field_array_roll="id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, roll_no, roll_id, from_roll_id, is_transfer, transfer_criteria, booking_without_order, booking_no, inserted_by, insert_date";
		$field_array_updateroll="qnty*roll_no*updated_by*update_date";
		
		$rollIds=''; $update_dtls_id='';
		for($j=1;$j<=$total_row;$j++)
		{ 	
			$barcodeNo="barcodeNo_".$j;
			$rollNo="rollNo_".$j;
			
			$productId="productId_".$j;
			$rollId="rollId_".$j;
			$rollWgt="rollWgt_".$j;
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
				$data_array_update_trans[str_replace("'","",$$transIdFrom)]=explode("*",($$productId."*".$tdate."*".$from_sameple."*".$$rollWgt."*".$rate."*".$amount."*".$$rack."*".$$shelf."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				
				$transId_arr[]=str_replace("'","",$$transIdTo);
				$data_array_update_trans[str_replace("'","",$$transIdTo)]=explode("*",($$productId."*".$tdate."*".$to_sameple."*".$$rollWgt."*".$rate."*".$amount."*".$$rack."*".$$shelf."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				
				$dtlsId_arr[]=str_replace("'","",$$dtlsId);
				$data_array_update_dtls[str_replace("'","",$$dtlsId)]=explode("*",($$productId."*".$$rollWgt."*".$$rollNo."*".$rate."*".$amount."*".$$rack."*".$$shelf."*".$$rack."*".$$shelf."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				
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
				$data_array_trans.="(".$id_trans.",".$update_id.",".$cbo_company_id.",".$$productId.",2,6,".$tdate.",".$from_sameple.",12,".$$rollWgt.",".$rate.",".$amount.",".$$rack.",".$$shelf.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				$transIdfromProp=$id_trans;
				//$id_trans=$id_trans+1;
				$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$transIdtoProp=$id_trans;
				$data_array_trans.=",(".$id_trans.",".$update_id.",".$cbo_company_id.",".$$productId.",2,5,".$txt_transfer_date.",".$to_sameple.",12,".$$rollWgt.",".$rate.",".$amount.",".$$rack.",".$$shelf.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$id_dtls.",".$update_id.",".$transIdfromProp.",".$transIdtoProp.",".$$productId.",2,".$$rollWgt.",".$$rollNo.",".$rate.",".$amount.",12,".$$rack.",".$$shelf.",".$$rack.",".$$shelf.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				if($data_array_roll!="") $data_array_roll.= ",";
				$data_array_roll.="(".$id_roll.",".$$barcodeNo.",".$update_id.",".$id_dtls.",".$to_sameple.",214,".$$rollWgt.",".$$rollNo.",".$$rollId.",".$$transRollId.",5,8,1,'".$to_sample_booking_no."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
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
			$deletedIds=explode(",",$txt_deleted_id); $dtlsIDDel=''; $transIDDel=''; $rollIDDel=''; $rollIDactive='';
			foreach($deletedIds as $delIds)
			{
				$delIds=explode("_",$delIds);
				if($dtlsIDDel=="")
				{
					$dtlsIDDel=$delIds[0];
					$transIDDel=$delIds[1].",".$delIds[2];
					$rollIDDel=$delIds[3];
					$rollIDactive=$delIds[4];
				}
				else
				{
					$dtlsIDDel.=",".$delIds[0];
					$transIDDel.=",".$delIds[1].",".$delIds[2];
					$rollIDDel.=",".$delIds[3];
					$rollIDactive.=",".$delIds[4];
				}
			}
			
			$prev_rol_id_sql=sql_select("select from_roll_id from pro_roll_details where id in($rollIDDel)");
			$prev_rol_id="";
			foreach($prev_rol_id_sql as $row)
			{
				$prev_rol_id.=$row[csf("from_roll_id")].",";
			}	
		}
		
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
		
		// As new roll are not added and existing rolls not required to change these field so this block is commented
		/*$rollIds=chop($rollIds,',');
		if($rollIds!="")
		{
			$rID5=sql_multirow_update("pro_roll_details","is_transfer*transfer_criteria*re_transfer","6*8*1","id",$rollIds,0);
			if($flag==1) 
			{
				if($rID5) $flag=1; else $flag=0; 
			} 
		}*/
		
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
		// echo "10**string";oci_rollback($con);disconnect($con);die;
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

function sql_multirow_update2($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues, $commit)
{
	$strQuery = "UPDATE ".$strTable." SET ";
	$arrUpdateFields=explode("*",$arrUpdateFields);
	$arrUpdateValues=explode("*",$arrUpdateValues);


	if(is_array($arrUpdateFields))
	{
		$arrayUpdate = array_combine($arrUpdateFields,$arrUpdateValues);
		$Arraysize = count($arrayUpdate);
		$i = 1;
		foreach($arrayUpdate as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value.", ":$key."=".$value." WHERE ";
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrUpdateFields."=".$arrUpdateValues." WHERE ";
	}

	//$arrRefFields=explode("*",$arrRefFields);
	//$arrRefValues=explode("*",$arrRefValues);
	$strQuery .= $arrRefFields." in (".$arrRefValues.")";
	echo $strQuery;die;
    global $con;
	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
	
	if ($exestd){user_activities($exestd);}
	
	if ($exestd)
		return "1";
	else
		return "0";

	die;
	$_SESSION['last_query']=$_SESSION['last_query'].";;".$strQuery;
	if ($commit==1)
	{
		if (!oci_error($stid))
		{

		$pc_time= add_time(date("H:i:s",time()),360);
		$pc_date_time = date("d-M-Y h:i:s",strtotime(add_time(date("H:i:s",time()),360)));
	    $pc_date = date("d-M-Y",strtotime(add_time(date("H:i:s",time()),360)));

		$strQuery= "INSERT INTO activities_history ( session_id,user_id,ip_address,entry_time,entry_date,module_name,form_name,query_details,query_type) VALUES ('".$_SESSION['logic_erp']["history_id"]."','".$_SESSION['logic_erp']["user_id"]."','".$_SESSION['logic_erp']["pc_local_ip"]."','".$pc_time."','".$pc_date."','".$_SESSION["module_id"]."','".$_SESSION['menu_id']."','".encrypt($_SESSION['last_query'])."','1')";

		mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");

		$resultss=oci_parse($con, $strQuery);
		oci_execute($resultss);
		$_SESSION['last_query']="";
		oci_commit($con);
		return "0";
		}
		else
		{
			oci_rollback($con);
			return "10";
		}
	}
	else return 1;
	die;
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
                <th width="214">Fabric Description</th>
                <th width="80">Color</th>
                <th width="70">GSM</th>
                <th width="80">DIA</th>
                <th width="55">Rack</th>
                <th width="55">Shelf</th>
                <th width="60">UOM</th>
                <th width="100">Transfered Qnty</th>
            </thead>
            <tbody> 
			<?
           $sql_dtls="select a.from_prod_id, a.transfer_qnty, a.uom, a.to_rack, a.to_shelf,a.feb_description_id, a.color_id,a.gsm,a.dia_width,b.barcode_no, b.roll_no from inv_item_transfer_dtls a, pro_roll_details b where a.id=b.dtls_id and b.entry_form=214 and a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.barcode_no";
            
            $sql_result= sql_select($sql_dtls);
            $i=1;
            foreach($sql_result as $row)
            {
                if ($i%2==0)  
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";


                $color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
                $composition_arr = array();
                $sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
                $data_array = sql_select($sql_deter);
                if (count($data_array) > 0) {
                	foreach ($data_array as $drow) {
                		if (array_key_exists($drow[csf('id')], $composition_arr)) {
                			$composition_arr[$drow[csf('id')]] = $composition_arr[$drow[csf('id')]] . " " . $composition[$drow[csf('copmposition_id')]] . " " . $drow[csf('percent')] . "%";
                		} else {
                			$composition_arr[$drow[csf('id')]] = $drow[csf('construction')] . ", " . $composition[$drow[csf('copmposition_id')]] . " " . $drow[csf('percent')] . "%";
                		}
                	}
                }

                $color='';
                $color_id=explode(',',$row[csf('color_id')]);
                
                foreach($color_id as $colorId)
                {
                	if ($color=='') $color=$color_arr[$colorId]; else $color.=",".$color_arr[$colorId];
                }

                if ($row[csf('feb_description_id')]>0) {
                	$fabric_desc = $composition_arr[$row[csf('feb_description_id')]];
                } 
                    
				$transfer_qnty=$row[csf('transfer_qnty')];
				$transfer_qnty_sum += $transfer_qnty;
                ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td><? echo $row[csf("barcode_no")]; ?></td>
                        <td><? echo $row[csf("roll_no")]; ?></td>
                        <td><? echo $fabric_desc; ?></td>
                        <td><? echo $color; ?></td>
                        <td><? echo $row[csf("gsm")]; ?></td>
                        <td><? echo $row[csf("dia_width")]; ?></td>
                        <td><? echo $row[csf("to_rack")]; ?></td>
                        <td><? echo $row[csf("to_shelf")]; ?></td>
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
                    <td colspan="10" align="right"><strong>Total </strong></td>
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
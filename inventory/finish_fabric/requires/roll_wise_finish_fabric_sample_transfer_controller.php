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

if ($action=="requ_variable_settings")
{
	extract($_REQUEST);
	$requisition_type=return_field_value("user_given_code_status","variable_settings_inventory","company_name='$cbo_company_id' and variable_list=30 and item_category_id='2' and status_active=1 and is_deleted=0");
	$variable_inventory=return_field_value("store_method","variable_settings_inventory","company_name='$cbo_company_id' and item_category_id=2 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	echo $requisition_type.'**'.$variable_inventory;
	exit();
}


if($action=="load_drop_store_from")
{
	$data= explode("_", $data);
	//var_dump($data);
	echo create_drop_down( "cbo_store_name", 160, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and b.category_type=2 and a.company_id=$data[1] and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "active_inactive();" );
}

if ($action=="load_room_rack_self_bin")
{
	load_room_rack_self_bin("requires/roll_wise_finish_fabric_sample_transfer_controller",$data);
}

if ($action=="sampleRequisitionTransfer_popup") // sample requisition Transfer System ID Popup
{
	echo load_html_head_contents("Sample To Order Transfer Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 
	<script>
		function js_set_value(data)
		{
			// alert(data);return;
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
	                    <th width="240" id="search_by_td_up">Please Enter Requisition No</th>
	                    <th>
	                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
	                        <input type="hidden" name="transfer_id" id="transfer_id" class="text_boxes" value="">
	                    </th>
	                </thead>
	                <tr class="general">
	                    <td>
							<?
								$search_by_arr=array(1=>"Requisition No",2=>"Challan No.");
								$dd="change_search_event(this.value, '0*0', '0*0', '../../../') ";
								echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
	                        ?>
	                    </td>
	                    <td id="search_by_td">
	                        <input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
	                    </td>
	                    <td>
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+<? echo $cbo_transfer_criteria; ?>, 'create_sample_requisition_search_list_view', 'search_div', 'roll_wise_finish_fabric_sample_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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


if($action=='create_sample_requisition_search_list_view') // sample requisition Transfer System ID list view 
{
	$data = explode("_",$data);

	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$company_id =$data[2];
	$transfer_criteria =$data[3];

	if($transfer_criteria==8) // Sample to Sample
	{
		$entry_form_no = 180;
	}
	elseif($transfer_criteria==7) // Sample to Order
	{
		$entry_form_no = 183;
	}
	else{
		$entry_form_no = 110; // // Order to Sample
	}
	
	if($search_by==1)
	{
		$search_field="transfer_system_id";
	}
	else {
		$search_field="challan_no";
	}
	
	if($db_type==0) $year_field="YEAR(insert_date) as year,"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year,";
	else $year_field="";//defined Later

	$sql_trans_requ=sql_select("SELECT a.transfer_requ_id from inv_item_transfer_mst a, inv_item_transfer_dtls b
	where a.id=b.mst_id and a.item_category=2 and a.company_id=$company_id and a.transfer_criteria=$transfer_criteria  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=$entry_form_no and a.transfer_requ_id IS NOT NULL
	group by a.transfer_requ_id");

	$requ_id="";
	foreach ($sql_trans_requ as $row)
	{
		if ($requ_id=="")
		{
			$requ_id.=$row[csf('transfer_requ_id')];
		}
		else
		{
			$requ_id.=', '.$row[csf('transfer_requ_id')];
		}
	}
	//echo $requ_id;
	if ($requ_id!="")
	{
		$requ_id_cond= "and id not in($requ_id)";
	}
	
 	$sql="SELECT id, transfer_prefix_number, $year_field transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria, item_category from inv_item_transfer_requ_mst where item_category=2 and company_id=$company_id and $search_field like '$search_string' and transfer_criteria=$transfer_criteria and entry_form=$entry_form_no $requ_id_cond and is_approved=1 and requisition_status=1 and status_active=1 and is_deleted=0 order by id";
	//echo $sql;
	
	$arr=array(3=>$company_arr,5=>$item_transfer_criteria,6=>$item_category);

	echo  create_list_view("tbl_list_search", "Transfer ID,Year,Challan No,Company,Transfer Date,Transfer Criteria,Item Category", "80,70,100,80,90,130","750","250",0, $sql, "js_set_value", "id", "", 1, "0,0,0,company_id,0,transfer_criteria,item_category", $arr, "transfer_prefix_number,year,challan_no,company_id,transfer_date,transfer_criteria,item_category", '','','0,0,0,0,3,0,0');
	
	exit();
}

// if($action=='populate_data_from_sample_requisition_master') // sample_requisition master data set
// {
// 	$data_array=sql_select("SELECT transfer_criteria, transfer_system_id, challan_no, company_id, to_company, transfer_date, item_category, from_order_id, to_order_id, from_samp_dtls_id, to_samp_dtls_id from inv_item_transfer_requ_mst where id='$data'");
// 	foreach ($data_array as $row)
// 	{ 
// 		echo "document.getElementById('txt_requisition_id').value 			= '".$data."';\n";
// 		echo "document.getElementById('cbo_transfer_criteria').value 		= '".$row[csf("transfer_criteria")]."';\n";
// 		echo "document.getElementById('txt_requisition_no').value 			= '".$row[csf("transfer_system_id")]."';\n";
// 		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
// 		echo "document.getElementById('cbo_company_id_to').value 			= '".$row[csf("to_company")]."';\n";
// 		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("challan_no")]."';\n";
// 		echo "document.getElementById('txt_transfer_date').value 			= '".change_date_format($row[csf("transfer_date")])."';\n";
// 		$transfer_criteria=$row[csf("transfer_criteria")];
// 		if($transfer_criteria==8) // Sample to Sample
// 		{
// 			$from_order_book_id = $row[csf("from_samp_dtls_id")];
// 			$to_order_book_id = $row[csf("to_samp_dtls_id")]; 
// 		}
// 		elseif($transfer_criteria==7) // Sample to Order
// 		{
// 			$from_order_book_id = $row[csf("from_samp_dtls_id")];
// 			$to_order_book_id = $row[csf("to_order_id")];
			
// 		}
// 		else // Order to Sample
// 		{ 
// 			$from_order_book_id = $row[csf("from_order_id")];
// 			$to_order_book_id = $row[csf("to_samp_dtls_id")]; 
// 		}
// 		echo "get_php_form_data('".$from_order_book_id."**"."from"."**".$row[csf("transfer_criteria")]."','populate_data_from_sample','requires/roll_wise_finish_fabric_sample_transfer_controller');\n";
// 		echo "get_php_form_data('".$to_order_book_id."**".$row[csf("transfer_criteria")]."','populate_data_to_order','requires/roll_wise_finish_fabric_sample_transfer_controller');\n";
// 		echo "$('#cbo_transfer_criteria').attr('disabled','disabled');\n";
// 		echo "$('#cbo_company_id').attr('disabled','disabled');\n";
// 		echo "$('#cbo_company_id_to').attr('disabled','disabled');\n";
// 		echo "load_drop_down( 'requires/roll_wise_finish_fabric_sample_transfer_controller','".$row[csf("transfer_criteria")].'_'.$row[csf("company_id")]."', 'load_drop_store_from', 'from_store_td' );\n";

// 		echo "load_room_rack_self_bin('requires/roll_wise_finish_fabric_sample_transfer_controller*2*cbo_store_name_to', 'store','to_store_td', $('#cbo_company_id_to').val(),'','','','','','','','store_load_cond()');\n";
// 		echo "$('#txt_from_order_book_no').attr('disabled','disabled');\n";
// 		echo "$('#txt_to_order_book_no').attr('disabled','disabled');\n";
// 		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_finish_transfer_entry',1,1);\n"; 
// 		exit();
// 	}
// }

if($action=="show_sample_requisition_transfer_listview") // sample_requisition Dtls list view
{
	$data=explode("**",$data);

	$mst_id=$data[0];
	$order_id=$data[1];
	$cbo_transfer_criteria=$data[2];
	if($cbo_transfer_criteria==8) // Sample to Sample
	{
		$entry_form_no = 180;
	}
	elseif($cbo_transfer_criteria==7) // Sample to Order
	{
		$entry_form_no = 183;
	}
	else{
		$entry_form_no = 110; // // Order to Sample
	}

	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');	
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');	
	//$product_arr=return_library_array( "select id, product_name_details from product_details_master where item_category_id=2",'id','product_name_details');

	
	$delv_arr=return_library_array("select barcode_num, grey_sys_id from pro_grey_prod_delivery_dtls where entry_form=56 and order_id=$order_id", "barcode_num", "grey_sys_id");
	
	$programArr=return_library_array("SELECT a.id, a.booking_id from inv_receive_master a, pro_roll_details b where a.id=b.mst_id and a.entry_form=2 and a.receive_basis=2 and b.entry_form=2 and b.po_breakdown_id=$order_id and b.status_active=1 and b.is_deleted=0", "id", "booking_id");

	$sql="SELECT a.id, a.entry_form, 0 as receive_basis, 0 as booking_id, b.from_prod_id as prod_id, b.yarn_lot, b.y_count as yarn_count, b.stitch_length, b.brand_id, b.floor_id, b.room, b.to_rack as rack, b.to_shelf as self, b.roll_id, b.barcode_no, b.to_order_id as po_breakdown_id, b.roll as roll_no, b.transfer_qnty as qnty, b.roll_id as roll_id_prev, 3 as type , b.to_store as store_id, b.id as dtls_id, b.trans_id as prev_rol_id, b.to_trans_id, c.gsm, c.dia_width, c.detarmination_id, c.item_description,c.product_name_details
	from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b, product_details_master c
	WHERE a.id=b.mst_id and b.from_prod_id=c.id and a.entry_form in($entry_form_no) and b.entry_form in($entry_form_no) and b.status_active=1 and b.is_deleted=0 and b.mst_id=$mst_id and a.transfer_criteria=$cbo_transfer_criteria
	order by barcode_no";
	
	//echo $sql;
	
	$data_array=sql_select($sql);	
	$i=1;
	foreach($data_array as $row)
	{  
		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	
		$ycount='';
		$count_id=explode(',',$row[csf('yarn_count')]);
		foreach($count_id as $count)
		{
			if ($ycount=='') $ycount=$count_arr[$count]; else $ycount.=",".$count_arr[$count];
		}
		
		$transRollId=$row[csf('prev_rol_id')];
		$dtls_id=$row[csf('dtls_id')];
		$from_trans_id=$row[csf('trans_id')];
		$to_trans_id=$row[csf('to_trans_id')];
		$rolltableId=$row[csf('roll_id')];
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
		
		$checked="checked"; $disabled="disabled";
		?>
		<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
			<td width="40" align="center" valign="middle"><input type="checkbox" id="tbl_<? echo $i;?>" <? echo $checked." ".$disabled; ?> name="check[]" /></td> 
			<td width="40"><? echo $i; ?></td>
			<td width="80"><? echo $row[csf('barcode_no')]; ?></td>
			<td width="50"><? echo $row[csf('roll_no')]; ?></td>
			<td width="70"><p><? echo $program_no; ?>&nbsp;</p></td>
			<td width="60"><p><? echo $row[csf('prod_id')]; ?></p></td>
			<td width="180"><p><? echo $row[csf('product_name_details')]; ?>&nbsp;</p></td>
			<td width="80"><p><? echo $ycount; ?>&nbsp;</p></td>
			<td width="70"><p><? echo $brand_arr[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
			<td width="80"><p><? echo $row[csf('yarn_lot')]; ?>&nbsp;</p></td>
			<td width="55"><p><? echo $row[csf('floor_id')]; ?>&nbsp;</p></td>
			<td width="55"><p><? echo $row[csf('room')]; ?>&nbsp;</p></td>
			<td width="55"><p><? echo $row[csf('rack')]; ?>&nbsp;</p></td>
			<td width="55"><p><? echo $row[csf('self')]; ?>&nbsp;</p></td>
			<td width="80"><p><? echo $row[csf('stitch_length')]; ?>&nbsp;</p></td>
			<td align="right" style="padding-right:2px"><? echo $row[csf('qnty')]; ?>
            	<input type="hidden" name="barcodeNo[]" id="barcodeNo_<? echo $i; ?>" value="<? echo $row[csf('barcode_no')]; ?>"/>
                <input type="hidden" name="rollNo[]" id="rollNo_<? echo $i; ?>" value="<? echo $row[csf('roll_no')]; ?>"/>
                <input type="hidden" name="progId[]" id="progId_<? echo $i; ?>" value="<? echo $program_no; ?>"/>
                <input type="hidden" name="productId[]" id="productId_<? echo $i; ?>" value="<? echo $row[csf('prod_id')]; ?>"/>
                <input type="hidden" name="rollId[]" id="rollId_<? echo $i; ?>" value="<? echo $row[csf('roll_id')]; ?>"/>
                <input type="hidden" name="rollWgt[]" id="rollWgt_<? echo $i; ?>" value="<? echo $row[csf('qnty')]; ?>"/>
                <input type="hidden" name="yarnLot[]" id="yarnLot_<? echo $i; ?>" value="<? echo $row[csf('yarn_lot')]; ?>"/>
               
                <input type="hidden" name="stichLn[]" id="stichLn_<? echo $i; ?>" value="<? echo $row[csf('stitch_length')]; ?>"/>
                <input type="hidden" name="brandId[]" id="brandId_<? echo $i; ?>" value="<? echo $row[csf('brand_id')]; ?>"/>
                <input type="hidden" name="floor[]" id="floor_<? echo $i; ?>" value="<? echo $row[csf('floor_id')]; ?>"/>
                <input type="hidden" name="room[]" id="room_<? echo $i; ?>" value="<? echo $row[csf('room')]; ?>"/>
                <input type="hidden" name="rack[]" id="rack_<? echo $i; ?>" value="<? echo $row[csf('rack')]; ?>"/>
                <input type="hidden" name="shelf[]" id="shelf_<? echo $i; ?>" value="<? echo $row[csf('self')]; ?>"/>
                <input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>" value=""/>
                <input type="hidden" name="requiDtlsId[]" id="requiDtlsId_<? echo $i; ?>" value="<? echo $dtls_id; ?>"/>
                <input type="hidden" name="transIdFrom[]" id="transIdFrom_<? echo $i; ?>" value="<? echo $from_trans_id; ?>"/>
                <input type="hidden" name="transIdTo[]" id="transIdTo_<? echo $i; ?>" value="<? echo $to_trans_id; ?>"/>
                <input type="hidden" name="rolltableId[]" id="rolltableId_<? echo $i; ?>" value="<? echo $rolltableId; ?>"/>
                <input type="hidden" name="transRollId[]" id="transRollId_<? echo $i; ?>" value="<? echo $transRollId ?>"/>
                <input type="hidden" name="storeId[]" id="storeId_<? echo $i; ?>" value="<? echo $row[csf('store_id')]; ?>"/>
                <input type="hidden" name="fromProductUp[]" id="fromProductUp_<? echo $i; ?>" value="<? echo $row[csf('prod_id')]; ?>">
                <input type="hidden" name="gsm[]" id="gsm_<? echo $i; ?>" value="<? echo $row[csf('gsm')]; ?>"/>
                <input type="hidden" name="diaWidth[]" id="diaWidth_<? echo $i; ?>" value="<? echo $row[csf('dia_width')]; ?>"/>
                <input type="hidden" name="febDescripId[]" id="febDescripId_<? echo $i; ?>" value="<? echo $row[csf('detarmination_id')]; ?>"/>
                <input type="hidden" name="constructCompo[]" id="constructCompo_<? echo $i; ?>" value="<? echo $row[csf('item_description')]; ?>"/>
            </td>
		</tr>
		<? 
		$i++; 
	} 
	exit();
}

if ($action=="from_order_popup")
{
	echo load_html_head_contents("sample Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 
	<script>
		function js_set_value(data)
		{
			//alert(data);return;
			$('#return_id').val(data);
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
	<?
	if ($cbo_transfer_criteria==6) // Order to sample
	{
		?>
		<div align="center" style="width:1405px;">
			<form name="searchdescfrm"  id="searchdescfrm">
				<fieldset style="width:1405px;">
					<legend>Enter search words</legend>
					<table cellpadding="0" cellspacing="0" width="1070" class="rpt_table" border="1" rules="all">
						<thead>
							<th>Buyer Name</th>
							<th>Order No</th>
							<th>Job No</th>
							<th>Batch No</th>
							<th>File No</th>
							<th>Ref. No</th>
							<th width="200">Shipment Date Range</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:80px;" class="formbutton" />
								<input type="hidden" name="return_id" id="return_id" class="text_boxes" value="">
							</th>
						</thead>
						<tr class="general">
							<td>
								<?
								echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",$data[0] );
								?>
							</td>
							<td>
								<input type="text" style="width:110px;" class="text_boxes" name="txt_to_order_book_no" id="txt_to_order_book_no" placeholder="Enter Order No" />
							</td>
							<td>
								<input type="text" style="width:110px;" class="text_boxes" name="txt_to_job_book_no" id="txt_to_job_book_no" placeholder="Enter Job No" />
							</td>
							<td>
								<input type="text" style="width:110px;" class="text_boxes" name="txt_to_batch_no" id="txt_to_batch_no" placeholder="Enter Batch No" />
							</td>
							<td>
								<input type="text" style="width:110px;" class="text_boxes" name="txt_file_no" id="txt_file_no" placeholder="Enter File No" />
							</td>
							<td>
								<input type="text" style="width:110px;" class="text_boxes" name="txt_ref_no" id="txt_ref_no" placeholder="Enter Ref. No" />
							</td>
							<td>
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" readonly>
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" readonly>
							</td>
							<td>
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_to_order_book_no').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $type; ?>'+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_ref_no').value+'_'+<? echo $cbo_transfer_criteria; ?>+'_'+document.getElementById('txt_to_job_book_no').value+'_'+document.getElementById('txt_to_batch_no').value+'_'+<? echo $cbo_store_name; ?>, 'create_po_search_list_view', 'search_div', 'roll_wise_finish_fabric_sample_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:80px;" />
							</td>
						</tr>
						<tr>
							<td colspan="8" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
						</tr>
					</table>
					<div style="margin-top:10px" id="search_div"></div>
				</fieldset>
			</form>
		</div>
		<?
	}
	else // 
	{
		?>
		<div align="center" style="width:1310px;">
			<form name="searchdescfrm"  id="searchdescfrm">
				<fieldset style="width:1300px;margin-left:10px">
		        <legend>Enter search words</legend>
		            <table cellpadding="0" cellspacing="0" width="930" class="rpt_table">
		                <thead>
		                    <th>Buyer Name</th>
		                    <th>Batch No</th>
		                    <th>Booking No</th>
		                    <th width="230">Booking Date Range</th>
		                    <th>
		                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
		                        <input type="hidden" name="return_id" id="return_id" class="text_boxes" value="">
		                    </th>
		                </thead>
		                <tr class="general">
		                    <td>
								<?
									echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",$data[0] );
								?>
		                    </td>
							<td>
		                        <input type="text" style="width:130px;" class="text_boxes" name="txt_batch_no" id="txt_batch_no" />
		                    </td>
		                    <td>
		                        <input type="text" style="width:130px;" class="text_boxes" name="txt_sample_no" id="txt_sample_no" />
		                    </td>
		                    <td>
		                    	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" readonly>
		                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" readonly>
		                    </td>
		                    <td>
		                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_sample_no').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $cbo_transfer_criteria; ?>+'_'+document.getElementById('txt_batch_no').value+'_'+<? echo $cbo_store_name; ?>, 'create_sample_search_list_view', 'search_div', 'roll_wise_finish_fabric_sample_transfer_controller', 'setFilterGrid(\'tbl_list_sample\',-1);')" style="width:100px;" />
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
		<?
	}
	?>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=='create_sample_search_list_view') // sample search list view
{
	$data=explode('_',$data);
		
	if ($data[0]==0) $buyer="%%"; else $buyer=$data[0];
	$search_string="%".trim($data[1])."%";
	$company_id=$data[2];
	$batch_no="%".trim($data[6])."%";
	$store_id=$data[7];
	//var_dump($store_id);
	
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
	$arr=array (3=>$company_arr,4=>$buyer_arr,5=>$style_name_array,6=>$sample_arr,7=>$body_part,9=>$color_arr);
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later

	
	// $sql= "SELECT a.id as booking_id, b.id as dtls_id, a.booking_no, $year_field, a.booking_no_prefix_num, a.company_id, a.buyer_id, 
	// a.booking_date, a.delivery_date, b.style_id, b.fabric_description,b.sample_type,b.fabric_color,b.grey_fabric, b.body_part,c.batch_no,d.barcode_no 
	// from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, pro_batch_create_mst c,pro_batch_create_dtls d 
	// where a.booking_no=b.booking_no and a.booking_no=c.booking_no and c.id=d.mst_id and a.booking_type=4 and a.company_id=$company_id and a.buyer_id like '$buyer' 
	// and b.booking_no like '$search_string' and c.batch_no like '$batch_no' and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $booking_date
	// group by a.id , b.id , a.booking_no, a.insert_date, a.booking_no_prefix_num, a.company_id, a.buyer_id, 
	// a.booking_date, a.delivery_date, b.style_id, b.fabric_description,b.sample_type,b.fabric_color,b.grey_fabric,
	// b.body_part,c.batch_no,d.barcode_no order by a.id, b.id";

	$sql= "SELECT a.id as booking_id, b.id as dtls_id, a.booking_no, $year_field, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.booking_date, a.delivery_date, b.style_id, b.fabric_description,b.sample_type,b.fabric_color,b.finish_fabric, b.body_part,c.batch_no,c.id as batch_id,d.store_id
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, pro_batch_create_mst c,inv_transaction d 
	where a.booking_no=b.booking_no and a.booking_no=c.booking_no and c.id=d.batch_id and a.booking_type=4 and a.company_id=$company_id and a.buyer_id like '$buyer' and b.booking_no like '$search_string' and c.batch_no like '$batch_no' and d.store_id=$store_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0  $booking_date group by a.id, b.id, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.booking_date, a.delivery_date, b.style_id, b.fabric_description,b.sample_type,b.fabric_color,b.finish_fabric, b.body_part,c.batch_no,c.id,d.store_id,a.insert_date order by a.id, b.id";

	//echo $sql;

	echo create_list_view("tbl_list_sample", "Booking No,Batch No, Year,Company,Buyer Name,Style Ref. No,Sample,Body Part,Style Description,Fab. Color,Booking Date, Finish Qnty", "60,60,60,80,80,100,100,130,160,80,80,60","1160","200",0, $sql , "js_set_value", "booking_id,dtls_id,body_part,batch_id", "", 1, "0,0,0,company_id,buyer_id,style_id,sample_type,body_part,0,fabric_color,0,0", $arr , "booking_no_prefix_num,batch_no,year,company_id,buyer_id,style_id,sample_type,body_part,fabric_description,fabric_color,booking_date,finish_fabric", "",'','0,0,0,0,0,0,0,0,0,0,3');

	// echo create_list_view("tbl_list_sample", "Booking No,Year,Company,Buyer Name,Style Ref. No,Sample,Body Part,Style Description,Fab. Color,Booking Date, Finish Qnty", "60,60,80,80,100,100,130,160,80,80,60","1100","200",0, $sql , "js_set_value", "booking_id,dtls_id,body_part", "", 1, "0,0,company_id,buyer_id,style_id,sample_type,body_part,0,fabric_color,0,0", $arr , "booking_no_prefix_num,year,company_id,buyer_id,style_id,sample_type,body_part,fabric_description,fabric_color,booking_date,grey_fabric", "",'','0,0,0,0,0,0,0,0,0,3');


	
	exit();
}

if($action=='create_po_search_list_view') // Order to sample search list view
{
	$data=explode('_',$data);
	
	if ($data[0]==0) $buyer="%%"; else $buyer=$data[0];
	$search_string=$data[1];
	$company_id=$data[2];
	$file_no=$data[6];
	$ref_no=$data[7];
	$booking_no=$data[9];
	$batch_no=$data[10];
	$store_id=$data[11];
	
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
	$arr=array(2=>$company_arr,3=>$buyer_arr,10=>$body_part);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	$str_cond="";
	if($file_no!="")  $str_cond=" and b.file_no=$file_no";
	if($ref_no!="")  $str_cond.=" and b.grouping like '%$ref_no%'";
	if($search_string!="")  $str_cond.=" and b.po_number like '%$search_string%'";
	if($booking_no!="")  $str_cond.=" and a.job_no like '%$booking_no%'";
	if($batch_no!="")  $str_cond.=" and f.batch_no like '%$batch_no%'";
	
	if($type=="from") $status_cond=" and b.status_active in(1,3)"; else $status_cond=" and b.status_active=1";
	//$sql= "SELECT a.job_no, $year_field, a.job_no_prefix_num,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date, b.file_no, b.grouping as ref_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_id and a.buyer_name like '$buyer' and b.po_number like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $status_cond $shipment_date $str_cond order by b.id, b.pub_shipment_date";  
	// if($order_id!="") 	$sql_cond =" and c.po_breakdown_id=$order_id";
	// $sql = "SELECT b.company_id, b.pi_wo_batch_no as batch_id, e.buyer_name as buyer_id, sum(c.quantity) as quantity, c.po_breakdown_id as order_id, c.prod_id, b.store_id, b.floor_id, b.room, b.rack, b.self, a.batch_no, a.booking_no_id as booking_id, a.booking_no, d.po_number,b.fabric_shade, b.body_part_id,b.cons_uom, e.job_no, e.style_ref_no from pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e where a.id=b.pi_wo_batch_no and b.id = c.trans_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and b.item_category=2 and c.entry_form in (37,52,14,306) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and b.pi_wo_batch_no>0 and c.trans_id>0 and b.company_id =$company_id $sql_cond and a.id=$batch_id and b.transaction_type in (1,4,5) group by b.company_id, b.pi_wo_batch_no, e.buyer_name, c.po_breakdown_id , c.prod_id, b.store_id, b.floor_id, b.room, b.rack, b.self, a.batch_no, a.booking_no_id , a.booking_no, d.po_number,b.fabric_shade, b.body_part_id, e.job_no, e.style_ref_no, b.cons_uom order by c.prod_id";

	// $sql ="SELECT a.job_no, $year_field, a.job_no_prefix_num,a.company_name, a.style_ref_no, a.job_quantity, a.buyer_name as buyer_id, d.buyer_name, c.booking_no, e.id as booking_id, b.file_no, b.id,b.grouping as ref_no, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date, f.batch_no, f.id as batch_id, h.body_part_id,g.store_id from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c, wo_booking_mst e, lib_buyer d, pro_batch_create_mst f,inv_transaction g, WO_PRE_COST_FABRIC_COST_DTLS h where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and f.id=g.batch_id and b.job_no_mst=c.job_no and c.booking_no=e.booking_no  and a.buyer_name=d.id and b.job_id=h.job_id and a.company_name=$company_id and a.buyer_name like '$buyer'  and c.booking_type in (1,4) and a.status_active=1 and a.is_deleted=0 $status_cond $shipment_date $str_cond and g.store_id=$store_id and b.status_active=1 and b.is_deleted=0 and e.booking_no = f.booking_no group by a.job_no,a.insert_date,a.job_no_prefix_num,a.company_name, a.style_ref_no, a.job_quantity, a.buyer_name, d.buyer_name, c.booking_no, b.file_no, b.id,b.grouping, b.po_number, b.po_quantity, b.pub_shipment_date,a.id,e.id, f.batch_no, f.id, h.body_part_id,g.store_id";

	$sql ="SELECT a.job_no, to_char(a.insert_date,'YYYY') as year, a.job_no_prefix_num,a.company_name, a.style_ref_no, a.job_quantity, a.buyer_name as buyer_id, d.buyer_name, c.booking_no, e.id as booking_id, b.file_no, b.id,b.grouping as ref_no, b.po_number, 
	b.po_quantity, b.pub_shipment_date as shipment_date, f.batch_no, f.id as batch_id, h.body_part_id
	from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c, wo_booking_mst e, lib_buyer d, pro_batch_create_mst f, WO_PRE_COST_FABRIC_COST_DTLS h 
	where a.id=b.job_id and b.id=c.po_break_down_id 
	and c.booking_no=e.booking_no and a.buyer_name=d.id and b.job_id=h.job_id and a.company_name=$company_id and a.buyer_name like '$buyer' and c.booking_type in (1,4) and a.status_active=1 and a.is_deleted=0 $status_cond $shipment_date $str_cond and b.status_active in(1,3) and b.status_active=1 and b.is_deleted=0 
	and e.booking_no = f.booking_no 
	group by a.job_no,a.insert_date,a.job_no_prefix_num,a.company_name, a.style_ref_no, a.job_quantity, a.buyer_name, d.buyer_name, c.booking_no, b.file_no, b.id,b.grouping, b.po_number, b.po_quantity, b.pub_shipment_date,a.id,e.id, f.batch_no, f.id,h.body_part_id";


	// $sql = "SELECT a.job_no, $year_field, a.job_no_prefix_num,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, c.body_part_id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date, b.file_no, b.grouping as ref_no, e.batch_no,e.id as batch_id,f.store_id from wo_po_details_master a, wo_po_break_down b, WO_PRE_COST_FABRIC_COST_DTLS c,pro_batch_create_mst e,inv_transaction f where a.id=b.job_id and b.job_id=c.job_id  and e.id=f.batch_id and a.company_name=$company_id and a.buyer_name like '$buyer' and b.po_number like '$search_string' and a.job_no like '$booking_no' and e.batch_no like '$batch_no' and f.store_id=$store_id and  a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $status_cond $shipment_date $str_cond  group by a.job_no, a.insert_date, a.job_no_prefix_num,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, c.body_part_id, b.po_number, b.po_quantity, b.pub_shipment_date, b.file_no, b.grouping, e.batch_no,e.id,f.store_id order by b.id, b.pub_shipment_date";
	
	
	//echo $sql;

	 echo create_list_view("tbl_list_search", "Job No,Year,Company,Buyer Name,Style Ref. No,Batch No,Job Qty.,File No,Ref. No,PO number,Body Part, PO Quantity,Shipment Date", "50,40,60,80,110,110,80,70,90,100,110,80","1120","200",0, $sql , "js_set_value", "id,id,body_part_id,batch_id", "", 1, "0,0,company_name,buyer_id,0,0,0,0,0,0,body_part_id,0", $arr , "job_no_prefix_num,year,company_name,buyer_id,style_ref_no,batch_no,job_quantity,file_no,ref_no,po_number,body_part_id,po_quantity,shipment_date", "",'','0,0,0,0,0,0,1,0,0,0,0,1,3');
	// echo create_list_view("tbl_list_search", "Job No,Year,Company,Buyer Name,Style Ref. No,Job Qty.,File No,Ref. No,PO number,Body Part, PO Quantity,Shipment Date", "50,40,60,80,110,80,70,90,100,110,80","1010","200",0, $sql , "js_set_value", "id,id,body_part_id", "", 1, "0,0,company_name,buyer_name,0,0,0,0,0,body_part_id,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,job_quantity,file_no,ref_no,po_number,body_part_id,po_quantity,shipment_date", "",'','0,0,0,0,0,1,0,0,0,0,1,3');
	exit();	
}

if($action=="populate_data_from_sample") // when system no browse problem
{
	//print_r($data);
	$data=explode("**",$data);
	
	$return_id=$data[0]; // return_id is booking or order no
	$from=$data[1];
	$transfer_criteria=$data[2];
	$body_part_id=$data[3];
	$batch_id=$data[4];

	//var_dump($batch_id);

	$style_name_array=return_library_array( "select id, style_ref_no from sample_development_mst",'id','style_ref_no');
	if ($transfer_criteria==6) // Order to Sample
	{
		
		$data_array=sql_select("SELECT a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date,d.batch_no,d.id as batch_id from wo_po_details_master a, wo_po_break_down b, pro_batch_create_dtls c,pro_batch_create_mst d where a.job_no=b.job_no_mst and b.id = c.po_id and c.mst_id=d.id and b.id=$return_id and d.id=$batch_id group by a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, b.po_number, b.po_quantity, b.pub_shipment_date,d.batch_no,d.id");
		foreach ($data_array as $row)
		{ 
			$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
			foreach($gmts_item_id as $item_id)
			{
				if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
			}
			
			echo "document.getElementById('txt_from_order_book_no').value 		= '".$row[csf("po_number")]."';\n";
			echo "document.getElementById('txt_from_order_book_id').value 		= '".$return_id."';\n";
			echo "document.getElementById('txt_from_qnty').value 				= '".$row[csf("po_quantity")]."';\n";
			echo "document.getElementById('cbo_from_buyer_name').value 			= '".$row[csf("buyer_name")]."';\n";
			echo "document.getElementById('txt_from_style_ref').value 			= '".$row[csf("style_ref_no")]."';\n";
			echo "document.getElementById('txt_from_batch_no').value 			= '".$row[csf("batch_no")]."';\n";
			echo "document.getElementById('txt_from_batch_id').value 			= '".$row[csf("batch_id")]."';\n";
			echo "document.getElementById('txt_from_job_no').value 				= '".$row[csf("job_no")]."';\n";
			echo "document.getElementById('txt_from_gmts_item').value 			= '".$gmts_item."';\n";
			echo "document.getElementById('txt_from_shipment_date').value 		= '".change_date_format($row[csf("shipment_date")])."';\n";
			echo "document.getElementById('cbo_from_body_part').value 			= '".$body_part_id."';\n";

			exit();
		}
	}
	else // Sample to Sample and Sample to Order, When Transfer System ID a.id=$return_id
	{
		// echo "SELECT a.id as booking_id, a.booking_no, a.booking_no_prefix_num, a.buyer_id, b.style_id, b.body_part, b.finish_fabric, b.id as dtls_id,c.batch_no,c.id as batch_id,c.company_id
		// from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, pro_batch_create_mst c  
		// where a.booking_no=b.booking_no and a.booking_no=c.booking_no and b.id=$return_id and c.id=$batch_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.booking_no, a.booking_no_prefix_num, a.buyer_id, b.style_id, b.body_part, b.finish_fabric, b.id,c.batch_no,c.id,c.company_id";
		$data_array=sql_select("SELECT a.id as booking_id, a.booking_no, a.booking_no_prefix_num, a.buyer_id, b.style_id, b.body_part, b.finish_fabric, b.id as dtls_id,c.batch_no,c.id as batch_id,c.company_id
		from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, pro_batch_create_mst c  
		where a.booking_no=b.booking_no and a.booking_no=c.booking_no and b.id=$return_id and c.id=$batch_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.booking_no, a.booking_no_prefix_num, a.buyer_id, b.style_id, b.body_part, b.finish_fabric, b.id,c.batch_no,c.id,c.company_id");
		// Sample to sample b.id=$return_id
		// Sample to Order a.id=$sample_id

		foreach ($data_array as $row)
		{ 
			echo "document.getElementById('txt_from_order_book_no').value 		= '".$row[csf("booking_no")]."';\n";
			echo "document.getElementById('txt_from_order_book_id').value 		= '".$row[csf("booking_id")]."';\n";
			echo "document.getElementById('txt_from_order_book_dtls_id').value 	= '".$row[csf("dtls_id")]."';\n";
			echo "document.getElementById('txt_from_qnty').value 				= '".$row[csf("finish_fabric")]."';\n";
			echo "document.getElementById('cbo_from_buyer_name').value 			= '".$row[csf("buyer_id")]."';\n";
			echo "document.getElementById('txt_from_batch_no').value 			= '".$row[csf("batch_no")]."';\n";
			echo "document.getElementById('txt_from_batch_id').value 			= '".$row[csf("batch_id")]."';\n";
			echo "document.getElementById('txt_from_style_ref').value 			= '".$style_name_array[$row[csf("style_id")]]."';\n";
			echo "document.getElementById('cbo_from_body_part').value 		= '".$row[csf("body_part")]."';\n";
			
			exit();
		}
	}	
}

if ($action=="to_order_popup")
{
	echo load_html_head_contents("Order Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $cbo_transfer_criteria.'TTT';die;
	?> 
	<script>
		function js_set_value(data)
		{
			$('#order_id').val(data);
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<?
	if ($cbo_transfer_criteria==7) // Order
	{
		?>
		<div align="center" style="width:1040px;">
			<form name="searchdescfrm"  id="searchdescfrm">
				<fieldset style="width:1040px;">
					<legend>Enter search words</legend>
					<table cellpadding="0" cellspacing="0" width="960" class="rpt_table" border="1" rules="all">
						<thead>
							<th>Buyer Name</th>
							<th>Order No</th>
							<th>Batch No</th>
							<th>File No</th>
							<th>Ref. No</th>
							<th width="200">Shipment Date Range</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:80px;" class="formbutton" />
								<input type="hidden" name="order_id" id="order_id" class="text_boxes" value="">
							</th>
						</thead>
						<tr class="general">
							<td>
								<?
								echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",$data[0] );
								?>
							</td>
							<td>
								<input type="text" style="width:110px;" class="text_boxes" name="txt_to_order_book_no" id="txt_to_order_book_no" placeholder="Enter Order No" />
							</td>
							<td>
								<input type="text" style="width:110px;" class="text_boxes" name="txt_to_batch_book_no" id="txt_to_batch_book_no" placeholder="Enter Batch No" />
							</td>
							<td>
								<input type="text" style="width:110px;" class="text_boxes" name="txt_file_no" id="txt_file_no" placeholder="Enter File No" />
							</td>
							<td>
								<input type="text" style="width:110px;" class="text_boxes" name="txt_ref_no" id="txt_ref_no" placeholder="Enter Ref. No" />
							</td>
							<td>
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" readonly>
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" readonly>
							</td>
							<td>
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_to_order_book_no').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $type; ?>'+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_ref_no').value+'_'+document.getElementById('txt_to_batch_book_no').value, 'create_to_po_search_list_view', 'search_div', 'roll_wise_finish_fabric_sample_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:80px;" />
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
		<?
	}
	else // Sample
	{
		?>
		<div align="center" style="width:930px;">
			<form name="searchdescfrm"  id="searchdescfrm">
				<fieldset style="width:930px;margin-left:10px">
		        <legend>Enter search words</legend>
		            <table cellpadding="0" cellspacing="0" width="800" class="rpt_table">
		                <thead>
		                    <th>Buyer Name</th>
		                    <th>Booking No</th>
		                    <th>Batch No</th>
		                    <th width="230">Booking Date Range</th>
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
		                        <input type="text" style="width:130px;" class="text_boxes" name="txt_sample_no" id="txt_sample_no" />
		                    </td>
							<td>
		                        <input type="text" style="width:130px;" class="text_boxes" name="txt_batch_no" id="txt_batch_no" />
		                    </td>
		                    <td>
		                    	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" readonly>
		                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" readonly>
		                    </td>
		                    <td>
		                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_sample_no').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $txt_from_order_book_no;?>+'_'+document.getElementById('txt_batch_no').value+'_'+<? echo $cbo_transfer_criteria;?>, 'create_sample_search_to_list_view', 'search_div', 'roll_wise_finish_fabric_sample_transfer_controller', 'setFilterGrid(\'tbl_list_sample\',-1);')" style="width:100px;" />
		                    </td>
		                </tr>
		                <tr>
		                    <td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
		                </tr>
		            </table>
		        	<div style="margin-top:10px" id="search_div"></div> 
				</fieldset>
			</form>
		</div>  
		<?
	}
	?>
	<body>
		
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=='create_sample_search_to_list_view')
{
	$data=explode('_',$data);
	
	
	if ($data[0]==0) $buyer="%%"; else $buyer=$data[0];
	$search_string="%".trim($data[1])."%";
	$company_id=$data[2];
	$batch_no="%".trim($data[6])."%";
	$cbo_transfer_criteria=$data[7];
	//var_dump($cbo_transfer_criteria);
	
	
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
	else {
		$booking_date ="";
	}
	$from_booking_chk="";
	// if($cbo_transfer_criteria==8)
	// {
	// 	if($data[5] !="")
	// 	{
	// 		$from_booking_chk = "and a.booking_no ='".$data[5]."'";
	// 		//$from_booking_chk = "and a.booking_no !='".$data[5]."'";
	// 	}
	// }
	// else
	// {
	// 	if($data[5] !="")
	// 	{
	// 		$from_booking_chk = "and a.booking_no !='".$data[5]."'";
	// 	}
	// }

	if($data[5] !="")
	{
		$from_booking_chk = "and a.booking_no !='".$data[5]."'";
	}
	
	
	$style_name_array=return_library_array( "select id, style_ref_no from sample_development_mst",'id','style_ref_no');
	$arr=array (3=>$company_arr,4=>$buyer_arr,5=>$style_name_array,6=>$sample_arr,7=>$body_part,9=>$color_arr);
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	$sql= "SELECT a.id as booking_id, b.id,a.booking_no, $year_field, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.booking_date, a.delivery_date, b.style_id, b.fabric_description,b.sample_type,b.fabric_color,b.finish_fabric, b.body_part,c.batch_no,c.id as batch_id
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b,pro_batch_create_mst c 
	where a.booking_no=b.booking_no and a.booking_no=c.booking_no and a.booking_type=4 and a.company_id=$company_id and a.buyer_id like '$buyer' and b.booking_no like '$search_string' and c.batch_no like '$batch_no' and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $booking_date $from_booking_chk order by a.id, b.id";

	
	//echo  $sql;//die;

	echo create_list_view("tbl_list_sample", "Booking No,Batch No,Year,Company,Buyer Name,Style Ref. No,Sample,Body Part,Style Description,Fab. Color,Booking Date, Finish Qnty", "60,60,60,80,80,100,100,130,160,80,80,60","1160","200",0, $sql , "js_set_value", "id,body_part,batch_id", "", 1, "0,0,0,company_id,buyer_id,style_id,sample_type,body_part,0,fabric_color,0,0", $arr , "booking_no_prefix_num,batch_no,year,company_id,buyer_id,style_id,sample_type,body_part,fabric_description,fabric_color,booking_date,finish_fabric", "",'','0,0,0,0,0,0,0,0,0,0,3');
	 
	// echo create_list_view("tbl_list_sample", "Booking No,Year,Company,Buyer Name,Style Ref. No,Sample,Body Part,Style Description,Fab. Color,Booking Date, Finish Qnty", "60,60,80,80,100,100,130,160,80,80,60","1100","200",0, $sql , "js_set_value", "id,body_part", "", 1, "0,0,company_id,buyer_id,style_id,sample_type,body_part,0,fabric_color,0,0", $arr , "booking_no_prefix_num,year,company_id,buyer_id,style_id,sample_type,body_part,fabric_description,fabric_color,booking_date,finish_fabric", "",'','0,0,0,0,0,0,0,0,0,3');
	
	exit();
}

if($action=='create_to_po_search_list_view')
{
	$data=explode('_',$data);
	
	
	if ($data[0]==0) $buyer="%%"; else $buyer=$data[0];
	$search_string="%".trim($data[1])."%";
	$company_id=$data[2];
	$file_no=$data[6];
	$ref_no=$data[7];
	$batch_no="%".trim($data[8])."%";

	
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
	$arr=array(2=>$company_arr,3=>$buyer_arr,10=>$body_part);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	$str_cond="";
	if($file_no!="")  $str_cond=" and b.file_no=$file_no";
	if($ref_no!="")  $str_cond.=" and b.grouping like '%$ref_no%'";
	
	if($type=="from") $status_cond=" and b.status_active in(1,3)"; else $status_cond=" and b.status_active=1";
	
	$sql = "SELECT a.job_no, $year_field, a.job_no_prefix_num,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, c.body_part_id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date, b.file_no, b.grouping as ref_no, e.batch_no,e.id as batch_id from wo_po_details_master a, wo_po_break_down b, WO_PRE_COST_FABRIC_COST_DTLS c,pro_batch_create_dtls d,pro_batch_create_mst e where a.id=b.job_id and b.job_id=c.job_id and b.id = d.po_id and d.mst_id=e.id and a.company_name=$company_id and a.buyer_name like '$buyer' and b.po_number like '$search_string' and e.batch_no like '$batch_no' and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $status_cond $shipment_date $str_cond group by a.job_no, a.insert_date, a.job_no_prefix_num,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, c.body_part_id, b.po_number, b.po_quantity, b.pub_shipment_date, b.file_no, b.grouping, e.batch_no,e.id order by b.id, b.pub_shipment_date";

	//echo $sql; 

	echo create_list_view("tbl_list_search", "Job No,Year,Company,Buyer Name,Style Ref. No,Job Qty.,File No,Ref. No,Batch No,PO number,Body Part, PO Quantity,Shipment Date", "50,40,60,80,110,80,70,90,100,100,110,80","1110","200",0, $sql , "js_set_value", "id,body_part_id,batch_id", "", 1, "0,0,company_name,buyer_name,0,0,0,0,0,0,body_part_id,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,job_quantity,file_no,ref_no,batch_no,po_number,body_part_id,po_quantity,shipment_date", "",'','0,0,0,0,0,1,0,0,0,0,0,1,3');
	exit();
}

if($action=='populate_data_to_order')
{
	//print_r($data);
	$data=explode("**", $data);
	
	$po_id=$data[0];
	$transfer_criteria=$data[1];
	$body_part_id=$data[2];
	$batch_id=$data[3];

	//var_dump($batch_id);

	if ($transfer_criteria==7) // Order
	{

		// echo "SELECT a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date,d.batch_no from wo_po_details_master a, wo_po_break_down b, pro_batch_create_dtls c,pro_batch_create_mst d where a.job_no=b.job_no_mst and b.id = c.po_id and c.mst_id=d.id and b.id=$po_id and d.id=$batch_id group by a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, b.po_number, b.po_quantity, b.pub_shipment_date,d.batch_no";

		//  echo "SELECT a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date,d.batch_no,d.booking_no from wo_po_details_master a, wo_po_break_down b, pro_batch_create_dtls c,pro_batch_create_mst d where a.job_no=b.job_no_mst and b.id = c.po_id and c.mst_id=d.id and b.id=$po_id and d.id=$batch_id group by a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, b.po_number, b.po_quantity, b.pub_shipment_date,d.batch_n,od.booking_no";die;

		$data_array=sql_select("SELECT a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date,d.batch_no,d.id as batch_id,d.booking_no from wo_po_details_master a, wo_po_break_down b, pro_batch_create_dtls c,pro_batch_create_mst d where a.job_no=b.job_no_mst and b.id = c.po_id and c.mst_id=d.id and b.id=$po_id and d.id=$batch_id group by a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, b.po_number, b.po_quantity, b.pub_shipment_date,d.batch_no,d.id,d.booking_no");
		foreach ($data_array as $row)
		{ 
			$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
			foreach($gmts_item_id as $item_id)
			{
				if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
			}
			
			echo "document.getElementById('txt_to_order_book_id').value 	= '".$po_id."';\n";
			echo "document.getElementById('txt_to_order_book_no').value 	= '".$row[csf("po_number")]."';\n";
			echo "document.getElementById('hidden_book_no').value 			= '".$row[csf("booking_no")]."';\n";
			echo "document.getElementById('txt_to_qnty').value 				= '".$row[csf("po_quantity")]."';\n";
			echo "document.getElementById('cbo_to_buyer_name').value 		= '".$row[csf("buyer_name")]."';\n";
			echo "document.getElementById('txt_to_style_ref').value 		= '".$row[csf("style_ref_no")]."';\n";
			//echo "document.getElementById('cbo_to_body_part').value 		= '".$body_part_id."';\n";
			echo "document.getElementById('txt_to_job_no').value 			= '".$row[csf("job_no")]."';\n";
			echo "document.getElementById('txt_to_batch_no').value 			= '".$row[csf("batch_no")]."';\n";
			echo "document.getElementById('txt_to_batch_id').value 			= '".$row[csf("batch_id")]."';\n";
			echo "document.getElementById('txt_to_gmts_item').value 		= '".$gmts_item."';\n";
			echo "document.getElementById('txt_to_shipment_date').value 	= '".change_date_format($row[csf("shipment_date")])."';\n";
			echo "load_drop_down('requires/roll_wise_finish_fabric_sample_transfer_controller', ".$po_id."+'_'+".$transfer_criteria.", 'load_body_part', 'to_body_part_td' );";
			exit();
		}
	}
	else
	{

		$style_name_array=return_library_array( "select id, style_ref_no from sample_development_mst",'id','style_ref_no');

		// echo "select a.id as booking_id, a.booking_no, a.booking_no_prefix_num, a.buyer_id, b.id as booking_dtls_id, b.style_id, b.body_part, b.finish_fabric,c.batch_no,c.id as batch_id
		// from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, pro_batch_create_mst c  
		// where a.booking_no=b.booking_no and a.booking_no=c.booking_no and b.id=$po_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	
		$data_array=sql_select("SELECT a.id as booking_id, a.booking_no, a.booking_no_prefix_num, a.buyer_id, b.id as booking_dtls_id, b.style_id, b.body_part, b.finish_fabric,c.batch_no,c.id as batch_id
		from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, pro_batch_create_mst c  
		where a.booking_no=b.booking_no and a.booking_no=c.booking_no and b.id=$po_id and c.id=$batch_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		 
		foreach ($data_array as $row)
		{ 
			
			echo "document.getElementById('txt_to_order_book_no').value 		= '".$row[csf("booking_no")]."';\n";
			echo "document.getElementById('hidden_book_no').value 		        = '".$row[csf("booking_no")]."';\n";
			echo "document.getElementById('txt_to_order_book_id').value 		= '".$row[csf("booking_id")]."';\n";
			echo "document.getElementById('txt_to_order_book_dtls_id').value 	= '".$row[csf("booking_dtls_id")]."';\n";
			echo "document.getElementById('txt_to_qnty').value 					= '".$row[csf("finish_fabric")]."';\n";
			echo "document.getElementById('cbo_to_buyer_name').value 			= '".$row[csf("buyer_id")]."';\n";
			echo "document.getElementById('txt_to_style_ref').value 			= '".$style_name_array[$row[csf("style_id")]]."';\n";
			//echo "document.getElementById('cbo_to_body_part').value 			= '".$row[csf("body_part")]."';\n";
			echo "document.getElementById('txt_to_batch_no').value 			    = '".$row[csf("batch_no")]."';\n";
			echo "document.getElementById('txt_to_batch_id').value 			    = '".$row[csf("batch_id")]."';\n";
			echo "load_drop_down('requires/roll_wise_finish_fabric_sample_transfer_controller', ".$row[csf('booking_id')]."+'_'+".$transfer_criteria.", 'load_body_part', 'to_body_part_td' );";

			// echo "load_drop_down('requires/roll_wise_finish_fabric_sample_transfer_controller', ".$po_id."+'_'+".$transfer_criteria.", 'load_body_part', 'to_body_part_td' );";
			exit();
		}
	}	
}

if ($action=="load_body_part")
{
	
	$data=explode("_",$data);
	//var_dump($data);
	$order_id = $data[0];
	$transfer_criteria = $data[1];

	if($transfer_criteria == 7)
	{
		$body_part_sql = sql_select("SELECT a.body_part_id from wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b where a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0  and b.status_active = 1 and b.is_deleted = 0 and b.pre_cost_fabric_cost_dtls_id = a.id and b.po_break_down_id =$order_id and b.booking_type =1 union all select b.body_part_id from wo_pre_cost_fab_conv_cost_dtls a, wo_pre_cost_fabric_cost_dtls b, wo_booking_dtls c where b.job_no=c.job_no and a.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.pre_cost_fabric_cost_dtls_id = a.id and c.po_break_down_id =$order_id and a.fabric_description = b.id and c.booking_type = 4");
	}
	else
	{
		$body_part_sql = sql_select("select b.body_part as body_part_id
		from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b
		where a.booking_no=b.booking_no and a.booking_type=4 and a.id=$order_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0");
	}

	foreach ($body_part_sql as $row) 
	{
		$body_part_arr[$row[csf("body_part_id")]] = $row[csf("body_part_id")]; 
	}
	$body_part_ids = implode(",",array_filter($body_part_arr));
	if($body_part_ids != "")
	{
		echo create_drop_down( "cbo_to_body_part", 160,$body_part,"", 1, "--Select--", 0, "",0,$body_part_ids );
	}else{
		echo create_drop_down( "cbo_to_body_part", 160,$blank_array,"", 1, "--Select--", 0, "",0,"" );
	}
	
	exit();
}

if($action=="show_dtls_list_view")
{
	$data=explode("**", $data);
	$booking_order_id = $data[0];
	//var_dump($booking_order_id);
	$transfer_criteria = $data[1];
	$body_part_id = $data[2];
	$batch_id = $data[3];
	$store_id = $data[4];

	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');	
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');	
	$batch_arr=return_library_array( "select id,batch_no from  pro_batch_create_mst",'id','batch_no');
	//$product_arr=return_library_array( "select id, product_name_details from product_details_master where item_category_id=2",'id','product_name_details');

	$delv_arr=return_library_array("select barcode_num, grey_sys_id from pro_grey_prod_delivery_dtls where entry_form=67 and order_id=$booking_order_id", "barcode_num", "grey_sys_id");	
				
	// $programArr=return_library_array("select a.id, a.booking_id from inv_receive_master a, pro_roll_details b where a.id=b.mst_id and a.entry_form=2 and a.receive_basis=2 and b.entry_form=2 and b.po_breakdown_id=$booking_order_id and b.status_active=1 and b.is_deleted=0", "id", "booking_id");

	$store_room_rack_arr = return_library_array("select a.floor_room_rack_id, a.floor_room_rack_name from lib_floor_room_rack_mst a,  lib_floor_room_rack_dtls b where a.floor_room_rack_id = b.floor_room_rack_dtls_id and a.status_active =1 and b.status_active =1 group by a.floor_room_rack_id, a.floor_room_rack_name","floor_room_rack_id","floor_room_rack_name");

	if ($transfer_criteria==6) // order
	{
		$sql="SELECT a.id, a.entry_form, a.receive_basis, a.booking_id, b.prod_id, b.floor as floor_id, b.room, cast(b.rack_no as varchar(4000)) as rack, b.shelf_no as self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 1 as type, a.store_id, d.gsm, d.dia_width, d.detarmination_id, d.item_description, d.product_name_details, b.body_part_id,b.batch_id,b.color_id
		from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c, product_details_master d
		WHERE a.id=b.mst_id and b.id=c.dtls_id and b.prod_id = d.id and b.trans_id<>0 and a.entry_form in(68,126) and c.entry_form in(68,126) and c.re_transfer=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=$booking_order_id and b.body_part_id=$body_part_id and b.batch_id=$batch_id and a.store_id=$store_id and c.is_sales=0 and c.booking_without_order=0
		union all
		select a.id, a.entry_form, 0 as receive_basis, 0 as booking_id, b.to_prod_id as prod_id, b.to_floor_id as floor_id, b.to_room as room, cast(b.to_rack as varchar(4000)) as rack, b.to_shelf as self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 2 as type, b.to_store as store_id, d.gsm, d.dia_width, d.detarmination_id, d.item_description, d.product_name_details, b.to_body_part as body_part_id,b.batch_id,b.color_id
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, product_details_master d
		WHERE a.id=b.mst_id and b.id=c.dtls_id and b.to_prod_id = d.id  and c.entry_form in(134,214) and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=$booking_order_id and b.to_body_part=$body_part_id and b.to_batch_id=$batch_id and b.to_store=$store_id and c.booking_without_order=0";
	}
	else // sample
	{	

		$sql="SELECT a.id, a.entry_form, a.receive_basis, a.booking_id, b.prod_id, b.floor as floor_id, b.room, cast(b.rack_no as varchar(4000)) as rack, b.shelf_no as self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 1 as type, a.store_id, d.gsm, d.dia_width, d.detarmination_id, d.item_description, d.product_name_details, b.body_part_id,b.batch_id,b.color_id
		from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c, product_details_master d
		WHERE a.id=b.mst_id and b.id=c.dtls_id and b.prod_id = d.id and a.entry_form in(68,126) and b.trans_id<>0 and c.entry_form in(68,126) and c.booking_without_order=1 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=$booking_order_id and b.body_part_id=$body_part_id and b.batch_id=$batch_id and a.store_id=$store_id and c.re_transfer =0

		UNION ALL

		SELECT a.id,a.entry_form, 0 as receive_basis, 0 as booking_id, b.to_prod_id as prod_id, b.to_floor_id as floor_id, b.to_room as room, cast(b.to_rack as varchar(4000)) as rack, b.to_shelf as self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 3 as type , b.to_store as store_id, d.gsm, d.dia_width, d.detarmination_id, d.item_description, d.product_name_details, b.to_body_part as body_part_id,b.to_batch_id,b.color_id
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, product_details_master d
		where a.id=b.mst_id and b.id=c.dtls_id and b.to_prod_id = d.id and a.entry_form in(214) and c.entry_form in(214) and c.status_active=1 and c.is_deleted=0 
		and a.to_order_id =$booking_order_id and b.to_body_part=$body_part_id and b.to_batch_id=$batch_id and b.batch_id=$batch_id and c.booking_without_order=1 and c.re_transfer =0";
		
		// UNION ALL

		// SELECT a.id,a.entry_form, 0 as receive_basis, 0 as booking_id, b.to_prod_id as prod_id, b.to_floor_id as floor_id, b.to_room as room, b.to_rack as rack, b.to_shelf as self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 3 as type , b.to_store as store_id, d.gsm, d.dia_width, d.detarmination_id, d.item_description, d.product_name_details, b.to_body_part as body_part_id,b.to_batch_id,b.color_id
		// from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, product_details_master d
		// where a.id=b.mst_id and b.id=c.dtls_id and b.to_prod_id = d.id and a.entry_form in(134) and c.entry_form in(134) and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id =$booking_order_id and b.to_batch_id=$batch_id and b.to_body_part=$body_part_id  and c.booking_without_order=1 and c.re_transfer=0
		// order by barcode_no
	}
	//echo $sql;
	$data_array=sql_select($sql);	
	$i=1;$barcod_NOs="";$po_breakdown_Ids="";$batch_Ids="";
	foreach($data_array as $row)
	{
		$barcod_NOs_arr[$row[csf('barcode_no')]] =$row[csf('barcode_no')];
		$po_breakdown_Ids_arr[$row[csf('po_breakdown_id')]] =$row[csf('po_breakdown_id')];
		$batch_Ids_arr[$row[csf('batch_id')]] =$row[csf('batch_id')];
	} 

	$batch_Ids_arr = array_filter($batch_Ids_arr);
	if(count($batch_Ids_arr)>0)
	{
		$batch_Ids = implode(",", $batch_Ids_arr);
		$all_batch_cond=""; $batchCond="";
		if($db_type==2 && count($batch_Ids_arr)>999)
		{
			$batch_Ids_arr_chunk=array_chunk($batch_Ids_arr,999) ;
			foreach($batch_Ids_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$batchCond.=" b.id in($chunk_arr_value) or ";
			}

			$all_batch_cond.=" and (".chop($batchCond,'or ').")";
		}
		else
		{
			$all_batch_cond=" and b.id in($batch_Ids)";
		}
	} 
	
	$po_breakdown_Ids_arr = array_filter($po_breakdown_Ids_arr);
	if(count($po_breakdown_Ids_arr)>0)
	{
		$po_breakdown_Ids = implode(",", $po_breakdown_Ids_arr);
		$all_Breakdown_cond=""; $poBreakdownCond="";
		if($db_type==2 && count($po_breakdown_Ids_arr)>999)
		{
			$po_breakdown_Ids_arr_chunk=array_chunk($po_breakdown_Ids_arr,999) ;
			foreach($po_breakdown_Ids_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$poBreakdownCond.=" id in($chunk_arr_value) or ";
			}

			$all_Breakdown_cond.=" and (".chop($poBreakdownCond,'or ').")";
		}
		else
		{
			$all_Breakdown_cond=" and id in($po_breakdown_Ids)";
		}
	}

	$barcod_NOs_arr = array_filter($barcod_NOs_arr);
	if(count($barcod_NOs_arr)>0)
	{
		$barcod_NOs = implode(",", $barcod_NOs_arr);
		$all_barcode_no_cond=""; $barCond="";
		if($db_type==2 && count($barcod_NOs_arr)>999)
		{
			$barcod_NOs_arr_chunk=array_chunk($barcod_NOs_arr,999) ;
			foreach($barcod_NOs_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$barCond.=" barcode_no in($chunk_arr_value) or ";
			}

			$all_barcode_no_cond.=" and (".chop($barCond,'or ').")";
		}
		else
		{
			$all_barcode_no_cond=" and barcode_no in($barcod_NOs)";
		}
	}

	$sql_breakDown= "SELECT id, po_number from  wo_po_break_down where  is_deleted=0 and status_active in(1,3) $all_Breakdown_cond order by id, pub_shipment_date";
	//echo $sql_breakDown;die;
	$sql_breakDown_result = sql_select($sql_breakDown);
	$breakDown_arr=array();
	foreach ($sql_breakDown_result as $row) 
	{
		$breakDown_arr[$row[csf('id')]]['po_number'] = $row[csf('po_number')];
	}
	unset($sql_breakDown_result);

	$sql_smn_booking = "SELECT a.id as booking_id, a.booking_no, b.id from wo_non_ord_samp_booking_mst a,pro_batch_create_mst b where a.booking_no=b.booking_no and a.booking_type=4 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $all_batch_cond order by a.id, b.id";

	//echo $sql_smn_booking;
	$sql_smn_booking_result = sql_select($sql_smn_booking);
	$smn_booking_arr=array();
	foreach ($sql_smn_booking_result as $row) 
	{
		$smn_booking_arr[$row[csf('id')]]['booking_no'] = $row[csf('booking_no')];
	}
	unset($sql_smn_booking);

	$barcodeData = sql_select("select barcode_no,entry_form from pro_roll_details where entry_form=71 and status_active=1 and is_deleted=0 and is_returned = 0 $all_barcode_no_cond");
	foreach ($barcodeData as $row) 
	{
		$issued_barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
	}

	// echo "<pre>";
	// print_r($issued_barcode_arr);die;

	foreach($data_array as $row)
	{
		if($issued_barcode_arr[$row[csf('barcode_no')]]=="")
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		
			
			 $transRollId=$row[csf('roll_id')];

			if ($transfer_criteria==6) 
			{
				$po_breakdown_booking = $breakDown_arr[$row[csf('po_breakdown_id')]]['po_number'];
			}
			else
			{
				$po_breakdown_booking = $smn_booking_arr[$row[csf('batch_id')]]['booking_no'];
			}
			
			?>
			<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
				<td width="40" align="center" valign="middle"><input type="checkbox" id="tbl_<? echo $i;?>" name="check[]" /></td> 
				<td width="40"><? echo $i; ?></td>
				<td width="100"><? echo $row[csf('barcode_no')]; ?></td>
				<td width="50"><? echo $row[csf('roll_no')]; ?></td>
				<td width="150"><p><? echo $batch_arr[$row[csf('batch_id')]]; ?>&nbsp;</p></td>
				<td width="60"><p><? echo $row[csf('prod_id')]; ?></p></td>
				<td width="200" align="center"><p><? echo $row[csf('product_name_details')]; ?>&nbsp;</p></td>
				<td width="120" align="center"><p><? echo $po_breakdown_booking; ?>&nbsp;</p></td>
				<td width="70" align="center"><p><? echo $color_arr[$row[csf('color_id')]]; ?>&nbsp;</p></td>
				<td width="55"><p><? echo $store_room_rack_arr[$row[csf('floor_id')]]; ?>&nbsp;</p></td>
				<td width="55"><p><? echo $store_room_rack_arr[$row[csf('room')]]; ?>&nbsp;</p></td>
				<td width="55"><p><? echo $store_room_rack_arr[$row[csf('rack')]]; ?>&nbsp;</p></td>
				<td width="55"><p><? echo $store_room_rack_arr[$row[csf('self')]]; ?>&nbsp;</p></td>
				<td align="left" style="padding-right:2px"><? echo $row[csf('qnty')]; ?>
                	<input type="hidden" name="barcodeNo[]" id="barcodeNo_<? echo $i; ?>" value="<? echo $row[csf('barcode_no')]; ?>"/>
                    <input type="hidden" name="rollNo[]" id="rollNo_<? echo $i; ?>" value="<? echo $row[csf('roll_no')]; ?>"/>
                    <input type="hidden" name="progId[]" id="progId_<? echo $i; ?>" value="<? //echo $program_no; ?>"/>
                    <input type="hidden" name="productId[]" id="productId_<? echo $i; ?>" value="<? echo $row[csf('prod_id')]; ?>"/>
                    <input type="hidden" name="rollId[]" id="rollId_<? echo $i; ?>" value="<? echo $row[csf('roll_id_prev')]; ?>"/>
                    <input type="hidden" name="rollWgt[]" id="rollWgt_<? echo $i; ?>" value="<? echo $row[csf('qnty')]; ?>"/>
                    <input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" value="<? echo $row[csf('color_id')]; ?>"/>
                    <input type="hidden" name="floor[]" id="floor_<? echo $i; ?>" value="<? echo $row[csf('floor_id')]; ?>"/>
                    <input type="hidden" name="room[]" id="room_<? echo $i; ?>" value="<? echo $row[csf('room')]; ?>"/>
                    <input type="hidden" name="rack[]" id="rack_<? echo $i; ?>" value="<? echo $row[csf('rack')]; ?>"/>
                    <input type="hidden" name="shelf[]" id="shelf_<? echo $i; ?>" value="<? echo $row[csf('self')]; ?>"/>
                    <input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>" value=""/>
                    <input type="hidden" name="requiDtlsId[]" id="requiDtlsId_<? echo $i; ?>" value=""/>
                    <input type="hidden" name="transIdFrom[]" id="transIdFrom_<? echo $i; ?>" value=""/>
                    <input type="hidden" name="transIdTo[]" id="transIdTo_<? echo $i; ?>" value=""/>
                    <input type="hidden" name="rolltableId[]" id="rolltableId_<? echo $i; ?>" value=""/>
                    <input type="hidden" name="transRollId[]" id="transRollId_<? echo $i; ?>" value="<? echo $transRollId ?>"/>
                    <input type="hidden" name="storeId[]" id="storeId_<? echo $i; ?>" value="<? echo $row[csf('store_id')]; ?>"/>

                    <input type="hidden" name="fromProductUp[]" id="fromProductUp_<? echo $i;?>" value="<? echo $row[csf('prod_id')]; ?>">

                    <input type="hidden" name="gsm[]" id="gsm_<? echo $i; ?>" value="<? echo $row[csf('gsm')]; ?>"/>
                	<input type="hidden" name="diaWidth[]" id="diaWidth_<? echo $i; ?>" value="<? echo $row[csf('dia_width')]; ?>"/>
                	<input type="hidden" name="febDescripId[]" id="febDescripId_<? echo $i; ?>" value="<? echo $row[csf('detarmination_id')]; ?>"/>
                	<input type="hidden" name="constructCompo[]" id="constructCompo_<? echo $i; ?>" value="<? echo $row[csf('item_description')]; ?>"/>
                	<input type="hidden" name="bodyPart[]" id="bodyPart_<? echo $i; ?>" value="<? echo $row[csf('body_part_id')]; ?>"/>
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
	$cbo_transfer_criteria=$data[2];

	$entry_form_no = 214;

	// if($cbo_transfer_criteria==8) // Sample to Sample
	// {
	// 	$entry_form_no = 214;
	// }
	// elseif($cbo_transfer_criteria==7) // Sample to Order
	// {
	// 	$entry_form_no = 219;
	// }
	// else{
	// 	$entry_form_no = 216; // // Order to Sample
	// }

	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');	
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');	
	$batch_arr=return_library_array( "select id,batch_no from  pro_batch_create_mst",'id','batch_no');
	$color_arr=return_library_array( "select id,color_name from lib_color where is_deleted=0 and status_active=1 order by color_name",'id','color_name');
	//$product_arr=return_library_array( "select id, product_name_details from product_details_master where item_category_id=2",'id','product_name_details');
	$delv_arr=return_library_array("select barcode_num, grey_sys_id from pro_grey_prod_delivery_dtls where entry_form=67 and order_id=$order_id", "barcode_num", "grey_sys_id");
	//$trans_arr=return_library_array("select barcode_no, mst_id from pro_roll_details where entry_form=2", "barcode_no", "mst_id");
	
	$re_trans_arr=return_library_array("select barcode_no from pro_roll_details where entry_form=$entry_form_no and re_transfer=1 and status_active=1 and is_deleted=0 and mst_id=$mst_id ", "barcode_no", "barcode_no");

	$issued_barcode_arr=return_library_array("select barcode_no from pro_roll_details where entry_form=71 and status_active=1 and is_deleted=0 and po_breakdown_id=$order_id and is_returned = 0","barcode_no", "barcode_no");
	
	$transfer_arr=array();
	$transfer_dataArray=sql_select("select a.id, a.trans_id, a.to_trans_id, b.id as roll_id, b.barcode_no from inv_item_transfer_dtls a, pro_roll_details b where a.id=b.dtls_id and a.mst_id=$mst_id and b.mst_id=$mst_id and b.entry_form=$entry_form_no and b.transfer_criteria=$cbo_transfer_criteria and b.status_active=1 and b.is_deleted=0");
	foreach($transfer_dataArray as $row)
	{
		$transfer_arr[$row[csf('barcode_no')]]['dtls_id']=$row[csf('id')];
		$transfer_arr[$row[csf('barcode_no')]]['from_trans_id']=$row[csf('trans_id')];
		$transfer_arr[$row[csf('barcode_no')]]['to_trans_id']=$row[csf('to_trans_id')];
		$transfer_arr[$row[csf('barcode_no')]]['rolltableId']=$row[csf('roll_id')];
	}


	$store_room_rack_arr = return_library_array("select a.floor_room_rack_id, a.floor_room_rack_name from lib_floor_room_rack_mst a,  lib_floor_room_rack_dtls b where a.floor_room_rack_id = b.floor_room_rack_dtls_id and a.status_active =1 and b.status_active =1 group by a.floor_room_rack_id, a.floor_room_rack_name","floor_room_rack_id","floor_room_rack_name");
	
	$programArr=return_library_array("select a.id, a.booking_id from inv_receive_master a, pro_roll_details b where a.id=b.mst_id and a.entry_form=2 and a.receive_basis=2 and b.entry_form=2 and b.po_breakdown_id=$order_id and b.status_active=1 and b.is_deleted=0", "id", "booking_id");
	
	$sql="SELECT a.id, a.entry_form,a.from_order_id, 0 as receive_basis, 0 as booking_id, b.from_prod_id, to_prod_id as prod_id, b.floor_id, b.room, b.rack as rack, b.shelf as self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 3 as type , b.to_store as store_id, transfer_requ_dtls_id, d.gsm, d.dia_width, d.item_description, d.product_name_details, d.detarmination_id,b.color_id,b.batch_id,b.transfer_qnty
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, product_details_master d
	WHERE a.id=b.mst_id and b.id=c.dtls_id and b.from_prod_id=d.id and a.entry_form in($entry_form_no) and c.entry_form in($entry_form_no) and c.status_active=1 and c.is_deleted=0 and c.mst_id=$mst_id
	order by barcode_no";
	
	//echo $sql;
	
	$data_array=sql_select($sql);	
	$i=1;
	
	$i=1;$po_breakdown_Ids="";$batch_Ids="";
	foreach($data_array as $row)
	{
		
		$po_breakdown_Ids_arr[$row[csf('from_order_id')]] =$row[csf('from_order_id')];
		$batch_Ids_arr[$row[csf('batch_id')]] =$row[csf('batch_id')];
	} 

	$batch_Ids_arr = array_filter($batch_Ids_arr);
	if(count($batch_Ids_arr)>0)
	{
		$batch_Ids = implode(",", $batch_Ids_arr);
		$all_batch_cond=""; $batchCond="";
		if($db_type==2 && count($batch_Ids_arr)>999)
		{
			$batch_Ids_arr_chunk=array_chunk($batch_Ids_arr,999) ;
			foreach($batch_Ids_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$batchCond.=" b.id in($chunk_arr_value) or ";
			}

			$all_batch_cond.=" and (".chop($batchCond,'or ').")";
		}
		else
		{
			$all_batch_cond=" and b.id in($batch_Ids)";
		}
	} 
	
	$po_breakdown_Ids_arr = array_filter($po_breakdown_Ids_arr);
	if(count($po_breakdown_Ids_arr)>0)
	{
		$po_breakdown_Ids = implode(",", $po_breakdown_Ids_arr);
		$all_Breakdown_cond=""; $poBreakdownCond="";
		if($db_type==2 && count($po_breakdown_Ids_arr)>999)
		{
			$po_breakdown_Ids_arr_chunk=array_chunk($po_breakdown_Ids_arr,999) ;
			foreach($po_breakdown_Ids_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$poBreakdownCond.=" id in($chunk_arr_value) or ";
			}

			$all_Breakdown_cond.=" and (".chop($poBreakdownCond,'or ').")";
		}
		else
		{
			$all_Breakdown_cond=" and id in($po_breakdown_Ids)";
		}
	}

	$sql_breakDown= "SELECT id, po_number from  wo_po_break_down where  is_deleted=0 and status_active in(1,3) $all_Breakdown_cond order by id, pub_shipment_date";
	//echo $sql_breakDown;die;
	$sql_breakDown_result = sql_select($sql_breakDown);
	$breakDown_arr=array();
	foreach ($sql_breakDown_result as $row) 
	{
		$breakDown_arr[$row[csf('id')]]['po_number'] = $row[csf('po_number')];
	}
	unset($sql_breakDown_result);

	$sql_smn_booking = "SELECT a.id as booking_id, a.booking_no, b.id from wo_non_ord_samp_booking_mst a,pro_batch_create_mst b where a.booking_no=b.booking_no and a.booking_type=4 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $all_batch_cond order by a.id, b.id";

	//echo $sql_smn_booking;
	$sql_smn_booking_result = sql_select($sql_smn_booking);
	$smn_booking_arr=array();
	foreach ($sql_smn_booking_result as $row) 
	{
		$smn_booking_arr[$row[csf('id')]]['booking_no'] = $row[csf('booking_no')];
	}
	unset($sql_smn_booking);

	foreach($data_array as $row)
	{  
		if($issued_barcode_arr[$row[csf('barcode_no')]]=="")
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		
			
			$transRollId=$row[csf('roll_id')];
			
			if($transfer_arr[$row[csf('barcode_no')]]['dtls_id']=="")
			{
				$checked=""; 	
			}
			else $checked="checked"; 
			
			if($re_trans_arr[$row[csf('barcode_no')]]=="")
			{
				$disabled=""; 	
			}
			else $disabled="disabled";
			if ($row[csf('transfer_requ_dtls_id')]!="") 
			{
				$disabled="disabled";
			}

			if ($cbo_transfer_criteria==6) 
			{
				$po_breakdown_booking = $breakDown_arr[$row[csf('from_order_id')]]['po_number'];
			}
			else
			{
				$po_breakdown_booking = $smn_booking_arr[$row[csf('batch_id')]]['booking_no'];
			}
			
			$dtls_id=$transfer_arr[$row[csf('barcode_no')]]['dtls_id'];
			$from_trans_id=$transfer_arr[$row[csf('barcode_no')]]['from_trans_id'];
			$to_trans_id=$transfer_arr[$row[csf('barcode_no')]]['to_trans_id'];
			$rolltableId=$transfer_arr[$row[csf('barcode_no')]]['rolltableId'];
		?>
			<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
				<td width="40" align="center" valign="middle"><input type="checkbox" id="tbl_<? echo $i;?>" <? echo $checked." ".$disabled; ?> name="check[]" /></td> 
				<td width="40"><? echo $i; ?></td>
				<td width="100"><? echo $row[csf('barcode_no')]; ?></td>
				<td width="50"><? echo $row[csf('roll_no')]; ?></td>
				<td width="150"><p><? echo $batch_arr[$row[csf('batch_id')]];  ?>&nbsp;</p></td>
				<td width="60"><p><? echo $row[csf('prod_id')]; ?></p></td>
				<td width="200"><p><? echo $row[csf('product_name_details')]; ?>&nbsp;</p></td>
				<td width="120"><p><? echo $po_breakdown_booking; ?>&nbsp;</p></td>
				<td width="70"><p><? echo $color_arr[$row[csf('color_id')]]; ?>&nbsp;</p></td>
				<td width="55"><p><? echo $store_room_rack_arr[$row[csf('floor_id')]]; ?>&nbsp;</p></td>
				<td width="55"><p><? echo $store_room_rack_arr[$row[csf('room')]]; ?>&nbsp;</p></td>
				<td width="55"><p><? echo $store_room_rack_arr[$row[csf('rack')]]; ?>&nbsp;</p></td>
				<td width="55"><p><? echo $store_room_rack_arr[$row[csf('self')]]; ?>&nbsp;</p></td>
				<td align="left" style="padding-right:2px"><? echo $row[csf('qnty')]; ?>
                	<input type="hidden" name="barcodeNo[]" id="barcodeNo_<? echo $i; ?>" value="<? echo $row[csf('barcode_no')]; ?>"/>
                    <input type="hidden" name="rollNo[]" id="rollNo_<? echo $i; ?>" value="<? echo $row[csf('roll_no')]; ?>"/>
                    <input type="hidden" name="progId[]" id="progId_<? echo $i; ?>" value="<? echo $program_no; ?>"/>
                    <input type="hidden" name="productId[]" id="productId_<? echo $i; ?>" value="<? echo $row[csf('prod_id')]; ?>"/>
                    <input type="hidden" name="rollId[]" id="rollId_<? echo $i; ?>" value="<? echo $row[csf('roll_id_prev')]; ?>"/>
                    <input type="hidden" name="rollWgt[]" id="rollWgt_<? echo $i; ?>" value="<? echo $row[csf('qnty')]; ?>"/>
                    <input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" value="<? echo $row[csf('color_id')]; ?>"/>
                   
                    <input type="hidden" name="floor[]" id="floor_<? echo $i; ?>" value="<? echo $row[csf('floor_id')]; ?>"/>
                    <input type="hidden" name="room[]" id="room_<? echo $i; ?>" value="<? echo $row[csf('room')]; ?>"/>
                    <input type="hidden" name="rack[]" id="rack_<? echo $i; ?>" value="<? echo $row[csf('rack')]; ?>"/>
                    <input type="hidden" name="shelf[]" id="shelf_<? echo $i; ?>" value="<? echo $row[csf('self')]; ?>"/>
                    <input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>" value="<? echo $dtls_id; ?>"/>
                    <input type="hidden" name="requiDtlsId[]" id="requiDtlsId_<? echo $i; ?>" value="<? echo $row[csf('transfer_requ_dtls_id')]; ?>"/>
                    <input type="hidden" name="transIdFrom[]" id="transIdFrom_<? echo $i; ?>" value="<? echo $from_trans_id; ?>"/>
                    <input type="hidden" name="transIdTo[]" id="transIdTo_<? echo $i; ?>" value="<? echo $to_trans_id; ?>"/>
                    <input type="hidden" name="rolltableId[]" id="rolltableId_<? echo $i; ?>" value="<? echo $rolltableId; ?>"/>
                    <input type="hidden" name="transRollId[]" id="transRollId_<? echo $i; ?>" value="<? echo $transRollId ?>"/>
                    <input type="hidden" name="storeId[]" id="storeId_<? echo $i; ?>" value="<? echo $row[csf('store_id')]; ?>"/>
                    <input type="hidden" name="fromProductUp[]" id="fromProductUp_<? echo $i;?>" value="<? echo $row[csf('from_prod_id')]; ?>">

                    <input type="hidden" name="gsm[]" id="gsm_<? echo $i; ?>" value="<? echo $row[csf('gsm')]; ?>"/>
                	<input type="hidden" name="diaWidth[]" id="diaWidth_<? echo $i; ?>" value="<? echo $row[csf('dia_width')]; ?>"/>
                	<input type="hidden" name="febDescripId[]" id="febDescripId_<? echo $i; ?>" value="<? echo $row[csf('detarmination_id')]; ?>"/>
                	<input type="hidden" name="constructCompo[]" id="constructCompo_<? echo $i; ?>" value="<? echo $row[csf('item_description')]; ?>"/>
                	<input type="hidden" name="hiddenTransferqnty[]" id="hiddenTransferqnty_<? echo $i; ?>" value="<? echo $row[csf('transfer_qnty')]; ?>"/>
                </td>
			</tr>
		<? 
			$i++; 
		}
	} 
	exit();
}

if ($action=="sampleToOrderTransfer_popup")
{
	echo load_html_head_contents("Sample To Order Transfer Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 
	<script>
		function js_set_value(data)
		{
			// alert(data);return;
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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+<? echo $cbo_transfer_criteria; ?>, 'create_transfer_search_list_view', 'search_div', 'roll_wise_finish_fabric_sample_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
	$transfer_criteria =$data[3];
	$entry_form_no = 214;

	// if($transfer_criteria==8) // Sample to Sample
	// {
	// 	$entry_form_no = 214;
	// }
	// elseif($transfer_criteria==7) // Sample to Order
	// {
	// 	$entry_form_no = 219;
	// }
	// else{
	// 	$entry_form_no = 216; // // Order to Sample
	// }
	
	if($search_by==1)
	{
		$search_field="transfer_system_id";
	}
	else {
		$search_field="challan_no";
	}
	
	if($db_type==0) $year_field="YEAR(insert_date) as year,"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year,";
	else $year_field="";//defined Later
	
 	$sql="SELECT id, transfer_prefix_number, $year_field transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria, item_category from inv_item_transfer_mst where item_category=2 and company_id=$company_id and $search_field like '$search_string' and transfer_criteria=$transfer_criteria and entry_form=$entry_form_no and status_active=1 and is_deleted=0 order by id";
	//echo $sql;
	
	$arr=array(3=>$company_arr,5=>$item_transfer_criteria,6=>$item_category);

	echo  create_list_view("tbl_list_search", "Transfer ID,Year,Challan No,Company,Transfer Date,Transfer Criteria,Item Category", "80,70,100,80,90,130","750","250",0, $sql, "js_set_value", "id", "", 1, "0,0,0,company_id,0,transfer_criteria,item_category", $arr, "transfer_prefix_number,year,challan_no,company_id,transfer_date,transfer_criteria,item_category", '','','0,0,0,0,3,0,0');
	
	exit();
}

if($action=='populate_data_from_transfer_master')
{
	//$data_array=sql_select("SELECT transfer_criteria, transfer_system_id,challan_no, company_id, to_company, transfer_date, item_category, from_order_id, to_order_id, from_samp_dtls_id, to_samp_dtls_id, transfer_requ_no, transfer_requ_id from inv_item_transfer_mst where id='$data'");
	// echo "SELECT a.transfer_criteria, a.transfer_system_id,a.challan_no, a.company_id, a.to_company, a.transfer_date, a.item_category, a.from_order_id, a.to_order_id, a.from_samp_dtls_id, a.to_samp_dtls_id, a.transfer_requ_no, a.transfer_requ_id , b.to_store, b.from_store, b.to_floor_id, b.to_room, b.to_rack, b.to_shelf, b.body_part_id, b.to_body_part,b.batch_id,b.to_batch_id from inv_item_transfer_mst a, inv_item_transfer_dtls b where a.id = b.mst_id and a.id='$data' group by a.transfer_criteria, a.transfer_system_id,a.challan_no, a.company_id, a.to_company, a.transfer_date, a.item_category, a.from_order_id, a.to_order_id, a.from_samp_dtls_id, a.to_samp_dtls_id, a.transfer_requ_no, a.transfer_requ_id , b.to_store, b.from_store, b.to_floor_id,b.to_room, b.to_rack, b.to_shelf, b.body_part_id, b.to_body_part,b.batch_id,b.to_batch_id";die;

	$data_array=sql_select("SELECT a.transfer_criteria, a.transfer_system_id,a.challan_no, a.company_id, a.to_company, a.transfer_date, a.item_category, a.from_order_id, a.to_order_id, a.from_samp_dtls_id, a.to_samp_dtls_id, a.transfer_requ_no, a.transfer_requ_id , b.to_store, b.from_store, b.to_floor_id, b.to_room, b.to_rack, b.to_shelf, b.body_part_id, b.to_body_part,b.batch_id,b.to_batch_id from inv_item_transfer_mst a, inv_item_transfer_dtls b where a.id = b.mst_id and a.id='$data' group by a.transfer_criteria, a.transfer_system_id,a.challan_no, a.company_id, a.to_company, a.transfer_date, a.item_category, a.from_order_id, a.to_order_id, a.from_samp_dtls_id, a.to_samp_dtls_id, a.transfer_requ_no, a.transfer_requ_id , b.to_store, b.from_store, b.to_floor_id,b.to_room, b.to_rack, b.to_shelf, b.body_part_id, b.to_body_part,b.batch_id,b.to_batch_id");

	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('update_id').value 					= '".$data."';\n";
		echo "document.getElementById('cbo_transfer_criteria').value 		= '".$row[csf("transfer_criteria")]."';\n";
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("transfer_system_id")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_company_id_to').value 			= '".$row[csf("to_company")]."';\n";
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_requisition_no').value 			= '".$row[csf("transfer_requ_no")]."';\n";
		echo "document.getElementById('txt_requisition_id').value 			= '".$row[csf("transfer_requ_id")]."';\n";
		echo "document.getElementById('txt_transfer_date').value 			= '".change_date_format($row[csf("transfer_date")])."';\n";

		echo "document.getElementById('pre_cbo_company_id').value 			= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('pre_cbo_company_id_to').value 		= '".$row[csf("to_company")]."';\n";
		echo "document.getElementById('cbo_from_body_part').value 			= '".$row[csf("body_part_id")]."';\n";
		//echo "document.getElementById('cbo_to_body_part').value 			= '".$row[csf("to_body_part")]."';\n";
		



		$transfer_criteria=$row[csf("transfer_criteria")];
		if($transfer_criteria==8) // Sample to Sample
		{
			$from_order_book_id = $row[csf("from_samp_dtls_id")];
			$to_order_book_id = $row[csf("to_samp_dtls_id")]; 
		}
		elseif($transfer_criteria==7) // Sample to Order
		{
			$from_order_book_id = $row[csf("from_samp_dtls_id")];
			$to_order_book_id = $row[csf("to_order_id")];
			
		}
		else // Order to Sample
		{ 
			$from_order_book_id = $row[csf("from_order_id")];
			$to_order_book_id = $row[csf("to_samp_dtls_id")]; 
		}
		echo "get_php_form_data('".$from_order_book_id."**"."from"."**".$row[csf("transfer_criteria")]."**".$row[csf("body_part_id")]."**".$row[csf("batch_id")]."','populate_data_from_sample','requires/roll_wise_finish_fabric_sample_transfer_controller');\n";
		echo "get_php_form_data('".$to_order_book_id."**".$row[csf("transfer_criteria")]."**".$row[csf("to_body_part")]."**".$row[csf("to_batch_id")]."','populate_data_to_order','requires/roll_wise_finish_fabric_sample_transfer_controller');\n";
		/*echo "get_php_form_data('".$row[csf("from_order_id")]."**"."from"."**".$row[csf("transfer_criteria")]."','populate_data_from_sample','requires/roll_wise_finish_fabric_sample_transfer_controller');\n";
		echo "get_php_form_data('".$row[csf("to_order_id")]."**".$row[csf("transfer_criteria")]."','populate_data_to_order','requires/roll_wise_finish_fabric_sample_transfer_controller');\n";*/

		// echo "load_drop_down( 'requires/roll_wise_finish_fabric_sample_transfer_controller','".$row[csf("transfer_criteria")].'_'.$row[csf("company_id")]."', 'load_drop_store_from', 'from_store_td' );\n";

		echo "$('#cbo_transfer_criteria').attr('disabled','disabled');\n";
		echo "$('#cbo_company_id').attr('disabled','disabled');\n";
		echo "$('#cbo_company_id_to').attr('disabled','disabled');\n";
		echo "$('#txt_from_order_book_no').attr('disabled','disabled');\n";
		echo "$('#txt_to_order_book_no').attr('disabled','disabled');\n";
		echo "$('#txt_requisition_no').attr('disabled','disabled');\n";
		echo "$('#cbo_store_name').attr('disabled','disabled');\n";

		

		echo "load_room_rack_self_bin('requires/roll_wise_finish_fabric_sample_transfer_controller*2*cbo_store_name_to', 'store','to_store_td', $('#cbo_company_id_to').val(),'','','','','','','','store_load_cond()');\n";

		echo "document.getElementById('cbo_store_name').value 			    = '".$row[csf("from_store")]."';\n";
		echo "document.getElementById('cbo_to_body_part').value 			= '".$row[csf("to_body_part")]."';\n";

		echo "$('#cbo_store_name').removeAttr('onchange');\n";

		if($row[csf("to_store")]>0)
		{
			echo "document.getElementById('cbo_store_name_to').value 				= '".$row[csf("to_store")]."';\n";
		}
		if($row[csf("to_floor_id")]>0){
			echo "load_room_rack_self_bin('requires/roll_wise_finish_fabric_sample_transfer_controller*2*cbo_floor_to', 'floor','floor_td_to', '".$row[csf("to_company")]."','"."','".$row[csf('to_store')]."',this.value);\n";
			echo "document.getElementById('cbo_floor_to').value 			= '".$row[csf("to_floor_id")]."';\n";
		}
		if($row[csf("to_room")]>0){
			echo "load_room_rack_self_bin('requires/roll_wise_finish_fabric_sample_transfer_controller*2*cbo_room_to', 'room','room_td_to', '".$row[csf("to_company")]."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."',this.value);\n";
			echo "document.getElementById('cbo_room_to').value 				= '".$row[csf("to_room")]."';\n";
		}
		if($row[csf("to_rack")]>0){
			echo "load_room_rack_self_bin('requires/roll_wise_finish_fabric_sample_transfer_controller*2*txt_rack_to', 'rack','rack_td_to','".$row[csf("to_company")]."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."','".$row[csf('to_room')]."',this.value);\n";
			echo "document.getElementById('txt_rack_to').value 				= '".$row[csf("to_rack")]."';\n";
		}
		if($row[csf("to_shelf")]>0){
			echo "load_room_rack_self_bin('requires/roll_wise_finish_fabric_sample_transfer_controller*2*txt_shelf_to', 'shelf','shelf_td_to','".$row[csf("to_company")]."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."','".$row[csf('to_room')]."','".$row[csf('to_rack')]."',this.value);\n";
			echo "document.getElementById('txt_shelf_to').value 			= '".$row[csf("to_shelf")]."';\n";
		}


		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_finish_transfer_entry',1,1);\n"; 
		exit();
	} // +"'**'"+'".$row[csf("transfer_criteria")]."'
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
                    <td align="center"><? echo ucfirst($type); ?> Order No: <b><? echo $txt_to_order_book_no; ?></b></td>
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
					$req_qty=return_field_value("sum(b.grey_fab_qnty) as grey_req_qnty","wo_booking_mst a, wo_booking_dtls b","a.booking_no=b.booking_no and a.item_category in(2) and b.po_break_down_id=$txt_to_order_book_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1","grey_req_qnty");
					
					$sql="select 
								sum(CASE WHEN entry_form ='3' THEN quantity ELSE 0 END) AS issue_qnty,
								sum(CASE WHEN entry_form ='5' THEN quantity ELSE 0 END) AS dye_issue_qnty,
								sum(CASE WHEN entry_form ='9' THEN quantity ELSE 0 END) AS return_qnty,
								sum(CASE WHEN entry_form ='13' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_out_qnty,
								sum(CASE WHEN entry_form ='13' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_in_qnty,
								sum(CASE WHEN trans_id<>0 and entry_form in(2,22) THEN quantity ELSE 0 END) AS knit_qnty
							from order_wise_pro_details where po_breakdown_id=$txt_to_order_book_id and status_active=1 and is_deleted=0";
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

		$is_acknowledge = return_field_value("b.id id", "inv_item_transfer_mst a,inv_item_trans_acknowledgement b", "a.id=b.challan_id and  a.id=$update_id and a.status_active=1 and a.is_acknowledge=1", "id");
		if($is_acknowledge != "" )
		{
			echo "20**Update not allowed. This Transfer Challan is already Acknowledged.\nAcknowledge System ID = $is_acknowledge";
			die;
		}
	}
	
    for($k=1;$k<=$total_row;$k++)
    { 
        $productId="productId_".$k;
        $prod_ids.=$$productId.",";
        $barcodeNO="barcodeNo_".$k;
        $barcodeNOS.=$$barcodeNO.",";
    }
    // $prod_ids=implode(",",array_unique(explode(",",chop($prod_ids,','))));
    // $barcodeNOS=implode(",",array_unique(explode(",",chop($barcodeNOS,','))));


	$barcodeNOS=chop(str_replace("'","",$barcodeNOS),',');
	$barcodeNOS_array =  array_unique(explode(",", $barcodeNOS));

	
	$prod_ids=chop(str_replace("'","",$prod_ids),',');
	$prod_ids_array =  array_unique(explode(",", $prod_ids));
	

	$barcodeNOS_cond=""; $barcodeNOSCond="";
	if($db_type==2 && count($barcodeNOS_array)>999)
	{
		$barcodeNOS_array_chunk=array_chunk($barcodeNOS_array,999) ;
		foreach($barcodeNOS_array_chunk as $chunk_arr)
		{
			$chunk_arr_value=implode(",",$chunk_arr);
			$barcodeNOSCond.=" barcode_no in($chunk_arr_value) or ";
		}
		$barcodeNOS_cond.=" and (".chop($barcodeNOSCond,'or ').")";
	}
	else
	{
		$barcodeNOS_cond=" and barcode_no in($barcodeNOS)";
	}

	$prod_ids_cond=""; $prod_idsCond="";
	if($db_type==2 && count($prod_ids_array)>999)
	{
		$prod_ids_array_chunk=array_chunk($prod_ids_array,999) ;
		foreach($prod_ids_array_chunk as $chunk_arr)
		{
			$chunk_arr_value=implode(",",$chunk_arr);
			$prod_idsCond.=" prod_id in($chunk_arr_value) or ";
		}
		$prod_ids_cond.=" and (".chop($prod_idsCond,'or ').")";
	}
	else
	{
		$prod_ids_cond=" and prod_id in($prod_ids)";
	}
	
    $max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "transaction_type in (1,4,5) $prod_ids_cond", "max_date");      
	$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
	$trans_date = date("Y-m-d", strtotime(str_replace("'","",$txt_transfer_date)));
	if ($trans_date < $max_recv_date) 
    {
        echo "20**Transfer Date Can not Be Less Than Last Receive Date Of These Lot";
        die;
	}
	$cbo_transfer_criteria=str_replace("'","",$cbo_transfer_criteria);
	$entry_form_no = 214;
	$short_prefix_name="FFRSTE";

	// if($cbo_transfer_criteria==8) // Sample to Sample
	// {
	// 	$entry_form_no = 214;
	// 	$short_prefix_name="FFRSTE";
	// 	//$short_prefix_name="FFSTSTE";
	// }
	// elseif($cbo_transfer_criteria==7) // Sample to Order
	// {
	// 	$entry_form_no = 219;
	// 	$short_prefix_name="FFSTOTE";
	// }
	// else{
	// 	$entry_form_no = 216; // // Order to Sample
	// 	$short_prefix_name="FFOTSTE";
	// }
    //echo "10**".$entry_form_no;die;

	// Lib -> Variable Settings -> Inventory -> Variable List -> Auto Transfer Receive
	// if Auto Transfer Receive yes, then no need to acknowledgement
	$variable_auto_rcv = return_field_value("auto_transfer_rcv", "variable_settings_inventory", " company_name=$cbo_company_id and variable_list= 27 and item_category_id=2", "auto_transfer_rcv");
	if($variable_auto_rcv == "")
	{
		$variable_auto_rcv = 1;
	}

	if($variable_auto_rcv ==1 && str_replace("'","",$cbo_store_name_to) ==0)
	{
		echo "20**To Store field required";
		die;
	}
	
	$trans_check_sql = sql_select("select barcode_no,entry_form,po_breakdown_id,qnty from pro_roll_details where entry_form in ( 37,52,68,134,214,216,219 ) $barcodeNOS_cond and re_transfer =0 and status_active = 1 and is_deleted = 0 ");
	// union all select a.barcode_no,a.entry_form,a.po_breakdown_id, a.qnty from pro_roll_details a, pro_grey_prod_entry_dtls b where a.dtls_id=b.id and a.entry_form in (2) and b.trans_id<>0 and a.re_transfer =0 and a.barcode_no in($barcodeNOS) and a.status_active = 1 and a.is_deleted = 0

	if($trans_check_sql[0][csf("barcode_no")] !="")
	{
		foreach ($trans_check_sql as $val)
		{
			$trans_po_barcode_check_arr[$val[csf("barcode_no")]][$val[csf("po_breakdown_id")]] = $val[csf("barcode_no")]."__".$val[csf("po_breakdown_id")];
			$actual_wgt_arr[$val[csf("barcode_no")]] = $val[csf("qnty")];
		}
	}

	$cbo_from_body_part = str_replace("'", "", $cbo_from_body_part);
	$cbo_to_body_part 	= str_replace("'", "", $cbo_to_body_part);
	$txt_from_batch_id 	= str_replace("'", "", $txt_from_batch_id);
	$txt_to_batch_id 	= str_replace("'", "", $txt_to_batch_id);
	//echo "10**txt_from_batch_id : ". $txt_to_batch_id;die;

	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		for($x=1;$x<=$total_row;$x++)
		{
			$barcodeNo="barcodeNo_".$x;
			$all_barcodeNo.=$$barcodeNo.",";
			//$tot_rollWgt="rollWgt_".$x;
			//$tot_rollWgt2+=$$tot_rollWgt;
		
		}
		
		
		$all_barcodeNo=chop(str_replace("'", "", $all_barcodeNo),',');
		$all_barcodeNo_array =  array_unique(explode(",", $all_barcodeNo));

		$all_barcodeNo_cond=""; $barcodeNoCond="";
		if($db_type==2 && count($all_barcodeNo_array)>999)
		{
			$all_barcodeNo_array_chunk=array_chunk($all_barcodeNo_array,999) ;
			foreach($all_barcodeNo_array_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$barcodeNoCond.=" a.barcode_no in($chunk_arr_value) or ";
			}
			$all_barcodeNo_cond.=" and (".chop($barcodeNoCond,'or ').")";
		}
		else
		{
			$all_barcodeNo_cond=" and a.barcode_no in($all_barcodeNo)";
		}


		// echo "SELECT a.id,a.batch_no,a.color_id, a.batch_weight, a.booking_no from pro_batch_create_mst a where   a.status_active=1 and a.is_deleted=0 and a.entry_form in(14,68,134,214) and a.company_id=$cbo_company_id_to and a.batch_no=$txt_from_batch_no  group by a.id,a.batch_no,a.color_id,a.batch_weight, a.booking_no";die;
		$batchData=sql_select("SELECT a.id,a.batch_no,a.color_id, a.batch_weight, a.booking_no from pro_batch_create_mst a where   a.status_active=1 and a.is_deleted=0 and a.entry_form in(14,68,134,214) and a.company_id=$cbo_company_id_to and a.batch_no=$txt_from_batch_no  group by a.id,a.batch_no,a.color_id,a.batch_weight, a.booking_no");

		$batch_data_arr=array();
		foreach ($batchData as $rows)
		{
			$batch_data_arr[$rows[csf("batch_no")]][$rows[csf("color_id")]][$rows[csf("booking_no")]]['id']=$rows[csf("id")];
			$batch_data_arr[$rows[csf("batch_no")]][$rows[csf("color_id")]][$rows[csf("booking_no")]]['batch_weight']=$rows[csf("batch_weight")];
		}

		if($all_barcodeNo!="")
		{
			
			$issue_data_refer = sql_select("select a.id, a.barcode_no, b.issue_number from pro_roll_details a, inv_issue_master b where a.mst_id = b.id and a.entry_form = 71 $all_barcodeNo_cond and a.status_active = 1 and a.is_deleted = 0 and a.is_returned=0");
			if($issue_data_refer[0][csf("barcode_no")] != "")
			{
				echo "20**Sorry Barcode No  : ". $issue_data_refer[0][csf("barcode_no")] ."\nFound in Issue No ".$issue_data_refer[0][csf("issue_number")];
				disconnect($con);
				die;
			}
		}

		
		$transfer_recv_num=''; $transfer_update_id='';
		
		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
			
			$id = return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst", $con);
			$new_transfer_system_id = explode("*", return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst",$con,1,$cbo_company_id,$short_prefix_name,$entry_form_no,date("Y",time()),2 ));
					 
			$field_array="id, transfer_prefix, transfer_prefix_number, transfer_system_id, company_id, challan_no, transfer_date, entry_form, transfer_criteria, to_company, from_order_id, from_samp_dtls_id, to_order_id, to_samp_dtls_id, transfer_requ_no, transfer_requ_id, item_category, inserted_by, insert_date";

			$data_array="(".$id.",'".$new_transfer_system_id[1]."',".$new_transfer_system_id[2].",'".$new_transfer_system_id[0]."',".$cbo_company_id.",".$txt_challan_no.",".$txt_transfer_date.",".$entry_form_no.",".$cbo_transfer_criteria.",".$cbo_company_id_to.",".$txt_from_order_book_id.",".$txt_from_order_book_dtls_id.",".$txt_to_order_book_id.",".$txt_to_order_book_dtls_id.",".$txt_requisition_no.",".$txt_requisition_id.",2,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			//echo "10**insert into inv_item_transfer_mst (".$field_array.") values ".$data_array;die;

			$transfer_recv_num=$new_transfer_system_id[0];
			$transfer_update_id=$id;
		}
		else
		{
			$field_array_update="challan_no*transfer_date*from_order_id*from_samp_dtls_id*to_order_id, to_samp_dtls_id*updated_by*update_date";
			$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$txt_from_order_book_id."*".$txt_from_order_book_dtls_id."*".$txt_to_order_book_id."*".$txt_to_order_book_dtls_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			/*$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0; */
			
			$transfer_recv_num=str_replace("'","",$txt_system_id);
			$transfer_update_id=str_replace("'","",$update_id);
		}
		
		$rate=0; $amount=0;
		//$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;

		$field_array_trans="id, mst_id, company_id, prod_id, item_category, transaction_type, transaction_date, order_id, cons_uom, cons_quantity, cons_rate, cons_amount, floor_id, room, rack, self, store_id, inserted_by, insert_date, body_part_id,batch_id,pi_wo_batch_no";
		
		//$id_dtls=return_next_id( "id", "inv_item_transfer_dtls", 1 ) ; 

		$field_array_dtls="id, mst_id, trans_id, to_trans_id, from_prod_id, to_prod_id, item_category, transfer_qnty, roll, rate, transfer_value, uom, color_id, floor_id, room, rack, shelf, to_floor_id, to_room, to_rack, to_shelf, from_store, to_store, transfer_requ_dtls_id, active_dtls_id_in_transfer, inserted_by, insert_date, body_part_id, to_body_part,batch_id,to_batch_id, feb_description_id";
		

		$field_array_roll="id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, roll_no, roll_id, from_roll_id, is_transfer, transfer_criteria, booking_without_order, booking_no, re_transfer, inserted_by, insert_date";
		
		//$id_prop = return_next_id( "id", "order_wise_pro_details", 1 );
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, inserted_by, insert_date";
		
		$field_array_prod_insert = "id, company_id, store_id, item_category_id, detarmination_id, item_description, product_name_details, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, gsm, dia_width, inserted_by, insert_date";
		$field_array_prod_update = "current_stock*avg_rate_per_unit*stock_value*updated_by*update_date";

		$field_array_batch_dtls="id, mst_id, po_id, prod_id, item_description, roll_no, roll_id, barcode_no, batch_qnty, dtls_id,width_dia_type, inserted_by, insert_date";

		$rollIds='';
		for($j=1;$j<=$total_row;$j++)
		{
			$barcodeNo="barcodeNo_".$j;
			$rollNo="rollNo_".$j;
			$productId="productId_".$j;
			$rollId="rollId_".$j;
			$rollWgt="rollWgt_".$j;
			$colorId="colorId_".$j;
			$floor="floor_".$j;
			$room="room_".$j;
			$rack="rack_".$j;
			$shelf="shelf_".$j;
			$transRollId="transRollId_".$j;
			$storeId="storeId_".$j;
			$requiDtlsId="requiDtlsId_".$j;
			$febDescripId="febDescripId_".$j;
			$constructCompo="constructCompo_".$j;
			$gsm="gsm_".$j;
			$diaWidth="diaWidth_".$j;
			
			$rollIds.=$$transRollId.",";

			

			//------------------------------------VALIDATION FOR DUPLICATE---------------------------------------
			if($trans_po_barcode_check_arr[str_replace("'", "", $$barcodeNo)][str_replace("'", "", $txt_from_order_book_id)] != str_replace("'", "", $$barcodeNo)."__".str_replace("'", "", $txt_from_order_book_id))
			{
				if($cbo_transfer_criteria != 6)
				{
					echo "20**Sorry! This barcode =". $$barcodeNo ." doesn't belong to this from booking no";
				}
				else{
					echo "20**Sorry! This barcode  =". $$barcodeNo ." doesn't belong to this from order no";
				}
				disconnect($con);
				die;
			}



			if( number_format($actual_wgt_arr[str_replace("'","", $$barcodeNo)],2,".","")  != number_format(str_replace("'", "",$$rollWgt),2,".",""))
			{
				echo "20**Sorry! This barcode (". str_replace("'","", $$barcodeNo) .") is split. actual weight ". number_format($actual_wgt_arr[str_replace("'","", $$barcodeNo)],2,".","") ." doesn't match with current ".number_format(str_replace("'", "",$$rollWgt),2,".","") ."";
				disconnect($con);
				die;
			}
			
			$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
			$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);

			//---------------------------------------------------------------


			$batch_no          = str_replace("'", "", $txt_from_batch_no);
			$colorId           = str_replace("'", "", $$colorId);
			$txt_transfer_qnty = str_replace("'", "", $$rollWgt);
			$booking_no        = str_replace("'", "", $hidden_book_no);
			$booking_id        = str_replace("'", "", $txt_to_order_book_id);
		
			//echo "10**This batch_no =". $batch_no;die;
			$batchData = $batch_data_arr[$batch_no][$colorId][$booking_no]['id'];

			//echo "10**This batchData =". $batchData;die;
			
			if($batchData)
			{
				$batch_id_to=$batchData;
				//$curr_batch_weight=$batchData[0][csf('batch_weight')]+str_replace("'", '',$txt_transfer_qnty);
				$curr_batch_weight=$batch_data_arr[$batch_no][$colorId][$booking_no]['batch_weight']+$txt_transfer_qnty;
				$field_array_batch_update="batch_weight*updated_by*update_date";
				$data_array_batch_update=$curr_batch_weight."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			}
			else
			{
				if($cbo_transfer_criteria==7){
					$booking_without_order = 0;
				}else{
					$booking_without_order = 1;
				}
				//.",".$booking_without_order.",".
				if($new_created_batch[$batch_no][$colorId][$booking_no]['id'] == ""){

					$batch_id_to = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);
					$field_array_batch="id,batch_no,entry_form,batch_date,company_id,booking_no_id,booking_no,booking_without_order,color_id,batch_weight,inserted_by,insert_date";
				}
				//echo "10**This batchData =". $batch_id_to;die;
				if($data_array_batch!="") $data_array_batch.=",";
				$data_array_batch="(".$batch_id_to.",'".$batch_no."',".$entry_form_no.",".$txt_transfer_date.",".$cbo_company_id_to.",".$booking_id.",'".$booking_no."',".$booking_without_order.",".$colorId.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";


				$new_created_batch[$batch_no][$colorId][$booking_no]['id'] =$batch_id_to;
			}

			//---------------------------------------------------------------
			if($cbo_company_id != $cbo_company_id_to)
			{
				if (str_replace("'", "", $$diaWidth)=="")
				{
					if($db_type == 0){
						$dia_cond = " and dia_width = '' ";
					}else{
						$dia_cond = " and dia_width is null ";
					}
				}
				else
				{
					$dia_cond = " and dia_width = '".str_replace("'", "", $$diaWidth)."'";
				}
				$row_prod = sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where company_id=$cbo_company_id_to and item_category_id=2 and detarmination_id=".$$febDescripId." and gsm=".$$gsm." and color=".$colorId." $dia_cond and status_active=1 and is_deleted=0");
				if (count($row_prod) > 0 || $new_prod_ref_arr[$cbo_company_id_to."**".$$febDescripId."**".$$gsm."**".$$diaWidth."**2"] != "")
				{
					if(count($row_prod) > 0)
					{
	       				$new_prod_id = $row_prod[0][csf('id')];
	       				$product_id_update_parameter[$new_prod_id]['qnty']+= str_replace("'", "", $$rollWgt);
	       				//$product_id_update_parameter[$new_prod_id]['amount']+=$$rollAmount;
	       				$update_to_prod_id[$new_prod_id]=$new_prod_id;
					}
					else
					{
						$new_prod_id = $new_prod_ref_arr[$cbo_company_id_to."**".$$febDescripId."**".$$gsm."**".$$diaWidth."**2"];
						$product_id_insert_parameter[$new_prod_id."**".$$febDescripId."**".$$gsm."**".$$diaWidth."**".$$constructCompo."**2"]+=str_replace("'", "", $$rollWgt);
						//$product_id_insert_amount[$new_prod_id."**".$$febDescripId."**".$$gsm."**".$$diaWidth."**".$$constructCompo."**2"]+=$$rollAmount;
					}
	           	}
	           	else
	           	{
	           		$new_prod_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
	           		$new_prod_ref_arr[$cbo_company_id_to."**".$$febDescripId."**".$$gsm."**".$$diaWidth."**2"] = $new_prod_id;
	           		$product_id_insert_parameter[$new_prod_id."**".$$febDescripId."**".$$gsm."**".$$diaWidth."**".$$constructCompo."**2"]+=str_replace("'", "", $$rollWgt);
	           		//$product_id_insert_amount[$new_prod_id."**".$$febDescripId."**".$$gsm."**".$$diaWidth."**".$$constructCompo."**2"]+=$$rollAmount;
	           	}
	        }
	        else
	        {
	        	//if company same then product will be same
	        	$new_prod_id = str_replace("'", "", $$productId);
	        }


           	//-----------------------------------------------------------------------------------
			  
			
			if($data_array_trans!="") $data_array_trans.=",";
			$data_array_trans.="(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",".$$productId.",2,6,".$txt_transfer_date.",".$txt_from_order_book_id.",12,".$$rollWgt.",".$rate.",".$amount.",".$$floor.",".$$room.",".$$rack.",".$$shelf.",".$$storeId.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$cbo_from_body_part."',".$txt_from_batch_id.",".$txt_from_batch_id.")";
			
			$from_trans_id=$id_trans;

			$recv_trans_id=0;
			if($variable_auto_rcv!=2) // if auto receive yes, then no need to acknowledgement
			{
				$recv_trans_id = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$data_array_trans.=",(".$recv_trans_id.",".$transfer_update_id.",".$cbo_company_id.",".$new_prod_id.",2,5,".$txt_transfer_date.",".$txt_to_order_book_id.",12,".$$rollWgt.",".$rate.",".$amount.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_store_name_to.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$cbo_to_body_part."',".$batch_id_to.",".$batch_id_to.")";
			}
			// $field_array_dtls="id, mst_id, trans_id, to_trans_id, from_prod_id, to_prod_id, item_category, transfer_qnty, roll, rate, transfer_value, uom, color_id, floor_id, room, rack, shelf, to_floor_id, to_room, to_rack, to_shelf, from_store, to_store, transfer_requ_dtls_id, active_dtls_id_in_transfer, inserted_by, insert_date, body_part_id, to_body_part,batch_id,to_batch_id";
			if($data_array_dtls!="") $data_array_dtls.=",";
			$data_array_dtls.="(".$id_dtls.",".$transfer_update_id.",".$from_trans_id.",".$recv_trans_id.",".$$productId.",".$new_prod_id.",2,".$$rollWgt.",".$$rollNo.",".$rate.",".$amount.",12,".$colorId.",".$$floor.",".$$room.",".$$rack.",".$$shelf.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$$storeId.",".$cbo_store_name_to.",".$$requiDtlsId.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$cbo_from_body_part."','".$cbo_to_body_part."','".$txt_from_batch_id."','".$batch_id_to."',".$$febDescripId.")";	

			if($cbo_transfer_criteria==7){
				$booking_without_order = 0;
			}else{
				$booking_without_order = 1;
			}

			if($variable_auto_rcv!=2) // if Auto recv Yes 1
			{
				$re_transfer=0;
			}
			else{
				$re_transfer=1;
			}

			// $field_array_roll="id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, roll_no, roll_id, from_roll_id, is_transfer, transfer_criteria, booking_without_order, booking_no, re_transfer, inserted_by, insert_date";
			
			if($data_array_roll!="") $data_array_roll.= ",";
			$data_array_roll.="(".$id_roll.",".$$barcodeNo.",".$transfer_update_id.",".$id_dtls.",".$txt_to_order_book_id.",".$entry_form_no.",".$$rollWgt.",".$$rollNo.",".$$rollId.",".$$transRollId.",5,".$cbo_transfer_criteria.",".$booking_without_order.",".$txt_to_order_book_no.",".$re_transfer.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			// $field_array_batch_dtls="id, mst_id, po_id, prod_id, item_description, roll_no, roll_id, barcode_no, batch_qnty, dtls_id,width_dia_type, inserted_by, insert_date";
			$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
			if($data_array_batch_dtls!="") $data_array_batch_dtls.=",";
			$data_array_batch_dtls.="(".$id_dtls_batch.",".$batch_id_to.",".$txt_to_order_book_id.",".$new_prod_id.",0,0,0,0,".$txt_transfer_qnty.",".$id_dtls.",0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

		

			
			// $data_array_batch="(".$batch_id_to.",'".$batch_no."'".$entry_form_no.",".$txt_transfer_date.",".$cbo_company_id_to.",".$booking_id.",'".$booking_no."',0,".$colorId.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			// $field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, inserted_by, insert_date";

			if ($cbo_transfer_criteria==6) // Order to Sample
			{
				if($data_array_prop!="") $data_array_prop.= ",";
				$data_array_prop.="(".$id_prop.",".$from_trans_id.",6,".$entry_form_no.",".$id_dtls.",".$txt_from_order_book_id.",".$$productId.",".$$rollWgt.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}

			if ($cbo_transfer_criteria==7) // Sample to Order
			{
				if($variable_auto_rcv!=2)
				{
					if($data_array_prop!="") $data_array_prop.= ",";
					$data_array_prop.="(".$id_prop.",".$recv_trans_id.",5,".$entry_form_no.",".$id_dtls.",".$txt_to_order_book_id.",".$new_prod_id.",".$$rollWgt.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				}
			}

			$prodData_array[str_replace("'", "",$$productId)]+=str_replace("'", "", $$rollWgt);
			$all_prod_id.=$$productId.",";

			$all_trans_roll_id.=$$transRollId.",";
			$inserted_roll_id_arr[$id_roll] =  $id_roll;
			$barcode_id[str_replace("'", "", $$barcodeNo)] = str_replace("'", "", $$barcodeNo);

			
		}

		/*echo "10**";
		print_r($prodData_array);die;*/

		if(!empty($product_id_insert_parameter))
		{
			foreach ($product_id_insert_parameter as $key => $val)
			{
				$prod_description_arr = explode("**", $key);
				$prod_id = $prod_description_arr[0];
				$fabric_desc_id = $prod_description_arr[1];
				$txt_gsm = str_replace("'", "", $prod_description_arr[2]);
				$txt_width = str_replace("'", "", $prod_description_arr[3]);
				$cons_compo = str_replace("'", "", $prod_description_arr[4]);
				$prod_name_dtls = trim($cons_compo) . ", " . trim($txt_gsm) . ", " . trim($txt_width);
				
				$roll_amount = 0;
				$avg_rate_per_unit = 0;
				
				if($variable_auto_rcv!=2)
				{
					$product_quantity =$val;
				}
				else
				{
					$product_quantity =0;
				}
				// if Qty is zero then rate & value will be zero
				if ($val<=0) 
				{
					$roll_amount=0;
					$avg_rate_per_unit=0;
				}
				
				if($data_array_prod_insert!="") $data_array_prod_insert.=",";
               	$data_array_prod_insert .= "(" . $prod_id . "," . $cbo_company_id_to . "," . $cbo_store_name_to . ",2," . $fabric_desc_id . ",'" . $cons_compo . "','" . $prod_name_dtls . "'," . "12" . "," . $avg_rate_per_unit . "," . $product_quantity . "," . $product_quantity . "," . $roll_amount . "," . $txt_gsm . ",'" . $txt_width . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
			}
		}

		if(!empty($update_to_prod_id))
		{

			$prod_id_array=array();
			$up_to_prod_ids=implode(",",array_unique($update_to_prod_id));
			// echo "10**select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id in($up_to_prod_ids) ";die;
			$toProdIssueResult=sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id in($up_to_prod_ids) ");
			foreach($toProdIssueResult as $row)
			{
				
				//$stock_value = $product_id_update_parameter[$row[csf("id")]]['amount'] + $row[csf("stock_value")];
				//$avg_rate_per_unit = $stock_value/$stock_qnty;

				if($variable_auto_rcv!=2)
				{
					$stock_qnty = $product_id_update_parameter[$row[csf("id")]]['qnty'] + $row[csf("current_stock")];
				}
				else
				{
					$stock_qnty =  $row[csf("current_stock")];
				}

				$stock_value = $row[csf("avg_rate_per_unit")]*$stock_qnty;
				// if Qty is zero then rate & value will be zero
				if ($stock_qnty<=0) 
				{
					$stock_value=0;
					$row[csf("avg_rate_per_unit")]=0;
				}
				$prod_id_array[]=$row[csf('id')];
				$data_array_prod_update[$row[csf('id')]]=explode("*",("'".$stock_qnty."'*'".$row[csf("avg_rate_per_unit")]."'*'".$stock_value."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
			unset($toProdIssueResult);
		}


		//$all_prod_id_arr=implode(",",array_unique(explode(",",chop($all_prod_id,','))));
		
		
		$all_prod_id=chop(str_replace("'", "", $all_prod_id),',');
		$all_prod_id_array =  array_unique(explode(",", $all_prod_id));
		$all_prod_id_cond=""; $all_prod_idCond="";
		if($db_type==2 && count($all_prod_id_array)>999)
		{
			$all_prod_id_array_chunk=array_chunk($all_prod_id_array,999) ;
			foreach($all_prod_id_array_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$all_prod_idCond.=" id in($chunk_arr_value) or ";
			}
			$all_prod_id_cond.=" and (".chop($all_prod_idCond,'or ').")";
		}
		else
		{
			$all_prod_id_cond=" and id in($all_prod_id)";
		}

		$fromProdIssueResult=sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where company_id=$cbo_company_id $all_prod_id_cond");
		foreach($fromProdIssueResult as $row)
		{
			$issue_qty=$prodData_array[$row[csf('id')]];
			$current_stock=$row[csf('current_stock')]-$issue_qty;
			$current_avg_rate=$row[csf('avg_rate_per_unit')];
			$current_amount=$row[csf('avg_rate_per_unit')]*$current_stock;
			// if Qty is zero then rate & value will be zero
			if ($current_stock<=0) 
			{
				$current_amount=0;
				$current_avg_rate=0;
			}
			$prod_id_array[]=$row[csf('id')];
			$data_array_prod_update[$row[csf('id')]]=explode("*",("'".$current_stock."'*'".$current_avg_rate."'*'".$current_amount."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		}



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
		//echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;
		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		}
		// echo "10**insert into inv_item_transfer_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		
		$rID3=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} 
		//echo "10**insert into inv_item_transfer_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		// 1 && 3 = 
		$rollIds=chop($rollIds,',');		
		$rID4=sql_multirow_update("pro_roll_details","is_transfer*transfer_criteria*re_transfer","6*$cbo_transfer_criteria*1","id",$rollIds,0);
		if($flag==1) 
		{
			if($rID4) $flag=1; else $flag=0; 
		}
		//echo "10**insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;die;
		$rID5=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,1);
		if($flag==1) 
		{
			if($rID5) $flag=1; else $flag=0; 
		}
		

		// 6 = Order To Sample && 7 = Sample to Order
		if ($cbo_transfer_criteria==6) 
		{
			$rID6=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1);
			if($flag==1) 
			{
				if($rID6) $flag=1; else $flag=0; 
			}
		}
		if($cbo_transfer_criteria==7) // Sample to Order
		{
			if($variable_auto_rcv!=2)
			{
				$rID6=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1);
				if($flag==1) 
				{
					if($rID6) $flag=1; else $flag=0; 
				}
			}			
		}

		if(!empty($data_array_prod_update))
		{
			$prodUpdate=execute_query(bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array ));
			//echo "10**".bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array );die;
			if($flag==1) 
			{
				if($prodUpdate) $flag=1; else $flag=0; 
			}
		}

		if ($data_array_prod_insert != "")
		{
			$rID7=sql_insert("product_details_master",$field_array_prod_insert,$data_array_prod_insert,0);

			if($flag==1) 
			{
				if($rID7) $flag=1; else $flag=0; 
			}
		}


		$rID8=execute_query("update pro_roll_details set is_returned=1 where barcode_no in (". implode(',', $barcode_id).") and id not in (".implode(',', $inserted_roll_id_arr).")");
		if ($flag == 1)
		{
			if ($rID8)
				$flag = 1;
			else
				$flag = 0;
		}

		if(str_replace("'", "", $txt_requisition_id) !="")
		{
			$rID9=execute_query("update inv_item_transfer_requ_mst set requisition_status=2 where id =$txt_requisition_id");
			if ($rID9)
				$flag = 1;
			else
				$flag = 0;
		}

		// if($flag==1)
		// {
		// 	if($rID9) $flag=1; else $flag=0;
		// }

		$rID10=$rID11=true;

		
		if($data_array_batch_dtls!="")
		{
			//echo "10**insert into pro_batch_create_dtls (".$field_array_batch_dtls.") values ".$data_array_batch_dtls;oci_rollback($con);die;
			$rID10=sql_insert("pro_batch_create_dtls",$field_array_batch_dtls,$data_array_batch_dtls,0);
			if($flag==1)
			{
				if($rID10) $flag=1; else $flag=0;
			}
		}
		
		if($batchData)
		{
			//echo "10**";echo $data_array_batch_update."==".$batch_id_to;die;
			$rID11=sql_update("pro_batch_create_mst",$field_array_batch_update,$data_array_batch_update,"id",$batch_id_to,0);
			if($flag==1)
			{
				if($rID11) $flag=1; else $flag=0;
			}
		}
		else
		{
			//echo "10**insert into pro_batch_create_mst (".$field_array_batch.") values ".$data_array_batch;oci_rollback($con);die;
			$rID11=sql_insert("pro_batch_create_mst",$field_array_batch,$data_array_batch,0);
			if($flag==1)
			{
				if($rID11) $flag=1; else $flag=0;
			}
		}
		
		

		/*echo "10**"."insert into product_details_master ($field_array_prod_insert) values $data_array_prod_insert";
		oci_rollback($con);
		die;*/
		//echo "10**".bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array );die;
		//echo "10**$rID##$rID2##$rID3##$rID4##$rID5##$rID6##$rID7##$rID8##$rID10##$rID11##$prodUpdate";oci_rollback($con);die;

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
		//check_table_status( $_SESSION['menu_id'], 0 );
		disconnect($con);
		die;
	}	
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }


		for($j=1;$j<=$total_row;$j++)
		{
			$barcodeNo="barcodeNo_".$j;
			$all_barcodeNo.=$$barcodeNo.",";
			$dtlsId="dtlsId_".$j;
			$all_dtlsId.=$$dtlsId.",";
			$rolltableId="rolltableId_".$j;
			$all_rolltableId.=$$rolltableId.",";


			$rollMstId="transRollId_".$j;

			if ($$rolltableId!="") 
			{
				$saved_roll_arr[$$barcodeNo]=$$rolltableId;
			}
			else
			{
				$new_roll_arr[$$barcodeNo]=$$rollMstId;
			}
		}

		

		$all_barcodeNo=chop($all_barcodeNo,',');
		$all_barcodeNo_arr=explode(",", $all_barcodeNo);

		$all_rolltableId=chop($all_rolltableId,',');
		$all_roll_id_arr=explode(",", $all_rolltableId);

		// echo "SELECT a.id,a.batch_no,a.color_id, a.batch_weight, a.booking_no from pro_batch_create_mst a where   a.status_active=1 and a.is_deleted=0 and a.entry_form in(14,68,134,214) and a.company_id=$cbo_company_id_to and a.batch_no=$txt_from_batch_no  group by a.id,a.batch_no,a.color_id,a.batch_weight, a.booking_no";die;
		$batchData=sql_select("SELECT a.id,a.batch_no,a.color_id, a.batch_weight, a.booking_no from pro_batch_create_mst a where   a.status_active=1 and a.is_deleted=0 and a.entry_form in(14,68,134,214) and a.company_id=$cbo_company_id_to and a.batch_no=$txt_from_batch_no  group by a.id,a.batch_no,a.color_id,a.batch_weight, a.booking_no");

		$batch_data_arr=array();
		foreach ($batchData as $rows)
		{
			$batch_data_arr[$rows[csf("batch_no")]][$rows[csf("color_id")]][$rows[csf("booking_no")]]['id']=$rows[csf("id")];
			$batch_data_arr[$rows[csf("batch_no")]][$rows[csf("color_id")]][$rows[csf("booking_no")]]['batch_weight']=$rows[csf("batch_weight")];
		}

		if($all_barcodeNo!="")
		{
			$all_barcodeNo_arr = array_filter($all_barcodeNo_arr);
			if(count($all_barcodeNo_arr)>0)
			{
				$barcod_NOs = implode(",", $all_barcodeNo_arr);
				$all_barcode_no_cond=""; $barCond="";
				if($db_type==2 && count($all_barcodeNo_arr)>999)
				{
					$all_barcodeNo_arr_chunk=array_chunk($all_barcodeNo_arr,999) ;
					foreach($all_barcodeNo_arr_chunk as $chunk_arr)
					{
						$chunk_arr_value=implode(",",$chunk_arr);
						$barCond.=" a.barcode_no in($chunk_arr_value) or ";
					}

					$all_barcode_no_cond.=" and (".chop($barCond,'or ').")";
				}
				else
				{
					$all_barcode_no_cond=" and a.barcode_no in($barcod_NOs)";
				}
			}

			

			$issue_data_refer = sql_select("select a.id, a.barcode_no, b.issue_number from pro_roll_details a, inv_issue_master b where a.mst_id = b.id and a.entry_form = 71 $all_barcode_no_cond  and a.status_active = 1 and a.is_deleted = 0 and a.is_returned=0"); //and a.barcode_no in ($all_barcodeNo)
			if($issue_data_refer[0][csf("barcode_no")] != "")
			{
				echo "20**Sorry Barcode No : ". $issue_data_refer[0][csf("barcode_no")] ."\nFound in Issue No ".$issue_data_refer[0][csf("issue_number")];
				disconnect($con);
				die;
			}
			// echo "select max(a.id) as max_id,  a.barcode_no from pro_roll_details a
			// where a.status_active =1 and a.is_deleted=0 $all_barcode_no_cond group by  a.barcode_no";die;
			$next_transfer_arr = array();
			$next_transfer_sql = sql_select("select max(a.id) as max_id,  a.barcode_no from pro_roll_details a
			where a.status_active =1 and a.is_deleted=0 $all_barcode_no_cond group by  a.barcode_no");
			foreach ($next_transfer_sql as $next_trans)
			{
				$next_transfer_arr[$next_trans[csf('barcode_no')]]=$next_trans[csf('max_id')];
			}

		

			$current_transfer_sql = sql_select("select a.barcode_no, b.transfer_system_id as system_id from pro_roll_details a, inv_item_transfer_mst b where a.mst_id=b.id and a.entry_form in (134,214,216,219) $all_barcode_no_cond and a.status_active=1 and a.is_deleted=0 and a.re_transfer=0
			union all 
			select a.barcode_no, b.recv_number as system_id from pro_roll_details a, inv_receive_master b where a.mst_id=b.id and a.entry_form in (126) $all_barcode_no_cond and a.status_active=1 and a.is_deleted=0 and a.re_transfer=0");

			foreach ($current_transfer_sql as $current_trans)
			{
				$next_transfer_ref[$current_trans[csf('barcode_no')]]["transfer_no"]=$current_trans[csf('system_id')];
			}
				
			if (!empty($saved_roll_arr)) // Saved barcode to next transaction found
			{				
				foreach ($saved_roll_arr as $barcode => $saved_roll_id) 
				{					
					$barcode= str_replace("'", "", $barcode);
					$saved_roll_id= str_replace("'", "", $saved_roll_id);
					if ($saved_roll_id != $next_transfer_arr[$barcode]) 
					{
						echo "20**Sorry Barcode No : ". $barcode ." \nFound in Transfer/Return No : ".$next_transfer_ref[$barcode]["transfer_no"];
						disconnect($con);
						die;
					}
				}
			}
			if (!empty($new_roll_arr)) // new barcode show in current transfer but this barcode saved to another tab 
			{
				foreach ($new_roll_arr as $barcode => $new_roll_id) 
				{
					$barcode= str_replace("'", "", $barcode);
					$new_roll_id= str_replace("'", "", $new_roll_id);
					if ($new_roll_id != $next_transfer_arr[$barcode]) 
					{
						echo "20**Sorry Barcode No : ". $barcode ." \nFound in Transfer/Return No : ".$next_transfer_ref[$barcode]["transfer_no"];
						disconnect($con);
						die;
					}
				}
			}

			$split_roll_sql=sql_select("select a.barcode_no, a.split_from_id from pro_roll_split a where status_active =1 $all_barcode_no_cond");

			foreach($split_roll_sql as $bar)
			{
				$split_roll_ref[$bar[csf('barcode_no')]][$bar[csf('split_from_id')]]=$bar[csf('barcode_no')];
			}

			$child_split_sql=sql_select("select a.barcode_no, a.id from pro_roll_details a where roll_split_from >0 $all_barcode_no_cond and a.entry_form = 134 order by a.barcode_no");
			foreach($child_split_sql as $bar)
			{
				$child_splited_arr[$bar[csf('barcode_no')]][$bar[csf('id')]]=$bar[csf('barcode_no')];
			}

			$all_dtlsId=chop($all_dtlsId,',');
			$all_dtlsId_arr=explode(",", $all_dtlsId);

			if($all_dtlsId !="")
			{
				$all_dtlsId_arr = array_filter($all_dtlsId_arr);
				if(count($all_dtlsId_arr)>0)
				{
					$all_dtlsIds = implode(",", $all_dtlsId_arr);
					$all_dtls_id_cond=""; $idCond="";
					if($db_type==2 && count($all_dtlsId_arr)>999)
					{
						$all_dtlsId_arr_chunk=array_chunk($all_dtlsId_arr,999) ;
						foreach($all_dtlsId_arr_chunk as $chunk_arr)
						{
							$chunk_arr_value=implode(",",$chunk_arr);
							$idCond.=" a.id in($chunk_arr_value) or ";
						}

						$all_dtls_id_cond.=" and (".chop($idCond,'or ').")";
					}
					else
					{
						$all_dtls_id_cond=" and a.id in($all_dtlsIds)";
					}
				}

				$deleted_dtls=sql_select("select b.barcode_no, a.id from inv_item_transfer_dtls a, pro_roll_details b where a.id=b.dtls_id and b.entry_form=134 $all_dtls_id_cond and a.status_active=0 and a.is_deleted=1");

				foreach($deleted_dtls as $row)
				{
					if($row[csf('id')])
					{
						echo "20**Barcode ".$row[csf('barcode_no')]." is already deleted by another user. Please reload the System ID again.";
						disconnect($con);
						die;
					}
				}
			}

			for($inc=1; $inc <= count($all_dtlsId_arr); $inc++)
			{
				$rollDtlsId=trim($all_roll_id_arr[$inc-1],"'");
				$BarcodeNO=trim($all_barcodeNo_arr[$inc-1],"'");
				//echo $rollDtlsId.'='.$BarcodeNO;
				if($split_roll_ref[$BarcodeNO][$rollDtlsId] !="" || $child_splited_arr[$BarcodeNO][$rollDtlsId] != "")
				{
					echo "20**"."Split Found. barcode no: ".$BarcodeNO;
					disconnect($con);
					die;
				}
			}
		}

		//===========================================================================================================================

		//echo "10**".$entry_form_no.'Update';die;
		$field_array_update="challan_no*transfer_date*from_order_id*to_order_id*updated_by*update_date";
		$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$txt_from_order_book_id."*".$txt_to_order_book_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rate=0; $amount=0;
		//$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
	

		$field_array_trans="id, mst_id, company_id, prod_id, item_category, transaction_type, transaction_date, order_id, cons_uom, cons_quantity, cons_rate, cons_amount, floor_id, room, rack, self, store_id, inserted_by, insert_date, body_part_id,batch_id,pi_wo_batch_no";

		$field_array_trans_update="prod_id*transaction_date*order_id*cons_quantity*cons_rate*cons_amount*floor_id*room*rack*self*body_part_id*batch_id*pi_wo_batch_no*updated_by*update_date";

		// $field_array_trans_update="prod_id*transaction_date*order_id*cons_quantity*cons_rate*cons_amount*floor_id*room*rack*self*updated_by*update_date*body_part_id*batch_id*pi_wo_batch_no";
		
		$field_array_dtls="id, mst_id, trans_id, to_trans_id, from_prod_id, to_prod_id, item_category, transfer_qnty, roll, rate, transfer_value, uom, color_id, floor_id, room, rack, shelf, to_floor_id, to_room, to_rack, to_shelf, from_store, to_store, transfer_requ_dtls_id, active_dtls_id_in_transfer, inserted_by, insert_date, body_part_id, to_body_part,batch_id,to_batch_id, feb_description_id";

		$field_array_dtls_update="from_prod_id*to_prod_id*transfer_qnty*roll*rate*transfer_value*color_id*floor_id*room*rack*shelf*to_floor_id*to_room*to_rack*to_shelf*feb_description_id*updated_by*update_date*body_part_id*to_body_part*batch_id*to_batch_id";
		
		//$id_roll = return_next_id( "id", "pro_roll_details", 1 );

		$field_array_roll="id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, roll_no, roll_id, from_roll_id, is_transfer, transfer_criteria, booking_without_order, booking_no, inserted_by, insert_date";
		$field_array_updateroll="qnty*roll_no*updated_by*update_date";

		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, inserted_by, insert_date";

		$field_array_prod_insert = "id, company_id, store_id, item_category_id, detarmination_id, item_description, product_name_details, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, gsm, dia_width, inserted_by, insert_date";
		$field_array_prod_update = "current_stock*avg_rate_per_unit*stock_value*updated_by*update_date";
		$field_array_batch_dtls="id, mst_id, po_id, prod_id, item_description, roll_no, roll_id, barcode_no, batch_qnty, dtls_id,width_dia_type, inserted_by, insert_date";
		
		$rollIds=''; $update_dtls_id='';
		for($j=1;$j<=$total_row;$j++)
		{
			$barcodeNo="barcodeNo_".$j;
			$rollNo="rollNo_".$j;
			$productId="productId_".$j;
			$rollId="rollId_".$j;
			$rollWgt="rollWgt_".$j;
			$colorId="colorId_".$j;
			$floor="floor_".$j;
			$room="room_".$j;
			$rack="rack_".$j;
			$shelf="shelf_".$j;
			$rollNo="rollNo_".$j;
			$dtlsId="dtlsId_".$j;
			$transIdFrom="transIdFrom_".$j;
			$transIdTo="transIdTo_".$j;
			$rolltableId="rolltableId_".$j;
			$transRollId="transRollId_".$j;
			$storeId="storeId_".$j;
			$requiDtlsId="requiDtlsId_".$j;
			$fromProductUp="fromProductUp_".$j;
			$hiddenTransferqnty="hiddenTransferqnty_".$j;
			$febDescripId="febDescripId_".$j;
			$constructCompo="constructCompo_".$j;
			$gsm="gsm_".$j;
			$diaWidth="diaWidth_".$j;
			$rollIds.=$$transRollId.",";
			
			//echo "10**febDescripId:".$$febDescripId;die;

			

			if(str_replace("'","",$$rolltableId)>0)
			{
				$update_dtls_id.=str_replace("'","",$$dtlsId).",";
				
				$transId_arr[]=str_replace("'","",$$transIdFrom);

				$batch_no          = str_replace("'", "", $txt_from_batch_no);
				$colorId           = str_replace("'", "", $$colorId);
				$txt_transfer_qnty = str_replace("'", "", $$rollWgt);
				$booking_no        = str_replace("'", "", $hidden_book_no);
				$booking_id        = str_replace("'", "", $txt_to_order_book_id);
				
				//echo "10**".$batch_no;die;
				$batchData = $batch_data_arr[$batch_no][$colorId][$booking_no]['id'];
				
				
				$field_array_batch_update="batch_weight*updated_by*update_date";

				if($batchData)
				{
					$batch_id_to=$batchData;
					//$batch_id_to=$batchData[0][csf('id')];
					//echo "10**batchData:".str_replace("'","",$txt_to_batch_id);die;
					
					if($batch_id_to==str_replace("'","",$txt_to_batch_id))
					{
						
						$curr_batch_weight=$batch_data_arr[$batch_no][$colorId][$booking_no]['batch_weight']+str_replace("'", '',$txt_transfer_qnty)-str_replace("'", '',$$hiddenTransferqnty);
						
						
						$update_batch_id[]=str_replace("'","",$txt_to_batch_id);
						$data_array_batch_update[str_replace("'","",$txt_to_batch_id)]=explode("*",("'".$curr_batch_weight."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					}
					else
					{
						//previous batch adjusted
						$txt_to_batch_id = str_replace("'","",$txt_to_batch_id);
						$batch_weight= return_field_value("batch_weight","pro_batch_create_mst","id=$txt_to_batch_id");
						$adjust_batch_weight=$batch_weight-str_replace("'", '',$$hiddenTransferqnty);
						$data_array_batch_update[$txt_to_batch_id]=explode("*",("'".$adjust_batch_weight."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
	
						//new batch adjusted $tobookingNo
						$curr_batch_weight=$batch_data_arr[$batch_no][$colorId][$booking_no]['batch_weight']+str_replace("'", '',$txt_transfer_qnty);
						$update_batch_id[]=$batchData;
						$data_array_batch_update[]=explode("*",("'".$curr_batch_weight."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
						
					}
				}
				else
				{

					if($cbo_transfer_criteria==7)
					{
						$booking_without_order = 0;
					}else{
						$booking_without_order = 1;
					}
		

					if( $new_created_batch[$batch_no][$colorId][$booking_no]['id'] == "" )
					{
						$batch_id_to = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);
						$field_array_batch="id,batch_no,entry_form,batch_date,company_id,booking_no_id,booking_no,booking_without_order,color_id,batch_weight,inserted_by,insert_date";
						
					}

					$data_array_batch="(".$batch_id_to.",'".$batch_no."'".$entry_form_no.",".$txt_transfer_date.",".$cbo_company_id_to.",".$booking_id.",'".$booking_no."',".$booking_without_order.",".$colorId.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

					$new_created_batch[$batch_no][$colorId][$booking_no]['id'] =$batch_id_to;
	
					//previous batch adjusted
					$txt_to_batch_id = str_replace("'","",$txt_to_batch_id);
					$batch_weight= return_field_value("batch_weight","pro_batch_create_mst","id=$txt_to_batch_id");
					$adjust_batch_weight=$batch_weight-str_replace("'", '',$$hiddenTransferqnty);
					$update_batch_id[]=$txt_to_batch_id;
					$data_array_batch_update[$txt_to_batch_id]=explode("*",("'".$adjust_batch_weight."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					
				} 
			
				$data_array_update_trans[str_replace("'","",$$transIdFrom)]=explode("*",($$fromProductUp."*".$txt_transfer_date."*".$txt_from_order_book_id."*".$$rollWgt."*".$rate."*".$amount."*".$$floor."*".$$room."*".$$rack."*".$$shelf."*".$cbo_from_body_part."*".$txt_from_batch_id."*".$txt_from_batch_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				
				if($variable_auto_rcv != 2 )
				{
					$transId_arr[]=str_replace("'","",$$transIdTo);
					$data_array_update_trans[str_replace("'","",$$transIdTo)]=explode("*",($$productId."*".$txt_transfer_date."*".$txt_to_order_book_id."*".$$rollWgt."*".$rate."*".$amount."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$cbo_to_body_part."*".$batch_id_to."*".$batch_id_to."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				}

				$dtlsId_arr[]=str_replace("'","",$$dtlsId);
				$data_array_update_dtls[str_replace("'","",$$dtlsId)]=explode("*",($$fromProductUp."*".$$productId."*".$$rollWgt."*".$$rollNo."*".$rate."*".$amount."*".$colorId."*".$$floor."*".$$room."*".$$rack."*".$$shelf."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$$febDescripId."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'".$cbo_from_body_part."'*'".$cbo_to_body_part."'*'".$txt_from_batch_id."'*'".$batch_id_to."'"));
				
				$rollId_arr[]=str_replace("'","",$$rolltableId);
				$data_array_update_roll[str_replace("'","",$$rolltableId)]=explode("*",($$rollWgt."*".$$rollNo."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				
				$dtlsIdProp=str_replace("'","",$$dtlsId);
				$transIdfromProp=str_replace("'","",$$transIdFrom);
				$transIdtoProp=str_replace("'","",$$transIdTo);

				$new_prod_id = $$productId;

				// 	$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
				// if($data_array_batch_dtls!="") $data_array_batch_dtls.=",";
				// $data_array_batch_dtls.="(".$id_dtls_batch.",".$batch_id_to.",".$txt_to_order_book_id.",".$new_prod_id.",0,0,0,0,".$txt_transfer_qnty.",".$id_dtls.",0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

				$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
				if($data_array_batch_dtls!="") $data_array_batch_dtls.=",";
				$data_array_batch_dtls.="(".$id_dtls_batch.",".$batch_id_to.",".$txt_to_order_book_id.",".$new_prod_id.",0,0,0,0,".$txt_transfer_qnty.",".$$dtlsId.",0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
			else // New Insert
			{
				$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
				$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);	

				if($cbo_company_id != $cbo_company_id_to)
				{
					if (str_replace("'", "", $$diaWidth)=="")
					{
						if($db_type == 0){
							$dia_cond = " and dia_width = '' ";
						}else{
							$dia_cond = " and dia_width is null ";
						}
					}
					else
					{
						$dia_cond = " and dia_width = '".str_replace("'", "", $$diaWidth)."'";
					}

					$row_prod = sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where company_id=$cbo_company_id_to and item_category_id=2 and detarmination_id='".$$febDescripId."' and gsm='".$$gsm."' and color=".$colorId." $dia_cond and status_active=1 and is_deleted=0");
					if (count($row_prod) > 0 || $new_prod_ref_arr[$cbo_company_id_to."**".$$febDescripId."**".$$gsm."**".$$diaWidth."**2"] != "")
					{
						if(count($row_prod) > 0)
						{
	           				$new_prod_id = $row_prod[0][csf('id')];
	           				$product_id_update_parameter[$new_prod_id]['qnty']+=str_replace("'", "'", $$rollWgt); 
	           				//$product_id_update_parameter[$new_prod_id]['amount']+=$$rollAmount;
	           				$update_to_prod_id[$new_prod_id]=$new_prod_id;
						}
						else
						{
							$new_prod_id = $new_prod_ref_arr[$cbo_company_id_to."**".$$febDescripId."**".$$gsm."**".$$diaWidth."**2"];
							$product_id_insert_parameter[$new_prod_id."**".$$febDescripId."**".$$gsm."**".$$diaWidth."**".$$constructCompo."**2"]+=str_replace("'", "'", $$rollWgt);
							//$product_id_insert_amount[$new_prod_id."**".$$febDescripId."**".$$gsm."**".$$diaWidth."**".$$constructCompo."**2"]+=$$rollAmount;
						}
	               	}
	               	else
	               	{
	               		$new_prod_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
	               		$new_prod_ref_arr[$cbo_company_id_to."**".$$febDescripId."**".$$gsm."**".$$diaWidth."**2"] = $new_prod_id;
	               		$product_id_insert_parameter[$new_prod_id."**".$$febDescripId."**".$$gsm."**".$$diaWidth."**".$$constructCompo."**2"]+=str_replace("'", "'", $$rollWgt);
	               		//$product_id_insert_amount[$new_prod_id."**".$$febDescripId."**".$$gsm."**".$$diaWidth."**".$$constructCompo."**2"]+=$$rollAmount;
	               	}

	               	$prodData_array[$$productId]+=str_replace("'", "'", $$rollWgt); 
					//$prodData_array_amount[$$productId]+=$$rollAmount;
					$all_prod_id.=$$productId.",";
	            }
	            else
	            {
	            	$new_prod_id = $$productId;
	            }

				$batch_no          = str_replace("'", "", $txt_from_batch_no);
				$colorId           = str_replace("'", "", $$colorId);
				$txt_transfer_qnty = str_replace("'", "", $$rollWgt);
				$booking_no        = str_replace("'", "", $hidden_book_no);
				$booking_id        = str_replace("'", "", $txt_to_order_book_id);
			
				//echo "10**This batch_no =". $batch_no;die;
				$batchData = $batch_data_arr[$batch_no][$colorId][$booking_no]['id'];

				//echo "10**This batchData =". $batchData;die;
				
				if($batchData)
				{
					$batch_id_to=$batchData;
					//$curr_batch_weight=$batchData[0][csf('batch_weight')]+str_replace("'", '',$txt_transfer_qnty);
					$curr_batch_weight=$batch_data_arr[$batch_no][$colorId][$booking_no]['batch_weight']+$txt_transfer_qnty;
					$field_array_batch_update="batch_weight*updated_by*update_date";
					$data_array_batch_update=$curr_batch_weight."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				}
				else
				{
					
					if($new_created_batch[$batch_no][$colorId][$booking_no]['id'] == ""){

						$batch_id_to = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);
						$field_array_batch="id,batch_no,entry_form,batch_date,company_id,booking_no_id,booking_no,booking_without_order,color_id,batch_weight,inserted_by,insert_date";
					}
					//echo "10**This batchData =". $batch_id_to;die;
					if($data_array_batch!="") $data_array_batch.=",";
					$data_array_batch="(".$batch_id_to.",'".$batch_no."',".$entry_form_no.",".$txt_transfer_date.",".$cbo_company_id_to.",".$booking_id.",'".$booking_no."',0,".$colorId.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";


					$new_created_batch[$batch_no][$colorId][$booking_no]['id'] =$batch_id_to;
				}

				$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
				if($data_array_batch_dtls!="") $data_array_batch_dtls.=",";
				$data_array_batch_dtls.="(".$id_dtls_batch.",".$batch_id_to.",".$booking_id.",".$new_prod_id.",0,0,0,0,".$txt_transfer_qnty.",".$id_dtls.",0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				// $field_array_trans="id, mst_id, company_id, prod_id, item_category, transaction_type, transaction_date, order_id, cons_uom, cons_quantity, cons_rate, cons_amount, floor_id, room, rack, self, store_id, inserted_by, insert_date, body_part_id,batch_id,pi_wo_batch_no";

				if($data_array_trans!="") $data_array_trans.=",";
				$data_array_trans.="(".$id_trans.",".$update_id.",".$cbo_company_id.",".$$fromProductUp.",2,6,".$txt_transfer_date.",".$txt_from_order_book_id.",12,".$$rollWgt.",".$rate.",".$amount.",".$$floor.",".$$room.",".$$rack.",".$$shelf.",".$$storeId.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$cbo_from_body_part."','".$txt_from_batch_id."','".$txt_from_batch_id."')";
				
				$transIdfromProp=$id_trans;

				$transIdtoProp=0;
				if($variable_auto_rcv!=2)
				{
					$transIdtoProp = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
					$data_array_trans.=",(".$transIdtoProp.",".$update_id.",".$cbo_company_id.",".$new_prod_id.",2,5,".$txt_transfer_date.",".$txt_to_order_book_id.",12,".$$rollWgt.",".$rate.",".$amount.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_store_name_to.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$cbo_to_body_part."','".$batch_id_to."','".$batch_id_to."')";
				}


				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$id_dtls.",".$update_id.",".$transIdfromProp.",".$transIdtoProp.",".$$fromProductUp.",".$new_prod_id.",2,".$$rollWgt.",".$$rollNo.",".$rate.",".$amount.",12,".$$colorId.",".$$floor.",".$$room.",".$$rack.",".$$shelf.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$$storeId.",".$cbo_store_name_to.",".$$requiDtlsId.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$cbo_from_body_part."','".$cbo_to_body_part."','".$txt_from_batch_id."','".$batch_id_to."',".$febDescripId.")";

				if($cbo_transfer_criteria==7){
					$booking_without_order = 0;
				}else{
					$booking_without_order = 1;
				}
				
				if($data_array_roll!="") $data_array_roll.= ",";
				$data_array_roll.="(".$id_roll.",".$$barcodeNo.",".$update_id.",".$id_dtls.",".$txt_to_order_book_id.",".$entry_form_no.",".$$rollWgt.",".$$rollNo.",".$$rollId.",".$$transRollId.",5,".$cbo_transfer_criteria.",".$booking_without_order.",".$txt_to_order_book_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$dtlsIdProp=$id_dtls;
				$all_trans_roll_id.=$$transRollId.",";

				$inserted_roll_id_arr[$id_roll] = $id_roll;
				$new_inserted[str_replace("'", "", $$barcodeNo)] = str_replace("'", "", $$barcodeNo);
			}

			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			if ($cbo_transfer_criteria==6) // Order to Sample
			{
				if($data_array_prop!="") $data_array_prop.= ",";
				$data_array_prop.="(".$id_prop.",".$transIdfromProp.",6,214,".$dtlsIdProp.",".$txt_from_order_book_id.",".$$fromProductUp.",".$$rollWgt.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}

			
			if ($cbo_transfer_criteria==7) // Sample to Order
			{
				if($variable_auto_rcv!=2)
				{
					if($data_array_prop!="") $data_array_prop.= ",";
					$data_array_prop.="(".$id_prop.",".$transIdtoProp.",5,214,".$dtlsIdProp.",".$txt_to_order_book_id.",".$new_prod_id.",".$$rollWgt.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				}
			}

				
		}
		
		if($txt_deleted_id!="")
		{
			//echo "10**5**jahid";die;
			$deletedIds=explode(",",$txt_deleted_id); $dtlsIDDel=''; $transIDDel=''; $rollIDDel=''; $rollIDactive=''; $delBarcodeNo='';
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


			$txt_deleted_prod_qty=trim($txt_deleted_prod_qty,"'");
			$txt_deleted_prod_qty=explode(",", $txt_deleted_prod_qty);

			foreach($txt_deleted_prod_qty as $val)
			{
				$qty_production=explode("=", $val);

				$up_del_prod_id_data[$qty_production[0]]['qnty'] += $qty_production[1];
				$deleted_prod_id_arr[$qty_production[0]] = $qty_production[0];

				$up_del_from_prod_id_data[$qty_production[2]]['qnty'] += $qty_production[1];
				$update_from_prod_id_arr[$qty_production[2]] = $qty_production[2];
			}

			$update_from_prod_id_arr= array_filter(array_unique($update_from_prod_id_arr));
			$deleted_prod_id_arr= array_filter(array_unique($deleted_prod_id_arr));


			
			if($delBarcodeNo != "")
			{
				$check_sql=sql_select("SELECT a.barcode_no , b.issue_number as system_no, a.entry_form, 'Issue' as msg_source from pro_roll_details a, inv_issue_master b where a.mst_id = b.id and a.entry_form = 71 and b.entry_form = 71 and a.is_returned != 1 and  a.status_active=1 and  b.status_active=1 and a.barcode_no in ($delBarcodeNo) 
				union all 
				select a.barcode_no , b.transfer_system_id as system_no, a.entry_form, 'Transfer' as msg_source from pro_roll_details a, inv_item_transfer_mst b where a.mst_id = b.id and a.entry_form = $entry_form_no and b.entry_form = $entry_form_no and a.re_transfer = 0 and  a.status_active=1 and  b.status_active=1 and a.barcode_no in ($delBarcodeNo) and a.id not in ($rollIDDel) ");

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


				$splited_roll_sql=sql_select("SELECT barcode_no,split_from_id from pro_roll_split where status_active =1 and barcode_no in ($delBarcodeNo)");

				foreach($splited_roll_sql as $bar)
				{ 
					$splited_roll_ref[$bar[csf('barcode_no')]][$bar[csf('split_from_id')]]=$bar[csf('barcode_no')];
				}

				$child_split_sql=sql_select("SELECT barcode_no, id from pro_roll_details where roll_split_from >0 and barcode_no in ($delBarcodeNo) and entry_form = $entry_form_no order by barcode_no");
				foreach($child_split_sql as $bar)
				{ 
					$child_split_arr[$bar[csf('barcode_no')]][$bar[csf('id')]]=$bar[csf('barcode_no')];
				}
				
				foreach($deletedIds as $delIds)
				{
					$delIds=explode("_",$delIds);
					if($splited_roll_ref[$delIds[5]][$delIds[3]] !="" || $child_split_arr[$delIds[5]][$delIds[3]] != "")
					{
						echo "20**"."Split Found. barcode no: ".$delIds[5];
						disconnect($con);
						die;
					}
				}
			}


			$prev_rol_id_sql=sql_select("SELECT from_roll_id from pro_roll_details where id in($rollIDDel)");
			$prev_rol_id="";
			foreach($prev_rol_id_sql as $row)
			{
				$prev_rol_id.=$row[csf("from_roll_id")].",";
			}
			$prev_rol_id=chop($prev_rol_id,",");
			//echo "10**5##select from_roll_id from pro_roll_details where id in($rollIDDel)";die;
			$field_array_status="updated_by*update_date*status_active*is_deleted";
			$data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
			$statusChangeTrans=sql_multirow_update("inv_transaction",$field_array_status,$data_array_status,"id",$transIDDel,0);
			$statusChangeDtls=sql_multirow_update("inv_item_transfer_dtls",$field_array_status,$data_array_status,"id",$dtlsIDDel,0);
			$statusChangeRoll=sql_multirow_update("pro_roll_details",$field_array_status,$data_array_status,"id",$rollIDDel,0);
			$activeRoll=sql_multirow_update("pro_roll_details","is_transfer*transfer_criteria*re_transfer","0*0*0","id",$prev_rol_id,0);
			//$active_prev_roll=sql_multirow_update("pro_roll_details","re_transfer","0","id",$prev_rol_id,0);
			
			if($flag==1) 
			{
				if($statusChangeTrans && $statusChangeDtls && $statusChangeRoll && $activeRoll) $flag=1; else $flag=0; 
			} 
		}

		if(!empty($product_id_insert_parameter))
		{
			foreach ($product_id_insert_parameter as $key => $val)
			{
				$prod_description_arr = explode("**", $key);
				$prod_id = $prod_description_arr[0];
				$fabric_desc_id = $prod_description_arr[1];
				$txt_gsm = str_replace("'", "'", $prod_description_arr[2]);
				$txt_width = str_replace("'", "'", $prod_description_arr[3]);
				$cons_compo = str_replace("'", "'", $prod_description_arr[4]);

				//$roll_amount = $product_id_insert_amount[$key];
				//$avg_rate_per_unit = $roll_amount/$val;

				$prod_name_dtls = trim($cons_compo) . ", " . trim($txt_gsm) . ", " . trim($txt_width);

				$roll_amount = 0;
				$avg_rate_per_unit = 0;
				if($variable_auto_rcv!=2)
				{
					$product_quantity =$val;
				}
				else
				{
					$product_quantity =0;
				}
				// if Qty is zero then rate & value will be zero
				if ($val<=0) 
				{
					$roll_amount=0;
					$avg_rate_per_unit=0;
				}
				
				if($data_array_prod_insert!="") $data_array_prod_insert.=",";
               	$data_array_prod_insert .= "(" . $prod_id . "," . $cbo_to_company_id . "," . $cbo_store_name . ",2," . $fabric_desc_id . ",'" . $cons_compo . "','" . $prod_name_dtls . "'," . "12" . "," . $avg_rate_per_unit . "," . $product_quantity . "," . $product_quantity . "," . $roll_amount . "," . $txt_gsm . ",'" . $txt_width . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

			}
		}


		$all_prod_id_arr=array_unique(explode(",",chop($all_prod_id,',')));

		$all_up_del_prod_id = array_merge($update_to_prod_id,$deleted_prod_id_arr,$update_from_prod_id_arr,$all_prod_id_arr); // New Roll, Deleted Roll, Deleted From roll product id Mearged to update
		//echo "10**";print_r($all_up_del_prod_id);die;
		if(!empty($all_up_del_prod_id))
		{

			$prod_id_array=array();
			$all_up_del_prod_id=implode(",",array_unique($all_up_del_prod_id));
			$toProdIssueResult=sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id in($all_up_del_prod_id) ");

			//echo "10**"."select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id in($all_up_del_prod_id) ";die;

			foreach($toProdIssueResult as $row)
			{
				//New Roll (+) and Deleted roll (-) and Deleted from roll (+)

				$new_added_from_prod_qnty = $prodData_array[$row[csf("id")]];
				$new_added_from_prod_amount = $prodData_array_amount[$row[csf("id")]];

				$stock_qnty = $product_id_update_parameter[$row[csf("id")]]['qnty'] + $row[csf("current_stock")] - $up_del_prod_id_data[$row[csf("id")]]['qnty'] + $up_del_from_prod_id_data[$row[csf("id")]]['qnty'] - $new_added_from_prod_qnty;

				//$stock_value = $product_id_update_parameter[$row[csf("id")]]['amount'] + $row[csf("stock_value")] - $up_del_prod_id_data[$row[csf("id")]]['amount'] + $up_del_from_prod_id_data[$row[csf("id")]]['amount'] - $new_added_from_prod_amount;

				$avg_rate_per_unit = $stock_value/$stock_qnty;
				// if Qty is zero then rate & value will be zero
				if ($stock_qnty<=0) 
				{
					$stock_value=0;
					$avg_rate_per_unit=0;
				}
				$prod_id_array[]=$row[csf('id')];
				$data_array_prod_update[$row[csf('id')]]=explode("*",("'".$stock_qnty."'*'".$avg_rate_per_unit."'*'".$stock_value."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
			unset($toProdIssueResult);
		}

		//------------------------------------------------------------------------
		
		$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0;
		
		if(count($data_array_update_roll)>0)
		{
			//echo "10**".bulk_update_sql_statement("inv_transaction","id",$field_array_trans_update,$data_array_update_trans,$transId_arr);die;
			$rID2=execute_query(bulk_update_sql_statement("inv_transaction","id",$field_array_trans_update,$data_array_update_trans,$transId_arr));
			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=0; 
			}
			  //echo "10**".bulk_update_sql_statement("inv_item_transfer_dtls","id",$field_array_dtls_update,$data_array_update_dtls,$dtlsId_arr);die;
			
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
			//echo "insert into inv_item_transfer_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
			$rIDDtls=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);
			if($flag==1) 
			{
				if($rIDDtls) $flag=1; else $flag=0; 
			} 
			
			//echo $flag;die;
			//echo "insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;die;
			$rIDRoll=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,1);
			if($flag==1) 
			{
				if($rIDRoll) $flag=1; else $flag=0; 
			} 
		}
		
		/*$rollIds=chop($rollIds,',');
		if($rollIds!="")
		{
			$rID7=sql_multirow_update("pro_roll_details","re_transfer","1","id",$rollIds,0);
			if($flag==1) 
			{
				if($rID7) $flag=1; else $flag=0; 
			} 
		}*/
		
		if($dtlsIDDel=="")
		{
			$update_dtls_id=chop($update_dtls_id,',');
		}
		else
		{
			$update_dtls_id=$update_dtls_id.$dtlsIDDel;
		}

		if ($cbo_transfer_criteria==6) // Order to Sample
		{
			if($update_dtls_id!="")
			{
				$query = execute_query("DELETE FROM order_wise_pro_details WHERE dtls_id in(".$update_dtls_id.") and entry_form=$entry_form_no");
				if($flag==1) 
				{
					if($query) $flag=1; else $flag=0; 
				} 
			}
			
			// echo "10**5**insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;
			if($data_array_prop!="")
			{
				$rIDProp=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1);
				if($flag==1) 
				{
					if($rIDProp) $flag=1; else $flag=0; 
				} 
			}
		}
		
		if($cbo_transfer_criteria==7) // Sample to Order
		{
			if($variable_auto_rcv!=2)
			{
				if($update_dtls_id!="")
				{
					$query = execute_query("DELETE FROM order_wise_pro_details WHERE dtls_id in(".$update_dtls_id.") and entry_form=$entry_form_no");
					if($flag==1) 
					{
						if($query) $flag=1; else $flag=0; 
					} 
				}
				
				// echo "10**5**insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;
				if($data_array_prop!="")
				{
					$rIDProp=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1);
					if($flag==1) 
					{
						if($rIDProp) $flag=1; else $flag=0; 
					} 
				}
			}			
		}
		
		if(!empty($new_inserted))
		{
			
			$rID8=execute_query("update pro_roll_details set is_returned=1 where barcode_no in (". implode(',', $new_inserted).") and id  not in (".implode(',', $inserted_roll_id_arr).")");
			if ($flag == 1)
			{
				if ($rID8)
					$flag = 1;
				else
					$flag = 0;
			}
		}

		

		if($data_array_prod_insert!="")
		{
			//echo "10**insert into product_details_master (".$field_array_prod_insert.") values ".$data_array_prod_insert;oci_rollback($con);die;
			$rID9=sql_insert("product_details_master",$field_array_prod_insert,$data_array_prod_insert,0);
			if ($flag == 1)
			{
				if ($rID9)
					$flag = 1;
				else
					$flag = 0;
			}
		}
		
		if(!empty($data_array_prod_update) )
		{
			// echo bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array );die;
			$prodUpdate=execute_query(bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array ));

			if ($flag == 1)
			{
				if ($prodUpdate)
					$flag = 1;
				else
					$flag = 0;
			}
		}

		
			
		if(count($data_array_batch_update)>0)
		{
			// echo "10**"; echo bulk_update_sql_statement("pro_batch_create_mst","id",$field_array_batch_update,$data_array_batch_update,$update_batch_id);oci_rollback($con);die;
			$batchMstUpdate=execute_query(bulk_update_sql_statement("pro_batch_create_mst","id",$field_array_batch_update,$data_array_batch_update,$update_batch_id));
			if($flag==1)
			{
				if($batchMstUpdate) $flag=1; else $flag=0;
			}
		}
		
		//echo "10**insert into pro_batch_create_mst (".$field_array_batch.") values ".$data_array_batch;oci_rollback($con);die;

		if(count($data_array_batch)>0)
		{
			//echo "10**insert into pro_batch_create_mst (".$field_array_batch.") values ".$data_array_batch;oci_rollback($con);die;
			$rID10=sql_insert("pro_batch_create_mst",$field_array_batch,$data_array_batch,0);
			if($flag==1)
			{
				if($rID10) $flag=1; else $flag=0;
			}
		}
		
		if($update_dtls_id!="")
		{
			//echo "20**delete from pro_batch_create_dtls where mst_id in($txt_to_batch_id) and dtls_id in(".$update_dtls_id.")";die;
			$delete_batch_dtls=execute_query( "DELETE from pro_batch_create_dtls where mst_id in($txt_to_batch_id) and dtls_id in(".$update_dtls_id.")",0);
			if($flag==1) 
			{
				if($delete_batch_dtls) $flag=1; else $flag=0; 
			} 
		}
		//echo $flag;die;
		// if(empty($all_dtlsId))
		// {
		// 	//echo "20**delete from pro_batch_create_dtls where mst_id in($txt_to_batch_id) and dtls_id in($all_dtlsId)";die;
		// 	$delete_batch_dtls=execute_query( "delete from pro_batch_create_dtls where mst_id in($txt_to_batch_id) and dtls_id in($all_dtlsId)",0);
		// 	if($flag==1)
		// 	{
		// 		if($delete_batch_dtls) $flag=1; else $flag=0;
		// 	}
		// }

		//echo "20**".$data_array_batch_dtls;die;

		if($data_array_batch_dtls!="")
		{
			//echo "6**insert into pro_batch_create_dtls (".$field_array_batch_dtls.") values ".$data_array_batch_dtls;die;
			$batchDtls=sql_insert("pro_batch_create_dtls",$field_array_batch_dtls,$data_array_batch_dtls,1);
			if($flag==1)
			{
				if($batchDtls) $flag=1; else $flag=0;
			}
		}

		

		//echo "10**5**insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;

		//echo "10**5**$flag";die;
		// echo "10**".$rID.'**'.$rID2.'**'.$rID3.'**'.$rID4.'**'.$rIDinv.'**'.$rIDDtls.'**'.$rIDRoll.'**'.$rID7.'**'.$query.'**'.$rIDProp.'**'.$rID9.'**'.$batchMstUpdate.'**'.$rID10.'**'.$delete_batch_dtls.'**'.$batchDtls.'**'.$prodUpdate;
		// oci_rollback($con);
		// die; 
	
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
		//check_table_status( $_SESSION['menu_id'], 0 );
		disconnect($con);
		die;
 	}
}


if ($action=="finish_fabric_order_to_order_transfer_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data); die();
	$cbo_transfer_criteria=$data[3];	

	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=2","id","product_name_details");
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');	
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');	
	$style_name_array=return_library_array( "select id, style_ref_no from sample_development_mst",'id','style_ref_no');

	$poDataArray=sql_select("SELECT b.id,a.buyer_name,a.style_ref_no,a.job_no,b.po_number,b.pub_shipment_date, b.file_no, b.grouping as ref_no, (a.total_set_qnty*b.po_quantity) as qty from wo_po_break_down b,wo_po_details_master a where  a.job_no=b.job_no_mst and a.company_name=$data[0] and b.status_active=1 and b.is_deleted=0 ");
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

	$sql="SELECT id, transfer_system_id, transfer_date, challan_no, from_order_id, to_order_id, item_category from inv_item_transfer_mst a where id='$data[1]' and company_id='$data[0]'";
	//echo $sql;die;
	$dataArray=sql_select($sql);

	$sampledata_array=sql_select("SELECT a.id as booking_id, a.booking_no, a.booking_no_prefix_num, a.buyer_id, b.style_id, b.body_part, b.grey_fabric
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
	where a.booking_no=b.booking_no and a.id=".$dataArray[0][csf('from_order_id')]."");

	$sampledata_to_array=sql_select("SELECT a.id as booking_id, a.booking_no, a.booking_no_prefix_num, a.buyer_id, b.style_id, b.body_part, b.grey_fabric
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
	where a.booking_no=b.booking_no and a.id=".$dataArray[0][csf('to_order_id')]."");

	/*// Sample to Sample
	$sampledata_from_array=sql_select("SELECT a.id as booking_id, a.booking_no, a.booking_no_prefix_num, a.buyer_id, b.style_id, b.body_part, b.grey_fabric
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
	where a.booking_no=b.booking_no and a.id=".$dataArray[0][csf('from_order_id')]."");
	
	$sampledata_to_array=sql_select("SELECT a.id as booking_id, a.booking_no, a.booking_no_prefix_num, a.buyer_id, b.style_id, b.body_part, b.grey_fabric
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
	where a.booking_no=b.booking_no and a.id=".$dataArray[0][csf('to_order_id')]."");

	// Order to Sample
	$sampledata_array=sql_select("SELECT a.id as booking_id, a.booking_no, a.booking_no_prefix_num, a.buyer_id, b.style_id, b.body_part, b.grey_fabric
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
	where a.booking_no=b.booking_no and a.id=".$dataArray[0][csf('to_order_id')]."");*/

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
    		<?
    		if($cbo_transfer_criteria==8) // Sample to Sample
			{
				$entry_form_no = 214;
				?>
				<td>
	                <table width="100%" cellspacing="0" align="right">
	                 	<tr>
	                    	<td colspan="4" align="center" style="font-weight:bold; font-size:18px;"><u><? echo $from_title; ?></u></td>
	                    </tr>
	                    <tr>
	                    	<td width="100">Booking No:</td>
	                        <td width="340" colspan="3">&nbsp;<? echo $sampledata_array[0][csf('booking_no')]; ?></td>
	                    </tr>
	                    <tr>
	                    	<td>Buyer:</td>
	                        <td >&nbsp;<? echo $buyer_library[$sampledata_array[0][csf('buyer_id')]]; ?></td>
	                        <td width="100">Quantity:</td>
	                        <td>&nbsp;<? echo $sampledata_array[0][csf('grey_fabric')]; ?></td>
	                    </tr>
	                    <tr>
	                    	<td>Style Ref. :</td>
	                        <td colspan="3">&nbsp;<? echo $style_name_array[$sampledata_array[0][csf('style_id')]]; ?></td>  
	                    </tr> 
	                    <tr>
	                    	<td>Body Part:</td>
	                        <td colspan="3">&nbsp;<? echo $body_part[$sampledata_array[0][csf('body_part')]]; ?></td>
	                    </tr>
	                </table>
	            </td>
	        	<td width="450">
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
				<?
			}
			elseif($cbo_transfer_criteria==7) // Sample to Order
			{
				$entry_form_no = 214;
				?>
				<td>
	                <table width="100%" cellspacing="0" align="right">
	                 	<tr>
	                    	<td colspan="4" align="center" style="font-weight:bold; font-size:18px;"><u>Form Sample</u></td>
	                    </tr>
	                    <tr>
	                    	<td width="100">Booking No:</td>
	                        <td width="340" colspan="3">&nbsp;<? echo $sampledata_array[0][csf('booking_no')]; ?></td>
	                       
	                    </tr>
	                    <tr>
	                    	<td>Buyer:</td>
	                        <td >&nbsp;<? echo $buyer_library[$sampledata_array[0][csf('buyer_id')]]; ?></td>
	                        <td width="100">Quantity:</td>
	                        <td>&nbsp;<? echo $sampledata_array[0][csf('grey_fabric')]; ?></td>
	                        
	                    </tr>
	                    <tr>
	                    	<td>Style Ref. :</td>
	                        <td colspan="3">&nbsp;<? echo $style_name_array[$sampledata_array[0][csf('style_id')]]; ?></td>
	                        
	                    </tr> 
	                    <tr>
	                    	<td>Body Part:</td>
	                        <td colspan="3">&nbsp;<? echo $body_part[$sampledata_array[0][csf('body_part')]]; ?></td>
	              
	                    </tr>
	                </table>
	            </td>
	        	<td width="450">
	                <table width="100%" cellspacing="0" align="right">
	                 	<tr>
	                    	<td colspan="4" align="center" style="font-weight:bold; font-size:18px;"><u>To Order</u></td>
	                    </tr>
	                    <tr>
	                    	<td width="100">Order No:</td>
	                        <td width="120">&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['no']; ?></td>
	                        <td width="100">Quantity:</td>
	                        <td>&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['qty']; ?></td>
	                    </tr>
	                    <tr>
	                    	<td>Buyer:</td>
	                        <td>&nbsp;<? echo $buyer_library[$job_array[$dataArray[0][csf('to_order_id')]]['buyer']]; ?></td>
	                        <td>Job No:</td>
	                        <td>&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['job']; ?></td>
	                    </tr>
	                    <tr>
	                    	<td>File No:</td>
	                        <td>&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['file']; ?></td>
	                        <td>Ref. No:</td>
	                        <td>&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['ref']; ?></td>
	                    </tr> 
	                    <tr>
	                    	<td>Style Ref:</td>
	                        <td>&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['style']; ?></td>
	                        <td>Ship. Date:</td>
	                        <td>&nbsp;<? echo change_date_format($job_array[$dataArray[0][csf('to_order_id')]]['date']); ?></td>
	                    </tr>  
	                </table>
	            </td>
				<?
			}
			else // Order TO Sample
			{
				$entry_form_no = 214;
				?>
				<td>
	                <table width="100%" cellspacing="0" align="right">
	                 	<tr>
	                    	<td colspan="4" align="center" style="font-weight:bold; font-size:18px;"><u>From Order</u></td>
	                    </tr>
	                    <tr>
	                    	<td width="100">Order No:</td>
	                        <td width="120">&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['no']; ?></td>
	                        <td width="100">Quantity:</td>
	                        <td>&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['qty']; ?></td>
	                    </tr>
	                    <tr>
	                    	<td>Buyer:</td>
	                        <td>&nbsp;<? echo $buyer_library[$job_array[$dataArray[0][csf('from_order_id')]]['buyer']]; ?></td>
	                        <td>Job No:</td>
	                        <td>&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['job']; ?></td>
	                    </tr>
	                    <tr>
	                    	<td>File No:</td>
	                        <td>&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['file']; ?></td>
	                        <td>Ref. No:</td>
	                        <td>&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['ref']; ?></td>
	                    </tr> 
	                    <tr>
	                    	<td>Style Ref:</td>
	                        <td>&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['style']; ?></td>
	                        <td>Ship. Date:</td>
	                        <td>&nbsp;<? echo change_date_format($job_array[$dataArray[0][csf('from_order_id')]]['date']); ?></td>
	                    </tr>
	                </table>
	            </td>
	        	<td width="450">
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
				<?
			}
    		?>
    		           
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
            $sql_dtls="SELECT a.from_prod_id, a.transfer_qnty, a.uom, a.y_count, a.brand_id, a.yarn_lot, a.to_rack, a.to_shelf, a.stitch_length, b.barcode_no, b.roll_no from inv_item_transfer_dtls a, pro_roll_details b where a.id=b.dtls_id and b.entry_form=$entry_form_no and a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.barcode_no";
            echo  $sql_dtls;
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
        <div style="margin-left: 30px;">
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

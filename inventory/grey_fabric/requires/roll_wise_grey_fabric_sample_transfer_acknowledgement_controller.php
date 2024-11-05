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

if ($action=="sampleRequisitionTransfer_popup") // sample Acknowledge Transfer System ID Popup
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
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+<? echo $cbo_transfer_criteria; ?>, 'create_sample_requisition_search_list_view', 'search_div', 'roll_wise_grey_fabric_sample_transfer_acknowledgement_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
	
 	$sql="SELECT id, transfer_prefix_number, $year_field transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria, item_category 
 	from inv_item_transfer_mst where item_category=13 and to_company=$company_id and $search_field like '$search_string' and transfer_criteria=$transfer_criteria and entry_form=$entry_form_no and is_acknowledge=0 and status_active=1 and is_deleted=0 order by id";
	//echo $sql;
	
	$arr=array(3=>$company_arr,5=>$item_transfer_criteria,6=>$item_category);

	echo  create_list_view("tbl_list_search", "Transfer ID,Year,Challan No,Company,Transfer Date,Transfer Criteria,Item Category", "80,70,100,80,90,130","750","250",0, $sql, "js_set_value", "id", "", 1, "0,0,0,company_id,0,transfer_criteria,item_category", $arr, "transfer_prefix_number,year,challan_no,company_id,transfer_date,transfer_criteria,item_category", '','','0,0,0,0,3,0,0');
	
	exit();
}

if($action=='populate_data_from_sample_requisition_master') // sample_requisition master data set
{
	//$data_array=sql_select("SELECT transfer_criteria, transfer_system_id,challan_no, company_id, to_company, transfer_date, item_category, from_order_id, to_order_id, from_samp_dtls_id, to_samp_dtls_id from inv_item_transfer_mst where id='$data'");


	$data_array=sql_select("SELECT a.transfer_criteria, a.transfer_system_id,a.challan_no, a.company_id, a.to_company, a.transfer_date, a.item_category, a.from_order_id, a.to_order_id, a.from_samp_dtls_id, a.to_samp_dtls_id, b.body_part_id, b.to_body_part from inv_item_transfer_mst a, inv_item_transfer_dtls b where a.id = b.mst_id and a.id='$data' group by a.transfer_criteria, a.transfer_system_id,a.challan_no, a.company_id, a.to_company, a.transfer_date, a.item_category, a.from_order_id, a.to_order_id, a.from_samp_dtls_id, a.to_samp_dtls_id, b.body_part_id, b.to_body_part");


	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('txt_transfer_mst_id').value 			= '".$data."';\n";
		echo "document.getElementById('cbo_transfer_criteria').value 		= '".$row[csf("transfer_criteria")]."';\n";
		echo "document.getElementById('txt_transfer_sys_no').value 			= '".$row[csf("transfer_system_id")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("to_company")]."';\n";
		echo "document.getElementById('cbo_company_id_from').value 			= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('txt_transfer_date').value 			= '".change_date_format($row[csf("transfer_date")])."';\n";

		echo "document.getElementById('cbo_from_body_part').value 			= '".$row[csf("body_part_id")]."';\n";
		echo "document.getElementById('cbo_to_body_part').value 			= '".$row[csf("to_body_part")]."';\n";
		
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
		echo "get_php_form_data('".$from_order_book_id."**"."from"."**".$row[csf("transfer_criteria")]."','populate_data_from_sample','requires/roll_wise_grey_fabric_sample_transfer_acknowledgement_controller');\n";
		echo "get_php_form_data('".$to_order_book_id."**".$row[csf("transfer_criteria")]."','populate_data_to_order','requires/roll_wise_grey_fabric_sample_transfer_acknowledgement_controller');\n";
		echo "$('#cbo_transfer_criteria').attr('disabled','disabled');\n";
		echo "$('#cbo_company_id').attr('disabled','disabled');\n";
		echo "$('#txt_from_order_book_no').attr('disabled','disabled');\n";
		echo "$('#txt_to_order_book_no').attr('disabled','disabled');\n";

		echo "load_room_rack_self_bin('requires/roll_wise_grey_fabric_sample_transfer_controller*13*cbo_store_name_to', 'store','to_store_td', $('#cbo_company_id').val());\n";

		if($row[csf("to_store")]>0)
		{
			echo "document.getElementById('cbo_store_name_to').value 				= '".$row[csf("to_store")]."';\n";
		}
		if($row[csf("to_floor_id")]>0){
			echo "load_room_rack_self_bin('requires/roll_wise_grey_fabric_sample_transfer_controller*13*cbo_floor_to', 'floor','floor_td_to', '".$row[csf("to_company")]."','"."','".$row[csf('to_store')]."',this.value);\n";
			echo "document.getElementById('cbo_floor_to').value 			= '".$row[csf("to_floor_id")]."';\n";
		}
		if($row[csf("to_room")]>0){
			echo "load_room_rack_self_bin('requires/roll_wise_grey_fabric_sample_transfer_controller*13*cbo_room_to', 'room','room_td_to', '".$row[csf("to_company")]."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."',this.value);\n";
			echo "document.getElementById('cbo_room_to').value 				= '".$row[csf("to_room")]."';\n";
		}
		if($row[csf("to_rack")]>0){
			echo "load_room_rack_self_bin('requires/roll_wise_grey_fabric_sample_transfer_controller*13*txt_rack_to', 'rack','rack_td_to','".$row[csf("to_company")]."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."','".$row[csf('to_room')]."',this.value);\n";
			echo "document.getElementById('txt_rack_to').value 				= '".$row[csf("to_rack")]."';\n";
		}
		if($row[csf("to_shelf")]>0){
			echo "load_room_rack_self_bin('requires/roll_wise_grey_fabric_sample_transfer_controller*13*txt_shelf_to', 'shelf','shelf_td_to','".$row[csf("to_company")]."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."','".$row[csf('to_room')]."','".$row[csf('to_rack')]."',this.value);\n";
			echo "document.getElementById('txt_shelf_to').value 			= '".$row[csf("to_shelf")]."';\n";
		}


		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_grey_transfer_entry',1,1);\n"; 
		exit();
	}
}

if($action=="show_sample_requisition_transfer_listview") // sample Transfer Dtls list view
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
	$product_arr=return_library_array( "select id, product_name_details from product_details_master where item_category_id=13",'id','product_name_details');
	$delv_arr=return_library_array("select barcode_num, grey_sys_id from pro_grey_prod_delivery_dtls where entry_form=56 and order_id=$order_id", "barcode_num", "grey_sys_id");
	
	//$trans_arr=return_library_array("select barcode_no, mst_id from pro_roll_details where entry_form=2", "barcode_no", "mst_id");
	
	//$issued_barcode_arr=return_library_array("select barcode_no from pro_roll_details where entry_form=61 and status_active=1 and is_deleted=0 and po_breakdown_id=$order_id and is_returned = 0","barcode_no", "barcode_no");

	//$re_trans_arr=return_library_array("select barcode_no from pro_roll_details where entry_form=$entry_form_no and re_transfer=1 and status_active=1 and is_deleted=0 and mst_id=$mst_id ", "barcode_no", "barcode_no");
	
	/*$transfer_arr=array();
	$transfer_dataArray=sql_select("SELECT a.id, a.trans_id, a.to_trans_id, b.id as roll_id, b.barcode_no from inv_item_transfer_dtls a, pro_roll_details b where a.id=b.dtls_id and a.mst_id=$mst_id and b.mst_id=$mst_id and b.entry_form=$entry_form_no and b.transfer_criteria=$cbo_transfer_criteria and b.status_active=1 and b.is_deleted=0");
	foreach($transfer_dataArray as $row)
	{
		$transfer_arr[$row[csf('barcode_no')]]['dtls_id']=$row[csf('id')];
		$transfer_arr[$row[csf('barcode_no')]]['from_trans_id']=$row[csf('trans_id')];
		$transfer_arr[$row[csf('barcode_no')]]['to_trans_id']=$row[csf('to_trans_id')];
		$transfer_arr[$row[csf('barcode_no')]]['rolltableId']=$row[csf('roll_id')];
	}*/
	
	$programArr=return_library_array("select a.id, a.booking_id from inv_receive_master a, pro_roll_details b where a.id=b.mst_id and a.entry_form=2 and a.receive_basis=2 and b.entry_form=2 and b.po_breakdown_id=$order_id and b.status_active=1 and b.is_deleted=0", "id", "booking_id");
	
	$sql="SELECT a.id, a.entry_form, 0 as receive_basis, 0 as booking_id, b.to_prod_id as prod_id, b.yarn_lot, b.y_count as yarn_count, b.stitch_length, b.brand_id, b.floor_id, b.room, b.rack as rack, b.shelf as self, b.id as dtls_id, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 3 as type , b.to_store as store_id, b.transfer_requ_dtls_id, b.from_prod_id, b.to_order_id, b.transfer_qnty, b.trans_id, b.to_trans_id, d.product_name_details
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, product_details_master d
	WHERE a.id=b.mst_id and b.id=c.dtls_id and b.to_prod_id=d.id and a.entry_form in($entry_form_no) and c.entry_form in($entry_form_no) and c.status_active=1 and c.is_deleted=0 and c.mst_id=$mst_id and b.active_dtls_id_in_transfer=1
	order by barcode_no";
	//echo $sql;//die;

	/*$prev_rcv=return_field_value( "sum(a.cons_quantity) as cons_quantity", "inv_transaction a, order_wise_pro_details b","a.status_active=1 and a.is_deleted=0 and a.transaction_type=5 and a.mst_id=$mst_id and a.prod_id=$prod_id and a.pi_wo_batch_no='".$row[csf("to_batch_id")]."' and b.po_breakdown_id ='".$row[csf("to_order_id")]."' and a.id = b.trans_id and b.entry_form in($entry_form_no)","cons_quantity");*/

	
	$data_array=sql_select($sql);	
	$i=1;
	foreach($data_array as $row)
	{
		$prev_rcv=return_field_value( "sum(a.cons_quantity) as cons_quantity", "inv_transaction a","a.status_active=1 and a.is_deleted=0 and a.transaction_type=5 and a.mst_id=$mst_id and a.prod_id='".$row[csf("from_prod_id")]."' ","cons_quantity");
		$balance_qnty=$row[csf('transfer_qnty')]-$prev_rcv;
		if($balance_qnty>0)
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
			}
			
			$row[csf('roll_id')]=$row[csf('roll_id_prev')];
			
			if($re_trans_arr[$row[csf('barcode_no')]]=="")
			{
				$disabled=""; 	
			}
			else $disabled="disabled";
			
			$disabled="disabled";
		

			$dtls_id=$row[csf('dtls_id')];
			$from_trans_id=$row[csf('trans_id')];
			$to_trans_id=$row[csf('to_trans_id')];
			$rolltableId=$transRollId;

			$checked="checked";
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
                    <input type="hidden" name="yarnCount[]" id="yarnCount_<? echo $i; ?>" value="<? echo $row[csf('yarn_count')]; ?>"/>
                    <input type="hidden" name="stichLn[]" id="stichLn_<? echo $i; ?>" value="<? echo $row[csf('stitch_length')]; ?>"/>
                    <input type="hidden" name="brandId[]" id="brandId_<? echo $i; ?>" value="<? echo $row[csf('brand_id')]; ?>"/>
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
                </td>
			</tr>
			<? 
			$i++;
		}
	} 
	exit();
}

if($action=="populate_data_from_sample") // ACK
{
	//print_r($data);
	$data=explode("**",$data);
	$return_id=$data[0]; // return_id is booking or order no
	$from=$data[1];
	$transfer_criteria=$data[2];

	$style_name_array=return_library_array( "select id, style_ref_no from sample_development_mst",'id','style_ref_no');
	if ($transfer_criteria==6) // Order to Sample
	{
		$data_array=sql_select("SELECT a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$return_id");
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
			echo "document.getElementById('txt_from_job_no').value 				= '".$row[csf("job_no")]."';\n";
			echo "document.getElementById('txt_from_gmts_item').value 			= '".$gmts_item."';\n";
			echo "document.getElementById('txt_from_shipment_date').value 		= '".change_date_format($row[csf("shipment_date")])."';\n";
			exit();
		}
	}
	else // Sample to Sample and Sample to Order, When Transfer System ID a.id=$return_id
	{
		/*Sample to Sample=$data_array=sql_select("select a.id as booking_id, a.booking_no, a.booking_no_prefix_num, a.buyer_id, b.id as dtls_id, b.style_id, b.body_part, b.grey_fabric
		from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b  
		where a.booking_no=b.booking_no and b.id=$sample_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");*/

		// Sample to Order
		/*$data_array=sql_select("SELECT a.id as booking_id, a.booking_no, a.booking_no_prefix_num, a.buyer_id, b.style_id, b.body_part, b.grey_fabric
		from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b  
		where a.booking_no=b.booking_no and a.id=$sample_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");*/

		$data_array=sql_select("SELECT a.id as booking_id, a.booking_no, a.booking_no_prefix_num, a.buyer_id, b.style_id, b.body_part, b.grey_fabric, b.id as dtls_id
		from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b  
		where a.booking_no=b.booking_no and b.id=$return_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		// Sample to sample b.id=$return_id
		// Sample to Order a.id=$sample_id

		foreach ($data_array as $row)
		{ 
			echo "document.getElementById('txt_from_order_book_no').value 		= '".$row[csf("booking_no")]."';\n";
			echo "document.getElementById('txt_from_order_book_id').value 		= '".$row[csf("booking_id")]."';\n";
			echo "document.getElementById('txt_from_order_book_dtls_id').value 	= '".$row[csf("dtls_id")]."';\n";
			echo "document.getElementById('txt_from_qnty').value 				= '".$row[csf("grey_fabric")]."';\n";
			echo "document.getElementById('cbo_from_buyer_name').value 			= '".$row[csf("buyer_id")]."';\n";
			echo "document.getElementById('txt_from_style_ref').value 			= '".$style_name_array[$row[csf("style_id")]]."';\n";
			echo "document.getElementById('cbo_from_body_part').value 			= '".$row[csf("body_part")]."';\n";
			exit();
		}
	}	
}

if($action=='populate_data_to_order') // ACK
{
	//print_r($data);
	$data=explode("**", $data);
	$po_id=$data[0];
	$transfer_criteria=$data[1];

	if ($transfer_criteria==7) // Order
	{
		$data_array=sql_select("SELECT a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$po_id");
		foreach ($data_array as $row)
		{ 
			$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
			foreach($gmts_item_id as $item_id)
			{
				if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
			}
			
			echo "document.getElementById('txt_to_order_book_id').value 	= '".$po_id."';\n";
			echo "document.getElementById('txt_to_order_book_no').value 	= '".$row[csf("po_number")]."';\n";
			echo "document.getElementById('txt_to_qnty').value 				= '".$row[csf("po_quantity")]."';\n";
			echo "document.getElementById('cbo_to_buyer_name').value 		= '".$row[csf("buyer_name")]."';\n";
			echo "document.getElementById('txt_to_style_ref').value 		= '".$row[csf("style_ref_no")]."';\n";
			//echo "document.getElementById('cbo_to_body_part').value 		= '".$row[csf("body_part")]."';\n";
			echo "document.getElementById('txt_to_job_no').value 			= '".$row[csf("job_no")]."';\n";
			echo "document.getElementById('txt_to_gmts_item').value 		= '".$gmts_item."';\n";
			echo "document.getElementById('txt_to_shipment_date').value 	= '".change_date_format($row[csf("shipment_date")])."';\n";
			exit();
		}
	}
	else
	{
		$style_name_array=return_library_array( "SELECT id, style_ref_no from sample_development_mst",'id','style_ref_no');
		$data_array=sql_select("SELECT a.id as booking_id, a.booking_no, a.booking_no_prefix_num, a.buyer_id, b.id as booking_dtls_id, b.style_id, b.body_part, b.grey_fabric
		from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b  
		where a.booking_no=b.booking_no and b.id=$po_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		 
		foreach ($data_array as $row)
		{ 
			
			echo "document.getElementById('txt_to_order_book_no').value 		= '".$row[csf("booking_no")]."';\n";
			echo "document.getElementById('txt_to_order_book_id').value 		= '".$row[csf("booking_id")]."';\n";
			echo "document.getElementById('txt_to_order_book_dtls_id').value 	= '".$row[csf("booking_dtls_id")]."';\n";
			echo "document.getElementById('txt_to_qnty').value 					= '".$row[csf("grey_fabric")]."';\n";
			echo "document.getElementById('cbo_to_buyer_name').value 			= '".$row[csf("buyer_id")]."';\n";
			echo "document.getElementById('txt_to_style_ref').value 			= '".$style_name_array[$row[csf("style_id")]]."';\n";
			echo "document.getElementById('cbo_to_body_part').value 			= '".$row[csf("body_part")]."';\n";
			exit();
		}
	}	
}

if($action=="show_dtls_list_view") // Data come from Sample transfer
{
	$data=explode("**", $data);
	$booking_order_id = $data[0];
	$transfer_criteria = $data[1];

	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');	
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');	
	$product_arr=return_library_array( "select id, product_name_details from product_details_master where item_category_id=13",'id','product_name_details');
	$delv_arr=return_library_array("select barcode_num, grey_sys_id from pro_grey_prod_delivery_dtls where entry_form=56 and order_id=$booking_order_id", "barcode_num", "grey_sys_id");	
				
	$programArr=return_library_array("select a.id, a.booking_id from inv_receive_master a, pro_roll_details b where a.id=b.mst_id and a.entry_form=2 and a.receive_basis=2 and b.entry_form=2 and b.po_breakdown_id=$booking_order_id and b.status_active=1 and b.is_deleted=0", "id", "booking_id");

	if ($transfer_criteria==6) 
	{
		$sql="SELECT a.id, a.entry_form, a.receive_basis, a.booking_id, b.prod_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.floor_id, b.room, b.rack, b.self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 1 as type, a.store_id
		from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c 
		WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=$booking_order_id
		union all
		 select a.id, a.entry_form, 0 as receive_basis, 0 as booking_id, b.from_prod_id as prod_id, b.yarn_lot, b.y_count as yarn_count, b.stitch_length, b.brand_id, b.floor_id, b.room, b.rack, b.shelf as self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 2 as type, b.to_store as store_id
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c 
		WHERE a.id=b.mst_id and b.id=c.dtls_id  and c.entry_form in(82,83,183) and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=$booking_order_id";
	}
	else
	{
		// Sample to Sample
		$sql="SELECT a.id, a.entry_form, a.receive_basis, a.booking_id, b.prod_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.floor_id, b.room, b.rack, b.self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 1 as type, a.store_id
		from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c 
		WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2,22,58) and b.trans_id<>0 and c.entry_form in(2,22,58) and c.re_transfer =0 and c.booking_without_order=1 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=$booking_order_id

		UNION ALL

		SELECT a.id,a.entry_form, 0 as receive_basis, 0 as booking_id, b.from_prod_id as prod_id, b.yarn_lot, b.y_count as yarn_count, b.stitch_length, b.brand_id, b.floor_id, b.room, b.to_rack as rack, b.to_shelf as self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 3 as type , b.to_store as store_id
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c 
		where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(110,180) and c.entry_form in(110,180) and c.status_active=1 and c.is_deleted=0 
		and a.to_order_id =$booking_order_id and c.booking_without_order=1 and c.re_transfer =0
		order by barcode_no";
	}
	// echo $sql;
	$data_array=sql_select($sql);	
	$i=1;$barcod_NOs="";
	foreach($data_array as $row)
	{
		$barcod_NOs.=$row[csf('barcode_no')].",";
	}  
	// echo $barcod_NOs.'BAER';die;
	$barcod_NOs=chop($barcod_NOs,",");
	$barcodeData = sql_select("select barcode_no,entry_form from pro_roll_details where entry_form=61 and status_active=1 and is_deleted=0 and barcode_no in($barcod_NOs) and is_returned = 0");
	foreach ($barcodeData as $row) 
	{
		$issued_barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
		//$issued_barcode_arr2[$row[csf('barcode_no')]] = $row[csf('entry_form')];
	}
	/*echo "<pre>";
	print_r($issued_barcode_arr);die;*/
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

			if ($transfer_criteria==6) 
			{
				$program_no='';
				if($row[csf('entry_form')]==2)
				{
					if($row[csf('receive_basis')]==2) $program_no=$row[csf('booking_id')];
				}
				else if($row[csf('entry_form')]==58 || $row[csf('entry_form')]==82 || $row[csf('entry_form')]==83)
				{
					$program_no=$programArr[$delv_arr[$row[csf('barcode_no')]]];
					$row[csf('roll_id')]=$row[csf('roll_id_prev')];
				}
			}
			else
			{
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
				<td width="55"><p><? echo create_drop_down("floor_$i", 55, "","",1, "--Select Floor--", "", "" ); ?>&nbsp;</p></td>
				<td width="55"><p><? echo create_drop_down( "room_$i", 55, "","",1, "--Select Room--", "", "" ); ?>&nbsp;</p></td>
				<td width="55"><p><? echo create_drop_down( "rack_$i", 55, "","",1, "--Select Rack--", "", "" ); ?>&nbsp;</p></td>
				<td width="55"><p><? echo create_drop_down( "shelf_$i", 55, "","",1, "--Select Shelf--", "", "" ); ?>&nbsp;</p></td>
				<td width="80"><p><? echo $row[csf('stitch_length')]; ?>&nbsp;</p></td>
				<td align="right" style="padding-right:2px"><? echo $row[csf('qnty')]; ?>
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
                </td>
			</tr>
			<? 
			$i++; 
		}
	} 
	exit();
}

if($action=="show_transfer_listview") // Data come from after Acknowledgement
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
	$product_arr=return_library_array( "select id, product_name_details from product_details_master where item_category_id=13",'id','product_name_details');
	$delv_arr=return_library_array("select barcode_num, grey_sys_id from pro_grey_prod_delivery_dtls where entry_form=56 and order_id=$order_id", "barcode_num", "grey_sys_id");
	$trans_arr=return_library_array("select barcode_no, mst_id from pro_roll_details where entry_form=2", "barcode_no", "mst_id");

	//$issued_barcode_arr=return_library_array("select barcode_no from pro_roll_details where entry_form=61 and status_active=1 and is_deleted=0 and po_breakdown_id=$order_id and is_returned = 0","barcode_no", "barcode_no");
	
	$transfer_arr=array();
	$transfer_dataArray=sql_select("SELECT a.id, a.trans_id, a.to_trans_id, b.id as roll_id, b.barcode_no from inv_item_transfer_dtls a, pro_roll_details b where a.id=b.dtls_id and a.mst_id=$mst_id and b.mst_id=$mst_id and b.entry_form=$entry_form_no and b.transfer_criteria=$cbo_transfer_criteria and b.status_active=1 and b.is_deleted=0");
	foreach($transfer_dataArray as $row)
	{
		$transfer_arr[$row[csf('barcode_no')]]['dtls_id']=$row[csf('id')];
		$transfer_arr[$row[csf('barcode_no')]]['from_trans_id']=$row[csf('trans_id')];
		$transfer_arr[$row[csf('barcode_no')]]['to_trans_id']=$row[csf('to_trans_id')];
		$transfer_arr[$row[csf('barcode_no')]]['rolltableId']=$row[csf('roll_id')];
	}
	
	$programArr=return_library_array("select a.id, a.booking_id from inv_receive_master a, pro_roll_details b where a.id=b.mst_id and a.entry_form=2 and a.receive_basis=2 and b.entry_form=2 and b.po_breakdown_id=$order_id and b.status_active=1 and b.is_deleted=0", "id", "booking_id");
	
	$sql="SELECT a.id, a.entry_form, 0 as receive_basis, 0 as booking_id, b.from_prod_id as prod_id, b.yarn_lot, b.y_count as yarn_count, b.stitch_length, b.brand_id, b.floor_id, b.room, b.to_rack as rack, b.to_shelf as self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 3 as type , b.to_store as store_id, transfer_requ_dtls_id
	from inv_item_trans_acknowledgement a, inv_item_transfer_dtls b, pro_roll_details c 
	WHERE a.challan_id=b.mst_id and b.id=c.dtls_id and a.entry_form in($entry_form_no) and c.entry_form in($entry_form_no) and c.status_active=1 and c.is_deleted=0 and c.mst_id=$mst_id
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
		
		$disabled="disabled";
		
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
                <input type="hidden" name="yarnCount[]" id="yarnCount_<? echo $i; ?>" value="<? echo $row[csf('yarn_count')]; ?>"/>
                <input type="hidden" name="stichLn[]" id="stichLn_<? echo $i; ?>" value="<? echo $row[csf('stitch_length')]; ?>"/>
                <input type="hidden" name="brandId[]" id="brandId_<? echo $i; ?>" value="<? echo $row[csf('brand_id')]; ?>"/>
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
            </td>
		</tr>
		<? 
		$i++;
	} 
	exit();
}

if ($action=="sampleToOrderTransfer_popup") // System id popup
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
	                    <th width="240" id="search_by_td_up">Please Enter Acknowledge ID</th>
	                    <th>
	                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
	                        <input type="hidden" name="transfer_id" id="transfer_id" class="text_boxes" value="">
	                    </th>
	                </thead>
	                <tr class="general">
	                    <td>
							<?
								$search_by_arr=array(1=>"Acknowledge ID");
								$dd="change_search_event(this.value, '0', '0', '../../../') ";
								echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
	                        ?>
	                    </td>
	                    <td id="search_by_td">
	                        <input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
	                    </td>
	                    <td>
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+<? echo $cbo_transfer_criteria; ?>, 'create_transfer_search_list_view', 'search_div', 'roll_wise_grey_fabric_sample_transfer_acknowledgement_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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

if($action=='create_transfer_search_list_view') // System id popup list view ACK Data
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
		$search_field="id";
	}
	else {
		$search_field="id";
	}
	
	if($db_type==0) $year_field="YEAR(insert_date) as year,"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year,";
	else $year_field="";//defined Later
	
 	$sql="SELECT id, challan_id, $year_field challan_id, challan_id, company_id, acknowledg_date, transfer_criteria, item_category from inv_item_trans_acknowledgement where item_category=13 and company_id=$company_id and $search_field like '$search_string' and transfer_criteria=$transfer_criteria and entry_form=$entry_form_no and status_active=1 and is_deleted=0 order by id";
	//echo $sql;
	$transfer_system_arr=return_library_array( "select id, transfer_system_id from inv_item_transfer_mst",'id','transfer_system_id');
	$arr=array(2=>$transfer_system_arr,3=>$company_arr,5=>$item_transfer_criteria,6=>$item_category);

	echo  create_list_view("tbl_list_search", "Acknowledge ID,Year,Challan No,Company,Acknowledge Date,Acknowledge Criteria,Item Category", "80,70,100,80,90,130","750","250",0, $sql, "js_set_value", "challan_id", "", 1, "0,0,challan_id,company_id,0,transfer_criteria,item_category", $arr, "id,year,challan_id,company_id,acknowledg_date,transfer_criteria,item_category", '','','0,0,0,0,3,0,0');
	
	exit();
}

if($action=='populate_data_from_transfer_master') // 
{
	$data_array=sql_select("SELECT b.id as system_id, b.acknowledg_date, b.transfer_criteria, b.challan_id, a.transfer_system_id, a.company_id, a.item_category, a.from_order_id, a.to_order_id, a.from_samp_dtls_id, a.to_samp_dtls_id from inv_item_transfer_mst a, inv_item_trans_acknowledgement b where a.id=b.challan_id and a.id='$data'");
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('update_id').value 					= '".$row[csf("system_id")]."';\n";
		echo "document.getElementById('cbo_transfer_criteria').value 		= '".$row[csf("transfer_criteria")]."';\n";
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("system_id")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('txt_transfer_sys_no').value 			= '".$row[csf("transfer_system_id")]."';\n";
		echo "document.getElementById('txt_transfer_mst_id').value 			= '".$row[csf("challan_id")]."';\n";
		echo "document.getElementById('txt_transfer_date').value 			= '".change_date_format($row[csf("acknowledg_date")])."';\n";
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
		echo "get_php_form_data('".$from_order_book_id."**"."from"."**".$row[csf("transfer_criteria")]."','populate_data_from_sample','requires/roll_wise_grey_fabric_sample_transfer_acknowledgement_controller');\n";
		echo "get_php_form_data('".$to_order_book_id."**".$row[csf("transfer_criteria")]."','populate_data_to_order','requires/roll_wise_grey_fabric_sample_transfer_acknowledgement_controller');\n";

		echo "$('#cbo_transfer_criteria').attr('disabled','disabled');\n";
		echo "$('#cbo_company_id').attr('disabled','disabled');\n";
		echo "$('#txt_from_order_book_no').attr('disabled','disabled');\n";
		echo "$('#txt_to_order_book_no').attr('disabled','disabled');\n";
		echo "$('#txt_transfer_sys_no').attr('disabled','disabled');\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_grey_transfer_entry',1,1);\n"; 
		exit();
	} // +"'**'"+'".$row[csf("transfer_criteria")]."'
}

//data save update delete here------------------------------//
if($action=="save_update_delete")
{	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
    for($k=1;$k<=$total_row;$k++)
    { 
        $productId="productId_".$k;
        $prod_ids.=$$productId.",";
    }
    $prod_ids=implode(",",array_unique(explode(",",chop($prod_ids,','))));
    $max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id in ($prod_ids) and transaction_type in (1,4,5)", "max_date");      
	$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
	$trans_date = date("Y-m-d", strtotime(str_replace("'","",$txt_transfer_date)));
	if ($trans_date < $max_recv_date) 
    {
        echo "20**Transfer Date Can not Be Less Than Last Receive Date Of These Lot";
        die;
	}

	/*$is_acknowledge=return_field_value("is_acknowledge","inv_item_transfer_mst","id=$txt_transfer_mst_id");
	if($is_acknowledge==1)
	{
		echo "20**Acknowledgement against this challan is already done";die;
	}*/
	
	$cbo_transfer_criteria=str_replace("'","",$cbo_transfer_criteria);
	if($cbo_transfer_criteria==8) // Sample to Sample
	{
		$entry_form_no = 180;
		$short_prefix_name="GFSTSTE";
	}
	elseif($cbo_transfer_criteria==7) // Sample to Order
	{
		$entry_form_no = 183;
		$short_prefix_name="GFSTOTE";
	}
	else{
		$entry_form_no = 110; // // Order to Sample
		$short_prefix_name="GFOTSTE";
	}
    //echo "10**".$entry_form_no;die;
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$transfer_recv_num=''; $transfer_update_id='';
		//echo "10**";
		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later

			$id = return_next_id_by_sequence("INV_ITEM_TRANS_MST_AC_PK_SEQ", "inv_item_trans_acknowledgement", $con);
			$field_array="id, entry_form, challan_id, company_id, transfer_criteria, item_category, acknowledg_date, inserted_by, insert_date";

			$data_array="(".$id.",".$entry_form_no.",".$txt_transfer_mst_id.",".$cbo_company_id.",".$cbo_transfer_criteria.",13,".$txt_transfer_date.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			//echo "10**insert into inv_item_transfer_mst (".$field_array.") values ".$data_array;die;

			$transfer_recv_num=$txt_transfer_mst_id;
			$transfer_update_id=$id;
		}
		else
		{
			$field_array="acknowledg_date*updated_by*update_date";
			$data_array="".$txt_transfer_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$id=str_replace("'","",$update_id);
			
			$transfer_recv_num=str_replace("'","",$txt_transfer_mst_id);
			$transfer_update_id=str_replace("'","",$update_id);
		}

		//echo "10**Check";die;
		
		$rate=0; $amount=0;
		//$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
		$field_array_trans="id, mst_id, company_id, prod_id, item_category, transaction_type, transaction_date, order_id, cons_uom, cons_quantity, cons_rate, cons_amount, floor_id, room, rack, self, program_no, stitch_length, store_id, inserted_by, insert_date, body_part_id";
		
		//$id_prop = return_next_id( "id", "order_wise_pro_details", 1 );
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, inserted_by, insert_date";

		$field_array_dtls_update = "to_trans_id*to_store*to_floor_id*to_room*to_rack*to_shelf*updated_by*update_date";
		$field_array_prod_update = "current_stock*avg_rate_per_unit*stock_value*updated_by*update_date";
		
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
			$floor="floor_".$j;
			$room="room_".$j;
			$rack="rack_".$j;
			$shelf="shelf_".$j;
			$rollNo="rollNo_".$j;
			$transRollId="transRollId_".$j;
			$storeId="storeId_".$j;
			$requiDtlsId="requiDtlsId_".$j;
			$transIdFrom="transIdFrom_".$j;			
			$dtlsId="dtlsId_".$j;
			
			$rollIds.=$$transRollId.",";
			$transDtlsId.=$$dtlsId.",";
			
			$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);			
			
			if($data_array_trans!="") $data_array_trans.=",";
			$recv_trans_id = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			$data_array_trans.="(".$recv_trans_id.",".$txt_transfer_mst_id.",".$cbo_company_id.",".$$productId.",13,5,".$txt_transfer_date.",".$txt_to_order_book_id.",12,".$$rollWgt.",".$rate.",".$amount.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$$progId.",".$$stichLn.",".$cbo_store_name_to.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_to_body_part.")";

			
			if ($cbo_transfer_criteria==7) // Sample to Order
			{
				if($data_array_prop!="") $data_array_prop.= ",";
				$data_array_prop.="(".$id_prop.",".$$transIdFrom.",5,183,".$$dtlsId.",".$txt_to_order_book_id.",".$$productId.",".$$rollWgt.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}


			$dtls_id_array[]=str_replace("'", "", $$dtlsId);
			$data_array_dtls_update[str_replace("'", "", $$dtlsId)]=explode("*",("".$recv_trans_id."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));


			$product_qnty_update[str_replace("'", "",$$productId)]['qnty']+= str_replace("'", "", $$rollWgt);

			
			$all_trans_roll_id.=$$transRollId.",";

			$all_prod_id.=$$productId.",";
		}

		if(str_replace("'", "", $cbo_company_id_from) != str_replace("'", "", $cbo_company_id))
		{
			$all_prod_id = chop($all_prod_id,",");
			$all_prod_id= array_filter(array_unique(explode(",", $all_prod_id)));
			if(!empty($all_prod_id))
			{
				$prod_id_array=array();
				$up_to_prod_ids=implode(",",$all_prod_id);
				
				//echo "10**select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id in($up_to_prod_ids) ";die;
				$toProdResult=sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id in($up_to_prod_ids) ");
				foreach($toProdResult as $row)
				{
					$stock_qnty = $product_qnty_update[$row[csf("id")]]['qnty'] + $row[csf("current_stock")];
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
			}
		}


		if(str_replace("'","",$update_id)=="")
		{
			$rID=sql_insert("inv_item_trans_acknowledgement",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;
		}
		else
		{
			$rID=sql_update("inv_item_trans_acknowledgement",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0; 
		}
		//echo "10**insert into inv_item_trans_acknowledgement (".$field_array.") values ".$data_array;die;

		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		}
		//echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;

		$field_array_mst_update = "is_acknowledge";
		$data_array_mst_update  = "1";

		$rID3=sql_update("inv_item_transfer_mst",$field_array_mst_update,$data_array_mst_update,"id",$txt_transfer_mst_id,0);
		if($rID3) $flag=1; else $flag=0; 


		//echo "10**".bulk_update_sql_statement( "inv_item_transfer_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $dtls_id_array );die;
		$rID4=execute_query(bulk_update_sql_statement( "inv_item_transfer_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $dtls_id_array ));
		if($flag==1)
		{
			if($rID4) $flag=1; else $flag=0; 
		}
		
		
		if(!empty($data_array_prod_update))
		{
			//echo "10**".bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array );die;
			$prodUpdate=execute_query(bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array ));
			if($flag==1) 
			{
				if($prodUpdate) $flag=1; else $flag=0; 
			}
		}
		

		$rollIds=chop($rollIds,',');
		$rID5=sql_multirow_update("pro_roll_details","re_transfer","0","id",$rollIds,0);
		if($flag==1) 
		{
			if($rID5) $flag=1; else $flag=0; 
		}
		
		if ($cbo_transfer_criteria==7) 
		{
			$rID6=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1);
			if($flag==1) 
			{
				if($rID6) $flag=1; else $flag=0; 
			}
		}

		//echo "10**$flag##$rID##$rID2##$rID3##$rID4##$rID5##$rID6";die;

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$txt_transfer_mst_id."**".$transfer_update_id."**0";
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
				echo "0**".$txt_transfer_mst_id."**".$transfer_update_id."**0";
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

		$field_array_update="acknowledg_date*updated_by*update_date";
		$data_array_update="".$txt_transfer_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$rID=sql_update("inv_item_trans_acknowledgement",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0;

		//echo "10**".$rID."**".$update_id."**".$txt_transfer_mst_id;die;
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$txt_transfer_mst_id)."**".str_replace("'","",$update_id)."**0";
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
				echo "1**".str_replace("'","",$txt_transfer_mst_id)."**".str_replace("'","",$update_id)."**0";
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

if ($action=="grey_fabric_order_to_order_transfer_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data); die();
	$cbo_transfer_criteria=$data[3];	

	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=13","id","product_name_details");
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
				$entry_form_no = 180;
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
				$entry_form_no = 183;
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
				$entry_form_no = 110;
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

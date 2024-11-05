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
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_order_no').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $type; ?>', 'create_po_search_list_view', 'search_div', 'yarn_order_to_order_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year,"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year,";
	else $year_field="";//defined Later
	
	if($type=="from") $status_cond=" and b.status_active in(1,3)"; else $status_cond=" and b.status_active=1";
	$sql= "select a.job_no_prefix_num, $year_field a.job_no,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_id and a.buyer_name like '$buyer' and b.po_number like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $status_cond $shipment_date order by b.id, b.pub_shipment_date";  
	
	echo create_list_view("tbl_list_search", "Job No,Year,Company,Buyer Name,Style Ref. No,Job Qty.,PO number,PO Quantity,Shipment Date", "70,60,70,80,120,90,110,90,80","850","200",0, $sql , "js_set_value", "id", "", 1, "0,0,company_name,buyer_name,0,0,0,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,job_quantity,po_number,po_quantity,shipment_date", "","",'0,0,0,0,0,1,0,1,3');
	
	exit();
}

if($action=='populate_data_from_order')
{
	$data=explode("**",$data);
	$po_id=$data[0];
	$which_order=$data[1];
	
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

if($action=='populate_data_from_item_stock')
{
	$data=explode("**",$data);
	$po_id=$data[0];
	$prod_id=$data[1];
	
	$data_array=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$po_id");
	foreach ($data_array as $row)
		{ }
	
	$dataArray=sql_select("select 
		sum(CASE WHEN entry_form ='3' and trans_type=2 THEN quantity ELSE 0 END) AS iss_qnty_yarn,
		sum(CASE WHEN entry_form ='9' and trans_type=4 THEN quantity ELSE 0 END) AS return_qnty,
		sum(CASE WHEN entry_form ='11' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty,
		sum(CASE WHEN entry_form ='11' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty
		from order_wise_pro_details where status_active=1 and is_deleted=0 and po_breakdown_id='$po_id' and prod_id='$prod_id' and entry_form in(3,9,11)");
	
	$tot_issued_qty=$dataArray[0][csf('iss_qnty_yarn')]-$dataArray[0][csf('return_qnty')]+$dataArray[0][csf('transfer_in_qnty')];
	$tot_transfer_qty=$dataArray[0][csf('transfer_out_qnty')];
	$transferable_qnty=$tot_issued_qty-$tot_transfer_qty;
	
	echo "document.getElementById('txt_cum_issue_qnty').value 				= '".$tot_issued_qty."';\n";
	echo "document.getElementById('txt_tot_transfer_qnty').value 			= '".$tot_transfer_qty."';\n";
	echo "document.getElementById('txt_transferable_qnty').value 			= '".$transferable_qnty."';\n";

	exit();
	
}

if($action=="load_drop_down_item_desc")
{
	$item_description=array();
	$sql="select a.id, a.product_name_details from product_details_master a, order_wise_pro_details b where a.id=b.prod_id and b.po_breakdown_id=$data and b.entry_form=3 and b.trans_type=2 and b.status_active=1 and b.is_deleted=0";
	$dataArray=sql_select($sql);	
	foreach($dataArray as $row)
	{
		$item_description[$row[csf('id')]]=$row[csf('product_name_details')];
	}
	
	echo create_drop_down( "cbo_item_desc", 250, $item_description,'', 1, "--Select Item Description--",'0','load_item_stock_data(this.value);','');  
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
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>, 'create_transfer_search_list_view', 'search_div', 'yarn_order_to_order_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
	$search_string=trim($data[0]);
	$search_by=$data[1];
	$company_id =$data[2];
	
	if($search_by==1)
		$search_field="transfer_prefix_number";	
	else
		$search_field="challan_no";
	
	if($db_type==0) $year_field="YEAR(insert_date) as year,"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year,";
	else $year_field="";//defined Later
	
	$sql="select id, transfer_prefix_number, $year_field transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria, item_category from inv_item_transfer_mst where item_category=1 and company_id=$company_id and $search_field like '$search_string' and transfer_criteria=4 and status_active=1 and is_deleted=0 order by id";
	
	$arr=array(3=>$company_arr,5=>$item_transfer_criteria,6=>$item_category);
	
	echo  create_list_view("tbl_list_search", "Transfer ID,Year,Challan No,Company,Transfer Date,Transfer Criteria,Item Category", "80,70,100,110,90,140","760","250",0, $sql, "js_set_value", "id", "", 1, "0,0,0,company_id,0,transfer_criteria,item_category", $arr, "transfer_prefix_number,year,challan_no,company_id,transfer_date,transfer_criteria,item_category", '','','0,0,0,0,3,0,0');
	
	exit();
}

if($action=='populate_data_from_transfer_master')
{
	$data_array=sql_select("select transfer_system_id, challan_no, company_id, transfer_date, item_category, from_order_id,to_order_id from inv_item_transfer_mst where id='$data'");
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('update_id').value 					= '".$data."';\n";
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("transfer_system_id")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_transfer_date').value 			= '".change_date_format($row[csf("transfer_date")])."';\n";
		
		echo "get_php_form_data('".$row[csf("from_order_id")]."**from'".",'populate_data_from_order','requires/yarn_order_to_order_transfer_controller');\n";
		echo "get_php_form_data('".$row[csf("to_order_id")]."**to'".",'populate_data_from_order','requires/yarn_order_to_order_transfer_controller');\n";
		
		echo "$('#cbo_company_id').attr('disabled','disabled');\n";		
		echo "$('#txt_from_order_no').attr('disabled','disabled');\n";
		echo "$('#txt_to_order_no').attr('disabled','disabled');\n";
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_yarn_transfer_entry',1,1);\n"; 
		
		exit();
	}
}

if($action=="show_transfer_listview")
{
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=1","id","product_name_details");
	
	$sql="select id, from_prod_id, transfer_qnty, item_category, uom from inv_item_transfer_dtls where mst_id='$data' and status_active = '1' and is_deleted = '0'";
	
	$arr=array(0=>$item_category,1=>$product_arr,3=>$unit_of_measurement);
	
	echo create_list_view("list_view", "Item Category,Item Description,Transfered Qnty,UOM", "130,350,140","750","200",0, $sql, "get_php_form_data", "id", "'populate_transfer_details_form_data'", 0, "item_category,from_prod_id,0,uom", $arr, "item_category,from_prod_id,transfer_qnty,uom", "requires/yarn_order_to_order_transfer_controller",'','0,0,2,0');
	
	exit();
}

if($action=='populate_transfer_details_form_data')
{
	$data_array=sql_select("select id, mst_id, from_prod_id, transfer_qnty, item_category, uom from inv_item_transfer_dtls where id='$data'");
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('update_dtls_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_item_desc').value 				= '".$row[csf("from_prod_id")]."';\n";
		echo "document.getElementById('txt_transfer_qnty').value 			= '".$row[csf("transfer_qnty")]."';\n";
		echo "document.getElementById('hide_transfer_qnty').value 			= '".$row[csf("transfer_qnty")]."';\n";
		echo "document.getElementById('cbo_item_category').value 			= '".$row[csf("item_category")]."';\n";
		echo "document.getElementById('cbo_uom').value 						= '".$row[csf("uom")]."';\n";
		
		//$sql_trans=sql_select("select id, transaction_type from inv_transaction where mst_id=".$row[csf('mst_id')]." and item_category=1 and transaction_type in(5,6) order by id asc");
		$sql_trans=sql_select("select trans_id from order_wise_pro_details where dtls_id=".$row[csf('id')]." and entry_form=11 and trans_type in(5,6) order by trans_type DESC");
		echo "document.getElementById('update_trans_issue_id').value 		= '".$sql_trans[0][csf("trans_id")]."';\n";
		echo "document.getElementById('update_trans_recv_id').value 		= '".$sql_trans[1][csf("trans_id")]."';\n";
		
		echo "load_item_stock_data('".$row[csf("from_prod_id")]."');\n"; 
		
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
						<th width="100">Issued</th>
						<th width="100">Issue Return</th>
						<th width="100">Transfer Out</th>
						<th width="100">Transfer In</th>
						<?
						if($type=="from")
						{ 
							?>
							<th width="100">Knitted</th>
							<th>Remaining</th>
							<?
						}
						else
						{
							?>
							<th width="100">Shortage</th>
							<th>Knitted</th>
							<?	
						}
						?>
						
					</thead>
					<?
					$req_qty=return_field_value("sum(b.grey_fab_qnty) as grey_req_qnty","wo_booking_mst a, wo_booking_dtls b","a.booking_no=b.booking_no and a.item_category in(2,13) and b.po_break_down_id=$txt_order_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1","grey_req_qnty");
					
					$sql="select 
					sum(CASE WHEN entry_form ='3' THEN quantity ELSE 0 END) AS issue_qnty,
					sum(CASE WHEN entry_form ='9' THEN quantity ELSE 0 END) AS return_qnty,
					sum(CASE WHEN entry_form ='11' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty,
					sum(CASE WHEN entry_form ='11' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty,
					sum(CASE WHEN trans_id<>0 and entry_form in(2,22) THEN quantity ELSE 0 END) AS knit_qnty
					from order_wise_pro_details where po_breakdown_id=$txt_order_id and status_active=1 and is_deleted=0";
					$dataArray=sql_select($sql);
					$remaining=0; $shoratge=0;
					?>
					<tr bgcolor="#EFEFEF">
						<td>1</td>
						<td align="right"><? echo number_format($req_qty,2); ?>&nbsp;</td>
						<td align="right"><? echo number_format($dataArray[0][csf('issue_qnty')],2); ?>&nbsp;</td>
						<td align="right"><? echo number_format($dataArray[0][csf('return_qnty')],2); ?></td>
						<td align="right"><? echo number_format($dataArray[0][csf('transfer_out_qnty')],2); ?>&nbsp;</td>
						<td align="right"><? echo number_format($dataArray[0][csf('transfer_in_qnty')],2); ?></td>
						<?
						if($type=="from")
						{
							$remaining=$dataArray[0][csf('issue_qnty')]-$dataArray[0][csf('return_qnty')]-$dataArray[0][csf('transfer_out_qnty')]+$dataArray[0][csf('transfer_in_qnty')]-$dataArray[0][csf('knit_qnty')];
							?>
							<td align="right"><? echo number_format($dataArray[0][csf('knit_qnty')],2); ?>&nbsp;</td>
							<td align="right"><? echo number_format($remaining,2); ?>&nbsp;</td>
							<?
						}
						else
						{
							$shoratge=$req_qty-$dataArray[0][csf('issue_qnty')]+$dataArray[0][csf('return_qnty')]+$dataArray[0][csf('transfer_out_qnty')]-$dataArray[0][csf('transfer_in_qnty')];
							?>
							<td align="right"><? echo number_format($shoratge,2); ?>&nbsp;</td>
							<td align="right"><? echo number_format($dataArray[0][csf('knit_qnty')],2); ?>&nbsp;</td>
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
	
        //----------------Check Last Receive Date for Transfer Out----------------
	$is_update_cond_for_iss = ($operation==1)? " and id <> $update_trans_recv_id ": "";
	$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$cbo_item_desc and transaction_type in (1,4,5) $is_update_cond_for_iss and status_active = 1", "max_date");      
	if($max_recv_date !="")
	{
		$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
		$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));

		if ($transfer_date < $max_recv_date) 
		{
			echo "20**Transfer Out Date Can not Be Less Than Last Receive Date Of This Lot";
			die;
		}
	}
        //-----------------Check Last issue date for Transfer In-----------------
	$is_update_cond_for_rcv = ($operation==1)? " and id not in ( $update_trans_recv_id , $update_trans_issue_id ) ": "";
	$max_transaction_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$cbo_item_desc $is_update_cond_for_rcv and status_active = 1", "max_date");      
	if($max_issue_date != "")
	{
		$max_issue_date = date("Y-m-d", strtotime($max_issue_date));
		$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
		if ($transfer_date < $max_issue_date) 
		{
			echo "20**Transfer In Date Can not Be Less Than Last Transaction Date Of This Lot";
                //check_table_status($_SESSION['menu_id'], 0);
			disconnect($con);
			die;
		}
	} 
	
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		$transfer_recv_num=''; $transfer_update_id='';
		
		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
			
			//$new_transfer_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'YOTOTE', date("Y",time()), 5, "select transfer_prefix, transfer_prefix_number from inv_item_transfer_mst where company_id=$cbo_company_id and transfer_criteria=4 and item_category='1' and $year_cond=".date('Y',time())." order by id desc ", "transfer_prefix", "transfer_prefix_number" ));

			$new_transfer_system_id = explode("*", return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ","inv_item_transfer_mst",$con,1,$cbo_company_id,'YOTOTE',11,date("Y",time()),1 ));

			$id = return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst", $con);
			
			$field_array="id, transfer_prefix, transfer_prefix_number, transfer_system_id, company_id, challan_no, transfer_date, transfer_criteria, to_company, from_order_id, to_order_id, item_category, inserted_by, insert_date,entry_form";
			
			$data_array="(".$id.",'".$new_transfer_system_id[1]."',".$new_transfer_system_id[2].",'".$new_transfer_system_id[0]."',".$cbo_company_id.",".$txt_challan_no.",".$txt_transfer_date.",4,0,".$txt_from_order_id.",".$txt_to_order_id.",".$cbo_item_category.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',11)";
			
			//echo "insert into inv_item_transfer_mst (".$field_array.") values ".$data_array;die;
			/*$rID=sql_insert("inv_item_transfer_mst",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;*/
			
			$transfer_recv_num=$new_transfer_system_id[0];
			$transfer_update_id=$id;
		}
		else
		{
			$field_array_update="challan_no*transfer_date*from_order_id*to_order_id*updated_by*update_date";
			$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$txt_from_order_id."*".$txt_to_order_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			/*$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0;*/ 
			
			$transfer_recv_num=str_replace("'","",$txt_system_id);
			$transfer_update_id=str_replace("'","",$update_id);
		}
		
		/******** original product id check start ********/
		
		$origin_prod_id=return_field_value("origin_prod_id","inv_transaction","prod_id=$cbo_item_desc and status_active=1 and transaction_type=2","origin_prod_id");
		
		/******** original product id check end ********/
		
		$rate=return_field_value("avg_rate_per_unit","product_details_master","id=$cbo_item_desc");
		$amount=str_replace("'","",$txt_transfer_qnty)*$rate;
		
		$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		$field_array_trans="id, mst_id, company_id, prod_id, origin_prod_id, item_category, transaction_type, transaction_date, order_id, cons_uom, cons_quantity, cons_rate, cons_amount, inserted_by, insert_date";
		
		$data_array_trans="(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",".$cbo_item_desc.",'".$origin_prod_id."',".$cbo_item_category.",6,".$txt_transfer_date.",".$txt_from_order_id.",".$cbo_uom.",".$txt_transfer_qnty.",'".$rate."','".$amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		//$id_trans_iss=$id_trans+1;
		$id_trans_iss = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		$data_array_trans.=",(".$id_trans_iss.",".$transfer_update_id.",".$cbo_company_id.",".$cbo_item_desc.",'".$origin_prod_id."',".$cbo_item_category.",5,".$txt_transfer_date.",".$txt_to_order_id.",".$cbo_uom.",".$txt_transfer_qnty.",'".$rate."','".$amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		//echo "insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;
		/*$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} */
		
		$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
		$field_array_dtls="id, mst_id, from_prod_id, item_category, transfer_qnty, rate, transfer_value, uom, inserted_by, insert_date";
		
		$data_array_dtls="(".$id_dtls.",".$transfer_update_id.",".$cbo_item_desc.",".$cbo_item_category.",".$txt_transfer_qnty.",'".$rate."','".$amount."',".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		/*//echo "insert into inv_item_transfer_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID3=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} */
		
		$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, inserted_by, insert_date";
		
		$data_array_prop="(".$id_prop.",".$id_trans.",6,11,".$id_dtls.",".$txt_from_order_id.",".$cbo_item_desc.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		//$id_prop=$id_prop+1;
		$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
		$data_array_prop.=",(".$id_prop.",".$id_trans_iss.",5,11,".$id_dtls.",".$txt_to_order_id.",".$cbo_item_desc.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		//echo $data_array;
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
		//echo "insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;
		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
		//echo "insert into inv_item_transfer_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
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
		
		//echo $flag;die;
		
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
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
		
	}	
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}

		/*#### Stop not eligible field from update operation start ####*/
		// from_order_id*to_order_id*
		// $txt_from_order_id."*".$txt_to_order_id."*".
		/*#### Stop not eligible field from update operation end ####*/
		
		$field_array_update="challan_no*transfer_date*updated_by*update_date";
		$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		/*$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0;*/
		
		/******** original product id check start ********/
		
		$origin_prod_id=return_field_value("origin_prod_id","inv_transaction","prod_id=$cbo_item_desc and status_active=1 and transaction_type=2","origin_prod_id");
		
		/******** original product id check end ********/
		
		$field_array_trans="prod_id*origin_prod_id*transaction_date*order_id*cons_uom*cons_quantity*cons_rate*cons_amount*updated_by*update_date";
		$updateTransID_array=array();
		$update_trans_recv_id=str_replace("'","",$update_trans_recv_id); 
		$update_trans_issue_id=str_replace("'","",$update_trans_issue_id); 
		
		$rate=return_field_value("avg_rate_per_unit","product_details_master","id=$cbo_item_desc");
		$amount=str_replace("'","",$txt_transfer_qnty)*$rate;
		
		$updateTransID_array[]=$update_trans_recv_id; 
		$updateTransID_data[$update_trans_recv_id]=explode("*",("".$cbo_item_desc."*'".$origin_prod_id."'*".$txt_transfer_date."*".$txt_from_order_id."*".$cbo_uom."*".$txt_transfer_qnty."*'".$rate."'*'".$amount."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		
		$updateTransID_array[]=$update_trans_issue_id; 
		$updateTransID_data[$update_trans_issue_id]=explode("*",("".$cbo_item_desc."*'".$origin_prod_id."'*".$txt_transfer_date."*".$txt_to_order_id."*".$cbo_uom."*".$txt_transfer_qnty."*'".$rate."'*'".$amount."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		
		/*$rID2=execute_query(bulk_update_sql_statement("inv_transaction","id",$field_array_trans,$updateTransID_data,$updateTransID_array));
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		}*/
		
		$field_array_dtls="from_prod_id*transfer_qnty*rate*transfer_value*uom*updated_by*update_date";
		$data_array_dtls=$cbo_item_desc."*".$txt_transfer_qnty."*'".$rate."'*'".$amount."'*".$cbo_uom."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		/*$rID3=sql_update("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,"id",$update_dtls_id,1);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		}
		
		$query = execute_query("DELETE FROM order_wise_pro_details WHERE dtls_id=$update_dtls_id and entry_form=11");
		{
			if($query) $flag=1; else $flag=0; 
		} */
		
		$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, inserted_by, insert_date";
		
		$data_array_prop="(".$id_prop.",".$update_trans_recv_id.",6,11,".$update_dtls_id.",".$txt_from_order_id.",".$cbo_item_desc.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		//$id_prop=$id_prop+1;
		$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
		$data_array_prop.=",(".$id_prop.",".$update_trans_issue_id.",5,11,".$update_dtls_id.",".$txt_to_order_id.",".$cbo_item_desc.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
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
		$query = execute_query("DELETE FROM order_wise_pro_details WHERE dtls_id=$update_dtls_id and entry_form=11");
		{
			if($query) $flag=1; else $flag=0; 
		} 
		//echo "insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;
		$rID4=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1);
		if($flag==1) 
		{
			if($rID4) $flag=1; else $flag=0; 
		} 
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
		//check_table_status( $_SESSION['menu_id'],0);	
		disconnect($con);
		die;
	}
}

if ($action=="yarn_order_to_order_transfer_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	
	$sql="select id, transfer_system_id, transfer_date, challan_no, from_order_id, to_order_id, item_category from inv_item_transfer_mst a where id='$data[1]' and company_id='$data[0]' and item_category=1";
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
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=1","id","product_name_details");
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
					$sql_dtls="select id, item_category, item_group, from_prod_id, transfer_qnty, uom from inv_item_transfer_dtls where mst_id='$data[1]' and item_category=1 and status_active=1 and is_deleted=0";
					
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
				echo signature_table(39, $data[0], "900px");
				?>
			</div>
		</div>   
		<?	
		exit();
	}
	?>
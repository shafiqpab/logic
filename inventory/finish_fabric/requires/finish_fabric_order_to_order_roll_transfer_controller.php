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
		<div align="center" style="width:930px;">
			<form name="searchdescfrm"  id="searchdescfrm">
				<fieldset style="width:930px;">
					<legend>Enter search words</legend>
					<table cellpadding="0" cellspacing="0" width="850" class="rpt_table" border="1" rules="all">
						<thead>
							<th>Buyer Name</th>
							<th>Order No</th>
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
								echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",$data[0] );
								?>
							</td>
							<td>
								<input type="text" style="width:110px;" class="text_boxes" name="txt_order_no" id="txt_order_no" placeholder="Enter Order No" />
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
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_order_no').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $type; ?>'+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_ref_no').value, 'create_po_search_list_view', 'search_div', 'finish_fabric_order_to_order_roll_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:80px;" />
							</td>
						</tr>
						<tr>
							<td colspan="6" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
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
	$file_no=$data[6];
	$ref_no=$data[7];
	
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
	$arr=array(2=>$company_arr,3=>$buyer_arr);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	$str_cond="";
	if($file_no!="")  $str_cond=" and b.file_no=$file_no";
	if($ref_no!="")  $str_cond.=" and b.grouping like '%$ref_no%'";
	
	if($type=="from") $status_cond=" and b.status_active in(1,3)"; else $status_cond=" and b.status_active=1";
	$sql= "select a.job_no, $year_field, a.job_no_prefix_num,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date, b.file_no, b.grouping as ref_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_id and a.buyer_name like '$buyer' and b.po_number like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $status_cond $shipment_date $str_cond order by b.id, b.pub_shipment_date";  

	echo create_list_view("tbl_list_search", "Job No,Year,Company,Buyer Name,Style Ref. No,Job Qty.,File No,Ref. No,PO number,PO Quantity,Shipment Date", "50,40,60,80,110,80,70,90,110,80","910","200",0, $sql , "js_set_value", "id", "", 1, "0,0,company_name,buyer_name,0,0,0,0,0,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,job_quantity,file_no,ref_no,po_number,po_quantity,shipment_date", "",'','0,0,0,0,0,1,0,0,0,1,3');
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

if($action=="show_dtls_list_view")
{
	$batch_arr=return_library_array( "select a.id, a.batch_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and b.po_id=$data",'id','batch_no');
	$machine_arr=return_library_array( "select id, machine_no from lib_machine_name",'id','machine_no');	
	
	$prod_sql=sql_select("select id, product_name_details, detarmination_id, gsm, dia_width from product_details_master where item_category_id=2");
	foreach($prod_sql as $row)
	{
		$product_arr[$row[csf("id")]]["product_name_details"]=$row[csf("product_name_details")];
		$product_arr[$row[csf("id")]]["detarmination_id"]=$row[csf("detarmination_id")];
		$product_arr[$row[csf("id")]]["gsm"]=$row[csf("gsm")];
		$product_arr[$row[csf("id")]]["dia_width"]=$row[csf("dia_width")];
	}
	
	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	
	//$issued_barcode_arr=return_library_array("select barcode_no,reprocess from pro_roll_details where entry_form=71 and status_active=1 and is_deleted=0 and po_breakdown_id=$data and is_returned<>1","barcode_no", "barcode_no");

	$scanned_barcode_sql=sql_select("select barcode_no,prev_reprocess from pro_roll_details where entry_form=71 and status_active=1 and is_deleted=0 and po_breakdown_id=$data and is_returned<>1");
	foreach($scanned_barcode_sql as $row)
	{
		$issued_barcode_arr[$row[csf("barcode_no")]][$row[csf("prev_reprocess")]]=$row[csf("barcode_no")] ;
	}
	unset( $scanned_barcode_sql);
	
	
	
	
	/*	$sql="select a.id, a.entry_form, a.receive_basis, a.booking_id, b.prod_id, b.shelf_no as self,b.gsm,b.machine_no_id,c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 1 as type,c.booking_no
	from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0  and c.is_transfer!=6 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=4207
	union all
	select a.id, a.entry_form, 0 as receive_basis, 0 as booking_id, b.from_prod_id as prod_id, b.to_shelf as self,b.gsm,b.machine_no_id, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 2 as type ,c.booking_no
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c 
	WHERE a.id=b.mst_id and b.id=c.dtls_id  and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=4207
	order by barcode_no";
	*/
	
	
	
	
	$sql="select a.id, a.entry_form, a.receive_basis, a.booking_id, b.color_id,b.batch_id, b.prod_id, b.shelf_no as self, b.gsm, b.machine_no_id, c.id as roll_tbl_id, c.roll_id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qc_pass_qnty as qnty, c.roll_id as roll_id_prev, 1 as type, c.booking_no,c.reprocess
	from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(7,37,68) and c.entry_form in(7,37,68) and c.reprocess=0 and c.is_transfer!=6 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=$data
	union all
	select a.id, a.entry_form, a.receive_basis, a.booking_id, b.color_id,b.batch_id, b.prod_id, b.shelf_no as self, b.gsm, b.machine_no_id, c.id as roll_tbl_id, c.roll_id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qc_pass_qnty as qnty, c.roll_id as roll_id_prev, 1 as type, c.booking_no,c.reprocess
	from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(7,37,68) and c.entry_form in(7,37,68) and c.reprocess>0 and c.is_transfer!=6 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=$data
	union all
	select a.id, a.entry_form, 0 as receive_basis, 0 as booking_id, 0 as batch_id, b.color_id,b.from_prod_id as prod_id, b.to_shelf as self, b.gsm, b.machine_no_id, c.id as roll_tbl_id, c.roll_id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qc_pass_qnty as qnty, c.roll_id as roll_id_prev, 2 as type , c.booking_no,c.reprocess
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(134) and c.entry_form in(134) and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=$data
	order by barcode_no";

	//echo $sql;die;

	$data_array=sql_select($sql);	
	$i=1;
	foreach($data_array as $row)
	{  
		if($issued_barcode_arr[$row[csf('barcode_no')]][$row[csf('reprocess')]]=="")
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

			$transRollId=$row[csf('roll_tbl_id')];
			$program_no='';
			if($row[csf('entry_form')]==68)
			{
				$row[csf('roll_id')]=$row[csf('roll_id')];
			}
			else if($row[csf('entry_form')]==134)
			{
				$row[csf('roll_id')]=$row[csf('roll_id')];
			}
			else 
			{
				$row[csf('roll_id')]=$row[csf('roll_tbl_id')];
			}
			$program_no=$programArr[$row[csf('barcode_no')]];
			$prod_name_dtls=$constructtion_arr[$product_arr[$row[csf('prod_id')]]["detarmination_id"]]." ".$composition_arr[$product_arr[$row[csf('prod_id')]]["detarmination_id"]]." ".$product_arr[$row[csf('prod_id')]]["gsm"]." ".$product_arr[$row[csf('prod_id')]]["dia_width"];
			
			?>
			<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
				<td width="30" align="center" valign="middle"><input type="checkbox" id="tbl_<? echo $i;?>" name="check[]" onClick="show_selected_total('<? echo $i;?>')"/></td> 
				<td width="30" align="center"><? echo $i; ?></td>
				<td width="110"><? echo $batch_arr[$row[csf('batch_id')]]; ?></td>
				<td width="100" align="center"><? echo $row[csf('barcode_no')]; ?></td>
				<td width="70" align="center"><? echo $row[csf('roll_no')]; ?></td>
				<td width="80" align="center"><p><? echo $program_no; ?>&nbsp;</p></td>
				<td width="50" align="center"><p><? echo $row[csf('prod_id')]; ?></p></td>
				<td width="220"><p><? echo $prod_name_dtls; ?>&nbsp;</p></td>
				<td width="70"><p><? echo $machine_arr[$row[csf('machine_no_id')]]; ?>&nbsp;</p></td>
				<td width="80" align="center"><p><? echo $row[csf('gsm')]; ?>&nbsp;</p></td>
				<td width="55" align="center"><p><? echo $row[csf('self')]; ?>&nbsp;</p></td>
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
					<input type="hidden" name="rack[]" id="rack_<? echo $i; ?>" value="<? echo $row[csf('rack')]; ?>"/>
					<input type="hidden" name="shelf[]" id="shelf_<? echo $i; ?>" value="<? echo $row[csf('self')]; ?>"/>
					<input type="hidden" name="gsm[]" id="gsm_<? echo $i; ?>" value="<? echo $row[csf('gsm')]; ?>"/>
					<input type="hidden" name="machine_no_id[]" id="machine_no_id_<? echo $i; ?>" value="<? echo $row[csf('machine_no_id')]; ?>"/>
					<input type="hidden" name="batch_id[]" id="batch_id_<? echo $i; ?>" value="<? echo $row[csf('batch_id')]; ?>"/>
					<input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>" value=""/>
					<input type="hidden" name="transIdFrom[]" id="transIdFrom_<? echo $i; ?>" value=""/>
					<input type="hidden" name="transIdTo[]" id="transIdTo_<? echo $i; ?>" value=""/>
					<input type="hidden" name="rolltableId[]" id="rolltableId_<? echo $i; ?>" value=""/>
					<input type="hidden" name="transRollId[]" id="transRollId_<? echo $i; ?>" value="<? echo $transRollId ?>"/>
					<input type="hidden" name="reProcess[]" id="reProcess_<? echo $i; ?>" value="<? echo $row[csf('reprocess')] ?>"/>
					<input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" value="<? echo $row[csf('color_id')] ?>"/>
					<!--   <input type="hidden" name="preReprocess[]" id="preReprocess_<? echo $i; ?>" value="<? echo $row[csf('reprocess')] ?>"/> -->
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
	$batch_arr=return_library_array( "select a.id, a.batch_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and b.po_id=$order_id",'id','batch_no');
	$machine_arr=return_library_array( "select id, machine_no from lib_machine_name",'id','machine_no');
	
	$prod_sql=sql_select("select id, product_name_details, detarmination_id, gsm, dia_width from product_details_master where item_category_id=2");
	foreach($prod_sql as $row)
	{
		$product_arr[$row[csf("id")]]["product_name_details"]=$row[csf("product_name_details")];
		$product_arr[$row[csf("id")]]["detarmination_id"]=$row[csf("detarmination_id")];
		$product_arr[$row[csf("id")]]["gsm"]=$row[csf("gsm")];
		$product_arr[$row[csf("id")]]["dia_width"]=$row[csf("dia_width")];
	}
	
	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	
	$re_trans_arr=return_library_array("select barcode_no from pro_roll_details where entry_form=134 and re_transfer=1 and status_active=1 and is_deleted=0 and mst_id=$mst_id ", "barcode_no", "barcode_no");
	
	
	$transfer_arr=array();
	$transfer_dataArray=sql_select("select a.id, a.trans_id, a.to_trans_id, b.id as roll_id, b.barcode_no from inv_item_transfer_dtls a, pro_roll_details b where a.id=b.dtls_id and a.mst_id=$mst_id and b.mst_id=$mst_id and b.entry_form=134 and b.transfer_criteria=4 and b.status_active=1 and b.is_deleted=0");
	foreach($transfer_dataArray as $row)
	{
		$transfer_arr[$row[csf('barcode_no')]]['dtls_id']=$row[csf('id')];
		$transfer_arr[$row[csf('barcode_no')]]['from_trans_id']=$row[csf('trans_id')];
		$transfer_arr[$row[csf('barcode_no')]]['to_trans_id']=$row[csf('to_trans_id')];
		$transfer_arr[$row[csf('barcode_no')]]['rolltableId']=$row[csf('roll_id')];
	}
	
	
	
	$sql="select a.id, a.entry_form, 0 as receive_basis, 0 as booking_id, b.id as dtls_id, b.trans_id, b.to_trans_id, b.batch_id, b.from_prod_id as prod_id, b.to_shelf as self, b.gsm, b.machine_no_id, b.yarn_lot, b.y_count as yarn_count, b.stitch_length, b.brand_id, b.to_rack as rack, b.from_program,b.color_id, c.id as roll_tbl_id, c.roll_id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.from_roll_id as from_roll_id, 3 as type ,c.reprocess
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(134) and c.entry_form in(134) and c.status_active=1 and c.is_deleted=0 and c.mst_id=$mst_id
	order by barcode_no";
	
	//echo $sql;
	
	$data_array=sql_select($sql);	
	$i=1;
	foreach($data_array as $row)
	{  
		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		
		/*if($row[csf('entry_form')]==2)
		{
			if($row[csf('receive_basis')]==2) $program_no=$row[csf('booking_id')];
		}
		else if($row[csf('entry_form')]==68)
		{
			$program_no=$programArr[$delv_arr[$row[csf('barcode_no')]]];
			$row[csf('roll_id')]=$row[csf('roll_id_prev')];
		}
		else if($row[csf('entry_form')]==134)
		{
			$program_no=$programArr[$trans_arr[$row[csf('barcode_no')]]];
			$row[csf('roll_id')]=$row[csf('roll_id_prev')];
		}*/
		
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
		
		
		$program_no=$row[csf('from_program')];
		$dtls_id=$row[csf('dtls_id')];
		$from_trans_id=$row[csf('trans_id')];
		$to_trans_id=$row[csf('to_trans_id')];
		$rolltableId=$row[csf('roll_tbl_id')];
		$transRollId=$row[csf('from_roll_id')];
		
		$prod_name_dtls=$constructtion_arr[$product_arr[$row[csf('prod_id')]]["detarmination_id"]]." ".$composition_arr[$product_arr[$row[csf('prod_id')]]["detarmination_id"]]." ".$product_arr[$row[csf('prod_id')]]["gsm"]." ".$product_arr[$row[csf('prod_id')]]["dia_width"];
		?>
		<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
			<td width="30" align="center" valign="middle"><input type="checkbox" id="tbl_<? echo $i;?>" <? echo $checked." ".$disabled; ?> name="check[]" onClick="show_selected_total('<? echo $i;?>')" /></td>			
			<td width="30"><? echo $i; ?></td>
			<td width="110"><? echo $batch_arr[$row[csf('batch_id')]]; ?></td>
			<td width="100"><? echo $row[csf('barcode_no')]; ?></td>
			<td width="70"><? echo $row[csf('roll_no')]; ?></td>
			<td width="80"><p><? echo $program_no; ?>&nbsp;</p></td>
			<td width="50"><p><? echo $row[csf('prod_id')]; ?></p></td>
			<td width="220"><p><? echo $prod_name_dtls; ?>&nbsp;</p></td>
			<td width="70"><p><? echo $machine_arr[$row[csf('machine_no_id')]]; ?>&nbsp;</p></td>
			<td width="80"><p><? echo $row[csf('gsm')]; ?>&nbsp;</p></td>
			<td width="55"><p><? echo $row[csf('self')]; ?>&nbsp;</p></td>
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
				<input type="hidden" name="rack[]" id="rack_<? echo $i; ?>" value="<? echo $row[csf('rack')]; ?>"/>
				<input type="hidden" name="shelf[]" id="shelf_<? echo $i; ?>" value="<? echo $row[csf('self')]; ?>"/>
				<input type="hidden" name="gsm[]" id="gsm_<? echo $i; ?>" value="<? echo $row[csf('gsm')]; ?>"/>
				<input type="hidden" name="machine_no_id[]" id="machine_no_id_<? echo $i; ?>" value="<? echo $row[csf('machine_no_id')]; ?>"/>
				
				<input type="hidden" name="batch_id[]" id="batch_id_<? echo $i; ?>" value="<? echo $row[csf('batch_id')]; ?>"/>
				<input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>" value="<? echo $dtls_id; ?>"/>
				<input type="hidden" name="transIdFrom[]" id="transIdFrom_<? echo $i; ?>" value="<? echo $from_trans_id; ?>"/>
				<input type="hidden" name="transIdTo[]" id="transIdTo_<? echo $i; ?>" value="<? echo $to_trans_id; ?>"/>
				<input type="hidden" name="rolltableId[]" id="rolltableId_<? echo $i; ?>" value="<? echo $rolltableId; ?>"/>
				<input type="hidden" name="transRollId[]" id="transRollId_<? echo $i; ?>" value="<? echo $transRollId ?>"/>
				<input type="hidden" name="reProcess[]" id="reProcess_<? echo $i; ?>" value="<? echo $row[csf('reprocess')] ?>"/>
				<input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" value="<? echo $row[csf('color_id')] ?>"/>
			</td>
		</tr>
		<? 
		$i++;
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
				<table cellpadding="0" cellspacing="0" width="750" class="rpt_table">
					<thead>
						<th>Search By</th>
						<th id="search_by_td_up">Please Enter Transfer ID</th>
						<th>Date Range</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:80px;" class="formbutton" />
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
							<input type="text" style="width:170px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td >
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" readonly>
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" readonly>
						</td>
						<td>
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_transfer_search_list_view', 'search_div', 'finish_fabric_order_to_order_roll_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:80px;" />
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
	
	if ($data[3]!="" &&  $data[4]!="") 
	{
		if($db_type==0)
		{
			$transfer_date_cond = " and transfer_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$transfer_date_cond = " and transfer_date between '".change_date_format($data[3],'','',1)."' and '".change_date_format($data[4],'','',1)."'";
		}
	}
	else 
		$transfer_date_cond ="";
	
	if($search_by==1)
		$search_field="transfer_prefix_number";	
	else
		$search_field="challan_no";
	
	if($db_type==0) $year_field="YEAR(insert_date) as year,"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year,";
	else $year_field="";//defined Later
	
	$sql="select id, transfer_prefix_number, $year_field transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria, item_category from inv_item_transfer_mst where item_category=2 and company_id=$company_id and $search_field like '$search_string' and transfer_criteria=4 and entry_form=134 and status_active=1 and is_deleted=0 $transfer_date_cond order by id";
	
	$arr=array(3=>$company_arr,5=>$item_transfer_criteria,6=>$item_category);

	echo  create_list_view("tbl_list_search", "Transfer ID,Year,Challan No,Company,Transfer Date,Transfer Criteria,Item Category", "80,70,100,80,90,130","750","250",0, $sql, "js_set_value", "id", "", 1, "0,0,0,company_id,0,transfer_criteria,item_category", $arr, "transfer_prefix_number,year,challan_no,company_id,transfer_date,transfer_criteria,item_category", '','','0,0,0,0,3,0,0');
	
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
		echo "get_php_form_data('".$row[csf("from_order_id")]."**from'".",'populate_data_from_order','requires/finish_fabric_order_to_order_roll_transfer_controller');\n";
		echo "get_php_form_data('".$row[csf("to_order_id")]."**to'".",'populate_data_from_order','requires/finish_fabric_order_to_order_roll_transfer_controller');\n";
		echo "$('#cbo_company_id').attr('disabled','disabled');\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_finish_transfer_entry',1,1);\n"; 
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
	for($j=1;$j<=$total_row;$j++)
	{ 	
		$productId="productId_".$j;   
		$prod_ids .= $$productId.",";
	}
	$prod_ids=implode(",",array_unique(explode(",",chop($prod_ids,","))));
	$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id in ($prod_ids) and transaction_type in (1,4,5)", "max_date");      
	$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
	$transfer_date = date("Y-m-d", strtotime(str_replace("'","",$txt_transfer_date)));
	if ($transfer_date < $max_recv_date) 
	{
		echo "20**Transfer Date Can not Be Less Than Last Receive Date Of This Lot";
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
			
			$new_transfer_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'GFOTOTE', date("Y",time()), 5, "select transfer_prefix, transfer_prefix_number from inv_item_transfer_mst where company_id=$cbo_company_id and entry_form=134 and transfer_criteria=4 and item_category=2 and $year_cond=".date('Y',time())." order by id desc ", "transfer_prefix", "transfer_prefix_number" ));

			$id = return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst", $con);

			$field_array="id, transfer_prefix, transfer_prefix_number, transfer_system_id, company_id, challan_no, transfer_date, entry_form, transfer_criteria, to_company, from_order_id, to_order_id, item_category, inserted_by, insert_date";
			
			$data_array="(".$id.",'".$new_transfer_system_id[1]."',".$new_transfer_system_id[2].",'".$new_transfer_system_id[0]."',".$cbo_company_id.",".$txt_challan_no.",".$txt_transfer_date.",134,4,0,".$txt_from_order_id.",".$txt_to_order_id.",2,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
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
			if($rID) $flag=1; else $flag=0; */
			
			$transfer_recv_num=str_replace("'","",$txt_system_id);
			$transfer_update_id=str_replace("'","",$update_id);
		}
		
		$rate=0; $amount=0;
		$field_array_trans="id, mst_id, company_id, prod_id, item_category, transaction_type, transaction_date, order_id, cons_uom, cons_quantity, cons_rate, cons_amount, rack, self, program_no, stitch_length, machine_id, inserted_by, insert_date";
		
		$field_array_dtls="id, mst_id, trans_id, to_trans_id, from_prod_id, item_category, transfer_qnty, roll, rate, transfer_value, uom, y_count, brand_id, yarn_lot, rack, shelf, to_rack, to_shelf, from_program, to_program, stitch_length, gsm, machine_no_id, batch_id, inserted_by, insert_date,color_id";		
		
		$field_array_roll="id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty,qc_pass_qnty, roll_no, roll_id, from_roll_id, is_transfer, transfer_criteria,reprocess, prev_reprocess , inserted_by, insert_date";
		
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, inserted_by, insert_date,color_id";


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
			$gsm="gsm_".$j;
			$machine_id="machine_no_id_".$j;
			$batch_id="batch_id_".$j;
			$colorId="colorId_".$j;
			
			$rollNo="rollNo_".$j;
			$transRollId="transRollId_".$j;
			$reProcess="reProcess_".$j;
			
			$rollIds.=$$transRollId.",";
			
			if($data_array_trans!="") $data_array_trans.=",";
			
			$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);			
			$data_array_trans.="(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",".$$productId.",2,6,".$txt_transfer_date.",".$txt_from_order_id.",12,".$$rollWgt.",".$rate.",".$amount.",".$$rack.",".$$shelf.",".$$progId.",".$$stichLn.",".$$machine_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$from_trans_id=$id_trans;
			$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			$data_array_trans.=",(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",".$$productId.",2,5,".$txt_transfer_date.",".$txt_to_order_id.",12,".$$rollWgt.",".$rate.",".$amount.",".$$rack.",".$$shelf.",".$$progId.",".$$stichLn.",".$$machine_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);			
			if($data_array_dtls!="") $data_array_dtls.=",";
			$data_array_dtls.="(".$id_dtls.",".$transfer_update_id.",".$from_trans_id.",".$id_trans.",".$$productId.",2,".$$rollWgt.",".$$rollNo.",".$rate.",".$amount.",12,".$$yarnCount.",".$$brandId.",".$$yarnLot.",".$$rack.",".$$shelf.",".$$rack.",".$$shelf.",".$$progId.",".$$progId.",".$$stichLn.",".$$gsm.",".$$machine_id.",".$$batch_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$colorId.")";
			
			$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);			
			if($data_array_roll!="") $data_array_roll.= ",";
			$data_array_roll.="(".$id_roll.",".$$barcodeNo.",".$transfer_update_id.",".$id_dtls.",".$txt_to_order_id.",134,".$$rollWgt.",".$$rollWgt.",".$$rollNo.",".$$rollId.",".$$transRollId.",5,4,".$$reProcess.",".$$reProcess.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			if($data_array_prop!="") $data_array_prop.= ",";
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			$data_array_prop.="(".$id_prop.",".$from_trans_id.",6,134,".$id_dtls.",".$txt_from_order_id.",".$$productId.",".$$rollWgt.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$colorId.")";
			//$id_prop=$id_prop+1;
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			$data_array_prop.=",(".$id_prop.",".$id_trans.",5,134,".$id_dtls.",".$txt_to_order_id.",".$$productId.",".$$rollWgt.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$colorId.")";
		}
		//echo $data_array_prop;
		$rID=$rID2=$rID3=$rID4=$rID5=$rID6=1;
		if(str_replace("'","",$update_id)=="")
		{
			$rID=sql_insert("inv_item_transfer_mst",$field_array,$data_array,0);
			//if($rID) $flag=1; else $flag=0;
		}
		else
		{
			$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			//if($rID) $flag=1; else $flag=0; 
		}
		
		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		/*if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} */

		/*	mysql_query("ROLLBACK");
		echo "5**".$flag;die;*/
		//echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;
		//echo "10**".$rID2;die;
		//echo "10**insert into inv_item_transfer_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		
		$rID3=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);
		/*if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} */
		
		$rollIds=chop($rollIds,',');
		$rID4=sql_multirow_update("pro_roll_details","is_transfer*transfer_criteria*re_transfer","6*4*1","id",$rollIds,0);
	/*	if($flag==1) 
		{
			if($rID4) $flag=1; else $flag=0; 
		} */
		
		
		//echo $flag;die;
		//echo "insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;die;
		$rID5=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,1);
	/*	if($flag==1) 
		{
			if($rID5) $flag=1; else $flag=0; 
		} */
		
		//echo $flag;die;
		//echo "insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;
		$rID6=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1);
		/*if($flag==1) 
		{
			if($rID6) $flag=1; else $flag=0; 
		}*/
		
		/*$all_trans_roll_id=chop($all_trans_roll_id,',');
		if($all_trans_roll_id!="")
		{
			$rID7=sql_multirow_update("pro_roll_details","re_transfer","1","id",$all_trans_roll_id,0);
			if($flag==1) 
			{
				if($rID7) $flag=1; else $flag=0; 
			} 
		} */
		//echo $flag;die;
		//oci_rollback($con);
		//echo "5**".$flag;die;
		//echo "10**$rID##$rID2##$rID3##$rID4##$rID5##$rID6";die;
		//echo "10**insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;die;
		
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6)
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
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6)
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
		$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$txt_from_order_id."*".$txt_to_order_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		
		$rate=0; $amount=0;
		$field_array_trans="id, mst_id, company_id, prod_id, item_category, transaction_type, transaction_date, order_id, cons_uom, cons_quantity, cons_rate, cons_amount, rack, self, program_no, stitch_length, machine_id, inserted_by, insert_date";
		$field_array_trans_update="prod_id*transaction_date*order_id*cons_quantity*cons_rate*cons_amount*rack*self*program_no*stitch_length*updated_by*update_date";
		
		$field_array_dtls="id, mst_id, trans_id, to_trans_id, from_prod_id, item_category, transfer_qnty, roll, rate, transfer_value, uom, y_count, brand_id, yarn_lot, rack, shelf, to_rack, to_shelf, from_program, to_program, stitch_length, gsm, machine_no_id, batch_id, inserted_by, insert_date,color_id";	
		$field_array_dtls_update="from_prod_id*transfer_qnty*roll*rate*transfer_value*y_count*brand_id*yarn_lot*rack*shelf*to_rack*to_shelf*from_program*to_program*stitch_length*updated_by*update_date";	
		
		$field_array_roll="id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty,qc_pass_qnty, roll_no, roll_id, from_roll_id, is_transfer, transfer_criteria,reprocess, prev_reprocess , inserted_by, insert_date";
		$field_array_updateroll="qnty*roll_no*updated_by*update_date";
		
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, inserted_by, insert_date,color_id";
		
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
			$batch_id="batch_id_".$j;
			$reProcess="reProcess_".$j;
			$colorId="colorId_".$j;
			
			
			if(str_replace("'","",$$rolltableId)>0)
			{
				$update_dtls_id.=str_replace("'","",$$dtlsId).",";
				
				$transId_arr[]=str_replace("'","",$$transIdFrom);
				$data_array_update_trans[str_replace("'","",$$transIdFrom)]=explode("*",($$productId."*".$txt_transfer_date."*".$txt_from_order_id."*".$$rollWgt."*".$rate."*".$amount."*".$$rack."*".$$shelf."*".$$progId."*".$$stichLn."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				
				$transId_arr[]=str_replace("'","",$$transIdTo);
				$data_array_update_trans[str_replace("'","",$$transIdTo)]=explode("*",($$productId."*".$txt_transfer_date."*".$txt_to_order_id."*".$$rollWgt."*".$rate."*".$amount."*".$$rack."*".$$shelf."*".$$progId."*".$$stichLn."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				
				$dtlsId_arr[]=str_replace("'","",$$dtlsId);
				$data_array_update_dtls[str_replace("'","",$$dtlsId)]=explode("*",($$productId."*".$$rollWgt."*".$$rollNo."*".$rate."*".$amount."*".$$yarnCount."*".$$brandId."*".$$yarnLot."*".$$rack."*".$$shelf."*".$$rack."*".$$shelf."*".$$progId."*".$$progId."*".$$stichLn."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$$colorId));
				
				$rollId_arr[]=str_replace("'","",$$rolltableId);
				$data_array_update_roll[str_replace("'","",$$rolltableId)]=explode("*",($$rollWgt."*".$$rollNo."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				
				$dtlsIdProp=str_replace("'","",$$dtlsId);
				$transIdfromProp=str_replace("'","",$$transIdFrom);
				$transIdtoProp=str_replace("'","",$$transIdTo);
			}
			else
			{
				$transfer_update_id=$update_id;
				$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);			
				if($data_array_trans!="") $data_array_trans.=",";
				$data_array_trans.="(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",".$$productId.",2,6,".$txt_transfer_date.",".$txt_from_order_id.",12,".$$rollWgt.",".$rate.",".$amount.",".$$rack.",".$$shelf.",".$$progId.",".$$stichLn.",".$$machine_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$transIdfromProp=$id_trans;
				$from_trans_id=$id_trans;
				$id_trans=$id_trans+1;
				$transIdtoProp=$id_trans;
				$data_array_trans.=",(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",".$$productId.",2,5,".$txt_transfer_date.",".$txt_to_order_id.",12,".$$rollWgt.",".$rate.",".$amount.",".$$rack.",".$$shelf.",".$$progId.",".$$stichLn.",".$$machine_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$id_dtls.",".$transfer_update_id.",".$from_trans_id.",".$id_trans.",".$$productId.",2,".$$rollWgt.",".$$rollNo.",".$rate.",".$amount.",12,".$$yarnCount.",".$$brandId.",".$$yarnLot.",".$$rack.",".$$shelf.",".$$rack.",".$$shelf.",".$$progId.",".$$progId.",".$$stichLn.",".$$gsm.",".$$machine_id.",".$$batch_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$colorId.")";
				
				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);			
				if($data_array_roll!="") $data_array_roll.= ",";
				$data_array_roll.="(".$id_roll.",".$$barcodeNo.",".$transfer_update_id.",".$id_dtls.",".$txt_to_order_id.",134,".$$rollWgt.",".$$rollWgt.",".$$rollNo.",".$$rollId.",".$$transRollId.",5,4,".$$reProcess.",".$$reProcess.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				/*if($data_array_prop!="") $data_array_prop.= ",";
				$data_array_prop.="(".$id_prop.",".$from_trans_id.",6,134,".$id_dtls.",".$txt_from_order_id.",".$$productId.",".$$rollWgt.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$id_prop=$id_prop+1;
				$data_array_prop.=",(".$id_prop.",".$id_trans.",5,134,".$id_dtls.",".$txt_to_order_id.",".$$productId.",".$$rollWgt.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				
				$rollIds.=$$transRollId.",";
				if($data_array_trans!="") $data_array_trans.=",";
				$data_array_trans.="(".$id_trans.",".$update_id.",".$cbo_company_id.",".$$productId.",13,6,".$txt_transfer_date.",".$txt_from_order_id.",12,".$$rollWgt.",".$rate.",".$amount.",".$$rack.",".$$shelf.",".$$progId.",".$$stichLn.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				$transIdfromProp=$id_trans;
				$id_trans=$id_trans+1;
				$transIdtoProp=$id_trans;
				$data_array_trans.=",(".$id_trans.",".$update_id.",".$cbo_company_id.",".$$productId.",13,5,".$txt_transfer_date.",".$txt_to_order_id.",12,".$$rollWgt.",".$rate.",".$amount.",".$$rack.",".$$shelf.",".$$progId.",".$$stichLn.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$id_dtls.",".$update_id.",".$transIdfromProp.",".$transIdtoProp.",".$$productId.",13,".$$rollWgt.",".$$rollNo.",".$rate.",".$amount.",12,".$$yarnCount.",".$$brandId.",".$$yarnLot.",".$$rack.",".$$shelf.",".$$rack.",".$$shelf.",".$$progId.",".$$progId.",".$$stichLn.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				if($data_array_roll!="") $data_array_roll.= ",";
				$data_array_roll.="(".$id_roll.",".$$barcodeNo.",".$update_id.",".$id_dtls.",".$txt_to_order_id.",134,".$$rollWgt.",".$$rollNo.",".$$rollId.",".$$transRollId.",5,4,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";*/
				
				$dtlsIdProp=$id_dtls;
				$all_trans_roll_id.=$$transRollId.",";
			}
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			if($data_array_prop!="") $data_array_prop.= ",";
			$data_array_prop.="(".$id_prop.",".$transIdfromProp.",6,134,".$dtlsIdProp.",".$txt_from_order_id.",".$$productId.",".$$rollWgt.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$colorId.")";
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			$data_array_prop.=",(".$id_prop.",".$transIdtoProp.",5,134,".$dtlsIdProp.",".$txt_to_order_id.",".$$productId.",".$$rollWgt.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$colorId.")";
		}

		$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0;
		
		$rID2=$rID3=$rID4=true;
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
		
		$rIDinv=$rIDDtls=$rIDRoll=true;
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
			
			//echo $flag;die;
			//echo "insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;
			$rIDRoll=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,1);
			if($flag==1) 
			{
				if($rIDRoll) $flag=1; else $flag=0; 
			} 
		}
		
		$rID5=true;
		$rollIds=chop($rollIds,',');
		if($rollIds!="")
		{
			$rID5=sql_multirow_update("pro_roll_details","is_transfer*transfer_criteria*re_transfer","6*4*1","id",$rollIds,0);
			if($flag==1) 
			{
				if($rID5) $flag=1; else $flag=0; 
			} 
		}
		
		
		
		/*$all_trans_roll_id=chop($all_trans_roll_id,',');
		if($all_trans_roll_id!="")
		{
			$rID7=sql_multirow_update("pro_roll_details","re_transfer","1","id",$all_trans_roll_id,0);
			if($flag==1) 
			{
				if($rID7) $flag=1; else $flag=0; 
			} 
		}*/
		
		
		$statusChangeTran=$statusChangeDtls=$statusChangeRoll= $activeRoll=$active_prev_roll=true;
		if($txt_deleted_id!="")
		{
			//echo "10**5**jahid";die;
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
			
			$prev_rol_id_sql=sql_select("select from_roll_id from pro_roll_details where id in($rollIDDel) and status_active=1");
			$prev_rol_id="";
			foreach($prev_rol_id_sql as $row)
			{
				$prev_rol_id.=$row[csf("from_roll_id")].",";
			}
			$prev_rol_id=implode(",",array_unique(explode(",",chop($prev_rol_id,","))));
			//echo "10**5##select from_roll_id from pro_roll_details where id in($rollIDDel)";die;
			$field_array_status="updated_by*update_date*status_active*is_deleted";
			$data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
			$statusChangeTrans=sql_multirow_update("inv_transaction",$field_array_status,$data_array_status,"id",$transIDDel,0);
			$statusChangeDtls=sql_multirow_update("inv_item_transfer_dtls",$field_array_status,$data_array_status,"id",$dtlsIDDel,0);
			$statusChangeRoll=sql_multirow_update("pro_roll_details",$field_array_status,$data_array_status,"id",$rollIDDel,0);
			$activeRoll=sql_multirow_update("pro_roll_details","is_transfer*transfer_criteria*re_transfer","0*0*0","id",$rollIDactive,0);
			$active_prev_roll=sql_multirow_update("pro_roll_details","is_transfer*transfer_criteria*re_transfer","0*0*0","id",$prev_rol_id,0);
			
			if($flag==1) 
			{
				if($statusChangeTrans && $statusChangeDtls && $statusChangeRoll && $activeRoll && $active_prev_roll) $flag=1; else $flag=0; 
			} 
			
			
		}
		
		//echo "10**$rID=$rID2=$rID3=$rID4=$rID5=$rIDinv=$rIDDtls=$rIDRoll=$statusChangeTran=$statusChangeDtls=$statusChangeRoll= $activeRoll=$active_prev_roll";die;
		
		if($dtlsIDDel=="")
		{
			$update_dtls_id=chop($update_dtls_id,',');
		}
		else
		{
			$update_dtls_id=$update_dtls_id.$dtlsIDDel;
		}
		
		if($update_dtls_id!="")
		{
			$query = execute_query("DELETE FROM order_wise_pro_details WHERE dtls_id in(".$update_dtls_id.") and entry_form=134");
			if($flag==1) 
			{
				if($query) $flag=1; else $flag=0; 
			} 
		}
		
		
		
		
		
		//echo "10**5**insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;
		if($data_array_prop!="")
		{
			$rIDProp=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1);
			if($flag==1) 
			{
				if($rIDProp) $flag=1; else $flag=0; 
			} 
		}
		
		//echo "10**5**$flag";die;
		
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


if ($action=="finish_fabric_order_to_order_transfer_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$all_order_id=$data[3].",".$data[4];
	//print_r ($data);
	
	$sql="select id, transfer_system_id, transfer_date, challan_no, from_order_id, to_order_id, item_category from inv_item_transfer_mst a where id='$data[1]' and company_id='$data[0]'";
	//echo $sql;die;
	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$color_library=return_library_array( "select id, color_name from  lib_color", "id", "color_name"  );
	$batch_result=sql_select( "select a.id, a.batch_no, a.color_id from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and b.po_id=$data[3]");
	$batch_arr=array();
	foreach($batch_result as $row)
	{
		$batch_arr[$row[csf("id")]]["batch_no"]=$row[csf("batch_no")];
		$batch_arr[$row[csf("id")]]["color_id"]=$row[csf("color_id")];
	}
	
	$prod_sql=sql_select("select id, product_name_details, detarmination_id, gsm, dia_width from product_details_master where item_category_id=2");
	foreach($prod_sql as $row)
	{
		$product_arr[$row[csf("id")]]["product_name_details"]=$row[csf("product_name_details")];
		$product_arr[$row[csf("id")]]["detarmination_id"]=$row[csf("detarmination_id")];
		$product_arr[$row[csf("id")]]["gsm"]=$row[csf("gsm")];
		$product_arr[$row[csf("id")]]["dia_width"]=$row[csf("dia_width")];
	}
	
	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	
	$poDataArray=sql_select("select b.id,a.buyer_name,a.style_ref_no,a.job_no,b.po_number,b.pub_shipment_date, b.file_no, b.grouping as ref_no, (a.total_set_qnty*b.po_quantity) as qty from wo_po_break_down b,wo_po_details_master a where  a.job_no=b.job_no_mst and a.company_name=$data[0] and b.id in($all_order_id) and b.status_active=1 and b.is_deleted=0 ");
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
	?>
	<div style="width:920px;">
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
						if($result[csf('plot_no')]!="")  echo "Plot No: ".$result[csf('plot_no')]." ";
							if($result[csf('level_no')]!="")  echo "Level No: ".$result[csf('level_no')]." ";
								if($result[csf('road_no')]!="")  echo "Road No: ".$result[csf('road_no')]." ";
									if($result[csf('block_no')]!="")  echo "Block No: ".$result[csf('block_no')]." ";
										if($result[csf('city')]!="")  echo "City No: ".$result[csf('city')]." ";
											if($result[csf('zip_code')]!="")  echo "Zip Code: ".$result[csf('zip_code')]." "; 
												if($result[csf('email')]!="")  echo "Email Address: ".$result[csf('email')]." "; 
													if($result[csf('website')]!="")  echo "Website No: ".$result[csf('website')];
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
												<td width="450">
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
												<td>
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
														<tr>
															<td colspan="4">&nbsp;</td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
										<div style="width:100%;">
											<table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
												<thead bgcolor="#dddddd" align="center">
													<th width="50">SL</th>
													<th width="55">Product Id</th>
													<th width="100">Barcode No</th>
													<th width="60">Roll No</th>
													<th width="100">Batch No</th>
													<th width="110">Color</th>
													<th width="230">Fabric Description</th>
													<th width="70">GSM</th>
													<th>Transfered Qnty</th>
												</thead>
												<tbody> 
													<?
													$sql_dtls="select a.from_prod_id, a.transfer_qnty, a.uom, a.y_count, a.brand_id, a.yarn_lot, a.to_rack, a.to_shelf, a.stitch_length, a.batch_id, a.gsm, b.barcode_no, b.roll_no 
													from inv_item_transfer_dtls a, pro_roll_details b 
													where a.id=b.dtls_id and b.entry_form=134 and a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.barcode_no";

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
														$prod_name_dtls=$constructtion_arr[$product_arr[$row[csf('from_prod_id')]]["detarmination_id"]]." ".$composition_arr[$product_arr[$row[csf('from_prod_id')]]["detarmination_id"]]." ".$product_arr[$row[csf('from_prod_id')]]["gsm"]." ".$product_arr[$row[csf('from_prod_id')]]["dia_width"];
														?>
														<tr bgcolor="<? echo $bgcolor; ?>">
															<td align="center"><? echo $i; ?></td>
															<td align="center"><? echo $row[csf("from_prod_id")]; ?></td>
															<td align="center"><? echo $row[csf("barcode_no")]; ?></td>
															<td align="center"><? echo $row[csf("roll_no")]; ?></td>
															<td><? echo $batch_arr[$row[csf("batch_id")]]["batch_no"]; ?></td>
															<td><? echo $color_library[$batch_arr[$row[csf("batch_id")]]["color_id"]]; ?></td>
															<td><? echo $prod_name_dtls; ?></td>
															<td align="center"><? echo $row[csf("gsm")]; ?></td>
															<td align="right"><? echo $row[csf("transfer_qnty")]; ?></td>
														</tr>
														<? 
														$i++; 
													}
													?>
												</tbody>
												<tfoot>
													<tr>
														<td colspan="8" align="right"><strong>Total </strong></td>
														<td align="right"><?php echo $transfer_qnty_sum; ?></td>
													</tr>                           
												</tfoot>
											</table>
										</div>
									</div>   
									<?	
									exit();
								}
								?>

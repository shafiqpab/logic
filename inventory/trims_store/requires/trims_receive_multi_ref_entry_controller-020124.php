<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$permission=$_SESSION['page_permission'];
include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="upto_variable_settings")
{
	extract($_REQUEST);
	/*echo "select store_method from variable_settings_inventory where company_name='$cbo_company_id' and item_category_id=13 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1";die;*/
	echo $variable_inventory=return_field_value("store_method","variable_settings_inventory","company_name='$cbo_company_id' and item_category_id=4 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	exit();
}

if ($action == "get_receive_basis") 
{
	$variable_set_invent = return_field_value("independent_controll", "variable_settings_inventory", "company_name='$data' and variable_list=20 and menu_page_id=24 and status_active=1 and is_deleted=0", "independent_controll");
	$is_independent_controlled = ($variable_set_invent != 1) ? "1,2,4,6,12" : "1,2,6,12";
	echo create_drop_down("cbo_receive_basis", 122, $receive_basis_arr, "", 1, "-- Select --", $selected, "set_receive_basis();", "", $is_independent_controlled);
	exit();
}

if($action=="set_print_button")
{
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=6 and report_id=287 and is_deleted=0 and status_active=1");
	echo "print_report_button_setting('$print_report_format');\n"; 
	exit();
}

if($action=="load_drop_down_supplier")
{
	$data_ref=explode("_",$data);
	if($data_ref[1]==3 || $data_ref[1]==5)
	{
		echo create_drop_down( "cbo_supplier_name", 142,"select company_name,id from lib_company where status_active=1 and is_deleted=0 order by company_name",'id,company_name', 1, '-- Select Supplier --',0,0,1);
	}
	else
	{
		//echo "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data_ref[0]' and b.party_type in(4,5) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name";die;
		echo create_drop_down( "cbo_supplier_name", 142,"select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data_ref[0]' and b.party_type in(4,5) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name",'id,supplier_name', 1, '-- Select Supplier --',0,0,1);
	}
	exit();
}
if ($action=="load_room_rack_self_bin")
{
	load_room_rack_self_bin("requires/trims_receive_multi_ref_entry_controller",$data);
}
/*if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 142, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data' and b.category_type=4 and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "" );
	exit();
}

if($action=="get_library_exchange_rate")
{
	$exchange_rate=sql_select("select conversion_rate from currency_conversion_rate where currency=$data and status_active=1 and is_deleted=0 order by id desc");
	if($data==1)
	{
		echo "document.getElementById('txt_exchange_rate').value 	= '1';\n";
	}
	else
	{
		echo "document.getElementById('txt_exchange_rate').value 	= '".$exchange_rate[0][csf("conversion_rate")]."';\n";
	}
	exit();
}*/


if ($action == "check_conversion_rate") {
	$data = explode("**", $data);
	if ($db_type == 0) {
		$conversion_date = change_date_format($data[1], "Y-m-d", "-", 1);
	} else {
		$conversion_date = change_date_format($data[1], "d-M-y", "-", 1);
	}
	$exchange_rate = set_conversion_rate($data[0], $conversion_date,$data[2]);
	echo $exchange_rate;
	exit();
}

 //-------------------START ----------------------------------------

if($action=="addi_info_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);

	$user_info_arr=return_library_array("SELECT a.id, a.user_full_name, b.custom_designation from user_passwd a, lib_designation b where a.designation = b.id and a.valid = 1 order by a.user_full_name","id","user_full_name");


 	if(str_replace("'","",$pre_addi_info))
	{
		$pre_addi_info_arr = explode("_",str_replace("'","",$pre_addi_info)); 

		$txt_gate_entry_no = $pre_addi_info_arr[0];
		$txt_delv_chln_date = $pre_addi_info_arr[1];
		$txt_gate_entry_date = $pre_addi_info_arr[2];
		$txt_vehicle_no = $pre_addi_info_arr[3];
		$txt_invoice_no = $pre_addi_info_arr[4];
		$txt_transporter_name = $pre_addi_info_arr[5];
		$txt_invoice_date = $pre_addi_info_arr[6];
		$txt_short_qnty = $pre_addi_info_arr[7];
		$txt_excess_qnty = $pre_addi_info_arr[8];
	}
	
	?>
	<script>

	function fnClosed_addinfo() 
	{   var txtString = "";
		txtString = $("#txt_gate_entry_no").val() + '_' + $("#txt_delv_chln_date").val() + '_' + $("#txt_gate_entry_date").val() + '_' + $("#txt_vehicle_no").val() + '_' + $("#txt_invoice_no").val() + '_' + $("#txt_transporter_name").val() + '_' + $("#txt_invoice_date").val() + '_' + $("#txt_short_qnty").val() + '_' + $("#txt_excess_qnty").val();
		$("#txt_string").val(txtString);
		parent.emailwindow.hide();
 	}

	</script>
	</head>
	<body>
		<div align="center" style="width:100%;" >
			<br>
			<form name="searchlcfrm_1" id="searchlcfrm_1" autocomplete="off">
				<fieldset style="width:650px;">   
				<table  width="650" cellspacing="2" cellpadding="0" border="0" >
					<tr>
						<td width="100">
							<b>Gate Entry No</b>
						</td>
						<td>
							<input type="text" id="txt_gate_entry_no" name="txt_gate_entry_no" style="width:150px" class="text_boxes" value="<? echo $txt_gate_entry_no; ?>" />
						</td>
						
						<td width="120">
							<b>Delivery Challan Date</b>
						</td>
						<td>
							<input type="text" id="txt_delv_chln_date" name="txt_delv_chln_date" style="width:150px" class="datepicker" value="<? echo $txt_delv_chln_date ; ?>" readonly />
						</td>
					</tr>
					<tr>
						<td width="100">
							<b>Gate Entry Date</b>
						</td>
						<td>
							<input type="text" id="txt_gate_entry_date" name="txt_gate_entry_date" style="width:150px" class="datepicker" value="<? echo $txt_gate_entry_date ; ?>" readonly />
						</td>
						<td width="100">
							<b>Vehicle No</b>
						</td>
						<td>
							<input type="text" id="txt_vehicle_no" name="txt_vehicle_no" style="width:150px" class="text_boxes" value="<? echo $txt_vehicle_no; ?>" />
						</td>
					</tr>
					<tr>
						<td width="100">
							<b>Invoice No</b>
						</td>
						<td>
							<input type="text" id="txt_invoice_no" name="txt_invoice_no" style="width:150px" class="text_boxes" value="<? echo $txt_invoice_no; ?>" />
						</td>
						<td width="100">
							<b>Transporter Name</b>
						</td>
						<td>
							<input type="text" id="txt_transporter_name" name="txt_transporter_name" style="width:150px" class="text_boxes" value="<? echo $txt_transporter_name; ?>" />
						</td>
					</tr>
					<tr>
						<td width="100">
							<b>Invoice Date</b>
						</td>
						<td>
							<input type="text" id="txt_invoice_date" name="txt_invoice_date" style="width:150px" class="datepicker" value="<? echo $txt_invoice_date ; ?>" readonly />
						</td>
						<td width="100">
							<b>Short Qty</b>
						</td>
						<td>
							<input type="text" id="txt_short_qnty" name="txt_short_qnty" style="width:150px" class="text_boxes" value="<? echo $txt_short_qnty; ?>" />
						</td>

					</tr>
					<tr>
						<td width="100">
							
						</td>
						<td>
						
						</td>
						<td width="100">
							<b>Excess Qty</b>
						</td>
						<td>
							<input type="text" id="txt_excess_qnty" name="txt_excess_qnty" style="width:150px" class="text_boxes" value="<? echo $txt_excess_qnty; ?>" />
						</td>
					</tr>
				

				</table>
				<br>  
	            <div><input type="button" name="btn_close" class="formbutton" style="width:100px" value="Close" onClick="fnClosed_addinfo()" /></div>

				<input type="hidden" id="txt_string" value="" />
				<br>
				</fieldset>
			</form>
		</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}




$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
$size_arr = return_library_array("select id, size_name from lib_size","id","size_name");

$trim_group_arr =array(); 
$data_array=sql_select("select id, item_name, order_uom from lib_item_group");
foreach($data_array as $row)
{
	$trim_group_arr[$row[csf('id')]]['name']=$row[csf('item_name')];
	$trim_group_arr[$row[csf('id')]]['uom']=$row[csf('order_uom')];
}

if ($action=="wo_pi_popup")
{
	echo load_html_head_contents("WO/PI Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 

	<script>
		var update_id='<? echo $update_id; ?>';
		var dtls_tbl_id='<? echo $dtls_id; ?>';
		
		function js_set_value(id,no,type,data)
		{
			if(update_id!="")
			{
				var response = trim(return_global_ajax_value(update_id+"**"+dtls_tbl_id, 'duplication_check', '', 'trims_receive_multi_ref_entry_controller'));
				if(response!="")
				{
					var curr_data=data.split("**");
					var curr_supplier_id=curr_data[0];
					var curr_currency_id=curr_data[1];
					var curr_source=curr_data[2];
					var curr_lc_id=curr_data[4];
					
					var prev_data=response.split("**");
					var prev_supplier_id=prev_data[0];
					var prev_currency_id=prev_data[1];
					var prev_source=prev_data[2];
					var prev_lc_id=prev_data[3];
					
					if(!(curr_supplier_id==prev_supplier_id && curr_currency_id==prev_currency_id && curr_source==prev_source))
					{
						alert("Supplier, Currency and Source Mix not allow in Same Received ID");
						return;
					}
				}
			}
			//alert("Fuad");return;
			$('#hidden_wo_pi_id').val(id);
			$('#hidden_wo_pi_no').val(no);
			$('#booking_without_order').val(type);
			$('#hidden_data').val(data);
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body onLoad="set_hotkey()">
<div align="center" style="width:980px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:980px; margin-left:5px">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="750" class="rpt_table">
                <thead>
					<tr>
						<th colspan="4"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",1); ?></th>
					</tr>
					<tr>
						<th width="200">Search By</th>
						<th width="200">Date Range</th>
						<th width="200">Enter WO/PI No</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
							<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
							<input type="hidden" name="hidden_wo_pi_id" id="hidden_wo_pi_id" class="text_boxes" value="">  
							<input type="hidden" name="hidden_wo_pi_no" id="hidden_wo_pi_no" class="text_boxes" value=""> 
							<input type="hidden" name="booking_without_order" id="booking_without_order" class="text_boxes" value="">
							<input type="hidden" name="hidden_data" id="hidden_data" class="text_boxes" value=""> 
						</th> 
					</tr>
        
                </thead>
                <tr class="general">
                    <td align="center">	
                    	<? echo create_drop_down("cbo_receive_basis",152,$receive_basis_arr,"",1,"-- Select --",$recieve_basis,"","1","1,2,4,6"); ?>
                    </td> 
                    <td>
                    <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">To
					<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                    </td>                
                    <td align="center" id="search_by_td">				
                        <input type="text" style="width:150px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 						
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_receive_basis').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_string_search_type').value, 'create_wo_pi_search_list_view', 'search_div', 'trims_receive_multi_ref_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                     </td>
                </tr>
                <tr>
                	<td colspan="4" align="center" height="30" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
           </table>
            <div style="margin-top:10px;" id="search_div" align="left"></div> 
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<? exit;
}

if($action=="create_wo_pi_search_list_view")
{
	$data = explode("_",$data);
	
	$recieve_basis=$data[1];
	$company_id =$data[2];
	$date_from=str_replace("'","",$data[3]);
	$date_to=str_replace("'","",$data[4]);
	$search_type=str_replace("'","",$data[5]);
	if($db_type==0)
	{
		$date_from=change_date_format($date_from,'yyyy-mm-dd');
		$date_to=change_date_format($date_to,'yyyy-mm-dd');
	}
	else
	{
		$date_from=change_date_format($date_from, "", "",1);
		$date_to=change_date_format($date_to, "", "",1);
	}
	//echo $date_from."=".$date_to;die;
	
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$company_arr = return_library_array("select id, company_name from lib_company","id","company_name");
	
	if($recieve_basis==1)
	{
		$search_field_cond="";
		$search_string=trim($data[0]);
		if(trim($data[0])!="")
		{
			// $search_field_cond=" and a.pi_number like '$search_string'";
			if($search_type==1) $search_field_cond="and a.pi_number='$search_string'";
			else if($search_type==4 || $search_type==0)  $search_field_cond="and a.pi_number like '%$search_string%'";  
			else if ($search_type==2) $search_field_cond="and a.pi_number like '$search_string%'"; 
			else if ($search_type==3) $search_field_cond="and a.pi_number like '%$search_string'"; 
		}
		if($date_from!="" && $date_to!="")
		{
			$search_field_cond.=" and a.pi_date between '$date_from' and '$date_to'";
		}
		
		$btbLcArr=array();
		$lc_data=sql_select("select a.pi_id, b.id, b.lc_number from com_btb_lc_pi a, com_btb_lc_master_details b where a.status_active=1 and a.is_deleted=0 and a.com_btb_lc_master_details_id=b.id");
		foreach($lc_data as $row)
		{
			$btbLcArr[$row[csf('pi_id')]]=$row[csf('id')]."**".$row[csf('lc_number')];
		}


		$approval_status_cond="";
		if($db_type==0)
		{ 
			$approval_status="select approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company_id' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'),'yyyy-mm-dd')."' and company_id='$company_id')) and page_id=18 and status_active=1 and is_deleted=0";
		}
		else
		{
			$approval_status="select approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company_id' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'), "", "",1)."' and company_id='$company_id')) and page_id=18 and status_active=1 and is_deleted=0";
		}
		$approval_status=sql_select($approval_status);
		if($approval_status[0][csf('approval_need')]==1)
		{			
			if($approval_status[0][csf('allow_partial')]==1) $approval_status_cond= "and a.approved in(1,3)";
			else $approval_status_cond= "and a.approved = 1";
		}
		
		$sql = "select a.id, a.pi_number, a.supplier_id, a.pi_date, a.last_shipment_date, a.pi_basis_id, a.internal_file_no, a.currency_id, a.source, b.booking_without_order 
		from com_pi_master_details a, com_pi_item_details b
		where a.id=b.pi_id and a.item_category_id='4' and a.status_active=1 and a.is_deleted=0 and a.importer_id=$company_id and a.goods_rcv_status<>1 $search_field_cond $approval_status_cond
		group by a.id, a.pi_number, a.supplier_id, a.pi_date, a.last_shipment_date, a.pi_basis_id, a.internal_file_no, a.currency_id, a.source, b.booking_without_order"; 
		//echo $sql;
		$result = sql_select($sql);
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="960" class="rpt_table">
			<thead>
				<tr>
					<th width="50">SL</th>
					<th width="135">PI No</th>
					<th width="80">PI Date</th>
					<th width="110">PI Basis</th>               
					<th width="200">Supplier</th>
					<th width="100">Last Shipment Date</th>
					<th width="100">Internal File No</th>
					<th width="80">Currency</th>
					<th>Source</th>
				</tr>
			</thead>
		</table>
		<div style="width:978px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">	 
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="960" class="rpt_table" id="tbl_list_search">  
			<?
				$i=1;
				foreach ($result as $row)
				{  
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					
					$lc_data=explode("**",$btbLcArr[$row[csf('id')]]);
					$lc_id=$lc_data[0];
					$lc_no=$lc_data[1];
					
					$data=$row[csf('supplier_id')]."**".$row[csf('currency_id')]."**".$row[csf('source')]."**".$lc_no."**".$lc_id."****2"; 
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('pi_number')]; ?>','<? echo $row[csf('booking_without_order')]; ?>','<? echo $data; ?>');"> 
						<td width="50"><? echo $i; ?></td>
						<td width="135"><p><? echo $row[csf('pi_number')]; ?></p></td>
						<td width="80" align="center"><? echo change_date_format($row[csf('pi_date')]); ?></td>  
                        <td width="110"><? echo $pi_basis[$row[csf('pi_basis_id')]]; ?></td>             
						<td width="200"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
						<td width="100" align="center"><? echo change_date_format($row[csf('last_shipment_date')]); ?>&nbsp;</td>
						<td width="100"><p><? echo $row[csf('internal_file_no')]; ?></p></td>
						<td width="80"><p><? echo $currency[$row[csf('currency_id')]]; ?></p></td>
						<td><p><? echo $source[$row[csf('source')]]; ?></p></td>
					</tr>
				<?
				$i++;
				}
				?>
			</table>
		</div>
	<?	
	}
	else if($recieve_basis==2)
	{
		$search_string=trim($data[0]);
		
		$po_arr=array();
		$po_data=sql_select("select b.id, b.po_number, b.pub_shipment_date, b.po_quantity, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst");	
		foreach($po_data as $row)
		{
			$po_arr[$row[csf('id')]]=$row[csf('po_number')]."**".$row[csf('pub_shipment_date')]."**".$row[csf('po_quantity')]."**".$row[csf('po_qnty_in_pcs')];
		}
		$search_field_cond=$search_field_cond_sample="";
		if(trim($data[0])!="")
		{
			// $search_field_cond=" and a.booking_no like '$search_string'";
			// $search_field_cond_sample=" and s.booking_no like '$search_string'";
			if($search_type==1){
				$search_field_cond=" and a.booking_no_prefix_num='$search_string'";
				$search_field_cond_sample=" and s.booking_no_prefix_num='$search_string'";
			}
			else if($search_type==4 || $search_type==0){
				$search_field_cond=" and a.booking_no_prefix_num like '%$search_string%'"; 
				$search_field_cond_sample=" and s.booking_no_prefix_num like '%$search_string%'"; 
			} 
			else if ($search_type==2){
				$search_field_cond=" and a.booking_no_prefix_num like '$search_string%'"; 
				$search_field_cond_sample=" and s.booking_no_prefix_num like '$search_string%'"; 
			}
			else if ($search_type==3){
				$search_field_cond=" and a.booking_no_prefix_num like '%$search_string'"; 
				$search_field_cond_sample=" and s.booking_no_prefix_num like '%$search_string'"; 
			}
		}
		
		if($date_from!="" && $date_to!="")
		{
			$search_field_cond.=" and a.booking_date between '$date_from' and '$date_to'";
			$search_field_cond_sample.="and s.booking_date between '$date_from' and '$date_to'";
		}

		if($db_type==0)
		{ 
			$approval_status="select page_id, approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company_id' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'),'yyyy-mm-dd')."' and company_id='$company_id')) and page_id in(9,10) and status_active=1 and is_deleted=0";
		}
		else
		{
			$approval_status="select page_id, approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company_id' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'), "", "",1)."' and company_id='$company_id')) and page_id in(9,10) and status_active=1 and is_deleted=0"; 
		}
		//echo $approval_status;die;
		$approval_status=sql_select($approval_status);
		$approval_status_cond_main=$approval_status_cond_short="";
		foreach($approval_status as $row)
		{
			if($row[csf("page_id")]==9 && $row[csf("approval_need")]==1)
			{
				if($row[csf("allow_partial")]==1) $approval_status_cond_main=" and a.is_approved in(1,3)";
				else $approval_status_cond_main=" and a.is_approved = 1";
			}
			if($row[csf("page_id")]==10 && $row[csf("approval_need")]==1)
			{
				if($row[csf("allow_partial")]==1) $approval_status_cond_short=" and a.is_approved in(1,3)";
				else $approval_status_cond_short=" and a.is_approved = 1";
			}
		}
		
		if($db_type==0)
		{
			$sql = "select a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.delivery_date, a.currency_id, a.source, a.supplier_id, a.booking_type, a.is_short, a.pay_mode, group_concat(distinct(b.po_break_down_id)) as po_id, group_concat(distinct(b.job_no)) as job_no, YEAR(a.insert_date) as year, a.fabric_source, 0 as type from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pay_mode<>2 $search_field_cond $approval_status_cond_main  group by a.id
			union all
			select s.id, s.booking_no_prefix_num, s.booking_no, s.booking_date, s.delivery_date, s.currency_id, s.source, s.supplier_id, 0 as booking_type, 0 as is_short, s.pay_mode, null as po_id, null as job_no, YEAR(s.insert_date) as year, 0 as fabric_source, 1 as type FROM wo_non_ord_samp_booking_mst s WHERE s.company_id=$company_id and s.status_active =1 and s.is_deleted=0 and s.item_category=4 $search_field_cond_sample group by s.id"; 
		}
		else
		{
			$sql = "select a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.delivery_date, a.currency_id, a.source, a.supplier_id, a.booking_type, a.is_short, a.pay_mode, 
			rtrim(xmlagg(xmlelement(e,b.po_break_down_id,',').extract('//text()') order by b.po_break_down_id).GetClobVal(),',') as po_id, 
			rtrim(xmlagg(xmlelement(e,b.job_no,',').extract('//text()') order by b.job_no).GetClobVal(),',') as job_no, to_char(a.insert_date,'YYYY') as year, a.fabric_source, 0 as type 
			from wo_booking_mst a, wo_booking_dtls b 
			where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pay_mode<>2 $search_field_cond $approval_status_cond_main  group by a.id, a.booking_no, a.booking_date, a.delivery_date, a.currency_id, a.source, a.supplier_id, a.booking_no_prefix_num, a.insert_date, a.booking_type, a.is_short, a.pay_mode, a.fabric_source
			union all
			select s.id, s.booking_no_prefix_num, s.booking_no, s.booking_date, s.delivery_date, s.currency_id, s.source, s.supplier_id, 0 as booking_type, 0 as is_short, s.pay_mode, null as po_id, null as job_no, to_char(s.insert_date,'YYYY') as year, 0 as fabric_source, 1 as type 
			FROM wo_non_ord_samp_booking_mst s 
			WHERE s.company_id=$company_id and s.status_active =1 and s.is_deleted=0 and s.item_category=4 $search_field_cond_sample group by s.id, s.booking_no_prefix_num, s.booking_no, s.booking_date, s.delivery_date, s.currency_id, s.source, s.pay_mode, s.supplier_id, s.insert_date order by type, id"; 
		}
		// echo $sql;die;
		$result = sql_select($sql);

		$mst_id_arr=array();
		foreach ($result as $row) {
			$mst_id_arr[$row[csf('id')]] = $row[csf('id')];
		}

		$mst_id_arr = array_unique($mst_id_arr);

		$mst_id_con=where_con_using_array($mst_id_arr,0,"d.id");


		$sql_style ="select d.id,a.buyer_name,a.style_ref_no,b.po_number,b.id as po_id,b.grouping from wo_po_details_master a, wo_po_break_down b , wo_booking_dtls c, wo_booking_mst d where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.booking_no=d.booking_no and d.company_id=$company_id $mst_id_con and d.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ";
	 		//echo $sql_style;
 		$result_dtls = sql_select($sql_style); $booking_dtls_dataArr=array();
 		foreach($result_dtls as $val)
		{
			//$booking_ids.=$val[csf("id")].",";
			$booking_dtls_dataArr[$val[csf('id')]]['buyer_name'].=$buyer_arr[$val[csf('buyer_name')]].",";
			$booking_dtls_dataArr[$val[csf('id')]]['style_ref_no'].=$val[csf('style_ref_no')].",";
			$booking_dtls_dataArr[$val[csf('id')]]['grouping'].=$val[csf('grouping')].",";
			$booking_dtls_dataArr[$val[csf('id')]]['po_number'].=$val[csf('po_number')].",";
			$booking_dtls_dataArr[$val[csf('id')]]['act_po_number'].=$act_po_no_arr[$val[csf('po_id')]].",";
			//echo $act_po_no_arr[$val[csf('po_id')]].'DD';
		}



		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1160" class="rpt_table">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="60">Booking No</th>
					<th width="45">Year</th>
					<th width="70">Type</th>
					<th width="75">Booking Date</th>               
					<th width="100">Supplier</th>
					<th width="75">Delivary date</th>
					<th width="65">Source</th>
					<th width="65">Currency</th>
					<th width="100">Master Style</th>
					<th width="90">Job No</th>
					<th width="80">Order Qnty</th>
					<th width="75">Shipment Date</th>
					<th width="100">Style Ref</th>
					<th>Order No</th>
				</tr>
			</thead>
		</table>
		<div style="width:1178px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">	 
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1160" class="rpt_table" id="tbl_list_search">  
			<?
				$i=1;
				foreach ($result as $row)
				{  
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
						
					$booking_type='';	
					if($row[csf('booking_type')]==0) 
					{
						$booking_type='Sample Without Order';
					}
					else if($row[csf('booking_type')]==5) 
					{
						$booking_type='Sample';
					}
					else
					{
						if($row[csf('is_short')]==1) $booking_type='Short'; else $booking_type='Main';
					}
					
					$po_qnty_in_pcs=''; $po_no=''; $min_shipment_date='';
					
					if($row[csf('po_id')]!="" && $row[csf('type')]==0)
					{
						
						if($db_type==0)
						{
							$po_id=array_unique(explode(",",$row[csf('po_id')]));
							$job_no=implode(",",array_unique(explode(",",$row[csf('job_no')])));
						}else{
							$po_id=array_unique(explode(",",$row[csf('po_id')]->load()));
							$job_no=implode(",",array_unique(explode(",",$row[csf('job_no')]->load())));
						}
						foreach ($po_id as $id)
						{
							$po_data=explode("**",$po_arr[$id]);
							$po_number=$po_data[0];
							$pub_shipment_date=$po_data[1];
							$po_qnty=$po_data[2];
							$poQntyPcs=$po_data[3];
							
							if($po_no=="") $po_no=$po_number; else $po_no.=",".$po_number;
							
							if($min_shipment_date=='')
							{
								$min_shipment_date=$pub_shipment_date;
							}
							else
							{
								if($pub_shipment_date<$min_shipment_date) $min_shipment_date=$pub_shipment_date; else $min_shipment_date=$min_shipment_date;
							}
							
							$po_qnty_in_pcs+=$poQntyPcs;
						}
						$min_shipment_date=change_date_format($min_shipment_date);
					}
					else
					{
						$po_qnty_in_pcs='&nbsp;'; $po_no='&nbsp;'; $min_shipment_date='&nbsp;';
					}
					
					$data=$row[csf('supplier_id')]."**".$row[csf('currency_id')]."**".$row[csf('source')]."******".$row[csf('fabric_source')]."**".$row[csf('pay_mode')]; 


					$internal_ref=implode(",",array_unique(explode(",",chop($booking_dtls_dataArr[$row[csf('id')]]['grouping'],","))));

					$style_ref=implode(",",array_unique(explode(",",chop($booking_dtls_dataArr[$row[csf('id')]]['style_ref_no'],","))));



					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('booking_no')]; ?>','<? echo $row[csf('type')]; ?>','<? echo $data; ?>');"> 
						<td width="30"><? echo $i; ?></td>
						<td width="60" style="word-break:break-all;"><p>&nbsp;&nbsp;<? echo $row[csf('booking_no_prefix_num')]; ?></p></td>
                        <td width="45" align="center" style="word-break:break-all;"><? echo $row[csf('year')]; ?></td>
                        <td width="70" align="center" style="word-break:break-all;"><p><? echo $booking_type; ?></p></td>
						<td width="75" align="center" style="word-break:break-all;"><? echo change_date_format($row[csf('booking_date')]); ?>&nbsp;</td>               
						<td width="100" style="word-break:break-all;"><p><? if($row[csf('pay_mode')]==3 || $row[csf('pay_mode')]==5) echo $company_arr[$row[csf('supplier_id')]]; else echo $supplier_arr[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
						<td width="75" align="center" style="word-break:break-all;"><? echo change_date_format($row[csf('delivery_date')]); ?>&nbsp;</td>
                        <td width="65" style="word-break:break-all;"><p><? echo $source[$row[csf('source')]]; ?></p></td>
                        <td width="65" style="word-break:break-all;"><p><? echo $currency[$row[csf('currency_id')]]; ?></p></td>
                        <td width="100" style="word-break:break-all;"><p><? echo $internal_ref; ?></p></td>
						<td width="90" style="word-break:break-all;"><p><? echo $job_no; ?>&nbsp;</p></td>
						<td width="80" align="right"><? echo $po_qnty_in_pcs; ?></td>
						<td width="75" align="center" style="word-break:break-all;"><? echo $min_shipment_date; ?></td>
						<td width="100" align="center" style="word-break:break-all;"><? echo $style_ref; ?></td>
						<td style="word-break:break-all;"><p><? echo $po_no; ?></p></td>
					</tr>
				<?
				$i++;
				}
				?>
			</table>
		</div>
	<?	
	}
	exit();
}

if($action=="duplication_check")
{
	$data=explode("**",$data);
	$update_id=$data[0];
	$dtls_id=$data[1];
	
	if($dtls_id=="") $dtls_id_cond=""; else $dtls_id_cond=" and b.id!=$dtls_id";
	
	$sql="select a.supplier_id, a.currency_id, a.source, a.lc_no from inv_receive_master a, inv_trims_entry_dtls b where a.id=b.mst_id and a.id=$update_id and a.item_category=4 and a.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $dtls_id_cond";
	$dataArray=sql_select($sql);
	$data=$dataArray[0][csf('supplier_id')]."**".$dataArray[0][csf('currency_id')]."**".$dataArray[0][csf('source')]."**".$dataArray[0][csf('lc_no')];
	echo $data;
	exit();
}	 

/*if($action=='show_fabric_desc_listview')
{
	$data=explode("**",$data);
	$bookingNo_piId=$data[0];
	$receive_basis=$data[1];
	$booking_without_order=$data[2];
	
	if($receive_basis==1)
	{
		$table_width=480; $po_column=''; $sensitivity_column=''; $size_width="60"; $brandSup_width="60"; $rate_width="50";
		$sql="select item_group as trim_group, item_description as description, brand_supplier, color_id, item_color, size_id, item_size, '' as sensitivity, rate from com_pi_item_details where pi_id='$bookingNo_piId' and status_active=1 and is_deleted=0";// group by item_group, item_description, brand_supplier, color_id, item_color, size_id, item_size
	}
	else if($receive_basis==2)
	{
		
		if($booking_without_order==1)
		{
			$table_width=480; $po_column=''; $sensitivity_column=''; $size_width="60"; $brandSup_width="60"; $rate_width="50";
			$sql = "select 0 as sensitivity, trim_group, fabric_description as description, gmts_color as color_id, fabric_color as item_color, gmts_size as size_id, item_size, barnd_sup_ref as brand_supplier, rate from wo_non_ord_samp_booking_dtls where booking_no='$bookingNo_piId' and status_active=1 and is_deleted=0";
		}
		else
		{
			$po_arr=array();
			$poDataArr = sql_select("select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst"); 
			foreach($poDataArr as $rowP)
			{
				$po_arr[$rowP[csf('id')]][1]=$rowP[csf('style_ref_no')];
				$po_arr[$rowP[csf('id')]][2]=$rowP[csf('po_number')];
			}
			
			$table_width=550; $size_width="40"; $brandSup_width="50"; $rate_width="45";
			$po_column="<th width='50'>PO No.</th><th width='50'>Style Ref.</th>"; $sensitivity_column="<th width='55'>Sensitivity</th>";
			$sql = "select b.po_break_down_id, b.sensitivity, b.trim_group, c.description, c.color_number_id as color_id, c.item_color, c.gmts_sizes as size_id, c.item_size, c.brand_supplier, c.rate from wo_booking_dtls b, wo_trim_book_con_dtls c where b.id=c.wo_trim_booking_dtls_id and b.booking_no='$bookingNo_piId' and c.cons>0 and b.status_active=1 and b.is_deleted=0";
		}
	}
	//echo $sql;
	$data_array=sql_select($sql);
	
	?>
    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="<? echo $table_width; ?>">
        <thead>
            <th width="20">SL</th>
            <? echo $po_column; ?>
            <th width="60">Item Group</th>
            <th>Item Description</th>
            <th width="<? echo $brandSup_width; ?>">Brand/ Sup Ref</th>
            <th width="<? echo $size_width; ?>">Gmts Size</th>
            <th width="60">Item Color</th>
            <th width="<? echo $size_width; ?>">Item Size</th>
            <th width="<? echo $rate_width; ?>">rate</th>
            <? echo $sensitivity_column; ?>
        </thead>
	</table>
    <div style="width:<? echo $table_width+20; ?>px; overflow-y: scroll; max-height:400px;" id="scroll_body">
    	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="<? echo $table_width; ?>">
            <? 
            $i=1;
            foreach($data_array as $row)
            {  
                if ($i%2==0)  
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";
				
				$data=$row[csf('trim_group')]."**".$row[csf('description')]."**".$row[csf('brand_supplier')]."**".$row[csf('sensitivity')]."**".$row[csf('color_id')]."**".$color_arr[$row[csf('color_id')]]."**".$row[csf('size_id')]."**".$size_arr[$row[csf('size_id')]]."**".$row[csf('item_color')]."**".$color_arr[$row[csf('item_color')]]."**".$row[csf('item_size')];
				
				$desc=$row[csf('description')];
				
				//if($row[csf('item_color')]!="" && $row[csf('item_color')]!=0) {$desc.=" ".$color_arr[$row[csf('item_color')]];}
				//if($row[csf('item_size')]!="") $desc.=", ".$row[csf('item_size')];
             ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $data; ?>");change_color("tr_<? echo $i; ?>","<? echo $bgcolor;?>");' id="tr_<? echo $i; ?>" style="cursor:pointer" >
                    <td width="20"><? echo $i; ?></td>
                    <?
					if($receive_basis==2 && $booking_without_order!=1)
					{
					?>
                    	<td width="50"><p><? echo $po_arr[$row[csf('po_break_down_id')]][2]; ?></p></td>
                        <td width="50"><p><? echo $po_arr[$row[csf('po_break_down_id')]][1]; ?></p></td>
                    <?
					}
					?>
                    <td width="60"><p><? echo $trim_group_arr[$row[csf('trim_group')]]['name']; ?></p></td>
                    <td><p><? echo $desc; ?></p></td>
                    <td width="<? echo $brandSup_width; ?>"><p><? echo $row[csf('brand_supplier')]; ?></p></td>
                    <td width="<? echo $size_width; ?>"><p><? echo $size_arr[$row[csf('size_id')]]; ?></p></td>
                    <td width="60"><p><? echo $color_arr[$row[csf('item_color')]]; ?></p></td>
                    <td width="<? echo $size_width; ?>"><p><? echo $row[csf('item_size')]; ?></p></td>
                    <td width="<? echo $rate_width; ?>" align="right"><? echo number_format($row[csf('rate')],2); ?></td>
                    <? if($receive_basis==2 && $booking_without_order!=1) echo "<td width='55'><p>".$size_color_sensitive[$row[csf('sensitivity')]]."</p></td>";  ?>
                </tr>
            <? 
            $i++; 
            } 
            ?>
    	</table>
    </div>
<?
exit();
}*/

if($action=='show_fabric_desc_listview')
{
	$data=explode("**",$data);
	$bookingNo_piId=$data[0];
	$receive_basis=$data[1];
	$booking_without_order=$data[2];
	$book_pi_id = $data[3];
	?>
	<script type="text/javascript">
		function fnc_close()
		{
			var save_string='';	 var tot_trims_qnty=''; var po_id_array = new Array(); var po_no='';
			var total_balance=0;
			
			$("#tbl_list_search").find('tbody tr').each(function()
			{
				var txtPoId=$(this).find('input[name="txtPoId[]"]').val();
				var txtPoName=$(this).find('input[name="txtPoName[]"]').val();
				var txtRecvQnty=$(this).find('input[name="txtRecvQnty[]"]').val();
				var txtTotRecvQnty=$(this).find('input[name="txtTotRecvQnty[]"]').val();
				var txtRcvBalance=$(this).find('input[name="txtRcvBalance[]"]').val();
				total_balance+=	txtRcvBalance*1;			

				tot_trims_qnty=tot_trims_qnty*1+txtRecvQnty*1;
				
				if(txtRecvQnty*1>0)
				{
					if(save_string=="")
					{
						save_string=txtPoId+"_"+txtRecvQnty;
					}
					else
					{
						save_string+=","+txtPoId+"_"+txtRecvQnty;
					}
					
					if(jQuery.inArray( txtPoId, po_id_array) == -1 ) 
					{
						po_id_array.push(txtPoId);
						if(po_no=="") po_no=txtPoName; else po_no+=","+txtPoName;
					}
				}
			});
			
			$('#save_string').val( save_string );
			$('#tot_trims_qnty').val( tot_trims_qnty.toFixed(2));
			$('#all_po_id').val( po_id_array );
			$('#all_po_no').val( po_no );
			$('#all_balance').val( total_balance );
			
			parent.emailwindow.hide();
		}
	</script>
	<?
	if($receive_basis==1)
	{
		$table_width=740;  $sensitivity_column=''; $qty_column=''; $size_width="60"; $brandSup_width="60"; $item_width="60";
		$po_column="<th width='60'>PO No.</th><th width='60'>Style Ref.</th>";
		$qty_column="<th width='50'>Qty.</th>";
		$sql="select item_group as trim_group,work_order_dtls_id as po_break_down_id, item_description as description, brand_supplier, color_id, item_color, size_id, item_size, '' as sensitivity, quantity as qty, rate from com_pi_item_details where pi_id='$bookingNo_piId' and status_active=1 and is_deleted=0";// group by item_group, item_description, brand_supplier, color_id, item_color, size_id, item_size
		
		 
		//$po_arr = return_library_array("select id, po_number from wo_po_break_down","id","po_number");
		
		//echo "select c.id as po_break_down_id,a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b,wo_booking_dtls c,wo_booking_mst d,com_pi_item_details e where a.job_no=b.job_no_mst and c.po_break_down_id=b.id and c.booking_no=d.booking_no and e.pi_id='$bookingNo_piId' and e.work_order_dtls_id=c.id and e.work_order_no=c.booking_no and d.booking_type=2 and d.item_category=4 ";
		
		$po_arr=array(); $style_arr=array(); $booking_po_id=array();
		
		 $poDataArr = sql_select("select c.id as po_break_down_id,a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c,wo_booking_mst d,com_pi_item_details e where a.job_no=b.job_no_mst and c.po_break_down_id=b.id and c.booking_no=d.booking_no and e.pi_id='$bookingNo_piId' and e.work_order_dtls_id=c.id and e.work_order_no=c.booking_no and d.booking_type=2 and d.item_category=4 ");
		foreach($poDataArr as $wRow)
		{
			$po_arr[$wRow[csf('po_break_down_id')]]=$wRow[csf('po_number')];
			$booking_po_id[$wRow[csf('po_break_down_id')]]=$wRow[csf('id')];
			$style_arr[$wRow[csf('po_break_down_id')]]=$wRow[csf('style_ref_no')];
		}

		$prev_rcv_sql="select b.po_breakdown_id, a.item_group_id, a.item_description, a.brand_supplier, a.gmts_color_id, a.item_color, a.gmts_size_id, a.item_size, b.quantity as qnty ,b.ORDER_RATE as rate
		from inv_receive_master m, inv_trims_entry_dtls a, order_wise_pro_details b 
		where m.id=a.mst_id and a.id=b.dtls_id and m.receive_basis=1 and b.entry_form=24 and b.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_id=$bookingNo_piId";
	}
	else if($receive_basis==2)
	{
		if($booking_without_order==1)
		{
			$table_width=640; $po_column=''; $sensitivity_column=''; $qty_column="<th width='50'>Qty.</th>"; $size_width="60"; $brandSup_width="60"; $item_width="60";
			$sql = "select 0 as po_break_down_id, 0 as sensitivity, trim_group, fabric_description as description, gmts_color as color_id, fabric_color as item_color, gmts_size as size_id, item_size, barnd_sup_ref as brand_supplier, trim_qty as qty, rate, 0 as pre_cost_fabric_cost_dtls_id  
			from wo_non_ord_samp_booking_dtls where booking_no='$bookingNo_piId' and status_active=1 and is_deleted=0";

			$prev_rcv_sql="select 0 as po_breakdown_id, a.item_group_id, a.item_description, a.brand_supplier, a.gmts_color_id, a.item_color, a.gmts_size_id, a.item_size, a.receive_qnty as qnty, a.rate as rate
			from inv_receive_master m, inv_trims_entry_dtls a
			where m.id=a.mst_id and m.receive_basis=2 and m.entry_form=24 and a.booking_without_order=1 and a.status_active=1 and a.is_deleted=0 and m.status_active=1 and a.booking_no='$bookingNo_piId'";
		}
		else
		{
			$po_arr=array(); $style_arr=array(); 
			//$po_arr = return_library_array("select id, po_number from wo_po_break_down","id","po_number");
			$poDataArr = sql_select("select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst");
			foreach($poDataArr as $wRow)
			{
				$po_arr[$wRow[csf('id')]]=$wRow[csf('po_number')];
				$style_arr[$wRow[csf('id')]]=$wRow[csf('style_ref_no')];
			}
			
			$table_width=750; $size_width="40"; $brandSup_width="50"; $item_width="50";
			$po_column="<th width='60'>PO No.</th><th width='60'>Style Ref.</th>"; $sensitivity_column="<th width='60'>Sensitivity</th>"; $qty_column="<th width='50'>Qty.</th>";
			$sql = "select b.po_break_down_id, b.sensitivity, b.trim_group, c.description, c.color_number_id as color_id, c.item_color, c.gmts_sizes as size_id, c.item_size, c.brand_supplier, c.cons as qty, c.rate, b.pre_cost_fabric_cost_dtls_id 
			from wo_booking_dtls b, wo_trim_book_con_dtls c 
			where b.id=c.wo_trim_booking_dtls_id and b.booking_no='$bookingNo_piId' and c.cons>0 and b.status_active=1 and b.is_deleted=0";

			$prev_rcv_sql="select b.po_breakdown_id, a.item_group_id, a.item_description, a.brand_supplier, a.gmts_color_id, a.item_color, a.gmts_size_id, a.item_size, b.quantity as qnty, a.rate as rate
			from inv_receive_master m, inv_trims_entry_dtls a, order_wise_pro_details b 
			where m.id=a.mst_id and a.id=b.dtls_id and m.receive_basis=2 and b.entry_form=24 and b.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and m.status_active=1 and a.booking_no='$bookingNo_piId'";
		}
	}
	//echo $sql;
	$data_array=sql_select($sql);
	if($booking_without_order!=1)
	{
		$budge_dtls_id="";
		foreach($data_array as $row)
		{
			$budge_dtls_id.=$row[csf("pre_cost_fabric_cost_dtls_id")].",";
		}
		$budge_dtls_id=chop($budge_dtls_id,",");


		if($budge_dtls_id=="") $budge_dtls_id=0;
		$trims_budge_sql=sql_select("select id, remark from wo_pre_cost_trim_cost_dtls where id in($budge_dtls_id)");
		$trims_budge_data=array();
		foreach($trims_budge_sql as $row)
		{
			$trims_budge_data[$row[csf("id")]]=$row[csf("remark")];
		}
		unset($trims_budge_sql);
		
	}

	//echo $prev_rcv_sql."<br>";
	$prev_rcv_sql_result=sql_select($prev_rcv_sql);
	$prev_rcv_data=array();
	foreach($prev_rcv_sql_result as $row)
	{
		$prev_rcv_data[$row[csf("po_breakdown_id")]][$row[csf("item_group_id")]][trim($row[csf("item_description")])][trim($row[csf("brand_supplier")])][$row[csf("gmts_color_id")]][$row[csf("item_color")]][$row[csf("gmts_size_id")]][trim($row[csf("item_size")])][number_format($row[csf("rate")],6,".","")]["qnty"]+=$row[csf("qnty")];
	}

	if($receive_basis==1 || $receive_basis==2)
	{ 
		$rcvRtn_qty_sql = "SELECT b.po_breakdown_id, a.item_group_id, a.item_description, a.brand_supplier, a.color as gmts_color_id, a.gmts_size as gmts_size_id, a.item_color, a.item_size, b.quantity as recv_return_qty from product_details_master a, inv_transaction t, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d
		where a.id=b.prod_id and a.id=t.prod_id and t.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.id=b.prod_id and a.item_category_id=4 and b.entry_form in(49) and t.transaction_type=3 and t.pi_wo_batch_no in($book_pi_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and t.status_active=1 and t.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	}
	//echo $rcvRtn_qty_sql;
	$totalRcvRtnQty_arr=array();
	$rcvRtn_qtyArray=sql_select($rcvRtn_qty_sql); 
	foreach($rcvRtn_qtyArray as $row)
	{ 
		$totalRcvRtnQty_arr[$row[csf("po_breakdown_id")]][$row[csf("item_group_id")]][trim($row[csf("item_description")],', [BS]')][trim($row[csf("brand_supplier")])][$row[csf("gmts_color_id")]][$row[csf("item_color")]][$row[csf("gmts_size_id")]][trim($row[csf("item_size")])]+=$row[csf('recv_return_qty')];
	}
	
	?>
    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="<? echo $table_width; ?>">
        <thead>
            <th width="25">SL</th>
            <? echo $po_column; ?>
            <th width="60">Item Group</th>
            <th>Item Description</th>
            <th width="<? echo $brandSup_width; ?>">Brand/ Sup Ref</th>
            <th width="60">Gmts Color</th>
            <th width="<? echo $size_width; ?>">Gmts Size</th>
            <th width="60">Item Color</th>
            <th width="<? echo $item_width; ?>">Item Size</th>
            <? echo $qty_column; ?>
            <th width="50">Rcv.Bal</th>
            <th width="40">rate</th>
            <? echo $sensitivity_column; ?>
        </thead>
	</table>
    <div style="width:<? echo $table_width+20; ?>px; overflow-y: scroll; max-height:400px;" id="scroll_body">
    	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="<? echo $table_width; ?>" id="tbl_list_search">
            <? 
            $i=1;
            foreach($data_array as $row)
            {  
                if ($i%2==0)  
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";
				
				if( $receive_basis==1 && $booking_without_order!=1)
				{
					$order_id=$booking_po_id[$row[csf('po_break_down_id')]];
				}
				else if( $receive_basis==2 && $booking_without_order!=1)
				{
					$order_id=$row[csf('po_break_down_id')];
				}
				else
				{
					$order_id=0;
				}
				
				$data=$row[csf('trim_group')]."**".$row[csf('description')]."**".$row[csf('brand_supplier')]."**".$row[csf('sensitivity')]."**".$row[csf('color_id')]."**".$color_arr[$row[csf('color_id')]]."**".$row[csf('size_id')]."**".$size_arr[$row[csf('size_id')]]."**".$row[csf('item_color')]."**".$color_arr[$row[csf('item_color')]]."**".$row[csf('item_size')]."**".$order_id."**".$row[csf('po_break_down_id')]."**".$row[csf('rate')];
				
				$rate=number_format($row[csf('rate')],6,".","");
				$desc=$row[csf('description')];
				
				//if($row[csf('item_color')]!="" && $row[csf('item_color')]!=0) {$desc.=" ".$color_arr[$row[csf('item_color')]];}
				//if($row[csf('item_size')]!="") $desc.=", ".$row[csf('item_size')];
				if($order_id>0) $po_popup_function='openmypage_po("",'.$receive_basis.');'; else $po_popup_function="";

				$prev_rec_qnty=$prev_rcv_data[$order_id][$row[csf('trim_group')]][trim($row[csf('description')])][trim($row[csf('brand_supplier')])][$row[csf('color_id')]][$row[csf('item_color')]][$row[csf('size_id')]][trim($row[csf('item_size')])][$rate]["qnty"];
				$prev_rtn_qnty=$totalRcvRtnQty_arr[$order_id][$row[csf('trim_group')]][trim($row[csf('description')])][trim($row[csf('brand_supplier')])][$row[csf('color_id')]][$row[csf('item_color')]][$row[csf('size_id')]][trim($row[csf('item_size')])];
				$balance_qnty=(($row[csf('qty')]+$prev_rtn_qnty)-$prev_rec_qnty);

		
             	?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $data; ?>");change_color("tr_<? echo $i; ?>","<? echo $bgcolor;?>");<? echo $po_popup_function; ?>' id="tr_<? echo $i; ?>" style="cursor:pointer" >
                    <td width="25" align="center"><? echo $i; ?></td>
                    <?
					if(($receive_basis==2 || $receive_basis==1) && $booking_without_order!=1)
					{
						?>
                    	<td width="60"><p><? echo $po_arr[$row[csf('po_break_down_id')]]; ?></p></td>
                        <td width="60"><p><? echo $style_arr[$row[csf('po_break_down_id')]]; ?></p></td> 
                    	<?
					}
					?>
                    <td width="60"><p><? echo $trim_group_arr[$row[csf('trim_group')]]['name']; ?></p></td>
                    <td title="<? echo $trims_budge_data[$row[csf("pre_cost_fabric_cost_dtls_id")]]; ?>"><p><? echo $desc; ?></p></td>
                    <td width="<? echo $brandSup_width; ?>"><p><? echo $row[csf('brand_supplier')]; ?></p></td>
                    <td width="60"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                    <td width="<? echo $size_width; ?>"><p><? echo $size_arr[$row[csf('size_id')]]; ?></p></td>
                    <td width="60"><p><? echo $color_arr[$row[csf('item_color')]]; ?></p></td>
                    <td width="<? echo $item_width; ?>"><p><? echo $row[csf('item_size')]; ?></p></td>
                    <td width="50" align="right"><? echo number_format($row[csf('qty')],2); ?></td>
                    <td width="50" align="right"><? echo  number_format($balance_qnty,2); ?></td>
                    <td width="40" align="right"><? echo number_format($row[csf('rate')],4); ?></td>
                    <? if($receive_basis==2 && $booking_without_order!=1) echo "<td width='60'><p>".$size_color_sensitive[$row[csf('sensitivity')]]."</p></td>";  ?>
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




if ($action=="po_popup")
{
	
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	$data=explode("**",$data);
	$po_id=$data[0]; $type=$data[1];
	$booking_no=trim($booking_no);
	$txt_rate=trim($txt_rate);
	$txt_hidden_rate=trim($txt_hidden_rate);

	//echo $wo_pi_basis_id."=";die;
	//echo $type.'kaiyum';die;
	if($type==1) 
	{
		$item_group=$data[2]; 
		$item_description=$data[3]; 
		$brand_supref=$data[4]; 
		$order_uom=$data[5]; 
		$receive_basis=$data[6]; 
		$save_data=$data[7];
		$gmts_color_id=$data[8]; 
		$gmts_size_id=$data[9]; 
		$item_color_id=$data[10]; 
		$item_size=$data[11];
		$booking_pi_id=$data[12];
	}
	$variable_set_invent = sql_select("select category,over_rcv_status,over_rcv_percent,over_rcv_payment from variable_inv_ile_standard where company_name=$cbo_company_id and variable_list=23 and category =4 order by id");
	$over_receive_limit = !empty($variable_set_invent) ? $variable_set_invent[0][csf('over_rcv_percent')] : 0;
	//$item_description=str_replace("'","",$item_description);
	//echo $item_description.jahid;die;
	//$item_description=str_replace('"','',$item_description);
	?> 

	<script>
		var receive_basis='<? echo $receive_basis; ?>';
		var call_source='<? echo $call_source; ?>';
		
		function fn_show_check()
		{
			if(form_validation('cbo_buyer_name','Buyer Name')==false )
			{
				return;
			}	
					
			show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('cbo_buyer_name').value+'_'+'<? echo $all_po_id; ?>', 'create_po_search_list_view', 'search_div', 'trims_receive_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);hidden_field_reset();');
			set_all();
		}
		
		function distribute_qnty()
		{ 
			var tot_wo_qty=$('#value_tot_wo_qty').text().replace(/,/g, "")*1;
			var tot_rcv_balance=$('#value_tot_rcv_balance').text().replace(/,/g, "")*1;
			
			var txt_prop_trims_qty=$('#txt_prop_trims_qty').val()*1;
			var tblRow = $("#tbl_list_search tbody tr").length-1;
			var balance =txt_prop_trims_qty;
			var len=totalTrims=0;
			
			if(txt_prop_trims_qty>0)
			{
				$('#txt_prop_ship_trims_qty').attr('disabled',true);
			}
			else
			{
				$('#txt_prop_ship_trims_qty').attr('disabled',false);
			}
			
			for(var i=1;i<=tblRow;i++)
			{
				if($("#tr_"+i).is(':visible')==true)
				{
					len=len+1;
					
					var wo_qnty=$('#woQty_'+i).text()*1;
					
					var RcvBalance=$('#txtRcvBalance_'+i).val()*1;
					
					var perc=(RcvBalance/tot_rcv_balance)*100;
					
					
					var trims_qnty=(perc*txt_prop_trims_qty)/100;
					totalTrims = totalTrims*1+trims_qnty*1;
					totalTrims = totalTrims.toFixed(4);
					if(RcvBalance>0 && txt_prop_trims_qty>0)
					{
						if(i==tblRow)
						{
							$('#txtRecvQnty_'+i).val(balance.toFixed(4));
						}
						else
						{
							$('#txtRecvQnty_'+i).val(trims_qnty.toFixed(4));
							balance=(balance*1)-((trims_qnty*1).toFixed(4));
						}
					}
				}
				else
				{
					$('#txtRecvQnty_'+i).val(0);
				}//end if;
			} 
			calculate_total();
		}
		
		
		function distribute_ship_qnty()
		{ 
			var tot_wo_qty=$('#value_tot_wo_qty').text()*1;
			var tot_rcv_balance=$('#value_tot_rcv_balance').text().replace(/,/g, "")*1;
			var txt_prop_ship_trims_qty=$('#txt_prop_ship_trims_qty').val()*1;
			var tblRow = $("#tbl_list_search tbody tr").length-1;
			var balance =txt_prop_ship_trims_qty;
			var len=totalTrims=0;
			
			if(txt_prop_ship_trims_qty>0)
			{
				$('#txt_prop_trims_qty').attr('disabled',true);
			}
			else
			{
				$('#txt_prop_trims_qty').attr('disabled',false);
			}
			
			
			$("#tbl_list_search").find('tbody tr').each(function()
				{
					$(this).find('input[name="txtRecvQnty[]"]').val("");
				});
			
			for(var i=1;i<=tblRow;i++)
			{
				len=len+1;
				
				var RcvBalance=$('#txtRcvBalance_'+i).val()*1;
				var trims_qnty=RcvBalance;
				totalTrims = totalTrims*1+trims_qnty*1;
				totalTrims = totalTrims.toFixed(4);
				if(RcvBalance>0 && txt_prop_ship_trims_qty>0)
				{
					if(balance<trims_qnty)
					{
						$('#txtRecvQnty_'+i).val(balance.toFixed(4));
						break;
					}
					else
					{
						$('#txtRecvQnty_'+i).val(trims_qnty.toFixed(4));
						balance=(balance*1)-(trims_qnty*1).toFixed(4);
					}
				}
			}			
			calculate_total();
		}
		
		function calculate_total()
		{
			var tblRow = $("#tbl_list_search tbody tr").length;
			var total_receive=0; var recv_qnty=0;
			for(var i=1;i<tblRow;i++)
			{
				if($('#txtRecvQnty_'+i).val()*1>0)
				{
					var wo_qnty=$('#woQty_'+i).text()*1;
					var over_receive_limit="<?= $over_receive_limit;?>";
					var cbo_payment_over_recv="<?= $cbo_payment_over_recv;?>";
					if(over_receive_limit>0)
					{ 
						var over_receive_limit_qnty=((over_receive_limit/100)* wo_qnty);
					}
					else var over_receive_limit_qnty=0;
					var recv_bal=(($('#tdRcvQnty_'+i).attr("title")*1)+over_receive_limit_qnty);
					recv_bal=recv_bal.toFixed(4);
					var recv_qnty=$('#txtRecvQnty_'+i).val()*1;

					//DID AS PER SAYED SIR INSTRACTION
					if(receive_basis==6){
						total_receive=total_receive*1+recv_qnty;
					}
					else
					{
						if(recv_qnty>recv_bal  && cbo_payment_over_recv==0)
						{
							alert("Receive Quantity Not Allow Over Balance" + " rcv qnty ="+ recv_qnty + " rcv balance ="+ recv_bal);
							$('#txtRecvQnty_'+i).val("");
						}
						else
						{
							total_receive=total_receive*1+recv_qnty;
						}
					}				
				}
			}
			
			$('#total_recieve').html(total_receive.toFixed(4));
		}
		
		var selected_id = new Array();
		
		function check_all_data() 
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function set_all()
		{
			var old=document.getElementById('txt_po_row_id').value;
			if(old!="")
			{   
				old=old.split(",");
				for(var i=0; i<old.length; i++)
				{  
					js_set_value( old[i],0 ) 
				}
			}
		}
		
		function js_set_value( str) 
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			
			$('#po_id').val( id );
		}
		
		function show_trims_recv() 
		{ 
			var po_id=$('#po_id').val();

			show_list_view ( po_id+'**'+'1'+'**'+'<? echo $item_group; ?>'+'**'+<? echo $item_description; ?>+'**'+<? echo $brand_supref; ?>+'**'+'<? echo $order_uom; ?>'+'**'+'<? echo $receive_basis; ?>'+'**'+'<? echo $save_data; ?>'+'**'+'<? echo $gmts_color_id; ?>'+'**'+'<? echo $gmts_size_id; ?>'+'**'+'<? echo $item_color_id; ?>'+'**'+'<? echo $item_size; ?>'+'**'+'<? echo $booking_pi_id; ?>'+'**'+'<? echo $txt_hidden_rate; ?>', 'po_popup', 'search_div', 'trims_receive_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);');
		}
		
		function hidden_field_reset()
		{
			$('#po_id').val('');
			$('#save_string').val( '' );
			$('#tot_trims_qnty').val( '' );
			selected_id = new Array();
		}
		
		function fnc_close()
		{
			var save_string='';	 var tot_trims_qnty=0; var po_id_array = new Array(); var po_no='';
			var total_balance=0;
			var i=0;
			$("#tbl_list_search").find('tbody tr').each(function()
			{
				if(i!=0) // for skip filtergrid
				{
					var txtPoId=$(this).find('input[name="txtPoId[]"]').val();
					var txtPoName=$(this).find('input[name="txtPoName[]"]').val();
					var txtRecvQnty=$(this).find('input[name="txtRecvQnty[]"]').val();
					var txtTotRecvQnty=$(this).find('input[name="txtTotRecvQnty[]"]').val();
					var txtRcvBalance=$(this).find('input[name="txtRcvBalance[]"]').val();
					total_balance+=	txtRcvBalance*1;			
					tot_trims_qnty +=txtRecvQnty*1;
					if(txtRecvQnty*1>0)
					{
						if(save_string=="")
						{
							save_string=txtPoId+"_"+txtRecvQnty;
						}
						else
						{
							save_string+=","+txtPoId+"_"+txtRecvQnty;
						}
						
						if(jQuery.inArray( txtPoId, po_id_array) == -1 ) 
						{
							po_id_array.push(txtPoId);
							if(po_no=="") po_no=txtPoName; else po_no+=","+txtPoName;
						}
					}
				}
				i++;
			});
			
			$('#save_string').val( save_string );
			$('#tot_trims_qnty').val( tot_trims_qnty.toFixed(4));
			$('#all_po_id').val( po_id_array );
			$('#all_po_no').val( po_no );
			$('#all_balance').val( total_balance );
			
			parent.emailwindow.hide();
		}
    </script>

	</head>

	<body>
		<? 
		if($type!=1)
		{
		?>
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:640px;margin-left:10px">
				<input type="hidden" name="save_string" id="save_string" class="text_boxes" value="">
				<input type="hidden" name="tot_trims_qnty" id="tot_trims_qnty" class="text_boxes" value="">
				<input type="hidden" name="all_po_id" id="all_po_id" class="text_boxes" value="">
				<input type="hidden" name="all_po_no" id="all_po_no" class="text_boxes" value="">
				<input type="hidden" name="all_balance" id="all_balance" class="text_boxes" value="">
		<?
		}
		
		if((($receive_basis==1 && $wo_pi_basis_id==2) || $receive_basis==4 || $receive_basis==6 ) && $type!=1)
		{
		?>
			<table cellpadding="0" cellspacing="0" width="640" class="rpt_table" border="1" rules="all">
				<thead>
					<th>Buyer</th>
					<th>Search By</th>
					<th>Search</th>
					<th>
						<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
						<input type="hidden" name="po_id" id="po_id" value="">
					</th> 
				</thead>
				<tr class="general">
					<td align="center">
						<?
							echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",$data[0] ); 
						?>       
					</td>
					<td align="center">	
						<?
							$search_by_arr=array(1=>"PO No",2=>"Job No",3=>"Style Ref." );
							echo create_drop_down( "cbo_search_by", 170, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
					</td>                 
					<td align="center">				
						<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
					</td> 						
					<td align="center">
						<input type="button" name="button2" class="formbutton" value="Show" onClick="fn_show_check();" style="width:100px;" />
					</td>
				</tr>
			</table>
			<div id="search_div" style="margin-top:10px">
				<?
				if($save_data!="")
				{
				?>
					<div style="margin-left:10px; margin-top:10px">
						<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="670">
							<thead>
								<th width="80">PO No</th>
								<th width="70">Ship Date</th>
								<th width="60">Style</th>
								<th width="80">Garments Qty.</th>
								<th width="110">Total Receive Qty.</th>
								<th width="60">UOM</th>
								<th width="115">Receive Qty.</th>
							</thead>
						</table>
						<div style="width:690px; max-height:280px; overflow-y:scroll" id="list_container" align="left"> 
							<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="670" id="tbl_list_search">  
								<tbody>

								<? 
								$i=1; $tot_trims_receive_qnty=0;

								$explSaveData = explode(",",$save_data); 	
								for($z=0;$z<count($explSaveData);$z++)
								{
									if ($i%2==0)  
										$bgcolor="#E9F3FF";
									else
										$bgcolor="#FFFFFF";
										
									$po_wise_data = explode("_",$explSaveData[$z]);
									$order_id=$po_wise_data[0];
									$recv_qnty=$po_wise_data[1];
									
									if($item_size!="" || $db_type==0)
									{
										$item_size_cond="a.item_size='$item_size'";
									}
									else $item_size_cond="a.item_size is null";
									
									if($brand_supref!="" || $db_type==0)
									{
										$brand_supref_cond="a.brand_supplier='$brand_supref'";
									}
									else $brand_supref_cond="a.brand_supplier is null";
									
									if($db_type==2 && $gmts_size_id=="")
									{
										$gmts_size_field="nvl(a.gmts_size_id,0)";
										$gmts_size_id=0;
									}
									else
									{
										$gmts_size_field="a.gmts_size_id";
									}
									
									if($db_type==2 && $gmts_color_id=="")
									{
										$gmts_color_field="nvl(a.gmts_color_id,0)";
										$gmts_color_id=0;
									}
									else
									{
										$gmts_color_field="a.gmts_color_id";
									}
									
									if($db_type==2 && $item_color_id=="")
									{
										$item_color_field="nvl(a.item_color,0)";
										$item_color_id=0;
									}
									else
									{
										$item_color_field="a.item_color";
									}

									if($txt_hidden_rate>0) $rate_cond=" and ROUND(a.rate,4)=ROUND($txt_hidden_rate,4)";
									
									if($receive_basis==1)
									{
										$tot_recv_qnty=return_field_value("sum(b.quantity) as qnty","inv_receive_master m, inv_trims_entry_dtls a, order_wise_pro_details b","m.id=a.mst_id and m.receive_basis=1 and m.booking_id='$booking_pi_id' and a.id=b.dtls_id and a.item_group_id='$item_group' and a.item_description=$item_description and $brand_supref_cond and $gmts_color_field='$gmts_color_id' and $item_color_field='$item_color_id' and $gmts_size_field='$gmts_size_id' and $item_size_cond and b.entry_form=24 and b.trans_type=1 and b.po_breakdown_id='$order_id' $rate_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","qnty");
									}
									else
									{
										$tot_recv_qnty=return_field_value("sum(b.quantity) as qnty","inv_receive_master m, inv_trims_entry_dtls a, order_wise_pro_details b","m.id=a.mst_id and m.receive_basis=$receive_basis and m.entry_form=24 and a.id=b.dtls_id and a.item_group_id='$item_group' and a.item_description=$item_description and $brand_supref_cond and $gmts_color_field='$gmts_color_id' and $item_color_field='$item_color_id' and $gmts_size_field='$gmts_size_id' and $item_size_cond and b.entry_form=24 and b.trans_type=1 and b.po_breakdown_id='$order_id' $rate_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","qnty");
									}

									$po_data=sql_select("select a.style_ref_no,b.id, b.po_number, b.po_quantity,b.pub_shipment_date from wo_po_break_down b,wo_po_details_master a where b.job_no_mst=a.job_no and b.id=$order_id order by b.pub_shipment_date");
									
									$tot_trims_receive_qnty+=$recv_qnty;
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
										<td width="80">
											<p><? echo $po_data[0][csf('po_number')]; ?></p>
											<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $po_data[0][csf('id')]; ?>">
											<input type="hidden" name="txtPoName[]" id="txtPoName_<? echo $i; ?>" value="<? echo $po_data[0][csf('po_number')]; ?>">
										</td>
										<td width="70"><p><? echo change_date_format($po_data[0][csf('pub_shipment_date')]); ?></p></td>
										<td width="60"><p><? echo $po_data[0][csf('style_ref_no')]; ?></p></td>
										<td align="right" width="80"><? echo $po_data[0][csf('po_quantity')]; ?></td>
										<td width="110" align="right">
											<? echo number_format($tot_recv_qnty); ?>
											<input type="hidden" name="txtTotRecvQnty[]" id="txtTotRecvQnty_<? echo $i; ?>" value="<? echo $tot_recv_qnty; ?>">
										</td>
										<td width="60" align="center"><? echo $unit_of_measurement[$order_uom]; ?></td>
										<td align="right" width="115">
											<input type="text" name="txtRecvQnty[]" id="txtRecvQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:100px" value="<? echo $recv_qnty; ?>" onKeyUp="calculate_total();">
										</td>
									</tr>
								<? 
								$i++;
								}
								?>
								</tbody>
								<tfoot class="tbl_bottom">
									<td colspan="6">Total</td>
									<td id="total_recieve"><? echo $tot_trims_receive_qnty; ?></td>
								</tfoot>
							</table>
						</div>
						<table width="620">
							<tr>
								<td align="center" >
									<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
								</td>
							</tr>
						</table>
					</div>
				<?
				}
				?>
			</div>
		<?
		}
		else //|| $wo_pi_basis_id==2
		{
			//echo "test3";die;
			if($receive_basis==2 || $wo_pi_basis_id==1) 
			{
				$style='';
				$disabled="";
			}
			else 
			{
				$style='style="display:none"';
				$disabled="disabled";
			}
			?>
			<div style="margin-left:10px; margin-top:5px">
				<table cellpadding="0" cellspacing="0" rules="all" width="590" align="center" id="tbl_prop" <? echo $style; ?> >
					<tr>
						<td>&nbsp;&nbsp;&nbsp;</td>
						<td><b>Proportionately</b></td>
						<td><b>Ship Date Wise</b></td>
					</tr>
					<tr>
						<td width="250" align="right"><b>Total Receive Qty : &nbsp;&nbsp;</b></td>
						<td><input type="text" name="txt_prop_trims_qty" id="txt_prop_trims_qty" class="text_boxes_numeric" style="width:100px" onBlur="distribute_qnty()" <? echo $disabled; ?> /></td>
						<td><input type="text" name="txt_prop_ship_trims_qty" id="txt_prop_ship_trims_qty" class="text_boxes_numeric" style="width:100px" onBlur="distribute_ship_qnty()" <? echo $disabled; ?> /></td>
					</tr>
				</table>
				<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="670">
					<thead>
						<th width="80">PO No</th>
						<? 
						if($receive_basis==2) 
						{
							echo "<th width='65'>Ship Date</th>";
							echo "<th width='60'>Style</th>";
							echo "<th width='80'>Garments Qty.</th>";
							echo "<th width='80'>WO Qty.</th>";
							echo "<th width='80'>Total Receive Qty.</th>";
							echo "<th width='80'>Balance</th>";
							echo "<th width='50'>UOM</th>";
							echo "<th>Receive Qty.</th>";
						}
						else
						{
							echo "<th width='65'>Ship Date</th>";
							echo "<th width='90'>Style</th>";
							echo "<th width='90'>PI Qty.</th>";
							echo "<th width='90'>Total Receive Qty.</th>";
							echo "<th width='80'>Balance</th>";
							echo "<th width='60'>UOM</th>";
							echo "<th>Receive Qty.</th>";
						}
						?>
					</thead>
				</table>
				<div style="width:690px; max-height:280px; overflow-y:scroll" id="list_container" align="left"> 
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="670" id="tbl_list_search">  
						<tbody>
						<? 
						$i=1; $tot_trims_receive_qnty=0; $po_array=array();
						
						if($item_size!="" || $db_type==0)
						{
							$item_size_cond="trim(a.item_size)='".trim(str_replace("'","",$item_size))."'";
							$item_size_cond2="trim(c.item_size)='".trim(str_replace("'","",$item_size))."'";
							$item_size_cond4="trim(b.item_size)='".trim(str_replace("'","",$item_size))."'";
						}
						else 
						{
							$item_size_cond="a.item_size is null";
							$item_size_cond2="c.item_size is null";
							$item_size_cond4="b.item_size is null";
						}
						
						if(trim(str_replace("'","",$brand_supref))!="" || $db_type==0)
						{
							$brand_supref_cond="trim(a.brand_supplier)='".trim(str_replace("'","",$brand_supref))."'";
							$brand_supref_cond2="trim(c.brand_supplier)='".trim(str_replace("'","",$brand_supref))."'";
							$brand_supref_cond4="trim(b.brand_supplier)='".trim(str_replace("'","",$brand_supref))."'";
						}
						else 
						{
							$brand_supref_cond="a.brand_supplier is null";
							$brand_supref_cond2="c.brand_supplier is null";
							$brand_supref_cond4="b.brand_supplier is null";
						}
						
						if($db_type==2 && ($gmts_size_id=="" || $gmts_size_id==0))
						{
							$gmts_size_field="nvl(a.gmts_size_id,0)";
							$gmts_size_field2="nvl(c.gmts_sizes,0)";
							$gmts_size_field3="nvl(a.gmts_size,0)";
							$gmts_size_id=0;
							$gmts_size_field4="nvl(b.size_id,0)";
						}
						else
						{
							$gmts_size_field="a.gmts_size_id";
							$gmts_size_field2="c.gmts_sizes";
							$gmts_size_field3="a.gmts_size";
							$gmts_size_field4="b.size_id";
						}
						
						if($db_type==2 && ($gmts_color_id=="" || $gmts_color_id==0))
						{
							$gmts_color_field="nvl(a.gmts_color_id,0)";
							$gmts_color_field2="nvl(c.color_number_id,0)";
							$gmts_color_id=0;
							$gmts_color_field4="nvl(b.color_id,0)";
						}
						else
						{
							$gmts_color_field="a.gmts_color_id";
							$gmts_color_field2="c.color_number_id";
							$gmts_color_field4="b.color_id";
						}
						
						if($db_type==2 && ($item_color_id==""|| $item_color_id==0))
						{
							$item_color_field="nvl(a.item_color,0)";
							$item_color_field2="nvl(c.item_color,0)";
							$item_color_id=0;
							$item_color_field4="nvl(b.item_color,0)";
						}
						else
						{
							$item_color_field="a.item_color";
							$item_color_field2="c.item_color";
							$item_color_field4="b.item_color";
						}
						
						$conver_fact_sql=sql_select("select id, conversion_factor from lib_item_group where item_category=4 and id=$item_group");
						$lib_conversion_factor=$conver_fact_sql[0][csf("conversion_factor")];
						
						if($save_data!="" && (($receive_basis==1 && $wo_pi_basis_id==2) || $receive_basis==4 || $receive_basis==6))
						{
							//$po_id = explode(",",$po_id);
							
							$explSaveData = explode(",",$save_data); $po_array=array();	
							for($z=0;$z<count($explSaveData);$z++)
							{
								$po_wise_data = explode("_",$explSaveData[$z]);

								$order_id=$po_wise_data[0];
								$recv_qnty=$po_wise_data[1];
								
								$po_array[$order_id]=$recv_qnty;
							}
							//echo "select a.style_ref_no as style_ref, b.id, b.po_number, b.po_quantity,b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where  a.job_no=b.job_no_mst and b.id in ($po_id) order by b.pub_shipment_date";die;
							$data_array=sql_select("select a.style_ref_no as style_ref, b.id, b.po_number, b.po_quantity,b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where  a.job_no=b.job_no_mst and b.id in ($po_id) order by b.pub_shipment_date");
							foreach($data_array as $row)
							{
								if ($i%2==0)  
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
								
								$recv_qnty=$po_array[$row[csf('id')]];
								$tot_trims_receive_qnty+=$recv_qnty;
								
								if($txt_hidden_rate>0) $rate_cond=" and ROUND(a.rate,4)=ROUND($txt_hidden_rate,4)";
								if($receive_basis==1)
								{
									$tot_recv_qnty=return_field_value("sum(b.quantity) as qnty","inv_receive_master m, inv_trims_entry_dtls a, order_wise_pro_details b","m.id=a.mst_id and m.receive_basis=1 and m.booking_id='$booking_pi_id' and a.id=b.dtls_id and a.item_group_id='$item_group' and a.item_description=$item_description and $brand_supref_cond and $gmts_color_field='$gmts_color_id' and $item_color_field='$item_color_id' and $gmts_size_field='$gmts_size_id' and $item_size_cond and b.entry_form=24 and b.trans_type=1 and b.po_breakdown_id='".$row[csf('id')]."' $rate_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","qnty");
								}
								else
								{
									$tot_recv_qnty=return_field_value("sum(b.quantity) as qnty","inv_receive_master m, inv_trims_entry_dtls a, order_wise_pro_details b","m.id=a.mst_id and m.receive_basis=$receive_basis and m.entry_form=24 and a.id=b.dtls_id and a.item_group_id='$item_group' and a.item_description=$item_description and $brand_supref_cond and $gmts_color_field='$gmts_color_id' and $item_color_field='$item_color_id' and $gmts_size_field='$gmts_size_id' and $item_size_cond and b.entry_form=24 and b.trans_type=1 and b.po_breakdown_id='".$row[csf('id')]."' $rate_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","qnty");
								}
								
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
									<td width="80">
										<p><? echo $row[csf('po_number')]; ?></p>
										<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
										<input type="hidden" name="txtPoName[]" id="txtPoName_<? echo $i; ?>" value="<? echo $row[csf('po_number')]; ?>">
									</td>
									<td width="70"><p><? echo change_date_format($row[csf('pub_shipment_date')]); ?></p></td>
									<td width="60"><p><? echo $row[csf('style_ref')]; ?></p></td>
									
									<td width="100" align="right"><? echo $row[csf('po_quantity')]; ?></td>
									<td width="110" align="right">
										<? echo number_format($tot_recv_qnty,4,'.',''); ?>
										<input type="hidden" name="txtTotRecvQnty[]" id="txtTotRecvQnty_<? echo $i; ?>" value="<? echo $tot_recv_qnty; ?>">
									</td>
									<td width="50" align="center"><? echo $unit_of_measurement[$order_uom]; ?></td>
									<td align="right" width="115" title="<?= $rcv_balance ?>" id="tdRcvQnty_<? echo $i; ?>">
										<input type="text" name="txtRecvQnty[]" id="txtRecvQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:100px" value="<? echo $recv_qnty; ?>" onKeyUp="calculate_total();">
									</td>
								</tr>
								<? 
								$i++;
							}
						}
						else if($save_data!="" && ($receive_basis==2 || $receive_basis==1))
						{
							$explSaveData = explode(",",$save_data); $order_data_array=array();	
							for($z=0;$z<count($explSaveData);$z++)
							{
								$po_wise_data = explode("_",$explSaveData[$z]);
								$order_data_array[$po_wise_data[0]]=$po_wise_data[1];
							}
							//if($db_type==2) $group_con="LISTAGG(cast(work_order_no as varchar2(4000)), ',') WITHIN GROUP (ORDER BY work_order_no) as work_order_no";else $group_con="group_concat(work_order_no)";
							
							if($receive_basis==1)
							{
								
									$sql_data="select b.work_order_dtls_id from com_pi_master_details a,com_pi_item_details b where a.id=b.pi_id and a.id=$booking_pi_id and a.item_category_id in(4) and b.status_active=1 and b.is_deleted=0";
									$booking_no='';
									$sql_result=sql_select($sql_data);
									foreach($sql_result as $row)
									{
										if($booking_no=='') $booking_no=$row[csf('work_order_dtls_id')];else $booking_no.=",".$row[csf('work_order_dtls_id')];
									}
									$booking_no=rtrim($booking_no,",");
										$book_id_cond="";
										if($booking_no!="") 
										{
											//echo $po_id=substr($po_id,0,-1);po_break_down_id
											if($db_type==0) $book_id_cond="and b.id in(".$booking_no.")";
											else
											{
												$b_ids=explode(",",$booking_no);
												if(count($b_ids)>990)
												{
													$book_id_cond="and (";
													$b_ids=array_chunk($b_ids,990);
													$z=0;
													foreach($b_ids as $id)
													{
														$id=implode(",",$id);
														if($z==0) $book_id_cond.=" b.id in(".$id.")";
														else $book_id_cond.=" or b.id in(".$id.")";
														$z++;
													}
													$book_id_cond.=")";
												}
												else $book_id_cond="and b.id in(".$booking_no.")";
											}
										}
							}
							
							
							//echo $gmts_size_field2."==".$gmts_size_id;
							if($sensitivity=='') $sensitivity=0; else $sensitivity=$sensitivity;
							$rate_cond="";
							if($txt_hidden_rate>0) $rate_cond=" and ROUND(c.rate,4)=ROUND($txt_hidden_rate,4)";
							
							if(trim(str_replace("'","",$item_description))=="")
							{
								if($db_type==0) $description_cond=" and trim(c.description)=''";
								else $description_cond=" and trim(c.description) is null";
							}
							else
							{
								$description_cond=" and trim(c.description)='".trim(str_replace("'","",$item_description))."'";
							}
							
							if($receive_basis==1)
							{
							$po_sql="select a.id, a.pub_shipment_date,a.po_number,d.style_ref_no as style_ref, a.po_quantity, c.cons as qty from wo_po_break_down a, wo_booking_dtls b, wo_trim_book_con_dtls c,wo_po_details_master d where a.id=b.po_break_down_id and b.id=c.wo_trim_booking_dtls_id and d.job_no=a.job_no_mst and b.job_no=a.job_no_mst  and b.trim_group='$item_group' $description_cond and $brand_supref_cond2 and $gmts_color_field2='$gmts_color_id' and $gmts_size_field2='$gmts_size_id' and $item_color_field2='$item_color_id' and $item_size_cond2 and c.cons>0 $rate_cond and b.status_active=1 and b.is_deleted=0 $book_id_cond order by a.pub_shipment_date";
							}
							else if($receive_basis==2)
							{
								$po_sql="select a.id, a.pub_shipment_date,a.po_number,d.style_ref_no as style_ref, sum(distinct a.po_quantity) as po_quantity, sum(c.cons) as qty  from wo_po_break_down a, wo_booking_dtls b, wo_trim_book_con_dtls c,wo_po_details_master d where a.id=b.po_break_down_id and b.id=c.wo_trim_booking_dtls_id and d.job_no=a.job_no_mst and b.job_no=a.job_no_mst and b.booking_no='$booking_no' and b.sensitivity='$sensitivity' and b.trim_group='$item_group' $description_cond and $brand_supref_cond2 and $gmts_color_field2='$gmts_color_id' and $gmts_size_field2='$gmts_size_id' and $item_color_field2='$item_color_id' and $item_size_cond2 and c.cons>0 $rate_cond and b.status_active=1 and b.is_deleted=0 group by a.id, a.pub_shipment_date,a.po_number,d.style_ref_no  order by a.pub_shipment_date";
							}
							//echo $po_sql;
							$nameArray=sql_select($po_sql); $rate_cond_rcv="";
							foreach($nameArray as $row)
							{  
								if ($i%2==0)  
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
								//if($txt_rate!="") $rate_cond_rcv=" and ROUND(a.rate,2)='".number_format($txt_rate,2,'.','')."'";
								if($txt_hidden_rate>0) $rate_cond_rcv=" and ROUND(a.rate,4)=ROUND($txt_hidden_rate,4)";
								$tot_recv_qnty=return_field_value("sum(b.quantity) as qnty","inv_receive_master m, inv_trims_entry_dtls a, order_wise_pro_details b","m.id=a.mst_id and m.receive_basis=$receive_basis and m.entry_form=24 and m.booking_id='$booking_pi_id' and a.id=b.dtls_id and a.item_group_id='$item_group' and a.item_description=$item_description and $brand_supref_cond and $gmts_color_field='$gmts_color_id' and $item_color_field='$item_color_id' and $gmts_size_field='$gmts_size_id' and $item_size_cond and b.entry_form=24 and b.trans_type=1 and b.po_breakdown_id='".$row[csf('id')]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $rate_cond_rcv","qnty");
								$rcvRtn_qty=return_field_value("sum(b.quantity) as qnty","product_details_master a, inv_transaction t, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d","a.id=b.prod_id and a.id=t.prod_id and t.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.id=b.prod_id and  b.po_breakdown_id ='".$row[csf('id')]."' and a.item_category_id=4 and b.entry_form in(49) and t.transaction_type=3 and pi_wo_batch_no ='$booking_pi_id' and a.item_group_id='$item_group' and a.item_description=$item_description and $brand_supref_cond and $item_color_field='$item_color_id' and $gmts_size_field3='$gmts_size_id' and $item_size_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and t.status_active=1 and t.is_deleted=0 and c.status_active=1 and c.is_deleted=0","qnty");
								$recv_qnty=$order_data_array[$row[csf('id')]];
								$tot_trims_receive_qnty+=$recv_qnty;
								$tot_wo_qty+=$row[csf('qty')];
								$only_rcv_qnty=$tot_recv_qnty;
								$tot_recv_qnty=$tot_recv_qnty-$rcvRtn_qty;
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
									<td width="80">
										<p><? echo $row[csf('po_number')]; ?></p>
										<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
										<input type="hidden" name="txtPoName[]" id="txtPoName_<? echo $i; ?>" value="<? echo $row[csf('po_number')]; ?>">
									</td>
									<td width='65'><p><? echo change_date_format($row[csf('pub_shipment_date')]); ?></p></td> 
									<?
									if($receive_basis==2) 
									{
										?>
										<td width='60'><p><? echo $row[csf('style_ref')]; ?></p></td>
										<td width='80' align='right'><? echo $row[csf('po_quantity')]; ?></td>
										<td width='80' align='right' id="woQty_<? echo $i; ?>"><? echo number_format($row[csf('qty')],4,'.',''); ?></td>
										<td width="80" align="right" title="<? echo "rcv qnty =".$only_rcv_qnty." : "."rcv rtn qnty =".$rcvRtn_qty; ?>">
											<? echo number_format($tot_recv_qnty,4,'.',''); ?>
											<input type="hidden" name="txtTotRecvQnty[]" id="txtTotRecvQnty_<? echo $i; ?>" value="<? echo $tot_recv_qnty; ?>">
										</td>
										<td width="80" align="right">
											<? $rcv_balance=($row[csf('qty')]+$recv_qnty)-$tot_recv_qnty; echo number_format($rcv_balance,4,'.',''); ?>
											<input type="hidden" name="txtRcvBalance[]" id="txtRcvBalance_<? echo $i; ?>" value="<? echo $rcv_balance; ?>">
										</td>
										<td width="60" align="center"><? echo $unit_of_measurement[$order_uom]; ?></td>
										<?
									}
									if($wo_pi_basis_id==1) 
									{
										?>
										<td width='90'><p><? echo $row[csf('style_ref')]; ?></p></td>
										<td width='90' align='right' id="woQty_<? echo $i; ?>"><? echo number_format($row[csf('qty')],4,'.',''); ?></td>
										<td width="90" align="right" title="<? echo "rcv qnty =".$only_rcv_qnty." : "."rcv rtn qnty =".$rcvRtn_qty; ?>">
											<? echo number_format($tot_recv_qnty,4,'.',''); ?>
											<input type="hidden" name="txtTotRecvQnty[]" id="txtTotRecvQnty_<? echo $i; ?>" value="<? echo $tot_recv_qnty; ?>">
										</td>
										<td width="80" align="right">
											<? $rcv_balance=($row[csf('qty')]+$recv_qnty)-$tot_recv_qnty; echo number_format($rcv_balance,4,'.',''); ?>
											<input type="hidden" name="txtRcvBalance[]" id="txtRcvBalance_<? echo $i; ?>" value="<? echo $rcv_balance; ?>">
										</td>
										<td width="60" align="center"><? echo $unit_of_measurement[$order_uom]; ?></td>
										<?
									}
									/* else
									{
										?>
										<td width='90'><p><? echo $row[csf('style_ref')]; ?></p></td>
										<td width='90' align='right' id="woQty_<? echo $i; ?>"><? echo number_format($row[csf('qty')],4,'.',''); ?></td>
										<td width="90" align="right" title="<? echo "rcv qnty =".$only_rcv_qnty." : "."rcv rtn qnty =".$rcvRtn_qty; ?>">
											<? echo number_format($tot_recv_qnty,4,'.',''); ?>
											<input type="hidden" name="txtTotRecvQnty[]" id="txtTotRecvQnty_<? echo $i; ?>" value="<? echo $tot_recv_qnty; ?>">
										</td>
										<td width="80" align="right">
											<? $rcv_balance=$row[csf('qty')]-$tot_recv_qnty; echo number_format($rcv_balance,4,'.',''); ?>
											<input type="hidden" name="txtRcvBalance[]" id="txtRcvBalance_<? echo $i; ?>" value="<? echo $rcv_balance; ?>">
										</td>
										<td width="60" align="center"><? echo $unit_of_measurement[$order_uom]; ?></td>
										<?
									} */
									?>
									<td align="right" title="<?= $rcv_balance ?>" id="tdRcvQnty_<? echo $i; ?>">
										<input type="text" name="txtRecvQnty[]" id="txtRecvQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px" value="<? echo $recv_qnty; ?>" onKeyUp="calculate_total();">
									</td>
								</tr>
							<? 
							$i++; 
							} 
						}
						else
						{
							if($type==1)
							{
								if($po_id!="")
								{
									$po_sql="select b.id, b.po_number,a.style_ref_no as style_ref, b.po_quantity,b.pub_shipment_date from wo_po_break_down b,wo_po_details_master a where a.job_no=b.job_no_mst and b.id in ($po_id) order by b.pub_shipment_date";
								}
							}
							else
							{
								//echo $booking_pi_id.'dddddddd'.$type; $receive_basis==1 && $wo_pi_basis_id==1
								if($receive_basis==1)
								{
									$sql_data="select b.work_order_dtls_id from com_pi_master_details a,com_pi_item_details b where a.id=b.pi_id and a.id=$booking_pi_id and a.item_category_id in(4) and b.status_active=1 and b.is_deleted=0 and b.item_group='$item_group' and trim(b.item_description)='".trim(str_replace("'","",$item_description))."' and $brand_supref_cond4  and $gmts_size_field4='$gmts_size_id' and $gmts_color_field4='$gmts_color_id' and $item_color_field4='$item_color_id' and $item_size_cond4";
									//echo $sql_data;
									$booking_no='';
									$sql_result=sql_select($sql_data);
									foreach($sql_result as $row)
									{
										if($temp_wo_id_check[$row[csf('work_order_dtls_id')]]=="" && $row[csf('work_order_dtls_id')])
										{
											$temp_wo_id_check[$row[csf('work_order_dtls_id')]]=$row[csf('work_order_dtls_id')];
											$booking_id_arr[$row[csf('work_order_dtls_id')]]=$row[csf('work_order_dtls_id')];
										}
									}
									//echo "<pre>";print_r($booking_id_arr);//die;
									$booking_no=implode(",",$booking_id_arr);
									//echo $booking_no.test;die;
									$book_id_cond="";
									if($booking_no!="") 
									{
										//echo $po_id=substr($po_id,0,-1);po_break_down_id
										if($db_type==0) $book_id_cond="and b.id in(".$booking_no.")";
										else
										{
											$b_ids=array_unique(explode(",",$booking_no));
											if(count($b_ids)>990)
											{
												$book_id_cond="and (";
												$b_ids=array_chunk($b_ids,990);
												$z=0;
												foreach($b_ids as $id)
												{
													$id=implode(",",$id);
													if($z==0) $book_id_cond.=" b.id in(".$id.")";
													else $book_id_cond.=" or b.id in(".$id.")";
													$z++;
												}
												$book_id_cond.=")";
											}
											else $book_id_cond="and b.id in(".$booking_no.")";
										}
									}
									//echo $booking_no;
								}
								
								
								if($sensitivity=='') $sensitivity=0; else $sensitivity=$sensitivity;
								
								// b.sensitivity='$sensitivity'   
								//echo $receive_basis==1 && $wo_pi_basis_id==1"; die;
								$rate_cond="";
								if($txt_hidden_rate>0) $rate_cond=" and ROUND(c.rate,4)=ROUND($txt_hidden_rate,4)";
								//if($txt_rate!="") $rate_cond=" and ROUND(c.rate,2)='".number_format($txt_rate,2,'.','')."'";
								if($receive_basis==1)
								{
									//$po_sql="select a.id, a.po_number, d.style_ref_no as style_ref,a.po_quantity,a.pub_shipment_date, c.cons as qty from wo_po_break_down a, wo_booking_dtls b, wo_trim_book_con_dtls c,wo_po_details_master d where a.id=b.po_break_down_id and b.id=c.wo_trim_booking_dtls_id  and d.job_no=a.job_no_mst and b.job_no=a.job_no_mst  and b.trim_group='$item_group' and trim(c.description)='".trim(str_replace("'","",$item_description))."' and $brand_supref_cond2  and $gmts_size_field2='$gmts_size_id' and $gmts_color_field2='$gmts_color_id' and $item_color_field2='$item_color_id' and $item_size_cond2 and c.cons>0 $rate_cond and b.status_active=1 and b.is_deleted=0 $book_id_cond order by a.pub_shipment_date";
									$po_sql="select a.id, a.po_number, d.style_ref_no as style_ref,a.po_quantity,a.pub_shipment_date, sum(c.cons) as qty  from wo_po_break_down a, wo_booking_dtls b, wo_trim_book_con_dtls c,wo_po_details_master d where a.id=b.po_break_down_id and b.id=c.wo_trim_booking_dtls_id  and d.job_no=a.job_no_mst and b.job_no=a.job_no_mst  and b.trim_group='$item_group' and trim(c.description)='".trim(str_replace("'","",$item_description))."' and $brand_supref_cond2  and $gmts_size_field2='$gmts_size_id' and $gmts_color_field2='$gmts_color_id' and $item_color_field2='$item_color_id' and $item_size_cond2 and c.cons>0 $rate_cond and b.status_active=1 and b.is_deleted=0 $book_id_cond group by a.id, a.po_number,d.style_ref_no, a.pub_shipment_date,a.po_quantity  order by a.pub_shipment_date ";
								}
								else if($receive_basis==2)
								{

									/*$po_sql="select a.id, a.po_number,d.style_ref_no as style_ref, a.pub_shipment_date,a.po_quantity, c.cons as qty from wo_po_break_down a, wo_booking_dtls b, wo_trim_book_con_dtls c,wo_po_details_master d where a.id=b.po_break_down_id and b.id=c.wo_trim_booking_dtls_id and d.job_no=a.job_no_mst and b.job_no=a.job_no_mst and b.booking_no='$booking_no' and b.sensitivity='$sensitivity' and b.trim_group='$item_group' and c.description='$item_description' and $brand_supref_cond2 and $gmts_color_field2='$gmts_color_id' and $gmts_size_field2='$gmts_size_id' and $item_color_field2='$item_color_id' and $item_size_cond2 and c.cons>0 and b.status_active=1 and b.is_deleted=0 order by a.pub_shipment_date";*/
									/*if(str_replace("'","",$item_description)!='') $item_description_con="and c.description=$item_description";else $item_description_con="";
									*/	
									if(trim(str_replace("'","",$item_description))!="" || $db_type==0)
									{
										$item_description_con="and trim(c.description)='".trim(str_replace("'","",$item_description))."'";
									}
									else 
									{
										$item_description_con=" and c.description is null";
									}
									$po_sql="select a.id, a.po_number,d.style_ref_no as style_ref, a.pub_shipment_date,a.po_quantity, sum(c.cons) as qty 
									from wo_po_break_down a, wo_booking_dtls b, wo_trim_book_con_dtls c,wo_po_details_master d 
									where a.id=b.po_break_down_id and b.id=c.wo_trim_booking_dtls_id and d.job_no=a.job_no_mst and b.job_no=a.job_no_mst and b.booking_no='$booking_no' and b.sensitivity='$sensitivity' and b.trim_group='$item_group' and $brand_supref_cond2 and $gmts_color_field2='$gmts_color_id' and $gmts_size_field2='$gmts_size_id' and $item_color_field2='$item_color_id' and $item_size_cond2 and c.cons>0 $rate_cond and b.status_active=1 and b.is_deleted=0 $item_description_con
									group by a.id, a.po_number,d.style_ref_no, a.pub_shipment_date,a.po_quantity  
									order by a.pub_shipment_date";
								}
							}
							
							/*$rcvRtn_qty_sql = "SELECT b.po_breakdown_id ,sum( b.quantity ) as recv_return_qty from product_details_master a, inv_transaction t, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d
							where a.id=b.prod_id and a.id=t.prod_id and t.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.id=b.prod_id and  b.po_breakdown_id in ($po_breakdown_ids) and a.item_category_id=4 and b.entry_form in(49) and t.transaction_type=3 and pi_wo_batch_no in($booking_pi_id)  and a.item_group_id='$item_group' and a.item_description=$item_description and $brand_supref_cond and $item_color_field='$item_color_id' and $gmts_size_field3='$gmts_size_id' and $item_size_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and t.status_active=1 and t.is_deleted=0 and c.status_active=1 and c.is_deleted=0
							group by b.po_breakdown_id";
							echo $rcvRtn_qty_sql;die;*/

							//echo $po_sql;//die;
							$nameArray=sql_select($po_sql);
							foreach($nameArray as $row)
							{  
								if ($i%2==0)  
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
								
								if(trim(str_replace("'","",$item_description)) !="" || $db_type==0)
								{
									$description_cond_rcv=" and trim(a.item_description)='".trim(str_replace("'","",$item_description))."'";
								}
								else
								{
									$description_cond_rcv=" and trim(a.item_description) is null";
								}
								if($receive_basis==1 || $receive_basis==2)
								{
									//if($txt_rate!="") $rate_cond_rcv=" and ROUND(a.rate,2)='".number_format($txt_rate,2,'.','')."'";
									if($txt_hidden_rate>0) $rate_cond_rcv=" and ROUND(a.rate,4)=ROUND($txt_hidden_rate,4)";	
									$tot_recv_qnty=return_field_value("sum(b.quantity) as qnty","inv_receive_master m, inv_trims_entry_dtls a, order_wise_pro_details b","m.id=a.mst_id and a.id=b.dtls_id and m.receive_basis=$receive_basis and m.entry_form=24 and a.booking_id='$booking_pi_id' and a.item_group_id='$item_group' $description_cond_rcv and $brand_supref_cond and $gmts_color_field='$gmts_color_id' and $item_color_field='$item_color_id' and $gmts_size_field='$gmts_size_id' and $item_size_cond and b.entry_form=24 and b.trans_type=1 and b.po_breakdown_id='".$row[csf('id')]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $rate_cond_rcv","qnty");
									$rcvRtn_qty=return_field_value("sum(b.quantity) as qnty","product_details_master a, inv_transaction t, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d","a.id=b.prod_id and a.id=t.prod_id and t.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.id=b.prod_id and  b.po_breakdown_id ='".$row[csf('id')]."' and a.item_category_id=4 and b.entry_form in(49) and t.transaction_type=3 and pi_wo_batch_no ='$booking_pi_id' and a.item_group_id='$item_group' $description_cond_rcv and $brand_supref_cond and $item_color_field='$item_color_id' and $gmts_size_field3='$gmts_size_id' and $item_size_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and t.status_active=1 and t.is_deleted=0 and c.status_active=1 and c.is_deleted=0","qnty");
								}
								else
								{
									
									$tot_recv_qnty=return_field_value("sum(b.quantity) as qnty","inv_receive_master m, inv_trims_entry_dtls a, order_wise_pro_details b","m.id=a.mst_id and a.id=b.dtls_id and m.receive_basis=$receive_basis and m.entry_form=24 and a.item_group_id='$item_group' $description_cond_rcv and $brand_supref_cond and $gmts_color_field='$gmts_color_id' and $item_color_field='$item_color_id' and $gmts_size_field='$gmts_size_id' and $item_size_cond and b.entry_form=24 and b.trans_type=1 and b.po_breakdown_id='".$row[csf('id')]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","qnty");
								}
								$only_rcv_qnty=$tot_recv_qnty;
								$tot_recv_qnty=$tot_recv_qnty-$rcvRtn_qty;
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
									<td width="80">
										<p><? echo $row[csf('po_number')]; ?></p>
										<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
										<input type="hidden" name="txtPoName[]" id="txtPoName_<? echo $i; ?>" value="<? echo $row[csf('po_number')]; ?>">
									</td>
									<td width="65" style="word-break:break-all"><P><? echo change_date_format($row[csf('pub_shipment_date')]); ?></P></td>
									<td width="90" style="word-break:break-all"><P><? echo $row[csf('style_ref')]; ?></P></td>
									<? 
										if($receive_basis==2) 
										{	
											$tot_wo_qty+=$row[csf('qty')];
											$tot_po_quantity+=$tot_recv_qnty;
											$total_rcv_balance+=$row[csf('qty')]-$tot_recv_qnty;
											echo "<td width='80' align='right'>".$row[csf('po_quantity')]."</td>";
											echo "<td width='80' align='right' id='woQty_$i'>".number_format($row[csf('qty')],4,'.','')."</td>";
										}
										elseif($receive_basis==1)
										{
											$tot_wo_qty+=$row[csf('qty')];
											$tot_po_quantity+=$tot_recv_qnty;
											$total_rcv_balance+=$row[csf('qty')]-$tot_recv_qnty;
											echo "<td width='90' align='right' id='woQty_$i'>".number_format($row[csf('qty')],4,'.','')."</td>";
										}
										else 
										{
											$tot_wo_qty+=$row[csf('qty')];
											echo "<td width='90' align='right'>".number_format($row[csf('qty')],4,'.','')."</td>";
										}
									?>
									<td width="90" align="right" title="<? echo "rcv qnty =".$only_rcv_qnty." : "."rcv rtn qnty =".$rcvRtn_qty; ?>">
										<? echo number_format($tot_recv_qnty,4,'.',''); ?>
										<input type="hidden" name="txtTotRecvQnty[]" id="txtTotRecvQnty_<? echo $i; ?>" value="<? echo $tot_recv_qnty; ?>">
									</td>
									<td width="80" align="right">
										<? $rcv_balance=$row[csf('qty')]-$tot_recv_qnty; echo number_format($rcv_balance,4,'.',''); ?>
										<input type="hidden" name="txtRcvBalance[]" id="txtRcvBalance_<? echo $i; ?>" value="<? echo $rcv_balance; ?>">
									</td>
									<td width="60" align="center"><? echo $unit_of_measurement[$order_uom]; ?></td>
									<td align="right" title="<?= $rcv_balance ?>" id="tdRcvQnty_<? echo $i; ?>"> 
										<input type="text" name="txtRecvQnty[]" id="txtRecvQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px" value="" onKeyUp="calculate_total();">
									</td>
								</tr>
							<? 
							$i++; 
							} 
						}
						?>
						</tbody>
						<tfoot class="tbl_bottom">
							<?
								if($receive_basis==2)
								{
								?>
									<td colspan="4">Total</td>
									<td id="value_tot_wo_qty"><? echo number_format($tot_wo_qty,4,'.',''); ?></td>
									<td><? echo number_format($tot_po_quantity,4,'.',''); ?></td>
									<td id="value_tot_rcv_balance"><? echo number_format($total_rcv_balance,4,'.',''); ?></td>
									<td>&nbsp;</td>
								<?
								}
								elseif($wo_pi_basis_id==1)
								{
								?>
									<td colspan="3">Total</td>
									<td id="value_tot_wo_qty"><? echo number_format($tot_wo_qty,4,'.',''); ?></td>
									<td><? echo number_format($tot_po_quantity,4,'.',''); ?></td>
									<td id="value_tot_rcv_balance"><? echo number_format($total_rcv_balance,4,'.',''); ?></td>
									<td>&nbsp;</td>
								<?
								}
								else
								{
								?>
									<td colspan="6">Total</td>
								<?
								}
							?>
							<td id="total_recieve"><? echo number_format($tot_trims_receive_qnty,2,'.',''); ?></td>
						</tfoot>
					</table>
				</div>
				<table width="620">
					<tr>
						<td align="center" >
							<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
						</td>
					</tr>
				</table>
			</div>
		<?
		}
		if($type!=1)
		{
		?>
			</fieldset>
		</form>
		<?
		}
		?>
	</body> 
	<script>
		var tableFilters = 
		{
			col_operation: {
			id:["value_tot_wo_qty","value_tot_rcv_balance"],
			col:[4,6],
			operation:["sum","sum"],
			write_method:["innerHTML","innerHTML"]
			}	
		}
		var tableFilters2 = 
		{
			col_operation: {
			id:["value_tot_wo_qty","value_tot_rcv_balance"],
			col:[3,5],
			operation:["sum","sum"],
			write_method:["innerHTML","innerHTML"]
			}	
		}
			
		var receive_basis='<?=$receive_basis;?>';
		var wo_pi_basis_id='<?=$wo_pi_basis_id;?>';
		if(receive_basis==2){
			setFilterGrid('tbl_list_search',-1,tableFilters);
		}
		else if(wo_pi_basis_id==1){
			setFilterGrid('tbl_list_search',-1,tableFilters2);
		}
		else{
			setFilterGrid('tbl_list_search',-1);
		}
	</script>          
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_po_search_list_view")
{
	$data = explode("_",$data);
	
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	
	if($search_by==1)
		$search_field='b.po_number';
	else if($search_by==2)
		$search_field='a.job_no';
	else
		$search_field='a.style_ref_no';	
		
	$company_id =$data[2];
	$buyer_id =$data[3];
	
	$all_po_id=$data[4];
	
	if($all_po_id!="")
		$po_id_cond=" or b.id in($all_po_id)";
	else 
		$po_id_cond="";
	
	$hidden_po_id=explode(",",$all_po_id);

	if($buyer_id==0) { echo "Please Select Buyer First."; die; }
	
	$sql = "select a.job_no, a.style_ref_no, a.order_uom, b.id, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_id and a.buyer_name=$buyer_id and $search_field like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0"; 
	//echo $sql;die; $po_id_cond
	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" >
            <thead>
                <th width="40">SL</th>
                <th width="100">Job No</th>
                <th width="110">Style No</th>
                <th width="110">PO No</th>
                <th width="90">PO Quantity</th>
                <th width="50">UOM</th>
                <th>Shipment Date</th>
            </thead>
        </table>
        <div style="width:618px; overflow-y:scroll; max-height:240px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" id="tbl_list_search" >
            <?
				$i=1; $po_row_id='';
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $selectResult)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
						
					if(in_array($selectResult[csf('id')],$hidden_po_id)) 
					{
						if($po_row_id=="") $po_row_id=$i; else $po_row_id.=",".$i;
					}
							
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
                        <td width="40" align="center">
                            <? echo $i; ?>
                            <input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>"/>
                        </td>	
                        <td width="100"><p><? echo $selectResult[csf('job_no')]; ?></p></td>
                        <td width="110"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
                        <td width="110"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
                        <td width="90" align="right"><? echo $selectResult[csf('po_qnty_in_pcs')]; ?></td> 
                        <td width="50" align="center"><p><? echo $unit_of_measurement[$selectResult[csf('order_uom')]]; ?></p></td>
                        <td align="center"><? echo change_date_format($selectResult[csf('pub_shipment_date')]); ?></td>	
                    </tr>
                    <?
                    $i++;
				}
			?>
				<input type="hidden" name="txt_po_row_id" id="txt_po_row_id" value="<? echo $po_row_id; ?>"/>
            </table>
        </div>
         <table width="620" cellspacing="0" cellpadding="0" style="border:none" align="center">
            <tr>
                <td align="center" height="30" valign="bottom">
                    <div style="width:100%"> 
                        <div style="width:50%; float:left" align="left">
                            <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                        </div>
                        <div style="width:50%; float:left" align="left">
                            <input type="button" name="close" onClick="show_trims_recv();" class="formbutton" value="Close" style="width:100px" />
                        </div>
                    </div>
                </td>
            </tr>
        </table>
	</div>           
<?
	
exit();
}

if($action=="show_ile_load_uom")
{
	$data=explode("_",$data);
	
	$uom=$trim_group_arr[$data[0]]['uom'];
	$company = $data[1];
	$source = $data[2];
	$rate = $data[3];
	
	$ile=return_field_value("standard","variable_inv_ile_standard","source='$source' and company_name='$company' and category=4 and status_active=1 and is_deleted=0");
	echo "document.getElementById('cbo_uom').value 	= '".$uom."';\n";
	// NOTE :- ILE=standard, ILE% = standard/100*rate
	
	if($ile<0 || $ile=='')
	{
		$ile_percentage=0; $ile=0;
	}
	else
	{
		$ile_percentage = number_format(($ile/100)*$rate,$dec_place[3],".","");
	}
	echo "document.getElementById('ile_td').innerHTML 	= 'ILE% ".$ile."';\n";
	echo "document.getElementById('txt_ile').value 	= '".$ile_percentage."';\n";
	
	exit();	
}

if($action=="put_balance_qnty")
{
	$data=explode("_",$data);
	
	$recieve_basis = $data[0];
	$bookingNo_piId = $data[1];
	$bookingNo_piNo = $data[2];
	$item_group = $data[3];
	$item_description ="'".trim($data[4])."'";
	$brand_supref = $data[5];
	$sensitivity = $data[6];
	$gmts_color_id = $data[7];
	$gmts_size_id = $data[8];
	$item_color_id = $data[9];
	$item_size = $data[10];
	$booking_without_order = $data[11];
	$order_id = $data[12];
	$booking_rate = $data[13];
	
	if($order_id=="") $order_id=0;
	
	//echo $item_description."===".$data[4];die;
	if($booking_rate>0) $rate_cond=" and ROUND(rate,4)=ROUND($booking_rate,4)";
	if($recieve_basis==1 || $recieve_basis==2)
	{
		if($recieve_basis==1)
		{
			if($db_type==0)
			{
				$sql="select quantity as qnty, rate from com_pi_item_details where pi_id='$bookingNo_piId' and item_group='$item_group' and item_description=$item_description and brand_supplier='".trim($brand_supref)."' and color_id='$gmts_color_id' and item_color='$item_color_id' and size_id='$gmts_size_id' and item_size='$item_size' $rate_cond and status_active=1 and is_deleted=0";
			}
			else
			{
				if($item_size=="") $item_size_cond="item_size is null"; else $item_size_cond="item_size='$item_size'";
				if($brand_supref=="") $brand_supref_cond="brand_supplier is null"; else $brand_supref_cond="brand_supplier='".trim($brand_supref)."'";
				if($gmts_size_id=="") $gmts_size_id=0;
				if($item_color_id=="") $item_color_id=0;
				if($gmts_color_id=="") $gmts_color_id=0;
				
				$sql="select sum(quantity) as qnty, avg(rate) as rate from com_pi_item_details where pi_id='$bookingNo_piId' and item_group='$item_group' and item_description=$item_description and nvl(color_id,0)='$gmts_color_id' and nvl(item_color,0)='$item_color_id' and nvl(size_id,0)='$gmts_size_id' and $brand_supref_cond and $item_size_cond $rate_cond and status_active=1 and is_deleted=0";
			}
		}
		else if($recieve_basis==2)
		{
			if($db_type==0)
			{
				if($booking_without_order==1)
				{
					$sql = "select trim_qty, rate from wo_non_ord_samp_booking_dtls where booking_no='$bookingNo_piNo' and trim_group='$item_group' and fabric_description=$item_description and barnd_sup_ref='$brand_supref' and gmts_color='$gmts_color_id' and gmts_size='$gmts_size_id' and fabric_color='$item_color_id' and item_size='$item_size' $rate_cond and status_active=1 and is_deleted=0";
				}
				else
				{
					$sql="select sum(c.cons) as qnty, c.rate from wo_booking_dtls b, wo_trim_book_con_dtls c where b.id=c.wo_trim_booking_dtls_id and b.booking_no='$bookingNo_piNo' and b.sensitivity='$sensitivity' and b.trim_group='$item_group' and c.description=$item_description and c.brand_supplier='".trim($brand_supref)."' and c.color_number_id='$gmts_color_id' and c.gmts_sizes='$gmts_size_id' and c.item_color='$item_color_id' and c.item_size='$item_size' $rate_cond and b.status_active=1 and b.is_deleted=0";
				}
			}
			else
			{
				if($item_size==""){
					$item_size_cond="c.item_size is null"; 
					$item_size_cond_samp="item_size is null";
				}else {
					$item_size_cond="c.item_size='$item_size'";
					$item_size_cond_samp="item_size='$item_size'";
				}
				
				if($brand_supref=="") {
					$brand_supref_cond="c.brand_supplier is null"; 
					$brand_supref_cond_samp="barnd_sup_ref is null"; 
				}else {
					$brand_supref_cond="c.brand_supplier='".trim($brand_supref)."'";
					$brand_supref_cond_samp="barnd_sup_ref='".trim($brand_supref)."'";
				}

				if($booking_rate>0) $rate_condition=" and ROUND(c.rate,4)=ROUND($booking_rate,4)";
				
				if($gmts_size_id=="") $gmts_size_id=0;
				if($item_color_id=="") $item_color_id=0;
				if($gmts_color_id=="") $gmts_color_id=0;
				if($order_id){$orderCon=" and c.po_break_down_id=$order_id";}

				
				if($booking_without_order==1)
				{
					$sql = "select trim_qty as qnty, rate from wo_non_ord_samp_booking_dtls where booking_no='$bookingNo_piNo' and trim_group='$item_group' and fabric_description=$item_description and nvl(gmts_color,0)='$gmts_color_id' and nvl(gmts_size,0)='$gmts_size_id' and nvl(fabric_color,0)='$item_color_id' and status_active=1 and is_deleted=0 and $item_size_cond_samp and $brand_supref_cond_samp $rate_cond";
				}
				else
				{
					$sql="select sum(c.cons) as qnty, avg(c.rate) as rate from wo_booking_dtls b, wo_trim_book_con_dtls c where b.id=c.wo_trim_booking_dtls_id and b.booking_no='$bookingNo_piNo' and b.sensitivity='$sensitivity' and b.trim_group='$item_group' and c.description=$item_description and nvl(c.color_number_id,0)='$gmts_color_id' and nvl(c.gmts_sizes,0)='$gmts_size_id' and nvl(c.item_color,0)='$item_color_id' and $brand_supref_cond and $item_size_cond  $orderCon $rate_condition and b.status_active=1 and b.is_deleted=0";
					//$sql="select c.cons as qnty, c.rate as rate from wo_booking_dtls b, wo_trim_book_con_dtls c where b.id=c.wo_trim_booking_dtls_id and b.booking_no='$bookingNo_piNo' and b.sensitivity='$sensitivity' and b.trim_group='$item_group' and c.description='$item_description' and nvl(c.color_number_id,0)='$gmts_color_id' and nvl(c.gmts_sizes,0)='$gmts_size_id' and nvl(c.item_color,0)='$item_color_id' and $brand_supref_cond and $item_size_cond and b.status_active=1 and b.is_deleted=0";
				}
				
				//echo $sql;die;
				
				/*if($sensitivity==1 || $sensitivity==3)
				{
					$qnty=return_field_value("sum(c.cons) as qnty","wo_booking_dtls b, wo_trim_book_con_dtls c","b.id=c.wo_trim_booking_dtls_id and b.booking_no='$bookingNo_piNo' and b.sensitivity='$sensitivity' and b.trim_group='$item_group' and c.description='$item_description' and c.color_number_id='$gmts_color_id' and c.item_color='$item_color_id' and $brand_supref_cond and $item_size_cond and b.status_active=1 and b.is_deleted=0","qnty");
				}
				else if($sensitivity==2)
				{
					$qnty=return_field_value("sum(c.cons) as qnty","wo_booking_dtls b, wo_trim_book_con_dtls c","b.id=c.wo_trim_booking_dtls_id and b.booking_no='$bookingNo_piNo' and b.sensitivity='$sensitivity' and b.trim_group='$item_group' and c.description='$item_description' and c.gmts_sizes='$gmts_size_id' and c.item_color='$item_color_id' and $brand_supref_cond and $item_size_cond and b.status_active=1 and b.is_deleted=0","qnty");
				}
				else if($sensitivity==4)
				{
					$qnty=return_field_value("sum(c.cons) as qnty","wo_booking_dtls b, wo_trim_book_con_dtls c","b.id=c.wo_trim_booking_dtls_id and b.booking_no='$bookingNo_piNo' and b.sensitivity='$sensitivity' and b.trim_group='$item_group' and c.description='$item_description' and c.color_number_id='$gmts_color_id' and c.gmts_sizes='$gmts_size_id' and c.item_color='$item_color_id' and $brand_supref_cond and $item_size_cond and b.status_active=1 and b.is_deleted=0","qnty");
				}
				else if($sensitivity==0)
				{
					$qnty=return_field_value("sum(c.cons) as qnty","wo_booking_dtls b, wo_trim_book_con_dtls c","b.id=c.wo_trim_booking_dtls_id and b.booking_no='$bookingNo_piNo' and b.sensitivity='$sensitivity' and b.trim_group='$item_group' and c.description='$item_description' and c.item_color='$item_color_id' and $brand_supref_cond and $item_size_cond and b.status_active=1 and b.is_deleted=0","qnty");
				}	*/
			}
		}
		//echo $sql;die;
		$result=sql_select($sql);
		$qnty=$result[0][csf('qnty')];
		$rate=$result[0][csf('rate')];
		
		if($recieve_basis==1 || $recieve_basis==2)
		{
			$rate_condition=" and ROUND(a.rate,4)=ROUND($booking_rate,4)";
			if($db_type==0)
			{
				$receive_qnty=return_field_value("sum(b.quantity) as qnty","inv_receive_master m, inv_trims_entry_dtls a, order_wise_pro_details b","m.id=a.mst_id and a.id=b.dtls_id and b.entry_form=24 and m.entry_form=24 and b.po_breakdown_id in($order_id) and m.receive_basis=$recieve_basis and m.booking_id='$bookingNo_piId' and a.item_group_id='$item_group' and a.item_description=$item_description and a.brand_supplier='".trim($brand_supref)."' and a.gmts_color_id='$gmts_color_id' and a.item_color='$item_color_id' and a.gmts_size_id ='$item_size_id' and a.item_size ='$item_size' $rate_condition and a.status_active=1 and a.is_deleted=0","qnty");
			}
			else
			{
				if($item_size=="") $item_size_cond="a.item_size is null"; else $item_size_cond="a.item_size='$item_size'";
				if($brand_supref=="") $brand_supref_cond="a.brand_supplier is null"; else $brand_supref_cond="a.brand_supplier='".trim($brand_supref)."'";
				if($gmts_size_id=="") $gmts_size_id=0;
				if($item_color_id=="") $item_color_id=0;
				if($gmts_color_id=="") $gmts_color_id=0;
				
				if($booking_without_order==1)
				{
					$receive_qnty=return_field_value("sum(a.receive_qnty) as qnty","inv_receive_master m, inv_trims_entry_dtls a","m.id=a.mst_id and m.entry_form=24 and m.receive_basis=$recieve_basis and m.booking_id='$bookingNo_piId' and a.item_group_id='$item_group' and a.item_description=$item_description and nvl(a.gmts_color_id,0)='$gmts_color_id' and nvl(a.item_color,0)='$item_color_id' and nvl(a.gmts_size_id,0)='$gmts_size_id' and $brand_supref_cond and $item_size_cond $rate_condition and a.status_active=1 and a.is_deleted=0","qnty");
				}
				else
				{
					$receive_qnty=return_field_value("sum(b.quantity) as qnty","inv_receive_master m, inv_trims_entry_dtls a, order_wise_pro_details b","m.id=a.mst_id and a.id=b.dtls_id and b.entry_form=24 and m.entry_form=24 and b.po_breakdown_id in($order_id) and m.receive_basis=$recieve_basis and m.booking_id='$bookingNo_piId' and a.item_group_id='$item_group' and a.item_description=$item_description and nvl(a.gmts_color_id,0)='$gmts_color_id' and nvl(a.item_color,0)='$item_color_id' and nvl(a.gmts_size_id,0)='$gmts_size_id' and $brand_supref_cond and $item_size_cond $rate_condition and a.status_active=1 and a.is_deleted=0","qnty");
				}
			}
			$sql_cond_prev="";
			if($item_description!="") $sql_cond_prev.=" and trim(c.item_description)=".trim($item_description)."";
			if($brand_supref!="") $sql_cond_prev.=" and trim(c.brand_supplier)='".trim($brand_supref)."'";
			if($item_color_id!="") $sql_cond_prev.=" and trim(c.item_color)='".trim($item_color_id)."'";
			if($item_size!="") $sql_cond_prev.=" and trim(c.item_size)='".trim($item_size)."'";
			if($gmts_size_id!="" && $gmts_size_id!=0) $sql_cond_prev_book.=" and trim(c.gmts_size_id)='".trim($gmts_size_id)."'";
			if($gmts_size_id!="" && $gmts_size_id!=0) $sql_cond_prev_prod.=" and trim(c.gmts_size)='".trim($gmts_size_id)."'";
			$gmt_color_rtn_cond="";		
			if($gmts_color_id!="" && $gmts_color_id!=0) $gmt_color_rtn_cond.=" and trim(c.color)='".trim($gmts_color_id)."'";
			$sql_prev_rcv_rtn=sql_select("select b.cons_quantity, d.conversion_factor 

			from inv_transaction b, product_details_master c, lib_item_group d 
			where b.prod_id=c.id and c.item_group_id=d.id and b.transaction_type=3 and b.item_category=4 and c.item_category_id=4 and c.entry_form=24 and b.receive_basis=$recieve_basis and b.pi_wo_batch_no=$bookingNo_piId and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.item_group_id=$item_group $sql_cond_prev $sql_cond_prev_prod $gmt_color_rtn_cond");
			$rcv_rtn_qnty=0;
			foreach($sql_prev_rcv_rtn as $row)
			{
				//$rcv_rtn_qnty+=$row[csf("cons_quantity")]-$row[csf("conversion_factor")];
				$rcv_rtn_qnty+=$row[csf("cons_quantity")]/$row[csf("conversion_factor")];
			}
			$balance_qnty=number_format($qnty,4,'.','')-(number_format($receive_qnty,4,'.','')-number_format($rcv_rtn_qnty,4,'.',''));
			//$balance_qnty=number_format($qnty,2,'.','')-number_format($receive_qnty,2,'.','');
		}
		else $balance_qnty=0;
	}
	else
	{
		$balance_qnty='';
		$rate='';
	}
	
	echo "document.getElementById('txt_bl_qty').value 		= '".number_format($balance_qnty,4,'.','')."';\n";
	echo "document.getElementById('txt_rate').value 		= '".number_format($booking_rate,2,'.','')."';\n";
	echo "document.getElementById('txt_hidden_rate').value 	= '".$booking_rate."';\n";
	//echo "document.getElementById('txt_rate').value 	= '".number_format($rate,4,'.','')."';\n";
	
	exit();	
}

if ($action=="save_update_delete")
{
	//$process = array( &$_POST );
	$process = $_POST;
	extract(check_magic_quote_gpc( $process ));
	$payment_for=str_replace("'", "", $cbo_payment_over_recv);
	
    if (str_replace("'",'',$update_id) != '')
    {
        $is_audited=return_field_value("is_audited","inv_receive_master","id=".str_replace("'",'',$update_id)." and status_active=1 and is_deleted=0","is_audited");
        if($is_audited==1) {
            echo "50**This MRR is Audited. Save, Update and Delete Not Allowed.";
            die;
        }
    }
	
	if(str_replace("'","",$txt_hidden_rate)=="" || str_replace("'","",$txt_hidden_rate)==0)
	{
		$txt_hidden_rate = str_replace("'","",$txt_rate);
		$txt_hidden_amount=str_replace("'","",$txt_receive_qnty)*$txt_hidden_rate;
	}
	$rcv_basis=str_replace("'", "", $cbo_receive_basis);
	$txt_receive_chal_no=urldecode($txt_receive_chal_no);
	$txt_receive_chal_no=str_replace("'", "", $txt_receive_chal_no);
	$str_rep=array("*",  "=", "\r", "\n", "\t", "#");
	$txt_receive_chal_no=str_replace($str_rep, "", $txt_receive_chal_no);
	$txt_receive_chal_no="'".$txt_receive_chal_no."'";
	
	if($rcv_basis==1 || $rcv_basis==2)
	{
		$variable_set_invent = sql_select("select category,over_rcv_status,over_rcv_percent,over_rcv_payment from variable_inv_ile_standard where company_name=$cbo_company_id and variable_list=23 and category =4 order by id");
		$over_receive_limit = !empty($variable_set_invent) ? $variable_set_invent[0][csf('over_rcv_percent')] : 0;
		
		$up_trans_id=str_replace("'","",$update_trans_id);
		$up_cond="";
		if($up_trans_id!="") $up_cond=" and b.id <> $up_trans_id";
		
		$sql_cond_prev="";
		if(str_replace("'","",$hidden_item_description)!="") $sql_cond_prev.=" and trim(c.item_description)='".trim(str_replace("'","",$hidden_item_description))."'";
		if(str_replace("'","",$txt_brand_supref)!="") $sql_cond_prev.=" and trim(c.brand_supplier)='".trim(str_replace("'","",$txt_brand_supref))."'";
		if(str_replace("'","",$txt_item_color_id)!="") $sql_cond_prev.=" and trim(c.item_color)='".trim(str_replace("'","",$txt_item_color_id))."'";
		//if(str_replace("'","",$txt_item_size)!="") $sql_cond_prev.=" and trim(c.item_size)='".trim(str_replace("'","",$txt_item_size))."'";
		if(str_replace("'","",$txt_gmts_size_id)!="" && str_replace("'","",$txt_gmts_size_id)!=0) $sql_cond_prev_book.=" and trim(c.gmts_size_id)='".trim(str_replace("'","",$txt_gmts_size_id))."'";
		if(str_replace("'","",$txt_gmts_size_id)!="" && str_replace("'","",$txt_gmts_size_id)!=0) $sql_cond_prev_prod.=" and trim(c.gmts_size)='".trim(str_replace("'","",$txt_gmts_size_id))."'";

		if(str_replace("'","",$txt_item_size)!="") // 8930
		{
			$sql_cond_prev.=" and trim(c.item_size)='".trim(str_replace("'","",$txt_item_size))."'";
		} else {
			$sql_cond_prev.=" and trim(c.item_size)  is null";
		}
		
		$gmt_color_rtn_cond="";		
		if(str_replace("'","",$txt_gmts_color_id)!="" && str_replace("'","",$txt_gmts_color_id)!=0) $gmt_color_rtn_cond.=" and trim(c.color)='".trim(str_replace("'","",$txt_gmts_color_id))."'";

		$sql_prev_rcv_rtn=sql_select("select b.cons_quantity, d.conversion_factor 
		from inv_transaction b, product_details_master c, lib_item_group d 
		where b.prod_id=c.id and c.item_group_id=d.id and b.transaction_type=3 and b.item_category=4 and c.item_category_id=4 and c.entry_form=24 and b.receive_basis=$rcv_basis and b.pi_wo_batch_no=$txt_booking_pi_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.item_group_id=$cbo_item_group $sql_cond_prev $sql_cond_prev_prod $gmt_color_rtn_cond");
		$rcv_rtn_qnty=0;
		foreach($sql_prev_rcv_rtn as $row)
		{
			$rcv_rtn_qnty+=$row[csf("cons_quantity")]/$row[csf("conversion_factor")];
		}

		$gmt_color_rcv_cond="";		
		if(str_replace("'","",$txt_gmts_color_id)!="" && str_replace("'","",$txt_gmts_color_id)!=0) $gmt_color_rcv_cond.=" and trim(c.gmts_color_id)='".trim(str_replace("'","",$txt_gmts_color_id))."'";
		$sql_prev_rcv=sql_select("select sum(b.order_qnty) as rcv_qnty 
		from inv_transaction b, inv_trims_entry_dtls c 
		where b.id=c.trans_id and b.receive_basis=$rcv_basis and b.pi_wo_batch_no=$txt_booking_pi_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.item_group_id=$cbo_item_group $sql_cond_prev $sql_cond_prev_book $gmt_color_rcv_cond $up_cond");
		$prev_rcv=$sql_prev_rcv[0][csf("rcv_qnty")]-$rcv_rtn_qnty;
		
		if($rcv_basis==2)
		{
			if(str_replace("'","",$booking_without_order)==1)
			{
				$sql_cond_wo="";
				if(str_replace("'","",$hidden_item_description)!="") $sql_cond_wo.=" and trim(c.fabric_description)='".trim(str_replace("'","",$hidden_item_description))."'";
				if(str_replace("'","",$txt_brand_supref)!="") $sql_cond_wo.=" and trim(c.barnd_sup_ref)='".trim(str_replace("'","",$txt_brand_supref))."'";
				if(str_replace("'","",$txt_item_color_id)!="") $sql_cond_wo.=" and c.fabric_color=$txt_item_color_id";
				if(str_replace("'","",$txt_gmts_color_id)!="" && str_replace("'","",$txt_gmts_color_id)!=0) $sql_cond_wo.=" and c.gmts_color=$txt_gmts_color_id ";
				if(str_replace("'","",$txt_item_size)!="") $sql_cond_wo.=" and trim(c.item_size)='".trim(str_replace("'","",$txt_item_size))."'";
				if(str_replace("'","",$txt_gmts_size_id)!="" && str_replace("'","",$txt_gmts_size_id)!=0) $sql_cond_wo.=" and trim(c.gmts_size)='".trim(str_replace("'","",$txt_gmts_size_id))."' ";
				
				$booking_pi_sql=sql_select("select sum(c.trim_qty) as booking_pi_qnty from wo_non_ord_samp_booking_mst b, wo_non_ord_samp_booking_dtls c
				where b.booking_no=c.booking_no and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.booking_no=$txt_booking_pi_no and c.booking_no=$txt_booking_pi_no and c.trim_group=$cbo_item_group $sql_cond_wo");
			}
			else
			{
				$sql_cond_wo="";
				if(str_replace("'","",$hidden_item_description)!="") $sql_cond_wo.=" and trim(c.description)='".trim(str_replace("'","",$hidden_item_description))."'";
				if(str_replace("'","",$txt_brand_supref)!="") $sql_cond_wo.=" and trim(c.brand_supplier)='".trim(str_replace("'","",$txt_brand_supref))."'";
				if(str_replace("'","",$txt_item_color_id)!="") $sql_cond_wo.=" and c.item_color=$txt_item_color_id";
				if(str_replace("'","",$txt_gmts_color_id)!="" && str_replace("'","",$txt_gmts_color_id)!=0) $sql_cond_wo.=" and c.color_number_id=$txt_gmts_color_id ";
				if(str_replace("'","",$txt_item_size)!="") $sql_cond_wo.=" and trim(c.item_size)='".trim(str_replace("'","",$txt_item_size))."'";
				if(str_replace("'","",$txt_gmts_size_id)!="" && str_replace("'","",$txt_gmts_size_id)!=0) $sql_cond_wo.=" and trim(c.gmts_sizes)='".trim(str_replace("'","",$txt_gmts_size_id))."' ";
				$booking_pi_sql=sql_select(" select sum(c.cons) as booking_pi_qnty from wo_booking_dtls b,  wo_trim_book_con_dtls c
				where b.id=c.wo_trim_booking_dtls_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.booking_no=$txt_booking_pi_no and c.booking_no=$txt_booking_pi_no and b.trim_group=$cbo_item_group $sql_cond_wo");
			}
		}
		else
		{
			$sql_cond_pi="";
			if(str_replace("'","",$hidden_item_description)!="") $sql_cond_pi.=" and trim(item_description)='".trim(str_replace("'","",$hidden_item_description))."'";
			if(str_replace("'","",$txt_brand_supref)!="") $sql_cond_pi.=" and trim(brand_supplier)='".trim(str_replace("'","",$txt_brand_supref))."'";
			if(str_replace("'","",$txt_item_color_id)!="") $sql_cond_pi.=" and item_color=$txt_item_color_id ";
			if(str_replace("'","",$txt_gmts_color_id)!="" && str_replace("'","",$txt_gmts_color_id)!=0) $sql_cond_pi.=" and color_id=$txt_gmts_color_id ";
			if(str_replace("'","",$txt_item_size)!="") $sql_cond_pi.=" and trim(item_size)='".trim(str_replace("'","",$txt_item_size))."'";
			if(str_replace("'","",$txt_gmts_size_id)!="" && str_replace("'","",$txt_gmts_size_id)!=0) $sql_cond_pi.=" and trim(size_id)='".trim(str_replace("'","",$txt_gmts_size_id))."' ";
			
			$booking_pi_sql=sql_select("select sum(quantity) as booking_pi_qnty from com_pi_item_details
			where pi_id=$txt_booking_pi_id and status_active=1 and is_deleted=0 and item_group=$cbo_item_group $sql_cond_pi");
		}
		$wobookingQnty=$booking_pi_sql[0][csf("booking_pi_qnty")];
	}
	
	$store_update_upto=str_replace("'","",$store_update_upto);
	if($store_update_upto > 1)
	{
		$cbo_floor=str_replace("'","",$cbo_floor);
		$cbo_room=str_replace("'","",$cbo_room);
		$txt_rack=str_replace("'","",$txt_rack);
		$txt_shelf=str_replace("'","",$txt_shelf);
		$cbo_bin=str_replace("'","",$cbo_bin);
		if($store_update_upto==2)
		{
			$cbo_room=0;
			$txt_rack=0;
			$txt_shelf=0;
			$cbo_bin=0;
		}
		else if($store_update_upto==3)
		{
			$txt_rack=0;
			$txt_shelf=0;
			$cbo_bin=0;
		}
		else if($store_update_upto==4)
		{
			$txt_shelf=0;
			$cbo_bin=0;
		}
		else if($store_update_upto==5)
		{
			$cbo_bin=0;
		}
	}
	else
	{
		$cbo_floor=0;
		$cbo_room=0;
		$txt_rack=0;
		$txt_shelf=0;
		$cbo_bin=0;
	}
	
	//echo "10**$wobookingQnty==$hidden_item_description==$txt_item_description";die;
	if ($operation==0)  // Insert Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		
		if($rcv_basis==1 || $rcv_basis==2)
		{
			$txt_receive_qty = str_replace("'", "", $txt_receive_qnty);
			$total_recvQnty=$prev_rcv+$txt_receive_qty;
			$woYarnQnty=$wobookingQnty;
			$over_receive_limit_qnty = ($over_receive_limit>0)?($over_receive_limit / 100) * $woYarnQnty:0;			
			$allow_total_val = $woYarnQnty + $over_receive_limit_qnty;
			$overRecvLimitMsg="Over Receive limit = $over_receive_limit% ($over_receive_limit_qnty Kg.)";
			if($allow_total_val<$total_recvQnty && $payment_for==0) 
			{
				$over_msg = ($over_receive_limit>0)?"\nAllowed Quantity = $allow_total_val":"";
				echo "40**Recv. quantity can not be greater than WO/Booking quantity.\n\nWO/Booking quantity = $woYarnQnty \n$overRecvLimitMsg $over_msg";
				disconnect($con);die;
			}
		}
		
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		
		$trims_recv_num=''; $trims_update_id='';



		$txt_delv_chln_date=$txt_gate_entry_date=$txt_invoice_date="";
		$addi_info_arr = explode("_", str_replace("'","",$txt_addi_info));
		
		$txt_gate_entry_no = $addi_info_arr[0];
		$txt_delv_chln_date = $addi_info_arr[1];
		$txt_gate_entry_date = $addi_info_arr[2];
		$txt_vehicle_no = $addi_info_arr[3];
		$txt_invoice_no = $addi_info_arr[4];
		$txt_transporter_name = $addi_info_arr[5];
		$txt_invoice_date = $addi_info_arr[6];
		$txt_short_qnty = $addi_info_arr[7];
		$txt_excess_qnty = $addi_info_arr[8];

		if($db_type == 0)
		{
			$txt_delv_chln_date= change_date_format($txt_delv_chln_date, 'yyyy-mm-dd');
			$txt_gate_entry_date = change_date_format($txt_gate_entry_date, 'yyyy-mm-dd');
			$txt_invoice_date = change_date_format($txt_invoice_date, 'yyyy-mm-dd');
		}
		else
		{
			$txt_delv_chln_date = change_date_format($txt_delv_chln_date, '', '', 1);
			$txt_gate_entry_date = change_date_format($txt_gate_entry_date, '', '', 1);
			$txt_invoice_date = change_date_format($txt_invoice_date, '', '', 1);
		}

		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
			
			$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);
			$new_trims_recv_system_id = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master",$con,1,$cbo_company_id,'TRE',24,date("Y",time()) ));
			//$id=return_next_id( "id", "inv_receive_master", 1 ) ;
			$field_array="id, recv_number_prefix, recv_number_prefix_num, recv_number, entry_form, item_category, receive_basis, company_id, receive_date, challan_no, challan_date, booking_id, booking_no, booking_without_order, store_id,floor,room,rack,shelf,bin, lc_no, source, supplier_id, currency_id, exchange_rate, gate_entry_no,addi_challan_date, gate_entry_date,vehicle_no,rcvd_book_no,transporter_name,bill_date,short_qnty,excess_qnty, is_multi, inserted_by, insert_date,pay_mode";
			
			$data_array="(".$id.",'".$new_trims_recv_system_id[1]."',".$new_trims_recv_system_id[2].",'".$new_trims_recv_system_id[0]."',24,4,".$cbo_receive_basis.",".$cbo_company_id.",".$txt_receive_date.",".$txt_receive_chal_no.",".$txt_challan_date.",".$txt_booking_pi_id.",".$txt_booking_pi_no.",".$booking_without_order.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$lc_id.",".$cbo_source.",".$cbo_supplier_name.",".$cbo_currency_id.",".$txt_exchange_rate.",'".$txt_gate_entry_no."','".$txt_delv_chln_date."','".$txt_gate_entry_date."','".$txt_vehicle_no."','".$txt_invoice_no."','".$txt_transporter_name."','".$txt_invoice_date."','".$txt_short_qnty."','".$txt_excess_qnty."',1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$hidd_pay_mode.")";
			
			$trims_recv_num=$new_trims_recv_system_id[0];
			$trims_update_id=$id;
		}
		else
		{
			$original_receive_basis=sql_select("select receive_basis, supplier_id from inv_receive_master where id=$update_id");
			if(str_replace("'","",$cbo_receive_basis)!=$original_receive_basis[0][csf('receive_basis')])
			{
				echo "40**Multiple Receive Basis Not Allow In Same Received ID";disconnect($con);die;
			}
			if(str_replace("'","",$cbo_supplier_name)!=$original_receive_basis[0][csf('supplier_id')])
			{
				echo "40**Multiple Supplier Allow In Same Received ID";disconnect($con);die;
			}
			 
			$field_array_update="receive_basis*receive_date*challan_no*challan_date*booking_id*booking_no*booking_without_order*store_id*floor*room*rack*shelf*bin*lc_no*source*supplier_id*currency_id*exchange_rate*gate_entry_no*addi_challan_date*gate_entry_date*vehicle_no*rcvd_book_no*transporter_name*bill_date*short_qnty*excess_qnty*updated_by*update_date";
			
			$data_array_update=$cbo_receive_basis."*".$txt_receive_date."*".$txt_receive_chal_no."*".$txt_challan_date."*".$txt_booking_pi_id."*".$txt_booking_pi_no."*".$booking_without_order."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$lc_id."*".$cbo_source."*".$cbo_supplier_name."*".$cbo_currency_id."*".$txt_exchange_rate."*'".$txt_gate_entry_no."'*'".$txt_delv_chln_date."'*'".$txt_gate_entry_date."'*'".$txt_vehicle_no."'*'".$txt_invoice_no."'*'".$txt_transporter_name."'*'".$txt_invoice_date."'*'".$txt_short_qnty."'*'".$txt_excess_qnty."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			$trims_recv_num=str_replace("'","",$txt_recieved_id);
			$trims_update_id=str_replace("'","",$update_id);
		}
		
		//details table entry here START-----------------------------------//		

		$ile=return_field_value("standard","variable_inv_ile_standard","source=$cbo_source and company_name=$cbo_company_id and category=4 and status_active=1 and is_deleted=0");
		$ile_cost = str_replace("'","",$txt_ile);
		
		if($db_type==0)
		{
			$concattS = explode(",",return_field_value("concat(trim_uom,',',conversion_factor)","lib_item_group","id=$cbo_item_group")); 
		}
		else
		{
			$concattS = explode(",",return_field_value("trim_uom || ',' || conversion_factor as data","lib_item_group","id=$cbo_item_group","data")); 
		}
		
		$cons_uom = $concattS[0];
		$conversion_factor = $concattS[1];
		
		$exchange_rate = str_replace("'","",$txt_exchange_rate);
		$txt_receive_qnty = str_replace("'","",$txt_receive_qnty);
		$con_qnty = $conversion_factor*$txt_receive_qnty;
		
		$rate = str_replace("'","",$txt_hidden_rate);
		$domestic_rate = return_domestic_rate($rate,$ile_cost,$exchange_rate,$conversion_factor);
		$cons_rate = number_format($domestic_rate,$dec_place[3],".","");
		$con_amount = $cons_rate*$con_qnty;
		$con_ile = $ile/$conversion_factor;
		$con_ile_cost = ($con_ile/100)*$cons_rate;
		
		$item_desc=''; $gmts_color_id=0; $gmts_size_id=0; 
		
		if(str_replace("'","",$cbo_receive_basis)==1 || str_replace("'","",$cbo_receive_basis)==2)
		{
			$item_desc=str_replace("'","",$hidden_item_description);
			$gmts_color_id=str_replace("'","",$txt_gmts_color_id);
			$gmts_size_id=str_replace("'","",$txt_gmts_size_id);
		}
		else 
		{
			$item_desc=str_replace("'","",$txt_item_description);
			
			if(str_replace("'","",$txt_gmts_color)!="")
			{ 
				if (!in_array(str_replace("'","",$txt_gmts_color),$new_array_color))
				{
					$gmts_color_id = return_id( str_replace("'","",$txt_gmts_color), $color_arr, "lib_color", "id,color_name","24");  
					$new_array_color[$gmts_color_id]=str_replace("'","",$txt_gmts_color);
				}
				else $gmts_color_id =  array_search(str_replace("'","",$txt_gmts_color), $new_array_color); 
			}
			else
			{
				$gmts_color_id=0;
			}
			
			if(str_replace("'","",$txt_item_color)!="")
			{ 
				if (!in_array(str_replace("'","",$txt_item_color),$new_array_color))
				{
					$txt_item_color_id = return_id( str_replace("'","",$txt_item_color), $color_arr, "lib_color", "id,color_name","24");  
					$new_array_color[$txt_item_color_id]=str_replace("'","",$txt_item_color);
				}
				else $txt_item_color_id =  array_search(str_replace("'","",$txt_item_color), $new_array_color); 
			}
			else
			{
				$txt_item_color_id=0;

			}
			
			if(str_replace("'","",$txt_gmts_size)!="")
			{
				if (!in_array(str_replace("'","",$txt_gmts_size),$new_array_size))
				{
				  $gmts_size_id = return_id( str_replace("'","",$txt_gmts_size), $size_arr, "lib_size", "id,size_name","24");  
				  $new_array_size[$gmts_size_id]=str_replace("'","",$txt_gmts_size);
				}
				else $gmts_size_id =  array_search(str_replace("'","",$txt_gmts_size), $new_array_size); 
			}
			else
			{
				$gmts_size_id=0;
			}
		}
		
		if(str_replace("'","",$txt_item_size)!="" || $db_type==0)
		{
			$item_size_cond="item_size=$txt_item_size";
		}
		else 
		{
			$item_size_cond="item_size is null";
		}
		
		if(str_replace("'","",$txt_brand_supref)!="" || $db_type==0)
		{
			$brand_supref_cond="brand_supplier=$txt_brand_supref";
		}
		else 
		{
			$brand_supref_cond="brand_supplier is null";
		}
		
		if($gmts_size_id=="") $gmts_size_id=0;
		if($gmts_color_id=="") $gmts_color_id=0;
		if(str_replace("'","",$txt_item_color_id)=="") $txt_item_color_id=0; 
		
		$meterial_source=str_replace("'","",$meterial_source);
		$is_buyer_supplied = ($meterial_source==3)?1 : 0;
		$buyer_supplied = ($meterial_source==3)?", [BS]" : "";
		
		if($db_type==2)
		{
			$item_desc=str_replace("(","[",$item_desc);
			$item_desc=str_replace(")","]",$item_desc);
		}
		$item_desc_dtls=$item_desc;
		$item_desc=$item_desc.$buyer_supplied;
		$row_prod=sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where company_id=$cbo_company_id and item_category_id=4 and entry_form=24 and item_group_id=$cbo_item_group and item_description='$item_desc' and $brand_supref_cond and color='$gmts_color_id' and item_color=$txt_item_color_id and gmts_size='$gmts_size_id' and $item_size_cond and status_active=1 and is_deleted=0");
		
		if(count($row_prod)>0)
		{
			$prod_id=$row_prod[0][csf('id')];

			$curr_stock_qnty=$row_prod[0][csf('current_stock')]+$con_qnty;
			$avg_rate_per_unit=$row_prod[0][csf('avg_rate_per_unit')];
			$curr_stock_value=0;
			if ($curr_stock_qnty != 0){
				$curr_stock_value=$row_prod[0][csf('stock_value')]+$con_amount;
				$avg_rate_per_unit=number_format($curr_stock_value/$curr_stock_qnty,$dec_place[3],'.','');
			}			
			
			$field_array_prod_update="store_id*avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
			$data_array_prod_update=$cbo_store_name."*".$avg_rate_per_unit."*".$con_qnty."*".$curr_stock_qnty."*".$curr_stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
		}
		else
		{
			//$prod_id=return_next_id( "id", "product_details_master", 1 ) ;
			$prod_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
			$item_name =$trim_group_arr[str_replace("'","",$cbo_item_group)]['name'];
			$prod_name_dtls=$item_name.", ".trim(str_replace("'","",$txt_item_description)).$buyer_supplied;
			
			if($db_type==2)
			{
				$prod_name_dtls=str_replace("(","[",$prod_name_dtls);
				$prod_name_dtls=str_replace(")","]",$prod_name_dtls);
			}
			
			$field_array_prod="id, company_id, store_id, item_category_id, entry_form, item_group_id, item_description, product_name_details, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, color, item_color, gmts_size, item_size, brand_supplier, is_buyer_supplied, inserted_by, insert_date";
			
			$data_array_prod="(".$prod_id.",".$cbo_company_id.",".$cbo_store_name.",4,24,".$cbo_item_group.",'".$item_desc."','".$prod_name_dtls."',".$cons_uom.",".$cons_rate.",".$con_qnty.",".$con_qnty.",".$con_amount.",'".$gmts_color_id."',".$txt_item_color_id.",'".$gmts_size_id."',".$txt_item_size.",".$txt_brand_supref.",".$is_buyer_supplied.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
		}
		
		$max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$prod_id and store_id =$cbo_store_name and status_active = 1 and is_deleted =0", "max_date");      
		if($max_issue_date !="")
		{

			$max_issue_date = date("Y-m-d", strtotime($max_issue_date));
			$receive_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_receive_date)));

			if ($receive_date < $max_issue_date) 
			{
				echo "20**Receive Date Can not Be Less Than Last Transaction Date Of This Item";disconnect($con);
				die;
			}
		}
		
		//$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
		$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		$field_array_trans="id, mst_id, receive_basis, pi_wo_batch_no, booking_no, booking_without_order, company_id, supplier_id, prod_id, item_category, transaction_type, transaction_date, store_id,floor_id,room,rack,self,bin_box, order_uom, order_qnty, order_rate, order_amount, order_ile, order_ile_cost, cons_uom, cons_quantity, cons_rate, cons_amount, cons_ile, cons_ile_cost, balance_qnty, balance_amount, payment_over_recv, remarks, inserted_by, insert_date";
		
		$data_array_trans="(".$id_trans.",".$trims_update_id.",".$cbo_receive_basis.",".$txt_booking_pi_id.",".$txt_booking_pi_no.",".$booking_without_order.",".$cbo_company_id.",".$cbo_supplier_name.",".$prod_id.",4,1,".$txt_receive_date.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_uom.",".$txt_receive_qnty.",".$rate.",".$txt_amount.",'".$ile."','".$ile_cost."',".$cons_uom.",".$con_qnty.",".$cons_rate.",".$con_amount.",".$con_ile.",".$con_ile_cost.",".$con_qnty.",".$con_amount.",".$cbo_payment_over_recv.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		if($db_type==2)
		{
			$txt_item_description=str_replace("(","[",$txt_item_description);
			$txt_item_description=str_replace(")","]",$txt_item_description);
		}

		$save_data=str_replace("'","",$save_data);
		$tot_string=strlen($save_data);
		$count_loop=ceil($tot_string/3900);
		$first_save_data=''; $second_save_data=''; $theRest_save_data='';$count=0; $interval=3900;
		for($i=1;$i<=$count_loop; $i++)
		{
		    if($count_loop>0 && $i==1) $first_save_data=substr($save_data, $count, $interval);
		    if($count_loop>1 && $i==2) $second_save_data=substr($save_data, $count, $interval);
		    if($count_loop>2 && $i==3) $theRest_save_data=substr($save_data, $count, $interval);
		    $count+=3900;
		}

		//$id_dtls=return_next_id( "id", "inv_trims_entry_dtls", 1 ) ;
		$id_dtls = return_next_id_by_sequence("INV_TRIMS_ENTRY_DTLS_PK_SEQ", "inv_trims_entry_dtls", $con);
		
		$field_array_dtls="id, mst_id, trans_id, booking_id, booking_no, booking_without_order, prod_id, item_group_id, item_description, brand_supplier, order_uom, order_id, receive_qnty, reject_receive_qnty, rate, amount, ile, ile_cost, gmts_color_id, item_color, gmts_size_id, item_size, save_string, save_string_2, save_string_3, item_description_color_size, cons_uom, cons_qnty, cons_rate, cons_ile, cons_ile_cost, book_keeping_curr, sensitivity, payment_over_recv, material_source,floor,room_no,rack_no,self_no,box_bin_no, remarks, inserted_by, insert_date";
		
		$data_array_dtls="(".$id_dtls.",".$trims_update_id.",".$id_trans.",".$txt_booking_pi_id.",".$txt_booking_pi_no.",".$booking_without_order.",".$prod_id.",".$cbo_item_group.",'".$item_desc_dtls."',".$txt_brand_supref.",".$cbo_uom.",".$all_po_id.",".$txt_receive_qnty.",".$txt_reject_recv_qnty.",".$rate.",".$txt_amount.",'".$ile."',".$ile_cost.",'".$gmts_color_id."',".$txt_item_color_id.",'".$gmts_size_id."',".$txt_item_size.",'".$first_save_data."','".$second_save_data."','".$theRest_save_data."',".$txt_item_description.",".$cons_uom.",".$con_qnty.",".$cons_rate.",".$con_ile.",".$con_ile_cost.",".$con_amount.",".$hidden_sensitivity.",".$cbo_payment_over_recv.",'".$meterial_source."',".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		

		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, order_rate, order_amount, inserted_by, insert_date";
		$save_data=explode(",",str_replace("'","",$save_data));
		for($i=0;$i<count($save_data);$i++)
		{
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			$order_dtls=explode("_",$save_data[$i]);
			
			$order_id=$order_dtls[0];
			$order_qnty=$order_dtls[1];
			$order_amount=$order_qnty*$rate;
			
			if($i==0) $add_comma=""; else $add_comma=",";
			
			$data_array_prop.="$add_comma(".$id_prop.",".$id_trans.",1,24,".$id_dtls.",".$order_id.",".$prod_id.",".$order_qnty.",'".$rate."','".$order_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			//$id_prop = $id_prop+1;
			
		}
		
		
		
		$rID=$rID2=$rID3=$rID4=$rID6=true;
		//echo "5**insert into inv_receive_master (".$field_array.") values ".$data_array;die;
		if(str_replace("'","",$update_id)=="")
		{
			$rID=sql_insert("inv_receive_master",$field_array,$data_array,0);
		}
		else
		{
			$rID=sql_update("inv_receive_master",$field_array_update,$data_array_update,"id",$update_id,1);
		}
		
		if(count($row_prod)>0)
		{
			$rID2=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$prod_id,0);
		}
		else
		{
			//echo "5**insert into product_details_master (".$field_array_prod.") values ".$data_array_prod;die;
			$rID2=sql_insert("product_details_master",$field_array_prod,$data_array_prod,0);
		}
		// echo "insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;
		$rID3=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
	
		//echo "insert into inv_trims_entry_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID4=sql_insert("inv_trims_entry_dtls",$field_array_dtls,$data_array_dtls,0);
		
		//echo "insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;
		if($data_array_prop!="" && str_replace("'","",$booking_without_order)!=1)
		{
			$rID6=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
		}
		
		//echo "5**$rID=$rID2=$rID3=$rID4=$rID6";oci_rollback($con);disconnect($con);die;
		
		//check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID6)
			{
				mysql_query("COMMIT");  
				echo "0**".$trims_update_id."**".$trims_recv_num."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**"."&nbsp;"."**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID6)
			{
				oci_commit($con);  
				echo "0**".$trims_update_id."**".$trims_recv_num."**0";
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
	else if ($operation==1)   // Update Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		if($rcv_basis==1 || $rcv_basis==2)
		{
			$txt_receive_qty = str_replace("'", "", $txt_receive_qnty);
			$total_recvQnty=$prev_rcv+$txt_receive_qty;
			$woYarnQnty=$wobookingQnty;
			$over_receive_limit_qnty = ($over_receive_limit>0)?($over_receive_limit / 100) * $woYarnQnty:0;			
			$allow_total_val = $woYarnQnty + $over_receive_limit_qnty;
			$overRecvLimitMsg="Over Receive limit = $over_receive_limit% ($over_receive_limit_qnty Kg.)";
			if($allow_total_val<$total_recvQnty && $payment_for==0) 
			{
				$over_msg = ($over_receive_limit>0)?"\nAllowed Quantity = $allow_total_val":"";
				echo "40**Recv. quantity can not be greater than WO/Booking quantity.\n\nWO/Booking quantity = $woYarnQnty \n$overRecvLimitMsg $over_msg";
				disconnect($con);die;
			}
		}
		   
		$original_receive_basis=sql_select(" select a.id,a.receive_basis,a.booking_id,count(b.id) as dtls_row from inv_receive_master a,inv_trims_entry_dtls b where a.id=b.mst_id and a.id=$update_id and b.id!=$update_dtls_id group by a.id,a.receive_basis,a.booking_id");
		if($original_receive_basis[0][csf('dtls_row')]>0)
		{
			if(str_replace("'","",$cbo_receive_basis)!=$original_receive_basis[0][csf('receive_basis')])
			{
				echo "40**Multiple Receive Basis Not Allow In Same Received ID";disconnect($con);die;
			}
		}
		$item_desc=''; $gmts_color_id=0; $gmts_size_id=0; 
		
		if(str_replace("'","",$cbo_receive_basis)==1 || str_replace("'","",$cbo_receive_basis)==2)
		{
			$item_desc=str_replace("'","",$hidden_item_description);
			$gmts_color_id=str_replace("'","",$txt_gmts_color_id);
			$gmts_size_id=str_replace("'","",$txt_gmts_size_id);
		}
		else if(str_replace("'","",$cbo_receive_basis)==12)
		{
			$item_desc=str_replace("'","",$hidden_item_description);
			$txt_item_color=str_replace("'","",$txt_item_color);
			$gmts_size_id=str_replace("'","",$txt_gmts_size_id);
		}
		else 
		{
			$item_desc=str_replace("'","",$txt_item_description);
			
			if(str_replace("'","",$txt_gmts_color)!="")
			{ 
				if (!in_array(str_replace("'","",$txt_gmts_color),$new_array_color))
				{
					$gmts_color_id = return_id( str_replace("'","",$txt_gmts_color), $color_arr, "lib_color", "id,color_name","24");  
					$new_array_color[$gmts_color_id]=str_replace("'","",$txt_gmts_color);
				}
				else $gmts_color_id =  array_search(str_replace("'","",$txt_gmts_color), $new_array_color); 
			}
			else
			{
				$gmts_color_id=0;
			}
			
			if(str_replace("'","",$txt_item_color)!="")
			{ 
				if (!in_array(str_replace("'","",$txt_item_color),$new_array_color))
				{
					$txt_item_color_id = return_id( str_replace("'","",$txt_item_color), $color_arr, "lib_color", "id,color_name","24");  
					$new_array_color[$txt_item_color_id]=str_replace("'","",$txt_item_color);
				}
				else $txt_item_color_id =  array_search(str_replace("'","",$txt_item_color), $new_array_color); 
			}
			else
			{
				$txt_item_color_id=0;
			}
			
			if(str_replace("'","",$txt_gmts_size)!="")
			{
				if (!in_array(str_replace("'","",$txt_gmts_size),$new_array_size))
				{
				  $gmts_size_id = return_id( str_replace("'","",$txt_gmts_size), $size_arr, "lib_size", "id,size_name","24");  
				  $new_array_size[$gmts_size_id]=str_replace("'","",$txt_gmts_size);
				}
				else $gmts_size_id =  array_search(str_replace("'","",$txt_gmts_size), $new_array_size); 
			}
			else
			{
				$gmts_size_id=0;
			}
		}


		$txt_delv_chln_date=$txt_gate_entry_date=$txt_invoice_date="";
		$addi_info_arr = explode("_", str_replace("'","",$txt_addi_info));
		
		$txt_gate_entry_no = $addi_info_arr[0];
		$txt_delv_chln_date = $addi_info_arr[1];
		$txt_gate_entry_date = $addi_info_arr[2];
		$txt_vehicle_no = $addi_info_arr[3];
		$txt_invoice_no = $addi_info_arr[4];
		$txt_transporter_name = $addi_info_arr[5];
		$txt_invoice_date = $addi_info_arr[6];
		$txt_short_qnty = $addi_info_arr[7];
		$txt_excess_qnty = $addi_info_arr[8];

		if($db_type == 0)
		{
			$txt_delv_chln_date= change_date_format($txt_delv_chln_date, 'yyyy-mm-dd');
			$txt_gate_entry_date = change_date_format($txt_gate_entry_date, 'yyyy-mm-dd');
			$txt_invoice_date = change_date_format($txt_invoice_date, 'yyyy-mm-dd');
		}
		else
		{
			$txt_delv_chln_date = change_date_format($txt_delv_chln_date, '', '', 1);
			$txt_gate_entry_date = change_date_format($txt_gate_entry_date, '', '', 1);
			$txt_invoice_date = change_date_format($txt_invoice_date, '', '', 1);
		}
		
		$field_array_update="receive_basis*receive_date*challan_no*challan_date*booking_id*booking_no*booking_without_order*store_id*floor*room*rack*shelf*bin*lc_no*source*supplier_id*currency_id*exchange_rate*gate_entry_no*addi_challan_date*gate_entry_date*vehicle_no*rcvd_book_no*transporter_name*bill_date*short_qnty*excess_qnty*updated_by*update_date";
			
		$data_array_update=$cbo_receive_basis."*".$txt_receive_date."*".$txt_receive_chal_no."*".$txt_challan_date."*".$txt_booking_pi_id."*".$txt_booking_pi_no."*".$booking_without_order."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$lc_id."*".$cbo_source."*".$cbo_supplier_name."*".$cbo_currency_id."*".$txt_exchange_rate."*'".$txt_gate_entry_no."'*'".$txt_delv_chln_date."'*'".$txt_gate_entry_date."'*'".$txt_vehicle_no."'*'".$txt_invoice_no."'*'".$txt_transporter_name."'*'".$txt_invoice_date."'*'".$txt_short_qnty."'*'".$txt_excess_qnty."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		
		$ile=return_field_value("standard","variable_inv_ile_standard","source=$cbo_source and company_name=$cbo_company_id and category=4 and status_active=1 and is_deleted=0");
		$ile_cost = str_replace("'","",$txt_ile);
		
		if($db_type==0)
		{
			$concattS = explode(",",return_field_value("concat(trim_uom,',',conversion_factor)","lib_item_group","id=$cbo_item_group")); 
		}
		else
		{
			$concattS = explode(",",return_field_value("trim_uom || ',' || conversion_factor as data","lib_item_group","id=$cbo_item_group","data")); 
		}

		$cons_uom = $concattS[0];
		$conversion_factor = $concattS[1];
		
		$exchange_rate = str_replace("'","",$txt_exchange_rate);
		$txt_receive_qnty = str_replace("'","",$txt_receive_qnty);
		$con_qnty = $conversion_factor*$txt_receive_qnty;
		
		$rate = str_replace("'","",$txt_hidden_rate);
		$domestic_rate = return_domestic_rate($rate,$ile_cost,$exchange_rate,$conversion_factor);
		$cons_rate = number_format($domestic_rate,$dec_place[3],".","");
		$con_amount = $cons_rate*$con_qnty;
		$con_ile = $ile/$conversion_factor;
		$con_ile_cost = ($con_ile/100)*$cons_rate;
		
		
		if(str_replace("'","",$txt_item_size)!="" || $db_type==0)
		{
			$item_size_cond="item_size=$txt_item_size";
		}
		else 
		{
			$item_size_cond="item_size is null";
		}
		
		if(str_replace("'","",$txt_brand_supref)!="" || $db_type==0)
		{
			$brand_supref_cond="brand_supplier=$txt_brand_supref";
		}
		else 
		{
			$brand_supref_cond="brand_supplier is null";
		}
		
		if($gmts_size_id=="") $gmts_size_id=0;
		if($gmts_color_id=="") $gmts_color_id=0;
		
		if(str_replace("'","",$txt_item_color_id)=="") $txt_item_color_id=0;
		
		$meterial_source=str_replace("'","",$meterial_source);
		$is_buyer_supplied = ($meterial_source==3)?1 : 0;
		$buyer_supplied = ($meterial_source==3)?", [BS]" : "";
		
		if($db_type==2)
		{
			$item_desc=str_replace("(","[",$item_desc);
			$item_desc=str_replace(")","]",$item_desc);
		}
		$item_desc_dtls=$item_desc;
		$item_desc=$item_desc.$buyer_supplied;
		
		
		$adjust_sql = sql_select("select a.cons_qnty, a.cons_rate, a.book_keeping_curr, a.payment_over_recv, b.avg_rate_per_unit, b.current_stock, b.stock_value from inv_trims_entry_dtls a, product_details_master b where a.id=$update_dtls_id and a.prod_id=b.id");
		
		$prod_id='';
		//echo "5**select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where company_id=$cbo_company_id and item_category_id=4 and entry_form=24 and item_group_id=$cbo_item_group and item_description='$item_desc' and $brand_supref_cond and color='$gmts_color_id' and item_color=$txt_item_color_id and gmts_size='$gmts_size_id' and $item_size_cond and status_active=1 and is_deleted=0";die;
		$row_prod=sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where company_id=$cbo_company_id and item_category_id=4 and entry_form=24 and item_group_id=$cbo_item_group and item_description='$item_desc' and $brand_supref_cond and color='$gmts_color_id' and item_color=$txt_item_color_id and gmts_size='$gmts_size_id' and $item_size_cond and status_active=1 and is_deleted=0");

		if(count($row_prod)>0)
		{
			$prod_id=$row_prod[0][csf('id')];
			if($prod_id==str_replace("'","",$previous_prod_id))
			{
				$curr_stock_qnty=$row_prod[0][csf('current_stock')]+$con_qnty-$adjust_sql[0][csf('cons_qnty')];
				$avg_rate_per_unit=$row_prod[0][csf('avg_rate_per_unit')];
				$curr_stock_value=0;
				if ($curr_stock_qnty != 0){
					$curr_stock_value=$row_prod[0][csf('stock_value')]+$con_amount-$adjust_sql[0][csf('book_keeping_curr')];
					$avg_rate_per_unit=number_format($curr_stock_value/$curr_stock_qnty,$dec_place[3],'.','');
				}				
				
				$field_array_prod_update="store_id*avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
				$data_array_prod_update=$cbo_store_name."*".$avg_rate_per_unit."*".$con_qnty."*".$curr_stock_qnty."*".$curr_stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				
				if($curr_stock_qnty<0)
				{
					echo "40**Stock cannot be less than zero.";disconnect($con);die;
				}
			}
			else
			{
				$adjust_curr_stock=$adjust_sql[0][csf('current_stock')]-$adjust_sql[0][csf('cons_qnty')];
				$cur_st_rate=$adjust_sql[0][csf('avg_rate_per_unit')];
				$cur_st_value=0;
				if ($adjust_curr_stock != 0){
					$cur_st_value=$adjust_sql[0][csf('stock_value')]-$adjust_sql[0][csf('book_keeping_curr')];
					$cur_st_rate=number_format($cur_st_value/$adjust_curr_stock,$dec_place[3],'.','');
				}				
				
				$field_array_adjust="current_stock*avg_rate_per_unit*stock_value";
				$data_array_adjust=$adjust_curr_stock."*".$cur_st_rate."*".$cur_st_value;
				
				if($adjust_curr_stock<0)
				{
					echo "40**Stock cannot be less than zero.";disconnect($con);die;
				}
				$avg_rate_per_unit=$row_prod[0][csf('avg_rate_per_unit')];
				$curr_stock_qnty=$row_prod[0][csf('current_stock')]+$con_qnty;
				$curr_stock_value=0;
				if ($curr_stock_qnty != 0){
					$curr_stock_value=$row_prod[0][csf('stock_value')]+$con_amount;
					$avg_rate_per_unit=number_format($curr_stock_value/$curr_stock_qnty,$dec_place[3],'.','');
				}				
				
				$field_array_prod_update="store_id*avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
				$data_array_prod_update=$cbo_store_name."*".$avg_rate_per_unit."*".$con_qnty."*".$curr_stock_qnty."*".$curr_stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			}
		}
		else
		{
			$cur_st_rate=$adjust_sql[0][csf('avg_rate_per_unit')];
			$adjust_curr_stock=$adjust_sql[0][csf('current_stock')]-$adjust_sql[0][csf('cons_qnty')];
			$cur_st_value=0;
			if ($adjust_curr_stock != 0){
				$cur_st_value=$adjust_sql[0][csf('stock_value')]-$adjust_sql[0][csf('book_keeping_curr')];
				$cur_st_rate=number_format($cur_st_value/$adjust_curr_stock,$dec_place[3],'.','');
			}			
			
			$field_array_adjust="current_stock*avg_rate_per_unit*stock_value";
			$data_array_adjust=$adjust_curr_stock."*".$cur_st_rate."*".$cur_st_value;
			
			if($adjust_curr_stock<0)
			{
				echo "40**Stock cannot be less than zero.";disconnect($con);die;
			}
			//$prod_id=return_next_id( "id", "product_details_master", 1 ) ;
			$prod_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
			$item_name =$trim_group_arr[str_replace("'","",$cbo_item_group)]['name'];
			$prod_name_dtls=$item_name.", ".trim(str_replace("'","",$txt_item_description)).$buyer_supplied;
			
			if($db_type==2)
			{
				$prod_name_dtls=str_replace("(","[",$prod_name_dtls);
				$prod_name_dtls=str_replace(")","]",$prod_name_dtls);
			}
			
			$field_array_prod="id, company_id, store_id, item_category_id, entry_form, item_group_id, item_description, product_name_details, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, color, item_color, gmts_size, item_size, brand_supplier, is_buyer_supplied, inserted_by, insert_date";
			
			$data_array_prod="(".$prod_id.",".$cbo_company_id.",".$cbo_store_name.",4,24,".$cbo_item_group.",'".$item_desc."','".$prod_name_dtls."',".$cons_uom.",".$cons_rate.",".$con_qnty.",".$con_qnty.",".$con_amount.",'".$gmts_color_id."',".$txt_item_color_id.",'".$gmts_size_id."',".$txt_item_size.",".$txt_brand_supref.",".$is_buyer_supplied.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
		}
		
		$max_issue_id = return_field_value("max(id) as max_tans_id", "inv_transaction", "prod_id=$prod_id and status_active=1 and transaction_type in(2,3,6) and id<>$update_trans_id", "max_tans_id");//and store_id=$cbo_store_name      
		if ($max_issue_id > str_replace("'", "", $update_trans_id)) 
		{
			echo "20**Next Transaction Found";disconnect($con);die;
		}
		$max_issue_date = date("Y-m-d", strtotime($max_issue_date));
		$receive_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_receive_date)));

		$field_array_trans_update="remarks*receive_basis*pi_wo_batch_no*booking_no*booking_without_order*prod_id*transaction_date*supplier_id*store_id*floor_id*room*rack*self*bin_box*order_uom*order_qnty*order_rate*order_amount*order_ile*order_ile_cost*cons_uom*cons_quantity*cons_rate*cons_amount*cons_ile*cons_ile_cost*balance_qnty*balance_amount*payment_over_recv*updated_by*update_date";
		
		$data_array_trans_update=$txt_remarks."*".$cbo_receive_basis."*".$txt_booking_pi_id."*".$txt_booking_pi_no."*".$booking_without_order."*".$prod_id."*".$txt_receive_date."*".$cbo_supplier_name."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$cbo_uom."*".$txt_receive_qnty."*".$rate."*".$txt_amount."*'".$ile."'*'".$ile_cost."'*".$cons_uom."*".$con_qnty."*".$cons_rate."*".$con_amount."*".$con_ile."*".$con_ile_cost."*".$con_qnty."*".$con_amount."*".$cbo_payment_over_recv."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		
		if($db_type==2)
		{
			$txt_item_description=str_replace("(","[",$txt_item_description);
			$txt_item_description=str_replace(")","]",$txt_item_description);
		}

		$save_data=str_replace("'","",$save_data);
		$tot_string=strlen($save_data);
		$count_loop=ceil($tot_string/3900);
		$first_save_data=''; $second_save_data=''; $theRest_save_data='';$count=0; $interval=3900;
		for($i=1;$i<=$count_loop; $i++)
		{
		    if($count_loop>0 && $i==1) $first_save_data=substr($save_data, $count, $interval);
		    if($count_loop>1 && $i==2) $second_save_data=substr($save_data, $count, $interval);
		    if($count_loop>2 && $i==3) $theRest_save_data=substr($save_data, $count, $interval);
		    $count+=3900;
		}
		
		$field_array_dtls_update="remarks*prod_id*booking_id*booking_no*booking_without_order*item_group_id*item_description*brand_supplier*order_uom*order_id*receive_qnty*reject_receive_qnty*rate*amount*ile*ile_cost*gmts_color_id*item_color*gmts_size_id*item_size*save_string*save_string_2*save_string_3*item_description_color_size*cons_uom*cons_qnty*cons_rate*cons_ile*cons_ile_cost*book_keeping_curr*sensitivity*payment_over_recv*material_source*floor*room_no*rack_no*self_no*box_bin_no*updated_by*update_date";
		
		$data_array_dtls_update=$txt_remarks."*".$prod_id."*".$txt_booking_pi_id."*".$txt_booking_pi_no."*".$booking_without_order."*".$cbo_item_group."*'".$item_desc_dtls."'*".$txt_brand_supref."*".$cbo_uom."*".$all_po_id."*".$txt_receive_qnty."*".$txt_reject_recv_qnty."*".$rate."*".$txt_amount."*'".$ile."'*'".$ile_cost."'*'".$gmts_color_id."'*".$txt_item_color_id."*'".$gmts_size_id."'*".$txt_item_size."*'".$first_save_data."'*'".$second_save_data."'*'".$theRest_save_data."'*".$txt_item_description."*".$cons_uom."*".$con_qnty."*".$cons_rate."*".$con_ile."*".$con_ile_cost."*".$con_amount."*".$hidden_sensitivity."*".$cbo_payment_over_recv."*'".$meterial_source."'*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		//echo "10**".$data_array_dtls_update;die;

		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, order_rate, order_amount, inserted_by, insert_date";
		//$id_prop = return_next_id( "id", "order_wise_pro_details", 1 );
		$save_data=explode(",",str_replace("'","",$save_data));
		for($i=0;$i<count($save_data);$i++)
		{
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			$order_dtls=explode("_",$save_data[$i]);
			
			$order_id=$order_dtls[0];
			$order_qnty=$order_dtls[1];
			$order_amount=$rate*$order_qnty;
			
			if($i==0) $add_comma=""; else $add_comma=",";
			
			$data_array_prop.="$add_comma(".$id_prop.",".$update_trans_id.",1,24,".$update_dtls_id.",".$order_id.",".$prod_id.",".$order_qnty.",'".$rate."','".$order_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			//$id_prop = $id_prop+1;
		}
		
		//echo '5****'.$data_array_update;die;
		
		$rID=$rID_adjust=$rID2=$rID3=$rID4=$delete_prop=$rID6=true;
		$rID=sql_update("inv_receive_master",$field_array_update,$data_array_update,"id",$update_id,1);
		if(count($row_prod)>0)
		{
			if($prod_id==str_replace("'","",$previous_prod_id))
			{
				$rID2=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$prod_id,0);
			}
			else
			{
				$rID_adjust=sql_update("product_details_master",$field_array_adjust,$data_array_adjust,"id",$previous_prod_id,0);
				$rID2=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$prod_id,0);
			}
		}
		else
		{
			$rID_adjust=sql_update("product_details_master",$field_array_adjust,$data_array_adjust,"id",$previous_prod_id,0);
			$rID2=sql_insert("product_details_master",$field_array_prod,$data_array_prod,0);
		}
		$rID3=sql_update("inv_transaction",$field_array_trans_update,$data_array_trans_update,"id",$update_trans_id,0);
		$rID4=sql_update("inv_trims_entry_dtls",$field_array_dtls_update,$data_array_dtls_update,"id",$update_dtls_id,0);
		$delete_prop=execute_query( "delete from order_wise_pro_details where dtls_id=$update_dtls_id and trans_id=$update_trans_id and entry_form=24",0);
		//echo "insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;
		if($data_array_prop!="" && str_replace("'","",$booking_without_order)!=1)
		{
			$rID6=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
		}
		
		//echo "5** $rID=$rID_adjust=$rID2=$rID3=$rID4=$delete_prop=$rID6";oci_rollback($con);die;
		
		if($db_type==0)
		{
			if($rID && $rID_adjust && $rID2 && $rID3 && $rID4 && $delete_prop && $rID6)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_recieved_id)."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**0**0**1";
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID_adjust && $rID2 && $rID3 && $rID4 && $delete_prop && $rID6)
			{
				oci_commit($con);  
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_recieved_id)."**0";
			}
			else
			{
				oci_rollback($con);
				echo "6**0**0**1";
			}
		}
		disconnect($con);
		die;
	}
	
	else if ($operation==2)   // Delete Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		$update_id=str_replace("'","",$update_id);
		$previous_prod_id=str_replace("'","",$previous_prod_id);
		$update_dtls_id=str_replace("'","",$update_dtls_id);
		$update_trans_id=str_replace("'","",$update_trans_id);
		//echo "10**$update_id=$previous_prod_id=$update_dtls_id=$update_trans_id";die;
		if($update_id>0 && $previous_prod_id>0 && $update_dtls_id>0 && $update_trans_id>0)
		{
			$previous_data_check=sql_select("select id as rcv_id, cons_quantity as rcv_qnty, cons_amount as rcv_amount  from inv_transaction where transaction_type=1 and id=$update_trans_id and prod_id=$previous_prod_id");
			$previous_check_id=$previous_data_check[0][csf("rcv_id")];
			$previous_qnty=$previous_data_check[0][csf("rcv_qnty")];
			$previous_amount=$previous_data_check[0][csf("rcv_amount")];
			
			if($db_type==0) $row_count_cond=" limit 1"; else $row_count_cond=" and rownum<2";
			$next_operation_check=sql_select("select id as next_id, mst_id as mst_id, transaction_type as transaction_type from inv_transaction where id > $previous_check_id and prod_id=$previous_prod_id and status_active=1 and transaction_type in(2,3,6) $row_count_cond");
			if(count($next_operation_check)>0)
			{
				$next_id=$next_operation_check[0][csf("next_id")];
				$next_mst_id=$next_operation_check[0][csf("mst_id")];
				$next_transaction_type=$next_operation_check[0][csf("transaction_type")];

				if($next_transaction_type==1 || $next_transaction_type==4)
				{
					$next_mrr=return_field_value("recv_number as next_mrr_number","inv_receive_master","id=$next_mst_id","next_mrr_number");
				}
				else if($next_transaction_type==2 || $next_transaction_type==3)
				{
					$next_mrr=return_field_value("issue_number as next_mrr_number","inv_issue_master","id=$next_mst_id","next_mrr_number");
				}
				else
				{
					$next_mrr=return_field_value("transfer_system_id as next_mrr_number","inv_item_transfer_mst","id=$next_mst_id","next_mrr_number");
				}
				echo "20**Next Operation No:- $next_mrr  Found, Delete Not Allow.";
				disconnect($con);die;
				//check_table_status( $_SESSION['menu_id'],0);
			}
			
			$after_goods_pi_check=sql_select("select b.id, a.pi_number from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.after_goods_source=2 and b.after_goods_source=2 and a.goods_rcv_status=1 and a.status_active=1 and b.status_active=1 and b.work_order_dtls_id=$previous_check_id $row_count_cond");
			if(count($after_goods_pi_check)>0)
			{
				$pi_no=$after_goods_pi_check[0][csf("pi_number")];
				echo "20**PI No:- $pi_no  Found, Delete Not Allow.";
				disconnect($con);die;
			}
			
			
			$row_prod=sql_select( "select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id=$previous_prod_id and status_active=1 and is_deleted=0" );
			$prod_id=$row_prod[0][csf('id')];
			$avg_rate_per_unit=$row_prod[0][csf('avg_rate_per_unit')];
			$curr_stock_qnty=$row_prod[0][csf('current_stock')]-$previous_qnty;
			$curr_stock_value=0;
			if ($curr_stock_qnty != 0){
				$curr_stock_value=$row_prod[0][csf('stock_value')]-$previous_amount;
				$avg_rate_per_unit=number_format($curr_stock_value/$curr_stock_qnty,$dec_place[3],'.','');
			}		
			
			$field_array_prod_update="avg_rate_per_unit*current_stock*stock_value*updated_by*update_date";
			$data_array_prod_update=$avg_rate_per_unit."*".$curr_stock_qnty."*".$curr_stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			
			$row_propotionate=sql_select( "select id, po_breakdown_id, quantity, order_rate, order_amount 
			from order_wise_pro_details where trans_id=$previous_check_id and status_active=1 and is_deleted=0" );
			$propotionate_data=array();
			foreach($row_propotionate as $row)
			{
				$all_order_id.=$row[csf("po_breakdown_id")].",";
				$propotionate_data[$row[csf("po_breakdown_id")]]["quantity"]+=$row[csf("quantity")];
				$propotionate_data[$row[csf("po_breakdown_id")]]["order_amount"]+=$row[csf("order_amount")];
			}
			$all_order_id=chop($all_order_id,",");
			$field_array_prod_ord_update="avg_rate*stock_quantity*stock_amount*updated_by*update_date";
			if($all_order_id!="")
			{
				$prod_order_stock=sql_select("select id, po_breakdown_id, stock_quantity, stock_amount 
				from order_wise_stock where prod_id=$previous_prod_id and po_breakdown_id in($all_order_id) and status_active=1 and is_deleted=0 ");
				foreach($prod_order_stock as $row)
				{
					$current_stock_qnty=$row[csf('stock_quantity')]-$propotionate_data[$row[csf("po_breakdown_id")]]["quantity"];
					$current_stock_value=$row[csf('stock_amount')]-$propotionate_data[$row[csf("po_breakdown_id")]]["order_amount"];
					if($current_stock_value>0 && $current_stock_qnty>0)
					{
						$current_avg_rate=number_format($current_stock_value/$current_stock_qnty,$dec_place[3],'.','');
					}
					else
					{
						$current_avg_rate=0;
					}
					
					
					$ord_prod_id_arr[]=$row[csf('id')];
					$data_array_prod_ord_update[$row[csf('id')]]=explode("*",("".$current_avg_rate."*".$current_stock_qnty."*".$current_stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				}
			}
			
			
			$field_arr="status_active*is_deleted*updated_by*update_date";
			$data_arr="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID=$rID2=$rID3=$rID4=$ordProdUpdate=true;
			$rID=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$previous_prod_id,1);
			if(count($ord_prod_id_arr)>0)
			{
				$ordProdUpdate=execute_query(bulk_update_sql_statement("order_wise_stock","id",$field_array_prod_ord_update,$data_array_prod_ord_update,$ord_prod_id_arr));
			}
			//echo "10**$update_trans_id == $update_dtls_id == $update_trans_id";oci_rollback($con);check_table_status( $_SESSION['menu_id'],0);disconnect($con);die;
			$rID2=sql_update("inv_transaction",$field_arr,$data_arr,"id",$update_trans_id,1);
			$rID3=sql_update("inv_trims_entry_dtls",$field_arr,$data_arr,"id",$update_dtls_id,1);
			if($all_order_id!="")
			{
				$rID4=sql_update("order_wise_pro_details",$field_arr,$data_arr,"trans_id",$update_trans_id,1);
			}
			
			//echo "10** $rID && $ordProdUpdate && $rID2 && $rID3 && $rID4";oci_rollback($con);disconnect($con);die;
			if($db_type==0)
			{
				if($rID && $ordProdUpdate && $rID2 && $rID3 && $rID4)
				{
					mysql_query("COMMIT");  
					echo "2**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_recieved_id)."**0";
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "7**0**0**1";
				}
			}
	
			if($db_type==2 || $db_type==1 )
			{
				if($rID && $ordProdUpdate && $rID2 && $rID3 && $rID4)
				{
					oci_commit($con);  
					echo "2**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_recieved_id)."**0";
				}
				else
				{
					oci_rollback($con);
					echo "7**0**0**1";
				}
			}
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);
			die;
		}
	}
	
}

// ######## For 3rd version trims erp 

if($action=="populate_data_from_delivery_challan")
{
	$data_ref=explode("__",$data);
	if($data_ref[2]=="") $data_ref[2]=date('j-M-Y');
	$sql_delivery=sql_select("select currency_id from trims_delivery_mst where status_active=1 and trims_del='$data_ref[0]'");
	$delivery_currency_id=$sql_delivery[0][csf("currency_id")];
	$exchange_rate = set_conversion_rate($delivery_currency_id, $data_ref[2],$data_ref[1]);
	echo "document.getElementById('cbo_currency_id').value 				= '".$delivery_currency_id."';\n";
	echo "document.getElementById('txt_exchange_rate').value 				= '".$exchange_rate."';\n";
}

if ( $action=="save_by_scan" )
{
		
	$color_arr_text_index = return_library_array("select color_name,id from lib_color where status_active=1 and is_deleted=0","color_name","id");
	$size_arr_text_index = return_library_array("select size_name,id from lib_size","size_name","id");
		
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$cbo_supplier_name=147;// 147 default for Trims;
	
	$store_update_upto=str_replace("'","",$store_update_upto);
	if($store_update_upto > 1)
	{
		$cbo_floor=str_replace("'","",$cbo_floor);
		$cbo_room=str_replace("'","",$cbo_room);
		$txt_rack=str_replace("'","",$txt_rack);
		$txt_shelf=str_replace("'","",$txt_shelf);
		$cbo_bin=str_replace("'","",$cbo_bin);
		if($store_update_upto==2)
		{
			$cbo_room=0;
			$txt_rack=0;
			$txt_shelf=0;
			$cbo_bin=0;
		}
		else if($store_update_upto==3)
		{
			$txt_rack=0;
			$txt_shelf=0;
			$cbo_bin=0;
		}
		else if($store_update_upto==4)
		{
			$txt_shelf=0;
			$cbo_bin=0;
		}
		else if($store_update_upto==5)
		{
			$cbo_bin=0;
		}
	}
	else
	{
		$cbo_floor=0;
		$cbo_room=0;
		$txt_rack=0;
		$txt_shelf=0;
		$cbo_bin=0;
	}
	//insert start .................................
	$con = connect();
	//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
	
	$trims_recv_num=''; $trims_update_id='';
	if(str_replace("'","",$update_id)=="")
	{
		if($db_type==0) $year_cond="YEAR(insert_date)"; 
		else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
		else $year_cond="";//defined Later
		//$txt_receive_chal_no=trim(str_replace("'","",$txt_receive_chal_no),5);
		$txt_receive_chal_no=str_replace("'","",$txt_receive_chal_no); 
		//$mdbc=trims_erpDBConnect();
		
		//$sql="select a.id, a.td_barcode, a.system_id_string, a.system_id_no, a.customer_source, a.customer_name, a.customer_id, a.item_category, a.delivery_date, a.challan_no, a.gate_pass_no,b.wo_dtls_tbl_id, b.wo_no, b.wo_id, b.section, b.item_group, b.item_description, b.color_name, b.customer_po_no, b.customer_po_id,c.customer_order_id as order_id,c.rate,d.exchange_rate, b.customer_buyer_id, b.wo_qty, b.rmg_color, b.rmg_size, b.uom, b.current_delivery_qty, b.claim_qty 
		//from trims_delivery_mst a,trims_delivery_dtls b,trims_order_receive_dtls c,trims_order_receive_mst d where a.id=b.mst_tbl_id and b.customer_po_id=c.id and d.id=c.mst_tbl_id and a.td_barcode=$txt_receive_chal_no and d.customer_source=1";
		$sql="select a.id, a.trims_del as system_id_string, a.del_no_prefix_num as system_id_no, 4 as item_category, a.delivery_date, a.challan_no, a.gate_pass_no, a.currency_id, b.job_dtls_id as wo_dtls_tbl_id, b.order_no as wo_no, b.order_id as wo_id, b.section, b.item_group, b.description as item_description, b.order_quantity as production_qnty, b.workoder_qty as wo_qty, b.gmts_color_id as gmts_color, b.gmts_size_id as gmts_size, b.color_id as item_color, b.size_name as item_size, b.order_uom as uom, b.delevery_qty as current_delivery_qty, b.claim_qty, b.order_receive_rate as rate, a.within_group, b.receive_dtls_id as order_id, b.buyer_po_id 
 		from trims_delivery_mst a,trims_delivery_dtls b
		where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trims_del='$txt_receive_chal_no'";
		//echo $sql;die;
		$sql_result= sql_select($sql);
		$within_group_delivery=$sql_result[0][csf("within_group")]*1;
		if($within_group_delivery==2)
		{
			echo "20**0**"."&nbsp;"."**0**";die;
		}
		  
		$i=0;
		foreach($sql_result as $rows)
		{
			$within_group=$rows[csf('within_group')];
			if($rows[csf('within_group')]==1) $order_ids=$rows[csf('buyer_po_id')]; else $order_ids=$rows[csf('order_id')];
			$delivary_data[]=array(
				'id'=>$rows[csf('id')],
				'td_barcode'=>$rows[csf('td_barcode')],
				'system_id_string'=>$rows[csf('system_id_string')],
				'system_id_no'=>$rows[csf('system_id_no')],
				'customer_source'=>$rows[csf('customer_source')],
				'item_category'=>$rows[csf('item_category')],
				'delivery_date'=>$rows[csf('delivery_date')],
				'challan_no'=>$rows[csf('challan_no')],
				'gate_pass_no'=>$rows[csf('gate_pass_no')],
				'wo_dtls_tbl_id'=>$rows[csf('wo_dtls_tbl_id')],
				'wo_no'=>$rows[csf('wo_no')],
				'wo_id'=>$rows[csf('wo_id')],
				'section'=>$rows[csf('section')],
				'item_group'=>$rows[csf('item_group')],
				'item_description'=>$rows[csf('item_description')],
				'gmts_color'=>$rows[csf('gmts_color')],
				'gmts_size'=>$rows[csf('gmts_size')],
				'item_color'=>$rows[csf('item_color')],
				'item_size'=>$rows[csf('item_size')],
				'order_id'=>$order_ids,
				'wo_qty'=>$rows[csf('wo_qty')],
				'uom'=>$rows[csf('uom')],
				'rate'=>$rows[csf('rate')],
				'currency_id'=>$rows[csf('currency_id')],
				'current_delivery_qty'=>$rows[csf('current_delivery_qty')],
				'claim_qty'=>$rows[csf('claim_qty')]
				);	
				$booking_id[$rows[csf('order_id')]]=$order_ids;
				$booking_no=$rows[csf('wo_no')];
				$delivery_system_id_string=$rows[csf('system_id_string')];
				$txt_booking_pi_no=$rows[csf('wo_no')];
				$txt_booking_pi_id=$rows[csf('wo_id')];
		}
		//echo "<pre>";print_r($delivary_data);die;
		
		if($delivery_system_id_string){
		$check_duplicate = return_field_value("id","inv_receive_master","challan_no='$delivery_system_id_string' and receive_basis=$cbo_receive_basis and entry_form='24' and status_active=1");
		}
		
		//....................................................................
		if(count($delivary_data)<1){echo "12**0**"."&nbsp;"."**0**";die;}
		elseif($check_duplicate){echo "11**0**"."&nbsp;"."**0**";die;}
		$cbo_source=3;//default Non-EPZ=3;
		$booking_without_order=0;//default 0;
		//........................................................
		
		//$po_id
		$booking_data= sql_select("select a.id as booking_id, a.booking_no, a.source, b.po_break_down_id from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.id in(".implode(",",$booking_id).") and a.item_category=4 and a.status_active = 1 and a.is_deleted = 0"); 
		foreach($booking_data as $rows)
		{	
			$booking_id_arr[$rows[csf('booking_no')]]=$rows[csf('booking_id')];
			$booking_source_arr[$rows[csf('booking_no')]]=$rows[csf('source')];
			$po_id[$rows[csf('po_break_down_id')]]=$rows[csf('po_break_down_id')];
		}
	
	
		//$new_trims_recv_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'TRE', date("Y",time()), 5, "select recv_number_prefix, recv_number_prefix_num from inv_receive_master where company_id=$cbo_company_id and entry_form='24' and $year_cond=".date('Y',time())." order by id desc", "recv_number_prefix", "recv_number_prefix_num" ));
		
		//$id=return_next_id( "id", "inv_receive_master", 1 ) ;
		
		$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);
		$new_trims_recv_system_id = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master",$con,1,$cbo_company_id,'TRE',24,date("Y",time()) ));
		$field_array="id, recv_number_prefix, recv_number_prefix_num, recv_number, entry_form, item_category, receive_basis, company_id, receive_date, challan_no, challan_date, booking_id, booking_no, booking_without_order, store_id, lc_no, source, supplier_id, currency_id, exchange_rate, within_group, is_multi, inserted_by, insert_date";
		
		$data_array="(".$id.",'".$new_trims_recv_system_id[1]."',".$new_trims_recv_system_id[2].",'".$new_trims_recv_system_id[0]."',24,4,".$cbo_receive_basis.",".$cbo_company_id.",".$txt_receive_date.",'".$delivery_system_id_string."',".$txt_challan_date.",'".$txt_booking_pi_id."','".$txt_booking_pi_no."','".$booking_without_order."',".$cbo_store_name.",'".$lc_id."',".$cbo_source.",".$cbo_supplier_name.",".$cbo_currency_id.",".$txt_exchange_rate.",'".$within_group."',1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		//echo "insert into inv_receive_master (".$field_array.") values ".$data_array;die;
		
		$trims_recv_num=$new_trims_recv_system_id[0];
		$trims_update_id=$id;
	}

	//details table entry here START-----------------------------------//		

	//file--------
	
	//$prod_new_id=return_next_id( "id", "product_details_master", 1 ) ;
	
	$field_array_prod="id, company_id, store_id, item_category_id, entry_form, item_group_id, item_description, product_name_details, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, color, item_color, gmts_size, item_size, brand_supplier, inserted_by, insert_date";
	$data_array_prod='';
	//--------------------------
	$field_array_prod_update="store_id*avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
	$data_array_prod_update='';
	//--------------------------
	
	//$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
	$field_array_trans="id, mst_id, receive_basis, pi_wo_batch_no, booking_no, booking_without_order, company_id, supplier_id, prod_id, item_category, transaction_type, transaction_date, store_id, order_uom, order_qnty, order_rate, order_amount, order_ile, order_ile_cost, cons_uom, cons_quantity, cons_rate, cons_amount, cons_ile, cons_ile_cost, balance_qnty, balance_amount, payment_over_recv, inserted_by, insert_date, floor_id, room, rack, self, bin_box";
	$data_array_trans='';	
	//--------------------------
	//$id_dtls=return_next_id( "id", "inv_trims_entry_dtls", 1 ) ;
	$field_array_dtls="id, mst_id, trans_id, booking_id, booking_no, booking_without_order, prod_id, item_group_id, item_description, brand_supplier, order_uom, order_id, receive_qnty, reject_receive_qnty, rate, amount, ile, ile_cost, gmts_color_id, item_color, gmts_size_id, item_size, save_string, save_string_2, save_string_3, item_description_color_size, cons_uom, cons_qnty, cons_rate, cons_ile, cons_ile_cost, book_keeping_curr, sensitivity, payment_over_recv, inserted_by, insert_date, floor, room_no, rack_no, self_no, box_bin_no";
	$data_array_dtls='';
	//--------------------------	
	//$id_prop = return_next_id( "id", "order_wise_pro_details", 1 );
	$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, order_rate, order_amount, inserted_by, insert_date";
	$data_array_prop='';

	
	
  	$i=0;
 	foreach($delivary_data as $rows)
  	{
		//variable assign here.....................
		$cbo_item_group=$rows['item_group'];
		$txt_exchange_rate=str_replace("'","",$txt_exchange_rate);
		$txt_receive_qnty=$rows['current_delivery_qty'];
		$hidden_item_description=$rows['item_description'];
		$txt_booking_pi_id=$booking_id_arr[$rows['wo_no']];
		$txt_booking_pi_no=$rows['wo_no'];
		$cbo_uom=$rows['uom'];
		$txt_rate=$rows['rate'];
		$txt_amount=$rows['rate']*$rows['current_delivery_qty'];
		if($rows['item_color'])
		{
			$txt_item_color_id=$rows['item_color'];
		}
		else{$txt_item_color_id=0;}
			
		if($rows['item_size']){
			$txt_item_size_id=$rows['item_size'];
		}
		else{$txt_item_size_id="";}
		
		if($rows['gmts_color'])
		{
			$gmts_color_id=$rows['gmts_color'];
		}
		else{$gmts_color_id=0;}
			
		if($rows['gmts_size']){
			$gmts_size_id=$rows['gmts_size'];
		}
		else{$gmts_size_id=0;}
		
		$txt_item_description=$rows['item_description'];
		$order_qnty=$rows['wo_qty'];
		$save_data=$rows['order_id'].'_'.$rows['current_delivery_qty'];
		$txt_item_size=0;
		$txt_gmts_color_id=0;
		$cbo_payment_over_recv=0; // 0=yes,1=No;		
		$all_po_id=$rows['order_id'];
		
		
		if($i==0) $add_comma=""; else $add_comma=",";
			
		$ile=return_field_value("standard","variable_inv_ile_standard","source=$cbo_source and company_name=$cbo_company_id and category=4 and status_active=1 and is_deleted=0");
		
		$ile_cost = str_replace("'","",$txt_ile);
		
		if($db_type==0)
		{
			$concattS = explode(",",return_field_value("concat(trim_uom,',',conversion_factor)","lib_item_group","id=$cbo_item_group")); 
		}
		else
		{
			$concattS = explode(",",return_field_value("trim_uom || ',' || conversion_factor as data","lib_item_group","id=$cbo_item_group","data")); 
		}
		
		$cons_uom = $concattS[0];
		$conversion_factor = $concattS[1];
		
		$exchange_rate = str_replace("'","",$txt_exchange_rate);
		$txt_receive_qnty = str_replace("'","",$txt_receive_qnty);
		$con_qnty = $conversion_factor*$txt_receive_qnty;
		
		$rate = str_replace("'","",$txt_rate);
		$domestic_rate = return_domestic_rate($rate,$ile_cost,$exchange_rate,$conversion_factor);
		$cons_rate = number_format($domestic_rate,$dec_place[3],".","");
		$con_amount = $cons_rate*$con_qnty;
		$con_ile = $ile/$conversion_factor;
		$con_ile_cost = ($con_ile/100)*$cons_rate;
		
		$item_desc='';
		if(str_replace("'","",$cbo_receive_basis)==12)
		{
			$item_desc=str_replace("'","",$txt_item_description);
		}
		
		if(str_replace("'","",$txt_brand_supref)!="" || $db_type==0)
		{
			$brand_supref_cond="brand_supplier=$txt_brand_supref";
		}
		else 
		{
			$brand_supref_cond="brand_supplier is null";
		}
		
		 
		
		if($db_type==2)
		{
			$item_desc=str_replace("(","[",$item_desc);
			$item_desc=str_replace(")","]",$item_desc);
		}
		//echo '5**'.$prod_id;die;
		// ################product data ready ################//
		$key=str_replace("'","",$cbo_company_id).'**'.$rows['item_category'].'**'.$cbo_item_group.'**'.$item_desc.'**'.$txt_item_color_id.'**'.$txt_item_size_id.'**'.$gmts_color_id.'**'.$gmts_size_id.'**24';
		if($pro_data_arr[$key]['por_id']=='')
		{
			//echo $key."<br>";
			//echo "select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where company_id=$cbo_company_id and item_category_id=4 and entry_form=24 and item_group_id=$cbo_item_group and item_description='$item_desc' and item_color=$txt_item_color_id and item_size='$txt_item_size_id' and color=$gmts_color_id and gmts_size='$gmts_size_id' and status_active=1 and is_deleted=0";die;	
			$row_prod=sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where company_id=$cbo_company_id and item_category_id=4 and entry_form=24 and item_group_id=$cbo_item_group and item_description='$item_desc' and item_color=$txt_item_color_id and item_size='$txt_item_size_id' and color=$gmts_color_id and gmts_size='$gmts_size_id' and status_active=1 and is_deleted=0");
			
			if(count($row_prod)>0)
			{
				$prod_id=$row_prod[0][csf('id')]; 
				$pro_data_arr[$key]['por_id']=$prod_id;
			
				$pro_data_arr[$key]['id']=$row_prod[0][csf('id')];
				$pro_data_arr[$key]['current_stock']=$row_prod[0][csf('current_stock')];
				$pro_data_arr[$key]['stock_value']=$row_prod[0][csf('stock_value')];

			}
			else
			{
				$prod_new_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
				$prod_id=$prod_new_id; 
				$pro_data_arr[$key]['por_id']=$prod_new_id; 
				//$prod_new_id++; 
			}
		}
		else
		{
			$prod_id=$pro_data_arr[$key]['por_id'];	
		}
				
		$pro_data_arr[$key]['qty']+=$con_qnty;
		$pro_data_arr[$key]['amu']+=$con_amount;
		$pro_data_arr[$key]['uom']=$cons_uom;
		
		$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		$data_array_trans.="$add_comma(".$id_trans.",".$trims_update_id.",".$cbo_receive_basis.",'".$txt_booking_pi_id."','".$txt_booking_pi_no."','".$booking_without_order."',".$cbo_company_id.",".$cbo_supplier_name.",".$prod_id.",4,1,".$txt_receive_date.",".$cbo_store_name.",".$cbo_uom.",".$txt_receive_qnty.",".$rate.",".$txt_amount.",'".$ile."','".$ile_cost."',".$cons_uom.",".$con_qnty.",".$cons_rate.",".$con_amount.",".$con_ile.",".$con_ile_cost.",".$con_qnty.",".$con_amount.",'".$cbo_payment_over_recv."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$cbo_floor."','".$cbo_room."','".$txt_rack."','".$txt_shelf."','".$cbo_bin."')";
		//$id_trans++; $cbo_floor=0; $cbo_room=0; $txt_rack=0; $txt_shelf=0; $cbo_bin=0;
		
		//echo "10**insert into inv_transaction (".$field_array_trans.") values ".data_array_trans;die;
		
		if($db_type==2)
		{
			$txt_item_description=str_replace("(","[",$txt_item_description);
			$txt_item_description=str_replace(")","]",$txt_item_description);
		}
		
		$save_data=str_replace("'","",$save_data);
		$tot_string=strlen($save_data);
		$count_loop=ceil($tot_string/3900);
		$first_save_data=''; $second_save_data=''; $theRest_save_data='';$count=0; $interval=3900;
		for($i=1;$i<=$count_loop; $i++)
		{
		    if($count_loop>0 && $i==1) $first_save_data=substr($save_data, $count, $interval);
		    if($count_loop>1 && $i==2) $second_save_data=substr($save_data, $count, $interval);
		    if($count_loop>2 && $i==3) $theRest_save_data=substr($save_data, $count, $interval);
		    $count+=3900;
		}

		$id_dtls = return_next_id_by_sequence("INV_TRIMS_ENTRY_DTLS_PK_SEQ", "inv_trims_entry_dtls", $con);
		$data_array_dtls.="$add_comma(".$id_dtls.",".$trims_update_id.",".$id_trans.",'".$txt_booking_pi_id."','".$txt_booking_pi_no."','".$booking_without_order."','".$prod_id."','".$cbo_item_group."','".$item_desc."','".$txt_banrd_supref."','".$cbo_uom."','".$all_po_id."','".$txt_receive_qnty."','".$txt_reject_recv_qnty."','".$rate."','".$txt_amount."','".$ile."','".$ile_cost."','".$gmts_color_id."','".$txt_item_color_id."','".$gmts_size_id."','".$txt_item_size_id."','".$first_save_data."','".$second_save_data."','".$theRest_save_data."','".$txt_item_description."','".$cons_uom."','".$con_qnty."','".$cons_rate."','".$con_ile."','".$con_ile_cost."','".$con_amount."','".$hidden_sensitivity."','".$cbo_payment_over_recv."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$cbo_floor."','".$cbo_room."','".$txt_rack."','".$txt_shelf."','".$cbo_bin."')";
		
		
		//echo "insert into inv_trims_entry_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		
		$save_data=explode(",",str_replace("'","",$save_data));
		foreach($save_data as $val)
		{
			$order_dtls=explode("_",$val);
			
			$order_id=$order_dtls[0];
			$order_qnty=$order_dtls[1];
			$ord_rate=0;$ord_amt=0;
			if($rows['rate']>0)
			{ 
				$ord_rate=$rows['rate'];
				$ord_amt=$order_qnty*$rows['rate'];
			}
			
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			$data_array_prop.="$add_comma(".$id_prop.",".$id_trans.",1,24,".$id_dtls.",".$order_id.",".$prod_id.",".$order_qnty.",'".$ord_rate."','".$ord_amt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			//$id_prop = $id_prop+1;
		}
		//$id_dtls++;
		$i++;	
	}//end while...............................
	
	
	//strat product query.................................
	//$key=str_replace("'","",$cbo_company_id).'**'.$rows['item_category'].'**'.$cbo_item_group.'**'.$item_desc.'**'.$txt_item_color_id.'**'.$txt_item_size_id.'**'.$gmts_color_id.'**'.$gmts_size_id.'**24';
	$m=0;
	foreach($pro_data_arr as $key=>$rows){
		list($cbo_company_id,$item_category,$cbo_item_group,$item_desc,$txt_item_color_id,$txt_item_size_id,$gmts_color_id,$gmts_size_id,$form_no)=explode('**',$key);	
		$con_qnty=$rows['qty'];
		$con_amount=$rows['amu'];
		$prod_id=$rows['por_id'];
		$cons_rate=number_format($con_amount/$con_qnty,$dec_place[3],'.','');
		$cons_uom=$rows['uom'];
		if($m==0) $add_comma=""; else $add_comma=",";
		
		if($pro_data_arr[$key]['current_stock'] !="")
		{
			$prod_id_up=$pro_data_arr[$key]['id'];
			$curr_stock_qnty=$pro_data_arr[$key]['current_stock']+$con_qnty;
			$curr_stock_value=$pro_data_arr[$key]['stock_value']+$con_amount;
			$avg_rate_per_unit=number_format($curr_stock_value/$curr_stock_qnty,$dec_place[3],'.','');
			$id_arr[]=$prod_id_up;
			$data_array_prod_update[$prod_id_up] =explode(",",($cbo_store_name.",".$avg_rate_per_unit.",".$con_qnty.",".$curr_stock_qnty.",".$curr_stock_value.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'"));

		}
		else
		{
			$item_name =$trim_group_arr[str_replace("'","",$cbo_item_group)]['name'];
			$prod_name_dtls=$item_name.", ".trim(str_replace("'","",$item_desc));
			
			if($db_type==2)
			{
				$prod_name_dtls=str_replace("(","[",$prod_name_dtls);
				$prod_name_dtls=str_replace(")","]",$prod_name_dtls);
			}
			
			$data_array_prod.="$add_comma(".$prod_id.",".$cbo_company_id.",".$cbo_store_name.",4,24,".$cbo_item_group.",'".$item_desc."','".$prod_name_dtls."','".$cons_uom."',".$cons_rate.",".$con_qnty.",".$con_qnty.",".$con_amount.",'".$gmts_color_id."','".$txt_item_color_id."','".$gmts_size_id."','".$txt_item_size_id."','".$txt_brand_supref."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			//$prod_id++;
			$m++;	
		}		
	
	}
	
	//end product query..................................
  	//trims_erpDBClose($mdbc);
	
	//echo "insert into inv_trims_entry_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;	
	//echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;
	//echo $data_array;echo '<br/>';	
	//echo $data_array_prod;	
	//echo $data_array_trans;echo '<br>';	
	//echo $data_array_prod_update;	 die;
	// echo '5***'.$gmts_size_id;die;
	//echo '5***';echo var_dump($data_array_prod_update);die;
	//echo '5***';print_r($data_array_prod_update);die;
	
	if(str_replace("'","",$update_id)=="")
	{
		$rID=sql_insert("inv_receive_master",$field_array,$data_array,0);
		if($rID) $flag=1; else $flag=0;
	}
	
	//echo '5***'.$data_array;die;
	
	if(count($id_arr)>0)
	{
	//$rID2=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$prod_id,0);
		$rID2=execute_query(bulk_update_sql_statement("product_details_master","id", $field_array_prod_update,$data_array_prod_update,$id_arr),1);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
	}
	if($data_array_prod)
	{
		 //echo "5**insert into product_details_master (".$field_array_prod.") values ".$data_array_prod;die;
		$rID2=sql_insert("product_details_master",$field_array_prod,$data_array_prod,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
	}
	//echo "5**".$field_array_prod_update;die;
	
	//echo "insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;
	$rID3=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
	if($flag==1) 
	{
		if($rID3) $flag=1; else $flag=0; 
	} 
	
	//echo "insert into inv_trims_entry_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
	$rID4=sql_insert("inv_trims_entry_dtls",$field_array_dtls,$data_array_dtls,0);
	if($flag==1) 
	{
		if($rID4) $flag=1; else $flag=0; 
	} 
	
	$rID6=true;	
	if($data_array_prop!="" && str_replace("'","",$booking_without_order)!=1)
	{
		$rID6=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
		if($flag==1) 
		{
			if($rID6) $flag=1; else $flag=0; 
		} 
	}
	
	
	//echo "5**".$rID."**".$rID2."**".$rID3."**".$rID4."**".$rID6; oci_rollback($con); die;
	
	if($db_type==0)
	{
		if($flag==1)
		{
			mysql_query("COMMIT");  
			echo "0**".$trims_update_id."**".$trims_recv_num."**0**".$delivery_system_id_string;
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
			echo "0**".$trims_update_id."**".$trims_recv_num."**0**".$delivery_system_id_string;
		}
		else
		{
			oci_rollback($con);
			echo "5**0**"."&nbsp;"."**0";
		}
	}

	disconnect($con);
	die;
	//insert end ....................................	
}

######## For 2nd version trims erp 
 
/*if ( $action=="save_by_scan" )
{
		
	$color_arr_text_index = return_library_array("select color_name,id from lib_color","color_name","id");
	$size_arr_text_index = return_library_array("select size_name,id from lib_size","size_name","id");
		
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$cbo_supplier_name=147;// 147 default for Trims;
		
	$trims_recv_num=''; $trims_update_id='';
	if(str_replace("'","",$update_id)=="")
	{
		if($db_type==0) $year_cond="YEAR(insert_date)"; 
		else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
		else $year_cond="";//defined Later
		//$txt_receive_chal_no=trim(str_replace("'","",$txt_receive_chal_no),5);
		$txt_receive_chal_no=substr(str_replace("'","",$txt_receive_chal_no), 0, -1); 
		disconnect($con);
		$mdbc=trims_erpDBConnect();
		
		$sql="select a.id, a.td_barcode, a.system_id_string, a.system_id_no, a.customer_source, a.customer_name, a.customer_id, a.item_category, a.delivery_date, a.challan_no, a.gate_pass_no,b.wo_dtls_tbl_id, b.wo_no, b.wo_id, b.section, b.item_group, b.item_description, b.color_name, b.customer_po_no, b.customer_po_id,c.customer_order_id as order_id,c.rate,d.exchange_rate, b.customer_buyer_id, b.wo_qty, b.rmg_color, b.rmg_size, b.uom, b.previous_delivery_qty, b.current_delivery_qty, b.claim_qty from trims_delivery_mst a,trims_delivery_dtls b,trims_order_receive_dtls c,trims_order_receive_mst d where a.id=b.mst_tbl_id and b.customer_po_id=c.id and d.id=c.mst_tbl_id and a.td_barcode=$txt_receive_chal_no and d.customer_source=1";
		
	
		$sql_result= mysqli_query($mdbc,$sql);
		  $i=0;
		  while ($rows = mysqli_fetch_array($sql_result))
			{
				$delivary_data[]=array(
					'id'=>$rows['id'],
					'td_barcode'=>$rows['td_barcode'],
					'system_id_string'=>$rows['system_id_string'],
					'system_id_no'=>$rows['system_id_no'],
					'customer_source'=>$rows['customer_source'],
					'customer_name'=>$rows['customer_name'],
					'customer_id'=>$rows['customer_id'],
					'item_category'=>$rows['item_category'],
					'delivery_date'=>$rows['delivery_date'],
					'challan_no'=>$rows['challan_no'],
					'gate_pass_no'=>$rows['gate_pass_no'],
					'wo_dtls_tbl_id'=>$rows['wo_dtls_tbl_id'],
					'wo_no'=>$rows['wo_no'],
					'wo_id'=>$rows['wo_id'],
					'section'=>$rows['section'],
					'item_group'=>$rows['item_group'],
					'item_description'=>$rows['item_description'],
					'color_name'=>$rows['color_name'],
					'customer_po_no'=>$rows['customer_po_no'],
					'customer_po_id'=>$rows['customer_po_id'],
					'order_id'=>$rows['order_id'],
					'customer_buyer_id'=>$rows['customer_buyer_id'],
					'wo_qty'=>$rows['wo_qty'],
					'rmg_color'=>$rows['rmg_color'],
					'rmg_size'=>$rows['rmg_size'],
					'uom'=>$rows['uom'],
					'rate'=>$rows['rate'],
					'exchange_rate'=>$rows['exchange_rate'],
					'previous_delivery_qty'=>$rows['previous_delivery_qty'],
					'current_delivery_qty'=>$rows['current_delivery_qty'],
					'claim_qty'=>$rows['claim_qty']
					);	
					$po_id[$rows['order_id']]=$rows['order_id'];
					$booking_no=$rows['wo_no'];
					$delivery_system_id_string=$rows['system_id_string'];
			}
	trims_erpDBClose($mdbc);
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
		
		
		if($delivery_system_id_string){
		$check_duplicate = return_field_value("id","inv_receive_master","challan_no='$delivery_system_id_string' and receive_basis=$cbo_receive_basis and entry_form='24'");
		}
		
		//....................................................................
		if(count($delivary_data)<1){echo "12**0**"."&nbsp;"."**0**";die;}
		elseif($check_duplicate){echo "11**0**"."&nbsp;"."**0**";die;}
		$cbo_source=3;//default Non-EPZ=3;
		$booking_without_order=0;//default 0;
		//........................................................
		
		
	$booking_data= sql_select("select a.id as booking_id,a.booking_no,a.source from wo_booking_mst a , wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in(".implode(",",$po_id).") and a.item_category=4 and a.status_active = 1 and a.is_deleted = 0"); 
	foreach($booking_data as $rows)
	{	
		$booking_id_arr[$rows[csf('booking_no')]]=$rows[csf('booking_id')];
		$booking_source_arr[$rows[csf('booking_no')]]=$rows[csf('source')];
	}
	
		$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);
		$new_trims_recv_system_id = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master",$con,1,$cbo_company_id,'TRE',24,date("Y",time()) ));
		$field_array="id, recv_number_prefix, recv_number_prefix_num, recv_number, entry_form, item_category, receive_basis, company_id, receive_date, challan_no, challan_date, booking_id, booking_no, booking_without_order, store_id, lc_no, source, supplier_id, currency_id, exchange_rate, is_multi, inserted_by, insert_date";
		
		$data_array="(".$id.",'".$new_trims_recv_system_id[1]."',".$new_trims_recv_system_id[2].",'".$new_trims_recv_system_id[0]."',24,4,".$cbo_receive_basis.",".$cbo_company_id.",".$txt_receive_date.",'".$delivery_system_id_string."',".$txt_challan_date.",'".$txt_booking_pi_id."','".$txt_booking_pi_no."','".$booking_without_order."',".$cbo_store_name.",'".$lc_id."',".$cbo_source.",".$cbo_supplier_name.",".$cbo_currency_id.",".$txt_exchange_rate.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		//echo "insert into inv_receive_master (".$field_array.") values ".$data_array;die;
		// $rID=sql_insert("inv_receive_master",$field_array,$data_array,0);
		//if($rID) $flag=1; else $flag=0;
		
		$trims_recv_num=$new_trims_recv_system_id[0];
		$trims_update_id=$id;
	}

	
	$field_array_prod="id, company_id, store_id, item_category_id, entry_form, item_group_id, item_description, product_name_details, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, color, item_color, gmts_size, item_size, brand_supplier, inserted_by, insert_date";
	$data_array_prod='';
	//--------------------------
	$field_array_prod_update="store_id*avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
	$data_array_prod_update='';
	//--------------------------
	
	//$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
	$field_array_trans="id, mst_id, receive_basis, pi_wo_batch_no, booking_no, booking_without_order, company_id, supplier_id, prod_id, item_category, transaction_type, transaction_date, store_id, order_uom, order_qnty, order_rate, order_amount, order_ile, order_ile_cost, cons_uom, cons_quantity, cons_rate, cons_amount, cons_ile, cons_ile_cost, balance_qnty, balance_amount, payment_over_recv, inserted_by, insert_date";
	$data_array_trans='';	
	//--------------------------
	//$id_dtls=return_next_id( "id", "inv_trims_entry_dtls", 1 ) ;
	$field_array_dtls="id, mst_id, trans_id, booking_id, booking_no, booking_without_order, prod_id, item_group_id, item_description, brand_supplier, order_uom, order_id, receive_qnty, reject_receive_qnty, rate, amount, ile, ile_cost, gmts_color_id, item_color, gmts_size_id, item_size, save_string, item_description_color_size, cons_uom, cons_qnty, cons_rate, cons_ile, cons_ile_cost, book_keeping_curr, sensitivity, payment_over_recv, inserted_by, insert_date";
	$data_array_dtls='';
	//--------------------------	
	//$id_prop = return_next_id( "id", "order_wise_pro_details", 1 );
	$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, inserted_by, insert_date";
	$data_array_prop='';

	$i=0;
	foreach($delivary_data as $rows)
  	{
		
		//variable assign here.....................
		$cbo_item_group=$rows['item_group'];
		$txt_exchange_rate=$rows['exchange_rate'];
		$txt_receive_qnty=$rows['current_delivery_qty'];
		$hidden_item_description=$rows['item_description'];
		$txt_booking_pi_id=$booking_id_arr[$rows['wo_no']];
		$txt_booking_pi_no=$rows['wo_no'];
		$cbo_uom=$rows['uom'];
		$txt_rate=$rows['rate'];
		$txt_amount=$rows['rate']*$rows['current_delivery_qty'];
		if($color_arr_text_index[$rows['rmg_color']] and $rows['rmg_color'])
		{
			$txt_item_color_id=$color_arr_text_index[$rows['rmg_color']];
		}
		else{$txt_item_color_id=0;}
			
		if($size_arr_text_index[$rows['rmg_size']] and $rows['rmg_size']){
			$txt_gmts_size_id=$size_arr_text_index[$rows['rmg_size']];
			}
			else{$txt_gmts_size_id=0;}
		
		$txt_item_description=$rows['item_description'];
		$order_qnty=$rows['wo_qty'];
		$save_data=$rows['order_id'].'_'.$rows['current_delivery_qty'];
		$txt_item_size=0;
		$txt_gmts_color_id=0;
		$cbo_payment_over_recv=0; // 0=yes,1=No;		
		$all_po_id=$rows['order_id'];
		
		
		if($i==0) $add_comma=""; else $add_comma=",";
			
		$ile=return_field_value("standard","variable_inv_ile_standard","source=$cbo_source and company_name=$cbo_company_id and category=4 and item_group=$cbo_item_group and status_active=1 and is_deleted=0");
		
		$ile_cost = str_replace("'","",$txt_ile);
		
		if($db_type==0)
		{
			$concattS = explode(",",return_field_value("concat(trim_uom,',',conversion_factor)","lib_item_group","id=$cbo_item_group")); 
		}
		else
		{
			$concattS = explode(",",return_field_value("trim_uom || ',' || conversion_factor as data","lib_item_group","id=$cbo_item_group","data")); 
		}
		
		$cons_uom = $concattS[0];
		$conversion_factor = $concattS[1];
		
		$exchange_rate = str_replace("'","",$txt_exchange_rate);
		$txt_receive_qnty = str_replace("'","",$txt_receive_qnty);
		$con_qnty = $conversion_factor*$txt_receive_qnty;
		
		if(str_replace("'","",$cbo_payment_over_recv)==0)
		{
			$rate = str_replace("'","",$txt_rate);
			$domestic_rate = return_domestic_rate($rate,$ile_cost,$exchange_rate,$conversion_factor);
			$cons_rate = number_format($domestic_rate,$dec_place[3],".","");
			$con_amount = $cons_rate*$con_qnty;
			$con_ile = $ile/$conversion_factor;
			$con_ile_cost = ($con_ile/100)*$cons_rate;
		}
		else
		{
			$cons_rate=0;
			$con_amount=0;
			$con_ile=0;
			$con_ile_cost=0;
			$txt_amount=0;
			$rate=0;
		}
		
		$item_desc=''; $gmts_color_id=0; $gmts_size_id=0; 
		
		if(str_replace("'","",$cbo_receive_basis)==12)
		{
			$item_desc=str_replace("'","",$txt_item_description);
			$gmts_color_id=str_replace("'","",$txt_gmts_color_id);
			$gmts_size_id=str_replace("'","",$txt_gmts_size_id);
		}
		
		if(str_replace("'","",$txt_brand_supref)!="" || $db_type==0)
		{
			$brand_supref_cond="brand_supplier=$txt_brand_supref";
		}
		else 
		{
			$brand_supref_cond="brand_supplier is null";
		}
		
		if($gmts_size_id=="") $gmts_size_id=0;
		if($gmts_color_id=="") $gmts_color_id=0;
		if(str_replace("'","",$txt_item_color_id)=="") $txt_item_color_id=0; 
		
		if($db_type==2)
		{
			$item_desc=str_replace("(","[",$item_desc);
			$item_desc=str_replace(")","]",$item_desc);
		}
			//echo '5**'.$prod_id;die;
		//#######################product data ready#######################//
		$key=str_replace("'","",$cbo_company_id).'**'.$rows['item_category'].'**'.$cbo_item_group.'**'.$item_desc.'**'.$txt_item_color_id.'**'.$gmts_size_id.'**24';
		if($pro_data_arr[$key]['por_id']=='')
		{	
			$row_prod=sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where company_id=$cbo_company_id and item_category_id=4 and entry_form=24 and item_group_id=$cbo_item_group and item_description='$item_desc' and item_color=$txt_item_color_id and gmts_size='$gmts_size_id' and color=0 and item_size=0 and status_active=1 and is_deleted=0");
			
			if(count($row_prod)>0)
			{
				$prod_id=$row_prod[0][csf('id')]; $pro_data_arr[$key]['por_id']=$prod_id;
			
				$pro_data_arr[$key]['id']=$row_prod[0][csf('id')];
				$pro_data_arr[$key]['current_stock']=$row_prod[0][csf('current_stock')];
				$pro_data_arr[$key]['stock_value']=$row_prod[0][csf('stock_value')];

			}
			else
			{
				$prod_new_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
				$prod_id=$prod_new_id; 
				$pro_data_arr[$key]['por_id']=$prod_new_id; 
				//$prod_new_id++; 
			}
		}
		else
		{
			$prod_id=$pro_data_arr[$key]['por_id'];	
		}
				
		$pro_data_arr[$key]['qty']+=$con_qnty;
		$pro_data_arr[$key]['amu']+=$con_amount;
		$pro_data_arr[$key]['uom']=$cons_uom;
			
		
		
		$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		$data_array_trans.="$add_comma(".$id_trans.",".$trims_update_id.",".$cbo_receive_basis.",'".$txt_booking_pi_id."','".$txt_booking_pi_no."','".$booking_without_order."',".$cbo_company_id.",".$cbo_supplier_name.",".$prod_id.",4,1,".$txt_receive_date.",".$cbo_store_name.",".$cbo_uom.",".$txt_receive_qnty.",".$rate.",".$txt_amount.",'".$ile."','".$ile_cost."',".$cons_uom.",".$con_qnty.",".$cons_rate.",".$con_amount.",".$con_ile.",".$con_ile_cost.",".$con_qnty.",".$con_amount.",'".$cbo_payment_over_recv."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		//$id_trans++;
		
		if($db_type==2)
		{
			$txt_item_description=str_replace("(","[",$txt_item_description);
			$txt_item_description=str_replace(")","]",$txt_item_description);
		}
		
		$id_dtls = return_next_id_by_sequence("INV_TRIMS_ENTRY_DTLS_PK_SEQ", "inv_trims_entry_dtls", $con);
		
		$data_array_dtls.="$add_comma(".$id_dtls.",".$trims_update_id.",".$id_trans.",'".$txt_booking_pi_id."','".$txt_booking_pi_no."','".$booking_without_order."','".$prod_id."','".$cbo_item_group."','".$item_desc."','".$txt_banrd_supref."','".$cbo_uom."','".$all_po_id."','".$txt_receive_qnty."','".$txt_reject_recv_qnty."','".$rate."','".$txt_amount."','".$ile."','".$ile_cost."','".$gmts_color_id."','".$txt_item_color_id."','".$gmts_size_id."','".$txt_item_size."','".$save_data."','".$txt_item_description."','".$cons_uom."','".$con_qnty."','".$cons_rate."','".$con_ile."','".$con_ile_cost."','".$con_amount."','".$hidden_sensitivity."','".$cbo_payment_over_recv."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		
		$save_data=explode(",",str_replace("'","",$save_data));
		foreach($save_data as $val)
		{
			$order_dtls=explode("_",$val);
			
			$order_id=$order_dtls[0];
			$order_qnty=$order_dtls[1];
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			$data_array_prop.="$add_comma(".$id_prop.",".$id_trans.",1,24,".$id_dtls.",".$order_id.",".$prod_id.",".$order_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			//$id_prop = $id_prop+1;
		}
		$i++;	
	}//end while...............................
	
	
	//strat product query.................................
	$m=0;
	foreach($pro_data_arr as $key=>$rows){
		list($cbo_company_id,$item_category,$cbo_item_group,$item_desc,$txt_item_color_id,$gmts_size_id,$form_no)=explode('**',$key);	
		$con_qnty=$rows['qty'];
		$con_amount=$rows['amu'];
		$prod_id=$rows['por_id'];
		$cons_rate=number_format($con_amount/$con_qnty,$dec_place[3],'.','');
		$cons_uom=$rows['uom'];
		if($m==0) $add_comma=""; else $add_comma=",";
		
		if($pro_data_arr[$key]['current_stock'] != "")
		{
			$prod_id_up=$pro_data_arr[$key]['id'];
			$curr_stock_qnty=$pro_data_arr[$key]['current_stock']+$con_qnty;
			$curr_stock_value=$pro_data_arr[$key]['stock_value']+$con_amount;
			$avg_rate_per_unit=number_format($curr_stock_value/$curr_stock_qnty,$dec_place[3],'.','');
			$id_arr[]=$prod_id_up;
			$data_array_prod_update[$prod_id_up] =explode(",",($cbo_store_name.",".$avg_rate_per_unit.",".$con_qnty.",".$curr_stock_qnty.",".$curr_stock_value.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'"));

		}
		else
		{
			$item_name =$trim_group_arr[str_replace("'","",$cbo_item_group)]['name'];
			$prod_name_dtls=$item_name.", ".trim(str_replace("'","",$item_desc));
			
			if($db_type==2)
			{
				$prod_name_dtls=str_replace("(","[",$prod_name_dtls);
				$prod_name_dtls=str_replace(")","]",$prod_name_dtls);
			}
			
			$data_array_prod.="$add_comma(".$prod_id.",".$cbo_company_id.",".$cbo_store_name.",4,24,".$cbo_item_group.",'".$item_desc."','".$prod_name_dtls."','".$cons_uom."',".$cons_rate.",".$con_qnty.",".$con_qnty.",".$con_amount.",'".$gmts_color_id."','".$txt_item_color_id."','".$gmts_size_id."','".$txt_item_size."','".$txt_brand_supref."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			//$prod_id++;
		$m++;	
		}		
	
	}
		$rID=$rID2=$rID3=$rID4=$rID5=$rID6=true;
		
		if(str_replace("'","",$update_id)=="")
		{
			$rID=sql_insert("inv_receive_master",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;
		}
		
		//echo '5***'.count($id_arr)."=".$data_array_prod;die;
		
		if(count($id_arr)>0)
		{
		//$rID2=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$prod_id,0);
			$rID2=execute_query(bulk_update_sql_statement("product_details_master","id", $field_array_prod_update,$data_array_prod_update,$id_arr),1);
			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=0; 
			} 
		}
		if($data_array_prod)
		{
			//echo "5**insert into product_details_master (".$field_array_prod.") values ".$data_array_prod;oci_rollback($con);die;
			$rID5=sql_insert("product_details_master",$field_array_prod,$data_array_prod,0);
			if($flag==1) 
			{
				if($rID5) $flag=1; else $flag=0; 
			} 
		}
		//echo "5**".$field_array_prod_update;die;
		
		//echo "insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;
		$rID3=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} 
		
		//echo "insert into inv_trims_entry_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID4=sql_insert("inv_trims_entry_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1) 
		{
			if($rID4) $flag=1; else $flag=0; 
		} 
		
		$rID6=true;	
		if($data_array_prop!="" && str_replace("'","",$booking_without_order)!=1)
		{
			$rID6=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
			if($flag==1) 
			{
				if($rID6) $flag=1; else $flag=0; 
			} 
		}
		
		
		//echo "5**".$rID."**".$rID2."**".$rID3."**".$rID4."**".$rID5."**".$rID6; oci_rollback($con); die;
		
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$trims_update_id."**".$trims_recv_num."**0**".$delivery_system_id_string;
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
				echo "0**".$trims_update_id."**".$trims_recv_num."**0**".$delivery_system_id_string;
			}
			else
			{
				oci_rollback($con);
				echo "5**0**"."&nbsp;"."**0";
			}
		}

		disconnect($con);
		die;
		//insert end ....................................	
}*/


if ($action=="trims_receive_popup_search")
{
	echo load_html_head_contents("Trims Receive Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 

	<script>
	
		function js_set_value(id)
		{
			var ids= id.split("_");
			$('#hidden_recv_id').val(ids[0]);
			$('#hidden_posted_in_account').val(ids[1]);
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
<div align="center" style="width:885px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:883px; margin-left:3px">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" width="860" class="rpt_table" border="1" rules="all">
                <thead>
                    <th>Supplier</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="150">Enter Received ID No</th>
					<th>Store</th>
					<th>Received Date Range</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
                    	<input type="hidden" name="hidden_recv_id" id="hidden_recv_id" class="text_boxes" value=""> 
                        <input type="hidden" name="hidden_posted_in_account" id="hidden_posted_in_account" class="text_boxes" value=""> 
                    </th> 
                </thead>
                <tr class="general">
                    <td align="center">
                    	<?
							echo create_drop_down( "cbo_supplier_name", 142,"select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$cbo_company_id' and b.party_type in(4,5) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name",'id,supplier_name', 1, '-- ALL Supplier --',0);
						?>       
                    </td>
                    <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Received ID",2=>"WO/PI",3=>"Challan No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../../') ";							
							echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                    </td>     
                    <td align="center" id="search_by_td">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 
					<td align="center">
                    	<?
							echo create_drop_down( "cbo_store_id", 140,"select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$cbo_company_id' and b.category_type=4 and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, '-- ALL Store --',0);
						?>       
                    </td>	
					<td align="center">
                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">To
					  	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					</td>					
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('cbo_store_id').value, 'create_trims_recv_search_list_view', 'search_div', 'trims_receive_multi_ref_entry_controller', 'setFilterGrid(\'list_view\',-1);')" style="width:100px;" />
                     </td>
                </tr>
                <tr>
                	<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
           </table>
           <div style="width:100%; margin-top:5px; margin-left:2px" id="search_div" align="left"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_trims_recv_search_list_view")
{
	$data = explode("_",$data);
	
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];
	$supplier_id =$data[5];
	$store_id =$data[6];
	
	if($supplier_id==0) $supplier_name="%%"; else $supplier_name=$supplier_id;
	if($store_id==0) $store_name="%%"; else $store_name=$store_id;
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$store_arr = return_library_array("select id, store_name from lib_store_location","id","store_name");
	$company_arr = return_library_array("select id, company_name from lib_company","id","company_name");
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and receive_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and receive_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";	
		}
	}
	else
	{
		$date_cond="";
	}
	
	if(trim($data[0])!="")
	{
		if($search_by==1)	
			$search_field_cond="and recv_number like '$search_string'";
		else if($search_by==2)
			$search_field_cond="and booking_no like '$search_string'";
		else	
			$search_field_cond="and challan_no like '$search_string'";
	}
	else
	{
		$search_field_cond="";
	}
	
	if($db_type==0) $year_field="YEAR(insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	$sql = "select id, recv_number_prefix_num, $year_field, recv_number, receive_basis, supplier_id, store_id, source, currency_id, receive_date, challan_no, challan_date, is_posted_account, pay_mode from inv_receive_master where entry_form=24 and is_multi=1 and status_active=1 and is_deleted=0 and company_id=$company_id and supplier_id like '$supplier_name' and store_id like '$store_name' $search_field_cond $date_cond order by id"; 
	// echo $sql;die;
	$sql_result=sql_select($sql);
	$sup_com_arr=array();
	foreach($sql_result as $row)
	{
		if($row[csf("pay_mode")]==3 || $row[csf("pay_mode")]==5) $sup_com_arr[$row[csf("id")]]=$company_arr[$row[csf("supplier_id")]]; else $sup_com_arr[$row[csf("id")]]=$supplier_arr[$row[csf("supplier_id")]];
	}
	
	$arr=array(2=>$receive_basis_arr,3=>$sup_com_arr,4=>$store_arr,8=>$currency,9=>$source);
	
	echo create_list_view("list_view", "Received No,Year,Receive Basis,Supplier,Store,Receive date,Challan No,Challan Date,Currency,Source", "75,50,105,130,80,75,75,80,60","870","240",0, $sql, "js_set_value", "id,is_posted_account", "", 1, "0,0,receive_basis,id,store_id,0,0,0,currency_id,source", $arr, "recv_number_prefix_num,year,receive_basis,id,store_id,receive_date,challan_no,challan_date,currency_id,source", "",'','0,0,0,0,0,3,0,3,0,0');
	
	exit();
}

if($action=='populate_data_from_trims_recv')
{
	$data_array=sql_select("select id, recv_number, company_id, receive_basis, pay_mode, supplier_id, is_audited, store_id, source, currency_id, challan_no, receive_date, challan_date, lc_no, exchange_rate, gate_entry_no, addi_challan_date, gate_entry_date, vehicle_no, rcvd_book_no, transporter_name, bill_date, short_qnty, excess_qnty from inv_receive_master where id='$data'");//, booking_id, booking_no, booking_without_order

	$company_id=$data_array[0][csf("company_id")];
	$variable_inventory_sql=sql_select("select store_method, rack_balance from variable_settings_inventory  where company_name=$company_id and item_category_id=4 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	$store_method=$variable_inventory_sql[0][csf("store_method")];
	foreach ($data_array as $row)
	{
		//
		echo "load_drop_down( 'requires/trims_receive_multi_ref_entry_controller','".$row[csf('company_id')]."_".$row[csf('pay_mode')]."', 'load_drop_down_supplier', 'supplier_td_id' );\n";
		echo "document.getElementById('txt_recieved_id').value 				= '".$row[csf("recv_number")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_receive_basis').value 			= '".$row[csf("receive_basis")]."';\n";
		echo "document.getElementById('store_update_upto').value 			= '" . $store_method . "';\n";
		echo "document.getElementById('cbo_supplier_name').value 			= '".$row[csf("supplier_id")]."';\n";

		$addi_info_str = $row[csf("gate_entry_no")]."_".change_date_format($row[csf("addi_challan_date")])."_".change_date_format($row[csf("gate_entry_date")])."_".$row[csf("vehicle_no")]."_".$row[csf("rcvd_book_no")]."_".$row[csf("transporter_name")]."_".change_date_format($row[csf("bill_date")])."_".$row[csf("short_qnty")]."_".$row[csf("excess_qnty")];
		echo "$('#txt_addi_info').val('".$addi_info_str."');\n";

		if($row[csf("receive_basis")]==12)
		{
			echo "$('#cbo_receive_basis').attr('disabled','true')".";\n";
			echo "$('#txt_receive_chal_no').attr('disabled','true')".";\n";
		}
		else
		{
			echo "$('#cbo_receive_basis').attr('disabled','false')".";\n";
			echo "$('#txt_receive_chal_no').attr('disabled','false')".";\n";
		}
		echo "$('#cbo_company_id').attr('disabled','true')".";\n";
		//echo "set_receive_basis();\n";
		
		echo "document.getElementById('txt_receive_date').value 			= '".change_date_format($row[csf("receive_date")])."';\n";
		
		$lc_no=return_field_value("lc_number","com_btb_lc_master_details","id='".$row[csf("lc_no")]."'");
		
		echo "document.getElementById('txt_receive_chal_no').value 			= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_challan_date').value 			= '".change_date_format($row[csf("challan_date")])."';\n";
		echo "document.getElementById('txt_lc_no').value 					= '".$lc_no."';\n";
		echo "document.getElementById('lc_id').value 						= '".$row[csf("lc_no")]."';\n";
		echo "document.getElementById('cbo_source').value 					= '".$row[csf("source")]."';\n";

		echo "load_room_rack_self_bin('requires/trims_receive_multi_ref_entry_controller*4', 'store','store_td', '".$row[csf('company_id')]."','"."',this.value);\n";
		echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("store_id")]."';\n";
		
		echo "document.getElementById('cbo_currency_id').value 				= '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value 			= '".$row[csf("exchange_rate")]."';\n";
		echo "document.getElementById('update_id').value 					= '".$row[csf("id")]."';\n";
		
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_trims_receive',1,1);\n";
        echo "$('#audited').text('');\n";
        if($row[csf("is_audited")]==1) echo "$('#audited').text('Audited');\n";
		exit();
	}
}

if($action=="show_trims_listview")
{

	$data=explode('_', $data);

	$item_arr = return_library_array("select id, item_name from lib_item_group","id","item_name");

	$sql="select id, booking_no, item_group_id, item_description, item_description_color_size, brand_supplier, receive_qnty, rate, amount, reject_receive_qnty, gmts_color_id, gmts_size_id, item_color, item_size, order_uom, payment_over_recv from inv_trims_entry_dtls where mst_id='$data[0]' and status_active = '1' and is_deleted = '0'";
	$result=sql_select($sql);
?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table">
        <thead>
        	<th width="90">WO/PI No.</th>	
            <th width="80">Item Group</th>
            <th width="100">Item Description</th>
            <th width="60">Brand/Sup Ref</th>
            <th width="60">Recv. Qty.</th>
            <th width="50">Rate</th>
            <th width="70">Amount</th>
            <th width="60">Reject Qty</th>
            <th width="40">UOM</th>
            <th width="60">Item Color</th>
            <th>Item Size</th>
        </thead>
	</table>
	<div style="width:750px; max-height:200px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="730" class="rpt_table" id="list_view">  
        <?
            $i=1;
            foreach($result as $row)
            {  
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
        	?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data('<? echo $row[csf('id')]."_".$data[1]."_".$data[2]; ?>','populate_trims_details_form_data', 'requires/trims_receive_multi_ref_entry_controller');"> 
                    <td width="90"><p><? echo $row[csf('booking_no')]; ?></p></td>
                    <td width="80"><p><? echo $item_arr[$row[csf('item_group_id')]]; ?></p></td>
                    <td width="100"><p><? echo $row[csf('item_description_color_size')]; ?></p></td>
                    <td width="60"><p><? echo $row[csf('brand_supplier')]; ?>&nbsp;</p></td>
                    <td width="60" align="right"><? echo number_format($row[csf('receive_qnty')],2); ?></td>
                    <td width="50" align="right"><? echo number_format($row[csf('rate')],4); ?></td>
                    <td width="70" align="right"><? echo number_format($row[csf('amount')],2); ?></td>
                    <td width="60" align="right"><? echo number_format($row[csf('reject_receive_qnty')],2); ?>&nbsp;</p></td>
                    <td width="40" align="center"><p><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?>&nbsp;</p></td>
                    <td width="60"><p><? echo $color_arr[$row[csf('item_color')]]; ?>&nbsp;</p></td>
                    <td><p><? echo $row[csf('item_size')]; ?>&nbsp;</p></td>
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

if($action=='populate_trims_details_form_data')
{

	$data=explode('_', $data);
	//$booking_without_order=return_field_value("distinct(a.booking_without_order) as booking_without_order","inv_receive_master a, inv_trims_entry_dtls b","a.id=b.mst_id and b.id=$data and a.entry_form=24","booking_without_order");
	
	$data_array=sql_select("select id, mst_id, trans_id, booking_id, booking_no, booking_without_order, prod_id, item_group_id, item_description, item_description_color_size, brand_supplier, receive_qnty, rate, amount, reject_receive_qnty, gmts_color_id, gmts_size_id, item_size, order_uom, order_id,save_string, save_string_2, save_string_3, item_color, ile, ile_cost, book_keeping_curr, sensitivity, payment_over_recv,floor,room_no,rack_no,self_no,box_bin_no, material_source, remarks 
	from inv_trims_entry_dtls where id='$data[0]'");
	foreach ($data_array as $row)
	{ 
		$order_no='';
		if($row[csf("booking_without_order")]!=1)
		{
			$sql_trims=sql_select("select receive_basis, within_group from inv_receive_master where id =".$row[csf("mst_id")]."");
			$rcv_basis=$sql_trims[0][csf("receive_basis")];
			$within_group=$sql_trims[0][csf("within_group")];
			if($rcv_basis==12 )
			{
				if($within_group==1)
				{
					if($db_type==0)
					{
						$order_no=return_field_value("group_concat(po_number)","wo_po_break_down","id in(".$row[csf("order_id")].")");
					}
					else
					{
						$order_no=return_field_value("LISTAGG(cast(po_number as varchar2(4000)), ',') WITHIN GROUP (ORDER BY id) as po_id","wo_po_break_down","id in(".$row[csf("order_id")].")","po_id");	
					}
				}
				else
				{
					if($db_type==0)
					{
						$order_no=return_field_value("group_concat(order_no)","subcon_ord_dtls","id in(".$row[csf("order_id")].")");
					}
					else
					{
						$order_no=return_field_value("LISTAGG(cast(order_no as varchar2(4000)), ',') WITHIN GROUP (ORDER BY id) as po_id","subcon_ord_dtls","id in(".$row[csf("order_id")].")","po_id");	
					}
				}
			}
			else
			{
				if($db_type==0)
				{
					$order_no=return_field_value("group_concat(po_number)","wo_po_break_down","id in(".$row[csf("order_id")].")");
				}
				else
				{
					$order_no=return_field_value("LISTAGG(cast(po_number as varchar2(4000)), ',') WITHIN GROUP (ORDER BY id) as po_id","wo_po_break_down","id in(".$row[csf("order_id")].")","po_id");	
				}
			}
			
		}
		$save_string_data=$row[csf("save_string")]."".$row[csf("save_string_2")]."".$row[csf("save_string_3")];	
		echo "document.getElementById('txt_booking_pi_no').value 			= '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('txt_booking_pi_id').value 			= '".$row[csf("booking_id")]."';\n";
		echo "document.getElementById('booking_without_order').value 		= '".$row[csf("booking_without_order")]."';\n";
		
		if($row[csf("booking_without_order")]==1)
		{
			echo "$('#txt_buyer_order').attr('disabled','disabled');\n";
			echo "$('#txt_receive_qnty').removeAttr('disabled','disabled');\n";	
		}
		else
		{
			echo "$('#txt_buyer_order').removeAttr('disabled','disabled');\n";
			echo "$('#txt_receive_qnty').attr('disabled','disabled');\n";	
		}

			
		echo "document.getElementById('cbo_item_group').value 				= '".$row[csf("item_group_id")]."';\n";
		echo "document.getElementById('cbo_uom').value 						= '".$row[csf("order_uom")]."';\n";
		echo "document.getElementById('txt_amount').value 					= '".$row[csf("amount")]."';\n";
		echo "$('#txt_amount').attr('title','".$row[csf("amount")]."');\n";
		echo "document.getElementById('txt_item_description').value 		= '".$row[csf("item_description_color_size")]."';\n";
		echo "document.getElementById('hidden_item_description').value 		= '".$row[csf("item_description")]."';\n";
		echo "document.getElementById('txt_receive_qnty').value 			= '".$row[csf("receive_qnty")]."';\n";
		echo "document.getElementById('txt_reject_recv_qnty').value 		= '".$row[csf("reject_receive_qnty")]."';\n";
		echo "document.getElementById('txt_brand_supref').value 			= '".$row[csf("brand_supplier")]."';\n";
		
		echo "get_php_form_data(document.getElementById('cbo_receive_basis').value+'_'+document.getElementById('txt_booking_pi_id').value+'_'+document.getElementById('txt_booking_pi_no').value+'_'+".$row[csf('item_group_id')]."+'_'+'".$row[csf('item_description')]."'+'_'+'".$row[csf('brand_supplier')]."'+'_'+'".$row[csf('sensitivity')]."'+'_'+'".$row[csf('gmts_color_id')]."'+'_'+'".$row[csf('gmts_size_id')]."'+'_'+'".$row[csf('item_color')]."'+'_'+'".$row[csf('item_size')]."'+'_'+'".$row[csf("booking_without_order")]."', 'put_balance_qnty', 'requires/trims_receive_multi_ref_entry_controller')".";\n";
		
		echo "document.getElementById('txt_rate').value 					= '".$row[csf("rate")]."';\n";
		echo "document.getElementById('txt_remarks').value 					= '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('txt_hidden_rate').value 				= '".$row[csf("rate")]."';\n";
		echo "document.getElementById('txt_buyer_order').value 				= '".$order_no."';\n";

		echo "load_room_rack_self_bin('requires/trims_receive_multi_ref_entry_controller', 'floor','floor_td', '".$data[1]."','"."','".$data[2]."',this.value);\n";
		echo "document.getElementById('cbo_floor').value 					= '".$row[csf("floor")]."';\n";
		if($row[csf('floor')])
		{
			echo "load_room_rack_self_bin('requires/trims_receive_multi_ref_entry_controller', 'room','room_td', '".$data[1]."','"."','".$data[2]."','".$row[csf('floor')]."',this.value);\n";
		}
		echo "document.getElementById('cbo_room').value 					= '".$row[csf("room_no")]."';\n";
		if($row[csf('room_no')])
		{
			echo "load_room_rack_self_bin('requires/trims_receive_multi_ref_entry_controller', 'rack','rack_td', '".$data[1]."','"."','".$data[2]."','".$row[csf('floor')]."','".$row[csf('room_no')]."',this.value);\n";
		}
		echo "document.getElementById('txt_rack').value 					= '".$row[csf("rack_no")]."';\n";		
		if($row[csf('rack_no')])
		{
			echo "load_room_rack_self_bin('requires/trims_receive_multi_ref_entry_controller', 'shelf','shelf_td', '".$data[1]."','"."','".$data[2]."','".$row[csf('floor')]."','".$row[csf('room_no')]."','".$row[csf('rack_no')]."',this.value);\n";
		}
		echo "document.getElementById('txt_shelf').value 					= '".$row[csf("self_no")]."';\n";
		if($row[csf("self_no")])
		{
			echo "load_room_rack_self_bin('requires/trims_receive_multi_ref_entry_controller', 'bin','bin_td', '".$data[1]."','"."','".$data[2]."','".$row[csf('floor')]."','".$row[csf('room_no')]."','".$row[csf('rack_no')]."','".$row[csf('self_no')]."',this.value);\n";
		}
		echo "document.getElementById('cbo_bin').value 					= '".$row[csf("box_bin_no")]."';\n";



		echo "document.getElementById('ile_td').innerHTML 					= 'ILE% ".$row[csf("ile")]."';\n";
		echo "document.getElementById('txt_ile').value 						= '".$row[csf("ile_cost")]."';\n";
		echo "document.getElementById('txt_gmts_color').value 				= '".$color_arr[$row[csf("gmts_color_id")]]."';\n";
		echo "document.getElementById('txt_gmts_color_id').value 			= '".$row[csf("gmts_color_id")]."';\n";
		echo "document.getElementById('txt_item_color').value 				= '".$color_arr[$row[csf("item_color")]]."';\n";

		echo "document.getElementById('txt_item_color_id').value 			= '".$row[csf("item_color")]."';\n";
		echo "document.getElementById('txt_gmts_size').value 				= '".$size_arr[$row[csf("gmts_size_id")]]."';\n";
		echo "document.getElementById('txt_gmts_size_id').value 			= '".$row[csf("gmts_size_id")]."';\n";
		echo "document.getElementById('txt_item_size').value 				= '".$row[csf("item_size")]."';\n";
		echo "document.getElementById('txt_book_currency').value 			= '".$row[csf("book_keeping_curr")]."';\n";
		echo "document.getElementById('all_po_id').value 					= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('previous_prod_id').value 			= '".$row[csf("prod_id")]."';\n";
		echo "document.getElementById('update_dtls_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('update_trans_id').value 				= '".$row[csf("trans_id")]."';\n";
		echo "document.getElementById('hidden_sensitivity').value 			= '".$row[csf("sensitivity")]."';\n";
		echo "document.getElementById('cbo_payment_over_recv').value 		= '".$row[csf("payment_over_recv")]."';\n";
		echo "document.getElementById('save_data').value 					= '".$save_string_data."';\n";
		echo "document.getElementById('meterial_source').value 				= '".$row[csf("material_source")]."';\n";
		
		echo "load_description('".$row[csf('booking_id')]."','".$row[csf('booking_no')]."','".$row[csf("booking_without_order")]."');\n";
		
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_trims_receive',1,1);\n";  
		exit();
	}
}

if ($action=="goods_placement_popup")
{
	echo load_html_head_contents("Goods Placement Entry Form", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$dtls_data=sql_select("select item_group_id, item_description, receive_qnty from inv_trims_entry_dtls where id=$update_dtls_id");
?> 

	<script>
		
		var permission='<? echo $permission; ?>';
		
		function fn_addRow( i )
		{ 
			var row_num=$('#txt_tot_row').val();
			row_num++;
			
			var clone= $("#tr_"+i).clone();
			clone.attr({
				id: "tr_" + row_num,
			});
			
			clone.find("input,select").each(function(){
				  
			$(this).attr({ 
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
			  'name': function(_, name) { return name },
			  'value': function(_, value) { return value }              
			});
			 
			}).end();
			
			$("#tr_"+i).after(clone);
			
			$('#txtSelfNo_'+row_num).val('');
			$('#txtBoxBinNo_'+row_num).val('');
			$('#txtCtnNo_'+row_num).val('');
			$('#txtCtnQnty_'+row_num).val('');
			
			$('#increase_'+row_num).removeAttr("value").attr("value","+");
			$('#decrease_'+row_num).removeAttr("value").attr("value","-");
			$('#increase_'+row_num).removeAttr("onclick").attr("onclick","fn_addRow("+row_num+");");
			$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fn_deleteRow("+row_num+");");
			
			$('#txt_tot_row').val(row_num);
		}
		
		function fn_deleteRow(rowNo) 
		{ 		
			var row_num=$('#tbl_list tbody tr').length;
			if(row_num!=1)
			{
				$("#tr_"+rowNo).remove();
			}
		}
		
		function fnc_goods_placement_entry(operation)
		{
			var dataString=""; var j=0;
			$("#tbl_list").find('tbody tr').each(function()
			{
				var txtRoomNo=$(this).find('input[name="txtRoomNo[]"]').val();
				var txtRackNo=$(this).find('input[name="txtRackNo[]"]').val();
				var txtSelfNo=$(this).find('input[name="txtSelfNo[]"]').val();
				var txtBoxBinNo=$(this).find('input[name="txtBoxBinNo[]"]').val();
				var txtCtnNo=$(this).find('input[name="txtCtnNo[]"]').val();
				var txtCtnQnty=$(this).find('input[name="txtCtnQnty[]"]').val();
				
				if(txtRackNo!="")
				{
					j++;
					
					dataString+='&txtRoomNo_' + j + '=' + txtRoomNo + '&txtRackNo_' + j + '=' + txtRackNo + '&txtSelfNo_' + j + '=' + txtSelfNo + '&txtBoxBinNo_' + j + '=' + txtBoxBinNo + '&txtCtnNo_' + j + '=' + txtCtnNo + '&txtCtnQnty_' + j + '=' + txtCtnQnty;
				}
			});
			
			if(j==0)
			{
				alert("Please Insert At Least One Rack No.");
				return;	
			}
			
			var data="action=save_update_delete_goods_placement&operation="+operation+'&tot_row='+j+get_submitted_data_string('dtls_id',"../../../")+dataString;
			//alert(data);return;
			freeze_window(operation);
			
			http.open("POST","trims_receive_multi_ref_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange=fnc_goods_placement_entry_Reply_info;
		}
		
		function fnc_goods_placement_entry_Reply_info()
		{
			if(http.readyState == 4) 
			{
				//alert(http.responseText);release_freezing();return;
				var reponse=trim(http.responseText).split('**');	
					
				show_msg(reponse[0]);
				
				if((reponse[0]==0 || reponse[0]==1 || reponse[0]==2))
				{
					reset_form('goodsPlacement_1','','','','','dtls_id');
					load_dtls_part();
				}
				
				set_button_status(reponse[2], permission, 'fnc_goods_placement_entry',1);	
				release_freezing();	
			}
		}
		
		function load_dtls_part()
		{
			var list_view_goods_placement = return_global_ajax_value( <? echo $update_dtls_id; ?>, 'load_php_dtls_form', '', 'trims_receive_multi_ref_entry_controller');

			if(list_view_goods_placement!='')
			{
				$("#tbl_list tbody tr").remove();
				$("#tbl_list tbody").append(list_view_goods_placement);
				
				var row_num=$("#tbl_list tbody tr").length;
				$('#txt_tot_row').val(row_num);
			}
		}
		
		function fnc_carton_sticker()
		{
			data=<? echo $update_dtls_id; ?>;
			var url=return_ajax_request_value(data, "print_report_carton_sticker", "trims_receive_multi_ref_entry_controller");
			//alert(url);
			window.open(url,"##");
		}
	
    </script>

</head>

<body>
<div align="center">
	<? echo load_freeze_divs ("../../../",$permission,1); ?>
	<form name="goodsPlacement_1" id="goodsPlacement_1">
		<fieldset style="width:580px;">
        	<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="500" align="center">
                <thead>
                    <th width="160">Item Group</th>
                    <th width="200">Item Description</th>
                    <th>Qnty</th>
                </thead>
                <tr bgcolor="#FFFFFF">
                    <td><p>&nbsp;<? echo $trim_group_arr[$dtls_data[0][csf('item_group_id')]]['name']; ?></p></td>
                    <td><p>&nbsp;<? echo $dtls_data[0][csf('item_description')]; ?></p></td>
                    <td align="right"><? echo number_format($dtls_data[0][csf('receive_qnty')],2); ?>&nbsp;</td>
                    <input type="hidden" name="dtls_id" id="dtls_id" class="text_boxes" value="<? echo $update_dtls_id; ?>">
                    <input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="">
                </tr>
            </table>
        </fieldset> 
        <fieldset style="width:770px; margin-top:10px">
            <legend>New Entry</legend>
            <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="760" id="tbl_list">
            	<thead>
                    <th width="110">Room No</th>
                    <th width="110">Rack No</th>
                    <th width="110">Shelf No</th>
                    <th width="110">Box/Bin</th>
                    <th width="110">Ctn. No</th>
                    <th width="110">Ctn. Qnty</th>
                    <th></th>
                </thead>
                <tbody>
                    <!--<tr id="tr_1">
                        <td>
                            <input type="text" name="txtRoomNo[]" id="txtRoomNo_1" class="text_boxes" style="width:100px;"/>
                        </td>
                        <td>
                            <input type="text" name="txtRackNo[]" id="txtRackNo_1" class="text_boxes" style="width:100px;"/>
                        </td>
                        <td>
                            <input type="text" name="txtSelfNo[]" id="txtSelfNo_1" class="text_boxes" style="width:100px;"/>
                        </td>
                        <td>
                            <input type="text" name="txtBoxBinNo[]" id="txtBoxBinNo_1" class="text_boxes" style="width:100px;"/>
                        </td>
                        <td>
                            <input type="text" name="txtCtnNo[]" id="txtCtnNo_1" class="text_boxes" style="width:100px;"/>
                        </td>
                        <td>
                            <input type="text" name="txtCtnQnty[]" id="txtCtnQnty_1" class="text_boxes_numeric" style="width:100px;"/>
                        </td>
                        <td>
                            <input type="button" id="increase_1" name="increase[]" style="width:35px" class="formbuttonplasminus" value="+" onClick="fn_addRow(1)" />
                            <input type="button" id="decrease_1" name="decrease[]" style="width:35px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);"/>
						</td>
                    </tr>-->
                </tbody>    
            </table>
			<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="760">
             	<tr>
                    <td colspan="7">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="7" align="center" class="button_container">
						<? 
							echo load_submit_buttons($permission, "fnc_goods_placement_entry", 0,0,"reset_form('goodsPlacement_1','','','','','txt_tot_row*dtls_id');$('#tbl_list tbody tr:not(:first)').remove();",1);
                        ?>
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="parent.emailwindow.hide();" style="width:100px" />
                        <input type="button" name="sticker" class="formbutton" value="Carton Sticker" id="sticker" onClick="fnc_carton_sticker();" style="width:120px" /> 
                    </td>	  
                </tr>
			</table>
		</fieldset>
	</form>
</div>
</body>  
<script>

 	get_php_form_data(<? echo $update_dtls_id; ?>, "populate_data_goods_placement", "trims_receive_multi_ref_entry_controller" );
	load_dtls_part();
	        
</script>		
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="save_update_delete_goods_placement")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if($operation==0)  // Insert Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array="id, entry_form, mst_id, trans_id, dtls_id, prod_id, room_no, rack_no, self_no, box_bin_no, ctn_no, ctn_qnty, inserted_by, insert_date";
		$dtls_data=sql_select("select mst_id, trans_id, prod_id from inv_trims_entry_dtls where id=$dtls_id");
		//$id=return_next_id( "id", "inv_goods_placement", 1 ) ;
		$data_array='';
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$id = return_next_id_by_sequence("INV_GOODS_PLACEMENT_PK_SEQ", "inv_goods_placement", $con);
			$txtRoomNo="txtRoomNo_".$j;
			$txtRackNo="txtRackNo_".$j;
			$txtSelfNo="txtSelfNo_".$j;
			$txtBoxBinNo="txtBoxBinNo_".$j;
			$txtCtnNo="txtCtnNo_".$j;
			$txtCtnQnty="txtCtnQnty_".$j;
			
			if($data_array!="") $data_array.=",";
			$data_array.="(".$id.",24,".$dtls_data[0][csf('mst_id')].",".$dtls_data[0][csf('trans_id')].",".$dtls_id.",".$dtls_data[0][csf('prod_id')].",'".$$txtRoomNo."','".$$txtRackNo."','".$$txtSelfNo."','".$$txtBoxBinNo."','".$$txtCtnNo."','".$$txtCtnQnty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			//$id = $id+1;
		}
		
		if($data_array!="")
		{
			//echo $data_array;die;
			$rID=sql_insert("inv_goods_placement",$field_array,$data_array,1);
			if($rID) $flag=1; else $flag=0; 
		}
			
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  

				echo "0**".str_replace("'", '', $dtls_id)."**1";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);  
				echo "0**".str_replace("'", '', $dtls_id)."**1";
			}
			else
			{
				oci_rollback($con);
				echo "5**0**0";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$dtls_data=sql_select("select mst_id, trans_id, prod_id from inv_trims_entry_dtls where id=$dtls_id");
		$field_array="id, entry_form, mst_id, trans_id, dtls_id, prod_id, room_no, rack_no, self_no, box_bin_no, ctn_no, ctn_qnty, inserted_by, insert_date";
		//$id=return_next_id( "id", "inv_goods_placement", 1 ) ;

		$data_array='';
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$id = return_next_id_by_sequence("INV_GOODS_PLACEMENT_PK_SEQ", "inv_goods_placement", $con);
			$txtRoomNo="txtRoomNo_".$j;
			$txtRackNo="txtRackNo_".$j;
			$txtSelfNo="txtSelfNo_".$j;
			$txtBoxBinNo="txtBoxBinNo_".$j;
			$txtCtnNo="txtCtnNo_".$j;
			$txtCtnQnty="txtCtnQnty_".$j;
			
			if($data_array!="") $data_array.=",";
			$data_array.="(".$id.",24,".$dtls_data[0][csf('mst_id')].",".$dtls_data[0][csf('trans_id')].",".$dtls_id.",".$dtls_data[0][csf('prod_id')].",'".$$txtRoomNo."','".$$txtRackNo."','".$$txtSelfNo."','".$$txtBoxBinNo."','".$$txtCtnNo."','".$$txtCtnQnty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			//$id = $id+1;
		}
		
		$delete=execute_query("delete from inv_goods_placement where dtls_id=$dtls_id and entry_form=24",0);
		if($delete) $flag=1; else $flag=0;
		
		if($data_array!="")
		{
			//echo $data_array;die;
			$rID=sql_insert("inv_goods_placement",$field_array,$data_array,1);
			if($flag==1)
			{
				if($rID) $flag=1; else $flag=0;
			} 
		}
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $update_id)."**1";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**".str_replace("'", '', $update_id)."**1";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);  
				echo "1**".str_replace("'", '', $update_id)."**1";
			}
			else
			{
				oci_rollback($con);
				echo "6**".str_replace("'", '', $update_id)."**1";
			}
		}
		disconnect($con);
		die;
	}
	
	else if ($operation==2)   // Update Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$delete=execute_query("delete from inv_goods_placement where dtls_id=$dtls_id and entry_form=24",0);
		if($db_type==0)
		{
			if($delete)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $update_id)."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**".str_replace("'", '', $update_id)."**1";
			}
		}
		else if($db_type==2 || $db_type==1)
		{
			if($delete)
			{
				oci_commit($con); 
				echo "1**".str_replace("'", '', $update_id)."**0";
			}
			else
			{
				oci_rollback($con);
				echo "6**".str_replace("'", '', $update_id)."**1";
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="load_php_dtls_form")
{
	$sql="select room_no, rack_no, self_no, box_bin_no, ctn_no, ctn_qnty from inv_goods_placement where dtls_id=$data and entry_form=24 and status_active=1 and is_deleted=0";
	$result=sql_select($sql);
	$count=count($result);
	
	if($count==0 ) // New Insert
	{
	?>
        <tr id="tr_1">
            <td>
                <input type="text" name="txtRoomNo[]" id="txtRoomNo_1" class="text_boxes" style="width:100px;"/>
            </td>
            <td>
                <input type="text" name="txtRackNo[]" id="txtRackNo_1" class="text_boxes" style="width:100px;"/>
            </td>
            <td>
                <input type="text" name="txtSelfNo[]" id="txtSelfNo_1" class="text_boxes" style="width:100px;"/>
            </td>
            <td>
                <input type="text" name="txtBoxBinNo[]" id="txtBoxBinNo_1" class="text_boxes" style="width:100px;"/>
            </td>
            <td>
                <input type="text" name="txtCtnNo[]" id="txtCtnNo_1" class="text_boxes" style="width:100px;"/>
            </td>
            <td>
                <input type="text" name="txtCtnQnty[]" id="txtCtnQnty_1" class="text_boxes_numeric" style="width:100px;"/>
            </td>
            <td>
                <input type="button" id="increase_1" name="increase[]" style="width:35px" class="formbuttonplasminus" value="+" onClick="fn_addRow(1)" />
                <input type="button" id="decrease_1" name="decrease[]" style="width:35px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);"/>
            </td>
        </tr>
    <?
	}
	else // From Update
	{
		$i=0;
		foreach($result as $row)
		{
			$i++;
		?>
			<tr id="tr_<? echo $i; ?>">
                <td>
                    <input type="text" name="txtRoomNo[]" id="txtRoomNo_<? echo $i; ?>" class="text_boxes" style="width:100px;" value="<? echo $row[csf('room_no')]; ?>"/>
                </td>
                <td>
                    <input type="text" name="txtRackNo[]" id="txtRackNo_<? echo $i; ?>" class="text_boxes" style="width:100px;" value="<? echo $row[csf('rack_no')]; ?>"/>
                </td>
                <td>
                    <input type="text" name="txtSelfNo[]" id="txtSelfNo_<? echo $i; ?>" class="text_boxes" style="width:100px;" value="<? echo $row[csf('self_no')]; ?>"/>
                </td>
                <td>
                    <input type="text" name="txtBoxBinNo[]" id="txtBoxBinNo_<? echo $i; ?>" class="text_boxes" style="width:100px;" value="<? echo $row[csf('box_bin_no')]; ?>"/>
                </td>
                <td>
                    <input type="text" name="txtCtnNo[]" id="txtCtnNo_<? echo $i; ?>" class="text_boxes" style="width:100px;" value="<? echo $row[csf('ctn_no')]; ?>"/>
                </td>
                <td>
                    <input type="text" name="txtCtnQnty[]" id="txtCtnQnty_<? echo $i;?>" class="text_boxes_numeric" style="width:100px;" value="<? echo $row[csf('ctn_qnty')]; ?>"/>
                </td>
                <td>
                    <input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:35px" class="formbuttonplasminus" value="+" onClick="fn_addRow(<? echo $i; ?>)" />
                    <input type="button" id="decrease_<? echo $i;?>" name="decrease[]" style="width:35px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);"/>
                </td>
            </tr>
		<?
		}		
	}
	
	exit();
}

if ($action=="populate_data_goods_placement")
{
	$result=sql_select("select id from inv_goods_placement where dtls_id=$data and entry_form=24 and status_active=1 and is_deleted=0");
	
	if(count($result)>0) $button_status=1; else $button_status=0;
	
	echo "set_button_status($button_status, '".$_SESSION['page_permission']."', 'fnc_goods_placement_entry',1,1);\n";  
	exit();
}

if($action=="print_report_carton_sticker")
{
	/*define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	require('../../../ext_resource/pdf/fpdf.php');
	require('../../../ext_resource/pdf/html_table.php');
	
	foreach (glob(""."*.pdf") as $filename) {			
			@unlink($filename);
		}
		
	$pdf=new PDF();
	$pdf->AddPage();
	$pdf->SetFont('Arial','',12);
	
	$html='<table width="100%" border="1"><tr>
		<td width="200"><table><tr><td>Fuad</td></tr></table></td>
		<td width="200"><table><tr><td>Shahriar</td></tr></table></td>
		</tr></table>';
	$html='<table border="1"><tr>';
	$sql="select a.challan_no, a.receive_date, b.prod_id, b.order_id, c.room_no, c.rack_no, c.self_no, c.box_bin_no, c.ctn_no, c.ctn_qnty from inv_receive_master a, inv_trims_entry_dtls b, inv_goods_placement c where a.id=b.mst_id and a.entry_form=24 and a.item_category=4 and b.id=c.dtls_id and c.entry_form=24 and c.dtls_id=$data";
	$result=sql_select($sql);
	foreach($result as $row)
	{
		$html.='<td><table border="1" rules="all">
		<tr>
		<td width="170"><b>BUYER</b></td><td width="130">'.$row[csf('challan_no')].'</td>
		</tr>
		<tr>
		<td width="170"><b>ORDER-NO</b></td><td width="130"></td>
		</tr>
		<tr>
		<td width="170"><b>NAME OF ITEM</b></td><td width="130"></td>
		</tr>
		<tr>
		<td width="170"><b>CHALLAN-NO</b></td><td width="130"></td>
		</tr>
		<tr>
		<td width="170"><b>RCVD-DATE</b></td><td width="130"></td>
		</tr>
		<tr>
		<td width="170"><b>CTN-NO</b></td><td width="130"></td>
		</tr>
		<tr>
		<td width="170"><b>Room No</b></td><td width="130"></td>
		</tr>
		<tr>
		<td width="170"><b>Rack No</b></td><td width="130"></td>
		</tr>
		<tr>
		<td width="170"><b>Self No</b></td><td width="130"></td>
		</tr>
		<tr>
		<td width="170"><b>Box/Bin</b></td><td width="130"></td>
		</tr>
		<tr>
		<td width="170"><b>Ctn. Qty.</b></td><td width="130"></td>
		</tr>
		<tr>
		<td width="170"><b>FIRST ISSUE DATE</b></td><td width="130"></td>
		</tr>
		</table></td>';
	}
	
	$html.='</tr></table>';
	
	$pdf->WriteHTML($html);	
	
	$name = 'carton_sticker_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output( "".$name, 'F');
	echo $name;*/
	
	require_once('../../../ext_resource/pdf/tcpdf_5_9_082/tcc/config/lang/eng.php');
	require_once('../../../ext_resource/pdf/tcpdf_5_9_082/tcc/tcpdf.php');
	header ('Content-type: text/html; charset=utf-8'); 
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'RA4', true, 'UTF-8', false);	// create new PDF document
	 
	// set document information
	$pdf->SetCreator('Md. Fuad Shahriar');
	$pdf->SetAuthor('Md. Fuad Shahriar');
	$pdf->SetTitle('Logic ERP');
	$pdf->SetSubject('Goods Placement Carton Sticker');
	//$pdf->SetKeywords('Logic, HRM, Payroll, HRM & Payroll, ID Card');
	
	// remove default header/footer
	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);
	
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);	//set default monospaced font
	$pdf->SetMargins(12, 15, 8);								//set margins
	$pdf->SetAutoPageBreak(TRUE, 5);						//set auto page breaks
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);				//set image scale factor
	$pdf->setLanguageArray($l);								//set some language-dependent strings
	$pdf->SetFont('times', '', 12);
	
	foreach (glob(""."*.pdf") as $filename) {			
			@unlink($filename);
		}
		
	$pdf->AddPage();
	/*$html='<table width="100%" border="1"><tr>
		<td width="200"><table><tr><td>Fuad</td></tr></table></td>
		<td width="200"><table><tr><td>Shahriar</td></tr></table></td>
		</tr></table>';*/
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer","id","buyer_name");
	$i=1; $br=0; $order_no='';	
	$html='<table border="0"><tr>';
	$sql="select a.challan_no, a.receive_date, b.prod_id, b.order_id, c.room_no, c.rack_no, c.self_no, c.box_bin_no, c.ctn_no, c.ctn_qnty from inv_receive_master a, inv_trims_entry_dtls b, inv_goods_placement c where a.id=b.mst_id and a.entry_form=24 and a.item_category=4 and b.id=c.dtls_id and c.entry_form=24 and c.dtls_id=$data";
	$result=sql_select($sql);
	foreach($result as $row)
	{
		if($i==1)
		{
			if($row[csf('order_id')]!="")
			{
				$order_data=sql_select("select a.buyer_name, group_concat(b.po_number) as po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in(".$row[csf('order_id')].")");
				$buyer=$buyer_arr[$order_data[0][csf('buyer_name')]];
				$order_no=$order_data[0][csf('po_number')];
			}
			
			$item_desc=return_field_value("product_name_details","product_details_master","id=".$row[csf('prod_id')]);
		}
		
		$html.='<td><table border="1" rules="all">
		<tr>
		<td width="150"><b>&nbsp;BUYER</b></td><td width="170">&nbsp;'.$buyer.'</td>
		</tr>
		<tr>
		<td width="150"><b>&nbsp;ORDER-NO</b></td><td width="170">&nbsp;'.$order_no.'</td>
		</tr>
		<tr>
		<td width="150"><b>&nbsp;NAME OF ITEM</b></td><td width="170">&nbsp;'.$item_desc.'</td>
		</tr>
		<tr>
		<td width="150"><b>&nbsp;CHALLAN-NO</b></td><td width="170">&nbsp;'.$row[csf('challan_no')].'</td>
		</tr>
		<tr>
		<td width="150"><b>&nbsp;RCVD-DATE</b></td><td width="170">&nbsp;'.change_date_format($row[csf('receive_date')]).'</td>
		</tr>
		<tr>
		<td width="150"><b>&nbsp;CTN-NO</b></td><td width="170">&nbsp;'.$row[csf('ctn_no')].'</td>
		</tr>
		<tr>
		<td width="150"><b>&nbsp;ROOM NO</b></td><td width="170">&nbsp;'.$row[csf('room_no')].'</td>
		</tr>
		<tr>
		<td width="150"><b>&nbsp;RACK NO</b></td><td width="170">&nbsp;'.$row[csf('rack_no')].'</td>
		</tr>
		<tr>
		<td width="150"><b>&nbsp;SHELF NO</b></td><td width="170">&nbsp;'.$row[csf('self_no')].'</td>
		</tr>
		<tr>
		<td width="150"><b>&nbsp;BOX/BIN</b></td><td width="170">&nbsp;'.$row[csf('box_bin_no')].'</td>
		</tr>
		<tr>
		<td width="150"><b>&nbsp;CARTON QTY.</b></td><td width="170">&nbsp;'.$row[csf('ctn_qnty')].'</td>
		</tr>
		<tr>
		<td width="150"><b>&nbsp;FIRST ISSUE DATE</b></td><td width="170">&nbsp;</td>
		</tr>
		</table></td>';
		
		if($i%2==0) {$html.='</tr><tr><td><br><br><br><br></td></tr><tr>';}
		if( $i % 6 == 0 && $i < count( $result ) ) {
				$html .= "</tr></table>";
				$pdf->writeHTML($html, true, false, true, false, '');
				$pdf->AddPage();
				$html='<table border="0"><tr>';
			}
		$i++;
		
	}
	
	$html.='</tr></table>';	
		
	$pdf->writeHTML($html, true, false, true, false, '');
	$name = 'carton_sticker_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output( "".$name, 'F');
	echo $name;	
}

function return_domestic_rate($rate,$ile_cost,$exchange_rate,$conversion_factor)
{
	$rate_ile=$rate+$ile_cost;
	$rate_ile_exchange=$rate_ile*$exchange_rate;
	$doemstic_rate=$rate_ile_exchange/$conversion_factor;
	return $doemstic_rate;	
}

if ($action=="trims_receive_entry_printbackup")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	 //print_r ($data);
	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name"  );
	$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
	$color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );
	$size_library=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name"  );
    $user_name 		= return_library_array("select id,user_full_name from user_passwd", "id", "user_full_name");
	$sql="select id, recv_number, receive_basis, receive_date, booking_id, booking_no, challan_no, challan_date, lc_no, source, store_id, pay_mode, is_audited, audit_date, audit_by, supplier_id, currency_id, exchange_rate,booking_without_order from inv_receive_master where id='$data[1]' and status_active=1 and is_deleted=0 and entry_form=24 ";
	//echo $sql;
	$dataArray=sql_select($sql);

	$challan_no=$dataArray[0][csf('challan_no')];	
	
	$supplier_name="";
	
	if($dataArray[0][csf('pay_mode')]==3 || $dataArray[0][csf('pay_mode')]==5) $supplier_name=$company_library[$dataArray[0][csf('supplier_id')]]; else $supplier_name=$supplier_library[$dataArray[0][csf('supplier_id')]];
	
	?>
	<div style="width:1200px; margin-left:10px;">
		<table width="980" cellspacing="0" align="center" border="0" >
			<tr>
				<td colspan="7" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong>
				<br><b style="font-size:13px">
				<?
						
						$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
						foreach ($nameArray as $result)
						{ 
							echo $result[csf('plot_no')].', ';  
							echo $result[csf('level_no')].', ';
							echo $result[csf('road_no')].', ';  
							echo $result[csf('block_no')].', '; 
							echo $result[csf('city')].', '; 
							echo $result[csf('zip_code')].', ';  
							echo $result[csf('province')].', '; 
							echo $country_arr[$result[csf('country_id')]]; 
							
						}
					?>
					</b>
				</td>
			</tr>
			
			<tr>
				<td colspan="7" align="center" style="font-size:x-large"><strong><u>Trims Receive Challan</u></strong></center></td>
			</tr>
			<tr>
				<td width="160"><strong>System ID:</strong></td> <td width="175px"><? echo $dataArray[0][csf('recv_number')]; ?></td>
				<td width="120"><strong> Receive Basis :</strong></td><td width="175px" ><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
				<td width="125"><strong>Received Date:</strong></td><td width="175px"><? echo  change_date_format($dataArray[0][csf('receive_date')]); ?></td>
			</tr>
			<tr>
				<!--<td><strong>WO/PI:</strong></td> <td width="175px"><?echo $dataArray[0][csf('booking_no')]; ?></td>-->
				<td><strong>Challan No :</strong></td><td width="175px" ><? echo $dataArray[0][csf('challan_no')]; ?></td>
				<td><strong>Challan Date:</strong></td><td width="175px"><? echo  change_date_format($dataArray[0][csf('challan_date')]); ?></td>
				<td><strong>Supplier:</strong></td><td width="175px"><?=$supplier_name;//$supplier_library[$dataArray[0][csf('supplier_id')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Source:</strong></td><td width="175px"><? echo $source[$dataArray[0][csf('source')]]; ?></td>
				<td><strong> Currency:</strong></td><td width="175px"><? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
			</tr>
		</table>
		<br>
		<div style="width:100%;">
			<table align="right" cellspacing="0" width="1200"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<th width="30">SL</th>
					<th width="90" align="center">WO/PI No.</th>
					<th width="90" align="center">Item Group</th>
					<th width="110" align="center">Item Des.</th>
					<th width="70" align="center">Gmts Color</th>
					<th width="70" align="center">Item Color</th>
					<th width="60" align="center">Gmts Size</th>
					<th width="60" align="center">Item Size</th>
					<th width="70" align="center">Buyer Order</th>
					<th width="40" align="center">UOM</th>
					<th width="70" align="center">WO. Qty </th>
					<th width="70" align="center">Curr. Rec. Qty </th>
					<th width="60" align="center">Rate</th>
					<th width="70" align="center">Amount</th>
					<th width="70" align="center">Total Recv. Qty.</th>
					<th width="70" align="center">Balance Qty.</th>
					<th width="50" align="center">Reject Qty</th>
					<th align="center">Over Rcv. Status</th>
					<!--<th width="60" align="center">Rack</th>
					<th width="60" align="center">Shelf</th>
					<th width="60" align="center">Box</th>-->
				</thead>
		<?
			$mst_id=$dataArray[0][csf('id')];
			$booking_nos=''; $booking_sam_nos=''; $pi_ids=''; $orderIds='';
			$dtls_data=sql_select("select booking_no, booking_id, booking_without_order, order_id from inv_trims_entry_dtls where mst_id='$mst_id' and status_active='1' and is_deleted='0'");
			foreach($dtls_data as $row)
			{
				$orderIds.=$row[csf('order_id')].",";
				
				if($dataArray[0][csf('receive_basis')]==1)
				{
					$pi_ids.=$row[csf('booking_id')].",";
				}
				else if($dataArray[0][csf('receive_basis')]==12)
				{
					$booking_nos.="'".$row[csf('booking_no')]."',";
				}
				else if($dataArray[0][csf('receive_basis')]==2)
				{
					if($row[csf('booking_without_order')]==1)
					{
						$booking_sam_nos.="'".$row[csf('booking_no')]."',";
					}
					else
					{
						$booking_nos.="'".$row[csf('booking_no')]."',";
					}
				}
			}
			
			$orderIds=chop($orderIds,','); 
			$piArray=array();
			if($orderIds!="")
			{
				$orderIds=implode(",",array_unique(explode(",",$orderIds)));
				$piArray=return_library_array( "select id, po_number from wo_po_break_down where id in ($orderIds)", "id", "po_number"  );
			}
			
			if($dataArray[0][csf('receive_basis')]==2)
			{
				
				$recv_wo_data_arr=array();$recv_wo_data_arr_amt=array();
				$recv_data=sql_select("select a.booking_no,b.order_id as po_id,b.item_group_id as item_group,b.item_description,b.item_color,b.item_size,a.recv_number,sum(b.receive_qnty) as  receive_qnty from inv_receive_master a,inv_trims_entry_dtls b where a.id=b.mst_id and  a.entry_form=24  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 group by a.recv_number,a.booking_no,b.item_group_id,b.item_description,b.item_color,b.item_size,b.order_id");
				foreach($recv_data as $row)
				{ //pre_cost_fabric_cost_dtls_id
					$po_id_arr=array_unique(explode(",",$row[csf('po_id')]));
					foreach($po_id_arr as $po)
					{
						$recv_wo_data_arr[$row[csf('booking_no')]][$po][$row[csf('item_group')]][$row[csf('item_description')]]['recv_no'].=$row[csf('recv_number')].',';
						$recv_wo_data_arr_amt[$row[csf('recv_number')]][$row[csf('booking_no')]][$po][$row[csf('item_group')]][$row[csf('item_description')]][$row[csf('item_color')]][$row[csf('item_size')]]['recv_qty']=$row[csf('receive_qnty')];		
					}
				}
				//print_r($recv_wo_data_arr);
				
				
				$booking_nos=chop($booking_nos,','); $booking_sam_nos=chop($booking_sam_nos,',');
				
				if($booking_nos!="")
				{
					$booking_sam_nos=implode(",",array_unique(explode(",",$booking_sam_nos)));
					$sql_bookingqty = sql_select("select b.booking_no, sum(c.cons) as wo_qnty, b.trim_group as item_group, c.color_number_id, c.item_color, c.description, c.item_size 
					from wo_booking_dtls b,wo_trim_book_con_dtls c 
					where b.id=c.wo_trim_booking_dtls_id and b.booking_no in($booking_nos) 
					group by b.booking_no, b.trim_group, c.color_number_id, c.item_color, c.description, c.item_size");
				}
				foreach($sql_bookingqty as $b_qty)
				{
					if($b_qty[csf('color_number_id')]=="") $b_qty[csf('color_number_id')]=0;
					if($b_qty[csf('item_color')]=="") $b_qty[csf('item_color')]=0;
					$booking_qty_arr[$b_qty[csf('booking_no')]][$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$b_qty[csf('description')]][$b_qty[csf('item_size')]]=$b_qty[csf('wo_qnty')];
				}
				
				if($booking_sam_nos!="")
				{
					$booking_sam_nos=implode(",",array_unique(explode(",",$booking_sam_nos)));
					$sql_bookingqtysam = sql_select("select a.booking_no, sum(a.trim_qty) as wo_qnty, a.trim_group as item_group, a.fabric_color as item_color, a.gmts_color as color_number_id, a.fabric_description as description, a.item_size 
					from wo_non_ord_samp_booking_dtls a where a.booking_no in($booking_sam_nos) and a.status_active=1 and a.is_deleted=0 group by a.booking_no, a.trim_group, a.fabric_color, a.gmts_color, a.fabric_description, a.item_size");	
				}
				foreach($sql_bookingqtysam as $b_qty)
				{
					if($b_qty[csf('color_number_id')]=="") $b_qty[csf('color_number_id')]=0;
					if($b_qty[csf('item_color')]=="") $b_qty[csf('item_color')]=0;
					$booking_qty_arr[$b_qty[csf('booking_no')]][$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$b_qty[csf('description')]][$b_qty[csf('item_size')]]=$b_qty[csf('wo_qnty')];
				}
			}
			else if($dataArray[0][csf('receive_basis')]==1)
			{
				$pi_ids=chop($pi_ids,',');	
				$sql_bookingqty = sql_select("select a.id, sum(b.quantity) as wo_qnty,b.item_group,b.item_color,b.color_id as color_number_id,b.item_description as description, b.item_size  
				from  com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.id in($pi_ids) group by a.id, b.item_group, b.item_color, b.color_id, b.item_description, b.item_size");	
				foreach($sql_bookingqty as $b_qty)
				{
					if($b_qty[csf('color_number_id')]=="") $b_qty[csf('color_number_id')]=0;
					if($b_qty[csf('item_color')]=="") $b_qty[csf('item_color')]=0;
					$booking_qty_arr[$b_qty[csf('id')]][$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$b_qty[csf('description')]][$b_qty[csf('item_size')]]=$b_qty[csf('wo_qnty')];
				}
			}
			else if($dataArray[0][csf('receive_basis')]==12)
			{ 
				$booking_nos=chop($booking_nos,',');	
				$sql_booking="select a.order_no as booking_no, a.buyer_po_id as po_break_down_id, a.item_group, b.gmts_size_id as gmts_sizes, b.color_id as item_color, b.description, b.qnty as wo_qnty from subcon_ord_dtls a, subcon_ord_breakdown b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.order_no in($booking_nos)";
				//echo $sql_booking;die;
				$sql_bookingqty=sql_select($sql_booking);
				foreach($sql_bookingqty as $b_qty)
				{
					if($b_qty[csf('gmts_sizes')]=="") $b_qty[csf('gmts_sizes')]=0;
					if($b_qty[csf('item_color')]=="") $b_qty[csf('item_color')]=0;
					$booking_qty_arr[$b_qty[csf('booking_no')]][$b_qty[csf('po_break_down_id')]][$b_qty[csf('item_group')]][$b_qty[csf('gmts_sizes')]][$b_qty[csf('item_color')]][$b_qty[csf('description')]]=$b_qty[csf('wo_qnty')];				
				}			
				
			}
			//echo '<pre>';print_r($booking_qty_arr);

			$i=1;$total_rec_qty=0; $total_rec_balance_qty=0;
			$sql_dtls="select b.id, b.booking_no, b.booking_id, b.booking_without_order, b.item_group_id, b.item_description, b.order_id, b.gmts_color_id, b.item_color, b.item_size, b.cons_qnty, b.cons_uom, b.receive_qnty, b.rate, b.amount, b.reject_receive_qnty,b.gmts_size_id, c.room_no, c.rack_no, c.self_no, c.box_bin_no, c.ctn_no, c.ctn_qnty, b.payment_over_recv 
			from inv_trims_entry_dtls b left join inv_goods_placement c on c.dtls_id=b.id and c.entry_form=24 where b.mst_id='$mst_id' and b.status_active='1' and b.is_deleted='0' and b.trans_id=183885";
			//echo $sql_dtls;
			$pay_yes_no=array(0=>"Yes",1=>"No");
			$sql_result=sql_select($sql_dtls);
			foreach($sql_result as $row)
			{
				//print_r($booking_qty_arr);
				if ($i%2==0)  
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";
					
					$order_ids=explode(",",$row[csf('order_id')]);
					$po_number='';$recv_no_arr='';$prev_recv_qty=0;
					foreach($order_ids as $po_id)
					{
						$po_number.=$piArray[$po_id].',';
						
						$recv_no_arr=implode(",",array_unique(explode(",",rtrim($recv_wo_data_arr[$row[csf('booking_no')]][$po_id][$row[csf('item_group_id')]][$row[csf('item_description')]]['recv_no'],','))));
						$recv_id_arr=explode(",",$recv_no_arr);
						//print_r($recv_id_arr);
						foreach($recv_id_arr as $recv_id)
						{
							//echo $recv_id.'**'.$dataArray[0][csf('recv_number')].'####';
							if($recv_id!=$dataArray[0][csf('recv_number')])
							{
								echo 'aa';
								$prev_recv_qty+=$recv_wo_data_arr_amt[$recv_id][$row[csf('booking_no')]][$po_id][$row[csf('item_group_id')]][$row[csf('item_description')]][$row[csf('item_color')]][$row[csf('item_size')]]['recv_qty'];
							}
						}
						
					}
					echo $prev_recv_qty.'hjhgygvygkuy';
					$po_number=chop($po_number,',');
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td><? echo $i; ?></td>
						<td><p><? echo $row[csf('booking_no')]; ?></p></td>
						<td><p><? echo $item_library[$row[csf('item_group_id')]]; ?></p></td>
						<td><p><? echo $row[csf('item_description')]; ?></p></td>
						<td><p><? echo $color_library[$row[csf('gmts_color_id')]]; ?></p></td>
						<td><p><? echo $color_library[$row[csf('item_color')]]; ?></p></td>
						<td align="center"><p><? echo $size_library[$row[csf('gmts_size_id')]]; ?></p></td>
						<td align="center"><p><? echo $row[csf('item_size')];?></p></td>
						<td width="170" style="word-break:break-all;"><? echo $po_number; ?></td>
						<td align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
						<td align="right">
						<?
							if($row[csf('gmts_size_id')]=="") $row[csf('gmts_size_id')]=0;
							if($row[csf('gmts_color_id')]=="") $row[csf('gmts_color_id')]=0;
							if($row[csf('item_color')]=="") $row[csf('item_color')]=0;
							
							if($dataArray[0][csf('receive_basis')]==1)
							{
								$woorder_qty=$booking_qty_arr[$row[csf('booking_id')]][$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$row[csf('item_description')]][$row[csf('item_size')]];
							}
							else if($dataArray[0][csf('receive_basis')]==12)
							{
								$woorder_qty=$booking_qty_arr[$row[csf('booking_no')]][$row[csf('order_id')]][$row[csf('item_group_id')]][$row[csf('gmts_size_id')]][$row[csf('item_color')]][$row[csf('item_description')]];
							}
							else
							{
								$woorder_qty=$booking_qty_arr[$row[csf('booking_no')]][$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$row[csf('item_description')]][$row[csf('item_size')]];
							}
							
							$total_woorder_qty+=$woorder_qty;
							echo number_format($woorder_qty,2,".",""); 

							$tot_recv_qty=$row[csf('receive_qnty')]+$prev_recv_qty;
							$tot_recv_balance=$woorder_qty-$tot_recv_qty;//$row[csf('receive_qnty')]+$prev_recv_qty;
						?>
						</td>
						<td align="right"><? echo number_format($row[csf('receive_qnty')],2,".",""); ?></td>
						<td align="right"><? echo number_format($row[csf('rate')],4,'.',''); ?></td>
						<td align="right" title="<?= $row[csf('amount')];?>"><? echo number_format($row[csf('amount')],2,'.',''); ?></td>
						<td align="right"><? echo number_format($tot_recv_qty,2,'.',''); ?></td>
						<td align="right"><? echo number_format($tot_recv_balance,2,'.',''); ?></td>
						<td align="right"><? echo number_format($row[csf('reject_receive_qnty')],2,'.',''); ?></td>
						<td align="center"><p><? echo $pay_yes_no[$row[csf('payment_over_recv')]]; ?></p></td>
					<!-- <td align="center"><p><? //echo $row[csf('rack_no')]; ?></p></td>
						<td align="center"><p><? //echo $row[csf('self_no')]; ?></p></td>
						<td align="center"><p><? //echo $row[csf('box_bin_no')]; ?></p></td>-->
					</tr>
				<?
				$i++;
				$tot_rec_qty+=$row[csf('receive_qnty')];
				$tot_amount+=$row[csf('amount')];
				$tot_reject_qty+=$row[csf('reject_receive_qnty')];
				$total_rec_qty+=$tot_recv_qty;
				$total_rec_balance_qty+=$woorder_qty-$row[csf('receive_qnty')];
			}
		?>
				<tr bgcolor="#dddddd">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td colspan="2" align="right"><b>Total :</b></td>
					<td align="right"><? echo number_format($total_woorder_qty,2,'.',''); ?></td>
					<td align="right"><? echo number_format($tot_rec_qty,2,'.',''); ?></td>
					<td>&nbsp;</td>
					<td align="right" title="<?= $tot_amount;?>"><? echo number_format($tot_amount,2,'.',''); ?></td>
					<td align="right"><? echo number_format($total_rec_qty,2,'.',''); ?></td>
					<td align="right"><? echo number_format($total_rec_balance_qty,2,'.',''); ?></td>
					<td align="right"><? echo number_format($tot_reject_qty,2,'.',''); ?></td>
					<td>&nbsp;</td>
				<!-- <td align="center">&nbsp;</td>
					<td align="center">&nbsp;</td>
					<td align="center">&nbsp;</td>-->
				</tr>
		</table>
			<table>
				<tr>
					<?php

					if($dataArray[0][csf("is_audited")]==1){
						?>
						<td><strong><? echo 'Audited By &nbsp;'.$user_name[$dataArray[0][csf("audit_by")]].'&nbsp;At&nbsp;'.$dataArray[0][csf("audit_date")]; ?></strong></td>
						<?php
					}
					?>


				</tr>
			</table>
		<br>
		<?
			echo signature_table(35, $data[0], "1150px");
		?>
		</div>
	</div>
	<?
	exit();
}

if ($action=="trims_receive_entry_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);	
	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name"  );
	$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
	$color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );
	$size_library=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name"  );
    $user_name 		= return_library_array("select id,user_full_name from user_passwd", "id", "user_full_name");
	$sql="select id, recv_number, receive_basis, receive_date, booking_id, booking_no, challan_no, challan_date, lc_no, source, store_id, pay_mode, is_audited, audit_date, audit_by, supplier_id, currency_id, exchange_rate,booking_without_order from inv_receive_master where id='$data[1]' and status_active=1 and is_deleted=0 and entry_form=24 ";
	//echo $sql;
	$dataArray=sql_select($sql);

	$mst_id=$dataArray[0][csf('id')];
	$challan_no=$dataArray[0][csf('challan_no')];
	$booking_without_order=$dataArray[0][csf('booking_without_order')];
	
	$supplier_name="";
	
	if($dataArray[0][csf('pay_mode')]==3 || $dataArray[0][csf('pay_mode')]==5) $supplier_name=$company_library[$dataArray[0][csf('supplier_id')]]; else $supplier_name=$supplier_library[$dataArray[0][csf('supplier_id')]];
	
	?>
	<div style="width:1200px; margin-left:10px;">
		<table width="980" cellspacing="0" align="center" border="0" >
			<tr>
				<td colspan="7" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong>
				<br><b style="font-size:13px">
					<?						
						$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
						foreach ($nameArray as $result)
						{ 
							echo $result[csf('plot_no')].', ';  
							echo $result[csf('level_no')].', ';
							echo $result[csf('road_no')].', ';  
							echo $result[csf('block_no')].', '; 
							echo $result[csf('city')].', '; 
							echo $result[csf('zip_code')].', ';  
							echo $result[csf('province')].', '; 
							echo $country_arr[$result[csf('country_id')]]; 
							
						}
					?>
				</b>
				</td>
			</tr>
			
			<tr>
				<td colspan="7" align="center" style="font-size:x-large"><strong><u>Trims Receive Challan</u></strong></center></td>
			</tr>
			<tr>
				<td width="160"><strong>System ID:</strong></td> <td width="175px"><? echo $dataArray[0][csf('recv_number')]; ?></td>
				<td width="120"><strong> Receive Basis :</strong></td><td width="175px" ><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
				<td width="125"><strong>Received Date:</strong></td><td width="175px"><? echo  change_date_format($dataArray[0][csf('receive_date')]); ?></td>
			</tr>
			<tr>
				<!--<td><strong>WO/PI:</strong></td> <td width="175px"><?echo $dataArray[0][csf('booking_no')]; ?></td>-->
				<td><strong>Challan No :</strong></td><td width="175px" ><? echo $dataArray[0][csf('challan_no')]; ?></td>
				<td><strong>Challan Date:</strong></td><td width="175px"><? echo  change_date_format($dataArray[0][csf('challan_date')]); ?></td>
				<td><strong>Supplier:</strong></td><td width="175px"><?=$supplier_name;//$supplier_library[$dataArray[0][csf('supplier_id')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Source:</strong></td><td width="175px"><? echo $source[$dataArray[0][csf('source')]]; ?></td>
				<td><strong> Currency:</strong></td><td width="175px"><? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
			</tr>
		</table>
		<br>
		<div style="width:100%;">
			<table align="right" cellspacing="0" width="1200"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<th width="30">SL</th>
					<th width="90" align="center">WO/PI No.</th>
					<th width="90" align="center">Item Group</th>
					<th width="110" align="center">Item Des.</th>
					<th width="70" align="center">Gmts Color</th>
					<th width="70" align="center">Item Color</th>
					<th width="60" align="center">Gmts Size</th>
					<th width="60" align="center">Item Size</th>
					<th width="70" align="center">Buyer Order</th>
					<th width="40" align="center">UOM</th>
					<th width="70" align="center">WO. Qty </th>
					<th width="70" align="center">Curr. Rec. Qty </th>
					<th width="60" align="center">Rate</th>
					<th width="70" align="center">Amount</th>
					<th width="70" align="center">Total Recv. Qty.</th>
					<th width="70" align="center">Balance Qty.</th>
					<th width="50" align="center">Reject Qty</th>
					<th align="center">Over Rcv. Status</th>
					<!--<th width="60" align="center">Rack</th>
					<th width="60" align="center">Shelf</th>
					<th width="60" align="center">Box</th>-->
				</thead>
				<?				
				$booking_nos=''; $booking_sam_nos=''; $pi_ids=''; $orderIds='';
				$dtls_data=sql_select("select booking_no, booking_id, booking_without_order, order_id from inv_trims_entry_dtls where mst_id='$mst_id' and status_active='1' and is_deleted='0'");
				foreach($dtls_data as $row)
				{
					$orderIds.=$row[csf('order_id')].",";
					
					if($dataArray[0][csf('receive_basis')]==1)
					{
						$pi_ids.=$row[csf('booking_id')].",";
					}					
					else if($dataArray[0][csf('receive_basis')]==2)
					{
						if($row[csf('booking_without_order')]==1) $booking_sam_nos.="'".$row[csf('booking_no')]."',";
						else $booking_nos.="'".$row[csf('booking_no')]."',";
					}
					else
					{
						$booking_nos.="'".$row[csf('booking_no')]."',";
					}
				}
			
				$orderIds=chop($orderIds,','); 
				$piArray=array();
				if($orderIds!="")
				{
					$orderIds=implode(",",array_unique(explode(",",$orderIds)));
					$piArray=return_library_array( "select id, po_number from wo_po_break_down where id in ($orderIds)", "id", "po_number"  );
				}
			
				if($dataArray[0][csf('receive_basis')]==2)
				{
					if($dataArray[0][csf('booking_without_order')]==0)
					{
						if($db_type==0) $null_val="c.color_number_id,c.item_color,c.item_size,c.gmts_sizes,";
						else if($db_type==2) $null_val="nvl(c.color_number_id,0) as color_number_id,nvl(c.item_color,0) as item_color,nvl(c.gmts_sizes,0) as gmts_sizes,c.item_size,";
						$booking_nos=implode(",",array_unique(explode(",",rtrim($booking_nos,','))));

						$sql_bookingqty ="select c.cons as wo_qnty, b.id as dtls_id, c.id, b.trim_group as item_group, $null_val c.description, c.brand_supplier, b.po_break_down_id as po_id, b.sensitivity 
						from wo_booking_mst a, wo_booking_dtls b, wo_trim_book_con_dtls c 
						where a.booking_no=b.booking_no and b.id=c.wo_trim_booking_dtls_id and b.status_active=1 and c.status_active=1 and a.booking_no in ($booking_nos)";
						//and a.booking_no='".$dataArray[0][csf('booking_no')]."'
					}
					else
					{
						$booking_sam_nos=implode(",",array_unique(explode(",",rtrim($booking_sam_nos,','))));
						$sql_bookingqty = "select a.booking_no, sum(a.trim_qty) as wo_qnty, a.trim_group as item_group, a.fabric_color as item_color, a.gmts_color as color_number_id, 0 as gmts_sizes, a.fabric_description as description, a.item_size 
						from wo_non_ord_samp_booking_dtls a where a.booking_no in($booking_sam_nos) and a.status_active=1 and a.is_deleted=0 group by a.booking_no, a.trim_group, a.fabric_color, a.gmts_color, a.fabric_description, a.item_size";
					}
				}
				else if($dataArray[0][csf('receive_basis')]==1)
				{
					$pi_ids=chop($pi_ids,',');
					$sql_bookingqty = "select b.id, b.quantity as wo_qnty, b.item_group, b.item_color, b.color_id as color_number_id, 0 as gmts_sizes, b.item_description as description, b.item_size, c.po_break_down_id as po_id 
					from  com_pi_master_details a, com_pi_item_details b, wo_trim_book_con_dtls c where a.id=b.pi_id and b.work_order_dtls_id=c.wo_trim_booking_dtls_id and a.id in($pi_ids) and a.status_active=1 and b.status_active=1 and c.status_active=1";					
				}
				else
				{
					$booking_nos=implode(",",array_unique(explode(",",rtrim($booking_nos,','))));
					$sql_bookingqty="select a.order_no as booking_no, a.buyer_po_id as po_id, a.item_group, b.gmts_size_id as gmts_sizes, b.color_id as item_color, b.description, b.qnty as wo_qnty from subcon_ord_dtls a, subcon_ord_breakdown b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.order_no in($booking_nos)";
				}
				$sql_bookingqty_res=sql_select($sql_bookingqty);
				foreach($sql_bookingqty_res as $b_qty)
				{
					$desc=trim(strtolower($b_qty[csf('description')]));
					if($b_qty[csf('color_number_id')]=="") $b_qty[csf('color_number_id')]=0;
					if($b_qty[csf('gmts_sizes')]=="") $b_qty[csf('gmts_sizes')]=0;
					if($b_qty[csf('item_color')]=="") $b_qty[csf('item_color')]=0;

					if($dataArray[0][csf('receive_basis')]==2)
					{
						if($dataArray[0][csf('booking_without_order')]==0)
						{
							$booking_qty_arr[$b_qty[csf('po_id')]][$b_qty[csf('item_group')]][$desc][$b_qty[csf('color_number_id')]][$b_qty[csf('gmts_sizes')]][$b_qty[csf('item_color')]][trim($b_qty[csf('item_size')])][trim($b_qty[csf('brand_supplier')])][$b_qty[csf('sensitivity')]]+=$b_qty[csf('wo_qnty')];
						}
						else
						{
							$booking_qty_arr[$b_qty[csf('item_group')]][$desc][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]]+=$b_qty[csf('wo_qnty')];	
						}
					}
					else if($dataArray[0][csf('receive_basis')]==1)
					{
						if ($pi_dtls_id_check[$b_qty[csf('id')]]=="")
						{
							$pi_dtls_id_check[$b_qty[csf('id')]]=$b_qty[csf('id')];
							$booking_qty_arr[$b_qty[csf('po_id')]][$b_qty[csf('item_group')]][$desc][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$b_qty[csf('item_size')]]+=$b_qty[csf('wo_qnty')];
						}						
					}
					else
					{
						$booking_qty_arr[$b_qty[csf('po_id')]][$b_qty[csf('item_group')]][$desc][$b_qty[csf('gmts_sizes')]][$b_qty[csf('item_color')]]+=$b_qty[csf('wo_qnty')];
					}
				
				}
				//echo '<pre>';print_r($booking_qty_arr);

				if($booking_without_order==0)
				{
					$sql_dtls="select b.id,b.mst_id,b.item_group_id, b.item_description, c.po_breakdown_id as order_id, b.gmts_color_id, b.gmts_size_id,  b.item_color, b.item_size, b.brand_supplier, b.sensitivity, c.quantity as receive_qnty 
					from inv_receive_master a,inv_trims_entry_dtls b, order_wise_pro_details c 
					where c.dtls_id=b.id and c.entry_form=24 and b.mst_id=a.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.booking_no in ($booking_nos) and b.mst_id<>$mst_id";
				}
				else
				{
					$sql_dtls="select b.id,b.mst_id,b.item_group_id, b.item_description, b.order_id, b.gmts_color_id, b.gmts_size_id, b.item_color, b.item_size, b.brand_supplier, b.sensitivity, b.receive_qnty 
					from inv_receive_master a,inv_trims_entry_dtls b where a.entry_form=24 and b.mst_id=a.id and b.status_active=1 and b.is_deleted=0 and a.booking_no in ($booking_nos) and b.mst_id<>$mst_id";
				}
				//echo $sql_dtls;
				$po_breakdown_ids='';
				$tot_qty_arr=array();
				$prev_qty_arr=array();
				$sql_result=sql_select($sql_dtls);

				foreach($sql_result as $rows)
				{
					$key=$rows[csf('order_id')]."**".$rows[csf('item_group_id')]."**".trim(strtolower($rows[csf('item_description')]))."**".$rows[csf('gmts_color_id')]."**".$rows[csf('gmts_size_id')]."**".$rows[csf('item_color')]."**".trim($rows[csf('item_size')])."**".trim($rows[csf('brand_supplier')]."**".$rows[csf('sensitivity')]);
					if($rows[csf('mst_id')] < $dataArray[0][csf('id')])
					{
						$prev_qty_arr[$key]+=$rows[csf('receive_qnty')];
					}
					$tot_qty_arr[$key]+=$rows[csf('receive_qnty')];
					$po_breakdown_ids .=$rows[csf('order_id')].',';
				}
				//echo '<pre>';print_r($prev_qty_arr);

				$po_breakdown_ids=implode(",",array_unique(explode(",",chop($po_breakdown_ids,','))));
				$rcvRtn_qty_sql = "SELECT b.po_breakdown_id, sum( b.quantity ) as recv_return_qty 
				from product_details_master a, inv_transaction t, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d
				where a.id=b.prod_id and a.id=t.prod_id and t.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.id=b.prod_id and  b.po_breakdown_id in ($po_breakdown_ids) and a.item_category_id=4 and b.entry_form in(49) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  
				group by b.po_breakdown_id";

				$totalRcvRtnQty_arr=array();
				$rcvRtn_qtyArray=sql_select($rcvRtn_qty_sql); 
				foreach($rcvRtn_qtyArray as $row)
				foreach($rcvRtn_qtyArray as $row)
				{ 
					$totalRcvRtnQty_arr[$row[csf('po_breakdown_id')]]=$row[csf('recv_return_qty')];
				}
				
				$po_sql="select a.JOB_NO, a.BUYER_NAME, a.STYLE_REF_NO, b.ID, b.PO_NUMBER from wo_po_details_master a, wo_po_break_down b 
				where a.id=b.job_id and a.status_active=1 and b.status_active=1 and b.id in($orderIds)";
				$po_sql_result=sql_select($po_sql);
				$po_data_arr=array();
				foreach($po_sql_result as $val)
				{
					$po_data_arr[$val["ID"]]["JOB_NO"]=$val["JOB_NO"];
					$po_data_arr[$val["ID"]]["BUYER_NAME"]=$val["BUYER_NAME"];
					$po_data_arr[$val["ID"]]["STYLE_REF_NO"]=$val["STYLE_REF_NO"];
					$po_data_arr[$val["ID"]]["PO_NUMBER"]=$val["PO_NUMBER"];
				}	

				$i=1;$total_rec_qty=0; $total_rec_balance_qty=0;

				$sql_dtls="select b.id, b.booking_no, b.booking_id, b.booking_without_order, b.item_group_id, b.item_description, b.brand_supplier, b.order_id, b.gmts_color_id, b.gmts_size_id, b.item_color, b.item_size, b.cons_qnty, b.cons_uom, b.receive_qnty, b.sensitivity, b.rate, b.amount, b.reject_receive_qnty, c.room_no, c.rack_no, c.self_no, c.box_bin_no, c.ctn_no, c.ctn_qnty, b.payment_over_recv 
				from inv_trims_entry_dtls b left join inv_goods_placement c on c.dtls_id=b.id and c.entry_form=24 where b.mst_id='$mst_id' and b.status_active='1' and b.is_deleted='0'";
				//echo $sql_dtls;
				$pay_yes_no=array(0=>"Yes",1=>"No");
				$sql_result=sql_select($sql_dtls);
				$totalRcvRtnQty_arr=array();
				$total_woorder_qty=0;
				foreach($sql_result as $row)
				{
					//print_r($booking_qty_arr);
					$woorder_qty=$prevRcvQty=$rcvRtnQty=0;
					$po_numbers='';
					if ($i%2==0) $bgcolor="#E9F3FF";						
					else $bgcolor="#FFFFFF";

					$descp=trim(strtolower($row[csf('item_description')]));					

					if ($row[csf('order_id')] != "")
					{
						$order_ids=explode(",",$row[csf('order_id')]);
						foreach ($order_ids as $order_id)
						{
							$po_numbers.=$po_data_arr[$order_id]["PO_NUMBER"].',';
							$key=$order_id."**".$row[csf('item_group_id')]."**".$descp."**".$row[csf('gmts_color_id')]."**".$row[csf('gmts_size_id')]."**".$row[csf('item_color')]."**".trim($row[csf('item_size')])."**".trim($row[csf('brand_supplier')])."**".$row[csf('sensitivity')];
							$rcvRtnQty=$totalRcvRtnQty_arr[$row[csf('order_id')]];
							$prev_recv_qty=$prev_qty_arr[$key]-$rcvRtnQty;

							if($dataArray[0][csf('receive_basis')]==2)
							{
								if($dataArray[0][csf('booking_without_order')]==0)
								{
									$woorder_qty+=$booking_qty_arr[$order_id][$row[csf('item_group_id')]][$descp][$row[csf('gmts_color_id')]][$row[csf('gmts_size_id')]][$row[csf('item_color')]][trim($row[csf('item_size')])][trim($row[csf('brand_supplier')])][$row[csf('sensitivity')]];
									//echo $order_id."**".$row[csf('item_group_id')]."**".$descp."**".$row[csf('gmts_color_id')]."**".$row[csf('gmts_size_id')]."**".$row[csf('item_color')]."**".trim($row[csf('item_size')])."**".trim($row[csf('brand_supplier')])."**".$row[csf('sensitivity')].'#####';						
								}
								else
								{
									$woorder_qty+=$booking_qty_arr[$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$descp];	
								}
							}
							else if ($dataArray[0][csf('receive_basis')]==1)
							{
								$woorder_qty+=$booking_qty_arr[$order_id][$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$descp][$row[csf('item_size')]];
							}
							else
							{
								$woorder_qty+=$booking_qty_arr[$order_id][$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$descp];
							}
						}
					}
					else
					{
						$key=$row[csf('order_id')]."**".$row[csf('item_group_id')]."**".$descp."**".$row[csf('gmts_color_id')]."**".$row[csf('gmts_size_id')]."**".$row[csf('item_color')]."**".trim($row[csf('item_size')])."**".trim($row[csf('brand_supplier')])."**".$row[csf('sensitivity')];
						$rcvRtnQty=$totalRcvRtnQty_arr[$row[csf('order_id')]];
						$prevRcvQty=$prev_qty_arr[$key]-$rcvRtnQty;

						if($dataArray[0][csf('receive_basis')]==2)
						{
							if($dataArray[0][csf('booking_without_order')]!=0)
							{
								$woorder_qty+=$booking_qty_arr[$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$descp];
							}
						}
					}	
			
					$total_woorder_qty+=$woorder_qty;
					$tot_recv_qty=$row[csf('receive_qnty')]+$prev_recv_qty;
					$tot_recv_balance=$woorder_qty-$tot_recv_qty;//$row[csf('receive_qnty')]+$prev_recv_qty;

					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td><? echo $i; ?></td>
						<td><p><? echo $row[csf('booking_no')]; ?></p></td>
						<td><p><? echo $item_library[$row[csf('item_group_id')]]; ?></p></td>
						<td><p><? echo $row[csf('item_description')]; ?></p></td>
						<td><p><? echo $color_library[$row[csf('gmts_color_id')]]; ?></p></td>
						<td><p><? echo $color_library[$row[csf('item_color')]]; ?></p></td>
						<td align="center"><p><? echo $size_library[$row[csf('gmts_size_id')]]; ?></p></td>
						<td align="center"><p><? echo $row[csf('item_size')];?></p></td>
						<td width="170" style="word-break:break-all;"><? echo rtrim($po_numbers,','); ?></td>
						<td align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
						<td align="right"><? echo number_format($woorder_qty,2,".",""); ?></td>
						<td align="right"><? echo number_format($row[csf('receive_qnty')],2,".",""); ?></td>
						<td align="right"><? echo number_format($row[csf('rate')],4,'.',''); ?></td>
						<td align="right" title="<?= $row[csf('amount')];?>"><? echo number_format($row[csf('amount')],2,'.',''); ?></td>
						<td align="right"><? echo number_format($tot_recv_qty,2,'.',''); ?></td>
						<td align="right"><? echo number_format($tot_recv_balance,2,'.',''); ?></td>
						<td align="right"><? echo number_format($row[csf('reject_receive_qnty')],2,'.',''); ?></td>
						<td align="center"><p><? echo $pay_yes_no[$row[csf('payment_over_recv')]]; ?></p></td>
					</tr>
					<?
					$i++;
					$tot_rec_qty+=$row[csf('receive_qnty')];
					$tot_amount+=$row[csf('amount')];
					$tot_reject_qty+=$row[csf('reject_receive_qnty')];
					$total_rec_qty+=$tot_recv_qty;
					$total_rec_balance_qty+=$woorder_qty-$row[csf('receive_qnty')];
				}
				?>
				<tr bgcolor="#dddddd">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>

					<td colspan="2" align="right"><b>Total :</b></td>
					<td align="right"><? echo number_format($total_woorder_qty,2,'.',''); ?></td>
					<td align="right"><? echo number_format($tot_rec_qty,2,'.',''); ?></td>
					<td>&nbsp;</td>
					<td align="right" title="<?= $tot_amount;?>"><? echo number_format($tot_amount,2,'.',''); ?></td>
					<td align="right"><? echo number_format($total_rec_qty,2,'.',''); ?></td>
					<td align="right"><? echo number_format($total_rec_balance_qty,2,'.',''); ?></td>
					<td align="right"><? echo number_format($tot_reject_qty,2,'.',''); ?></td>
					<td>&nbsp;</td>
				</tr>
			</table>
			<table>
				<tr>
					<?
					if($dataArray[0][csf("is_audited")]==1)
					{
						?>
						<td><strong><? echo 'Audited By &nbsp;'.$user_name[$dataArray[0][csf("audit_by")]].'&nbsp;At&nbsp;'.$dataArray[0][csf("audit_date")]; ?></strong></td>
						<?
					}
					?>
				</tr>
			</table>
			<br>
			<? echo signature_table(35, $data[0], "1150px"); ?>
		</div>
	</div>
	<?
	exit();
}


if ($action=="trims_receive_entry_print_2") 
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	
	$buyer_name_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$item_library=return_library_array( "select id, item_name from lib_item_group where ITEM_CATEGORY=4", "id", "item_name"  );
	$store_name_arr=return_library_array( "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data[0]' and b.category_type=4 and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name", "id", "store_name"  );
    //$user_name = return_library_array("select id,user_full_name from user_passwd", "id", "user_full_name");

	$sql="select id, recv_number,item_category, receive_basis, receive_date, booking_id, booking_no, challan_no, challan_date, lc_no, source, store_id, supplier_id, is_audited, audit_date, audit_by, currency_id, exchange_rate,booking_without_order,pay_mode,knitting_source from inv_receive_master where id='$data[1]' and status_active=1 and is_deleted=0 and entry_form=24 ";
	//echo $sql;
	$dataArray=sql_select($sql);

	$tranSql="select id, booking_no from inv_trims_entry_dtls where mst_id='$data[1]' and status_active=1 and is_deleted=0 ";
	$transDataArray=sql_select($tranSql); $booking_no_arr=array();
	foreach($transDataArray as $row)
	{ 
		$booking_no_arr[]=$row[csf('booking_no')];
	}
	$booking_nos=implode("','",array_unique($booking_no_arr));
	$bookingNos=implode(",",array_unique($booking_no_arr));
	$booking_without_order=$dataArray[0][csf('booking_without_order')];
	
	if($dataArray[0][csf('knitting_source')]==3 || $dataArray[0][csf('knitting_source')]==5)
	{
		$supplier_library=return_library_array( "select id, company_name from lib_company where id='".$dataArray[0][csf('supplier_id')]."'", "id", "company_name"  );
	}
	else
	{
		$supplier_library=return_library_array( "select id,supplier_name from lib_supplier where id='".$dataArray[0][csf('supplier_id')]."'", "id","supplier_name"  );
	}
	
	
	?>
	<div style="width:1300px;">
		<table width="1010" cellspacing="0" align="center" border="0">
			<tr>
				<td colspan="7" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="7" align="center">
					<?
						$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
						foreach ($nameArray as $result)
						{
						?>
							<? echo $result[csf('plot_no')]; ?>
							<? if($result[csf('level_no')]!="") echo ",".$result[csf('level_no')]?>
							<? if($result[csf('road_no')]!="") echo ",".$result[csf('road_no')]; ?>
							<? if($result[csf('block_no')]!="") echo ",".$result[csf('block_no')];?>
							<? if($result[csf('city')]!="") echo ",".$result[csf('city')];?>
							<? if($result[csf('zip_code')]!="") echo ",".$result[csf('zip_code')]; ?>
							<? if($result[csf('province')]!="") echo ",".$result[csf('province')];?>
							<? if($result[csf('country_id')]!="") echo ",".$country_arr[$result[csf('country_id')]]; ?><br>
							Email:<? if($result[csf('email')]!="") echo $result[csf('email')].",";?>
							Website:<? if($result[csf('website')]!="") echo $result[csf('website')];

						}
					?> 
				</td>
			</tr>
			<tr>
				<td colspan="7" align="center" style="font-size:x-large"><strong><u>Material Receiving Report</u></strong></center></td>
			</tr>
			<br>
			<table cellspacing="0" width="1000" align="center" border="1" rules="all" class="">
			<tr>
				<td width="130"><strong>MRR/System ID:</strong></td>
				<td width="230"><? echo $dataArray[0][csf('recv_number')]; ?></td>
				<td width="110"><strong> Receive Basis :</strong></td>
				<td><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
				<td ><strong>Received Date:</strong></td>
				<td><? echo  change_date_format($dataArray[0][csf('receive_date')]); ?></td>
			</tr>
			<tr>
				<td><strong>Challan No :</strong></td> <td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
				<td><strong>Currency:</strong></td><td width="175px" ><? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
				<td><strong>Store Name:</strong></td><td width="175px"><? echo $store_name_arr[$dataArray[0][csf('store_id')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Supplier:</strong></td><td width="175px"><? echo $supplier_library[$dataArray[0][csf('supplier_id')]]; ?></td>
				<td><strong>L/C:</strong></td><td width="175px"><? echo $supplier_library[$dataArray[0][csf('lc_no')]]; ?></td>
				<td><strong> Exchange Rate:</strong></td><td width="175px"><? echo $dataArray[0][csf('exchange_rate')]; ?></td>
			</tr>
			
			<tr>
				<td><strong>WO/PI:</strong></td><td width="175px"><? echo $bookingNos; ?></td>
				<td><strong>Source:</strong></td><td width="175px"><? echo $source[$dataArray[0][csf('source')]]; ?></td>
				<td><strong>Pay mode:</strong></td><td width="175px"><? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Item Catagory</strong></td><td width="175px"><? echo $item_category[$dataArray[0][csf('item_category')]]; ?></td>
			</tr>
		</table>
		</table>
		<br>
		<div style="width:100%;">
			<table align="center" cellspacing="0" width="1300"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<th width="30">SL</th>
					<th width="110" align="center">Job/PO/Buyer/Style</th>
					<th width="110" align="center">Item Group</th>
					<th width="140" align="center">Item Des.</th>
					<th width="40" align="center">UOM</th>
					<th width="70" align="center">WO Qty </th>
					<th width="70" align="center">Prev. Rec. Qty </th>
					<th width="70" align="center">Curr. Rec. Qty </th>
					<th width="70" align="center">Tot. Rec. Qty </th>
					<th width="70" align="center">WO Balance</th>
					<th width="60" align="center">Rate</th>
					<th width="70" align="center">Amount</th>
					<th width="70">Comments</th>
					<th width="60" align="center">Room No</th>
					<th width="60" align="center">Rack No</th>
					<th width="60" align="center">Shelf No</th>
					<th width="60" align="center">Box/Bin</th>
					<th align="center">Over Rcv. Status</th>
				</thead>
				<? 			
				if($dataArray[0][csf('receive_basis')]==1 || $dataArray[0][csf('receive_basis')]==2)
				{
					if($dataArray[0][csf('receive_basis')]==2)
					{
						if($dataArray[0][csf('booking_without_order')]==0)
						{
							if($db_type==0) $null_val="c.color_number_id,c.item_color,c.item_size,c.gmts_sizes,";
							else if($db_type==2) $null_val="nvl(c.color_number_id,0) as color_number_id,nvl(c.item_color,0) as item_color,nvl(c.gmts_sizes,0) as gmts_sizes,c.item_size,";

							$sql_bookingqty =sql_select("select c.cons as wo_qnty, b.id as dtls_id, c.id, b.trim_group as item_group, $null_val c.description, c.brand_supplier, b.po_break_down_id as po_id, b.sensitivity 
							from wo_booking_mst a, wo_booking_dtls b, wo_trim_book_con_dtls c 
							where a.booking_no=b.booking_no and b.id=c.wo_trim_booking_dtls_id and b.status_active=1 and c.status_active=1 and a.booking_no in ('".$booking_nos."')");
							//and a.booking_no='".$dataArray[0][csf('booking_no')]."'
						}
						else
						{
							$sql_bookingqty = sql_select("select sum(a.trim_qty) as wo_qnty, a.trim_group as item_group, a.fabric_color as item_color, a.gmts_color as color_number_id, a.fabric_description as description 
							from wo_non_ord_samp_booking_dtls a where a.booking_no in ('".$booking_nos."') 
							group by  a.trim_group,a.fabric_color,a.gmts_color,a.fabric_description");	
						}
					}
					else
					{
						$sql_bookingqty = sql_select("select b.id, b.quantity as wo_qnty, b.item_group, b.item_color, b.color_id as color_number_id, b.item_description as description, b.item_size, c.po_break_down_id as po_id 
						from  com_pi_master_details a, com_pi_item_details b, wo_trim_book_con_dtls c where a.id=b.pi_id and b.work_order_dtls_id=c.wo_trim_booking_dtls_id and a.id='".$dataArray[0][csf('booking_id')]."' and a.status_active=1 and b.status_active=1 and c.status_active=1");
					}
					
					foreach($sql_bookingqty as $b_qty)
					{
						$desc=trim(strtolower($b_qty[csf('description')]));
						if($dataArray[0][csf('receive_basis')]==2)
						{
							if($dataArray[0][csf('booking_without_order')]==0)
							{
								$booking_qty_arr[$b_qty[csf('po_id')]][$b_qty[csf('item_group')]][$desc][$b_qty[csf('color_number_id')]][$b_qty[csf('gmts_sizes')]][$b_qty[csf('item_color')]][trim($b_qty[csf('item_size')])][trim($b_qty[csf('brand_supplier')])][$b_qty[csf('sensitivity')]]+=$b_qty[csf('wo_qnty')];
							}
							else
							{
								$booking_qty_arr[$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$desc]+=$b_qty[csf('wo_qnty')];	
							}
						}
						else
						{
							if ($pi_dtls_id_check[$b_qty[csf('id')]]=="")
							{
								$pi_dtls_id_check[$b_qty[csf('id')]]=$b_qty[csf('id')];
								$booking_qty_arr[$b_qty[csf('po_id')]][$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$desc][$b_qty[csf('item_size')]]+=$b_qty[csf('wo_qnty')];
							}
							
						}
					
					}
				}
				//print_r($booking_qty_arr);
				if($booking_without_order==0)
				{
					$sql_dtls="select b.id,b.mst_id,b.item_group_id, b.item_description, c.po_breakdown_id as order_id, b.gmts_color_id, b.gmts_size_id,  b.item_color, b.item_size, b.brand_supplier, b.sensitivity, c.quantity as receive_qnty 
					from inv_receive_master a,inv_trims_entry_dtls b, order_wise_pro_details c 
					where c.dtls_id=b.id and c.entry_form=24 and b.mst_id=a.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.booking_no in ('".$booking_nos."')";
				}
				else
				{
					$sql_dtls="select b.id,b.mst_id,b.item_group_id, b.item_description, b.order_id, b.gmts_color_id, b.gmts_size_id, b.item_color, b.item_size, b.brand_supplier, b.sensitivity, b.receive_qnty 
					from inv_receive_master a,inv_trims_entry_dtls b where a.entry_form=24 and b.mst_id=a.id and b.status_active=1 and b.is_deleted=0 and a.booking_no in ('".$booking_nos."')";
				}
				//echo $sql_dtls;
				$po_breakdown_ids='';
				$po_breakdown_ids='';
				$sql_result=sql_select($sql_dtls);

				foreach($sql_result as $rows)
				{
					$key=$rows[csf('order_id')]."**".$rows[csf('item_group_id')]."**".trim(strtolower($rows[csf('item_description')]))."**".$rows[csf('gmts_color_id')]."**".$rows[csf('gmts_size_id')]."**".$rows[csf('item_color')]."**".trim($rows[csf('item_size')])."**".trim($rows[csf('brand_supplier')]."**".$rows[csf('sensitivity')]);
					if($rows[csf('mst_id')] < $dataArray[0][csf('id')])
					{
						$prev_qty_arr[$key]+=$rows[csf('receive_qnty')];
					}
					$tot_qty_arr[$key]+=$rows[csf('receive_qnty')];
					$po_breakdown_ids .=$rows[csf('order_id')].',';
				}
				/* echo "<pre>";
				print_r($prev_qty_arr); */
				$po_breakdown_ids=implode(",",array_unique(explode(",",chop($po_breakdown_ids,','))));
				$rcvRtn_qty_sql = "SELECT b.po_breakdown_id, sum( b.quantity ) as recv_return_qty 
				from product_details_master a, inv_transaction t, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d
				where a.id=b.prod_id and a.id=t.prod_id and t.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.id=b.prod_id and  b.po_breakdown_id in ($po_breakdown_ids) and a.item_category_id=4 and b.entry_form in(49) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  
				group by b.po_breakdown_id";

				$totalRcvRtnQty_arr=array();
				$rcvRtn_qtyArray=sql_select($rcvRtn_qty_sql); 
				foreach($rcvRtn_qtyArray as $row)
				foreach($rcvRtn_qtyArray as $row)
				{ 
					$totalRcvRtnQty_arr[$row[csf('po_breakdown_id')]]=$row[csf('recv_return_qty')];
				}
				
				$po_sql="select a.JOB_NO, a.BUYER_NAME, a.STYLE_REF_NO, b.ID, b.PO_NUMBER from wo_po_details_master a, wo_po_break_down b 
				where a.id=b.job_id and a.status_active=1 and b.status_active=1 and b.id in($po_breakdown_ids)";
				$po_sql_result=sql_select($po_sql);
				$po_data_arr=array();
				foreach($po_sql_result as $val)
				{
					$po_data_arr[$val["ID"]]["JOB_NO"]=$val["JOB_NO"];
					$po_data_arr[$val["ID"]]["BUYER_NAME"]=$val["BUYER_NAME"];
					$po_data_arr[$val["ID"]]["STYLE_REF_NO"]=$val["STYLE_REF_NO"];
					$po_data_arr[$val["ID"]]["PO_NUMBER"]=$val["PO_NUMBER"];
				}			
					
				$i=1; 
				$mst_id=$dataArray[0][csf('id')];
				if($booking_without_order==0)
				{
					$sql_dtls="select b.item_group_id, b.item_description, c.po_breakdown_id as order_id, b.gmts_color_id, b.gmts_size_id, b.item_color, b.item_size, b.sensitivity, b.brand_supplier, b.order_uom, b.cons_uom, max(b.room_no) as room_no, max(b.rack_no) as rack_no, max(b.self_no) as self_no, max(b.box_bin_no) as box_bin_no, sum(b.cons_qnty) as cons_qnty, sum(c.quantity) as receive_qnty, avg(b.rate) as rate, sum(c.quantity*b.rate) as amount, sum(c.reject_qty) as reject_receive_qnty, b.remarks, listagg(cast(b.payment_over_recv as varchar(4000)),',') within group(order by b.id) as payment_over_recv
					from inv_trims_entry_dtls b, order_wise_pro_details c 
					where c.dtls_id=b.id and c.entry_form=24 and b.mst_id='$mst_id' and b.status_active='1' and b.is_deleted='0'
					group by b.item_group_id, b.item_description, c.po_breakdown_id, b.gmts_color_id, b.gmts_size_id, b.item_color, b.item_size, b.sensitivity, b.brand_supplier, b.order_uom, b.cons_uom, b.remarks";
					
				}
				else
				{
					$sql_dtls="select b.item_group_id, b.item_description, b.gmts_color_id, b.gmts_size_id, b.item_color, b.item_size, b.sensitivity, b.brand_supplier, b.cons_uom, b.order_uom, b.room_no, b.rack_no, b.self_no, b.box_bin_no, b.order_id, b.cons_qnty, b.receive_qnty, b.rate, b.amount, b.reject_receive_qnty, b.remarks, b.payment_over_recv
					from inv_trims_entry_dtls b  
					where b.mst_id='$mst_id' and b.status_active='1' and b.is_deleted='0'";
				}
				//echo $sql_dtls;
				
				$lib_room_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "room_id","floor_room_rack_name" ); 
				$lib_rack_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "rack_id","floor_room_rack_name" ); 
				$lib_shelf_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.shelf_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "shelf_id","floor_room_rack_name" ); 
				$lib_bin_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.bin_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "bin_id","floor_room_rack_name" ); 
				$sql_result=sql_select($sql_dtls);
				//echo "test3";die;
				$pay_yes_no=array(0=>"Yes", 1=>"No");
				foreach($sql_result as $row)
				{
					$key=$row[csf('order_id')]."**".$row[csf('item_group_id')]."**".trim(strtolower($row[csf('item_description')]))."**".$row[csf('gmts_color_id')]."**".$row[csf('gmts_size_id')]."**".$row[csf('item_color')]."**".trim($row[csf('item_size')])."**".trim($row[csf('brand_supplier')])."**".$row[csf('sensitivity')];
					if ($i%2==0) $bgcolor="#E9F3FF";							
					else $bgcolor="#FFFFFF";

					$order_no=$row[csf('order_id')];
					$po_number = $po_data_arr[$row[csf('order_id')]]["PO_NUMBER"];
					$po_number_job = $po_data_arr[$row[csf('order_id')]]["JOB_NO"];
					$po_number_buyer = $po_data_arr[$row[csf('order_id')]]["BUYER_NAME"];
					$style_no = $po_data_arr[$row[csf('order_id')]]["STYLE_REF_NO"];
					
					$descp=trim(strtolower($row[csf('item_description')]));
					if($dataArray[0][csf('receive_basis')]==2)
					{
						if($dataArray[0][csf('booking_without_order')]==0)
						{
							$woorder_qty=$booking_qty_arr[$order_no][$row[csf('item_group_id')]][$descp][$row[csf('gmts_color_id')]][$row[csf('gmts_size_id')]][$row[csf('item_color')]][trim($row[csf('item_size')])][trim($row[csf('brand_supplier')])][$row[csf('sensitivity')]];
							
						}
						else
						{
							$woorder_qty=$booking_qty_arr[$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$descp];	
						}
					}
					else if ($dataArray[0][csf('receive_basis')]==1)
					{
						$woorder_qty=$booking_qty_arr[$order_no][$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$descp][$row[csf('item_size')]];
					}
					else
					{
						$woorder_qty=$booking_qty_arr[$order_no][$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$descp];	
					}
					
					if($buyer_id=='') $buyer_id=$buyer_name_library[$bid]; else $buyer_id.=",".$buyer_name_library[$bid];
					$buyer_id=$buyer_name_library[$po_number_buyer];
					$rcvRtnQty=$totalRcvRtnQty_arr[$row[csf('order_id')]];
					$prevRcvQty=$prev_qty_arr[$key]-$rcvRtnQty;
					$uom_check_arr[$row[csf('order_uom')]]=$row[csf('order_uom')];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td><? echo $i; ?></td>
						<td><p><? echo $po_number_job.'<br>'.$po_number.'<br>'.$buyer_id.'<br>'.$style_no;//$item_category[4]; ?></p></td>
						<td><p><? echo $item_library[$row[csf('item_group_id')]] ?></p></td>
						<td><p><? echo $row[csf('item_description')]; ?></p></td>
						<td><p><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></p></td>
						<td align="right"><div style="word-wrap:break-word;">
							<? //$woorder_qty=$booking_qty_arr[$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$row[csf('item_description')]];
							//$total_woorder_qty+=$woorder_qty;
							echo number_format($woorder_qty,2,".","");; 
							?>
						</div></td>
						<td align="right"><? echo number_format($prevRcvQty,2,".",""); ?></td>
						<td align="right"><? echo number_format($row[csf('receive_qnty')],2,".",""); ?></td>
						<td align="right"><? echo number_format($prevRcvQty+$row[csf('receive_qnty')],2,".",""); ?></td>
					
						<td align="right"><? echo number_format($woorder_qty-($prevRcvQty+$row[csf('receive_qnty')]),4,'.',''); ?></td>
						<td align="right"><? echo number_format($row[csf('rate')],4,'.',''); ?></td>
						<td align="right" title="<?= $row[csf('amount')];?>"><? echo number_format($row[csf('amount')],2,'.',''); ?></td>
						<td><? echo $row[csf('remarks')]; ?></td>
						<td align="right"><? echo $lib_room_arr[$row[csf('room_no')]]; ?></td>
						<td align="right"><? echo $lib_rack_arr[$row[csf('rack_no')]]; ?></td>
						<td align="right"><? echo $lib_shelf_arr[$row[csf('self_no')]]; ?></td>
						<td align="right"><? echo $lib_bin_arr[$row[csf('box_bin_no')]]; ?></td>
						<td align="center"><p>
						<?
						$payment_over_rcv_arr=explode(",",$row[csf('payment_over_recv')]);
						$payment_over_rcv=0;
						foreach($payment_over_rcv_arr as $pay_rcv)
						{
							$payment_over_rcv=$pay_rcv;
						}
						echo $pay_yes_no[$payment_over_rcv]; 
						?></p></td>
					
					</tr>
					<?
					$i++;
					$tot_woorder_qty+=$woorder_qty;
					$tot_prevRcvQty+=$prevRcvQty;
					$tot_rec_qty+=$row[csf('receive_qnty')];
					$tot_amount+=$row[csf('amount')];
					$tot_reject_qty+=$row[csf('reject_receive_qnty')];
					$col_tot_rcv+=$prevRcvQty+$row[csf('receive_qnty')];
				}
				?>
				<tr bgcolor="#dddddd">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td colspan="2" align="right"><b>Total :</b></td>
					<td align="right"><? if(count($uom_check_arr)<2) echo number_format($tot_woorder_qty,2); ?></td>
					<td align="right"><? if(count($uom_check_arr)<2)echo number_format($tot_prevRcvQty,2); ?></td>
					<td align="right"><? if(count($uom_check_arr)<2)echo number_format($tot_rec_qty,2); ?></td>
					<td align="right"><? if(count($uom_check_arr)<2)echo number_format($col_tot_rcv,2); ?></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td align="right" title="<?= $tot_amount;?>"><? echo number_format($tot_amount,2,'.',''); ?></td>
					<td align="center">&nbsp;</td>
					<td align="center">&nbsp;</td>
					<td align="center">&nbsp;</td>
					<td align="center">&nbsp;</td>
					<td align="center">&nbsp;</td>
					<td align="center">&nbsp;</td>
				</tr>
			</table>
			<br>
			<table>
				<tr>
					<?
					if($dataArray[0][csf("is_audited")]==1)
					{
						?>
						<td><strong><? echo 'Audited By &nbsp;'.$user_name[$dataArray[0][csf("audit_by")]].'&nbsp;At&nbsp;'.$dataArray[0][csf("audit_date")]; ?></strong></td>
						<?
					}
					?>
				</tr>
			</table>
		</div>
    </div>     
    <?
    echo signature_table(35, $data[0], "1030px");
    exit();
}


?>


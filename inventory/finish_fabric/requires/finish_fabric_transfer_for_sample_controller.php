<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
	$permission=$_SESSION['page_permission'];

	$data=$_REQUEST['data'];
	$action=$_REQUEST['action'];

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1",'id','color_name');
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");

	if ($action=="upto_variable_settings")
	{
		extract($_REQUEST);
		/*echo "select store_method from variable_settings_inventory where company_name='$cbo_company_id' and item_category_id=13 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1";die;*/
		echo $variable_inventory=return_field_value("store_method","variable_settings_inventory","company_name='$cbo_company_id' and item_category_id=2 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
		exit();
	}

	if ($action=="load_room_rack_self_bin")
	{
		$explodeData = explode('*', $data);
		$customFnc = array( 'store_update_upto_disable()' ); // not necessarily an array, see manual quote
		array_splice( $explodeData, 11, 0, $customFnc ); // splice in at position 3
		$data=implode('*', $explodeData);
		//echo $data;
		load_room_rack_self_bin("requires/finish_fabric_transfer_for_sample_controller",$data);
	}

	if ($action=="load_drop_down_location")
	{
		$data=explode("_",$data);
		echo create_drop_down( "cbo_location", 160, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_room_rack_self_bin('requires/finish_fabric_transfer_for_sample_controller*2','store','from_store_td',$('#cbo_company_id').val(),this.value),'','','','','','',fnc_item_blank(1); " );
		exit();
	}

	if ($action=="load_drop_down_location_to")
	{
		$data=explode("_",$data);
		echo create_drop_down( "cbo_location_to", 160, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_room_rack_self_bin('requires/finish_fabric_transfer_for_sample_controller*2*cbo_store_name_to', 'store','to_store_td', $('#cbo_company_id_to').val(),this.value);" );
		exit();
	}

	if ($action=="load_body_part")
	{
		$data=explode("_",$data);
		$order_id = $data[0];
		$product_id = $data[1];
		$transfer_criteria = $data[2];

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


	if ($action=="itemTransfer_popup")
	{
		echo load_html_head_contents("Item Transfer Info", "../../../", 1, 1,'','','');
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
							<th width="200">Search By</th>
							<th width="200" id="search_by_td_up">Please Enter Transfer ID</th>
							<th width="220">Date Range</th>
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
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />&nbsp;To&nbsp;
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
							</td>
							<td>
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_transfer_search_list_view', 'search_div', 'finish_fabric_transfer_for_sample_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
							</td>
						</tr>
						<tr>                  
							<td align="center" height="40" valign="middle" colspan="4"><? echo load_month_buttons(1);  ?></td>
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
	$date_form=trim($data[3]);
	$date_to =trim($data[4]);
	
	if($date_form !="" && $date_to !="")
	{
		if($db_type==0) $date_cond=" and transfer_date between '".change_date_format($date_form,"YYYY-MM-DD")."' and '".change_date_format($date_to,"YYYY-MM-DD")."'"; 
		else $date_cond=" and transfer_date between '".change_date_format($date_form,"","",-1)."' and '".change_date_format($date_to,"","",-1)."'";
	}
	
	if($search_by==1)
		$search_field="transfer_system_id";	
	else
		$search_field="challan_no";
	
	if($db_type==0) $year_field="YEAR(insert_date)"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY')";
	else $year_field="";//defined Later
	
	$sql="select id, $year_field as year, transfer_prefix_number, transfer_system_id, remarks, company_id, transfer_date, transfer_criteria, item_category,location_id,to_location_id from inv_item_transfer_mst where item_category=2 and company_id=$company_id and $search_field like '$search_string' and transfer_criteria in(6,7,8) and entry_form = 306 and status_active=1 and is_deleted=0 $date_cond order by id desc";
	//echo $sql;die;
	
	$arr=array(3=>$company_arr,5=>$item_transfer_criteria,6=>$item_category);

	echo  create_list_view("tbl_list_search", "Transfer ID,Year,Remarks,Company,Transfer Date,Transfer Criteria,Item Category", "70,60,100,120,90,140","760","250",0, $sql, "js_set_value", "id", "", 1, "0,0,0,company_id,0,transfer_criteria,item_category", $arr, "transfer_prefix_number,year,remarks,company_id,transfer_date,transfer_criteria,item_category", '','','0,0,0,0,3,0,0');
	
	exit();
}

if($action=='populate_data_from_transfer_master')
{
	$data_array=sql_select("select transfer_system_id, remarks, company_id, location_id, transfer_date, transfer_criteria, item_category, to_company, to_location_id from inv_item_transfer_mst where id='$data'");

	$company_id=$data_array[0][csf("company_id")];
	$variable_inventory_sql=sql_select("select store_method, rack_balance from variable_settings_inventory  where company_name=$company_id and item_category_id=2 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	$store_method=$variable_inventory_sql[0][csf("store_method")];


	$to_company=$data_array[0][csf("to_company")];
	$variable_inventory_sql_to=sql_select("select store_method, rack_balance from variable_settings_inventory  where company_name=$to_company and item_category_id=2 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	$store_method_to=$variable_inventory_sql_to[0][csf("store_method")];

	foreach ($data_array as $row)
	{ 
		echo "load_drop_down('requires/finish_fabric_transfer_for_sample_controller','".$row[csf("company_id")]."', 'load_drop_down_location', 'from_location_td' );\n";
		echo "load_drop_down('requires/finish_fabric_transfer_for_sample_controller','".$row[csf("to_company")]."', 'load_drop_down_location_to', 'to_location_td' );\n";

		echo "document.getElementById('store_update_upto').value 			= '".$store_method."';\n";
		echo "document.getElementById('store_update_upto_to').value 		= '".$store_method_to."';\n";
		echo "store_update_upto_disable();\n";
		
		echo "document.getElementById('update_id').value 					= '".$data."';\n";
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("transfer_system_id")]."';\n";
		echo "document.getElementById('cbo_transfer_criteria').value 		= '".$row[csf("transfer_criteria")]."';\n";
		
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_location').value 				= '".$row[csf("location_id")]."';\n";
		echo "document.getElementById('txt_master_remarks').value 			= '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('txt_transfer_date').value 			= '".change_date_format($row[csf("transfer_date")])."';\n";
		echo "document.getElementById('cbo_company_id_to').value 			= '".$row[csf("to_company")]."';\n";
		echo "document.getElementById('previous_to_company_id').value 		= '".$row[csf("to_company")]."';\n";
		echo "document.getElementById('cbo_location_to').value 				= '".$row[csf("to_location_id")]."';\n";
		echo "document.getElementById('cbo_item_category').value 			= '".$row[csf("item_category")]."';\n";
		
		echo "$('#cbo_company_id').attr('disabled','disabled');\n";
		echo "$('#cbo_location').attr('disabled','disabled');\n";
		echo "$('#cbo_company_id_to').attr('disabled','disabled');\n";
		echo "$('#cbo_location_to').attr('disabled','disabled');\n";
		echo "$('#cbo_transfer_criteria').attr('disabled','disabled');\n";
		echo "store_update_upto_disable();\n";
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_yarn_transfer_entry',1,1);\n"; 
		exit();
		
	}
}



if ($action=="itemDescription_popup")
{
	echo load_html_head_contents("Item Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 
	<script>
		function js_set_value(data)
		{
			$('#product_id').val(data);
			parent.emailwindow.hide();
		}
	</script>

</head>

<body>
	<div align="center">
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:910px;margin-left:10px">
				<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="900" class="rpt_table" border="1" rules="all" align="center">
					<thead>
						<th width="150">Order</th>
						<th width="150">Booking</th>
						<th width="150">Batch No.</th>
						<th width="150">Buyer</th>
						<th width="180" id="search_by_td_up">Item Details</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
							<input type="hidden" name="product_id" id="product_id" class="text_boxes" value="">
						</th>
					</thead>
					<tr class="general">
						<td>
							<input type="text" style="width:120px;" class="text_boxes"  name="txt_order_no" id="txt_order_no" value="<? if($transfer_criteria==4) echo $txt_from_order_no; ?>" <? if($transfer_criteria==4) echo "disabled"; else echo ""; ?> />
							<input type="hidden"  name="txt_order_id" id="txt_order_id" value="<? if($transfer_criteria==4) echo $txt_from_order_id; ?>" />
						</td>
						<td>
							<input type="text" style="width:120px;" class="text_boxes"  name="txt_batch_no" id="txt_batch_no" />
						</td>
						<td>
							<input type="text" style="width:120px;" class="text_boxes"  name="txt_booking_no" id="txt_booking_no" />
						</td>

						<td>
							<?
							echo create_drop_down( "cbo_buyer_name", 140, "select a.id, a.buyer_name from lib_buyer a, lib_buyer_party_type b, lib_buyer_tag_company c where a.id=b.buyer_id and a.id=c.buyer_id and b.party_type=1 and c.tag_company='$cbo_company_id' and a.status_active=1 and a.is_deleted =0 order by a.buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" ); 
							?>
						</td>

						<td>
							<input type="text" style="width:150px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td>
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_order_no').value+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('txt_batch_no').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $cbo_company_id; ?>+'_'+<? echo $cbo_store_name; ?>+'_'+<? echo $cbo_floor; ?>+'_'+<? echo $cbo_room; ?>+'_'+<? echo $txt_rack; ?>+'_'+<? echo $txt_shelf; ?>+'_'+document.getElementById('txt_order_id').value, 'create_product_search_list_view', 'search_div', 'finish_fabric_transfer_for_sample_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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

if($action=='create_product_search_list_view')
{
	$data = explode("_",$data);
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	
	$txt_order_no 	= str_replace("'","",$data[0]);
	$txt_batch_no 	= str_replace("'","",$data[1]);
	$txt_booking_no = str_replace("'","",$data[2]);
	$cbo_buyer_name = str_replace("'","",$data[3]);
	$search_string	= trim(str_replace("'","",$data[4]));
	$company_id 	= str_replace("'","",$data[5]);
	$cbo_store_name	= str_replace("'","",$data[6]);
	$cbo_floor		= str_replace("'","",$data[7]);
	$cbo_room		= str_replace("'","",$data[8]);
	$cbo_rack		= str_replace("'","",$data[9]);
	$txt_shelf		= str_replace("'","",$data[10]);
	$order_id		= trim(str_replace("'","",$data[11]));
	
	//$ord_cond="";
	//if($order_id!="") $ord_cond=" and id=$order_id";
	//$orderNumber=return_library_array( "select id from wo_po_break_down where po_number='$txt_order_no' and status_active=1 and is_deleted = 0 $ord_cond", "id", "id"  );
	$product_ids="";;
	if($db_type==0) $select_prod_field=" group_concat(id) as id"; else $select_prod_field="listagg(cast(id as varchar(4000)),',') within group(order by id) as id";
	if($search_string!="") $product_ids=return_field_value("$select_prod_field","product_details_master","product_name_details ='$search_string'","id");
	
	$sql_cond="";
	if($order_id!="") 			$sql_cond =" and c.po_breakdown_id =$order_id";
	if($product_ids!="") 		$sql_cond .=" and b.prod_id in($product_ids)";
	if($txt_order_no != "") 	$sql_cond .=" and d.po_number = '$txt_order_no'";
	if($txt_booking_no != "") 	$sql_cond .=" and a.booking_no = '$txt_booking_no'";
	if($txt_batch_no != "") 	$sql_cond .=" and a.batch_no = '$txt_batch_no'";
	if($cbo_buyer_name > 0) 	$sql_cond .=" and e.buyer_name ='$cbo_buyer_name'";	
	if($cbo_floor  > 0) 		$sql_cond .=" and b.floor_id ='$cbo_floor'";
	if($cbo_room  > 0) 			$sql_cond .=" and b.room ='$cbo_room'";
	if($cbo_rack  > 0) 			$sql_cond .=" and b.rack ='$cbo_rack'";
	if($txt_shelf  > 0) 		$sql_cond .=" and b.self ='$txt_shelf'";
	
	//echo $sql_cond.jahid;die;
	
	$sql ="select b.company_id, b.pi_wo_batch_no as batch_id, e.buyer_name as buyer_id, c.quantity, c.po_breakdown_id as order_id, c.prod_id, b.store_id, b.floor_id, b.room,  b.rack, b.self, a.batch_no, a.booking_no_id as booking_id, a.booking_no, d.po_number
	from pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e
	where a.id=b.pi_wo_batch_no and b.id = c.trans_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and b.item_category=2 and c.entry_form in(37,14) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and b.pi_wo_batch_no>0 and b.company_id=$company_id and b.store_id = '$cbo_store_name' $sql_cond group by b.company_id, b.pi_wo_batch_no, e.buyer_name, c.quantity, c.po_breakdown_id, c.prod_id, b.store_id, b.floor_id, b.room,  b.rack, b.self, a.batch_no, a.booking_no_id, a.booking_no, d.po_number,b.fabric_shade";
	//echo $sql;//die;
	$result = sql_select($sql);
	//echo "<pre>";
	//print_r($result);die;
	$order_data_array=array();
	foreach($result as $row )
	{
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['item_details']=$row[csf('product_name_details')];
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['color']=$row[csf('color')];
		
		//$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]]['stock_qty']		+= $row[csf('quantity')];
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['company_id']=$row[csf('company_id')];
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['buyer_id']=$row[csf('buyer_id')];
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['booking_id']=$row[csf('booking_id')];
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['booking_no']=$row[csf('booking_no')];
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['booking_no_batch']=$row[csf('booking_no')];
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['batch_id']=$row[csf('batch_id')];
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['batch_no']=$row[csf('batch_no')];
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['store_id']=$row[csf('store_id')];
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['floor_id']=$row[csf('floor_id')];
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['room']=$row[csf('room')];
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['rack']=$row[csf('rack')];
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['self']=$row[csf('self')];
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['po_number']=$row[csf('po_number')];
		
		$orderidArr[$row[csf('order_id')]]=$row[csf('order_id')];
		$prodidArr[$row[csf('prod_id')]]=$row[csf('prod_id')];
		$batchidArr[$row[csf('batch_id')]]=$row[csf('batch_id')];
	}
	
	$sql_stock=sql_select("select b.po_breakdown_id as order_id, a.prod_id, a.pi_wo_batch_no,
		sum(case when a.transaction_type in(1,4,5) and b.trans_type in(1,4,5) then b.quantity end) as rcv_qty,
		sum(case when a.transaction_type in(2,3,6) and b.trans_type in(2,3,6) then b.quantity end) as issue_qty  
		from inv_transaction a, order_wise_pro_details b
		where a.id=b.trans_id and b.entry_form in(14,15,18,37,46,52) and a.store_id = '$cbo_store_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=2  and a.company_id=$company_id and b.po_breakdown_id in(".implode(",",$orderidArr).") and a.prod_id in(".implode(",",$prodidArr).") and a.pi_wo_batch_no in(".implode(",",$batchidArr).")
		group by b.po_breakdown_id, a.prod_id, a.pi_wo_batch_no");
	//  --and b.entry_form in(14,15,37,52)   and b.entry_form in(18,46)  and a.transaction_type in(1,2,3,4,5,6) and b.trans_type in (1,2,3,4,5,6)
	foreach($sql_stock as $value)
	{
		$stock_data_arrray[$value[csf('order_id')]][$value[csf('prod_id')]][$value[csf('pi_wo_batch_no')]]['rcv_qty']=$value[csf('rcv_qty')];
		$stock_data_arrray[$value[csf('order_id')]][$value[csf('prod_id')]][$value[csf('pi_wo_batch_no')]]['issue_qty']=$value[csf('issue_qty')];
	}
	
	$prod_sql=sql_select("select id, product_name_details, color from product_details_master where id in(".implode(",",$prodidArr).")");
	$prod_data=array();
	foreach($prod_sql as $row)
	{
		$prod_data[$row[csf("id")]]["product_name_details"]=$row[csf("product_name_details")];
		$prod_data[$row[csf("id")]]["color"]=$row[csf("color")];
	}
	
	
	//"select id ,po_number from wo_po_break_down where status_active=1 and is_deleted =0 and id in(".implode(",",$orderidArr).")";
	
	?>
	<div style="width:1000px;" align="center">  
		<table cellspacing="0" width="100%" class="rpt_table" id="" rules="all" align="center">
			<thead>
				<tr>
					<th width="35">SL</th>
					<th width="80">Order</th>
					<th width="80">Item ID</th>
					<th width="120">Company</th>
					<th width="120">Buyer</th>
					<th width="200">Item Details</th>
					<th width="90">Color</th>
					<th width="90">Booking</th>
					<th width="90">Batch</th>
					<th>Stock</th>
				</tr>
			</thead>
		</table>
	</div>
	<div style="width:1000px; overflow-y:scroll; max-height:250px;" align="center">  
		<table cellspacing="0" width="980" class="rpt_table" id="tbl_list_search" rules="all" align="center"  style="margin-bottom: 5px; word-break:break-all">
			<tbody>
				<? 
				$i=1;
				foreach($order_data_array as $order_id => $order_data_array)
				{
					foreach($order_data_array as $item_ids => $item_val)
					{
						foreach($item_val as $batch_ids => $row)
						{
							$stock = $stock_data_arrray[$order_id][$item_ids][$batch_ids]['rcv_qty'] - $stock_data_arrray[$order_id][$item_ids][$batch_ids]['issue_qty'];
							if($stock>0)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";		
								?>
								<tr  bgcolor="<? echo $bgcolor; ?>" valign="middle"  style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $order_id."_".$item_ids."_".$row['company_id']."_".$row['store_id']."_".$row['floor_id']."_".$row['room']."_".$row['rack']."_".$row['self']."_".$batch_ids."_".$row['batch_no']; ?>')" >
									<td width="35" align="center"><? echo $i; ?></td>
									<td width="80" title="<? echo $order_id; ?>"><? echo $row['po_number']; ?></td>
									<td width="80"><? echo $item_ids; ?></td>
									<td width="120"><? echo $company_arr[$row['company_id']]; ?></td>
									<td width="120"><? echo $buyer_library[$row['buyer_id']]; ?></td>
									<td width="200"><? echo $prod_data[$item_ids]["product_name_details"];?></td>
									<td width="90"><? echo $color_arr[$prod_data[$item_ids]["color"]]; ?></td>
									<td width="90"><? echo $row['booking_no_batch']; ?></td>
									<td width="90"><? echo $row['batch_no']; ?></td>
									<td><? echo $stock; ?></td>
								</tr>
								<? 
								$i++;
							}
						}
					}
				}
				?>
			</tbody>
		</table>
	</div>
	<?
	exit();
}


if($action=='populate_data_from_product_master')
{
	$data = explode("_",$data);
	$transfer_criteria=$data[10];
	$order_name_arr=return_library_array( "select id ,po_number from wo_po_break_down where status_active=1 and is_deleted =0 and id in($data[0])",'id','po_number');
	$sql_stock=sql_select("select b.po_breakdown_id as order_id, a.prod_id, a.pi_wo_batch_no,
		sum(case when a.transaction_type in(1,4,5) and b.trans_type in(1,4,5) then b.quantity end) as rcv_qty,
		sum(case when a.transaction_type in(2,3,6) and b.trans_type in(2,3,6) then b.quantity end) as issue_qty  
		from inv_transaction a, order_wise_pro_details b
		where a.id=b.trans_id and b.entry_form in(14,15,18,37,46,52) and a.store_id = $data[3] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=2  and a.company_id=$data[2] and b.po_breakdown_id=$data[0] and a.prod_id=$data[1] and a.pi_wo_batch_no=$data[8]
		group by b.po_breakdown_id, a.prod_id, a.pi_wo_batch_no");
	//  --and b.entry_form in(14,15,37,52)   and b.entry_form in(18,46) and a.transaction_type in(1,2,3,4,5,6) and b.trans_type in (1,2,3,4,5,6)
	foreach($sql_stock as $value)
	{
		$stock_data_arrray[$value[csf('order_id')]][$value[csf('prod_id')]][$value[csf('pi_wo_batch_no')]]['rcv_qty']=$value[csf('rcv_qty')];
		$stock_data_arrray[$value[csf('order_id')]][$value[csf('prod_id')]][$value[csf('pi_wo_batch_no')]]['issue_qty']=$value[csf('issue_qty')];
	}
	
	$sql ="select  a.company_id, b.pi_wo_batch_no as batch_id, c.quantity, c.po_breakdown_id as order_id, a.id as prod_id, a.product_name_details, a.color, a.current_stock , a.avg_rate_per_unit, b.store_id, b.floor_id, b.room, b.rack, b.self, a.unit_of_measure as uom , b.fabric_shade
	from product_details_master a, inv_transaction b, order_wise_pro_details c
	where a.id=b.prod_id and b.id = c.trans_id and b.item_category=2 and c.entry_form in(37,14) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and b.pi_wo_batch_no>0 and b.company_id=$data[2] and b.store_id = $data[3] and c.po_breakdown_id=$data[0] and b.prod_id=$data[1] and b.pi_wo_batch_no=$data[8]";
	
	$data_array=sql_select($sql);
	foreach ($data_array as $row)
	{ 
		if($row[csf("floor_id")]) $floor_id=$row[csf("floor_id")]; else $floor_id=0;
		if($row[csf("room")]) $room=$row[csf("room")]; else  $room=0;
		if($row[csf("rack")]) $rack=$row[csf("rack")]; else  $rack=0;
		if($row[csf("self")]) $self=$row[csf("self")]; else  $self=0;

		$stockQty = $stock_data_arrray[$row[csf("order_id")]][$row[csf("prod_id")]][$row[csf("batch_id")]]['rcv_qty'] - $stock_data_arrray[$row[csf("order_id")]][$row[csf("prod_id")]][$row[csf("batch_id")]]['issue_qty'];
		echo "document.getElementById('txt_from_order_id').value 			= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('txt_from_order_no').value 			= '".$order_name_arr[$row[csf("order_id")]]."';\n";
		if($transfer_criteria==2)
		{
			echo "document.getElementById('txt_to_order_id').value 			= '".$row[csf("order_id")]."';\n";
			echo "document.getElementById('txt_to_order_no').value 			= '".$order_name_arr[$row[csf("order_id")]]."';\n";
		}
		echo "document.getElementById('from_product_id').value 				= '".$row[csf("prod_id")]."';\n";
		echo "document.getElementById('txt_item_desc').value 				= '".$row[csf("product_name_details")]."';\n";
		echo "document.getElementById('txt_current_stock').value 			= '".$stockQty."';\n";
		echo "document.getElementById('hidden_current_stock').value 		= '".$stockQty."';\n";
		echo "document.getElementById('batch_id').value 					= '".$row[csf("batch_id")]."';\n";
		echo "document.getElementById('txt_batch_no').value 				= '".$data[9]."';\n";
		//echo "document.getElementById('txt_current_stock').value 			= '".$row[csf("current_stock")]."';\n";
		//echo "document.getElementById('hidden_current_stock').value 		= '".$row[csf("current_stock")]."';\n";
		//echo "document.getElementById('txt_rate').value 					= '".$row[csf("avg_rate_per_unit")]."';\n";
		
		echo "document.getElementById('txt_rate').value 					= '".$row[csf("avg_rate_per_unit")]."';\n";
		echo "document.getElementById('hide_color_id').value 				= '".$row[csf("color")]."';\n";
		echo "document.getElementById('txt_color').value 					= '".$color_arr[$row[csf("color")]]."';\n";
		echo "document.getElementById('cbo_uom').value 						= '".$row[csf("uom")]."';\n";
		echo "document.getElementById('cbo_fabric_shade').value 			= '".$row[csf("fabric_shade")]."';\n";
		
		if($floor_id !=0)
		{
			echo "load_room_rack_self_bin('requires/finish_fabric_transfer_for_sample_controller*2', 'floor','floor_td', '".$data[2]."','"."','".$data[3]."',this.value);\n";
			echo "document.getElementById('cbo_floor').value 				= '".$floor_id."';\n";
		}
		if($room !=0)
		{
			echo "load_room_rack_self_bin('requires/finish_fabric_transfer_for_sample_controller*2', 'room','room_td', '".$data[2]."','"."','".$data[3]."','".$floor_id."',this.value);\n";
			echo "document.getElementById('cbo_room').value 				= '".$room."';\n";
		}
		if($rack !=0)
		{
			echo "load_room_rack_self_bin('requires/finish_fabric_transfer_for_sample_controller*2', 'rack','rack_td', '".$data[2]."','"."','".$data[3]."','".$floor_id."','".$room."',this.value);\n";
			echo "document.getElementById('txt_rack').value 				= '".$rack."';\n";
		}
		if($self !=0)
		{
			echo "load_room_rack_self_bin('requires/finish_fabric_transfer_for_sample_controller*2', 'shelf','shelf_td', '".$data[2]."','"."','".$data[3]."','".$floor_id."','".$room."','".$rack."',this.value);\n";
			echo "document.getElementById('txt_shelf').value 				= '".$self."';\n";
		}
	}
	exit();
}


if ($action=="po_search_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST); 
	?> 
	<script> 
		pop_source = '<? echo $pop_source;?>';
		function fn_show_check()
		{
			if(pop_source*1 == 2)
			{
				if( $('#txt_po_no').val()=='' && $('#txt_job_no').val()=='' && $('#cbo_buyer_name').val()*1==0 && $('#txt_internal_ref').val()=='')
				{
					alert("Please Enter at Least One Search");return;
				}
				show_list_view ( $('#txt_po_no').val()+'_'+$('#txt_job_no').val()+'_'+<? echo $cbo_company_id_to; ?>+'_'+$('#cbo_buyer_name').val()+'_'+$('#txt_internal_ref').val()+'_'+$('#cbo_search_category').val()+'_'+pop_source, 'create_po_search_list_view', 'search_div', 'finish_fabric_transfer_for_sample_controller', 'setFilterGrid(\'tbl_list_search\',-1);hidden_field_reset();');
			}
			else 
			{
				if( $('#txt_po_no').val()=='' && $('#txt_batch_no').val()=='' && $('#txt_job_no').val()=='' && $('#cbo_buyer_name').val()*1==0 && $('#txt_internal_ref').val()=='')
				{
					alert("Please Enter at Least One Search");return;
				}
				show_list_view ( $('#txt_po_no').val()+'_'+$('#txt_job_no').val()+'_'+<? echo $cbo_company_id_to; ?>+'_'+$('#cbo_buyer_name').val()+'_'+$('#txt_internal_ref').val()+'_'+$('#cbo_search_category').val()+'_'+pop_source+'_'+$('#txt_batch_no').val(), 'create_po_search_list_view', 'search_div', 'finish_fabric_transfer_for_sample_controller', 'setFilterGrid(\'tbl_list_search\',-1);hidden_field_reset();');
			}
			
		}

		function js_set_value( id,name,buyer_name,job_no,style_ref,booking_no,booking_id,batch_no,batch_id) 
		{
			$('#hidden_order_id').val(id);
			$('#hidden_order_no').val(name);
			$('#hidden_buyer_name').val(buyer_name);
			$('#hidden_job_no').val(job_no);
			$('#hidden_style_ref').val(style_ref);
			$('#hidden_booking_no').val(booking_no);
			$('#hidden_booking_id').val(booking_id);
			$('#hidden_batch_no').val(batch_no);
			$('#hidden_batch_id').val(batch_id);
			parent.emailwindow.hide();
		}
		
		function hidden_field_reset()
		{
			$('#hidden_order_id').val('');
			$('#hidden_order_no').val( '' );
			$('#hidden_buyer_name').val('');
			$('#hidden_job_no').val('');
			$('#hidden_style_ref').val('');
			$('#hidden_batch_no').val('');
			$('#hidden_batch_id').val('');
		}
		
	</script>

</head>
<body>
	<div align="center">
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:620px;margin-left:5px">
				<input type="hidden" name="hidden_order_id" id="hidden_order_id" class="text_boxes" value="">
				<input type="hidden" name="hidden_order_no" id="hidden_order_no" class="text_boxes" value="">
				<input type="hidden" name="hidden_buyer_name" id="hidden_buyer_name" class="text_boxes" value="">
				<input type="hidden" name="hidden_job_no" id="hidden_job_no" class="text_boxes" value="">
				<input type="hidden" name="hidden_style_ref" id="hidden_style_ref" class="text_boxes" value="">
				<input type="hidden" name="hidden_booking_no" id="hidden_booking_no" class="text_boxes" value="">
				<input type="hidden" name="hidden_booking_id" id="hidden_booking_id" class="text_boxes" value="">
				<input type="hidden" name="hidden_batch_no" id="hidden_batch_no" class="text_boxes" value="">
				<input type="hidden" name="hidden_batch_id" id="hidden_batch_id" class="text_boxes" value="">
				<table cellpadding="0" cellspacing="0" width="620" class="rpt_table" border="1" rules="all">
					<thead>
						<th  colspan="13">
							<?
							echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --",1 );
							?>
						</th>
					</thead>
					<thead>
						<th>Buyer</th>
						<? 	
						if($pop_source==1)
						{
							?>
							<th>Batch No</th>
							<?
						}
						?>
						<th>PO No</th>
						<th>Job No</th>
						<th>Internal Ref. No</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
							<input type="hidden" name="po_id" id="po_id" value="">
						</th> 
					</thead>
					<tr class="general">
						<td align="center">
							<?
							echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id_to' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $buyer_name, "","" ); 
							?>       
						</td>
						<? 	
						if($pop_source==1)
						{
							?>
							<td>
								<input type="text" style="width:130px;" class="text_boxes" name="txt_batch_no" id="txt_batch_no" />
							</td>
							<?
						}
						?>
						<td align="center">				
							<input type="text" style="width:130px" class="text_boxes"  name="txt_po_no" id="txt_po_no" placeholder="Write" />	
						</td>
						<td align="center">				
							<input type="text" style="width:130px" class="text_boxes"  name="txt_job_no" id="txt_job_no"  placeholder="Write"/>	
						</td>
						<td align="center">				
							<input type="text" style="width:130px" class="text_boxes"  name="txt_internal_ref" id="txt_internal_ref"  placeholder="Write"/>	
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="fn_show_check()" style="width:100px;" />
						</td>
					</tr>
				</table>
				<div id="search_div" style="margin-top:10px"  align="center"></div>	
			</fieldset>
		</form>
	</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}


if($action=="create_po_search_list_view")
{
	$data = explode("_",$data);
	$txt_po_no = trim($data[0]);
	$txt_job_no = trim($data[1]);
	$company_id =$data[2];
	$buyer_id =$data[3];
	$txt_internal_ref = trim($data[4]);
	$search_category =$data[5];
	$pop_source =$data[6];
	//N.B pop_source defines from/or order

	$batch_no =$data[7];
	
	$search_con="";
	if($buyer_id!=0)
		$search_con = " and a.buyer_name=$buyer_id";

	if($txt_po_no!="")
	{
		if($search_category==1) {
			$search_con .=" and b.po_number = '$txt_po_no'"; 
		}
		else if ($search_category==0 || $search_category==4) {
			$search_con .=" and b.po_number like '%$txt_po_no%'"; 
		}
		else if($search_category==2) {
			$search_con .=" and b.po_number like '$txt_po_no%'"; 
		}
		else if($search_category==3) {
			$search_con .=" and b.po_number like '%$txt_po_no'"; 
		}
		else {$search_con .="";}
	}

	if($txt_job_no!="")
	{
		if($search_category==1) {
			$search_con .=" and a.job_no = '$txt_job_no'"; 
		}
		else if ($search_category==0 || $search_category==4) {
			$search_con .=" and a.job_no like '%$txt_job_no%'"; 
		}
		else if($search_category==2) {
			$search_con .=" and a.job_no like '$txt_job_no%'"; 
		}
		else if($search_category==3) {
			$search_con .=" and a.job_no like '%$txt_job_no'"; 
		}
		else {$search_con .="";}
	}

	if($txt_internal_ref!="")
	{
		if($search_category==1) {
			$search_con .=" and b.grouping = '$txt_internal_ref'"; 
		}
		else if ($search_category==0 || $search_category==4) {
			$search_con .=" and b.grouping like '%$txt_internal_ref%'"; 
		}
		else if($search_category==2) {
			$search_con .=" and b.grouping like '$txt_internal_ref%'"; 
		}
		else if($search_category==3) {
			$search_con .=" and b.grouping like '%$txt_internal_ref'"; 
		}
		else {$search_con .="";}
	}

	if($batch_no!="")
	{
		if($search_category==1) {
			$batch_cond_1 =" and f.batch_no = '$batch_no'"; 
		}
		else if ($search_category==0 || $search_category==4) {
			$batch_cond_1 =" and f.batch_no like '%$batch_no%'"; 
		}
		else if($search_category==2) {
			$batch_cond_1 =" and f.batch_no like '$batch_no%'"; 
		}
		else if($search_category==3) {
			$batch_cond_1 =" and f.batch_no like '%$batch_no'"; 
		}
		else {$batch_cond_1 ="";}
	}else{
		$batch_cond_1 = "";
	}

	if($pop_source ==2)
	{
		/*$sql = "select a.job_no, a.style_ref_no, a.buyer_name as buyer_id, d.buyer_name, c.booking_no, e.id as  booking_id, b.file_no, b.id,b.grouping as ref_no, b.po_number, b.pub_shipment_date 
		from wo_po_details_master a, wo_po_break_down b left join wo_booking_dtls c on b.id = c.po_break_down_id and b.job_no_mst= c.job_no left join wo_booking_mst e on c.booking_no = e.booking_no, lib_buyer d
		where a.job_no=b.job_no_mst and a.buyer_name = d.id and a.company_name=$company_id $search_con and c.booking_type in (1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_no, a.style_ref_no, a.buyer_name, d.buyer_name, c.booking_no, b.file_no, b.id,b.grouping, b.po_number, b.pub_shipment_date,a.id,e.id order by a.id desc";*/

		$sql = "select a.job_no, a.style_ref_no, a.buyer_name as buyer_id, d.buyer_name, c.booking_no, e.id as booking_id, b.file_no, b.id,b.grouping as ref_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c, wo_booking_mst e, lib_buyer d where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and b.job_no_mst= c.job_no and c.booking_no=e.booking_no and a.buyer_name=d.id and a.company_name=$company_id $search_con and c.booking_type in (1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_no, a.style_ref_no, a.buyer_name, d.buyer_name, c.booking_no, b.file_no, b.id,b.grouping, b.po_number, b.pub_shipment_date, a.id, e.id";
	}
	else
	{
		$sql ="select a.job_no, a.style_ref_no, a.buyer_name as buyer_id, d.buyer_name, c.booking_no, e.id as booking_id, b.file_no, b.id,b.grouping as ref_no, b.po_number, b.pub_shipment_date, f.batch_no, f.id as batch_id from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c, wo_booking_mst e, lib_buyer d, pro_batch_create_mst f where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and b.job_no_mst=c.job_no and c.booking_no=e.booking_no and a.buyer_name=d.id and a.company_name=$company_id $search_con $batch_cond_1 and c.booking_type in (1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.booking_no = f.booking_no group by a.job_no, a.style_ref_no, a.buyer_name, d.buyer_name, c.booking_no, b.file_no, b.id,b.grouping, b.po_number, b.pub_shipment_date,a.id,e.id, f.batch_no, f.id";
	}

	

	
	?>
	<style type="text/css">
		.word_wrap_break{
			word-wrap: break-word !important;
			word-break: break-all !important;
		}
	</style>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table" align="left">
		<thead>
			<th width="30">SL</th>
			<th width="100">Buyer</th>
			<th width="90">Booking No</th>
			<th width="90">Job No</th>
			<th width="100">Style No</th>
			<th width="100">PO No</th>
			<th width="100">File No</th>
			<th width="90">Ref. No</th>
			<? if($pop_source==1){?>
			<th width="">Batch No</th>
			<?}?>
		</thead>
	</table>
	<div style="width:840px; overflow-y:scroll; max-height:220px;" id="buyer_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table" id="tbl_list_search" >
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
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value('<? echo $selectResult[csf('id')];?>','<? echo $selectResult[csf('po_number')];?>','<? echo $selectResult[csf('buyer_name')];?>','<? echo $selectResult[csf('job_no')];?>','<? echo $selectResult[csf('style_ref_no')];?>','<? echo $selectResult[csf('booking_no')];?>','<? echo $selectResult[csf('booking_id')];?>','<? echo $selectResult[csf('batch_no')];?>','<? echo $selectResult[csf('batch_id')];?>')"> 
					<td width="30" align="center"><?php echo "$i"; ?></td>	
					<td width="100" align="center"><?php echo $selectResult[csf('buyer_name')]; ?></td>	
					<td width="90" align="center"><?php echo $selectResult[csf('booking_no')]; ?></td>	
					<td width="90" align="center"><p><? echo $selectResult[csf('job_no')]; ?></p></td>
					<td width="100" align="center"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
					<td width="100" align="center"><p class="word_wrap_break"><? echo $selectResult[csf('po_number')]; ?></p></td>
					<td width="100" align="center"><p><? echo $selectResult[csf('file_no')]; ?></p></td>
					<td width="90" align="center"><p><? echo $selectResult[csf('ref_no')]; ?></p></td>
					<? if($pop_source==1){?>
					<td width="" align="center"><p><? echo $selectResult[csf('batch_no')]; ?></p></td>
					<?}?>
				</tr>
				<?
				$i++;
			}
			?>
			<input type="hidden" name="txt_po_row_id" id="txt_po_row_id" value="<?php echo $po_row_id; ?>"/>
		</table>
	</div>
	<?
	exit();
}

if ($action=="booking_search_popup")
{
	echo load_html_head_contents("Booking Info", "../../../", 1, 1,'','','');
	extract($_REQUEST); 
	?> 
	<script> 
		var pop_source = '<? echo $pop_source;?>';
		/*function fn_show_check()
		{	
			if( $('#txt_po_no').val()=='' && $('#txt_job_no').val()=='' && $('#cbo_buyer_name').val()*1==0 && $('#txt_internal_ref').val()=='')
			{
				alert("Please Enter at Least One Search");return;
			}
			show_list_view ( $('#txt_po_no').val()+'_'+$('#txt_job_no').val()+'_'+<? echo $cbo_company_id_to; ?>+'_'+$('#cbo_buyer_name').val()+'_'+$('#txt_internal_ref').val()+'_'+ pop_source, 'create_po_search_list_view', 'search_div', 'finish_fabric_transfer_for_sample_controller', 'setFilterGrid(\'tbl_list_search\',-1);hidden_field_reset();');
		}*/

		function create_booking_desc()
		{
			if(pop_source*1 == 2)
			{
				if( $('#txt_sample_no').val()=='' && $('#cbo_buyer_name').val()*1==0 )
				{
					if($('#txt_date_from').val()=='' && $('#txt_date_to').val()=='')
					{
						alert("Please Enter at Least One Search");return;
					}
				}
				show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_sample_no').value+'_'+<? echo $cbo_company_id_to; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_search_category').value + '_'+ '<? echo $pop_source;?>', 'create_sample_search_list_view', 'search_div', 'finish_fabric_transfer_for_sample_controller', 'setFilterGrid(\'tbl_list_sample\',-1);');
			}
			else
			{
				if( $('#txt_sample_no').val()=='' && $('#cbo_buyer_name').val()*1==0 && $('#txt_batch_no').val()=='') 
				{
					if($('#txt_date_from').val()=='' && $('#txt_date_to').val()=='')
					{
						alert("Please Enter at Least One Search");return;
					}
				}
				show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_sample_no').value+'_'+<? echo $cbo_company_id_to; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_search_category').value + '_'+ '<? echo $pop_source;?>'+'_'+document.getElementById('txt_batch_no').value, 'create_sample_search_list_view', 'search_div', 'finish_fabric_transfer_for_sample_controller', 'setFilterGrid(\'tbl_list_sample\',-1);');
			}
		}

		function js_set_value( str) 
		{
			var str = str.split("_");
			$('#hidden_order_id').val(str[0]);
			$('#hidden_order_no').val(str[1]);
			$('#hidden_buyer_name').val(str[2]);

			$('#hidden_booking_id').val(str[0]);
			$('#hidden_booking_no').val(str[1]);

			$('#hidden_batch_no').val(str[3]);
			$('#hidden_batch_id').val(str[4]);
			

			parent.emailwindow.hide();
		}
		
		function hidden_field_reset()
		{
			$('#hidden_order_id').val('');
			$('#hidden_order_no').val( '' );
			$('#hidden_batch_no').val('');
			$('#hidden_batch_id').val('');
		}
		
	</script>

</head>
<body>
	<div align="center">
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:820px;margin-left:5px">
				<input type="hidden" name="hidden_order_id" id="hidden_order_id" class="text_boxes" value="">
				<input type="hidden" name="hidden_order_no" id="hidden_order_no" class="text_boxes" value="">
				<input type="hidden" name="hidden_buyer_name" id="hidden_buyer_name" class="text_boxes" value="">
				<input type="hidden" name="hidden_job_no" id="hidden_job_no" class="text_boxes" value="">
				<input type="hidden" name="hidden_style_ref" id="hidden_style_ref" class="text_boxes" value="">
				<input type="hidden" name="hidden_booking_no" id="hidden_booking_no" class="text_boxes" value="">
				<input type="hidden" name="hidden_booking_id" id="hidden_booking_id" class="text_boxes" value="">
				<input type="hidden" name="hidden_batch_no" id="hidden_batch_no" class="text_boxes" value="">
				<input type="hidden" name="hidden_batch_id" id="hidden_batch_id" class="text_boxes" value="">
				<table cellpadding="0" cellspacing="0" width="820" class="rpt_table" border="1" rules="all">
					<thead>
						<th  colspan="13">
							<?
							echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --",4 );
							?>
						</th>
					</thead>
					<thead>
						<th>Buyer Name</th>
						<? 	
						if($pop_source==1)
						{
							?>
							<th>Batch No</th>
							<?
						}
						?>
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
							echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id_to' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "","" );
							?>
						</td>
						<? 	
						if($pop_source==1)
						{
							?>
							<td>
								<input type="text" style="width:130px;" class="text_boxes" name="txt_batch_no" id="txt_batch_no" />
							</td>
							<?
						}
						?>
						<td>
							<input type="text" style="width:130px;" class="text_boxes" name="txt_sample_no" id="txt_sample_no" />
						</td>
						<td>
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" readonly>
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" readonly>
						</td>
						<td>
							<input type="button" name="button2" class="formbutton" value="Show" onClick="create_booking_desc();" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td colspan="4" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
				</table>
				<div id="search_div" style="margin-top:10px"  align="center"></div>	
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
	
	$search_string=trim($data[1]);
	$company_id=$data[2];
	$search_category=$data[5];
	$pop_source=$data[6];
	//pop_source defines from or to order/booking

	$batch_no=trim($data[7]);

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
	$buyer_arr = return_library_array("select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' order by buy.buyer_name","id","buyer_name"); 
	$arr=array (2=>$company_arr,3=>$buyer_arr,4=>$style_name_array,5=>$body_part);
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later

	$booking_no_cond= "";
	if($search_string!="")
	{
		if($search_category==1) {
			$booking_no_cond .=" and b.booking_no = '$search_string'"; 
		}
		else if ($search_category==0 || $search_category==4) {
			$booking_no_cond .=" and b.booking_no like '%$search_string%'"; 
		}
		else if($search_category==2) {
			$booking_no_cond .=" and b.booking_no like '$search_string%'"; 
		}
		else if($search_category==3) {
			$booking_no_cond .=" and b.booking_no like '%$search_string'"; 
		}
		else {$booking_no_cond .="";}
	}


	if($batch_no!="")
	{
		if($search_category==1) {
			$batch_cond_1 =" and d.batch_no = '$batch_no'"; 
			$batch_cond_2 =" and e.batch_no = '$batch_no'"; 
		}
		else if ($search_category==0 || $search_category==4) {
			$batch_cond_1 =" and d.batch_no like '%$batch_no%'"; 
			$batch_cond_2 =" and e.batch_no like '%$batch_no%'"; 
		}
		else if($search_category==2) {
			$batch_cond_1 =" and d.batch_no like '$batch_no%'"; 
			$batch_cond_2 =" and e.batch_no like '$batch_no%'"; 
		}
		else if($search_category==3) {
			$batch_cond_1 =" and d.batch_no like '%$batch_no'"; 
			$batch_cond_2 =" and e.batch_no like '%$batch_no'"; 
		}
		else {$batch_cond_1 = $batch_cond_2 = "";}
	}
	else 
	{
		$batch_cond_1=$batch_cond_2="";
	}

	if ($data[0]==0) $buyer_cond=""; else $buyer_cond= " and a.buyer_id =".$data[0];

	if($pop_source ==2)
	{
		$sql= "SELECT a.id as booking_id, a.booking_no, $year_field, a.booking_no_prefix_num, a.company_id, a.buyer_id, c.buyer_name, a.booking_date, a.delivery_date, b.style_id, b.fabric_description, b.body_part, null as batch_no, null as batch_id from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, lib_buyer c where a.booking_no=b.booking_no and a.buyer_id = c.id and a.booking_type=4 and a.company_id=$company_id $buyer_cond $booking_no_cond and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $booking_date order by a.id, b.id";
		echo create_list_view("tbl_list_sample", "Booking No,Year,Company,Buyer Name,Style Ref. No,Garments Item,Style Description,Booking Date", "60,60,80,80,120,130,160","850","200",0, $sql , "js_set_value", "booking_id,booking_no,buyer_name", "", 1, "0,0,company_id,buyer_id,style_id,body_part,0,0", $arr , "booking_no_prefix_num,year,company_id,buyer_id,style_id,body_part,fabric_description,booking_date", "",'','0,0,0,0,0,0,0,3');
	}
	else
	{
		$sql= "SELECT a.id as booking_id, a.booking_no, $year_field, a.booking_no_prefix_num, a.company_id, a.buyer_id, c.buyer_name, a.booking_date, a.delivery_date, b.style_id, b.fabric_description, b.body_part, d.batch_no, d.id as batch_id
		from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, lib_buyer c, pro_batch_create_mst d
		where a.booking_no=b.booking_no and A.BOOKING_NO=d.booking_no and a.buyer_id = c.id and a.booking_type=4 and a.company_id=$company_id $buyer_cond $booking_no_cond and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $booking_date $batch_cond_1
		group by a.id, a.booking_no, a.insert_date, a.booking_no_prefix_num, a.company_id, a.buyer_id, c.buyer_name, a.booking_date, a.delivery_date, b.style_id, b.fabric_description, b.body_part , d.batch_no, d.id
		union all
		select a.id as booking_id, a.booking_no, to_char(a.insert_date,'YYYY') as year, a.booking_no_prefix_num, a.company_id, a.buyer_id, c.buyer_name, a.booking_date, a.delivery_date, b.style_id, b.fabric_description, b.body_part , e.batch_no, e.id as batch_id
		from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, lib_buyer c, wo_non_ord_knitdye_booking_mst d, pro_batch_create_mst e
		where a.booking_no=b.booking_no and a.buyer_id = c.id and d.fab_booking_id=a.id and d.booking_no=e.booking_no and a.booking_type=4 and a.company_id=$company_id $buyer_cond $booking_no_cond and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $booking_date $batch_cond_2
		group by a.id, a.booking_no, a.insert_date, a.booking_no_prefix_num, a.company_id, a.buyer_id, c.buyer_name, a.booking_date, a.delivery_date, b.style_id, b.fabric_description, b.body_part , e.batch_no, e.id
		order by booking_id, booking_no";

		echo create_list_view("tbl_list_sample", "Booking No,Year,Company,Buyer Name,Style Ref. No,Garments Item,Style Description,Booking Date,Batch No", "60,60,80,80,120,130,160,100","950","200",0, $sql , "js_set_value", "booking_id,booking_no,buyer_name,batch_no,batch_id", "", 1, "0,0,company_id,buyer_id,style_id,body_part,0,0,0", $arr , "booking_no_prefix_num,year,company_id,buyer_id,style_id,body_part,fabric_description,booking_date,batch_no", "",'','0,0,0,0,0,0,0,3,0');
	}
	exit();
}

if($action=="show_fabric_item_listview")
{
	$data = explode("_",$data);
	$order_id = str_replace("'","",$data[0]);
	$company_id = str_replace("'","",$data[1]);
	$transfer_criteria = str_replace("'","",$data[2]);
	$batch_id = str_replace("'","",$data[3]);

	$sql_cond="";
	if($transfer_criteria == 6)
	{
		if($order_id!="") 	$sql_cond =" and c.po_breakdown_id=$order_id";
		$sql = "select b.company_id, b.pi_wo_batch_no as batch_id, e.buyer_name as buyer_id, sum(c.quantity) as quantity, c.po_breakdown_id as order_id, c.prod_id, b.store_id, b.floor_id, b.room, b.rack, b.self, a.batch_no, a.booking_no_id as booking_id, a.booking_no, d.po_number,b.fabric_shade, b.body_part_id,b.cons_uom, e.job_no, e.style_ref_no from pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e where a.id=b.pi_wo_batch_no and b.id = c.trans_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and b.item_category=2 and c.entry_form in (37,52,14,306) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and b.pi_wo_batch_no>0 and c.trans_id>0 and b.company_id =$company_id $sql_cond and a.id=$batch_id and b.transaction_type in (1,4,5) group by b.company_id, b.pi_wo_batch_no, e.buyer_name, c.po_breakdown_id , c.prod_id, b.store_id, b.floor_id, b.room, b.rack, b.self, a.batch_no, a.booking_no_id , a.booking_no, d.po_number,b.fabric_shade, b.body_part_id, e.job_no, e.style_ref_no, b.cons_uom order by c.prod_id";
	}
	else
	{
		if($order_id!="") $sql_cond =" and c.id =$order_id"; 
		$sql = "SELECT b.company_id, b.pi_wo_batch_no as batch_id, c.buyer_id, sum(b.cons_quantity) as quantity, c.id as order_id, b.prod_id, b.store_id, b.floor_id, b.room, b.rack, b.self, a.batch_no, a.booking_no_id as booking_id, a.booking_no, null po_number,b.fabric_shade, b.body_part_id, b.cons_uom, null as job_no, null as style_ref_no from pro_batch_create_mst a, inv_transaction b, wo_non_ord_samp_booking_mst c where a.id=b.pi_wo_batch_no and a.booking_no_id = c.id and a.booking_no = c.booking_no  and a.booking_without_order=1 and b.item_category=2 and b.transaction_type in (1,4,5) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.pi_wo_batch_no>0 and b.company_id= $company_id and a.id=$batch_id $sql_cond group by b.company_id, b.pi_wo_batch_no, c.buyer_id,  c.id , b.prod_id, b.store_id, b.floor_id, b.room,  b.rack, b.self, a.batch_no, a.booking_no_id, a.booking_no, b.fabric_shade, b.body_part_id, b.cons_uom
			union all 
			select b.company_id, b.pi_wo_batch_no as batch_id, c.buyer_id, sum(b.cons_quantity) as quantity, c.id as order_id, b.prod_id, b.store_id, b.floor_id, b.room, b.rack, b.self, a.batch_no, a.booking_no_id as booking_id, a.booking_no, null po_number,b.fabric_shade, b.body_part_id, b.cons_uom, null as job_no, null as style_ref_no from pro_batch_create_mst a, inv_transaction b, wo_non_ord_samp_booking_mst c,  wo_non_ord_knitdye_booking_mst d where a.id=b.pi_wo_batch_no and a.booking_no_id = d.id and d.fab_booking_id=c.id and a.booking_without_order=1 and b.item_category=2 and b.transaction_type in (1,4,5) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.pi_wo_batch_no>0 and b.company_id= $company_id and a.id=$batch_id $sql_cond group by b.company_id, b.pi_wo_batch_no, c.buyer_id, c.id , b.prod_id, b.store_id, b.floor_id, b.room, b.rack, b.self, a.batch_no, a.booking_no_id , a.booking_no, b.fabric_shade, b.body_part_id, b.cons_uom order by prod_id";
	}
	//echo $sql;
	$result = sql_select($sql);
	foreach($result as $row )
	{
		$orderidArr[$row[csf('order_id')]]=$row[csf('order_id')];
		$prodidArr[$row[csf('prod_id')]]=$row[csf('prod_id')];
		$batchidArr[$row[csf('batch_id')]]=$row[csf('batch_id')];
	}

	if($transfer_criteria == 6)
	{
		$sql_issue=sql_select("select b.po_breakdown_id as order_id, a.prod_id, a.pi_wo_batch_no,  a.store_id, a.floor_id, a.room,  a.rack, a.self,a.fabric_shade, a.body_part_id, a.cons_uom, sum(case when a.transaction_type in(2,3,6) and b.trans_type in(2,3,6) then b.quantity end) as issue_qty from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and b.entry_form in(14,18,46,306) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=2 and a.company_id=$company_id and b.po_breakdown_id in(".implode(",",$orderidArr).") and a.prod_id in (".implode(",",$prodidArr).") and a.pi_wo_batch_no in(".implode(",",$batchidArr).") and a.transaction_type in(2,3,6) group by b.po_breakdown_id, a.prod_id, a.pi_wo_batch_no, a.store_id, a.floor_id, a.room,  a.rack, a.self, a.fabric_shade, a.body_part_id, a.cons_uom");
	}
	else
	{
		$sql_issue=sql_select("select b.booking_no_id as order_id, a.prod_id, a.pi_wo_batch_no,  a.store_id, a.floor_id, a.room,  a.rack, a.self,a.fabric_shade, a.body_part_id, a.cons_uom, sum(case when a.transaction_type in(2,3,6)  then a.cons_quantity end) as issue_qty from inv_transaction a, pro_batch_create_mst b  where a.pi_wo_batch_no=b.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=2 and a.company_id=$company_id and b.booking_no_id in(".implode(",",$orderidArr).") and a.prod_id in(".implode(",",$prodidArr).") and a.pi_wo_batch_no in(".implode(",",$batchidArr).") and a.transaction_type in(2,3,6) group by b.booking_no_id, a.prod_id, a.pi_wo_batch_no, a.store_id, a.floor_id, a.room,  a.rack, a.self, a.fabric_shade, a.body_part_id, a.cons_uom");
	}


	
	foreach($sql_issue as $value)
	{
		if($value[csf('floor_id')] =="") $ref_floor = "0"; else $ref_floor = $value[csf('floor_id')];
		if($value[csf('room')] =="") $ref_room = "0"; else $ref_room = $value[csf('room')];
		if($value[csf('rack')] =="") $ref_rack = "0"; else $ref_rack = $value[csf('rack')];
		if($value[csf('self')] =="") $ref_self = "0"; else $ref_self = $value[csf('self')];

		/*$stock_data_arrray[$value[csf('order_id')]][$value[csf('prod_id')]][$value[csf('pi_wo_batch_no')]][$value[csf('store_id')]][$ref_floor][$ref_room][$ref_rack][$ref_self][$value[csf('fabric_shade')]][$value[csf('cons_uom')]]['rcv_qty']=$value[csf('rcv_qty')];
		$stock_data_arrray[$value[csf('order_id')]][$value[csf('prod_id')]][$value[csf('pi_wo_batch_no')]][$value[csf('store_id')]][$ref_floor][$ref_room][$ref_rack][$ref_self][$value[csf('fabric_shade')]][$value[csf('cons_uom')]]['issue_qty']=$value[csf('issue_qty')];*/
		$issue_data_arrray[$value[csf('order_id')]][$value[csf('prod_id')]][$value[csf('pi_wo_batch_no')]][$value[csf('store_id')]][$ref_floor][$ref_room][$ref_rack][$ref_self][$value[csf('fabric_shade')]][$value[csf('body_part_id')]][$value[csf('cons_uom')]]['issue_qty'] +=$value[csf('issue_qty')];
	}

	//print_r($issue_data_arrray);die;
	
	$prod_sql=sql_select("select id, product_name_details, color, avg_rate_per_unit from product_details_master where id in(".implode(",",$prodidArr).")");
	$prod_data=array();
	foreach($prod_sql as $row)
	{
		$prod_data[$row[csf("id")]]["product_name_details"]=$row[csf("product_name_details")];
		$prod_data[$row[csf("id")]]["color"]=$row[csf("color")];
		$prod_data[$row[csf("id")]]["avg_rate"]=$row[csf("avg_rate_per_unit")];
	}
	
	
	$lib_room_rack_shelf_sql = "select b.company_id,b.location_id,b.store_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name floor_name,c.floor_room_rack_name room_name,d.floor_room_rack_name rack_name,e.floor_room_rack_name shelf_name from lib_floor_room_rack_dtls b 
	left join lib_floor_room_rack_mst a on b.floor_id=a.floor_room_rack_id and a.status_active=1 and a.is_deleted=0
	left join lib_floor_room_rack_mst c on b.room_id=c.floor_room_rack_id and c.status_active=1 and c.is_deleted=0
	left join lib_floor_room_rack_mst d on b.rack_id=d.floor_room_rack_id and d.status_active=1 and d.is_deleted=0
	left join lib_floor_room_rack_mst e on b.shelf_id=e.floor_room_rack_id and e.status_active=1 and e.is_deleted=0
	where b.status_active=1 and b.is_deleted=0";
	$lib_floor_arr=sql_select($lib_room_rack_shelf_sql); 
	foreach ($lib_floor_arr as $room_rack_shelf_row) {
		$company  = $room_rack_shelf_row[csf("company_id")];
		$floor_id = $room_rack_shelf_row[csf("floor_id")];
		$room_id  = $room_rack_shelf_row[csf("room_id")];
		$rack_id  = $room_rack_shelf_row[csf("rack_id")];
		$shelf_id = $room_rack_shelf_row[csf("shelf_id")];
		$bin_id   = $room_rack_shelf_row[csf("bin_id")];

		if($floor_id!="" && $room_id=="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
			$lib_floor_arr[$company][$floor_id] = $room_rack_shelf_row[csf("floor_name")];
		}

		if($floor_id!="" && $room_id!="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
			$lib_room_arr[$company][$floor_id][$room_id] = $room_rack_shelf_row[csf("room_name")];
		}

		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id=="" && $bin_id==""){
			$lib_rack_arr[$company][$floor_id][$room_id][$rack_id] = $room_rack_shelf_row[csf("rack_name")];
		}

		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id==""){
			$lib_shelf_arr[$company][$floor_id][$room_id][$rack_id][$shelf_id] = $room_rack_shelf_row[csf("shelf_name")];
		}
	}
	
	?>
	<div style="width:1100px;" align="center">  
		<table cellspacing="0" width="100%" class="rpt_table" id="" rules="all" align="center">
			<thead>
				<tr>
					<th width="50">Item ID</th>
					<th width="200">Item Details</th>
					<th width="100">Body Part</th>
					<th width="50">UOM</th>
					<th width="90">Batch</th>
					<th width="90">Color</th>
					<th width="90">Floor</th>
					<th width="90">Room</th>
					<th width="90">Rack</th>
					<th width="90">shelf</th>
					<th width="90">F. Shade</th>
					<th width="">Stock</th>
				</tr>
			</thead>
		</table>
	</div>
	<div style="width:1118px; overflow-y:scroll; max-height:250px;" align="center">  
		<table cellspacing="0" width="1100" class="rpt_table" id="tbl_list_search" rules="all" align="center" style="word-break:break-all">
			<tbody>
				<? 
				$buyer_arr = return_library_array("select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' order by buy.buyer_name","id","buyer_name"); 
				$i=1;
				foreach($result as $row)
				{
					if($row[csf('floor_id')] =="") $floor_id = "0"; else $floor_id = $row[csf('floor_id')];
					if($row[csf('room')] =="") $room_id = "0"; else $room_id = $row[csf('room')];
					if($row[csf('rack')] =="") $rack_id = "0"; else $rack_id = $row[csf('rack')];
					if($row[csf('self')] =="") $self_id = "0"; else $self_id = $row[csf('self')];

					$issue_qty =$issue_data_arrray[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('store_id')]][$floor_id][$room_id][$rack_id][$self_id][$row[csf('fabric_shade')]][$row[csf('body_part_id')]][$row[csf('cons_uom')]]['issue_qty'];

					$stock = $row[csf("quantity")] - $issue_qty;
					$avg_rate = $prod_data[$row[csf('prod_id')]]["avg_rate"]*1;
					$stock=number_format($stock,4,'.','');
					if($stock>0)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$floor 		= $lib_floor_arr[$row[csf('company_id')]][$floor_id];
						$room 		= $lib_room_arr[$row[csf('company_id')]][$floor_id][$room_id];
						$rack_no	= $lib_rack_arr[$row[csf('company_id')]][$floor_id][$room_id][$rack_id];
						$shelf_no 	= $lib_shelf_arr[$row[csf('company_id')]][$floor_id][$room_id][$rack_id][$self_id];	
						?>
						<tr  bgcolor="<? echo $bgcolor; ?>" valign="middle"  style="text-decoration:none; cursor:pointer" onClick="set_form_data('<? echo $row[csf('order_id')]."_".$row[csf('prod_id')]."_".$row[csf('company_id')]."_".$row[csf('store_id')]."_".$floor_id."_".$room_id."_".$rack_id."_".$self_id."_".$row[csf('batch_id')]."_".$row[csf('batch_no')]."_".$floor."_".$room."_".$rack_no."_".$shelf_no."_".$row[csf('fabric_shade')]."_". $prod_data[$row[csf('prod_id')]]['product_name_details']."_".$row[csf('job_no')]."_".$row[csf('style_ref_no')]."_".$buyer_arr[$row[csf('buyer_id')]]."_".$row[csf('cons_uom')]."_".$prod_data[$row[csf('prod_id')]]["color"]."_".$color_arr[$prod_data[$row[csf('prod_id')]]["color"]]."_".$stock."_".$avg_rate."_".$row[csf('body_part_id')]."_".$body_part[$row[csf('body_part_id')]]; ?>')" >
							<td width="50"><? echo $row[csf('prod_id')]; ?></td>
							<td width="200"><? echo $prod_data[$row[csf('prod_id')]]["product_name_details"];?></td>
							<td width="100"><? echo $body_part[$row[csf('body_part_id')]];?></td>
							<td width="50"><? echo $unit_of_measurement[$row[csf('cons_uom')]];?></td>
							<td width="90"><? echo $row[csf('batch_no')]; ?></td>
							<td width="90"><? echo $color_arr[$prod_data[$row[csf('prod_id')]]["color"]]; ?></td>
							<td width="90"><? echo $floor; ?></td>
							<td width="90"><? echo $room; ?></td>
							<td width="90"><? echo $rack_no; ?></td>
							<td width="90"><? echo $shelf_no; ?></td>
							<td width="90"><? echo $fabric_shade[$row[csf('fabric_shade')]]; ?></td>
							<td width="" align="right"><? echo $stock; ?></td>

						</tr>
						<? 
						$i++;
					}
				}
				?>
			</tbody>
		</table>
	</div>
	<?
	exit();
}


if($action=="show_transfer_listview")
{
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=2","id","product_name_details");
	
	$sql="select id, from_store, to_store, from_prod_id, transfer_qnty, color_id from inv_item_transfer_dtls where mst_id='$data' and status_active = '1' and is_deleted = '0' and active_dtls_id_in_transfer=1";
	
	$arr=array(0=>$store_arr,1=>$store_arr,2=>$product_arr,4=>$color_arr);

	echo  create_list_view("list_view", "From Store,To Store,Item Description,Transfered Qnty,Color", "130,130,280,130,110","880","200",0, $sql, "get_php_form_data", "id", "'populate_transfer_details_form_data'", 0, "from_store,to_store,from_prod_id,0,color_id", $arr, "from_store,to_store,from_prod_id,transfer_qnty,color_id", "requires/finish_fabric_transfer_for_sample_controller",'','0,0,0,2,0');
	
	exit();
}

if($action=='populate_transfer_details_form_data')
{
	
	$data_array=sql_select("select a.transfer_criteria, a.company_id, a.to_company, a.location_id, a.to_location_id, b.from_order_id, b.to_order_id, b.id, b.mst_id, b.from_store, b.to_store, b.floor_id, b.room, b.rack, b.shelf, b.to_floor_id, b.to_room, b.to_rack, b.to_shelf, b.from_prod_id, b.to_prod_id, b.transfer_qnty, b.rate, b.transfer_value, b.color_id, b.batch_id,b.remarks,b.fabric_shade, b.uom, b.to_batch_id, b.body_part_id, b.to_body_part, b.trans_id, b.to_trans_id,b.to_ord_book_id,b.to_ord_book_no from inv_item_transfer_mst a, inv_item_transfer_dtls b where b.id='$data' and a.id=b.mst_id");
	
	$transfer_criteria = $data_array[0][csf('transfer_criteria')];
	$order_no = $sample_no = array();

	if($transfer_criteria == 6)
	{
		$order_no[] = $data_array[0][csf('from_order_id')];
		$sample_no[] = $data_array[0][csf('to_order_id')];
	}
	else if($transfer_criteria == 7)
	{
		$order_no[] = $data_array[0][csf('to_order_id')];
		$sample_no[] = $data_array[0][csf('from_order_id')];
	}
	else
	{
		$sample_no[] = $data_array[0][csf('to_order_id')];
		$sample_no[] = $data_array[0][csf('from_order_id')];
	}

	if(!empty($order_no))
	{ 
		$job_sql = sql_select("select a.id ,a.po_number, b.job_no, b.style_ref_no, b.buyer_name as buyer_id, c.buyer_name from wo_po_break_down a,wo_po_details_master b, lib_buyer c where a.status_active=1 and a.is_deleted =0 and a.job_no_mst = b.job_no and b.buyer_name = c.id and a.id in (".implode(",",$order_no).")");
		foreach ($job_sql as $val) 
		{
			$order_name_arr[$val[csf("id")]]["po_number"] = $val[csf("po_number")];
			$order_name_arr[$val[csf("id")]]["job_no"] = $val[csf("job_no")];
			$order_name_arr[$val[csf("id")]]["style_ref_no"] = $val[csf("style_ref_no")];
			$order_name_arr[$val[csf("id")]]["buyer_name"] = $val[csf("buyer_name")];
			$order_name_arr[$val[csf("id")]]["buyer_id"] = $val[csf("buyer_id")];
		}
	}

	if(!empty($sample_no))
	{
		$sample_sql = sql_select("select a.id, a.booking_no, a.buyer_id, b.buyer_name from wo_non_ord_samp_booking_mst a, lib_buyer b where a.status_active =1 and a.is_deleted = 0 and a.buyer_id = b.id and  b.status_active=1 and b.is_deleted =0 and a.id in (".implode(",",$sample_no).")");

		foreach ($sample_sql as $val) 
		{
			$sample_name_arr[$val[csf("id")]]["booking_no"] = $val[csf("booking_no")];
			$sample_name_arr[$val[csf("id")]]["buyer_name"] = $val[csf("buyer_name")];
		}
	}
	
	if($transfer_criteria == 6)
	{
		$sql_stock=sql_select("select b.po_breakdown_id as order_id, a.prod_id, a.pi_wo_batch_no,  a.store_id, a.floor_id, a.room,  a.rack, a.self,a.fabric_shade, a.body_part_id, a.cons_uom,sum(case when a.transaction_type in(1,4,5) and b.trans_type in(1,4,5) then b.quantity end) as rcv_qty,sum(case when a.transaction_type in(2,3,6) and b.trans_type in(2,3,6) then b.quantity end) as issue_qty from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and b.entry_form in(14,15,18,37,46,52,306) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=2 and a.company_id=". $data_array[0][csf('company_id')] ." and b.po_breakdown_id in(".$data_array[0][csf('from_order_id')].") and a.prod_id in (".$data_array[0][csf('from_prod_id')].") and a.pi_wo_batch_no in(".$data_array[0][csf('batch_id')].") group by b.po_breakdown_id, a.prod_id, a.pi_wo_batch_no, a.store_id, a.floor_id, a.room,  a.rack, a.self, a.fabric_shade, a.body_part_id, a.cons_uom");
	}
	else 
	{
		$sql_stock=sql_select("select b.booking_no_id as order_id, a.prod_id, a.pi_wo_batch_no,  a.store_id, a.floor_id, a.room,  a.rack, a.self,a.fabric_shade, a.body_part_id, a.cons_uom, sum(case when a.transaction_type in(1,4,5)  then a.cons_quantity end) as rcv_qty, sum(case when a.transaction_type in(2,3,6)  then a.cons_quantity end) as issue_qty from inv_transaction a, pro_batch_create_mst b  where a.pi_wo_batch_no=b.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=2 and a.company_id=".$data_array[0][csf('company_id')]." and b.booking_no_id in(".$data_array[0][csf('from_order_id')].") and a.prod_id in(".$data_array[0][csf('from_prod_id')].") and a.pi_wo_batch_no in(".$data_array[0][csf('batch_id')].") group by b.booking_no_id, a.prod_id, a.pi_wo_batch_no, a.store_id, a.floor_id, a.room,  a.rack, a.self, a.fabric_shade, a.body_part_id, a.cons_uom");

	}

	foreach($sql_stock as $value)
	{
		if($value[csf('floor_id')] =="") $ref_floor = "0"; else $ref_floor = $value[csf('floor_id')];
		if($value[csf('room')] =="") $ref_room = "0"; else $ref_room = $value[csf('room')];
		if($value[csf('rack')] =="") $ref_rack = "0"; else $ref_rack = $value[csf('rack')];
		if($value[csf('self')] =="") $ref_self = "0"; else $ref_self = $value[csf('self')];

		$stock_data_arrray[$value[csf('order_id')]][$value[csf('prod_id')]][$value[csf('pi_wo_batch_no')]][$value[csf('store_id')]][$ref_floor][$ref_room][$ref_rack][$ref_self][$value[csf('fabric_shade')]][$value[csf('body_part_id')]][$value[csf('cons_uom')]]['rcv_qty'] +=$value[csf('rcv_qty')];
		$stock_data_arrray[$value[csf('order_id')]][$value[csf('prod_id')]][$value[csf('pi_wo_batch_no')]][$value[csf('store_id')]][$ref_floor][$ref_room][$ref_rack][$ref_self][$value[csf('fabric_shade')]][$value[csf('body_part_id')]][$value[csf('cons_uom')]]['issue_qty']+=$value[csf('issue_qty')];
	}
	//echo "<pre>";
	//print_r($stock_data_arrray);die;

	$lib_room_rack_shelf_sql = "select b.company_id,b.location_id,b.store_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name floor_name,c.floor_room_rack_name room_name,d.floor_room_rack_name rack_name,e.floor_room_rack_name shelf_name from lib_floor_room_rack_dtls b 
	left join lib_floor_room_rack_mst a on b.floor_id=a.floor_room_rack_id and a.status_active=1 and a.is_deleted=0
	left join lib_floor_room_rack_mst c on b.room_id=c.floor_room_rack_id and c.status_active=1 and c.is_deleted=0
	left join lib_floor_room_rack_mst d on b.rack_id=d.floor_room_rack_id and d.status_active=1 and d.is_deleted=0
	left join lib_floor_room_rack_mst e on b.shelf_id=e.floor_room_rack_id and e.status_active=1 and e.is_deleted=0
	where b.status_active=1 and b.is_deleted=0";
	$lib_floor_arr=sql_select($lib_room_rack_shelf_sql); 
	foreach ($lib_floor_arr as $room_rack_shelf_row) {
		$company  = $room_rack_shelf_row[csf("company_id")];
		$floor_id = $room_rack_shelf_row[csf("floor_id")];
		$room_id  = $room_rack_shelf_row[csf("room_id")];
		$rack_id  = $room_rack_shelf_row[csf("rack_id")];
		$shelf_id = $room_rack_shelf_row[csf("shelf_id")];
		$bin_id   = $room_rack_shelf_row[csf("bin_id")];

		if($floor_id!="" && $room_id=="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
			$lib_floor_arr[$company][$floor_id] = $room_rack_shelf_row[csf("floor_name")];
		}

		if($floor_id!="" && $room_id!="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
			$lib_room_arr[$company][$floor_id][$room_id] = $room_rack_shelf_row[csf("room_name")];
		}

		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id=="" && $bin_id==""){
			$lib_rack_arr[$company][$floor_id][$room_id][$rack_id] = $room_rack_shelf_row[csf("rack_name")];
		}

		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id==""){
			$lib_shelf_arr[$company][$floor_id][$room_id][$rack_id][$shelf_id] = $room_rack_shelf_row[csf("shelf_name")];
		}
	}
	
	foreach ($data_array as $row)
	{ 
		$to_company=$row[csf("to_company")];
		$company_id=$row[csf("company_id")];
		
		echo "document.getElementById('update_dtls_id').value 				= '".$row[csf("id")]."';\n";
		
		if($row[csf("from_store")]>0){
			echo "load_room_rack_self_bin('requires/finish_fabric_transfer_for_sample_controller*2', 'store','from_store_td', $('#cbo_company_id').val(),'"."',this.value);\n";
			echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("from_store")]."';\n";
		}

		if($row[csf("floor_id")]>0){
			$floor 		= $lib_floor_arr[$company_id][$row[csf('floor_id')]];
			echo "document.getElementById('cbo_floor').value 				= '".$row[csf("floor_id")]."';\n";
			echo "document.getElementById('txt_floor').value 				= '".$floor."';\n";
		}
		if($row[csf("room")]>0){
			$room 		= $lib_room_arr[$company_id][$row[csf('floor_id')]][$row[csf('room')]];
			echo "document.getElementById('cbo_room').value 				= '".$row[csf("room")]."';\n";
			echo "document.getElementById('txt_room').value 				= '".$room."';\n";
		}
		if($row[csf("rack")]>0){
			$rack_no	= $lib_rack_arr[$company_id][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]];
			echo "document.getElementById('txt_rack').value 				= '".$row[csf("rack")]."';\n";
			echo "document.getElementById('txt_rack_show').value 			= '".$rack_no."';\n";
		}
		if($row[csf("shelf")]>0){
			$shelf_no 	= $lib_shelf_arr[$company_id][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('shelf')]];
			echo "document.getElementById('txt_shelf').value 				= '".$row[csf("shelf")]."';\n";
			echo "document.getElementById('txt_shelf_show').value 			= '".$shelf_no."';\n";
		}
		
		if($row[csf("to_store")]>0){
			echo "load_room_rack_self_bin('requires/finish_fabric_transfer_for_sample_controller*2*cbo_store_name_to', 'store','to_store_td', '".$to_company."','".$row[csf("to_location_id")]."',this.value);\n";
			echo "document.getElementById('cbo_store_name_to').value 				= '".$row[csf("to_store")]."';\n";
			echo "document.getElementById('previous_to_store').value 				= '".$row[csf("to_store")]."';\n";
		}
		if($row[csf("to_floor_id")]>0){
			echo "load_room_rack_self_bin('requires/finish_fabric_transfer_for_sample_controller*2*cbo_floor_to', 'floor','floor_td_to', '".$to_company."','"."','".$row[csf('to_store')]."',this.value);\n";
			echo "document.getElementById('cbo_floor_to').value 			= '".$row[csf("to_floor_id")]."';\n";
		}
		if($row[csf("to_room")]>0){
			echo "load_room_rack_self_bin('requires/finish_fabric_transfer_for_sample_controller*2*cbo_room_to', 'room','room_td_to', '".$to_company."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."',this.value);\n";
			echo "document.getElementById('cbo_room_to').value 				= '".$row[csf("to_room")]."';\n";
		}
		if($row[csf("to_rack")]>0){
			echo "load_room_rack_self_bin('requires/finish_fabric_transfer_for_sample_controller*2*txt_rack_to', 'rack','rack_td_to','".$to_company."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."','".$row[csf('to_room')]."',this.value);\n";
			echo "document.getElementById('txt_rack_to').value 				= '".$row[csf("to_rack")]."';\n";
		}
		if($row[csf("to_shelf")]>0){
			echo "load_room_rack_self_bin('requires/finish_fabric_transfer_for_sample_controller*2*txt_shelf_to', 'shelf','shelf_td_to','".$to_company."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."','".$row[csf('to_room')]."','".$row[csf('to_rack')]."',this.value);\n";
			echo "document.getElementById('txt_shelf_to').value 			= '".$row[csf("to_shelf")]."';\n";
		}

		echo "document.getElementById('txt_transfer_qnty').value 			= '".$row[csf("transfer_qnty")]."';\n";
		echo "document.getElementById('hidden_transfer_qnty').value 		= '".$row[csf("transfer_qnty")]."';\n";
		echo "document.getElementById('hide_color_id').value 				= '".$row[csf("color_id")]."';\n";
		echo "document.getElementById('txt_color').value 					= '".$color_arr[$row[csf("color_id")]]."';\n";
		echo "document.getElementById('cbo_fabric_shade').value 			= '".$row[csf("fabric_shade")]."';\n";
		echo "document.getElementById('cbo_uom').value 						= '".$row[csf("uom")]."';\n";
		
		echo "document.getElementById('batch_id').value 					= '".$row[csf("batch_id")]."';\n";
		echo "document.getElementById('previous_from_batch_id').value 		= '".$row[csf("batch_id")]."';\n";
		echo "document.getElementById('previous_to_batch_id').value 		= '".$row[csf("to_batch_id")]."';\n";

		$batch_no = return_field_value("batch_no", "pro_batch_create_mst", "id=".$row[csf("batch_id")]);
		echo "document.getElementById('txt_batch_no').value 				= '".$batch_no."';\n";

		if($transfer_criteria == 6)
		{
			echo "document.getElementById('txt_from_order_no').value 		= '".$order_name_arr[$row[csf("from_order_id")]]["po_number"]."';\n";
			echo "document.getElementById('cbo_buyer_name').value 			= '".$order_name_arr[$row[csf("from_order_id")]]["buyer_name"]."';\n";
			echo "document.getElementById('txt_style_ref').value 			= '".$order_name_arr[$row[csf("from_order_id")]]["style_ref_no"]."';\n";
			echo "document.getElementById('txt_job_no').value 				= '".$order_name_arr[$row[csf("from_order_id")]]["job_no"]."';\n";

		}else{
			echo "document.getElementById('txt_from_order_no').value 		= '".$sample_name_arr[$row[csf("from_order_id")]]["booking_no"]."';\n";
			echo "document.getElementById('cbo_buyer_name').value 			= '".$sample_name_arr[$row[csf("from_order_id")]]["buyer_name"]."';\n";
		}
		echo "document.getElementById('txt_from_order_id').value 			= '".$row[csf("from_order_id")]."';\n";

		echo "document.getElementById('from_product_id').value 				= '".$row[csf("from_prod_id")]."';\n";
		echo "document.getElementById('previous_from_prod_id').value 		= '".$row[csf("from_prod_id")]."';\n";
		echo "document.getElementById('body_part_id_from').value 			= '".$row[csf("body_part_id")]."';\n";
		echo "document.getElementById('txt_body_part').value 				= '".$body_part[$row[csf("body_part_id")]]."';\n";

		echo "load_drop_down('requires/finish_fabric_transfer_for_sample_controller', ".$row[csf('to_order_id')]."+'_'+".$row[csf("from_prod_id")]."+'_' +".$row[csf("transfer_criteria")].", 'load_body_part', 'to_body_part_td' );";

		echo "document.getElementById('cbo_to_body_part').value 			= '".$row[csf("to_body_part")]."';\n";
		
		if($transfer_criteria == 7)
		{
			echo "document.getElementById('txt_to_order_no').value 				= '".$order_name_arr[$row[csf("to_order_id")]]["po_number"]."';\n";
			echo "document.getElementById('cbo_buyer_name_to').value 			= '".$order_name_arr[$row[csf("to_order_id")]]["buyer_name"]."';\n";
			echo "document.getElementById('txt_style_ref_to').value 			= '".$order_name_arr[$row[csf("to_order_id")]]["style_ref_no"]."';\n";
			echo "document.getElementById('txt_job_no_to').value 				= '".$order_name_arr[$row[csf("to_order_id")]]["job_no"]."';\n";
		}else{
			echo "document.getElementById('txt_to_order_no').value 				= '".$sample_name_arr[$row[csf("to_order_id")]]["booking_no"]."';\n";
			echo "document.getElementById('cbo_buyer_name_to').value 			= '".$sample_name_arr[$row[csf("to_order_id")]]["buyer_name"]."';\n";
		}

		echo "document.getElementById('txt_to_order_id').value 				= '".$row[csf("to_order_id")]."';\n";

		echo "document.getElementById('previous_to_order_id').value 		= '".$row[csf("to_order_id")]."';\n";
		echo "document.getElementById('previous_to_prod_id').value 			= '".$row[csf("to_prod_id")]."';\n";
		echo "document.getElementById('previous_to_company_id').value 		= '".$data_array[0][csf("to_company")]."';\n";
		echo "document.getElementById('txt_remarks').value 					= '".$row[csf("remarks")]."';\n";
		
		echo "document.getElementById('txt_rate').value 					= '".$row[csf("rate")]."';\n";
		echo "document.getElementById('txt_transfer_value').value 			= '".$row[csf("transfer_value")]."';\n";
		
		$sql=sql_select("select product_name_details,current_stock,avg_rate_per_unit from product_details_master where id=".$row[csf('from_prod_id')]);


		if($row[csf('floor_id')] =="") $floor_id = "0"; else $floor_id = $row[csf('floor_id')];
		if($row[csf('room')] =="") $room_id = "0"; else $room_id = $row[csf('room')];
		if($row[csf('rack')] =="") $rack_id = "0"; else $rack_id = $row[csf('rack')];
		if($row[csf('shelf')] =="") $self_id = "0"; else $self_id = $row[csf('shelf')];


		$stockQty = ($stock_data_arrray[$row[csf('from_order_id')]][$row[csf('from_prod_id')]][$row[csf('batch_id')]][$row[csf('from_store')]][$floor_id][$room_id][$rack_id][$self_id][$row[csf('fabric_shade')]][$row[csf('body_part_id')]][$row[csf('uom')]]['rcv_qty'])  -   ($stock_data_arrray[$row[csf('from_order_id')]][$row[csf('from_prod_id')]][$row[csf('batch_id')]][$row[csf('from_store')]][$floor_id][$room_id][$rack_id][$self_id][$row[csf('fabric_shade')]][$row[csf('body_part_id')]][$row[csf('uom')]]['issue_qty'])  +  $row[csf("transfer_qnty")];


		echo "document.getElementById('txt_item_desc').value 				= '".$sql[0][csf("product_name_details")]."';\n";
		echo "document.getElementById('txt_current_stock').value 			= '".number_format($stockQty,4,'.','')."';\n";
		echo "document.getElementById('hidden_current_stock').value 		= '".number_format($stockQty,4,'.','')."';\n";

		//echo "document.getElementById('txt_rate').value 					= '".$sql[0][csf("avg_rate_per_unit")]."';\n";
		//echo "document.getElementById('txt_transfer_value').value 			= '".$stockQty*$sql[0][csf("avg_rate_per_unit")]."';\n";

		
		
		echo "document.getElementById('update_trans_issue_id').value 		= '".$row[csf("trans_id")]."';\n";
		echo "document.getElementById('update_trans_recv_id').value 		= '".$row[csf("to_trans_id")]."';\n";
		echo "document.getElementById('hidden_to_booking_no').value 		= '".$row[csf("to_ord_book_no")]."';\n";
		echo "document.getElementById('hidden_to_booking_id').value 		= '".$row[csf("to_ord_book_id")]."';\n";

		echo "show_list_view(".$row[csf('from_order_id')]."+'_'+".$row[csf("company_id")]."+'_'+".$row[csf("transfer_criteria")]."+'_'+".$row[csf("batch_id")].",'show_fabric_item_listview','list_fabric_desc_container','requires/finish_fabric_transfer_for_sample_controller','');\n";

		echo "disable_enable_fields('cbo_store_name*cbo_floor*cbo_room*txt_rack*txt_shelf',1);\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_yarn_transfer_entry',1,1);\n"; 
		
		exit();
	}
}

//data save update delete here------------------------------//
if($action=="save_update_delete")
{	 
	//echo "10**";die;
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if($db_type==0)	{ mysql_query("BEGIN"); }

	$company_id_to=str_replace("'","",$cbo_company_id_to);
	$cbo_location_to=str_replace("'","",$cbo_location_to);
	$transfer_criteria=str_replace("'","",$cbo_transfer_criteria);
	
	if(str_replace("'","",$update_id)!="")
	{
		$is_acknowledge = return_field_value("b.id id", "inv_item_transfer_mst a,inv_item_trans_acknowledgement b", "a.id=b.challan_id and  a.id=$update_id and a.status_active=1 and a.is_acknowledge=1", "id");
		if($is_acknowledge != "" )
		{
			echo "20**Update not allowed. This Transfer Challan is already Acknowledged.\nAcknowledge System ID = $is_acknowledge";
			disconnect($con);die;
		}

		$up_cond="";
		if(str_replace("'","",$update_trans_issue_id)!="")
		{
			$all_trans_id=str_replace("'","",$update_trans_issue_id);
			$up_cond=" and id not in($all_trans_id)";
		}
		
		$duplicate_product_check=return_field_value("id", "inv_transaction", " status_active=1 and transaction_type in(6) and item_category =$cbo_item_category and prod_id=$from_product_id and pi_wo_batch_no=$batch_id and mst_id=$update_id $up_cond", "id");
		if($duplicate_product_check)
		{
			echo "20**Duplicate Item Not Allow Within Same MRR";disconnect($con);die;
		}
	}

	
	$up_trans_id=str_replace("'","",$update_trans_issue_id);
	if(str_replace("'","",$update_trans_recv_id)!="") $up_trans_id.=",".str_replace("'","",$update_trans_recv_id);
	$up_cond="";
	if(str_replace("'","",$update_trans_issue_id)!="") $up_cond=" and a.id not in($up_trans_id)";

	if($transfer_criteria == 6)
	{
		$sql_stock_query="select sum(case when a.transaction_type in(1,4,5) and b.trans_type in(1,4,5) then b.quantity end) as rcv , sum(case when a.transaction_type in(2,3,6) and b.trans_type in(2,3,6) then b.quantity end) as iss from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and b.entry_form in(14,15,18,37,46,52,306) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=2 and a.company_id=$cbo_company_id and b.po_breakdown_id in($txt_from_order_id) and a.prod_id in ($from_product_id) and a.pi_wo_batch_no in($batch_id) and a.store_id = $cbo_store_name and a.body_part_id =$body_part_id_from $up_cond group by b.po_breakdown_id, a.prod_id, a.pi_wo_batch_no, a.store_id, a.floor_id, a.room,  a.rack, a.self, a.fabric_shade,a.cons_uom";
	}
	else
	{
		$sql_stock_query="select  sum(case when a.transaction_type in(1,4,5) then a.cons_quantity end) as rcv, sum(case when a.transaction_type in(2,3,6)  then a.cons_quantity end) as iss from inv_transaction a, pro_batch_create_mst b  where a.pi_wo_batch_no=b.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=2 and a.company_id=$cbo_company_id and b.booking_no_id in($txt_from_order_id) and a.prod_id in($from_product_id) and a.pi_wo_batch_no in($batch_id) and a.store_id = $cbo_store_name and a.body_part_id =$body_part_id_from $up_cond group by b.booking_no_id, a.prod_id, a.pi_wo_batch_no, a.store_id, a.floor_id, a.room,  a.rack, a.self, a.fabric_shade,a.cons_uom";
	}

	//echo "10**".$sql_stock_query;die;
	$sql_stock=sql_select($sql_stock_query);

	foreach ($sql_stock as $val) 
	{
		$sql_stock_rcv += $val[csf("rcv")];
		$sql_stock_iss += $val[csf("iss")];
	}

	$trans_qnty=str_replace("'","",$txt_transfer_qnty)*1;

	$stock_qnty=$sql_stock_rcv*1 - $sql_stock_iss*1;
	if($trans_qnty>$stock_qnty)
	{
		echo "20**Transfer quantity is not available in this Store.\nAvailable=$stock_qnty";
		disconnect($con);die;
	}

	$cbo_floor = (str_replace("'", "", $cbo_floor) =="")? 0 :str_replace("'", "", $cbo_floor);
	$cbo_room = (str_replace("'", "", $cbo_room)=="")? 0 :str_replace("'", "", $cbo_room);
	$txt_rack = (str_replace("'", "", $txt_rack)=="")? 0 :str_replace("'", "", $txt_rack);
	$txt_shelf = (str_replace("'", "", $txt_shelf)=="")? 0 :str_replace("'", "", $txt_shelf);

	$cbo_floor_to = (str_replace("'", "", $cbo_floor_to) =="")? 0 :str_replace("'", "", $cbo_floor_to);
	$cbo_room_to = (str_replace("'", "", $cbo_room_to)=="")? 0 :str_replace("'", "", $cbo_room_to);
	$txt_rack_to = (str_replace("'", "", $txt_rack_to)=="")? 0 :str_replace("'", "", $txt_rack_to);
	$txt_shelf_to = (str_replace("'", "", $txt_shelf_to)=="")? 0 :str_replace("'", "", $txt_shelf_to);

	
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		$transfer_recv_num=''; $transfer_update_id='';
		$variable_auto_rcv = return_field_value("auto_transfer_rcv", "variable_settings_inventory", " company_name=$cbo_company_id and item_category_id=2 and status_active=1 and variable_list= 27", "auto_transfer_rcv");

		//echo "10**".$variable_auto_rcv; die;
		if($variable_auto_rcv == "")
		{
			$variable_auto_rcv = 1; // if auto receive yes(1), then no need to acknowledgement
		}
		//$variable_auto_rcv = 1;

		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
			
			$id = return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst", $con);
			$new_transfer_system_id = explode("*", return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst",$con,1,$cbo_company_id,"FFTES",306,date("Y",time()),$cbo_item_category ));

			$field_array="id, transfer_prefix, transfer_prefix_number, transfer_system_id, company_id, location_id, remarks, transfer_date, entry_form, transfer_criteria, to_company,to_location_id, from_order_id, to_order_id, item_category, is_acknowledge, inserted_by, insert_date";

			$is_acknowledge=0;
			if($variable_auto_rcv!=2) // if auto receive yes(1), then no need to acknowledgement
			{
				$is_acknowledge=1;
			}
			
			$data_array="(".$id.",'".$new_transfer_system_id[1]."',".$new_transfer_system_id[2].",'".$new_transfer_system_id[0]."',".$cbo_company_id.",".$cbo_location.",".$txt_master_remarks.",".$txt_transfer_date.",306,".$cbo_transfer_criteria.",".$company_id_to.",".$cbo_location_to.",0,0,".$cbo_item_category.",".$is_acknowledge.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			
			//echo "10**insert into inv_item_transfer_mst (".$field_array.") values ".$data_array;die;
			$transfer_recv_num=$new_transfer_system_id[0];
			$transfer_update_id=$id;
		}
		else
		{
			$field_array_update="remarks*transfer_date*to_company*to_location_id*updated_by*update_date";
			$data_array_update=$txt_master_remarks."*".$txt_transfer_date."*".$company_id_to."*".$cbo_location_to."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			/*$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);*/
			
			$transfer_recv_num=str_replace("'","",$txt_system_id);
			$transfer_update_id=str_replace("'","",$update_id);
		}
		

		$field_array_trans="id, mst_id, company_id, prod_id, pi_wo_batch_no, item_category, transaction_type, transaction_date, store_id, floor_id, room, rack, self, order_id, brand_id, fabric_shade, body_part_id, cons_uom, cons_quantity, cons_rate, cons_amount, balance_qnty, balance_amount, inserted_by, insert_date";
		$field_array_dtls="id, mst_id, from_prod_id, to_prod_id, batch_id, yarn_lot, brand_id, color_id, item_group, from_store, floor_id, room, rack, shelf, to_store, to_floor_id, to_room, to_rack, to_shelf, item_category, transfer_qnty, rate, transfer_value, uom, inserted_by, insert_date, from_order_id, to_order_id, trans_id, to_trans_id,remarks, fabric_shade, to_batch_id, body_part_id, to_body_part, to_ord_book_id, to_ord_book_no,active_dtls_id_in_transfer";

		
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, inserted_by, insert_date";
		
		$field_array_batch_dtls="id, mst_id, po_id, prod_id, item_description, roll_no, roll_id, barcode_no, batch_qnty, dtls_id,width_dia_type, inserted_by, insert_date";


		if(str_replace("'", "", $cbo_company_id) != str_replace("'", "", $company_id_to))
		{
			$data_prod=sql_select("select supplier_id, current_stock, avg_rate_per_unit, stock_value,detarmination_id,dia_width,gsm, unit_of_measure from product_details_master where id=$from_product_id");
			
			$presentStock=$data_prod[0][csf('current_stock')]-str_replace("'","",$txt_transfer_qnty);
			$presentAvgRate=$data_prod[0][csf('avg_rate_per_unit')];
			$presentStockValue=$presentStock*$presentAvgRate;

			if($presentStock <=0){
				$presentAvgRate=0;
				$presentStockValue=0;
			}
			
			$field_array_prodUpdate="avg_rate_per_unit*last_issued_qnty*current_stock*stock_value*updated_by*update_date";
			$data_array_prodUpdate="'".$presentAvgRate."'*".$txt_transfer_qnty."*".$presentStock."*".$presentStockValue."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			$supplier_id=$data_prod[0][csf('supplier_id')];// and supplier_id='$supplier_id'
			$product_id=0;	
			
			if ($data_prod[0][csf('dia_width')]=="") 
			{
				if($db_type == 0){
					$dia_cond = " and dia_width = '' ";
				}else{
					$dia_cond = " and dia_width is null ";
				}
			}
			else
			{
				$dia_cond = " and dia_width = '".$data_prod[0][csf('dia_width')]."'";
			}

			$row_prod=sql_select("select id, current_stock, avg_rate_per_unit from product_details_master where company_id=$company_id_to and item_category_id=$cbo_item_category and detarmination_id=".$data_prod[0][csf('detarmination_id')]." $dia_cond and gsm = ".$data_prod[0][csf('gsm')]." and color=$hide_color_id and unit_of_measure = ".$data_prod[0][csf('unit_of_measure')]." and status_active=1 and is_deleted=0");

			if(count($row_prod)>0)
			{
				$product_id=$row_prod[0][csf('id')];
				$stock_qnty=$row_prod[0][csf('current_stock')];
				$avg_rate_per_unit=$row_prod[0][csf('avg_rate_per_unit')]*1;

				$curr_stock_qnty=$stock_qnty+str_replace("'", '',$txt_transfer_qnty);
				$stock_value=$curr_stock_qnty*$avg_rate_per_unit;

				if($variable_auto_rcv!=2) // if auto receive yes(1), then no need to acknowledgement
				{
					$field_array_prod_update="avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
					$data_array_prod_update=$avg_rate_per_unit."*".$txt_transfer_qnty."*".$curr_stock_qnty."*".$stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				}
				
			}
			else
			{
				$product_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
				$curr_stock_qnty=str_replace("'","",$txt_transfer_qnty);

				$avg_rate_per_unit=$data_prod[0][csf('avg_rate_per_unit')];
				$stock_value=$curr_stock_qnty*$avg_rate_per_unit;

				if($variable_auto_rcv!=2)
				{
					$sql_prod_insert="insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, inserted_by, insert_date) 
					select	
					'$product_id', $cbo_company_id_to, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, $txt_transfer_qnty, $curr_stock_qnty, $stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, inserted_by, insert_date from product_details_master where id=$from_product_id";
				}
				else
				{
					$sql_prod_insert="insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id, item_description, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, inserted_by, insert_date)
					select
					'$product_id', $cbo_company_id_to, supplier_id, item_category_id, detarmination_id, item_description, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, 0, 0, 0, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, inserted_by, insert_date from product_details_master where id=$from_product_id";
				}
			}
		}
		else
		{
			$product_id =$from_product_id;
		}


		//----------------Check Last Receive Date for Transfer Out----------------
		$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$from_product_id and transaction_type in (1,4,5) and store_id = $cbo_store_name  and status_active = 1", "max_date");      
		if($max_recv_date !="")
		{
			$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
			$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));

			if ($transfer_date < $max_recv_date) 
			{
				echo "20**Transfer Out Date Can not Be Less Than Last Receive Date Of This Item";
				disconnect($con);
				die;
			}
		}

		//----------------Check Last Issue Date for Transfer In----------------
		$max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$product_id and store_id = $cbo_store_name_to  and status_active = 1", "max_date");      
		if($max_issue_date !="")
		{
			$max_issue_date = date("Y-m-d", strtotime($max_issue_date));
			$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));

			if ($transfer_date < $max_issue_date) 
			{
				echo "20**Transfer In Date Can not Be Less Than Last Transaction Date Of This Lot";
				disconnect($con);
				die;
			}
		}

		$batchData=sql_select("select a.id, a.batch_weight from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.batch_no=$txt_batch_no and a.color_id=$hide_color_id and a.status_active=1 and a.is_deleted=0 and a.entry_form=306 and a.company_id=$company_id_to and a.booking_no=$hidden_to_booking_no group by a.id, a.batch_weight");

		if(count($batchData)>0)
		{
			$batch_id_to=$batchData[0][csf('id')];
			$curr_batch_weight=$batchData[0][csf('batch_weight')]+str_replace("'", '',$txt_transfer_qnty);
			$field_array_batch_update="batch_weight*updated_by*update_date";
			$data_array_batch_update=$curr_batch_weight."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		}
		else
		{
			if($transfer_criteria==7){
				$booking_without_order = 0;
			}else{
				$booking_without_order = 1;
			}
			
			$batch_id_to = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);
			$field_array_batch="id,batch_no,entry_form,batch_date,company_id,booking_no_id,booking_no,booking_without_order,color_id,batch_weight,inserted_by,insert_date";

			$data_array_batch="(".$batch_id_to.",".$txt_batch_no.",306,".$txt_transfer_date.",".$company_id_to.",".$hidden_to_booking_id.",".$hidden_to_booking_no.",".$booking_without_order.",".$hide_color_id.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		}

		$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		$data_array_trans="(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",".$from_product_id.",".$batch_id.",".$cbo_item_category.",6,".$txt_transfer_date.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$txt_from_order_id.",0,".$cbo_fabric_shade.",".$body_part_id_from.",".$cbo_uom.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		$recv_trans_id=0;
		if($variable_auto_rcv!=2)// if auto receive yes(1), then no need to acknowledgement
		{
			$recv_trans_id = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			$data_array_trans.=",(".$recv_trans_id.",".$transfer_update_id.",".$company_id_to.",".$product_id.",".$batch_id_to.",".$cbo_item_category.",5,".$txt_transfer_date.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$txt_to_order_id.",0,".$cbo_fabric_shade.",".$cbo_to_body_part.",".$cbo_uom.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",".$txt_transfer_qnty.",".$txt_transfer_value.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		}
		
		
		$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
		$data_array_dtls="(".$id_dtls.",".$transfer_update_id.",".$from_product_id.",".$product_id.",".$batch_id.",0,0,".$hide_color_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_item_category.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_from_order_id.",".$txt_to_order_id.",".$id_trans.",".$recv_trans_id.",".$txt_remarks.",".$cbo_fabric_shade.",".$batch_id_to.",".$body_part_id_from.",".$cbo_to_body_part.",".$hidden_to_booking_id.",".$hidden_to_booking_no.",1)";
		

		if($transfer_criteria==6) // Order to Sample
		{
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			$data_array_prop="(".$id_prop.",".$id_trans.",6,306,".$id_dtls.",".$txt_from_order_id.",".$from_product_id.",".$hide_color_id.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		}

		if($transfer_criteria==7) // Sample to Order
		{
			if($variable_auto_rcv!=2)// if auto receive yes(1), then no need to acknowledgement
			{
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$data_array_prop.=",(".$id_prop.",".$recv_trans_id.",5,306,".$id_dtls.",".$txt_to_order_id.",".$product_id.",".$hide_color_id.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
		}

		
		$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
		if($data_array_batch_dtls!="") $data_array_batch_dtls.=",";
		$data_array_batch_dtls.="(".$id_dtls_batch.",".$batch_id_to.",".$txt_to_order_id.",".$product_id.",".$txt_item_desc.",0,0,0,".$txt_transfer_qnty.",".$id_dtls.",0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";			



		$isLIFOfifo=return_field_value("store_method","variable_settings_inventory","company_name=$cbo_company_id and variable_list=17 and item_category_id=2 and status_active=1 and is_deleted=0");
		if($isLIFOfifo==2) $cond_lifofifo=" DESC"; else $cond_lifofifo=" ASC";
		
		$transfer_qnty = str_replace("'","",$txt_transfer_qnty);
		$transfer_value = str_replace("'","",$txt_transfer_value);
		$field_array_mrr= "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";

		$update_array = "balance_qnty*balance_amount*updated_by*update_date";
		$updateID_array=array();
		$update_data=array();
		
		$sql = sql_select("select id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id=$from_product_id and store_id=$cbo_store_name and balance_qnty>0 and transaction_type in (1,4,5) and item_category=2 and floor_id = $cbo_floor and room = $cbo_room and rack = $txt_rack and self = $txt_shelf and fabric_shade = $cbo_fabric_shade and pi_wo_batch_no = $batch_id order by transaction_date $cond_lifofifo");

		foreach($sql as $result)
		{
			$recv_trans_id = $result[csf("id")]; // this row will be updated
			$balance_qnty = $result[csf("balance_qnty")];
			$balance_amount = $result[csf("balance_amount")];
			$cons_rate = $result[csf("cons_rate")]*1;
			if($cons_rate=="") { $cons_rate=str_replace("'","",$txt_rate)*1; }

			//echo "10**".$cons_rate;die;
			$transferQntyBalance = $balance_qnty-$transfer_qnty; // minus issue qnty
			$transferStockBalance = $balance_amount-($transfer_qnty*$cons_rate);
			if($transferQntyBalance>=0)
			{					
				$amount = $transfer_qnty*$cons_rate;
				//for insert
				$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
				if($data_array_mrr!="") $data_array_mrr .= ",";  
				$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$id_trans.",306,".$from_product_id.",".$transfer_qnty.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
				//for update
				$updateID_array[]=$recv_trans_id; 
				$update_data[$recv_trans_id]=explode("*",("".$transferQntyBalance."*".$transferStockBalance."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				break;
			}
			else if($transferQntyBalance<0)
			{
				$transferQntyBalance = $transfer_qnty-$balance_qnty;				
				$transfer_qnty = $balance_qnty;				
				$amount = $transfer_qnty*$cons_rate;
				
				//for insert
				$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
				if($data_array_mrr!="") $data_array_mrr .= ",";  
				$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$id_trans.",306,".$from_product_id.",".$balance_qnty.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
				//for update
				$updateID_array[]=$recv_trans_id; 
				$update_data[$recv_trans_id]=explode("*",("0*0*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				$transfer_qnty = $transferQntyBalance;
			}
		}


		$rID=$prod=$rID2=$rID3=$rID4=$rID5=true;

		if(str_replace("'","",$update_id)=="")
		{
			//echo "10**insert into inv_item_transfer_mst (".$field_array.") values ".$data_array;die;
			$rID=sql_insert("inv_item_transfer_mst",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;
		}
		else
		{
			$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0; 
		}
		

		if(str_replace("'", "", $cbo_company_id) != str_replace("'", "", $company_id_to))
		{
			$prodUpdate=sql_update("product_details_master",$field_array_prodUpdate,$data_array_prodUpdate,"id",$from_product_id,1);
			if($flag==1) 
			{
				if($prodUpdate) $flag=1; else $flag=0; 
			} 

			if(count($row_prod)>0)
			{
				if($variable_auto_rcv!=2)
				{
					echo "10**".$field_array_prod_update."<br>".$data_array_prod_update."<br>".$product_id;oci_rollback($con);	disconnect($con); die;

					$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
					if($flag==1) 
					{
						if($prod) $flag=1; else $flag=0; 
					}
				}
			}
			else
			{
				$prod=execute_query($sql_prod_insert,0);
				if($flag==1) 
				{
					if($prod) $flag=1; else $flag=0; 
				} 
			}
		}
		
		//echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;
		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
		
		//echo "10**insert into inv_item_transfer_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID3=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		}
		
		if($data_array_prop!="")
		{
			//echo "10**insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;
			$rID4=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1); 
			if($flag==1) 
			{
				if($rID4) $flag=1; else $flag=0; 
			}
		}

		$rID6=$rID7=true;
		

		if($data_array_batch_dtls!="")
		{
			//echo "10**insert into pro_batch_create_dtls (".$field_array_batch_dtls.") values ".$data_array_batch_dtls;die;
			$rID6=sql_insert("pro_batch_create_dtls",$field_array_batch_dtls,$data_array_batch_dtls,0);
			if($flag==1) 
			{
				if($rID6) $flag=1; else $flag=0; 
			}
		}

		if(count($batchData)>0)
		{
			//echo "10**";echo $data_array_batch_update."==".$batch_id_to;die;
			$rID7=sql_update("pro_batch_create_mst",$field_array_batch_update,$data_array_batch_update,"id",$batch_id_to,0);
		}
		else
		{
			//echo "10**insert into pro_batch_create_mst (".$field_array_batch.") values ".$data_array_batch;die;
			$rID7=sql_insert("pro_batch_create_mst",$field_array_batch,$data_array_batch,0);
		}

		if($flag==1)
		{
			if($rID7) $flag=1; else $flag=0;
		}
		
		//echo "10**insert into inv_mrr_wise_issue_details (".$field_array_mrr.") values ".$data_array_mrr; oci_rollback($con);	disconnect($con); die; 
		if($data_array_mrr!="")
		{		
			$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,1);
			//echo "10**insert into inv_mrr_wise_issue_details (".$field_array_mrr.") values ".$data_array_mrr;die;
			if($flag==1) 
			{
				if($mrrWiseIssueID) $flag=1; else $flag=0; 
			} 
		}

		if(count($updateID_array)>0)
		{
			//echo "10**".bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array);die;
			$upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array));
			if($flag==1) 
			{
				if($upTrID) $flag=1; else $flag=0; 
			} 
		}
		
		//echo "10**".$flag."**".$rID."**".$prod."**".$rID2."**".$rID3."**".$rID4."**".$rID6."**".$rID7."**".$mrrWiseIssueID."**".$upTrID."**".$variable_auto_rcv;
		//oci_rollback($con);	disconnect($con); die;

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

		$variable_auto_rcv = return_field_value("auto_transfer_rcv", "variable_settings_inventory", " company_name=$cbo_company_id and item_category_id=2 and status_active=1 and variable_list= 27", "auto_transfer_rcv");

		if($variable_auto_rcv == "")
		{
			$variable_auto_rcv = 1;
		}
		//$variable_auto_rcv = 1;

		// Check to order/store to validate their balance
		if($variable_auto_rcv != 2 ) // if auto receive yes(1), then no need to acknowledgement
		{
			$is_rcv_exist = return_field_value("to_trans_id", "inv_item_transfer_dtls", " id =$update_dtls_id and status_active=1", "to_trans_id");
			if($is_rcv_exist > 0)
			{
				if($transfer_criteria == 7)
				{
					$sql_stock=sql_select("select sum(case when a.transaction_type in(1,4,5) and b.trans_type in(1,4,5) then b.quantity else 0 end) as rcv , sum(case when a.transaction_type in(2,3,6) and b.trans_type in(2,3,6) then b.quantity else 0 end) as iss  
						from inv_transaction a, order_wise_pro_details b
						where a.id=b.trans_id and b.entry_form in(14,15,18,37,46,52,306) and a.store_id = $previous_to_store and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=2  and a.company_id=$previous_to_company_id and b.po_breakdown_id=$previous_to_order_id and a.prod_id=$previous_to_prod_id and a.pi_wo_batch_no=$previous_to_batch_id ");
				}
				else
				{
					$sql_stock=sql_select("select  sum(case when a.transaction_type in(1,4,5) then a.cons_quantity end) as rcv, sum(case when a.transaction_type in(2,3,6)  then a.cons_quantity end) as iss from inv_transaction a, pro_batch_create_mst b  where a.pi_wo_batch_no=b.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=2 and a.company_id=$previous_to_company_id and b.booking_no_id =$previous_to_order_id and a.prod_id =$previous_to_prod_id and a.pi_wo_batch_no =$previous_to_batch_id and a.store_id = $previous_to_store");
				}
				
				$trans_qnty=str_replace("'","",$txt_transfer_qnty)*1;
				$hidden_transfer_qnty=str_replace("'","",$hidden_transfer_qnty)*1;

				$stock_qnty=$sql_stock[0][csf("rcv")]*1 - $sql_stock[0][csf("iss")]*1;

				$check_to_ord_stock = ($hidden_transfer_qnty - $stock_qnty);

				/*if(($hidden_transfer_qnty - $stock_qnty) > $trans_qnty)
				{
					echo "20**Transfer quantity is not available in previous to order and to Store .\nAvailable=".$check_to_ord_stock;
					disconnect($con);die;
				}*/


				$issue_check = sql_select("select sum(issue_qnty) as issue_qnty from inv_mrr_wise_issue_details  where recv_trans_id = $update_trans_recv_id and status_active = 1");
			
				if($issue_check[0][csf("issue_qnty")] > $trans_qnty)
				{
					$check_to_ord_stock = ($hidden_transfer_qnty - $issue_check[0][csf("issue_qnty")]);

					echo "20**Next transaction found.\nTransfered Fabric already used = ".$issue_check[0][csf("issue_qnty")]." \nAvailable for update = ".$check_to_ord_stock;
					disconnect($con);die;
				}
			}
		}

		//echo "20**failed ";die;
		$field_array_update="remarks*transfer_date*to_company*to_location_id*updated_by*update_date";
		$data_array_update=$txt_master_remarks."*".$txt_transfer_date."*".$company_id_to."*".$cbo_location_to."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		
		$field_array_trans="prod_id*pi_wo_batch_no*transaction_date*store_id*floor_id*room*rack*self*order_id*brand_id*fabric_shade*body_part_id*cons_uom*cons_quantity*cons_rate*cons_amount*balance_qnty*balance_amount*updated_by*update_date";
		$updateTransID_array=array();
		$update_trans_issue_id=str_replace("'","",$update_trans_issue_id); 
		$update_trans_recv_id=str_replace("'","",$update_trans_recv_id); 
		
		$field_array_dtls="from_prod_id*to_prod_id*batch_id*color_id*from_store*floor_id*room*rack*shelf*to_store*to_floor_id*to_room*to_rack*to_shelf*transfer_qnty*rate*transfer_value*uom*updated_by*update_date*from_order_id*to_order_id*remarks*fabric_shade*to_batch_id*body_part_id*to_body_part";
		
		$field_array_proportionate_update="po_breakdown_id*prod_id*color_id*quantity*updated_by*update_date";

		$field_array_batch_dtls="id, mst_id, po_id, prod_id, item_description, roll_no, roll_id, barcode_no, batch_qnty, dtls_id,width_dia_type, inserted_by, insert_date";

		$prod=true;

		$updateProdID_array=array();
		$field_array_adjust="current_stock*avg_rate_per_unit*stock_value*updated_by*update_date";
		
		if(str_replace("'", "", $cbo_company_id) != str_replace("'", "", $company_id_to))
		{
			$stock_from=sql_select("select current_stock, avg_rate_per_unit from product_details_master where id=$previous_from_prod_id");
			$data_prod=sql_select("select supplier_id, current_stock, avg_rate_per_unit, stock_value, detarmination_id,dia_width,gsm,unit_of_measure from product_details_master where id=$from_product_id");
			$stock_to=sql_select("select current_stock, avg_rate_per_unit from product_details_master where id=$previous_to_prod_id");

			if(str_replace("'","",$previous_from_prod_id) != str_replace("'","",$from_product_id))
			{
				//before from product
				//echo "10**fail".str_replace("'","",$previous_from_prod_id) . "=" .str_replace("'","",$from_product_id);die;
				$adjust_curr_stock_from=$stock_from[0][csf('current_stock')]+str_replace("'",'',$hidden_transfer_qnty);
				$cur_st_rate_from=$stock_from[0][csf('avg_rate_per_unit')];

				$cur_st_value_from=$adjust_curr_stock_from*$cur_st_rate_from;

				if($adjust_curr_stock_from<=0){
					$cur_st_rate_from=0;
					$cur_st_value_from=0;
				}


				$previous_from_prod_id=str_replace("'","",$previous_from_prod_id);
				$updateProdID_array[]=$previous_from_prod_id; 

				$data_array_adjust[$previous_from_prod_id]=explode("*",("".$adjust_curr_stock_from."*".$cur_st_rate_from."*".$cur_st_value_from."*".$user_id."*'".$pc_date_time."'"));
				
				$presentStock=$data_prod[0][csf('current_stock')] - str_replace("'","",$txt_transfer_qnty);

				$adjust_curr_stock_to=$stock_to[0][csf('current_stock')]- str_replace("'","",$hidden_transfer_qnty);


				$supplier_id=$data_prod[0][csf('supplier_id')];
				if ($data_prod[0][csf('dia_width')]=="") 
				{
					if($db_type == 0){
						$dia_cond = " and dia_width = '' ";
					}else{
						$dia_cond = " and dia_width is null ";
					}
				}
				else
				{
					$dia_cond = " and dia_width = '".$data_prod[0][csf('dia_width')]."'";
				}
				
				$row_prod=sql_select("select id, current_stock, avg_rate_per_unit from product_details_master where company_id=$company_id_to and item_category_id=$cbo_item_category and detarmination_id=".$data_prod[0][csf('detarmination_id')]." $dia_cond and gsm = ".$data_prod[0][csf('gsm')]." and color=$hide_color_id and unit_of_measure = " .$data_prod[0][csf('unit_of_measure')] ." and status_active=1 and is_deleted=0");

				if(count($row_prod)>0)
				{
					$product_id=$row_prod[0][csf('id')];
					$stock_qnty=$row_prod[0][csf('current_stock')];
					$avg_rate_per_unit=$row_prod[0][csf('avg_rate_per_unit')];

					$curr_stock_qnty=$stock_qnty+str_replace("'", '',$txt_transfer_qnty);
					$stock_value=$curr_stock_qnty*$avg_rate_per_unit;

					if($variable_auto_rcv != 2 )
					{
						$field_array_prod_update="avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
						$data_array_prod_update="'".$avg_rate_per_unit."'*".$txt_transfer_qnty."*".$curr_stock_qnty."*".$stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
					}
					
				}
				else
				{
					$product_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
					$curr_stock_qnty=str_replace("'","",$txt_transfer_qnty);
					$avg_rate_per_unit=$data_prod[0][csf('avg_rate_per_unit')];
					$stock_value=$curr_stock_qnty*$avg_rate_per_unit;
					
					if($variable_auto_rcv != 2 )
					{
						$sql_prod_insert="insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, inserted_by, insert_date) 
						select	
						'$product_id', $cbo_company_id_to, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, $txt_transfer_qnty, $curr_stock_qnty, $stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, inserted_by, insert_date from product_details_master where id=$from_product_id";
					}
					else
					{
						$sql_prod_insert="insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, inserted_by, insert_date)
						select
						'$product_id', $cbo_company_id_to, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, 0, 0, 0, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, inserted_by, insert_date from product_details_master where id=$from_product_id";
					}
				}
			}
			else
			{
				$presentStock=$data_prod[0][csf('current_stock')] + (str_replace("'", '',$hidden_transfer_qnty) - str_replace("'","",$txt_transfer_qnty) );
				$adjust_curr_stock_to=$stock_to[0][csf('current_stock')]- (str_replace("'", '',$hidden_transfer_qnty)- str_replace("'","",$txt_transfer_qnty));
				$product_id = str_replace("'", "", $previous_to_prod_id);
			}
		}
		else
		{
			$product_id = str_replace("'","",$from_product_id);
		}

		//echo "10**failed";die;

		$presentAvgRate=$data_prod[0][csf('avg_rate_per_unit')];
		$presentStockValue=$presentStock*$presentAvgRate;

		$field_array_prodUpdate="avg_rate_per_unit*last_issued_qnty*current_stock*stock_value*updated_by*update_date";
		$data_array_prodUpdate="'".$presentAvgRate."'*".$txt_transfer_qnty."*".$presentStock."*".$presentStockValue."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		if($variable_auto_rcv != 2 )
		{
			$cur_st_rate_to=$stock_to[0][csf('avg_rate_per_unit')];
			$cur_st_value_to=$adjust_curr_stock_to*$cur_st_rate_to;		

			if($adjust_curr_stock_to<=0)
			{
				$cur_st_rate_to=0;
				$cur_st_value_to=0;
			}

			$previous_to_prod_id=str_replace("'","",$previous_to_prod_id);
			$updateProdID_array[]=$previous_to_prod_id; 		
			$data_array_adjust[$previous_to_prod_id]=explode("*",("".$adjust_curr_stock_to."*".$cur_st_rate_to."*".$cur_st_value_to."*".$user_id."*'".$pc_date_time."'"));
		}
		
		//----------------Check Last Receive Date for Transfer Out----------------
		$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$from_product_id and store_id = $cbo_store_name and id <> $update_trans_recv_id  and status_active = 1", "max_date");      
		if($max_recv_date !="")
		{
			$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
			$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
			if ($transfer_date < $max_recv_date) 
			{
				echo "20**Transfer Out Date Can not Be Less Than Last Receive Date Of This Lot";
				disconnect($con);
				die;
			}
		}
		
		//----------------Check Last Issue Date for Transfer In----------------
		$max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$product_id and store_id = $cbo_store_name_to and id not in ($update_trans_recv_id , $update_trans_issue_id )  and status_active = 1", "max_date");      
		if($max_issue_date !="")
		{
			$max_issue_date = date("Y-m-d", strtotime($max_issue_date));
			$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));

			if ($transfer_date < $max_issue_date) 
			{
				echo "20**Transfer In Date Can not Be Less Than Last Transaction Date Of This Lot";
				disconnect($con);
				die;
			}
		}
		
		$batchData=sql_select("select a.id, a.batch_weight from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.batch_no=$txt_batch_no and a.color_id=$hide_color_id and a.status_active=1 and a.is_deleted=0 and a.entry_form=306 and a.company_id=$company_id_to and a.booking_no='$hidden_to_booking_no' group by a.id, a.batch_weight");

		$field_array_batch_update="batch_weight*updated_by*update_date";
		if(count($batchData)>0)
		{
			$batch_id_to=$batchData[0][csf('id')];
			if($batch_id_to==str_replace("'","",$previous_to_batch_id))
			{ 
				$curr_batch_weight=$batchData[0][csf('batch_weight')]+str_replace("'", '',$txt_transfer_qnty)-str_replace("'", '',$hidden_transfer_qnty);
				$update_batch_id[]=str_replace("'","",$previous_to_batch_id);
				$data_array_batch_update[str_replace("'","",$previous_to_batch_id)]=explode("*",("'".$curr_batch_weight."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
			else
			{
				//previous batch adjusted
				$batch_weight= return_field_value("batch_weight","pro_batch_create_mst","id=$previous_to_batch_id");
				$adjust_batch_weight=$batch_weight-str_replace("'", '',$hidden_transfer_qnty);
				$data_array_batch_update[str_replace("'","",$previous_to_batch_id)]=explode("*",("'".$adjust_batch_weight."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

				//new batch adjusted
				$curr_batch_weight=$batchData[0][csf('batch_weight')]+str_replace("'", '',$txt_transfer_qnty);
				$update_batch_id[]=$batchData[0][csf('id')];
				$data_array_batch_update[$batchData[0][csf('id')]]=explode("*",("'".$curr_batch_weight."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
		}
		else
		{
			if($transfer_criteria==7){
				$booking_without_order = 0;
			}else{
				$booking_without_order = 1;
			}
			
			$batch_id_to = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);
			$field_array_batch="id,batch_no,entry_form,batch_date,company_id,booking_no_id,booking_no,booking_without_order,color_id,batch_weight,inserted_by,insert_date";

			$data_array_batch="(".$batch_id_to.",".$txt_batch_no.",306,".$txt_transfer_date.",".$company_id_to.",".$hidden_to_booking_id.",".$hidden_to_booking_no.",".$booking_without_order.",".$hide_color_id.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			//previous batch adjusted
			$batch_weight= return_field_value("batch_weight","pro_batch_create_mst","id=$previous_to_batch_id");
			$adjust_batch_weight=$batch_weight-str_replace("'", '',$hidden_transfer_qnty);
			$update_batch_id[]=str_replace("'","",$previous_to_batch_id);
			$data_array_batch_update[str_replace("'","",$previous_to_batch_id)]=explode("*",("'".$adjust_batch_weight."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		}
		/*echo "10**'$hidden_to_booking_no'==".$batch_id_to;
		print_r($data_array_batch_update);
		die;*/

		$updateTransID_array[]=$update_trans_issue_id; 
		$updateTransID_data[$update_trans_issue_id]=explode("*",("".$from_product_id."*".$batch_id."*".$txt_transfer_date."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$txt_from_order_id."*0*".$cbo_fabric_shade."*".$body_part_id_from."*".$cbo_uom."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*'0'*'0'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

		if($variable_auto_rcv != 2 )
		{
			$updateTransID_array[]=$update_trans_recv_id; 				
			$updateTransID_data[$update_trans_recv_id]=explode("*",("'".$product_id."'*'".$batch_id_to."'*".$txt_transfer_date."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$txt_to_order_id."*0*".$cbo_fabric_shade."*".$cbo_to_body_part."*".$cbo_uom."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*".$txt_transfer_qnty."*".$txt_transfer_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		}
		
		
		$data_array_dtls=$from_product_id."*".$product_id."*".$batch_id."*".$hide_color_id."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*".$cbo_uom."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_from_order_id."*".$txt_to_order_id."*".$txt_remarks."*".$cbo_fabric_shade."*".$batch_id_to."*".$body_part_id_from."*".$cbo_to_body_part."";
		//echo "10**string";
		
		if(str_replace("'", "", $update_trans_issue_id))
		{
			$data_array_prop_update[$update_trans_issue_id]=explode("*",("".$txt_from_order_id."*".$from_product_id."*".$hide_color_id."*".$txt_transfer_qnty."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		}

		if($variable_auto_rcv != 2 )
		{
			if(str_replace("'", "", $update_trans_recv_id))
			{
				$data_array_prop_update[$update_trans_recv_id]=explode("*",("".$txt_to_order_id."*'".$product_id."'*".$hide_color_id."*".$txt_transfer_qnty."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
		}

		$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
		if($data_array_batch_dtls!="") $data_array_batch_dtls.=",";
		$data_array_batch_dtls.="(".$id_dtls_batch.",".$batch_id_to.",".$txt_to_order_id.",".$product_id.",".$txt_item_desc.",0,0,0,".$txt_transfer_qnty.",".$update_dtls_id.",0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";


		//============================================================================

		//transaction table START--------------------------//
		$field_array_trans_pre_mrr = "balance_qnty*balance_amount*updated_by*update_date";
		$sql = sql_select("select a.id,a.balance_qnty,a.balance_amount,b.issue_qnty,b.rate,b.amount from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id=$update_trans_issue_id and b.entry_form=306 and a.item_category=2"); 

		$updateID_array_pre_trans = array();
		$update_data_trans_pre_mrr = array();
		foreach($sql as $result)
		{
			$adjBalance = $result[csf("balance_qnty")]+$result[csf("issue_qnty")];
			$adjAmount = $result[csf("balance_amount")]+$result[csf("amount")];
			$updateID_array_pre_trans[]=$result[csf("id")]; 
			$update_data_trans_pre_mrr[$result[csf("id")]]=explode("*",("".$adjBalance."*".$adjAmount."*'".$user_id."'*'".$pc_date_time."'"));
			
			$trans_data_array[$result[csf("id")]]['qnty']=$adjBalance;
			$trans_data_array[$result[csf("id")]]['amnt']=$adjAmount;
		}

		
		//print_r($update_data);
		//LIFO/FIFO Start-----------------------------------------------//
		$update_array = "balance_qnty*balance_amount*updated_by*update_date";
		$isLIFOfifo=return_field_value("store_method","variable_settings_inventory","company_name=$cbo_company_id and variable_list=17 and item_category_id=1 and status_active=1 and is_deleted=0");
		if($isLIFOfifo==2) $cond_lifofifo=" DESC"; else $cond_lifofifo=" ASC";
		
		$transfer_qnty = str_replace("'","",$txt_transfer_qnty);
		$transfer_value = str_replace("'","",$txt_transfer_value);
		$field_array_mrr= "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";

		$previous_from_prod_id=str_replace("'","",$previous_from_prod_id); 
		$from_product_id = str_replace("'","",$from_product_id);
		$transId=implode(",",$updateID_array_pre_trans);
		if($from_product_id==$previous_from_prod_id) $balance_cond="(balance_qnty>0 or id in($transId))";
		else $balance_cond="balance_qnty>0";
		
		if(str_replace("'", "", $cbo_floor) == "" ) { $cbo_floor = 0;}else $cbo_floor = str_replace("'", "", $cbo_floor);
		if(str_replace("'", "", $cbo_room) == "" ) { $cbo_room = 0;}else $cbo_room = str_replace("'", "", $cbo_room);
		if(str_replace("'", "", $txt_rack) == "" ) { $txt_rack = 0;}else $txt_rack = str_replace("'", "", $txt_rack);
		if(str_replace("'", "", $txt_shelf) == "" ) { $txt_shelf = 0;}else $txt_shelf = str_replace("'", "", $txt_shelf);

		$sql = sql_select("select id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id=$from_product_id and store_id=$cbo_store_name and $balance_cond and floor_id = $cbo_floor and room = $cbo_room and rack = $txt_rack and self = $txt_shelf and fabric_shade = $cbo_fabric_shade and pi_wo_batch_no = $batch_id and transaction_type in (1,4,5) and item_category=2 order by transaction_date $cond_lifofifo");

		foreach($sql as $result)
		{
			$recv_trans_id = $result[csf("id")]; 
			if($trans_data_array[$recv_trans_id]['qnty']=="")
			{
				$balance_qnty = $result[csf("balance_qnty")];
				$balance_amount = $result[csf("balance_amount")];
			}
			else
			{
				$balance_qnty = $trans_data_array[$recv_trans_id]['qnty'];
				$balance_amount = $trans_data_array[$recv_trans_id]['amnt'];
			}
			//echo $balance_qnty."**";
			$cons_rate = $result[csf("cons_rate")];
			if($cons_rate=="") { $cons_rate=$txt_rate; }
			
			$transferQntyBalance = $balance_qnty-$transfer_qnty; // minus issue qnty
			$transferStockBalance = $balance_amount-($transfer_qnty*$cons_rate);
			if($transferQntyBalance>=0)
			{					
				$amount = $transfer_qnty*$cons_rate;
				//for insert
				$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
				if($data_array_mrr!="") $data_array_mrr .= ",";  
				$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$update_trans_issue_id.",306,".$from_product_id.",".$transfer_qnty.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
				//for update

				$updateID_array[]=$recv_trans_id; 

				
				$update_data[$recv_trans_id]=explode("*",("".$transferQntyBalance."*".$transferStockBalance."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				break;
			}
			else if($transferQntyBalance<0)
			{
				$transferQntyBalance = $transfer_qnty-$balance_qnty;				
				$transfer_qnty = $balance_qnty;				
				$amount = $transfer_qnty*$cons_rate;
				
				//for insert
				$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
				if($data_array_mrr!="") $data_array_mrr .= ",";  
				$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$update_trans_issue_id.",306,".$from_product_id.",".$balance_qnty.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
				//for update
				$updateID_array[]=$recv_trans_id; 
				
				$update_data[$recv_trans_id]=explode("*",("0*0*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				$transfer_qnty = $transferQntyBalance;
			}
		}


		//echo "10**";print_r($update_data);die;

		//=============================================================================
		//echo "10**fail";die;
		//echo "10**".$field_array_mrr."<br>".$data_array_mrr;oci_rollback($con); die;

		$prod=$prodUpdate_adjust=$prodUpdate=$rID=$rID2=$rID3=$rID4=$rID5=true;

		if(str_replace("'", "", $cbo_company_id) != str_replace("'", "", $company_id_to))
		{
			if(count($row_prod)>0)
			{
				if($data_array_prod_update!="")
				{	//echo "10**".$field_array_prod_update ."==". $data_array_prod_update ."==". $product_id;die;
					$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
					if($flag==1) 
					{
						if($prod) $flag=1; else $flag=0; 
					}
				}
			}
			else
			{
				$prod=execute_query($sql_prod_insert,0);
				if($flag==1) 
				{
					if($prod) $flag=1; else $flag=0; 
				} 
			}	

			//echo "10**".bulk_update_sql_statement("product_details_master","id",$field_array_adjust,$data_array_adjust,$updateProdID_array);die;
			$prodUpdate_adjust=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_adjust,$data_array_adjust,$updateProdID_array));
			if($flag==1) 
			{
				if($prodUpdate_adjust) $flag=1; else $flag=0; 
			}
					//echo "10**".$field_array_prodUpdate ."=". $data_array_prodUpdate ."=". $from_product_id;die;
			$prodUpdate=sql_update("product_details_master",$field_array_prodUpdate,$data_array_prodUpdate,"id",$from_product_id,1);
			if($flag==1) 
			{
				if($prodUpdate) $flag=1; else $flag=0; 
			}
		}

		//if(count($data_array_batch_update)>0)
		//{ 
			//echo "10**"; echo bulk_update_sql_statement("pro_batch_create_mst","id",$field_array_batch_update,$data_array_batch_update,$update_batch_id);die;
			//$batchMstUpdate=execute_query(bulk_update_sql_statement("pro_batch_create_mst","id",$field_array_batch_update,$data_array_batch_update,$update_batch_id));
			
		//}

		$batchMstUpdate=execute_query(bulk_update_sql_statement("pro_batch_create_mst","id",$field_array_batch_update,$data_array_batch_update,$update_batch_id));

		if($flag==1) 
		{
			if($batchMstUpdate) $flag=1; else $flag=0; 
		}

		if($data_array_batch!="")
		{
			$batchMstNewInsert=sql_insert("pro_batch_create_mst",$field_array_batch,$data_array_batch,0);
			if($flag==1) 
			{
				if($batchMstNewInsert) $flag=1; else $flag=0; 
			}
		}


		$delete_batch_dtls=execute_query( "delete from pro_batch_create_dtls where mst_id=$previous_to_batch_id and dtls_id=$update_dtls_id",0);
		if($flag==1)
		{
			if($delete_batch_dtls) $flag=1; else $flag=0;
		}


		if($data_array_batch_dtls!="")
		{
			//echo "6**insert into pro_batch_create_dtls (".$field_array_batch_dtls.") values ".$data_array_batch_dtls;die;
			$batchDtls=sql_insert("pro_batch_create_dtls",$field_array_batch_dtls,$data_array_batch_dtls,1);
			if($flag==1)
			{
				if($batchDtls) $flag=1; else $flag=0;
			}
		}

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


		$rID4=execute_query(bulk_update_sql_statement("order_wise_pro_details","trans_id",$field_array_proportionate_update,$data_array_prop_update,$updateTransID_array));
		if($flag==1) 
		{
			if($rID4) $flag=1; else $flag=0; 
		}


		//transaction table stock update here------------------------//

		if (count($updateID_array_pre_trans) > 0) 
		{
			$query2 = execute_query(bulk_update_sql_statement("inv_transaction", "id", $field_array_trans_pre_mrr, $update_data_trans_pre_mrr, $updateID_array_pre_trans));
			$query3 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id=$update_trans_issue_id and entry_form=306");

			if($flag==1) 
			{
				if($query2 && $query3) $flag=1; else $flag=0; 
			}

		}

		//echo "10**insert into inv_mrr_wise_issue_details ($field_array_mrr) values $data_array_mrr"."<br><br>".bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array);oci_rollback($con); disconnect($con);die;
		if($data_array_mrr!="")
		{	
			//echo "10**insert into inv_mrr_wise_issue_details ($field_array_mrr) values $data_array_mrr";die;
			$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,1);
			if($flag==1) 
			{
				if($mrrWiseIssueID) $flag=1; else $flag=0; 
			} 
		}


		if(count($updateID_array)>0)
		{
			//echo "10**".bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array); disconnect($con);die;
			$query=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array));
			if($flag==1) 
			{
				if($query) $flag=1; else $flag=0; 
			} 
		}


		//echo "10**".$flag."**".$batchMstNewInsert."**".$rID."**".$prodUpdate_adjust."**".$prodUpdate."**".$prod."**".$rID2."**".$rID3."**".$rID4."**".$rID5."**".$batchMstUpdate."**".$delete_batch_dtls."**".$batchDtls."**".$query."**".$query2."**".$mrrWiseIssueID."**".$query3;oci_rollback($con);disconnect($con);die;

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
	else if ($operation==2)
	{
		$update_dtls_id = str_replace("'", "", $update_dtls_id);
		$update_trans_recv_id = str_replace("'", "", $update_trans_recv_id);
		$update_trans_issue_id = str_replace("'", "", $update_trans_issue_id);
		$hidden_transfer_qnty = str_replace("'", "", $hidden_transfer_qnty);
		$previous_from_prod_id = str_replace("'", "", $previous_from_prod_id);
		$previous_to_prod_id = str_replace("'", "", $previous_to_prod_id);

		if($update_dtls_id == "")
		{
			echo "20**Delete Failed";
			die;
		}
		
		$variable_auto_rcv = return_field_value("auto_transfer_rcv", "variable_settings_inventory", " company_name=$cbo_company_id and item_category_id=2 and status_active=1 and variable_list= 27", "auto_transfer_rcv");

		if($variable_auto_rcv == "")
		{
			$variable_auto_rcv = 1;
		}
		//$variable_auto_rcv = 1;

		if($variable_auto_rcv == 2 )
		{
			$is_rcv_exist = return_field_value("to_trans_id", "inv_item_transfer_dtls", " id =$update_dtls_id and status_active=1", "to_trans_id");
			if($is_rcv_exist > 0)
			{
				echo "20**Fabric transfer already acknowledged by user.Delete not allowed.";
				die;
			}
		}

		/*$issue_check = sql_select("select sum(issue_qnty) as issue_qnty from inv_mrr_wise_issue_details  where recv_trans_id = $update_trans_recv_id and status_active = 1");
		
		if($issue_check[0][csf("issue_qnty")])
		{
			echo "20**Delete not allowed. Next transaction found.";
			die;
		}*/

		if($update_trans_recv_id)
		{
			$max_trans_query = sql_select("SELECT  max(id) as max_id from inv_transaction where prod_id =$previous_to_prod_id and item_category=2 and status_active=1");

			$max_trans_id = $max_trans_query[0][csf('max_id')];
			if($max_trans_id > $update_trans_recv_id)
			{
				echo "20**Delete not allowed. Next transaction found.";
				die;
			}
		}

		$field_array_trans_pre_mrr = "balance_qnty*balance_amount*updated_by*update_date";
		$from_mrr_trans_sql = sql_select("select a.id,a.balance_qnty,a.balance_amount,b.issue_qnty,b.rate,b.amount from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id=$update_trans_issue_id and b.entry_form=306 and a.item_category=2");

		$updateID_array_pre_trans = array();
		$update_data_trans_pre_mrr = array();
		foreach($from_mrr_trans_sql as $result)
		{
			$adjBalance = $result[csf("balance_qnty")]+$result[csf("issue_qnty")];
			$adjAmount = $result[csf("balance_amount")]+$result[csf("amount")];
			$updateID_array_pre_trans[]=$result[csf("id")]; 
			$update_data_trans_pre_mrr[$result[csf("id")]]=explode("*",("".$adjBalance."*".$adjAmount."*'".$user_id."'*'".$pc_date_time."'"));
			
		}

		$product_sql =  sql_select("select id, current_stock ,avg_rate_per_unit ,stock_value from product_details_master where id in ($previous_from_prod_id , $previous_to_prod_id )");

		foreach ($product_sql as  $val) 
		{
			$product_arr[$val[csf("id")]]["current_stock"] = $val[csf("current_stock")];
			$product_arr[$val[csf("id")]]["avg_rate_per_unit"] = $val[csf("avg_rate_per_unit")];
			$product_arr[$val[csf("id")]]["stock_value"] = $val[csf("stock_value")];
		}


		$field_product_arr_up = "current_stock*avg_rate_per_unit*stock_value*updated_by*update_date";

		$from_product_qnty =  $product_arr[$previous_from_prod_id]["current_stock"]+ $hidden_transfer_qnty;
		$from_product_rate =  $product_arr[$previous_from_prod_id]["avg_rate_per_unit"]*1;
		$from_product_amount =  $product_arr[$previous_from_prod_id]["stock_value"] + ($hidden_transfer_qnty*$from_product_rate);

		$updateID_array[]=$previous_from_prod_id; 
		$data_product_arr_up[$previous_from_prod_id]=explode("*",("".$from_product_qnty."*".$from_product_rate."*".$from_product_amount."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));

		if($variable_auto_rcv!=2)
		{
			$to_product_qnty =  $product_arr[$previous_to_prod_id]["current_stock"]- $hidden_transfer_qnty;
			$to_product_rate =  $product_arr[$previous_to_prod_id]["avg_rate_per_unit"]*1;
			$to_product_amount =  $product_arr[$previous_to_prod_id]["stock_value"] - ($hidden_transfer_qnty*$to_product_rate);

			if($to_product_qnty <=0){
				$to_product_rate=0;
				$to_product_amount=0;
			}

			$updateID_array[]=$previous_to_prod_id; 
			$data_product_arr_up[$previous_to_prod_id]=explode("*",("".$to_product_qnty."*".$to_product_rate."*".$to_product_amount."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
		}

		//echo "10**update inv_item_transfer_dtls set status_active=0, is_deleted=1, updated_by=". $_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where id =$update_dtls_id";die;
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$flag=1;
		$rID=execute_query("update inv_item_transfer_dtls set status_active=0, is_deleted=1, updated_by=". $_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where id =$update_dtls_id");
		if($flag==1) 
		{ 
			if($rID) $flag=1; else $flag=0; 
		}
		//echo "10**$flag##$rID";oci_rollback($con);disconnect($con);die;
		if($variable_auto_rcv!=2)
		{
			$rID2=execute_query("update inv_transaction set status_active=0, is_deleted=1, updated_by=". $_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where id in ($update_trans_issue_id, $update_trans_recv_id)");
			if($flag==1) { if($rID2) $flag=1; else $flag=0; }
		}

		//echo "10**$flag##$rID##$rID2";oci_rollback($con);disconnect($con);die;


		if(count($update_data_trans_pre_mrr)>0)
		{
			$rID3=execute_query(bulk_update_sql_statement("inv_transaction","id",$field_array_trans_pre_mrr,$update_data_trans_pre_mrr,$updateID_array_pre_trans));
			$query3 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id=$update_trans_issue_id and entry_form=14");
			if($flag==1) 
			{
				if($rID3) $flag=1; else $flag=0; 
			}
			if($flag==1) 
			{
				if($query3) $flag=1; else $flag=0; 
			}
		}

		/*$rID3=sql_update("inv_transaction",$field_mrr_array,$update_mrr_array,"id",$from_mrr_id,1);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		}*/

		//echo "10**$flag##$rID##$rID2##$rID3";oci_rollback($con);disconnect($con);die;

		//echo "10**".bulk_update_sql_statement("product_details_master","id",$field_product_arr_up,$data_product_arr_up,$updateID_array);oci_rollback($con); disconnect($con);die;
		$rID4=execute_query(bulk_update_sql_statement("product_details_master","id",$field_product_arr_up,$data_product_arr_up,$updateID_array));
		if($flag==1) 
		{
			if($rID4) $flag=1; else $flag=0; 
		} 

		//echo "10**$flag##$rID##$rID2##$rID3##$rID4";oci_rollback($con);disconnect($con);die;
		if($variable_auto_rcv!=2)
		{
			$rID5=execute_query("update order_wise_pro_details set status_active=0, is_deleted=1, updated_by=". $_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where trans_id in ($update_trans_issue_id, $update_trans_recv_id)");
			if($flag==1) { if($rID5) $flag=1; else $flag=0; }
		}

		//echo "10**$flag##$rID##$rID2##$rID3##$rID4##$rID5";oci_rollback($con);disconnect($con);die;
		

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "7**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);  
				echo "2**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id);
			}
			else
			{
				oci_rollback($con);
				echo "7**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id);
			}
		}
		disconnect($con);
		die;	
	}
}


if($action=="finish_fabric_transfer_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	
	$sql="select id, transfer_system_id, challan_no, transfer_date, transfer_criteria, item_category, to_company, from_order_id, to_order_id, item_category,location_id,to_location_id from inv_item_transfer_mst a where id='$data[1]' and company_id='$data[0]'";
	//echo $sql;die;
	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$prod_sql=sql_select("select id, product_name_details, unit_of_measure from product_details_master where item_category_id=2");
	foreach($prod_sql as $row)
	{
		$product_arr[$row[csf("id")]]=$row[csf("product_name_details")];
		$uom_arr[$row[csf("id")]]=$row[csf("unit_of_measure")];
	}

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
				<td width="120"><strong>Transfer ID :</strong></td><td width="175px"><? echo $dataArray[0][csf('transfer_system_id')]; ?></td>
				<td width="130"><strong>Transfer Criteria:</strong></td> <td width="175px"><? echo $item_transfer_criteria[$dataArray[0][csf('transfer_criteria')]]; ?></td>
				<td width="125"><strong>To Company</strong></td><td width="175px"><? echo $company_library[$dataArray[0][csf('to_company')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Transfer Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
				<td><strong>Challan No.:</strong></td><td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
				<td><strong>Item Category:</strong></td> <td width="175px"><? echo $item_category[$dataArray[0][csf('item_category')]]; ?></td>
			</tr>
		</table>
		<br>
		<div style="width:100%;">
			<table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<th width="40">SL</th>
					<th width="100" >From Store</th>
					<th width="100" >To Store</th>
					<th width="250" >Item Description</th>
					<th width="50" >UOM</th>
					<th width="100" >Transfered Qnty</th>
					<th width="110" >Color</th> 
				</thead>
				<tbody> 
					<?
					$sql_dtls="select id, from_store, to_store, from_prod_id, transfer_qnty, color_id from inv_item_transfer_dtls where mst_id='$data[1]' and status_active=1 and is_deleted=0";
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
							<td><? echo $store_library[$row[csf("from_store")]]; ?></td>
							<td><? echo $store_library[$row[csf("to_store")]]; ?></td>
							<td><? echo $product_arr[$row[csf("from_prod_id")]]; ?></td>
							<td align="center"><? echo $unit_of_measurement[$uom_arr[$row[csf("from_prod_id")]]]; ?></td>
							<td align="right"><? echo number_format($row[csf("transfer_qnty")],2); ?></td>
							<td><? echo $color_arr[$row[csf("color_id")]]; ?></td>
						</tr>
						<? 
						$i++; 
					} 
					?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="5" align="right"><strong>Total :</strong></td>
						<td align="right"><?php echo number_format($transfer_qnty_sum,2); ?></td>
						<td align="right"><?php //echo $req_qny_edit_sum; ?></td>
					</tr>                           
				</tfoot>
			</table>
			<br>
			<?
			echo signature_table(23, $data[0], "900px");
			?>
		</div>
	</div>   
	<?	
	exit();
}

if($action=="finish_fabric_transfer_print_2")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst where is_deleted=0 and status_active=1","id","batch_no");
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$location_library=return_library_array( "select id, location_name from lib_location", "id", "location_name");
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	
	$prod_sql=sql_select("select id, product_name_details, unit_of_measure from product_details_master where item_category_id=2");
	foreach($prod_sql as $row)
	{
		$product_arr[$row[csf("id")]]=$row[csf("product_name_details")];
		$uom_arr[$row[csf("id")]]=$row[csf("unit_of_measure")];
	}

	$sql="select a.id, a.transfer_system_id, a.challan_no, a.transfer_date, a.transfer_criteria, b.batch_id, a.item_category, a.location_id, a.to_company, a.from_store_id, a.to_store_id, b.from_order_id, b.to_order_id, a.item_category, a.location_id, b.from_store, b.to_store, b.from_prod_id, sum(b.transfer_qnty) as transfer_qnty, b.uom,b.remarks  
	from inv_item_transfer_mst a,inv_item_transfer_dtls b 
	where a.id=b.mst_id and a.id='$data[1]' and a.company_id='$data[0]' and a.status_active=1 and a.is_deleted=0 
	group by a.id, a.transfer_system_id, a.challan_no, a.transfer_date, a.transfer_criteria,b.batch_id, a.item_category, a.location_id, a.to_company, a.from_store_id, a.to_store_id, b.from_order_id, b.to_order_id, a.item_category, a.location_id, b.from_store, b.to_store, b.from_prod_id, b.uom,b.remarks  
	order by b.from_prod_id";
	//echo $sql;die;
	$dataArray=sql_select($sql);
	$orderID="";$prodID="";
	foreach ($dataArray as $row) {
		$orderidArr[$row[csf('from_order_id')]]=$row[csf('from_order_id')];
		$orderID.=$row[csf('from_order_id')].',';
		$prodID.=$row[csf('from_prod_id')].',';
		$batchID.=$row[csf('batch_id')].',';
		
	}
	$orderID=chop($orderID,",");
	$prodID=chop($prodID,",");
	$batchID=chop($batchID,",");
	//echo "select a.id ,a.po_number,b.style_ref_no from wo_po_break_down a,wo_po_details_master b  where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted =0 and a.id in(".implode(",",$orderidArr).")";die;
	$sql_order= sql_select("select a.id ,a.po_number, b.style_ref_no from wo_po_break_down a,wo_po_details_master b  where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted =0 and a.id in(".implode(",",$orderidArr).")");
	foreach ($sql_order as  $row) {
		$orderArr[$row[csf('id')]]['po_number']=$row[csf('po_number')];
		$orderArr[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
	}

	$sql_dtls ="select   a.company_id, a.supplier_id,  b.buyer_id, c.po_breakdown_id as order_id, d.id as prod_id, d.product_name_details, d.color , f.booking_no as booking_no_batch
	from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, product_details_master d , inv_transaction e, pro_batch_create_mst f
	where a.id=b.mst_id and b.id = c.dtls_id and b.prod_id=d.id  and c.trans_id=e.id and b.batch_id=f.id and a.item_category=2  and a.entry_form=37 and c.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id is not null and c.po_breakdown_id in($orderID) and d.id in($prodID) and f.id in($batchID) group by a.company_id, a.supplier_id, b.buyer_id, c.po_breakdown_id, d.id, d.product_name_details, d.color , f.booking_no order by d.id";
	//echo $sql_dtls;
	$sql_result= sql_select($sql_dtls);

	$tarnsferArry=array();
	foreach($sql_result as $row)
	{
		$tarnsferArry[$row[csf('order_id')]][$row[csf('prod_id')]]['buyer_id']=$row[csf('buyer_id')];
		$tarnsferArry[$row[csf('order_id')]][$row[csf('prod_id')]]['booking_no_batch']=$row[csf('booking_no_batch')];
		$tarnsferArry[$row[csf('order_id')]][$row[csf('prod_id')]]['color']=$row[csf('color')];
		$tarnsferArry[$row[csf('order_id')]][$row[csf('prod_id')]]['product_name_details']=$row[csf('product_name_details')];
		$tarnsferArry[$row[csf('order_id')]][$row[csf('prod_id')]]['batch_id']=$batch_arr[$row[csf('batch_id')]];
	}
	$tableWidth = 1300;
	?>
	<div style="width:<? echo $tableWidth; ?>px;">
		<table width="<? echo $tableWidth; ?>" cellspacing="0" align="right">
			<tr>
				<td align="left"><img src="../../<? echo $image_location; ?>" height="70" width="180"></td>
				<td  align="left" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
				<td></td>
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
						City No: <? echo $result[csf('city')];?> <br>
						Zip Code: <? echo $result[csf('zip_code')]; ?> 
						Province No: <?php echo $result[csf('province')];?> 
						Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
						Email Address: <? echo $result[csf('email')];?> 
						Website No: <? echo $result[csf('website')];
					}
					?> 

				</td>  
			</tr>
			<tr><td></td></tr>
			<tr><td></td></tr>
			<tr><td></td></tr>
			<tr>
				<td colspan="6" align="center" style="font-size:x-large;"><strong><u><? echo $data[2]; ?> Report</u></strong></td>
			</tr>
		</table>
		<table width="<? echo $tableWidth; ?>" cellspacing="0" align="right" style="margin-top: 30px;">
			<tr>
				<td width="130"><strong>Transfer Criteria</strong></td> <td width="175px"><strong>:<? echo $item_transfer_criteria[$dataArray[0][csf('transfer_criteria')]]; ?></strong></td>
				<td><strong>Print Date and Time</strong></td><td width="175px"><strong>:<? echo date("d/m/Y") ." : ". date("h:i:sa"); ?></strong></td>
			</tr>
			<tr style="height: 20px;">
			</tr>

			<tr>
				<td width="125"><strong>From</strong></td><td width="175px">: <? echo $store_library[$dataArray[0][csf('from_store')]]; ?></td>
				<td width="125"><strong>To Company</strong></td><td width="175px">: <? echo $company_library[$dataArray[0][csf('to_company')]]; ?></td>
			</tr>
			<tr>
				<td width="120"><strong>Transfer ID</strong></td><td width="175px"><strong>: <? echo $dataArray[0][csf('transfer_system_id')]; ?></strong></td>
				<td width="125"><strong>To Store</strong></td><td width="175px">: <? echo $store_library[$dataArray[0][csf('to_store')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Transfer Date</strong></td><td width="175px">: <? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
			</tr>
			<tr>
				<td><strong>Challan No</strong></td><td width="175px">: <? echo $dataArray[0][csf('challan_no')]; ?></td>
			</tr>	
			<tr>
				<td><strong>Item Category</strong></td> <td width="175px">: <? echo $item_category[$dataArray[0][csf('item_category')]]; ?></td>
			</tr>
		</table>
		<br>
		<div  style="width:<? echo $tableWidth; ?>px;">
			<table align="right" cellspacing="0" width="1300"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<th width="30">SL</th>
					<th width="100">Buyer</th>
					<th width="180">Order</th>
					<th width="100">Style</th>
					<th width="160">Fab. Booking</th>
					<th width="100">Batch</th>
					<th width="100">Fab. Color</th> 
					<th width="350">Item Description</th> 
					<th width="100">Fab. WT</th> 
					<th width="50">UOM</th> 
					<th>Remarks</th> 
				</thead>
				<tbody> 

					<?

	//$sql_dtls="select id, from_store, to_store, from_prod_id, transfer_qnty, color_id from inv_item_transfer_dtls where mst_id='$data[1]' and status_active=1 and is_deleted=0";


					$i=1;
					foreach($dataArray as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="30" align="center"><? echo $i; ?></td>
							<td align="center" width="100"><? echo $buyer_library[$tarnsferArry[$row[csf('from_order_id')]][$row[csf('from_prod_id')]]['buyer_id']]; ?></td>
							<td align="center" width="100" title="<? echo $order_id; ?>"><? echo $orderArr[$row[csf('from_order_id')]]['po_number'] ; ?></td>
							<td align="center" width="100"><? echo $orderArr[$row[csf('from_order_id')]]['style_ref_no']; ?></td>
							<td align="center" width="180"><? echo $tarnsferArry[$row[csf('from_order_id')]][$row[csf('from_prod_id')]]['booking_no_batch']; ?></td>
							<td align="center" width="100"><? echo $batch_arr[$row[csf('batch_id')]]; ?></td>
							<td align="center" width="100"><? echo $color_arr[$tarnsferArry[$row[csf('from_order_id')]][$row[csf('from_prod_id')]]['color']]; ?></td>
							<td align="center" width="250"><? echo $tarnsferArry[$row[csf('from_order_id')]][$row[csf('from_prod_id')]]['product_name_details']; ?></td>
							<td align="right" width="100"><? echo $total_trnf=$row[csf('transfer_qnty')];  ?></td>
							<td align="center" width="50"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
							<td align="center"><? echo $row[csf('remarks')]; ?></td>
						</tr>
						<?
						$total_transfer_qty+=$total_trnf;
						$i++; } ?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="8" align="right"><strong>Total :</strong></td>
							<td align="right"><?php echo $total_transfer_qty; ?></td>
							<td align="right"><?php //echo $req_qny_edit_sum; ?></td>
							<td align="right"></td>
						</tr>                           
					</tfoot>
				</table>
				<br>
				<?
				echo signature_table(23, $data[0], "1100px");
				?>
			</div>
		</div>   
		<?	
		exit();
	}

if($action=="finish_fabric_transfer_print_3") // Print 2
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	
	$sql="select id, transfer_system_id, challan_no, transfer_date, transfer_criteria, item_category, to_company, from_order_id, to_order_id, item_category,location_id,to_location_id from inv_item_transfer_mst a where id='$data[1]' and company_id='$data[0]'";
	//echo $sql;die;
	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$prod_sql=sql_select("select id, product_name_details, unit_of_measure from product_details_master where item_category_id=2");
	foreach($prod_sql as $row)
	{
		$product_arr[$row[csf("id")]]=$row[csf("product_name_details")];
		$uom_arr[$row[csf("id")]]=$row[csf("unit_of_measure")];
	}

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
						<? if($result['plot_no']) echo $result['plot_no'].","; ?> 
						<? if($result['level_no']) echo $result['level_no'].",";?>
						<? if($result['road_no']) echo $result['road_no'].","; ?> 
						<? if($result['block_no']) echo $result['block_no'].",";?> 
						<? if($result['city']) echo $result['city'].",";?> 
						<? if($result['zip_code']) echo $result['zip_code'].","; ?> 
						<? if($result['province']) echo $result['province'].",";?> 
						<? if($result['country_id']) echo $country_arr[$result['country_id']].","; ?><br> 
						<? if($result['email']) echo $result['email'].",";?> 
						<? if($result['website']) echo $result['website'];
					}
					?> 
				</td>  
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo $data[2]; ?> Report</u></strong></td>
			</tr>
			<tr>
				<td width="120"><strong>Transfer ID :</strong></td><td width="175px"><? echo $dataArray[0][csf('transfer_system_id')]; ?></td>
				<td width="130"><strong>Transfer Criteria:</strong></td> <td width="175px"><? echo $item_transfer_criteria[$dataArray[0][csf('transfer_criteria')]]; ?></td>
				<td width="125"><strong>To Company</strong></td><td width="175px"><? echo $company_library[$dataArray[0][csf('to_company')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Transfer Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
				<td><strong>Challan No.:</strong></td><td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
				<td><strong>Item Category:</strong></td> <td width="175px"><? echo $item_category[$dataArray[0][csf('item_category')]]; ?></td>
			</tr>
		</table>
		<br>
		<div style="width:100%; margin-left: 560px;">
			<table align="right" cellspacing="0" width="1460"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<th width="40">SL</th>
					<th width="160" >From</th>
					<th width="160" >To</th>
					<th width="100" >Batch/Lot No</th>
					<th width="250" >Item Description</th>
					<th width="50" >UOM</th>
					<th width="100" >Transfered Qnty</th>
					<th width="110" >Color</th>
					<th width="100" >From Store</th>
					<th width="100" >To Store</th>
					<th width="140" >Remarks</th> 
				</thead>
				<tbody> 
					<?
					$sql_dtls="select from_store, to_store, from_prod_id, transfer_qnty, color_id,from_order_id,to_order_id,remarks,to_batch_id from inv_item_transfer_dtls where mst_id='$data[1]' and status_active=1 and is_deleted=0 group by from_store, to_store, from_prod_id, transfer_qnty, color_id,from_order_id,to_order_id,remarks,to_batch_id";
					$sql_result= sql_select($sql_dtls);

					$order_no = $sample_no = array();
					foreach ($sql_result as $val) 
					{
						$all_batch_arr[$val[csf("to_batch_id")]] = $val[csf("to_batch_id")];
						if($dataArray[0][csf('transfer_criteria')] == 6)
						{
							$order_no[] = $val[csf('from_order_id')];
							$sample_no[] = $val[csf('to_order_id')];
						}
						else if($dataArray[0][csf('transfer_criteria')] == 7)
						{
							$order_no[] = $val[csf('to_order_id')];
							$sample_no[] = $val[csf('from_order_id')];
						}
						else
						{
							$sample_no[] = $val[csf('to_order_id')];
							$sample_no[] = $val[csf('from_order_id')];
						}
					}

					$batch_sql=sql_select("select id, batch_no from pro_batch_create_mst where id in (".implode(',', $all_batch_arr).")");
					foreach($batch_sql as $row)
					{
						$batch_arr[$row[csf("id")]]=$row[csf("batch_no")];
					}

					if(!empty($order_no))
					{ 
						$job_sql = sql_select("select a.id ,a.po_number, b.job_no, b.style_ref_no, b.buyer_name as buyer_id, c.buyer_name,a.grouping from wo_po_break_down a,wo_po_details_master b, lib_buyer c where a.status_active=1 and a.is_deleted =0 and a.job_no_mst = b.job_no and b.buyer_name = c.id and a.id in (".implode(",",$order_no).")");
						foreach ($job_sql as $val) 
						{
							$order_name_arr[$val[csf("id")]]["po_number"] = $val[csf("po_number")];
							$order_name_arr[$val[csf("id")]]["job_no"] = $val[csf("job_no")];
							$order_name_arr[$val[csf("id")]]["style_ref_no"] = $val[csf("style_ref_no")];
							$order_name_arr[$val[csf("id")]]["buyer_name"] = $val[csf("buyer_name")];
							$order_name_arr[$val[csf("id")]]["buyer_id"] = $val[csf("buyer_id")];
							$order_name_arr[$val[csf("id")]]["grouping"] = $val[csf("grouping")];
						}
					}

					if(!empty($sample_no))
					{
						$sample_sql = sql_select("select a.id, a.booking_no, a.buyer_id, b.buyer_name from wo_non_ord_samp_booking_mst a, lib_buyer b where a.status_active =1 and a.is_deleted = 0 and a.buyer_id = b.id and  b.status_active=1 and b.is_deleted =0 and a.id in (".implode(",",$sample_no).")");

						foreach ($sample_sql as $val) 
						{
							$sample_name_arr[$val[csf("id")]]["booking_no"] = $val[csf("booking_no")];
							$sample_name_arr[$val[csf("id")]]["buyer_name"] = $val[csf("buyer_name")];
						}
					}

					$booking_styleRef_sql=sql_select("select id, grouping from wo_non_ord_samp_booking_mst where id in(".implode(",",$sample_no).")");
					foreach($booking_styleRef_sql as $row)
					{
						$booking_styleRef_arr[$row[csf("id")]]=$row[csf("grouping")];
					}

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
							<td>
								<?
								if($dataArray[0][csf('transfer_criteria')] == 6)
								{
									echo "Ref: " . $order_name_arr[$row[csf("from_order_id")]]["grouping"]."<br/>";
									echo "O: " . $order_name_arr[$row[csf("from_order_id")]]["po_number"];
								}
								else if($dataArray[0][csf('transfer_criteria')] == 7)
								{
									echo "R: " . $booking_styleRef_arr[$row[csf("from_order_id")]]."<br/>";
									echo "B: " . $sample_name_arr[$row[csf("from_order_id")]]["booking_no"];
								}
								else
								{
									echo "R: " . $booking_styleRef_arr[$row[csf("from_order_id")]]."<br/>";
									echo "B: " . $sample_name_arr[$row[csf("from_order_id")]]["booking_no"];
								}
								?>
							</td>
							<td>
								<?
								if($dataArray[0][csf('transfer_criteria')] == 6)
								{
									echo "R: " . $booking_styleRef_arr[$row[csf("to_order_id")]]."<br/>";
									echo "B: " . $sample_name_arr[$row[csf("to_order_id")]]["booking_no"];
								}
								else if($dataArray[0][csf('transfer_criteria')] == 7)
								{
									echo "Ref: " . $order_name_arr[$row[csf("to_order_id")]]["grouping"]."<br/>";
									echo "O: " . $order_name_arr[$row[csf("to_order_id")]]["po_number"];
								}
								else
								{
									echo "R: " . $booking_styleRef_arr[$row[csf("to_order_id")]]."<br/>";
									echo "B: " . $sample_name_arr[$row[csf("to_order_id")]]["booking_no"];
								}
								?>
							</td>
							<td><? echo $batch_arr[$row[csf("to_batch_id")]]; ?></td>
							<td><? echo $product_arr[$row[csf("from_prod_id")]]; ?></td>
							<td align="center"><? echo $unit_of_measurement[$uom_arr[$row[csf("from_prod_id")]]]; ?></td>
							<td align="right"><? echo number_format($row[csf("transfer_qnty")],2); ?>&nbsp;</td>
							<td><? echo $color_arr[$row[csf("color_id")]]; ?></td>
							<td><? echo $store_library[$row[csf("from_store")]]; ?></td>
							<td><? echo $store_library[$row[csf("to_store")]]; ?></td>
							<td><? echo $row[csf("remarks")]; ?></td>
						</tr>
						<? 
						$i++; 
					} 
					?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="6" align="right"><strong>Total :</strong></td>
						<td align="right"><?php echo number_format($transfer_qnty_sum,2); ?>&nbsp;</td>
						<td align="right"><?php //echo $req_qny_edit_sum; ?></td>
						<td align="right"><?php //echo $req_qny_edit_sum; ?></td>
					</tr>                           
				</tfoot>
			</table>
			<br>
			<?
			//echo signature_table(23, $data[0], "1460px");
			?>
		</div>
		<div id="signature_bottom">
			<? echo signature_table(306, $data[0], "1460px",$template_id); ?>
		</div> 
	</div>   
	<?	
	exit();
}
if($action=="finish_fabric_transfer_print_4") // Print 3
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$location_library=return_library_array( "select id, location_name from lib_location", "id", "location_name");
	$prod_sql=sql_select("select id, product_name_details, unit_of_measure from product_details_master where item_category_id=2");
	foreach($prod_sql as $row)
	{
		$product_arr[$row[csf("id")]]=$row[csf("product_name_details")];
		$uom_arr[$row[csf("id")]]=$row[csf("unit_of_measure")];
	}

	$sql="SELECT id, transfer_system_id, challan_no, transfer_date, transfer_criteria, item_category, to_company, from_order_id, to_order_id, item_category,location_id, to_location_id from inv_item_transfer_mst a where id='$data[1]' and company_id='$data[0]'";
	//echo $sql;die;
	$dataArray=sql_select($sql);

	$sql_dtls="SELECT from_store, to_store, from_prod_id, transfer_qnty, color_id,from_order_id,to_order_id,remarks,batch_id from inv_item_transfer_dtls where mst_id='$data[1]' and status_active=1 and is_deleted=0 group by from_store, to_store, from_prod_id, transfer_qnty, color_id,from_order_id,to_order_id,remarks,batch_id";
	// echo $sql_dtls;
	$sql_result= sql_select($sql_dtls);
	$batchNoID=$sql_result[0][csf('batch_id')];

	?>
	<div style="width:930px;">
		<table width="900" cellspacing="0" align="right">
			<tr>
				<td colspan="6" align="center" style="font-size:x-large"><strong><u>Knit Finish Fabric Transfer Entry For Sample Challan</u></strong></td>
			</tr>
			<tr>
				<td width="140"><strong></strong></td><td width="175px"></td>
				<td width="100"><strong></strong></td> <td width="145px"></td>
				<td width="165"><strong>Print Date and Time :</td><td width="195px"><? 
				$date = date('d-m-Y : h.i.s a');
				echo $date; ?></strong></td>
			</tr>
			<tr>
				<td><strong>Transfer Criteria :</strong></td><td width="175px"><? echo $item_transfer_criteria[$dataArray[0][csf('transfer_criteria')]]; ?></td>
				<td><strong></strong></td> <td width="165px"></td>
				<td><strong>From Company :</strong></td><td width="185px"><? echo $company_library[$data[0]]; ?></td>
			</tr>
			<tr>
				<td><strong>Transfer ID :</strong></td><td width="175px"><? echo $dataArray[0][csf('transfer_system_id')]; ?></td>
				<td><strong></strong></td> <td width="165px"></td>
				<td><strong>From Location :</strong></td><td width="185px"><? echo $location_library[$dataArray[0][csf('location_id')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Transfer Date :</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
				<td><strong></strong></td><td width="165px"></td>
				<td><strong>To Company :</strong></td><td width="185px"><? echo $company_library[$dataArray[0][csf('to_company')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Item Category :</strong></td> <td width="175px"><? echo $item_category[$dataArray[0][csf('item_category')]]; ?></td>
				<td><strong></strong></td><td width="165px"></td>
				<td><strong>To Location :</strong></td> <td width="185px"><? echo $location_library[$dataArray[0][csf('to_location_id')]]; ?></td>
			</tr>
		</table>
		<br>
		<div style="width:100%; margin-left: 400px;">
			<table align="right" cellspacing="0" width="1300"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<th width="30" >SL</th>
					<th width="100" >From Store</th>
					<th width="100" >To Store</th>
					<th width="80">Buyer</th>
					<th width="80">Style</th>
					<th width="80">Fab. Booking</th>
					<th width="40">Batch</th>
					<th width="40">Fab. Color</th>
					<th width="130">Item Description</th>					
					<th width="50">Transfered Qnty</th>
					<th width="20" >UOM</th>
					<th width="100">Remarks</th> 
				</thead>
				<tbody> 
					<?
					

					$batch_sql=sql_select("select id, batch_no from pro_batch_create_mst where id=$batchNoID");
					foreach($batch_sql as $row)
					{
						$batch_arr[$row[csf("id")]]=$row[csf("batch_no")];
					}

					$order_no = $sample_no = array();
					if($dataArray[0][csf('transfer_criteria')] == 6) // Order To Sample
					{
						$order_no[] = $sql_result[0][csf('from_order_id')];
					}
					else // Sample to Sample and sample to order
					{
						$sample_no[] = $sql_result[0][csf('from_order_id')];
					}

					if(!empty($order_no))
					{ 
						$job_sql = sql_select("select a.id ,a.po_number, b.job_no, b.style_ref_no, b.buyer_name as buyer_id, c.buyer_name,a.grouping from wo_po_break_down a,wo_po_details_master b, lib_buyer c where a.status_active=1 and a.is_deleted =0 and a.job_no_mst = b.job_no and b.buyer_name = c.id and a.id in (".implode(",",$order_no).")");
						foreach ($job_sql as $val) 
						{
							$order_name_arr[$val[csf("id")]]["po_number"] = $val[csf("po_number")];
							$order_name_arr[$val[csf("id")]]["job_no"] = $val[csf("job_no")];
							$order_name_arr[$val[csf("id")]]["style_ref_no"] = $val[csf("style_ref_no")];
							$order_name_arr[$val[csf("id")]]["buyer_name"] = $val[csf("buyer_name")];
							$order_name_arr[$val[csf("id")]]["buyer_id"] = $val[csf("buyer_id")];
							$order_name_arr[$val[csf("id")]]["grouping"] = $val[csf("grouping")];
						}
					}
					if(!empty($order_no))
					{ 
						$order_booking_sql=sql_select("select a.id, b.booking_no from wo_po_break_down a, wo_booking_dtls b where a.id=b.po_break_down_id and a.id in(".implode(",",$order_no).") group by  a.id, b.booking_no");
						foreach($order_booking_sql as $row)
						{
							$order_booking_arr[$row[csf("id")]]['booking_no']=$row[csf("booking_no")];
						}
					}

					if(!empty($sample_no))
					{
						$sample_sql = sql_select("select a.id, a.booking_no, a.buyer_id, b.buyer_name from wo_non_ord_samp_booking_mst a, lib_buyer b where a.status_active =1 and a.is_deleted = 0 and a.buyer_id = b.id and  b.status_active=1 and b.is_deleted =0 and a.id in (".implode(",",$sample_no).")");

						foreach ($sample_sql as $val) 
						{
							$sample_name_arr[$val[csf("id")]]["booking_no"] = $val[csf("booking_no")];
							$sample_name_arr[$val[csf("id")]]["buyer_name"] = $val[csf("buyer_name")];
						}
					}

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
							<td><? echo $store_library[$row[csf("from_store")]]; ?></td>
							<td><? echo $store_library[$row[csf("to_store")]]; ?></td>
							<td>
								<? // td for buyer
								if($dataArray[0][csf('transfer_criteria')] == 6) // Order To Sample
								{
									echo $order_name_arr[$row[csf("from_order_id")]]["buyer_name"];
								}
								else // Sample to Sample and sample to order
								{
									echo $sample_name_arr[$row[csf("from_order_id")]]["buyer_name"];
								}
								?>
							</td>
							<td>
								<? // td for style_ref_no
								if($dataArray[0][csf('transfer_criteria')] == 6) // Order To Sample
								{
									echo $order_name_arr[$row[csf("from_order_id")]]["style_ref_no"];
								}
								else // Sample to Sample and sample to order
								{
									echo "";
								}
								?>
							</td>
							<td title="<? echo 'From Order/SMN ID: '.$row[csf("from_order_id")]; ?>">
								<? // td for booking_no
								if($dataArray[0][csf('transfer_criteria')] == 6) // Order To Sample
								{
									echo $order_booking_arr[$row[csf("from_order_id")]]["booking_no"];
								}
								else // Sample to Sample and sample to order
								{
									echo $sample_name_arr[$row[csf("from_order_id")]]["booking_no"];
								}
								?>
							</td>
							<td><? echo $batch_arr[$row[csf("batch_id")]]; ?></td>
							<td><? echo $color_arr[$row[csf("color_id")]]; ?></td>
							<td><? echo $product_arr[$row[csf("from_prod_id")]]; ?></td>
							<td align="right"><? echo $row[csf("transfer_qnty")]; ?>&nbsp;</td>
							<td align="center"><? echo $unit_of_measurement[$uom_arr[$row[csf("from_prod_id")]]]; ?></td>
							<td><? echo $row[csf("remarks")]; ?></td>
						</tr>
						<? 
						$i++; 
					} 
					?>
				</tbody>
			</table>
			<br>
			<?
			//echo signature_table(23, $data[0], "1460px");
			?>
		</div>
	</div>   
	<?	
	exit();
}

	?>
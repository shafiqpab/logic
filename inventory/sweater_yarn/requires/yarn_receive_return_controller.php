<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------

if($action=="mrr_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>

	<script>
		function js_set_value(mrr)
		{
			$("#hidden_recv_number").val(mrr); // mrr number
			parent.emailwindow.hide();
		}
	</script>

</head>

<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<table width="800" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
				<thead>
					<tr>
						<th>Supplier</th>
						<th>Search By</th>
						<th align="center" id="search_by_td_up">Please Enter MRR No</th>
						<th>Date Range</th>
						<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>
					</tr>
				</thead>
				<tbody>
					<tr class="general">
						<td>
							<?
							echo create_drop_down( "cbo_supplier", 150, "select c.supplier_name,c.id from lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and b.party_type=2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
							?>
						</td>
						<td>
							<?
							$search_by = array(1=>'MRR No',2=>'Challan No',3=>'Lot No');
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../../') ";
							echo create_drop_down( "cbo_search_by", 120, $search_by,"",0, "--Select--", "",$dd,0 );
							?>
						</td>
						<td width="" align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td align="center">
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" />
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" />
						</td>
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>, 'create_mrr_search_list_view', 'search_div', 'yarn_receive_return_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td align="center" height="40" valign="middle" colspan="5">
							<? echo load_month_buttons(1);  ?>
							<!-- Hidden field here-->
							<input type="hidden" id="hidden_recv_number" value="" />

						</td>
					</tr>
				</tbody>
			</tr>
		</table>
		<div align="center" style="margin-top:10px" valign="top" id="search_div"> </div>
	</form>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if ($action=="load_drop_down_store")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_store_name", 80, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data[0]' and b.category_type in(1) and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1,"--Select store--",0,"load_drop_down('requires/yarn_receive_return_controller', this.value+'_'+$data[0], 'load_drop_floor','floor_td');load_drop_down('requires/yarn_receive_return_controller', this.value+'_'+$data[0], 'load_drop_room','room_td');load_drop_down('requires/yarn_receive_return_controller', this.value+'_'+$data[0], 'load_drop_rack','rack_td');load_drop_down('requires/yarn_receive_return_controller', this.value+'_'+$data[0], 'load_drop_shelf','shelf_td');load_drop_down('requires/yarn_receive_return_controller', this.value+'_'+$data[0], 'load_drop_bin','bin_td');",0);
	exit();
}

if($action == "load_drop_floor")
{
	// echo "string";die;
	$data = explode("_", $data);
	$store_id=$data[0];
	$company_id = $data[1];
	echo create_drop_down( "cbo_floor", "80", "select b.floor_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.floor_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name","floor_id,floor_room_rack_name", 1, "--Select Floor--", 0, "",0 );
}

if($action == "load_drop_room")
{
	$data = explode("_", $data);
	$store_id=$data[0];
	$company_id = $data[1];

	echo create_drop_down( "cbo_room", "80", "select b.room_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.room_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.room_id,a.floor_room_rack_name order by a.floor_room_rack_name","room_id,floor_room_rack_name", 1, "--Select Room--", 0, "",0 );
}

if($action == "load_drop_rack")
{
	$data = explode("_", $data);
	$store_id=$data[0];
	$company_id = $data[1];
	echo create_drop_down( "txt_rack", '80', "select b.rack_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.rack_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,a.floor_room_rack_name order by a.floor_room_rack_name","rack_id,floor_room_rack_name", 1, "--Select Rack--", 0, "",0 );
}

if($action == "load_drop_shelf")
{
	$data = explode("_", $data);
	$store_id=$data[0];
	$company_id = $data[1];
	echo create_drop_down( "txt_shelf", '80', "select b.shelf_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.shelf_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name","shelf_id,floor_room_rack_name", 1, "--Select Shelf--", 0, "",0 );
}

if($action == "load_drop_bin")
{
	$data = explode("_", $data);
	$store_id=$data[0];
	$company_id = $data[1];
	echo create_drop_down( "cbo_bin", '80', "select b.bin_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.bin_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.bin_id,a.floor_room_rack_name order by a.floor_room_rack_name","bin_id,floor_room_rack_name", 1, "--Select Bin--", 0, "",0 );
}

if($action=="create_mrr_search_list_view")
{
	$ex_data = explode("_",$data);
	$supplier = $ex_data[0];
	$txt_search_by = $ex_data[1];
	$txt_search_common = trim($ex_data[2]);
	$fromDate = $ex_data[3];
	$toDate = $ex_data[4];
	$company = $ex_data[5];

	$sql_cond="";
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==1) // for mrr
		{
			$mrr_cond .= " and a.recv_number LIKE '%$txt_search_common'";
			$trans_cond .= " and a.transfer_system_id LIKE '%$txt_search_common'";
		}
		else if(trim($txt_search_by)==2) // for chllan no
		{
			$mrr_cond .= " and a.challan_no LIKE '%$txt_search_common%'";
			$trans_cond .= " and a.challan_no LIKE '%$txt_search_common%'";
		}
		else if(trim($txt_search_by)==3) // for chllan no
		{
			$mrr_cond .= " and d.lot='$txt_search_common'";
			$trans_cond .= " and d.lot='$txt_search_common'";
		}
	}

	if( $fromDate!="" && $toDate!="" )
	{
		if($db_type==0)
		{
			$mrr_cond .= " and a.receive_date  between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
			$trans_cond .= " and a.transfer_date  between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
		}
		else
		{
			$mrr_cond .= " and a.receive_date  between '".change_date_format($fromDate,'','',1)."' and '".change_date_format($toDate,'','',1)."'";
			$trans_cond .= " and a.transfer_date  between '".change_date_format($fromDate,'','',1)."' and '".change_date_format($toDate,'','',1)."'";
		}
	}
	if(trim($company)!="")
	{
		$mrr_cond .= " and b.company_id='$company'";
		$trans_cond .= " and b.company_id='$company'";
	}
	if(trim($supplier)!=0)
	{
		$mrr_cond .= " and d.supplier_id='$supplier'";
		$trans_cond .= " and d.supplier_id='$supplier'";
	}

	if($db_type==0) $year_field="YEAR(a.insert_date) as year,";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year,";
	else $year_field="";
	
	if($db_type==0)
	{
		$select_prod=" group_concat(d.id) as prod_id";
	}
	else
	{
		$select_prod=" listagg(cast(d.id as varchar(4000)),',') within group(order by d.id) as prod_id";
	}

	$sql = "select a.id as mst_id, a.recv_number_prefix_num,a.recv_number, $year_field d.supplier_id, a.challan_no, a.receive_date, a.receive_basis, sum(b.cons_quantity) as receive_qnty, sum(b.balance_qnty) as balance_qnty, 1 as type ,a.lc_no, d.lot, $select_prod
	from product_details_master d, inv_transaction b, inv_receive_master a
	where d.id=b.prod_id and a.id=b.mst_id and a.entry_form in(248) and b.entry_form in(248) and b.item_category=1 and b.transaction_type in(1) and a.status_active=1 $mrr_cond
	group by a.id, a.recv_number_prefix_num ,a.recv_number, d.supplier_id, a.challan_no, a.receive_date, a.receive_basis, a.insert_date, a.lc_no, d.lot
	HAVING sum(b.balance_qnty)>0";
	//echo $sql;
	$supplier_arr = return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$btb_lc_arr = return_library_array( "select id, lc_number from com_btb_lc_master_details",'id','lc_number');
	$arr=array(2=>$supplier_arr,5=>$btb_lc_arr,7=>$receive_basis_arr);
	echo create_list_view("list_view", "MRR No, Year, Supplier Name, Challan No, Lot, BTB LC, Receive Date, Receive Basis, Receive Qty., Balance Qty ","70,60,180,120,80,90,80,140,90","1080","220",0, $sql , "js_set_value", "mst_id,type,supplier_id,prod_id", "", 1, "0,0,supplier_id,0,0,lc_no,0,receive_basis,0,0", $arr, "recv_number_prefix_num,year,supplier_id,challan_no,lot,lc_no,receive_date,receive_basis,receive_qnty,balance_qnty", "",'','0,0,0,0,0,0,3,0,2,2') ;
	exit();

}

if($action=="populate_data_from_data")
{
	$data_ref=explode("**",$data);
	$mst_id=$data_ref[0];
	$mrr_type=$data_ref[1];
	$supplier_id=$data_ref[2];
	$prod_id=$data_ref[3];

	//echo $mrr_type;die;

	if($mrr_type==1)
	{
		$sql = "select id,recv_number,entry_form,receive_basis,receive_date,challan_no,booking_id,booking_no,issue_id from inv_receive_master where id=$mst_id and entry_form in(248)";
	}
	else
	{
		$sql = "select id, transfer_system_id as recv_number, 10 as entry_form, 0 as receive_basis, transfer_date as receive_date, challan_no, 0 as booking_id, null as booking_no from inv_item_transfer_mst where id=$mst_id";
	}

	//echo $sql;die;
	$res = sql_select($sql);
	foreach($res as $row)
	{
		echo "$('#txt_mrr_no').val('".$row[csf("recv_number")]."');\n";
		echo "$('#txt_received_id').val('".$row[csf("id")]."');\n";
		echo "$('#hdn_issue_id').val('".$row[csf("issue_id")]."');\n";		
		echo "$('#cbo_return_to').val('".$supplier_id."');\n";

		if($row[csf("receive_basis")]==1)
		{
			$pi_no=return_field_value("pi_number","com_pi_master_details","id='".$row[csf("booking_id")]."'");
			$ref_closing_status=return_field_value("ref_closing_status","com_pi_master_details","id='".$row[csf("booking_id")]."'");
			echo "$('#txt_pi_no').val('".$pi_no."');\n";
			echo "$('#pi_id').val('".$row[csf("booking_id")]."');\n";
			echo "$('#txt_pi_no').attr('disabled','disabled');\n";	
			echo "$('#hidden_ref_closing_status').val('".$ref_closing_status."');\n";				
		}
		//right side list view
		echo"show_list_view('".$row[csf("id")]."**".$mrr_type."','show_product_listview','list_product_container','requires/yarn_receive_return_controller','');\n";
	}

	exit();
}

//right side product list create here--------------------//
if($action=="show_product_listview")
{
	$mrr_ref = explode("**",$data);
	$mrr_id=$mrr_ref[0];
	$mrr_type=$mrr_ref[1];
	if($mrr_type==1)
		$transaction_type_cond=" and b.transaction_type in(1,4)";
	else
		$transaction_type_cond=" and b.transaction_type in(5)";
	$sql = "select c.product_name_details, c.current_stock, b.mst_id as mrr_id, b.id as tr_id, c.id as prod_id, b.cons_quantity, b.balance_qnty, b.buyer_id, b.job_no, b.style_ref_no
	from inv_transaction b, product_details_master c
	where b.prod_id=c.id and b.mst_id=$mrr_id and b.item_category=1 and b.entry_form=248 $transaction_type_cond";
	//echo $sql;
	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$result = sql_select($sql);
	$i=1;
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all">
		<thead>
            <tr>
            	<th>SL</th>
                <th>Buyer</th>
                <th>Job</th>
                <th>Style</th>
                <th>Product Name</th>
                <th>Curr.Stock</th>
            </tr>
        </thead>
		<tbody>
			<? foreach($result as $row)
			{
				if($row[csf("balance_qnty")]>0)
				{
					if ($i%2==0)$bgcolor="#E9F3FF";
					else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $row[csf("tr_id")];?>","item_details_form_input","requires/yarn_receive_return_controller")' style="cursor:pointer" >
						<td><? echo $i; ?></td>
						<td><p><? echo $buyer_arr[$row[csf("buyer_id")]]; ?>&nbsp;</p></td>
                        <td><p><? echo $row[csf("job_no")]; ?>&nbsp;</p></td>
                        <td><p><? echo $row[csf("style_ref_no")]; ?>&nbsp;</p></td>
                        <td><p><? echo $row[csf("product_name_details")]; ?>&nbsp;</p></td>
						<td align="right"><? echo number_format($row[csf("balance_qnty")],2,".",""); ?></td>
					</tr>
					<?
					$i++;
				}

			}
			?>
		</tbody>
	</table>
</fieldset>
<?
exit();
}



//child form data input here-----------------------------//
if($action=="item_details_form_input")
{
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$sql = "select b.id as prod_id, b.product_name_details, a.no_of_bags, a.cone_per_bag, a.store_id, a.order_rate, a.order_ile_cost, a.cons_uom, a.cons_rate, a.cons_quantity, a.cons_amount, a.balance_qnty, a.balance_amount, a.mst_id as receive_id, a.transaction_type, a.issue_id, a.buyer_id, a.job_no, a.style_ref_no, b.lot, b.color, a.floor_id, a.room, a.rack, a.self, a.bin_box
	from inv_transaction a, product_details_master b
	where a.id=$data  and a.status_active=1 and a.item_category=1 and transaction_type in(1,4,5) and a.prod_id=b.id and b.status_active=1";

 	// echo $sql;//die;
	$result = sql_select($sql);
	foreach($result as $row)
	{
		echo "$('#cbo_buyer_name').val('".$row[csf("buyer_id")]."');\n";
		echo "$('#txt_job_no').val('".$row[csf("job_no")]."');\n";
		echo "$('#txt_style_no').val('".$row[csf("style_ref_no")]."');\n";
		echo "$('#txt_item_description').val('".$row[csf("product_name_details")]."');\n";
		echo "$('#txt_prod_id').val('".$row[csf("prod_id")]."');\n";
		echo "$('#txt_item_description').attr( 'title', '".$color_arr[$row[csf("color")]]."');\n";
		echo "$('#txt_lot').val('".$row[csf("lot")]."');\n";
		//echo "$('#txt_no_of_bag').val('".$row[csf("no_of_bags")]."');\n";
		//echo "$('#txt_no_of_cone').val('".$row[csf("cone_per_bag")]."');\n";
		echo "$('#cbo_store_name').val('".$row[csf("store_id")]."');\n";
		echo "$('#cbo_floor').val('".$row[csf("floor_id")]."');\n";
		echo "$('#cbo_room').val('".$row[csf("room")]."');\n";
		echo "$('#txt_rack').val('".$row[csf("rack")]."');\n";
		echo "$('#txt_shelf').val('".$row[csf("self")]."');\n";
		echo "$('#cbo_bin').val('".$row[csf("bin_box")]."');\n";
		echo "$('#cbo_store_name').attr('disabled','disabled');\n";
		echo "$('#cbo_floor').attr('disabled','disabled');\n";
		echo "$('#cbo_room').attr('disabled','disabled');\n";
		echo "$('#txt_rack').attr('disabled','disabled');\n";
		echo "$('#txt_shelf').attr('disabled','disabled');\n";
		echo "$('#cbo_bin').attr('disabled','disabled');\n";
		echo "$('#txt_return_qnty').val('');\n";
		echo "$('#txt_receive_qnty').val('". number_format($row[csf("balance_qnty")],2,".","")."');\n";
		echo "$('#cbo_uom').val('".$row[csf("cons_uom")]."');\n";

		if($row[csf("transaction_type")] == 4 || $row[csf("transaction_type")] == 5){
			echo "$('#txt_rate').val('". number_format($row[csf("cons_rate")],4,".","")."');\n";
			$rate = $row[csf("cons_rate")];
		}
		//echo "$('#txt_return_value').val('');\n";
		$yarn_issue_return_sql = "select e.id as rcv_trans_id, e.pi_wo_batch_no, a.id as issue_id, b.id as issue_trans_id 
			from  inv_issue_master a, inv_transaction b, inv_mrr_wise_issue_details c, inv_receive_master d, inv_transaction e 
			where a.id = b.mst_id and b.id = c.issue_trans_id and c.recv_trans_id = e.id and d.id = e.mst_id and b.prod_id in ($all_prod_id) and a.id=$issue_id and a.entry_form = 3 and c.entry_form = 3 and d.item_category=1 and b.status_active = 1 and b.is_deleted = 0";
		if($row[csf("transaction_type")]==4)
		{
			$ord_rate_sql="select c.order_rate from inv_transaction a, inv_mrr_wise_issue_details b, inv_transaction c where a.id=b.issue_trans_id and b.recv_trans_id=c.id and a.prod_id='".$row[csf("prod_id")]."' and a.mst_id='".$row[csf("issue_id")]."' and a.status_active = 1 and a.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0";
			//echo $ord_rate_sql;die;
			$ord_result=sql_select($ord_rate_sql);
			$order_rate=$ord_result[0][csf("order_rate")];
		}
		else
		{
			$order_rate=$row[csf("order_rate")];
		}
		
		echo "$('#order_rate').val('".$order_rate."');\n";
		echo "$('#order_ile_cost').val('".$row[csf("order_ile_cost")]."');\n";

	}
	if($result[0][csf("transaction_type")] == 1){
		
		$rate_data= sql_select("select receive_date,currency_id,exchange_rate from inv_receive_master where id = ".$result[0][csf("receive_id")]." order by id asc");
		$rate = $result[0][csf("order_rate")] * $rate_data[0][csf("exchange_rate")];
		echo "$('#txt_rate').val('".$rate."');\n";
	}

	$return_value = $rate * $result[0][csf("balance_qnty")];
	echo "$('#txt_return_value').val('".number_format($return_value,2,".","")."');\n";
	exit();
}




//data save update delete here------------------------------//
if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$con = connect();
	if($db_type==0)	{ mysql_query("BEGIN"); }


	$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$txt_prod_id and transaction_type in (1,4,5) and store_id = $cbo_store_name and status_active = 1", "max_date");
	if($max_recv_date != "")
	{
		$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
		$return_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_return_date)));
		if ($return_date < $max_recv_date)
		{
			echo "20**Return Date Can not Be Less Than Last Receive Date Of This Lot";
			die;
		}
	}

	$pi_id = str_replace("'","",$pi_id);

	if($pi_id!="" && $pi_id>0)
	{
		
		/*
		////######## As per saeed vai decission this validation off ##########////
		$rcv_rtn_trans_id=str_replace("'","",$update_id);
		$pi_total_rcv_value=return_field_value("sum(order_amount) as pi_total_rcv", "inv_transaction", "pi_wo_batch_no in($pi_id) and receive_basis in(1) and transaction_type in(1) and status_active=1 and is_deleted=0","pi_total_rcv");
		$rcv_rtn_up_cond="";
		if($rcv_rtn_trans_id!="") $rcv_rtn_up_cond=" and id <>$rcv_rtn_trans_id";
		$pi_total_rcv_rtn_value = return_field_value ("sum(cons_quantity*order_rate) as pi_total_rcv_rtn", "inv_transaction", "pi_wo_batch_no in($pi_id) and transaction_type =3 and status_active=1 and is_deleted=0 $rcv_rtn_up_cond","pi_total_rcv_rtn");

		$actual_pi_rcv_value = ($pi_total_rcv_value-$pi_total_rcv_rtn_value);

		$accept_value = return_field_value("sum(current_acceptance_value) as accept_value",  "com_import_invoice_dtls", "status_active=1 and is_deleted=0 and pi_id in($pi_id)", "accept_value");

		$pi_value = return_field_value("sum(net_pi_amount) as pi_value",  "com_pi_item_details", "status_active=1 and is_deleted=0 and pi_id in($pi_id)", "pi_value");
		
		$cumu_accept_value=$actual_pi_rcv_value-$accept_value;
		$order_rate = str_replace("'","",$order_rate);
		$txt_return_qnty = str_replace("'","",$txt_return_qnty);
		$return_value = ($order_rate*$txt_return_qnty);		
		if($return_value>$cumu_accept_value)
		{
			echo "20**Total Payable Value= $actual_pi_rcv_value\nTotal Accp.Value= $accept_value\nAllowed Rtn Value= $cumu_accept_value\nSo current Return value $return_value is not allowed";
			die;
		}*/
	}
	
	$trans_id=str_replace("'","",$update_id);
	$storeId=str_replace("'","",$cbo_store_name);
	$job_no=str_replace("'","",$txt_job_no);
	$up_cond="";
	if($trans_id!="") $up_cond=" and a.id <> $trans_id";
	$sql_transaction = sql_select("select sum((case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end)-(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)) as yarn_qnty 
	from inv_transaction a
	where a.job_no='$job_no' and a.store_id=$storeId and a.company_id=$cbo_company_name and a.prod_id=$txt_prod_id and a.status_active=1 and a.is_deleted=0 and a.item_category=1 and a.entry_form in(248,249,277,381,382) $up_cond");
	$store_stock_quantity=$sql_transaction[0][csf("yarn_qnty")]*1;
	
	
	$allocated_qnty_balance=$allocated_qnty;
	$available_qnty_balance=$available_qnty-$issue_quantity;
	$lot_prev_iss=sql_select("select sum(a.cons_quantity) as cons_quantity from inv_transaction a where a.job_no='$job_no' and a.store_id=$storeId and a.company_id=$cbo_company_name and a.prod_id=$txt_prod_id and a.status_active=1 and a.is_deleted=0 and a.item_category=1 and a.entry_form in(277) and a.transaction_type=2 and a.receive_basis=6 $up_cond");

	$lot_ratio_sql=sql_select("select sum(b.alocated_qty) as alocated_qty  from ppl_cut_lay_mst a,  ppl_cut_lay_prod_dtls b
	where a.status_active=1 and b.status_active=1 and a.id=b.mst_id and a.entry_form=253 and a.job_no='$job_no' and a.store_id=$storeId and a.company_id=$cbo_company_name and b.prod_id=$txt_prod_id");

	$lot_ratio_qnty=($lot_ratio_sql[0][csf("alocated_qty")]-$lot_prev_iss[0][csf("cons_quantity")])*1;
	$store_cu_stock=$store_stock_quantity-$lot_ratio_qnty; // ## store stock - pending allocation qnty;
	$issue_quantity=str_replace("'","",$txt_return_qnty)*1;
	//echo "31**".$issue_quantity .">". $cu_available_qnty .">".$store_cu_stock; die();  Available Qnty = $cu_available_qnty \n
	 
	if($issue_quantity > $store_cu_stock)
	{
		echo "31**Issue quantity can not exceed available quantity or store stock quantity. \n Issue qnty = $issue_quantity \n job against store stock = $store_stock_quantity \n  cumilative allocation = $lot_ratio_qnty \n Available (store stock - cumilative allocation) = $store_cu_stock ";die;
	}

	// check variable settings if allocation is available or not
	$variable_set_allocation = return_field_value("allocation","variable_settings_inventory","company_name=$cbo_company_name and variable_list=18 and item_category_id = 1");

	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		//---------------Check Duplicate product in Same return number ------------------------//
		$duplicate = is_duplicate_field("b.id","inv_issue_master a, inv_transaction b","a.id=b.mst_id and a.issue_number=$txt_return_id and b.prod_id=$txt_prod_id and b.transaction_type=3");
		if($duplicate==1)
		{
			echo "20**Duplicate Product is Not Allow in Same Return Number.";
			disconnect($con);
			die;
		}
		//------------------------------Check Brand END---------------------------------------//

		if(str_replace("'","",$txt_return_no)!="")
		{
			$new_return_number[0] = str_replace("'","",$txt_return_no);
			$id=str_replace("'","",$txt_return_id);
			//yarn master table UPDATE here START----------------------//
			$field_array_mst="entry_form*company_id*supplier_id*issue_date*received_id*received_mrr_no*pi_id*remarks*updated_by*update_date";
			$data_array_mst="381*".$cbo_company_name."*".$cbo_return_to."*".$txt_return_date."*".$txt_received_id."*".$txt_mrr_no."*'".$pi_id."'*".$txt_remarks."*'".$user_id."'*'".$pc_date_time."'";
			//yarn master table UPDATE here END---------------------------------------//
		}
		else
		{
			//yarn master table entry here START---------------------------------------//
			$id = return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master", $con);

			if($db_type==0) $year_cond="YEAR(insert_date)";
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later

			$new_return_number = explode("*", return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master",$con,1,$cbo_company_name,'SYRR',381,date("Y",time()),1 ));

			$field_array_mst="id, issue_number_prefix, issue_number_prefix_num, issue_number, entry_form, item_category, company_id, supplier_id, issue_date, received_id, pi_id, received_mrr_no, remarks, inserted_by, insert_date";
			$data_array_mst="(".$id.",'".$new_return_number[1]."','".$new_return_number[2]."','".$new_return_number[0]."',381,1,".$cbo_company_name.",".$cbo_return_to.",".$txt_return_date.",".$txt_received_id.",'".$pi_id."',".$txt_mrr_no.",".$txt_remarks.",'".$user_id."','".$pc_date_time."')";
			//yarn master table entry here END---------------------------------------//
		}


		/******** original product id check start ********/
		$origin_prod_id=return_field_value("origin_prod_id","inv_transaction","prod_id=$txt_prod_id and status_active=1 and mst_id=$txt_received_id and transaction_type in(1,4,5) and item_category=1","origin_prod_id");
		/******** original product id check end ********/

		$txt_issue_qnty = str_replace("'","",$txt_return_qnty);
		$txt_rate = str_replace("'","",$txt_rate);
		$issue_stock_value = str_replace("'","",$txt_return_value);

		//adjust product master table START-------------------------------------//
		$txt_return_qnty = str_replace("'","",$txt_return_qnty);
		$txt_return_value = str_replace("'","",$txt_return_value);
		$sql = sql_select("select product_name_details,avg_rate_per_unit,last_purchased_qnty,current_stock,stock_value,available_qnty,allocated_qnty from product_details_master where id=$txt_prod_id");
		$presentStock=$presentStockValue=$presentAvgRate=$allocated_qnty=$available_qnty=0;
		$product_name_details="";
		foreach($sql as $result)
		{
			$presentStock			= $result[csf("current_stock")];
			$presentStockValue		= $result[csf("stock_value")];
			$presentAvgRate			= $result[csf("avg_rate_per_unit")];
			$product_name_details 	= $result[csf("product_name_details")];
			$available_qnty			= $result[csf("available_qnty")];
			$allocated_qnty 		= $result[csf("allocated_qnty")];
		}

		/*$receive_info = sql_select("select receive_purpose,receive_basis,issue_id,entry_form,booking_id from inv_receive_master where id=$txt_received_id");
		$receive_purpose=$receive_info[0][csf("receive_purpose")];
		$receive_basis=$receive_info[0][csf("receive_basis")];
		$rec_entry_form=$receive_info[0][csf("entry_form")];
		$booking_id=$receive_info[0][csf("booking_id")];
		
		//$is_with_order = return_field_value("entry_form", "wo_yarn_dyeing_mst", " status_active=1 and id=$booking_id", "entry_form");

		if($variable_set_allocation == 1){

			if($receive_basis == 2 && $receive_purpose==2)
			{
				if($is_with_order==41 || $is_with_order==125 || $is_with_order==135)
				{
					if($txt_return_qnty > $allocated_qnty)
					{
						echo "20**Return quantity is not available.\nAvailable=".$allocated_qnty;
						die;
					}
				}else{
					if($txt_return_qnty > $available_qnty){
						echo "20**Return quantity is not available.\nAvailable=".$available_qnty;
						die;
					}
				}

			}else{
				if($txt_return_qnty > $available_qnty){
					echo "20**Return quantity is not available.\nAvailable=".$available_qnty;
					die;
				}
			}
		}else{
			if($txt_return_qnty > $available_qnty){
				echo "20**Return quantity is not available.\nAvailable=".$available_qnty;
				die;
			}
		}

		if($variable_set_allocation == 1){
			if($receive_basis == 2 && $receive_purpose==2)
			{
				if($is_with_order==41 || $is_with_order==125 || $is_with_order==135)
				{
					$allocated_qnty = $allocated_qnty-$txt_return_qnty;
					$available_qnty = $available_qnty;
				}else{
					$allocated_qnty = $allocated_qnty;
					$available_qnty = $available_qnty-$txt_return_qnty;
				}				
			}
			else
			{
				$allocated_qnty = $allocated_qnty;
				$available_qnty = $available_qnty-$txt_return_qnty;
			}
		}
		else
		{
			$allocated_qnty = $allocated_qnty;
			$available_qnty = $available_qnty-$txt_return_qnty;
		}*/
		
		$allocated_qnty = $allocated_qnty;
		$available_qnty = $available_qnty-$txt_return_qnty;
		
		$nowStock 		= $presentStock-$txt_return_qnty;
		$nowStockValue 	= $presentStockValue-$txt_return_value;
		$nowAvgRate		= number_format($nowStockValue/$nowStock,$dec_place[3],".","");

		$field_array_prod="last_issued_qnty*current_stock*stock_value*allocated_qnty*available_qnty*updated_by*update_date";
		$data_array_prod=$txt_return_qnty."*".$nowStock."*".$nowStockValue."*".$allocated_qnty."*".$available_qnty."*'".$user_id."'*'".$pc_date_time."'";

		
		//transaction table insert here START--------------------------------//
		$avg_rate_amount=str_replace("'","",$txt_issue_qnty)*$presentAvgRate;
		$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		$field_array_trans = "id, mst_id, entry_form, company_id, supplier_id, prod_id, origin_prod_id, item_category, transaction_type, transaction_date, no_of_bags, cone_per_bag, store_id,floor_id, room, rack, self, bin_box, order_rate, order_ile_cost, cons_uom, cons_quantity, cons_rate, cons_amount, inserted_by, insert_date, pi_wo_batch_no, rcv_rate, rcv_amount, buyer_id, job_no, style_ref_no";
		$data_array_trans = "(".$transactionID.",".$id.",381,".$cbo_company_name.",".$cbo_return_to.",".$txt_prod_id.",'".$origin_prod_id."',1,3,".$txt_return_date.",".$txt_no_of_bag.",".$txt_no_of_cone.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$order_rate.",".$order_ile_cost.",".$cbo_uom.",".$txt_issue_qnty.",'".$presentAvgRate."','".$avg_rate_amount."','".$user_id."','".$pc_date_time."','".$pi_id."',".$txt_rate.",".$issue_stock_value.",".$cbo_buyer_name.",".$txt_job_no.",".$txt_style_no.")";
		//transaction table insert here END ---------------------------------//

		//if LIFO/FIFO then START -----------------------------------------//
		$field_array = "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
		$update_array = "balance_qnty*balance_amount*updated_by*update_date";
		$cons_rate=0;
		$data_array="";
		$updateID_array=array();
		$update_data=array();
		$issueQnty = $txt_return_qnty;

		$isLIFOfifo=return_field_value("store_method","variable_settings_inventory","company_name=$cbo_company_name and variable_list=17");
		if($isLIFOfifo==2) $cond_lifofifo=" DESC"; else $cond_lifofifo=" ASC";
		$sql = sql_select("select id,cons_rate,balance_qnty,balance_amount from inv_transaction where prod_id=$txt_prod_id and mst_id=$txt_received_id and balance_qnty>0 and transaction_type in (1,4,5) and item_category=1 order by transaction_date $cond_lifofifo");
		foreach($sql as $result)
		{
			$issue_trans_id = $result[csf("id")]; // this row will be updated
			$balance_qnty = $result[csf("balance_qnty")];
			$balance_amount = $result[csf("balance_amount")];
			$cons_rate = $result[csf("cons_rate")];
			$issueQntyBalance = $balance_qnty-$issueQnty; // minus issue qnty
			$issueStockBalance = $balance_amount-($issueQnty*$cons_rate);
			if($issueQntyBalance>=0)
			{
				$amount = $issueQnty*$cons_rate;
				//for insert
				$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
				if($data_array!="") $data_array .= ",";
				$data_array .= "(".$mrrWiseIsID.",".$issue_trans_id.",".$transactionID.",381,".$txt_prod_id.",".$issueQnty.",".$cons_rate.",".$amount.",'".$user_id."','".$pc_date_time."')";
				//for update
				$updateID_array[]=$issue_trans_id;
				$update_data[$issue_trans_id]=explode("*",("".$issueQntyBalance."*".$issueStockBalance."*'".$user_id."'*'".$pc_date_time."'"));
				break;
			}
			else if($issueQntyBalance<0)
			{

				$issueQntyBalance  = $issueQnty-$balance_qnty;
				$issueQnty = $balance_qnty;
				$amount = $issueQnty*$cons_rate;

				//for insert
				$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
				if($data_array!="") $data_array .= ",";
				$data_array .= "(".$mrrWiseIsID.",".$issue_trans_id.",".$transactionID.",381,".$txt_prod_id.",".$issueQnty.",".$cons_rate.",".$amount.",'".$user_id."','".$pc_date_time."')";
				//for update
				$updateID_array[]=$issue_trans_id;
				$update_data[$issue_trans_id]=explode("*",("0*0*'".$user_id."'*'".$pc_date_time."'"));
				$issueQnty = $issueQntyBalance;
			}

		}
		//end foreach

		$mrrWiseIssueID=true; $upTrID=true;
		if(str_replace("'","",$txt_return_no)!="")
		{
			$rID=sql_update("inv_issue_master",$field_array_mst,$data_array_mst,"id",$id,1);
		}
		else
		{
			$rID=sql_insert("inv_issue_master",$field_array_mst,$data_array_mst,1);
		}

		$transID = sql_insert("inv_transaction",$field_array_trans,$data_array_trans,1);
		$prodUpdate = sql_update("product_details_master",$field_array_prod,$data_array_prod,"id",$txt_prod_id,1);

		//mrr wise issue data insert here----------------------------//
		if($data_array!="")
		{
			$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array,$data_array,1);
		}

		//echo "5**".bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array); oci_rollback($con); die;
		//transaction table stock update here------------------------//
		if(count($updateID_array)>0)
		{
			$upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array));
		}
 		//if LIFO/FIFO then END -----------------------------------------//

		//echo "20**".$rID." && ".$transID." && ".$prodUpdate." && ".$mrrWiseIssueID." && ".$upTrID;oci_rollback($con); die;
		if($db_type==0)
		{
			if( $rID && $transID && $prodUpdate && $mrrWiseIssueID && $upTrID)
			{
				mysql_query("COMMIT");
				echo "0**".$new_return_number[0]."**".$id;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$new_return_number[0]."**".$id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if( $rID && $transID && $prodUpdate && $mrrWiseIssueID && $upTrID)
			{
				//echo "10**".$rID." && ".$transID." && ".$prodUpdate." && ".$mrrWiseIssueID." && ".$upTrID;oci_rollback($con); die;
				oci_commit($con);
				echo "0**".$new_return_number[0]."**".$id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$new_return_number[0]."**".$id;
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;

	}
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		//****************************************** BEFORE ENTRY ADJUST START *****************************************//
		//product master table information
		//before stock update
		$sql = sql_select( "select a.id,a.avg_rate_per_unit,a.current_stock,a.stock_value, a.allocated_qnty, a.available_qnty, b.cons_quantity, b.cons_amount from product_details_master a, inv_transaction b where a.id=b.prod_id and b.id=$update_id and a.item_category_id=1 and b.item_category=1 and b.transaction_type=3" );
		$before_prod_id=$before_issue_qnty=$before_stock_qnty=$before_stock_value=$before_available_qnty=$before_allocated_qnty=0;
		foreach($sql as $result)
		{
			$before_prod_id 	= $result[csf("id")];
			$before_stock_qnty 	= $result[csf("current_stock")];
			$before_stock_value = $result[csf("stock_value")];
			$before_available_qnty	=$result[csf("available_qnty")];
			$before_allocated_qnty	=$result[csf("allocated_qnty")];
			//before quantity and stock value
			$before_issue_qnty	= $result[csf("cons_quantity")];
			$before_issue_value	= $result[csf("cons_amount")];
		}

		//current product ID
		$txt_prod_id = str_replace("'","",$txt_prod_id);
		$txt_return_qnty = str_replace("'","",$txt_return_qnty);
		$sql = sql_select("select avg_rate_per_unit,current_stock,stock_value,available_qnty,allocated_qnty from product_details_master where id=$txt_prod_id and item_category_id=1");
		$curr_avg_rate=$curr_stock_qnty=$curr_stock_value=0;
		foreach($sql as $result)
		{
			$curr_avg_rate 		= $result[csf("avg_rate_per_unit")];
			$curr_stock_qnty 	= $result[csf("current_stock")];

			$curr_stock_value 	= $result[csf("stock_value")];
			$available_qnty		= $result[csf("available_qnty")];
			$allocated_qnty 	= $result[csf("allocated_qnty")];
		}

		/*$receive_info = sql_select("select receive_purpose,receive_basis,issue_id,entry_form,booking_id from inv_receive_master where id=$txt_received_id");
		$receive_purpose=$receive_info[0][csf("receive_purpose")];
		$receive_basis=$receive_info[0][csf("receive_basis")];
		$rec_entry_form=$receive_info[0][csf("entry_form")];
		$booking_id=$receive_info[0][csf("booking_id")];

		$is_with_order = return_field_value("entry_form", "wo_yarn_dyeing_mst", " status_active=1 and id=$booking_id", "entry_form");

		if($variable_set_allocation == 1){
			if($receive_basis == 2 && $receive_purpose==2)
			{
				if($is_with_order==41 || $is_with_order==125 || $is_with_order==135)
				{
					if(($txt_return_qnty-$before_issue_qnty) > $allocated_qnty){
					echo "20**Return quantity is not available.\nAvailable=".$available_qnty;
					die;
					}
				}else{
					if(($txt_return_qnty-$before_issue_qnty) > $available_qnty){
						echo "20**Return quantity is not available.\nAvailable=".$available_qnty;
						die;
					}
				}
			}else{
				if(($txt_return_qnty-$before_issue_qnty) > $available_qnty){
					echo "20**Return quantity is not available.\nAvailable=".$available_qnty;
					die;
				}
			}
		}else{
			if(($txt_return_qnty-$before_issue_qnty) > $available_qnty){
				echo "20**Return quantity is not available.\nAvailable=".$available_qnty;
				die;
			}
		}*/
		//product master table data UPDATE START----------------------//
		$update_array_prod= "last_issued_qnty*current_stock*stock_value*allocated_qnty*available_qnty*updated_by*update_date";
		if($before_prod_id==$txt_prod_id)
		{
			$adj_stock_qnty = $curr_stock_qnty+$before_issue_qnty-$txt_return_qnty; // CurrentStock + Before Issue Qnty - Current Issue Qnty
			$adj_stock_val  = $curr_stock_value+$before_issue_value-($txt_return_qnty*$curr_avg_rate); // CurrentStockValue + Before Issue Value - Current Issue Value

			if($adj_stock_qnty==0)
			{
				$adj_avgrate = number_format($curr_avg_rate,$dec_place[3],'.','');
			}else
			{
				$adj_avgrate = number_format($adj_stock_val/$adj_stock_qnty,$dec_place[3],'.','');
			}

			if($adj_stock_qnty<0) //Aziz
			{
				echo "30**Stock cannot be less than zero.";
				disconnect($con);
				die;
			}
			
			/*if($variable_set_allocation == 1){
				if($receive_basis==2 && $receive_purpose==2)
				{
					if($is_with_order==41 || $is_with_order==125 || $is_with_order==135)
					{
						$adj_allocated_qnty=$allocated_qnty+$before_issue_qnty-$txt_return_qnty;
						$adj_beforeAvailableQnty=$available_qnty;
					}else {
						$adj_allocated_qnty=$allocated_qnty;
						$adj_beforeAvailableQnty=$available_qnty+$before_issue_qnty-$txt_return_qnty;
					}
				}
				else
				{
					$adj_allocated_qnty=$allocated_qnty;
					$adj_beforeAvailableQnty=$available_qnty+$before_issue_qnty-$txt_return_qnty;
				}
			}
			else
			{
				$adj_allocated_qnty=$allocated_qnty;
				$adj_beforeAvailableQnty=$available_qnty+$before_issue_qnty-$txt_return_qnty;
			}*/
			
			$adj_allocated_qnty=$allocated_qnty;
			$adj_beforeAvailableQnty=$available_qnty+$before_issue_qnty-$txt_return_qnty;

			$data_array_prod= $txt_return_qnty."*".$adj_stock_qnty."*".number_format($adj_stock_val,$dec_place[4],'.','')."*".$adj_allocated_qnty."*".$adj_beforeAvailableQnty."*'".$user_id."'*'".$pc_date_time."'";

			//now current stock
			$curr_avg_rate 		= $adj_avgrate;
			$curr_stock_qnty 	= $adj_stock_qnty;
			$curr_stock_value 	= $adj_stock_val;
		}
		else
		{
			/*if($variable_set_allocation == 1){
				if($receive_purpose==2)
				{
					if($is_with_order==41 || $is_with_order==125 || $is_with_order==135)
					{
						$adj_allocated_qnty=$before_allocated_qnty+$before_issue_qnty;
						$adj_beforeAvailableQnty=$before_available_qnty;

						$allocated_qnty=$allocated_qnty-$txt_return_qnty;
						$available_qnty = $available_qnty;
					}else {
						$adj_allocated_qnty=$before_allocated_qnty;
						$adj_beforeAvailableQnty=$before_available_qnty+$before_issue_qnty;

						$allocated_qnty=$allocated_qnty;
						$available_qnty = $available_qnty-$txt_return_qnty;
					}
				}
				else
				{
					$adj_allocated_qnty=$before_allocated_qnty;
					$adj_beforeAvailableQnty=$before_available_qnty+$before_issue_qnty;

					$allocated_qnty=$allocated_qnty;
					$available_qnty = $available_qnty-$txt_return_qnty;
				}
			}
			else
			{
				$adj_allocated_qnty=$before_allocated_qnty;
				$adj_beforeAvailableQnty=$before_available_qnty+$before_issue_qnty;

				$allocated_qnty=$allocated_qnty;
				$available_qnty = $available_qnty-$txt_return_qnty;
			}*/
			
			$adj_allocated_qnty=$before_allocated_qnty;
			$adj_beforeAvailableQnty=$before_available_qnty+$before_issue_qnty;

			$allocated_qnty=$allocated_qnty;
			$available_qnty = $available_qnty-$txt_return_qnty;
			
			$updateIdprod_array = $update_dataProd = array();
			//before product adjust
			$adj_before_stock_qnty 	= $before_stock_qnty+$before_issue_qnty; // CurrentStock + Before Issue Qnty
			$adj_before_stock_val  	= $before_stock_value+$before_issue_value; // CurrentStockValue + Before Issue Value
			$adj_before_avgrate		= number_format($adj_before_stock_val/$adj_before_stock_qnty,$dec_place[3],'.','');
			 if($adj_before_stock_qnty<0) //Aziz
			 {
			 	echo "30**Stock cannot be less than zero.";
			 	disconnect($con);
			 	die;
			 }
			 $updateIdprod_array[]=$before_prod_id;
			 $update_dataProd[$before_prod_id]=explode("*",("".$txt_return_qnty."*".$adj_before_stock_qnty."*".number_format($adj_before_stock_val,$dec_place[4],'.','')."*".$adj_allocated_qnty."*".$adj_beforeAvailableQnty."*'".$user_id."'*'".$pc_date_time."'"));

			//current product adjust
			$adj_curr_stock_qnty = 	$curr_stock_qnty-$txt_return_qnty; // CurrentStock + Before Issue Qnty
			$adj_curr_stock_val  = 	$curr_stock_value-($txt_return_qnty*$curr_avg_rate); // CurrentStockValue + Before Issue Value
			$adj_curr_avgrate	 =	number_format($adj_curr_stock_val/$adj_curr_stock_qnty,$dec_place[3],'.','');

			$updateIdprod_array[]=$txt_prod_id;
			$update_dataProd[$txt_prod_id]=explode("*",("".$txt_return_qnty."*".$adj_curr_stock_qnty."*".number_format($adj_curr_stock_val,$dec_place[4],'.','')."*".$allocated_qnty."*".$available_qnty."*'".$user_id."'*'".$pc_date_time."'"));

			//now current stock
			$curr_avg_rate 		= $adj_curr_avgrate;
			$curr_stock_qnty 	= $adj_curr_stock_qnty;
			$curr_stock_value 	= $adj_curr_stock_val;
		}
  		//------------------ product_details_master END--------------//
		//weighted and average rate END here-------------------------//
		$trans_data_array=array();
 		//transaction table START--------------------------//
		$update_array = "balance_qnty*balance_amount*updated_by*update_date";
		$sql = sql_select("select a.id,a.balance_qnty,a.balance_amount,b.issue_qnty,b.rate,b.amount from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id=$update_id and b.entry_form=381 and a.item_category=1");
		$updateID_array = array();
		$update_data = array();
		foreach($sql as $result)
		{
			$adjBalance = $result[csf("balance_qnty")]+$result[csf("issue_qnty")];
			$adjAmount = $result[csf("balance_amount")]+$result[csf("amount")];
			$updateID_array[]=$result[csf("id")];
			$update_data[$result[csf("id")]]=explode("*",("".$adjBalance."*".$adjAmount."*'".$user_id."'*'".$pc_date_time."'"));
			$trans_data_array[$result[csf("id")]]['qnty']=$adjBalance;
			$trans_data_array[$result[csf("id")]]['amnt']=$adjAmount;
		}

		$query2=true; $query3=true;
		//############## SAVE POINT START  ###################
		if($db_type==0)
		{
			$savepoint="updatesql";
			mysql_query("SAVEPOINT $savepoint");
		}
		//############## SAVE POINT END    ###################

		$id=str_replace("'","",$txt_return_id);
		//yarn master table UPDATE here START----------------------//
		$field_array_mst="entry_form*issue_date*received_id*remarks*updated_by*update_date";
		$data_array_mst="381*".$txt_return_date."*".$txt_received_id."*".$txt_remarks."*'".$user_id."'*'".$pc_date_time."'";
		//yarn master table UPDATE here END---------------------------------------//

		/******** original product id check start ********/
		$origin_prod_id=return_field_value("origin_prod_id","inv_transaction","prod_id=$txt_prod_id and status_active=1 and mst_id=$txt_received_id and transaction_type in(1,4,5) and item_category=1","origin_prod_id");
		/******** original product id check end ********/

		// if receive return from issue return order rate is calculated (Issue Return -> Issue -> Receive) in receive currency
		/*if($rec_entry_form == 9){
			$issue_ids=$receive_info[0][csf("issue_id")];			
			$issue_receive_ids=sql_select("select d.order_rate 
			from inv_transaction b, inv_mrr_wise_issue_details a left join inv_transaction d on a.recv_trans_id=d.id and d.status_active=1 and d.transaction_type=1 
			where b.id=a.issue_trans_id and b.mst_id in($issue_ids) and a.status_active=1 and b.status_active=1 and b.transaction_type=2");
			$order_rate =0;
			foreach ($issue_receive_ids as $issue_receive_row) {
				if($issue_receive_row[csf("order_rate")]!="" || $issue_receive_row[csf("order_rate")]>0)
				{
					$order_rate = $issue_receive_row[csf("order_rate")];
				}
			}
		}*/
		//transaction table insert here START--------------------------------//
		$avg_rate_amount=str_replace("'","",$txt_return_qnty)*$curr_avg_rate;
		$txt_rate = str_replace("'","",$txt_rate);
		$issue_stock_value = str_replace("'","",$txt_return_value);
		$field_array_trans= "company_id*supplier_id*prod_id*origin_prod_id*item_category*transaction_type*transaction_date*no_of_bags*cone_per_bag*store_id*floor_id*room*rack*self*bin_box*order_rate*order_ile_cost*cons_uom*cons_quantity*cons_rate*cons_amount*updated_by*update_date*pi_wo_batch_no*rcv_rate*rcv_amount*buyer_id*job_no*style_ref_no";
		$data_array_trans= "".$cbo_company_name."*".$cbo_return_to."*".$txt_prod_id."*'".$origin_prod_id."'*1*3*".$txt_return_date."*".$txt_no_of_bag."*".$txt_no_of_cone."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$order_rate."*".$order_ile_cost."*".$cbo_uom."*".$txt_return_qnty."*'".$curr_avg_rate."'*'".$avg_rate_amount."'*'".$user_id."'*'".$pc_date_time."'*'".$pi_id."'*".$txt_rate."*".$txt_return_value."*".$cbo_buyer_name."*".$txt_job_no."*".$txt_style_no."";
		//transaction table insert here END ---------------------------------//

		//if LIFO/FIFO then START -----------------------------------------//
		$field_array = "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
		$updateTrans_array = "balance_qnty*balance_amount*updated_by*update_date";
		$cons_rate=0;
		$data_array="";
		$updateIDtrans_array=array();
		$update_dataTrans=array();
		$issueQnty = $txt_return_qnty;

		$isLIFOfifo=return_field_value("store_method","variable_settings_inventory","company_name=$cbo_company_name and variable_list=17");
		if($isLIFOfifo==2) $cond_lifofifo=" DESC"; else $cond_lifofifo=" ASC";
		if($before_prod_id==$txt_prod_id) $balance_cond=" and( balance_qnty>0 or id=$update_id)";
		else $balance_cond=" and balance_qnty>0";
		$sql = sql_select("select id,cons_rate,balance_qnty,balance_amount from inv_transaction where prod_id=$txt_prod_id and mst_id=$txt_received_id $balance_cond and transaction_type in (1,4,5) and item_category=1 order by transaction_date $cond_lifofifo");
		foreach($sql as $result)
		{
			$issue_trans_id = $result[csf("id")]; // this row will be updated
			if($trans_data_array[$issue_trans_id]['qnty']=="")
			{
				$balance_qnty = $result[csf("balance_qnty")];
				$balance_amount = $result[csf("balance_amount")];
			}
			else
			{
				$balance_qnty = $trans_data_array[$issue_trans_id]['qnty'];
				$balance_amount = $trans_data_array[$issue_trans_id]['amnt'];
			}

			$cons_rate = $result[csf("cons_rate")];
			$issueQntyBalance = $balance_qnty-$issueQnty; // minus issue qnty
			$issueStockBalance = $balance_amount-($issueQnty*$cons_rate);
			if($issueQntyBalance>=0)
			{
				$amount = $issueQnty*$cons_rate;
				//for insert
				$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
				if($data_array!="") $data_array .= ",";
				$data_array .= "(".$mrrWiseIsID.",".$issue_trans_id.",".$update_id.",381,".$txt_prod_id.",".$issueQnty.",".$cons_rate.",".$amount.",'".$user_id."','".$pc_date_time."')";
				//for update
				$updateIDtrans_array[]=$issue_trans_id;
				$update_dataTrans[$issue_trans_id]=explode("*",("".$issueQntyBalance."*".$issueStockBalance."*'".$user_id."'*'".$pc_date_time."'"));
				break;
			}
			else if($issueQntyBalance<0)
			{

				$issueQntyBalance  = $issueQnty-$balance_qnty;
				$issueQnty = $balance_qnty;
				$amount = $issueQnty*$cons_rate;

				//for insert
				$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
				if($data_array!="") $data_array .= ",";
				$data_array .= "(".$mrrWiseIsID.",".$issue_trans_id.",".$update_id.",381,".$txt_prod_id.",".$issueQnty.",".$cons_rate.",".$amount.",'".$user_id."','".$pc_date_time."')";
				//echo "20**".$data_array;die;
				//for update
				$updateIDtrans_array[]=$issue_trans_id;
				$update_dataTrans[$issue_trans_id]=explode("*",("0*0*'".$user_id."'*'".$pc_date_time."'"));
				$issueQnty = $issueQntyBalance;
			}

		}//end foreach

		$mrrWiseIssueID=true; $upTrID=true;

		if($before_prod_id==$txt_prod_id)
		{
			$query1= sql_update("product_details_master",$update_array_prod,$data_array_prod,"id",$before_prod_id,1);
		}
		else
		{
			$query1=execute_query(bulk_update_sql_statement("product_details_master","id",$update_array_prod,$update_dataProd,$updateIdprod_array));
		}

		if(count($updateID_array)>0)
		{
			$query2=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array));
			$updateIDArray = implode(",",$updateID_array);
			$query3 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id=$update_id and entry_form=381");
		}
		$rID=sql_update("inv_issue_master",$field_array_mst,$data_array_mst,"id",$id,1);
		$transID = sql_update("inv_transaction",$field_array_trans,$data_array_trans,"id",$update_id,1);
		//mrr wise issue data insert here----------------------------//
		if($data_array!="")
		{
			$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array,$data_array,1);
		}

		//transaction table stock update here------------------------//
		if(count($updateIDtrans_array)>0)
		{
			//echo "10**".bulk_update_sql_statement("inv_transaction","id",$updateTrans_array,$update_dataTrans,$updateIDtrans_array);die;
			$upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$updateTrans_array,$update_dataTrans,$updateIDtrans_array));
		}
 		//if LIFO/FIFO then END -----------------------------------------//

 		//echo "20**".$field_array_trans."**".$data_array_trans;die;
		//echo "20**".$query1." && ".$query2." && ".$query3." && ".$upTrID." && ".$rID." && ".$transID." && ".$data_array." && ".$upTrID;mysql_query("ROLLBACK");mysql_query("ROLLBACK TO $savepoint"); die;

		if($db_type==0)
		{
			if($query1 && $query2 && $query3 && $rID && $transID && $mrrWiseIssueID && $upTrID)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$txt_return_no)."**".$id;
			}
			else
			{
				mysql_query("ROLLBACK");
				mysql_query("ROLLBACK TO $savepoint");
				echo "10**".str_replace("'","",$txt_return_no)."**".$id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($query1 && $query2 && $query3 && $rID && $transID && $mrrWiseIssueID && $upTrID)
			{
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_return_no)."**".$id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_return_no)."**".$id;
			}
		}

		disconnect($con);
		die;
	}
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		$mrr_data=sql_select("select a.id, a.is_posted_account, b.cons_quantity, b.cons_rate, b.cons_amount, c.id as prod_id, c.current_stock, c.stock_value, c.allocated_qnty, c.available_qnty from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and b.item_category=1 and b.transaction_type=3 and a.status_active=1 and b.status_active=1 and b.id=$update_id");
		$master_id=$mrr_data[0][csf("id")];
		$is_posted_account=$mrr_data[0][csf("is_posted_account")]*1;
		$cons_quantity=$mrr_data[0][csf("cons_quantity")];
		$cons_rate=$mrr_data[0][csf("cons_rate")];
		$cons_amount=$mrr_data[0][csf("cons_amount")];
		$prod_id=$mrr_data[0][csf("prod_id")];
		$current_stock=$mrr_data[0][csf("current_stock")];
		$stock_value=$mrr_data[0][csf("stock_value")];
		$allocated_qnty=$mrr_data[0][csf("allocated_qnty")];
		$available_qnty=$mrr_data[0][csf("available_qnty")];

		$cu_current_stock=$current_stock+$cons_quantity;
		$cu_stock_value=$stock_value+$cons_amount;
		if($cu_stock_value>0 && $cu_current_stock>0) $cu_avg_rate=$cu_stock_value/$cu_current_stock; else $cu_avg_rate=0;

		$receive_info = sql_select("select receive_purpose,receive_basis,issue_id,entry_form,booking_id from inv_receive_master where id=$txt_received_id");
		$receive_purpose=$receive_info[0][csf("receive_purpose")];
		$receive_basis=$receive_info[0][csf("receive_basis")];
		$rec_entry_form=$receive_info[0][csf("entry_form")];
		$booking_id=$receive_info[0][csf("booking_id")];

		$is_with_order = return_field_value("entry_form", "wo_yarn_dyeing_mst", " status_active=1 and id=$booking_id", "entry_form");
		
		if ($variable_set_allocation == 1)
		{
			//$cu_allocated_qnty=$allocated_qnty+$cons_quantity;
			$cu_available_qnty=$available_qnty;
			if($receive_basis == 2 && $receive_purpose==2)
			{
				if($is_with_order==41 || $is_with_order==125 || $is_with_order==135)
				{
					$cu_allocated_qnty = $allocated_qnty+$cons_quantity;
					$cu_available_qnty = $available_qnty;
				}else{
					$cu_allocated_qnty = $allocated_qnty;
					$cu_available_qnty = $available_qnty+$cons_quantity;
				}
			}
			else
			{
				$cu_allocated_qnty = $allocated_qnty;
				$cu_available_qnty = $available_qnty+$cons_quantity;
			}
		}
		else
		{
			$cu_allocated_qnty=$allocated_qnty;
			$cu_available_qnty=$available_qnty+$cons_quantity;
		}

		if($is_posted_account>0)
		{
			echo "13**Delete restricted, This Information is used in another Table."; disconnect($con); oci_rollback($con); die;
		}

		$next_operation=return_field_value("max(id) as max_trans_id", "inv_transaction", "status_active=1 and item_category=1 and transaction_type<>3 and prod_id=$prod_id", "max_trans_id");
		if($next_operation)
		{
			if($next_operation>str_replace("'","",$update_id))
			{
				echo "13**Delete restricted, This Information is used in another Table."; disconnect($con); oci_rollback($con); die;
			}
		}

		$field_array = "updated_by*update_date*status_active*is_deleted";
		$data_array = "'" . $user_id . "'*'" . $pc_date_time . "'*0*1";

		$field_array_prod = "current_stock*avg_rate_per_unit*stock_value*allocated_qnty*available_qnty*updated_by*update_date";
		$data_array_prod = "".$cu_current_stock."*".$cu_avg_rate."*".$cu_stock_value."*'".$cu_allocated_qnty."'*'".$cu_available_qnty."'*'".$user_id."'*'".$pc_date_time."'";

		$mrrsql= sql_select("select id, recv_trans_id, issue_trans_id, entry_form, prod_id, issue_qnty, rate, amount from inv_mrr_wise_issue_details where status_active=1 and entry_form=381 and issue_trans_id=$update_id order by recv_trans_id");

		$mrr_data=array();
		foreach($mrrsql as $row){

			$all_recv_trans_id.=$row[csf('recv_trans_id')].",";
			$all_issue_trans_id.=$row[csf('issue_trans_id')].",";
			$mrr_data[$row[csf('recv_trans_id')]]['issue_qnty']=$row[csf('issue_qnty')];
			$mrr_data[$row[csf('recv_trans_id')]]['amount']=$row[csf('amount')];
			$mrr_wise_issue_details_id[] = $row[csf('id')];
			$data_mrr_wise_issue_details[$row[csf('id')]] = explode("*",($user_id."*'".$pc_date_time."'*0*1"));
		}
		$all_recv_trans_id=chop($all_recv_trans_id,",");

		$rcv_sql = sql_select("select id, balance_qnty, balance_amount from inv_transaction where id in($all_recv_trans_id) order by id");
		$update_trans_field="balance_qnty*balance_amount*updated_by*update_date";
		foreach($rcv_sql as $row)
		{
			$current_bal_qnty=$row[csf("balance_qnty")]+$mrr_data[$row[csf("id")]]['issue_qnty'];
			$current_bal_amt=$row[csf("balance_amount")]+$mrr_data[$row[csf("id")]]['amount'];
			$updateID_trans_array[]=$row[csf("id")];
			$update_trans_data[$row[csf("id")]]=explode("*",("'".$current_bal_qnty."'*'".$current_bal_amt."'*'".$user_id."'*'".$pc_date_time."'"));
		}

		//$rID = sql_update("inv_issue_master",$field_array,$data_array,"issue_number","$txt_return_no",1);
		$rIDTr = sql_update("inv_transaction", $field_array, $data_array, "id", "$update_id", 1);
		$rIDprodID = sql_update("product_details_master", $field_array_prod, $data_array_prod, "id", "$prod_id", 1);

		$upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_trans_field,$update_trans_data,$updateID_trans_array));

		$upMrrTrID=execute_query(bulk_update_sql_statement("inv_mrr_wise_issue_details","id",$field_array,$data_mrr_wise_issue_details,$mrr_wise_issue_details_id));

		//echo "10**".$rIDTr."**".$rIDprodID."**".$upTrID."**".$upMrrTrID;oci_rollback($con);disconnect($con); die;
		if ($db_type == 0) {
			if ($rIDTr && $rIDprodID && $upTrID && $upMrrTrID) {
				mysql_query("COMMIT");
				echo "2**" . str_replace("'", "", $txt_mrr_no)."**".str_replace("'", "", $txt_return_id);
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . str_replace("'", "", $txt_mrr_no)."**".str_replace("'", "", $txt_return_id);
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rIDTr && $rIDprodID && $upTrID && $upMrrTrID) {
				oci_commit($con);
				echo "2**" . str_replace("'", "", $txt_mrr_no)."**".str_replace("'", "", $txt_return_id);
			} else {
				oci_rollback($con);
				echo "10**" . str_replace("'", "", $txt_mrr_no)."**".str_replace("'", "", $txt_return_id);
			}
		}
		disconnect($con);
		die;
	}
}



if($action=="return_number_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>

	<script>
		function js_set_value(mrr)
		{
			var splitArr = mrr.split("_");
 		$("#hidden_return_number").val(splitArr[0]); // mrr number
		$("#hidden_posted_in_account").val(splitArr[1]); // is posted account
		$("#hidden_return_id").val(splitArr[2]);
		parent.emailwindow.hide();
	}
</script>

</head>

<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<table width="850" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
				<thead>
					<tr>
						<th width="180">Search By</th>
						<th width="250" align="center" id="search_by_td_up">Enter Return Number</th>
						<th width="220">Date Range</th>
						<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td align="center">
							<?
							$search_by = array(1=>'Return Number');
							//$dd="change_search_event(this.value, '0*0', '0*0', '../../../') ";
							echo create_drop_down( "cbo_search_by", 140, $search_by,"",0, "--Select--", "",1,0 );
							?>
						</td>
						<td width="" align="center" id="search_by_td">
							<input type="text" style="width:230px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td align="center">
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" />&nbsp;&nbsp;&nbsp;
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" />
						</td>
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('cbo_year_selection').value, 'create_return_search_list_view', 'search_div', 'yarn_receive_return_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td align="center" height="40" valign="middle" colspan="5">
							<? echo load_month_buttons(1);  ?>
							<!-- Hidden field here -->
							<input type="hidden" id="hidden_return_number" value="" />
							<input type="hidden" id="hidden_return_id" value="" />
							<input type="hidden" id="hidden_posted_in_account" value="" />
							<!--END -->
						</td>
					</tr>
				</tbody>
			</tr>
		</table>
		<div align="center" style="margin-top:10px" valign="top" id="search_div"> </div>
	</form>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}


if($action=="create_return_search_list_view")
{
	$ex_data = explode("_",$data);
	$search_by = $ex_data[0];
	$search_common = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
	$year_selection = $ex_data[5];

	$sql_cond="";
	if($search_by==1)
	{
		if($search_common!="") $sql_cond .= " and a.issue_number like '%$search_common'";
	}

	if( $txt_date_from!="" && $txt_date_to!="" )
	{
		if($db_type==0)
		{
			$sql_cond .= " and a.issue_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		}
		else
		{
			$sql_cond .= " and a.issue_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
		}
	}

	if($db_type==0)
	{
		$year_cond=" and YEAR(a.issue_date)=$year_selection";
	}
	else if($db_type==2)
	{
		$year_cond=" and to_char(a.issue_date,'YYYY')=$year_selection";
	}
	else
	{
		$year_cond=""; $year_field="";
	}

	if(trim($company)!="") $sql_cond .= " and a.company_id='$company'";

	if($db_type==0) $year_field="YEAR(a.insert_date) as year,";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year,";
	else $year_field="";//defined Later

	$sql = "select a.id, $year_field a.issue_number_prefix_num, a.issue_number, a.company_id, a.supplier_id,a.issue_date, a.item_category, a.received_id,a.received_mrr_no, sum(b.cons_quantity)as cons_quantity,a.is_posted_account
	from inv_issue_master a, inv_transaction b
	where a.id=b.mst_id and b.transaction_type=3 and a.status_active=1 and a.item_category=1 and b.item_category=1 and a.entry_form=381 and b.entry_form=381 $sql_cond $year_cond group by a.id, a.issue_number_prefix_num, a.issue_number, a.company_id, a.supplier_id, a.issue_date, a.item_category, a.received_id, a.received_mrr_no, a.insert_date,a.is_posted_account order by a.id";

	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$arr=array(2=>$company_arr,3=>$supplier_arr);
	echo create_list_view("list_view", "Return No, Year, Company Name, Returned To, Return Date,Return Qty,Receive MRR","70,60,150,170,80,100,150","850","230",0, $sql , "js_set_value", "issue_number,is_posted_account,id", "", 1, "0,0,company_id,supplier_id,0,0,0", $arr, "issue_number_prefix_num,year,company_id,supplier_id,issue_date,cons_quantity,received_mrr_no","","",'0,0,0,0,3,1,0') ;
	exit();
}


if($action=="populate_master_from_data")
{
	$sql = "select id,issue_number,company_id,supplier_id,issue_date,item_category,received_id,received_mrr_no,pi_id,remarks
	from inv_issue_master
	where id='$data' and item_category=1 and entry_form=381";
	//echo $sql;
	$res = sql_select($sql);
	foreach($res as $row)
	{
		echo "$('#txt_return_id').val('".$row[csf("id")]."');\n";
		echo "$('#txt_return_no').val('".$row[csf("issue_number")]."');\n";
		echo "$('#cbo_company_name').val(".$row[csf("company_id")].");\n";
		echo "$('#cbo_return_to').val('".$row[csf("supplier_id")]."');\n";
		echo "$('#txt_return_date').val('".change_date_format($row[csf("issue_date")])."');\n";
		//$('#txt_return_date').attr('disabled','disabled');
		echo "$('#txt_mrr_no').val('".$row[csf("received_mrr_no")]."');\n";
		echo "$('#txt_received_id').val('".$row[csf("received_id")]."');\n";
		echo "$('#txt_remarks').val('".$row[csf("remarks")]."');\n";
		$pi_no=return_field_value("pi_number","com_pi_master_details","id='".$row[csf("pi_id")]."'");
		$ref_closing_status=return_field_value("ref_closing_status","com_pi_master_details","id='".$row[csf("pi_id")]."'");
		echo "$('#txt_pi_no').val('".$pi_no."');\n";
		echo "$('#hidden_ref_closing_status').val('".$ref_closing_status."');\n";
		echo "$('#pi_id').val('".$row[csf("pi_id")]."');\n";
		$issue_id=return_field_value("issue_id","inv_receive_master","id='".$row[csf("received_id")]."'");
		echo "$('#hdn_issue_id').val('".$issue_id."');\n";
		echo "$('#cbo_company_name').attr('disabled','disabled');\n";
		echo "$('#txt_mrr_no').attr('disabled','disabled');\n";

		$entry_form=return_field_value("entry_form","inv_receive_master","id=".$row[csf("received_id")]);
		if($entry_form==9)
		{
			//$pi_no=return_field_value("pi_number","com_pi_master_details","id='".$row[csf("pi_id")]."'");
			echo "$('#txt_pi_no').removeAttr('disabled','disabled');\n";
			//echo "$('#txt_pi_no').val('".$pi_no."');\n";
			//echo "$('#pi_id').val('".$row[csf("pi_id")]."');\n";
		}
		else
		{
			echo "$('#txt_pi_no').attr('disabled','disabled');\n";
			//echo "$('#txt_pi_no').val('');\n";
			//echo "$('#pi_id').val('');\n";
		}
		//right side list view
		$trans_type_sql=sql_select("select distinct b.transaction_type from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.transaction_type in(1,4) and a.recv_number='".$row[csf("received_mrr_no")]."'
			union all
			select distinct b.transaction_type from inv_item_transfer_mst a, inv_transaction b where a.id=b.mst_id and b.transaction_type in(5) and a.transfer_system_id='".$row[csf("received_mrr_no")]."'");
		if($trans_type_sql[0][csf("transaction_type")]==1 ||$trans_type_sql[0][csf("transaction_type")]==4) $mrr_type=1; else $mrr_type=2;
		echo "show_list_view('".$row[csf("received_id")]."**".$mrr_type."' ,'show_product_listview' ,'list_product_container' ,'requires/yarn_receive_return_controller' , '');\n";
	}
	exit();
}



if($action=="show_dtls_list_view")
{
	/*$ex_data = explode("**",$data);
	$return_number = $ex_data[0];
	$ret_mst_id = $ex_data[1];

	$cond="";
	if($return_number!="") $cond .= " and a.issue_number='$return_number'";
	if($ret_mst_id!="") $cond .= " and a.id='$ret_mst_id'";
	*/
	$sql = "select a.issue_number, a.company_id, a.supplier_id, a.issue_date, a.item_category, a.received_id, a.received_mrr_no, b.id, b.no_of_bags, b.cone_per_bag, b.cons_quantity, b.cons_uom, b.rcv_rate as cons_rate, b.rcv_amount as cons_amount, c.product_name_details, c.id as prod_id, c.lot
	from inv_issue_master a, inv_transaction b left join product_details_master c on b.prod_id=c.id
	where a.id=b.mst_id and b.item_category=1 and b.transaction_type=3 and a.id='$data' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	//echo $sql; //die();
	$result = sql_select($sql);
	$i=1;
	$rettotalQnty=0;
	$rcvtotalQnty=0;
	$totalAmount=0;
	?>
	
	<table class="rpt_table" border="1" cellpadding="2" cellspacing="0" style="width:1000px" rules="all" >
		<thead>
			<tr>
				<th>SL</th>
				<th>Item Description</th>
                <th>Lot</th>
				<th>No Of Bag</th>
				<th>No Of Cone</th>
				<th>Product ID</th>
				<th>Received No</th>
				<th>Return Qnty</th>
				<th>UOM</th>
				<th>Rate</th>
				<th>Return Value</th>
			</tr>
		</thead>
		<tbody>
			<?
			//$ref_closing_status = ("ref_closing_status","inv_receive_master","id=".$row[csf("received_id")]);
			foreach($result as $row){
				if($i%2==0)
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";

				/*echo "select b.balance_qnty from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.prod_id=".$row[csf("prod_id")]." and b.item_category=1 and b.transaction_type=1 and a.recv_number='".$row[csf("received_mrr_no")]."'";*/
				if($row[csf("prod_id")]!="")
				{
					//echo "select b.balance_qnty from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.prod_id=".$row[csf("prod_id")]." and b.item_category=1 and b.transaction_type in (1,4,5) and a.id='".$row[csf("received_id")]."'";
					$sqlTr = sql_select("select b.balance_qnty from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.prod_id=".$row[csf("prod_id")]." and b.item_category=1 and b.transaction_type in (1,4,5) and a.id='".$row[csf("received_id")]."'");
				}

				//$ref_closing_status = return_field_value("ref_closing_status","inv_receive_master","id=".$row[csf("received_id")]);
				
				$rcvQnty = $sqlTr[0][csf('balance_qnty')];

				$rettotalQnty +=$row[csf("cons_quantity")];
					//$rcvtotalQnty +=$rcvQnty;
				$totalAmount +=$row[csf("cons_amount")];

				$tot_no_of_bags +=$row[csf("no_of_bags")];
				$tot_cone_per_bag +=$row[csf("cone_per_bag")];

				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $row[csf("id")]."_";?>,<? echo $rcvQnty;?>","child_form_input_data","requires/yarn_receive_return_controller");hidden_ref_closing(<?php echo $row[csf("cons_quantity")]; ?>);' style="cursor:pointer" >
					<td width="30"><?php echo $i; ?></td>
					<td width="160"><p><?php echo $row[csf("product_name_details")]; ?></p></td>
					<td width="60" align="center"><p><?php echo $row[csf("lot")]; ?></p></td>
                    <td width="60" align="right"><p><?php echo $row[csf("no_of_bags")]; ?></p></td>
					<td width="60" align="right"><p><?php echo $row[csf("cone_per_bag")]; ?></p></td>
					<td width="70" align="center"><p><?php echo $row[csf("prod_id")]; ?></p></td>
					<td width="100"><p><?php echo $row[csf("received_mrr_no")]; ?></p></td>
					<td width="70" align="right"><p><?php echo $row[csf("cons_quantity")]; ?></p></td>
					<!--<td width="70" align="right"><p><!?php echo $rcvQnty; ?></p></td>-->
					<td width="70"><p><?php echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
					<td width="70" align="right"><p><?php echo $row[csf("cons_rate")]; ?></p></td>
					<td width="70" align="right"><p><?php echo $row[csf("cons_amount")]; ?></p></td>
				</tr>
				<? $i++; } ?>
				<tfoot>
					<th colspan="3">Total</th>
					<th><?php echo $tot_no_of_bags; ?></th>
					<th><?php echo $tot_cone_per_bag; ?></th>
					<th colspan="2"></th>
					<th><?php echo number_format($rettotalQnty,2,".",""); ?></th>
					<th colspan="2"></th>
					<th><?php echo number_format($totalAmount,2,".",""); ?></th>
				</tfoot>
			</tbody>
		</table>
		
		<?
		exit();
	}


if($action=="child_form_input_data")
{
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$ex_data = explode(",",$data);
	$data = explode("_",$ex_data[0]); 	// transaction id
	$rcvQnty = $ex_data[1];
	$ref_closing_status = $data[1];
	//echo $ref_closing_status;die;
	$sql = "select b.id as prod_id, b.product_name_details, a.id as tr_id,a.company_id, a.no_of_bags, a.cone_per_bag, a.store_id, a.floor_id, a.room, a.rack, a.self, a.bin_box, a.order_rate, a.order_ile_cost, a.cons_uom, a.rcv_rate as cons_rate, a.cons_quantity, a.rcv_amount as cons_amount, a.buyer_id, a.job_no, a.style_ref_no, b.lot, b.color
	from inv_transaction a, product_details_master b
	where a.id=$data[0] and a.status_active=1 and a.item_category=1 and transaction_type=3 and a.prod_id=b.id and b.status_active=1";
	// echo $sql;die;
	$result = sql_select($sql);
	$com=20;
	foreach($result as $row)
	{
		echo "$('#txt_item_description').val('".$row[csf("product_name_details")]."');\n";
		echo "$('#txt_item_description').attr( 'title', '".$color_arr[$row[csf("color")]]."');\n";
		echo "$('#txt_lot').val('".$row[csf("lot")]."');\n";
		echo "$('#txt_prod_id').val('".$row[csf("prod_id")]."');\n";
		echo "$('#cbo_buyer_name').val('".$row[csf("buyer_id")]."');\n";
		echo "$('#txt_job_no').val('".$row[csf("job_no")]."');\n";
		echo "$('#txt_style_no').val('".$row[csf("style_ref_no")]."');\n";
		echo "$('#txt_no_of_bag').val('".$row[csf("no_of_bags")]."');\n";
		echo "$('#txt_no_of_cone').val('".$row[csf("cone_per_bag")]."');\n";
		echo "$('#before_prod_id').val('".$row[csf("prod_id")]."');\n";
		echo "$('#cbo_store_name').val('".$row[csf("store_id")]."');\n";
		
		echo "load_drop_down('requires/yarn_receive_return_controller', ".$row[csf("store_id")]."+'_'+".$row[csf('company_id')].", 'load_drop_floor','floor_td');\n";
		echo "document.getElementById('cbo_floor').value 	= '".$row[csf("floor_id")]."';\n";

		echo "load_drop_down('requires/yarn_receive_return_controller', ".$row[csf("store_id")]."+'_'+".$row[csf('company_id')].", 'load_drop_room','room_td');\n";
		echo "document.getElementById('cbo_room').value 	= '".$row[csf("room")]."';\n";

		echo "load_drop_down('requires/yarn_receive_return_controller', ".$row[csf("store_id")]."+'_'+".$row[csf('company_id')].", 'load_drop_rack','rack_td');\n";
		echo "document.getElementById('txt_rack').value 	= '".$row[csf("rack")]."';\n";

		echo "load_drop_down('requires/yarn_receive_return_controller', ".$row[csf("store_id")]."+'_'+".$row[csf('company_id')].", 'load_drop_shelf','shelf_td');\n";
		echo "document.getElementById('txt_shelf').value 	= '".$row[csf("self")]."';\n";

		echo "load_drop_down('requires/yarn_receive_return_controller', ".$row[csf("store_id")]."+'_'+".$row[csf('company_id')].", 'load_drop_bin','bin_td');\n";
		echo "document.getElementById('cbo_bin').value 		= '".$row[csf("bin_box")]."';\n";
		echo "$('#cbo_store_name').attr('disabled','disabled');\n";
		echo "$('#cbo_floor').attr('disabled','disabled');\n";
		echo "$('#cbo_room').attr('disabled','disabled');\n";
		echo "$('#txt_rack').attr('disabled','disabled');\n";
		echo "$('#txt_shelf').attr('disabled','disabled');\n";
		echo "$('#cbo_bin').attr('disabled','disabled');\n";
		
		echo "$('#txt_return_qnty').val('".$row[csf("cons_quantity")]."');\n";
		$rcvQnty = $rcvQnty+$row[csf("cons_quantity")];
		echo "$('#txt_receive_qnty').val('".$rcvQnty."');\n";
		echo "$('#cbo_uom').val('".$row[csf("cons_uom")]."');\n";
		echo "$('#txt_rate').val('". number_format($row[csf("cons_rate")],4,".","")."');\n";
		echo "$('#txt_return_value').val(".$row[csf("cons_amount")].");\n";
		echo "$('#update_id').val(".$row[csf("tr_id")].");\n";

		echo "$('#order_rate').val('".$row[csf("order_rate")]."');\n";
		echo "$('#order_ile_cost').val('".$row[csf("order_ile_cost")]."');\n";
		if($ref_closing_status==1)
		{
			echo "$('#txt_return_qnty').attr('disabled',true);\n";
			echo "$('#txt_return_qnty').attr('readonly',true);\n";
		}
	}

	echo "set_button_status(1, permission, 'fnc_yarn_receive_return_entry',1,1);\n";
	//echo "$('#tbl_master').find('input,select').attr('disabled', false);\n";
	//echo "disable_enable_fields( 'cbo_company_name*txt_mrr_no', 1, '', '');\n";
	exit();
}

if($action=="upto_variable_settings")
{	
    $sql =  sql_select("select store_method from variable_settings_inventory where company_name = $data and item_category_id=1 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	$return_data="";
    if(count($sql)>0)
	{
		$return_data=$sql[0][csf('store_method')];
	}
	else
	{ 
		$return_data=0; 
	}
	
	echo $return_data;
	die;
}

// pi popup here----------------------//
if ($action=="pi_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>

	<script>
		function js_set_value(str)
		{
			var splitData = str.split("_");
		$("#hidden_tbl_id").val(splitData[0]); // pi id
		$("#hidden_pi_number").val(splitData[1]); // pi number
		parent.emailwindow.hide();
	}
</script>

</head>

<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<table width="800" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
				<thead>
					<tr>
						<th>Supplier</th>
						<th align="center" id="search_by_th_up">Enter PI Number</th>
						<th>Date Range</th>
						<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>
					</tr>
				</thead>
				<tbody>
					<tr class="general">
						<td>
							<?
							echo create_drop_down( "cbo_supplier", 170, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$company' and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
							?>
						</td>
						<td width="180" align="center" id="search_by_td">
							<input type="text" style="width:230px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td align="center">
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
						</td>
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>, 'create_wopi_search_list_view', 'search_div', 'yarn_receive_return_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td align="center" height="40" valign="middle" colspan="4">
							<? echo load_month_buttons(1);  ?>
							<!-- Hidden field here-->
							<input type="hidden" id="hidden_tbl_id" value="" />
							<input type="hidden" id="hidden_pi_number" value="hidden_pi_number" />
							<!-- -END-->
						</td>
					</tr>
				</tbody>
			</tr>
		</table>
		<div align="center" style="margin-top:5px" valign="top" id="search_div"> </div>
	</form>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_wopi_search_list_view")
{
	$ex_data = explode("_",$data);
	$supplier = $ex_data[0];
	$txt_search_common = trim($ex_data[1]);
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];

	$sql_cond="";
	$sql_cond .= " and a.pi_number LIKE '%$txt_search_common%'";
	if(trim($company)!=0) $sql_cond .= " and a.importer_id='$company'";
	if(trim($supplier)!=0) $sql_cond .= " and a.supplier_id='$supplier'";

	if($txt_date_from!="" && $txt_date_to!="" )
	{
		if($db_type==0)
		{
			$sql_cond .= " and a.pi_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		}
		else
		{
			$sql_cond .= " and a.pi_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
		}
	}

	$sql = "select a.id, a.pi_number, a.pi_date, a.supplier_id, a.currency_id, a.source, c.lc_number as lc_number
	from com_pi_master_details a
	left join com_btb_lc_pi b on a.id=b.pi_id
	left join com_btb_lc_master_details c on b.com_btb_lc_master_details_id=c.id
	where
	a.item_category_id = 1 and
	a.status_active=1 and a.is_deleted=0
	$sql_cond order by a.id";
	//echo $sql;
	$result = sql_select($sql);
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$arr=array(3=>$supplier_arr,4=>$currency,5=>$source);

	echo  create_list_view("list_view", "PI No, LC ,Date, Supplier, Currency, Source","120,130,100,200,100","830","230",0, $sql , "js_set_value", "id,pi_number", "", 1, "0,0,0,supplier_id,currency_id,source", $arr, "pi_number,lc_number,pi_date,supplier_id,currency_id,source", "",'','0,0,3,0,0,0') ;
	exit();
}


if ($action=="yarn_receive_return_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	if(isset($data[3]))
	{
		$path = $data[3];
	}else{
		$path = "";
	}
	$sql=" select id, issue_number, received_id, issue_date, supplier_id, pi_id, remarks, received_mrr_no from  inv_issue_master where issue_number='$data[1]' and entry_form=381 and item_category=1 and status_active=1 and is_deleted=0";
	$dataArray=sql_select($sql);

	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr = return_library_array("select id,country_name from lib_country","id","country_name");
	//$receive_arr = return_library_array("select id, recv_number from inv_receive_master","id","recv_number");
	if($db_type==0)
	{
		$select_prod=" group_concat(b.prod_id) as prod_id";
	}
	else
	{
		$select_prod=" listagg(cast(b.prod_id as varchar(4000)),',') within group(order by b.prod_id) as prod_id";
	}
	$sql_rcv=sql_select("select a.entry_form, a.issue_id, $select_prod from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.transaction_type in(1,4) and a.item_category=1 and a.id='".$dataArray[0][csf('received_id')]."'");
	if($sql_rcv[0][csf('entry_form')]==9)
	{
		$all_prod_id=$sql_rcv[0][csf('prod_id')];
		if($all_prod_id=="") $all_prod_id=0;
		//echo "select c.mst_id from inv_transaction a, inv_mrr_wise_issue_details b, inv_transaction c where a.id=b.issue_trans_id and b.recv_trans_id=c.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.transaction_type=2 and b.entry_form=3 and a.mst_id='".$sql_rcv[0][csf('issue_id')]."' and a.prod_id in($all_prod_id) and c.prod_id in($all_prod_id)";die;
		$rcv_mst_id=sql_select("select c.mst_id from inv_transaction a, inv_mrr_wise_issue_details b, inv_transaction c where a.id=b.issue_trans_id and b.recv_trans_id=c.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.transaction_type=2 and b.entry_form=3 and a.mst_id='".$sql_rcv[0][csf('issue_id')]."' and a.prod_id in($all_prod_id) and c.prod_id in($all_prod_id)");
		$org_rcv_mst_no=return_field_value("recv_number","inv_receive_master","id='".$rcv_mst_id[0][csf('mst_id')]."'","recv_number");
		//echo $org_rcv_mst_no;die;
	}

	?>
	<div style="width:930px;">
		<table width="910" cellspacing="0" align="right">
			<tr>
				<td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
			</tr>
			<tr class="form_caption">

				<?
				$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
				?>
				<td  align="left" width="50">
					<?
					foreach($data_array as $img_row)
					{
						?>
						<img src='../<? echo $path;?><? echo $img_row[csf('image_location')]; ?>' height='50' width='50' align="middle" />
						<?
					}
					?>
				</td>
				<td colspan="4" align="center" style="font-size:14px">
					<?

					echo show_company($data[0],'','');
					/*$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
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
					}*/
					?>
				</td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:x-large"><strong><u>Purchase Return/Delivery Challan</u></strong></td>
			</tr>
			<tr>
				<td width="120"><strong>Return Number:</strong></td>
				<td width="175"><? echo $dataArray[0][csf('issue_number')]; ?></td>
				<td width="110"><strong>Receive ID:</strong></td>
				<td width="175"><? echo $dataArray[0][csf('received_mrr_no')]; ?></td>
				<td width="100"><strong>Return To :</strong></td>
				<td><? echo $supplier_library[$dataArray[0][csf('supplier_id')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Return Date:</strong></td>
				<td><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
				<td><strong>PI No:</strong></td>
				<td>
					<?
					$pi_no=return_field_value("pi_number","com_pi_master_details","id=".$dataArray[0][csf('pi_id')],"pi_number");
					echo $pi_no;
					?>
				</td>
				<td><strong>Lc No:</strong></td>
				<td>
					<?
					$btb_lc_no=return_field_value("a.lc_number as lc_number"," com_btb_lc_master_details a, com_btb_lc_pi b","a.id=b.com_btb_lc_master_details_id and b.pi_id=".$dataArray[0][csf('pi_id')],"lc_number");
					echo $btb_lc_no;
					?>
				</td>
			</tr>
			<tr>
				<td><strong>Remarks:</strong></td>
				<td><? echo $dataArray[0][csf('remarks')]; ?></td>
				<td><strong>Origin Rcv ID:</strong></td>
				<td><? if($sql_rcv[0][csf('entry_form')]==9) echo $org_rcv_mst_no; else echo $dataArray[0][csf('received_mrr_no')]; ?></td>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="6">&nbsp;</td>
			</tr>
		</table>
		<br />
		<div style="width:100%;">
			<table align="right" cellspacing="0" width="910"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<th width="30">SL</th>
					<th width="250" align="center">Item Description</th>
					<th width="70" align="center">UOM</th>
					<th width="100" align="center">No Of Bag</th>
					<th width="100" align="center">No Of Cone</th>
					<th width="100" align="center">Return Qnty.</th>
					<th align="center">Store</th>
				</thead>
				<?
				$mrr_no =$dataArray[0][csf('issue_number')];;
	//$up_id =$data[1];
				$cond="";
				if($mrr_no!="") $cond .= " and c.issue_number='$mrr_no'";
	//if($up_id!="") $cond .= " and a.id='$up_id'";
				$i=1;
				$sql_dtls = "select b.id as prod_id, b.product_name_details, a.id as tr_id, a.no_of_bags, a.cone_per_bag, a.store_id, a.cons_uom, a.cons_quantity
				from inv_transaction a, product_details_master b, inv_issue_master c
				where c.id=a.mst_id and a.status_active=1 and a.company_id='$data[0]' and c.issue_number='$data[1]' and a.item_category=1 and transaction_type=3 and a.prod_id=b.id and b.status_active=1 ";
				$sql_result= sql_select($sql_dtls);

				foreach($sql_result as $row)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					$qnty+=$row[csf('cons_quantity')];
					$no_of_bags+=$row[csf('no_of_bags')];
					$cone_per_bag+=$row[csf('cone_per_bag')];
					?>

					<tr bgcolor="<? echo $bgcolor; ?>">
						<td><? echo $i; ?></td>
						<td><? echo $row[csf('product_name_details')]; ?></td>
						<td align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
						<td align="right"><? echo $row[csf('no_of_bags')]; ?></td>
						<td align="right"><? echo $row[csf('cone_per_bag')]; ?></td>
						<td align="right"><? echo $row[csf('cons_quantity')]; ?></td>
						<td><? echo $store_library[$row[csf('store_id')]]; ?></td>
					</tr>
					<?php
					$i++;
				}
				?>
				<tr>
					<td align="right" colspan="3" >Total</td>
					<td align="right"><? echo number_format($no_of_bags,0,'',','); ?></td>
					<td align="right"><? echo number_format($cone_per_bag,0,'',','); ?></td>
					<td align="right"><? echo number_format($qnty,0,'',','); ?></td>
					<td align="right">&nbsp;</td>
				</tr>
			</table>
			<br>
			<?
			echo signature_table(7, $data[0], "930px");
			?>
		</div>
	</div>
	<?
	exit();
}



?>

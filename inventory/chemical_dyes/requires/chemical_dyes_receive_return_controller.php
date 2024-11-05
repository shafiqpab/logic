<? 
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$user_id = $_SESSION['logic_erp']["user_id"];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if ($action=="load_room_rack_self_bin")
{
	load_room_rack_self_bin("requires/chemical_dyes_receive_return_controller",$data);
}

if ($action=="load_drop_down_supplier")
{
	echo create_drop_down( "cbo_return_to", 160, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_tag_company b, lib_supplier_party_type c where a.id=c.supplier_id and a.id=b.supplier_id $supplier_credential_cond and b.tag_company='$data' and c.party_type='3' and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",1 );
	exit();
}

if($action=="mrr_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST); 
	//echo "$company"; 
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
	<table width="820" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <tr>                	 
                    <th width="160">Supplier</th>
                    <th width="150">Item Category</th>
                    <th width="120">MRR No</th>
                    <th width="120">Lot No</th>
                    <th width="200">Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:80px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                <tr class="general">
                    <td>
                        <?  
 							echo create_drop_down( "cbo_supplier", 150, "select id,supplier_name from lib_supplier order by supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
                        ?>
                    </td>
                    <td><?  echo create_drop_down( "cbo_item_category", 130, $item_category,"", 1, "-- Select --", 0, "", 0,"5,6,7,23" );?></td>
                    <td><input type="text" id="txt_mrr_no" name="txt_mrr_no" style="width:100px" class="text_boxes" /></td>
                    <td><input type="text" id="txt_lot" name="txt_lot" style="width:100px" class="text_boxes" /></td>
                    <td>
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
                    </td> 
                    <td>
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier').value+'_'+document.getElementById('cbo_item_category').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('txt_lot').value+'_'+document.getElementById('txt_mrr_no').value, 'create_mrr_search_list_view', 'search_div', 'chemical_dyes_receive_return_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />				
                    </td>
            </tr>
        	<tr>                  
            	<td align="center" height="40" valign="middle" colspan="6">
					<? echo load_month_buttons(1);  ?>
                    <!-- Hidden field here-------->
                     <input type="hidden" id="hidden_recv_number" value="" />
                    <!-- ---------END------------->
                </td>
            </tr>    
            </tbody>
         </tr>         
        </table>    
        <div align="center" valign="top" style="margin-top:5px" id="search_div"> </div> 
        </form>
   </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_mrr_search_list_view")
{
	$ex_data = explode("_",$data);
	
	$supplier = trim(str_replace("'","",$ex_data[0]));
	$txt_item_category1 = trim(str_replace("'","",$ex_data[1]));
	$fromDate = trim(str_replace("'","",$ex_data[2]));
	$toDate = trim(str_replace("'","",$ex_data[3]));	
	$company = trim(str_replace("'","",$ex_data[4]));
	$dyes_lot = trim(str_replace("'","",$ex_data[5]));
	$mrr_no = trim(str_replace("'","",$ex_data[6]));
	
    if($db_type==0)
	{
		if ($fromDate!="" && $toDate!="") $sql_cond .= " and a.receive_date between '".change_date_format($fromDate, "yyyy-mm-dd", "-")."' and '".change_date_format($toDate, "yyyy-mm-dd", "-")."'"; else $sql_cond ="";
	}
	else if($db_type==2 || $db_type==1)
	{
		if ($fromDate!="" && $toDate!="") $sql_cond .= " and a.receive_date between '".change_date_format($fromDate, "yyyy-mm-dd", "-",1)."' and '".change_date_format($toDate, "yyyy-mm-dd", "-",1)."'"; else $sql_cond ="";
	}
	
	if(($company)!="") $sql_cond .= " and a.company_id=".str_replace("'","",$company).""; 
	if(($supplier)!="" && str_replace("'","",$supplier)!=0) $sql_cond .= " and a.supplier_id=".str_replace("'","",$supplier).""; 
	if(str_replace("'","",$txt_item_category1)!=0) $sql_cond.=" and b.item_category=".str_replace("'","",$txt_item_category1)." ";
	else $sql_cond.=" and b.item_category in(5,6,7,23)";
	if($dyes_lot !="") $sql_cond.=" and b.batch_lot ='$dyes_lot'";
	if($mrr_no!="") $sql_cond.=" and a.recv_number like '%$mrr_no'";
	
	$sql = "select b.mst_id, a.recv_number_prefix_num, a.recv_number, a.supplier_id, b.item_category, a.challan_no, c.lc_number, a.receive_date, a.receive_basis, b.id as trans_id, b.cons_quantity as rec_qnty, b.balance_qnty as balance_qnty, b.batch_lot 
	from inv_transaction b,inv_receive_master a left join com_btb_lc_master_details c on a.lc_no=c.id 
	where a.id=b.mst_id and  a.status_active=1 and a.entry_form in(4,29) $sql_cond and b.balance_qnty>0 order by b.mst_id desc";
	
	//echo $sql;
	
	$supplier_arr = return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$arr=array(1=>$item_category,2=>$supplier_arr,6=>$receive_basis_arr);
	echo  create_list_view("list_view", "MRR No, Item Category, Supplier Name, Challan No, LC No, Receive Date, Receive Basis, Lot, Receive Qnty, Balance Qty","110,130,130,70,70,70,100,80,80","1010","250",0, $sql , "js_set_value", "mst_id,recv_number,trans_id","", 1, "0,item_category,supplier_id,0,0,0,receive_basis,0,0,0", $arr, "recv_number,item_category,supplier_id,challan_no,lc_number,receive_date,receive_basis,batch_lot,rec_qnty,balance_qnty", "",'','0,0,0,0,0,3,0,0,2,2') ;
exit();
}

if($action=="populate_data_from_data")
{
	$data_ref=explode("**",$data);
	$sql = "select id, recv_number, company_id, receive_basis, receive_purpose, receive_date, challan_no, store_id, lc_no, supplier_id, exchange_rate, currency_id, lc_no, source, entry_form from inv_receive_master where id=$data_ref[0]";
	//echo $sql;
	$res = sql_select($sql);
	foreach($res as $row)
	{
		echo "$('#txt_received_id').val('".$row[csf("id")]."');\n";
		echo "$('#txt_mrr_no').val('".$row[csf("recv_number")]."');\n";
		echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
 		echo "$('#cbo_return_to').val('".$row[csf("supplier_id")]."');\n";
		if($row[csf("entry_form")]==29)
		{
			echo "disable_enable_fields( 'cbo_return_to', 0, '', '' );\n";
		}
		else
		{
			echo "disable_enable_fields( 'cbo_return_to', 1, '', '' );\n";
		}
		//right side list view
		echo"show_list_view('".$row[csf("id")]."','show_product_listview','list_product_container','requires/chemical_dyes_receive_return_controller','');\n";
   	}
	
	
	$sql = "select b.id as prod_id, b.item_group_id, b.item_description, b.current_stock, a.id, a.item_category, a.balance_qnty, a.cons_quantity, a.cons_rate, a.cons_uom, a.store_id 
	from inv_transaction a, product_details_master b 
	where a.id=$data_ref[1] and a.status_active=1 and a.prod_id=b.id and b.status_active in(1,3)";
	
	$store_name=return_library_array("select id,store_name from lib_store_location", "id","store_name"); 
 	//echo $sql."\n";	
	$item_name_arr=return_library_array("select id,item_name from lib_item_group", "id","item_name");
	$result = sql_select($sql);
	foreach($result as $row)
	{
		echo "$('#cbo_store_name').val('".$store_name[$row[csf("store_id")]]."');\n";
		echo "$('#category').val(".$row[csf("item_category")].");\n";
 		echo "$('#cbo_item_category').val(".$row[csf("item_category")].");\n";
		echo "$('#txt_item_group').val('".$item_name_arr[$row[csf("item_group_id")]]."');\n";
		echo "$('#txt_prod_id').val('".$row[csf("prod_id")]."');\n";
		echo "$('#store').val('".$row[csf("store_id")]."');\n";
		
		echo "$('#transaction_id').val('".$row[csf("id")]."');\n";
		//echo "$('#store').val('id');\n";
		//echo "$('#category_store_uom').val('".$row[csf("id")]."');\n";
		echo "$('#txt_item_description').val('".$row[csf("item_description")]."');\n";
		//echo "$('#txt_receive_qty').val('".$row[csf("cons_quantity")]."');\n";
		//echo "$('#txt_item_description').val('".$row[csf("cons_rate")]."');\n";
		//echo "$('#txt_receive_qty').val('');\n";
		echo "$('#txt_return_rate').val('".$row[csf("cons_rate")]."');\n";
		echo "$('#txt_curr_stock').val('".$row[csf("balance_qnty")]."');\n";
		
		echo "$('#txt_cons_quantity').val('".$row[csf("cons_quantity")]."');\n";
		//echo "$('#txt_curr_stock').val('".$row[csf("cons_quantity")]."');\n";
		echo "$('#txt_uom').val('".$unit_of_measurement[$row[csf("cons_uom")]]."');\n";
		echo "$('#uom').val('".$row[csf("cons_uom")]."');\n";
		echo "set_button_status(0, permission, 'fnc_dyes_receive_return_entry',1);\n";
		echo "$('#txt_receive_qty').val('');\n";
	}
		
	exit();	
}
?>
<?
//right side product list create here--------------------//
if($action=="show_product_listview")
{ 
	$company_id=return_field_value("company_id","inv_receive_master","id=$data","company_id");
	$store_id=return_field_value("a.store_id","inv_receive_master a,inv_transaction b","a.id=$data and a.id=b.mst_id and b.transaction_type=1 ","store_id");
	$stock_qty_arr=fnc_store_wise_stock($company_id,$store_id,'','');
	$sql_lot = sql_select("select auto_transfer_rcv, id from variable_settings_inventory where company_name = $company_id and variable_list = 29 and is_deleted = 0 and status_active = 1");
	$variable_lot=$sql_lot[0][csf("auto_transfer_rcv")];
	//print_r($stock_qty_arr);
	 $sql = "select b.item_category, b.transaction_type,c.id as prod_id, c.item_group_id, c.item_description, b.cons_quantity, b.balance_qnty, b.cons_rate, b.id as tr_id, b.batch_lot,b.prod_id,b.STORE_ID
	from inv_receive_master a, inv_transaction b, product_details_master c 
	where a.id=b.mst_id and b.prod_id=c.id and a.id=$data and b.transaction_type in(1,4) and b.item_category in (5,6,7,23) and a.status_active=1 and b.status_active=1 and b.BALANCE_QNTY>0";
	//echo $sql;
  	$result = sql_select($sql);
	// issue id 1925 Decision Pending 
	// $prodArr = [];
	//   foreach($result as $row)
	//   { 
	// 	$prodArr[$row['PROD_ID']] = $row['PROD_ID'];
	// 	$toStoreArr[$row['STORE_ID']] = $row['STORE_ID'];
	//   }
	//   $prodCond = implode(',',$prodArr);
	//   $storeCond = implode(',',$toStoreArr);
	//   $sql_state = "SELECT a.TRANSFER_QNTY,a.TO_PROD_ID FROM INV_ITEM_TRANSFER_DTLS a  WHERE   a.STATUS_ACTIVE =1 AND a.IS_DELETED=0   AND a.TO_PROD_ID IN($prodCond) AND a.to_store IN($storeCond)";
	//   foreach(sql_select($sql_state) as $fetch)
	//   {
	// 	$TransferQtyArr[$fetch['TO_PROD_ID']]['Tran_Qnty'] += $fetch['TRANSFER_QNTY'];
	//   }

	  
	$item_name_arr=return_library_array("select id,item_name from lib_item_group", "id","item_name");
	$i=1; 
 	?>
    	<table class="rpt_table" border="1" cellpadding="2" cellspacing="0" rules="all" width="480">
        <caption>Display Received Items </caption>
        	<thead>
                <tr>
                    <th width="20">SL</th>
                    <th width="80">Item Categ.</th>
                    <th width="70">Group Name</th>
                    <th width="60">Lot</th>
                    <th width="110">Description</th>
                    <th width="70">Recv. Qty</th>
                    <th>Bal. Qty</th>
                </tr>
            </thead>
            <tbody>
            	<?
				 $item_arr=array();
				 $item_group_arr=array();
				 $item_descript_arr=array();
				 foreach($result as $row)
				 { 
					if ($i%2==0)$bgcolor="#E9F3FF";						
					else $bgcolor="#FFFFFF";
					if($variable_lot) $dyes_lot=$row[csf('batch_lot')]; else $dyes_lot=""; 
					$stock_qty=$stock_qty_arr[$company_id][$store_id][$row[csf('item_category')]][$row[csf('prod_id')]][$dyes_lot]['stock'];
					// if( !in_array($value[csf("po_number")],$check_arr)) 
					?>
                	<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $row[csf("tr_id")];?>","item_details_form_input","requires/chemical_dyes_receive_return_controller")' style="cursor:pointer" >

                		<td><? echo $i; ?></td>
                    	<td><? echo $item_category[$row[csf("item_category")]]; ?></td>
                        <td><? echo $item_name_arr[$row[csf("item_group_id")]]; ?></td>
                         <td><? echo $row[csf("batch_lot")]; ?></td>
                        <td><? echo $row[csf("item_description")]; ?></td>
                        <td align="right"><? echo number_format($row[csf("cons_quantity")],2); ?></td>
                        <td align="right" title="Store Wise Stock"><? echo number_format($row[csf("balance_qnty")],2); //$TransferQtyArr[$row['PROD_ID']]['Tran_Qnty']; //number_format($row[csf("balance_qnty")],2); // number_format($stock_qty,2); ?></td>
                    </tr>
					<? 
                     $recive_qty+=$row[csf("cons_quantity")];
                     $item_ar[]=$row[csf("item_category")];
                     $item_group_arr[]=$row[csf("item_group_id")];
                     $item_descript_arr[]=$row[csf("item_description")];
                     $i++; 
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
	$company_id=return_field_value("company_id","inv_transaction","id=$data","company_id");
	$store_ids=return_field_value("a.store_id","inv_receive_master a,inv_transaction b","b.id=$data and a.id=b.mst_id and b.transaction_type in(1,4) ","store_id");
	$stock_qty_arr=fnc_store_wise_stock($company_id,$store_ids,'','');
	$sql_lot = sql_select("select auto_transfer_rcv, id from variable_settings_inventory where company_name = $company_id and variable_list = 29 and is_deleted = 0 and status_active = 1");
	$variable_lot=$sql_lot[0][csf("auto_transfer_rcv")];
	 $sql = "select b.id as prod_id, b.item_group_id, b.item_description, b.current_stock, a.id, a.item_category, a.balance_qnty, a.cons_quantity, a.cons_rate, a.cons_uom, a.store_id, a.floor_id, a.room, a.rack, a.self, a.bin_box, a.batch_lot
	from inv_transaction a,product_details_master bg
	where a.id=$data and a.status_active=1 and a.prod_id=b.id and b.status_active in(1,3)";
	//echo $sql."\n";
	$store_name=return_library_array("select id,store_name from lib_store_location", "id","store_name"); 
	$item_name_arr=return_library_array("select id,item_name from lib_item_group", "id","item_name");
	$result = sql_select($sql);
	foreach($result as $row)
	{
		if($variable_lot==1) $dyes_lot=$row[csf('batch_lot')]; else $dyes_lot="";
		$stock_qty=$stock_qty_arr[$company_id][$store_ids][$row[csf('item_category')]][$row[csf('prod_id')]][$dyes_lot]['stock'];
		
		echo "load_room_rack_self_bin('requires/chemical_dyes_receive_return_controller*5_6_7_23', 'store','store_td', '".$company_id."','"."',this.value);\n";
		echo "$('#cbo_store_name').val('".$row[csf("store_id")]."');\n";
		if($row[csf("floor_id")]>0)
		{
			echo "load_room_rack_self_bin('requires/chemical_dyes_receive_return_controller', 'floor','floor_td', '".$company_id."','"."','".$row[csf('store_id')]."',this.value);\n";
		}
		echo "$('#cbo_floor').val('".$row[csf("floor_id")]."');\n";
		if($row[csf("room")]>0)
		{
			echo "load_room_rack_self_bin('requires/chemical_dyes_receive_return_controller', 'room','room_td', '".$company_id."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."',this.value);\n";
		}
		echo "$('#cbo_room').val('".$row[csf("room")]."');\n";
		if($row[csf("rack")]>0)
		{
			echo "load_room_rack_self_bin('requires/chemical_dyes_receive_return_controller', 'rack','rack_td', '".$company_id."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."',this.value);\n";
		}
		echo "$('#txt_rack').val('".$row[csf("rack")]."');\n";
		if($row[csf("self")]>0)
		{
			echo "load_room_rack_self_bin('requires/chemical_dyes_receive_return_controller', 'shelf','shelf_td', '".$company_id."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."',this.value);\n";
		}
		echo "$('#txt_shelf').val('".$row[csf("self")]."');\n";
		if($row[csf("bin_box")]>0)
		{
			echo "load_room_rack_self_bin('requires/chemical_dyes_receive_return_controller', 'bin','bin_td', '".$company_id."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."','".$row[csf('bin_box')]."',this.value);\n";
		}
		echo "$('#cbo_bin').val('".$row[csf("bin_box")]."');\n";
		echo "$('#category').val(".$row[csf("item_category")].");\n";
 		echo "$('#cbo_item_category').val(".$row[csf("item_category")].");\n";
		echo "$('#txt_item_group').val('".$item_name_arr[$row[csf("item_group_id")]]."');\n";
		echo "$('#txt_prod_id').val('".$row[csf("prod_id")]."');\n";
		echo "$('#store').val('".$row[csf("store_id")]."');\n";
		
		echo "$('#transaction_id').val('".$row[csf("id")]."');\n";
		//echo "$('#store').val('id');\n";
		//echo "$('#category_store_uom').val('".$row[csf("id")]."');\n";
		echo "$('#txt_item_description').val('".$row[csf("item_description")]."');\n";
		echo "$('#txt_lot').val('".$row[csf("batch_lot")]."');\n";
		//echo "$('#txt_receive_qty').val('".$row[csf("cons_quantity")]."');\n";
		//echo "$('#txt_item_description').val('".$row[csf("cons_rate")]."');\n";
		//echo "$('#txt_receive_qty').val('');\n";
		echo "$('#txt_return_rate').val('".$row[csf("cons_rate")]."');\n";
		echo "$('#txt_curr_stock').val('".$row[csf("balance_qnty")]."');\n";
		//echo "$('#txt_curr_stock').val('".$stock_qty."');\n";
		
		echo "$('#txt_cons_quantity').val('".$row[csf("cons_quantity")]."');\n";
		//echo "$('#txt_curr_stock').val('".$row[csf("cons_quantity")]."');\n";
		echo "$('#txt_uom').val('".$unit_of_measurement[$row[csf("cons_uom")]]."');\n";
		echo "$('#uom').val('".$row[csf("cons_uom")]."');\n";
		echo "set_button_status(0, permission, 'fnc_dyes_receive_return_entry',1);\n";
		echo "$('#txt_receive_qty').val('');\n";

		echo "$('#cbo_store_name').attr('disabled','disabled');\n";
		echo "$('#cbo_floor').attr('disabled','disabled');\n";
		echo "$('#cbo_room').attr('disabled','disabled');\n";
		echo "$('#txt_rack').attr('disabled','disabled');\n";
		echo "$('#txt_shelf').attr('disabled','disabled');\n";
		echo "$('#cbo_bin').attr('disabled','disabled');\n";
	}

	exit();		
}


if($action=="populate_data_lib_data")
{
	$sql = sql_select("select auto_transfer_rcv, id from variable_settings_inventory where company_name = $data and variable_list = 29 and is_deleted = 0 and status_active = 1");
	echo $sql[0][csf("auto_transfer_rcv")];
	exit();
}

//data save update delete here------------------------------//
if($action=="save_update_delete")
{	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$txt_issue_qnty = str_replace("'","",$txt_receive_qty);
	$txt_rate = str_replace("'","",$txt_return_rate);
	$issue_stock_value = str_replace("'","",$txt_return_value);
	$update_id = str_replace("'","",$update_id);
	$variable_lot = str_replace("'","",$variable_lot);
	$txt_receive_qty = str_replace("'","",$txt_receive_qty);
	$txt_return_value = str_replace("'","",$txt_return_value);
	$txt_prod_id = str_replace("'","",$txt_prod_id);
	if($variable_lot==1) $dyes_lot   = str_replace("'","",$txt_lot); else $dyes_lot   = "";
	
    $max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$txt_prod_id and transaction_type in (1,4,5) and status_active = 1 and is_deleted = 0", "max_date");  
    if($max_recv_date != "" && $operation!=2)
    {
    	$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
		$return_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_receive_date)));
		if ($return_date < $max_recv_date) 
	    {
            echo "20**Issue Date Can not Be Less Than Last Receive Date Of This Lot";
            die;
		}
    }    
	
	//--------------Store Wise Stock------------------
	
	$lot_cond="";
	if($variable_lot == 1 && $dyes_lot !="") 
	{
		$store_lot_cond=" and lot='$dyes_lot'";
		$trans_lot_cond=" and batch_lot='$dyes_lot'";
	}
	$sql_store = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value, last_issued_qnty, last_purchased_qnty from inv_store_wise_qty_dtls where company_id=$cbo_company_id and prod_id=$txt_prod_id and category_id=$cbo_item_category and store_id=$store $store_lot_cond");
	$store_presentStock=$store_presentStockValue=$store_presentAvgRate=0;
	foreach($sql_store as $result)
	{
		$store_presentStock	=$result[csf("current_stock")];
		$store_presentStockValue =$result[csf("stock_value")];
		$store_presentAvgRate	=$result[csf("avg_rate_per_unit")];
		$store_before_receive_qnty = $result[csf("last_purchased_qnty")]; //stock qnty
		$store_before_last_issued_qnty = $result[csf("last_issued_qnty")]; //stock qnty
		$store_update_id= $result[csf("id")];
	}
	if(str_replace("'","",$update_id)!="") $bal_check_cond=" and id <> ".str_replace("'","",$update_id);
	$store_balance_check=sql_select("select sum((case when transaction_type in (1,4,5) then cons_quantity else 0 end)-(case when transaction_type in (2,3,6) then cons_quantity else 0 end)) as bal from inv_transaction where status_active=1 and is_deleted=0 and prod_id = $txt_prod_id and store_id=$cbo_store_name $bal_check_cond $trans_lot_cond ");
	$store_wise_qnty=$store_balance_check[0][csf("bal")]*1;
	$rtn_qnty=$txt_issue_qnty*1;
	if(($rtn_qnty>$store_wise_qnty) && $operation!=2)
	{
		echo "20**Return Quantity Not Allow Over Stock Quantity.";
		die;
	}
	$mrr_balance_check=sql_select("select balance_qnty from inv_transaction where status_active=1 and is_deleted=0 and transaction_type=1 and prod_id = $txt_prod_id and balance_qnty>0 and mst_id=$txt_received_id $bal_check_cond $trans_lot_cond ");
	$mrr_bal_qnty=$mrr_balance_check[0][csf("balance_qnty")]*1;
	$rtn_qnty=$txt_issue_qnty*1;
	if($bal_check_cond!="") $mrr_bal_qnty=$mrr_bal_qnty+$rtn_qnty;
	if($rtn_qnty>$mrr_bal_qnty)
	{
		echo "20**Return Quantity Not Allow Over MRR Balance Quantity.";
		die;
	}
	
	$up_trans_conds="";
	if($update_id>0) $up_trans_conds=" and id<>$update_id";
	$store_stock_sql="select sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as BALANCE_STOCK, sum((case when transaction_type in(1,4,5) then store_amount else 0 end)-(case when transaction_type in(2,3,6) then store_amount else 0 end)) as BALANCE_AMT 
	from inv_transaction 
	where status_active=1 and prod_id=$txt_prod_id and store_id=$cbo_store_name $up_trans_conds";
	//echo "30**$store_stock_sql";disconnect($con);die;
	$store_stock_sql_result=sql_select($store_stock_sql);
	$store_item_rate=0;
	if($store_stock_sql_result[0]["BALANCE_AMT"]!=0 && $store_stock_sql_result[0]["BALANCE_STOCK"]!=0)
	{
		$store_item_rate=$store_stock_sql_result[0]["BALANCE_AMT"]/$store_stock_sql_result[0]["BALANCE_STOCK"];
	}
	$issue_store_value = $store_item_rate*$txt_issue_qnty;
	
	
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here  
		///if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
 		
		//---------------Check Duplicate product in Same return number ------------------------//
		$duplicate = is_duplicate_field("b.id","inv_issue_master a, inv_transaction b","a.id=b.mst_id and a.issue_number=$txt_mrr_retrun_no and b.prod_id=$txt_prod_id and b.transaction_type=3"); 
		if($duplicate==1) 
		{
			echo "20**Duplication is Not Allowed in Same Return Number And Same Product.";
			disconnect($con);die;
		}
		
		//------------------------------Check Brand END---------------------------------------//
		
 		if(str_replace("'","",$txt_mrr_retrun_no)!="")
		{
			$new_return_number[0] = str_replace("'","",$txt_mrr_retrun_no);
			$id=str_replace("'","",$hidden_mrr_id);
			//General master table UPDATE here START----------------------//		
 			$field_array="entry_form*item_category*company_id*supplier_id*issue_date*challan_no*received_id*received_mrr_no*updated_by*update_date";
			$data_array="28*".$cbo_item_category."*".$cbo_company_id."*".$cbo_return_to."*".$txt_receive_date."*".$txt_challan_no."*".$txt_received_id."*".$txt_mrr_no."*'".$user_id."'*'".$pc_date_time."'";
			//General master table UPDATE here END---------------------------------------// 
		}
		else  	
		{	
		    if($db_type==0) {$insert_year="YEAR(insert_date)=";}
		    if($db_type==1 || $db_type==2) {$insert_year="extract(year from insert_date)=";}
			//General master table entry here START---------------------------------------txt_challan_no//		
			/*$id=return_next_id("id", "inv_issue_master", 1);		
			$new_return_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'DRR', date("Y",time()), 5, "select issue_number_prefix,issue_number_prefix_num from inv_issue_master where company_id=$cbo_company_id and entry_form=28 and $insert_year".date('Y',time())." order by issue_number_prefix_num DESC ", "issue_number_prefix", "issue_number_prefix_num" ));*/
			
			$id = return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master", $con);
			$new_return_number = explode("*", return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master",$con,1,$cbo_company_id,'DRR',28,date("Y",time()),0 ));
			
 			$field_array="id, issue_number_prefix, issue_number_prefix_num, issue_number, entry_form,item_category,company_id, supplier_id, issue_date,challan_no, received_id, received_mrr_no, inserted_by, insert_date";
			$data_array="(".$id.",'".$new_return_number[1]."','".$new_return_number[2]."','".$new_return_number[0]."',28,".$cbo_item_category.",".$cbo_company_id.",".$cbo_return_to.",".$txt_receive_date.",".$txt_challan_no.",".$txt_received_id.",".$txt_mrr_no.",'".$user_id."','".$pc_date_time."')";
			//General master table entry here END---------------------------------------// 
		}
		
		
		//adjust product master table START-------------------------------------//
		
		
		$sql = sql_select("select item_group_id,item_description,last_issued_qnty,avg_rate_per_unit,last_purchased_qnty,current_stock,stock_value from product_details_master where id=$txt_prod_id ");	//print_r($sql); die;
		
		$presentStock=$presentStockValue=$presentAvgRate=$laststock=0;
		$item_description="";
		foreach($sql as $result)
		{
			$presentStock			=$result[csf("current_stock")];
			$laststock				=$result[csf("last_issued_qnty")];
			$presentStockValue		=$result[csf("stock_value")];
			$presentAvgRate			=$result[csf("avg_rate_per_unit")];
			$item_group_id 			=$result[csf("item_group_id")];
			$item_description		=$result[csf("item_description")];
		}
		$nowStock 		=($presentStock-$txt_receive_qty);	
		$avg_rate_amount=str_replace("'","",$txt_issue_qnty)*$presentAvgRate; 
		$nowStockValue=0;
		$nowAvgRate=$presentAvgRate;
		if ($nowStock != 0){
			$nowStockValue 	= $presentStockValue-$avg_rate_amount;
			$nowAvgRate		= abs($nowStockValue/$nowStock);			
		}

		$field_array2="last_issued_qnty*current_stock*stock_value*updated_by*update_date";
		$data_array2="".$txt_receive_qty."*".$nowStock."*".number_format($nowStockValue,8,'.','')."*'".$user_id."'*'".$pc_date_time."'";		
		
		//adjust product master table END  -------------------------------------//
		//transaction table insert here START--------------------------------//
		//$transactionID = return_next_id("id", "inv_transaction", 1);
		$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
						
		$field_array3 = "id,mst_id,company_id,supplier_id,prod_id,item_category,transaction_type,transaction_date,store_id,floor_id,room,rack,self,bin_box,remarks,cons_uom,cons_quantity,cons_rate,cons_amount,inserted_by,insert_date,rcv_rate,rcv_amount,batch_lot,store_rate,store_amount";
 		$data_array3 = "(".$transactionID.",".$id.",".$cbo_company_id.",".$cbo_return_to.",".$txt_prod_id.",".$category.",3,".$txt_receive_date.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$txt_remark.",".$uom.",".$txt_issue_qnty.",'".number_format($presentAvgRate,10,'.','')."','".number_format($avg_rate_amount,8,'.','')."','".$user_id."','".$pc_date_time."',".number_format($txt_rate,10,'.','').",".number_format($issue_stock_value,8,'.','').",".$txt_lot.",".number_format($store_item_rate,10,'.','').",".number_format($issue_store_value,8,'.','').")"; 
		
		//transaction table insert here END ---------------------------------//
			
		//if LIFO/FIFO then START -----------------------------------------//
		//$field_array1 = "id,recv_trans_id";
		
		$field_array1 = "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
		$update_array = "balance_qnty*balance_amount*updated_by*update_date";
		$cons_rate=0; 
		$data_array1="";
		$updateID_array=array();
		$update_data=array();
		$issueQnty = $txt_receive_qty;
		//$mrrWiseIsID = return_next_id("id", "inv_mrr_wise_issue_details", 1);
		
		
		$isLIFOfifo=return_field_value("store_method","variable_settings_inventory","company_name=$cbo_company_id and variable_list=16");
		if($isLIFOfifo==2) $cond_lifofifo=" DESC"; else $cond_lifofifo=" ASC"; 
		
		$sql = sql_select("select id,cons_rate,balance_qnty,balance_amount from inv_transaction where id=$transaction_id and prod_id=$txt_prod_id and balance_qnty>0 and transaction_type in(1,4,5) and item_category=$category and store_id=$cbo_store_name order by id $cond_lifofifo");			
		foreach($sql as $result)
		{				
			$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
			
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
				if($data_array1!="") $data_array1 .= ",";  
				//$data_array1 .= "(".$mrrWiseIsID.",".$issue_trans_id.")";
				$data_array1 .= "(".$mrrWiseIsID.",".$issue_trans_id.",".$transactionID.",28,".$txt_prod_id.",".$issueQnty.",".number_format($cons_rate,10,'.','').",".number_format($amount,8,'.','').",'".$user_id."','".$pc_date_time."')";
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
				if($data_array1!="") $data_array1 .= ",";  
				//$data_array1 .= "(".$mrrWiseIsID.",".$issue_trans_id.")";
				$data_array1 .= "(".$mrrWiseIsID.",".$issue_trans_id.",".$transactionID.",28,".$txt_prod_id.",".$issueQnty.",".number_format($cons_rate,10,'.','').",".number_format($amount,8,'.','').",'".$user_id."','".$pc_date_time."')";
				//echo "20**".$data_array;die;
				//for update
				$updateID_array[]=$issue_trans_id; 
				$update_data[$issue_trans_id]=explode("*",("0*0*'".$user_id."'*'".$pc_date_time."'"));
				$issueQnty = $issueQntyBalance;
			}
			//$mrrWiseIsID++;
			
		}//end foreach
		//---Store Wise Stock----// $txt_prod_id $cbo_item_category  $cbo_company_id $store
		$cbo_company_id = str_replace("'","",$cbo_company_id);
		$cbo_store_name_id = str_replace("'","",$store);
		$item_category_id = str_replace("'","",$cbo_item_category);

		
		$store_stock_value 		= $store_presentAvgRate*$txt_receive_qty;
		$store_currentStock 	= $store_presentStock-$txt_receive_qty;
		$store_StockValue	 	= $store_presentStockValue-$issue_store_value;
		$store_avgRate			= abs($store_StockValue/$store_currentStock);
		$field_array_store_up="last_issued_qnty*cons_qty*amount*updated_by*update_date";		
		$data_array_store_up="".$txt_receive_qty."*".$store_currentStock."*".number_format($store_StockValue,8,'.','')."*'".$user_id."'*'".$pc_date_time."'";
		
		 
		$rID=$transID =$prodUpdate = $mrrWiseIssueID=$upTrID=$storeUpdate =true;
        if(str_replace("'","",$txt_mrr_retrun_no)!="")
		{
			$rID=sql_update("inv_issue_master",$field_array,$data_array,"id",$id,0);
		}
		else
		{
			$rID=sql_insert("inv_issue_master",$field_array,$data_array,0);
		}
		$transID = sql_insert("inv_transaction",$field_array3,$data_array3,0);
		$prodUpdate = sql_update("product_details_master",$field_array2,$data_array2,"id",$txt_prod_id,1); 
		if($data_array1!="")
		{		
			$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array1,$data_array1,1);
		}
		//if($mrrWiseIssueID) {echo "YOU";die;} else {echo "& AZIZ"; die;}
		//transaction table stock update here------------------------//
		if(count($updateID_array)>0)
		{
			//echo bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array);die;
 			$upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array),1);
		}
		//echo "10**jahid=$store_update_id";die;	
		if($store_update_id!='')
		{
			$storeUpdate = sql_update("inv_store_wise_qty_dtls",$field_array_store_up,$data_array_store_up,"id",$store_update_id,1);
		}
		//echo "10**".$rID.'='.$transID.'='.$prodUpdate.'='.$mrrWiseIssueID.'='.$upTrID.'='.$storeUpdate."=".$store_update_id;die;
        //echo "INSERT INTO inv_store_wise_qty_dtls (".$field_array.") VALUES ".$data_array.""; die;
		 
		if($db_type==0)
		{
			
			if($rID && $transID && $prodUpdate && $mrrWiseIssueID && $upTrID && $storeUpdate)
			{
				mysql_query("COMMIT");  
				echo "0**".$new_return_number[0]."**".$id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_return_number[0];
			}
		}
		if($db_type==2 || $db_type==1 )
		{  
			if($rID && $transID && $prodUpdate && $mrrWiseIssueID && $upTrID && $storeUpdate)
			{
				oci_commit($con);
				echo "0**".$new_return_number[0]."**".$id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$new_return_number[0];  
			}
		}
		disconnect($con);
		die;
				
	}
	
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }


		$is_posted=sql_select("select is_posted_account from inv_issue_master where id=$hidden_mrr_id and status_active=1");

		if($is_posted[0][csf("is_posted_account")] == 1)
		{
			echo "20**Already Posted in Accounts, so update and delete is not allowed";
			disconnect($con);die;
		}

		//check update id
		if( str_replace("'","",$update_id) == "" )
		{
			echo "15";
			disconnect($con);
			die; 
		}
		
	
		$sql = sql_select( "select a.id,a.avg_rate_per_unit,a.current_stock,a.stock_value, b.cons_quantity, b.cons_amount, b.store_amount from product_details_master a, inv_transaction b where a.id=b.prod_id and b.id=$update_id and a.item_category_id in (5,6,7,23) and b.item_category in (5,6,7,23) and b.transaction_type=3" );
		$before_prod_id=$before_issue_qnty=$before_stock_qnty=$before_stock_value=0;
		foreach($sql as $result)
		{
			$before_prod_id 	= $result[csf("id")];
 			$before_stock_qnty 	= $result[csf("current_stock")];
			$before_stock_value = $result[csf("stock_value")];
			$before_avg_rate_per_unit = $result[csf("avg_rate_per_unit")];
			//before quantity and stock value
			$before_issue_qnty	= $result[csf("cons_quantity")];
			$before_issue_value	= $result[csf("cons_amount")];
			$before_store_amount	= $result[csf("store_amount")];
		}

		
		
		$sql = sql_select("select avg_rate_per_unit,current_stock,stock_value from product_details_master where id=$txt_prod_id and item_category_id in (5,6,7,23)");
		$curr_avg_rate=$curr_stock_qnty=$curr_stock_value=0;
		foreach($sql as $result)
		{
			$curr_avg_rate 		= $result[csf("avg_rate_per_unit")];
			$curr_stock_qnty 	= $result[csf("current_stock")];
			$curr_stock_value 	= $result[csf("stock_value")];
		}
		
		$avg_rate_amount=$txt_issue_qnty*$curr_avg_rate; 
		
		//weighted and average rate START here------------------------//
		//product master table data UPDATE START----------------------//		
		$update_array	= "last_issued_qnty*current_stock*stock_value*updated_by*update_date";
		if($before_prod_id==$txt_prod_id)
		{
			$adj_stock_qnty = $curr_stock_qnty+$before_issue_qnty-$txt_issue_qnty; // CurrentStock + Before Issue Qnty - Current Issue Qnty
			
			if($adj_stock_qnty<0)
			{
				echo "30**Stock cannot be less than zero.";
				disconnect($con);
				die;
			}
			$adj_stock_val=0;
			$adj_avgrate=$curr_avg_rate;
			if ($adj_stock_qnty != 0){
				$adj_stock_val  = $curr_stock_value+$before_issue_value-$avg_rate_amount;
				$adj_avgrate	= abs($adj_stock_val/$adj_stock_qnty);
			} 			
			$data_array		= "".$txt_issue_qnty."*".$adj_stock_qnty."*".number_format($adj_stock_val,8,'.','')."*'".$user_id."'*'".$pc_date_time."'";

			//now current stock
			//$curr_avg_rate 		= $adj_avgrate;
			$curr_stock_qnty 	= $adj_stock_qnty;
			$curr_stock_value 	= $adj_stock_val;
		}
		else
		{
			$updateID_array = $update_data = array();
			//before product adjust
			$adj_before_stock_qnty 	= $before_stock_qnty+$before_issue_qnty; // CurrentStock + Before Issue Qnty			
			if($adj_before_stock_qnty<0) //Aziz
			{
				echo "30**Stock cannot be less than zero.";
				disconnect($con);
				die;
			}
			
			$adj_before_stock_val=0;
			$adj_before_avgrate=$before_avg_rate_per_unit;
			if ($adj_before_stock_qnty != 0){
				$adj_before_stock_val  	= $before_stock_value+$before_issue_value; // CurrentStockValue + Before Issue Value
				$adj_before_avgrate		= abs($adj_before_stock_val/$adj_before_stock_qnty);				
			}

			$updateID_array[]=$before_prod_id;
			$update_data[$before_prod_id]=explode("*",("".$txt_issue_qnty."*".$adj_before_stock_qnty."*".number_format($adj_before_stock_val,8,'.','')."*'".$user_id."'*'".$pc_date_time."'"));
			
			//current product adjust
			$adj_curr_stock_qnty = 	$curr_stock_qnty-$txt_issue_qnty; // CurrentStock + Before Issue Qnty
			$adj_curr_stock_val=0;
			$adj_curr_avgrate=$curr_avg_rate;
			if ($adj_curr_stock_qnty != 0){
				$adj_curr_stock_val  = 	$curr_stock_value-$avg_rate_amount; // CurrentStockValue + Before Issue Value
				$adj_curr_avgrate	 =	abs($adj_curr_stock_val/$adj_curr_stock_qnty);
			}

			$updateID_array[]=$txt_prod_id;
			$update_data[$txt_prod_id]=explode("*",("".$txt_issue_qnty."*".$adj_curr_stock_qnty."*".number_format($adj_curr_stock_val,8,'.','')."*'".$user_id."'*'".$pc_date_time."'"));

			//now current stock
			//$curr_avg_rate 		= $adj_curr_avgrate;
			$curr_stock_qnty 	= $adj_curr_stock_qnty;
			$curr_stock_value 	= $adj_curr_stock_val;
		}
  		//------------------ product_details_master END--------------//
		
		//---Store Wise Stock----// $txt_prod_id $cbo_item_category  $cbo_company_id $store
		$cbo_company_id = str_replace("'","",$cbo_company_id);
		$cbo_store_name_id = str_replace("'","",$store);
		$item_category_id = str_replace("'","",$cbo_item_category);
		$variable_lot = str_replace("'","",$variable_lot);
		if($variable_lot==1) $dyes_lot   = $txt_lot; else $dyes_lot   = "";
		$field_array_store_up="last_issued_qnty*cons_qty*amount*updated_by*update_date";
		if($before_prod_id==str_replace("'","",$txt_prod_id))
		{
			$store_adj_stock_qnty = (($store_presentStock+$before_issue_qnty)-$txt_issue_qnty); // CurrentStock + Before Issue Qnty - Current Issue Qnty			
			$store_adj_stock_val  = (($store_presentStockValue+$before_store_amount)-$issue_store_value); // CurrentStockValue + Before Issue 		
			$data_array_store_up= "".$txt_issue_qnty."*".$store_adj_stock_qnty."*".number_format($store_adj_stock_val,8,'.','')."*'".$user_id."'*'".$pc_date_time."'";			
		}
			
		//weighted and average rate END here-------------------------//
		//transaction table START--------------------------//
		$update_id = str_replace("'","",$update_id);
		$update_array_trans = "balance_qnty*balance_amount*updated_by*update_date";
		$sql = sql_select("select a.id,a.balance_qnty,a.balance_amount,b.issue_qnty,b.rate,b.amount from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id=$update_id and b.entry_form=28 and a.item_category in (5,6,7,23)"); 
		$updateID_array_trans = array();
		$update_data_trans = array();
		foreach($sql as $result)
		{
			$adjBalance = $result[csf("balance_qnty")]+$result[csf("issue_qnty")];
			$adjAmount = $result[csf("balance_amount")]+$result[csf("amount")];
			$updateID_array_trans[]=$result[csf("id")]; 
			$update_data_trans[$result[csf("id")]]=explode("*",("".$adjBalance."*".$adjAmount."*'".$user_id."'*'".$pc_date_time."'"));
		}
		
		
		$query1=$query2=$query3=true;
		if($before_prod_id==$txt_prod_id)
		{
			$query1 = sql_update("product_details_master",$update_array,$data_array,"id",$before_prod_id,1);
		}
		else
		{
			$query1=execute_query(bulk_update_sql_statement("product_details_master","id",$update_array,$update_data,$updateID_array),1);
		}
		if(count($updateID_array_trans)>0)
		{
 			$query2=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array_trans,$update_data_trans,$updateID_array_trans),1);
		}
		if(count($updateID_array_trans)>0)
		{
			 $updateIDArray = implode(",",$updateID_array_trans);
			 $query3 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id=$update_id and entry_form=28",1);
		}
		
		 //****************************************** BEFORE ENTRY ADJUST END *****************************************//
		
		//############## SAVE POINT START  ###################
		//$savepoint="updatesql";
		//mysql_query("SAVEPOINT $savepoint");
		//############## SAVE POINT END    ###################
				
  		$id=str_replace("'","",$hidden_mrr_id);;
		//General Item master table UPDATE here START----------------------//		
		$field_array_mst="entry_form*company_id*supplier_id*issue_date*challan_no*received_id*received_mrr_no*updated_by*update_date";
		$data_array_mst="28*".$cbo_company_id."*".$cbo_return_to."*".$txt_receive_date."*".$txt_challan_no."*".$txt_received_id."*".$txt_mrr_no."*'".$user_id."'*'".$pc_date_time."'";
			
		//General Item master table UPDATE here END---------------------------------------//	 

		//transaction table insert here START--------------------------------//
		
		$txt_rate = str_replace("'","",$txt_return_rate);
 		$issue_stock_value = str_replace("'","",$txt_return_value);
		
 		$field_array_trans = "company_id*supplier_id*prod_id*item_category*transaction_type*transaction_date*store_id*floor_id*room*rack*self*bin_box*remarks*cons_uom*cons_quantity*cons_rate*cons_amount*updated_by*update_date*rcv_rate*rcv_amount*batch_lot*store_rate*store_amount";
 		$data_array_trans = "".$cbo_company_id."*".$cbo_return_to."*".$txt_prod_id."*".$category."*3*".$txt_receive_date."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$txt_remark."*".$uom."*".$txt_issue_qnty."*'".number_format($curr_avg_rate,10,'.','')."'*'".number_format($avg_rate_amount,8,'.','')."'*'".$user_id."'*'".$pc_date_time."'*".number_format($txt_rate,10,'.','')."*".number_format($issue_stock_value,8,'.','')."*".$txt_lot."*".number_format($store_item_rate,10,'.','')."*".number_format($issue_store_value,8,'.','')."";
 		
		//transaction table insert here END ---------------------------------//
		//if LIFO/FIFO then START -----------------------------------------//
		$cons_rate=0;
		$field_array_mrr = "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
		$update_array_trans_issue = "balance_qnty*balance_amount*updated_by*update_date";
		$updateID_array_trans_issue=array();
		$update_data_trans_issue=array();
		$issueQnty = $txt_issue_qnty;
		//$mrrWiseIsID = return_next_id("id", "inv_mrr_wise_issue_details", 1);  
		
		$isLIFOfifo=return_field_value("store_method","variable_settings_inventory","company_name=$cbo_company_id and variable_list=16");
		if($isLIFOfifo==2) $cond_lifofifo=" DESC"; else $cond_lifofifo=" ASC"; 
		
		$sql = sql_select("select id,cons_rate,balance_qnty,balance_amount from inv_transaction where id=$transaction_id and prod_id=$txt_prod_id and balance_qnty>0 and transaction_type in(1,4,5) and item_category=$category and store_id=$cbo_store_name order by id $cond_lifofifo");			
		foreach($sql as $result)
		{	
			$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);			
			
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
				if($data_array!="") $data_array .= ",";  
				$data_array_mrr .= "(".$mrrWiseIsID.",".$issue_trans_id.",".$update_id.",28,".$txt_prod_id.",".$issueQnty.",".number_format($cons_rate,10,'.','').",".number_format($amount,8,'.','').",'".$user_id."','".$pc_date_time."')";
				//for update
				$updateID_array_trans_issue[]=$issue_trans_id; 
				$update_data_trans_issue[$issue_trans_id]=explode("*",("".$issueQntyBalance."*".$issueStockBalance."*'".$user_id."'*'".$pc_date_time."'"));
				break;
			}
			else if($issueQntyBalance<0)
			{
				
				$issueQntyBalance  = $issueQnty-$balance_qnty;				
				$issueQnty = $balance_qnty;				
				$amount = $issueQnty*$cons_rate;
				
				//for insert
				if($data_array!="") $data_array .= ",";  
				$data_array_mrr .= "(".$mrrWiseIsID.",".$issue_trans_id.",".$update_id.",28,".$txt_prod_id.",".$issueQnty.",".number_format($cons_rate,10,'.','').",".number_format($amount,8,'.','').",'".$user_id."','".$pc_date_time."')";
				//for update
				$updateID_array_trans_issue[]=$issue_trans_id; 
				$update_data_trans_issue[$issue_trans_id]=explode("*",("0*0*'".$user_id."'*'".$pc_date_time."'"));
				$issueQnty = $issueQntyBalance;
			}
			//$mrrWiseIsID++;
			
		}//end foreach 
		
			
		$rID=$transID=$mrrWiseIssueID=$upTrID=$storeupdate=true;
		$rID=sql_update("inv_issue_master",$field_array_mst,$data_array_mst,"id",$id,1);
		$transID = sql_update("inv_transaction",$field_array_trans,$data_array_trans,"id",$update_id,1); 
		//mrr wise issue data insert here----------------------------//
		if($data_array_mrr!="")
		{		
			$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,1);
		}
		//transaction table stock update here------------------------//
		
		if(count($updateID_array_trans_issue)>0)
		{
 			$upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array_trans_issue,$update_data_trans_issue,$updateID_array_trans_issue),1);
		}
		
		if($store_update_id!='')
		{
			$storeupdate = sql_update("inv_store_wise_qty_dtls",$field_array_store_up,$data_array_store_up,"id",$store_update_id,1);
		}
		
		//echo "10**$query1=$query2=$query3=$rID=$transID=$mrrWiseIssueID=$upTrID=$storeupdate";die;
 		//echo "10**".$storeupdate." && ".$query1." && ".$query2." && ".$query3." && ".$upTrID." && ".$rID." && ".$transID." && ".$data_array." && ".$upTrID;mysql_query("ROLLBACK");mysql_query("ROLLBACK TO $savepoint"); die; 
		if($db_type==0)
		{	
			if($query1 && $query2 && $query3 && $rID && $transID && $upTrID && $mrrWiseIssueID && $storeupdate)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$txt_mrr_retrun_no)."**".$id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				mysql_query("ROLLBACK TO $savepoint");
				echo "10**".str_replace("'","",$txt_mrr_retrun_no);
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($query1 && $query2 && $query3 && $rID && $transID && $upTrID && $mrrWiseIssueID && $storeupdate )
			{
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_mrr_retrun_no)."**".$id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_mrr_retrun_no);
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
 	}
 	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }

		$is_posted=sql_select("select is_posted_account from inv_issue_master where id=$update_id and status_active=1");

		if($is_posted[0][csf("is_posted_account")] == 1)
		{
			echo "20**Already Posted in Accounts, so update and delete is not allowed";
			disconnect($con);die;
		}
		
		//check update id
		if( str_replace("'","",$update_id) == "" )
		{
			echo "15";
			disconnect($con);
			die; 
		}
		
	
		$sql = sql_select( "select a.id, a.avg_rate_per_unit, a.current_stock, a.stock_value, b.cons_quantity, b.cons_amount, b.store_amount from product_details_master a, inv_transaction b where a.id=b.prod_id and b.id=$update_id and a.item_category_id in (5,6,7,23) and b.item_category in (5,6,7,23) and b.transaction_type=3" );
		$before_prod_id=$before_issue_qnty=$before_stock_qnty=$before_stock_value=0;
		foreach($sql as $result)
		{
			$before_prod_id 	= $result[csf("id")];
 			$before_stock_qnty 	= $result[csf("current_stock")];
			$before_stock_value = $result[csf("stock_value")];
			$before_avg_rate_per_unit = $result[csf("avg_rate_per_unit")];
			//before quantity and stock value
			$before_issue_qnty	= $result[csf("cons_quantity")];
			$before_issue_value	= $result[csf("cons_amount")];
			$before_store_amount		= $row[csf("store_amount")];
		}
		
		//current product ID
		$txt_prod_id = str_replace("'","",$txt_prod_id);
		$txt_issue_qnty = str_replace("'","",$txt_receive_qty);
		$txt_return_value = str_replace("'","",$txt_return_value);
		
		$sql = sql_select("select avg_rate_per_unit,current_stock,stock_value from product_details_master where id=$txt_prod_id and item_category_id in (5,6,7,23)");
		$curr_avg_rate=$curr_stock_qnty=$curr_stock_value=0;
		foreach($sql as $result)
		{
			$curr_avg_rate 		= $result[csf("avg_rate_per_unit")];
			$curr_stock_qnty 	= $result[csf("current_stock")];
			$curr_stock_value 	= $result[csf("stock_value")];
		}
		$avg_rate_amount=$txt_issue_qnty*$curr_avg_rate; 
		//weighted and average rate START here------------------------//
		//product master table data UPDATE START----------------------//		
		$update_array	= "last_issued_qnty*current_stock*stock_value*updated_by*update_date";
		$adj_stock_qnty = $curr_stock_qnty+$before_issue_qnty; // CurrentStock + Before Issue Qnty - Current Issue Qnty			
		$adj_stock_val=0;
		$adj_avgrate=$curr_avg_rate;
		if ($adj_stock_qnty != 0){
			$adj_stock_val  = $curr_stock_value+$before_issue_value; // CurrentStockValue + Before Issue Value - Current Issue Value
			$adj_avgrate	= abs($adj_stock_val/$adj_stock_qnty);
		} 

		$data_array		= "".$txt_issue_qnty."*".$adj_stock_qnty."*".number_format($adj_stock_val,8,'.','')."*'".$user_id."'*'".$pc_date_time."'";
					
		//now current stock
		//$curr_avg_rate 		= $adj_avgrate;
		$curr_stock_qnty 	= $adj_stock_qnty;
		$curr_stock_value 	= $adj_stock_val;
  		//------------------ product_details_master END--------------//
		
		//---Store Wise Stock----// $txt_prod_id $cbo_item_category  $cbo_company_id $store
		$cbo_company_id = str_replace("'","",$cbo_company_id);
		$cbo_store_name_id = str_replace("'","",$store);
		$item_category_id = str_replace("'","",$cbo_item_category);
		$variable_lot = str_replace("'","",$variable_lot);
		if($variable_lot==1) $dyes_lot   = $txt_lot; else $dyes_lot   = "";
		
		$field_array_store_up="last_issued_qnty*cons_qty*amount*updated_by*update_date";
		$store_adj_stock_qnty = ($store_presentStock+$before_issue_qnty); // CurrentStock + Before Issue Qnty - Current Issue Qnty
     	$store_adj_stock_val  = ($store_presentStockValue+$before_store_amount); // CurrentStockValue + Before Issue 		
		$data_array_store_up= "".$txt_issue_qnty."*".$store_adj_stock_qnty."*".number_format($store_adj_stock_val,8,'.','')."*'".$user_id."'*'".$pc_date_time."'";
		
		$update_array_trans = "balance_qnty*balance_amount*updated_by*update_date";
		//echo "10**select a.id,a.balance_qnty,a.balance_amount,b.issue_qnty,b.rate,b.amount from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id=$update_id and b.entry_form=28 and a.item_category in (5,6,7,23)";die;
		$sql = sql_select("select a.id,a.balance_qnty,a.balance_amount,b.issue_qnty,b.rate,b.amount from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id=$update_id and b.entry_form=28 and a.item_category in (5,6,7,23)"); 
		$updateID_array_trans = array();
		$update_data_trans = array();
		foreach($sql as $result)
		{
			$adjBalance = $result[csf("balance_qnty")]+$result[csf("issue_qnty")];
			$adjAmount = $result[csf("balance_amount")]+$result[csf("amount")];
			$updateID_array_trans[]=$result[csf("id")]; 
			$update_data_trans[$result[csf("id")]]=explode("*",("".$adjBalance."*".$adjAmount."*'".$user_id."'*'".$pc_date_time."'"));
		}
		
		
		
		//transaction table START--------------------------//
		$update_id = str_replace("'","",$update_id);
		$field_array_trans="updated_by*update_date*status_active*is_deleted";
		$data_array_trans= "".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
		
		//echo "10**".bulk_update_sql_statement("inv_transaction","id",$update_array_trans,$update_data_trans,$updateID_array_trans);die;
		$query1=$query2=$query3=$storeupdate=$upTrID= true;
		$query1 = sql_update("product_details_master",$update_array,$data_array,"id",$before_prod_id,1);
		$query2=sql_update("inv_transaction",$field_array_trans,$data_array_trans,"id",$update_id,1);
		$query3 = execute_query("update inv_mrr_wise_issue_details set updated_by='".$_SESSION['logic_erp']['user_id']."' , update_date='".$pc_date_time."' , status_active=0 , is_deleted =1 WHERE issue_trans_id in($update_id) and entry_form=28",1);
		$storeupdate = sql_update("inv_store_wise_qty_dtls",$field_array_store_up,$data_array_store_up,"id",$store_update_id,1);
		if(count($updateID_array_trans)>0)
		{
 			$upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array_trans,$update_data_trans,$updateID_array_trans),1);
		}
		
		
		//echo "10**$query1=$query2=$query3=$upTrID=$storeupdate";oci_rollback($con);disconnect($con);die;
		if($db_type==0)
		{	
			if($query1 && $query2 && $query3 && $upTrID && $storeupdate)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$txt_mrr_retrun_no)."**".$id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				mysql_query("ROLLBACK TO $savepoint");
				echo "10**".str_replace("'","",$txt_mrr_retrun_no);
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($query1 && $query2 && $query3 && $upTrID && $storeupdate )
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_mrr_retrun_no)."**".$id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_mrr_retrun_no);
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
 	}
	/*else if ($operation==2) 
	}*/
}
/* Return ID List View Action*/
if($action=="return_number_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);  
?>
     
<script>
	function js_set_value(mrr)
	{
 		$("#hidden_return_number").val(mrr); // mrr number
		parent.emailwindow.hide();
	}
</script>
</head>
<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">

	<table width="880" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <tr>                	 
                  <!--  <th width="150">Search By</th>-->
                    <th width="300" align="center">Enter Return Number</th>
                    <th width="300">Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                <tr>                    
                    <!--<td>
                        <?  
                            //$search_by = array(1=>'Return Number');
							//$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							//echo create_drop_down( "cbo_search_by", 120, $search_by,"",0, "--Select--", "",1,0 );
                        ?>
                    </td>-->
                    <td width="" align="center">				
                        <input type="text" style="width:230px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td>    
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px" placeholder="From Date" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px" placeholder="To Date" />
                    </td> 
                    <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>, 'create_return_search_list_view', 'search_div', 'chemical_dyes_receive_return_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                    </td>
            </tr>
        	<tr>                  
            	<td align="center" height="40" valign="middle" colspan="5">
					<? echo load_month_buttons(1);  ?>
                    <!-- Hidden field here-------->
                     <input type="hidden" id="hidden_return_number" value="" />
                    <!-- ---------END------------->
                </td>
            </tr>    
            </tbody>
         </tr>         
        </table>    
        <div align="center" valign="top" id="search_div"> </div> 
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
	$search_common = $ex_data[0];
	$txt_date_from = $ex_data[1];
	$txt_date_to = $ex_data[2];
	$company = $ex_data[3];
	//echo $company; die;
	
	$sql_cond="";	 
	if( $txt_date_from!="" || $txt_date_to!="" )
	{
		 if($db_type==0){$sql_cond .= " and issue_date  between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";}
		 if($db_type==2 || $db_type==1){$sql_cond .= " and issue_date  between '".change_date_format($txt_date_from, "yyyy-mm-dd", "-",1)."' and '".change_date_format($txt_date_to, "yyyy-mm-dd", "-",1)."'";}
	 }
	if($company!="") $sql_cond .= " and company_id='$company'";
	if($search_common!="") $sql_cond .= " and issue_number_prefix_num='$search_common'";
	
	$sql = "select id,issue_number_prefix_num,issue_number,company_id,supplier_id,issue_date,item_category,received_id,received_mrr_no   
			from inv_issue_master 
			where item_category in(5,6,7,23) and  status_active=1 and entry_form=28 $sql_cond";
	
	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$arr=array(1=>$company_arr,2=>$supplier_arr);
 	echo create_list_view("list_view", "Return No, Company Name, Returned To, Return Date, Receive MRR","100,200,200,100","850","260",0, $sql , "js_set_value", "issue_number", "", 1, "0,company_id,supplier_id,0,0", $arr, "issue_number_prefix_num,company_id,supplier_id,issue_date,received_mrr_no","","",'0,0,0,3,0') ;	
 	exit();
}
if($action=="populate_master_from_data")
{  
	
	$sql = "select id,issue_number,company_id,supplier_id,issue_date,challan_no,item_category,received_id,received_mrr_no ,is_posted_account  
			from inv_issue_master 
			where issue_number='$data' and entry_form=28";
	//echo $sql;
	$res = sql_select($sql);
	foreach($res as $row)
	{
		echo "$('#hidden_mrr_id').val('".$row[csf("id")]."');\n";
		echo "$('#txt_mrr_retrun_no').val('".$row[csf("issue_number")]."');\n";
 		echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
 		echo "$('#cbo_return_to').val('".$row[csf("supplier_id")]."');\n";
		echo "$('#txt_challan_no').val('".$row[csf("challan_no")]."');\n";
		echo "$('#txt_receive_date').val('".change_date_format($row[csf("issue_date")])."');\n";
		echo "$('#txt_mrr_no').val('".$row[csf("received_mrr_no")]."');\n";
		echo "$('#txt_received_id').val('".$row[csf("received_id")]."');\n";
		//right side list view
		echo"show_list_view('".$row[csf("received_id")]."','show_product_listview','list_product_container','requires/chemical_dyes_receive_return_controller','');\n";
 	   echo "set_button_status(1, permission, 'fnc_dyes_receive_return_entry',1,1);\n";

		$msg="Already Posted in Accounts";
        if($row[csf("is_posted_account")]==1){
			echo "$('#posted_account_td').text('".$msg."');\n";
		}else{
			
		}
	
	 $sql = sql_select("select b.id as tr_id, c.id as prod_id
	from inv_receive_master a, inv_transaction b, product_details_master c 
	where a.id=b.mst_id and b.prod_id=c.id and a.recv_number='".$row[csf("received_mrr_no")]."' and b.transaction_type=1 and b.item_category in (5,6,7,23)");
	 foreach($sql as $row_t)
	 {
		 echo "$('#transaction_id').val('".$row_t[csf("tr_id")]."');\n";
	 }
	
	if($row[csf("is_posted_account")]==1)
	{
		echo "disable_enable_fields( 'cbo_company_id*txt_challan_no*txt_receive_date*txt_mrr_no', 1, '', '' );\n";
	}
	else
	{
		echo "disable_enable_fields( 'cbo_company_id*txt_challan_no*txt_receive_date*txt_mrr_no', 0, '', '' );\n";
	}
	
	
   	}	
	exit();	
}
/*After Save List View*/
if($action=="show_dtls_list_view")
{
	$ex_data = explode("**",$data);
	//echo($data[0]); die;
	$return_number = $ex_data[0];
	$ret_mst_id = $ex_data[1];
	//echo $ret_mst_id;
	$cond="";
	if($return_number!="") $cond .= " and a.issue_number='$return_number'";
	//if($ret_mst_id!="") $cond .= " and a.id='$ret_mst_id'";
	$item_name_arr=return_library_array("select id,item_name from lib_item_group", "id","item_name");
    if($db_type==0) {$group_concat="group_concat";  $prod_des="group_concat(c.sub_group_name,' ',c.item_description,' ',c.item_size )";}
    if($db_type==1 || $db_type==2) {$group_concat="wm_concat"; $prod_des="(c.sub_group_name ||' '||c.item_description ||' '||c.item_size )";}
  
	$sql = "select a.id as mst_id,  a.issue_number, a.company_id, a.supplier_id, a.issue_date, a.received_id, a.received_mrr_no, b.id, b.item_category, b.prod_id, b.cons_quantity, b.cons_uom, b.cons_rate as cons_rate, b.cons_amount as cons_amount, $prod_des as item_description, c.item_group_id 
	from inv_issue_master a,   inv_transaction b,product_details_master c 
    where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,3) and c.is_deleted=0  $cond ";
	
	$result = sql_select($sql);
	$i=1;
	$rettotalQnty=0;
	$rcvtotalQnty=0;
	$totalAmount=0;
	?> 
     	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:790px" rules="all">
        	<thead>
            	<tr>
                	<th>SL</th>
                    <th>Item Category</th>
                    <th>Item Group</th>
                    <th>Item Description</th>
                    <th>Returned Qty.</th>
                    <th>UOM</th>
                    <th>Rate</th>
                    <th>Return Value</th>
                    <th>Product Id</th> 
                </tr>
            </thead>
            <tbody>
            	<? 
				foreach($result as $row){					
					if($i%2==0)
						$bgcolor="#E9F3FF";
					else 
						$bgcolor="#FFFFFF";
					$pro_id=$row[csf("prod_id")];
					$received_mrr_no=$row[csf("received_mrr_no")];
			
					$sqlTr = sql_select("select b.balance_qnty,b.cons_quantity from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.prod_id='$pro_id' and b.transaction_type=1 and b.item_category in (5,6,7,23) and  a.recv_number='$received_mrr_no'");
				
					$rcvQnty = $sqlTr[0][csf('balance_qnty')];
			        $total_cons_quantity=$sqlTr[0][csf('cons_quantity')];
					
					$rettotalQnty +=$row[csf("cons_quantity")];
					//$rcvtotalQnty +=$rcvQnty;
					$totalAmount +=$row[csf("cons_amount")];		
 				?>
                	<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $row[csf("id")];?>,<? echo $rcvQnty;?>,<? echo $total_cons_quantity;?>,<? echo $row[csf("mst_id")];?>","child_form_input_data","requires/chemical_dyes_receive_return_controller")' style="cursor:pointer" >
                        <td width="30"><?php echo $i; ?></td>
                        <td width="100"><p><?php echo  $item_category[$row[csf("item_category")]]; ?></p></td>
                        <td width="100"><p><?php echo $item_name_arr[$row[csf("item_group_id")]]; ?></p></td>
                        <td width="180"><p><?php echo $row[csf("item_description")]; ?></p></td>
                        <td width="70" align="right"><p><?php echo number_format($row[csf("cons_quantity")],2); ?></p></td>
                        <!--<td width="70" align="right"><p><!?php echo $rcvQnty; ?></p></td>-->
                        <td width="60" align="center"><p><?php echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                        <td width="70" align="right"><p><?php echo number_format($row[csf("cons_rate")],4); ?></p></td>
                        <td width="70" align="right"><p><?php echo number_format($row[csf("cons_amount")],2); ?></p></td>
                        <td width="70" align="right"><p><?php echo $row[csf("prod_id")]; ?></p></td>
                   </tr>
                <? $i++; } ?>
                	<tfoot>
                        <th colspan="4">Total</th>                         
                        <th><?php echo number_format($rettotalQnty,0,'',',')  //$total_order_qnty; ?></th> 
                        <th colspan="2"></th>
                        <th><?php echo number_format($totalAmount,0,'',','); ?></th>
                        <th ></th>
                   </tfoot>
            </tbody>
        </table>
    <?
	exit();
}

if($action=="child_form_input_data")
{
	
	$ex_data = explode(",",$data);
	$data2 = $ex_data[0]; 	// transaction id
	$rcvQnty = $ex_data[1];
	$total_cons_quantity= $ex_data[2];
	$mst_id= $ex_data[3];
	//echo $rcvQnty;
	
	 $company_id=return_field_value("company_id","inv_issue_master","id=$mst_id","company_id");
	// echo "select b.store_id from inv_issue_master a,inv_transaction b where a.id=$mst_id and a.id=b.mst_id and b.transaction_type=3";
	 $store_id=return_field_value("b.store_id","inv_issue_master a,inv_transaction b","a.id=$mst_id and a.id=b.mst_id and b.transaction_type=3 ","store_id");
	 $sql_lot = sql_select("select auto_transfer_rcv, id from variable_settings_inventory where company_name = $company_id and variable_list = 29 and is_deleted = 0 and status_active = 1");
	$variable_lot=$sql_lot[0][csf("auto_transfer_rcv")];
	$stock_qty_arr=fnc_store_wise_stock($company_id,$store_id,'','');

	$store_name=return_library_array("select id,store_name from lib_store_location", "id","store_name"); 
 	
	$item_name_arr=return_library_array("select id,item_name from lib_item_group", "id","item_name");
	/*$sql = "select c.is_posted_account, b.id as prod_id, b.item_description,b.item_group_id, a.id as tr_id, a.item_category, a.store_id,a.remarks, a.cons_uom, a.rcv_rate as cons_rate, a.cons_quantity, a.rcv_amount as cons_amount
			from inv_transaction a, product_details_master b, inv_receive_master c
 			where c.id=a.mst_id and a.id=$data2 and a.status_active=1 and transaction_type=3 and a.item_category in(5,6,7) and a.prod_id=b.id and b.status_active=1";*/
	
	$sql = "select c.is_posted_account, b.id as prod_id, b.item_description, b.item_group_id, a.id as tr_id, a.item_category, a.store_id, a.floor_id, a.room, a.rack ,a.self, a.bin_box, a.remarks, a.cons_uom, a.cons_rate, a.cons_quantity, a.cons_amount, a.batch_lot
	from inv_issue_master d, inv_transaction a, product_details_master b, inv_receive_master c
	where d.id=a.mst_id and a.prod_id=b.id and d.received_id=c.id and a.id=$data2 and a.status_active=1 and a.transaction_type=3";
	//echo $sql;die;
	$result = sql_select($sql);
	foreach($result as $row)
	{
		if($variable_lot) $dyes_lot=$row[csf('batch_lot')]; else $dyes_lot="";
		$stock_qty=$stock_qty_arr[$company_id][$store_id][$row[csf('item_category')]][$row[csf('prod_id')]][$dyes_lot]['stock']+$row[csf("cons_quantity")];
		
		//$rcvQnty=$rcvQnty+$row[csf("cons_quantity")];
		//$total_cons_quantity=$total_cons_quantity+$row[csf("cons_quantity")];
 		echo "$('#cbo_item_category').val(".$row[csf("item_category")].");\n";
		echo "$('#category').val(".$row[csf("item_category")].");\n";

		echo "load_room_rack_self_bin('requires/chemical_dyes_receive_return_controller*5_6_7_23', 'store','store_td', '".$company_id."','"."',this.value);\n";
		echo "$('#cbo_store_name').val('".$row[csf("store_id")]."');\n";

		echo "load_room_rack_self_bin('requires/chemical_dyes_receive_return_controller', 'floor','floor_td', '".$company_id."','"."','".$row[csf('store_id')]."',this.value);\n";
		echo "$('#cbo_floor').val('".$row[csf("floor_id")]."');\n";
		echo "load_room_rack_self_bin('requires/chemical_dyes_receive_return_controller', 'room','room_td', '".$company_id."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."',this.value);\n";
		echo "$('#cbo_room').val('".$row[csf("room")]."');\n";
		echo "load_room_rack_self_bin('requires/chemical_dyes_receive_return_controller', 'rack','rack_td', '".$company_id."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."',this.value);\n";
		echo "$('#txt_rack').val('".$row[csf("rack")]."');\n";
		echo "load_room_rack_self_bin('requires/chemical_dyes_receive_return_controller', 'shelf','shelf_td', '".$company_id."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."',this.value);\n";	
		echo "$('#txt_shelf').val('".$row[csf("self")]."');\n";
		echo "load_room_rack_self_bin('requires/chemical_dyes_receive_return_controller', 'bin','bin_td', '".$company_id."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."','".$row[csf('bin_box')]."',this.value);\n";	
		echo "$('#cbo_bin').val('".$row[csf("bin_box")]."');\n";

		//echo "$('#cbo_store_name').val('".$row[csf("store_id")]]."');\n";
		echo "$('#store').val('".$row[csf("store_id")]."');\n";
		echo "$('#txt_prod_id').val('".$row[csf("prod_id")]."');\n";
		echo "$('#before_prod_id').val('".$row[csf("prod_id")]."');\n";
		echo "$('#txt_item_group').val('".$item_name_arr[$row[csf("item_group_id")]]."');\n";
		echo "$('#txt_item_description').val('".$row[csf("item_description")]."');\n";
		echo "$('#txt_receive_qty').val('".$row[csf("cons_quantity")]."');\n";
		echo "$('#txt_remark').val('".$row[csf("remarks")]."');\n";	
		//$rcvQnty = $rcvQnty+$row[csf("cons_quantity")];
		//echo "$('#txt_curr_stock').val('".$rcvQnty."');\n";
		echo "$('#txt_curr_stock').val('".$stock_qty."');\n";
		
		echo "$('#txt_cons_quantity').val('".$total_cons_quantity."');\n";
		
		echo "$('#txt_uom').val('".$unit_of_measurement[$row[csf("cons_uom")]]."');\n";
		echo "$('#uom').val('".$row[csf("cons_uom")]."');\n";
		echo "$('#txt_return_rate').val('".$row[csf("cons_rate")]."');\n";		
		echo "$('#txt_return_value').val(".$row[csf("cons_amount")].");\n";
		echo "$('#update_id').val(".$row[csf("tr_id")].");\n";
		if($row[csf("is_posted_account")]==1)
		{
			echo "disable_enable_fields( 'cbo_item_category*cbo_store_name*txt_item_group*txt_item_description*txt_receive_qty*txt_return_rate', 1, '', '' );\n";
		}
		else
		{
		echo "disable_enable_fields( 'cbo_item_category*cbo_store_name*txt_item_group*txt_item_description*txt_receive_qty*txt_return_rate', 0, '', '' );\n";
		}

	}
	echo "$('#cbo_store_name').attr('disabled','disabled');\n";
	echo "$('#cbo_floor').attr('disabled','disabled');\n";
	echo "$('#cbo_room').attr('disabled','disabled');\n";
	echo "$('#txt_rack').attr('disabled','disabled');\n";
	echo "$('#txt_shelf').attr('disabled','disabled');\n";
	echo "$('#cbo_bin').attr('disabled','disabled');\n";
	echo "set_button_status(1, permission, 'fnc_dyes_receive_return_entry',1,1);\n";
	echo "$('#cbo_company_id').attr('disabled',true);\n";
	echo "$('#txt_mrr_no').attr('disabled',true);\n";
	//echo "$('#tbl_master').find('input,select').attr('disabled', false);\n";
	//echo "disable_enable_fields( 'cbo_company_id*txt_mrr_no*txt_mrr_retrun_no', 1, '', '');\n";
  	exit();
}

if ($action=="chemical_dyes_receive_return_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	
	$sql=" select id, issue_number, issue_date, received_id, challan_no, supplier_id from  inv_issue_master where id=$data[1] and company_id='$data[0]'";
	//echo $sql;die;
	$dataArray=sql_select($sql);

	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$rec_id_arr=return_library_array( "select id, recv_number from  inv_receive_master", "id", "recv_number"  );
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
            <td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo $data[2]; ?> Challan</u></strong></td>
        </tr>
        <tr>
        	<td width="120"><strong>Return ID:</strong></td><td width="175px"><? echo $dataArray[0][csf('issue_number')]; ?></td>
            <td width="100"><strong>Return Date :</strong></td> <td width="230px"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
            <td width="100"><strong>Receive ID:</strong></td><td width="175px"><? echo $rec_id_arr[$dataArray[0][csf('received_id')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Challan No:</strong></td> <td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td colspan=""><strong>Returned To:</strong></td><td width="230px"><? echo $supplier_library[$dataArray[0][csf('supplier_id')]]; ?></td>
            <td><strong>&nbsp;</strong></td><td width="175px"><? //echo $store_library[$dataArray[0][csf('store_id')]]; ?></td>
        </tr>
    </table>
         <br>
	<div style="width:100%;">
		<table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="40">SL</th>
                <th width="80" align="center">Item Category</th>
                <th width="150" align="center">Item Group</th>
                <th width="200" align="center">Item Description</th>
                <th width="50" align="center">UOM</th> 
                <th width="80" align="center">Returned. Qnty.</th>
                <th width="50" align="center">Rate</th>
                <th width="70" align="center">Return Value</th>
                <th width="80" align="center">Store</th> 
                <th width="80" align="center">Remarks</th> 
            </thead>
<?
	 $i=1;
	$item_name_arr=return_library_array( "select id, item_name from  lib_item_group", "id", "item_name"  );
	$sql_dtls= "select a.id, a.item_category,
	b.cons_uom, b.cons_quantity, b.rcv_rate as cons_rate, b.rcv_amount as cons_amount, b.store_id, b.remarks,
	c.item_group_id, c.item_description,(c.sub_group_name||' '||c.item_description||' '||c.item_size ) as product_name_details
	from  inv_issue_master a, inv_transaction b,  product_details_master c
	where a.id=$data[1] and a.company_id='$data[0]' and a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=3 and b.item_category in (5,6,7,23) and a.entry_form=28 ";
	//echo $sql_dtls;
	
	$sql_result=sql_select($sql_dtls);
	foreach($sql_result as $row)
	{
		if ($i%2==0)  
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";
			$cons_quantity=$row[csf('cons_quantity')];
			$cons_quantity_sum += $cons_quantity;
			
			$cons_amount=$row[csf('cons_amount')];
			$cons_amount_sum += $cons_amount;
			
			$desc=$row[csf('item_description')];
			
			if($row[csf('item_size')]!="")
			{
				$desc.=", ".$row[csf('item_size')];
			}
		?>
			<tr bgcolor="<? echo $bgcolor; ?>">
                <td><? echo $i; ?></td>
                <td><? echo $item_category[$row[csf('item_category')]]; ?></td>
                <td><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
                <td><? echo $row[csf('item_description')]; ?></td>
                <td align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
                <td align="right"><? echo $row[csf('cons_quantity')]; ?></td>
                <td align="right"><? echo $row[csf('cons_rate')]; ?></td>
                <td align="right"><? echo $row[csf('cons_amount')]; ?></td>
                <td><? echo $store_library[$row[csf('store_id')]]; ?></td>
                <td><? echo $row[csf('remarks')]; ?></td>
			</tr>
			<?php
			$i++;
			}
			?>
        	<tr> 
                <td align="right" colspan="5" >Total</td>
                <td align="right"><? echo number_format($cons_quantity_sum,0,'',','); ?></td>
                <td align="right" colspan="2" ><? echo $cons_amount_sum; ?></td>
                <td align="right">&nbsp;</td><td align="right">&nbsp;</td>
			</tr>
		</table>
        <br>
		 <?
            echo signature_table(10, $data[0], "900px");
         ?>
      </div>
   </div> 
    <?
	exit();
}

function fnc_store_wise_qty_operation($company_id,$store_id,$category,$prod_id,$trans_type,$dyes_lot)
{
	
	$trans_type=str_replace("'","",$trans_type);
	$prod_id=str_replace("'","",$prod_id);
	$store_id=str_replace("'","",$store_id);
	$category=str_replace("'","",$category);
	$company_id=str_replace("'","",$company_id);
	if($trans_type==2) //Issue
	{
		$prod_ids=rtrim($prod_id,",");
		$prod_ids=array_chunk(array_unique(explode(",",$prod_ids)),1000, true);
		$prod_cond="";
		$ji=0;
		foreach($prod_ids as $key=> $value)
		{
			if($ji==0)
			{
				$prod_cond=" and prod_id  in(".implode(",",$value).")"; 
			}
			else
			{
				$prod_cond.=" or prod_id  in(".implode(",",$value).")";
			}
			$ji++;
		}
		 $category_ids=rtrim($category,",");
		$cat_ids=array_chunk(array_unique(explode(",",$category_ids)),1000, true);
		 $cat_cond="";
		   $k=0;
		   foreach($cat_ids as $key=> $value)
		   {
			   if($k==0)
			   {
				$cat_cond=" and category_id  in(".implode(",",$value).")"; 
				
			   }
			   else
			   {
				$cat_cond.=" or category_id  in(".implode(",",$value).")";
				
			   }
			   $k++;
		   }
	}
	
	if($trans_type==2) //Issue
	{
	 	$sql_data=sql_select("select id, company_id, category_id, prod_id, cons_qty, rate, amount
		from inv_store_wise_qty_dtls where company_id=$company_id and status_active=1 and is_deleted=0 $prod_cond $cat_cond");
	}
	else if($trans_type==1 || $trans_type==3 || $trans_type==4) //Recv & Issue Return
	{
		$lot_cond="";
		if($dyes_lot!="")  $lot_cond=" and lot='$dyes_lot'";
		$sql_data=sql_select("select id, company_id, category_id, store_id, prod_id, cons_qty, rate, amount, lot 
		from inv_store_wise_qty_dtls where company_id=$company_id and store_id=$store_id and category_id in($category) and status_active=1 and is_deleted=0 and prod_id=$prod_id $lot_cond");
	}
	
	$stock_prod_arr=array();
	if($trans_type==2) //Issue
	{
		$updated_store_ids=''; $updated_ids='';$prod_arr=array();
		foreach($sql_data as $row)
		{
			if($updated_store_ids=='') $updated_store_ids=$row[csf("id")];else $updated_store_ids.=",".$row[csf("id")];
		}
		$stock_prod_arr=$updated_store_ids;//.'**'.$stock_prod_arr;
	}
	else if($trans_type==1 || $trans_type==3 || $trans_type==4) //recv & Issue Return
	{
		foreach($sql_data as $row)
		{
			$stock_prod_arr[$row[csf('company_id')]][$row[csf('prod_id')]][$row[csf('store_id')]][$row[csf('category_id')]][$row[csf('lot')]]=$row[csf('id')];
		}
	}
	 return $stock_prod_arr;
} //Function End
//Store Wise Stock Function
function fnc_store_wise_stock($company_id,$store_id,$category,$prod_id)
{
	 $result=sql_select("select category_id, prod_id, cons_qty, lot 
	 from  inv_store_wise_qty_dtls where  company_id=$company_id and store_id=$store_id and status_active=1 and is_deleted=0 ");
	$stock_qty_arr=array();
	 foreach($result as $row)
	 {
		 $stock_qty_arr[$company_id][$store_id][$row[csf('category_id')]][$row[csf('prod_id')]][$row[csf('lot')]]['stock']=$row[csf('cons_qty')]; 
	 }
	 return $stock_qty_arr;
}

?>
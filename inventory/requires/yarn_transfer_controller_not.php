<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");

if ($action=="itemDescription_popup")
{
	echo load_html_head_contents("Item Description Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
?> 

	<script>
		
		/*$(document).ready(function(e) {
            setFilterGrid('tbl_list_search',-1);
        });*/
		
		function js_set_value(data)
		{
			$('#product_id').val(data);
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
<div align="center" style="width:920px;">
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:910px;margin-left:10px">
        <legend>Enter search words</legend>
            <table cellpadding="0" cellspacing="0" width="600" class="rpt_table">
                <thead>
                    <th>Search By</th>
                    <th width="280" id="search_by_td_up">Please Enter Item Details</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
                        <input type="hidden" name="product_id" id="product_id" class="text_boxes" value="">
                    </th>
                </thead>
                <tr class="general">
                    <td>
						<?
							$search_by_arr=array(1=>"Item Details",2=>"Lot No.");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>
                    <td id="search_by_td">
                        <input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                    </td>
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>, 'create_product_search_list_view', 'search_div', 'yarn_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                    </td>
                </tr>
            </table>
        	<div style="margin-top:10px" id="search_div"></div> 
		</fieldset>
	</form>
</div>    
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=='create_product_search_list_view')
{
	$data = explode("_",$data);
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$company_id =$data[2];
	
	if($search_by==1)
		$search_field="product_name_details";	
	else
		$search_field="lot";	
	
 	$sql="select id, company_id, supplier_id, product_name_details, lot, current_stock, brand from product_details_master where item_category_id=1 and company_id=$company_id and $search_field like '$search_string' and current_stock>0 and status_active=1 and is_deleted=0";
	
	$arr=array(1=>$company_arr,2=>$supplier_arr,5=>$brand_arr);

	echo  create_list_view("tbl_list_search", "Item ID,Company,Supplier,Item Details,Lot No,Brand,Stock", "80,120,120,180,90,100","900","250",0, $sql, "js_set_value", "id", "", 1, "0,company_id,supplier_id,0,0,brand,0", $arr, "id,company_id,supplier_id,product_name_details,lot,brand,current_stock", '','','0,0,0,0,0,0,2');
	
	exit();
}

if($action=='populate_data_from_product_master')
{
	
	$data_array=sql_select("select product_name_details, lot, current_stock, avg_rate_per_unit, brand from product_details_master where id='$data'");
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('hidden_product_id').value 			= '".$data."';\n";
		echo "document.getElementById('txt_item_desc').value 				= '".$row[csf("product_name_details")]."';\n";
		echo "document.getElementById('txt_yarn_lot').value 				= '".$row[csf("lot")]."';\n";
		echo "document.getElementById('txt_current_stock').value 			= '".$row[csf("current_stock")]."';\n";
		echo "document.getElementById('hidden_current_stock').value 		= '".$row[csf("current_stock")]."';\n";
		echo "document.getElementById('txt_rate').value 					= '".$row[csf("avg_rate_per_unit")]."';\n";
		echo "document.getElementById('hide_brand_id').value 				= '".$row[csf("brand")]."';\n";
		echo "document.getElementById('txt_yarn_brand').value 				= '".$brand_arr[$row[csf("brand")]]."';\n";

		exit();
	}
}

if ($action=="itemTransfer_popup")
{
	echo load_html_head_contents("Item Transfer Info", "../../", 1, 1,'','','');
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
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>
                    <td id="search_by_td">
                        <input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                    </td>
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>, 'create_transfer_search_list_view', 'search_div', 'yarn_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                    </td>
                </tr>
            </table>
        	<div style="margin-top:10px" id="search_div"></div> 
		</fieldset>
	</form>
</div>    
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
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
	
 	$sql="select id, transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria, item_category from inv_item_transfer_mst where item_category=1 and company_id=$company_id and $search_field like '$search_string' and transfer_criteria in(1,2) and status_active=1 and is_deleted=0";
	
	$arr=array(2=>$company_arr,4=>$item_transfer_criteria,5=>$item_category);

	echo  create_list_view("tbl_list_search", "Transfer ID,Challan No,Company,Transfer Date,Transfer Criteria,Item Category", "120,100,120,90,140","760","250",0, $sql, "js_set_value", "id", "", 1, "0,0,company_id,0,transfer_criteria,item_category", $arr, "transfer_system_id,challan_no,company_id,transfer_date,transfer_criteria,item_category", '','','0,0,0,3,0,0');
	
	exit();
}

if($action=='populate_data_from_transfer_master')
{
	
	$data_array=sql_select("select transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria, item_category, to_company from inv_item_transfer_mst where id='$data'");
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('update_id').value 					= '".$data."';\n";
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("transfer_system_id")]."';\n";
		echo "document.getElementById('cbo_transfer_criteria').value 		= '".$row[csf("transfer_criteria")]."';\n";
		
		echo "active_inactive(".$row[csf("transfer_criteria")].");\n";
		
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_transfer_date').value 			= '".change_date_format($row[csf("transfer_date")])."';\n";
		echo "document.getElementById('cbo_to_company_id').value 			= '".$row[csf("to_company")]."';\n";
		echo "document.getElementById('cbo_item_category').value 			= '".$row[csf("item_category")]."';\n";
		
		echo "$('#cbo_company_id').attr('disabled','disabled');\n";
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_yarn_transfer_entry',1);\n"; 
		
		exit();
	}
}

if($action=="show_transfer_listview")
{
	$product_arr = return_library_array("select id, product_name_details from product_details_master","id","product_name_details");
	
	$sql="select id, from_store, to_store, from_prod_id, transfer_qnty, yarn_lot, brand_id from inv_item_transfer_dtls where mst_id='$data' and status_active = '1' and is_deleted = '0'";
	
	$arr=array(0=>$store_arr,1=>$store_arr,2=>$product_arr,5=>$brand_arr);
	 
	echo  create_list_view("list_view", "From Store,To Store,Item Description,Yarn Lot,Transfered Qnty,Yarn Brand", "130,130,220,100,110","880","200",0, $sql, "get_php_form_data", "id", "'populate_transfer_details_form_data'", 0, "from_store,to_store,from_prod_id,0,0,brand_id", $arr, "from_store,to_store,from_prod_id,yarn_lot,transfer_qnty,brand_id", "requires/yarn_transfer_controller",'','0,0,0,0,2,0');
	
	exit();
}

if($action=='populate_transfer_details_form_data')
{
	
	$data_array=sql_select("select id, from_store, to_store, from_prod_id, to_prod_id, transfer_qnty, rate, transfer_value, yarn_lot, brand_id from inv_item_transfer_dtls where id='$data'");
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('update_dtls_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_from_store').value 				= '".$row[csf("from_store")]."';\n";
		echo "document.getElementById('cbo_to_store').value 				= '".$row[csf("to_store")]."';\n";
		echo "document.getElementById('txt_yarn_lot').value 				= '".$row[csf("yarn_lot")]."';\n";
		echo "document.getElementById('txt_transfer_qnty').value 			= '".$row[csf("transfer_qnty")]."';\n";
		echo "document.getElementById('txt_rate').value 					= '".$row[csf("rate")]."';\n";
		echo "document.getElementById('txt_transfer_value').value 			= '".$row[csf("transfer_value")]."';\n";
		echo "document.getElementById('hide_brand_id').value 				= '".$row[csf("brand_id")]."';\n";
		echo "document.getElementById('txt_yarn_brand').value 				= '".$brand_arr[$row[csf("brand_id")]]."';\n";
		echo "document.getElementById('hidden_transfer_qnty').value 		= '".$row[csf("transfer_qnty")]."';\n";
		echo "document.getElementById('hidden_product_id').value 			= '".$row[csf("from_prod_id")]."';\n";
		echo "document.getElementById('previous_from_prod_id').value 		= '".$row[csf("from_prod_id")]."';\n";
		echo "document.getElementById('previous_to_prod_id').value 			= '".$row[csf("to_prod_id")]."';\n";
		
		$sql=sql_select("select product_name_details,current_stock,avg_rate_per_unit from product_details_master where id=$row[from_prod_id]");
		
		$stock=$sql[0][csf("current_stock")]+$row[csf("transfer_qnty")];
		
		echo "document.getElementById('txt_item_desc').value 				= '".$sql[0][csf("product_name_details")]."';\n";
		echo "document.getElementById('txt_current_stock').value 			= '".$sql[0][csf("current_stock")]."';\n";
		echo "document.getElementById('hidden_current_stock').value 		= '".$stock."';\n";
		
		$sql_trans=sql_select("select id, transaction_type from inv_transaction where mst_id=$data and item_category=1 and transaction_type in(5,6)");
		/*foreach()
		{
			
		}*/
		echo "document.getElementById('update_trans_issue_id').value 		= '".$sql_trans[0][csf("id")]."';\n";
		echo "document.getElementById('update_trans_recv_id').value 		= '".$sql_trans[1][csf("id")]."';\n";
		
		
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_yarn_transfer_entry',1);\n"; 
		
		exit();
	}
}

//data save update delete here------------------------------//
if($action=="save_update_delete")
{	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$transfer_recv_num=''; $transfer_update_id='';
		
		if(str_replace("'","",$update_id)=="")
		{
			$new_transfer_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'YTE', date("Y",time()), 5, "select transfer_prefix, transfer_prefix_number from inv_item_transfer_mst where company_id=$cbo_company_id and transfer_criteria in(1,2) and item_category='1' and YEAR(insert_date)=".date('Y',time())." order by transfer_prefix_number desc ", "transfer_prefix", "transfer_prefix_number" ));
		 	
			$id=return_next_id( "id", "inv_item_transfer_mst", 1 ) ;
					 
			$field_array="id, transfer_prefix, transfer_prefix_number, transfer_system_id, company_id, challan_no, transfer_date, transfer_criteria, to_company, from_order_id, to_order_id, item_category, inserted_by, insert_date";
			
			$data_array="(".$id.",'".$new_transfer_system_id[1]."',".$new_transfer_system_id[2].",'".$new_transfer_system_id[0]."',".$cbo_company_id.",".$txt_challan_no.",".$txt_transfer_date.",".$cbo_transfer_criteria.",".$cbo_to_company_id.",0,0,".$cbo_item_category.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			//echo "insert into inv_item_transfer_mst (".$field_array.") values ".$data_array;die;
			$rID=sql_insert("inv_item_transfer_mst",$field_array,$data_array,0);
			
			if($rID) $flag=1; else $flag=0;
			
			$transfer_recv_num=$new_transfer_system_id[0];
			$transfer_update_id=$id;
		}
		else
		{
			$field_array_update="challan_no*transfer_date*to_company*updated_by*update_date";
			
			$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$cbo_to_company_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			
			if($rID) $flag=1; else $flag=0; 
			
			$transfer_recv_num=str_replace("'","",$txt_system_id);
			$transfer_update_id=str_replace("'","",$update_id);
		}
		
		$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
		
		$field_array_trans="id, mst_id, company_id, prod_id, item_category, transaction_type, transaction_date, store_id, brand_id, cons_uom, cons_quantity, cons_rate, cons_amount, balance_qnty, balance_amount, inserted_by, insert_date";
		
		$id_dtls=return_next_id( "id", "inv_item_transfer_dtls", 1 ) ;
		
		$field_array_dtls="id, mst_id, from_prod_id, to_prod_id, yarn_lot, brand_id, item_group, from_store, to_store, item_category, transfer_qnty, rate, transfer_value, uom, inserted_by, insert_date";
		
		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			$data_prod=sql_select("select supplier_id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id=$hidden_product_id");
			
			$presentStock=$data_prod[0][csf('current_stock')]-str_replace("'","",$txt_transfer_qnty);
			$presentAvgRate=$data_prod[0][csf('avg_rate_per_unit')];
			$presentStockValue=$presentStock*$presentAvgRate;
			
			$field_array_prodUpdate="avg_rate_per_unit*last_issued_qnty*current_stock*stock_value*updated_by*update_date";
			$data_array_prodUpdate=$presentAvgRate."*".$txt_transfer_qnty."*".$presentStock."*".$presentStockValue."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$prodUpdate=sql_update("product_details_master",$field_array_prodUpdate,$data_array_prodUpdate,"id",$hidden_product_id,1);
			
			if($flag==1) 
			{
				if($prodUpdate) $flag=1; else $flag=0; 
			} 
			
			$supplier_id=$data_prod[0][csf('supplier_id')];
				
			$row_prod=sql_select("select id, current_stock, avg_rate_per_unit from product_details_master where company_id=$cbo_to_company_id and item_category_id=1 and supplier_id='$supplier_id' and product_name_details=$txt_item_desc and lot=$txt_yarn_lot and status_active=1 and is_deleted=0");
		
			if(count($row_prod)>0)
			{
				$product_id=$row_prod[0][csf('id')];
				$stock_qnty=$row_prod[0][csf('current_stock')];
				$avg_rate_per_unit=$row_prod[0][csf('avg_rate_per_unit')];
	
				$curr_stock_qnty=$stock_qnty+str_replace("'", '',$txt_transfer_qnty);
				$stock_value=$curr_stock_qnty*$avg_rate_per_unit;
	
				$field_array_prod_update="avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
				
				$data_array_prod_update=$avg_rate_per_unit."*".$txt_transfer_qnty."*".$curr_stock_qnty."*".$stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				
				$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
				if($flag==1) 
				{
					if($prod) $flag=1; else $flag=0; 
				} 
			}
			else
			{
				$product_id=return_next_id( "id", "product_details_master", 1 ) ;
				$curr_stock_qnty=str_replace("'","",$txt_transfer_qnty);
				$avg_rate_per_unit=$data_prod[0][csf('avg_rate_per_unit')];
				$stock_value=$curr_stock_qnty*$avg_rate_per_unit;
				
				$sql_prod_insert="insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, inserted_by, insert_date) 
			select	
			'$product_id', $cbo_company_id, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, $txt_transfer_qnty, $curr_stock_qnty, $stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, inserted_by, insert_date from product_details_master where id=$hidden_product_id";
				
				$prod=execute_query($sql_prod_insert,0);
				if($flag==1) 
				{
					if($prod) $flag=1; else $flag=0; 
				} 
			}
			
			$data_array_trans="(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",".$hidden_product_id.",".$cbo_item_category.",6,".$txt_transfer_date.",".$cbo_from_store.",".$hide_brand_id.",".$cbo_uom.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$recv_trans_id=$id_trans+1;
			$data_array_trans.=",(".$recv_trans_id.",".$transfer_update_id.",".$cbo_company_id.",".$product_id.",".$cbo_item_category.",5,".$txt_transfer_date.",".$cbo_to_store.",".$hide_brand_id.",".$cbo_uom.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",".$txt_transfer_qnty.",".$txt_transfer_value.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$data_array_dtls="(".$id_dtls.",".$transfer_update_id.",".$hidden_product_id.",".$product_id.",".$txt_yarn_lot.",".$hide_brand_id.",0,".$cbo_from_store.",".$cbo_to_store.",".$cbo_item_category.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			//LIFO/FIFO Start-----------------------------------------------//
			$isLIFOfifo=return_field_value("store_method","variable_settings_inventory","company_name=$cbo_company_id and variable_list=17");
			if($isLIFOfifo==2) $cond_lifofifo=" DESC"; else $cond_lifofifo=" ASC";
			
			$transfer_qnty = str_replace("'","",$txt_transfer_qnty);
			$transfer_value = str_replace("'","",$txt_transfer_value);
			$field_array_mrr= "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
			$update_array = "balance_qnty*balance_amount*updated_by*update_date";
			$updateID_array=array();
			$update_data=array();
			$mrrWiseIsID = return_next_id("id", "inv_mrr_wise_issue_details", 1);
			
			$sql = sql_select("select id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id=$hidden_product_id and balance_qnty>0 and transaction_type in (1,4,5) and item_category=1 order by transaction_date $cond_lifofifo");
			foreach($sql as $result)
			{				
				$recv_trans_id = $result[csf("id")]; // this row will be updated
				$balance_qnty = $result[csf("balance_qnty")];
				$balance_amount = $result[csf("balance_amount")];
				$cons_rate = $result[csf("cons_rate")];
				$transferQntyBalance = $balance_qnty-$transfer_qnty; // minus issue qnty
				$transferStockBalance = $balance_amount-($transfer_qnty*$cons_rate);
				if($transferQntyBalance>=0)
				{					
					$amount = $transfer_qnty*$cons_rate;
					//for insert
					if($data_array_mrr!="") $data_array_mrr .= ",";  
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$id_trans.",10,".$hidden_product_id.",".$transfer_qnty.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
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
					if($data_array_mrr!="") $data_array_mrr .= ",";  
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$id_trans.",10,".$hidden_product_id.",".$balance_qnty.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
					//for update
					$updateID_array[]=$recv_trans_id; 
					$update_data[$recv_trans_id]=explode("*",("0*0*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					$transfer_qnty = $transferQntyBalance;
				}
				$mrrWiseIsID++;
			}//end foreach
			
			if($data_array_mrr!="")
			{		
				$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,1);
				if($flag==1) 
				{
					if($mrrWiseIssueID) $flag=1; else $flag=0; 
				} 
			}
			
			//transaction table stock update here------------------------//
			if(count($updateID_array)>0)
			{
				$upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array));
				if($flag==1) 
				{
					if($upTrID) $flag=1; else $flag=0; 
				} 
			}	
 		// LIFO/FIFO END-----------------------------------------------//
		}
		else
		{
			$data_array_trans="(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",".$hidden_product_id.",".$cbo_item_category.",6,".$txt_transfer_date.",".$cbo_from_store.",".$hide_brand_id.",".$cbo_uom.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$id_trans=$id_trans+1;
			$data_array_trans.=",(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",".$hidden_product_id.",".$cbo_item_category.",5,".$txt_transfer_date.",".$cbo_to_store.",".$hide_brand_id.",".$cbo_uom.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$data_array_dtls="(".$id_dtls.",".$transfer_update_id.",".$hidden_product_id.",".$hidden_product_id.",".$txt_yarn_lot.",".$hide_brand_id.",0,".$cbo_from_store.",".$cbo_to_store.",".$cbo_item_category.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
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
		
		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				echo "0**".$transfer_update_id."**".$transfer_recv_num."**0";
			}
			else
			{
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

		$field_array_update="challan_no*transfer_date*to_company*updated_by*update_date";
		$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$cbo_to_company_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		
		if($rID) $flag=1; else $flag=0; 
		
		$field_array_trans="prod_id*transaction_date*store_id*brand_id*cons_uom*cons_quantity*cons_rate*cons_amount*balance_qnty*balance_amount*updated_by*update_date";
		$updateTransID_array=array();
		$update_trans_issue_id=str_replace("'","",$update_trans_issue_id); 
		$update_trans_recv_id=str_replace("'","",$update_trans_recv_id); 

		$field_array_dtls="from_prod_id*to_prod_id*yarn_lot*brand_id*from_store*to_store*transfer_qnty*rate*transfer_value*uom*updated_by*update_date";
		
		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			$updateProdID_array=array();
			$field_array_adjust="current_stock*avg_rate_per_unit*stock_value";
			
			$stock_from=sql_select("select current_stock, avg_rate_per_unit from product_details_master where id=$previous_from_prod_id");
			$adjust_curr_stock_from=$stock_from[0][csf('current_stock')]-str_replace("'", '',$hidden_transfer_qnty);
			$cur_st_rate_from=$stock_from[0][csf('avg_rate_per_unit')];
			$cur_st_value_from=$adjust_curr_stock_from*$cur_st_rate_from;
			
			$previous_from_prod_id=str_replace("'","",$previous_from_prod_id);
			$updateProdID_array[]=$previous_from_prod_id; 
			
			$data_array_adjust[$previous_from_prod_id]=explode("*",("".$adjust_curr_stock_from."*".$cur_st_rate_from."*".$cur_st_value_from));
			
			$stock_to=sql_select("select current_stock, avg_rate_per_unit from product_details_master where id=$previous_to_prod_id");
			$adjust_curr_stock_to=$stock_to[0][csf('current_stock')]+str_replace("'", '',$hidden_transfer_qnty);
			$cur_st_rate_to=$stock_to[0][csf('avg_rate_per_unit')];
			$cur_st_value_to=$adjust_curr_stock_to*$cur_st_rate_to;
			
			$previous_to_prod_id=str_replace("'","",$previous_to_prod_id);
			$updateProdID_array[]=$previous_to_prod_id; 
			
			$data_array_adjust[$previous_to_prod_id]=explode("*",("".$adjust_curr_stock_to."*".$cur_st_rate_to."*".$cur_st_value_to));
			
			$prodUpdate_adjust=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_adjust,$data_array_adjust,$updateProdID_array));
			if($flag==1) 
			{
				if($prodUpdate_adjust) $flag=1; else $flag=0; 
			}
			
			$data_prod=sql_select("select supplier_id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id=$hidden_product_id");
			
			$presentStock=$data_prod[0][csf('current_stock')]-str_replace("'","",$txt_transfer_qnty);
			$presentAvgRate=$data_prod[0][csf('avg_rate_per_unit')];
			$presentStockValue=$presentStock*$presentAvgRate;
			
			$field_array_prodUpdate="avg_rate_per_unit*last_issued_qnty*current_stock*stock_value*updated_by*update_date";
			$data_array_prodUpdate=$presentAvgRate."*".$txt_transfer_qnty."*".$presentStock."*".$presentStockValue."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$prodUpdate=sql_update("product_details_master",$field_array_prodUpdate,$data_array_prodUpdate,"id",$hidden_product_id,1);
			
			if($flag==1) 
			{
				if($prodUpdate) $flag=1; else $flag=0; 
			} 
			
			$supplier_id=$data_prod[0][csf('supplier_id')];
				
			$row_prod=sql_select("select id, current_stock, avg_rate_per_unit from product_details_master where company_id=$cbo_to_company_id and item_category_id=1 and supplier_id='$supplier_id' and product_name_details=$txt_item_desc and lot=$txt_yarn_lot and status_active=1 and is_deleted=0");
		
			if(count($row_prod)>0)
			{
				$product_id=$row_prod[0][csf('id')];
				$stock_qnty=$row_prod[0][csf('current_stock')];
				$avg_rate_per_unit=$row_prod[0][csf('avg_rate_per_unit')];
	
				$curr_stock_qnty=$stock_qnty+str_replace("'", '',$txt_transfer_qnty);
				$stock_value=$curr_stock_qnty*$avg_rate_per_unit;
	
				$field_array_prod_update="avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
				
				$data_array_prod_update=$avg_rate_per_unit."*".$txt_transfer_qnty."*".$curr_stock_qnty."*".$stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
				if($flag==1) 
				{
					if($prod) $flag=1; else $flag=0; 
				} 
			}
			else
			{
				$product_id=return_next_id( "id", "product_details_master", 1 ) ;
				$curr_stock_qnty=str_replace("'","",$txt_transfer_qnty);
				$avg_rate_per_unit=$data_prod[0][csf('avg_rate_per_unit')];
				$stock_value=$curr_stock_qnty*$avg_rate_per_unit;
				
				$sql_prod_insert="insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, inserted_by, insert_date) 
			select	
			'$product_id', $cbo_company_id, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, $txt_transfer_qnty, $curr_stock_qnty, $stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, inserted_by, insert_date from product_details_master where id=$hidden_product_id";
				
				$prod=execute_query($sql_prod_insert,0);
				if($flag==1) 
				{
					if($prod) $flag=1; else $flag=0; 
				} 
			}
			
			$updateTransID_array[]=$update_trans_issue_id; 
			
			$updateTransID_data[$update_trans_issue_id]=explode("*",("".$hidden_product_id."*".$txt_transfer_date."*".$cbo_from_store."*".$hide_brand_id."*".$cbo_uom."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*0*0*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			
			$updateTransID_array[]=$update_trans_recv_id; 
			
			$updateTransID_data[$update_trans_recv_id]=explode("*",("".$product_id."*".$txt_transfer_date."*".$cbo_from_store."*".$hide_brand_id."*".$cbo_uom."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*".$txt_transfer_qnty."*".$txt_transfer_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			
			$data_array_dtls=$hidden_product_id."*".$product_id."*".$txt_yarn_lot."*".$hide_brand_id."*".$cbo_from_store."*".$cbo_to_store."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*".$cbo_uom."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			//transaction table START--------------------------//
			$update_array = "balance_qnty*balance_amount*updated_by*update_date";
			$sql = sql_select("select a.id,a.balance_qnty,a.balance_amount,b.issue_qnty,b.rate,b.amount from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id=$update_trans_issue_id and b.entry_form=10 and a.item_category=1"); 
			$updateID_array = array();
			$update_data = array();
			foreach($sql as $result)
			{
				$adjBalance = $result[csf("balance_qnty")]+$result[csf("issue_qnty")];
				$adjAmount = $result[csf("balance_amount")]+$result[csf("amount")];
				$updateID_array[]=$result[csf("id")]; 
				$update_data[$result[csf("id")]]=explode("*",("".$adjBalance."*".$adjAmount."*'".$user_id."'*'".$pc_date_time."'"));
			}

			if(count($updateID_array)>0)
			{
				$query=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array));
				if($flag==1) 
				{
					if($query) $flag=1; else $flag=0; 
				} 
				
				$query2 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id=$update_trans_issue_id and entry_form=10");
				{
					if($query2) $flag=1; else $flag=0; 
				} 
			}
			
			//transaction table END----------------------------//
			
			//LIFO/FIFO Start-----------------------------------------------//
			$isLIFOfifo=return_field_value("store_method","variable_settings_inventory","company_name=$cbo_company_id and variable_list=17");
			if($isLIFOfifo==2) $cond_lifofifo=" DESC"; else $cond_lifofifo=" ASC";
			
			$transfer_qnty = str_replace("'","",$txt_transfer_qnty);
			$transfer_value = str_replace("'","",$txt_transfer_value);
			$field_array_mrr= "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
			$update_array = "balance_qnty*balance_amount*updated_by*update_date";
			$updateID_array=array();
			$update_data=array();
			$mrrWiseIsID = return_next_id("id", "inv_mrr_wise_issue_details", 1);
			
			$sql = sql_select("select id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id=$hidden_product_id and balance_qnty>0 and transaction_type in (1,4,5) and item_category=1 order by transaction_date $cond_lifofifo");
			foreach($sql as $result)
			{				
				$recv_trans_id = $result[csf("id")]; // this row will be updated
				$balance_qnty = $result[csf("balance_qnty")];
				$balance_amount = $result[csf("balance_amount")];
				$cons_rate = $result[csf("cons_rate")];
				$transferQntyBalance = $balance_qnty-$transfer_qnty; // minus issue qnty
				$transferStockBalance = $balance_amount-($transfer_qnty*$cons_rate);
				if($transferQntyBalance>=0)
				{					
					$amount = $transfer_qnty*$cons_rate;
					//for insert
					if($data_array_mrr!="") $data_array_mrr .= ",";  
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$update_trans_issue_id.",10,".$hidden_product_id.",".$transfer_qnty.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
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
					if($data_array_mrr!="") $data_array_mrr .= ",";  
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$update_trans_issue_id.",10,".$hidden_product_id.",".$balance_qnty.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
					//for update
					$updateID_array[]=$recv_trans_id; 
					$update_data[$recv_trans_id]=explode("*",("0*0*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					$transfer_qnty = $transferQntyBalance;
				}
				$mrrWiseIsID++;
			}//end foreach
			
			if($data_array_mrr!="")
			{		
				$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,1);
				if($flag==1) 
				{
					if($mrrWiseIssueID) $flag=1; else $flag=0; 
				} 
			}
			
			//transaction table stock update here------------------------//
			if(count($updateID_array)>0)
			{
				$upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array));
				if($flag==1) 
				{
					if($upTrID) $flag=1; else $flag=0; 
				} 
			}	
 		// LIFO/FIFO END-----------------------------------------------//
		}
		else
		{
			$updateTransID_array[]=$update_trans_issue_id; 
			
			$updateTransID_data[$update_trans_issue_id]=explode("*",("".$hidden_product_id."*".$txt_transfer_date."*".$cbo_from_store."*".$hide_brand_id."*".$cbo_uom."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*0*0*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			
			$updateTransID_array[]=$update_trans_recv_id; 
			
			$updateTransID_data[$update_trans_recv_id]=explode("*",("".$hidden_product_id."*".$txt_transfer_date."*".$cbo_from_store."*".$hide_brand_id."*".$cbo_uom."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*0*0*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			
			$data_array_dtls=$hidden_product_id."*".$hidden_product_id."*".$txt_yarn_lot."*".$hide_brand_id."*".$cbo_from_store."*".$cbo_to_store."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*".$cbo_uom."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		}
		
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
		
		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id)."**0";
			}
			else
			{
				echo "6**0**"."&nbsp;"."**1";
			}
		}	

		disconnect($con);
		die;
 	}
		
}

?>

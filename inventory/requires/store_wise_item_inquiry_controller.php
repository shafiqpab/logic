<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if($action=="populate_data_lib_data")
{
	$sql = sql_select("select auto_transfer_rcv, id from variable_settings_inventory where company_name = $data and variable_list = 29 and is_deleted = 0 and status_active = 1");
	echo "$('#variable_lot').val('".$sql[0][csf("auto_transfer_rcv")]."');\n";
	exit();
}

if($action=="item_desc_popup")
{
	echo load_html_head_contents("Item Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
	
		function js_set_value(data)
		{
			$('#hide_data').val(data);
			parent.emailwindow.hide();
		}

    </script>
	<input type="hidden" id="hide_data" name="hide_data" />
	<?

	$sql= "select id, product_name_details, lot from product_details_master where status_active=1 and is_deleted=0 and company_id=$companyID and item_category_id=$item_category_id order by id";
	
	//echo $sql;die;
	
	if($item_category_id==1)
	{
		echo create_list_view("tbl_list_search", "Product Id,Lot No,Product Details", "80,120","470","260",0, $sql , "js_set_value", "id", "", 1, "0,0,0", $arr , "id,lot,product_name_details", "",'','0,0,0','',0) ;
	}
	else
	{
		echo create_list_view("tbl_list_search", "Product Id,Product Details", "80","470","300",0, $sql , "js_set_value", "id", "", 1, "0,0", $arr , "id,product_name_details", "",'','0,0','',0) ;

	}
	
	?>
    <script>setFilterGrid('tbl_list_search',-1);</script>
	
    <?
		
    exit(); 
}


if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_name=str_replace("'","",$cbo_company_id);
	$item_category_id=str_replace("'","",$cbo_item_category_id);
	$variable_lot=str_replace("'","",$variable_lot);
	//echo $variable_lot;die;
	
	$store_arr=return_library_array( "select id, store_name from  lib_store_location",'id','store_name');
	
	
	//echo $sql;
	if($variable_lot==1)
	{
		$sql="select a.prod_id, a.store_id, a.batch_lot,
		sum((case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end)-(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)) as balamce_qty
		from inv_transaction a where a.item_category=$item_category_id and a.status_active=1 and a.is_deleted=0 and a.prod_id=$txt_product_id
		group by a.prod_id, a.store_id, a.batch_lot";
	}
	else
	{
		$sql="select a.prod_id, a.store_id, null as batch_lot,
		sum((case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end)-(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)) as balamce_qty
		from inv_transaction a where a.item_category=$item_category_id and a.status_active=1 and a.is_deleted=0 and a.prod_id=$txt_product_id
		group by a.prod_id, a.store_id";
	}
	//echo $sql;
	$dataArray=sql_select($sql);
	$productData=sql_select("select product_name_details, current_stock, supplier_id from product_details_master where id=$txt_product_id");
	
	$sql_store="select store_id, prod_id, lot, cons_qty from inv_store_wise_qty_dtls a where a.category_id=$item_category_id and a.status_active=1 and a.is_deleted=0 and a.prod_id=$txt_product_id";
	$storeData = sql_select($sql_store);
	$store_data_arr=array();
	foreach($storeData as $row)
	{
		$store_data_arr[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("lot")]]+=$row[csf("cons_qty")];
	}
	
	?>
    <div align="center" style="width:100%">
    <form name="storeItemInquiry_2" id="storeItemInquiry_2">
        <fieldset style="width:550px;">
        	<table width="550" style="margin-bottom:10px">
            	<tr>
                	<td width="250"><b>Item Description:</b> <? echo $productData[0][csf('product_name_details')]; ?>;</td>
                    <td width="150"><b>Global Stock:</b><? echo number_format($productData[0][csf('current_stock')],2); ?></td>
                    <td><input type="button" name="search" id="search" value="Click For Synchronize" onClick="synchronize_stock(<? echo $txt_product_id; ?>)" style="width:140px" class="formbutton" /></td>
                </tr>
            </table>
            <b>Product ID Level Transaction (Product ID : <? echo str_replace("'","",$txt_product_id); ?>)</b>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="550" class="rpt_table" style="margin-bottom:10px">
                <thead>
                    <th width="200">Store Name</th>
                    <th width="100">Item Lot</th>
                    <th width="100">Balance On Transaction</th>
                    <th>Store Wise Stock</th>
                </thead>
                <tbody>
                <?
				foreach($dataArray as $row)
				{
					$trans_stock=$row[csf("balamce_qty")]*1;
					$store_stock=$store_data_arr[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("batch_lot")]]*1;
					if($trans_stock==$store_stock) {$tdColor="";} else {$tdColor="red";} 
					?>
                	<tr bgcolor="#FFFFFF" style="color:<? echo $tdColor; ?>">
                        <td><p><? echo $store_arr[$row[csf('store_id')]]; ?>&nbsp;</p></td>
                        <td><p><? echo $row[csf('batch_lot')]; ?>&nbsp;</p></td>
                        <td align="right" style="padding-right:3px"><? echo number_format($trans_stock,2); ?></td>
                        <td align="right" style="padding-right:3px"><? echo number_format($store_stock,2); ?></td>
                    </tr>
                    <?
				}
				?>
                </tbody>
                
            </table>
        </fieldset>
    </form>         
    </div>
<?
	exit();	
}

if ($action=="synchronize_stock")
{
	extract($_REQUEST);
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	$variable_lot=trim(str_replace("'","",$variable_lot));
	//echo $variable_lot.test;die;
	//$curr_stock_qnty=return_field_value("sum(case when transaction_type in(1,4,5) then cons_quantity else 0 end)-sum(case when transaction_type in(2,3,6) then cons_quantity else 0 end) as stock_qty","inv_transaction","status_active=1 and is_deleted=0 and prod_id=$prod_id","stock_qty");
	if($variable_lot==1)
	{
		$sql_trans="select a.prod_id, a.store_id, batch_lot,
		sum(case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end) as receive_qnty,
		sum(case when a.transaction_type in(1,4,5) then a.cons_amount else 0 end) as receive_amt,
		sum((case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end)-(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)) as balamce_qty
		from inv_transaction a where a.status_active=1 and a.is_deleted=0 and a.prod_id=$prod_id
		group by a.prod_id, a.store_id, batch_lot";
	}
	else
	{
		$sql_trans="select a.prod_id, a.store_id, null as batch_lot,
		sum(case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end) as receive_qnty,
		sum(case when a.transaction_type in(1,4,5) then a.cons_amount else 0 end) as receive_amt,
		sum((case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end)-(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)) as balamce_qty
		from inv_transaction a where a.status_active=1 and a.is_deleted=0 and a.prod_id=$prod_id
		group by a.prod_id, a.store_id";
	}
	$sql_transction=sql_select($sql_trans);
	$transac_data=array();
	foreach($sql_transction as $row)
	{
		if($variable_lot==1) $dyes_lot=$row[csf("batch_lot")]; else $dyes_lot="";
		$transac_data[$row[csf("prod_id")]][$row[csf("store_id")]][$dyes_lot]["receive_qnty"]=$row[csf("receive_qnty")];
		$transac_data[$row[csf("prod_id")]][$row[csf("store_id")]][$dyes_lot]["receive_amt"]=$row[csf("receive_amt")];
		if($row[csf("receive_amt")]> 0 && $row[csf("receive_qnty")] > 0)
		{
			$transac_data[$row[csf("prod_id")]][$row[csf("store_id")]][$dyes_lot]["avg_rate"]=$row[csf("receive_amt")]/$row[csf("receive_qnty")];
		}
		else
		{
			$transac_data[$row[csf("prod_id")]][$row[csf("store_id")]][$dyes_lot]["avg_rate"]=0;
		}
		
		$transac_data[$row[csf("prod_id")]][$row[csf("store_id")]][$dyes_lot]["balamce_qty"]=$row[csf("balamce_qty")];
	}
	//echo "<pre>"; print_r($transac_data);die;
	$row_store=sql_select("select id, prod_id, store_id, cons_qty, rate, amount, lot from inv_store_wise_qty_dtls where prod_id=$prod_id and status_active=1");
	$field_array_update="cons_qty*rate*amount*updated_by*update_date";
	foreach($row_store as $row)
	{
		$stock_qnty=$transac_data[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("lot")]]["balamce_qty"];
		if($stock_qnty=="") $stock_qnty=0;
		$stock_rate=number_format($transac_data[$row[csf("prod_id")]][$row[csf("store_id")]][$row[csf("lot")]]["avg_rate"],4,".","");
		$stock_amt=number_format(($stock_qnty*$stock_rate),4,".","");
		$updateID_array[]=$row[csf("id")];
		$data_array_update[$row[csf("id")]]=explode("*",("".$stock_qnty."*".$stock_rate."*".$stock_amt."*'1'*'".$pc_date_time."'"));
	}
	//echo bulk_update_sql_statement("inv_store_wise_qty_dtls","id",$field_array_update,$data_array_update,$updateID_array);die;
	$rID=execute_query(bulk_update_sql_statement("inv_store_wise_qty_dtls","id",$field_array_update,$data_array_update,$updateID_array),1);
	
	if($db_type==0)
	{
		if($rID)
		{
			mysql_query("COMMIT");  
			echo "Data Synchronize is completed successfully";
		}
		else
		{
			mysql_query("ROLLBACK"); 
			echo "Data Synchronize is not completed successfully";
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID)
		{
			oci_commit($con);  
			echo "Data Synchronize is completed successfully";
		}
		else
		{
			oci_rollback($con);
			echo "Data Synchronize is not completed successfully**$data_array_prod_update";
		}
	}
	disconnect($con);
	die;

}

?>

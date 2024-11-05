<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

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
</head>
<body>
<div align="center">
	<form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:890px;">
            <table width="740" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                	<th>Company</th> 
                    <th>Item Category</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Product Details</th>
                    <th>
                    	<input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');">
                        <input type="hidden" name="hide_data" id="hide_data" value="" />
                    </th> 					
                </thead>
                <tbody>
                	<tr class="general">
                    	<td>
							<? 
                            	echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --",'', "",0 );
                            ?>                            
                        </td>
                        <td> 
							<? echo create_drop_down("cbo_item_category",150,$item_category,"",1,"--- Select ---",$item_category_id,"",1,"","","","12,24,25,28"); ?>
                        </td>
                        <td align="center">	
							<?
								if($item_category_id==1) { $search_by_arr=array(1=>"Product Details",2=>"Product Id",3=>"Lot No"); }
                                else { $search_by_arr=array(1=>"Product Details",2=>"Product Id"); }
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";	
								echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", $selected,$dd,0 );
                            ?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" value=""/>	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_item_category').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value, 'create_product_search_list_view', 'search_div', 'store_revaluation_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    	</td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:5px" id="search_div"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
	exit(); 
}

if($action=="create_product_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$item_category_id=$data[1];
	$search_by=$data[2];
	$search_string=trim($data[3]);
	
	$company_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	
	if($company_id==0) $company_cond=""; else $company_cond=" and company_id=$company_id";
	
	if($item_category_id==1)
	{
		if($search_by==1) 
			$search_field_cond=" and product_name_details like '%".$search_string."%'"; 
		else if($search_by==2) 
			$search_field_cond=" and id=$search_string"; 
		else 
			$search_field_cond=" and lot like '%".$search_string."%'";	
		
		$sql= "select id, company_id, item_category_id, product_name_details, lot, current_stock, avg_rate_per_unit, stock_value from product_details_master where status_active=1 and is_deleted=0 and item_category_id=$item_category_id $company_cond $search_field_cond order by id";
		
		$arr=array(0=>$company_arr,1=>$item_category);
		echo create_list_view("tbl_list_search", "Company, Item Category, Product Id,Lot No,Product Details, Current Stock, Avg. Rate(Tk.), Stock Value", "60,80,70,80,240,90,90","890","260",0, $sql , "js_set_value", "id,product_name_details,company_id", "", 1, "company_id,item_category_id,0,0", $arr , "company_id,item_category_id,id,lot,product_name_details,current_stock,avg_rate_per_unit,stock_value", "",'','0,0,0,0,0,2,2,2','',0) ;
	}
	else
	{
		if($search_by==1) 
			$search_field_cond=" and product_name_details like '%".$search_string."%'"; 
		else if($search_by==2) 
			$search_field_cond=" and id=$search_string";  
			
		$sql= "select id, company_id, item_category_id, product_name_details, current_stock, avg_rate_per_unit, stock_value from product_details_master where status_active=1 and is_deleted=0 and item_category_id=$item_category_id $company_cond $search_field_cond order by id";
		
		$arr=array(0=>$company_arr,1=>$item_category);
		echo create_list_view("tbl_list_search", "Company, Item Category, Product Id,Product Details, Current Stock, Avg. Rate(Tk.), Stock Value", "60,110,70,280,90,90","890","260",0, $sql , "js_set_value", "id,product_name_details,company_id", "", 1, "company_id,item_category_id,0,0", $arr , "company_id,item_category_id,id,product_name_details,current_stock,avg_rate_per_unit,stock_value", "",'','0,0,0,0,2,2,2','',0) ;

	}
	
   exit(); 
}

if ($action=="store_revaluation")
{
	extract($_REQUEST);
	$con = connect();
	/*if($db_type==0)
	{
		mysql_query("BEGIN");
		
		$effective_date=change_date_format($effective_date,'yyyy-mm-dd');
	}
	else
	{
		$effective_date=change_date_format($effective_date,'','',1);
	}*/
	
	$row_prod=sql_select("select company_id, item_category_id, current_stock, avg_rate_per_unit, stock_value, entry_form from product_details_master where id=$prod_id");
	
	$company_id=$row_prod[0][csf('company_id')];
	$item_category_id=$row_prod[0][csf('item_category_id')];
	$current_stock=$row_prod[0][csf('current_stock')];
	$avg_rate_per_unit=$row_prod[0][csf('avg_rate_per_unit')];
	$entry_form=$row_prod[0][csf('entry_form')];
	
	
	$stock_value=$current_stock*$avg_rate;
	$field_array_prod_update="avg_rate_per_unit*stock_value";
	$data_array_prod_update=$avg_rate."*".$stock_value;
	
	$id=return_next_id("id", "inv_store_revaluation", 1);		
	$field_array="id, company_id, item_category_id, prod_id, prev_avg_rate, avg_rate, effective_date, inserted_by, insert_date";
	$data_array="(".$id.",'".$company_id."','".$item_category_id."','".$prod_id."','".$avg_rate_per_unit."','".$avg_rate."','".$effective_date."',".$user_id.",'".$pc_date_time."')";
	
	$query="update inv_transaction set cons_rate=$avg_rate,cons_amount=$avg_rate*cons_quantity,balance_amount=balance_qnty*$avg_rate, order_ile=0, order_ile_cost=0, cons_ile=0,  cons_ile_cost=0 where prod_id=$prod_id" ; //and transaction_date>='$effective_date'
	if($item_category_id==5 || $item_category_id==6 || $item_category_id==7 || $item_category_id==23)
	{
		$store_tbl_revaluation="update inv_store_wise_qty_dtls set rate=$avg_rate,amount=$avg_rate*cons_qty where prod_id=$prod_id";
	}
	
	if($db_type==0)
	{
		$queryOrderRate="update inv_receive_master a, inv_transaction b set b.order_rate=$avg_rate/a.exchange_rate, b.order_amount=($avg_rate/a.exchange_rate)*b.order_qnty where a.id=b.mst_id and b.transaction_type=1 and b.prod_id=$prod_id " ; //and b.transaction_date>='$effective_date'
		
		$queryMrr="update inv_transaction a, inv_mrr_wise_issue_details b set b.rate=$avg_rate, b.amount=$avg_rate*b.issue_qnty where a.id=b.issue_trans_id and b.prod_id=$prod_id and a.prod_id=$prod_id and a.transaction_type in(2,3,6)"; //and a.transaction_date>='$effective_date'
	
		$queryTransfer="update inv_item_transfer_mst a, inv_item_transfer_dtls b set b.rate=$avg_rate, b.transfer_value=$avg_rate*b.transfer_qnty where a.id=b.mst_id and b.to_prod_id=$prod_id and a.transfer_criteria in(1,2) and b.item_category=$item_category_id"; //and a.transfer_date>='$effective_date' 
		if($item_category_id==4 && $entry_form==24)
		{
			$queryRcvDtls="update inv_receive_master a, inv_trims_entry_dtls b set b.rate=$avg_rate/a.exchange_rate, b.amount=($avg_rate/a.exchange_rate)*b.receive_qnty, b.cons_rate=$avg_rate where a.id=b.mst_id and b.prod_id=$prod_id " ; //and DATE_FORMAT(b.insert_date, '%Y-%m-%d')>='$effective_date'
			$queryIssueDtls="update inv_trims_issue_dtls set rate=$avg_rate,amount=$avg_rate*issue_qnty where prod_id=$prod_id " ; //and DATE_FORMAT(insert_date, '%Y-%m-%d')>='$effective_date'
		}
	}
	else
	{
		$queryOrderRate="update inv_transaction b set (b.order_rate,b.order_amount)=(select $avg_rate/nvl(a.exchange_rate,1),($avg_rate/nvl(a.exchange_rate,1))*b.order_qnty from inv_receive_master a where a.id=b.mst_id) where b.transaction_type=1 and b.prod_id=$prod_id "; //and b.transaction_date>='$effective_date'
		
		$queryMrr="update inv_mrr_wise_issue_details set rate=$avg_rate, amount=$avg_rate*issue_qnty where prod_id=$prod_id and issue_trans_id in(select id from inv_transaction where prod_id=$prod_id and transaction_type in(2,3,6) )"; //and transaction_date>='$effective_date'
	
		$queryTransfer="update inv_item_transfer_dtls set rate=$avg_rate, transfer_value=$avg_rate*transfer_qnty where to_prod_id=$prod_id and mst_id in(select id from inv_item_transfer_mst where transfer_criteria in(1,2) and item_category=$item_category_id)"; //and transfer_date>='$effective_date') 
		
		if($item_category_id==4 && $entry_form==24)
		{
			$queryRcvDtls="update inv_trims_entry_dtls b set (b.rate,b.amount,b.cons_rate)=(select $avg_rate/nvl(a.exchange_rate,1),($avg_rate/nvl(a.exchange_rate,1))*b.receive_qnty, $avg_rate from inv_receive_master a where a.id=b.mst_id) where b.prod_id=$prod_id" ;
			//and to_char(b.insert_date,'DD-MON-YYYY')>='$effective_date'
			$queryIssueDtls="update inv_trims_issue_dtls set rate=$avg_rate,amount=$avg_rate*issue_qnty where prod_id=$prod_id" ; //and to_char(insert_date,'DD-MON-YYYY')>='$effective_date'
		}
	}
	
	//echo $queryTransfer;die;	
	//echo "10** insert into inv_store_revaluation ($field_array) values $data_array";die;
	$rID=$rID2=$rID3=$rID4=$rID5=$rID6=$rID7=$rID8=$rID9=true;
	$rID=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$prod_id,1);
	$rID2=sql_insert("inv_store_revaluation",$field_array,$data_array,0);
	$rID3=execute_query($query,0); 
	$rID4=execute_query($queryOrderRate,0); 
	$rID5=execute_query($queryMrr,0); 
	$rID6=execute_query($queryTransfer,0);
	if($item_category_id==4 && $entry_form==24)
	{
		$rID7=execute_query($queryRcvDtls,0); 
		$rID8=execute_query($queryIssueDtls,0);
	}
	if($item_category_id==5 || $item_category_id==6 || $item_category_id==7 || $item_category_id==23)
	{
		$rID9=execute_query($store_tbl_revaluation,0);
	}
	
	//mysql_query("ROLLBACK");
	//echo "10**".$rID. "&&". $rID2. "&&".$rID3. "&&". $rID4 . "&&". $rID5 . "&&". $rID6;die;
	
	if($db_type==0)
	{
		if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $rID7 && $rID8 && $rID9)
		{
			mysql_query("COMMIT");  
			echo "Data Revaluation is completed successfully";
		}
		else
		{
			mysql_query("ROLLBACK"); 
			echo "Data Revaluation is not completed successfully";
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $rID7 && $rID8 && $rID9)
		{
			oci_commit($con);  
			echo "Data Revaluation is completed successfully";
		}
		else
		{
			oci_rollback($con);
			echo "Data Revaluation is not completed successfully";
		}
	}
	disconnect($con);
	die;
}

?>

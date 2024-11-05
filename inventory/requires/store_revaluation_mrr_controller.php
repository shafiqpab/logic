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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_item_category').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value, 'create_product_search_list_view', 'search_div', 'store_revaluation_mrr_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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


if($action=="mrr_popup")
{
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	extract($_REQUEST);
	$company=str_replace("'","",$company);
	$item_category_id=str_replace("'","",$item_category_id);
	$txt_product_id=str_replace("'","",$txt_product_id);
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
							echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Display --", $company, "",1 );
							?> 
						</td>
						<td>
							 <? echo create_drop_down("cbo_item_category_id",150,$item_category,"",1,"--- Select Item Category ---",$item_category_id,"",1,'','',"","12,24,25,28"); ?>
						</td>
						<td>
							<input type="text" style="width:130px" class="text_boxes"  name="txt_mrr_no" id="txt_mrr_no" />
						</td>
						<td align="center">
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" />
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" />
						</td>
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_item_category_id').value+'_'+document.getElementById('txt_mrr_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $txt_product_id;?>', 'create_mrr_search_list_view', 'search_div', 'store_revaluation_mrr_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_mrr_search_list_view")
{
	$ex_data = explode("_",$data);
	$company = $ex_data[0];
	$item_category_id = $ex_data[1];
	$txt_mrr_no = trim($ex_data[2]);
	$fromDate = $ex_data[3];
	$toDate = $ex_data[4];
	$prod_id = $ex_data[5];
	
	if($txt_mrr_no=="" && $fromDate=="" && $toDate=="")
	{
		echo "Please Select Date Range";die;
	}
	
	$sql_cond="";
	$sql_cond .= " and a.recv_number LIKE '%$txt_mrr_no'";
	$sql_cond2 .= " and a.TRANSFER_SYSTEM_ID LIKE '%$txt_mrr_no'";

	if( $fromDate!="" && $toDate!="" )
	{
		if($db_type==0)
		{
			$sql_cond .= " and a.receive_date  between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
		}
		else
		{
			$sql_cond .= " and a.receive_date  between '".change_date_format($fromDate,'','',1)."' and '".change_date_format($toDate,'','',1)."'";
			$sql_cond2 .= " and a.TRANSFER_DATE  between '".change_date_format($fromDate,'','',1)."' and '".change_date_format($toDate,'','',1)."'";
		}
	}
	$sql_cond .= " and a.company_id='$company'";
	$sql_cond2 .= " and a.TO_COMPANY='$company'";
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year,";
	else $year_field="to_char(a.insert_date,'YYYY') as year,";

	$sql = "select a.id as mst_id, a.recv_number_prefix_num, a.recv_number, $year_field a.company_id, a.challan_no, a.receive_date, a.receive_basis, sum(b.cons_quantity) as receive_qnty, sum(b.balance_qnty) as balance_qnty
	from inv_transaction b, inv_receive_master a
	where a.id=b.mst_id and b.item_category=$item_category_id and b.transaction_type=1 and a.status_active=1 and b.prod_id=$prod_id $sql_cond
	group by a.id, a.recv_number_prefix_num , a.recv_number,a.company_id, a.challan_no, a.receive_date, a.receive_basis, a.insert_date
	union all
	select a.id as mst_id, a.TRANSFER_PREFIX_NUMBER as recv_number_prefix_num, a.TRANSFER_SYSTEM_ID as recv_number, $year_field a.TO_COMPANY as company_id, a.challan_no, a.TRANSFER_DATE, 0 as receive_basis, sum(b.cons_quantity) as receive_qnty, sum(b.balance_qnty) as balance_qnty
	from inv_transaction b, INV_ITEM_TRANSFER_MST a
	where a.id=b.mst_id and b.item_category=$item_category_id and b.transaction_type=5 and a.status_active=1 and b.prod_id=$prod_id $sql_cond2
	group by a.id, a.TRANSFER_PREFIX_NUMBER , a.TRANSFER_SYSTEM_ID,a.TO_COMPANY, a.challan_no, a.TRANSFER_DATE, a.insert_date";
	//echo $sql;die;
	
	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	
	$arr=array(2=>$company_arr,5=>$receive_basis_arr);
	echo create_list_view("list_view", "MRR No, Year, Company Name, Challan No, Receive Date, Receive Basis, Receive Qty., Balance Qty ","130,60,180,120,80,130,90","980","220",0, $sql , "js_set_value", "mst_id,recv_number", "", 1, "0,0,company_id,0,0,receive_basis,0,0", $arr, "recv_number,year,company_id,challan_no,receive_date,receive_basis,receive_qnty,balance_qnty", "",'','0,0,0,0,3,0,2,2') ;
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
	
	$txt_mrr_no=str_replace("'","",$txt_mrr_no);
	$txt_received_id=str_replace("'","",$txt_received_id);
	
	
	$row_prod=sql_select("select company_id, item_category_id, current_stock, avg_rate_per_unit, stock_value, entry_form from product_details_master where id=$prod_id");
	
	$company_id=$row_prod[0][csf('company_id')];
	$item_category_id=$row_prod[0][csf('item_category_id')];
	$current_stock=$row_prod[0][csf('current_stock')];
	$avg_rate_per_unit=$row_prod[0][csf('avg_rate_per_unit')];
	$entry_form=$row_prod[0][csf('entry_form')];
	
	
	//$stock_value=$current_stock*$avg_rate;
	//$field_array_prod_update="avg_rate_per_unit*stock_value";
	//$data_array_prod_update=$avg_rate."*".$stock_value;
	
	$id=return_next_id("id", "inv_store_revaluation", 1);		
	$field_array="id, company_id, item_category_id, prod_id, prev_avg_rate, avg_rate, effective_date, mrr_no, inserted_by, insert_date";
	$data_array="(".$id.",'".$company_id."','".$item_category_id."','".$prod_id."','".$avg_rate_per_unit."','".$avg_rate."','".$effective_date."','".$txt_mrr_no."',".$user_id.",'".$pc_date_time."')";
	
	$sql_trans_recive=sql_select("select b.ID as TRANS_ID, b.CONS_QUANTITY, b.CONS_AMOUNT 
	from inv_transaction b where b.PROD_ID=$prod_id and b.mst_id=$txt_received_id and b.transaction_type in(1,5) and b.status_active=1 and b.is_deleted=0");
	//$rcv_trans_id=$sql_trans_recive[0]["TRANS_ID"];
	$rcv_trans_id_arr=array();
	foreach($sql_trans_recive as $val)
	{
		$rcv_trans_id_arr[$val["TRANS_ID"]]=$val["TRANS_ID"];
	}
	
	//$rcv_trans_id=432650;
	//echo $rcv_trans_id;die;
	
	$sql_trans="select b.PROD_ID, b.ID as TRANS_ID, b.CONS_QUANTITY, b.CONS_AMOUNT, b.TRANSACTION_TYPE, b.STORE_ID, b.STORE_RATE, b.STORE_AMOUNT
	from inv_transaction b where b.PROD_ID=$prod_id and b.status_active=1 and b.is_deleted=0 
	order by b.ID";
	//echo $sql_trans;die;
	$result=sql_select($sql_trans);
	$i=1;$k=1;
	$upTransID=$queryRcvDtls=$queryIssueDtls=$queryMrr=$upProdID=true;
	foreach($result as $row)
	{
		if($row["TRANSACTION_TYPE"]==1 || $row["TRANSACTION_TYPE"]==4 || $row["TRANSACTION_TYPE"]==5)
		{
			if($row["TRANSACTION_TYPE"]==1)
			{
				//if($row["TRANS_ID"]==$rcv_trans_id)
				if(in_array($row["TRANS_ID"],$rcv_trans_id_arr))
				{
					$rcv_amt=$row["CONS_QUANTITY"]*$avg_rate;
					
					$upTransID=execute_query("update inv_transaction b set (b.order_rate,b.order_amount,b.cons_rate,b.cons_amount,b.store_rate, b.store_amount)=(select $avg_rate/nvl(a.exchange_rate,1),($avg_rate/nvl(a.exchange_rate,1))*b.order_qnty,$avg_rate,$rcv_amt,$avg_rate,$rcv_amt from inv_receive_master a where a.id=b.mst_id) where b.transaction_type=1 and b.prod_id=$prod_id and b.id=".$row["TRANS_ID"]." ");
					if($upTransID){ $upTransID=1; } else {echo"update inv_transaction b set (b.order_rate,b.order_amount,b.cons_rate,b.cons_amount,b.store_rate, b.store_amount)=(select $avg_rate/nvl(a.exchange_rate,1),($avg_rate/nvl(a.exchange_rate,1))*b.order_qnty,$avg_rate,$rcv_amt,$avg_rate,$rcv_amt from inv_receive_master a where a.id=b.mst_id) where b.transaction_type=1 and b.prod_id=$prod_id and b.id=".$row["TRANS_ID"]." ";oci_rollback($con);die;}
					
					if($item_category_id==4 && $entry_form==24)
					{
						$queryRcvDtls=execute_query("update inv_trims_entry_dtls b set (b.rate,b.amount,b.cons_rate)=(select $avg_rate/nvl(a.exchange_rate,1),($avg_rate/nvl(a.exchange_rate,1))*b.receive_qnty, $avg_rate from inv_receive_master a where a.id=b.mst_id) where b.prod_id=$prod_id and b.TRANS_ID=".$row["TRANS_ID"]." ") ;
						if($queryRcvDtls){ $queryRcvDtls=1; } else {echo"update inv_trims_entry_dtls b set (b.rate,b.amount,b.cons_rate)=(select $avg_rate/nvl(a.exchange_rate,1),($avg_rate/nvl(a.exchange_rate,1))*b.receive_qnty, $avg_rate from inv_receive_master a where a.id=b.mst_id) where b.prod_id=$prod_id and b.TRANS_ID=".$row["TRANS_ID"]." ";oci_rollback($con);die;}
					}
					
					$rcv_data[$row["PROD_ID"]]["qnty"]+=$row["CONS_QUANTITY"];
					$rcv_data[$row["PROD_ID"]]["amt"]+=$rcv_amt;
					
					$rcv_store_data[$row["PROD_ID"]][$row["STORE_ID"]]["qnty"]+=$row["CONS_QUANTITY"];
					$rcv_store_data[$row["PROD_ID"]][$row["STORE_ID"]]["amt"]+=$rcv_amt;
				}
				else
				{
					$rcv_data[$row["PROD_ID"]]["qnty"]+=$row["CONS_QUANTITY"];
					$rcv_data[$row["PROD_ID"]]["amt"]+=$row["CONS_AMOUNT"];
					
					$rcv_store_data[$row["PROD_ID"]][$row["STORE_ID"]]["qnty"]+=$row["CONS_QUANTITY"];
					$rcv_store_data[$row["PROD_ID"]][$row["STORE_ID"]]["amt"]+=$row["STORE_AMOUNT"];
				}
				
				$runtime_rate=0;
				if($rcv_data[$row["PROD_ID"]]["qnty"] > 0 && $rcv_data[$row["PROD_ID"]]["amt"] > 0)
				{
					$runtime_rate=number_format(($rcv_data[$row["PROD_ID"]]["amt"]/$rcv_data[$row["PROD_ID"]]["qnty"]),8,'.','');
				}
				
				$runtime_store_rate=0;
				if($rcv_store_data[$row["PROD_ID"]][$row["STORE_ID"]]["qnty"] > 0 && $rcv_store_data[$row["PROD_ID"]][$row["STORE_ID"]]["amt"] > 0)
				{
					$runtime_store_rate=number_format(($rcv_store_data[$row["PROD_ID"]][$row["STORE_ID"]]["amt"]/$rcv_store_data[$row["PROD_ID"]][$row["STORE_ID"]]["qnty"]),8,'.','');
				}
			}
			else
			{
				if($row["TRANS_ID"]==$rcv_trans_id)
				{
					$rcv_amt=$row["CONS_QUANTITY"]*$avg_rate;
					$upTransID=execute_query("update inv_transaction set cons_rate='".$avg_rate."', cons_amount='".$rcv_amt."', store_rate='".$avg_rate."', store_amount='".$rcv_amt."' where id=".$row["TRANS_ID"]." ");
					if($upTransID){ $upTransID=1; } else {echo"update inv_transaction set cons_rate='".$avg_rate."', cons_amount='".$rcv_amt."', store_rate='".$avg_rate."', store_amount='".$rcv_amt."' where id=".$row["TRANS_ID"]."";oci_rollback($con);die;}
					
					
					$rcv_data[$row["PROD_ID"]]["qnty"] += $row["CONS_QUANTITY"];
					$rcv_data[$row["PROD_ID"]]["amt"] += $rcv_amt;
					
					$rcv_store_data[$row["PROD_ID"]][$row["STORE_ID"]]["qnty"]+=$row["CONS_QUANTITY"];
					$rcv_store_data[$row["PROD_ID"]][$row["STORE_ID"]]["amt"]+=$rcv_amt;
					
					$runtime_rate=0;
					if($rcv_data[$row["PROD_ID"]]["qnty"] > 0 && $rcv_data[$row["PROD_ID"]]["amt"] > 0)
					{
						$runtime_rate=number_format(($rcv_data[$row["PROD_ID"]]["amt"]/$rcv_data[$row["PROD_ID"]]["qnty"]),8,'.','');
					}
					
					$runtime_store_rate=0;
					if($rcv_store_data[$row["PROD_ID"]][$row["STORE_ID"]]["qnty"] > 0 && $rcv_store_data[$row["PROD_ID"]][$row["STORE_ID"]]["amt"] > 0)
					{
						$runtime_store_rate=number_format(($rcv_store_data[$row["PROD_ID"]][$row["STORE_ID"]]["amt"]/$rcv_store_data[$row["PROD_ID"]][$row["STORE_ID"]]["qnty"]),8,'.','');
					}
				}
				else
				{
					if($row["TRANSACTION_TYPE"]==4)
					{
						$issue_amount=number_format(($row["CONS_QUANTITY"]*$runtime_rate),8,'.','');
						$issue_store_amount=number_format(($row["CONS_QUANTITY"]*$runtime_store_rate),8,'.','');
					
						$upTransID=execute_query("update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."', store_rate='".$runtime_store_rate."', store_amount='".$issue_store_amount."' where id=".$row["TRANS_ID"]." ");
						if($upTransID){ $upTransID=1; } else {echo"update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."', store_rate='".$runtime_store_rate."', store_amount='".$issue_store_amount."' where id=".$row["TRANS_ID"]."";oci_rollback($con);die;}
						
						$rcv_data[$row["PROD_ID"]]["qnty"] += $row["CONS_QUANTITY"];
						$rcv_data[$row["PROD_ID"]]["amt"] += $issue_amount;	
						
						$rcv_store_data[$row["PROD_ID"]][$row["STORE_ID"]]["qnty"]+=$row["CONS_QUANTITY"];
						$rcv_store_data[$row["PROD_ID"]][$row["STORE_ID"]]["amt"]+=$issue_store_amount;
					}
					else
					{
						$rcv_data[$row["PROD_ID"]]["qnty"] += $row["CONS_QUANTITY"];
						$rcv_data[$row["PROD_ID"]]["amt"] += $row["CONS_AMOUNT"];
						
						$rcv_store_data[$row["PROD_ID"]][$row["STORE_ID"]]["qnty"]+=$row["CONS_QUANTITY"];
						$rcv_store_data[$row["PROD_ID"]][$row["STORE_ID"]]["amt"]+=$row["CONS_AMOUNT"];
					}
					
				}
			}
		}
		else
		{
			if($rcv_data[$row["PROD_ID"]]["qnty"] > 0 && $rcv_data[$row["PROD_ID"]]["amt"] > 0)
			{
				$runtime_rate=number_format(($rcv_data[$row["PROD_ID"]]["amt"]/$rcv_data[$row["PROD_ID"]]["qnty"]),8,'.','');
			}
			$issue_amount=number_format(($row["CONS_QUANTITY"]*$runtime_rate),8,'.','');
			
			if($rcv_store_data[$row["PROD_ID"]][$row["STORE_ID"]]["qnty"] > 0 && $rcv_store_data[$row["PROD_ID"]][$row["STORE_ID"]]["amt"] > 0)
			{
				$runtime_store_rate=number_format(($rcv_store_data[$row["PROD_ID"]][$row["STORE_ID"]]["amt"]/$rcv_store_data[$row["PROD_ID"]][$row["STORE_ID"]]["qnty"]),8,'.','');
			}
			$issue_store_amount=number_format(($row["CONS_QUANTITY"]*$runtime_store_rate),8,'.','');
			
			if($row["TRANS_ID"]>$rcv_trans_id)
			{
				$upTransID=execute_query("update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."', store_rate='".$runtime_store_rate."', store_amount='".$issue_store_amount."' where id=".$row["TRANS_ID"]." ");
				if($upTransID){ $upTransID=1; } else {echo "update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."', store_rate='".$runtime_store_rate."', store_amount='".$issue_store_amount."' where id=".$row["TRANS_ID"]."";oci_rollback($con);die;}
				
				
				if($item_category_id==4 && $entry_form==24)
				{
					$queryIssueDtls=execute_query("update inv_trims_issue_dtls set rate='".$runtime_rate."',amount='".$runtime_rate."'*issue_qnty where TRANS_ID=".$row["TRANS_ID"]."") ; 
					if($queryIssueDtls){ $queryIssueDtls=1; } 
					else 
					{
						echo"update inv_trims_issue_dtls set rate='".$runtime_rate."',amount='".$runtime_rate."'*issue_qnty where TRANS_ID=".$row["TRANS_ID"]."";
						if($db_type==0) mysql_query("ROLLBACK"); else oci_rollback($con);
						die;
					}
				}
				else
				{
					$queryMrr=execute_query("update inv_mrr_wise_issue_details set rate=$runtime_rate, amount=$runtime_rate*issue_qnty where prod_id=$prod_id and issue_trans_id =".$row["TRANS_ID"]."");
					if($queryMrr){ $queryMrr=1; } 
					else 
					{
						echo"update inv_mrr_wise_issue_details set rate=$runtime_rate, amount=$runtime_rate*issue_qnty where prod_id=$prod_id and issue_trans_id =".$row["TRANS_ID"]."";
						if($db_type==0) mysql_query("ROLLBACK"); else oci_rollback($con);
						die;
					}
				}
			}
			
			$rcv_data[$row["PROD_ID"]]["qnty"] -= $row["CONS_QUANTITY"];
			$rcv_data[$row["PROD_ID"]]["amt"] -= $issue_amount;	
			
			$rcv_store_data[$row["PROD_ID"]][$row["STORE_ID"]]["qnty"] -=$row["CONS_QUANTITY"];
			$rcv_store_data[$row["PROD_ID"]][$row["STORE_ID"]]["amt"] -=$issue_store_amount;
					
			$k++;
		}
	}
	
	$prod_agv_rate=0;
	if($rcv_data[$prod_id]["amt"]!=0 && $rcv_data[$prod_id]["qnty"]!=0)
	{
		$prod_agv_rate=$rcv_data[$prod_id]["amt"]/$rcv_data[$prod_id]["qnty"];
	}
	
	$upProdID=execute_query("update product_details_master set current_stock='".number_format($rcv_data[$prod_id]["qnty"],8,'.','')."', stock_value='".number_format($rcv_data[$prod_id]["amt"],8,'.','')."', avg_rate_per_unit='".number_format($prod_agv_rate,8,'.','')."' where id=$prod_id");
	if(!$upProdID) 
	{ 
		echo "update product_details_master set current_stock='".number_format($rcv_data[$prod_id]["qnty"],8,'.','')."', stock_value='".number_format($rcv_data[$prod_id]["amt"],8,'.','')."', avg_rate_per_unit='".number_format($prod_agv_rate,8,'.','')."' where id=$prod_id";
		if($db_type==0) mysql_query("ROLLBACK"); else oci_rollback($con); 
		die;
	}
	
	//echo $queryTransfer;die;	
	//echo "10** insert into inv_store_revaluation ($field_array) values $data_array";oci_rollback($con);disconnect($con);die;
	$rID2=sql_insert("inv_store_revaluation",$field_array,$data_array,0);
	
	if($db_type==0)
	{
		if($rID2 && $upTransID && $queryRcvDtls && $queryIssueDtls && $queryMrr && $upProdID)
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
		//echo "Data  successfully $rID2 && $upTransID && $queryRcvDtls && $queryIssueDtls && $queryMrr && $upProdID";oci_rollback($con);disconnect($con);die;
		if($rID2 && $upTransID && $queryRcvDtls && $queryIssueDtls && $queryMrr && $upProdID)
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
	disconnect($con);die;
}

?>

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
	
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
		
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			if(tbl_row_count>50)
			{
				alert("You Can Select More Then 50 Item");
				$("#check_all").prop('checked', false);
				return;
			}
			
			tbl_row_count = tbl_row_count - 1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
				
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) { 
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( strCon ) 
		{
			var splitSTR = strCon.split("_");
			var str = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			var com_id = splitSTR[3];
			
			
			
			if( jQuery.inArray( selectID, selected_id ) == -1 ) {
				selected_id.push( selectID );
				selected_name.push( selectDESC );
				selected_no.push(str);					
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 ); 
				selected_no.splice( i, 1 );
			}
			var id = ''; var name = ''; var job = ''; var num='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				num += selected_no[i] + ',';
				if(i>49)
				{
					alert("You Can Select More Then 50 Item");return;
				}
			}
			toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 );
			num 	= num.substr( 0, num.length - 1 ); 
			
			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name ); 
			$('#txt_selected_no').val( num );
			$('#hdn_company_id').val( com_id );
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
                        <input type='hidden' id='txt_selected_id' />
                        <input type='hidden' id='txt_selected' />
                        <input type='hidden' id='txt_selected_no' />
                        <input type='hidden' id='hdn_company_id' />
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_item_category').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value, 'create_product_search_list_view', 'search_div', 'store_revaluation_mrr_v3_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	//echo $company_id.test;die;
	if($company_id==false) {echo "Please Select Company";die;}
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
		
		//echo $sql;
		
		$arr=array(0=>$company_arr,1=>$item_category);
		echo create_list_view("tbl_list_search", "Company, Item Category, Product Id,Lot No,Product Details, Current Stock, Avg. Rate(Tk.), Stock Value", "60,80,70,80,240,90,90","890","260",0, $sql , "js_set_value", "id,product_name_details,company_id", "", 1, "company_id,item_category_id,0,0", $arr , "company_id,item_category_id,id,lot,product_name_details,current_stock,avg_rate_per_unit,stock_value", "",'','0,0,0,0,0,2,2,2','',1) ;
	}
	else
	{
		if($search_by==1) 
			$search_field_cond=" and product_name_details like '%".$search_string."%'"; 
		else if($search_by==2) 
			$search_field_cond=" and id=$search_string";  
			
		$sql= "select id, company_id, item_category_id, product_name_details, current_stock, avg_rate_per_unit, stock_value from product_details_master where status_active=1 and is_deleted=0 and item_category_id=$item_category_id $company_cond $search_field_cond order by id";
		
		$arr=array(0=>$company_arr,1=>$item_category);
		echo create_list_view("tbl_list_search", "Company, Item Category, Product Id,Product Details,Current Stock, Avg. Rate(Tk.), Stock Value", "60,110,70,280,90,90","890","260",0, $sql , "js_set_value", "id,product_name_details,company_id", "", 1, "company_id,item_category_id,0,0", $arr , "company_id,item_category_id,id,product_name_details,current_stock,avg_rate_per_unit,stock_value", "",'','0,0,0,0,2,2,2','',1) ;

	}
	
   exit(); 
}


if($action=="rate_popup")
{
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	extract($_REQUEST);
	$company=str_replace("'","",$company);
	$item_category_id=str_replace("'","",$item_category_id);
	$txt_product_id=str_replace("'","",$txt_product_id);
	?>

	<script>
		function fn_close()
		{
			var tbl_row_count = $("#tbl_details tbody tr").length;
			var data_string="";
			for( var i=1; i<=tbl_row_count; i++)
			{
				if($("#txtRate_"+i).val()*1>0)
				{
					if(data_string=="") data_string=$("#txtProdId_"+i).val()+"_"+$("#txtRate_"+i).val()*1;
					else data_string=data_string+","+$("#txtProdId_"+i).val()+"_"+$("#txtRate_"+i).val()*1;
				}
				
			}
			//alert(data_string);
			$("#hdn_data_string").val(data_string);
			parent.emailwindow.hide();
		}
	</script>

</head>

<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<table width="600" cellspacing="0" cellpadding="0" border="1" class="rpt_table" id="tbl_details" align="center" rules="all">
				<thead>
					<tr>
						<th width="50">Product ID</th>
						<th width="150">Item Category</th>
                        <th width="250">Product Details</th>
						<th>Rate<input type='hidden' id='hdn_data_string' /></th>
					</tr>
				</thead>
				<tbody>
					<?
                    $sql= "select ID, COMPANY_ID, ITEM_CATEGORY_ID, PRODUCT_NAME_DETAILS, CURRENT_STOCK, AVG_RATE_PER_UNIT, STOCK_VALUE from product_details_master where status_active=1 and is_deleted=0 and id in($txt_product_id) order by id";
                    $sql_result=sql_select($sql);
					$i=1;
					foreach($sql_result as $val)
					{
						if ($i%2==0)$bgcolor="#E9F3FF";	else $bgcolor="#FFFFFF";
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>">
                            <td><input type="text" id="txtProdId_<?=$i;?>" name="txtProdId[]" class="text_boxes_numeric" style="width:50px" value="<?= $val["ID"];?>" /></td>
                            <td><? echo $item_category[$val["ITEM_CATEGORY_ID"]]; ?></td>
                            <td><? echo $val["PRODUCT_NAME_DETAILS"]; ?></td>
                            <td align="center"><input type="text" id="txtRate_<?=$i;?>" name="txtRate[]" class="text_boxes_numeric" style="width:50px" value="" /></td>
                        </tr>
                        <?
						$i++;
					}
                    ?>
				</tbody>
		</table>
        <input type="button" onClick="fn_close()" value="Close" style="width:100px;" class="formbutton" />
		<div align="center" style="margin-top:10px" valign="top" id="search_div"> </div>
	</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
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
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_item_category_id').value+'_'+document.getElementById('txt_mrr_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $txt_product_id;?>', 'create_mrr_search_list_view', 'search_div', 'store_revaluation_mrr_v3_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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

	if( $fromDate!="" && $toDate!="" )
	{
		if($db_type==0)
		{
			$sql_cond .= " and a.receive_date  between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
		}
		else
		{
			$sql_cond .= " and a.receive_date  between '".change_date_format($fromDate,'','',1)."' and '".change_date_format($toDate,'','',1)."'";
		}
	}
	else if($toDate!="" )
	{
		$sql_cond .= " and a.receive_date <= '".change_date_format($toDate,'','',1)."'";
	}
	
	$sql_cond .= " and a.company_id='$company'";
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year,";
	else $year_field="to_char(a.insert_date,'YYYY') as year,";

	$sql = "select a.id as mst_id, a.recv_number_prefix_num, a.recv_number, $year_field a.company_id, a.challan_no, a.receive_date, a.receive_basis, sum(b.cons_quantity) as receive_qnty, sum(b.balance_qnty) as balance_qnty
	from inv_transaction b, inv_receive_master a
	where a.id=b.mst_id and b.item_category=$item_category_id and b.transaction_type=1 and a.status_active=1 and b.prod_id=$prod_id $sql_cond
	group by a.id, a.recv_number_prefix_num , a.recv_number,a.company_id, a.challan_no, a.receive_date, a.receive_basis, a.insert_date
	order by a.id desc";
	//echo $sql;//die;
	
	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	
	$arr=array(2=>$company_arr,5=>$receive_basis_arr);
	echo create_list_view("list_view", "MRR No, Year, Company Name, Challan No, Receive Date, Receive Basis, Receive Qty., Balance Qty ","130,60,180,120,80,130,90","980","220",0, $sql , "js_set_value", "mst_id,recv_number", "", 1, "0,0,company_id,0,0,receive_basis,0,0", $arr, "recv_number,year,company_id,challan_no,receive_date,receive_basis,receive_qnty,balance_qnty", "",'','0,0,0,0,3,0,2,2') ;
	exit();

}




if ($action=="store_revaluation")
{
	extract($_REQUEST);
	$con = connect();
	//echo $db_type;die;
	/*if($user_id!=1)
	{
		echo "This page under construction, Plz wait;";die;
	}*/
	$prod_id=str_replace("'","",$prod_id);
	$txt_mrr_no=str_replace("'","",$txt_mrr_no);
	$txt_received_id=str_replace("'","",$txt_received_id);
	$avg_rate_data=str_replace("'","",$avg_rate_data);
	$txt_effective_date=change_date_format(str_replace("'","",$txt_effective_date),"","",1);
	//echo $avg_rate;die;
	$avg_rate_arr=explode(",",$avg_rate_data);
	$prod_wise_rate=array();
	foreach($avg_rate_arr as $avg_val)
	{
		$avg_val_arr=explode("_",$avg_val);
		$prod_wise_rate[$avg_val_arr[0]]=$avg_val_arr[1];
	}
	
	$dup_check=sql_select("select id from inv_store_revaluation where prod_id in(".implode(",",array_flip($prod_wise_rate)).") and status_active=1");
	if(count($dup_check)>0)
	{
		echo "Duplicate Adjustment Not Allow For This Item";die;
	}
	
	
	$row_prod=sql_select("select ID, COMPANY_ID, ITEM_CATEGORY_ID, CURRENT_STOCK, AVG_RATE_PER_UNIT, STOCK_VALUE, ENTRY_FORM from product_details_master where id in(".implode(",",array_flip($prod_wise_rate)).")");
	$product_lib_data=array();
	foreach($row_prod as $row)
	{
		$company_id=$row["COMPANY_ID"];
		$item_category_id=$row["ITEM_CATEGORY_ID"];
		$product_lib_data[$row["ID"]]["CURRENT_STOCK"]=$row["CURRENT_STOCK"];
		$product_lib_data[$row["ID"]]["AVG_RATE_PER_UNIT"]=$row["AVG_RATE_PER_UNIT"];
		$product_lib_data[$row["ID"]]["STOCK_VALUE"]=$row["STOCK_VALUE"];
		$product_lib_data[$row["ID"]]["ENTRY_FORM"]=$row["ENTRY_FORM"];;
	}
	unset($row_prod);
	
	$sql_rcv="select b.PROD_ID, max(b.ID) as TRANS_ID, max(b.MST_ID) as MST_ID, max(a.RECV_NUMBER) as RECV_NUMBER, max(b.ID) as TRANS_ID, max(b.TRANSACTION_DATE) as TRANSACTION_DATE, max(b.INSERT_DATE) as INSERT_DATE 
	from inv_transaction b, INV_RECEIVE_MASTER a where a.id=b.mst_id and b.PROD_ID in(".implode(",",array_flip($prod_wise_rate)).") and b.TRANSACTION_DATE < '$txt_effective_date' and b.transaction_type=1 and b.status_active=1 and b.is_deleted=0 and b.mst_id>0 and b.receive_basis<>30 
	group by b.PROD_ID";
	//echo $sql_rcv;die;
	$sql_rcv_result=sql_select($sql_rcv);
	$prod_wise_max_rcv_data=array();
	foreach($sql_rcv_result as $val)
	{
		$prod_wise_max_rcv_data[$val["PROD_ID"]]["TRANS_ID"]=$val["TRANS_ID"];
		$prod_wise_max_rcv_data[$val["PROD_ID"]]["TRANSACTION_DATE"]=$val["TRANSACTION_DATE"];
		$prod_wise_max_rcv_data[$val["PROD_ID"]]["INSERT_DATE"]=$val["INSERT_DATE"];
		$prod_wise_max_rcv_data[$val["PROD_ID"]]["MST_ID"]=$val["MST_ID"];
		$prod_wise_max_rcv_data[$val["PROD_ID"]]["RECV_NUMBER"]=$val["RECV_NUMBER"];
		$prod_wise_max_rcv_data[$val["PROD_ID"]]["TRANS_ID"]=$val["TRANS_ID"];
	}
	unset($sql_rcv_result);
	
	
	
	
	if($item_category_id==5 || $item_category_id==6 || $item_category_id==7 || $item_category_id==23)
	{
		$sql = sql_select("select auto_transfer_rcv, id from variable_settings_inventory where company_name = $company_id and variable_list = 29 and is_deleted = 0 and status_active = 1");
		$variable_lot=$sql[0][csf("auto_transfer_rcv")];
		if($variable_lot==1)
		{
			$sql_trans_before=sql_select("select b.ID, b.PROD_ID, b.STORE_ID, b.BATCH_LOT, b.TRANSACTION_TYPE, b.CONS_QUANTITY, b.CONS_AMOUNT, b.FLOOR_ID, b.ROOM, b.RACK, b.SELF, b.BIN_BOX
			from inv_transaction b where b.PROD_ID in(".implode(",",array_flip($prod_wise_rate)).") and b.TRANSACTION_DATE < '$txt_effective_date' and b.status_active=1 and b.is_deleted=0 and mst_id>0 and receive_basis<>30
			order by b.ID");
			
		}
		else
		{
			$sql_trans_before=sql_select("select b.ID, b.PROD_ID, b.STORE_ID, null as BATCH_LOT, b.TRANSACTION_TYPE, b.CONS_QUANTITY, b.CONS_AMOUNT, b.FLOOR_ID, b.ROOM, b.RACK, b.SELF, b.BIN_BOX
			from inv_transaction b where b.PROD_ID in(".implode(",",array_flip($prod_wise_rate)).") and b.TRANSACTION_DATE < '$txt_effective_date' and b.status_active=1 and b.is_deleted=0 and mst_id>0 and receive_basis<>30
			order by b.ID");
		}
	}
	else
	{
		$sql_trans_before=sql_select("select b.ID, b.PROD_ID, b.STORE_ID, null as BATCH_LOT, b.TRANSACTION_TYPE, b.CONS_QUANTITY, b.CONS_AMOUNT, b.FLOOR_ID, b.ROOM, b.RACK, b.SELF, b.BIN_BOX
			from inv_transaction b where b.PROD_ID in(".implode(",",array_flip($prod_wise_rate)).") and b.TRANSACTION_DATE < '$txt_effective_date' and b.status_active=1 and b.is_deleted=0 and mst_id>0 and receive_basis<>30
			order by b.ID");
	}
	$before_trans_data=array();
	$bal_qnty=$bal_amt=0;
	foreach($sql_trans_before as $row)
	{
		if($row["ID"]<=$prod_wise_max_rcv_data[$row["PROD_ID"]]["TRANS_ID"])
		{
			if($row["TRANSACTION_TYPE"]==1 || $row["TRANSACTION_TYPE"]==4 || $row["TRANSACTION_TYPE"]==5)
			{
				$before_trans_data[$row["PROD_ID"]][$row["STORE_ID"]][$row["BATCH_LOT"]]["balance_qnty"] +=$row["CONS_QUANTITY"];
				$before_trans_data[$row["PROD_ID"]][$row["STORE_ID"]][$row["BATCH_LOT"]]["balance_amount"] +=$row["CONS_AMOUNT"];
			}
			else
			{
				$before_trans_data[$row["PROD_ID"]][$row["STORE_ID"]][$row["BATCH_LOT"]]["balance_qnty"] -=$row["CONS_QUANTITY"];
				$before_trans_data[$row["PROD_ID"]][$row["STORE_ID"]][$row["BATCH_LOT"]]["balance_amount"] -=$row["CONS_AMOUNT"];
			}
			
			$before_trans_data[$row["PROD_ID"]][$row["STORE_ID"]][$row["BATCH_LOT"]]["FLOOR_ID"] =$row["FLOOR_ID"];
			$before_trans_data[$row["PROD_ID"]][$row["STORE_ID"]][$row["BATCH_LOT"]]["ROOM"] =$row["ROOM"];
			$before_trans_data[$row["PROD_ID"]][$row["STORE_ID"]][$row["BATCH_LOT"]]["RACK"] =$row["RACK"];
			$before_trans_data[$row["PROD_ID"]][$row["STORE_ID"]][$row["BATCH_LOT"]]["SELF"] =$row["SELF"];
			$before_trans_data[$row["PROD_ID"]][$row["STORE_ID"]][$row["BATCH_LOT"]]["BIN_BOX"] =$row["BIN_BOX"];
		}
	}
	
	//echo "<pre>";print_r($before_trans_data);echo $bal_qnty."=".$bal_amt;die;
	$before_insert_field="id, mst_id, receive_basis, company_id, prod_id, item_category, transaction_date, store_id, batch_lot, floor_id, room, rack, self, bin_box, transaction_type, cons_quantity, cons_rate, cons_amount, inserted_by, insert_date";
	$before_insert_data="";
	foreach($before_trans_data as $prod_id=>$prod_data)
	{
		foreach($prod_data as $store_id=>$store_data)
		{
			foreach($store_data as $batch_lot=>$val)
			{
				if($val["balance_qnty"]>0)
				{
					$befor_bal_qnty=$val["balance_qnty"];
					$befor_bal_amt=$val["balance_amount"];
					$bals_rate=0;
					if($val["balance_amount"]!=0 && $val["balance_qnty"]!=0) $bals_rate=$val["balance_amount"]/$val["balance_qnty"];
					$befor_bal_rate=$bals_rate;
					$new_bal_amt=$befor_bal_qnty*$prod_wise_rate[$prod_id];
					if($before_insert_data!="") $before_insert_data.=",";
					$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
					$before_insert_data.="(".$transactionID.",0,30,'".$company_id."','".$prod_id."','".$item_category_id."','".$prod_wise_max_rcv_data[$prod_id]["TRANSACTION_DATE"]."','".$store_id."','".$batch_lot."','".$val["FLOOR_ID"]."','".$val["ROOM"]."','".$val["RACK"]."','".$val["SELF"]."','".$val["BIN_BOX"]."',2,'".$befor_bal_qnty."','".$befor_bal_rate."','".$befor_bal_amt."',".$user_id.",'".$prod_wise_max_rcv_data[$prod_id]["INSERT_DATE"]."')";
					$insert_trans_id.=$transactionID.",";
					
					$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
					$before_insert_data.=",(".$transactionID.",0,30,'".$company_id."','".$prod_id."','".$item_category_id."','".$prod_wise_max_rcv_data[$prod_id]["TRANSACTION_DATE"]."','".$store_id."','".$batch_lot."','".$val["FLOOR_ID"]."','".$val["ROOM"]."','".$val["RACK"]."','".$val["SELF"]."','".$val["BIN_BOX"]."',1,'".$befor_bal_qnty."','".$prod_wise_rate[$prod_id]."','".$new_bal_amt."',".$user_id.",'".$prod_wise_max_rcv_data[$prod_id]["INSERT_DATE"]."')";
					$insert_trans_id.=$transactionID.",";
				}
			}
		}
	}
	
	$insert_trans_id=chop($insert_trans_id,",");
	$adjustRID=sql_insert("inv_transaction",$before_insert_field,$before_insert_data,0);
	
	if($db_type==0)
	{
		if($adjustRID)
		{
			mysql_query("COMMIT");  
		}
		else
		{
			mysql_query("ROLLBACK");
			echo "Failed Adjust";die;
		}
	}
	else
	{
		if($adjustRID)
		{
			oci_commit($con);  
		}
		else
		{
			oci_rollback($con);
			echo "Failed Adjust";die;
		}
	}
	//echo $sql_trans_before;die;
	
	$sql_trans="select b.PROD_ID, b.ID as TRANS_ID, b.TRANSACTION_DATE, b.INSERT_DATE, b.CONS_QUANTITY, b.CONS_AMOUNT, b.TRANSACTION_TYPE, b.STORE_ID, b.MST_ID, b.RECEIVE_BASIS
	from inv_transaction b where b.PROD_ID in(".implode(",",array_flip($prod_wise_rate)).") and b.status_active=1 and b.is_deleted=0 
	order by b.PROD_ID, b.INSERT_DATE, b.ID";
	//echo $sql_trans;die;
	$result=sql_select($sql_trans);
	//echo count($result);die;
	$i=1;$k=1;
	$upTransID=$queryIssueDtls=$queryMrr=$upProdID=true;
	foreach($result as $row)
	{
		$issue_amount=0;
		if($row["TRANSACTION_TYPE"]==1 || $row["TRANSACTION_TYPE"]==4 || $row["TRANSACTION_TYPE"]==5)
		{
			if($row["TRANSACTION_TYPE"]==1)
			{
				$runtime_rate=0;
				$rcv_data[$row["PROD_ID"]]["qnty"]+=$row["CONS_QUANTITY"];
				$rcv_data[$row["PROD_ID"]]["amt"]+=$row["CONS_AMOUNT"];
				if($rcv_data[$row["PROD_ID"]]["qnty"] > 0 && $rcv_data[$row["PROD_ID"]]["amt"] > 0)
				{
					$runtime_rate=($rcv_data[$row["PROD_ID"]]["amt"]/$rcv_data[$row["PROD_ID"]]["qnty"]);
				}
			}
			else
			{
				$issue_amount=($row["CONS_QUANTITY"]*$runtime_rate);
				if($row["TRANS_ID"]>$rcv_trans_id )
				{
					$upTransID=execute_query("update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."' where id=".$row["TRANS_ID"]." ");
					if($upTransID){ $upTransID=1; } else {echo"update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."' where id=".$row["TRANS_ID"]."";oci_rollback($con);die;}
					
					$rcv_data[$row["PROD_ID"]]["qnty"] += $row["CONS_QUANTITY"];
					$rcv_data[$row["PROD_ID"]]["amt"] += $issue_amount;
					
					if($item_category_id==4 && $entry_form==24)
					{
						$queryIssueDtls=execute_query("update inv_trims_issue_dtls set rate='".$runtime_rate."',amount='".$runtime_rate."'*issue_qnty where TRANS_ID=".$row["TRANS_ID"]."") ; 
						if($queryIssueDtls){ $queryIssueDtls=1; } 
						else 
						{
							echo"update inv_trims_issue_dtls set rate='".$runtime_rate."',amount='".$runtime_rate."'*issue_qnty where TRANS_ID=".$row["TRANS_ID"]."";
							if($db_type==0) mysql_query("ROLLBACK"); else oci_rollback($con);die;
						}
					}
					
				}
				else
				{
					$rcv_data[$row["PROD_ID"]]["qnty"]+=$row["CONS_QUANTITY"];
					$rcv_data[$row["PROD_ID"]]["amt"]+=$row["CONS_AMOUNT"];
				}
			}
		}
		else
		{
			$issue_amount=($row["CONS_QUANTITY"]*$runtime_rate);
			if($row["MST_ID"]==0 && $row["RECEIVE_BASIS"]==30)
			{
				$rcv_data[$row["PROD_ID"]]["qnty"] -= $row["CONS_QUANTITY"];
				$rcv_data[$row["PROD_ID"]]["amt"] -= $row["CONS_AMOUNT"];
			}
			else
			{
				if($row["TRANS_ID"]>$rcv_trans_id )
				{
					//echo $rcv_data[$row["PROD_ID"]]["amt"]."=".$rcv_data[$row["PROD_ID"]]["qnty"]."=".$runtime_rate."<br>";die;
					$upTransID=execute_query("update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."' where id=".$row["TRANS_ID"]." ");
					if($upTransID){ $upTransID=1; } else {echo"update inv_transaction set cons_rate='".$runtime_rate."', cons_amount='".$issue_amount."' where id=".$row["TRANS_ID"]."";oci_rollback($con);die;}
					
					if($item_category_id==4 && $entry_form==24)
					{
						$queryIssueDtls=execute_query("update inv_trims_issue_dtls set rate='".$runtime_rate."',amount='".$runtime_rate."'*issue_qnty where TRANS_ID=".$row["TRANS_ID"]."") ; 
						if($queryIssueDtls){ $queryIssueDtls=1; } 
						else 
						{
							echo"update inv_trims_issue_dtls set rate='".$runtime_rate."',amount='".$runtime_rate."'*issue_qnty where TRANS_ID=".$row["TRANS_ID"]."";
							if($db_type==0) mysql_query("ROLLBACK"); else oci_rollback($con);die;
						}
					}
					else
					{
						$queryMrr=execute_query("update inv_mrr_wise_issue_details set rate=$runtime_rate, amount=$runtime_rate*issue_qnty where prod_id=$prod_id and issue_trans_id =".$row["TRANS_ID"]."");
						if($queryMrr){ $queryMrr=1; } 
						else 
						{
							echo"update inv_mrr_wise_issue_details set rate=$runtime_rate, amount=$runtime_rate*issue_qnty where prod_id=$prod_id and issue_trans_id =".$row["TRANS_ID"]."";
							if($db_type==0) mysql_query("ROLLBACK"); else oci_rollback($con);die;
						}
					}
					$rcv_data[$row["PROD_ID"]]["qnty"] -= $row["CONS_QUANTITY"];
					$rcv_data[$row["PROD_ID"]]["amt"] -= $issue_amount;
				}
				else
				{
					$rcv_data[$row["PROD_ID"]]["qnty"] -= $row["CONS_QUANTITY"];
					$rcv_data[$row["PROD_ID"]]["amt"] -= $row["CONS_AMOUNT"];
				}
			}
		}
	}
	
	$upProdID=true;
	$id=return_next_id("id", "inv_store_revaluation", 1);		
	$field_array="id, company_id, item_category_id, prod_id, prev_avg_rate, avg_rate, effective_date, mrr_no, inserted_by, insert_date";
	foreach($rcv_data as $prod_id=>$prod_val)
	{
		$prod_agv_rate=0;
		if(number_format($prod_val["qnty"],8,'.','') > 0 && number_format($prod_val["amt"],8,'.','') > 0) 
		{
			$prod_agv_rate=number_format($prod_val["amt"],8,'.','')/number_format($prod_val["qnty"],8,'.','');
		}
		$upProdID=execute_query("update product_details_master set current_stock='".number_format($prod_val["qnty"],8,'.','')."', stock_value='".number_format($prod_val["amt"],8,'.','')."', avg_rate_per_unit='".number_format($prod_agv_rate,8,'.','')."' where id=$prod_id");
		if(!$upProdID) { echo "update product_details_master set current_stock='".number_format($prod_val["qnty"],8,'.','')."', stock_value='".number_format($prod_val["amt"],8,'.','')."', avg_rate_per_unit='".number_format($prod_agv_rate,8,'.','')."' where id=$prod_id";oci_rollback($con); die;}
		if($data_array!="") $data_array.=", ";
		$data_array.="(".$id.",'".$company_id."','".$item_category_id."','".$prod_id."','".$prod_agv_rate."','".$prod_wise_rate[$prod_id]."','".$txt_effective_date."','".$txt_mrr_no."',".$user_id.",'".$pc_date_time."')";
		$id++;
	}
		
	//echo "10** insert into inv_store_revaluation ($field_array) values $data_array";die;
	$rID2=sql_insert("inv_store_revaluation",$field_array,$data_array,0);
	//echo "$rID2 && $upTransID && $queryIssueDtls && $queryMrr && $upProdID";oci_rollback($con);die;
	if($db_type==0)
	{
		if($rID2 && $upTransID && $queryIssueDtls && $queryMrr && $upProdID)
		{
			mysql_query("COMMIT");  
			echo "Data Revaluation is completed successfully";
		}
		else
		{
			mysql_query("ROLLBACK");
			if($insert_trans_id!="")
			{
				$del_tr=execute_query("update inv_transaction set status_active=6, is_deleted=7 where id in($insert_trans_id) ");
				if($del_tr) mysql_query("COMMIT");
			}
			
			echo "Failed";
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		//echo "Data  successfully $rID2 && $upTransID && $queryIssueDtls && $queryMrr && $upProdID";disconnect($con);die;
		if($rID2 && $upTransID && $queryIssueDtls && $queryMrr && $upProdID)
		{
			oci_commit($con);  
			echo "Data Revaluation is completed successfully";
		}
		else
		{
			oci_rollback($con);
			if($insert_trans_id!="")
			{
				$del_tr=execute_query("update inv_transaction set status_active=6, is_deleted=7 where id in($insert_trans_id) ");
				if($del_tr) oci_commit($con);
			}
			echo "Failed";
		}
	}
	disconnect($con);die;
}

?>

<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------

if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 100, "select a.id, a.store_name from lib_store_location a,lib_store_location_category b  where a.id=b.store_location_id and a.company_id='$data' and b.category_type in(5,6,7) and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", 0, "" );
	exit();
}
//item search------------------------------//
if($action=="item_description_search")
{		  
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
    <script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
		
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
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
				var selectDESC = splitSTR[3];
				
				toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
				
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
				}
				id 		= id.substr( 0, id.length - 1 );
				name 	= name.substr( 0, name.length - 1 );
				num 	= num.substr( 0, num.length - 1 ); 
				
				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name ); 
				$('#txt_selected_no').val( num );
		}
		
		function fn_check_lot()
		{ 
			show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $company; ?>+'_'+<? echo $cbo_item_cat; ?>+'_'+document.getElementById('txt_prod_id').value+'_'+document.getElementById('txt_lot_no').value, 'create_lot_search_list_view', 'search_div', 'dyes_chemical_item_ledger_controller', 'setFilterGrid("list_view",-1)');
		}
		function fn_item_search(str)
		{
			var field_type="";
			$('#search_by_td').html('');
			$('#search_by_td_up').html('');
			if(str==1)
			{
				field_type='<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />';
				$('#search_by_td_up').html('Enter Item Description');
			}
			else if(str==2)
			{
				field_type='<? echo create_drop_down( "txt_search_common", 140,"select id,item_name  from lib_item_group where status_active=1 and item_category in($cbo_item_cat)","id,item_name", 1, "-- Select --", "", "","","","","",""); ?>';
				$('#search_by_td_up').html('Enter Item Group');
			}
			$('#search_by_td').html(field_type);
		}
    </script>
    <body>
	<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
				<thead>
					<tr>                	 
						<th width="160">Search By</th>
						<th align="center" width="160" id="search_by_td_up">Enter Item Description</th>
                        <th align="center" width="120">Lot No</th>
                        <th align="center" width="120">Product Id</th>
 						<th>
                       		<input type="reset" name="re_button" id="re_button" value="Reset" style="width:90px" class="formbutton"  />
                            <input type='hidden' id='txt_selected_id' />
							<input type='hidden' id='txt_selected' />
							<input type='hidden' id='txt_selected_no' />
                        </th>
					</tr>
				</thead>
				<tbody>
					<tr align="center" class="general">
						<td align="center">
							<?  
								$search_by = array(1=>'Item Description', 2=>'Item Group');
								$dd="";
								echo create_drop_down( "cbo_search_by", 150, $search_by, "", 0, "--Select--", "", "fn_item_search(this.value);", 0);
							?>
						</td>
						<td  align="center" id="search_by_td">				
							<input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td> 
                        <td  align="center">				
							<input type="text" style="width:90px" class="text_boxes"  name="txt_lot_no" id="txt_lot_no" />
						</td>
                        <td  align="center">				
							<input type="text" style="width:90px" class="text_boxes"  name="txt_prod_id" id="txt_prod_id" />
						</td> 
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="fn_check_lot()" style="width:90px;" />				
						</td>
					</tr>
 				</tbody>
			 </tr>         
			</table>    
			<div align="center" valign="top" style="margin-top:5px" id="search_div"> </div> 
			</form>
	   </div>
	</body>           
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script> 
	</html>
    <?
	exit();
}

if($action=="create_lot_search_list_view")
{
 	$ex_data = explode("_",$data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = trim($ex_data[1]);
	$company = $ex_data[2];
	$cbo_item_cat=$ex_data[3];
	$prod_id=str_replace("'","",$ex_data[4]);
	$lot_no=trim(str_replace("'","",$ex_data[5]));
	
	if($lot_no=="" && $prod_id=="" && ($txt_search_common=="" || $txt_search_common==0))
	{
		echo "Please Select At List One Field Item group/Lot/Product Id";die;
	}
	
	$sql_cond="";
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==1) // for LOT NO
		{
			$sql_cond= " and product_name_details LIKE '%$txt_search_common%'";	 
 		}
		else if(trim($txt_search_by)==2) // for Yarn Count
		{
			if($txt_search_common==0)
			{
			$sql_cond= " ";	 	
			}
			else
			{
			$sql_cond= " and item_group_id LIKE '%$txt_search_common%'";	 	
			}
 		} 
 	}
	if($prod_id!="") $sql_cond.=" and id=$prod_id";
	if($lot_no!="") $sql_cond.=" and lot='$lot_no'";
	
 	$sql = "select ID, ITEM_GROUP_ID, SUB_GROUP_NAME, ITEM_DESCRIPTION AS PRODUCT_NAME_DETAILS, LOT 
	from product_details_master 
	where company_id=$company and item_category_id =$cbo_item_cat and status_active=1 and is_deleted=0 $sql_cond"; 
	//echo $sql;
	$item_group_arr=return_library_array( "select id, item_name from  lib_item_group where item_category in(5,6,7,23)",'id','item_name');
	//$arr=array(1=>$item_group_arr);
	//echo create_list_view("list_view", "Product Id, Item Group, Sub Group Name, Lot, Item Description","60,120,110,100","550","260",0, $sql , "js_set_value", "id,sub_group_name,product_name_details", "", 1, "0,item_group_id,0,0,0", $arr, "id,item_group_id,sub_group_name,lot,product_name_details", "","","0","",1) ;	
	$sql_result = sql_select($sql);
	?>
    <div>
        <div style="width:680px;" align="left">
            <table cellspacing="0" cellpadding="0" width="100%" class="rpt_table" border="1" rules="all">
                <thead>
                	<th width="30">SL No</th>
                    <th width="60">Product ID</th>
                    <th width="130">Item Group</th>
                    <th width="80">Sub Group Name</th>
                    <th width="70">Lot</th>
                    <th>Item Description</th>
                </thead>
            </table>
        </div>

        <div style="width:680px; overflow-y:scroll; min-height:50px; max-height:230px;" id="buyer_list_view" align="left">
            <table cellspacing="0" cellpadding="0" width="660" class="rpt_table" id="list_view" border="1" rules="all" >
	            <?php

				
				$i = 1;
				foreach ($sql_result as $row) 
				{
					$id_arr[] = $row['ID'];

					if ($i % 2 == 0) {
						$bgcolor = "#E9F3FF";
					} else {
						$bgcolor = "#FFFFFF";
					}
					
					$selectedString = "'".$i.'_'.$row['ID'].'_'.$row['sub_group_name'].'_'.$row['PRODUCT_NAME_DETAILS']."'";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" onClick="js_set_value(<? echo $selectedString;?>)">
						<td width="30" align="center"><?php echo $i; ?></td>
						<td width="60" align="center"><?php echo $row['ID']; ?></td>
						<td width="130" style="word-break:break-all;">&nbsp;<?php echo $item_group_arr[$row['ITEM_GROUP_ID']]; ?></td>
                        <td width="80" style="word-break:break-all;"><?php echo $row['SUB_GROUP_NAME']; ?></td>
						<td width="70" style="word-break:break-all;">&nbsp; <?php echo $row['LOT']; ?></td>
						<td style="word-break:break-all">&nbsp; <?php echo $row['PRODUCT_NAME_DETAILS']; ?></td>
					</tr>
					<?php
					$i++;
				}
				?>
            </table>
        </div>

        <div style="width:580px;" align="left">
            <table width="100%">
                <tr>
                    <td align="center" colspan="6" height="30" valign="bottom">
                        <div style="width:100%">
                                <div style="width:50%; float:left" align="left">
                                    <!--<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data('<?// echo implode(',',$id_arr);?>')" /> Check / Uncheck All-->
                                </div>
                                <div style="width:50%;" align="center">
                                <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                                </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <script>
        setFilterGrid('list_view',-1);
        check_all_data();
    </script>
    <?
	exit();	
}

//report generated here--------------------//
if($action=="generate_report")
{ 
	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_item_cat=str_replace("'",'',$cbo_item_cat);
	$cbo_store_id=str_replace("'",'',$cbo_store_name);
	$cbo_company_name=str_replace("'",'',$cbo_company_name);
	//print $company; $txt_pay_date=date("j-M-Y",strtotime($txt_pay_date));
	


	$search_cond='';
	if($db_type==0)
	{
 		if( $from_date!='' && $to_date!='' ) $search_cond .= " and a.transaction_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
	}
	else
	{
		if($from_date!='' && $to_date!='') $search_cond .= " and a.transaction_date  between '".date('j-M-Y',strtotime($from_date))."' and '".date('j-M-Y',strtotime($to_date))."'";
	}
	if($cbo_store_id!=0) $store_cond=" and a.store_id in($cbo_store_id)"; else $store_cond='';
	
	$companyArr = return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0","id","company_name"); 
	$supplierArr = return_library_array("select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name"); 
	$item_group_arr=return_library_array( "select id, item_name from  lib_item_group where item_category in(5,6,7,23)",'id','item_name');
	$store_name_arr = return_library_array("select id, store_name from lib_store_location where status_active=1 and is_deleted=0","id","store_name");
	
	$variable_store_wise_rate=return_field_value("auto_transfer_rcv","variable_settings_inventory","company_name=$cbo_company_name and variable_list=47 and item_category_id=5 and status_active=1 and is_deleted=0","auto_transfer_rcv"); 
	 
 	// receive MRR array------------------------------------------------
	$sql_receive_mrr = "select a.id as TRID, a.TRANSACTION_TYPE, b.RECV_NUMBER, b.SUPPLIER_ID
	from inv_transaction a, inv_receive_master b
	where a.mst_id=b.id and a.transaction_type in (1,4,5) and a.prod_id in ($txt_product_id) and a.item_category='$cbo_item_cat'"; 
	//echo $sql_receive_mrr;die;
	$result_rcv = sql_select($sql_receive_mrr);
	$receiveMRR=array();
	$trWiseReceiveMRR=array();
    $supplierReceiveMRR=array();
	foreach($result_rcv as $row)
	{
		$receiveMRR[$row['TRID'].'**'.$row['TRANSACTION_TYPE']] = $row['RECV_NUMBER'];
		$trWiseReceiveMRR[$row['TRID']] = $row['RECV_NUMBER'];
        $supplierReceiveMRR[$row['TRID']] = $row['SUPPLIER_ID'];
	}
	unset($result_rcv);
	
	
	// issue MRR array------------------------------------------------		
	$sql_issue_mrr = "select a.id as TRID, a.TRANSACTION_TYPE, b.ISSUE_NUMBER, b.ISSUE_PURPOSE
	from inv_transaction a, inv_issue_master b
	where a.prod_id in ($txt_product_id) and a.mst_id=b.id and a.transaction_type in (2,3,6) and a.item_category='$cbo_item_cat' $store_cond";
			
	//echo $sql_issue_mrr;		 
	$result_iss = sql_select($sql_issue_mrr);
	$issueMRR=array();$issuePupose=array();
	foreach($result_iss as $row)
	{
		$issueMRR[$row['TRID'].'**'.$row['TRANSACTION_TYPE']] = $row['ISSUE_NUMBER'];
		$issuePupose[$row['TRID']] = $general_issue_purpose[$row['ISSUE_PURPOSE']];
	}
	unset($result_iss);
	
	//array join or merge here ------------- do not delete or change
	$mrrArray = array();
	$mrrArray = $receiveMRR+$issueMRR;
    $sql_transfer = "select A.ID AS TRID, B.ID AS MST_ID, A.TRANSACTION_TYPE, B.TRANSFER_SYSTEM_ID, B.COMPANY_ID, B.TO_COMPANY
	from inv_transaction a, inv_item_transfer_mst b
	where a.mst_id=b.id and a.prod_id in ($txt_product_id) and a.transaction_type in (5,6) and a.item_category='$cbo_item_cat' and b.transfer_criteria in(1,2) and b.entry_form=55";		 //echo $sql_transfer;die;
    $result_trans = sql_select($sql_transfer);
    $transferMRR=array();$trWiseTransferWith=array();
    foreach($result_trans as $row)
    {
        $transferMRR[$row["TRID"]."##".$row["TRANSACTION_TYPE"]] = $row["TRANSFER_SYSTEM_ID"];
        if($row[csf("transaction_type")]==5)
        {
            $trWiseTransferWith[$row["TRID"]."##".$row["TRANSACTION_TYPE"]] = $row["COMPANY_ID"];
        }
        else
        {
            $trWiseTransferWith[$row["TRID"]."##".$row["TRANSACTION_TYPE"]] = $row["TO_COMPANY"];
        }
    }
	unset($result_trans);

    //var_dump($mrrArray);
	?>
    <fieldset>
    <?
		$opning_bal_arr=array();
		if( $from_date!='' && $to_date!='' ) 
		{
			if($db_type==2) $from_date=date('j-M-Y',strtotime($from_date)); 
			if($db_type==0) $from_date=change_date_format($from_date, 'yyyy-mm-dd'); 
			//for opening balance
			if($variable_store_wise_rate==1)
			{
				$sqlTR = "select  a.PROD_ID, SUM(CASE WHEN a.transaction_type in (1,4,5) THEN a.cons_quantity ELSE 0 END) as RECEIVE,
				SUM(CASE WHEN a.transaction_type in (2,3,6) THEN a.cons_quantity ELSE 0 END) as ISSUE,
				SUM(CASE WHEN a.transaction_type in (1,4,5) THEN a.store_amount ELSE 0 END) as RCV_BALANCE,
				SUM(CASE WHEN a.transaction_type in (2,3,6) THEN a.store_amount ELSE 0 END) as ISS_BALANCE
				from inv_transaction a
				where transaction_date < '".$from_date."' and a.status_active=1 and a.is_deleted=0 and a.prod_id in ($txt_product_id) $store_cond 
				group by a.prod_id";
			}
			else
			{
				$sqlTR = "select  a.PROD_ID, SUM(CASE WHEN a.transaction_type in (1,4,5) THEN a.cons_quantity ELSE 0 END) as RECEIVE,
				SUM(CASE WHEN a.transaction_type in (2,3,6) THEN a.cons_quantity ELSE 0 END) as ISSUE,
				SUM(CASE WHEN a.transaction_type in (1,4,5) THEN a.cons_amount ELSE 0 END) as RCV_BALANCE,
				SUM(CASE WHEN a.transaction_type in (2,3,6) THEN a.cons_amount ELSE 0 END) as ISS_BALANCE
				from inv_transaction a
				where transaction_date < '".$from_date."' and a.status_active=1 and a.is_deleted=0 and a.prod_id in ($txt_product_id) $store_cond 
				group by a.prod_id";
			}
			
			$trResult = sql_select($sqlTR);
			
			foreach($trResult as $row)
			{
				$opning_bal_arr[$row['PROD_ID']]['PROD_ID']=$row['PROD_ID'];
				$opning_bal_arr[$row['PROD_ID']]['RECEIVE']=$row['RECEIVE'];
				$opning_bal_arr[$row['PROD_ID']]['ISSUE']=$row['ISSUE'];
				$opning_bal_arr[$row['PROD_ID']]['RCV_BALANCE']=$row['RCV_BALANCE'];
				$opning_bal_arr[$row['PROD_ID']]['ISS_BALANCE']=$row['ISS_BALANCE'];
			}
			unset($trResult);
		}
		

		//echo 'system';die;
		//var_dump($opning_bal_arr);die;
		if($cbo_method==0) //average rate ######################
		{
			if($variable_store_wise_rate==1)
			{
				$sql = "select a.ID, a.MST_ID, a.RECEIVE_BASIS, a.INSERT_DATE, a.PROD_ID, a.TRANSACTION_DATE, a.TRANSACTION_TYPE, a.CONS_QUANTITY, a.store_rate as CONS_RATE, a.store_amount as CONS_AMOUNT, b.PRODUCT_NAME_DETAILS, b.SUB_GROUP_NAME, b.UNIT_OF_MEASURE, b.ITEM_GROUP_ID, b.SUB_GROUP_NAME, b.LOT, a.STORE_ID, c.KNIT_DYE_SOURCE, c.KNIT_DYE_COMPANY, c.ISSUE_PURPOSE, a.BATCH_LOT, a.COMPANY_ID
				from inv_transaction a left join inv_issue_master c on a.mst_id=c.id and a.transaction_type in (2,3,6), product_details_master b 
				where a.status_active=1 and a.is_deleted=0 and  a.prod_id in ($txt_product_id) and a.prod_id=b.id  and a.item_category='$cbo_item_cat' and a.company_id=$cbo_company_name $search_cond $store_cond 
				order by a.prod_id, a.insert_date, a.id ASC";
			}
			else
			{
				$sql = "select a.ID, a.MST_ID, a.RECEIVE_BASIS, a.INSERT_DATE, a.PROD_ID, a.TRANSACTION_DATE, a.TRANSACTION_TYPE, a.CONS_QUANTITY, a.CONS_RATE, a.CONS_AMOUNT, b.PRODUCT_NAME_DETAILS, b.SUB_GROUP_NAME, b.UNIT_OF_MEASURE, b.ITEM_GROUP_ID, b.SUB_GROUP_NAME, b.LOT, a.STORE_ID, c.KNIT_DYE_SOURCE, c.KNIT_DYE_COMPANY, c.ISSUE_PURPOSE, a.BATCH_LOT, a.COMPANY_ID
				from inv_transaction a left join inv_issue_master c on a.mst_id=c.id and a.transaction_type in (2,3,6), product_details_master b 
				where a.status_active=1 and a.is_deleted=0 and  a.prod_id in ($txt_product_id) and a.prod_id=b.id  and a.item_category='$cbo_item_cat' and a.company_id=$cbo_company_name $search_cond $store_cond 
				order by a.prod_id, a.insert_date, a.id ASC";
			}
					
 
		}
		else if($cbo_method==1) //FIFU #########################################################################
		{
			if($variable_store_wise_rate==1)
			{
				$sql = "select a.ID, a.MST_ID, a.RECEIVE_BASIS, a.INSERT_DATE, a.PROD_ID, a.TRANSACTION_DATE, a.TRANSACTION_TYPE, a.CONS_QUANTITY, a.store_rate as CONS_RATE, a.store_amount as CONS_AMOUNT, b.PRODUCT_NAME_DETAILS, b.SUB_GROUP_NAME, b.UNIT_OF_MEASURE, b.ITEM_GROUP_ID, b.SUB_GROUP_NAME, b.LOT,a.STORE_ID, c.KNIT_DYE_SOURCE, c.KNIT_DYE_COMPANY, c.ISSUE_PURPOSE, a.BATCH_LOT, a.COMPANY_ID
				from inv_transaction a left join inv_issue_master c on a.mst_id=c.id and a.transaction_type in (2,3,6), product_details_master b 
				where a.status_active=1 and a.is_deleted=0 and  a.prod_id in ($txt_product_id) and a.prod_id=b.id  and a.item_category='$cbo_item_cat' and a.company_id=$cbo_company_name $search_cond $store_cond 
				order by  a.prod_id, a.insert_date, a.id ASC";
			}
			else
			{
				$sql = "select a.ID, a.MST_ID, a.RECEIVE_BASIS, a.INSERT_DATE, a.PROD_ID, a.TRANSACTION_DATE, a.TRANSACTION_TYPE, a.CONS_QUANTITY, a.CONS_RATE, a.CONS_AMOUNT, b.PRODUCT_NAME_DETAILS, b.SUB_GROUP_NAME, b.UNIT_OF_MEASURE, b.ITEM_GROUP_ID, b.SUB_GROUP_NAME, b.LOT,a.STORE_ID, c.KNIT_DYE_SOURCE, c.KNIT_DYE_COMPANY, c.ISSUE_PURPOSE, a.BATCH_LOT, a.COMPANY_ID
				from inv_transaction a left join inv_issue_master c on a.mst_id=c.id and a.transaction_type in (2,3,6), product_details_master b 
				where a.status_active=1 and a.is_deleted=0 and  a.prod_id in ($txt_product_id) and a.prod_id=b.id  and a.item_category='$cbo_item_cat' and a.company_id=$cbo_company_name $search_cond $store_cond 
				order by  a.prod_id, a.insert_date, a.id ASC";
			}
		}
		else if($cbo_method==2) //LIFU #########################################################################
		{
			if($variable_store_wise_rate==1)
			{
				$sql = "select a.ID, a.MST_ID, a.RECEIVE_BASIS, a.INSERT_DATE, a.PROD_ID, a.TRANSACTION_DATE, a.TRANSACTION_TYPE, a.CONS_QUANTITY, a.store_rate as CONS_RATE, a.store_amount as CONS_AMOUNT, b.PRODUCT_NAME_DETAILS, b.SUB_GROUP_NAME, b.UNIT_OF_MEASURE, b.ITEM_GROUP_ID, b.SUB_GROUP_NAME, b.LOT, a.STORE_ID, c.KNIT_DYE_SOURCE, c.KNIT_DYE_COMPANY, c.ISSUE_PURPOSE, a.BATCH_LOT, a.COMPANY_ID
				from inv_transaction a left join inv_issue_master c on a.mst_id=c.id and a.transaction_type in (2,3,6), product_details_master b 
				where a.status_active=1 and a.is_deleted=0 and  a.prod_id in ($txt_product_id) and a.prod_id=b.id  and a.item_category='$cbo_item_cat' and a.company_id=$cbo_company_nam $search_cond $store_cond 
				order by  a.prod_id, a.insert_date, a.id ASC";
			}
			else
			{
				$sql = "select a.ID, a.MST_ID, a.RECEIVE_BASIS, a.INSERT_DATE, a.PROD_ID, a.TRANSACTION_DATE, a.TRANSACTION_TYPE, a.CONS_QUANTITY, a.CONS_RATE, a.CONS_AMOUNT, b.PRODUCT_NAME_DETAILS, b.SUB_GROUP_NAME, b.UNIT_OF_MEASURE, b.ITEM_GROUP_ID, b.SUB_GROUP_NAME, b.LOT, a.STORE_ID, c.KNIT_DYE_SOURCE, c.KNIT_DYE_COMPANY, c.ISSUE_PURPOSE, a.BATCH_LOT, a.COMPANY_ID
				from inv_transaction a left join inv_issue_master c on a.mst_id=c.id and a.transaction_type in (2,3,6), product_details_master b 
				where a.status_active=1 and a.is_deleted=0 and  a.prod_id in ($txt_product_id) and a.prod_id=b.id  and a.item_category='$cbo_item_cat' and a.company_id=$cbo_company_nam $search_cond $store_cond 
				order by  a.prod_id, a.insert_date, a.id ASC";
			}
		}
//		echo $sql;
		$result = sql_select($sql);	
		$checkItemArr=array();
		$balQnty=$balValue=array(); 
		$rcvQnty=$rcvValue=$issQnty=$issValue=0;
		$i=1;
		ob_start();	
		?>
    	
        <div> 
			<table style="width:1650px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" >
            	<thead>
                	<tr class="form_caption" style="border:none;">
                        <td colspan="17" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?> </td>
                    </tr>
                    <tr style="border:none;">
                            <td colspan="17" align="center" style="border:none; font-size:14px;">
                                Company Name : <? echo $companyArr[str_replace("'",'',$cbo_company_name)]; ?>                                
                            </td>
                    </tr>
                    <tr style="border:none;">
                            <td colspan="17" align="center" style="border:none;font-size:12px; font-weight:bold">
                                <? if($from_date!='' || $to_date!='') echo 'From '.change_date_format($from_date).' To '.change_date_format($to_date); ?>
                            </td>
                    </tr>
                    <tr>
                        <td colspan="9">&nbsp;</td>
                        <td colspan="9" align="center"><b>Weighted Average Method</b></td>
                    </tr> 
                    <tr>
                        <th width="30" rowspan="2">SL</th>
                        <th width="100" rowspan="2">Store</th>
                        <th width="70" rowspan="2">Trans Date</th>
                        <th width="110" rowspan="2">Trans Ref No</th>
                        <th width="100" rowspan="2">Lot</th>
                        <th width="100" rowspan="2">Trans Type</th>
                        <th width="100" rowspan="2">Purpose</th>
                        <th width="100" rowspan="2">Trans Com</th>
                        <th width="100" rowspan="2">Trans With</th>
                        <th width="" colspan="3">Receive</th>
                        <th width="" colspan="3">Issue</th>
                        <th width="" colspan="3">Balance</th>                    
                    </tr>
                    <tr>
                      <th width="80">Qnty</th>
                      <th width="80">Rate</th>
                      <th width="110">Value</th>
                      <th width="80">Qnty</th>
                      <th width="80">Rate</th>
                      <th width="110">Value</th>
                      <th width="80">Qnty</th>
                      <th width="80">Rate</th>
                      <th>Value</th>
                  </tr>
                </thead>
            </table>  
          <div style="width:1650px; overflow-y:scroll; max-height:250px" id="scroll_body" >
          	    <table style="width:1630px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body"  >
					<?		
					$m=1;$k=1;
					$product_id_arr=array();
										
					foreach($result as $row)
					{
						$pro_id=$row['PROD_ID'];							 
						//check items new or not and print product description------
						if(!in_array($row['PROD_ID'],$checkItemArr))
						{								
							if($i!=1) // product wise sum/total here------------
							{
								?>
								<tr class="tbl_bottom">
									<td colspan="9" align="right">Total</td>
									<td  align="right"><? echo number_format($rcvQnty,4); ?></td>
                                    <td>&nbsp;</td>
                                    <td  align="right"><? echo number_format($rcvValue,2); ?></td>
									<td  align="right"><? echo number_format($issQnty,4); ?></td>
                                    <td>&nbsp;</td>
                                    <td  align="right"><? echo number_format($issValue,2); ?></td>                                    
									<td  align="right"><? echo number_format($total_balQnty,4); ?></td>
                                    <td>&nbsp;</td>
                                    <td  align="right"><? echo number_format($total_balValue,2); ?></td>
								</tr>									
								<!-- product wise herder -->
								<thead>
									<tr>
										<td colspan="12"><b>Product ID : <? echo $row['PROD_ID']." , ".$row['PRODUCT_NAME_DETAILS'].", Item Group# ".$item_group_arr[$row['ITEM_GROUP_ID']].", Sub Group Name# ".$row['SUB_GROUP_NAME'].", Item Category#  ".$item_category[$cbo_item_cat].", UOM# ".$unit_of_measurement[$row['UNIT_OF_MEASURE']]; ?></b></td>
										<td colspan="6" align="center">&nbsp;</td>
									</tr>
								</thead>
									<!-- product wise herder END -->
								<?
							}
							
							$flag=0;
							$opening_qnty=$opening_balance=$opening_rate=0;
							if($opning_bal_arr[$pro_id]['PROD_ID'] != '')
							{
								?>									
								<tr style="background-color:#FFFFCC">
									<td colspan="15" align="right"><b>Opening Balance</b></td>
										<?
										$opening_qnty = $opning_bal_arr[$pro_id]['RECEIVE']-$opning_bal_arr[$pro_id]['ISSUE'];
										$opening_balance = $opning_bal_arr[$pro_id]['RCV_BALANCE']-$opning_bal_arr[$pro_id]['ISS_BALANCE'];
										$opening_rate = $opening_balance/$opening_qnty;
										?>
									<td width="80" align="right"><? echo number_format($opening_qnty,4); ?></td>
									<td width="60" align="right"><? echo number_format($opening_rate,2); ?></td>
									<td width="" align="right"><? echo number_format($opening_balance,4); ?></td>              
								</tr>
									
								<?
								$balQnty[$opning_bal_arr[$pro_id]['PROD_ID']] = $opening_qnty;
								$balValue [$opning_bal_arr[$pro_id]['PROD_ID']]= $opening_balance;
								
								$flag=1;
								$opening_qnty=0;
								$opening_balance=0;
							} // end opening balance foreach 	
								
							$checkItemArr[$row['PROD_ID']]=$row['PROD_ID'];
							$rcvQnty=$rcvValue=$issQnty=$issValue=0; // initialize variable
							//$balQnty=$balValue=0;	
							$total_balQnty=0;$total_balValue=0;						
						}
						//var_dump($balQnty);								
						//print product name details header---------------------------
						if($i==1)
						{
							?> 
                            <thead>
								<tr>
									<td colspan="12"><b>Product ID : <? echo $row['PROD_ID']." , ".$row['PRODUCT_NAME_DETAILS'].", Item Group# ".$item_group_arr[$row['ITEM_GROUP_ID']].", Sub Group Name# ".$row['SUB_GROUP_NAME'].", Item Category#  ".$item_category[$cbo_item_cat].", UOM# ".$unit_of_measurement[$row['UNIT_OF_MEASURE']]; ?></b></td>
									<td colspan="6" align="center"></td>
								</tr>
							</thead> 
							<?
						}
							
							
						if ($i%2==0) $bgcolor='#E9F3FF'; else $bgcolor='#FFFFFF';
						if($row['TRANSACTION_TYPE']==2 || $row['TRANSACTION_TYPE']==3 || $row['TRANSACTION_TYPE']==6) 
							$stylecolor='style="color:#A61000"';
						else
							$stylecolor='style="color:#000000"';
						if(!in_array($row['PROD_ID'],$product_id_arr))
						{
							$k=1;										
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30" title="<? echo $row['ID']; ?>"><? echo $k; ?></td>	
                                <td width="100"><? echo $store_name_arr[$row['STORE_ID']]; ?></td>     							
								<td width="70"><p><? if($row['TRANSACTION_DATE'] !='' && $row['TRANSACTION_DATE'] !='0000-00-00')  echo change_date_format($row['TRANSACTION_DATE']); ?>&nbsp;</p></td>                                
								<td width="110"><p>
								<?
								if( $row['MST_ID']==0 && $row['RECEIVE_BASIS']==30)
								{
									echo 'Adjustment'; 
								}
								else
								{
									if($row['TRANSACTION_TYPE']==5 || $row['TRANSACTION_TYPE']==6)
									{
										echo $trans_sys_num[$row['MST_ID']]; 
									}
									else
									{
										echo $mrrArray[$row['ID'].'**'.$row['TRANSACTION_TYPE']]; 
									} 
								}
								
								?>
                                </p></td>
                                <td width="100"><p><? echo $row['BATCH_LOT']; ?></p></td>
								<td width="100"><p><? echo $transaction_type[$row['TRANSACTION_TYPE']]; ?></p></td>
								<td width="100"><p>
								<?
								//echo $row[csf("id")];
								if($row['TRANSACTION_TYPE']==5 || $row['TRANSACTION_TYPE']==6)
								{
									
								} else {										
									echo $issuePupose[$row['ID']];
								}
								?>
                                </p></td>
								<? 										
								$transactionWithCom =  $companyArr[$row['COMPANY_ID']];

                                if($row[csf("transaction_type")]==2)
                                {
                                    if($row[csf("knit_dye_source")]==1) {
                                        $transactionWith = $companyArr[$row[csf("knit_dye_company")]];
                                    }else {
                                        $transactionWith = $supplierArr[$row[csf("knit_dye_company")]];
                                    }
                                }
                                else if($row[csf("transaction_type")]==1)
                                {
                                    $transactionWith =  $supplierArr[$supplierReceiveMRR[$row[csf("id")]]];
                                }
                                else if($row[csf("transaction_type")]==5 || $row[csf("transaction_type")]==6)
                                {
                                    //$trWiseTransferWith[$row[csf("trid")]."##".$row[csf("transaction_type")]]
                                    $transactionWith =  $companyArr[$trWiseTransferWith[$row[csf("id")]."##".$row[csf("transaction_type")]]];
                                }

                                ?>
                                <td width="100"><p><? if($row['TRANSACTION_TYPE']==2 || $row['TRANSACTION_TYPE']==3 || $row['TRANSACTION_TYPE']==6) echo $transactionWithCom; else echo "&nbsp;"; ?></p></td>
                                <td width="100"><p><? echo $transactionWith; ?></p></td>
                                <td width="80" align="right"><? if($row['TRANSACTION_TYPE']==1 || $row['TRANSACTION_TYPE']==4 || $row['TRANSACTION_TYPE']==5) echo number_format($row['CONS_QUANTITY'],4); ?></td>
								<td width="80" align="right" title="<? if($row['TRANSACTION_TYPE']==1 || $row['TRANSACTION_TYPE']==4 || $row['TRANSACTION_TYPE']==5) echo $row['CONS_RATE']; ?>"><? if($row['TRANSACTION_TYPE']==1 || $row['TRANSACTION_TYPE']==4 || $row['TRANSACTION_TYPE']==5) echo number_format($row['CONS_RATE'],4); ?></td>
								<td width="110" align="right"><? if($row['TRANSACTION_TYPE']==1 || $row['TRANSACTION_TYPE']==4 || $row['TRANSACTION_TYPE']==5) echo number_format($row['CONS_AMOUNT'],2); ?></td>              
								
								<td width="80" align="right"><? if($row['TRANSACTION_TYPE']==2 || $row['TRANSACTION_TYPE']==3 || $row['TRANSACTION_TYPE']==6) echo number_format($row['CONS_QUANTITY'],4); ?></td>
								<td width="80" align="right" title="<? if($row['TRANSACTION_TYPE']==2 || $row['TRANSACTION_TYPE']==3 || $row['TRANSACTION_TYPE']==6) echo $row['CONS_RATE']; ?>"><? if($row['TRANSACTION_TYPE']==2 || $row['TRANSACTION_TYPE']==3 || $row['TRANSACTION_TYPE']==6) echo number_format($row['CONS_RATE'],4); ?></td>
								<td width="110" align="right"><? if($row['TRANSACTION_TYPE']==2 || $row['TRANSACTION_TYPE']==3 || $row['TRANSACTION_TYPE']==6|| $row['TRANSACTION_TYPE']==6) echo number_format($row['CONS_AMOUNT'],2); ?></td>
								<?
								$each_pro_id=array();									
																									
								if($row['TRANSACTION_TYPE']==1 || $row['TRANSACTION_TYPE']==4 || $row['TRANSACTION_TYPE']==5) $total_balQnty =number_format($balQnty[$row['PROD_ID']],8,'.','') + number_format(trim($row['CONS_QUANTITY']),8,'.',''); 
								if($row['TRANSACTION_TYPE']==2 || $row['TRANSACTION_TYPE']==3 || $row['TRANSACTION_TYPE']==6) $total_balQnty =number_format($balQnty[$row['PROD_ID']],8,'.','') - number_format(trim($row['CONS_QUANTITY']),8,'.','');
								
								if($row['TRANSACTION_TYPE']==1 || $row['TRANSACTION_TYPE']==4 || $row['TRANSACTION_TYPE']==5)  $total_balValue =number_format($balValue[$row['PROD_ID']],8,'.','') + number_format(trim($row['CONS_AMOUNT']),8,'.','');
								if($row['TRANSACTION_TYPE']==2 || $row['TRANSACTION_TYPE']==3 || $row['TRANSACTION_TYPE']==6)  $total_balValue =number_format($balValue[$row['PROD_ID']],8,'.','') - number_format(trim($row['CONS_AMOUNT']),8,'.','');
									
								if($total_balQnty == 0)
								{
									$bal_rate=0;
									$total_balValue=0;
								}
								else 
								{
									if($total_balValue!=0 && $total_balQnty !=0)
									{
										$bal_rate=$total_balValue/$total_balQnty;
									}
									else $bal_rate=0;
								}
							
								/*if($total_balValue!=0 && $total_balQnty !=0)
								{
									$bal_rate=$total_balValue/$total_balQnty;
								}
								else
								{
									$bal_rate=0;
								}*/
							
								?> 
								<td width="80" align="right" title="<? echo $total_balQnty; ?>"><? echo number_format($total_balQnty,4,'.',''); ?></td>
								<td width="80" style="word-break:break-all;" align="right" title="<? echo $bal_rate; ?>"><? if(number_format($total_balQnty,4,'.','')!=0) echo number_format($bal_rate,4,'.',''); else echo "0.0000"; ?></td>
								<td align="right" title="<? echo $total_balValue; ?>"><? echo number_format($total_balValue,2,'.',''); ?></td>              
							</tr>							
							<?
							$k++; 
							$product_id_arr[]=$row['PROD_ID'];						
						}
						else
						{
							?>
                            <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30" title="<? echo $row['ID']; ?>"><? echo $k; ?></td>	
                                 <td width="100"><? echo $store_name_arr[$row['STORE_ID']]; ?></td> 							
								<td width="70"><p><? if($row['TRANSACTION_DATE'] !='' && $row['TRANSACTION_DATE'] !='0000-00-00')  echo change_date_format($row['TRANSACTION_DATE']); ?>&nbsp;</p></td>                                 
								<td width="110"><p>
								<?
								if( $row['MST_ID']==0 && $row['RECEIVE_BASIS']==30)
								{
									echo "Adjustment"; 
								}
								else
								{
									if($row['TRANSACTION_TYPE']==5 || $row['TRANSACTION_TYPE']==6)
									{
										echo $trans_sys_num[$row['MST_ID']]; 
									}
									else
									{
										echo $mrrArray[$row['ID'].'**'.$row['TRANSACTION_TYPE']]; 
									} 
								}
								?>
                                </p></td>
								<td width="100"><p><? echo $row['BATCH_LOT']; ?></p></td>
                                <td width="100"><p><? echo $transaction_type[$row['TRANSACTION_TYPE']]; ?></p></td>
								<td width="100"><p>
                                <?
								//echo  $row[csf("id")];
								if($row['TRANSACTION_TYPE']==5 || $row['TRANSACTION_TYPE']==6)
								{
									
								} else	{
									echo $issuePupose[$row['ID']]; 
								}
								?>									
                                </p></td>                                    
								<? 										
								$transactionWithCom =  $companyArr[$row['COMPANY_ID']];
                                if($row[csf("transaction_type")]==2)
                                {
                                    if($row[csf("knit_dye_source")]==1) {
                                        $transactionWith = $companyArr[$row[csf("knit_dye_company")]];
                                    }
                                    else{
                                        $transactionWith =  $supplierArr[$row[csf("knit_dye_company")]];
                                        }
                                }
                                else if($row[csf("transaction_type")]==1)
                                {
                                    $transactionWith =  $supplierArr[$supplierReceiveMRR[$row[csf("id")]]];
                                }
                                else if($row[csf("transaction_type")]==5 || $row[csf("transaction_type")]==6)
                                {
                                    //$trWiseTransferWith[$row[csf("trid")]."##".$row[csf("transaction_type")]]
                                    $transactionWith =  $companyArr[$trWiseTransferWith[$row[csf("id")]."##".$row[csf("transaction_type")]]];
                                }

                                ?>

                                <td width="100"><p><? if($row['TRANSACTION_TYPE']==2 || $row['TRANSACTION_TYPE']==3 || $row['TRANSACTION_TYPE']==6) echo $transactionWithCom; else echo "&nbsp;"; ?></p></td>
                                <td width="100"><p><? echo $transactionWith; ?></p></td>
                                <td width="80" align="right"><? if($row['TRANSACTION_TYPE']==1 || $row['TRANSACTION_TYPE']==4  || $row['TRANSACTION_TYPE']==5) echo number_format($row['CONS_QUANTITY'],4); ?></td>
								<td width="80" align="right" title="<? if($row['TRANSACTION_TYPE']==1 || $row['TRANSACTION_TYPE']==4 || $row['TRANSACTION_TYPE']==5) echo $row['CONS_RATE']; ?>"><? if($row['TRANSACTION_TYPE']==1 || $row['TRANSACTION_TYPE']==4 || $row['TRANSACTION_TYPE']==5) echo number_format($row['CONS_RATE'],4); ?></td>
								<td width="110" align="right"><? if($row['TRANSACTION_TYPE']==1 || $row['TRANSACTION_TYPE']==4 || $row['TRANSACTION_TYPE']==5) echo number_format($row['CONS_AMOUNT'],2); ?></td>              
								
								<td width="80" align="right"><? if($row['TRANSACTION_TYPE']==2 || $row['TRANSACTION_TYPE']==3  || $row['TRANSACTION_TYPE']==6) echo number_format($row['CONS_QUANTITY'],4); ?></td>
								<td width="80" align="right" title="<? if($row['TRANSACTION_TYPE']==2 || $row['TRANSACTION_TYPE']==3 || $row['TRANSACTION_TYPE']==6) echo $row['CONS_RATE']; ?>"><? if($row['TRANSACTION_TYPE']==2 || $row['TRANSACTION_TYPE']==3 || $row['TRANSACTION_TYPE']==6) echo number_format($row['CONS_RATE'],4); ?></td>
								<td width="110" align="right"><? if($row['TRANSACTION_TYPE']==2 || $row['TRANSACTION_TYPE']==3 || $row['TRANSACTION_TYPE']==6) echo number_format($row['CONS_AMOUNT'],2); ?></td>
								<?
								$each_pro_id=array();										
																									
								if($row['TRANSACTION_TYPE']==1 || $row['TRANSACTION_TYPE']==4 || $row['TRANSACTION_TYPE']==5) $total_balQnty =number_format($total_balQnty,8,'.','') + number_format(trim($row['CONS_QUANTITY']),8,'.',''); 
								if($row['TRANSACTION_TYPE']==2 || $row['TRANSACTION_TYPE']==3 || $row['TRANSACTION_TYPE']==6) $total_balQnty =number_format($total_balQnty,8,'.','') - number_format(trim($row['CONS_QUANTITY']),8,'.',''); 
								
								if($row['TRANSACTION_TYPE']==1 || $row['TRANSACTION_TYPE']==4 || $row['TRANSACTION_TYPE']==5)  $total_balValue = number_format($total_balValue,8,'.','') + number_format(trim($row['CONS_AMOUNT']),8,'.','');
								if($row['TRANSACTION_TYPE']==2 || $row['TRANSACTION_TYPE']==3 || $row['TRANSACTION_TYPE']==6)  $total_balValue = number_format($total_balValue,8,'.','') - number_format(trim($row['CONS_AMOUNT']),8,'.','');
									
								if($total_balQnty == 0)
								{
									$bal_rate=0;
									$total_balValue=0;
								}
								else 
								{
									if($total_balValue!=0 && $total_balQnty !=0)
									{
										$bal_rate=$total_balValue/$total_balQnty;
									}
									else $bal_rate=0;
								}
							
								/*if($total_balValue!=0 && $total_balQnty !=0)
								{
									$bal_rate=$total_balValue/$total_balQnty;
								}
								else
								{
									$bal_rate=0;
								}*/
								
								?> 
								<td width="80" align="right" title="<? echo $total_balQnty; ?>"><? echo number_format($total_balQnty,4,'.',''); ?></td>
								<td width="80" style="word-break:break-all;" align="right" title="<? echo $bal_rate; ?>"><? if(number_format($total_balQnty,4,'.','')!=0) echo number_format($bal_rate,4,'.',''); else echo "0.0000"; ?></td>
								<td align="right" title="<? echo $total_balValue; ?>"><? echo number_format($total_balValue,2,'.',''); ?></td> 
							</tr>                               
                            <?
							$k++;
						}
						//$total_balQnty=0;
						//$total_balValue=0;											
						$i++;
						
						//total sum START-----------------------
						if($row['TRANSACTION_TYPE']==1 || $row['TRANSACTION_TYPE']==4 || $row['TRANSACTION_TYPE']==5) $rcvQnty += $row['CONS_QUANTITY'];
						if($row['TRANSACTION_TYPE']==1 || $row['TRANSACTION_TYPE']==4 || $row['TRANSACTION_TYPE']==5) $rcvValue += $row['CONS_AMOUNT'];
						
						if($row['TRANSACTION_TYPE']==2 || $row['TRANSACTION_TYPE']==3 || $row['TRANSACTION_TYPE']==6) $issQnty += $row['CONS_QUANTITY'];
						if($row['TRANSACTION_TYPE']==2 || $row['TRANSACTION_TYPE']==3 || $row['TRANSACTION_TYPE']==6) $issValue += $row['CONS_AMOUNT'];
						  
					} ?> <!-- END FOREACH LOOP--> 			
                     
					<tr class="tbl_bottom">
						<td colspan="9" align="right">Total</td>
						<td align="right" ><? echo number_format($rcvQnty,4); ?></td>
                        <td>&nbsp;</td>
                        <td align="right" ><? echo number_format($rcvValue,2); ?></td>
						<td align="right" ><? echo number_format($issQnty,4); ?></td>
                        <td>&nbsp;</td>
                        <td align="right" ><? echo number_format($issValue,2); ?></td>
                        <td align="right" ><? echo number_format($total_balQnty,4); ?></td>
                        <td>&nbsp;</td>
                        <td align="right" ><? echo number_format($total_balValue,2); ?></td>
					</tr>  
				</table> 
			</div>  
		</div>    
	</fieldset>  
    <?
	$html = ob_get_contents();
	ob_clean();
	//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename"; 
	exit();
	 
}
?>


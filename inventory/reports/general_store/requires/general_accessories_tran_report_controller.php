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
//--------------------------------------------------------------------------------------------

//library array-------------------
$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name"); 
$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name"); 
$item_group_arr=return_library_array( "select id, item_name from  lib_item_group",'id','item_name');


if ($action=="load_drop_down_store")
{
	//echo create_drop_down( "cbo_store_name", 100, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data' and b.category_type=1 and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", $selected, "",0 );     	 
	echo create_drop_down( "cbo_store_name", 100, "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and b.category_type in (8,9,10,11,15,16,17,18,19,20,21,22,32) $store_location_credential_cond and a.status_active=1 and a.is_deleted=0 and a.company_id in($data)  group by a.id ,a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", "", "","" ); 
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
				var selectDESC = splitSTR[2];
				
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
			show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $company; ?>+'_'+<? echo $cbo_item_cat; ?>+'_'+document.getElementById('txt_item_code').value+'_'+document.getElementById('txt_prod_id').value, 'create_lot_search_list_view', 'search_div', 'general_accessories_tran_report_controller', 'setFilterGrid("list_view",-1)');
		}
		function fn_item_search(str)
		{
			var field_type="";
			$('#search_by_td').html('');
			$('#search_by_td_up').html('');
			if(str==1)
			{
				field_type='<input type="text" style="width:150px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />';
				$('#search_by_td_up').html('Enter Item Description');
			}
			else if(str==2)
			{
				field_type='<? echo create_drop_down( "txt_search_common", 160,"select id,item_name  from lib_item_group where item_category=$cbo_item_cat and status_active=1","id,item_name", 1, "-- Select --", "", "","","","","",""); ?>';
				$('#search_by_td_up').html('Enter Item Group');
			}
			$('#search_by_td').html(field_type);
		}
    </script>
    <body>
	<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<table width="650" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
				<thead>
					<tr>                	 
						<th width="130">Search By</th>
						<th align="center" width="180" id="search_by_td_up">Enter Item Description</th>
                        <th width="110">Item Code</th>
                        <th width="110">Product Id</th>
 						<th>
                       		<input type="reset" name="re_button" id="re_button" value="Reset" style="width:80px" class="formbutton"  />
                            <input type='hidden' id='txt_selected_id' />
							<input type='hidden' id='txt_selected' />
							<input type='hidden' id='txt_selected_no' />
                        </th>
					</tr>
				</thead>
				<tbody>
					<tr class="general">
						<td align="center">
							<?  
								$search_by = array(1=>'Item Description', 2=>'Item Group');
								$dd="";
								echo create_drop_down( "cbo_search_by", 120, $search_by, "", 0, "--Select--", "", "fn_item_search(this.value);", 0);
							?>
						</td>
						<td align="center" id="search_by_td">				
							<input type="text" style="width:150px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
                        <td align="center">				
							<input type="text" style="width:90px" class="text_boxes"  name="txt_item_code" id="txt_item_code" />
						</td> 
                        <td align="center" id="search_by_td">				
							<input type="text" style="width:90px" class="text_boxes_numeric"  name="txt_prod_id" id="txt_prod_id" />
						</td>  
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="fn_check_lot()" style="width:80px;" />				
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
	$txt_item_code=str_replace("'","",$ex_data[4]);
	$txt_prod_id=str_replace("'","",$ex_data[5]);
	
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
	
	if($txt_item_code!="") $sql_cond.=" and  item_code='$txt_item_code'";
	if($txt_prod_id!="") $sql_cond.=" and  id=$txt_prod_id";
	
 	$sql = "select id,item_group_id,item_description as product_name_details,item_code,item_size from product_details_master where entry_form<>24 and company_id=$company and item_category_id =$cbo_item_cat $sql_cond  and status_active=1 and is_deleted=0"; 
	$arr=array(1=>$item_group_arr);
	echo create_list_view("list_view", "Product Id, Item Group, Item Code, Item Description,Item Size","70,160,100,100","600","260",0, $sql , "js_set_value", "id,product_name_details", "", 1, "0,item_group_id,0,0,0", $arr, "id,item_group_id,item_code,product_name_details,item_size", "","","0","",1) ;	
	
	exit();	
}

//report generated here--------------------//
if($action=="generate_report")
{ 
	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_item_cat=str_replace("'","",$cbo_item_cat);
	//print $company; $txt_pay_date=date("j-M-Y",strtotime($txt_pay_date));
	$search_cond="";
	if($db_type==0)
	{
 		if( $from_date!="" && $to_date!="" ) $search_cond .= " and a.transaction_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
	}
	else
	{
		if($from_date!="" && $to_date!="") $search_cond .= " and a.transaction_date  between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";
	}

	
	$all_product_ids= array_chunk(explode(",",$txt_product_id),999);
	$item_desc_search_cond="";
	//$chunk_arr=array_chunk($po_ids,999);
	foreach($all_product_ids as $val)
	{
		$ids=implode(",",$val);
		if($item_desc_search_cond=="")
		{
			$item_desc_search_cond.=" and (a.prod_id in ($ids) ";
			//$poIdsCond.=" and ( b.id in ( $ids) ";
		}
		else
		{
			$item_desc_search_cond.=" or  a.prod_id in  ( $ids) ";
			//$poIdsCond.=" or  b.id in ( $ids) ";
		}
	}
	$item_desc_search_cond.=")";
	//$poIdsCond.=")";
	//echo $item_desc_search_cond;die;

 	$department_arr=return_library_array("select id,department_name from lib_department",'id','department_name');

 	// receive MRR array------------------------------------------------
	$sql_receive_mrr = "select a.id as trid, a.transaction_type,b.recv_number, b.supplier_id 
			from inv_transaction a, inv_receive_master b
			where a.mst_id=b.id $item_desc_search_cond and a.transaction_type in (1,4) and a.item_category='$cbo_item_cat'"; 
			//echo $sql_receive_mrr;die;
	$result_rcv = sql_select($sql_receive_mrr);
	$receiveMRR=array();
	$trWiseReceiveMRR=array();
	$supplierReceiveMRR=array();
	foreach($result_rcv as $row)
	{
		$receiveMRR[$row[csf("trid")]."##".$row[csf("transaction_type")]] = $row[csf("recv_number")];
		$trWiseReceiveMRR[$row[csf("trid")]] = $row[csf("recv_number")];
		$supplierReceiveMRR[$row[csf("trid")]] = $row[csf("supplier_id")];
	}
	
	
	// issue MRR array------------------------------------------------		
	$sql_issue_mrr = "select a.id as trid,a.transaction_type,b.issue_number,b.issue_purpose,a.order_id	from inv_transaction a, inv_issue_master b
			where a.mst_id=b.id $item_desc_search_cond and a.transaction_type in (2,3) and a.item_category='$cbo_item_cat'";		 
	$result_iss = sql_select($sql_issue_mrr);
	$issueMRR=array();$issuePupose=array(); $issuePO=array();
	foreach($result_iss as $row)
	{
		$issueMRR[$row[csf("trid")]."##".$row[csf("transaction_type")]] = $row[csf("issue_number")];
		$issuePupose[$row[csf("trid")]] = $yarn_issue_purpose[$row[csf("issue_purpose")]]; 
		$issuePO[$row[csf("trid")]]['po'] .= $row[csf("order_id")].','; 
	}
	
	$sql_transfer = "select a.id as trid,a.transaction_type,b.transfer_system_id, b.company_id, b.to_company
			from inv_transaction a, inv_item_transfer_mst b
			where a.mst_id=b.id $item_desc_search_cond and a.transaction_type in (5,6) and a.item_category='$cbo_item_cat' and b.transfer_criteria in(1,2) and b.entry_form=57";		 //echo $sql_transfer;die;
	$result_trans = sql_select($sql_transfer);
	$transferMRR=array();$trWiseTransferWith=array();
	foreach($result_trans as $row)
	{
		$transferMRR[$row[csf("trid")]."##".$row[csf("transaction_type")]] = $row[csf("transfer_system_id")];
		if($row[csf("transaction_type")]==5)
		{
			$trWiseTransferWith[$row[csf("trid")]."##".$row[csf("transaction_type")]] = $row[csf("company_id")]; 
		}
		else
		{
			$trWiseTransferWith[$row[csf("trid")]."##".$row[csf("transaction_type")]] = $row[csf("to_company")]; 
		}
		
	} 
	//var_dump($issuePO);
	// var_dump($issuePupose);
	//array join or merge here ------------- do not delete or change
	$mrrArray = array();
	$mrrArray = $receiveMRR+$issueMRR+$transferMRR;
	
	//var_dump($mrrArray);
	?>
    <?
		//Master Query---------------------------------------------------- 
		/*$sql = "select a.*, b.product_name_details,b.unit_of_measure,b.lot,c.knit_dye_source,c.knit_dye_company,c.issue_purpose
				from inv_transaction a left join inv_issue_master c on a.mst_id=c.id and a.transaction_type in (2,3), product_details_master b
				where a.prod_id in ($txt_product_id) and a.prod_id=b.id and a.item_category=1 $search_cond order by a.transaction_date,a.prod_id ASC";*/ 
		if( $from_date!="" && $to_date!="" ) 
		{
			if($db_type==2) $from_date=date("j-M-Y",strtotime($from_date)); 
			if($db_type==0) $from_date=change_date_format($from_date, 'yyyy-mm-dd'); 
			//for opening balance
			$sqlTR = "select  prod_id, SUM(CASE WHEN transaction_type in (1,4,5) THEN cons_quantity ELSE 0 END) as receive,
			SUM(CASE WHEN transaction_type in (2,3,6) THEN cons_quantity ELSE 0 END) as issue,
			SUM(CASE WHEN transaction_type in (1,4,5) THEN cons_amount ELSE 0 END) as rcv_balance,
			SUM(CASE WHEN transaction_type in (2,3,6) THEN cons_amount ELSE 0 END) as iss_balance
			from inv_transaction
			where transaction_date < '".$from_date."' and status_active=1 and is_deleted=0 group by prod_id";
			$trResult = sql_select($sqlTR);
		}
		$opning_bal_arr=array();
		foreach($trResult as $row)
		{
			$opning_bal_arr[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$opning_bal_arr[$row[csf("prod_id")]]["receive"]=$row[csf("receive")];
			$opning_bal_arr[$row[csf("prod_id")]]["issue"]=$row[csf("issue")];
			$opning_bal_arr[$row[csf("prod_id")]]["rcv_balance"]=$row[csf("rcv_balance")];
			$opning_bal_arr[$row[csf("prod_id")]]["iss_balance"]=$row[csf("iss_balance")];
		}
                $store_arr = return_library_array("select id,store_name from lib_store_location", "id", "store_name");
                $cbo_store_name=str_replace("'","",$cbo_store_name);
		$store_cond="";
		if($cbo_store_name>0) $store_cond=" and a.store_id=$cbo_store_name";
                
		//var_dump($opning_bal_arr);die;
		if($cbo_method==0) //average rate #########################################################################
		{
			$sql = "select a.id, a.prod_id,a.store_id,a.store_id, a.transaction_date, a.transaction_type, a.cons_quantity, a.cons_rate, a.cons_amount, a.department_id, b.product_name_details, b.unit_of_measure, b.item_group_id, b.lot, c.knit_dye_source, c.knit_dye_company, c.issue_purpose
			from inv_transaction a left join inv_issue_master c on a.mst_id=c.id and a.transaction_type in (2,3), product_details_master b 
			where a.status_active=1 and a.is_deleted=0 $item_desc_search_cond and a.prod_id=b.id and b.entry_form<>24 and a.item_category='$cbo_item_cat' and a.company_id=$cbo_company_name $search_cond $store_cond order by  a.prod_id, a.id ASC";		
		}
		else if($cbo_method==1) //FIFU #########################################################################
		{
			$sql = "select a.id, a.prod_id,a.store_id, a.transaction_date, a.transaction_type, a.cons_quantity, a.cons_rate, a.cons_amount,  a.department_id, b.product_name_details, b.unit_of_measure, b.item_group_id, b.lot, c.knit_dye_source, c.knit_dye_company, c.issue_purpose
			from inv_transaction a left join inv_issue_master c on a.mst_id=c.id and a.transaction_type in (2,3), product_details_master b 
			where a.status_active=1 and a.is_deleted=0 $item_desc_search_cond and a.prod_id=b.id and b.entry_form<>24 and a.item_category='$cbo_item_cat' and a.company_id=$cbo_company_name $search_cond $store_cond order by  a.prod_id, a.transaction_date ASC";		
		}
		else if($cbo_method==2) //LIFU #########################################################################
		{
			$sql = "select a.id, a.prod_id,a.store_id, a.transaction_date, a.transaction_type, a.cons_quantity, a.cons_rate, a.cons_amount,  a.department_id, b.product_name_details, b.unit_of_measure, b.item_group_id, b.lot, c.knit_dye_source, c.knit_dye_company, c.issue_purpose
			from inv_transaction a left join inv_issue_master c on a.mst_id=c.id and a.transaction_type in (2,3), product_details_master b 
			where a.status_active=1 and a.is_deleted=0 and  $item_desc_search_cond and a.prod_id=b.id and b.entry_form<>24 and a.item_category='$cbo_item_cat' and a.company_id=$cbo_company_name $search_cond $store_cond order by  a.prod_id, a.transaction_date DESC";		
		}		
		//echo $sql;
		$result = sql_select($sql);	
		$checkItemArr=array();
		$balQnty=$balValue=array(); 
		$rcvQnty=$rcvValue=$issQnty=$issValue=0;
		$buyer_arr=return_library_array( "SELECT id, buyer_name from lib_buyer where status_active =1 and is_deleted=0",'id','buyer_name');
		$po_sql ="SELECT a.style_ref_no,a.job_no_prefix_num,a.buyer_name, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $poIdsCond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
			$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
			$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no_prefix_num")];
			$buyer_po_arr[$row[csf("id")]]['buyer']=$buyer_arr[$row[csf("buyer_name")]];
		}
		unset($po_sql_res);
		ob_start();	
		?>
    	
        	<table width="1900" border="0">
            	<tr class="form_caption" style="border:none;">
                    <td colspan="21" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?> </td> 
                </tr>
                <tr style="border:none;">
                    <td colspan="21" align="center" style="border:none; font-size:14px;">
                    Company Name : <? echo $companyArr[str_replace("'","",$cbo_company_name)]; ?>                                
                    </td>
                </tr>
                <tr style="border:none;">
                    <td colspan="21" align="center" style="border:none;font-size:12px; font-weight:bold">
                    <? if($from_date!="" || $to_date!="")echo "From ".change_date_format($from_date)." To ".change_date_format($to_date)."" ;?>
                    </td>
                </tr>
                <tr>
                    <td colspan="21" align="center"><b>Weighted Average Method</b></td>
                </tr>
            </table>
            <table width="1900" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" > 
                <thead>
                    <tr>
                        <th width="50" rowspan="2">SL</th>
                        <th width="100" rowspan="2">Store Name</th>
                        <th width="80" rowspan="2">Trans Date</th>
                        <th width="120" rowspan="2">Trans Ref No</th>
                        <th width="100" rowspan="2">Trans Type</th>
                        <th width="100" rowspan="2">Buyer</th>
                        <th width="100" rowspan="2">Style</th>
                        <th width="100" rowspan="2">Job</th>
                        <th width="100" rowspan="2">PO</th>
                        <th width="100" rowspan="2">Purpose</th>
                        <th width="100" rowspan="2">Department</th>
                        <th width="100" rowspan="2">Trans With</th>
                        <th colspan="3">Receive</th>
                        <th colspan="3">Issue</th>
                        <th colspan="3">Balance</th>                    
                        </tr>
                        <tr>
                        <th width="80">Qnty</th>
                        <th width="60">Rate</th>
                        <th width="110">Value</th>
                        <th width="80">Qnty</th>
                        <th width="60">Rate</th>
                        <th width="110">Value</th>
                        <th width="80">Qnty</th>
                        <th width="60">Rate</th>
                        <th width="110">Value</th>
                    </tr>
                </thead>
            </table>  
        <div style="width:1900px; overflow-y:scroll; max-height:250px" id="scroll_body" > 
            <table width="1880" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body"> 
                <tbody>
                <?		
                $i=1;$m=1;$product_id_arr=array();$k=1;
                foreach($result as $row)
                {
					$pro_id=$row[csf("prod_id")];
					//check items new or not and print product description-------------------
					if(!in_array($row[csf("prod_id")],$checkItemArr))
					{
					
						if($i!=1) // product wise sum/total here------------
						{
							?>                                
							<tr class="tbl_bottom">
                                <td colspan="12" align="right">Total</td>
                                <td><? echo number_format($rcvQnty,2); ?></td><td></td><td><? echo number_format($rcvValue,2); ?></td>
                                <td><? echo number_format($issQnty,2); ?></td><td></td><td><? echo number_format($issValue,2); ?></td>                                    
                                <td>&nbsp;</td><td></td><td>&nbsp;</td>
                            </tr>
								
								<!-- product wise herder -->
                            <tr>
                            	<td colspan="16"><b>Product ID : <? echo $row[csf("prod_id")]." , ".$row[csf("product_name_details")].", Item Group# ".$item_group_arr[$row[csf("item_group_id")]].", Item Category#  ".$general_item_category[$cbo_item_cat].", UOM# ".$unit_of_measurement[$row[csf("unit_of_measure")]]; ?></b></td>
                            </tr>
								<!-- product wise herder END -->
							<?
						}
						
						
						//opening balance query-----------
						/*if( $from_date!="" && $to_date!="" ) 
						{
						if($db_type==2) $from_date=date("j-M-Y",strtotime($from_date)); 
						if($db_type==0) $from_date=change_date_format($from_date, 'yyyy-mm-dd'); 
						//for opening balance
						$sqlTR = "select prod_id, SUM(CASE WHEN transaction_type in (1,4) THEN cons_quantity ELSE 0 END) as receive,
						SUM(CASE WHEN transaction_type in (2,3) THEN cons_quantity ELSE 0 END) as issue,
						SUM(CASE WHEN transaction_type in (1,4) THEN cons_amount ELSE 0 END) as rcv_balance,
						SUM(CASE WHEN transaction_type in (2,3) THEN cons_amount ELSE 0 END) as iss_balance
						from inv_transaction
						where prod_id in ($pro_id) and transaction_date < '".$from_date."' and status_active=1 and is_deleted=0 group by prod_id,id";
						$trResult = sql_select($sqlTR);
						}*/
						//echo $sqlTR ;die;		
						
						$flag=0;
						$opening_qnty=$opening_balance=$opening_rate=0;
						if($opning_bal_arr[$pro_id]['prod_id']!="")
						{
							?>
							
							<tr style="background-color:#FFFFCC">
								<td colspan="12" align="right"><b>Opening Balance</b></td>  
								<?
								$opening_qnty = $opning_bal_arr[$pro_id]['receive']- $opning_bal_arr[$pro_id]['issue'];
								$opening_balance = $opning_bal_arr[$pro_id]['rcv_balance'] - $opning_bal_arr[$pro_id]['iss_balance'];
								$opening_rate = $opening_balance/$opening_qnty;
								?>
								<td width="80" align="right"><? echo number_format($opening_qnty,2); ?></td>
								<td width="60" align="right"><? echo number_format($opening_rate,2); ?></td>
								<td width="" align="right"><? echo number_format($opening_balance,2); ?></td>              
							</tr>
							<?
							$balQnty[$opning_bal_arr[$pro_id]['prod_id']] = $opening_qnty;
							$balValue [$opning_bal_arr[$pro_id]['prod_id']]= $opening_balance;
							
							$flag=1;
							$opening_qnty=0;
							$opening_balance=0;
						} // end opening balance foreach 	
						
						$checkItemArr[$row[csf("prod_id")]]=$row[csf("prod_id")];
						$rcvQnty=$rcvValue=$issQnty=$issValue=0; // initialize variable
						//$balQnty=$balValue=0;	
						$total_balQnty=0;$total_balValue=0;								
					
					}
					//var_dump($balQnty);							
					
					//print product name details header---------------------------
					if($i==1)
					{
						?> 
                            <tr>
                            	<td colspan="16"><b>Product ID : <? echo $row[csf("prod_id")]." , ".$row[csf("product_name_details")].", Item Group# ".$item_group_arr[$row[csf("item_group_id")]].", Item Category#  ".$general_item_category[$cbo_item_cat].", UOM# ".$unit_of_measurement[$row[csf("unit_of_measure")]]; ?></b></td>
                            </tr>
						<?
					}
					//print product name details header END -------------------------	
					
					
					/*if($flag==1) // adjust opening balance
					{
					$balQnty = $balQnty+$opening_qnty;
					$balValue = $balValue+$opening_balance;
					}
					else
					{
					$flag=0;
					}*/
					
					
					if ($i%2==0)$bgcolor="#E9F3FF";	else $bgcolor="#FFFFFF"; 
					if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) 
					$stylecolor='style="color:#A61000"';
					else
					$stylecolor='style="color:#000000"';
					//var_dump($balQnty); 
					/*if(!in_array($row[csf("prod_id")],$each_pro_id))
					{*/
					if(!in_array($row[csf("prod_id")],$product_id_arr))
					{
						$k=1;										
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="50" align="center" title="<?= "trans id ".$row[csf("id")]?>"><? echo $k; ?></td>
                            <td width="100"><p><? echo $store_arr[$row[csf("store_id")]]; ?></p></td>
                            <td width="80" align="center"><? if($row[csf("transaction_date")] !="" && $row[csf("transaction_date")] !="0000-00-00")  echo change_date_format($row[csf("transaction_date")]); ?></td>                                 
                            <td width="120"><p><? echo $mrrArray[$row[csf("id")]."##".$row[csf("transaction_type")]]; ?></p></td>
                            <td width="100"><p><? echo $transaction_type[$row[csf("transaction_type")]]; ?></p></td>
                            <td width="100"><p> &nbsp;</p></td>
                            <td width="100"><p>&nbsp;</p></td>
                            <td width="100"><p>&nbsp;</p></td>
                            <td width="100"><p>&nbsp;</p></td>

                            <td width="100"><p><? if($row[csf("transaction_type")]==2) echo $general_issue_purpose[$row[csf("issue_purpose")]]; ?></p></td>

                            <td width="100"><p><? echo $department_arr[$row[csf("department_id")]]; ?></p></td>
                            
                            <?
                            if($row[csf("transaction_type")]==2)
                            {
								if($row[csf("knit_dye_source")]==1)
								$transactionWith =  $companyArr[$row[csf("knit_dye_company")]]; 
								else  	
								$transactionWith =  $supplierArr[$row[csf("knit_dye_company")]];
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
                            
                            <td width="100"><p><? echo $transactionWith; ?></p></td> 
                            <td width="80" align="right"><? if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) echo number_format($row[csf("cons_quantity")],4); ?></td>
                            <td width="60" align="right"><? if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) echo number_format($row[csf("cons_rate")],4); ?></td>
                            <td width="110" align="right"><? if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) echo number_format($row[csf("cons_amount")],2); ?></td>              
                            
                            <td width="80" align="right"><? if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) echo number_format($row[csf("cons_quantity")],4); ?></td>
                            <td width="60" align="right"><? if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3  || $row[csf("transaction_type")]==6) echo number_format($row[csf("cons_rate")],4); ?></td>
                            <td width="110" align="right"><? if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) echo number_format($row[csf("cons_amount")],2); ?></td>
                            <?
                            $each_pro_id=array();
                            
                            
                            if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) $total_balQnty =$balQnty[$row[csf("prod_id")]]+ $row[csf("cons_quantity")]; 
                            if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) $total_balQnty =$balQnty[$row[csf("prod_id")]]-$row[csf("cons_quantity")]; 
                            
                            if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5)  $total_balValue =$balValue[$row[csf("prod_id")]]+ $row[csf("cons_amount")];
                            if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6)  $total_balValue =$balValue[$row[csf("prod_id")]]- $row[csf("cons_amount")];
                            
                            //if($m==1) $total_balQnty= $total_balQnty+$balQnty[$row[csf("prod_id")]]; else $total_balQnty+=$total_balQnty;
                            //if($m==1) $total_balValue= $total_balValue+$balValue[$row[csf("prod_id")]]; else $total_balValue+=$total_balValue;
                            
                            $total_balQnty=number_format($total_balQnty,4,'.','');
                            $total_balValue=number_format($total_balValue,2,'.','');
                            if($total_balQnty<0.00009)
                            {
                            $bal_rate=0;
                            $total_balValue=0.00;
                            }
                            else 
                            {
                            $bal_rate=$total_balValue/$total_balQnty;
                            }
                            ?> 
                            <td width="80" align="right"><? echo $total_balQnty; ?></td>
                            <td width="60" align="right" title="<?= $bal_rate;?>"><? echo number_format($bal_rate,4,'.',''); ?></td>
                            <td width="90" align="right"><? echo number_format($total_balValue,2,'.',''); ?></td>              
						</tr>
						<?
						$k++; 
						$product_id_arr[]=$row[csf("prod_id")];
					}
					else
					{
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="50" align="center" title="<?= "trans id ".$row[csf("id")]?>"><? echo $k; ?></td>
                            <td width="100"><p><? echo $store_arr[$row[csf("store_id")]]; ?></p></td>
                            <td width="80" align="center"><? if($row[csf("transaction_date")] !="" && $row[csf("transaction_date")] !="0000-00-00")  echo change_date_format($row[csf("transaction_date")]); ?></td>                                 
                            <td width="120"><p><? echo $mrrArray[$row[csf("id")]."##".$row[csf("transaction_type")]]; ?></p></td>
                            <td width="100"><p><? echo $transaction_type[$row[csf("transaction_type")]]; ?></p></td>
                            <td width="100"><p><? 
                            $buyer_po=$buyer_style=$buyer_job=$buyer_buyer=''; 
        					$buyer_po_id=explode(",",$issuePO[$row[csf("id")]]['po']);
							foreach($buyer_po_id as $po_id)
							{
								if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
								if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
								if($buyer_job=="") $buyer_job=$buyer_po_arr[$po_id]['job']; else $buyer_job.=','.$buyer_po_arr[$po_id]['job'];
								if($buyer_buyer=="") $buyer_buyer=$buyer_po_arr[$po_id]['buyer']; else $buyer_buyer.=','.$buyer_po_arr[$po_id]['buyer'];
							}
							$buyer_po=chop(implode(",",array_unique(explode(",",$buyer_po))),',');
							$buyer_style=chop(implode(",",array_unique(explode(",",$buyer_style))),',');
							$buyer_job=chop(implode(",",array_unique(explode(",",$buyer_job))),',');
							$buyer_buyer=chop(implode(",",array_unique(explode(",",$buyer_buyer))),',');
                           	echo $buyer_buyer; ?></p></td>
                            <td width="100"><p><? echo $buyer_style; ?></p></td>
                            <td width="100"><p><? echo $buyer_job; ?></p></td>
                            <td width="100"><p><? echo $buyer_po; ?></p></td>


                            <td width="100"><p><?  if($row[csf("transaction_type")]==2) echo $general_issue_purpose[$row[csf("issue_purpose")]]; ?></p></td>

                            <td width="100"><p><? echo $department_arr[$row[csf("department_id")]]; ?></p></td>
                            
                            <? 										
                            if($row[csf("transaction_type")]==2)
                            {
								if($row[csf("knit_dye_source")]==1)
								$transactionWith =  $companyArr[$row[csf("knit_dye_company")]]; 
								else  	
								$transactionWith =  $supplierArr[$row[csf("knit_dye_company")]];
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
                            
                            <td width="100"><p><? echo $transactionWith; ?></p></td> 
                            <td width="80" align="right"><? if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) echo number_format($row[csf("cons_quantity")],4); ?></td>
                            <td width="60" align="right"><? if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) echo number_format($row[csf("cons_rate")],4); ?></td>
                            <td width="110" align="right"><? if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) echo number_format($row[csf("cons_amount")],2); ?></td>              
                            
                            <td width="80" align="right"><? if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) echo number_format($row[csf("cons_quantity")],4); ?></td>
                            <td width="60" align="right"><? if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) echo number_format($row[csf("cons_rate")],4); ?></td>
                            <td width="110" align="right"><? if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) echo number_format($row[csf("cons_amount")],2); ?></td>
                            <?
                            $each_pro_id=array();
                            
                            
                            if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) $total_balQnty +=$row[csf("cons_quantity")]; 
                            if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) $total_balQnty -=$row[csf("cons_quantity")]; 
                            
                            if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5)  $total_balValue += $row[csf("cons_amount")];
                            if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6)  $total_balValue -= $row[csf("cons_amount")];
                            
                            //if($m==1) $total_balQnty= $total_balQnty+$balQnty[$row[csf("prod_id")]]; else $total_balQnty+=$total_balQnty;
                            //if($m==1) $total_balValue= $total_balValue+$balValue[$row[csf("prod_id")]]; else $total_balValue+=$total_balValue;
                            $total_balQnty=number_format($total_balQnty,4,'.','');
                            $total_balValue=number_format($total_balValue,2,'.','');
                            if($total_balQnty< 0.00009)
                            {
                            $bal_rate=0;
                            $total_balValue=0.00;
                            }
                            else 
                            {
                            $bal_rate=$total_balValue/$total_balQnty;
                            }
                            ?> 
                            <td width="80" align="right"><? echo $total_balQnty; ?></td>
                            <td width="60" align="right" title="<?= $bal_rate;?>"><? echo number_format($bal_rate,4,'.',''); ?></td>
                            <td width="90" align="right"><? echo number_format($total_balValue,2,'.',''); ?></td>              
						</tr>
						<?
						$k++;
					}
					//$total_balQnty=0;
					//$total_balValue=0;
					$i++;
					//total sum START-----------------------
					if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4  || $row[csf("transaction_type")]==5) $rcvQnty += $row[csf("cons_quantity")];
					if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4  || $row[csf("transaction_type")]==5) $rcvValue += $row[csf("cons_amount")];
					
					if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) $issQnty += $row[csf("cons_quantity")];
					if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) $issValue += $row[csf("cons_amount")];
					
					/*		//total sum END-----------------------
					$each_pro_id[$row[csf("prod_id")]]=$row[csf("prod_id")];
					$m++;
					}
					$total_balQnty=0;
					$total_balValue=0;*/
                
                
                } 
				?> <!---- END FOREACH LOOP----->
                    <tr class="tbl_bottom">
                        <td colspan="12" align="right">Total</td>
                        <td align="right" ><? echo number_format($rcvQnty,4); ?></td>
                        <td></td>
                        <td align="right" ><? echo number_format($rcvValue,2); ?></td>
                        <td align="right" ><? echo number_format($issQnty,4); ?></td>
                        <td></td>
                        <td align="right" ><? echo number_format($issValue,2); ?></td>                                    
                        <td>&nbsp;</td>
                        <td></td>
                        <td>&nbsp;</td>
                    </tr> 
                </tbody>  
            </table> 
        </div>
          
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


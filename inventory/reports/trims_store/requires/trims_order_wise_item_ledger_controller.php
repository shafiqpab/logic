﻿<?
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

if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 130, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data' and b.category_type=4 and a.status_active=1 and a.is_deleted=0 $store_location_credential_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", 0, "" );
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
			show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $company; ?>, 'create_lot_search_list_view', 'search_div', 'trims_order_wise_item_ledger_controller', 'setFilterGrid("list_view",-1)');
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
				field_type='<? echo create_drop_down( "txt_search_common", 150,"select id,item_name  from lib_item_group where item_category=4 and status_active=1 and is_deleted=0 order by item_name","id,item_name", 1, "-- Select --", "", "","","","","",""); ?>';
				$('#search_by_td_up').html('Enter Item Group');
			}
			$('#search_by_td').html(field_type);
		}
    </script>
    <body>
	<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<table width="500" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
				<thead>
					<tr>                	 
						<th>Search By</th>
						<th align="center" width="200" id="search_by_td_up">Enter Item Description</th>
 						<th>
                       		<input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  />
                            <input type='hidden' id='txt_selected_id' />
							<input type='hidden' id='txt_selected' />
							<input type='hidden' id='txt_selected_no' />
                        </th>
					</tr>
				</thead>
				<tbody>
					<tr align="center">
						<td align="center">
							<?  
								$search_by = array(1=>'Item Description', 2=>'Item Group');
								$dd="";
								echo create_drop_down( "cbo_search_by", 150, $search_by, "", 0, "--Select--", "", "fn_item_search(this.value);", 0);
							?>
						</td>
						<td width="180" align="center" id="search_by_td">				
							<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td> 
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="fn_check_lot()" style="width:100px;" />				
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
$item_group_arr=return_library_array( "select id, item_name from  lib_item_group",'id','item_name');
if($action=="create_lot_search_list_view")
{
 	$ex_data = explode("_",$data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = trim($ex_data[1]);
	$company = $ex_data[2];
	
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
	
 	$sql = "select id,item_group_id,item_description from product_details_master where company_id=$company and item_category_id =4 $sql_cond"; 
	$arr=array(1=>$item_group_arr);
	echo create_list_view("list_view", "Product Id, Item Group, Item Description","70,160","500","260",0, $sql , "js_set_value", "id,item_description", "", 1, "0,item_group_id,0", $arr, "id,item_group_id,item_description", "","","0","",1) ;	
	
	exit();	
}

//Order Search-----------------------------//
if($action=="order_no_popup")
{
	echo load_html_head_contents("Order Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST); 
	?>
	<script>
		var selected_id = new Array; var selected_name = new Array;
		
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			
			if (str!="") str=str.split("_");
			// alert(str[0]);
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			 
			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_po_id').val( id );
			$('#hide_po_no').val( name );
		}
	
    </script>
	</head>

	<body>
	<div align="center">
		<form name="order_search_form" id="order_search_form">
			<fieldset style="width:580px;">
	            <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
	            	<thead>
	                    <th>Job Year</th>
	                    <th>Job No</th>
	                    <th>Order No</th>
	                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
	                    <input type="hidden" name="hide_po_no" id="hide_po_no" value="" />
	                    <input type="hidden" name="hide_po_id" id="hide_po_id" value="" />
	                </thead>
	                <tbody>
	                	<tr>
	                        <td align="center">
	                        	<? 
									echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
								?>
	                        </td>                 
	                        <td align="center">	
	    						<input type="text" style="width:130px" class="text_boxes" name="txt_job_no" id="txt_job_no" />	
	                        </td>     
	                        <td align="center" id="search_by_td">				
	                            <input type="text" style="width:130px" class="text_boxes" name="txt_order_no" id="txt_order_no" />	
	                        </td> 	
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_year').value+'**'+document.getElementById('txt_job_no').value+'**'+document.getElementById('txt_order_no').value, 'create_order_no_search_list_view', 'search_div', 'trims_order_wise_item_ledger_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
	                    </td>
	                    </tr>
	            	</tbody>
	           	</table>
	            <div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit(); 
}

if($action=="create_order_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[1];
	$job_no=$data[2];
	$order_no=$data[3];

	$job_no_cond=$order_no_cond="";
	if(trim($job_no))
	{
		$job_no_cond=" AND a.job_no_prefix_num like '%$job_no%'";
	}

	if(trim($order_no))
	{
		$order_no_cond=" AND b.po_number like '%$order_no%'";
	}

	if($db_type==0) $year_field=" YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="  to_char(a.insert_date,'YYYY') as year";

	if($db_type==0) $year_field_by="and YEAR(a.insert_date)"; 
	else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";

	$year_cond="";
	if($year_id!=0) { $year_cond=" $year_field_by=$year_id"; }	
		
	$sql="SELECT a.job_no_prefix_num, $year_field, b.id, b.po_number FROM wo_po_details_master a, wo_po_break_down b, order_wise_pro_details c WHERE a.job_no=b.job_no_mst AND b.id=c.po_breakdown_id AND c.entry_form in (24,25,49,73,78,112) $year_cond $job_no_cond $order_no_cond AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND c.status_active=1 AND c.is_deleted=0 GROUP BY a.job_no_prefix_num, a.insert_date, b.id, b.po_number ORDER BY b.id DESC";
	//echo $sql; die;	
	
	echo create_list_view("tbl_list_search", "Year,Job No,Order No", "100,100,120","400","240",0, $sql , "js_set_value", "id,po_number", "", 1, "0,0,0", $arr , "year,job_no_prefix_num,po_number", "",'','0,0,0','',1) ;
	
   exit(); 
} 

//library array-------------------
$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name"); 
$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name"); 



//report generated here--------------------//
if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$search_cond="";
	if($from_date!="" && $to_date!="")
	{
		if($db_type==0) {
			$search_cond .= " and a.transaction_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
		}
		else {
			$search_cond .= " and a.transaction_date  between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";
		}
	}

	$po_breakdown_id_conds="";
	if($txt_po_id)
	{
		$po_breakdown_id_conds=" and b.po_breakdown_id in ($txt_po_id) ";
	}
 	 
 	// receive MRR array------------------------------------------------
	$sql_receive_mrr = "SELECT a.id as trid, a.transaction_type, b.recv_number, b.supplier_id from inv_transaction a, inv_receive_master b where b.company_id=$cbo_company_name and a.prod_id in ($txt_product_id) and a.mst_id=b.id and a.transaction_type in (1,4) and a.item_category=4"; 
	$result_rcv = sql_select($sql_receive_mrr);
	$receiveMRR=array();
	$rcvSupplier=array();
	foreach($result_rcv as $row)
	{
		$receiveMRR[$row[csf("trid")].$row[csf("transaction_type")]] = $row[csf("recv_number")];
		$rcvSupplier[$row[csf("trid")]] = $row[csf("supplier_id")];
	}
	
	// issue MRR array------------------------------------------------		
	$sql_issue_mrr = "SELECT a.id as trid,a.transaction_type,b.issue_number,b.issue_purpose, b.knit_dye_source, b.knit_dye_company from inv_transaction a, inv_issue_master b where b.company_id=$cbo_company_name and a.prod_id in ($txt_product_id) and a.mst_id=b.id and a.transaction_type in (2,3) and a.item_category=4";		 
	$result_iss = sql_select($sql_issue_mrr);
	$issueMRR=$issuePupose=$issue_mst_arr=array();
	foreach($result_iss as $row)
	{
		$issueMRR[$row[csf("trid")].$row[csf("transaction_type")]] = $row[csf("issue_number")];
		$issuePupose[$row[csf("trid")]] = $yarn_issue_purpose[$row[csf("issue_purpose")]]; 
		$issue_mst_arr[$row[csf("trid")]]["knit_dye_source"]=$row[csf("knit_dye_source")];
		$issue_mst_arr[$row[csf("trid")]]["knit_dye_company"]=$row[csf("knit_dye_company")];
	} 
	
	// transfer MRR array------------------------------------------------
	$sql_transfer_mrr = "SELECT a.id as trid, a.transaction_type, b.transfer_system_id  from inv_transaction a, inv_item_transfer_mst b where b.company_id=$cbo_company_name and a.prod_id in ($txt_product_id) and a.mst_id=b.id and a.transaction_type in (5,6) and a.item_category=4 and b.entry_form=78"; 
	$transfer_rcv = sql_select($sql_transfer_mrr);
	$transferMRR=array();
	foreach($transfer_rcv as $row)
	{
		$transferMRR[$row[csf("trid")].$row[csf("transaction_type")]] = $row[csf("transfer_system_id")];
	}

	//array join or merge here ------------- will not change
	$mrrArray = array();
	$mrrArray = $receiveMRR+$issueMRR+$transferMRR; 
	?>

    <fieldset>
    <?
		//Master Query---------------------------------------------------- 

    	//Making Opening Balance if exists===============================
    	$opning_bal_arr=array();
		if( $from_date!="" && $to_date!="" ) 
		{
			if($db_type==2) $from_date=date("j-M-Y",strtotime($from_date)); 
			if($db_type==0) $from_date=change_date_format($from_date, 'yyyy-mm-dd'); 

			$sqlTR = "SELECT  a.prod_id, b.po_breakdown_id as order_id, 
					 SUM(CASE WHEN b.trans_type in (1,4,5) THEN b.quantity ELSE 0 END) as receive, 
					 SUM(CASE WHEN b.trans_type in (2,3,6) THEN b.quantity ELSE 0 END) as issue, 
					 SUM(CASE WHEN b.trans_type in (1,4,5) THEN b.order_amount ELSE 0 END) as rcv_balance, 
					 SUM(CASE WHEN b.trans_type in (2,3,6) THEN b.order_amount ELSE 0 END) as iss_balance 
					 from inv_transaction a, order_wise_pro_details b 
					 where a.company_id=$cbo_company_name $po_breakdown_id_conds and a.prod_id in ($txt_product_id) and a.id=b.trans_id and b.trans_type in (1,2,3,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.entry_form in (24,25,49,73,78,112) and b.status_active=1 and b.is_deleted=0 and a.transaction_date < '".$from_date."' 
					 group by a.prod_id, b.po_breakdown_id";
			$trResult = sql_select($sqlTR);
		}
		foreach($trResult as $row)
		{
			$opning_bal_arr[$row[csf("prod_id")]][$row[csf("order_id")]]["prod_id"]=$row[csf("prod_id")];
			$opning_bal_arr[$row[csf("prod_id")]][$row[csf("order_id")]]["receive"]=$row[csf("receive")];
			$opning_bal_arr[$row[csf("prod_id")]][$row[csf("order_id")]]["issue"]=$row[csf("issue")];
			$opning_bal_arr[$row[csf("prod_id")]][$row[csf("order_id")]]["rcv_balance"]=$row[csf("rcv_balance")];
			$opning_bal_arr[$row[csf("prod_id")]][$row[csf("order_id")]]["iss_balance"]=$row[csf("iss_balance")];
		}
		
		if($cbo_method==0 || $cbo_method==1) // 0 = average rate, 1 = FIFU
		{
			$order_by_cond="order by a.prod_id, b.po_breakdown_id, a.id ASC";
		}
		else if($cbo_method==2) //LIFU
		{
			$order_by_cond="order by a.prod_id, b.po_breakdown_id, a.transaction_date DESC";
		}
		//The main Query=========================================
		$sql="SELECT a.id, a.prod_id, a.transaction_date, b.po_breakdown_id as order_id, b.trans_type as transaction_type, b.quantity as cons_quantity, b.order_rate as cons_rate, b.order_amount as cons_amount, c.product_name_details, c.unit_of_measure, c.item_group_id, c.lot 
		from inv_transaction a, order_wise_pro_details b , product_details_master c 
		where a.id=b.trans_id and b.prod_id=c.id and a.prod_id=c.id and a.company_id=$cbo_company_name and a.transaction_type in (1,2,3,4,5,6) and b.trans_type in (1,2,3,4,5,6) and b.entry_form in (24,25,49,73,78,112) and a.prod_id in ($txt_product_id) $po_breakdown_id_conds and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_cond $order_by_cond"; 
		//echo $sql;die;
		$result = sql_select($sql);	


		$po_sql="SELECT b.id, b.po_number from WO_PO_DETAILS_MASTER a, WO_PO_BREAK_DOWN b where a.job_no=b.job_no_mst and a.company_name=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		$po_sql_result=sql_select($po_sql);
		foreach ($po_sql_result as $data) 
		{
			$po_num_arr[$data[csf("id")]]=$data[csf("po_number")];
		}
		unset($po_sql_result);
		$checkItemArr=$balQnty=$balValue=array(); 
		$rcvQnty=$rcvValue=$issQnty=$issValue=0;
		$i=1;
		ob_start();	
		?>
    	
        <div> 
			<table style="width:1330px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" > 
            	<thead>
                	<tr class="form_caption" style="border:none;">
                        <td colspan="15" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?> </td> 
                    </tr>
                    <tr style="border:none;">
                            <td colspan="15" align="center" style="border:none; font-size:14px;">
                                Company Name : <? echo $companyArr[str_replace("'","",$cbo_company_name)]; ?>                                
                            </td>
                    </tr>
                    <tr style="border:none;">
                            <td colspan="15" align="center" style="border:none;font-size:12px; font-weight:bold">
                                <? if($from_date!="" || $to_date!="")echo "From ".change_date_format($from_date)." To ".change_date_format($to_date)."" ;?>
                            </td>
                    </tr>
                    <tr>
                        <td colspan="6">&nbsp;</td>
                        <td colspan="9" align="center"><b>Weighted Average Method</b></td>
                    </tr> 
                    <tr>
                        <th width="50" rowspan="2">SL</th>
                        <th width="80" rowspan="2">Trans Date</th>
                        <th width="120" rowspan="2">Trans Ref No</th>
                        <th width="100" rowspan="2">Trans Type</th>
                        <th width="100" rowspan="2">Purpose</th>
                        <th width="100" rowspan="2">Trans With</th>
                        <th width="" colspan="3">Receive</th>
                        <th width="" colspan="3">Issue</th>
                        <th width="" colspan="3">Balance</th>                    
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
                      <th width="">Value</th>
                  </tr>
                </thead>
           </table>  
          <div style="width:1330px; overflow-y:scroll; max-height:250px" id="scroll_body" > 
          	<table style="width:1310px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body"  >   
						<?		
							$m=1;$product_id_arr=array();$k=1;

							foreach($result as $row)
							{
								$pro_id=$row[csf("prod_id")];
								$order_id=$row[csf("order_id")];
								 
								//check items new or not and print product description-------------------
								if(!in_array($row[csf("prod_id")]."*".$order_id,$checkItemArr))
								{
									
									if($i!=1) // product and order wise sum/total here------------
									{
										?>                                
										<tr class="tbl_bottom">
											<td colspan="6" align="right">Total</td>
											<td><? echo number_format($rcvQnty,2); ?></td>
											<td></td>
											<td><? echo number_format($rcvValue,2); ?></td>
											<td><? echo number_format($issQnty,2); ?></td>
											<td></td>
											<td><? echo number_format($issValue,2); ?></td>                                    
											<td>&nbsp;</td><td></td><td>&nbsp;</td>
										</tr>
										
										<!-- product and order wise header -->
										<thead>
											<tr>
												<td colspan="9"><b>Product ID : <? echo $row[csf("prod_id")]." , Order No: ".$po_num_arr[$order_id]." , ".$row[csf("product_name_details")].", Item Group# ".$item_group_arr[$row[csf("item_group_id")]].", UOM# ".$unit_of_measurement[$row[csf("unit_of_measure")]]; ?></b></td>
												<td colspan="6" align="center">&nbsp;</td>
											</tr>
										</thead>
										<!-- product wise herder END -->
										<?
									}	
								
									$flag=0;
									$opening_qnty=$opening_balance=$opening_rate=0;
									if($opning_bal_arr[$pro_id][$order_id]['prod_id']!="")
									{
										?>
										<tr style="background-color:#FFFFCC">
											<td colspan="12" align="right"><b>Opening Balance</b></td>  
											<?
											$opening_qnty = $opning_bal_arr[$pro_id][$order_id]['receive']- $opning_bal_arr[$pro_id][$order_id]['issue'];
											$opening_balance = $opning_bal_arr[$pro_id][$order_id]['rcv_balance'] - $opning_bal_arr[$pro_id][$order_id]['iss_balance'];
											$opening_rate = $opening_balance/$opening_qnty;
											?>
											<td width="80" align="right"><? echo number_format($opening_qnty,2); ?></td>
											<td width="60" align="right"><? echo number_format($opening_rate,2); ?></td>
											<td width="" align="right"><? echo number_format($opening_balance,2); ?></td>              
										</tr>
										
										<?
										$balQnty[$opning_bal_arr[$pro_id][$order_id]['prod_id'].$order_id] = $opening_qnty;
										$balValue [$opning_bal_arr[$pro_id][$order_id]['prod_id'].$order_id]= $opening_balance;
										
										$flag=1;
										$opening_qnty=0;
										$opening_balance=0;
									} // end opening balance foreach 	
									
									$checkItemArr[$row[csf("prod_id")]."*".$order_id]=$row[csf("prod_id")]."*".$order_id;
									$rcvQnty=$rcvValue=$issQnty=$issValue=0; // initialize variable
									$total_balQnty=0;$total_balValue=0;								
								}							
								
								//print product name details header---------------------------
								if($i==1)
								{
									?> 
                                    <thead>
										<tr>
											<td colspan="9"><b>Product ID : <? echo $row[csf("prod_id")]." , Order No: ".$po_num_arr[$order_id]." , ".$row[csf("product_name_details")].", Item Group# ".$item_group_arr[$row[csf("item_group_id")]].", UOM# ".$unit_of_measurement[$row[csf("unit_of_measure")]]; ?></b></td>
											<td colspan="6" align="center"></td>
										</tr>
									</thead> 
									<?
								}

								//print product name details header END -------------------------	
								
								if ($i%2==0)$bgcolor="#E9F3FF";	else $bgcolor="#FFFFFF"; 
								if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) 
									$stylecolor='style="color:#A61000"';
								else
									$stylecolor='style="color:#000000"';

								if(!in_array($row[csf("prod_id")]."*".$order_id,$product_id_arr))
								{
									$k=1;										
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="50"><? echo $k; ?></td>								
										<td width="80">
											<? 
												if($row[csf("transaction_date")] !="" && $row[csf("transaction_date")] !="0000-00-00")  
													echo change_date_format($row[csf("transaction_date")]); ?>
										</td>                                 
										<td width="120"><p><? echo $mrrArray[$row[csf("id")].$row[csf("transaction_type")]]; ?></p></td>
										<td width="100"><p><? echo $transaction_type[$row[csf("transaction_type")]]; ?></p></td>
										<td width="100"><p><? echo $issuePupose[$row[csf("id")]]; ?></p></td>
										<?
										if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 )
										{
											$knit_dye_source=$issue_mst_arr[$row[csf("id")]]["knit_dye_source"];
											$knit_dye_company=$issue_mst_arr[$row[csf("id")]]["knit_dye_company"];

											if($knit_dye_source==1)
												$transactionWith =  $companyArr[$knit_dye_company]; 
											else  	
												$transactionWith =  $supplierArr[$knit_dye_company];
										}
										else
										{
											$transactionWith =  $supplierArr[$rcvSupplier[$row[csf("id")]]];
										}
	 									?>
	                                    <td width="100"><p><? echo $transactionWith; ?></p></td> 
	                                    <td width="80" align="right">
	                                    	<? 
	                                    		if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) 
	                                    		echo number_format($row[csf("cons_quantity")],2); 
	                                    	?>
	                                    </td>
										<td width="60" align="right">
											<? 
												if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) 
												echo number_format($row[csf("cons_rate")],2); 
											?>
										</td>
										<td width="110" align="right">
											<? 
												if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4|| $row[csf("transaction_type")]==5) 
												echo number_format($row[csf("cons_amount")],2); 
											?>
										</td>              
										
										<td width="80" align="right">
											<? 
												if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) 
												echo number_format($row[csf("cons_quantity")],2); 
											?>
										</td>
										<td width="60" align="right">
											<? 
												if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) 
												echo number_format($row[csf("cons_rate")],2); 
											?>
										</td>
										<td width="110" align="right">
											<? 
												if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) 
												echo number_format($row[csf("cons_amount")],2); 
											?>
										</td>
										<?
										$each_pro_id=array();																	
										if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) 
											$total_balQnty =$balQnty[$row[csf("prod_id")].$order_id]+ $row[csf("cons_quantity")]; 

										if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) 
											$total_balQnty =$balQnty[$row[csf("prod_id")].$order_id]-$row[csf("cons_quantity")]; 
										
										if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5)  
											$total_balValue =$balValue[$row[csf("prod_id")].$order_id]+ $row[csf("cons_amount")];

										if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6)  
											$total_balValue =$balValue[$row[csf("prod_id")].$order_id]- $row[csf("cons_amount")];
										
										
										
										?> 

										<td width="80" align="right"><? echo number_format($total_balQnty,2); ?></td>
										<td width="60" align="right"><? if($total_balQnty>0 && $total_balValue>0) echo number_format($total_balValue/$total_balQnty,2); else echo "0.00"; ?></td>
										<td width="" align="right"><? echo number_format($total_balValue,2); ?></td>              
									</tr>
							
									<?
									$k++; 
									$product_id_arr[$row[csf("prod_id")]."*".$order_id]=$row[csf("prod_id")]."*".$order_id;
						
								}
								else
								{
									?>
	                                <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="50"><? echo $k; ?></td>								
										<td width="80"><? if($row[csf("transaction_date")] !="" && $row[csf("transaction_date")] !="0000-00-00")  echo change_date_format($row[csf("transaction_date")]); ?></td>                                 
										<td width="120"><p><? echo $mrrArray[$row[csf("id")].$row[csf("transaction_type")]]; ?></p></td>
										<td width="100"><p><? echo $transaction_type[$row[csf("transaction_type")]]; ?></p></td>
										<td width="100"><p><? echo $issuePupose[$row[csf("id")]]; ?></p></td>
	                                    <?
										if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 )
										{
											$knit_dye_source=$issue_mst_arr[$row[csf("id")]]["knit_dye_source"];
											$knit_dye_company=$issue_mst_arr[$row[csf("id")]]["knit_dye_company"];

											if($knit_dye_source==1)
												$transactionWith =  $companyArr[$knit_dye_company]; 
											else  	
												$transactionWith =  $supplierArr[$knit_dye_company];
										}
										else
										{
											$transactionWith =  $supplierArr[$rcvSupplier[$row[csf("id")]]];
										}
	 									?>
	                                    <td width="100"><p><? echo $transactionWith; ?></p></td> 
	                                    <td width="80" align="right"><? if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) echo number_format($row[csf("cons_quantity")],2); ?></td>
										<td width="60" align="right"><? if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) echo number_format($row[csf("cons_rate")],2); ?></td>
										<td width="110" align="right"><? if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) echo number_format($row[csf("cons_amount")],2); ?></td>              
										
										<td width="80" align="right"><? if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) echo number_format($row[csf("cons_quantity")],2); ?></td>
										<td width="60" align="right"><? if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) echo number_format($row[csf("cons_rate")],2); ?></td>
										<td width="110" align="right"><? if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) echo number_format($row[csf("cons_amount")],2); ?></td>
										<?
										$each_pro_id=array();
																											
										if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) $total_balQnty +=$row[csf("cons_quantity")]; 
										if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) $total_balQnty -=$row[csf("cons_quantity")]; 
										
										if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5)  $total_balValue += $row[csf("cons_amount")];
										if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6)  $total_balValue -= $row[csf("cons_amount")];
											
										?> 
									<td width="80" align="right"><? echo number_format($total_balQnty,2); ?></td>
									<td width="60" align="right"><? if($total_balQnty>0 && $total_balValue>0) echo number_format($total_balValue/$total_balQnty,2); else echo "0.00"; ?></td>
									<td width="" align="right"><? echo number_format($total_balValue,2); ?></td>              
									</tr>

                                	<?
									$k++;
								}
											
								$i++;

								//total sum START-----------------------
								if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) $rcvQnty += $row[csf("cons_quantity")];
								if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) $rcvValue += $row[csf("cons_amount")];
							
								if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) $issQnty += $row[csf("cons_quantity")];
								if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) $issValue += $row[csf("cons_amount")]; 

							} 
								?> 
						<!-- END FOREACH LOOP--> 
					
                     
					<tr class="tbl_bottom">
						<td colspan="6" align="right">Total</td>
						<td align="right" ><? echo number_format($rcvQnty,2); ?></td>
						<td></td>
						<td align="right" ><? echo number_format($rcvValue,2); ?></td>
						<td align="right" ><? echo number_format($issQnty,2); ?></td>
						<td></td>
						<td align="right" ><? echo number_format($issValue,2); ?></td>                                    
						<td>&nbsp;</td>
						<td></td>
						<td>&nbsp;</td>
					</tr>  
				</table> 
			</div>  
		</div>    
	</fieldset>  
    <?

	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename) {
	@unlink($filename);
	}
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename"; 
	exit();	 
}

?>

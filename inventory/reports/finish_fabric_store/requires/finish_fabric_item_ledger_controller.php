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
			show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $company; ?>+'_'+<? echo $cbo_item_category_id; ?>+'_'+document.getElementById('txt_product_id').value, 'create_lot_search_list_view', 'search_div', 'finish_fabric_item_ledger_controller', 'setFilterGrid("list_view",-1)');
		}
		
		function search_by_type( val )
		{
			$('#txt_search_common').val('');
			
			if(val==1)
			{
				$('#search_by_td_up').html('Enter Item Description');
			}
			else if(val==2)
			{
				$('#search_by_td_up').html('Enter Item Code');
			}
		}
    </script>
    <body>
	<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<table width="500" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
				<thead>
					<tr>                	 
						<th>Search By</th>
						<th align="center" width="200" id="search_by_td_up">Enter Item Description </th>
						<th align="center" width="100">Product ID</th>
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
								$search_by = array(1=>'Item Description',2=>'Item Code');
								$dd="";
								echo create_drop_down( "cbo_search_by", 150, $search_by, "", 0, "--Select--", "", "search_by_type(this.value);", 0);
							?>
						</td>
						<td width="180" align="center" id="search_by_td">				
							<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td width="100" align="center">				
							<input type="text" style="width:100px" class="text_boxes"  name="txt_product_id" id="txt_product_id" />
						</td> 
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="fn_check_lot()" style="width:100px;" />				
						</td>
					</tr>
 				</tbody>
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
	$cbo_item_category_id = $ex_data[3];
	$product_id = trim($ex_data[4]);
	
	$sql_cond="";
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==1) // for LOT NO
		{
			$sql_cond= " and product_name_details LIKE '%$txt_search_common%'";	 
 		}
		else if(trim($txt_search_by)==2) // for Yarn Count
		{
			if($txt_search_common==0 || $txt_search_common=="")
			{
				$sql_cond= " ";	 	
			}
			else
			{
				$sql_cond=" and item_code='$txt_search_common'";	 	
			}
 		} 
 	}
	if($product_id)
	{
		$sql_cond.= " and id=$product_id";
	}
	
 	$sql = "select id,product_name_details,gsm,dia_width from product_details_master where company_id=$company and item_category_id ='$cbo_item_category_id' $sql_cond"; 
	$arr=array();
	echo create_list_view("list_view", "Product Id, Item Description, GSM, Dia","70,230,100","550","260",0, $sql , "js_set_value", "id,product_name_details", "", 1, "0,0,0,0", $arr, "id,product_name_details,gsm,dia_width", "","","0","",1) ;	
	
	exit();	
}

//report generated here--------------------//
if($action=="generate_report")
{ 
	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$txt_product_id=str_replace("'","",$txt_product_id);
	$user_product_id=str_replace("'","",$user_product_id);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);

	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );
	$search_cond="";
	if($db_type==0)
	{
 		if( $from_date!="" && $to_date!="" ) $search_cond .= " and a.transaction_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
	}
	else
	{
		if($from_date!="" && $to_date!="") $search_cond .= " and a.transaction_date  between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";
	}
	$str_cond="";
	if($txt_product_id!="") $str_cond.="and a.prod_id in ($txt_product_id)";
	if($user_product_id!="") $str_cond.="and a.prod_id in ($user_product_id)";
	

 	 
 	// receive MRR array------------------------------------------------
	$sql_receive_mrr = "select a.id as trid, a.transaction_type, b.recv_number, b.receive_date from inv_transaction a, inv_receive_master b where b.company_id='$cbo_company_name' and a.item_category='$cbo_item_category_id' and a.mst_id=b.id and a.transaction_type in (1,4) and a.status_active=1 and a.is_deleted=0 $str_cond"; 
	//echo $sql_receive_mrr;die;
	$result_rcv = sql_select($sql_receive_mrr);
	$receiveMRR=array();
	$trWiseReceiveMRR=array();
	foreach($result_rcv as $row)
	{
		$receiveMRR[$row[csf("trid")]."*".$row[csf("transaction_type")]] = $row[csf("recv_number")];
		$trWiseReceiveMRR[$row[csf("trid")]] = $row[csf("recv_number")];
		$trans_master_date_arr[$row[csf("trid")]] = $row[csf("receive_date")];
	}
	
	
	// issue MRR array------------------------------------------------		
	$sql_issue_mrr = "select a.id as trid,a.transaction_type,b.issue_number,b.issue_purpose, b.issue_date from inv_transaction a, inv_issue_master b where b.company_id='$cbo_company_name' and a.item_category='$cbo_item_category_id' and a.mst_id=b.id and a.transaction_type in (2,3) and a.status_active=1 and a.is_deleted=0 $str_cond";		 
	$result_iss = sql_select($sql_issue_mrr);
	$issueMRR=array();$issuePupose=array();
	foreach($result_iss as $row)
	{
		$issueMRR[$row[csf("trid")]."*".$row[csf("transaction_type")]] = $row[csf("issue_number")];
		$issuePupose[$row[csf("trid")]] = $yarn_issue_purpose[$row[csf("issue_purpose")]]; 
		$trans_master_date_arr[$row[csf("trid")]] = $row[csf("issue_date")]; 
	} 

	//transfer MRR array-----------------------------------------------
	$sql_transfer_mrr = "select a.id as trid,a.transaction_type, b.transfer_system_id, b.transfer_date from inv_transaction a, inv_item_transfer_mst b where a.company_id='$cbo_company_name' and a.item_category='$cbo_item_category_id' and a.mst_id=b.id and a.transaction_type in (5,6) and a.status_active=1 and a.is_deleted=0 $str_cond";	

	$result_transfer = sql_select($sql_transfer_mrr);
	$transferMRR=array();
	foreach($result_transfer as $row)
	{
		$transferMRR[$row[csf("trid")]."*".$row[csf("transaction_type")]] = $row[csf("transfer_system_id")];
		$trans_master_date_arr[$row[csf("trid")]] = $row[csf("transfer_date")]; 
	} 
	 
	 
	// var_dump($issueMRR);
	// var_dump($issuePupose);
	
	//array join or merge here ------------- do not delete or change
	$mrrArray = array();
	$mrrArray = $receiveMRR+$issueMRR+$transferMRR;
	
	/*echo "<pre>";
	echo "rcv=".print_r($receiveMRR)."<br>"."<br>"."<br>";
	print_r($issueMRR)."<br>"."<br>"."<br>";
	print_r($mrrArray);*/
	?>
    <fieldset>
    <?
		
	//Master Query---------------------------------------------------- 
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
		where transaction_date < '".$from_date."' and item_category='$cbo_item_category_id' and status_active=1 and is_deleted=0 group by prod_id";
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
	//var_dump($opning_bal_arr);die;
	if($cbo_method==0) //average rate #########################################################################
	{
		$sql = "SELECT a.id, a.prod_id, a.transaction_type, a.receive_basis, a.insert_date, a.cons_quantity, a.cons_rate, a.cons_amount, b.product_name_details, b.color, b.unit_of_measure, b.item_group_id, b.lot, b.current_stock, c.knit_dye_source, c.knit_dye_company, c.issue_purpose,a.transaction_date
		from inv_transaction a left join inv_issue_master c on a.mst_id=c.id and a.transaction_type in (2,3,6), product_details_master b where a.status_active=1 and a.is_deleted=0 and a.prod_id=b.id and a.item_category='$cbo_item_category_id' and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 $str_cond $search_cond 
		order by a.prod_id, a.insert_date, a.id ASC";		

	}
	else if($cbo_method==1) //FIFU #########################################################################
	{
		$sql = "SELECT a.id, a.prod_id, a.transaction_type, a.receive_basis, a.insert_date, a.cons_quantity, a.cons_rate, a.cons_amount, b.product_name_details, b.color, b.unit_of_measure, b.item_group_id, b.lot, b.current_stock, c.knit_dye_source, c.knit_dye_company, c.issue_purpose, a.transaction_date
		from inv_transaction a left join inv_issue_master c on a.mst_id=c.id and a.transaction_type in (2,3,6), product_details_master b where a.status_active=1 and a.is_deleted=0 and  a.prod_id=b.id and a.item_category='$cbo_item_category_id' and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 $str_cond  $search_cond 
		order by a.prod_id, a.insert_date, a.id ASC";		

	}
	else if($cbo_method==2) //LIFU #########################################################################
	{
		$sql = "SELECT a.id, a.prod_id, a.transaction_type, a.receive_basis, a.insert_date, a.cons_quantity, a.cons_rate, a.cons_amount, b.product_name_details, b.color, b.unit_of_measure, b.item_group_id, b.lot, b.current_stock, c.knit_dye_source, c.knit_dye_company, c.issue_purpose,a.transaction_date
		from inv_transaction a left join inv_issue_master c on a.mst_id=c.id and a.transaction_type in (2,3,6), product_details_master b where a.status_active=1 and a.is_deleted=0 and  a.prod_id=b.id and a.item_category='$cbo_item_category_id' and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 $str_cond  $search_cond 
		order by a.prod_id, a.insert_date, a.id ASC";

	}
	
	//echo $sql;die;
	$result = sql_select($sql);	
	$checkItemArr=array();
	$balQnty=$balValue=array(); 
	$rcvQnty=$rcvValue=$issQnty=$issValue=0;
	$i=1;
	ob_start();	
	?>
	<div> 
		<table style="width:1400px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" > 
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
				  <th width="80">Rate</th>
				  <th width="110">Value</th>
				  <th width="80">Qnty</th>
				  <th width="80">Rate</th>
				  <th width="110">Value</th>
				  <th width="80">Qnty</th>
				  <th width="80">Rate</th>
				  <th width="">Value</th>
			  </tr>
			</thead>
	   </table>  
		<div style="width:1400px; overflow-y:scroll; max-height:250px" id="scroll_body" > 
			<table style="width:1380px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">   
					<?		
					$m=1;$product_id_arr=array();$k=1;
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
									<td colspan="6" align="right">Total</td>
									<td><? echo number_format($rcvQnty,2); ?></td><td></td><td><? echo number_format($rcvValue,2); ?></td>
									<td><? echo number_format($issQnty,2); ?></td><td></td><td><? echo number_format($issValue,2); ?></td>                                    
									<td>&nbsp;</td><td></td><td>&nbsp;</td>
								</tr>
								
								<!-- product wise herder -->
								<thead>
									<tr>
										<td colspan="9"><b>Product ID : <? echo $row[csf("prod_id")]." , ".$row[csf("product_name_details")].", ".$color_arr[$row[csf("color")]].", UOM# ".$unit_of_measurement[$row[csf("unit_of_measure")]].", Current Stock: ".number_format($row[csf("current_stock")],2); ?></b></td>
										<td colspan="6" align="center">&nbsp;</td>
									</tr>
								</thead>
								<!-- product wise herder END -->
								<?
							}
							
						
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
						//print product name details header---------------------------
						if($i==1)
						{
							?> 
							<thead>
								<tr>
									<td colspan="9"><b>Product ID : <? echo $row[csf("prod_id")]." , ".$row[csf("product_name_details")].", ".$color_arr[$row[csf("color")]].", UOM# ".$unit_of_measurement[$row[csf("unit_of_measure")]].", Current Stock: ".$row[csf("current_stock")]; ?></b></td>
									<td colspan="6" align="center"></td>
								</tr>
							</thead> 
							<?
						}
						//print product name details header END -------------------------
						
						if ($i%2==0)$bgcolor="#E9F3FF";	else $bgcolor="#FFFFFF"; 
						if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3) 
							$stylecolor='style="color:#A61000"';
						else
							$stylecolor='style="color:#000000"';
							
						if(!in_array($row[csf("prod_id")],$product_id_arr))
						{
							$k=1;
							$trans_master_date  = $trans_master_date_arr[$row[csf("id")]];				
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="50"><? echo $k; ?></td>								
								<td width="80"><p><? if($trans_master_date !="" && $trans_master_date !="0000-00-00")  echo change_date_format($trans_master_date); ?>&nbsp;</p></td>                                 
								<td width="120"><p>
								<?
								if( $row[csf("mst_id")]==0 && $row[csf("receive_basis")]==30)
								{
									echo "Adjustment"; 
								}
								else
								{
									echo $mrrArray[$row[csf("id")]."*".$row[csf("transaction_type")]]; 
								} 
								?></p></td>
								<td width="100"><p><? echo $transaction_type[$row[csf("transaction_type")]]; ?></p></td>
								<td width="100"><p><? echo $issuePupose[$row[csf("id")]]; ?></p></td>
								
								<? 										
									if($row[csf("knit_dye_source")]==1)
										$transactionWith =  $companyArr[$row[csf("knit_dye_company")]]; 
									else  	
										$transactionWith =  $supplierArr[$row[csf("knit_dye_company")]];
								?>
								
								<td width="100"><p><? echo $transactionWith; ?></p></td> 
								<td width="80" align="right"><? if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) echo number_format($row[csf("cons_quantity")],2); ?></td>
								<td width="80" align="right" title="<? if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) echo $row[csf("cons_rate")]; ?>"><? if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) echo number_format($row[csf("cons_rate")],2); ?></td>
								<td width="110" align="right"><? if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) echo number_format($row[csf("cons_amount")],2); ?></td>              
								
								<td width="80" align="right"><? if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) echo number_format($row[csf("cons_quantity")],2); ?></td>
								<td width="80" align="right" title="<? if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) echo $row[csf("cons_rate")]; ?>"><? if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) echo number_format($row[csf("cons_rate")],2); ?></td>
								<td width="110" align="right"><? if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) echo number_format($row[csf("cons_amount")],2); ?></td>
								<?
								$each_pro_id=array();
								if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) $total_balQnty =number_format($balQnty[$row[csf("prod_id")]],8,'.','')+ number_format(trim($row[csf("cons_quantity")]),8,'.',''); 
								if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) $total_balQnty =number_format($balQnty[$row[csf("prod_id")]],8,'.','')-number_format(trim($row[csf("cons_quantity")]),8,'.',''); 
								
								if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5)  $total_balValue =number_format($balValue[$row[csf("prod_id")]],8,'.','')+ number_format(trim($row[csf("cons_amount")]),8,'.','');
								if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6)  $total_balValue =number_format($balValue[$row[csf("prod_id")]],8,'.','')- number_format(trim($row[csf("cons_amount")]),8,'.','');
								
									
								/*$total_balQnty=number_format($total_balQnty,4,'.','');
								$total_balValue=number_format($total_balValue,2,'.','');
								if($total_balQnty< 0.00009)
								{
									$bal_rate=0;
									$total_balValue=0.00;
								}
								else 
								{
									$bal_rate=$total_balValue/$total_balQnty;
								}*/
								
								if($total_balValue!=0 && $total_balQnty !=0)
								{
									$bal_rate=$total_balValue/$total_balQnty;
								}
								else
								{
									$bal_rate=0;
								}
								?> 
								<td width="80" align="right"><? echo number_format($total_balQnty,4,'.',''); ?></td>
								<td style="word-break:break-all;" width="80" align="right" title="<?= $bal_rate;?>"><? echo number_format($bal_rate,4,'.',''); ?></td>
								<td width="" align="right" title="<?= $total_balValue;?>"><? echo number_format($total_balValue,2,'.',''); ?></td>              
							</tr>
							<?
							$k++; 
							$product_id_arr[]=$row[csf("prod_id")];
				
						}
						else
						{
							$trans_master_date  = $trans_master_date_arr[$row[csf("id")]];	
							?>	
							<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="50"><? echo $k; ?></td>								
								<td width="80"><p><? if($trans_master_date !="" && $trans_master_date !="0000-00-00")  echo change_date_format($trans_master_date); ?>&nbsp;</p></td>                                 
								<td width="120"><p>
								<?
								if( $row[csf("mst_id")]==0 && $row[csf("receive_basis")]==30)
								{
									echo "Adjustment"; 
								}
								else
								{
									echo $mrrArray[$row[csf("id")]."*".$row[csf("transaction_type")]]; 
								} 
								?></p></td>
								<td width="100"><p><? echo $transaction_type[$row[csf("transaction_type")]]; ?></p></td>
								<td width="100"><p><? echo $issuePupose[$row[csf("id")]]; ?></p></td>
								
								<?
									if($row[csf("knit_dye_source")]==1)
										$transactionWith =  $companyArr[$row[csf("knit_dye_company")]]; 
									else  	
										$transactionWith =  $supplierArr[$row[csf("knit_dye_company")]];
								?>
								
								<td width="100"><p><? echo $transactionWith; ?></p></td> 
								<td width="80" align="right"><? if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) echo number_format($row[csf("cons_quantity")],2); ?></td>
								<td width="80" align="right" title="<? if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) echo $row[csf("cons_rate")]; ?>"><? if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) echo number_format($row[csf("cons_rate")],2); ?></td>
								<td width="110" align="right"><? if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) echo number_format($row[csf("cons_amount")],2); ?></td>              
								
								<td width="80" align="right"><? if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) echo number_format($row[csf("cons_quantity")],2); ?></td>
								<td width="80" align="right" title="<? if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) echo $row[csf("cons_rate")]; ?>"><? if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) echo number_format($row[csf("cons_rate")],2); ?></td>
								<td width="110" align="right"><? if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) echo number_format($row[csf("cons_amount")],2); ?></td>
								<?
								$each_pro_id=array();
								if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) $total_balQnty = number_format($total_balQnty,8,'.','')+number_format(trim($row[csf("cons_quantity")]),8,'.',''); 
								if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) $total_balQnty = number_format($total_balQnty,8,'.','')-number_format(trim($row[csf("cons_quantity")]),8,'.',''); 
								if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5)  $total_balValue = number_format($total_balValue,8,'.','')+number_format(trim($row[csf("cons_amount")]),8,'.','');
								if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6)  $total_balValue = number_format($total_balValue,8,'.','')-number_format(trim($row[csf("cons_amount")]),8,'.','');
								
								//if($m==1) $total_balQnty= $total_balQnty+$balQnty[$row[csf("prod_id")]]; else $total_balQnty+=$total_balQnty;
								//if($m==1) $total_balValue= $total_balValue+$balValue[$row[csf("prod_id")]]; else $total_balValue+=$total_balValue;
								/*$total_balQnty=number_format($total_balQnty,4,'.','');
								$total_balValue=number_format($total_balValue,2,'.','');
								if($total_balQnty< 0.00009)
								{
									$bal_rate=0;
									$total_balValue=0.00;
								}
								else 
								{
									$bal_rate=$total_balValue/$total_balQnty;
								}*/
								if($total_balValue!=0 && $total_balQnty !=0)
								{
									$bal_rate=$total_balValue/$total_balQnty;
								}
								else
								{
									$bal_rate=0;
								}
								?> 
								<td width="80" align="right"><? echo number_format($total_balQnty,4,'.',''); ?></td>
								<td style="word-break:break-all;" width="80" align="right" title="<? echo $bal_rate; ?>"><? echo number_format($bal_rate,4,'.',''); ?></td>
								<td width="" align="right" title="<?= $total_balValue;?>"><? echo number_format($total_balValue,2,'.',''); ?></td>              
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
				<tr class="tbl_bottom">
					<td colspan="6" align="right">Total</td>
					<td align="right" ><? echo number_format($rcvQnty,2); ?></td><td></td><td align="right" ><? echo number_format($rcvValue,2); ?></td>
					<td align="right" ><? echo number_format($issQnty,2); ?></td><td></td><td align="right" ><? echo number_format($issValue,2); ?></td>                                    
					<td>&nbsp;</td><td></td><td>&nbsp;</td>
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
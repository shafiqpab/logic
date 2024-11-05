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
			show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $company; ?>+'_'+document.getElementById('txt_prod_id').value, 'create_lot_search_list_view', 'search_div', 'woven_grey_fabric_item_ledger_controller', 'setFilterGrid("list_view",-1)');
		}
	</script>
	<body>
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="600" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
					<thead>
						<tr>                	 
							<th>Search By</th>
							<th align="center" width="180" id="search_by_td_up">Enter Item Description</th>
							<th align="center" width="120">Product Id</th>
							<th width="120">
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
								$search_by = array(1=>'Item Description');
								$dd="";
								echo create_drop_down( "cbo_search_by", 150, $search_by, "", 0, "--Select--", "", "", 0);
								?>
							</td>
							<td  align="center" id="search_by_td">				
								<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">				
								<input type="text" style="width:90px" class="text_boxes"  name="txt_prod_id" id="txt_prod_id" />
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
	$prod_id = $ex_data[3];
	
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
	
	$sql = "select id,product_name_details,gsm,dia_width from product_details_master where company_id=$company and item_category_id =14 $sql_cond"; 
	$arr=array();
	echo create_list_view("list_view", "Product Id, Item Description, GSM, Dia","70,230,100","550","260",0, $sql , "js_set_value", "id,product_name_details", "", 1, "0,0,0,0", $arr, "id,product_name_details,gsm,dia_width", "","","0","",1) ;	
	
	exit();	
}

//report generated here--------------------//
if($action=="generate_report")
{ 	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if($rpt_type==1)
	{
		$search_cond="";
		if($db_type==0)
		{
			if( $from_date!="" && $to_date!="" ) $search_cond .= " and a.transaction_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
		}
		else
		{
			if($from_date!="" && $to_date!="") $search_cond .= " and a.transaction_date  between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";
		}

		// receive MRR array------------------------------------------------
		$sql_receive_mrr = "select a.id as trid, a.transaction_type,b.recv_number 
		from inv_transaction a, inv_receive_master b
		where a.prod_id in ($txt_product_id) and a.mst_id=b.id and a.transaction_type in (1,4) and a.item_category='14'"; 
		$result_rcv = sql_select($sql_receive_mrr);
		$receiveMRR=array();
		$trWiseReceiveMRR=array();
		foreach($result_rcv as $row)
		{
			$receiveMRR[$row[csf("trid")].$row[csf("transaction_type")]] = $row[csf("recv_number")];
			$trWiseReceiveMRR[$row[csf("trid")]] = $row[csf("recv_number")];
		}
		
		// issue MRR array------------------------------------------------		
		$sql_issue_mrr = "select a.id as trid,a.transaction_type,b.issue_number,b.issue_purpose
		from inv_transaction a, inv_issue_master b
		where a.prod_id in ($txt_product_id) and a.mst_id=b.id and a.transaction_type in (2,3) and a.item_category='14'";		 
		$result_iss = sql_select($sql_issue_mrr);
		$issueMRR=array();$issuePupose=array();
		foreach($result_iss as $row)
		{
			$issueMRR[$row[csf("trid")].$row[csf("transaction_type")]] = $row[csf("issue_number")];
			$issuePupose[$row[csf("trid")]] = $yarn_issue_purpose[$row[csf("issue_purpose")]]; 
		}


		// transfer MRR array------------------------------------------------		
		$sql_transfer_mrr = "select a.id as trid,a.transaction_type,b.transfer_system_id, b.transfer_criteria, b.from_order_id, b.to_order_id
		from inv_transaction a, inv_item_transfer_mst b
		where a.mst_id=b.id and a.transaction_type in (5,6) and a.item_category='14' and a.prod_id in ($txt_product_id)";		 
		$result_transfer = sql_select($sql_transfer_mrr);
		$transferMRR=array();
		foreach($result_transfer as $row)
		{
			$transferMRR[$row[csf("trid")].$row[csf("transaction_type")]] = $row[csf("transfer_system_id")];
		} 
		//array join or merge here ------------- do not delete or change
		$mrrArray = array();
		$mrrArray = $receiveMRR+$issueMRR+$transferMRR;
		?>
		<fieldset style="width:1420px">
			<?
			
			//Master Query---------------------------------------------------- 
			if( $from_date!="" && $to_date!="" ) 
			{
				if($db_type==2) $from_date=date("j-M-Y",strtotime($from_date)); 
				if($db_type==0) $from_date=change_date_format($from_date, 'yyyy-mm-dd'); 
				//for opening balance
				$sqlTR = "SELECT  prod_id, 
				SUM(CASE WHEN transaction_type in (1,4,5) THEN cons_quantity ELSE 0 END) as receive,
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

			if($cbo_method==0) //average rate #########################################################################
			{
				$sql = "SELECT a.id, a.prod_id, a.transaction_date, a.receive_basis, a.insert_date, a.transaction_type, a.cons_quantity, a.cons_rate, a.cons_amount, b.product_name_details, b.unit_of_measure, b.item_group_id, b.lot, c.knit_dye_source, c.knit_dye_company, c.issue_purpose
				from inv_transaction a left join inv_issue_master c on a.mst_id=c.id and a.transaction_type in (2,3,6), product_details_master b 
				where a.status_active=1 and a.is_deleted=0 and  a.prod_id in ($txt_product_id) and a.prod_id=b.id  and a.item_category='14' and a.company_id=$cbo_company_name $search_cond 
				order by  a.prod_id, a.insert_date, a.id ASC";		

			}
			else if($cbo_method==1) //FIFU #########################################################################
			{
				$sql = "SELECT a.id, a.prod_id, a.transaction_date, a.receive_basis, a.insert_date, a.transaction_type, a.cons_quantity, a.cons_rate, a.cons_amount, b.product_name_details, b.unit_of_measure, b.item_group_id, b.lot, c.knit_dye_source, c.knit_dye_company, c.issue_purpose
				from inv_transaction a left join inv_issue_master c on a.mst_id=c.id and a.transaction_type in (2,3,6), product_details_master b 
				where a.status_active=1 and a.is_deleted=0 and  a.prod_id in ($txt_product_id) and a.prod_id=b.id  and a.item_category='14' and a.company_id=$cbo_company_name $search_cond
				order by  a.prod_id, a.insert_date, a.id ASC";		

			}
			else if($cbo_method==2) //LIFU #########################################################################
			{
				$sql = "SELECT a.id, a.prod_id, a.transaction_date, a.receive_basis, a.insert_date, a.transaction_type, a.cons_quantity, a.cons_rate, a.cons_amount, b.product_name_details, b.unit_of_measure, b.item_group_id, b.lot, c.knit_dye_source, c.knit_dye_company, c.issue_purpose
				from inv_transaction a left join inv_issue_master c on a.mst_id=c.id and a.transaction_type in (2,3,6), product_details_master b 
				where a.status_active=1 and a.is_deleted=0 and  a.prod_id in ($txt_product_id) and a.prod_id=b.id  and a.item_category='14' and a.company_id=$cbo_company_name $search_cond
				order by  a.prod_id, a.insert_date, a.id ASC";
			}
			//echo $sql;
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
					<table style="width:1380px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body"  >   
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
											<td colspan="9"><b>Product ID : <? echo $row[csf("prod_id")]." , ".$row[csf("product_name_details")].", UOM# ".$unit_of_measurement[$row[csf("unit_of_measure")]]; ?></b></td>
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
								$rcvQnty=$rcvValue=$issQnty=$issValue=0;//initialize variable
								$total_balQnty=0;$total_balValue=0;								
							}

							//print product name details header---------------------------
							if($i==1)
							{
								?> 
								<thead>
									<tr>
										<td colspan="9"><b>Product ID : <? echo $row[csf("prod_id")]." , ".$row[csf("product_name_details")].", UOM# ".$unit_of_measurement[$row[csf("unit_of_measure")]]; ?></b></td>
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
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="50"><? echo $k; ?></td>								
									<td width="80"><? if($row[csf("transaction_date")] !="" && $row[csf("transaction_date")] !="0000-00-00")  echo change_date_format($row[csf("transaction_date")]); ?></td>
                                    <td width="120"><p>
									<?
									if( $row[csf("mst_id")]==0 && $row[csf("receive_basis")]==30)
									{
										echo "Adjustment"; 
									}
									else
									{
										echo $mrrArray[$row[csf("id")].$row[csf("transaction_type")]]; 
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
									<td width="80" align="right">
									<? 
										if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[("transaction_type")]==5) {
											echo number_format($row[csf("cons_quantity")],2); 
										}
									?>
										
									</td>
									<td width="80" align="right" title="<? if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[("transaction_type")]==5) echo $row[csf("cons_rate")]; ?>">
										<? 
										if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) {
											echo number_format($row[csf("cons_rate")],2); 
										}
										?>
										
									</td>

									<td width="110" align="right">
										<? 
										if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) {
											echo number_format($row[csf("cons_amount")],2); 
										}
										?>
									</td>								
									<td width="80" align="right">
										<? 
										if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) 
										{
											echo number_format($row[csf("cons_quantity")],2); 
										}
										?>
									</td>

									<td width="80" align="right" title="<? if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[("transaction_type")]==6) echo $row[csf("cons_rate")]; ?>">
										<? 
										if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6)
										{ 
											echo number_format($row[csf("cons_rate")],2); 
										}
											?>
									</td>

									<td width="110" align="right">
									<? 
										if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) {
											echo number_format($row[csf("cons_amount")],2); 
										}
									?>
										
									</td>
									<?
									$each_pro_id=array();


									if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) 
									{
										$total_balQnty =$balQnty[$row[csf("prod_id")]]+ $row[csf("cons_quantity")];
									} 
									if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) 
									{
										$total_balQnty =$balQnty[$row[csf("prod_id")]]-$row[csf("cons_quantity")]; 
									}

									if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5)  
									{
										$total_balValue =$balValue[$row[csf("prod_id")]]+ $row[csf("cons_amount")];
									}
									if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6)  
									{
										$total_balValue =$balValue[$row[csf("prod_id")]]- $row[csf("cons_amount")];
									}
									?> 
									<td width="80" align="right"><? echo number_format($total_balQnty,2); ?></td>
									<td title="<? echo $total_balValue/$total_balQnty; ?>" width="80" align="right">
										<? 
											if($total_balQnty == 0)
											{
												echo "0.00";
											}else{
												echo number_format($total_balValue/$total_balQnty,2); 
											}
										?>
									</td>

									<td width="" align="right"><? echo number_format($total_balaValue,2); ?></td>              
								</tr>

								<?
								$k++; 
								$product_id_arr[]=$row[csf("prod_id")];
							}
							else
							{
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="50"><? echo $k; ?></td>								
									<td width="80"><? if($row[csf("transaction_date")] !="" && $row[csf("transaction_date")] !="0000-00-00")  echo change_date_format($row[csf("transaction_date")]); ?></td>                                 
									<td width="120"><p>
									<?
									if( $row[csf("mst_id")]==0 && $row[csf("receive_basis")]==30)
									{
										echo "Adjustment"; 
									}
									else
									{
										echo $mrrArray[$row[csf("id")].$row[csf("transaction_type")]]; 
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
									<td width="80" align="right">
									<? 
										if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) {
											echo number_format($row[csf("cons_quantity")],2); 
										}
									?>
										
									</td>
									<td width="80" align="right" title="<? if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) echo $row[csf("cons_rate")]; ?>">
									<? 
										if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) {
											echo number_format($row[csf("cons_rate")],2); 
										}
									?>
										
									</td>
									<td width="110" align="right">
									<? 
									if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) {
										echo number_format($row[csf("cons_amount")],2); 
									}
									?>
										
									</td>              

									<td width="80" align="right">
									<? 
										if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) {
											echo number_format($row[csf("cons_quantity")],2); 
										}
									?>
										
									</td>
									<td width="80" align="right" title="<? if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) echo $row[csf("cons_rate")]; ?>">
									<? 
									if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) {
										echo number_format($row[csf("cons_rate")],2); 
									}
									?>
										
									</td>
									<td width="110" align="right">
									<? 
										if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[("transaction_type")]==6) {
											echo number_format($row[csf("cons_amount")],2); 
										}
									?>
										
									</td>
									<?
									$each_pro_id=array();

									$total_balQnty=str_replace(",","",$total_balQnty);
									$total_balValue=str_replace(",","",$total_balValue);																
									if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5) 
									{
										$total_balQnty +=$row[csf("cons_quantity")]; 
									}
									if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6) 
									{
										$total_balQnty -=$row[csf("cons_quantity")]; 
									}

									if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 || $row[csf("transaction_type")]==5)  
									{
										$total_balValue += $row[csf("cons_amount")];
									}
									if($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==6)  
									{
										$total_balValue -= $row[csf("cons_amount")];
									}
									?> 
									<td width="80" align="right"><? echo number_format($total_balQnty,2); ?></td>
									<td width="80" align="right" title="<? echo $total_balValue/$total_balQnty; ?>">
										<? 
										if($total_balQnty == 0)
										{
											echo "0.00";
										}else{
											echo number_format($total_balValue/$total_balQnty,2); 
										}
										?>
									</td>
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
						?> <!-- END FOREACH LOOP--> 


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
	}
	else if($rpt_type==2)
	{
		$search_cond="";
		if($db_type==0)
		{
			if( $from_date!="" && $to_date!="" ) $search_cond .= " and a.transaction_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
		}
		else
		{
			if($from_date!="" && $to_date!="") $search_cond .= " and a.transaction_date  between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";
		}


		$prod_rcv_booking = return_library_array("select id,booking_no from  inv_receive_master where status_active=1 and is_deleted=0 and entry_form=550 and item_category=14","id","booking_no"); 
		$trans_sam_booking = return_library_array("select id,booking_no from  wo_non_ord_samp_booking_mst where status_active=1 and is_deleted=0","id","booking_no"); 
		// receive MRR array------------------------------------------------
		$rcv_prod_cond="";
		if($txt_product_id!="") $rcv_prod_cond=" and a.prod_id in ($txt_product_id)";
		$sql_receive_mrr = "SELECT a.id as trid, a.transaction_type,b.recv_number, b.receive_basis, b.booking_id, b.booking_no
		from inv_transaction a, inv_receive_master b
		where a.mst_id=b.id and a.transaction_type in (1,4) and a.item_category='14' $rcv_prod_cond"; 
				//echo $sql_receive_mrr;die;
		//echo $sql_receive_mrr;

		$result_rcv = sql_select($sql_receive_mrr);
		$receiveMRR=array();
		$trWiseReceiveMRR=array();$book_no_arr=array();
		foreach($result_rcv as $row)
		{
			$receiveMRR[$row[csf("trid")].$row[csf("transaction_type")]] = $row[csf("recv_number")];
			$trWiseReceiveMRR[$row[csf("trid")]] = $row[csf("recv_number")];
			$book_no_arr[$row[csf("trid")]]["receive_basis"]=$row[csf("receive_basis")];
			$book_no_arr[$row[csf("trid")]]["booking_id"]=$row[csf("booking_id")];
			$book_no_arr[$row[csf("trid")]]["booking_no"]=$row[csf("booking_no")];
		}
		
		// issue MRR array------------------------------------------------

		$sql_issue_mrr = "SELECT a.id as trid,a.transaction_type,b.issue_number,b.issue_purpose,b.booking_no
		from inv_transaction a, inv_issue_master b
		where a.mst_id=b.id and a.transaction_type in (2,3) and a.item_category='14' $rcv_prod_cond";		 
		$result_iss = sql_select($sql_issue_mrr);
		$issueMRR=array();$issuePupose=array();
		foreach($result_iss as $row)
		{
			$issueMRR[$row[csf("trid")].$row[csf("transaction_type")]] = $row[csf("issue_number")];
			$issuePupose[$row[csf("trid")]] = $yarn_issue_purpose[$row[csf("issue_purpose")]]; 
			$book_no_arr[$row[csf("trid")]]["booking_no"]=$row[csf("booking_no")];
		} 

		// transfer MRR array------------------------------------------------		
		$sql_transfer_mrr = "SELECT a.id as trid,a.transaction_type,b.transfer_system_id, b.transfer_criteria, b.from_order_id, b.to_order_id
		from inv_transaction a, inv_item_transfer_mst b
		where a.mst_id=b.id and a.transaction_type in (5,6) and a.item_category='14' $rcv_prod_cond";		 
		$result_transfer = sql_select($sql_transfer_mrr);
		$transferMRR=array();
		foreach($result_transfer as $row)
		{
			$transferMRR[$row[csf("trid")].$row[csf("transaction_type")]] = $row[csf("transfer_system_id")];
			$book_no_arr[$row[csf("trid")]]["transfer_criteria"]=$row[csf("transfer_criteria")];
			$book_no_arr[$row[csf("trid")]]["from_order_id"]=$row[csf("from_order_id")];
			$book_no_arr[$row[csf("trid")]]["to_order_id"]=$row[csf("to_order_id")];
		} 

		//array join or merge here ------------ do not delete or change
		$mrrArray = array();
		$mrrArray = $receiveMRR+$issueMRR+$transferMRR; 

		//var_dump($mrrArray);
		?>
		<fieldset style="width:1550px">
			<?

			//Master Query---------------------------------------------------- 
			if( $from_date!="" && $to_date!="" ) 
			{
				if($db_type==2) $from_date=date("j-M-Y",strtotime($from_date)); 
				if($db_type==0) $from_date=change_date_format($from_date, 'yyyy-mm-dd'); 
				//for opening balance
				$sqlTR = "SELECT  prod_id, SUM(CASE WHEN transaction_type in (1,4,5) THEN cons_quantity ELSE 0 END) as receive,
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
			//var_dump($opning_bal_arr);die;


			if($txt_po_no!=''){$po_con=" and b.job_no ='$txt_po_no'";}else{$po_con="";}
			$po_sql="SELECT a.id as po_id, b.job_no from fabric_sales_order_dtls a, fabric_sales_order_mst b, order_wise_pro_details c  where a.mst_id=b.id and a.id=c.po_breakdown_id and a.status_active=1 and b.status_active=1 $po_con";
			//echo $po_sql;
			$job_sql=sql_select($po_sql);
			$select_po_id="";
			foreach($job_sql as $row)
			{
				$job_data[$row[csf("po_id")]]["po_number"]=$row[csf("job_no")];
				$job_data[$row[csf("po_id")]]["job_no"]=$row[csf("job_no")];
				$select_po_id.=$row[csf("po_id")].",";
			}
			$select_po_id=implode(",",array_unique(explode(",",chop($select_po_id,","))));

			$order_cond="";
			if($txt_po_no!='') $order_cond=" and d.po_breakdown_id in($select_po_id)";

			//echo $order_cond;die;

			if($cbo_method==0) //average rate #########################################################################
			{
				$sql = "SELECT a.id, a.prod_id, a.transaction_date, a.insert_date, a.transaction_type, d.quantity as cons_quantity, a.cons_rate, (d.quantity*a.cons_rate) as cons_amount, b.product_name_details, b.unit_of_measure, b.item_group_id, b.lot, c.knit_dye_source, c.knit_dye_company, c.issue_purpose, d.po_breakdown_id
				from inv_transaction a left join inv_issue_master c on a.mst_id=c.id and a.transaction_type in (2,3,6), product_details_master b , order_wise_pro_details d 
				where a.prod_id=b.id and a.id=d.trans_id  and  a.status_active=1 and a.is_deleted=0 $rcv_prod_cond and a.item_category='14' and a.company_id=$cbo_company_name $search_cond $order_cond order by  a.prod_id, a.insert_date, a.id ASC";		

			}
			else if($cbo_method==1) //FIFU #########################################################################
			{
				$sql = "SELECT a.id, a.prod_id, a.transaction_date, a.insert_date, a.transaction_type, d.quantity as cons_quantity, a.cons_rate, (d.quantity*a.cons_rate) as cons_amount, b.product_name_details, b.unit_of_measure, b.item_group_id, b.lot, c.knit_dye_source, c.knit_dye_company, c.issue_purpose, d.po_breakdown_id
				from inv_transaction a left join inv_issue_master c on a.mst_id=c.id and a.transaction_type in (2,3,6), product_details_master b  , order_wise_pro_details d 
				where a.prod_id=b.id  and a.id=d.trans_id and a.status_active=1 and a.is_deleted=0 $rcv_prod_cond and  a.item_category='14' and a.company_id=$cbo_company_name $search_cond $order_cond order by  a.prod_id, a.insert_date, a.id ASC";		

			}
			else if($cbo_method==2) //LIFU #########################################################################
			{
				$sql = "SELECT a.id, a.prod_id, a.transaction_date, a.transaction_type, d.quantity as cons_quantity, a.cons_rate, (d.quantity*a.cons_rate) as cons_amount, b.product_name_details, b.unit_of_measure, b.item_group_id, b.lot, c.knit_dye_source, c.knit_dye_company, c.issue_purpose, d.po_breakdown_id
				from inv_transaction a left join inv_issue_master c on a.mst_id=c.id and a.transaction_type in (2,3,6), product_details_master b, order_wise_pro_details d 
				where a.prod_id=b.id and a.id=d.trans_id and a.status_active=1 and a.is_deleted=0 $rcv_prod_cond and  a.item_category='14' and a.company_id=$cbo_company_name $search_cond $order_cond order by  a.prod_id, a.insert_date, a.id DESC";		

			}
			//echo $sql."<br>";
			$result = sql_select($sql);	
			$result_data_array=array();
			foreach($result as $row)
			{
				$trans_id_arr[$row[csf("id")]]=$row[csf("id")];
				$result_data_array[$row[csf("prod_id")]][$row[csf("id")]][$row[csf("po_breakdown_id")]]["id"]=$row[csf("id")];
				$result_data_array[$row[csf("prod_id")]][$row[csf("id")]][$row[csf("po_breakdown_id")]]["prod_id"]=$row[csf("prod_id")];
				$result_data_array[$row[csf("prod_id")]][$row[csf("id")]][$row[csf("po_breakdown_id")]]["transaction_date"]=$row[csf("transaction_date")];
				$result_data_array[$row[csf("prod_id")]][$row[csf("id")]][$row[csf("po_breakdown_id")]]["transaction_type"]=$row[csf("transaction_type")];
				$result_data_array[$row[csf("prod_id")]][$row[csf("id")]][$row[csf("po_breakdown_id")]]["cons_quantity"]=$row[csf("cons_quantity")];
				$result_data_array[$row[csf("prod_id")]][$row[csf("id")]][$row[csf("po_breakdown_id")]]["cons_rate"]=$row[csf("cons_rate")];
				$result_data_array[$row[csf("prod_id")]][$row[csf("id")]][$row[csf("po_breakdown_id")]]["cons_amount"]=$row[csf("cons_amount")];
				$result_data_array[$row[csf("prod_id")]][$row[csf("id")]][$row[csf("po_breakdown_id")]]["product_name_details"]=$row[csf("product_name_details")];
				$result_data_array[$row[csf("prod_id")]][$row[csf("id")]][$row[csf("po_breakdown_id")]]["unit_of_measure"]=$row[csf("unit_of_measure")];
				$result_data_array[$row[csf("prod_id")]][$row[csf("id")]][$row[csf("po_breakdown_id")]]["item_group_id"]=$row[csf("item_group_id")];
				$result_data_array[$row[csf("prod_id")]][$row[csf("id")]][$row[csf("po_breakdown_id")]]["lot"]=$row[csf("lot")];
				$result_data_array[$row[csf("prod_id")]][$row[csf("id")]][$row[csf("po_breakdown_id")]]["knit_dye_source"]=$row[csf("knit_dye_source")];
				$result_data_array[$row[csf("prod_id")]][$row[csf("id")]][$row[csf("po_breakdown_id")]]["knit_dye_company"]=$row[csf("knit_dye_company")];
				$result_data_array[$row[csf("prod_id")]][$row[csf("id")]][$row[csf("po_breakdown_id")]]["issue_purpose"]=$row[csf("issue_purpose")];
				$result_data_array[$row[csf("prod_id")]][$row[csf("id")]][$row[csf("po_breakdown_id")]]["po_breakdown_id"]=$row[csf("po_breakdown_id")];
			}
			
			if(count($trans_id_arr)>0)
			{
				if($cbo_method==0) //average rate #########################################################################
				{
					$trans_non_order = "SELECT a.id, a.prod_id, a.transaction_date, a.transaction_type, a.cons_quantity as cons_quantity, a.cons_rate, a.cons_amount as cons_amount, b.product_name_details, b.unit_of_measure, b.item_group_id, b.lot, c.knit_dye_source, c.knit_dye_company, c.issue_purpose, 0 as po_breakdown_id
					from inv_transaction a left join inv_issue_master c on a.mst_id=c.id and a.transaction_type in (2,3,6), product_details_master b 
					where a.prod_id=b.id and  a.status_active=1 and a.is_deleted=0 and  a.prod_id in ($txt_product_id) and a.item_category='14' and a.company_id=$cbo_company_name and a.id not in(".implode(",",$trans_id_arr).") $search_cond order by  a.prod_id, a.insert_date, a.id ASC";		

				}
				else if($cbo_method==1) //FIFU #########################################################################
				{
					$trans_non_order = "SELECT a.id, a.prod_id, a.transaction_date, a.transaction_type, a.cons_quantity as cons_quantity, a.cons_rate, a.cons_amount as cons_amount, b.product_name_details, b.unit_of_measure, b.item_group_id, b.lot, c.knit_dye_source, c.knit_dye_company, c.issue_purpose, 0 as po_breakdown_id
					from inv_transaction a left join inv_issue_master c on a.mst_id=c.id and a.transaction_type in (2,3,6), product_details_master b 
					where a.prod_id=b.id and  a.status_active=1 and a.is_deleted=0 and  a.prod_id in ($txt_product_id) and a.item_category='14' and a.company_id=$cbo_company_name and a.id not in(".implode(",",$trans_id_arr).") $search_cond order by  a.prod_id, a.insert_date, a.id ASC";		

				}
				else if($cbo_method==2) //LIFU #########################################################################
				{
					$trans_non_order = "SELECT a.id, a.prod_id, a.transaction_date, a.transaction_type, a.cons_quantity as cons_quantity, a.cons_rate, a.cons_amount as cons_amount, b.product_name_details, b.unit_of_measure, b.item_group_id, b.lot, c.knit_dye_source, c.knit_dye_company, c.issue_purpose, 0 as po_breakdown_id
					from inv_transaction a left join inv_issue_master c on a.mst_id=c.id and a.transaction_type in (2,3,6), product_details_master b  
					where a.prod_id=b.id and  a.status_active=1 and a.is_deleted=0 and  a.prod_id in ($txt_product_id) and a.item_category='14' and a.company_id=$cbo_company_name and a.id not in(".implode(",",$trans_id_arr).") $search_cond order by  a.prod_id, a.insert_date, a.id ASC";		

				}
				//echo $trans_non_order."<br>";die;
				$result_non_order = sql_select($trans_non_order);	
				foreach($result_non_order as $row)
				{
					$result_data_array[$row[csf("prod_id")]][$row[csf("id")]][$row[csf("po_breakdown_id")]]["id"]=$row[csf("id")];
					$result_data_array[$row[csf("prod_id")]][$row[csf("id")]][$row[csf("po_breakdown_id")]]["prod_id"]=$row[csf("prod_id")];
					$result_data_array[$row[csf("prod_id")]][$row[csf("id")]][$row[csf("po_breakdown_id")]]["transaction_date"]=$row[csf("transaction_date")];
					$result_data_array[$row[csf("prod_id")]][$row[csf("id")]][$row[csf("po_breakdown_id")]]["transaction_type"]=$row[csf("transaction_type")];
					$result_data_array[$row[csf("prod_id")]][$row[csf("id")]][$row[csf("po_breakdown_id")]]["cons_quantity"]=$row[csf("cons_quantity")];
					$result_data_array[$row[csf("prod_id")]][$row[csf("id")]][$row[csf("po_breakdown_id")]]["cons_rate"]=$row[csf("cons_rate")];
					$result_data_array[$row[csf("prod_id")]][$row[csf("id")]][$row[csf("po_breakdown_id")]]["cons_amount"]=$row[csf("cons_amount")];
					$result_data_array[$row[csf("prod_id")]][$row[csf("id")]][$row[csf("po_breakdown_id")]]["product_name_details"]=$row[csf("product_name_details")];
					$result_data_array[$row[csf("prod_id")]][$row[csf("id")]][$row[csf("po_breakdown_id")]]["unit_of_measure"]=$row[csf("unit_of_measure")];
					$result_data_array[$row[csf("prod_id")]][$row[csf("id")]][$row[csf("po_breakdown_id")]]["item_group_id"]=$row[csf("item_group_id")];
					$result_data_array[$row[csf("prod_id")]][$row[csf("id")]][$row[csf("po_breakdown_id")]]["lot"]=$row[csf("lot")];
					$result_data_array[$row[csf("prod_id")]][$row[csf("id")]][$row[csf("po_breakdown_id")]]["knit_dye_source"]=$row[csf("knit_dye_source")];
					$result_data_array[$row[csf("prod_id")]][$row[csf("id")]][$row[csf("po_breakdown_id")]]["knit_dye_company"]=$row[csf("knit_dye_company")];
					$result_data_array[$row[csf("prod_id")]][$row[csf("id")]][$row[csf("po_breakdown_id")]]["issue_purpose"]=$row[csf("issue_purpose")];
					$result_data_array[$row[csf("prod_id")]][$row[csf("id")]][$row[csf("po_breakdown_id")]]["po_breakdown_id"]=$row[csf("po_breakdown_id")];
				}
			}
			
			$checkItemArr=array();
			$balQnty=$balValue=array(); 
			$rcvQnty=$rcvValue=$issQnty=$issValue=0;
			$i=1;
			ob_start();
			?>
			
			<div> 
				<table style="width:1530px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" > 
					<thead>
						<tr class="form_caption" style="border:none;">
							<td colspan="17" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?> </td> 
						</tr>
						<tr style="border:none;">
							<td colspan="17" align="center" style="border:none; font-size:14px;">
								Company Name : <? echo $companyArr[str_replace("'","",$cbo_company_name)]; ?>                                
							</td>
						</tr>
						<tr style="border:none;">
							<td colspan="17" align="center" style="border:none;font-size:12px; font-weight:bold">
								<? if($from_date!="" || $to_date!="")echo "From ".change_date_format($from_date)." To ".change_date_format($to_date)."" ;?>
							</td>
						</tr>
						<tr>
							<td colspan="8">&nbsp;</td>
							<td colspan="9" align="center"><b>Weighted Average Method</b></td>
						</tr> 
						<tr>
							<th width="50" rowspan="2">SL</th>
							<th width="80" rowspan="2">Trans Date</th>
							<th width="120" rowspan="2">Trans Ref No</th>
							<th width="100" rowspan="2">Trans Type</th>
							<th width="100" rowspan="2">Purpose</th>
							<th width="100" rowspan="2">Trans With</th>
							<th width="100" rowspan="2">Order No</th>
							<th width="100" rowspan="2">Job No</th>
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
				<div style="width:1530px; overflow-y:scroll; max-height:250px" id="scroll_body" > 
					<table style="width:1510px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body"  >   
						<?		
						$m=1;$product_id_arr=array();$k=1;
						$checkItemArr=array();
						foreach($result_data_array as $prod_id=>$value)
						{
							foreach($value as $tr_id=>$val)
							{
								foreach($val as $order_id=>$row)
								{
									$pro_id=$row["prod_id"];

									//check items new or not and print product description-------------------
									if(!in_array($row["prod_id"],$checkItemArr))
									{

										if($i!=1) // product wise sum/total here------------
										{
											?>                                
											<tr class="tbl_bottom">
												<td colspan="8" align="right">Total</td>
												<td><? echo number_format($rcvQnty,2); ?></td><td></td><td><? echo number_format($rcvValue,2); ?></td>
												<td><? echo number_format($issQnty,2); ?></td><td></td><td><? echo number_format($issValue,2); ?></td>                                    
												<td>&nbsp;</td><td></td><td>&nbsp;</td>
											</tr>
											
											<!-- product wise herder -->
											<thead>
												<tr>
													<td colspan="9"><b>Product ID : <? echo $row["prod_id"]." , ".$row["product_name_details"].", UOM# ".$unit_of_measurement[$row["unit_of_measure"]]; ?></b></td>
													<td colspan="8" align="center">&nbsp;</td>
												</tr>
											</thead>
											<!-- product wise herder END -->
											<?
										}
										
										
										//opening balance query-----------
										
										$flag=0;
										$opening_qnty=$opening_balance=$opening_rate=0;
										if($opning_bal_arr[$pro_id]['prod_id']!="")
										{
											?>

											<tr style="background-color:#FFFFCC">
												<td colspan="14" align="right"><b>Opening Balance</b></td>  
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
										
										$checkItemArr[$row[("prod_id")]]=$row[("prod_id")];
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
												<td colspan="9"><b>Product ID : <? echo $row["prod_id"]." , ".$row["product_name_details"].", UOM# ".$unit_of_measurement[$row["unit_of_measure"]]; ?></b></td>
												<td colspan="8" align="center"></td>
											</tr>
										</thead> 
										<?
									}
									
									//print product name details header END -------------------------	
									
								
									if ($i%2==0)$bgcolor="#E9F3FF";	else $bgcolor="#FFFFFF"; 
									if($row[csf("transaction_type")]==2 || $row["transaction_type"]==3 || $row["transaction_type"]==6) 
										$stylecolor='style="color:#A61000"';
									else
										$stylecolor='style="color:#000000"';

									
									if(!in_array($row[csf("prod_id")],$product_id_arr))
									{
										$k=1;										
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td width="50"><? echo $k; ?></td>								
											<td width="80"><? if($row["transaction_date"] !="" && $row["transaction_date"] !="0000-00-00")  echo change_date_format($row["transaction_date"]); ?></td>
											<?
											if($row["transaction_type"]==1 || $row["transaction_type"]==2)
											{
												?>
												<td width="120"><p><a href="##" onClick="openmypage(<? echo $row["id"]; ?>,'<? echo $row["prod_id"]; ?>',<? echo $row["transaction_type"];?>,'trans_ref');" ><? echo $mrrArray[$row[csf("id")].$row["transaction_type"]]; ?></a></p></td>
												<?
											}
											else
											{
												?>
												<td width="120"><p><? echo $mrrArray[$row["id"].$row["transaction_type"]]; ?></p></td>
												<?
											}
											?>                                 

											<td width="100"><p><? echo $transaction_type[$row["transaction_type"]]; ?></p></td>
											<td width="100"><p><? echo $issuePupose[$row["id"]]; ?></p></td>

											<? 										
											if($row[csf("knit_dye_source")]==1)
												$transactionWith =  $companyArr[$row["knit_dye_company"]]; 
											else  	
												$transactionWith =  $supplierArr[$row["knit_dye_company"]];
											?>

											<td width="100"><p><? echo $transactionWith; ?></p></td> 
											<?
											if($job_data[$row["po_breakdown_id"]]["po_number"]=="")
											{
												if($row["transaction_type"]==1 || $row["transaction_type"]==4)
												{
													if($book_no_arr[$row["id"]]["receive_basis"]==9)
													{
														$order_no=$prod_rcv_booking[$book_no_arr[$row["id"]]["booking_id"]];
														$job_no=$prod_rcv_booking[$book_no_arr[$row["id"]]["booking_id"]];
													}
													else
													{
														$order_no=$book_no_arr[$row["id"]]["booking_no"];
														$job_no=$book_no_arr[$row["id"]]["booking_no"];
													}
												}
												else if( $row["transaction_type"]==2 || $row["transaction_type"]==3)
												{
													$order_no=$book_no_arr[$row["id"]]["booking_no"];
													$job_no=$book_no_arr[$row["id"]]["booking_no"];
												}
												else if($row["transaction_type"]==5 || $row["transaction_type"]==6)
												{
													if($book_no_arr[$row["id"]]["transfer_criteria"]==6)
													{
														$order_no=$trans_sam_booking[$book_no_arr[$row["id"]]["to_order_id"]];
														$job_no=$trans_sam_booking[$book_no_arr[$row["id"]]["to_order_id"]];
													}
													else if($book_no_arr[$row["id"]]["transfer_criteria"]==7)
													{

														$order_no=$trans_sam_booking[$book_no_arr[$row["id"]]["from_order_id"]];
														$job_no=$trans_sam_booking[$book_no_arr[$row["id"]]["from_order_id"]];
													}
													else
													{
														$order_no="";
														$job_no="";
													}
												}
											}
											else
											{
												$order_no=$job_data[$row["po_breakdown_id"]]["po_number"];
												$job_no=$job_data[$row["po_breakdown_id"]]["job_no"];
											}

											?>
											<td width="100"><p><? echo  $order_no; ?></p></td>
											<td width="100"><p><? echo $job_no; ?></p></td>
											<td width="80" align="right"><? if($row["transaction_type"]==1 || $row["transaction_type"]==4 || $row["transaction_type"]==5) echo number_format($row["cons_quantity"],2); ?></td>

											<td width="60" align="right"><? if($row["transaction_type"]==1 || $row["transaction_type"]==4 || $row["transaction_type"]==5) echo number_format($row["cons_rate"],2); ?></td>
											<td width="110" align="right"><? if($row["transaction_type"]==1 || $row["transaction_type"]==4 || $row["transaction_type"]==5) echo number_format($row["cons_amount"],2); ?></td>              

											<td width="80" align="right"><? if($row["transaction_type"]==2 || $row["transaction_type"]==3 || $row["transaction_type"]==6) echo number_format($row["cons_quantity"],2); ?></td>
											<td width="60" align="right"><? if($row["transaction_type"]==2 || $row["transaction_type"]==3 || $row["transaction_type"]==6) echo number_format($row["cons_rate"],2); ?></td>
											<td width="110" align="right"><? if($row["transaction_type"]==2 || $row["transaction_type"]==3 || $row["transaction_type"]==6) echo number_format($row["cons_amount"],2); ?></td>
											<?
											$each_pro_id=array();


											if($row["transaction_type"]==1 || $row["transaction_type"]==4 || $row["transaction_type"]==5) $total_balQnty =$balQnty[$row["prod_id"]]+ $row["cons_quantity"]; 
											if($row["transaction_type"]==2 || $row["transaction_type"]==3 || $row["transaction_type"]==6) $total_balQnty =$balQnty[$row["prod_id"]]-$row["cons_quantity"]; 

											if($row["transaction_type"]==1 || $row["transaction_type"]==4 || $row["transaction_type"]==5)  $total_balValue =$balValue[$row["prod_id"]]+ $row["cons_amount"];
											if($row["transaction_type"]==2 || $row["transaction_type"]==3 || $row["transaction_type"]==6)  $total_balValue =$balValue[$row["prod_id"]]- $row["cons_amount"];

											$bal_rate=$total_balValue/$total_balQnty;
											?> 
											<td width="80" align="right"><? echo number_format($total_balQunty,2); ?></td>
											<td width="60" align="right"><? echo number_format($bal_rate,2); ?></td>
											<td width="" align="right"><? echo number_format($total_balaValue,2); ?></td>              
										</tr>
										<?
										$k++; 
										$product_id_arr[]=$row[csf("prod_id")];

									}
									else
									{
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td width="50"><? echo $k; ?></td>								
											<td width="80"><? if($row["transaction_date"] !="" && $row["transaction_date"] !="0000-00-00")  echo change_date_format($row["transaction_date"]); ?></td>                                 
											<?
											if($row["transaction_type"]==1 || $row["transaction_type"]==2)
											{
												?>
												<td width="120"><p><a href="##" onClick="openmypage(<? echo $row["id"]; ?>,'<? echo $row["prod_id"]; ?>',<? echo $row["transaction_type"];?>,'trans_ref');" ><? echo $mrrArray[$row["id"].$row["transaction_type"]]; ?></a></p></td>
												<?
											}
											else
											{
												?>
												<td width="120"><p><? echo $mrrArray[$row["id"].$row["transaction_type"]]; ?></p></td>
												<?
											}
											?> 
											<td width="100"><p><? echo $transaction_type[$row["transaction_type"]]; ?></p></td>
											<td width="100"><p><? echo $issuePupose[$row["id"]]; ?></p></td>

											<? 										
											if($row[("knit_dye_source")]==1)
												$transactionWith =  $companyArr[$row["knit_dye_company"]]; 
											else  	
												$transactionWith =  $supplierArr[$row["knit_dye_company"]];
											?>

											<td width="100"><p><? echo $transactionWith; ?></p></td>
											<?
											if($job_data[$row["po_breakdown_id"]]["po_number"]=="")
											{
												if($row["transaction_type"]==1 || $row["transaction_type"]==4)
												{
													if($book_no_arr[$row["id"]]["receive_basis"]==9)
													{
														$order_no=$prod_rcv_booking[$book_no_arr[$row["id"]]["booking_id"]];
														$job_no=$prod_rcv_booking[$book_no_arr[$row["id"]]["booking_id"]];
													}
													else
													{
														$order_no=$book_no_arr[$row["id"]]["booking_no"];
														$job_no=$book_no_arr[$row["id"]]["booking_no"];
													}
												}
												else if( $row["transaction_type"]==2 || $row["transaction_type"]==3)
												{
													$order_no=$book_no_arr[$row["id"]]["booking_no"];
													$job_no=$book_no_arr[$row["id"]]["booking_no"];
												}
												else if($row["transaction_type"]==5 || $row["transaction_type"]==6)
												{
													if($book_no_arr[$row["id"]]["transfer_criteria"]==6)
													{
														$order_no=$trans_sam_booking[$book_no_arr[$row["id"]]["to_order_id"]];
														$job_no=$trans_sam_booking[$book_no_arr[$row["id"]]["to_order_id"]];
													}
													else if($book_no_arr[$row["id"]]["transfer_criteria"]==7)
													{

														$order_no=$trans_sam_booking[$book_no_arr[$row["id"]]["from_order_id"]];
														$job_no=$trans_sam_booking[$book_no_arr[$row["id"]]["from_order_id"]];
													}
													else
													{
														$order_no="";
														$job_no="";
													}
												}
											}
											else
											{
												$order_no=$job_data[$row["po_breakdown_id"]]["po_number"];
												$job_no=$job_data[$row["po_breakdown_id"]]["job_no"];
											}

											?>
											<td width="100"><p><? echo  $order_no; ?></p></td>
											<td width="100"><p><? echo $job_no; ?></p></td> 
											<td width="80" align="right"><? if($row["transaction_type"]==1 || $row["transaction_type"]==4 || $row["transaction_type"]==5) echo number_format($row["cons_quantity"],2); ?></td>
											<td width="60" align="right"><? if($row["transaction_type"]==1 || $row["transaction_type"]==4 || $row["transaction_type"]==5) echo number_format($row["cons_rate"],2); ?></td>
											<td width="110" align="right"><? if($row["transaction_type"]==1 || $row["transaction_type"]==4 || $row["transaction_type"]==5) echo number_format($row["cons_amount"],2); ?></td>              

											<td width="80" align="right"><? if($row["transaction_type"]==2 || $row["transaction_type"]==3 || $row["transaction_type"]==6) echo number_format($row["cons_quantity"],2); ?></td>
											<td width="60" align="right"><? if($row["transaction_type"]==2 || $row["transaction_type"]==3 || $row["transaction_type"]==6) echo number_format($row["cons_rate"],2); ?></td>
											<td width="110" align="right"><? if($row["transaction_type"]==2 || $row["transaction_type"]==3 || $row["transaction_type"]==6) echo number_format($row["cons_amount"],2); ?></td>
											<?
											$each_pro_id=array();
											$total_balQnty=str_replace(",","",$total_balQnty);
											$total_balValue=str_replace(",","",$total_balValue);
											if($row["transaction_type"]==1 || $row["transaction_type"]==4 || $row["transaction_type"]==5) $total_balQnty +=$row["cons_quantity"]; 
											if($row["transaction_type"]==2 || $row["transaction_type"]==3 || $row["transaction_type"]==6) $total_balQnty -=$row["cons_quantity"]; 

											if($row["transaction_type"]==1 || $row["transaction_type"]==4 || $row["transaction_type"]==5)  $total_balValue += $row["cons_amount"];
											if($row["transaction_type"]==2 || $row["transaction_type"]==3 || $row["transaction_type"]==6)  $total_balValue -= $row["cons_amount"];

											$bal_rate=$total_balValue/$total_balQnty;
											?> 
											<td width="80" align="right"><? echo number_format($total_balQnty,2); ?></td>
											<td width="60" align="right"><? echo number_format($bal_rate,2); ?></td>
											<td width="" align="right"><? echo number_format($total_balValue,2); ?></td>             
										</tr>
										<?
										$k++;
									}
									$i++;

									//total sum START-----------------------
									if($row["transaction_type"]==1 || $row["transaction_type"]==4 || $row["transaction_type"]==5) $rcvQnty += $row["cons_quantity"];
									if($row["transaction_type"]==1 || $row["transaction_type"]==4 || $row["transaction_type"]==5) $rcvValue += $row["cons_amount"];

									if($row["transaction_type"]==2 || $row["transaction_type"]==3 || $row["transaction_type"]==6) $issQnty += $row["cons_quantity"];
									if($row["transaction_type"]==2 || $row["transaction_type"]==3 || $row["transaction_type"]==6) $issValue += $row["cons_amount"];
								}
							}
						} 
						?> <!-- END FOREACH LOOP --> 
                        <tr class="tbl_bottom">
                            <td colspan="8" align="right">Total</td>
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
	}
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename) 
	{
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

if($action=="trans_ref")
{
	echo load_html_head_contents("Transaction Details", "../../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $trans_id."##".$prod_id."##".$trans_type;die;
	//print_r ($data);
	?>	
	<script>
	
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
		}
	</script>	
	<input type="button" value="Print" onClick="print_window()" style="width:100px; margin-left:400px;"  class="formbutton" /><br />
    <?
	if($trans_type==1)
	{
		$sql="select a.id, a.recv_number, a.company_id, a.item_category, a.receive_basis, a.receive_date, a.challan_no, a.booking_id, a.booking_no, a.store_id, a.knitting_source, a.knitting_company, a.location_id, a.yarn_issue_challan_no, a.buyer_id, a.fabric_nature from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.id=$trans_id group by a.id, a.recv_number, a.company_id, a.item_category, a.receive_basis, a.receive_date, a.challan_no, a.booking_id, a.booking_no, a.store_id, a.knitting_source, a.knitting_company, a.location_id, a.yarn_issue_challan_no, a.buyer_id, a.fabric_nature";
		//echo $sql;
		$dataArray=sql_select($sql);
		
		$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
		$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
		$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name");
		$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name");
		$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name");
		$wo_arr=return_library_array( "select id, booking_no from  wo_booking_mst", "id", "booking_no");
		$pi_arr=return_library_array( "select id, pi_number from  com_pi_master_details", "id", "pi_number");
		$po_arr=return_library_array( "select id, job_no from  wo_po_details_master", "id", "job_no");
		$job_arr=return_library_array( "select id, job_no from  wo_booking_mst", "id", "job_no");
		$location_arr=return_library_array( "select id, location_name from lib_location", "id", "location_name");
		$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
		$machine_arr = return_library_array("select id, machine_no from lib_machine_name","id","machine_no");
		
		$program_no="";
		if($dataArray[0][csf('receive_basis')]==9)
		{
			$program_no=return_field_value("booking_id","inv_receive_master","id=".$dataArray[0][csf('booking_id')]." and entry_form=2 and receive_basis=2");
		}
	
		?>	
		<div style="width:930px;" id="report_container">
			<table width="900" cellspacing="0">
				<tr>
					<td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
				</tr>
				<tr class="form_caption">
					<td colspan="6" align="center" style="font-size:14px">  
						<?
							$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=".$dataArray[0][csf('company_id')].""); 
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
					<td colspan="6" align="center" style="font-size:16px"><strong><u><? echo $data[2]; ?> Report</u></strong></td>
				</tr>
				<tr>
					<td width="125"><strong>Receive ID :</strong></td><td width="175px"><? echo $dataArray[0][csf('recv_number')]; ?></td>
					<td width="130"><strong>Receive Basis :</strong></td> <td width="175px"><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
					<td width="125"><strong>Receive Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
				</tr>
				<tr>
					<td><strong>Rec. Chal. No :</strong></td><td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
					<td><strong><? $show_label=""; if($dataArray[0][csf('item_category')]==13) echo $show_label='WO/PI/Prod: '; else echo $show_label='WO/PI: '; ?></strong></td><td width="175px"><? if ($dataArray[0][csf('receive_basis')]==1 ) echo $pi_arr[$dataArray[0][csf('booking_id')]]; else echo $dataArray[0][csf('booking_no')]; ?></td>
					<td><strong>Store:</strong></td> <td width="175px"><? echo $store_library[$dataArray[0][csf('store_id')]]; ?></td>
				</tr>
				<tr>
					<td><strong>Knit. Source :</strong></td><td width="175px"><? echo $knitting_source[$dataArray[0][csf('knitting_source')]]; ?></td>
					<td><strong>Knit. Com:</strong></td><td width="175px"><? if ($dataArray[0][csf('knitting_source')]==1) echo $company_library[$dataArray[0][csf('knitting_company')]]; else if($dataArray[0][csf('knitting_source')]==3) echo $supplier_library[$dataArray[0][csf('knitting_company')]];  ?></td>
					<td><strong>Knit. Location:</strong></td> <td width="175px"><? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></td>
				</tr>
				<tr>
					<td><strong>Issue Chal. No:</strong></td> <td width="175px"><? echo $dataArray[0][csf('yarn_issue_challan_no')]; ?></td>
					<?
						$job_no='';
						if($dataArray[0][csf('receive_basis')]==2)
						{
							$job_no=$job_arr[$dataArray[0][csf('booking_id')]];
						}
						else if($dataArray[0][csf('receive_basis')]==9)
						{
							$prodData=sql_select("select receive_basis, booking_id, booking_without_order from inv_receive_master where id='".$dataArray[0][csf('booking_id')]."'");
							$receive_basis=$prodData[0][csf('receive_basis')];
							$booking_plan_id=$prodData[0][csf('booking_id')];
							$booking_without_order=$prodData[0][csf('booking_without_order')];
							
							if($receive_basis==1 && $booking_without_order==0)
							{
								$job_no=return_field_value("job_no","wo_booking_mst","id='".$booking_plan_id."'","job_no");
							}
							else if($receive_basis==2)
							{
								$job_no=return_field_value("c.job_no as job_no","ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, wo_booking_mst c","a.id=b.mst_id and a.booking_no=c.booking_no and b.id='".$booking_plan_id."'","job_no");
							}
						}
					?>
					<td><strong>Job No:</strong></td><td width="175px"><? echo $job_no; ?></td>
					<td><strong>Buyer:</strong></td><td width="175px"><? echo $buyer_library[$dataArray[0][csf('buyer_id')]]; ?></td>
				</tr>
				<tr>
					<td><strong>Program No:</strong></td><td width="175px"><? echo $program_no; ?></td>
					<td>&nbsp;</td><td width="175px">&nbsp;</td>
					<td>&nbsp;</td><td width="175px">&nbsp;</td>
				</tr> 
			</table>
			<br>
			<div style="width:100%;">
				<table cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
					<thead bgcolor="#dddddd" align="center">
						<th width="30">SL</th>
						<th width="100" >Body Part</th>
						<th width="160" >Feb. Description</th>
						<th width="40" >GSM</th>
						<th width="50" >Dia/ Width</th>
						<th width="40" >UOM</th> 
						<th width="70" >Grey Qnty</th>
						<th width="70" >Reject Qnty</th>
						<th width="60" >Yarn Lot</th>
						<th width="50" >No of Roll</th>
						<th width="80" >Brand</th>
						<th width="60" >Shift Name</th> 
						<th width="70" >Machine No</th>
					</thead>
					<tbody> 
						<?
						$composition_arr=array();
						$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
						$data_array=sql_select($sql_deter);
						if(count($data_array)>0)
						{
							foreach( $data_array as $row )
							{
								if(array_key_exists($row[csf('id')],$composition_arr))
								{
									$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
								}
								else
								{
									$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
								}
							}
						}
					
						$sql_dtls="select id, body_part_id, febric_description_id, gsm, width, grey_receive_qnty, reject_fabric_receive, uom, yarn_lot, no_of_roll, brand_id, shift_name, machine_no_id from pro_grey_prod_entry_dtls where trans_id=$trans_id and prod_id=$prod_id and status_active = '1' and is_deleted = '0'";
					
						$sql_result= sql_select($sql_dtls);
						$i=1;
						$group_arr=return_library_array( "select id, item_name from  lib_item_group", "id", "item_name"  );
						foreach($sql_result as $row)
						{
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
								
							$grey_receive_qnty=$row[csf('grey_receive_qnty')];
							$grey_receive_qnty_sum += $grey_receive_qnty;
							
							$reject_fabric_receive=$row[csf('reject_fabric_receive')];
							$reject_fabric_receive_sum += $reject_fabric_receive;
							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td align="center"><? echo $i; ?></td>
								<td><? echo $body_part[$row[csf("body_part_id")]]; ?></td>
								<td><? echo $composition_arr[$row[csf("febric_description_id")]]; ?></td>
								<td><? echo $row[csf("gsm")]; ?></td>
								<td><? echo $row[csf("width")]; ?></td>
								<td><? echo $unit_of_measurement[$row[csf("uom")]]; ?></td>
								<td align="right"><? echo $row[csf("grey_receive_qnty")]; ?></td>
								<td  align="right"><? echo $row[csf("reject_fabric_receive")]; ?></td>
								<td align="center"><? echo $row[csf("yarn_lot")]; ?></td>
								<td align="center"><? echo $row[csf("no_of_roll")]; ?></td>
								<td align="center"><? echo $brand_arr[$row[csf("brand_id")]]; ?></td>
								<td><? echo $shift_name[$row[csf("shift_name")]]; ?></td>
								<td align="center"><? echo $machine_arr[$row[csf("machine_no_id")]]; ?></td>
							</tr>
							<? $i++; 
						} 
						?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="6" align="right"><strong>Total :</strong></td>
							<td align="right"><?php echo $grey_receive_qnty_sum; ?></td>
							<td align="right" ><?php echo $reject_fabric_receive_sum; ?></td>
							<td colspan="5">&nbsp;</td>
						</tr>                           
					</tfoot>
				</table>
			</div>
		</div>         
		<?
	}
	else if($trans_type==2)
	{
		$sql="select a.id, a.issue_number, a.company_id, a.issue_date, a.issue_basis, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, a.booking_id, a.batch_no, a.buyer_id, a.challan_no, a.style_ref, a.order_id from  inv_issue_master a, inv_transaction b where a.id=b.mst_id and b.id=$trans_id group by a.id, a.issue_number, a.company_id, a.issue_date, a.issue_basis, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, a.booking_id, a.batch_no, a.buyer_id, a.challan_no, a.style_ref, a.order_id";
		//echo $sql;die;
		$supplier_library=array(); $supplier_short_library=array();
		$supplier_data=sql_select( "select id,supplier_name,short_name from lib_supplier");
		foreach ($supplier_data as $row)
		{
			$supplier_library[$row[csf('id')]]=$row[csf('supplier_name')];
			$supplier_short_library[$row[csf('id')]]=$row[csf('short_name')];
		}
		
		$dataArray=sql_select($sql);
		$grey_issue_basis=array(1=>"Booking",2=>"Independent",3=>"Knitting Plan");
		$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
		$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
		$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
		$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
		$batch_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no"  );
		$booking_arr=return_library_array( "select id, booking_no from  wo_booking_mst", "id", "booking_no"  );
		$booking_non_order_arr=return_library_array( "select id, booking_no from  wo_non_ord_samp_booking_mst", "id", "booking_no"  );
		$po_arr=return_library_array( "select id, po_number from  wo_po_break_down", "id", "po_number"  );
		$job_arr=return_library_array( "select id, job_no_mst from  wo_po_break_down","id","job_no_mst");
	 	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count",'id','yarn_count');
		$brand_arr = return_library_array("select id, brand_name from lib_brand",'id','brand_name');	
		
		?>
	    <div style="width:930px;" id="report_container">
		    <table width="900" cellspacing="0" style="margin-bottom:10px">
		        <tr>
		            <td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
		        </tr>
		        <tr class="form_caption">
		        	<td colspan="6" align="center" style="font-size:14px">  
						<?
							$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=".$dataArray[0][csf("company_id")]); 
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
		            <td colspan="6" align="center" style="font-size:18px"><strong><u><? echo $data[3]; ?> Challan</u></strong></td>
		        </tr>
		        <tr>
		            <td rowspan="3" colspan="2" width="300" valign="top"><strong>Dyeing Company:</strong> 
						<?
							$supp_add=$dataArray[0][csf('knit_dye_company')];
							$nameArray=sql_select( "select address_1,web_site,email,country_id from lib_supplier where id=$supp_add"); 
							foreach ($nameArray as $result)
							{ 
		                    	$address="";
								if($result!="") $address=$result['address_1'];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
							}
		              if ($dataArray[0][csf('knit_dye_source')]==1) echo $company_library[$dataArray[0][csf('knit_dye_company')]].'<br>'.'Address :- '.$address; else if ($dataArray[0][csf('knit_dye_source')]==3) echo $supplier_library[$dataArray[0][csf('knit_dye_company')]].'<br>'.'Address :- '.$address; ?></td>
		        	<td width="120"><strong>Issue ID :</strong></td><td width="175px"><? echo $dataArray[0][csf('issue_number')]; ?></td>
		            <td width="125"><strong>Issue Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
		        </tr>
		        <tr>
		            <td><strong>Issue Basis :</strong></td> <td width="175px"><? echo $grey_issue_basis[$dataArray[0][csf('issue_basis')]]; ?></td>
		            <td><strong>Issue Purpose:</strong></td><td width="175px"><? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
		        </tr>
		        <tr>
		            <td><strong>Dyeing Source:</strong></td><td width="175px"><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
		            <td><strong>F. Booking No:</strong></td><td width="175px">
						<? 
						if($dataArray[0][csf('issue_basis')]==1)
						{
							if(($dataArray[0][csf('issue_purpose')]==8 || $dataArray[0][csf('issue_purpose')]==3 || $dataArray[0][csf('issue_purpose')]==26 || $dataArray[0][csf('issue_purpose')]==29 || $dataArray[0][csf('issue_purpose')]==30 || $dataArray[0][csf('issue_purpose')]==31) && $dataArray[0][csf('issue_basis')]==1) 
							{
								echo $booking_non_order_arr[$dataArray[0][csf('booking_id')]]; 
							}
							else 
							{
								echo $booking_arr[$dataArray[0][csf('booking_id')]];
							}
						}
						else
						{
							echo $dataArray[0][csf('booking_id')];
						}
						?>
		        	</td>
		        </tr>
		        <tr>
		            <td><strong>Challan No:</strong></td><td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
		            <td><strong>Batch Number:</strong></td><td width="175px"><? echo $batch_arr[$dataArray[0][csf('batch_no')]]; ?></td>
		            <td><strong>Buyer Name:</strong></td><td width="175px"><? echo $buyer_library[$dataArray[0][csf('buyer_id')]]; ?></td>
		        </tr>
		        <tr>
		            <td>
		            <?
						if($db_type==0)
						{
							$po_id=return_field_value("group_concat(b.po_breakdown_id) as po_id","inv_grey_fabric_issue_dtls a, order_wise_pro_details b","a.id=b.dtls_id and a.mst_id=".$dataArray[0][csf("id")]." and b.entry_form=16 and b.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","po_id");
						}
						else
						{
							$po_id=return_field_value("LISTAGG(b.po_breakdown_id, ',') WITHIN GROUP (ORDER BY b.po_breakdown_id) as po_id","inv_grey_fabric_issue_dtls a, order_wise_pro_details b","a.id=b.dtls_id and a.mst_id=".$dataArray[0][csf("id")]." and b.entry_form=16 and b.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","po_id");
						}
						
						$po_exp=array_unique(explode(',',$po_id));
						$po_no=''; $job='';
						foreach($po_exp as $row)
						{
							if($po_no=='') $po_no=$po_arr[$row]; else $po_no.=', '.$po_arr[$row];
							if($job=='') $job=$job_arr[$row]; else $job.=','.$job_arr[$row];
						}
						
						$job=implode(",",array_unique(explode(',',$job)));
					?>
		            <strong>Job No:</strong></td>
		            <td width="175px" colspan="3"><? echo $job;//$job_arr[$dataArray[0][csf('order_id')]]; ?></td>
		            <td><strong>Style Ref.:</strong></td><td width="175px"><? echo $dataArray[0][csf('style_ref')]; ?></td>
		        </tr>
		        <tr>
		        	 <td><strong>Order No:</strong></td><td colspan="5"><? echo $po_no;//$po_arr[$dataArray[0][csf('order_id')]]; ?></td>
		        </tr>
		        <tr>
		            <td><strong>Bar Code:</strong></td><td colspan="3" id="barcode_img_id"></td>
		        </tr>
		    </table>
		    <div style="width:100%;">
			    <table cellspacing="0" width="930"  border="1" rules="all" class="rpt_table" >
			        <thead bgcolor="#dddddd" align="center">
			            <th width="20">SL</th>
			            <th width="50">Prog. No</th>
			            <th width="130">Item Des.</th>
			            <th width="50">Stich Length</th>
			            <th width="40">GSM</th>
			            <th width="40">Fin. Dia</th>
			            <th width="30">M/C Dia</th>
			            <th width="70">Color</th>
			            <th width="40">Roll</th>
			            <th width="70">Issue Qty</th> 
			            <th width="40">UOM</th>
			            <th width="50">Count</th>
			            <th width="40">Supplier</th>
			            <th width="50">Yarn Lot</th>
			            <th width="30">Rack</th>
			            <th width="30">Shelf</th>
			            <th width="80">Store</th> 
			            <th>Remarks</th> 
			        </thead>
			        <tbody> 
						<?
						$color_arr = return_library_array( "select id, color_name from lib_color",'id','color_name');
						$product_array=array();
						$product_sql = sql_select("select id, supplier_id,item_category_id,product_name_details,lot,gsm,dia_width,color,brand,unit_of_measure from product_details_master where item_category_id in(1,13)");
						foreach($product_sql as $row)
						{
							$product_array[$row[csf("id")]]['product_name_details']=$row[csf("product_name_details")];
							$product_array[$row[csf("id")]]['gsm']=$row[csf("gsm")];
							$product_array[$row[csf("id")]]['dia_width']=$row[csf("dia_width")];
							$product_array[$row[csf("id")]]['brand']=$row[csf("brand")];
							$product_array[$row[csf("id")]]['color']=$row[csf("color")];
							$product_array[$row[csf("id")]]['uom']=$row[csf("unit_of_measure")];
							
							if($row[csf("item_category_id")]==1)
							{
								$product_array[$row[csf("lot")].'l']['lot']=$row[csf("brand")];
								$product_array[$row[csf("lot")]]['supp']=$supplier_short_library[$row[csf("supplier_id")]];
							}
						}
						
						$sql_dtls = "select b.id, b.program_no, b.prod_id, a.booking_id, b.issue_qnty, b.no_of_roll, b.yarn_lot, b.yarn_count, b.store_name, b.color_id, b.rack, b.self, b.stitch_length,b.remarks from inv_issue_master a, inv_grey_fabric_issue_dtls b where a.id=b.mst_id and a.entry_form=16 and a.id=".$dataArray[0][csf("id")]." and b.prod_id=$prod_id and b.status_active=1 and b.is_deleted=0";
						//echo $sql;
						$sql_result= sql_select($sql_dtls);
						$i=1; $all_program_no='';
						foreach($sql_result as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							if($row[csf('program_no')]!='')
							{
								if ($all_program_no=='') $all_program_no=$row[csf('program_no')]; else $all_program_no.=', '.$row[csf('program_no')];
							}	
									
							$roll_qty_sum+=$row[csf('no_of_roll')];
							$issue_qnty_sum+=$row[csf('issue_qnty')];
							
							$item_des=explode(',',$product_array[$row[csf("prod_id")]]['product_name_details']);
							//print_r ($item_des);
							if($item_des[0]!='' && $item_des[1]!='')
							{
								$item_name_details=$item_des[0].', '.$item_des[1];
							}
							else
							{
								$item_name_details='';
							}
							
							$yarn_count=$row[csf("yarn_count")];
							$count_id=explode(',',$yarn_count);
							$count_val='';
							foreach ($count_id as $val)
							{
								if($count_val=='') $count_val=$yarn_count_arr[$val]; else $count_val.=", ".$yarn_count_arr[$val];
							}
							
							$yarn_lot=$row[csf("yarn_lot")];
							$yarn_lot_id=explode(',',$yarn_lot);
							$yarn_lot_supp='';
							foreach ($yarn_lot_id as $val)
							{
								if($yarn_lot_supp=='') $yarn_lot_supp=$product_array[$val]['supp']; else $yarn_lot_supp.=", ".$product_array[$val]['supp'];
							}
							
							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td align="center"><? echo $i; ?></td>
								<td><p><? if($row[csf("program_no")]==0) echo "&nbsp;"; else echo $row[csf("program_no")]; ?></p></td>
								<td><p><? echo $item_name_details; ?></p></td>
								<td><p><? echo $row[csf("stitch_length")]; ?></p></td>
								<td align="center"><p><? echo $product_array[$row[csf("prod_id")]]['gsm']; ?></p></td>
								<td align="center"><p><? echo $product_array[$row[csf("prod_id")]]['dia_width']; ?></p></td>
								<td align="center"><p><? //echo $row[csf("booking_id")]; ?></p></td>
								<td><p><? echo $color_arr[$row[csf("color_id")]]; ?></p></td>
								<td align="right"><? echo $row[csf("no_of_roll")]; ?></td>
								<td align="right"><? echo number_format($row[csf("issue_qnty")],2); ?></td>
								<td align="center"><p><? echo $unit_of_measurement[$product_array[$row[csf("prod_id")]]['uom']]; ?></p></td>
								<td align="center"><p><? echo $count_val; ?></p></td>
								<td><p><? echo $yarn_lot_supp; ?></p></td>
								<td><p><? echo $row[csf("yarn_lot")]; ?></p></td>
								<td align="center"><p><? echo $row[csf("rack")]; ?></p></td>
								<td align="center"><p><? echo $row[csf("self")]; ?></p></td>
								<td><p><? echo $store_library[$row[csf("store_name")]]; ?></p></td>
								<td><p><? echo $row[csf("remarks")]; ?></p></td>
							</tr>
							<? $i++; 
						} 
						?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="8" align="right"><strong>Total :</strong></td>
							<td align="right"><?php echo $roll_qty_sum; ?></td>
							<td align="right"><?php echo number_format($issue_qnty_sum,2); ?></td>
							<td align="right" colspan="8"><?php //echo $req_qny_edit_sum; ?></td>
						</tr>                           
					</tfoot>
				</table>
			    <br>
			    <br>&nbsp;
			    <!--================================================================-->
			    <? 
			    if($dataArray[0][csf('issue_basis')]==3)
			    {
			    	?>
			    	<table width="850" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">
			    		<thead>
			    			<tr>
			    				<th colspan="7" align="center">Requisition Details</th>
			    			</tr>
			    			<tr>
			    				<th width="40">SL</th>
			    				<th width="100">Requisition No</th>
			    				<th width="110">Lot No</th>
			    				<th width="220">Yarn Description</th>
			    				<th width="110">Brand</th>
			    				<th width="90">Requisition Qty</th>
			    				<th>Remarks</th>
			    			</tr>
			    		</thead>
			    		<?
			    		$i=1; $tot_reqsn_qnty=0;
			    		$product_details_array=array();
			    		$sql_prod="select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=13 and company_id=".$dataArray[0][csf("company_id")]." and status_active=1 and is_deleted=0";
			    		$result_prod = sql_select($sql_prod);

			    		foreach($result_prod as $row)
			    		{
			    			$compos='';
			    			if($row[csf('yarn_comp_percent2nd')]!=0)
			    			{
			    				$compos=$composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]."%"." ".$composition[$row[csf('yarn_comp_type2nd')]]." ".$row[csf('yarn_comp_percent2nd')]."%";
			    			}
			    			else
			    			{
			    				$compos=$composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]."%"." ".$composition[$row[csf('yarn_comp_type2nd')]];
			    			}
			    			$product_details_array[$row[csf('id')]]['count']=$count_arr[$row[csf('yarn_count_id')]];
			    			$product_details_array[$row[csf('id')]]['comp']=$compos;
			    			$product_details_array[$row[csf('id')]]['type']=$yarn_type[$row[csf('yarn_type')]];
			    			$product_details_array[$row[csf('id')]]['lot']=$row[csf('lot')];
			    			$product_details_array[$row[csf('id')]]['brand']=$brand_arr[$row[csf('brand')]];
			    		}	

			    		$sql_knit="select knit_id, requisition_no, prod_id, sum(yarn_qnty) as yarn_qnty from ppl_yarn_requisition_entry where knit_id in ($all_program_no) and status_active=1 and is_deleted=0 group by requisition_no, prod_id, knit_id";
			    		$nameArray=sql_select( $sql_knit );
			    		foreach ($nameArray as $selectResult)
			    		{
			    			?>
			    			<tr>
			    				<td width="40" align="center"><? echo $i; ?></td>
			    				<td width="100" align="center">&nbsp;<? echo $selectResult[csf('requisition_no')]; ?></td>
			    				<td width="110" align="center">&nbsp;<? echo $product_details_array[$selectResult[csf('prod_id')]]['lot']; ?></td>
			    				<td width="220">&nbsp;<? echo $product_details_array[$selectResult[csf('prod_id')]]['count']." ".$product_details_array[$selectResult[csf('prod_id')]]['comp']." ".$product_details_array[$selectResult[csf('prod_id')]]['type']; ?></td>
			    				<td width="110" align="center">&nbsp;<? echo $brand_arr[$product_details_array[$selectResult[csf('prod_id')]]['brand']]; ?></td>
			    				<td width="90" align="right"><? echo number_format($selectResult[csf('yarn_qnty')],2); ?>&nbsp;</td>
			    				<td>&nbsp;<? //echo $selectResult[csf('requisition_no')]; ?></td>	
			    			</tr>
			    			<?
			    			$tot_reqsn_qnty+=$selectResult[csf('yarn_qnty')];
			    			$i++;
			    		}
			    		?>
			    		<tfoot>
			    			<th colspan="5" align="right"><b>Total</b></th>
			    			<th align="right"><? echo number_format($tot_reqsn_qnty,2); ?>&nbsp;</th>
			    			<th>&nbsp;</th>
			    		</tfoot>
			    	</table>
			    	<?
			    }
			    else if($dataArray[0][csf('issue_basis')]==1)
			    {
			    	if($dataArray[0][csf('issue_purpose')]==8 )
			    	{
			    		$sql = "select a.id, a.booking_no, sum(b.grey_fabric) as fabric_qty from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id='".$dataArray[0][csf('booking_id')]."' group by a.id, a.booking_no"; 
			    	}
			    	else if( $dataArray[0][csf('issue_purpose')]==2)
			    	{					
			    		$sql = "select a.id, a.ydw_no as booking_no, sum(b.yarn_wo_qty) as fabric_qty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id='".$dataArray[0][csf('booking_id')]."' group by a.id, a.ydw_no"; 
			    	}
			    	else
			    	{
			    		$sql = "select a.id, a.booking_no, sum(b.grey_fab_qnty) as fabric_qty from wo_booking_mst a,  wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id='".$dataArray[0][csf('booking_id')]."' group by a.id, a.booking_no"; 
			    	}
			    	$result = sql_select($sql);

			    	$total_issue_qty=return_field_value("sum(a.cons_quantity) as total_issue_qty"," inv_transaction a, inv_issue_master b","b.id=a.mst_id and b.booking_id='".$dataArray[0][csf('booking_id')]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=2 and b.item_category=13","total_issue_qty");		 
			    	$total_return_qty=return_field_value("sum(a.cons_quantity) as total_issue_qty"," inv_transaction a, inv_receive_master b","b.id=a.mst_id and b.booking_id='".$dataArray[0][csf('booking_id')]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.transaction_type=4 and b.item_category=13","total_issue_qty");		 
			    	?>  
			    	<table style="margin-top:20px;" width="500" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">
			    		<thead>
			    			<tr><th colspan="4" align="center">Comments (Booking : <? echo $result[0][csf('booking_no')]; ?>)</th></tr>
			    			<tr>
			    				<th>Req. Qty</th>
			    				<th>Cuml. Issue Qty</th>
			    				<th>Balance Qty</th>
			    				<th>Remarks</th>
			    			</tr>
			    		</thead>

			    		<tbody>
			    			<tr>
			    				<td align="center">
			    					<? echo number_format($result[0][csf('fabric_qty')],3); ?>
			    				</td>
			    				<td align="center">
			    					<? $cumulative_qty=$total_issue_qty-$total_return_qty; echo number_format($cumulative_qty,3); ?>
			    				</td>
			    				<td align="center">
			    					<? $balance_qty=$result[0][csf('fabric_qty')]-$cumulative_qty; echo number_format($balance_qty,3);?>
			    				</td>
			    				<td align="center">
			    					<? if($result[0][csf('fabric_qty')]>$cumulative_qty) echo "Less"; else if ($result[0][csf('fabric_qty')]<$cumulative_qty) echo "Over"; else echo ""; ?>
			    				</td>
			    			</tr>
			    		</tbody>
			    	</table>
			    	<?
			    }
			    ?>
			</div>
		</div>          
		<?
	}
	exit();
}

?>


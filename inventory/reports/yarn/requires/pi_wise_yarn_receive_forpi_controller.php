<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$store_arr=return_library_array( "select id, store_name from lib_store_location", "id", "store_name"  );
$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');

if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 150, "select a.id, a.store_name from lib_store_location a,lib_store_location_category b  where a.id=b.store_location_id and a.company_id='$data' and b.category_type in(1) and a.status_active=1 and a.is_deleted=0 order by a.store_name","id,store_name", 1, "-- Select Store --", 0, "" );
	exit();
}

if($action=="pinumber_popup")
{
	echo load_html_head_contents("PI Number Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);  
	?>

	<script>
		function js_set_value(str)
		{
			var splitData = str.split("_");		 
			$("#pi_id").val(splitData[0]); 
			$("#pi_no").val(splitData[1]); 
			parent.emailwindow.hide();
		}


	</script>

</head>

<body>
	<div align="center" style="width:100%; margin-top:5px" >
		<form name="searchlcfrm_1" id="searchlcfrm_1" autocomplete="off">
			<table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
				<thead>
					<tr>                	 
						<th>Supplier</th>
						<th>PI Year</th>
						<th id="search_by_td_up">Enter PI Number</th>
						<th>Enter PI Date</th>
						<th>
							<input type="reset" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('searchlcfrm_1','search_div','','','','');" />
							<input type="hidden" id="pi_id" value="" />
							<input type="hidden" id="pi_no" value="" />
						</th>           
					</tr>
				</thead>
				<tbody>
					<tr align="center">
						<td>
							<?  
							echo create_drop_down( "cbo_supplier_id", 150,"select DISTINCT(c.id),c.supplier_name from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$companyID' and b.party_type=2",'id,supplier_name', 1, '-- All Supplier --',0,'',0);
							?>
						</td>
						<td>
							<?  
							echo create_drop_down( "cbo_year", 65, create_year_array(),'', 1, '-- All Year--', date("Y", time()),'',0);
							?>
						</td>
						<td align="center" id="search_by_td">				
							<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
						</td> 
						<td align="center" id="search_by_td">				
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px;" placeholder="From Date" readonly />
							To
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px;" placeholder="To Date" readonly />
						</td> 
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier_id').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $companyID; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year').value, 'create_pi_search_list_view', 'search_div', 'pi_wise_yarn_receive_forpi_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
						</td>
					</tr>
					<tr>
						<td colspan="4" align="center"><? echo load_month_buttons(1); ?></td>
					</tr>
				</tbody>         
			</table>    
			<div align="center" style="margin-top:10px" id="search_div"> </div> 
		</form>
	</div>
</body>           
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?

}

if($action=="create_pi_search_list_view")
{
	$ex_data = explode("_",$data);
	
	if($ex_data[0]==0) $cbo_supplier = "%%"; else $cbo_supplier = $ex_data[0];
	$txt_search_common = trim($ex_data[1]);
	$company = $ex_data[2];
	$from_date = $ex_data[3];
	$to_date = $ex_data[4];
	$pi_year = $ex_data[5];
	if( $from_date!="" && $to_date!="")
	{
		if($db_type==0)
		{
			$pi_date_cond= " and pi_date between '".change_date_format($from_date,"yyyy-mm-dd")."' and '".change_date_format($to_date,"yyyy-mm-dd")."'";
		}
		else
		{
			$pi_date_cond= " and pi_date between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";	
		}
	}
	else $pi_date_cond=""; 

	if($pi_year!=0)
	{
		if($db_type==0)
		{
			$pi_year_cond=" and year(pi_date)=".$pi_year.""; 
		}
		else
		{
			$pi_year_cond=" and to_char(pi_date,'YYYY')=".$pi_year."";
		}
	}
	else
	{
		$pi_year_cond="";
	}

	$sql= "select id, pi_number, supplier_id, importer_id,EXTRACT(year FROM pi_date) as pi_year, pi_date, last_shipment_date, total_amount from com_pi_master_details where importer_id=$company and item_category_id=1 and supplier_id like '$cbo_supplier' and pi_number like '%".$txt_search_common."%' and is_deleted=0 and status_active=1 $pi_date_cond $pi_year_cond";

	$arr=array(1=>$company_arr,2=>$supplier_arr);
	echo create_list_view("list_view", "PI No, Importer, Supplier Name, PI Date, PI Year, Last Shipment Date, PI Value","130,110,130,70,50,110","780","260",0, $sql , "js_set_value", "id,pi_number", "", 1, "0,importer_id,supplier_id,0,0,0,0,0", $arr, "pi_number,importer_id,supplier_id,pi_date,pi_year,last_shipment_date,total_amount", "",'','0,0,0,3,0,3,2') ;	
	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$pi_no_cond=str_replace("'","",$txt_pi_no);
	$btbLc_id_str=str_replace("'","",$btbLc_id);
	
	if(str_replace("'","",$cbo_store_name)==0) $store="%%"; else $store=str_replace("'","",$cbo_store_name);
	if(str_replace("'","",$txt_pi_no)=='') $pi_cond=""; else $pi_cond="and b.pi_number='$pi_no_cond'";	

	ob_start();
	if(str_replace("'","",$type)==1)
	{
		?>
		<fieldset style="width:1250px">
			<div style="width:100%; margin-left:10px;" align="left">
				<table width="1230" cellpadding="0" cellspacing="0" style="visibility:hidden; border:none" id="caption">
					<tr>
						<td align="center" width="100%" colspan="11" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
					</tr>
				</table>

				<br>
				<table width="1230" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">            	
					<thead>
						<tr>
							<th colspan="12">PI Details</th>
						</tr>
						<tr>
							<th width="30">SL</th>
							<th width="120">PI Number</th>
							<th width="80">PI Date</th>
							<th width="130">Supplier</th>
							<th width="70">Count</th>
							<th width="150">Composition</th>
							<th width="80">Type</th>
							<th width="90">Color</th>
							<th width="110">Qnty</th>
							<th width="90">Rate</th>                            
							<th width="120">Value</th>
							<th>Remarks</th>
						</tr>
					</thead>
					<? 	
					$i=1; $pi_id_all_array=array(); $pi_name_array=array(); $rate=0; $compos=''; $tot_pi_qnty=0; $tot_pi_amnt=0;
					
					$sql="select b.id, b.pi_number, b.pi_date,b.supplier_id, b.remarks, c.color_id, c.count_name, c.yarn_composition_item1, c.yarn_composition_percentage1, c.yarn_composition_item2, c.yarn_composition_percentage2, c.yarn_type, c.quantity as qnty, c.net_pi_amount as amnt, b.goods_rcv_status, c.work_order_no, c.work_order_id 
					from com_pi_master_details b, com_pi_item_details c 
					where b.id=c.pi_id and b.item_category_id=1 and b.importer_id=$cbo_company_name  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $pi_cond";
					//echo $sql;
					$result=sql_select($sql);
					foreach($result as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

						$rate=$row[csf('amnt')]/$row[csf('qnty')];

						$compos=$composition[$row[csf('yarn_composition_item1')]]." ".$row[csf('yarn_composition_percentage1')]."%";

						if($row[csf('yarn_composition_percentage2')]>0)
						{
							$compos.=" ".$composition[$row[csf('yarn_composition_item2')]]." ".$row[csf('yarn_composition_percentage2')]."%";
						}
						$ac_pi_id[$row[csf('id')]]=$row[csf('id')];
						if($row[csf('goods_rcv_status')]==2)
						{
							$pi_id_all_array[$row[csf('id')]]=$row[csf('id')];
							$pi_name_array[$row[csf('id')]]=$row[csf('pi_number')];
						}
						else
						{
							$book_id_all_array[$row[csf('work_order_id')]]=$row[csf('work_order_id')];
							$btb_pi_id_array[$row[csf('work_order_id')]]=$row[csf('id')];
							$pi_name_array[$row[csf('id')]]=$row[csf('pi_number')];
						}
						?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
							<td width="30"><? echo $i; ?></td>
							<td width="120"><p><? echo $row[csf('pi_number')]; ?></p></td>
							<td width="80" align="center"><? echo change_date_format($row[csf('pi_date')]); ?></td>
							<td width="130" align="center"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></p></td>
							<td width="70" align="center"><p>&nbsp;<? echo $count_arr[$row[csf('count_name')]]; ?></p></td>
							<td width="150"><p><? echo $compos; ?></p></td>
							<td width="80"><p>&nbsp;<? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
							<td width="90"><p>&nbsp;<? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td width="110" align="right"><? echo number_format($row[csf('qnty')],2,'.',''); ?>&nbsp;</td>
							<td width="90" align="right"><? echo number_format($rate,4,'.',''); ?>&nbsp;</td>
							<td width="120" align="right"><? echo number_format($row[csf('amnt')],2,'.',''); ?>&nbsp;</td>
							<td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
						</tr>
						<?

						$tot_pi_qnty+=$row[csf('qnty')]; 
						$tot_pi_amnt+=$row[csf('amnt')];
						
						$i++;
					}
					
					?>
					<tfoot>
						<th colspan="8" align="right">Total</th>
						<th align="right"> <?php echo  number_format($tot_pi_qnty,2,'.','');?></th>
						<th>&nbsp;</th>
						<th align="right"> <?php echo  number_format($tot_pi_amnt,2,'.','');?></th>
						<th>&nbsp;</th>
					</tfoot>
				</table>
				<br>
				<table width="1100" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
					<thead>
						<tr>
							<th colspan="12">Yarn Receive</th>
						</tr>
						<tr>	
							<th width="80">Recv. Date</th>
							<th width="110">MRR No</th>
							<th width="80">Challan No</th>
							<th width="80">Lot No</th>
							<th width="70">Count</th>            
							<th width="130">Composition</th>
							<th width="80">Type</th>
							<th width="80">Color</th>
							<th width="90">Qnty</th>
							<th width="70">Rate</th>
							<th width="100">Value</th>
							<th>Remarks</th>
						</tr>
					</thead>
					<?
					$pi_id_all=implode(",",$pi_id_all_array);
					$book_id_all=implode(",",$book_id_all_array);  
					$compos=''; $tot_recv_qnty=0; $tot_recv_amnt=0; $recv_id_array=array(); $recv_pi_array=array(); $pi_data_array=array();$receive_trans_id_arr=array();

					if ($pi_id_all!='' || $pi_id_all!=0)
					{
						$sql_recv="select a.id, a.recv_number, a.receive_date, a.challan_no,b.id trans_id,b.pi_wo_batch_no, (b.order_rate+b.order_ile_cost) as rate, b.order_qnty, b.order_amount, c.yarn_count_id, c.yarn_type, c.lot, c.color, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, a.receive_basis 
						from inv_receive_master a, inv_transaction b, product_details_master c 
						where a.item_category=1 and a.entry_form=1 and a.company_id=$cbo_company_name and a.receive_basis=1 and a.booking_id in($pi_id_all) and a.id=b.mst_id and b.item_category=1 and b.transaction_type=1 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.store_id like '$store' order by a.id"; 

						$dataArray=sql_select($sql_recv);
					}
					elseif ($book_id_all!='' || $book_id_all!=0)
					{
						$sql_recv="select a.id, a.recv_number, a.receive_date, a.challan_no,b.id trans_id,b.pi_wo_batch_no, (b.order_rate+b.order_ile_cost) as rate, b.order_qnty, b.order_amount, c.yarn_count_id, c.yarn_type, c.lot, c.color, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, a.receive_basis 
						from inv_receive_master a, inv_transaction b, product_details_master c 
						where a.item_category=1 and a.entry_form=1 and a.company_id=$cbo_company_name and a.receive_basis=2 and a.booking_id in($book_id_all) and a.id=b.mst_id and b.item_category=1 and b.transaction_type=1 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.store_id like '$store' 
						order by a.id"; 

						$dataArray=sql_select($sql_recv);
					}
					
					
					foreach($dataArray as $row_recv)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

						$compos=$composition[$row_recv[csf('yarn_comp_type1st')]]." ".$row_recv[csf('yarn_comp_percent1st')]."%";

						if($row_recv[csf('yarn_comp_percent2nd')]>0)
						{
							$compos.=" ".$composition[$row_recv[csf('yarn_comp_type2nd')]]." ".$row_recv[csf('yarn_comp_percent2nd')]."%";
						}

						if($row_recv[csf('receive_basis')]==1)
						{
							$pi_data_array[$row_recv[csf('pi_wo_batch_no')]]['rcv']+=$row_recv[csf('order_amount')];
							if(!in_array($row_recv[csf('id')],$recv_id_array))
							{
								$recv_id_array[$row_recv[csf('id')]]=$row_recv[csf('id')];
								$recv_pi_array[$row_recv[csf('id')]]=$row_recv[csf('pi_wo_batch_no')];
							}
						}
						else
						{
							$pi_data_array[$btb_pi_id_array[$row_recv[csf('pi_wo_batch_no')]]]['rcv']+=$row_recv[csf('order_amount')];
							if(!in_array($row_recv[csf('id')],$recv_id_array))
							{
								$recv_id_array[$row_recv[csf('id')]]=$row_recv[csf('id')];
								$recv_pi_array[$row_recv[csf('id')]]=$btb_pi_id_array[$row_recv[csf('pi_wo_batch_no')]];
							}
						}
						
						?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
							<td width="80" align="center"><? echo change_date_format($row_recv[csf('receive_date')]); ?></td>
							<td width="110"><p><? echo $row_recv[csf('recv_number')]; ?></p></td>
							<td width="80"><p>&nbsp;<? echo $row_recv[csf('challan_no')]; ?></p></td>
							<td width="80"><p>&nbsp;<? echo $row_recv[csf('lot')]; ?></p></td>
							<td width="70" align="center"><p>&nbsp;<? echo $count_arr[$row_recv[csf('yarn_count_id')]]; ?></p></td>
							<td width="130"><p><? echo $compos; ?></p></td>
							<td width="80"><p>&nbsp;<? echo $yarn_type[$row_recv[csf('yarn_type')]]; ?></p></td>
							<td width="80"><p>&nbsp;<? echo $color_arr[$row_recv[csf('color')]]; ?></p></td>
							<td width="90" align="right"><? echo number_format($row_recv[csf('order_qnty')],2,'.',''); ?>&nbsp;</td>
							<td width="70" align="right"><? echo number_format($row_recv[csf('rate')],2,'.',''); ?>&nbsp;</td>
							<td width="100" align="right"><? echo number_format($row_recv[csf('order_amount')],2,'.',''); ?>&nbsp;</td>
							<td><p>&nbsp;</p></td>
						</tr>
						<?

						$tot_recv_qnty+=$row_recv[csf('order_qnty')]; 
						$tot_recv_amnt+=$row_recv[csf('order_amount')];
						$receive_trans_id_arr[] = $row_recv[csf('trans_id')];
						$i++;
					}
					?>
					<tfoot>
						<th colspan="8" align="right">Total</th>
						<th align="right"> <?php echo  number_format($tot_recv_qnty,2,'.','');?></th>
						<th>&nbsp;</th>
						<th align="right"> <?php echo  number_format($tot_recv_amnt,2,'.','');?></th>
						<th>&nbsp;</th>
					</tfoot>
				</table>
				<br>
				<table width="1100" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
					<thead>
						<tr>
							<th colspan="7">Yarn Return</th>
						</tr>
						<tr>	
							<th width="100">Return Date</th>
							<th width="130">Return No</th>
							<th width="290">Item Description</th>
							<th width="110">Qnty</th>
							<th width="100">Rate</th>
							<th width="120">Value</th>
							<th>Remarks</th>
						</tr>
					</thead>
					<?
					//print_r($recv_id_array);echo test;die;  
					$tot_retn_qnty=0; $tot_retn_amnt=0;
					if(count($recv_id_array)>0)
					{
						$ac_pi_ids = implode(",",$ac_pi_id);
						$sql_retn="select a.pi_id,a.received_id, a.issue_number, a.issue_date, a.challan_no, (b.order_rate+b.order_ile_cost) as rate, b.cons_quantity, b.cons_amount, c.product_name_details 
						from inv_issue_master a, inv_transaction b, product_details_master c 
						where a.item_category=1 and a.entry_form=8 and a.company_id=$cbo_company_name and a.id=b.mst_id and b.item_category=1 and b.transaction_type=3 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pi_id in($ac_pi_ids)";

						$dataRtArray=sql_select($sql_retn);
						$amnt = 0;
						foreach($dataRtArray as $row_retn)
						{
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";

							$amnt=$row_retn[csf('cons_quantity')]*$row_retn[csf('rate')];
							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
								<td width="100" align="center"><? echo change_date_format($row_retn[csf('issue_date')]); ?></td>
								<td width="130"><p><? echo $row_retn[csf('issue_number')]; ?></p></td>
								<td width="290"><p>&nbsp;<? echo $row_retn[csf('product_name_details')]; ?></p></td>
								<td width="110" align="right"><? echo number_format($row_retn[csf('cons_quantity')],2,'.',''); ?>&nbsp;</td>
								<td width="100" align="right"><? echo number_format($row_retn[csf('rate')],2,'.',''); ?>&nbsp;</td>
								<td width="120" align="right"><? echo number_format($amnt,2,'.',''); ?>&nbsp;</td>
								<td><p>&nbsp;</p></td>
							</tr>
							<?
							//$pi_id=$recv_pi_array[$row_retn[csf('received_id')]];
							$tot_retn_qnty+=$row_retn[csf('cons_quantity')]; 
							$tot_retn_amnt+=$amnt;
							$pi_data_array[$row_retn[csf('pi_id')]]['rtn']+=$amnt;
							$i++;
						}
					}

					$total_balance_qty = ($tot_pi_qnty+$tot_retn_qnty-$tot_recv_qnty); 
					$total_balance_value = ($tot_pi_amnt+$tot_retn_amnt-$tot_recv_amnt);
					?>
					<tfoot>
						<tr>
							<th colspan="3" align="right">Total</th>
							<th align="right"><?php echo number_format($tot_retn_qnty,2,'.','');?></th>
							<th>&nbsp;</th>
							<th align="right"><?php echo number_format($tot_retn_amnt,2,'.','');?></th>
							<th>&nbsp;</th>
						</tr>
						<tr>
							<th colspan="3" align="right">Balance</th>
							<th align="right"><?php echo number_format($total_balance_qty,2,'.','');?></th>
							<th>&nbsp;</th>
							<th align="right"><?php echo number_format($total_balance_value,2,'.','');?></th>
							<th>&nbsp;</th>
						</tr>
					</tfoot>
				</table>
				<br>
				<table width="850" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
					<thead>
						<th width="140">PI Number</th>
						<th width="130">Receive Value</th>
						<th width="130">Return Value</th>
						<th width="130">Payable Value</th>
						<th width="130">Acceptance Given</th>            
						<th>Yet To Accept</th>
					</thead>
					<?
					
					$ac_pi_id=implode(",",$ac_pi_id);
					//echo $ac_pi_id.test;
					if ($ac_pi_id!='' || $ac_pi_id!=0)
					{
						$acceptance_arr=return_library_array( "select pi_id, sum(current_acceptance_value) as acceptance_value from com_import_invoice_dtls where pi_id in($ac_pi_id) and is_lc=1 and status_active=1 and is_deleted=0 group by pi_id", "pi_id", "acceptance_value"  );
					}

					$payble_value=0; $tot_payble_value=0; $yet_to_accept=0; $tot_accept_value=0; $tot_yet_to_accept=0; $total_receive_value=0; $total_return_value=0;
					foreach($pi_name_array as $key=>$value)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

						$payble_value=$pi_data_array[$key]['rcv']-$pi_data_array[$key]['rtn'];
						$yet_to_accept=$payble_value-$acceptance_arr[$key];

						$total_receive_value+=$pi_data_array[$key]['rcv'];
						$total_return_value+=$pi_data_array[$key]['rtn'];

						$tot_payble_value+=$payble_value;
						$tot_accept_value+=$acceptance_arr[$key];
						$tot_yet_to_accept+=$yet_to_accept;

						?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
							<td width="140"><p><? echo $value; ?></p></td>
							<td width="130" align="right"><? echo number_format($pi_data_array[$key]['rcv'],2,'.',''); ?>&nbsp;</td>
							<td width="130" align="right"><? echo number_format($pi_data_array[$key]['rtn'],2,'.',''); ?>&nbsp;</td>
							<td width="130" align="right"><? echo number_format($payble_value,2,'.',''); ?>&nbsp;</td>
							<td width="130" align="right"><? echo number_format($acceptance_arr[$key],2,'.',''); ?>&nbsp;</td>
							<td align="right"><? echo number_format($yet_to_accept,2,'.',''); ?></td>
						</tr>
						<?
						$i++;
					}
					?>
					<tfoot>
						<th align="right">Total</th>
						<th align="right"><?php echo number_format($total_receive_value,2,'.',''); ?></th>
						<th align="right"><?php echo number_format($total_return_value,2,'.',''); ?></th>
						<th align="right"><?php echo number_format($tot_payble_value,2,'.',''); ?></th>
						<th align="right"><?php echo number_format($tot_accept_value,2,'.',''); ?></th>
						<th align="right"><?php echo number_format($tot_yet_to_accept,2,'.',''); ?></th>
					</tfoot>
				</table>
				<?
				echo signature_table(3, $cbo_company_name, "1100px");
				?>
			</div>      
		</fieldset>      
		<?
	}
	else
	{
		?>
		<fieldset style="width:1650px">
			<div style="width:100%; margin-left:10px;" align="center">
				<table width="1230" cellpadding="0" cellspacing="0" style="visibility:hidden; border:none" id="caption" align="center"s>
					<tr>
						<td align="center" width="100%" colspan="11" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
					</tr>
				</table>
				<br>
				<table width="1430" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="center">
					<thead>
						<tr>
							<th colspan="14">PI Details</th>
						</tr>
						<tr>
							<th width="30">SL</th>
							<th width="120">PI Number</th>
							<th width="80">PI Date</th>
							<th width="130">Supplier</th>
							<th width="70">Count</th>
							<th width="200">Composition</th>
							<th width="80">Type</th>
							<th width="100">Color</th>
							<th width="110">Qnty</th>
							<th width="90">Rate</th>                            
							<th width="120">Value</th>
							<th width="100">Yarn Rcv.Qnty</th>
							<th width="100">Yarn Receive Return</th>
							<th>Balance</th>
						</tr>
					</thead>
					<tbody>
					<? 	

					$i=1; $pi_id_all_array=array(); $pi_name_array=array(); $rate=0; $compos=''; $tot_pi_qnty=0; $tot_pi_amnt=0;
					$supplierArr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
					$buyerArr = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
					$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
					$yarn_composition_arr = return_library_array("select id, composition_name from lib_composition_array", 'id', 'composition_name');
					$color_name_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
					$store_name_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');

					/*$prod_sql ="SELECT id, yarn_count_id,yarn_comp_type1st,yarn_type,color from product_details_master where status_active =1 and is_deleted =0";
					$prod_sql_res=sql_select($prod_sql); 
					foreach ($prod_sql_res as $row){
						$key=$row[csf('yarn_count_id')].'_'.$row[csf('yarn_comp_type1st')].'_'.$row[csf('yarn_type')].'_'.$row[csf('color')];
						$prod_array[$key][]=$row[csf('id')];
					}
					echo "<pre>";
					print_r($prod_array); die;*/
					
					//$prod_id_str=",listagg(d.prod_id,',') within group (order by d.id) as prod_ids";
					//, inv_transaction d =and a.job_no=d.job_no and b.id=d.pi_wo_batch_no and  and d.entry_form=248 and d.transaction_type=1

					if($db_type==0){
						$job_no_str=", group_concat(b.job_no) as job_no AS job_no";
					}else{
						$job_no_str=", rtrim(xmlagg(xmlelement(e,a.job_no,',').extract('//text()') order by a.job_no).GetClobVal(),',') AS job_no";
					}
					$sql="select b.id, b.pi_number, b.pi_date,b.supplier_id, b.remarks, c.color_id, c.count_name, c.yarn_composition_item1, c.yarn_composition_percentage1, c.yarn_composition_item2, c.yarn_composition_percentage2, c.yarn_type, sum(c.quantity) as qnty, sum(c.net_pi_amount) as amnt, b.goods_rcv_status, c.work_order_no, c.work_order_id $job_no_str
					from com_pi_master_details b, com_pi_item_details c, wo_non_order_info_dtls a 
					where b.id=c.pi_id and c.work_order_dtls_id=a.id and b.item_category_id=1  and b.importer_id=$cbo_company_name  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $pi_cond
					group by b.id, b.supplier_id , c.color_id, c.count_name, c.yarn_composition_item1, c.yarn_composition_percentage1, c.yarn_composition_item2, c.yarn_composition_percentage2, c.yarn_type, b.pi_number, b.pi_date, b.remarks, b.goods_rcv_status, c.work_order_no, c.work_order_id";

					$result=sql_select($sql); $all_job_nos=''; $job_arr=array(); $prod_array=array();
					foreach($result as $row)
					{
						if($db_type==2) $row[csf('job_no')] = $row[csf('job_no')]->load();
						$key=$row[csf('count_name')].'_'.$row[csf('yarn_composition_item1')].'_'.$row[csf('yarn_type')].'_'.$row[csf('color_id')];
						$all_job_nos .=$row[csf("job_no")].',';
						$pi_id =$row[csf("id")];
						$prod_array[]=$key;
						//$prod_ids .=$prod_array[$key].',';
					}

					//echo $all_prod_ids;
					//$all_prod_ids=implode("','",array_unique(explode(",",chop($prod_ids,","))));
					$all_job_nos=implode("','",array_unique(explode(",",chop($all_job_nos,","))));
					$all_job_nos="'".$all_job_nos."'";
					$job_no_cond=" and a.job_no in($all_job_nos)";
					$store_name_cond=" and a.store_id like '$store'";  
					$pi_id_cond=" and a.pi_wo_batch_no =$pi_id";
					//$prod_id_cond=" and a.prod_id in($all_prod_ids)";

					$wo_sql = "select a.id, a.company_id, a.supplier_id, a.prod_id, a.item_category, a.store_id, a.job_no, a.buyer_id, a.style_ref_no, a.entry_form, a.receive_basis as basis, a.cons_amount, a.cons_rate, a.order_rate, a.order_qnty,	a.order_amount, a.transaction_type as trans_type, a.remarks, b.id as product_id, b.lot, b.color, b.current_stock, b.yarn_count_id, b.avg_rate_per_unit, b.allocated_qnty, b.available_qnty, b.yarn_comp_type1st, b.yarn_type, c.exchange_rate as exchange_rate,c.id as rcv_iss_trans_id, 0 as issue_purpose,a.cons_quantity
					from inv_transaction a, product_details_master b, inv_receive_master c
					where  a.prod_id = b.id and a.mst_id=c.id and a.company_id = $cbo_company_name and a.item_category = 1 $job_no_cond  $store_name_cond $buyer_cond $pi_id_cond  and a.status_active = 1 and b.status_active = 1 and c.status_active=1 and a.transaction_type in(1,4) and a.entry_form in(248,382)
					union all
					select a.id, a.company_id, a.supplier_id, a.prod_id, a.item_category, a.store_id, a.job_no, a.buyer_id, a.style_ref_no, a.entry_form, a.receive_basis as basis, a.cons_amount, a.cons_rate, a.order_rate, a.order_qnty,	a.order_amount,  a.transaction_type as trans_type, a.remarks, b.id as product_id, b.lot, b.color, b.current_stock, b.yarn_count_id, b.avg_rate_per_unit, b.allocated_qnty, b.available_qnty, b.yarn_comp_type1st, b.yarn_type, 0 as exchange_rate, c.id as rcv_iss_trans_id, c.issue_purpose, a.cons_quantity
					from inv_transaction a, product_details_master b, inv_issue_master c
					where  a.prod_id = b.id and a.mst_id=c.id and a.company_id = $cbo_company_name and a.item_category = 1 $job_no_cond  $store_name_cond $buyer_cond  and a.status_active = 1 and b.status_active = 1 and c.status_active=1 and a.transaction_type in(2,3) and a.entry_form in(277,381)
					union all
					select a.id, a.company_id, a.supplier_id, a.prod_id, a.item_category, a.store_id, a.job_no, a.buyer_id, a.style_ref_no, a.entry_form, a.receive_basis as basis, a.cons_amount, a.cons_rate, a.order_rate, a.order_qnty,	a.order_amount,  a.transaction_type as trans_type, a.remarks, b.id as product_id, b.lot, b.color, b.current_stock, b.yarn_count_id, b.avg_rate_per_unit, b.allocated_qnty, b.available_qnty, b.yarn_comp_type1st, b.yarn_type, 0 as exchange_rate,c.id as rcv_iss_trans_id, 0 as issue_purpose,a.cons_quantity
					from inv_transaction a, product_details_master b, inv_item_transfer_mst c
					where  a.prod_id = b.id and a.mst_id=c.id and a.company_id = $cbo_company_name and a.item_category = 1 $job_no_cond  $store_name_cond $buyer_cond  and a.status_active = 1 and b.status_active = 1 and c.status_active=1 and a.transaction_type in(5,6) and a.entry_form in(249)";

					$dataArray=sql_select($wo_sql); $yarn_data_array=$rcv_data_array=array();
					foreach ($dataArray as $row)
					{
						$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['company']=$row[csf('company_id')];
						$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['buyer_id']=$row[csf('buyer_id')];
						$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['style_ref_no']=$row[csf('style_ref_no')];
						$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['job_no']=$row[csf('job_no')];
						$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['supplier_id']=$row[csf('supplier_id')];
						$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['yarn_count_id']=$row[csf('yarn_count_id')];
						$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['yarn_comp_type1st']=$row[csf('yarn_comp_type1st')];
						$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['color']=$row[csf('color')];
						$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['yarn_type']=$row[csf('yarn_type')];
						$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['lot']=$row[csf('lot')];
						$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['remarks']=$row[csf('remarks')];
						$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['issue_purpose']=$row[csf('issue_purpose')];

						$key=$row[csf('yarn_count_id')].'_'.$row[csf('yarn_comp_type1st')].'_'.$row[csf('yarn_type')].'_'.$row[csf('color')];
						if (in_array($key, $prod_array))
						{
							if ($row[csf('trans_type')]==1) {
							$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['purchase']+=$row[csf('cons_quantity')];
							$rcv_data_array[$key]['purchase']+=$row[csf('cons_quantity')];
							}
							else if ($row[csf('trans_type')]==2) {
								$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['lot_issue']+=$row[csf('cons_quantity')];
							}
							else if ($row[csf('trans_type')]==3) {
								$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['rcv_return']+=$row[csf('cons_quantity')];
								$rcv_rtn_data_array[$key]['rcv_rtn']+=$row[csf('cons_quantity')];
							}
							else if ($row[csf('trans_type')]==4) {
								$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['issue_return']+=$row[csf('cons_quantity')];
								$iss_rtn_data_array[$key]['issue_return']+=$row[csf('cons_quantity')];
							}
							else if ($row[csf('trans_type')]==5) {
								$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['tran_in_qty']+=$row[csf('cons_quantity')];
								$rcv_rtn_data_array[$key]['tran_in_qty']+=$row[csf('cons_quantity')];
							}
							else if ($row[csf('trans_type')]==6) {
								$yarn_data_array[$row[csf('job_no')]][$row[csf('prod_id')]]['tran_out_qty']+=$row[csf('cons_quantity')];
								$rcv_rtn_data_array[$key]['tran_out_qty']+=$row[csf('cons_quantity')];
							}
						}
					}
					//echo "<pre>";
					//print_r($prod_array);
					$rcv_data_arr=array();
					$key='';
					$receive_qty=$receive_rtn_qty=$issue_rtn_qty=0;
					//$result=sql_select($sql);

					foreach($result as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else$bgcolor="#FFFFFF";

						$rate=$row[csf('amnt')]/$row[csf('qnty')];
						$compos=$composition[$row[csf('yarn_composition_item1')]]." ".$row[csf('yarn_composition_percentage1')]."%";

						if($row[csf('yarn_composition_percentage2')]>0)
						{
							$compos.=" ".$composition[$row[csf('yarn_composition_item2')]]." ".$row[csf('yarn_composition_percentage2')]."%";
						}

						$key=$row[csf('count_name')].'_'.$row[csf('yarn_composition_item1')].'_'.$row[csf('yarn_type')].'_'.$row[csf('color_id')];
						$receive_qty=$rcv_data_array[$key]['purchase'];
						$receive_rtn_qty=$rcv_rtn_data_array[$key]['rcv_rtn'];
						$issue_rtn_qty=$iss_rtn_data_array[$key]['issue_return'];

						//echo $row[csf("prod_ids")].'='.$key."<br>" ;
						/*$prod_ids=array_unique(explode(",",chop($row[csf("prod_ids")],",")));
						foreach($prod_ids as $val)
						{
							echo $val.'=='.$key."<br>";
							$receive_qty=$rcv_data_array[$val][$key]['purchase'];
						}*/
						?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
							<td width="30"><? echo $i; ?></td>
							<td width="120"><p><? echo $row[csf('pi_number')]; ?></p></td>
							<td width="80" align="center"><? echo change_date_format($row[csf('pi_date')]); ?></td>
							<td width="130" align="center"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></p></td>
							<td width="70" align="center"><p>&nbsp;<? echo $count_arr[$row[csf('count_name')]]; ?></p></td>
							<td width="200"><p><? echo $compos; ?></p></td>
							<td width="80"><p>&nbsp;<? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
							<td width="100"><p>&nbsp;<? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td width="110" align="right"><? echo number_format($row[csf('qnty')],2,'.',''); ?>&nbsp;</td>
							<td width="90" align="right"><? echo number_format($rate,4,'.',''); ?>&nbsp;</td>
							<td width="120" align="right"><? echo number_format($row[csf('amnt')],2,'.',''); ?>&nbsp;</td>
							<td width="100" align="right"><? echo number_format($receive_qty,4,'.',''); ?>&nbsp;</td>
							<td width="100" align="right"><? echo number_format($receive_rtn_qty,4,'.',''); ?>&nbsp;</td>
							<td  align="right"><p><? $rcv_balance=$row[csf('qnty')]-($receive_qty-$receive_rtn_qty) ;echo number_format($rcv_balance,4,'.',''); ?>&nbsp;</p></td>
						</tr>
						<?
						$tot_pi_qnty+=$row[csf('qnty')]; 
						$tot_pi_amnt+=$row[csf('amnt')];
						$tot_receive_qty+=$receive_qty;
						$tot_rtn_qty+=$receive_rtn_qty;
						$tot_rcv_balance+=$rcv_balance;
						$i++;
					}
					?>
					</tbody>
					<tfoot>
						<th colspan="8" align="right">Total</th>
						<th align="right"> <?php echo  number_format($tot_pi_qnty,2,'.','');?></th>
						<th>&nbsp;</th>
						<th align="right"> <?php echo  number_format($tot_pi_amnt,2,'.','');?></th>
						<th align="right"> <?php echo  number_format($tot_receive_qty,2,'.','');?></th>
						<th align="right"> <?php echo  number_format($tot_rtn_qty,2,'.','');?></th>
						<th align="right"> <?php echo  number_format($tot_rcv_balance,2,'.','');?></th>
					</tfoot>
				</table>
				<br>
				<div style="width:1600px; margin-left:10px;" align="left">
				<table width="1600" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="center">
					<thead>
						<tr>
							<th colspan="18">Yarn Lot Wise Stock Retails</th>
						</tr>
						<tr>	
							<th width="120">Buyer</th>
							<th width="80">Style No</th>
							<th width="80">Job No</th>
							<th width="100">PI No</th>
							<th width="130">Yarn Supplier</th>
							<th width="80">Yarn Count</th>
							<th width="100">Yarn Composition</th>
							<th width="100">Yarn Color</th>
							<th width="80">Lot</th>
							<th width="80">Receive Qty.</th>
							<th width="80">Trnsfer In</th>
							<th width="80">Issue Return</th>
							<th width="80">Total Receive</th>
							<th width="80">Yarn Issue</th>
							<th width="80">Receive Return</th>
							<th width="80">Transfer Out</th>
							<th width="80">Total Issue</th>
							<th>Balance</th>
						</tr>
					</thead>
					<tbody>
					<?
					$compos=''; $tot_recv_qnty=0; $tot_recv_amnt=0; $i=0;

					//$recv_id_array=$recv_pi_array=$pi_data_array=$receive_trans_id_arr=array();
					foreach ($yarn_data_array as $job_key=>$prod_data ) 
					{
						$total_rcv=$total_issue=$rcv_rtn_qty=$tran_in_qty=$tran_out_qty=0;
						foreach ($prod_data as $row)
						{
							$key=$row['yarn_count_id'].'_'.$row['yarn_comp_type1st'].'_'.$row['yarn_type'].'_'.$row['color'];
							if (in_array($key, $prod_array))
							{
								//cho "<pre>";
								//print_r($row);
								if ($i%2==0) $bgcolor="#E9F3FF"; else$bgcolor="#FFFFFF";
								if($row['purchase']=='') $rcv_qty=0; else $rcv_qty=$row['purchase'];
								if($row['lot_issue']=='') $issue_qty=0; else $issue_qty=$row['lot_issue'];
								if($row['rcv_return']=='') $rcv_rtn_qty=0; else $rcv_rtn_qty=$row['rcv_return'];
								if($row['issue_return']=='') $issue_rtn_qty=0; else $issue_rtn_qty=$row['issue_return'];
								if($row['tran_in_qty']=='') $tran_in_qty=0; else $tran_in_qty=$row['tran_in_qty'];
								if($row['tran_out_qty']=='') $tran_out_qty=0; else $tran_out_qty=$row['tran_out_qty'];
								$total_rcv=$rcv_qty+$tran_in_qty+$issue_rtn_qty;
								$total_issue=$issue_qty+$rcv_rtn_qty+$tran_out_qty;
								$totRcv +=$rcv_qty;
								$totTransIn +=$tran_in_qty;
								$totIssRtn +=$issue_rtn_qty;
								$totIss +=$issue_qty;
								$totRcvRtn +=$rcv_rtn_qty;
								$totTransOut +=$tran_out_qty;
								$totSumRcv +=$total_rcv;
								$totSumIss +=$total_issue;
								
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td valign="middle"><? echo $buyerArr[$row['buyer_id']];?></td>
									<td valign="middle"><? echo $row['style_ref_no'];?></td>
									<td valign="middle"><? echo $row['job_no'];?></td>
									<td valign="middle"><? echo $pi_no_cond;?></td>
									<td valign="middle"><? echo $supplierArr[$row['supplier_id']];?></td>
									<td valign="middle"><? echo $yarn_count_arr[$row['yarn_count_id']];?></td>
									<td valign="middle"><? echo $yarn_composition_arr[$row['yarn_comp_type1st']];?></td>
									<td><? echo $color_name_arr[$row['color']];?></td>
									<td title="<? echo "Prod_ID==".$row['prod_id']; ?>"><? echo $row['lot'];?> </td>
									<td align="right"><? echo  number_format($rcv_qty,2,'.','');?></td>
									<td align="right"><? echo  number_format($tran_in_qty,2,'.','') ;?></td>
									<td align="right"><? echo  number_format($issue_rtn_qty,2,'.','') ;?></td>
									<td align="right"><? echo  number_format($total_rcv,2,'.','') ;?></td>
									<td align="right"><? echo  number_format($issue_qty,2,'.','') ;?></td>
									<td align="right"><? echo  number_format($rcv_rtn_qty,2,'.','') ;?></td>
									<td align="right"><? echo  number_format($tran_out_qty,2,'.','') ;?></td>
									<td align="right"><? echo  number_format($total_issue,2,'.','') ;?></td>
									<td align="right"><? echo  number_format($total_rcv-$total_issue,2,'.','');?></td>
								</tr>
								<?
								$i++;
							}
						}
					}
					?>
					</tbody>
					<!-- Receive Qty. 	Trnsfer In 	Issue Return 	Total Receive 	Yarn Issue 	Receive Return 	Transfer Out 	Total Issue 	Balance -->
					<tfoot>
						<th colspan="9" align="right">Total</th>
						<th align="right"> <?php echo  number_format($totRcv,2,'.','');?></th>
						<th align="right"> <?php echo  number_format($totTransIn,2,'.','');?></th>
						<th align="right"> <?php echo  number_format($totIssRtn,2,'.','');?></th>
						<th align="right"> <?php echo  number_format($totSumRcv,2,'.','');?></th>

						<th align="right"> <?php echo  number_format($totIss,2,'.','');?></th>
						<th align="right"> <?php echo  number_format($totRcvRtn,2,'.','');?></th>
						<th align="right"> <?php echo  number_format($totTransOut,2,'.','');?></th>
						<th align="right"> <?php echo  number_format($totSumIss,2,'.','');?></th>
						<th align="right"> <?php echo  number_format($totSumRcv-$totSumIss,2,'.','');?></th>
					</tfoot>
				</table>
				</div>
				<?
				echo signature_table(3, $cbo_company_name, "1100px");
				?>
			</div>      
		</fieldset>      
		<?
	}
	
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}
?>

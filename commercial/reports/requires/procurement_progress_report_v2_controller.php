<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
	$txt_req_no=str_replace("'","",$txt_req_no);
	$txt_wo_no=str_replace("'","",$txt_wo_no);
	$txt_pi_num=str_replace("'","",$txt_pi_num);
	$txt_btb_num=str_replace("'","",$txt_btb_num);
	$cbo_date_type=str_replace("'","",$cbo_date_type);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);

	$company_arr=return_library_array( "SELECT id, company_name from lib_company",'id','company_name');
	$item_group_arr=return_library_array( "SELECT id, item_name from lib_item_group",'id','item_name');

	$str_cond="";
	if($cbo_item_category_id){ $str_cond.=" and c.item_category_id=$cbo_item_category_id "; }
	else{ $str_cond.=" and c.item_category_id in ( ".implode(",",array_keys($general_item_category)).") "; }
	if($txt_req_no!=""){ $str_cond.=" and a.requ_prefix_num='$txt_req_no' and to_char(a.insert_date,'YYYY')=$cbo_year_selection "; }
	if($txt_wo_no!=""){ $str_cond.=" and d.wo_number_prefix_num='$txt_wo_no' and to_char(d.insert_date,'YYYY')=$cbo_year_selection "; }
	if($txt_pi_num!=""){ $str_cond.=" and g.pi_number='$txt_pi_num' and to_char(g.insert_date,'YYYY')=$cbo_year_selection "; }
	if($txt_btb_num!=""){ $str_cond.=" and i.lc_number='$txt_btb_num' and to_char(i.insert_date,'YYYY')=$cbo_year_selection "; }

	if($txt_date_from!="" && $txt_date_to!="")
	{
		if($db_type==0)
		{
			$date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
			$date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
		}
		else if($db_type==2)
		{
			$date_from=change_date_format($txt_date_from,'','',-1);
			$date_to=change_date_format($txt_date_to,'','',-1);
		}

		if($cbo_date_type==1){$str_cond.=" and a.requisition_date between '$date_from' and '$date_to'";}
		else if($cbo_date_type==2){$str_cond.=" and d.wo_date between '$date_from' and '$date_to'";}
		else if($cbo_date_type==3){$str_cond.=" and g.pi_date between '$date_from' and '$date_to'";}
		else if($cbo_date_type==4){$str_cond.=" and i.lc_date between '$date_from' and '$date_to'";}
	}

	if($txt_req_no!="" || $cbo_date_type==1)
	{
		$main_sql="SELECT a.company_id as COMPANY_ID, a.id as REQ_ID, a.requ_prefix_num as REQ_NO, a.requisition_date as REQ_DATE, b.required_for as REQ_FOR, b.quantity as REQ_QNTY, b.remarks as REQ_REMARKS, c.id as PROD_ID, c.item_category_id as CATEGORY_ID, c.UNIT_OF_MEASURE as UOM, c.item_group_id as ITEM_GROUP_ID, c.item_description AS ITEM_DESCRIPTION, d.id as WO_ID, d.wo_number_prefix_num as WO_NO, d.wo_date as WO_DATE, d.currency_id as CURRENCY_ID, e.supplier_order_quantity as WO_QNTY, e.rate as WO_RATE, e.amount as WO_AMT, g.id as PI_ID, g.pi_number as PI_NUMBER, g.pi_date as PI_DATE, g.goods_rcv_status as GOODS_RCV_STATUS, i.id as BTB_ID, i.lc_number as LC_NUMBER, i.lc_date as LC_DATE
		from inv_purchase_requisition_mst a, product_details_master c,  inv_purchase_requisition_dtls b 
		left join  wo_non_order_info_dtls e on b.id=e.requisition_dtls_id and e.status_active=1
		left join wo_non_order_info_mst d on d.id=e.mst_id and d.status_active=1 
		left join com_pi_item_details f on e.id=f.work_order_dtls_id and e.mst_id=f.work_order_id and f.status_active=1 
		left join com_pi_master_details g on g.id=f.pi_id and g.status_active=1
		left join com_btb_lc_pi h on g.id=h.pi_id and h.status_active=1
		left join com_btb_lc_master_details i on i.id=h.com_btb_lc_master_details_id and i.status_active=1
		where a.id=b.mst_id and b.product_id=c.id and a.status_active=1 and b.status_active=1 and a.company_id='$cbo_company_name' $str_cond
		order by CATEGORY_ID,REQ_ID,PROD_ID,WO_ID,PI_ID,BTB_ID"; 
	}
	else
	{
		$main_sql="SELECT d.company_name as COMPANY_ID, a.id as REQ_ID, a.requ_prefix_num as REQ_NO, a.requisition_date as REQ_DATE, b.required_for as REQ_FOR, b.quantity as REQ_QNTY, b.remarks as REQ_REMARKS, c.id as PROD_ID, c.item_category_id as CATEGORY_ID, c.UNIT_OF_MEASURE as UOM, c.item_group_id as ITEM_GROUP_ID, c.item_description AS ITEM_DESCRIPTION, d.id as WO_ID, d.wo_number_prefix_num as WO_NO, d.wo_date as WO_DATE, d.currency_id as CURRENCY_ID, e.supplier_order_quantity as WO_QNTY, e.rate as WO_RATE, e.amount as WO_AMT, g.id as PI_ID, g.pi_number as PI_NUMBER, g.pi_date as PI_DATE, g.goods_rcv_status as GOODS_RCV_STATUS, i.id as BTB_ID, i.lc_number as LC_NUMBER, i.lc_date as LC_DATE
		from wo_non_order_info_mst d, product_details_master c, wo_non_order_info_dtls e 
		left join inv_purchase_requisition_dtls b on b.id=e.requisition_dtls_id and b.status_active=1
		left join inv_purchase_requisition_mst a on a.id=b.mst_id and a.status_active=1
		left join com_pi_item_details f on e.id=f.work_order_dtls_id and e.mst_id=f.work_order_id and f.status_active=1 
		left join com_pi_master_details g on g.id=f.pi_id and g.status_active=1
		left join com_btb_lc_pi h on g.id=h.pi_id and h.status_active=1
		left join com_btb_lc_master_details i on i.id=h.com_btb_lc_master_details_id and i.status_active=1
		where d.id=e.mst_id and e.item_id=c.id and d.status_active=1 and e.status_active=1 and d.company_name='$cbo_company_name' $str_cond
		order by CATEGORY_ID,REQ_ID,PROD_ID,WO_ID,PI_ID,BTB_ID"; 
	}
	// echo $main_sql;
	
	$data_result=sql_select($main_sql);
	$all_wo_id=$all_pi_id=$all_prod_id=array();
	foreach($data_result as $row)
	{
		if($row["GOODS_RCV_STATUS"]==2)
		{
			$all_pi_id[$row["PI_ID"]]=$row["PI_ID"];
		}
		else
		{
			$all_wo_id[$row["WO_ID"]]=$row["WO_ID"];
		}
		$all_prod_id[$row["PROD_ID"]]=$row["PROD_ID"];

		if($row['REQ_ID'])
		{
			if(!in_array($row['PROD_ID'].'**'.$row['WO_ID'],$woId_chk))
			{
				$woId_chk[]=$row['PROD_ID'].'**'.$row['WO_ID'];
				$wo_qnty_arr[$row['PROD_ID']][$row['REQ_ID']]+=$row['WO_QNTY'];
			}
		}

		$req_count[$row['PROD_ID']][$row['REQ_ID']]++;
		$wo_count[$row['PROD_ID']][$row['WO_ID']][$row['REQ_ID']]++;
	}
	
	if(count($all_pi_id)>0 || count($all_wo_id)>0)
	{
		$receive_sql="";
		$all_prod_in=where_con_using_array($all_prod_id,0,'b.prod_id');

		if(count($all_pi_id)>0)
		{
			$all_pi_in=where_con_using_array($all_pi_id,0,'a.booking_id');

			$receive_sql = " SELECT a.booking_id as BOOKING_ID, a.receive_basis as RCV_BASIS, b.id as TRANSACTION_ID, b.prod_id as PROD_ID, b.order_qnty as RCV_QNTY, b.order_rate as RCV_RATE, b.order_amount as RCV_AMT
			from inv_receive_master a, inv_transaction b
			where a.id=b.mst_id and a.receive_basis=1 and b.transaction_type=1 and a.status_active=1 and b.status_active=1 $all_pi_in $all_prod_in ";
		}
		if(count($all_wo_id)>0)
		{
			$all_wo_in=where_con_using_array($all_wo_id,0,'a.booking_id');

			if($receive_sql!=""){$receive_sql .= " union all ";}
			$receive_sql .= " SELECT a.booking_id as BOOKING_ID, a.receive_basis as RCV_BASIS, b.id as TRANSACTION_ID, b.prod_id as PROD_ID, b.order_qnty as RCV_QNTY, b.order_rate as RCV_RATE, b.order_amount as RCV_AMT
			from inv_receive_master a, inv_transaction b
			where a.id=b.mst_id and a.receive_basis=2 and b.transaction_type=1 and a.status_active=1 and b.status_active=1 $all_wo_in $all_prod_in ";
		}
		$receive_sql .= " order by TRANSACTION_ID desc";
	}
	// echo $receive_sql;

	$receive_result=sql_select($receive_sql);
	$receive_data=array();
	foreach($receive_result as $row)
	{
		$receive_data[$row['RCV_BASIS']][$row['BOOKING_ID']][$row['PROD_ID']]['RCV_QNTY']+=$row['RCV_QNTY'];
		$receive_data[$row['RCV_BASIS']][$row['BOOKING_ID']][$row['PROD_ID']]['RCV_AMT']+=$row['RCV_AMT'];
		$receive_data[$row['RCV_BASIS']][$row['BOOKING_ID']][$row['PROD_ID']]['RCV_BASIS']=$row['RCV_BASIS'];
		$receive_data[$row['RCV_BASIS']][$row['BOOKING_ID']][$row['PROD_ID']]['TRANSACTION_ID']=$row['TRANSACTION_ID'];
	}
	unset($receive_result);

	$last_rcv_prod_in=where_con_using_array($all_prod_id,0,'prod_id');
	$last_rcv_sql="SELECT id as TRANSACTION_ID, prod_id as PROD_ID, order_rate as RCV_RATE
	from inv_transaction 
	where transaction_type=1 and status_active=1 $last_rcv_prod_in order by prod_id,id";
	// echo $last_rcv_rate;

	$last_rcv_result=sql_select($last_rcv_sql);
	$last_rcv_data=array();
	foreach($last_rcv_result as $row)
	{
		$last_rcv_data[$row['PROD_ID']][$row['TRANSACTION_ID']]=$row['RCV_RATE'];
	}
	unset($last_rcv_result);

	$tbl_width=2250;
	ob_start();
	?>
	<style>
		.wrd_brk{word-break: break-all;}
		.center{text-align: center;}
		.right{text-align: right;}
	</style>

    <div style="width:<?=$tbl_width+50;?>px">
    	<div>
	        <table width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" id="caption"  align="left">
				<tr>
					<td align="center" width="100%"  class="form_caption" colspan="26"><strong style="font-size:18px">Company Name: <? echo  $company_arr[$cbo_company_name]; ?></strong></td>
				</tr> 
				<tr>  
					<td align="center" width="100%" class="form_caption" colspan="26"><strong style="font-size:18px"><? echo $report_title; ?></strong></td>
				</tr>
	        </table>
	    </div>
    	<br />
        <div style="width:<?=$tbl_width;?>px; float:left" align="left">
            <table width="<?=$tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1"  align="left">
				<thead>
					<tr>
						<th width="50" rowspan="2">Sl</th>
						<th colspan="5">Product Details</th>
						<th colspan="4">Requisition Details</th>
						<th colspan="7">Work Order Details</th>
						<th colspan="4">PI/BTB LC Details</th>
						<th colspan="6">Matarials Received Information</th>
					</tr>
					<tr>
						<!-- Product Details -->
						<th width="80">Item Category</th>
						<th width="80">Item Group</th>
						<th width="150">Item Description</th>
						<th width="50">UOM</th>
						<th width="80">Prod. ID</th>
						<!-- Requisition Details -->
						<th width="80">Required For</th>
						<th width="80">Req Date</th>
						<th width="50">Req No</th>
						<th width="80">Req Qty</th>
						<!-- Work Order Details -->
						<th width="50">WO No</th>
						<th width="80">WO Date</th>
						<th width="80">WO Qty</th>
						<th width="80">Currancy</th>
						<th width="80">Rate</th>
						<th width="80">Total Amount</th>
						<th width="80">WO Balance</th>
						<!-- PI/BTB LC Details -->
						<th width="100">PI Number</th>
						<th width="80">PI Date</th>
						<th width="100">BTB LC</th>
						<th width="80">BTB LC Date</th>
						<!-- Matarials Received Information -->
						<th width="80">Rcv. Qty</th>
						<th width="80">Rcv. Rate</th>
						<th width="80">Rcv. Balance</th>
						<th width="80">Last Rcv. Rate</th>
						<th width="80">Difference</th>
						<th >Remarks</th>
					</tr>
				</thead>
			</table>
			<div style="width:<?=$tbl_width+20;?>px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body" align="left">
				<table width="<?=$tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left" >
					<tbody>
					<?
						$req_chk=array();
						$wo_chk=array();
						$wo_chk2=array();
						$i=1;
						foreach($data_result as $row)
						{
							if($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}

							$req_span=$req_count[$row['PROD_ID']][$row['REQ_ID']];
							$wo_span=$wo_count[$row['PROD_ID']][$row['WO_ID']][$row['REQ_ID']];
							if($row['WO_ID']==''){$wo_span=$req_span;}

							$rcv_qnty=$rcv_amt=$rcv_last_rate=0;
							$woPiId=$prodId=$rcv_basis='';
							if($row["GOODS_RCV_STATUS"]==2)
							{
								$rcv_qnty=$receive_data[1][$row['PI_ID']][$row['PROD_ID']]['RCV_QNTY'];
								$rcv_amt=$receive_data[1][$row['PI_ID']][$row['PROD_ID']]['RCV_AMT'];
								$transaction_id=$receive_data[1][$row['PI_ID']][$row['PROD_ID']]['TRANSACTION_ID'];
								$rcv_basis=$receive_data[1][$row['PI_ID']][$row['PROD_ID']]['RCV_BASIS'];
								$woPiId=$row['PI_ID'];
								$prodId=$row['PROD_ID'];

							}
							else
							{
								$rcv_qnty=$receive_data[2][$row['WO_ID']][$row['PROD_ID']]['RCV_QNTY'];
								$rcv_amt=$receive_data[2][$row['WO_ID']][$row['PROD_ID']]['RCV_AMT'];
								$transaction_id=$receive_data[2][$row['WO_ID']][$row['PROD_ID']]['TRANSACTION_ID'];
								$rcv_basis=$receive_data[2][$row['WO_ID']][$row['PROD_ID']]['RCV_BASIS'];
								$woPiId=$row['WO_ID'];
								$prodId=$row['PROD_ID'];
							}
							if($transaction_id)
							{
								foreach($last_rcv_data[$row['PROD_ID']] as $trans_id=>$val)
								{
									if($trans_id<$transaction_id)
									{
										$rcv_last_rate=$val;
									}
								}
							}
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" >
								<td class="wrd_brk center" width="50"><?=$i;?></td>
								<!-- Product Details -->
								<td class="wrd_brk" width="80"><?=$general_item_category[$row['CATEGORY_ID']];?></td>
								<td class="wrd_brk" width="80"><?=$item_group_arr[$row['ITEM_GROUP_ID']];?></td>
								<td class="wrd_brk" width="150"><?=$row['ITEM_DESCRIPTION'];?></td>
								<td class="wrd_brk center" width="50"><?=$unit_of_measurement[$row['UOM']];?></td>
								<td class="wrd_brk center" width="80"><?=$row['PROD_ID'];?></td>
								<!-- Requisition Details -->
								<?
									 if($row['REQ_ID'])
									{
										if(!in_array($row['REQ_ID'].'**'.$row['PROD_ID'],$req_chk))
										{
											$req_chk[]=$row['REQ_ID'].'**'.$row['PROD_ID'];
											?>
												<td rowspan="<?=$req_span;?>" class="wrd_brk" width="80"><?=$use_for[$row['REQ_FOR']];?></td>
												<td rowspan="<?=$req_span;?>" class="wrd_brk center" width="80"><?=change_date_format($row['REQ_DATE']);?></td>
												<td rowspan="<?=$req_span;?>" class="wrd_brk center" width="50"><?=$row['REQ_NO'];?></td>
												<td rowspan="<?=$req_span;?>" class="wrd_brk right" width="80"><?=number_format($row['REQ_QNTY'],2);?></td>
											<?
										}
									}
									else
									{
										?>
											<td width="80"></td>
											<td width="80"></td>
											<td width="50"></td>
											<td width="80"></td>
										<?
									} 
								?>
								<!-- Work Order Details -->
								<?
									 if(!in_array($row['REQ_ID'].'**'.$row['WO_ID'].'**'.$row['PROD_ID'],$wo_chk))
									{
										$wo_chk[]=$row['REQ_ID'].'**'.$row['WO_ID'].'**'.$row['PROD_ID'];
										?>
											<td rowspan="<?=$wo_span;?>" class="wrd_brk center" width="50"><?=$row['WO_NO'];?></td>
											<td rowspan="<?=$wo_span;?>" class="wrd_brk center" width="80"><?=change_date_format($row['WO_DATE']);?></td>
											<td rowspan="<?=$wo_span;?>" class="wrd_brk right" width="80"><?=number_format($row['WO_QNTY'],2);?></td>
											<td rowspan="<?=$wo_span;?>" class="wrd_brk center" width="80"><?=$currency[$row['CURRENCY_ID']];?></td>
											<td rowspan="<?=$wo_span;?>" class="wrd_brk right" width="80"><?=number_format($row['WO_RATE'],2);?></td>
											<td rowspan="<?=$wo_span;?>" class="wrd_brk right" width="80"><?=number_format($row['WO_AMT'],2);?></td>
											<?
												if($row['REQ_ID'])
												{
													if(!in_array($row['REQ_ID'].'**'.$row['PROD_ID'],$wo_chk2))
													{
														$wo_chk2[]=$row['REQ_ID'].'**'.$row['PROD_ID'];
														$wo_balance=$row['REQ_QNTY']-$wo_qnty_arr[$row['PROD_ID']][$row['REQ_ID']];
														?>
															<td rowspan="<?=$req_span;?>" class="wrd_brk right" width="80"><?=number_format($wo_balance,2);?></td>
														<?
														$total_wo_balance+=$wo_balance;
													}
												}
												else
												{
													$wo_balance=$row['REQ_QNTY']-$row['WO_QNTY'];
													?>
														<td rowspan="<?=$wo_span;?>" class="wrd_brk right" width="80"><?=number_format($wo_balance,2);?></td>
													<?
													$total_wo_balance+=$wo_balance;
												}
												$total_wo_qnty+=$row['WO_QNTY'];
												$total_wo_value+=$row['WO_AMT'];												
									} 
								?>
								<!-- PI/BTB LC Details -->
								<td class="wrd_brk" width="100"><?=$row['PI_NUMBER'];?></td>
								<td class="wrd_brk center" width="80"><?=change_date_format($row['PI_DATE']);?></td>
								<td class="wrd_brk" width="100"><?=$row['LC_NUMBER'];?></td>
								<td class="wrd_brk center" width="80"><?=change_date_format($row['LC_DATE']);?></td>
								<!-- Matarials Received Information -->
								<td class="wrd_brk right" width="80">
									<?
										if($rcv_qnty)
										{
											?>
												<a href="javascript:openmypage_popup('<? echo $woPiId; ?>','<? echo $prodId;?>','<? echo $rcv_basis;?>','Receive Popup','receive_popup')">
												<?=number_format($rcv_qnty,2);?></a>
											<?
										}
										else
										{echo number_format($rcv_qnty,2);}
									?>
								</td>
								<td class="wrd_brk right" width="80"><?=fn_number_format($rcv_amt/$rcv_qnty,2);?></td>
								<td class="wrd_brk right" width="80"><?=number_format($row['WO_QNTY']-$rcv_qnty,2);?></td>
								<td class="wrd_brk right" width="80"><?=number_format($rcv_last_rate,2);?></td>
								<td class="wrd_brk right" width="80"><?if($rcv_last_rate){echo number_format($rcv_last_rate-$row['WO_RATE'],2);};?></td>
								<td class="wrd_brk" ><?=$row['REQ_REMARKS'];?></td>
							</tr>
							<?
							$i++;
							$total_req_qnty+=$row['REQ_QNTY'];
							$total_rcv_qnty+=$rcv_qnty;
							$total_rcv_balance+=$row['WO_QNTY']-$rcv_qnty;
							$total_diff+=$rcv_last_rate-$row['WO_RATE'];
						}
					?>
					</tbody>					
				</table>
			</div>
			<table width="<?=$tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_footer" align="left">
				<tfoot>
					<tr>
						<th width="50">&nbsp;</th>
						<!-- Product Details -->
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="150">&nbsp;</th>
						<th width="50">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<!-- Requisition Details -->
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="50">Total:&nbsp;</th>
						<th width="80"><?=number_format($total_req_qnty,2);?></th>
						<!-- Work Order Details -->
						<th width="50">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80"><?=number_format($total_wo_qnty,2);?></th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80"><?=number_format($total_wo_value,2);?></th>
						<th width="80"><?=number_format($total_wo_balance,2);?></th>
						<!-- PI/BTB LC Details -->
						<th width="100">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<!-- Matarials Received Information -->
						<th width="80"><?=number_format($total_rcv_qnty,2);?></th>
						<th width="80">&nbsp;</th>
						<th width="80"><?=number_format($total_rcv_balance,2);?></th>
						<th width="80">&nbsp;</th>
						<th width="80"><?=number_format($total_diff,2);?></th>
						<th >&nbsp;</th>
					</tr>
				</tfoot>
			</table>
        </div>
	</div>

	<br />
	<?
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

if($action=='receive_popup')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);

	$sql_recv="SELECT a.id, a.challan_no as CHALLAN_NO,a.recv_number as RECV_NUMBER, a.receive_date as RCV_DATE, b.id as trans_id, b.cons_quantity as RCV_QNTY, b.cons_rate as RCV_RATE
	from inv_receive_master a, inv_transaction b 
	where a.id=b.mst_id and b.transaction_type=1 and a.booking_id=$wopi_id and b.prod_id=$prod_id and a.receive_basis=$rcv_basis and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
	// echo $sql_recv;
	$data_rcv=sql_select($sql_recv);
   ?>		
	<fieldset style="width:630px; margin-left:10px" >
		<div id="report_container">
			<table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="630">
				<thead>
					<tr>
						<th width="35" >SI</th>
						<th width="100">MRR No</th>
						<th width="100">Challan No</th>
						<th width="80">MRR Date</th>
						<th width="80">MRR Rate</th>
						<th width="80">MRR Qty.</th>
					</tr>
				</thead>
				<tbody>
					<?
						$i=1;
						foreach($data_rcv as $row)
						{
							if ($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td align="center"><? echo $i; ?></td>
								<td><p><? echo $row["RECV_NUMBER"]; ?> </p></td>
								<td><p><? echo $row["CHALLAN_NO"]; ?> </p></td>
								<td align="center"><p><? echo change_date_format($row["RCV_DATE"]); ?> </p></td>
								<td align="right"><? echo number_format($row["RCV_RATE"],2); ?></td>
								<td align="right"><? echo number_format($row["RCV_QNTY"],2); $grand_tot_in+=$row["RCV_QNTY"]; ?></td>
							</tr>
							<?
							$i++;
						}
					?>
				</tbody>
				<tfoot>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th >Total:</th>
					<th ><? echo number_format($grand_tot_in,2); ?></th>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?	
}

disconnect($con);
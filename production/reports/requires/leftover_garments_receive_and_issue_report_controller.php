<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
function pre($array){
	echo "<pre>";
	print_r($array);
	echo "</pre>";
}
if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 140, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "","" );     	 
	exit();
}


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}

if ($action=="load_drop_down_store_name")
{
	echo create_drop_down( "cbo_store_name", 150, "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id  and a.status_active=1 and a.is_deleted=0 and a.company_id in($data)  group by a.id ,a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", "0", "","" ); 
	//and b.category_type in ($item_cate_credential_cond) $store_location_credential_cond
	exit();
}

if ($action == 'receive_popup') 
{
	echo load_html_head_contents("Popup Info", "../../../", 1, 1,'','','');
	$width = "1180"; 
	$po_break_down_id = $data;
	$date_con = "";
	$form_date = $_REQUEST['form_date'];
	$to_date = $_REQUEST['to_date'];
	if($form_date !='' && $to_date !='')
	{
		$date_con = "and a.leftover_date between '".change_date_format($form_date,'dd-mm-yyyy','-',1)."' and '".change_date_format($to_date,'dd-mm-yyyy','-',1)."'" ;
	}  
	$sql="SELECT a.id, a.sys_number, a.leftover_date, a.order_type, a.buyer_name, a.goods_type, a.remarks, b.style_ref_no, b.po_break_down_id, b.order_no, b.total_left_over_receive,b.category_id, b.job_no from pro_leftover_gmts_rcv_mst a, pro_leftover_gmts_rcv_dtls b where a.id=b.mst_id and  a.status_active=1 and  a.is_deleted=0  and b.po_break_down_id= $po_break_down_id  $date_con order by a.leftover_date desc"; 
	$sql_result = sql_select($sql);
	// echo $sql; die;
	// pre($sql_result); die;
	$order_info_array = array();
	foreach($sql_result as $v)
	{
		if (!$order_info_array['ORDER'] ) 
		{ 
			$order_info_array['BUYER']=$v['BUYER_NAME'];
			$order_info_array['STYLE']=$v['STYLE_REF_NO'];
			$order_info_array['ORDER']=$v['ORDER_NO'];
			$order_info_array['JOB']=$v['JOB_NO'];
		}
		$received_data_arr[$v['LEFTOVER_DATE']][$v['SYS_NUMBER']]['RCV_ID']=$v['SYS_NUMBER'];
		$received_data_arr[$v['LEFTOVER_DATE']][$v['SYS_NUMBER']]['ORDER_TYPE']=$v['ORDER_TYPE'];
		$received_data_arr[$v['LEFTOVER_DATE']][$v['SYS_NUMBER']]['GOODS_TYPE']=$v['GOODS_TYPE'];

		$received_data_arr[$v['LEFTOVER_DATE']][$v['SYS_NUMBER']]['GOODS'][$v['GOODS_TYPE']][$v['CATEGORY_ID']]=$v['GOODS_TYPE'];


		$received_data_arr[$v['LEFTOVER_DATE']][$v['SYS_NUMBER']]['RCV_REMARKS']=$v['REMARKS'];
		$received_data_arr[$v['LEFTOVER_DATE']][$v['SYS_NUMBER']]['CATEGORY_ID']=$v['CATEGORY_ID'];
		$received_data_arr[$v['LEFTOVER_DATE']][$v['SYS_NUMBER']]['CATEGORY'][$v['CATEGORY_ID']]=$v['CATEGORY_ID']; 

		$received_data_arr[$v['LEFTOVER_DATE']][$v['SYS_NUMBER']]['TOTAL_RECEIVE']+=$v['TOTAL_LEFT_OVER_RECEIVE'];

		$received_data_arr[$v['LEFTOVER_DATE']][$v['SYS_NUMBER']]['DAMAGE_RECEIVE'][$v['GOODS_TYPE']][$v['CATEGORY_ID']]+=$v['TOTAL_LEFT_OVER_RECEIVE'];

		$received_data_arr[$v['LEFTOVER_DATE']][$v['SYS_NUMBER']]['RECEIVE'][$v['GOODS_TYPE']][$v['CATEGORY_ID']]+=$v['TOTAL_LEFT_OVER_RECEIVE'];
	}
	// pre($received_data_arr);die;
	?> 
		<style>
			.tableFixHead          { height: 100vh; }
			.tableFixHead thead th { position: sticky;   top: 0; z-index: 99; }
			</style>
		</head>
		<body>
			<div align="center" style="width:410px;">  
				<fieldset style="width:410px;"> 
					<table cellspacing="0" width="400" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<thead>
							<tr >
								<th width='100'>Buyer</th>
								<th width='100'>Style Ref.</th>
								<th width='100'>Order No</th>
								<th width='100'>Job No</th> 
							</tr>  
							<tr>
								<td> <?= $buyer_arr[$order_info_array['BUYER']] ?></td>
								<td> <?= $order_info_array['STYLE']?></td>
								<td> <?= $order_info_array['ORDER']?></td>
								<td> <?= $order_info_array['JOB']?></td>
							</tr>  
						</thead>
					</table> 
				</fieldset>   
			</div>
			<div style="width:900px; margin:0 auto; margin-top:10px;" >
				<fieldset style="width:100%;" > 
					<div style="width:900px; max-height:200px; overflow-y:scroll" id="scroll_body">
						<table class="rpt_table" width="880" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
							<thead>
								<tr>
									<th rowspan="2" width="30">SL</th>
									<th rowspan="2" width="80">Receive Date</th> 
									<th rowspan="2" width="80">Receive ID</th> 
									<th rowspan="2" width="80">Order Type</th>
									<th colspan="3">Category</th>
									<th rowspan="2" width="80">Good GMT</th>
									<th rowspan="2" width="80">Damage GMT</th>
									<th rowspan="2" width="80">Total Rcv Qty</th> 
									<th rowspan="2" width="200">Remarks</th>
								</tr> 
								<tr>
									<th width="50">A</th>
									<th width="50">B</th>
									<th width="50">C</th>
								</tr>
							</thead>
							<tbody>
								<?
								$categories = array(1 => 'A', 2 => 'B',3 => 'C');
								$goods_type_arr = array(1=>"Good GMT In Hand",2=>"Damage GMT",3=>"Leftover Sample");	
								$i=1;
								$total_rev_qty=0;   
								$total_cat_a=0;
								$total_cat_b=0;
								$total_cat_c=0;
								$total_good_gmt=0;
								$total_damage_amt=0;
								foreach($received_data_arr as $recive_date => $date_wise_arr)
								{ 
									foreach($date_wise_arr as $receive_id => $v)
									{ 
										$total_rev_qty+=$v['TOTAL_RECEIVE']; 

										?>
										<tr bgcolor="<?= $bgcolor;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor;?>')" id="tr_<?= $i; ?>">
											<td width="30"><?= $i;?></td>
											<td width="80"><?= change_date_format($recive_date);?></td> 
											<td width="80"><?= $receive_id;?></td>
											<td width="80"><?= $order_source[$v['ORDER_TYPE']];?></td>
											<td align="right" width="50">
												<?php
													$receive_qty = 0;
													if($v['CATEGORY'][1]==1 && $v['GOODS'][1][$v['CATEGORY'][1]]==1)
													{
														echo $v['RECEIVE'][1][1];

														$receive_qty += $v['RECEIVE'][1][1];
														$total_cat_a += $v['RECEIVE'][1][1];
													}
													else
													{
														echo $v['RECEIVE'][2][1];
														$receive_qty += 0;
														$total_cat_a += 0;
													}
												?>	
											</td>
											<td align="right" width="50">
												<?php
													if($v['CATEGORY'][2]==2 && $v['GOODS'][1][$v['CATEGORY'][2]]==1)
													{
														echo $v['RECEIVE'][1][2];

														$receive_qty += $v['RECEIVE'][1][2];
														$total_cat_b += $v['RECEIVE'][1][2];
													}
													else
													{
														echo $v['RECEIVE'][2][2];
														$receive_qty += 0;
														$total_cat_b += 0;
													}
												?>
											</td>
											<td align="right" width="50">
												<?php
													if($v['CATEGORY'][3]==3 && $v['GOODS'][1][$v['CATEGORY'][3]]==1)
													{
														echo $v['RECEIVE'][1][3];

														$receive_qty += $v['RECEIVE'][1][3];
														$total_cat_c += $v['RECEIVE'][1][3];
													}
													else
													{
														echo $v['RECEIVE'][2][3];
														$receive_qty += 0;
														$total_cat_c += 0;
													}
												?>
											</td>
											<td align="right" width="80">
												<?php 
													echo $receive_qty;
													$total_good_gmt +=$receive_qty;
												?>
											</td>
											<td align="right" width="80">
												<?php

													$damage_amt = 0;
													foreach($v['DAMAGE_RECEIVE'][2] as $data)
													{
														$total_damage_amt += $data;
														$damage_amt += $data;
													}

													echo $damage_amt;
												?>
											</td>
											<td width="80" align="right"><? echo $v['TOTAL_RECEIVE'];?></td> 
											<td width="200"><? echo $v['RCV_REMARKS'];?></td>
										</tr>
										<? 
										$i++; 
									}	
								}
								?>
								<tfoot>
									<th colspan="4" align="center">Grand Total</th>
									<th> <?= $total_cat_a; ?> </th>
									<th> <?= $total_cat_b; ?> </th>
									<th> <?= $total_cat_c; ?> </th>
									<th> <?= $total_good_gmt; ?> </th>
									<th> <?= $total_damage_amt; ?> </th>
									<th> <?= $total_rev_qty; ?> </th>
									<th></th>
								</tfoot>
							</tbody> 
						</table>
					</div>
				</fieldset>
			</div>
		</body>           
		<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
		</html>
		<?
		exit();  

}
if ($action == 'issue_popup') 
{
	echo load_html_head_contents("Popup Info", "../../../", 1, 1,'','','');
	$width = "1180";
	$po_break_down_id = $data;
	$date_con = "";
	$form_date = $_REQUEST['form_date'];
	$to_date = $_REQUEST['to_date'];
	if($form_date !='' && $to_date !='')
	{
		$date_con = "and a.issue_date between '".change_date_format($form_date,'dd-mm-yyyy','-',1)."' and '".change_date_format($to_date,'dd-mm-yyyy','-',1)."'" ;
	} 
	$sql = "SELECT a.sys_number,a.issue_date,a.order_type,a.goods_type,a.remarks,b.po_break_down_id, b.order_no,b.style_ref_no, b.total_issue,b.buyer_id,b.category_id from pro_leftover_gmts_issue_mst a, pro_leftover_gmts_issue_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id=$po_break_down_id $date_con order by a.issue_date ASC";
	$sql_result = sql_select($sql);
	// echo  $sql;die;
	$job_sql="SELECT b.job_no_mst,b.id from wo_po_break_down b where b.status_active=1 and b.is_deleted=0 and b.id= $po_break_down_id ";
	$job_sql_result = sql_select($job_sql);
	$job_array = array();
	foreach($job_sql_result as $v)
	{
		$job_array[$v['ID']] = $v['JOB_NO_MST'];
	}	 
	$order_info_array = array();
	foreach($sql_result as $v)
	{
		if (!$order_info_array['ORDER'] ) 
		{ 
			$order_info_array['BUYER']=$v['BUYER_ID'];
			$order_info_array['STYLE']=$v['STYLE_REF_NO'];
			$order_info_array['ORDER']=$v['ORDER_NO'];
			$order_info_array['JOB']=$job_array[$v['PO_BREAK_DOWN_ID']];
		}
		$issue_data_arr[$v['ISSUE_DATE']][$v['SYS_NUMBER']]['ISSUE_ID']=$v['SYS_NUMBER'];
		$issue_data_arr[$v['ISSUE_DATE']][$v['SYS_NUMBER']]['ORDER_TYPE']=$v['ORDER_TYPE'];
		$issue_data_arr[$v['ISSUE_DATE']][$v['SYS_NUMBER']]['GOODS_TYPE']=$v['GOODS_TYPE'];

		$issue_data_arr[$v['ISSUE_DATE']][$v['SYS_NUMBER']]['GOODS'][$v['GOODS_TYPE']][$v['CATEGORY_ID']]=$v['GOODS_TYPE'];


		$issue_data_arr[$v['ISSUE_DATE']][$v['SYS_NUMBER']]['REMARKS']=$v['REMARKS']; 
		$issue_data_arr[$v['ISSUE_DATE']][$v['SYS_NUMBER']]['CATEGORY_ID']=$v['CATEGORY_ID'];
		$issue_data_arr[$v['ISSUE_DATE']][$v['SYS_NUMBER']]['CATEGORY'][$v['CATEGORY_ID']]=$v['CATEGORY_ID'];

		$issue_data_arr[$v['ISSUE_DATE']][$v['SYS_NUMBER']]['TOTAL_ISSUE']+=$v['TOTAL_ISSUE'];

		$issue_data_arr[$v['ISSUE_DATE']][$v['SYS_NUMBER']]['DAMAGE_ISSUE'][$v['GOODS_TYPE']][$v['CATEGORY_ID']]+=$v['TOTAL_ISSUE'];

		$issue_data_arr[$v['ISSUE_DATE']][$v['SYS_NUMBER']]['ISSUE'][$v['GOODS_TYPE']][$v['CATEGORY_ID']]+=$v['TOTAL_ISSUE'];
	}
		// pre($issue_data_arr);die;
	?> 
	<style>
		.tableFixHead          { height: 100vh; }
		.tableFixHead thead th { position: sticky;   top: 0; z-index: 99; }
		</style>
	</head>
	<body>
		<div align="center" style="width:410px;">  
			<fieldset style="width:410px;"> 
				<table cellspacing="0" width="400" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<tr >
							<th width='100'>Buyer</th>
							<th width='100'>Style Ref.</th>
							<th width='100'>Order No</th>
							<th width='100'>Job No</th> 
						</tr>  
						<tr>
							<td> <?= $buyer_arr[$order_info_array['BUYER']] ?></td>
							<td> <?= $order_info_array['STYLE']?></td>
							<td> <?= $order_info_array['ORDER']?></td>
							<td> <?= $order_info_array['JOB']?></td>
						</tr>  
					</thead>
				</table> 
			</fieldset>   
		</div>
		<div style="width:900px; margin:0 auto; margin-top:10px;" >
			<fieldset style="width:100%;" > 
				<div style="width:900px; max-height:200px; overflow-y:scroll" id="scroll_body">
					<table class="rpt_table" width="880" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
						<thead>
							<tr>
								<th rowspan="2" width="30">SL</th>
								<th rowspan="2" width="80">Issue Date</th> 
								<th rowspan="2" width="80">Issue ID</th> 
								<th rowspan="2" width="80">Order Type</th>
								<th colspan="3">Category</th>
								<th rowspan="2" width="80">Good GMT</th>
								<th rowspan="2" width="80">Damage GMT</th>
								<th rowspan="2" width="80">Total Issue Qty</th> 
								<th rowspan="2" width="200">Remarks</th>
							</tr> 
							<tr>
								<th width="50">A</th>
								<th width="50">B</th>
								<th width="50">C</th>
							</tr>
						</thead>
						<tbody>
							<?
							$categories = array(1 => 'A', 2 => 'B',3 => 'C');
							$goods_type_arr = array(1=>"Good GMT In Hand",2=>"Damage GMT",3=>"Leftover Sample");	
							$i=1;
							$total_issue_qty=0;   
							$total_cat_a=0;
							$total_cat_b=0;
							$total_cat_c=0;
							$total_good_gmt=0;
							$total_damage_amt=0;
							foreach($issue_data_arr as $date => $date_arr)
							{ 
								foreach($date_arr as $issue_id => $v)
								{
									$total_issue_qty+=$v['TOTAL_ISSUE']; 

									?>
									<tr bgcolor="<?= $bgcolor;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor;?>')" id="tr_<?= $i; ?>">
										<td width="30"><?= $i;?></td>
										<td width="80"><?= change_date_format($date);?></td> 
										<td width="80"><?= $issue_id;?></td>
										<td width="80"><?= $order_source[$v['ORDER_TYPE']];?></td>
										<td align="right" width="50">
											<?php
												$issue_qty = 0;
												if($v['CATEGORY'][1]==1 && $v['GOODS'][1][$v['CATEGORY'][1]]==1)
												{
													echo $v['ISSUE'][1][1];

													$issue_qty += $v['ISSUE'][1][1];
													$total_cat_a += $v['ISSUE'][1][1];
												}
												else
												{
													echo 0;
													$issue_qty += 0;
													$total_cat_a += 0;
												}
											?>	
										</td>
										<td align="right" width="50">
											<?php
												if($v['CATEGORY'][2]==2 && $v['GOODS'][1][$v['CATEGORY'][2]]==1)
												{
													echo $v['ISSUE'][1][2];

													$issue_qty += $v['ISSUE'][1][2];
													$total_cat_b += $v['ISSUE'][1][2];
												}
												else
												{
													echo 0;
													$issue_qty += 0;
													$total_cat_b += 0;
												}
											?>
										</td>
										<td align="right" width="50">
											<?php
												if($v['CATEGORY'][3]==3 && $v['GOODS'][1][$v['CATEGORY'][3]]==1)
												{
													echo $v['ISSUE'][1][3];

													$issue_qty += $v['ISSUE'][1][3];
													$total_cat_c += $v['ISSUE'][1][3];
												}
												else
												{
													echo 0;
													$issue_qty += 0;
													$total_cat_c += 0;
												}
											?>
										</td>
										<td align="right" width="80">
											<?php 
												echo $issue_qty;
												$total_good_gmt +=$issue_qty;
											?>
										</td>
										<td align="right" width="80">
											<?php

												$damage_amt = 0;
												foreach($v['DAMAGE_ISSUE'][2] as $data)
												{
													$total_damage_amt += $data;
													$damage_amt += $data;
												}

												echo $damage_amt;
											?>
										</td>
										<td width="80" align="right"><?= $v['TOTAL_ISSUE'];?></td> 
										<td width="200"> <?= $v['REMARKS'] ;?> </td>
									</tr>
									<? 
									$i++; 
								}	
							}
							?>
							<tfoot>
								<th colspan="4" align="center">Grand Total</th>
								<th> <?= $total_cat_a; ?> </th>
								<th> <?= $total_cat_b; ?> </th>
								<th> <?= $total_cat_c; ?> </th>
								<th> <?= $total_good_gmt; ?> </th>
								<th> <?= $total_damage_amt; ?> </th>
								<th> <?= $total_issue_qty; ?> </th>
								<th></th>
							</tfoot>
						</tbody> 
					</table>
				</div>
			</fieldset>
		</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();  

}

if($action=="report_generate") 
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//$report_type=str_replace("'","",$reporttype);
	//cbo_company_name*cbo_location_id*cbo_buyer_name*txt_date_from*txt_date_to*cbo_search_by*txt_search_text
	$report_title=str_replace("'","",$report_title);
	
	
	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_location_id=str_replace("'","",$cbo_location_id); 
	$store_name=str_replace("'","",$cbo_store_name); 
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$search_by=str_replace("'","",$cbo_search_by);
	$search_text=trim(str_replace("'","",$txt_search_text));
	
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$report_type=str_replace("'","",$type);
	
	if($cbo_location_id==0) $location_id_cond=""; else $location_id_cond=" and a.location in($cbo_location_id) ";
	if($store_name==0) $store_name_cond=""; else $store_name_cond=" and a.store_name in($store_name) ";

	ob_start();
	if($report_type==1) //Show 1
	{
		if(str_replace("'","",$cbo_buyer_name)==0)
		{
			$buyer_id_cond="";
			$issue_buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond=" and a.buyer_name in($cbo_buyer_name)";
			$issue_buyer_id_cond=" and b.buyer_id in($cbo_buyer_name)";
		}
		
		$orer_job_cond="";
		
		if($search_by == 1 && $search_text !="")
		{
			$orer_job_cond=" and b.job_no like '%$search_text%'";
		}
		elseif($search_by == 2 && $search_text !="")
		{
			$orer_job_cond=" and b.style_ref_no like '%$search_text%'";
		}
		else
		{
			$orer_job_cond=" and b.order_no like '%$search_text%'";
		}

		//$cbo_year=str_replace("'","",$cbo_year);
		//if($db_type==0)
		//{
		//	if(trim($cbo_year)!=0) $year_cond=" and YEAR(a.insert_date)=$cbo_year"; else $year_cond="";
		//}
		//else if($db_type==2)
		//{
		//	if(trim($cbo_year)!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year"; else $year_cond="";
		//}

	
		$date_cond='';	$ex_fact_date_cond='';$est_date_cond='';$reso_date_cond='';
		
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
			}
			$date_cond=" and a.leftover_date between '$start_date' and '$end_date'";
		}

		// ===================================== for issue ==============================
		$sql_issue="SELECT a.id, a.sys_number, min(a.issue_date) as issue_date, a.goods_type, a.order_type, a.party_name, a.party_id, a.issue_purpose, a.store_name, a.pay_term, a.currency_id, a.challan_no, a.exchange_rate, a.remarks, b.po_break_down_id, b.order_no, b.production_type,b.sale_rate, b.style_ref_no, b.item_number_id, b.total_issue, b.currency_id, b.fob_rate, b.issue_amount, b.bdt_amount, b.room_no, b.rack_no, b.shelf_no, b.bin_no, b.color_size_id, b.style_order_wisw, b.buyer_id, b.receive_dtls_id from pro_leftover_gmts_issue_mst a, pro_leftover_gmts_issue_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_name $location_id_cond $store_name_cond $issue_buyer_id_cond $orer_job_cond group by a.id, a.sys_number,a.goods_type, a.order_type, a.party_name, a.party_id, a.issue_purpose, a.store_name, a.pay_term, a.currency_id, a.challan_no, a.exchange_rate, a.remarks, b.po_break_down_id, b.order_no, b.production_type,b.sale_rate, b.style_ref_no, b.item_number_id, b.total_issue, b.currency_id, b.fob_rate, b.issue_amount, b.bdt_amount, b.room_no, b.rack_no, b.shelf_no, b.bin_no, b.color_size_id, b.style_order_wisw, b.buyer_id, b.receive_dtls_id";

		$sql_result_issue = sql_select($sql_issue);
		foreach($sql_result_issue as $row)
		{
			$leftover_issue_data_arr[$row[csf('po_break_down_id')]]['order_type']	= $row[csf('order_type')];
			$leftover_issue_data_arr[$row[csf('po_break_down_id')]]['issue_date']	= $row[csf('issue_date')];
			$leftover_issue_data_arr[$row[csf('po_break_down_id')]]['remarks']		= $row[csf('remarks')];
			$leftover_issue_data_arr[$row[csf('po_break_down_id')]]['total_issue']	+= $row[csf('total_issue')];
			$leftover_issue_data_arr[$row[csf('po_break_down_id')]]['sale_rate']	+= $row[csf('sale_rate')];
			$leftover_issue_data_arr[$row[csf('po_break_down_id')]]['bdt_amount']	+= $row[csf('bdt_amount')];
			$leftover_issue_data_arr[$row[csf('po_break_down_id')]]['issue_purpose']= $row[csf('issue_purpose')];
			$leftover_issue_data_arr[$row[csf('po_break_down_id')]]['party_name']	= $row[csf('party_name')];
		}
	
		//================================== for receive ==================================
		$sql="SELECT a.id, a.sys_number, a.leftover_date, a.order_type, a.buyer_name, a.store_name, a.floor_id, a.exchange_rate, a.garments_nature, a.goods_type, a.remarks, b.style_ref_no, b.po_break_down_id, b.order_no, b.total_left_over_receive,b.category_id, b.fob_rate, b.leftover_amount, b.room_no, b.rack_no, b.shelf_no, b.bin_no, b.job_no from pro_leftover_gmts_rcv_mst a, pro_leftover_gmts_rcv_dtls b where a.id=b.mst_id and  a.status_active=1 and  a.is_deleted=0  and a.company_id=$company_name $location_id_cond $store_name_cond $buyer_id_cond $date_cond $orer_job_cond order by a.leftover_date desc"; 
		$sql_result = sql_select($sql);
		
		foreach($sql_result as $row)
		{
			$leftover_data_arr[$row[csf('leftover_date')]][$row[csf('po_break_down_id')]]['rcv_id']=$row[csf('id')];
			$leftover_data_arr[$row[csf('leftover_date')]][$row[csf('po_break_down_id')]]['order_type']=$row[csf('order_type')];
			$leftover_data_arr[$row[csf('leftover_date')]][$row[csf('po_break_down_id')]]['goods_type']=$row[csf('goods_type')];

			$leftover_data_arr[$row[csf('leftover_date')]][$row[csf('po_break_down_id')]]['goods'][$row[csf('goods_type')]][$row[csf('category_id')]]=$row[csf('goods_type')];


			$leftover_data_arr[$row[csf('leftover_date')]][$row[csf('po_break_down_id')]]['rcv_remarks']=$row[csf('remarks')];
			$leftover_data_arr[$row[csf('leftover_date')]][$row[csf('po_break_down_id')]]['store_name']=$row[csf('store_name')];
			$leftover_data_arr[$row[csf('leftover_date')]][$row[csf('po_break_down_id')]]['style_ref_no']=$row[csf('style_ref_no')];
			$leftover_data_arr[$row[csf('leftover_date')]][$row[csf('po_break_down_id')]]['order_no']=$row[csf('order_no')];
			$leftover_data_arr[$row[csf('leftover_date')]][$row[csf('po_break_down_id')]]['fob_rate']=$row[csf('fob_rate')];
			$leftover_data_arr[$row[csf('leftover_date')]][$row[csf('po_break_down_id')]]['category_id']=$row[csf('category_id')];
			$leftover_data_arr[$row[csf('leftover_date')]][$row[csf('po_break_down_id')]]['category'][$row[csf('category_id')]]=$row[csf('category_id')];


			$leftover_data_arr[$row[csf('leftover_date')]][$row[csf('po_break_down_id')]]['leftover_date']=$row[csf('leftover_date')];
			$leftover_data_arr[$row[csf('leftover_date')]][$row[csf('po_break_down_id')]]['buyer_name']=$row[csf('buyer_name')];
			$leftover_data_arr[$row[csf('leftover_date')]][$row[csf('po_break_down_id')]]['room_no']=$row[csf('room_no')];
			$leftover_data_arr[$row[csf('leftover_date')]][$row[csf('po_break_down_id')]]['rack_no']=$row[csf('rack_no')];
			$leftover_data_arr[$row[csf('leftover_date')]][$row[csf('po_break_down_id')]]['shelf_no']=$row[csf('shelf_no')];
			$leftover_data_arr[$row[csf('leftover_date')]][$row[csf('po_break_down_id')]]['bin_no']=$row[csf('bin_no')];
			$leftover_data_arr[$row[csf('leftover_date')]][$row[csf('po_break_down_id')]]['job_no']=$row[csf('job_no')];
			
			$leftover_data_arr[$row[csf('leftover_date')]][$row[csf('po_break_down_id')]]['total_receive']+=$row[csf('total_left_over_receive')];

			$leftover_data_arr[$row[csf('leftover_date')]][$row[csf('po_break_down_id')]]['damage_receive'][$row[csf('goods_type')]][$row[csf('category_id')]]+=$row[csf('total_left_over_receive')];

			$leftover_data_arr[$row[csf('leftover_date')]][$row[csf('po_break_down_id')]]['receive'][$row[csf('goods_type')]][$row[csf('category_id')]]+=$row[csf('total_left_over_receive')];

			$leftover_data_arr[$row[csf('leftover_date')]][$row[csf('po_break_down_id')]]['leftover_amount']+=$row[csf('leftover_amount')];
		}
		unset($sql_result);
		
		//$leftover_data_arr = $sql_result;
		$issue_purpose_arr = array(1=>"Sell",2=>"Gift",3=>"Others"); 
		// if($report_type==1)
		// {
		?>
			<div style="width:2160px">
				<fieldset style="width:100%;">	
					<table id="table_header_1" class="rpt_table" width="2160" cellpadding="0" cellspacing="0" border="1" rules="all">
						<caption><strong><? echo $report_title.'<br>'.$company_arr[$company_name].'<br>'.$start_date.' To '.$end_date;?> </strong> </caption>
						<thead>
							<tr>
								<th width="30">SL</th>
								<th width="80">Receive Date</th>
								<th width="100">Buyer</th>
								<th width="100">Style</th>
								<th width="100">Order No</th>
								<th width="100">Job NO.</th>
								<th width="80">Goods Type</th>
								<th width="80">Category</th>
								<th width="80">Order Type</th>
								<th width="80">Rcv Qty</th>
								<th width="80">Rcv Fob</th>
								<th width="80">Rcv Amount</th>
								<th width="80">Rack No</th>
								<th width="80">Shelf No</th>
								<th width="80">Bin No</th>
								<th width="90">Remarks</th>
								<th width="80">Issue Date</th>
								<th width="80">Issue Qty</th>
								<th width="80">Stock In Hand</th>
								<th width="80">Stock Value</th>
								<th width="80">Sale Rate</th>
								<th width="80">Sale Amount</th>
								<th width="100">Issue Purpose</th>
								<th width="100">Party</th>
								<th width="">Remarks</th>
							</tr>
						</thead>
					</table>
					<div style="width:2180px; max-height:400px; overflow-y:scroll" id="scroll_body">
						<table class="rpt_table" width="2160" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
							<?
							$categories = array(1 => 'A', 2 => 'B',3 => 'C');
							$goods_type_arr = array(1=>"Good GMT In Hand",2=>"Damage GMT",3=>"Leftover Sample");	
							$i=1;
							$total_rev_qty=0;
							$total_issue_qty=0;
							$total_stock_qty=0;
							$total_stock_amt=0;
							$order_chk_array = array();
							foreach($leftover_data_arr as $recive_date => $date){
								foreach($date as $order_no => $row)
								{
									$stock_in_hand = $row['total_receive'] - $leftover_issue_data_arr[$order_no]['total_issue'];
									
									$total_rev_qty+=$row['total_receive'];
									if($order_chk_array[$order_no]=="")
									{
										$total_issue_qty+=$leftover_issue_data_arr[$order_no]['total_issue'];
										$order_chk_array[$order_no] = $order_no;
									}
									$total_stock_qty+=$stock_in_hand;

								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
									<td width="30"><? echo $i;?></td>
									<td width="80"><? echo change_date_format($row['leftover_date']);?></td>
									<td width="100"><? echo $buyer_arr[$row['buyer_name']];?></td>
									<td width="100"><p><? echo $row['style_ref_no'];?></p></td>
									<td width="100"><? echo $row['order_no'];?></td>
									<td width="100"><? echo $row['job_no'];?></td>
									<td width="80"><? echo $goods_type_arr[$row['goods_type']];?></td>
									<td width="80"><? echo $categories[$row['category_id']];?></td> 
									<td width="80"><? echo $order_source[$row['order_type']];?></td>
									<td width="80" align="center"><? echo $row['total_receive'];?></td>
									<td width="80" align="center"><? echo $row['fob_rate'];?></td>
									<td width="80" align="center"><? echo $row['leftover_amount'];?></td>
									<td width="80" align="center"><? echo $row['rack_no'];?></td>
									<td width="80" align="center"><? echo $row['shelf_no'];?></td>
									<td width="80" align="center"><? echo $row['bin_no'];?></td>
									<td width="90" align="center"><? echo $row['rcv_remarks'];?></td>
									<td width="80" align="center">
										<a href="##" onClick="open_issue_popup('<? echo $order_no ;?>**<? echo $leftover_issue_data_arr[$order_no]['issue_date'] ;?>','get_issue_dtls','Issue Details')">
											<? echo change_date_format($leftover_issue_data_arr[$order_no]['issue_date']);?>
										</a>
									</td>
									<td width="80" align="center"><? echo $leftover_issue_data_arr[$order_no]['total_issue'];?></td>
									<td width="80" align="center"><? echo $stock_in_hand;?></td>
									<td width="80" align="right"><? echo $stock_value = $row['fob_rate']*$stock_in_hand;?></td>
									<td width="80" align="center"><? echo $leftover_issue_data_arr[$order_no]['sale_rate'];?></td>
									<td width="80" align="right"><? echo number_format($leftover_issue_data_arr[$order_no]['bdt_amount'],2);?></td>
									<td width="100" align="center"><? echo $issue_purpose_arr[$leftover_issue_data_arr[$order_no]['issue_purpose']];?></td>
									<td width="100"><? echo $leftover_issue_data_arr[$order_no]['party_name'];?></td>
									<td width=""><? echo $leftover_issue_data_arr[$order_no]['remarks'];?></td>
								</tr>
								<?
								$total_stock_amt += $stock_value;
								$i++;
								}
							}
							?>
						</table>
					</div>
					<table class="rpt_table" width="2080" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
						<tfoot>
							<td width="30">&nbsp;</td>
							<td width="80">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="80">&nbsp;</td>
							<td width="80">&nbsp;</td>
							<td width="80">Total : </td>
							<td width="80" align="center"><? echo $total_rev_qty; ?></td>
							<td width="80">&nbsp;</td>
							<td width="80">&nbsp;</td>
							<td width="80">&nbsp;</td>
							<td width="80">&nbsp;</td>
							<td width="80">&nbsp;</td>
							<td width="90">&nbsp;</td>
							<td width="80">&nbsp;</td>
							<td width="80" align="center"><? echo $total_issue_qty; ?></td>
							<td width="80" align="center"><? echo $total_stock_qty; ?></td>
							<td width="80" align="right"><? echo $total_stock_amt; ?></td>
							<td width="80">&nbsp;</td>
							<td width="80">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="">&nbsp;</td>
						</tfoot>
					</table>
				</fieldset>
			</div>
		<?
	}
	
	else if($report_type== 2) //Show 2
	{
		if($cbo_location_id==0) $location_id_cond=""; else $location_id_cond=" and a.location in($cbo_location_id) ";
		if($store_name==0) $store_name_cond=""; else $store_name_cond=" and a.store_name in($store_name) ";
	
		if(str_replace("'","",$cbo_buyer_name)==0)
		{
			$buyer_id_cond="";
			$issue_buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond=" and a.buyer_name in($cbo_buyer_name)";
			$issue_buyer_id_cond=" and b.buyer_id in($cbo_buyer_name)";
		}
		
		$orer_job_cond="";
		
		if($search_by == 1 && $search_text !="")
		{
			$orer_job_cond=" and b.job_no like '%$search_text%'";
		}
		elseif($search_by == 2 && $search_text !="")
		{
			$orer_job_cond=" and b.style_ref_no like '%$search_text%'";
		}
		else
		{
			$orer_job_cond=" and b.order_no like '%$search_text%'";
		} 
	
		$date_cond='';	$ex_fact_date_cond='';$est_date_cond='';$reso_date_cond='';
		
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
			}
			$date_cond=" and a.leftover_date between '$start_date' and '$end_date'";
			$date_cond2=" and a.issue_date between '$start_date' and '$end_date'";
		}

		
	
		//================================== for receive ==================================
		$sql="SELECT a.id,a.buyer_name, b.style_ref_no, b.po_break_down_id, b.order_no, b.total_left_over_receive as total_receive,b.job_no from pro_leftover_gmts_rcv_mst a, pro_leftover_gmts_rcv_dtls b where a.id=b.mst_id and  a.status_active=1 and  a.is_deleted=0  and a.company_id=$company_name $location_id_cond $store_name_cond $buyer_id_cond $date_cond $orer_job_cond"; 
		// echo $sql; die;
		$sql_result = sql_select($sql); 
		foreach($sql_result as $v)
		{
			$po_break_down_id_array[$v['PO_BREAK_DOWN_ID']] = $v['PO_BREAK_DOWN_ID']; 
			$leftover_data_arr[$v['PO_BREAK_DOWN_ID']]['TOTAL_RECEIVE'] += $v['TOTAL_RECEIVE']; 
			$leftover_data_arr[$v['PO_BREAK_DOWN_ID']]['BUYER_NAME'] = $v['BUYER_NAME'];
			$leftover_data_arr[$v['PO_BREAK_DOWN_ID']]['STYLE_REF_NO'] = $v['STYLE_REF_NO'];
			$leftover_data_arr[$v['PO_BREAK_DOWN_ID']]['ORDER_NO'] = $v['ORDER_NO'];
			$leftover_data_arr[$v['PO_BREAK_DOWN_ID']]['JOB_NO']=$v['JOB_NO']; 
		}

		// ===================================== for issue ==============================
		// $sql_issue="SELECT b.po_break_down_id, b.order_no,b.style_ref_no, b.total_issue,b.buyer_id from pro_leftover_gmts_issue_mst a, pro_leftover_gmts_issue_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_name $location_id_cond $store_name_cond $issue_buyer_id_cond $orer_job_cond ";
		$po_id_con = where_con_using_array($po_break_down_id_array,0,'b.po_break_down_id');
		$sql_issue="SELECT b.po_break_down_id, b.order_no,b.style_ref_no, b.total_issue,b.buyer_id from pro_leftover_gmts_issue_mst a, pro_leftover_gmts_issue_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 $po_id_con $date_cond2";
		// echo $sql_issue; die;
		$sql_result_issue = sql_select($sql_issue);
		foreach($sql_result_issue as $v)
		{ 
			$leftover_issue_data_arr[$v['PO_BREAK_DOWN_ID']]['TOTAL_ISSUE']	+= $v['TOTAL_ISSUE'];
		}
		// pre($po_break_down_id_array);die;
						// =======================Order Qty ===================
		// pre($leftover_data_arr); die;
		$po_cond = where_con_using_array($po_break_down_id_array , 1 ,'a.id'); 
		$po_qty_sql = "SELECT a.id,a.po_quantity FROM WO_PO_BREAK_DOWN a  WHERE a.status_active=1 and a.is_deleted=0 $po_cond";
		// echo $po_qty_sql; die;
		$po_qty_sql_result = sql_select($po_qty_sql); 
		$po_qty_array = array();
		foreach ($po_qty_sql_result as $v) {
			 $po_qty_array [$v['ID']] +=  $v['PO_QUANTITY']; 
		} 

											// ============Production Data===========
		$po_cond = where_con_using_array($po_break_down_id_array , 1 ,'a.po_break_down_id'); 
		$prod_qty_sql = "SELECT a.po_break_down_id,a.production_quantity as prod_qty,production_type as prod_type FROM pro_garments_production_mst a  WHERE a.status_active=1 and a.is_deleted=0 and production_type in (1,4,5) $po_cond";
		$prod_qty_sql_result = sql_select($prod_qty_sql); 
		$prod_qty_array = array();
		foreach ($prod_qty_sql_result as $v) {
			$prod_qty_array [$v['PO_BREAK_DOWN_ID']] [$v['PROD_TYPE']] +=  $v['PROD_QTY'];  
		} 
											// ============Exfactory Data===========
		$po_cond = where_con_using_array($po_break_down_id_array , 1 ,'a.po_break_down_id'); 
		$ex_qty_sql = "SELECT a.po_break_down_id, sum(a.ex_factory_qnty) as ex_qty, max(ex_factory_date) as ex_date FROM pro_ex_factory_mst a  WHERE a.status_active=1 and a.is_deleted=0  $po_cond group by a.po_break_down_id";
		// echo $ex_qty_sql; die;
		$ex_qty_sql_result = sql_select($ex_qty_sql); 
		$ex_qty_array = array();
		foreach ($ex_qty_sql_result as $v) {
			$ex_qty_array [$v['PO_BREAK_DOWN_ID']] ['EX_QTY'] =  $v['EX_QTY'];  
			$ex_qty_array [$v['PO_BREAK_DOWN_ID']]['EX_DATE'] =  $v['EX_DATE'];  
		} 
		// pre($ex_qty_array);die;
		?> 
		<div style="width:1400px; margin-top:40px">
			<fieldset style="width:100%;">	
				<table id="table_header_1" class="rpt_table" width="1400" cellpadding="0" cellspacing="0" border="1" rules="all">
					<caption><strong><?= $report_title.'<br>'.$company_arr[$company_name].'<br>'.$start_date.' To '.$end_date;?> </strong> </caption>
					<thead>
						<tr>
							<th width="30">SL</th>
							<th width="100">Buyer</th>
							<th width="100">Style</th>
							<th width="100">Job NO.</th>    
							<th width="100">Order No</th>
							<th width="80">Order Qty</th>
							<th width="80">Last Exfactory Date</th>
							<th width="80">Cutting Qty</th>
							<th width="80">Seiwng Input Qty</th>
							<th width="80">Sewing Output Qty</th>
							<th width="80">Ex-Factory Qty</th>
							<th width="80">Cut to Ship Balance qty</th>
							<th width="80">Rcv Qty</th>
							<th width="80">Issue Qty</th>
							<th width="80">Stock In Hand</th>
						</tr>
					</thead>
				</table>
				<div style="width:1420px; max-height:400px; overflow-y:scroll" id="scroll_body">
					<table class="rpt_table" width="1400" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
						<tbody> 
							<? 
							$i=1;
							$total_rev_qty=0;
							$total_issue_qty=0; 
							$total_stock_in_hand=0; 
							foreach($leftover_data_arr as $order_no => $row)
							{ 
								$stock_in_hand = $row['TOTAL_RECEIVE'] - $leftover_issue_data_arr[$order_no]['TOTAL_ISSUE'];
								$total_stock_in_hand += $stock_in_hand; 
								$total_rev_qty += $row['TOTAL_RECEIVE'];
								$issue = $leftover_issue_data_arr[$order_no]['TOTAL_ISSUE'] ?? 0;
								$total_issue_qty += $issue; 
								$cut_qty = $prod_qty_array[$order_no][1];
								$sew_in_qty = $prod_qty_array[$order_no][4];
								$sew_out_qty = $prod_qty_array[$order_no][5];
								$ex_qty = $ex_qty_array[$order_no]['EX_QTY'];
								$ex_date = $ex_qty_array[$order_no]['EX_DATE'];
								$cut_to_ship_blnc = $cut_qty - $ex_qty ;

								?>
								<tr bgcolor="<?= $bgcolor;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor;?>')" id="tr_<?= $i; ?>">
									<td width="30"><?= $i;?></td> 
									<td width="100"><?= $buyer_arr[$row['BUYER_NAME']]; ?></td>
									<td width="100"><p><?= $row['STYLE_REF_NO']; ?></p></td>
									<td width="100"><?= $row['JOB_NO']; ?></td>  
									<td width="100" align="right" ><?= $row['ORDER_NO']; ?></td> 
									<td width="80" align="right" ><?= $po_qty_array[$order_no]; ?></td>  
									<td width="80"><?= $ex_date ?></td>
									<td width="80" align="right" ><?= $cut_qty ?></td>
									<td width="80" align="right" ><?= $sew_in_qty ?></td>
									<td width="80" align="right" ><?= $sew_out_qty ?></td>
									<td width="80" align="right" ><?= $ex_qty ?></td>
									<td width="80" align="right" ><?= $cut_to_ship_blnc ?></td>
									<td width="80" align="right" > <a href="##" onclick="open_leftover_popup(<?= $order_no;?>,'receive_popup','Leftover Garments Receive')"> <?= $row['TOTAL_RECEIVE']; ?> </a> </td>
									<td width="80" align="right" ><a href="##" onclick="open_leftover_popup(<?= $order_no;?>,'issue_popup','Leftover Garments Issue')"> <?= $issue ?> </a></td>
									<td width="80" align="right" ><?= $stock_in_hand ?></td> 
								</tr>
								<?
								$i++; 
							}
							?>
						</tbody>
						<tfoot>    
							<tr>
								<th colspan="6" align="right">Total : </th> 
								<th colspan="6"> </th>
								<th><?= $total_rev_qty ; ?></th> 
								<th><?= $total_issue_qty ; ?></th> 
								<th><?= $total_stock_in_hand ; ?></th> 
							</tr>
						</tfoot> 
					</table>
				</div>
			</fieldset>
		</div>
		<?
	}	
	else if($report_type==3)
	{
		?>
		<div style="width:2360px">
			<fieldset style="width:100%;">	
				<table id="table_header_1" class="rpt_table" width="2360" cellpadding="0" cellspacing="0" border="1" rules="all">
					<caption><strong><? echo $report_title.'<br>'.$company_arr[$company_name].'<br>'.$start_date.' To '.$end_date;?> </strong> </caption>
					<thead>
						<tr>
							<th rowspan="3" width="30">SL</th>
							<th rowspan="3" width="80">Receive Date</th>
							<th rowspan="3" width="100">Buyer</th>
							<th rowspan="3" width="100">Style</th>
							<th rowspan="3" width="100">Order No</th>
							<th rowspan="3" width="100">Job NO.</th>
							<th rowspan="3" width="80">Order Type</th>
							<th colspan="3">Good GMT (Quantity)</th>
							<th rowspan="3" width="80">Good GMT Total</th>
							<th rowspan="3" width="80">Damage GMT</th>
							<th rowspan="3" width="80">Total Rcv Qty</th>
							<th rowspan="3" width="80">Rcv Fob</th>
							<th rowspan="3" width="80">Rack No</th>
							<th rowspan="3" width="80">Shelf No</th>
							<th rowspan="3" width="80">Bin No</th>
							<th rowspan="3" width="80">Remarks</th>
							<th rowspan="3" width="80">Issue Date</th>
							<th rowspan="3" width="80">Issue Qty</th>
							<th rowspan="3" width="80">Stock In Hand</th>
							<th rowspan="3" width="80">Stock Value</th>
							<th rowspan="3" width="80">Sale Rate</th>
							<th rowspan="3" width="80">Sale Amount</th>
							<th rowspan="3" width="100">Issue Purpose</th>
							<th rowspan="3" width="100">Party</th>
							<th rowspan="3" width="200">Remarks</th>
						</tr>
						<tr>
							<th colspan="3">Category</th>
						</tr>
						<tr>
							<th width="50">A</th>
							<th width="50">B</th>
							<th width="50">C</th>
						</tr>
					</thead>
				</table>
				<div style="width:2380px; max-height:400px; overflow-y:scroll" id="scroll_body">
					<table class="rpt_table" width="2360" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
						<?
						$categories = array(1 => 'A', 2 => 'B',3 => 'C');
						$goods_type_arr = array(1=>"Good GMT In Hand",2=>"Damage GMT",3=>"Leftover Sample");	
						$i=1;
						$total_rev_qty=0;
						$total_issue_qty=0;
						$total_stock_qty=0;
						$total_stock_amt=0;
						$total_cat_a=0;
						$total_cat_b=0;
						$total_cat_c=0;
						$total_good_gmt=0;
						$total_damage_amt=0;
						foreach($leftover_data_arr as $recive_date => $date){
							foreach($date as $order_no => $row)
							{
								$stock_in_hand = $row['total_receive'] - $leftover_issue_data_arr[$order_no]['total_issue'];
								
								$total_rev_qty+=$row['total_receive'];
								$total_issue_qty+=$leftover_issue_data_arr[$order_no]['total_issue'];
								$total_stock_qty+=$stock_in_hand;

							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
								<td width="30"><? echo $i;?></td>
								<td width="80"><? echo change_date_format($row['leftover_date']);?></td>
								<td width="100"><? echo $buyer_arr[$row['buyer_name']];?></td>
								<td width="100"><? echo $row['style_ref_no'];?></td>
								<td width="100"><? echo $row['order_no'];?></td>
								<td width="100"><? echo $row['job_no'];?></td>
								<td width="80"><? echo $order_source[$row['order_type']];?></td>
								<td align="right" width="50">
									<?php
										$receive_qty = 0;
										if($row['category'][1]==1 && $row['goods'][1][$row['category'][1]]==1)
										{
											echo $row['receive'][1][1];

											$receive_qty += $row['receive'][1][1];
											$total_cat_a += $row['receive'][1][1];
										}
										else
										{
											echo 0;
											$receive_qty += 0;
											$total_cat_a += 0;
										}
									?>	
								</td>
								<td align="right" width="50">
									<?php
										if($row['category'][2]==2 && $row['goods'][1][$row['category'][2]]==1)
										{
											echo $row['receive'][1][2];

											$receive_qty += $row['receive'][1][2];
											$total_cat_b += $row['receive'][1][2];
										}
										else
										{
											echo 0;
											$receive_qty += 0;
											$total_cat_b += 0;
										}
									?>
								</td>
								<td align="right" width="50">
									<?php
										if($row['category'][3]==3 && $row['goods'][1][$row['category'][3]]==1)
										{
											echo $row['receive'][1][3];

											$receive_qty += $row['receive'][1][3];
											$total_cat_c += $row['receive'][1][3];
										}
										else
										{
											echo 0;
											$receive_qty += 0;
											$total_cat_c += 0;
										}
									?>
								</td>
								<td align="right" width="80">
									<?php 
										echo $receive_qty;
										$total_good_gmt +=$receive_qty;
									?>
								</td>
								<td align="right" width="80">
									<?php

										$damage_amt = 0;
										foreach($row['damage_receive'][2] as $data)
										{
											$total_damage_amt += $data;
											$damage_amt += $data;
										}

										echo $damage_amt;
									?>
								</td>
								<td width="80" align="center"><? echo $row['total_receive'];?></td>
								<td width="80" align="center"><? echo $row['fob_rate'];?></td>
								<td width="80" align="center"><? echo $row['rack_no'];?></td>
								<td width="80" align="center"><? echo $row['shelf_no'];?></td>
								<td width="80" align="center"><? echo $row['bin_no'];?></td>
								<td width="80" align="center"><? echo $row['rcv_remarks'];?></td>
								<td width="80" align="center">
									<a href="##" onClick="open_issue_popup('<? echo $order_no ;?>**<? echo $leftover_issue_data_arr[$order_no]['issue_date'] ;?>','get_issue_dtls','Issue Details')">
										<? echo change_date_format($leftover_issue_data_arr[$order_no]['issue_date']);?>
									</a>
								</td>
								<td width="80" align="center"><? echo $leftover_issue_data_arr[$order_no]['total_issue'];?></td>
								<td width="80" align="center"><? echo $stock_in_hand;?></td>
								<td width="80" align="right"><? echo $stock_value = $row['fob_rate']*$stock_in_hand;?></td>
								<td width="80" align="center"><? echo $leftover_issue_data_arr[$order_no]['sale_rate'];?></td>
								<td width="80" align="right"><? echo number_format($leftover_issue_data_arr[$order_no]['bdt_amount'],2);?></td>
								<td width="100" align="center"><? echo $issue_purpose_arr[$leftover_issue_data_arr[$order_no]['issue_purpose']];?></td>
								<td width="100"><? echo $leftover_issue_data_arr[$order_no]['party_name'];?></td>
								<td width="200"><? echo $leftover_issue_data_arr[$order_no]['remarks'];?></td>
							</tr>
							<?
							$total_stock_amt += $stock_value;
							$i++;
							}
						}
						?>
					</table>
				</div>
				<table class="rpt_table" width="2360" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
					<tfoot>
						<td width="30">&nbsp;</td>
						<td width="80">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td align="right" width="80">Total : </td>
						<td id="total_cat_a" align="right" width="50"><? echo $total_cat_a; ?></td>
						<td id="total_cat_b" align="right" width="50"><? echo $total_cat_b; ?></td>
						<td id="total_cat_c" align="right" width="50"><? echo $total_cat_c; ?></td>
						<td id="total_good_gmt" align="right" width="80"><? echo $total_good_gmt; ?></td>
						<td id="total_damage_amt" align="right" width="80"><? echo $total_damage_amt; ?></td>
						<td id="total_rev_qty" width="80" align="center"><? echo $total_rev_qty; ?></td>
						<td width="80">&nbsp;</td>
						<td width="80">&nbsp;</td>
						<td width="80">&nbsp;</td>
						<td width="80">&nbsp;</td>
						<td width="80">&nbsp;</td>
						<td width="80">&nbsp;</td>
						<td id="total_issue_qty" width="80" align="center"><? echo $total_issue_qty; ?></td>
						<td id="total_stock_qty" width="80" align="center"><? echo $total_stock_qty; ?></td>
						<td id="total_stock_amt" width="80" align="right"><? echo $total_stock_amt; ?></td>
						<td width="80">&nbsp;</td>
						<td width="80">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="200">&nbsp;</td>
					</tfoot>
				</table>
			</fieldset>
		</div>
	<?
	}	
	
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("$user_id*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html****$filename****$report_type"; 
	exit();	
}

if($action=="get_issue_dtls")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);

	$data = explode("**", $data);
	$po_id = $data[0];
	$date = $data[1];

	$sql = "SELECT a.issue_date,b.sale_rate,sum(b.total_issue) as total_issue from pro_leftover_gmts_issue_mst a, pro_leftover_gmts_issue_dtls b where a.id=b.mst_id and b.po_break_down_id=$po_id and a.status_active=1 and b.status_active=1 group by a.issue_date,b.sale_rate";
	$sql_res = sql_select($sql);
	?>
	<div style="margin: 10px auto;clear: both;width: 450px;">
		<table class="rpt_table" cellpadding="0" cellspacing="0" rules="all" width="450" border="1">
			<caption style="font-size: 24px;font-weight: bold;">Issue Details</caption>
			<thead>
				<tr>
					<th width="50">Sl</th>
					<th width="100">Date</th>
					<th width="100">Qty</th>
					<th width="100">Rate</th>
					<th width="100">Value</th>
				</tr>
			</thead>
			<tbody>
				<?
				$sl=1;
				$total_qty = 0;
				$total_val = 0;
				foreach ($sql_res as $val) 
				{
					$value = $val[csf('total_issue')]*$val[csf('sale_rate')];
					?>
					<tr>
						<td align="center"><? echo $sl;?></td>
						<td align="center"><? echo change_date_format($val[csf('issue_date')]);?></td>
						<td align="right"><? echo $val[csf('total_issue')];?></td>
						<td align="center"><? echo number_format($val[csf('sale_rate')],2);?></td>
						<td align="right"><? echo number_format($value,2);?></td>
					</tr>
					<?
					$total_qty += $val[csf('total_issue')];
					$total_val += $value;
				}
				$sl++;
				?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="2" align="right"><b>Total</b></th>
					<th align="center"><? echo $total_qty;?></th>
					<th align="center"></th>
					<th align="center"><? echo number_format($total_val,2);?></th>
				</tr>
			</tfoot>
		</table>
	</div>
	<?
}

die();


































if ($action=="buyer_location_multi_select")
	{
		//echo "set_multiselect('cbo_location_id','0','0','','0');\n";
		//echo "set_multiselect('cbo_buyer_name','0','0','','0');\n";
		exit();
	}


//--------------------------------------------------------------------------------------------------------------------

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $job_no;
	
	if($search_type==1 || $search_type==2 || $search_type==3) $search_cond=$job_no;
	//else if($search_type==2) $search_cond=$job_no;
	
	?>
	<script>
	
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_job_id").val(splitData[0]); 
			$("#hide_job_no").val(splitData[1]); 
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:580px;">
            <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref",3=>"Order No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
					?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" value="<? //echo $search_cond;?>" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>'+'**'+'<? echo $search_type; ?>'+'**'+'<? echo $job_no; ?>'+'**'+'<? echo $po_no; ?>', 'create_job_no_search_list_view', 'search_div', 'leftover_garments_receive_and_issue_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}
if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$month_id=$data[5];
	$search_type=$data[6];
	$po_no=$data[8];
	$job_no=$data[7];
	//echo $month_id;
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$data[1]";
	}
	
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";
	if($search_by==2) $search_field="a.style_ref_no";
	 else if($search_by==1) $search_field="a.job_no";
	 else $search_field="b.po_number";
	//if($job_no!='') $job_no_cond="and a.job_no_prefix_num=$job_no"; else $job_no_cond="";
	//$year="year(insert_date)";
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";
	
	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(a.insert_date)=$year_id"; else $year_cond="";	
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
	}
	if($search_type==1)
	{
		$search_filed="id,job_no_prefix_num";
	}
	else if($search_type==2)
	{
		$search_filed="po_id,po_number";
	}
	else
	{
		$search_filed="id,style_ref_no";
	}
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	$sql= "select a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.id as po_id,b.po_number, $year_field from wo_po_details_master a,wo_po_break_down b where   b.job_no_mst=a.job_no and a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond  order by job_no";
	
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No,Po No", "120,130,80,60,100","700","240",0, $sql , "js_set_value", "$search_filed", "", 1, "company_name,buyer_name,0,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no,po_number", "",'','0,0,0,0,0,0','') ;
	exit(); 
} // Job Search end



$tmplte=explode("**",$data);

if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;


if($action=="report_generate2") 
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$report_type=str_replace("'","",$reporttype);
	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	
	$cbo_year=str_replace("'","",$cbo_year);
	$txt_style_id=str_replace("'","",$txt_style_id);
	$style_ref_no=str_replace("'","",$txt_style_no);
	$cbo_product_cat=str_replace("'","",$cbo_product_cat);
	$cbo_location_id=str_replace("'","",$cbo_location_id); 
	//$report_type=str_replace("'","",$report_type);
	$report_title=str_replace("'","",$report_title);
	
	if($txt_style_id=="") 	$style_id_cond=""; 		else $style_id_cond		=" and a.id in ($txt_style_id) ";
	if($style_ref_no=="") 	$style_no_cond=""; 		else $style_no_cond		=" and a.style_ref_no='$style_ref_no' ";
	if($cbo_product_cat==0) $product_cat_cond=""; 	else $product_cat_cond	=" and a.product_category=$cbo_product_cat ";
	if($cbo_location_id==0) $location_id_cond=""; 	else $location_id_cond	=" and c.location_id in($cbo_location_id) ";
	
	
	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond =" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; 
			else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name in($cbo_buyer_name)";
	}

	$cbo_year=str_replace("'","",$cbo_year);
	if($db_type==0)
	{
		if(trim($cbo_year)!=0) $year_cond=" and YEAR(a.insert_date)=$cbo_year"; else $year_cond="";
	}
	else if($db_type==2)
	{
		if(trim($cbo_year)!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year"; else $year_cond="";
	}
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	
	$date_cond='';	$ex_fact_date_cond='';$est_date_cond='';$reso_date_cond='';$pre_cost_date_cond='';
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		if($db_type==0)
		{
			$start_date	= change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
			$end_date	= change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$start_date	= change_date_format(str_replace("'","",$txt_date_from),"","",1);
			$end_date	= change_date_format(str_replace("'","",$txt_date_to),"","",1);
		}
		$date_cond			= " and c.production_date  between '$start_date' and '$end_date'";
		$ex_fact_date_cond	= " and c.ex_factory_date  between '$start_date' and '$end_date'";
		$est_date_cond		= " and c.production_date  between '$start_date' and '$end_date'";
		$reso_date_cond		= " and e.pr_date  between '$start_date' and '$end_date'";
		$pre_cost_date_cond	= " and c.costing_date  between '$start_date' and '$end_date'";
	}
	
	//echo $report_type.'dd';die;
	
	
	$financial_para=array();
	$sql_std_para=sql_select("select cost_per_minute,interest_expense,income_tax,applying_period_date as from_period_date,applying_period_to_date from lib_standard_cm_entry where company_id=$company_name and status_active=1 and is_deleted=0  order by id desc");	
	foreach($sql_std_para as $row)
	{
		$date_key=date("Y-m",strtotime($row[csf("from_period_date")]));
			
		$financial_para2[$date_key]['cost_per_minute']	=$row[csf('cost_per_minute')];
		$financial_para2[$date_key]['interest_expense']	=$row[csf('interest_expense')];
		$financial_para2[$date_key]['income_tax']		=$row[csf('income_tax')];
		/*$applying_period_date=change_date_format($row[csf('from_period_date')],'','',1);
		$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
		$diff=datediff('d',$applying_period_date,$applying_period_to_date);
		for($j=0;$j<$diff;$j++)
		{
			$newdate =change_date_format(add_date(str_replace("'","",$applying_period_date),$j),'','',1);
			$financial_para[$newdate]['cost_per_minute']=$row[csf('cost_per_minute')];
			$financial_para[$newdate]['interest_expense']=$row[csf('interest_expense')];
			$financial_para[$newdate]['income_tax']=$row[csf('income_tax')];
		}*/
	
	}
	unset($sql_std_para);
	//echo "<pre>";
	//print_r($financial_para2['2018-05']);die;
	
	$sql_precost=sql_select("select a.job_no, b.id,c.costing_date, c.costing_per, d.cm_cost, d.margin_pcs_set 
	from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_mst c, wo_pre_cost_dtls d 
	where  a.job_no=b.job_no_mst and a.job_no=c.job_no and a.job_no=d.job_no  and c.job_no=d.job_no  and a.company_name=$company_name and c.status_active=1 and c.is_deleted=0 $buyer_id_cond $year_cond $style_no_cond  $style_id_cond  $product_cat_cond 
	order by a.id desc"); //$pre_cost_date_cond
	foreach($sql_precost as $row)
	{
		//$costing_date=date("d-m-Y", strtotime($row[csf('costing_date')]));
		//$cost_date = change_date_format($row[csf('costing_date')],'','',1);
		//$pre_cost_date_arr[$row[csf('job_no')]]['cost_per_minute']	=$financial_para[$cost_date]['cost_per_minute'];
		//$pre_cost_date_arr[$row[csf('job_no')]]['interest_expense']	=$financial_para[$cost_date]['interest_expense'];
		//$pre_cost_date_arr[$row[csf('job_no')]]['income_tax']		=$financial_para[$cost_date]['income_tax'];
		
		//$date_key=date("Y-m",strtotime($row[csf("costing_date")]));
		
		
		$pre_cost_date_arr[$row[csf('job_no')]]['margin_pcs_set']	=$row[csf('margin_pcs_set')];
		$pre_cost_date_arr[$row[csf('job_no')]]['costing_date']		=$row[csf('costing_date')];
		$pre_cost_date_arr[$row[csf('job_no')]]['costing_per']		=$row[csf('costing_per')];
		
		$pre_cost_date_arr2[$row[csf('id')]]['cm_cost']				=$row[csf('cm_cost')];
	}
	unset($sql_precost);
	//echo "<pre>";
	//print_r($pre_cost_date_arr3['FAL-18-00056']);die;
	
	$sql_result="select a.company_name as company_id, a.job_quantity, a.total_price, a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, a.total_set_qnty as ratio, b.id as po_id, b.unit_price, b.po_number, b.pub_shipment_date, c.produced_min  as produced_min, c.efficency_min, c.total_produced  as production_quantity, c.total_target, c.production_date
	from wo_po_details_master a, wo_po_break_down b, pro_resource_ava_min_dtls c
	where a.job_no=b.job_no_mst and c.order_ids=b.id and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $location_id_cond $buyer_id_cond $year_cond $style_no_cond $style_id_cond $product_cat_cond  $est_date_cond
	order by b.id"; 
	$data_result=sql_select($sql_result);
	$prod_detail_arr=array();
	
	$i=$z=1; 
	$all_full_job=""; 
	$total_po_qty = $total_fab_req_qty = $total_po_qty_pcs = $total_produced_min = $total_effecincy = $total_cm_cost_earning = $total_fob_earning = $total_cm_cost = $total_profit_loss = $total_po_total_price = 0;
	$all_po_id='';
	foreach($data_result as $row)
	{
		// $po_wise_prod_data_arr[$row[csf('po_id')]]['pub_date']	=$row[csf('pub_shipment_date')];
		$po_wise_prod_data_arr[$row[csf('po_id')]]['production_date']	=$row[csf('production_date')];
		$po_wise_prod_data_arr[$row[csf('po_id')]]['job_no']	=$row[csf('job_no')];
		$po_wise_prod_data_arr[$row[csf('po_id')]]['buyer']		=$row[csf('buyer_name')];
		$po_wise_prod_data_arr[$row[csf('po_id')]]['po_no']			=$row[csf('po_number')];
		$po_wise_prod_data_arr[$row[csf('po_id')]]['unit_price']	=$row[csf('unit_price')];
		$po_wise_prod_data_arr[$row[csf('po_id')]]['production_quantity']+=$row[csf('production_quantity')];
		$po_wise_prod_data_arr[$row[csf('po_id')]]['produced_min']	+=$row[csf('produced_min')];
		$po_wise_prod_data_arr[$row[csf('po_id')]]['efficency_min']	+=$row[csf('efficency_min')];
		
		
		// $po_wise_prod_data_arr[$row[csf('po_id')]]['season_matrix']	=$row[csf('season_matrix')];
		// $po_wise_prod_data_arr[$row[csf('po_id')]]['gmts_item_id']	=$row[csf('gmts_item_id')];
		// $po_wise_prod_data_arr[$row[csf('po_id')]]['style_ref_no']	=$row[csf('style_ref_no')];
		// $po_wise_prod_data_arr[$row[csf('po_id')]]['po_quantity']	=$row[csf('po_quantity')]*$row[csf('ratio')];
		// $po_wise_prod_data_arr[$row[csf('po_id')]]['po_total_price']=$row[csf('po_total_price')];
		// $po_wise_prod_data_arr[$row[csf('po_id')]]['total_target']	=$row[csf('total_target')];
		// $po_wise_prod_data_arr[$row[csf('po_id')]]['po_id']			=$row[csf('po_id')];
		
		$job_wise_prod_data_arr[$row[csf('job_no')]]['row_span']	+=1;
		
		$job_wise_prod_data_arr[$row[csf('job_no')]]['job_quantity']=$row[csf('job_quantity')];
		$job_wise_prod_data_arr[$row[csf('job_no')]]['total_price']	=$row[csf('total_price')];
		
		$buyer_wise_prod_data_arr[$row[csf('buyer_name')]]['production_quantity']	+=$row[csf('production_quantity')];
		$buyer_wise_prod_data_arr[$row[csf('buyer_name')]]['efficency_min']			+=$row[csf('efficency_min')];
		
		if($all_po_id=='') $all_po_id=$row[csf('po_id')];	
		else  $all_po_id.=",".$row[csf('po_id')];
		
		if($all_full_job=="") $all_full_job="'".$row[csf('job_no')]."'"; 	
		else $all_full_job.=","."'".$row[csf('job_no')]."'";
	}
	unset($data_result);
	
	
	
	$all_job_no=array_unique(explode(",",$all_full_job));
	$all_jobs="";
	foreach($all_job_no as $jno)
	{
		if($all_jobs=="") $all_jobs=$jno; else $all_jobs.=",".$jno;
	}

	$poIds=implode(",",array_unique(explode(",",$all_po_id)));
	$poIds=chop($poIds,','); $po_cond_for_in=""; $po_cond_for_in2=""; 
	$po_ids=count(array_unique(explode(",",$poIds)));
	if($db_type==2 && $po_ids>1000)
	{
		$po_cond_for_in		=" and (";
		$po_cond_for_in2	=" and (";
		$poIdsArr	=array_chunk(explode(",",$poIds),999);
		foreach($poIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$po_cond_for_in		.=" b.po_breakdown_id in($ids) or"; 
			$po_cond_for_in2	.=" b.id in($ids) or"; 
		}
		$po_cond_for_in	=chop($po_cond_for_in,'or ');
		$po_cond_for_in		.=")";
		$po_cond_for_in2	=chop($po_cond_for_in2,'or ');
		$po_cond_for_in2	.=")";
	}
	else
	{
		$poIds	=implode(",",array_unique(explode(",",$poIds)));
		$po_cond_for_in		=" and b.po_breakdown_id in($poIds)";
		$po_cond_for_in2	=" and b.id in($poIds)";
	}
	
	$all_job_cond	="and b.job_no_mst in($all_jobs)";
	
	$sql_resouce="SELECT b.id as po_id, d.target_per_line as target_per_hour, c.working_hour
	FROM  wo_po_break_down b, prod_resource_dtls_mast c, prod_resource_color_size d, prod_resource_dtls e
	WHERE c.id=d.dtls_id  and d.po_id=b.id and e.mast_dtl_id=c.id  and b.is_deleted=0 and b.status_active=1 and d.is_deleted=0 and d.status_active=1 $all_job_cond $reso_date_cond order by b.id";
	$result_resource=sql_select($sql_resouce);
	foreach($result_resource as $row)
	{
		$res_prod_data_arr[$row[csf('po_id')]]['target']	+=$row[csf('target_per_hour')]*$row[csf('working_hour')];
		//$res_prod_data_arr[$row[csf('po_id')]]['efficency_min']+=$row[csf('efficency_min')];
	}
	unset($result_resource);
	
	$exfactory_res="SELECT c.po_break_down_id as po_id,
	sum(CASE WHEN c.entry_form!=85 THEN c.ex_factory_qnty ELSE 0 END)-sum(CASE WHEN c.entry_form=85 THEN c.ex_factory_qnty ELSE 0 END) as ex_factory_qnty
	FROM wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c
	WHERE a.job_no=b.job_no_mst  and  c.po_break_down_id=b.id   and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $ex_fact_date_cond $po_cond_for_in2 $buyer_id_cond $year_cond $style_no_cond  $style_id_cond  $product_cat_cond  group by c.po_break_down_id order by c.po_break_down_id";
	$result_exf=sql_select($exfactory_res);
	foreach($result_exf as $row)
	{
		$exfactory_qty_arr[$row[csf('po_id')]]['ex_factory_qnty']=$row[csf('ex_factory_qnty')];
	}
	unset($result_exf);
	
	ob_start();
	?>
	<div style="width:1020px">
	<fieldset style="width:100%;">	
		<table id="table_header_1" class="rpt_table" width="1000" cellpadding="0" cellspacing="0" border="1" rules="all">
		<caption><strong><? echo $report_title.'<br>'.$company_arr[$company_name].'<br>'.$start_date.' To '.$end_date;
		?> </strong> </caption>
		<thead>
			<tr>
				<th width="30" >SL</th>
				<th width="120">Buyer</th>
				<th width="90">Avg. SMV</th>
				<th width="90">Effeciency</th>
				<th width="90">QC Pass Qty</th>
				<th width="90">Spent Minute</th>
				<th width="90">Produced Min</th>
				<th width="90">CM Earning</th>
				<th width="90">CM %</th>
				<th width="90">FOB on Sweing Qty</th>
				<th width="120">Cost Spend Minutes</th>
			</tr>
		</thead>
		</table>
		<div style="width:1020px; max-height:400px; overflow-y:scroll" id="scroll_body">
		<table class="rpt_table" width="1000" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
		<?
		$total_prod_sew_out_qty=$total_avaiable_spent_min=$total_produced_min=$total_tot_knit_balance_qnty=$total_tot_fin_com_qty=$total_tot_dye_balance_qnty=$total_tot_cut_balance_qnty=$total_tot_embrod_balance_qnty=$total_tot_sew_in_qty=$total_shipment_value=0;
		
		$condition= new condition();
		$condition->company_name("=$company_name");
		if(str_replace("'","",$cbo_buyer_name)>0){
			$condition->buyer_name("=$cbo_buyer_name");
		}
		if($style_ref_no!=''){
			$condition->style_ref_no("='$style_ref_no'");
		}
		if($db_type==0 || $db_type==2)
		{
			if(str_replace("'","",$all_jobs)!='')
			{
				$condition->job_no("in($all_jobs)");
			}
		}
		
		$condition->init();
		$other	= new other($condition);
		$yarn	= new yarn($condition);
		$fabric	= new fabric($condition);
		$conversion	= new conversion($condition);
		$trim	= new trims($condition);
		$emblishment= new emblishment($condition);
		$wash	= new wash($condition);
		$commercial	= new commercial($condition);
		$commission	= new commision($condition);
		//echo $other->getQuery(); die;
		$fabric_costing_arr		= $fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
		$yarn_costing_arr		= $yarn->getJobWiseYarnAmountArray();
		$trim_arr_amount		= $trim->getAmountArray_by_job();
		$conversion_costing_arr	= $conversion->getAmountArray_by_job();
		$emblishment_amount_arr	= $emblishment->getAmountArray_by_job();
		$wash_amount_arr		= $wash->getAmountArray_by_job();
		$commercial_amount_arr	= $commercial->getAmountArray_by_job();
		$commission_amount_arr	= $commission->getAmountArray_by_job();
		//print_r($emblishment_amount_arr);
		$other_costing_arr		= $other->getAmountArray_by_job(); 
		$j=1;
		
		foreach($po_wise_prod_data_arr as $po_key=>$val)
		{
			$date_key=date("Y-m",strtotime($val["production_date"]));
			
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			
			$job_no=$val['job_no'];
			
			$job_quantity	 	= $job_wise_prod_data_arr[$job_no]['job_quantity'];
			$total_job_value 	= $job_wise_prod_data_arr[$job_no]['total_price'];
			
			$fabric_cost_knit 	= array_sum($fabric_costing_arr['knit']['grey'][$job_no]);
			$fabric_cost_wv		= array_sum($fabric_costing_arr['woven']['grey'][$job_no]);
			
			$fabric_cost		= $fabric_cost_knit+$fabric_cost_wv;
			$yarn_cost			= $yarn_costing_arr[$job_no];
			$trim_cost			= $trim_arr_amount[$job_no];
			$conversion_cost	= array_sum($conversion_costing_arr[$job_no]);
			$emblishment_cost	= $emblishment_amount_arr[$job_no];
			$wash_cost			= $wash_amount_arr[$job_no];
			$commercial_cost	= $commercial_amount_arr[$job_no];
			$commission_cost	= $commission_amount_arr[$job_no];
			
			$lab_cost			= $other_costing_arr[$job_no]['lab_test'];
			$inspection_cost	= $other_costing_arr[$job_no]['inspection'];
			$currier_cost		= $other_costing_arr[$job_no]['currier_pre_cost'];
			$certificate_cost 	= $other_costing_arr[$job_no]['certificate_pre_cost'];
			$common_oh_cost		= $other_costing_arr[$job_no]['common_oh'];
			$freight_cost		= $other_costing_arr[$job_no]['freight'];
			$po_cm_cost			= $other_costing_arr[$job_no]['cm_cost'];
			$design_cost		= $other_costing_arr[$job_no]['design_cost'];
			$studio_cost		= $other_costing_arr[$job_no]['studio_cost'];
			$depr_amor_pre_cost	= $other_costing_arr[$job_no]['depr_amor_pre_cost'];
			
			$interest_expense	= $financial_para2[$date_key]['interest_expense']/100;
			$income_tax			= $financial_para2[$date_key]['income_tax']/100;
			$NetFOBValue_job	= $total_job_value-$commission_cost;
			//$interest_expense_job=$NetFOBValue_job*$interest_expense;
			//$income_tax_job=$NetFOBValue_job*$income_tax;
			
			$total_other_cost	= $lab_cost+$inspection_cost+$currier_cost+$certificate_cost+$common_oh_cost+$freight_cost+$depr_amor_pre_cost+$design_cost+$studio_cost+$po_cm_cost;
			$total_cost			= $fabric_cost+$yarn_cost+$trim_cost+$conversion_cost+$emblishment_cost+$wash_cost+$commercial_cost+$commission_cost+$total_other_cost+$interest_expense_job+$income_tax_job;
			
			$net_job_profit_value = $total_job_value-$total_cost;
			
			$produced_min		= $val['produced_min'];
			
			//$pre_costing_date	= $pre_cost_date_arr[$val['job_no']]['costing_date'];
			$pre_costing_date	= $date_key."-01";
			
			$cost_per_minute	= $financial_para2[$date_key]['cost_per_minute'];
			
			$avaiable_spent_min = $val['efficency_min'];
			$prod_sew_out_qty	= $val['production_quantity'];
			
			$cm_cost_earning_per_pcs=($po_cm_cost+$net_job_profit_value)/$job_quantity;
			$cm_cost_earning	= $cm_cost_earning_per_pcs*$prod_sew_out_qty;
			/*if($db_type==0)
			{
				$conversion_date = change_date_format($pre_costing_date, "Y-m-d", "-",1);
			}
			else
			{
				$conversion_date = change_date_format($pre_costing_date, "d-M-y", "-",1);
			}*/
			
			
			
			if($avaiable_spent_min>0  || $produced_min !=0)
			{
				
				$usd_id=2;
				//$currency_rate	= set_conversion_rate($usd_id,$conversion_date );
				$currency_rate	= set_conversion_rate($usd_id,$pre_costing_date );
				
				$buyer_wise_arr[$val['buyer']]['avg_smv']+=($prod_sew_out_qty=="0")? $prod_sew_out_qty : $produced_min/$prod_sew_out_qty;
				
				$buyer_wise_arr[$val['buyer']]['tot_effecincy']+=$produced_min/$avaiable_spent_min;
				$buyer_wise_arr[$val['buyer']]['qc_pass_sew_out_qty'] += $prod_sew_out_qty;
				$buyer_wise_arr[$val['buyer']]['avaiable_spent_min'] += $avaiable_spent_min;
				$buyer_wise_arr[$val['buyer']]['produced_min'] += $produced_min;
				$buyer_wise_arr[$val['buyer']]['cm_cost_earning'] += $cm_cost_earning;
				$buyer_wise_arr[$val['buyer']]['price_per_qty'] += $val['unit_price']*$prod_sew_out_qty;
				$buyer_wise_arr[$val['buyer']]['fob_sew_qty']+=$val['unit_price']*$prod_sew_out_qty;
				$buyer_wise_arr[$val['buyer']]['cost_spend_minutes'] =($cost_per_minute)/$currency_rate;
			}
		}
		
		
		foreach($buyer_wise_arr as $buyer_id=>$buyer_data)
		{	
			?>
			<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
				<td width="30"><? echo $i; ?></td>
				<td width="120"><? echo $buyer_arr[$buyer_id]; ?></td>
				
				<td width="90" align="right" title="Produced Min/Production(Sewing Out)"><? 
					echo number_format($buyer_data['avg_smv'],2);
					?></td>
                <td  width="90" align="right"  title="Produced Min/ Available Min"><? 
					echo number_format($buyer_data['tot_effecincy'],2); 
					?></td>
				<td width="90" title="" align="right"><? 
				echo number_format($buyer_data['qc_pass_sew_out_qty'],2);
				?></td>
				<td width="90" title="Available Min"  align="right" ><? 
				echo $buyer_data['avaiable_spent_min']; 
				?></td>
				<td width="90"  align="right"><?  echo $buyer_data['produced_min'];  ?></td>
				<td  width="90" title="" align="right"><?  
				echo number_format($buyer_data['cm_cost_earning'],2); 
				?></td>
				<td  width="90" align="right"><?
				 ($buyer_data['price_per_qty']>0)? $cmPercent = ($buyer_data['cm_cost_earning'] / $buyer_data['price_per_qty'])*100 : $cmPercent = 0 ;
				echo number_format( $cmPercent,2);
				?></td>
				<td width="90"  title=""  align="right"><? 
				echo number_format($buyer_data['fob_sew_qty'],2); 
				?></td>
				<td width="120"  title="" align="right"><? 
				echo number_format($buyer_data['cost_spend_minutes'],4); 
				?></td>
			</tr>
			<?
			$i++;
			
			//$total_prod_sew_out_qty		+= $buyer_data['qc_pass_sew_out_qty'];
			//$total_avaiable_spent_min	+= $buyer_data['avaiable_spent_min'];
			//$total_produced_min			+= $buyer_data['produced_min'];
			//$total_cm_cost_earning		+= $buyer_data['cm_cost_earning'];
			//$total_fob_earning			+= $buyer_data['fob_sew_qty'];
		}
		?>
		</table>
		</div>
		<table class="rpt_table" width="1000" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
			<tfoot>
				<th width="30">&nbsp;</th>
				<th width="120">&nbsp;</th>
				<th width="90">&nbsp;</th>
				<th width="90">&nbsp;</th>
				<th width="90" align="right" id="value_total_prod_sew_out_qty"><? //echo number_format($total_prod_sew_out_qty,2); ?></th>
				<th width="90" align="right" id="value_total_avaiable_spent_min"><? //echo number_format($total_avaiable_spent_min,2); ?></th>
                <th width="90" align="right" id="value_total_produced_min"><? //echo number_format($total_produced_min,2); ?> </th>
				<th width="90" align="right" id="value_total_cm_cost_earning"><? //echo number_format($total_cm_cost_earning,2); ?> </th>
                <th width="90">&nbsp;</th>
				<th width="90" align="right" id="value_total_fob_earning"><? //echo number_format($total_fob_earning,2); ?> </th>
				<th width="120">&nbsp;</th>
			</tfoot>
		</table>
	</fieldset>
	<?
                      echo signature_table(136, $company_name, "450px");
			?>
	</div>
	<?	
	
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("$user_id*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    
	exit("$html****$filename****$report_type");	
}

?>
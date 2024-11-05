<?

header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------

if($action=="print_button_variable_setting")
{
	 
    $print_report_format_arr = return_library_array("select format_id,format_id from lib_report_template where template_name in($data) and module_id=17 and report_id=214 and is_deleted=0 and status_active=1","format_id","format_id");
    echo "print_report_button_setting('".implode(',',$print_report_format_arr)."');\n";
    exit(); 
}




if($action=="load_drop_down_subsection")
{
	$data=explode('_',$data);
	if($data[0]==1) $subID='1,2,3,23';
	else if($data[0]==3) $subID='4,5,18';
	else if($data[0]==5) $subID='6,7,8,9,10,11,12,13,16,17,17,24';
	else if($data[0]==10) $subID='14,15';
	else if($data[0]==7) $subID='19,20,21,25,26';
	else if($data[0]==9) $subID='22';
	else $subID='0';
	//echo $data[0]."**".$subID;
	//echo create_drop_down( "cboembtype_".$data[1], 80,$emb_type,"", 1, "-- Select --", "", "","","" ); 
	echo create_drop_down( "cbo_sub_section_id", 100, $trims_sub_section,"",1, "-- Select Sub-Section --","","",0,$subID,'','','','','',"");
	exit();
}

if ($action=="load_drop_down_member")
{
	$data=explode("_",$data);
	$sql="select b.id,b.team_member_name  from lib_marketing_team a, lib_mkt_team_member_info b where a.id=b.team_id and   a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.project_type=3 and team_id='$data[0]'";
	echo create_drop_down( "cbo_team_member", 150, $sql,"id,team_member_name", 1, "-- Select Member --", $selected, "" );	
	exit();
}

$company_library=return_library_array( "select id, company_name from lib_company where status_active =1 and is_deleted=0", "id", "company_name"  );
$team_leader_arr_library=return_library_array( "select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0", "id", "team_leader_name"  );
$team_member_name=return_library_array( "select id, team_member_name from lib_mkt_team_member_info", "id", "team_member_name"  );
$buyer_arr_library=return_library_array( "select id, buyer_name from lib_buyer where status_active =1 and is_deleted=0", "id", "buyer_name"  );



if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_name=str_replace("'","",$cbo_company_name);
	$section_id=str_replace("'","",$cbo_section_id);
	$sub_section_id=str_replace("'","",$cbo_sub_section_id);
	$team_leader=str_replace("'","",$cbo_team_leader);
	$team_member=str_replace("'","",$cbo_team_member);
	$year_from=str_replace("'","",$cbo_year_from);
	$month_from=str_replace("'","",$cbo_month_from);
	$year_to=str_replace("'","",$cbo_year_to);
	$month_to=str_replace("'","",$cbo_month_to);
	$reportType=str_replace("'","",$reportType);
	$cbo_Status_type=str_replace("'","",$cbo_Status_type);
	

	if($reportType==1){
		if($section_id==0)
		{
		$section_cond="";
		$section_id_cond_order="";
		}
		else
		{
			$section_cond=" and b.section=$section_id";
			$section_id_cond_order=" and a.section_id=$section_id";
		}
		
		
		if($sub_section_id==0)
		{
		$sub_section_cond="";
		$sub_section_cond_order="";
		}
		else
		{
			$sub_section_cond=" and a.sub_section_id=$sub_section_id";
			$sub_section_cond_order=" and b.sub_section=$sub_section_id";
		}
		
		if($team_leader==0)
		{
			$team_leader_cond="";
			$team_leader_order_cond="";
		}
		else
		{
			$team_leader_order_cond=" and a.team_leader=$team_leader";
			$team_leader_cond=" and a.team_leader_id=$team_leader";
		}
		
		if($team_member==0)
		{
			$team_member_cond="";
			$team_member_target_cond="";
		}
		else
		{
			$team_member_cond=" and a.team_member=$team_member";
			$team_member_target_cond=" and a.team_member_id=$team_member";
		}
		//--------------------------------------------------start	
			
		
		$start_date=$year_from."-".$month_from."-01";
		$num_days = cal_days_in_month(CAL_GREGORIAN, $month_to, $year_to);
		$end_date=$year_to."-".$month_to."-".$num_days;
		$maonthYearCond='';
		for($x=$year_from; $x <= $year_to;  $x++)
		{
			//echo "aa=";
			for($i=$month_from; $i <= 12;  $i++)
			{
				if($maonthYearCond=="")
				{
					$maonthYearCond.=" and ( b.year_id=$x and b.month_id=$i ";
				}
				else
				{
					$maonthYearCond.=" or  ( b.year_id=$x and b.month_id=$i) ";
				}
			}
		}
		$maonthYearCond.=")";
		
		if($db_type==0) 
		{
			$start_date=change_date_format($start_date,'yyyy-mm-dd');
			$end_date=change_date_format($end_date,'yyyy-mm-dd');
		}
		else if($db_type==2) 
		{
			$start_date=change_date_format($start_date,'','',1);
			$end_date=change_date_format($end_date,'','',1);
		}
		$tot_month = datediff( 'm', $start_date,$end_date);
		
		$month_year_arr=array();
		
		for($i=0; $i<= $tot_month; $i++ )
		{
			$next_month=month_add($start_date,$i);
			$month_year_arr[]=date("M-Y",strtotime($next_month));
		}
		
		//-------------------------------end
		
		if($db_type==0) 
		{
			//( year_id=2019 and month_id>=1)  or  ( year_id=2020 and month_id<=1) or  ( year_id=2021 and month_id<=1) 
			$date_cond_order=" and b.delivery_date between '$start_date' and '$end_date'";
		}
		if($db_type==2) 
		{
			$date_cond_order=" and a.delivery_date between '".date("j-M-Y",strtotime($start_date))."' and '".date("j-M-Y",strtotime($end_date))."'";
		}
		ob_start();

		$order_sql="select a.team_leader, a.team_member,a.delivery_date,b.section ,b.sub_section ,b.booked_qty,b.amount,b.booked_uom,ltrim(TO_CHAR(a.delivery_date,'mm'),'0') AS month,ltrim(TO_CHAR(a.delivery_date,'yyyy'),'0') AS year from subcon_ord_mst a, subcon_ord_dtls b  where a.subcon_job=b.job_no_mst and a.company_id=$company_name  $date_cond_order $team_leader_order_cond $section_cond $team_member_cond $sub_section_cond_order";
		$order_sql_res=sql_select($order_sql);
			
		foreach ($order_sql_res as $row)
		{ 
			$orderDataArr[$row[csf("team_leader")]][$row[csf("team_member")]][$row[csf("section")]][$row[csf("sub_section")]]['year_month_qty_amt'].=$row[csf("year")]."_".$row[csf("month")]."_".$row[csf("booked_qty")]."_".$row[csf("amount")]."_".$row[csf("booked_uom")]."##";

			$key=$row[csf("team_leader")]."_".$row[csf("team_member")]."_".$row[csf("section")]."_".$row[csf("sub_section")]."_".$row[csf("year")]."_".$row[csf("month")];
			
		}
		/*echo "<pre>";	
		print_r($orderDataArr);*/
		
		$target_sql="select a.company_id,a.section_id,a.sub_section_id,a.team_leader_id,a.team_member_id,a.starting_month_id,b.year_id,b.month_id,b.uom_id, b.quantity, b.amount  from trims_sales_target_mst a,trims_sales_target_dtls b where a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and a.company_id=$company_name $maonthYearCond  $team_leader_cond $section_id_cond_order $team_member_target_cond $sub_section_cond";
		$sql_sales=sql_select($target_sql);
		$dataArr=array();
		foreach($sql_sales as $row) 
		{
			$dataArr[$row[csf("team_leader_id")]][$row[csf("team_member_id")]][$row[csf("section_id")]][$row[csf("sub_section_id")]]['year_month_qty_amt'].=$row[csf("year_id")]."_".$row[csf("month_id")]."_".$row[csf("quantity")]."_".$row[csf("amount")]."_".$row[csf("uom_id")]."##";
			$target_key=$row[csf("team_leader_id")]."_".$row[csf("team_member_id")]."_".$row[csf("section_id")]."_".$row[csf("sub_section_id")]."_".$row[csf("year_id")]."_".$row[csf("month_id")];
			
		}



		//echo "<pre>";	
		//print_r($teamLeader);

		$total_month=count($month_year_arr);
		$width=$total_month*(3*90)+(650+100+150)+($total_month*20);
			
		?>
		<br>
		<fieldset style="width:<? echo $width+20; ?>px;">
		<table cellpadding="0" cellspacing="0" width="<? echo $width; ?>">
			<tr>
			<td align="center" width="100%" colspan="<? echo $total_month*2+6+1 ?>" class="form_caption" style="font-size:16px;"><? echo $company_library[$company_name]; ?></td>
			</tr>
			<tr>
			<td align="center" width="100%" colspan="<? echo $total_month*2+6+1 ?>" class="form_caption"><strong>Sales Forecast Vs Booked</strong></td>
			</tr>
		</table>	
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width; ?>" class="rpt_table" >
			<thead>
				<tr>
					<th width="40" rowspan="2">SL</th>
					<th width="100" rowspan="2">Team Leader</th>
					<th width="100" rowspan="2">Team Member</th>
					<th width="100" rowspan="2">Section</th>
					<th rowspan="2">Sub-Section</th>
					<th width="100" rowspan="2">Perticulars</th>
					<?
						foreach($month_year_arr as $yearMonth)
						{
							list($month,$year)=explode("-",$yearMonth);
						?>
							<th width="200" colspan="3"><p><? echo  $month.'-'.$year; ?></p></th>
						<?	
						}
					?>
				<th colspan="3" width="200">Month Total</th>
				</tr>
				
				<tr>
					<?
						foreach($month_year_arr as $yearMonth=>$val)
						{
						?>
							<th width="90">UOM</th>
							<th width="90">Quantity</th>
							<th width="110">Value ($)</th>
						<?	
						}
					?>
					<th width="90">UOM</th>
					<th width="90">Quantity</th>
					<th width="110">Value ($)</th>
				</tr>
			</thead>
		</table>
		<div style="width:<? echo $width+18; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width; ?>" class="rpt_table" id="table_body" >
				<? 
					$i=1; 
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";  
					$lead_var_arr=array(); $totLeadVariQty=0; $totLedVariAmt=0; $totQty=0; $totAmt=0;
					foreach($dataArr as $leader_id=>$leader_data)
					{ 
						//$lead_var_arr=array(); $totLeadVariQty=0; $totLedVariAmt=0;
						foreach($leader_data as $member_id=>$member_data)
						{ 
							$totMemVariQty=0; $totMemVariAmt=0; $mem_var_arr=array();
							foreach($member_data as $section_id=>$section_data)
							{ 
								//$year_month_qty_amt='';
								foreach($section_data as $sub_section_id=>$sub_section_data)
								{ 
									$totForQty=0; $totForAmt=0; $totOrdQty=0; $totOrdAmt=0; $totVarQty=0; $totVarAmt=0;
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_colors('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
										<td width="40" rowspan="3" valign="middle" align="center"><? echo $i; ?></td>
										<td width="100" rowspan="3" valign="middle"><p><? echo $team_leader_arr_library[$leader_id]; ?>&nbsp;</p></td>
										<td width="100" rowspan="3" valign="middle"><p><? echo  $team_member_name[$member_id]; ?>&nbsp;</p></td>
										<td  width="100" rowspan="3" valign="middle"><p><? echo $trims_section[$section_id]; ?></p></td>
										<td rowspan="3" valign="middle"><p><? echo $trims_sub_section[$sub_section_id]; ?></p></td>
										<td width="100"><p>Forecast</p></td>
											<? 
											$z=1; $qty_arr=array(); $qty_amt_arr=array(); //$year_month_qty_amt='';
											//echo $sub_section_data['year_month_qty_amt'];
											$yearMonthQtyMmt_arr=explode("##",$sub_section_data['year_month_qty_amt']);
											foreach ($yearMonthQtyMmt_arr as $values) 
											{
												$val=explode("_",$values);
												$ym=$months_short[$val[1]].'-'.$val[0];
												$qty_amt_arr[$ym]['qty']+=$val[2];
												$qty_amt_arr[$ym]['amt']+=$val[3];
												$qty_amt_arr[$ym]['uom']=$unit_of_measurement[$val[4]]; 
												
											}
											foreach($month_year_arr as $month_id)
											{
												$totForQty +=$qty_amt_arr[$month_id]['qty'];
												$totForAmt +=$qty_amt_arr[$month_id]['amt'];
												?>
												<td width="90" align="right"><? echo $qty_amt_arr[$month_id]['uom']; ?></td>
												<td width="90" align="right"><? echo $qty_amt_arr[$month_id]['qty']; ?></td>
												<td width="110" align="right"><? echo $qty_amt_arr[$month_id]['amt']; ?></td>
												<?
												$z++;
											}
										?>
										<td align="right" width="90"><? echo $tot_sales_uom; ?></td>
										<td align="right" width="90"><? echo number_format($totForQty); ?></td>
										<td align="right" width="110"><? echo number_format($totForAmt,2,'.',','); ?></td>
									</tr>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_colors('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>1"> 
									<td width="100"><p>Booked</p></td>
										<?
										$order_qty_amt_arr=array();
										$order_data=$orderDataArr[$leader_id][$member_id][$section_id][$sub_section_id]['year_month_qty_amt'];
										if($order_data!='')
										{
											$order_yearMonthQtyMmt_arr=explode("##",$order_data); $ym='';
											foreach ($order_yearMonthQtyMmt_arr as $values) 
											{
												$val=explode("_",$values);
												$ym=$months_short[$val[1]].'-'.$val[0];
												$order_qty_amt_arr[$ym]['qty']+=$val[2];
												$order_qty_amt_arr[$ym]['amt']+=$val[3];
												$order_qty_amt_arr[$ym]['uom']=$unit_of_measurement[$val[4]];
												
											}
										}
										$tot_projectqty=0; $tot_projectqty=0; $tot_projectamount=0; $z=1;
										foreach($month_year_arr as $month_id)
										{
											$totOrdQty +=$order_qty_amt_arr[$month_id]['qty'];
											$totOrdAmt +=$order_qty_amt_arr[$month_id]['amt'];
											?>
											<td width="90" align="right"><? echo $order_qty_amt_arr[$month_id]['uom']; ?></td>
											<td width="90" align="right"><? echo $order_qty_amt_arr[$month_id]['qty']; ?></td>
											<td width="110" align="right"><? echo $order_qty_amt_arr[$month_id]['amt']; ?></td>
											<?
											$z++;
											$booked_uom=$order_qty_amt_arr[$month_id]['uom'];
										}
										?>
										<td align="right" width="90"><? echo $booked_uom; ?></td>
										<td align="right" width="90"><? echo number_format($totOrdQty); ?></td>
										<td align="right"><? echo number_format($totOrdAmt,2,'.',','); ?></td>
									</tr>
									<tr bgcolor="#f7a8a8" onClick="change_colors('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>3"> 
										<td width="100"><b>Variance</b></td>
										<? 
											$tot_variance_qnty=0; $tot_variance_amount=0; $z=1;
											$varQty=0; $varAmt=0; $subTotQty=0; $subTotAmt=0;
											foreach($month_year_arr as $month_id)
											{
												$sales_qnty=$qty_amt_arr[$month_id]['qty'];
												$sales_qnty_val=$qty_amt_arr[$month_id]['amt'];
												$sales_qnty_uom=$order_qty_amt_arr[$month_id]['uom'];

												$projectqty=$order_qty_amt_arr[$month_id]['qty'];
												$projectamount=$order_qty_amt_arr[$month_id]['amt'];
												
												$variance_qnty=($projectqty)-$sales_qnty;
												$variance_amount=($projectamount)-$sales_qnty_val;
												$td_va_color=$td_vq_color='';
												if( $variance_qnty < 0){$td_vq_color="#f7a8a8";}
												if( $variance_amount < 0){$td_va_color="#f7a8a8";}

												$totVarQty=$order_qty_amt_arr[$month_id]['qty']-$qty_amt_arr[$month_id]['qty'];
												$totVarAmt=$order_qty_amt_arr[$month_id]['amt']-$qty_amt_arr[$month_id]['amt'];
												$mem_var_arr[$month_id]['qty'] +=$totVarQty;
												$mem_var_arr[$month_id]['amt'] +=$totVarAmt;
												$subTotQty+=$totVarQty;
												$subTotAmt+=$totVarAmt;
												?>
												<td width="90" align="right" bgcolor="<? echo $td_vq_color;?>"><b><? echo $sales_qnty_uom; ?></b></td>
												<td width="90" align="right" bgcolor="<? echo $td_vq_color;?>"><b><? echo number_format($totVarQty); ?></b></td>
												<td width="90" align="right" bgcolor="<? echo $td_va_color;?>"><b><? echo number_format($totVarAmt,2); ?></b></td>
												<?
												$z++;
											}

											$td_va_color=$td_vq_color='';
											if( $tot_variance_qnty < 0){$td_vq_color="#f7a8a8";}
											if( ($tot_projectamount)-$tot_sales_qnty_val < 0){$td_va_color="#f7a8a8";}
											$var_qty=$qty_amt_arr[$month_id]['qty']-$ord_qty_amt_arr[$month_id]['qty'];

											$grandTotQty+=$subTotQty;
											$grandTotAmt+=$subTotAmt;
											?>
											<td align="right" width="90" bgcolor="<? echo $td_vq_color;?>"><b><?  echo $qty_amt_arr[$month_id]['uom']; ?></b></td>
											<td align="right" width="90" bgcolor="<? echo $td_vq_color;?>"><b><?   echo number_format($subTotQty); ?></b></td>
											<td align="right" bgcolor="<? echo $td_va_color;?>"><b><? echo number_format(($subTotAmt),2,'.',',');?></b></td>
										</tr>
										<?
										$i++;
										}
									}
									?>
									<tr bgcolor="#fad7a0" onClick="change_colors('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>1">			 								
										<td colspan="6" align="right" style="background:#fad7a0">Team Member Variance</td>
										<?
											$p=1; $subTotVarQty=0; $subTotVarAmt=0; 
											foreach($month_year_arr as $month_id)
											{
												$totMemVariQty=$mem_var_arr[$month_id]['qty'];
												$totMemVariAmt=$mem_var_arr[$month_id]['amt'];

												$lead_var_arr[$month_id]['qty'] +=$totMemVariQty;
												$lead_var_arr[$month_id]['amt'] +=$totMemVariAmt;
												$subTotVarQty+=$totMemVariQty;
												$subTotVarAmt+=$totMemVariAmt;

												?>
												<td width="90" align="right"><? echo $order_qty_amt_arr[$month_id]['uom']; ?></td>
												<td width="90" align="right"><? //echo $totMemVariQty;?></td>
												<td width="110" align="right"><? echo $totMemVariAmt; ?></td>
												<?
												$p++;
											}
										?>
										<td align="right" width="90"></td>
										<td align="right" width="90"><? //echo number_format($subTotVarQty); ?></td>
										<td align="right"><? echo number_format($subTotVarAmt,2,'.',','); ?></td>
									</tr>	  
									<?  
									}
								}
							?>
						<tfoot>
							<tr style="background:#FF9">
								<td colspan="6" align="right"><b>Team Leader Variance</b></td>
								<?
									foreach($month_year_arr as $month_id)
									{
										$totLedVariAmt=$lead_var_arr[$month_id]['amt'];
										?>
										<td align="right"><b><? ?></b></td>
										<td align="right"><b><? ?></b></td>
										<td align="right"><b><? echo number_format($totLedVariAmt,2,'.',','); ?></td>
										<?	
									}
								?>
								<td align="right"><b><?  ?></b></td>
								<td align="right"><b><? ?></b></td>
								<td align="right"><b><? echo number_format($grandTotAmt,2,'.',','); ?></b></td>
							</tr>
						</tfoot>    
					</table>
				</div>
			</fieldset> 
		<?
	}
	else
	{
		$str_cond="";
		$str_cond2="";
		$cbo_status="";
		if($company_name){ $str_cond.=" and a.COMPANY_ID=$company_name";$str_cond2.=" and a.COMPANY_ID=$company_name";} 
		if($team_leader){ $str_cond.=" and a.team_leader=$team_leader"; $str_cond2.=" and a.team_leader_id=$team_leader";}
		if($team_member){ $str_cond.=" and a.team_member=$team_member"; $str_cond2.=" and a.team_member_id=$team_member";}
		if($section_id){ $str_cond.=" and b.section=$section_id"; $str_cond2.=" and a.section_id=$section_id";}
		if($sub_section_id){ $str_cond.=" and b.sub_section=$sub_section_id"; $str_cond2.=" and a.sub_section_id=$sub_section_id";}
	    if($cbo_Status_type=='') $str_cond.="";else $str_cond.=" and a.status like '%$cbo_Status_type%'";

		$start_date=$year_from."-".$month_from."-01";
		$num_days = cal_days_in_month(CAL_GREGORIAN, $month_to, $year_to);
		$end_date=$year_to."-".$month_to."-".$num_days;
		$maonthYearCond='';
		for($x=$year_from; $x <= $year_to;  $x++)
		{
			//echo "aa=";
			for($i=$month_from; $i <= 12;  $i++)
			{
				if($maonthYearCond=="")
				{
					$maonthYearCond.=" and ( b.year_id=$x and b.month_id=$i ";
				}
				else
				{
					$maonthYearCond.=" or  ( b.year_id=$x and b.month_id=$i) ";
				}
			}
		}
		$maonthYearCond.=")";
		
		if($db_type==0) 
		{
			$start_date=change_date_format($start_date,'yyyy-mm-dd');
			$end_date=change_date_format($end_date,'yyyy-mm-dd');
		}
		else if($db_type==2) 
		{
			$start_date=change_date_format($start_date,'','',1);
			$end_date=change_date_format($end_date,'','',1);
		}
		$tot_month = datediff( 'm', $start_date,$end_date);
		
		$month_year_arr=array();
		
		for($i=0; $i<= $tot_month; $i++ )
		{
			$next_month=month_add($start_date,$i);
			$month_year_arr[]=date("M-Y",strtotime($next_month));
		}
		
		//-------------------------------end
		
		if($db_type==0) 
		{
			//( year_id=2019 and month_id>=1)  or  ( year_id=2020 and month_id<=1) or  ( year_id=2021 and month_id<=1) 
			$str_cond.=" and a.receive_date between '$start_date' and '$end_date'";
		}
		if($db_type==2) 
		{
			$str_cond.=" and a.receive_date between '".date("j-M-Y",strtotime($start_date))."' and '".date("j-M-Y",strtotime($end_date))."'";
		}

        $order_sql="SELECT a.team_leader as TEAM_LEADER, a.team_member as TEAM_MEMBER, a.currency_id as CURRENCY_ID, b.amount as AMOUNT,ltrim(TO_CHAR(a.receive_date,'mm'),'0') AS MONTH,ltrim(TO_CHAR(a.receive_date,'yyyy'),'0') AS YEAR, a.status from subcon_ord_mst a, subcon_ord_dtls b  where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and a.entry_form=255 and a.id = b.mst_id and b.status_active=1 and b.is_deleted=0 $str_cond";

		// echo $order_sql;
        $order_sql_res=sql_select($order_sql);
        if($db_type==0){
            $lib_currency_data=sql_select("select conversion_rate from currency_conversion_rate where  company_id=$company_name and currency=2 order by id desc limit 1");
        }else{
            $lib_currency_data=sql_select("select conversion_rate from currency_conversion_rate where  company_id=$company_name and currency=2 and rownum<2 order by id desc");
        }
        $currency_conversion_rate=$lib_currency_data[0][csf("conversion_rate")];
        $orderDataArr=array();
        $orderQtyArr=array();
        foreach ($order_sql_res as $row)
        {
            if($row['CURRENCY_ID']==1)
                $amount=$row['AMOUNT']/$currency_conversion_rate;
            else
                $amount=$row['AMOUNT'];

//			$orderDataArr[$row["TEAM_LEADER"]][$row["TEAM_MEMBER"]]['team_leader']=$row["TEAM_LEADER"];
//			$orderDataArr[$row["TEAM_LEADER"]][$row["TEAM_MEMBER"]]['team_member']=$row["TEAM_MEMBER"];
            if($amount > 0)
                $orderQtyArr[$row["TEAM_LEADER"]][$row["TEAM_MEMBER"]][$months_short[$row["MONTH"]]."-".$row["YEAR"]]["order_amount"]+=$amount;

        }

        $target_sql="SELECT a.team_leader_id as TEAM_LEADER_ID,a.team_member_id as TEAM_MEMBER_ID,b.year_id as YEAR_ID,b.month_id as MONTH_ID, b.amount as AMOUNT from trims_sales_target_mst a,trims_sales_target_dtls b where a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 $str_cond2 $maonthYearCond";
        $sql_sales=sql_select($target_sql);
        $dataArr=array();
        foreach($sql_sales as $row)
        {
            if($row["AMOUNT"] > 0)
                $orderQtyArr[$row["TEAM_LEADER_ID"]][$row["TEAM_MEMBER_ID"]][$months_short[$row["MONTH_ID"]]."-".$row["YEAR_ID"]]["target_amount"]+=$row["AMOUNT"];

        }

		$total_month=count($month_year_arr);
		$width=240+$total_month*180;
		ob_start();
		
		?>
        <style>
            .margine{
                margin-left: 15px;
            }
        </style>
		<br>
		<fieldset style="width:<? echo $width; ?>px;" class="margine">
		<table cellpadding="0" cellspacing="0" width="<? echo $width; ?>">
		   <tr>
			<td align="center" width="100%" colspan="" class="form_caption" style="font-size:16px;">Target Achievement Report				</td>
			</tr>
			<tr>
			<td align="center" width="100%" colspan="" class="form_caption" style="font-size:16px;"><? echo $company_library[$company_name]; ?></td>
			</tr>
			<tr>
			<td align="center" width="100%" colspan="" class="form_caption" style="font-size:16px;"><? echo $start_date." TO ".$end_date ?></td>
			</tr>
		</table>	
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width; ?>" class="rpt_table" >
			<thead>
				<tr>
					<th width="40" rowspan="2">SL</th>
					<th width="100" rowspan="2">Team Leader</th>
					<th width="100" rowspan="2">Mkt. By</th>
					<? 
						foreach($month_year_arr as $yearMonth)
						{
							list($month,$year)=explode("-",$yearMonth);
						?>
							<th  colspan="3"><p><? echo  $month.'-'.$year; ?></p></th>
						<?	
						}
					?>
				</tr>
				<tr>
					<?
						foreach($month_year_arr as $yearMonth)
						{
						?>
							<th width="60">Order Amount ($)</th>
							<th width="60">Target</th>
							<th  width="60">Target Achv %</th>
						<?	
						}
					?>
				</tr>
			</thead>
		</table>
		<div style="width:<? echo $width+18; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width; ?>" class="rpt_table" id="table_body" >
					
				<tbody>
					<?
					$i=1;
					foreach($orderQtyArr as $team_leader_key=>$team_leader_value){
						foreach($team_leader_value as $team_member_key=>$row){
							?>
							<tr>
								<td style="word-break: break-all" width="40" align="center"><? echo $i; ?></td>
								<td style="word-break: break-all" width="100"><p><? echo $team_leader_arr_library[$team_leader_key]; ?>&nbsp;</p></td>
								<td style="word-break: break-all" width="100"><p><? echo  $team_member_name[$team_member_key]; ?>&nbsp;</p></td>
								<?
									foreach($month_year_arr as $yearMonth)
									{
										list($month,$year)=explode("-",$yearMonth);
										$order_amount=$row[$month."-".$year]["order_amount"];
										$target_amount=$row[$month."-".$year]["target_amount"];
									?>
										<td style="word-break: break-all" width="60" align="right"><? echo number_format($order_amount,2) ;?></td>
										<td style="word-break: break-all" width="60" align="right"><? echo number_format($target_amount,2);?></td>
										<td style="word-break: break-all" width="60" align="right"><? echo number_format($order_amount/$target_amount*100,2);?></td>
									<?	
									$total_target[$yearMonth]+=$target_amount;
									$total_order[$yearMonth]+=$order_amount;
									$total_achive[$yearMonth] +=$order_amount/$target_amount*100;

									}
								?>
							</tr>
							<?
							$i++;

						}
					}
					?>
				</tbody>

			</table>
		</div>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width; ?>" class="rpt_table" >
			<tfoot>
				
				<tr>
					<th width="40" ></th>
					<th width="100" >	</th>
					<th width="100" >G.Total:	</th>
					<?
						foreach($month_year_arr as $yearMonth)
						{
						?>
							<th style="word-break: break-all" width="60"><?echo number_format($total_order[$yearMonth],2);?></th>
							<th style="word-break: break-all" width="60"><?echo number_format($total_target[$yearMonth],2);?></th>
							<th style="word-break: break-all"  width="60"><?echo number_format($total_achive[$yearMonth],2);?></th>
						<?	
						}
					?>
				</tr>
			</tfoot>
		</table>

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

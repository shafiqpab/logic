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
		
	$year_from=str_replace("'","",$cbo_year_from);
	$month_from=str_replace("'","",$cbo_month_from);
	$start_date=$year_from."-".$month_from."-01";
	$year_to=str_replace("'","",$cbo_year_to);
	$month_to=str_replace("'","",$cbo_month_to);
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
                <th width="100" rowspan="2">Team Leader  </th>
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
            <th colspan="3" width="200">Total</th>
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
                foreach($dataArr as $leader_id=>$leader_data)
                { 
                	foreach($leader_data as $member_id=>$member_data)
                    { 
                    	foreach($member_data as $section_id=>$section_data)
                        { 
                        	//$year_month_qty_amt='';
                        	foreach($section_data as $sub_section_id=>$sub_section_data)
	                        { 
               					?>
			                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_colors('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
			                        <td width="40" rowspan="3" valign="middle" align="center"><? echo $i; ?></td>
			                        <td width="100" rowspan="3" valign="middle"><p><? echo $team_leader_arr_library[$leader_id]; ?>&nbsp;</p></td>
			                        <td width="100" rowspan="3" valign="middle"><p><? echo  $team_member_name[$member_id]; ?>&nbsp;</p></td>
			                   		<td  width="100" rowspan="3" valign="middle"><p><? echo $trims_section[$section_id]; ?></p></td>
			                        <td rowspan="3" valign="middle"><p><? echo $trims_sub_section[$sub_section_id]; ?></p></td>
			                        <td width="100"><p>Forecast</p></td>
			                        	<? //$agent_arr_library
										$tot_sales_qty=0; $tot_sales_qnty_val=0; $z=1; $qty_arr=array(); $qty_amt_arr=array(); //$year_month_qty_amt='';

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
										//echo "<pre>";
										//print_r($qty_amt_arr);
									 	foreach($month_year_arr as $month_id)
										{
											?>
			                            	<td width="90" align="right"><? echo $qty_amt_arr[$month_id]['uom']; ?></td>
			                                <td width="90" align="right"><? echo $qty_amt_arr[$month_id]['qty']; ?></td>
			                                <td width="110" align="right"><? echo $qty_amt_arr[$month_id]['amt']; ?></td>
			                            	<?
											$z++;
											$tot_sales_uom=$qty_amt_arr[$month_id]['uom'];;	
											$tot_sales_qty+=$qty_amt_arr[$month_id]['qty'];	
											$tot_sales_qnty_val+=$qty_amt_arr[$month_id]['amt'];
												
											$tot_sales_qty_month[$month_id]+=$tot_sales_qty;	
											$tot_sales_qnty_val_month[$month_id]+=$tot_sales_qnty_val;	
										}
									?>
			                        <td align="right" width="90"><? echo $tot_sales_uom; ?></td>
			                        <td align="right" width="90"><? echo number_format($tot_sales_qty); ?></td>
			                        <td align="right" width="110"><? echo number_format($tot_sales_qnty_val,2,'.',','); ?></td>
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
			                        
										$tot_projectqty=0; $tot_projectqty=0;$tot_projectamount=0; $z=1;
									 	foreach($month_year_arr as $month_id)
										{
										?>
			                                <td width="90" align="right"><? echo $order_qty_amt_arr[$month_id]['uom']; ?></td>
			                                <td width="90" align="right"><? echo $order_qty_amt_arr[$month_id]['qty']; ?></td>
			                                <td width="110" align="right"><? echo $order_qty_amt_arr[$month_id]['amt']; ?></td>
			                            <?
											$z++;
											$booked_uom=$order_qty_amt_arr[$month_id]['uom'];	
											$tot_projectqty+=$order_qty_amt_arr[$month_id]['qty'];	
											$tot_projectamount+=$order_qty_amt_arr[$month_id]['amt'];
											
											
											$tot_projectqty_month[$month_id]+=$projectqty;	
											$tot_projectamount_month[$month_id]+=$projectamount;	
										}
									?>
			                        <td align="right" width="90"><? echo $booked_uom; ?></td>
			                        <td align="right" width="90"><? echo number_format($tot_projectqty,2,'.',','); ?></td>
			                        <td align="right"><? echo number_format($tot_projectamount,2,'.',','); ?></td>
			                    </tr>
			                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_colors('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>3"> 
			                       <td width="100"><b>Variance</b></td>
			                        <? //$agent_arr_library
										$tot_variance_qnty=0; $tot_variance_amount=0; $z=1;
									 	foreach($month_year_arr as $month_id)
										{
											 //echo $buyer_id;
											
											$sales_qnty=$qty_amt_arr[$month_id]['qty'];
											$sales_qnty_val=$qty_amt_arr[$month_id]['amt'];
											$sales_qnty_uom=$order_qty_amt_arr[$month_id]['uom'];

											$projectqty=$order_qty_amt_arr[$month_id]['qty'];
											$projectamount=$order_qty_amt_arr[$month_id]['amt'];
											
											$variance_qnty=($projectqty)-$sales_qnty;
											$variance_amount=($projectamount)-$sales_qnty_val;
											$td_va_color=$td_vq_color='';
											if( $variance_qnty < 0){$td_vq_color="#f00";}
											if( $variance_amount < 0){$td_va_color="#f00";}
											?>
			                                <td width="90" align="right" bgcolor="<? echo $td_vq_color;?>"><b><? echo $sales_qnty_uom; ?></b></td>
			                                 <td width="90" align="right" bgcolor="<? echo $td_vq_color;?>"><b><? echo number_format($variance_qnty); ?></b></td>
			                                <td width="90" align="right" bgcolor="<? echo $td_va_color;?>"><b><? echo number_format($variance_amount,2); ?></b></td>
			                            	<?
											$z++;
											$tot_variance_uom=$sales_qnty_uom;	
											$tot_variance_qnty+=$variance_qnty;	
											$tot_variance_amount+=$variance_amount;
												
											$tot_variance_uom[$month_id]=$sales_qnty_uom;	
											$tot_variance_qnty_month[$month_id]+=$variance_qnty;	
											$tot_variance_amount_month[$month_id]+=$variance_amount;	
										}
									
										$td_va_color=$td_vq_color='';
										if( $tot_variance_qnty < 0){$td_vq_color="#f00";}
										if( ($tot_projectamount)-$tot_sales_qnty_val < 0){$td_va_color="#f00";}
										?>
				                        <td align="right" width="90" bgcolor="<? echo $td_vq_color;?>"><b><?  echo $qty_amt_arr[$month_id]['uom']; ?></b></td>
				                        <td align="right" width="90" bgcolor="<? echo $td_vq_color;?>"><b><? echo number_format($tot_variance_qnty); ?></b></td>
				                        <td align="right" bgcolor="<? echo $td_va_color;?>"><b><? echo number_format(($tot_variance_amount),2,'.',',');?></b></td>
				                    </tr>
					                    <?
					                    $i++;
										
			                        }
									
		                  }
						  
						  
						 ?>
						  
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_colors('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>1">			 								<td colspan="6" align="right" style="background:#FF8C00">Team Member Variance</td>
			                        <?
										 $p=1;
									 	foreach($month_year_arr as $month_id)
										{
										?>
			                                <td width="90" align="right"><? echo $order_qty_amt_arr[$month_id]['uom']; ?></td>
			                                <td width="90" align="right"><? echo $tot_variance_qnty_month[$month_id];?></td>
			                                <td width="110" align="right"><? echo $tot_variance_amount_month[$month_id] ?></td>
			                            <?
											$p++;
											$tot_projectqty+=$order_qty_amt_arr[$month_id]['qty'];	
											$tot_projectamount+=$order_qty_amt_arr[$month_id]['amt'];
											
											
											$tot_projectqty_month[$month_id]+=$projectqty;	
											$tot_projectamount_month[$month_id]+=$projectamount;	
										}
									?>
			                        <td align="right" width="90"></td>
			                        <td align="right" width="90"><? echo number_format($tot_projectqty,2,'.',','); ?></td>
			                        <td align="right"><? echo number_format($tot_projectamount,2,'.',','); ?></td>
			                    </tr>	  
	               <?  }
                }
                
			?>
            
        	<tfoot>
            	<tr style="background:#FF9">
                    <td colspan="6" align="right"><b>Team Leader Variance</b></td>
					<?
                        foreach($month_year_arr as $month_id)
                        {
                            ?>
                            <td align="right"><b><? ?></b></td>
                            <td align="right"><b><? ?></b></td>
                            <td align="right"><b><? echo number_format($tot_sales_qnty_val_month[$month_id],2,'.',','); ?></b></td>
                    <?	
							$total_sales_qty+=$tot_sales_qty_month[$month_id];	
							$total_tot_sales_qnty_val+=$tot_sales_qnty_val_month[$month_id];
					
                        }
                    ?>
                    <td align="right"><b><?  ?></b></td>
                    <td align="right"><b><? ?></b></td>
                    <td align="right"><b><? echo number_format($total_tot_sales_qnty_val,2,'.',','); ?></b></td>
                </tr>
        	</tfoot>    
		</table>
	</div>
                 
      	</fieldset> 
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
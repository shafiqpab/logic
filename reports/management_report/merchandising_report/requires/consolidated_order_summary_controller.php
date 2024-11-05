<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form for Consolidated Order Summary Report.
Functionality	:	
JS Functions	:
Created by		:	Saidul Islam Reza
Creation date 	: 	31-03-2015
Updated by 		:  		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
extract($_REQUEST);
//------------------------------------------------------------------------------------------------------------
if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 145, "select buy.buyer_name, buy.id from lib_buyer buy where status_active =1 and is_deleted=0 order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );   	 
	exit();
}



if($action=="report_generate")
{ 
	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_product_department=str_replace("'","",$cbo_product_department)==0?'%%':str_replace("'","",$cbo_product_department);
	
	
	
	
	$buyer_id_cond="";
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
				$buyer_id_cond_2=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else
			{
				$buyer_id_cond="";
				$buyer_id_cond_2="";
			}
		}
		else
		{
			$buyer_id_cond="";
			$buyer_id_cond_2="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_id=$cbo_buyer_name";
		$buyer_id_cond_2=" and a.buyer_name=$cbo_buyer_name";
	}

	$comp_id_cond="";
	$comp_id_cond_2="";
	if(str_replace("'","",$cbo_company_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["company_id"]!="")
			{
				$comp_id_cond=" and id in (".$_SESSION['logic_erp']["company_id"].")";
				$comp_id_cond_2=" and a.company_name in (".$_SESSION['logic_erp']["company_id"].")";
			}
			else
			{
				$comp_id_cond="";
				$comp_id_cond_2="";
			}
		}
		else
		{
			$comp_id_cond="";
			$comp_id_cond_2="";
		}
	}
	else
	{
		$comp_id_cond=" and id=$cbo_company_name";
		$comp_id_cond_2=" and a.company_name=$cbo_company_name";
	}

	$company_library=return_library_array( "select id, company_name from lib_company where status_active =1 and is_deleted=0 $comp_id_cond order by id ASC", "id", "company_name");
	if($company_name!=0)$company_library=array($company_name=>$company_library[$company_name]); else $company_name=''; 
	
	if($agent_name==0) $agent_cond=""; else $agent_cond=" and a.agent=$agent_name";
	if(str_replace("'","",$cbo_team_leader)==0) $team_leader_cond=""; else $team_leader_cond=" and a.team_leader=$cbo_team_leader";
	
	
	
	//echo $buyer_id_cond;die;
	//if($year_from!=0 && $month_from!=0)
	//{
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_date_type=str_replace("'","",$cbo_date_type);	
	$sql_clm_nam=" b.pub_shipment_date ";
	if($cbo_date_type==2)	
	{
		if($db_type==0) 
		{
			$date_cond=" and b.sales_target_date between '$txt_date_from' and '$txt_date_to'";
			$date_cond2=" and b.pub_shipment_date between '$txt_date_from' and '$txt_date_to'";
		}
		if($db_type==2) 
		{
			 $date_cond=" and b.sales_target_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
			 $date_cond2=" and b.pub_shipment_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
		}
		$sql_clm_nam=" b.pub_shipment_date ";
	}
	else if($cbo_date_type==3)
	{
		if($db_type==0) 
		{
			$date_cond=" and b.sales_target_date between '$txt_date_from' and '$txt_date_to'";
			$date_cond2=" and b.shipment_date between '$txt_date_from' and '$txt_date_to'";
		}
		if($db_type==2) 
		{
			 $date_cond=" and b.sales_target_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
			 $date_cond2=" and b.shipment_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
		}
		$sql_clm_nam=" b.shipment_date ";
	}
	else if($cbo_date_type==1)
	{
		if($db_type==0) 
		{
			$date_cond=" and b.sales_target_date between '$txt_date_from' and '$txt_date_to'";
			$date_cond2=" and c.country_ship_date between '$txt_date_from' and '$txt_date_to'";
		}
		if($db_type==2) 
		{
			 $date_cond=" and b.sales_target_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
			 $date_cond2=" and c.country_ship_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
		}
	}
	else
	{
		if($db_type==0) 
		{
			$date_cond=" and b.sales_target_date between '$txt_date_from' and '$txt_date_to'";
			//$date_cond2=" and c.country_ship_date between '$txt_date_from' and '$txt_date_to'";
			$date_cond2=" and b.pub_shipment_date between '$txt_date_from' and '$txt_date_to'";
		}
		if($db_type==2) 
		{
			//echo "sdsdsd";die;
			 $date_cond=" and b.sales_target_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
			 //$date_cond2=" and c.country_ship_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
			 $date_cond2=" and b.pub_shipment_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
		}
		
	}
	
		
	
		
		
		
	//}
	
ob_start();
	
		/*$sql="select a.company_name, sum(c.order_quantity) as po_quantity,c.country_ship_date as shipment_date, sum(c.order_total) as amount,b.is_confirmed from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where b.id=c.po_break_down_id and a.job_no=c.job_no_mst and a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.is_deleted=0  and a.company_name like('%$company_name%') $buyer_id_cond_2 $product_department_con $date_cond2 $team_leader_cond GROUP BY b.is_confirmed,a.company_name,c.country_ship_date";*/
		

	if($cbo_date_type==1)
	{
		$sql="select a.company_name, sum(c.order_quantity) as po_quantity,c.country_ship_date as shipment_date, sum(c.order_total) as amount,b.is_confirmed,c.order_rate from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where  a.job_no=b.job_no_mst and b.id=c.po_break_down_id and b.job_no_mst=c.job_no_mst and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0  $comp_id_cond_2 $buyer_id_cond_2 $product_department_con $date_cond2 $team_leader_cond GROUP BY b.is_confirmed,a.company_name,c.country_ship_date,c.order_rate";
	}
	else
	{
		$sql="select a.company_name, sum(b.po_quantity*a.total_set_qnty) as po_quantity,$sql_clm_nam as shipment_date, sum(b.po_total_price) as amount,b.is_confirmed from wo_po_details_master a, wo_po_break_down b where  a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and b.is_deleted=0  $comp_id_cond_2 $buyer_id_cond_2 $product_department_con $date_cond2 $team_leader_cond GROUP BY b.is_confirmed,a.company_name,$sql_clm_nam";
	}
	//  echo $sql;
		
	$sql_order= sql_select($sql);
	
	foreach ($sql_order as $row)
	{ 
		if($row[csf("is_confirmed")]==1){
			
			$order_data_arr[date("Y-m",strtotime($row[csf("shipment_date")]))][$row[csf("company_name")]]['confirmqty']+=$row[csf("po_quantity")];
			$order_data_arr[date("Y-m",strtotime($row[csf("shipment_date")]))][$row[csf("company_name")]]['confirmamount']+=$row[csf("amount")];
			
			
		}
		else if($row[csf("is_confirmed")]==2)
		{
		$order_data_arr[date("Y-m",strtotime($row[csf("shipment_date")]))][$row[csf("company_name")]]['projectqty']+=$row[csf("po_quantity")];
		$order_data_arr[date("Y-m",strtotime($row[csf("shipment_date")]))][$row[csf("company_name")]]['projectamount']+=$row[csf("amount")];
		}
		
	}
	//print_r($order_data_arr);
//var_dump($order_data_arr);	
		
		 $sql_sales=sql_select("select a.company_id,a.team_leader, b.sales_target_date ,a.agent,b.sales_target_qty as sales_target_qty,b.sales_target_value from wo_sales_target_mst  a,wo_sales_target_dtls b where a.id=b.sales_target_mst_id and a.status_active=1 and a.is_deleted=0 and a.company_id like('%$company_name%') $buyer_id_cond $team_leader_cond  $date_cond order by b.sales_target_date,a.company_id");
	$sale_data_arr=array();$buyer_tem_arr=array();$agent_tem_arr=array();
		foreach($sql_sales as $row)
		{
		$sale_data_arr[date("Y-m",strtotime($row[csf("sales_target_date")]))][$row[csf("company_id")]]['target_qty']+=$row[csf("sales_target_qty")];
		//$sale_data_arr[date("Y-m",strtotime($row[csf("sales_target_date")]))][$row[csf("company_id")]]['target_qty']+=$row[csf("sales_target_qty")];
		$sale_data_arr[date("Y-m",strtotime($row[csf("sales_target_date")]))][$row[csf("company_id")]]['target_val']+=$row[csf("sales_target_value")];
		$sale_data_company_arr[$row[csf("company_id")]]=$row[csf("company_id")];
		}
		$total_company=count($company_library);
		$width=($total_company*230)+550;
		$colspan=$total_company+4;

//var_dump($sale_data_arr);
//var_dump($company_library);

$tot_month = datediff( 'm', $txt_date_from,$txt_date_to);
for($i=0; $i<= $tot_month; $i++ )
{
$next_month=month_add($txt_date_from,$i);
$month_arr[]=date("Y-m",strtotime($next_month));
}
?>

 <fieldset style="width:<? echo $width; ?>px; margin-top:10px;">
        	<table cellpadding="0" cellspacing="0" width="<? echo $width; ?>">
                 <tr>
                   <td align="center" width="100%" colspan="<? echo $colspan; ?>" class="form_caption"><strong>Consolidated Order Summary</strong></td>
                </tr>
            </table>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width; ?>" class="rpt_table" >
                <thead>
                	<tr>
                        <th width="40" rowspan="2" align="center">SL</th>
                        <th width="80" rowspan="2">Month</th>
                        <th width="80" rowspan="2">Perticulars</th>
						<?
                        foreach($company_library as $company_id=>$val)
                        {
                        ?>
                        <th colspan="3"><p><? echo $company_library[$company_id]; ?></p></th>
                        <?	
                        }
                        ?>
                    	<th colspan="3">Total</th>
                    </tr>
                    <tr>
                    	<?
						for($z=1;$z<=$total_company;$z++)
						{
							?>
							<th width="70">Quantity</th>
							<th width="60">Avg. Rate</th>
							<th width="100">Value</th>
							<?	
						}
						?>
						<th width="70">Quantity</th>
						<th width="60">Avg. Rate</th>
						<th  width="">Value</th>
                    </tr>
                </thead>
            </table>
			<div style="width:<? echo $width; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width-20; ?>" class="rpt_table" id="table_body" >
					<? 
					   $i=1;
					   foreach($month_arr as $month_id)
                         //foreach($sale_data_arr as $month_id=>$company_data_arr)
                        {
                           $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF"; 
                       	?>
<!-- //Forecast.......................................................--> 
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
                                <td width="40" rowspan="3" valign="middle" align="center"><? echo $i; ?></td>
                                <td width="80" rowspan="3" valign="middle">
								<? list($y,$m)=explode('-',$month_id); $m=$m*1; echo $months[$m].', '.$y; ?>
                                </td>
                                <td width="80"><p>Forecast</p></td>
                                <? 
									$tot_sales_qty=$tot_sales_val=$tot_sales_avg=0; $z=1;
								 	foreach($company_library as $company_id=>$val)
									//foreach($company_data_arr as $company_id=>$val)
									{   
										$bgc=($z%2==0)?"#C5D9F1":"#FDE9D9";
										$sales_qty=$sale_data_arr[$month_id][$company_id]['target_qty'];
										$sales_val=$sale_data_arr[$month_id][$company_id]['target_val'];
										$sales_avg=$sales_val/$sales_qty;
										if(is_infinite($sales_avg) || is_nan($sales_avg)){$sales_avg=0;}
										//remove zeor;
										$sales_val=($sales_val==0)?'':$sales_val;
										$sales_avg=($sales_avg==0)?'':$sales_avg;
									
									?>
                                        <td width="70" bgcolor="<? echo $bgc; ?>" align="right"><? echo number_format($sales_qty); ?></td>
                                        <td width="60" bgcolor="<? echo $bgc; ?>"  align="right"><? echo number_format($sales_avg,2); ?></td>
                                        <td width="100" bgcolor="<? echo $bgc; ?>"  align="right"><? echo number_format($sales_val,2); ?></td>
                                    <?
										$z++;
										$tot_sales_qty+=$sales_qty;	
										$tot_sales_val+=$sales_val;	
										
										$tot_sales_qty_company[$month_id][$company_id]+=$sales_qty;	
										$tot_sales_val_company[$month_id][$company_id]+=$sales_val;	
										
										$grand_forecast_qty_company[$company_id]+=$sales_qty;	
										$grand_forecast_val_company[$company_id]+=$sales_val;	
									}
								?>
                                <td width="70" bgcolor="#DDD9C3" align="right"><? echo number_format($tot_sales_qty); ?></td>
                                <td width="60" bgcolor="#DDD9C3" align="right"><? 
								$cv=$tot_sales_val/$tot_sales_qty;
								if(is_infinite($cv) || is_nan($cv)){$cv=0;}
								echo number_format($cv,2); 
								
								?></td>
                           		<td bgcolor="#DDD9C3" align="right"><? echo number_format($tot_sales_val,2); ?></td>
                            </tr>

<!-- //Projection.......................................................--> 
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
                               <td><p>Projection</p></td>
                                <? 
									$tot_proj_qty=$tot_proj_val=$tot_proj_avg=0; $z=1;
								 	foreach($company_library as $company_id=>$val)
									//foreach($company_data_arr as $company_id=>$val)
									{
										$bgc=($z%2==0)?"#C5D9F1":"#FDE9D9";
										$proj_qty=$order_data_arr[$month_id][$company_id]['projectqty'];
										$proj_val=$order_data_arr[$month_id][$company_id]['projectamount'];
										$proj_avg=$proj_val/$proj_qty;
										if(is_infinite($proj_avg) || is_nan($proj_avg)){$proj_avg=0;}
									
									
										//remove zeor;
										$proj_val=($proj_val==0)?'':$proj_val;
										$proj_avg=($proj_avg==0)?'':$proj_avg;
									
									?>
                                        <td width="70" bgcolor="<? echo $bgc; ?>" align="right"><? echo number_format($proj_qty); ?></td>
                                        <td width="60" bgcolor="<? echo $bgc; ?>"  align="right"><? echo number_format($proj_avg,2); ?></td>
                                        <td width="100" bgcolor="<? echo $bgc; ?>"  align="right"><? echo number_format($proj_val,2); ?></td>
                                    <?
										$z++;
										$tot_proj_qty+=$proj_qty;	
										$tot_proj_val+=$proj_val;	
										
										$tot_proj_qty_company[$month_id][$company_id]+=$proj_qty;	
										$tot_proj_val_company[$month_id][$company_id]+=$proj_val;	
									}
								?>
                                <td bgcolor="#DDD9C3" align="right"><? echo number_format($tot_proj_qty); ?></td>
                                <td bgcolor="#DDD9C3" align="right"><? 
								$cv=$tot_proj_val/$tot_proj_qty;
								if(is_infinite($cv) || is_nan($cv)){$cv=0;}
								echo number_format($cv,2);
								
								 ?></td>
                                <td bgcolor="#DDD9C3" align="right"><? echo number_format($tot_proj_val,2); ?></td>
                            </tr>

<!-- //Confirm.......................................................--> 
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
                               <td><p>Confirm</p></td>
                                <? 
									$tot_conf_qty=$tot_conf_val=$tot_conf_avg=0; $z=1;
								 	foreach($company_library as $company_id=>$val)
									//foreach($company_data_arr as $company_id=>$val)
									{
										$bgc=($z%2==0)?"#C5D9F1":"#FDE9D9";
										$conf_qty=$order_data_arr[$month_id][$company_id]['confirmqty'];
										$conf_val=$order_data_arr[$month_id][$company_id]['confirmamount'];
										$conf_avg=$conf_val/$conf_qty;
										if(is_infinite($conf_avg) || is_nan($conf_avg)){$conf_avg=0;}
									
										//remove zeor;
										$conf_val=($conf_val==0)?'':$conf_val;
										$conf_avg=($conf_avg==0)?'':$conf_avg;
									
									
									?>
                                        <td width="70" bgcolor="<? echo $bgc; ?>" align="right"><? echo number_format($conf_qty); ?></td>
                                        <td width="60" bgcolor="<? echo $bgc; ?>"  align="right"><? echo number_format($conf_avg,2); ?></td>
                                        <td width="100" bgcolor="<? echo $bgc; ?>"  align="right"><? echo number_format($conf_val,2); ?></td>
                                    <?
										$z++;
										$tot_conf_qty+=$conf_qty;	
										$tot_conf_val+=$conf_val;	
										
										$tot_conf_qty_company[$month_id][$company_id]+=$conf_qty;	
										$tot_conf_val_company[$month_id][$company_id]+=$conf_val;	
									}
								?>
                                <td bgcolor="#DDD9C3" align="right"><? echo number_format($tot_conf_qty); ?></td>
                                <td bgcolor="#DDD9C3" align="right"><? 
								$cv=$tot_conf_val/$tot_conf_qty;
								if(is_infinite($cv) || is_nan($cv)){$cv=0;}
								echo number_format($cv,2);
								
								 ?></td>
                                <td bgcolor="#DDD9C3" align="right"><? echo number_format($tot_conf_val,2); ?></td>
                            </tr>


                    	<tr bgcolor="#FFCC99">
                            <td colspan="3" align="right">Order Total (Proj + Con)</td>
							<?
							$tot_order_qty=$tot_order_val=0;
							foreach($company_library as $company_id=>$val)
							//foreach($company_data_arr as $company_id=>$val)
                            {
                            $order_qty=($tot_proj_qty_company[$month_id][$company_id]+$tot_conf_qty_company[$month_id][$company_id]);
							$order_val=($tot_proj_val_company[$month_id][$company_id]+$tot_conf_val_company[$month_id][$company_id]);
							$order_avg=$order_val/$order_qty;
							if(is_infinite($order_avg) || is_nan($order_avg)){$order_avg=0;}
							
							//remove zeor;
							$order_qty=($order_qty==0)?'':$order_qty;
							$order_val=($order_val==0)?'':$order_val;
							$order_avg=($order_avg==0)?'':$order_avg;
							
							?>
                            <td align="right"><? echo number_format($order_qty); ?></td>
                            <td align="right"><? echo number_format($order_avg,2); ?></td>
                            <td align="right"><? echo number_format($order_val,2); ?></td>
                            <?	
								$tot_order_qty+=$order_qty;
								$tot_order_val+=$order_val;
							
								$grand_order_qty_company[$company_id]+=$order_qty;	
								$grand_order_val_company[$company_id]+=$order_val;	
							}
                            ?>
                            <td align="right"><? echo number_format($tot_order_qty); ?></td>
                            <td align="right"><? 
							$cv=$tot_order_val/$tot_order_qty;
							if(is_infinite($cv) || is_nan($cv)){$cv=0;}
							echo number_format($cv,2); 
							?></td>
                            <td align="right"><? echo number_format($tot_order_val,2); ?></td>
                        </tr>
                        
                    	<tr bgcolor="#F29536">
                            <td colspan="3" align="right">Varience(Forecast - Ord.Total)</td>
							<?
							$tot_varience_qty=$tot_varience_val=0;
							foreach($company_library as $company_id=>$val)
							//foreach($company_data_arr as $company_id=>$val)
                            {
                            $varience_qty=$tot_sales_qty_company[$month_id][$company_id]-($tot_proj_qty_company[$month_id][$company_id]+$tot_conf_qty_company[$month_id][$company_id]);
							$varience_val=$tot_sales_val_company[$month_id][$company_id]-($tot_proj_val_company[$month_id][$company_id]+$tot_conf_val_company[$month_id][$company_id]);
							$varience_avg=$varience_val/$varience_qty;
							if(is_infinite($varience_avg) || is_nan($varience_avg)){$varience_avg=0;}
							
							//remove zeor;
							$varience_qty=($varience_qty==0)?'':$varience_qty;
							$varience_val=($varience_val==0)?'':$varience_val;
							$varience_avg=($varience_avg==0)?'':$varience_avg;
							
							?>
                            <td align="right"><? echo number_format($varience_qty); ?></td>
                            <td align="right"><? echo number_format($varience_avg,2); ?></td>
                            <td align="right"><? echo number_format($varience_val,2); ?></td>
                            <?	
								$tot_varience_qty+=$varience_qty;
								$tot_varience_val+=$varience_val;
								
								$grand_varience_qty_company[$company_id]+=$varience_qty;	
								$grand_varience_val_company[$company_id]+=$varience_val;	
							}
                            ?>
                            <td align="right"><? echo number_format($tot_varience_qty); ?></td>
                            <td align="right"><?
								$cv=$tot_varience_val/$tot_varience_qty;
								if(is_infinite($cv) || is_nan($cv)){$cv=0;}
								echo number_format($cv,2); 
							 //echo number_format($tot_varience_val/$tot_varience_qty,2); 
							 ?></td>
                            <td align="right"><? echo number_format($tot_varience_val,2); ?></td>
                        </tr>
                        
                   <? 
				   	$i++;
				   	$grand_order_qty+=$tot_order_qty;
					$grand_order_val+=$tot_order_val;
					
					$grand_varience_qty+=$tot_varience_qty;
				   	$grand_varience_val+=$tot_varience_val;
					
					}
					?>   
                    <tfoot>
                    	<tr>
                            <th colspan="3">Forecast Total</th>
							<?
							$grand_forecast_qty=$grand_forecast_val=0;
							foreach($company_library as $company_id=>$val)
                            {
								//$grand_forecast_qty_company[$company_id]+=$forecast_qty;	
								//$grand_forecast_val_company[$company_id]
								//$tot_sales_company_wiseQty[$company_id]+=$sales_qty;	
									//tot_sales_company_wiseVal[$company_id]+=$sales_val;
								?>
								<th align="right"><? if($grand_forecast_qty_company[$company_id])echo number_format($grand_forecast_qty_company[$company_id]); ?></th>
								<th  align="right"><? if($grand_forecast_val_company[$company_id])echo fn_number_format($grand_forecast_val_company[$company_id]/$grand_forecast_qty_company[$company_id],2); ?></th>
								<th  align="right"><? if($grand_forecast_val_company[$company_id])echo number_format($grand_forecast_val_company[$company_id],2); ?></th>
								<?	
								$grand_forecast_qty+=$grand_forecast_qty_company[$company_id];
								$grand_forecast_val+=$grand_forecast_val_company[$company_id];
							}
                            ?>
                            <th  align="right"><? echo number_format($grand_forecast_qty); ?></th>
                            <th  align="right"><? 
							$gfcv=$grand_forecast_val/$grand_forecast_qty;
							if(is_infinite($gfcv) || is_nan($gfcv)){$gfcv=0;}
							echo number_format($gfcv,2); 
							
							?></th>
                            <th  align="right"><? echo number_format($grand_forecast_val,2); ?></th>
                        </tr>
                        <tr>
                            <th colspan="3">Order Total (Proj + Con)</th>
							<?
							foreach($company_library as $company_id=>$val)
							//foreach($company_data_arr as $company_id=>$val)
                            {
								?>
								<th  align="right"><? if($grand_order_qty_company[$company_id])echo number_format($grand_order_qty_company[$company_id]); ?></th>
								<th align="right"><? if($grand_order_val_company[$company_id])echo number_format($grand_order_val_company[$company_id]/$grand_order_qty_company[$company_id],2); ?></th>
								<th align="right"><? if($grand_order_val_company[$company_id])echo number_format($grand_order_val_company[$company_id],2); ?></th>
								<?	
						    }
                            ?>
                            <th  align="right"><? echo number_format($grand_order_qty); ?></th>
                            <th  align="right"><? 
								$cv=$grand_order_val/$grand_order_qty;
								if(is_infinite($cv) || is_nan($cv)){$cv=0;}
								echo number_format($cv,2); 
							?></th>
                            <th align="right" width=""><? echo number_format($grand_order_val,2); ?></th>
                       
                       
                        </tr>
                    	<tr>
                            <th colspan="3">Varience(Forecast - Ord.Total)</th>
							<?
							foreach($company_library as $company_id=>$val)
                            {
								?>
								<th align="right"><? if($grand_varience_qty_company[$company_id])echo number_format($grand_varience_qty_company[$company_id]); ?></th>
								<th  align="right"><? if($grand_varience_val_company[$company_id])echo number_format($grand_varience_val_company[$company_id]/$grand_varience_qty_company[$company_id],2); ?></th>
								<th  align="right"><? if($grand_varience_val_company[$company_id])echo number_format($grand_varience_val_company[$company_id],2); ?></th>
								<?	
							}
                            ?>
                            <th  align="right"><? echo number_format($grand_varience_qty); ?></th>
                            <th  align="right"><? 
							$cv=$grand_varience_val/$grand_varience_qty;
							if(is_infinite($cv) || is_nan($cv)){$cv=0;}
							echo number_format($cv,2); 
							
							?></th>
                            <th  align="right"><? echo number_format($grand_varience_val,2); ?></th>
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



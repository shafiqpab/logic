<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.yarns.php');
require_once('../../../../includes/class4/class.conversions.php');
require_once('../../../../includes/class4/class.emblishments.php');
require_once('../../../../includes/class4/class.commisions.php');
require_once('../../../../includes/class4/class.commercials.php');
require_once('../../../../includes/class4/class.others.php');
require_once('../../../../includes/class4/class.trims.php');
require_once('../../../../includes/class4/class.fabrics.php');
require_once('../../../../includes/class4/class.washes.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//------------------------------------------------------------------------------------------------------------
if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 145, "select buy.buyer_name, buy.id from lib_buyer buy where status_active =1 and is_deleted=0 order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );   	 
	exit();
}
$company_library=return_library_array( "select id, company_name from lib_company where status_active =1 and is_deleted=0 order by id ASC", "id", "company_name");
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_name=str_replace("'","",$cbo_company_name);
	if($company_name!=0)$company_library=array($company_name=>$company_library[$company_name]); else $company_name=''; 
	
	
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
	
	if($agent_name==0) $agent_cond=""; else $agent_cond=" and a.agent=$agent_name";
	if(str_replace("'","",$cbo_team_leader)==0) $team_leader_cond=""; else $team_leader_cond=" and a.team_leader=$cbo_team_leader";
	
	
	
	//echo $buyer_id_cond;die;
	//if($year_from!=0 && $month_from!=0)
	//{
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_date_type=str_replace("'","",$cbo_date_type);	
	if($cbo_date_type==3)	
	{
		if($db_type==0) 
		{
			$date_cond=" and b.sales_target_date between '$txt_date_from' and '$txt_date_to'";
			$date_cond2=" and b.pub_shipment_date between '$txt_date_from' and '$txt_date_to'";
			$date_cond3=" and b.pub_shipment_date between '$txt_date_from' and '$txt_date_to'";
		}
		if($db_type==2) 
		{
			 $date_cond=" and b.sales_target_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
			 $date_cond2=" and b.pub_shipment_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
		}
	}
	else if($cbo_date_type==2)	
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

	$condition= new condition();
	if(!empty($company_name))
	{

		$condition->company_name("in ($company_name)");
	}

	if(str_replace("'","",$cbo_buyer_name)>0){
		$condition->buyer_name("=$cbo_buyer_name");
	}
	// str_replace("'","",$cbo_search_date) ==1 && 
	if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='') {
	  //$condition->country_ship_date(" between '$start_date' and '$end_date'");
	   $condition->pub_shipment_date(" between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'");
 	}
 	//$condition->job_no("='FAL-20-00781'");
	$condition->init();
	$other= new other($condition);
	$other_costing_arr=$other->getAmountArray_by_order();

	//echo $other->getQuery();die;

	//echo '<pre>';
   // print_r($other_costing_arr);die;
    
		
		
		
	//}
	
ob_start();
	
		$company_con="";
		if(!empty($company_name)){$company_con="and a.company_name='$company_name'";}
		
	//$date_cond2=" AND b.pub_shipment_date BETWEEN '1-Oct-2020' AND '31-Oct-2020'";
	if($cbo_date_type==1)
	{
		$sql="select a.company_name, a.buyer_name, sum(c.order_quantity) as po_quantity,c.country_ship_date as shipment_date, sum(c.order_total) as amount,b.is_confirmed, b.po_total_price, b.id as po_id, c.order_rate from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where  a.job_no=b.job_no_mst and b.id=c.po_break_down_id and b.job_no_mst=c.job_no_mst and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0  $company_con $buyer_id_cond_2 $product_department_con $date_cond2 GROUP BY b.id, a.buyer_name,b.is_confirmed,b.po_total_price,a.company_name,c.country_ship_date,c.order_rate";
	}
	else
	{
		
		$sql="select a.company_name, a.buyer_name, sum(b.po_quantity*a.total_set_qnty) as po_quantity,b.pub_shipment_date as shipment_date, sum((b.unit_price/a.total_set_qnty)*(b.po_quantity*a.total_set_qnty)) as amount,b.po_total_price, b.id as po_id, b.is_confirmed from wo_po_details_master a, wo_po_break_down b where  a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and b.is_deleted=0  $company_con $buyer_id_cond_2 $product_department_con $date_cond2 GROUP BY b.id, a.buyer_name,b.is_confirmed,b.po_total_price,a.company_name,b.pub_shipment_date";
	}
	 //echo $sql;
		
	$sql_order= sql_select($sql);
	
	foreach ($sql_order as $row)
	{ 

		//$summ_cm_cost+=$other_costing_arr[$row[csf('po_id')]]['cm_cost'];
		//$summ_cm_cost+=$other_costing_arr[$pi_id]['cm_cost'];

		if($row[csf("is_confirmed")]==1){
			
			$order_data_arr[date("Y-m",strtotime($row[csf("shipment_date")]))][$row[csf("company_name")]]['confirmqty']+=$row[csf("po_quantity")];
			$order_data_arr[date("Y-m",strtotime($row[csf("shipment_date")]))][$row[csf("company_name")]]['confirmamount']+=$row[csf("amount")];
			
			$buyer_wise_data[$row[csf('buyer_name')]]['order_qty_pcs']+=$row[csf('po_quantity')];
			$buyer_wise_data[$row[csf('buyer_name')]]['po_total_price']+=$row[csf('amount')];
			
			
		}
		else if($row[csf("is_confirmed")]==2)
		{
		

		$order_data_arr[date("Y-m",strtotime($row[csf("shipment_date")]))][$row[csf("company_name")]]['projectqty']+=$row[csf("po_quantity")];
		$order_data_arr[date("Y-m",strtotime($row[csf("shipment_date")]))][$row[csf("company_name")]]['projectamount']+=$row[csf("amount")];
		}
		
	}

	//echo $summ_cm_cost; die;

	//print_r($order_data_arr);
//var_dump($order_data_arr);	
		
		 $sql_sales=sql_select("select a.company_id,a.team_leader, b.sales_target_date ,a.agent,b.sales_target_qty as sales_target_qty,b.sales_target_value from wo_sales_target_mst  a,wo_sales_target_dtls b where a.id=b.sales_target_mst_id and a.status_active=1 and a.is_deleted=0 and a.company_id like('%$company_name%') $buyer_id_cond $date_cond order by b.sales_target_date,a.company_id");
	$sale_data_arr=array();$buyer_tem_arr=array();$agent_tem_arr=array();
		foreach($sql_sales as $row)
		{
		$sale_data_arr[date("Y-m",strtotime($row[csf("sales_target_date")]))][$row[csf("company_id")]]['target_qty']+=$row[csf("sales_target_qty")];
		//$sale_data_arr[date("Y-m",strtotime($row[csf("sales_target_date")]))][$row[csf("company_id")]]['target_qty']+=$row[csf("sales_target_qty")];
		$sale_data_arr[date("Y-m",strtotime($row[csf("sales_target_date")]))][$row[csf("company_id")]]['target_val']+=$row[csf("sales_target_value")];
		
		$sale_data_company_arr[$row[csf("company_id")]]=$row[csf("company_id")];
		}

		$company_con="";
		if(!empty($company_name)){$company_con="and a.company_name='$company_name'";}
		
		$sql_budget="select a.company_name, b.insert_date, a.buyer_name, b.is_confirmed, a.total_set_qnty as ratio, b.id as po_id, b.po_number, b.pub_shipment_date, b.po_received_date,
			b.po_quantity, b.po_total_price
			from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c where a.job_no=b.job_no_mst and a.job_no=c.job_no and c.entry_from=158 and a.status_active=1 and
			a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_con $buyer_id_cond_2  and b.pub_shipment_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";

		    //echo $sql_budget; die;
			
			$result_sql_budget=sql_select($sql_budget);
			$total_cm_cost=array();
			foreach($result_sql_budget as $row )
			{
				//$buyer_wise_data[$row[csf('buyer_name')]]['order_qty_pcs']+=$row[csf('po_quantity')]*$row[csf('ratio')];
				//$buyer_wise_data[$row[csf('buyer_name')]]['po_quantity']+=$row[csf('po_quantity')];
				$buyer_wise_data[$row[csf('buyer_name')]]['cm_cost']+=$other_costing_arr[$row[csf('po_id')]]['cm_cost'];
				//$buyer_wise_data[$row[csf('buyer_name')]]['po_total_price']+=$row[csf('po_total_price')];

				$total_cm_cost[$row[csf('company_name')]]+=$other_costing_arr[$row[csf('po_id')]]['cm_cost'];
			}
			
		
		$total_company=count($company_library);
		$width=($total_company*150)+550;
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
                   <td align="center" width="100%" colspan="<? echo $colspan; ?>" class="form_caption"><strong>Factory Wise Expected Export Quantity and Value and CM</strong></td>
                </tr>
            </table>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width; ?>" class="rpt_table" >
                <thead>
                	<tr>
                        <th width="40" align="center">SL</th>
                        <th width="80">Month</th>
                        <th width="220">Perticulars</th>
						<?
                        foreach($company_library as $company_id=>$val)
                        {
                        ?>
                        <th width="150"><p><? echo $company_library[$company_id]; ?></p></th>
                        <?	
                        }
                        ?>
                    	<th>Total</th>
                    </tr>
                    
                </thead>
            </table>
			<div style="width:<? echo $width; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width-20; ?>" class="rpt_table" id="table_body" >
					<? 
					   $i=1;
					   foreach($month_arr as $month_id)
                        {
                           $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF"; 
                       		?>
<!-- //Confirm qty -->
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
                            	 <td width="40" rowspan="6" valign="middle" align="center"><? echo $i; ?></td>
                                <td width="80" rowspan="6" valign="middle">
								<? list($y,$m)=explode('-',$month_id); $m=$m*1; echo $months[$m].', '.$y; ?>
                                </td>
                               <td width="220"><p>Total Expected Export Quantity</p></td>
                                <? 
									$tot_conf_qty=$tot_conf_val=$tot_conf_avg=0; $z=1;
								 	foreach($company_library as $company_id=>$val)
									{
										$bgc=($z%2==0)?"#C5D9F1":"#FDE9D9";
										$conf_qty=$order_data_arr[$month_id][$company_id]['confirmqty'];
									?>
                                        <td width="150" bgcolor="<? echo $bgc; ?>" align="right"><? echo number_format($conf_qty); ?></td>
                                    <?
										$z++;
										$tot_conf_qty+=$conf_qty;	
										$tot_conf_qty_company[$month_id][$company_id]+=$conf_qty;	
									}
								?>
                                <td bgcolor="#DDD9C3" align="right"><? echo number_format($tot_conf_qty); ?></td>
                            </tr>

<!-- //Confirm rate.......................................................--> 
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
                               <td width="220"><p>Total Expected Export Average Rate(Per Pcs)</p></td>
                                <? 
									$tot_conf_qty=$tot_conf_val=$tot_conf_avg=0; $z=1;
								 	foreach($company_library as $company_id=>$val)
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
                                        <td width="150" bgcolor="<? echo $bgc; ?>"  align="right"><? echo number_format($conf_avg,2); ?></td>
                                    <?
										$z++;
										$tot_conf_qty+=$conf_qty;	
										$tot_conf_val+=$conf_val;
										$tot_conf_qty_company[$month_id][$company_id]+=$conf_qty;	
										$tot_conf_val_company[$month_id][$company_id]+=$conf_val;	
									}
								?>
                                <td bgcolor="#DDD9C3" align="right"><? 
								$cv=$tot_conf_val/$tot_conf_qty;
								if(is_infinite($cv) || is_nan($cv)){$cv=0;}
								echo number_format($cv,2); ?></td>
                                
                            </tr>



<!-- //Confirm value.......................................................--> 
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
                               <td width="220"><p>Total Expected Export  Value</p></td>
                                <? 
									$tot_conf_qty=$tot_conf_val=$tot_conf_avg=0; $z=1;
								 	foreach($company_library as $company_id=>$val)
									{
										$bgc=($z%2==0)?"#C5D9F1":"#FDE9D9";
										$conf_val=$order_data_arr[$month_id][$company_id]['confirmamount'];
										//remove zeor;
										$conf_val=($conf_val==0)?'':$conf_val;
									?>
                                        <td width="150" bgcolor="<? echo $bgc; ?>"  align="right"><? echo number_format($conf_val,2); ?></td>
                                    <?
										$z++;
										$tot_conf_val+=$conf_val;		
										$tot_conf_val_company[$month_id][$company_id]+=$conf_val;	
									}
								?>
                                <td bgcolor="#DDD9C3" align="right"><? echo number_format($tot_conf_val,2); ?></td>
                            </tr>


 <!-- //CM.......................................................--> 
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
                               <td width="220"><p> CM </p></td>
                                <? 
									$tot_cm_val=0; $z=1;
								 	foreach($company_library as $company_id=>$val)
									{
										$bgc=($z%2==0)?"#C5D9F1":"#FDE9D9";
			

										$summ_cm_cost=$total_cm_cost[$company_id];
										
										
									?>
                                        <td width="150" bgcolor="<? echo $bgc; ?>"  align="right"><? echo number_format($total_cm_cost[$company_id],2); ?> </td>
                                    <?
										$z++;
										$tot_cm_val+=$summ_cm_cost;		
										$tot_cm_val_company[$month_id][$company_id]+=$summ_cm_cost;	
									}
								?>
                                
                                <td bgcolor="#DDD9C3" align="right"><? echo number_format($tot_cm_val,2); ?></td>
                            </tr>
<!-- //CM percentage %.......................................................-->
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
                               <td width="220"><p>Average CM %</p></td>
                                <? 
									$tot_cm_per=0; $z=1;
								 	foreach($company_library as $company_id=>$val)
									{
										$bgc=($z%2==0)?"#C5D9F1":"#FDE9D9";
										$conf_val=$order_data_arr[$month_id][$company_id]['confirmamount'];
										$summ_cm_cost=$total_cm_cost[$company_id];
										$cm_percentage=$summ_cm_cost/$conf_val;
										
										$cm_percentage=($cm_percentage==0)?'':$cm_percentage;
									?>
                                        <td width="150" bgcolor="<? echo $bgc; ?>"  align="right"><? echo number_format($cm_percentage,2); ?></td>
                                    <?
										$z++;
										$tot_conf_val+=$conf_val;		
										$tot_conf_val_company[$month_id][$company_id]+=$conf_val;
										$tot_cm_val+=$summ_cm_cost;		
										$tot_cm_val_company[$month_id][$company_id]+=$summ_cm_cost;	
									}
								?>
                                
                                <td bgcolor="#DDD9C3" align="right"><? $per=$tot_cm_val/$tot_conf_val;
								if(is_infinite($per) || is_nan($per)){$per=0;}
								echo number_format($per,2); ?></td>
                            </tr>
 <!-- //CM Average.......................................................-->
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
                               <td width="220"><p>Average CM</p></td>
                                <? 
									$tot_cm_avg=0; $z=1;
								 	foreach($company_library as $company_id=>$val)
									{
										$bgc=($z%2==0)?"#C5D9F1":"#FDE9D9";
										$conf_qty=$order_data_arr[$month_id][$company_id]['confirmqty'];
										$summ_cm_cost=$total_cm_cost[$company_id];

										$cm_average=$summ_cm_cost/$conf_qty;

										//remove zeor;
										$cm_average=($cm_average==0)?'':$cm_average;
									?>
                                        <td width="150" bgcolor="<? echo $bgc; ?>"  align="right"><? echo number_format($cm_average,2); ?></td>
                                    <?
										$z++;
										$tot_cm_val+=$summ_cm_cost;		
										$tot_cm_val_company[$month_id][$company_id]+=$summ_cm_cost;	
										$tot_conf_qty+=$conf_qty;	
										$tot_conf_qty_company[$month_id][$company_id]+=$conf_qty;	
									}
								?>
                                <td bgcolor="#DDD9C3" align="right"><? $avg=$tot_cm_val/$tot_conf_qty;
                                if(is_infinite($avg) || is_nan($avg)){$avg=0;}
								echo number_format($avg,2); ?></td>
                            </tr>

                   <? 
				   	
					}
					?>   
                    
				</table>

		 </fieldset>

		<br/> 

		
        	<fieldset style="width:850px;" >	
            <table width="800">
                    <tr class="form_caption">
                        <td colspan="7" align="center"><strong>Buyer Wise Expected Export Quantity and Value and CM</strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="7" align="center"><strong><? echo $company_library[$company_name]; ?></strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td align="left"><strong>Details Report </strong></td>
                    </tr>
            </table>
               <? $asking_profit_head=$asking_profit_arr[$company_name]['asking_profit']; 
			   
			   		if(str_replace("'","",$cbo_search_date)==1) $caption="Ship. Date";
					else if(str_replace("'","",$cbo_search_date)==2) $caption="PO Recv. Date";
					else $caption="PO Insert Date";
			   ?>
            <table  class="rpt_table" width="800" cellpadding="0" cellspacing="0" border="1" rules="all">
               <thead>
                    <tr>
                        <th width="40" >SL</th>
                        <th width="70">Buyer</th>
                        <th width="90" >Exp. Qty (PCS)</th>
                        <th width="100" >Value (USD)</th>
                        <th width="100" >CM</th>
                        <th width="100">Avg. FOB</th>
                        <th width="100" >Avg. CM</th>

                    </tr>
				</thead>
                
            </table>
            <div style="width: 800px; max-height:400px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table" style="margin: 0px; padding: 0px;" width="800" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
			<? 
			
            $i=1; $total_order_qty=0;
			

			foreach($buyer_wise_data as $buyer_name=>$row )
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
				
				$order_qty_pcs=$row['order_qty_pcs'];
					//$order_qty_pcs=$row['po_quantity'];
				?>
				 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                     <td width="40"><? echo $i; ?></td>
                     <td width="70"><p><? echo $buyer_library[$buyer_name]; ?></p></td>
                     <? 
					$total_order_qty+=$order_qty_pcs;
					?>
                     <td width="90" align="right"><p><? echo number_format($order_qty_pcs,2); ?></p></td>
                     <? 
					$order_amount=$row['po_total_price'];
				
					$total_order_amount+=$order_amount;
					?>
                     <td width="100" align="right"><p><? echo number_format($order_amount,2); ?></p></td>
                     <? 
					$cm_cost=$row['cm_cost'];
				
					$total_cm_amount+=$cm_cost;
					?>
                     
                     <td width="100" align="right" bgcolor="<? echo $color; ?>"><? echo number_format($cm_cost,2);?></td>
                     <?
                     $order_amount=$row['po_total_price'];
                     $order_qty_pcs=$row['order_qty_pcs'];
                     $avg_fob=$order_amount/$order_qty_pcs;
                     ?>
                     <td width="100" align="right" bgcolor="<? echo $color; ?>"><? echo number_format($avg_fob,2);?></td>
                      <?
                     $cm_cost=$row['cm_cost'];
                     $order_qty_pcs=$row['order_qty_pcs'];
                     $avg_cm=$cm_cost/$order_qty_pcs;
                     ?>
                     <td width="100" align="right" bgcolor="<? echo $color; ?>"><? echo number_format($avg_cm,2);?></td>
                   
                  </tr> 
                <?
				
				$i++;
			}
			
			?>
            </table>
            
            <table class="tbl_bottom" width="800" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tr>
                    <td width="40">&nbsp;</td>
                    
                    <td width="70">Total</td>
                    <td width="90" align="right" id="value_total_order_qnty"><? echo number_format($total_order_qty,2); ?></td>
                    
                    <td width="100" align="right" id="value_total_order_amount2"><? echo number_format($total_order_amount,2); ?></td>
                    
                    <td width="100" align="right" id="value_total_cm_amount"><? echo number_format($total_cm_amount,2); ?></td>
                    <td width="100" align="right" id="value_total_tot_cost"><? echo number_format($total_order_amount/$total_order_qty,2); ?></td>
                    <td width="100" align="right" id="value_total_fabric_profit"><? echo number_format($total_cm_amount/$total_order_qty,2);?></td>
                    
                </tr>
            </table>
            
            	 </fieldset>
			  </div>
                
			</div>
            
      	

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



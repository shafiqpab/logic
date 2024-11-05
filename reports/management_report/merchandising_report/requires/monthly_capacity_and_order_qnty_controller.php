<?
/*-------------------------------------------- Comments
Version                  :   V1
Purpose			         : 	This form will create  Monthly Capacity and order qty Report
Functionality	         :	
JS Functions	         :
Created by		         :	Monzu, Saidul Islam 
Creation date 	         :  25 April,2015
Requirment Client        : 
Requirment By            : 
Requirment type          : 
Requirment               : 
Affected page            : 
Affected Code            :                   
DB Script                : 
Updated by 		         : 		
Update date		         : 		   
QC Performed BY	         :		
QC Date			         :	
Comments		         : From this version oracle conversion is start
*/

include('../../../../includes/common.php');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');


$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');

if ($action=="load_drop_down_buyer")
{
echo create_drop_down( "cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );   	 
} 

if ($action=="load_drop_down_location")
{
	 echo create_drop_down( "cbo_location_id", 160, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "","","","","","",3 );		 
}


if($action=="report_generate")
{

	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_year_name=str_replace("'","",$cbo_year_name);
	$cbo_month=str_replace("'","",$cbo_month);
	$cbo_month_end=str_replace("'","",$cbo_month_end);
	$cbo_end_year_name=str_replace("'","",$cbo_end_year_name);

	//$year_library=return_library_array( "select id, year from  lib_capacity_calc_mst", "id", "year"  );
	//$basic_smv_arr=return_library_array( );
	
	$sql_data_smv=sql_select("select comapny_id,year, basic_smv from lib_capacity_calc_mst where year between $cbo_year_name and $cbo_end_year_name");
	foreach( $sql_data_smv as $row)
	{
		$basic_smv_arr[$row[csf("comapny_id")]][$row[csf("year")]]=$row[csf("basic_smv")];
	}


	$daysinmonth=cal_days_in_month(CAL_GREGORIAN, $cbo_month_end, $cbo_year_name);
	$s_date=$cbo_year_name."-".$cbo_month."-"."01";
	$e_date=$cbo_end_year_name."-".$cbo_month_end."-".$daysinmonth;
	if($db_type==2)
	{
		$s_date=change_date_format($s_date,'yyyy-mm-dd','-',1);
		$e_date=change_date_format($e_date,'yyyy-mm-dd','-',1);
	}
	

	$tot_month = datediff( 'm', $s_date,$e_date);
	for($i=0; $i<= $tot_month; $i++ )
	{
		$next_month=month_add($s_date,$i);
		$month_arr[]=date("Y-m",strtotime($next_month));
	}
	

	$sql_con_po="SELECT a.job_no,a.company_name,a.buyer_name ,c.country_ship_date as shipment_date,c.order_quantity as po_quantity,c.order_total as po_total_price,a.set_smv,((c.order_quantity/a.total_set_qnty)*a.set_smv) as sum 
	FROM wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c
	WHERE a.job_no=b.job_no_mst AND b.id=c.po_break_down_id AND a.company_name=$cbo_company_name AND c.country_ship_date between '$s_date' and '$e_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.buyer_name";
/* 	
$sql_con_po="SELECT  a.company_name,a.buyer_name ,c.country_ship_date as shipment_date,(c.order_quantity* a.total_set_qnty) AS po_quantity,(c.order_quantity* a.total_set_qnty*a.set_smv) AS sum,b.po_total_price,a.set_smv FROM wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c
	WHERE a.job_no=b.job_no_mst AND b.id=c.po_break_down_id AND a.company_name=$cbo_company_name AND c.country_ship_date between '$s_date' and '$e_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.buyer_name";	
	
	
	$sql_con_po="SELECT  a.company_name,a.buyer_name ,c.country_ship_date as shipment_date,(b.po_quantity* a.total_set_qnty) AS po_quantity,(b.po_quantity* a.total_set_qnty*a.set_smv) AS sum,b.po_total_price,a.set_smv FROM wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c
	WHERE a.job_no=b.job_no_mst AND b.id=c.po_break_down_id AND a.company_name=$cbo_company_name AND c.country_ship_date between '$s_date' and '$e_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.buyer_name";*/

	$po_qnty_array=array();
	$sql_data_po=sql_select($sql_con_po);
	foreach( $sql_data_po as $row_po)
	{
		
		$date_key=date("Y-m",strtotime($row_po[csf("shipment_date")]));
		$year_key=date("Y",strtotime($row_po[csf("shipment_date")]));
		$com_sum[$date_key]+=$row_po[csf("po_quantity")];
		$com_sum_total+=$row_po[csf("po_quantity")];
		$po_qnty_array[$row_po[csf("buyer_name")]][$date_key]+=$row_po[csf("po_quantity")];
		
		$com_sum_b[$date_key]+=$row_po[csf("sum")]/ $basic_smv_arr[$row_po[csf("company_name")]][$year_key];
		//$com_sum_b_total+=$row_po[csf("sum")]/ $basic_smv_arr[$row_po[csf("company_name")]][$year_key];
		
		$buy_sum_b[$row_po[csf("buyer_name")]][$date_key]+=$row_po[csf("sum")]/ $basic_smv_arr[$row_po[csf("company_name")]][$year_key];
		
		$monthPoValArr[$date_key]+=$row_po[csf("po_total_price")];
		$tot_smv[$date_key]+=$row_po[csf("sum")];

	
	}

	$sql_capa="SELECT a.year,b.month_id, b.capacity_month_pcs AS capa  FROM lib_capacity_calc_mst a,  lib_capacity_year_dtls b
	WHERE a.id=b.mst_id and a.comapny_id =$cbo_company_name AND a.year between $cbo_year_name and $cbo_end_year_name and b.month_id between $cbo_month and $cbo_month_end and a.status_active=1 and a.is_deleted=0";
	$sql_data_capa=sql_select($sql_capa);
	foreach( $sql_data_capa as $row)
	{
	$date_key=date("Y-m",strtotime($row[csf("year")].'-'.$row[csf("month_id")]));
	$com_month_capa_arr[$date_key]+=$row[csf("capa")];	
	$tot_com_month_capa+=$row[csf("capa")];	
	}



	$sql_con="SELECT  b.buyer_id,a.year_id,a.month_id, b.allocation_percentage  FROM lib_capacity_allocation_mst a,lib_capacity_allocation_dtls b
	WHERE a.id=b.mst_id AND a.company_id=$cbo_company_name AND a.year_id between $cbo_year_name and $cbo_end_year_name and a.month_id between $cbo_month and $cbo_month_end and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	 //echo $sql_con; 
	$sql_data=sql_select($sql_con);
	foreach( $sql_data as $row)
		{
			$date_key=date("Y-m",strtotime($row[csf("year_id")].'-'.$row[csf("month_id")]));
			$buy_month_capa_arr[$row[csf("buyer_id")]][$date_key]=($com_month_capa_arr[$date_key]*$row[csf("allocation_percentage")])/100;
			$allo_parcent[$row[csf("buyer_id")]][$date_key]=$row[csf("allocation_percentage")];
			
		}


	$cpAvgRateArray=sql_select( "select applying_period_to_date,asking_avg_rate from  lib_standard_cm_entry where company_id='$cbo_company_name'" );
	foreach( $cpAvgRateArray as $row){
		$date_key=date("Y-m",strtotime($row[csf("applying_period_to_date")]));
		$monthCapAvgArr[$date_key] = $row[csf("asking_avg_rate")];
	}
	


$width=($tot_month*75)+($tot_month+395);
$bgcolor1="#FFFFFF";
$bgcolor2="#E9F3FF";
?>    
<div style="width:<? echo $width;?>px; overflow:hidden; margin:10px 0; height:auto;">
    
    <table width="<? echo $width;?>" cellspacing="0" border="1" rules="all" class="rpt_table">
      <thead>
            <th style="text-align:left;">Company Summary</th>
        </thead>
    </table>   
    
    <table align="right" cellspacing="0" width="<? echo $width;?>"  border="1" rules="all" class="rpt_table" id="tbl_month_pce" >
        <thead>
            <th width="120">Purticulars</th>
            <? foreach($month_arr as $month_id):?>
            <th width="75"><? list($y,$m)=explode('-',$month_id); $m=$m*1; echo $months[$m].' '.$y; ?></th>
            <? endforeach;?>
            <th width="100">Total</th>
            <th>% of Capacity</th> 
        </thead>
        <tbody>
        	<tr onclick="change_color('tr1st_1','<? echo $bgcolor1; ?>')" id="tr1st_1">
                <td align="left">Order Qty</td>
               <? foreach($month_arr as $month_id):?>
                <td align="right" title="Total SMV <? echo $tot_smv[$month_id];?>"><? echo number_format($com_sum[$month_id],0,'',','); ?></td>
                <? endforeach;?> 
                <td align="right"><? echo number_format($com_sum_total,0);?></td>
                <td align="right">&nbsp;</td>
            </tr>
            <tr bgcolor="<? echo $bgcolor2; ?>" onclick="change_color('tr1st_2','<? echo $bgcolor2; ?>')" id="tr1st_2">
                <td align="left">Capacity (Basic)</td>
               <? foreach($month_arr as $month_id): list($y,$m)=explode('-',$month_id);?>
                <td align="right" title="SMV of Basic <? echo $basic_smv_arr[$row_po[csf("company_name")]][$y];?>"><? echo number_format($com_month_capa_arr[$month_id],0,'',','); ?></td>
                <? endforeach;?> 
                <td align="right"><? echo number_format($tot_com_month_capa,0);?></td>
                <td align="right"><? echo $tot_com_month_capa*100/$tot_com_month_capa;?>%</td>
            </tr>
            <tr onclick="change_color('tr1st_3','<? echo $bgcolor2; ?>')" id="tr1st_3">
                <td align="left">Order Qty (Basic)</td>
               <? foreach($month_arr as $month_id):?>
                <td align="right"><? echo number_format($com_sum_b[$month_id],0,'',',');$com_sum_b_total+=$com_sum_b[$month_id]; ?></td>
                <? endforeach;?> 
                <td align="right"><? echo number_format($com_sum_b_total,0);?></td>
                <td align="right"><? echo number_format($com_sum_b_total*100/$tot_com_month_capa,2);?>%</td>
            </tr>
         </tbody>
         <tfoot>
            <tr bgcolor="<? echo $bgcolor ; ?>">
                <th align="left">Balance:</th>
               <? foreach($month_arr as $month_id):?>
                <th align="right"><? echo number_format($com_month_capa_arr[$month_id],0,'','')-number_format($com_sum_b[$month_id],0,'',''); ?></th>
                <? endforeach;?> 
                <th align="right"><? echo $tot_cmp_bal=number_format($tot_com_month_capa,0,'','')- number_format($com_sum_b_total,0,'','');?></th>
                <th align="right"><? 
					$cv=$tot_cmp_bal*100/$tot_com_month_capa;
					if(is_infinite($cv) || is_nan($cv)){$cv=0;}
					echo number_format($cv,2);
				//echo number_format($tot_cmp_bal*100/$tot_com_month_capa,2);
				?></th>
            </tr>
        </tfoot>
     </table>
 <!--Company Summary End....................................................... -->
</div>

<?
$width=($tot_month*75)+($tot_month+465);
?>    
	
    <table width="<? echo $width;?>" cellspacing="0" border="1" rules="all" class="rpt_table">
      <thead>
        <tr>
            <th style="text-align:left;">Buyer wise comparison of basic quantity</th>
        </tr>
        </thead>
    </table> 
      
    <table cellspacing="0" width="<? echo $width;?>px"  border="1" rules="all" class="rpt_table" >
        <thead  align="center">
            <th width="40">SL</th>
            <th width="60" align="center">Buyer</th>
            <th width="90" align="center">Purticulars</th>
            <? foreach($month_arr as $month_id):?>
            <th width="75" align="center"><? list($y,$m)=explode('-',$month_id); $m=$m*1; echo $months[$m].' '.$y; ?></th>
            <? endforeach;?>
            <th width="75" align="center">Total</th>
            <th align="center">% of Capacity</th>           
        </thead>
     </table>
     
 <div style=" max-height:400px; overflow-y:scroll; width:<? echo $width;?>px"  align="left" id="scroll_body">
     <table align="right" cellspacing="0" width="100%"  border="1" rules="all" class="rpt_table" >
        <tbody>
<?
		$i=1;
		foreach($po_qnty_array as $buyer_id=>$row)
		{
			$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";  
	
		?>
       
            <tr bgcolor="<? echo $bgcolor ; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td rowspan="3" width="40"><? echo $i; ?></td>
                <td align="left" rowspan="3" width="60"><p><? echo $buyer_short_name_arr[$buyer_id]; ?></p></td>
                <td align="left" width="90">Capacity (Basic)</td>
               <? foreach($month_arr as $month_id):?>
                <td align="right" width="75" title="Allocated <? echo $allo_parcent[$buyer_id][$month_id];?>%"><? echo number_format($buy_month_capa_arr[$buyer_id][$month_id],0,'',','); ?></td>
			   <? $grand_cap_qty_m[$month_id]+=$buy_month_capa_arr[$buyer_id][$month_id]; ?>
			   <? $tot_cap_qty_b[$buyer_id]+=$buy_month_capa_arr[$buyer_id][$month_id]; ?>
               <? endforeach;?> 
                <td align="right"  width="75"><? echo number_format($tot_cap_qty_b[$buyer_id],0);?></td>
                <td align="right"><? 
					$cv=$tot_cap_qty_b[$buyer_id]*100/$tot_cap_qty_b[$buyer_id];
					if(is_infinite($cv) || is_nan($cv)){$cv=0;}
					echo number_format($cv,2);
				//echo $tot_cap_qty_b[$buyer_id]*100/$tot_cap_qty_b[$buyer_id];
				
				?>%</td> 
            </tr>
             <tr bgcolor="<? echo $bgcolor ; ?>">
                <td align="left">Order Qty (Basic)</td>
               <? foreach($month_arr as $month_id):?>
                <td align="right"><? echo number_format($buy_sum_b[$buyer_id][$month_id],0,'',','); ?></td>
               <? $grand_order_qty_m[$month_id]+=$buy_sum_b[$buyer_id][$month_id]; ?>
			   <? $tot_order_qty_b[$buyer_id]+=$buy_sum_b[$buyer_id][$month_id]; ?>
               <? endforeach;?> 
                <td align="right"><? echo number_format($tot_order_qty_b[$buyer_id],0);?></td>
                <td align="right"><? 
					$cv=($tot_order_qty_b[$buyer_id]*100)/$tot_cap_qty_b[$buyer_id];
					if(is_infinite($cv) || is_nan($cv)){$cv=0;}
					echo number_format($cv,2);
				//echo number_format(($tot_order_qty_b[$buyer_id]*100)/$tot_cap_qty_b[$buyer_id],2); 
				?>%</td> 
            </tr>
             <tr bgcolor="<? echo $bgcolor ; ?>">
                <td align="left">Balance</td>
               <? foreach($month_arr as $month_id):?>
                <td align="right"><? echo number_format($buy_month_capa_arr[$buyer_id][$month_id]-$buy_sum_b[$buyer_id][$month_id],0,'',','); ?></td>
                <? $tot_bal_b[$buyer_id]+=$buy_month_capa_arr[$buyer_id][$month_id]-$buy_sum_b[$buyer_id][$month_id];?>
				<? endforeach;?> 
                <td align="right"><? echo $tot_bal=number_format($tot_cap_qty_b[$buyer_id],0,'','')- number_format($tot_order_qty_b[$buyer_id],0,'',''); ?></td>
                <td align="right"><? 
					$cv=($tot_bal*100)/$tot_cap_qty_b[$buyer_id];
					if(is_infinite($cv) || is_nan($cv)){$cv=0;}
					echo number_format($cv,2);
				
				//echo number_format(($tot_bal*100)/$tot_cap_qty_b[$buyer_id],2); 
				?>%</td> 
            </tr>
        <?
		$i++;
		}
	?>
        </tbody>
     </table>
  </div>
            
   <table width="<? echo $width;?>" cellspacing="0" border="1" rules="all" class="rpt_table">
       <tfoot>
            <tr>
                <th width="192" >Allocate Capacity:</th>
               <? foreach($month_arr as $month_id):?>
                <th align="right" width="75"><? echo number_format($grand_cap_qty_m[$month_id],0,'',',');?></th>
               <? $grand_to_cap_qty+=$grand_cap_qty_m[$month_id];?> 
               <? endforeach;?> 
                <th width="75"><? echo number_format($grand_to_cap_qty,0,'',',');?></th>
                <th width=""><? echo $grand_to_cap_qty*100/$grand_to_cap_qty;?>%</th>
                <th width="13">&nbsp;</th>
            </tr>
            <tr>
                <th>Order Qty.:</th>
               <? foreach($month_arr as $month_id):?>
                <th align="right" width="75"><? echo number_format($grand_order_qty_m[$month_id],0,'',',');?></th>
              <? $grand_to_order_qty+=$grand_order_qty_m[$month_id];?> 
               <? endforeach;?> 
                <th align="right"><? echo number_format($grand_to_order_qty,0); ?></th>
                <th align="right"><? echo number_format(($grand_to_order_qty*100)/$grand_to_cap_qty,2) ?>%</th>
                <th width="13">&nbsp;</th>
            </tr>
            <tr>
                <th>Balance:</th>
               <? foreach($month_arr as $month_id):?>
                <th align="right" width="75"><? echo number_format($grand_cap_qty_m[$month_id],0,'','')-number_format($grand_order_qty_m[$month_id],0,'','');?></th>
               <? endforeach;?> 
                <th align="right"><? echo $g_bla=number_format($grand_to_cap_qty,0,'','')-number_format($grand_to_order_qty,0,'','');?></th>
                <th align="right"><? echo number_format(($g_bla*100)/$grand_to_cap_qty,2) ?>%</th>
                <th width="16">&nbsp;</th>
            </tr>
        </tfoot>
    </table>
  
 <!--Buyer wise comparison of basic quantity End.............................. -->
    
    <br/>
	
	<?
    $width=($tot_month==0)?500:1000;
    $width2=($tot_month==0)?100:49;
    ?>    
    
    
     <table width="<? echo $width;?>" cellspacing="0" border="1" rules="all" class="rpt_table">
      <thead>
        <tr>
            <th style="text-align:left;">Company Wise Booking Status</th>
        </tr>
      </thead>
    </table> 
    
    <div style="width:<? echo $width;?>px;">
        <? $i=1; foreach($month_arr as $month_id): $margin=($i%2==0)?'right':'left';?>
        
        <div style="width:<? echo $width2;?>%; float:<? echo $margin;?>; margin:5px; margin-<? echo $margin;?>:0;">
        <strong><? list($y,$m)=explode('-',$month_id); $m=$m*1; echo $months[$m].', '.$y; ?></strong>
            <table width="100%" border="1" rules="all" class="rpt_table">
                <thead>
                    <th width="220">Bar Chart</th>
                    <th width="65">Particular</th>
                    <th width="65">Qty(pcs)</th>
                    <th width="60">Avg.Rate</th>
                    <th>Po Value ($)</th>
                </thead>
                <tr>
                    <td rowspan="4" valign="bottom">
                    <div id="container<? echo $i;?>" style="width: 220px; height:190px;"></div>
                   
				    <script>  hs_chart(<? echo $i;?>,'<? echo $com_month_capa_arr[$month_id];?>','<? echo $com_sum_b[$month_id];?>','<? echo number_format($com_month_capa_arr[$month_id],0,'','')-number_format($com_sum_b[$month_id],0,'','');?>','<? echo $months[$m];?>'); </script>
                    <script>  //hs_chart(<? //echo $i;?>,144,215,154); </script>
                    
                    </td>
                    <td>Capacity (Basic)</td>
                    <td align="right"><? echo number_format($com_month_capa_arr[$month_id],0,'',','); ?></td>
                    <td align="right"><? echo number_format($monthCapAvgArr[$month_id],0,'',','); ?></td>
                    <td align="right"><? echo number_format($com_month_capa_arr[$month_id]*$monthCapAvgArr[$month_id],0,'',','); ?></td>
                </tr>
                <tr>
                    <td>Order Qty. (Basic)</td>
                    <td align="right"><? echo round($com_sum_b[$month_id]);?></td>
                    <td align="right"><? 
						$cv=($monthPoValArr[$month_id]/round($com_sum_b[$month_id]));
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,4,'.',',');
					//echo number_format(($monthPoValArr[$month_id]/round($com_sum_b[$month_id])),4,'.',','); 
					?></td>
                    <td align="right"><? echo number_format($monthPoValArr[$month_id],2,'.',',');?></td>
                </tr>
                <tr>
                    <td>Balance</td>
                    <td align="right"><? echo $rb=number_format($com_month_capa_arr[$month_id],0,'','')-number_format($com_sum_b[$month_id],0,'',''); ?></td>
                    <td align="right"><? 
						$cv=(($com_month_capa_arr[$month_id]*$monthCapAvgArr[$month_id])-$monthPoValArr[$month_id])/($com_month_capa_arr[$month_id]-$com_sum_b[$month_id]);
						if(is_infinite($cv) || is_nan($cv)){$cv=0;}
						echo number_format($cv,2,'.',',');
					// echo number_format((($com_month_capa_arr[$month_id]*$monthCapAvgArr[$month_id])-$monthPoValArr[$month_id])/($com_month_capa_arr[$month_id]-$com_sum_b[$month_id]),2,'.',''); 
					 ?></td>
                    <td align="right"><? echo number_format($com_month_capa_arr[$month_id]*$monthCapAvgArr[$month_id],2,'.','')-number_format($monthPoValArr[$month_id],2,'.',''); ?></td>
                </tr>
                <tr>
                    <td colspan="4" valign="middle" align="center"><? if($com_month_capa_arr[$month_id]-$com_sum_b[$month_id]>0){ echo "Under Booking";} else{ echo "Over Booking";} ?></td>
                </tr>
          </table>
        </div>
 		<? $i++;  endforeach; ?>
          
    </div>

        
    
    <?
	
}














if($action=="capacity_allocation_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
?>
<div id="table_row" align="center" style="height:auto; width:1190px; margin:0 auto; padding:0;">
<?
$company_library=sql_select("select id, company_name, plot_no, level_no,road_no,city from lib_company where id=".$data[0]."");
$year_library=return_library_array( "select id, year from  lib_capacity_calc_mst", "id", "year"  );
	foreach( $company_library as $row)
	{
?>
		<span style="font-size:30px"><center><? echo $row[csf('company_name')]." .\n";?></center></span>
<?
	}
?>

<?
$basic_smv_arr=return_library_array( "select comapny_id, basic_smv from lib_capacity_calc_mst",'comapny_id','basic_smv');
$daysinmonth=cal_days_in_month(CAL_GREGORIAN, $data[3], $data[1]);
$s_date=$data[1]."-".$data[2]."-"."01";
$e_date=$data[1]."-".$data[3]."-".$daysinmonth;
$sql_con_po="SELECT  a.company_name,a.buyer_name ";
for($i=1;$i<=12;$i++)
{
	$sql_con_po .= ",SUM(CASE WHEN MONTH(b.pub_shipment_date) =".$i. " THEN (b.po_quantity* a.total_set_qnty) END) AS 'po_quantity$i',SUM(CASE WHEN MONTH(b.pub_shipment_date) =".$i. " THEN (b.po_quantity* a.total_set_qnty*a.set_smv)   END) AS 'sum$i'";

}
	$sql_con_po .= " FROM wo_po_details_master a,wo_po_break_down b
WHERE a.job_no=b.job_no_mst AND a.company_name=$data[0]   AND b.pub_shipment_date between '$s_date' and '$e_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0    GROUP BY a.buyer_name order by a.buyer_name";

$po_qnty_array=array();
$sql_data_po=sql_select($sql_con_po);
foreach( $sql_data_po as $row_po)
{
  $po_qnty_array[$row_po[csf("buyer_name")]]=array("sum1"=>0,"sum2"=>0,"sum3"=>0,"sum4"=>0,"sum5"=>0,"sum6"=>0,"sum7"=>0,"sum8"=>0,"sum9"=>0,"sum10"=>0,"sum11"=>0,"sum12"=>0);
  $po_qnty_array[$row_po[csf("buyer_name")]][sum1]+=$row_po[csf("sum1")]/ $basic_smv_arr[$row_po[csf("company_name")]];
  $po_qnty_array[$row_po[csf("buyer_name")]][sum2]+=$row_po[csf("sum2")]/ $basic_smv_arr[$row_po[csf("company_name")]];
  $po_qnty_array[$row_po[csf("buyer_name")]][sum3]+=$row_po[csf("sum3")]/ $basic_smv_arr[$row_po[csf("company_name")]];
  $po_qnty_array[$row_po[csf("buyer_name")]][sum4]+=$row_po[csf("sum4")]/ $basic_smv_arr[$row_po[csf("company_name")]];
  $po_qnty_array[$row_po[csf("buyer_name")]][sum5]+=$row_po[csf("sum5")]/ $basic_smv_arr[$row_po[csf("company_name")]];
  $po_qnty_array[$row_po[csf("buyer_name")]][sum6]+=$row_po[csf("sum6")]/ $basic_smv_arr[$row_po[csf("company_name")]];
  $po_qnty_array[$row_po[csf("buyer_name")]][sum7]+=$row_po[csf("sum7")]/ $basic_smv_arr[$row_po[csf("company_name")]];
  $po_qnty_array[$row_po[csf("buyer_name")]][sum8]+=$row_po[csf("sum8")]/ $basic_smv_arr[$row_po[csf("company_name")]];
  $po_qnty_array[$row_po[csf("buyer_name")]][sum9]+=$row_po[csf("sum9")]/ $basic_smv_arr[$row_po[csf("company_name")]];
  $po_qnty_array[$row_po[csf("buyer_name")]][sum10]+=$row_po[csf("sum10")]/ $basic_smv_arr[$row_po[csf("company_name")]];
  $po_qnty_array[$row_po[csf("buyer_name")]][sum11]+=$row_po[csf("sum11")]/ $basic_smv_arr[$row_po[csf("company_name")]];
  $po_qnty_array[$row_po[csf("buyer_name")]][sum12]+=$row_po[csf("sum12")]/ $basic_smv_arr[$row_po[csf("company_name")]];
  $com_sum_1+=$row_po[csf("po_quantity1")];
  $com_sum_2+=$row_po[csf("po_quantity2")];
  $com_sum_3+=$row_po[csf("po_quantity3")];
  $com_sum_4+=$row_po[csf("po_quantity4")];
  $com_sum_5+=$row_po[csf("po_quantity5")];
  $com_sum_6+=$row_po[csf("po_quantity6")];
  $com_sum_7+=$row_po[csf("po_quantity7")];
  $com_sum_8+=$row_po[csf("po_quantity8")];
  $com_sum_9+=$row_po[csf("po_quantity9")];
  $com_sum_10+=$row_po[csf("po_quantity10")];
  $com_sum_11+=$row_po[csf("po_quantity11")];
  $com_sum_12+=$row_po[csf("po_quantity12")];
  
  $com_sum_total+=$row_po[csf("po_quantity1")]+$row_po[csf("po_quantity2")]+$row_po[csf("po_quantity3")]+$row_po[csf("po_quantity4")]+$row_po[csf("po_quantity5")]+$row_po[csf("po_quantity6")]+$row_po[csf("po_quantity7")]+$row_po[csf("po_quantity8")]+$row_po[csf("po_quantity9")]+$row_po[csf("po_quantity10")]+$row_po[csf("po_quantity11")]+$row_po[csf("po_quantity12")];
  
  $com_sum_b_1+=$row_po[csf("sum1")]/ $basic_smv_arr[$row_po[csf("company_name")]];
  $com_sum_b_2+=$row_po[csf("sum2")]/ $basic_smv_arr[$row_po[csf("company_name")]];
  $com_sum_b_3+=$row_po[csf("sum3")]/ $basic_smv_arr[$row_po[csf("company_name")]];
  $com_sum_b_4+=$row_po[csf("sum4")]/ $basic_smv_arr[$row_po[csf("company_name")]];
  $com_sum_b_5+=$row_po[csf("sum5")]/ $basic_smv_arr[$row_po[csf("company_name")]];
  $com_sum_b_6+=$row_po[csf("sum6")]/ $basic_smv_arr[$row_po[csf("company_name")]];
  $com_sum_b_7+=$row_po[csf("sum7")]/ $basic_smv_arr[$row_po[csf("company_name")]];
  $com_sum_b_8+=$row_po[csf("sum8")]/ $basic_smv_arr[$row_po[csf("company_name")]];
  $com_sum_b_9+=$row_po[csf("sum9")]/ $basic_smv_arr[$row_po[csf("company_name")]];
  $com_sum_b_10+=$row_po[csf("sum10")]/ $basic_smv_arr[$row_po[csf("company_name")]];
  $com_sum_b_11+=$row_po[csf("sum11")]/ $basic_smv_arr[$row_po[csf("company_name")]];
  $com_sum_b_12+=$row_po[csf("sum12")]/ $basic_smv_arr[$row_po[csf("company_name")]];
 
}

	$total_sum1=0;$total_sum2=0;$total_sum3=0;$total_sum4=0;$total_sum5=0;$total_sum6=0;$total_sum7=0;$total_sum8=0;$total_sum9=0;$total_sum10=0;$total_sum11=0;$total_sum12=0;    $grand_total=0;$total_allocation=0;
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id","buyer_name"  );
	$sql_con="SELECT  a.company_id,b.buyer_id,b.allocation_percentage";
	for($i=1;$i<=12;$i++)
	{
		 $sql_con .= ",SUM(CASE WHEN d.month_id =".$i. " THEN (d.capacity_month_pcs)   END) AS 'capa$i',SUM(CASE WHEN d.month_id =".$i. " THEN (d.capacity_month_pcs* b.allocation_percentage)/100   END) AS 'sum$i'";
	}
	$sql_con .= "FROM lib_capacity_allocation_mst a,lib_capacity_allocation_dtls b, lib_capacity_calc_mst c,  lib_capacity_year_dtls d
	WHERE a.id=b.mst_id AND a.year_id=c.year AND d.month_id=a.month_id  AND c.id=d.mst_id AND a.company_id=$data[0]    AND a.year_id=$data[1] and d.month_id between $data[2] and $data[3]   GROUP BY b.buyer_id";
	$sql_data=sql_select($sql_con);
	foreach( $sql_data as $row)
		{
			$total_sum1 = $total_sum1+$row[csf("sum1")];
			$total_sum2 = $total_sum2+$row[csf("sum2")];
			$total_sum3 = $total_sum3+$row[csf("sum3")];
			$total_sum4 = $total_sum4+$row[csf("sum4")];
			$total_sum5 = $total_sum5+$row[csf("sum5")];
			$total_sum6 = $total_sum6+$row[csf("sum6")];
			$total_sum7 = $total_sum7+$row[csf("sum7")];
			$total_sum8 = $total_sum8+$row[csf("sum8")];
			$total_sum9 = $total_sum9+$row[csf("sum9")];
			$total_sum10 = $total_sum10+$row[csf("sum10")];
			$total_sum11 = $total_sum11+$row[csf("sum11")];
			$total_sum12 = $total_sum12+$row[csf("sum12")];
			$total_cap+=$row[csf("sum1")]+$row[csf("sum2")]+$row[csf("sum3")]+$row[csf("sum4")]+$row[csf("sum5")]+$row[csf("sum6")]+$row[csf("sum7")]+$row[csf("sum8")]+$row[csf("sum9")]+$row[csf("sum10")]+$row[csf("sum11")]+$row[csf("sum12")];
		}
?>
    <table width="1190px" align="center">
        <tr>
            <td colspan="6" align="center" style="font-size:28px"><center><strong><u>Monthly Capacity and Order Qty <? echo $year_library[$data[1]]; ?></u></strong></center></td>
        </tr>
           
    </table>
    <br/>
    
    <table width="1190px" align="center">
        <tr>
            <td colspan="6" align="left" style="font-size:24px"><strong>Company Summary</strong></td>
        </tr>
    </table>   
    
<div style="width:1190px; height:auto">
    <table align="right" cellspacing="0" width="1190px"  border="1" rules="all" class="rpt_table_qty_allocation" id="tbl_month_pce" >
        <thead bgcolor="#dddddd" align="center">
            <th width="145" align="center">Purticulars</th>
            <th width="75" align="center">Jan</th>
            <th width="75" align="center">Feb</th>
            <th width="75" align="center">Mar</th>
            <th width="75" align="center">Apr</th>
            <th width="75" align="center">May</th>
            <th width="75" align="center">Jun</th>
            <th width="75" align="center">Jul</th>
            <th width="75" align="center">Aug</th>
            <th width="75" align="center">Sep</th>
            <th width="75" align="center">Oct</th>
            <th width="75" align="center">Nov</th>
            <th width="75" align="center">Dec</th>
            <th width="75" align="center">Total</th>
                      
        </thead>
        <tbody>
        <tr bgcolor="<? echo $bgcolor ; ?>">
                <td align="left">Order Qty.</td>
                <td align="right"><? echo number_format($com_sum_1,0,'',','); ?></td>
                <td align="right"><? echo number_format($com_sum_2,0,'',',');  ; ?></td>
                <td align="right"><? echo number_format($com_sum_3,0,'',','); ?></td>
                <td align="right"><? echo number_format($com_sum_4,0,'',','); ?></td>
                <td align="right"><? echo number_format($com_sum_5,0,'',','); ?></td>
                <td align="right"><? echo number_format($com_sum_6,0,'',','); ?></td>
                <td align="right"><? echo number_format($com_sum_7,0,'',','); ?></td>
                <td align="right"><? echo number_format($com_sum_8,0,'',','); ?></td>
                <td align="right"><? echo number_format($com_sum_9,0,'',','); ?></td>
                <td align="right"><? echo number_format($com_sum_10,0,'',','); ?></td>
                <td align="right"><? echo number_format($com_sum_11,0,'',','); ?></td>
                <td align="right"><? echo number_format($com_sum_12,0,'',','); ?></td>
                <td align="right"><? echo number_format($com_sum_total,0);?></td>
            </tr>
            <tr bgcolor="<? echo $bgcolor ; ?>">
                <td align="left">Capacity (Basic Qty)</td>
                <td align="right"><? echo number_format($total_sum1,0,'',','); ?></td>
                <td align="right"><? echo number_format($total_sum2,0,'',',');  ; ?></td>
                <td align="right"><? echo number_format($total_sum3,0,'',','); ?></td>
                <td align="right"><? echo number_format($total_sum4,0,'',','); ?></td>
                <td align="right"><? echo number_format($total_sum5,0,'',','); ?></td>
                <td align="right"><? echo number_format($total_sum6,0,'',','); ?></td>
                <td align="right"><? echo number_format($total_sum7,0,'',','); ?></td>
                <td align="right"><? echo number_format($total_sum8,0,'',','); ?></td>
                <td align="right"><? echo number_format($total_sum9,0,'',','); ?></td>
                <td align="right"><? echo number_format($total_sum10,0,'',','); ?></td>
                <td align="right"><? echo number_format($total_sum11,0,'',','); ?></td>
                <td align="right"><? echo number_format($total_sum12,0,'',','); ?></td>
                <td align="right"><? echo number_format($total_cap,0);?></td>
            </tr>
            <tr bgcolor="<? echo $bgcolor ; ?>">
                <td align="left">Order Qty. In Basic</td>
                <td align="right"><? echo number_format($com_sum_b_1,0,'',','); ?></td>
                <td align="right"><? echo number_format($com_sum_b_2,0,'',',');  ; ?></td>
                <td align="right"><? echo number_format($com_sum_b_3,0,'',','); ?></td>
                <td align="right"><? echo number_format($com_sum_b_4,0,'',','); ?></td>
                <td align="right"><? echo number_format($com_sum_b_5,0,'',','); ?></td>
                <td align="right"><? echo number_format($com_sum_b_6,0,'',','); ?></td>
                <td align="right"><? echo number_format($com_sum_b_7,0,'',','); ?></td>
                <td align="right"><? echo number_format($com_sum_b_8,0,'',','); ?></td>
                <td align="right"><? echo number_format($com_sum_b_9,0,'',','); ?></td>
                <td align="right"><? echo number_format($com_sum_b_10,0,'',','); ?></td>
                <td align="right"><? echo number_format($com_sum_b_11,0,'',','); ?></td>
                <td align="right"><? echo number_format($com_sum_b_12,0,'',','); ?></td>
                <?  $com_sum_b_total= $com_sum_b_1+$com_sum_b_2+$com_sum_b_3+$com_sum_b_4+$com_sum_b_5+$com_sum_b_6+$com_sum_b_7+$com_sum_b_8+$com_sum_b_9+$com_sum_b_10+$com_sum_b_11+$com_sum_b_12;?>
                <td align="right"><? echo number_format($com_sum_b_total,0);?></td>
            </tr>
            
            <tr bgcolor="<? echo $bgcolor ; ?>">
                <td align="left">Balance:</td>
                <td align="right"><? echo number_format($total_sum1-$com_sum_b_1,0,'',','); ?></td>
                <td align="right"><? echo number_format($total_sum2-$com_sum_b_2,0,'',',');  ; ?></td>
                <td align="right"><? echo number_format($total_sum3-$com_sum_b_3,0,'',','); ?></td>
                <td align="right"><? echo number_format($total_sum4-$com_sum_b_4,0,'',','); ?></td>
                <td align="right"><? echo number_format($total_sum5-$com_sum_b_5,0,'',','); ?></td>
                <td align="right"><? echo number_format($total_sum6-$com_sum_b_6,0,'',','); ?></td>
                <td align="right"><? echo number_format($total_sum7-$com_sum_b_7,0,'',','); ?></td>
                <td align="right"><? echo number_format($total_sum8-$com_sum_b_8,0,'',','); ?></td>
                <td align="right"><? echo number_format($total_sum9-$com_sum_b_9,0,'',','); ?></td>
                <td align="right"><? echo number_format($total_sum10-$com_sum_b_10,0,'',','); ?></td>
                <td align="right"><? echo number_format($total_sum11-$com_sum_b_11,0,'',','); ?></td>
                <td align="right"><? echo number_format($total_sum12-$com_sum_b_12,0,'',','); ?></td>
                <td align="right"><? echo number_format($total_cap-$com_sum_b_total,0);?></td>
            </tr>
        </tbody>
        </table>
        <br />
    
 
 <table width="1190px" align="center">
        <tr>
            <td colspan="6" align="left" style="font-size:24px"><strong>Buyer wise comparison of basic quantity</strong></td>
        </tr>
    </table>   
    
<div style="width:1190px; height:auto">
    <table align="right" cellspacing="0" width="1190px"  border="1" rules="all" class="rpt_table_qty_allocation" id="tbl_month_pce" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="105" align="center">Buyer</th>
            <th width="105" align="center">Purticulars</th>
            <th width="75" align="center">Jan</th>
            <th width="75" align="center">Feb</th>
            <th width="75" align="center">Mar</th>
            <th width="75" align="center">Apr</th>
            <th width="75" align="center">May</th>
            <th width="75" align="center">Jun</th>
            <th width="75" align="center">Jul</th>
            <th width="75" align="center">Aug</th>
            <th width="75" align="center">Sep</th>
            <th width="75" align="center">Oct</th>
            <th width="75" align="center">Nov</th>
            <th width="75" align="center">Dec</th>
            <th width="75" align="center">Total</th>
            <th width="80" align="center">%of Total</th>           
        </thead>
        <tbody>
<?
	
			
		$i=1;
		//$total_sum1=0;$total_sum2=0;$total_sum3=0;$total_sum4=0;$total_sum5=0;$total_sum6=0;$total_sum7=0;$total_sum8=0;$total_sum9=0;$total_sum10=0;$total_sum11=0;$total_sum12=0;$grand_total=0;$total_allocation=0;
		
		foreach( $sql_data as $row)
		{
			if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
			
			/*$total_sum1 = $total_sum1+$row[csf("sum1")];
			$total_sum2 = $total_sum2+$row[csf("sum2")];
			$total_sum3 = $total_sum3+$row[csf("sum3")];
			$total_sum4 = $total_sum4+$row[csf("sum4")];
			$total_sum5 = $total_sum5+$row[csf("sum5")];
			$total_sum6 = $total_sum6+$row[csf("sum6")];
			$total_sum7 = $total_sum7+$row[csf("sum7")];
			$total_sum8 = $total_sum8+$row[csf("sum8")];
			$total_sum9 = $total_sum9+$row[csf("sum9")];
			$total_sum10 = $total_sum10+$row[csf("sum10")];
			$total_sum11 = $total_sum11+$row[csf("sum11")];
			$total_sum12 = $total_sum12+$row[csf("sum12")];
			$grand_total = $grand_total+$total;
			$total_allocation = $total_allocation+$row[csf("allocation_percentage")];*/
			
			$total_order_1+=$po_qnty_array[$row[csf("buyer_id")]][sum1];
			$total_order_2+=$po_qnty_array[$row[csf("buyer_id")]][sum2];
			$total_order_3+=$po_qnty_array[$row[csf("buyer_id")]][sum3];
			$total_order_4+=$po_qnty_array[$row[csf("buyer_id")]][sum4];
			$total_order_5+=$po_qnty_array[$row[csf("buyer_id")]][sum5];
			$total_order_6+=$po_qnty_array[$row[csf("buyer_id")]][sum6];
			$total_order_7+=$po_qnty_array[$row[csf("buyer_id")]][sum7];
			$total_order_8+=$po_qnty_array[$row[csf("buyer_id")]][sum8];
			$total_order_9+=$po_qnty_array[$row[csf("buyer_id")]][sum9];
			$total_order_10+=$po_qnty_array[$row[csf("buyer_id")]][sum10];
			$total_order_11+=$po_qnty_array[$row[csf("buyer_id")]][sum11];
			$total_order_12+=$po_qnty_array[$row[csf("buyer_id")]][sum12];
			
			$balance_1+=$row[csf("sum1")]-$po_qnty_array[$row[csf("buyer_id")]][sum1];
			$balance_2+=$row[csf("sum2")]-$po_qnty_array[$row[csf("buyer_id")]][sum2];
			$balance_3+=$row[csf("sum3")]-$po_qnty_array[$row[csf("buyer_id")]][sum3];
			$balance_4+=$row[csf("sum4")]-$po_qnty_array[$row[csf("buyer_id")]][sum4];
			$balance_5+=$row[csf("sum5")]-$po_qnty_array[$row[csf("buyer_id")]][sum5];
			$balance_6+=$row[csf("sum6")]-$po_qnty_array[$row[csf("buyer_id")]][sum6];
			$balance_7+=$row[csf("sum7")]-$po_qnty_array[$row[csf("buyer_id")]][sum7];
			$balance_8+=$row[csf("sum8")]-$po_qnty_array[$row[csf("buyer_id")]][sum8];
			$balance_9+=$row[csf("sum9")]-$po_qnty_array[$row[csf("buyer_id")]][sum9];
			$balance_10+=$row[csf("sum10")]-$po_qnty_array[$row[csf("buyer_id")]][sum10];
			$balance_11+=$row[csf("sum11")]-$po_qnty_array[$row[csf("buyer_id")]][sum11];
			$balance_12+=$row[csf("sum12")]-$po_qnty_array[$row[csf("buyer_id")]][sum12];
			
	
		?>
       
            <tr bgcolor="<? echo $bgcolor ; ?>">
            	<td rowspan="3"><? echo $i; ?></td>
                <td align="left" rowspan="3"><? echo $buyer_library[$row[csf("buyer_id")]]; ?></td>
                <td align="left">Capacity</td>
                <td align="right"><? echo number_format($row[csf("sum1")],0,'',','); ?></td>
                <td align="right"><? echo number_format($row[csf("sum2")],0,'',',');  ; ?></td>
                <td align="right"><? echo number_format($row[csf("sum3")],0,'',','); ?></td>
                <td align="right"><? echo number_format($row[csf("sum4")],0,'',','); ?></td>
                <td align="right"><? echo number_format($row[csf("sum5")],0,'',','); ?></td>
                <td align="right"><? echo number_format($row[csf("sum6")],0,'',','); ?></td>
                <td align="right"><? echo number_format($row[csf("sum7")],0,'',','); ?></td>
                <td align="right"><? echo number_format($row[csf("sum8")],0,'',','); ?></td>
                <td align="right"><? echo number_format($row[csf("sum9")],0,'',','); ?></td>
                <td align="right"><? echo number_format($row[csf("sum10")],0,'',','); ?></td>
                <td align="right"><? echo number_format($row[csf("sum11")],0,'',','); ?></td>
                <td align="right"><? echo number_format($row[csf("sum12")],0,'',','); ?></td>
                <?
				$total=$row[csf("sum1")]+$row[csf("sum2")]+$row[csf("sum3")]+$row[csf("sum4")]+$row[csf("sum5")]+$row[csf("sum6")]+$row[csf("sum7")]+$row[csf("sum8")]+$row[csf("sum9")]+$row[csf("sum10")]+$row[csf("sum11")]+$row[csf("sum12")];
				?>
                <td align="right"><? echo number_format($total,0);?></td>
                <td align="right"><? echo number_format($total/$total_cap*100,2); $percent_tot+=$total/$total_cap*100; ?>%</td> 
            </tr>
             <tr bgcolor="<? echo $bgcolor ; ?>">
            	
               
                <td align="left">Order Qty.</td>
                <td align="right"><? echo number_format($po_qnty_array[$row[csf("buyer_id")]][sum1],0,'',','); ?></td>
                <td align="right"><? echo number_format($po_qnty_array[$row[csf("buyer_id")]][sum2],0,'',','); ?></td>
                <td align="right"><? echo number_format($po_qnty_array[$row[csf("buyer_id")]][sum3],0,'',','); ?></td>
                <td align="right"><? echo number_format($po_qnty_array[$row[csf("buyer_id")]][sum4],0,'',','); ?></td>
                <td align="right"><? echo number_format($po_qnty_array[$row[csf("buyer_id")]][sum5],0,'',','); ?></td>
                <td align="right"><? echo number_format($po_qnty_array[$row[csf("buyer_id")]][sum6],0,'',','); ?></td>
                <td align="right"><? echo number_format($po_qnty_array[$row[csf("buyer_id")]][sum7],0,'',','); ?></td>
                <td align="right"><? echo number_format($po_qnty_array[$row[csf("buyer_id")]][sum8],0,'',','); ?></td>
                <td align="right"><? echo number_format($po_qnty_array[$row[csf("buyer_id")]][sum9],0,'',','); ?></td>
                <td align="right"><? echo number_format($po_qnty_array[$row[csf("buyer_id")]][sum10],0,'',','); ?></td>
                <td align="right"><? echo number_format($po_qnty_array[$row[csf("buyer_id")]][sum11],0,'',','); ?></td>
                <td align="right"><? echo number_format($po_qnty_array[$row[csf("buyer_id")]][sum12],0,'',','); ?></td>
                <?
				$total_order=$po_qnty_array[$row[csf("buyer_id")]][sum1]+$po_qnty_array[$row[csf("buyer_id")]][sum2]+$po_qnty_array[$row[csf("buyer_id")]][sum3]+$po_qnty_array[$row[csf("buyer_id")]][sum4]+$po_qnty_array[$row[csf("buyer_id")]][sum5]+$po_qnty_array[$row[csf("buyer_id")]][sum6]+$po_qnty_array[$row[csf("buyer_id")]][sum7]+$po_qnty_array[$row[csf("buyer_id")]][sum8]+$po_qnty_array[$row[csf("buyer_id")]][sum9]+$po_qnty_array[$row[csf("buyer_id")]][sum10]+$po_qnty_array[$row[csf("buyer_id")]][sum11]+$po_qnty_array[$row[csf("buyer_id")]][sum12];
				?>
                <td align="right"><? echo number_format($total_order,0);?></td>
                <td align="right"><? //echo $row[csf("allocation_percentage")]; ?>%</td> 
            </tr>
             <tr bgcolor="<? echo $bgcolor ; ?>">
            	
               
                <td align="left">Balance</td>
                <td align="right"><? echo number_format($row[csf("sum1")]-$po_qnty_array[$row[csf("buyer_id")]][sum1],0,'',','); ?></td>
                <td align="right"><? echo number_format($row[csf("sum2")]-$po_qnty_array[$row[csf("buyer_id")]][sum2],0,'',',');  ; ?></td>
                <td align="right"><? echo number_format($row[csf("sum3")]-$po_qnty_array[$row[csf("buyer_id")]][sum3],0,'',','); ?></td>
                <td align="right"><? echo number_format($row[csf("sum4")]-$po_qnty_array[$row[csf("buyer_id")]][sum4],0,'',','); ?></td>
                <td align="right"><? echo number_format($row[csf("sum5")]-$po_qnty_array[$row[csf("buyer_id")]][sum5],0,'',','); ?></td>
                <td align="right"><? echo number_format($row[csf("sum6")]-$po_qnty_array[$row[csf("buyer_id")]][sum6],0,'',','); ?></td>
                <td align="right"><? echo number_format($row[csf("sum7")]-$po_qnty_array[$row[csf("buyer_id")]][sum7],0,'',','); ?></td>
                <td align="right"><? echo number_format($row[csf("sum8")]-$po_qnty_array[$row[csf("buyer_id")]][sum8],0,'',','); ?></td>
                <td align="right"><? echo number_format($row[csf("sum9")]-$po_qnty_array[$row[csf("buyer_id")]][sum9],0,'',','); ?></td>
                <td align="right"><? echo number_format($row[csf("sum10")]-$po_qnty_array[$row[csf("buyer_id")]][sum10],0,'',','); ?></td>
                <td align="right"><? echo number_format($row[csf("sum11")]-$po_qnty_array[$row[csf("buyer_id")]][sum11],0,'',','); ?></td>
                <td align="right"><? echo number_format($row[csf("sum12")]-$po_qnty_array[$row[csf("buyer_id")]][sum12],0,'',','); ?></td>
                <?
				$total_balance=($row[csf("sum1")]-$sql_data_po[$i-1][sum1])+($row[csf("sum2")]-$sql_data_po[$i-1][sum2])+($row[csf("sum3")]-$sql_data_po[$i-1][sum3])+($row[csf("sum4")]-$sql_data_po[$i-1][sum4])+($row[csf("sum5")]-$sql_data_po[$i-1][sum5])+($row[csf("sum6")]-$sql_data_po[$i-1][sum6])+($row[csf("sum7")]-$sql_data_po[$i-1][sum7])+($row[csf("sum8")]-$sql_data_po[$i-1][sum8])+($row[csf("sum9")]-$sql_data_po[$i-1][sum9])+($row[csf("sum10")]-$sql_data_po[$i-1][sum10])+($row[csf("sum11")]-$sql_data_po[$i-1][sum11])+($row[csf("sum12")]-$sql_data_po[$i-1][sum12]);
				?>
                <td align="right"><?  echo number_format($total_balance,0);?></td>
                <td align="right"><? //echo $row[csf("allocation_percentage")]; ?>%</td> 
            </tr>
            
            
            <?
				$i++;
				}
			
			?>
             
            <tr>
                <td colspan="3" ><strong>Allocate Capacity:</strong></td>
                <td align="right"><strong><? echo  number_format($total_sum1,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum2,0,'',','); ?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum3,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum4,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum5,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum6,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum7,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum8,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum9,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum10,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum11,0,'',',');;?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum12,0,'',',');?></strong></td>
                 <?
				$total_allocation=$total_sum1+$total_sum2+$total_sum3+$total_sum4+$total_sum5+$total_sum6+$total_sum7+$total_sum8+$total_sum9+$total_sum10+$total_sum11+$total_sum12;
				$total_order=$total_order_1+$total_order_2+$total_order_3+$total_order_4+$total_order_5+$total_order_6+$total_order_7+$total_order_8+$total_order_9+$total_order_10+$total_order_11+$total_order_12;
				?>
                <td align="right"><strong><?  echo number_format($total_allocation,0,'',',');?></strong></td>
               
                <td align="right"><strong><?  echo number_format($percent_tot,0,'',',');?></strong></td>
            </tr>
            <tr>
                <td colspan="3" ><strong>Order Qty.:</strong></td>
                <td align="right"><strong><? echo  number_format($total_order_1,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_order_2,0,'',','); ?></strong></td>
                <td align="right"><strong><? echo number_format($total_order_3,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_order_4,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_order_5,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_order_6,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_order_7,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_order_8,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_order_9,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_order_10,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_order_11,0,'',',');;?></strong></td>
                <td align="right"><strong><? echo number_format($total_order_12,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_order,0); ?></strong></td>
                <td align="right"><strong><? ?></strong></td>
            </tr>
            <tr>
                <td colspan="3" ><strong>Balance:</strong></td>
                <td align="right"><strong><? echo  number_format($balance_1,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($balance_2,0,'',','); ?></strong></td>
                <td align="right"><strong><? echo number_format($balance_3,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($balance_4,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($balance_5,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($balance_6,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($balance_7,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($balance_8,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($balance_9,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($balance_10,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($balance_11,0,'',',');;?></strong></td>
                <td align="right"><strong><? echo number_format($balance_12,0,'',',');?></strong></td>
                 <?
				$total_balance=$balance_1+$balance_2+$balance_3+$balance_4+$balance_5+$balance_6+$balance_7+$balance_8+$balance_9+$balance_10+$balance_11+$balance_12;
				?>
                <td align="right"><strong><? echo number_format($total_balance,0);?></strong></td>
                <td align="right"><strong><? ?></strong></td>
            </tr>
        </tbody>
    </table>
    <br/>
    <br/>
     <br/>
    <table align="right" cellspacing="0" width="1190px"  border="0"  >
    <tr>
    
    <td width="590">
    <strong>Company Wise Booking Status</strong>
    </td>
    <td width="10">
    </td>
    <td width="590">
    </td>
    </tr>
    <tr>
    <td width="590">
    <strong>Month: Jan</strong>
    <table width="100%" border="1" rules="all" class="rpt_table_qty_allocation">
    <tr>
    <td width="290" rowspan="4">Bar Chart Coming Soon</td>
    <td width="100">Capacity</td>
    <td width="100" align="right"><? echo number_format($total_sum1,0,'',','); ?></td>
    <td width="100" align="right">&nbsp;</td>
    </tr>
    <tr>
    
    <td width="100">Order Qty.</td>
    <td width="100" align="right"><? echo number_format($com_sum_b_1,0,'',','); ?></td>
    <td width="100" align="right">&nbsp;</td>
    </tr>
    <tr>
    
    <td width="100">Balance</td>
    <td width="100" align="right"><? echo number_format($total_sum1-$com_sum_b_1,0,'',','); ?></td>
    <td width="100" align="right">&nbsp;</td>
    </tr>
    <tr>
    <td colspan="3" align="center"><? if($total_sum1-$com_sum_b_1>0){ echo "Under Booking";} else{ echo "Over Booking";} ?></td>
    </tr>
    </table>
    </td>
    <td width="10">&nbsp;
    
    </td>
    <td width="590">
   <strong> Month: Fab</strong>
     <table width="100%" border="1" rules="all" class="rpt_table_qty_allocation">
    <tr>
    <td width="290" rowspan="4">Bar Chart Coming Soon</td>
    <td width="100">Capacity</td>
    <td width="100" align="right"><? echo number_format($total_sum2,0,'',','); ?></td>
    <td width="100" align="right">&nbsp;</td>
    </tr>
    <tr>
    
    <td width="100">Order Qty.</td>
    <td width="100" align="right"><? echo number_format($com_sum_b_2,0,'',','); ?></td>
    <td width="100" align="right">&nbsp;</td>
    </tr>
    <tr>
    
    <td width="100">Balance</td>
    <td width="100" align="right"><? echo number_format($total_sum2-$com_sum_b_2,0,'',','); ?></td>
    <td width="100" align="right">&nbsp;</td>
    </tr>
    <tr>
    <td colspan="3" align="center"><? if($total_sum1-$com_sum_b_1>0){ echo "Under Booking";} else{ echo "Over Booking";} ?></td>
    </tr>
    </table>
    </td>
    </tr>
    
     <tr>
    <td width="590">
    <strong>Month: Mar</strong>
    <table width="100%" border="1" rules="all" class="rpt_table_qty_allocation">
    <tr>
    <td width="290" rowspan="4">Bar Chart Coming Soon</td>
    <td width="100">Capacity</td>
    <td width="100" align="right"><? echo number_format($total_sum3,0,'',','); ?></td>
    <td width="100" align="right">&nbsp;</td>
    </tr>
    <tr>
    
    <td width="100">Order Qty.</td>
    <td width="100" align="right"><? echo number_format($com_sum_b_3,0,'',','); ?></td>
    <td width="100" align="right">&nbsp;</td>
    </tr>
    <tr>
    
    <td width="100">Balance</td>
    <td width="100" align="right"><? echo number_format($total_sum3-$com_sum_b_3,0,'',','); ?></td>
    <td width="100" align="right">&nbsp;</td>
    </tr>
    <tr>
    <td colspan="3" align="center"><? if($total_sum3-$com_sum_b_3>0){ echo "Under Booking";} else{ echo "Over Booking";} ?></td>
    </tr>
    </table>
    </td>
    <td width="10">&nbsp;
    
    </td>
    <td width="590">
   <strong> Month: Apr</strong>
     <table width="100%" border="1" rules="all" class="rpt_table_qty_allocation">
    <tr>
    <td width="290" rowspan="4">Bar Chart Coming Soon</td>
    <td width="100">Capacity</td>
    <td width="100" align="right"><? echo number_format($total_sum4,0,'',','); ?></td>
    <td width="100" align="right">&nbsp;</td>
    </tr>
    <tr>
    
    <td width="100">Order Qty.</td>
    <td width="100" align="right"><? echo number_format($com_sum_b_4,0,'',','); ?></td>
    <td width="100" align="right">&nbsp;</td>
    </tr>
    <tr>
    
    <td width="100">Balance</td>
    <td width="100" align="right"><? echo number_format($total_sum4-$com_sum_b_4,0,'',','); ?></td>
    <td width="100" align="right">&nbsp;</td>
    </tr>
    <tr>
    <td colspan="3" align="center"><? if($total_sum4-$com_sum_b_4>0){ echo "Under Booking";} else{ echo "Over Booking";} ?></td>
    </tr>
    </table>
    </td>
    </tr>
    
    <tr>
    <td width="590">
    <strong>Month: May</strong>
    <table width="100%" border="1" rules="all" class="rpt_table_qty_allocation">
    <tr>
    <td width="290" rowspan="4">Bar Chart Coming Soon</td>
    <td width="100">Capacity</td>
    <td width="100" align="right"><? echo number_format($total_sum5,0,'',','); ?></td>
    <td width="100" align="right">&nbsp;</td>
    </tr>
    <tr>
    
    <td width="100">Order Qty.</td>
    <td width="100" align="right"><? echo number_format($com_sum_b_5,0,'',','); ?></td>
    <td width="100" align="right">&nbsp;</td>
    </tr>
    <tr>
    
    <td width="100">Balance</td>
    <td width="100" align="right"><? echo number_format($total_sum5-$com_sum_b_5,0,'',','); ?></td>
    <td width="100" align="right">&nbsp;</td>
    </tr>
    <tr>
    <td colspan="3" align="center"><? if($total_sum5-$com_sum_b_5>0){ echo "Under Booking";} else{ echo "Over Booking";} ?></td>
    </tr>
    </table>
    </td>
    <td width="10">&nbsp;
    
    </td>
    <td width="590">
   <strong> Month: Jun</strong>
     <table width="100%" border="1" rules="all" class="rpt_table_qty_allocation">
    <tr>
    <td width="290" rowspan="4">Bar Chart Coming Soon</td>
    <td width="100">Capacity</td>
    <td width="100" align="right"><? echo number_format($total_sum6,0,'',','); ?></td>
    <td width="100" align="right">&nbsp;</td>
    </tr>
    <tr>
    
    <td width="100">Order Qty.</td>
    <td width="100" align="right"><? echo number_format($com_sum_b_6,0,'',','); ?></td>
    <td width="100" align="right">&nbsp;</td>
    </tr>
    <tr>
    
    <td width="100">Balance</td>
    <td width="100" align="right"><? echo number_format($total_sum6-$com_sum_b_6,0,'',','); ?></td>
    <td width="100" align="right">&nbsp;</td>
    </tr>
    <tr>
    <td colspan="3" align="center"><? if($total_sum6-$com_sum_b_6>0){ echo "Under Booking";} else{ echo "Over Booking";} ?></td>
    </tr>
    </table>
    </td>
    </tr>
    
    <tr>
    <td width="590">
    <strong>Month: Jul</strong>
    <table width="100%" border="1" rules="all" class="rpt_table_qty_allocation">
    <tr>
    <td width="290" rowspan="4">Bar Chart Coming Soon</td>
    <td width="100">Capacity</td>
    <td width="100" align="right"><? echo number_format($total_sum7,0,'',','); ?></td>
    <td width="100" align="right">&nbsp;</td>
    </tr>
    <tr>
    
    <td width="100">Order Qty.</td>
    <td width="100" align="right"><? echo number_format($com_sum_b_7,0,'',','); ?></td>
    <td width="100" align="right">&nbsp;</td>
    </tr>
    <tr>
    
    <td width="100">Balance</td>
    <td width="100" align="right"><? echo number_format($total_sum7-$com_sum_b_7,0,'',','); ?></td>
    <td width="100" align="right">&nbsp;</td>
    </tr>
    <tr>
    <td colspan="3" align="center"><? if($total_sum7-$com_sum_b_7>0){ echo "Under Booking";} else{ echo "Over Booking";} ?></td>
    </tr>
    </table>
    </td>
    <td width="10">&nbsp;
    
    </td>
    <td width="590">
   <strong> Month: Aug</strong>
     <table width="100%" border="1" rules="all" class="rpt_table_qty_allocation">
    <tr>
    <td width="290" rowspan="4">Bar Chart Coming Soon</td>
    <td width="100">Capacity</td>
    <td width="100" align="right"><? echo number_format($total_sum8,0,'',','); ?></td>
    <td width="100" align="right">&nbsp;</td>
    </tr>
    <tr>
    
    <td width="100">Order Qty.</td>
    <td width="100" align="right"><? echo number_format($com_sum_b_8,0,'',','); ?></td>
    <td width="100" align="right">&nbsp;</td>
    </tr>
    <tr>
    
    <td width="100">Balance</td>
    <td width="100" align="right"><? echo number_format($total_sum8-$com_sum_b_8,0,'',','); ?></td>
    <td width="100" align="right">&nbsp;</td>
    </tr>
    <tr>
    <td colspan="3" align="center"><? if($total_sum8-$com_sum_b_8>0){ echo "Under Booking";} else{ echo "Over Booking";} ?></td>
    </tr>
    </table>
    </td>
    </tr>
    
    
    <tr>
    <td width="590">
    <strong>Month: Sep</strong>
    <table width="100%" border="1" rules="all" class="rpt_table_qty_allocation">
    <tr>
    <td width="290" rowspan="4">Bar Chart Coming Soon</td>
    <td width="100">Capacity</td>
    <td width="100" align="right"><? echo number_format($total_sum9,0,'',','); ?></td>
    <td width="100" align="right">&nbsp;</td>
    </tr>
    <tr>
    
    <td width="100">Order Qty.</td>
    <td width="100" align="right"><? echo number_format($com_sum_b_9,0,'',','); ?></td>
    <td width="100" align="right">&nbsp;</td>
    </tr>
    <tr>
    
    <td width="100">Balance</td>
    <td width="100" align="right"><? echo number_format($total_sum9-$com_sum_b_9,0,'',','); ?></td>
    <td width="100" align="right">&nbsp;</td>
    </tr>
    <tr>
    <td colspan="3" align="center"><? if($total_sum9-$com_sum_b_9>0){ echo "Under Booking";} else{ echo "Over Booking";} ?></td>
    </tr>
    </table>
    </td>
    <td width="10">&nbsp;
    
    </td>
    <td width="590">
   <strong> Month: Oct</strong>
     <table width="100%" border="1" rules="all" class="rpt_table_qty_allocation">
    <tr>
    <td width="290" rowspan="4">Bar Chart Coming Soon</td>
    <td width="100">Capacity</td>
    <td width="100" align="right"><? echo number_format($total_sum10,0,'',','); ?></td>
    <td width="100" align="right">&nbsp;</td>
    </tr>
    <tr>
    
    <td width="100">Order Qty.</td>
    <td width="100" align="right"><? echo number_format($com_sum_b_10,0,'',','); ?></td>
    <td width="100" align="right">&nbsp;</td>
    </tr>
    <tr>
    
    <td width="100">Balance</td>
    <td width="100" align="right"><? echo number_format($total_sum10-$com_sum_b_10,0,'',','); ?></td>
    <td width="100" align="right">&nbsp;</td>
    </tr>
    <tr>
    <td colspan="3" align="center"><? if($total_sum10-$com_sum_b_10>0){ echo "Under Booking";} else{ echo "Over Booking";} ?></td>
    </tr>
    </table>
    </td>
    </tr>
    
    
    <tr>
    <td width="590">
    <strong>Month: Nov</strong>
    <table width="100%" border="1" rules="all" class="rpt_table_qty_allocation">
    <tr>
    <td width="290" rowspan="4">Bar Chart Coming Soon</td>
    <td width="100">Capacity</td>
    <td width="100" align="right"><? echo number_format($total_sum11,0,'',','); ?></td>
    <td width="100" align="right">&nbsp;</td>
    </tr>
    <tr>
    
    <td width="100">Order Qty.</td>
    <td width="100" align="right"><? echo number_format($com_sum_b_11,0,'',','); ?></td>
    <td width="100" align="right">&nbsp;</td>
    </tr>
    <tr>
    
    <td width="100">Balance</td>
    <td width="100" align="right"><? echo number_format($total_sum11-$com_sum_b_11,0,'',','); ?></td>
    <td width="100" align="right">&nbsp;</td>
    </tr>
    <tr>
    <td colspan="3" align="center"><? if($total_sum11-$com_sum_b_11>0){ echo "Under Booking";} else{ echo "Over Booking";} ?></td>
    </tr>
    </table>
    </td>
    <td width="10">&nbsp;
    
    </td>
    <td width="590">
   <strong> Month: Dec</strong>
     <table width="100%" border="1" rules="all" class="rpt_table_qty_allocation">
    <tr>
    <td width="290" rowspan="4">Bar Chart Coming Soon</td>
    <td width="100">Capacity</td>
    <td width="100" align="right"><? echo number_format($total_sum12,0,'',','); ?></td>
    <td width="100" align="right">&nbsp;</td>
    </tr>
    <tr>
    
    <td width="100">Order Qty.</td>
    <td width="100" align="right"><? echo number_format($com_sum_b_12,0,'',','); ?></td>
    <td width="100" align="right">&nbsp;</td>
    </tr>
    <tr>
    
    <td width="100">Balance</td>
    <td width="100" align="right"><? echo number_format($total_sum12-$com_sum_b_12,0,'',','); ?></td>
    <td width="100" align="right">&nbsp;</td>
    </tr>
    <tr>
    <td colspan="3" align="center"><? if($total_sum12-$com_sum_b_12>0){ echo "Under Booking";} else{ echo "Over Booking";} ?></td>
    </tr>
    </table>
    </td>
    </tr>
    
    </table>
</div>
<?
}

?>
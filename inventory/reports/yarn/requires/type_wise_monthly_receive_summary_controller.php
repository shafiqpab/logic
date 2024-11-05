<?
/*-------------------------------------------- Comments
Version                  :   V1
Purpose			         : 	This form will create  MType Wise Monthly Receive Summary Report
Functionality	         :	
JS Functions	         :
Created by		         :	Md.mahbubur Rahman
Creation date 	         :  01-08-2018
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
Comments		         : 
*/


include('../../../../includes/common.php');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');


if($action=="report_generate")
{

	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_year_name=str_replace("'","",$cbo_year_name);
	$cbo_month=str_replace("'","",$cbo_month);
	$cbo_month_end=str_replace("'","",$cbo_month_end);
	$cbo_end_year_name=str_replace("'","",$cbo_end_year_name);
	$cbo_dyed_type=str_replace("'","",$cbo_dyed_type);
	$cbo_yarn_type=str_replace("'","",$cbo_yarn_type);
	$type=str_replace("'","",$rptType);
	//echo $type; die;

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
	
	
			if ($cbo_dyed_type >0)
          	{
          		$search_cond .= " and b.dyed_type=$cbo_dyed_type";
          	}
          	if ($cbo_yarn_type > 0)
          	{
          		$search_cond .= " and b.yarn_type in ($cbo_yarn_type)";
          	}
			
			if ($cbo_company_name > 0)
          	{
          		$search_cond .= " and a.company_id=$cbo_company_name";
          	}

	ob_start();

	if($type==1)
	{
	

		$sql="select b.id as prod_id,a.transaction_date,
				case when a.transaction_type in(1) then a.cons_quantity else 0 end as receive_qty ,b.yarn_type,a.cons_amount from
				inv_transaction a, product_details_master b
				where a.prod_id=b.id and a.item_category=1 $search_cond  and a.transaction_type in(1) and a.status_active=1 and a.is_deleted=0 and a.transaction_date between '$s_date' and '$e_date'";


	$cpAvgRateArray=sql_select($sql);
	
	$typeWiseYarnRecQtyArr=array();
	foreach( $cpAvgRateArray as $row)
	{
		$date_key=date("Y-m",strtotime($row[csf("transaction_date")]));
		$typeWiseYarnRecQtyArr[$row[csf("yarn_type")]][$date_key] += $row[csf("receive_qty")];
		//$typeWiseYarnRecamountArr[$row[csf("yarn_type")]][$date_key] += $row[csf("cons_amount")];
		$typeWiseYarnRecamountArr[$row[csf("yarn_type")]] += $row[csf("cons_amount")];
		$custom_yarn_type_arr[$row[csf("yarn_type")]] = $row[csf("yarn_type")];
		$monthWiseYarnRecQty[$date_key] += $row[csf("receive_qty")];
	}
	
//var_dump($typeWiseYarnRecQtyArr);


$width=($tot_month*75)+($tot_month+495);
$bgcolor1="#FFFFFF";
$bgcolor2="#E9F3FF";
 $totalAmount=0;
?>    
<div style="width:<? echo $width;?>px; overflow:hidden; margin:10px 0; height:auto;">
    
    <table width="<? echo $width;?>" cellspacing="0" border="1" rules="all" class="rpt_table">
      <thead>
            <th style="text-align:left;">Receive Summary</th>
        </thead>
    </table>   
    
    <table align="right" cellspacing="0" width="<? echo $width;?>"  border="1" rules="all" class="rpt_table" id="tbl_month_pce" >
        <thead>
            <tr>
                <th width="120">Particulars</th>
                <? foreach($month_arr as $month_id):?>
					<th width="75"><? list($y,$m)=explode('-',$month_id); $m=$m*1; echo $months[$m].' '.$y; ?></th>
				<? endforeach; ?>
                <th rowspan="2" width="100">Total</th>
                <th rowspan="2" width="100">Total AVG Rate(tk)</th> 
                <th rowspan="2">Total Sum of Amount(tk)</th> 
              </tr>
            <tr>
                <th width="120">Yarn Type</th>
                <? foreach($month_arr as $month_id):?>
                <th width="75">Receive Qty</th>
                <? endforeach;?>
              </tr>
        </thead>
        <tbody>
            <? 
			$p=1; 
			foreach($custom_yarn_type_arr as $yarn_type_id=>$yarn_type_id){?>
            <tr onclick="change_color('tr1st_<? echo $p;?>','<? echo $bgcolor1; ?>')" id="tr1st_<? echo $p;$p++?>">
                <td><? echo $yarn_type[$yarn_type_id];?></td>
			   <? foreach($month_arr as $month_id){?>
                <td align="right"><? echo  $typeWiseYarnRecQtyArr[$yarn_type_id][$month_id]; ?></td>
                <? } ?> 
                <td align="right"><? echo number_format(array_sum($typeWiseYarnRecQtyArr[$yarn_type_id]),0);?></td>
                <td align="right"><? echo number_format($typeWiseYarnRecamountArr[$yarn_type_id]/array_sum($typeWiseYarnRecQtyArr[$yarn_type_id]),2); ?></td>
                <td align="right"><? echo  $typeWiseYarnRecamountArr[$yarn_type_id]; $totalAmount+=$typeWiseYarnRecamountArr[$yarn_type_id]; ?></td>
            </tr>
           <? } ?> 

         </tbody>
         <tfoot>
            <tr bgcolor="<? echo $bgcolor ; ?>">
                <th align="left">Total:</th>
               <? foreach($month_arr as $month_id):?>
                <th align="right"><? echo $monthWiseYarnRecQty[$month_id]; ?></th>
                <? endforeach;?> 
                <th align="right"><? echo array_sum($monthWiseYarnRecQty);?></th>
                <th align="right"><? echo number_format($totalAmount/array_sum($monthWiseYarnRecQty),2);?></th>
                <th align="right"><? echo number_format($totalAmount,2);?></th>
            </tr>
        </tfoot>
     </table>
 <!--Company Summary End....................................................... -->
</div>
    <?
	}
	if($type==2)
	{
	

		$sql="select b.id as prod_id,a.transaction_date,
							case when a.transaction_type in(2) then a.cons_quantity else 0 end as issue_qty ,b.yarn_type,a.cons_amount from
							inv_transaction a, product_details_master b
						where
							a.prod_id=b.id and a.item_category=1 $search_cond  and a.transaction_type in(2) and a.status_active=1 and a.is_deleted=0 and a.transaction_date between '$s_date' and '$e_date'";


	$cpAvgRateArray=sql_select($sql);
	
	$typeWiseYarnRecQtyArr=array();
	foreach( $cpAvgRateArray as $row)
	{
		$date_key=date("Y-m",strtotime($row[csf("transaction_date")]));
		$typeWiseYarnissueQtyArr[$row[csf("yarn_type")]][$date_key] += $row[csf("issue_qty")];
		$custom_yarn_type_arr[$row[csf("yarn_type")]] = $row[csf("yarn_type")];
		
		$typeWiseYarnissueQtyArr[$date_key] += $row[csf("issue_qty")];
	}
	
//var_dump($typeWiseYarnRecQtyArr);


$width=($tot_month*75)+($tot_month+495);
$bgcolor1="#FFFFFF";
$bgcolor2="#E9F3FF";
?>    
<div style="width:<? echo $width;?>px; overflow:hidden; margin:10px 0; height:auto;">
    
    <table width="<? echo $width;?>" cellspacing="0" border="1" rules="all" class="rpt_table">
      <thead>
            <th style="text-align:left;">Issue Summary</th>
        </thead>
    </table>   
    
    <table align="right" cellspacing="0" width="<? echo $width;?>"  border="1" rules="all" class="rpt_table" id="tbl_month_pce" >
        <thead>
            <tr>
                <th width="120">Particulars</th>
                <? foreach($month_arr as $month_id):?>
					<th width="75"><? list($y,$m)=explode('-',$month_id); $m=$m*1; echo $months[$m].' '.$y; ?></th>
				<? endforeach; ?>
                <th rowspan="2" width="100">Total</th>
              </tr>
            <tr>
                <th width="120">Yarn Type</th>
                <? foreach($month_arr as $month_id):?>
                <th width="75">Issue Qty</th>
                <? endforeach;?>
              </tr>
        </thead>
        <tbody>
            <?
            $p=1;  
			
			foreach($custom_yarn_type_arr as $yarn_type_id=>$yarn_type_id){?>
            <tr onclick="change_color('tr1st_<? echo $p;?>','<? echo $bgcolor1; ?>')" id="tr1st_<? echo $p;$p++;?>">
                <td><? echo $yarn_type[$yarn_type_id];?></td>
			   <? foreach($month_arr as $month_id){?>
                <td align="right"><? echo  $typeWiseYarnissueQtyArr[$yarn_type_id][$month_id]; ?></td>
                <? } ?> 
                <td align="right"><? echo number_format(array_sum($typeWiseYarnissueQtyArr[$yarn_type_id]),0);?></td>
            </tr>
           <? } ?> 

         </tbody>
         <tfoot>
         
         <tr bgcolor="<? echo $bgcolor ; ?>">
                <th align="left">Total:</th>
               <? foreach($month_arr as $month_id):?>
                <th align="right"><? echo $typeWiseYarnissueQtyArr[$month_id]; ?></th>
                <? endforeach;?> 
                <th align="right"><? echo array_sum($typeWiseYarnissueQtyArr);?></th>
            </tr>
           
        </tfoot>
     </table>
 <!--Company Summary End....................................................... -->
</div>
    <?
	}
	if($type==3)
	{
	

		$sql="select b.id as prod_id,a.transaction_date,
							case when a.transaction_type in(1) then a.cons_quantity else 0 end as receive_qty ,
							case when a.transaction_type in(2) then a.cons_quantity else 0 end as issue_qty ,
							b.yarn_type,a.cons_amount from
							inv_transaction a, product_details_master b where
							a.prod_id=b.id and a.item_category=1 $search_cond and a.transaction_type in(1,2) and a.status_active=1 and a.is_deleted=0 and a.transaction_date between '$s_date' and '$e_date'";


	$cpAvgRateArray=sql_select($sql);
	
	$typeWiseYarnRecQtyArr=array();
	foreach( $cpAvgRateArray as $row)
	{
		$date_key=date("Y-m",strtotime($row[csf("transaction_date")]));
		$typeWiseYarnRecQtyArr[$row[csf("yarn_type")]][$date_key] += $row[csf("receive_qty")];
		$typeWiseYarnissueqtyArr[$row[csf("yarn_type")]][$date_key] += $row[csf("issue_qty")];
		$custom_yarn_type_arr[$row[csf("yarn_type")]] = $row[csf("yarn_type")];
		$typeWiseYarnRecQtyArr[$date_key] += $row[csf("receive_qty")];
		$typeWiseYarnissueqtyArr[$date_key] += $row[csf("issue_qty")];
	}
	
//var_dump($typeWiseYarnRecQtyArr);


$width=($tot_month*75)+($tot_month+495);
$bgcolor1="#FFFFFF";
$bgcolor2="#E9F3FF";
?>    
<div style="width:<? echo $width;?>px; overflow:hidden; margin:10px 0; height:auto;">
    
    <table width="<? echo $width;?>" cellspacing="0" border="1" rules="all" class="rpt_table">
      <thead>
            <th style="text-align:left;">Receive and Issue Summary</th>
        </thead>
    </table>   
    
    <table align="right" cellspacing="0" width="<? echo $width;?>"  border="1" rules="all" class="rpt_table" id="tbl_month_pce" >
        <thead>
            <tr>
                <th width="120">Particulars</th>
                <? foreach($month_arr as $month_id):?>
					<th  width="75" colspan="2"><? list($y,$m)=explode('-',$month_id); $m=$m*1; echo $months[$m].' '.$y; ?></th>
				<? endforeach; ?>
              </tr>
            <tr>
                <th width="120">Yarn Type</th>
                <? foreach($month_arr as $month_id):?>
                <th width="75">Receive Qty</th>
                <th width="75">Issue Qty</th>
                <? endforeach;?>
              </tr>
        </thead>
        <tbody>
            <? 
			$p=1;
			foreach($custom_yarn_type_arr as $yarn_type_id=>$yarn_type_id){?>
            <tr onclick="change_color('tr1st_<? echo $p;?>','<? echo $bgcolor1; ?>')" id="tr1st_<? echo $p;$p++;?>">
                <td><? echo $yarn_type[$yarn_type_id];?></td>
			   <? foreach($month_arr as $month_id){?>
                <td align="right"><? echo  $typeWiseYarnRecQtyArr[$yarn_type_id][$month_id]; ?></td>
                <td align="right"><? echo  $typeWiseYarnissueqtyArr[$yarn_type_id][$month_id]; ?></td>
                <? } ?> 
            </tr>
           <? } ?> 

         </tbody>
         <tfoot>
            <tr bgcolor="<? echo $bgcolor ; ?>">
                <th align="left">Total:</th>
               <? foreach($month_arr as $month_id):?>
                <th align="right"><? echo $typeWiseYarnRecQtyArr[$month_id]; ?></th>
                <th align="right"><? echo $typeWiseYarnissueqtyArr[$month_id]; ?></th>
                <? endforeach;?> 
            </tr>
        </tfoot>
     </table>

</div>
    <?
	}
	
	
	
	$html = ob_get_contents();
	ob_clean();
			//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
	foreach (glob("*.xls") as $filename) {
				//if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
			//---------end------------//
	$name = time();
	$filename = $user_id . "_" . $name . ".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html##$filename";
	exit();
	
	
	
	
	
}

?>
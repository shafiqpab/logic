<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create AOP Capacity Plan Report.
Functionality	:	
JS Functions	:
Created by		:	Md Mahbubur Rahman
Creation date 	: 	18-09-2023
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

include('../../../includes/common.php');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');

if ($action == "load_drop_machine")
	{
		//$data=explode('_',$data);
		//$company_id = $data;
 		echo create_drop_down("cboMachineName", 100, "select id, machine_no as machine_name from lib_machine_name where category_id=33 and company_id=$data and status_active=1 and is_deleted=0 order by seq_no", "id,machine_name", 1, "-- Select Machine --", 0, "","","","","","","","","cboMachineName[]");
 		exit();
	}
$machine_name_arr=return_library_array( "select id, machine_no from lib_machine_name  where  category_id=33  and status_active=1 and is_deleted=0 order by seq_no",'id','machine_no');

 

if($action=="report_generate")
{

	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_year_name=str_replace("'","",$cbo_year_name);
	$cbo_month=str_replace("'","",$cbo_month);
	$cbo_month_end=str_replace("'","",$cbo_month_end);
	$cbo_end_year_name=str_replace("'","",$cbo_end_year_name);
 	$cboMachineName=str_replace("'","",$cboMachineName);
	$cboapacityType=str_replace("'","",$cboapacityType);

  if( $cboMachineName!=0 )  $machine_name=" and b.machine_id='$cboMachineName'"; else $machine_name="";
   
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
	
  
	 $sql_con_po="SELECT a.subcon_job as job_no,a.company_id as company_name,b.machine_id as machine_id ,b.delivery_date as delivery_date,b.order_quantity as order_quantity 
	FROM subcon_ord_mst a,subcon_ord_dtls b 
	WHERE a.id=b.mst_id and   a.company_id=$cbo_company_name AND b.delivery_date between '$s_date' and '$e_date' and b.machine_id>0  and a.entry_form=278 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $machine_name   order by b.machine_id";
 

	$po_qnty_array=array(); $order_qnty_array=array();
	$sql_data_po=sql_select($sql_con_po);
	foreach( $sql_data_po as $row_po)
	{
		
		$date_key=date("Y-m",strtotime($row_po[csf("delivery_date")]));
		$year_key=date("Y",strtotime($row_po[csf("delivery_date")]));
		$com_sum[$date_key]+=$row_po[csf("order_quantity")];
		$com_sum_total+=$row_po[csf("order_quantity")];
		$po_qnty_array[$row_po[csf("machine_id")]][$date_key]+=$row_po[csf("order_quantity")];
 		$order_qnty_array[$date_key]+=$row_po[csf("order_quantity")];
 	}
	
	
 
	if( $cboMachineName!=0 )  $machine_id=" and  id='$cboMachineName'"; else $machine_id="";

	$sql_capa="select id, prod_capacity as prod_capacity from lib_machine_name where category_id=33 and company_id=$cbo_company_name and status_active=1 and is_deleted=0 $machine_id order by seq_no";
	$sql_data_capa=sql_select($sql_capa);
	foreach( $sql_data_capa as $row)
	{
		//$date_key=date("Y-m",strtotime($row[csf("year")].'-'.$row[csf("month_id")]));
		$com_month_capa_arr[$row[csf("id")]]+=$row[csf("prod_capacity")];	
		$tot_com_month_capacity+=$row[csf("prod_capacity")];	
	}
	
	
	
	
$width=($tot_month*75)+($tot_month+395);
$bgcolor1="#FFFFFF";
$bgcolor2="#E9F3FF";
?>    


<?
$width=($tot_month*150)+($tot_month+465);
?>    
	
    <table width="<? echo $width;?>" cellspacing="0" border="1" rules="all" class="rpt_table">
      <thead>
        <tr>
            <th style="text-align:left;">AOP Capacity Plan Report</th>
        </tr>
        </thead>
    </table> 
      
    <table cellspacing="0" width="<? echo $width;?>px"  border="1" rules="all" class="rpt_table" >
        <thead  align="center">
        <tr>
            <th width="40" rowspan="3">SL</th>
            <th width="60" rowspan="3" align="center">Machine No.</th>
            <th width="90"  rowspan="3" align="center">Machine Capacity Monthly</th>
            <? foreach($month_arr as $month_id):?>
            <th  colspan="2"  align="center"><? list($y,$m)=explode('-',$month_id); $m=$m*1; echo  $y; ?></th>
            <? endforeach;?>
            <th   align="center" rowspan="3">Varience Total</th>
         </tr> 
         <tr>
            <? foreach($month_arr as $month_id):?>
            <th  colspan="2" align="center"><? list($y,$m)=explode('-',$month_id); $m=$m*1; echo $months[$m]; ?></th>
            <? endforeach;?>
         </tr>  
         <tr>
             <? foreach($month_arr as $month_id):?>
                <th    width="75"   align="center">Booked</th>
                 <th    width="75"   align="center">Varience</th>
             <? endforeach;?>
         </tr>         
        </thead>
     </table>
     
 <div style=" max-height:400px; overflow-y:scroll; width:<? echo $width;?>px"  align="left" id="scroll_body">
     <table align="right" cellspacing="0" width="100%"  border="1" rules="all" class="rpt_table" >
        <tbody>
<?
		$i=1;$month_capa_variance_qty=0;
		foreach($po_qnty_array as $machin_id=>$row)
		{
			$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";  
		?>
            <tr bgcolor="<? echo $bgcolor ; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td  width="40"><? echo $i; ?></td>
                <td align="left"  width="60"><p><? echo $machine_name_arr[$machin_id]; ?></p></td>
                <td align="left" width="90"><? echo $com_month_capa_arr[$machin_id]; ?></td>
               <? foreach($month_arr as $month_id):?>
                <td align="right" width="75" > <? echo number_format($po_qnty_array[$machin_id][$month_id],0,'.',''); ?></td>
                <td align="right" width="75">  <? echo $month_capa_variance_qty=$com_month_capa_arr[$machin_id]-number_format($po_qnty_array[$machin_id][$month_id],0,'.',''); ?></td>
 			   <? $tot_cap_qty_b[$machin_id]+=$month_capa_variance_qty; ?>
               <? endforeach;?> 
                <td align="right"  ><? echo number_format($tot_cap_qty_b[$machin_id],0,'.','');?></td>
                
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
               <th width="192">Total:</th>
               <? foreach($month_arr as $month_id):?>
                 <th align="right" width="75"><? echo number_format($order_qnty_array[$month_id],0,'.','');?></th>
                 <th align="right" width="75"><? echo number_format($tot_com_month_capacity-$order_qnty_array[$month_id],0,'.','');?></th>
              <? $grand_to_varience_qty+=number_format($tot_com_month_capacity-$order_qnty_array[$month_id],0,'.','');;?> 
               <? endforeach;?> 
                <th align="right"  ><? echo number_format($grand_to_varience_qty,0,'.',''); ?></th>
                 <th width="13">&nbsp;</th>
            </tr>
            
        </tfoot>
    </table>
  
 <!--Buyer wise comparison of basic quantity End.............................. -->
    
    <br/>
     <?
	
}














 

?>
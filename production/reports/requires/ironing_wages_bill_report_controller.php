<? 
header('Content-type:text/html; charset=utf-8');
session_start();

if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$user_id = $_SESSION['logic_erp']['user_id'];

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 110, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select --", $selected, "",0 );     	 
}


 
if($action=="report_generate")
{ 
	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_lib=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$company_short_lib=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );
 	$buyer_lib=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
 	$location_lib=return_library_array( "select id,location_name from lib_location", "id", "location_name"  ); 

	$po_number_arr = return_library_array("select id, po_number from wo_po_break_down","id","po_number");
	$po_quantity_arr = return_library_array("select id, po_quantity from wo_po_break_down","id","po_quantity");



	$from_date=str_replace("'","",trim($txt_date_from));
	$to_date=str_replace("'","",trim($txt_date_to));
	$cbo_location=str_replace("'","",trim($cbo_location));


	if(str_replace("'","",$cbo_company_name)==0){
		$company_name="";
		$production_company="";
	}
	else{
		$company_name=" and a.company_id=$cbo_company_name";
		$production_company=" and serving_company=$cbo_company_name";
	}
		
	if($cbo_location==0){
		$location_con="";
		$production_location="";
	}
	else{
		$location_con=" and a.location=$cbo_location";
		$production_location=" and location=$cbo_location";
	}
	
	if(str_replace("'","",$cbo_division)==0)$division_con="";else $division_con=" and a.division_id=$cbo_division";
	if(str_replace("'","",$cbo_department)==0)$department_con="";else $department_con=" and a.department_id=$cbo_department";
	if(str_replace("'","",$cbo_shift)==0)$shift_con="";else $shift_con=" and a.shift=$cbo_shift";
	
	
	if($from_date!='' && $to_date!=''){
		if($db_type==0){
			
			$from_date=change_date_format($from_date);
			$to_date=change_date_format($to_date);
		}
		else{
			$from_date=change_date_format($from_date,'','',-1);
			$to_date=change_date_format($to_date,'','',-1);
		}
		//$date_con_from="and a.week_from_date BETWEEN '$from_date' and '$to_date'";
		//$date_con_to="and a.week_to_date BETWEEN '$from_date' and '$to_date'";
		$date_con=" and a.week_from_date >= '$from_date' and a.week_to_date <= '$to_date'";

		$production_date_con="and production_date between '$from_date' and  '$to_date'";
	}
	else{
		$date_con_from="";	
		$date_con_to="";	
	}
	
	
$sql="
	select
		a.company_id,
		a.location,
		a.final_bill,
		b.emp_id,
		b.emp_name,
		b.buyer_id,
		b.designation,
		b.grade,
		b.salary,
		b.buyer_id,
		c.order_id,
		c.gmt_item_id,
		b.rate_variables,
		b.deducted_qty,
		b.deducted_emp,
		c.approved_wo_qty ,
		c.previous_bill_qty,
		c.bill_qty,
		b.wo_rate,
		c.amount
	from 
		pro_weekly_wages_bill_mst a,
		pro_weekly_wages_bill_dtls b,
		pro_weekly_wages_order_brk c
	where
		a.id=b.mst_id
		and b.id=c.weekly_wages_dtls_id
		and b.mst_id=c.weekly_wages_mst_id
		and a.bill_for=35
		and a.status_active=1
		and a.is_deleted=0
		and b.status_active=1
		and b.is_deleted=0
		and c.status_active=1
		and c.is_deleted=0
		$company_name
		$location_con
		
		$department_con
		$shift_con
		$date_con
		";//$date_con_from $date_con_to$division_con	
	
$result = sql_select($sql);
foreach($result as $row){
	$key=$row[csf('order_id')].$row[csf('company_id')].$row[csf('location')].$row[csf('gmt_item_id')];
	$data_arr[]=array(
		'key'=>$key,
		'emp_id'=>$row[csf('emp_id')],
		'emp_name'=>$row[csf('emp_name')],
		'order_id'=>$row[csf('order_id')],
		'buyer_id'=>$row[csf('buyer_id')],
		'rate_variables'=>$row[csf('rate_variables')],
		'gmt_item_id'=>$row[csf('gmt_item_id')],
		'wo_qty'=>$row[csf('approved_wo_qty')],
		'previous_bill_qty'=>$row[csf('previous_bill_qty')],
		'bill_qty'=>$row[csf('bill_qty')],
		'bill_safty_qty'=>$row[csf('bill_safty_qty')],
		'bill_safty_rate'=>$row[csf('bill_safty_rate')],
		'bill_safty_amount'=>$row[csf('bill_safty_amount')],
		'final_bill'=>$row[csf('final_bill')],
		'wo_rate'=>$row[csf('wo_rate')],
		'amount'=>$row[csf('amount')],
		'deducted_emp'=>$row[csf('deducted_emp')]

	);	
	$po_id.=$row[csf('order_id')].',';
}
//var_dump($data_arr);


//for previous deduct by order.........................
$po_id=rtrim($po_id,',');
 $sql="SELECT c.order_id,c.bill_qty,c.bill_safty_qty ,c.bill_safty_amount  FROM pro_weekly_wages_bill_mst a, pro_weekly_wages_bill_dtls b,pro_weekly_wages_order_brk c WHERE a.id=b.mst_id and a.id=c.weekly_wages_mst_id and b.id=weekly_wages_dtls_id and c.order_id in($po_id) and a.bill_for=35 $company_name $location_con $division_con $department_con $shift_con and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
 $data_array=sql_select($sql);
	foreach ($data_array as $row)
	{  
		$total_previous_deducted_qty[$row[csf("order_id")]]+=$row[csf("bill_safty_qty")];
		$total_previous_deducted_amount[$row[csf("order_id")]]+=$row[csf("bill_safty_amount")];
	}




$sql="select production_quantity,po_break_down_id,serving_company,location,item_number_id from  pro_garments_production_mst where production_type = 7 $production_company $production_location $production_date_con and status_active=1 and is_deleted=0";
$production_result = sql_select($sql);
	
foreach($production_result as $row){
	$key=$row[csf('po_break_down_id')].$row[csf('serving_company')].$row[csf('location')].$row[csf('item_number_id')];
	$pro_qty_arr[$key]+=$row[csf('production_quantity')];
}
	
	
ob_start();	
?>


<div style="width:1315px;">
<table width="100%">
    <tr>
        <th colspan="15">
            <strong style="font-size:20px; line-height:18px;">Company Name: <?php echo $company_lib[str_replace("'",'',$cbo_company_name)];  ?></strong>
            <h2 style="font-size:25px; line-height:22px;">Ironing Wages Bill</h2>
        </th>
    </tr>
    <tr>
        <th style="text-align:left;" colspan="8">
            Unit Name : <?php echo $location_lib[$cbo_location];  ?>
        </th>
        <th style="text-align:right;" colspan="7">
            Bill Date : <?php echo change_date_format($from_date); ?> To <?php echo change_date_format($to_date); ?>
        </th>
    </tr>
</table>
</div>

<div style="width:1315px;">
<table width="100%" border="1" class="rpt_table" rules="all">
    <thead>
        <tr>
            <th width="35">SL</th>
            <th width="60">ID No</th>
            <th width="110">Service Provider</th>
            <th width="100">Buyer</th>
            <th width="90">Order No</th>
            <th width="90"> Rate Variable</th>
            <th width="110">Gmts. Name</th>
            <th width="75">Order Qnty.(Pcs)</th>
            <th width="75">WO Qnty.(Dzn)</th>
            <th width="85">Previous Bill Qnty.(Dzn)</th>
            <th width="90">Production Qnty.(Pcs)</th>
            <th width="75">Bill Qnty.(Dzn)</th>
            <th width="50">WO Rate</th>
            <th width="80">Amount(Tk)</th>
            <th>Signature</th>
        </tr>
     </thead>
</table>    
</div>	
<div style="width:1332px; max-height:300px; overflow-y:auto;" id="scroll_body">
<table width="1312" border="1" class="rpt_table" rules="all" id="table_body">
    <tbody>
        <?
		$i=1;
		foreach($data_arr as $rows){
         $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
			$tot_po_qty+=$po_quantity_arr[$rows['order_id']];
			$tot_wo_qty+=$rows['wo_qty'];
			$tot_prev_bill_qty+=$rows['previous_bill_qty'];
			$tot_pro_qty+=$pro_qty_arr[$rows['key']];
			$deducted_emp+=$rows['deducted_emp'];
			
			$tot_bill_qty+=$rows['bill_qty'];
			$tot_amount+=round($rows['wo_rate']*$rows['bill_qty']);
		?>
        <tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $i;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer;">
            <td width="35" align="center"><? echo $i;?></td>
            <td width="60" align="center"><? echo $rows['emp_id'];?></td>
            <td width="110"><? echo $rows['emp_name'];?></td>
            <td width="100"><? echo $buyer_lib[$rows['buyer_id']];?></td>
            <td width="90"><p><? echo $po_number_arr[$rows['order_id']];?></p></td>
            <td width="90" align="center"><? echo $color_type[$rows['rate_variables']];?></td>
            <td width="110"><? echo $garments_item[$rows['gmt_item_id']];?></td>
            <td width="75" align="right"><? echo $po_quantity_arr[$rows['order_id']];?></td>
            <td width="75" align="right"><? echo $rows['wo_qty'];?></td>
            <td width="85" align="right"><? echo round($rows['previous_bill_qty']);?></td>
            <td width="90" align="right"><? echo $pro_qty_arr[$rows['key']];?></td>
            <td width="75" align="right"><?  echo round($rows['bill_qty']);?></td>
            <td width="50" align="right"><? echo $rows['wo_rate'];?></td>
            <td width="80" align="right"><? echo round($rows['wo_rate']*$rows['bill_qty']);?></td>
            <td></td>
        </tr>
       <?
	   $i++; 
	   } 
	   ?> 
     </tbody>
</table>    
</div>	
<div style="width:1315px;">
<table width="100%" border="1" class="rpt_table" rules="all">
    <tfoot>
       <tr>
            <th width="35"></th>
            <th width="60"></th>
            <th width="110"></th>
            <th width="100"></th>
            <th width="90"></th>
            <th width="90"></th>
            <th width="110"></th>
            <th width="75" id="tot_po_qty"><? echo $tot_po_qty;?></th>
            <th width="75" id="tot_wo_qty"><? echo $tot_wo_qty;?></th>
            <th width="85" id="tot_prev_bill_qty"><? echo $tot_prev_bill_qty;?></th>
            <th width="90" id="tot_pro_qty"><? echo $tot_pro_qty;?></th>
            <th width="75" id="tot_bill_qty"><? echo round($tot_bill_qty);?></th>
            <th width="50"></th>
            <th width="80" id="tot_amount"><? echo round($tot_amount);?></th>
            <th></th>
       </tr>
       <tr>
            <th width="50" colspan="13" align="right">Deducted Amount: </th>
            <th width="80" align="right"><? echo $deducted_emp;?></th>
            <th></th>
        </tr>
        <tr>
            <th width="50" colspan="13" align="right">Net Amount: </th>
            <th width="80" align="right"><? echo $net=$tot_amount-$deducted_emp;?></th>
            <th></th>
        </tr>

     </tfoot>
</table>    
</div>	


		 
<?	
	echo signature_table(229, $cbo_company_name, "950px");
	foreach (glob("$user_id*.xls") as $filename)
	{		
		@unlink($filename);
	}
	$name=$user_id.'_'.time().".xls";
	$create_new_excel = fopen($name, 'w');	
	$report_data=ob_get_contents();
	ob_clean();
	$is_created = fwrite($create_new_excel,$report_data);
	echo $report_data."####".$name;
	
exit();
}


?>
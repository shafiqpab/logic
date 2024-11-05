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
	echo create_drop_down( "cbo_location", 110, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/sewing_wages_bill_report_controller', this.value, 'load_drop_down_line', 'line_td' );",0 );     	 
}



if ($action=="load_drop_down_line")
{
	echo create_drop_down( "cbo_line", 80, "select id,line_name from lib_sewing_line where status_active =1 and is_deleted=0 and location_name='$data' order by line_name","id,line_name", 1, "-- Select --", $selected, "",0 );     	 
}


 
if($action=="report_generate")
{ 
	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_lib=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$company_short_lib=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );
 	$buyer_lib=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
 	$location_lib=return_library_array( "select id,location_name from lib_location", "id", "location_name"  ); 			
	$line_lib=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name" );

	//$po_number_arr = return_library_array("select id, po_number from wo_po_break_down","id","po_number");
	//$po_quantity_arr = return_library_array("select id, po_quantity from wo_po_break_down","id","po_quantity");



//---------------------------------
	$cbo_company_name=str_replace("'","",$cbo_company_name);
		$line_array=array();
		
			
			if($db_type==0)
			{
				$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_name and a.is_deleted=0 and b.is_deleted=0 group by a.id");
			}
			else if($db_type==2 || $db_type==1)
			{	
				$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_name and a.is_deleted=0 and b.is_deleted=0 group by a.id, a.line_number");
			}
		
		
		foreach($line_data as $row)
		{
			$line='';
			$line_number=explode(",",$row[csf('line_number')]);
			foreach($line_number as $val)
			{
				if($line=='') $line=$line_lib[$val]; else $line.=",".$line_lib[$val];
			}
			$line_array[$row[csf('id')]]=$line;
			//$line_array[$line_number[0]]=$line;
		}

	
//---------------------------------------------




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
	if(str_replace("'","",$cbo_line)==0)$line_con="";else $line_con=" and b.line_id=$cbo_line";
	
	
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
	SELECT
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
		b.line_id,
		b.rate_variables,
		b.deducted_qty,
		b.deducted_emp,
		b.prod_reso_allo,
		c.gmt_item_id,
		c.order_id,
		c.approved_wo_qty ,
		c.previous_bill_qty,
		c.bill_qty,
		b.wo_rate,
		c.amount,
		d.sewing_line_serial
	from 
		pro_weekly_wages_bill_mst a,
		pro_weekly_wages_bill_dtls b,
		pro_weekly_wages_order_brk c,
		lib_sewing_line d,
		prod_resource_mst e
	where
		a.id=b.mst_id
		and b.id=c.weekly_wages_dtls_id
		and a.id=c.weekly_wages_mst_id
		and b.line_id=e.id
		and a.bill_for=30
		and a.status_active=1
		and a.is_deleted=0
		and b.status_active=1
		and b.is_deleted=0
		and c.status_active=1
		and c.is_deleted=0
		and REGEXP_SUBSTR( e.line_number, '[^,]+', 1)=d.id
		$company_name
		$location_con
		$line_con
		$division_con
		$shift_con
		$date_con
	order by d.sewing_line_serial
		";
		

		//d.COMPANY_NAME=a.company_id and d.LOCATION_NAME=a.location and		
		//echo $sql;die;
		//$department_con$date_con_from$date_con_to

$con = connect();
execute_query("delete from tmp_poid where userid=".$_SESSION['logic_erp']['user_id']."");
	
$result = sql_select($sql);
foreach($result as $row){
	//$key=$row[csf('order_id')].$row[csf('company_id')].$row[csf('location')].$row[csf('line_id')].$row[csf('prod_reso_allo')].$row[csf('gmt_item_id')];
	$key=$row[csf('order_id')].$row[csf('company_id')].$row[csf('location')].$row[csf('line_id')].$row[csf('gmt_item_id')];
	$key2=$row[csf('line_id')].$row[csf('prod_reso_allo')];
	$data_arr[$key2][]=array(
				'key'=>$key,
				'location'=>$row[csf('location')],
				'line_id'=>$row[csf('line_id')],
				'prod_reso_allo'=>$row[csf('prod_reso_allo')],
				'emp_id'=>$row[csf('emp_id')],
				'emp_name'=>$row[csf('emp_name')],
				'order_id'=>$row[csf('order_id')],
				'buyer_id'=>$row[csf('buyer_id')],
				'rate_variables'=>$row[csf('rate_variables')],
				'gmt_item'=>$row[csf('gmt_item_id')],
				'deducted_qty'=>$row[csf('deducted_qty')],
				'deducted_emp'=>$row[csf('deducted_emp')],
				'wo_qty'=>$row[csf('approved_wo_qty')],
				'previous_bill_qty'=>$row[csf('previous_bill_qty')],
				'bill_qty'=>$row[csf('bill_qty')],
				'final_bill'=>$row[csf('final_bill')],
				'wo_rate'=>$row[csf('wo_rate')],
				'amount'=>$row[csf('amount')]
			);
			//$po_id.=$row[csf('order_id')].',';
			$line_arrary[$key2]=$row[csf('line_id')];	
			$prod_reso_allo_arr[$key2]=$row[csf('prod_reso_allo')];
			
			$r_id1=execute_query("insert into tmp_poid (userid, poid) values (".$_SESSION['logic_erp']['user_id'].",".$row[csf('order_id')].")");

			
}

//for previous deduct by order
//$po_id=rtrim($po_id,',');


 $sql="SELECT a.company_id,a.location,b.line_id,b.gmt_item,c.order_id,c.bill_qty,c.bill_safty_qty ,c.bill_safty_amount  FROM pro_weekly_wages_bill_mst a, pro_weekly_wages_bill_dtls b,pro_weekly_wages_order_brk c,tmp_poid tmp WHERE a.id=b.mst_id and a.id=c.weekly_wages_mst_id and b.id=weekly_wages_dtls_id and c.order_id=tmp.poid and tmp.userid=".$_SESSION['logic_erp']['user_id']." and a.bill_for=30 $company_name $location_con $division_con $department_con $shift_con";
 $data_array=sql_select($sql);
	foreach ($data_array as $row)
	{  
		$total_previous_deducted_qty[$row[csf('order_id')]]+=$row[csf("bill_safty_qty")];
		$total_previous_deducted_amount[$row[csf('order_id')]]+=$row[csf("bill_safty_amount")];
	}



$sql="select production_quantity,po_break_down_id,serving_company,location,sewing_line,prod_reso_allo,item_number_id from  pro_garments_production_mst where production_type = 5 and company_id=$cbo_company_name $production_location $production_date_con and status_active=1 and is_deleted=0";
$production_result = sql_select($sql);
	
foreach($production_result as $row){
	$key=$row[csf('po_break_down_id')].$row[csf('serving_company')].$row[csf('location')].$row[csf('sewing_line')].$row[csf('item_number_id')];
	$pro_qty_arr[$key]+=$row[csf('production_quantity')];
}


$sql_po="select a.ID,a.PO_NUMBER,a.PO_QUANTITY,b.STYLE_REF_NO  from wo_po_break_down a,WO_PO_DETAILS_MASTER b,tmp_poid tmp where a.id=tmp.poid and b.JOB_NO=a.JOB_NO_MST and tmp.userid=".$_SESSION['logic_erp']['user_id']." and a.status_active=1 and a.is_deleted=0";
$sql_po_result = sql_select($sql_po);
$po_number_arr=array();
$po_quantity_arr=array();	
$style_ref_no_arr=array();	
foreach($sql_po_result as $row){
	$po_number_arr[$row[ID]]+=$row[PO_NUMBER];
	$po_quantity_arr[$row[ID]]+=$row[PO_QUANTITY];
	$style_ref_no_arr[$row[ID]]+=$row[STYLE_REF_NO];
}


	
ob_start();	
?>

<div style="width:1315px;">
<table width="100%">
    <tr>
        <th colspan="15">
            <strong style="font-size:20px; line-height:18px;">Company Name: <?php echo $company_lib[str_replace("'",'',$cbo_company_name)];  ?></strong>
            <h2 style="font-size:25px; line-height:22px;">Sewing Wages Bill</h2>
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
<? foreach($data_arr as $key_id=>$line_arr){ ?>
<div style="width:1415px;">
<table width="100%">    
    <tr>
        <th style="text-align:left;" colspan="16">
            Line : <?php 
			$line_id=$line_arrary[$key_id];
			$is_allocation=$prod_reso_allo_arr[$key_id]; 
			if($is_allocation==1){echo $line_array[$line_id];}else{echo $line_lib[$line_id];}?>
        </th>
    </tr>
</table>
<table width="100%" border="1" class="rpt_table" rules="all">
    <thead>
        <tr>
            <th width="35">SL</th>
            <th width="60">ID No</th>
            <th width="110">Service Provider</th>
            <th width="100">Buyer</th>
            <th width="90">Order No</th>
            <th width="100">Style Ref.</th>
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
<div style="width:1432px; max-height:300px; overflow-y:auto;" id="scroll_body" class="scroll_body">
<table width="1415" border="1" class="rpt_table" rules="all" id="table_body">
    <tbody>
        <?
		
		$tot_po_qty=0; $tot_wo_qty=0; $tot_prev_bill_qty=0;
		$tot_pro_qty=0; $tot_bill_qty=0; $tot_amount=0; $deducted_emp=0;
		
		$i=1;
		foreach($line_arr as $rows){
         $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
			$tot_po_qty+=$po_quantity_arr[$rows['order_id']];
			$tot_wo_qty+=$rows['wo_qty'];
			$tot_prev_bill_qty+=$rows['previous_bill_qty'];
			$tot_pro_qty+=$pro_qty_arr[$rows['key']];
			$deducted_emp+=$rows['deducted_emp'];
			
			$tot_bill_qty+=round($rows['bill_qty']);
			$tot_amount+=round($rows['amount']);
			$grand_bill_qty+=round($rows['bill_qty']);
			$grand_amount+=round($rows['wo_rate']*$rows['bill_qty']);
			
		
		?>
        <tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $line_id.$i;?>" onClick="change_color('tr_<? echo $line_id.$i; ?>','<? echo $bgcolor; ?>')" style="cursor:pointer;">
            <td width="35" align="center"><? echo $i;?></td>
            <td width="60" align="center"><? echo $rows['emp_id'];?></td>
            <td width="110"><? echo $rows['emp_name'];?></td>
            <td width="100"><? echo $buyer_lib[$rows['buyer_id']];?></td>
            <td width="90"><p><? echo $po_number_arr[$rows['order_id']];?></p></td>
            <td width="100"><p><? echo $style_ref_no_arr[$rows['order_id']];?></p></td>
            <td width="90" align="center"><? echo $color_type[$rows['rate_variables']];?></td>
            <td width="110"><? echo $garments_item[$rows['gmt_item']];?></td>
            <td width="75" align="right"><? echo $po_quantity_arr[$rows['order_id']];?></td>
            <td width="75" align="right"><? echo $rows['wo_qty'];?></td>
            <td width="85" align="right"><? echo $rows['previous_bill_qty']*1;?></td>
            <td width="90" align="right"><? echo round($pro_qty_arr[$rows['key']]);?></td>
            <td width="75" align="right"><? echo round($rows['bill_qty']);?></td>
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
<div style="width:1415px;">
<table width="100%" border="1" class="rpt_table" rules="all">
    <tfoot>
        <tr>
            <th width="35"></th>
            <th width="60"></th>
            <th width="110"></th>
            <th width="100"></th>
            <th width="90"></th>
            <th width="100"></th>
            <th width="90"></th>
            <th width="110"></th>
            <th width="75" id="tot_po_qty" align="right"><? echo $tot_po_qty;?></th>
            <th width="75" id="tot_wo_qty" align="right"><? echo $tot_wo_qty;?></th>
            <th width="85" id="tot_prev_bill_qty" align="right"><? echo $tot_prev_bill_qty;?></th>
            <th width="90" id="tot_pro_qty" align="right"><? echo $tot_pro_qty;?></th>
            <th width="75" id="tot_bill_qty" align="right"><? echo $tot_bill_qty;?></th>
            <th width="50"></th>
            <th width="80" id="tot_amount" align="right"><? echo $tot_amount;?></th>
            <th></th>
        </tr>
        <tr>
            <th width="50" colspan="14" align="right">Deducted Qty: </th>
            <th width="80" align="right"><? echo $deducted_emp;?></th>
            <th></th>
        </tr>
        <tr>
            <th width="50" colspan="14" align="right">Net Amount: </th>
            <th width="80" align="right"><? echo $net=$tot_amount-$deducted_emp;?></th>
            <th></th>
        </tr>
        <tr>
        	<td colspan="16"><b>In Words: <? echo number_to_words($net, "Taka", "Paisa"); ?></b></td>
        </tr>
     </tfoot>
</table>    
</div>	

<? } ?>

<div style="width:1415px;">
<table width="100%" border="1" class="rpt_table" rules="all">
    <tfoot>
        <tr>
            <th colspan="14" align="right">Grand Bill Qnty[Dzn]: </th>
            <th width="80" align="right"><? echo round($grand_bill_qty);?></th>
            <th width="175"></th>
        </tr>
        <tr>
            <th colspan="14" align="right">Grand Amount: </th>
            <th width="80" align="right"><? echo round($grand_amount);?></th>
            <th width="175"></th>
        </tr>
        
        
        <tr>
        	<td colspan="16"><b>In Words: <? echo number_to_words($grand_amount, "Taka", "Paisa"); ?></b></td>
        </tr>
     </tfoot>
</table>    
</div>	



		 
<?	
	echo signature_table(228, $cbo_company_name, "950px");

	foreach (glob("$user_id*.xls") as $filename)
	{		
		@unlink($filename);
	}
	$name=$user_id."_".time().".xls";	
	
	$create_new_excel = fopen($name, 'w');	
	$report_data=ob_get_contents();
	ob_clean();
	$is_created = fwrite($create_new_excel,$report_data);
	echo $report_data."####".$name;
	
exit();
}


?>
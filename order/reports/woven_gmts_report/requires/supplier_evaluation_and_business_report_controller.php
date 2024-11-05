<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');

$company_library=return_library_array( "select id, company_name from lib_company where status_active =1 and is_deleted=0", "id", "company_name"  );
$buyer_arr_library=return_library_array( "select id, buyer_name from lib_buyer where status_active =1 and is_deleted=0", "id", "buyer_name"  );
$supp_arr_library=return_library_array( "select id, supplier_name from lib_supplier where status_active =1 and is_deleted=0", "id", "supplier_name"  );

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.buyer_name, buy.id from lib_buyer buy where status_active =1 and is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );
	exit();
}
if($action=="load_drop_down_supplier")
{
	$data = explode('*', $data);
	echo create_drop_down( "cbo_supplier_id", 150, "SELECT a.id, a.supplier_name from lib_supplier a join lib_supplier_tag_company b on a.id=b. supplier_id join lib_supplier_party_type c on a.id = c.supplier_id where b.tag_company =$data[1] and c.party_type = $data[0] order by a.supplier_name","id,supplier_name", 1, "-- All Supplier --", $selected, "" );
	exit();
}

if($action == "report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_name=str_replace("'","",$cbo_company_id);
	$buyer_id=str_replace("'","",$cbo_buyer_name);
	$category_id=str_replace("'","",$cbo_category_id);
	$supplier_id=str_replace("'","",$cbo_supplier_id);
	$year_to=str_replace("'","",$cbo_year);
	$month_from=str_replace("'","",$cbo_month_from);
	$month_to=str_replace("'","",$cbo_month_to);
	$start_date=$year_to."-".$month_from."-01";
	$num_days = cal_days_in_month(CAL_GREGORIAN, $month_to, $year_to);
	$end_date=$year_to."-".$month_to."-$num_days";

	if($company_name != ''){
		$report_con .= " and a.company_id=$company_name";
	}
	if($buyer_id != 0){
		$report_con .= " and a.buyer_id=$buyer_id";
	}
	if($category_id == 3){
		$report_con .= " and a.booking_type=1";
	}
	if($category_id == 4){
		$report_con .= " and a.booking_type=2";
	}
	if($supplier_id != 0){
		$report_con .= " and a.supplier_id=$supplier_id";
	}
	if($start_date != 0 && $end_date != 0){
		$report_con .=" and a.booking_date between '".date("j-M-Y",strtotime($start_date))."' and '".date("j-M-Y",strtotime($end_date))."'";
	}



	$sql = "SELECT a.supplier_id , EXTRACT( MONTH FROM TO_DATE( a.booking_date,  'DD-Mon-YYYY HH24:MI:SS' ) ) month, sum(b.amount) as amount  from wo_booking_mst a join wo_booking_dtls b on a.booking_no = b.booking_no where a.status_active=1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted =0 and b.amount > 0 and a.supplier_id >0 $report_con group by a.supplier_id ,a.booking_date order by a.booking_date";
	//echo $sql;

	$bokking_amount = sql_select($sql);
	$supp_wise_amount = array();
	foreach ($bokking_amount as $row) {
		$supp_wise_amount[$row[csf('supplier_id')]][$row[csf('month')]] += $row[csf('amount')];
	}

/*	echo "<pre>";
	print_r($supp_wise_amount);*/
	foreach ($supp_wise_amount as $supplier_id => $supplier_data) {
		$total_amount += array_sum($supplier_data);
	}
	$i=1;
	ob_start();
	?>
	<div style="width:1100px;">
		<fieldset style="width:100%;">
			<table width="1100">
	            <tr>
	               <td align="center" class="form_caption" style="font-size:16px;" colspan="15"> <? echo $company_library[$company_name]; ?></td>
	            </tr>
	             <tr>
	               <td align="center" class="form_caption" colspan="15"><strong>Supplier Evaluation And Business Report</strong></td>
	            </tr>
	        </table>
	        <table class="rpt_table" width="1100" cellpadding="0" cellspacing="0" border="1" rules="all">
	        	<thead>
	        		<th width="30">SL</th>
					<th width="200">Supplier</th>
					<? foreach ($months as $key => $month) { ?>
					<th width="70"><? echo $month ?></th>
					<? } ?>
					<th width="80">Total</th>
					<th width="50">%</th>
	        	</thead>
	        	<tbody>
	        		<?
	        			foreach ($supp_wise_amount as $supplier_id => $supplier_data) { ?>
	        				<tr>
	        					<td><? echo $i; ?></td>
	        					<td><? echo $supp_arr_library[$supplier_id] ?></td>
	        					<?
	        					$supplier_total =0;
	        					foreach ($months as $month_id => $month) {
	        						if($supplier_data[$month_id] != ''){
	        							echo '<td>'.number_format($supplier_data[$month_id],2).'</td>';
	        							$month_total[$month_id] += $supplier_data[$month_id];
	        							$supplier_total += $supplier_data[$month_id];
	        						}
	        						else{
	        							echo '<td> </td>';
	        						}
		        				}
	        					?>
	        					<td><? echo number_format($supplier_total,2); $gr_supp_total += $supplier_total; ?></td>
	        					<td><? echo number_format($supplier_total/$total_amount*100,2).'%'; ?></td>
	        				</tr>
	        			<?
	        			$i++;
	        			}
	        		?>
	        	</tbody>
	        	<tfoot>
	        		<th colspan="2">Total</th>
	        		<? foreach ($months as $month_id => $month) {
	        			if($month_total[$month_id] != ''){
	        				echo '<th>'.number_format($month_total[$month_id],2).'</th>';
	        			}
	        			else{
	        				echo '<th> </th>';
	        			}
	        		} ?>
	        		<th><? echo number_format($gr_supp_total,2) ?></th>
	        		<th></th>
	        	</tfoot>
	        </table>
    	</fieldset>
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
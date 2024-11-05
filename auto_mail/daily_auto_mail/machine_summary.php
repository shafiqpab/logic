<?

ini_set('precision', 8);
ini_set("display_errors", 0);
require_once('../../includes/common.php');
  
$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);
$previous_date= change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1);
$from_date=$previous_date;
$to_date=$previous_date;

//$previous_date='05-Sep-2022';$current_date='05-Sep-2022';
//------------------------------------------------------------------------------------------------------


$machine_sql= "select id,category_id,machine_group,dia_width,gauge from lib_machine_name where category_id in (1,2) and is_deleted = 0 and status_active = 1";
$machine_sql_result=sql_select($machine_sql);
foreach($machine_sql_result as $row)
{
	if($row[csf('machine_group')] and $row[csf('machine_group')] and $row[csf('gauge')] and $row[csf('category_id')]==1){
		$machineIdArr[$row[csf('category_id')]][$row[csf('id')]]=$row[csf('id')];
	}
	else if($row[csf('category_id')]==2)
	{
		$machineIdArr[$row[csf('category_id')]][$row[csf('id')]]=$row[csf('id')];
	}
	

} 
	
	$receive_date= " and a.receive_date between '".$previous_date."' and '".$previous_date."'";
	$receive_date_sub= " and a.product_date between '".$previous_date."' and '".$previous_date."'";
	$sql="Select b.machine_no_id as MACHINE_ID, 1 as ACTIVE_MACHINE_TYPE from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.entry_form=2 and a.item_category=13 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.machine_no_id!=0 $receive_date 
	group by b.machine_no_id
	union all
	
	Select  b.machine_id as MACHINE_ID, 1 as ACTIVE_MACHINE_TYPE from  subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.product_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.machine_id!=0 $receive_date_sub 
	group by b.machine_id
	
	union all
	select f.machine_id as MACHINE_ID, 2 as ACTIVE_MACHINE_TYPE  from pro_batch_create_dtls b, fabric_sales_order_mst d, pro_fab_subprocess f, pro_batch_create_mst a where f.batch_id=a.id and a.working_company_id=1 and f.process_end_date = '".$previous_date."'   and f.service_source in(1) and a.entry_form=0 and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1,11,2,3) and b.po_id=d.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and a.is_sales=1 and b.is_sales=1 group by  f.machine_id 
	";	
	
 //echo $sql;
 
   
$sql_result=sql_select($sql);
foreach($sql_result as $row)
{
	if($row[csf('ACTIVE_MACHINE_TYPE')]==1){$active_kniting_machine_id_arr[$row[csf('MACHINE_ID')]]=$row[csf('MACHINE_ID')];}
	if($row[csf('ACTIVE_MACHINE_TYPE')]==2){$active_dyeing_machine_id_arr[$row[csf('MACHINE_ID')]]=$row[csf('MACHINE_ID')];}

}
	$total_kniting_machine=count($machineIdArr[1]);
	$total_dyeing_machine=count($machineIdArr[2]);
	$total_idle_kniting_machine=$total_kniting_machine-count($active_kniting_machine_id_arr);
	$total_idle_dyeing_machine=$total_dyeing_machine-count($active_dyeing_machine_id_arr);


	ob_start();
?>




<table cellspacing="0" border="1" rules="all" width="243">
    <tr bgcolor="#CCCCCC">
        <th colspan="4">Machine Summary	</th>
    </tr>		
    <tr>
        <th>Dept</th>
        <th>Total M/C</th>
        <th>Idle M/C</th>
        <th>Idle M/C%</th>
    </tr>
    <tr>
        <td>Knitting</td>
        <td align="right"><? echo $total_kniting_machine;?></td>
        <td align="right" title="TAM:<?= count($active_kniting_machine_id_arr); ?>"><? echo $total_idle_kniting_machine;?></td>
        <td align="right"><? echo number_format(($total_idle_kniting_machine*100)/$total_kniting_machine,2);?></td>
    </tr>
    <tr>
        <td>Dyeing</td>
        <td align="right"><? echo $total_dyeing_machine;?></td>
        <td align="right" title="TAM:<?= count($active_dyeing_machine_id_arr); ?>"><? echo $total_idle_dyeing_machine;?></td>
        <td align="right"><? echo number_format(($total_idle_dyeing_machine*100)/$total_dyeing_machine,2);?></td>
    </tr>
</table>

<?
	$html .= ob_get_contents();
	ob_clean();
	$file_name = 'html/machine_summary.html';
	$create_file = fopen($file_name, 'w');	
	fwrite($create_file,$html);
	echo $html;
	?>
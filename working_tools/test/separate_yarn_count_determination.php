<?
/*

Developed By: Zakaria joy
date 		: 25/04/2021
*/
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();
$job_yarn_count=sql_select( "select a.id as job_id, a.job_no, a.garments_nature, b.lib_yarn_count_deter_id   from  wo_pre_cost_mst a, wo_pre_cost_fabric_cost_dtls b where a.job_id=b.job_id ");

foreach ($job_yarn_count as $row) {
	$job_data[$row[csf('lib_yarn_count_deter_id')]][$row[csf('garments_nature')]][$row[csf('job_id')]]['job_no']=$row[csf('job_no')];
	$job_data[$row[csf('lib_yarn_count_deter_id')]][$row[csf('garments_nature')]][$row[csf('job_id')]]['job_id']=$row[csf('job_id')];
}
/*echo '<pre>';
print_r($job_data); die;*/
?>
<legend>Knit And Woven</legend>
<table border="1">
<tr>
	<th>SL</th>
	<th>Fabric Id</th>
	<!-- <th>Knit</th>
	<th>Knit JOB</th>
	<th>Woven</th> -->	
	<th>Woven JOB</th>
</tr>
<?
$i=1;
foreach ($job_data as $yarn_id=>$yan_data) {
	if(count($yan_data[2])>0 && count($yan_data[3])>0)
	//if(count($yan_data[3])>0 && count($yan_data[2])=='')
	{
	?>
		<tr>
			<td rowspan="<? echo count($yan_data[3])+1; ?>"><? echo $i ?></td>
			<td rowspan="<? echo count($yan_data[3])+1; ?>"><? echo $yarn_id ?></td>
			<!-- <td rowspan="<? echo count($yan_data[2]); ?>"><? echo empty($yan_data[2]) ? "" : "Knit"; ?></td>
			<td rowspan="<? echo count($yan_data[2]); ?>"><? echo empty($yan_data[3]) ? "" : "Woven"; ?></td> -->
		</tr>
		
	<?
		/*foreach ($yan_data[2] as $job_id=>$job_data) {
			?><tr>
				<td><? echo $job_data['job_no'] ?></td>
				</tr>
			<?
		}*/
		foreach ($yan_data[3] as $job_id=>$job_data) {
			?><tr>
				<td><? echo $job_data['job_no'] ?></td>
				</tr>
			<?
		}
		?>
		
		<?
	$i++;
	$id_arr[$yarn_id] = $yarn_id;
	}
}
?>
</table>
<?
//echo implode(",", $id_arr);

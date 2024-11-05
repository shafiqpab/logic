<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();
 
$sew_qnty_sql="SELECT max(mst_id),count(bundle_no),bundle_no from pro_garments_production_dtls where (is_rescan=0 or is_rescan is null) and production_type =4 and   status_active=1 and is_deleted=0 
 
           group by bundle_no having  count(bundle_no)>1    ";
if(count(sql_select($sew_qnty_sql))>0)
{
	?>
	<br>
	<br>
	<table border="1" width="300">
	<caption>Sewing Input Duplicate</caption>
		<thead>
			<th>SL</th>
			<th>Bundle No</th>
		</thead>
	 <tbody>

	<?
	$i=1;
	foreach(sql_select($sew_qnty_sql) as $v)
	{
		
		?>
		<tr>
			<td><? echo $i++;?></td>
			<td><? echo $v[csf("bundle_no")];?></td>
		</tr>

		<?
	}

	?>
	</tbody>
	</table>


	<?

}




$sewout_qnty_sql="SELECT max(mst_id),count(bundle_no),bundle_no from pro_garments_production_dtls where (is_rescan=0 or is_rescan is null) and production_type =5 and   status_active=1 and is_deleted=0 
 
           group by bundle_no having  count(bundle_no)>1    ";
           ?>
<div style="margin: 0px auto;">

           <?
if(count(sql_select($sewout_qnty_sql))>0)
{
	?>
	
		
	
	<br>
	<br>
	<table border="1" width="300">
	<caption>Sewing Output Duplicate</caption>
		<thead>
			<th>SL</th>
			<th>Bundle No</th>
		</thead>
	 <tbody>

	<?
	$i=1;
	foreach(sql_select($sewout_qnty_sql) as $v)
	{
		
		?>
		<tr>
			<td><? echo $i++;?></td>
			<td><? echo $v[csf("bundle_no")];?></td>
		</tr>

		<?
	}

	?>
	</tbody>
	</table>


	<?

}



$cut_qnty_sql="SELECT max(mst_id),count(bundle_no),bundle_no from pro_garments_production_dtls where (is_rescan=0 or is_rescan is null) and production_type =1 and   status_active=1 and is_deleted=0 
 
           group by bundle_no having  count(bundle_no)>1    ";
if(count(sql_select($cut_qnty_sql))>0)
{
	?>
	<br>
	<br>
	<table border="1" width="300">
	<caption>Cutting  Duplicate</caption>
		<thead>
			<th>SL</th>
			<th>Bundle No</th>
		</thead>
	 <tbody>

	<?
	$i=1;
	foreach(sql_select($cut_qnty_sql) as $v)
	{
		
		?>
		<tr>
			<td><? echo $i++;?></td>
			<td><? echo $v[csf("bundle_no")];?></td>
		</tr>

		<?
	}

	?>
	</tbody>
	</table>


	<?

}

 
 
 ?></div><?
?>
<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();
$sid=$_GET["sid"]; 
$serial=$_GET["serial"]; 
if($sid && $serial)
{
	 $sql_kill="ALTER SYSTEM KILL SESSION '$sid,$serial'";
	 echo $rows=execute_query($sql_kill);

	die;
}
$sqls='SELECT s.username,s.sid,s.serial# as serial ,s.last_call_et/60 mins_running  from v$session s 
join v$sqltext_with_newlines q on s.sql_address = q.address  where status='."'ACTIVE'
and type <>'BACKGROUND' and last_call_et> 60 group by s.username,s.sid,s.serial#,s.last_call_et/60    ";
$row_data=sql_select($sqls);
?>
<table class="rpt_table" border="1" rules="all" width="310" cellpadding="1" cellspacing="0" align="left">
<thead>
	<tr>
		<th>SID</th>
		<th>Serial</th>
		<th>Running Mins</th>
	</tr>
</thead>
<tbody>
<?
foreach($row_data as $val)
{
	
	?>
	<tr>
		<td align="center"><? echo $val[csf("sid")];?></td>
		<td align="center"><? echo $val[csf("serial")];?></td>
		<td align="center"><? echo number_format( $val[csf("mins_running")],2);?></td>
	</tr>

	<?
	 
	 
	
	 
}
echo "</tbody></table>";

	 
 

 
?>
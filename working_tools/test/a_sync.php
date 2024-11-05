<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../includes/common.php');
$con=connect();

$orderSql = "select b.SHIPMENT_DATE,A.JOB_NO,B.ID AS PO_ID from WO_PO_DETAILS_MASTER a, WO_PO_BREAK_DOWN b where a.id=b.job_id and b.SHIPMENT_DATE between '20-jan-2023' and '30-jan-2023' order by b.SHIPMENT_DATE,B.ID ";
$orderSqlRes=sql_select($orderSql);
//, WO_PO_COLOR_SIZE_BREAKDOWN c //b.id=c.PO_BREAK_DOWN_ID and 

$dataArr = array();
$targetBr = 3;
foreach($orderSqlRes as $rows){
	if($dataArr[$rows['SHIPMENT_DATE']] ==''){$key=0;$flag = 0;}
	$dataArr[$rows['SHIPMENT_DATE']][$key][$rows['PO_ID']] = $rows;
	$flag++;
	if($flag == $targetBr){$key++;$flag = 0;}
}


foreach($dataArr as $ship_date => $customRow){
	echo "<p>{$ship_date}</p><br>";
 
		foreach($customRow as $data_row){
		//echo count($data_row);
		?>
			<table border="1" rules="all">

			<tr>
			<td>Job</td>
			<?
				foreach($data_row as $row){
					?>
						<td><?= $row['JOB_NO'];?></td>
					<?	
				}
			?>
			</tr>


			<tr>
			<td>Order</td>
			<?
				foreach($data_row as $row){
					?>
						<td><?= $row['SHIPMENT_DATE'];?></td>
					<?	
				}
			?>
			</tr>



			</table><br>
	   <?

 

	}


}




?>
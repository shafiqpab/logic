<?php
date_default_timezone_set("Asia/Dhaka");

require_once('../../includes/common.php');
require_once('../../mailer/class.phpmailer.php');
require_once('../setting/mail_setting.php');

if(date('D') != "Mon" && $_REQUEST['isview']!=1 ){exit('This Mail Send Only '.date("D"));}


 
extract($_REQUEST);

$current_date =($_REQUEST['view_date']=='')?change_date_format(date("Y-m-d",time()),'','',1):$_REQUEST['view_date'];
$previous_date = change_date_format(date('Y-m-d', strtotime('-1 day', strtotime($current_date))),'','',1);


$buyer_arr = return_library_array("select id,buyer_name from  lib_buyer ","id","buyer_name");
$company_library = return_library_array("select id,company_name from lib_company","id","company_name");

//$in_query = "select b.INQUERY_ID from WO_PO_DETAILS_MASTER a,WO_PRICE_QUOTATION b,WO_PO_BREAK_DOWN c where b.id=a.QUOTATION_ID and a.id=c.JOB_ID and c.SHIPING_STATUS <>3 and a.STATUS_ACTIVE=1 and b.STATUS_ACTIVE=1 and c.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.IS_DELETED=0 and c.IS_DELETED=0";


$where_cond =" and x.INQUERY_DATE  between '1-Jul-2022' and '$previous_date' ";
$sql = "select x.SYSTEM_NUMBER,x.ID as INQUIRY_ID,x.COMPANY_ID, x.BUYER_ID, x.STYLE_REFERNCE,x.GMTS_ITEM, x.EST_SHIP_DATE,x.OFFER_QTY,x.BUYER_SUBMIT_PRICE from wo_quotation_inquery x where x.status_active=1 and x.IS_DELETED=0 $where_cond  order by x.id";
 //echo $sql ;die;
$sql_result=sql_select($sql);
$inquiry_id_arr=array();
$dataArr = array();
foreach($sql_result as $rows){
	$dataArr[$rows['COMPANY_ID']][$rows['BUYER_ID']][$rows['STYLE_REFERNCE']][$rows['INQUIRY_ID']]=$rows;
	$ship_status[$rows['COMPANY_ID']][$rows['INQUIRY_ID']][$rows['BUYER_ID']][$rows['STYLE_REFERNCE']][1]=1;

	$SYSTEM_INQ_NUMBER_ARR[$rows['INQUIRY_ID']]=$rows['SYSTEM_NUMBER'];
}



$where_cond =" and a.INQUERY_DATE  between '1-Jul-2022' and '$previous_date' ";
$tagedOrderInquerySql = "select b.COMPANY_NAME AS COMPANY_ID,b.BUYER_NAME AS BUYER_ID,b.STYLE_REF_NO AS STYLE_REFERNCE,c.id as PO_BREAK_DOWN_ID,a.id as INQUIRY_ID,c.SHIPING_STATUS,(b.TOTAL_SET_QNTY*c.PO_QUANTITY) AS OFFER_QTY,(c.PO_TOTAL_PRICE/OFFER_QTY) AS BUYER_SUBMIT_PRICE from wo_quotation_inquery a,WO_PO_DETAILS_MASTER b , WO_PO_BREAK_DOWN c where a.id=b.INQUIRY_ID and b.id=c.job_id  $where_cond  and a.STATUS_ACTIVE=1 and a.IS_DELETED=0  and b.STATUS_ACTIVE=1 and b.IS_DELETED=0  and c.STATUS_ACTIVE=1 and c.IS_DELETED=0";
 //echo $tagedOrderInquerySql;die;

$tagedOrderInquerySqlRes=sql_select($tagedOrderInquerySql);
foreach($tagedOrderInquerySqlRes as $rows){
	unset( $ship_status[$rows['COMPANY_ID']][$rows['INQUIRY_ID']][$rows['BUYER_ID']][$rows['STYLE_REFERNCE']][1]);

	$rows['GMTS_ITEM'] = $dataArr[$rows['COMPANY_ID']][$rows['BUYER_ID']][$rows['STYLE_REFERNCE']][$rows['INQUIRY_ID']]['GMTS_ITEM'];
	$rows['EST_SHIP_DATE'] = $dataArr[$rows['COMPANY_ID']][$rows['BUYER_ID']][$rows['STYLE_REFERNCE']][$rows['INQUIRY_ID']]['EST_SHIP_DATE'];

	$dataArr[$rows['COMPANY_ID']][$rows['BUYER_ID']][$rows['STYLE_REFERNCE']][$rows['INQUIRY_ID']]=$rows;
	
	$offerQtyArr[$rows['COMPANY_ID']][$rows['BUYER_ID']][$rows['STYLE_REFERNCE']][$rows['INQUIRY_ID']]+=$rows['OFFER_QTY'];
	$buyerSubmitPriceArr[$rows['COMPANY_ID']][$rows['BUYER_ID']][$rows['STYLE_REFERNCE']][$rows['INQUIRY_ID']]+=$rows['BUYER_SUBMIT_PRICE'];

	$inquiry_id_arr[$rows['INQUIRY_ID']] = $rows['INQUIRY_ID'];

	 $ship_status[$rows['COMPANY_ID']][$rows['INQUIRY_ID']][$rows['BUYER_ID']][$rows['STYLE_REFERNCE']][$rows['PO_BREAK_DOWN_ID']]=$rows['SHIPING_STATUS'];

}



$shipSql = "select c.STYLE_REF_NO as STYLE_REFERNCE,c.COMPANY_NAME AS COMPANY_ID,c.BUYER_NAME AS BUYER_ID,a.PO_BREAK_DOWN_ID,c.INQUIRY_ID,a.EX_FACTORY_QNTY,a.SHIPING_STATUS from PRO_EX_FACTORY_MST a,WO_PO_BREAK_DOWN b,WO_PO_DETAILS_MASTER c where b.id=a.PO_BREAK_DOWN_ID and c.id=b.job_id ".where_con_using_array($inquiry_id_arr,0,'c.INQUIRY_ID')."  and a.STATUS_ACTIVE=1 and a.IS_DELETED=0  ";
 //echo $shipSql;die;
 $ship_qty = array();
$shipSqlRes=sql_select($shipSql);
foreach($shipSqlRes as $rows){
	$ship_qty[$rows['COMPANY_ID']][$rows['BUYER_ID']][$rows['STYLE_REFERNCE']][$rows['INQUIRY_ID']] += $rows['EX_FACTORY_QNTY'];
}



ob_start();

$GROUP_NAME=return_field_value("GROUP_NAME","LIB_GROUP","is_deleted=0 and status_active=1","",$con);

$mail_item=101;
$toArr=array();
$sql = "SELECT a.COMPANY_ID,c.EMAIL_ADDRESS FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=$mail_item and b.mail_user_setup_id=c.id   and a.IS_DELETED=0 and a.STATUS_ACTIVE=1  and b.IS_DELETED=0 and b.STATUS_ACTIVE=1   and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
//echo $sql;die;
$mail_sql=sql_select($sql);
$setup_company_array=array();
foreach($mail_sql as $row)
{
	$toArr[$row['EMAIL_ADDRESS']]=$row['EMAIL_ADDRESS']; 
	$setup_company_array[$row['COMPANY_ID']]=$company_library[$row['COMPANY_ID']];
	
	
}
$to = implode(',',$toArr);


?>

<table width="800">
	<tr>
			<td colspan="4">
				<h3><?=$GROUP_NAME;?></h3>
			</td>
			<td colspan="5" align="right">
				<strong>Style Wise Confirm QTY</strong><br>
				<strong><?=change_date_format($previous_date);?></strong>
			</td>
		</tr>
</table>
<table border="1" class="rpt_table"  cellpadding="0" cellspacing="0" rules="all" width="800" >
	<thead>
		<tr style="background-color:#DDD;">
			<th>Buyer</th>
			<th>Style</th>
			<th>Product Name</th>
			<th width='70'>Est. TOD Date</th>
			<th>Act. Qnty pcs</th>
			<th>Unit Price</th>
			<th>Value</th>
			<th>Curr.Ship Qty</th>
			<th>Balance Qty</th>
		</tr>
	</thead>
	<tbody>
		<? 
		$group_total=array();
		foreach($setup_company_array as $company_id => $company_name){
		?>
			<tr>
				<td colspan="7"><strong><?=$company_name;?></strong></td>
			</tr>
		<?
		
		$buyer_total=array();$companyh_total=array();
		foreach($dataArr[$company_id] as $buyer_id => $buyer_rows){
			foreach($buyer_rows as $style_no => $QtyRows){
				
				$balanceQty = 0; $offer_qty = 0; $value = 0; $rows = array();
				foreach($QtyRows as $inquiry_id => $row){
					if((min($ship_status[$company_id][$inquiry_id][$buyer_id][$style_no])*1) < 3){
						$rows = $row;
						
						$row['OFFER_QTY'] = $offerQtyArr[$company_id][$buyer_id][$style_no][$inquiry_id];
						$row['BUYER_SUBMIT_PRICE'] = $buyerSubmitPriceArr[$company_id][$buyer_id][$style_no][$inquiry_id];
						$balanceQty += $row['OFFER_QTY'] - $ship_qty[$company_id][$buyer_id][$style_no][$inquiry_id];	
						$offer_qty += $row['OFFER_QTY'];
						$value += $row['OFFER_QTY'] * $row['BUYER_SUBMIT_PRICE'];
					}

				}
				$rows['OFFER_QTY'] = $offer_qty; 

				
				if($balanceQty<=0 ){continue;}

				
				
				$buyer_total['qty'][$buyer_id]+=$rows['OFFER_QTY'];
				$buyer_total['val'][$buyer_id]+=$value;
				$buyer_total['curr_ship'][$buyer_id] += $ship_qty[$company_id][$rows['BUYER_ID']][$rows['STYLE_REFERNCE']][$rows['INQUIRY_ID']];
				$buyer_total['ship_bal_qty'][$buyer_id]+=$balanceQty;

				$companyh_total['qty'][$company_id]+=$rows['OFFER_QTY'];
				$companyh_total['val'][$company_id]+=$value;
				$companyh_total['curr_ship'][$company_id] += $ship_qty[$company_id][$rows['BUYER_ID']][$rows['STYLE_REFERNCE']][$rows['INQUIRY_ID']];
				$companyh_total['ship_bal_qty'][$company_id]+=$balanceQty;

				$group_total['qty'][$company_id]+=$rows['OFFER_QTY'];
				$group_total['val'][$company_id]+=$value;
				$group_total['curr_ship'][$buyer_id] += $ship_qty[$company_id][$rows['BUYER_ID']][$rows['STYLE_REFERNCE']][$rows['INQUIRY_ID']];
				$group_total['ship_bal_qty'][$buyer_id]+=$balanceQty;

				$bgcolor = ($ship_status[$company_id][$rows['INQUIRY_ID']][$buyer_id][$rows['STYLE_REFERNCE']][1] == 1)?" #fdedec ":"";

						

			?>
		
				<tr bgcolor="<?= $bgcolor;?>">
					<td><?= $buyer_arr[$buyer_id];?></td>
					<td title="<?=$SYSTEM_INQ_NUMBER_ARR[$rows['INQUIRY_ID']];?>"><?= $rows['STYLE_REFERNCE'];?></td>
					<td><?= $garments_item[$rows['GMTS_ITEM']];?></td>
					<td><?= change_date_format($rows['EST_SHIP_DATE']);?></td>
					<td align="right"><?= $rows['OFFER_QTY'];?></td>
					<td align="right"><?= number_format($rows['BUYER_SUBMIT_PRICE'],2);?></td>
					<td align="right"><?= number_format($value,2);?></td>
					<td align="right"><?= $ship_qty[$company_id][$buyer_id][$rows['STYLE_REFERNCE']][$rows['INQUIRY_ID']];?></td>
					<td align="right"><?= $balanceQty;?></td>
				</tr>
		<?
			}
			if($buyer_total['ship_bal_qty'][$buyer_id]){
			?>
			<tr style="background-color:#EEE;">
				<td colspan="4" align="right"><strong>Buyer Total</strong></td>
				<td align="right"><?=$buyer_total['qty'][$buyer_id];?></td>
				<td></td>
				<td align="right"><?=number_format($buyer_total['val'][$buyer_id],2);?></td>
				<td align="right"><?=number_format($buyer_total['curr_ship'][$buyer_id]);?></td>
				<td align="right"><?=number_format($buyer_total['ship_bal_qty'][$buyer_id]);?></td>
			</tr>

			<?
			}
		}
		?>
			<tr style="background-color:#CCC;">
				<td colspan="4" align="right"><strong>Company Total</strong></td>
				<td align="right"><?=$companyh_total['qty'][$company_id];?></td>
				<td></td>
				<td align="right"><?=number_format($companyh_total['val'][$company_id],2);?></td>
				<td align="right"><?=number_format($companyh_total['curr_ship'][$company_id]);?></td>
				<td align="right"><?=number_format($companyh_total['ship_bal_qty'][$company_id]);?></td>
			</tr>

		<?
		}
		?>

			<tr>
				<td colspan="4" align="right"><strong>Group Total</strong></td>
				<td align="right"><?=array_sum($group_total['qty']);?></td>
				<td></td>
				<td align="right"><?=number_format(array_sum($group_total['val']),2);?></td>
				<td align="right"><?=number_format(array_sum($group_total['curr_ship']));?></td>
				<td align="right"><?=number_format(array_sum($group_total['ship_bal_qty']));?></td>
			</tr>


	</tbody>
</table>


<?


		
	$message=ob_get_contents();
	ob_clean();

	$subject="Style Wise Confirm QTY ( Date :".date("d-m-Y", strtotime($previous_date)).")";
	$header=mailHeader();
	if($_REQUEST['isview']==1){
		if($to){
			echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
		}else{
			echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
		}
		echo $message;
	}
	else{
		require_once('../../ext_resource/mpdf60/mpdf.php');
		$mpdf = new mPDF('utf-8');
		$mpdf = new mPDF();

		$mpdf->WriteHTML($message);
		foreach (glob("../tmp/"."*.pdf") as $filename) {			
			@unlink($filename);
		}
		$name = 'mail_' . date('j-M-Y_h-iA') . '.pdf';
		$mpdf->Output('../tmp/' . $name, 'F');
		$att_file_arr=array('../tmp/'.$name);

		
		

		if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail,$att_file_arr );}		
	}
		
	
		
?>
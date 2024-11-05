<?php
date_default_timezone_set("Asia/Dhaka");

require_once('../../includes/common.php');
 
 
$company_library = return_library_array( "select id, company_short_name from lib_company where status_active=1 and is_deleted=0", "id", "company_short_name",$con);
$lib_color_arr = return_library_array( "SELECT ID,COLOR_NAME FROM LIB_COLOR WHERE IS_DELETED=0 AND STATUS_ACTIVE=1", "ID", "COLOR_NAME");
$buyer_arr = return_library_array( "SELECT ID,BUYER_NAME FROM LIB_BUYER WHERE IS_DELETED=0 AND STATUS_ACTIVE=1", "ID", "BUYER_NAME");
$floor_arr = return_library_array( "SELECT ID,FLOOR_NAME FROM LIB_PROD_FLOOR WHERE IS_DELETED=0 AND STATUS_ACTIVE=1", "ID", "FLOOR_NAME");

$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);
if($_REQUEST['view_date']){
	$current_date = change_date_format(date("Y-m-d H:i:s",strtotime($_REQUEST['view_date'])),'','',1);
}
$previous_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1);

	


//$company_library=array(1=>$company_library[1]);
foreach($company_library as $compid=>$compname)
{
	
	$date_con=" and a.ENTRY_DATE between '$previous_date' and '$previous_date'";
	
	$cutting_sql="select a.JOB_NO,A.FLOOR_ID,C.ORDER_ID, b.COLOR_ID, sum(c.SIZE_QTY) AS CUTTING_QTY from PPL_CUT_LAY_MST a,PPL_CUT_LAY_DTLS b,PPL_CUT_LAY_BUNDLE c where a.id=b.mst_id and a.id=c.mst_id and b.id=c.DTLS_ID  AND A.status_active=1 and A.is_deleted=0 AND B.status_active=1 and B.is_deleted=0 AND C.status_active=1 and C.is_deleted=0 and a.COMPANY_ID=$compid $date_con group by a.FLOOR_ID,a.JOB_NO,C.ORDER_ID, b.COLOR_ID";
	 //echo $cutting_sql;die;
	$cutting_sql_sql_res=sql_select($cutting_sql);
	$cutting_data_arr=array();
	$tmp_po_id_arr=array();
	foreach ( $cutting_sql_sql_res as $row )
	{
		$cutting_data_arr['CUTTING_QTY'][$row['JOB_NO']][$row['COLOR_ID']]+=$row['CUTTING_QTY'];
		$cutting_data_arr['FLOOR_ID'][$row['JOB_NO']][$row['COLOR_ID']][$row['FLOOR_ID']]=$floor_arr[$row['FLOOR_ID']];
		$tmp_po_id_arr[$row['ORDER_ID']]=$row['ORDER_ID'];
	}
	
	//var_dump($cutting_data_arr[CUTTING_QTY]); 	
	
	
	
	
	$order_sql="select c.JOB_NO_MST,a.BUYER_NAME,a.STYLE_REF_NO,b.id as ORDER_ID,b.PO_NUMBER, c.COLOR_NUMBER_ID,c.EXCESS_CUT_PERC AS EXCESS_CUT_PERC,(c.ORDER_QUANTITY) AS ORDER_QUANTITY from WO_PO_DETAILS_MASTER a,WO_PO_BREAK_DOWN b, WO_PO_COLOR_SIZE_BREAKDOWN c where b.SHIPING_STATUS<3 and a.COMPANY_NAME=$compid and a.id=b.job_id and a.id=c.job_id and b.id=c.PO_BREAK_DOWN_ID and A.status_active=1 and A.is_deleted=0 AND B.status_active=1 and B.is_deleted=0 AND C.status_active=1 and C.is_deleted=0 ".where_con_using_array(array_keys($tmp_po_id_arr),0, 'b.id')."";
		   //echo $order_sql;die;
	$order_sql_res=sql_select($order_sql);
	$order_data_arr=array();
	foreach ( $order_sql_res as $row )
	{
		if($row['EXCESS_CUT_PERC']>0){
			$row['PLAN_CUT_QNTY']=(($row['EXCESS_CUT_PERC']*$row['ORDER_QUANTITY'])/100)+$row['ORDER_QUANTITY'];
		}
		else{
			$row['PLAN_CUT_QNTY']=$row['ORDER_QUANTITY'];
		}
		//$excess_allow_data_arr[$key][PO_QTY]+=$row[ORDER_QUANTITY];
		$order_data_arr['EXCESS_ALLOW_CUT_QTY'][$row['JOB_NO_MST']][$row['COLOR_NUMBER_ID']]+=ceil($row['PLAN_CUT_QNTY']);
		$style_by_job_arr[$row['JOB_NO_MST']]=$row['STYLE_REF_NO'];
		$buyer_by_job_arr[$row['JOB_NO_MST']]=$row['BUYER_NAME'];
	}
	unset($order_sql_res);	
	//var_dump($order_data_arr[EXCESS_ALLOW_CUT_QTY]);die;	
		
	
	$partial_color_cut_qty_arr=array();	
	foreach($cutting_data_arr['CUTTING_QTY'] as $job_no=>$dataArr){
		$temp_data_arr=array();
		foreach($dataArr as $color_id=>$cutColorQty){
			$order_color_qty = $order_data_arr['EXCESS_ALLOW_CUT_QTY'][$job_no][$color_id];
			if($order_color_qty>$cutColorQty){$temp_data_arr[$job_no][$color_id]=$cutColorQty;}
		}
		if(count($temp_data_arr[$job_no])>1){$partial_color_cut_qty_arr[$job_no]=$temp_data_arr[$job_no];}



	}
	
	//var_dump($partial_color_cut_qty_arr);die;	
	
	 
		ob_start();	
		?>
         <table border="1" cellpadding="5" cellspacing="5" class="rpt_table" rules="all"> 
            <tr>
                <th colspan="11" align="center">
                    <table cellpadding="0" cellspacing="0">
                        <tr>
                           <td align="center" colspan="<?= $count_data; ?>" class="form_caption" >
                            <strong style="font-size:18px"><?= $company_library[$compid];?></strong>
                           </td>
                        </tr> 
                        <tr>  
                           <td align="center" colspan="<?= $count_data; ?>" class="form_caption" ><strong style="font-size:14px">Unit Wise Garments  Production</strong></td>
                        </tr>
                        <tr>  
                           <td align="center" colspan="<?= $count_data; ?>" class="form_caption" ><strong style="font-size:14px"> <?= change_date_format($previous_date);?></strong></td>
                        </tr>  
                    </table>
                    Cutting done before a color qty finish
                </th>
            </tr>
            <tr bgcolor="#999999">
                <th>Sl</th>
                <th>Buyer</th>
                <th>Job No</th>
                <th>Style No</th>
                <th>Floor</th>
                <th>Color Name</th>
                <th>Color Qty</th>
                <th>Cut Qty</th>
                <th>Variance</th>
                <th>Cutting % </th>
                <th width="50">Incomplete %</th>
            </tr>
            <?
            $i=1;
			$flag=0;
            foreach($partial_color_cut_qty_arr as $job_no=>$dataArr)
            {
                foreach($dataArr as $color_id=>$cut_qty)
                {
                    $bgcolor=($i%2==0)?"#ffffff":"#D7E8FF";
					if($order_data_arr['EXCESS_ALLOW_CUT_QTY'][$job_no][$color_id]>2000){continue;}
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>">
                    <td align="center"><?=$i;?></td>
					<td><?=$buyer_arr[$buyer_by_job_arr[$job_no]];?></td>
                    <td><?=$job_no;?></td>
                    <td><?=$style_by_job_arr[$job_no];?></td>
                    <td><?=implode(',',$cutting_data_arr['FLOOR_ID'][$job_no][$color_id]);?></td>
                    <td><?=$lib_color_arr[$color_id];?></td>
                    <td align="right"><?=$order_data_arr['EXCESS_ALLOW_CUT_QTY'][$job_no][$color_id];?></td>
                    <td align="right"><?=$cut_qty;?></td>
                    <td align="right" style="background-color:#D00;"><?=$variance = $order_data_arr['EXCESS_ALLOW_CUT_QTY'][$job_no][$color_id]-$cut_qty;?></td>
                    <td align="right"><?=number_format((100*$cut_qty)/$order_data_arr['EXCESS_ALLOW_CUT_QTY'][$job_no][$color_id],2);?></td>
                    <td align="right" style="background-color:red;">
					<? 
						$blacne =$order_data_arr['EXCESS_ALLOW_CUT_QTY'][$job_no][$color_id]-$cut_qty;
						if($blacne>0){
							$incomPar = ($blacne/$order_data_arr['EXCESS_ALLOW_CUT_QTY'][$job_no][$color_id])*100;
							$incomPar = number_format($incomPar,2);
						}
						echo $incomPar;
					?></td>
                </tr>
                <?
                $i++;
				$flag=1;
                }
            }
            ?>
        </table><br />
	<?
		
		if($flag==1){$message.=ob_get_contents();}	
		ob_clean();

	}
	
	
	
	$mail_item=95;
	$mailArr=array();
	$sql = "SELECT c.email_address as MAIL_ADDRESS FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=95 and b.mail_user_setup_id=c.id AND a.MAIL_TYPE=1";
	$mail_sql=sql_select($sql);
	foreach($mail_sql as $row)
	{
		$mailArr[$row['MAIL_ADDRESS']]=$row['MAIL_ADDRESS'];
	}
 	$to=implode(',',$mailArr);
	
	$subject="Cutting done before a color qty finish ";
	
	
	
	
	
	if($_REQUEST['isview']==1){
		if($to){
			echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
		}else{
			echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
		}
		echo $message;
	}
	else{
		require_once('../../mailer/class.phpmailer.php');
		require_once('../setting/mail_setting.php');
		
		$header=mailHeader();
		if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail );}
	}

		
?>



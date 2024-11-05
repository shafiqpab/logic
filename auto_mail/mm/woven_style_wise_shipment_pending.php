<?
		include('../../includes/common.php');
		include('../setting/mail_setting.php');
		
		
		$buyer_lib_arr=return_library_array( "select id, short_name from lib_buyer where status_active=1",'id','short_name');
		$team_leader_arr=return_library_array( "select id,team_member_name from lib_mkt_team_member_info where is_deleted=0 and status_active=1",'id','team_member_name');
		$company_arr=return_library_array( "select id, company_name from lib_company where status_active=1",'id','company_name');
 		
		
		$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);
		$previous_date = change_date_format(date('Y-m-d H:i:s', strtotime('-7 day', time())),'','',1);
		
 
		
		$po_date_con = " and b.SHIPMENT_DATE between '".$previous_date."' and '".$current_date."'";

		$order_sql="select  a.COMPANY_NAME,a.JOB_NO,A.BUYER_NAME,A.TEAM_LEADER,A.STYLE_DESCRIPTION,b.PO_NUMBER,b.SHIPMENT_DATE as SHIPMENT_DATE,c.PO_BREAK_DOWN_ID,c.ORDER_QUANTITY as ORDER_QUANTITY,c.id AS COLOR_SIZE_ID from WO_PO_DETAILS_MASTER a,WO_PO_BREAK_DOWN b,WO_PO_COLOR_SIZE_BREAKDOWN c where a.id=b.job_id and a.id=c.job_id and b.id=c.PO_BREAK_DOWN_ID and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $po_date_con";
	 	  //echo $order_sql;die;
		$order_sql_result=sql_select($order_sql);	

		$order_data_arr=array();
		foreach($order_sql_result as $rows)
		{
			$key = $rows['JOB_NO'].'***'.$rows['BUYER_NAME'].'***'.$rows['TEAM_LEADER'].'***'.$rows['STYLE_DESCRIPTION'].'***'.$rows['PO_NUMBER'].'***'.$rows['SHIPMENT_DATE'];
			$order_data_arr['PO_ID'][$key]+=$rows['ORDER_QUANTITY'];
			$order_data_arr['PO_BREAK_DOWN_ID'][$rows['PO_BREAK_DOWN_ID']]=$rows['PO_BREAK_DOWN_ID'];
			$key_arr_by_po_id[$rows['PO_BREAK_DOWN_ID']]=$key;
		}
		
		$delivery_sql="select PO_BREAK_DOWN_ID,max(EX_FACTORY_DATE) as EX_FACTORY_DATE from PRO_EX_FACTORY_MST where is_deleted=0 and status_active=1 ".where_con_using_array($order_data_arr['PO_BREAK_DOWN_ID'],0,'PO_BREAK_DOWN_ID')." group by PO_BREAK_DOWN_ID";
		// echo $delivery_sql;die;
		$delivery_sql_result=sql_select($delivery_sql);
		foreach($delivery_sql_result as $rows)
		{
			$key = $key_arr_by_po_id[$rows['PO_BREAK_DOWN_ID']];
			$order_data_arr['ACTUAL_SHIPMENT_DATE'][$key]=$rows['EX_FACTORY_DATE'];
		}
		
			
	
	ob_start();	
?>   
         <table cellpadding="0" cellspacing="0" width="950" border="0">
            <tr>
                <td align="center" colspan="2"  style="font-size:20px"><strong>Order Wise Ex factory Balance QTY</strong></td>
            </tr>
            <tr>
                <td align="center" colspan="2" style="font-size:16px"><strong>Date From: <?=change_date_format($previous_date);?> To <?=change_date_format($current_date);?> </strong></td>
            </tr>
        </table>
        <table cellspacing="0" cellpadding="3" border="1" rules="all" width="950">
            <thead bgcolor="#CCCCCC">
				<tr>
					<th>SL</th>	
					<th>Job No</th>	
                    <th>Buyer</th>	
					<th>Team Leader</th>
					<th>Style</th>	
					<th>PO No</th>	
					<th>Order Quantity</th>	
					<th with="100">EX Factory Date</th>	
					<th with="100">Act. Ship. Date</th>	
					<th>Days in Hand</th>	
				</tr>
			</thead>
            <tbody>
            	<? 
				$i=1;
				foreach($order_data_arr['PO_ID'] as $keyStr=>$ORDER_QTY){
					list($rows['JOB_NO'],$rows['BUYER_NAME'],$rows['TEAM_LEADER'],$rows['STYLE_DESCRIPTION'],$rows['PO_NUMBER'],$rows['SHIPMENT_DATE'])=explode('***',$keyStr);
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	
					//$left_over_qty = $ORDER_QTY-$order_data_arr['DELIVERY_QTY'][$keyStr];
					//if($left_over_qty<=0){continue;}

                    $remain_days=datediff( "d", date("Y-m-d",strtotime($rows['SHIPMENT_DATE'])), date("Y-m-d",strtotime($order_data_arr['ACTUAL_SHIPMENT_DATE'][$keyStr])) );

					if($remain_days<=0){continue;}

				?>
                <tr bgcolor="<?=$bgcolor;?>">
                    <td><?=$i;?></td>
                    <td><?=$rows['JOB_NO'];?></td>
                    <td><?=$buyer_lib_arr[$rows['BUYER_NAME']];?></td>
                    <td><?=$team_leader_arr[$rows['TEAM_LEADER']];?></td>
                    <td><?=$rows['STYLE_DESCRIPTION'];?></td>
                    <td><?=$rows['PO_NUMBER'];?></td>
                    <td><?=$ORDER_QTY?></td>
                    <td align="center"><?=change_date_format($rows['SHIPMENT_DATE']);?></td>
                    <td align="center"><?=change_date_format($order_data_arr['ACTUAL_SHIPMENT_DATE'][$keyStr]);?></td>
                    <td align="center"><?=$remain_days;?></td>
                </tr>
                <?
				$i++;
				}
				?>
            </tbody>
         
		</table>
     
    
    <?
	$message=ob_get_contents();
	ob_clean();

	 
 
 	
	$sql = "SELECT a.BRAND_IDS,a.BUYER_IDS,c.email_address as MAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id  and a.mail_item=b.MAIL_ITEM_MST and a.mail_item=115 and b.mail_user_setup_id=c.id and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
	 //echo $sql;die;
	$mail_sql=sql_select($sql);
	foreach($mail_sql as $row)
	{
		$receverMailArr[$row['MAIL']]=$row['MAIL'];
	}
	$to=implode(',',$receverMailArr);
	
	$subject = "Order Wise Ex factory Balance QTY";
	$header=mailHeader();
	//echo $message ;

	
	if($_REQUEST['isview']==1){
		$mail_item=115;
		if($to){
			echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
		}else{
			echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
		}
		echo $message;
	}
	else{
		echo  sendMailMailer( $to, $subject, $message, '','' );
	}
	
	
	
	
?>   
    



    

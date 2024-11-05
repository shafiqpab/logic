<?
		include('../../includes/common.php');
		include('../setting/mail_setting.php');
		
		
		$buyer_lib_arr=return_library_array( "select id, short_name from lib_buyer where status_active=1",'id','short_name');
		//$team_leader_arr=return_library_array( "select id,team_member_name from lib_mkt_team_member_info where is_deleted=0 and status_active=1",'id','team_member_name');
		$team_leader_arr=return_library_array( "select id,team_leader_name from lib_marketing_team where project_type=1 and status_active =1 and is_deleted=0 order by team_leader_name",'id','team_leader_name');
		$company_arr=return_library_array( "select id, company_name from lib_company where status_active=1",'id','company_name');
 		
		
		$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);
		$previous_date = change_date_format(date('Y-m-d H:i:s', strtotime('-7 day', time())),'','',1);
		
 
		
		$po_date_con = " and b.SHIPMENT_DATE between '".$previous_date."' and '".$current_date."'";

		$order_sql="select  b.SHIPING_STATUS,a.COMPANY_NAME,A.BUYER_NAME,A.TEAM_LEADER,A.STYLE_REF_NO,b.PO_NUMBER,c.PO_BREAK_DOWN_ID,c.ITEM_NUMBER_ID,c.PLAN_CUT_QNTY as PLAN_CUT_QNTY,c.id AS COLOR_SIZE_ID from WO_PO_DETAILS_MASTER a,WO_PO_BREAK_DOWN b,WO_PO_COLOR_SIZE_BREAKDOWN c where a.id=b.job_id and a.id=c.job_id and b.id=c.PO_BREAK_DOWN_ID and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0   $po_date_con"; //and b.SHIPING_STATUS<>3
	 	 // echo $order_sql;die;
		$order_sql_result=sql_select($order_sql);	

		$order_data_arr=array();
		foreach($order_sql_result as $rows)
		{
			$key = $rows['COMPANY_NAME'].'***'.$rows['BUYER_NAME'].'***'.$rows['TEAM_LEADER'].'***'.$rows['STYLE_REF_NO'].'***'.$rows['PO_NUMBER'].'***'.$rows['ITEM_NUMBER_ID'].'***'.$rows['PO_BREAK_DOWN_ID'];
			$order_data_arr['PO_ID'][$key]+=$rows['PLAN_CUT_QNTY'];
			$order_data_arr['COLOR_SIZE_ID'][$rows['COLOR_SIZE_ID']]=$rows['COLOR_SIZE_ID'];
			$order_data_arr['SHIPING_STATUS'][$rows['PO_BREAK_DOWN_ID']]=$rows['SHIPING_STATUS'];
			$key_arr_by_color_size_id[$rows['COLOR_SIZE_ID']]=$key;
		}
		
		$delivery_sql="select COLOR_SIZE_BREAK_DOWN_ID,PRODUCTION_QNTY from PRO_EX_FACTORY_DTLS where is_deleted=0 and status_active=1 ".where_con_using_array($order_data_arr['COLOR_SIZE_ID'],0,'COLOR_SIZE_BREAK_DOWN_ID')."";
		 //echo $delivery_sql;die;
		$delivery_sql_result=sql_select($delivery_sql);
		foreach($delivery_sql_result as $rows)
		{
			$key = $key_arr_by_color_size_id[$rows['COLOR_SIZE_BREAK_DOWN_ID']];
			$order_data_arr['DELIVERY_QTY'][$key]+=$rows['PRODUCTION_QNTY'];
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
					<th>Company</th>	
					<th>Team Leader</th>
					<th>Buyer</th>	
					<th>Style</th>	
					<th>PO No</th>	
					<th>Product Name</th>	
					<th>Plan Cut</th>	
					<th>Gmt Del Qty</th>	
					<th>GMT. Left Over</th>	
					<th>Ship Status</th>	
				</tr>
			</thead>
            <tbody>
            	<? 
				$i=1;
				foreach($order_data_arr['PO_ID'] as $keyStr=>$CUT_QTY){
					list($rows['COMPANY_NAME'],$rows['BUYER_NAME'],$rows['TEAM_LEADER'],$rows['STYLE_REF_NO'],$rows['PO_NUMBER'],$rows['ITEM_NUMBER_ID'],$rows['PO_BREAK_DOWN_ID'])=explode('***',$keyStr);


					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	
					$left_over_qty = $CUT_QTY-$order_data_arr['DELIVERY_QTY'][$keyStr];
					if($left_over_qty<=0){continue;}
				?>
                <tr bgcolor="<?=$bgcolor;?>">
                    <td><?=$i;?></td>
                    <td><?=$company_arr[$rows['COMPANY_NAME']];?></td>
                    <td><?=$team_leader_arr[$rows['TEAM_LEADER']];?></td>
                    <td><?=$buyer_lib_arr[$rows['BUYER_NAME']];?></td>
                    <td><?=$rows['STYLE_REF_NO'];?></td>
                    <td><?=$rows['PO_NUMBER'];?></td>
                    <td><?=$garments_item[$rows['ITEM_NUMBER_ID']];?></td>
                    <td align="right"><?=$CUT_QTY;?></td>
                    <td align="right"><?=$order_data_arr['DELIVERY_QTY'][$keyStr];?></td>
                    <td align="right"><?=$left_over_qty ;?></td>
					<td><?=$shipment_status[$order_data_arr['SHIPING_STATUS'][$rows['PO_BREAK_DOWN_ID']]];?></td>
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

	 
 
 	
	$sql = "SELECT a.BRAND_IDS,a.BUYER_IDS,c.email_address as MAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id  and a.mail_item=b.MAIL_ITEM_MST and a.mail_item=114 and b.mail_user_setup_id=c.id   and   A.IS_DELETED=0 and A.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1  and b.IS_DELETED=0 and b.STATUS_ACTIVE=1  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
	 //echo $sql;die;
	$mail_sql=sql_select($sql);
	foreach($mail_sql as $row)
	{
		$receverMailArr[$row['MAIL']]=$row['MAIL'];
	}
	$to=implode(',',$receverMailArr);
	
	$subject = "Order Wise Ex factory Balance QTY [Last 30 days ]";
	$header=mailHeader();
	//echo $message ;

	
	if($_REQUEST['isview']==1){
		$mail_item=114;
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
    



    

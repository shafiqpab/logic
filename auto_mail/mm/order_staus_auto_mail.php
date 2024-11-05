<style>
	 p
 {
	word-break:break-all;
	word-wrap: break-word;
	width:100%;
	margin:0px;
	padding:0px;
 }
</style>

<?
		require_once('../../includes/common.php');
		require_once('../../mailer/class.phpmailer.php');
		require_once('../setting/mail_setting.php');

		//if(date('D') != "Mon" && ($_REQUEST['isview']!=1 && $_REQUEST['isview']!=0)){exit('This Mail Send Only '.date("D"));}
		if(date('D') != "Mon" && $_REQUEST['isview']!=1 ){exit('This Mail Send Only '.date("D"));}



		$buyer_lib_arr=return_library_array( "select id, short_name from lib_buyer where status_active=1",'id','short_name');
		$company_arr=return_library_array( "select id, company_name from lib_company where status_active=1",'id','company_name');
 		
		$group_name = return_field_value("group_name","LIB_GROUP","STATUS_ACTIVE=1 and IS_DELETED=0","group_name");
		
		$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);
		$previous_date = change_date_format(date('Y-m-d H:i:s', strtotime('-150 day', time())),'','',1);
		

//Production ---------------------------  SHIPMENT_DATE                         
	
		$po_rec_date_con = " and c.PO_RECEIVED_DATE between '".$previous_date."' and '".$current_date."'";
		
		
		$order_sql="select  b.COMPANY_NAME, B.BUYER_NAME, B.STYLE_REF_NO,c.id as PO_BREAK_DOWN_ID,c.SHIPMENT_DATE,c.PO_RECEIVED_DATE, E.ITEM_NUMBER_ID,c.PO_NUMBER,E.ORDER_QUANTITY from wo_po_details_master b, wo_po_break_down c,wo_po_color_size_breakdown e where c.job_no_mst=b.job_no and e.po_break_down_id=c.id and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3) and c.SHIPING_STATUS!=3   and c.is_deleted=0  and e.status_active in(1,2,3) and e.is_deleted=0 $po_rec_date_con and b.COMPANY_NAME in(".implode(',',array_keys($company_arr)).")"; // and c.id=77734
	   //echo $order_sql;die;
		$order_sql_result=sql_select($order_sql);			 
		foreach($order_sql_result as $rows)
		{
			$po_qty_arr[$rows['COMPANY_NAME']][$rows['BUYER_NAME']][$rows['PO_BREAK_DOWN_ID']][$rows['ITEM_NUMBER_ID']]+=$rows['ORDER_QUANTITY'];
			$po_data_arr[$rows['COMPANY_NAME']][$rows['BUYER_NAME']][$rows['STYLE_REF_NO']][$rows['PO_BREAK_DOWN_ID']][$rows['ITEM_NUMBER_ID']]=array(
				'SHIPMENT_DATE'=>$rows['SHIPMENT_DATE'],
				'PO_RECEIVED_DATE'=>$rows['PO_RECEIVED_DATE'],
				'STYLE_REF_NO'=>$rows['STYLE_REF_NO'],
				'PO_NUMBER'=>$rows['PO_NUMBER'],
			);
			$all_po_arr[$rows['PO_BREAK_DOWN_ID']]=$rows['PO_BREAK_DOWN_ID'];
			$buyer_by_po_arr[$rows['PO_BREAK_DOWN_ID']]=$rows['BUYER_NAME'];
			
		}
		
		
		$production_sql="select  a.ITEM_NUMBER_ID,A.SERVING_COMPANY,A.PO_BREAK_DOWN_ID,d.COLOR_SIZE_BREAK_DOWN_ID,D.PRODUCTION_QNTY from pro_garments_production_mst a,pro_garments_production_dtls d
			where a.production_type = 5 and d.production_type=5 and a.id=d.mst_id and a.status_active=1 and a.is_deleted=0  and d.status_active=1 and d.is_deleted=0 ".where_con_using_array($all_po_arr,0,'A.PO_BREAK_DOWN_ID')."";
	  //echo $production_sql;die;
		$production_sql_result=sql_select($production_sql);			 
		foreach($production_sql_result as $rows)
		{
			$pro_qty_arr[$rows['SERVING_COMPANY']][$buyer_by_po_arr[$rows['PO_BREAK_DOWN_ID']]][$rows['PO_BREAK_DOWN_ID']][$rows['ITEM_NUMBER_ID']]+=$rows['PRODUCTION_QNTY'];
			
		}




		$poLogSql = "select PO_ID,max(ORG_SHIP_DATE) as ORG_SHIP_DATE from WO_PO_UPDATE_LOG where 1=1  ".where_con_using_array($all_po_arr,0,'PO_ID')." group by PO_ID";
		$poLogSqlRes=sql_select($poLogSql);	
		$po_log_data_arr = array();		 
		foreach($poLogSqlRes as $rows)
		{
			$po_log_data_arr[$rows['PO_ID']] = $rows['ORG_SHIP_DATE'];
		}

		


		$tnaSql = "select PO_NUMBER_ID,TASK_FINISH_DATE,ACTUAL_FINISH_DATE from TNA_PROCESS_MST where TASK_TYPE = 6".where_con_using_array($all_po_arr,0,'PO_NUMBER_ID')." and TASK_NUMBER=86";
		//echo $tnaSql;die;
		$tnaSqlRes=sql_select($tnaSql);			 
		foreach($tnaSqlRes as $rows)
		{
			$finish_date_dif = datediff( "d", $pc_date, $rows['TASK_FINISH_DATE'])-1;


			if($finish_date_dif < 2 && $rows['TASK_FINISH_DATE'] != ''){
				
				if($rows['ACTUAL_FINISH_DATE'] == ''){
					$date_dif = datediff( "d", $rows['TASK_FINISH_DATE'], $pc_date)-1;	
				}
				else{
					$date_dif = datediff( "d", $rows['TASK_FINISH_DATE'], $rows['ACTUAL_FINISH_DATE']);
				}
				

				$key = ($date_dif <= 1)?"ontime":"late";


				$tna_data_arr[$rows['PO_NUMBER_ID']][$key] = ucfirst($key);
				$bgcolor_data_arr[$rows['PO_NUMBER_ID']][$key] = ($date_dif <= 1)?"green":"red";
				$date_dif_arr[$rows['PO_NUMBER_ID']][$key] = $date_dif;
			}


			
			
		}
		
 
		
	
	ob_start();	
?>   
         <table cellpadding="0" cellspacing="0" width="950" border="0">
            <tr>
                <td align="left" style="font-size:20px"><strong><?=$group_name;?></strong></td>
                <td align="right" style="font-size:20px"><strong>Inhand Order Status</strong></td>
            </tr>
            <tr>
                <td align="right" colspan="2" style="font-size:16px"><strong>Date: <?=change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', time())),'','',1);?> </strong></td>
            </tr>
        </table>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="950">
            <thead bgcolor="#CCCCCC">

				<tr>
					<th rowspan="2" width="80">Order Recv. Date</th>
					<th rowspan="2">Buyer</th>
					<th rowspan="2">Style</th>
					<th rowspan="2">Order No</th>
					<th rowspan="2">Product Name</th>
					<th rowspan="2" width="80">Original Delivery Date</th>
					<th rowspan="2" width="80">EXT- Delivery  Date</th>
					<th rowspan="2" width="60">Quantity</th>
					<th rowspan="2" width="60">Cut Sewing QTY</th>
					<th rowspan="2" width="60">Sewing Bal</th>
					<th colspan="2">Sewing TNA</th>
					<th rowspan="2">Delay<br>Days</th>
				</tr>
				<tr>
					<th>Ontime</th>
					<th>Late</th>
				</tr>
			</thead>
            <tbody>
            	<? 
				$buyer_total_arr=array();$grand_total_arr=array();
				foreach($po_data_arr as $company_id=>$companyRows){
				?>
                	<tr>
                    	<td colspan="13" bgcolor="#FFFFCC"><strong><?=$company_arr[$company_id];?></strong></td>
                    </tr>
                <?	
				foreach($companyRows as $buyer_id=>$styleRows){
				foreach($styleRows as $style_no=>$buyerRows){
				foreach($buyerRows as $po_id=>$poRows){
				foreach($poRows as $item_id=>$row){
					$i++;
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	
					$row['PO_QTY']=$po_qty_arr[$company_id][$buyer_id][$po_id][$item_id];
					$row['PRODUCTION_QNTY']=$pro_qty_arr[$company_id][$buyer_id][$po_id][$item_id];
					
				 	if(($row['PO_QTY']-$row['PRODUCTION_QNTY'])<0){continue;}
					
					$buyer_style_total_arr['PO_QTY'][$buyer_id][$style_no]+=$row['PO_QTY'];
				 	$buyer_style_total_arr['PRODUCTION_QNTY'][$buyer_id][$style_no]+=$row['PRODUCTION_QNTY'];
				 	$buyer_style_total_arr['BLANCE_QNTY'][$buyer_id][$style_no]+=($row[PO_QTY]-$row['PRODUCTION_QNTY']);
					
					$company_total_arr['PO_QTY'][$company_id]+=$row['PO_QTY'];
				 	$company_total_arr['PRODUCTION_QNTY'][$company_id]+=$row['PRODUCTION_QNTY'];
				 	$company_total_arr['BLANCE_QNTY'][$company_id]+=($row['PO_QTY']-$row['PRODUCTION_QNTY']);
					
				 	$grand_total_arr['PO_QTY']+=$row['PO_QTY'];
				 	$grand_total_arr['PRODUCTION_QNTY']+=$row['PRODUCTION_QNTY'];
				 	$grand_total_arr['BLANCE_QNTY']+=($row['PO_QTY']-$row['PRODUCTION_QNTY']);

					 $row['SHIPMENT_DATE'] = date('d-M-y',strtotime($row['SHIPMENT_DATE']));

					 $row['ORGINAL_SHIPMENT_DATE'] = ($po_log_data_arr[$po_id] != '') ? date('d-M-y',strtotime($po_log_data_arr[$po_id])) : $row['SHIPMENT_DATE'];
					
					
					 $row['EXTENDED_SHIPMENT_DATE'] = ($po_log_data_arr[$po_id] == '' || $row['ORGINAL_SHIPMENT_DATE'] == $row['SHIPMENT_DATE']) ? '' : $row['SHIPMENT_DATE'];
					
				?>
                <tr bgcolor="<?=$bgcolor;?>">
                    <td align="center"><?=$row['PO_RECEIVED_DATE'];?></td>
                    <td><?= $buyer_lib_arr[$buyer_id];?></td>
                    <td><p><?= $row['STYLE_REF_NO'];?></p></td>
                    <td><p><?= $row['PO_NUMBER']; ?></p></td>
                    <td><?= $garments_item[$item_id];?></td>
                    <td align="center"><?= $row['ORGINAL_SHIPMENT_DATE'];?></td>
					<td align="center"><?= $row['EXTENDED_SHIPMENT_DATE'];?></td>
                    <td align="right"><?= $row['PO_QTY'];?></td>
                    <td align="right"><?= $row['PRODUCTION_QNTY'];?></td>
                    <td align="right"><?= $row['PO_QTY']-$row['PRODUCTION_QNTY'];?></td>

					<td align="center" bgcolor="<?= $bgcolor_data_arr[$po_id]['ontime'];?>">
						<?= $tna_data_arr[$po_id]['ontime'];?>
					</td>
					<td bgcolor="<?= $bgcolor_data_arr[$po_id]['late'];?>" align="center">
						<?= $tna_data_arr[$po_id]['late'];?>
					</td>
                    <td align="center"><?= $date_dif_arr[$po_id]['late'];?></td>
                </tr>
                <?
				}}
				?>
                <tr>
                	<td colspan="7" align="right"><strong>Sub Total </strong></td>
                	<td align="right"><?=$buyer_style_total_arr['PO_QTY'][$buyer_id][$style_no];?></td>
                	<td align="right"><?=$buyer_style_total_arr['PRODUCTION_QNTY'][$buyer_id][$style_no];?></td>
                	<td align="right"><?=$buyer_style_total_arr['BLANCE_QNTY'][$buyer_id][$style_no];?></td>
					<td align="right"></td>
                    <td align="right"></td>
                    <td align="right"></td>
                </tr>
                <?
				}}
				
				?>
                <tr bgcolor="#FFF999">
                	<td colspan="7" align="right"><strong>Grand Total </strong></td>
                	<td align="right"><?=$company_total_arr['PO_QTY'][$company_id];?></td>
                	<td align="right"><?=$company_total_arr['PRODUCTION_QNTY'][$company_id];?></td>
                	<td align="right"><?=$company_total_arr['BLANCE_QNTY'][$company_id];?></td>
					<td align="right"></td>
                    <td align="right"></td>
                    <td align="right"></td>
                </tr>
                <?
				}
				?>
            </tbody>
      
		</table>
     
    
 <?
	$message=ob_get_contents();
	ob_clean();

	
	require_once('../../ext_resource/mpdf60/mpdf.php');
	$mpdf = new mPDF('utf-8');
	$mpdf = new mPDF();

	

	//$mpdf->SetCreator("REZA");
	//$mpdf->SetAuthor("Logic Softwer Ltd");
	//$mpdf->SetTitle("Sewing Production Pending Auto Mail");
	//$mpdf->SetSubject("Sewing Production Pending Auto Mail");


	$mpdf->WriteHTML($message);
	foreach (glob("../tmp/"."*.pdf") as $filename) {			
		@unlink($filename);
	}

	$name = 'mail_' . date('j-M-Y_h-iA') . '.pdf';
	$mpdf->Output('../tmp/' . $name, 'F');
	$att_file_arr=array('../tmp/'.$name);
	
 
	$sql = "SELECT a.BRAND_IDS,a.BUYER_IDS,c.email_address as MAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id  and a.mail_item=b.MAIL_ITEM_MST and a.mail_item=94 and b.mail_user_setup_id=c.id and A.IS_DELETED=0 and A.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
	 //echo $sql;die;
	$mail_sql=sql_select($sql);
	foreach($mail_sql as $row)
	{
		$receverMailArr[$row[MAIL]]=$row[MAIL];
	}
	$to=implode(',',array_unique($receverMailArr));
	$subject = "Inhand Order Status Auto Mail [Last 90 days ]";
	$header=mailHeader();
	//echo $message ;
	
	
		if($_REQUEST['isview']==1){
			echo $to.$message;	
		}
		else{
			if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail,$att_file_arr );}
		}

	
	
	
	
	
	
	
?>  


    



    

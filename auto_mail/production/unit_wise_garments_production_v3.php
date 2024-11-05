<?php
date_default_timezone_set("Asia/Dhaka");

require_once('../../includes/common.php');
require_once('../../mailer/class.phpmailer.php');
require_once('../setting/mail_setting.php');
 
 
$company_library = return_library_array( "select id, company_short_name from lib_company where status_active=1 and is_deleted=0", "id", "company_short_name",$con);
$floor_arr = return_library_array( "select id,floor_name from lib_prod_floor where is_deleted=0 and status_active=1", "id", "floor_name");
$lib_buuer = return_library_array( "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1", "id", "buyer_name");

 



 
$lib_location = return_library_array( "SELECT ID,LOCATION_NAME FROM LIB_LOCATION WHERE IS_DELETED=0 AND STATUS_ACTIVE=1", "ID", "LOCATION_NAME");
$lib_color_arr = return_library_array( "SELECT ID,COLOR_NAME FROM LIB_COLOR WHERE IS_DELETED=0 AND STATUS_ACTIVE=1", "ID", "COLOR_NAME");

	
	if($db_type==0)
	{
		$current_date = date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0)));
		if($_REQUEST['view_date']){
			$current_date = change_date_format(date("Y-m-d H:i:s",strtotime($_REQUEST['view_date'])),'','',1);
		}
		$previous_date = date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))); 
	}
	else
	{
		$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);
		if($_REQUEST['view_date']){
			$current_date = change_date_format(date("Y-m-d H:i:s",strtotime($_REQUEST['view_date'])),'','',1);
		}
		$previous_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1);
	}
	

	


//$company_library=array(1=>$company_library[1]);
foreach($company_library as $compid=>$compname)
{
	
	$head_arr=array(101=>"Cut and Lay Qty",1=>"Cutting QC",222=>'Printing',315=>'Embroidery',4=>"Sewing In",5=>"Sewing Out",7=>"Iron",15=>'Hang Tag',11=>"Poly",8=>"Packing",100=>"Ex-Factory");
	$color_arr=array(101=>"#B99",1=>"#CCC",222=>'#FF00FF',315=>'#FFAAff',4=>"#FFF777",5=>"#FFFAAA",7=>"#CCC",15=>"#F79AAA",11=>"#FFFAAA",8=>"#CCC",100=>"#FFFAAA");

 	
	$sql_result="select A.LOCATION,A.COMPANY_ID,B.TOTAL_LEFT_OVER_RECEIVE,A.GOODS_TYPE,A.LEFTOVER_DATE  from PRO_LEFTOVER_GMTS_RCV_MST a,PRO_LEFTOVER_GMTS_RCV_DTLS b where a.id=b.mst_id and a.WORKING_COMPANY_ID=$compid and a.LEFTOVER_DATE between '$previous_date' and '$previous_date' and a.GOODS_TYPE in(1,2)";
	   //echo $sql_result;	
	$sql_dtls=sql_select($sql_result);
	$left_over_qty=array();
	foreach ( $sql_dtls as $row )
	{
		$left_over_qty[$row['LOCATION']][$row['GOODS_TYPE']] +=$row['TOTAL_LEFT_OVER_RECEIVE'];
	}

	
	
	
	$production_date= " and a.production_date between '".$previous_date."' and '".$previous_date."'";
	$sql_result="Select a.LOCATION, a.PRODUCTION_TYPE, a.FLOOR_ID, a.production_date, b.production_qnty as production_qnty from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and a.production_type in (1,4,5,8,7,11,15) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.SERVING_COMPANY=$compid $production_date and a.FLOOR_ID<>0 and a.LOCATION<>0";//1,
	   // echo $sql_result;	
	$sql_dtls=sql_select($sql_result);
	$floor_qnty=array();
	$floorArr=array();
	$locationArr=array();
	
	foreach ( $sql_dtls as $row )
	{
		$floor_qnty[$row['LOCATION']][$row['PRODUCTION_TYPE']][$row['FLOOR_ID']] +=$row['PRODUCTION_QNTY'];
		$floorArr[$row['PRODUCTION_TYPE']][$row['FLOOR_ID']]=$row['FLOOR_ID'];
		$locationArr[$row['LOCATION']]=$row['LOCATION'];
	}



		$cut_date= " and a.ENTRY_DATE between '".$previous_date."' and '".$previous_date."'";
		$bundle_sql="select a.cutting_no, a.LOCATION_ID, a.FLOOR_ID, c.size_qty
		from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle c  where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.WORKING_COMPANY_ID=$compid $cut_date";//and a.id in($all_cut_id)
		  //echo $bundle_sql;
		$bundle_sql_res = sql_select($bundle_sql);
		
		foreach($bundle_sql_res as $row)
		{
			$floorArr[101][$row['FLOOR_ID']]=$row['FLOOR_ID'];
			$locationArr[$row['LOCATION_ID']]=$row['LOCATION_ID'];
			$floor_qnty[$row['LOCATION_ID']][101][$row['FLOOR_ID']]+=$row[csf('size_qty')];
		}



	
 	$deliverySql="select a.DELIVERY_LOCATION_ID,A.DELIVERY_FLOOR_ID,a.DELIVERY_DATE, A.DELIVERY_COMPANY_ID,A.DELIVERY_LOCATION_ID,B.EX_FACTORY_QNTY  from PRO_EX_FACTORY_DELIVERY_MST a, PRO_EX_FACTORY_MST b where a.ID=B.DELIVERY_MST_ID and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 and B.IS_DELETED=0 and B.STATUS_ACTIVE=1 and A.DELIVERY_COMPANY_ID=$compid and a.DELIVERY_DATE between '$previous_date' and '$previous_date' and a.DELIVERY_LOCATION_ID<>0";	
	$deliverySqlResult=sql_select($deliverySql);
	foreach ( $deliverySqlResult as $row )
	{
		$floor_qnty[$row['DELIVERY_LOCATION_ID']][100][$row['DELIVERY_FLOOR_ID']] +=$row['EX_FACTORY_QNTY'];
		$floorArr[100][$row['DELIVERY_FLOOR_ID']]=$row['DELIVERY_FLOOR_ID'];
		$locationArr[$row['DELIVERY_LOCATION_ID']]=$row['DELIVERY_LOCATION_ID'];
	}
	//print ($deliverySql);
	
	
	$sub_pro_date= " and b.PRODUCTION_DATE between '".$previous_date."' and '".$previous_date."'";
 	$subProSql="select a.COMPANY_ID,a.ENTRY_FORM,a.PRODUCT_DATE,a.LOCATION_ID,a.FLOOR_ID,b.QCPASS_QTY from SUBCON_EMBEL_PRODUCTION_MST a, SUBCON_EMBEL_PRODUCTION_DTLS b where a.id=b.mst_id and a.STATUS_ACTIVE=1 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and a.IS_DELETED=0 and a.COMPANY_ID=$compid $sub_pro_date and a.ENTRY_FORM in(222,315)";	
	
	//echo $subProSql;
	
	$subProSqlResult=sql_select($subProSql);
	foreach ( $subProSqlResult as $row )
	{
		$floor_qnty[$row['LOCATION_ID']][$row['ENTRY_FORM']][$row['FLOOR_ID']] +=$row['QCPASS_QTY'];
		$floorArr[$row['ENTRY_FORM']][$row['FLOOR_ID']]=$row['FLOOR_ID'];
		$locationArr[$row['LOCATION_ID']]=$row['LOCATION_ID'];
	}
	//print ($deliverySql);
	
	
	
	//$excess_data_arr
	//$cutting_order_id_arr[$row[JOB_NO]]
	
		
		
		
	$order_sql="select c.JOB_NO_MST,a.STYLE_REF_NO,A.BUYER_NAME,b.id as ORDER_ID,b.PO_NUMBER, c.COLOR_NUMBER_ID, 
	
	c.EXCESS_CUT_PERC AS EXCESS_CUT_PERC,
	
	 (c.ORDER_QUANTITY) AS ORDER_QUANTITY from WO_PO_DETAILS_MASTER a,WO_PO_BREAK_DOWN b, WO_PO_COLOR_SIZE_BREAKDOWN c where b.SHIPING_STATUS<3 and a.COMPANY_NAME=$compid and a.id=b.job_id and a.id=c.job_id and b.id=c.PO_BREAK_DOWN_ID and A.status_active=1 and A.is_deleted=0 AND B.status_active=1 and B.is_deleted=0 AND C.status_active=1 and C.is_deleted=0";
		  // echo $order_sql;die;
	$order_sql_res=sql_select($order_sql);
	$excess_allow_data_arr=array();$tmp_po_id_arr=array();
	foreach ( $order_sql_res as $row )
	{
 			if($row[EXCESS_CUT_PERC]>0){
				$row[PLAN_CUT_QNTY]=(($row[EXCESS_CUT_PERC]*$row[ORDER_QUANTITY])/100)+$row[ORDER_QUANTITY];
			}
			else{
				$row[PLAN_CUT_QNTY]=$row[ORDER_QUANTITY];
			}
			
			
			
			$key=$row[JOB_NO_MST].'**'.$row[ORDER_ID].'**'.$row[COLOR_NUMBER_ID];
			$excess_allow_data_arr[$key][PO_QTY]+=$row[ORDER_QUANTITY];
			$excess_allow_data_arr[$key][EXCESS_ALLOW_CUT_QTY]+=ceil($row[PLAN_CUT_QNTY]);
			
			$style_by_job_arr[$row[JOB_NO_MST]]=$row[STYLE_REF_NO];
			$buyer_by_job_arr[$row[JOB_NO_MST]]=$row[BUYER_NAME];
			$tmp_po_id_arr[$row[ORDER_ID]]=$row[PO_NUMBER];
			
	}
	
	unset($order_sql_res);	
		
		$excess_cut_sql="select a.JOB_NO,a.FLOOR_ID,C.ORDER_ID, b.COLOR_ID, sum(c.SIZE_QTY) AS CUTTING_QTY from PPL_CUT_LAY_MST a,PPL_CUT_LAY_DTLS b,PPL_CUT_LAY_BUNDLE c where a.id=b.mst_id and a.id=c.mst_id and b.id=c.DTLS_ID  AND A.status_active=1 and A.is_deleted=0 AND B.status_active=1 and B.is_deleted=0 AND C.status_active=1 and C.is_deleted=0 and a.COMPANY_ID=$compid ".where_con_using_array(array_keys($tmp_po_id_arr),0, 'c.ORDER_ID')." group by a.JOB_NO,a.FLOOR_ID,C.ORDER_ID, b.COLOR_ID";
		 
	$excess_cut_sql_res=sql_select($excess_cut_sql);
	$excess_data_arr=array();
	foreach ( $excess_cut_sql_res as $row )
	{
		
		$key=$row[JOB_NO].'**'.$row[ORDER_ID].'**'.$row[COLOR_ID];
		if($row[CUTTING_QTY]-$excess_allow_data_arr[$key][EXCESS_ALLOW_CUT_QTY]>0 && $excess_allow_data_arr[$key][EXCESS_ALLOW_CUT_QTY]>0){
			
			$excess_data_arr[$key][TOTAL_CUTTING_QTY]=$row[CUTTING_QTY];
			$excess_data_arr[$key][FLOOR_ID]=$row[FLOOR_ID];
			$excess_data_arr[$key][VARIANCE]=$row[CUTTING_QTY]-$excess_allow_data_arr[$key][EXCESS_ALLOW_CUT_QTY];
			$excess_data_arr[$key][PO_QTY]=$excess_allow_data_arr[$key][PO_QTY];
			$excess_data_arr[$key][EXCESS_ALLOW_CUT_QTY]=$excess_allow_data_arr[$key][EXCESS_ALLOW_CUT_QTY];
		}
	}
	

	
	
	$count_data=count($floorArr[101])+count($floorArr[1])+count($floorArr[5])+count($floorArr[8])+count($floorArr[7])+count($floorArr[11]) +count($floorArr[100]) +count($floorArr[222]) +count($floorArr[315]) +count($floorArr[15]);

     $width=($count_data*80)+(count($head_arr)*80)+200;
	 
	 
		ob_start();	

		?>
		<div id="scroll_body" align="center" style="height:auto; width:<?= $width;?>px; margin:0 auto; padding:0;">
	        <table width="100%" cellpadding="0" cellspacing="0" id="caption" align="center">
	            <tr>
	               <td align="center" width="100%" colspan="<?= $count_data; ?>" class="form_caption" >
                   	<strong style="font-size:18px"><?= $company_library[$compid];?></strong>
	               </td>
	            </tr> 
	            <tr>  
	               <td align="center" width="100%" colspan="<?= $count_data; ?>" class="form_caption" ><strong style="font-size:14px">Unit Wise Garments  Production</strong></td>
	            </tr>
	            <tr>  
	               <td align="center" width="100%" colspan="<?= $count_data; ?>" class="form_caption" ><strong style="font-size:14px"> <?= change_date_format($previous_date);?></strong></td>
	            </tr>  
	        </table>
	     
	        <div align="center" style="height:auto; width:<?= $width;?>px;">
	        <table border="1" width="100%" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
	            <thead>
	                <tr>
	                    <th width="35" rowspan="2">SL</th>
	                    <th width="100" rowspan="2">Location</th>
	                    <?
	                     foreach ( $head_arr as $head=>$headval )
						 {
	                        ?>
	                            <th bgcolor="<?= $color_arr[$head];?>" colspan="<? echo count($floorArr[$head])+1; ?>"><? echo $headval; ?></th>
	                        <?
						 }
						?>
                        <th colspan="2">Left Over Garments</th>
	                </tr>
	               <tr>
						<?
						foreach ( $head_arr as $head=>$headval )
						{
							foreach ( $floorArr[$head] as $floor_id )
							{
								?>
									<th bgcolor="<?= $color_arr[$head];?>" width="80" ><small><? echo $floor_arr[$floor_id]; ?></small></th>
								<?
							}
							echo '<th bgcolor="'.$color_arr[$head].'" width="80">Total</th>';
						}
						?>
                        <th width="80">Total Goods</th>
                        <th width="80">Total Damage</th>
	               </tr>
	            </thead>
	            <tbody>
				
	            <?
				$i=1;
				foreach($locationArr as $location_id){
					$bgcolor=($i%2==0)?"#ffffff":"#D7E8FF";
					?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $j; ?>">
	                <td align="center"><?= $i++; ?></td>
	                <td><?= $lib_location[$location_id]; ?></td>
	                <?
	                foreach ( $head_arr as $head=>$headval )
	                {
						foreach ( $floorArr[$head] as $floor_id )
						{ 
							?>
								<td align="right"><?  echo number_format($floor_qnty[$location_id][$head][$floor_id],0); ?></td>
							<?
						}
						?>
							<td bgcolor="<?= $color_arr[$head];?>" align="right"><strong><? echo number_format(array_sum($floor_qnty[$location_id][$head]),0); ?></strong></td>
						<?
	                }
	                ?>
                        <td align="right"><?= $left_over_qty[$location_id][1];?></td>
                        <td align="right"><?= $left_over_qty[$location_id][2];?></td>
	            </tr>
                <?
				}
				?>
	         </tbody>    
	        </table>
            
            <br />
            <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
            	<tr><th colspan="12">Daily Cutting Status of exceed of  Excess Cut %</th></tr>
                <tr bgcolor="#999999">
                	<th>Sl</th>
                    <th>Job No</th>
                    <th>Buyer</th>
                    <th>Cutting Floor</th>
                    <th>Style No</th>
                    <th>PO No</th>
                    <th>Color</th>
                    <th>PO Qty</th>
                    <th>Excess Allowable Cut Qty</th>
                    <th>Total Cutting Qty</th>
                    <th>%</th>
                    <th>Variance</th>
                </tr>
                <?
				$i=1;
				foreach($excess_data_arr as $key=>$row)
				{

					list($row['JOB_NO'],$row['PO_ID'],$row['COLOR_ID'])=explode('**',$key);
					$row[PO_NUMBER]=$tmp_po_id_arr[$row['PO_ID']];
					$row[STYLE_REF_NO]=$style_by_job_arr[$row['JOB_NO']];
					$row[BUYER_ID]=$buyer_by_job_arr[$row['JOB_NO']];
					$bgcolor=($i%2==0)?"#ffffff":"#D7E8FF";
				?>
            	<tr bgcolor="<? echo $bgcolor; ?>">
                	<td align="center"><?=$i;?></td>
                    <td><?=$row['JOB_NO'];?></td>
                    <td><?=$lib_buuer[$row['BUYER_ID']];?></td>
                    <td><?=$floor_arr[$row['FLOOR_ID']];?></td>
                    <td><?=$row['STYLE_REF_NO'];?></td>
                    <td><?=$row['PO_NUMBER'];?></td>
                    <td><?=$lib_color_arr[$row['COLOR_ID']];?></td>
                    <td align="right"><?=$row['PO_QTY'];?></td>
                    <td align="right"><?=$row['EXCESS_ALLOW_CUT_QTY'];?></td>
                    <td align="right"><?=$row['TOTAL_CUTTING_QTY'];?></td>
                    <td align="right"><?=number_format(($row['TOTAL_CUTTING_QTY']*100)/$row['PO_QTY'],2);?></td>
                    <td align="right" style="background-color:red;"><?=$row['VARIANCE'];?></td>
                </tr>
                <?
				$i++;
				}
				?>
            
            </table>
	   
	    </div>
	   </div>
	    <?
		
		$message.=ob_get_contents();
		ob_clean();
 
	}
	

	$to='';
	$sql2 = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=33 and b.mail_user_setup_id=c.id  and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
	$mail_sql2=sql_select($sql2, '', '', '', $con);
	foreach($mail_sql2 as $row)
	{
		if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
	}

 
 	//$to='muktobani@gmail.com';
	$subject="Unit Wise Garments Production With Excess Cut";
	$header=mailHeader();
	if($_REQUEST['isview']==1){
		$mail_item=33;
		if($to){
			echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
		}else{
			echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
		}
		echo $message;
	}
	else{
		if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail );}
	}

	// echo $message;	
	

	
		
?>
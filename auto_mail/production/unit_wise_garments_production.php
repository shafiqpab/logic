<?php
date_default_timezone_set("Asia/Dhaka");

require_once('../../includes/common.php');
require_once('../../mailer/class.phpmailer.php');
require_once('../setting/mail_setting.php');
 
 
$company_library = return_library_array( "select id, company_short_name from lib_company where status_active=1 and is_deleted=0", "id", "company_short_name",$con);
$floor_arr = return_library_array( "select id,floor_name from lib_prod_floor where is_deleted=0 and status_active=1", "id", "floor_name");
 
$lib_location = return_library_array( "SELECT ID,LOCATION_NAME FROM LIB_LOCATION WHERE IS_DELETED=0 AND STATUS_ACTIVE=1", "ID", "LOCATION_NAME");


	
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
	

	
	//$previous_date="2-Jan-2020";
	//$previous_date="2-Jan-2020";
	


//$company_library=array(1=>$company_library[1]);
foreach($company_library as $compid=>$compname)
{
	
	$head_arr=array(1=>"Cutting Production",5=>"Sewing Production",7=>"Iron",11=>"Poly",8=>"Packing",100=>"Ex-Factory");
	$color_arr=array(1=>"#CCC",5=>"#FFFAAA",7=>"#CCC",11=>"#FFFAAA",8=>"#CCC",100=>"#FFFAAA");
	
	
	
	$sql_result="select A.LOCATION,A.COMPANY_ID,B.TOTAL_LEFT_OVER_RECEIVE,A.GOODS_TYPE,A.LEFTOVER_DATE  from PRO_LEFTOVER_GMTS_RCV_MST a,PRO_LEFTOVER_GMTS_RCV_DTLS b where a.id=b.mst_id and a.WORKING_COMPANY_ID=$compid and a.LEFTOVER_DATE between '$previous_date' and '$current_date' and a.GOODS_TYPE in(1,2)";
	   //echo $sql_result;	
	$sql_dtls=sql_select($sql_result);
	$left_over_qty=array();
	foreach ( $sql_dtls as $row )
	{
		$left_over_qty[$row[LOCATION]][$row[GOODS_TYPE]] +=$row[TOTAL_LEFT_OVER_RECEIVE];
	}

	
	
	
	$production_date= " and a.production_date between '".$previous_date."' and '".$current_date."'";

	$sql_result="Select a.LOCATION, a.PRODUCTION_TYPE, a.FLOOR_ID, a.production_date, b.production_qnty as production_qnty from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and a.production_type in (5,8,7,11) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.SERVING_COMPANY=$compid $production_date and a.FLOOR_ID<>0 and a.LOCATION<>0";//1,
	   // echo $sql_result;	
	$sql_dtls=sql_select($sql_result);
	$floor_qnty=array();
	$floorArr=array();
	$locationArr=array();
	
	foreach ( $sql_dtls as $row )
	{
		$floor_qnty[$row[LOCATION]][$row[PRODUCTION_TYPE]][$row[FLOOR_ID]] +=$row[PRODUCTION_QNTY];
		$floorArr[$row[PRODUCTION_TYPE]][$row[FLOOR_ID]]=$row[FLOOR_ID];
		$locationArr[$row[LOCATION]]=$row[LOCATION];
	}



		$cut_date= " and a.ENTRY_DATE between '".$previous_date."' and '".$current_date."'";
		$bundle_sql="select a.cutting_no, a.LOCATION_ID, a.FLOOR_ID, c.size_qty
		from ppl_cut_lay_mst a, ppl_cut_lay_dtls b, ppl_cut_lay_bundle c  where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.WORKING_COMPANY_ID=$compid $cut_date";//and a.id in($all_cut_id)
		 //echo $bundle_sql;die();
		$bundle_sql_res = sql_select($bundle_sql);
		foreach($bundle_sql_res as $row)
		{
			$floorArr[1][$row[FLOOR_ID]]=$row[FLOOR_ID];
			$locationArr[$row[LOCATION_ID]]=$row[LOCATION_ID];
			$floor_qnty[$row[LOCATION_ID]][1][$row[FLOOR_ID]]+=$row[csf('size_qty')];
		}



	
 	$deliverySql="select a.DELIVERY_LOCATION_ID,A.DELIVERY_FLOOR_ID,a.DELIVERY_DATE, A.DELIVERY_COMPANY_ID,A.DELIVERY_LOCATION_ID,B.EX_FACTORY_QNTY  from PRO_EX_FACTORY_DELIVERY_MST a, PRO_EX_FACTORY_MST b where a.ID=B.DELIVERY_MST_ID and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 and B.IS_DELETED=0 and B.STATUS_ACTIVE=1 and A.DELIVERY_COMPANY_ID=$compid and a.DELIVERY_DATE between '$previous_date' and '$current_date' and a.DELIVERY_LOCATION_ID<>0";	
	$deliverySqlResult=sql_select($deliverySql);
	foreach ( $deliverySqlResult as $row )
	{
		$floor_qnty[$row[DELIVERY_LOCATION_ID]][100][$row[DELIVERY_FLOOR_ID]] +=$row[EX_FACTORY_QNTY];
		$floorArr[100][$row[DELIVERY_FLOOR_ID]]=$row[DELIVERY_FLOOR_ID];
		$locationArr[$row[DELIVERY_LOCATION_ID]]=$row[DELIVERY_LOCATION_ID];
	}
	//print ($deliverySql);
	
	$count_data=count($floorArr[1])+count($floorArr[5])+count($floorArr[8])+count($floorArr[7])+count($floorArr[11]) +count($floorArr[100]);

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
									<th bgcolor="<?= $color_arr[$head];?>" width="80" ><? echo $floor_arr[$floor_id]; ?></th>
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
	   
	    </div>
	   </div>
	    <?
		
		
	$message=ob_get_contents();
	ob_clean();

	$to='';
	$mail_item=33;
	$sql2 = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=33 and b.mail_user_setup_id=c.id and a.company_id =$compid AND a.MAIL_TYPE=1";
	$mail_sql2=sql_select($sql2, '', '', '', $con);
	foreach($mail_sql2 as $row)
	{
		if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
	}


 	//$to='muktobani@gmail.com';
	$subject="Unite Wise Garments Production";
	$header=mailHeader();
	//

		if($_REQUEST['isview']==1){
			if($to){
				echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
			}else{
				echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
			}
			echo $to.$message;
		}
		else{
			if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail );}
		}
		

	}
		

	
		
?>
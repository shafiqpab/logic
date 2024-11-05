<?php
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Daily Ex-factory Schedule SMS
Functionality	:	
JS Functions	:
Created by		:	Md Mamun Ahmed Sagor 
Creation date 	: 	28-08-2021
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/




date_default_timezone_set("Asia/Dhaka");
require_once('../includes/common.php');
require_once('setting/sms_setting.php');


$company_lib =return_library_array( "select id, COMPANY_SHORT_NAME from lib_company where status_active=1 and is_deleted=0", "id", "COMPANY_SHORT_NAME");
$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
// $set_item_ratio_arr=return_library_array( "select job_no, set_item_ratio from wo_po_details_mas_set_details",'job_no','set_item_ratio');
$company_ids = implode(',',array_keys($company_lib));

$current_date = change_date_format(date("Y-m-d H:i:s",time()),'','',1);



//====ref information=============
// $current_date="26-Aug-2021";
// $job_no='RpC-21-00318','RpC-21-00319';
//====end=========================

 $next_date = change_date_format(date('Y-m-d H:i:s', strtotime('+1 day', strtotime($current_date))),'','',1);
// echo $current_date."=>".$previous_date;


//............................................................main query Start..................................;
$recipient_number_arr=getSMSRecipient(array(item=>3));
	$smsBody="";

		foreach($company_lib as $company_id=>$company_name){

			$set_ratio_sql="select a.buyer_name,a.job_no,a.order_uom,b.set_item_ratio from wo_po_details_master a,wo_po_details_mas_set_details b where  a.job_no=b.job_no  and a.status_active=1 ";
			$set_sql_data=sql_select($set_ratio_sql);

			foreach($set_sql_data as $row){
				$job_wise_set_ratio[$row[csf('buyer_name')]][$row[csf('job_no')]]['set_ratio'] +=$row[csf('set_item_ratio')];
			}
			unset($set_sql_data);


				$sql="select a.buyer_name,a.job_no,b.id,b.po_number,b.pub_shipment_date,b.shipment_date,b.po_quantity,b.unit_price,b.po_total_price,b.excess_cut,b.plan_cut,a.set_break_down,a.order_uom,a.company_name,a.JOB_NO_PREFIX_NUM from wo_po_details_master a,wo_po_break_down b where    a.job_no=b.job_no_mst and to_date(to_char(b.shipment_date, 'DD-MON-YYYY')) BETWEEN '$current_date' AND '$next_date' and a.company_name=$company_id and b.IS_CONFIRMED=1 and b.shiping_status!=3 and b.status_active=1 and a.status_active=1 and b.status_active=1 order by b.shipment_date asc";

				//echo $sql;
			
				$sql_data=sql_select($sql);
				$com_dateArr[$company_id]=array(0=>$current_date,1=>$next_date);

				

				
				$company_buyer_wise_data=array();
				$i=0;
				foreach($sql_data as $row){
					
					
					$company_buyer_wise_data[$row[csf('company_name')]][$row[csf('shipment_date')]][$row[csf('buyer_name')]]['order_qnty'] +=$row[csf('po_quantity')]*$job_wise_set_ratio[$row[csf('buyer_name')]][$row[csf('job_no')]]['set_ratio'];			
					$company_buyer_wise_data[$row[csf('company_name')]][$row[csf('shipment_date')]][$row[csf('buyer_name')]]['total_amount'] +=$row[csf('po_total_price')];

					$job_wise_data[$row[csf('company_name')]][$row[csf('shipment_date')]][$row[csf('buyer_name')]][$row[csf('po_number')]]=$row[csf('po_number')];
					
					
					$job_arr[$row[csf('job_no')]]=$row[csf('job_no')];
					$buyer_job_arr[$row[csf('company_name')]][$row[csf('shipment_date')]][$row[csf('buyer_name')]][$row[csf('job_no')]]=$row[csf('JOB_NO_PREFIX_NUM')];
					$buyer_po_arr[$row[csf('company_name')]][$row[csf('shipment_date')]][$row[csf('buyer_name')]][$row[csf('po_number')]]=$row[csf('po_number')];
					$company_buyer_arr[$row[csf('company_name')]][$row[csf('shipment_date')]][$row[csf('buyer_name')]][$row[csf('job_no')]]=$row[csf('job_no')];
					$com_date_arr[$row[csf('company_name')]][$row[csf('shipment_date')]]=$row[csf('shipment_date')];
				}
				// unset($sql_data);
			
			
				// 
			
				// echo implode(",",$buyer_job_arr[$row[csf('company_name')]][$row[csf('buyer_name')]]);
	//............................................................main query end................................................;



			

	//............................................................SMS Send Activities........................................;

					//   echo "<pre>";
					//    print_r($company_buyer_wise_data);
					$smsBody="";
				
					foreach($company_buyer_wise_data[$company_id] as $date=>$buyer_data){								
									$smsBody.=$company_name." Ex-factory Schedule:".$date."\n\n";
												
								foreach($buyer_data as $buyer_id=>$val){		
									$smsBody.="Buyer:".$buyer_arr[$buyer_id]."\n\n";													
									$smsBody.="Job No:".implode(",",$buyer_job_arr[$company_id][$date][$buyer_id])."\n";	
									$smsBody.="PO Count:".count($buyer_po_arr[$company_id][$date][$buyer_id])."\n";	
									$smsBody.="Qnty(Pcs):".number_format($val['order_qnty'], 0,'.',',')."\n";
									$smsBody.="Values:$".number_format($val['total_amount'], 2,'.',',')."\n\n";
											
									$total_qnty+=$val['order_qnty'];
									$total_value+=$val['total_amount'];
					            }
								$smsBody.="Grand Total Qnty(Pcs):".number_format($total_qnty, 0,'.',',')."\n";
								$smsBody.="Grand Total Values:$".number_format($total_value, 2,'.',',')."\n";
								$smsBody.=".........................."."\n";
								$total_qnty=0;
								$total_value=0;
				    	}
						// echo "<pre>";
						//  echo $smsBody."<br>";
					
					

						
							$mobile_number_arr=array();
							foreach($recipient_number_arr[$company_id] as $buyerRows){
								foreach($buyerRows as $brandRows){
									foreach($brandRows as $number){
										$mobile_number_arr[]=$number;
									}
								}
							}
						
							// array('01975643095')


					if(count($sql_data)<=0){
						foreach($com_dateArr[$company_id] as $date){
							$smsBody.=$company_name." Ex-factory Schedule:".$date."\n\n";							
							$smsBody.=" Data Not Found"."\n";
							$smsBody.="........................"."\n\n";
						 }
				    }

					$sms="SMS Generated On :".date('d-M-Y h:i:s A',time())."\n".$smsBody;

					echo "<pre>";
					echo $sms."<br>";
					
							//   $mobile_number_arr=array('01975643095');
			//	sendSMS($mobile_number_arr,$sms);//array('01511100004,01709632668,01975643095')

    }

	
 // sendSMS($mobile_number_arr,$smsBody);//array('01511100004,01709632668,01975643095')
//check link ............(http://localhost/platform-v3.5/SMS/daily_ex_factory_schedule_sms.php)...............;
//check link ............(http://59.152.60.149:8091/platform-v3.5/SMS/daily_ex_factory_schedule_sms.php)...............;
//check ...................(http://erp.norbangroup.com/erp/SMS/daily_ex_factory_schedule_sms.php)











?>
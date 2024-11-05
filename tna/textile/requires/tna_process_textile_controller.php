<?
/*--------------------------------------------Comments----------------
Version (MySql)			:  V2
Version (Oracle)		:  V1
Developed by			:  Md. Saidul Islam Reza
Developed Date			:  1.05.2018
Purpose					: 	
Functionality			:	
JS Functions			: 
Requirment Client		:  
Requirment By			: 
Requirment type			: 
Requirment				: 
Affected page			: 
Affected Code			:                   
DB Script				: 
Updated by				:  		
Update date				:  		   
QC Performed BY			:	
QC Date					:	
Comments				:  From this version oracle conversion is start
----------------------------------------------------------------------*/
header('Content-type:text/html; charset=utf-8');
session_start();
extract ( $_REQUEST );

if($action==""){
	$action="tna_process";
	$cornd_service=true;
}
$gross_level=0;


include('../../../includes/common.php');

if( $action=="load_drop_down_buyer" )
{	
	echo create_drop_down( "cbo_buyer", 170, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );  
	die; 	 
}

if( $cbo_company<1 ) $company_array=return_library_array( "select id,id from lib_company",'id','id' );
else $company_array[$cbo_company]=$cbo_company;
 
if ( $action=="tna_process" )
{
	
	
	
 
	foreach( $company_array as $cbo_company )
	{
		$tna_process_type=return_field_value("tna_process_type"," variable_order_tracking"," company_name=".$cbo_company." and variable_list=31"); 
		$tna_process_start_date=return_field_value("tna_process_start_date"," variable_order_tracking"," company_name=".$cbo_company." and variable_list=43"); 
		$textile_tna_process_base=return_field_value("textile_tna_process_base"," variable_order_tracking"," company_name=".$cbo_company." and variable_list=62"); 
		
	
		
		
		if($tna_process_type=='' || $tna_process_start_date=='' || $textile_tna_process_base==''){
			echo "Not config, Company:$cbo_company ";continue;
		}
		
		
		
		if( $tna_process_type==2 )//parcent
		{
			
			$sql = "SELECT task_name,completion_percent FROM  lib_tna_task WHERE is_deleted = 0 and status_active=1 order by task_name asc";
			$result = sql_select( $sql );
			foreach( $result as $row ) 
			{
				$tna_completion[$row[csf('task_name')]]=$row[csf('completion_percent')];
			}			
			
			
			$sql = "SELECT task_id,buyer_id,start_percent,end_percent,notice_before FROM  tna_task_entry_percentage WHERE is_deleted = 0 and status_active=1 order by task_id asc";
			$result = sql_select( $sql );
			$tna_task_percent = array();
			$tna_task_percent_buyer = array();
			foreach( $result as $row ) 
			{
				$tna_task_percent[$row[csf('task_id')]]['task_name']=$row[csf('task_id')];
				$tna_task_percent[$row[csf('task_id')]]['buyer_id']=$row[csf('buyer_id')];
				$tna_task_percent[$row[csf('task_id')]]['start_percent']=$row[csf('start_percent')];
				$tna_task_percent[$row[csf('task_id')]]['end_percent']=$row[csf('end_percent')];
				$tna_task_percent[$row[csf('task_id')]]['notice_before']=$row[csf('notice_before')];
				$tna_task_percent[$row[csf('task_id')]]['completion_percent']=$tna_completion[$row[csf('task_id')]];
				
				$tna_task_percent_buyer_wise[$row[csf('buyer_id')]]=$row[csf('buyer_id')];
				$tna_task_percent_buyer[$row[csf('buyer_id')]][$row[csf('task_id')]]['task_name']=$row[csf('task_id')];
				$tna_task_percent_buyer[$row[csf('buyer_id')]][$row[csf('task_id')]]['buyer_id']=$row[csf('buyer_id')];
				$tna_task_percent_buyer[$row[csf('buyer_id')]][$row[csf('task_id')]]['start_percent']=$row[csf('start_percent')];
				$tna_task_percent_buyer[$row[csf('buyer_id')]][$row[csf('task_id')]]['end_percent']=$row[csf('end_percent')];
				$tna_task_percent_buyer[$row[csf('buyer_id')]][$row[csf('task_id')]]['notice_before']=$row[csf('notice_before')];
				$tna_task_percent_buyer[$row[csf('buyer_id')]][$row[csf('task_id')]]['completion_percent']=$tna_completion[$row[csf("task_id")]];
			} 
		}
		else if($tna_process_type==1)//Template
		{
			$sql = "SELECT id,task_catagory,task_name,task_short_name,task_type,module_name,link_page,penalty,completion_percent FROM lib_tna_task WHERE is_deleted = 0 and status_active=1";
			$result = sql_select( $sql ) ;
			$tna_task_details = array();
			$tna_task_name=array();
			$tna_task_name_tmp=array();
			foreach( $result as $row ) 
			{
				if(empty($row[csf('completion_percent')])){$row[csf('completion_percent')]=100;}
				
				$tna_task_name[$row[csf('id')]]=$row[csf('task_name')];
				$tna_task_details[$row[csf('task_name')]]['task_catagory']=  $row[csf('task_catagory')];
				$tna_task_details[$row[csf('task_name')]]['id']=  $row[csf('id')];
				$tna_task_details[$row[csf('task_name')]]['task_name']=  $row[csf('task_name')];
				$tna_task_details[$row[csf('task_name')]]['task_short_name']=  $row[csf('task_short_name')];
				$tna_task_details[$row[csf('task_name')]]['task_type']=  $row[csf('task_type')];
				$tna_task_details[$row[csf('task_name')]]['module_name']=  $row[csf('module_name')];
				$tna_task_details[$row[csf('task_name')]]['link_page']=  $row[csf('link_page')];
				$tna_task_details[$row[csf('task_name')]]['penalty']=  $row[csf('penalty')];
				$tna_task_details[$row[csf('task_name')]]['completion_percent']= $row[csf('completion_percent')];
				$tna_completion[$row[csf('task_name')]]=$row[csf('completion_percent')];
			}
		}
		
		
		 //Template Details
			$sql_task = "SELECT a.ID,TASK_TEMPLATE_ID,LEAD_TIME,MATERIAL_SOURCE,TOTAL_TASK,TNA_TASK_ID,DEADLINE,EXECUTION_DAYS,NOTICE_BEFORE,SEQUENCE_NO,FOR_SPECIFIC,B.TASK_CATAGORY,B.TASK_NAME,A.TASK_TYPE FROM  tna_task_template_details a, lib_tna_task b WHERE   a.tna_task_id=b.task_name and a.is_deleted=0 and a.status_active=1 and a.task_type=2 and b.task_type=2 and b.is_deleted=0 and b.status_active=1 and a.company_id in(".implode(',',$company_array).",0) order by for_specific,lead_time";
			
			 //echo $sql_task;die;
			
			$result = sql_select( $sql_task ) ;
			$template_wise_task = array();
			$tna_task_template = array();
			$tna_task_template_task=array();
			$tna_template = array();
			$tna_template_buyer = array(); 
			$i=0;
			$k=0;
			$j=0;
			$template_information=array();
			$m=0;
			$n=0;
			foreach( $result as $row ) 
			{
				if($template[$row['TASK_TEMPLATE_ID']]=='')
				{
					$template[$row['TASK_TEMPLATE_ID']]=$row['TASK_TEMPLATE_ID'];
					//if($row[FOR_SPECIFIC]==3) $row[FOR_SPECIFIC]=0;
					if ( $row['FOR_SPECIFIC']==0 )
					{
						$tna_template[$m]['lead']=$row['LEAD_TIME'];
						$tna_template[$m]['id']=$row['TASK_TEMPLATE_ID'];
						$i++;
						$m++;
					}
					else
					{
						if(!in_array($row['FOR_SPECIFIC'],$tna_template_spc)) { $j=0; $tna_template_spc[]=$row['FOR_SPECIFIC']; }
						$tna_template_buyer[$row['FOR_SPECIFIC']][$j]['lead']=$row['LEAD_TIME'];
						$tna_template_buyer[$row['FOR_SPECIFIC']][$j]['id']=$row['TASK_TEMPLATE_ID'];
						$j++;
					}
					$k++;
				}
				 
				$template_wise_task[$row['TASK_TEMPLATE_ID']][$row['TASK_NAME']]['deadline']= $row['DEADLINE'];
				$template_wise_task[$row['TASK_TEMPLATE_ID']][$row['TASK_NAME']]['execution_days']= $row['EXECUTION_DAYS'];
				$template_wise_task[$row['TASK_TEMPLATE_ID']][$row['TASK_NAME']]['notice_before']=$row['NOTICE_BEFORE'];
				$template_wise_task[$row['TASK_TEMPLATE_ID']][$row['TASK_NAME']]['sequence_no']=$row['SEQUENCE_NO'];
				$template_wise_task[$row['TASK_TEMPLATE_ID']][$row['TASK_NAME']]['for_specific']=$row['FOR_SPECIFIC'];
				$template_wise_task[$row['TASK_TEMPLATE_ID']][$row['TASK_NAME']]['task_name']=$row['TASK_NAME'];
				$template_wise_task[$row['TASK_TEMPLATE_ID']][$row['TASK_NAME']]['completion_percent']=$tna_completion[$row['TASK_NAME']];
				
				 $g++;
				 $i++;
			}
		   //print_r($tna_template_buyer);die;	
		 
		 
		$sql = "SELECT COMPANY_NAME,TNA_INTEGRATED FROM  variable_order_tracking WHERE  company_name=".$cbo_company." and status_active =1 and is_deleted = 0 and variable_list=14";
		$result = sql_select( $sql );
		$variable_settings = array();
		foreach( $result as $row ) 
		{		
			$variable_settings[$row['COMPANY_NAME']] = $row['TNA_INTEGRATED'];
		}
		if( $db_type==0 ) $blank_date="0000-00-00"; else $blank_date=""; 
		// Reprocess Check
		
		
		if (trim($txt_booking_no_id)==""){
			if( $is_delete==1 )
			{
				if($cbo_buyer!=0){$buyerCon=" and a.buyer_id='$cbo_buyer'";}
				$po_id_arr=return_library_array( "select b.id, b.id from fabric_sales_order_mst a,FABRIC_SALES_ORDER_DTLS b where a.id=b.mst_id a.company_id=$cbo_company  $buyerCon ",'id','job_no');
				$con = connect();
				$rid=execute_query("delete FROM tna_process_mst WHERE task_type=2 ".where_con_using_array($po_id_arr,0,'PO_NUMBER_ID')." ",1);
				oci_commit($con); 
				disconnect($con);
			}

		}
		else 
		{
			if( $is_delete==1 )
			{
				$con = connect();
				$rid=execute_query("delete FROM tna_process_mst WHERE task_type=2 and PO_NUMBER_ID in (select id from fabric_sales_order_dtls where mst_id in($txt_booking_no_id))",1);

				if( $db_type==2 ) oci_commit($con); 
				disconnect($con);
			}
			
		}
		

	
		 
		 
		if($textile_tna_process_base==2){//Sales Order Base
			
			//if($txt_booking_no_id!=''){$booking_con="and a.job_no='$txt_booking_no'";}
			if($txt_booking_no_id!=''){$booking_con="and a.id in($txt_booking_no_id)";}
			if($cbo_buyer!=0){$buyer_cond="and a.buyer_id='$cbo_buyer'";}
			
			$sql = "select a.id as JOB_ID,a.DELIVERY_DATE,a.BOOKING_DATE as BOOKING_DATE,a.JOB_NO,a.BUYER_ID,b.id as PO_ID,b.COLOR_TYPE_ID,b.COLOR_ID,b.FABRIC_DESC,b.DETERMINATION_ID,b.GSM_WEIGHT,b.DIA,sum(b.finish_qty) as FIN_FAB_QNTY,sum(b.grey_qty) as GREY_FAB_QNTY
			
            from fabric_sales_order_mst a,fabric_sales_order_dtls b  
			WHERE a.job_no=b.job_no_mst and b.is_deleted = 0 and b.status_active=1 and a.is_deleted = 0 and a.status_active=1 and a.delivery_date >'$tna_process_start_date' and company_id=".$cbo_company." $buyer_cond $booking_con
			group by a.id,a.BOOKING_DATE,a.delivery_date,a.job_no,a.buyer_id,b.id,b.COLOR_TYPE_ID,b.COLOR_ID,b.FABRIC_DESC,b.DETERMINATION_ID,b.GSM_WEIGHT,b.DIA ORDER BY a.delivery_date asc";			
			
			
			    //echo $sql;die;
			
		}
		else{//Booking Base
		
			
		}
		 
		 // echo $sql;die;
		 
		 
		 
		$data_array=sql_select($sql);
		
		$booking_details=array(); 
		$to_process_task=array();  
		$job_no_array=array();
		$order_id_array=array();
		$po_order_template=array();
		$po_order_details=array();
		$template_missing_po=array();
		$tna_task_update_data=array();
		$template_missing_po_mail_data_arr=array();
		$i=0;
		
		foreach($data_array as $row)
		{
			//$row['PO_ID']=$row[BOOKING_NO].'**'.$row['FABRIC_DESC'].'**'.$row['GSM_WEIGHT'].'**'.$row['DIA'].'**'.$row['COLOR_ID'];
			
			$row['BOOKING_DATE']=date('d-M-y',strtotime($row['BOOKING_DATE']));
			$remain_days=datediff( "d", date("Y-m-d",strtotime($row['BOOKING_DATE'])), date("Y-m-d",strtotime($row['DELIVERY_DATE'])) );
			 //print_r($tna_template);die;


		
			 
			if ( $tna_process_type==1 )
			{ 
				$template_id=get_tna_template($remain_days,$tna_template,$row['BUYER_ID']); 
			}
			else
			{
				
				
				$template_id=$remain_days-1; 
				
				if($tna_task_percent_buyer_wise[$row[csf('buyer_id')]]=="")
				{
				 
					foreach($tna_task_percent as $id=>$data)
					{
						$deadline=floor($template_id*$data['start_percent']/100);
						$exe=floor($template_id*$data['end_percent']/100);
						if($deadline==0){$v=0;}else{$v=1;}  if($exe==0){$e=0;}else{$e=1;}
						$template_wise_task[$template_id][$id]['deadline']= $deadline-$v;
						$template_wise_task[$template_id][$id]['execution_days']= $exe-$e;
						$template_wise_task[$template_id][$id]['notice_before']=$data['notice_before'];
						$template_wise_task[$template_id][$id]['sequence_no']=$row['sequence_no'];
						$template_wise_task[$template_id][$id]['for_specific']=$data['buyer_id'];
						$template_wise_task[$template_id][$id]['task_name']=$id;
						$template_wise_task[$template_id][$id]['completion_percent']=$data['completion_percent'];
					}
				}
				else
				{
					foreach($tna_task_percent_buyer[$row[csf("buyer_id")]] as $id=>$data)
					{
						$deadline=floor($template_id*$data['start_percent']/100);
						$exe=floor($template_id*$data['end_percent']/100);
						if($deadline==0){$v=0;}else{$v=1;}  if($exe==0){$e=0;}else{$e=1;}
	
						$template_wise_task[$template_id][$id]['deadline']= $deadline-$v;
						$template_wise_task[$template_id][$id]['execution_days']= $exe-$e;
						$template_wise_task[$template_id][$id]['notice_before']=$data['notice_before'];
						$template_wise_task[$template_id][$id]['sequence_no']=$row['sequence_no'];
						$template_wise_task[$template_id][$id]['for_specific']=$data['buyer_id'];
						$template_wise_task[$template_id][$id]['task_name']=$id;
						$template_wise_task[$template_id][$id]['completion_percent']=$data['completion_percent'];
					}
				}
			
			} 
			
			 //echo $template_id;die;
				$job_by_po_arr[$row['PO_ID']]=$row['JOB_ID'];
				
				
				$booking_template[$row['PO_ID']]=$template_id; 
				$booking_details[$row['PO_ID']]['booking_date']=$row['BOOKING_DATE'];
				$booking_details[$row['PO_ID']]['delivery_date']=$row['DELIVERY_DATE'];
				$booking_details[$row['PO_ID']]['booking_qty']+=$row['GREY_FAB_QNTY'];
				$booking_details[$row['PO_ID']]['template_id']=$template_id;
				$booking_details[$row['PO_ID']]['po_id']=$row['PO_ID'];
				$booking_details[$row['PO_ID']]['job_no']=$row['JOB_NO'];
				
				$booking_id_arr[$row['PO_ID']]=$row['PO_ID'];
				$job_id_arr[$row['JOB_ID']]=$row['JOB_ID'];
				
				
				$key=$row['JOB_ID'].'**'.$row['FABRIC_DESC'].'**'.$row['GSM_WEIGHT'].'**'.$row['DIA'];
				$po_id_by_ref[$key]=$row['PO_ID'];
				
				$key3=$row['JOB_ID'].'**'.$row['DETERMINATION_ID'].'**'.$row['GSM_WEIGHT'].'**'.$row['DIA'];
				$po_id_by_determination_id_ref[$key3]=$row['PO_ID'];
				
				
				//$key2=$row['JOB_ID'].'**'.$row['COLOR_TYPE_ID'].'**'.$row['FABRIC_DESC'].'**'.$row['GSM_WEIGHT'].'**'.$row['DIA'];
				$key2=$row['JOB_ID'].'**'.$row['COLOR_TYPE_ID'].'**'.$row['FABRIC_DESC'].'**'.$row['GSM_WEIGHT'];
				$po_id_by_color_type_ref[$key2]=$row['PO_ID'];
				
				$key3=$row['JOB_ID'].'**'.$row['COLOR_ID'].'**'.$row['DETERMINATION_ID'].'**'.$row['GSM_WEIGHT'].'**'.$row['DIA'];
				$po_id_by_job_color_determa_gsm_dia_ref[$key3]=$row['PO_ID'];
				
				$key4=$row['JOB_ID'].'**'.$row['COLOR_ID'].'**'.$row['FABRIC_DESC'].'**'.$row['GSM_WEIGHT'].'**'.$row['DIA'];
				$po_id_by_job_color_fabric_gsm_dia_ref[$key4]=$row['PO_ID'];
				
				
				
			 
			if ( $template_id=="" || $template_id==0 )
			{
				$template_missing[]=$row['PO_ID'];
				//This array for missiong PO Auto mail send..............
				$template_missing_mail_data_arr[]=array(
					'booking_no'	=> $row['PO_ID'],
					'buyer_id'		=> $row['BUYER_ID'],
					'booking_date'	=> $row['BOOKING_DATE'],
					'delivery_date'	=> $row['DELIVERY_DATE']
				
				);
			} 
			else
			{
								
			//color type wise req qty...................................start;
			
			
				if( $gross_level==1 ){
					$to_process_task[$row['PO_ID']][200]=200;
					$tna_task_update_data[$row['PO_ID']][200]['reqqnty']+=$row['GREY_FAB_QNTY'];
				
				
					//2,3,4,6,32,33 Y/D; Note: reqqnty of Y/D rcv FSOE  grey_fab_qnty mandatory
					if( in_array($row['COLOR_TYPE_ID'],array(2,3,4,6,32,33,44,47,48,63))){
						$tna_task_update_data[$row['PO_ID']][52]['reqqnty']+=$row['GREY_FAB_QNTY'];
						$req_qty_by_job_po[52][$row['JOB_ID']][$row['PO_ID']]+=$row['GREY_FAB_QNTY'];

					}
				
				}
				else
				{
					//2,3,4,6,32,33,44,47,48,63 Y/D
					if( in_array($row['COLOR_TYPE_ID'],array(2,3,4,6,32,33,44,47,48,63))){
						$to_process_task[$row['PO_ID']][211]=211;
						$tna_task_update_data[$row['PO_ID']][211]['reqqnty']+=$row['GREY_FAB_QNTY'];
						$tna_task_update_data[$row['PO_ID']][52]['reqqnty']+=$row['GREY_FAB_QNTY'];
						$req_qty_by_job_po[52][$row['JOB_ID']][$row['PO_ID']]+=$row['GREY_FAB_QNTY'];
					}
					//5,7 AOP
					else if(($row['COLOR_TYPE_ID']==5) || ($row['COLOR_TYPE_ID']==7)){
						$to_process_task[$row['PO_ID']][210]=210;
						$tna_task_update_data[$row['PO_ID']][210]['reqqnty']+=$row['GREY_FAB_QNTY'];
					}//solid..
					else if(($row['COLOR_TYPE_ID']==1) || ($row['COLOR_TYPE_ID']==20) || ($row['COLOR_TYPE_ID']==25) || ($row['COLOR_TYPE_ID']==26) || ($row['COLOR_TYPE_ID']==27) || ($row['COLOR_TYPE_ID']==28) || ($row['COLOR_TYPE_ID']==29) || ($row['COLOR_TYPE_ID']==30) || ($row['COLOR_TYPE_ID']==31) || ($row['COLOR_TYPE_ID']==''))
					{
						$to_process_task[$row['PO_ID']][200]=200;
						$tna_task_update_data[$row['PO_ID']][200]['reqqnty']+=$row['GREY_FAB_QNTY'];
					
					}
				}
				
			
				
			//color type wise req qty.....................................end;	
				
				
				
				$tna_task_update_data[$row['PO_ID']][31]['max_start_date']=$row['BOOKING_DATE'];
				$tna_task_update_data[$row['PO_ID']][31]['min_start_date']=$row['BOOKING_DATE']; 
				$tna_task_update_data[$row['PO_ID']][31]['doneqnty']+=$row['GREY_FAB_QNTY'];
				
				
				
				$to_process_task[$row['PO_ID']][8]=8;
				$to_process_task[$row['PO_ID']][10]=10;
				
				$tna_task_update_data[$row['PO_ID']][31]['reqqnty']+=$row['GREY_FAB_QNTY'];
				$to_process_task[$row['PO_ID']][31]=31;
				
				$to_process_task[$row['PO_ID']][33]=33;
				$to_process_task[$row['PO_ID']][40]=40;
				$to_process_task[$row['PO_ID']][45]=45;
				$to_process_task[$row['PO_ID']][46]=46;
				$to_process_task[$row['PO_ID']][47]=47;
				$tna_task_update_data[$row['PO_ID']][47]['reqqnty']+=$row['GREY_FAB_QNTY'];
				$req_qty_by_job_po[47][$row['JOB_ID']][$row['PO_ID']]+=$row['GREY_FAB_QNTY'];


				$to_process_task[$row['PO_ID']][48]=48;
				$tna_task_update_data[$row['PO_ID']][48]['reqqnty']+=$row['GREY_FAB_QNTY'];
				$req_qty_by_job_po[48][$row['JOB_ID']][$row['PO_ID']]+=$row['GREY_FAB_QNTY'];

				$tna_task_update_data[$row['PO_ID']][50]['reqqnty']+=$row['GREY_FAB_QNTY'];
				$to_process_task[$row['PO_ID']][50]=50;
				
				$to_process_task[$row['PO_ID']][51]=51;
				//$tna_task_update_data[$row['PO_ID']][51]['reqqnty']+=$row['FIN_FAB_QNTY'];
				$to_process_task[$row['PO_ID']][52]=52;
				
				
				$tna_task_update_data[$row['PO_ID']][60]['reqqnty']+=$row['GREY_FAB_QNTY'];
				$req_qty_by_job_po[60][$row['JOB_ID']][$row['PO_ID']]+=$row['GREY_FAB_QNTY'];
				$to_process_task[$row['PO_ID']][60]=60;
				
				$tna_task_update_data[$row['PO_ID']][61]['reqqnty']+=$row['FIN_FAB_QNTY'];
				$req_qty_by_job_po[61][$row['JOB_ID']][$row['PO_ID']]+=$row['FIN_FAB_QNTY'];
				$to_process_task[$row['PO_ID']][61]=61;
				
			
				
				$to_process_task[$row['PO_ID']][62]=62;
				$to_process_task[$row['PO_ID']][63]=63;
				
				$tna_task_update_data[$row['PO_ID']][64]['reqqnty']+=$row['FIN_FAB_QNTY'];
				$req_qty_by_job_po[64][$row['JOB_ID']][$row['PO_ID']]+=$row['FIN_FAB_QNTY'];
				$to_process_task[$row['PO_ID']][64]=64;
				
				
				
				$to_process_task[$row['PO_ID']][72]=72;
				$tna_task_update_data[$row['PO_ID']][72]['reqqnty']+=$row['GREY_FAB_QNTY'];
				$req_qty_by_job_po[72][$row['JOB_ID']][$row['PO_ID']]+=$row['GREY_FAB_QNTY'];
				
				$tna_task_update_data[$row['PO_ID']][73]['reqqnty']+=$row['FIN_FAB_QNTY'];
				$req_qty_by_job_po[73][$row['JOB_ID']][$row['PO_ID']]+=$row['FIN_FAB_QNTY'];
				$to_process_task[$row['PO_ID']][73]=73;
				
				$to_process_task[$row['PO_ID']][74]=74;
				$tna_task_update_data[$row['PO_ID']][74]['reqqnty']+=$row['FIN_FAB_QNTY'];
				$req_qty_by_job_po[74][$row['JOB_ID']][$row['PO_ID']]+=$row['FIN_FAB_QNTY'];


				$to_process_task[$row['PO_ID']][80]=80;
				$to_process_task[$row['PO_ID']][167]=167;
				
				$to_process_task[$row['PO_ID']][199]=199;
				$tna_task_update_data[$row['PO_ID']][199]['max_start_date']=$row['BOOKING_DATE'];
				$tna_task_update_data[$row['PO_ID']][199]['min_start_date']=$row['BOOKING_DATE']; 
				$tna_task_update_data[$row['PO_ID']][199]['reqqnty']+=$row['FIN_FAB_QNTY'];
				$tna_task_update_data[$row['PO_ID']][199]['doneqnty']+=$row['FIN_FAB_QNTY'];
				

				$to_process_task[$row['PO_ID']][201]=201;
				$tna_task_update_data[$row['PO_ID']][201]['reqqnty']+=$row['GREY_FAB_QNTY'];
				$req_qty_by_job_po[201][$row['JOB_ID']][$row['PO_ID']]+=$row['GREY_FAB_QNTY'];
				
				$to_process_task[$row['PO_ID']][202]=202;
				$tna_task_update_data[$row['PO_ID']][202]['reqqnty']+=$row['GREY_FAB_QNTY'];
				$req_qty_by_job_po[202][$row['JOB_ID']][$row['PO_ID']]+=$row['GREY_FAB_QNTY'];
				
				$to_process_task[$row['PO_ID']][203]=203;
				$tna_task_update_data[$row['PO_ID']][203]['reqqnty']+=$row['GREY_FAB_QNTY'];
				$req_qty_by_job_po[203][$row['JOB_ID']][$row['PO_ID']]+=$row['GREY_FAB_QNTY'];


				$to_process_task[$row['PO_ID']][204]=204;
				$tna_task_update_data[$row['PO_ID']][204]['reqqnty']+=$row['GREY_FAB_QNTY'];
				$req_qty_by_job_po[204][$row['JOB_ID']][$row['PO_ID']]+=$row['GREY_FAB_QNTY'];

				
				$to_process_task[$row['PO_ID']][205]=205;
				$tna_task_update_data[$row['PO_ID']][205]['reqqnty']+=$row['GREY_FAB_QNTY'];
				$req_qty_by_job_po[205][$row['JOB_ID']][$row['PO_ID']]+=$row['GREY_FAB_QNTY'];
				
				$to_process_task[$row['PO_ID']][206]=206;
				$to_process_task[$row['PO_ID']][207]=207;
				$tna_task_update_data[$row['PO_ID']][207]['reqqnty']+=$row['FIN_FAB_QNTY'];
				$req_qty_by_job_po[207][$row['JOB_ID']][$row['PO_ID']]+=$row['FIN_FAB_QNTY'];

				$to_process_task[$row['PO_ID']][208]=208;
				$to_process_task[$row['PO_ID']][209]=209;
				
				
				$to_process_task[$row['PO_ID']][212]=212;
				$tna_task_update_data[$row['PO_ID']][212]['reqqnty']+=$row['GREY_FAB_QNTY'];
				$req_qty_by_job_po[212][$row['JOB_ID']][$row['PO_ID']]+=$row['GREY_FAB_QNTY'];

				
				$to_process_task[$row['PO_ID']][213]=213;
				$to_process_task[$row['PO_ID']][214]=214;
				//$to_process_task[$row['PO_ID']][216]=216;
				$to_process_task[$row['PO_ID']][217]=217;
				$to_process_task[$row['PO_ID']][218]=218;
				$to_process_task[$row['PO_ID']][219]=219;
				
				$to_process_task[$row['PO_ID']][239]=239;
				$req_qty_by_job_po[239][$row['JOB_ID']][$row['PO_ID']]+=$row['FIN_FAB_QNTY'];
				$tna_task_update_data[$row['PO_ID']][239]['reqqnty']+=$row['FIN_FAB_QNTY'];				
				
				if(in_array($row['COLOR_TYPE_ID'],array(5,7,58,59,60,56,57,45,54,55,49))){//AOP
					$to_process_task[$row['PO_ID']][215]=215;
					$tna_task_update_data[$row['PO_ID']][215]['reqqnty']+=$row['FIN_FAB_QNTY'];
				}
				else if(in_array($row['COLOR_TYPE_ID'],array(2,3,4,6,32,33,44,47,48,63))){//YD
					$to_process_task[$row['PO_ID']][216]=216;
					$tna_task_update_data[$row['PO_ID']][216]['reqqnty']+=$row['FIN_FAB_QNTY'];
				}
				else if(in_array($row['COLOR_TYPE_ID'],array(1,20,25,26,27,28,29,30,31))){//Solid
					$to_process_task[$row['PO_ID']][61]=61;
					$tna_task_update_data[$row['PO_ID']][61]['reqqnty']+=$row['FIN_FAB_QNTY'];
				}
			
				$req_qty_by_job_po[215][$row['JOB_ID']][$row['PO_ID']]+=$row['FIN_FAB_QNTY'];
			
				
			}
		}
		
		 //print_r($to_process_task);die;

//Knitting Production.................................................start;
$sql="SELECT a.id as JOB_NO,b.id as PO_ID,B.GREY_QTY, SUM(f.GREY_RECEIVE_QNTY) AS GREY_RECEIVE_QNTY,MAX(e.RECEIVE_DATE) AS MAX_DATE,MIN(e.RECEIVE_DATE) AS MIN_DATE  from fabric_sales_order_mst a,fabric_sales_order_dtls b,ppl_planning_entry_plan_dtls d,INV_RECEIVE_MASTER e,PRO_GREY_PROD_ENTRY_DTLS f where a.id=b.mst_id and a.id=d.po_id and e.id=f.mst_id and d.dtls_id=e.BOOKING_ID AND f.BODY_PART_ID=b.BODY_PART_ID  and  b.GSM_WEIGHT=f.GSM  and e.RECEIVE_BASIS=2   and a.company_id=$cbo_company  and a.IS_DELETED = 0  and b.IS_DELETED = 0  ".where_con_using_array($booking_id_arr,0,'b.id')." GROUP BY a.id,b.id,B.GREY_QTY ";// and d.dtls_id=11724
	$knit_gray_data_array=sql_select($sql);
	$tmpData=array();
	foreach($knit_gray_data_array as $row)
	{
		$tna_task_update_data[$row['PO_ID']][60]['max_start_date']=$row['MAX_DATE'];
		$tna_task_update_data[$row['PO_ID']][60]['min_start_date']=$row['MIN_DATE']; 
		$tna_task_update_data[$row['PO_ID']][60]['doneqnty']=$row['GREY_RECEIVE_QNTY'];
		
		$tmpData['DONE_QTY'][$row['JOB_NO']][$row['PO_ID']]=$row['GREY_QTY'];
		$tmpData['MAX_DATE'][$row['JOB_NO']]=$row['MAX_DATE'];
		$tmpData['MIN_DATE'][$row['JOB_NO']]=$row['MIN_DATE'];
	}
	
	
	foreach($job_by_po_arr as $poId=>$jobNo){
		$reqQty=array_sum($req_qty_by_job_po[60][$jobNo]);
		$doneQty=array_sum($tmpData['DONE_QTY'][$jobNo]);
		if($doneQty >= $reqQty){
			$tna_task_update_data[$poId][60]['reqqnty']=$reqQty;
			$tna_task_update_data[$poId][60]['doneqnty']=$doneQty;
			$tna_task_update_data[$poId][60]['max_start_date']=$tmpData['MAX_DATE'][$jobNo];
			$tna_task_update_data[$poId][60]['min_start_date']=$tmpData['MIN_DATE'][$jobNo]; 
		}
	}
	unset($tmpData);
	unset($knit_gray_data_array);

	//print_r($tna_task_update_data);die;
	
	 
	 //echo $sql;die;


//Knitting Production.................................................end;


//Yarn Issue...................................................................................;	
		  $sql="select  b.DTLS_ID as PO_ID,b.ENTRY_FORM, min(a.transaction_date) as MIN_DATE, max(a.transaction_date) as MAX_DATE, sum(b.quantity) QTY, sum(b.returnable_qnty) ret_qty from inv_transaction a,order_wise_pro_details b   where  b.trans_id=a.id  and a.transaction_type in(1,2)  and a.item_category in(1,13) and b.entry_form in(3,58) and b.trans_type in(1,2) and a.status_active=1 and b.status_active=1 ".where_con_using_array($booking_id_arr,0,'b.DTLS_ID')."  group by b.entry_form,b.DTLS_ID";
		 //echo $sql;die;
		$planning_data_array=sql_select($sql);
		foreach($planning_data_array as $row)
		{
			if($row['ENTRY_FORM']==3){
				$tna_task_update_data[$row['PO_ID']][50]['max_start_date']=$row['MAX_DATE'];
				$tna_task_update_data[$row['PO_ID']][50]['min_start_date']=$row['MIN_DATE']; 
				$tna_task_update_data[$row['PO_ID']][50]['doneqnty']=$row['QTY'];
			}
			else if($row['ENTRY_FORM']==58){
				//$tna_task_update_data[$row['PO_ID']][72]['max_start_date']=$row['MAX_DATE'];
				//$tna_task_update_data[$row['PO_ID']][72]['min_start_date']=$row['MIN_DATE']; 
				//$tna_task_update_data[$row['PO_ID']][72]['doneqnty']=$row['QTY'];
			}
		}




//Planning data........................................................................................start;
			
/*			$sql = "select b.id as PO_ID, min(a.booking_date) mindate, max(a.delivery_date) maxdate,  sum(b.finish_qty) finish_qty, sum(b.grey_qty) grey_qty 
from fabric_sales_order_mst a,fabric_sales_order_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.company_id=".$cbo_company."  and a.within_group=1 ".where_con_using_array($booking_id_arr,0,'d.id')." group by b.id order by a.booking_date";
			
			
		//$planning_data_array=sql_select($sql);
		foreach($planning_data_array as $row)
		{
			//$tna_task_update_data[$row['PO_ID']][31]['reqqnty']+=$row['GREY_FAB_QNTY'];
			//$to_process_task[$row['PO_ID']][31]=31;
			
			//$tna_task_update_data[$row['PO_ID']][50]['reqqnty']+=$row['FIN_FAB_QNTY'];
			//$to_process_task[$row['PO_ID']][50]=50;
		}*/

//Planning data........................................................................................end;


//Knite Plan data........................................................................................start;
			
		$sql = "select c.PO_ID as JOB_ID, c.COLOR_TYPE_ID,c.FABRIC_DESC,c.GSM_WEIGHT,c.DIA, min(b.program_date) MIN_DATE, max(b.program_date) MAX_DATE,  sum(b.program_qnty) QTY
		from fabric_sales_order_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c 
		where a.id=c.po_id and b.id=c.dtls_id and a.company_id=$cbo_company ".where_con_using_array($job_id_arr,0,'c.PO_ID')."  group by c.PO_ID,c.color_type_id,c.FABRIC_DESC,c.GSM_WEIGHT,c.DIA";
		
		$sql = "select d.id as PO_ID, c.COLOR_TYPE_ID,c.FABRIC_DESC,c.GSM_WEIGHT,c.DIA, min(b.program_date) MIN_DATE, max(b.program_date) MAX_DATE,  sum(b.program_qnty) QTY
		from fabric_sales_order_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c ,fabric_sales_order_dtls d
		where a.id=c.po_id and b.id=c.dtls_id and a.company_id=$cbo_company ".where_con_using_array($booking_id_arr,0,'d.id')." and a.id=d.MST_ID and c.GSM_WEIGHT = d.GSM_WEIGHT and c.FABRIC_DESC=d.FABRIC_DESC and c.COLOR_TYPE_ID=d.COLOR_TYPE_ID and c.BODY_PART_ID=d.BODY_PART_ID
  group by d.id,c.color_type_id,c.FABRIC_DESC,c.GSM_WEIGHT,c.DIA";		 
		 // echo $sql;die;
		  
		$kniting_plan_data_array=sql_select($sql);
		foreach($kniting_plan_data_array as $row)
		{
			$key=$row['JOB_ID'].'**'.$row['FABRIC_DESC'].'**'.$row['GSM_WEIGHT'].'**'.$row['DIA'];
			$po_id_by_ref[$key]=$row['PO_ID'];
			
			if( $gross_level==1 ){
				//$key=$row['JOB_ID'].'**'.$row['FABRIC_DESC'].'**'.$row['GSM_WEIGHT'].'**'.$row['DIA'];
				//$row['PO_ID']=$po_id_by_ref[$key];
				if($row['PO_ID']){
					$tna_task_update_data[$row['PO_ID']][200]['max_start_date']=$row['MAX_DATE'];
					$tna_task_update_data[$row['PO_ID']][200]['min_start_date']=$row['MIN_DATE']; 
					$tna_task_update_data[$row['PO_ID']][200]['doneqnty']+=$row['QTY'];
				}
			}
			else
			{
				
				//$key=$row['JOB_ID'].'**'.$row['COLOR_TYPE_ID'].'**'.$row['FABRIC_DESC'].'**'.$row['GSM_WEIGHT'].'**'.$row['DIA'];
				//$row['PO_ID']=$po_id_by_color_type_ref[$key];
				//2,3,4,6,32,33 Y/D
				if(($row['COLOR_TYPE_ID']==2) || ($row['COLOR_TYPE_ID']==3) || ($row['COLOR_TYPE_ID']==4) || ($row['COLOR_TYPE_ID']==6) || ($row['COLOR_TYPE_ID']==32) || ($row['COLOR_TYPE_ID']==33)){
					$tna_task_update_data[$row['PO_ID']][211]['max_start_date']=$row['MAX_DATE'];
					$tna_task_update_data[$row['PO_ID']][211]['min_start_date']=$row['MIN_DATE']; 
					$tna_task_update_data[$row['PO_ID']][211]['doneqnty']+=$row['QTY'];
				}
				//5,7 AOP
				else if(($row['COLOR_TYPE_ID']==5) || ($row['COLOR_TYPE_ID']==7)){
					$tna_task_update_data[$row['PO_ID']][210]['max_start_date']=$row['MAX_DATE'];
					$tna_task_update_data[$row['PO_ID']][210]['min_start_date']=$row['MIN_DATE']; 
					$tna_task_update_data[$row['PO_ID']][210]['doneqnty']+=$row['QTY'];
				}//solid..
				elseif(($row['COLOR_TYPE_ID']==1) || ($row['COLOR_TYPE_ID']==20) || ($row['COLOR_TYPE_ID']==25) || ($row['COLOR_TYPE_ID']==26) || ($row['COLOR_TYPE_ID']==27) || ($row['COLOR_TYPE_ID']==28) || ($row['COLOR_TYPE_ID']==29) || ($row['COLOR_TYPE_ID']==30) || ($row['COLOR_TYPE_ID']==31) || ($row['COLOR_TYPE_ID']==''))
				{
					$tna_task_update_data[$row['PO_ID']][200]['max_start_date']=$row['MAX_DATE'];
					$tna_task_update_data[$row['PO_ID']][200]['min_start_date']=$row['MIN_DATE']; 
					$tna_task_update_data[$row['PO_ID']][200]['doneqnty']+=$row['QTY'];
				
				}
			}
			
		}
		unset($kniting_plan_data_array);
		
		//print_r($tna_task_update_data);die;
		

//Knite Plan data........................................................................................end;



//Kniting Production...................................................................................start;	
		  
	/*$sql="select c.id as JOB_ID,d.FABRIC_DESC,d.GSM_WEIGHT, d.DIA,d.id as PO_ID,d.COLOR_TYPE_ID,min(a.receive_date) MIN_DATE,max(a.receive_date) MAX_DATE, sum(b.qnty) QTY from inv_receive_master a ,pro_roll_details b ,fabric_sales_order_mst c ,fabric_sales_order_dtls d
	where a.id=b.mst_id and b.po_breakdown_id=c.id and a.receive_basis=2 and a.entry_form=2 and b.entry_form=2 and a.item_category=13  and a.status_active=1   and d.status_active=1 and b.status_active=1 and c.status_active=1 and a.knitting_company=$cbo_company  and c.id=d.mst_id ".where_con_using_array($booking_id_arr,0,'d.id')."  group by  c.id,d.FABRIC_DESC,d.GSM_WEIGHT, d.DIA,d.id,d.color_type_id"; */
  // echo $sql;die;
				  
	$sql="select d.COLOR_ID,e.FEBRIC_DESCRIPTION_ID,c.id as JOB_ID,d.FABRIC_DESC,e.GSM as GSM_WEIGHT, e.WIDTH as DIA,d.id as PO_ID,d.COLOR_TYPE_ID,min(a.receive_date) MIN_DATE,max(a.receive_date) MAX_DATE, sum(b.qnty) QTY 
from inv_receive_master a ,pro_roll_details b ,fabric_sales_order_mst c ,fabric_sales_order_dtls d ,PRO_GREY_PROD_ENTRY_DTLS e
where a.id=b.mst_id and b.po_breakdown_id=c.id and a.receive_basis=2 and a.entry_form=2 and b.entry_form=2 and a.item_category=13 and a.id=e.mst_id and d.GSM_WEIGHT=e.GSM  and b.DTLS_ID=e.id and e.BODY_PART_ID=d.BODY_PART_ID
and a.status_active=1 and d.status_active=1 and b.status_active=1 and c.status_active=1 and a.knitting_company=$cbo_company and c.id=d.mst_id ".where_con_using_array($booking_id_arr,0,'d.id')." 
group by d.COLOR_ID,e.FEBRIC_DESCRIPTION_ID,c.id,d.FABRIC_DESC,e.GSM, e.WIDTH,d.id,d.color_type_id";
			 //echo $sql;die;  
			  
		$knit_pro_data_array=sql_select($sql);
		foreach($knit_pro_data_array as $row)
		{
		
			$key=$row['JOB_ID'].'**'.$row['FABRIC_DESC'].'**'.$row['GSM_WEIGHT'].'**'.$row['DIA'];
			$po_id_by_ref[$key]=$row['PO_ID'];
			
			$key2=$row['JOB_ID'].'**'.$row['FEBRIC_DESCRIPTION_ID'].'**'.$row['GSM_WEIGHT'].'**'.$row['DIA'];
			$po_id_by_determination_id_ref[$key2]=$row['PO_ID'];
			
			$key3=$row['JOB_ID'].'**'.$row['COLOR_ID'].'**'.$row['FEBRIC_DESCRIPTION_ID'].'**'.$row['GSM_WEIGHT'].'**'.$row['DIA'];
			$po_id_by_job_color_determa_gsm_dia_ref[$key3]=$row['PO_ID'];
			
			
			
			if( $gross_level==1 ){
				if($tna_task_update_data[$row['PO_ID']][212]['max_start_date']==''){
					$tna_task_update_data[$row['PO_ID']][212]['max_start_date']=$row['MAX_DATE'];
					$tna_task_update_data[$row['PO_ID']][212]['min_start_date']=$row['MIN_DATE'];
				}
				$tna_task_update_data[$row['PO_ID']][212]['doneqnty']+=$row['QTY'];
				if(strtotime($row['MAX_DATE']) > strtotime($tna_task_update_data[$row['PO_ID']][212]['max_start_date'])){$tna_task_update_data[$row['PO_ID']][212]['max_start_date']=$row['MAX_DATE'];}
				
				if(strtotime($row['MIN_DATE']) < strtotime($tna_task_update_data[$row['PO_ID']][212]['min_start_date'])){$tna_task_update_data[$row['PO_ID']][212]['min_start_date']=$row['MIN_DATE'];}
				
			}
			else
			{
			
				if(($row['COLOR_TYPE_ID']==2) || ($row['COLOR_TYPE_ID']==3) || ($row['COLOR_TYPE_ID']==4) || ($row['COLOR_TYPE_ID']==6) || ($row['COLOR_TYPE_ID']==32) || ($row['COLOR_TYPE_ID']==33)){
	
				}
				//5,7 AOP
				else if(($row['COLOR_TYPE_ID']==5) || ($row['COLOR_TYPE_ID']==7)){
				}//solid..
				elseif(($row['COLOR_TYPE_ID']==1) || ($row['COLOR_TYPE_ID']==20) || ($row['COLOR_TYPE_ID']==25) || ($row['COLOR_TYPE_ID']==26) || ($row['COLOR_TYPE_ID']==27) || ($row['COLOR_TYPE_ID']==28) || ($row['COLOR_TYPE_ID']==29) || ($row['COLOR_TYPE_ID']==30) || ($row['COLOR_TYPE_ID']==31) || ($row['COLOR_TYPE_ID']==''))
				{
					if($tna_task_update_data[$row['PO_ID']][212]['max_start_date']==''){
					$tna_task_update_data[$row['PO_ID']][212]['max_start_date']=$row['MAX_DATE'];
					$tna_task_update_data[$row['PO_ID']][212]['min_start_date']=$row['MIN_DATE'];
					}
					 
					$tna_task_update_data[$row['PO_ID']][212]['doneqnty']+=$row['QTY'];
					
					if(strtotime($row['MAX_DATE']) > strtotime($tna_task_update_data[$row['PO_ID']][212]['max_start_date'])){$tna_task_update_data[$row['PO_ID']][212]['max_start_date']=$row['MAX_DATE'];}
					
					if(strtotime($row['MIN_DATE']) < strtotime($tna_task_update_data[$row['PO_ID']][212]['min_start_date'])){$tna_task_update_data[$row['PO_ID']][212]['min_start_date']=$row['MIN_DATE'];}
		/*			$tna_task_update_data[$row['PO_ID']][213]['max_start_date']=$row['MAX_DATE'];
					$tna_task_update_data[$row['PO_ID']][213]['min_start_date']=$row['MIN_DATE']; 
					$tna_task_update_data[$row['PO_ID']][213]['doneqnty']+=$row[csf("qty")];*/
				}
			}
		
		
		}
		unset($knit_pro_data_array);
		
		

//Kniting Production...................................................................................end;	





//Batch Creation...................................................................................start;	
	  $sql="select b.PO_ID as JOB_ID,c.ITEM_DESCRIPTION,c.GSM,c.DIA_WIDTH,max(a.batch_date) MAX_DATE, min(a.batch_date) MIN_DATE, sum(b.batch_qnty) QTY  from pro_batch_create_mst a, pro_batch_create_dtls b,PRODUCT_DETAILS_MASTER c where a.id=b.mst_id and c.id=b.PROD_ID and a.is_sales=1  and a.status_active=1 and b.status_active=1 and a.working_company_id=$cbo_company ".where_con_using_array($job_id_arr,0,'b.po_id')." group by b.PO_ID,c.ITEM_DESCRIPTION,c.GSM,c.DIA_WIDTH";
		// echo $sql;die;
	$batchy_data_array=sql_select($sql);
	foreach($batchy_data_array as $row)
	{
		$key=$row['JOB_ID'].'**'.$row['ITEM_DESCRIPTION'].'**'.$row['GSM'].'**'.$row['DIA_WIDTH'];
		$row['PO_ID']=$po_id_by_ref[$key];
		
		if($row['PO_ID']){
			//$tna_task_update_data[$row['PO_ID']][50]['reqqnty']=$row[csf("qty")];
			$tna_task_update_data[$row['PO_ID']][205]['max_start_date']=$row['MAX_DATE'];
			$tna_task_update_data[$row['PO_ID']][205]['min_start_date']=$row['MIN_DATE']; 
			$tna_task_update_data[$row['PO_ID']][205]['doneqnty']+=$row['QTY'];
		}
	}
	unset($batchy_data_array);
	//print_r($tna_task_update_data);die;



//Batch Creation...................................................................................end;	






//Grey Fabric Delivery to store................................................................start;	
	  $sql="select a.ENTRY_FORM,c.id as JOB_ID,d.ITEM_DESCRIPTION,d.GSM,d.DIA_WIDTH,max(a.delevery_date) MAX_DATE,min(a.delevery_date) MIN_DATE,sum(b.current_delivery) qty  from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b , fabric_sales_order_mst c,PRODUCT_DETAILS_MASTER d where a.id=b.mst_id and b.PRODUCT_ID=d.id  and b.order_id=c.id and a.knitting_company=$cbo_company and a.entry_form in(56,67) and a.status_active=1 and b.status_active=1  ".where_con_using_array($job_id_arr,0,'c.id')."  group by a.entry_form,c.ID,d.ITEM_DESCRIPTION,d.GSM,d.DIA_WIDTH";
	 //echo $sql;die;
	$pgrey_fab_delv_data_array=sql_select($sql);
	foreach($pgrey_fab_delv_data_array as $row)
	{
		$key=$row['JOB_ID'].'**'.$row['ITEM_DESCRIPTION'].'**'.$row['GSM'].'**'.$row['DIA_WIDTH'];
		$row['PO_ID']=$po_id_by_ref[$key];
		
		if($row[csf("entry_form")]==56 && $row['PO_ID']){
			$tna_task_update_data[$row['PO_ID']][201]['max_start_date']=$row['MAX_DATE'];
			$tna_task_update_data[$row['PO_ID']][201]['min_start_date']=$row['MIN_DATE']; 
			$tna_task_update_data[$row['PO_ID']][201]['doneqnty']=$row[csf("qty")];
		}
		elseif($row[csf("entry_form")]==67 && $row['PO_ID']){
/*				$tna_task_update_data[$row[csf("job_no")]][207]['max_start_date']=$row['MAX_DATE'];
			$tna_task_update_data[$row[csf("job_no")]][207]['min_start_date']=$row['MIN_DATE']; 
			$tna_task_update_data[$row[csf("job_no")]][207]['doneqnty']=$row[csf("qty")];
*/			}
		
		
	}
	unset($pgrey_fab_delv_data_array);
	//print_r($tna_task_update_data);die;
//Grey Fabric Delivery to store................................................................end;	




//Grey Fabric Issue [by Roll]................................................................start;	
		 /* $sql="select c.id as JOB_ID, max(a.issue_date) MAX_DATE, min(a.issue_date) MIN_DATE, sum(b.qnty) QTY  from inv_issue_master a,pro_roll_details b, fabric_sales_order_mst c
where a.id=b.mst_id and b.po_breakdown_id=c.id and a.knit_dye_company=$cbo_company and b.entry_form=61 and a.item_category=13 and b.status_active=1 and b.is_deleted=0   ".where_con_using_array($job_id_arr,0,'c.id')." group by c.id";*/
		  
	 $sql="SELECT b.po_breakdown_id AS JOB_ID,d.ITEM_DESCRIPTION,d.GSM,d.DIA_WIDTH, MAX (a.issue_date) MAX_DATE, MIN (a.issue_date) MIN_DATE, SUM (c.ISSUE_QNTY) QTY FROM inv_issue_master a, pro_roll_details b, inv_grey_fabric_issue_dtls c,PRODUCT_DETAILS_MASTER d  WHERE a.id = b.mst_id AND a.id = c.mst_id and b.DTLS_ID=c.id and d.id=c.PROD_ID AND a.knit_dye_company = $cbo_company  AND b.entry_form = 61  AND a.item_category = 13  AND b.status_active = 1  AND b.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 ".where_con_using_array($job_id_arr,0,'b.po_breakdown_id')." GROUP BY b.po_breakdown_id,d.ITEM_DESCRIPTION,d.GSM,d.DIA_WIDTH";
		  //echo $sql;die;
		 
		$grey_fab_issue_data_array=sql_select($sql);
		foreach($grey_fab_issue_data_array as $row)
		{
			$key=$row['JOB_ID'].'**'.$row['ITEM_DESCRIPTION'].'**'.$row['GSM'].'**'.$row['DIA_WIDTH'];
			$row['PO_ID']=$po_id_by_ref[$key];
			if($row['PO_ID']){
				$tna_task_update_data[$row['PO_ID']][203]['max_start_date']=$row['MAX_DATE'];
				$tna_task_update_data[$row['PO_ID']][203]['min_start_date']=$row['MIN_DATE']; 
				$tna_task_update_data[$row['PO_ID']][203]['doneqnty']=$row['QTY'];
			}
		}
		unset($grey_fab_issue_data_array);
//Grey Fabric Issue................................................................end;	




//Grey Fabric Rec................................................................start;	
		  /*$sql="select b.DTLS_ID AS PO_ID,b.ENTRY_FORM,max(a.receive_date) MAX_DATE,min(a.receive_date) MIN_DATE,sum(b.qnty) QTY
                    from  inv_receive_mas_batchroll a,pro_roll_details b,fabric_sales_order_mst c
                    where a.id=b.mst_id and b.po_breakdown_id=c.id and a.dyeing_company=$cbo_company and b.entry_form in(62,92)    and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($booking_id_arr,0,'b.DTLS_ID')."  group by b.DTLS_ID,b.entry_form";*/
					
	$sql="SELECT b.po_breakdown_id AS JOB_ID, b.ENTRY_FORM,d.ITEM_DESCRIPTION,d.GSM,d.DIA_WIDTH, MAX (a.receive_date) MAX_DATE,  MIN (a.receive_date) MIN_DATE, SUM (b.qnty) QTY
    FROM inv_receive_mas_batchroll a, pro_roll_details b, pro_grey_batch_dtls c, PRODUCT_DETAILS_MASTER d
   WHERE  c.PROD_ID=d.id and  a.id = b.mst_id AND a.id = c.mst_id AND b.DTLS_ID = c.id AND a.dyeing_company = $cbo_company AND b.entry_form IN (62, 92) AND b.status_active = 1 AND b.is_deleted = 0  ".where_con_using_array($job_id_arr,0,'b.po_breakdown_id')."  GROUP BY b.po_breakdown_id, b.entry_form,d.ITEM_DESCRIPTION,d.GSM,d.DIA_WIDTH";
			
			   //echo $sql;die;
			
		$grey_fab_issue_data_array=sql_select($sql);
		foreach($grey_fab_issue_data_array as $row)
		{
			
			$key=$row['JOB_ID'].'**'.$row['ITEM_DESCRIPTION'].'**'.$row['GSM'].'**'.$row['DIA_WIDTH'];
			$row['PO_ID']=$po_id_by_ref[$key];
			
			if($row['ENTRY_FORM']==62 && $row['PO_ID']){
				$tna_task_update_data[$row['PO_ID']][204]['max_start_date']=$row['MAX_DATE'];
				$tna_task_update_data[$row['PO_ID']][204]['min_start_date']=$row['MIN_DATE']; 
				$tna_task_update_data[$row['PO_ID']][204]['doneqnty']+=$row['QTY'];
			}
			elseif($row['ENTRY_FORM']==92 && $row['PO_ID']){
				$tna_task_update_data[$row['PO_ID']][206]['max_start_date']=$row['MAX_DATE'];
				$tna_task_update_data[$row['PO_ID']][206]['min_start_date']=$row['MIN_DATE']; 
				$tna_task_update_data[$row['PO_ID']][206]['doneqnty']+=$row['QTY'];
			}
			
		}
		unset($grey_fab_issue_data_array);
		//print_r($tna_task_update_data);die;
		
//Grey Fabric Rec................................................................end;	


//Grey Fabric Req for batch................................................................start;	
	$sql="select b.po_id as JOB_ID,b.CONSTRUCTION,b.COMPOSITION,b.GSM_WEIGHT,b.DIA_WIDTH,max(a.reqn_date) MAX_DATE,min(a.reqn_date) MIN_DATE,sum(b.reqn_qty) QTY,sum(b.booking_qty) REQ_QTY  from pro_fab_reqn_for_batch_mst a,pro_fab_reqn_for_batch_dtls b
where a.id=b.mst_id and  a.company_id=$cbo_company and b.entry_form=123 and b.is_sales=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($job_id_arr,0,'b.po_id')." group by b.po_id,b.CONSTRUCTION,b.COMPOSITION,b.GSM_WEIGHT,b.DIA_WIDTH";
			
		//echo $sql;die;
			
		$grey_fab_issue_data_array=sql_select($sql);
		foreach($grey_fab_issue_data_array as $row)
		{
			$key=$row['JOB_ID'].'**'.$row['CONSTRUCTION'].','.$row['COMPOSITION'].'**'.$row['GSM_WEIGHT'].'**'.$row['DIA_WIDTH'];
			$row['PO_ID']=$po_id_by_ref[$key];
			if($row['PO_ID']){
				$tna_task_update_data[$row['PO_ID']][202]['max_start_date']=$row['MAX_DATE'];
				$tna_task_update_data[$row['PO_ID']][202]['min_start_date']=$row['MIN_DATE']; 
				$tna_task_update_data[$row['PO_ID']][202]['doneqnty']=$row['QTY'];
			}
		}
		unset($grey_fab_issue_data_array);
		
		//print_r($tna_task_update_data);die;
//Grey Fabric Req for batch................................................................end;	

//Daying Production AOP................................................................start;

$aop_daying_pro_sql="select  c.ORDER_ID as JOB_ID, b.FABRIC_DESCRIPTION, b.COLOR_ID, b.GSM, b.DIA_WIDTH,  sum(c.QUANTITY) as QTY,max(a.PRODUCT_DATE) as MAX_DATE,min(a.PRODUCT_DATE) as MIN_DATE   from SUBCON_PRODUCTION_MST a,subcon_production_dtls b,SUBCON_PRODUCTION_QNTY c where a.id=b.mst_id and b.id=c.dtls_id and b.mst_id=c.mst_id  and a.status_active=1 and b.is_deleted=0  and b.status_active=1 and b.is_deleted=0  ".where_con_using_array($job_id_arr,0,'c.order_id')." group by c.order_id, b.fabric_description, b.color_id, b.gsm, b.dia_width";
	$aop_daying_pro_sql_result=sql_select($aop_daying_pro_sql);
	foreach($aop_daying_pro_sql_result as $row)
	{ 
		$key=$row['JOB_ID'].'**'.$row['COLOR_ID'].'**'.$row['FABRIC_DESCRIPTION'].'**'.$row['GSM'].'**'.$row['DIA_WIDTH'];
		$row['PO_ID']=$po_id_by_job_color_fabric_gsm_dia_ref[$key];
		//echo $key.',';
		
		if($row['PO_ID']){
			$tna_task_update_data[$row['PO_ID']][215]['max_start_date']=$row['MAX_DATE'];
			$tna_task_update_data[$row['PO_ID']][215]['min_start_date']=$row['MIN_DATE']; 
			$tna_task_update_data[$row['PO_ID']][215]['doneqnty']=$row['QTY'];
		}
		 
	}
	unset($aop_daying_pro_sql_result);

 //echo $aop_daying_pro_sql;die;
//print_r($po_id_by_job_color_fabric_gsm_dia_ref);
//die;
//Daying Production AOP................................................................end;


	
//Daying Production................................................................start;	
if( $gross_level==1 ){

	$sql="select a.sales_order_id as JOB_ID,d.ITEM_DESCRIPTION,c.GSM,c.DIA_WIDTH,max(b.process_end_date) MAX_DATE,min(b.process_end_date) MIN_DATE,sum(c.production_qty) QTY from pro_batch_create_mst a, pro_fab_subprocess b ,pro_fab_subprocess_dtls c,PRODUCT_DETAILS_MASTER d where  a.batch_no=b.batch_no and c.PROD_ID=d.id and b.id=c.mst_id and b.load_unload_id = 2  and a.is_sales=1 and b.service_company=$cbo_company  and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and b.status_active=1 and c.status_active=1  ".where_con_using_array($job_id_arr,0,'a.sales_order_id')."  group by a.sales_order_id,d.ITEM_DESCRIPTION,c.GSM,c.DIA_WIDTH";
	$tmpData=array();		
	$grey_fab_issue_data_array=sql_select($sql);
	foreach($grey_fab_issue_data_array as $row)
	{
		$key=$row['JOB_ID'].'**'.$row['ITEM_DESCRIPTION'].'**'.$row['GSM'].'**'.$row['DIA_WIDTH'];
		$row['PO_ID']=$po_id_by_ref[$key];

		if($row['PO_ID']){
			$tna_task_update_data[$row['PO_ID']][61]['max_start_date']=$row['MAX_DATE'];
			$tna_task_update_data[$row['PO_ID']][61]['min_start_date']=$row['MIN_DATE']; 
			$tna_task_update_data[$row['PO_ID']][61]['doneqnty']+=$row['QTY'];
		
			//$tmpData[JOB_NO][$row['PO_ID']]=$row['JOB_NO'];
			$tmpData['DONE_QTY'][$row['JOB_ID']][$row['PO_ID']]=$row['QTY'];
			$tmpData['MAX_DATE'][$row['JOB_ID']]=$row['MAX_DATE'];
			$tmpData['MIN_DATE'][$row['JOB_ID']]=$row['MIN_DATE'];
		
		}
		 
	}

	foreach($job_by_po_arr as $poId=>$jobNo){
		$reqQty=array_sum($req_qty_by_job_po[61][$jobNo]);
		$doneQty=array_sum($tmpData['DONE_QTY'][$jobNo]);
		if($doneQty>=$reqQty){
			$tna_task_update_data[$poId][61]['reqqnty']=$reqQty;
			$tna_task_update_data[$poId][61]['doneqnty']=$doneQty;
			$tna_task_update_data[$poId][61]['max_start_date']=$tmpData['MAX_DATE'][$jobNo];
			$tna_task_update_data[$poId][61]['min_start_date']=$tmpData['MIN_DATE'][$jobNo]; 
		}
	}
	unset($tmpData);
	unset($grey_fab_issue_data_array);


}
else{
	$sql = "select a.sales_order_id as JOB_ID,d.ITEM_DESCRIPTION,c.GSM,c.DIA_WIDTH,e.COLOR_TYPE,max(b.process_end_date) MAX_DATE,min(b.process_end_date) MIN_DATE,sum(c.production_qty) QTY from pro_batch_create_mst a,PRO_BATCH_CREATE_DTLS e, pro_fab_subprocess b ,pro_fab_subprocess_dtls c, PRODUCT_DETAILS_MASTER d where  a.id=e.mst_id and a.batch_no=b.batch_no and c.PROD_ID=d.id and b.id=c.mst_id and b.load_unload_id = 2  and a.is_sales=1 and b.service_company=$cbo_company  and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and b.status_active=1 and c.status_active=1 ".where_con_using_array($job_id_arr,0,'a.sales_order_id')."  group by a.sales_order_id,d.ITEM_DESCRIPTION,c.GSM,c.DIA_WIDTH,e.COLOR_TYPE";
	// echo $sql;die;

	$tmpData=array();		
	$grey_fab_issue_data_array=sql_select($sql);
	foreach($grey_fab_issue_data_array as $row)
	{

		//$key=$row['JOB_ID'].'**'.$row['COLOR_TYPE'].'**'.$row['ITEM_DESCRIPTION'].'**'.$row['GSM'].'**'.$row['DIA_WIDTH'];
		$key=$row['JOB_ID'].'**'.$row['COLOR_TYPE'].'**'.$row['ITEM_DESCRIPTION'].'**'.$row['GSM'];
		$row['PO_ID'] = $po_id_by_color_type_ref[$key];

		if($row['PO_ID']){
			if(in_array($row['COLOR_TYPE'],array(5,7,58,59,60,56,57,45,54,55,49))){//AOP
				$tna_task_update_data[$row['PO_ID']][215]['max_start_date']=$row['MAX_DATE'];
				$tna_task_update_data[$row['PO_ID']][215]['min_start_date']=$row['MIN_DATE']; 
				$tna_task_update_data[$row['PO_ID']][215]['doneqnty']+=$row['QTY'];
			}
			else if(in_array($row['COLOR_TYPE'],array(2,3,4,6,32,33,44,47,48,63))){//YD
				$tna_task_update_data[$row['PO_ID']][216]['max_start_date']=$row['MAX_DATE'];
				$tna_task_update_data[$row['PO_ID']][216]['min_start_date']=$row['MIN_DATE']; 
				$tna_task_update_data[$row['PO_ID']][216]['doneqnty']+=$row['QTY'];
			}
			else if(in_array($row['COLOR_TYPE'],array(1,20,25,26,27,28,29,30,31))){//Solid
				$tna_task_update_data[$row['PO_ID']][61]['max_start_date']=$row['MAX_DATE'];
				$tna_task_update_data[$row['PO_ID']][61]['min_start_date']=$row['MIN_DATE']; 
				$tna_task_update_data[$row['PO_ID']][61]['doneqnty']+=$row['QTY'];
			}
		
		}
		 
	}
	unset($tmpData);
	unset($grey_fab_issue_data_array);
}


//print_r($tna_task_update_data[21483][216]);die;

//Daying Production................................................................end;	





//Finish Production................................................................start;
//Finish Fabric Production and QC By Roll.....
	$sql ="SELECT c.COLOR_ID,a.sales_order_id as JOB_ID,c.FABRIC_DESCRIPTION_ID,c.gsm as GSM,c.width as DIA_WIDTH,sum(c.receive_qnty) as RECEIVE_QNTY,max(e.RECEIVE_DATE) as MAX_DATE,min(e.RECEIVE_DATE) as MIN_DATE
  FROM pro_batch_create_mst a, pro_batch_create_dtls b,pro_finish_fabric_rcv_dtls c, pro_roll_details d,INV_RECEIVE_MASTER e
 WHERE a.id = b.mst_id and b.barcode_no=d.barcode_no  and d.mst_id=e.id and c.id=d.dtls_id and d.entry_form=66 and d.status_active=1 and d.is_deleted=0 AND a.entry_form IN (0, 66) AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1AND b.is_deleted = 0  AND b.barcode_no > 0 and a.company_id=$cbo_company   ".where_con_using_array($job_id_arr,0,'a.sales_order_id')." group by c.COLOR_ID,a.sales_order_id,c.FABRIC_DESCRIPTION_ID,c.gsm,c.width";
//echo $sql;die;
		$fin_fab_data_array=sql_select($sql);
		foreach($fin_fab_data_array as $row)
		{
			
			$key=$row['JOB_ID'].'**'.$row['COLOR_ID'].'**'.$row['FABRIC_DESCRIPTION_ID'].'**'.$row['GSM'].'**'.$row['DIA_WIDTH'];
			$row['PO_ID']=$po_id_by_job_color_determa_gsm_dia_ref[$key];
			 //echo $row['PO_ID'].',';

			if($row['PO_ID']){
				$tna_task_update_data[$row['PO_ID']][64]['max_start_date']=$row['MAX_DATE'];
				$tna_task_update_data[$row['PO_ID']][64]['min_start_date']=$row['MIN_DATE']; 
				$tna_task_update_data[$row['PO_ID']][64]['doneqnty']+=$row['RECEIVE_QNTY'];
			}
		}
		unset($fin_fab_data_array);

 //print_r($tna_task_update_data[16417][64]);die;
 $sql = "select c.sales_order_id as JOB_ID,b.COLOR_ID,b.FABRIC_DESCRIPTION_ID,b.GSM,a.entry_form,a.receive_basis,c.sales_order_no job_no, max(a.receive_date) MAX_DATE, min(a.receive_date) MIN_DATE,sum( b.receive_qnty) qty 
 from inv_receive_master a, pro_finish_fabric_rcv_dtls b,pro_batch_create_mst c  where a.id=b.mst_id  and a.item_category=2 and a.entry_form in(7,37) and b.batch_id=c.id and b.is_sales=1 and a.receive_basis in(5,10) and a.knitting_company=$cbo_company and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and b.status_active=1 and c.status_active=1  ".where_con_using_array($job_id_arr,0,'c.sales_order_id')." group by c.sales_order_id,b.COLOR_ID,b.FABRIC_DESCRIPTION_ID,b.GSM,c.sales_order_no,a.entry_form,a.receive_basis";
  //echo $sql;die;
		 
	 $fin_fab_data_array=sql_select($sql);
	 foreach($fin_fab_data_array as $row)
	 {
		 $key=$row['JOB_ID'].'**'.$row['COLOR_ID'].'**'.$row['FABRIC_DESCRIPTION_ID'].'**'.$row['GSM'];
		 $row['PO_ID']=$po_id_by_job_color_determa_gsm_dia_ref[$key];
		 
		 
		 if($row[csf("entry_form")]==7 and $row[csf("receive_basis")]==5){
			 $tna_task_update_data[$row['PO_ID']][64]['max_start_date']=$row['MAX_DATE'];
			 $tna_task_update_data[$row['PO_ID']][64]['min_start_date']=$row['MIN_DATE']; 
			 $tna_task_update_data[$row['PO_ID']][64]['doneqnty']+=$row[csf("qty")];
		 }
		 else if($row[csf("entry_form")]==37 and $row[csf("receive_basis")]==10){
			 $tna_task_update_data[$row['PO_ID']][73]['max_start_date']=$row['MAX_DATE'];
			 $tna_task_update_data[$row['PO_ID']][73]['min_start_date']=$row['MIN_DATE']; 
			 $tna_task_update_data[$row['PO_ID']][73]['doneqnty']+=$row[csf("qty")];
		 }
	 }
	 unset($fin_fab_data_array); 
//Finish Production................................................................end;	



//Finish Production deliver................................................................start;	
	$sql="select a.ENTRY_FORM,b.order_id as JOB_ID,c.ITEM_DESCRIPTION,c.GSM,c.DIA_WIDTH,max(a.delevery_date) MAX_DATE,min(a.delevery_date) MIN_DATE,sum(b.current_delivery) QTY  from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b , PRODUCT_DETAILS_MASTER c
 where a.id=b.mst_id  and b.PRODUCT_ID=c.id  and a.status_active=1 and b.status_active=1  and c.status_active=1 and c.status_active=1 and a.company_id=$cbo_company ".where_con_using_array($job_id_arr,0,'b.order_id')." group by b.order_id,a.entry_form,c.ITEM_DESCRIPTION,c.GSM,c.DIA_WIDTH";
		 
		 //echo $sql;die;
			
		$fin_fab_data_array=sql_select($sql);
		foreach($fin_fab_data_array as $row)
		{
			$key=$row['JOB_ID'].'**'.$row['CONST_COMPOSITION'].'**'.$row['GSM'].'**'.$row['DIA_WIDTH'];
			$row['PO_ID']=$po_id_by_ref[$key];
			
			if($row['ENTRY_FORM']==54 && $row['PO_ID']){
				$tna_task_update_data[$row['PO_ID']][207]['max_start_date']=$row['MAX_DATE'];
				$tna_task_update_data[$row['PO_ID']][207]['min_start_date']=$row['MIN_DATE']; 
				$tna_task_update_data[$row['PO_ID']][207]['doneqnty']=$row['QTY'];
			}
		}
		unset($fin_fab_data_array);
//Finish Production deliver................................................................end;	


//Yarn Dyeing Work Order-Sales................................................................start;	
//Note:not done;
	$sql="select b.job_no ,max(a.booking_date) MAX_DATE,min(a.booking_date) MIN_DATE,sum(b.yarn_wo_qty) qty
	 from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.entry_form
	=135 and b.entry_form=135 and a.company_id=$cbo_company and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and a.is_deleted=0 and a.item_category_id=24 ".where_con_using_array($job_id_arr,0,'b.job_no_id')." group by b.job_no";
			 
		 //echo $sql;die;
			
		$yarn_dye_wo_sales_data_array=sql_select($sql);
		foreach($yarn_dye_wo_sales_data_array as $row)
		{
			$tna_task_update_data[$row[csf("job_no")]][51]['reqqnty']+=$row[csf("qty")];
			
		}
		unset($yarn_dye_wo_sales_data_array);


//Yarn Dyeing Work Order-Sales................................................................end;	

//Knit Grey Fabric Roll Receive.................................................start;
$grapy_fab_rec_sql = "SELECT  c.PO_BREAKDOWN_ID as JOB_ID,  b.PROD_ID, b.FEBRIC_DESCRIPTION_ID, b.GSM, b.WIDTH,   b.BODY_PART_ID,  b.UOM, b.COLOR_ID,max(a.RECEIVE_DATE) as MAX_DATE,min(a.RECEIVE_DATE) as MIN_DATE,sum(c.QNTY) as QTY 
		FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
		WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2  and c.entry_form=2 and a.knitting_company=$cbo_company  and c.status_active=1 and c.is_deleted=0 ".where_con_using_array($job_id_arr,0,'c.PO_BREAKDOWN_ID')." GROUP BY c.PO_BREAKDOWN_ID,  b.PROD_ID, b.FEBRIC_DESCRIPTION_ID, b.GSM, b.WIDTH,   b.BODY_PART_ID,  b.UOM, b.COLOR_ID";
//echo $grapy_fab_rec_sql;die;
	$grapy_fab_rec_sql_res=sql_select($grapy_fab_rec_sql);
	foreach($grapy_fab_rec_sql_res as $row)
	{
		
		$key=$row['JOB_ID'].'**'.$row['COLOR_ID'].'**'.$row['FEBRIC_DESCRIPTION_ID'].'**'.$row['GSM'].'**'.$row['WIDTH'];
		$row['PO_ID']=$po_id_by_job_color_determa_gsm_dia_ref[$key];
		
		if($row['PO_ID']){
			$tna_task_update_data[$row['PO_ID']][72]['max_start_date']=$row['MAX_DATE'];
			$tna_task_update_data[$row['PO_ID']][72]['min_start_date']=$row['MIN_DATE']; 
			$tna_task_update_data[$row['PO_ID']][72]['doneqnty']+=$row['QTY'];
		}
	}
	unset($grapy_fab_rec_sql_res);

//Knit Grey Fabric Roll Receive.................................................end;






//Note:not done;
//(YD) Yarn Issue................................................................start;	
	$sql="select c.job_no,min(a.issue_date) MIN_DATE, max(a.issue_date) MAX_DATE,sum(b.cons_quantity) qty
	from inv_issue_master a, inv_transaction b,fabric_sales_order_mst c
	where a.id=b.mst_id and b.job_no=c.job_no  and b.item_category=1 and a.issue_purpose=2  and a.company_id=$cbo_company and a.is_deleted=0 and b.transaction_type=2 and b.is_deleted=0 and c.is_deleted=0 and a.status_active=1 and b.status_active=1 and c.status_active=1  ".where_con_using_array($job_id_arr,0,'c.id')." group by c.job_no";
		//echo $sql;die; 
			
		$yarn_issue_data_array=sql_select($sql);
		foreach($yarn_issue_data_array as $row)
		{
			$tna_task_update_data[$row[csf("job_no")]][51]['max_start_date']=$row['MAX_DATE'];
			$tna_task_update_data[$row[csf("job_no")]][51]['min_start_date']=$row['MIN_DATE']; 
			$tna_task_update_data[$row[csf("job_no")]][51]['doneqnty']=$row[csf("qty")];
			
			//reqqnty of issue receive..............
			//$tna_task_update_data[$row[csf("job_no")]][52]['reqqnty']+=$row[csf("qty")];
			
		}
		unset($yarn_issue_data_array);

//echo $sql;die;
//Yarn Issue................................................................end;	

//Note:done;
// (Y/D)Yarn receive................................................................start;	
	/*$sql="select c.job_no,min(a.receive_date) MIN_DATE, max(a.receive_date) MAX_DATE,sum(b.cons_quantity) qty
	from inv_receive_master a, inv_transaction b,fabric_sales_order_mst c
	where a.id=b.mst_id and b.transaction_type=1 and b.job_no=c.job_no  and a.item_category=1 and a.receive_purpose=2 and b.item_category=1  and a.company_id=$cbo_company and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 ".where_con_using_array($job_id_arr,0,'c.id')." group by c.job_no";
			    //echo $sql;die;
			
		$yarn_issue_data_array=sql_select($sql);
		foreach($yarn_issue_data_array as $row)
		{
			$tna_task_update_data[$row[csf("job_no")]][52]['max_start_date']=$row['MAX_DATE'];
			$tna_task_update_data[$row[csf("job_no")]][52]['min_start_date']=$row['MIN_DATE']; 
			$tna_task_update_data[$row[csf("job_no")]][52]['doneqnty']=$row[csf("qty")];
		}
		unset($yarn_issue_data_array);*/
		
$sql ="SELECT b.id as ALLOCATION_DTLS_ID,a.JOB_NO,c.COMPANY_ID,d.id as PO_ID, b.QNTY as QTY,D.GREY_QTY, max(a.ALLOCATION_DATE) as MAX_DATE, min(a.ALLOCATION_DATE) as MIN_DATE
  FROM inv_material_allocation_mst a,INV_MATERIAL_ALLOCATION_DTLS b,fabric_sales_order_mst c,FABRIC_SALES_ORDER_DTLS d 
 WHERE a.id=b.MST_ID and a.JOB_NO=c.JOB_NO and c.id=d.mst_id  AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1  AND b.is_deleted = 0  AND c.status_active = 1  AND c.is_deleted = 0  and c.COMPANY_ID=$cbo_company  ".where_con_using_array($job_id_arr,0,'c.id')."  and b.IS_SALES=1 and a.IS_DYIED_YARN=1 group by b.id,a.JOB_NO,c.COMPANY_ID,d.id, b.QNTY,D.GREY_QTY";
    //echo $sql;die;
	$yarn_allocated_data_array=sql_select($sql);
	$tmpData=array();
	foreach($yarn_allocated_data_array as $row)
	{
		$tna_task_update_data[$row['PO_ID']][52]['max_start_date']=$row['MAX_DATE'];
		$tna_task_update_data[$row['PO_ID']][52]['min_start_date']=$row['MIN_DATE']; 
		$tna_task_update_data[$row['PO_ID']][52]['doneqnty']=$row['QTY'];
			
		$tmpData['JOB_NO'][$row['PO_ID']]=$row['JOB_NO'];
		$tmpData['ALLO_QTY'][$row['JOB_NO']][$row['ALLOCATION_DTLS_ID']]=$row['QTY'];
		$tmpData['MAX_DATE'][$row['JOB_NO']]=$row['MAX_DATE'];
		$tmpData['MIN_DATE'][$row['JOB_NO']]=$row['MIN_DATE'];
	}
 
	foreach($job_by_po_arr as $poId=>$jobNo){
		$reqQty=array_sum($req_qty_by_job_po[52][$jobNo]);
		$doneQty=array_sum($tmpData['ALLO_QTY'][$jobNo]);
		if($doneQty>=$reqQty){
			$tna_task_update_data[$poId][52]['reqqnty']=$reqQty;
			$tna_task_update_data[$poId][52]['doneqnty']=$doneQty;
			$tna_task_update_data[$poId][52]['max_start_date']=$tmpData['MAX_DATE'][$jobNo];
			$tna_task_update_data[$poId][52]['min_start_date']=$tmpData['MIN_DATE'][$jobNo]; 
		}
	}
	unset($yarn_allocated_data_array);
 	unset($tmpData);
		

//Yarn receive................................................................end;	

//Note:not varifiy by ref;
//Yarn Purchase Requisition................................................................start;	
	$sql="select b.JOB_ID,c.ITEM_DESCRIPTION,c.GSM,c.DIA_WIDTH ,max(a.requisition_date) MAX_DATE,min(a.requisition_date) MIN_DATE,sum(b.quantity) QTY from inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b,PRODUCT_DETAILS_MASTER c where a.id=b.mst_id and b.PRODUCT_ID=c.id and a.entry_form=70 and a.basis=4 and a.status_active=1  and b.is_deleted=0 and b.status_active=1  and a.is_deleted=0 and C.status_active=1  and C.is_deleted=0 and a.item_category_id=1 ".where_con_using_array($job_id_arr,0,'b.job_id')." group by b.JOB_ID,c.ITEM_DESCRIPTION,c.GSM,c.DIA_WIDTH";
		// echo $sql;die;
		
		$yarn_purchase_req_data_array=sql_select($sql);
		foreach($yarn_purchase_req_data_array as $row)
		{
			
			$key=$row['JOB_ID'].'**'.$row['CONST_COMPOSITION'].'**'.$row['GSM'].'**'.$row['DIA_WIDTH'];
			$row['PO_ID']=$po_id_by_ref[$key];
			if($row['PO_ID']){
				$tna_task_update_data[$row['PO_ID']][45]['max_start_date']=$row['MAX_DATE'];
				$tna_task_update_data[$row['PO_ID']][45]['min_start_date']=$row['MIN_DATE']; 
				$tna_task_update_data[$row['PO_ID']][45]['doneqnty']+=$row['QTY'];
				$tna_task_update_data[$row['PO_ID']][46]['reqqnty']+=$row['QTY'];
			}
		}
		unset($yarn_purchase_req_data_array);



//echo $sql;die;

//Yarn Purchase Requisition................................................................end;	


//Note:not done;
//Yarn Purchase wo................................................................end;	
$sql = "select  a.entry_form,b.job_no, sum(b.req_quantity) req_qty, sum(b.supplier_order_quantity) qty ,max(a.wo_date) MAX_DATE,min(a.wo_date) MIN_DATE
        from  wo_non_order_info_mst a, wo_non_order_info_dtls b
        where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$cbo_company and a.id=b.mst_id ".where_con_using_array($job_id_arr,0,'b.job_id')." group by  b.job_no,a.entry_form";
	 
	 //echo $sql;die;
			
		$yarn_issue_data_array=sql_select($sql);
		foreach($yarn_issue_data_array as $row)
		{
			$tna_task_update_data[$row[csf("job_no")]][46]['max_start_date']=$row['MAX_DATE'];
			$tna_task_update_data[$row[csf("job_no")]][46]['min_start_date']=$row['MIN_DATE']; 
			$tna_task_update_data[$row[csf("job_no")]][46]['doneqnty']+=$row[csf("qty")];
			/*if($row[csf("entry_form")]==144){
				$tna_task_update_data[$row[csf("job_no")]][47]['reqqnty']+=$row[csf("qty")];
			}*/
		}
		unset($yarn_issue_data_array);
//Yarn Purchase wo................................................................end;	


//Yarn Rec................................................................start;	
 
	/*$sql = "select f.job_no,max(b.transaction_date) MAX_DATE,min(b.transaction_date) MIN_DATE,sum(b.cons_quantity) qty,sum(f.req_quantity) req_qty
	 from inv_transaction b, com_pi_item_details d, wo_non_order_info_mst e, wo_non_order_info_dtls f
	where b.pi_wo_batch_no=d.pi_id and d.work_order_no=e.wo_number and e.id=f.mst_id and e.entry_form=144 ".where_con_using_array($job_id_arr,0,'f.job_id')." and b.item_category=1 and b.transaction_type=1 and b.receive_basis=1 and e.pay_mode=2 and   b.is_deleted=0 and e.is_deleted=0 and f.is_deleted=0  and b.status_active=1 and e.status_active=1 and f.status_active=1 
	group by f.job_no
	union all
	select f.job_no,max(a.receive_date) MAX_DATE,min(a.receive_date) MIN_DATE,sum(b.cons_quantity) qty,sum(f.req_quantity) req_qty
	 from inv_transaction b, inv_receive_master a, wo_non_order_info_mst e, wo_non_order_info_dtls f
	where a.id=b.mst_id and a.booking_id=e.id and e.id=f.mst_id and e.entry_form=144 ".where_con_using_array($job_id_arr,0,'f.job_id')." and b.item_category=1 and b.transaction_type=1 and a.receive_basis=2 and a.entry_form=1 and e.pay_mode <>2 and a.is_deleted=0 and b.is_deleted=0 and e.is_deleted=0 and f.is_deleted=0  and b.status_active=1 and e.status_active=1 and f.status_active=1 
	group by f.job_no";
	// echo $sql;die;
		$yarn_rec_data_array=sql_select($sql);
		foreach($yarn_rec_data_array as $row)
		{
				$tna_task_update_data[$row[csf("job_no")]][47]['max_start_date']=$row['MAX_DATE'];
				$tna_task_update_data[$row[csf("job_no")]][47]['min_start_date']=$row['MIN_DATE']; 
				$tna_task_update_data[$row[csf("job_no")]][47]['doneqnty']=$row[csf("qty")];
		}
		unset($yarn_rec_data_array);*/
		
$sql="select e.id as PO_ID,e.GREY_QTY, c.FSO_NO,a.TO_COMPANY,c.TRANSFER_QNTY,c.id as ITEM_TRANSFER_DTLS_ID, max(a.TRANSFER_DATE) MAX_DATE,min(a.TRANSFER_DATE) MIN_DATE from INV_ITEM_TRANSFER_MST a,INV_TRANSACTION b,INV_ITEM_TRANSFER_DTLS c,fabric_sales_order_mst d,FABRIC_SALES_ORDER_DTLS e where d.id= e.mst_id and d.JOB_NO = c.FSO_NO and a.id=b.mst_id and  a.id=c.mst_id  and a.TO_COMPANY=$cbo_company ".where_con_using_array($job_id_arr,0,'d.id')."  and b.transaction_type in(5,6) group by e.id,e.GREY_QTY, c.id,c.FSO_NO,a.TO_COMPANY,c.TRANSFER_QNTY";
//echo $sql;die;
		$tmpData=array();
		$yarn_transfer_data_array=sql_select($sql);
		foreach($yarn_transfer_data_array as $row)
		{
			$tna_task_update_data[$row['PO_ID']][47]['max_start_date']=$row['MAX_DATE'];
			$tna_task_update_data[$row['PO_ID']][47]['min_start_date']=$row['MIN_DATE']; 
			$tna_task_update_data[$row['PO_ID']][47]['doneqnty']=$row['TRANSFER_QNTY'];
				
			$tmpData['JOB_TRANS_QTY'][$row['FSO_NO']][$row['ITEM_TRANSFER_DTLS_ID']]=$row['TRANSFER_QNTY'];
			$tmpData['JOB_QTY'][$row['FSO_NO']][$row['PO_ID']]=$row['GREY_QTY'];
			$tmpData['MAX_DATE'][$row['JOB_NO']]=$row['MAX_DATE'];
			$tmpData['MIN_DATE'][$row['JOB_NO']]=$row['MIN_DATE'];
			
		}
		
		foreach($job_by_po_arr as $poId=>$jobNo){
			$reqQty=array_sum($req_qty_by_job_po[47][$jobNo]);
			$doneQty=array_sum($tmpData['JOB_TRANS_QTY'][$jobNo]);
			if($doneQty>=$reqQty){
				$tna_task_update_data[$poId][47]['reqqnty']=$reqQty;
				$tna_task_update_data[$poId][47]['doneqnty']=$doneQty;
				$tna_task_update_data[$poId][47]['max_start_date']=$tmpData['MAX_DATE'][$jobNo];
				$tna_task_update_data[$poId][47]['min_start_date']=$tmpData['MIN_DATE'][$jobNo]; 
			}
		}
		
		unset($yarn_transfer_data_array);
		unset($tmpData);

	
		
	//print_r($tna_task_update_data[15516][47]);die;	
		
//Yarn Rec................................................................end;	


//Yarn Allocation...........................................start;
$sql ="SELECT b.id as ALLOCATION_DTLS_ID,c.id as JOB_NO,c.COMPANY_ID,d.id as PO_ID, b.QNTY as QTY,D.GREY_QTY, max(a.ALLOCATION_DATE) as MAX_DATE, min(a.ALLOCATION_DATE) as MIN_DATE
  FROM inv_material_allocation_mst a,INV_MATERIAL_ALLOCATION_DTLS b,fabric_sales_order_mst c,FABRIC_SALES_ORDER_DTLS d 
 WHERE a.id=b.MST_ID and a.JOB_NO=c.JOB_NO and c.id=d.mst_id  AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1  AND b.is_deleted = 0  AND c.status_active = 1  AND c.is_deleted = 0  and c.COMPANY_ID=$cbo_company  ".where_con_using_array($job_id_arr,0,'c.id')."  and b.IS_SALES=1 group by b.id,c.id,c.COMPANY_ID,d.id, b.QNTY,D.GREY_QTY";
 // echo $sql;die;
 
	$yarn_allocation_data_array=sql_select($sql);
	$tmpData=array();
	foreach($yarn_allocation_data_array as $row)
	{
		$tna_task_update_data[$row['PO_ID']][48]['max_start_date']=$row['MAX_DATE'];
		$tna_task_update_data[$row['PO_ID']][48]['min_start_date']=$row['MIN_DATE']; 
		$tna_task_update_data[$row['PO_ID']][48]['doneqnty']=$row['QTY'];
			
		$tmpData['ALLO_QTY'][$row['JOB_NO']][$row['ALLOCATION_DTLS_ID']]=$row['QTY'];
		$tmpData['JOB_QTY'][$row['JOB_NO']][$row['PO_ID']]=$row['GREY_QTY'];
		$tmpData['MAX_DATE'][$row['JOB_NO']]=$row['MAX_DATE'];
		$tmpData['MIN_DATE'][$row['JOB_NO']]=$row['MIN_DATE'];
		
	}
 
	foreach($job_by_po_arr as $poId=>$jobNo){
		$reqQty=array_sum($req_qty_by_job_po[48][$jobNo]);
		$doneQty=array_sum($tmpData['ALLO_QTY'][$jobNo]);
		if($doneQty>=$reqQty){
			$tna_task_update_data[$poId][48]['reqqnty']=$reqQty;
			$tna_task_update_data[$poId][48]['doneqnty']=$doneQty;
			$tna_task_update_data[$poId][48]['max_start_date']=$tmpData['MAX_DATE'][$jobNo];
			$tna_task_update_data[$poId][48]['min_start_date']=$tmpData['MIN_DATE'][$jobNo]; 
		}
	}
 
  	unset($yarn_allocation_data_array);
 	unset($tmpData);
 
 
 //print_r($req_qty_by_job_po[48]);die;
 
//Yarn Allocation...........................................end;

//Finish Fabric Delivery To Garments................................................................start;
	//$selectDate="max(TO_CHAR(b.insert_date ,'DD-MON-YY')) MAX_DATE,min(TO_CHAR(b.insert_date ,'DD-MON-YY')) MIN_DATE";
		 
	/*$ffdtgSql="select  $selectDate , sum(b.issue_qnty) delivery_qty, e.job_no as fso_id
	 from inv_issue_master a,inv_finish_fabric_issue_dtls b,product_details_master d,fabric_sales_order_mst e
	 where a.company_id=1 and a.entry_form=224 and a.id=b.mst_id and b.prod_id=d.id and b.order_id=e.id and a.status_active='1' and a.is_deleted='0' ".where_con_using_array($job_id_arr,0,'e.id')." and b.status_active=1 and d.status_active=1 and e.status_active=1 
	 group by e.job_no";*/

	
	 
	
	$selectDate="max(TO_CHAR(b.insert_date ,'DD-MON-YY')) MAX_DATE,min(TO_CHAR(b.insert_date ,'DD-MON-YY')) MIN_DATE";
	$ffdtgSql="SELECT $selectDate,d.DETARMINATION_ID,d.GSM, d.dia_width as DIA_WIDTH, sum(a.qnty) as QTY,c.JOB_NO, c.id as JOB_ID from pro_roll_details a, inv_finish_fabric_issue_dtls b, fabric_sales_order_mst c , product_details_master d, pro_batch_create_mst e where a.dtls_id=b.id ".where_con_using_array($job_id_arr,0,'c.id')." and b.batch_id=e.id and a.po_breakdown_id=c.id and b.prod_id=d.id and a.entry_form=318 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.is_returned!=1 group by d.DETARMINATION_ID,d.GSM, d.dia_width ,c.JOB_NO, c.id "; 
	// echo $ffdtgSql;die;
	$ff_delivery_to_grms_result = sql_select( $ffdtgSql );
	foreach( $ff_delivery_to_grms_result as $row ) 
	{
		$key=$row['JOB_ID'].'**'.$row['DETARMINATION_ID'].'**'.$row['GSM'].'**'.$row['DIA_WIDTH'];
		$row['PO_ID']=$po_id_by_determination_id_ref[$key];
		
		$tna_task_update_data[$row['PO_ID']][239]['max_start_date']=$row['MAX_DATE'];
		$tna_task_update_data[$row['PO_ID']][239]['min_start_date']=$row['MIN_DATE']; 
		$tna_task_update_data[$row['PO_ID']][239]['doneqnty']+=$row['QTY'];
		
		$tmpData['DONE_QTY'][$row['JOB_ID']][$row['PO_ID']]=$row['QTY'];
		$tmpData['MAX_DATE'][$row['JOB_ID']]=$row['MAX_DATE'];
		$tmpData['MIN_DATE'][$row['JOB_ID']]=$row['MIN_DATE'];
	}
	
	$nextProcessFlag = 1;
	foreach($job_by_po_arr as $poId=>$jobNo){
		$reqQty=array_sum($req_qty_by_job_po[239][$jobNo]);
		$doneQty=array_sum($tmpData['DONE_QTY'][$jobNo]);
		if($doneQty >= $reqQty){
			$tna_task_update_data[$poId][239]['reqqnty']=$reqQty;
			$tna_task_update_data[$poId][239]['doneqnty']=$doneQty;
			$tna_task_update_data[$poId][239]['max_start_date']=$tmpData['MAX_DATE'][$jobNo];
			$tna_task_update_data[$poId][239]['min_start_date']=$tmpData['MIN_DATE'][$jobNo]; 
			$nextProcessFlag = 0;
		}
	}
	unset($tmpData);
	unset($ff_delivery_to_grms_result);

	if($nextProcessFlag == 1){
		$ffdtgSql = "SELECT c.ID,d.DETARMINATION_ID,d.GSM,d.DIA_WIDTH, MAX (a.issue_date) AS MAX_DATE, MIN (a.issue_date) AS MIN_DATE, SUM (b.issue_qnty) AS ISSUE_QTY, SUM (f.job_wise_qnty) AS JOB_QTY FROM inv_issue_master a, fabric_sales_order_mst c,product_details_master d, inv_finish_fabric_issue_dtls b LEFT JOIN order_wise_pro_details f ON b.id = f.dtls_id AND f.entry_form = 224 AND f.job_id IS NOT NULL AND f.job_wise_qnty <> 0 WHERE b.PROD_ID = d.id and a.entry_form = 224 AND a.id = b.mst_id AND b.order_id = CAST (c.id AS VARCHAR2 (4000)) AND a.fso_id = c.id ".where_con_using_array($job_id_arr,0,'c.id')." GROUP BY c.ID,d.DETARMINATION_ID,d.GSM,d.DIA_WIDTH";
		  //echo $ffdtgSql;die;
		$ff_delivery_to_grms_result = sql_select( $ffdtgSql );
		foreach( $ff_delivery_to_grms_result as $row ) 
		{
			$key=$row['ID'].'**'.$row['DETARMINATION_ID'].'**'.$row['GSM'].'**'.$row['DIA_WIDTH'];
			$row['PO_ID']=$po_id_by_determination_id_ref[$key];
			
			
			$tna_task_update_data[$row['PO_ID']][239]['reqqnty']+=$row['JOB_QTY'];
			$tna_task_update_data[$row['PO_ID']][239]['doneqnty']+=$row['ISSUE_QTY'];
			$tna_task_update_data[$row['PO_ID']][239]['max_start_date']=$row['MAX_DATE'];
			$tna_task_update_data[$row['PO_ID']][239]['min_start_date']=$row['MIN_DATE'];
		}
	}
	unset($ff_delivery_to_grms_result);
	
	// print_r($tna_task_update_data[4029][239]);die;
	
		
//Finish Fabric Delivery To Garments..................................................................end;	
	
	
 
//Process History data ........................................................................................start;		
		
	$sql = "SELECT JOB_NO,TASK_NUMBER,PO_NUMBER_ID,ACTUAL_START_DATE,ACTUAL_FINISH_DATE,TASK_START_DATE,TASK_FINISH_DATE ,PLAN_START_FLAG,PLAN_FINISH_FLAG FROM tna_plan_actual_history WHERE status_active =1 and is_deleted=0 and task_type=2 ".where_con_using_array($booking_id_arr,0,'po_number_id')."";
	  //echo $sql;die;
	$result = sql_select( $sql );
	$tna_updated_date = array();
	foreach( $result as $row ) 
	{
		$tna_updated_date[$row['PO_NUMBER_ID']][$row['TASK_NUMBER']]['start']=$row['ACTUAL_START_DATE'];
		$tna_updated_date[$row['PO_NUMBER_ID']][$row['TASK_NUMBER']]['finish']=$row['ACTUAL_FINISH_DATE'];
		$tna_updated_date[$row['PO_NUMBER_ID']][$row['TASK_NUMBER']]['planstart']=$row['TASK_START_DATE'];
		$tna_updated_date[$row['PO_NUMBER_ID']][$row['TASK_NUMBER']]['planfinish']=$row['TASK_FINISH_DATE'];
		$tna_updated_date[$row['PO_NUMBER_ID']][$row['TASK_NUMBER']]['planstartflag']=$row['PLAN_START_FLAG'];
		$tna_updated_date[$row['PO_NUMBER_ID']][$row['TASK_NUMBER']]['planfinishflag']=$row['PLAN_FINISH_FLAG'];
	}
//Process History data........................................................................................end;		

	
	
			
//Process Master data........................................................................................start;		
			
	$sql = "SELECT ID,JOB_NO,PO_NUMBER_ID,TASK_CATEGORY,TASK_NUMBER,ACTUAL_START_DATE,ACTUAL_FINISH_DATE,TEMPLATE_ID FROM tna_process_mst WHERE status_active =1 and is_deleted = 0 and task_type=2 ".where_con_using_array($booking_id_arr,0,'po_number_id')."";
	 //echo $sql;die;
	
	$result = sql_select( $sql );
	$tna_process_list = array();$tna_process_details = array();$changed_templates=array();
	foreach( $result as $row ) 
	{
		if( $booking_template[$row['PO_NUMBER_ID']]==$row['TEMPLATE_ID'] )
		{
			$tna_process_list[$row['PO_NUMBER_ID']][$row['TASK_NUMBER']]= $row['ID'];
			$tna_process_details[$row['PO_NUMBER_ID']]['start']=$row['ACTUAL_START_DATE'];
			$tna_process_details[$row['PO_NUMBER_ID']]['finish']=$row['ACTUAL_FINISH_DATE'];
		}
		else if( $row['TEMPLATE_ID']=='' )
		{
			$tna_process_list[$row['PO_NUMBER_ID']][$row['TASK_NUMBER']]= $row['ID'];
			$tna_process_details[$row['PO_NUMBER_ID']]['start']=$row['ACTUAL_START_DATE'];
			$tna_process_details[$row['PO_NUMBER_ID']]['finish']=$row['ACTUAL_FINISH_DATE'];
		}
		else
		{
			$changed_templates[$row['PO_NUMBER_ID']]=$row['PO_NUMBER_ID'];
		}
	}
	
	//print_r($changed_templates);die;
	
	if( count($changed_templates)>0 )
	{
		$con = connect();
		$rid=execute_query("delete FROM tna_process_mst WHERE po_number_id in(".implode(",",$changed_templates).")",1);
		if( $db_type==2 ) oci_commit($con); 
		disconnect($con);
	}

//Process Master data........................................................................................end;		

 //print_r($to_process_task);die;

//---------------------------------------------------------------------------------------------------------
	
	// print_r($to_process_task);die;
	//print_r($tna_process_details[15594]);die;//15185,15186 ,15187, 15188, 15189, 15191
 	

	// foreach($tna_task_update_data as $jid=>$val){var_dump($tna_task_update_data[$jid][48]);}die; 
	
	
	
	$insert_string=array();
	$data_array_tna_process_up=array();
	$process_id_up_array=array();

	$field_array_tna_process="id,template_id,job_no,po_number_id,po_receive_date,shipment_date,task_category,task_number,target_date,task_start_date,task_finish_date,notice_date_start,notice_date_end,process_date,sequence_no,plan_start_flag,plan_finish_flag,status_active,is_deleted,task_type";
	$field_array_tna_process_up="actual_start_date*actual_finish_date";
	$approval_array=array();
		 //print_r($to_process_task);;die;
		
		foreach($booking_details as $row )// Non Process Starts Here
		{
			foreach( $template_wise_task[$row['template_id']]  as $task_id=>$row_task)
			{  
				 
				if($to_process_task[$row['po_id']][$row_task['task_name']]!="")
				{
					
					if ($tna_process_type==1)
					{ 
						if($db_type==0) $target_date=add_date($row['delivery_date'] ,- $row_task['deadline']);
						else $target_date=change_date_format(trim(add_date($row['delivery_date'] ,- $row_task['deadline'])),'','',1);
						 
						$to_add_days=$row_task['execution_days']-1;
						if($db_type==0) $start_date=add_date($target_date ,-$to_add_days);
						else $start_date=change_date_format(trim(add_date($target_date ,-$to_add_days)),'','',1);
						 
						$finish_date=$target_date;
						$to_add_days=$row_task['notice_before'];
						
						if($db_type==0) $notice_date_start=add_date($start_date ,-$to_add_days);
						else $notice_date_start=change_date_format(trim(add_date($start_date ,-$to_add_days)),'','',1);
						 
						
						if($db_type==0) $notice_date_end=add_date($finish_date ,-$to_add_days);
						else $notice_date_end=change_date_format(trim(add_date($finish_date ,-$to_add_days)),'','',1);
					}
					else
					{
						 
						if($db_type==0) $target_date=add_date($row['booking_date'] , $row_task['execution_days']);
						else $target_date=change_date_format(trim(add_date($row['booking_date'] ,$row_task['execution_days'])),'','',1);
						 
						//$to_add_days=$row_task['execution_days']-1;
						if($db_type==0) $start_date=add_date($row['booking_date'] ,$row_task['deadline']);
						else $start_date=change_date_format(trim(add_date($row['booking_date'] ,$row_task['deadline'])),'','',1);
						 
						$finish_date=$target_date;
						$to_add_days=$row_task['notice_before'];
						
						if($db_type==0) $notice_date_start=add_date($start_date ,-$to_add_days);
						else $notice_date_start=change_date_format(trim(add_date($start_date ,-$to_add_days)),'','',1);
						 
						
						if($db_type==0) $notice_date_end=add_date($finish_date ,-$to_add_days);
						else $notice_date_end=change_date_format(trim(add_date($finish_date ,-$to_add_days)),'','',1);
					}
					
					
					
					if( $tna_process_list[$row['po_id']][$row_task['task_name']]=="") 
					{ 
						if ($mst_id==""){$mst_id=return_next_id( "id", "tna_process_mst");}else{$mst_id+=1;}
						if ($data_array_tna_process!=""){$data_array_tna_process .=",";}
						 
						
						if($tna_updated_date[$row['po_id']][$row_task['task_name']]['planstart'] =='0000-00-00') $tna_updated_date[$row['po_id']][$row_task['task_name']]['planstart'] ='';
						if($tna_updated_date[$row['po_id']][$row_task['task_name']]['planfinish'] =='0000-00-00') $tna_updated_date[$row['po_id']][$row_task['task_name']]['planfinish'] ='';
						
						if( $tna_updated_date[$row['po_id']][$row_task['task_name']]['planstart'] !='' ) $start_date=$tna_updated_date[$row['po_id']][$row_task['task_name']]['planstart'];
						if( $tna_updated_date[$row['po_id']][$row_task['task_name']]['planfinish'] !='' ) $finish_date=$tna_updated_date[$row['po_id']][$row_task['task_name']]['planfinish'];
						
						
						$plan_start_flag=$tna_updated_date[$row['po_id']][$row_task['task_name']]['planstartflag']*1;
						$plan_finish_flag=$tna_updated_date[$row['po_id']][$row_task['task_name']]['planfinishflag']*1;
						
						
						$data_array_tna_process .="('$mst_id','$row[template_id]','$row[job_no]','$row[po_id]','$row[booking_date]','$row[delivery_date]','1','$row_task[task_name]','$target_date','$start_date','$finish_date','$notice_date_start','$notice_date_end','$date','$row_task[sequence_no]',$plan_start_flag,$plan_finish_flag,1,0,2)";
						
						$insert_string[] ="('$mst_id','$row[template_id]','$row[job_no]','$row[po_id]','$row[booking_date]','$row[delivery_date]','1','$row_task[task_name]','$target_date','$start_date','$finish_date','$notice_date_start','$notice_date_end','$date','$row_task[sequence_no]',$plan_start_flag,$plan_finish_flag,1,0,2)";
						
					}
					else
					{ 	
					
					
						if ( ($tna_task_update_data[$row['po_id']][$row_task['task_name']]['min_start_date']=="0000-00-00" || $tna_task_update_data[$row['po_id']][$row_task['task_name']]['min_start_date']=="") && ($tna_task_update_data[$row['po_id']][$row_task['task_name']]['max_start_date']!="0000-00-00" || $tna_task_update_data[$row['po_id']][$row_task['task_name']]['max_start_date']!="" ) )
						{  
							$tna_task_update_data[$row['po_id']][$row_task['task_name']]['min_start_date']= $tna_task_update_data[$row['po_id']][$row_task['task_name']]['max_start_date'];
						}
						
						if ( $tna_task_update_data[$row['po_id']][$row_task['task_name']]['min_start_date']!="0000-00-00" || $tna_task_update_data[$row['po_id']][$row_task['task_name']]['min_start_date']!="" ){$start_date=$tna_task_update_data[$row['po_id']][$row_task['task_name']]['min_start_date'];}else{$start_date="0000-00-00";}
						
						if ( $tna_task_update_data[$row['po_id']][$row_task['task_name']]['max_start_date']!="0000-00-00" || $tna_task_update_data[$row['po_id']][$row_task['task_name']]['max_start_date']!="" ){$finish_date=$tna_task_update_data[$row['po_id']][$row_task['task_name']]['max_start_date'];}else{$finish_date="0000-00-00";}
						
						//if(!in_array($row_task['task_name'],$approval_array))
						
						if($approval_array[$row_task['task_name']]=='')
						{
							$compl_perc=get_percent($tna_task_update_data[$row['po_id']][$row_task['task_name']]['doneqnty'], $tna_task_update_data[$row['po_id']][$row_task['task_name']]['reqqnty']); 
							if($compl_perc<$row_task['completion_percent'])
							{
								$finish_date=$blank_date;
							}
						}
						else
						{
							if( $tna_task_update_data[$row['po_id']][$row_task['task_name']]['noofapproved']!=$tna_task_update_data[$row['po_id']][$row_task['task_name']]['noofval']) $finish_date=$blank_date; //"0000-00-00";
						}
						
	
	
						
						if($tna_updated_date[$row['po_id']][$row_task['task_name']]['start'] =='0000-00-00'){ $tna_updated_date[$row['po_id']][$row_task['task_name']]['start'] ='';}
						if($tna_updated_date[$row['po_id']][$row_task['task_name']]['finish'] =='0000-00-00'){ $tna_updated_date[$row['po_id']][$row_task['task_name']]['finish'] ='';}
						
						if( $tna_updated_date[$row['po_id']][$row_task['task_name']]['start'] !='' ){$start_date=$tna_updated_date[$row['po_id']][$row_task['task_name']]['start'];}
						if( $tna_updated_date[$row['po_id']][$row_task['task_name']]['finish'] !='' ){$finish_date=$tna_updated_date[$row['po_id']][$row_task['task_name']]['finish'];}
						
						
						$process_id_up_array[$tna_process_list[$row['po_id']][$row_task['task_name']]]=$tna_process_list[$row['po_id']][$row_task['task_name']];
						
						$data_array_tna_process_up[$tna_process_list[$row['po_id']][$row_task['task_name']]] =explode(",",("'".$start_date."','".$finish_date."'")); 
					}
				} // To Process Task List check
			}
			
			
			//print_r($data_array_tna_process_up);die;
		}
		
	
		//print_r($insert_string);die; 

		$con = connect();
		//oci_commit($con);
		if( $db_type==0 )
		{
			mysql_query( "BEGIN" );
		}
	
		if($db_type==0)
		{
			if( $data_array_tna_process!="" ) 
				$rID=sql_insert("tna_process_mst",$field_array_tna_process,$data_array_tna_process,1);
			if(count($process_id_up_array)>0)
				$rID_up=execute_query(bulk_update_sql_statement( "tna_process_mst", "id", $field_array_tna_process_up, $data_array_tna_process_up, $process_id_up_array ));
		
			mysql_query('COMMIT');
		
		}
		if($db_type==1 || $db_type==2 )
		{
			if( $data_array_tna_process!="" ) 
			{
				$tna_pro_array=array_chunk($insert_string,50);
				foreach($tna_pro_array as $dd=>$tna_pro_list)
				{
					$rID=sql_insert("tna_process_mst",$field_array_tna_process,implode(",",$tna_pro_list),1);
					oci_commit($con); 
				}
				
			}
			if(count($process_id_up_array)>0) 
			{
				$data_array_tna_up=array_chunk($data_array_tna_process_up,50,true);
				$id_up_array=array_chunk($process_id_up_array,50);
				$count=count($id_up_array);
				for ($i=0;$i<$count;$i++)
				{
					$rID_up=execute_query(bulk_update_sql_statement( "tna_process_mst", "id", $field_array_tna_process_up, $data_array_tna_up[$i], array_values($id_up_array[$i] )),1);
				}
				oci_commit($con);
			}
		
		}
		
		
		
		unset($insert_string);
		unset($process_id_up_array);
		unset($data_array_tna_process_up);
		unset($data_array_tna_process);
		unset($po_order_details);
	
		disconnect($con);
		
	} // Foreach Company level


 //print_r($insert_string);die;
   
	 
	
 //echo print_r($data_array_tna_process_up);
//.....................................................................................
	if($rID==1 || $rID_up==1){
		echo "0**Is insert:".$rID.",Is Update:".$rID_up."**".implode(", ",$template_missing_po);
	}
	else{
		echo "10**Is insert:".$rID.",Is Update:".$rID_up."**".implode(", ",$template_missing_po);
	}
	die;
	
}//end tna_process;




// Always treat the lowest template ... if not no process on that
function get_tna_template( $remain_days, $tna_template, $buyer ) 
{
	
	global $tna_template_buyer;   // print count($tna_template_buyer[$buyer]);die;
	if(count($tna_template_buyer[$buyer])>0)
	{ 
		$n=count($tna_template_buyer[$buyer]);
		for($i=0;$i<$n; $i++)
		{ 
			
			if($remain_days<$tna_template_buyer[$buyer][$i]['lead']) 
			{
				if( $i!=0 )
					return $tna_template_buyer[$buyer][$i-1]['id'];
				else
					return "0";
				 
			}
			else if( $remain_days==$tna_template_buyer[$buyer][$i]['lead'] ) 
			{
				return $tna_template_buyer[$buyer][$i]['id'];
			}
			else if($remain_days>$tna_template_buyer[$buyer][$i]['lead'] &&  $i==$n-1) 
			{
				return $tna_template_buyer[$buyer][$i]['id'];
			}
		}
	}
	else
	{
		 
		$n=count($tna_template); 
		for($i=0;$i<$n;$i++)
		{
			if( $remain_days<$tna_template[$i]['lead']) 
			{ 
				if( $i!=0 )
					return $tna_template[$i-1]['id'];
				else
					return "0";
			}
			else if($remain_days==$tna_template[$i]['lead']) 
			{
				return $tna_template[$i]['id'];
			}
			else if($remain_days>$tna_template[$i]['lead'] &&  $i==$n-1) 
			{ 
				return $tna_template[$i]['id'];
			}
			 
		}
	}
}


function get_percent($completed, $actual)
{
	return number_format((($completed*100)/$actual),0,'.','');
}





if ($action=="search_po_number")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$textile_tna_process_base=return_field_value("textile_tna_process_base"," variable_order_tracking"," company_name=".$company." and variable_list=62"); 
 	$caption=($textile_tna_process_base==2)?"Sales Order":"Booking";	
  
  
?>
     
<script>
	/*function ___js_set_value__(str, str1)
	{
		$("#selected_job").val(str+"__"+str1);
		//parent.emailwindow.hide(); 
	}*/


		var selected_id = new Array();
		var selected_name = new Array();
		 
		function check_all_data(totalRow){
			
			if(document.getElementById('check_all').checked){ 
				var returnFlag=0;
				for( var i = 1; i < totalRow; i++ ) {
					if(document.getElementById( 'tr_'+$('#po_'+i).val()).style.backgroundColor!='yellow'){
						js_set_value($('#po_'+i).val());
						returnFlag=1;
					}
				}
				if(returnFlag==1){return;}
			}
			else
			{
				var returnFlag=0;
				for( var i = 1; i < totalRow; i++ ) {
					if(document.getElementById( 'tr_'+$('#po_'+i).val()).style.backgroundColor=='yellow'){
						js_set_value($('#po_'+i).val());
						returnFlag=1;
					}
				}
				if(returnFlag==1){return;}
			}
			
			
			for( var i = 1; i < totalRow; i++ ) {
				js_set_value($('#po_'+i).val());
			}
			
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value(po_id) {
			po_id=po_id*1;
			if ( $('#tr_'+po_id).is(':visible') ) {
			
				var po_no=$('#tr_'+po_id).find("td p").eq(0).html();
				
				toggle( document.getElementById( 'tr_' + po_id ), '#E9F3FF' );
				
				if( jQuery.inArray(po_id, selected_id ) == -1 ) {
					selected_id.push(po_id);
					selected_name.push(po_no);
					
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == po_id) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 );
				}
				
				var id ='' 
				var name = '';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ',';
				}
				id = id.substr( 0, id.length - 1 );
				name = name.substr( 0, name.length - 1 );
				
				$('#txt_selected_id').val( id );
				$('#txt_selected_name').val( name );
			
			
				var totalRow =  $('#tbl_list_search tbody tr:visible').length-1;
				if(selected_id.length == totalRow){
					document.getElementById("check_all").checked = true;
				}
				else{
					document.getElementById("check_all").checked = false;
				}
			}
		
		}
     </script>
     
</head>
<body>
     
</head>

<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="100%" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table" align="center">
    	<tr>
        	<td align="center" width="100%">
            	<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                        <thead>
                         <th colspan="2"></th>
                        	<th colspan="2">
                              <?
                               echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" );
                              ?>
                            </th>
                          <th colspan="3"> </th>
                        </thead>
                    <thead>                	 
                        <th width="150">Company Name</th>
                        <th width="150">Buyer Name</th>
                        <th width="150">Sales Order</th>
                        <th width="150">Booking No</th>
                        <th colspan="2">Booking Date</th>
                        <th width="120" ></th>           
                    </thead>
        			<tr>
                    	<td> 
                        <input type="hidden" id="garments_nature" value="<? echo $garments_nature; ?>">
							<? 
								echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company,"load_drop_down( 'tna_process_textile_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
							?>
                    </td>
                   	<td id="buyer_td">
                     <? 
						echo create_drop_down( "cbo_buyer_name", 172, $blank_array,'', 1, "-- Select Buyer --");
					?>
                    </td>
                    <td><input name="txt_sales_order" id="txt_sales_order" class="text_boxes" style="width:130px"></td>
                    <td><input name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:130px"></td>
                    <td>
                    	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
					</td> 
                    <td>
					  	<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					</td> 
            		<td align="center">
                     <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_sales_order').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+<? echo $textile_tna_process_base;?>+'_'+document.getElementById('txt_booking_no').value, 'ponumber_search_list_view', 'search_div', 'tna_process_textile_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" /></td>
        		</tr>
             </table>
          </td>
        </tr>
        <tr>
            <td  align="center" height="40" valign="middle">
             <? 
				echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );		
				echo load_month_buttons();  
			?>
                <input type="hidden" id="txt_selected_id">
                <input type="hidden" id="txt_selected_name">
            </td>
        </tr>
    </table>    
    </form>
   </div>
   <div id="search_div"></div>
   
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	var buyer='<? echo $buyer; ?>';
	load_drop_down( 'tna_process_textile_controller', <? echo $company;?>, 'load_drop_down_buyer', 'buyer_td' );
	document.getElementById('cbo_buyer').value=buyer;
</script>
</html>
<?
exit();
}

if ($action=="ponumber_search_list_view")
{

 list($company,$buyer,$start_date,$end_date,$sales_order,$year,$surch_by,$process_base,$booking)=explode('_',$data);
 
 if($buyer==0 && $sales_order=='' && $booking=='' && str_replace("'","",$start_date)==''){
	 echo "<h2 style='color:#F00'>Please Select Date</h2>";exit();
 }
 if($company==0){echo"<h2 style='color:#d00'>Please select company</h2>";die;}



 $company_array=return_library_array( "select id, company_name from lib_company where is_deleted=0",'id','company_name');
 $buyer_array=return_library_array( "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90,80)) order by buy.buyer_name",'id','buyer_name');

 if($buyer!=0) $buyer_con="and a.buyer_id='$buyer'"; else $buyer_con="";
 if($surch_by==1)
 {
	 if($sales_order!="") $booking_no_con="and a.job_no='".trim($sales_order)."'"; else $booking_no_con="";
	 if($booking!="") $booking_no_con.=" and a.SALES_BOOKING_NO='".trim($booking)."'"; else $booking_no_con.="";
 }
 else if($surch_by==2)
 {
	 if($sales_order!="") $booking_no_con="and a.job_no like '".trim($sales_order)."%'"; else $booking_no_con="";
	 if($booking!="") $booking_no_con.=" and a.SALES_BOOKING_NO like '".trim($booking)."%'"; else $booking_no_con.="";
 }
 else if($surch_by==3)
 {
	 if($sales_order!="") $booking_no_con="and a.job_no like '%".trim($sales_order)."'"; else $booking_no_con="";
	 if($booking!="") $booking_no_con.=" and a.SALES_BOOKING_NO like '%".trim($booking)."'"; else $booking_no_con.="";
 }
 else if($surch_by==4 || $surch_by==0)
 {  
	 if($sales_order!=""){$booking_no_con="and a.job_no like '%".trim($sales_order)."%'";} else {$booking_no_con="";}
	 if($booking!=""){ $booking_no_con.=" and a.SALES_BOOKING_NO like '%".trim($booking)."%'";} else {$booking_no_con.="";}
 }
 
 
 
 
 
	$start_date=str_replace("'","",$start_date);
	$end_date=str_replace("'","",$end_date); 
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
		$date_cond  = " and a.booking_date between '".change_date_format($start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'";
		}
		
		if($db_type==2)
		{
		$date_cond  = " and a.booking_date between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-",1)."'";
		}
	}
	else
	{
	$date_cond  = "";	
	}
	
	
	
 ?>
        <table width="950" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
            <thead>
                <th width="40" height="34">SL</th>
                <th width="130">Company Name</th>
                <th width="130">Buyer/Unit</th>
                <th width="130">Sales Order No</th>
                <th width="130">Booking No</th>
                <th width="110">Booking Date</th>
                <th width="150">Delivery Date</th>
                <th>Lead Time</th>
            </thead>
        </table>
        <div style="width:950px; max-height:210px; overflow-y:scroll"> 
        <table width="930" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_search">
		<?
			if($db_type==0) $lead_time="DATEDIFF(a.delivery_date,a.booking_approval_date) as  date_diff";
			if($db_type==2) $lead_time="(a.delivery_date-a.booking_approval_date) as  date_diff";
			
			
			if($process_base==1){//booking;
				$sql = "select a.within_group,a.id,a.company_id,a.booking_date,a.delivery_date,a.booking_no,a.buyer_id,$lead_time
				from wo_booking_mst a
				WHERE a.is_deleted = 0 and a.status_active=1 and company_id=".$company." $buyer_con $booking_no_con $date_cond
				ORDER BY a.delivery_date asc";
			}
			else
			{
				$sql = "select a.within_group,a.id,a.company_id,a.booking_date,a.delivery_date,a.job_no,a.SALES_BOOKING_NO as booking_no,a.buyer_id,$lead_time
				from fabric_sales_order_mst a
				WHERE a.is_deleted = 0 and a.status_active=1 and company_id=".$company." $buyer_con $booking_no_con $date_cond
				ORDER BY a.delivery_date asc";
			}
			
			// echo $sql;
			
			$sql_result=sql_select($sql);
			$i=1;
			foreach($sql_result as $row){
			$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
			if($row[csf("within_group")]==1){$buyer_name=$company_array[$row[csf("buyer_id")]];}
			else{$buyer_name=$buyer_array[$row[csf("buyer_id")]];}
			?>
              <tr id="tr_<? echo $row[csf("id")]; ?>" bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf("id")];  ?>')" style="cursor:pointer;">
                   <td width="40" align="center" ><? echo $i; ?><input type="hidden" id="po_<? echo $i; ?>" value="<? echo $row[csf("id")]; ?>" /></td>
                   <td width="130" align="center"><? echo $company_array[$row[csf("company_id")]]; ?></td>
                   <td width="130" align="center"><? echo $buyer_name; ?></td>
                   <td width="130" align="center" id="txt_style_reff" ><p><? echo $row[csf('job_no')]; ?></p></td>
                   <td width="130" align="center" id="txt_style_reff" ><p><? echo $row[csf('booking_no')]; ?></p></td>
                   <td width="110" align="center" id="txt_style_reff" ><? echo change_date_format($row[csf("booking_date")]); ?></td>
                   <td width="150" align="center" id="txt_style_reff" ><? echo change_date_format($row[csf("delivery_date")]); ?></td>
                   <td  align="center" id="txt_style_reff" ><? echo $row[csf("date_diff")]; ?></td>
              </tr>
    	<?
            $i++;   
        }
        ?>
        </table>
        
        
    </div>
        <table width="950">
        	<td><input onClick="check_all_data(<? echo $i;?>)" type="checkbox" id="check_all"> All Select/Unselect</td>
        	<td align="center"><input type="button" class="formbutton" value="Close" onClick="parent.emailwindow.hide();" style="width:100px;" />
            </td>
        </table>
    
    
    
<?       
exit();
}
?>
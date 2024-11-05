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

include('../../../includes/common.php');

if( $action=="load_drop_down_buyer" )
{	
	echo create_drop_down( "cbo_buyer", 170, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );  
	die; 	 
}

if( $cbo_company<1 ) $company_array=return_library_array( "select id,id from lib_company",'id','id' );
else $company_array[$cbo_company]=$cbo_company;
 
if ( $action=="tna_process" )
{
	
	$gross_level=1;
	
	$tba_color_id=return_field_value("id","lib_color"," color_name ='TBA'");

	foreach( $company_array as $cbo_company )
	{
		$tna_process_type=return_field_value("tna_process_type"," variable_order_tracking"," company_name=".$cbo_company." and variable_list=31"); 
		$tna_process_start_date=return_field_value("tna_process_start_date"," variable_order_tracking"," company_name=".$cbo_company." and variable_list=43"); 
		$textile_tna_process_base=return_field_value("textile_tna_process_base"," variable_order_tracking"," company_name=".$cbo_company." and variable_list=62"); 
		
		 
		if( $tna_process_type==2 )
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
		else if($tna_process_type==1)
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
			$sql_task = "SELECT a.id,task_template_id,lead_time,material_source,total_task,tna_task_id,deadline,execution_days,notice_before,sequence_no,for_specific,b.task_catagory,b.task_name,a.task_type FROM  tna_task_template_details a, lib_tna_task b WHERE   a.tna_task_id=b.task_name and a.is_deleted=0 and a.status_active=1 and a.task_type=2 and b.is_deleted=0 and b.status_active=1 and a.company_id in(".implode(',',$company_array).",0) order by for_specific,lead_time";
			$result = sql_select( $sql_task ) ;
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
				if($template[$row[csf("task_template_id")]]=='')
				{
					$template[$row[csf("task_template_id")]]=$row[csf("task_template_id")];
					//if($row[csf("for_specific")]==3) $row[csf("for_specific")]=0;
					if ( $row[csf("for_specific")]==0 )
					{
						$tna_template[$m]['lead']=$row[csf("lead_time")];
						$tna_template[$m]['id']=$row[csf("task_template_id")];
						$i++;
						$m++;
					}
					else
					{
						if(!in_array($row[csf('for_specific')],$tna_template_spc)) { $j=0; $tna_template_spc[]=$row[csf("for_specific")]; }
						$tna_template_buyer[$row[csf('for_specific')]][$j]['lead']=$row[csf('lead_time')];
						$tna_template_buyer[$row[csf('for_specific')]][$j]['id']=$row[csf('task_template_id')];
						$j++;
					}
					$k++;
				}
				 
				$template_wise_task[$row[csf("task_template_id")]][$row[csf("task_name")]]['deadline']= $row[csf("deadline")];
				$template_wise_task[$row[csf("task_template_id")]][$row[csf("task_name")]]['execution_days']= $row[csf("execution_days")];
				$template_wise_task[$row[csf("task_template_id")]][$row[csf("task_name")]]['notice_before']=$row[csf("notice_before")];
				$template_wise_task[$row[csf("task_template_id")]][$row[csf("task_name")]]['sequence_no']=$row[csf("sequence_no")];
				$template_wise_task[$row[csf("task_template_id")]][$row[csf("task_name")]]['for_specific']=$row[csf("for_specific")];
				$template_wise_task[$row[csf("task_template_id")]][$row[csf("task_name")]]['task_name']=$row[csf("task_name")];
				$template_wise_task[$row[csf("task_template_id")]][$row[csf("task_name")]]['completion_percent']=$tna_completion[$row[csf("task_name")]];
				
				 $g++;
				 $i++;
			}
			
		 
		// print_r($tna_template_buyer);die;	
		 
		 
		$sql = "SELECT company_name,tna_integrated FROM  variable_order_tracking WHERE  company_name=".$cbo_company." and status_active =1 and is_deleted = 0 and variable_list=14";
		$result = sql_select( $sql );
		$variable_settings = array();
		foreach( $result as $row ) 
		{		
			$variable_settings[$row[csf('company_name')]] = $row[csf('tna_integrated')];
		}
		if( $db_type==0 ) $blank_date="0000-00-00"; else $blank_date=""; 
		// Reprocess Check
		
		
		
		if (trim($txt_booking_no_id)==""){
			if( $is_delete==1 )
			{
				if($cbo_buyer!=0){$buyerCon=" and buyer_id='$cbo_buyer'";}
				
				$job_array=return_library_array( "select id, job_no from fabric_sales_order_mst where company_id=$cbo_company  $buyerCon ",'id','job_no');
				$job_str=implode("','",$job_array);
				
				$con = connect();
				$p=1;
				$job_no_list_arr=array_chunk($job_array,999);
				foreach($job_no_list_arr as $job_no_process)
				{
					if($p==1){$sql_con .=" and (job_no in('".implode("','",$job_no_process)."')";} 
					else{$sql_con .=" or job_no in('".implode("','",$job_no_process)."')";}
					$p++;
				}
				$sql_con .=")";
				
				$rid=execute_query("delete FROM tna_process_mst WHERE task_type=2 $sql_con ",1);
			
			if( $db_type==2 ) oci_commit($con); 
			}

		}
		else 
		{
			if( $is_delete==1 )
			{
				$con = connect();
				$rid=execute_query("delete FROM tna_process_mst WHERE task_type=2 and job_no in ('".implode("','",explode(',',$txt_booking_no))."')",1);
				if( $db_type==2 ) oci_commit($con); 
			}
			
		}
		
		 
		 
		 
		//$textile_tna_process_base=2;
		 
		if($textile_tna_process_base==2){//Sales Order Base
			
			//if($txt_booking_no_id!=''){$booking_con="and a.job_no='$txt_booking_no'";}
			if($txt_booking_no_id!=''){$booking_con="and a.id in($txt_booking_no_id)";}
			if($cbo_buyer!=0){$buyer_cond="and a.buyer_id='$cbo_buyer'";}
			
			/*$sql = "select a.id as booking_id,a.delivery_date,a.booking_approval_date as booking_date,a.job_no as BOOKING_NO,a.buyer_id,b.color_type_id ,
			
			b.FABRIC_DESC,b.GSM_WEIGHT,b.DIA,b.COLOR_ID,
			sum(b.finish_qty) as fin_fab_qnty,sum(b.grey_qty) as grey_fab_qnty
            from fabric_sales_order_mst a,fabric_sales_order_dtls b  
			WHERE a.job_no=b.job_no_mst and b.is_deleted = 0 and b.status_active=1 and a.is_deleted = 0 and a.status_active=1 and a.delivery_date >'$tna_process_start_date' and company_id=".$cbo_company." $buyer_cond $booking_con
			group by a.id,a.booking_approval_date,a.delivery_date,a.job_no,a.buyer_id,b.color_type_id,b.FABRIC_DESC,b.GSM_WEIGHT,b.DIA,b.COLOR_ID ORDER BY a.delivery_date asc";*/
			
			
$sql = "select a.id as booking_id,a.delivery_date,a.booking_approval_date as booking_date,a.job_no as BOOKING_NO,a.buyer_id,
			sum(b.finish_qty) as fin_fab_qnty,sum(b.grey_qty) as grey_fab_qnty
            from fabric_sales_order_mst a,fabric_sales_order_dtls b  
			WHERE a.job_no=b.job_no_mst and b.is_deleted = 0 and b.status_active=1 and a.is_deleted = 0 and a.status_active=1 and a.delivery_date >'$tna_process_start_date' and company_id=".$cbo_company." $buyer_cond $booking_con
			group by a.id,a.booking_approval_date,a.delivery_date,a.job_no,a.buyer_id ORDER BY a.delivery_date asc";			
			
			
			     //echo $sql;die;
			
		}
		else{//Booking Base
		
			/*$sql = "select a.id as booking_id,a.booking_date,a.delivery_date,a.booking_no,a.buyer_id  ,sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
            from wo_booking_mst a,wo_booking_dtls b  
			WHERE b.is_deleted = 0 and b.status_active=1 and a.is_deleted = 0 and a.status_active=1 and a.booking_no=b.booking_no and a.delivery_date >'$tna_process_start_date' and company_id=".$cbo_company." $buyer_cond  
			and a.booking_no='$txt_booking_no'
			group by a.id,a.booking_date,a.delivery_date,a.booking_no,a.buyer_id ORDER BY a.delivery_date asc";*/
		}
		 
		  //echo $sql;die;
		$data_array=sql_select($sql);
		
		//print_r($data_array);die;
		
		
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
			//$row[csf("booking_no")]=$row[BOOKING_NO].'**'.$row[FABRIC_DESC].'**'.$row[GSM_WEIGHT].'**'.$row[DIA].'**'.$row[COLOR_ID];
			
			$row[csf("booking_date")]=date('d-M-y',strtotime($row[csf("booking_date")]));
			$remain_days=datediff( "d", date("Y-m-d",strtotime($row[csf("booking_date")])), date("Y-m-d",strtotime($row[csf("delivery_date")])) );
			
			
			if ( $tna_process_type==1 )
			{ 
				$template_id=get_tna_template($remain_days,$tna_template,$row[csf("buyer_id")]); 
			}
			else
			{
				
				
				$template_id=$remain_days-1; 
				
				if($tna_task_percent_buyer_wise[$row[csf('buyer_id')]]=="")
				{
				 
					foreach($tna_task_percent as $id=>$data)
					{
						$deadline=floor($template_id*$data[start_percent]/100);
						$exe=floor($template_id*$data[end_percent]/100);
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
						$deadline=floor($template_id*$data[start_percent]/100);
						$exe=floor($template_id*$data[end_percent]/100);
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
			
				$booking_template[$row[csf("booking_no")]]=$template_id; 
				$booking_details[$row[csf("booking_no")]]['booking_date']=$row[csf("booking_date")];
				$booking_details[$row[csf("booking_no")]]['delivery_date']=$row[csf("delivery_date")];
				$booking_details[$row[csf("booking_no")]]['booking_no']=$row[csf("booking_no")];
				$booking_details[$row[csf("booking_no")]]['booking_qty']+=$row[csf("grey_fab_qnty")];
				$booking_details[$row[csf("booking_no")]]['template_id']=$template_id;
				$booking_details[$row[csf("booking_no")]]['booking_id']=$row[csf("booking_id")];
				$booking_id_arr[$row[csf("booking_id")]]=$row[csf("booking_id")];
			 
			 
									
			if ( $template_id=="" || $template_id==0 )
			{
				$template_missing[]=$row[csf("booking_no")];
				//This array for missiong PO Auto mail send..............
				$template_missing_mail_data_arr[]=array(
					'booking_no'	=> $row[csf("booking_no")],
					'buyer_id'		=> $row[csf("buyer_id")],
					'booking_date'	=> $row[csf("booking_date")],
					'delivery_date'	=> $row[csf("delivery_date")]
				
				);
			} 
			else
			{
								
			//color type wise req qty...................................start;
			
			
			if( $gross_level==1 ){
				$to_process_task[$row[csf("booking_no")]][200]=200;
				$tna_task_update_data[$row[csf("booking_no")]][200]['reqqnty']+=$row[csf("grey_fab_qnty")];
			
			
				//2,3,4,6,32,33 Y/D; Note: reqqnty of Y/D rcv FSOE  grey_fab_qnty mandatory
				if(($row[csf("color_type_id")]==2) || ($row[csf("color_type_id")]==3) || ($row[csf("color_type_id")]==4) || ($row[csf("color_type_id")]==6) || ($row[csf("color_type_id")]==32) || ($row[csf("color_type_id")]==33)){
					$tna_task_update_data[$row[csf("booking_no")]][52]['reqqnty']+=$row[csf("grey_fab_qnty")];
				}
			
			
			
			}
			else
			{
				//2,3,4,6,32,33 Y/D
				if(($row[csf("color_type_id")]==2) || ($row[csf("color_type_id")]==3) || ($row[csf("color_type_id")]==4) || ($row[csf("color_type_id")]==6) || ($row[csf("color_type_id")]==32) || ($row[csf("color_type_id")]==33)){
					$to_process_task[$row[csf("booking_no")]][211]=211;
					$tna_task_update_data[$row[csf("booking_no")]][211]['reqqnty']+=$row[csf("grey_fab_qnty")];
					$tna_task_update_data[$row[csf("booking_no")]][52]['reqqnty']+=$row[csf("grey_fab_qnty")];
				}
				//5,7 AOP
				else if(($row[csf("color_type_id")]==5) || ($row[csf("color_type_id")]==7)){
					$to_process_task[$row[csf("booking_no")]][210]=210;
					$tna_task_update_data[$row[csf("booking_no")]][210]['reqqnty']+=$row[csf("grey_fab_qnty")];
				}//solid..
				else if(($row[csf("color_type_id")]==1) || ($row[csf("color_type_id")]==20) || ($row[csf("color_type_id")]==25) || ($row[csf("color_type_id")]==26) || ($row[csf("color_type_id")]==27) || ($row[csf("color_type_id")]==28) || ($row[csf("color_type_id")]==29) || ($row[csf("color_type_id")]==30) || ($row[csf("color_type_id")]==31) || ($row[csf("color_type_id")]==''))
				{
					$to_process_task[$row[csf("booking_no")]][200]=200;
					$tna_task_update_data[$row[csf("booking_no")]][200]['reqqnty']+=$row[csf("grey_fab_qnty")];
				
				}
			}
			
			
				
			//color type wise req qty.....................................end;	
				
				
				
				$tna_task_update_data[$row[csf("booking_no")]][31]['max_start_date']=$row[csf("booking_date")];
				$tna_task_update_data[$row[csf("booking_no")]][31]['min_start_date']=$row[csf("booking_date")]; 
				$tna_task_update_data[$row[csf("booking_no")]][31]['doneqnty']+=$row[csf("grey_fab_qnty")];
				
				
				
				$to_process_task[$row[csf("booking_no")]][10]=10;
				
				$tna_task_update_data[$row[csf("booking_no")]][31]['reqqnty']+=$row[csf("grey_fab_qnty")];
				$to_process_task[$row[csf("booking_no")]][31]=31;
				
				$to_process_task[$row[csf("booking_no")]][33]=33;
				$to_process_task[$row[csf("booking_no")]][40]=40;
				$to_process_task[$row[csf("booking_no")]][45]=45;
				$to_process_task[$row[csf("booking_no")]][46]=46;
				$to_process_task[$row[csf("booking_no")]][47]=47;
				$to_process_task[$row[csf("booking_no")]][48]=48;
				
				$tna_task_update_data[$row[csf("booking_no")]][50]['reqqnty']+=$row[csf("grey_fab_qnty")];
				$to_process_task[$row[csf("booking_no")]][50]=50;
				
				$to_process_task[$row[csf("booking_no")]][51]=51;
				//$tna_task_update_data[$row[csf("booking_no")]][51]['reqqnty']+=$row[csf("fin_fab_qnty")];
				
				
				
				$to_process_task[$row[csf("booking_no")]][52]=52;
				
				$tna_task_update_data[$row[csf("booking_no")]][60]['reqqnty']+=$row[csf("grey_fab_qnty")];
				$to_process_task[$row[csf("booking_no")]][60]=60;
				
				$tna_task_update_data[$row[csf("booking_no")]][61]['reqqnty']+=$row[csf("grey_fab_qnty")];
				$to_process_task[$row[csf("booking_no")]][61]=61;
				
				
				
				$to_process_task[$row[csf("booking_no")]][62]=62;
				$to_process_task[$row[csf("booking_no")]][63]=63;
				
				$tna_task_update_data[$row[csf("booking_no")]][64]['reqqnty']+=$row[csf("fin_fab_qnty")];
				$to_process_task[$row[csf("booking_no")]][64]=64;
				
				
				
				$to_process_task[$row[csf("booking_no")]][72]=72;
				$tna_task_update_data[$row[csf("booking_no")]][72]['reqqnty']+=$row[csf("grey_fab_qnty")];
				
				$tna_task_update_data[$row[csf("booking_no")]][73]['reqqnty']+=$row[csf("fin_fab_qnty")];
				$to_process_task[$row[csf("booking_no")]][73]=73;
				
				$to_process_task[$row[csf("booking_no")]][74]=74;
				$tna_task_update_data[$row[csf("booking_no")]][74]['reqqnty']+=$row[csf("fin_fab_qnty")];
				
				$to_process_task[$row[csf("booking_no")]][80]=80;
				$to_process_task[$row[csf("booking_no")]][167]=167;
				
				
				$to_process_task[$row[csf("booking_no")]][199]=199;
				$tna_task_update_data[$row[csf("booking_no")]][199]['max_start_date']=$row[csf("booking_date")];
				$tna_task_update_data[$row[csf("booking_no")]][199]['min_start_date']=$row[csf("booking_date")]; 
				$tna_task_update_data[$row[csf("booking_no")]][199]['doneqnty']+=$row[csf("fin_fab_qnty")];
				

				$to_process_task[$row[csf("booking_no")]][201]=201;
				$tna_task_update_data[$row[csf("booking_no")]][201]['reqqnty']+=$row[csf("grey_fab_qnty")];
				
				$to_process_task[$row[csf("booking_no")]][202]=202;
				$tna_task_update_data[$row[csf("booking_no")]][202]['reqqnty']+=$row[csf("grey_fab_qnty")];
				
				
				$to_process_task[$row[csf("booking_no")]][203]=203;
				$tna_task_update_data[$row[csf("booking_no")]][203]['reqqnty']+=$row[csf("grey_fab_qnty")];
				
				$to_process_task[$row[csf("booking_no")]][204]=204;
				$tna_task_update_data[$row[csf("booking_no")]][204]['reqqnty']+=$row[csf("grey_fab_qnty")];
				
				$to_process_task[$row[csf("booking_no")]][205]=205;
				$tna_task_update_data[$row[csf("booking_no")]][205]['reqqnty']+=$row[csf("grey_fab_qnty")];
				
				$to_process_task[$row[csf("booking_no")]][206]=206;
				$to_process_task[$row[csf("booking_no")]][207]=207;
				$tna_task_update_data[$row[csf("booking_no")]][207]['reqqnty']+=$row[csf("fin_fab_qnty")];
				
				$to_process_task[$row[csf("booking_no")]][208]=208;
				$to_process_task[$row[csf("booking_no")]][209]=209;
				
				
				$to_process_task[$row[csf("booking_no")]][212]=212;
				$tna_task_update_data[$row[csf("booking_no")]][212]['reqqnty']+=$row[csf("grey_fab_qnty")];
				
				
				$to_process_task[$row[csf("booking_no")]][213]=213;
				$to_process_task[$row[csf("booking_no")]][214]=214;
				$to_process_task[$row[csf("booking_no")]][215]=215;
				$to_process_task[$row[csf("booking_no")]][216]=216;
				$to_process_task[$row[csf("booking_no")]][217]=217;
				$to_process_task[$row[csf("booking_no")]][218]=218;
				$to_process_task[$row[csf("booking_no")]][219]=219;
				
				$to_process_task[$row[csf("booking_no")]][239]=239;
				$tna_task_update_data[$row[csf("booking_no")]][239]['reqqnty']+=$row[csf("fin_fab_qnty")];				
				
				
				
				
				
			}
		}

	$job_no_list_arr=array_chunk($booking_id_arr,999);
	$po_id_str=implode(',',$booking_id_arr);

//Yarn Issue...................................................................................;	
		  $sql="select  b.entry_form,c.job_no booking_no, min(a.transaction_date) as min_date, max(a.transaction_date) as max_date, sum(b.quantity) qty, sum(b.returnable_qnty) ret_qty from inv_transaction a,order_wise_pro_details b,fabric_sales_order_mst c  where c.id=b.po_breakdown_id and c.company_id=$cbo_company and  b.trans_id=a.id  and a.transaction_type in(1,2)  and a.item_category in(1,13) and b.entry_form in(3,58) and b.trans_type in(1,2) and a.status_active=1 and b.status_active=1 and c.status_active=1 ";
		  //echo $sql;die;
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1){$sql .=" and (b.po_breakdown_id in(".implode(',',$job_no_process).")";}
				else{$sql .=" or b.po_breakdown_id in(".implode(',',$job_no_process).")";}
				$p++;
			}
			$sql .=")";
			$sql .=" group by b.entry_form,c.job_no";
			
			//echo $sql;die;
			
		$planning_data_array=sql_select($sql);
		foreach($planning_data_array as $row)
		{
			//$tna_task_update_data[$row[csf("booking_no")]][50]['reqqnty']=$row[csf("qty")];
			if($row[csf("entry_form")]==3){
				$tna_task_update_data[$row[csf("booking_no")]][50]['max_start_date']=$row[csf("max_date")];
				$tna_task_update_data[$row[csf("booking_no")]][50]['min_start_date']=$row[csf("min_date")]; 
				$tna_task_update_data[$row[csf("booking_no")]][50]['doneqnty']=$row[csf("qty")];
			}
			else if($row[csf("entry_form")]==58){
				$tna_task_update_data[$row[csf("booking_no")]][72]['max_start_date']=$row[csf("max_date")];
				$tna_task_update_data[$row[csf("booking_no")]][72]['min_start_date']=$row[csf("min_date")]; 
				$tna_task_update_data[$row[csf("booking_no")]][72]['doneqnty']=$row[csf("qty")];
			}
		}


//Kniting Production...................................................................................start;	
		
		  
	$sql="select d.color_type_id,c.job_no booking_no,min(a.receive_date) min_date,max(a.receive_date) max_date, (b.qnty) qty from inv_receive_master a ,pro_roll_details b ,fabric_sales_order_mst c ,fabric_sales_order_dtls d
	where a.id=b.mst_id and b.po_breakdown_id=c.id and a.receive_basis=2 and a.entry_form=2 and b.entry_form=2 and a.item_category=13  and a.status_active=1   and d.status_active=1 and b.status_active=1 and c.status_active=1 and a.knitting_company=$cbo_company
	  and c.id=d.mst_id"; 
  
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1){$sql .=" and (b.po_breakdown_id in(".implode(',',$job_no_process).")";}
				else{$sql .=" or b.po_breakdown_id in(".implode(',',$job_no_process).")";}
				$p++;
			}
			$sql .=")";
			$sql .=" group by  c.job_no,d.color_type_id,b.qnty";		  
		  
			  
			  
		$knit_pro_data_array=sql_select($sql);
		foreach($knit_pro_data_array as $row)
		{
		
			if( $gross_level==1 ){
				if($tna_task_update_data[$row[csf("booking_no")]][212]['max_start_date']==''){
					$tna_task_update_data[$row[csf("booking_no")]][212]['max_start_date']=$row[csf("max_date")];
					$tna_task_update_data[$row[csf("booking_no")]][212]['min_start_date']=$row[csf("min_date")];
				}
				$tna_task_update_data[$row[csf("booking_no")]][212]['doneqnty']+=$row[csf("qty")];
				if(strtotime($row[csf("max_date")]) > strtotime($tna_task_update_data[$row[csf("booking_no")]][212]['max_start_date'])){$tna_task_update_data[$row[csf("booking_no")]][212]['max_start_date']=$row[csf("max_date")];}
				
				if(strtotime($row[csf("min_date")]) < strtotime($tna_task_update_data[$row[csf("booking_no")]][212]['min_start_date'])){$tna_task_update_data[$row[csf("booking_no")]][212]['min_start_date']=$row[csf("min_date")];}
				
			}
			else
			{
			
				if(($row[csf("color_type_id")]==2) || ($row[csf("color_type_id")]==3) || ($row[csf("color_type_id")]==4) || ($row[csf("color_type_id")]==6) || ($row[csf("color_type_id")]==32) || ($row[csf("color_type_id")]==33)){
	
				}
				//5,7 AOP
				else if(($row[csf("color_type_id")]==5) || ($row[csf("color_type_id")]==7)){
				}//solid..
				elseif(($row[csf("color_type_id")]==1) || ($row[csf("color_type_id")]==20) || ($row[csf("color_type_id")]==25) || ($row[csf("color_type_id")]==26) || ($row[csf("color_type_id")]==27) || ($row[csf("color_type_id")]==28) || ($row[csf("color_type_id")]==29) || ($row[csf("color_type_id")]==30) || ($row[csf("color_type_id")]==31) || ($row[csf("color_type_id")]==''))
				{
					if($tna_task_update_data[$row[csf("booking_no")]][212]['max_start_date']==''){
					$tna_task_update_data[$row[csf("booking_no")]][212]['max_start_date']=$row[csf("max_date")];
					$tna_task_update_data[$row[csf("booking_no")]][212]['min_start_date']=$row[csf("min_date")];
					}
					 
					$tna_task_update_data[$row[csf("booking_no")]][212]['doneqnty']+=$row[csf("qty")];
					
					if(strtotime($row[csf("max_date")]) > strtotime($tna_task_update_data[$row[csf("booking_no")]][212]['max_start_date'])){$tna_task_update_data[$row[csf("booking_no")]][212]['max_start_date']=$row[csf("max_date")];}
					
					if(strtotime($row[csf("min_date")]) < strtotime($tna_task_update_data[$row[csf("booking_no")]][212]['min_start_date'])){$tna_task_update_data[$row[csf("booking_no")]][212]['min_start_date']=$row[csf("min_date")];}
		/*			$tna_task_update_data[$row[csf("booking_no")]][213]['max_start_date']=$row[csf("max_date")];
					$tna_task_update_data[$row[csf("booking_no")]][213]['min_start_date']=$row[csf("min_date")]; 
					$tna_task_update_data[$row[csf("booking_no")]][213]['doneqnty']+=$row[csf("qty")];*/
				}
			}
		
		
		}
		unset($knit_pro_data_array);
		
		

//Kniting Production...................................................................................end;	



//Planning data........................................................................................start;
			$job_no_list_arr=array_chunk($booking_id_arr,999);
			
			$sql = "select a.job_no, min(a.booking_date) mindate, max(a.delivery_date) maxdate,  sum(b.finish_qty) finish_qty, sum(b.grey_qty) grey_qty 
from fabric_sales_order_mst a,fabric_sales_order_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.company_id=".$cbo_company."  and a.within_group=1 
";
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1){$sql .=" and (a.id in(".implode(',',$job_no_process).")";}
				else{$sql .=" or b.id in(".implode(',',$job_no_process).")";}
				$p++;
			}
			$sql .=")";
			$sql .=" group by a.job_no order by a.booking_date";
			
		//$planning_data_array=sql_select($sql);
		foreach($planning_data_array as $row)
		{
			//$tna_task_update_data[$row[csf("booking_no")]][31]['reqqnty']+=$row[csf("grey_fab_qnty")];
			//$to_process_task[$row[csf("booking_no")]][31]=31;
			
			//$tna_task_update_data[$row[csf("booking_no")]][50]['reqqnty']+=$row[csf("fin_fab_qnty")];
			//$to_process_task[$row[csf("booking_no")]][50]=50;
		}

//Planning data........................................................................................end;


//Knite Plan data........................................................................................start;
			$job_no_list_arr=array_chunk($booking_id_arr,999);
			
			$sql = "select a.job_no ,c.color_type_id, min(b.program_date) min_date, max(b.program_date) max_date,  sum(b.program_qnty) qty
			from 
			fabric_sales_order_mst a,
			ppl_planning_info_entry_dtls b, 
			ppl_planning_entry_plan_dtls c 
			where a.id=c.po_id and b.id=c.dtls_id and a.company_id=$cbo_company ";
			
			
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1){$sql .=" and (a.id in(".implode(',',$job_no_process).")";}
				else{$sql .=" or a.id in(".implode(',',$job_no_process).")";}
				$p++;
			}
			$sql .=")";
			$sql .=" group by a.job_no,c.color_type_id order by a.job_no";
			
		//echo $sql;die;
		
		$kniting_plan_data_array=sql_select($sql);
		foreach($kniting_plan_data_array as $row)
		{
			
			if( $gross_level==1 ){
				$tna_task_update_data[$row[csf("job_no")]][200]['max_start_date']=$row[csf("max_date")];
				$tna_task_update_data[$row[csf("job_no")]][200]['min_start_date']=$row[csf("min_date")]; 
				$tna_task_update_data[$row[csf("job_no")]][200]['doneqnty']+=$row[csf("qty")];
			}
			else
			{
				//2,3,4,6,32,33 Y/D
				if(($row[csf("color_type_id")]==2) || ($row[csf("color_type_id")]==3) || ($row[csf("color_type_id")]==4) || ($row[csf("color_type_id")]==6) || ($row[csf("color_type_id")]==32) || ($row[csf("color_type_id")]==33)){
					$tna_task_update_data[$row[csf("job_no")]][211]['max_start_date']=$row[csf("max_date")];
					$tna_task_update_data[$row[csf("job_no")]][211]['min_start_date']=$row[csf("min_date")]; 
					$tna_task_update_data[$row[csf("job_no")]][211]['doneqnty']+=$row[csf("qty")];
				}
				//5,7 AOP
				else if(($row[csf("color_type_id")]==5) || ($row[csf("color_type_id")]==7)){
					$tna_task_update_data[$row[csf("job_no")]][210]['max_start_date']=$row[csf("max_date")];
					$tna_task_update_data[$row[csf("job_no")]][210]['min_start_date']=$row[csf("min_date")]; 
					$tna_task_update_data[$row[csf("job_no")]][210]['doneqnty']+=$row[csf("qty")];
				}//solid..
				elseif(($row[csf("color_type_id")]==1) || ($row[csf("color_type_id")]==20) || ($row[csf("color_type_id")]==25) || ($row[csf("color_type_id")]==26) || ($row[csf("color_type_id")]==27) || ($row[csf("color_type_id")]==28) || ($row[csf("color_type_id")]==29) || ($row[csf("color_type_id")]==30) || ($row[csf("color_type_id")]==31) || ($row[csf("color_type_id")]==''))
				{
					$tna_task_update_data[$row[csf("job_no")]][200]['max_start_date']=$row[csf("max_date")];
					$tna_task_update_data[$row[csf("job_no")]][200]['min_start_date']=$row[csf("min_date")]; 
					$tna_task_update_data[$row[csf("job_no")]][200]['doneqnty']+=$row[csf("qty")];
				
				}
			}
			
			
			
				
		}
		
		
		
		unset($kniting_plan_data_array);

//Knite Plan data........................................................................................end;



//Batch Creation...................................................................................start;	
		  $sql="select a.sales_order_no,max(a.batch_date) max_date, min(a.batch_date) min_date, sum(b.batch_qnty) qty  from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.is_sales=1  and a.status_active=1 and b.status_active=1 and a.working_company_id=$cbo_company";
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1){$sql .=" and (a.sales_order_id in(".implode(',',$job_no_process).")";}
				else{$sql .=" or a.sales_order_id in(".implode(',',$job_no_process).")";}
				$p++;
			}
			$sql .=")";
			$sql .=" group by a.sales_order_no";
			 //echo $sql;die;
			
		$batchy_data_array=sql_select($sql);
		foreach($batchy_data_array as $row)
		{
			//$tna_task_update_data[$row[csf("booking_no")]][50]['reqqnty']=$row[csf("qty")];
			$tna_task_update_data[$row[csf("sales_order_no")]][205]['max_start_date']=$row[csf("max_date")];
			$tna_task_update_data[$row[csf("sales_order_no")]][205]['min_start_date']=$row[csf("min_date")]; 
			$tna_task_update_data[$row[csf("sales_order_no")]][205]['doneqnty']+=$row[csf("qty")];
		}
		unset($batchy_data_array);

//Batch Creation...................................................................................end;	






//Grey Fabric Delivery to store................................................................start;	
		  $sql="select a.entry_form,c.job_no,max(a.delevery_date) max_date,min(a.delevery_date) min_date,sum(b.current_delivery) qty  from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b , fabric_sales_order_mst c where a.id=b.mst_id  and b.order_id=c.id and a.knitting_company=$cbo_company and a.entry_form in(56,67) and a.status_active=1 and b.status_active=1 ";
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1){$sql .=" and (b.order_id in(".implode(',',$job_no_process).")";}
				else{$sql .=" or b.order_id in(".implode(',',$job_no_process).")";}
				$p++;
			}
			$sql .=")";
			$sql .=" group by c.job_no,a.entry_form";
			   //echo $sql;die;
			
		$pgrey_fab_delv_data_array=sql_select($sql);
		foreach($pgrey_fab_delv_data_array as $row)
		{
			if($row[csf("entry_form")]==56){
				$tna_task_update_data[$row[csf("job_no")]][201]['max_start_date']=$row[csf("max_date")];
				$tna_task_update_data[$row[csf("job_no")]][201]['min_start_date']=$row[csf("min_date")]; 
				$tna_task_update_data[$row[csf("job_no")]][201]['doneqnty']=$row[csf("qty")];
			}
			elseif($row[csf("entry_form")]==67){
/*				$tna_task_update_data[$row[csf("job_no")]][207]['max_start_date']=$row[csf("max_date")];
				$tna_task_update_data[$row[csf("job_no")]][207]['min_start_date']=$row[csf("min_date")]; 
				$tna_task_update_data[$row[csf("job_no")]][207]['doneqnty']=$row[csf("qty")];
*/			}
			
			
		}
		unset($pgrey_fab_delv_data_array);
//Grey Fabric Delivery to store................................................................end;	




//Grey Fabric Issue................................................................start;	
		  $sql="select c.job_no, max(a.issue_date) max_date, min(a.issue_date) min_date, sum(b.qnty) qty  from inv_issue_master a,pro_roll_details b, fabric_sales_order_mst c
where a.id=b.mst_id and b.po_breakdown_id=c.id and a.knit_dye_company=$cbo_company and b.entry_form=61 and a.item_category=13 and b.status_active=1 and b.is_deleted=0 ";
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1){$sql .=" and (b.po_breakdown_id in(".implode(',',$job_no_process).")";}
				else{$sql .=" or b.po_breakdown_id in(".implode(',',$job_no_process).")";}
				$p++;
			}
			$sql .=")";
			$sql .=" group by c.job_no";
			   //echo $sql;die;
			
		$grey_fab_issue_data_array=sql_select($sql);
		foreach($grey_fab_issue_data_array as $row)
		{
			$tna_task_update_data[$row[csf("job_no")]][203]['max_start_date']=$row[csf("max_date")];
			$tna_task_update_data[$row[csf("job_no")]][203]['min_start_date']=$row[csf("min_date")]; 
			$tna_task_update_data[$row[csf("job_no")]][203]['doneqnty']=$row[csf("qty")];
		}
		unset($grey_fab_issue_data_array);
//Grey Fabric Issue................................................................end;	




//Grey Fabric Rec................................................................start;	
		  $sql="select c.job_no,b.entry_form,max(a.receive_date) max_date,min(a.receive_date) min_date,sum(b.qnty) qty
                    from  inv_receive_mas_batchroll a,pro_roll_details b,fabric_sales_order_mst c
                    where a.id=b.mst_id and b.po_breakdown_id=c.id and a.dyeing_company=$cbo_company and b.entry_form in(62,92)    and b.status_active=1 and b.is_deleted=0 ";
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1){$sql .=" and (b.po_breakdown_id in(".implode(',',$job_no_process).")";}
				else{$sql .=" or b.po_breakdown_id in(".implode(',',$job_no_process).")";}
				$p++;
			}
			$sql .=")";
			$sql .=" group by c.job_no,b.entry_form";
			   //echo $sql;die;
			
		$grey_fab_issue_data_array=sql_select($sql);
		foreach($grey_fab_issue_data_array as $row)
		{
			if($row[csf("entry_form")]==62){
				$tna_task_update_data[$row[csf("job_no")]][204]['max_start_date']=$row[csf("max_date")];
				$tna_task_update_data[$row[csf("job_no")]][204]['min_start_date']=$row[csf("min_date")]; 
				$tna_task_update_data[$row[csf("job_no")]][204]['doneqnty']+=$row[csf("qty")];
			}
			elseif($row[csf("entry_form")]==92){
				$tna_task_update_data[$row[csf("job_no")]][206]['max_start_date']=$row[csf("max_date")];
				$tna_task_update_data[$row[csf("job_no")]][206]['min_start_date']=$row[csf("min_date")]; 
				$tna_task_update_data[$row[csf("job_no")]][206]['doneqnty']+=$row[csf("qty")];
			}
		}
		unset($grey_fab_issue_data_array);
//Grey Fabric Rec................................................................end;	



//Grey Fabric Req for batch................................................................start;	
		  $sql="select c.job_no,max(a.reqn_date) max_date,min(a.reqn_date) min_date,sum(b.reqn_qty) qty,sum(b.booking_qty) req_qty  from pro_fab_reqn_for_batch_mst a,pro_fab_reqn_for_batch_dtls b,fabric_sales_order_mst c 
where a.id=b.mst_id and b.po_id=to_char(c.id) and a.company_id=$cbo_company and b.entry_form=123 and b.is_sales=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1){$sql .=" and (c.id in(".implode(',',$job_no_process).")";}
				else{$sql .=" or c.id in(".implode(',',$job_no_process).")";}
				$p++;
			}
			$sql .=")";
			$sql .=" group by  c.job_no";
			    //echo $sql;die;
			
		$grey_fab_issue_data_array=sql_select($sql);
		foreach($grey_fab_issue_data_array as $row)
		{
			$tna_task_update_data[$row[csf("job_no")]][202]['max_start_date']=$row[csf("max_date")];
			$tna_task_update_data[$row[csf("job_no")]][202]['min_start_date']=$row[csf("min_date")]; 
			$tna_task_update_data[$row[csf("job_no")]][202]['doneqnty']=$row[csf("qty")];
		}
		unset($grey_fab_issue_data_array);
//Grey Fabric Req for batch................................................................end;	











//Daying Production................................................................start;	


/*select c.sales_order_no,max(a.process_end_date) max_date,min(a.process_end_date) min_date,sum(b.production_qty) as qty
 from pro_fab_subprocess a, pro_fab_subprocess_dtls b, pro_batch_create_mst c 
  where a.id=b.mst_id and a.batch_id=c.id  and a.service_company=1  and a.load_unload_id = 1  and c.batch_against in(1,2) and a.status_active=1 
  and a.is_deleted=0 and a.entry_form=35 and c.entry_form=0 and b.status_active=1 and b.is_deleted=0 
  and  c.status_active=1 and c.is_deleted=0 and c.sales_order_id=6072 and c.batch_no = '57754'  group by c.sales_order_no */


	$sql="select a.sales_order_no job_no,max(b.process_end_date) max_date,min(b.process_end_date) min_date,sum(c.production_qty) qty from pro_batch_create_mst a, pro_fab_subprocess b ,pro_fab_subprocess_dtls c 
	where  a.batch_no=b.batch_no and b.id=c.mst_id and b.load_unload_id = 2  and a.is_sales=1 and b.service_company=$cbo_company  and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and b.status_active=1 and c.status_active=1  
	";

			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1){$sql .=" and (a.sales_order_id in(".implode(',',$job_no_process).")";}
				else{$sql .=" or a.sales_order_id in(".implode(',',$job_no_process).")";}
				$p++;
			}
			$sql .=")";
			$sql .=" group by a.sales_order_no";
			    //echo $sql;die;
			
		$grey_fab_issue_data_array=sql_select($sql);
		foreach($grey_fab_issue_data_array as $row)
		{
			$tna_task_update_data[$row[csf("job_no")]][61]['max_start_date']=$row[csf("max_date")];
			$tna_task_update_data[$row[csf("job_no")]][61]['min_start_date']=$row[csf("min_date")]; 
			$tna_task_update_data[$row[csf("job_no")]][61]['doneqnty']=$row[csf("qty")];
		}
		unset($grey_fab_issue_data_array);

//Daying Production................................................................end;	





//Finish Production................................................................start;	
$sql = "select a.entry_form,a.receive_basis,c.sales_order_no job_no, max(a.receive_date) max_date, min(a.receive_date) min_date,sum( b.receive_qnty) qty 
from inv_receive_master a, pro_finish_fabric_rcv_dtls b,pro_batch_create_mst c  where a.id=b.mst_id  and a.item_category=2 and a.entry_form in(7,37) and b.batch_id=c.id and b.is_sales=1 and a.receive_basis in(5,10) and a.knitting_company=$cbo_company and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and b.status_active=1 and c.status_active=1 ";
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1){$sql .=" and (c.sales_order_id in(".implode(',',$job_no_process).")";}
				else{$sql .=" or c.sales_order_id in(".implode(',',$job_no_process).")";}
				$p++;
			}
			$sql .=")";
			$sql .=" group by c.sales_order_no,a.entry_form,a.receive_basis";
			 
			 //echo $sql;die;
			
		$fin_fab_data_array=sql_select($sql);
		foreach($fin_fab_data_array as $row)
		{
			if($row[csf("entry_form")]==7 and $row[csf("receive_basis")]==5){
				$tna_task_update_data[$row[csf("job_no")]][64]['max_start_date']=$row[csf("max_date")];
				$tna_task_update_data[$row[csf("job_no")]][64]['min_start_date']=$row[csf("min_date")]; 
				$tna_task_update_data[$row[csf("job_no")]][64]['doneqnty']=$row[csf("qty")];
			}
			else if($row[csf("entry_form")]==37 and $row[csf("receive_basis")]==10){
				$tna_task_update_data[$row[csf("job_no")]][73]['max_start_date']=$row[csf("max_date")];
				$tna_task_update_data[$row[csf("job_no")]][73]['min_start_date']=$row[csf("min_date")]; 
				$tna_task_update_data[$row[csf("job_no")]][73]['doneqnty']+=$row[csf("qty")];
			}
		}
		unset($fin_fab_data_array);
//Finish Production................................................................end;	





//Finish Production deliver................................................................start;	
$sql="select a.entry_form,c.job_no,max(a.delevery_date) max_date,min(a.delevery_date) min_date,sum(b.current_delivery) qty  from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b , fabric_sales_order_mst c
 where a.id=b.mst_id  and b.order_id=c.id  and a.status_active=1 and b.status_active=1 and a.company_id=$cbo_company ";
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1){$sql .=" and (c.id in(".implode(',',$job_no_process).")";}
				else{$sql .=" or c.id in(".implode(',',$job_no_process).")";}
				$p++;
			}
			$sql .=")";
			$sql .="group by c.job_no,a.entry_form";
			    //echo $sql;die;
			
		$fin_fab_data_array=sql_select($sql);
		foreach($fin_fab_data_array as $row)
		{
			if($row[csf("entry_form")]==54){
			$tna_task_update_data[$row[csf("job_no")]][207]['max_start_date']=$row[csf("max_date")];
			$tna_task_update_data[$row[csf("job_no")]][207]['min_start_date']=$row[csf("min_date")]; 
			$tna_task_update_data[$row[csf("job_no")]][207]['doneqnty']=$row[csf("qty")];
			}
		}
		unset($fin_fab_data_array);
//Finish Production deliver................................................................end;	


//Yarn Dyeing Work Order-Sales................................................................start;	

	$sql="select b.job_no ,max(a.booking_date) max_date,min(a.booking_date) min_date,sum(b.yarn_wo_qty) qty
	 from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.entry_form
	=135 and b.entry_form=135 and a.company_id=$cbo_company and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and a.is_deleted=0 and a.item_category_id=24";
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1){$sql .=" and (b.job_no_id in(".implode(',',$job_no_process).")";}
				else{$sql .=" or b.job_no_id in(".implode(',',$job_no_process).")";}
				$p++;
			}
			$sql .=")";
			$sql .="group by b.job_no";
			    //echo $sql;die;
			
		$yarn_dye_wo_sales_data_array=sql_select($sql);
		foreach($yarn_dye_wo_sales_data_array as $row)
		{
			$tna_task_update_data[$row[csf("job_no")]][51]['reqqnty']+=$row[csf("qty")];
			
		}
		unset($yarn_dye_wo_sales_data_array);


//Yarn Dyeing Work Order-Sales................................................................end;	



//(YD) Yarn Issue................................................................start;	
	$sql="select c.job_no,min(a.issue_date) min_date, max(a.issue_date) max_date,sum(b.cons_quantity) qty
	from inv_issue_master a, inv_transaction b,fabric_sales_order_mst c
	where a.id=b.mst_id and b.job_no=c.job_no  and b.item_category=1 and a.issue_purpose=2  and a.company_id=$cbo_company and a.is_deleted=0 and b.transaction_type=2 and b.is_deleted=0 and c.is_deleted=0 and a.status_active=1 and b.status_active=1 and c.status_active=1 ";
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1){$sql .=" and (c.id in(".implode(',',$job_no_process).")";}
				else{$sql .=" or c.id in(".implode(',',$job_no_process).")";}
				$p++;
			}
			$sql .=")";
			$sql .=" group by c.job_no";
			    //echo $sql;die;
			
		$yarn_issue_data_array=sql_select($sql);
		foreach($yarn_issue_data_array as $row)
		{
			$tna_task_update_data[$row[csf("job_no")]][51]['max_start_date']=$row[csf("max_date")];
			$tna_task_update_data[$row[csf("job_no")]][51]['min_start_date']=$row[csf("min_date")]; 
			$tna_task_update_data[$row[csf("job_no")]][51]['doneqnty']=$row[csf("qty")];
			
			//reqqnty of issue receive..............
			//$tna_task_update_data[$row[csf("job_no")]][52]['reqqnty']+=$row[csf("qty")];
			
		}
		unset($yarn_issue_data_array);

//echo $sql;die;
//Yarn Issue................................................................end;	


// (Y/D)Yarn receive................................................................start;	
	$sql="select c.job_no,min(a.receive_date) min_date, max(a.receive_date) max_date,sum(b.cons_quantity) qty
	from inv_receive_master a, inv_transaction b,fabric_sales_order_mst c
	where a.id=b.mst_id and b.transaction_type=1 and b.job_no=c.job_no  and a.item_category=1 and a.receive_purpose=2 and b.item_category=1  and a.company_id=$cbo_company and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0";
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1){$sql .=" and (c.id in(".implode(',',$job_no_process).")";}
				else{$sql .=" or c.id in(".implode(',',$job_no_process).")";}
				$p++;
			}
			$sql .=")";
			$sql .=" group by c.job_no";
			    //echo $sql;die;
			
		$yarn_issue_data_array=sql_select($sql);
		foreach($yarn_issue_data_array as $row)
		{
			$tna_task_update_data[$row[csf("job_no")]][52]['max_start_date']=$row[csf("max_date")];
			$tna_task_update_data[$row[csf("job_no")]][52]['min_start_date']=$row[csf("min_date")]; 
			$tna_task_update_data[$row[csf("job_no")]][52]['doneqnty']=$row[csf("qty")];
		}
		unset($yarn_issue_data_array);

//Yarn receive................................................................end;	

//Yarn Purchase Requisition................................................................start;	
			$sql="select b.job_no ,max(a.requisition_date) max_date,min(a.requisition_date) min_date,sum(b.quantity) qty from inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b where a.id=b.mst_id and a.entry_form=70 and a.basis=4 and a.status_active=1  and b.is_deleted=0 and b.status_active=1  and a.is_deleted=0 and a.item_category_id=1  ";

			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1){$sql .=" and (b.job_id in(".implode(',',$job_no_process).")";}
				else{$sql .=" or b.job_id in(".implode(',',$job_no_process).")";}
				$p++;
			}
			$sql .=")";
			$sql .=" group by b.job_no";
		
		$yarn_purchase_req_data_array=sql_select($sql);
		foreach($yarn_purchase_req_data_array as $row)
		{
			$tna_task_update_data[$row[csf("job_no")]][45]['max_start_date']=$row[csf("max_date")];
			$tna_task_update_data[$row[csf("job_no")]][45]['min_start_date']=$row[csf("min_date")]; 
			$tna_task_update_data[$row[csf("job_no")]][45]['doneqnty']+=$row[csf("qty")];
			$tna_task_update_data[$row[csf("job_no")]][46]['reqqnty']+=$row[csf("qty")];
		}
		unset($yarn_purchase_req_data_array);



//echo $sql;die;

//Yarn Purchase Requisition................................................................end;	

//Yarn Purchase wo................................................................end;	
$sql = "select  a.entry_form,b.job_no, sum(b.req_quantity) req_qty, sum(b.supplier_order_quantity) qty ,max(a.wo_date) max_date,min(a.wo_date) min_date
        from  wo_non_order_info_mst a, wo_non_order_info_dtls b
        where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$cbo_company and a.id=b.mst_id";
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1){$sql .=" and (b.job_id in(".implode(',',$job_no_process).")";}
				else{$sql .=" or b.job_id in(".implode(',',$job_no_process).")";}
				$p++;
			}
			$sql .=")";
			$sql .=" group by  b.job_no,a.entry_form";
			    //echo $sql;die;
			
		$yarn_issue_data_array=sql_select($sql);
		foreach($yarn_issue_data_array as $row)
		{
			$tna_task_update_data[$row[csf("job_no")]][46]['max_start_date']=$row[csf("max_date")];
			$tna_task_update_data[$row[csf("job_no")]][46]['min_start_date']=$row[csf("min_date")]; 
			$tna_task_update_data[$row[csf("job_no")]][46]['doneqnty']+=$row[csf("qty")];
			if($row[csf("entry_form")]==144){
				$tna_task_update_data[$row[csf("job_no")]][47]['reqqnty']+=$row[csf("qty")];
			}
		}
		unset($yarn_issue_data_array);
//Yarn Purchase wo................................................................end;	

//Yarn Rec................................................................start;	

			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1){$job_con .=" and (f.job_id in(".implode(',',$job_no_process).")";}
				else{$job_con .=" or f.job_id in(".implode(',',$job_no_process).")";}
				$p++;
			}
			$job_con .=")";


	$sql = "select f.job_no,max(b.transaction_date) max_date,min(b.transaction_date) min_date,sum(b.cons_quantity) qty,sum(f.req_quantity) req_qty
	 from inv_transaction b, com_pi_item_details d, wo_non_order_info_mst e, wo_non_order_info_dtls f
	where b.pi_wo_batch_no=d.pi_id and d.work_order_no=e.wo_number and e.id=f.mst_id and e.entry_form=144 $job_con and b.item_category=1 and b.transaction_type=1 and b.receive_basis=1 and e.pay_mode=2 and   b.is_deleted=0 and e.is_deleted=0 and f.is_deleted=0  and b.status_active=1 and e.status_active=1 and f.status_active=1 
	group by f.job_no
	union all
	select f.job_no,max(a.receive_date) max_date,min(a.receive_date) min_date,sum(b.cons_quantity) qty,sum(f.req_quantity) req_qty
	 from inv_transaction b, inv_receive_master a, wo_non_order_info_mst e, wo_non_order_info_dtls f
	where a.id=b.mst_id and a.booking_id=e.id and e.id=f.mst_id and e.entry_form=144 $job_con and b.item_category=1 and b.transaction_type=1 and a.receive_basis=2 and a.entry_form=1 and e.pay_mode <>2 and a.is_deleted=0 and b.is_deleted=0 and e.is_deleted=0 and f.is_deleted=0  and b.status_active=1 and e.status_active=1 and f.status_active=1 
	group by f.job_no";
	//echo $sql;die;
		$yarn_rec_data_array=sql_select($sql);
		foreach($yarn_rec_data_array as $row)
		{
				$tna_task_update_data[$row[csf("job_no")]][47]['max_start_date']=$row[csf("max_date")];
				$tna_task_update_data[$row[csf("job_no")]][47]['min_start_date']=$row[csf("min_date")]; 
				$tna_task_update_data[$row[csf("job_no")]][47]['doneqnty']=$row[csf("qty")];
		}
		unset($yarn_rec_data_array);
//Yarn Rec................................................................end;	


//Finish Fabric Delivery To Garments................................................................start;
			
$p=1;		
foreach($job_no_list_arr as $job_no_process)
{
	/*if($p==1){
		$job_con =" and (b.fso_id in(".implode(',',$job_no_process).")";
	}
	else{
		$job_con .=" or b.fso_id in(".implode(',',$job_no_process).")";
	}
	$p++;
}
$job_con .=")"; 
				
	 $ffdtgSql="select c.job_no,max(TO_CHAR(b.insert_date ,'DD-MON-YY')) max_date,min(TO_CHAR(b.insert_date ,'DD-MON-YY')) min_date , sum(b.delivery_qnty) qty from pro_fin_deli_multy_challan_mst a,pro_fin_deli_multy_challa_dtls b,fabric_sales_order_mst c where a.id=b.mst_id and c.id=b.fso_id and a.entry_form=231 and b.entry_form=231 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $job_con 
	 group by c.job_no";
	 */

	if($db_type==0)
	{
		$selectDate="max(date(b.insert_date ,'DD-MON-YY')) max_date,min(date(b.insert_date ,'DD-MON-YY')) min_date";
	}
	else
	{
		$selectDate="max(TO_CHAR(b.insert_date ,'DD-MON-YY')) max_date,min(TO_CHAR(b.insert_date ,'DD-MON-YY')) min_date";
	}

	 
if($p==1){
		$job_con =" and (e.id in(".implode(',',$job_no_process).")";
	}
	else{
		$job_con .=" or e.id in(".implode(',',$job_no_process).")";
	}
	$p++;
}
$job_con .=")"; 
	 
$ffdtgSql="select  $selectDate , sum(b.issue_qnty) delivery_qty, e.job_no as fso_id
 from inv_issue_master a,inv_finish_fabric_issue_dtls b,product_details_master d,fabric_sales_order_mst e
 where a.company_id=1 and a.entry_form=224 and a.id=b.mst_id and b.prod_id=d.id and b.order_id=e.id and a.status_active='1' and a.is_deleted='0' $job_con and b.status_active=1 and d.status_active=1 and e.status_active=1 
 group by e.job_no";	 
	 
	 		
		$ff_delivery_to_grms_result = sql_select( $ffdtgSql );
		foreach( $ff_delivery_to_grms_result as $row ) 
		{
			$tna_task_update_data[$row[csf("fso_id")]][239]['max_start_date']=$row[csf("max_date")];
			$tna_task_update_data[$row[csf("fso_id")]][239]['min_start_date']=$row[csf("min_date")]; 
			$tna_task_update_data[$row[csf("fso_id")]][239]['doneqnty']=$row[csf("qty")];
		}
		unset($ff_delivery_to_grms_result);
		
//Finish Fabric Delivery To Garments..................................................................end;	





//Process History data ........................................................................................start;		
		if($db_type==0)
		{
			$sql = "SELECT job_no,task_number,po_number_id,actual_start_date,actual_finish_date,task_start_date,task_finish_date ,plan_start_flag,plan_finish_flag FROM tna_plan_actual_history WHERE po_number_id in ( ".$po_id_str." ) and status_active =1 and is_deleted=0 and task_type=2";
		}
		else
		{
			$booking_id_list_arr=array_chunk($booking_id_arr,999);
		
			$sql = "SELECT job_no,task_number,po_number_id,actual_start_date,actual_finish_date,task_start_date,task_finish_date ,plan_start_flag,plan_finish_flag FROM tna_plan_actual_history WHERE  ";
			$p=1;
			foreach($booking_id_list_arr as $booking_id)
			{
				if($p==1) $sql .="  ( po_number_id in(".implode(',',$booking_id).")"; else  $sql .=" or po_number_id in(".implode(',',$booking_id).")";
				
				$p++;
			}
			$sql .=")  and status_active =1 and is_deleted=0 and task_type=2";
			
			 
		}
	//echo $sql;die;
		 
		$result = sql_select( $sql );
		$tna_updated_date = array();
		 
		foreach( $result as $row ) 
		{
			$tna_updated_date[$row[csf('job_no')]][$row[csf('task_number')]]['start']=$row[csf('actual_start_date')];
			$tna_updated_date[$row[csf('job_no')]][$row[csf('task_number')]]['finish']=$row[csf('actual_finish_date')];
			$tna_updated_date[$row[csf('job_no')]][$row[csf('task_number')]]['planstart']=$row[csf('task_start_date')];
			$tna_updated_date[$row[csf('job_no')]][$row[csf('task_number')]]['planfinish']=$row[csf('task_finish_date')];
			$tna_updated_date[$row[csf('job_no')]][$row[csf('task_number')]]['planstartflag']=$row[csf('plan_start_flag')];
			$tna_updated_date[$row[csf('job_no')]][$row[csf('task_number')]]['planfinishflag']=$row[csf('plan_finish_flag')];
		}
//Process History data........................................................................................end;		
//Process Master data........................................................................................start;		

		if($db_type==0)
		{
			$sql = "SELECT id,job_no,po_number_id,task_category,task_number,actual_start_date,actual_finish_date,template_id FROM tna_process_mst WHERE po_number_id in ( $po_id_str ) and status_active =1 and is_deleted = 0 and task_type=2";
		}
		else
		{
			
			$sql = "SELECT id,job_no,po_number_id,task_category,task_number,actual_start_date,actual_finish_date,template_id FROM tna_process_mst WHERE status_active =1 and is_deleted = 0 and task_type=2";
			
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1) $sql .=" and (po_number_id in(".implode(',',$job_no_process).")"; else  $sql .=" or po_number_id in(".implode(',',$job_no_process).")";
				
				$p++;
			}
			$sql .=")";
		}
		
		$result = sql_select( $sql );
		$tna_process_list = array();$tna_process_details = array();$changed_templates=array();
		foreach( $result as $row ) 
		{
			if( $booking_template[$row[csf('job_no')]]==$row[csf('template_id')] )
			{
				$tna_process_list[$row[csf('job_no')]][$row[csf('task_number')]]= $row[csf('id')];
				$tna_process_details[$row[csf('id')]]['start']=$row[csf('actual_start_date')];
				$tna_process_details[$row[csf('id')]]['finish']=$row[csf('actual_finish_date')];
			}
			else if( $row[csf('template_id')]=='' )
			{
				$tna_process_list[$row[csf('job_no')]][$row[csf('task_number')]]= $row[csf('id')];
				$tna_process_details[$row[csf('id')]]['start']=$row[csf('actual_start_date')];
				$tna_process_details[$row[csf('id')]]['finish']=$row[csf('actual_finish_date')];
			}
			else
			{
				$changed_templates[$row[csf('job_no')]]=$row[csf('po_number_id')];
			}
		}
	 	
		//print_r($tna_process_list);die;
		
		if( count($changed_templates)>0 )
		{
			$con = connect();
			$rid=execute_query("delete FROM tna_process_mst WHERE po_number_id in(".implode(",",$changed_templates).")",1);
			if( $db_type==2 ) oci_commit($con); 
		}

//Process Master data........................................................................................end;		

//var_dump($tna_task_update_data['FTML-FSOE-18-06292'][51]);die;


//---------------------------------------------------------------------------------------------------------
	
	//print_r($template_wise_task[17]);die;
	
	
	 
		$field_array_tna_process="id,template_id,job_no,po_number_id,po_receive_date,shipment_date,task_category,task_number,target_date,task_start_date,task_finish_date,notice_date_start,notice_date_end,process_date,sequence_no,plan_start_flag,plan_finish_flag,status_active,is_deleted,task_type";
		$field_array_tna_process_up="actual_start_date*actual_finish_date";

		$approval_array=array();
		
		
		 //print_r($booking_details);;die;
		
		foreach($booking_details as $row )// Non Process Starts Here
		{
			foreach( $template_wise_task[$row[template_id]]  as $task_id=>$row_task)
			{  
				 
				if($to_process_task[$row[booking_no]][$row_task[task_name]]!="")
				{
					if ($tna_process_type==1)
					{ 
						if($db_type==0) $target_date=add_date($row[delivery_date] ,- $row_task[deadline]);
						else $target_date=change_date_format(trim(add_date($row[delivery_date] ,- $row_task['deadline'])),'','',1);
						 
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
						 
						if($db_type==0) $target_date=add_date($row[booking_date] , $row_task[execution_days]);
						else $target_date=change_date_format(trim(add_date($row[booking_date] ,$row_task['execution_days'])),'','',1);
						 
						//$to_add_days=$row_task['execution_days']-1;
						if($db_type==0) $start_date=add_date($row[booking_date] ,$row_task[deadline]);
						else $start_date=change_date_format(trim(add_date($row[booking_date] ,$row_task[deadline])),'','',1);
						 
						$finish_date=$target_date;
						$to_add_days=$row_task['notice_before'];
						
						if($db_type==0) $notice_date_start=add_date($start_date ,-$to_add_days);
						else $notice_date_start=change_date_format(trim(add_date($start_date ,-$to_add_days)),'','',1);
						 
						
						if($db_type==0) $notice_date_end=add_date($finish_date ,-$to_add_days);
						else $notice_date_end=change_date_format(trim(add_date($finish_date ,-$to_add_days)),'','',1);
					}
					//print_r($tna_process_list[$row[po_id]][$row_task[task_name]]); die;
					
					if( $tna_process_list[$row[booking_no]][$row_task[task_name]]=="") 
					{ 
						if ($mst_id==""){$mst_id=return_next_id( "id", "tna_process_mst");}else{$mst_id+=1;}
						if ($data_array_tna_process!=""){$data_array_tna_process .=",";}
						 
						
						if($tna_updated_date[$row[booking_no]][$row_task[task_name]]['planstart'] =='0000-00-00') $tna_updated_date[$row[booking_no]][$row_task[task_name]]['planstart'] ='';
						if($tna_updated_date[$row[booking_no]][$row_task[task_name]]['planfinish'] =='0000-00-00') $tna_updated_date[$row[booking_no]][$row_task[task_name]]['planfinish'] ='';
						
						if( $tna_updated_date[$row[booking_no]][$row_task[task_name]]['planstart'] !='' ) $start_date=$tna_updated_date[$row[booking_no]][$row_task[task_name]]['planstart'];
						if( $tna_updated_date[$row[booking_no]][$row_task[task_name]]['planfinish'] !='' ) $finish_date=$tna_updated_date[$row[booking_no]][$row_task[task_name]]['planfinish'];
						
						
						$plan_start_flag=$tna_updated_date[$row[booking_no]][$row_task[task_name]]['planstartflag']*1;
						$plan_finish_flag=$tna_updated_date[$row[booking_no]][$row_task[task_name]]['planfinishflag']*1;
						
						
						$data_array_tna_process .="('$mst_id','$row[template_id]','$row[booking_no]','$row[booking_id]','$row[booking_date]','$row[delivery_date]','1','$row_task[task_name]','$target_date','$start_date','$finish_date','$notice_date_start','$notice_date_end','$date','$row_task[sequence_no]',$plan_start_flag,$plan_finish_flag,1,0,2)";
						
						$insert_string[] ="('$mst_id','$row[template_id]','$row[booking_no]','$row[booking_id]','$row[booking_date]','$row[delivery_date]','1','$row_task[task_name]','$target_date','$start_date','$finish_date','$notice_date_start','$notice_date_end','$date','$row_task[sequence_no]',$plan_start_flag,$plan_finish_flag,1,0,2)";
						
					}
					else
					{ 	
					
					
						if ( ($tna_task_update_data[$row[booking_no]][$row_task[task_name]]['min_start_date']=="0000-00-00" || $tna_task_update_data[$row[booking_no]][$row_task[task_name]]['min_start_date']=="") && ($tna_task_update_data[$row[booking_no]][$row_task[task_name]]['max_start_date']!="0000-00-00" || $tna_task_update_data[$row[booking_no]][$row_task[task_name]]['max_start_date']!="" ) )
						{  
							$tna_task_update_data[$row[booking_no]][$row_task[task_name]]['min_start_date']= $tna_task_update_data[$row[booking_no]][$row_task[task_name]]['max_start_date'];
						}
						
						if ( $tna_task_update_data[$row[booking_no]][$row_task[task_name]]['min_start_date']!="0000-00-00" || $tna_task_update_data[$row[booking_no]][$row_task[task_name]]['min_start_date']!="" ){$start_date=$tna_task_update_data[$row[booking_no]][$row_task[task_name]]['min_start_date'];}else{$start_date="0000-00-00";}
						
						if ( $tna_task_update_data[$row[booking_no]][$row_task[task_name]]['max_start_date']!="0000-00-00" || $tna_task_update_data[$row[booking_no]][$row_task[task_name]]['max_start_date']!="" ){$finish_date=$tna_task_update_data[$row[booking_no]][$row_task[task_name]]['max_start_date'];}else{$finish_date="0000-00-00";}
						
						//if(!in_array($row_task[task_name],$approval_array))
						
						if($approval_array[$row_task[task_name]]=='')
						{
							$compl_perc=get_percent($tna_task_update_data[$row[booking_no]][$row_task[task_name]]['doneqnty'], $tna_task_update_data[$row[booking_no]][$row_task[task_name]]['reqqnty']); 
							if($compl_perc<$row_task[completion_percent])
							{
								$finish_date=$blank_date;
							}
						}
						else
						{
							if( $tna_task_update_data[$row[booking_no]][$row_task[task_name]]['noofapproved']!=$tna_task_update_data[$row[booking_no]][$row_task[task_name]]['noofval']) $finish_date=$blank_date; //"0000-00-00";
						}
						
	
	
						
						if($tna_updated_date[$row[booking_no]][$row_task[task_name]]['start'] =='0000-00-00'){ $tna_updated_date[$row[booking_no]][$row_task[task_name]]['start'] ='';}
						if($tna_updated_date[$row[booking_no]][$row_task[task_name]]['finish'] =='0000-00-00'){ $tna_updated_date[$row[booking_no]][$row_task[task_name]]['finish'] ='';}
						
						if( $tna_updated_date[$row[booking_no]][$row_task[task_name]]['start'] !='' ){$start_date=$tna_updated_date[$row[booking_no]][$row_task[task_name]]['start'];}
						if( $tna_updated_date[$row[booking_no]][$row_task[task_name]]['finish'] !='' ){$finish_date=$tna_updated_date[$row[booking_no]][$row_task[task_name]]['finish'];}
						
						
						$process_id_up_array[$tna_process_list[$row[booking_no]][$row_task[task_name]]]=$tna_process_list[$row[booking_no]][$row_task[task_name]];
						
						$data_array_tna_process_up[$tna_process_list[$row[booking_no]][$row_task[task_name]]] =explode(",",("'".$start_date."','".$finish_date."'")); 
					}
				} // To Process Task List check
			}
			
			
			//print_r($data_array_tna_process_up);die;
		}
		
	 
		
	} // Foreach Company level

   

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
	
		mysql_query("COMMIT");
	
	}
	if($db_type==1 || $db_type==2 )
	{
		if( $data_array_tna_process!="" ) 
		{
			$tna_pro_array=array_chunk($insert_string,50);
			foreach($tna_pro_array as $dd=>$tna_pro_list)
			{
				
				//echo "insert into tna_process_mst ".$field_array_tna_process.' value '.implode(",",$tna_pro_list);die;
				
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

 //echo print_r($data_array_tna_process_up);
//.....................................................................................
	
	disconnect($con);
	echo "0**Is insert:".$rID.",Is Update:".$rID_up."**".implode(", ",$template_missing_po);
	die;
	
}//end tna_process;




// Always treat the lowest template ... if not no process on that
function get_tna_template( $remain_days, $tna_template, $buyer ) 
{
	
	
	
	
	global $tna_template_buyer; // print_r($tna_template_buyer);die;
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
	return number_format((($completed*100)/$actual),0);
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
                        	<th>
                              <?
                               echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" );
                              ?>
                            </th>
                          <th colspan="3"> </th>
                        </thead>
                    <thead>                	 
                        <th width="150">Company Name</th>
                        <th width="150">Buyer Name</th>
                        <th width="150"><? echo $caption;?></th>
                        <th colspan="2"><? echo $caption;?> Date</th>
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
                    <td><input name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:130px"></td>
                    <td>
                    	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
					</td> 
                    <td>
					  	<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					</td> 
            		<td align="center">
                     <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+<? echo $textile_tna_process_base;?>, 'ponumber_search_list_view', 'search_div', 'tna_process_textile_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" /></td>
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
 
 list($company,$buyer,$start_date,$end_date,$booking,$year,$surch_by,$process_base)=explode('_',$data);
  if($process_base==2){
	$booking_fill="a.job_no";	 
	$caption="Sales Order";	 
 }
 else
 {
	$booking_fill="a.booking_no";
	$caption="Booking";	 
 }
 

 
 if($buyer==0 && $booking=='' && str_replace("'","",$start_date)==''){
	 echo "<h2 style='color:#F00'>Please Select $caption Date</h2>";exit();
 }
 if($company==0){echo"<h2 style='color:#d00'>Please select company</h2>";die;}
 
 

 $company_array=return_library_array( "select id, company_name from lib_company where is_deleted=0",'id','company_name');
 $buyer_array=return_library_array( "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90,80)) order by buy.buyer_name",'id','buyer_name');

 if($buyer!=0) $buyer_con="and a.buyer_id='$buyer'"; else $buyer_con="";
 if($surch_by==1)
 {
	 if($booking!="") $booking_no_con="and $booking_fill='".trim($booking)."'"; else $booking_no_con="";
 }
 else if($surch_by==2)
 {
	 if($booking!="") $booking_no_con="and $booking_fill like '".trim($booking)."%'"; else $booking_no_con="";
 }
 else if($surch_by==3)
 {
	 if($booking!="") $booking_no_con="and $booking_fill like '%".trim($booking)."'"; else $booking_no_con="";
 }
 else if($surch_by==4 || $surch_by==0)
 {
	 if($booking!="") $booking_no_con="and $booking_fill like '%".trim($booking)."%'"; else $booking_no_con="";
 }
 
	$start_date=str_replace("'","",$start_date);
	$end_date=str_replace("'","",$end_date); 
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
		$date_cond  = " and a.delivery_date between '".change_date_format($start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'";
		}
		
		if($db_type==2)
		{
		$date_cond  = " and a.delivery_date between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-",1)."'";
		}
	}
	else
	{
	$date_cond  = "";	
	}
	
	
	
 ?>
        <table width="802" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
            <thead>
                <th width="40" height="34">SL</th>
                <th width="130">Company Name</th>
                <th width="130">Buyer/Unit</th>
                <th width="130"><? echo $caption;?> No</th>
                <th width="110">Booking Date</th>
                <th width="150">Delivery Date</th>
                <th>Lead Time</th>
            </thead>
        </table>
        <div style="width:802px; max-height:210px; overflow-y:scroll"> 
        <table width="780" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_search">
		<?
			if($db_type==0) $lead_time="DATEDIFF(a.delivery_date,a.booking_date) as  date_diff";
			if($db_type==2) $lead_time="(a.delivery_date-a.booking_date) as  date_diff";
			
			
			if($process_base==1){//booking;
				$sql = "select a.within_group,a.id,a.company_id,a.booking_date,a.delivery_date,a.booking_no,a.buyer_id,$lead_time
				from wo_booking_mst a
				WHERE a.is_deleted = 0 and a.status_active=1 and company_id=".$company." $buyer_con $booking_no_con $date_cond
				ORDER BY a.delivery_date asc";
			}
			else
			{
				$sql = "select a.within_group,a.id,a.company_id,a.booking_date,a.delivery_date,a.job_no as booking_no,a.buyer_id,$lead_time
				from fabric_sales_order_mst a
				WHERE a.is_deleted = 0 and a.status_active=1 and company_id=".$company." $buyer_con $booking_no_con $date_cond
				ORDER BY a.delivery_date asc";
			}
			
			//echo $sql;
			
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
                   <td width="130" align="center" id="txt_style_reff" ><p><? echo $row[csf("booking_no")]; ?></p></td>
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
        <table width="750">
        	<td><input onClick="check_all_data(<? echo $i;?>)" type="checkbox" id="check_all"> All Select/Unselect</td>
        	<td align="center"><input type="button" class="formbutton" value="Close" onClick="parent.emailwindow.hide();" style="width:100px;" />
            </td>
        </table>
    
    
    
<?       
exit();
}
?>
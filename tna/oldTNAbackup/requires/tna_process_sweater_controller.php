<?
/*--------------------------------------------Comments----------------
Version (MySql)          :  V2
Version (Oracle)         :  V1
Converted by             :  
Converted Date           :  
Purpose			         : 	
Functionality	         :	
JS Functions	         : 
Requirment Client        :  
Requirment By            : 
Requirment type          : 
Requirment               : 
Affected page            : 
Affected Code            :                   
DB Script                : 
Updated by 		         :  Reza		
Update date		         :  03.10.2018	   
QC Performed BY	         :	
QC Date			         :	
Comments		         :  From this version oracle conversion is start
----------------------------------------------------------------------*/
header('Content-type:text/html; charset=utf-8');
session_start();
extract ( $_REQUEST );

include('../../includes/common.php');



if( $action=="load_drop_down_buyer" )
{	
	echo create_drop_down( "cbo_buyer", 170, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );  
	die; 	 
}

if( $cbo_company<1 ) $company_array=return_library_array( "select id,id from lib_company",'id','id' );
else $company_array[$cbo_company]=$cbo_company;
 
if ( $action=="tna_process" )
{ 
	
	
	$tba_color_id=return_field_value("id","lib_color"," color_name ='TBA'");

	$sql = "SELECT task_name,completion_percent FROM lib_tna_task WHERE is_deleted = 0 and status_active=1 order by task_name asc";
	$result = sql_select( $sql );
 	
	foreach( $result as $row ) 
	{
		$tna_completion[$row[csf('task_name')]]=$row[csf('completion_percent')];
		 
	}
	

	foreach( $company_array as $cbo_company )
	{
		$tna_process_type=return_field_value("tna_process_type"," variable_order_tracking"," company_name=".$cbo_company." and variable_list=31"); 
		$tna_process_start_date=return_field_value("tna_process_start_date"," variable_order_tracking"," company_name=".$cbo_company." and variable_list=43"); 
		
		 
		if( $tna_process_type==2 )
		{
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
				$tna_task_name[$row[csf('id')]]=$row[csf('task_name')];
				$tna_task_details[$row[csf('task_name')]]['task_catagory']=  $row[csf('task_catagory')];
				$tna_task_details[$row[csf('task_name')]]['id']=  $row[csf('id')];
				$tna_task_details[$row[csf('task_name')]]['task_name']=  $row[csf('task_name')];
				$tna_task_details[$row[csf('task_name')]]['task_short_name']=  $row[csf('task_short_name')];
				$tna_task_details[$row[csf('task_name')]]['task_type']=  $row[csf('task_type')];
				$tna_task_details[$row[csf('task_name')]]['module_name']=  $row[csf('module_name')];
				$tna_task_details[$row[csf('task_name')]]['link_page']=  $row[csf('link_page')];
				$tna_task_details[$row[csf('task_name')]]['penalty']=  $row[csf('penalty')];
				$tna_task_details[$row[csf('task_name')]]['completion_percent']=  $row[csf('completion_percent')];
			}
		}
		
	   //print_r($tna_task_details);die;	
		
		 //Template Details
			$sql_task = "SELECT a.id,task_template_id,lead_time,material_source,total_task,tna_task_id,deadline,execution_days,notice_before,sequence_no,for_specific,b.task_catagory,b.task_name,b.task_type FROM  tna_task_template_details a, lib_tna_task b WHERE   a.tna_task_id=b.task_name and a.is_deleted=0 and a.status_active=1 and a.task_type=3 and b.is_deleted=0 and b.status_active=1 order by for_specific,lead_time ";
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
				//if (!in_array($row[csf("task_template_id")],$template))
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
			
		 
		  //print_r($template_wise_task);die;	
		 
		 
		$sql = "SELECT company_name,tna_integrated FROM  variable_order_tracking WHERE  company_name=".$cbo_company." and status_active =1 and is_deleted = 0 and variable_list=14";
		$result = sql_select( $sql );
		$variable_settings = array();
		foreach( $result as $row ) 
		{		
			$variable_settings[$row[csf('company_name')]] = $row[csf('tna_integrated')];
		}
		if( $db_type==0 ) $blank_date="0000-00-00"; else $blank_date=""; 
		// Reprocess Check
		
		
		
		if (trim($txt_ponumber_id)==""){
			if( $is_delete==1 )
			{
				$job_array=return_library_array( "select id, job_no from wo_po_details_master where buyer_name='$cbo_buyer' and company_name=$cbo_company",'id','job_no');
				$job_str=implode("','",$job_array);
				
				$con = connect();
				//$rid=execute_query("delete FROM tna_process_mst WHERE  job_no in ('".$job_str."')",1);
				$p=1;
				$job_no_list_arr=array_chunk($job_array,999);
				foreach($job_no_list_arr as $job_no_process)
				{
					if($p==1){$sql_con .=" and (job_no in('".implode("','",$job_no_process)."')";} 
					else{$sql_con .=" or job_no in('".implode("','",$job_no_process)."')";}
					$p++;
				}
				$sql_con .=")";
				$rid=execute_query("delete FROM tna_process_mst WHERE  task_type=3 $sql_con ",1);
			
			if( $db_type==2 ) oci_commit($con); 
			}

		}
		else 
		{
			if( $is_delete==1 )
			{
				$con = connect();
				$rid=execute_query("delete FROM tna_process_mst WHERE task_type=3 and  po_number_id in ( $txt_ponumber_id )",1);
				if( $db_type==2 ) oci_commit($con); 
			}
			
		}
		
		if( $cbo_buyer>0 ){$buyer_cond=" and a.buyer_name=$cbo_buyer ";}else{ $buyer_cond="";}
		  
	
		
		
		
		
		
		if ( $txt_ponumber_id=="" )
		{
			if($db_type==0)
			{
				$sql = "SELECT (pub_shipment_date) as shipment_date,job_no_mst,po_received_date,b.id,po_quantity,a.buyer_name,a.garments_nature,b.po_number,pp_meeting_date,a.style_ref_no,is_confirmed,tna_task_from_upto FROM wo_po_break_down b, wo_po_details_master a WHERE b.is_deleted = 0 and b.status_active=1 and a.is_deleted = 0 and a.status_active=1 and a.job_no=b.job_no_mst and b.pub_shipment_date!='0000-00-00' and b.po_received_date!='0000-00-00' and company_name=".$cbo_company." $buyer_cond and b.pub_shipment_date>'$tna_process_start_date'  ORDER BY b.shipment_date asc";
			}
			else
			{									
				$sql = "SELECT (pub_shipment_date) as shipment_date,job_no_mst,po_received_date,b.id,po_quantity,a.buyer_name,a.garments_nature,b.po_number,pp_meeting_date,a.style_ref_no,is_confirmed,tna_task_from_upto FROM wo_po_break_down b, wo_po_details_master a WHERE b.is_deleted = 0 and b.status_active=1 and a.is_deleted = 0 and a.status_active=1 and a.job_no=b.job_no_mst and to_char(b.pub_shipment_date)!='0000-00-00' and to_char(b.po_received_date)!='0000-00-00' and company_name=".$cbo_company." $buyer_cond   and (b.pub_shipment_date)>'$tna_process_start_date' ORDER BY b.shipment_date asc";
			}
		}
		else
		{
			if($db_type==0)
			{
				$sql = "SELECT (pub_shipment_date) as shipment_date,job_no_mst,po_received_date,b.id,po_quantity,a.buyer_name,a.garments_nature,b.po_number,pp_meeting_date,a.booking_meeting_date,a.style_ref_no,is_confirmed,tna_task_from_upto FROM wo_po_break_down b, wo_po_details_master a WHERE b.is_deleted = 0 and b.status_active=1 and a.is_deleted = 0 and a.status_active=1 and a.job_no=b.job_no_mst and b.pub_shipment_date!='0000-00-00' and b.po_received_date!='0000-00-00' and b.id  in ( $txt_ponumber_id )  and b.pub_shipment_date>'$tna_process_start_date'  $buyer_cond and company_name=".$cbo_company."  ORDER BY b.shipment_date asc";
			}
			else
			{
				$sql = "SELECT (pub_shipment_date) as shipment_date,job_no_mst,po_received_date,b.id,po_quantity,a.buyer_name,a.garments_nature,b.po_number,pp_meeting_date,a.booking_meeting_date,a.style_ref_no,is_confirmed,tna_task_from_upto FROM wo_po_break_down b, wo_po_details_master a WHERE b.is_deleted = 0 and b.status_active=1 and a.is_deleted = 0 and a.status_active=1 and a.job_no=b.job_no_mst and to_char(b.pub_shipment_date)!='0000-00-00' and to_char(b.po_received_date)!='0000-00-00' and b.id  in ( $txt_ponumber_id )  and (b.pub_shipment_date)>'$tna_process_start_date' and company_name=".$cbo_company." $buyer_cond  ORDER BY b.shipment_date asc";
			}
		}
		 
		 
		 
		$to_process_task=array();
		$data_array=sql_select($sql);
		  
		$job_no_array=array();
		$order_id_array=array();
		$po_order_template=array();
		$po_order_details=array();
		$job_nature = array();
		$template_missing_po=array();
		$tna_task_update_data=array();
		$template_missing_po_mail_data_arr=array();
		$i=0;
		
		foreach($data_array as $row)
		{
			$remain_days=datediff( "d", date("Y-m-d",strtotime($row[csf("po_received_date")])), date("Y-m-d",strtotime($row[csf("shipment_date")])) );
			 
			if ( $tna_process_type==1 )
			{
				$template_id=get_tna_template($remain_days,$tna_template,$row[csf("buyer_name")]);
			}
			else
			{
				
				$template_id=$remain_days-1; 
				
				if($tna_task_percent_buyer_wise[$row[csf('buyer_name')]]=="")
				{
				 
					foreach($tna_task_percent as $id=>$data)
					{
						$deadline=floor($template_id*$data[start_percent]/100);
						$exe=floor($template_id*$data[end_percent]/100);
						if($deadline==0) $v=0; else $v=1;  if($exe==0) $e=0; else $e=1;
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
					foreach($tna_task_percent_buyer[$row[csf("buyer_name")]] as $id=>$data)
					{
						$deadline=floor($template_id*$data[start_percent]/100);
						$exe=floor($template_id*$data[end_percent]/100);
						if($deadline==0) $v=0; else $v=1;  if($exe==0) $e=0; else $e=1;
	
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
			
			 
			 //echo $template_id; die;
									
			if ( $template_id=="" || $template_id==0 )
			{
				$template_missing_po[]=$row[csf("po_number")];
				//This array for missiong PO Auto mail send..............
				$template_missing_po_mail_data_arr[]=array(
					'job_no_mst'		=> $row[csf("job_no_mst")],
					'style_ref_no'		=> $row[csf("style_ref_no")],
					'buyer_name'		=> $row[csf("buyer_name")],
					'po_number'			=> $row[csf("po_number")],
					'po_received_date'	=> $row[csf("po_received_date")],
					'shipment_date'		=> $row[csf("shipment_date")]
				
				);
			} 
			else
			{
				$job_no_array[$row[csf("job_no_mst")]]= $row[csf("job_no_mst")];
				$order_id_array[$row[csf("id")]]=$row[csf("id")];
				$po_order_template[$row[csf("id")]]=  $template_id; 
				$po_order_details[$row[csf("id")]]['po_received_date']=$row[csf("po_received_date")];
				$po_order_details[$row[csf("id")]]['shipment_date']=$row[csf("shipment_date")];
				$po_order_details[$row[csf("id")]]['job_no_mst']=$row[csf("job_no_mst")];
				$po_order_details[$row[csf("id")]]['po_quantity']=$row[csf("po_quantity")];
				$po_order_details[$row[csf("id")]]['template_id']=$template_id;
				$po_order_details[$row[csf("id")]]['po_id']=$row[csf("id")];
			
			
			
				$tna_task_auto_process=array(7,10,12,15,17,32,46,47,80,101,110,120,121,131,177,178,183,240,241,242,243,244,245,246,247,249);
				foreach($tna_task_auto_process as $vid=>$vtask)
				{
					$to_process_task[$row[csf("id")]][$vtask]=$vtask;
				}
			
			
			
			
			}
		}
		
		
		
		$po_ids=implode(",",$order_id_array);
		$job_no_list="'".implode("','",$job_no_array)."'";
		
	
	unset($data_array);
	//unset($order_id_array); 
	//unset($job_no_array);
	
	
	
	
		if($db_type==0)
		{
			$sql = "SELECT id,job_no,po_number_id,task_category,task_number,actual_start_date,actual_finish_date,template_id FROM tna_process_mst WHERE po_number_id in ( $po_ids ) and status_active =1 and is_deleted = 0 and task_type=3";
		}
		else
		{
			$job_no_list_arr=array_chunk($order_id_array,999);
			
			$sql = "SELECT id,job_no,po_number_id,task_category,task_number,actual_start_date,actual_finish_date,template_id FROM tna_process_mst WHERE status_active =1 and is_deleted = 0  and task_type=3";
			
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1) $sql .=" and (po_number_id in(".implode(',',$job_no_process).")"; else  $sql .=" or po_number_id in(".implode(',',$job_no_process).")";
				
				$p++;
			}
			$sql .=")";
		}
		//echo  $sql;die;
		$result = sql_select( $sql );
		$tna_process_list = array();
		$tna_process_details = array();
		$changed_templates=array();
		foreach( $result as $row ) 
		{
			if( $po_order_template[$row[csf('po_number_id')]]==$row[csf('template_id')] )
			{
				$tna_process_list[$row[csf('po_number_id')]][$row[csf('task_number')]]= $row[csf('id')];
				$tna_process_details[$row[csf('id')]]['start']=$row[csf('actual_start_date')];
				$tna_process_details[$row[csf('id')]]['finish']=$row[csf('actual_finish_date')];
			}
			else if( $row[csf('template_id')]=='' )
			{
				$tna_process_list[$row[csf('po_number_id')]][$row[csf('task_number')]]= $row[csf('id')];
				$tna_process_details[$row[csf('id')]]['start']=$row[csf('actual_start_date')];
				$tna_process_details[$row[csf('id')]]['finish']=$row[csf('actual_finish_date')];
			}
			else
			{
				$changed_templates[$row[csf('po_number_id')]]=$row[csf('po_number_id')];
			}
		}
	 	
	
	
	
	
	
	
	
	
	
	   
//****************************************xxxxxxxxxxxxxxxxxxxxx*****************************************

		if($db_type==0)
		{
			$sql = "SELECT task_number,po_number_id,actual_start_date,actual_finish_date,task_start_date,task_finish_date ,plan_start_flag,plan_finish_flag FROM tna_plan_actual_history WHERE po_number_id in ( $po_ids ) and status_active =1 and is_deleted=0  and task_type=3";
		}
		else
		{
			$job_no_list_arr=array_chunk(array_unique(explode(",",$po_ids)),999);
		
			$sql = "SELECT task_number,po_number_id,actual_start_date,actual_finish_date,task_start_date,task_finish_date ,plan_start_flag,plan_finish_flag FROM tna_plan_actual_history WHERE  ";
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1) $sql .="  ( po_number_id in(".implode(',',$job_no_process).")"; else  $sql .=" or po_number_id in(".implode(',',$job_no_process).")";
				
				$p++;
			}
			$sql .=")  and status_active =1 and is_deleted=0 and task_type=3";
			
			 
		}
	//echo $sql;die;
		 
		$result = sql_select( $sql );
		$tna_updated_date = array();
		 
		foreach( $result as $row ) 
		{
			$tna_updated_date[$row[csf('po_number_id')]][$row[csf('task_number')]]['start']=$row[csf('actual_start_date')];
			$tna_updated_date[$row[csf('po_number_id')]][$row[csf('task_number')]]['finish']=$row[csf('actual_finish_date')];
			$tna_updated_date[$row[csf('po_number_id')]][$row[csf('task_number')]]['planstart']=$row[csf('task_start_date')];
			$tna_updated_date[$row[csf('po_number_id')]][$row[csf('task_number')]]['planfinish']=$row[csf('task_finish_date')];
			
			$tna_updated_date[$row[csf('po_number_id')]][$row[csf('task_number')]]['planstartflag']=$row[csf('plan_start_flag')];
			$tna_updated_date[$row[csf('po_number_id')]][$row[csf('task_number')]]['planfinishflag']=$row[csf('plan_finish_flag')];
			
			
		}
		



   //print_r($to_process_task);die;

//---------------------------------------------------------------------------------------------------------
	
	
	
	 
		$field_array_tna_process="id,template_id,job_no,po_number_id,po_receive_date,shipment_date,task_category,task_number,target_date,task_start_date,task_finish_date,notice_date_start,notice_date_end,process_date,sequence_no,plan_start_flag,plan_finish_flag,status_active,is_deleted,task_type";
		$field_array_tna_process_up="actual_start_date*actual_finish_date";

		$approval_array=array();
		
		
		 //echo $tna_process_type;die;
		
		foreach( $po_order_details as $row )  // Non Process Starts Here
		{
			
			foreach( $template_wise_task[$row[template_id]]  as $task_id=>$row_task)
			{  
				 
				 // print_r($to_process_task);die;
				 
				if($to_process_task[$row[po_id]][$row_task[task_name]]!="")
				{
					if ($tna_process_type==1)
					{ 
						if($db_type==0) $target_date=add_date($row[shipment_date] ,- $row_task[deadline]);
						else $target_date=change_date_format(trim(add_date($row[shipment_date] ,- $row_task['deadline'])),'','',1);
						 
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
						 
						if($db_type==0) $target_date=add_date($row[po_received_date] , $row_task[execution_days]);
						else $target_date=change_date_format(trim(add_date($row[po_received_date] ,$row_task['execution_days'])),'','',1);
						 
						//$to_add_days=$row_task['execution_days']-1;
						if($db_type==0) $start_date=add_date($row[po_received_date] ,$row_task[deadline]);
						else $start_date=change_date_format(trim(add_date($row[po_received_date] ,$row_task[deadline])),'','',1);
						 
						$finish_date=$target_date;
						$to_add_days=$row_task['notice_before'];
						
						if($db_type==0) $notice_date_start=add_date($start_date ,-$to_add_days);
						else $notice_date_start=change_date_format(trim(add_date($start_date ,-$to_add_days)),'','',1);
						 
						
						if($db_type==0) $notice_date_end=add_date($finish_date ,-$to_add_days);
						else $notice_date_end=change_date_format(trim(add_date($finish_date ,-$to_add_days)),'','',1);
					}
					

					
					if( $tna_process_list[$row[po_id]][$row_task[task_name]]=="") 
					{ 
						if ($mst_id=="") $mst_id=return_next_id( "id", "tna_process_mst"); else $mst_id+=1;
						if ($data_array_tna_process!="") $data_array_tna_process .=",";
						 
						
						if($tna_updated_date[$row[po_id]][$row_task[task_name]]['planstart'] =='0000-00-00') $tna_updated_date[$row[po_id]][$row_task[task_name]]['planstart'] ='';
						if($tna_updated_date[$row[po_id]][$row_task[task_name]]['planfinish'] =='0000-00-00') $tna_updated_date[$row[po_id]][$row_task[task_name]]['planfinish'] ='';
						
						if( $tna_updated_date[$row[po_id]][$row_task[task_name]]['planstart'] !='' ) $start_date=$tna_updated_date[$row[po_id]][$row_task[task_name]]['planstart'];
						if( $tna_updated_date[$row[po_id]][$row_task[task_name]]['planfinish'] !='' ) $finish_date=$tna_updated_date[$row[po_id]][$row_task[task_name]]['planfinish'];
						
						
						$plan_start_flag=$tna_updated_date[$row[po_id]][$row_task[task_name]]['planstartflag']*1;
						$plan_finish_flag=$tna_updated_date[$row[po_id]][$row_task[task_name]]['planfinishflag']*1;
						
						
						$data_array_tna_process .="('$mst_id','$row[template_id]','$row[job_no_mst]','$row[po_id]','$row[po_received_date]','$row[shipment_date]','1','$row_task[task_name]','$target_date','$start_date','$finish_date','$notice_date_start','$notice_date_end','$date','$row_task[sequence_no]',$plan_start_flag,$plan_finish_flag,1,0,3)";
						
						$insert_string[] ="('$mst_id','$row[template_id]','$row[job_no_mst]','$row[po_id]','$row[po_received_date]','$row[shipment_date]','1','$row_task[task_name]','$target_date','$start_date','$finish_date','$notice_date_start','$notice_date_end','$date','$row_task[sequence_no]',$plan_start_flag,$plan_finish_flag,1,0,3)";
						
						
					}
					else
					{ 	
					
					
						if ( ($tna_task_update_data[$row[po_id]][$row_task[task_name]]['min_start_date']=="0000-00-00" || $tna_task_update_data[$row[po_id]][$row_task[task_name]]['min_start_date']=="") && ($tna_task_update_data[$row[po_id]][$row_task[task_name]]['max_start_date']!="0000-00-00" || $tna_task_update_data[$row[po_id]][$row_task[task_name]]['max_start_date']!="" ) )
						{  
							$tna_task_update_data[$row[po_id]][$row_task[task_name]]['min_start_date']= $tna_task_update_data[$row[po_id]][$row_task[task_name]]['max_start_date'];
						}
						
						if ( $tna_task_update_data[$row[po_id]][$row_task[task_name]]['min_start_date']!="0000-00-00" || $tna_task_update_data[$row[po_id]][$row_task[task_name]]['min_start_date']!="" ) $start_date=$tna_task_update_data[$row[po_id]][$row_task[task_name]]['min_start_date']; else $start_date="0000-00-00";
						if ( $tna_task_update_data[$row[po_id]][$row_task[task_name]]['max_start_date']!="0000-00-00" || $tna_task_update_data[$row[po_id]][$row_task[task_name]]['max_start_date']!="" ) $finish_date=$tna_task_update_data[$row[po_id]][$row_task[task_name]]['max_start_date']; else $finish_date="0000-00-00";
						
						//if(!in_array($row_task[task_name],$approval_array))
						if($approval_array[$row_task[task_name]]=='')
						{
							
							$compl_perc=get_percent($tna_task_update_data[$row[po_id]][$row_task[task_name]]['doneqnty'], $tna_task_update_data[$row[po_id]][$row_task[task_name]]['reqqnty']); 
							
							if($compl_perc<$row_task[completion_percent])
							{
								$finish_date=$blank_date;
							}
							
							
						}
						else
						{
							if( $tna_task_update_data[$row[po_id]][$row_task[task_name]]['noofapproved']!=$tna_task_update_data[$row[po_id]][$row_task[task_name]]['noofval']) $finish_date=$blank_date; //"0000-00-00";
						
						
						}
						
	
	
	
			
	
						$process_id_up_array[]=$tna_process_list[$row[po_id]][$row_task[task_name]];
						
						if($tna_updated_date[$row[po_id]][$row_task[task_name]]['start'] =='0000-00-00') $tna_updated_date[$row[po_id]][$row_task[task_name]]['start'] ='';
						if($tna_updated_date[$row[po_id]][$row_task[task_name]]['finish'] =='0000-00-00') $tna_updated_date[$row[po_id]][$row_task[task_name]]['finish'] ='';
						
						if( $tna_updated_date[$row[po_id]][$row_task[task_name]]['start'] !='' ) $start_date=$tna_updated_date[$row[po_id]][$row_task[task_name]]['start'];
						if( $tna_updated_date[$row[po_id]][$row_task[task_name]]['finish'] !='' ) $finish_date=$tna_updated_date[$row[po_id]][$row_task[task_name]]['finish'];
						
						$data_array_tna_process_up[$tna_process_list[$row[po_id]][$row_task[task_name]]] =explode(",",("'".$start_date."','".$finish_date."'")); 
					}
				} // To Process Task List check
			}
		}
		
		$file = 'tna_log.txt';
		$current = file_get_contents($file);
		$current .= "TNA-PROCESS:: Company ID: ".$cbo_company.", Date and Time: ".date("d-m-Y H:i:s",time())."\n";
	 	file_put_contents($file, $current);
		
	}

	 
	// print_r($data_array_tna_process_up);die; 
	 
	 
	 
	
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
			$tna_pro_array=array_chunk($insert_string,2);
			foreach($tna_pro_array as $dd=>$tna_pro_list)
			{
				$rID=sql_insert("tna_process_mst",$field_array_tna_process,implode(",",$tna_pro_list),1);
				oci_commit($con); 
			}
			
		}
		
		if(count($process_id_up_array>0)) 
		{
			$data_array_tna_up=array_chunk($data_array_tna_process_up,50,true);//print_r($data_array_tna_up[1]);die;
			$id_up_array=array_chunk($process_id_up_array,50,true);
			$count=count($id_up_array);
			for ($i=0;$i<=$count;$i++)
		 	{
				
				$rID_up=execute_query(bulk_update_sql_statement( "tna_process_mst", "id", $field_array_tna_process_up, $data_array_tna_up[$i], array_values($id_up_array[$i] )),1);
			}
			
			oci_commit($con);
		}
	
	}


//.....................................................................................
	
	disconnect($con);
	echo "0**".$rID."**".implode(", ",$template_missing_po);
	//echo "INSERT INTO pro_garments_production_dtls (".$field_array.") VALUES ".$data_array;die;
	
	
}//end tna_process;




// Always treat the lowest template ... if not no process on that
function get_tna_template( $remain_days, $tna_template, $buyer ) 
{
	global $tna_template_buyer;
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
					
				//return $i."ss".$tna_template[$i-1]['id'];
				/*if ($i!=0)
				{
					$up_day=$tna_template[$i]['lead']-$remain_days;
					$low_day=$remain_days-$tna_template[$i-1]['lead'];
					if ($up_day>=$low_day)
						return $tna_template[$i-1]['id'];
					else
						return $tna_template[$i]['id'];
				}
				else
				{
					return $tna_template[$i]['id'];
				}*/
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
  	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	extract($_REQUEST);  
?>
     
<script>
	function js_set_value(str, str1)
	{
		$("#selected_job").val(str+"__"+str1);
		parent.emailwindow.hide(); 
	}
</script>
</head>
<body>


</head>

<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="1000" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
    	<tr>
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                        <thead>
                         <th width="150" colspan="3"> </th>
                        	<th>
                              <?
                               echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" );
                              ?>
                            </th>
                          <th width="150" colspan="3"> </th>
                        </thead>
                    <thead>                	 
                        <th width="150">Company Name</th>
                        <th width="150">Buyer Name</th>
                        <th width="80">Job No</th>
                        <th width="100">Style Ref </th>
                        <th width="120">Order No</th>
                        <th width="200">Ship Date Range</th>
                        <th><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">Job Without PO</th>           
                    </thead>
        			<tr>
                    	<td> 
                        <input type="hidden" id="selected_job">
                        <input type="hidden" id="garments_nature" value="<? echo $garments_nature; ?>">
							<? 
								echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company,"load_drop_down( 'tna_process_sweater_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
							?>
                    </td>
                   	<td id="buyer_td">
                     <? 
						echo create_drop_down( "cbo_buyer_name", 172, $blank_array,'', 1, "-- Select Buyer --");
					?>
                    </td>
                    <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:80px"></td>
                    <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:100px"></td>
                    <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:120px"></td>
                    <td>
                    	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
					  	<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					</td> 
            		<td align="center">
                     <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value, 'ponumber_search_list_view', 'search_div', 'tna_process_sweater_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
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
            </td>
        </tr>
        <tr>
            <td align="center" valign="top" id="search_div"></td>
        </tr>
    </table>    
    </form>
   </div>
   
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	var buyer='<? echo $buyer; ?>';
	load_drop_down( 'tna_process_sweater_controller', <? echo $company;?>, 'load_drop_down_buyer', 'buyer_td' );
	document.getElementById('cbo_buyer').value=buyer;
</script>
</html>
<?
exit();
}

if ($action=="ponumber_search_list_view")
{
 list($company,$buyer,$start_date,$end_date,$job_no,$year,$surch_by,$order_no,$style_no)=explode('_',$data);



 if($buyer==0 && $job_no=='' && $style_no=='' && $order_no=='' && ($start_date=='' and $end_date=='')){
	 exit('<h1 style="color:blue;">Please Select Date Range.</h1>');
 }
 
 
 $company_array=return_library_array( "select id, company_name from lib_company",'id','company_name');
 $buyer_array=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
 if($buyer!=0) $buyer_con="and a.buyer_name='$buyer'"; else $buyer_con="";
 if($surch_by==1)
 {
	 if($job_no!="") $job_no_con="and a.job_no='".trim($job_no)."'"; else $job_no_con="";
	 if($order_no!="") $order_no_con="and b.po_number='".trim($order_no)."'"; else $order_no_con="";
	 if($style_no!="") $style_no_con="and a.style_ref_no='".trim($style_no)."'"; else $style_no_con="";
 }
 else if($surch_by==2)
 {
	 if($job_no!="") $job_no_con="and a.job_no like '".trim($job_no)."%'"; else $job_no_con="";
	 if($order_no!="") $order_no_con="and b.po_number like '".trim($order_no)."%'"; else $order_no_con="";
	 if($style_no!="") $style_no_con="and a.style_ref_no like '".trim($style_no)."%'"; else $style_no_con="";
 }
 else if($surch_by==3)
 {
	 if($job_no!="") $job_no_con="and a.job_no like '%".trim($job_no)."'"; else $job_no_con="";
	 if($order_no!="") $order_no_con="and b.po_number like '%".trim($order_no)."'"; else $order_no_con="";
	 if($style_no!="") $style_no_con="and a.style_ref_no like '%".trim($style_no)."'"; else $style_no_con="";
 }
 else if($surch_by==4 || $surch_by==0)
 {
	 if($job_no!="") $job_no_con="and a.job_no like '%".trim($job_no)."%'"; else $job_no_con="";
	 if($order_no!="") $order_no_con="and b.po_number like '%".trim($order_no)."%'"; else $order_no_con="";
	 if($style_no!="") $style_no_con="and a.style_ref_no like '%".trim($style_no)."%'"; else $style_no_con="";
 }
 
	$start_date=str_replace("'","",$start_date);
	$end_date=str_replace("'","",$end_date); 
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
		$date_cond  = " and b.pub_shipment_date between'".change_date_format($start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'";
		}
		
		if($db_type==2)
		{
		$date_cond  = " and b.pub_shipment_date between'".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-",1)."'";
		}
	}
	else
	{
	$date_cond  = "";	
	}
 ?>
        <table width="802" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="">
            <thead>
                <th width="40" height="34">SL</th>
                <th width="130">Company Name</th>
                <th width="130">Buyer Name</th>
                <th width="130">PO Number/Style Reff</th>
                <th width="110">Po Receive Date</th>
                <th width="150">Publish Shipment Date</th>
                <th>Lead Time</th>
            </thead>
        </table>
        <div style="width:802px; max-height:250px; overflow-y:scroll"> 
        <table width="780" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_search">
		<?
			if($db_type==0) $lead_time="DATEDIFF(b.pub_shipment_date,b.po_received_date) as  date_diff";
			if($db_type==2) $lead_time="(b.pub_shipment_date-b.po_received_date) as  date_diff";
			
			$sql="select a.company_name,a.buyer_name,b.po_number,b.id,b.po_received_date,b.pub_shipment_date,$lead_time from   wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company' and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $job_no_con $buyer_con $order_no_con $style_no_con $date_cond";
			$sql_result=sql_select($sql);
			$i=1;
			foreach($sql_result as $row){
			$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
			?>
              <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<?  echo $row[csf("po_number")];  ?>',<?  echo $row[csf("id")];  ?>)" style="cursor:pointer;">
                   <td width="40" align="center" ><? echo $i; ?></td>
                   <td width="130" align="center"><? echo $company_array[$row[csf("company_name")]]; ?></td>
                   <td width="130" align="center"><? echo $buyer_array[$row[csf("buyer_name")]]; ?></td>
                   <td width="130" align="center" id="txt_style_reff" ><? echo $row[csf("po_number")]; ?></td>
                   <td width="110" align="center" id="txt_style_reff" ><? echo change_date_format($row[csf("po_received_date")]); ?></td>
                   <td width="150" align="center" id="txt_style_reff" ><? echo change_date_format($row[csf("pub_shipment_date")]); ?></td>
                   <td  align="center" id="txt_style_reff" ><? echo $row[csf("date_diff")]; ?></td>
              </tr>
    	<?
            $i++;   
        }
        ?>
        </table>
    </div>
<?       
exit();
}















?>


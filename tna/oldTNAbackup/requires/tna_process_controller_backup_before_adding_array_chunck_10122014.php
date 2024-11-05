<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../includes/common.php');


extract ($_REQUEST);

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer", 170,"select a.id,a.buyer_name from  lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0","id,buyer_name", 1, "-- Select Buyer --", 0, "" );
	die;
}

include('../../order/woven_order/bom_process_ship_date.php');

// Variable Settings List   $variable_settings[$row[csf('company_name')]]
	$sql = "SELECT company_name,tna_integrated FROM  variable_order_tracking WHERE status_active =1 and is_deleted = 0 and variable_list=14";
	$result = sql_select( $sql );
	$variable_settings = array();
	foreach( $result as $row ) 
	{		
		$variable_settings[$row[csf('company_name')]] = $row[csf('tna_integrated')];
	}
	$sql = "SELECT task_name,completion_percent FROM  lib_tna_task WHERE is_deleted = 0 and status_active=1 order by task_name asc";
	$result = sql_select( $sql );
 
	foreach( $result as $row ) 
	{
		$tna_completion[$row[csf('task_name')]]=$row[csf('completion_percent')];
		 
	}
	
if($tna_process_type==2)
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
		$tna_task_percent[$row[csf('task_id')]]['completion_percent']=$tna_completion[$row['task_id']];
		
		$tna_task_percent_buyer_wise[$row[csf('buyer_id')]]=$row[csf('buyer_id')];
		$tna_task_percent_buyer[$row[csf('buyer_id')]][$row[csf('task_id')]]['task_name']=$row[csf('task_id')];
		$tna_task_percent_buyer[$row[csf('buyer_id')]][$row[csf('task_id')]]['buyer_id']=$row[csf('buyer_id')];
		$tna_task_percent_buyer[$row[csf('buyer_id')]][$row[csf('task_id')]]['start_percent']=$row[csf('start_percent')];
		$tna_task_percent_buyer[$row[csf('buyer_id')]][$row[csf('task_id')]]['end_percent']=$row[csf('end_percent')];
		$tna_task_percent_buyer[$row[csf('buyer_id')]][$row[csf('task_id')]]['notice_before']=$row[csf('notice_before')];
		$tna_task_percent_buyer[$row[csf('buyer_id')]][$row[csf('task_id')]]['completion_percent']=$tna_completion[$row[csf("task_name")]];
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

//Template Details
	$sql_task = "SELECT a.id,task_template_id,lead_time,material_source,total_task,tna_task_id,deadline,execution_days,notice_before,sequence_no,for_specific,b.task_catagory,b.task_name,b.task_type FROM  tna_task_template_details a, lib_tna_task b WHERE   a.tna_task_id=b.task_name and b.is_deleted=0 and b.status_active=1 order by for_specific,lead_time ";
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
		if (!in_array($row[csf("task_template_id")],$template))
		{
			$template[]=$row[csf("task_template_id")];
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
	//print_r($template_wise_task); die;
	
if ( $action=="tna_process" )
{
	if( $db_type==0 ) $blank_date="0000-00-00"; else $blank_date=""; 
	//echo "delete FROM tna_process_mst WHERE  po_number_id in ( $txt_ponumber_id )"; die;
	// Reprocess Check
	if (trim($txt_ponumber_id)=="") $strcond=""; 
	else 
	{
		if( $is_delete==1 ) $rid=execute_query("delete FROM tna_process_mst WHERE  po_number_id in ( $txt_ponumber_id )",1); 
		 
	}
	if( $cbo_buyer>0 ) $buyer_cond=" and a.buyer_name=$cbo_buyer ";
	//$job_nos ="'ASL-13-00191'";
 
	if ( $txt_ponumber_id=="" )
	{
		if($db_type==0)
		{
			$sql = "SELECT (pub_shipment_date) as shipment_date,job_no_mst,po_received_date,b.id,po_quantity,a.buyer_name,a.garments_nature,b.po_number FROM wo_po_break_down b, wo_po_details_master a WHERE b.is_deleted = 0 and b.status_active=1 and a.job_no=b.job_no_mst and b.shipment_date!='0000-00-00' and b.po_received_date!='0000-00-00' $buyer_cond and b.shipment_date>'$tna_process_start_date'  ORDER BY b.shipment_date asc";
		}
		else
		{									
			$sql = "SELECT (pub_shipment_date) as shipment_date,job_no_mst,po_received_date,b.id,po_quantity,a.buyer_name,a.garments_nature,b.po_number FROM wo_po_break_down b, wo_po_details_master a WHERE b.is_deleted = 0 and b.status_active=1 and a.job_no=b.job_no_mst and to_char(b.shipment_date)!='0000-00-00' and to_char(b.po_received_date)!='0000-00-00' $buyer_cond   and to_char(b.shipment_date)>'$tna_process_start_date' ORDER BY b.shipment_date asc";
		}
	}
	else
	{
		if($db_type==0)
		{
			$sql = "SELECT (pub_shipment_date) as shipment_date,job_no_mst,po_received_date,b.id,po_quantity,a.buyer_name,a.garments_nature,b.po_number FROM wo_po_break_down b, wo_po_details_master a WHERE b.is_deleted = 0 and b.status_active=1 and a.job_no=b.job_no_mst and b.shipment_date!='0000-00-00' and b.po_received_date!='0000-00-00' and b.id  in ( $txt_ponumber_id )  and b.shipment_date>'$tna_process_start_date'  $buyer_cond  ORDER BY b.shipment_date asc";
		}
		else
		{
			$sql = "SELECT (pub_shipment_date) as shipment_date,job_no_mst,po_received_date,b.id,po_quantity,a.buyer_name,a.garments_nature,b.po_number FROM wo_po_break_down b, wo_po_details_master a WHERE b.is_deleted = 0 and b.status_active=1 and a.job_no=b.job_no_mst and to_char(b.shipment_date)!='0000-00-00' and to_char(b.po_received_date)!='0000-00-00' and b.id  in ( $txt_ponumber_id )  and to_char(b.shipment_date)>'$tna_process_start_date' $buyer_cond  ORDER BY b.shipment_date asc";
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
	$i=0;
	foreach($data_array as $row)
	{
		$remain_days=datediff( "d", date("Y-m-d",strtotime($row[csf("po_received_date")])), date("Y-m-d",strtotime($row[csf("shipment_date")])) );
		
		if ($tna_process_type==1)
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
		
		if ( $template_id=="" || $template_id==0 )
		{
			$template_missing_po[]=$row[csf("po_number")];
		} 
		else
		{
			if (!in_array( $row[csf("job_no_mst")],$job_no_array)) $job_no_array[]= $row[csf("job_no_mst")] ;
			$order_id_array[$i]=$row[csf("id")];
			$po_order_template[$row[csf("id")]]=  $template_id; 
			$po_order_details[$row[csf("id")]]['po_received_date']=$row[csf("po_received_date")];
			$po_order_details[$row[csf("id")]]['shipment_date']=$row[csf("shipment_date")];
			$po_order_details[$row[csf("id")]]['job_no_mst']=$row[csf("job_no_mst")];
			$po_order_details[$row[csf("id")]]['po_quantity']=$row[csf("po_quantity")];
			$po_order_details[$row[csf("id")]]['template_id']=$template_id;
			$po_order_details[$row[csf("id")]]['po_id']=$row[csf("id")];
			//$to_process_task
			$job_nature[$row[csf('job_no')]] = $row[csf('garments_nature')];
			foreach($tna_common_task_name_to_process as $vid=>$vtask)
			{
				$to_process_task[$row[csf("id")]][$vid]=$vid;
			}
			
			 
			$i++;
		}
	}
	$po_ids=implode(",",$order_id_array);
	$job_no_list="'".implode("','",$job_no_array)."'";
	
	 
	
	
// TNA Processs Data List for Update      TNA UPDATE
	$sql = "SELECT id,job_no,po_number_id,task_category,task_number,actual_start_date,actual_finish_date FROM   tna_process_mst WHERE job_no in ( $job_no_list ) and status_active =1 and is_deleted = 0";
	
	$result = sql_select( $sql );
	$tna_process_list = array();
	$tna_process_details = array();
	foreach( $result as $row ) 
	{
		$tna_process_list[$row[csf('po_number_id')]][$row[csf('task_number')]]= $row[csf('id')];
		$tna_process_details[$row[csf('id')]]['start']=$row[csf('actual_start_date')];
		$tna_process_details[$row[csf('id')]]['finish']=$row[csf('actual_finish_date')];
	}
 
// Sample Approval Update Data
	if($db_type==0)
	{
		$sql_task = "SELECT count(a.id) as cid,po_break_down_id,sample_type,max(submitted_to_buyer) as max_submitted_to_buyer,min(submitted_to_buyer) as min_submitted_to_buyer,max(approval_status_date) as max_approval_status_date,min(approval_status_date) as min_approval_status_date
	 FROM wo_po_sample_approval_info  a, lib_sample b WHERE job_no_mst in ( $job_no_list ) and sample_type in (2,3,4,7,8,9) and b.id=a.SAMPLE_TYPE_ID  and a.is_deleted = 0 and a.status_active=1 group by sample_type,po_break_down_id order by po_break_down_id asc, approval_status desc";
	}
	else
	{
		$sql_task = "SELECT count(a.id) as cid,po_break_down_id,sample_type,max(submitted_to_buyer) as max_submitted_to_buyer,min(submitted_to_buyer) as min_submitted_to_buyer,max(approval_status_date) as max_approval_status_date,min(approval_status_date) as min_approval_status_date
	 FROM wo_po_sample_approval_info  a, lib_sample b WHERE job_no_mst in ( $job_no_list ) and sample_type in (2,3,4,7,8,9) and b.id=a.SAMPLE_TYPE_ID   and a.is_deleted = 0 and a.status_active=1 group by sample_type,po_break_down_id order by po_break_down_id asc"; 
	}
  	
	$result = sql_select( $sql_task );
	$sample_approval_update = array(); 
	foreach( $result as $row ) 
	{
		 $sub=0;  $appr=0;
		 
		if ($row[csf("sample_type")]==2) { $sub=8;  $appr=12; }
		else if ($row[csf("sample_type")]==3) { $sub=7;  $appr=13;  }
		else if ($row[csf("sample_type")]==4) { $sub=14;  $appr=15; }
		else if ($row[csf("sample_type")]==7) { $sub=16;  $appr=17; }
		else if ($row[csf("sample_type")]==8) { $sub=21;  $appr=22; }
		else if ($row[csf("sample_type")]==9) { $sub=23;  $appr=24; }
		
		$to_process_task[$row[csf("po_break_down_id")]][$appr]=$appr;
		$to_process_task[$row[csf("po_break_down_id")]][$sub]=$sub;
		
		$tna_task_update_data[$row[csf("po_break_down_id")]][$appr]['max_start_date']=$row[csf("max_approval_status_date")];
		$tna_task_update_data[$row[csf("po_break_down_id")]][$appr]['min_start_date']=$row[csf("min_approval_status_date")];
		$tna_task_update_data[$row[csf("po_break_down_id")]][$appr]['noofval']=$row[csf("cid")];	
		
		$tna_task_update_data[$row[csf("po_break_down_id")]][$sub]['max_start_date']=$row[csf("max_submitted_to_buyer")];
		$tna_task_update_data[$row[csf("po_break_down_id")]][$sub]['min_start_date']=$row[csf("min_submitted_to_buyer")];
		$tna_task_update_data[$row[csf("po_break_down_id")]][$sub]['noofval']=$row[csf("cid")];				 
	}
	
	
	if($db_type==0)
	{
		$sql_task = "SELECT count(a.id) as cid,po_break_down_id,sample_type	 FROM wo_po_sample_approval_info  a, lib_sample b WHERE job_no_mst in ( $job_no_list ) and sample_type in (2,3,4,7) and b.id=a.SAMPLE_TYPE_ID and approval_status_date!='0000-00-00'  and a.is_deleted = 0 and a.status_active=1 group by sample_type,po_break_down_id order by po_break_down_id asc";
	}
	else
	{
		$sql_task = "SELECT count(a.id) as cid,po_break_down_id,sample_type	 FROM wo_po_sample_approval_info  a, lib_sample b WHERE job_no_mst in ( $job_no_list ) and sample_type in (2,3,4,7) and b.id=a.SAMPLE_TYPE_ID and approval_status_date is not null  and a.is_deleted = 0 and a.status_active=1 group by sample_type,po_break_down_id order by po_break_down_id asc"; 
	}
	// echo $sql_task;
	$result = sql_select( $sql_task );
	$sample_approval_update = array(); 
	foreach( $result as $row ) 
	{
		 $sub=0;  $appr=0;
	 	if ($row[csf("sample_type")]==2) { $sub=8;  $appr=12; }
		else if ($row[csf("sample_type")]==3) { $sub=7;  $appr=13; }
		else if ($row[csf("sample_type")]==4) { $sub=14;  $appr=15; }
		else if ($row[csf("sample_type")]==7) { $sub=16;  $appr=17; }
		
		$tna_task_update_data[$row[csf("po_break_down_id")]][$sub]['noofapproved']=$row[csf("cid")];	
		$tna_task_update_data[$row[csf("po_break_down_id")]][$appr]['noofapproved']=$row[csf("cid")];
	}

// LABDIP Approval Data for Update
	if($db_type==0)
	{
		$sql_task = "SELECT count(id) as cid,po_break_down_id,max(submitted_to_buyer) as max_submitted_to_buyer,min(submitted_to_buyer) as min_submitted_to_buyer,max(approval_status_date) as max_approval_status_date,min(approval_status_date) as min_approval_status_date
	 FROM wo_po_lapdip_approval_info WHERE job_no_mst in ( $job_no_list ) and is_deleted = 0 and status_active=1 group by po_break_down_id order by job_no_mst,po_break_down_id asc";
	}
	else
	{
		$sql_task = "SELECT count(id) as cid,po_break_down_id,max(submitted_to_buyer) as max_submitted_to_buyer,min(submitted_to_buyer) as min_submitted_to_buyer,max(approval_status_date) as max_approval_status_date,min(approval_status_date) as min_approval_status_date
	 FROM wo_po_lapdip_approval_info WHERE job_no_mst in ( $job_no_list ) and is_deleted = 0 and status_active=1 group by po_break_down_id order by po_break_down_id asc";
	}
	
	$result = sql_select( $sql_task );
	$labdip_update_task = array(); 
	foreach( $result as $row ) 
	{
		$to_process_task[$row[csf("po_break_down_id")]][9]=9;
		$to_process_task[$row[csf("po_break_down_id")]][10]=10;
		
		$tna_task_update_data[$row[csf("po_break_down_id")]][10]['max_start_date']=$row[csf("max_approval_status_date")];
		$tna_task_update_data[$row[csf("po_break_down_id")]][10]['min_start_date']=$row[csf("min_approval_status_date")];
		$tna_task_update_data[$row[csf("po_break_down_id")]][10]['noofval']=$row[csf("cid")];
		
		$tna_task_update_data[$row[csf("po_break_down_id")]][9]['max_start_date']=$row[csf("max_submitted_to_buyer")];
		$tna_task_update_data[$row[csf("po_break_down_id")]][9]['min_start_date']=$row[csf("min_submitted_to_buyer")];
		$tna_task_update_data[$row[csf("po_break_down_id")]][9]['noofval']=$row[csf("cid")];	
	}
	if($db_type==0)
	{
		$sql_task = "SELECT count(id) as cid,po_break_down_id FROM wo_po_lapdip_approval_info WHERE job_no_mst in ( $job_no_list ) and approval_status_date!='0000-00-00' and is_deleted = 0 and status_active=1 group by po_break_down_id order by po_break_down_id";
	}
	else
	{
		$sql_task = "SELECT count(id) as cid,po_break_down_id FROM wo_po_lapdip_approval_info WHERE job_no_mst in ( $job_no_list )and approval_status_date is not null  and is_deleted = 0 and status_active=1 group by po_break_down_id order by po_break_down_id asc"; 
	}
	$result = sql_select( $sql_task );
	$sample_approval_update = array(); 
	foreach( $result as $row ) 
	{
		$tna_task_update_data[$row[csf("po_break_down_id")]][10]['noofapproved']=$row[csf("cid")];	
		$tna_task_update_data[$row[csf("po_break_down_id")]][9]['noofapproved']=$row[csf("cid")];
	}
		
// Trims Approval Data for Update
	if($db_type==0)
	{
		$sql_task = "SELECT count(a.id) as cid,po_break_down_id,max(submitted_to_buyer) as max_submitted_to_buyer,min(submitted_to_buyer) as min_submitted_to_buyer,max(approval_status_date) as max_approval_status_date,min(approval_status_date) as min_approval_status_date,trim_type
FROM lib_item_group b, wo_po_trims_approval_info a
WHERE b.id=a.accessories_type_id and job_no_mst in ( $job_no_list ) and a.is_deleted = 0 and a.status_active=1 group by po_break_down_id,trim_type order by job_no_mst,po_break_down_id asc";
	}
	else
	{
		$sql_task = "SELECT count(a.id) as cid,po_break_down_id,max(submitted_to_buyer) as max_submitted_to_buyer,min(submitted_to_buyer) as min_submitted_to_buyer,max(approval_status_date) as max_approval_status_date,min(approval_status_date) as min_approval_status_date,trim_type
FROM lib_item_group b, wo_po_trims_approval_info a
WHERE b.id=a.accessories_type_id and job_no_mst in ( $job_no_list ) and a.is_deleted = 0 and a.status_active=1 group by po_break_down_id,trim_type order by po_break_down_id asc";
	}
	
	$result = sql_select( $sql_task );
	$trims_update_task = array(); 
	foreach( $result as $row ) 
	{		
		$to_process_task[$row[csf("po_break_down_id")]][11]=11;
		
		$tna_task_update_data[$row[csf("po_break_down_id")]][11]['max_start_date']=$row[csf("max_approval_status_date")];
		$tna_task_update_data[$row[csf("po_break_down_id")]][11]['min_start_date']=$row[csf("min_approval_status_date")];
		$tna_task_update_data[$row[csf("po_break_down_id")]][11]['noofval']=$row[csf("cid")];
	}
	if($db_type==0)
	{
		$sql_task = "SELECT count(a.id) as cid,po_break_down_id,trim_type FROM lib_item_group b, wo_po_trims_approval_info a
WHERE b.id=a.accessories_type_id and job_no_mst in ( $job_no_list ) and approval_status_date!='0000-00-00' and a.is_deleted = 0 and a.status_active=1 group by po_break_down_id,trim_type order by job_no_mst,po_break_down_id asc";
	}
	else
	{
		$sql_task = "SELECT count(a.id) as cid,po_break_down_id,trim_type FROM lib_item_group b, wo_po_trims_approval_info a
WHERE b.id=a.accessories_type_id and job_no_mst in ( $job_no_list ) and approval_status_date is not null  and a.is_deleted = 0 and a.status_active=1 group by po_break_down_id,trim_type order by po_break_down_id asc";
	}
	
	$result = sql_select( $sql_task );
	$sample_approval_update = array(); 
	foreach( $result as $row ) 
	{
		$tna_task_update_data[$row[csf("po_break_down_id")]][11]['noofapproved']=$row[csf("cid")];	
	}

// Embelishment Approval Data for Update
	if($db_type==0)
	{
		$sql_task = "SELECT count(id) as cid,po_break_down_id,max(submitted_to_buyer) as max_submitted_to_buyer,min(submitted_to_buyer) as min_submitted_to_buyer,max(approval_status_date) as max_approval_status_date,min(approval_status_date) as min_approval_status_date,embellishment_id
	 FROM wo_po_embell_approval WHERE job_no_mst in ( $job_no_list ) and is_deleted = 0 and status_active=1 group by po_break_down_id,embellishment_id order by job_no_mst,po_break_down_id asc";
	}
	else
	{
		$sql_task = "SELECT count(id) as cid,po_break_down_id,max(submitted_to_buyer) as max_submitted_to_buyer,min(submitted_to_buyer) as min_submitted_to_buyer,max(approval_status_date) as max_approval_status_date,min(approval_status_date) as min_approval_status_date,embellishment_id
	 FROM wo_po_embell_approval WHERE job_no_mst in ( $job_no_list ) and is_deleted = 0 and status_active=1 group by po_break_down_id,embellishment_id order by po_break_down_id asc";
	}
	
	$result = sql_select( $sql_task );
	$embelishment_update_task = array(); 
	foreach( $result as $row ) 
	{
		$to_process_task[$row[csf("po_break_down_id")]][19]=19;
		$to_process_task[$row[csf("po_break_down_id")]][20]=20;
		
		$tna_task_update_data[$row[csf("po_break_down_id")]][20]['max_start_date']=$row[csf("max_approval_status_date")];
		$tna_task_update_data[$row[csf("po_break_down_id")]][20]['min_start_date']=$row[csf("min_approval_status_date")];
		
		$tna_task_update_data[$row[csf("po_break_down_id")]][19]['max_start_date']=$row[csf("max_submitted_to_buyer")];
		$tna_task_update_data[$row[csf("po_break_down_id")]][19]['min_start_date']=$row[csf("min_submitted_to_buyer")];
		$tna_task_update_data[$row[csf("po_break_down_id")]][20]['noofval']=$row[csf("cid")];
		$tna_task_update_data[$row[csf("po_break_down_id")]][19]['noofval']=$row[csf("cid")];
	}
	if($db_type==0)
	{
		$sql_task = "SELECT count(id) as cid,po_break_down_id FROM wo_po_embell_approval WHERE job_no_mst in ( $job_no_list ) and approval_status_date!='0000-00-00' and is_deleted = 0 and status_active=1 group by po_break_down_id,embellishment_id order by job_no_mst,po_break_down_id asc";
	}
	else
	{
		$sql_task = "SELECT count(id) as cid,po_break_down_id FROM wo_po_embell_approval WHERE job_no_mst in ( $job_no_list ) and approval_status_date is not null   and is_deleted = 0 and status_active=1 group by po_break_down_id,embellishment_id order by po_break_down_id asc";
	}	
	
	$result = sql_select( $sql_task );
	$sample_approval_update = array(); 
	foreach( $result as $row ) 
	{
		$tna_task_update_data[$row[csf("po_break_down_id")]][19]['noofapproved']=$row[csf("cid")];	
		$tna_task_update_data[$row[csf("po_break_down_id")]][20]['noofapproved']=$row[csf("cid")];	
	}
		
	$sql="SELECT distinct (po_break_down_id) as  po_break_down_id,color_type_id FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b WHERE a.id=b.pre_cost_fabric_cost_dtls_id and b.cons>0 and a.color_type_id in (2,3,4,6,5) and b.po_break_down_id in ( $po_ids )"; 
	$data_array=sql_select($sql);
	foreach($data_array as $row)
	{
		if($row[csf("color_type_id")]==5) { $app=62; $sub=63; }
		else { $app=51; $sub=52; }
		$to_process_task[$row[csf("po_break_down_id")]][$app]=$app;
		$to_process_task[$row[csf("po_break_down_id")]][$sub]=$sub;
	}
	
	$sql="SELECT distinct (b.id) as  po_break_down_id, emb_name FROM wo_pre_cost_embe_cost_dtls a, wo_po_break_down b WHERE a.job_no=b.job_no_mst and b.id in ( $po_ids )";
 	$data_array=sql_select($sql);
	foreach($data_array as $row)
	{
		 
		
		if($row[csf("emb_name")]==3) { $app=89; $sub=90; }
		else if($row[csf("emb_name")]==1 || $row[csf("emb_name")]==2 ) { $app=85; $sub=85; }
		
		$to_process_task[$row[csf("po_break_down_id")]][$app]=$app;
		$to_process_task[$row[csf("po_break_down_id")]][$sub]=$sub;
	}
	
// order_quantity 	plan_cut_qnty 	kint_fin_fab_qnty 	kint_fin_fab_qnty_prod 	kint_fin_fab_qnty_purc 	kint_fin_fab_qnty_buysu 	kint_grey_fab_qnty 	kint_grey_fab_qnty_prod 	kint_grey_fab_qnty_purc 	kint_grey_fab_qnty_buysu 	kint_rate 	kint_amount 	woven_fin_fab_qnty 	woven_fin_fab_qnty_prod 	woven_fin_fab_qnty_purc 	woven_fin_fab_qnty_buysu 	woven_grey_fab_qnty 	woven_grey_fab_qnty_prod 	woven_grey_fab_qnty_purc 	woven_grey_fab_qnty_buysu 	woven_rate 	woven_amount 	yarn_qnty 	yarn_rate 	yarn_amount 	conv_qnty 	conv_rate 	conv_amount 	trim_qty 	trim_rate 	trim_amount 	emb_cons 	emb_rate 	emb_amount 	wash_cons 	wash_rate 	wash_amount 	commercial_rate 	commercial_amount 	commision_rate 	commision_amount 	lab_test_rate 	lab_test_amount 	inspection_rate 	inspection_amount 	cm_cost_rate 	cm_cost_amount 	freight_rate 	freight_amount 	currier_rate 	currier_amount 	certificate_rate 	certificate_amount 	common_oh_rate 	common_oh_amount
 
	$sql="SELECT po_break_down_id,sum(plan_cut_qnty) as plan_cut_qnty,sum(kint_fin_fab_qnty) as kint_fin_fab_qnty,sum(kint_grey_fab_qnty) as kint_grey_fab_qnty,sum(woven_fin_fab_qnty) as woven_fin_fab_qnty,sum(woven_grey_fab_qnty) as woven_grey_fab_qnty,sum(yarn_qnty) as yarn_qnty,sum(conv_qnty) as conv_qnty,sum(trim_qty) as trim_qty,sum(emb_cons) as emb_cons, sum(wash_cons) as wash_cons,sum(kint_grey_fab_qnty_prod) as kint_grey_fab_qnty_prod,sum(kint_fin_fab_qnty_prod) as kint_fin_fab_qnty_prod,sum(wash_cons) as wash_cons,sum(emb_cons) as emb_cons FROM wo_bom_process WHERE  po_break_down_id in ( $po_ids ) group by po_break_down_id";
 	$data_array=sql_select($sql);
	foreach($data_array as $row)
	{ 
		$tna_task_update_data[$row[csf("po_break_down_id")]][31]['reqqnty']=$row[csf("kint_grey_fab_qnty")];
		//$tna_task_update_data[$row[csf("po_break_down_id")]][32]['reqqnty']=$row[csf("trim_qty")];
		$tna_task_update_data[$row[csf("po_break_down_id")]][34]['reqqnty']=$row[csf("woven_grey_fab_qnty")];
		$tna_task_update_data[$row[csf("po_break_down_id")]][50]['reqqnty']=$row[csf("yarn_qnty")];
		//$tna_task_update_data[$row[csf("po_break_down_id")]][51]['reqqnty']=$row[csf("yarn_qnty")];
		$tna_task_update_data[$row[csf("po_break_down_id")]][60]['reqqnty']=$row[csf("kint_grey_fab_qnty_prod")];
		$tna_task_update_data[$row[csf("po_break_down_id")]][61]['reqqnty']=$row[csf("kint_grey_fab_qnty")];
		//$tna_task_update_data[$row[csf("po_break_down_id")]][62]['reqqnty']=$row[csf("aop")];
		$tna_task_update_data[$row[csf("po_break_down_id")]][64]['reqqnty']=$row[csf("kint_fin_fab_qnty_prod")];
		$tna_task_update_data[$row[csf("po_break_down_id")]][72]['reqqnty']=$row[csf("kint_grey_fab_qnty")];
		$tna_task_update_data[$row[csf("po_break_down_id")]][73]['reqqnty']=$row[csf("kint_fin_fab_qnty")];
		$tna_task_update_data[$row[csf("po_break_down_id")]][89]['reqqnty']=$row[csf("wash_cons")];
		$tna_task_update_data[$row[csf("po_break_down_id")]][90]['reqqnty']=$row[csf("wash_cons")];
		$tna_task_update_data[$row[csf("po_break_down_id")]][85]['reqqnty']=$row[csf("emb_cons")];
		//$tna_task_update_data[$row[csf("po_break_down_id")]][90]['reqqnty']=$row[csf("wash_cons")];
	}
	
	
	
// print_r($tna_task_update_data); die;
	//Purchase/Booking  Data for Update 2- FB, 4 -Trims, 12- Service
	if($db_type==0)
	{
		$sql_task = "SELECT b.po_break_down_id,  sum(wo_qnty) as tfb_qnty, sum(grey_fab_qnty) as gfb_qnty, min(a.booking_date) as start_date, max(a.booking_date) as end_date, a.item_category,a.booking_type
	FROM  wo_booking_mst a, wo_booking_dtls b WHERE b.po_break_down_id in ( $po_ids ) and a.booking_no=b.booking_no and a.item_category in ( 2,4,12,3 ) group by b.po_break_down_id,item_category order by b.po_break_down_id asc";
	}
	else
	{
		$sql_task = "SELECT b.po_break_down_id, sum(wo_qnty) as tfb_qnty, sum(grey_fab_qnty) as gfb_qnty, min(a.booking_date) as start_date, max(a.booking_date) as end_date, a.item_category,a.booking_type
	FROM  wo_booking_mst a, wo_booking_dtls b WHERE b.po_break_down_id in ( $po_ids ) and a.booking_no=b.booking_no and a.item_category in ( 2,4,12,3 ) group by b.po_break_down_id,item_category,a.booking_type order by b.po_break_down_id asc";
	}
 	
	$result = sql_select( $sql_task );
	$purchase_update_task = array(); 
	foreach( $result as $row ) 
	{ 
	 	$tsktype=0;  
		$qnty=0;
		if ($row[csf("item_category")]==2) { $tsktype=31; $qnty=$row[csf("gfb_qnty")]; }
		else if ($row[csf("item_category")]==3) { $tsktype=34; $qnty=$row[csf("gfb_qnty")]; }
		else if ($row[csf("item_category")]==4) { $tsktype=32; $qnty=$row[csf("tfb_qnty")]; }
		else if ($row[csf("item_category")]==12) { $tsktype=33; $qnty=$row[csf("tfb_qnty")]; }
		
		if( $row[csf("booking_type")]==4 && $row[csf("item_category")]==2) { $tsktype=30; $qnty=$row[csf("gfb_qnty")]; }
		
		$tna_task_update_data[$row[csf("po_break_down_id")]][$tsktype]['max_start_date']=$row[csf("end_date")];
		$tna_task_update_data[$row[csf("po_break_down_id")]][$tsktype]['min_start_date']=$row[csf("start_date")]; 
		$tna_task_update_data[$row[csf("po_break_down_id")]][$tsktype]['doneqnty']=$qnty;
	}
	//print_r($purchase_update_task); die;
// Inventory Update Data
	if($db_type==0)
	{  
		$sql = "SELECT b.po_breakdown_id,b.entry_form, min(a.transaction_date) mindate, max(a.transaction_date) maxdate, sum(quantity) as prod_qntry 
FROM inv_transaction a,  order_wise_pro_details b where  b.trans_id=a.id and b.entry_form in ( 2,7,22,37,3 ) and b.po_breakdown_id in ( $po_ids ) group by b.po_breakdown_id,b.entry_form order by b.po_breakdown_id";
	}
	else
	{
		$sql = "SELECT b.po_breakdown_id,b.entry_form, min(a.transaction_date) mindate, max(a.transaction_date) maxdate, sum(quantity) as prod_qntry 
FROM inv_transaction a,  order_wise_pro_details b where  b.trans_id=a.id and b.entry_form in (2,7,22,37,3 ) and b.po_breakdown_id in ( $po_ids ) group by b.po_breakdown_id,b.entry_form order by b.po_breakdown_id";
	}
	
	$inventory_transaction_update=array();
	$data_array=sql_select($sql);
	foreach($data_array as $row)
	{
		$tsktype=0;
		if ($row[csf("entry_form")]==2) $tsktype=72;
		else if ($row[csf("entry_form")]==7) $tsktype=73;
		else if ($row[csf("entry_form")]==22) $tsktype=72;
		else if ($row[csf("entry_form")]==37) $tsktype=73;
		else if ($row[csf("entry_form")]==3) $tsktype=50;
		
		$tna_task_update_data[$row[csf("po_breakdown_id")]][$tsktype]['max_start_date']=$row[csf("maxdate")];
		$tna_task_update_data[$row[csf("po_breakdown_id")]][$tsktype]['min_start_date']=$row[csf("mindate")];
		$tna_task_update_data[$row[csf("po_breakdown_id")]][$tsktype]['doneqnty']=$row[csf("prod_qntry")];
	}
	// print_r($inventory_transaction_update);die;
	if($db_type==0)
	{
		$sql = "SELECT b.po_breakdown_id, min(a.transaction_date) mindate, max(a.transaction_date) maxdate, sum(quantity) as prod_qntry,d.trim_type 
FROM inv_transaction a,  order_wise_pro_details b, product_details_master c , lib_item_group d
where a.prod_id=c.id and b.trans_id=a.id and c.item_group_id=d.id and d.trim_type in (1,2) and b.entry_form in ( 24 ) and b.po_breakdown_id in ( $po_ids ) group by b.po_breakdown_id,d.trim_type order by b.po_breakdown_id";
	}
	else
	{
		$sql = "SELECT b.po_breakdown_id, min(a.transaction_date) mindate, max(a.transaction_date) maxdate, sum(quantity) as prod_qntry,d.trim_type 
FROM inv_transaction a,  order_wise_pro_details b, product_details_master c , lib_item_group d
where a.prod_id=c.id and b.trans_id=a.id and c.item_group_id=d.id and d.trim_type in (1,2) and b.entry_form in ( 24 ) and b.po_breakdown_id in ( $po_ids ) group by b.po_breakdown_id,d.trim_type order by b.po_breakdown_id";
	} 
	
	
	$data_array=sql_select($sql);
	foreach($data_array as $row)
	{
		$entry=($row[csf("trim_type")] == 1 ? 70 : 71);
		
		$tna_task_update_data[$row[csf("po_breakdown_id")]][$entry]['max_start_date']=$row[csf("maxdate")];
		$tna_task_update_data[$row[csf("po_breakdown_id")]][$entry]['min_start_date']=$row[csf("mindate")];
		$tna_task_update_data[$row[csf("po_breakdown_id")]][$entry]['doneqnty']=$row[csf("prod_qntry")]; 
	}
	//echo "sumon"; print_r( $tna_task_percent ); die;
	
	 
// fabric_production_task Update Data 
	if($db_type==0)
	{ 
		$sql = "SELECT b.po_breakdown_id,b.entry_form, min(a.receive_date) mindate, max(a.receive_date) maxdate, sum(quantity) as prod_qntry 
FROM inv_receive_master a,  order_wise_pro_details b, pro_finish_fabric_rcv_dtls c where  c.mst_id=a.id and c.id=b.dtls_id and b.entry_form in ( 7 ) and b.po_breakdown_id in ( $po_ids ) group by b.po_breakdown_id,b.entry_form order by b.po_breakdown_id";
	}
	else
	{
		$sql = "SELECT b.po_breakdown_id,b.entry_form, min(a.receive_date) mindate, max(a.receive_date) maxdate, sum(quantity) as prod_qntry 
FROM inv_receive_master a,  order_wise_pro_details b, pro_finish_fabric_rcv_dtls c where  c.mst_id=a.id and c.id=b.dtls_id and b.entry_form in ( 7 ) and b.po_breakdown_id in ( $po_ids ) group by b.po_breakdown_id,b.entry_form order by b.po_breakdown_id";
	}
	
	$data_array=sql_select($sql);
	foreach($data_array as $row)
	{
		$tna_task_update_data[$row[csf("po_breakdown_id")]][64]['max_start_date']=$row[csf("maxdate")];
		$tna_task_update_data[$row[csf("po_breakdown_id")]][64]['min_start_date']=$row[csf("mindate")];
		$tna_task_update_data[$row[csf("po_breakdown_id")]][64]['doneqnty']=$row[csf("prod_qntry")]; 
		
	}
	
	if($db_type==0)
	{
		$sql = "SELECT b.po_breakdown_id,b.entry_form, min(a.receive_date) mindate, max(a.receive_date) maxdate, sum(quantity) as prod_qntry 
FROM inv_receive_master a,  order_wise_pro_details b, pro_grey_prod_entry_dtls c where  c.mst_id=a.id and c.id=b.dtls_id and b.entry_form in ( 2 ) and b.po_breakdown_id in ( $po_ids ) group by b.po_breakdown_id,b.entry_form order by b.po_breakdown_id";
	}
	else
	{
		$sql = "SELECT b.po_breakdown_id,b.entry_form, min(a.receive_date) mindate, max(a.receive_date) maxdate, sum(quantity) as prod_qntry 
FROM inv_receive_master a,  order_wise_pro_details b, pro_grey_prod_entry_dtls c where  c.mst_id=a.id and c.id=b.dtls_id and b.entry_form in ( 2 ) and b.po_breakdown_id in ( $po_ids ) group by b.po_breakdown_id,b.entry_form order by b.po_breakdown_id";
	}
	$data_array=sql_select($sql);
	foreach($data_array as $row)
	{
		$tna_task_update_data[$row[csf("po_breakdown_id")]][60]['max_start_date']=$row[csf("maxdate")];
		$tna_task_update_data[$row[csf("po_breakdown_id")]][60]['min_start_date']=$row[csf("mindate")];
		$tna_task_update_data[$row[csf("po_breakdown_id")]][60]['doneqnty']=$row[csf("prod_qntry")]; 
	}
	
	/*echo $sql = "SELECT b.po_breakdown_id,b.entry_form, min(a.received_date) mindate, max(a.received_date) maxdate, sum(quantity) as prod_qntry 
FROM  pro_dyeing_update_mst a,  order_wise_pro_details b, pro_dyeing_update_dtls c where  c.id=b.dtls_id and a.id=c.mst_id and b.entry_form in ( 6 ) and b.po_breakdown_id in ( $po_ids ) group by b.po_breakdown_id,b.entry_form order by b.po_breakdown_id";*/

	$sql="select b.po_id, sum(b.batch_qnty) as dye_qnty, min(c.process_end_date) mindate, max(c.process_end_date) maxdate from pro_batch_create_mst a,pro_batch_create_dtls b, pro_fab_subprocess c where a.id=b.mst_id and a.id=c.batch_id and c.load_unload_id=2 and c.entry_form=35 and a.batch_against<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_id in ( $po_ids ) and c.status_active=1 and c.is_deleted=0 group by b.po_id";
 	$data_array=sql_select($sql);
	foreach($data_array as $row)
	{
		$tna_task_update_data[$row[csf("po_id")]][61]['max_start_date']=$row[csf("maxdate")];
		$tna_task_update_data[$row[csf("po_id")]][61]['min_start_date']=$row[csf("mindate")];
		$tna_task_update_data[$row[csf("po_id")]][61]['doneqnty']=$row[csf("dye_qnty")]; 
	}
	
	
	
	

	
	/*if($db_type==0)
	{
		$sql=" SELECT c.po_id,6 as entry_form,sum(batch_qnty) as batch_weight, ";   
		$sql .=" min(CASE WHEN a.load_unload_id =1 THEN a.process_end_date END) AS process_start_date,  max(CASE WHEN a.load_unload_id =2 THEN a.process_end_date END) AS process_end_date";
		$sql .=" from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c where a.batch_id=b.id and b.id=c.mst_id and c.po_id in ($po_ids) group by c.po_id";
	}
	else
	{
		$sql=" SELECT c.po_id,6 as entry_form,sum(batch_qnty) as batch_weight, ";   
		$sql .=" min(CASE WHEN a.load_unload_id =1 THEN a.process_end_date END) AS process_start_date,  max(CASE WHEN a.load_unload_id =2 THEN a.process_end_date END) AS process_end_date";
		$sql .=" from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c where a.batch_id=b.id and b.id=c.mst_id and c.po_id in ($po_ids) group by c.po_id";
	}
	//echo "asw8_".$sql;die;
	$data_array=sql_select($sql);
	foreach($data_array as $row)
	{
		$tna_task_update_data[$row[csf("po_id")]][62]['max_start_date']=$row[csf("process_end_date")];
		$tna_task_update_data[$row[csf("po_id")]][62]['min_start_date']=$row[csf("process_start_date")];
		$tna_task_update_data[$row[csf("po_id")]][62]['quantity']=$row[csf("batch_weight")];
		 
	}
	*/

// Inspection Data for Update

	if($db_type==0)
	{
		$sql = "SELECT job_no,po_break_down_id,min(inspection_date) as mind,max(inspection_date) as maxd,sum(inspection_qnty) as sumtot FROM pro_buyer_inspection WHERE job_no in ( $job_no_list ) and status_active =1 and is_deleted = 0 group by po_break_down_id";
	}
	else
	{
		$sql = "SELECT po_break_down_id,min(inspection_date) as mind,max(inspection_date) as maxd,sum(inspection_qnty) as sumtot FROM pro_buyer_inspection WHERE job_no in ( $job_no_list ) and status_active =1 and is_deleted = 0 group by po_break_down_id";
	}
	
	$result = sql_select( $sql );
	$inspection_status_array = array();
	foreach( $result as $row ) 
	{
		$tna_task_update_data[$row[csf("po_break_down_id")]][101]['max_start_date']=$row[csf("maxd")];
		$tna_task_update_data[$row[csf("po_break_down_id")]][101]['min_start_date']=$row[csf("mind")];
		$tna_task_update_data[$row[csf("po_break_down_id")]][101]['doneqnty']=$row[csf("sumtot")];
		$tna_task_update_data[$row[csf("po_break_down_id")]][101]['reqqnty']=$po_order_details[$row[csf("po_break_down_id")]]['po_quantity'];
	}
	 
// Ex-factory Data for Update 

	if($db_type==0)
	{ 	
		$sql = "SELECT po_break_down_id,min(ex_factory_date) as mind,max(ex_factory_date) as maxd,sum(ex_factory_qnty) as sumtot FROM  pro_ex_factory_mst WHERE po_break_down_id in ( $po_ids ) and status_active =1 and is_deleted = 0 group by po_break_down_id";
	}
	else
	{
		$sql = "SELECT po_break_down_id,min(ex_factory_date) as mind,max(ex_factory_date) as maxd,sum(ex_factory_qnty) as sumtot FROM  pro_ex_factory_mst WHERE po_break_down_id in ( $po_ids ) and status_active =1 and is_deleted = 0 group by po_break_down_id";
	}
	
	$result = sql_select( $sql );
	$exfactory_status_array = array();
	foreach( $result as $row ) 
	{
		$tna_task_update_data[$row[csf("po_break_down_id")]][110]['max_start_date']=$row[csf("maxd")];
		$tna_task_update_data[$row[csf("po_break_down_id")]][110]['min_start_date']=$row[csf("mind")];
		$tna_task_update_data[$row[csf("po_break_down_id")]][110]['doneqnty']=$row[csf("sumtot")];
		$tna_task_update_data[$row[csf("po_break_down_id")]][110]['reqqnty']=$po_order_details[$row[csf("po_break_down_id")]]['po_quantity'];
	}
	
// Doc Submisiion	
	if($db_type==0)
	{
		$sql = "SELECT b.po_breakdown_id, min(a.submit_date) as mind,max(a.submit_date) as maxd,sum(b.current_invoice_qnty) as current_invoice_qnty
	FROM  com_export_doc_submission_mst a,com_export_invoice_ship_dtls b, com_export_doc_submission_invo c
	WHERE b.po_breakdown_id in ( $po_ids ) and a.id=c.doc_submission_mst_id and c.invoice_id=b.mst_id and b.current_invoice_qnty>0 group by b.po_breakdown_id
	 ";
	}
	else
	{
		$sql = "SELECT  b.po_breakdown_id, min(a.submit_date) as mind,max(a.submit_date) as maxd, sum(b.current_invoice_qnty) as current_invoice_qnty 
	FROM  com_export_doc_submission_mst a,com_export_invoice_ship_dtls b, com_export_doc_submission_invo c
	WHERE b.po_breakdown_id in ( $po_ids ) and a.id=c.doc_submission_mst_id and c.invoice_id=b.mst_id and b.current_invoice_qnty>0 group by b.po_breakdown_id
	 ";
	}
 	$result = sql_select( $sql );
	foreach( $result as $row ) 
	{	
		 
		$tna_task_update_data[$row[csf("po_breakdown_id")]][120]['max_start_date']=$row[csf("maxd")];
		$tna_task_update_data[$row[csf("po_breakdown_id")]][120]['min_start_date']=$row[csf("mind")];
		//$tna_task_update_data[$row[csf("po_breakdown_id")]][120]['quantity']=$row[csf("sumtot")];
		$tna_task_update_data[$row[csf("po_breakdown_id")]][120]['doneqnty']=$row[csf("current_invoice_qnty")];
		$tna_task_update_data[$row[csf("po_breakdown_id")]][120]['reqqnty']=$po_order_details[$row[csf("po_breakdown_id")]]['po_quantity'];
	}
	
// Realzn and invoice

	if($db_type==0)
	{
		$sql = "SELECT b.po_breakdown_id,max(d.received_date) maxd,min(d.received_date) mind, sum(b.current_invoice_qnty) as current_invoice_qnty FROM com_export_invoice_ship_dtls b, com_export_doc_submission_invo c, com_export_proceed_realization d WHERE c.invoice_id=b.mst_id and c.doc_submission_mst_id=d.invoice_bill_id and b.current_invoice_qnty>0 and  b.po_breakdown_id in ( $po_ids ) group by b.po_breakdown_id";
	}
	else
	{
		$sql = "SELECT b.po_breakdown_id,max(d.received_date) maxd,min(d.received_date) mind, sum(b.current_invoice_qnty) as current_invoice_qnty FROM com_export_invoice_ship_dtls b, com_export_doc_submission_invo c, com_export_proceed_realization d WHERE c.invoice_id=b.mst_id and c.doc_submission_mst_id=d.invoice_bill_id and b.current_invoice_qnty>0 and  b.po_breakdown_id in ( $po_ids ) group by b.po_breakdown_id";
	}
	 
	$result = sql_select( $sql );
	foreach( $result as $row ) 
	{
		$tna_task_update_data[$row[csf("po_breakdown_id")]][121]['max_start_date']=$row[csf("maxd")];
		$tna_task_update_data[$row[csf("po_breakdown_id")]][121]['min_start_date']=$row[csf("mind")];
		$tna_task_update_data[$row[csf("po_breakdown_id")]][121]['doneqnty']=$row[csf("current_invoice_qnty")];
		$tna_task_update_data[$row[csf("po_breakdown_id")]][121]['reqqnty']=$po_order_details[$row[csf("po_breakdown_id")]]['po_quantity'];
		//echo $po_order_details[$row[csf("po_breakdown_id")]]['po_quantity']; die;
	}
	
// Garments Production Data for Update  

	if($db_type==0)
	{		 
		$sql = "SELECT po_break_down_id, min(production_date) as mind,max(production_date) as maxd, production_type,sum(production_quantity) as production_quantity,embel_name FROM  pro_garments_production_mst  WHERE po_break_down_id in ( $po_ids )   group by po_break_down_id,production_type,embel_name";
	}
	else
	{
		$sql = "SELECT po_break_down_id, min(production_date) as mind,max(production_date) as maxd, production_type,sum(production_quantity) as production_quantity,embel_name FROM  pro_garments_production_mst  WHERE po_break_down_id in ( $po_ids )   group by po_break_down_id,production_type,embel_name";
	}
	//embel_name 	embel_type
	$result = sql_select( $sql );
	foreach( $result as $row ) 
	{
		$tsktype=0;
		if ($row[csf("production_type")]==1) $tsktype=84;
		else if ($row[csf("production_type")]==3) $tsktype=85;
		else if ($row[csf("production_type")]==5) $tsktype=86;
		else if ($row[csf("production_type")]==7) $tsktype=87;
		else if ($row[csf("production_type")]==8) $tsktype=88; 
		else if ($row[csf("production_type")]==10) $tsktype=87;
		
		if($row[csf("embel_name")]==3 && $row[csf("production_type")]==2) $tsktype=89;
		else if($row[csf("embel_name")]==3 && $row[csf("production_type")]==3) $tsktype=90;
		
		$tna_task_update_data[$row[csf("po_break_down_id")]][$tsktype]['max_start_date']=$row[csf("maxd")];
		$tna_task_update_data[$row[csf("po_break_down_id")]][$tsktype]['min_start_date']=$row[csf("mind")];
	//	$tna_task_update_data[$row[csf("po_break_down_id")]][$tsktype]['quantity']=$row[csf("production_quantity")];
		$tna_task_update_data[$row[csf("po_break_down_id")]][$tsktype]['doneqnty']=$row[csf("production_quantity")];
		$tna_task_update_data[$row[csf("po_break_down_id")]][$tsktype]['reqqnty']=$po_order_details[$row[csf("po_break_down_id")]]['po_quantity'];
	} 
  
 
	$field_array_tna_process="id,template_id,job_no,po_number_id,po_receive_date,shipment_date,task_category,task_number,target_date,task_start_date,task_finish_date,notice_date_start,notice_date_end,process_date,sequence_no,status_active,is_deleted";
	$field_array_tna_process_up="actual_start_date*actual_finish_date";
	 
	/// $tna_approval_task=explode(",","11,51"); // TASK ID'S TO BE PROCESSED BY STYLE OR JOB NUMBER
	  
	 
	//print_r($template_wise_task[30])."_*";die;
	$approval_array=array(0=>7,1=>8,2=>9,3=>10,4=>11,5=>12,6=>13,7=>14,8=>15,9=>16,10=>17,11=>19,12=>20);
	
	foreach( $po_order_details as $row )  // Non Process Starts Here
	{
		//print_r($template_wise_task[$row[template_id]]); die;
		foreach( $template_wise_task[$row[template_id]]  as $task_id=>$row_task)
		{
			//if(!in_array($task_id,$tna_approval_task)) // Selected Approval Task here
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
				//print_r($tna_process_list[$row[po_id]][$row_task[task_name]]); die;
				if( $tna_process_list[$row[po_id]][$row_task[task_name]]=="") 
				{
					if ($mst_id=="") $mst_id=return_next_id( "id", "tna_process_mst"); else $mst_id+=1;
					if ($data_array_tna_process!="") $data_array_tna_process .=",";
   
					$data_array_tna_process .="('$mst_id','$row[template_id]','$row[job_no_mst]','$row[po_id]','$row[po_received_date]','$row[shipment_date]','1','$row_task[task_name]','$target_date','$start_date','$finish_date','$notice_date_start','$notice_date_end','$date','$row_task[sequence_no]',1,0)";
					//print_r($data_array_tna_process);die;
				}
				else
				{
					if ( ($tna_task_update_data[$row[po_id]][$row_task[task_name]]['min_start_date']=="0000-00-00" || $tna_task_update_data[$row[po_id]][$row_task[task_name]]['min_start_date']=="") && ($tna_task_update_data[$row[po_id]][$row_task[task_name]]['max_start_date']!="0000-00-00" || $tna_task_update_data[$row[po_id]][$row_task[task_name]]['max_start_date']!="" ) )
					{  
						$tna_task_update_data[$row[po_id]][$row_task[task_name]]['min_start_date']= $tna_task_update_data[$row[po_id]][$row_task[task_name]]['max_start_date'];
					}
					
					if ( $tna_task_update_data[$row[po_id]][$row_task[task_name]]['min_start_date']!="0000-00-00" || $tna_task_update_data[$row[po_id]][$row_task[task_name]]['min_start_date']!="" ) $start_date=$tna_task_update_data[$row[po_id]][$row_task[task_name]]['min_start_date']; else $start_date="0000-00-00";
					if ( $tna_task_update_data[$row[po_id]][$row_task[task_name]]['max_start_date']!="0000-00-00" || $tna_task_update_data[$row[po_id]][$row_task[task_name]]['max_start_date']!="" ) $finish_date=$tna_task_update_data[$row[po_id]][$row_task[task_name]]['max_start_date']; else $finish_date="0000-00-00";
					
					if(!in_array($row_task[task_name],$approval_array))
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
					
//if($row_task[task_name]==101) { print_r($finish_date); die; }
					$process_id_up_array[]=$tna_process_list[$row[po_id]][$row_task[task_name]];
					 
						 
					//if($row_task[task_name]==50) echo "'".$start_date."','".$finish_date."'=".$tna_process_list[$row[job_no]][$row[poid]][$row_task[task_name]];
					$data_array_tna_process_up[$tna_process_list[$row[po_id]][$row_task[task_name]]] =explode(",",("'".$start_date."','".$finish_date."'")); 
				}
			}
		}
	}
//	  die;
	//die;
	  
	 
	//print_r($data_array_tna_process)."_*";die;
	
	//change_date_format(trim(    ),'','',1)       .'_'. 
	
	//$data_array_tna_process="";
	
	//print_r($process_id_up_array); die;
	
	$con = connect();
	if( $db_type==0 )
	{
		mysql_query( "BEGIN" );
	}
	
	
	if($db_type==0)
	{
		
		//echo "INSERT INTO tna_process_mst (".$field_array_tna_process.") VALUES ".$data_array_tna_process;die;
		
		if( $data_array_tna_process!="" ) 
			$rID=sql_insert("tna_process_mst",$field_array_tna_process,$data_array_tna_process,1);
	 	if(count($process_id_up_array>0)) 
 			$rID_up=execute_query(bulk_update_sql_statement( "tna_process_mst", "id", $field_array_tna_process_up, $data_array_tna_process_up, $process_id_up_array ));
	
		mysql_query("COMMIT");
	
	}
	if($db_type==1 || $db_type==2 )
	{
		//echo "INSERT INTO tna_process_mst (".$field_array_tna_process.") VALUES ".$data_array_tna_process;die;
		//echo "saju".$data_array_tna_process;die;
		if( $data_array_tna_process!="" ) 
		{
			//echo "INSERT INTO tna_process_mst (".$field_array_tna_process.") VALUES ".$data_array_tna_process;die;
			$rID=sql_insert("tna_process_mst",$field_array_tna_process,$data_array_tna_process,1);
		}
	 	if(count($process_id_up_array>0)) 
		{
			// echo bulk_update_sql_statement( "tna_process_mst", "id", $field_array_tna_process_up, $data_array_tna_process_up, $process_id_up_array );die;
			
 			$rID_up=execute_query(bulk_update_sql_statement( "tna_process_mst", "id", $field_array_tna_process_up, $data_array_tna_process_up, $process_id_up_array ));
		}
	
		oci_commit($con); 
		//echo "0**".$rID."**".implode(", ",$template_missing_po);
	}
	 
	disconnect($con);
	echo "0**".$rID."**".implode(", ",$template_missing_po);
	//echo "INSERT INTO pro_garments_production_dtls (".$field_array.") VALUES ".$data_array;die;
	die;
	
}


function get_tna_template( $remain_days, $tna_template, $buyer ) // Always treat the lowest template ... if not no process on that
{
	//print_r($tna_template);die; 
	//return 5;   
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
				/*
				if ($i!=0)
				{
					$up_day=$tna_template_buyer[$buyer][$i]['lead']-$remain_days;
					$low_day=$remain_days-$tna_template_buyer[$buyer][$i-1]['lead'];
					if ($up_day>=$low_day)
						return $tna_template_buyer[$buyer][$i-1]['id'];
					else
						return $tna_template_buyer[$buyer][$i]['id'];
				}
				else
				{
					return $tna_template_buyer[$buyer][$i]['id'];
				}*/
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
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="700" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
            <thead>
                <tr>                	 
                    <th width="150">Company Name</th>
                    <th width="100" align="center" >Buyer Name</th>
                    <th width="200">Po Number/Style Reff</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                         <?
						  echo create_drop_down( "cbo_company", 170,"select id,company_name from lib_company where status_active=1 and is_deleted=0","id,company_name", 1, "-- Select company --", $company, "load_drop_down( 'tna_process_controller', this.value, 'load_drop_down_buyer', 'buyer_td')" );
                            
                         ?> 
                    </td>
                    <td  align="center" id="buyer_td">				
                        <?
                            echo create_drop_down( "cbo_buyer", 155,"select id,buyer_name from  lib_buyer where status_active=1 and is_deleted=0","id,buyer_name", 1, "-- Select Buyer --", $buyer, "" );
                        ?> 	
                    </td>    
                    <td align="center">
                        <input type="hidden" id="selected_job">
                       <?  
                            $search_by_arr=array(1=>'PO Number',2=>'Style Refference');
														
							echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                        ?>
                     </td> 
                     <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('cbo_company').value+'_'+document.getElementById('cbo_buyer').value, 'ponumber_search_list_view', 'search_div', 'tna_process_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" />				
                    </td>
            </tr>
        	    
            </tbody>
         </tr>         
        </table>    
        <div align="center" valign="top" id="search_div"> </div> 
        </form>
   </div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
var buyer='<? echo $buyer; ?>';
document.getElementById('cbo_buyer').value=buyer;
</script>
</html>
<?
}

if ($action=="ponumber_search_list_view")
{
 $data=explode('_',$data);
 $surch_by=$data[0];
 $company=$data[1];
 $buyer=$data[2];
 $company_array=return_library_array( "select id, company_name from lib_company",'id','company_name');
 $buyer_array=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
 
 ?>
  
        <table width="802" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="">
                <thead>
                    <th width="40" height="34">SL</th>
                    <th width="130">Company Name</th>
                    <th width="130">Buyer Name</th>
                    <th width="130">PO Number/Style Reff</th>
                    <th width="110">Po Receive Date</th>
                    <th width="150">Publish Shipment Date</th>
                    <th >Lead Time</th>
                </thead>
        </table>
        <div style="width:802px; max-height:250px; overflow-y:scroll"> 
        <table width="780" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_search">
		<?
			 
			 
			      if($db_type==0) $lead_time="DATEDIFF(b.pub_shipment_date,b.po_received_date) as  date_diff";
				  if($db_type==2) $lead_time="(b.pub_shipment_date-b.po_received_date) as  date_diff";
			 
					$sql=sql_select("select a.company_name,a.buyer_name,b.po_number,b.id,b.po_received_date,b.pub_shipment_date,$lead_time from   wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$data[1]' and a.buyer_name='$data[2]'");
					$i=1;
					foreach($sql as $row)
				   {
					  if ($i%2==0)  
					  $bgcolor="#E9F3FF";
					  else
					  $bgcolor="#FFFFFF";
		?>
					  <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value(' <?  echo $row[csf("po_number")];  ?>',<?  echo $row[csf("id")];  ?>)">
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
 	
	
}
 
?>
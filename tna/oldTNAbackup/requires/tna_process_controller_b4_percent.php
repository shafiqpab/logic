<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../includes/common.php');

// if tna_integrated then upadte related apporval table from tna some date fields

extract ($_REQUEST);

 
if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer", 170,"select a.id,a.buyer_name from  lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0","id,buyer_name", 1, "-- Select Buyer --", 0, "" );

}
 
// Variable Settings List   $variable_settings[$row[csf('company_name')]]
	$sql = "SELECT company_name,tna_integrated FROM  variable_order_tracking WHERE status_active =1 and is_deleted = 0 and variable_list=14";
	$result = sql_select( $sql );
	$variable_settings = array();
	foreach( $result as $row ) 
	{		
		$variable_settings[$row[csf('company_name')]] = $row[csf('tna_integrated')];
	}
 
//Task Details
	$sql = "SELECT id,task_catagory,task_name,task_short_name,task_type,module_name,link_page,penalty,completion_percent FROM lib_tna_task WHERE is_deleted = 0 and status_active=1";
	$result = sql_select( $sql ) ;
	$tna_task_details = array();
	$tna_task_name=array();
	$tna_task_name_tmp=array();
	foreach( $result as $row ) 
	{
		$tna_task_details[$row[csf('task_catagory')]][$row[csf('task_name')]]['task_catagory']=  $row[csf('task_catagory')];
		$tna_task_details[$row[csf('task_catagory')]][$row[csf('task_name')]]['id']=  $row[csf('id')];
		$tna_task_details[$row[csf('task_catagory')]][$row[csf('task_name')]]['task_name']=  $row[csf('task_name')];
		$tna_task_details[$row[csf('task_catagory')]][$row[csf('task_name')]]['task_short_name']=  $row[csf('task_short_name')];
		$tna_task_details[$row[csf('task_catagory')]][$row[csf('task_name')]]['task_type']=  $row[csf('task_type')];
		$tna_task_details[$row[csf('task_catagory')]][$row[csf('task_name')]]['module_name']=  $row[csf('module_name')];
		$tna_task_details[$row[csf('task_catagory')]][$row[csf('task_name')]]['link_page']=  $row[csf('link_page')];
		$tna_task_details[$row[csf('task_catagory')]][$row[csf('task_name')]]['penalty']=  $row[csf('penalty')];
		$tna_task_details[$row[csf('task_catagory')]][$row[csf('task_name')]]['completion_percent']=  $row[csf('completion_percent')];
		
		$tna_task_name[$row[csf('id')]]['task_catagory']=  $row[csf('task_catagory')];
		$tna_task_name[$row[csf('id')]]['task_name']=  $row[csf('task_name')];
		$tna_task_name[$row[csf('id')]]['task_short_name']=  $row[csf('task_short_name')];
		$tna_task_name[$row[csf('id')]]['task_type']=  $row[csf('task_type')];
		$tna_task_name[$row[csf('id')]]['module_name']=  $row[csf('module_name')];
		$tna_task_name[$row[csf('id')]]['link_page']=  $row[csf('link_page')];
		$tna_task_name[$row[csf('id')]]['penalty']=  $row[csf('penalty')];
		$tna_task_name[$row[csf('id')]]['completion_percent']=  $row[csf('completion_percent')]; 
		$tna_task_name_tmp[$row[csf('task_catagory')]][]=$row[csf('id')];
		//$tna_task_name_cat[$row[csf('task_catagory')]][]=$row[csf('task_name')];
	}
	
	
	
//Template Details
	$sql_task = "SELECT a.id,task_template_id,lead_time,material_source,total_task,tna_task_id,deadline,execution_days,notice_before,sequence_no,for_specific,b.task_catagory,b.task_name,b.task_type FROM  tna_task_template_details a, lib_tna_task b WHERE   a.tna_task_id=b.id and b.is_deleted=0 and b.status_active=1 order by for_specific,lead_time ";
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
		$template_information[$row[csf("task_template_id")]][$tna_task_name[$row[csf("tna_task_id")]]['task_catagory']][$row[csf("tna_task_id")]]['lead_time']=$row[csf("lead_time")];
		
		 $tna_task_template[$row[csf("task_template_id")]][$tna_task_name[$row[csf("tna_task_id")]]['task_catagory']][$row[csf("tna_task_id")]]['lead_time']=$row[csf("lead_time")];
		 $tna_task_template[$row[csf("task_template_id")]][$tna_task_name[$row[csf("tna_task_id")]]['task_catagory']][$row[csf("tna_task_id")]]['material_source']=$row[csf("material_source")];
		 $tna_task_template[$row[csf("task_template_id")]][$tna_task_name[$row[csf("tna_task_id")]]['task_catagory']][$row[csf("tna_task_id")]]['total_task']=$row[csf("total_task")];
		 $tna_task_template[$row[csf("task_template_id")]][$tna_task_name[$row[csf("tna_task_id")]]['task_catagory']][$row[csf("tna_task_id")]]['tna_task_id']=$row[csf("tna_task_id")];
		 $tna_task_template[$row[csf("task_template_id")]][$tna_task_name[$row[csf("tna_task_id")]]['task_catagory']][$row[csf("tna_task_id")]]['deadline']=$row[csf("deadline")];
		 $tna_task_template[$row[csf("task_template_id")]][$tna_task_name[$row[csf("tna_task_id")]]['task_catagory']][$row[csf("tna_task_id")]]['execution_days']=$row[csf("execution_days")];
		 $tna_task_template[$row[csf("task_template_id")]][$tna_task_name[$row[csf("tna_task_id")]]['task_catagory']][$row[csf("tna_task_id")]]['notice_before']=$row[csf("notice_before")];
		 $tna_task_template[$row[csf("task_template_id")]][$tna_task_name[$row[csf("tna_task_id")]]['task_catagory']][$row[csf("tna_task_id")]]['sequence_no']=$row[csf("sequence_no")];
		 $tna_task_template[$row[csf("task_template_id")]][$tna_task_name[$row[csf("tna_task_id")]]['task_catagory']][$row[csf("tna_task_id")]]['for_specific']=$row[csf("for_specific")];
		 $tna_task_template[$row[csf("task_template_id")]][$tna_task_name[$row[csf("tna_task_id")]]['task_catagory']][$row[csf("tna_task_id")]]['id']=$row[csf("id")];
		 $tna_task_template[$row[csf("task_template_id")]][$tna_task_name[$row[csf("tna_task_id")]]['task_catagory']][$row[csf("tna_task_id")]]['task_template_id']=$row[csf("task_template_id")];
				 
		 if (!in_array($row[csf("task_template_id")],$templatesss))
		 	{ $templatesss[]=$row[csf("task_template_id")]; $g=0; }
			 
		 $tna_task_template_task[$row[csf("task_template_id")]][$row[csf("task_catagory")]][$row[csf("tna_task_id")]]=$row[csf("task_name")];
		 
		// $tna_task_template_task_new[$row[csf("task_template_id")]][$row[csf("task_catagory")]][$row[csf("task_name")]][$row[csf("task_type")]]=$row[csf("task_name")];
		 $tna_task_template_task[$row[csf("task_template_id")]][$row[csf("task_catagory")]]['type']=$row[csf("task_type")];
		 $g++;
		 $i++;
	}
	//print_r($tna_task_template); die;
	// print_r($tna_template_buyer); die;
	
if ( $action=="tna_process" )
{
	if($db_type==0) $blank_date="0000-00-00"; else $blank_date=""; 
	//echo "delete FROM tna_process_mst WHERE  po_number_id in ( $txt_ponumber_id )"; die;
	// Reprocess Check
	if (trim($txt_ponumber_id)=="") $strcond=""; 
	else 
	{
		if( $is_delete==1 ) $rid=execute_query("delete FROM tna_process_mst WHERE  po_number_id in ( $txt_ponumber_id )",1); 
		 
	} 
	if( $cbo_buyer>0 ) $buyer_cond=" and a.buyer_name=$cbo_buyer ";
	//$job_nos ="'ASL-13-00191'";
	///mysql_query("delete a.* FROM tna_process_mst a  WHERE   a.job_no in ( $job_nos )") or die (mysql_error()); 
	//$strcond="sdas";

// PO Details List with JOB NO
//$strcond="---";
//$job_nos="'ASL-14-00024','ASL-14-00025','ASL-14-00029'";

//echo "shajjad_".$txt_ponumber_id;die;
 
	if ( $txt_ponumber_id=="" )
	{
		if($db_type==0)
		{
			$sql = "SELECT (pub_shipment_date) as shipment_date,job_no_mst,po_received_date,b.id,po_quantity,a.buyer_name,a.garments_nature,b.po_number FROM wo_po_break_down b, wo_po_details_master a WHERE b.is_deleted = 0 and b.status_active=1 and a.job_no=b.job_no_mst and b.shipment_date!='0000-00-00' and b.po_received_date!='0000-00-00' $buyer_cond  ORDER BY b.shipment_date asc";
		}
		else
		{									
			$sql = "SELECT (pub_shipment_date) as shipment_date,job_no_mst,po_received_date,b.id,po_quantity,a.buyer_name,a.garments_nature,b.po_number FROM wo_po_break_down b, wo_po_details_master a WHERE b.is_deleted = 0 and b.status_active=1 and a.job_no=b.job_no_mst and to_char(b.shipment_date)!='0000-00-00' and to_char(b.po_received_date)!='0000-00-00'  $buyer_cond  ORDER BY b.shipment_date asc";
		}
	}
	else
	{
		if($db_type==0)
		{
			$sql = "SELECT (pub_shipment_date) as shipment_date,job_no_mst,po_received_date,b.id,po_quantity,a.buyer_name,a.garments_nature,b.po_number FROM wo_po_break_down b, wo_po_details_master a WHERE b.is_deleted = 0 and b.status_active=1 and a.job_no=b.job_no_mst and b.shipment_date!='0000-00-00' and b.po_received_date!='0000-00-00' and b.id  in ( $txt_ponumber_id ) $buyer_cond  ORDER BY b.shipment_date asc";
		}
		else
		{
			$sql = "SELECT (pub_shipment_date) as shipment_date,job_no_mst,po_received_date,b.id,po_quantity,a.buyer_name,a.garments_nature,b.po_number FROM wo_po_break_down b, wo_po_details_master a WHERE b.is_deleted = 0 and b.status_active=1 and a.job_no=b.job_no_mst and to_char(b.shipment_date)!='0000-00-00' and to_char(b.po_received_date)!='0000-00-00' and b.id  in ( $txt_ponumber_id ) $buyer_cond  ORDER BY b.shipment_date asc";
		}
	}
	// echo "delete FROM tna_process_mst WHERE  po_number_id in ( $txt_ponumber_id )"."sss".$rid; die;
	//echo "saju_".$sql;die;
	
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
		//print_r($tna_template); die;
		$remain_days=datediff( "d", $row[csf("po_received_date")], $row[csf("shipment_date")] );
		$template_id=get_tna_template($remain_days,$tna_template,$row[csf("buyer_name")]);
		
		//echo "shajjad_".$template_id;die; 
		
		// print_r($tna_template_buyer[$row[csf("buyer_name")]]);
		//  echo $remain_days."==".$template_id."==".$row[csf("shipment_date")]."**"; 
		if ( $template_id=="" || $template_id==0 )
		{
			$template_missing_po[]=$row[csf("po_number")];
			//if(count($tna_template_buyer[$row[csf("buyer_name")]])>0) $template_id=
			//$tna_template_buyer[$row[csf("buyer_name")]][count($tna_template_buyer[$row[csf("buyer_name")]])-1]['id'];
			//else $template_id= $tna_template[count($tna_template)-1]['id'];
		} 
		else
		{
		 //print_r( $template_id ); die;
			if (!in_array( $row[csf("job_no_mst")],$job_no_array)) $job_no_array[]= $row[csf("job_no_mst")] ;
			$order_id_array[$i]=$row[csf("id")];
			$po_order_template[$row[csf("id")]]=  $template_id; 
			$po_order_details[$row[csf("id")]]['po_received_date']=$row[csf("po_received_date")];
			$po_order_details[$row[csf("id")]]['shipment_date']=$row[csf("shipment_date")];
			$po_order_details[$row[csf("id")]]['job_no_mst']=$row[csf("job_no_mst")];
			$po_order_details[$row[csf("id")]]['po_quantity']=$row[csf("po_quantity")];
			$job_nature[$row[csf('job_no')]] = $row[csf('garments_nature')];
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
		$tna_process_list[$row[csf('task_category')]][$row[csf('po_number_id')]][$row[csf('task_number')]]= $row[csf('id')];
		$tna_process_details[$row[csf('id')]]['start']=$row[csf('actual_start_date')];
		$tna_process_details[$row[csf('id')]]['finish']=$row[csf('actual_finish_date')];
	}
	
// Required qnty from Booking as KG
	if($db_type==0)
	{
		$sql = "SELECT po_break_down_id,sum(grey_fab_qnty) as grey_fab_qnty FROM wo_booking_dtls WHERE po_break_down_id in ( $po_ids ) and status_active =1 and is_deleted = 0";
	}
	else
	{
		$sql = "SELECT po_break_down_id,sum(grey_fab_qnty) as grey_fab_qnty FROM wo_booking_dtls WHERE po_break_down_id in ( $po_ids ) and status_active =1 and is_deleted = 0 group by po_break_down_id";
	}
	$result = sql_select( $sql );
	$po_wise_booking_qnty = array();
	foreach( $result as $row ) 
	{
		$po_wise_booking_qnty[$row[csf('po_break_down_id')]]= $row[csf('grey_fab_qnty')];
	}
	
// Sample Approval Update Data

	if($db_type==0)
	{
		$sql_task = "SELECT job_no_mst,po_break_down_id,sample_type_id,max(submitted_to_buyer) as max_submitted_to_buyer,min(submitted_to_buyer) as min_submitted_to_buyer,max(approval_status_date) as max_approval_status_date,min(approval_status_date) as min_approval_status_date
	 FROM wo_po_sample_approval_info WHERE job_no_mst in ( $job_no_list ) and is_deleted = 0 and status_active=1 group by sample_type_id,po_break_down_id order by job_no_mst,po_break_down_id asc,  	approval_status desc";
	}
	else
	{
		$sql_task = "SELECT po_break_down_id,sample_type_id,max(submitted_to_buyer) as max_submitted_to_buyer,min(submitted_to_buyer) as min_submitted_to_buyer,max(approval_status_date) as max_approval_status_date,min(approval_status_date) as min_approval_status_date
	 FROM wo_po_sample_approval_info WHERE job_no_mst in ( $job_no_list ) and is_deleted = 0 and status_active=1 group by sample_type_id,po_break_down_id order by po_break_down_id asc"; 
	}
	
	
	$result = sql_select( $sql_task );
	$sample_approval_update = array(); 
	foreach( $result as $row ) 
	{
		$sample_approval_update[$row[csf("po_break_down_id")]][$row[csf("sample_type_id")]]['max_start_date']=$row[csf("max_submitted_to_buyer")];
		$sample_approval_update[$row[csf("po_break_down_id")]][$row[csf("sample_type_id")]]['min_start_date']=$row[csf("min_submitted_to_buyer")];
		
		$sample_approval_update[$row[csf("po_break_down_id")]][$row[csf("sample_type_id")]]['min_approval_status_date']=$row[csf("min_approval_status_date")];
		$sample_approval_update[$row[csf("po_break_down_id")]][$row[csf("sample_type_id")]]['max_approval_status_date']=$row[csf("max_approval_status_date")];
	}
	
	//print_r($sample_approval_update); die;
 
// LABDIP Approval Data for Update
	if($db_type==0)
	{
		$sql_task = "SELECT job_no_mst,po_break_down_id,max(submitted_to_buyer) as max_submitted_to_buyer,min(submitted_to_buyer) as min_submitted_to_buyer,max(approval_status_date) as max_approval_status_date,min(approval_status_date) as min_approval_status_date
	 FROM wo_po_lapdip_approval_info WHERE job_no_mst in ( $job_no_list ) and is_deleted = 0 and status_active=1 group by po_break_down_id order by job_no_mst,po_break_down_id asc";
	}
	else
	{
		$sql_task = "SELECT po_break_down_id,max(submitted_to_buyer) as max_submitted_to_buyer,min(submitted_to_buyer) as min_submitted_to_buyer,max(approval_status_date) as max_approval_status_date,min(approval_status_date) as min_approval_status_date
	 FROM wo_po_lapdip_approval_info WHERE job_no_mst in ( $job_no_list ) and is_deleted = 0 and status_active=1 group by po_break_down_id order by po_break_down_id asc";
	}
	
	//echo "as_".$sql_task;die;
	
	$result = sql_select( $sql_task );
	$labdip_update_task = array(); 
	foreach( $result as $row ) 
	{
		$labdip_update_task[$row[csf("po_break_down_id")]]['max_start_date']=$row[csf("max_submitted_to_buyer")];
		$labdip_update_task[$row[csf("po_break_down_id")]]['min_start_date']=$row[csf("min_submitted_to_buyer")];
		
		$labdip_update_task[$row[csf("po_break_down_id")]]['min_approval_status_date']=$row[csf("min_approval_status_date")];
		$labdip_update_task[$row[csf("po_break_down_id")]]['max_approval_status_date']=$row[csf("max_approval_status_date")];
	}

// Trims Approval Data for Update
	if($db_type==0)
	{
		$sql_task = "SELECT job_no_mst,po_break_down_id,max(submitted_to_buyer) as max_submitted_to_buyer,min(submitted_to_buyer) as min_submitted_to_buyer,max(approval_status_date) as max_approval_status_date,min(approval_status_date) as min_approval_status_date,trim_type
FROM lib_item_group b, wo_po_trims_approval_info a
WHERE b.id=a.accessories_type_id and job_no_mst in ( $job_no_list ) and a.is_deleted = 0 and a.status_active=1 group by po_break_down_id,trim_type order by job_no_mst,po_break_down_id asc";
	}
	else
	{
		$sql_task = "SELECT po_break_down_id,max(submitted_to_buyer) as max_submitted_to_buyer,min(submitted_to_buyer) as min_submitted_to_buyer,max(approval_status_date) as max_approval_status_date,min(approval_status_date) as min_approval_status_date,trim_type
FROM lib_item_group b, wo_po_trims_approval_info a
WHERE b.id=a.accessories_type_id and job_no_mst in ( $job_no_list ) and a.is_deleted = 0 and a.status_active=1 group by po_break_down_id,trim_type order by po_break_down_id asc";
		
	}
	
	//echo "as_".$sql_task;die;
	
	 $trim_type_array= return_library_array( "select id, trim_type from lib_item_group",'id','trim_type');
	 
	
	$result = sql_select( $sql_task );
	$trims_update_task = array(); 
	foreach( $result as $row ) 
	{
		$trims_update_task[$row[csf("po_break_down_id")]][$row[csf("trim_type")]]['max_start_date']=$row[csf("max_submitted_to_buyer")];
		$trims_update_task[$row[csf("po_break_down_id")]][$row[csf("trim_type")]]['min_start_date']=$row[csf("min_submitted_to_buyer")];
		
		$trims_update_task[$row[csf("po_break_down_id")]][$row[csf("trim_type")]]['min_approval_status_date']=$row[csf("min_approval_status_date")];
		$trims_update_task[$row[csf("po_break_down_id")]][$row[csf("trim_type")]]['max_approval_status_date']=$row[csf("max_approval_status_date")];
	}
 //print_r($trims_update_task); die;
// Embelishment Approval Data for Update
	if($db_type==0)
	{
		$sql_task = "SELECT job_no_mst,po_break_down_id,max(submitted_to_buyer) as max_submitted_to_buyer,min(submitted_to_buyer) as min_submitted_to_buyer,max(approval_status_date) as max_approval_status_date,min(approval_status_date) as min_approval_status_date,embellishment_id
	 FROM wo_po_embell_approval WHERE job_no_mst in ( $job_no_list ) and is_deleted = 0 and status_active=1 group by po_break_down_id,embellishment_id order by job_no_mst,po_break_down_id asc";
	}
	else
	{
		$sql_task = "SELECT po_break_down_id,max(submitted_to_buyer) as max_submitted_to_buyer,min(submitted_to_buyer) as min_submitted_to_buyer,max(approval_status_date) as max_approval_status_date,min(approval_status_date) as min_approval_status_date,embellishment_id
	 FROM wo_po_embell_approval WHERE job_no_mst in ( $job_no_list ) and is_deleted = 0 and status_active=1 group by po_break_down_id,embellishment_id order by po_break_down_id asc";
	}
	
	//echo "as2_".$sql_task;die;
	 
	$result = sql_select( $sql_task );
	$embelishment_update_task = array(); 
	foreach( $result as $row ) 
	{
		$embelishment_update_task[$row[csf("po_break_down_id")]][$row[csf("embellishment_id")]]['max_start_date']=$row[csf("max_submitted_to_buyer")];
		$embelishment_update_task[$row[csf("po_break_down_id")]][$row[csf("embellishment_id")]]['min_start_date']=$row[csf("min_submitted_to_buyer")];
		
		$embelishment_update_task[$row[csf("po_break_down_id")]][$row[csf("embellishment_id")]]['min_approval_status_date']=$row[csf("min_approval_status_date")];
		$embelishment_update_task[$row[csf("po_break_down_id")]][$row[csf("embellishment_id")]]['max_approval_status_date']=$row[csf("max_approval_status_date")];
	}	

// Purchase/Booking  Data for Update 2- FB, 4 -Trims, 12- Service
	if($db_type==0)
	{
		$sql_task = "SELECT b.po_break_down_id, min(a.booking_date) as start_date, max(a.booking_date) as end_date, a.item_category,a.booking_type
	FROM  wo_booking_mst a, wo_booking_dtls b WHERE b.po_break_down_id in ( $po_ids ) and a.booking_no=b.booking_no and a.item_category in ( 2,4,12,3 ) group by b.po_break_down_id,item_category order by b.po_break_down_id asc";
	}
	else
	{
		$sql_task = "SELECT b.po_break_down_id, min(a.booking_date) as start_date, max(a.booking_date) as end_date, a.item_category,a.booking_type
	FROM  wo_booking_mst a, wo_booking_dtls b WHERE b.po_break_down_id in ( $po_ids ) and a.booking_no=b.booking_no and a.item_category in ( 2,4,12,3 ) group by b.po_break_down_id,item_category,a.booking_type order by b.po_break_down_id asc";
	}
	
	//echo "as3_".$sql_task;die;
	
	$result = sql_select( $sql_task );
	$purchase_update_task = array(); 
	foreach( $result as $row ) 
	{ // $row[csf("booking_type")]=4;
		if( $row[csf("booking_type")]==4 && $row[csf("item_category")]==2) //smpl fab booking
		{
			$row[csf("item_category")]=23;
			$purchase_update_task[$row[csf("po_break_down_id")]][$row[csf("item_category")]]['start_date']=$row[csf("start_date")];
			$purchase_update_task[$row[csf("po_break_down_id")]][$row[csf("item_category")]]['end_date']=$row[csf("end_date")];
		}
		else
		{
			$purchase_update_task[$row[csf("po_break_down_id")]][$row[csf("item_category")]]['start_date']=$row[csf("start_date")];
			$purchase_update_task[$row[csf("po_break_down_id")]][$row[csf("item_category")]]['end_date']=$row[csf("end_date")];
		}
	}
	$purchase_array_mapping=array(1=>2,2=>4,3=>12,4=>23);
	
	//print_r( $purchase_update_task); die;
	//print_r($purchase_update_task); die;
// Inventory Update Data
	if($db_type==0)
	{  
		$sql = "SELECT b.po_breakdown_id,b.entry_form, min(a.transaction_date) mindate, max(a.transaction_date) maxdate, sum(quantity) as prod_qntry 
FROM inv_transaction a,  order_wise_pro_details b where  b.trans_id=a.id and b.entry_form in ( 2,7,22,37 ) and b.po_breakdown_id in ( $po_ids ) group by b.po_breakdown_id,b.entry_form order by b.po_breakdown_id";
	}
	else
	{
		$sql = "SELECT b.po_breakdown_id,b.entry_form, min(a.transaction_date) mindate, max(a.transaction_date) maxdate, sum(quantity) as prod_qntry 
FROM inv_transaction a,  order_wise_pro_details b where  b.trans_id=a.id and b.entry_form in (2,7,22,37 ) and b.po_breakdown_id in ( $po_ids ) group by b.po_breakdown_id,b.entry_form order by b.po_breakdown_id";
	}
	
	//echo $sql;die;
	
	$inventory_transaction_update=array();
	$inv_array_mapping=array(1=>"2",2=>"7",3=>"24",4=>"244");
	$data_array=sql_select($sql);
	foreach($data_array as $row)
	{
		if ($row[csf("entry_form")]==22) $row[csf("entry_form")]=2;
		if ($row[csf("entry_form")]==37) $row[csf("entry_form")]=7;
		
		$inventory_transaction_update[$row[csf("po_breakdown_id")]][$row[csf("entry_form")]]['start_date']=$row[csf("mindate")];
		$inventory_transaction_update[$row[csf("po_breakdown_id")]][$row[csf("entry_form")]]['end_date']=$row[csf("maxdate")];
		$inventory_transaction_update[$row[csf("po_breakdown_id")]][$row[csf("entry_form")]]['quantity']=$row[csf("prod_qntry")];
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
	
 // echo $sql; die;
	
	$data_array=sql_select($sql);
	foreach($data_array as $row)
	{
		//echo $row['trim_type']."==";
		$entry=($row[csf("trim_type")] == 1 ? 24 : 244);
		$inventory_transaction_update[$row[csf("po_breakdown_id")]][$entry]['start_date']=$row[csf("mindate")];
		$inventory_transaction_update[$row[csf("po_breakdown_id")]][$entry]['end_date']=$row[csf("maxdate")];
		$inventory_transaction_update[$row[csf("po_breakdown_id")]][$entry]['quantity']=$row[csf("prod_qntry")];
	}

//print_r($inventory_transaction_update);die;

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
	
	//echo "asw6_".$sql;die;
	
	$fabric_prod_transaction_update=array();
	$fab_prod_array_mapping=array(1=>"2",2=>"6",3=>"7");
	$data_array=sql_select($sql);
	foreach($data_array as $row)
	{
		$fabric_prod_transaction_update[$row[csf("po_breakdown_id")]][$row[csf("entry_form")]]['start_date']=$row[csf("mindate")];
		$fabric_prod_transaction_update[$row[csf("po_breakdown_id")]][$row[csf("entry_form")]]['end_date']=$row[csf("maxdate")];
		$fabric_prod_transaction_update[$row[csf("po_breakdown_id")]][$row[csf("entry_form")]]['quantity']=$row[csf("prod_qntry")];
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
	
	//echo "asw7_".$sql;die;
	 
	$data_array=sql_select($sql);
	foreach($data_array as $row)
	{
		$fabric_prod_transaction_update[$row[csf("po_breakdown_id")]][$row[csf("entry_form")]]['start_date']=$row[csf("mindate")];
		$fabric_prod_transaction_update[$row[csf("po_breakdown_id")]][$row[csf("entry_form")]]['end_date']=$row[csf("maxdate")];
		$fabric_prod_transaction_update[$row[csf("po_breakdown_id")]][$row[csf("entry_form")]]['quantity']=$row[csf("prod_qntry")];
	}
	
	/*$sql = "SELECT b.po_breakdown_id,b.entry_form, min(a.received_date) mindate, max(a.received_date) maxdate, sum(quantity) as prod_qntry 
FROM  pro_dyeing_update_mst a,  order_wise_pro_details b, pro_dyeing_update_dtls c where  c.id=b.dtls_id and a.id=c.mst_id and b.entry_form in ( 6 ) and b.po_breakdown_id in ( $po_ids ) group by b.po_breakdown_id,b.entry_form order by b.po_breakdown_id";
	$data_array=sql_select($sql);
	foreach($data_array as $row)
	{
		$fabric_prod_transaction_update[$row[csf("po_breakdown_id")]][$row[csf("entry_form")]]['start_date']=$row[csf("mindate")];
		$fabric_prod_transaction_update[$row[csf("po_breakdown_id")]][$row[csf("entry_form")]]['end_date']=$row[csf("maxdate")];
		$fabric_prod_transaction_update[$row[csf("po_breakdown_id")]][$row[csf("entry_form")]]['quantity']=$row[csf("prod_qntry")];
	}*/
	
	if($db_type==0)
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
		$fabric_prod_transaction_update[$row[csf("po_id")]][$row[csf("entry_form")]]['start_date']=$row[csf("process_start_date")];
		$fabric_prod_transaction_update[$row[csf("po_id")]][$row[csf("entry_form")]]['end_date']=$row[csf("process_end_date")];
		$fabric_prod_transaction_update[$row[csf("po_id")]][$row[csf("entry_form")]]['quantity']=$row[csf("batch_weight")];
	}
	
// Inspection Data for Update

	if($db_type==0)
	{
		$sql = "SELECT job_no,po_break_down_id,min(inspection_date) as mind,max(inspection_date) as maxd,sum(inspection_qnty) as sumtot FROM pro_buyer_inspection WHERE job_no in ( $job_no_list ) and status_active =1 and is_deleted = 0 group by po_break_down_id";
	}
	else
	{
		$sql = "SELECT po_break_down_id,min(inspection_date) as mind,max(inspection_date) as maxd,sum(inspection_qnty) as sumtot FROM pro_buyer_inspection WHERE job_no in ( $job_no_list ) and status_active =1 and is_deleted = 0 group by po_break_down_id";
	}
	
	//echo "asw9_".$sql;die;
	
	$result = sql_select( $sql );
	$inspection_status_array = array();
	foreach( $result as $row ) 
	{
		$inspection_status_array[$row[csf('po_break_down_id')]]['min'] = $row[csf('mind')];
		$inspection_status_array[$row[csf('po_break_down_id')]]['max'] = $row[csf('maxd')];
		$inspection_status_array[$row[csf('po_break_down_id')]]['qnty'] = $row[csf('sumtot')];
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
	
	//echo "asw10_".$sql;die;
	
	$result = sql_select( $sql );
	$exfactory_status_array = array();
	foreach( $result as $row ) 
	{
		$exfactory_status_array[$row[csf('po_break_down_id')]][1]['min'] = $row[csf('mind')];
		$exfactory_status_array[$row[csf('po_break_down_id')]][1]['max'] = $row[csf('maxd')];
		$exfactory_status_array[$row[csf('po_break_down_id')]][1]['qnty'] = $row[csf('sumtot')];
	}
	
	if($db_type==0)
	{
		$sql = "SELECT group_concat(distinct a.id) as bill_id, b.po_breakdown_id, min(a.submit_date) as mind,max(a.submit_date) as maxd 
	FROM  com_export_doc_submission_mst a,com_export_invoice_ship_dtls b, com_export_doc_submission_invo c
	WHERE b.po_breakdown_id in ( $po_ids ) and a.id=c.doc_submission_mst_id and c.invoice_id=b.mst_id and b.current_invoice_qnty>0 group by b.po_breakdown_id
	 ";
	}
	else
	{
		$sql = "SELECT listagg(CAST(a.id as VARCHAR(4000)),',') within group (order by a.id) as bill_id, b.po_breakdown_id, min(a.submit_date) as mind,max(a.submit_date) as maxd 
	FROM  com_export_doc_submission_mst a,com_export_invoice_ship_dtls b, com_export_doc_submission_invo c
	WHERE b.po_breakdown_id in ( $po_ids ) and a.id=c.doc_submission_mst_id and c.invoice_id=b.mst_id and b.current_invoice_qnty>0 group by b.po_breakdown_id
	 ";
	 
	 
	}
	
	//echo "asw12_".$sql;die;
	
	$result = sql_select( $sql );
	foreach( $result as $row ) 
	{	
		
		if($bill_id=="") $bill_id=implode(",",array_unique(explode(",",$row[csf('bill_id')]))); else $bill_id .= ",".implode(",",array_unique(explode(",",$row[csf('bill_id')])));
		$exfactory_status_array[$row[csf('po_breakdown_id')]][2]['min'] = $row[csf('mind')];
		$exfactory_status_array[$row[csf('po_breakdown_id')]][2]['max'] = $row[csf('maxd')];
		$exfactory_status_array[$row[csf('po_breakdown_id')]][2]['qnty'] = $row[csf('sumtot')];
	}
	
	if($db_type==0)
	{
		$sql = "SELECT b.po_breakdown_id,max(a.received_date) maxd,min(a.received_date) mind FROM com_export_proceed_realization a, com_export_invoice_ship_dtls b WHERE a.invoice_bill_id=b.mst_id and b.po_breakdown_id in ( $po_ids ) and a.is_invoice_bill=2 group by b.po_breakdown_id";
	}
	else
	{
		$sql = "SELECT b.po_breakdown_id,max(a.received_date) maxd,min(a.received_date) mind FROM com_export_proceed_realization a, com_export_invoice_ship_dtls b WHERE a.invoice_bill_id=b.mst_id and b.po_breakdown_id in ( $po_ids ) and a.is_invoice_bill=2 group by b.po_breakdown_id";
	}
	
	//echo "asw13_".$sql;die;
	
	$result = sql_select( $sql );
	foreach( $result as $row ) 
	{
		$exfactory_status_array[$row[csf('po_breakdown_id')]][3]['min'] = $row[csf('mind')];
		$exfactory_status_array[$row[csf('po_breakdown_id')]][3]['max'] = $row[csf('maxd')];
	}
	
	if($db_type==0)
	{
		$sql = "SELECT b.po_breakdown_id,max(a.received_date) maxd,min(a.received_date) mind FROM com_export_proceed_realization a, com_export_invoice_ship_dtls b,  com_export_doc_submission_invo c WHERE a.invoice_bill_id=c.doc_submission_mst_id and b.po_breakdown_id in ( $po_ids ) and a.is_invoice_bill=1 and c.invoice_id=b.mst_id group by b.po_breakdown_id";
	}
	else
	{
		$sql = "SELECT b.po_breakdown_id,max(a.received_date) maxd,min(a.received_date) mind FROM com_export_proceed_realization a, com_export_invoice_ship_dtls b,  com_export_doc_submission_invo c WHERE a.invoice_bill_id=c.doc_submission_mst_id and b.po_breakdown_id in ( $po_ids ) and a.is_invoice_bill=1 and c.invoice_id=b.mst_id group by b.po_breakdown_id";
	}
	
	//echo "asw14_".$sql;die;
	
	$result = sql_select( $sql );
	foreach( $result as $row ) 
	{
		$exfactory_status_array[$row[csf('po_breakdown_id')]][3]['min'] = $row[csf('mind')];
		$exfactory_status_array[$row[csf('po_breakdown_id')]][3]['max'] = $row[csf('maxd')];
	}

// Garments Production Data for Update  

	if($db_type==0)
	{		 
		$sql = "SELECT po_break_down_id, min(production_date) as mind,max(production_date) as maxd, production_type,sum(production_quantity) as production_quantity FROM  pro_garments_production_mst  WHERE po_break_down_id in ( $po_ids )   group by po_break_down_id,production_type";
	}
	else
	{
		$sql = "SELECT po_break_down_id, min(production_date) as mind,max(production_date) as maxd, production_type,sum(production_quantity) as production_quantity FROM  pro_garments_production_mst  WHERE po_break_down_id in ( $po_ids )   group by po_break_down_id,production_type";
	}
	
	//echo "asw11_".$sql;die;
	
	
	$result = sql_select( $sql );
	$gmts_prod_array = array();
	$gmts_prod_array_mapping=array(5=>"1",6=>"3",7=>"5",8=>"8");
	foreach( $result as $row ) 
	{
		$gmts_prod_array[$row[csf('po_break_down_id')]][$row[csf('production_type')]]['min'] = $row[csf('mind')];
		$gmts_prod_array[$row[csf('po_break_down_id')]][$row[csf('production_type')]]['max'] = $row[csf('maxd')];
		$gmts_prod_array[$row[csf('po_break_down_id')]][$row[csf('production_type')]]['qnty'] = $row[csf('production_quantity')];
	} 
 
 
	$field_array_tna_process="id,template_id,job_no,po_number_id,po_receive_date,shipment_date,task_category,task_number,target_date,task_start_date,task_finish_date,notice_date_start,notice_date_end,process_date,sequence_no,status_active,is_deleted";
	$field_array_tna_process_up="actual_start_date*actual_finish_date";
	/*$kl=0;
	
	if($kl!=0 && $kl!='' )
	 echo "and sumon";
	else
		echo "sumon";
	 die;*/
	 
	
	 foreach($order_id_array as $poid)   // treat fabric nature knit or woven
	 {
		foreach($tna_task_name_tmp[15] as $fid=>$value)
		{
			if( $tna_task_template_task[$po_order_template[$poid]][15][$value]!=""  )
			{
				
				if($db_type==0)
				{
					$target_date=add_date($po_order_details[$poid]['shipment_date'] ,- $tna_task_template[$po_order_template[$poid]][15][$value]['deadline']);
				}
				else
				{
					$target_date=change_date_format(trim(add_date($po_order_details[$poid]['shipment_date'] ,- $tna_task_template[$po_order_template[$poid]][15][$value]['deadline'])),'','',1);
				}
				
				$to_add_days=$tna_task_template[$po_order_template[$poid]][15][$value]['execution_days']-1;
				
				if($db_type==0)
				{
					$start_date=add_date($target_date ,-$to_add_days);
				}
				else
				{
					$start_date=change_date_format(trim(add_date($target_date ,-$to_add_days)),'','',1);
				}
				
				$finish_date=$target_date;
				$to_add_days=$tna_task_template[$po_order_template[$poid]][15][$value]['notice_before'];
				
				if($db_type==0)
				{
					$notice_date_start=add_date($start_date ,-$to_add_days);
				}
				else
				{
					$notice_date_start=change_date_format(trim(add_date($start_date ,-$to_add_days)),'','',1);
				}
				
				if($db_type==0)
				{
					$notice_date_end=add_date($finish_date ,-$to_add_days);
				}
				else
				{
					$notice_date_end=change_date_format(trim(add_date($finish_date ,-$to_add_days)),'','',1);
				}
				
				if ($tna_process_list[15][$poid][$value]=="")
				{
					if ($id_tna_process=="")$id_tna_process=return_next_id( "id", " tna_process_mst",1); else $id_tna_process+=1;
					if ($data_array_tna_process!="") $data_array_tna_process .=",";
					$data_array_tna_process .="(".$id_tna_process.",".$po_order_template[$poid].",'".$po_order_details[$poid]['job_no_mst']."','".$poid."','".$po_order_details[$poid]['po_received_date']."','".$po_order_details[$poid]['shipment_date']."',15,".$value.",'".$target_date."','".$start_date."','".$finish_date."','".$notice_date_start."','".$notice_date_end."','".$pc_date_time."','".$tna_task_template[$po_order_template[$poid]][15][$value]['sequence_no']."',1,0)";
				}
				else // Update  3 booking from booking table ---if booking found then booking date is start and end date (Same date)
				{
					// Need to be updated by Task wise
			 		//print_r($purchase_array_mapping[$tna_task_name[$value]['task_name']]); echo "==="; 
					$process_id_up=$tna_process_list[15][$poid][$value];
					
					if ($purchase_update_task[$poid][$purchase_array_mapping[$tna_task_name[$value]['task_name']]]['start_date']!="0000-00-00" || $purchase_update_task[$poid][$purchase_array_mapping[$tna_task_name[$value]['task_name']]]['start_date']!="" ) $start_date=$purchase_update_task[$poid][$purchase_array_mapping[$tna_task_name[$value]['task_name']]]['start_date']; else { if($db_type==0) $start_date=$tna_process_details[$process_id_up]['start']; else $start_date=$tna_process_details[$process_id_up]['start']; }
					
					if ( $purchase_update_task[$poid][$purchase_array_mapping[$tna_task_name[$value]['task_name']]]['end_date']!="0000-00-00" || $purchase_update_task[$poid][$purchase_array_mapping[$tna_task_name[$value]['task_name']]]['end_date']!="" ) $finish_date=$purchase_update_task[$poid][$purchase_array_mapping[$tna_task_name[$value]['task_name']]]['end_date']; else { if($db_type==0) $finish_date=$tna_process_details[$process_id_up]['finish']; else $finish_date=$tna_process_details[$process_id_up]['finish']; } 
					
					$process_id_up_array[]=$process_id_up;
					$data_array_tna_process_up[$process_id_up] =explode(",",("'".$start_date."','".$finish_date."'")); 
					//print_r( $data_array_tna_process_up); echo $poid; die;
				}
			}
		}
	}
	   
	
	 
	 //  General Task Start Here
	 foreach($order_id_array as $poid)   // treat fabric nature knit or woven
	 {
		foreach($tna_task_name_tmp[1] as $fid=>$value)
		{
			if( $tna_task_template_task[$po_order_template[$poid]][1][$value]!=""  )
			{
				if($db_type==0)
				{ 
					$target_date=add_date($po_order_details[$poid]['shipment_date'] ,-$tna_task_template[$po_order_template[$poid]][1][$value]['deadline']);
				}
				else
				{
					$target_date=change_date_format(trim(add_date($po_order_details[$poid]['shipment_date'] ,-$tna_task_template[$po_order_template[$poid]][1][$value]['deadline'])),'','',1);
				}
				
				$to_add_days=$tna_task_template[$po_order_template[$poid]][1][$value]['execution_days']-1;
				
				if($db_type==0)
				{
					$start_date=add_date($target_date ,-$to_add_days);
				}
				else
				{
					$start_date=change_date_format(trim(add_date($target_date ,-$to_add_days)),'','',1);
				}
				
				$finish_date=$target_date;
				$to_add_days=$tna_task_template[$po_order_template[$poid]][1][$value]['notice_before'];
				
				if($db_type==0)
				{
					$notice_date_start=add_date($start_date ,-$to_add_days);
				}
				else
				{
					$notice_date_start=change_date_format(trim(add_date($start_date ,-$to_add_days)),'','',1);
				}
				
				if($db_type==0)
				{
					$notice_date_end=add_date($finish_date ,-$to_add_days);
				}
				else
				{
					$notice_date_end=change_date_format(trim(add_date($finish_date ,-$to_add_days)),'','',1);
				}
				
				if ($tna_process_list[1][$poid][$value]=="")
				{
					if ($id_tna_process=="")$id_tna_process=return_next_id( "id", " tna_process_mst",1); else $id_tna_process+=1;
					if ($data_array_tna_process!="") $data_array_tna_process .=",";
					$data_array_tna_process .="(".$id_tna_process.",".$po_order_template[$poid].",'".$po_order_details[$poid]['job_no_mst']."','".$poid."','".$po_order_details[$poid]['po_received_date']."','".$po_order_details[$poid]['shipment_date']."',1,".$value.",'".$target_date."','".$start_date."','".$finish_date."','".$notice_date_start."','".$notice_date_end."','".$pc_date_time."','".$tna_task_template[$po_order_template[$poid]][1][$value]['sequence_no']."',1,0)";
				}
				
				
			}
		}
	}
	
	
	
	// Sample Approval Start Here
	 foreach($order_id_array as $poid)   // treat fabric nature knit or woven
	 {
		foreach( $tna_task_name_tmp[5] as $fid=>$value)
		{
			if( $tna_task_template_task[$po_order_template[$poid]][5][$value]!=""  )
			{
				if($db_type==0)
				{
					$target_date=add_date($po_order_details[$poid]['shipment_date'] ,- $tna_task_template[$po_order_template[$poid]][5][$value]['deadline']);
				}
				else
				{
					$target_date=change_date_format(trim(add_date($po_order_details[$poid]['shipment_date'] ,- $tna_task_template[$po_order_template[$poid]][5][$value]['deadline'])),'','',1);
				}
				$to_add_days=$tna_task_template[$po_order_template[$poid]][5][$value]['execution_days']-1;
				if($db_type==0)
				{
					$start_date=add_date($target_date ,-$to_add_days);
				}
				else
				{
					$start_date=change_date_format(trim(add_date($target_date ,-$to_add_days)),'','',1);
				}
				
				$finish_date=$target_date;
				$to_add_days=$tna_task_template[$po_order_template[$poid]][5][$value]['notice_before'];
				
				if($db_type==0)
				{
					$notice_date_start=add_date($start_date ,-$to_add_days);
				}
				else
				{
					$notice_date_start=change_date_format(trim(add_date($start_date ,-$to_add_days)),'','',1);
				}
				
				if($db_type==0)
				{
					$notice_date_end=add_date($finish_date ,-$to_add_days);
				}
				else
				{
					$notice_date_end=change_date_format(trim(add_date($finish_date ,-$to_add_days)),'','',1);
				}
				
				if ($tna_process_list[5][$poid][$value]=="")
				{
					if ($id_tna_process=="")$id_tna_process=return_next_id( "id", " tna_process_mst",1); else $id_tna_process+=1;
					if ($data_array_tna_process!="") $data_array_tna_process .=",";
					$data_array_tna_process .="(".$id_tna_process.",".$po_order_template[$poid].",'".$po_order_details[$poid]['job_no_mst']."','".$poid."','".$po_order_details[$poid]['po_received_date']."','".$po_order_details[$poid]['shipment_date']."',5,".$value.",'".$target_date."','".$start_date."','".$finish_date."','".$notice_date_start."','".$notice_date_end."','".$pc_date_time."','".$tna_task_template[$po_order_template[$poid]][5][$value]['sequence_no']."',1,0)";
				}
				else // Update  submitted_to_buyer   approval_status_date
				{
					// Need to be updated by Task wise
					
					//print_r($sample_approval_update)."**";
					
					$process_id_up=$tna_process_list[5][$poid][$value];
					if( $tna_task_name[$value]['task_type']==1 )  // submission
					{
						if ( $sample_approval_update[$poid][$tna_task_name[$value]['task_name']]['min_start_date']!="0000-00-00" || $sample_approval_update[$poid][$tna_task_name[$value]['task_name']]['min_start_date']!="" ) $start_date=$sample_approval_update[$poid][$tna_task_name[$value]['task_name']]['min_start_date']; else { if($db_type==0) $start_date=$tna_process_details[$process_id_up]['start']; else $start_date=$tna_process_details[$process_id_up]['start']; }
						
						if ( $sample_approval_update[$poid][$tna_task_name[$value]['task_name']]['max_start_date']!="0000-00-00" || $sample_approval_update[$poid][$tna_task_name[$value]['task_name']]['max_start_date']!="" ) $finish_date=$sample_approval_update[$poid][$tna_task_name[$value]['task_name']]['max_start_date']; else { if($db_type==0) $finish_date=$tna_process_details[$process_id_up]['finish']; else $finish_date=$tna_process_details[$process_id_up]['finish']; } 
						
						$process_id_up_array[]=$process_id_up;
						$data_array_tna_process_up[$process_id_up] =explode(",",("'".$start_date."','".$finish_date."'")); 
					}
					else if( $tna_task_name[$value]['task_type']==2 )
					{
						if ( $sample_approval_update[$poid][$tna_task_name[$value]['task_name']]['min_approval_status_date']!="0000-00-00" || $sample_approval_update[$poid][$tna_task_name[$value]['task_name']]['min_approval_status_date']!="" ) $start_date=$sample_approval_update[$poid][$tna_task_name[$value]['task_name']]['min_approval_status_date'];else { if($db_type==0) $start_date=$tna_process_details[$process_id_up]['start']; else $start_date=$tna_process_details[$process_id_up]['start']; }
						
						if ( $sample_approval_update[$poid][$tna_task_name[$value]['task_name']]['max_approval_status_date']!="0000-00-00" || $sample_approval_update[$poid][$tna_task_name[$value]['task_name']]['max_approval_status_date']!="" ) $finish_date=$sample_approval_update[$poid][$tna_task_name[$value]['task_name']]['max_approval_status_date']; else { if($db_type==0) $finish_date=$tna_process_details[$process_id_up]['finish']; else $finish_date=$tna_process_details[$process_id_up]['finish']; }  
						
						$process_id_up_array[]=$process_id_up;
						$data_array_tna_process_up[$process_id_up] =explode(",",("'".$start_date."','".$finish_date."'")); 
					}
					else
					{
						if ( $sample_approval_update[$poid][$tna_task_name[$value]['task_name']]['min_start_date']!="0000-00-00" || $sample_approval_update[$poid][$tna_task_name[$value]['task_name']]['min_start_date']!="" ) $start_date=$sample_approval_update[$poid][$tna_task_name[$value]['task_name']]['min_start_date']; else { if($db_type==0) $start_date=$tna_process_details[$process_id_up]['start']; else $start_date=$tna_process_details[$process_id_up]['start']; }
						
						if ( $sample_approval_update[$poid][$tna_task_name[$value]['task_name']]['max_approval_status_date']!="0000-00-00" || $sample_approval_update[$poid][$tna_task_name[$value]['task_name']]['max_approval_status_date']!="" ) $finish_date=$sample_approval_update[$poid][$tna_task_name[$value]['task_name']]['max_approval_status_date']; else { if($db_type==0) $finish_date=$tna_process_details[$process_id_up]['finish']; else $finish_date=$tna_process_details[$process_id_up]['finish']; } 
						
						$process_id_up_array[]=$process_id_up;
						$data_array_tna_process_up[$process_id_up] =explode(",",("'".$start_date."','".$finish_date."'")); 
					}
				}
			}
		}
	}
	
	//print_r($data_array_tna_process_up); die;
	//$data_array_tna_process_up=array();
	
	 // Lapdip Approval Start Here
	 foreach($order_id_array as $poid)   // treat fabric nature knit or woven
	 {
		foreach($tna_task_name_tmp[6] as $fid=>$value)
		{
			if( $tna_task_template_task[$po_order_template[$poid]][6][$value]!=""  )
			{
				if($db_type==0)
				{
					$target_date=add_date($po_order_details[$poid]['shipment_date'] ,- $tna_task_template[$po_order_template[$poid]][6][$value]['deadline']);
				}
				else
				{
					$target_date=change_date_format(trim(add_date($po_order_details[$poid]['shipment_date'] ,- $tna_task_template[$po_order_template[$poid]][6][$value]['deadline'])),'','',1);	
				}
				$to_add_days=$tna_task_template[$po_order_template[$poid]][6][$value]['execution_days']-1;
				
				if($db_type==0)
				{
					$start_date=add_date($target_date ,-$to_add_days);
				}
				else
				{
					$start_date=change_date_format(trim(add_date($target_date ,-$to_add_days)),'','',1);
				}
				
				$finish_date=$target_date;
				$to_add_days=$tna_task_template[$po_order_template[$poid]][6][$value]['notice_before'];
				
				if($db_type==0)
				{
					$notice_date_start=add_date($start_date ,-$to_add_days);
				}
				else
				{
					$notice_date_start=change_date_format(trim(add_date($start_date ,-$to_add_days)),'','',1);
				}
				
				if($db_type==0)
				{
					$notice_date_end=add_date($finish_date ,-$to_add_days);
				}
				else
				{
					$notice_date_end=change_date_format(trim(add_date($finish_date ,-$to_add_days)),'','',1);
				}
				
				if ($tna_process_list[6][$poid][$value]=="")
				{
					if ($id_tna_process=="")$id_tna_process=return_next_id( "id", " tna_process_mst",1); else $id_tna_process+=1;
					if ($data_array_tna_process!="") $data_array_tna_process .=",";
					$data_array_tna_process .="(".$id_tna_process.",".$po_order_template[$poid].",'".$po_order_details[$poid]['job_no_mst']."','".$poid."','".$po_order_details[$poid]['po_received_date']."','".$po_order_details[$poid]['shipment_date']."',6,".$value.",'".$target_date."','".$start_date."','".$finish_date."','".$notice_date_start."','".$notice_date_end."','".$pc_date_time."','".$tna_task_template[$po_order_template[$poid]][6][$value]['sequence_no']."',1,0)";
				}
				else // Update  submitted_to_buyer   approval_status_date
				{
					$process_id_up=$tna_process_list[6][$poid][$value];
					if( $tna_task_name[$value]['task_type']==1 )  // submission
					{
						if ( $labdip_update_task[$poid]['min_start_date']!="0000-00-00" || $labdip_update_task[$poid]['min_start_date']!="" ) $start_date=$labdip_update_task[$poid]['min_start_date']; else { if($db_type==0) $start_date=$tna_process_details[$process_id_up]['start']; else $start_date=$tna_process_details[$process_id_up]['start']; }
						
						if ( $labdip_update_task[$poid]['max_start_date']!="0000-00-00" || $labdip_update_task[$poid]['max_start_date']!="" ) $finish_date=$labdip_update_task[$poid]['max_start_date']; else { if($db_type==0) $finish_date=$tna_process_details[$process_id_up]['finish']; else $finish_date=$tna_process_details[$process_id_up]['finish']; } 
						
						$process_id_up_array[]=$process_id_up;
						$data_array_tna_process_up[$process_id_up] =explode(",",("'".$start_date."','".$finish_date."'")); 
					}
					else if( $tna_task_name[$value]['task_type']==2 )
					{
						if ( $labdip_update_task[$poid]['min_approval_status_date']!="0000-00-00" || $labdip_update_task[$poid]['min_approval_status_date']!="" ) $start_date=$labdip_update_task[$poid]['min_approval_status_date']; else { if($db_type==0) $start_date=$tna_process_details[$process_id_up]['start']; else $start_date=$tna_process_details[$process_id_up]['start']; }
						
						if ( $labdip_update_task[$poid]['max_approval_status_date']!="0000-00-00" || $labdip_update_task[$poid]['max_approval_status_date']!="" ) $finish_date=$labdip_update_task[$poid]['max_approval_status_date']; else { if($db_type==0) $finish_date=$tna_process_details[$process_id_up]['finish']; else $finish_date=$tna_process_details[$process_id_up]['finish']; } 
						
						$process_id_up_array[]=$process_id_up;
						$data_array_tna_process_up[$process_id_up] =explode(",",("'".$start_date."','".$finish_date."'")); 
					}
					else
					{
						if ( $labdip_update_task[$poid]['min_start_date']!="0000-00-00" || $labdip_update_task[$poid]['min_start_date']!="" ) $start_date=$labdip_update_task[$poid][$tna_task_name[$value]['task_name']]['min_start_date']; else { if($db_type==0) $start_date=$tna_process_details[$process_id_up]['start']; else $start_date=$tna_process_details[$process_id_up]['start']; }
						
						if ( $labdip_update_task[$poid]['max_approval_status_date']!="0000-00-00" || $labdip_update_task[$poid]['max_approval_status_date']!="" ) $finish_date=$labdip_update_task[$poid]['max_approval_status_date']; else { if($db_type==0) $finish_date=$tna_process_details[$process_id_up]['finish']; else $finish_date=$tna_process_details[$process_id_up]['finish']; } 
						
						$process_id_up_array[]=$process_id_up;
						$data_array_tna_process_up[$process_id_up] =explode(",",("'".$start_date."','".$finish_date."'")); 
					}
				}
			}
		}
	}
	
	
	 // Accessories Approval Start Here
	 foreach($order_id_array as $poid)   // treat fabric nature knit or woven
	 {
		foreach($tna_task_name_tmp[7] as $fid=>$value)
		{
			if( $tna_task_template_task[$po_order_template[$poid]][7][$value]!=""  )
			{
				if($db_type==0)
				{
					$target_date=add_date($po_order_details[$poid]['shipment_date'] ,- $tna_task_template[$po_order_template[$poid]][7][$value]['deadline']);
				}
				else
				{
					$target_date=change_date_format(trim(add_date($po_order_details[$poid]['shipment_date'] ,- $tna_task_template[$po_order_template[$poid]][7][$value]['deadline'])),'','',1);
				}
				
				$to_add_days=$tna_task_template[$po_order_template[$poid]][7][$value]['execution_days']-1;
				if($db_type==0)
				{
					$start_date=add_date($target_date ,-$to_add_days);
				}
				else
				{
					$start_date=change_date_format(trim(add_date($target_date ,-$to_add_days)),'','',1);
				}
				
				$finish_date=$target_date;
				$to_add_days=$tna_task_template[$po_order_template[$poid]][7][$value]['notice_before'];
				
				if($db_type==0)
				{
					$notice_date_start=add_date($start_date ,-$to_add_days);
				}
				else
				{
					$notice_date_start=change_date_format(trim(add_date($start_date ,-$to_add_days)),'','',1);
				}
				
				if($db_type==0)
				{
					$notice_date_end=add_date($finish_date ,-$to_add_days);
				}
				else
				{
					$notice_date_end=change_date_format(trim(add_date($finish_date ,-$to_add_days)),'','',1);
				}
				
				if ($tna_process_list[7][$poid][$value]=="")
				{
					if ($id_tna_process=="")$id_tna_process=return_next_id( "id", " tna_process_mst",1); else $id_tna_process+=1;
					if ($data_array_tna_process!="") $data_array_tna_process .=",";
					$data_array_tna_process .="(".$id_tna_process.",".$po_order_template[$poid].",'".$po_order_details[$poid]['job_no_mst']."','".$poid."','".$po_order_details[$poid]['po_received_date']."','".$po_order_details[$poid]['shipment_date']."',7,".$value.",'".$target_date."','".$start_date."','".$finish_date."','".$notice_date_start."','".$notice_date_end."','".$pc_date_time."','".$tna_task_template[$po_order_template[$poid]][7][$value]['sequence_no']."',1,0)";
				}
				else // Update  min submitted_to_buyer ,max submitted_to_buyer,   same approval_status_date
				{
					// Need to be updated by Task wise
					$process_id_up=$tna_process_list[7][$poid][$value];
					$tmptask=$trim_type_array[$tna_task_name[$value]['task_name']]; 
					//echo  $trims_update_task[$poid][1]['min_approval_status_date']; die;
					if( $tna_task_name[$value]['task_type']==1 )  // submission
					{
						if ( $trims_update_task[$poid][$tmptask]['min_start_date']!="0000-00-00" || $trims_update_task[$poid][$tmptask]['min_start_date']!="" ) $start_date=$trims_update_task[$poid][$tmptask]['min_start_date']; else { if($db_type==0) $start_date=$tna_process_details[$process_id_up]['start']; else $start_date=$tna_process_details[$process_id_up]['start']; }
						
						if ( $trims_update_task[$poid][$tmptask]['max_start_date']!="0000-00-00" || $trims_update_task[$poid][$tmptask]['max_start_date']!="" ) $finish_date=$trims_update_task[$poid][$tmptask]['max_start_date']; else { if($db_type==0) $finish_date=$tna_process_details[$process_id_up]['finish']; else $finish_date=$tna_process_details[$process_id_up]['finish']; } 
						
						$process_id_up_array[]=$process_id_up;
						$data_array_tna_process_up[$process_id_up] =explode(",",("'".$start_date."','".$finish_date."'")); 
					}
					else if( $tna_task_name[$value]['task_type']==2 )
					{
						
						if ( $trims_update_task[$poid][$tmptask]['min_approval_status_date']!="0000-00-00" || $trims_update_task[$poid][$tmptask]['min_approval_status_date']!="" ) $start_date=$trims_update_task[$poid][$tmptask]['min_approval_status_date']; else { if($db_type==0) $start_date=$tna_process_details[$process_id_up]['start']; else $start_date=$tna_process_details[$process_id_up]['start']; }
						
						if ( $trims_update_task[$poid][$tmptask]['max_approval_status_date']!="0000-00-00" || $trims_update_task[$poid][$tmptask]['max_approval_status_date']!="" ) $finish_date=$trims_update_task[$poid][$tmptask]['max_approval_status_date']; else { if($db_type==0) $finish_date=$tna_process_details[$process_id_up]['finish']; else $finish_date=$tna_process_details[$process_id_up]['finish']; } 
						
						$process_id_up_array[]=$process_id_up;
						$data_array_tna_process_up[$process_id_up] =explode(",",("'".$start_date."','".$finish_date."'")); 
					}
					else
					{
						if ( $trims_update_task[$poid][$tmptask]['min_start_date']!="0000-00-00" || $trims_update_task[$poid][$tmptask]['min_start_date']!="" ) $start_date=$trims_update_task[$poid][$tmptask]['min_start_date']; else { if($db_type==0) $start_date=$tna_process_details[$process_id_up]['start']; else $start_date=$tna_process_details[$process_id_up]['start']; }
						if ( $trims_update_task[$poid][$tmptask]['max_approval_status_date']!="0000-00-00" || $trims_update_task[$poid][$tmptask]['max_approval_status_date']!="" ) $finish_date=$trims_update_task[$poid][$tmptask]['max_approval_status_date']; else { if($db_type==0) $finish_date=$tna_process_details[$process_id_up]['finish']; else $finish_date=$tna_process_details[$process_id_up]['finish']; } 
						$process_id_up_array[]=$process_id_up;
						$data_array_tna_process_up[$process_id_up] =explode(",",("'".$start_date."','".$finish_date."'")); 
					}
				}
			}
		}
	}
	
	 
	
	 // Embellishment Approval Start Here
	 foreach($order_id_array as $poid)   // treat fabric nature knit or woven
	 {
		foreach($tna_task_name_tmp[8] as $fid=>$value)
		{
			if( $tna_task_template_task[$po_order_template[$poid]][8][$value]!=""  )
			{
				if($db_type==0)
				{
					$target_date=add_date($po_order_details[$poid]['shipment_date'] ,- $tna_task_template[$po_order_template[$poid]][8][$value]['deadline']);
				}
				else
				{
					$target_date=change_date_format(trim(add_date($po_order_details[$poid]['shipment_date'] ,- $tna_task_template[$po_order_template[$poid]][8][$value]['deadline'])),'','',1);
				}
				
				$to_add_days=$tna_task_template[$po_order_template[$poid]][8][$value]['execution_days']-1;
				
				if($db_type==0)
				{
					$start_date=add_date($target_date ,-$to_add_days);
				}
				else
				{
					$start_date=change_date_format(trim(add_date($target_date ,-$to_add_days)),'','',1);
				}
				
				$finish_date=$target_date;
				$to_add_days=$tna_task_template[$po_order_template[$poid]][8][$value]['notice_before'];
				
				if($db_type==0)
				{
					$notice_date_start=add_date($start_date ,-$to_add_days);
				}
				else
				{
					$notice_date_start=change_date_format(trim(add_date($start_date ,-$to_add_days)),'','',1);
				}
				
				if($db_type==0)
				{
					$notice_date_end=add_date($finish_date ,-$to_add_days);
				}
				else
				{
					$notice_date_end=change_date_format(trim(add_date($finish_date ,-$to_add_days)),'','',1);
				}
				
				if ($tna_process_list[8][$poid][$value]=="")
				{
					if ($id_tna_process=="")$id_tna_process=return_next_id( "id", " tna_process_mst",1); else $id_tna_process+=1;
					if ($data_array_tna_process!="") $data_array_tna_process .=",";
					$data_array_tna_process .="(".$id_tna_process.",".$po_order_template[$poid].",'".$po_order_details[$poid]['job_no_mst']."','".$poid."','".$po_order_details[$poid]['po_received_date']."','".$po_order_details[$poid]['shipment_date']."',8,".$value.",'".$target_date."','".$start_date."','".$finish_date."','".$notice_date_start."','".$notice_date_end."','".$pc_date_time."','".$tna_task_template[$po_order_template[$poid]][8][$value]['sequence_no']."',1,0)";
				}
				else // Update  submitted_to_buyer   approval_status_date
				{
					// Need to be updated by Task wise
					$process_id_up=$tna_process_list[8][$poid][$value];
					if( $tna_task_name[$value]['task_type']==1 )  // submission
					{
						if ( $embelishment_update_task[$poid][$tna_task_name[$value]['task_name']]['min_start_date']!="0000-00-00" || $embelishment_update_task[$poid][$tna_task_name[$value]['task_name']]['min_start_date']!="" ) $start_date=$embelishment_update_task[$poid][$tna_task_name[$value]['task_name']]['min_start_date']; else { if($db_type==0) $start_date=$tna_process_details[$process_id_up]['start']; else $start_date=$tna_process_details[$process_id_up]['start']; }
						
						if ( $embelishment_update_task[$poid][$tna_task_name[$value]['task_name']]['max_start_date']!="0000-00-00" || $embelishment_update_task[$poid][$tna_task_name[$value]['task_name']]['max_start_date']!="" ) $finish_date=$embelishment_update_task[$poid][$tna_task_name[$value]['task_name']]['max_start_date']; else { if($db_type==0) $finish_date=$tna_process_details[$process_id_up]['finish']; else $finish_date=$tna_process_details[$process_id_up]['finish']; } 
						
						$process_id_up_array[]=$process_id_up;
						$data_array_tna_process_up[$process_id_up] =explode(",",("'".$start_date."','".$finish_date."'")); 
					}
					else if( $tna_task_name[$value]['task_type']==2 )
					{
						if ( $embelishment_update_task[$poid][$tna_task_name[$value]['task_name']]['min_approval_status_date']!="0000-00-00" || $embelishment_update_task[$poid][$tna_task_name[$value]['task_name']]['min_approval_status_date']!="" ) $start_date=$embelishment_update_task[$poid][$tna_task_name[$value]['task_name']]['min_approval_status_date']; else { if($db_type==0) $start_date=$tna_process_details[$process_id_up]['start']; else $start_date=$tna_process_details[$process_id_up]['start']; }
						
						if ( $embelishment_update_task[$poid][$tna_task_name[$value]['task_name']]['max_approval_status_date']!="0000-00-00" || $embelishment_update_task[$poid][$tna_task_name[$value]['task_name']]['max_approval_status_date']!="" ) $finish_date=$embelishment_update_task[$poid][$tna_task_name[$value]['task_name']]['max_approval_status_date']; else { if($db_type==0) $finish_date=$tna_process_details[$process_id_up]['finish']; else $finish_date=$tna_process_details[$process_id_up]['finish']; } 
						
						$process_id_up_array[]=$process_id_up;
						$data_array_tna_process_up[$process_id_up] =explode(",",("'".$start_date."','".$finish_date."'")); 
					}
					else
					{
						if ( $embelishment_update_task[$poid][$tna_task_name[$value]['task_name']]['min_start_date']!="0000-00-00" || $embelishment_update_task[$poid][$tna_task_name[$value]['task_name']]['min_start_date']!="" ) $start_date=$embelishment_update_task[$poid][$tna_task_name[$value]['task_name']][$tna_task_name[$value]['task_name']]['min_start_date']; else { if($db_type==0) $start_date=$tna_process_details[$process_id_up]['start']; else $start_date=$tna_process_details[$process_id_up]['start']; }
						
						if ( $embelishment_update_task[$poid][$tna_task_name[$value]['task_name']]['max_approval_status_date']!="0000-00-00" || $embelishment_update_task[$poid][$tna_task_name[$value]['task_name']]['max_approval_status_date']!="" ) $finish_date=$embelishment_update_task[$poid][$tna_task_name[$value]['task_name']]['max_approval_status_date']; else { if($db_type==0) $finish_date=$tna_process_details[$process_id_up]['finish']; else $finish_date=$tna_process_details[$process_id_up]['finish']; } 
						
						$process_id_up_array[]=$process_id_up;
						$data_array_tna_process_up[$process_id_up] =explode(",",("'".$start_date."','".$finish_date."'")); 
					}
				}
			}
		}
	}
	
	
	
	 // Test  Approval Start Here
	 foreach($order_id_array as $poid)   // treat fabric nature knit or woven
	 {
		foreach($tna_task_name_tmp[9] as $fid=>$value)
		{
			if( $tna_task_template_task[$po_order_template[$poid]][9][$value]!=""  )
			{
				if($db_type==0)
				{
					$target_date=add_date($po_order_details[$poid]['shipment_date'] ,- $tna_task_template[$po_order_template[$poid]][9][$value]['deadline']);
				}
				else
				{
					$target_date=change_date_format(trim(add_date($po_order_details[$poid]['shipment_date'] ,- $tna_task_template[$po_order_template[$poid]][9][$value]['deadline'])),'','',1);
				}
				
				$to_add_days=$tna_task_template[$po_order_template[$poid]][9][$value]['execution_days']-1;
				
				if($db_type==0)
				{
					$start_date=add_date($target_date ,-$to_add_days);
				}
				else
				{
					$start_date=change_date_format(trim(add_date($target_date ,-$to_add_days)),'','',1);
				}
				
				$finish_date=$target_date;
				$to_add_days=$tna_task_template[$po_order_template[$poid]][9][$value]['notice_before'];
				
				if($db_type==0)
				{
					$notice_date_start=add_date($start_date ,-$to_add_days);
				}
				else
				{
					$notice_date_start=change_date_format(trim(add_date($start_date ,-$to_add_days)),'','',1);
				}
				
				if($db_type==0)
				{
					$notice_date_end=add_date($finish_date ,-$to_add_days);
				}
				else
				{
					$notice_date_end=change_date_format(trim(add_date($finish_date ,-$to_add_days)),'','',1);
				}
				
				if ($tna_process_list[9][$poid][$value]=="")
				{
					if ($id_tna_process=="")$id_tna_process=return_next_id( "id", " tna_process_mst",1); else $id_tna_process+=1;
					if ($data_array_tna_process!="") $data_array_tna_process .=",";
					$data_array_tna_process .="(".$id_tna_process.",".$po_order_template[$poid].",'".$po_order_details[$poid]['job_no_mst']."','".$poid."','".$po_order_details[$poid]['po_received_date']."','".$po_order_details[$poid]['shipment_date']."',9,".$value.",'".$target_date."','".$start_date."','".$finish_date."','".$notice_date_start."','".$notice_date_end."','".$pc_date_time."','".$tna_task_template[$po_order_template[$poid]][9][$value]['sequence_no']."',1,0)";
				}
			}
		}
	}
	
	
	
	
	 // Material Receive Task Start Here
	 foreach($order_id_array as $poid)   // treat fabric nature knit or woven
	 {

		foreach($tna_task_name_tmp[20] as $fid=>$value)
		{
//print_r($tna_task_template_task[$po_order_template[$poid]]); die;
			if( $tna_task_template_task[$po_order_template[$poid]][20][$value]!=""  )
			{
				if($db_type==0)
				{
					$target_date=add_date($po_order_details[$poid]['shipment_date'] ,- $tna_task_template[$po_order_template[$poid]][20][$value]['deadline']);
				}
				else
				{
					$target_date=change_date_format(trim(add_date($po_order_details[$poid]['shipment_date'] ,- $tna_task_template[$po_order_template[$poid]][20][$value]['deadline'])),'','',1);
				}
				
				$to_add_days=$tna_task_template[$po_order_template[$poid]][20][$value]['execution_days']-1;
				
				if($db_type==0)
				{
					$start_date=add_date($target_date ,-$to_add_days);
				}
				else
				{
					$start_date=change_date_format(trim(add_date($target_date ,-$to_add_days)),'','',1);
				}
				
				$finish_date=$target_date;
				$to_add_days=$tna_task_template[$po_order_template[$poid]][20][$value]['notice_before'];
				
				if($db_type==0)
				{
					$notice_date_start=add_date($start_date ,-$to_add_days);
				}
				else
				{
					$notice_date_start=change_date_format(trim(add_date($start_date ,-$to_add_days)),'','',1);
				}
				
				if($db_type==0)
				{
					$notice_date_end=add_date($finish_date ,-$to_add_days);
				}
				else
				{
					$notice_date_end=change_date_format(trim(add_date($finish_date ,-$to_add_days)),'','',1);
				}
				
				
				if ($tna_process_list[20][$poid][$value]=="")
				{
					if ($id_tna_process=="")$id_tna_process=return_next_id( "id", " tna_process_mst",1); else $id_tna_process+=1;
					if ($data_array_tna_process!="") $data_array_tna_process .=",";
					$data_array_tna_process .="(".$id_tna_process.",".$po_order_template[$poid].",'".$po_order_details[$poid]['job_no_mst']."','".$poid."','".$po_order_details[$poid]['po_received_date']."','".$po_order_details[$poid]['shipment_date']."',20,".$value.",'".$target_date."','".$start_date."','".$finish_date."','".$notice_date_start."','".$notice_date_end."','".$pc_date_time."','".$tna_task_template[$po_order_template[$poid]][20][$value]['sequence_no']."',1,0)";
				}
				else // Update
				{
					// Need to be updated by Task wise
//echo $value."=";
//print_r($tna_task_name);
//echo $inv_array_mapping[$tna_task_name[$value]['task_name']]."==".$inventory_transaction_update[$poid][$inv_array_mapping[$tna_task_name[$value]['task_name']]]['start_date']."*";

					$process_id_up=$tna_process_list[20][$poid][$value];
					if ( $inventory_transaction_update[$poid][$inv_array_mapping[$tna_task_name[$value]['task_name']]]['start_date']!="0000-00-00" || $inventory_transaction_update[$poid][$inv_array_mapping[$tna_task_name[$value]['task_name']]]['start_date']!="" ) $start_date=$inventory_transaction_update[$poid][$inv_array_mapping[$tna_task_name[$value]['task_name']]]['start_date']; else { if($db_type==0) $start_date=$tna_process_details[$process_id_up]['start']; else $start_date=$tna_process_details[$process_id_up]['start']; }
					
					if ( $inventory_transaction_update[$poid][$inv_array_mapping[$tna_task_name[$value]['task_name']]]['end_date']!="0000-00-00" || $inventory_transaction_update[$poid][$inv_array_mapping[$tna_task_name[$value]['task_name']]]['end_date']!="" ) $finish_date=$inventory_transaction_update[$poid][$inv_array_mapping[$tna_task_name[$value]['task_name']]]['end_date']; else { if($db_type==0) $finish_date=$tna_process_details[$process_id_up]['finish']; else $finish_date=$tna_process_details[$process_id_up]['finish']; } 
					
					$process_id_up_array[]=$process_id_up;
					$data_array_tna_process_up[$process_id_up] =explode(",",("'".$start_date."','".$finish_date."'")); 
				}
			}
//echo $process_id_up."==";
		}
	}
 
	 
	
	 // Fabric Production Task Start Here 
	 foreach($order_id_array as $poid)   // treat fabric nature knit or woven
	 {
		foreach($tna_task_name_tmp[25] as $fid=>$value)
		{
			if( $tna_task_template_task[$po_order_template[$poid]][25][$value]!=""  )
			{
				if($db_type==0)
				{
					$target_date=add_date($po_order_details[$poid]['shipment_date'] ,- $tna_task_template[$po_order_template[$poid]][25][$value]['deadline']);
				}
				else
				{
					$target_date=change_date_format(trim(add_date($po_order_details[$poid]['shipment_date'] ,- $tna_task_template[$po_order_template[$poid]][25][$value]['deadline'])),'','',1);
				}
				
				$to_add_days=$tna_task_template[$po_order_template[$poid]][25][$value]['execution_days']-1;
				
				if($db_type==0)
				{
					$start_date=add_date($target_date ,-$to_add_days);
				}
				else
				{
					$start_date=change_date_format(trim(add_date($target_date ,-$to_add_days)),'','',1);
				}
				
				$finish_date=$target_date;
				$to_add_days=$tna_task_template[$po_order_template[$poid]][25][$value]['notice_before'];
				
				if($db_type==0)
				{
					$notice_date_start=add_date($start_date ,-$to_add_days);
				}
				else
				{
					$notice_date_start=change_date_format(trim(add_date($start_date ,-$to_add_days)),'','',1);
				}
				
				if($db_type==0)
				{
					$notice_date_end=add_date($finish_date ,-$to_add_days);
				}
				else
				{
					$notice_date_end=change_date_format(trim(add_date($finish_date ,-$to_add_days)),'','',1);
				}
				
				if ($tna_process_list[25][$poid][$value]=="")
				{
					if ($id_tna_process=="")$id_tna_process=return_next_id( "id", " tna_process_mst",1); else $id_tna_process+=1;
					if ($data_array_tna_process!="") $data_array_tna_process .=",";
					$data_array_tna_process .="(".$id_tna_process.",".$po_order_template[$poid].",'".$po_order_details[$poid]['job_no_mst']."','".$poid."','".$po_order_details[$poid]['po_received_date']."','".$po_order_details[$poid]['shipment_date']."',25,".$value.",'".$target_date."','".$start_date."','".$finish_date."','".$notice_date_start."','".$notice_date_end."','".$pc_date_time."','".$tna_task_template[$po_order_template[$poid]][25][$value]['sequence_no']."',1,0)";
				}
				else // Update
				{
					// Need to be updated by Task wise
					//echo $poid."--".$fabric_prod_transaction_update[$poid][$fab_prod_array_mapping[$tna_task_name[$value]['task_name']]]['end_date'];
					$process_id_up=$tna_process_list[25][$poid][$value];
					
					if ( $fabric_prod_transaction_update[$poid][$fab_prod_array_mapping[$tna_task_name[$value]['task_name']]]['start_date']!="0000-00-00" || $fabric_prod_transaction_update[$poid][$fab_prod_array_mapping[$tna_task_name[$value]['task_name']]]['start_date']!="" ) $start_date=$fabric_prod_transaction_update[$poid][$fab_prod_array_mapping[$tna_task_name[$value]['task_name']]]['start_date']; else { if($db_type==0) $start_date=$tna_process_details[$process_id_up]['start']; else $start_date=$tna_process_details[$process_id_up]['start']; }
					 
					if( $fab_prod_array_mapping[$tna_task_name[$value]['task_name']]!=6)
						$compl_perc=get_percent($fabric_prod_transaction_update[$poid][$fab_prod_array_mapping[$tna_task_name[$value]['task_name']]]['quantity'], $po_order_details[$poid]['po_quantity']);
					else
						$compl_perc=get_percent($fabric_prod_transaction_update[$poid][$fab_prod_array_mapping[$tna_task_name[$value]['task_name']]]['quantity'], $po_wise_booking_qnty[$poid]);
					//if($fab_prod_array_mapping[$tna_task_name[$value]['task_name']]==2) { print_r($tna_task_details[25][$fab_prod_array_mapping[$tna_task_name[$value]['task_name']]]['completion_percent']);die; }
					if ($compl_perc>=$tna_task_details[25][$fab_prod_array_mapping[$tna_task_name[$value]['task_name']]]['completion_percent'])
					{
						
						if( $tna_process_details[$process_id_up]['finish']!="0000-00-00" && $tna_process_details[$process_id_up]['finish']!="" ) $finish_date=$tna_process_details[$process_id_up]['finish']; 
						else $finish_date=($fabric_prod_transaction_update[$poid][$fab_prod_array_mapping[$tna_task_name[$value]['task_name']]]['end_date'] == "" ? $blank_date : $fabric_prod_transaction_update[$poid][$fab_prod_array_mapping[$tna_task_name[$value]['task_name']]]['end_date']); 
						
					}
					
					else { if($db_type==0) $finish_date=$tna_process_details[$process_id_up]['finish']; else $finish_date=$tna_process_details[$process_id_up]['finish']; } 
						
					//if ( $inventory_transaction_update[$poid][$inv_array_mapping[$tna_task_name[$value]['task_name']]]['end_date']!="0000-00-00" || $inventory_transaction_update[$poid][$inv_array_mapping[$tna_task_name[$value]['task_name']]]['end_date']!="" ) $finish_date=$inventory_transaction_update[$poid][$inv_array_mapping[$tna_task_name[$value]['task_name']]]['end_date']; else $finish_date="0000-00-00";
					$process_id_up_array[]=$process_id_up;
					$data_array_tna_process_up[$process_id_up] =explode(",",("'".$start_date."','".$finish_date."'"));
				}
			}
		}
	}
	
	
	
	 // Garments Production Task Start Here
	 foreach($order_id_array as $poid)   // treat fabric nature knit or woven
	 {  // Need Cunsultancy abt task detais
		foreach( $tna_task_name_tmp[26] as $fid=>$value )
		{
			if( $tna_task_template_task[$po_order_template[$poid]][26][$value]!=""  )
			{
				if($db_type==0)
				{
					$target_date=add_date($po_order_details[$poid]['shipment_date'] ,- $tna_task_template[$po_order_template[$poid]][26][$value]['deadline']);
				}
				else
				{
					$target_date=change_date_format(trim(add_date($po_order_details[$poid]['shipment_date'] ,- $tna_task_template[$po_order_template[$poid]][26][$value]['deadline'])),'','',1);
				}
				
				$to_add_days=$tna_task_template[$po_order_template[$poid]][26][$value]['execution_days']-1;
				
				if($db_type==0)
				{
					$start_date=add_date($target_date ,-$to_add_days);
				}
				else
				{
					$start_date=change_date_format(trim(add_date($target_date ,-$to_add_days)),'','',1);
				}
				
				$finish_date=$target_date;
				$to_add_days=$tna_task_template[$po_order_template[$poid]][26][$value]['notice_before'];
				
				if($db_type==0)
				{
					$notice_date_start=add_date($start_date ,-$to_add_days);
				}
				else
				{
					$notice_date_start=change_date_format(trim(add_date($start_date ,-$to_add_days)),'','',1);
				}
				
				if($db_type==0)
				{
					$notice_date_end=add_date($finish_date ,-$to_add_days);
				}
				else
				{
					$notice_date_end=change_date_format(trim(add_date($finish_date ,-$to_add_days)),'','',1);
				}
				
				if ($tna_process_list[26][$poid][$value]=="")
				{
					if ($id_tna_process=="")$id_tna_process=return_next_id( "id", " tna_process_mst",1); else $id_tna_process+=1;
					if ($data_array_tna_process!="") $data_array_tna_process .=",";
					$data_array_tna_process .="(".$id_tna_process.",".$po_order_template[$poid].",'".$po_order_details[$poid]['job_no_mst']."','".$poid."','".$po_order_details[$poid]['po_received_date']."','".$po_order_details[$poid]['shipment_date']."',26,".$value.",'".$target_date."','".$start_date."','".$finish_date."','".$notice_date_start."','".$notice_date_end."','".$pc_date_time."','".$tna_task_template[$po_order_template[$poid]][26][$value]['sequence_no']."',1,0)";
				}
				else // Update
				{
					// Need to be updated by Task wise
					//tna_task_template_type[$row[csf("task_template_id")]][$row[csf("task_catagory")]]['type']
					 
					$process_id_up=$tna_process_list[26][$poid][$value];
					
					if ( $gmts_prod_array[$poid][$gmts_prod_array_mapping[$tna_task_name[$value]['task_name']]]['min']!="0000-00-00" || $gmts_prod_array[$poid][$gmts_prod_array_mapping[$tna_task_name[$value]['task_name']]]['min']!="" ) $start_date=$gmts_prod_array[$poid][$gmts_prod_array_mapping[$tna_task_name[$value]['task_name']]]['min']; else { if($db_type==0) $start_date=$tna_process_details[$process_id_up]['start']; else $start_date=$tna_process_details[$process_id_up]['start']; }
					
					if (get_percent($gmts_prod_array[$poid][$gmts_prod_array_mapping[$tna_task_name[$value]['task_name']]]['qnty'], $po_order_details[$poid]['po_quantity'])>=$tna_task_details[26][$gmts_prod_array_mapping[$tna_task_name[$value]['task_name']]]['completion_percent'])
					{
						if( $tna_process_details[$process_id_up]['finish']!="0000-00-00" && $tna_process_details[$process_id_up]['finish']!="" ) $finish_date=$tna_process_details[$process_id_up]['finish'];
						else
						{
							if(trim($gmts_prod_array[$poid][$gmts_prod_array_mapping[$tna_task_name[$value]['task_name']]]['max']) == "")
								$finish_date= $blank_date;
							else
								$finish_date=$gmts_prod_array[$poid][$gmts_prod_array_mapping[$tna_task_name[$value]['task_name']]]['max']; 
								//if($gmts_prod_array_mapping[$tna_task_name[$value]['task_name']]==1){  echo "==".$finish_date."==" ;die; }
						}
						
					}
					
					//else { if($db_type==0) $finish_date=$tna_process_details[$process_id_up]['finish']; else $finish_date=$tna_process_details[$process_id_up]['finish']; } 
					
					//if ( $gmts_prod_array[$poid][$gmts_prod_array_mapping[$tna_task_name[$value]['task_name']]]['max']!="0000-00-00" || $gmts_prod_array[$poid][$gmts_prod_array_mapping[$tna_task_name[$value]['task_name']]]['max']!="" ) $finish_date=$gmts_prod_array[$poid][$gmts_prod_array_mapping[$tna_task_name[$value]['task_name']]]['max']; else $finish_date="0000-00-00";
					$process_id_up_array[]=$process_id_up;
					$data_array_tna_process_up[$process_id_up] =explode(",",("'".$start_date."','".$finish_date."'"));
				}
			}
		}
	 }
	 
	 
	
	 // Inspection Task Start Here  Update Completed
	 foreach($order_id_array as $poid)   // treat fabric nature knit or woven
	 {
		foreach($tna_task_name_tmp[30] as $fid=>$value)
		{
			if( $tna_task_template_task[$po_order_template[$poid]][30][$value]!=""  )
			{
				if($db_type==0)
				{
					$target_date=add_date($po_order_details[$poid]['shipment_date'] ,- $tna_task_template[$po_order_template[$poid]][30][$value]['deadline']);
				}
				else
				{
					$target_date=change_date_format(trim(add_date($po_order_details[$poid]['shipment_date'] ,- $tna_task_template[$po_order_template[$poid]][30][$value]['deadline'])),'','',1);
				}
				
				$to_add_days=$tna_task_template[$po_order_template[$poid]][30][$value]['execution_days']-1;
				
				if($db_type==0)
				{
					$start_date=add_date($target_date ,-$to_add_days);
				}
				else
				{
					$start_date=change_date_format(trim(add_date($target_date ,-$to_add_days)),'','',1);
				}
				
				$finish_date=$target_date;
				$to_add_days=$tna_task_template[$po_order_template[$poid]][30][$value]['notice_before'];
				
				if($db_type==0)
				{
					$notice_date_start=add_date($start_date ,-$to_add_days);
				}
				else
				{
					$notice_date_start=change_date_format(trim(add_date($start_date ,-$to_add_days)),'','',1);
				}
				
				if($db_type==0)
				{
					$notice_date_end=add_date($finish_date ,-$to_add_days);
				}
				else
				{
					$notice_date_end=change_date_format(trim(add_date($finish_date ,-$to_add_days)),'','',1);
				}
				
				if ($tna_process_list[30][$poid][$value]=="")
				{
					if ($id_tna_process=="")$id_tna_process=return_next_id( "id", " tna_process_mst",1); else $id_tna_process+=1;
					if ($data_array_tna_process!="") $data_array_tna_process .=",";
					$data_array_tna_process .="(".$id_tna_process.",".$po_order_template[$poid].",'".$po_order_details[$poid]['job_no_mst']."','".$poid."','".$po_order_details[$poid]['po_received_date']."','".$po_order_details[$poid]['shipment_date']."',30,".$value.",'".$target_date."','".$start_date."','".$finish_date."','".$notice_date_start."','".$notice_date_end."','".$pc_date_time."','".$tna_task_template[$po_order_template[$poid]][30][$value]['sequence_no']."',1,0)";
				}
				else // Update
				{
					
					//tna_task_template_type[$row[csf("task_template_id")]][$row[csf("task_catagory")]]['type']
					//Update from Inspection table  total pass qnty to be compared with task completion %
					$process_id_up=$tna_process_list[30][$poid][$value];
					
					if ( $tna_process_details[$process_id_up]['start']!="0000-00-00" && $tna_process_details[$process_id_up]['start']!="" ) $start_date=$tna_process_details[$process_id_up]['start']; 
					
					else $start_date= ($inspection_status_array[$poid]['min'] == "" ? $blank_date : $inspection_status_array[$poid]['min']);
					
					if (get_percent($inspection_status_array[$poid]['qnty'], $po_order_details[$poid]['po_quantity'])>=$tna_task_details[30][$value]['completion_percent'])
					{
						if( $tna_process_details[$process_id_up]['finish']!="0000-00-00" && $tna_process_details[$process_id_up]['finish']!="" ) $finish_date=$tna_process_details[$process_id_up]['finish']; 
						else $finish_date=($inspection_status_array[$poid]['max'] == "" ? $blank_date : $inspection_status_array[$poid]['max']); 
					}
					
					else { if($db_type==0) $finish_date=$tna_process_details[$process_id_up]['finish']; else $finish_date=$tna_process_details[$process_id_up]['finish']; } 
					
					$process_id_up_array[]=$process_id_up;
					$data_array_tna_process_up[$process_id_up] =explode(",",("'".$start_date."','".$finish_date."'"));
				}
			}
		}

	}
	
	
	
	 // Export Task Start Here
	 foreach($order_id_array as $poid)   // treat fabric nature knit or woven
	 {
		foreach($tna_task_name_tmp[35] as $fid=>$value)
		{
			if( $tna_task_template_task[$po_order_template[$poid]][35][$value]!=""  )
			{
				if($db_type==0)
				{
					$target_date=add_date($po_order_details[$poid]['shipment_date'] ,- $tna_task_template[$po_order_template[$poid]][35][$value]['deadline']);
				}
				else
				{
					$target_date=change_date_format(trim(add_date($po_order_details[$poid]['shipment_date'] ,- $tna_task_template[$po_order_template[$poid]][35][$value]['deadline'])),'','',1);
				}
				
				$to_add_days=$tna_task_template[$po_order_template[$poid]][35][$value]['execution_days']-1;
				
				if($db_type==0)
				{
					$start_date=add_date($target_date ,-$to_add_days);
				}
				else
				{
					$start_date=change_date_format(trim(add_date($target_date ,-$to_add_days)),'','',1);
				}
				
				$finish_date=$target_date;
				$to_add_days=$tna_task_template[$po_order_template[$poid]][35][$value]['notice_before'];
				
				if($db_type==0)
				{
					$notice_date_start=add_date($start_date ,-$to_add_days);
				}
				else
				{
					$notice_date_start=change_date_format(trim(add_date($start_date ,-$to_add_days)),'','',1);
				}
				
				if($db_type==0)
				{
					$notice_date_end=add_date($finish_date ,-$to_add_days);
				}
				else
				{
					$notice_date_end=change_date_format(trim(add_date($finish_date ,-$to_add_days)),'','',1);
				}
				
				
				if ($tna_process_list[35][$poid][$value]=="")
				{
					if ($id_tna_process=="")$id_tna_process=return_next_id( "id", " tna_process_mst",1); else $id_tna_process+=1;
					if ($data_array_tna_process!="") $data_array_tna_process .=",";
					$data_array_tna_process .="(".$id_tna_process.",".$po_order_template[$poid].",'".$po_order_details[$poid]['job_no_mst']."','".$poid."','".$po_order_details[$poid]['po_received_date']."','".$po_order_details[$poid]['shipment_date']."',35,".$value.",'".$target_date."','".$start_date."','".$finish_date."','".$notice_date_start."','".$notice_date_end."','".$pc_date_time."','".$tna_task_template[$po_order_template[$poid]][35][$value]['sequence_no']."',1,0)";
				}
				else // Update
				{
					// Need to be updated by Task wise
					$process_id_up=$tna_process_list[35][$poid][$value];
					if ( $tna_process_details[$process_id_up]['start']!="0000-00-00" && $tna_process_details[$process_id_up]['start']!="" ) $start_date=$tna_process_details[$process_id_up]['start']; 
					
					else $start_date= ($exfactory_status_array[$poid][$tna_task_name[$value]['task_name']]['min'] == "" ? $blank_date : $exfactory_status_array[$poid][$tna_task_name[$value]['task_name']]['min']); 
					if ( get_percent($exfactory_status_array[$poid][$tna_task_name[$value]['task_name']]['qnty'], $po_order_details[$poid]['po_quantity'])>=$tna_task_details[35][$value]['completion_percent'])
					{
						if( $tna_process_details[$process_id_up]['finish']!="0000-00-00" && $tna_process_details[$process_id_up]['finish']!="" ) $finish_date=$tna_process_details[$process_id_up]['finish']; 
						else $finish_date=($exfactory_status_array[$poid][$tna_task_name[$value]['task_name']]['max'] == "" ? $blank_date : $exfactory_status_array[$poid][$tna_task_name[$value]['task_name']]['max']); 
					}
					
					//else $finish_date="0000-00-00";[Mysql]
					else { if($db_type==0) $finish_date=$tna_process_details[$process_id_up]['finish']; else $finish_date=$tna_process_details[$process_id_up]['finish']; } 
					
					$process_id_up_array[]=$process_id_up;
					$data_array_tna_process_up[$process_id_up] =explode(",",("'".$start_date."','".$finish_date."'"));
				}
			}
		}
	}
	//print_r($data_array_tna_process_up)."_*";die;
	
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
			//echo "INSERT INTO tna_process_mst (".$field_array_tna_process_up.") VALUES ".$data_array_tna_process_up;die;
			
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

function bulk_update_sql_statement2( $table, $id_column, $update_column, $data_values, $id_count )
{
	//echo $update_column."___".$data_values;die;
	$field_array=explode("*",$update_column);
	//$id_count=explode("*",$id_count);
	//$data_values=explode("*",$data_values);
	//print_r($data_values);die;
	$sql_up.= "UPDATE $table SET ";
	
	 for ($len=0; $len<count($field_array); $len++)
	 {
		 $sql_up.=" ".$field_array[$len]." = CASE $id_column ";
		 for ($id=0; $id<count($id_count); $id++)
		 {
			 if (trim($data_values[$id_count[$id]][$len])=="") $sql_up.=" when ".$id_count[$id]." then  '".$data_values[$id_count[$id]][$len]."'" ;
			 else $sql_up.=" when ".$id_count[$id]." then  ".$data_values[$id_count[$id]][$len]."" ;
		 }
		 if ($len!=(count($field_array)-1)) $sql_up.=" END, "; else $sql_up.=" END ";
	 }
	 $sql_up.=" where id in (".implode(",",$id_count).")";
	 //return $sql_up; 
	 echo $sql_up; die;  
} 

function sql_insert2( $strTable, $arrNames, $arrValues, $commit )
{
	global $con ;
	$tmpv=explode(")",$arrValues);
    $strQuery= "INSERT ALL \n";
    for($i=0; $i<count($tmpv)-1; $i++)
    {
		if( strpos(trim($tmpv[$i]), ",")==0)
			$tmpv[$i]=substr_replace($tmpv[$i], " ", 0, 1); 
        $strQuery .=" INTO ".$strTable." (".$arrNames.") values ".$tmpv[$i].") \n";
    }
	
    $strQuery .= "SELECT * FROM dual";
     return  $strQuery; die;
	//echo $strQuery;die;
	$_SESSION['last_query']=$_SESSION['last_query'].";;".$strQuery;

	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
	if ($exestd) 
		return "1";
	else 
		return "0";
	die;
	
	if ( $commit==1 )
	{
		if (!oci_error($exestd))
		{
			$pc_time= add_time(date("H:i:s",time()),360); 
			$pc_date_time = date("d-M-Y h:i:s",strtotime(add_time(date("H:i:s",time()),360)));
	        $pc_date = date("d-M-Y",strtotime(add_time(date("H:i:s",time()),360)));
			
			$strQuery= "INSERT INTO activities_history ( session_id,user_id,ip_address,entry_time,entry_date,module_name,form_name,query_details,query_type) VALUES ('".$_SESSION['logic_erp']["history_id"]."','".$_SESSION['logic_erp']["user_id"]."','".$_SESSION['logic_erp']["pc_local_ip"]."','".$pc_date_time."','".$pc_date."','".$_SESSION["module_id"]."','".$_SESSION['menu_id']."','".encrypt($_SESSION['last_query'])."','0')"; 
			$resultss=oci_parse($con, $strQuery);
			oci_execute($resultss);
			$_SESSION['last_query']="";
			//oci_commit($con); 
			return "0";
		}
		else
		{
			//oci_rollback($con);
			return "10";
		}
	}
	else return 1;
	//else
		//return 0;
		
	die;
}



	 // Purchase  Task Start Here
	/* foreach($order_id_array as $poid)   // treat fabric nature knit or woven
	 {
		foreach($tna_task_name_tmp[15] as $fid=>$value)
		{
			if( $tna_task_template_task[$po_order_template[$poid]][15][$value]!=""  )
			{
				if($db_type==0)
				{
					$target_date=add_date($po_order_details[$poid]['shipment_date'] ,- $tna_task_template[$po_order_template[$poid]][15][$value]['deadline']);
				}
				else
				{
					$target_date=change_date_format(trim(add_date($po_order_details[$poid]['shipment_date'] ,- $tna_task_template[$po_order_template[$poid]][15][$value]['deadline'])),'','',1);
				}
				
				$to_add_days=$tna_task_template[$po_order_template[$poid]][15][$value]['execution_days']-1;
				
				if($db_type==0)
				{
					$start_date=add_date($target_date ,-$to_add_days);
				}
				else
				{
					$start_date=change_date_format(trim(add_date($target_date ,-$to_add_days)),'','',1);
				}
				
				$finish_date=$target_date;
				$to_add_days=$tna_task_template[$po_order_template[$poid]][15][$value]['notice_before'];
				
				if($db_type==0)
				{
					$notice_date_start=add_date($start_date ,-$to_add_days);
				}
				else
				{
					$notice_date_start=change_date_format(trim(add_date($start_date ,-$to_add_days)),'','',1);
				}
				
				if($db_type==0)
				{
					$notice_date_end=add_date($finish_date ,-$to_add_days);
				}
				else
				{
					$notice_date_end=change_date_format(trim(add_date($finish_date ,-$to_add_days)),'','',1);
				}
				
				if ($tna_process_list[15][$poid][$value]=="")
				{
					if ($id_tna_process=="")$id_tna_process=return_next_id( "id", " tna_process_mst",1); else $id_tna_process+=1;
					if ($data_array_tna_process!="") $data_array_tna_process .=",";
					$data_array_tna_process .="(".$id_tna_process.",".$po_order_template[$poid].",'".$po_order_details[$poid]['job_no_mst']."','".$poid."','".$po_order_details[$poid]['po_received_date']."','".$po_order_details[$poid]['shipment_date']."',15,".$value.",'".$target_date."','".$start_date."','".$finish_date."','".$notice_date_start."','".$notice_date_end."','".$pc_date_time."','".$tna_task_template[$po_order_template[$poid]][15][$value]['sequence_no']."',1,0)";
				}
				else // Update  3 booking from booking table ---if booking found then booking date is start and end date (Same date)
				{
					// Need to be updated by Task wise
					//print_r($purchase_array_mapping); die;
					$process_id_up=$tna_process_list[15][$poid][$value];
					if ( $purchase_update_task[$poid][$purchase_array_mapping[$tna_task_name[$value]['task_name']]]['start_date']!="0000-00-00" || $purchase_update_task[$poid][$purchase_array_mapping[$tna_task_name[$value]['task_name']]]['start_date']!="" ) $start_date=$purchase_update_task[$poid][$purchase_array_mapping[$tna_task_name[$value]['task_name']]]['start_date']; else { if($db_type==0) $start_date=$tna_process_details[$process_id_up]['start']; else $start_date=$tna_process_details[$process_id_up]['start']; }
					
					if ( $purchase_update_task[$poid][$purchase_array_mapping[$tna_task_name[$value]['task_name']]]['end_date']!="0000-00-00" || $purchase_update_task[$poid][$purchase_array_mapping[$tna_task_name[$value]['task_name']]]['end_date']!="" ) $finish_date=$purchase_update_task[$poid][$purchase_array_mapping[$tna_task_name[$value]['task_name']]]['end_date']; else { if($db_type==0) $finish_date=$tna_process_details[$process_id_up]['finish']; else $finish_date=$tna_process_details[$process_id_up]['finish']; } 
					
					$process_id_up_array[]=$process_id_up;
					$data_array_tna_process_up[$process_id_up] =explode(",",("'".$start_date."','".$finish_date."'")); 
				}
			}
		}
	}
	*/
?>
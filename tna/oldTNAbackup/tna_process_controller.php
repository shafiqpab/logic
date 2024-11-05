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
Update date		         :  11.05.15		   
QC Performed BY	         :	
QC Date			         :	
Comments		         :  From this version oracle conversion is start
----------------------------------------------------------------------*/

header('Content-type:text/html; charset=utf-8');
session_start();

extract ( $_REQUEST );

include('../../includes/common.php');
require_once('../../mailer/class.phpmailer.php');
require_once('../../includes/class3/class.conditions.php');
require_once('../../includes/class3/class.reports.php');
require_once('../../includes/class3/class.fabrics.php');
require_once('../../includes/class3/class.yarns.php');
require_once('../../includes/class3/class.washes.php');
require_once('../../includes/class3/class.emblishments.php');
require_once('../../includes/class3/class.trims.php');

if( $action=="load_drop_down_buyer" )
{	
	echo create_drop_down( "cbo_buyer", 170, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );  
	die; 	 
}

if( $cbo_company<1 ) $company_array=return_library_array( "select id,id from lib_company",'id','id' );
else $company_array[$cbo_company]=$cbo_company;
 
if ( $action=="tna_process" )
{ 
	$tba_color_id=return_field_value("id","lib_color"," color_name ='TBA'");

	$sql = "SELECT task_name,completion_percent FROM  lib_tna_task WHERE is_deleted = 0 and status_active=1 order by task_name asc";
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
		
	// echo $sql;die;
	 
	   //print_r($tna_task_details);die;	
		
		 //Template Details
			$sql_task = "SELECT a.id,task_template_id,lead_time,material_source,total_task,tna_task_id,deadline,execution_days,notice_before,sequence_no,for_specific,b.task_catagory,b.task_name,b.task_type FROM  tna_task_template_details a, lib_tna_task b WHERE   a.tna_task_id=b.task_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 order by for_specific,lead_time ";
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
			
		 
		// print_r($template_wise_task[17]);die;	
		 
		 
		$sql = "SELECT company_name,tna_integrated FROM  variable_order_tracking WHERE  company_name=".$cbo_company." and status_active =1 and is_deleted = 0 and variable_list=14";
		$result = sql_select( $sql );
		$variable_settings = array();
		foreach( $result as $row ) 
		{		
			$variable_settings[$row[csf('company_name')]] = $row[csf('tna_integrated')];
		}
		if( $db_type==0 ) $blank_date="0000-00-00"; else $blank_date=""; 
		// Reprocess Check
		if (trim($txt_ponumber_id)=="" &&  $is_delete==1  ){
			//$strcond=""; 
			

				$job_array=return_library_array( "select id, job_no from wo_po_details_master where buyer_name=$cbo_buyer and company_name=$cbo_company",'id','job_no');
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
				$rid=execute_query("delete FROM tna_process_mst WHERE  1=1 $sql_con ",1);
			
			if( $db_type==2 ) oci_commit($con); 
						
			
		}
		else 
		{
			if( $is_delete==1 )
			{
				$con = connect();
				
				$rid=execute_query("delete FROM tna_process_mst WHERE  po_number_id in ( $txt_ponumber_id )",1);
				if( $db_type==2 ) oci_commit($con); 
			}
			//if( $is_delete==1 ) $rid=execute_query("delete FROM tna_process_mst WHERE  po_number_id in ( $txt_ponumber_id )",1); 
		}
		if( $cbo_buyer>0 ) $buyer_cond=" and a.buyer_name=$cbo_buyer ";
		//$job_nos ="'ASL-13-00191'";
		 
		  
		$condition= new condition();
		if($cbo_company>0){
			$condition->company_name("=$cbo_company");
		}
		if($cbo_buyer>0){
			$condition->buyer_name("=$cbo_buyer");
		}
		if($tna_process_start_date !=''){
			$condition->pub_shipment_date(" > '".$tna_process_start_date."'");
		//$condition->job_no("= 'FKTL-16-00117'");
		}
		
		if(trim($txt_ponumber_id) !=''){
			$condition->po_id(" in($txt_ponumber_id) ");
		}
	
		 //print_r($condition);die;
		
		$condition->init();
		$fabric= new fabric($condition);
		  //echo $fabric->getQuery();die;
		$fabricdata_production=$fabric->getQtyArray_by_order_knitAndwoven_greyAndfinish_production();
		
		
 //print_r($fabricdata_production);die;

		
		$fabric= new fabric($condition);
		//$fabricdata=$fabric->getQtyArray_by_order_knitAndwoven_greyAndfinish();
		$fabricdata=$fabric->getQtyArray_by_order_knitAndwoven_greyAndfinish_purchase();
		
		//print_r( $fabricdata); die;
		
		
		$yarn= new yarn($condition);
		$yarndata=$yarn->getOrderWiseYarnQtyArray();
		
		$wash= new wash($condition);
		$wash_data=$wash->getQtyArray_by_order();
		
		$emblishment= new emblishment($condition);
		$emblishment_data=$emblishment->getQtyArray_by_order();
		 
		$trims= new trims($condition);
		$trims_data=$trims->getAmountArray_by_order();
		
		// print_r($trims_data); die;
		
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
				$sql = "SELECT (pub_shipment_date) as shipment_date,job_no_mst,po_received_date,b.id,po_quantity,a.buyer_name,a.garments_nature,b.po_number,pp_meeting_date,a.style_ref_no,is_confirmed,tna_task_from_upto FROM wo_po_break_down b, wo_po_details_master a WHERE b.is_deleted = 0 and b.status_active=1 and a.is_deleted = 0 and a.status_active=1 and a.job_no=b.job_no_mst and b.pub_shipment_date!='0000-00-00' and b.po_received_date!='0000-00-00' and b.id  in ( $txt_ponumber_id )  and b.pub_shipment_date>'$tna_process_start_date'  $buyer_cond and company_name=".$cbo_company."  ORDER BY b.shipment_date asc";
			}
			else
			{
				$sql = "SELECT (pub_shipment_date) as shipment_date,job_no_mst,po_received_date,b.id,po_quantity,a.buyer_name,a.garments_nature,b.po_number,pp_meeting_date,a.style_ref_no,is_confirmed,tna_task_from_upto FROM wo_po_break_down b, wo_po_details_master a WHERE b.is_deleted = 0 and b.status_active=1 and a.is_deleted = 0 and a.status_active=1 and a.job_no=b.job_no_mst and to_char(b.pub_shipment_date)!='0000-00-00' and to_char(b.po_received_date)!='0000-00-00' and b.id  in ( $txt_ponumber_id )  and (b.pub_shipment_date)>'$tna_process_start_date' and company_name=".$cbo_company." $buyer_cond  ORDER BY b.shipment_date asc";
			}
		}
		  //echo $sql;die;
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
			
 	

			  //echo '10**'.$template_id; die;
									
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
				if( $fabricdata['knit']['grey'][$row[csf("id")]]>0) // Purchase
				{
					$tna_task_update_data[$row[csf("id")]][31]['reqqnty']+=$fabricdata['knit']['grey'][$row[csf("id")]];
					$to_process_task[$row[csf("id")]][31]=31;
					
					$tna_task_update_data[$row[csf("id")]][73]['reqqnty']+=$fabricdata['knit']['finish'][$row[csf("id")]];
					$to_process_task[$row[csf("id")]][73]=73;
				}
				
				if( $fabricdata['woven']['grey'][$row[csf("id")]]>0) // Woven Purchase
				{
					$tna_task_update_data[$row[csf("id")]][31]['reqqnty']+=$fabricdata['woven']['grey'][$row[csf("id")]];
					$to_process_task[$row[csf("id")]][31]=31;
					
					$tna_task_update_data[$row[csf("id")]][73]['reqqnty']+=$fabricdata['woven']['finish'][$row[csf("id")]];
					$to_process_task[$row[csf("id")]][73]=73;
				}

				
				if( $fabricdata_production['knit']['grey'][$row[csf("id")]]>0)
				{
					$tna_task_update_data[$row[csf("id")]][60]['reqqnty']+=$fabricdata_production['knit']['grey'][$row[csf("id")]];
					$to_process_task[$row[csf("id")]][60]=60;
					$tna_task_update_data[$row[csf("id")]][72]['reqqnty']+=$fabricdata_production['knit']['grey'][$row[csf("id")]];
					$to_process_task[$row[csf("id")]][72]=72;
					
					$tna_task_update_data[$row[csf("id")]][61]['reqqnty']+=$fabricdata_production['knit']['grey'][$row[csf("id")]];
					$to_process_task[$row[csf("id")]][61]=61;
					
					//$tna_task_update_data[$row[csf("id")]][72]['reqqnty']+=$fabricdata_production['knit']['grey'][$row[csf("id")]];
					//$to_process_task[$row[csf("id")]][72]=72;
					$tna_task_update_data[$row[csf("id")]][73]['reqqnty']+=$fabricdata_production['knit']['finish'][$row[csf("id")]];
					$to_process_task[$row[csf("id")]][73]=73;
					
					$tna_task_update_data[$row[csf("id")]][31]['reqqnty']+=$fabricdata_production['knit']['grey'][$row[csf("id")]];
					$to_process_task[$row[csf("id")]][31]=31;
				
					
					$tna_task_update_data[$row[csf("id")]][74]['reqqnty']+=$fabricdata_production['knit']['grey'][$row[csf("id")]];
					$to_process_task[$row[csf("id")]][74]=74;
				
				
				
				}
				
				
				
				if( $fabricdata_production['knit']['finish'][$row[csf("id")]]>0)
				{
					$tna_task_update_data[$row[csf("id")]][64]['reqqnty']+=$fabricdata_production['knit']['finish'][$row[csf("id")]];
					$to_process_task[$row[csf("id")]][64]=64;
				}
				
				if( $trims_data[$row[csf("id")]]>0 )
				{
					$tna_task_update_data[$row[csf("id")]][32]['reqqnty']=$trims_data[$row[csf("id")]];
					$to_process_task[$row[csf("id")]][32]=32;
				}
				
				if( $fabricdata['woven']['grey'][$row[csf("id")]]>0)
				{
					$tna_task_update_data[$row[csf("id")]][34]['reqqnty']=$fabricdata['woven']['grey'][$row[csf("id")]];
					$to_process_task[$row[csf("id")]][34]=34;
				}
				
				if( $yarndata[$row[csf("id")]]>0)
				{
					$tna_task_update_data[$row[csf("id")]][50]['reqqnty']=$yarndata[$row[csf("id")]];
					$to_process_task[$row[csf("id")]][50]=50;
					
					$tna_task_update_data[$row[csf("id")]][48]['reqqnty']=$yarndata[$row[csf("id")]];
					$to_process_task[$row[csf("id")]][48]=48;
					$tna_task_update_data[$row[csf("id")]][45]['reqqnty']=$yarndata[$row[csf("id")]];
					$to_process_task[$row[csf("id")]][45]=45;
					$tna_task_update_data[$row[csf("id")]][46]['reqqnty']=$yarndata[$row[csf("id")]];
					$to_process_task[$row[csf("id")]][46]=46;
					$tna_task_update_data[$row[csf("id")]][47]['reqqnty']=$yarndata[$row[csf("id")]];
					$to_process_task[$row[csf("id")]][47]=47;
					
				}
				
				if( $wash_data[$row[csf("id")]]>0)
				{
					$tna_task_update_data[$row[csf("id")]][89]['reqqnty']=$wash_data[$row[csf("id")]];
					$to_process_task[$row[csf("id")]][89]=89;
					
					$tna_task_update_data[$row[csf("id")]][90]['reqqnty']=$wash_data[$row[csf("id")]];
					$to_process_task[$row[csf("id")]][90]=90;
				}
				
				if( $emblishment_data[$row[csf("id")]]>0)
				{
					$tna_task_update_data[$row[csf("id")]][85]['reqqnty']=$emblishment_data[$row[csf("id")]];
					$to_process_task[$row[csf("id")]][85]=85;
				}
				//$tna_task_update_data[$row[csf("id")]][32]['reqqnty']=$trims_data[$row[csf("id")]];
				//$tna_task_update_data[$row[csf("id")]][34]['reqqnty']=$fabricdata['woven']['grey'][$row[csf("id")]];
				//$tna_task_update_data[$row[csf("id")]][50]['reqqnty']=$yarndata[$row[csf("id")]];
				//$tna_task_update_data[$row[csf("id")]][60]['reqqnty']=$fabricdata_production['knit']['grey'][$row[csf("id")]];
				//$tna_task_update_data[$row[csf("id")]][61]['reqqnty']=$fabricdata['knit']['grey'][$row[csf("id")]];
				//$tna_task_update_data[$row[csf("id")]][64]['reqqnty']=$fabricdata_production['knit']['finish'][$row[csf("id")]];;
				//$tna_task_update_data[$row[csf("id")]][72]['reqqnty']=$fabricdata['knit']['grey'][$row[csf("id")]];
				//$tna_task_update_data[$row[csf("id")]][73]['reqqnty']=$fabricdata['knit']['finish'][$row[csf("id")]];
				//$tna_task_update_data[$row[csf("id")]][89]['reqqnty']=$wash_data[$row[csf("id")]];
				//$tna_task_update_data[$row[csf("id")]][90]['reqqnty']=$wash_data[$row[csf("id")]];
				//$tna_task_update_data[$row[csf("id")]][85]['reqqnty']=$emblishment_data[$row[csf("id")]];
				
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
				$tna_task_update_data[$row[csf("id")]][80]['max_start_date']=$row[csf("pp_meeting_date")];
				$tna_task_update_data[$row[csf("id")]][80]['min_start_date']=$row[csf("pp_meeting_date")];
				//$tna_task_update_data[$row[csf("id")]][80]['noofval']=1;	
				$tna_task_update_data[$row[csf("id")]][80]['doneqnty']=1;
				$tna_task_update_data[$row[csf("id")]][80]['reqqnty']=1; 
				$tna_task_update_data[$row[csf("id")]][1]['doneqnty']=1;
				$tna_task_update_data[$row[csf("id")]][1]['reqqnty']=1; 
				$tna_task_update_data[$row[csf("id")]][1]['max_start_date']=$row[csf("po_received_date")];
				$tna_task_update_data[$row[csf("id")]][1]['min_start_date']=$row[csf("po_received_date")];
			
				$to_process_task[$row[csf("id")]][1]=1;
				$job_nature[$row[csf('job_no')]] = $row[csf('garments_nature')];
				
				
				
				foreach($tna_common_task_name_to_process as $vid=>$vtask)
				{
					if( $row[csf('is_confirmed')]==1 ) //&& $row[csf('projected_po_id')]!=0
					{
						if( $vid>=$row[csf('tna_task_from_upto')]  )
						{
							$to_process_task[$row[csf("id")]][$vid]=$vid;
						}
					}
					else if( $row[csf('is_confirmed')]==2 ) // Projected
					{
						if( $row[csf('tna_task_from_upto')] !=0 )
						{
							if( $vid <= $row[csf('tna_task_from_upto')] )
							{
								$to_process_task[$row[csf("id")]][$vid]=$vid;
							}
						}
						else $to_process_task[$row[csf("id")]][$vid]=$vid;
					}
				}
				$i++;
			}
		}
		
		$po_ids=implode(",",$order_id_array);
		$job_no_list="'".implode("','",$job_no_array)."'";
		
		if( $po_ids=='' )
		{
			echo "0**".$rID."**".implode(", ",$template_missing_po);
			//die;
		}
	
	
	unset($data_array);
	unset($order_id_array); 
	unset($job_no_array);
	   
		if($db_type==0)
		{
			$sql = "SELECT task_number,po_number_id,actual_start_date,actual_finish_date,task_start_date,task_finish_date ,plan_start_flag,plan_finish_flag FROM tna_plan_actual_history WHERE po_number_id in ( $po_ids ) and status_active =1 and is_deleted=0 and task_type=1";
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
			$sql .=")  and status_active =1 and is_deleted=0 and task_type=1";
			
			 
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
		
		
		if($db_type==0)
		{
			$sql = "SELECT id,job_no,po_number_id,task_category,task_number,actual_start_date,actual_finish_date,template_id FROM tna_process_mst WHERE po_number_id in ( $po_ids ) and status_active =1 and is_deleted = 0";
		}
		else
		{
			$job_no_list_arr=array_chunk(array_unique(explode(",",$po_ids)),999);
			
			$sql = "SELECT id,job_no,po_number_id,task_category,task_number,actual_start_date,actual_finish_date,template_id FROM tna_process_mst WHERE status_active =1 and is_deleted = 0 ";
			
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1) $sql .=" and (po_number_id in(".implode(',',$job_no_process).")"; else  $sql .=" or po_number_id in(".implode(',',$job_no_process).")";
				
				$p++;
			}
			$sql .=")";
		}
		
		   //echo $sql;die;
		 
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
			else //if( $po_order_template[$row[csf('po_number_id')]]!=$row[csf('template_id')] )
			{
				$changed_templates[$row[csf('po_number_id')]]=$row[csf('po_number_id')];
			}
		}
	 	
		   //print_r($tna_process_list);die;
		
		
		if( count($changed_templates)>0 )
		{
			$con = connect();
			$rid=execute_query("delete FROM tna_process_mst WHERE  po_number_id in ( ".implode(",",$changed_templates)." )",1);
			if( $db_type==2 ) oci_commit($con); 
		}
		
	// Sample Approval Update Data
		if($db_type==0)
		{
			$sql_task = "SELECT count(a.id) as cid,po_break_down_id,sample_type,max(submitted_to_buyer) as max_submitted_to_buyer,min(submitted_to_buyer) as min_submitted_to_buyer,max(approval_status_date) as max_approval_status_date,min(approval_status_date) as min_approval_status_date
		 FROM wo_po_sample_approval_info  a, lib_sample b WHERE job_no_mst in ( $job_no_list ) and sample_type in (2,3,4,7,8,9,10,11,9,5) and b.id=a.SAMPLE_TYPE_ID  and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 group by sample_type,po_break_down_id order by po_break_down_id asc, approval_status desc";
		}
		else
		{
		 
		 $job_no_list_arr=array_chunk(array_unique(explode(",",$job_no_list)),999);
			
		$sql_task = "SELECT count(a.id) as cid,po_break_down_id,sample_type,max(submitted_to_buyer) as max_submitted_to_buyer,min(submitted_to_buyer) as min_submitted_to_buyer,max(approval_status_date) as max_approval_status_date,min(approval_status_date) as min_approval_status_date
		 FROM wo_po_sample_approval_info  a, lib_sample b WHERE sample_type in (2,3,4,7,8,9,10,11,9,5) and b.id=a.SAMPLE_TYPE_ID and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1";
		
		$p=1;
		foreach($job_no_list_arr as $job_no_process)
		{
			if($p==1) $sql_task .="and (job_no_mst in(".implode(',',$job_no_process).")"; else  $sql_task .=" or job_no_mst in(".implode(',',$job_no_process).")";
			
			$p++;
		}
		$sql_task .=")";
		
		$sql_task .=" group by sample_type,po_break_down_id order by po_break_down_id asc";
		 
		 //echo $sql_task;die;
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
			else if ($row[csf("sample_type")]==10) { $sub=26;  $appr=27; }
			else if ($row[csf("sample_type")]==11) { $sub=28;  $appr=29; }
			else if ($row[csf("sample_type")]==9) { $sub=124;  $appr=124; }
			else if ($row[csf("sample_type")]==5) { $sub=123;  $appr=123; }
		
			
			
			
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
			$sql_task = "SELECT a.id as cid,po_break_down_id,sample_type,submitted_to_buyer,approval_status_date	 FROM wo_po_sample_approval_info  a, lib_sample b WHERE job_no_mst in ( $job_no_list ) and sample_type in  (2,3,4,7,8,9,10,11) and b.id=a.SAMPLE_TYPE_ID  and a.is_deleted = 0 and a.status_active=1  and b.is_deleted = 0 and b.status_active=1";
		}
		else
		{
			$job_no_list_arr=array_chunk(array_unique(explode(",",$job_no_list)),999);
			$sql_task = "SELECT a.id as cid,po_break_down_id,sample_type,submitted_to_buyer,approval_status_date FROM wo_po_sample_approval_info  a, lib_sample b WHERE sample_type in  (2,3,4,7,8,9,10,11) and b.id=a.SAMPLE_TYPE_ID  and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1";
			
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1) $sql_task .="and (job_no_mst in(".implode(',',$job_no_process).")"; else  $sql_task .=" or job_no_mst in(".implode(',',$job_no_process).")";
				
				$p++;
			}
			$sql_task .=")";
			//$sql_task .=" group by sample_type,po_break_down_id order by po_break_down_id asc";
		}
		
		$result = sql_select( $sql_task );
		$sample_approval_update = array(); 
		foreach( $result as $row ) 
		{
			 $sub=0;  $appr=0;
			/*if ($row[csf("sample_type")]==2) { $sub=8;  $appr=12; }
			else if ($row[csf("sample_type")]==3) { $sub=7;  $appr=13; }
			else if ($row[csf("sample_type")]==4) { $sub=14;  $appr=15; }
			else if ($row[csf("sample_type")]==7) { $sub=16;  $appr=17; }
			*/
			if ($row[csf("sample_type")]==2) { $sub=8;  $appr=12; }
			else if ($row[csf("sample_type")]==3) { $sub=7;  $appr=13;  }
			else if ($row[csf("sample_type")]==4) { $sub=14;  $appr=15; }
			else if ($row[csf("sample_type")]==7) { $sub=16;  $appr=17; }
			else if ($row[csf("sample_type")]==8) { $sub=21;  $appr=22; }
			else if ($row[csf("sample_type")]==9) { $sub=23;  $appr=24; }
			else if ($row[csf("sample_type")]==10) { $sub=26;  $appr=27; }
			else if ($row[csf("sample_type")]==11) { $sub=28;  $appr=29; }
			
			//$tna_task_update_data[$row[csf("po_break_down_id")]][$sub]['noofapproved']=$row[csf("cid")];	
			//$tna_task_update_data[$row[csf("po_break_down_id")]][$appr]['noofapproved']=$row[csf("cid")];
		
		
			if($row[csf("approval_status_date")]=='0000-00-00'){$row[csf("approval_status_date")]='';}
			if($row[csf("submitted_to_buyer")]=='0000-00-00'){$row[csf("submitted_to_buyer")]='';}
			
			if($row[csf("approval_status_date")]!=''){
				$tna_task_update_data[$row[csf("po_break_down_id")]][$appr]['noofapproved']+=1;	
			}
			if($row[csf("submitted_to_buyer")]!=''){
				$tna_task_update_data[$row[csf("po_break_down_id")]][$sub]['noofapproved']+=1;
			}
		
		
		
		}
		
							

		
		if($db_type==0)
		{
			$sql = "select b.po_break_down_id, sum(b.qnty) as qnty, min(a.allocation_date) as mindate, max(a.allocation_date) as maxdate from inv_material_allocation_mst a, inv_material_allocation_dtls b 
where a.id=b.mst_id and b.item_category=1 and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and b.po_break_down_id  in   ( $po_ids ) group by b.po_break_down_id    ";
		}
		else
		{
			$job_no_list_arr=array_chunk(array_unique(explode(",",$po_ids)),999);
		
			$sql = "select b.po_break_down_id, sum(b.qnty) as qnty, min(a.allocation_date) as mindate, max(a.allocation_date) as maxdate from inv_material_allocation_mst a, inv_material_allocation_dtls b 
where a.id=b.mst_id and b.item_category=1 and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  ";
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1) $sql .=" and ( b.po_break_down_id in(".implode(',',$job_no_process).")"; else  $sql .=" or b.po_break_down_id in(".implode(',',$job_no_process).")";
				
				$p++;
			}
			$sql .=")  group by b.po_break_down_id";
			
			 
		}
 
		$result = sql_select( $sql );
		foreach( $result as $row ) 
		{
			$tna_task_update_data[$row[csf("po_break_down_id")]][48]['max_start_date']=$row[csf("maxdate")];
			$tna_task_update_data[$row[csf("po_break_down_id")]][48]['min_start_date']=$row[csf("mindate")]; 
			$tna_task_update_data[$row[csf("po_break_down_id")]][48]['doneqnty']=$row[csf("qnty")];
			//$tna_task_update_data[$row[csf("PO_BREAK_DOWN_ID")]][48]['noofapproved']=$row[csf("QNTY")];	
			//$tna_task_update_data[$row[csf("PO_BREAK_DOWN_ID")]][48]['noofapproved']=$row[csf("cid")];
		}
		
		
	
	// LABDIP Approval Data for Update
		if($db_type==0)
		{
			$sql_task = "SELECT count(id) as cid,po_break_down_id,max(submitted_to_buyer) as max_submitted_to_buyer,min(submitted_to_buyer) as min_submitted_to_buyer,max(approval_status_date) as max_approval_status_date,min(approval_status_date) as min_approval_status_date, group_concat(color_name_id) as color_name_id
		 FROM wo_po_lapdip_approval_info WHERE job_no_mst in ( $job_no_list ) and is_deleted = 0 and status_active=1 group by po_break_down_id order by job_no_mst,po_break_down_id asc";  //and approval_status in ( 0,1,3 ) 
		}
		else
		{
			/*$sql_task = "SELECT count(id) as cid,po_break_down_id,max(submitted_to_buyer) as max_submitted_to_buyer,min(submitted_to_buyer) as min_submitted_to_buyer,max(approval_status_date) as max_approval_status_date,min(approval_status_date) as min_approval_status_date
		 FROM wo_po_lapdip_approval_info WHERE job_no_mst in ( $job_no_list ) and is_deleted = 0 and status_active=1 group by po_break_down_id order by po_break_down_id asc";*/
		 
			$job_no_list_arr=array_chunk(array_unique(explode(",",$job_no_list)),999);
			
			$sql_task = "SELECT count(id) as cid, po_break_down_id, max(submitted_to_buyer) as max_submitted_to_buyer,min(submitted_to_buyer) as min_submitted_to_buyer,max(approval_status_date) as max_approval_status_date,min(approval_status_date) as min_approval_status_date, wm_concat(color_name_id) as color_name_id
		 FROM wo_po_lapdip_approval_info WHERE is_deleted = 0 and status_active=1";
			
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1) $sql_task .="and (job_no_mst in(".implode(',',$job_no_process).")"; else  $sql_task .=" or job_no_mst in(".implode(',',$job_no_process).")";
				
				$p++;
			}
			$sql_task .=")";
			
			$sql_task .=" group by po_break_down_id order by po_break_down_id asc";
			
			 //echo $sql_task;die;
		}
		  
		$result = sql_select( $sql_task );
		$labdip_update_task = array(); 
		foreach( $result as $row ) 
		{
			$to_process_task[$row[csf("po_break_down_id")]][9]=9;
			$to_process_task[$row[csf("po_break_down_id")]][10]=10;
			 
			$colors=explode(",",$row[csf("color_name_id")]);
			if( count($colors)>1 )
			{
				if(in_array($tba_color_id,$colors))
				{
					if($delete_po_lab=="") $delete_po_lab=$row[csf("po_break_down_id")]; else $delete_po_lab .=",".$row[csf("po_break_down_id")];
				}
			}
			
			$tna_task_update_data[$row[csf("po_break_down_id")]][10]['max_start_date']=$row[csf("max_approval_status_date")];
			$tna_task_update_data[$row[csf("po_break_down_id")]][10]['min_start_date']=$row[csf("min_approval_status_date")];
			$tna_task_update_data[$row[csf("po_break_down_id")]][10]['noofval']=$row[csf("cid")];
			
			$tna_task_update_data[$row[csf("po_break_down_id")]][9]['max_start_date']=$row[csf("max_submitted_to_buyer")];
			$tna_task_update_data[$row[csf("po_break_down_id")]][9]['min_start_date']=$row[csf("min_submitted_to_buyer")];
			$tna_task_update_data[$row[csf("po_break_down_id")]][9]['noofval']=$row[csf("cid")];	
		}
		
		if( $delete_po_lab!="" )
		{
				$con = connect();
				$po_no_list_arr=array_chunk(array_unique(explode(",",$delete_po_lab)),999);
				$p=0;
			 foreach($po_no_list_arr as $job_no_process)
			{
				$p++;
				 if($p==1) $sql_taskss .=" and (po_break_down_id in(".implode(',',$job_no_process).")"; else  $sql_taskss .=" or po_break_down_id in(".implode(',',$job_no_process).")";
			}
			$sql_taskss .=")";
			//echo "delete from  wo_po_lapdip_approval_info where color_name_id='".$tba_color_id."' $sql_taskss " ; die;
			$rid=execute_query( "delete from  wo_po_lapdip_approval_info where color_name_id='".$tba_color_id."' $sql_taskss " ,1);
			
			if( $db_type==2 ) oci_commit($con); 
		}
		// echo "delete from  wo_po_lapdip_approval_info where color_name_id='".$tba_color_id."' $sql_taskss " ; die;
		
		if($db_type==0)
		{
			$sql_task = "SELECT id as cid,po_break_down_id ,submitted_to_buyer,approval_status_date FROM wo_po_lapdip_approval_info WHERE job_no_mst in ( $job_no_list ) and  is_deleted = 0 and status_active=1 order by po_break_down_id";
		}
		else
		{
			
			$job_no_list_arr=array_chunk(array_unique(explode(",",$job_no_list)),999);
			
			$sql_task = "SELECT id as cid,po_break_down_id ,submitted_to_buyer,approval_status_date FROM wo_po_lapdip_approval_info WHERE is_deleted = 0 and status_active=1";
			
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1) $sql_task .="and (job_no_mst in(".implode(',',$job_no_process).")"; else  $sql_task .=" or job_no_mst in(".implode(',',$job_no_process).")";
				
				$p++;
			}
			$sql_task .=")";
			
			//$sql_task .=" group by po_break_down_id order by po_break_down_id asc";
			
			// echo $sql_task;die; 
		}
		$result = sql_select( $sql_task );
		$sample_approval_update = array(); 
		foreach( $result as $row ) 
		{
			if($row[csf("approval_status_date")]=='0000-00-00'){$row[csf("approval_status_date")]='';}
			if($row[csf("submitted_to_buyer")]=='0000-00-00'){$row[csf("submitted_to_buyer")]='';}
			
			if($row[csf("approval_status_date")]!=''){
				$tna_task_update_data[$row[csf("po_break_down_id")]][10]['noofapproved']+=1;	
			}
			if($row[csf("submitted_to_buyer")]!=''){
				$tna_task_update_data[$row[csf("po_break_down_id")]][9]['noofapproved']+=1;
			}
		}
			
	// Trims Approval Data for Update
		if($db_type==0)
		{
			$sql_task = "SELECT count(a.id) as cid,po_break_down_id,max(submitted_to_buyer) as max_submitted_to_buyer,min(submitted_to_buyer) as min_submitted_to_buyer,max(approval_status_date) as max_approval_status_date,min(approval_status_date) as min_approval_status_date,trim_type
	FROM lib_item_group b, wo_po_trims_approval_info a
	WHERE b.id=a.accessories_type_id and job_no_mst in ( $job_no_list ) and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 group by po_break_down_id,trim_type order by job_no_mst,po_break_down_id asc";
		}
		else
		{
			$job_no_list_arr=array_chunk(array_unique(explode(",",$job_no_list)),999);
			
			$sql_task = "SELECT count(a.id) as cid,po_break_down_id,max(submitted_to_buyer) as max_submitted_to_buyer,min(submitted_to_buyer) as min_submitted_to_buyer,max(approval_status_date) as max_approval_status_date,min(approval_status_date) as min_approval_status_date,trim_type
	FROM lib_item_group b, wo_po_trims_approval_info a
	WHERE b.id=a.accessories_type_id and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1";
			
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1) $sql_task .="and (job_no_mst in(".implode(',',$job_no_process).")"; else  $sql_task .=" or job_no_mst in(".implode(',',$job_no_process).")";
				
				$p++;
			}
			$sql_task .=")";
			
			$sql_task .=" group by po_break_down_id,trim_type order by po_break_down_id asc";
			
			//echo $sql_task;die;
		}
		$result = sql_select( $sql_task );
		$trims_update_task = array(); 
		foreach( $result as $row ) 
		{		
			$to_process_task[$row[csf("po_break_down_id")]][11]=11;
			$to_process_task[$row[csf("po_break_down_id")]][25]=25;
			
			$tna_task_update_data[$row[csf("po_break_down_id")]][11]['max_start_date']=$row[csf("max_approval_status_date")];
			$tna_task_update_data[$row[csf("po_break_down_id")]][11]['min_start_date']=$row[csf("min_approval_status_date")];
			$tna_task_update_data[$row[csf("po_break_down_id")]][11]['noofval']=$row[csf("cid")];
			
			$tna_task_update_data[$row[csf("po_break_down_id")]][25]['max_start_date']=$row[csf("max_submitted_to_buyer")];
			$tna_task_update_data[$row[csf("po_break_down_id")]][25]['min_start_date']=$row[csf("min_submitted_to_buyer")];
			$tna_task_update_data[$row[csf("po_break_down_id")]][25]['noofval']=$row[csf("cid")];
			
			
			
		}
		
		
		if($db_type==0)
		{
			$sql_task = "SELECT count(a.id) as cid,po_break_down_id,trim_type FROM lib_item_group b, wo_po_trims_approval_info a
	WHERE b.id=a.accessories_type_id and job_no_mst in ( $job_no_list ) and approval_status_date!='0000-00-00' and a.is_deleted = 0 and a.status_active=1  and b.is_deleted = 0 and b.status_active=1 group by po_break_down_id,trim_type order by job_no_mst,po_break_down_id asc";
		}
		else
		{
	
			$job_no_list_arr=array_chunk(array_unique(explode(",",$job_no_list)),999);
			
			$sql_task = "SELECT count(a.id) as cid,po_break_down_id,trim_type FROM lib_item_group b, wo_po_trims_approval_info a
	WHERE b.id=a.accessories_type_id and approval_status_date is not null  and a.is_deleted = 0 and a.status_active=1   and b.is_deleted = 0 and b.status_active=1";
			
			$p=1;
			foreach($job_no_list_arr as $job_no_process)

			{
				if($p==1) $sql_task .="and (job_no_mst in(".implode(',',$job_no_process).")"; else  $sql_task .=" or job_no_mst in(".implode(',',$job_no_process).")";
				
				$p++;
			}
			$sql_task .=")";
			
			$sql_task .=" group by po_break_down_id,trim_type order by po_break_down_id asc";
			
			//echo $sql_task;die;
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
		 
			$job_no_list_arr=array_chunk(array_unique(explode(",",$job_no_list)),999);
			
			$sql_task = "SELECT count(id) as cid,po_break_down_id,max(submitted_to_buyer) as max_submitted_to_buyer,min(submitted_to_buyer) as min_submitted_to_buyer,max(approval_status_date) as max_approval_status_date,min(approval_status_date) as min_approval_status_date,embellishment_id
		 FROM wo_po_embell_approval WHERE is_deleted = 0 and status_active=1";
			
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1) $sql_task .="and (job_no_mst in(".implode(',',$job_no_process).")"; else  $sql_task .=" or job_no_mst in(".implode(',',$job_no_process).")";
				
				$p++;
			}
			$sql_task .=")";
			
			$sql_task .=" group by po_break_down_id,embellishment_id order by po_break_down_id asc";
			
			  //echo $sql_task;die;
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
			
			$job_no_list_arr=array_chunk(array_unique(explode(",",$job_no_list)),999);
			
			$sql_task = "SELECT count(id) as cid,po_break_down_id FROM wo_po_embell_approval WHERE approval_status_date is not null   and is_deleted = 0 and status_active=1";
			
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1) $sql_task .="and (job_no_mst in(".implode(',',$job_no_process).")"; else  $sql_task .=" or job_no_mst in(".implode(',',$job_no_process).")";
				
				$p++;
			}
			$sql_task .=")";
			
			$sql_task .=" group by po_break_down_id,embellishment_id order by po_break_down_id asc";
			
			//echo $sql_task;die;
		}	
		$result = sql_select( $sql_task );
		$sample_approval_update = array(); 
		foreach( $result as $row ) 
		{
			$tna_task_update_data[$row[csf("po_break_down_id")]][19]['noofapproved']=$row[csf("cid")];	
			$tna_task_update_data[$row[csf("po_break_down_id")]][20]['noofapproved']=$row[csf("cid")];	
		}
		
		unset($result);
		
		if($db_type==0)
		{
			$sql="SELECT distinct (po_break_down_id) as  po_break_down_id,color_type_id FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b WHERE a.id=b.pre_cost_fabric_cost_dtls_id and b.cons>0 and a.color_type_id in (2,3,4,6,5) and b.po_break_down_id in ( $po_ids ) and a.status_active =1 and a.is_deleted=0"; 
		}
		else
		{	
			
			$job_no_list_arr=array_chunk(array_unique(explode(",",$po_ids)),999);
			
			$sql = "SELECT distinct (po_break_down_id) as  po_break_down_id,color_type_id FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b WHERE a.id=b.pre_cost_fabric_cost_dtls_id and b.cons>0 and a.color_type_id in (2,3,4,6,5) and a.status_active =1 and a.is_deleted=0";
			
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1) $sql .="and (b.po_break_down_id in(".implode(',',$job_no_process).")"; else  $sql .=" or b.po_break_down_id in(".implode(',',$job_no_process).")";
				
				$p++;
			}
			$sql .=")";
			
			//echo $sql;die;
		}
		//echo $sql;die;
		$data_array=sql_select($sql);
		foreach($data_array as $row)
		{
			if($row[csf("color_type_id")]==5) { $app=62; $sub=63; }
			else { $app=51; $sub=52; }
			$to_process_task[$row[csf("po_break_down_id")]][$app]=$app;
			$to_process_task[$row[csf("po_break_down_id")]][$sub]=$sub;
		}
		
		/*if($db_type==0)
		{
			$sql="SELECT distinct (b.id) as  po_break_down_id, emb_name FROM wo_pre_cost_embe_cost_dtls a, wo_po_break_down b WHERE a.job_no=b.job_no_mst and b.id in ( $po_ids )";
		}
		else
		{	
			//$sql="SELECT distinct (b.id) as  po_break_down_id, emb_name FROM wo_pre_cost_embe_cost_dtls a, wo_po_break_down b WHERE a.job_no=b.job_no_mst and b.id in ( $po_ids )";
			
			$job_no_list_arr=array_chunk(array_unique(explode(",",$po_ids)),999);
			
			$sql = "SELECT distinct (b.id) as  po_break_down_id, emb_name FROM wo_pre_cost_embe_cost_dtls a, wo_po_break_down b WHERE a.job_no=b.job_no_mst";
			
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1) $sql .=" and (b.id in(".implode(',',$job_no_process).")"; else  $sql .=" or b.id in(".implode(',',$job_no_process).")";
				
				$p++;
			}
			$sql .=")";
			
			//echo $sql;die;
		}
		
		$data_array=sql_select($sql);
		foreach($data_array as $row)
		{
			 
			
			if($row[csf("emb_name")]==3) { $app=89; $sub=90; }
			//else if($row[csf("emb_name")]==1 || $row[csf("emb_name")]==2 ) { $app=85; $sub=85; }
			
			$to_process_task[$row[csf("po_break_down_id")]][$app]=$app;
			$to_process_task[$row[csf("po_break_down_id")]][$sub]=$sub;
		}
		*/
		 
		unset($data_array);
		
	// print_r($tna_task_update_data); die;
		//Purchase/Booking  Data for Update 2- FB, 4 -Trims, 12- Service
		if($db_type==0)
		{
			$sql_task = "SELECT b.po_break_down_id,  sum(wo_qnty) as tfb_qnty, sum(b.amount/b.exchange_rate)  as tfb_amount, sum(grey_fab_qnty) as gfb_qnty, min(a.booking_date) as start_date, max(a.booking_date) as end_date, a.item_category,a.booking_type
		FROM  wo_booking_mst a, wo_booking_dtls b WHERE b.po_break_down_id in ( $po_ids ) and a.booking_no=b.booking_no and a.is_short=2 and a.item_category in ( 2,4,12,3 ) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.po_break_down_id,item_category,a.booking_type order by b.po_break_down_id asc";
		}
		else
		{
			$job_no_list_arr=array_chunk(array_unique(explode(",",$po_ids)),999);
			$sql_task = "SELECT b.po_break_down_id, sum(wo_qnty) as tfb_qnty, sum(b.amount/b.exchange_rate)  as tfb_amount, sum(grey_fab_qnty) as gfb_qnty, min(a.booking_date) as start_date, max(a.booking_date) as end_date, a.item_category,a.booking_type FROM  wo_booking_mst a, wo_booking_dtls b WHERE a.booking_no=b.booking_no and a.item_category in ( 2,4,12,3 ) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.is_short=2 ";
			
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1) $sql_task .=" and (b.po_break_down_id in(".implode(',',$job_no_process).")"; else  $sql_task .=" or b.po_break_down_id in(".implode(',',$job_no_process).")";
				
				$p++;
			}
			$sql_task .=")";
			$sql_task .=" group by b.po_break_down_id,item_category,a.booking_type order by b.po_break_down_id asc";
			
			 
		}
		//echo $sql_task;die;
		$result = sql_select( $sql_task );
		$purchase_update_task = array(); 
		foreach( $result as $row ) 
		{ 
			$tsktype=0;  
			$qnty=0;
			if ($row[csf("item_category")]==2) { $tsktype=31; $qnty=$row[csf("gfb_qnty")]; }
			else if ($row[csf("item_category")]==3) { $tsktype=34; $qnty=$row[csf("gfb_qnty")]; }
			else if ($row[csf("item_category")]==4) { $tsktype=32; $qnty=$row[csf("tfb_amount")]; }
			else if ($row[csf("item_category")]==12) { $tsktype=33; $qnty=$row[csf("tfb_qnty")]; }
			
			if( $row[csf("booking_type")]==4 && $row[csf("item_category")]==2) 
			{ 
				$tsktype=30; 
				$qnty=$row[csf("gfb_qnty")]; 
				$tna_task_update_data[$row[csf("po_break_down_id")]][$tsktype]['reqqnty']=$qnty;
			}
			
			$tna_task_update_data[$row[csf("po_break_down_id")]][$tsktype]['max_start_date']=$row[csf("end_date")];
			$tna_task_update_data[$row[csf("po_break_down_id")]][$tsktype]['min_start_date']=$row[csf("start_date")]; 
			$tna_task_update_data[$row[csf("po_break_down_id")]][$tsktype]['doneqnty']=$qnty;
			
		}
		unset($result);
		//print_r($purchase_update_task); die;
	// Inventory Update Data
		if($db_type==0)
		{  
			$sql = "SELECT b.po_breakdown_id,b.entry_form, min(a.transaction_date) mindate, max(a.transaction_date) maxdate, sum(quantity) as prod_qntry 
	FROM inv_transaction a,  order_wise_pro_details b where  b.trans_id=a.id and b.entry_form in ( 2,7,22,37,3,58,18 ) and b.po_breakdown_id in ( $po_ids ) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.po_breakdown_id,b.entry_form order by b.po_breakdown_id";
		}
		else
		{
	
			$job_no_list_arr=array_chunk(array_unique(explode(",",$po_ids)),999);
			
			$sql = "SELECT b.po_breakdown_id,b.entry_form, min(a.transaction_date) mindate, max(a.transaction_date) maxdate, sum(quantity) as prod_qntry 
	FROM inv_transaction a,  order_wise_pro_details b where  b.trans_id=a.id and b.entry_form in (2,7,22,37,3,58,18 ) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0";
			
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1) $sql .=" and (b.po_breakdown_id in(".implode(',',$job_no_process).")"; else  $sql .=" or b.po_breakdown_id in(".implode(',',$job_no_process).")";
				
				$p++;
			}
			$sql .=")";
			$sql .=" group by b.po_breakdown_id,b.entry_form order by b.po_breakdown_id";
			
			  
		}
		//echo $sql;die;
		$inventory_transaction_update=array();
		$data_array=sql_select($sql);
		foreach($data_array as $row)
		{
			$tsktype=0;
			if ($row[csf("entry_form")]==2) $tsktype=72;
			else if ($row[csf("entry_form")]==7)  $tsktype=73;
			else if ($row[csf("entry_form")]==22) $tsktype=72;//22=Knit Grey Fabric Receive;
			else if ($row[csf("entry_form")]==58) $tsktype=72;//58=Knit Grey Fabric Roll Receive;
			else if ($row[csf("entry_form")]==37) $tsktype=73;
			else if ($row[csf("entry_form")]==3)  $tsktype=50;
			else if ($row[csf("entry_form")]==18) $tsktype=74;
			
			$tna_task_update_data[$row[csf("po_breakdown_id")]][$tsktype]['max_start_date']=$row[csf("maxdate")];
			$tna_task_update_data[$row[csf("po_breakdown_id")]][$tsktype]['min_start_date']=$row[csf("mindate")];
			$tna_task_update_data[$row[csf("po_breakdown_id")]][$tsktype]['doneqnty']=$row[csf("prod_qntry")];
		}
		unset($data_array);
	 
		if($db_type==0)
		{
			$sql = "SELECT b.po_breakdown_id, min(a.transaction_date) mindate, max(a.transaction_date) maxdate, sum(quantity) as prod_qntry,d.trim_type 
	FROM inv_transaction a,  order_wise_pro_details b, product_details_master c , lib_item_group d
	where a.prod_id=c.id and b.trans_id=a.id and c.item_group_id=d.id and d.trim_type in (1,2) and b.entry_form in ( 24 ) and b.po_breakdown_id in ( $po_ids ) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and c.status_active =1 and c.is_deleted=0 and d.status_active =1 and d.is_deleted=0 group by b.po_breakdown_id,d.trim_type order by b.po_breakdown_id";
		}
		else
		{
		
			$job_no_list_arr=array_chunk(array_unique(explode(",",$po_ids)),999);
			
			$sql = "SELECT b.po_breakdown_id, min(a.transaction_date) mindate, max(a.transaction_date) maxdate, sum(quantity) as prod_qntry,d.trim_type 
	FROM inv_transaction a,  order_wise_pro_details b, product_details_master c , lib_item_group d
	where a.prod_id=c.id and b.trans_id=a.id and c.item_group_id=d.id and d.trim_type in (1,2) and b.entry_form in ( 24 )  and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and c.status_active =1 and c.is_deleted=0 and d.status_active =1 and d.is_deleted=0";
			
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1) $sql .=" and (b.po_breakdown_id in(".implode(',',$job_no_process).")"; else  $sql .=" or b.po_breakdown_id in(".implode(',',$job_no_process).")";
				
				$p++;
			}
			$sql .=")";
			$sql .=" group by b.po_breakdown_id,d.trim_type order by b.po_breakdown_id";
			
			//echo $sql;die;
		} 
		
		
		$data_array=sql_select($sql);
		foreach($data_array as $row)
		{
			$entry=($row[csf("trim_type")] == 1 ? 70 : 71);
			
			$tna_task_update_data[$row[csf("po_breakdown_id")]][$entry]['max_start_date']=$row[csf("maxdate")];
			$tna_task_update_data[$row[csf("po_breakdown_id")]][$entry]['min_start_date']=$row[csf("mindate")];
			$tna_task_update_data[$row[csf("po_breakdown_id")]][$entry]['doneqnty']=$row[csf("prod_qntry")];
			if($row[csf("trim_type")] == 1){
				$tna_task_update_data[$row[csf("po_breakdown_id")]][$entry]['reqqnty']=$row[csf("prod_qntry")];
			}
		}
		//echo "sumon"; print_r( $tna_task_percent ); die;
		
		 
	// fabric_production_task Update Data 
		if($db_type==0)
		{ 
			$sql = "SELECT b.po_breakdown_id,b.entry_form, min(a.receive_date) mindate, max(a.receive_date) maxdate, sum(quantity) as prod_qntry 
	FROM inv_receive_master a,  order_wise_pro_details b, pro_finish_fabric_rcv_dtls c where  c.mst_id=a.id and c.id=b.dtls_id and b.entry_form in ( 7 ) and b.po_breakdown_id in ( $po_ids ) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and c.status_active =1 and c.is_deleted=0 group by b.po_breakdown_id,b.entry_form order by b.po_breakdown_id";
		}
		else
		{
			$job_no_list_arr=array_chunk(array_unique(explode(",",$po_ids)),999);
			
			$sql = "SELECT b.po_breakdown_id,b.entry_form, min(a.receive_date) mindate, max(a.receive_date) maxdate, sum(quantity) as prod_qntry 
	FROM inv_receive_master a,  order_wise_pro_details b, pro_finish_fabric_rcv_dtls c where  c.mst_id=a.id and c.id=b.dtls_id and b.entry_form in ( 7 ) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and c.status_active =1 and c.is_deleted=0";
			
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1) $sql .=" and (b.po_breakdown_id in(".implode(',',$job_no_process).")"; else  $sql .=" or b.po_breakdown_id in(".implode(',',$job_no_process).")";
				
				$p++;
			}
			$sql .=")";
			$sql .=" group by b.po_breakdown_id,b.entry_form order by b.po_breakdown_id";
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
	FROM inv_receive_master a,  order_wise_pro_details b, pro_grey_prod_entry_dtls c where  c.mst_id=a.id and c.id=b.dtls_id and b.entry_form in ( 2 ) and b.po_breakdown_id in ( $po_ids ) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and c.status_active =1 and c.is_deleted=0 group by b.po_breakdown_id,b.entry_form order by b.po_breakdown_id";
		}
		else
		{
			$job_no_list_arr=array_chunk(array_unique(explode(",",$po_ids)),999);
			
			$sql = "SELECT b.po_breakdown_id,b.entry_form, min(a.receive_date) mindate, max(a.receive_date) maxdate, sum(quantity) as prod_qntry 
	FROM inv_receive_master a,  order_wise_pro_details b, pro_grey_prod_entry_dtls c where  c.mst_id=a.id and c.id=b.dtls_id and b.entry_form in ( 2 )  and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and c.status_active =1 and c.is_deleted=0";
			
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1) $sql .=" and (b.po_breakdown_id in(".implode(',',$job_no_process).")"; else  $sql .=" or b.po_breakdown_id in(".implode(',',$job_no_process).")";
				
				$p++;
			}
			$sql .=")";
			$sql .=" group by b.po_breakdown_id,b.entry_form order by b.po_breakdown_id";
			
		}
		$data_array=sql_select($sql);
		foreach($data_array as $row)
		{
			$tna_task_update_data[$row[csf("po_breakdown_id")]][60]['max_start_date']=$row[csf("maxdate")];
			$tna_task_update_data[$row[csf("po_breakdown_id")]][60]['min_start_date']=$row[csf("mindate")];
			$tna_task_update_data[$row[csf("po_breakdown_id")]][60]['doneqnty']=$row[csf("prod_qntry")]; 
		}
		
		if($db_type==0)
		{
			$sql="select b.po_id, sum(b.batch_qnty) as dye_qnty, min(c.process_end_date) mindate, max(c.process_end_date) maxdate from pro_batch_create_mst a,pro_batch_create_dtls b, pro_fab_subprocess c where a.id=b.mst_id and a.id=c.batch_id and c.load_unload_id=2 and c.entry_form=35 and a.batch_against<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.po_id in ( $po_ids ) and c.status_active=1 and c.is_deleted=0 group by b.po_id";
		}
		else
		{
			
			$job_no_list_arr=array_chunk(array_unique(explode(",",$po_ids)),999);
			
			$sql = "select b.po_id, sum(b.batch_qnty) as dye_qnty, min(c.process_end_date) mindate, max(c.process_end_date) maxdate from pro_batch_create_mst a,pro_batch_create_dtls b, pro_fab_subprocess c where a.id=b.mst_id and a.id=c.batch_id and c.load_unload_id=2 and c.entry_form=35 and a.batch_against<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
			
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1) $sql .=" and (b.po_id in(".implode(',',$job_no_process).")"; else  $sql .=" or b.po_id in(".implode(',',$job_no_process).")";
				
				$p++;
			}
			$sql .=")";
			$sql .=" group by b.po_id";
			
			//echo $sql;die;
		}
		
		$data_array=sql_select($sql);
		foreach($data_array as $row)
		{
			$tna_task_update_data[$row[csf("po_id")]][61]['max_start_date']=$row[csf("maxdate")];
			$tna_task_update_data[$row[csf("po_id")]][61]['min_start_date']=$row[csf("mindate")];
			$tna_task_update_data[$row[csf("po_id")]][61]['doneqnty']=$row[csf("dye_qnty")]; 
		}
		
		unset($data_array);
		
		
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
			
			
			$job_no_list_arr=array_chunk(array_unique(explode(",",$job_no_list)),999);
			
			$sql = "SELECT po_break_down_id,min(inspection_date) as mind,max(inspection_date) as maxd,sum(inspection_qnty) as sumtot FROM pro_buyer_inspection WHERE status_active =1 and is_deleted = 0";
			
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1) $sql .=" and (job_no in(".implode(',',$job_no_process).")"; else  $sql .=" or job_no in(".implode(',',$job_no_process).")";
				
				$p++;
			}
			$sql .=")";
			$sql .=" group by po_break_down_id";
			
			//echo $sql;die;
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
			
			
			$job_no_list_arr=array_chunk(array_unique(explode(",",$po_ids)),999);
			
			$sql = "SELECT po_break_down_id,min(ex_factory_date) as mind,max(ex_factory_date) as maxd,sum(ex_factory_qnty) as sumtot FROM  pro_ex_factory_mst WHERE status_active =1 and is_deleted = 0";
			
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1) $sql .=" and (po_break_down_id in(".implode(',',$job_no_process).")"; else  $sql .=" or po_break_down_id in(".implode(',',$job_no_process).")";
				
				$p++;
			}
			$sql .=")";
			$sql .=" group by po_break_down_id";
			
			//echo $sql;die;
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
		WHERE b.po_breakdown_id in ( $po_ids ) and a.id=c.doc_submission_mst_id and c.invoice_id=b.mst_id and b.current_invoice_qnty>0 and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and c.status_active =1 and c.is_deleted=0 group by b.po_breakdown_id
		 ";
		}
		else
		{
		 
			$job_no_list_arr=array_chunk(array_unique(explode(",",$po_ids)),999);
			
			$sql = "SELECT  b.po_breakdown_id, min(a.submit_date) as mind,max(a.submit_date) as maxd, sum(b.current_invoice_qnty) as current_invoice_qnty 
		FROM  com_export_doc_submission_mst a,com_export_invoice_ship_dtls b, com_export_doc_submission_invo c
		WHERE a.id=c.doc_submission_mst_id and c.invoice_id=b.mst_id and b.current_invoice_qnty>0  and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and c.status_active =1 and c.is_deleted=0";
			
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1) $sql .=" and (b.po_breakdown_id in(".implode(',',$job_no_process).")"; else  $sql .=" or b.po_breakdown_id in(".implode(',',$job_no_process).")";
				
				$p++;
			}
			$sql .=")";
			$sql .=" group by b.po_breakdown_id";
			
			//echo $sql;die;
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
			$sql = "SELECT b.po_breakdown_id,max(d.received_date) maxd,min(d.received_date) mind, sum(b.current_invoice_qnty) as current_invoice_qnty FROM com_export_invoice_ship_dtls b, com_export_doc_submission_invo c, com_export_proceed_realization d WHERE c.invoice_id=b.mst_id and c.doc_submission_mst_id=d.invoice_bill_id and b.current_invoice_qnty>0 and  b.po_breakdown_id in ( $po_ids ) and d.status_active =1 and d.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and c.status_active =1 and c.is_deleted=0 group by b.po_breakdown_id";
		}
		else
		{
			
			$job_no_list_arr=array_chunk(array_unique(explode(",",$po_ids)),999);
			
			$sql = "SELECT b.po_breakdown_id,max(d.received_date) maxd,min(d.received_date) mind, sum(b.current_invoice_qnty) as current_invoice_qnty FROM com_export_invoice_ship_dtls b, com_export_doc_submission_invo c, com_export_proceed_realization d WHERE c.invoice_id=b.mst_id and c.doc_submission_mst_id=d.invoice_bill_id and b.current_invoice_qnty>0  and d.status_active =1 and d.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and c.status_active =1 and c.is_deleted=0";
			
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1) $sql .=" and (b.po_breakdown_id in(".implode(',',$job_no_process).")"; else  $sql .=" or b.po_breakdown_id in(".implode(',',$job_no_process).")";
				
				$p++;
			}
			$sql .=")";
			$sql .=" group by b.po_breakdown_id";
			
			//echo $sql;die;
		}
		$result = sql_select( $sql );
		foreach( $result as $row ) 
		{
			$tna_task_update_data[$row[csf("po_breakdown_id")]][121]['max_start_date']=$row[csf("maxd")];
			$tna_task_update_data[$row[csf("po_breakdown_id")]][121]['min_start_date']=$row[csf("mind")];
			$tna_task_update_data[$row[csf("po_breakdown_id")]][121]['doneqnty']=$row[csf("current_invoice_qnty")];
			$tna_task_update_data[$row[csf("po_breakdown_id")]][121]['reqqnty']=$po_order_details[$row[csf("po_breakdown_id")]]['po_quantity'];
			
		}
		
	// Garments Production Data for Update  
	
		if($db_type==0)
		{		 
			$sql = "SELECT po_break_down_id, min(production_date) as mind,max(production_date) as maxd, production_type,sum(production_quantity) as production_quantity,embel_name FROM  pro_garments_production_mst  WHERE po_break_down_id in ( $po_ids )  and status_active=1 and is_deleted=0   group by po_break_down_id,production_type,embel_name";
		}
		else
		{
			
			$job_no_list_arr=array_chunk(array_unique(explode(",",$po_ids)),999);
			
			$sql = "SELECT po_break_down_id, min(production_date) as mind,max(production_date) as maxd, production_type,sum(production_quantity) as production_quantity,embel_name FROM  pro_garments_production_mst  WHERE";
			
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1) $sql .=" (po_break_down_id in(".implode(',',$job_no_process).")"; else  $sql .=" or po_break_down_id in(".implode(',',$job_no_process).")";
				
				$p++;
			}
			$sql .=")";
			$sql .=" and status_active=1 and is_deleted=0 group by po_break_down_id,production_type,embel_name";
			
			//echo $sql;die;
		}
		//embel_name 	embel_type
		$result = sql_select( $sql );
	
		foreach( $result as $row ) 
		{
			$tsktype=0;
			if ($row[csf("production_type")]==1) $tsktype=84;
			else if ($row[csf("production_type")]==3) $tsktype=85;
			else if ($row[csf("production_type")]==4) $tsktype=122;
			else if ($row[csf("production_type")]==5) $tsktype=86;
			else if ($row[csf("production_type")]==7) $tsktype=87;
			else if ($row[csf("production_type")]==8) $tsktype=88; 
			else if ($row[csf("production_type")]==10) $tsktype=87;
			else if ($row[csf("production_type")]==11) $tsktype=91;
			
			if($row[csf("embel_name")]==3 && $row[csf("production_type")]==2) $tsktype=89;
			else if($row[csf("embel_name")]==3 && $row[csf("production_type")]==3) $tsktype=90;
			
			$tna_task_update_data[$row[csf("po_break_down_id")]][$tsktype]['max_start_date']=$row[csf("maxd")];
			$tna_task_update_data[$row[csf("po_break_down_id")]][$tsktype]['min_start_date']=$row[csf("mind")];
		//	$tna_task_update_data[$row[csf("po_break_down_id")]][$tsktype]['quantity']=$row[csf("production_quantity")];
			$tna_task_update_data[$row[csf("po_break_down_id")]][$tsktype]['doneqnty']=$row[csf("production_quantity")];
			$tna_task_update_data[$row[csf("po_break_down_id")]][$tsktype]['reqqnty']=$po_order_details[$row[csf("po_break_down_id")]]['po_quantity'];
		
		
		} 
		
	unset($result);
	
	
//Yarn Daying---------------------------------------------
		
		if($db_type==0)
		{
			$sql = "select max(a.receive_date) as max_receive_date,min(a.receive_date) as min_receive_date,d.id as po_break_down_id from inv_receive_master a, inv_transaction b, product_details_master c ,wo_po_break_down d
		where b.job_no in ( $job_no_list ) and b.job_no=d.job_no_mst and  a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and b.item_category=1 and a.entry_form=1 and b.status_active=1 and b.is_deleted=0 order by d.id asc";
		}
		else
		{
		 
			$job_no_list_arr=array_chunk(array_unique(explode(",",$job_no_list)),999);
			
			$sql_task = "select max(a.receive_date) as max_receive_date,min(a.receive_date) as min_receive_date,d.id as po_break_down_id from inv_receive_master a, inv_transaction b, product_details_master c,wo_po_break_down d 
		where b.job_no=d.job_no_mst and  a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and b.item_category=1 and a.entry_form=1 and b.status_active=1 and b.is_deleted=0";
			
			
			
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1) $sql_task .="and (b.job_no in(".implode(',',$job_no_process).")"; else  $sql_task .=" or b.job_no in(".implode(',',$job_no_process).")";
				
				$p++;
			}
			$sql_task .=")";
			
			$sql_task .=" group by d.id order by d.id asc";
			
			 //echo $sql_task;die;
		}
		
		$result = sql_select( $sql_task );
		$embelishment_update_task = array(); 
		foreach( $result as $row ) 
		{
			$to_process_task[$row[csf("po_break_down_id")]][52]=52;
			$tna_task_update_data[$row[csf("po_break_down_id")]][52]['max_start_date']=$row[csf("max_receive_date")];
			$tna_task_update_data[$row[csf("po_break_down_id")]][52]['min_start_date']=$row[csf("min_receive_date")];
			
			$tna_task_update_data[$row[csf("order_id")]][52]['doneqnty']=1;
			$tna_task_update_data[$row[csf("order_id")]][52]['reqqnty']=1;			
		}


//AOP Sent/AOP Receive........................................

		if($db_type==0)
		{
		$sql="select a.entry_form,b.order_id, max(a.receive_date) as  maxd, min(a.receive_date)  as mind  from inv_receive_mas_batchroll  a, pro_grey_batch_dtls b where a.id=b.mst_id and b.order_id in ( $po_ids ) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.entry_form in(91,92) group by  a.entry_form,b.order_id order by a.entry_form,b.order_id";
		}
		else
		{
			$job_no_list_arr=array_chunk(array_unique(explode(",",$po_ids)),999);
			$sql = "select a.entry_form,b.order_id, max(a.receive_date) as  maxd, min(a.receive_date) as mind  from inv_receive_mas_batchroll  a, pro_grey_batch_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.entry_form in (91,92) ";
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1) $sql .=" and (b.order_id in (".implode(',',$job_no_process).")"; else  $sql .=" or b.order_id in (".implode(',',$job_no_process).")";
				
				$p++;
			}
			$sql .=")";
			$sql .=" group by a.entry_form,b.order_id";
			
			 //echo $sql;die;
		}
		$result = sql_select( $sql );
		foreach( $result as $row ) 
		{
			if($row[csf("entry_form")]==91) $taskKey=62;
			else if($row[csf("entry_form")]==92) $taskKey=63;
			
			$to_process_task[$row[csf("order_id")]][$taskKey]=$taskKey;
			$tna_task_update_data[$row[csf("order_id")]][$taskKey]['max_start_date']=$row[csf("maxd")];
			$tna_task_update_data[$row[csf("order_id")]][$taskKey]['min_start_date']=$row[csf("mind")];
			
			$tna_task_update_data[$row[csf("order_id")]][$taskKey]['doneqnty']=1;
			$tna_task_update_data[$row[csf("order_id")]][$taskKey]['reqqnty']=1;			
		}



//-------------------------
	
	 //print_r($template_wise_task[17]);die;
	//echo $tna_task_update_data[8543][70]['max_start_date'].'**'. $tna_task_update_data[8543][70]['min_start_date'];die;
	
	
	 
		$field_array_tna_process="id,template_id,job_no,po_number_id,po_receive_date,shipment_date,task_category,task_number,target_date,task_start_date,task_finish_date,notice_date_start,notice_date_end,process_date,sequence_no,plan_start_flag,plan_finish_flag,status_active,is_deleted,task_type";
		
		$field_array_tna_process_up="actual_start_date*actual_finish_date";

		$approval_array=array(0=>7,1=>8,2=>9,3=>10,4=>11,5=>12,6=>13,7=>14,8=>15,9=>16,10=>17,11=>19,12=>20);
		
		
		
		foreach( $po_order_details as $row )  // Non Process Starts Here
		{
			
			foreach( $template_wise_task[$row[template_id]]  as $task_id=>$row_task)
			{  
				// if($task_id==19){print_r($to_process_task[$row[po_id]]);die;}
				 
				 
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
					$new_target_data[$row[po_id]][60]['st_date']=$start_date;
					$new_target_data[$row[po_id]][60]['end_date']=$finish_date;
					
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
						
						
						$data_array_tna_process .="('$mst_id','$row[template_id]','$row[job_no_mst]','$row[po_id]','$row[po_received_date]','$row[shipment_date]','1','$row_task[task_name]','$target_date','$start_date','$finish_date','$notice_date_start','$notice_date_end','$date','$row_task[sequence_no]',$plan_start_flag,$plan_finish_flag,1,0,1)";
						
						$insert_string[] ="('$mst_id','$row[template_id]','$row[job_no_mst]','$row[po_id]','$row[po_received_date]','$row[shipment_date]','1','$row_task[task_name]','$target_date','$start_date','$finish_date','$notice_date_start','$notice_date_end','$date','$row_task[sequence_no]',$plan_start_flag,$plan_finish_flag,1,0,1)";
						
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
						
	//if($row_task[task_name]==70){echo $tna_task_update_data[$row[po_id]][$row_task[task_name]]['doneqnty'].','. $tna_task_update_data[$row[po_id]][$row_task[task_name]]['reqqnty'];die;}
	
	
	
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
	 
	 //print_r($data_array_tna_process_up[$tna_process_list[8543][70]]);die;
	
	
		$file = 'tna_log.txt';
		$current = file_get_contents($file);
		$current .= "TNA-PROCESS:: Company ID: ".$cbo_company.", Date and Time: ".date("d-m-Y H:i:s",time())."\n";
	 	file_put_contents($file, $current);
		
	
	
	
	} // Foreach Company level
//	  die;
//	  die;



   //print_r($tna_process_list);die;

	/* $idd=return_next_id("id", "tna_grey_fab_target", 1);
	 $field_array_grey_target="id,po_id,target_qnty,target_date,tna_task_id,inserted_by,insert_date";	
	  foreach($tna_task_update_data as $po=>$qnty )
	  {
		  if($del_po=="") $del_po=$po; else $del_po .=",".$po;
		  $dur=datediff("d",$new_target_data[$po][60]['st_date'],$new_target_data[$po][60]['end_date']);
		  //echo $dur;die;
		  $avg=$qnty[60]['reqqnty']/$dur;
		  for($k=0; $k<$dur; $k++)
		  {
			  $cdate=add_date($new_target_data[$po][60]['st_date'], $k);
			  if($db_type==2) $cdate=change_date_format($cdate,'','',-1);
				  
			  if($data_array_grey_target!="") $data_array_grey_target .=",";
			  $data_array_grey_target .= "(".$idd.",'".$po."','".$avg."','".$cdate ."',60,'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
			  $idd++;
		  }
	  }
	  
	 $del_po=execute_query("delete fom  tna_grey_fab_target where po_id in ($del_po)",1);
	 $rID_tr=sql_insert("tna_grey_fab_target",$field_array_grey_target,$data_array_grey_target,1);
	 
	 */
	
	
	//echo "0**".count($process_id_up_array); die;
	
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

//echo '10**reza';die;
//....................Auto Mail Send................................................
$buyer_array=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

ob_start();
?>	
 <table width="100%" cellpadding="0" cellspacing="0" border="1">
    <tr>
        <td colspan="7" height="40" align="center"><strong>Missing P.O No list of TNA Process</strong></td>
    </tr>
    <tr>
        <td width="50" align="center"><strong>SL</strong></td>
        <td width="100" align="center"><strong>Job No</strong></td>
        <td width="150"><strong>Style Ref.</strong></td>
        <td width="100"><strong>Buyer Name</strong></td>
        <td width="100" align="center"><strong>P.O No</strong></td>
        <td width="100" align="center"><strong>PO Receive Date</strong></td>
        <td align="center"><strong>Shipment Date</strong></td>
    </tr>
<?	$i=1;
	foreach($template_missing_po_mail_data_arr as $val_arr)
	{		
?>
    <tr>
        <td align="center"><? echo $i; ?></td>
        <td align="center"><? echo $val_arr['job_no_mst']; ?></td>
        <td><? echo $val_arr['style_ref_no']; ?></td>
        <td><? echo $buyer_array[$val_arr['buyer_name']]; ?></td>
        <td><? echo $val_arr['po_number']; ?></td>
        <td align="center"><? echo $val_arr['po_received_date']; ?></td>
        <td align="center"><? echo $val_arr['shipment_date']; ?></td>
    </tr>
<? 
	$i++;
	} 
?>	
    <tr>
        <tfoot>
            <th colspan="7">&nbsp;</th>
        </tfoot>
    </tr>
 </table>
	
<?	

	/*	$to="";
		$sql2 = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=6 and b.mail_user_setup_id=c.id and a.company_id=$cbo_company";
		$mail_sql2=sql_select($sql2);
		foreach($mail_sql2 as $row)
		{
			if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
		}
		//$to="phpboss2010@gmail.com";
		$subject="Missing P.O No list of TNA Process";
    	$message="";
    	$message=ob_get_contents();
    	ob_clean();
		$header=mail_header();
		if($is_manual_process!=1){send_mail_mailer( $to, $subject, $message, $from_mail ); }
*/
//.....................................................................................
	
	disconnect($con);
	echo "0**".$rID."**".implode(", ",$template_missing_po);
	//echo "INSERT INTO pro_garments_production_dtls (".$field_array.") VALUES ".$data_array;die;
	die;
	
}


function get_tna_template( $remain_days, $tna_template, $buyer ) // Always treat the lowest template ... if not no process on that
{
	
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


<?php /*?>

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
   
<?php */?>   
   
   
<!-- //////////////////////////////////////////// --> 
  

     
	<script>
/*	function set_checkvalue()
	{
		if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
		else document.getElementById('chk_job_wo_po').value=0;
	}
	
	function js_set_value( job_no )
	{
		document.getElementById('selected_job').value=job_no;
		parent.emailwindow.hide();
	}

*/    </script>

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
								echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $company,"load_drop_down( 'tna_process_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
							?>
                    </td>
                   	<td id="buyer_td">
                     <? 
						echo create_drop_down( "cbo_buyer_name", 172, $blank_array,'', 1, "-- Select Buyer --");
					?>	</td>
                    <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:80px"></td>
                    <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:100px"></td>
                    <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:120px"></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
					  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 </td> 
            		 <td align="center">
                     <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value, 'ponumber_search_list_view', 'search_div', 'tna_process_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        		</tr>
             </table>
          </td>
        </tr>
        <tr>
            <td  align="center" height="40" valign="middle">
             <? 
			echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );		
			?>
			<? echo load_month_buttons();  ?>
            </td>
            </tr>
        <tr>
            <td align="center" valign="top" id="search_div"> 
	
            </td>
        </tr>
    </table>    
     
    </form>
   </div>
  
  
   
<!-- //////////////////////////////////////////// --> 
   
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>

var buyer='<? echo $buyer; ?>';
load_drop_down( 'tna_process_controller', <? echo $company;?>, 'load_drop_down_buyer', 'buyer_td' );
document.getElementById('cbo_buyer').value=buyer;
</script>
</html>
<?
}

if ($action=="ponumber_search_list_view")
{
 list($company,$buyer,$start_date,$end_date,$job_no,$year,$surch_by,$order_no,$style_no)=explode('_',$data);
 
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
					foreach($sql_result as $row)
				   {
					  if ($i%2==0)  
					  $bgcolor="#E9F3FF";
					  else
					  $bgcolor="#FFFFFF";
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
 	
	
}
 
?>
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
Update date		         :  11.05.2015		   
QC Performed BY	         :	
QC Date			         :	
Comments		         :  From this version oracle conversion is start
----------------------------------------------------------------------*/
header('Content-type:text/html; charset=utf-8');
session_start();
extract($_REQUEST);
$user_id = 1000;
$entry_form = 1000;
$gross_level = 0;
$is_pub_shipment_date = 0;

if ($action == "") {
	$action = "tna_process";
	$cornd_service = true;
}


include('../../../includes/common.php');
//require_once('../../../mailer/class.phpmailer.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.fabrics.php');
require_once('../../../includes/class4/class.yarns.php');
require_once('../../../includes/class4/class.washes.php');
require_once('../../../includes/class4/class.emblishments.php');
require_once('../../../includes/class4/class.trims.php');
//require_once('../../../includes/class3/class.conversions.php');

if ($action == "load_drop_down_buyer") {
	echo create_drop_down("cbo_buyer", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name", "id,buyer_name", 1, "-- All Buyer --", $selected, "");
	die;
}



if ($cbo_company < 1) $company_array = return_library_array("select id,id from lib_company", 'id', 'id');
else $company_array[$cbo_company] = $cbo_company;

if ($action == "tna_process") {
	$tba_color_id = return_field_value("id", "lib_color", " color_name ='TBA'");
	$trim_type_arr = return_library_array("select id,trim_type from lib_item_group where  item_category = 4 and trim_type in(1,2) and is_deleted = 0 and status_active = 1", 'id', 'trim_type');


	$sql = "select TASK_NAME,COMPLETION_PERCENT,TASK_SEQUENCE_NO from  lib_tna_task where is_deleted = 0 and status_active=1 and task_type=1 order by task_sequence_no asc";
	$resultRes = sql_select($sql);
	foreach ($resultRes as $row) {
		$tna_completion[$row['TASK_NAME']] = $row['COMPLETION_PERCENT'];
		$tna_completion[$row['TASK_NAME']] = $row['COMPLETION_PERCENT'];
	}

	$sql = "SELECT COMPANY_NAME,TNA_INTEGRATED FROM  variable_order_tracking WHERE status_active =1 and is_deleted = 0 and variable_list=14";
	$resultRes = sql_select($sql);
	$variable_settings = array();
	foreach ($resultRes as $row) {
		$variable_settings[$row['COMPANY_NAME']] = $row['TNA_INTEGRATED'];
	}




	foreach ($company_array as $cbo_company) {
		$tna_process_type = return_field_value("tna_process_type", " variable_order_tracking", " company_name=" . $cbo_company . " and variable_list=31");
		$tna_process_start_date = return_field_value("tna_process_start_date", " variable_order_tracking", " company_name=" . $cbo_company . " and variable_list=43");

		if ($cornd_service == false) {
			if ($tna_process_type == '') {
				echo "0**0**Please Set TNA Process Type";
				exit();
			}
			if ($tna_process_start_date == '') {
				echo "0**0**Please Set TNA Process Start Date";
				exit();
			}
		}


		if ($tna_process_type == 2) //Parcent base;
		{
			$sql = "SELECT task_id,buyer_id,start_percent,end_percent,notice_before FROM  tna_task_entry_percentage WHERE is_deleted = 0 and status_active=1 order by task_id asc";
			$result = sql_select($sql);
			$tna_task_percent = array();
			$tna_task_percent_buyer = array();
			foreach ($result as $row) {
				$tna_task_percent[$row[csf('task_id')]]['task_name'] = $row[csf('task_id')];
				$tna_task_percent[$row[csf('task_id')]]['buyer_id'] = $row[csf('buyer_id')];
				$tna_task_percent[$row[csf('task_id')]]['start_percent'] = $row[csf('start_percent')];
				$tna_task_percent[$row[csf('task_id')]]['end_percent'] = $row[csf('end_percent')];
				$tna_task_percent[$row[csf('task_id')]]['notice_before'] = $row[csf('notice_before')];
				$tna_task_percent[$row[csf('task_id')]]['completion_percent'] = $tna_completion[$row[csf('task_id')]];

				$tna_task_percent_buyer_wise[$row[csf('buyer_id')]] = $row[csf('buyer_id')];
				$tna_task_percent_buyer[$row[csf('buyer_id')]][$row[csf('task_id')]]['task_name'] = $row[csf('task_id')];
				$tna_task_percent_buyer[$row[csf('buyer_id')]][$row[csf('task_id')]]['buyer_id'] = $row[csf('buyer_id')];
				$tna_task_percent_buyer[$row[csf('buyer_id')]][$row[csf('task_id')]]['start_percent'] = $row[csf('start_percent')];
				$tna_task_percent_buyer[$row[csf('buyer_id')]][$row[csf('task_id')]]['end_percent'] = $row[csf('end_percent')];
				$tna_task_percent_buyer[$row[csf('buyer_id')]][$row[csf('task_id')]]['notice_before'] = $row[csf('notice_before')];
				$tna_task_percent_buyer[$row[csf('buyer_id')]][$row[csf('task_id')]]['completion_percent'] = $tna_completion[$row[csf("task_id")]];
			}
		} else if ($tna_process_type == 1) //Templae base;
		{
			$sql = "SELECT id,task_catagory,task_name,task_short_name,task_type,module_name,link_page,penalty,completion_percent,task_sequence_no FROM lib_tna_task WHERE is_deleted = 0 and status_active=1 and task_type=1 order by task_sequence_no";
			$result = sql_select($sql);
			$tna_task_details = array();
			$tna_task_name = array();
			$tna_task_name_tmp = array();
			foreach ($result as $row) {
				$tna_task_name[$row[csf('id')]] = $row[csf('task_name')];
				$tna_task_details[$row[csf('task_name')]]['task_catagory'] = $row[csf('task_catagory')];
				$tna_task_details[$row[csf('task_name')]]['id'] = $row[csf('id')];
				$tna_task_details[$row[csf('task_name')]]['task_name'] = $row[csf('task_name')];
				$tna_task_details[$row[csf('task_name')]]['task_short_name'] = $row[csf('task_short_name')];
				$tna_task_details[$row[csf('task_name')]]['task_type'] = $row[csf('task_type')];
				$tna_task_details[$row[csf('task_name')]]['module_name'] = $row[csf('module_name')];
				$tna_task_details[$row[csf('task_name')]]['link_page'] = $row[csf('link_page')];
				$tna_task_details[$row[csf('task_name')]]['penalty'] = $row[csf('penalty')];
				$tna_task_details[$row[csf('task_name')]]['completion_percent'] = $row[csf('completion_percent')];

				$tna_task_seq_arr[$row[csf('task_name')]] = $row[csf('task_sequence_no')];
			}
		}


		//Template Details


		$sql_task = "SELECT a.id,a.COMPANY_ID,task_template_id,lead_time,material_source,total_task,tna_task_id,deadline,execution_days,notice_before,sequence_no,for_specific,b.task_catagory,b.task_name,b.task_type FROM  tna_task_template_details a, lib_tna_task b WHERE   a.tna_task_id=b.task_name and a.is_deleted=0 and a.status_active=1 and a.task_type=1 and b.is_deleted=0 and b.status_active=1 and a.company_id in(" . implode(',', $company_array) . ",0) order by for_specific,lead_time";
		//echo $sql_task;die;
		$result = sql_select($sql_task);
		$tna_task_template = array();
		$tna_task_template_task = array();
		$tna_template = array();
		$template = array();
		$tna_template_company_arr = array();
		$tna_template_buyer = array();
		$template_wise_task = array();
		$tna_template_spc = array();
		$template_information = array();
		$i = 0;
		$k = 0;
		$j = 0;
		$m = 0;
		$n = 0;
		foreach ($result as $row) {
			if ($template[$row[csf("task_template_id")]] == '') {
				$template[$row[csf("task_template_id")]] = $row[csf("task_template_id")];
				if ($row[csf("for_specific")] == 0) {
					$tna_template[$m]['lead'] = $row[csf("lead_time")];
					$tna_template[$m]['id'] = $row[csf("task_template_id")];
					$i++;
					$m++;
				} else {
					if (!in_array($row[csf('for_specific')], $tna_template_spc)) {
						$j = 0;
						$tna_template_spc[] = $row[csf("for_specific")];
					}
					$tna_template_buyer[$row[csf('for_specific')]][$j]['lead'] = $row[csf('lead_time')];
					$tna_template_buyer[$row[csf('for_specific')]][$j]['id'] = $row[csf('task_template_id')];

					$tna_template_company_arr[$row[csf('COMPANY_ID')]][$row[csf('for_specific')]][$j]['lead'] = $row[csf('lead_time')];
					$tna_template_company_arr[$row[csf('COMPANY_ID')]][$row[csf('for_specific')]][$j]['id'] = $row[csf('task_template_id')];

					$j++;
				}
				$k++;
			}

			$template_wise_task[$row[csf("task_template_id")]][$row[csf("task_name")]]['deadline'] = $row[csf("deadline")];
			$template_wise_task[$row[csf("task_template_id")]][$row[csf("task_name")]]['execution_days'] = $row[csf("execution_days")];
			$template_wise_task[$row[csf("task_template_id")]][$row[csf("task_name")]]['notice_before'] = $row[csf("notice_before")];
			$template_wise_task[$row[csf("task_template_id")]][$row[csf("task_name")]]['sequence_no'] = $row[csf("sequence_no")];
			$template_wise_task[$row[csf("task_template_id")]][$row[csf("task_name")]]['for_specific'] = $row[csf("for_specific")];
			$template_wise_task[$row[csf("task_template_id")]][$row[csf("task_name")]]['task_name'] = $row[csf("task_name")];
			$template_wise_task[$row[csf("task_template_id")]][$row[csf("task_name")]]['completion_percent'] = $tna_completion[$row[csf("task_name")]];

			$g++;
			$i++;
		}



		if ($db_type == 0) $blank_date = "0000-00-00";
		else $blank_date = "";
		//Reprocess Check...................................................



		if (str_replace("'", "", $txt_job_no) != '') {
			$job_con = " and a.job_no='$txt_job_no'";
		}

        $is_pub_shipment_date =return_field_value("TEXTILE_TNA_PROCESS_BASE"," variable_order_tracking","variable_list=31 and company_name='".$cbo_company."'"); 
		$shipment_date = ($is_pub_shipment_date == 1) ? "b.pub_shipment_date" : "b.shipment_date";

		if ($txt_ponumber_id != "") {
			$where_con = " and b.id  in ( $txt_ponumber_id ) ";
		}

		//$job_id = "6173,6217,5745,6105,6119,6118,6141,6074,6126,6025,5992,6077,6185,6274,6188,6132,6275,5446,6215,6086,6164,6018,5918,5942,5921,6163,6017,5919,6045,6044"; //and a.id in($job_id) 

		$sql = "SELECT b.JOB_ID,$shipment_date as SHIPMENT_DATE,a.COMPANY_NAME,JOB_NO_MST,PO_RECEIVED_DATE,b.ID,PO_QUANTITY,a.BUYER_NAME,a.GARMENTS_NATURE,b.PO_NUMBER,PP_MEETING_DATE,a.STYLE_REF_NO,IS_CONFIRMED,TNA_TASK_FROM_UPTO,(b.po_quantity*a.TOTAL_SET_QNTY)as PO_QUANTITY_PCS FROM wo_po_break_down b, wo_po_details_master a WHERE b.is_deleted = 0 and b.status_active=1 and b.SHIPING_STATUS=3 and a.is_deleted = 0 and a.status_active=1 and a.id=b.job_id and to_char(b.pub_shipment_date)!='0000-00-00' and to_char(b.po_received_date)!='0000-00-00' $where_con  and $shipment_date > '$tna_process_start_date' and company_name=" . $cbo_company . " $buyer_cond $job_con ORDER BY b.shipment_date asc";

		 //echo $sql;die;
		$tmp_data_array = sql_select($sql);

		$po_data_chunk_array = array_chunk($tmp_data_array,999);
		unset($tmp_data_array);

		// print_r($po_data_chunk_array[0]);die;

		foreach($po_data_chunk_array as $data_array){

		$job_id_array = array();$tmp_po_array = array();
		foreach ($data_array as $row) {
			$job_id_array[$row['JOB_ID']] = $row['JOB_ID'];
			$tmp_po_array[$row['ID']] = $row['ID'];
		}

		// Delete TNA Data....................................................
		if ($is_delete == 1) {
			$con = connect();
			$rid = execute_query("delete FROM tna_process_mst WHERE  task_type=1 and po_number_id in(".implode(',',$tmp_po_array).")", 1);
			oci_commit($con);
			disconnect($con);
		}

		if ($delete_history_process == 1) {
			$con = connect();
			$rid = execute_query("update TNA_PLAN_ACTUAL_HISTORY set STATUS_ACTIVE=0,IS_DELETED=1 WHERE  task_type=1  and po_number_id in(".implode(',',$tmp_po_array).")", 1);
			oci_commit($con);
			disconnect($con);
		}
		//End TNA Delete..............................................



		if ($cbo_buyer > 0) {
			$buyer_cond = " and a.buyer_name=$cbo_buyer ";
		} else {
			$buyer_cond = "";
		}

		$condition = new condition();
		if ($cbo_company > 0) {
			$condition->company_name("=$cbo_company");
		}
		if ($cbo_buyer > 0) {
			$condition->buyer_name("=$cbo_buyer");
		}
		if ($tna_process_start_date != '') {
			if($is_pub_shipment_date == 1){
				$condition->pub_shipment_date(" > '" . $tna_process_start_date . "'");
			}
			else{
				$condition->shipment_date(" > '" . $tna_process_start_date . "'");
			}

			//$condition->pub_shipment_date(" > '" . $tna_process_start_date . "'");
		}

		if (count($job_id_array)) {
			$condition->jobid_in(implode(',', $job_id_array));
		}

		//echo count($job_id_array);die;

		if (trim($txt_ponumber_id) != '') {
			$condition->po_id(" in($txt_ponumber_id) ");
		}



		$condition->init();
		$fabric = new fabric($condition);
		//echo $fabric->getQuery();die;
		$fabricdata_production = $fabric->getQtyArray_by_order_knitAndwoven_greyAndfinish_production();
		$fabricdata_purchase = $fabric->getQtyArray_by_order_knitAndwoven_greyAndfinish_purchase();

		$fabricdata = $fabric->getQtyArray_by_orderAndSourceId_knitAndwoven_greyAndfinish();

		$yarn = new yarn($condition);
		$yarndata = $yarn->getOrderColorTypeWiseYarnQtyArray();
		//echo $yarn->getQuery();die;

		$wash = new wash($condition);
		$wash_data = $wash->getQtyArray_by_order();


		$emblishment = new emblishment($condition);
		$emblishment_data = $emblishment->getQtyArray_by_order();

		$trims = new trims($condition);
		$trims_data = $trims->getQtyArray_by_orderSourceidAndItemidType();





		//$trims_data_item_qty=$trims->getQtyArray_by_orderAndItemid();
		$trims_data_item_qty = $trims->getQtyArray_by_orderSourceidAndItemid();

		//echo $trims->getQuery();die;

		//print_r($trims_data_item_qty);die;


		//echo 1;die;

		$to_process_task = array();
		$job_no_array = array();
		$order_id_array = array();
		$po_order_template = array();
		$po_order_details = array();
		$job_nature = array();
		$template_missing_po = array();
		$tna_task_update_data = array();
		$template_missing_po_mail_data_arr = array();
		$i = 0;

		foreach ($data_array as $row) {
			$remain_days = datediff("d", date("Y-m-d", strtotime($row[csf("po_received_date")])), date("Y-m-d", strtotime($row[csf("shipment_date")])));

			if ($tna_process_type == 1) //Templae base;
			{
				$template_id = get_tna_template($remain_days, $tna_template, $row[csf("buyer_name")], $row[csf("company_name")]);
			} else {
				$template_id = $remain_days - 1;
				if ($tna_task_percent_buyer_wise[$row[csf('buyer_name')]] == "") {
					foreach ($tna_task_percent as $id => $data) {
						$deadline = floor($template_id * $data['start_percent'] / 100);
						$exe = floor($template_id * $data['end_percent'] / 100);
						if ($deadline == 0) $v = 0;
						else $v = 1;
						if ($exe == 0) $e = 0;
						else $e = 1;
						$template_wise_task[$template_id][$id]['deadline'] = $deadline - $v;
						$template_wise_task[$template_id][$id]['execution_days'] = $exe - $e;
						$template_wise_task[$template_id][$id]['notice_before'] = $data['notice_before'];
						$template_wise_task[$template_id][$id]['sequence_no'] = $row['sequence_no'];
						$template_wise_task[$template_id][$id]['for_specific'] = $data['buyer_id'];
						$template_wise_task[$template_id][$id]['task_name'] = $id;
						$template_wise_task[$template_id][$id]['completion_percent'] = $data['completion_percent'];
					}
				} else {
					foreach ($tna_task_percent_buyer[$row[csf("buyer_name")]] as $id => $data) {
						$deadline = floor($template_id * $data['start_percent'] / 100);
						$exe = floor($template_id * $data['end_percent'] / 100);
						if ($deadline == 0) $v = 0;
						else $v = 1;
						if ($exe == 0) $e = 0;
						else $e = 1;

						$template_wise_task[$template_id][$id]['deadline'] = $deadline - $v;
						$template_wise_task[$template_id][$id]['execution_days'] = $exe - $e;
						$template_wise_task[$template_id][$id]['notice_before'] = $data['notice_before'];
						$template_wise_task[$template_id][$id]['sequence_no'] = $row['sequence_no'];
						$template_wise_task[$template_id][$id]['for_specific'] = $data['buyer_id'];
						$template_wise_task[$template_id][$id]['task_name'] = $id;
						$template_wise_task[$template_id][$id]['completion_percent'] = $data['completion_percent'];
					}
				}
			}




			if ($template_id == "" || $template_id == 0) {
				$template_missing_po[$row[csf("po_number")]] = $row[csf("po_number")];
				//This array for missiong PO Auto mail send..............
				$template_missing_po_mail_data_arr[] = array(
					'job_no_mst'		=> $row[csf("job_no_mst")],
					'style_ref_no'		=> $row[csf("style_ref_no")],
					'buyer_name'		=> $row[csf("buyer_name")],
					'po_number'			=> $row[csf("po_number")],
					'po_received_date'	=> $row[csf("po_received_date")],
					'shipment_date'		=> $row[csf("shipment_date")]

				);
			} else {
				/*if( array_sum($fabricdata['knit']['grey'][$row[csf("id")]])>0) // Purchase
				{
					$tna_task_update_data[$row[csf("id")]][31]['reqqnty']+=array_sum($fabricdata['knit']['grey'][$row[csf("id")]]);
					$to_process_task[$row[csf("id")]][31]=31;
				}*/


				// Purchase
				foreach ($fabricdata['knit']['grey'][$row[csf("id")]] as $sourceId => $sourceUomRows) {
					if ($sourceId == 1) {
						$tna_task_update_data[$row[csf("id")]][276]['reqqnty'] = array_sum($fabricdata['knit']['grey'][$row[csf("id")]][$sourceId]);
						$to_process_task[$row[csf("id")]][276] = 276;
					} else {

						$tna_task_update_data[$row[csf("id")]][31]['reqqnty'] += array_sum($fabricdata['knit']['grey'][$row[csf("id")]][$sourceId]);
						$to_process_task[$row[csf("id")]][31] = 31;

						$tna_task_update_data[$row[csf("id")]][305]['reqqnty'] += array_sum($fabricdata['knit']['grey'][$row[csf("id")]][$sourceId]);
						$to_process_task[$row[csf("id")]][305] = 305;
					}
				}

				//  print_r($fabricdata['woven']['finish'][$row[csf("id")]]);die;


				foreach ($fabricdata['woven']['finish'][$row[csf("id")]] as $sourceId => $sourceUomRows) {

					if ($sourceId == 1) {
						$tna_task_update_data[$row[csf("id")]][353]['reqqnty'] += array_sum($sourceUomRows);
						$to_process_task[$row[csf("id")]][353] = 353;
					} else {
						$tna_task_update_data[$row[csf("id")]][353]['reqqnty'] += array_sum($sourceUomRows);
						$to_process_task[$row[csf("id")]][353] = 353;
					}
				}

				// print_r($fabricdata['woven']['finish']);die;



				// if( array_sum($fabricdata['woven']['grey'][$row[csf("id")]])>0) // Woven Purchase
				// {
				// 			//$tna_task_update_data[$row[csf("id")]][31]['reqqnty']+=array_sum($fabricdata['woven']['grey'][$row[csf("id")]]);
				//    			// $to_process_task[$row[csf("id")]][31]=31;

				// }



				if (array_sum($fabricdata_production['knit']['grey'][$row[csf("id")]]) > 0) {
					$tna_task_update_data[$row[csf("id")]][60]['reqqnty'] += array_sum($fabricdata_production['knit']['grey'][$row[csf("id")]]);
					$to_process_task[$row[csf("id")]][60] = 60;
					$tna_task_update_data[$row[csf("id")]][72]['reqqnty'] += array_sum($fabricdata_production['knit']['grey'][$row[csf("id")]]);
					$to_process_task[$row[csf("id")]][72] = 72;

					$tna_task_update_data[$row[csf("id")]][61]['reqqnty'] += array_sum($fabricdata_production['knit']['grey'][$row[csf("id")]]);
					$to_process_task[$row[csf("id")]][61] = 61;



					// $tna_task_update_data[$row[csf("id")]][31]['reqqnty']+=array_sum($fabricdata_production['knit']['grey'][$row[csf("id")]]);
					// $to_process_task[$row[csf("id")]][31]=31;

					$tna_task_update_data[$row[csf("id")]][314]['reqqnty'] += array_sum($fabricdata_production['knit']['grey'][$row[csf("id")]]);
					$to_process_task[$row[csf("id")]][314] = 314;
				}

				if (array_sum($fabricdata_production['knit']['finish'][$row[csf("id")]]) > 0) {
					$tna_task_update_data[$row[csf("id")]][64]['reqqnty'] += array_sum($fabricdata_production['knit']['finish'][$row[csf("id")]]);
					$to_process_task[$row[csf("id")]][64] = 64;

					$tna_task_update_data[$row[csf("id")]][74]['reqqnty'] += array_sum($fabricdata_production['knit']['grey'][$row[csf("id")]]);
					$to_process_task[$row[csf("id")]][74] = 74;
				}


				if (array_sum($fabricdata_purchase['knit']['finish'][$row[csf("id")]]) > 0) {
					$tna_task_update_data[$row[csf("id")]][74]['reqqnty'] += array_sum($fabricdata_purchase['knit']['grey'][$row[csf("id")]]);
					$to_process_task[$row[csf("id")]][74] = 74;
				}



				// if( count($trims_data[$row[csf("id")]])>0 )
				// {
				// 	foreach($trims_data[$row[csf("id")]] as $source_id=>$dataRows){
				// 		if($source_id==1){
				// 			$tna_task_update_data[$row[csf("id")]][278]['reqqnty']+=$trims_data[$row[csf("id")]][$source_id];
				// 			$to_process_task[$row[csf("id")]][278]=278;
				// 		}
				// 		else{

				// 			$tna_task_update_data[$row[csf("id")]][32]['reqqnty']+=$trims_data[$row[csf("id")]][$source_id];
				// 			$to_process_task[$row[csf("id")]][32]=32;

				// 			$tna_task_update_data[$row[csf("id")]][307]['reqqnty']+=$trims_data[$row[csf("id")]][$source_id];
				// 			$to_process_task[$row[csf("id")]][307]=307;

				// 		}
				// 	}
				// }




				if (count($trims_data[$row[csf("id")]]) > 0) {
					foreach ($trims_data[$row[csf("id")]] as $source_id => $dataRows) {
						foreach ($dataRows as $item_id => $dataRow) {

							if ($source_id == 1) {
								if ($trims_data[$row[csf("id")]][$source_id][$item_id][2]) {
									$tna_task_update_data[$row[csf("id")]][278]['reqqnty'] += $trims_data[$row[csf("id")]][$source_id][$item_id][2];
									$to_process_task[$row[csf("id")]][278] = 278;
								}
							} else {
								if ($trims_data[$row[csf("id")]][$source_id][$item_id][2]) {
									$tna_task_update_data[$row[csf("id")]][32]['reqqnty'] += $trims_data[$row[csf("id")]][$source_id][$item_id][2];
									$to_process_task[$row[csf("id")]][32] = 32;
									$tna_task_update_data[$row[csf("id")]][307]['reqqnty'] += $trims_data[$row[csf("id")]][$source_id][$item_id][2];
									$to_process_task[$row[csf("id")]][307] = 307;
								}
							}
						}
					}
				}



				//print_r($fabricdata['woven']['grey'][$row[csf("id")]]);die;


				foreach ($fabricdata['woven']['grey'][$row[csf("id")]] as $sourceId => $sourceUomRows) {
					if ($sourceId == 1) {
						$tna_task_update_data[$row[csf("id")]][302]['reqqnty'] = array_sum($fabricdata['woven']['grey'][$row[csf("id")]][$sourceId]);
						$to_process_task[$row[csf("id")]][302] = 302;
					} else {
						$tna_task_update_data[$row[csf("id")]][34]['reqqnty'] += array_sum($fabricdata['woven']['grey'][$row[csf("id")]][$sourceId]);
						$to_process_task[$row[csf("id")]][34] = 34;
					}
				}


				if (array_sum($yarndata[$row[csf("id")]]) > 0) {
					if ($gross_level == 1) {
						$tna_task_update_data[$row[csf("id")]][50]['reqqnty'] = array_sum($yarndata[$row[csf("id")]]);
						$to_process_task[$row[csf("id")]][50] = 50;
						$tna_task_update_data[$row[csf("id")]][48]['reqqnty'] = array_sum($yarndata[$row[csf("id")]]);
						$to_process_task[$row[csf("id")]][48] = 48;
						$tna_task_update_data[$row[csf("id")]][45]['reqqnty'] = array_sum($yarndata[$row[csf("id")]]);
						$to_process_task[$row[csf("id")]][45] = 45;
						$tna_task_update_data[$row[csf("id")]][46]['reqqnty'] = array_sum($yarndata[$row[csf("id")]]);
						$to_process_task[$row[csf("id")]][46] = 46;
						$tna_task_update_data[$row[csf("id")]][47]['reqqnty'] = array_sum($yarndata[$row[csf("id")]]);
						$to_process_task[$row[csf("id")]][47] = 47;
					} else {
						//YD......................
						foreach (array(2, 3, 4, 6, 32, 33) as $color_type_id) {
							$tna_task_update_data[$row[csf("id")]][345]['reqqnty'] += $yarndata[$row[csf("id")]][$color_type_id];
						}
						if ($tna_task_update_data[$row[csf("id")]][345]['reqqnty'] > 0) {
							$to_process_task[$row[csf("id")]][345] = 345;
						}

						//AOP......................
						foreach (array(5, 7, 63) as $color_type_id) {
							$tna_task_update_data[$row[csf("id")]][344]['reqqnty'] += $yarndata[$row[csf("id")]][$color_type_id];
						}
						if ($tna_task_update_data[$row[csf("id")]][344]['reqqnty'] > 0) {
							$to_process_task[$row[csf("id")]][344] = 344;
						}

						//Solid......................
						foreach (array(1, 20, 25, 26, 27, 28, 29, 30, 31, 34, 35, 35, 37) as $color_type_id) {
							$tna_task_update_data[$row[csf("id")]][50]['reqqnty'] += $yarndata[$row[csf("id")]][$color_type_id];
						}
						if ($tna_task_update_data[$row[csf("id")]][50]['reqqnty'] > 0) {
							$to_process_task[$row[csf("id")]][50] = 50;
						}

						$tna_task_update_data[$row[csf("id")]][45]['reqqnty'] = array_sum($yarndata[$row[csf("id")]]);
						$to_process_task[$row[csf("id")]][45] = 45;
						$tna_task_update_data[$row[csf("id")]][46]['reqqnty'] = array_sum($yarndata[$row[csf("id")]]);
						$to_process_task[$row[csf("id")]][46] = 46;

						$tna_task_update_data[$row[csf("id")]][48]['reqqnty'] = array_sum($yarndata[$row[csf("id")]]);
						$to_process_task[$row[csf("id")]][48] = 48;


						$tna_task_update_data[$row[csf("id")]][47]['reqqnty'] = array_sum($yarndata[$row[csf("id")]]);
						$to_process_task[$row[csf("id")]][47] = 47;
					}
				}



				if ($wash_data[$row[csf("id")]] > 0) {
					$tna_task_update_data[$row[csf("id")]][89]['reqqnty'] = $wash_data[$row[csf("id")]];
					$to_process_task[$row[csf("id")]][89] = 89;
					$tna_task_update_data[$row[csf("id")]][90]['reqqnty'] = $wash_data[$row[csf("id")]];
					$to_process_task[$row[csf("id")]][90] = 90;
				}

				if ($emblishment_data[$row[csf("id")]] > 0) {
					$tna_task_update_data[$row[csf("id")]][85]['reqqnty'] = $emblishment_data[$row[csf("id")]];
					$to_process_task[$row[csf("id")]][85] = 85;
				}

				//if (!in_array( $row[csf("job_no_mst")],$job_no_array)) $job_no_array[]= $row[csf("job_no_mst")] ;
				$job_no_array[$row[csf("job_no_mst")]] = $row[csf("job_no_mst")];

				//$order_id_array[$i]=$row[csf("id")];
				$order_id_array[$row['ID']] = $row[csf("id")];
				$po_order_template[$row[csf("id")]] =  $template_id;
				$po_order_details[$row[csf("id")]]['po_received_date'] = $row[csf("po_received_date")];
				$po_order_details[$row[csf("id")]]['shipment_date'] = $row[csf("shipment_date")];
				$po_order_details[$row[csf("id")]]['job_no_mst'] = $row[csf("job_no_mst")];
				$po_order_details[$row[csf("id")]]['po_quantity'] = $row[csf("po_quantity")];
				$po_order_details[$row[csf("id")]]['po_quantity_pcs'] = $row[csf("po_quantity_pcs")];
				$po_order_details[$row[csf("id")]]['template_id'] = $template_id;
				$po_order_details[$row[csf("id")]]['po_id'] = $row[csf("id")];
				$po_order_details[$row[csf("id")]]['is_confirmed'] = $row[csf("is_confirmed")];
				$po_order_details[$row[csf("id")]]['tna_task_from_upto'] = $row[csf("tna_task_from_upto")];
				//$to_process_task
				$tna_task_update_data[$row[csf("id")]][80]['max_start_date'] = $row[csf("pp_meeting_date")];
				$tna_task_update_data[$row[csf("id")]][80]['min_start_date'] = $row[csf("pp_meeting_date")];
				//184=PP Meeting AOP;
				$tna_task_update_data[$row[csf("id")]][184]['max_start_date'] = $row[csf("pp_meeting_date")];
				$tna_task_update_data[$row[csf("id")]][184]['min_start_date'] = $row[csf("pp_meeting_date")];
				//185=PP Meeting YD;
				$tna_task_update_data[$row[csf("id")]][185]['max_start_date'] = $row[csf("pp_meeting_date")];
				$tna_task_update_data[$row[csf("id")]][185]['min_start_date'] = $row[csf("pp_meeting_date")];


				//$tna_task_update_data[$row[csf("id")]][80]['noofval']=1;	
				$tna_task_update_data[$row[csf("id")]][80]['doneqnty'] = 1;
				$tna_task_update_data[$row[csf("id")]][80]['reqqnty'] = 1;
				$tna_task_update_data[$row[csf("id")]][1]['doneqnty'] = 1;
				$tna_task_update_data[$row[csf("id")]][1]['reqqnty'] = 1;
				$tna_task_update_data[$row[csf("id")]][1]['max_start_date'] = $row[csf("po_received_date")];
				$tna_task_update_data[$row[csf("id")]][1]['min_start_date'] = $row[csf("po_received_date")];
				$to_process_task[$row[csf("id")]][1] = 1;
				$job_nature[$row[csf('job_no')]] = $row[csf('garments_nature')];



				foreach ($tna_common_task_name_to_process as $vid => $vtask) {

					/*					
					if( $row[csf('is_confirmed')]==1 ) //Confirmed
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
					*/
					$to_process_task[$row[csf("id")]][$vid] = $vid;
				}
				$i++;
			}
		}

		//print_r($to_process_task);die;

		//delete & insert temp po-----------------------
		// $con = connect();
		// execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ENTRY_FORM=$entry_form",1);
		// oci_commit($con);
		// disconnect($con);
		// fnc_tempengine("GBL_TEMP_ENGINE", $user_id, $entry_form, 1, $order_id_array, $empty_arr);
		//---------------------------------end;

		//die;

		$po_ids = implode(",", $order_id_array);
		$job_no_list = "'" . implode("','", $job_no_array) . "'";

		if ($po_ids == '') {
			//echo "0**".$rID."**".implode(", ",$template_missing_po);
		}


		unset($data_array);
		//unset($order_id_array); 
		//unset($job_no_array);

		//****************************************Actual TNA Process Start*****************************************

		//Accessories PI Create......................................................
		if ($tna_task_details[315]['task_name']) {
			$pi_sql = " select  b.ORDER_ID,MAX(a.PI_DATE) AS MAX_DATE,MIN(a.PI_DATE) AS MIN_DATE, SUM(b.QUANTITY) AS QTY  from com_pi_master_details A,com_pi_item_details B where a.id=b.pi_id and a.ITEM_CATEGORY_ID = 4 and a.IMPORTER_ID = $cbo_company and A.status_active=1 and A.is_deleted=0 and b.status_active=1 and b.is_deleted=0 " . where_con_using_array($order_id_array, 0, 'b.ORDER_ID') . " GROUP BY b.ORDER_ID";
			// echo $pi_sql;die;
			$pi_sql_res = sql_select($pi_sql);
			foreach ($pi_sql_res as $row) {
				$tna_task_update_data[$row['ORDER_ID']][315]['max_start_date'] = $row['MAX_DATE'];
				$tna_task_update_data[$row['ORDER_ID']][315]['min_start_date'] = $row['MIN_DATE'];
				$tna_task_update_data[$row['ORDER_ID']][315]['doneqnty'] += $row['QTY'];

				$tna_task_update_data[$row['ORDER_ID']][315]['reqqnty'] += $row['QTY']; //req & done qty same
				$to_process_task[$row['ORDER_ID']][315] = 315;
			}
		}



		//fabric requrae..........................................start;

		$job_no_list_arr = array_chunk(array_unique(explode(",", $job_no_list)), 990);
		$costing_per_sql = "select job_no,costing_per from wo_pre_cost_mst a where 1=1 ";
		$p = 1;
		foreach ($job_no_list_arr as $job_no_process) {
			if ($p == 1) $costing_per_sql .= "and (a.job_no in(" . implode(',', $job_no_process) . ")";
			else  $costing_per_sql .= " or a.job_no in(" . implode(',', $job_no_process) . ")";
			$p++;
		}
		$costing_per_sql .= ")";
		$costing_per_arr = return_library_array($costing_per_sql, "job_no", "costing_per");




		$gmtsitem_ratio_array = array();
		$gmtsitem_ratio_sql = sql_select("select a.job_no,a.gmts_item_id,a.set_item_ratio from wo_po_details_mas_set_details a where  1=1 $job_cond_in"); // where job_no ='FAL-14-01157'
		foreach ($gmtsitem_ratio_sql as $gmtsitem_ratio_sql_row) {
			$gmtsitem_ratio_array[$gmtsitem_ratio_sql_row[csf('job_no')]][$gmtsitem_ratio_sql_row[csf('gmts_item_id')]] = $gmtsitem_ratio_sql_row[csf('set_item_ratio')];
		}



		$sql = "select a.BODY_PART_ID,a.FABRIC_SOURCE,a.job_no,a.COLOR_TYPE_ID,b.PO_BREAK_DOWN_ID,a.id as pre_cost_fabric_cost_dtls_id,a.item_number_id,a.fab_nature_id,b.color_size_table_id, b.color_number_id,b.gmts_sizes as size_number_id, b.cons, b.requirment,a.rate FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b WHERE a.job_no=b.job_no and  a.id=b.pre_cost_fabric_cost_dtls_id " . where_con_using_array($order_id_array, 0, 'b.po_break_down_id') . " and a.status_active=1 and a.is_deleted=0 order by a.id ";


		$cons_arr = array();
		$precost_fabric_source_by_po_arr = array();
		$sql_pre_cost_cons = sql_select($sql);
		foreach ($sql_pre_cost_cons as $cons_row) {
			$cons_arr[$cons_row[csf('job_no')]][$cons_row[csf('po_break_down_id')]][$cons_row[csf('pre_cost_fabric_cost_dtls_id')]][$cons_row[csf('item_number_id')]][$cons_row[csf('color_number_id')]][$cons_row[csf('size_number_id')]][$cons_row[csf('color_type_id')]]['cons'] = $cons_row[csf('cons')];

			$cons_arr[$cons_row[csf('job_no')]][$cons_row[csf('po_break_down_id')]][$cons_row[csf('pre_cost_fabric_cost_dtls_id')]][$cons_row[csf('item_number_id')]][$cons_row[csf('color_number_id')]][$cons_row[csf('size_number_id')]][$cons_row[csf('color_type_id')]]['requirment'] = $cons_row[csf('requirment')];

			if ($cons_row['FABRIC_SOURCE'] == 1 || empty($precost_fabric_source_by_po_arr[$cons_row[csf('po_break_down_id')]])) {
				$precost_fabric_source_by_po_arr[$cons_row[csf('po_break_down_id')]] = $cons_row['FABRIC_SOURCE'];
			}

			$color_type_in_precost[$cons_row['PO_BREAK_DOWN_ID']][$cons_row['BODY_PART_ID']][$cons_row['COLOR_TYPE_ID']] = $cons_row['COLOR_TYPE_ID'];
		}





		$sql = "select a.job_no,b.id,c.item_number_id,c.country_id,c.color_number_id,c.size_number_id,c.order_quantity ,c.plan_cut_qnty ,d.id as pre_cost_dtls_id,d.fab_nature_id,d.fabric_source,d.color_type_id,d.SOURCE_ID  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no  and b.id=c.po_break_down_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 " . where_con_using_array($order_id_array, 0, 'b.id') . "  order by b.id,pre_cost_dtls_id";
		//echo $sql;die;

		$data_arr = sql_select($sql);
		foreach ($data_arr as $row) {
			$costing_per_qty = 0;
			$costing_per = $costing_per_arr[$row[csf('job_no')]];
			if ($costing_per == 1) {
				$costing_per_qty = 12;
			} else if ($costing_per == 2) {
				$costing_per_qty = 1;
			} else if ($costing_per == 3) {
				$costing_per_qty = 24;
			} else if ($costing_per == 4) {
				$costing_per_qty = 36;
			} else if ($costing_per == 5) {
				$costing_per_qty = 48;
			}

			$set_item_ratio = $gmtsitem_ratio_array[$row[csf('job_no')]][$row[csf('item_number_id')]];

			$requirment = $cons_arr[$row[csf('job_no')]][$row[csf('id')]][$row[csf('pre_cost_dtls_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('color_type_id')]]['requirment'];


			$cons = $cons_arr[$row[csf('job_no')]][$row[csf('id')]][$row[csf('pre_cost_dtls_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]][$row[csf('color_type_id')]]['cons'];


			$req_fin_fab_qty_arr[$row[csf('id')]][$row['SOURCE_ID']][$row[csf('color_type_id')]] += (($row[csf("plan_cut_qnty")] / ($costing_per_qty * $set_item_ratio)) * $cons);
			$req_grey_fab_qty[$row[csf('id')]][$row['SOURCE_ID']][$row[csf('color_type_id')]] += (($row[csf("plan_cut_qnty")] / ($costing_per_qty * $set_item_ratio)) * $requirment);
		}


		//print_r($req_fin_fab_qty_arr[77213]);die;



		//Trims Booking-------------------
		$trimsBookingSql = "SELECT a.SOURCE,c.TRIM_TYPE,b.PO_BREAK_DOWN_ID,max(a.BOOKING_DATE) as MAX_BOOKING_DATE,min(a.BOOKING_DATE) as MIN_BOOKING_DATE,sum(b.WO_QNTY) as WO_QNTY  FROM  WO_BOOKING_MST a,WO_BOOKING_DTLS b,LIB_ITEM_GROUP c WHERE b.TRIM_GROUP=c.id and a.BOOKING_NO=b.BOOKING_NO and b.is_deleted = 0 AND b.status_active = 1 and b.BOOKING_TYPE in(2,8) and c.TRIM_TYPE in(1,2) and c.ITEM_CATEGORY=4 " . where_con_using_array($order_id_array, 0, 'b.PO_BREAK_DOWN_ID') . "  group by b.PO_BREAK_DOWN_ID,a.SOURCE,c.TRIM_TYPE";
		$trimsBookingSqlResult = sql_select($trimsBookingSql);


		foreach ($trimsBookingSqlResult as $row) {
			if ($row['TRIM_TYPE'] == 1) {
				if ($row['SOURCE'] == 1) { //Forain
					$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][279]['max_start_date'] = $row['MAX_BOOKING_DATE'];
					$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][279]['min_start_date'] = $row['MIN_BOOKING_DATE'];
					$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][279]['doneqnty'] += $row['WO_QNTY'];
				} else { //local
					$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][308]['max_start_date'] = $row['MAX_BOOKING_DATE'];
					$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][308]['min_start_date'] = $row['MIN_BOOKING_DATE'];
					$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][308]['doneqnty'] += $row['WO_QNTY'];
				}

				//Common.......................
				//270 => " Sewing Trims Booking To Be Issued", 
				$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][270]['max_start_date'] = $row['MAX_BOOKING_DATE'];
				$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][270]['min_start_date'] = $row['MIN_BOOKING_DATE'];
				$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][270]['doneqnty'] += $row['WO_QNTY'];
			} else {
				//134=>'Packing Accessories Booking'
				$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][134]['max_start_date'] = $row['MAX_BOOKING_DATE'];
				$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][134]['min_start_date'] = $row['MIN_BOOKING_DATE'];
				$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][134]['doneqnty'] += $row['WO_QNTY'];
			}
		}
		//echo $trimsCostSql;die;




		//fabric requrae..........................................end;		
		// if($db_type==0)
		// {
		// 	$sql="select a.color_type_id,b.po_break_down_id,sum(b.requirment) as requirment from wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b where a.id=b.pre_cost_fabric_cost_dtls_id and a.company_id=$cbo_company and b.po_break_down_id in ($po_ids) and a.status_active =1 and a.is_deleted=0 group by a.color_type_id,b.po_break_down_id";
		// }
		// else
		// {
		// 	$po_id_list_arr=array_chunk($order_id_array,999);

		// 	$sql = "select a.color_type_id,b.po_break_down_id,sum(b.requirment) as requirment from wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b WHERE  a.id=b.pre_cost_fabric_cost_dtls_id and a.company_id=$cbo_company ";
		// 	$p=1;
		// 	foreach($po_id_list_arr as $po_id_process)
		// 	{
		// 		if($p==1) $sql .="  and ( b.po_break_down_id in(".implode(',',$po_id_process).")"; else  $sql .=" or b.po_break_down_id in(".implode(',',$po_id_process).")";

		// 		$p++;
		// 	}
		// 	$sql .=")  and a.status_active =1 and a.is_deleted=0 group by a.color_type_id,b.po_break_down_id";
		// }

		$sql = "select a.color_type_id,b.po_break_down_id,sum((b.requirment/b.PCS)*c.PLAN_CUT_QNTY) as requirment from wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b,WO_PO_COLOR_SIZE_BREAKDOWN c WHERE c.id=b.COLOR_SIZE_TABLE_ID and a.id=b.pre_cost_fabric_cost_dtls_id and a.company_id=$cbo_company " . where_con_using_array($order_id_array, 0, 'b.po_break_down_id') . "  and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and c.status_active =1 and c.is_deleted=0 group by a.color_type_id,b.po_break_down_id";

		$SolidPoArr = array();
		$YDPoArr = array();
		$AOPPoArr = array();

		// echo $sql;die;

		// echo $gross_level;die;
		$result = sql_select($sql);
		foreach ($result as $row) {
			if ($row[csf("requirment")] > 0) {

				if ($gross_level == 1) {  //solid..
					//269=> "Sample Fabric Booking To Be Issued Woven",
					$tna_task_update_data[$row[csf("po_break_down_id")]][269]['reqqnty'] += $row[csf("requirment")];
					$to_process_task[$row[csf("po_break_down_id")]][269] = 269;

					//150	=> "PPS Fabric Issue",
					$tna_task_update_data[$row[csf("po_break_down_id")]][150]['reqqnty'] += $row[csf("requirment")];
					$to_process_task[$row[csf("po_break_down_id")]][150] = 150;

					//37  => "PPS Making",
					$tna_task_update_data[$row[csf("po_break_down_id")]][37]['reqqnty'] += $row[csf("requirment")];
					$to_process_task[$row[csf("po_break_down_id")]][37] = 37;
					//60 => "Gray Fabric Production To Be Done",	
					$tna_task_update_data[$row[csf("po_break_down_id")]][60]['reqqnty'] += $row[csf("requirment")];
					$to_process_task[$row[csf("po_break_down_id")]][60] = 60;
					//73 => "Finished fabric to be in-house",
					//$tna_task_update_data[$row[csf("po_break_down_id")]][73]['reqqnty']+=$row[csf("requirment")];



					foreach ($req_fin_fab_qty_arr[$row[csf('po_break_down_id')]] as $source_id => $dataRows) {
						if ($source_id == 1) {
							$tna_task_update_data[$row[csf("po_break_down_id")]][277]['reqqnty'] += round(array_sum($req_fin_fab_qty_arr[$row[csf('po_break_down_id')]][$source_id]));
							$to_process_task[$row[csf("po_break_down_id")]][277] = 277;

							$tna_task_update_data[$row[csf("po_break_down_id")]][352]['reqqnty'] += round(array_sum($req_fin_fab_qty_arr[$row[csf('po_break_down_id')]][$source_id]));
							$to_process_task[$row[csf("po_break_down_id")]][352] = 352;
						} else {
							$tna_task_update_data[$row[csf("po_break_down_id")]][73]['reqqnty'] += round(array_sum($req_fin_fab_qty_arr[$row[csf('po_break_down_id')]][$source_id]));
							$to_process_task[$row[csf("po_break_down_id")]][73] = 73;

							$tna_task_update_data[$row[csf("po_break_down_id")]][306]['reqqnty'] += round(array_sum($req_fin_fab_qty_arr[$row[csf('po_break_down_id')]][$source_id]));
							$to_process_task[$row[csf("po_break_down_id")]][306] = 306;

							$tna_task_update_data[$row[csf("po_break_down_id")]][352]['reqqnty'] += round(array_sum($req_fin_fab_qty_arr[$row[csf('po_break_down_id')]][$source_id]));
							$to_process_task[$row[csf("po_break_down_id")]][352] = 352;
						}
					}




					//131 => "Production File Handover",
					$tna_task_update_data[$row[csf("po_break_down_id")]][131]['reqqnty'] += $row[csf("requirment")];
					$to_process_task[$row[csf("po_break_down_id")]][131] = 131;
					//130 => "Trim Card Handover",
					$tna_task_update_data[$row[csf("po_break_down_id")]][130]['reqqnty'] += $row[csf("requirment")];
					$to_process_task[$row[csf("po_break_down_id")]][130] = 130;
					//80	=> "PP Meeting To Be Conducted",
					//$tna_task_update_data[$row[csf("po_break_down_id")]][80]['reqqnty']+=$row[csf("requirment")];
					$to_process_task[$row[csf("po_break_down_id")]][80] = 80;
					$tna_task_update_data[$row[csf("po_break_down_id")]][80]['doneqnty'] = 1;
					$tna_task_update_data[$row[csf("po_break_down_id")]][80]['reqqnty'] = 1;

					//84=> "Cutting To Be Done",
					$tna_task_update_data[$row[csf("po_break_down_id")]][84]['reqqnty'] += $row[csf("requirment")];
					$to_process_task[$row[csf("po_break_down_id")]][84] = 84;
					//86	=> "Sewing To Be Done",	
					$tna_task_update_data[$row[csf("po_break_down_id")]][86]['reqqnty'] += $row[csf("requirment")];
					$to_process_task[$row[csf("po_break_down_id")]][86] = 86;

					//51	=> "Yarn Send for Dyeing",	
					$tna_task_update_data[$row[csf("po_break_down_id")]][51]['reqqnty'] += $row[csf("requirment")];
					$to_process_task[$row[csf("po_break_down_id")]][51] = 51;

					//165	=> "Yarndip Requisition",
					$tna_task_update_data[$row[csf("po_break_down_id")]][165]['reqqnty'] += $row[csf("requirment")];
					$to_process_task[$row[csf("po_break_down_id")]][165] = 165;
					//166	=> "Yarndip Submit To Buyer",
					$tna_task_update_data[$row[csf("po_break_down_id")]][166]['reqqnty'] += $row[csf("requirment")];
					$to_process_task[$row[csf("po_break_down_id")]][166] = 166;
					//167	=> "Yarndip Approval",
					$tna_task_update_data[$row[csf("po_break_down_id")]][167]['reqqnty'] += $row[csf("requirment")];
					$to_process_task[$row[csf("po_break_down_id")]][167] = 167;

					//203	=> "Grey Fab Issue",
					$tna_task_update_data[$row[csf("po_break_down_id")]][203]['reqqnty'] += $row[csf("requirment")];
					$to_process_task[$row[csf("po_break_down_id")]][203] = 203;

					//212=> "Knitting production Solid",
					$tna_task_update_data[$row[csf("po_break_down_id")]][212]['reqqnty'] += $row[csf("requirment")];
					$to_process_task[$row[csf("po_break_down_id")]][212] = 212;


					//256=> "YD Knit down Submission",
					$tna_task_update_data[$row[csf("po_break_down_id")]][256]['reqqnty'] += $row[csf("requirment")];
					$to_process_task[$row[csf("po_break_down_id")]][256] = 256;

					//257=> "YD Knit down Approval", 
					$tna_task_update_data[$row[csf("po_break_down_id")]][257]['reqqnty'] += $row[csf("requirment")];
					$to_process_task[$row[csf("po_break_down_id")]][257] = 257;

					$SolidPoArr[$row[csf("po_break_down_id")]] = $row[csf("po_break_down_id")];
				} else {
					//2,3,4,6,32,33 Y/D
					if (($row[csf("color_type_id")] == 2) || ($row[csf("color_type_id")] == 3) || ($row[csf("color_type_id")] == 4) || ($row[csf("color_type_id")] == 6) || ($row[csf("color_type_id")] == 32) || ($row[csf("color_type_id")] == 33)) {

						//51	=> "Yarn Send for Dyeing",	
						$tna_task_update_data[$row[csf("po_break_down_id")]][51]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][51] = 51;
						//169	=> "PPS Fabrics Issue (YD)",
						$tna_task_update_data[$row[csf("po_break_down_id")]][169]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][169] = 169;
						//171	=> "PPS Making (YD)",
						$tna_task_update_data[$row[csf("po_break_down_id")]][171]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][171] = 171;
						//174	=> "PPS Submit (YD)",
						$tna_task_update_data[$row[csf("po_break_down_id")]][174]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][174] = 174;
						//175	=> "PPS Approval (YD)",
						$tna_task_update_data[$row[csf("po_break_down_id")]][175]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][175] = 175;
						//177	=> "Trim Card Handover (YD)",
						$tna_task_update_data[$row[csf("po_break_down_id")]][177]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][177] = 177;
						//178	=> "Knitting production (YD)",
						$tna_task_update_data[$row[csf("po_break_down_id")]][178]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][178] = 178;
						//180	=> "Finish Fabrics Inhouse (YD)",
						$tna_task_update_data[$row[csf("po_break_down_id")]][180]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][180] = 180;
						//181	=> "Production File Handover(YD)",
						$tna_task_update_data[$row[csf("po_break_down_id")]][181]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][181] = 181;
						//185	=> "PP Meeting (YD)",
						$tna_task_update_data[$row[csf("po_break_down_id")]][185]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][185] = 185;
						//187	=> "Cutting Production (YD)",
						$tna_task_update_data[$row[csf("po_break_down_id")]][187]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][187] = 187;
						//191	=> "Sewing Production(YD)"
						$tna_task_update_data[$row[csf("po_break_down_id")]][191]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][191] = 191;
						//165	=> "Yarndip Requisition",
						$tna_task_update_data[$row[csf("po_break_down_id")]][165]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][165] = 165;
						//166	=> "Yarndip Submit To Buyer",
						$tna_task_update_data[$row[csf("po_break_down_id")]][166]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][166] = 166;
						//167	=> "Yarndip Approval",
						$tna_task_update_data[$row[csf("po_break_down_id")]][167]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][167] = 167;


						//256=> "YD Knit down Submission",
						$tna_task_update_data[$row[csf("po_break_down_id")]][256]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][256] = 256;
						//257=> "YD Knit down Approval", 
						$tna_task_update_data[$row[csf("po_break_down_id")]][257]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][257] = 257;

						//258=> "Bulk Hanger submission",
						$to_process_task[$row[csf("po_break_down_id")]][258] = 258;
						//259=> "Bulk Hanger Approval",	
						$to_process_task[$row[csf("po_break_down_id")]][259] = 259;


						$YDPoArr[$row[csf("po_break_down_id")]] = $row[csf("po_break_down_id")];
					}
					//5,7,63 AOP
					else if (($row[csf("color_type_id")] == 5) || ($row[csf("color_type_id")] == 7) || ($row[csf("color_type_id")] == 63)) {
						//168	=> "PPS Fabrics Issue (AOP)"
						$tna_task_update_data[$row[csf("po_break_down_id")]][168]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][168] = 168;
						//170	=> "PPS Making (AOP)",
						$tna_task_update_data[$row[csf("po_break_down_id")]][170]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][170] = 170;
						//172	=> "PPS Submit (AOP)",
						$tna_task_update_data[$row[csf("po_break_down_id")]][172]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][172] = 172;
						//173	=> "PPS Approval (AOP)",
						$tna_task_update_data[$row[csf("po_break_down_id")]][173]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][173] = 173;
						//176	=> "Trim Card Handover (AOP)",
						$tna_task_update_data[$row[csf("po_break_down_id")]][176]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][176] = 176;
						//179	=> "Finish Fabrics Inhouse (AOP)",
						$tna_task_update_data[$row[csf("po_break_down_id")]][179]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][179] = 179;
						//184	=> "PP Meeting (AOP)",
						$tna_task_update_data[$row[csf("po_break_down_id")]][184]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][184] = 184;
						//186	=> "Cutting Production (AOP)",
						$tna_task_update_data[$row[csf("po_break_down_id")]][186]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][186] = 186;
						//190	=> "Sewing Production(AOP)",
						$tna_task_update_data[$row[csf("po_break_down_id")]][190]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][190] = 190;

						//60 => "Gray Fabric Production To Be Done",	
						$tna_task_update_data[$row[csf("po_break_down_id")]][60]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][60] = 60;
						//62 => "Fabric Send for AOP",	
						$tna_task_update_data[$row[csf("po_break_down_id")]][62]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][62] = 62;


						//254=> "AOP Strike Off Submission",
						$tna_task_update_data[$row[csf("po_break_down_id")]][254]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][254] = 254;
						//255=> "AOP Strike Off Approval",
						$tna_task_update_data[$row[csf("po_break_down_id")]][255]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][255] = 255;

						//258=> "Bulk Hanger submission",
						$to_process_task[$row[csf("po_break_down_id")]][258] = 258;
						//259=> "Bulk Hanger Approval",	
						$to_process_task[$row[csf("po_break_down_id")]][259] = 259;

						//254=> "AOP Strike Off Submission",
						$tna_task_update_data[$row[csf("po_break_down_id")]][254]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][254] = 254;
						//255=> "AOP Strike Off Approval",
						$tna_task_update_data[$row[csf("po_break_down_id")]][255]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][255] = 255;

						//323=> "Knitting production AOP",
						$tna_task_update_data[$row[csf("po_break_down_id")]][323]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][323] = 323;

						$AOPPoArr[$row[csf("po_break_down_id")]] = $row[csf("po_break_down_id")];
					} //solid..
					else if (($row[csf("color_type_id")] == 1) || ($row[csf("color_type_id")] == 20) || ($row[csf("color_type_id")] == 25) || ($row[csf("color_type_id")] == 26) || ($row[csf("color_type_id")] == 27) || ($row[csf("color_type_id")] == 28) || ($row[csf("color_type_id")] == 29) || ($row[csf("color_type_id")] == 30) || ($row[csf("color_type_id")] == 31) || ($row[csf("color_type_id")] == 34)  || ($row[csf("color_type_id")] == 35)  || ($row[csf("color_type_id")] == 36)  || ($row[csf("color_type_id")] == 37) || ($row[csf("color_type_id")] == '')) {
						//150	=> "PPS Fabric Issue",
						$tna_task_update_data[$row[csf("po_break_down_id")]][150]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][150] = 150;
						//37  => "PPS Making",
						$tna_task_update_data[$row[csf("po_break_down_id")]][37]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][37] = 37;
						//60 => "Gray Fabric Production To Be Done",	
						$tna_task_update_data[$row[csf("po_break_down_id")]][60]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][60] = 60;
						//73 => "Finished fabric to be in-house",
						$tna_task_update_data[$row[csf("po_break_down_id")]][73]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][73] = 73;

						//352 => " Woven Finished fabric to be in-house",
						$tna_task_update_data[$row[csf("po_break_down_id")]][352]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][352] = 352;

						//131 => "Production File Handover",
						$tna_task_update_data[$row[csf("po_break_down_id")]][131]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][131] = 131;
						//130 => "Trim Card Handover",
						$tna_task_update_data[$row[csf("po_break_down_id")]][130]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][130] = 130;
						//80 => "PP Meeting To Be Conducted",
						//$tna_task_update_data[$row[csf("po_break_down_id")]][80]['reqqnty']+=$row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][80] = 80;
						$tna_task_update_data[$row[csf("po_break_down_id")]][80]['doneqnty'] = 1;
						$tna_task_update_data[$row[csf("po_break_down_id")]][80]['reqqnty'] = 1;

						//84 => "Cutting To Be Done",
						$tna_task_update_data[$row[csf("po_break_down_id")]][84]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][84] = 84;
						//86 => "Sewing To Be Done",	
						$tna_task_update_data[$row[csf("po_break_down_id")]][86]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][86] = 86;

						//212=> "Knitting production Solid",
						$tna_task_update_data[$row[csf("po_break_down_id")]][212]['reqqnty'] += $row[csf("requirment")];
						$to_process_task[$row[csf("po_break_down_id")]][212] = 212;

						//258=> "Bulk Hanger submission",
						$to_process_task[$row[csf("po_break_down_id")]][258] = 258;
						//259=> "Bulk Hanger Approval",	
						$to_process_task[$row[csf("po_break_down_id")]][259] = 259;


						$SolidPoArr[$row[csf("po_break_down_id")]] = $row[csf("po_break_down_id")];
					}


					foreach ($req_fin_fab_qty_arr[$row[csf('po_break_down_id')]] as $source_id => $dataRows) {
						if ($source_id == 1) { //forain
							if (round(array_sum($req_fin_fab_qty_arr[$row[csf('po_break_down_id')]][$source_id]))) {
								$tna_task_update_data[$row[csf("po_break_down_id")]][277]['reqqnty'] += round(array_sum($req_fin_fab_qty_arr[$row[csf('po_break_down_id')]][$source_id]));
								$to_process_task[$row[csf("po_break_down_id")]][277] = 277;
							}
						} else {
							if (round(array_sum($req_fin_fab_qty_arr[$row[csf('po_break_down_id')]][$source_id]))) {
								$tna_task_update_data[$row[csf("po_break_down_id")]][306]['reqqnty'] += round(array_sum($req_fin_fab_qty_arr[$row[csf('po_break_down_id')]][$source_id]));
								$to_process_task[$row[csf("po_break_down_id")]][306] = 306;
							}
						}
					}
				}
			}
		}



		//Sample Part..............
		$sql = "select PO_BREAK_DOWN_ID,count(sample_type_id) as tot_sample_type_id from wo_po_sample_approval_info WHERE sample_type_id in(18,45) and status_active =1 and is_deleted=0  " . where_con_using_array($order_id_array, 0, 'po_break_down_id') . " group by po_break_down_id";

		//echo $sql;die;

		$result = sql_select($sql);
		foreach ($result as $row) {

			if ($gross_level == 1) { //solid..
				$to_process_task[$row['PO_BREAK_DOWN_ID']][159] = 159;
			} else {
				if ($SolidPoArr[$row['PO_BREAK_DOWN_ID']] != '' && $row['PO_BREAK_DOWN_ID'] != '') {
					//159 => "Size Set Sample Making",
					$to_process_task[$row['PO_BREAK_DOWN_ID']][159] = 159;
				}
				if ($AOPPoArr[$row['PO_BREAK_DOWN_ID']] != '' && $row['PO_BREAK_DOWN_ID'] != '') {
					//182	=> "Size Set Making (AOP)",
					$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][182]['reqqnty'] += $row[csf("tot_sample_type_id")];
					$to_process_task[$row['PO_BREAK_DOWN_ID']][182] = 182;
				}
				if ($YDPoArr[$row['PO_BREAK_DOWN_ID']] != '' && $row['PO_BREAK_DOWN_ID'] != '') {
					//183	=> "Size Set Making (YD)",
					$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][183]['reqqnty'] += $row[csf("tot_sample_type_id")];
					$to_process_task[$row['PO_BREAK_DOWN_ID']][183] = 183;
				}
			}
		}

		//Emb Part budget check......................................................

		$sql = "select a.EMB_NAME,b.PO_BREAK_DOWN_ID,sum(b.amount) as AMOUNT from wo_pre_cost_embe_cost_dtls a,wo_pre_cos_emb_co_avg_con_dtls b WHERE a.id=b.PRE_COST_EMB_COST_DTLS_ID  and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0  " . where_con_using_array($order_id_array, 0, 'po_break_down_id') . " group by a.EMB_NAME,b.PO_BREAK_DOWN_ID";


		//echo $sql;die;

		$result = sql_select($sql);
		foreach ($result as $row) {

			if ($gross_level == 1) { //solid..
				$tna_task_update_data[$row[csf("po_break_down_id")]][85]['reqqnty'] += $row[csf("amount")];
				$to_process_task[$row[csf("po_break_down_id")]][85] = 85;

				if ($row['EMB_NAME'] == 1 && $row['AMOUNT'] > 0) {
					////Print Receive reqqnty
					$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][267]['reqqnty'] += $row['AMOUNT'];
					$to_process_task[$row['PO_BREAK_DOWN_ID']][267] = 267;
					//Print issue reqqnty
					$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][312]['reqqnty'] += $row['AMOUNT'];
					$to_process_task[$row['PO_BREAK_DOWN_ID']][312] = 312;
				} else if ($row['EMB_NAME'] == 2 && $row['AMOUNT'] > 0) { //Embroidery Receive reqqnty
					$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][268]['reqqnty'] += $row['AMOUNT'];
					$to_process_task[$row['PO_BREAK_DOWN_ID']][268] = 268;
					//Embroidery Issue reqqnty
					$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][313]['reqqnty'] += $row['AMOUNT'];
					$to_process_task[$row['PO_BREAK_DOWN_ID']][313] = 313;
				}
			} else {
				if ($SolidPoArr[$row[csf("po_break_down_id")]] != '' && $row[csf("po_break_down_id")] != '') {
					//85 => "Print/Emb To Be Done",
					$tna_task_update_data[$row[csf("po_break_down_id")]][85]['reqqnty'] += $row[csf("amount")];
					$to_process_task[$row[csf("po_break_down_id")]][85] = 85;

					if ($row['EMB_NAME'] == 1 && $row['AMOUNT'] > 0) { //Print
						$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][267]['reqqnty'] += $row['AMOUNT'];
						$to_process_task[$row['PO_BREAK_DOWN_ID']][267] = 267;
						//Print issue reqqnty
						$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][312]['reqqnty'] += $row['AMOUNT'];
						$to_process_task[$row['PO_BREAK_DOWN_ID']][312] = 312;
					} else if ($row['EMB_NAME'] == 2 && $row['AMOUNT'] > 0) { //Embroidery
						$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][268]['reqqnty'] += $row['AMOUNT'];
						$to_process_task[$row['PO_BREAK_DOWN_ID']][268] = 268;
						//Embroidery Issue reqqnty
						$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][313]['reqqnty'] += $row['AMOUNT'];
						$to_process_task[$row['PO_BREAK_DOWN_ID']][313] = 313;
					}
				}
				if ($AOPPoArr[$row[csf("po_break_down_id")]] != '' && $row[csf("po_break_down_id")] != '') {
					//188	=> "Embellishment (AOP)",
					$tna_task_update_data[$row[csf("po_break_down_id")]][188]['reqqnty'] += $row[csf("amount")];
					$to_process_task[$row[csf("po_break_down_id")]][188] = 188;
				}
				if ($YDPoArr[$row[csf("po_break_down_id")]] != '' && $row[csf("po_break_down_id")] != '') {
					//189	=> "Embellishment (YD)",
					$tna_task_update_data[$row[csf("po_break_down_id")]][189]['reqqnty'] += $row[csf("amount")];
					$to_process_task[$row[csf("po_break_down_id")]][189] = 189;
				}
			}
		}


		//echo $tna_task_update_data[38930][267]['reqqnty'];die;	


		//****************************************************************************************		


		$sql = "SELECT TASK_NUMBER,PO_NUMBER_ID,ACTUAL_START_DATE,ACTUAL_FINISH_DATE,TASK_START_DATE,TASK_FINISH_DATE ,PLAN_START_FLAG,PLAN_FINISH_FLAG FROM tna_plan_actual_history WHERE status_active =1 and is_deleted=0 and task_type=1 " . where_con_using_array($order_id_array, 0, 'po_number_id') . " order by id";

		//echo $sql;die;
		$result = sql_select($sql);
		$tna_updated_date = array();
		foreach ($result as $row) {
			$tna_updated_date[$row['PO_NUMBER_ID']][$row['TASK_NUMBER']]['start'] = $row['ACTUAL_START_DATE'];
			$tna_updated_date[$row['PO_NUMBER_ID']][$row['TASK_NUMBER']]['finish'] = $row['ACTUAL_FINISH_DATE'];
			$tna_updated_date[$row['PO_NUMBER_ID']][$row['TASK_NUMBER']]['planstart'] = $row['TASK_START_DATE'];
			$tna_updated_date[$row['PO_NUMBER_ID']][$row['TASK_NUMBER']]['planfinish'] = $row['TASK_FINISH_DATE'];
			$tna_updated_date[$row['PO_NUMBER_ID']][$row['TASK_NUMBER']]['planstartflag'] = $row['PLAN_START_FLAG'];
			$tna_updated_date[$row['PO_NUMBER_ID']][$row['TASK_NUMBER']]['planfinishflag'] = $row['PLAN_FINISH_FLAG'];
		}


		// TNA Processs Data List for TNA UPDATE

		$sql = "SELECT ID,job_no,PO_NUMBER_ID,task_category,TASK_NUMBER,ACTUAL_START_DATE,ACTUAL_FINISH_DATE,TEMPLATE_ID FROM tna_process_mst WHERE status_active =1 and is_deleted = 0  and task_type=1 " . where_con_using_array($order_id_array, 0, 'po_number_id') . "";

		//echo $sql;die;
		$result = sql_select($sql);
		$tna_process_list = array();
		$tna_process_details = array();
		$changed_templates = array();
		foreach ($result as $row) {
			if ($po_order_template[$row['PO_NUMBER_ID']] == $row['TEMPLATE_ID'] || $row['TEMPLATE_ID'] == '') {
				$tna_process_list[$row['PO_NUMBER_ID']][$row['TASK_NUMBER']] = $row['ID'];
				$tna_process_details[$row['ID']]['start'] = $row['ACTUAL_START_DATE'];
				$tna_process_details[$row['ID']]['finish'] = $row['ACTUAL_FINISH_DATE'];
			} else {
				$changed_templates[$row['PO_NUMBER_ID']] = $row['PO_NUMBER_ID'];
			}
		}


		if (count($changed_templates) > 0) {
			$con = connect();
			$rid = execute_query("delete FROM TNA_PROCESS_MST WHERE task_type=1 " . where_con_using_array($changed_templates, 0, 'po_number_id') . "", 1);
			oci_commit($con);
			disconnect($con);
		}



		//Sample sub.......................................................

		$sample_sql = "SELECT a.id as CID,a.PO_BREAK_DOWN_ID,SAMPLE_TYPE,a.approval_status_date as MAXDATE,a.approval_status_date as MINDATE
			 FROM wo_po_sample_approval_info  a, lib_sample b,wo_po_color_size_breakdown c WHERE sample_type in (2,3,4,7,8,9,10,11,12,13,14,20) and c.id=a.color_number_id and a.po_break_down_id=c.po_break_down_id and a.job_no_mst=c.job_no_mst and c.status_active=1 AND c.is_deleted = 0 and b.id=a.SAMPLE_TYPE_ID and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.BUSINESS_NATURE=2 and b.status_active=1 and c.status_active=1  and c.is_deleted = 0 " . where_con_using_array($job_no_array, 1, 'a.job_no_mst') . " ";
		//echo $sample_sql;die;
		$sample_sql_result = sql_select($sample_sql);
		foreach ($sample_sql_result as $row) {
			$appr = 0;
			$sub = 0;
			$sub2 = 0;
			$appr2 = 0;
			if ($row['SAMPLE_TYPE'] == 2) {
				$sub = 8;
				$appr = 12;
			} elseif ($row['SAMPLE_TYPE'] == 3) {
				$sub = 7;
				$appr = 13;
			} elseif ($row['SAMPLE_TYPE'] == 4) {
				$sub = 14;
				$appr = 15;
			} elseif ($row['SAMPLE_TYPE'] == 7) {
				$sub = 16;
				$appr = 17;
			} elseif ($row['SAMPLE_TYPE'] == 8) {
				$sub = 21;
				$appr = 22;
			} elseif ($row['SAMPLE_TYPE'] == 9) {
				$sub = 23;
				$appr = 24;
			} elseif ($row['SAMPLE_TYPE'] == 10) {
				$sub = 26;
				$appr = 27;
			} elseif ($row['SAMPLE_TYPE'] == 11) {
				$sub = 28;
				$appr = 29;
			} elseif ($row['SAMPLE_TYPE'] == 12) {
				$sub = 93;
				$appr = 94;
				$sub2 = 193;
				$appr2 = 194;
			} elseif ($row['SAMPLE_TYPE'] == 13) {
				$sub = 197;
				$appr = 198;
			} elseif ($row['SAMPLE_TYPE'] == 14) {
				$sub = 265;
				$appr = 266;
			} elseif ($row['SAMPLE_TYPE'] == 20) {
				$sub = 264;
				$appr = 123;
			}

			$to_process_task[$row[csf("po_break_down_id")]][$appr] = 1;
			$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][$appr]['reqqnty'] += 1;
			$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][$sub]['reqqnty'] += 1;

			if ($appr2 != 0) {
				$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][$appr2]['reqqnty'] += 1;
				$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][$sub2]['reqqnty'] += 1;
			}
		}

		//print_r($tna_task_update_data[72822][13]);die;


		$sample_sub_sql = "SELECT count(a.id) as CID,a.PO_BREAK_DOWN_ID,sample_type,max(submitted_to_buyer) as MAXDATE,min(submitted_to_buyer) as MINDATE
			 FROM wo_po_sample_approval_info  a, lib_sample b,wo_po_color_size_breakdown c WHERE sample_type in (2,3,4,7,8,9,10,11,12,13,14,20) and c.id=a.color_number_id and a.po_break_down_id=c.po_break_down_id and a.job_no_mst=c.job_no_mst and c.status_active=1 AND c.is_deleted = 0 and b.id=a.SAMPLE_TYPE_ID and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 and b.BUSINESS_NATURE=2 " . where_con_using_array($job_no_array, 1, 'a.job_no_mst') . " and a.submitted_to_buyer is not null group by b.sample_type,a.po_break_down_id order by po_break_down_id asc";

		//echo $sample_sub_sql;die;
		$sample_sub_sql_result = sql_select($sample_sub_sql);
		foreach ($sample_sub_sql_result as $row) {
			$sub = 0;
			$sub2 = 0;
			if ($row['SAMPLE_TYPE'] == 2) {
				$sub = 8;
			} elseif ($row['SAMPLE_TYPE'] == 3) {
				$sub = 7;
			} elseif ($row['SAMPLE_TYPE'] == 4) {
				$sub = 14;
			} elseif ($row['SAMPLE_TYPE'] == 7) {
				$sub = 16;
			} elseif ($row['SAMPLE_TYPE'] == 8) {
				$sub = 21;
			} elseif ($row['SAMPLE_TYPE'] == 9) {
				$sub = 23;
			} elseif ($row['SAMPLE_TYPE'] == 10) {
				$sub = 26;
			} elseif ($row['SAMPLE_TYPE'] == 11) {
				$sub = 28;
			} elseif ($row['SAMPLE_TYPE'] == 12) {
				$sub = 93;
				$sub2 = 193;
			} elseif ($row['SAMPLE_TYPE'] == 13) {
				$sub = 197;
			} elseif ($row['SAMPLE_TYPE'] == 14) {
				$sub = 265;
			} elseif ($row['SAMPLE_TYPE'] == 20) {
				$sub = 264;
			}

			$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][$sub]['max_start_date'] = $row['MAXDATE'];
			$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][$sub]['min_start_date'] = $row['MINDATE'];
			$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][$sub]['doneqnty'] = $row['CID'];

			if ($sub2 != 0) {
				$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][$sub2]['max_start_date'] = $row['MAXDATE'];
				$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][$sub2]['min_start_date'] = $row['MINDATE'];
				$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][$sub2]['doneqnty'] = $row['CID'];
			}
		}

		//.......................................................Sample sub end;


		//Sample app.......................................................


		$sample_app_sql = "SELECT count(a.id) as CID,a.PO_BREAK_DOWN_ID,sample_type,max(approval_status_date) as MAXDATE,min(approval_status_date) as MINDATE
			 FROM wo_po_sample_approval_info  a, lib_sample b,wo_po_color_size_breakdown c WHERE sample_type in (2,3,4,7,8,9,10,11,12,13,14,20) and c.id=a.color_number_id and a.po_break_down_id=c.po_break_down_id and a.job_no_mst=c.job_no_mst and c.status_active=1 AND c.is_deleted = 0 and b.id=a.SAMPLE_TYPE_ID and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 and a.APPROVAL_STATUS=3  and b.BUSINESS_NATURE=2 " . where_con_using_array($job_no_array, 1, 'a.job_no_mst') . " group by b.sample_type,a.po_break_down_id order by po_break_down_id asc";

		//echo $sql_task;die;
		$sample_app_sql_result = sql_select($sample_app_sql);
		foreach ($sample_app_sql_result as $row) {
			$appr = 0;
			$appr2 = 0;
			if ($row['SAMPLE_TYPE'] == 2) {
				$appr = 12;
			} elseif ($row['SAMPLE_TYPE'] == 3) {
				$appr = 13;
			} elseif ($row['SAMPLE_TYPE'] == 4) {
				$appr = 15;
			} elseif ($row['SAMPLE_TYPE'] == 7) {
				$appr = 17;
			} elseif ($row['SAMPLE_TYPE'] == 8) {
				$appr = 22;
			} elseif ($row['SAMPLE_TYPE'] == 9) {
				$appr = 24;
			} elseif ($row['SAMPLE_TYPE'] == 10) {
				$appr = 27;
			} elseif ($row['SAMPLE_TYPE'] == 11) {
				$appr = 29;
			} elseif ($row['SAMPLE_TYPE'] == 12) {
				$appr = 94;
				$appr2 = 194;
			} elseif ($row['SAMPLE_TYPE'] == 13) {
				$appr = 198;
			} elseif ($row['SAMPLE_TYPE'] == 14) {
				$appr = 266;
			} elseif ($row['SAMPLE_TYPE'] == 20) {
				$appr = 123;
			}


			$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][$appr]['max_start_date'] = $row['MAXDATE'];
			$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][$appr]['min_start_date'] = $row['MINDATE'];
			$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][$appr]['doneqnty'] = $row['CID'];

			if ($appr2 != 0) {
				$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][$appr2]['max_start_date'] = $row['MAXDATE'];
				$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][$appr2]['min_start_date'] = $row['MINDATE'];
				$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][$appr2]['doneqnty'] = $row['CID'];
			}
		}


		//................................................Sample app end;

		//print_r($tna_task_update_data[60643][7]);die;



		//277 => "Finished fabric to be in-house [Foreign]",
		//306 => "Finished fabric to be in-house [Local]",
		$sql_knit_finish_rec = "select a.PO_BREAKDOWN_ID,c.booking_id, c.RECEIVE_BASIS,d.SOURCE,  sum(a.quantity) as QTY,min(c.RECEIVE_DATE) MINDATE, max(c.RECEIVE_DATE) MAXDATE from order_wise_pro_details a,pro_finish_fabric_rcv_dtls b,inv_receive_master c,COM_PI_MASTER_DETAILS d where d.id=c.BOOKING_ID and c.RECEIVE_BASIS=1 and a.dtls_id=b.id and b.mst_id=c.id  " . where_con_using_array($order_id_array, 0, 'a.po_breakdown_id') . " and a.entry_form=37 and a.status_active=1 and a.is_deleted=0  group by a.po_breakdown_id,c.booking_id,c.RECEIVE_BASIS,d.SOURCE
		union all
		select a.PO_BREAKDOWN_ID,c.booking_id, c.RECEIVE_BASIS,d.SOURCE,  sum(a.quantity) as QTY,min(c.RECEIVE_DATE) MINDATE, max(c.RECEIVE_DATE) MAXDATE from order_wise_pro_details a,pro_finish_fabric_rcv_dtls b,inv_receive_master c,wo_booking_mst d where d.id=c.BOOKING_ID and c.RECEIVE_BASIS in( 2,11) and a.dtls_id=b.id and b.mst_id=c.id  " . where_con_using_array($order_id_array, 0, 'a.po_breakdown_id') . " and a.entry_form=37 and a.status_active=1 and a.is_deleted=0  group by a.po_breakdown_id,c.booking_id,c.RECEIVE_BASIS,d.SOURCE";
		$sql_knit_finish_rec_result = sql_select($sql_knit_finish_rec);
		foreach ($sql_knit_finish_rec_result as $row) {
			if ($row['SOURCE'] == 1) {
				$tna_task_update_data[$row['PO_BREAKDOWN_ID']][277]['max_start_date'] = $row['MAXDATE'];
				$tna_task_update_data[$row['PO_BREAKDOWN_ID']][277]['min_start_date'] = $row['MINDATE'];
				$tna_task_update_data[$row['PO_BREAKDOWN_ID']][277]['doneqnty'] += $row['QTY'];
			} else {
				$tna_task_update_data[$row['PO_BREAKDOWN_ID']][306]['max_start_date'] = $row['MAXDATE'];
				$tna_task_update_data[$row['PO_BREAKDOWN_ID']][306]['min_start_date'] = $row['MINDATE'];
				$tna_task_update_data[$row['PO_BREAKDOWN_ID']][306]['doneqnty'] += $row['QTY'];
			}
		}

		//echo $sql_knit_finish_rec;die;



		$sql = "select b.PO_BREAK_DOWN_ID, sum(b.qnty) as qnty, min(a.allocation_date) as mindate, max(a.allocation_date) as maxdate from inv_material_allocation_mst a, inv_material_allocation_dtls b 
where a.id=b.mst_id and b.item_category=1 and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  " . where_con_using_array($order_id_array, 0, 'b.po_break_down_id') . "  group by b.po_break_down_id";

		$result = sql_select($sql);
		foreach ($result as $row) {
			$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][48]['max_start_date'] = $row[csf("maxdate")];
			$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][48]['min_start_date'] = $row[csf("mindate")];
			$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][48]['doneqnty'] = $row[csf("qnty")];
			$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][252]['reqqnty'] = $row[csf("qnty")];
		}


		$yarnReqSql = "select b.ORDER_ID, sum(b.REQUISITION_QTY) as QTY,min(a.REQUISITION_DATE) as MIN_DATE,max(a.REQUISITION_DATE) as MAX_DATE  from PPL_YARN_REQUISITION_ENTRY a,PPL_YARN_REQUISITION_BREAKDOWN b where a.REQUISITION_NO=b.REQUISITION_ID and b.status_active=1 AND b.is_deleted=0  " . where_con_using_array($order_id_array, 0, 'b.ORDER_ID') . " group by b.ORDER_ID";
		$yarnReqSqlRes = sql_select($yarnReqSql);
		foreach ($yarnReqSqlRes as $row) {
			$tna_task_update_data[$row['ORDER_ID']][252]['doneqnty'] = $row['QTY'];
			$tna_task_update_data[$row['ORDER_ID']][252]['max_start_date'] = $row['MAX_DATE'];
			$tna_task_update_data[$row['ORDER_ID']][252]['min_start_date'] = $row['MIN_DATE'];
		}

		//print_r($tna_task_update_data[58524][48]);die;

		//echo $yarnReqSql;die;

		// LABDIP Approval Data for Update


		$sql_task = "SELECT count(id) as cid, po_break_down_id, max(submitted_to_buyer) as max_submitted_to_buyer,min(submitted_to_buyer) as min_submitted_to_buyer,max(approval_status_date) as max_approval_status_date,min(approval_status_date) as min_approval_status_date, LISTAGG(color_name_id, ',') WITHIN GROUP (ORDER BY color_name_id) AS color_name_id
		 FROM wo_po_lapdip_approval_info WHERE is_deleted = 0 and status_active=1 " . where_con_using_array($order_id_array, 0, 'po_break_down_id') . "  group by po_break_down_id order by po_break_down_id asc";


		$result = sql_select($sql_task);
		$labdip_update_task = array();
		foreach ($result as $row) {
			$to_process_task[$row[csf("po_break_down_id")]][9] = 9;
			$to_process_task[$row[csf("po_break_down_id")]][10] = 10;

			$colors = explode(",", $row[csf("color_name_id")]);
			if (count($colors) > 1) {
				if (in_array($tba_color_id, $colors)) {
					if ($delete_po_lab == "") {
						$delete_po_lab = $row[csf("po_break_down_id")];
					} else {
						$delete_po_lab .= "," . $row[csf("po_break_down_id")];
					}
				}
			}

			$tna_task_update_data[$row[csf("po_break_down_id")]][10]['max_start_date'] = $row[csf("max_approval_status_date")];
			$tna_task_update_data[$row[csf("po_break_down_id")]][10]['min_start_date'] = $row[csf("min_approval_status_date")];
			$tna_task_update_data[$row[csf("po_break_down_id")]][10]['noofval'] = $row[csf("cid")];

			$tna_task_update_data[$row[csf("po_break_down_id")]][9]['max_start_date'] = $row[csf("max_submitted_to_buyer")];
			$tna_task_update_data[$row[csf("po_break_down_id")]][9]['min_start_date'] = $row[csf("min_submitted_to_buyer")];
			$tna_task_update_data[$row[csf("po_break_down_id")]][9]['noofval'] = $row[csf("cid")];
		}

		if ($delete_po_lab != "") {
			$con = connect();
			$rid = execute_query("delete from  wo_po_lapdip_approval_info where color_name_id='" . $tba_color_id . "' " . where_con_using_array(explode(",", $delete_po_lab), 0, 'po_break_down_id') . " ", 1);
			oci_commit($con);
			disconnect($con);
		}





		$sql_task = "SELECT id as cid,po_break_down_id ,submitted_to_buyer,approval_status_date FROM wo_po_lapdip_approval_info WHERE is_deleted = 0 and status_active=1  " . where_con_using_array($order_id_array, 0, 'po_break_down_id') . " ";


		$result = sql_select($sql_task);
		$sample_approval_update = array();
		foreach ($result as $row) {
			if ($row[csf("approval_status_date")] == '0000-00-00') {
				$row[csf("approval_status_date")] = '';
			}
			if ($row[csf("submitted_to_buyer")] == '0000-00-00') {
				$row[csf("submitted_to_buyer")] = '';
			}

			if ($row[csf("approval_status_date")] != '') {
				$tna_task_update_data[$row[csf("po_break_down_id")]][10]['noofapproved'] += 1;
			}
			if ($row[csf("submitted_to_buyer")] != '') {
				$tna_task_update_data[$row[csf("po_break_down_id")]][9]['noofapproved'] += 1;
			}
		}




		// Trims Approval Data for Update


		$sql_task = "SELECT count(a.id) as cid,po_break_down_id,max(submitted_to_buyer) as max_submitted_to_buyer,min(submitted_to_buyer) as min_submitted_to_buyer,max(approval_status_date) as max_approval_status_date,min(approval_status_date) as min_approval_status_date,trim_type
	FROM lib_item_group b, wo_po_trims_approval_info a
	WHERE b.id=a.accessories_type_id and a.is_deleted = 0 and a.status_active=1 and b.is_deleted = 0 and b.status_active=1 " . where_con_using_array($order_id_array, 0, 'po_break_down_id') . " group by po_break_down_id,trim_type order by po_break_down_id asc";


		//echo $sql_task;die;

		$result = sql_select($sql_task);
		$trims_update_task = array();
		foreach ($result as $row) {
			$to_process_task[$row[csf("po_break_down_id")]][11] = 11;
			$to_process_task[$row[csf("po_break_down_id")]][25] = 25;

			$tna_task_update_data[$row[csf("po_break_down_id")]][11]['max_start_date'] = $row[csf("max_approval_status_date")];
			$tna_task_update_data[$row[csf("po_break_down_id")]][11]['min_start_date'] = $row[csf("min_approval_status_date")];
			$tna_task_update_data[$row[csf("po_break_down_id")]][11]['noofval'] = $row[csf("cid")];

			$tna_task_update_data[$row[csf("po_break_down_id")]][25]['max_start_date'] = $row[csf("max_submitted_to_buyer")];
			$tna_task_update_data[$row[csf("po_break_down_id")]][25]['min_start_date'] = $row[csf("min_submitted_to_buyer")];
			$tna_task_update_data[$row[csf("po_break_down_id")]][25]['noofval'] = $row[csf("cid")];
		}



		$sql_task = "SELECT count(a.id) as CID,PO_BREAK_DOWN_ID,trim_type FROM lib_item_group b, wo_po_trims_approval_info a
	WHERE b.id=a.accessories_type_id and approval_status_date is not null  and a.is_deleted = 0 and a.status_active=1   and b.is_deleted = 0 and b.status_active=1 " . where_con_using_array($order_id_array, 0, 'po_break_down_id') . " group by po_break_down_id,trim_type order by po_break_down_id asc";
		//echo $sql_task;die;


		$result = sql_select($sql_task);
		$sample_approval_update = array();
		foreach ($result as $row) {
			$tna_task_update_data[$row['PO_BREAK_DOWN_ID']][11]['noofapproved'] = $row['CID'];
		}

		// Embelishment Approval Data for Update


		$sql_task = "SELECT count(id) as cid,po_break_down_id,max(submitted_to_buyer) as max_submitted_to_buyer,min(submitted_to_buyer) as min_submitted_to_buyer,max(approval_status_date) as max_approval_status_date,min(approval_status_date) as min_approval_status_date,embellishment_id FROM wo_po_embell_approval WHERE is_deleted = 0 and status_active=1 " . where_con_using_array($order_id_array, 0, 'po_break_down_id') . " group by po_break_down_id,embellishment_id order by po_break_down_id asc";


		$result = sql_select($sql_task);
		$embelishment_update_task = array();
		foreach ($result as $row) {
			if ($row[csf("embellishment_id")] == 1) {
				$sub = 126;
				$appr = 127;
			} else if ($row[csf("embellishment_id")] == 2) {
				$sub = 128;
				$appr = 129;
			} else {
				$sub = 19;
				$appr = 20;
			}

			$to_process_task[$row[csf("po_break_down_id")]][$sub] = $sub;
			$to_process_task[$row[csf("po_break_down_id")]][$appr] = $appr;

			$tna_task_update_data[$row[csf("po_break_down_id")]][$appr]['max_start_date'] = $row[csf("max_approval_status_date")];
			$tna_task_update_data[$row[csf("po_break_down_id")]][$appr]['min_start_date'] = $row[csf("min_approval_status_date")];

			$tna_task_update_data[$row[csf("po_break_down_id")]][$sub]['max_start_date'] = $row[csf("max_submitted_to_buyer")];
			$tna_task_update_data[$row[csf("po_break_down_id")]][$sub]['min_start_date'] = $row[csf("min_submitted_to_buyer")];
			$tna_task_update_data[$row[csf("po_break_down_id")]][$appr]['noofval'] = $row[csf("cid")];
			$tna_task_update_data[$row[csf("po_break_down_id")]][$sub]['noofval'] = $row[csf("cid")];
		}



		$sql_task = "SELECT count(id) as cid,po_break_down_id FROM wo_po_embell_approval WHERE approval_status_date is not null   and is_deleted = 0 and status_active=1 " . where_con_using_array($order_id_array, 0, 'po_break_down_id') . " group by po_break_down_id,embellishment_id order by po_break_down_id asc";

		//echo $sql_task;die;

		$result = sql_select($sql_task);
		$sample_approval_update = array();
		foreach ($result as $row) {
			if ($row[csf("embellishment_id")] == 1) {
				$sub = 126;
				$appr = 127;
			} else if ($row[csf("embellishment_id")] == 2) {
				$sub = 128;
				$appr = 129;
			} else {
				$sub = 19;
				$appr = 20;
			}


			$tna_task_update_data[$row[csf("po_break_down_id")]][$sub]['noofapproved'] = $row[csf("cid")];
			$tna_task_update_data[$row[csf("po_break_down_id")]][$appr]['noofapproved'] = $row[csf("cid")];
		}

		unset($result);





		$sql = "SELECT distinct (po_break_down_id) as  po_break_down_id,color_type_id FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b WHERE a.id=b.pre_cost_fabric_cost_dtls_id and b.cons>0 and a.color_type_id in (2,3,4,6,5) and a.status_active =1 and a.is_deleted=0 " . where_con_using_array($order_id_array, 0, 'b.po_break_down_id') . "";
		//echo $sql;die;

		//echo $sql;die;
		$data_array = sql_select($sql);
		foreach ($data_array as $row) {
			if ($row[csf("color_type_id")] == 5) {
				$app = 62;
				$sub = 63;
			} else {
				$app = 51;
				$sub = 52;
			}
			$to_process_task[$row[csf("po_break_down_id")]][$app] = $app;
			$to_process_task[$row[csf("po_break_down_id")]][$sub] = $sub;
		}
		unset($data_array);


		$sql_task = "SELECT A.ITEM_CATEGORY,A.BOOKING_TYPE,A.SOURCE ,B.PO_BREAK_DOWN_ID,a.IS_APPROVED, 
		sum(wo_qnty) as TFB_QNTY, 
		sum(b.amount/nullif(b.exchange_rate,0))  as TFB_AMOUNT, 
		sum(grey_fab_qnty) as GFB_QNTY, 
		min(a.booking_date) as START_DATE, 
		max(a.booking_date) as END_DATE  
		FROM  wo_booking_mst a, wo_booking_dtls b
		WHERE a.booking_no=b.booking_no and a.item_category in ( 2,4,12,3 ) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and (a.is_short=2 or a.ITEM_FROM_PRECOST=1) " . where_con_using_array($order_id_array, 0, 'b.po_break_down_id') . "  group by b.po_break_down_id,a.item_category,a.booking_type,A.IS_APPROVED,a.SOURCE order by b.po_break_down_id asc";
		//echo $sql_task;die;

		$result = sql_select($sql_task);
		$purchase_update_task = array();
		foreach ($result as $row) {
			$tsktype = 0;
			$qnty = 0;

			if ($row['SOURCE'] == 1) {

				if ($row[csf("item_category")] == 2) {
					$tsktype = 276;
					$qnty = $row[csf("gfb_qnty")];
				} else if ($row[csf("item_category")] == 3) {
					$tsktype = 302;
					$qnty = $row[csf("gfb_qnty")];
				} else if ($row[csf("item_category")] == 4) {
					$tsktype = 278;
					$qnty = $row[csf("tfb_amount")];
				} else if ($row[csf("item_category")] == 12) {
					$tsktype = 33;
					$qnty = $row[csf("TFB_QNTY")];
				}

				$tna_task_update_data[$row[csf("po_break_down_id")]][$tsktype]['max_start_date'] = $row[csf("end_date")];
				$tna_task_update_data[$row[csf("po_break_down_id")]][$tsktype]['min_start_date'] = $row[csf("start_date")];
				$tna_task_update_data[$row[csf("po_break_down_id")]][$tsktype]['doneqnty'] += $qnty;

				if ($row[csf("booking_type")] == 1 && $row[csf("item_category")] == 2) {
					$tsktype = 31;
					$qnty = $row[csf("gfb_qnty")];
				} else if ($row[csf("booking_type")] == 4 && $row[csf("item_category")] == 2) {
					$tsktype = 30;
					$qnty = $row[csf("gfb_qnty")];
					$tna_task_update_data[$row[csf("po_break_down_id")]][$tsktype]['reqqnty'] += $qnty;
					$to_process_task[$row[csf("po_break_down_id")]][$tsktype] = $tsktype;
				} else if ($row[csf("booking_type")] == 4 && $row[csf("item_category")] == 3) {
					$tsktype = 269;
					$qnty = $row[csf("gfb_qnty")];
				}

				$tna_task_update_data[$row[csf("po_break_down_id")]][$tsktype]['max_start_date'] = $row[csf("end_date")];
				$tna_task_update_data[$row[csf("po_break_down_id")]][$tsktype]['min_start_date'] = $row[csf("start_date")];
				$tna_task_update_data[$row[csf("po_break_down_id")]][$tsktype]['doneqnty'] += $qnty;

				//echo $tsktype;die;
			} else {
				if ($row[csf("item_category")] == 2) {
					$tsktype = 31;
					$qnty = $row[csf("gfb_qnty")];
					//Fabric Booking To Be Issued [Local]............
					$tna_task_update_data[$row[csf("po_break_down_id")]][305]['doneqnty'] += $qnty;
					$tna_task_update_data[$row[csf("po_break_down_id")]][305]['max_start_date'] = $row[csf("end_date")];
					$tna_task_update_data[$row[csf("po_break_down_id")]][305]['min_start_date'] = $row[csf("start_date")];
					//............................end; 
				} else if ($row[csf("item_category")] == 3) {
					$tsktype = 34;
					$qnty = $row[csf("gfb_qnty")];
				} else if ($row[csf("item_category")] == 4) {
					$tsktype = 32;
					$qnty = $row[csf("tfb_amount")];
					//Finishing Trims Booking To Be Issued [Local]........................
					$tna_task_update_data[$row[csf("po_break_down_id")]][307]['max_start_date'] = $row[csf("end_date")];
					$tna_task_update_data[$row[csf("po_break_down_id")]][307]['min_start_date'] = $row[csf("start_date")];
					$tna_task_update_data[$row[csf("po_break_down_id")]][307]['doneqnty'] += $qnty;
					//.....................................end;
				} else if ($row[csf("item_category")] == 12) {
					$tsktype = 33;
					$qnty = $row[csf("TFB_QNTY")];
				}

				if ($row[csf("booking_type")] == 1 && $row[csf("item_category")] == 2) {
					$tsktype = 31;
					$qnty = $row[csf("gfb_qnty")];
				} else if ($row[csf("booking_type")] == 4 && $row[csf("item_category")] == 2) {
					$tsktype = 30;
					$qnty = $row[csf("gfb_qnty")];
					$tna_task_update_data[$row[csf("po_break_down_id")]][$tsktype]['reqqnty'] += $qnty;
					$to_process_task[$row[csf("po_break_down_id")]][$tsktype] = $tsktype;
				} else if ($row[csf("booking_type")] == 4 && $row[csf("item_category")] == 3) {
					$tsktype = 269;
					$qnty = $row[csf("gfb_qnty")];
				}
			}

			//echo $tsktype;die;

			$tna_task_update_data[$row[csf("po_break_down_id")]][$tsktype]['max_start_date'] = $row[csf("end_date")];
			$tna_task_update_data[$row[csf("po_break_down_id")]][$tsktype]['min_start_date'] = $row[csf("start_date")];
			$tna_task_update_data[$row[csf("po_break_down_id")]][$tsktype]['doneqnty'] += $qnty;

			//echo $tsktype.'='.$qnty.',';

			if ($row['IS_APPROVED'] == 1) {
				$tna_task_update_data[$row[csf("po_break_down_id")]][314]['max_start_date'] = $row[csf("end_date")];
				$tna_task_update_data[$row[csf("po_break_down_id")]][314]['min_start_date'] = $row[csf("start_date")];
				$tna_task_update_data[$row[csf("po_break_down_id")]][314]['doneqnty'] += $qnty;
			}
		}
		unset($result);

		// print_r($tna_task_update_data[71041][276]);die;


		//print_r($purchase_update_task); die;
		// Inventory Update Data

		$sql = "SELECT b.po_breakdown_id,b.entry_form, min(a.transaction_date) mindate, max(a.transaction_date) maxdate, sum(quantity) as prod_qntry 
	FROM inv_transaction a,  order_wise_pro_details b where  b.trans_id=a.id and b.entry_form in (2,7,22,37,3,58,18,17,61,68,19 ) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 " . where_con_using_array($order_id_array, 0, 'b.po_breakdown_id') . " group by b.po_breakdown_id,b.entry_form order by b.po_breakdown_id";

		//echo $sql;die;
		$inventory_transaction_update = array();
		$data_array = sql_select($sql);
		foreach ($data_array as $row) {
			$tsktype = 0;
			if ($row[csf("entry_form")] == 2) $tsktype = 72;
			else if ($row[csf("entry_form")] == 7)  $tsktype = 73;
			else if ($row[csf("entry_form")] == 37) $tsktype = 73;
			else if ($row[csf("entry_form")] == 17) {
				$tsktype = 73;
				$tsktype = 352;
			} //Woven Finish Fabric
			else if ($row[csf("entry_form")] == 68) $tsktype = 73; //Knit Finish Fabric Roll Receive By Store

			else if ($row[csf("entry_form")] == 22) $tsktype = 72; //22=Knit Grey Fabric Receive;
			else if ($row[csf("entry_form")] == 58) $tsktype = 72; //58=Knit Grey Fabric Roll Receive;
			else if ($row[csf("entry_form")] == 3)  $tsktype = 50;
			else if ($row[csf("entry_form")] == 18) {
				$tsktype = 74;
			}  //Knit Finish Fabric issue to cut
			else if ($row[csf("entry_form")] == 19) {
				$tsktype = 353;
			}  //Woven Finish Fabric issue to cut
			else if ($row[csf("entry_form")] == 61) $tsktype = 203; //Grey Fabric Issue

			$tna_task_update_data[$row[csf("po_breakdown_id")]][$tsktype]['max_start_date'] = $row[csf("maxdate")];
			$tna_task_update_data[$row[csf("po_breakdown_id")]][$tsktype]['min_start_date'] = $row[csf("mindate")];
			$tna_task_update_data[$row[csf("po_breakdown_id")]][$tsktype]['doneqnty'] += $row[csf("prod_qntry")];
		}
		unset($data_array);

		//print_r($tna_task_update_data[77213][353]);die;

		$yarnIssueYDSql = "SELECT b.PO_BREAKDOWN_ID, min(a.transaction_date) MINDATE, max(a.transaction_date) MAXDATE, sum(quantity) as PROD_QNTRY FROM inv_transaction a,  order_wise_pro_details b where  b.trans_id=a.id and b.entry_form=3 and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 " . where_con_using_array($order_id_array, 0, 'b.po_breakdown_id') . " and b.ISSUE_PURPOSE=2 group by b.po_breakdown_id";
		$yarnIssueYDSqlRes = sql_select($yarnIssueYDSql);
		foreach ($yarnIssueYDSqlRes as $rows) {
			$tna_task_update_data[$row['PO_BREAKDOWN_ID']][345]['max_start_date'] = $row['MAXDATE'];
			$tna_task_update_data[$row['PO_BREAKDOWN_ID']][345]['min_start_date'] = $row['MINDATE'];
			$tna_task_update_data[$row['PO_BREAKDOWN_ID']][345]['doneqnty'] = $row['PROD_QNTRY'];
		}

		//print_r($tna_task_update_data[69709][345]);die;
		//print_r($tna_task_update_data[69709][50]) ;die;
		//echo $yarnIssueYDSql;die;

		//$trim_type_arr=return_library_array( "select id,trim_type from lib_item_group where  item_category = 4 and trim_type in(1,2) and is_deleted = 0 and status_active = 1",'id','trim_type');

		//print_r($trims_data_item_qty);die;

		foreach ($trims_data_item_qty as $pid => $soruceDataArr) {
			foreach ($soruceDataArr as $source_id => $itemArr) {
				if ($source_id == 1) {
					foreach ($itemArr as $item_id => $trims_pre_cost_qty) {
						if ($trim_type_arr[$item_id] == 1) {
							$tsktype = 301;
							$tsktype2 = 279;
						} else {
							$tsktype = 300;
							$tsktype2 = 134;
						}
						//$tsktype=($trim_type_arr[$item_id] == 1) ? 70 : 71;
						$tna_task_update_data[$pid][$tsktype]['reqqnty'] += $trims_pre_cost_qty;
						$tna_task_update_data[$pid][$tsktype2]['reqqnty'] += $trims_pre_cost_qty;
						$to_process_task[$pid][$tsktype] = $tsktype;
						$to_process_task[$pid][$tsktype2] = $tsktype2;
					}
				} else {
					foreach ($itemArr as $item_id => $trims_pre_cost_qty) {
						if ($trim_type_arr[$item_id] == 1) {
							$tsktype = 70;
							$tsktype2 = 270;
							$tna_task_update_data[$pid][310]['reqqnty'] += $trims_pre_cost_qty;
							$to_process_task[$pid][310] = 310;
							$tna_task_update_data[$pid][308]['reqqnty'] += $trims_pre_cost_qty;
							$to_process_task[$pid][308] = 308;
						} else {
							$tsktype = 71;
							$tsktype2 = 134;
							$tna_task_update_data[$pid][309]['reqqnty'] += $trims_pre_cost_qty;
							$to_process_task[$pid][309] = 309;
						}
						//$tsktype=($trim_type_arr[$item_id] == 1) ? 70 : 71;
						$tna_task_update_data[$pid][$tsktype]['reqqnty'] += $trims_pre_cost_qty;
						$tna_task_update_data[$pid][$tsktype2]['reqqnty'] += $trims_pre_cost_qty;
						$to_process_task[$pid][$tsktype] = $tsktype;
						$to_process_task[$pid][$tsktype2] = $tsktype2;
					}
				}
			}
		}




		//echo $tna_task_update_data[41346][70]['reqqnty'].'======';die;	


		$sql = "SELECT a.ORDER_UOM,b.po_breakdown_id, min(a.transaction_date) mindate, max(a.transaction_date) maxdate, sum(quantity) as prod_qntry,d.trim_type 
	FROM inv_transaction a,  order_wise_pro_details b, product_details_master c , lib_item_group d
	where a.prod_id=c.id and b.trans_id=a.id and c.item_group_id=d.id and d.trim_type in (1,2) and b.entry_form in ( 24 )  and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and c.status_active =1 and c.is_deleted=0 and d.status_active =1 and d.is_deleted=0 " . where_con_using_array($order_id_array, 0, 'b.po_breakdown_id') . " group by a.ORDER_UOM,b.po_breakdown_id,d.trim_type
	union all  
	SELECT 0 ORDER_UOM,b.po_breakdown_id, min(a.transaction_date) mindate, max(a.transaction_date) maxdate, 0 as prod_qntry,d.trim_type 
	FROM inv_transaction a,  order_wise_pro_details b, product_details_master c , lib_item_group d
	where a.prod_id=c.id and b.trans_id=a.id and c.item_group_id=d.id and d.trim_type in (1,2) and b.entry_form in ( 24 )  and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and c.status_active =1 and c.is_deleted=0 and d.status_active =1 and d.is_deleted=0 " . where_con_using_array($order_id_array, 0, 'b.po_breakdown_id') . " group by b.po_breakdown_id,d.trim_type";

		// echo $sql;die;  //order by b.po_breakdown_id


		$data_array = sql_select($sql);
		foreach ($data_array as $row) {
			$entry = ($row[csf("trim_type")] == 1) ? 70 : 71;
			if ($row['ORDER_UOM'] == 0) {
				$tna_task_update_data[$row[csf("po_breakdown_id")]][$entry]['max_start_date'] = $row[csf("maxdate")];
				$tna_task_update_data[$row[csf("po_breakdown_id")]][$entry]['min_start_date'] = $row[csf("mindate")];
			}
			if ($row['ORDER_UOM'] == 2) {
				$row[csf("prod_qntry")] = $row[csf("prod_qntry")] * 12;
			}

			$tna_task_update_data[$row[csf("po_breakdown_id")]][$entry]['doneqnty'] += $row[csf("prod_qntry")];
		}
		//echo "sumon"; print_r( $tna_task_percent ); die;



		$sewing_finish_trims_sql = "SELECT e.SOURCE,b.PO_BREAKDOWN_ID, min(a.transaction_date) MINDATE, max(a.transaction_date) MAXDATE, sum(quantity) as QTY,d.TRIM_TYPE 
	FROM inv_transaction a,  order_wise_pro_details b, product_details_master c , lib_item_group d,inv_receive_master e
	where e.id=a.mst_id and a.prod_id=c.id and b.trans_id=a.id and c.item_group_id=d.id and d.trim_type in (1,2) and b.entry_form=24 " . where_con_using_array(explode(",", $po_ids), 0, 'b.po_breakdown_id') . "  and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and c.status_active =1 and c.is_deleted=0 and d.status_active =1 and d.is_deleted=0 and e.status_active =1 and e.is_deleted=0 group by b.po_breakdown_id,d.trim_type,e.source order by b.po_breakdown_id"; //echo $sewing_finish_trims_sql;die;
		$sewing_finish_trims_sql_result = sql_select($sewing_finish_trims_sql);
		foreach ($sewing_finish_trims_sql_result as $row) {
			if ($row['TRIM_TYPE'] == 1) { //Sewing Trims
				if ($row['SOURCE'] == 1) { //Forain..........
					$tna_task_update_data[$row['PO_BREAKDOWN_ID']][301]['max_start_date'] = $row['MINDATE'];
					$tna_task_update_data[$row['PO_BREAKDOWN_ID']][301]['min_start_date'] = $row['MAXDATE'];
					$tna_task_update_data[$row['PO_BREAKDOWN_ID']][301]['doneqnty'] = $row['QTY'];
				} else { //Local..........
					$tna_task_update_data[$row['PO_BREAKDOWN_ID']][310]['max_start_date'] = $row['MINDATE'];
					$tna_task_update_data[$row['PO_BREAKDOWN_ID']][310]['min_start_date'] = $row['MAXDATE'];
					$tna_task_update_data[$row['PO_BREAKDOWN_ID']][310]['doneqnty'] = $row['QTY'];
				}
			} else { //Finishing Trims
				if ($row['SOURCE'] == 1) { //Forain..........
					$tna_task_update_data[$row['PO_BREAKDOWN_ID']][300]['max_start_date'] = $row['MINDATE'];
					$tna_task_update_data[$row['PO_BREAKDOWN_ID']][300]['min_start_date'] = $row['MAXDATE'];
					$tna_task_update_data[$row['PO_BREAKDOWN_ID']][300]['doneqnty'] = $row['QTY'];
				} else { //Local..........
					$tna_task_update_data[$row['PO_BREAKDOWN_ID']][309]['max_start_date'] = $row['MINDATE'];
					$tna_task_update_data[$row['PO_BREAKDOWN_ID']][309]['min_start_date'] = $row['MAXDATE'];
					$tna_task_update_data[$row['PO_BREAKDOWN_ID']][309]['doneqnty'] = $row['QTY'];
				}
			}
		}




		// fabric_production_task Update Data 


		$sql = "SELECT b.po_breakdown_id,b.entry_form, min(a.receive_date) mindate, max(a.receive_date) maxdate, sum(quantity) as prod_qntry 
	FROM inv_receive_master a,  order_wise_pro_details b, pro_finish_fabric_rcv_dtls c where  c.mst_id=a.id and c.id=b.dtls_id and b.entry_form in ( 7 ) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and c.status_active =1 and c.is_deleted=0 " . where_con_using_array($order_id_array, 0, 'b.po_breakdown_id') . " group by b.po_breakdown_id,b.entry_form order by b.po_breakdown_id";


		$data_array = sql_select($sql);
		foreach ($data_array as $row) {
			$tna_task_update_data[$row[csf("po_breakdown_id")]][64]['max_start_date'] = $row[csf("maxdate")];
			$tna_task_update_data[$row[csf("po_breakdown_id")]][64]['min_start_date'] = $row[csf("mindate")];
			$tna_task_update_data[$row[csf("po_breakdown_id")]][64]['doneqnty'] = $row[csf("prod_qntry")];
		}



		$sql = "SELECT b.po_breakdown_id,b.entry_form, min(a.receive_date) mindate, max(a.receive_date) maxdate, sum(quantity) as prod_qntry 
	FROM inv_receive_master a,  order_wise_pro_details b, pro_grey_prod_entry_dtls c where  c.mst_id=a.id and c.id=b.dtls_id and b.entry_form in ( 2 )  and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and c.status_active =1 and c.is_deleted=0 " . where_con_using_array($order_id_array, 0, 'b.po_breakdown_id') . " group by b.po_breakdown_id,b.entry_form order by b.po_breakdown_id";

		$data_array = sql_select($sql);
		foreach ($data_array as $row) {
			$tna_task_update_data[$row[csf("po_breakdown_id")]][60]['max_start_date'] = $row[csf("maxdate")];
			$tna_task_update_data[$row[csf("po_breakdown_id")]][60]['min_start_date'] = $row[csf("mindate")];
			$tna_task_update_data[$row[csf("po_breakdown_id")]][60]['doneqnty'] = $row[csf("prod_qntry")];
		}





		$sql = "select b.po_id, sum(b.batch_qnty) as dye_qnty, min(c.process_end_date) mindate, max(c.process_end_date) maxdate from pro_batch_create_mst a,pro_batch_create_dtls b, pro_fab_subprocess c where a.id=b.mst_id and a.id=c.batch_id and c.load_unload_id=2 and c.entry_form=35 and a.batch_against<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 " . where_con_using_array($order_id_array, 0, 'b.po_id') . " group by b.po_id";


		$data_array = sql_select($sql);
		foreach ($data_array as $row) {
			$tna_task_update_data[$row[csf("po_id")]][61]['max_start_date'] = $row[csf("maxdate")];
			$tna_task_update_data[$row[csf("po_id")]][61]['min_start_date'] = $row[csf("mindate")];
			$tna_task_update_data[$row[csf("po_id")]][61]['doneqnty'] = $row[csf("dye_qnty")];
		}

		unset($data_array);



		// Inspection Data for Update


		$sql = "SELECT po_break_down_id,min(inspection_date) as mind,max(inspection_date) as maxd,sum(inspection_qnty) as sumtot FROM pro_buyer_inspection WHERE status_active =1 and is_deleted = 0 " . where_con_using_array($order_id_array, 0, 'po_break_down_id') . " group by po_break_down_id";

		//echo $sql;die;

		$result = sql_select($sql);
		$inspection_status_array = array();
		foreach ($result as $row) {
			$tna_task_update_data[$row[csf("po_break_down_id")]][101]['max_start_date'] = $row[csf("maxd")];
			$tna_task_update_data[$row[csf("po_break_down_id")]][101]['min_start_date'] = $row[csf("mind")];
			$tna_task_update_data[$row[csf("po_break_down_id")]][101]['doneqnty'] = $row[csf("sumtot")];
			$tna_task_update_data[$row[csf("po_break_down_id")]][101]['reqqnty'] = $po_order_details[$row[csf("po_break_down_id")]]['po_quantity'];
		}

		// Ex-factory Data for Update 

		$sql = "SELECT po_break_down_id,min(ex_factory_date) as mind,max(ex_factory_date) as maxd,sum(ex_factory_qnty) as sumtot FROM  pro_ex_factory_mst WHERE status_active =1 and is_deleted = 0 " . where_con_using_array($order_id_array, 0, 'po_break_down_id') . " group by po_break_down_id";

		//echo $sql;die;

		$result = sql_select($sql);
		$exfactory_status_array = array();
		foreach ($result as $row) {
			$tna_task_update_data[$row[csf("po_break_down_id")]][110]['max_start_date'] = $row[csf("maxd")];
			$tna_task_update_data[$row[csf("po_break_down_id")]][110]['min_start_date'] = $row[csf("mind")];
			$tna_task_update_data[$row[csf("po_break_down_id")]][110]['doneqnty'] = $row[csf("sumtot")];
			$tna_task_update_data[$row[csf("po_break_down_id")]][110]['reqqnty'] = $po_order_details[$row[csf("po_break_down_id")]]['po_quantity'];
		}

		// Doc Submisiion	

		$sql = "SELECT  b.po_breakdown_id, min(a.submit_date) as mind,max(a.submit_date) as maxd, sum(b.current_invoice_qnty) as current_invoice_qnty 
		FROM  com_export_doc_submission_mst a,com_export_invoice_ship_dtls b, com_export_doc_submission_invo c
		WHERE a.id=c.doc_submission_mst_id and c.invoice_id=b.mst_id and b.current_invoice_qnty>0  and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and c.status_active =1 and c.is_deleted=0 " . where_con_using_array($order_id_array, 0, 'b.po_breakdown_id') . " group by b.po_breakdown_id";

		//echo $sql;die;

		$result = sql_select($sql);
		foreach ($result as $row) {

			$tna_task_update_data[$row[csf("po_breakdown_id")]][120]['max_start_date'] = $row[csf("maxd")];
			$tna_task_update_data[$row[csf("po_breakdown_id")]][120]['min_start_date'] = $row[csf("mind")];
			//$tna_task_update_data[$row[csf("po_breakdown_id")]][120]['quantity']=$row[csf("sumtot")];
			$tna_task_update_data[$row[csf("po_breakdown_id")]][120]['doneqnty'] = $row[csf("current_invoice_qnty")];
			$tna_task_update_data[$row[csf("po_breakdown_id")]][120]['reqqnty'] = $po_order_details[$row[csf("po_breakdown_id")]]['po_quantity'];
		}

		// Realzn and invoice



		$sql = "SELECT b.po_breakdown_id,max(d.received_date) maxd,min(d.received_date) mind, sum(b.current_invoice_qnty) as current_invoice_qnty FROM com_export_invoice_ship_dtls b, com_export_doc_submission_invo c, com_export_proceed_realization d WHERE c.invoice_id=b.mst_id and c.doc_submission_mst_id=d.invoice_bill_id and b.current_invoice_qnty>0  and d.status_active =1 and d.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and c.status_active =1 and c.is_deleted=0 " . where_con_using_array($order_id_array, 0, 'b.po_breakdown_id') . "  group by b.po_breakdown_id";

		//echo $sql;die;

		$result = sql_select($sql);
		foreach ($result as $row) {
			$tna_task_update_data[$row[csf("po_breakdown_id")]][121]['max_start_date'] = $row[csf("maxd")];
			$tna_task_update_data[$row[csf("po_breakdown_id")]][121]['min_start_date'] = $row[csf("mind")];
			$tna_task_update_data[$row[csf("po_breakdown_id")]][121]['doneqnty'] = $row[csf("current_invoice_qnty")];
			$tna_task_update_data[$row[csf("po_breakdown_id")]][121]['reqqnty'] = $po_order_details[$row[csf("po_breakdown_id")]]['po_quantity'];
		}



		// Garments Production Data for Update .................................................... 

		$sql = "select a.po_break_down_id,min(a.production_date) as mind,max(a.production_date) as maxd,a.production_type,sum(b.production_qnty) as production_quantity,a.embel_name,b.color_type_id from pro_garments_production_mst a,pro_garments_production_dtls b where  a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 " . where_con_using_array($order_id_array, 0, 'a.po_break_down_id') . " group by a.po_break_down_id,a.production_type,b.color_type_id,a.embel_name";

		//embel_name 	embel_type
		$result = sql_select($sql);

		foreach ($result as $row) {
			$tsktype = 0;
			if ($gross_level == 1) {
				//84=Cutting Prod.;
				if ($row[csf("production_type")] == 1) $tsktype = 84;
				//85=Embellishment
				else if ($row[csf("production_type")] == 3 && $to_process_task[$row[csf("po_break_down_id")]][85] == 85) {
					$tsktype = 85;
				}
				//86=Sewing Prod.
				else if ($row[csf("production_type")] == 5) $tsktype = 86;

				if ($row[csf("production_type")] == 3 && $row[csf("embel_name")] == 1) {
					$tna_task_update_data[$row[csf("po_break_down_id")]][267]['max_start_date'] = $row[csf("maxd")];
					$tna_task_update_data[$row[csf("po_break_down_id")]][267]['min_start_date'] = $row[csf("mind")];
					$tna_task_update_data[$row[csf("po_break_down_id")]][267]['doneqnty'] += $row[csf("production_quantity")];
				} else if ($row[csf("production_type")] == 3 && $row[csf("embel_name")] == 2) {
					$tna_task_update_data[$row[csf("po_break_down_id")]][268]['max_start_date'] = $row[csf("maxd")];
					$tna_task_update_data[$row[csf("po_break_down_id")]][268]['min_start_date'] = $row[csf("mind")];
					$tna_task_update_data[$row[csf("po_break_down_id")]][268]['doneqnty'] += $row[csf("production_quantity")];
				} else if ($row[csf("production_type")] == 2 && $row[csf("embel_name")] == 1) {
					$tna_task_update_data[$row[csf("po_break_down_id")]][312]['max_start_date'] = $row[csf("maxd")];
					$tna_task_update_data[$row[csf("po_break_down_id")]][312]['min_start_date'] = $row[csf("mind")];
					$tna_task_update_data[$row[csf("po_break_down_id")]][312]['doneqnty'] += $row[csf("production_quantity")];
				} else if ($row[csf("production_type")] == 2 && $row[csf("embel_name")] == 2) {
					$tna_task_update_data[$row[csf("po_break_down_id")]][313]['max_start_date'] = $row[csf("maxd")];
					$tna_task_update_data[$row[csf("po_break_down_id")]][313]['min_start_date'] = $row[csf("mind")];
					$tna_task_update_data[$row[csf("po_break_down_id")]][313]['doneqnty'] += $row[csf("production_quantity")];
				}
			} else {


				//2,3,4,6,32,33 Y/D
				if (($row[csf("color_type_id")] == 2) || ($row[csf("color_type_id")] == 3) || ($row[csf("color_type_id")] == 4) || ($row[csf("color_type_id")] == 6) || ($row[csf("color_type_id")] == 32) || ($row[csf("color_type_id")] == 33)) {

					//187=Cutting Prod YD
					if ($row[csf("production_type")] == 1) $tsktype = 187;
					//189=Embellishmen YD
					else if ($row[csf("production_type")] == 3 && $to_process_task[$row[csf("po_break_down_id")]][189] == 189) {
						$tsktype = 189;
					}
					//191=Sewing Prod YD
					else if ($row[csf("production_type")] == 5) $tsktype = 191;
				}
				//5,7 AOP
				else if (($row[csf("color_type_id")] == 5) || ($row[csf("color_type_id")] == 7)) {
					//186=Cutting Pro AOP
					if ($row[csf("production_type")] == 1) $tsktype = 186;
					//188=Embellish AOP
					else if ($row[csf("production_type")] == 3 && $to_process_task[$row[csf("po_break_down_id")]][188] == 188) {
						$tsktype = 188;
					}
					//190=Sewing Prod AOP
					else if ($row[csf("production_type")] == 5) $tsktype = 190;
				} elseif (($row[csf("color_type_id")] == 1) || ($row[csf("color_type_id")] == 20) || ($row[csf("color_type_id")] == 25) || ($row[csf("color_type_id")] == 26) || ($row[csf("color_type_id")] == 27) || ($row[csf("color_type_id")] == 28) || ($row[csf("color_type_id")] == 29) || ($row[csf("color_type_id")] == 30) || ($row[csf("color_type_id")] == 31) || ($row[csf("color_type_id")] == 34)  || ($row[csf("color_type_id")] == 35)  || ($row[csf("color_type_id")] == 36)  || ($row[csf("color_type_id")] == 37) || ($row[csf("color_type_id")] == '')) {
					//84=Cutting Prod.;
					if ($row[csf("production_type")] == 1) $tsktype = 84;
					//85=Embellishment
					else if ($row[csf("production_type")] == 3 && $to_process_task[$row[csf("po_break_down_id")]][85] == 85) {
						$tsktype = 85;
					}
					//86=Sewing Prod.
					else if ($row[csf("production_type")] == 5) $tsktype = 86;


					if ($row[csf("production_type")] == 3 && $row[csf("embel_name")] == 1) {
						$tna_task_update_data[$row[csf("po_break_down_id")]][267]['max_start_date'] = $row[csf("maxd")];
						$tna_task_update_data[$row[csf("po_break_down_id")]][267]['min_start_date'] = $row[csf("mind")];
						$tna_task_update_data[$row[csf("po_break_down_id")]][267]['doneqnty'] += $row[csf("production_quantity")];
					} else if ($row[csf("production_type")] == 3 && $row[csf("embel_name")] == 2) {
						$tna_task_update_data[$row[csf("po_break_down_id")]][268]['max_start_date'] = $row[csf("maxd")];
						$tna_task_update_data[$row[csf("po_break_down_id")]][268]['min_start_date'] = $row[csf("mind")];
						$tna_task_update_data[$row[csf("po_break_down_id")]][268]['doneqnty'] += $row[csf("production_quantity")];
					} else if ($row[csf("production_type")] == 2 && $row[csf("embel_name")] == 1) {
						$tna_task_update_data[$row[csf("po_break_down_id")]][312]['max_start_date'] = $row[csf("maxd")];
						$tna_task_update_data[$row[csf("po_break_down_id")]][312]['min_start_date'] = $row[csf("mind")];
						$tna_task_update_data[$row[csf("po_break_down_id")]][312]['doneqnty'] += $row[csf("production_quantity")];
					} else if ($row[csf("production_type")] == 2 && $row[csf("embel_name")] == 2) {
						$tna_task_update_data[$row[csf("po_break_down_id")]][313]['max_start_date'] = $row[csf("maxd")];
						$tna_task_update_data[$row[csf("po_break_down_id")]][313]['min_start_date'] = $row[csf("mind")];
						$tna_task_update_data[$row[csf("po_break_down_id")]][313]['doneqnty'] += $row[csf("production_quantity")];
					}
				}
			}


			if ($row[csf("production_type")] == 4) $tsktype = 122;
			else if ($row[csf("production_type")] == 7) $tsktype = 87;
			else if ($row[csf("production_type")] == 8) $tsktype = 88;
			else if ($row[csf("production_type")] == 10) $tsktype = 87;
			else if ($row[csf("production_type")] == 11) $tsktype = 91;

			else if ($row[csf("embel_name")] == 3 && $row[csf("production_type")] == 2) $tsktype = 89;
			else if ($row[csf("embel_name")] == 3 && $row[csf("production_type")] == 3) $tsktype = 90;


			$tna_task_update_data[$row[csf("po_break_down_id")]][$tsktype]['max_start_date'] = $row[csf("maxd")];
			$tna_task_update_data[$row[csf("po_break_down_id")]][$tsktype]['min_start_date'] = $row[csf("mind")];
			$tna_task_update_data[$row[csf("po_break_down_id")]][$tsktype]['doneqnty'] += $row[csf("production_quantity")];
			$tna_task_update_data[$row[csf("po_break_down_id")]][$tsktype]['reqqnty'] = $po_order_details[$row[csf("po_break_down_id")]]['po_quantity_pcs'];
		}

		unset($result);


		//print_r($tna_task_update_data[69943][186]);die;


		//Yarn Daying---------------------------------------------


		$yrecSql = "SELECT c.PO_BREAKDOWN_ID, SUM (c.quantity) QTY,  MAX (a.receive_date) AS MAX_RECEIVE_DATE, MIN (a.receive_date) AS MIN_RECEIVE_DATE
		FROM inv_receive_master a
			INNER JOIN inv_transaction b ON a.id = b.mst_id
			INNER JOIN order_wise_pro_details c ON b.id = c.trans_id
			INNER JOIN product_details_master d ON c.prod_id = d.id
		WHERE c.trans_type = 1  AND b.item_category = 1  AND b.transaction_type = 1 AND a.status_active = 1 AND a.is_deleted = 0  AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 " . where_con_using_array($order_id_array, 0, 'c.po_breakdown_id') . "   GROUP BY c.po_breakdown_id";

		$yrecSqlRes = sql_select($yrecSql);
		foreach ($yrecSqlRes as $row) {
			$to_process_task[$row['PO_BREAKDOWN_ID']][52] = 52;
			$tna_task_update_data[$row['PO_BREAKDOWN_ID']][52]['max_start_date'] = $row['MAX_RECEIVE_DATE'];
			$tna_task_update_data[$row['PO_BREAKDOWN_ID']][52]['min_start_date'] = $row['MIN_RECEIVE_DATE'];

			$tna_task_update_data[$row['PO_BREAKDOWN_ID']][52]['doneqnty'] = $row['QTY'];
			$tna_task_update_data[$row['PO_BREAKDOWN_ID']][52]['reqqnty'] = $tna_task_update_data[$row['PO_BREAKDOWN_ID']][345]['reqqnty'];
		}



		//AOP Sent/AOP Receive........................................

		$sql = "select a.entry_form,b.order_id, max(a.receive_date) as  maxd, min(a.receive_date) as mind  from inv_receive_mas_batchroll  a, pro_grey_batch_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.entry_form in (91,92)  " . where_con_using_array($order_id_array, 0, 'b.order_id') . " group by a.entry_form,b.order_id";


		//echo $sql;die;

		$result = sql_select($sql);
		foreach ($result as $row) {
			if ($row[csf("entry_form")] == 91) $taskKey = 62;
			else if ($row[csf("entry_form")] == 92) $taskKey = 63;

			//$to_process_task[$row[csf("order_id")]][$taskKey]=$taskKey;
			$tna_task_update_data[$row[csf("order_id")]][$taskKey]['max_start_date'] = $row[csf("maxd")];
			$tna_task_update_data[$row[csf("order_id")]][$taskKey]['min_start_date'] = $row[csf("mind")];

			$tna_task_update_data[$row[csf("order_id")]][$taskKey]['doneqnty'] = 1;
			$tna_task_update_data[$row[csf("order_id")]][$taskKey]['reqqnty'] = 1;
		}


		//new...........................................start;


		$sql_task = "select min(a.REQUISITION_DATE) as start_date, max(a.REQUISITION_DATE) as end_date ,d.id as po_breakdown_id,e.sample_type,sum(b.sample_prod_qty) as sample_prod_qty from sample_development_mst a,sample_development_dtls b,wo_po_details_master c,wo_po_break_down d, lib_sample e where e.id=b.sample_name and a.quotation_id=c.id and c.job_no=d.job_no_mst and a.id=b.sample_mst_id and b.entry_form_id=117 and e.sample_type in(2,12,13) and e.BUSINESS_NATURE=2 and b.is_deleted=0 and b.status_active=1   " . where_con_using_array($order_id_array, 0, 'd.id') . " group by d.id ,e.sample_type order by d.id asc";


		$result = sql_select($sql_task);
		$purchase_update_task = array();
		foreach ($result as $row) {
			if ($row[csf("sample_type")] == 2) {
				$tsktype = 36;
			} else if ($row[csf("sample_type")] == 12) {
				$tsktype = 192;
			} else if ($row[csf("sample_type")] == 13) {
				$tsktype = 196;
			}
			//36=PPS Requsition;
			$tna_task_update_data[$row[csf("po_breakdown_id")]][$tsktype]['max_start_date'] = $row[csf("end_date")];
			$tna_task_update_data[$row[csf("po_breakdown_id")]][$tsktype]['min_start_date'] = $row[csf("start_date")];
			$tna_task_update_data[$row[csf("po_breakdown_id")]][$tsktype]['doneqnty'] = $row[csf("sample_prod_qty")];
			$tna_task_update_data[$row[csf("po_breakdown_id")]][$tsktype]['reqqnty'] = $row[csf("sample_prod_qty")];
		}



		$sql_task = "SELECT b.po_break_down_id,  sum(wo_qnty) as tfb_qnty, sum(b.amount/b.exchange_rate)  as tfb_amount, sum(grey_fab_qnty) as gfb_qnty, min(a.booking_date) as start_date, max(a.booking_date) as end_date, a.item_category,c.sample_type
		FROM  wo_booking_mst a, wo_booking_dtls b,lib_sample c WHERE c.id=b.sample_type and a.booking_no=b.booking_no and a.is_short=2 and c.sample_type=2 and c.BUSINESS_NATURE=2 and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  " . where_con_using_array($order_id_array, 0, 'b.po_break_down_id') . " group by b.po_break_down_id,item_category,c.sample_type order by b.po_break_down_id asc";


		$result = sql_select($sql_task);
		$purchase_update_task = array();
		foreach ($result as $row) {
			//142=PPS fab Booking;
			$tna_task_update_data[$row[csf("po_break_down_id")]][142]['max_start_date'] = $row[csf("end_date")];
			$tna_task_update_data[$row[csf("po_break_down_id")]][142]['min_start_date'] = $row[csf("start_date")];
			$tna_task_update_data[$row[csf("po_break_down_id")]][142]['doneqnty'] = $row[csf("gfb_qnty")];
			$tna_task_update_data[$row[csf("po_break_down_id")]][142]['reqqnty'] = $row[csf("gfb_qnty")];
		}
		unset($result);



		$sql = "select max(a.transaction_date) as end_date,min(a.transaction_date) as start_date,b.po_breakdown_id ,b.entry_form, sum(b.quantity) as finish_fabric_issue from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and b.entry_form in(18,19,46,15) and b.trans_type in(2,3,6) and a.transaction_type in(2,3,6) and a.prod_id=b.prod_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  " . where_con_using_array($order_id_array, 0, 'b.po_breakdown_id') . " group by b.po_breakdown_id,b.entry_form";


		$result = sql_select($sql);

		foreach ($result as $row) {
			$finish_fabric_issued = $row[csf('finish_fabric_issue')] + $row[csf('finish_fabric_trans_issued')] + $row[csf('recv_rtn_qnty')];

			if ($SolidPoArr[$row[csf("po_breakdown_id")]] != '') {
				//150=PPS Fab Issue;
				$tna_task_update_data[$row[csf("po_breakdown_id")]][150]['max_start_date'] = $row[csf("end_date")];
				$tna_task_update_data[$row[csf("po_breakdown_id")]][150]['min_start_date'] = $row[csf("start_date")];
				$tna_task_update_data[$row[csf("po_breakdown_id")]][150]['doneqnty'] += $finish_fabric_issued;
				//$tna_task_update_data[$row[csf("po_breakdown_id")]][150]['reqqnty']=$finish_fabric_issued;
			}

			if ($AOPPoArr[$row[csf("po_breakdown_id")]] != '' && $row[csf("entry_form")] != 19) {
				//168=PPS Fab iss Aop
				$tna_task_update_data[$row[csf("po_breakdown_id")]][168]['max_start_date'] = $row[csf("end_date")];
				$tna_task_update_data[$row[csf("po_breakdown_id")]][168]['min_start_date'] = $row[csf("start_date")];
				$tna_task_update_data[$row[csf("po_breakdown_id")]][168]['doneqnty'] += $finish_fabric_issued;
				//$tna_task_update_data[$row[csf("po_breakdown_id")]][168]['reqqnty']=$finish_fabric_issued;
			} else if ($YDPoArr[$row[csf("po_breakdown_id")]] != '' && $row[csf("entry_form")] != 19) {
				//169=PPS Fab Issu YD
				$tna_task_update_data[$row[csf("po_breakdown_id")]][169]['max_start_date'] = $row[csf("end_date")];
				$tna_task_update_data[$row[csf("po_breakdown_id")]][169]['min_start_date'] = $row[csf("start_date")];
				$tna_task_update_data[$row[csf("po_breakdown_id")]][169]['doneqnty'] += $finish_fabric_issued;
				//$tna_task_update_data[$row[csf("po_breakdown_id")]][169]['reqqnty']=$finish_fabric_issued;
			}
		}
		unset($sql);




		$sql = "select d.id as po_breakdown_id,b.sample_prod_qty,e.ex_factory_qty,min(e.delivery_date) as start_date,max(e.delivery_date) as end_date
	from sample_development_mst a,sample_development_dtls b,wo_po_details_master c,wo_po_break_down d ,sample_ex_factory_dtls e
	where 
	a.id=e.sample_development_id and a.quotation_id=c.id and c.job_no=d.job_no_mst and a.id=b.sample_mst_id and b.entry_form_id=117  and b.is_deleted=0 and b.status_active=1  " . where_con_using_array($order_id_array, 0, 'd.id') . "  group by d.id ,b.sample_prod_qty,e.ex_factory_qty order by d.id asc";



		$sample_delivery_arr = sql_select($sql);
		foreach ($sample_delivery_arr as $row) {
			if ($SolidPoArr[$row[csf("po_breakdown_id")]] != '' && $row[csf("po_breakdown_id")] != '') {
				//37=PPS Making
				$tna_task_update_data[$row[csf("po_breakdown_id")]][37]['max_start_date'] = $row[csf("end_date")];
				$tna_task_update_data[$row[csf("po_breakdown_id")]][37]['min_start_date'] = $row[csf("start_date")];
				$tna_task_update_data[$row[csf("po_breakdown_id")]][37]['doneqnty'] = $row[csf("ex_factory_qty")];
				$tna_task_update_data[$row[csf("po_breakdown_id")]][37]['reqqnty'] = $row[csf("sample_prod_qty")];

				//159=Size Set Making
				$tna_task_update_data[$row[csf("po_breakdown_id")]][159]['max_start_date'] = $row[csf("end_date")];
				$tna_task_update_data[$row[csf("po_breakdown_id")]][159]['min_start_date'] = $row[csf("start_date")];
				$tna_task_update_data[$row[csf("po_breakdown_id")]][159]['doneqnty'] = $row[csf("ex_factory_qty")];
				$tna_task_update_data[$row[csf("po_breakdown_id")]][159]['reqqnty'] = $row[csf("sample_prod_qty")];
			}
			if ($AOPPoArr[$row[csf("po_breakdown_id")]] != '' && $row[csf("po_breakdown_id")] != '') {
				//170=PPS Making AOP
				$tna_task_update_data[$row[csf("po_breakdown_id")]][170]['max_start_date'] = $row[csf("end_date")];
				$tna_task_update_data[$row[csf("po_breakdown_id")]][170]['min_start_date'] = $row[csf("start_date")];
				$tna_task_update_data[$row[csf("po_breakdown_id")]][170]['doneqnty'] = $row[csf("ex_factory_qty")];
				$tna_task_update_data[$row[csf("po_breakdown_id")]][170]['reqqnty'] = $row[csf("sample_prod_qty")];

				//182=Size Set Mk AOP
				$tna_task_update_data[$row[csf("po_breakdown_id")]][182]['max_start_date'] = $row[csf("end_date")];
				$tna_task_update_data[$row[csf("po_breakdown_id")]][182]['min_start_date'] = $row[csf("start_date")];
				$tna_task_update_data[$row[csf("po_breakdown_id")]][182]['doneqnty'] = $row[csf("ex_factory_qty")];
				$tna_task_update_data[$row[csf("po_breakdown_id")]][182]['reqqnty'] = $row[csf("sample_prod_qty")];
			}
			if ($YDPoArr[$row[csf("po_breakdown_id")]] != '' && $row[csf("po_breakdown_id")] != '') {
				//171=PPS Making YD
				$tna_task_update_data[$row[csf("po_breakdown_id")]][171]['max_start_date'] = $row[csf("end_date")];
				$tna_task_update_data[$row[csf("po_breakdown_id")]][171]['min_start_date'] = $row[csf("start_date")];
				$tna_task_update_data[$row[csf("po_breakdown_id")]][171]['doneqnty'] = $row[csf("ex_factory_qty")];
				$tna_task_update_data[$row[csf("po_breakdown_id")]][171]['reqqnty'] = $row[csf("sample_prod_qty")];

				//183=Size Set Mak YD
				$tna_task_update_data[$row[csf("po_breakdown_id")]][183]['max_start_date'] = $row[csf("end_date")];
				$tna_task_update_data[$row[csf("po_breakdown_id")]][183]['min_start_date'] = $row[csf("start_date")];
				$tna_task_update_data[$row[csf("po_breakdown_id")]][183]['doneqnty'] = $row[csf("ex_factory_qty")];
				$tna_task_update_data[$row[csf("po_breakdown_id")]][183]['reqqnty'] = $row[csf("sample_prod_qty")];
			}
		}
		unset($sample_delivery_arr);





		$program_sql = "select b.COLOR_TYPE_ID,b.BODY_PART_ID,b.PO_ID, sum(b.program_qnty) as QNTY from ppl_planning_entry_plan_dtls b where b.status_active=1 and b.is_deleted=0  " . where_con_using_array($order_id_array, 0, 'b.po_id') . " group by b.COLOR_TYPE_ID,b.BODY_PART_ID,b.PO_ID";

		// echo $program_sql;die;
		$program_sql_result = sql_select($program_sql);
		foreach ($program_sql_result as $row) {
			$program_qnty_array[$row['PO_ID']][$row['COLOR_TYPE_ID']] += $row['QNTY'];
			$color_type_in_program[$row['PO_ID']][$row['BODY_PART_ID']][$row['COLOR_TYPE_ID']] = $row['COLOR_TYPE_ID'];
		}






		$sql = "select b.BODY_PART_ID,c.po_breakdown_id,sum(c.qnty) as qty,min(a.receive_date) as start_date,max(a.receive_date) as end_date from inv_receive_master a,pro_grey_prod_entry_dtls b,pro_roll_details c where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id  and c.roll_used=1 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 " . where_con_using_array($order_id_array, 0, 'c.po_breakdown_id') . " group by b.BODY_PART_ID,c.PO_BREAKDOWN_ID
	UNION ALL
	select b.BODY_PART_ID,c.PO_BREAKDOWN_ID, SUM (c.QUANTITY) AS qty, MIN (a.receive_date) AS start_date,  MAX (a.receive_date) AS end_date from inv_receive_master a,PRO_GREY_PROD_ENTRY_DTLS b,ORDER_WISE_PRO_DETAILS c where a.id = b.MST_ID and b.TRANS_ID = c.TRANS_ID and a.IS_DELETED=0 and b.IS_DELETED=0 and b.IS_DELETED=0 and a.STATUS_ACTIVE=1 and b.STATUS_ACTIVE=1 and c.STATUS_ACTIVE=1 " . where_con_using_array($order_id_array, 0, 'c.po_breakdown_id') . " group by b.BODY_PART_ID,c.PO_BREAKDOWN_ID";
		//echo $sql;die;

		$sample_delivery_arr = sql_select($sql);
		foreach ($sample_delivery_arr as $row) {
			//$color_type_id=$color_type_in_program[$row[csf("po_breakdown_id")]][$row[csf("BODY_PART_ID")]];
			$color_type_id_arr = ($color_type_in_program[$row[csf("PO_BREAKDOWN_ID")]][$row[csf("BODY_PART_ID")]]) ? $color_type_in_program[$row[csf("PO_BREAKDOWN_ID")]][$row[csf("BODY_PART_ID")]] : $color_type_in_precost[$row[csf("PO_BREAKDOWN_ID")]][$row[csf("BODY_PART_ID")]];

			foreach ($color_type_id_arr as $color_type_id) {
				if (($color_type_id == 2) || ($color_type_id == 3) || ($color_type_id == 4) || ($color_type_id == 6) || ($color_type_id == 32) || ($color_type_id == 33)) {
					if ($YDPoArr[$row[csf("po_breakdown_id")]] != '') {
						//178 => Knitting production (YD)
						$tna_task_update_data[$row[csf("po_breakdown_id")]][178]['max_start_date'] = $row[csf("end_date")];
						$tna_task_update_data[$row[csf("po_breakdown_id")]][178]['min_start_date'] = $row[csf("start_date")];
						$tna_task_update_data[$row[csf("po_breakdown_id")]][178]['doneqnty'] = $row[csf("qty")];
						//$tna_task_update_data[$row[csf("po_breakdown_id")]][178]['reqqnty']=$program_qnty_array[$row[csf("po_breakdown_id")]][$color_type_id];
					}
				} else if (($color_type_id == 1) || ($color_type_id == 20) || ($color_type_id == 25) || ($color_type_id == 26) || ($color_type_id == 27) || ($color_type_id == 28) || ($color_type_id == 29) || ($color_type_id == 30) || ($color_type_id == 31) || ($color_type_id == 34)  || ($color_type_id == 35)  || ($color_type_id == 36)  || ($color_type_id == 37) || ($color_type_id == '')) {
					if ($SolidPoArr[$row[csf("po_breakdown_id")]] != '') {
						//212 => Knitting Production Solid
						$tna_task_update_data[$row[csf("po_breakdown_id")]][212]['max_start_date'] = $row[csf("end_date")];
						$tna_task_update_data[$row[csf("po_breakdown_id")]][212]['min_start_date'] = $row[csf("start_date")];
						$tna_task_update_data[$row[csf("po_breakdown_id")]][212]['doneqnty'] += $row[csf("qty")];
						//$tna_task_update_data[$row[csf("po_breakdown_id")]][212]['reqqnty']=$program_qnty_array[$row[csf("po_breakdown_id")]][$color_type_id];
					}
				} else if (($color_type_id == 5) || ($color_type_id == 7)) {
					if ($AOPPoArr[$row[csf("po_breakdown_id")]] != '') {
						//323 => Knitting Production AOP
						$tna_task_update_data[$row[csf("po_breakdown_id")]][323]['max_start_date'] = $row[csf("end_date")];
						$tna_task_update_data[$row[csf("po_breakdown_id")]][323]['min_start_date'] = $row[csf("start_date")];
						$tna_task_update_data[$row[csf("po_breakdown_id")]][323]['doneqnty'] = $row[csf("qty")];
						//$tna_task_update_data[$row[csf("po_breakdown_id")]][323]['reqqnty']=$program_qnty_array[$row[csf("po_breakdown_id")]][$color_type_id];

						if ($program_qnty_array[$row[csf("po_breakdown_id")]][$color_type_id]) {
							$tna_task_update_data[$row[csf("po_breakdown_id")]][323]['reqqnty'] = $program_qnty_array[$row[csf("po_breakdown_id")]][$color_type_id];
						}
					}
				}
			}
		}
		unset($sample_delivery_arr);


		$sql = "select b.BODY_PART_ID,c.po_breakdown_id,sum(c.qnty) as reqqnty, sum(c.qc_pass_qnty) as doneqnty, 0 as returnable_qnty,min(a.receive_date) as start_date,max(a.receive_date) as end_date from inv_receive_master a,pro_finish_fabric_rcv_dtls b,pro_roll_details c where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id  and c.entry_form=68 and c.status_active=1 and c.is_deleted=0  " . where_con_using_array($order_id_array, 0, 'c.po_breakdown_id') . " group by b.BODY_PART_ID,c.po_breakdown_id
		union all 
		select b.BODY_PART_ID,a.po_breakdown_id,sum(0) as reqqnty, sum(a.quantity) as doneqnty, sum(a.returnable_qnty) as returnable_qnty,min(c.receive_date) as start_date,max(c.receive_date) as end_date from order_wise_pro_details a,pro_finish_fabric_rcv_dtls b,inv_receive_master c where a.dtls_id=b.id and b.mst_id=c.id " . where_con_using_array($order_id_array, 0, 'a.po_breakdown_id') . "and a.entry_form in(37,7)  and a.status_active=1 and a.is_deleted=0 group by b.BODY_PART_ID,a.po_breakdown_id";
		// echo $sql;die;



		$sample_delivery_arr = sql_select($sql);
		foreach ($sample_delivery_arr as $row) {

			$color_type_id = $color_type_in_program[$row[csf("po_breakdown_id")]][$row[csf("BODY_PART_ID")]];

			if (($color_type_id == 2) || ($color_type_id == 3) || ($color_type_id == 4) || ($color_type_id == 6) || ($color_type_id == 32) || ($color_type_id == 33)) {
				if ($YDPoArr[$row[csf("po_breakdown_id")]] != '') {
					//180=Fin Fab Recv YD
					$tna_task_update_data[$row[csf("po_breakdown_id")]][180]['max_start_date'] = $row[csf("end_date")];
					$tna_task_update_data[$row[csf("po_breakdown_id")]][180]['min_start_date'] = $row[csf("start_date")];
					$tna_task_update_data[$row[csf("po_breakdown_id")]][180]['doneqnty'] = $row[csf("doneqnty")] - $row[csf("returnable_qnty")];
					//$tna_task_update_data[$row[csf("po_breakdown_id")]][180]['reqqnty']=$row[csf("po_breakdown_id")];
				}
			} else if (($color_type_id == 1) || ($color_type_id == 20) || ($color_type_id == 25) || ($color_type_id == 26) || ($color_type_id == 27) || ($color_type_id == 28) || ($color_type_id == 29) || ($color_type_id == 30) || ($color_type_id == 31) || ($color_type_id == 34)  || ($color_type_id == 35)  || ($color_type_id == 36)  || ($color_type_id == 37) || ($color_type_id == '')) {
			} else if (($color_type_id == 5) || ($color_type_id == 7)) {
				if ($AOPPoArr[$row[csf("po_breakdown_id")]] != '') {
					//179=FIn Fab Rcv AOP
					$tna_task_update_data[$row[csf("po_breakdown_id")]][179]['max_start_date'] = $row[csf("end_date")];
					$tna_task_update_data[$row[csf("po_breakdown_id")]][179]['min_start_date'] = $row[csf("start_date")];
					$tna_task_update_data[$row[csf("po_breakdown_id")]][179]['doneqnty'] = $row[csf("doneqnty")] - $row[csf("returnable_qnty")];;
					//$tna_task_update_data[$row[csf("po_breakdown_id")]][179]['reqqnty']=$row[csf("reqqnty")];
				}
			}
		}
		unset($sample_delivery_arr);
		//new...........................................end;


		//LC/SC Receive...........................................start;

		$po_con = where_con_using_array($order_id_array, 0, 'b.wo_po_break_down_id');

		$sql = "select max(a.contract_date) as max_date,min(a.contract_date) as min_date,b.wo_po_break_down_id,sum(b.attached_qnty) as qty  from com_sales_contract a,com_sales_contract_order_info b where a.id=b.com_sales_contract_id $po_con  and a.convertible_to_lc in(2,3) group by b.wo_po_break_down_id
	union all
	select  max(a.lc_date) as max_date,min(a.lc_date) as min_date,b.wo_po_break_down_id,sum(b.attached_qnty) as qty  from com_export_lc a,com_export_lc_order_info b where a.id=b.com_export_lc_id $po_con group by b.wo_po_break_down_id";

		$lc_sc_receive_arr = sql_select($sql);
		foreach ($lc_sc_receive_arr as $row) {
			$tna_task_update_data[$row[csf("wo_po_break_down_id")]][153]['max_start_date'] = $row[csf("max_date")];
			$tna_task_update_data[$row[csf("wo_po_break_down_id")]][153]['min_start_date'] = $row[csf("min_date")];
			$tna_task_update_data[$row[csf("wo_po_break_down_id")]][153]['doneqnty'] += $row[csf("qty")];
			$tna_task_update_data[$row[csf("po_breakdown_id")]][153]['reqqnty'] = $po_order_details[$row[csf("po_breakdown_id")]]['po_quantity'];
			$to_process_task[$row[csf("po_breakdown_id")]][153] = 153;
		}
		unset($lc_sc_receive_arr);

		//LC/SC Receive...........................................end;




		//Knit precost app...........................................start;

		$pre_cost_sql = "select a.APPROVED,b.id as PO_ID,max(c.APPROVED_DATE) as MAX_DATE,MIN(c.APPROVED_DATE) as MIN_DATE from WO_PRE_COST_MST a,WO_PO_BREAK_DOWN b,APPROVAL_HISTORY c where a.job_no=b.job_no_mst and a.id=c.mst_id and c.entry_form=15  and a.IS_DELETED=0 and a.STATUS_ACTIVE =1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1 and a.APPROVED>0 " . where_con_using_array($order_id_array, 0, 'b.id') . " GROUP BY a.APPROVED,b.id
union all 
select a.APPROVED,b.id as PO_ID,max(c.APPROVED_DATE) as MAX_DATE,MIN(c.APPROVED_DATE) as MIN_DATE from WO_PRE_COST_MST a,WO_PO_BREAK_DOWN b,co_com_pre_costing_app_his c where a.job_no=b.job_no_mst and a.id=c.mst_id and c.entry_form=11  and a.IS_DELETED=0 and a.STATUS_ACTIVE =1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1 and a.APPROVED>0 " . where_con_using_array($order_id_array, 0, 'b.id') . " GROUP BY a.APPROVED,b.id";

		$pre_cost_sql_arr = sql_select($pre_cost_sql);
		foreach ($pre_cost_sql_arr as $row) {
			$tna_task_update_data[$row['PO_ID']][346]['max_start_date'] = date("d-M-Y", strtotime($row['MAX_DATE']));
			$tna_task_update_data[$row['PO_ID']][346]['min_start_date'] = date("d-M-Y", strtotime($row['MIN_DATE']));
			$tna_task_update_data[$row['PO_ID']][346]['doneqnty'] = ($row['APPROVED'] == 1) ? 1 : 0;
			$tna_task_update_data[$row['PO_ID']][346]['reqqnty'] = 1;
			$to_process_task[$row['PO_ID']][346] = 346;
		}
		unset($pre_cost_sql_arr);



		$pre_cost_sql = "select b.id as PO_ID,max(a.APPROVED_DATE) as MAX_DATE,MIN(a.APPROVED_DATE) as MIN_DATE from WO_PRE_COST_MST a,WO_PO_BREAK_DOWN b where a.job_no=b.job_no_mst and a.IS_DELETED=0 and a.STATUS_ACTIVE =1 and a.READY_TO_APPROVED=1 and a.APPROVED=1  and b.IS_DELETED=0 and b.STATUS_ACTIVE=1 " . where_con_using_array($order_id_array, 0, 'b.id') . " GROUP BY b.id";
		//echo $pre_cost_sql;die;

		$pre_cost_sql_arr = sql_select($pre_cost_sql);
		foreach ($pre_cost_sql_arr as $row) {
			if ($row['MAX_DATE'] != '') {
				$tna_task_update_data[$row['PO_ID']][322]['max_start_date'] = date("d-M-Y", strtotime($row['MAX_DATE']));
			}
			if ($row['MIN_DATE'] != '') {
				$tna_task_update_data[$row['PO_ID']][322]['min_start_date'] = date("d-M-Y", strtotime($row['MIN_DATE']));
			}

			$tna_task_update_data[$row['PO_ID']][322]['doneqnty'] = 1;
			$tna_task_update_data[$row['PO_ID']][322]['reqqnty'] = 1;
			$to_process_task[$row['PO_ID']][322] = 322;
		}
		unset($pre_cost_sql_arr);



		//print_r($tna_task_update_data[76276][322]);die;
		//Knit precost app...........................................end;


		//$tna_task_seq_arr[$row[csf('task_name')]]=$row[csf('task_sequence_no')];


		foreach ($po_order_details as $row)  // Non Process Starts Here
		{

			foreach ($tna_task_seq_arr as $taskId => $seq) {
				if ($row['is_confirmed'] == 1) //Confirmed
				{
					if ($seq >= $tna_task_seq_arr[$row['tna_task_from_upto']]) //tna_task_from_upto=task id
					{
						//$to_process_task[$row[po_id]][$taskId]=$taskId;
					} else {
						unset($to_process_task[$row['po_id']][$taskId]);
					}
				} else if ($row['is_confirmed'] == 2) // Projected
				{
					if ($row['tna_task_from_upto'] != 0) {
						if ($seq <= $tna_task_seq_arr[$row['tna_task_from_upto']]) //tna_task_from_upto=task id
						{
							//$to_process_task[$row[po_id]][$taskId]=$taskId;
						} else {
							unset($to_process_task[$row['po_id']][$taskId]);
						}
					}
					//else $to_process_task[$row[po_id]][$vid]=$vid;
				}
			}



			//unset some task proces if fabric source purchase;
			if ($precost_fabric_source_by_po_arr[$row['po_id']] == 2) {
				foreach (array(48, 50, 60, 72, 61, 9, 10, 212, 73, 179, 323) as $vid) {
					unset($to_process_task[$row['po_id']][$vid]);
				}
			}

			//forain/local......................
			foreach (array(178, 212, 323, 73, 179, 180, 86, 191, 190, 84, 186, 187, 80, 184, 185) as $vid) {
				if ($tna_task_update_data[$row['po_id']][$vid]['reqqnty'] == 0) {
					unset($to_process_task[$row['po_id']][$vid]);
				}
			}
		}







		//print_r($tna_task_update_data[54724][277]);die;//51659,51660		
		//---------------------------------------------------------------------------------------------------------
		$insert_string = array();
		$data_array_tna_process_up = array();
		$process_id_up_array = array();
		
		//$shipment_date = ($is_pub_shipment_date == 1) ? "pub_shipment_date as shipment_date" : "shipment_date";

		$field_array_tna_process = "id,template_id,job_no,po_number_id,po_receive_date,shipment_date,task_category,task_number,target_date,task_start_date,task_finish_date,notice_date_start,notice_date_end,process_date,sequence_no,plan_start_flag,plan_finish_flag,status_active,is_deleted,task_type";
		$field_array_tna_process_up = "actual_start_date*actual_finish_date";

		//$approval_array=array(7=>7,8=>8,9=>9,10=>10,11=>11,12=>12,13=>13,14=>14,15=>15,16=>16,17=>17,19=>19,20=>20,197=>197,198=>198,194=>194,21=>21,22=>22,23=>23,24=>24,28=>28,29=>29,265=>265,266=>266);


		$approval_array = array(9 => 9, 10 => 10, 11 => 11, 19 => 19, 20 => 20);


		foreach ($po_order_details as $row)  // Non Process Starts Here
		{
			//echo $row[template_id];die;
			foreach ($template_wise_task[$row['template_id']]  as $task_id => $row_task) {

				// $row_task['completion_percent'] = ($row_task['completion_percent']) ? $row_task['completion_percent'] : 100;
				$row_task['completion_percent'] = 1;

				if ($to_process_task[$row['po_id']][$row_task['task_name']] != "") {

					if ($tna_process_type == 1) {
						if ($db_type == 0) $target_date = add_date($row['shipment_date'], -$row_task['deadline']);
						else $target_date = change_date_format(trim(add_date($row['shipment_date'], -$row_task['deadline'])), '', '', 1);

						$to_add_days = $row_task['execution_days'] - 1;
						if ($db_type == 0) $start_date = add_date($target_date, -$to_add_days);
						else $start_date = change_date_format(trim(add_date($target_date, -$to_add_days)), '', '', 1);

						$finish_date = $target_date;
						$to_add_days = $row_task['notice_before'];

						if ($db_type == 0) $notice_date_start = add_date($start_date, -$to_add_days);
						else $notice_date_start = change_date_format(trim(add_date($start_date, -$to_add_days)), '', '', 1);


						if ($db_type == 0) $notice_date_end = add_date($finish_date, -$to_add_days);
						else $notice_date_end = change_date_format(trim(add_date($finish_date, -$to_add_days)), '', '', 1);
					} else {

						if ($db_type == 0) $target_date = add_date($row['po_received_date'], $row_task['execution_days']);
						else $target_date = change_date_format(trim(add_date($row['po_received_date'], $row_task['execution_days'])), '', '', 1);

						if ($db_type == 0) $start_date = add_date($row['po_received_date'], $row_task['deadline']);
						else $start_date = change_date_format(trim(add_date($row['po_received_date'], $row_task['deadline'])), '', '', 1);

						$finish_date = $target_date;
						$to_add_days = $row_task['notice_before'];

						if ($db_type == 0) $notice_date_start = add_date($start_date, -$to_add_days);
						else $notice_date_start = change_date_format(trim(add_date($start_date, -$to_add_days)), '', '', 1);


						if ($db_type == 0) $notice_date_end = add_date($finish_date, -$to_add_days);
						else $notice_date_end = change_date_format(trim(add_date($finish_date, -$to_add_days)), '', '', 1);
					}


					//$new_target_data[$row[po_id]][60]['st_date']=$start_date;
					//$new_target_data[$row[po_id]][60]['end_date']=$finish_date;



					if ($tna_process_list[$row['po_id']][$row_task['task_name']] == "") {
						if ($mst_id == "") $mst_id = return_next_id("id", "tna_process_mst");
						else $mst_id += 1;
						if ($data_array_tna_process != "") $data_array_tna_process .= ",";


						if ($tna_updated_date[$row['po_id']][$row_task['task_name']]['planstart'] == '0000-00-00') $tna_updated_date[$row['po_id']][$row_task['task_name']]['planstart'] = '';
						if ($tna_updated_date[$row['po_id']][$row_task['task_name']]['planfinish'] == '0000-00-00') $tna_updated_date[$row['po_id']][$row_task['task_name']]['planfinish'] = '';

						if ($tna_updated_date[$row['po_id']][$row_task['task_name']]['planstart'] != '') $start_date = $tna_updated_date[$row['po_id']][$row_task['task_name']]['planstart'];
						if ($tna_updated_date[$row['po_id']][$row_task['task_name']]['planfinish'] != '') $finish_date = $tna_updated_date[$row['po_id']][$row_task['task_name']]['planfinish'];


						$plan_start_flag = $tna_updated_date[$row['po_id']][$row_task['task_name']]['planstartflag'] * 1;
						$plan_finish_flag = $tna_updated_date[$row['po_id']][$row_task['task_name']]['planfinishflag'] * 1;


						
						$data_array_tna_process .= "('$mst_id','$row[template_id]','$row[job_no_mst]','$row[po_id]','$row[po_received_date]','$row[shipment_date]','1','".$row_task['task_name']."','$target_date','$start_date','$finish_date','$notice_date_start','$notice_date_end','$date','$row_task[sequence_no]',$plan_start_flag,$plan_finish_flag,1,0,1)";

						$insert_string[] = "('$mst_id','$row[template_id]','$row[job_no_mst]','$row[po_id]','$row[po_received_date]','$row[shipment_date]','1','".$row_task['task_name']."','$target_date','$start_date','$finish_date','$notice_date_start','$notice_date_end','$date','$row_task[sequence_no]',$plan_start_flag,$plan_finish_flag,1,0,1)";
					} else {


						if (($tna_task_update_data[$row['po_id']][$row_task['task_name']]['min_start_date'] == "0000-00-00" || $tna_task_update_data[$row['po_id']][$row_task['task_name']]['min_start_date'] == "") && ($tna_task_update_data[$row['po_id']][$row_task['task_name']]['max_start_date'] != "0000-00-00" || $tna_task_update_data[$row['po_id']][$row_task['task_name']]['max_start_date'] != "")) {
							$tna_task_update_data[$row['po_id']][$row_task['task_name']]['min_start_date'] = $tna_task_update_data[$row['po_id']][$row_task['task_name']]['max_start_date'];
						}

						if ($tna_task_update_data[$row['po_id']][$row_task['task_name']]['min_start_date'] != "0000-00-00" || $tna_task_update_data[$row['po_id']][$row_task['task_name']]['min_start_date'] != "") $start_date = $tna_task_update_data[$row['po_id']][$row_task['task_name']]['min_start_date'];
						else $start_date = "0000-00-00";
						if ($tna_task_update_data[$row['po_id']][$row_task['task_name']]['max_start_date'] != "0000-00-00" || $tna_task_update_data[$row['po_id']][$row_task['task_name']]['max_start_date'] != "") $finish_date = $tna_task_update_data[$row['po_id']][$row_task['task_name']]['max_start_date'];
						else $finish_date = "0000-00-00";

						if ($approval_array[$row_task['task_name']] == '') {

							$compl_perc = get_percent($tna_task_update_data[$row['po_id']][$row_task['task_name']]['doneqnty'], $tna_task_update_data[$row['po_id']][$row_task['task_name']]['reqqnty']);

							if ($compl_perc < $row_task['completion_percent']) {
								$finish_date = $blank_date;
							}
						} else {
							if ($tna_task_update_data[$row['po_id']][$row_task['task_name']]['noofapproved'] != $tna_task_update_data[$row['po_id']][$row_task['task_name']]['noofval']) $finish_date = $blank_date; //"0000-00-00";


						}


						$process_id_up_array[] = $tna_process_list[$row['po_id']][$row_task['task_name']];

						if ($tna_updated_date[$row['po_id']][$row_task['task_name']]['start'] == '0000-00-00') $tna_updated_date[$row['po_id']][$row_task['task_name']]['start'] = '';
						if ($tna_updated_date[$row['po_id']][$row_task['task_name']]['finish'] == '0000-00-00') $tna_updated_date[$row['po_id']][$row_task['task_name']]['finish'] = '';

						if ($tna_updated_date[$row['po_id']][$row_task['task_name']]['start'] != '') $start_date = $tna_updated_date[$row['po_id']][$row_task['task_name']]['start'];
						if ($tna_updated_date[$row['po_id']][$row_task['task_name']]['finish'] != '') $finish_date = $tna_updated_date[$row['po_id']][$row_task['task_name']]['finish'];

						$data_array_tna_process_up[$tna_process_list[$row['po_id']][$row_task['task_name']]] = explode(",", ("'" . $start_date . "','" . $finish_date . "'"));
					}
				} // To Process Task List check
			}
		}

		$file = 'knit_tna_log.txt';
		$current = file_get_contents($file);
		$current .= "TNA-PROCESS:: Company ID: ".$cbo_company.", Date and Time: ".date("d-m-Y h:i:s a",time())."\n";
		file_put_contents($file, $current);

		//Company wise insert and update process......................
		$con = connect();


		if ($db_type == 1 || $db_type == 2) {
			if ($data_array_tna_process != "") {
				$tna_pro_array = array_chunk($insert_string, 2);
				foreach ($tna_pro_array as $dd => $tna_pro_list) {
					$rID = sql_insert("tna_process_mst", $field_array_tna_process, implode(",", $tna_pro_list), 1);
					oci_commit($con);
				}
			}

			if (count($process_id_up_array) > 0) {
				$data_array_tna_up = array_chunk($data_array_tna_process_up, 50, true);
				$id_up_array = array_chunk($process_id_up_array, 50);
				$count = count($id_up_array);
				for ($i = 0; $i < $count; $i++) {

					$rID_up = execute_query(bulk_update_sql_statement("tna_process_mst", "id", $field_array_tna_process_up, $data_array_tna_up[$i], $id_up_array[$i]), 1);
				}
			}
		}

		oci_commit($con);
		disconnect($con);

		unset($insert_string);
		unset($process_id_up_array);
		unset($data_array_tna_process_up);
		unset($data_array_tna_process);
		unset($po_order_details);
	}//po chunk end;
	}


	//.....................................................................................


	echo "0**" . (($rID) ? $rID : $rID_up) . "**" . implode(", ", $template_missing_po);
	//echo "INSERT INTO pro_garments_production_dtls (".$field_array.") VALUES ".$data_array;die;

	disconnect($con);
} //end tna_process;




function get_tna_template($remain_days, $tna_template, $buyer, $company_id)
{
	global $tna_template_company_arr;
	global $tna_template_buyer;

	//print_r($tna_template_company_arr[$company_id][$buyer]);die;

	if (count($tna_template_company_arr[$company_id][$buyer]) > 0) {
		$n = count($tna_template_company_arr[$company_id][$buyer]);
		for ($i = 0; $i < $n; $i++) {
			if ($remain_days < $tna_template_company_arr[$company_id][$buyer][$i]['lead']) {
				if ($i != 0)
					return $tna_template_company_arr[$company_id][$buyer][$i - 1]['id'];
				else
					return "0";
			} else if ($remain_days == $tna_template_company_arr[$company_id][$buyer][$i]['lead']) {
				return $tna_template_company_arr[$company_id][$buyer][$i]['id'];
			} else if ($remain_days > $tna_template_company_arr[$company_id][$buyer][$i]['lead'] &&  $i == $n - 1) {
				return $tna_template_company_arr[$company_id][$buyer][$i]['id'];
			}
		}
	} else if (count($tna_template_buyer[$buyer]) > 0) {
		$n = count($tna_template_buyer[$buyer]);
		for ($i = 0; $i < $n; $i++) {
			if ($remain_days < $tna_template_buyer[$buyer][$i]['lead']) {
				if ($i != 0)
					return $tna_template_buyer[$buyer][$i - 1]['id'];
				else
					return "0";
			} else if ($remain_days == $tna_template_buyer[$buyer][$i]['lead']) {
				return $tna_template_buyer[$buyer][$i]['id'];
			} else if ($remain_days > $tna_template_buyer[$buyer][$i]['lead'] &&  $i == $n - 1) {
				return $tna_template_buyer[$buyer][$i]['id'];
			}
		}
	} else {

		$n = count($tna_template);
		for ($i = 0; $i < $n; $i++) {
			if ($remain_days < $tna_template[$i]['lead']) {
				if ($i != 0)
					return $tna_template[$i - 1]['id'];
				else
					return "0";
			} else if ($remain_days == $tna_template[$i]['lead']) {
				return $tna_template[$i]['id'];
			} else if ($remain_days > $tna_template[$i]['lead'] &&  $i == $n - 1) {
				return $tna_template[$i]['id'];
			}
		}
	}
}


function get_percent($completed, $actual)
{
	//return number_format((($completed*100)/$actual),0);
	return number_format((($completed * 100) / $actual), 0, '', '');
}

if ($action == "search_po_number") {
	echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>

	<script>
		var selected_id = new Array();
		var selected_name = new Array();

		function check_all_data(totalRow) {

			if (document.getElementById('check_all').checked) {
				var returnFlag = 0;
				for (var i = 1; i < totalRow; i++) {
					if (document.getElementById('tr_' + $('#po_' + i).val()).style.backgroundColor != 'yellow') {
						js_set_value($('#po_' + i).val());
						returnFlag = 1;
					}
				}
				if (returnFlag == 1) {
					return;
				}
			} else {
				var returnFlag = 0;
				for (var i = 1; i < totalRow; i++) {
					if (document.getElementById('tr_' + $('#po_' + i).val()).style.backgroundColor == 'yellow') {
						js_set_value($('#po_' + i).val());
						returnFlag = 1;
					}
				}
				if (returnFlag == 1) {
					return;
				}
			}


			for (var i = 1; i < totalRow; i++) {
				js_set_value($('#po_' + i).val());
			}

		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function js_set_value(po_id) {
			po_id = po_id * 1;
			if ($('#tr_' + po_id).is(':visible')) {

				var po_no = $('#tr_' + po_id).find("td p").eq(1).html();

				toggle(document.getElementById('tr_' + po_id), '#E9F3FF');

				if (jQuery.inArray(po_id, selected_id) == -1) {
					selected_id.push(po_id);
					selected_name.push(po_no);

				} else {
					for (var i = 0; i < selected_id.length; i++) {
						if (selected_id[i] == po_id) break;
					}
					selected_id.splice(i, 1);
					selected_name.splice(i, 1);
				}

				var id = ''
				var name = '';
				for (var i = 0; i < selected_id.length; i++) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ',';
				}
				id = id.substr(0, id.length - 1);
				name = name.substr(0, name.length - 1);

				$('#txt_selected_id').val(id);
				$('#txt_selected_name').val(name);


				var totalRow = $('#tbl_list_search tbody tr:visible').length - 1;
				if (selected_id.length == totalRow) {
					document.getElementById("check_all").checked = true;
				} else {
					document.getElementById("check_all").checked = false;
				}
			}

		}

		function generate_list_view(dataStr) {
			var dataArr = dataStr.split('_');
			var poNoSplitArr = dataArr[7].split('+');
			dataArr[7] = (poNoSplitArr.join('*'));
			show_list_view(dataArr[0] + '_' + dataArr[1] + '_' + dataArr[2] + '_' + dataArr[3] + '_' + dataArr[4] + '_' + dataArr[5] + '_' + dataArr[6] + '_' + dataArr[7] + '_' + dataArr[8] + '_' + dataArr[9], 'ponumber_search_list_view', 'search_div', 'tna_process_on_close_order_controller', 'setFilterGrid(\'tbl_list_search\',-1)');
		}
	</script>


	</head>

	<body>
		</head>

		<div align="center" style="width:100%;">
			<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
					<tr>
						<td align="center">
							<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
								<thead>
									<th colspan="4"></th>
									<th>
										<?
										echo create_drop_down("cbo_string_search_type", 110, $string_search_type, '', 1, "-- Searching Type --");
										?>
									</th>
									<th colspan="4"></th>
								</thead>
								<thead>
									<th width="100">Company Name</th>
									<th width="100">Buyer Name</th>
									<th width="80">Job No</th>
									<th width="100">Style Ref </th>
									<th width="100">Internal Ref.</th>
									<th width="120">Order No</th>
									<th width="170">Ship Date Range</th>
									<th><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">Job Without PO</th>
								</thead>

								<tr>
									<td>
										<input type="hidden" id="garments_nature" value="<? echo $garments_nature; ?>">
										<?
										echo create_drop_down("cbo_company_mst", 100, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name", "id,company_name", 1, "-- Select Company --", $company, "load_drop_down( 'tna_process_on_close_order_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
										?>
									</td>
									<td id="buyer_td">
										<?
										echo create_drop_down("cbo_buyer_name", 100, $blank_array, '', 1, "-- Select Buyer --");
										?>
									</td>
									<td align="center"><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:80px"></td>
									<td align="center"><input name="txt_style" id="txt_style" class="text_boxes" style="width:100px"></td>
									<td align="center"><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:100px"></td>
									<td align="center"><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:120px"></td>
									<td align="center">
										<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
										<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
									</td>
									<td align="center">
										<!--<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value, 'ponumber_search_list_view', 'search_div', 'tna_process_on_close_order_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" />-->


										<input type="button" name="button2" class="formbutton" value="Show" onClick="generate_list_view( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_internal_ref').value)" style="width:100px;" />

									</td>
								</tr>

								<tr>
									<td colspan="8" align="center">
										<?
										echo create_drop_down("cbo_year_selection", 70, $year, "", 1, "-- Select --", date('Y'), "", 0);
										echo load_month_buttons();
										?>

										<input type="hidden" id="txt_selected_id">
										<input type="hidden" id="txt_selected_name">

									</td>
								</tr>


							</table>
						</td>
					</tr>
					<tr>
						<td id="search_div"></td>
					</tr>
				</table>
			</form>
		</div>

	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		var buyer = '<? echo $buyer; ?>';
		load_drop_down('tna_process_on_close_order_controller', <? echo $company; ?>, 'load_drop_down_buyer', 'buyer_td');
		document.getElementById('cbo_buyer').value = buyer;
	</script>

	</html>
<?
	exit();
}

if ($action == "ponumber_search_list_view") {

	list($company, $buyer, $start_date, $end_date, $job_no, $year, $surch_by, $order_no, $style_no, $internal_ref) = explode('_', $data);
	$order_no = str_replace('*', '+', $order_no);

	$company_array = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	$buyer_array = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');

	if ($buyer == 0 && $style_no == '' && $job_no == '' && $order_no == '' && str_replace("'", "", $start_date) == '') {
		echo "<h1>Please Select Ship Date</h1>";
		exit();
	}

	if ($buyer != 0) $buyer_con = "and a.buyer_name='$buyer'";
	else $buyer_con = "";

	if ($surch_by == 1) {
		if ($job_no != "") $job_no_con = "and a.job_no='" . trim($job_no) . "'";
		else $job_no_con = "";
		if ($order_no != "") $order_no_con = "and b.po_number='" . trim($order_no) . "'";
		else $order_no_con = "";
		if ($style_no != "") $style_no_con = "and a.style_ref_no='" . trim($style_no) . "'";
		else $style_no_con = "";
		if ($internal_ref != "") {
			$style_no_con .= "and b.GROUPING='" . trim($internal_ref) . "'";
		}
	} else if ($surch_by == 2) {
		if ($job_no != "") $job_no_con = "and a.job_no like '" . trim($job_no) . "%'";
		else $job_no_con = "";
		if ($order_no != "") $order_no_con = "and b.po_number like '" . trim($order_no) . "%'";
		else $order_no_con = "";
		if ($style_no != "") $style_no_con = "and a.style_ref_no like '" . trim($style_no) . "%'";
		else $style_no_con = "";
		if ($internal_ref != "") $style_no_con .= "and b.GROUPING like '" . trim($internal_ref) . "%'";
	} else if ($surch_by == 3) {
		if ($job_no != "") $job_no_con = "and a.job_no like '%" . trim($job_no) . "'";
		else $job_no_con = "";
		if ($order_no != "") $order_no_con = "and b.po_number like '%" . trim($order_no) . "'";
		else $order_no_con = "";
		if ($style_no != "") $style_no_con = "and a.style_ref_no like '%" . trim($style_no) . "'";
		else $style_no_con = "";
		if ($internal_ref != "") $style_no_con .= "and b.GROUPING like '%" . trim($internal_ref) . "'";
	} else if ($surch_by == 4 || $surch_by == 0) {
		if ($job_no != "") $job_no_con = "and a.job_no like '%" . trim($job_no) . "%'";
		else $job_no_con = "";
		if ($order_no != "") $order_no_con = "and b.po_number like '%" . trim($order_no) . "%'";
		else $order_no_con = "";
		if ($style_no != "") $style_no_con = "and a.style_ref_no like '%" . trim($style_no) . "%'";
		else $style_no_con = "";
		if ($internal_ref != "") $style_no_con .= "and b.GROUPING like '%" . trim($internal_ref) . "%'";
	}


	$start_date = str_replace("'", "", $start_date);
	$end_date = str_replace("'", "", $end_date);
	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond  = " and b.pub_shipment_date between'" . change_date_format($start_date, "yyyy-mm-dd", "-") . "' and '" . change_date_format($end_date, "yyyy-mm-dd", "-") . "'";
		}

		if ($db_type == 2) {
			$date_cond  = " and b.pub_shipment_date between'" . change_date_format($start_date, "yyyy-mm-dd", "-", 1) . "' and '" . change_date_format($end_date, "yyyy-mm-dd", "-", 1) . "'";
		}
	} else {
		$date_cond  = "";
	}
?>
	<table width="930" align="left" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="">
		<thead>
			<th width="30" height="34">SL</th>
			<th width="100">Company Name</th>
			<th width="130">Buyer Name</th>
			<th width="130">PO Number</th>
			<th width="130">Style Ref.</th>
			<th width="100">Internal Ref.</th>
			<th width="70">Job No</th>
			<th width="70">Po Receive Date</th>
			<th width="70">Publish Shipment Date</th>
			<th>Lead Time</th>
		</thead>
	</table>
	<div style="width:950px; max-height:230px; float:left; overflow-y:scroll">
		<table width="930" align="left" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_search">
			<?
			if ($db_type == 0) $lead_time = "DATEDIFF(b.pub_shipment_date,b.po_received_date) as  date_diff";
			if ($db_type == 2) $lead_time = "(b.pub_shipment_date-b.po_received_date) as  date_diff";

			$sql = "select a.company_name,a.buyer_name,a.style_ref_no,a.job_no,b.po_number,b.GROUPING,b.id,b.po_received_date,b.pub_shipment_date,$lead_time from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company' and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.SHIPING_STATUS =3 $job_no_con $buyer_con $order_no_con $style_no_con $date_cond";
			$sql_result = sql_select($sql);
			$i = 1;
			foreach ($sql_result as $row) {
				$bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
			?>
				<tbody>
					<tr id="tr_<? echo $row[csf("id")]; ?>" bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value(<? echo $row[csf("id")]; ?>)" style="cursor:pointer;">
						<td width="30" align="center"><? echo $i; ?><input type="hidden" id="po_<? echo $i; ?>" value="<? echo $row[csf("id")]; ?>" /></td>
						<td width="100"><? echo $company_array[$row[csf("company_name")]]; ?></td>
						<td width="130">
							<p><? echo $buyer_array[$row[csf("buyer_name")]]; ?></p>
						</td>
						<td width="130">
							<p><? echo $row[csf("po_number")]; ?></p>
						</td>
						<td width="130">
							<p><? echo $row[csf("style_ref_no")]; ?></p>
						</td>
						<td width="100">
							<p><? echo $row[GROUPING]; ?></p>
						</td>
						<td width="70">
							<p><? echo $row[csf("job_no")]; ?></p>
						</td>
						<td width="70" align="center"><? echo change_date_format($row[csf("po_received_date")]); ?></td>
						<td width="70" align="center"><? echo change_date_format($row[csf("pub_shipment_date")]); ?></td>
						<td align="center"><? echo $row[csf("date_diff")]; ?></td>
					</tr>
				<?
				$i++;
			}
				?>
				</tbody>
		</table>

	</div>
	<table align="left">
		<td><input onClick="check_all_data(<? echo $i; ?>)" type="checkbox" id="check_all"> All Select/Unselect</td>
		<td align="center"><input type="button" class="formbutton" value="Close" onClick="parent.emailwindow.hide();" style="width:100px;" />
		</td>
	</table>

<?
	exit();
}
?>
<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
include('../../../includes/class4/class.conditions.php');
include('../../../includes/class4/class.reports.php');
include('../../../includes/class4/class.fabrics.php');
include('../../../includes/class4/class.yarns.php');
include('../../../includes/class4/class.conversions.php');
include('../../../includes/class4/class.trims.php');
include('../../../includes/class4/class.emblishments.php');
include('../../../includes/class4/class.washes.php');
include('../../../includes/class4/class.commercials.php');
include('../../../includes/class4/class.commisions.php');
include('../../../includes/class4/class.others.php');

include ("../../../ext_resource/excel/excel/vendor/autoload.php");
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0);
	exit();
}

if ($action=="lc_sc_no_auto_com")
{
	$sql = "SELECT b.export_lc_no as lc_sc_no FROM com_export_lc b WHERE b.status_active=1 AND b.is_deleted=0 AND b.beneficiary_name=$data
			UNION ALL
			SELECT c.contract_no as lc_sc_no FROM com_sales_contract c WHERE c.status_active=1 AND c.is_deleted=0 AND c.beneficiary_name=$data";
    echo "[" . substr(return_library_autocomplete($sql, "lc_sc_no"), 0, -1) . "]";
    exit();
}



if($action=="company_wise_report_button_setting")
{
	extract($_REQUEST);
	//echo "SELECT format_id from lib_report_template where template_name ='".$data."'  and module_id=5 and report_id=96 and is_deleted=0 and status_active=1";
	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=5 and report_id=96 and is_deleted=0 and status_active=1");
	//echo $print_report_format;die;
	$print_report_format_arr=explode(",",$print_report_format);
	//print_r($print_report_format_arr);
	echo "$('#search_1').hide();\n";
	echo "$('#search_2').hide();\n";
	echo "$('#search_3').hide();\n";
	echo "$('#search_4').hide();\n";
	echo "$('#search_5').hide();\n";
	echo "$('#search_7').hide();\n";
	echo "$('#search_8').hide();\n";

	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==261){echo "$('#search_1').show();\n";}
			if($id==281){echo "$('#search_2').show();\n";}
			if($id==282){echo "$('#search_3').show();\n";}
			if($id==283){echo "$('#search_4').show();\n";}
			if($id==284){echo "$('#search_5').show();\n";}
			if($id==305){echo "$('#search_7').show();\n";}
			if($id==160){echo "$('#search_8').show();\n";}
		}
	}
	/*else
	{
		echo "$('#search').show();\n";
		echo "$('#search2').show();\n";
		echo "$('#search3').show();\n";
	}*/
	exit();
}

// if($action=="company_wise_report_button_setting")
// {
// 	extract($_REQUEST);
// 	//echo "SELECT format_id from lib_report_template where template_name ='".$data."'  and module_id=5 and report_id=96 and is_deleted=0 and status_active=1";
// 	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=5 and report_id=96 and is_deleted=0 and status_active=1");

// 	$print_report_format_arr=explode(",",$print_report_format);	

// 	if($print_report_format != "")
// 	{
// 		foreach($print_report_format_arr as $id)
// 		{
// 			if($id==261){echo "$('#search1').show();\n";}
// 			if($id==281){echo "$('#search2').show();\n";}
// 			if($id==282){echo "$('#search3').show();\n";}
// 			if($id==283){echo "$('#search4').show();\n";}
// 			if($id==284){echo "$('#search5').show();\n";}
// 		}
// 	}
// 	else
// 	{
// 		echo "$('#search1').hide();\n";
// 		echo "$('#search2').hide();\n";
// 		echo "$('#search3').hide();\n";
// 		echo "$('#search4').hide();\n";
// 		echo "$('#search5').hide();\n";
// 	}
// 	exit();
// }


if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_based_on=str_replace("'","",$cbo_based_on);
	$cbo_ascending_by=str_replace("'","",$cbo_ascending_by);
	if($cbo_based_on==8)
	{
		$c_date=date('d-M-Y');
		$txt_date_from="16-Apr-2019";
		$txt_date_to=$c_date;
	}
	//echo $RptType;die;//
	//echo $RptType;die;
	if($RptType==1) //DETAILS
	{
		$user_arr = return_library_array("select id,user_name from user_passwd ","id","user_name");
		$bank_arr=return_library_array("select id,bank_name from   lib_bank","id","bank_name");
		$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$location_arr=return_library_array("select id,location_name from  lib_location","id","location_name");
		$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
		$supplier_arr=return_library_array("select id,supplier_name from lib_supplier","id","supplier_name");
		$country_arr=return_library_array("select id,country_name from  lib_country","id","country_name");

		$sub_sql=sql_select("SELECT b.invoice_id, a.courier_date, a.submit_date, a.bnk_to_bnk_cour_no, a.bank_ref_no, a.possible_reali_date 
			from com_export_doc_submission_mst a,com_export_doc_submission_invo b 
			where a.id=b.doc_submission_mst_id and a.entry_form=40 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		$bank_sub_data=array();
		foreach($sub_sql as $row)
		{
			$bank_sub_data[$row[csf("invoice_id")]]["courier_date"]=$row[csf("courier_date")];
			$bank_sub_data[$row[csf("invoice_id")]]["submit_date"]=$row[csf("submit_date")];
			$bank_sub_data[$row[csf("invoice_id")]]["bnk_to_bnk_cour_no"]=$row[csf("bnk_to_bnk_cour_no")];
			$bank_sub_data[$row[csf("invoice_id")]]["bank_ref_no"]=$row[csf("bank_ref_no")];
			$bank_sub_data[$row[csf("invoice_id")]]["possible_reali_date"]=$row[csf("possible_reali_date")];
		}

		$buyer_submit_date_arr=return_library_array(" SELECT b.invoice_id,a.submit_date from com_export_doc_submission_mst a,com_export_doc_submission_invo b where a.id=b.doc_submission_mst_id and a.entry_form=39","invoice_id","submit_date");


		$rlz_date_arr=return_library_array(" SELECT a.invoice_id,b.received_date,b.is_invoice_bill
		from com_export_doc_submission_invo a, com_export_proceed_realization b
		where a.doc_submission_mst_id=b.invoice_bill_id and b.is_invoice_bill  = 1
		union all
		select b.invoice_bill_id as invoice_id, b.received_date , b.is_invoice_bill
		from  com_export_proceed_realization b
		where  b.is_invoice_bill  = 2
		order by invoice_id","invoice_id","received_date");

		$exfac_sql=("SELECT B.INVOICE_NO,A.SYS_NUMBER,B.PO_BREAK_DOWN_ID, B.COUNTRY_ID, B.ITEM_NUMBER_ID,
		SUM(CASE WHEN B.ENTRY_FORM!=85 THEN B.EX_FACTORY_QNTY ELSE 0 END)  AS EX_FACTORY_QNTY
		FROM  PRO_EX_FACTORY_DELIVERY_MST A,  PRO_EX_FACTORY_MST B  WHERE A.ID=B.DELIVERY_MST_ID AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND B.ENTRY_FORM!=85 AND B.EX_FACTORY_QNTY>0 GROUP BY  B.INVOICE_NO, A.SYS_NUMBER, B.PO_BREAK_DOWN_ID, B.COUNTRY_ID, B.ITEM_NUMBER_ID");
		//echo $exfac_sql;die;
		$sql_dtls=sql_select($exfac_sql);
		$exfact_sys_arr=array();
		$exfact_qnty_arr=array();
		foreach($sql_dtls as $row)
		{
			$exfact_qnty_arr[$row["INVOICE_NO"]]+=$row["EX_FACTORY_QNTY"];
			//$exfact_sys_arr[$row["SYS_NUMBER"]]=$row["INVOICE_NO"];
			$seq_grouping = $row['PO_BREAK_DOWN_ID']."*".$row['ITEM_NUMBER_ID']."*".$row['COUNTRY_ID']."*".$row['SYS_NUMBER']."*".$row['INVOICE_NO'];
			$exfact_sys_arr[$seq_grouping]=$row["INVOICE_NO"];
		}
		//echo '<pre>';print_r($exfact_sys_arr);die;
		
		$exfac_return_sql=("SELECT B.CHALLAN_NO, B.INVOICE_NO, SUM(CASE WHEN B.ENTRY_FORM=85 THEN B.EX_FACTORY_QNTY ELSE 0 END) AS EX_FACTORY_RETURN_QNTY,B.PO_BREAK_DOWN_ID, B.COUNTRY_ID, B.ITEM_NUMBER_ID
		FROM  PRO_EX_FACTORY_DELIVERY_MST A,  PRO_EX_FACTORY_MST B  WHERE A.ID=B.DELIVERY_MST_ID AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND B.ENTRY_FORM=85 AND B.EX_FACTORY_QNTY>0 GROUP BY B.CHALLAN_NO, B.INVOICE_NO ,B.PO_BREAK_DOWN_ID, B.COUNTRY_ID, B.ITEM_NUMBER_ID");
		//echo $exfac_return_sql;
		$exfac_return_sql_res=sql_select($exfac_return_sql);
		$return_qty_arr=array();
		foreach ($exfac_return_sql_res as $row){
			//$return_qty_arr[$exfact_sys_arr[$row["CHALLAN_NO"]]]+=$row["EX_FACTORY_RETURN_QNTY"];
			//$return_qty_arr[$row["INVOICE_NO"]]+=$row["EX_FACTORY_RETURN_QNTY"];
			$seq_grouping = $row['PO_BREAK_DOWN_ID']."*".$row['ITEM_NUMBER_ID']."*".$row['COUNTRY_ID']."*".$row['CHALLAN_NO']."*".$row['INVOICE_NO'];
			$return_qty_arr[$exfact_sys_arr[$seq_grouping]]+=$row["EX_FACTORY_RETURN_QNTY"];
		}
		//echo '<pre>';print_r($return_qty_arr);die;
		
		$variable_standard_arr=return_library_array(" SELECT monitor_head_id,monitoring_standard_day from variable_settings_commercial where status_active=1 and is_deleted=0 and company_name=$cbo_company_name and variable_list=19","monitor_head_id","monitoring_standard_day");
		
		

		$cbo_company_name=str_replace("'","",$cbo_company_name);
		if($cbo_company_name!=0) $cbo_company_name = $cbo_company_name; else $cbo_company_name="%%";

		$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
		if($cbo_buyer_name == 0) $cbo_buyer_name="%%"; else $cbo_buyer_name = $cbo_buyer_name;

		$cbo_lien_bank=str_replace("'","",$cbo_lien_bank);
		if($cbo_lien_bank == 0) $cbo_lien_bank="%%"; else $cbo_lien_bank = $cbo_lien_bank;
		//echo  $cbo_lien_bank;die;
		$cbo_location=str_replace("'","",$cbo_location);
		if($cbo_location == 0) $cbo_location="%%"; else $cbo_location = $cbo_location;

		if($cbo_based_on == 0) $cbo_based_on="%%"; else $cbo_based_on = $cbo_based_on;

		$txt_date_from=str_replace("'","",$txt_date_from);
		if(trim($txt_date_from)!= "") $txt_date_from  =$txt_date_from;

		$txt_date_to=str_replace("'","",$txt_date_to);
		if(trim($txt_date_to)!= "") $txt_date_to = $txt_date_to;

		$forwarder_name=str_replace("'","",$forwarder_name);
		if($forwarder_name!=0) $forwarder_cond=" and a.forwarder_name='$forwarder_name'"; else $forwarder_cond="";
		$txt_invoice_no=str_replace("'","",$txt_invoice_no);
		if($txt_invoice_no!="") $invoice_cond=" and a.invoice_no like '%$txt_invoice_no'"; else $invoice_cond="";

		if($cbo_ascending_by!="" && $cbo_ascending_by==1 ) 
		{
			$ascending_by = "invoice_no";
			$ascendig_cond=" order by $ascending_by asc ";
		} elseif ($cbo_ascending_by!="" && $cbo_ascending_by==2) {
			$ascending_by = "invoice_date";
			$ascendig_cond=" order by $ascending_by asc ";
		} elseif ($cbo_ascending_by!="" && $cbo_ascending_by==3) {
			$ascending_by = "exp_form_no";
			$ascendig_cond=" order by $ascending_by asc ";
		}elseif ($cbo_ascending_by!="" && $cbo_ascending_by==4) {
			$ascending_by = "exp_form_date";
			$ascendig_cond=" order by $ascending_by asc ";
		}else{
			$ascendig_cond="";
		}
		//echo $cbo_ascending_by;die;
		//if(trim($data[7])!="") $cbo_year2=$data[7];
		if ($txt_date_from!="" && $txt_date_to!="")
		{
			if($cbo_based_on ==0)
			{
				$str_cond=" and a.invoice_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==1)
			{
				$str_cond=" and a.invoice_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==2)
			{
				$str_cond=" and a.ex_factory_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==3)
			{
				$str_cond=" and a.actual_shipment_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==4)
			{
				$str_cond=" and a.bl_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==5)
			{
				$str_cond=" and a.ship_bl_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==6)
			{
				$str_cond=" and e.received_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==7)
			{

				if($db_type==0) $str_cond=" and a.insert_date between '$txt_date_from 00:00:01' and  '$txt_date_to 23:59:59'";
				else if($db_type==2) $str_cond=" and a.insert_date between '$txt_date_from 12:00:01 AM' and  '$txt_date_to 11:59:59 PM'";
			}
			else if($cbo_based_on ==8)
			{
				$str_cond=" and a.invoice_date between '$txt_date_from' and  '$txt_date_to'";
			}
		}
		else
		{
			$str_cond="";
		}
		$shipping_mode=str_replace("'","",$shipping_mode);
		$ship_cond="";
		if($shipping_mode!=0) $ship_cond=" and a.shipping_mode=$shipping_mode";
		
		$txt_lc_sc_no=str_replace("'","",$txt_lc_sc_no);
		$txt_int_ref_no=str_replace("'","",$txt_int_ref_no);
		$ref_inv_cond="";
		$ref_inv_id_arr=array();
		if($txt_int_ref_no!="")
		{
			$sql_ref="select a.mst_id as INV_ID, b.id as PO_ID, b.grouping as REF_NO from com_export_invoice_ship_dtls a, wo_po_break_down b where a.po_breakdown_id=b.id and a.status_active=1 and a.is_deleted=0 and b.grouping='$txt_int_ref_no'";
			//echo $sql_ref;die;
			$sql_ref_result=sql_select($sql_ref);
			foreach($sql_ref_result as $row)
			{
				$ref_inv_id_arr[$row["INV_ID"]]=$row["INV_ID"];
			}
			if(count($ref_inv_id_arr)<1)
			{
				echo "No Data Found.";die;
			}
		}
		//echo $invoice_cond;die;
		if(count($ref_inv_id_arr)>0)
		{
			$ref_inv_ids = implode(",", $ref_inv_id_arr);
			
			$ref_inv_cond = " and a.id in($ref_inv_ids)";
			
			//echo $ref_inv_cond.rrrr.count($ref_inv_id_arr);die;
		}
		//echo $invoice_cond.test.count($ref_inv_id_arr);die;
		// $job_id_data=implode(",",array_unique(explode(",",chop($job_id_data,','))));
		if($cbo_based_on==6)
		{
			if($db_type==0)
			{
				$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, b.internal_file_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor, group_concat(distinct(f.po_breakdown_id)) as PO_ID,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,b.export_item_category,a.atsite_discount_amt
				FROM com_export_invoice_ship_mst a, com_export_lc b, com_export_doc_submission_invo d, com_export_proceed_realization e, com_export_invoice_ship_dtls f
				WHERE a.lc_sc_id=b.id and a.id=d.invoice_id and d.doc_submission_mst_id=e.invoice_bill_id and a.id=f.mst_id  and a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' AND b.export_lc_no LIKE '%$txt_lc_sc_no%' and a.is_lc=1 and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and f.status_active=1 and f.is_deleted=0  $str_cond $forwarder_cond $ship_cond $invoice_cond  $ref_inv_cond
				group by a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name, b.pay_term, b.lien_bank, b.export_lc_no, b.internal_file_no, a.insert_date, b.doc_presentation_days, a.co_date,b.tenor,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,b.export_item_category,a.atsite_discount_amt

				UNION ALL

				SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, c.internal_file_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor, group_concat(distinct(f.po_breakdown_id)) as PO_ID,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,c.export_item_category,a.atsite_discount_amt
				FROM com_export_invoice_ship_mst a, com_sales_contract c , com_export_doc_submission_invo d, com_export_proceed_realization e, com_export_invoice_ship_dtls f
				WHERE a.lc_sc_id=c.id and a.id=d.invoice_id and d.doc_submission_mst_id=e.invoice_bill_id and a.id=f.mst_id and a.is_lc=2 and a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' AND c.contract_no LIKE '%$txt_lc_sc_no%' and  a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $str_cond $forwarder_cond $ship_cond $invoice_cond $ref_inv_cond
				group by a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name, c.pay_term, c.lien_bank, c.contract_no, c.internal_file_no, a.insert_date, c.doc_presentation_days, a.co_date ,c.tenor,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,c.export_item_category,a.atsite_discount_amt

				UNION ALL

				SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date, a. 	invoice_value, a.discount_ammount, a.stamp_value, a.bonus_ammount, a.claim_ammount, a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor, group_concat(distinct(f.po_breakdown_id)) as PO_ID,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,c.export_item_category,a.atsite_discount_amt
				FROM com_export_invoice_ship_mst a, com_sales_contract c , com_export_proceed_realization e, com_export_invoice_ship_dtls f
				WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=2 and a.lc_sc_id=c.id and a.id=f.mst_id and e.invoice_bill_id = a.id and e.is_invoice_bill = 2 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.status_active=1 and f.is_deleted=0 AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond $ref_inv_cond $ascendig_cond 
				group by a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date, a. 	invoice_value, a.discount_ammount, a.stamp_value, a.bonus_ammount, a.claim_ammount, a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name, c.pay_term, c.lien_bank, c.contract_no, a.insert_date, c.doc_presentation_days, a.co_date,c.tenor,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,c.export_item_category,a.atsite_discount_amt";

			}
			else
			{
				$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor, LISTAGG(f.po_breakdown_id, ',') WITHIN GROUP (ORDER BY f.po_breakdown_id) as PO_ID,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,b.export_item_category,a.atsite_discount_amt

				FROM com_export_invoice_ship_mst a, com_export_lc b , com_export_doc_submission_invo d, com_export_proceed_realization e, com_export_invoice_ship_dtls f
				WHERE a.lc_sc_id=b.id and a.id=d.invoice_id and a.id=f.mst_id and d.doc_submission_mst_id=e.invoice_bill_id  and a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' AND b.export_lc_no LIKE '%$txt_lc_sc_no%' and a.is_lc=1 and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and f.status_active=1 and f.is_deleted=0  $str_cond $forwarder_cond $ship_cond $invoice_cond $ref_inv_cond
				group by a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name , b.pay_term, b.lien_bank, b.export_lc_no ,a.insert_date, b.doc_presentation_days, a.co_date ,b.tenor,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,b.export_item_category,a.atsite_discount_amt

				UNION ALL

				SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor, LISTAGG(f.po_breakdown_id, ',') WITHIN GROUP (ORDER BY f.po_breakdown_id) as PO_ID,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,c.export_item_category,a.atsite_discount_amt
				FROM com_export_invoice_ship_mst a, com_sales_contract c, com_export_doc_submission_invo d, com_export_proceed_realization e, com_export_invoice_ship_dtls f
				WHERE a.lc_sc_id=c.id and a.id=d.invoice_id and a.id=f.mst_id and d.doc_submission_mst_id=e.invoice_bill_id  and a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' AND c.contract_no LIKE '%$txt_lc_sc_no%' and a.is_lc=2 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.status_active=1 and f.is_deleted=0  $str_cond $forwarder_cond $ship_cond $invoice_cond $ref_inv_cond 
				group by a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name , c.pay_term , c.lien_bank , c.contract_no , a.insert_date, c.doc_presentation_days, a.co_date ,c.tenor,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,c.export_item_category,a.atsite_discount_amt
				UNION ALL

				SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor, LISTAGG(f.po_breakdown_id, ',') WITHIN GROUP (ORDER BY f.po_breakdown_id) as PO_ID,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,c.export_item_category,a.atsite_discount_amt
				FROM com_export_invoice_ship_mst a, com_sales_contract c , com_export_proceed_realization e, com_export_invoice_ship_dtls f
				WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=2 and a.lc_sc_id=c.id and a.id=f.mst_id and e.invoice_bill_id = a.id and e.is_invoice_bill = 2 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.status_active=1 and f.is_deleted=0 AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond $ref_inv_cond $ascendig_cond 
				group by a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name , c.pay_term , c.lien_bank , c.contract_no , a.insert_date, c.doc_presentation_days, a.co_date,c.tenor,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,c.export_item_category,a.atsite_discount_amt";
			}
		}
		else if($cbo_based_on==8)
		{
			if($db_type==0)
			{
				$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, b.internal_file_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor, group_concat(distinct(f.po_breakdown_id)) as PO_ID,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,b.export_item_category,a.atsite_discount_amt
				FROM com_export_invoice_ship_mst a, com_export_lc b, com_export_invoice_ship_dtls f
				WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=1 and a.lc_sc_id=b.id and a.id=f.mst_id and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and f.status_active=1 and f.is_deleted=0 AND b.export_lc_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond and a.id not in(select a.invoice_id from com_export_doc_submission_invo a, com_export_proceed_realization b where a.doc_submission_mst_id=b.invoice_bill_id and a.is_lc=1) $ref_inv_cond
				group by a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name, b.pay_term, b.lien_bank, b.export_lc_no, b.internal_file_no, a.insert_date, b.doc_presentation_days, a.co_date,b.tenor,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,b.export_item_category,a.atsite_discount_amt

				UNION ALL

				SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, c.internal_file_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor, group_concat(distinct(f.po_breakdown_id)) as PO_ID,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,c.export_item_category,a.atsite_discount_amt
				FROM com_export_invoice_ship_mst a, com_sales_contract c, com_export_invoice_ship_dtls f
				WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=2 and a.lc_sc_id=c.id and a.id=f.mst_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.status_active=1 and f.is_deleted=0  AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond and a.id not in(select a.invoice_id from com_export_doc_submission_invo a, com_export_proceed_realization b where a.doc_submission_mst_id=b.invoice_bill_id and a.is_lc=2) $ref_inv_cond $ascendig_cond
				group by a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name, c.pay_term, c.lien_bank, c.contract_no, c.internal_file_no, a.insert_date, c.doc_presentation_days, a.co_date,c.tenor,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,c.export_item_category,a.atsite_discount_amt";

			}
			else
			{							
				$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, b.internal_file_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor, LISTAGG(f.po_breakdown_id, ',') WITHIN GROUP (ORDER BY f.po_breakdown_id) as PO_ID,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,b.export_item_category,a.atsite_discount_amt
				FROM com_export_invoice_ship_mst a, com_export_lc b, com_export_invoice_ship_dtls f
				WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=1 and a.lc_sc_id=b.id and a.id=f.mst_id and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and f.status_active=1 and f.is_deleted=0 AND b.export_lc_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond and a.id not in(select a.invoice_id from com_export_doc_submission_invo a, com_export_proceed_realization b where a.doc_submission_mst_id=b.invoice_bill_id and a.is_lc=1) $ref_inv_cond
				group by a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name, b.pay_term, b.lien_bank , b.export_lc_no, b.internal_file_no, a.insert_date, b.doc_presentation_days, a.co_date ,b.tenor,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,b.export_item_category,a.atsite_discount_amt

				UNION ALL

				SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, c.internal_file_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor, LISTAGG(f.po_breakdown_id, ',') WITHIN GROUP (ORDER BY f.po_breakdown_id) as PO_ID,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,c.export_item_category,a.atsite_discount_amt
				FROM com_export_invoice_ship_mst a, com_sales_contract c, com_export_invoice_ship_dtls f
				WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=2 and a.lc_sc_id=c.id and a.id=f.mst_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.status_active=1 and f.is_deleted=0  AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond and a.id not in(select a.invoice_id from com_export_doc_submission_invo a, com_export_proceed_realization b where a.doc_submission_mst_id=b.invoice_bill_id and a.is_lc=2) $ref_inv_cond $ascendig_cond
				group by a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name , c.pay_term , c.lien_bank , c.contract_no, c.internal_file_no, a.insert_date, c.doc_presentation_days, a.co_date , c.tenor,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,c.export_item_category,a.atsite_discount_amt";
			}
		}
		else
		{
			if($cbo_based_on ==9)
			{
				$submit_cond=" and b.submit_date between '$txt_date_from' and  '$txt_date_to'";
				//echo "SELECT a.invoice_id from com_export_doc_submission_invo a, com_export_doc_submission_mst bwhere a.doc_submission_mst_id=b.id and b.status_active = 1 and b.is_deleted = 0 and b.company_id = $cbo_company_name and b.entry_form=40 $submit_cond "; die;
				$submitted_invoices = sql_select("SELECT a.invoice_id from com_export_doc_submission_invo a, com_export_doc_submission_mst b
				where a.doc_submission_mst_id=b.id and b.status_active = 1 and b.is_deleted = 0 and b.company_id = $cbo_company_name and b.entry_form=40 $submit_cond ");
				foreach ($submitted_invoices as $val)
				{
					$all_submitted_invoice_arr[$val[csf("invoice_id")]] = $val[csf("invoice_id")];
				}

				if(count($all_submitted_invoice_arr)>999)
				{
					$all_submitted_invoice_chunk_arr=array_chunk($all_submitted_invoice_arr, 999);
					$all_submitted_invoice_cond=" and (";
					foreach ($all_submitted_invoice_chunk_arr as $value)
					{
						$all_submitted_invoice_cond .="and a.id in (".implode(",", $value).") or ";
					}
					$all_submitted_invoice_cond=chop($all_submitted_invoice_cond,"or ");
					$all_submitted_invoice_cond.=")";
				}
				else
				{
					$all_submitted_invoice_cond=" and a.id in (".implode(",", $all_submitted_invoice_arr).")";
				}
			}
			if($db_type==0)
			{
				$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, b.internal_file_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor, group_concat(distinct(f.po_breakdown_id)) as PO_ID,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,b.export_item_category,a.atsite_discount_amt
				FROM com_export_invoice_ship_mst a, com_export_lc b, com_export_invoice_ship_dtls f
				WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' AND b.export_lc_no LIKE '%$txt_lc_sc_no%' and a.is_lc=1 and a.lc_sc_id=b.id and a.id=f.mst_id and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $str_cond $forwarder_cond $ship_cond $invoice_cond $all_submitted_invoice_cond $ref_inv_cond
				group by SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name, b.pay_term, b.lien_bank, b.export_lc_no, b.internal_file_no, a.insert_date, b.doc_presentation_days, a.co_date ,b.tenor,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,b.export_item_category,a.atsite_discount_amt
				
				UNION ALL

				SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date, a.invoice_value, a.discount_ammount, a.stamp_value,a.bonus_ammount, a.claim_ammount, a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, c.internal_file_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor, group_concat(distinct(f.po_breakdown_id)) as PO_ID,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,c.export_item_category,a.atsite_discount_amt
				FROM com_export_invoice_ship_mst a, com_sales_contract c, com_export_invoice_ship_dtls f
				WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=2 and a.lc_sc_id=c.id and a.id=f.mst_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.status_active=1 and f.is_deleted=0 AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond  $ascendig_cond $all_submitted_invoice_cond $ref_inv_cond
				group by a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date, a.invoice_value, a.discount_ammount, a.stamp_value,a.bonus_ammount, a.claim_ammount, a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name, c.pay_term, c.lien_bank, c.contract_no, c.internal_file_no, a.insert_date, c.doc_presentation_days, a.co_date, c.tenor,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,c.export_item_category,a.atsite_discount_amt";//id

			}
			else
			{
				$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, b.internal_file_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor, LISTAGG(f.po_breakdown_id, ',') WITHIN GROUP (ORDER BY f.po_breakdown_id) as PO_ID,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,b.export_item_category,a.atsite_discount_amt
				FROM com_export_invoice_ship_mst a, com_export_lc b, com_export_invoice_ship_dtls f
				WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=1 and a.lc_sc_id=b.id and a.id=f.mst_id and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and f.status_active=1 and f.is_deleted=0 AND b.export_lc_no LIKE '%$txt_lc_sc_no%'  $str_cond $forwarder_cond $ship_cond $invoice_cond $all_submitted_invoice_cond $ref_inv_cond
				group by a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name , b.pay_term, b.lien_bank , b.export_lc_no, b.internal_file_no, a.insert_date, b.doc_presentation_days, a.co_date ,b.tenor,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,b.export_item_category,a.atsite_discount_amt

				UNION ALL

				SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, c.internal_file_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor, LISTAGG(f.po_breakdown_id, ',') WITHIN GROUP (ORDER BY f.po_breakdown_id) as PO_ID,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,c.export_item_category,a.atsite_discount_amt
				FROM com_export_invoice_ship_mst a, com_sales_contract c, com_export_invoice_ship_dtls f
				WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=2 and a.lc_sc_id=c.id and a.id=f.mst_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.status_active=1 and f.is_deleted=0 AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond $ascendig_cond $all_submitted_invoice_cond $ref_inv_cond
				group by a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name, c.pay_term, c.lien_bank, c.contract_no, c.internal_file_no, a.insert_date, c.doc_presentation_days, a.co_date,c.tenor,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,c.export_item_category,a.atsite_discount_amt";
			}
		}
		//echo $sql;die;
		$sql_re=sql_select($sql);
		//echo count($sql_re)."=".$sql;die;
		// $job_arr=return_library_array("select id, job_no_mst from wo_po_break_down","id","job_no_mst");
		if(count($sql_re)<1){echo "No Data Found"; die;}
		$k=1;
		$invoice_id_check=array();
		$buyer_wise_summary_data=array();
		foreach($sql_re as $row)
		{
			$system_id_val="'".$row[csf('id')]."'";
			$system_file_img_arr[$row[csf('id')]] = $system_id_val;

			$inv_po_id_arr=array_unique(explode(",",$row['PO_ID']));
			foreach($inv_po_id_arr as $po_ids)
			{
				if($po_ids > 0) $po_id_arr[$po_ids]=$po_ids;
			}		
			$po_id_all.=$row['PO_ID'].',';
		}
		$po_id_arr = explode(",", rtrim($po_id_all,", "));
		// $po_id_all=array_unique(explode(",",chop($po_id_all,',')));
		// $job_no_all=implode(",",array_unique(explode(",",chop($job_no_all,','))));
		//$po_filter_id=where_con_using_array($po_id_arr,0,'id');
		$po_filter=where_con_using_array($po_id_arr,0,'po_breakdown_id');
		
		$sql_inv_set="SELECT A.MST_ID, A.PO_BREAKDOWN_ID, A.CURRENT_INVOICE_QNTY, A.CURRENT_INVOICE_QNTY, (A.CURRENT_INVOICE_QNTY*C.TOTAL_SET_QNTY) AS INVOICE_QNTY_PCS, C.TOTAL_SET_QNTY, b.grouping as REF_NO, b.job_id as JOB_ID, b.JOB_NO_MST 
		from com_export_invoice_ship_dtls a, wo_po_break_down b, wo_po_details_master c 
		where a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and c.company_name=$cbo_company_name and a.status_active=1 and a.is_deleted=0 $po_filter";
		//echo $sql_inv_set;
		$sql_order_set=sql_select($sql_inv_set);
		$inv_qnty_pcs_arr=array();
		foreach($sql_order_set as $row)
		{
			$inv_qnty_pcs_arr[$row['MST_ID']]+=$row["INVOICE_QNTY_PCS"];
			$inv_po_qnty_pcs_arr[$row['MST_ID']][$row['PO_BREAKDOWN_ID']]+=$row['CURRENT_INVOICE_QNTY'];
			
			if($ref_inv_check[$row["MST_ID"]][$row["REF_NO"]]=="")
			{
				$ref_inv_check[$row["MST_ID"]][$row["REF_NO"]]=$row["REF_NO"];
				$inv_ref_data[$row["MST_ID"]].=$row["REF_NO"].",";
				$job_id_data.=$row["JOB_ID"].",";
			}
			
			$job_id_arr[$row["JOB_ID"]]=$row["JOB_ID"];
			$job_arr[$row["PO_BREAKDOWN_ID"]]=$row["JOB_NO_MST"];
		}
		unset($sql_order_set);
		
		//print_r($po_id_arr);
		// echo "select image_location, master_tble_id from common_photo_library where master_tble_id in($system_file_img_cond) and form_name='export_invoice' and is_deleted=0 and file_type=2";
		if ($system_file_img_cond != '')
		{
			$data_file=sql_select("select image_location, master_tble_id from common_photo_library where master_tble_id in($system_file_img_cond) and form_name='export_invoice' and is_deleted=0 and file_type=2");
			$system_file_arr=array();
			foreach($data_file as $row)
			{
				$system_file_arr[$row[csf('master_tble_id')]]['file']=$row[csf('image_location')];
			}
			unset($data_file);
		}	
		// $po_id=where_con_using_array($po_id_arr,0,'b.id');
		$job_in_cond=where_con_using_array($job_id_arr,0,'a.id');
		if($db_type==0)
		{
			$order_sql = "SELECT a.id as ID, a.job_no as JOB_NO, a.avg_unit_price as AVG_UNIT_PRICE, sum(b.po_quantity) as ORD_QTY,group_concat(distinct(b.id)) as PO_ID, c.costing_per as COSTING_PER
			from wo_po_details_master a,wo_po_break_down b, wo_pre_cost_mst c
			where a.id=b.job_id $job_id_arr and b.job_no_mst=c.job_no  and a.status_active=1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id,a.avg_unit_price,a.job_no, c.costing_per"; 
		}
		else
		{
			$order_sql = "SELECT a.id as ID, a.job_no as JOB_NO, a.avg_unit_price as AVG_UNIT_PRICE, sum(b.po_quantity) as ORD_QTY, LISTAGG(b.id, ',') WITHIN GROUP (ORDER BY b.id) as PO_ID, c.costing_per as COSTING_PER 
			from wo_po_details_master a,wo_po_break_down b, wo_pre_cost_mst c
			where a.id=b.job_id $job_id_arr and b.job_no_mst=c.job_no  and a.status_active=1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id,a.avg_unit_price,a.job_no, c.costing_per";
		}
		///echo $order_sql;die;
		$order_data=sql_select($order_sql);
		$condition= new condition();
		// $condition->po_id("in($po_id_all)");
		// $condition->job_no("in ($job_no_all)");
		// $condition->po_id("in(". implode(',',$po_id_arr) .")");
		
		
		
		$condition->jobid_in(implode(',',$job_id_arr));
		$condition->init();
		$yarn= new yarn($condition);
		$yarn_costing_arr=$yarn->getJobWiseYarnAmountArray();
		
		$fabric= new fabric($condition);
		$fabric_costing_arr2=$fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
		
		
		
		//$conversion= new conversion($condition);
		//$conversion_costing_arr_process=$conversion->getAmountArray_by_job();
		
		$trims= new trims($condition);		
		$trims_costing_arr=$trims->getAmountArray_by_job();
		$emblishment= new emblishment($condition);
		$emblishment_costing_arr=$emblishment->getAmountArray_by_job();
		$wash= new wash($condition);
		
		
		
		$emblishment_costing_arr_wash=$wash->getAmountArray_by_job();
		$commercial= new commercial($condition);
		$commercial_costing_arr=$commercial->getAmountArray_by_job();
		$commission= new commision($condition);
		$commission_costing_arr=$commission->getAmountArray_by_job();
		$other= new other($condition);
		$other_costing_arr=$other->getAmountArray_by_job();
		//echo "test2";die;
		$job_in_cond=where_con_using_array($job_id_arr,0,'job_id');
		$sql_trim_summ = "SELECT id, job_no, trim_group,description,brand_sup_ref,remark, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,status_active,seq
		from wo_pre_cost_trim_cost_dtls
		where status_active=1 and is_deleted=0 $job_in_cond order by seq";
		$data_array_trim_summ=sql_select($sql_trim_summ);
		$trim_amount_arr=$trims->getAmountArray_precostdtlsid();					
		foreach( $data_array_trim_summ as $row )
		{
			$trim_amount=$trim_amount_arr[$row[csf("id")]];
			$trim_job_amountArr[$row[csf("job_no")]]+=$trim_amount;
		}
		$pre_cost_dtls_sql = "SELECT job_no,fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent,certificate_pre_cost,certificate_percent,common_oh,common_oh_percent,total_cost,deffdlc_cost,deffdlc_percent,interest_cost,interest_percent,incometax_cost,incometax_percent,total_cost_percent,price_dzn,price_dzn_percent,margin_dzn,margin_dzn_percent,price_pcs_or_set,depr_amor_pre_cost,depr_amor_po_price,price_pcs_or_set_percent,margin_pcs_set,margin_pcs_set_percent,cm_for_sipment_sche
		from wo_pre_cost_dtls
		where status_active=1 and is_deleted=0 $job_in_cond";
		//echo $pre_cost_dtls_sql;die;
		$pre_cost_dtls_data=sql_select($pre_cost_dtls_sql);
		$all_total_cost=array();
		foreach($pre_cost_dtls_data as $row){
			$job_no=$row[csf("job_no")];
			$fab_purchase_knit2=array_sum($fabric_costing_arr2['knit']['grey'][$job_no]);
			$fab_purchase_woven2=array_sum($fabric_costing_arr2['woven']['grey'][$job_no]);
			$yarn_costing=$yarn_costing_arr[$job_no];
			$tot_fabric_cost=$fab_purchase_knit2+$fab_purchase_woven2;
			$conversion_cost=array_sum($conversion_costing_arr_process[$job_no]);
			$freight_cost=$other_costing_arr[$job_no]['freight'];
			$inspection_cost=$other_costing_arr[$job_no]['inspection'];
			$certificate_cost=$other_costing_arr[$job_no]['certificate_pre_cost'];
			$common_oh=$other_costing_arr[$job_no]['common_oh'];
			$currier_cost=$other_costing_arr[$job_no]['currier_pre_cost'];
			$cm_cost=$other_costing_arr[$job_no]['cm_cost'];
			$lab_test_cost=$other_costing_arr[$job_no]['lab_test'];
			$depr_amor_pre_cost=$other_costing_arr[$job_no]['depr_amor_pre_cost'];
			$deffdlc_cost=$other_costing_arr[$job_no]['deffdlc_cost'];
			$fabric_cost=$tot_fabric_cost;
			$trims_cost=$trim_job_amountArr[$job_no];
			$embel_cost=$emblishment_costing_arr[$job_no];
			$wash=$emblishment_costing_arr_wash[$job_no];
			// $commercial_cost=$commercial_costing_arr[$job_no];
			// $comm_cost=$row[csf("comm_cost")];
			// $cm_cost_dzn=$row[csf("cm_cost")];
			// $deffdlc_cost_dzn=$row[csf("deffdlc_cost")];
			$interest_cost=$row[csf("interest_cost")];
			$incometax_cost=$row[csf("incometax_cost")];
			// $commission=$commission_costing_arr[$job_no];
			$lab_test=$lab_test_cost;
			$inspection=$inspection_cost;
			// $cm_cost=$cm_cost;
			// $freight=$freight_cost;
			$currier_pre_cost=$currier_cost;
			$certificate_pre_cost=$certificate_cost;
			$common_oh=$common_oh;

			// $all_total_cost=$tot_fabric_cost+$yarn_costing+$conversion_cost+$trims_cost+$embel_cost+$wash+$commercial_cost+$commission+$lab_test_cost+$cm_cost+$currier_pre_cost+$inspection_cost+$freight+$common_oh+$certificate_pre_cost+$depr_amor_pre_cost+$interest_cost+$incometax_cost+$deffdlc_cost;
			// $others_cost_value=$all_total_cost-$cm_cost-$freight-$commercial_cost-$commission;
			$all_total_cost[$job_no]=$tot_fabric_cost+$yarn_costing+$conversion_cost+$trims_cost+$embel_cost+$wash+$lab_test_cost+$currier_pre_cost+$inspection_cost+$common_oh+$certificate_pre_cost+$depr_amor_pre_cost+$interest_cost+$incometax_cost+$deffdlc_cost;
		}
		// var_dump($all_total_cost);die;
		$cmPerDzn=array();
		$costPerDzn=array();
		foreach($order_data as $row){
			$po_id_all_arr=explode(",",$row['PO_ID']);
			if($row[csf("costing_per")]==1){$order_price_per_dzn=12;}
			else if($row[csf("costing_per")]==2){$order_price_per_dzn=1;}
			else if($row[csf("costing_per")]==3){$order_price_per_dzn=24;}
			else if($row[csf("costing_per")]==4){$order_price_per_dzn=36;}
			else if($row[csf("costing_per")]==5){$order_price_per_dzn=48;}
			foreach($po_id_all_arr as $val){
				$less_commission=$commission_costing_arr[$job_arr[$val]];
				$less_commercial=$commercial_costing_arr[$job_arr[$val]];
				$less_freight=$other_costing_arr[$job_arr[$val]]['freight'];
				$order_net_value=($row['ORD_QTY']*$row['AVG_UNIT_PRICE'])-($less_commission+$less_commercial+$less_freight);
				$cmValue = $order_net_value-$all_total_cost[$job_arr[$val]];
				$cmPerDzn[$val]=$cmValue/$row['ORD_QTY']*$order_price_per_dzn;
				$costPerDzn[$val]=$order_price_per_dzn;
			}
		}
		// var_dump($cmPerDzn);die;
		
		//echo "<pre>";
		//print_r($system_file_arr);

		$invoice_id_check=array();
		$buyer_wise_summary_data=array();
		$buyerwise_cm_dzn_inv_qty=0;
		foreach($sql_re as $row)
		{
			
			$exfact_qnty=$exfact_qnty_arr[$row[csf('id')]]-$return_qty_arr[$row[csf('id')]];
			$print_data=0;
			if($cbo_based_on==2)
			{
				if($exfact_qnty>0) $print_data=1; else $print_data=0;
			}
			else
			{
				$print_data=1;
			}
			
			if($print_data)
			{
				$buyerpo_id_arr=explode(",",$row['PO_ID']);
				if($invoice_id_check[$row[csf('id')]]=="")
				{
					$invoice_id_check[$row[csf('id')]]=$row[csf('id')];
					$buyer_wise_summary_data[$row[csf('buyer_id')]]['invoice_quantity']+=$row[csf('invoice_quantity')];
					$buyer_wise_summary_data[$row[csf('buyer_id')]]['invoice_value']+=$row[csf('invoice_value')];
					$buyer_wise_summary_data[$row[csf('buyer_id')]]['net_invo_value']+=$row[csf('net_invo_value')];
					$buyer_wise_summary_data[$row[csf('buyer_id')]]['invoice_qnty_pcs']+=$inv_qnty_pcs_arr[$row[csf('id')]];
					//$buyer_key_data[$row[csf('buyer_id')]]+=$row[csf('net_invo_value')];
									
				}
				foreach($buyerpo_id_arr as $val){
					if ($costPerDzn[$val] > 0){
						$buyerwise_cm_dzn_inv_qty=($cmPerDzn[$val]/$costPerDzn[$val])*$inv_po_qnty_pcs_arr[$row[csf('id')]][$val];
						$buyer_wise_summary_data[$row[csf('buyer_id')]]['buyerwise_cm_dzn_inv_qty']+=$buyerwise_cm_dzn_inv_qty;		
					}						
				}
			}
		}

		$print_report_format=return_field_value("format_id","lib_report_template","template_name =".$cbo_company_name." and module_id=5 and report_id=245 and status_active=1 and is_deleted=0");		
		$format_id=explode(",",$print_report_format);
		//echo $print_report_format.'**'.$format_id[0];
		ob_start();


		//echo " ". $company_arr[str_replace("'","",$cbo_company_name)];
		//echo $report_title;
		//$based_on_arr=array(1=>"Invoice Date",2=>"Exfactory Date",3=>"Actual Date"); echo " ". $based_on_arr[$cbo_based_on];
		?>
		
		<div style="width:6150px">
			<table width="5980" cellpadding="0" cellspacing="0" id="caption">
				<tr>
					<td align="center" width="100%" colspan="20" class="form_caption" >
						<strong style="font-size:18px">Company Name:<? echo " ". $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong>
					</td>
				</tr>
				<tr>
					<td align="center" width="100%" colspan="20" class="form_caption" >
						<strong style="font-size:18px"><? echo $report_title; ?>
						<?
						if($cbo_based_on!=0)
						{
						?>
							(<strong style="font-size:18px">Based On:<? $based_on_arr=array(1=>"Invoice Date",2=>"Exfactory Date",3=>"Actual Date"); echo " ". $based_on_arr[$cbo_based_on]; ?></strong>)

						<?
						}
						?></strong>
					</td>
				</tr>
			</table>
			<br>
            <table width="850" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_0" align="left">
                <thead>
                    <tr>
                        <th width="50">Sl</th>
                        <th width="150">Buyer</th>
                        <th width="150">Invoice Qty.</th>
						<th width="150">Invoice Qnty. Pcs</th>
                        <th width="150">Invoice Value (Gross)</th>						
                        <th width="150">Invoice Value (Net)</th>
						<th>Total CM as per Invoice Qty</th>
                    </tr>
                </thead>
                <tbody>
                <?
				$i=1;
				foreach($buyer_wise_summary_data as $buy_id=>$buy_val)
				{
					if ($i%2==0)
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                    	<td align="center" title="<?= $buy_id;?>"><? echo $i;?></td>
                        <td title="<?=$buy_id;?>"><? echo $buyer_arr[$buy_id];?></td>
                        <td align="right"><? echo number_format($buy_val["invoice_quantity"],2);?></td>
						<td align="right"><? echo number_format($buy_val["invoice_qnty_pcs"],2);?></td>
                        <td align="right"><? echo number_format($buy_val["invoice_value"],2);?></td>
                        <td align="right"><? echo number_format($buy_val["net_invo_value"],2);?></td>
						<td align="right"><? echo number_format($buy_val["buyerwise_cm_dzn_inv_qty"],2);?></td>
                    </tr>
                    <?
					$i++;
					$buyerwise_tot_invoice_quantity +=$buy_val["invoice_quantity"];
					$buyerwise_tot_invoice_quantity_pcs +=$buy_val["invoice_qnty_pcs"];
					$buyerwise_tot_invoice_value +=$buy_val["invoice_value"];
					$buyerwise_tot_net_invo_value +=$buy_val["net_invo_value"];
					$buyerwise_tot_cmasper_invoice_qty +=$buy_val["buyerwise_cm_dzn_inv_qty"];
				}
				?>
                </tbody>
                <tfoot>
                	<tr>
                    	<th colspan="2" align="right">Total:</th>
                        <th align="right"><? echo number_format($buyerwise_tot_invoice_quantity,2);?></th>
						 <th align="right"><? echo number_format($buyerwise_tot_invoice_quantity_pcs,2);?></th>
                        <th align="right"><? echo number_format($buyerwise_tot_invoice_value,2);?></th>
                        <th align="right"><? echo number_format($buyerwise_tot_net_invo_value,2);?></th>
						<th align="right"><? echo number_format($buyerwise_tot_cmasper_invoice_qty,2);?></th>
                    </tr>
                </tfoot>
            </table>			
			<br />           
            <table width="6330" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1" align="left">
                <thead>
                    <tr>
                        <th width="50">Sl</th>
                        <th width="100">Company Name</th>

                        <th width="100">Country Name</th>
						<th width="70">Buyer Name</th>
                        <th width="100">Invoice No.</th>
                        <th width="50">File</th>
                        <th width="180">Int. Ref. No</th>

                        <th width="70">Invoice Date</th>
                        <th width="70">Insert Date</th>
                        <th width="80">Ship Mode</th>
                        <th width="70">SC/LC</th>
                        <th width="100">SC/LC No.</th>
                        <th width="100">File No</th>
                        <th width="100">Forwarder Name</th>
                        <th width="150">Lien Bank</th>
                        <th width="90">EXP Form No</th>
                        <th width="70">EXP Form Date</th>
                        <th width="100">Ex-factory Qnty</th>
                        <th width="100">Invoice Qnty.</th>
						<th width="100">Invoice Qnty. Pcs</th>

                        <th width="100">CM/Dzn</th>
                        <th width="100">CM Value Per Pcs</th>
                        <th width="100">Total CM as per Invoice Qty</th>
                        
                        <th width="80">Num. Of Ctn Qnty.</th>
                        <th width="80">Avg Price</th>
                        <th width="100">Invoice value</th>
                        <th width="80">Discount</th>
                        <th width="100">Discount For At Sight Payment Amount</th>
                        <th width="70">Stamp Value</th>
                        <th width="70">Inspection Amount</th>
                        <th width="70">Claim</th>
                        <th width="80">Commission</th>
                        <th width="80">Other Deduction</th>
                        <th width="80">Upcharge</th>
                        <th width="100">Net Invoice Amount</th>
                        <th width="80">Currency</th>

                         <th width="70">ETD Date</th>
                        <th width="70">Ex-Factory Date</th>
                        <th width="70">Actual Ship Date</th>

                        <th width="70">Possible BL Date</th>
                        <th width="100">Copy B/L No</th>
                        <th width="70">Copy B/L Date</th>
                        <th width="70">B/L Days</th>
                        <th width="70">Org B/L Rcv Date</th>
                        <th width="70">Shipping Bill No</th>
                        <th width="70">Shipping Bill Date</th>
                        <th width="70">Possible GSP Date</th>
                        <th width="70">Actual GSP Date</th>
                        <th width="100">GSP No.</th>
                        <th width="70">Possible CO Date</th>
                        <th width="70">Actual CO Date</th>
                        <th width="70">GSP Cour. Date</th>
                        <th width="70">I/C Rcv Date</th>
                        <th width="70">Document In Hand</th>

                        <th width="70">Possible Buyer Sub date</th>
                        <th width="70">Actual Buyer Sub Date</th>
                        <th width="70">Buyer Sub Days</th>
                        <th width="70">Possible Bank Sub date</th>
                        <th width="70">Actual Bank Sub Date</th>
                        <th width="70">Bank Sub Days</th>
                        <th width="100">Bank Bill No.</th>
                        <th width="70">Bank Bill Date</th>

                        <th width="70">Shipping Bill No</th>
                        <th width="70">Shipping Bill Date</th>
                        <th width="70">Gross Weight</th>
                        <th width="70">Net weight</th>

                        <th width="80">Pay Term</th>
                        <th width="80">LC Tenor</th>
                        <th width="70">Possible Rlz. Date</th>
                        <th width="70">Actual Realized Date</th>
                        <th width="70">Realization Days</th>
                        <th width="70">Realization Amount</th>
                        <th width="100">Remarks</th>
                        <th width="100">Feeder Vessle</th>
                        <th width="100">Mother Vessle</th>
                        <th width="70">ETA Dest.</th>
                        <th >B TO B Courier No</th>

                    </tr>
                </thead>
            </table>
            <div style="width:6350px; overflow-y:scroll; max-height:290px;font-size:12px; overflow-x:hidden;" id="scroll_body" align="left">
	            <table width="6330" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
	                <tbody>
	                <?	                
	                foreach($sql_re as $row_result)
	                {
						$exfact_qnty=$exfact_qnty_arr[$row_result[csf('id')]]-$return_qty_arr[$row_result[csf('id')]];
						$print_data=0;
						if($cbo_based_on==2)
						{
							if($exfact_qnty>0) $print_data=1; else $print_data=0;
						}
						else
						{
							$print_data=1;
						}
						
						if($print_data)
						{
							if ($k%2==0)
							$bgcolor="#E9F3FF";
							else
							$bgcolor="#FFFFFF";
							$id=$row_result[csf('id')];
							$exfact_date_calculate=$row_result[csf('ex_factory_date')];//$variable_standard_arr
							$bl_date_calculate=$row_result[csf('bl_date')];
							$variable_standard_bl_day=$possiable_bl_date=$realization_sub_day=$doc_presentation_days=$possiable_bank_sub_date=$variable_standard_gsp_day=$possiable_gsp_date=$variable_standard_co_day=$possiable_co_date="";
							if($exfact_date_calculate!="" && $exfact_date_calculate!='0000-00-00')
							{
								$variable_standard_bl_day=$variable_standard_arr[1]*60*60*24;
								$possiable_bl_date=date('d-m-Y',strtotime($exfact_date_calculate)+$variable_standard_bl_day);
	
							}
							if($bl_date_calculate!="" && $bl_date_calculate!='0000-00-00')
							{
								if($row_result[csf("type")]==1)
								{
									$realization_sub_day=$bank_sub_data[$row_result[csf('id')]]["submit_date"];
									$doc_presentation_days=$row_result[csf("doc_presentation_days")]*60*60*24;
									$possiable_bank_sub_date=date('d-m-Y',strtotime($bl_date_calculate)+$doc_presentation_days);
								}
								if($row_result[csf("type")]==2)
								{
									$realization_sub_day=$buyer_submit_date_arr[$row_result[csf('id')]];
									$doc_presentation_days=$row_result[csf("doc_presentation_days")]*60*60*24;
									$possiable_buyer_sub_date=date('d-m-Y',strtotime($bl_date_calculate)+$doc_presentation_days);
								}
								$variable_standard_gsp_day=$variable_standard_arr[2]*60*60*24;
								$possiable_gsp_date=date('d-m-Y',strtotime($bl_date_calculate)+$variable_standard_gsp_day);
								$variable_standard_co_day=$variable_standard_arr[3]*60*60*24;
								$possiable_co_date=date('d-m-Y',strtotime($bl_date_calculate)+$variable_standard_co_day);
	
							}
							$po_id_arr=explode(",",$row_result['PO_ID']);
							$cm_dzn=$cm_dzn_per=$cm_dzn_inv_qty=0;
							$cm_dzn_popup=$cm_dzn_per_popup=$cm_invoice_dzn_per_popup='';
							$m=0;
							foreach($po_id_arr as $val){
								$cm_dzn+=$cmPerDzn[$val];
								$cm_dzn_inv_qty+=($cmPerDzn[$val]/$costPerDzn[$val])*$inv_po_qnty_pcs_arr[$row_result['ID']][$val];
								$cm_dzn_per+=$cmPerDzn[$val]/$costPerDzn[$val];
								$cm_dzn_popup.=$val."**".$cmPerDzn[$val].",";
								$cm_dzn_per_popup.=$val."**".$cmPerDzn[$val]/$costPerDzn[$val].",";
								$cm_invoice_dzn_per_popup.=$val."**".($cmPerDzn[$val]/$costPerDzn[$val])*$inv_po_qnty_pcs_arr[$row_result['ID']][$val].",";
								$m++;
							}
							$cm_dzn_popup=rtrim($cm_dzn_popup,',');
							$cm_dzn_per_popup=rtrim($cm_dzn_per_popup,',');
							$cm_invoice_dzn_per_popup=rtrim($cm_invoice_dzn_per_popup,',');
							
							$additional_info=$import_btb=$export_item_category="";
							if ($format_id[0]==788){
								$action="pdf";
								$invoice_variable="<a href='#' style='color:#000' title='".$format_id[0]."' onclick=\"fn_print_link('".$action."',".$format_id[0].",".$id.",".$row_result[csf('buyer_id')].");\"><font color='blue'><b>".$row_result[csf('invoice_no')]."</b></font><a/>";
							} else if ($format_id[0]==85) {
								$action="print_invoice3";
								$invoice_variable="<a href='#' style='color:#000' title='".$format_id[0]."' onclick=\"fn_print_link('".$action."',".$format_id[0].",".$id.",".$row_result[csf('buyer_id')].");\"><font color='blue'><b>".$row_result[csf('invoice_no')]."</b></font><a/>";
							} else if ($format_id[0]==84) {
								$action="invoice_report_print_2";
								$additional_info=$row_result[csf("cargo_delivery_to")].'_'.$row_result[csf("place_of_delivery")].'_'.$row_result[csf("main_mark")].'_'.$row_result[csf("side_mark")].'_'.$row_result[csf("net_weight")].'_'.$row_result[csf("gross_weight")].'_'.$row_result[csf("cbm_qnty")].'_'.$row_result[csf("delv_no")].'_'.$row_result[csf("consignee")].'_'.$row_result[csf("notifying_party")].'_'.$row_result[csf("item_description")];
								$invoice_variable="<a href='#' style='color:#000' title='".$format_id[0]."' onclick=\"fn_print_link('".$action."',".$format_id[0].",".$id.",".$additional_info.");\"><font color='blue'><b>".$row_result[csf('invoice_no')]."</b></font><a/>";
							} else if ($format_id[0]==150) {
								$action="print_invoice";
								$invoice_variable="<a href='#' style='color:#000' title='".$format_id[0]."' onclick=\"fn_print_link('".$action."',".$format_id[0].",".$id.",".$row_result[csf('buyer_id')].");\"><font color='blue'><b>".$row_result[csf('invoice_no')]."</b></font><a/>";
							} else if ($format_id[0]==789) {
								$action="invoice_report_print";
								$additional_info=$row_result[csf("cargo_delivery_to")].'_'.$row_result[csf("place_of_delivery")].'_'.$row_result[csf("main_mark")].'_'.$row_result[csf("side_mark")].'_'.$row_result[csf("net_weight")].'_'.$row_result[csf("gross_weight")].'_'.$row_result[csf("cbm_qnty")].'_'.$row_result[csf("delv_no")].'_'.$row_result[csf("consignee")].'_'.$row_result[csf("notifying_party")].'_'.$row_result[csf("item_description")];
								$invoice_variable="<a href='#' style='color:#000' title='".$format_id[0]."' onclick=\"fn_print_link('".$action."',".$format_id[0].",".$id.",'".$additional_info."');\"><font color='blue'><b>".$row_result[csf('invoice_no')]."</b></font><a/>";
							} else if ($format_id[0]==790 || $format_id[0]==792 || $format_id[0]==793 || $format_id[0]==794 || $format_id[0]==795 || $format_id[0]==796) {
								$action="print_generate";
								$export_item_category=0;
								if ($format_id[0]==790) $type=1;
								else if ($format_id[0]==792) $type=2;
								else if ($format_id[0]==793) $type=3;
								else if ($format_id[0]==794) $type=4;							
								else if ($format_id[0]==795) $type=5;
								else if ($format_id[0]==796) $type=6;
								if ($row_result[csf("is_lc")]==1) $export_item_category=$row_result[csf("export_item_category")];
								$additional_info=$row_result[csf("import_btb")].'_'.$export_item_category.'_'.$type;
								$invoice_variable="<a href='#' style='color:#000' title='".$format_id[0]."' onclick=\"fn_print_link('".$action."',".$format_id[0].",".$id.",'".$additional_info."');\"><font color='blue'><b>".$row_result[csf('invoice_no')]."</b></font><a/>";
							} else if ($format_id[0]==791) {
								$action="invoice_report_print_ci3";
								$invoice_variable="<a href='#' style='color:#000' title='".$format_id[0]."' onclick=\"fn_print_link('".$action."',".$format_id[0].",".$id.",'".$row_result[csf('buyer_id')]."');\"><font color='blue'><b>".$row_result[csf('invoice_no')]."</b></font><a/>";
							} else if ($format_id[0]==797) {
								$action="print_invoice_CIHnM";
								$invoice_variable="<a href='#' style='color:#000' title='".$format_id[0]."' onclick=\"fn_print_link('".$action."',".$format_id[0].",".$id.",'".$row_result[csf('buyer_id')]."');\"><font color='blue'><b>".$row_result[csf('invoice_no')]."</b></font><a/>";
							} else if ($format_id[0]==798) {
								$action="invoice_report_print_ci_ny";
								$additional_info=$row_result[csf("cargo_delivery_to")].'_'.$row_result[csf("place_of_delivery")].'_'.$row_result[csf("main_mark")].'_'.$row_result[csf("side_mark")].'_'.$row_result[csf("net_weight")].'_'.$row_result[csf("gross_weight")].'_'.$row_result[csf("cbm_qnty")].'_'.$row_result[csf("delv_no")].'_'.$row_result[csf("consignee")].'_'.$row_result[csf("notifying_party")].'_'.$row_result[csf("item_description")];
								$invoice_variable="<a href='#' style='color:#000' title='".$format_id[0]."' onclick=\"fn_print_link('".$action."',".$format_id[0].",".$id.",'".$additional_info."');\"><font color='blue'><b>".$row_result[csf('invoice_no')]."</b></font><a/>";
							} else $invoice_variable=$row_result[csf('invoice_no')];
							
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
								<td width="50" style="word-break: break-all;text-align: center;"><? echo $k;//$row_result[csf('id')];?></td>
								<td width="100" style="word-break: break-all;"><? echo  $company_arr[$row_result[csf('benificiary_id')]]; ?></td>
	
								<td width="100" style="word-break: break-all;"><? echo $country_arr[$row_result[csf('country_id')]];?></td>
							   
								<td width="70" style="word-break: break-all;"><? echo  $buyer_arr[$row_result[csf('buyer_id')]];?></td>
								<td width="100" style="word-break: break-all;">
									<? echo $invoice_variable; ?>
								</td>
								<td width="50"><p>
								<? //echo $row[csf('id')]]."==";
									$file_name=$system_file_arr[$row_result[csf('id')]]['file'];
									if( $file_name!="")
									{
										?>
										<input type="button" class="image_uploader" id="fileno_<? echo $i;?>" style="width:50px" value="File" onClick="openmypage_file(<? echo $k; ?>,'1')"/>
										<input type="hidden" class="text_boxes" id="sysid_<? echo $k;?>" name="sysid_<? echo $k;?>" value="<? echo $row_result[csf('id')];?>" style="width:45px" />
										<?	  
									}
								?>
								</p></td>
								<td width="180" style="word-break: break-all;"><p><? echo chop($inv_ref_data[$row_result[csf('id')]],",");?>&nbsp;</p></td>
								<td width="70" align="center" style="word-break: break-all;">
									<? if($row_result[csf('invoice_date')]!="0000-00-00" && $row_result[csf('invoice_date')]!="") {echo change_date_format($row_result[csf('invoice_date')]);} else {echo "&nbsp;";}?>
								</td>
								<td width="70"  align="center" style="word-break: break-all;">
									<? if($row_result[csf('insert_date')]!="0000-00-00" && $row_result[csf('insert_date')]!="") {echo change_date_format($row_result[csf('insert_date')]);} else {echo "&nbsp;";}?>
								</td>
								<td width="80" style="word-break: break-all;">
									<? echo $shipment_mode[$row_result[csf('shipping_mode')]];?>
								</td>
								<td width="70"  align="center" style="word-break: break-all;">
									<? if($row_result[csf('type')] == 1) echo "LC"; else echo "SC"; ?>
								</td>
								<td width="100" style="word-break: break-all;"><? echo $row_result[csf('lc_sc_no')];?></td>
								<td width="100" style="word-break: break-all;"><? echo $row_result[csf('internal_file_no')];?></td>
	
								<td width="100" style="word-break: break-all;"><? echo  $supplier_arr[$row_result[csf('forwarder_name')]];?></td>
								<td width="150" style="word-break: break-all;"><? echo $bank_arr[$row_result[csf('lien_bank')]];?></td>
								<td width="90" style="word-break: break-all;"><? echo $row_result[csf('exp_form_no')]."&nbsp;";?></td>
								<td width="70"  align="center" style="word-break: break-all;"><?  if($row_result[csf('exp_form_date')]!="0000-00-00" && $row_result[csf('exp_form_date')]!="") {echo change_date_format($row_result[csf('exp_form_date')]);} else {echo "&nbsp;";} ?></td>
								<td width="100" align="right" style="word-break: break-all;"><? //echo number_format($exfact_qnty_arr[$row_result[csf('id')]],2);
								 //$total_ex_fact_qnty+=$exfact_qnty_arr[$row_result[csf('id')]]; ?>
								<a href="##" onClick="generate_ex_factory_popup('ex_factory_popup','<? echo $row_result[csf('country_id')];?>','<? echo $row_result[csf('id')]; ?>','680px')">
								<? 
								
								echo  number_format($exfact_qnty,2);
								$total_ex_fact_qnty+=$exfact_qnty;
								?></a>
	
								</td>
								<td width="100" align="right" style="word-break: break-all;"><a href='#report_detals'  onclick= "openmypage('<? echo $id; ?>','<? echo $k; ?>')"><? echo number_format($row_result[csf('invoice_quantity')],2); $total_invoice_qty +=$row_result[csf('invoice_quantity')];?></a></td>
								
								<td width="100" align="right" style="word-break: break-all;"><? echo number_format($inv_qnty_pcs_arr[$row_result[csf('id')]],2); $total_invoice_qty_pcs +=$inv_qnty_pcs_arr[$row_result[csf('id')]];?></td>
	
								<td width="100" align="right" style="word-break: break-all;"><a href='#report_detals'  onclick= "openmypage_cm('1','<? echo $cm_dzn_popup; ?>')"><? echo number_format($cm_dzn/$m,4);?></a></td>
								<td width="100" align="right" style="word-break: break-all;"><a href='#report_detals'  onclick= "openmypage_cm('2','<? echo $cm_dzn_per_popup; ?>')"><? echo number_format($cm_dzn_per/$m,4);?></a></td>
								<td width="100" align="right" style="word-break: break-all;"><a href='#report_detals'  onclick= "openmypage_cm('3','<? echo $cm_invoice_dzn_per_popup; ?>')"><? echo number_format($cm_dzn_inv_qty,4); $total_cm_asper_invoice_qty +=$cm_dzn_inv_qty; //echo number_format($cm_dzn_inv_qty/$m,4); ?></a></td>
	
								<td width="80" align="right" style="word-break: break-all;"><? echo number_format($row_result[csf('total_carton_qnty')],2); $total_carton_qty +=$row_result[csf('total_carton_qnty')];?></td>
								<td width="80" align="right" style="word-break: break-all;">
									<? $avg_price=$row_result[csf('invoice_value')]/$row_result[csf('invoice_quantity')];  
									echo number_format($avg_price,2); $total_avg_price +=$avg_price;?></td>
								<td width="100" align="right" style="word-break: break-all;"><? echo number_format($row_result[csf('invoice_value')],2,'.',''); $total_grs_value +=$row_result[csf('invoice_value')];?></td>	                         
								<td width="80" align="right" style="word-break: break-all;"><? echo number_format($row_result[csf('discount_ammount')],2,'.',''); $total_discount_value +=$row_result[csf('discount_ammount')]; ?></td>
								<td width="100" align="right" style="word-break: break-all;"><? echo number_format($row_result[csf('atsite_discount_amt')],2,'.',''); $atsite_discount_amt+= $row_result[csf('atsite_discount_amt')]; ?></td>
								<td width="70" align="right" style="word-break: break-all;"><? echo $stamp_value_array[$row_result[csf('stamp_value')]]; ?></td>
								<td width="70" align="right" style="word-break: break-all;"><? echo number_format($row_result[csf('bonus_ammount')],2,'.',''); $total_bonous_value +=$row_result[csf('bonus_ammount')];  ?></td>
								<td width="70" align="right" style="word-break: break-all;"><? echo number_format($row_result[csf('claim_ammount')],2,'.',''); $total_claim_value +=$row_result[csf('claim_ammount')];  ?></td>
								<td width="80" align="right" style="word-break: break-all;"><?  echo number_format($row_result[csf('commission')],2,'.',''); $total_commission_value +=$row_result[csf('commission')]; ?></td>
								<td width="80" align="right" style="word-break: break-all;"><?  echo number_format($row_result[csf('other_discount_amt')],2,'.',''); $total_other_discount_value +=$row_result[csf('other_discount_amt')]; ?></td>
								<td width="80" align="right" style="word-break: break-all;"><?  echo number_format($row_result[csf('upcharge')],2,'.',''); $total_upcharge_value +=$row_result[csf('upcharge')]; ?></td>
								<td width="100" align="right" style="word-break: break-all;"><? echo number_format($row_result[csf('net_invo_value')],2,'.',''); $total_order_qnty +=$row_result[csf('net_invo_value')];?></td>
								<td width="80" align="center" style="word-break: break-all;"><? echo $currency[$row_result[csf('currency_name')]];?></td>
	
								 <td width="70"  align="center" style="word-break: break-all;"><? if($row_result[csf('etd')]!="0000-00-00" && $row_result[csf('etd')]!="") echo change_date_format( $row_result[csf('etd')]); else echo "";?></td>
								<td width="70"  align="center" style="word-break: break-all;"><? if($row_result[csf('ex_factory_date')]!="0000-00-00" && $row_result[csf('ex_factory_date')]!="") {echo change_date_format($row_result[csf('ex_factory_date')]);} else {echo "&nbsp;";} ?></td>
								<td width="70"  align="center" style="word-break: break-all;"><?  if($row_result[csf('actual_shipment_date')]!="0000-00-00" && $row_result[csf('actual_shipment_date')]!="") {echo change_date_format($row_result[csf('actual_shipment_date')]);} else {echo "&nbsp;";}  ?></td>
	
								
	
								<td width="70"  align="center" style="word-break: break-all;" title="Ex-factory Date+Variable Standard Date"><? if($possiable_bl_date!="0000-00-00" && $possiable_bl_date!="") {echo change_date_format($possiable_bl_date);} else {echo "&nbsp;";} ?></td>							
								
								<td width="100" style="word-break: break-all;"><? echo $row_result[csf('bl_no')];?></td>
								<td width="70"  align="center" style="word-break: break-all;"><?  if($row_result[csf('bl_date')]!="0000-00-00" && $row_result[csf('bl_date')]!="") {echo change_date_format($row_result[csf('bl_date')]);} else {echo "&nbsp;";} ?></td>
								<td width="70" style="word-break: break-all;" title="exfactory date-bl date"  align="center"><?  $diff_bl=datediff("d",$exfact_date_calculate, $row_result[csf('bl_date')]); if($diff_bl>0) echo $diff_bl."days";  ?></td>
								<td width="70" style="word-break: break-all;"  align="center"><? if($row_result[csf('bl_rev_date')]!="0000-00-00" && $row_result[csf('bl_rev_date')]!="") {echo change_date_format($row_result[csf('bl_rev_date')]);} else { echo "&nbsp;";}?></td>
	
								<td width="70"  align="center" style="word-break: break-all;">
								<?  echo $row_result[csf('shipping_bill_n')]; ?></td>
	
								<td width="70"  align="center" style="word-break: break-all;">
								<?  echo change_date_format($row_result[csf('ship_bl_date')]);  ?></td>
	
								<td width="70"  style="word-break: break-all;" align="center" title="BL Date+Variable Standard Date"><? if($possiable_gsp_date!="0000-00-00" && $possiable_gsp_date!="") {echo change_date_format($possiable_gsp_date);} else {echo "&nbsp;";} ?></td>
	
								<td width="70" style="word-break: break-all;"  align="center">
								<? if(trim($row_result[csf('gsp_co_no_date')])!="0000-00-00" && trim($row_result[csf('gsp_co_no_date')])!="") {echo change_date_format($row_result[csf('gsp_co_no_date')]);}else {echo "&nbsp;";}?>
								</td>
								<td width="100" style="word-break: break-all;"><? echo $row_result[csf('gsp_co_no')];?></td>
								<td width="70" style="word-break: break-all;" align="center" title="BL Date+Variable Standard Date"><? if($possiable_co_date!="0000-00-00" && $possiable_co_date!="") {echo change_date_format($possiable_co_date);} else {echo "&nbsp;";} ?></td>
								<td width="70" style="word-break: break-all;" align="center"><? if($row_result[csf('co_date')]!="0000-00-00" && $row_result[csf('co_date')]!="") {echo change_date_format($row_result[csf('co_date')]);} else {echo "&nbsp;";} ?></td>
								<td width="70" style="word-break: break-all;" align="center"><?
								$curier_receipt_date=$bank_sub_data[$row_result[csf("id")]]["courier_date"];
								if(!(trim($curier_receipt_date)=="0000-00-00" || trim($curier_receipt_date)==""))
								{
									echo change_date_format($curier_receipt_date);
								}
								else
								{
									echo "&nbsp;";
								}
								?></td>
								<td width="70" style="word-break: break-all;" align="center"><? if($row_result[csf('ic_recieved_date')]!="0000-00-00" && $row_result[csf('ic_recieved_date')]!="") echo change_date_format( $row_result[csf('ic_recieved_date')]); else echo "";?></td>
								<td width="70" style="word-break: break-all;" title="exfactory date-bank submission date/current date"  align="center">
								<?
	
								if(($exfact_date_calculate!='0000-00-00') )
								{
									$current_date=date("Y-m-d");
									if($bank_sub_data[$row_result[csf('id')]]["submit_date"]=='0000-00-00' || $bank_sub_data[$row_result[csf('id')]]["submit_date"]=='')
									{
										$diff=datediff("d",$exfact_date_calculate, $current_date);
									}
									else
									{
										$diff=datediff("d",$exfact_date_calculate, $bank_sub_data[$row_result[csf('id')]]["submit_date"]);
									}
	
								}
								else
								{
									$diff="";
								}
							   if($diff>0) echo  $diff." days";
								?>
								</td>
	
								<td width="70"  style="word-break: break-all;"  align="center"  title="BL Date+Document Presentation Days"><? if($possiable_buyer_sub_date!="0000-00-00" && $possiable_buyer_sub_date!="") {echo change_date_format($possiable_buyer_sub_date);} else {echo "&nbsp;";} ?></td>
								<td width="70"   style="word-break: break-all;" align="center"><?
									if(trim($buyer_submit_date_arr[$row_result[csf('id')]])!='0000-00-00' && trim($buyer_submit_date_arr[$row_result[csf('id')]])!='') echo change_date_format(trim($buyer_submit_date_arr[$row_result[csf('id')]])); else echo "&nbsp;";
					          ?></td>
								<td width="70" style="word-break: break-all;" title="From BL Date To Buyer Submission Date"   align="center">
								<?
								$diff_buyer_sub=0;
								if($row_result[csf('bl_date')]!="0000-00-00" && $row_result[csf('bl_date')]!="")
								{
									$diff_buyer_sub=datediff("d",$row_result[csf('bl_date')], $buyer_submit_date_arr[$row_result[csf('id')]]);
								}
								else
								{
									$diff_buyer_sub=0;
								}
								 if($diff_buyer_sub>0) echo $diff_buyer_sub."days";
								?></td>
								<td width="70"  style="word-break: break-all;"  align="center"   title="BL Date+Document Presentation Days"><? if($possiable_bank_sub_date!="0000-00-00" && $possiable_bank_sub_date!="") {echo change_date_format($possiable_bank_sub_date);} else {echo "&nbsp;";} ?></td>
								<td width="70"  style="word-break: break-all;"  align="center">
								<?
									if(trim($bank_sub_data[$row_result[csf('id')]]["submit_date"])!='0000-00-00' && trim($bank_sub_data[$row_result[csf('id')]]["submit_date"])!='') echo change_date_format(trim($bank_sub_data[$row_result[csf('id')]]["submit_date"])); else echo "&nbsp;";
								?></td>
								<td width="70" style="word-break: break-all;" title="From BL Date To Bank Submission Date"   align="center">
								<?
								$diff_sub=0;
								if($row_result[csf('bl_date')]!="0000-00-00" && $row_result[csf('bl_date')]!="")
								{
									$diff_sub=datediff("d",$row_result[csf('bl_date')], $bank_sub_data[$row_result[csf('id')]]["submit_date"]);
								}
								else
								{
									$diff_sub=0;
								}
								 if($diff_sub>0) echo $diff_sub."days";
								?></td>
								<td width="100" style="word-break: break-all;"><? echo $bank_sub_data[$row_result[csf('id')]]["bank_ref_no"]; ?></td>
								<td width="70"  style="word-break: break-all;"  align="center"><?
	
								if(!(trim($bank_sub_data[$row_result[csf('id')]]["submit_date"])=="0000-00-00" || trim($bank_sub_data[$row_result[csf('id')]]["submit_date"])==""))
								{
								   echo change_date_format($bank_sub_data[$row_result[csf('id')]]["submit_date"]);
								}
								else
								{
									echo "&nbsp;";
								}
								?></td>
	
								<td width="70" style="word-break: break-all;"><? echo $row_result[csf('shipping_bill_n')]; ?></td>
								<td width="70" style="word-break: break-all;"><?  if($row_result[csf('ship_bl_date')]!="0000-00-00" && $row_result[csf('ship_bl_date')]!="") {echo change_date_format($row_result[csf('ship_bl_date')]);} else {echo "&nbsp;";}  ?></td>
								<td width="70" style="word-break: break-all;"><?
								$total_carton_gross_weight += $row_result[csf('carton_gross_weight')]; 
								echo $row_result[csf('carton_gross_weight')];
								?></td>
								<td width="70" style="word-break: break-all;"><? 
								$total_carton_net_weight += $row_result[csf('carton_net_weight')];
								echo $row_result[csf('carton_net_weight')];
								?></td>
	
								<td width="80" style="word-break: break-all;"><? echo $pay_term[$row_result[csf('pay_term')]];?></td>
								<td width="80" style="word-break: break-all;"><? echo $row_result[csf('tenor')];?></td>
								<td width="70"  style="word-break: break-all;"  align="center">
								<?
								if($cbo_based_on ==2)
								{
									$possiable_rls_date=add_date($row_result[csf('ex_factory_date')], $row_result[csf('tenor')]);
									echo change_date_format($possiable_rls_date);
								}
								else
								{
									if(!(trim($bank_sub_data[$row_result[csf('id')]]["possible_reali_date"])=="0000-00-00" || trim($bank_sub_data[$row_result[csf('id')]]["possible_reali_date"])==""))
									{
										echo change_date_format($bank_sub_data[$row_result[csf('id')]]["possible_reali_date"]);
									}
									else
									{
										echo "&nbsp;";
									}
								}
								?>
								</td>
								<td width="70"  style="word-break: break-all;"  align="center">
								<?
								if(!(trim($rlz_date_arr[$row_result[csf('id')]])=="0000-00-00" || trim($rlz_date_arr[$row_result[csf('id')]])==""))
								{
									echo change_date_format($rlz_date_arr[$row_result[csf('id')]]);
								}
								else
								{
									echo "&nbsp;";
								}
								 ?>
								</td>
								<td width="70" style="word-break: break-all;" align="center" title="From Bank or Buyer Submit Date To Actual Realization Date">
								<?
								if( $realization_sub_day!="" && $realization_sub_day!='0000-00-00' &&$rlz_date_arr[$row_result[csf('id')]]!="" && $rlz_date_arr[$row_result[csf('id')]]!='0000-00-00')
								{
									$diff_rlz=datediff("d",$realization_sub_day,$rlz_date_arr[$row_result[csf('id')]]);
								}
								if($diff_rlz>0) echo $diff_rlz." days";
								?></td>
								<td width="70" style="word-break: break-all;" align="right"><? if(!(trim($rlz_date_arr[$row_result[csf('id')]])=="0000-00-00" || trim($rlz_date_arr[$row_result[csf('id')]])==""))
								{
									echo number_format($row_result[csf('net_invo_value')],2,'.',''); $total_rlz_amt+=$row_result[csf('net_invo_value')];
								}
								else
								{
									echo "";
								}
								?></td>
								<td width="100" style="word-break: break-all;"><? echo $row_result[csf('remarks')];?></td>
								<td width="100" style="word-break: break-all;"><? echo $row_result[csf('feeder_vessel')];?></td>
								<td width="100" style="word-break: break-all;"><? echo $row_result[csf('mother_vessel')];?></td>
								<td width="70" style="word-break: break-all;" align="center"><?  if($row_result[csf('etd_destination')]!="0000-00-00" && $row_result[csf('etd_destination')]!="") {echo change_date_format($row_result[csf('etd_destination')]);} else { echo "&nbsp;";} ?></td>
								<td ><? echo $bank_sub_data[$row_result[csf('id')]]["bnk_to_bnk_cour_no"];  ?></td>
							</tr>
							<?
							$k++;
						}
	                }
	                ?>
	                </tbody>
	            </table>
	     		<table width="6330" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="report_table_footer" align="left">
	            	<tfoot>
	                    <tr>
	                        <th width="50">&nbsp;</th>
	                        <th width="100">&nbsp;</th>

	                        <th width="100">&nbsp;</th>
							<th width="70">&nbsp;</th>
	                        <th width="100">&nbsp;</th>
	                        <th width="50">&nbsp;</th>

	                        <th width="180">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="80">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="100">&nbsp;</th>
	                        <th width="100">&nbsp;</th>

	                        <th width="100">&nbsp;</th>
	                        <th width="150">&nbsp;</th>
	                        <th width="90">&nbsp;</th>
	                        <th width="70">Total:</th>
	                        <th width="100" id="total_ex_fact_qnty" align="right"><? echo number_format($total_ex_fact_qnty,2); ?></th>
	                        <th width="100" id="total_invoice_qty" align="right"><? echo number_format($total_invoice_qty,2); ?></th>
							<th width="100" id="total_invoice_qty_pcs" align="right"><? echo number_format($total_invoice_qty_pcs,2); ?></th>
	                        <th width="100" ></th>
	                        <th width="100" ></th>
	                        <th width="100" id="value_total_cm_asper_invoice_qty" align="right"><? echo number_format($total_cm_asper_invoice_qty,2); ?></th>
	                        <th width="80" id="total_carton_qty" align="right"><? echo number_format($total_carton_qty,2); ?></th>
	                        <th width="80" id="value_total_avg_price"  align="right"><? echo number_format($total_avg_price,2);  ?></th>
	                        <th width="100" id="value_total_grs_value"  align="right"><? echo number_format($total_grs_value,2);  ?></th>
	                        <th width="80" id="value_total_discount_value"  align="right"><? echo number_format($total_discount_value,2);  ?></th>
	                        <th width="100" id="value_atsite_discount_amt" > <?=number_format($atsite_discount_amt,2)?></th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70" id="value_total_bonous_value"  align="right"><? echo number_format($total_bonous_value,2);  ?></th>
	                        <th width="70" id="value_total_claim_value"  align="right"><? echo number_format($total_claim_value,2);  ?></th>
	                        <th width="80" id="value_total_commission_value"  align="right"><? echo number_format($total_commission_value,2);  ?></th>
							
	                        <th width="80" id="value_total_other_discount_value" align="right"><? echo number_format($total_other_discount_value,2); ?></th>
	                        <th width="80" id="value_total_upcharge_value" align="right"><? echo number_format($total_upcharge_value,2);?></th>
	                        <th width="100"  id="value_total_net_invo_value"  align="right"><? echo number_format($total_order_qnty,2);  ?></th>
	                        <th width="80">&nbsp;</th>

	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>

	                        
	                        <th width="100">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="100">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>

	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="100">&nbsp;</th>
	                        <th width="70">&nbsp;</th>

	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70" id="value_total_carton_gross_weight"  align="right"><? echo number_format($total_carton_gross_weight,2);  ?></th>
	                        <th width="70" id="value_total_carton_net_weight"  align="right"><? echo number_format($total_carton_net_weight,2);  ?></th>

	                        <th width="80">&nbsp;</th>
	                        <th width="80">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70"  id="value_total_rlz_amt"  align="right"><? echo number_format($total_rlz_amt,2);  ?></th>
	                        <th width="100">&nbsp;</th>
	                        <th width="100">&nbsp;</th>
	                        <th width="100">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th >&nbsp;</th>
	                    </tr>
	                </tfoot>
	            </table>
     		</div>
    	</div>
    	<div align="left" style="font-weight:bold; margin-left:30px;"><? echo "User Id : ". $user_arr[$user_id] ." , &nbsp; THIS IS SYSTEM GENERATED STATEMENT, NO SIGNATURE REQUIRED ."; ?></div>
		<?
		foreach (glob("$user_id*.xls") as $filename)
		{

			if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename=$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc,ob_get_contents());
		//$filename=$user_id."_".$name.".xls";
		echo "$total_data****".$RptType."****".$filename;
		//echo "****".$RptType;
	}
	else if ($RptType == 2) // SHORT
	{
		$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$buyer_arr=return_library_array("select id,buyer_name from lib_buyer","id","buyer_name");
		$cbo_company_name=str_replace("'","",$cbo_company_name);
		$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
		$txt_date_from=str_replace("'","",$txt_date_from);
		$txt_date_to=str_replace("'","",$txt_date_to);
		$shipping_mode=str_replace("'","",$shipping_mode);
		$txt_invoice_no=str_replace("'","",$txt_invoice_no);
		$ship_cond="";
		if($cbo_company_name!=0) $cbo_company_name = $cbo_company_name; else $cbo_company_name="%%";
		if($cbo_buyer_name != 0) $cbo_buyer_name = $cbo_buyer_name;  else $cbo_buyer_name="%%";
		if($shipping_mode!=0) $ship_cond=" and a.shipping_mode=$shipping_mode";
		if($txt_invoice_no!="") $ship_cond.=" and a.invoice_no like '%$txt_invoice_no%'";

		if($cbo_ascending_by!="" && $cbo_ascending_by==1 ) 
		{
			$ascending_by = "invoice_no";
			$ascendig_cond=" order by $ascending_by asc ";
		} elseif ($cbo_ascending_by!="" && $cbo_ascending_by==2) {
			$ascending_by = "invoice_date";
			$ascendig_cond=" order by $ascending_by asc ";
		} elseif ($cbo_ascending_by!="" && $cbo_ascending_by==3) {
			$ascending_by = "exp_form_no";
			$ascendig_cond=" order by $ascending_by asc ";
		}elseif ($cbo_ascending_by!="" && $cbo_ascending_by==4) {
			$ascending_by = "exp_form_date";
			$ascendig_cond=" order by $ascending_by asc ";
		}else{
			$ascendig_cond="";
		}

		$date_cond="";
		//if($txt_date_from!="" && $txt_date_to!="")  $date_cond=" and a.invoice_date between '$txt_date_from' and '$txt_date_to'";
		if ($txt_date_from!="" && $txt_date_to!="")
		{
			if($cbo_based_on ==0)
			{
				$str_cond=" and a.invoice_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==1)
			{
				$str_cond=" and a.invoice_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==2)
			{
				$str_cond=" and a.ex_factory_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==3)
			{
				$str_cond=" and a.actual_shipment_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==4)
			{
				$str_cond=" and a.bl_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==5)
			{
				$str_cond=" and a.ship_bl_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==6)
			{
				$str_cond=" and a.invoice_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==7)
			{

				if($db_type==0) $str_cond=" and a.insert_date between '$txt_date_from 00:00:01' and  '$txt_date_to 23:59:59'";
				else if($db_type==2) $str_cond=" and a.insert_date between '$txt_date_from 12:00:01 AM' and  '$txt_date_to 11:59:59 PM'";
			}
			else if($cbo_based_on ==8)
			{
				$str_cond=" and a.invoice_date between '$txt_date_from' and  '$txt_date_to'";
			}
		}
		else
		{
			$str_cond="";
		}

		$txt_int_ref_no=str_replace("'","",$txt_int_ref_no);
		$ref_inv_cond="";
		$ref_inv_id_arr=array();
		if($txt_int_ref_no!="")
		{
			$sql_ref="select a.mst_id as INV_ID from com_export_invoice_ship_dtls a, wo_po_break_down b where a.po_breakdown_id=b.id and a.status_active=1 and a.is_deleted=0 and b.grouping like '%$txt_int_ref_no%'";
			//echo $sql_ref;die;
			$sql_ref_result=sql_select($sql_ref);
			foreach($sql_ref_result as $row)
			{
				$ref_inv_id_arr[$row["INV_ID"]]=$row["INV_ID"];
			}
			if(count($ref_inv_id_arr)<1)
			{
				echo "No Data Found.";die;
			}
		}
		
		if(count($ref_inv_id_arr)>0)
		{
			$sql_inv_ref="select a.mst_id as INV_ID, b.id, b.po_number, b.po_quantity, b.grouping from com_export_invoice_ship_dtls a, wo_po_break_down b where a.po_breakdown_id=b.id and a.status_active=1 and a.is_deleted=0 and a.mst_id in(".implode(",",$ref_inv_id_arr).")";
			$ref_inv_cond=" and a.id in(".implode(",",$ref_inv_id_arr).")";
		}
		else
		{
			$sql_inv_ref="select id, po_number, po_quantity, grouping from wo_po_break_down where status_active=1 and is_deleted=0";
		}
		$order_data_arr=array();
		$sql_inv_ref_result=sql_select($sql_inv_ref);
		$inv_ref_data=array();
		foreach($sql_inv_ref_result as $row)
		{
			$order_data_arr[$row[csf("id")]]["po_number"]=$row[csf("po_number")];
			$order_data_arr[$row[csf("id")]]["po_quantity"]=$row[csf("po_quantity")];
			$order_data_arr[$row[csf("id")]]["internal_ref"]=$row[csf("grouping")];
		}
		unset($sql_inv_ref_result);
               
		$sql="select a.id, a.invoice_no, a.invoice_date, a.buyer_id, a.benificiary_id, a.commission, a.shipping_mode, b.id as dtls_id, b.po_breakdown_id, b.current_invoice_rate, b.current_invoice_qnty, b.current_invoice_value 
		from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.benificiary_id like '$cbo_company_name' and a.buyer_id like '$cbo_buyer_name' $str_cond $ship_cond $ascendig_cond $ref_inv_cond";
		//echo $sql;

        $sql_re=sql_select($sql);
		$invoice_data=array();
		foreach($sql_re as $row)
		{
			$invoice_data[$row[csf("id")]][$row[csf("dtls_id")]]['dtls_id']=$row[csf("dtls_id")];
			$invoice_data[$row[csf("id")]][$row[csf("dtls_id")]]['invoice_no']=$row[csf("invoice_no")];
			$invoice_data[$row[csf("id")]][$row[csf("dtls_id")]]['invoice_date']=$row[csf("invoice_date")];
			$invoice_data[$row[csf("id")]][$row[csf("dtls_id")]]['buyer_id']=$row[csf("buyer_id")];
			$invoice_data[$row[csf("id")]][$row[csf("dtls_id")]]['benificiary_id']=$row[csf("benificiary_id")];
			$invoice_data[$row[csf("id")]][$row[csf("dtls_id")]]['commission']=$row[csf("commission")];
			$invoice_data[$row[csf("id")]][$row[csf("dtls_id")]]['po_breakdown_id']=$row[csf("po_breakdown_id")];
			$invoice_data[$row[csf("id")]][$row[csf("dtls_id")]]['current_invoice_rate']=$row[csf("current_invoice_rate")];
			$invoice_data[$row[csf("id")]][$row[csf("dtls_id")]]['current_invoice_value']=$row[csf("current_invoice_value")];
			$invoice_data[$row[csf("id")]][$row[csf("dtls_id")]]['shipping_mode']=$row[csf("shipping_mode")];
			$invoice_data[$row[csf("id")]][$row[csf("dtls_id")]]['current_invoice_qnty']=$row[csf("current_invoice_qnty")];

		}
		$order_sql=sql_select("select id, po_number, po_quantity, grouping from wo_po_break_down");
		$order_data_arr=array();
		foreach($order_sql as $row)
		{
			$order_data_arr[$row[csf("id")]]["po_number"]=$row[csf("po_number")];
			$order_data_arr[$row[csf("id")]]["po_quantity"]=$row[csf("po_quantity")];
			$order_data_arr[$row[csf("id")]]["internal_ref"]=$row[csf("grouping")];
		}
		ob_start();
		?>
        <div style="width:1450px">
            <table width="1430" cellpadding="0" cellspacing="0" id="caption">
                <tr>
                	<td align="center" width="100%" colspan="13" class="form_caption" ><strong style="font-size:18px">Company Name:<? echo " ". $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
                </tr>
                <tr>
                	<td align="center" width="100%" colspan="13" class="form_caption" ><strong style="font-size:18px"><? echo $report_title; ?></strong></td>
                </tr>
            </table>
            <table width="1430" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_short" align="left">
                <thead>
                    <tr>
                        <th width="40">Sl</th>
                        <th width="130">Invoice No</th>
                        <th width="80">Invoice Date</th>
                        <th width="130">Order No</th>
                        <th width="90">Internal Ref.</th>
                        <th width="100">Order Qnty</th>
                        <th width="100">Ship Qnty (Invoice)</th>
                        <th width="80">Short Qnty</th>
                        <th width="80">Extra Qnty</th>
                        <th width="70">Unite Price</th>
                        <th width="100">Invoice Value</th>
                        <th width="100">Short Qnty Value</th>
                        <th width="100">Extra Qnty Value</th>
                        <th width="100">Total Commission</th>
                        <th width="100">Ship Mode</th>
                        <th width="100" >Buyer Name</th>
                    </tr>
                </thead>
            </table>
            <div style="width:1450px; overflow-y:scroll; max-height:310px;font-size:12px; overflow-x:hidden;" id="scroll_body">
            <table width="1430" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
                <tbody>
	                <?
					$k=1;$temp_arr=$temp_arr2=array();$i=1;$j=1;
	                foreach($invoice_data as $inv_id=>$row_val)
	                {
						if($j!=1)
						{
							?>
							<tr bgcolor="#CCCCCC">
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>Sub Total:</td>
								<td align="right"><? echo number_format($sub_order_qnty,0); ?></td>
								<td align="right"><? echo number_format($sub_ship_qnty,0); ?></td>
								<td align="right"><? echo number_format($sub_short_qnty,0); ?></td>
								<td align="right"><? echo number_format($sub_extra_qnty,0); ?></td>
								<td>&nbsp;</td>
								<td align="right"><? echo number_format($sub_ship_value,2); ?></td>
								<td align="right"><? echo number_format($sub_short_value,2); ?></td>
								<td align="right"><? echo number_format($sub_extra_value,2); ?></td>
								<td>&nbsp;</td>
	                            <td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							<?
							$sub_order_qnty=$sub_ship_qnty=$sub_short_qnty=$sub_extra_qnty=$sub_ship_value=$sub_short_value=$sub_extra_value=0;
						}
						$j++;
						$row_count=count($row_val);
						$flag=$flag2=1;
						foreach($row_val as $row_result)
						{
							if ($k%2==0)
							$bgcolor="#E9F3FF";
							else
							$bgcolor="#FFFFFF";
							$extra_qnty=$short_qnty=$short_value=$extra_value=0;
							if($order_data_arr[$row_result[('po_breakdown_id')]]["po_quantity"]>$row_result[('current_invoice_qnty')]) $short_qnty=$order_data_arr[$row_result[('po_breakdown_id')]]["po_quantity"]-$row_result[('current_invoice_qnty')];
							if($order_data_arr[$row_result[('po_breakdown_id')]]["po_quantity"]<$row_result[('current_invoice_qnty')]) $extra_qnty=$row_result[('current_invoice_qnty')]-$order_data_arr[$row_result[('po_breakdown_id')]]["po_quantity"];
							$short_value=$short_qnty*$row_result[('current_invoice_rate')];
							$extra_value=$extra_qnty*$row_result[('current_invoice_rate')];
							//$short_qnty=0;
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
								<?
								if($flag==1)
								{
									?>
	                                <td  width="35" rowspan="<? echo $row_count; ?>" align="center" valign="top"><? echo $i;?>&nbsp;</td>
	                                <td  width="130" rowspan="<? echo $row_count; ?>" valign="top"><? echo  $row_result[('invoice_no')]; ?>&nbsp;</td>
	                                <td  width="80" align="center" rowspan="<? echo $row_count; ?>" valign="top"><? if($row_result[('invoice_date')]!="" && $row_result[('invoice_date')]!="0000-00-00") echo change_date_format($row_result[('invoice_date')]); ?>&nbsp;</td>
	                                <?
									$flag=0;$i++;
								}
								?>
								<td  width="130"><? echo $order_data_arr[$row_result[('po_breakdown_id')]]["po_number"];?>&nbsp;</td>
								<td  width="90"><? echo $order_data_arr[$row_result[('po_breakdown_id')]]["internal_ref"];?>&nbsp;</td>
								<td align="right" width="100"><? echo number_format($order_data_arr[$row_result[('po_breakdown_id')]]["po_quantity"],0); $sub_order_qnty+=$order_data_arr[$row_result[('po_breakdown_id')]]["po_quantity"]; $grand_order_qnty+=$order_data_arr[$row_result[('po_breakdown_id')]]["po_quantity"];?></td>
								<td align="right" width="100"><? echo number_format($row_result[('current_invoice_qnty')],0); $sub_ship_qnty+=$row_result[('current_invoice_qnty')]; $grand_ship_qnty+=$row_result[('current_invoice_qnty')];?></td>
								<td align="right" width="80"><? echo number_format($short_qnty,0); $sub_short_qnty+=$short_qnty; $grand_short_qnty+=$short_qnty;?></td>
								<td align="right" width="80"><? echo number_format($extra_qnty,0);  $sub_extra_qnty+=$extra_qnty; $grand_extra_qnty+=$extra_qnty; ?></td>
								<td align="right" width="70"><? echo number_format($row_result[('current_invoice_rate')],2); ?></td>
								<td align="right" width="100"><? echo number_format($row_result[('current_invoice_value')],2);  $sub_ship_value+=$row_result[('current_invoice_value')]; $grand_ship_value+=$row_result[('current_invoice_value')];  ?></td>
								<td align="right" width="100"><? echo number_format($short_value,2); $sub_short_value+=$short_value; $grand_short_value+=$short_value; ?></td>
								<td align="right" width="100"><? echo number_format($extra_value,2); $sub_extra_value+=$extra_value; $grand_extra_value+=$extra_value; ?></td>
								<?
								if($flag2==1)
								{
									?>
									<td align="right" width="100" rowspan="<? echo $row_count; ?>" valign="top"><? echo number_format($row_result[('commission')],2); ?></td>
	                                <td width="100" align="center" rowspan="<? echo $row_count; ?>" valign="top"><? echo $shipment_mode[$row_result[('shipping_mode')]]; ?></td>
									<td  width="100" style="padding-left:3px;" rowspan="<? echo $row_count; ?>" valign="top"><? echo $buyer_arr[$row_result[('buyer_id')]];?></td>
									<?
									$flag2=0;
								}
								?>
							</tr>
							<?
							$k++;
						}
	                }
	                //print_r($sc_value_1_3);
	                ?>
	                <tr bgcolor="#CCCCCC">
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>Sub Total:</td>
	                    <td align="right"><? echo number_format($sub_order_qnty,0); ?></td>
	                    <td align="right"><? echo number_format($sub_ship_qnty,0); ?></td>
	                    <td align="right"><? echo number_format($sub_short_qnty,0); ?></td>
	                    <td align="right"><? echo number_format($sub_extra_qnty,0); ?></td>
	                    <td>&nbsp;</td>
	                    <td align="right"><? echo number_format($sub_ship_value,2); ?></td>
	                    <td align="right"><? echo number_format($sub_short_value,2); ?></td>
	                    <td align="right"><? echo number_format($sub_extra_value,2); ?></td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                </tr>
                </tbody>
            </table>
     		</div>
            <table width="1430" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="report_table_footer_short" align="left">
            	<tfoot>
                <tr>
                	<th width="40">&nbsp;</th>
                    <th width="130">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="130">&nbsp;</th>
                    <th width="90">Grand Total:</th>
                    <th width="100"><? echo number_format($grand_order_qnty,0); ?></th>
                    <th width="100"><? echo number_format($grand_ship_qnty,0); ?></th>
                    <th width="80"><? echo number_format($grand_short_qnty,0); ?></th>
                    <th width="80"><? echo number_format($grand_extra_qnty,0); ?></th>
                    <th width="70">&nbsp;</th>
                    <th width="100"><? echo number_format($grand_ship_value,2); ?></th>
                    <th width="100"><? echo number_format($grand_short_value,2); ?></th>
                    <th width="100"><? echo number_format($grand_extra_value,2); ?></th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th  width="100">&nbsp;</th>
                </tr>
                </tfoot>
            </table>
    	</div>
    	<?
    	foreach (glob("$user_id*.xls") as $filename)
		{

			if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename=$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc,ob_get_contents());
		//$filename=$user_id."_".$name.".xls";
		echo "$total_data****".$RptType."****".$filename;

		//echo "****".$RptType;
	}
	else if($RptType ==3) // DETAILS 2
	{

		$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");

		$sub_sql=sql_select("select b.invoice_id, a.courier_date, a.submit_date, a.bnk_to_bnk_cour_no, a.bank_ref_no, a.possible_reali_date from com_export_doc_submission_mst a,com_export_doc_submission_invo b where a.id=b.doc_submission_mst_id and a.entry_form=40 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		$bank_sub_data=array();

		foreach($sub_sql as $row)
		{
			$bank_sub_data[$row[csf("invoice_id")]]["courier_date"]=$row[csf("courier_date")];
			$bank_sub_data[$row[csf("invoice_id")]]["submit_date"]=$row[csf("submit_date")];
			$bank_sub_data[$row[csf("invoice_id")]]["bnk_to_bnk_cour_no"]=$row[csf("bnk_to_bnk_cour_no")];
			$bank_sub_data[$row[csf("invoice_id")]]["bank_ref_no"]=$row[csf("bank_ref_no")];
			$bank_sub_data[$row[csf("invoice_id")]]["possible_reali_date"]=$row[csf("possible_reali_date")];
		}

		$buyer_submit_date_arr=return_library_array(" select b.invoice_id,a.submit_date from com_export_doc_submission_mst a,com_export_doc_submission_invo b where a.id=b.doc_submission_mst_id and a.entry_form=39","invoice_id","submit_date");

		$rlz_date_arr=return_library_array(" select a.invoice_id,b.received_date,b.is_invoice_bill
		from com_export_doc_submission_invo a, com_export_proceed_realization b
		where a.doc_submission_mst_id=b.invoice_bill_id and b.is_invoice_bill  = 1
		union all
		select b.invoice_bill_id as invoice_id, b.received_date , b.is_invoice_bill
		from  com_export_proceed_realization b
		where  b.is_invoice_bill  = 2
		order by invoice_id","invoice_id","received_date");	

		$rlz_date_res = sql_select("select  a.invoice_id,b.received_date,b.is_invoice_bill ,c.type,   sum( c.document_currency) as document_currency
		from com_export_doc_submission_invo a, com_export_proceed_realization b , com_export_proceed_rlzn_dtls c
		where a.doc_submission_mst_id=b.invoice_bill_id and b.is_invoice_bill  = 1 and b.id = c.mst_id 
		group by  a.invoice_id,b.received_date,b.is_invoice_bill , c.type
		union all
		select b.invoice_bill_id as invoice_id, b.received_date , b.is_invoice_bill, c.type,  sum(c.document_currency) as document_currency
		from  com_export_proceed_realization b , com_export_proceed_rlzn_dtls c
		where  b.is_invoice_bill  = 2 and b.id = c.mst_id
		group by b.invoice_bill_id, b.received_date , b.is_invoice_bill, c.type
		order by invoice_id");

		$rlzdtlsChk =array();// $rlz_date_arr=array();
		foreach ($rlz_date_res as $val)
		{
			//$rlz_date_arr[$val[csf("invoice_id")]] = $val[csf("received_date")];
			if($val[csf("type")]==0)
			{
				$rlz_invoice_deduc_dist[$val[csf("invoice_id")]]["deduct"] += $val[csf("document_currency")];
			}
			else
			{
				$rlz_invoice_deduc_dist[$val[csf("invoice_id")]]["dist"] += $val[csf("document_currency")];
			}
				$rlz_invoice_deduc_dist[$val[csf("invoice_id")]]["total"] += $val[csf("document_currency")];

		}

		$exfact_qnty_arr=return_library_array(" select invoice_no,
		sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty
		from pro_ex_factory_mst where status_active=1 and is_deleted=0 and invoice_no>0 group by invoice_no","invoice_no","ex_factory_qnty");
		$variable_standard_arr=return_library_array(" select monitor_head_id,monitoring_standard_day from variable_settings_commercial where status_active=1 and is_deleted=0 and company_name=$cbo_company_name and variable_list=19","monitor_head_id","monitoring_standard_day");

		$sql_order_set=sql_select("select a.mst_id, a.po_breakdown_id, a.current_invoice_qnty, (a.current_invoice_qnty*c.total_set_qnty) as invoice_qnty_pcs, c.total_set_qnty from com_export_invoice_ship_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0");
		$inv_qnty_pcs_arr=array();
		foreach($sql_order_set as $row)
		{
			$inv_qnty_pcs_arr[$row[csf("mst_id")]]+=$row[csf("invoice_qnty_pcs")];
		}
		ob_start();
		?>
		<div style="width:1840px">
            <table width="1820" cellpadding="0" cellspacing="0" id="caption">
            <tr>
            <td align="center" width="100%" colspan="20" class="form_caption" ><strong style="font-size:18px">Company Name:<? echo " ". $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
            </tr>
            <tr>
            <td align="center" width="100%" colspan="20" class="form_caption" ><strong style="font-size:18px"><? echo $report_title; ?>
            <?
			if($cbo_based_on!=0)
			{
			?>
            (<strong style="font-size:18px">Based On:<? $based_on_arr=array(1=>"Invoice Date",2=>"Exfactory Date",3=>"Actual Date"); echo " ". $based_on_arr[$cbo_based_on]; ?></strong>)

            <?
			}
			?></strong></td>
            </tr>
            </table>
    		<br />
            <table width="1820" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1" align="left">
                <thead>
                    <tr>
                        <th width="50">Sl</th>
                        <th width="100">Invoice No.</th>
                        <th width="70">Invoice Date</th>
                        <th width="70">SC/LC</th>
                        <th width="100">SC/LC No.</th>
                        <th width="70">Buyer Name</th>
                        <th width="100">Ex-factory Qnty</th>
                        <th width="100">Invoice Qnty.</th>
                        <th width="100">Invoice Qnty. Pcs</th>
                        <th width="100">Invoice value</th>
                        <th width="100">Net Invoice Amount</th>
                        <th width="80">Currency</th>
                        <th width="70">Ex-Factory Date</th>
                        <th width="100">Bank Bill No.</th>
                        <th width="70">Bank Bill Date</th>
                        <th width="80">Pay Term</th>
                        <th width="70">Actual Realized Date</th>
                        <th width="70">Realization Amount</th>
                        <th width="100">Distributions</th>
                        <th width="100">Deduction at source</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
            </table>
            <div style="width:1840px; overflow-y:scroll; max-height:290px;font-size:12px; overflow-x:hidden;" id="scroll_body" align="left">
	            <table width="1820" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
	                <tbody>
	                <?
	                $cbo_company_name=str_replace("'","",$cbo_company_name);
	                if($cbo_company_name!=0) $cbo_company_name = $cbo_company_name; else $cbo_company_name="%%";

	                $cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	                if($cbo_buyer_name == 0) $cbo_buyer_name="%%"; else $cbo_buyer_name = $cbo_buyer_name;

	                $cbo_lien_bank=str_replace("'","",$cbo_lien_bank);
	                if($cbo_lien_bank == 0) $cbo_lien_bank="%%"; else $cbo_lien_bank = $cbo_lien_bank;
					//echo  $cbo_lien_bank;die;
	                $cbo_location=str_replace("'","",$cbo_location);
	                if($cbo_location == 0) $cbo_location="%%"; else $cbo_location = $cbo_location;

	                if($cbo_based_on == 0) $cbo_based_on="%%"; else $cbo_based_on = $cbo_based_on;

	                $txt_date_from=str_replace("'","",$txt_date_from);
	                if(trim($txt_date_from)!= "") $txt_date_from  =$txt_date_from;

	                $txt_date_to=str_replace("'","",$txt_date_to);
	                if(trim($txt_date_to)!= "") $txt_date_to = $txt_date_to;

					$forwarder_name=str_replace("'","",$forwarder_name);
					if($forwarder_name!=0) $forwarder_cond=" and a.forwarder_name='$forwarder_name'"; else $forwarder_cond="";
					$txt_invoice_no=str_replace("'","",$txt_invoice_no);
					if($txt_invoice_no!="") $invoice_cond=" and a.invoice_no like '%$txt_invoice_no'"; else $invoice_cond="";

					if($cbo_ascending_by!="" && $cbo_ascending_by==1 ) 
					{
						$ascending_by = "invoice_no";
						$ascendig_cond=" order by $ascending_by asc ";
					} elseif ($cbo_ascending_by!="" && $cbo_ascending_by==2) {
						$ascending_by = "invoice_date";
						$ascendig_cond=" order by $ascending_by asc ";
					} elseif ($cbo_ascending_by!="" && $cbo_ascending_by==3) {
						$ascending_by = "exp_form_no";
						$ascendig_cond=" order by $ascending_by asc ";
					}elseif ($cbo_ascending_by!="" && $cbo_ascending_by==4) {
						$ascending_by = "exp_form_date";
						$ascendig_cond=" order by $ascending_by asc ";
					}else{
						$ascendig_cond="";
					}

	                //if(trim($data[7])!="") $cbo_year2=$data[7];
	                if ($txt_date_from!="" && $txt_date_to!="")
	                {
						if($cbo_based_on ==0)
						{
							$str_cond=" and a.invoice_date between '$txt_date_from' and  '$txt_date_to'";
						}
						else if($cbo_based_on ==1)
						{
							$str_cond=" and a.invoice_date between '$txt_date_from' and  '$txt_date_to'";
						}
						else if($cbo_based_on ==2)
						{
							$str_cond=" and a.ex_factory_date between '$txt_date_from' and  '$txt_date_to'";
						}
						else if($cbo_based_on ==3)
						{
							$str_cond=" and a.actual_shipment_date between '$txt_date_from' and  '$txt_date_to'";
						}
						else if($cbo_based_on ==4)
						{
							$str_cond=" and a.bl_date between '$txt_date_from' and  '$txt_date_to'";
						}
						else if($cbo_based_on ==5)
						{
							$str_cond=" and a.ship_bl_date between '$txt_date_from' and  '$txt_date_to'";
						}
						else if($cbo_based_on ==6)
						{
							$str_cond=" and e.received_date between '$txt_date_from' and  '$txt_date_to'";
						}
						else if($cbo_based_on ==7)
						{

							if($db_type==0) $str_cond=" and a.insert_date between '$txt_date_from 00:00:01' and  '$txt_date_to 23:59:59'";
							else if($db_type==2) $str_cond=" and a.insert_date between '$txt_date_from 12:00:01 AM' and  '$txt_date_to 11:59:59 PM'";
						}
						else if($cbo_based_on ==8)
						{
							$str_cond=" and a.invoice_date between '$txt_date_from' and  '$txt_date_to'";
						}
	                }
	                else
	                {
	                	$str_cond="";
	                }
					$shipping_mode=str_replace("'","",$shipping_mode);
					$ship_cond="";
					if($shipping_mode!=0) $ship_cond=" and a.shipping_mode=$shipping_mode";

					$txt_lc_sc_no=str_replace("'","",$txt_lc_sc_no);
					//echo  $str_cond;die;
					if($cbo_based_on==6)
					{
						if($db_type==0)
						{
							$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type
							FROM com_export_invoice_ship_mst a, com_export_lc b, com_export_doc_submission_invo d, com_export_proceed_realization e
							WHERE a.lc_sc_id=b.id and a.id=d.invoice_id and d.doc_submission_mst_id=e.invoice_bill_id  and a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' AND b.export_lc_no LIKE '%$txt_lc_sc_no%' and a.is_lc=1 and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  $str_cond $forwarder_cond $ship_cond $invoice_cond

							UNION ALL

							SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type
							FROM com_export_invoice_ship_mst a, com_sales_contract c , com_export_doc_submission_invo d, com_export_proceed_realization e
							WHERE a.lc_sc_id=c.id and a.id=d.invoice_id and d.doc_submission_mst_id=e.invoice_bill_id and a.is_lc=2 and a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' AND c.contract_no LIKE '%$txt_lc_sc_no%' and  a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $str_cond $forwarder_cond $ship_cond $invoice_cond

							UNION ALL

							SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date, a. 	invoice_value, a.discount_ammount, a.bonus_ammount, a.claim_ammount, a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type
							FROM com_export_invoice_ship_mst a, com_sales_contract c , com_export_proceed_realization e
							WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=2 and a.lc_sc_id=c.id and e.invoice_bill_id = a.id and e.is_invoice_bill = 2 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond $ascendig_cond";

						}
						else
						{
							$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode,b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type
						FROM com_export_invoice_ship_mst a, com_export_lc b , com_export_doc_submission_invo d, com_export_proceed_realization e
						WHERE a.lc_sc_id=b.id and a.id=d.invoice_id and d.doc_submission_mst_id=e.invoice_bill_id  and a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' AND b.export_lc_no LIKE '%$txt_lc_sc_no%' and a.is_lc=1 and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  $str_cond $forwarder_cond $ship_cond $invoice_cond

						UNION ALL

						SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type
						FROM com_export_invoice_ship_mst a, com_sales_contract c, com_export_doc_submission_invo d, com_export_proceed_realization e
						WHERE a.lc_sc_id=c.id and a.id=d.invoice_id and d.doc_submission_mst_id=e.invoice_bill_id  and a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' AND c.contract_no LIKE '%$txt_lc_sc_no%' and a.is_lc=2 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $str_cond $forwarder_cond $ship_cond $invoice_cond
						UNION ALL

						SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type
						FROM com_export_invoice_ship_mst a, com_sales_contract c , com_export_proceed_realization e
						WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=2 and a.lc_sc_id=c.id and e.invoice_bill_id = a.id and e.is_invoice_bill = 2  and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond $ascendig_cond";
						}
					}
					else if($cbo_based_on==8)
					{
						if($db_type==0)
						{
							$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a. 	invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type
							FROM com_export_invoice_ship_mst a, com_export_lc b
							WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=1 and a.lc_sc_id=b.id and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 AND b.export_lc_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond and a.id not in(select a.invoice_id from com_export_doc_submission_invo a, com_export_proceed_realization b where a.doc_submission_mst_id=b.invoice_bill_id and a.is_lc=1)

							UNION ALL

							SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a. 	invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type
							FROM com_export_invoice_ship_mst a, com_sales_contract c
							WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=2 and a.lc_sc_id=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0  AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond and a.id not in(select a.invoice_id from com_export_doc_submission_invo a, com_export_proceed_realization b where a.doc_submission_mst_id=b.invoice_bill_id and a.is_lc=2)  $ascendig_cond";

						}
						else
						{
							/*$realized_invoice = sql_select("select a.invoice_id from com_export_doc_submission_invo a, com_export_proceed_realization b
							where a.doc_submission_mst_id=b.invoice_bill_id and a.is_lc=2 and b.status_active = 1 and b.is_deleted = 0
							union all
							select b.invoice_bill_id as invoice_id from  com_export_proceed_realization b where b.is_invoice_bill=2 and b.benificiary_id = $cbo_company_name and b.status_active = 1 and b.is_deleted = 0");
							foreach ($realized_invoice as $val)
							{
								$all_realized_invoice_arr[$val[csf("invoice_id")]] = $val[csf("invoice_id")];
							}

							if(count($all_realized_invoice_arr)>999)
							{
								$all_realized_invoice_chunk_arr=array_chunk($all_realized_invoice_arr, 999);
								$all_realized_invoice_cond=" and (";
								foreach ($all_realized_invoice_chunk_arr as $value)
								{
									$all_realized_invoice_cond .="and a.id not in (".implode(",", $value).") or ";
								}
								$all_realized_invoice_cond=chop($all_realized_invoice_cond,"or ");
								$all_realized_invoice_cond.=")";
							}
							else
							{
								$all_realized_invoice_cond=" and a.id not in (".implode(",", $all_realized_invoice_arr).")";
							}


							$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode,b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type
							FROM com_export_invoice_ship_mst a, com_export_lc b
							WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' AND b.export_lc_no LIKE '%$txt_lc_sc_no%' and a.is_lc=1 and a.lc_sc_id=b.id and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  $str_cond $forwarder_cond $ship_cond $invoice_cond and a.id not in(select a.invoice_id from com_export_doc_submission_invo a, com_export_proceed_realization b where a.doc_submission_mst_id=b.invoice_bill_id and a.is_lc=1)
	
							UNION ALL
	
							SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a. 	invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type
							FROM com_export_invoice_ship_mst a, com_sales_contract c
							WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank'  AND c.contract_no LIKE '%$txt_lc_sc_no%' and a.is_lc=2 and a.lc_sc_id=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $str_cond $forwarder_cond $ship_cond $invoice_cond $all_realized_invoice_cond  ORDER BY buyer_id, invoice_date";*/
							
							$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a. 	invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type
							FROM com_export_invoice_ship_mst a, com_export_lc b
							WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=1 and a.lc_sc_id=b.id and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 AND b.export_lc_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond and a.id not in(select a.invoice_id from com_export_doc_submission_invo a, com_export_proceed_realization b where a.doc_submission_mst_id=b.invoice_bill_id and a.is_lc=1)

							UNION ALL

							SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a. 	invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type
							FROM com_export_invoice_ship_mst a, com_sales_contract c
							WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=2 and a.lc_sc_id=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0  AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond and a.id not in(select a.invoice_id from com_export_doc_submission_invo a, com_export_proceed_realization b where a.doc_submission_mst_id=b.invoice_bill_id and a.is_lc=2)  $ascendig_cond";
						}
					}
					else
					{
						if($db_type==0)
						{
							$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a. 	invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type
							FROM com_export_invoice_ship_mst a, com_export_lc b
							WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' AND b.export_lc_no LIKE '%$txt_lc_sc_no%' and a.is_lc=1 and a.lc_sc_id=b.id and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  $str_cond $forwarder_cond $ship_cond $invoice_cond

							UNION ALL

							SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date, a. 	invoice_value, a.discount_ammount, a.bonus_ammount, a.claim_ammount, a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type
							FROM com_export_invoice_ship_mst a, com_sales_contract c
							WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=2 and a.lc_sc_id=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond  $ascendig_cond";

						}
						else
						{
							$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode,b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type
						FROM com_export_invoice_ship_mst a, com_export_lc b
						WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=1 and a.lc_sc_id=b.id and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 AND b.export_lc_no LIKE '%$txt_lc_sc_no%'  $str_cond $forwarder_cond $ship_cond $invoice_cond

						UNION ALL

						SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type
						FROM com_export_invoice_ship_mst a, com_sales_contract c
						WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=2 and a.lc_sc_id=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond  $ascendig_cond";
						}
					}
					//echo $sql;
	                $sql_re=sql_select($sql);$k=1;$gb=1;
	                foreach($sql_re as $row_result)
	                {
						if ($k%2==0)
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";
						$id=$row_result[csf('id')];
						$exfact_date_calculate=$row_result[csf('ex_factory_date')];//$variable_standard_arr
						$bl_date_calculate=$row_result[csf('bl_date')];
						$variable_standard_bl_day=$possiable_bl_date=$realization_sub_day=$doc_presentation_days=$possiable_bank_sub_date=$variable_standard_gsp_day=$possiable_gsp_date=$variable_standard_co_day=$possiable_co_date="";
						if($exfact_date_calculate!="" && $exfact_date_calculate!='0000-00-00')
						{
							$variable_standard_bl_day=$variable_standard_arr[1]*60*60*24;
							$possiable_bl_date=date('d-m-Y',strtotime($exfact_date_calculate)+$variable_standard_bl_day);

						}
						if($bl_date_calculate!="" && $bl_date_calculate!='0000-00-00')
						{
							if($row_result[csf("type")]==1)
							{
								$realization_sub_day=$bank_sub_data[$row_result[csf('id')]]["submit_date"];
								$doc_presentation_days=$row_result[csf("doc_presentation_days")]*60*60*24;
								$possiable_bank_sub_date=date('d-m-Y',strtotime($bl_date_calculate)+$doc_presentation_days);
							}
							if($row_result[csf("type")]==2)
							{
								$realization_sub_day=$buyer_submit_date_arr[$row_result[csf('id')]];
								$doc_presentation_days=$row_result[csf("doc_presentation_days")]*60*60*24;
								$possiable_buyer_sub_date=date('d-m-Y',strtotime($bl_date_calculate)+$doc_presentation_days);
							}
							$variable_standard_gsp_day=$variable_standard_arr[2]*60*60*24;
							$possiable_gsp_date=date('d-m-Y',strtotime($bl_date_calculate)+$variable_standard_gsp_day);
							$variable_standard_co_day=$variable_standard_arr[3]*60*60*24;
							$possiable_co_date=date('d-m-Y',strtotime($bl_date_calculate)+$variable_standard_co_day);

						}
						if($group_buyer[$row_result[csf('buyer_id')]]=="")
						{
							$group_buyer[$row_result[csf('buyer_id')]]=$row_result[csf('buyer_id')];
							if($gb!=1)
							{
								?>
								<tr bgcolor="#EFEFEF">
									<th width="50">&nbsp;</th>
			                        <th width="100">&nbsp;</th>

			                        <th width="70">&nbsp;</th>
			                        <th width="70">&nbsp;</th>
			                        <th width="100">&nbsp;</th>
			                        <th width="70">Sub Total:</th>

			                        <th width="100" align="right"><? echo number_format($sub_ex_fact_qnty,2); ?></th>
			                        <th width="100" align="right"><? echo number_format($sub_invoice_qty,2); ?></th>
			                        <th width="100" align="right"><? echo number_format($sub_invoice_qty_pcs,2); ?></th>
			                        <th width="100" align="right"><?  ?></th>
			                        <th width="100"  align="right"><? echo number_format($sub_order_qnty,2);  ?></th>
			                        <th width="80">&nbsp;</th>

			                        <th width="70">&nbsp;</th>
			                        <th width="100">&nbsp;</th>
			                        <th width="70">&nbsp;</th>


			                        <th width="80">&nbsp;</th>
			                        <th width="70">&nbsp;</th>
			                        <th width="70" align="right"><? echo number_format($sub_rlz_amt,2); ?></th>
			                        <th width="100" align="right"><? echo fn_number_format($sub_rlz_dist,2); ?></th>
			                        <th width="100" align="right"><? echo fn_number_format($sub_rlz_deduct,2); ?></th>
			                        <th >&nbsp;</th>
								</tr>
								<?
								$sub_ex_fact_qnty=$sub_invoice_qty=$sub_invoice_qty_pcs=$sub_order_qnty=$sub_rlz_amt=$sub_rlz_dist=$sub_rlz_deduct=$distribution_amt=$diductiontion_amt=0;
							}
							?>
							<tr bgcolor="#EFEFEF">
								<td colspan="21"><b><? echo $buyer_arr[$row_result[csf('buyer_id')]];?></b></td>
							</tr>
							<?
							$gb++;
						}
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
	                    	<td width="50"><? echo $k;//$row_result[csf('id')];?></th>

	                        <td width="100"><? echo $row_result[csf('invoice_no')];?></td>

	                        <td width="70" align="center">
	                        <? if($row_result[csf('invoice_date')]!="0000-00-00" && $row_result[csf('invoice_date')]!="") {echo change_date_format($row_result[csf('invoice_date')]);} else {echo "&nbsp;";}?>
	                        </td>

	                        <td width="70"  align="center"><? if($row_result[csf('type')] == 1) echo "LC"; else echo "SC"; ?></td>
	                        <td width="100"><? echo $row_result[csf('lc_sc_no')];?></td>
	                        <td width="70"><? echo  $buyer_arr[$row_result[csf('buyer_id')]];?></td>

	                        <td width="100" align="right">
	                        <a href="##" onClick="generate_ex_factory_popup('ex_factory_popup','<? echo $row_result[csf('country_id')];?>','<? echo $row_result[csf('id')]; ?>','550px')"><? echo  number_format($exfact_qnty_arr[$row_result[csf('id')]],2);
							 $total_ex_fact_qnty+=$exfact_qnty_arr[$row_result[csf('id')]];$sub_ex_fact_qnty+=$exfact_qnty_arr[$row_result[csf('id')]];
							 ?></a>

	                        </td>
	                        <td width="100" align="right"><a href='#report_detals'  onclick= "openmypage('<? echo $id; ?>','<? echo $k; ?>')"><? echo number_format($row_result[csf('invoice_quantity')],2); $total_invoice_qty +=$row_result[csf('invoice_quantity')]; $sub_invoice_qty +=$row_result[csf('invoice_quantity')];?></a></td>
	                        <td width="100" align="right"><? echo number_format($inv_qnty_pcs_arr[$row_result[csf('id')]],2); $total_invoice_qty_pcs +=$inv_qnty_pcs_arr[$row_result[csf('id')]]; $sub_invoice_qty_pcs +=$inv_qnty_pcs_arr[$row_result[csf('id')]];?></td>

	                        <td width="100" align="right"><? echo number_format($row_result[csf('invoice_value')],2,'.',''); $total_grs_value +=$row_result[csf('invoice_value')];?></td>

	                        <td width="100" align="right"><? echo number_format($row_result[csf('net_invo_value')],2,'.',''); $total_order_qnty +=$row_result[csf('net_invo_value')]; $sub_order_qnty +=$row_result[csf('net_invo_value')];?></td>
	                        <td width="80" align="center"><? echo $currency[$row_result[csf('currency_name')]];?></td>


	                        <td width="70"  align="center"><? if($row_result[csf('ex_factory_date')]!="0000-00-00" && $row_result[csf('ex_factory_date')]!="") {echo change_date_format($row_result[csf('ex_factory_date')]);} else {echo "&nbsp;";} ?></td>

	                        <td width="100"><? echo $bank_sub_data[$row_result[csf('id')]]["bank_ref_no"]; ?></td>
	                        <td width="70"   align="center"><?

	                        if(!(trim($bank_sub_data[$row_result[csf('id')]]["submit_date"])=="0000-00-00" || trim($bank_sub_data[$row_result[csf('id')]]["submit_date"])==""))
	                        {
	                           echo change_date_format($bank_sub_data[$row_result[csf('id')]]["submit_date"]);
	                        }
	                        else
	                        {
	                            echo "&nbsp;";
	                        }
	                        ?></td>

	                        <td width="80"><? echo $pay_term[$row_result[csf('pay_term')]];?></td>

	                        <td width="70"   align="center">
	                        <?
							if(!(trim($rlz_date_arr[$row_result[csf('id')]])=="0000-00-00" || trim($rlz_date_arr[$row_result[csf('id')]])==""))
							{
								echo change_date_format($rlz_date_arr[$row_result[csf('id')]]);
							}
							else
							{
								echo "&nbsp;";
							}
	                         ?>
	                        </td>

	                        <td width="70" align="right"><? if(!(trim($rlz_date_arr[$row_result[csf('id')]])=="0000-00-00" || trim($rlz_date_arr[$row_result[csf('id')]])==""))
							{
								echo number_format($row_result[csf('net_invo_value')],2,'.','');
								$total_rlz_amt+=$row_result[csf('net_invo_value')]; $sub_rlz_amt+=$row_result[csf('net_invo_value')];
							}
							else
							{
								echo "";
							}
							?></td>
							<td width="100" align="right" title="invoice Distribution share =  Distribution X ( invoice Realization / total Realization)">

									<?
									$distribution_amt=$rlz_invoice_deduc_dist[$row_result[csf('id')]]["dist"] * ($row_result[csf('net_invo_value')]/($rlz_invoice_deduc_dist[$row_result[csf('id')]]["total"]));
										
											echo fn_number_format($distribution_amt,2,".","");
											//echo $distribution_amt;
											if(fn_number_format($distribution_amt,2,".","")!='')
											{
												$sub_rlz_dist += $distribution_amt;
												$total_rlz_dist += $distribution_amt;
											}
											
										
									?>

							</td>
							<td width="100" align="right" title="invoice Deduction share =  Deduction X ( invoice Realization / total Realization)">

									<?
										$diductiontion_amt=$rlz_invoice_deduc_dist[$row_result[csf('id')]]["deduct"] * ($row_result[csf('net_invo_value')]/$rlz_invoice_deduc_dist[$row_result[csf('id')]]["total"]);
										echo fn_number_format($diductiontion_amt,2,".","");
										if(fn_number_format($diductiontion_amt,2,".","")!='')
										{
											$total_rlz_deduct += $diductiontion_amt;

											$sub_rlz_deduct += $diductiontion_amt;
										}
									?>

							</td>
	                        <td><? echo $row_result[csf('remarks')];?></td>

						</tr>
						<?
						$k++;
	                }
	                ?>
	                </tbody>
	            </table>
	     		<table width="1820" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="report_table_footer" align="left">
	            	<tfoot>
	            		<tr class="tbl_bottom">
							<th width="50">&nbsp;</th>
	                        <th width="100">&nbsp;</th>

	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="100">&nbsp;</th>
	                        <th width="70">Sub Total:</th>

	                        <th width="100" align="right"><? echo number_format($sub_ex_fact_qnty,2); ?></th>
	                        <th width="100" align="right"><? echo number_format($sub_invoice_qty,2); ?></th>
	                        <th width="100" align="right"><? echo number_format($sub_invoice_qty_pcs,2); ?></th>
	                        <th width="100" align="right"><?  ?></th>
	                        <th width="100"  align="right"><? echo number_format($sub_order_qnty,2);  ?></th>
	                        <th width="80">&nbsp;</th>

	                        <th width="70">&nbsp;</th>
	                        <th width="100">&nbsp;</th>
	                        <th width="70">&nbsp;</th>


	                        <th width="80">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70" align="right"><? echo number_format($sub_rlz_amt,2); ?></th>
	                        <th width="100" align="right"><? echo fn_number_format($sub_rlz_dist,2); ?></th>
	                        <th width="100" align="right"><? echo fn_number_format($sub_rlz_deduct,2); ?></th>
	                        <th >&nbsp;</th>
						</tr>
	                    <tr>
	                        <th width="50">&nbsp;</th>
	                        <th width="100">&nbsp;</th>

	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="100">&nbsp;</th>
	                        <th width="70">Total:</th>

	                        <th width="100" id="value_total_ex_fact_qnty" align="right"><? echo number_format($total_ex_fact_qnty,2); ?></th>
	                        <th width="100" id="value_total_invoice_qty" align="right"><? echo number_format($total_invoice_qty,2); ?></th>
	                        <th width="100" id="value_total_invoice_qty_pcs" align="right"><? echo number_format($total_invoice_qty_pcs,2); ?></th>
	                        <th width="100"  align="right"><?  ?></th>
	                        <th width="100"  id="value_total_net_invo_value"  align="right"><? echo number_format($total_order_qnty,2);  ?></th>
	                        <th width="80">&nbsp;</th>

	                        <th width="70">&nbsp;</th>
	                        <th width="100">&nbsp;</th>
	                        <th width="70">&nbsp;</th>


	                        <th width="80">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70"  id="value_total_rlz_amt"  align="right"><? echo number_format($total_rlz_amt,2);?></th>
	                        <th width="100" id="value_total_rlz_dist" align="right"><? echo fn_number_format($total_rlz_dist,2)?></th>
	                        <th width="100" id="value_total_rlz_deduct" align="right"><? echo fn_number_format($total_rlz_deduct,2);?></th>
	                        <th >&nbsp;</th>
	                    </tr>
	                </tfoot>
	            </table>
     		</div>
    	</div>
    	<div align="left" style="font-weight:bold; margin-left:30px;"><? echo "User Id : ". $user_arr[$user_id] ." , &nbsp; THIS IS SYSTEM GENERATED STATEMENT, NO SIGNATURE REQUIRED ."; ?></div>
		<?

		foreach (glob("$user_id*.xls") as $filename)
		{

			if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename=$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc,ob_get_contents());
		//$filename=$user_id."_".$name.".xls";
		echo "$total_data****".$RptType."****".$filename;

		//echo "****".$RptType;
	}
	else if($RptType==4) //DETAILS 3 (shafiq)
	{
		$user_arr = return_library_array("select id,user_name from user_passwd ","id","user_name");
		$bank_arr=return_library_array("select id,bank_name from   lib_bank","id","bank_name");
		$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$location_arr=return_library_array("select id,location_name from  lib_location","id","location_name");
		$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
		$supplier_arr=return_library_array("select id,supplier_name from lib_supplier","id","supplier_name");
		$country_arr=return_library_array("select id,country_name from  lib_country","id","country_name");

		$sub_sql=sql_select("SELECT b.invoice_id, a.courier_date, a.submit_date, a.bnk_to_bnk_cour_no, a.bank_ref_no, a.possible_reali_date 
			from com_export_doc_submission_mst a,com_export_doc_submission_invo b 
			where a.id=b.doc_submission_mst_id and a.entry_form=40 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		$bank_sub_data=array();
		foreach($sub_sql as $row)
		{
			$bank_sub_data[$row[csf("invoice_id")]]["courier_date"]=$row[csf("courier_date")];
			$bank_sub_data[$row[csf("invoice_id")]]["submit_date"]=$row[csf("submit_date")];
			$bank_sub_data[$row[csf("invoice_id")]]["bnk_to_bnk_cour_no"]=$row[csf("bnk_to_bnk_cour_no")];
			$bank_sub_data[$row[csf("invoice_id")]]["bank_ref_no"]=$row[csf("bank_ref_no")];
			$bank_sub_data[$row[csf("invoice_id")]]["possible_reali_date"]=$row[csf("possible_reali_date")];
		}

		$buyer_submit_date_arr=return_library_array(" SELECT b.invoice_id,a.submit_date from com_export_doc_submission_mst a,com_export_doc_submission_invo b where a.id=b.doc_submission_mst_id and a.entry_form=39","invoice_id","submit_date");

		//$currier_date_arr=return_library_array("select b.invoice_id,a.courier_date from  com_export_doc_submission_mst a,com_export_doc_submission_invo b where a.id=b.doc_submission_mst_id ","invoice_id","courier_date");
		//$bank_submit_date_arr=return_library_array(" select b.invoice_id,a.submit_date from com_export_doc_submission_mst a,com_export_doc_submission_invo b where a.id=b.doc_submission_mst_id and a.entry_form=40","invoice_id","submit_date");
		//$bnk_to_bnk_cour_no_arr=return_library_array(" select b.invoice_id,a.bnk_to_bnk_cour_no from com_export_doc_submission_mst a,com_export_doc_submission_invo b where a.id=b.doc_submission_mst_id","invoice_id","bnk_to_bnk_cour_no");
		//$bank_ref_no_arr=return_library_array(" select b.invoice_id,a.bank_ref_no from com_export_doc_submission_mst a,com_export_doc_submission_invo b where a.id=b.doc_submission_mst_id","invoice_id","bank_ref_no");
		//$bank_date_no_arr=return_library_array(" select b.invoice_id,a.submit_date from com_export_doc_submission_mst a,com_export_doc_submission_invo b where a.id=b.doc_submission_mst_id and a.entry_form=40","invoice_id","submit_date");
		//$possible_rlz_date_arr=return_library_array(" select b.invoice_id,a.possible_reali_date from com_export_doc_submission_mst a,com_export_doc_submission_invo b where a.id=b.doc_submission_mst_id","invoice_id","possible_reali_date");

		//$rlz_date_arr=return_library_array(" select a.invoice_id,b.received_date from com_export_doc_submission_invo a, com_export_proceed_realization b where a.doc_submission_mst_id=b.invoice_bill_id","invoice_id","received_date");

		$rlz_date_arr=return_library_array(" SELECT a.invoice_id,b.received_date,b.is_invoice_bill
		from com_export_doc_submission_invo a, com_export_proceed_realization b
		where a.doc_submission_mst_id=b.invoice_bill_id and b.is_invoice_bill  = 1
		union all
		select b.invoice_bill_id as invoice_id, b.received_date , b.is_invoice_bill
		from  com_export_proceed_realization b
		where  b.is_invoice_bill  = 2
		order by invoice_id","invoice_id","received_date");


		//$exfact_qnty_arr=return_library_array(" select invoice_no, sum(ex_factory_qnty) as ex_factory_qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 and invoice_no>0 group by invoice_no","invoice_no","ex_factory_qnty");
		$exfact_qnty_arr=return_library_array(" SELECT invoice_no,
		sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty
		from pro_ex_factory_mst where status_active=1 and is_deleted=0 and invoice_no>0 group by invoice_no","invoice_no","ex_factory_qnty");
		$variable_standard_arr=return_library_array(" SELECT monitor_head_id,monitoring_standard_day from variable_settings_commercial where status_active=1 and is_deleted=0 and company_name=$cbo_company_name and variable_list=19","monitor_head_id","monitoring_standard_day");

		$sql_order_set=sql_select("SELECT a.mst_id, a.po_breakdown_id, a.current_invoice_qnty, (a.current_invoice_qnty*c.total_set_qnty) as invoice_qnty_pcs, c.total_set_qnty from com_export_invoice_ship_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0");
		$inv_qnty_pcs_arr=array();
		foreach($sql_order_set as $row)
		{
			$inv_qnty_pcs_arr[$row[csf("mst_id")]]+=$row[csf("invoice_qnty_pcs")];
		}
		ob_start();


		//echo " ". $company_arr[str_replace("'","",$cbo_company_name)];
		//echo $report_title;
		//$based_on_arr=array(1=>"Invoice Date",2=>"Exfactory Date",3=>"Actual Date"); echo " ". $based_on_arr[$cbo_based_on];
		?>
		
		<div style="width:5098px"> 
		<table width="3500" cellpadding="0" cellspacing="0" id="caption">
            <tr>
            <td align="center" width="100%" colspan="20" class="form_caption" ><strong style="font-size:18px">Company Name:<? echo " ". $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
            </tr>
            <tr>
            <td align="center" width="100%" colspan="20" class="form_caption" ><strong style="font-size:18px"><? echo $report_title; ?>
            <?
			if($cbo_based_on!=0)
			{
			?>
            (<strong style="font-size:18px">Based On:<? $based_on_arr=array(1=>"Invoice Date",2=>"Exfactory Date",3=>"Actual Date"); echo " ". $based_on_arr[$cbo_based_on]; ?></strong>)


            <?
			}
			?></strong></td>
            </tr>
            </table>
    		<br />           
            <table width="5390" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1" align="left">
                <thead>
                    <tr>
                        <th width="50">Sl</th>
                        <th width="100">Company Name</th>

                        <th width="100">Country Name</th>
                        <th width="100">Invoice No.</th>

                        
                        <th width="70">SC/LC</th>
                        <th width="100">SC/LC No.</th>
                        <th width="70">Buyer Name</th>
                        <th width="100">Forwarder Name</th>
                        <th width="150">Lien Bank</th>
                        <th width="90">EXP Form No</th>
                        <th width="70">EXP Form Date</th>
                        <th width="100">Ex-factory Qnty</th>
                        <th width="100">Invoice Qnty.</th>
                        <th width="100">Invoice Qnty. Pcs</th>
                        <th width="80">Num. Of Ctn Qnty.</th>
                        <th width="80">Avg Price</th>
                        <th width="100">Invoice value</th>
                        <th width="80">Discount</th>
                        <th width="70">Bonous</th>
                        <th width="70">Claim</th>
                        <th width="80">Commission</th>
                        <th width="80">Other Deduction</th>
                        <th width="80">Upcharge</th>
                        <th width="100">Net Invoice Amount</th>
                        <th width="80">Currency</th>

                        <th width="70">ETD Date</th>
                        <th width="70">Invoice Date</th>
                        <th width="70">Insert Date</th>
                        <th width="80">Ship Mode</th>
                        <th width="70">Ex-Factory Date</th>
                        <th width="70">Actual Ship Date</th>

                        <th width="70">Possible BL Date</th>
                        <th width="100">Copy B/L No</th>
                        <th width="70">Copy B/L Date</th>
                        <th width="70">B/L Days</th>
                        <th width="70">Org B/L Rcv Date</th>
                        <th width="70">Possible GSP Date</th>
                        <th width="70">Actual GSP Date</th>
                        <th width="100">GSP No.</th>
                        <th width="70">Possible CO Date</th>
                        <th width="70">Actual CO Date</th>
                        <th width="70">GSP Cour. Date</th>
                        <th width="70">I/C Rcv Date</th>
                        <th width="70">Document In Hand</th>

                        <th width="70">Possible Buyer Sub date</th>
                        <th width="70">Actual Buyer Sub Date</th>
                        <th width="70">Buyer Sub Days</th>
                        <th width="70">Possible Bank Sub date</th>
                        <th width="70">Actual Bank Sub Date</th>
                        <th width="70">Bank Sub Days</th>
                        <th width="100">Bank Bill No.</th>
                        <th width="70">Bank Bill Date</th>

                        <th width="70">Shipping Bill No</th>
                        <th width="70">Shipping Bill Date</th>
                        <th width="70">Gross Weight</th>
                        <th width="70">Net weight</th>

                        <th width="80">Pay Term</th>
                        <th width="80">LC Tenor</th>
                        <th width="70">Possible Rlz. Date</th>
                        <th width="70">Actual Realized Date</th>
                        <th width="70">Realization Days</th>
                        <th width="70">Realization Amount</th>
                        <th width="100">Remarks</th>
                        <th width="100">Feeder Vessle</th>
                        <th width="100">Mother Vessle</th>
                        <th width="70">ETA Dest.</th>
                        <th >B TO B Courier No</th>

                    </tr>
                </thead>
            </table>
            <div style="width:5400px; overflow-y:scroll; max-height:290px;font-size:12px; overflow-x:hidden;" id="scroll_body" align="left">
	            <table width="5390" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
	                <tbody>
	                <?
	                $cbo_company_name=str_replace("'","",$cbo_company_name);
	                if($cbo_company_name!=0) $cbo_company_name = $cbo_company_name; else $cbo_company_name="%%";

	                $cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	                if($cbo_buyer_name == 0) $cbo_buyer_name="%%"; else $cbo_buyer_name = $cbo_buyer_name;

	                $cbo_lien_bank=str_replace("'","",$cbo_lien_bank);
	                if($cbo_lien_bank == 0) $cbo_lien_bank="%%"; else $cbo_lien_bank = $cbo_lien_bank;
					//echo  $cbo_lien_bank;die;
	                $cbo_location=str_replace("'","",$cbo_location);
	                if($cbo_location == 0) $cbo_location="%%"; else $cbo_location = $cbo_location;

	                if($cbo_based_on == 0) $cbo_based_on="%%"; else $cbo_based_on = $cbo_based_on;

	                $txt_date_from=str_replace("'","",$txt_date_from);
	                if(trim($txt_date_from)!= "") $txt_date_from  =$txt_date_from;

	                $txt_date_to=str_replace("'","",$txt_date_to);
	                if(trim($txt_date_to)!= "") $txt_date_to = $txt_date_to;

					$forwarder_name=str_replace("'","",$forwarder_name);
					if($forwarder_name!=0) $forwarder_cond=" and a.forwarder_name='$forwarder_name'"; else $forwarder_cond="";
					$txt_invoice_no=str_replace("'","",$txt_invoice_no);
					if($txt_invoice_no!="") $invoice_cond=" and a.invoice_no like '%$txt_invoice_no'"; else $invoice_cond="";

					if($cbo_ascending_by!="" && $cbo_ascending_by==1 ) 
					{
						$ascending_by = "invoice_no";
						$ascendig_cond=" order by $ascending_by asc ";
					} elseif ($cbo_ascending_by!="" && $cbo_ascending_by==2) {
						$ascending_by = "invoice_date";
						$ascendig_cond=" order by $ascending_by asc ";
					} elseif ($cbo_ascending_by!="" && $cbo_ascending_by==3) {
						$ascending_by = "exp_form_no";
						$ascendig_cond=" order by $ascending_by asc ";
					}elseif ($cbo_ascending_by!="" && $cbo_ascending_by==4) {
						$ascending_by = "exp_form_date";
						$ascendig_cond=" order by $ascending_by asc ";
					}else{
						$ascendig_cond="";
					}
	                //if(trim($data[7])!="") $cbo_year2=$data[7];
	                if ($txt_date_from!="" && $txt_date_to!="")
	                {
						if($cbo_based_on ==0)
						{
							$str_cond=" and a.invoice_date between '$txt_date_from' and  '$txt_date_to'";
						}
						else if($cbo_based_on ==1)
						{
							$str_cond=" and a.invoice_date between '$txt_date_from' and  '$txt_date_to'";
						}
						else if($cbo_based_on ==2)
						{
							$str_cond=" and a.ex_factory_date between '$txt_date_from' and  '$txt_date_to'";
						}
						else if($cbo_based_on ==3)
						{
							$str_cond=" and a.actual_shipment_date between '$txt_date_from' and  '$txt_date_to'";
						}
						else if($cbo_based_on ==4)
						{
							$str_cond=" and a.bl_date between '$txt_date_from' and  '$txt_date_to'";
						}
						else if($cbo_based_on ==5)
						{
							$str_cond=" and a.ship_bl_date between '$txt_date_from' and  '$txt_date_to'";
						}
						else if($cbo_based_on ==6)
						{
							$str_cond=" and e.received_date between '$txt_date_from' and  '$txt_date_to'";
						}
						else if($cbo_based_on ==7)
						{

							if($db_type==0) $str_cond=" and a.insert_date between '$txt_date_from 00:00:01' and  '$txt_date_to 23:59:59'";
							else if($db_type==2) $str_cond=" and a.insert_date between '$txt_date_from 12:00:01 AM' and  '$txt_date_to 11:59:59 PM'";
						}
						else if($cbo_based_on ==8)
						{
							$str_cond=" and a.invoice_date between '$txt_date_from' and  '$txt_date_to'";
						}
	                }
	                else
	                {
	                	$str_cond="";
	                }
					$shipping_mode=str_replace("'","",$shipping_mode);
					$ship_cond="";
					if($shipping_mode!=0) $ship_cond=" and a.shipping_mode=$shipping_mode";

					$txt_lc_sc_no=str_replace("'","",$txt_lc_sc_no);
					//echo  $str_cond;die;
					if($cbo_based_on==6)
					{
						if($db_type==0)
						{
							$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor
							FROM com_export_invoice_ship_mst a, com_export_lc b, com_export_doc_submission_invo d, com_export_proceed_realization e
							WHERE a.lc_sc_id=b.id and a.id=d.invoice_id and d.doc_submission_mst_id=e.invoice_bill_id  and a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' AND b.export_lc_no LIKE '%$txt_lc_sc_no%' and a.is_lc=1 and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  $str_cond $forwarder_cond $ship_cond $invoice_cond

							UNION ALL

							SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor
							FROM com_export_invoice_ship_mst a, com_sales_contract c , com_export_doc_submission_invo d, com_export_proceed_realization e
							WHERE a.lc_sc_id=c.id and a.id=d.invoice_id and d.doc_submission_mst_id=e.invoice_bill_id and a.is_lc=2 and a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' AND c.contract_no LIKE '%$txt_lc_sc_no%' and  a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $str_cond $forwarder_cond $ship_cond $invoice_cond

							UNION ALL

							SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date, a. 	invoice_value, a.discount_ammount, a.bonus_ammount, a.claim_ammount, a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor
							FROM com_export_invoice_ship_mst a, com_sales_contract c , com_export_proceed_realization e
							WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=2 and a.lc_sc_id=c.id and e.invoice_bill_id = a.id and e.is_invoice_bill = 2 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond $ascendig_cond";

						}
						else
						{
							$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor
						FROM com_export_invoice_ship_mst a, com_export_lc b , com_export_doc_submission_invo d, com_export_proceed_realization e
						WHERE a.lc_sc_id=b.id and a.id=d.invoice_id and d.doc_submission_mst_id=e.invoice_bill_id  and a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' AND b.export_lc_no LIKE '%$txt_lc_sc_no%' and a.is_lc=1 and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  $str_cond $forwarder_cond $ship_cond $invoice_cond

						UNION ALL

						SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor
						FROM com_export_invoice_ship_mst a, com_sales_contract c, com_export_doc_submission_invo d, com_export_proceed_realization e
						WHERE a.lc_sc_id=c.id and a.id=d.invoice_id and d.doc_submission_mst_id=e.invoice_bill_id  and a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' AND c.contract_no LIKE '%$txt_lc_sc_no%' and a.is_lc=2 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $str_cond $forwarder_cond $ship_cond $invoice_cond
						UNION ALL

						SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor
						FROM com_export_invoice_ship_mst a, com_sales_contract c , com_export_proceed_realization e
						WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=2 and a.lc_sc_id=c.id and e.invoice_bill_id = a.id and e.is_invoice_bill = 2  and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond $ascendig_cond";
						}
					}
					else if($cbo_based_on==8)
					{
						if($db_type==0)
						{
							$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor
							FROM com_export_invoice_ship_mst a, com_export_lc b
							WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=1 and a.lc_sc_id=b.id and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 AND b.export_lc_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond and a.id not in(select a.invoice_id from com_export_doc_submission_invo a, com_export_proceed_realization b where a.doc_submission_mst_id=b.invoice_bill_id and a.is_lc=1)

							UNION ALL

							SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor
							FROM com_export_invoice_ship_mst a, com_sales_contract c
							WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=2 and a.lc_sc_id=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0  AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond and a.id not in(select a.invoice_id from com_export_doc_submission_invo a, com_export_proceed_realization b where a.doc_submission_mst_id=b.invoice_bill_id and a.is_lc=2) $ascendig_cond";

						}
						else
						{
							/*$realized_invoice = sql_select("SELECT a.invoice_id from com_export_doc_submission_invo a, com_export_proceed_realization b
							where a.doc_submission_mst_id=b.invoice_bill_id and a.is_lc=2 and b.status_active = 1 and b.is_deleted = 0
							union all
							select b.invoice_bill_id as invoice_id from  com_export_proceed_realization b where b.is_invoice_bill=2 and b.benificiary_id = $cbo_company_name and b.status_active = 1 and b.is_deleted = 0");
							foreach ($realized_invoice as $val)
							{
								$all_realized_invoice_arr[$val[csf("invoice_id")]] = $val[csf("invoice_id")];
							}

							if(count($all_realized_invoice_arr)>999)
							{
								$all_realized_invoice_chunk_arr=array_chunk($all_realized_invoice_arr, 999);
								$all_realized_invoice_cond=" and (";
								foreach ($all_realized_invoice_chunk_arr as $value)
								{
									$all_realized_invoice_cond .="and a.id not in (".implode(",", $value).") or ";
								}
								$all_realized_invoice_cond=chop($all_realized_invoice_cond,"or ");
								$all_realized_invoice_cond.=")";
							}
							else
							{
								$all_realized_invoice_cond=" and a.id not in (".implode(",", $all_realized_invoice_arr).")";
							}


							$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor
							FROM com_export_invoice_ship_mst a, com_export_lc b
							WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' AND b.export_lc_no LIKE '%$txt_lc_sc_no%' and a.is_lc=1 and a.lc_sc_id=b.id and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  $str_cond $forwarder_cond $ship_cond $invoice_cond and a.id not in(select a.invoice_id from com_export_doc_submission_invo a, com_export_proceed_realization b where a.doc_submission_mst_id=b.invoice_bill_id and a.is_lc=1)
	
							UNION ALL
	
							SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor
							FROM com_export_invoice_ship_mst a, com_sales_contract c
							WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank'  AND c.contract_no LIKE '%$txt_lc_sc_no%' and a.is_lc=2 and a.lc_sc_id=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $str_cond $forwarder_cond $ship_cond $invoice_cond $all_realized_invoice_cond  $ascendig_cond";*/
							
							$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor
							FROM com_export_invoice_ship_mst a, com_export_lc b
							WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=1 and a.lc_sc_id=b.id and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 AND b.export_lc_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond and a.id not in(select a.invoice_id from com_export_doc_submission_invo a, com_export_proceed_realization b where a.doc_submission_mst_id=b.invoice_bill_id and a.is_lc=1)
							UNION ALL
							SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor
							FROM com_export_invoice_ship_mst a, com_sales_contract c
							WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=2 and a.lc_sc_id=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0  AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond and a.id not in(select a.invoice_id from com_export_doc_submission_invo a, com_export_proceed_realization b where a.doc_submission_mst_id=b.invoice_bill_id and a.is_lc=2) $ascendig_cond";
							
						}
					}
					else
					{
						$order_by_cond = "";
						switch ($cbo_based_on) 
						{
							case 1:
								$order_by_cond = "invoice_date";
								break;
							case 2:
								$order_by_cond = "ex_factory_date";
								break;
							case 3:
								$order_by_cond = "actual_shipment_date";
								break;
							case 4:
								$order_by_cond = "bl_date";
								break;
							case 5:
								$order_by_cond = "ship_bl_date";
								break;
							case 6:
								$order_by_cond = "id";
								break;
							case 7:
								$order_by_cond = "insert_date";
								break;							
							default:
								$order_by_cond = "id";
								break;
						}

						if($db_type==0)
						{
							$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor
							FROM com_export_invoice_ship_mst a, com_export_lc b
							WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' AND b.export_lc_no LIKE '%$txt_lc_sc_no%' and a.is_lc=1 and a.lc_sc_id=b.id and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  $str_cond $forwarder_cond $ship_cond $invoice_cond

							UNION ALL

							SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date, a.invoice_value, a.discount_ammount, a.bonus_ammount, a.claim_ammount, a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor
							FROM com_export_invoice_ship_mst a, com_sales_contract c
							WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=2 and a.lc_sc_id=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond  $ascendig_cond";//a.ex_factory_date";//id

						}
						else
						{
							$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor

						FROM com_export_invoice_ship_mst a, com_export_lc b
						WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=1 and a.lc_sc_id=b.id and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 AND b.export_lc_no LIKE '%$txt_lc_sc_no%'  $str_cond $forwarder_cond $ship_cond $invoice_cond

						UNION ALL

						SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor
						FROM com_export_invoice_ship_mst a, com_sales_contract c
						WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=2 and a.lc_sc_id=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond  $ascendig_cond";
						}
						// echo $sql;
					}
					//echo $sql;//die;
	                $sql_re=sql_select($sql);$k=1;
	                foreach($sql_re as $row_result)
	                {
						if ($k%2==0)
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";
						$id=$row_result[csf('id')];
						$exfact_date_calculate=$row_result[csf('ex_factory_date')];//$variable_standard_arr
						$bl_date_calculate=$row_result[csf('bl_date')];
						$variable_standard_bl_day=$possiable_bl_date=$realization_sub_day=$doc_presentation_days=$possiable_bank_sub_date=$variable_standard_gsp_day=$possiable_gsp_date=$variable_standard_co_day=$possiable_co_date="";
						if($exfact_date_calculate!="" && $exfact_date_calculate!='0000-00-00')
						{
							$variable_standard_bl_day=$variable_standard_arr[1]*60*60*24;
							$possiable_bl_date=date('d-m-Y',strtotime($exfact_date_calculate)+$variable_standard_bl_day);

						}
						if($bl_date_calculate!="" && $bl_date_calculate!='0000-00-00')
						{
							if($row_result[csf("type")]==1)
							{
								$realization_sub_day=$bank_sub_data[$row_result[csf('id')]]["submit_date"];
								$doc_presentation_days=$row_result[csf("doc_presentation_days")]*60*60*24;
								$possiable_bank_sub_date=date('d-m-Y',strtotime($bl_date_calculate)+$doc_presentation_days);
							}
							if($row_result[csf("type")]==2)
							{
								$realization_sub_day=$buyer_submit_date_arr[$row_result[csf('id')]];
								$doc_presentation_days=$row_result[csf("doc_presentation_days")]*60*60*24;
								$possiable_buyer_sub_date=date('d-m-Y',strtotime($bl_date_calculate)+$doc_presentation_days);
							}
							$variable_standard_gsp_day=$variable_standard_arr[2]*60*60*24;
							$possiable_gsp_date=date('d-m-Y',strtotime($bl_date_calculate)+$variable_standard_gsp_day);
							$variable_standard_co_day=$variable_standard_arr[3]*60*60*24;
							$possiable_co_date=date('d-m-Y',strtotime($bl_date_calculate)+$variable_standard_co_day);

						}
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
	                    	<td width="50" style="word-break: break-all;"><? echo $k;//$row_result[csf('id')];?></th>

	                        <td width="100" style="word-break: break-all;"><? echo  $company_arr[$row_result[csf('benificiary_id')]]; ?></td>

	                        <td width="100" style="word-break: break-all;"><? echo $country_arr[$row_result[csf('country_id')]];?></td>
	                       
	                        <td width="100" style="word-break: break-all;"><p>
	                        	<a href='##' style='color:#000' onClick="print_report('<? echo $row_result[csf('id')] ;?>','invoice_report_print','../export_details/requires/export_information_entry_controller')"><font color="blue"><b><? echo $row_result[csf('invoice_no')];?></b></font></a></p>
							</td>

	                        
	                        <td width="70"  align="center" style="word-break: break-all;"><? if($row_result[csf('type')] == 1) echo "LC"; else echo "SC"; ?></td>
	                        <td width="100" style="word-break: break-all;"><? echo $row_result[csf('lc_sc_no')];?></td>
	                        <td width="70" style="word-break: break-all;"><? echo  $buyer_arr[$row_result[csf('buyer_id')]];?></td>
	                        <td width="100" style="word-break: break-all;"><? echo  $supplier_arr[$row_result[csf('forwarder_name')]];?></td>
	                        <td width="150" style="word-break: break-all;"><? echo $bank_arr[$row_result[csf('lien_bank')]];?></td>
	                        <td width="90" style="word-break: break-all;"><? echo $row_result[csf('exp_form_no')]."&nbsp;";?></td>
	                        <td width="70"  align="center" style="word-break: break-all;"><?  if($row_result[csf('exp_form_date')]!="0000-00-00" && $row_result[csf('exp_form_date')]!="") {echo change_date_format($row_result[csf('exp_form_date')]);} else {echo "&nbsp;";} ?></td>
	                        <td width="100" align="right" style="word-break: break-all;"><? //echo number_format($exfact_qnty_arr[$row_result[csf('id')]],2);
							 //$total_ex_fact_qnty+=$exfact_qnty_arr[$row_result[csf('id')]]; ?>
	                        <a href="##" onClick="generate_ex_factory_popup('ex_factory_popup','<? echo $row_result[csf('country_id')];?>','<? echo $row_result[csf('id')]; ?>','550px')"><? echo  number_format($exfact_qnty_arr[$row_result[csf('id')]],2);
							 $total_ex_fact_qnty+=$exfact_qnty_arr[$row_result[csf('id')]];
							 ?></a>

	                        </td>
	                        <td width="100" align="right" style="word-break: break-all;"><a href='#report_detals'  onclick= "openmypage('<? echo $id; ?>','<? echo $k; ?>')"><? echo number_format($row_result[csf('invoice_quantity')],2); $total_invoice_qty +=$row_result[csf('invoice_quantity')];?></a></td>
	                        <td width="100" align="right" style="word-break: break-all;"><? echo number_format($inv_qnty_pcs_arr[$row_result[csf('id')]],2); $total_invoice_qty_pcs +=$inv_qnty_pcs_arr[$row_result[csf('id')]];?></td>
	                        <td width="80" align="right" style="word-break: break-all;"><? echo number_format($row_result[csf('total_carton_qnty')],2); $total_carton_qty +=$row_result[csf('total_carton_qnty')];?></td>
	                        <td width="80" align="right" style="word-break: break-all;">
	                        	<? $avg_price=$row_result[csf('invoice_value')]/$row_result[csf('invoice_quantity')];  
	                        	echo number_format($avg_price,2); $total_avg_price +=$avg_price;?></td>
	                        <td width="100" align="right" style="word-break: break-all;"><? echo number_format($row_result[csf('invoice_value')],2,'.',''); $total_grs_value +=$row_result[csf('invoice_value')];?></td>
	                        <td width="80" align="right" style="word-break: break-all;"><? echo number_format($row_result[csf('discount_ammount')],2,'.',''); $total_discount_value +=$row_result[csf('discount_ammount')]; ?></td>
	                        <td width="70" align="right" style="word-break: break-all;"><? echo number_format($row_result[csf('bonus_ammount')],2,'.',''); $total_bonous_value +=$row_result[csf('bonus_ammount')];  ?></td>
	                        <td width="70" align="right" style="word-break: break-all;"><? echo number_format($row_result[csf('claim_ammount')],2,'.',''); $total_claim_value +=$row_result[csf('claim_ammount')];  ?></td>
	                        <td width="80" align="right" style="word-break: break-all;"><?  echo number_format($row_result[csf('commission')],2,'.',''); $total_commission_value +=$row_result[csf('commission')]; ?></td>
	                        <td width="80" align="right" style="word-break: break-all;"><?  echo number_format($row_result[csf('other_discount_amt')],2,'.',''); $total_other_discount_value +=$row_result[csf('other_discount_amt')]; ?></td>
	                        <td width="80" align="right" style="word-break: break-all;"><?  echo number_format($row_result[csf('upcharge')],2,'.',''); $total_upcharge_value +=$row_result[csf('upcharge')]; ?></td>
	                        <td width="100" align="right" style="word-break: break-all;"><? echo number_format($row_result[csf('net_invo_value')],2,'.',''); $total_order_qnty +=$row_result[csf('net_invo_value')];?></td>
	                        <td width="80" align="center" style="word-break: break-all;"><? echo $currency[$row_result[csf('currency_name')]];?></td>

	                         <td width="70"  align="center" style="word-break: break-all;"><? if($row_result[csf('etd')]!="0000-00-00" && $row_result[csf('etd')]!="") echo change_date_format( $row_result[csf('etd')]); else echo "";?></td>
	                         <td width="70" align="center" style="word-break: break-all;">
	                        <? if($row_result[csf('invoice_date')]!="0000-00-00" && $row_result[csf('invoice_date')]!="") {echo change_date_format($row_result[csf('invoice_date')]);} else {echo "&nbsp;";}?>
	                        </td>
	                        <td width="70"  align="center" style="word-break: break-all;">
	                        <? if($row_result[csf('insert_date')]!="0000-00-00" && $row_result[csf('insert_date')]!="") {echo change_date_format($row_result[csf('insert_date')]);} else {echo "&nbsp;";}?>
	                        </td>
	                        <td width="80" style="word-break: break-all;"><? echo $shipment_mode[$row_result[csf('shipping_mode')]];?></td>
	                        <td width="70"  align="center" style="word-break: break-all;"><? if($row_result[csf('ex_factory_date')]!="0000-00-00" && $row_result[csf('ex_factory_date')]!="") {echo change_date_format($row_result[csf('ex_factory_date')]);} else {echo "&nbsp;";} ?></td>
	                        <td width="70"  align="center" style="word-break: break-all;"><?  if($row_result[csf('actual_shipment_date')]!="0000-00-00" && $row_result[csf('actual_shipment_date')]!="") {echo change_date_format($row_result[csf('actual_shipment_date')]);} else {echo "&nbsp;";}  ?></td>

	                        <td width="70"  align="center" style="word-break: break-all;" title="Ex-factory Date+Variable Standard Date"><? if($possiable_bl_date!="0000-00-00" && $possiable_bl_date!="") {echo change_date_format($possiable_bl_date);} else {echo "&nbsp;";} ?></td>							
							
	                        <td width="100" style="word-break: break-all;"><? echo $row_result[csf('bl_no')];?></td>
	                        <td width="70"  align="center" style="word-break: break-all;"><?  if($row_result[csf('bl_date')]!="0000-00-00" && $row_result[csf('bl_date')]!="") {echo change_date_format($row_result[csf('bl_date')]);} else {echo "&nbsp;";} ?></td>
	                        <td width="70" style="word-break: break-all;" title="exfactory date-bl date"  align="center"><?  $diff_bl=datediff("d",$exfact_date_calculate, $row_result[csf('bl_date')]); if($diff_bl>0) echo $diff_bl."days";  ?></td>
	                        <td width="70" style="word-break: break-all;"  align="center"><? if($row_result[csf('bl_rev_date')]!="0000-00-00" && $row_result[csf('bl_rev_date')]!="") {echo change_date_format($row_result[csf('bl_rev_date')]);} else { echo "&nbsp;";}?></td>
	                        <td width="70"  style="word-break: break-all;" align="center" title="BL Date+Variable Standard Date"><? if($possiable_gsp_date!="0000-00-00" && $possiable_gsp_date!="") {echo change_date_format($possiable_gsp_date);} else {echo "&nbsp;";} ?></td>

	                        <td width="70" style="word-break: break-all;"  align="center">
	                        <? if(trim($row_result[csf('gsp_co_no_date')])!="0000-00-00" && trim($row_result[csf('gsp_co_no_date')])!="") {echo change_date_format($row_result[csf('gsp_co_no_date')]);}else {echo "&nbsp;";}?>
	                        </td>
	                        <td width="100" style="word-break: break-all;"><? echo $row_result[csf('gsp_co_no')];?></td>
	                        <td width="70" style="word-break: break-all;" align="center" title="BL Date+Variable Standard Date"><? if($possiable_co_date!="0000-00-00" && $possiable_co_date!="") {echo change_date_format($possiable_co_date);} else {echo "&nbsp;";} ?></td>
	                        <td width="70" style="word-break: break-all;" align="center"><? if($row_result[csf('co_date')]!="0000-00-00" && $row_result[csf('co_date')]!="") {echo change_date_format($row_result[csf('co_date')]);} else {echo "&nbsp;";} ?></td>
	                        <td width="70" style="word-break: break-all;" align="center"><?
							$curier_receipt_date=$bank_sub_data[$row_result[csf("id")]]["courier_date"];
	                        if(!(trim($curier_receipt_date)=="0000-00-00" || trim($curier_receipt_date)==""))
	                        {
	                            echo change_date_format($curier_receipt_date);
	                        }
	                        else
	                        {
	                            echo "&nbsp;";
	                        }
	                        ?></td>
	                        <td width="70" style="word-break: break-all;" align="center"><? if($row_result[csf('ic_recieved_date')]!="0000-00-00" && $row_result[csf('ic_recieved_date')]!="") echo change_date_format( $row_result[csf('ic_recieved_date')]); else echo "";?></td>
	                        <td width="70" style="word-break: break-all;" title="exfactory date-bank submission date/current date"  align="center">
	                        <?

	                        if(($exfact_date_calculate!='0000-00-00') )
	                        {
	                            $current_date=date("Y-m-d");
	                            if($bank_sub_data[$row_result[csf('id')]]["submit_date"]=='0000-00-00' || $bank_sub_data[$row_result[csf('id')]]["submit_date"]=='')
	                            {
	                            	$diff=datediff("d",$exfact_date_calculate, $current_date);
	                            }
	                            else
	                            {
	                            	$diff=datediff("d",$exfact_date_calculate, $bank_sub_data[$row_result[csf('id')]]["submit_date"]);
	                            }

	                        }
	                        else
	                        {
	                            $diff="";
	                        }
	                       if($diff>0) echo  $diff." days";
	                        ?>
	                        </td>

	                        <td width="70"  style="word-break: break-all;"  align="center"  title="BL Date+Document Presentation Days"><? if($possiable_buyer_sub_date!="0000-00-00" && $possiable_buyer_sub_date!="") {echo change_date_format($possiable_buyer_sub_date);} else {echo "&nbsp;";} ?></td>
	                        <td width="70"   style="word-break: break-all;" align="center"><?
	                            if(trim($buyer_submit_date_arr[$row_result[csf('id')]])!='0000-00-00' && trim($buyer_submit_date_arr[$row_result[csf('id')]])!='') echo change_date_format(trim($buyer_submit_date_arr[$row_result[csf('id')]])); else echo "&nbsp;";
	                        ?></td>
	                        <td width="70" style="word-break: break-all;" title="From BL Date To Buyer Submission Date"   align="center">
							<?
							$diff_buyer_sub=0;
							if($row_result[csf('bl_date')]!="0000-00-00" && $row_result[csf('bl_date')]!="")
							{
								$diff_buyer_sub=datediff("d",$row_result[csf('bl_date')], $buyer_submit_date_arr[$row_result[csf('id')]]);
							}
							else
							{
								$diff_buyer_sub=0;
							}
							 if($diff_buyer_sub>0) echo $diff_buyer_sub."days";
							?></td>
	                        <td width="70"  style="word-break: break-all;"  align="center"   title="BL Date+Document Presentation Days"><? if($possiable_bank_sub_date!="0000-00-00" && $possiable_bank_sub_date!="") {echo change_date_format($possiable_bank_sub_date);} else {echo "&nbsp;";} ?></td>
	                        <td width="70"  style="word-break: break-all;"  align="center">
							<?
	                            if(trim($bank_sub_data[$row_result[csf('id')]]["submit_date"])!='0000-00-00' && trim($bank_sub_data[$row_result[csf('id')]]["submit_date"])!='') echo change_date_format(trim($bank_sub_data[$row_result[csf('id')]]["submit_date"])); else echo "&nbsp;";
	                        ?></td>
	                        <td width="70" style="word-break: break-all;" title="From BL Date To Bank Submission Date"   align="center">
							<?
							$diff_sub=0;
							if($row_result[csf('bl_date')]!="0000-00-00" && $row_result[csf('bl_date')]!="")
							{
								$diff_sub=datediff("d",$row_result[csf('bl_date')], $bank_sub_data[$row_result[csf('id')]]["submit_date"]);
							}
							else
							{
								$diff_sub=0;
							}
							 if($diff_sub>0) echo $diff_sub."days";
							?></td>
	                        <td width="100" style="word-break: break-all;"><? echo $bank_sub_data[$row_result[csf('id')]]["bank_ref_no"]; ?></td>
	                        <td width="70"  style="word-break: break-all;"  align="center"><?

	                        if(!(trim($bank_sub_data[$row_result[csf('id')]]["submit_date"])=="0000-00-00" || trim($bank_sub_data[$row_result[csf('id')]]["submit_date"])==""))
	                        {
	                           echo change_date_format($bank_sub_data[$row_result[csf('id')]]["submit_date"]);
	                        }
	                        else
	                        {
	                            echo "&nbsp;";
	                        }
	                        ?></td>

	                        <td width="70" style="word-break: break-all;"><? echo $row_result[csf('shipping_bill_n')]; ?></td>
							<td width="70" style="word-break: break-all;"><?  if($row_result[csf('ship_bl_date')]!="0000-00-00" && $row_result[csf('ship_bl_date')]!="") {echo change_date_format($row_result[csf('ship_bl_date')]);} else {echo "&nbsp;";}  ?></td>
							<td width="70" style="word-break: break-all;"><?
							$total_carton_gross_weight += $row_result[csf('carton_gross_weight')]; 
							echo $row_result[csf('carton_gross_weight')];
							?></td>
							<td width="70" style="word-break: break-all;"><? 
							$total_carton_net_weight += $row_result[csf('carton_net_weight')];
							echo $row_result[csf('carton_net_weight')];
							?></td>

	                        <td width="80" style="word-break: break-all;"><? echo $pay_term[$row_result[csf('pay_term')]];?></td>
	                        <td width="80" style="word-break: break-all;"><? echo $row_result[csf('tenor')];?></td>
	                        <td width="70"  style="word-break: break-all;"  align="center">
	                        <?
							if(!(trim($bank_sub_data[$row_result[csf('id')]]["possible_reali_date"])=="0000-00-00" || trim($bank_sub_data[$row_result[csf('id')]]["possible_reali_date"])==""))
							{
								echo change_date_format($bank_sub_data[$row_result[csf('id')]]["possible_reali_date"]);
							}
							else
							{
								echo "&nbsp;";
							}
	                         ?>
	                        </td>
	                        <td width="70"  style="word-break: break-all;"  align="center">
	                        <?
							if(!(trim($rlz_date_arr[$row_result[csf('id')]])=="0000-00-00" || trim($rlz_date_arr[$row_result[csf('id')]])==""))
							{
								echo change_date_format($rlz_date_arr[$row_result[csf('id')]]);
							}
							else
							{
								echo "&nbsp;";
							}
	                         ?>
	                        </td>
	                        <td width="70" style="word-break: break-all;" align="center" title="From Bank or Buyer Submit Date To Actual Realization Date">
							<?
							if( $realization_sub_day!="" && $realization_sub_day!='0000-00-00' &&$rlz_date_arr[$row_result[csf('id')]]!="" && $rlz_date_arr[$row_result[csf('id')]]!='0000-00-00')
							{
								$diff_rlz=datediff("d",$realization_sub_day,$rlz_date_arr[$row_result[csf('id')]]);
							}
							if($diff_rlz>0) echo $diff_rlz." days";
							?></td>
	                        <td width="70" style="word-break: break-all;" align="right"><? if(!(trim($rlz_date_arr[$row_result[csf('id')]])=="0000-00-00" || trim($rlz_date_arr[$row_result[csf('id')]])==""))
							{
								echo number_format($row_result[csf('net_invo_value')],2,'.',''); $total_rlz_amt+=$row_result[csf('net_invo_value')];
							}
							else
							{
								echo "";
							}
							?></td>
	                        <td width="100" style="word-break: break-all;"><? echo $row_result[csf('remarks')];?></td>
	                        <td width="100" style="word-break: break-all;"><? echo $row_result[csf('feeder_vessel')];?></td>
	                        <td width="100" style="word-break: break-all;"><? echo $row_result[csf('mother_vessel')];?></td>
	                        <td width="70" style="word-break: break-all;" align="center"><?  if($row_result[csf('etd_destination')]!="0000-00-00" && $row_result[csf('etd_destination')]!="") {echo change_date_format($row_result[csf('etd_destination')]);} else { echo "&nbsp;";} ?></td>
	                        <td ><? echo $bank_sub_data[$row_result[csf('id')]]["bnk_to_bnk_cour_no"];  ?></td>
						</tr>
						<?
						$k++;
	                }
	                ?>
	                </tbody>
	            </table>
	     		 <table width="5390" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="report_table_footer" align="left">
	            	<tfoot>
	                    <tr>
	                        <th width="50">&nbsp;</th>
	                        <th width="100">&nbsp;</th>

	                        <th width="100">&nbsp;</th>
	                        <th width="100">&nbsp;</th>

	                        
	                        <th width="70">&nbsp;</th>
	                        <th width="100">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="100">&nbsp;</th>
	                        <th width="150">&nbsp;</th>
	                        <th width="90">&nbsp;</th>
	                        <th width="70">Total:</th>
	                        <th width="100" id="total_ex_fact_qnty" align="right"><? echo number_format($total_ex_fact_qnty,2); ?></th>
	                        <th width="100" id="total_invoice_qty" align="right"><? echo number_format($total_invoice_qty,2); ?></th>
	                        <th width="100" id="total_invoice_qty_pcs" align="right"><? echo number_format($total_invoice_qty_pcs,2); ?></th>
	                        <th width="80" id="total_carton_qty" align="right"><? echo number_format($total_carton_qty,2); ?></th>
	                        <th width="80" id="value_total_avg_price"  align="right"><? echo number_format($total_avg_price,2);  ?></th>
	                        <th width="100" id="value_total_grs_value"  align="right"><? echo number_format($total_grs_value,2);  ?></th>
	                        <th width="80" id="value_total_discount_value"  align="right"><? echo number_format($total_discount_value,2);  ?></th>
	                        <th width="70" id="value_total_bonous_value"  align="right"><? echo number_format($total_bonous_value,2);  ?></th>
	                        <th width="70" id="value_total_claim_value"  align="right"><? echo number_format($total_claim_value,2);  ?></th>
	                        <th width="80" id="value_total_commission_value"  align="right"><? echo number_format($total_commission_value,2);  ?></th>

	                        <th width="80"  align="right"><? echo number_format($total_other_discount_value,2); ?></th>
	                        <th width="80"  align="right"><? echo number_format($total_upcharge_value,2);?></th>
	                        <th width="100"  id="value_total_net_invo_value"  align="right"><? echo number_format($total_order_qnty,2);  ?></th>
	                        <th width="80">&nbsp;</th>

	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="80">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>

	                        
	                        <th width="100">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="100">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>

	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="100">&nbsp;</th>
	                        <th width="70">&nbsp;</th>

	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70"><? echo number_format($total_carton_gross_weight,2);  ?></th>
	                        <th width="70"><? echo number_format($total_carton_net_weight,2);  ?></th>

	                        <th width="80">&nbsp;</th>
	                        <th width="80">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70"  id="value_total_rlz_amt"  align="right"><? echo number_format($total_rlz_amt,2);  ?></th>
	                        <th width="100">&nbsp;</th>
	                        <th width="100">&nbsp;</th>
	                        <th width="100">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th >&nbsp;</th>
	                    </tr>
	                </tfoot>
	            </table>
     		</div>
    	</div>
    	<div align="left" style="font-weight:bold; margin-left:30px;"><? echo "User Id : ". $user_arr[$user_id] ." , &nbsp; THIS IS SYSTEM GENERATED STATEMENT, NO SIGNATURE REQUIRED ."; ?></div>
		<?
		foreach (glob("$user_id*.xls") as $filename)
		{

			if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename=$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc,ob_get_contents());
		//$filename=$user_id."_".$name.".xls";
		echo "$total_data****".$RptType."****".$filename;
		//echo "****".$RptType;
	}
	else if($RptType==5) //DETAILS 4 (shafiq)
	{
		$user_arr = return_library_array("select id,user_name from user_passwd ","id","user_name");
		$bank_arr=return_library_array("select id,bank_name from   lib_bank","id","bank_name");
		$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$location_arr=return_library_array("select id,location_name from  lib_location","id","location_name");
		$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
		$supplier_arr=return_library_array("select id,supplier_name from lib_supplier","id","supplier_name");
		$country_arr=return_library_array("select id,country_name from  lib_country","id","country_name"); 

		$buyer_submit_date_arr=return_library_array(" SELECT b.invoice_id,a.submit_date from com_export_doc_submission_mst a,com_export_doc_submission_invo b where a.id=b.doc_submission_mst_id and a.entry_form=39","invoice_id","submit_date");

		/*$rlz_date_arr=return_library_array(" SELECT a.invoice_id,b.received_date,b.is_invoice_bill
		from com_export_doc_submission_invo a, com_export_proceed_realization b
		where a.doc_submission_mst_id=b.invoice_bill_id and b.is_invoice_bill  = 1
		union all
		select b.invoice_bill_id as invoice_id, b.received_date , b.is_invoice_bill
		from  com_export_proceed_realization b
		where  b.is_invoice_bill  = 2
		order by invoice_id","invoice_id","received_date");
		*/
		$exfact_qnty_arr=return_library_array(" SELECT invoice_no, sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty
		from pro_ex_factory_mst where status_active=1 and is_deleted=0 and invoice_no>0 group by invoice_no","invoice_no","ex_factory_qnty");
		$variable_standard_arr=return_library_array(" SELECT monitor_head_id,monitoring_standard_day from variable_settings_commercial where status_active=1 and is_deleted=0 and company_name=$cbo_company_name and variable_list=19","monitor_head_id","monitoring_standard_day");

		$sql_order_set=sql_select("SELECT a.mst_id, a.po_breakdown_id, a.current_invoice_qnty, (a.current_invoice_qnty*c.total_set_qnty) as invoice_qnty_pcs, c.total_set_qnty from com_export_invoice_ship_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0");
		$inv_qnty_pcs_arr=array();
		foreach($sql_order_set as $row)
		{
			$inv_qnty_pcs_arr[$row[csf("mst_id")]]+=$row[csf("invoice_qnty_pcs")];
		}

		$cbo_company_name=str_replace("'","",$cbo_company_name);
        if($cbo_company_name!=0) $cbo_company_name = $cbo_company_name; else $cbo_company_name="%%";

        $cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
        if($cbo_buyer_name == 0) $cbo_buyer_name="%%"; else $cbo_buyer_name = $cbo_buyer_name;

        $cbo_lien_bank=str_replace("'","",$cbo_lien_bank);
        if($cbo_lien_bank == 0) $cbo_lien_bank="%%"; else $cbo_lien_bank = $cbo_lien_bank;
		//echo  $cbo_lien_bank;die;
        $cbo_location=str_replace("'","",$cbo_location);
        if($cbo_location == 0) $cbo_location="%%"; else $cbo_location = $cbo_location;

        if($cbo_based_on == 0) $cbo_based_on="%%"; else $cbo_based_on = $cbo_based_on;

        $txt_date_from=str_replace("'","",$txt_date_from);
        if(trim($txt_date_from)!= "") $txt_date_from  =$txt_date_from;

        $txt_date_to=str_replace("'","",$txt_date_to);
        if(trim($txt_date_to)!= "") $txt_date_to = $txt_date_to;

		$forwarder_name=str_replace("'","",$forwarder_name);
		if($forwarder_name!=0) $forwarder_cond=" and a.forwarder_name='$forwarder_name'"; else $forwarder_cond="";
		$txt_invoice_no=str_replace("'","",$txt_invoice_no);
		if($txt_invoice_no!="") $invoice_cond=" and a.invoice_no like '%$txt_invoice_no'"; else $invoice_cond="";

		if($cbo_ascending_by!="" && $cbo_ascending_by==1 ) 
		{
			$ascending_by = "invoice_no";
			$ascendig_cond=" order by $ascending_by asc ";
		} elseif ($cbo_ascending_by!="" && $cbo_ascending_by==2) {
			$ascending_by = "invoice_date";
			$ascendig_cond=" order by $ascending_by asc ";
		} elseif ($cbo_ascending_by!="" && $cbo_ascending_by==3) {
			$ascending_by = "exp_form_no";
			$ascendig_cond=" order by $ascending_by asc ";
		}elseif ($cbo_ascending_by!="" && $cbo_ascending_by==4) {

			$ascending_by = "exp_form_date";
			$ascendig_cond=" order by $ascending_by asc ";
		}else{
			$ascendig_cond="";
		}

        //if(trim($data[7])!="") $cbo_year2=$data[7];
        if ($txt_date_from!="" && $txt_date_to!="")
        {
			if($cbo_based_on ==0)
			{
				$str_cond=" and a.invoice_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==1)
			{
				$str_cond=" and a.invoice_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==2)
			{
				$str_cond=" and a.ex_factory_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==3)
			{
				$str_cond=" and a.actual_shipment_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==4)
			{
				$str_cond=" and a.bl_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==5)
			{
				$str_cond=" and a.ship_bl_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==6)
			{
				$str_cond=" and e.received_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==7)
			{

				if($db_type==0) $str_cond=" and a.insert_date between '$txt_date_from 00:00:01' and  '$txt_date_to 23:59:59'";
				else if($db_type==2) $str_cond=" and a.insert_date between '$txt_date_from 12:00:01 AM' and  '$txt_date_to 11:59:59 PM'";
			}
			else if($cbo_based_on ==8) // Un-Realization
			{
				$str_cond=" and a.invoice_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==9) // Bank Submission Date
			{
				$sub_cond=" and d.submit_date between '$txt_date_from' and  '$txt_date_to'";
				$sub_cond2=" and e.submit_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==10) // Un-Submitted Invoice
			{
				$str_cond=" and a.invoice_date between '$txt_date_from' and  '$txt_date_to'";
			}
        }
        else
        {
        	$str_cond="";
        }
		$shipping_mode=str_replace("'","",$shipping_mode);
		$ship_cond="";
		if($shipping_mode!=0) $ship_cond=" and a.shipping_mode=$shipping_mode";

		$txt_lc_sc_no=str_replace("'","",$txt_lc_sc_no);

		// echo  $str_cond;die;

		$sub_sql=sql_select("SELECT b.invoice_id, a.courier_date, a.submit_date, a.bnk_to_bnk_cour_no, a.bank_ref_no,a.bank_ref_date, a.possible_reali_date 
			from com_export_doc_submission_mst a,com_export_doc_submission_invo b 
			where a.id=b.doc_submission_mst_id and a.entry_form=40 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		$bank_sub_data=array();
		foreach($sub_sql as $row)

		{
			$bank_sub_data[$row[csf("invoice_id")]]["courier_date"]=$row[csf("courier_date")];
			$bank_sub_data[$row[csf("invoice_id")]]["submit_date"]=$row[csf("submit_date")];
			$bank_sub_data[$row[csf("invoice_id")]]["bank_ref_date"]=$row[csf("bank_ref_date")];
			$bank_sub_data[$row[csf("invoice_id")]]["bnk_to_bnk_cour_no"]=$row[csf("bnk_to_bnk_cour_no")];
			$bank_sub_data[$row[csf("invoice_id")]]["bank_ref_no"]=$row[csf("bank_ref_no")];
			$bank_sub_data[$row[csf("invoice_id")]]["possible_reali_date"]=$row[csf("possible_reali_date")];
		}


		if($cbo_based_on==6)
		{
			if($db_type==0)
			{
				$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor
				FROM com_export_invoice_ship_mst a, com_export_lc b, com_export_doc_submission_invo d, com_export_proceed_realization e
				WHERE a.lc_sc_id=b.id and a.id=d.invoice_id and d.doc_submission_mst_id=e.invoice_bill_id  and a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' AND b.export_lc_no LIKE '%$txt_lc_sc_no%' and a.is_lc=1 and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  $str_cond $forwarder_cond $ship_cond $invoice_cond

				UNION ALL

				SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor
				FROM com_export_invoice_ship_mst a, com_sales_contract c , com_export_doc_submission_invo d, com_export_proceed_realization e
				WHERE a.lc_sc_id=c.id and a.id=d.invoice_id and d.doc_submission_mst_id=e.invoice_bill_id and a.is_lc=2 and a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' AND c.contract_no LIKE '%$txt_lc_sc_no%' and  a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $str_cond $forwarder_cond $ship_cond $invoice_cond

				UNION ALL

				SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date, a. 	invoice_value, a.discount_ammount, a.bonus_ammount, a.claim_ammount, a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor
				FROM com_export_invoice_ship_mst a, com_sales_contract c , com_export_proceed_realization e
				WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=2 and a.lc_sc_id=c.id and e.invoice_bill_id = a.id and e.is_invoice_bill = 2 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond $ascendig_cond";

			}
			else
			{
				$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor
			FROM com_export_invoice_ship_mst a, com_export_lc b , com_export_doc_submission_invo d, com_export_proceed_realization e
			WHERE a.lc_sc_id=b.id and a.id=d.invoice_id and d.doc_submission_mst_id=e.invoice_bill_id  and a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' AND b.export_lc_no LIKE '%$txt_lc_sc_no%' and a.is_lc=1 and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  $str_cond $forwarder_cond $ship_cond $invoice_cond

			UNION ALL

			SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor
			FROM com_export_invoice_ship_mst a, com_sales_contract c, com_export_doc_submission_invo d, com_export_proceed_realization e
			WHERE a.lc_sc_id=c.id and a.id=d.invoice_id and d.doc_submission_mst_id=e.invoice_bill_id  and a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' AND c.contract_no LIKE '%$txt_lc_sc_no%' and a.is_lc=2 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $str_cond $forwarder_cond $ship_cond $invoice_cond
			UNION ALL

			SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor
			FROM com_export_invoice_ship_mst a, com_sales_contract c , com_export_proceed_realization e
			WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=2 and a.lc_sc_id=c.id and e.invoice_bill_id = a.id and e.is_invoice_bill = 2  and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond $ascendig_cond";
			}
		}
		else if($cbo_based_on==8) // Un-Realization
		{
			if($db_type==0)
			{
				$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor
				FROM com_export_invoice_ship_mst a, com_export_lc b
				WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=1 and a.lc_sc_id=b.id and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 AND b.export_lc_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond and a.id not in(select a.invoice_id from com_export_doc_submission_invo a, com_export_proceed_realization b where a.doc_submission_mst_id=b.invoice_bill_id and a.is_lc=1)

				UNION ALL

				SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor
				FROM com_export_invoice_ship_mst a, com_sales_contract c
				WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=2 and a.lc_sc_id=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0  AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond and a.id not in(select a.invoice_id from com_export_doc_submission_invo a, com_export_proceed_realization b where a.doc_submission_mst_id=b.invoice_bill_id and a.is_lc=2)  $ascendig_cond";
			}
			else
			{
				/*$realized_invoice = sql_select("SELECT a.invoice_id from com_export_doc_submission_invo a, com_export_proceed_realization b
				where a.doc_submission_mst_id=b.invoice_bill_id and a.is_lc=2 and b.status_active = 1 and b.is_deleted = 0
				union all
				select b.invoice_bill_id as invoice_id from  com_export_proceed_realization b where b.is_invoice_bill=2 and b.benificiary_id = $cbo_company_name and b.status_active = 1 and b.is_deleted = 0");
				foreach ($realized_invoice as $val)
				{
					$all_realized_invoice_arr[$val[csf("invoice_id")]] = $val[csf("invoice_id")];
				}

				if(count($all_realized_invoice_arr)>999)
				{
					$all_realized_invoice_chunk_arr=array_chunk($all_realized_invoice_arr, 999);
					$all_realized_invoice_cond=" and (";
					foreach ($all_realized_invoice_chunk_arr as $value)
					{
						$all_realized_invoice_cond .="a.id not in (".implode(",", $value).") or ";
					}
					$all_realized_invoice_cond=chop($all_realized_invoice_cond,"or ");
					$all_realized_invoice_cond.=")";
				}
				else
				{
					$all_realized_invoice_cond=" and a.id not in (".implode(",", $all_realized_invoice_arr).")";
				}


				$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor
				FROM com_export_invoice_ship_mst a, com_export_lc b
				WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' AND b.export_lc_no LIKE '%$txt_lc_sc_no%' and a.is_lc=1 and a.lc_sc_id=b.id and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  $str_cond $forwarder_cond $ship_cond $invoice_cond and a.id not in(select a.invoice_id from com_export_doc_submission_invo a, com_export_proceed_realization b where a.doc_submission_mst_id=b.invoice_bill_id and a.is_lc=1)

				UNION ALL

				SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor
				FROM com_export_invoice_ship_mst a, com_sales_contract c
				WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank'  AND c.contract_no LIKE '%$txt_lc_sc_no%' and a.is_lc=2 and a.lc_sc_id=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $str_cond $forwarder_cond $ship_cond $invoice_cond $all_realized_invoice_cond  $ascendig_cond";*/
				
				$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor
				FROM com_export_invoice_ship_mst a, com_export_lc b
				WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=1 and a.lc_sc_id=b.id and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 AND b.export_lc_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond and a.id not in(select a.invoice_id from com_export_doc_submission_invo a, com_export_proceed_realization b where a.doc_submission_mst_id=b.invoice_bill_id and a.is_lc=1)

				UNION ALL

				SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor
				FROM com_export_invoice_ship_mst a, com_sales_contract c
				WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=2 and a.lc_sc_id=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0  AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond and a.id not in(select a.invoice_id from com_export_doc_submission_invo a, com_export_proceed_realization b where a.doc_submission_mst_id=b.invoice_bill_id and a.is_lc=2)  $ascendig_cond";
			}
		}
		else if($cbo_based_on==9) // Bank Submission Date
		{
			$order_by_cond = "";
			switch ($cbo_based_on) 
			{
				case 1:
					$order_by_cond = "invoice_date";
					break;
				case 2:
					$order_by_cond = "ex_factory_date";
					break;
				case 3:
					$order_by_cond = "actual_shipment_date";
					break;
				case 4:
					$order_by_cond = "bl_date";
					break;
				case 5:
					$order_by_cond = "ship_bl_date";
					break;
				case 6:
					$order_by_cond = "id";
					break;
				case 7:
					$order_by_cond = "insert_date";
					break;							
				default:
					$order_by_cond = "id";
					break;
			}

			if($db_type==0)

			{

				$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor, d.submit_date, d.bank_ref_date
				FROM com_export_invoice_ship_mst a, com_export_lc b, com_export_doc_submission_invo c, com_export_doc_submission_mst d
				WHERE a.lc_sc_id=b.id and a.id=c.invoice_id and c.doc_submission_mst_id=d.id and d.entry_form=40 and a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' AND b.export_lc_no LIKE '%$txt_lc_sc_no%' and a.is_lc=1 and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $str_cond $forwarder_cond $ship_cond $invoice_cond $sub_cond

				UNION ALL

				SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date, a.invoice_value, a.discount_ammount, a.bonus_ammount, a.claim_ammount, a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor, d.submit_date, d.bank_ref_date
				FROM com_export_invoice_ship_mst a, com_sales_contract c, com_export_doc_submission_invo d, com_export_doc_submission_mst e
				WHERE a.lc_sc_id=c.id and a.id=d.invoice_id and d.doc_submission_mst_id=e.id and e.entry_form=40 and a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=2 a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond $sub_cond2 $ascendig_cond"; //ORDER BY  order by $order_by_cond";//a.ex_factory_date";//id
			}
			else
			{
				$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor, d.submit_date, d.bank_ref_date
				FROM com_export_invoice_ship_mst a, com_export_lc b, com_export_doc_submission_invo c, com_export_doc_submission_mst d
				WHERE a.lc_sc_id=b.id and a.id=c.invoice_id and c.doc_submission_mst_id=d.id and d.entry_form=40 and a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=1 and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 AND b.export_lc_no LIKE '%$txt_lc_sc_no%' $forwarder_cond $ship_cond $invoice_cond $sub_cond

				UNION ALL

				SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor, e.submit_date, e.bank_ref_date
				FROM com_export_invoice_ship_mst a, com_sales_contract c, com_export_doc_submission_invo d, com_export_doc_submission_mst e
				WHERE a.lc_sc_id=c.id and a.id=d.invoice_id and d.doc_submission_mst_id=e.id and e.entry_form=40 and a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=2 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 AND c.contract_no LIKE '%$txt_lc_sc_no%' $forwarder_cond $ship_cond $invoice_cond $sub_cond2 $ascendig_cond"; //ORDER BY $order_by_cond";
			}
			//echo $sql;
		}
		else if($cbo_based_on==10) // Un-Submitted Invoice
		{
			$order_by_cond = "";
			switch ($cbo_based_on) 
			{
				case 1:
					$order_by_cond = "invoice_date";
					break;
				case 2:
					$order_by_cond = "ex_factory_date";
					break;
				case 3:
					$order_by_cond = "actual_shipment_date";
					break;
				case 4:
					$order_by_cond = "bl_date";
					break;
				case 5:
					$order_by_cond = "ship_bl_date";
					break;
				case 6:
					$order_by_cond = "id";
					break;
				case 7:
					$order_by_cond = "insert_date";
					break;							
				default:
					$order_by_cond = "id";
					break;
			}

			if($db_type==0)
			{
				$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor
				FROM com_export_invoice_ship_mst a, com_export_lc b
				WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' AND b.export_lc_no LIKE '%$txt_lc_sc_no%' and a.is_lc=1 and a.lc_sc_id=b.id and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  $str_cond $forwarder_cond $ship_cond $invoice_cond

				UNION ALL

				SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date, a.invoice_value, a.discount_ammount, a.bonus_ammount, a.claim_ammount, a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor
				FROM com_export_invoice_ship_mst a, com_sales_contract c
				WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=2 and a.lc_sc_id=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond  $ascendig_cond"; //ORDER BY  order by $order_by_cond";//a.ex_factory_date";//id
			}
			else
			{
				$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor
				FROM com_export_invoice_ship_mst a, com_export_lc b
				WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=1 and a.lc_sc_id=b.id and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 AND b.export_lc_no LIKE '%$txt_lc_sc_no%'  $str_cond $forwarder_cond $ship_cond $invoice_cond and a.id not in (SELECT d.invoice_id FROM com_export_doc_submission_invo d, com_export_doc_submission_mst e WHERE d.doc_submission_mst_id=e.id and e.entry_form=40 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0)

				UNION ALL

				SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor
				FROM com_export_invoice_ship_mst a, com_sales_contract c
				WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=2 and a.lc_sc_id=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond and a.id not in(SELECT d.invoice_id FROM com_export_doc_submission_invo d, com_export_doc_submission_mst e
				WHERE d.doc_submission_mst_id=e.id and e.entry_form=40 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0) $ascendig_cond"; //ORDER BY $order_by_cond";
			}
			// echo $sql;
		}
		else
		{
			$order_by_cond = "";
			switch ($cbo_based_on) 
			{
				case 1:
					$order_by_cond = "invoice_date";
					break;
				case 2:
					$order_by_cond = "ex_factory_date";
					break;
				case 3:
					$order_by_cond = "actual_shipment_date";
					break;
				case 4:
					$order_by_cond = "bl_date";
					break;
				case 5:
					$order_by_cond = "ship_bl_date";
					break;
				case 6:
					$order_by_cond = "id";
					break;
				case 7:
					$order_by_cond = "insert_date";
					break;							
				default:
					$order_by_cond = "id";
					break;
			}

			if($db_type==0)
			{
				$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor
				FROM com_export_invoice_ship_mst a, com_export_lc b
				WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' AND b.export_lc_no LIKE '%$txt_lc_sc_no%' and a.is_lc=1 and a.lc_sc_id=b.id and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  $str_cond $forwarder_cond $ship_cond $invoice_cond

				UNION ALL

				SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date, a.invoice_value, a.discount_ammount, a.bonus_ammount, a.claim_ammount, a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor
				FROM com_export_invoice_ship_mst a, com_sales_contract c
				WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=2 and a.lc_sc_id=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond  $ascendig_cond"; //ORDER BY  order by $order_by_cond";//a.ex_factory_date";//id
			}
			else
			{
				$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor
				FROM com_export_invoice_ship_mst a, com_export_lc b
				WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=1 and a.lc_sc_id=b.id and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 AND b.export_lc_no LIKE '%$txt_lc_sc_no%'  $str_cond $forwarder_cond $ship_cond $invoice_cond

				UNION ALL

				SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor
				FROM com_export_invoice_ship_mst a, com_sales_contract c
				WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=2 and a.lc_sc_id=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond  $ascendig_cond"; //ORDER BY $order_by_cond";
			}
			// echo $sql;
		}
		// echo $sql;die;
        $sql_re=sql_select($sql);$k=1;

		$invoice_id_arr=array();
		foreach($sql_re as $row_result)
	    {
			$invoice_id_arr[$row_result[csf("id")]] = $row_result[csf("id")];
		}
		$invoice_ids = implode(",", $invoice_id_arr);
		$realization_deduction_sql="select  a.invoice_id,c.type,b.received_date, c.document_currency from com_export_doc_submission_invo a, com_export_proceed_realization b , com_export_proceed_rlzn_dtls c where a.doc_submission_mst_id=b.invoice_bill_id and b.id = c.mst_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.invoice_id in($invoice_ids)";
		$realization_deduction_rs=sql_select($realization_deduction_sql);
		$invoice_real_ded_arr=array();
		foreach($realization_deduction_rs as $row)
	    {
			$invoice_real_ded_arr[$row[csf("invoice_id")]]["received_date"] = $row[csf("received_date")];
			$invoice_real_ded_arr[$row[csf("invoice_id")]][$row[csf("type")]] += $row[csf("document_currency")];
		}
        /*echo "<pre>";
        print_r($sql_re);*/
		ob_start();

		?>	
		<style type="text/css">
			table tr td{ word-break: break-all;word-wrap: break-word; }
		</style>	
		<div style="width:1120px; margin: 0 auto;"> 
		<table width="1110" cellpadding="0" cellspacing="0" id="caption">
            <tr>
            <td align="center" width="100%" colspan="21" class="form_caption" ><strong style="font-size:18px">Company Name:<? echo " ". $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
            </tr>
            <tr>
            <td align="center" width="100%" colspan="21" class="form_caption" ><strong style="font-size:18px"><? echo $report_title; ?>
            <?
			if($cbo_based_on!=0)
			{
			?>
            (<strong style="font-size:18px">Based On:<? $based_on_arr=array(1=>"Invoice Date",2=>"Exfactory Date",3=>"Actual Date",4=>"BL/Cargo Date",5=>"Shipping Bill Date",6=>"Realization Date",7=>"Insert Date",8=>"Un-Realization",9=>"Bank Submission Date",10=>"Un-Submitted Invoice"); echo " ". $based_on_arr[$cbo_based_on]; ?></strong>)

            <?
			}
			?></strong></td>
            </tr>
            </table>
    		<br />           
            <table width="1510" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_2" align="left">
                <thead>
                	<tr>
                		<th width="550" colspan="10">Ex-Factory Status</th>
                		<th width="480" colspan="6">Bank Submission</th>
                		<th width="160" colspan="3">Realization</th>
                	</tr>
                    <tr>
                        <th rowspan="2" width="30">Sl</th>
                        <th rowspan="2" width="80">Ex. Factory Date</th>
                        <th rowspan="2" width="100">Buyer</th>
                        <th rowspan="2" width="100">Export L/C No.</th>
                        <th rowspan="2" width="80">Invoice No.</th>
                        <th rowspan="2" width="80">Pcs</th>
                        <th rowspan="2" width="80">Invoice Value</th>
                        <th rowspan="2" width="80">Discount Value</th>
                        <th rowspan="2" width="80">Commission Value</th>
                        <th rowspan="2" width="80">N. Invoice Value</th>

                        <th rowspan="2" width="80">BL No</th>
                        <th rowspan="2" width="80">BL Date</th>
                        <th rowspan="2" width="80">Bank Sub. Date</th>
                        <th rowspan="2" width="80">Bank Ref/ Bill No</th>
                        <th rowspan="2" width="80">Bank Ref Date</th>
                        <th rowspan="2" width="80">Possible Rlz. Date</th>

                        <th rowspan="2" width="80">Realise Value</th>
						<th rowspan="2" width="80">Short Realise</th>
                        <th rowspan="2" width="80">Received Date</th>                      

                    </tr>
                </thead>
            </table>
            <div style="width:1530px; overflow-y:scroll; max-height:290px;font-size:12px; overflow-x:hidden;" id="scroll_body" align="left">
	            <table width="1510" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
	                <tbody>
	                <?
	                $gr_pcs = 0; $gr_inv_val = 0; $gr_discount_ammount = 0; $gr_commission_value=0; $gr_net_inv_val = 0; $gr_realiz_val 	= 0;	                
	                foreach($sql_re as $row_result)
	                {	                	
						$id=$row_result[csf('id')];
	                	if($exfact_qnty_arr[$id]>0)
	                	{
							if ($k%2==0)
							$bgcolor="#E9F3FF";
							else
							$bgcolor="#FFFFFF";
							$exfact_date_calculate=$row_result[csf('ex_factory_date')];//$variable_standard_arr
							$bl_date_calculate=$row_result[csf('bl_date')];
							$variable_standard_bl_day=$possiable_bl_date=$realization_sub_day=$doc_presentation_days=$possiable_bank_sub_date=$variable_standard_gsp_day=$possiable_gsp_date=$variable_standard_co_day=$possiable_co_date="";
							if($exfact_date_calculate!="" && $exfact_date_calculate!='0000-00-00')
							{
								$variable_standard_bl_day=$variable_standard_arr[1]*60*60*24;
								$possiable_bl_date=date('d-m-Y',strtotime($exfact_date_calculate)+$variable_standard_bl_day);

							}
							if($bl_date_calculate!="" && $bl_date_calculate!='0000-00-00')
							{
								if($row_result[csf("type")]==1)
								{
									$realization_sub_day=$bank_sub_data[$row_result[csf('id')]]["submit_date"];
									$doc_presentation_days=$row_result[csf("doc_presentation_days")]*60*60*24;
									$possiable_bank_sub_date=date('d-m-Y',strtotime($bl_date_calculate)+$doc_presentation_days);
								}
								if($row_result[csf("type")]==2)
								{
									$realization_sub_day=$buyer_submit_date_arr[$row_result[csf('id')]];
									$doc_presentation_days=$row_result[csf("doc_presentation_days")]*60*60*24;
									$possiable_buyer_sub_date=date('d-m-Y',strtotime($bl_date_calculate)+$doc_presentation_days);
								}
								$variable_standard_gsp_day=$variable_standard_arr[2]*60*60*24;
								$possiable_gsp_date=date('d-m-Y',strtotime($bl_date_calculate)+$variable_standard_gsp_day);
								$variable_standard_co_day=$variable_standard_arr[3]*60*60*24;
								$possiable_co_date=date('d-m-Y',strtotime($bl_date_calculate)+$variable_standard_co_day);
							}
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
		                    	<td width="30" style="word-break: break-all;"><? echo $k;//$row_result[csf('id')];?></td>
		                    	<td align="center" width="80"><? if($row_result[csf('ex_factory_date')]!="0000-00-00" && $row_result[csf('ex_factory_date')]!="") {echo change_date_format($row_result[csf('ex_factory_date')]);} else {echo "&nbsp;";} ?></td>
		                    	<td width="100"><? echo  $buyer_arr[$row_result[csf('buyer_id')]];?></td>
		                    	<td width="100"><? echo $row_result[csf('lc_sc_no')];?></td>
		                    	<td width="80" align="center">
		                    		<p>
		                        		<a href='##' style='color:#000' onClick="print_report('<? echo $row_result[csf('id')] ;?>','invoice_report_print','../export_details/requires/export_information_entry_controller')">
			                        		<font color="blue">
			                        			<b>
			                        				<? echo $row_result[csf('invoice_no')];?>		                        					
			                        			</b>
			                        		</font>
		                        		</a>
		                        	</p>
		                        </td>
		                    	<td align="right" width="80" style="word-break:break-word; word-wrap: break-word; "><? echo number_format($inv_qnty_pcs_arr[$row_result[csf('id')]],2);?></td>
		                    	<td align="right" width="80" style="word-break:break-word; word-wrap: break-word; "><? echo number_format($row_result[csf('invoice_value')],2,'.','');?></td>
		                    	<td align="right" width="80" style="word-break:break-word; word-wrap: break-word; "><? echo number_format($row_result[csf('discount_ammount')],2,'.','');?></td>
		                    	<td align="right" width="80" style="word-break:break-word; word-wrap: break-word; "><? echo number_format($row_result[csf('commission')],2,'.','');?></td>
		                    	<td align="right" width="80" style="word-break:break-word; word-wrap: break-word; "><? echo number_format($row_result[csf('net_invo_value')],2,'.','');?></td>

		                    	<td width="80" align="center" style="word-break:break-word; word-wrap: break-word; "><? echo $row_result[csf('bl_no')];?></td>
		                    	<td width="80" align="center"><? echo change_date_format($row_result[csf('bl_date')]);?></td>
		                    	<td width="80">
		                    		<?
		                    			if ($cbo_based_on==9) 
		                    			{
		                    				echo change_date_format($row_result[csf('submit_date')]);
		                    			}
		                    			else
		                    			{		                    				
			                            if(trim($bank_sub_data[$row_result[csf('id')]]["submit_date"])!='0000-00-00' && trim($bank_sub_data[$row_result[csf('id')]]["submit_date"])!='') echo change_date_format(trim($bank_sub_data[$row_result[csf('id')]]["submit_date"])); else echo "&nbsp;";
		                    			}
			                        ?>	                        	
		                        </td>
		                    	<td width="80"><? echo $bank_sub_data[$row_result[csf('id')]]["bank_ref_no"]; ?></td>
		                    	<td width="80">
		                    		<?
		                    			if ($cbo_based_on==9) 
		                    			{
		                    				echo change_date_format($row_result[csf('bank_ref_date')]);
		                    			}
		                    			else
		                    			{
		                    				if(trim($bank_sub_data[$row_result[csf('id')]]["bank_ref_date"])!='0000-00-00' && trim($bank_sub_data[$row_result[csf('id')]]["bank_ref_date"])!='') echo change_date_format(trim($bank_sub_data[$row_result[csf('id')]]["bank_ref_date"])); else echo "&nbsp;";
		                    			}			                            
			                        ?>
		                    	</td>
		                    	<td width="80"  style="word-break: break-all;"  align="center">
			                        <?
									if(!(trim($bank_sub_data[$row_result[csf('id')]]["possible_reali_date"])=="0000-00-00" || trim($bank_sub_data[$row_result[csf('id')]]["possible_reali_date"])==""))
									{
										echo change_date_format($bank_sub_data[$row_result[csf('id')]]["possible_reali_date"]);
									}
									else
									{
										echo "&nbsp;";
									}
			                        ?>
		                        </td>
		                    	<td width="80" align="right">
		                    		<? 
									echo number_format($invoice_real_ded_arr[$row_result[csf("id")]][1],2,'.','');	                
									$gr_realiz_val 	+= $invoice_real_ded_arr[$row_result[csf("id")]][1];
									?>
									
								</td>
								<td width="80" align="right">
		                    		<? 
									echo number_format($invoice_real_ded_arr[$row_result[csf("id")]][0],2,'.','');
									?>
									
								</td>

		                    	<td width="80" align="center">
		                    		<?
									echo ($invoice_real_ded_arr[$row_result[csf("id")]]['received_date'])?change_date_format($invoice_real_ded_arr[$row_result[csf("id")]]['received_date']):"";
			                         ?>	                    			
		                    	</td>	                        
							</tr>
							<?
							$k++;
							$gr_pcs 		+= $inv_qnty_pcs_arr[$row_result[csf('id')]];	 
							$gr_inv_val += $row_result[csf('invoice_value')];
							$gr_discount_ammount += $row_result[csf('discount_ammount')];
							$gr_commission_value += $row_result[csf('commission')];
		                	$gr_net_inv_val += $row_result[csf('net_invo_value')];
		                }
	                }
	                ?>
	                </tbody>
	            </table>
	     		 <table width="1510" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="report_table_footer" align="left">
	            	<tfoot>
	                    <tr>
	                        <th width="30">&nbsp;</th>
	                        <th width="80">&nbsp;</th>
	                        <th width="100">&nbsp;</th>
	                        <th width="100">&nbsp;</th>	                        
	                        <th width="80">Total:</th>
	                        <th width="80"><? echo number_format($gr_pcs,2); ?></th>
	                        <th width="80"><? echo number_format($gr_inv_val,2); ?></th>
	                        <th width="80"><? echo number_format($gr_discount_ammount,2); ?></th>
	                        <th width="80"><? echo number_format($gr_commission_value,2); ?></th>
	                        <th width="80"><? echo number_format($gr_net_inv_val,2); ?></th>

	                        <th width="80">&nbsp;</th>
	                        <th width="80">&nbsp;</th>
	                        <th width="80">&nbsp;</th>
	                        <th width="80">&nbsp;</th>
	                        <th width="80">&nbsp;</th>
	                        <th width="80">&nbsp;</th>

	                        <th width="80"><? echo number_format($gr_realiz_val,2); ?></th>
	                        <th width="80">&nbsp;</th>
							<th width="80">&nbsp;</th>
	                    </tr>
	                </tfoot>
	            </table>
     		</div>
     		<div align="left" style="font-weight:bold; margin-left:30px;"><? echo "User Id : ". $user_arr[$user_id] ." , &nbsp; THIS IS SYSTEM GENERATED STATEMENT, NO SIGNATURE REQUIRED ."; ?></div>
    	</div>
    	
		<?
		foreach (glob("$user_id*.xls") as $filename)
		{

			if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename=$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc,ob_get_contents());
		//$filename=$user_id."_".$name.".xls";
		echo "$total_data****".$RptType."****".$filename;
		//echo "****".$RptType;
	}
	if($RptType==6) //DETAILS 5
	{
		$cbo_company_name=str_replace("'","",$cbo_company_name);
		$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
		$cbo_lien_bank=str_replace("'","",$cbo_lien_bank);
		$cbo_location=str_replace("'","",$cbo_location);
		$txt_date_from=str_replace("'","",$txt_date_from);
		$txt_date_to=str_replace("'","",$txt_date_to);
		$forwarder_name=str_replace("'","",$forwarder_name);
		$txt_invoice_no=str_replace("'","",$txt_invoice_no);
		//$user_arr = return_library_array("select id,user_name from user_passwd ","id","user_name");
		//$bank_arr=return_library_array("select id,bank_name from   lib_bank","id","bank_name");
		$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		//$location_arr=return_library_array("select id,location_name from  lib_location","id","location_name");
		$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
		//$supplier_arr=return_library_array("select id,supplier_name from lib_supplier","id","supplier_name");
		//$country_arr=return_library_array("select id,country_name from  lib_country","id","country_name");

		$sub_sql="SELECT b.INVOICE_ID, a.SUBMIT_DATE, a.BANK_REF_NO, c.RECEIVED_DATE 
		from com_export_doc_submission_mst a, com_export_doc_submission_invo b
		left join com_export_proceed_realization c on b.doc_submission_mst_id=c.invoice_bill_id 
		where a.id=b.doc_submission_mst_id and a.company_id=$cbo_company_name and a.entry_form=40 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		//echo $sub_sql;die;
		$sub_sql_result=sql_select($sub_sql);
		$bank_sub_data=array();
		foreach($sub_sql_result as $row)
		{
			$bank_sub_data[$row["INVOICE_ID"]]["SUBMIT_DATE"]=$row["SUBMIT_DATE"];
			$bank_sub_data[$row["INVOICE_ID"]]["BANK_REF_NO"]=$row["BANK_REF_NO"];
			$bank_sub_data[$row["INVOICE_ID"]]["RECEIVED_DATE"]=$row["RECEIVED_DATE"];
		}
		//echo "<pre>"; print_r($bank_sub_data);die;
		unset($sub_sql_result);
		$order_sql="SELECT b.ID as PO_ID, b.PO_NUMBER, c.GMTS_ITEM_ID 
		from wo_po_break_down b, wo_po_details_master c 
		where b.job_no_mst=c.job_no and c.company_name=$cbo_company_name and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		$order_sql_result=sql_select($order_sql);
		$order_data_arr=array();
		foreach($order_sql_result as $row)
		{
			$order_data_arr[$row["PO_ID"]]["PO_NUMBER"]=$row["PO_NUMBER"];
			$gmt_item_arr=explode(",",$row["GMTS_ITEM_ID"]);
			foreach($gmt_item_arr as $gmt_id)
			{
				$order_data_arr[$row["PO_ID"]]["GMTS_ITEM"].=$garments_item[$gmt_id].",";
			}
		}
		unset($order_sql_result);
		
		
		if($cbo_company_name!=0) $cbo_company_name = $cbo_company_name; else $cbo_company_name="%%";
		if($cbo_buyer_name == 0) $cbo_buyer_name="%%"; else $cbo_buyer_name = $cbo_buyer_name;
		if($cbo_lien_bank == 0) $cbo_lien_bank="%%"; else $cbo_lien_bank = $cbo_lien_bank;
		if($cbo_location == 0) $cbo_location="%%"; else $cbo_location = $cbo_location;
		if($cbo_based_on == 0) $cbo_based_on="%%"; else $cbo_based_on = $cbo_based_on;
		if(trim($txt_date_from)!= "") $txt_date_from  =$txt_date_from;
		if(trim($txt_date_to)!= "") $txt_date_to = $txt_date_to;
		if($forwarder_name!=0) $forwarder_cond=" and a.forwarder_name='$forwarder_name'"; else $forwarder_cond="";
		if($txt_invoice_no!="") $invoice_cond=" and a.invoice_no like '%$txt_invoice_no'"; else $invoice_cond="";

		if($cbo_ascending_by!="" && $cbo_ascending_by==1 ) 
		{
			$ascending_by = "invoice_no";
			$ascendig_cond=" order by $ascending_by asc ";
		} elseif ($cbo_ascending_by!="" && $cbo_ascending_by==2) {
			$ascending_by = "invoice_date";
			$ascendig_cond=" order by $ascending_by asc ";
		} elseif ($cbo_ascending_by!="" && $cbo_ascending_by==3) {
			$ascending_by = "exp_form_no";
			$ascendig_cond=" order by $ascending_by asc ";
		}elseif ($cbo_ascending_by!="" && $cbo_ascending_by==4) {
			$ascending_by = "exp_form_date";
			$ascendig_cond=" order by $ascending_by asc ";
		}else{
			$ascendig_cond="";
		}
		//echo $ascendig_cond;die;
		//if(trim($data[7])!="") $cbo_year2=$data[7];
		if ($txt_date_from!="" && $txt_date_to!="")
		{
			if($cbo_based_on ==0)
			{
				$str_cond=" and a.invoice_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==1)
			{
				$str_cond=" and a.invoice_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==2)
			{
				$str_cond=" and a.ex_factory_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==3)
			{
				$str_cond=" and a.actual_shipment_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==4)
			{
				$str_cond=" and a.bl_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==5)
			{
				$str_cond=" and a.ship_bl_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==6)
			{
				$str_cond=" and e.received_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==7)
			{

				if($db_type==0) $str_cond=" and a.insert_date between '$txt_date_from 00:00:01' and  '$txt_date_to 23:59:59'";
				else if($db_type==2) $str_cond=" and a.insert_date between '$txt_date_from 12:00:01 AM' and  '$txt_date_to 11:59:59 PM'";
			}
			else if($cbo_based_on ==8)
			{
				$str_cond=" and a.invoice_date between '$txt_date_from' and  '$txt_date_to'";
			}
		}
		else
		{
			$str_cond="";
		}
		$shipping_mode=str_replace("'","",$shipping_mode);
		$ship_cond="";
		if($shipping_mode!=0) $ship_cond=" and a.shipping_mode=$shipping_mode";

		$txt_lc_sc_no=str_replace("'","",$txt_lc_sc_no);
		//echo  $str_cond;die;
		$sql="SELECT c.po_breakdown_id, a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date, a.invoice_value, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.remarks, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, a.freight_amnt_by_supllier, a.insentive_applicable, b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, b.tenor, 1 as type
		FROM com_export_invoice_ship_dtls c, com_export_invoice_ship_mst a, com_export_lc b
		WHERE a.lc_sc_id=b.id and c.mst_id=a.id and a.is_lc=1 and a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 AND b.export_lc_no LIKE '%$txt_lc_sc_no%'  $str_cond $forwarder_cond $ship_cond $invoice_cond $all_submitted_invoice_cond
		UNION ALL
		SELECT c.po_breakdown_id, a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date, a.invoice_value, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.remarks, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, a.freight_amnt_by_supllier, a.insentive_applicable, b.pay_term as pay_term, b.lien_bank as lien_bank, b.contract_no as lc_sc_no, b.tenor, 2 as type
		FROM com_export_invoice_ship_dtls c, com_export_invoice_ship_mst a, com_sales_contract b
		WHERE a.lc_sc_id=b.id and c.mst_id=a.id and a.is_lc=2 and a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 AND b.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond $ascendig_cond $all_submitted_invoice_cond 
		order by buyer_id, invoice_no";
		//echo $sql;die;
		$sql_re=sql_select($sql);
		$inv_data=array();
		foreach($sql_re as $row)
		{
			if($orde_inv_check[$row[csf('id')]][$row[csf('po_breakdown_id')]]=="")
			{
				$orde_inv_check[$row[csf('id')]][$row[csf('po_breakdown_id')]]=$row[csf('po_breakdown_id')];
				$inv_data[$row[csf('id')]]['po_breakdown_id'] .= $row[csf('po_breakdown_id')].",";
				$inv_data[$row[csf('id')]]['po_number'] .= $order_data_arr[$row[csf('po_breakdown_id')]]["PO_NUMBER"].",";
				$inv_data[$row[csf('id')]]['gmts_item'] .= chop($order_data_arr[$row[csf('po_breakdown_id')]]["GMTS_ITEM"],",").",";
			}
			$inv_data[$row[csf('id')]]['id'] = $row[csf('id')];
			$inv_data[$row[csf('id')]]['benificiary_id'] = $row[csf('benificiary_id')];
			$inv_data[$row[csf('id')]]['location_id'] = $row[csf('location_id')];
			$inv_data[$row[csf('id')]]['invoice_no'] = $row[csf('invoice_no')];
			$inv_data[$row[csf('id')]]['invoice_date'] = $row[csf('invoice_date')];
			$inv_data[$row[csf('id')]]['ex_factory_date'] = $row[csf('ex_factory_date')];
			$inv_data[$row[csf('id')]]['is_lc'] = $row[csf('is_lc')];
			$inv_data[$row[csf('id')]]['buyer_id'] = $row[csf('buyer_id')];
			$inv_data[$row[csf('id')]]['exp_form_no'] = $row[csf('exp_form_no')];
			$inv_data[$row[csf('id')]]['exp_form_date'] = $row[csf('exp_form_date')];
			$inv_data[$row[csf('id')]]['invoice_value'] = $row[csf('invoice_value')];
			$inv_data[$row[csf('id')]]['net_invo_value'] = $row[csf('net_invo_value')];
			$inv_data[$row[csf('id')]]['invoice_quantity'] = $row[csf('invoice_quantity')];
			$inv_data[$row[csf('id')]]['total_carton_qnty'] = $row[csf('total_carton_qnty')];
			$inv_data[$row[csf('id')]]['actual_shipment_date'] = $row[csf('actual_shipment_date')];
			$inv_data[$row[csf('id')]]['shipping_bill_n'] = $row[csf('shipping_bill_n')];
			$inv_data[$row[csf('id')]]['ship_bl_date'] = $row[csf('ship_bl_date')];
			$inv_data[$row[csf('id')]]['bl_no'] = $row[csf('bl_no')];
			$inv_data[$row[csf('id')]]['bl_date'] = $row[csf('bl_date')];
			$inv_data[$row[csf('id')]]['remarks'] = $row[csf('remarks')];
			$inv_data[$row[csf('id')]]['shipping_mode'] = $row[csf('shipping_mode')];
			$inv_data[$row[csf('id')]]['carton_net_weight'] = $row[csf('carton_net_weight')];
			$inv_data[$row[csf('id')]]['carton_gross_weight'] = $row[csf('carton_gross_weight')];
			$inv_data[$row[csf('id')]]['freight_amnt_by_supllier'] = $row[csf('freight_amnt_by_supllier')];
			$inv_data[$row[csf('id')]]['insentive_applicable'] = $row[csf('insentive_applicable')];			
			$inv_data[$row[csf('id')]]['pay_term'] = $row[csf('pay_term')];
			$inv_data[$row[csf('id')]]['lien_bank'] = $row[csf('lien_bank')];
			$inv_data[$row[csf('id')]]['lc_sc_no'] = $row[csf('lc_sc_no')];
			$inv_data[$row[csf('id')]]['tenor'] = $row[csf('tenor')];
			$inv_data[$row[csf('id')]]['type'] = $row[csf('type')];
			
			$inv_data[$row[csf('id')]]['submit_date'] = $bank_sub_data[$row[csf('id')]]["SUBMIT_DATE"];
			$inv_data[$row[csf('id')]]['bank_ref_no'] = $bank_sub_data[$row[csf('id')]]["BANK_REF_NO"];
			$inv_data[$row[csf('id')]]['received_date'] = $bank_sub_data[$row[csf('id')]]["RECEIVED_DATE"];
			if($inv_check[$row[csf('id')]]=="")
			{
				$inv_check[$row[csf('id')]]=$row[csf('id')];
				$buyer_wise_data[$row[csf('buyer_id')]]['invoice_quantity']+=$row[csf('invoice_quantity')];
				$buyer_wise_data[$row[csf('buyer_id')]]['invoice_value']+=$row[csf('invoice_value')];
				$buyer_wise_data[$row[csf('buyer_id')]]['net_invo_value']+=$row[csf('net_invo_value')];
				$buyer_key_data[$row[csf('buyer_id')]]+=$row[csf('net_invo_value')];
			}
		}
		unset($sql_re);
		sort($buyer_key_data);
	   	//echo "<pre>"; print_r($buyer_wise_data);die;
		
		ob_start();
		?>
		<div style="width:2000px"> 
			<table width="2000" cellpadding="0" cellspacing="0" id="caption">
				<tr>
					<td align="center" width="100%" colspan="20" class="form_caption" >
						<strong style="font-size:18px">Company Name:<? echo " ". $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong>
					</td>
				</tr>
				<tr>
					<td align="center" width="100%" colspan="20" class="form_caption" >
						<strong style="font-size:18px"><? echo $report_title; ?></strong>
					</td>
				</tr>
			</table>
            <br />                       
            <table width="650" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_0" align="left">
                <thead>
                    <tr>
                        <th width="50">Sl</th>
                        <th width="150">Buyer</th>
                        <th width="150">Invoice Qty.</th>
                        <th width="150">Invoice Value (Gross)</th>
                        <th>Invoice Value (Net)</th>
                    </tr>
                </thead>
                <tbody>
                <?
				$i=1;
				foreach($buyer_wise_data as $buy_id=>$buy_val)
				{
					if ($i%2==0)
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                    	<td align="center" title="<?= $buy_id;?>"><? echo $i;?></td>
                        <td title="<?=$buy_id;?>"><? echo $buyer_arr[$buy_id];?></td>
                        <td align="right"><? echo number_format($buy_val["invoice_quantity"],2);?></td>
                        <td align="right"><? echo number_format($buy_val["invoice_value"],2);?></td>
                        <td align="right"><? echo number_format($buy_val["net_invo_value"],2);?></td>
                    </tr>
                    <?
					$i++;
					$buy_tot_invoice_quantity +=$buy_val["invoice_quantity"];
					$buy_tot_invoice_value +=$buy_val["invoice_value"];
					$buy_tot_net_invo_value +=$buy_val["net_invo_value"];
				}
				?>
                </tbody>
                <tfoot>
                	<tr>
                    	<th colspan="2" align="right">Total:</th>
                        <th align="right"><? echo number_format($buy_tot_invoice_quantity,2);?></th>
                        <th align="right"><? echo number_format($buy_tot_invoice_value,2);?></th>
                        <th align="right"><? echo number_format($buy_tot_net_invo_value,2);?></th>
                    </tr>
                </tfoot>
            </table>
			<br />                       
            <table width="2000" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1" align="left">
                <thead>
                    <tr>
                        <th width="40">Sl</th>
                        <th width="100">Invoice No.</th>
                        <th width="70">Invoice Date</th>
                        <th width="150">Order No</th>
                        <th width="100">LC/CON. NO.</th>
                        <th width="100">Buyer Name</th>
                        <th width="100">CTN</th>
                        <th width="100">Invoice Qnty.</th>
                        <th width="100">Invoice value</th>
                        <th width="100">Realization Amount</th>
                        <th width="70">Realized Date</th>
                        <th width="80">Ship Mode</th>
                        <th width="100">FDBC</th>
                        <th width="100">EXP. NO.</th>
                        <th width="120">Item</th>
                        <th width="100">B/L No</th>
                        <th width="70">B/L Date</th>
                        <th width="80">Discount</th>
                        <th width="80">Air Freight</th>
                        <th width="80">LOC/IMP</th>
                        <th>Rmarks</th>
                    </tr>
                </thead>
            </table>
            <div style="width:2000px; overflow-y:scroll; max-height:290px;font-size:12px; overflow-x:hidden;" id="scroll_body" align="left">
	            <table width="1980" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
	                <tbody>
	                <?
					$k=1;
					$insentive_applicable_arr=array(1=>"Local",2=>"Foreign");
	                foreach($inv_data as $inv_id=>$inv_data)
	                {
						if ($k%2==0)
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";
						$inv_discount=$inv_data["invoice_value"]-$inv_data["net_invo_value"];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
	                    	<td width="40"><? echo $k;?></td>
                            <td width="100" style="word-break:break-all;" title="<? echo $inv_id; ?>"><p><? echo $inv_data["invoice_no"]; ?>&nbsp;</p></td>
                            <td width="70" style="word-break:break-all;"><p><? echo change_date_format($inv_data["invoice_date"]); ?>&nbsp;</p></td>
                            <td width="150" style="word-break:break-all;"><p><? echo chop($inv_data["po_number"],","); ?>&nbsp;</p></td>
                            <td width="100" style="word-break:break-all;"><p><? echo $inv_data["lc_sc_no"]; ?>&nbsp;</p></td>
                            <td width="100" style="word-break:break-all;"><p><? echo $buyer_arr[$inv_data["buyer_id"]]; ?>&nbsp;</p></td>
                            <td width="100" align="right"><? echo number_format($inv_data["total_carton_qnty"],2);?></td>
                            <td width="100" align="right"><? echo number_format($inv_data["invoice_quantity"],2);?></td>
                            <td width="100" align="right" title="<?= $inv_data["invoice_value"]; ?>"><? echo number_format($inv_data["net_invo_value"],2);?></td>
                            <td width="100" align="right"><? if($inv_data["received_date"]!="" && $inv_data["received_date"]!="0000-00-00") echo number_format($inv_data["net_invo_value"],2); else echo "0.00";?></td>
                            <td width="70" align="center"><? if($inv_data["received_date"]!="" && $inv_data["received_date"]!="0000-00-00") echo change_date_format($inv_data["received_date"]); ?></td>
                            <td width="80" style="word-break:break-all;"><p><? echo $shipment_mode[$inv_data["shipping_mode"]]; ?>&nbsp;</p></td>
                            <td width="100" style="word-break:break-all;"><p><? echo $inv_data["bank_ref_no"]; ?>&nbsp;</p></td>
                            <td width="100" style="word-break:break-all;"><p><? echo $inv_data["exp_form_no"]; ?>&nbsp;</p></td>
                            <td width="120" style="word-break:break-all;"><p><? echo implode(",",array_unique(explode(",",chop($inv_data["gmts_item"],",")))); ?>&nbsp;</p></td>
                            <td width="100" style="word-break:break-all;"><p><? echo $inv_data["bl_no"]; ?>&nbsp;</p></td>
                            <td width="70" align="center"><? if($inv_data["bl_date"]!="" && $inv_data["bl_date"]!="0000-00-00") echo change_date_format($inv_data["bl_date"]); ?></td>
                            <td width="80" align="right"><? echo number_format($inv_discount,2);?></td>
                            <td width="80" align="right"><? echo number_format($inv_data["freight_amnt_by_supllier"],2);?></td>
                            <td width="80" style="word-break:break-all;"><p><? echo $insentive_applicable_arr[$inv_data["insentive_applicable"]]; ?>&nbsp;</p></td>
                            <td style="word-break:break-all;"><p><? echo $inv_data["remarks"]; ?>&nbsp;</p></td>
						</tr>
						<?
						$k++;
						$total_total_carton_qnty+=$inv_data["total_carton_qnty"];
						$total_invoice_quantity+=$inv_data["invoice_quantity"];
						$total_invoice_value+=$inv_data["net_invo_value"];
						if($inv_data["received_date"]!="" && $inv_data["received_date"]!="0000-00-00")
						{
							$total_rlz_value+=$inv_data["net_invo_value"];
						}
						$total_inv_discount+=$inv_discount;
						$total_freight_amnt_by_supllier+=$inv_data["freight_amnt_by_supllier"];
	                }
	                ?>
	                </tbody>
	            </table>
                </div>
	     		<table width="2000" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="report_table_footer" align="left">
	            	<tfoot>
	                    <tr>
	                        <th width="40">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="150">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="100" align="right">Total</th>
                            <th width="100" align="right" id="value_total_total_carton_qnty"><? echo number_format($total_total_carton_qnty,2);?></th>
                            <th width="100" align="right" id="value_total_invoice_quantity"><? echo number_format($total_invoice_quantity,2);?></th>
                            <th width="100" align="right" id="value_total_invoice_value"><? echo number_format($total_invoice_value,2);?></th>
                            <th width="100" align="right" id="value_total_rlz_value"><? echo number_format($total_rlz_value,2);?></th>
                            <th width="70">&nbsp;</th>
                            <th width="80">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="120">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="80" align="right" id="value_total_inv_discount"><? echo number_format($total_inv_discount,2);?></th>
                            <th width="80" align="right" id="value_total_inv_discount"><? echo number_format($total_freight_amnt_by_supllier,2);?></th>
                            <th width="80">&nbsp;</th>
                            <th>&nbsp;</th>
	                    </tr>
	                </tfoot>
	            </table>
    	</div>
    	<div align="left" style="font-weight:bold; margin-left:30px;"><? echo "User Id : ". $user_arr[$user_id] ." , &nbsp; THIS IS SYSTEM GENERATED STATEMENT, NO SIGNATURE REQUIRED ."; ?></div>
		<?
		foreach (glob("$user_id*.xls") as $filename)
		{

			if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename=$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc,ob_get_contents());
		//$filename=$user_id."_".$name.".xls";
		echo "$total_data****".$RptType."****".$filename;
		//echo "****".$RptType;
	}
	if ($RptType == 7) // SHORT 2
	{
		$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$buyer_arr=return_library_array("select id,buyer_name from lib_buyer","id","buyer_name");
		$country_arr=return_library_array("select id,country_name from lib_country","id","country_name");
		$cbo_company_name=str_replace("'","",$cbo_company_name);
		$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
		$txt_date_from=str_replace("'","",$txt_date_from);
		$txt_date_to=str_replace("'","",$txt_date_to);
		$shipping_mode=str_replace("'","",$shipping_mode);
		$txt_invoice_no=str_replace("'","",$txt_invoice_no);
		$txt_int_ref_no=str_replace("'","",$txt_int_ref_no);
		$ship_cond="";
		if($cbo_company_name!=0) $cbo_company_name = $cbo_company_name; else $cbo_company_name="%%";
		if($cbo_buyer_name != 0) $cbo_buyer_name = $cbo_buyer_name;  else $cbo_buyer_name="%%";
		if($shipping_mode!=0) $ship_cond=" and b.shipping_mode=$shipping_mode";
		if($txt_invoice_no!="") $ship_cond.=" and b.invoice_no like '%$txt_invoice_no%'";
		if($txt_int_ref_no!="") $ref_inv_cond=" and a.grouping ='$txt_int_ref_no'";

		if($cbo_ascending_by!="" && $cbo_ascending_by==1 ) 
		{
			$ascending_by = "invoice_no";
			$ascendig_cond=" order by $ascending_by asc ";
		} elseif ($cbo_ascending_by!="" && $cbo_ascending_by==2) {
			$ascending_by = "invoice_date";
			$ascendig_cond=" order by $ascending_by asc ";
		} elseif ($cbo_ascending_by!="" && $cbo_ascending_by==3) {
			$ascending_by = "exp_form_no";
			$ascendig_cond=" order by $ascending_by asc ";
		}elseif ($cbo_ascending_by!="" && $cbo_ascending_by==4) {
			$ascending_by = "exp_form_date";
			$ascendig_cond=" order by $ascending_by asc ";
		}else{
			$ascendig_cond="";
		}

		$date_cond="";

		if ($txt_date_from!="" && $txt_date_to!="")
		{
			if($cbo_based_on ==0)
			{
				$str_cond=" and b.invoice_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==1)
			{
				$str_cond=" and b.invoice_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==2)
			{
				$str_cond=" and b.ex_factory_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==3)
			{
				$str_cond=" and b.actual_shipment_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==4)
			{
				$str_cond=" and b.bl_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==5)
			{
				$str_cond=" and b.ship_bl_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==6)
			{
				$str_cond=" and b.invoice_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==7)
			{

				if($db_type==0) $str_cond=" and b.insert_date between '$txt_date_from 00:00:01' and  '$txt_date_to 23:59:59'";
				else if($db_type==2) $str_cond=" and b.insert_date between '$txt_date_from 12:00:01 AM' and  '$txt_date_to 11:59:59 PM'";
			}
			else if($cbo_based_on ==8)
			{
				$str_cond=" and b.invoice_date between '$txt_date_from' and  '$txt_date_to'";
			}
		}
		else
		{
			$str_cond="";
		}

		// , a.po_quantity

		/*$sql="select a.id, a.job_id, a.po_number, a.grouping, b.id as mst_id, d.country_id, b.invoice_no, b.invoice_date, b.buyer_id, b.benificiary_id, b.commission, b.shipping_mode, c.id as dtls_id, c.po_breakdown_id, c.current_invoice_rate, c.current_invoice_qnty, c.current_invoice_value,(sum(CASE WHEN d.entry_form!=85 THEN d.ex_factory_qnty ELSE 0 END)-sum(CASE WHEN d.entry_form=85 THEN d.ex_factory_qnty ELSE 0 END)) as ex_factory_qnty
		from wo_po_break_down a, com_export_invoice_ship_mst b, com_export_invoice_ship_dtls c, pro_ex_factory_mst d
		where a.id=c.po_breakdown_id and c.mst_id=b.id and b.id=d.invoice_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and b.benificiary_id=$cbo_company_name and b.buyer_id like '$cbo_buyer_name' $str_cond $ship_cond $ascendig_cond $ref_inv_cond group by a.id, a.job_id, a.po_number, a.grouping, b.id, d.country_id, b.invoice_no, b.invoice_date, b.buyer_id, b.benificiary_id, b.commission, b.shipping_mode, c.id , c.po_breakdown_id, c.current_invoice_rate, c.current_invoice_qnty, c.current_invoice_value order by 
		a.job_id,a.id,d.country_id,c.id";*/
		$sql="SELECT a.id, a.job_id, a.po_number, a.grouping, a.po_quantity, a.unit_price, b.id as mst_id, d.country_id, b.invoice_no, b.invoice_date, b.buyer_id, b.benificiary_id, b.commission, b.shipping_mode, c.id as dtls_id, c.po_breakdown_id, c.current_invoice_rate, c.current_invoice_qnty, c.current_invoice_value,(sum(CASE WHEN d.entry_form!=85 THEN d.ex_factory_qnty ELSE 0 END)-sum(CASE WHEN d.entry_form=85 THEN d.ex_factory_qnty ELSE 0 END)) as ex_factory_qnty, e.order_uom, e.total_set_qnty
		from wo_po_break_down a, com_export_invoice_ship_mst b, com_export_invoice_ship_dtls c, pro_ex_factory_mst d, wo_po_details_master e
		where a.id=c.po_breakdown_id and c.mst_id=b.id and b.id=d.invoice_no and a.id=d.po_break_down_id and a.job_id=e.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and b.benificiary_id=$cbo_company_name and b.buyer_id like '$cbo_buyer_name' $str_cond $ship_cond $ascendig_cond $ref_inv_cond 
		group by a.id, a.job_id, a.po_number, a.grouping, a.po_quantity, a.unit_price, b.id, d.country_id, b.invoice_no, b.invoice_date, b.buyer_id, b.benificiary_id, b.commission, b.shipping_mode, c.id , c.po_breakdown_id, c.current_invoice_rate, c.current_invoice_qnty, c.current_invoice_value, e.order_uom, e.total_set_qnty order by 
		a.job_id,a.id,d.country_id,c.id";
		// echo $sql;

        $sql_result=sql_select($sql);
		$data_result=array();
		foreach($sql_result as $row)
		{
			$data_result[$row[csf("job_id")]][$row[csf("dtls_id")]]['id']=$row[csf("id")];
			$data_result[$row[csf("job_id")]][$row[csf("dtls_id")]]['job_id']=$row[csf("job_id")];
			$data_result[$row[csf("job_id")]][$row[csf("dtls_id")]]['po_number']=$row[csf("po_number")];
			$data_result[$row[csf("job_id")]][$row[csf("dtls_id")]]['po_quantity']=$row[csf("po_quantity")];
			$data_result[$row[csf("job_id")]][$row[csf("dtls_id")]]['unit_price']=$row[csf("unit_price")];
			$data_result[$row[csf("job_id")]][$row[csf("dtls_id")]]['internal_ref']=$row[csf("grouping")];
			$data_result[$row[csf("job_id")]][$row[csf("dtls_id")]]['country_id']=$row[csf("country_id")];
			$data_result[$row[csf("job_id")]][$row[csf("dtls_id")]]['invoice_no']=$row[csf("invoice_no")];
			$data_result[$row[csf("job_id")]][$row[csf("dtls_id")]]['invoice_date']=$row[csf("invoice_date")];
			$data_result[$row[csf("job_id")]][$row[csf("dtls_id")]]['buyer_id']=$row[csf("buyer_id")];
			$data_result[$row[csf("job_id")]][$row[csf("dtls_id")]]['benificiary_id']=$row[csf("benificiary_id")];
			$data_result[$row[csf("job_id")]][$row[csf("dtls_id")]]['commission']=$row[csf("commission")];
			$data_result[$row[csf("job_id")]][$row[csf("dtls_id")]]['shipping_mode']=$row[csf("shipping_mode")];
			$data_result[$row[csf("job_id")]][$row[csf("dtls_id")]]['dtls_id']=$row[csf("dtls_id")];
			$data_result[$row[csf("job_id")]][$row[csf("dtls_id")]]['current_invoice_rate']=$row[csf("current_invoice_rate")];
			$data_result[$row[csf("job_id")]][$row[csf("dtls_id")]]['current_invoice_qnty']=$row[csf("current_invoice_qnty")];
			$data_result[$row[csf("job_id")]][$row[csf("dtls_id")]]['current_invoice_value']=$row[csf("current_invoice_value")];
			$data_result[$row[csf("job_id")]][$row[csf("dtls_id")]]['mst_id']=$row[csf("mst_id")];
			$data_result[$row[csf("job_id")]][$row[csf("dtls_id")]]['ex_factory_qnty']=$row[csf("ex_factory_qnty")];
			$data_result[$row[csf("job_id")]][$row[csf("dtls_id")]]['order_uom']=$row[csf("order_uom")];
			$data_result[$row[csf("job_id")]][$row[csf("dtls_id")]]['total_set_qnty']=$row[csf("total_set_qnty")];
		}
		$rowspan_arr=array();$rowspan_order_arr=array();$invoice_qnty_arr=array();$wo_po_break_down_id='';
		foreach($data_result as $inv_id=>$row_val)
		{
			foreach($row_val as $row_result)
			{
				$rowspan_arr[$row_result['id']][$row_result['country_id']]++;
				$rowspan_order_arr[$row_result['id']]++;
				// $invoice_qnty_arr[$row_result['id']][$row_result['country_id']]['total_invoice']+=$row_result['current_invoice_qnty'];
				$invoice_qnty_arr[$row_result['id']][$row_result['country_id']]['total_invoice_pcs']+=$row_result['current_invoice_qnty']*$row_result['total_set_qnty'];
				$wo_po_break_down_id.=$row_result['id'].',';
			}
		}

		$m=1;
		$wo_po_break_down_id_arr=array_chunk(array_unique(explode(",",chop($wo_po_break_down_id,','))),999);
		$country_qnty_arr=array();
		if(count($wo_po_break_down_id_arr)>0)
		{
			foreach($wo_po_break_down_id_arr as $woPoBreakDownID)
			{
				if($m==1) $woPoBreakDownID_cond .=" and (po_break_down_id in(".implode(",",$woPoBreakDownID).")"; else $woPoBreakDownID_cond .=" or po_break_down_id in(".implode(",",$woPoBreakDownID).")";
				$m++;
			}
			$woPoBreakDownID_cond .=" )";
			$woPoBreakDownID_sql="SELECT po_break_down_id as PO_BREAK_DOWN_ID,country_id as COUNTRY_ID, sum(order_quantity) as ORDER_QUANTITY,avg(order_rate) as ORDER_RATE from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 $woPoBreakDownID_cond group by po_break_down_id,country_id";

			$woPoBreakDownID_result=sql_select($woPoBreakDownID_sql);
			foreach($woPoBreakDownID_result as $val)
			{
				$country_qnty_arr[$val['PO_BREAK_DOWN_ID']][$val['COUNTRY_ID']]['order_quantity']=$val['ORDER_QUANTITY'];
				$country_qnty_arr[$val['PO_BREAK_DOWN_ID']][$val['COUNTRY_ID']]['order_rate']=$val['ORDER_RATE'];
			}
		}

		ob_start();
		?>
        <div style="width:2250px">
            <table width="2230" cellpadding="0" cellspacing="0" id="caption">
                <tr>
                	<td align="center" width="100%" colspan="13" class="form_caption" ><strong style="font-size:18px">Company Name:<? echo " ". $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
                </tr>
                <tr>
                	<td align="center" width="100%" colspan="13" class="form_caption" ><strong style="font-size:18px"><? echo $report_title; ?></strong></td>
                </tr>
            </table>
            <table width="2230" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_short" align="left">
                <thead>
                    <tr>
                        <th width="40">Sl</th>
                        <th width="100">Order No</th>
                        <th width="100">Internal Ref.</th>
                        <th width="100">Country</th>
                        <th width="100">Order UOM</th>
                        <th width="100">Order Qty</th>
                        <th width="100">Country Qty<br>[PCS]</th>
                        <th width="100">Invoice No</th>
                        <th width="100">Invoice Date</th>
                        <th width="100">Ex-Factory Qty<br>[PCS]</th>
                        <th width="100">Invoice Qty</th>
                        <th width="100">Invoice Qty<br>[PCS]</th>
                        <th width="100">Short Qnty<br>[PCS]</th>
                        <th width="100">Extra Qnty<br>[PCS]</th>
                        <th width="100">Unit Price<br>(Invoice Rate)</th>
                        <th width="100">Unit Price Avg<br>( Order Entry)</th>
                        <th width="100">Unit Price<br>(Country)</th>
                        <th width="100">Invoice Value</th>
                        <th width="100">Short Qnty Value</th>
                        <th width="100">Extra Qnty Value</th>
                        <th width="100" >Total Commission</th>
                        <th width="70" >Ship Mode</th>
                        <th >Buyer Name</th>
                    </tr>
                </thead>
            </table>
            <div style="width:2250px; overflow-y:scroll; max-height:310px;font-size:12px; overflow-x:hidden;" id="scroll_body">
            <table width="2230" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
                <tbody>
	                <?
					$array_check_arr=$array_check_arr1=$array_check_arr2=$array_check_arr3=$array_check_arr4=array();
					$k=1;$i=1;$j=1;
	                foreach($data_result as $inv_id=>$row_val)
	                {
						$row_count=count($row_val);
						if($j!=1)
						{
							?>
							<tr bgcolor="#CCCCCC">
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>Sub Total:</td>
								<td>&nbsp;</td>
								<td align="right"><? echo number_format($sub_order_qty,0); ?></td>
								<td align="right"><? echo number_format($sub_country_qty,0); ?></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right"><? echo number_format($sub_ex_factory_qty,0); ?></td>
								<td align="right"><? echo number_format($sub_invoice_qty,0); ?></td>
								<td align="right"><? echo number_format($sub_invoice_qty_pcs,0); ?></td>
								<td align="right"><? echo number_format($sub_short_qty,0); ?></td>
								<td align="right"><? echo number_format($sub_extra_qty,0); ?></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right"><? echo number_format($sub_invoice_value,2); ?></td>
								<td align="right"><? echo number_format($sub_short_value,2); ?></td>
								<td align="right"><? echo number_format($sub_extra_value,2); ?></td>
								<td>&nbsp;</td>
	                            <td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							<?
							$sub_order_qty=$sub_country_qty=$sub_ex_factory_qty=$sub_invoice_qty=$sub_invoice_qty_pcs=$sub_short_qty=$sub_extra_qty=$sub_invoice_value=$sub_short_value=$sub_extra_value=0;

						}
						$j++;
						foreach($row_val as $row_result)
						{
							if ($k%2==0)
							$bgcolor="#E9F3FF";
							else
							$bgcolor="#FFFFFF";
							$country_qnty=0;
							$country_qnty=$country_qnty_arr[$row_result['id']][$row_result['country_id']]['order_quantity'];
							
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
							<?php
								if(!in_array($row_result['job_id'],$array_check_arr3))
								{
									$array_check_arr3[]=$row_result['job_id'];
							?>
							<td  width="40" align="center" valign="top" rowspan="<?= $row_count;?>" ><? echo $i;?></td>
							<?
								$i++;
								}
							?>
							<?php
								if(!in_array($row_result['id'].'*'.$row_result['country_id'],$array_check_arr))
								{
									$array_check_arr[]=$row_result['id'].'*'.$row_result['country_id'];
									$rowspan_count= $rowspan_arr[$row_result['id']][$row_result['country_id']];
							?>
								<td width="100" rowspan="<?= $rowspan_count;?>" ><? echo $row_result['po_number'];?></td>
								<td width="100" rowspan="<?= $rowspan_count;?>" ><? echo $row_result['internal_ref'];?></td>
								<td width="100" rowspan="<?= $rowspan_count;?>" ><? echo $country_arr[$row_result['country_id']];?></td>
								<td width="100" align="center" rowspan="<?= $rowspan_count;?>" ><? echo $unit_of_measurement[$row_result['order_uom']];?></td>
							<?php
								if(!in_array($row_result['id'],$array_check_arr4))
								{
									$array_check_arr4[]=$row_result['id'];
									$rowspan_order_count= $rowspan_order_arr[$row_result['id']];
							?>
								<td width="100" align="right" rowspan="<?= $rowspan_order_count;?>" ><? echo $row_result['po_quantity'];$sub_order_qty+=$row_result['po_quantity']; $grand_order_qty+=$row_result['po_quantity'];?></td>
							<?
								}
							?>
								<td width="100" align="right" rowspan="<?= $rowspan_count;?>" ><? 
									echo $country_qnty;
									$sub_country_qty+=$country_qnty; $grand_country_qty+=$country_qnty;
								?></td>
							<?
								}
							?>
							<td width="100"><? echo $row_result['invoice_no'];?></td>
							<td width="100" align="center"><? echo change_date_format($row_result['invoice_date']);?></td>
							<td width="100" align="right"><? echo $row_result['ex_factory_qnty'];
								$sub_ex_factory_qty+=$row_result['ex_factory_qnty']; $grand_ex_factory_qty+=$row_result['ex_factory_qnty'];
							?></td>
							<td width="100" align="right"><? echo $row_result['current_invoice_qnty'];
								$sub_invoice_qty+=$row_result['current_invoice_qnty']; $grand_invoice_qty+=$row_result['current_invoice_qnty'];
							?></td>
							<td width="100" align="right"><? echo $row_result['current_invoice_qnty']*$row_result['total_set_qnty'];
								$sub_invoice_qty_pcs+=$row_result['current_invoice_qnty']*$row_result['total_set_qnty']; $grand_invoice_qty_pcs+=$row_result['current_invoice_qnty']*$row_result['total_set_qnty'];
							?></td>
							<?php
							$short_qnty=$extra_qnty=$short_value=$extra_value=0;
								if(!in_array($row_result['id'].'*'.$row_result['country_id'],$array_check_arr1))
								{
									$array_check_arr1[]=$row_result['id'].'*'.$row_result['country_id'];
									$rowspan_count1= $rowspan_arr[$row_result['id']][$row_result['country_id']];
									// $invoice_qnty= $invoice_qnty_arr[$row_result['id']][$row_result['country_id']]['total_invoice'];
									$total_sub_invoice_qty_pcs= $invoice_qnty_arr[$row_result['id']][$row_result['country_id']]['total_invoice_pcs'];
									// $total_sub_invoice_qty_pcs=$row_result['current_invoice_qnty']*$row_result['total_set_qnty'];
									if($total_sub_invoice_qty_pcs<$country_qnty)
									{
										$short_qnty=$country_qnty-$total_sub_invoice_qty_pcs;
										$short_value=$short_qnty*$row_result[('current_invoice_rate')];
									}
									if($total_sub_invoice_qty_pcs>$country_qnty)
									{
										$extra_qnty=$total_sub_invoice_qty_pcs-$country_qnty;
										$extra_value=$extra_qnty*$row_result['current_invoice_rate'];
									}
							?>
								<td width="100" align="right" rowspan="<?= $rowspan_count1;?>" ><? echo $short_qnty;
									$sub_short_qty+=$short_qnty; $grand_short_qty+=$short_qnty;
								?></td>
								<td width="100" align="right" rowspan="<?= $rowspan_count1;?>" ><? echo $extra_qnty;
									$sub_extra_qty+=$extra_qnty; $grand_extra_qty+=$extra_qnty;
								?></td>
							<?
								}
							?>
							<td width="100"  align="right"><? echo number_format($row_result['current_invoice_rate'],4);?></td>
							<td width="100"  align="right"><? echo number_format($row_result['unit_price'],4);?></td>
							<td width="100"  align="right"><? echo number_format($country_qnty_arr[$row_result['id']][$row_result['country_id']]['order_rate'],4);?></td>
							<td width="100" align="right"><? echo $row_result['current_invoice_value'];
								$sub_invoice_value+=$row_result['current_invoice_value']; $grand_invoice_value+=$row_result['current_invoice_value'];
							?></td>
							<?php
								if(!in_array($row_result['id'].'*'.$row_result['country_id'],$array_check_arr2))
								{
									$array_check_arr2[]=$row_result['id'].'*'.$row_result['country_id'];
									$rowspan_count2= $rowspan_arr[$row_result['id']][$row_result['country_id']];
									
							?>
								<td width="100" align="right" rowspan="<?= $rowspan_count2;?>" ><? echo $short_value;
									$sub_short_value+=$short_value; $grand_short_value+=$short_value;
								?></td>
								<td width="100" align="right" rowspan="<?= $rowspan_count2;?>" ><? echo $extra_value;
									$sub_extra_value+=$extra_value; $grand_extra_value+=$extra_value;
								?></td>
							<?
								}
							?>
							<td width="100" align="right"><? echo $row_result['commission'];?></td>
							<td width="70" align="center"><? echo $shipment_mode[$row_result['shipping_mode']];?></td>
							<td ><? echo $buyer_arr[$row_result['buyer_id']];?></td>
							</tr>
							<?
							$k++;
						}
	                }
	                //print_r($sc_value_1_3);
	                ?>
	                <tr bgcolor="#CCCCCC">
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>Sub Total:</td>
						<td>&nbsp;</td>
						<td align="right"><? echo number_format($sub_order_qty,0); ?></td>
						<td align="right"><? echo number_format($sub_country_qty,0); ?></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td align="right"><? echo number_format($sub_ex_factory_qty,0); ?></td>
						<td align="right"><? echo number_format($sub_invoice_qty,0); ?></td>
						<td align="right"><? echo number_format($sub_invoice_qty_pcs,0); ?></td>
						<td align="right"><? echo number_format($sub_short_qty,0); ?></td>
						<td align="right"><? echo number_format($sub_extra_qty,0); ?></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td align="right"><? echo number_format($sub_invoice_value,2); ?></td>
						<td align="right"><? echo number_format($sub_short_value,2); ?></td>
						<td align="right"><? echo number_format($sub_extra_value,2); ?></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
	                </tr>
                </tbody>
            </table>
     		</div>
            <table width="2230" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="report_table_footer_short" align="left">
            	<tfoot>
                <tr>
					<th width="40">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">Grand Total:</th>
					<td width="100">&nbsp;</td>
					<td width="100" align="right"><? echo number_format($grand_order_qty,0); ?></td>
					<th width="100" align="right"><? echo number_format($grand_country_qty,0); ?></th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100" align="right"><? echo number_format($grand_ex_factory_qty,0); ?></th>
					<th width="100" align="right"><? echo number_format($grand_invoice_qty,0); ?></th>
					<th width="100" align="right"><? echo number_format($grand_invoice_qty_pcs,0); ?></th>
					<th width="100" align="right"><? echo number_format($grand_short_qty,0); ?></th>
					<th width="100" align="right"><? echo number_format($grand_extra_qty,0); ?></th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100" align="right"><? echo number_format($grand_invoice_value,2); ?></th>
					<th width="100" align="right"><? echo number_format($grand_short_value,2); ?></th>
					<th width="100" align="right"><? echo number_format($grand_extra_value,2); ?></th>
					<th width="100">&nbsp;</th>
					<th width="70">&nbsp;</th>
					<th >&nbsp;</th>

                </tr>
                </tfoot>
            </table>
    	</div>
    	<?
    	foreach (glob("$user_id*.xls") as $filename)
		{

			if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename=$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc,ob_get_contents());
		//$filename=$user_id."_".$name.".xls";
		echo "$total_data****".$RptType."****".$filename;

		//echo "****".$RptType;
	}
    if($RptType==8) //search 4 (Wayasel)
	{
		// echo "ddddddddddddddddd";die;
		$user_arr = return_library_array("select id,user_name from user_passwd ","id","user_name");
		$bank_arr=return_library_array("select id,bank_name from   lib_bank","id","bank_name");
		$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$location_arr=return_library_array("select id,location_name from  lib_location","id","location_name");
		$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
		$supplier_arr=return_library_array("select id,supplier_name from lib_supplier","id","supplier_name");
		$country_arr=return_library_array("select id,country_name from  lib_country","id","country_name");

		$sub_sql=sql_select("SELECT b.invoice_id, a.courier_date, a.submit_date, a.bnk_to_bnk_cour_no, a.bank_ref_no, a.possible_reali_date 
			from com_export_doc_submission_mst a,com_export_doc_submission_invo b 
			where a.id=b.doc_submission_mst_id and a.entry_form=40 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		$bank_sub_data=array();
		foreach($sub_sql as $row)
		{
			$bank_sub_data[$row[csf("invoice_id")]]["courier_date"]=$row[csf("courier_date")];
			$bank_sub_data[$row[csf("invoice_id")]]["submit_date"]=$row[csf("submit_date")];
			$bank_sub_data[$row[csf("invoice_id")]]["bnk_to_bnk_cour_no"]=$row[csf("bnk_to_bnk_cour_no")];
			$bank_sub_data[$row[csf("invoice_id")]]["bank_ref_no"]=$row[csf("bank_ref_no")];
			$bank_sub_data[$row[csf("invoice_id")]]["possible_reali_date"]=$row[csf("possible_reali_date")];
		}

		$buyer_submit_date_arr=return_library_array(" SELECT b.invoice_id,a.submit_date from com_export_doc_submission_mst a,com_export_doc_submission_invo b where a.id=b.doc_submission_mst_id and a.entry_form=39","invoice_id","submit_date");

		$rlz_date_arr=return_library_array(" SELECT a.invoice_id,b.received_date,b.is_invoice_bill
		from com_export_doc_submission_invo a, com_export_proceed_realization b
		where a.doc_submission_mst_id=b.invoice_bill_id and b.is_invoice_bill  = 1
		union all
		select b.invoice_bill_id as invoice_id, b.received_date , b.is_invoice_bill
		from  com_export_proceed_realization b
		where  b.is_invoice_bill  = 2
		order by invoice_id","invoice_id","received_date");


		//$exfact_qnty_arr=return_library_array(" select invoice_no, sum(ex_factory_qnty) as ex_factory_qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 and invoice_no>0 group by invoice_no","invoice_no","ex_factory_qnty");
		$exfact_qnty_arr=return_library_array(" SELECT invoice_no, sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty
		from pro_ex_factory_mst where status_active=1 and is_deleted=0 and invoice_no>0 group by invoice_no","invoice_no","ex_factory_qnty");
		$variable_standard_arr=return_library_array(" SELECT monitor_head_id,monitoring_standard_day from variable_settings_commercial where status_active=1 and is_deleted=0 and company_name=$cbo_company_name and variable_list=19","monitor_head_id","monitoring_standard_day");

		// $sql_order_set=sql_select("SELECT a.mst_id, a.po_breakdown_id, a.current_invoice_qnty, (a.current_invoice_qnty*c.total_set_qnty) as invoice_qnty_pcs, c.total_set_qnty from com_export_invoice_ship_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_breakdown_id=b.id and b.job_id=c.id and a.status_active=1 and a.is_deleted=0 and c.company_name=$cbo_company_name  ");
		// $inv_qnty_pcs_arr=array();
		// foreach($sql_order_set as $row)
		// {
		// 	$inv_qnty_pcs_arr[$row[csf("mst_id")]]+=$row[csf("invoice_qnty_pcs")];
		// }
		ob_start();


		//echo " ". $company_arr[str_replace("'","",$cbo_company_name)];
		//echo $report_title;
		//$based_on_arr=array(1=>"Invoice Date",2=>"Exfactory Date",3=>"Actual Date"); echo " ". $based_on_arr[$cbo_based_on];
		?>
		
		<div style="width:3500px"> 
		<table width="3500" cellpadding="0" cellspacing="0" id="caption">
            <tr>
            <td align="center" width="100%" colspan="20" class="form_caption" ><strong style="font-size:18px">Company Name:<? echo " ". $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
            </tr>
            <tr>
            <td align="center" width="100%" colspan="20" class="form_caption" ><strong style="font-size:18px"><? echo $report_title; ?>
            <?
			if($cbo_based_on!=0)
			{
			?>
            (<strong style="font-size:18px">Based On:<? $based_on_arr=array(1=>"Invoice Date",2=>"Exfactory Date",3=>"Actual Date"); echo " ". $based_on_arr[$cbo_based_on]; ?></strong>)

            <?
			}
			?></strong></td>
            </tr>
            </table>
    		<br />           
            <table width="4290" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1" align="left">
                <thead>
                    <tr>
                        <th width="50">Sl</th>
                        <th width="100">Company Name</th>
                        <th width="100">Invoice No.</th>                        
                        <th width="70">SC/LC</th>
                        <th width="100">SC/LC No.</th>
                        <th width="70">Buyer Name</th>
                        <th width="90">EXP Form No</th>
                        <th width="70">EXP Form Date</th>
                        <th width="100">Ex-factory Qnty</th>
                        <th width="100">Invoice Qnty.</th>
                        <th width="80">Num. Of Ctn Qnty.</th>
                        <th width="100">Invoice value</th>

						<th width="100">Discount</th>
						<th width="100">Bonous</th>
						<th width="100">Claim</th>
						<th width="100">Commission</th>
						<th width="100">Other Deduction</th>
						<th width="100">Upcharge</th>
						<th width="100">Net Invoice Amount</th>

                        <th width="70">Invoice Date</th>
                        <th width="70">Insert Date</th>
                        <th width="80">Ship Mode</th>
						<th width="70" >Incoterm</th>
                        <th width="70">Ex-Factory Date</th>
    
                        <th width="100">Copy B/L No</th>
                        <th width="70">Copy B/L Date</th>
             
						
                        <th width="70">Actual Bank Sub Date</th>
                        <th width="100">Bank Bill No.</th>
                        <th width="70">Shipping Bill No</th>
                        <th width="70">Shipping Bill Date</th>
                        <th width="70">Gross Weight</th>
                        <th width="70">Net weight</th>
                        <th width="70">Realization Amount</th>
                       
                    </tr>
                </thead>
            </table>
            <div style="width:4300px; overflow-y:scroll; max-height:290px;font-size:12px; overflow-x:hidden;" id="scroll_body" align="left">
	            <table width="4290" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
	                <tbody>
	                <?
	                $cbo_company_name=str_replace("'","",$cbo_company_name);
	                if($cbo_company_name!=0) $cbo_company_name = $cbo_company_name; else $cbo_company_name="%%";

	                $cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	                if($cbo_buyer_name == 0) $cbo_buyer_name="%%"; else $cbo_buyer_name = $cbo_buyer_name;

	                $cbo_lien_bank=str_replace("'","",$cbo_lien_bank);
	                if($cbo_lien_bank == 0) $cbo_lien_bank="%%"; else $cbo_lien_bank = $cbo_lien_bank;
					//echo  $cbo_lien_bank;die;
	                $cbo_location=str_replace("'","",$cbo_location);
	                if($cbo_location == 0) $cbo_location="%%"; else $cbo_location = $cbo_location;

	                if($cbo_based_on == 0) $cbo_based_on="%%"; else $cbo_based_on = $cbo_based_on;

	                $txt_date_from=str_replace("'","",$txt_date_from);
	                if(trim($txt_date_from)!= "") $txt_date_from  =$txt_date_from;

	                $txt_date_to=str_replace("'","",$txt_date_to);
	                if(trim($txt_date_to)!= "") $txt_date_to = $txt_date_to;

					$forwarder_name=str_replace("'","",$forwarder_name);
					if($forwarder_name!=0) $forwarder_cond=" and a.forwarder_name='$forwarder_name'"; else $forwarder_cond="";
					$txt_invoice_no=str_replace("'","",$txt_invoice_no);
					if($txt_invoice_no!="") $invoice_cond=" and a.invoice_no like '%$txt_invoice_no'"; else $invoice_cond="";

					if($cbo_ascending_by!="" && $cbo_ascending_by==1 ) 
					{
						$ascending_by = "invoice_no";
						$ascendig_cond=" order by $ascending_by asc ";
					} elseif ($cbo_ascending_by!="" && $cbo_ascending_by==2) {
						$ascending_by = "invoice_date";
						$ascendig_cond=" order by $ascending_by asc ";
					} elseif ($cbo_ascending_by!="" && $cbo_ascending_by==3) {
						$ascending_by = "exp_form_no";
						$ascendig_cond=" order by $ascending_by asc ";
					}elseif ($cbo_ascending_by!="" && $cbo_ascending_by==4) {
						$ascending_by = "exp_form_date";
						$ascendig_cond=" order by $ascending_by asc ";
					}else{
						$ascendig_cond="";
					}
	                //if(trim($data[7])!="") $cbo_year2=$data[7];
	                if ($txt_date_from!="" && $txt_date_to!="")
	                {
						if($cbo_based_on ==0)
						{
							$str_cond=" and a.invoice_date between '$txt_date_from' and  '$txt_date_to'";
						}
						else if($cbo_based_on ==1)
						{
							$str_cond=" and a.invoice_date between '$txt_date_from' and  '$txt_date_to'";
						}
						else if($cbo_based_on ==2)
						{
							$str_cond=" and a.ex_factory_date between '$txt_date_from' and  '$txt_date_to'";
						}
						else if($cbo_based_on ==3)
						{
							$str_cond=" and a.actual_shipment_date between '$txt_date_from' and  '$txt_date_to'";
						}
						else if($cbo_based_on ==4)
						{
							$str_cond=" and a.bl_date between '$txt_date_from' and  '$txt_date_to'";
						}
						else if($cbo_based_on ==5)
						{
							$str_cond=" and a.ship_bl_date between '$txt_date_from' and  '$txt_date_to'";
						}
						else if($cbo_based_on ==6)
						{
							$str_cond=" and e.received_date between '$txt_date_from' and  '$txt_date_to'";
						}
						else if($cbo_based_on ==7)
						{

							if($db_type==0) $str_cond=" and a.insert_date between '$txt_date_from 00:00:01' and  '$txt_date_to 23:59:59'";
							else if($db_type==2) $str_cond=" and a.insert_date between '$txt_date_from 12:00:01 AM' and  '$txt_date_to 11:59:59 PM'";
						}
						else if($cbo_based_on ==8)
						{
							$str_cond=" and a.invoice_date between '$txt_date_from' and  '$txt_date_to'";
						}
	                }
	                else
	                {
	                	$str_cond="";
	                }
					$shipping_mode=str_replace("'","",$shipping_mode);
					$ship_cond="";
					if($shipping_mode!=0) $ship_cond=" and a.shipping_mode=$shipping_mode";

					$txt_lc_sc_no=str_replace("'","",$txt_lc_sc_no);
					//echo  $str_cond;die;
					if($cbo_based_on==6)
					{
						if($db_type==0)
						{
							$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor,b.inco_term
							FROM com_export_invoice_ship_mst a, com_export_lc b, com_export_doc_submission_invo d, com_export_proceed_realization e
							WHERE a.lc_sc_id=b.id and a.id=d.invoice_id and d.doc_submission_mst_id=e.invoice_bill_id  and a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' AND b.export_lc_no LIKE '%$txt_lc_sc_no%' and a.is_lc=1 and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  $str_cond $forwarder_cond $ship_cond $invoice_cond

							UNION ALL

							SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor,c.inco_term
							FROM com_export_invoice_ship_mst a, com_sales_contract c , com_export_doc_submission_invo d, com_export_proceed_realization e
							WHERE a.lc_sc_id=c.id and a.id=d.invoice_id and d.doc_submission_mst_id=e.invoice_bill_id and a.is_lc=2 and a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' AND c.contract_no LIKE '%$txt_lc_sc_no%' and  a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $str_cond $forwarder_cond $ship_cond $invoice_cond

							UNION ALL

							SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date, a. 	invoice_value, a.discount_ammount, a.bonus_ammount, a.claim_ammount, a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor,c.inco_term
							FROM com_export_invoice_ship_mst a, com_sales_contract c , com_export_proceed_realization e
							WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=2 and a.lc_sc_id=c.id and e.invoice_bill_id = a.id and e.is_invoice_bill = 2 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond $ascendig_cond";

						}
						else
						{
						$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor,b.inco_term
						FROM com_export_invoice_ship_mst a, com_export_lc b , com_export_doc_submission_invo d, com_export_proceed_realization e
						WHERE a.lc_sc_id=b.id and a.id=d.invoice_id and d.doc_submission_mst_id=e.invoice_bill_id  and a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' AND b.export_lc_no LIKE '%$txt_lc_sc_no%' and a.is_lc=1 and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  $str_cond $forwarder_cond $ship_cond $invoice_cond

						UNION ALL

						SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor,c.inco_term
						FROM com_export_invoice_ship_mst a, com_sales_contract c, com_export_doc_submission_invo d, com_export_proceed_realization e
						WHERE a.lc_sc_id=c.id and a.id=d.invoice_id and d.doc_submission_mst_id=e.invoice_bill_id  and a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' AND c.contract_no LIKE '%$txt_lc_sc_no%' and a.is_lc=2 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $str_cond $forwarder_cond $ship_cond $invoice_cond
						UNION ALL

						SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor,c.inco_term
						FROM com_export_invoice_ship_mst a, com_sales_contract c , com_export_proceed_realization e
						WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=2 and a.lc_sc_id=c.id and e.invoice_bill_id = a.id and e.is_invoice_bill = 2  and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond $ascendig_cond";
						}
					}
					else if($cbo_based_on==8)
					{
						if($db_type==0)
						{
							$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor,b.inco_term
							FROM com_export_invoice_ship_mst a, com_export_lc b
							WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=1 and a.lc_sc_id=b.id and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 AND b.export_lc_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond and a.id not in(select a.invoice_id from com_export_doc_submission_invo a, com_export_proceed_realization b where a.doc_submission_mst_id=b.invoice_bill_id and a.is_lc=1)

							UNION ALL

							SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor,c.inco_term
							FROM com_export_invoice_ship_mst a, com_sales_contract c
							WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=2 and a.lc_sc_id=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0  AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond and a.id not in(select a.invoice_id from com_export_doc_submission_invo a, com_export_proceed_realization b where a.doc_submission_mst_id=b.invoice_bill_id and a.is_lc=2) $ascendig_cond";

						}
						else
						{
							
							
							$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor,b.inco_term
							FROM com_export_invoice_ship_mst a, com_export_lc b
							WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=1 and a.lc_sc_id=b.id and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 AND b.export_lc_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond and a.id not in(select a.invoice_id from com_export_doc_submission_invo a, com_export_proceed_realization b where a.doc_submission_mst_id=b.invoice_bill_id and a.is_lc=1)
							UNION ALL
							SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor,c.inco_term
							FROM com_export_invoice_ship_mst a, com_sales_contract c
							WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=2 and a.lc_sc_id=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0  AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond and a.id not in(select a.invoice_id from com_export_doc_submission_invo a, com_export_proceed_realization b where a.doc_submission_mst_id=b.invoice_bill_id and a.is_lc=2) $ascendig_cond";
							
						}
					}
					else if($cbo_based_on==9) //bank sub date
					{
			
						$sub_date_cond=" and e.submit_date between '$txt_date_from' and  '$txt_date_to'";
			
						$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor,b.inco_term

						FROM com_export_invoice_ship_mst a, com_export_lc b,com_export_doc_submission_invo d,COM_EXPORT_DOC_SUBMISSION_MST e
						WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=1 and a.lc_sc_id=b.id and  a.id=d.invoice_id  and d.DOC_SUBMISSION_MST_ID = e.id and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and e.status_active=1 and e.is_deleted=0 AND b.export_lc_no  LIKE '%$txt_lc_sc_no%'  $str_cond $sub_date_cond $forwarder_cond $ship_cond $invoice_cond

						group by a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name , b.pay_term, b.lien_bank, b.export_lc_no, a.insert_date, b.doc_presentation_days, a.co_date,b.tenor,b.inco_term

						UNION ALL

						SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor, c.inco_term
						FROM com_export_invoice_ship_mst a, com_sales_contract c,com_export_doc_submission_invo d,COM_EXPORT_DOC_SUBMISSION_MST e
						WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=2 and a.lc_sc_id=c.id  and a.id=d.invoice_id and d.DOC_SUBMISSION_MST_ID = e.id  and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $sub_date_cond $forwarder_cond $ship_cond $invoice_cond  $ascendig_cond

						group by a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name, c.pay_term, c.lien_bank, c.contract_no,a.insert_date, c.doc_presentation_days, a.co_date,c.tenor, c.inco_term
						";
						
						
					}
					else
					{
						$order_by_cond = "";
						switch ($cbo_based_on) 
						{
							case 1:
								$order_by_cond = "invoice_date";
								break;
							case 2:
								$order_by_cond = "ex_factory_date";
								break;
							case 3:
								$order_by_cond = "actual_shipment_date";
								break;
							case 4:
								$order_by_cond = "bl_date";
								break;
							case 5:
								$order_by_cond = "ship_bl_date";
								break;
							case 6:
								$order_by_cond = "id";
								break;
							case 7:
								$order_by_cond = "insert_date";
								break;							
							default:
								$order_by_cond = "id";
								break;
						}

						if($db_type==0)
						{
							$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor,b.inco_term
							FROM com_export_invoice_ship_mst a, com_export_lc b
							WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' AND b.export_lc_no LIKE '%$txt_lc_sc_no%' and a.is_lc=1 and a.lc_sc_id=b.id and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  $str_cond $forwarder_cond $ship_cond $invoice_cond

							UNION ALL

							SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date, a.invoice_value, a.discount_ammount, a.bonus_ammount, a.claim_ammount, a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor,c.inco_term
							FROM com_export_invoice_ship_mst a, com_sales_contract c
							WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=2 and a.lc_sc_id=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond  $ascendig_cond";//a.ex_factory_date";//id

						}
						else
						{
							$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor,b.inco_term

							FROM com_export_invoice_ship_mst a, com_export_lc b
							WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=1 and a.lc_sc_id=b.id and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 AND b.export_lc_no LIKE '%$txt_lc_sc_no%'  $str_cond $forwarder_cond $ship_cond $invoice_cond

							UNION ALL

							SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor, c.inco_term
							FROM com_export_invoice_ship_mst a, com_sales_contract c
							WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=2 and a.lc_sc_id=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond  $ascendig_cond";
						}
						// echo $sql;
					}
					// echo $sql;die;
	                $sql_re=sql_select($sql);$k=1;
	                foreach($sql_re as $row_result)
	                {
						if ($k%2==0)
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";
						$id=$row_result[csf('id')];
						$exfact_date_calculate=$row_result[csf('ex_factory_date')];//$variable_standard_arr
						$bl_date_calculate=$row_result[csf('bl_date')];
						$variable_standard_bl_day=$possiable_bl_date=$realization_sub_day=$doc_presentation_days=$possiable_bank_sub_date=$variable_standard_gsp_day=$possiable_gsp_date=$variable_standard_co_day=$possiable_co_date="";
						if($exfact_date_calculate!="" && $exfact_date_calculate!='0000-00-00')
						{
							$variable_standard_bl_day=$variable_standard_arr[1]*60*60*24;
							$possiable_bl_date=date('d-m-Y',strtotime($exfact_date_calculate)+$variable_standard_bl_day);

						}
						if($bl_date_calculate!="" && $bl_date_calculate!='0000-00-00')
						{
							if($row_result[csf("type")]==1)
							{
								$realization_sub_day=$bank_sub_data[$row_result[csf('id')]]["submit_date"];
								$doc_presentation_days=$row_result[csf("doc_presentation_days")]*60*60*24;
								$possiable_bank_sub_date=date('d-m-Y',strtotime($bl_date_calculate)+$doc_presentation_days);
							}
							if($row_result[csf("type")]==2)
							{
								$realization_sub_day=$buyer_submit_date_arr[$row_result[csf('id')]];
								$doc_presentation_days=$row_result[csf("doc_presentation_days")]*60*60*24;
								$possiable_buyer_sub_date=date('d-m-Y',strtotime($bl_date_calculate)+$doc_presentation_days);
							}
							$variable_standard_gsp_day=$variable_standard_arr[2]*60*60*24;
							$possiable_gsp_date=date('d-m-Y',strtotime($bl_date_calculate)+$variable_standard_gsp_day);
							$variable_standard_co_day=$variable_standard_arr[3]*60*60*24;
							$possiable_co_date=date('d-m-Y',strtotime($bl_date_calculate)+$variable_standard_co_day);

						}
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
	                    	<td width="50" style="word-break: break-all;"><? echo $k;//$row_result[csf('id')];?></th>

	                        <td width="100" style="word-break: break-all;"><? echo  $company_arr[$row_result[csf('benificiary_id')]]; ?></td>
	                       
	                        <td width="100" style="word-break: break-all;"><p>
	                        	<a href='##' style='color:#000' onClick="print_report('<? echo $row_result[csf('id')] ;?>','invoice_report_print','../export_details/requires/export_information_entry_controller')"><font color="blue"><b><? echo $row_result[csf('invoice_no')];?></b></font></a></p>
							</td>

	                        
	                        <td width="70"  align="center" style="word-break: break-all;"><? if($row_result[csf('type')] == 1) echo "LC"; else echo "SC"; ?></td>
	                        <td width="100" style="word-break: break-all;"><? echo $row_result[csf('lc_sc_no')];?></td>
	                        <td width="70" style="word-break: break-all;"><? echo  $buyer_arr[$row_result[csf('buyer_id')]];?></td>
	                        <td width="90" style="word-break: break-all;"><? echo $row_result[csf('exp_form_no')]."&nbsp;";?></td>
	                        <td width="70"  align="center" style="word-break: break-all;"><?  if($row_result[csf('exp_form_date')]!="0000-00-00" && $row_result[csf('exp_form_date')]!="") {echo change_date_format($row_result[csf('exp_form_date')]);} else {echo "&nbsp;";} ?></td>
	                        <td width="100" align="right" style="word-break: break-all;"><? //echo number_format($exfact_qnty_arr[$row_result[csf('id')]],2);
							 //$total_ex_fact_qnty+=$exfact_qnty_arr[$row_result[csf('id')]]; ?>
	                        <a href="##" onClick="generate_ex_factory_popup('ex_factory_popup','<? echo $row_result[csf('country_id')];?>','<? echo $row_result[csf('id')]; ?>','550px')"><? echo  number_format($exfact_qnty_arr[$row_result[csf('id')]],2);
							 $total_ex_fact_qnty+=$exfact_qnty_arr[$row_result[csf('id')]];
							 ?></a>

	                        </td>
	                        <td width="100" align="right" style="word-break: break-all;"><a href='#report_detals'  onclick= "openmypage('<? echo $id; ?>','<? echo $k; ?>')"><? echo number_format($row_result[csf('invoice_quantity')],2); $total_invoice_qty +=$row_result[csf('invoice_quantity')];?></a></td>
	                        <td width="80" align="right" style="word-break: break-all;"><? echo number_format($row_result[csf('total_carton_qnty')],2); $total_carton_qty +=$row_result[csf('total_carton_qnty')];?></td>
	                        <td width="100" align="right" style="word-break: break-all;"><? echo number_format($row_result[csf('invoice_value')],2,'.',''); $total_grs_value +=$row_result[csf('invoice_value')];?></td>

							<td width="100" align="right" style="word-break: break-all;"><? echo number_format($row_result[csf('discount_ammount')],2,'.',''); $total_discount_value +=$row_result[csf('discount_ammount')]; ?></td>
	                        <td width="100" align="right" style="word-break: break-all;"><? echo number_format($row_result[csf('bonus_ammount')],2,'.',''); $total_bonous_value +=$row_result[csf('bonus_ammount')];  ?></td>
	                        <td width="100" align="right" style="word-break: break-all;"><? echo number_format($row_result[csf('claim_ammount')],2,'.',''); $total_claim_value +=$row_result[csf('claim_ammount')];  ?></td>
	                        <td width="100" align="right" style="word-break: break-all;"><?  echo number_format($row_result[csf('commission')],2,'.',''); $total_commission_value +=$row_result[csf('commission')]; ?></td>
	                        <td width="100" align="right" style="word-break: break-all;"><?  echo number_format($row_result[csf('other_discount_amt')],2,'.',''); $total_other_discount_value +=$row_result[csf('other_discount_amt')]; ?></td>
	                        <td width="100" align="right" style="word-break: break-all;"><?  echo number_format($row_result[csf('upcharge')],2,'.',''); $total_upcharge_value +=$row_result[csf('upcharge')]; ?></td>
	                        <td width="100" align="right" style="word-break: break-all;"><? echo number_format($row_result[csf('net_invo_value')],2,'.',''); $total_order_qnty +=$row_result[csf('net_invo_value')];?></td>


	                         <td width="70" align="center" style="word-break: break-all;">
	                        <? if($row_result[csf('invoice_date')]!="0000-00-00" && $row_result[csf('invoice_date')]!="") {echo change_date_format($row_result[csf('invoice_date')]);} else {echo "&nbsp;";}?>
	                        </td>
	                        <td width="70"  align="center" style="word-break: break-all;">
	                        <? if($row_result[csf('insert_date')]!="0000-00-00" && $row_result[csf('insert_date')]!="") {echo change_date_format($row_result[csf('insert_date')]);} else {echo "&nbsp;";}?>
	                        </td>
	                        <td width="80" style="word-break: break-all;"><? echo $shipment_mode[$row_result[csf('shipping_mode')]];?></td>
	                        <td width="70" align="center"><? echo $incoterm[$row_result[csf('inco_term')]];  ?></td>
	                        <td width="70"  align="center" style="word-break: break-all;"><? if($row_result[csf('ex_factory_date')]!="0000-00-00" && $row_result[csf('ex_factory_date')]!="") {echo change_date_format($row_result[csf('ex_factory_date')]);} else {echo "&nbsp;";} ?></td>
	          						
							
	                        <td width="100" style="word-break: break-all;"><? echo $row_result[csf('bl_no')];?></td>
	                        <td width="70"  align="center" style="word-break: break-all;"><?  if($row_result[csf('bl_date')]!="0000-00-00" && $row_result[csf('bl_date')]!="") {echo change_date_format($row_result[csf('bl_date')]);} else {echo "&nbsp;";} ?></td>
	                
	                     
	                        <td width="70"  style="word-break: break-all;"  align="center">
							<?
	                            if(trim($bank_sub_data[$row_result[csf('id')]]["submit_date"])!='0000-00-00' && trim($bank_sub_data[$row_result[csf('id')]]["submit_date"])!='') echo change_date_format(trim($bank_sub_data[$row_result[csf('id')]]["submit_date"])); else echo "&nbsp;";
	                        ?></td>

	                        <td width="100" style="word-break: break-all;"><? echo $bank_sub_data[$row_result[csf('id')]]["bank_ref_no"]; ?></td>
	                        <td width="70" style="word-break: break-all;"><? echo $row_result[csf('shipping_bill_n')]; ?></td>
							<td width="70" style="word-break: break-all;"><?  if($row_result[csf('ship_bl_date')]!="0000-00-00" && $row_result[csf('ship_bl_date')]!="") {echo change_date_format($row_result[csf('ship_bl_date')]);} else {echo "&nbsp;";}  ?></td>
							<td width="70" style="word-break: break-all;"><?
							$total_carton_gross_weight += $row_result[csf('carton_gross_weight')]; 
							echo $row_result[csf('carton_gross_weight')];
							?></td>
							<td width="70" style="word-break: break-all;"><? 
							$total_carton_net_weight += $row_result[csf('carton_net_weight')];
							echo $row_result[csf('carton_net_weight')];
							?></td>
	                        <td width="70" align="center"><? echo $row_result[csf('net_invo_value')];$total_rlz_amt+=$row_result[csf('net_invo_value')]; //$bank_sub_data[$row_result[csf('id')]]["bnk_to_bnk_cour_no"];  ?></td>
						</tr>
						<?
						$k++;
	                }
	                ?>
	                </tbody>
	            </table>
	     		 <table width="4290" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="report_table_footer" align="left">
	            	<tfoot>
	                    <tr>
	                        <th width="50">&nbsp;</th>
	                        <th width="100">&nbsp;</th>
	                        <th width="100">&nbsp;</th>

	                        
	                        <th width="70">&nbsp;</th>
	                        <th width="100">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="90">&nbsp;</th>
	                        <th width="70">Total:</th>
	                        <th width="100" id="total_ex_fact_qnty" align="right"><? echo number_format($total_ex_fact_qnty,2); ?></th>
	                        <th width="100" id="total_invoice_qty" align="right"><? echo number_format($total_invoice_qty,2); ?></th>
	                        <th width="80" id="total_carton_qty" align="right"><? echo number_format($total_carton_qty,2); ?></th>
	                        <th width="100" id="value_total_grs_value"  align="right"><? echo number_format($total_grs_value,2);  ?></th>
							
							<th width="100" id="value_total_discount_value"  align="right"><? echo number_format($total_discount_value,2);  ?></th>
	                        <th width="100" id="value_total_bonous_value"  align="right"><? echo number_format($total_bonous_value,2);  ?></th>
	                        <th width="100" id="value_total_claim_value"  align="right"><? echo number_format($total_claim_value,2);  ?></th>
	                        <th width="100" id="value_total_commission_value"  align="right"><? echo number_format($total_commission_value,2);  ?></th>

	                        <th width="100"  align="right"><? echo number_format($total_other_discount_value,2); ?></th>
	                        <th width="100"  align="right"><? echo number_format($total_upcharge_value,2);?></th>
	                        <th width="100"  id="value_total_net_invo_value"  align="right"><? echo number_format($total_order_qnty,2);  ?></th>

	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="80">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70" >&nbsp;</th>	                        
	                        <th width="100">&nbsp;</th>
	                        <th width="70">&nbsp;</th>               
	                        <th width="70">&nbsp;</th>
	                        <th width="100">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70"><? echo number_format($total_carton_gross_weight,2);  ?></th>
	                        <th width="70"><? echo number_format($total_carton_net_weight,2);  ?></th>
	                        <th width="70"  id="value_total_rlz_amt"  align="center"><? echo number_format($total_rlz_amt,2);  ?></th>                    
	                    </tr>
	                </tfoot>
	            </table>
     		</div>
    	</div>
    	<div align="left" style="font-weight:bold; margin-left:30px;"><? echo "User Id : ". $user_arr[$user_id] ." , &nbsp; THIS IS SYSTEM GENERATED STATEMENT, NO SIGNATURE REQUIRED ."; ?></div>
		<?
		foreach (glob("$user_id*.xls") as $filename)
		{

			if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename=$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc,ob_get_contents());
		//$filename=$user_id."_".$name.".xls";
		echo "$total_data****".$RptType."****".$filename;
		//echo "****".$RptType;
	}
	exit();
}


if($action=="report_generate_excel")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_based_on=str_replace("'","",$cbo_based_on);
	$cbo_ascending_by=str_replace("'","",$cbo_ascending_by);
	if($cbo_based_on==8)
	{
		$c_date=date('d-M-Y');
		$txt_date_from="16-Apr-2019";
		$txt_date_to=$c_date;
	}

	if($RptType==1) //DETAILS
	{
		$user_arr = return_library_array("select id,user_name from user_passwd ","id","user_name");
		$bank_arr=return_library_array("select id,bank_name from   lib_bank","id","bank_name");
		$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$location_arr=return_library_array("select id,location_name from  lib_location","id","location_name");
		$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
		$supplier_arr=return_library_array("select id,supplier_name from lib_supplier","id","supplier_name");
		$country_arr=return_library_array("select id,country_name from  lib_country","id","country_name");

		$sub_sql=sql_select("SELECT b.invoice_id, a.courier_date, a.submit_date, a.bnk_to_bnk_cour_no, a.bank_ref_no, a.possible_reali_date 
			from com_export_doc_submission_mst a,com_export_doc_submission_invo b 
			where a.id=b.doc_submission_mst_id and a.entry_form=40 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		$bank_sub_data=array();
		foreach($sub_sql as $row)
		{
			$bank_sub_data[$row[csf("invoice_id")]]["courier_date"]=$row[csf("courier_date")];
			$bank_sub_data[$row[csf("invoice_id")]]["submit_date"]=$row[csf("submit_date")];
			$bank_sub_data[$row[csf("invoice_id")]]["bnk_to_bnk_cour_no"]=$row[csf("bnk_to_bnk_cour_no")];
			$bank_sub_data[$row[csf("invoice_id")]]["bank_ref_no"]=$row[csf("bank_ref_no")];
			$bank_sub_data[$row[csf("invoice_id")]]["possible_reali_date"]=$row[csf("possible_reali_date")];
		}

		$buyer_submit_date_arr=return_library_array(" SELECT b.invoice_id,a.submit_date from com_export_doc_submission_mst a,com_export_doc_submission_invo b where a.id=b.doc_submission_mst_id and a.entry_form=39","invoice_id","submit_date");


		$rlz_date_arr=return_library_array(" SELECT a.invoice_id,b.received_date,b.is_invoice_bill
		from com_export_doc_submission_invo a, com_export_proceed_realization b
		where a.doc_submission_mst_id=b.invoice_bill_id and b.is_invoice_bill  = 1
		union all
		select b.invoice_bill_id as invoice_id, b.received_date , b.is_invoice_bill
		from  com_export_proceed_realization b
		where  b.is_invoice_bill  = 2
		order by invoice_id","invoice_id","received_date");

		$exfac_sql=("SELECT B.INVOICE_NO,A.SYS_NUMBER,B.PO_BREAK_DOWN_ID, B.COUNTRY_ID, B.ITEM_NUMBER_ID,
		SUM(CASE WHEN B.ENTRY_FORM!=85 THEN B.EX_FACTORY_QNTY ELSE 0 END)  AS EX_FACTORY_QNTY
		FROM  PRO_EX_FACTORY_DELIVERY_MST A,  PRO_EX_FACTORY_MST B  WHERE A.ID=B.DELIVERY_MST_ID AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND B.ENTRY_FORM!=85 AND B.EX_FACTORY_QNTY>0 GROUP BY  B.INVOICE_NO, A.SYS_NUMBER, B.PO_BREAK_DOWN_ID, B.COUNTRY_ID, B.ITEM_NUMBER_ID");
		//echo $exfac_sql;die;
		$sql_dtls=sql_select($exfac_sql);
		$exfact_sys_arr=array();
		$exfact_qnty_arr=array();
		foreach($sql_dtls as $row)
		{
			$exfact_qnty_arr[$row["INVOICE_NO"]]+=$row["EX_FACTORY_QNTY"];
			//$exfact_sys_arr[$row["SYS_NUMBER"]]=$row["INVOICE_NO"];
			$seq_grouping = $row['PO_BREAK_DOWN_ID']."*".$row['ITEM_NUMBER_ID']."*".$row['COUNTRY_ID']."*".$row['SYS_NUMBER'];
			$exfact_sys_arr[$seq_grouping]=$row["INVOICE_NO"];
		}
		//echo '<pre>';print_r($exfact_sys_arr);die;
		
		$exfac_return_sql=("SELECT B.CHALLAN_NO, B.INVOICE_NO, SUM(CASE WHEN B.ENTRY_FORM=85 THEN B.EX_FACTORY_QNTY ELSE 0 END) AS EX_FACTORY_RETURN_QNTY,B.PO_BREAK_DOWN_ID, B.COUNTRY_ID, B.ITEM_NUMBER_ID
		FROM  PRO_EX_FACTORY_DELIVERY_MST A,  PRO_EX_FACTORY_MST B  WHERE A.ID=B.DELIVERY_MST_ID AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND B.ENTRY_FORM=85 AND B.EX_FACTORY_QNTY>0 GROUP BY B.CHALLAN_NO, B.INVOICE_NO ,B.PO_BREAK_DOWN_ID, B.COUNTRY_ID, B.ITEM_NUMBER_ID");
		//echo $exfac_return_sql;
		$exfac_return_sql_res=sql_select($exfac_return_sql);
		$return_qty_arr=array();
		foreach ($exfac_return_sql_res as $row){
			//$return_qty_arr[$exfact_sys_arr[$row["CHALLAN_NO"]]]+=$row["EX_FACTORY_RETURN_QNTY"];
			//$return_qty_arr[$row["INVOICE_NO"]]+=$row["EX_FACTORY_RETURN_QNTY"];
			$seq_grouping = $row['PO_BREAK_DOWN_ID']."*".$row['ITEM_NUMBER_ID']."*".$row['COUNTRY_ID']."*".$row['CHALLAN_NO'];
			$return_qty_arr[$exfact_sys_arr[$seq_grouping]]+=$row["EX_FACTORY_RETURN_QNTY"];
		}
		//echo '<pre>';print_r($return_qty_arr);die;
		
		$variable_standard_arr=return_library_array(" SELECT monitor_head_id,monitoring_standard_day from variable_settings_commercial where status_active=1 and is_deleted=0 and company_name=$cbo_company_name and variable_list=19","monitor_head_id","monitoring_standard_day");
		
		

		$cbo_company_name=str_replace("'","",$cbo_company_name);
		if($cbo_company_name!=0) $cbo_company_name = $cbo_company_name; else $cbo_company_name="%%";

		$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
		if($cbo_buyer_name == 0) $cbo_buyer_name="%%"; else $cbo_buyer_name = $cbo_buyer_name;

		$cbo_lien_bank=str_replace("'","",$cbo_lien_bank);
		if($cbo_lien_bank == 0) $cbo_lien_bank="%%"; else $cbo_lien_bank = $cbo_lien_bank;
		//echo  $cbo_lien_bank;die;
		$cbo_location=str_replace("'","",$cbo_location);
		if($cbo_location == 0) $cbo_location="%%"; else $cbo_location = $cbo_location;

		if($cbo_based_on == 0) $cbo_based_on="%%"; else $cbo_based_on = $cbo_based_on;

		$txt_date_from=str_replace("'","",$txt_date_from);
		if(trim($txt_date_from)!= "") $txt_date_from  =$txt_date_from;

		$txt_date_to=str_replace("'","",$txt_date_to);
		if(trim($txt_date_to)!= "") $txt_date_to = $txt_date_to;

		$forwarder_name=str_replace("'","",$forwarder_name);
		if($forwarder_name!=0) $forwarder_cond=" and a.forwarder_name='$forwarder_name'"; else $forwarder_cond="";
		$txt_invoice_no=str_replace("'","",$txt_invoice_no);
		if($txt_invoice_no!="") $invoice_cond=" and a.invoice_no like '%$txt_invoice_no'"; else $invoice_cond="";

		if($cbo_ascending_by!="" && $cbo_ascending_by==1 ) 
		{
			$ascending_by = "invoice_no";
			$ascendig_cond=" order by $ascending_by asc ";
		} elseif ($cbo_ascending_by!="" && $cbo_ascending_by==2) {
			$ascending_by = "invoice_date";
			$ascendig_cond=" order by $ascending_by asc ";
		} elseif ($cbo_ascending_by!="" && $cbo_ascending_by==3) {
			$ascending_by = "exp_form_no";
			$ascendig_cond=" order by $ascending_by asc ";
		}elseif ($cbo_ascending_by!="" && $cbo_ascending_by==4) {
			$ascending_by = "exp_form_date";
			$ascendig_cond=" order by $ascending_by asc ";
		}else{
			$ascendig_cond="";
		}
		//echo $cbo_ascending_by;die;
		//if(trim($data[7])!="") $cbo_year2=$data[7];
		if ($txt_date_from!="" && $txt_date_to!="")
		{
			if($cbo_based_on ==0)
			{
				$str_cond=" and a.invoice_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==1)
			{
				$str_cond=" and a.invoice_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==2)
			{
				$str_cond=" and a.ex_factory_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==3)
			{
				$str_cond=" and a.actual_shipment_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==4)
			{
				$str_cond=" and a.bl_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==5)
			{
				$str_cond=" and a.ship_bl_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==6)
			{
				$str_cond=" and e.received_date between '$txt_date_from' and  '$txt_date_to'";
			}
			else if($cbo_based_on ==7)
			{

				if($db_type==0) $str_cond=" and a.insert_date between '$txt_date_from 00:00:01' and  '$txt_date_to 23:59:59'";
				else if($db_type==2) $str_cond=" and a.insert_date between '$txt_date_from 12:00:01 AM' and  '$txt_date_to 11:59:59 PM'";
			}
			else if($cbo_based_on ==8)
			{
				$str_cond=" and a.invoice_date between '$txt_date_from' and  '$txt_date_to'";
			}
		}
		else
		{
			$str_cond="";
		}
		$shipping_mode=str_replace("'","",$shipping_mode);
		$ship_cond="";
		if($shipping_mode!=0) $ship_cond=" and a.shipping_mode=$shipping_mode";
		
		$txt_lc_sc_no=str_replace("'","",$txt_lc_sc_no);
		$txt_int_ref_no=str_replace("'","",$txt_int_ref_no);
		$ref_inv_cond="";
		$ref_inv_id_arr=array();
		if($txt_int_ref_no!="")
		{
			$sql_ref="select a.mst_id as INV_ID, b.id as PO_ID, b.grouping as REF_NO from com_export_invoice_ship_dtls a, wo_po_break_down b where a.po_breakdown_id=b.id and a.status_active=1 and a.is_deleted=0 and b.grouping='$txt_int_ref_no'";
			//echo $sql_ref;die;
			$sql_ref_result=sql_select($sql_ref);
			foreach($sql_ref_result as $row)
			{
				$ref_inv_id_arr[$row["INV_ID"]]=$row["INV_ID"];
			}
			if(count($ref_inv_id_arr)<1)
			{
				echo "No Data Found.";die;
			}
		}
		//echo $invoice_cond;die;
		
		
		// $job_id_data=implode(",",array_unique(explode(",",chop($job_id_data,','))));
		if($cbo_based_on==6)
		{
			if($db_type==0)
			{
				$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, b.internal_file_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor, group_concat(distinct(f.po_breakdown_id)) as PO_ID,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,b.export_item_category,a.atsite_discount_amt
				FROM com_export_invoice_ship_mst a, com_export_lc b, com_export_doc_submission_invo d, com_export_proceed_realization e, com_export_invoice_ship_dtls f
				WHERE a.lc_sc_id=b.id and a.id=d.invoice_id and d.doc_submission_mst_id=e.invoice_bill_id and a.id=f.mst_id  and a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' AND b.export_lc_no LIKE '%$txt_lc_sc_no%' and a.is_lc=1 and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and f.status_active=1 and f.is_deleted=0  $str_cond $forwarder_cond $ship_cond $invoice_cond  $ref_inv_cond
				group by a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name, b.pay_term, b.lien_bank, b.export_lc_no, b.internal_file_no, a.insert_date, b.doc_presentation_days, a.co_date,b.tenor,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,b.export_item_category,a.atsite_discount_amt

				UNION ALL

				SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, c.internal_file_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor, group_concat(distinct(f.po_breakdown_id)) as PO_ID,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,c.export_item_category,a.atsite_discount_amt
				FROM com_export_invoice_ship_mst a, com_sales_contract c , com_export_doc_submission_invo d, com_export_proceed_realization e, com_export_invoice_ship_dtls f
				WHERE a.lc_sc_id=c.id and a.id=d.invoice_id and d.doc_submission_mst_id=e.invoice_bill_id and a.id=f.mst_id and a.is_lc=2 and a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' AND c.contract_no LIKE '%$txt_lc_sc_no%' and  a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $str_cond $forwarder_cond $ship_cond $invoice_cond $ref_inv_cond
				group by a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name, c.pay_term, c.lien_bank, c.contract_no, c.internal_file_no, a.insert_date, c.doc_presentation_days, a.co_date ,c.tenor,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,c.export_item_category,a.atsite_discount_amt

				UNION ALL

				SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date, a. 	invoice_value, a.discount_ammount, a.stamp_value, a.bonus_ammount, a.claim_ammount, a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor, group_concat(distinct(f.po_breakdown_id)) as PO_ID,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,c.export_item_category,a.atsite_discount_amt
				FROM com_export_invoice_ship_mst a, com_sales_contract c , com_export_proceed_realization e, com_export_invoice_ship_dtls f
				WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=2 and a.lc_sc_id=c.id and a.id=f.mst_id and e.invoice_bill_id = a.id and e.is_invoice_bill = 2 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.status_active=1 and f.is_deleted=0 AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond $ref_inv_cond $ascendig_cond 
				group by a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date, a. 	invoice_value, a.discount_ammount, a.stamp_value, a.bonus_ammount, a.claim_ammount, a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name, c.pay_term, c.lien_bank, c.contract_no, a.insert_date, c.doc_presentation_days, a.co_date,c.tenor,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,c.export_item_category,a.atsite_discount_amt";

			}
			else
			{
				$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor, LISTAGG(f.po_breakdown_id, ',') WITHIN GROUP (ORDER BY f.po_breakdown_id) as PO_ID,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,b.export_item_category,a.atsite_discount_amt

				FROM com_export_invoice_ship_mst a, com_export_lc b , com_export_doc_submission_invo d, com_export_proceed_realization e, com_export_invoice_ship_dtls f
				WHERE a.lc_sc_id=b.id and a.id=d.invoice_id and a.id=f.mst_id and d.doc_submission_mst_id=e.invoice_bill_id  and a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' AND b.export_lc_no LIKE '%$txt_lc_sc_no%' and a.is_lc=1 and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and f.status_active=1 and f.is_deleted=0  $str_cond $forwarder_cond $ship_cond $invoice_cond $ref_inv_cond
				group by a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name , b.pay_term, b.lien_bank, b.export_lc_no ,a.insert_date, b.doc_presentation_days, a.co_date ,b.tenor,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,b.export_item_category,a.atsite_discount_amt

				UNION ALL

				SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor, LISTAGG(f.po_breakdown_id, ',') WITHIN GROUP (ORDER BY f.po_breakdown_id) as PO_ID,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,c.export_item_category,a.atsite_discount_amt
				FROM com_export_invoice_ship_mst a, com_sales_contract c, com_export_doc_submission_invo d, com_export_proceed_realization e, com_export_invoice_ship_dtls f
				WHERE a.lc_sc_id=c.id and a.id=d.invoice_id and a.id=f.mst_id and d.doc_submission_mst_id=e.invoice_bill_id  and a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' AND c.contract_no LIKE '%$txt_lc_sc_no%' and a.is_lc=2 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.status_active=1 and f.is_deleted=0  $str_cond $forwarder_cond $ship_cond $invoice_cond $ref_inv_cond 
				group by a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name , c.pay_term , c.lien_bank , c.contract_no , a.insert_date, c.doc_presentation_days, a.co_date ,c.tenor,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,c.export_item_category,a.atsite_discount_amt
				UNION ALL

				SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor, LISTAGG(f.po_breakdown_id, ',') WITHIN GROUP (ORDER BY f.po_breakdown_id) as PO_ID,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,c.export_item_category,a.atsite_discount_amt
				FROM com_export_invoice_ship_mst a, com_sales_contract c , com_export_proceed_realization e, com_export_invoice_ship_dtls f
				WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=2 and a.lc_sc_id=c.id and a.id=f.mst_id and e.invoice_bill_id = a.id and e.is_invoice_bill = 2 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.status_active=1 and f.is_deleted=0 AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond $ref_inv_cond $ascendig_cond 
				group by a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name , c.pay_term , c.lien_bank , c.contract_no , a.insert_date, c.doc_presentation_days, a.co_date,c.tenor,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,c.export_item_category,a.atsite_discount_amt";
			}
		}
		else if($cbo_based_on==8)
		{
			if($db_type==0)
			{
				$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, b.internal_file_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor, group_concat(distinct(f.po_breakdown_id)) as PO_ID,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,b.export_item_category,a.atsite_discount_amt
				FROM com_export_invoice_ship_mst a, com_export_lc b, com_export_invoice_ship_dtls f
				WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=1 and a.lc_sc_id=b.id and a.id=f.mst_id and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and f.status_active=1 and f.is_deleted=0 AND b.export_lc_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond and a.id not in(select a.invoice_id from com_export_doc_submission_invo a, com_export_proceed_realization b where a.doc_submission_mst_id=b.invoice_bill_id and a.is_lc=1) $ref_inv_cond
				group by a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name, b.pay_term, b.lien_bank, b.export_lc_no, b.internal_file_no, a.insert_date, b.doc_presentation_days, a.co_date,b.tenor,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,b.export_item_category,a.atsite_discount_amt

				UNION ALL

				SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, c.internal_file_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor, group_concat(distinct(f.po_breakdown_id)) as PO_ID,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,c.export_item_category,a.atsite_discount_amt
				FROM com_export_invoice_ship_mst a, com_sales_contract c, com_export_invoice_ship_dtls f
				WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=2 and a.lc_sc_id=c.id and a.id=f.mst_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.status_active=1 and f.is_deleted=0  AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond and a.id not in(select a.invoice_id from com_export_doc_submission_invo a, com_export_proceed_realization b where a.doc_submission_mst_id=b.invoice_bill_id and a.is_lc=2) $ref_inv_cond $ascendig_cond
				group by a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name, c.pay_term, c.lien_bank, c.contract_no, c.internal_file_no, a.insert_date, c.doc_presentation_days, a.co_date,c.tenor,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,c.export_item_category,a.atsite_discount_amt";

			}
			else
			{							
				$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, b.internal_file_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor, LISTAGG(f.po_breakdown_id, ',') WITHIN GROUP (ORDER BY f.po_breakdown_id) as PO_ID,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,b.export_item_category,a.atsite_discount_amt
				FROM com_export_invoice_ship_mst a, com_export_lc b, com_export_invoice_ship_dtls f
				WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=1 and a.lc_sc_id=b.id and a.id=f.mst_id and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and f.status_active=1 and f.is_deleted=0 AND b.export_lc_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond and a.id not in(select a.invoice_id from com_export_doc_submission_invo a, com_export_proceed_realization b where a.doc_submission_mst_id=b.invoice_bill_id and a.is_lc=1) $ref_inv_cond
				group by a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name, b.pay_term, b.lien_bank , b.export_lc_no, b.internal_file_no, a.insert_date, b.doc_presentation_days, a.co_date ,b.tenor,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,b.export_item_category,a.atsite_discount_amt

				UNION ALL

				SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, c.internal_file_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor, LISTAGG(f.po_breakdown_id, ',') WITHIN GROUP (ORDER BY f.po_breakdown_id) as PO_ID,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,c.export_item_category,a.atsite_discount_amt
				FROM com_export_invoice_ship_mst a, com_sales_contract c, com_export_invoice_ship_dtls f
				WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=2 and a.lc_sc_id=c.id and a.id=f.mst_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.status_active=1 and f.is_deleted=0  AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond and a.id not in(select a.invoice_id from com_export_doc_submission_invo a, com_export_proceed_realization b where a.doc_submission_mst_id=b.invoice_bill_id and a.is_lc=2) $ref_inv_cond $ascendig_cond
				group by a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name , c.pay_term , c.lien_bank , c.contract_no, c.internal_file_no, a.insert_date, c.doc_presentation_days, a.co_date , c.tenor,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,c.export_item_category,a.atsite_discount_amt";
			}
		}
		else
		{
			if($cbo_based_on ==9)
			{
				$submit_cond=" and b.submit_date between '$txt_date_from' and  '$txt_date_to'";
				//echo "SELECT a.invoice_id from com_export_doc_submission_invo a, com_export_doc_submission_mst bwhere a.doc_submission_mst_id=b.id and b.status_active = 1 and b.is_deleted = 0 and b.company_id = $cbo_company_name and b.entry_form=40 $submit_cond "; die;
				$submitted_invoices = sql_select("SELECT a.invoice_id from com_export_doc_submission_invo a, com_export_doc_submission_mst b
				where a.doc_submission_mst_id=b.id and b.status_active = 1 and b.is_deleted = 0 and b.company_id = $cbo_company_name and b.entry_form=40 $submit_cond ");
				foreach ($submitted_invoices as $val)
				{
					$all_submitted_invoice_arr[$val[csf("invoice_id")]] = $val[csf("invoice_id")];
				}

				if(count($all_submitted_invoice_arr)>999)
				{
					$all_submitted_invoice_chunk_arr=array_chunk($all_submitted_invoice_arr, 999);
					$all_submitted_invoice_cond=" and (";
					foreach ($all_submitted_invoice_chunk_arr as $value)
					{
						$all_submitted_invoice_cond .="and a.id in (".implode(",", $value).") or ";
					}
					$all_submitted_invoice_cond=chop($all_submitted_invoice_cond,"or ");
					$all_submitted_invoice_cond.=")";
				}
				else
				{
					$all_submitted_invoice_cond=" and a.id in (".implode(",", $all_submitted_invoice_arr).")";
				}
			}
			if($db_type==0)
			{
				$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, b.internal_file_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor, group_concat(distinct(f.po_breakdown_id)) as PO_ID,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,b.export_item_category,a.atsite_discount_amt
				FROM com_export_invoice_ship_mst a, com_export_lc b, com_export_invoice_ship_dtls f
				WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' AND b.export_lc_no LIKE '%$txt_lc_sc_no%' and a.is_lc=1 and a.lc_sc_id=b.id and a.id=f.mst_id and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $str_cond $forwarder_cond $ship_cond $invoice_cond $all_submitted_invoice_cond $ref_inv_cond
				group by SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name, b.pay_term, b.lien_bank, b.export_lc_no, b.internal_file_no, a.insert_date, b.doc_presentation_days, a.co_date ,b.tenor,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,b.export_item_category,a.atsite_discount_amt
				
				UNION ALL

				SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date, a.invoice_value, a.discount_ammount, a.stamp_value,a.bonus_ammount, a.claim_ammount, a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, c.internal_file_no, DATE_FORMAT(a.insert_date,'%d-%m-%Y') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor, group_concat(distinct(f.po_breakdown_id)) as PO_ID,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,c.export_item_category,a.atsite_discount_amt
				FROM com_export_invoice_ship_mst a, com_sales_contract c, com_export_invoice_ship_dtls f
				WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=2 and a.lc_sc_id=c.id and a.id=f.mst_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.status_active=1 and f.is_deleted=0 AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond  $ascendig_cond $all_submitted_invoice_cond $ref_inv_cond
				group by a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date, a.invoice_value, a.discount_ammount, a.stamp_value,a.bonus_ammount, a.claim_ammount, a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name, c.pay_term, c.lien_bank, c.contract_no, c.internal_file_no, a.insert_date, c.doc_presentation_days, a.co_date, c.tenor,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,c.export_item_category,a.atsite_discount_amt";//id

			}
			else
			{
				$sql="SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name as currency_name , b.pay_term as pay_term, b.lien_bank as lien_bank, b.export_lc_no as lc_sc_no, b.internal_file_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, b.doc_presentation_days, a.co_date as co_date, 1 as type,b.tenor, LISTAGG(f.po_breakdown_id, ',') WITHIN GROUP (ORDER BY f.po_breakdown_id) as PO_ID,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,b.export_item_category,a.atsite_discount_amt
				FROM com_export_invoice_ship_mst a, com_export_lc b, com_export_invoice_ship_dtls f
				WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND b.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=1 and a.lc_sc_id=b.id and a.id=f.mst_id and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and f.status_active=1 and f.is_deleted=0 AND b.export_lc_no LIKE '%$txt_lc_sc_no%'  $str_cond $forwarder_cond $ship_cond $invoice_cond $all_submitted_invoice_cond $ref_inv_cond
				group by a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, b.currency_name , b.pay_term, b.lien_bank , b.export_lc_no, b.internal_file_no, a.insert_date, b.doc_presentation_days, a.co_date ,b.tenor,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,b.export_item_category,a.atsite_discount_amt

				UNION ALL

				SELECT a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name as currency_name, c.pay_term as pay_term, c.lien_bank as lien_bank, c.contract_no as lc_sc_no, c.internal_file_no, to_char(a.insert_date,'DD-MM-YYYY') as  insert_date, c.doc_presentation_days, a.co_date as co_date, 2 as type,c.tenor, LISTAGG(f.po_breakdown_id, ',') WITHIN GROUP (ORDER BY f.po_breakdown_id) as PO_ID,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,c.export_item_category,a.atsite_discount_amt
				FROM com_export_invoice_ship_mst a, com_sales_contract c, com_export_invoice_ship_dtls f
				WHERE a.benificiary_id LIKE '$cbo_company_name' AND a.location_id LIKE '$cbo_location' AND a.buyer_id LIKE '$cbo_buyer_name' AND c.lien_bank LIKE '$cbo_lien_bank' and a.is_lc=2 and a.lc_sc_id=c.id and a.id=f.mst_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.status_active=1 and f.is_deleted=0 AND c.contract_no LIKE '%$txt_lc_sc_no%' $str_cond $forwarder_cond $ship_cond $invoice_cond $ascendig_cond $all_submitted_invoice_cond $ref_inv_cond
				group by a.id, a.benificiary_id, a.location_id, a.invoice_no, a.invoice_date, a.ex_factory_date, a.is_lc, a.buyer_id, a.exp_form_no, a.exp_form_date,a.invoice_value,a.discount_ammount,a.stamp_value,a.bonus_ammount,a.claim_ammount,a.commission,a.other_discount_amt,a.upcharge, a.net_invo_value, a.invoice_quantity, a.total_carton_qnty, a.actual_shipment_date, a.shipping_bill_n, a.ship_bl_date, a.bl_no, a.bl_date, a.etd, a.feeder_vessel, a.mother_vessel, a.etd_destination, a.bl_rev_date, a.ic_recieved_date, a.remarks, a.gsp_co_no, a.gsp_co_no_date,a.shipping_bill_n,a.ship_bl_date, a.forwarder_name, a.country_id, a.shipping_mode, a.carton_net_weight, a.carton_gross_weight, c.currency_name, c.pay_term, c.lien_bank, c.contract_no, c.internal_file_no, a.insert_date, c.doc_presentation_days, a.co_date,c.tenor,a.cargo_delivery_to,a.place_of_delivery,a.main_mark,a.side_mark,a.net_weight,a.gross_weight,a.cbm_qnty,a.delv_no,a.consignee,a.notifying_party,a.item_description,a.import_btb,c.export_item_category,a.atsite_discount_amt";
			}
		}
		//echo $sql;die;
		$sql_re=sql_select($sql);
		//echo count($sql_re)."=".$sql;die;
		// $job_arr=return_library_array("select id, job_no_mst from wo_po_break_down","id","job_no_mst");
		if(count($sql_re)<1){echo "No Data Found"; die;}
		$k=1;
		$invoice_id_check=array();
		$buyer_wise_summary_data=array();
		foreach($sql_re as $row)
		{
			$system_id_val="'".$row[csf('id')]."'";
			$system_file_img_arr[$row[csf('id')]] = $system_id_val;

			// $po_id_all.=$row['PO_ID'].',';
			$po_id_arr=explode(",",$row['PO_ID']);
			foreach($po_id_arr as $val){
				if($po_id) $po_id_arr[$po_id]=$po_id;
			}		
		}

		// $po_id_all=array_unique(explode(",",chop($po_id_all,',')));
		// $job_no_all=implode(",",array_unique(explode(",",chop($job_no_all,','))));
		//$po_filter_id=where_con_using_array($po_id_arr,0,'id');
		
		$po_filter=where_con_using_array($po_id_arr,0,'po_breakdown_id');
		
		$sql_order_set=sql_select("SELECT A.MST_ID, A.PO_BREAKDOWN_ID, A.CURRENT_INVOICE_QNTY, A.CURRENT_INVOICE_QNTY, (A.CURRENT_INVOICE_QNTY*C.TOTAL_SET_QNTY) AS INVOICE_QNTY_PCS, C.TOTAL_SET_QNTY, b.grouping as REF_NO, b.job_id as JOB_ID, b.JOB_NO_MST from com_export_invoice_ship_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and c.company_name=$cbo_company_name and a.status_active=1 and a.is_deleted=0 $po_filter");
		$inv_qnty_pcs_arr=array();
		foreach($sql_order_set as $row)
		{
			$inv_qnty_pcs_arr[$row['MST_ID']]+=$row["INVOICE_QNTY_PCS"];
			$inv_po_qnty_pcs_arr[$row['MST_ID']][$row['PO_BREAKDOWN_ID']]+=$row['CURRENT_INVOICE_QNTY'];
			
			if($ref_inv_check[$row["MST_ID"]][$row["REF_NO"]]=="")
			{
				$ref_inv_check[$row["MST_ID"]][$row["REF_NO"]]=$row["REF_NO"];
				$inv_ref_data[$row["MST_ID"]].=$row["REF_NO"].",";
				$job_id_data.=$row["JOB_ID"].",";
			}
			
			$job_id_arr[$row["JOB_ID"]]=$row["JOB_ID"];
			$job_arr[$row["PO_BREAKDOWN_ID"]]=$row["JOB_NO_MST"];
		}
		unset($sql_order_set);
		
		
		// echo "select image_location, master_tble_id from common_photo_library where master_tble_id in($system_file_img_cond) and form_name='export_invoice' and is_deleted=0 and file_type=2";
		if ($system_file_img_cond != '')
		{
			$data_file=sql_select("select image_location, master_tble_id from common_photo_library where master_tble_id in($system_file_img_cond) and form_name='export_invoice' and is_deleted=0 and file_type=2");
			$system_file_arr=array();
			foreach($data_file as $row)
			{
				$system_file_arr[$row[csf('master_tble_id')]]['file']=$row[csf('image_location')];
			}
			unset($data_file);
		}	
		// $po_id=where_con_using_array($po_id_arr,0,'b.id');
		$job_in_cond=where_con_using_array($job_id_arr,0,'a.id');
		if($db_type==0)
		{
			$order_sql = "SELECT a.id as ID, a.job_no as JOB_NO, a.avg_unit_price as AVG_UNIT_PRICE, sum(b.po_quantity) as ORD_QTY,group_concat(distinct(b.id)) as PO_ID, c.costing_per as COSTING_PER
			from wo_po_details_master a,wo_po_break_down b, wo_pre_cost_mst c
			where a.id=b.job_id $job_id_arr and b.job_no_mst=c.job_no  and a.status_active=1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id,a.avg_unit_price,a.job_no, c.costing_per"; 
		}
		else
		{
			$order_sql = "SELECT a.id as ID, a.job_no as JOB_NO, a.avg_unit_price as AVG_UNIT_PRICE, sum(b.po_quantity) as ORD_QTY, LISTAGG(b.id, ',') WITHIN GROUP (ORDER BY b.id) as PO_ID, c.costing_per as COSTING_PER 
			from wo_po_details_master a,wo_po_break_down b, wo_pre_cost_mst c
			where a.id=b.job_id $job_id_arr and b.job_no_mst=c.job_no  and a.status_active=1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id,a.avg_unit_price,a.job_no, c.costing_per";
		}
		///echo $order_sql;die;
		$order_data=sql_select($order_sql);
		$condition= new condition();
		// $condition->po_id("in($po_id_all)");
		// $condition->job_no("in ($job_no_all)");
		// $condition->po_id("in(". implode(',',$po_id_arr) .")");
		
		
		
		$condition->jobid_in(implode(',',$job_id_arr));
		$condition->init();
		$yarn= new yarn($condition);
		$yarn_costing_arr=$yarn->getJobWiseYarnAmountArray();
		
		$fabric= new fabric($condition);
		$fabric_costing_arr2=$fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
		
		
		
		//$conversion= new conversion($condition);
		//$conversion_costing_arr_process=$conversion->getAmountArray_by_job();
		
		$trims= new trims($condition);		
		$trims_costing_arr=$trims->getAmountArray_by_job();
		$emblishment= new emblishment($condition);
		$emblishment_costing_arr=$emblishment->getAmountArray_by_job();
		$wash= new wash($condition);
		
		
		
		$emblishment_costing_arr_wash=$wash->getAmountArray_by_job();
		$commercial= new commercial($condition);
		$commercial_costing_arr=$commercial->getAmountArray_by_job();
		$commission= new commision($condition);
		$commission_costing_arr=$commission->getAmountArray_by_job();
		$other= new other($condition);
		$other_costing_arr=$other->getAmountArray_by_job();
		//echo "test2";die;
		$job_in_cond=where_con_using_array($job_id_arr,0,'job_id');
		$sql_trim_summ = "SELECT id, job_no, trim_group,description,brand_sup_ref,remark, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,status_active,seq
		from wo_pre_cost_trim_cost_dtls
		where status_active=1 and is_deleted=0 $job_in_cond order by seq";
		$data_array_trim_summ=sql_select($sql_trim_summ);
		$trim_amount_arr=$trims->getAmountArray_precostdtlsid();					
		foreach( $data_array_trim_summ as $row )
		{
			$trim_amount=$trim_amount_arr[$row[csf("id")]];
			$trim_job_amountArr[$row[csf("job_no")]]+=$trim_amount;
		}
		$pre_cost_dtls_sql = "SELECT job_no,fabric_cost,fabric_cost_percent,trims_cost,trims_cost_percent,embel_cost,embel_cost_percent,wash_cost,wash_cost_percent,comm_cost,comm_cost_percent,commission,commission_percent,lab_test,lab_test_percent,inspection,inspection_percent,cm_cost,cm_cost_percent,freight,freight_percent,currier_pre_cost,currier_percent,certificate_pre_cost,certificate_percent,common_oh,common_oh_percent,total_cost,deffdlc_cost,deffdlc_percent,interest_cost,interest_percent,incometax_cost,incometax_percent,total_cost_percent,price_dzn,price_dzn_percent,margin_dzn,margin_dzn_percent,price_pcs_or_set,depr_amor_pre_cost,depr_amor_po_price,price_pcs_or_set_percent,margin_pcs_set,margin_pcs_set_percent,cm_for_sipment_sche
		from wo_pre_cost_dtls
		where status_active=1 and is_deleted=0 $job_in_cond";
		//echo $pre_cost_dtls_sql;die;
		$pre_cost_dtls_data=sql_select($pre_cost_dtls_sql);
		$all_total_cost=array();
		foreach($pre_cost_dtls_data as $row){
			$job_no=$row[csf("job_no")];
			$fab_purchase_knit2=array_sum($fabric_costing_arr2['knit']['grey'][$job_no]);
			$fab_purchase_woven2=array_sum($fabric_costing_arr2['woven']['grey'][$job_no]);
			$yarn_costing=$yarn_costing_arr[$job_no];
			$tot_fabric_cost=$fab_purchase_knit2+$fab_purchase_woven2;
			$conversion_cost=array_sum($conversion_costing_arr_process[$job_no]);
			$freight_cost=$other_costing_arr[$job_no]['freight'];
			$inspection_cost=$other_costing_arr[$job_no]['inspection'];
			$certificate_cost=$other_costing_arr[$job_no]['certificate_pre_cost'];
			$common_oh=$other_costing_arr[$job_no]['common_oh'];
			$currier_cost=$other_costing_arr[$job_no]['currier_pre_cost'];
			$cm_cost=$other_costing_arr[$job_no]['cm_cost'];
			$lab_test_cost=$other_costing_arr[$job_no]['lab_test'];
			$depr_amor_pre_cost=$other_costing_arr[$job_no]['depr_amor_pre_cost'];
			$deffdlc_cost=$other_costing_arr[$job_no]['deffdlc_cost'];
			$fabric_cost=$tot_fabric_cost;
			$trims_cost=$trim_job_amountArr[$job_no];
			$embel_cost=$emblishment_costing_arr[$job_no];
			$wash=$emblishment_costing_arr_wash[$job_no];
			// $commercial_cost=$commercial_costing_arr[$job_no];
			// $comm_cost=$row[csf("comm_cost")];
			// $cm_cost_dzn=$row[csf("cm_cost")];
			// $deffdlc_cost_dzn=$row[csf("deffdlc_cost")];
			$interest_cost=$row[csf("interest_cost")];
			$incometax_cost=$row[csf("incometax_cost")];
			// $commission=$commission_costing_arr[$job_no];
			$lab_test=$lab_test_cost;
			$inspection=$inspection_cost;
			// $cm_cost=$cm_cost;
			// $freight=$freight_cost;
			$currier_pre_cost=$currier_cost;
			$certificate_pre_cost=$certificate_cost;
			$common_oh=$common_oh;

			// $all_total_cost=$tot_fabric_cost+$yarn_costing+$conversion_cost+$trims_cost+$embel_cost+$wash+$commercial_cost+$commission+$lab_test_cost+$cm_cost+$currier_pre_cost+$inspection_cost+$freight+$common_oh+$certificate_pre_cost+$depr_amor_pre_cost+$interest_cost+$incometax_cost+$deffdlc_cost;
			// $others_cost_value=$all_total_cost-$cm_cost-$freight-$commercial_cost-$commission;
			$all_total_cost[$job_no]=$tot_fabric_cost+$yarn_costing+$conversion_cost+$trims_cost+$embel_cost+$wash+$lab_test_cost+$currier_pre_cost+$inspection_cost+$common_oh+$certificate_pre_cost+$depr_amor_pre_cost+$interest_cost+$incometax_cost+$deffdlc_cost;
		}
		// var_dump($all_total_cost);die;
		$cmPerDzn=array();
		$costPerDzn=array();
		foreach($order_data as $row){
			$po_id_all_arr=explode(",",$row['PO_ID']);
			if($row[csf("costing_per")]==1){$order_price_per_dzn=12;}
			else if($row[csf("costing_per")]==2){$order_price_per_dzn=1;}
			else if($row[csf("costing_per")]==3){$order_price_per_dzn=24;}
			else if($row[csf("costing_per")]==4){$order_price_per_dzn=36;}
			else if($row[csf("costing_per")]==5){$order_price_per_dzn=48;}
			foreach($po_id_all_arr as $val){
				$less_commission=$commission_costing_arr[$job_arr[$val]];
				$less_commercial=$commercial_costing_arr[$job_arr[$val]];
				$less_freight=$other_costing_arr[$job_arr[$val]]['freight'];
				$order_net_value=($row['ORD_QTY']*$row['AVG_UNIT_PRICE'])-($less_commission+$less_commercial+$less_freight);
				$cmValue = $order_net_value-$all_total_cost[$job_arr[$val]];
				$cmPerDzn[$val]=$cmValue/$row['ORD_QTY']*$order_price_per_dzn;
				$costPerDzn[$val]=$order_price_per_dzn;
			}
		}
		// var_dump($cmPerDzn);die;
		
		//echo "<pre>";
		//print_r($system_file_arr);

		$invoice_id_check=array();
		$buyer_wise_summary_data=array();
		$buyerwise_cm_dzn_inv_qty=0;
		foreach($sql_re as $row)
		{
			
			$exfact_qnty=$exfact_qnty_arr[$row[csf('id')]]-$return_qty_arr[$row[csf('id')]];
			$print_data=0;
			if($cbo_based_on==2)
			{
				if($exfact_qnty>0) $print_data=1; else $print_data=0;
			}
			else
			{
				$print_data=1;
			}
			
			if($print_data)
			{
				$buyerpo_id_arr=explode(",",$row['PO_ID']);
				if($invoice_id_check[$row[csf('id')]]=="")
				{
					$invoice_id_check[$row[csf('id')]]=$row[csf('id')];
					$buyer_wise_summary_data[$row[csf('buyer_id')]]['invoice_quantity']+=$row[csf('invoice_quantity')];
					$buyer_wise_summary_data[$row[csf('buyer_id')]]['invoice_value']+=$row[csf('invoice_value')];
					$buyer_wise_summary_data[$row[csf('buyer_id')]]['net_invo_value']+=$row[csf('net_invo_value')];
					$buyer_wise_summary_data[$row[csf('buyer_id')]]['invoice_qnty_pcs']+=$inv_qnty_pcs_arr[$row[csf('id')]];
					//$buyer_key_data[$row[csf('buyer_id')]]+=$row[csf('net_invo_value')];
									
				}
				foreach($buyerpo_id_arr as $val){
					if ($costPerDzn[$val] > 0){
						$buyerwise_cm_dzn_inv_qty=($cmPerDzn[$val]/$costPerDzn[$val])*$inv_po_qnty_pcs_arr[$row[csf('id')]][$val];
						$buyer_wise_summary_data[$row[csf('buyer_id')]]['buyerwise_cm_dzn_inv_qty']+=$buyerwise_cm_dzn_inv_qty;		
					}						
				}
			}
		}

		$print_report_format=return_field_value("format_id","lib_report_template","template_name =".$cbo_company_name." and module_id=5 and report_id=245 and status_active=1 and is_deleted=0");		
		$format_id=explode(",",$print_report_format);
		//echo $print_report_format.'**'.$format_id[0];
		$based_on_arr=array(1=>"Invoice Date",2=>"Exfactory Date",3=>"Actual Date");

		
		$html .= '<div>
			<table>
				<tr>
					<td>
						<strong>Company Name:'. " ". $company_arr[str_replace("'","",$cbo_company_name)].'</strong>
					</td>
				</tr>
				<tr>
					<td>
						<strong>'. $report_title;
						
						if($cbo_based_on!=0)
						{						
							'<strong>Based On:' . " ".$based_on_arr[$cbo_based_on].'</strong>';						
						}
				$html .= '</strong>
					</td>
				</tr>
			</table>
			<br>
            <table>
                <thead>
                    <tr>
                        <th >Sl</th>
                        <th>Buyer</th>
                        <th>Invoice Qty.</th>
						<th>Invoice Qnty. Pcs</th>
                        <th>Invoice Value (Gross)</th>						
                        <th>Invoice Value (Net)</th>
						<th>Total CM as per Invoice Qty</th>
                    </tr>
                </thead>
                <tbody>';
				$i=1;
				foreach($buyer_wise_summary_data as $buy_id=>$buy_val)
				{
					if ($i%2==0)
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
					
             $html .= '<tr>
                    	<td>'.$i.'</td>
                        <td>'. $buyer_arr[$buy_id].'</td>
                        <td>'. number_format($buy_val["invoice_quantity"],2,'.','').'</td>
						<td>'. number_format($buy_val["invoice_qnty_pcs"],2,'.','').'</td>
                        <td>'. number_format($buy_val["invoice_value"],2,'.','').'</td>
                        <td>'. number_format($buy_val["net_invo_value"],2,'.','').'</td>
						<td>'. number_format($buy_val["buyerwise_cm_dzn_inv_qty"],2,'.','').'</td>
                    </tr>';
                    
					$i++;
					$buyerwise_tot_invoice_quantity +=$buy_val["invoice_quantity"];
					$buyerwise_tot_invoice_quantity_pcs +=$buy_val["invoice_qnty_pcs"];
					$buyerwise_tot_invoice_value +=$buy_val["invoice_value"];
					$buyerwise_tot_net_invo_value +=$buy_val["net_invo_value"];
					$buyerwise_tot_cmasper_invoice_qty +=$buy_val["buyerwise_cm_dzn_inv_qty"];
				}
				
     $html .= '</tbody>
                <tfoot>
                	<tr>
                    	<th>Total:</th>
                        <th>'. number_format($buyerwise_tot_invoice_quantity,2,'.','').'</th>
						 <th>'. number_format($buyerwise_tot_invoice_quantity_pcs,2,'.','').'</th>
                        <th>'. number_format($buyerwise_tot_invoice_value,2,'.','').'</th>
                        <th>'. number_format($buyerwise_tot_net_invo_value,2,'.','').'</th>
						<th>'. number_format($buyerwise_tot_cmasper_invoice_qty,2,'.','').'</th>
                    </tr>
                </tfoot>
            </table>			
			<br/>           
            <table>
                <thead>
                    <tr>
                        <th>Sl</th>
                        <th>Company Name</th>
                        <th>Country Name</th>
						<th>Buyer Name</th>
                        <th>Invoice No.</th>
                        <th>File</th>
                        <th>Int. Ref. No</th>
                        <th>Invoice Date</th>
                        <th>Insert Date</th>
                        <th>Ship Mode</th>
                        <th>SC/LC</th>
                        <th>SC/LC No.</th>
                        <th>File No</th>
                        <th>Forwarder Name</th>
                        <th>Lien Bank</th>
                        <th>EXP Form No</th>
                        <th>EXP Form Date</th>
                        <th>Ex-factory Qnty</th>
                        <th>Invoice Qnty.</th>
						<th>Invoice Qnty. Pcs</th>
                        <th>CM/Dzn</th>
                        <th>CM Value Per Pcs</th>
                        <th>Total CM as per Invoice Qty</th>                      
                        <th>Num. Of Ctn Qnty.</th>
                        <th>Avg Price</th>
                        <th>Invoice value</th>
                        <th>Discount</th>
                        <th>Discount For At Sight Payment Amount</th>
                        <th>Stamp Value</th>
                        <th>Inspection Amount</th>
                        <th>Claim</th>
                        <th>Commission</th>
                        <th>Other Deduction</th>
                        <th>Upcharge</th>
                        <th>Net Invoice Amount</th>
                        <th>Currency</th>
                        <th>ETD Date</th>
                        <th>Ex-Factory Date</th>
                        <th>Actual Ship Date</th>
                        <th>Possible BL Date</th>
                        <th>Copy B/L No</th>
                        <th>Copy B/L Date</th>
                        <th>B/L Days</th>
                        <th>Org B/L Rcv Date</th>
                        <th>Shipping Bill No</th>
                        <th>Shipping Bill Date</th>
                        <th>Possible GSP Date</th>
                        <th>Actual GSP Date</th>
                        <th>GSP No.</th>
                        <th>Possible CO Date</th>
                        <th>Actual CO Date</th>
                        <th>GSP Cour. Date</th>
                        <th>I/C Rcv Date</th>
                        <th>Document In Hand</th>
                        <th>Possible Buyer Sub date</th>
                        <th>Actual Buyer Sub Date</th>
                        <th>Buyer Sub Days</th>
                        <th>Possible Bank Sub date</th>
                        <th>Actual Bank Sub Date</th>
                        <th>Bank Sub Days</th>
                        <th>Bank Bill No.</th>
                        <th>Bank Bill Date</th>
                        <th>Shipping Bill No</th>
                        <th>Shipping Bill Date</th>
                        <th>Gross Weight</th>
                        <th>Net weight</th>
                        <th>Pay Term</th>
                        <th>LC Tenor</th>
                        <th>Possible Rlz. Date</th>
                        <th>Actual Realized Date</th>
                        <th>Realization Days</th>
                        <th>Realization Amount</th>
                        <th>Remarks</th>
                        <th>Feeder Vessle</th>
                        <th>Mother Vessle</th>
                        <th>ETA Dest.</th>
                        <th >B TO B Courier No</th>
                    </tr>
                </thead>
            </table>
            <div>
	            <table>
	                <tbody>';
	                          
	                foreach($sql_re as $row_result)
	                {
						$exfact_qnty=$exfact_qnty_arr[$row_result[csf('id')]]-$return_qty_arr[$row_result[csf('id')]];
						$print_data=0;
						if($cbo_based_on==2)
						{
							if($exfact_qnty>0) $print_data=1; else $print_data=0;
						}
						else
						{
							$print_data=1;
						}
						
						if($print_data)
						{
							if ($k%2==0)
							$bgcolor="#E9F3FF";
							else
							$bgcolor="#FFFFFF";
							$id=$row_result[csf('id')];
							$exfact_date_calculate=$row_result[csf('ex_factory_date')];//$variable_standard_arr
							$bl_date_calculate=$row_result[csf('bl_date')];
							$variable_standard_bl_day=$possiable_bl_date=$realization_sub_day=$doc_presentation_days=$possiable_bank_sub_date=$variable_standard_gsp_day=$possiable_gsp_date=$variable_standard_co_day=$possiable_co_date="";
							if($exfact_date_calculate!="" && $exfact_date_calculate!='0000-00-00')
							{
								$variable_standard_bl_day=$variable_standard_arr[1]*60*60*24;
								$possiable_bl_date=date('d-m-Y',strtotime($exfact_date_calculate)+$variable_standard_bl_day);
	
							}
							if($bl_date_calculate!="" && $bl_date_calculate!='0000-00-00')
							{
								if($row_result[csf("type")]==1)
								{
									$realization_sub_day=$bank_sub_data[$row_result[csf('id')]]["submit_date"];
									$doc_presentation_days=$row_result[csf("doc_presentation_days")]*60*60*24;
									$possiable_bank_sub_date=date('d-m-Y',strtotime($bl_date_calculate)+$doc_presentation_days);
								}
								if($row_result[csf("type")]==2)
								{
									$realization_sub_day=$buyer_submit_date_arr[$row_result[csf('id')]];
									$doc_presentation_days=$row_result[csf("doc_presentation_days")]*60*60*24;
									$possiable_buyer_sub_date=date('d-m-Y',strtotime($bl_date_calculate)+$doc_presentation_days);
								}
								$variable_standard_gsp_day=$variable_standard_arr[2]*60*60*24;
								$possiable_gsp_date=date('d-m-Y',strtotime($bl_date_calculate)+$variable_standard_gsp_day);
								$variable_standard_co_day=$variable_standard_arr[3]*60*60*24;
								$possiable_co_date=date('d-m-Y',strtotime($bl_date_calculate)+$variable_standard_co_day);
	
							}
							$po_id_arr=explode(",",$row_result['PO_ID']);
							$cm_dzn=$cm_dzn_per=$cm_dzn_inv_qty=0;
							$cm_dzn_popup=$cm_dzn_per_popup=$cm_invoice_dzn_per_popup='';
							$m=0;
							foreach($po_id_arr as $val){
								$cm_dzn+=$cmPerDzn[$val];
								$cm_dzn_inv_qty+=($cmPerDzn[$val]/$costPerDzn[$val])*$inv_po_qnty_pcs_arr[$row_result['ID']][$val];
								$cm_dzn_per+=$cmPerDzn[$val]/$costPerDzn[$val];
								$cm_dzn_popup.=$val."**".$cmPerDzn[$val].",";
								$cm_dzn_per_popup.=$val."**".$cmPerDzn[$val]/$costPerDzn[$val].",";
								$cm_invoice_dzn_per_popup.=$val."**".($cmPerDzn[$val]/$costPerDzn[$val])*$inv_po_qnty_pcs_arr[$row_result['ID']][$val].",";
								$m++;
							}
							$cm_dzn_popup=rtrim($cm_dzn_popup,',');
							$cm_dzn_per_popup=rtrim($cm_dzn_per_popup,',');
							$cm_invoice_dzn_per_popup=rtrim($cm_invoice_dzn_per_popup,',');
							
							$additional_info=$import_btb=$export_item_category="";
							if ($format_id[0]==788){
								$action="pdf";
								$invoice_variable="<a href='#' style='color:#000' title='".$format_id[0]."' onclick=\"fn_print_link('".$action."',".$format_id[0].",".$id.",".$row_result[csf('buyer_id')].");\"><font color='blue'><b>".$row_result[csf('invoice_no')]."</b></font><a/>";
							} else if ($format_id[0]==85) {
								$action="print_invoice3";
								$invoice_variable="<a href='#' style='color:#000' title='".$format_id[0]."' onclick=\"fn_print_link('".$action."',".$format_id[0].",".$id.",".$row_result[csf('buyer_id')].");\"><font color='blue'><b>".$row_result[csf('invoice_no')]."</b></font><a/>";
							} else if ($format_id[0]==84) {
								$action="invoice_report_print_2";
								$additional_info=$row_result[csf("cargo_delivery_to")].'_'.$row_result[csf("place_of_delivery")].'_'.$row_result[csf("main_mark")].'_'.$row_result[csf("side_mark")].'_'.$row_result[csf("net_weight")].'_'.$row_result[csf("gross_weight")].'_'.$row_result[csf("cbm_qnty")].'_'.$row_result[csf("delv_no")].'_'.$row_result[csf("consignee")].'_'.$row_result[csf("notifying_party")].'_'.$row_result[csf("item_description")];
								$invoice_variable="<a href='#' style='color:#000' title='".$format_id[0]."' onclick=\"fn_print_link('".$action."',".$format_id[0].",".$id.",".$additional_info.");\"><font color='blue'><b>".$row_result[csf('invoice_no')]."</b></font><a/>";
							} else if ($format_id[0]==150) {
								$action="print_invoice";
								$invoice_variable="<a href='#' style='color:#000' title='".$format_id[0]."' onclick=\"fn_print_link('".$action."',".$format_id[0].",".$id.",".$row_result[csf('buyer_id')].");\"><font color='blue'><b>".$row_result[csf('invoice_no')]."</b></font><a/>";
							} else if ($format_id[0]==789) {
								$action="invoice_report_print";
								$additional_info=$row_result[csf("cargo_delivery_to")].'_'.$row_result[csf("place_of_delivery")].'_'.$row_result[csf("main_mark")].'_'.$row_result[csf("side_mark")].'_'.$row_result[csf("net_weight")].'_'.$row_result[csf("gross_weight")].'_'.$row_result[csf("cbm_qnty")].'_'.$row_result[csf("delv_no")].'_'.$row_result[csf("consignee")].'_'.$row_result[csf("notifying_party")].'_'.$row_result[csf("item_description")];
								$invoice_variable="<a href='#' style='color:#000' title='".$format_id[0]."' onclick=\"fn_print_link('".$action."',".$format_id[0].",".$id.",'".$additional_info."');\"><font color='blue'><b>".$row_result[csf('invoice_no')]."</b></font><a/>";
							} else if ($format_id[0]==790 || $format_id[0]==792 || $format_id[0]==793 || $format_id[0]==794 || $format_id[0]==795 || $format_id[0]==796) {
								$action="print_generate";
								$export_item_category=0;
								if ($format_id[0]==790) $type=1;
								else if ($format_id[0]==792) $type=2;
								else if ($format_id[0]==793) $type=3;
								else if ($format_id[0]==794) $type=4;							
								else if ($format_id[0]==795) $type=5;
								else if ($format_id[0]==796) $type=6;
								if ($row_result[csf("is_lc")]==1) $export_item_category=$row_result[csf("export_item_category")];
								$additional_info=$row_result[csf("import_btb")].'_'.$export_item_category.'_'.$type;
								$invoice_variable="<a href='#' style='color:#000' title='".$format_id[0]."' onclick=\"fn_print_link('".$action."',".$format_id[0].",".$id.",'".$additional_info."');\"><font color='blue'><b>".$row_result[csf('invoice_no')]."</b></font><a/>";
							} else if ($format_id[0]==791) {
								$action="invoice_report_print_ci3";
								$invoice_variable="<a href='#' style='color:#000' title='".$format_id[0]."' onclick=\"fn_print_link('".$action."',".$format_id[0].",".$id.",'".$row_result[csf('buyer_id')]."');\"><font color='blue'><b>".$row_result[csf('invoice_no')]."</b></font><a/>";
							} else if ($format_id[0]==797) {
								$action="print_invoice_CIHnM";
								$invoice_variable="<a href='#' style='color:#000' title='".$format_id[0]."' onclick=\"fn_print_link('".$action."',".$format_id[0].",".$id.",'".$row_result[csf('buyer_id')]."');\"><font color='blue'><b>".$row_result[csf('invoice_no')]."</b></font><a/>";
							} else if ($format_id[0]==798) {
								$action="invoice_report_print_ci_ny";
								$additional_info=$row_result[csf("cargo_delivery_to")].'_'.$row_result[csf("place_of_delivery")].'_'.$row_result[csf("main_mark")].'_'.$row_result[csf("side_mark")].'_'.$row_result[csf("net_weight")].'_'.$row_result[csf("gross_weight")].'_'.$row_result[csf("cbm_qnty")].'_'.$row_result[csf("delv_no")].'_'.$row_result[csf("consignee")].'_'.$row_result[csf("notifying_party")].'_'.$row_result[csf("item_description")];
								$invoice_variable="<a href='#' style='color:#000' title='".$format_id[0]."' onclick=\"fn_print_link('".$action."',".$format_id[0].",".$id.",'".$additional_info."');\"><font color='blue'><b>".$row_result[csf('invoice_no')]."</b></font><a/>";
							} else $invoice_variable=$row_result[csf('invoice_no')];
							$total_ex_fact_qnty+=$exfact_qnty;
							$total_invoice_qty +=$row_result[csf('invoice_quantity')];
							$total_invoice_qty_pcs +=$inv_qnty_pcs_arr[$row_result[csf('id')]];
							$total_cm_asper_invoice_qty +=$cm_dzn_inv_qty;
							$total_carton_qty +=$row_result[csf('total_carton_qnty')];
							$avg_price=$row_result[csf('invoice_value')]/$row_result[csf('invoice_quantity')];  
							$total_avg_price +=$avg_price;
							$total_grs_value +=$row_result[csf('invoice_value')];

							$total_discount_value +=$row_result[csf('discount_ammount')];
							$atsite_discount_amt+= $row_result[csf('atsite_discount_amt')];
							$total_bonous_value +=$row_result[csf('bonus_ammount')];
							$total_claim_value +=$row_result[csf('claim_ammount')];
							$total_commission_value +=$row_result[csf('commission')]; 
							$total_other_discount_value +=$row_result[csf('other_discount_amt')];
							$total_upcharge_value +=$row_result[csf('upcharge')];
							$total_order_qnty +=$row_result[csf('net_invo_value')];
							$total_carton_gross_weight += $row_result[csf('carton_gross_weight')]; 
							$total_carton_net_weight += $row_result[csf('carton_net_weight')];
							
					$html .= '<tr>
								<td>'. $k.'</td>
								<td>'.  $company_arr[$row_result[csf('benificiary_id')]].'</td>
	
								<td>'. $country_arr[$row_result[csf('country_id')]].'</td>
							   
								<td>'.  $buyer_arr[$row_result[csf('buyer_id')]].'</td>
								<td>'. $invoice_variable.'</td>
								<td></td>
								<td>'. chop($inv_ref_data[$row_result[csf('id')]],",").'</td>';

								if($row_result[csf('invoice_date')]!="0000-00-00" && $row_result[csf('invoice_date')]!="") { $invoice_date = change_date_format($row_result[csf('invoice_date')]);}else {$invoice_date= "&nbsp;";}

					  $html .= '<td>'.$invoice_date.'
								</td>';
								if($row_result[csf('insert_date')]!="0000-00-00" && $row_result[csf('insert_date')]!="") {$date_insert= change_date_format($row_result[csf('insert_date')]);} else {$date_insert= "&nbsp;";} 
					 $html .= '<td>'.$date_insert.'</td>
								<td>'.$shipment_mode[$row_result[csf('shipping_mode')]].'</td>';
								if($row_result[csf('type')] == 1) $lc_sc= "LC"; else $lc_sc= "SC";
					$html .= '<td>'.$lc_sc.'</td>
								<td>'. $row_result[csf('lc_sc_no')].'</td>
								<td>'. $row_result[csf('internal_file_no')].'</td>
	
								<td>'.  $supplier_arr[$row_result[csf('forwarder_name')]].'</td>
								<td>'. $bank_arr[$row_result[csf('lien_bank')]].'</td>
								<td>'. $row_result[csf('exp_form_no')].'</td>';
								if($row_result[csf('exp_form_date')]!="0000-00-00" && $row_result[csf('exp_form_date')]!="") { $exp_form_date= change_date_format($row_result[csf('exp_form_date')]);} else { $exp_form_date= "&nbsp;";}
					  $html .= '<td>'.$exp_form_date.' </td>
								<td>'. number_format($exfact_qnty,2,'.','').'</td>';
								
				  	 $html .= '<td>'. number_format($row_result[csf('invoice_quantity')],2,'.','').'</td>
								
							   <td>'. number_format($inv_qnty_pcs_arr[$row_result[csf('id')]],2,'.','').'</td>
	
								<td>'. number_format($cm_dzn/$m,4,'.','').'</td>
								<td>'. number_format($cm_dzn_per/$m,4,'.','').'</td>
								<td>'. number_format($cm_dzn_inv_qty,4,'.','').'</td>
	
								<td>'. number_format($row_result[csf('total_carton_qnty')],2).'</td>
								<td>'. number_format($avg_price,2).'</td>
								<td>'. number_format($row_result[csf('invoice_value')],2,'.','').'</td>	                         
								<td>'. number_format($row_result[csf('discount_ammount')],2,'.','').'</td>
								<td>'. number_format($row_result[csf('atsite_discount_amt')],2,'.','').'</td>
								<td>'. $stamp_value_array[$row_result[csf('stamp_value')]].'</td>
								<td>'. number_format($row_result[csf('bonus_ammount')],2,'.','').'</td>
								<td>'. number_format($row_result[csf('claim_ammount')],2,'.','').'</td>
								<td>'. number_format($row_result[csf('commission')],2,'.','').'</td>
								<td>'. number_format($row_result[csf('other_discount_amt')],2,'.','').'</td>
								<td>'. number_format($row_result[csf('upcharge')],2,'.','').'</td>
								<td>'. number_format($row_result[csf('net_invo_value')],2,'.','').'</td>
								<td>'. $currency[$row_result[csf('currency_name')]].'</td>';
								if($row_result[csf('etd')]!="0000-00-00" && $row_result[csf('etd')]!="") $etd= change_date_format( $row_result[csf('etd')]); else $etd= "";
					  $html .= '<td>'.$etd.'</td>';
					           if($row_result[csf('ex_factory_date')]!="0000-00-00" && $row_result[csf('ex_factory_date')]!="") {$ex_factory_date= change_date_format($row_result[csf('ex_factory_date')]);} else {$ex_factory_date= "&nbsp;";}
					   $html .= '<td>'.$ex_factory_date.'</td>';
								if($row_result[csf('actual_shipment_date')]!="0000-00-00" && $row_result[csf('actual_shipment_date')]!="") {$actual_date= change_date_format($row_result[csf('actual_shipment_date')]);} else {$actual_date=  "&nbsp;";}
					  $html .= '<td>'.$actual_date.'</td>';
	
								if($possiable_bl_date!="0000-00-00" && $possiable_bl_date!="") {$p_bl_date= change_date_format($possiable_bl_date);} else {$p_bl_date= "&nbsp;";}
					  $html .= '<td>'.$p_bl_date.'</td>															
								<td>'. $row_result[csf('bl_no')].'</td>';
								if($row_result[csf('bl_date')]!="0000-00-00" && $row_result[csf('bl_date')]!="") {$bl_date= change_date_format($row_result[csf('bl_date')]);} else {$bl_date= "&nbsp;";}
					$html .= '<td>'.$bl_date.'</td>';
					$diff_bl=datediff("d",$exfact_date_calculate, $row_result[csf('bl_date')]); if($diff_bl>0) $diff_bls= $diff_bl."days";
					$html .= '<td>'.$diff_bls.'</td>';
								if($row_result[csf('bl_rev_date')]!="0000-00-00" && $row_result[csf('bl_rev_date')]!="") {$bl_rev_date= change_date_format($row_result[csf('bl_rev_date')]);} else { $bl_rev_date= "&nbsp;";}
					$html .= '<td>'.$bl_rev_date.'</td>
	
								<td>'. $row_result[csf('shipping_bill_n')].'</td>
	
								<td>'. change_date_format($row_result[csf('ship_bl_date')]).'</td>';
								if($possiable_gsp_date!="0000-00-00" && $possiable_gsp_date!="") {$possiable_gsp_dates= change_date_format($possiable_gsp_date);} else {$possiable_gsp_dates= "&nbsp;";}
					$html .= '<td>'.$possiable_gsp_dates.'</td>';
					   if(trim($row_result[csf('gsp_co_no_date')])!="0000-00-00" && trim($row_result[csf('gsp_co_no_date')])!="") {$gsp_co_no_date= change_date_format($row_result[csf('gsp_co_no_date')]);}else {$gsp_co_no_date= "&nbsp;";}
					 $html .= '<td>'.$gsp_co_no_date.'</td>
								<td>'. $row_result[csf('gsp_co_no')].'</td>';
								if($possiable_co_date!="0000-00-00" && $possiable_co_date!="") {$possiable_co_dates= change_date_format($possiable_co_date);} else {$possiable_co_dates= "&nbsp;";}
								
					$html .= '<td>'.$possiable_co_dates.'</td>';
					if($row_result[csf('co_date')]!="0000-00-00" && $row_result[csf('co_date')]!="") {$co_date= change_date_format($row_result[csf('co_date')]);} else {$co_date= "&nbsp;";}
					$html .= '<td>'.$co_date.'</td>';
								$curier_receipt_date=$bank_sub_data[$row_result[csf("id")]]["courier_date"];
								if(!(trim($curier_receipt_date)=="0000-00-00" || trim($curier_receipt_date)==""))
								{
									$curier_receipt_date= change_date_format($curier_receipt_date);
								}
								else
								{
									$curier_receipt_date= "&nbsp;";
								}
					$html .= '<td>'.$curier_receipt_date.'</td>';
					if($row_result[csf('ic_recieved_date')]!="0000-00-00" && $row_result[csf('ic_recieved_date')]!="") $ic_recieved_date= change_date_format($row_result[csf('ic_recieved_date')]); else $ic_recieved_date= "";
					$html .= '<td>'.$ic_recieved_date.'</td>';
								if(($exfact_date_calculate!='0000-00-00') )
								{
									$current_date=date("Y-m-d");
									if($bank_sub_data[$row_result[csf('id')]]["submit_date"]=='0000-00-00' || $bank_sub_data[$row_result[csf('id')]]["submit_date"]=='')
									{
										$diff=datediff("d",$exfact_date_calculate, $current_date);
									}
									else
									{
										$diff=datediff("d",$exfact_date_calculate, $bank_sub_data[$row_result[csf('id')]]["submit_date"]);
									}
	
								}
								else
								{
									$diff="";
								}
							   if($diff>0) $diffs=  $diff." days";
								

					 $html .= '<td>'.$diffs.'</td>';
					 if($possiable_buyer_sub_date!="0000-00-00" && $possiable_buyer_sub_date!="") {$poss_buyer_sub_date= change_date_format($possiable_buyer_sub_date);} else {$poss_buyer_sub_date= "&nbsp;";}
					 $html .= '<td>'.$poss_buyer_sub_date.'</td>';
					 if(trim($buyer_submit_date_arr[$row_result[csf('id')]])!='0000-00-00' && trim($buyer_submit_date_arr[$row_result[csf('id')]])!='') $buyer_sub= change_date_format(trim($buyer_submit_date_arr[$row_result[csf('id')]])); else $buyer_sub= "&nbsp;";
					 $html .= '<td>'.$buyer_sub.' </td>';
							   $diff_buyer_sub=0;
								if($row_result[csf('bl_date')]!="0000-00-00" && $row_result[csf('bl_date')]!="")
								{
									$diff_buyer_sub=datediff("d",$row_result[csf('bl_date')], $buyer_submit_date_arr[$row_result[csf('id')]]);
								}
								else
								{
									$diff_buyer_sub=0;
								}
								 if($diff_buyer_sub>0) $diff_buyer_subs= $diff_buyer_sub."days";

					  $html .= '<td> '.$diff_buyer_subs.'</td>';
					  if($possiable_bank_sub_date!="0000-00-00" && $possiable_bank_sub_date!="") {$bank_sub_date= change_date_format($possiable_bank_sub_date);} else {$bank_sub_date= "&nbsp;";}
					  $html .= '<td>'.$bank_sub_date.'</td>';
					  if(trim($bank_sub_data[$row_result[csf('id')]]["submit_date"])!='0000-00-00' && trim($bank_sub_data[$row_result[csf('id')]]["submit_date"])!='') $bank_sub_date= change_date_format(trim($bank_sub_data[$row_result[csf('id')]]["submit_date"])); else $bank_sub_date= "&nbsp;";
					  $html .= '<td>'.$bank_sub_date.' </td>';

					  			$diff_sub=0;
								if($row_result[csf('bl_date')]!="0000-00-00" && $row_result[csf('bl_date')]!="")
								{
									$diff_sub=datediff("d",$row_result[csf('bl_date')], $bank_sub_data[$row_result[csf('id')]]["submit_date"]);
								}
								else
								{
									$diff_sub=0;
								}
								 if($diff_sub>0) $diff_subs= $diff_sub."days";
					  $html .= '<td>'.$diff_subs.'</td>
								<td>'. $bank_sub_data[$row_result[csf('id')]]["bank_ref_no"].'</td>';
								
	
								if(!(trim($bank_sub_data[$row_result[csf('id')]]["submit_date"])=="0000-00-00" || trim($bank_sub_data[$row_result[csf('id')]]["submit_date"])==""))
								{
								   $submit_dates= change_date_format($bank_sub_data[$row_result[csf('id')]]["submit_date"]);
								}
								else
								{
									$submit_dates= "&nbsp;";
								}
					 $html .= '<td>'.$submit_dates.'</td>
	
								<td >'. $row_result[csf('shipping_bill_n')].'</td>';
								if($row_result[csf('ship_bl_date')]!="0000-00-00" && $row_result[csf('ship_bl_date')]!="") {$ship_bl_date= change_date_format($row_result[csf('ship_bl_date')]);} else {$ship_bl_date= "&nbsp;";}
					$html .= '<td>'.$ship_bl_date.'</td>
								<td>'. $row_result[csf('carton_gross_weight')].' </td>
								<td>'. $row_result[csf('carton_net_weight')].'</td>
								<td>'. $pay_term[$row_result[csf('pay_term')]].'</td>
								<td>'. $row_result[csf('tenor')].'</td>';
								if($cbo_based_on ==2)
								{
									$possiable_rls_date=add_date($row_result[csf('ex_factory_date')], $row_result[csf('tenor')]);
									$po_rls_date= change_date_format($possiable_rls_date);
								}
								else
								{
									if(!(trim($bank_sub_data[$row_result[csf('id')]]["possible_reali_date"])=="0000-00-00" || trim($bank_sub_data[$row_result[csf('id')]]["possible_reali_date"])==""))
									{
										$po_rls_date= change_date_format($bank_sub_data[$row_result[csf('id')]]["possible_reali_date"]);
									}
									else
									{
										$po_rls_date= "&nbsp;";
									}
								}
					$html .= '<td>'.$po_rls_date.'</td>';
								if(!(trim($rlz_date_arr[$row_result[csf('id')]])=="0000-00-00" || trim($rlz_date_arr[$row_result[csf('id')]])==""))
								{
									$rlz_date= change_date_format($rlz_date_arr[$row_result[csf('id')]]);
								}
								else
								{
									$rlz_date= "&nbsp;";
								}
					$html .= '<td>'.$rlz_date.'</td>';
					
								if( $realization_sub_day!="" && $realization_sub_day!='0000-00-00' &&$rlz_date_arr[$row_result[csf('id')]]!="" && $rlz_date_arr[$row_result[csf('id')]]!='0000-00-00')
								{
									$diff_rlz=datediff("d",$realization_sub_day,$rlz_date_arr[$row_result[csf('id')]]);
								}
								if($diff_rlz>0){ $diff_rlzs= $diff_rlz." days";}
                    $html .= '<td>'.$diff_rlzs.'</td>';
								if(!(trim($rlz_date_arr[$row_result[csf('id')]])=="0000-00-00" || trim($rlz_date_arr[$row_result[csf('id')]])==""))
								{
									$net_inv_value= number_format($row_result[csf('net_invo_value')],2,'.',''); $total_rlz_amt+=$row_result[csf('net_invo_value')];
								}
								else
								{
									$net_inv_value= "";
								}
					$html .= '<td>'.$net_inv_value.'</td>
								<td>'. $row_result[csf('remarks')].'</td>
								<td>'. $row_result[csf('feeder_vessel')].'</td>
								<td>'. $row_result[csf('mother_vessel')].'</td>';
								if($row_result[csf('etd_destination')]!="0000-00-00" && $row_result[csf('etd_destination')]!="") {$std_desdate= change_date_format($row_result[csf('etd_destination')]);} else { $std_desdate= "&nbsp;";}

					$html .= '<td>'.$std_desdate.'</td>
								<td >'. $bank_sub_data[$row_result[csf('id')]]["bnk_to_bnk_cour_no"].'</td>
							</tr>';
						
							$k++;
						}
	                }
	               
	               $html .= ' </tbody>
	            </table>
	     		<table>
	            	<tfoot>
	                    <tr>
	                        <th >&nbsp;</th>
	                        <th>&nbsp;</th>

	                        <th>&nbsp;</th>
							<th>&nbsp;</th>
	                        <th>&nbsp;</th>
	                        <th >&nbsp;</th>

	                        <th>&nbsp;</th>
	                        <th>&nbsp;</th>
	                        <th>&nbsp;</th>
	                        <th>&nbsp;</th>
	                        <th>&nbsp;</th>
	                        <th>&nbsp;</th>
	                        <th>&nbsp;</th>

	                        <th>&nbsp;</th>
	                        <th>&nbsp;</th>
	                        <th>&nbsp;</th>
	                        <th>Total:</th>
	                        <th>'. number_format($total_ex_fact_qnty,2,'.','').'</th>
	                        <th>'. number_format($total_invoice_qty,2,'.','').'</th>
							<th>'. number_format($total_invoice_qty_pcs,2,'.','').'</th>
	                        <th></th>
	                        <th></th>
	                        <th>'. number_format($total_cm_asper_invoice_qty,2,'.','').'</th>
	                        <th>'. number_format($total_carton_qty,2,'.','').'</th>
	                        <th>'. number_format($total_avg_price,2,'.','').'</th>
	                        <th>'. number_format($total_grs_value,2,'.','').'</th>
	                        <th>'. number_format($total_discount_value,2,'.','').'</th>
	                        <th>'.number_format($atsite_discount_amt,2,'.','').'</th>
	                        <th>&nbsp;</th>
	                        <th>'. number_format($total_bonous_value,2,'.','').'</th>
	                        <th>'. number_format($total_claim_value,2,'.','').'</th>
	                        <th>'. number_format($total_commission_value,2,'.','').'</th>
							
	                        <th>'. number_format($total_other_discount_value,2,'.','').'</th>
	                        <th>'. number_format($total_upcharge_value,2,'.','').'</th>
	                        <th>'. number_format($total_order_qnty,2,'.','').'</th>
	                        <th></th>
	                        <th></th>
	                        <th></th>
	                        <th></th>
	                        <th></th>	            
	                        <th></th>
	                        <th></th>
	                        <th></th>
	                        <th></th>
	                        <th></th>
	                        <th></th>
	                        <th></th>
	                        <th></th>
	                        <th></th>
	                        <th></th>
	                        <th></th>
	                        <th></th>
	                        <th></th>
	                        <th></th>
	                        <th></th>
	                        <th></th>
	                        <th></th>
	                        <th></th>
	                        <th></th>
	                        <th></th>
	                        <th></th>
	                        <th></th>
	                        <th></th>
	                        <th></th>
	                        <th>'. number_format($total_carton_gross_weight,2,'.','').'</th>
	                        <th>'. number_format($total_carton_net_weight,2,'.','').'</th>
	                        <th width="80"></th>
	                        <th width="80"></th>
	                        <th></th>
	                        <th></th>
	                        <th></th>
	                        <th>'.number_format($total_rlz_amt,2,'.','').'</th>
	                        <th></th>
	                        <th></th>
	                        <th></th>
	                        <th></th>
	                        <th ></th>
	                    </tr>
	                </tfoot>
	            </table>
     		</div>';

 $html .= '</div>';
 $html .= '<div>'. "User Id : ". $user_arr[$user_id] ." , &nbsp; THIS IS SYSTEM GENERATED STATEMENT, NO SIGNATURE REQUIRED .".'</div>';
				
		foreach (glob("bwffsr_$user_id*.xlsx") as $filename) {
			@unlink($filename);
		}
		$name=time();
		$filename='bwffsr_'.$user_id."_".$name.".xlsx";
		// echo $filename;die;
		
		$reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
		$spreadsheet = $reader->loadFromString($html);
	
		$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
		$writer->save($filename); 
	
		echo "$filename####$filename####$return_item_cat####$rptType";
	}
	exit();
}

if($action=="show_file")
{
	echo load_html_head_contents("File","../../../", 1, 1, $unicode);
    extract($_REQUEST);
   // echo "select image_location  from common_photo_library  where master_tble_id='$invoice_id' and form_name='export_invoice' and is_deleted=0 and file_type=2";
	$data_array=sql_select("select image_location ,real_file_name from common_photo_library  where master_tble_id='$invoice_id' and form_name='export_invoice' and is_deleted=0 and file_type=2");
	?>
    <table>
        <tr>
        <?
        foreach ($data_array as $row)
        { 
        	$image_location=explode(".",$row[csf('image_location')]);
        	$file_name=$row[csf('real_file_name')];
	        ?>
	        <td><a style="display: block; overflow: hidden; width: 80px; float: left;text-align: center;" href="../../../<? echo $row[csf('image_location')]; ?>" target="_new"> 
	        	<?
	        	//echo $image_location[0];

	        		if($image_location[1]=='xls')
	        		{
		        		?>
		        		<img src="../../../file_upload/Excel-icon.png" width="80" height="60"><br> <? echo $file_name; ?> </a>
		        		<?
	        		}
	        		else if($image_location[1]=='doc' || $image_location[1]=='docx')
	        		{
		        		?>
		        		<img src="../../../file_upload/docx-icon.png" width="80" height="60"><br> <? echo $file_name; ?> </a>
		        		<?
	        		}
	        		else
	        		{
		        		?>
		        		<img src="../../../file_upload/blank_file.png" width="80" height="60"><br> <? echo $file_name; ?>  </a>
		        		<?
	        		}
	        	?>
	        </td>
	        <?
        }
        ?>
        </tr>
    </table>
    <?
}

if($action=="po_id_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$invoice_id=str_replace("'","",$invoice_id);
	//print_r($po_id);die;
	?>

	<div style="width:970px">
	<fieldset style="width:100%"  >
	    <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="1070">
	        <thead>
	            <th width="120">Order NO</th>
	            <th width="120">Style No</th>
				<th width="100">Job No</th>
	            <th width="80">Ship Date</th>
	            <th width="80">Order Qty (Pcs)</th>
	            <th width="70">Unit Price (P.O)</th>
	            <th width="80">Order Value ($)</th>
	            <th width="80">Attach Qnty (Pcs)</th>
	            <th width="70">Unit Price (LC/SC)</th>
	            <th width="80">Attach Value ($)</th>
	            <th width="80">Invoice Qnty (Pcs)</th>
	            <th width="70">Unit Price (Invoice)</th>
	            <th width="80">Invoice Value ($)</th>
	            <th width="80">Unit Price Diff. (LC-Inv)</th>
	        </thead>
	        <tbody>
			<?
			$lc_attach_sql=sql_select("select b.com_export_lc_id as lc_sc_id, b.wo_po_break_down_id, b.attached_qnty, b.attached_rate, b.attached_value, 1 as type from com_export_lc_order_info b where b.status_active=1 and b.is_deleted=0
			union all
			select b.com_sales_contract_id as lc_sc_id, b.wo_po_break_down_id, b.attached_qnty, b.attached_rate, b.attached_value, 2 as type from  com_sales_contract_order_info b where b.status_active=1 and b.is_deleted=0");
			$lc_sc_qnty_arr=array();
			foreach($lc_attach_sql as $row)
			{
				$lc_sc_qnty_arr[$row[csf("lc_sc_id")]][$row[csf("type")]][$row[csf("wo_po_break_down_id")]]["attached_qnty"]=$row[csf("attached_qnty")];
				$lc_sc_qnty_arr[$row[csf("lc_sc_id")]][$row[csf("type")]][$row[csf("wo_po_break_down_id")]]["attached_rate"]=$row[csf("attached_rate")];
				$lc_sc_qnty_arr[$row[csf("lc_sc_id")]][$row[csf("type")]][$row[csf("wo_po_break_down_id")]]["attached_value"]=$row[csf("attached_value")];
			}
			$sql="select a.lc_sc_id, a.is_lc, b.current_invoice_qnty, b.current_invoice_rate, b.current_invoice_value, c.id as po_id, c.po_number, c.pub_shipment_date, c.po_quantity, c.unit_price, c.po_total_price,d.style_ref_no,c.job_no_mst from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b, wo_po_break_down c ,wo_po_details_master d where a.id=b.mst_id and b.po_breakdown_id=c.id and  c.job_no_mst = d.job_no  and a.id='$invoice_id' and b.current_invoice_qnty>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			//echo $sql;

			/*if($db_type==0)
			{
				$company_sql="select c.invoice_no, b.po_number, b.po_quantity, a.po_breakdown_id, a.current_invoice_qnty from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b, wo_po_break_down c where a.po_breakdown_id=b.id and a.mst_id=c.id and c.id='$po_id' and a.current_invoice_qnty not in(0) and a.status_active=1 and a.is_deleted=0";
			}
			else if($db_type==2)
			{
				$company_sql="select c.invoice_no, b.po_number, b.po_quantity, a.po_breakdown_id, a.current_invoice_qnty from com_export_invoice_ship_dtls a, wo_po_break_down b, com_export_invoice_ship_mst c where a.po_breakdown_id=b.id and a.mst_id=c.id and c.id='$po_id' and a.current_invoice_qnty not in(0) and a.status_active=1 and a.is_deleted=0";
			}*/

			//echo $company_sql;die;
	        //$sql_re=sql_select($sql);
			$i=$k+1;
	        $sql_re=sql_select($sql);
	        $total_invoice_qty=0;  $total_order_qty=0;  $total_attach_qty=0;
	        $result=0;
	        foreach($sql_re as $row)
	        {
	        ?>

	            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                <td><? echo $row[csf('po_number')]; ?>&nbsp;</td>
	                <td><? echo $row[csf('style_ref_no')]; ?>&nbsp;</td>
					<td><? echo $row['JOB_NO_MST'] ?>&nbsp;</td>
	                <td align="center"><? if($row[csf('pub_shipment_date')]!="" && $row[csf('pub_shipment_date')]!="0000-00-00") echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
	                <td align="right"><?  echo number_format($row[csf('po_quantity')],0);  $total_order_qty +=$row[csf('po_quantity')]; ?> </td>
	                <td align="right"><?  echo number_format($row[csf('unit_price')],4);  ?> </td>
	                <td align="right"><?  echo number_format($row[csf('po_total_price')],2); $total_order_value+=$row[csf('po_total_price')]; ?></td>

	                <td align="right"><?  echo number_format($lc_sc_qnty_arr[$row[csf("lc_sc_id")]][$row[csf("is_lc")]][$row[csf("po_id")]]["attached_qnty"],0);  $total_lc_qty +=$lc_sc_qnty_arr[$row[csf("lc_sc_id")]][$row[csf("is_lc")]][$row[csf("po_id")]]["attached_qnty"]; ?> </td>
	                <td align="right"><?  echo number_format($lc_sc_qnty_arr[$row[csf("lc_sc_id")]][$row[csf("is_lc")]][$row[csf("po_id")]]["attached_rate"],4);  ?> </td>
	                <td align="right"><?  echo number_format($lc_sc_qnty_arr[$row[csf("lc_sc_id")]][$row[csf("is_lc")]][$row[csf("po_id")]]["attached_value"],2); $total_lc_value+=$lc_sc_qnty_arr[$row[csf("lc_sc_id")]][$row[csf("is_lc")]][$row[csf("po_id")]]["attached_value"]; ?></td>
	                <td align="right"><?  echo number_format($row[csf('current_invoice_qnty')],0);  $total_invoice_qty +=$row[csf('current_invoice_qnty')]; ?> </td>
	                <td align="right"><?  echo number_format($row[csf('current_invoice_rate')],4);  ?> </td>
	                <td align="right"><?  echo number_format($row[csf('current_invoice_value')],2); $total_invoice_value+=$row[csf('current_invoice_value')]; ?></td>
	            	<td align="right" title="LC Rate-Invoice Rate"><? $rate_dev=$lc_sc_qnty_arr[$row[csf("lc_sc_id")]][$row[csf("is_lc")]][$row[csf("po_id")]]["attached_rate"]-$row[csf('current_invoice_rate')];  echo number_format($rate_dev,4);  ?> </td>
	            </tr>
			<?
			$i++;
	        }
	        ?>
	        </tbody>
	        <tfoot>
	            <tr >
	                <th align="right">&nbsp;</th>
	                <th align="right">&nbsp;</th>
					<th align="right">&nbsp;</th>
	                <th align="right" >Total</th>
	                <th align="right"><? echo number_format($total_order_qty,0); ?></th>
	                <th align="right">&nbsp;</th>
	                <th align="right"><? echo number_format($total_order_value,2); ?></th>
	                <th align="right"><? echo number_format($total_lc_qty,0); ?></th>
	                <th align="right">&nbsp;</th>
	                <th align="right"><? echo number_format($total_lc_value,2); ?></th>
	                <th align="right"><? echo number_format($total_invoice_qty,0); ?></th>
	                <th align="right">&nbsp;</th>
	                <th align="right"><? echo number_format($total_invoice_value,2); ?></th>
	                <th align="right">&nbsp;</th>
	            </tr>
	        </tfoot>
	    </table>
	</fieldset>
	</div>
	<?
	exit();
}

if($action=="po_id_details_cm")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$type=str_replace("'","",$type);
	$po_number_arr=return_library_array("select id, po_number from wo_po_break_down","id","po_number");
	$data=explode(',',$data);
	?>
		<div style="width:340px">
		<fieldset style="width:100%"  >
			<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="330">
				<thead>
					<th width="50">SL NO</th>
					<th width="150">PO Number</th>
					<th width="120">
						<? 
							if($type==1)
							{
								echo "CM/Dzn";
							}
							else if($type==2)
							{
								echo "CM Value Per Pcs";
							}
							else
							{
								echo "Total CM as per Invoice Qty";
							}
						?>
					</th>
				</thead>
				<tbody>
				<?
				$i=1;
				$total_invoice_qty=0;  $total_order_qty=0;  $total_attach_qty=0; 
				$result=0;
				foreach($data as $val)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$val=explode('**',$val);
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td align="center"><? echo $i; ?>&nbsp;</td>
						<td><? echo $po_number_arr[$val[0]]; ?>&nbsp;</td>
						<td align="right"><?  echo number_format($val[1],4); ?> </td>
					</tr>
				<?
				$i++;
				}
				?>
				</tbody>
			</table>
		</fieldset>
		</div>
	<?

	exit();
}
//Ex-Factory Delv. and Return
if($action=="ex_factory_popup")
{
 	echo load_html_head_contents("Ex-Factory Details", "../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	//echo $id;//$job_no;
	?>
	<div style="width:100%" align="center">
		<fieldset style="width:650px">
        <div class="form_caption" align="center"><strong>Ex-Factory Details</strong></div><br />
            <div style="width:100%">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="35">SL</th>
                        <th width="80">Ex-fac. Date</th>
                        <th width="110">System /Challan no</th>
                        <th width="90">Ex-Fact. Del.Qty.</th>
						<th width="80">Return Date</th>
						<th width="110">Return Challan No</th>
                        <th width="">Ex-Fact.Return Qty.</th>
                     </tr>
                </thead>
            </table>
        </div>
        <div style="width:100%; max-height:400px;">
            <table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
                <?
                $i=1;				

				// $exfac_sql=("SELECT b.challan_no, a.sys_number, b.ex_factory_date, CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_qnty
				// from  pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.invoice_no in($id) ");
				// //echo $exfac_sql;
                // $sql_dtls=sql_select($exfac_sql);

				// foreach ($sql_dtls as $row){
				// 	$challan_no.="'".$row[csf("sys_number")]."',";
				// }
				// $challan_nos=rtrim($challan_no,',');
				// if ($challan_nos != "")
				// {					
				//     // $exfac_return_sql=("SELECT b.challan_no,a.sys_number,b.ex_factory_date, CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_return_qnty
				// 	// from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b, com_export_invoice_ship_mst c  where a.id=b.delivery_mst_id and b.invoice_no=c.id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.id in($id) and  b.challan_no in($challan_nos)");
					
				// 	$exfac_return_sql=("SELECT b.challan_no,a.sys_number,b.ex_factory_date, CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_return_qnty
				// 	from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b 
				// 	where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  b.challan_no in($challan_nos)");
					
				// 	//echo $exfac_return_sql;
					
				// 	$exfac_return_sql_res=sql_select($exfac_return_sql);
				// 	$return_qty_arr=array();
				// 	foreach ($exfac_return_sql_res as $row){
				// 		$return_qty_arr[$row[csf("challan_no")]]['return_qty']+=$row[csf("ex_factory_return_qnty")];
				// 		$return_qty_arr[$row[csf("challan_no")]]['return_no']=$row[csf("sys_number")];
				// 		$return_qty_arr[$row[csf("challan_no")]]['return_date']=$row[csf("ex_factory_date")];
				// 	}
				// }


				$exfac_sql=("SELECT B.CHALLAN_NO, b.INVOICE_NO,A.SYS_NUMBER, B.EX_FACTORY_DATE, sum(CASE WHEN B.ENTRY_FORM!=85 THEN B.EX_FACTORY_QNTY ELSE 0 END) AS EX_FACTORY_QNTY ,B.PO_BREAK_DOWN_ID, B.COUNTRY_ID, B.ITEM_NUMBER_ID
				FROM  PRO_EX_FACTORY_DELIVERY_MST A, PRO_EX_FACTORY_MST B WHERE A.ID=B.DELIVERY_MST_ID AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND B.INVOICE_NO IN($id) and B.ENTRY_FORM!=85 group by B.CHALLAN_NO, b.INVOICE_NO,A.SYS_NUMBER, B.EX_FACTORY_DATE, B.PO_BREAK_DOWN_ID, B.COUNTRY_ID, B.ITEM_NUMBER_ID");
				//echo $exfac_sql;
                $sql_dtls=sql_select($exfac_sql);
				$exfact_sys_arr=array();
				foreach ($sql_dtls as $row){
					$challan_no.="'".$row[csf("sys_number")]."',";

					$seq_grouping = $row['PO_BREAK_DOWN_ID']."*".$row['ITEM_NUMBER_ID']."*".$row['COUNTRY_ID']."*".$row['SYS_NUMBER']."*".$row['INVOICE_NO'];
					$exfact_sys_arr[$seq_grouping]=$row["SYS_NUMBER"];
				}
				$challan_nos=rtrim($challan_no,',');
				if ($challan_nos != "")
				{					
			
					$exfac_return_sql=("SELECT B.CHALLAN_NO,A.SYS_NUMBER,B.EX_FACTORY_DATE,b.INVOICE_NO, CASE WHEN B.ENTRY_FORM=85 THEN B.EX_FACTORY_QNTY ELSE 0 END AS EX_FACTORY_RETURN_QNTY,B.PO_BREAK_DOWN_ID, B.COUNTRY_ID, B.ITEM_NUMBER_ID
					FROM  PRO_EX_FACTORY_DELIVERY_MST A,  PRO_EX_FACTORY_MST B 
					WHERE A.ID=B.DELIVERY_MST_ID AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND  B.CHALLAN_NO IN($challan_nos)");
					
					//echo $exfac_return_sql;
					
					$exfac_return_sql_res=sql_select($exfac_return_sql);
					$return_qty_arr=array();
					foreach ($exfac_return_sql_res as $row){
						$seq_grouping = $row['PO_BREAK_DOWN_ID']."*".$row['ITEM_NUMBER_ID']."*".$row['COUNTRY_ID']."*".$row['CHALLAN_NO']."*".$row['INVOICE_NO'];
						$return_qty_arr[$exfact_sys_arr[$seq_grouping]]['return_qty']+=$row[csf("ex_factory_return_qnty")];
						$return_qty_arr[$exfact_sys_arr[$seq_grouping]]['return_no']=$row[csf("sys_number")];
						$return_qty_arr[$exfact_sys_arr[$seq_grouping]]['return_date']=$row[csf("ex_factory_date")];

						//$return_qty_arr[$exfact_sys_arr[$seq_grouping]]+=$row["EX_FACTORY_RETURN_QNTY"];
					}
					//echo "<pre>"; print_r($return_qty_arr);
				}

                foreach($sql_dtls as $row_real)
                {
                    if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="35"><? echo $i; ?></td>
                        <td width="80"><? echo change_date_format($row_real[csf("ex_factory_date")]); ?></td>
                        <td width="110"><? echo $row_real[csf("sys_number")]; ?></td>
                        <td width="90" align="right"><? echo $row_real[csf("ex_factory_qnty")]; ?></td>
						<td width="80" align="center"><? echo change_date_format($return_qty_arr[$row_real[csf("sys_number")]]['return_date']); ?></td>
						<td width="110" align="right"><? echo $return_qty_arr[$row_real[csf("sys_number")]]['return_no']; ?></td>
                        <td width="" align="right"><? echo $return_qty_arr[$row_real[csf("sys_number")]]['return_qty']; ?></td>
                    </tr>
                    <?
                    $rec_qnty+=$row_real[csf("ex_factory_qnty")];
					$rec_return_qnty+=$return_qty_arr[$row_real[csf("sys_number")]]['return_qty'];
                    $i++;
                }
                ?>
                <tfoot>
                <tr>
                    <th colspan="3">Total</th>
                    <th><? echo number_format($rec_qnty,2); ?></th>
					<th colspan="2">&nbsp;</th>
                    <th><? echo number_format($rec_return_qnty,2); ?></th>
                </tr>
                <tr>
                 <th colspan="3">Total Balance</th>
                 <th colspan="4" align="right"><? echo number_format($rec_qnty-$rec_return_qnty,2); ?></th>
                </tr>
                </tfoot>
            </table>
        </div>
		</fieldset>
	</div>
	<?
    exit();
}

if($action=="invoice_popup_search")
{
	echo load_html_head_contents("Invoice No PopUp Search", "../../../", 1, 1,'','1','');
	extract($_REQUEST);

	$cbo_company_name = str_replace("'","",$cbo_company_name);
	?>

	<script>

		function js_set_value(data)
		{
			var data_string=data.split('_');
			$('#txt_invoice_no').val(data_string[0]);
			$('#cbo_buyer_name').val(data_string[1]);
			parent.emailwindow.hide();
		}

    </script>

	</head>

	<body>
	<div align="center" style="width:900px;">
		<form name="searchexportinformationfrm"  id="searchexportinformationfrm">
			<fieldset style="width:880px;">
			<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="860" class="rpt_table" border="1" rules="all">
				<input type="hidden" name="txt_invoice_no" id="txt_invoice_no" value="" />
				<input type="hidden" name="cbo_buyer_name" id="cbo_buyer_name" value="" />
					<thead>
						<tr>
                            <th colspan="4"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --",1 ); ?></th>
                            <th colspan="2"><input type="checkbox" name="with_value" id="with_value" /> Load PO with only value</th>
                        </tr>
						<tr>
							<th>Company</th>
							<th>Buyer</th>
							<th>Search By</th>
							<th>Invoice Date Range</th>
							<th>Enter Invoice No</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
							</th>
						</tr>
					</thead>
					<tr class="general">
						<td>
							<?
								echo create_drop_down( "cbo_company_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", $cbo_company_name, "--- Select Company ---", 0, "load_drop_down( 'export_information_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td_id' );",1 );
							?>
						</td>
						<td id="buyer_td_id">
							<?
							echo create_drop_down("cbo_buyer_name", 162, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name", "id,buyer_name", 1, "--- Select Buyer ---", $selected, "");
							?>
						</td>
						<td>
							<?
								$arr=array(1=>'Invoice NO');
								echo create_drop_down( "cbo_search_by", 100, $arr,"", 0, "", 0, "" );
							?>
						</td>
						<td>
							<input type="text" name="invoice_start_date" id="invoice_start_date" class="datepicker" style="width:70px;" />To
                            <input type="text" name="invoice_end_date" id="invoice_end_date" class="datepicker" style="width:70px;" />
						</td>
						<td>
							<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td>
							<input type="button" id="search_button" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('invoice_start_date').value+'**'+document.getElementById('invoice_end_date').value+'**'+document.getElementById('cbo_string_search_type').value,'invoice_search_list_view', 'search_div', 'export_ci_statement_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
						</td>
					</tr> 
				</table>
				<div style="width:100%; margin-top:10px" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}


if($action==='invoice_search_list_view')
{
	list($company_id, $buyer_id, $search_by, $invoice_num, $invoice_start_date, $invoice_end_date, $search_string) = explode('**', $data);

	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']['data_level_secured']==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!='') $buyer_id_cond=" and buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond='';
		}
	}
	else
	{
		$buyer_id_cond=" and buyer_id=$buyer_id";
	}

	$search_text=''; $company_cond ='';
	if($company_id !=0) $company_cond = "and benificiary_id=$company_id";

	if ($invoice_num != '')
	{
		if($search_string==1)
			$search_text="and invoice_no like '".trim($invoice_num)."'";
		else if ($search_string==2) 
			$search_text="and invoice_no like '".trim($invoice_num)."%'";
		else if ($search_string==3)
			$search_text="and invoice_no like '%".trim($invoice_num)."'";
		else if ($search_string==4 || $search_string==0)
			$search_text="and invoice_no like '%".trim($invoice_num)."%'";
	}

	if ($invoice_start_date != '' && $invoice_end_date != '') 
	{
        if ($db_type == 0) {
            $date_cond = "and invoice_date between '" . change_date_format($invoice_start_date, 'yyyy-mm-dd') . "' and '" . change_date_format($invoice_end_date, 'yyyy-mm-dd') . "'";
        } else if ($db_type == 2) {
            $date_cond = "and invoice_date between '" . change_date_format($invoice_start_date, '', '', 1) . "' and '" . change_date_format($invoice_end_date, '', '', 1) . "'";
        }
    } 
    else 
    {
        $date_cond = '';
    }

    $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');

	$sql = "select id, benificiary_id, buyer_id, invoice_no, invoice_date, is_lc, lc_sc_id, invoice_value, net_invo_value, import_btb, is_posted_account from com_export_invoice_ship_mst where status_active=1 and is_deleted=0 $company_cond $search_text $buyer_id_cond $date_cond order by invoice_date desc";
	$data_array=sql_select($sql);		

	$lc_arr=return_library_array( "select id, export_lc_no from com_export_lc",'id','export_lc_no');
	$sc_arr=return_library_array( "select id, contract_no from com_sales_contract",'id','contract_no');

	?>
	<table width="880" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
        <thead>
            <th width="40">SL</th>
            <th width="100">Company</th>
            <th width="100">Buyer</th>
            <th width="150">Invoice No</th>
            <th width="100">Invoice Date</th>
            <th width="150">LC/SC No</th>
            <th width="100">LC/SC</th>
            <th>Net Invoice Value</th>
        </thead>
     </table>
     <div style="width:900px; overflow-y:scroll; max-height:280px">
     	<table width="880" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="list_view">
		<?			
            $i = 1;
            foreach($data_array as $row)
            {
                if ($i%2==0)
                    $bgcolor="#FFFFFF";
                else
                    $bgcolor="#E9F3FF";

				if($row[csf('is_lc')]==1)
				{
					$lc_sc_no=$lc_arr[$row[csf('lc_sc_id')]];
					$is_lc_sc='LC';
				}
				else
				{
					$lc_sc_no=$sc_arr[$row[csf('lc_sc_id')]];
					$is_lc_sc='SC';
				}

				if($row[csf('import_btb')]==1) $buyer=$comp_arr[$row[csf('buyer_id')]]; else $buyer=$buyer_arr[$row[csf('buyer_id')]];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer;" id="search<? echo $i;?>" onClick="js_set_value( '<? echo $row[csf('invoice_no')]; ?>_<? echo $row[csf('buyer_id')]; ?>');" >                	
					<td width="40"><? echo $i; ?></td>
					<td width="100"><p><? echo $comp_arr[$row[csf('benificiary_id')]]; ?></p></td>
					<td width="100"><p><? echo $buyer; ?></p></td>
                    <td width="150"><p><? echo $row[csf('invoice_no')]; ?></p></td>
					<td width="100" align="center"><p><? echo change_date_format($row[csf('invoice_date')]); ?></td>
                    <td width="150"><p><? echo $lc_sc_no; ?></p></td>
                    <td width="100" align="center"><p><? echo $is_lc_sc; ?></p></td>
					<td align="right"><p><?
					echo number_format($row[csf('net_invo_value')],2);
					//echo number_format($row[csf('invoice_value')],2); ?></p></td>
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

disconnect($con);
?>
<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

include ("../../../../ext_resource/excel/excel/vendor/autoload.php");
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

//echo test;die;

if($action=="company_wise_report_button_setting")
{
	extract($_REQUEST);

	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=6 and report_id=143 and is_deleted=0 and status_active=1");
	//echo $print_report_format; disconnect($con); die;
	
	$print_report_format_arr=explode(",",$print_report_format);
	//print_r($print_report_format_arr);
	echo "$('#search1').hide();\n";
	echo "$('#search2').hide();\n";
	echo "$('#search3').hide();\n";
	echo "$('#search4').hide();\n";
	echo "$('#search5').hide();\n";
	echo "$('#search6').hide();\n";
	echo "$('#search7').hide();\n";
	echo "$('#search8').hide();\n";
	echo "$('#search9').hide();\n";
	

	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			
			if($id==725){echo "$('#search1').show();\n";}
			if($id==726){echo "$('#search2').show();\n";}
			if($id==727){echo "$('#search3').show();\n";}
			if($id==728){echo "$('#search4').show();\n";}
			if($id==729){echo "$('#search5').show();\n";}
			if($id==733){echo "$('#search6').show();\n";}
			if($id==740){echo "$('#search7').show();\n";}
			if($id==741){echo "$('#search8').show();\n";}
			if($id==742){echo "$('#search9').show();\n";}
			
			
		}
	}
	exit();	
}


if ($action=="load_drop_down_supplier")
{
	echo create_drop_down( "cbo_supplier_name", 100, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_tag_company b where a.status_active =1 and a.is_deleted=0 and a.id=b.supplier_id and b.tag_company='$data' and a.id in (select supplier_id from  lib_supplier_party_type where party_type in (1,3,21,90)) order by supplier_name","id,supplier_name", 1, "-- All Supplier --", $selected, "" ,0);
	exit();
}


if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 100, "select a.id, a.store_name from lib_store_location a,lib_store_location_category b  where a.id=b.store_location_id and a.company_id='$data' and b.category_type in(5,6,7,23) and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", 0, "" );
	exit();
	//select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data[0] and b.category_type=$data[1] order by a.store_name
}


//--------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------

//$yarn_requisition_arr=return_library_array( "select id, requisition_no from  ppl_yarn_requisition_entry",'id','requisition_no');
//$yarn_booking_arr=return_library_array( "select id, booking_no from  wo_booking_mst",'id','booking_no');
/*$composition_arr=array();
$construction_arr=array();
$sql_deter="select a.id as ID, a.construction as CONSTRUCTION, b.copmposition_id as COPMPOSITION_ID, b.percent as PERCENT from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
$data_array=sql_select($sql_deter);
if(count($data_array)>0)
{
	foreach( $data_array as $row )
	{
		if(array_key_exists($row["ID"],$construction_arr))
		{
			$construction_arr[$row["ID"]]=$construction_arr[$row["ID"]];
		}
		else
		{
			$construction_arr[$row["ID"]]=$row["CONSTRUCTION"];

		}
		if(array_key_exists($row["ID"],$composition_arr))
		{
			$composition_arr[$row["ID"]]=$composition_arr[$row["ID"]];
		}
		else
		{
			$composition_arr[$row["ID"]]=$composition[$row["COPMPOSITION_ID"]]." ".$row["PERCENT"]."%";
		}
	}
}

unset($data_array);*/

$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
//$buyer_name_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
//$buyer_short_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
//$color_arr=return_library_array( "select id, color_name from  lib_color",'id','color_name');
//$supplier_arr=return_library_array( "select id, short_name from  lib_supplier",'id','short_name');
$user_name_arr=return_library_array( "select id, user_name from  user_passwd",'id','user_name');
$loan_party_arr=return_library_array( "select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0 and id in(select supplier_id from lib_supplier_party_type where party_type=91) order by supplier_name",'id','supplier_name');



//report generated here--------------------//
if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_item_cat=str_replace("'","",$cbo_item_cat);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$rptType=str_replace("'","",$rptType);
	$cbo_based_on=str_replace("'","",$cbo_based_on);
	$cbo_purpose=str_replace("'","",$cbo_purpose);
	$cbo_supplier_name=str_replace("'","",$cbo_supplier_name);
	$cbo_store_name=str_replace("'","",$cbo_store_name);
	$txt_item_des=str_replace("'","",$txt_item_des);
	$cbo_uom=str_replace("'","",$cbo_uom);
	$account_posted_arr = array(  0=>"No",1=>"Yes" );

	if($db_type==0)
	{
		if($cbo_based_on==1)
		{
			if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.transaction_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' "; else $date_cond="";
		}
		else
		{
			if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.insert_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."  00:00:01' and '".change_date_format($txt_date_to,"yyyy-mm-dd")." 23:59:59' "; else $date_cond="";
		}
		$select_insert_date="DATE_FORMAT(a.insert_date,'%d-%m-%Y') as INSERT_DATE";
		$select_insert_time="DATE_FORMAT(a.insert_date,'%H:%i:%S') as INSERT_TIME";
		$insert_date_group="DATE_FORMAT(a.insert_date,'%d-%m-%Y')";
		$insert_time_group="DATE_FORMAT(a.insert_date,'%H:%i:%S')";
		//
		$select_recepi_id="group_concat(d.recipe_id)  as recipe_id";
		$select_po_id="group_concat(e.po_id)  as po_id";
	}
	else
	{
		if($cbo_based_on==1)
		{
			if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.transaction_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' "; else $date_cond="";
		}
		else
		{
			if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.insert_date between '".date("j-M-Y",strtotime($txt_date_from))."   01:00:01 AM' and '".date("j-M-Y",strtotime($txt_date_to))."  11:59:59 PM' "; else $date_cond="";
		}
		$select_insert_date=" to_char(a.insert_date,'DD-MON-YYYY') as INSERT_DATE";//HH24:MI:SS
		$select_insert_time=" to_char(a.insert_date,'HH24:MI:SS') as INSERT_TIME";
		$insert_date_group="to_char(a.insert_date,'DD-MON-YYYY')";
		$insert_time_group="to_char(a.insert_date,'HH24:MI:SS')";
		$select_recepi_id="listagg(cast(d.recipe_id as varchar(4000)),',') within group (order by d.recipe_id) as recipe_id";
		$select_po_id="listagg(cast(e.po_id as varchar(4000)),',') within group (order by e.po_id) as po_id";
	}

	$supp_cond=$trans_cond="";
	if($cbo_item_cat!=0) $item_cond=" and a.item_category=$cbo_item_cat"; else $item_cond=" and a.item_category in(5,6,7,23)";
	if($txt_item_des!='') { $item_cond .=" and b.item_description like '%$txt_item_des%'";}
	if($cbo_supplier_name>0)
	{
		$item_cond.=" and a.supplier_id=$cbo_supplier_name";
		$trans_cond=" and c.id=0"; // avoid transfer information
	}

	
	if($cbo_store_name>0)
	{
		$item_cond.=" and a.store_id=$cbo_store_name";
	}

	if($cbo_uom!='' && $cbo_uom!=0)
	{
		$item_cond.=" and a.cons_uom=$cbo_uom";
	}
	
	if($cbo_purpose!="" && $cbo_purpose!=0)
	{
		$purpose_cond=" and c.issue_purpose=$cbo_purpose";
		$purpose_cond1=" and c.receive_purpose=$cbo_purpose";
	}

	if($cbo_purpose ==5 && $rptType==3) // if purpose=loan and button issue
	{
		$stop_cond = "and a.id=''";
	}

	if($rptType==1)
	{
		$all_data_cond = '';

        if($cbo_purpose!="" && $cbo_purpose!=0){
            $all_data_cond .=" and receive_issue_purpose=$cbo_purpose";
        }
        if($cbo_uom!='' && $cbo_uom!=0){
            $all_data_cond .=" and cons_uom=$cbo_uom";
        }
        if($cbo_item_cat!=0) $all_data_cond .=" and item_category=$cbo_item_cat"; else $all_data_cond .=" and item_category in(5,6,7,23)";
        if($txt_item_des!='') {
            $all_data_cond .=" and item_description like '%$txt_item_des%'";
        }
        if($cbo_supplier_name>0){
            $all_data_cond .=" and supplier_id=$cbo_supplier_name";
        }

        if($cbo_store_name>0){
            $all_data_cond .=" and store_id=$cbo_store_name";
        }


        $sql="select
				a.id as TRANS_ID,
				a.store_id as STORE_ID,
				a.transaction_type as TRANSACTION_TYPE,
				a.mst_id as REC_ISSUE_ID,
				a.transaction_date as TRANSACTION_DATE,
				a.item_category as ITEM_CATEGORY,
				a.cons_quantity as RECEIVE_QTY,
				0 as ISSUE_QTY,
				a.cons_uom as CONS_UOM,
				a.remarks as REMARKS,
				a.batch_lot as BATCH_LOT,
				b.id as PROD_ID,
				b.item_group_id as ITEM_GROUP_ID,
				b.sub_group_name as SUB_GROUP_NAME,
				b.item_description as ITEM_DESCRIPTION,
				b.item_code as ITEM_CODE,
				b.is_compliance as IS_COMPLIANCE,
				a.cons_rate as CONS_RATE,
				a.cons_amount as CONS_AMOUNT,
				a.inserted_by as INSERTED_BY,
				$select_insert_date,
				$select_insert_time,
				a.order_qnty as ORDER_QNTY,
				a.order_rate as ORDER_RATE,
				c.recv_number as RECV_ISSUE_NUM,
				c.booking_id as BOOKING_ID,
				c.booking_no as BOOKING_NO,
				c.challan_no as CHALLAN_NO,
				c.knitting_source as KNITTING_SOURCE,
				c.knitting_company as KNITTING_COMPANY,
				c.receive_basis as RECEIVE_ISSUE_BASIS,
				c.receive_purpose as RECEIVE_ISSUE_PURPOSE,
				c.supplier_id as SUPPLIER_ID,
				null as REQ_NO,
				null as BATCH_NO,
				c.loan_party as LOAN_PARTY,
				null as ORDER_ID,
				c.currency_id as CURRENCY_ID,
				c.exchange_rate as EXCHANGE_RATE,
				1 as TYPE,
                a.status_active
			from
				inv_transaction a, product_details_master b, inv_receive_master c
			where
				a.prod_id=b.id and a.mst_id=c.id and a.company_id=$cbo_company_name and a.transaction_type in(1,4) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $item_cond $date_cond $purpose_cond1
			union all
			select
				a.id as TRANS_ID,
				a.store_id as STORE_ID,
				a.transaction_type as TRANSACTION_TYPE,
				a.mst_id as REC_ISSUE_ID,
				a.transaction_date as TRANSACTION_DATE,
				a.item_category as ITEM_CATEGORY,
				0 as RECEIVE_QTY,
				a.cons_quantity as ISSUE_QTY,
				a.cons_uom as CONS_UOM,
				a.remarks as REMARKS,
				a.batch_lot as BATCH_LOT,
				b.id as PROD_ID,
				b.item_group_id as ITEM_GROUP_ID,
				b.sub_group_name as SUB_GROUP_NAME,
				b.item_description as ITEM_DESCRIPTION,
				b.item_code as ITEM_CODE,
				b.is_compliance as IS_COMPLIANCE,
				a.cons_rate as CONS_RATE,
				a.cons_amount as CONS_AMOUNT,
				a.inserted_by as INSERTED_BY,
				$select_insert_date,
				$select_insert_time,
				a.order_qnty as ORDER_QNTY,
				a.order_rate as ORDER_RATE,
				c.issue_number as RECV_ISSUE_NUM,
				c.booking_id as BOOKING_ID,
				c.booking_no as BOOKING_NO,
				c.challan_no as CHALLAN_NO,
				c.knit_dye_source as KNITTING_SOURCE,
				c.knit_dye_company as KNITTING_COMPANY,
				c.issue_basis as RECEIVE_ISSUE_BASIS,
				c.issue_purpose as RECEIVE_ISSUE_PURPOSE,
				0 as SUPPLIER_ID,
				c.req_no as REQ_NO,
				c.batch_no as BATCH_NO,
				c.loan_party as LOAN_PARTY,
				c.order_id as ORDER_ID,
				null as CURRENCY_ID,
				null as EXCHANGE_RATE,
				2 as TYPE,
			    a.status_active   
			from
				inv_transaction a, product_details_master b, inv_issue_master c
			where
				a.prod_id=b.id and a.mst_id=c.id and a.company_id=$cbo_company_name and a.transaction_type in(2,3) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $item_cond $date_cond $purpose_cond 
			union all
			select
				a.id as TRANS_ID,
				a.store_id as STORE_ID,
				a.transaction_type as TRANSACTION_TYPE,
				a.mst_id as REC_ISSUE_ID,
				a.transaction_date as TRANSACTION_DATE,
				a.item_category as ITEM_CATEGORY,
				(case when a.transaction_type=5 then  a.cons_quantity else 0 end) as RECEIVE_QTY,
				(case when a.transaction_type=6 then  a.cons_quantity else 0 end) as ISSUE_QTY,
				a.cons_uom as CONS_UOM,
				a.remarks as REMARKS,
				a.batch_lot as BATCH_LOT,
				b.id as PROD_ID,
				b.item_group_id as ITEM_GROUP_ID,
				b.sub_group_name as SUB_GROUP_NAME,
				b.item_description as ITEM_DESCRIPTION,
				b.item_code as ITEM_CODE,
				b.is_compliance as IS_COMPLIANCE,
				a.cons_rate as CONS_RATE,
				a.cons_amount as CONS_AMOUNT,
				a.inserted_by as INSERTED_BY,
				$select_insert_date,
				$select_insert_time,
				a.order_qnty as ORDER_QNTY,
				a.order_rate as ORDER_RATE,
				c.transfer_system_id as RECV_ISSUE_NUM,
				0 as BOOKING_ID,
				null as BOOKING_NO,
				c.challan_no as CHALLAN_NO,
				0 as KNITTING_SOURCE,
				0 as KNITTING_COMPANY,
				0 as RECEIVE_ISSUE_BASIS,
				0 as RECEIVE_ISSUE_PURPOSE,
				0 as SUPPLIER_ID,
				null as REQ_NO,
				null as BATCH_NO,
				0 as LOAN_PARTY,
				null as ORDER_ID,
				null as CURRENCY_ID,
				null as EXCHANGE_RATE,
				3 as TYPE,
			    a.status_active
			from
				inv_transaction a, product_details_master b, inv_item_transfer_mst c
			where
				a.prod_id=b.id and a.mst_id=c.id and a.company_id=$cbo_company_name and a.transaction_type in(5,6) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $item_cond $date_cond $trans_cond ORDER BY TRANSACTION_DATE";
	}
	else if($rptType==2)
	{
		//and c.entry_form in(4,29) and c.entry_form in(55)
		$sql="SELECT
				a.id as TRANS_ID,
				a.store_id as STORE_ID,
				a.transaction_type as TRANSACTION_TYPE,
				a.mst_id as REC_ISSUE_ID,
				a.transaction_date as TRANSACTION_DATE,
				a.item_category as ITEM_CATEGORY,
				a.cons_quantity as RECEIVE_QTY,
				0 as ISSUE_QTY,
				a.cons_uom as CONS_UOM,
				a.batch_lot as BATCH_LOT,
				a.remarks as REMARKS,
				b.id as PROD_ID,
				b.item_group_id as ITEM_GROUP_ID,
				b.sub_group_name as SUB_GROUP_NAME,
				b.item_description as ITEM_DESCRIPTION,
				b.item_code as ITEM_CODE,
				b.is_compliance as IS_COMPLIANCE,
				a.cons_rate as CONS_RATE,
				a.cons_amount as CONS_AMOUNT,
				a.inserted_by as INSERTED_BY,
				$select_insert_date,
				$select_insert_time,
				a.order_qnty as ORDER_QNTY,
				a.order_rate as ORDER_RATE,
				c.recv_number as RECV_ISSUE_NUM,
				c.is_posted_account as IS_POSTED_ACCOUNT,
				c.booking_id as BOOKING_ID,
				c.booking_no as BOOKING_NO,
				c.challan_no as CHALLAN_NO,
				c.knitting_source as KNITTING_SOURCE,
				c.knitting_company as KNITTING_COMPANY,
				c.receive_basis as RECEIVE_ISSUE_BASIS,
				c.receive_purpose as RECEIVE_ISSUE_PURPOSE,
				c.loan_party as LOAN_PARTY,
				c.supplier_id as SUPPLIER_ID,
				null as REQ_NO,
				null as BATCH_NO,
				c.currency_id as CURRENCY_ID,
				c.exchange_rate as EXCHANGE_RATE,
				1 as TYPE,
				a.expire_date as EXPIRE_DATE, 
				a.manufacture_date as MANUFACTURE_DATE
			from
				inv_transaction a, product_details_master b, inv_receive_master c
			where
				a.prod_id=b.id and a.mst_id=c.id and a.company_id=$cbo_company_name and a.transaction_type in(1,4) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $item_cond $date_cond $purpose_cond1
			union all
			select
				a.id as TRANS_ID,
				a.store_id as STORE_ID,
				a.transaction_type as TRANSACTION_TYPE,
				a.mst_id as REC_ISSUE_ID,
				a.transaction_date as TRANSACTION_DATE,
				a.item_category as ITEM_CATEGORY,
				a.cons_quantity as RECEIVE_QTY,
				0 as ISSUE_QTY,
				a.cons_uom as CONS_UOM,
				a.batch_lot as BATCH_LOT,
				a.remarks as REMARKS,
				b.id as PROD_ID,
				b.item_group_id as ITEM_GROUP_ID,
				b.sub_group_name as SUB_GROUP_NAME,
				b.item_description as ITEM_DESCRIPTION,
				b.item_code as ITEM_CODE,
				b.is_compliance as IS_COMPLIANCE,
				a.cons_rate as CONS_RATE,
				a.cons_amount as CONS_AMOUNT,
				a.inserted_by as INSERTED_BY,
				$select_insert_date,
				$select_insert_time,
				a.order_qnty as ORDER_QNTY,
				a.order_rate as ORDER_RATE,
				c.transfer_system_id as RECV_ISSUE_NUM,
				c.is_posted_account as IS_POSTED_ACCOUNT,
				0 as BOOKING_ID,
				null as BOOKING_NO,
				c.challan_no as CHALLAN_NO,
				0 as KNITTING_SOURCE,
				0 as KNITTING_COMPANY,
				0 as RECEIVE_ISSUE_BASIS,
				0 as RECEIVE_ISSUE_PURPOSE,
				0 as LOAN_PARTY,
				0 as SUPPLIER_ID,
				null as REQ_NO,
				null as BATCH_NO,
				null as CURRENCY_ID,
				null as EXCHANGE_RATE,
				3 as TYPE,
				a.expire_date as EXPIRE_DATE, 
				a.manufacture_date as MANUFACTURE_DATE
			from
				inv_transaction a, product_details_master b, inv_item_transfer_mst c
			where
				a.prod_id=b.id and a.mst_id=c.id and a.company_id=$cbo_company_name and a.transaction_type in(5) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $item_cond $date_cond $trans_cond ORDER BY TRANSACTION_DATE ";
			
	}
	else if($rptType==3)
	{
        $all_data_cond = '';

        if($cbo_purpose!="" && $cbo_purpose!=0){
            $all_data_cond .=" and receive_issue_purpose=$cbo_purpose";
        }
        if($cbo_uom!='' && $cbo_uom!=0){
            $all_data_cond .=" and cons_uom=$cbo_uom";
        }
        if($cbo_item_cat!=0) $all_data_cond .=" and item_category=$cbo_item_cat"; else $all_data_cond .=" and item_category in(5,6,7,23)";
        if($txt_item_des!='') {
            $all_data_cond .=" and item_description like '%$txt_item_des%'";
        }

        if($cbo_store_name>0){
            $all_data_cond .=" and store_id=$cbo_store_name";
        }
		$sql="select
				a.id as TRANS_ID,
				a.store_id as STORE_ID,
				a.transaction_type as TRANSACTION_TYPE,
				a.mst_id as REC_ISSUE_ID,
				a.transaction_date as TRANSACTION_DATE,
				a.item_category as ITEM_CATEGORY,
				0 as RECEIVE_QTY,
				a.cons_quantity as ISSUE_QTY,
				a.cons_uom as CONS_UOM,
				a.remarks as REMARKS,
				a.batch_lot as BATCH_LOT,
				b.id as PROD_ID,
				b.item_group_id as ITEM_GROUP_ID,
				b.sub_group_name as SUB_GROUP_NAME,
				b.item_description as ITEM_DESCRIPTION,
				b.item_code as ITEM_CODE,
				b.is_compliance as IS_COMPLIANCE,
				a.cons_rate as CONS_RATE,
				a.cons_amount as CONS_AMOUNT,
				a.inserted_by as INSERTED_BY,
				c.is_posted_account as IS_POSTED_ACCOUNT,
				$select_insert_date,
				$select_insert_time,
				a.order_qnty as ORDER_QNTY,
				a.order_rate as ORDER_RATE,
				c.issue_number as RECV_ISSUE_NUM,
				c.booking_id as BOOKING_ID,
				c.booking_no as BOOKING_NO,
				c.challan_no as CHALLAN_NO,
				c.knit_dye_source as KNITTING_SOURCE,
				c.knit_dye_company as KNITTING_COMPANY,
				c.issue_basis as RECEIVE_ISSUE_BASIS,
				c.issue_purpose as RECEIVE_ISSUE_PURPOSE,				
				c.req_no as REQ_NO,
				c.batch_no as BATCH_NO,
				c.loan_party as LOAN_PARTY,
				c.order_id as ORDER_ID,
				c.buyer_id as BUYER_ID,
				null as CURRENCY_ID,
				null as EXCHANGE_RATE,
				d.batch_id as BATCH_ID,
				d.recipe_id as RECIPE_ID,
				2 as TYPE,
                a.status_active      
			from
				inv_transaction a, product_details_master b, inv_issue_master c, dyes_chem_issue_dtls d
			where
				a.prod_id=b.id and a.mst_id=c.id and a.id=d.trans_id and a.company_id=$cbo_company_name and a.transaction_type in(2,3) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $item_cond $date_cond $purpose_cond
			union all
			select
				a.id as TRANS_ID,
				a.store_id as STORE_ID,
				a.transaction_type as TRANSACTION_TYPE,
				a.mst_id as REC_ISSUE_ID,
				a.transaction_date as TRANSACTION_DATE,
				a.item_category as ITEM_CATEGORY,
				0 as RECEIVE_QTY,
				a.cons_quantity as ISSUE_QTY,
				a.cons_uom as CONS_UOM,
				a.remarks as REMARKS,
				a.batch_lot as BATCH_LOT,
				b.id as PROD_ID,
				b.item_group_id as ITEM_GROUP_ID,
				b.sub_group_name as SUB_GROUP_NAME,
				b.item_description as ITEM_DESCRIPTION,
				b.item_code as ITEM_CODE,
				b.is_compliance as IS_COMPLIANCE,
				a.cons_rate as CONS_RATE,
				a.cons_amount as CONS_AMOUNT,
				a.inserted_by as INSERTED_BY,
				c.is_posted_account as IS_POSTED_ACCOUNT,
				$select_insert_date,
				$select_insert_time,
				a.order_qnty as ORDER_QNTY,
				a.order_rate as ORDER_RATE,
				c.transfer_system_id as RECV_ISSUE_NUM,
				0 as BOOKING_ID,
				null as BOOKING_NO,
				c.challan_no as CHALLAN_NO,
				0 as KNITTING_SOURCE,
				0 as KNITTING_COMPANY,
				0 as RECEIVE_ISSUE_BASIS,
				0 as RECEIVE_ISSUE_PURPOSE,
				null as REQ_NO,
				null as BATCH_NO,
				0 as LOAN_PARTY,
				null as ORDER_ID,
				null as BUYER_ID,
				null as CURRENCY_ID,
				null as EXCHANGE_RATE,
				null as BATCH_ID,
				null as RECIPE_ID,
				3 as TYPE,
			    a.status_active   
			from
				inv_transaction a, product_details_master b, inv_item_transfer_mst c
			where
				a.prod_id=b.id and a.mst_id=c.id and a.company_id=$cbo_company_name and a.transaction_type in(6) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $item_cond $date_cond $trans_cond $stop_cond ORDER BY TRANSACTION_DATE";

	}
	if($rptType==5)
	{
        $all_data_cond = '';

        if($cbo_purpose!="" && $cbo_purpose!=0){
            $all_data_cond .=" and receive_issue_purpose=$cbo_purpose";
        }
        if($cbo_uom!='' && $cbo_uom!=0){
            $all_data_cond .=" and cons_uom=$cbo_uom";
        }
        if($cbo_item_cat!=0) $all_data_cond .=" and item_category=$cbo_item_cat"; else $all_data_cond .=" and item_category in(5,6,7,23)";
        if($txt_item_des!='') {
            $all_data_cond .=" and item_description like '%$txt_item_des%'";
        }
        if($cbo_supplier_name>0){
            $all_data_cond .=" and supplier_id=$cbo_supplier_name";
        }

        if($cbo_store_name>0){
            $all_data_cond .=" and store_id=$cbo_store_name";
        }
		$sql="select
				a.id as TRANS_ID,
				a.store_id as STORE_ID,
				a.transaction_type as TRANSACTION_TYPE,
				a.mst_id as REC_ISSUE_ID,
				a.transaction_date as TRANSACTION_DATE,
				a.item_category as ITEM_CATEGORY,
				a.cons_quantity as RECEIVE_QTY,
				0 as ISSUE_QTY,
				a.cons_uom as CONS_UOM,
				a.batch_lot as BATCH_LOT,
				b.id as PROD_ID,
				b.item_group_id as ITEM_GROUP_ID,
				b.sub_group_name as SUB_GROUP_NAME,
				b.item_description as ITEM_DESCRIPTION,
				b.item_code as ITEM_CODE,
				a.cons_rate as CONS_RATE,
				a.cons_amount as CONS_AMOUNT,
				a.inserted_by as INSERTED_BY,
				$select_insert_date,
				$select_insert_time,
				a.order_qnty as ORDER_QNTY,
				a.order_rate as ORDER_RATE,
				c.recv_number as RECV_ISSUE_NUM,
				c.booking_id as BOOKING_ID,
				c.booking_no as BOOKING_NO,
				c.challan_no as CHALLAN_NO,
				c.knitting_source as KNITTING_SOURCE,
				c.knitting_company as KNITTING_COMPANY,
				c.receive_basis as RECEIVE_ISSUE_BASIS,
				c.receive_purpose as RECEIVE_ISSUE_PURPOSE,
				c.supplier_id as SUPPLIER_ID,
				null as REQ_NO,
				null as BATCH_NO,
				c.currency_id as CURRENCY_ID,
				c.exchange_rate as EXCHANGE_RATE,
				1 as TYPE,
		        a.status_active    
			from
				inv_transaction a, product_details_master b, inv_receive_master c
			where
				a.prod_id=b.id and a.mst_id=c.id and a.company_id=$cbo_company_name and a.transaction_type in(1,4) and c.entry_form in(4,29) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $item_cond $date_cond
			union all
			select
				a.id as TRANS_ID,
				a.store_id as STORE_ID,
				a.transaction_type as TRANSACTION_TYPE,
				a.mst_id as REC_ISSUE_ID,
				a.transaction_date as TRANSACTION_DATE,
				a.item_category as ITEM_CATEGORY,
				0 as RECEIVE_QTY,
				a.cons_quantity as ISSUE_QTY,
				a.cons_uom as CONS_UOM,
				a.batch_lot as BATCH_LOT,
				b.id as PROD_ID,
				b.item_group_id as ITEM_GROUP_ID,
				b.sub_group_name as SUB_GROUP_NAME,
				b.item_description as ITEM_DESCRIPTION,
				b.item_code as ITEM_CODE,
				a.cons_rate as CONS_RATE,
				a.cons_amount as CONS_AMOUNT,
				a.inserted_by as INSERTED_BY,
				$select_insert_date,
				$select_insert_time,
				a.order_qnty as ORDER_QNTY,
				a.order_rate as ORDER_RATE,
				c.issue_number as RECV_ISSUE_NUM,
				c.booking_id as BOOKING_ID,
				c.booking_no as BOOKING_NO,
				c.challan_no as CHALLAN_NO,
				c.knit_dye_source as KNITTING_SOURCE,
				c.knit_dye_company as KNITTING_COMPANY,
				c.issue_basis as RECEIVE_ISSUE_BASIS,
				c.issue_purpose as RECEIVE_ISSUE_PURPOSE,
				0 as SUPPLIER_ID,
				c.req_no as REQ_NO,
				c.batch_no as BATCH_NO,
				null as CURRENCY_ID,
				null as EXCHANGE_RATE,
				2 as TYPE,
				a.status_active
			from
				inv_transaction a, product_details_master b, inv_issue_master c
			where
				a.prod_id=b.id and a.mst_id=c.id and a.company_id=$cbo_company_name and a.transaction_type in(2,3) and c.entry_form in(5,28,250) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $item_cond $date_cond ORDER BY TRANSACTION_DATE";
			
	}
	if($rptType==6)
	{
        $all_data_cond = '';

        if($cbo_purpose!="" && $cbo_purpose!=0){
            $all_data_cond .=" and receive_issue_purpose=$cbo_purpose";
        }
        if($cbo_uom!='' && $cbo_uom!=0){
            $all_data_cond .=" and cons_uom=$cbo_uom";
        }
        if($cbo_item_cat!=0) $all_data_cond .=" and item_category=$cbo_item_cat"; else $all_data_cond .=" and item_category in(5,6,7,23)";
        if($txt_item_des!='') {
            $all_data_cond .=" and item_description like '%$txt_item_des%'";
        }

        if($cbo_store_name>0){
            $all_data_cond .=" and store_id=$cbo_store_name";
        }
		$sql="select
				a.id as TRANS_ID,
				a.store_id as STORE_ID,
				a.transaction_type as TRANSACTION_TYPE,
				a.mst_id as REC_ISSUE_ID,
				a.transaction_date as TRANSACTION_DATE,
				a.item_category as ITEM_CATEGORY,
				0 as RECEIVE_QTY,
				a.cons_quantity as ISSUE_QTY,
				a.cons_uom as CONS_UOM,
				a.batch_lot as BATCH_LOT,
				b.id as PROD_ID,
				b.item_group_id as ITEM_GROUP_ID,
				b.sub_group_name as SUB_GROUP_NAME,
				b.item_description as ITEM_DESCRIPTION,
				b.item_code as ITEM_CODE,
				a.cons_rate as CONS_RATE,
				a.cons_amount as CONS_AMOUNT,
				a.inserted_by as INSERTED_BY,
				$select_insert_date,
				$select_insert_time,
				a.order_qnty as ORDER_QNTY,
				a.order_rate as ORDER_RATE,
				c.issue_number as RECV_ISSUE_NUM,
				c.booking_id as BOOKING_ID,
				c.booking_no as BOOKING_NO,
				c.challan_no as CHALLAN_NO,
				c.knit_dye_source as KNITTING_SOURCE,
				c.knit_dye_company as KNITTING_COMPANY,
				c.issue_basis as RECEIVE_ISSUE_BASIS,
				c.issue_purpose as RECEIVE_ISSUE_PURPOSE,				
				c.req_no as REQ_NO,
				c.batch_no as BATCH_NO,
				null as CURRENCY_ID,
				null as EXCHANGE_RATE,
				d.batch_id as BATCH_ID,
				d.recipe_id as RECIPE_ID,
				d.ratio as RATIO,
				2 as TYPE,
                a.status_active    
			from
				inv_transaction a, product_details_master b, inv_issue_master c, dyes_chem_issue_dtls d
			where
				a.prod_id=b.id and a.mst_id=c.id and a.id=d.trans_id and a.company_id=$cbo_company_name and a.transaction_type in(2,3) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $item_cond $date_cond $purpose_cond
			union all
			select
				a.id as TRANS_ID,
				a.store_id as STORE_ID,
				a.transaction_type as TRANSACTION_TYPE,
				a.mst_id as REC_ISSUE_ID,
				a.transaction_date as TRANSACTION_DATE,
				a.item_category as ITEM_CATEGORY,
				0 as RECEIVE_QTY,
				a.cons_quantity as ISSUE_QTY,
				a.cons_uom as CONS_UOM,
				a.batch_lot as BATCH_LOT,
				b.id as PROD_ID,
				b.item_group_id as ITEM_GROUP_ID,
				b.sub_group_name as SUB_GROUP_NAME,
				b.item_description as ITEM_DESCRIPTION,
				b.item_code as ITEM_CODE,
				a.cons_rate as CONS_RATE,
				a.cons_amount as CONS_AMOUNT,
				a.inserted_by as INSERTED_BY,
				$select_insert_date,
				$select_insert_time,
				a.order_qnty as ORDER_QNTY,
				a.order_rate as ORDER_RATE,
				c.transfer_system_id as RECV_ISSUE_NUM,
				0 as BOOKING_ID,
				null as BOOKING_NO,
				c.challan_no as CHALLAN_NO,
				0 as KNITTING_SOURCE,
				0 as KNITTING_COMPANY,
				0 as RECEIVE_ISSUE_BASIS,
				0 as RECEIVE_ISSUE_PURPOSE,
				null as REQ_NO,
				null as BATCH_NO,
				null as CURRENCY_ID,
				null as EXCHANGE_RATE,
				null as BATCH_ID,
				null as RECIPE_ID,
				null as RATIO,
				3 as TYPE,
			    a.status_active   
			from
				inv_transaction a, product_details_master b, inv_item_transfer_mst c
			where
				a.prod_id=b.id and a.mst_id=c.id and a.company_id=$cbo_company_name and a.transaction_type in(6) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $item_cond $date_cond ORDER BY TRANSACTION_DATE";
	}
	
	if($rptType==5) $rptType=1;
	
	//echo $sql;die;////

	if($rptType==1)
	{
		$table_width=2900;
		$div_width="2920px";
	}
	else if($rptType==2)
	{
		$table_width=2920;
		$div_width="2940px";
	}
	else if($rptType==3)
	{
		$table_width=2800;
		$div_width="2820px";
	}
	else if($rptType==6)
	{
		$table_width=2620;
		$div_width="2640px";
	}

	//echo $sql;
	$sql_result=sql_select($sql);
	//var_dump($sql_result);die;die;

	//$wo_num=return_library_array("select id, wo_number_prefix_num,requisition_no from wo_non_order_info_mst where item_category in(5,6,7)","id","wo_number_prefix_num");
	//$pi_num_arr=return_library_array( "select id, pi_number from com_pi_master_details where item_category_id in(5,6,7)",'id','pi_number');
	$issue_summary_arr=array();$issue_summary_avg_rate_arr=array();$issue_summary_arr_up=array();$issue_recv_summary_arr_up=array();$req_all_arr=array();$po_all_arr=array();
	if($rptType==3 || $rptType==6)
	{
		foreach($sql_result as $row)
		{
			$issue_summary_arr[$row["ITEM_CATEGORY"]][$row["ITEM_GROUP_ID"]][$row["ITEM_DESCRIPTION"]]["issue_qty"]+=$row["ISSUE_QTY"];
			$issue_summary_avg_rate_arr[$row["ITEM_CATEGORY"]][$row["ITEM_GROUP_ID"]][$row["ITEM_DESCRIPTION"]]['cons_amount']+=$row["CONS_AMOUNT"];
			if($row['BATCH_NO']!='' && $row['BATCH_NO']!=0)
			{
				$batch_arr=explode(",",$row['BATCH_NO']);
				foreach($batch_arr as $batch_no)
				{
					$batch_all[$batch_no]=$batch_no;
				}
			}
			
			if($row["TRANSACTION_TYPE"]==2)
			{
				if($row['REQ_NO']!='' && $row['REQ_NO']!=0)
				{
					$req_arr=explode(",",$row['REQ_NO']);
					foreach($req_arr as $req_no)
					{
						$req_all_arr[$req_no]=$req_no;
					}
				}
			}
			
			if($row["ORDER_ID"]!='' && $row["ORDER_ID"]!=0)
			{
				$po_id=array_unique(explode(",",$row["ORDER_ID"]));
				foreach($po_id as $poId)
				{
					$po_all_arr[$poId]=$poId;
				}
			}
		}
	}
	if($rptType==2)
	{
		foreach($sql_result as $row)
		{
			$issue_summary_arr[$row["ITEM_CATEGORY"]][$row["ITEM_GROUP_ID"]][$row["ITEM_DESCRIPTION"]]["issue_qty"]+=$row["RECEIVE_QTY"];
			$issue_summary_avg_rate_arr[$row["ITEM_CATEGORY"]][$row["ITEM_GROUP_ID"]][$row["ITEM_DESCRIPTION"]]['cons_amount']+=$row["CONS_AMOUNT"];
			if($row['BATCH_NO']!='' && $row['BATCH_NO']!=0)
			{
				$batch_arr=explode(",",$row['BATCH_NO']);
				foreach($batch_arr as $batch_no)
				{
					$batch_all[$batch_no]=$batch_no;
				}
			}
			
			if($row["TRANSACTION_TYPE"]==2)
			{
				if($row['REQ_NO']!='' && $row['REQ_NO']!=0)
				{
					$req_arr=explode(",",$row['REQ_NO']);
					foreach($req_arr as $req_no)
					{
						$req_all_arr[$req_no]=$req_no;
					}
				}
			}
			
			if($row["ORDER_ID"]!='' && $row["ORDER_ID"]!=0)
			{
				$po_id=array_unique(explode(",",$row["ORDER_ID"]));
				foreach($po_id as $poId)
				{
					$po_all_arr[$poId]=$poId;
				}
			}
		}
	}
	if($rptType==1)
	{
		foreach($sql_result as $row)
		{
			$issue_recv_summary_arr_up[$row["ITEM_CATEGORY"]][$row["ITEM_GROUP_ID"]][$row["ITEM_DESCRIPTION"]]["receive_qty"]+=$row["RECEIVE_QTY"];
			$issue_summary_arr_up[$row["ITEM_CATEGORY"]][$row["ITEM_GROUP_ID"]][$row["ITEM_DESCRIPTION"]]["issue_qty"]+=$row["ISSUE_QTY"];
			if($row['BATCH_NO']!='' && $row['BATCH_NO']!=0)
			{
				$batch_arr=explode(",",$row['BATCH_NO']);
				foreach($batch_arr as $batch_no)
				{
					$batch_all[$batch_no]=$batch_no;
				}
			}
			
			if($row["TRANSACTION_TYPE"]==2)
			{
				if($row['REQ_NO']!='' && $row['REQ_NO']!=0)
				{
					$req_arr=explode(",",$row['REQ_NO']);
					foreach($req_arr as $req_no)
					{
						$req_all_arr[$req_no]=$req_no;
					}
				}
			}
			
			if($row["ORDER_ID"]!='' && $row["ORDER_ID"]!=0)
			{
				$po_id=array_unique(explode(",",$row["ORDER_ID"]));
				foreach($po_id as $poId)
				{
					$po_all_arr[$poId]=$poId;
				}
			}
		}
	}
	
	$con = connect();
	$r_id=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id");
	if($r_id)
	{
		oci_commit($con);
	}
	
	if(count($batch_all)>0)
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 40, 1,$batch_all, $empty_arr);
	}
	
	if(count($req_all_arr)>0)
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 40, 2,$req_all_arr, $empty_arr);
	}
	
	if(count($po_all_arr)>0)
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 40, 3,$po_all_arr, $empty_arr);
	}
	
	//$requisiton_arr=return_library_array( "select id, requ_no from inv_purchase_requisition_mst where company_id=$cbo_company_name and status_active=1",'id','requ_no');
	
	//$batch_num_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');and a.company_id=$cbo_company_name
	$buyer_array=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$batch_ex_sql="select a.id as ID, a.batch_no as BATCH_NO, a.extention_no as EXTENTION_NO, a.batch_against as BATCH_AGAINST , a.batch_weight as BATCH_WEIGHT,a.booking_no as BOOKING_NO, b.buyer_id as BUYER_ID, b.job_no as JOB_NO
	from wo_booking_mst b, pro_batch_create_mst a, GBL_TEMP_ENGINE c
	where a.booking_no=b.booking_no and a.id=c.ref_val and c.REF_FROM=1 and a.status_active=1
	union all
	select a.id as ID, a.batch_no as BATCH_NO, a.extention_no as EXTENTION_NO, a.batch_against as BATCH_AGAINST , a.batch_weight as BATCH_WEIGHT,a.booking_no as BOOKING_NO, b.buyer_id as BUYER_ID, b.job_no as JOB_NO
	from wo_non_ord_samp_booking_mst b, pro_batch_create_mst a, GBL_TEMP_ENGINE c 
	where a.booking_no=b.booking_no and a.id=c.ref_val and c.REF_FROM=1 and a.status_active=1
	union all
	select a.id as ID, a.batch_no as BATCH_NO, a.extention_no as EXTENTION_NO, a.batch_against as BATCH_AGAINST , a.batch_weight as BATCH_WEIGHT,a.booking_no as BOOKING_NO, null as BUYER_ID, null as JOB_NO
	from pro_batch_create_mst a, GBL_TEMP_ENGINE c where a.id=c.ref_val and c.REF_FROM=1 and a.status_active=1 and a.entry_form=36 and a.is_deleted=0";
	//echo $batch_ex_sql;die;
	
	$batch_extn_no_result=sql_select($batch_ex_sql);
	
	foreach ($batch_extn_no_result as $row) 
	{
		if($row['EXTENTION_NO']==2)
		{
			$batch_num_arr[$row['ID']]=$row['BATCH_NO'];
			$batch_extn_no_arr[$row['ID']]=$row['EXTENTION_NO'];
			$batch_wt[$row['ID']]=$row['BATCH_WEIGHT'];
			$batch_booking_no[$row['ID']]=$row['BOOKING_NO'];
			$batch_buyer[$row['ID']]=$buyer_array[$row['BUYER_ID']];
			$batch_job_no[$row['ID']]=$row['JOB_NO'];
		}
		else
		{
			$batch_num_arr[$row['ID']]=$row['BATCH_NO'];
			$batch_wt[$row['ID']]=$row['BATCH_WEIGHT'];
			$batch_booking_no[$row['ID']]=$row['BOOKING_NO'];
			$batch_buyer[$row['ID']]=$buyer_array[$row['BUYER_ID']];
			$batch_job_no[$row['ID']]=$row['JOB_NO'];
		}
	}
	//echo '<pre>';print_r($batch_num_arr);
	unset($batch_extn_no_result);
	//var_dump($batch_extn_no_arr);
	$floor_sqls=sql_select("Select a.batch_id as BATCH_ID, a.floor_id as FLOOR_ID from pro_fab_subprocess a, GBL_TEMP_ENGINE c where a.batch_id=c.ref_val and c.REF_FROM=1 and a.status_active=1 group by a.batch_id, a.floor_id");
	unset($batch_all_cond);
	$floor_data_arr=array();
	foreach($floor_sqls as $row)
	{
		$floor_data_arr[$row['BATCH_ID']]['floor_id']=$row['FLOOR_ID'];
	}
	unset($floor_sqls);
	

	$req_sql="select a.id as ID, a.requ_no as REQU_NO, a.machine_id as MACHINE_ID from dyes_chem_issue_requ_mst a, GBL_TEMP_ENGINE c  
	where a.id=c.ref_val and c.REF_FROM=2 and a.company_id=$cbo_company_name and a.status_active=1";
	$req_sql_result=sql_select($req_sql);
	foreach($req_sql_result as $val)
	{
		$dyes_Issue_requisiton_arr[$val['ID']]=$val['REQU_NO'];
		$req_machine_arr[$val['ID']]=$val['MACHINE_ID'];
	}
	unset($req_sql_result);
	$po_sqls=sql_select("Select a.id as PO_ID, a.grouping as REF_NO from wo_po_break_down a, GBL_TEMP_ENGINE c  where a.id=c.ref_val and c.REF_FROM=3 and a.status_active=1 and a.is_deleted=0");
	$po_data_arr=array();
	foreach($po_sqls as $row)
	{
		$po_data_arr[$row['PO_ID']]['ref']=$row['REF_NO'];
	}
	unset($po_sqls);
	
	$r_id=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id");
	if($r_id)
	{
		oci_commit($con);
	}
	disconnect($con);
	//echo "select id,floor_name from lib_prod_floor where status_active=1 and is_deleted=0 and company_id=$cbo_company_name and production_process=3";die;
	$floor_arr=return_library_array("select id,floor_name from lib_prod_floor where status_active=1 and is_deleted=0 and company_id=$cbo_company_name and production_process=3", "id","floor_name");
	$machine_arr=return_library_array( "select id, machine_no from lib_machine_name",'id','machine_no');
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$supplier_array=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');	
	$group_arr=return_library_array( "select id, item_name from  lib_item_group",'id','item_name');
	ob_start();
	?>
	<div style="width:<? echo $div_width; ?>">
		<fieldset style="width:<? echo $div_width; ?>">
			<table width="<? echo $table_width; ?>" border="0" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="" align="left">
					<tr class="form_caption" style="border:none;">
						<td colspan="12" align="center" style="border:none;font-size:16px; font-weight:bold" >Date Wise Item Receive Issue Report </td>
					</tr>
					<tr style="border:none;">
						<td colspan="12" align="center" style="border:none; font-size:14px;">
								Company Name : <? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></td>
					</tr>
			</table>
			<br/>
			<table style="width:<? echo $div_width; ?>;">
					<tr>
						<th colspan="12" align="center"><? echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
					</tr>
			</table>
			<table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" align="left">
				<thead>
					<tr>
						<th width="30" >SL</th>
						<th width="70" >Prod. Id</th>
                        <th width="120" >Store</th>
						<th width="100" >Trans. Date</th>
						<th width="130" >Trans. Ref.</th>
						<?
                        if($rptType==1 || $rptType==3)
                        {
                        ?>
                        <th width="100" >Dyeing Floor</th>
                        <?
                        }
                        ?>
						<th width="100" >Challan No</th>
						<th width="80">BATCH LOT</th>
						<th width="80">Basis</th>
                        <th width="80">Purpose</th>
						<?
                        if($rptType==1 || $rptType==2 || $rptType==3)
                        {
                        ?>
                        <th width="100" >Loan/Sales Party</th>
                        <?
                        }
                        if($rptType==3 || $rptType==6)
                        {
							?>
							<th width="80">Buyer</th>
							<?
							if ($rptType==6)
							{
								?>
								<th width="70">Job No</th>
								<th width="100">Booking No</th>
								<?
							}
                        }
                        ?>
						<th width="100">Req. No/ Batch NO/ PI/ WO</th>
							<?
						if($rptType==1 || $rptType==3)
						{
						?>
						<th width="100" >Internal Ref</th>
						<?
						}

						if($rptType==2 || $rptType==1 )
						{
							?>
							<th width="120">Supplier</th>
							<?
						}
						if($rptType==3 || $rptType==6)
						{
							?>
							<th width="80">Batch No</th>
								<?
								if($rptType==6)
								{
								?>
									<th width="80">Batch Wt.</th>
								<?
								}
								?>
							<th width="40">Extn No</th>
							<th width="80">Recipe No</th>
								<?
								if($rptType==6)
								{
								?>
									<th width="80">Ratio</th>
								<?
								}
								?>
							<th width="80">Machine No</th>
							<?
						}
						?>
                        <th width="80">Category</th>
						<th width="80">Item Group</th>
						<th width="80">Item Sub-Group</th>
                        <th width="50">Item Code</th>
						<th width="120">Item Description</th>
						<?
						if($rptType==2)
						{
							?>
							<th width="100">Manufacture Date</th>
							<th width="100">Expire Date</th>
							<?
						}
                        if($rptType==2 || $rptType==1 )
                        {
                            ?>
                            <th width="80">Currency</th>
                            <th width="80">Exchange Rate</th>
                            <th width="80">Actual Rate</th>
                            <?
                        }
                        ?>
						<th width="50">UOM</th>
						<?
                        if($rptType==2 || $rptType==1 )
                        {
                            ?>
                            <th width="80">Receive Qty</th>
                            <th width="80">Actual Amt</th>
                            <?
                        }
                        if($rptType==3 || $rptType==1 || $rptType==6)
                        {
                            ?>
                            <th  width="80">Issue Qty</th>
                            <?
                        }
                        ?>
						<th  width="80">Rate(TK)</th>
						<th  width="100">Amount(TK)</th>
						<?
                        if($rptType==1 || $rptType==2 || $rptType==3)
                        {
                        ?>
                        <th width="100" >Remarks</th>
                        <?
                        }
                        ?>
						<th  width="110">User</th>
						<th  width="80">Accounting Posting</th>
						<th  width="160">Insert Date</th>
                        <?
                        if($rptType==1 || $rptType==2 || $rptType==3)
                        {
                        ?>
                        <th width="100" >Zero Discharge</th>
                        <?
                        }
                        ?>
					</tr>
				</thead>
		    </table>
		    <br/>
			
		  	<div style="width:<? echo $div_width; ?>; overflow-y: scroll; max-height:250px;" id="scroll_body">
				<table width="<? echo $table_width; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
					<tbody>
					<?
					
					$i=1;$total_receive="";$total_issue="";
					foreach($sql_result as $row)
					{
						if ($i%2==0)
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";
						$ref_no='';$floor_no='';
						if($row["ORDER_ID"]!='' && $row["ORDER_ID"]!=0){
							$po_id=array_unique(explode(",",$row["ORDER_ID"]));
							foreach($po_id as $poId)
							{
								if($ref_no!='') {$ref_no.=", ".$po_data_arr[$poId]['ref'];} else  {$ref_no=$po_data_arr[$poId]['ref'];}	
							}
						}
						if($row["BATCH_NO"]!='' && $row["BATCH_NO"]!=0){
							$batch_id=array_unique(explode(",",$row["BATCH_NO"]));
							foreach($batch_id as $batchId)
							{
								if($floor_no!='') {$floor_no.=", ".$floor_arr[$floor_data_arr[$batchId]['floor_id']];} 
								else{$floor_no=$floor_arr[$floor_data_arr[$batchId]['floor_id']];}	
							}
						}
						// print_r($floor_data_arr);
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30"><p><? echo $i; ?>&nbsp;</p></td>
							<td width="70" align="center"><p><? echo $row["PROD_ID"]; ?>&nbsp;</p></td>
                            <td width="120"><p><? echo $store_arr[$row["STORE_ID"]]; ?>&nbsp;</p></td>
							<td width="100" align="center"><p><? if($row["TRANSACTION_DATE"]!="0000-00-00" && $row["TRANSACTION_DATE"]!="") echo change_date_format($row["TRANSACTION_DATE"]); else echo ""; ?>&nbsp;</p></td>
							<td width="130"  align="center"><p><? echo $row["RECV_ISSUE_NUM"]; ?>&nbsp;</p></td>
							<?
							if($rptType==1 || $rptType==3)
							{
							?>
							<td width="100" ><?echo $floor_no;?></td>
							<?
							}
							?>
							<td width="100"><p><? echo $row['CHALLAN_NO'];?>&nbsp;</p></td>
							<td width="80"><p><? echo $row['BATCH_LOT'];?>&nbsp;</p></td>
							<td width="80"><p><? echo $receive_basis_arr[$row["RECEIVE_ISSUE_BASIS"]];?>&nbsp;</p></td>
							<td width="80" title="<? echo $row['RECEIVE_ISSUE_PURPOSE']; ?>"><p><? echo $general_issue_purpose[$row['RECEIVE_ISSUE_PURPOSE']]; ?>&nbsp;</p></td>
							<?
							if($rptType==1 || $rptType==2 || $rptType==3)
							{
							?>
							<td width="100" ><p><? echo $loan_party_arr[$row['LOAN_PARTY']];?>&nbsp;</p></td>
							<?
							}
							?>
								<?
								if($rptType==3 || $rptType==6)
								{
									if($rptType==6)
									{
										?>
										<td width="80"><p><? echo $batch_buyer[$row['BATCH_NO']]; ?>&nbsp;</p></td>
										<td width="70"><p><? echo $batch_job_no[$row['BATCH_NO']]; ?>&nbsp;</p></td>
										<td width="100"><p><? echo $batch_booking_no[$row['BATCH_NO']]; ?>&nbsp;</p></td>
										<?
									}
									else
									{
										?>
										<td width="80"><p><? echo $buyer_array[$row['BUYER_ID']]; ?>&nbsp;</p></td>
										<?
									}
								}
								?>
							<td width="100"><p>
							<?
							if($row["TRANSACTION_TYPE"]==1)
							{
								if($row["RECEIVE_ISSUE_BASIS"]==1 || $row["RECEIVE_ISSUE_BASIS"]==2)
								{
									echo $row["BOOKING_NO"];
								}
								else
								{
									echo "Independent";
								}
							}
							else if($row["TRANSACTION_TYPE"]==2)
							{
								if($row["RECEIVE_ISSUE_BASIS"]==5 )
								{
									echo $batch_num_arr[$row["BATCH_NO"]];
								}
								else if($row["RECEIVE_ISSUE_BASIS"]==7)
								{
									echo $dyes_Issue_requisiton_arr[$row["REQ_NO"]];
								}
								else
								{
									echo "Independent";
								}
							}
							else if($row["TRANSACTION_TYPE"]==3)
							{
								echo "Receive Return";
							}
							else if($row["TRANSACTION_TYPE"]==4)
							{
								echo "Issue Return";
							}
							else
							{
								echo "Transfer";
							}
							?>&nbsp;</p></td>

							<?
								if($rptType==1 || $rptType==3)
								{
								?>
								<td width="100" ><? echo $ref_no;?></td>
								<?
								}
							?>

							<?
							if($rptType==2 || $rptType==1 )
							{
								?>
	                            <td width="120" ><p><? echo $supplier_array[$row['SUPPLIER_ID']];//echo $supplier_array[]; ?>&nbsp;</p></td>
	                            <?
							}

							if($rptType==3 || $rptType==6)
							{
								if (strpos($row["BATCH_NO"], ',') !== false) 
								{
									$batch = explode(",",$row["BATCH_NO"]);
									$batch_nos = '';
									for($k=0; $k < count($batch); $k++){
										$batch_nos .=$batch_num_arr[$batch[$k]].",";
									}
									?>
									<td width="80" align="center"><p><? echo chop($batch_nos,',');?></p></td>
									<?
								}
								else
								{
									?>
									<td width="80" align="center"><p><? echo $batch_num_arr[$row["BATCH_NO"]];?></p></td>
									<?
								}
															
								if($rptType==6) 
								{
									?>
									<td width="80" align="right"><p><? echo $batch_wt[$row["BATCH_NO"]]; ?>&nbsp;</p></td>
									<?
								}
								?>			
								<td width="40" align="center"><p><? echo $batch_extn_no_arr[$row["BATCH_NO"]];?></p></td>
								<td width="80" align="center"><p><? echo $row["RECIPE_ID"];?></p></td>
									<?
									if($rptType==6)
									{
									?>
									<td width="80" align="right"><p><? echo $row["RATIO"]; ?>&nbsp;</p></td>
									<?
									}
									?>
	                            <td width="80" align="center"><p><? if($row["RECEIVE_ISSUE_BASIS"]==7) echo $machine_arr[$req_machine_arr[$row["REQ_NO"]]];?></p></td>
								<?
							}
							?>
	                        <td width="80" ><p><? echo $item_category[$row['ITEM_CATEGORY']]; ?>&nbsp;</p></td>
							<td width="80" ><p><? echo $group_arr[$row['ITEM_GROUP_ID']]; ?></p></td>
							<td width="80" ><p><? echo $row['SUB_GROUP_NAME']; ?>&nbsp;</p></td>
	                        <td width="50" ><p><? echo $row['ITEM_CODE']; ?>&nbsp;</p></td>
							<td width="120"><p><? echo $row['ITEM_DESCRIPTION']; ?></p></td>
							<?
							if($rptType==2)
							{
								?>
								<td width="100" align="center"><p><? echo change_date_format($row["MANUFACTURE_DATE"]); ?>&nbsp;</p></td>
								<td width="100" align="right"><p><? echo change_date_format($row["EXPIRE_DATE"]); ?>&nbsp;</p></td>
								<?
							} 

							if($rptType==2 || $rptType==1 )
							{
								?>
								<td width="80" align="center"><p><? if($row["TRANSACTION_TYPE"]==1) echo $currency[$row['CURRENCY_ID']]; ?>&nbsp;</p></td>
								<td width="80" align="right"><p><? if($row["TRANSACTION_TYPE"]==1) echo number_format($row['EXCHANGE_RATE'],2); ?>&nbsp;</p></td>
								<td width="80" align="right"><p><? if($row["TRANSACTION_TYPE"]==1) echo number_format($row['ORDER_RATE'],2); ?></p></td>
								<?
							}
							    ?>
						    <td width="50"  align="center"><p><? echo $unit_of_measurement[$row['CONS_UOM']]; ?>&nbsp;</p></td>
						        <?
							if($rptType==2 || $rptType==1 )
							{
								?>
								<td width="80" align="right"><p><? echo number_format($row["RECEIVE_QTY"],2); $total_receive +=$row["RECEIVE_QTY"]; ?></p></td>
								<td width="80" align="right"><p><? if($row["TRANSACTION_TYPE"]==1) $order_amt=$row['ORDER_QNTY']*$row['ORDER_RATE']; echo number_format($order_amt,4); $total_order_amt+=$order_amt; $order_amt=0; ?></p></td>
								<?
							}
							if($rptType==3 || $rptType==1 || $rptType==6)
							{
								?>
								<td width="80" align="right"><p><? echo number_format($row["ISSUE_QTY"],2); $total_issue +=$row["ISSUE_QTY"]; ?></p></td>
								<?
							}
							?>
							<td width="80" align="right"><p><? echo number_format($row["CONS_RATE"],2); ?></p></td>
							<td align="right" width="100"><p><? echo number_format($row["CONS_AMOUNT"],2); $total_amount +=$row["CONS_AMOUNT"]; ?></p></td>
							<?
							if($rptType==1 || $rptType==2 || $rptType==3)
							{
							?>
							<td width="100" ><p><? echo $row['REMARKS'];?>&nbsp;</p></td>
							<?
							}
							?>
							<td width="110"><p><? echo $user_name_arr[$row["INSERTED_BY"]]; ?>&nbsp;</p></td>
							<td width="80"><p><? echo $account_posted_arr[$row["IS_POSTED_ACCOUNT"]]; ?>&nbsp;</p></td>
							<td width="160"><p><? echo change_date_format($row["INSERT_DATE"])." ".$row["INSERT_TIME"]; ?>&nbsp;</p></td>
                            <?
							if($rptType==1 || $rptType==2 || $rptType==3)
							{
							?>
							<td width="100" ><p><? echo $compliance_arr[$row['IS_COMPLIANCE']];?>&nbsp;</p></td>
							<?
							}
							?>
						</tr>
						<?
						$i++;
					}
					?>
					</tbody>
				</table>
            </div>
            <div>
				<table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer" align="left">
					<tfoot>
						<tr>
	                    	<th width="30" >&nbsp;</th>
							<th width="70" >&nbsp;</th>
                            <th width="120" >&nbsp;</th>
							<th width="100" >&nbsp;</th>
							<th width="130" >&nbsp;</th>
								<?
								if($rptType==1 || $rptType==3)
								{
								?>
								<th width="100" >&nbsp;</th>
								<?
								}
								?>
							<th width="100" >&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="80">&nbsp;</th>
	                        <th width="80">&nbsp;</th>
								<?
								if($rptType==1 || $rptType==2 || $rptType==3 )
								{
								?>
								<th width="100" >&nbsp;</th>
								<?
								}
								if($rptType==3 || $rptType==6)
								{
									?>
									<th width="80">&nbsp;</th>
									<?
									if($rptType==6)
									{
										?>
										<th width="70">&nbsp;</th>
										<th width="100">&nbsp;</th>
										<?
									}
								}
								?>
							<th width="100">&nbsp;</th>
								<?
								if($rptType==1 || $rptType==3)
								{
								?>
								<th width="100" >&nbsp;</th>
								<?
								}

								if($rptType==2 || $rptType==1 )
								{
									?>
									<th width="120">&nbsp;</th>
									<?
								}
								if($rptType==3 || $rptType==6)
								{
									?>
									<th width="80">&nbsp;</th>
										<?
										if($rptType==6)
										{
										?>
										<th width="80">&nbsp;</th>
										<?
										}
										?>
									<th width="40">&nbsp;</th>
									<th width="80">&nbsp;</th>
										<?
										if($rptType==6)
										{
										?>
										<th width="80">&nbsp;</th>
										<?
										}
										?>
									<th width="80">&nbsp;</th>
									<?
								}
								?>
	                        <th width="80">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="80">&nbsp;</th>
	                        <th width="50">&nbsp;</th>
							<th width="120"></th>
								<?
								if($rptType==2)
								{
									?>
									<th width="100">&nbsp;</th>
									<th width="100">&nbsp;</th>
									<?
								}

								if($rptType==2 || $rptType==1 )
								{
									?>
									<th width="80">&nbsp;</th>
									<th width="80">&nbsp;</th>
									<th width="80">&nbsp;</th>
									<?
							    }
							        ?>
						    <th width="50">Total</th>
						        <?
							    if($rptType==2 || $rptType==1 )
							    {
									?>
									<th width="80" id="value_total_receive"><? echo  number_format($total_receive,2); ?></th>
									<th width="80"  id="value_total_order_amt"><?// echo number_format($total_order_amt,2); ?></th>
									<?
								}

								if($rptType==3 || $rptType==1 || $rptType==6 )
								{
									//echo $rptType."dfdf"; die;
									
									?>
									<th  width="80" id="value_total_issue" class="issue"><? echo number_format($total_issue,2); ?></th>
									<?
								}
								?>
							<th  width="80">&nbsp;</th>
							<th  width="100" id="value_total_amount"><? echo number_format($total_amount,2); ?></th>
							<?
                            if($rptType==1 || $rptType==2 || $rptType==3)
                            {
                            ?>
                            <th width="100" >&nbsp;</th>
                            <?
                            }
                            ?>
							<th  width="110">&nbsp;</th>
							<th  width="80">&nbsp;</th>
							<th  width="160">&nbsp;</th>
                            <?
                            if($rptType==1 || $rptType==2 || $rptType==3)
                            {
                            ?>
                            <th width="100" >&nbsp;</th>
                            <?
                            }
                            ?>
						</tr>
					</tfoot>
				</table>
		 	</div>
         	<br/>
			<? 
			if($rptType==3 || $rptType==2 || $rptType==6)
			{
				?>
	            <div>
		            <table align="left" width="480" id="table_header_1" border="1" class="rpt_table" rules="all">
		             	<caption><b> 
		             		<? if($rptType==3 || $rptType==6)
							{
							echo "Issue Summary";
							$col_head="Issue Qty";
							}
							else if($rptType==2)
							{
								$col_head="Receive Qty";
							echo "Receive Summary";
							}
							else if($rptType==1)
							{
							echo "Receive & Issue Summary";
							}
		                    ?>
		                    </b>
		            	</caption>
		            	<thead>
			                <tr>
			                    <th width="30">SL</th>
			                    <th width="80" >Category</th>
			                    <th width="80">Item Group</th>
			                    <th width="100">Item Desc.</th>
			                    <th width="70"><? echo $col_head;?></th>
			                    <th width="70">Avg. Rate.</th>
			                    <th width="">Issue Value</th>
			              	</tr>
		              	</thead>
						<tbody>
			              	<?
							$k=1;$tot_val=$tot_issue_value=0;
							foreach($issue_summary_arr as $cat=>$item_data)
							{
								if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								foreach($item_data as $item_key=>$des_data)
								{
									foreach($des_data as $des_key=>$qty_val)
								  	{
										foreach($qty_val as $val)
								  		{
											$tot_val+=$val;
											$tot_rate=$issue_summary_avg_rate_arr[$cat][$item_key][$des_key]['cons_amount']/$val;

										  	?>
							              	<tr  bgcolor="<? echo $bgcolor;?>">
							                  <td width="30"> <? echo $k;?> </td>
							                  <td width="80"> <? echo $item_category[$cat];?></td>
							                  <td width="80"><? echo $group_arr[$item_key];?>  </td>
							                  <td width="100"> <? echo $des_key;?></td>
							                  <td width="70" align="right"><? echo number_format($val,2);?> </td>
							                  <td width="70" align="right"><? echo number_format($tot_rate,2);?> </td>
							                  <td width="" align="right"><? echo number_format($issue_value=($val*$tot_rate),2);?> </td>
							              	</tr>
							              	<?     
							              	$k++;
							              	$tot_issue_value+=$issue_value;
							  		 	}
									}
								}
							}
						  	?>
						</tbody>
			            <tfoot>
			              	<th width="30"></th>
			                <th width="80"></th>
			                <th width="80"></th>
			                <th width="100">Total</th>
			                <th width="70" align="right"><? echo $tot_val;?></th>
			                <th width="70"></th>
			                <th width="" align="right"><? echo number_format($tot_issue_value,2);?></th>
			            </tfoot>
		            </table>
				</div>
	            <?
			}
			if($rptType==1)
			{
				?>
	            <div>
					<table align="left" width="430" id="table_header_1" border="1" class="rpt_table" rules="all">
						<caption><b> 
			             	<?
							echo "Receive & Issue Summary";
							?></b> 
						</caption>
			            <thead>
			                <tr>
			                    <th width="30">SL</th>
			                    <th width="80" >Category</th>
			                    <th width="80">Item Group</th>
			                    <th width="100">Item Desc.</th>
			                    <th width="70">Recv. Qty.</th>
			                    <th width="">Issue Qty.</th>
							</tr>
						</thead>
			            <tbody>
							<?
							$k=1;$tot_issue_qty=0;$total_recv_qty=0;
							foreach($issue_summary_arr_up as $cat=>$item_data)
							{
								if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								foreach($item_data as $item_key=>$des_data)
								{
									foreach($des_data as $des_key=>$qty_val)
								  	{
										foreach($qty_val as $qty)
								  		{
											$tot_issue_qty+=$qty;
											$tot_recv=$issue_recv_summary_arr_up[$cat][$item_key][$des_key]['receive_qty'];
											$total_recv_qty+=$tot_recv;
											?>
											<tr bgcolor="<? echo $bgcolor;?>">
												<td width="30"> <? echo $k;?> </td>
												<td width="80"> <? echo $item_category[$cat];?></td>
												<td width="80"><? echo $group_arr[$item_key];?>  </td>
												<td width="100"> <? echo $des_key;?></td>
												<td width="70" align="right"><? echo number_format($tot_recv,2);?> </td>
												<td width="" align="right"><? echo number_format($qty,2);?> </td>
											</tr>
											<?     
											$k++;
							  			}
									}
								}
							}
							?>
			            </tbody>
						<tfoot>
							<th width="30"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="100">Total</th>
							<th width="70"><? echo $total_recv_qty;?></th>
							<th width="" align="right"><? echo $tot_issue_qty;?></th>
						</tfoot>
					</table>
				</div>
	            <?
			}
			?>
		</fieldset>
	</div>
	<?

	foreach (glob("*.xls") as $filename) {
	if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$html**$filename**$cbo_item_cat**$rptType";
	exit();

}

//report generated for Excel download here--------------------//
if($action=="generate_report_excel")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_item_cat=str_replace("'","",$cbo_item_cat);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$rptType=str_replace("'","",$rptType);
	$cbo_based_on=str_replace("'","",$cbo_based_on);
	$cbo_purpose=str_replace("'","",$cbo_purpose);
	$cbo_supplier_name=str_replace("'","",$cbo_supplier_name);
	$cbo_uom=str_replace("'","",$cbo_uom);
	//var_dump($cbo_purpose);
	


	if($db_type==0)
	{
		if($cbo_based_on==1)
		{
			if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.transaction_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' "; else $date_cond="";
		}
		else
		{
			if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.insert_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."  00:00:01' and '".change_date_format($txt_date_to,"yyyy-mm-dd")." 23:59:59' "; else $date_cond="";
		}
		$select_insert_date="DATE_FORMAT(a.insert_date,'%d-%m-%Y') as insert_date";
		$select_insert_time="DATE_FORMAT(a.insert_date,'%H:%i:%S') as insert_time";
		$insert_date_group="DATE_FORMAT(a.insert_date,'%d-%m-%Y')";
		$insert_time_group="DATE_FORMAT(a.insert_date,'%H:%i:%S')";
		//
		$select_recepi_id="group_concat(d.recipe_id)  as recipe_id";
		$select_po_id="group_concat(e.po_id)  as po_id";
	}
	else
	{
		if($cbo_based_on==1)
		{
			if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.transaction_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' "; else $date_cond="";
		}
		else
		{
			if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.insert_date between '".date("j-M-Y",strtotime($txt_date_from))."   01:00:01 AM' and '".date("j-M-Y",strtotime($txt_date_to))."  11:59:59 PM' "; else $date_cond="";
		}
		$select_insert_date=" to_char(a.insert_date,'DD-MON-YYYY')  as insert_date";//HH24:MI:SS
		$select_insert_time=" to_char(a.insert_date,'HH24:MI:SS')  as insert_time";
		$insert_date_group="to_char(a.insert_date,'DD-MON-YYYY')";
		$insert_time_group="to_char(a.insert_date,'HH24:MI:SS')";
		$select_recepi_id="listagg(cast(d.recipe_id as varchar(4000)),',') within group (order by d.recipe_id) as recipe_id";
		$select_po_id="listagg(cast(e.po_id as varchar(4000)),',') within group (order by e.po_id) as po_id";
	}

	$supp_cond=$trans_cond="";
	if($cbo_item_cat!=0) $item_cond=" and a.item_category=$cbo_item_cat"; else $item_cond=" and a.item_category in(5,6,7,23)";
	if($cbo_uom!='' && $cbo_uom!=0)
	{
		$item_cond.=" and a.cons_uom=$cbo_uom";
	}
	if($cbo_supplier_name>0)
	{
		$supp_cond=" and c.supplier_id=$cbo_supplier_name";
		$trans_cond=" and c.id=0"; // avoid transfer information
	}
	if($cbo_purpose!="" && $cbo_purpose!=0)
	{
		$purpose_cond=" and c.issue_purpose=$cbo_purpose";
		//$purpose_cond1=" and c.receive_purpose=$cbo_purpose";
	}

	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_item_cat=str_replace("'","",$cbo_item_cat);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$rptType=str_replace("'","",$rptType);
	$cbo_based_on=str_replace("'","",$cbo_based_on);
	$cbo_purpose=str_replace("'","",$cbo_purpose);
	$cbo_supplier_name=str_replace("'","",$cbo_supplier_name);
	$cbo_store_name=str_replace("'","",$cbo_store_name);
	$txt_item_des=str_replace("'","",$txt_item_des);
	$cbo_uom=str_replace("'","",$cbo_uom);
	$account_posted_arr = array(  0=>"No",1=>"Yes" );

	if($db_type==0)
	{
		if($cbo_based_on==1)
		{
			if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.transaction_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' "; else $date_cond="";
		}
		else
		{
			if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.insert_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."  00:00:01' and '".change_date_format($txt_date_to,"yyyy-mm-dd")." 23:59:59' "; else $date_cond="";
		}
		$select_insert_date="DATE_FORMAT(a.insert_date,'%d-%m-%Y') as INSERT_DATE";
		$select_insert_time="DATE_FORMAT(a.insert_date,'%H:%i:%S') as INSERT_TIME";
		$insert_date_group="DATE_FORMAT(a.insert_date,'%d-%m-%Y')";
		$insert_time_group="DATE_FORMAT(a.insert_date,'%H:%i:%S')";
		//
		$select_recepi_id="group_concat(d.recipe_id)  as recipe_id";
		$select_po_id="group_concat(e.po_id)  as po_id";
	}
	else
	{
		if($cbo_based_on==1)
		{
			if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.transaction_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' "; else $date_cond="";
		}
		else
		{
			if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.insert_date between '".date("j-M-Y",strtotime($txt_date_from))."   01:00:01 AM' and '".date("j-M-Y",strtotime($txt_date_to))."  11:59:59 PM' "; else $date_cond="";
		}
		$select_insert_date=" to_char(a.insert_date,'DD-MON-YYYY') as INSERT_DATE";//HH24:MI:SS
		$select_insert_time=" to_char(a.insert_date,'HH24:MI:SS') as INSERT_TIME";
		$insert_date_group="to_char(a.insert_date,'DD-MON-YYYY')";
		$insert_time_group="to_char(a.insert_date,'HH24:MI:SS')";
		$select_recepi_id="listagg(cast(d.recipe_id as varchar(4000)),',') within group (order by d.recipe_id) as recipe_id";
		$select_po_id="listagg(cast(e.po_id as varchar(4000)),',') within group (order by e.po_id) as po_id";
	}

	$supp_cond=$trans_cond="";
	if($cbo_item_cat!=0) $item_cond=" and a.item_category=$cbo_item_cat"; else $item_cond=" and a.item_category in(5,6,7,23)";
	if($txt_item_des!='') { $item_cond .=" and b.item_description like '%$txt_item_des%'";}
	if($cbo_supplier_name>0)
	{
		$item_cond.=" and a.supplier_id=$cbo_supplier_name";
		$trans_cond=" and c.id=0"; // avoid transfer information
	}

	
	if($cbo_store_name>0)
	{
		$item_cond.=" and a.store_id=$cbo_store_name";
	}

	if($cbo_uom!='' && $cbo_uom!=0)
	{
		$item_cond.=" and a.cons_uom=$cbo_uom";
	}
	
	if($cbo_purpose!="" && $cbo_purpose!=0)
	{
		$purpose_cond=" and c.issue_purpose=$cbo_purpose";
		$purpose_cond1=" and c.receive_purpose=$cbo_purpose";
	}

	if($rptType==4)
	{
		$all_data_cond = '';

        if($cbo_purpose!="" && $cbo_purpose!=0){
            $all_data_cond .=" and receive_issue_purpose=$cbo_purpose";
        }
        if($cbo_uom!='' && $cbo_uom!=0){
            $all_data_cond .=" and cons_uom=$cbo_uom";
        }
        if($cbo_item_cat!=0) $all_data_cond .=" and item_category=$cbo_item_cat"; else $all_data_cond .=" and item_category in(5,6,7,23)";
        if($txt_item_des!='') {
            $all_data_cond .=" and item_description like '%$txt_item_des%'";
        }
        if($cbo_supplier_name>0){
            $all_data_cond .=" and supplier_id=$cbo_supplier_name";
        }


        if($cbo_store_name>0){

            $all_data_cond .=" and store_id=$cbo_store_name";
        }


        $sql="select
				a.id as TRANS_ID,
				a.store_id as STORE_ID,
				a.transaction_type as TRANSACTION_TYPE,
				a.mst_id as REC_ISSUE_ID,
				a.transaction_date as TRANSACTION_DATE,
				a.item_category as ITEM_CATEGORY,
				a.cons_quantity as RECEIVE_QTY,
				0 as ISSUE_QTY,
				a.cons_uom as CONS_UOM,
				a.remarks as REMARKS,
				a.batch_lot as BATCH_LOT,
				b.id as PROD_ID,
				b.item_group_id as ITEM_GROUP_ID,
				b.sub_group_name as SUB_GROUP_NAME,
				b.item_description as ITEM_DESCRIPTION,
				b.item_code as ITEM_CODE,
				b.is_compliance as IS_COMPLIANCE,
				a.cons_rate as CONS_RATE,
				a.cons_amount as CONS_AMOUNT,
				a.inserted_by as INSERTED_BY,
				$select_insert_date,
				$select_insert_time,
				a.order_qnty as ORDER_QNTY,
				a.order_rate as ORDER_RATE,
				c.recv_number as RECV_ISSUE_NUM,
				c.booking_id as BOOKING_ID,
				c.booking_no as BOOKING_NO,
				c.challan_no as CHALLAN_NO,
				c.knitting_source as KNITTING_SOURCE,
				c.knitting_company as KNITTING_COMPANY,
				c.receive_basis as RECEIVE_ISSUE_BASIS,
				c.receive_purpose as RECEIVE_ISSUE_PURPOSE,
				c.supplier_id as SUPPLIER_ID,
				null as REQ_NO,
				null as BATCH_NO,
				c.loan_party as LOAN_PARTY,
				null as ORDER_ID,
				c.currency_id as CURRENCY_ID,
				c.exchange_rate as EXCHANGE_RATE,
				1 as TYPE,
                a.status_active
			from
				inv_transaction a, product_details_master b, inv_receive_master c
			where
				a.prod_id=b.id and a.mst_id=c.id and a.company_id=$cbo_company_name and a.transaction_type in(1,4) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $item_cond $date_cond $purpose_cond1
			union all
			select
				a.id as TRANS_ID,
				a.store_id as STORE_ID,
				a.transaction_type as TRANSACTION_TYPE,
				a.mst_id as REC_ISSUE_ID,
				a.transaction_date as TRANSACTION_DATE,
				a.item_category as ITEM_CATEGORY,
				0 as RECEIVE_QTY,
				a.cons_quantity as ISSUE_QTY,
				a.cons_uom as CONS_UOM,
				a.remarks as REMARKS,
				a.batch_lot as BATCH_LOT,
				b.id as PROD_ID,
				b.item_group_id as ITEM_GROUP_ID,
				b.sub_group_name as SUB_GROUP_NAME,
				b.item_description as ITEM_DESCRIPTION,
				b.item_code as ITEM_CODE,
				b.is_compliance as IS_COMPLIANCE,
				a.cons_rate as CONS_RATE,
				a.cons_amount as CONS_AMOUNT,
				a.inserted_by as INSERTED_BY,
				$select_insert_date,
				$select_insert_time,
				a.order_qnty as ORDER_QNTY,
				a.order_rate as ORDER_RATE,
				c.issue_number as RECV_ISSUE_NUM,
				c.booking_id as BOOKING_ID,
				c.booking_no as BOOKING_NO,
				c.challan_no as CHALLAN_NO,
				c.knit_dye_source as KNITTING_SOURCE,
				c.knit_dye_company as KNITTING_COMPANY,
				c.issue_basis as RECEIVE_ISSUE_BASIS,
				c.issue_purpose as RECEIVE_ISSUE_PURPOSE,
				0 as SUPPLIER_ID,
				c.req_no as REQ_NO,
				c.batch_no as BATCH_NO,
				c.loan_party as LOAN_PARTY,
				c.order_id as ORDER_ID,
				null as CURRENCY_ID,

				null as EXCHANGE_RATE,
				2 as TYPE,
			    a.status_active   
			from
				inv_transaction a, product_details_master b, inv_issue_master c
			where
				a.prod_id=b.id and a.mst_id=c.id and a.company_id=$cbo_company_name and a.transaction_type in(2,3) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $item_cond $date_cond $purpose_cond 
			union all
			select
				a.id as TRANS_ID,
				a.store_id as STORE_ID,
				a.transaction_type as TRANSACTION_TYPE,
				a.mst_id as REC_ISSUE_ID,
				a.transaction_date as TRANSACTION_DATE,
				a.item_category as ITEM_CATEGORY,
				(case when a.transaction_type=5 then  a.cons_quantity else 0 end) as RECEIVE_QTY,
				(case when a.transaction_type=6 then  a.cons_quantity else 0 end) as ISSUE_QTY,
				a.cons_uom as CONS_UOM,
				a.remarks as REMARKS,
				a.batch_lot as BATCH_LOT,
				b.id as PROD_ID,
				b.item_group_id as ITEM_GROUP_ID,
				b.sub_group_name as SUB_GROUP_NAME,

				b.item_description as ITEM_DESCRIPTION,
				b.item_code as ITEM_CODE,
				b.is_compliance as IS_COMPLIANCE,
				a.cons_rate as CONS_RATE,
				a.cons_amount as CONS_AMOUNT,
				a.inserted_by as INSERTED_BY,
				$select_insert_date,
				$select_insert_time,
				a.order_qnty as ORDER_QNTY,
				a.order_rate as ORDER_RATE,
				c.transfer_system_id as RECV_ISSUE_NUM,
				0 as BOOKING_ID,
				null as BOOKING_NO,
				c.challan_no as CHALLAN_NO,
				0 as KNITTING_SOURCE,
				0 as KNITTING_COMPANY,
				0 as RECEIVE_ISSUE_BASIS,
				0 as RECEIVE_ISSUE_PURPOSE,
				0 as SUPPLIER_ID,
				null as REQ_NO,
				null as BATCH_NO,
				0 as LOAN_PARTY,
				null as ORDER_ID,
				null as CURRENCY_ID,
				null as EXCHANGE_RATE,
				3 as TYPE,
			    a.status_active
			from
				inv_transaction a, product_details_master b, inv_item_transfer_mst c
			where
				a.prod_id=b.id and a.mst_id=c.id and a.company_id=$cbo_company_name and a.transaction_type in(5,6) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $item_cond $date_cond $trans_cond";
	}
	else if($rptType==7)
	{
		//and c.entry_form in(4,29) and c.entry_form in(55)
		$sql="SELECT
				a.id as TRANS_ID,
				a.store_id as STORE_ID,
				a.transaction_type as TRANSACTION_TYPE,
				a.mst_id as REC_ISSUE_ID,
				a.transaction_date as TRANSACTION_DATE,
				a.item_category as ITEM_CATEGORY,
				a.cons_quantity as RECEIVE_QTY,
				0 as ISSUE_QTY,
				a.cons_uom as CONS_UOM,
				a.batch_lot as BATCH_LOT,
				a.remarks as REMARKS,
				b.id as PROD_ID,
				b.item_group_id as ITEM_GROUP_ID,
				b.sub_group_name as SUB_GROUP_NAME,
				b.item_description as ITEM_DESCRIPTION,
				b.item_code as ITEM_CODE,
				b.is_compliance as IS_COMPLIANCE,
				a.cons_rate as CONS_RATE,
				a.cons_amount as CONS_AMOUNT,
				a.inserted_by as INSERTED_BY,
				$select_insert_date,
				$select_insert_time,
				a.order_qnty as ORDER_QNTY,
				a.order_rate as ORDER_RATE,
				c.recv_number as RECV_ISSUE_NUM,
				c.is_posted_account as IS_POSTED_ACCOUNT,
				c.booking_id as BOOKING_ID,
				c.booking_no as BOOKING_NO,
				c.challan_no as CHALLAN_NO,
				c.knitting_source as KNITTING_SOURCE,
				c.knitting_company as KNITTING_COMPANY,
				c.receive_basis as RECEIVE_ISSUE_BASIS,
				c.receive_purpose as RECEIVE_ISSUE_PURPOSE,
				c.loan_party as LOAN_PARTY,
				c.supplier_id as SUPPLIER_ID,
				null as REQ_NO,
				null as BATCH_NO,
				c.currency_id as CURRENCY_ID,
				c.exchange_rate as EXCHANGE_RATE,
				1 as TYPE,
				a.expire_date as EXPIRE_DATE, 
				a.manufacture_date as MANUFACTURE_DATE
			from
				inv_transaction a, product_details_master b, inv_receive_master c
			where
				a.prod_id=b.id and a.mst_id=c.id and a.company_id=$cbo_company_name and a.transaction_type in(1,4) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $item_cond $date_cond $purpose_cond1
			union all
			select
				a.id as TRANS_ID,
				a.store_id as STORE_ID,
				a.transaction_type as TRANSACTION_TYPE,
				a.mst_id as REC_ISSUE_ID,
				a.transaction_date as TRANSACTION_DATE,
				a.item_category as ITEM_CATEGORY,
				a.cons_quantity as RECEIVE_QTY,
				0 as ISSUE_QTY,
				a.cons_uom as CONS_UOM,
				a.batch_lot as BATCH_LOT,
				a.remarks as REMARKS,
				b.id as PROD_ID,
				b.item_group_id as ITEM_GROUP_ID,
				b.sub_group_name as SUB_GROUP_NAME,
				b.item_description as ITEM_DESCRIPTION,
				b.item_code as ITEM_CODE,
				b.is_compliance as IS_COMPLIANCE,
				a.cons_rate as CONS_RATE,
				a.cons_amount as CONS_AMOUNT,
				a.inserted_by as INSERTED_BY,
				$select_insert_date,
				$select_insert_time,
				a.order_qnty as ORDER_QNTY,
				a.order_rate as ORDER_RATE,
				c.transfer_system_id as RECV_ISSUE_NUM,
				c.is_posted_account as IS_POSTED_ACCOUNT,
				0 as BOOKING_ID,
				null as BOOKING_NO,
				c.challan_no as CHALLAN_NO,
				0 as KNITTING_SOURCE,
				0 as KNITTING_COMPANY,
				0 as RECEIVE_ISSUE_BASIS,
				0 as RECEIVE_ISSUE_PURPOSE,
				0 as LOAN_PARTY,
				0 as SUPPLIER_ID,
				null as REQ_NO,
				null as BATCH_NO,
				null as CURRENCY_ID,
				null as EXCHANGE_RATE,
				3 as TYPE,
				a.expire_date as EXPIRE_DATE, 
				a.manufacture_date as MANUFACTURE_DATE
			from
				inv_transaction a, product_details_master b, inv_item_transfer_mst c
			where
				a.prod_id=b.id and a.mst_id=c.id and a.company_id=$cbo_company_name and a.transaction_type in(5) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $item_cond $date_cond $trans_cond";
			
	}
	else if($rptType==8)
	{
        $all_data_cond = '';

        if($cbo_purpose!="" && $cbo_purpose!=0){
            $all_data_cond .=" and receive_issue_purpose=$cbo_purpose";
        }
        if($cbo_uom!='' && $cbo_uom!=0){
            $all_data_cond .=" and cons_uom=$cbo_uom";
        }
        if($cbo_item_cat!=0) $all_data_cond .=" and item_category=$cbo_item_cat"; else $all_data_cond .=" and item_category in(5,6,7,23)";
        if($txt_item_des!='') {
            $all_data_cond .=" and item_description like '%$txt_item_des%'";
        }

        if($cbo_store_name>0){
            $all_data_cond .=" and store_id=$cbo_store_name";
        }
		$sql="select
				a.id as TRANS_ID,
				a.store_id as STORE_ID,
				a.transaction_type as TRANSACTION_TYPE,
				a.mst_id as REC_ISSUE_ID,
				a.transaction_date as TRANSACTION_DATE,
				a.item_category as ITEM_CATEGORY,
				0 as RECEIVE_QTY,
				a.cons_quantity as ISSUE_QTY,
				a.cons_uom as CONS_UOM,
				a.remarks as REMARKS,
				a.batch_lot as BATCH_LOT,
				b.id as PROD_ID,
				b.item_group_id as ITEM_GROUP_ID,
				b.sub_group_name as SUB_GROUP_NAME,
				b.item_description as ITEM_DESCRIPTION,
				b.item_code as ITEM_CODE,
				b.is_compliance as IS_COMPLIANCE,
				a.cons_rate as CONS_RATE,
				a.cons_amount as CONS_AMOUNT,
				a.inserted_by as INSERTED_BY,
				c.is_posted_account as IS_POSTED_ACCOUNT,
				$select_insert_date,
				$select_insert_time,
				a.order_qnty as ORDER_QNTY,
				a.order_rate as ORDER_RATE,
				c.issue_number as RECV_ISSUE_NUM,
				c.booking_id as BOOKING_ID,
				c.booking_no as BOOKING_NO,
				c.challan_no as CHALLAN_NO,
				c.knit_dye_source as KNITTING_SOURCE,
				c.knit_dye_company as KNITTING_COMPANY,
				c.issue_basis as RECEIVE_ISSUE_BASIS,
				c.issue_purpose as RECEIVE_ISSUE_PURPOSE,				
				c.req_no as REQ_NO,
				c.batch_no as BATCH_NO,
				c.loan_party as LOAN_PARTY,
				c.order_id as ORDER_ID,
				c.buyer_id as BUYER_ID,
				null as CURRENCY_ID,
				null as EXCHANGE_RATE,
				d.batch_id as BATCH_ID,
				d.recipe_id as RECIPE_ID,
				2 as TYPE,
                a.status_active      
			from
				inv_transaction a, product_details_master b, inv_issue_master c, dyes_chem_issue_dtls d
			where
				a.prod_id=b.id and a.mst_id=c.id and a.id=d.trans_id and a.company_id=$cbo_company_name and a.transaction_type in(2,3) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $item_cond $date_cond $purpose_cond
			union all
			select
				a.id as TRANS_ID,
				a.store_id as STORE_ID,
				a.transaction_type as TRANSACTION_TYPE,
				a.mst_id as REC_ISSUE_ID,
				a.transaction_date as TRANSACTION_DATE,
				a.item_category as ITEM_CATEGORY,
				0 as RECEIVE_QTY,
				a.cons_quantity as ISSUE_QTY,
				a.cons_uom as CONS_UOM,
				a.remarks as REMARKS,
				a.batch_lot as BATCH_LOT,
				b.id as PROD_ID,
				b.item_group_id as ITEM_GROUP_ID,
				b.sub_group_name as SUB_GROUP_NAME,
				b.item_description as ITEM_DESCRIPTION,
				b.item_code as ITEM_CODE,
				b.is_compliance as IS_COMPLIANCE,
				a.cons_rate as CONS_RATE,
				a.cons_amount as CONS_AMOUNT,
				a.inserted_by as INSERTED_BY,
				c.is_posted_account as IS_POSTED_ACCOUNT,
				$select_insert_date,
				$select_insert_time,
				a.order_qnty as ORDER_QNTY,
				a.order_rate as ORDER_RATE,
				c.transfer_system_id as RECV_ISSUE_NUM,
				0 as BOOKING_ID,
				null as BOOKING_NO,
				c.challan_no as CHALLAN_NO,
				0 as KNITTING_SOURCE,
				0 as KNITTING_COMPANY,
				0 as RECEIVE_ISSUE_BASIS,
				0 as RECEIVE_ISSUE_PURPOSE,
				null as REQ_NO,
				null as BATCH_NO,
				0 as LOAN_PARTY,
				null as ORDER_ID,
				null as BUYER_ID,
				null as CURRENCY_ID,
				null as EXCHANGE_RATE,
				null as BATCH_ID,
				null as RECIPE_ID,
				3 as TYPE,
			    a.status_active   
			from
				inv_transaction a, product_details_master b, inv_item_transfer_mst c
			where
				a.prod_id=b.id and a.mst_id=c.id and a.company_id=$cbo_company_name and a.transaction_type in(6) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $item_cond $date_cond $trans_cond";

	}
	if($rptType==9)
	{
        $all_data_cond = '';

        if($cbo_purpose!="" && $cbo_purpose!=0){
            $all_data_cond .=" and receive_issue_purpose=$cbo_purpose";
        }
        if($cbo_uom!='' && $cbo_uom!=0){
            $all_data_cond .=" and cons_uom=$cbo_uom";
        }
        if($cbo_item_cat!=0) $all_data_cond .=" and item_category=$cbo_item_cat"; else $all_data_cond .=" and item_category in(5,6,7,23)";
        if($txt_item_des!='') {
            $all_data_cond .=" and item_description like '%$txt_item_des%'";
        }

        if($cbo_store_name>0){
            $all_data_cond .=" and store_id=$cbo_store_name";
        }
		$sql="select
				a.id as TRANS_ID,
				a.store_id as STORE_ID,
				a.transaction_type as TRANSACTION_TYPE,
				a.mst_id as REC_ISSUE_ID,
				a.transaction_date as TRANSACTION_DATE,
				a.item_category as ITEM_CATEGORY,
				0 as RECEIVE_QTY,
				a.cons_quantity as ISSUE_QTY,
				a.cons_uom as CONS_UOM,
				a.batch_lot as BATCH_LOT,
				b.id as PROD_ID,
				b.item_group_id as ITEM_GROUP_ID,
				b.sub_group_name as SUB_GROUP_NAME,
				b.item_description as ITEM_DESCRIPTION,
				b.item_code as ITEM_CODE,
				a.cons_rate as CONS_RATE,
				a.cons_amount as CONS_AMOUNT,
				a.inserted_by as INSERTED_BY,
				$select_insert_date,
				$select_insert_time,
				a.order_qnty as ORDER_QNTY,
				a.order_rate as ORDER_RATE,
				c.issue_number as RECV_ISSUE_NUM,
				c.booking_id as BOOKING_ID,
				c.booking_no as BOOKING_NO,
				c.challan_no as CHALLAN_NO,
				c.knit_dye_source as KNITTING_SOURCE,
				c.knit_dye_company as KNITTING_COMPANY,
				c.issue_basis as RECEIVE_ISSUE_BASIS,
				c.issue_purpose as RECEIVE_ISSUE_PURPOSE,				
				c.req_no as REQ_NO,
				c.batch_no as BATCH_NO,
				null as CURRENCY_ID,
				null as EXCHANGE_RATE,
				d.batch_id as BATCH_ID,
				d.recipe_id as RECIPE_ID,
				d.ratio as RATIO,
				2 as TYPE,
                a.status_active    
			from
				inv_transaction a, product_details_master b, inv_issue_master c, dyes_chem_issue_dtls d
			where
				a.prod_id=b.id and a.mst_id=c.id and a.id=d.trans_id and a.company_id=$cbo_company_name and a.transaction_type in(2,3) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $item_cond $date_cond $purpose_cond
			union all
			select
				a.id as TRANS_ID,
				a.store_id as STORE_ID,
				a.transaction_type as TRANSACTION_TYPE,
				a.mst_id as REC_ISSUE_ID,

				a.transaction_date as TRANSACTION_DATE,
				a.item_category as ITEM_CATEGORY,
				0 as RECEIVE_QTY,
				a.cons_quantity as ISSUE_QTY,
				a.cons_uom as CONS_UOM,
				a.batch_lot as BATCH_LOT,
				b.id as PROD_ID,
				b.item_group_id as ITEM_GROUP_ID,
				b.sub_group_name as SUB_GROUP_NAME,
				b.item_description as ITEM_DESCRIPTION,
				b.item_code as ITEM_CODE,
				a.cons_rate as CONS_RATE,
				a.cons_amount as CONS_AMOUNT,
				a.inserted_by as INSERTED_BY,
				$select_insert_date,
				$select_insert_time,
				a.order_qnty as ORDER_QNTY,
				a.order_rate as ORDER_RATE,
				c.transfer_system_id as RECV_ISSUE_NUM,
				0 as BOOKING_ID,
				null as BOOKING_NO,
				c.challan_no as CHALLAN_NO,
				0 as KNITTING_SOURCE,
				0 as KNITTING_COMPANY,
				0 as RECEIVE_ISSUE_BASIS,
				0 as RECEIVE_ISSUE_PURPOSE,
				null as REQ_NO,
				null as BATCH_NO,
				null as CURRENCY_ID,
				null as EXCHANGE_RATE,
				null as BATCH_ID,
				null as RECIPE_ID,
				null as RATIO,
				3 as TYPE,
			    a.status_active   
			from
				inv_transaction a, product_details_master b, inv_item_transfer_mst c
			where
				a.prod_id=b.id and a.mst_id=c.id and a.company_id=$cbo_company_name and a.transaction_type in(6) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $item_cond $date_cond";
	}
	//echo $sql;die;
	

	$sql_result=sql_select($sql);
	$buyer_array=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	//var_dump($sql_result);die;die;

	//$wo_num=return_library_array("select id, wo_number_prefix_num,requisition_no from wo_non_order_info_mst where item_category in(5,6,7)","id","wo_number_prefix_num");
	//$pi_num_arr=return_library_array( "select id, pi_number from com_pi_master_details where item_category_id in(5,6,7)",'id','pi_number');
	$issue_summary_arr=array();$issue_summary_avg_rate_arr=array();$issue_summary_arr_up=array();$issue_recv_summary_arr_up=array();$req_all_arr=array();$po_all_arr=array();
	if($rptType==8 || $rptType==9)
	{
		foreach($sql_result as $row)
		{
			$issue_summary_arr[$row["ITEM_CATEGORY"]][$row["ITEM_GROUP_ID"]][$row["ITEM_DESCRIPTION"]]["issue_qty"]+=$row["ISSUE_QTY"];
			$issue_summary_avg_rate_arr[$row["ITEM_CATEGORY"]][$row["ITEM_GROUP_ID"]][$row["ITEM_DESCRIPTION"]]['cons_amount']+=$row["CONS_AMOUNT"];
			if($row['BATCH_NO']!='' && $row['BATCH_NO']!=0)
			{
				$batch_arr=explode(",",$row['BATCH_NO']);
				foreach($batch_arr as $batch_no)
				{
					$batch_all[$batch_no]=$batch_no;
				}
			}
			
			if($row["TRANSACTION_TYPE"]==2)
			{
				if($row['REQ_NO']!='' && $row['REQ_NO']!=0)
				{
					$req_arr=explode(",",$row['REQ_NO']);
					foreach($req_arr as $req_no)
					{
						$req_all_arr[$req_no]=$req_no;
					}
				}
			}
			
			if($row["ORDER_ID"]!='' && $row["ORDER_ID"]!=0)
			{
				$po_id=array_unique(explode(",",$row["ORDER_ID"]));
				foreach($po_id as $poId)
				{
					$po_all_arr[$poId]=$poId;
				}
			}
		}
	}
	if($rptType==7)
	{
		foreach($sql_result as $row)
		{
			$issue_summary_arr[$row["ITEM_CATEGORY"]][$row["ITEM_GROUP_ID"]][$row["ITEM_DESCRIPTION"]]["issue_qty"]+=$row["RECEIVE_QTY"];
			$issue_summary_avg_rate_arr[$row["ITEM_CATEGORY"]][$row["ITEM_GROUP_ID"]][$row["ITEM_DESCRIPTION"]]['cons_amount']+=$row["CONS_AMOUNT"];
			if($row['BATCH_NO']!='' && $row['BATCH_NO']!=0)
			{
				$batch_arr=explode(",",$row['BATCH_NO']);
				foreach($batch_arr as $batch_no)
				{
					$batch_all[$batch_no]=$batch_no;
				}
			}
			
			if($row["TRANSACTION_TYPE"]==2)
			{
				if($row['REQ_NO']!='' && $row['REQ_NO']!=0)
				{
					$req_arr=explode(",",$row['REQ_NO']);
					foreach($req_arr as $req_no)
					{
						$req_all_arr[$req_no]=$req_no;
					}
				}
			}
			
			if($row["ORDER_ID"]!='' && $row["ORDER_ID"]!=0)
			{
				$po_id=array_unique(explode(",",$row["ORDER_ID"]));
				foreach($po_id as $poId)
				{
					$po_all_arr[$poId]=$poId;
				}
			}
		}
	}
	if($rptType==4)
	{
		foreach($sql_result as $row)
		{
			$issue_recv_summary_arr_up[$row["ITEM_CATEGORY"]][$row["ITEM_GROUP_ID"]][$row["ITEM_DESCRIPTION"]]["receive_qty"]+=$row["RECEIVE_QTY"];
			$issue_summary_arr_up[$row["ITEM_CATEGORY"]][$row["ITEM_GROUP_ID"]][$row["ITEM_DESCRIPTION"]]["issue_qty"]+=$row["ISSUE_QTY"];
			if($row['BATCH_NO']!='' && $row['BATCH_NO']!=0)
			{
				$batch_arr=explode(",",$row['BATCH_NO']);
				foreach($batch_arr as $batch_no)
				{
					$batch_all[$batch_no]=$batch_no;
				}
			}
			
			if($row["TRANSACTION_TYPE"]==2)
			{
				if($row['REQ_NO']!='' && $row['REQ_NO']!=0)
				{
					$req_arr=explode(",",$row['REQ_NO']);
					foreach($req_arr as $req_no)
					{
						$req_all_arr[$req_no]=$req_no;
					}
				}
			}
			
			if($row["ORDER_ID"]!='' && $row["ORDER_ID"]!=0)
			{
				$po_id=array_unique(explode(",",$row["ORDER_ID"]));
				foreach($po_id as $poId)
				{
					$po_all_arr[$poId]=$poId;
				}
			}
		}
	}
	
	$con = connect();
	$r_id=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id");
	if($r_id)
	{
		oci_commit($con);
	}
	
	if(count($batch_all)>0)
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 40, 1,$batch_all, $empty_arr);
	}
	
	if(count($req_all_arr)>0)
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 40, 2,$req_all_arr, $empty_arr);
	}
	
	if(count($po_all_arr)>0)
	{
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 40, 3,$po_all_arr, $empty_arr);
	}
	
	//$requisiton_arr=return_library_array( "select id, requ_no from inv_purchase_requisition_mst where company_id=$cbo_company_name and status_active=1",'id','requ_no');
	
	//$batch_num_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');and a.company_id=$cbo_company_name
	
	
	$batch_extn_no_result=sql_select( "select a.id as ID, a.batch_no as BATCH_NO, a.extention_no as EXTENTION_NO, a.batch_against as BATCH_AGAINST , a.batch_weight as BATCH_WEIGHT,a.booking_no as BOOKING_NO, b.buyer_id as BUYER_ID, b.job_no as JOB_NO
	from wo_booking_mst b, pro_batch_create_mst a, GBL_TEMP_ENGINE c
	where a.booking_no=b.booking_no and a.id=c.ref_val and c.REF_FROM=1 and a.status_active=1
	union all
	select a.id as ID, a.batch_no as BATCH_NO, a.extention_no as EXTENTION_NO, a.batch_against as BATCH_AGAINST , a.batch_weight as BATCH_WEIGHT,a.booking_no as BOOKING_NO, b.buyer_id as BUYER_ID, b.job_no as JOB_NO
	from wo_non_ord_samp_booking_mst b, pro_batch_create_mst a, GBL_TEMP_ENGINE c 
	where a.booking_no=b.booking_no and a.id=c.ref_val and c.REF_FROM=1 and a.status_active=1
	union all
	select a.id as ID, a.batch_no as BATCH_NO, a.extention_no as EXTENTION_NO, a.batch_against as BATCH_AGAINST , a.batch_weight as BATCH_WEIGHT,a.booking_no as BOOKING_NO, null as BUYER_ID, null as JOB_NO
	from pro_batch_create_mst a, GBL_TEMP_ENGINE c where a.id=c.ref_val and c.REF_FROM=1 and a.status_active=1 and a.entry_form=36 and a.is_deleted=0");
	foreach ($batch_extn_no_result as $row) 
	{
		if($row['EXTENTION_NO']==2)
		{
			$batch_num_arr[$row['ID']]=$row['BATCH_NO'];
			$batch_extn_no_arr[$row['ID']]=$row['EXTENTION_NO'];
			$batch_wt[$row['ID']]=$row['BATCH_WEIGHT'];
			$batch_booking_no[$row['ID']]=$row['BOOKING_NO'];
			$batch_buyer[$row['ID']]=$buyer_array[$row['BUYER_ID']];
			$batch_job_no[$row['ID']]=$row['JOB_NO'];
		}
		else
		{
			$batch_num_arr[$row['ID']]=$row['BATCH_NO'];
			$batch_wt[$row['ID']]=$row['BATCH_WEIGHT'];
			$batch_booking_no[$row['ID']]=$row['BOOKING_NO'];
			$batch_buyer[$row['ID']]=$buyer_array[$row['BUYER_ID']];
			$batch_job_no[$row['ID']]=$row['JOB_NO'];
		}
	}
	//echo '<pre>';print_r($batch_num_arr);
	unset($batch_extn_no_result);
	//var_dump($batch_extn_no_arr);
	$floor_sqls=sql_select("Select a.batch_id as BATCH_ID, a.floor_id as FLOOR_ID from pro_fab_subprocess a, GBL_TEMP_ENGINE c where a.batch_id=c.ref_val and c.REF_FROM=1 and a.status_active=1 group by a.batch_id, a.floor_id");
	unset($batch_all_cond);
	$floor_data_arr=array();
	foreach($floor_sqls as $row)
	{
		$floor_data_arr[$row['BATCH_ID']]['floor_id']=$row['FLOOR_ID'];
	}
	unset($floor_sqls);
	

	$req_sql="select a.id as ID, a.requ_no as REQU_NO, a.machine_id as MACHINE_ID from dyes_chem_issue_requ_mst a, GBL_TEMP_ENGINE c  
	where a.id=c.ref_val and c.REF_FROM=2 and a.company_id=$cbo_company_name and a.status_active=1";
	$req_sql_result=sql_select($req_sql);
	foreach($req_sql_result as $val)
	{
		$dyes_Issue_requisiton_arr[$val['ID']]=$val['REQU_NO'];
		$req_machine_arr[$val['ID']]=$val['MACHINE_ID'];
	}
	unset($req_sql_result);
	$po_sqls=sql_select("Select a.id as PO_ID, a.grouping as REF_NO from wo_po_break_down a, GBL_TEMP_ENGINE c  where a.id=c.ref_val and c.REF_FROM=3 and a.status_active=1 and a.is_deleted=0");
	$po_data_arr=array();
	foreach($po_sqls as $row)
	{
		$po_data_arr[$row['PO_ID']]['ref']=$row['REF_NO'];
	}
	unset($po_sqls);
	
	$r_id=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id");
	if($r_id)
	{
		oci_commit($con);
	}
	disconnect($con);
	
	$floor_arr=return_library_array("select id,floor_name from lib_prod_floor where status_active=1 and is_deleted=0 and company_id=$cbo_company_name and production_process=3", "id","floor_name");
	$machine_arr=return_library_array( "select id, machine_no from lib_machine_name",'id','machine_no');
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$supplier_array=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	
	$group_arr=return_library_array( "select id, item_name from  lib_item_group",'id','item_name');
	$i=1;$total_receive="";$total_issue="";
	$html = '
	<div>
		<fieldset>
			<table>
					<tr>
						<td>Date Wise Item Receive Issue Report </td>
					</tr>
					<tr>
						<td>Company Name : '.$company_arr[str_replace("'","",$cbo_company_name)].'</td>
					</tr>
			</table>
			<table>
				<thead>
					<tr>
						<th>SL</th>
						<th>Prod. Id</th>
                        <th>Store</th>
						<th>Trans. Date</th>
						<th>Trans. Ref.</th>';
                        if($rptType==4 || $rptType==8)
                        {
							$html .= '<th>Dyeing Floor</th>';
                        }
                        $html .= '<th>Challan No</th>
						<th>BATCH LOT</th>
						<th>Basis</th>
                        <th>Purpose</th>';
                        if($rptType==4 || $rptType==7 || $rptType==8)
                        {
							$html .= '<th>Loan/Sales Party</th>';
                        }
                        if($rptType==8 || $rptType==9)
                        {
							$html .= '<th>Buyer</th>';
							if ($rptType==9)
							{
								$html .= '<th>Job No</th>
								<th>Booking No</th>';
							}
                        }
                        $html .= '<th>Req. No/ Batch NO/ PI/ WO</th>';
						if($rptType==4 || $rptType==8)
						{
							$html .= '<th>Internal Ref</th>';
						}

						if($rptType==7 || $rptType==4 )
						{
							$html .= '<th>Supplier</th>';
						}
						if($rptType==8 || $rptType==9)
						{
							$html .= '<th>Batch No</th>';
							if($rptType==9)
							{
								$html .= '<th>Batch Wt.</th>';
							}
							$html .= '<th>Extn No</th>
							<th>Recipe No</th>';
							if($rptType==9)
							{
								$html .= '<th>Ratio</th>';
							}
							$html .= '<th>Machine No</th>';
						}
						$html .= '<th>Category</th>
						<th>Item Group</th>
						<th>Item Sub-Group</th>
                        <th>Item Code</th>
						<th>Item Description</th>';
						if($rptType==7)
						{
							$html .= '<th>Manufacture Date</th>
							<th>Expire Date</th>';
						}
                        if($rptType==7 || $rptType==4 )
                        {
                            $html .= '<th>Currency</th>
                            <th>Exchange Rate</th>
                            <th>Actual Rate</th>';
                        }
                        $html .= '<th>UOM</th>';
                        if($rptType==7 || $rptType==4 )
                        {
                            $html .= '<th>Receive Qty</th>
                            <th>Actual Amt</th>';
                        }
                        if($rptType==8 || $rptType==4 || $rptType==9)
                        {
                             $html .= '<th>Issue Qty</th>';
                        }
                        $html .= '<th>Rate(TK)</th>
						<th>Amount(TK)</th>';
                        if($rptType==4 || $rptType==7 || $rptType==8)
                        {
							$html .= '<th>Remarks</th>';
                        }
                        $html .= '<th>User</th>
						<th>Accounting Posting</th>
						<th>Insert Date</th>';
                        if($rptType==4 || $rptType==7 || $rptType==8)
                        {
							$html .= '<th>Zero Discharge</th>';
                        }
					$html .= '</tr>
				</thead>
		    </table>
			
		  	<div>
				<table>
					<tbody>';
					
					foreach($sql_result as $row)
					{
						$ref_no='';$floor_no='';
						if($row["ORDER_ID"]!='' && $row["ORDER_ID"]!=0){
							$po_id=array_unique(explode(",",$row["ORDER_ID"]));
							foreach($po_id as $poId)
							{
								if($ref_no!='') {$ref_no.=", ".$po_data_arr[$poId]['ref'];} else  {$ref_no=$po_data_arr[$poId]['ref'];}	
							}
						}
						if($row["BATCH_NO"]!='' && $row["BATCH_NO"]!=0){
							$batch_id=array_unique(explode(",",$row["BATCH_NO"]));
							foreach($batch_id as $batchId)
							{
								if($floor_no!='') {$floor_no.=", ".$floor_arr[$floor_data_arr[$batchId]['floor_id']];} 
								else{$floor_no=$floor_arr[$floor_data_arr[$batchId]['floor_id']];}	
							}
						}
						$html .= '<tr>
							<td>'.$i.'</td>
							<td>'.$row["PROD_ID"].'</td>
                            <td>'.$store_arr[$row["STORE_ID"]].'</td>
							<td>'. change_date_format($row["TRANSACTION_DATE"]).'</td>
							<td>'.$row["RECV_ISSUE_NUM"].'</td>';
							if($rptType==4 || $rptType==8)
							{
								$html .= '<td>'.$floor_no.'</td>';
							}
							$html .= '<td>'.$row['CHALLAN_NO'].'</td>
							<td>'.$row['BATCH_LOT'].'</td>
							<td>'.$receive_basis_arr[$row["RECEIVE_ISSUE_BASIS"]].'</td>
							<td>'.$general_issue_purpose[$row['RECEIVE_ISSUE_PURPOSE']].'</td>';
							if($rptType==4 || $rptType==7 || $rptType==8)
							{
								$html .= '<td>'.$loan_party_arr[$row['LOAN_PARTY']].'</td>';
							}
							if($rptType==8 || $rptType==9)
							{
								if($rptType==9)
								{
									$html .= '<td>'.$batch_buyer[$row['BATCH_NO']].'</td>
									<td>'.$batch_job_no[$row['BATCH_NO']].'</td>
									<td>'.$batch_booking_no[$row['BATCH_NO']].'</td>';
								}
								else
								{
									$html .= '<td>'.$buyer_array[$row['BUYER_ID']].'</td>';
								}
							}
							$html .= '<td>';
							if($row["TRANSACTION_TYPE"]==1)
							{
								if($row["RECEIVE_ISSUE_BASIS"]==1 || $row["RECEIVE_ISSUE_BASIS"]==2)
								{
									$html .= $row["BOOKING_NO"];
								}
								else
								{
									$html .= "Independent";
								}
							}
							else if($row["TRANSACTION_TYPE"]==2)
							{
								if($row["RECEIVE_ISSUE_BASIS"]==5 )
								{
									$html .= $batch_num_arr[$row["BATCH_NO"]];
								}
								else if($row["RECEIVE_ISSUE_BASIS"]==7)
								{
									$html .= $dyes_Issue_requisiton_arr[$row["REQ_NO"]];
								}
								else
								{
									$html .= "Independent";
								}
							}
							else if($row["TRANSACTION_TYPE"]==3)
							{
								$html .= "Receive Return";
							}
							else if($row["TRANSACTION_TYPE"]==4)
							{
								$html .= "Issue Return";
							}
							else
							{
								$html .= "Transfer";
							}
							$html .= '</td>';
							if($rptType==4 || $rptType==8)
							{
								$html .= '<td>'.$ref_no.'</td>';
							}
							if($rptType==7 || $rptType==4 )
							{
								$html .= '<td>'.$supplier_array[$row['SUPPLIER_ID']].'</td>';
							}
							if($rptType==8 || $rptType==9)
							{
								if (strpos($row["BATCH_NO"], ',') !== false) 
								{
									$batch = explode(",",$row["BATCH_NO"]);
									$batch_nos = '';
									for($k=0; $k < count($batch); $k++){
										$batch_nos .=$batch_num_arr[$batch[$k]].",";
									}
									$html .= '<td>'.chop($batch_nos,',').'</td>';
								}
								else
								{
									$html .= '<td>'.$batch_num_arr[$row["BATCH_NO"]].'</td>';
								}
															
								if($rptType==9) 
								{
									$html .= '<td>'.$batch_wt[$row["BATCH_NO"]].'</td>';
								}
								$html .= '<td>'.$batch_extn_no_arr[$row["BATCH_NO"]].'</td>
								<td>'.$row["RECIPE_ID"].'</td>';
								if($rptType==9)
								{
									$html .= '<td>'.$row["RATIO"].'</td>';
								}
								$html .= '<td>';
								if($row["RECEIVE_ISSUE_BASIS"]==7) $html .= $machine_arr[$req_machine_arr[$row["REQ_NO"]]];
                                $html .= '</td>';
							}
							$html .= '<td>'.$item_category[$row['ITEM_CATEGORY']].'</td>
							<td>'.$group_arr[$row['ITEM_GROUP_ID']].'</td>
							<td>'.$row['SUB_GROUP_NAME'].'</td>
	                        <td>'.$row['ITEM_CODE'].'</td>
							<td>'.$row['ITEM_DESCRIPTION'].'</td>';
							if($rptType==7)
							{
								$html .= '<td>'.change_date_format($row["MANUFACTURE_DATE"]).'</td>
								<td>'.change_date_format($row["EXPIRE_DATE"]).'</td>';
							} 

							if($rptType==7 || $rptType==4 )
							{
								$html .= '<td>';
								if($row["TRANSACTION_TYPE"]==1) $html .=$currency[$row['CURRENCY_ID']]; 
								$html .= '</td>
								<td>';
								if($row["TRANSACTION_TYPE"]==1) $html .=number_format($row['EXCHANGE_RATE'],2); 
								$html .= '</td>
								<td>';
								if($row["TRANSACTION_TYPE"]==1) $html .=number_format($row['ORDER_RATE'],2); 
								$html .= '</td>';
							}
							$html .= '<td>'.$unit_of_measurement[$row['CONS_UOM']].'</td>';
							if($rptType==7 || $rptType==4 )
							{
								$html .= '<td>'.number_format($row["RECEIVE_QTY"],2); 
								$total_receive +=$row["RECEIVE_QTY"]; 
								$html .= '</td>
								<td>';
								if($row["TRANSACTION_TYPE"]==1) 
								{
									$order_amt=$row['ORDER_QNTY']*$row['ORDER_RATE']; 
									$html .= number_format($order_amt,4); 
									$total_order_amt+=$order_amt; $order_amt=0;
								}
								$html .= '</td>';
							}
							if($rptType==8 || $rptType==4 || $rptType==9)
							{
								$html .= '<td>'.number_format($row["ISSUE_QTY"],2,'.',''); 
								$total_issue +=$row["ISSUE_QTY"]; 
								$html .= '</td>';
							}
							$html .= '<td>'.number_format($row["CONS_RATE"],2,'.','').'</td>
							<td>'.number_format($row["CONS_AMOUNT"],2,'.',''); 
							$total_amount +=$row["CONS_AMOUNT"]; 
							$html .= '</td>';
							if($rptType==4 || $rptType==7 || $rptType==8)
							{
								$html .= '<td>'.$row['REMARKS'].'</td>';
							}
							$html .= '<td>'.$user_name_arr[$row["INSERTED_BY"]].'</td>
							<td>'.$account_posted_arr[$row["IS_POSTED_ACCOUNT"]].'</td>
							<td>'.change_date_format($row["INSERT_DATE"])." ".$row["INSERT_TIME"].'</td>';
							if($rptType==4 || $rptType==7 || $rptType==8)
							{
								$html .= '<td>'.$compliance_arr[$row['IS_COMPLIANCE']].'</td>';
							}
							
						$html .= '</tr>';
						$i++;
					}
					$html .= '</tbody>
				</table>
            </div>
            <div>
				<table>
					<tfoot>
						<tr>
	                    	<th></th>
							<th></th>
                            <th></th>
							<th></th>
							<th></th>';
							if($rptType==4 || $rptType==8)
							{
								$html .= '<th></th>';
							}
							$html .= '<th></th>
							<th></th>
							<th></th>
	                        <th></th>';
							if($rptType==4 || $rptType==7 || $rptType==8 )
							{
								$html .= '<th></th>';
							}
							if($rptType==8 || $rptType==9)
							{
								$html .= '<th></th>';
								if($rptType==9)
								{
									$html .= '<th></th>
									<th></th>';
								}
							}
							$html .= '<th></th>';
							if($rptType==4 || $rptType==8)
							{
								?>
								<th width="100" >&nbsp;</th>
								<?
							}

							if($rptType==7 || $rptType==4 )
							{
								$html .= '<th></th>';
							}
							if($rptType==8 || $rptType==9)
							{
								$html .= '<th></th>';
                                if($rptType==9)
                                {
									$html .= '<th></th>';
                                }
                                $html .= '<th></th>
								<th></th>';
                                if($rptType==9)
                                {
									$html .= '<th></th>';
                                }
                                $html .= '<th></th>';
							}
							 $html .= '<th></th>
							<th></th>
							<th></th>
	                        <th></th>
							<th></th>';
							if($rptType==7)
							{
								$html .= '<th></th>
								<th></th>';
							}

							if($rptType==7 || $rptType==4 )
							{
								$html .= '<th></th>
								<th></th>
								<th></th>';
							}
							$html .= '<th>Total</th>';
							
                            if($rptType==7 || $rptType==4 )
                            {
                                $html .= '<th>'.number_format($total_receive,2,'.','').'</th>
                                <th></th>';
                            }

                            if($rptType==8 || $rptType==4 || $rptType==9 )
                            {
                                $html .= '<th>'.number_format($total_issue,2,'.','').'</th>';
                            }
                            $html .= '<th></th>
							<th>'.number_format($total_amount,2,'.','').'</th>';
                            if($rptType==4 || $rptType==7 || $rptType==8)
                            {
								$html .= '<th></th>';
                            }
                            $html .= '<th></th>
							<th></th>
							<th></th>';
                            if($rptType==4 || $rptType==7 || $rptType==8)
                            {
								$html .= '<th></th>';
                            }
						$html .= '</tr>
					</tfoot>
				</table>
		 	</div>';
			if($rptType==8 || $rptType==7 || $rptType==9)
			{
				$html .= '<div>
		            <table><caption>'; 
		             		if($rptType==8 || $rptType==9)
							{
								$html .= 'Issue Summary';
								$col_head="Issue Qty";
							}
							else if($rptType==7)
							{
								$col_head="Receive Qty";
								$html .= 'echo "Receive Summary';
							}
							else if($rptType==4)
							{
								$html .= 'Receive & Issue Summary';
							}		                    
		            	$html .= '</caption><thead>
			                <tr>
			                    <th>SL</th>
			                    <th>Category</th>
			                    <th>Item Group</th>
			                    <th>Item Desc.</th>
			                    <th>'.$col_head.'</th>
			                    <th>Avg. Rate.</th>
			                    <th>Issue Value</th>
			              	</tr>
		              	</thead>
						<tbody>';
							$k=1;$tot_val=$tot_issue_value=0;
							foreach($issue_summary_arr as $cat=>$item_data)
							{
								foreach($item_data as $item_key=>$des_data)
								{
									foreach($des_data as $des_key=>$qty_val)
								  	{
										foreach($qty_val as $val)
								  		{
											$tot_val+=$val;
											$tot_rate=$issue_summary_avg_rate_arr[$cat][$item_key][$des_key]['cons_amount']/$val;

										  	$html .= '<tr>
							                  <td>'.$k.'</td>
							                  <td>'.$item_category[$cat].'</td>
							                  <td>'.$group_arr[$item_key].'</td>
							                  <td>'.$des_key.'</td>
							                  <td>'.number_format($val,2).'</td>
							                  <td>'.number_format($tot_rate,2).'</td>
							                  <td>'.number_format($issue_value=($val*$tot_rate),2).'</td>
							              	</tr>';     
							              	$k++;
							              	$tot_issue_value+=$issue_value;
							  		 	}
									}
								}
							}
						$html .= '</tbody>
			            <tfoot>
			              	<th></th>
			                <th></th>
			                <th></th>
			                <th>Total</th>
			                <th>'.$tot_val.'</th>
			                <th></th>
			                <th>'.number_format($tot_issue_value,2).'</th>
			            </tfoot>
		            </table>
				</div>';
			}
			if($rptType==4 && count($issue_summary_arr_up)>0)
			{
				$html .= '<div>
					<table>
						<caption>Receive And Issue Summary</caption>
			            <thead>
			                <tr>
			                    <th>SL</th>
			                    <th>Category</th>
			                    <th>Item Group</th>
			                    <th>Item Desc.</th>
			                    <th>Recv. Qty.</th>
			                    <th>Issue Qty.</th>
							</tr>
						</thead>
			            <tbody>';
							$k=1;$tot_issue_qty=0;$total_recv_qty=0;
							foreach($issue_summary_arr_up as $cat=>$item_data)
							{
								foreach($item_data as $item_key=>$des_data)
								{
									foreach($des_data as $des_key=>$qty_val)
								  	{
										foreach($qty_val as $qty)
								  		{
											$tot_issue_qty+=$qty;
											$tot_recv=$issue_recv_summary_arr_up[$cat][$item_key][$des_key]['receive_qty'];
											$total_recv_qty+=$tot_recv;
											$html .= '<tr>
												<td>'.$k.'</td>
												<td>'.$item_category[$cat].'</td>
												<td>'.$group_arr[$item_key].'</td>
												<td>'.$des_key.'</td>
												<td>'.number_format($tot_recv,2).'</td>
												<td>'.number_format($qty,2).'</td>
											</tr>';
							  			}
									}
								}
							}
			            $html .= '</tbody>
						<tfoot>
							<th></th>
							<th></th>
							<th></th>
							<th>Total</th>
							<th>'.$total_recv_qty.'</th>
							<th>'.$tot_issue_qty.'</th>
						</tfoot>
					</table>
				</div>';
			}
	$html .= '</div>';
	
	
	
			
	foreach (glob("bwffsr_$user_id*.xlsx") as $filename) {
		@unlink($filename);
	}
	$name=time();
	$filename='bwffsr_'.$user_id."_".$name.".xlsx";
	//echo "$html####$filename"; die;
	//echo $filename;die;
	
	$reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
	$spreadsheet = $reader->loadFromString($html);

	//$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
	//$writer->save($filename); 
	$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
	$writer->save($filename); 
	
	//header('Content-Type: application/x-www-form-urlencoded');
	//header('Content-Transfer-Encoding: Binary');
	//header("Content-disposition: attachment; filename=\"".$filename."\"");

    echo "$filename####$filename####$return_item_cat####$rptType";
	exit();
}

disconnect($con);
?>

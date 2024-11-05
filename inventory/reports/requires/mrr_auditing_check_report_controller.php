<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../../includes/common.php');

$user_id	= $_SESSION['logic_erp']['user_id'];
$sessionUserID	= $_SESSION['logic_erp']['user_id'];
$data		= $_REQUEST['data'];
$action		= $_REQUEST['action'];
$menu_id	= $_SESSION['menu_id'];

/*$userPrevItemcategory=return_field_value("item_cate_id", "user_passwd", "valid=1 and id='".$user_id."'","item_cate_id");*/

if ($action=="load_drop_down_location")
{
    echo create_drop_down( "cbo_location_id", 80, "select id,location_name from lib_location where company_id='$data' and status_active=1 and is_deleted=0 order by location_name","id,location_name", 1, "-- All --", 0, "load_drop_down( 'requires/mrr_auditing_report_controller', this.value+'**'+$data, 'load_drop_down_store', 'com_store_td' );" );
    exit();
}


if ($action=="load_drop_down_store")
{
    $data=explode('**',$data);
    if ($data[1]=='')  // company to store
    {
        echo create_drop_down( "cbo_store_name", 100, "select a.id, a.store_name from lib_store_location a, lib_location b where a.location_id=b.id and b.company_id=$data[0] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by store_name","id,store_name", 1, "--Select Store--", 0, "");
    }
    else if ($data[0]==0)  // all location cond
    {
        echo create_drop_down( "cbo_store_name", 100, "select a.id, a.store_name from lib_store_location a, lib_location b where a.location_id=b.id and b.company_id=$data[1] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by store_name","id,store_name", 1, "--Select Store--", 0, "");
    }
    else  // location id wise store
    {
        echo create_drop_down( "cbo_store_name", 100, "select a.id, a.store_name from lib_store_location a, lib_location b where a.location_id=b.id and b.id=$data[0] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by store_name","id,store_name", 1, "--Select Store--", 0, "");
    }
    exit();
}

if ($action=="load_drop_down_supplier")
{
    echo create_drop_down( "cbo_suppler_name", 100, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_tag_company b  where a.id=b.supplier_id and b.tag_company='$data' and a.status_active=1 and a.is_deleted=0 order by a.supplier_name","id,supplier_name", 1, "-- Select Supplier --", 0, "" );
    exit();
}


if($action=="report_generate")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));

    $company_name		= str_replace("'","",$cbo_company_name);
    $cbo_location_id	= str_replace("'","",$cbo_location_id);
    $cbo_store_name		= str_replace("'","",$cbo_store_name);
    $cbo_item_category_id = str_replace("'","",$cbo_item_category_id);
    $txt_challan_no		= str_replace("'","",$txt_challan_no);
    $txt_mrr_no			= str_replace("'","",$txt_mrr_no);
    $txt_date_from		= str_replace("'","",$txt_date_from);
    $txt_date_to		= str_replace("'","",$txt_date_to);
    $cbo_suppler_name	= str_replace("'","",$cbo_suppler_name);
    $txt_wo_no			= str_replace("'","",$txt_wo_no);
    $txt_bill_no			= str_replace("'","",$txt_bill_no);
    $txt_pi_no			= str_replace("'","",$txt_pi_no);
    $cbo_audit_type		= str_replace("'","",$cbo_audit_type);
    $cbo_date_basis		= str_replace("'","",$cbo_date_basis);
    $cbo_year           = str_replace("'","",$cbo_year);

    $search_cond = '';
    //if ($cbo_location_id>0) $search_cond .= " and a.location_id=$cbo_location_id";
    // Location filter only search panel. Not filter data lvel
    if ($cbo_store_name>0) $search_cond .= " and a.store_id=$cbo_store_name";
    if ($txt_challan_no != '') $search_cond .= " and a.challan_no='$txt_challan_no'";
    if ($txt_mrr_no != '') $search_cond .= " and a.recv_number_prefix_num=$txt_mrr_no";
    if ($cbo_suppler_name>0) $search_cond .= " and a.supplier_id=$cbo_suppler_name";
    if ($txt_wo_no != '') $search_cond .= " and a.booking_no like '%$txt_wo_no'";
    if ($txt_bill_no != '') $search_cond .= " and e.BILL_NO like '%$txt_bill_no'";
    if ($txt_pi_no != '') $search_cond .= " and a.booking_no='$txt_pi_no'";

    if($cbo_audit_type == 1){
        $search_cond .= " and a.is_audited = 1";
    }elseif($cbo_audit_type == 2){
        $search_cond .= " and a.is_audited = 0";
    }

    $category_cond='';
    if ($cbo_item_category_id>0) $category_cond = " and b.item_category=$cbo_item_category_id";

    /*========== user credential  ========*/
    $userCredential = sql_select("select unit_id as COMPANY_ID, item_cate_id as ITEM_CATE_ID, company_location_id as COMPANY_LOCATION_ID, store_location_id as STORE_LOCATION_ID from user_passwd where id=$user_id");
    $category_credential_id = $userCredential[0]['ITEM_CATE_ID'];

    if ($category_credential_id !='') {
        if ($cbo_item_category_id>0) $category_cond = " and b.item_category=$cbo_item_category_id"; //Credential category search
        else $category_cond = " and b.item_category in($category_credential_id)"; // All credential category
    }
    /*========== End user credential  ========*/

    if ($txt_date_from != '' && $txt_date_to != '')
    {
        if($db_type==0)
        {
            $txt_date_from 	= date("Y-m-d", strtotime($txt_date_from));
            $txt_date_to 	= date("Y-m-d", strtotime($txt_date_to));
        }
        else
        {
            $txt_date_from 	= date("d-M-Y", strtotime($txt_date_from));
            $txt_date_to 	= date("d-M-Y", strtotime($txt_date_to));
        }
        if($cbo_date_basis==1){
            $date_cond 		= " and a.receive_date between '$txt_date_from' and '$txt_date_to'";
        }else {
            $date_cond 		= " and a.audit_date between '$txt_date_from' and '$txt_date_to'";

        }
    }


    $company_arr	= return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
    $company_fullname_arr = return_library_array( "select id, company_name from lib_company",'id','company_name');
    $supplier_arr	= return_library_array( "select id,supplier_name from lib_supplier",'id','supplier_name');
    $store_arr 		= return_library_array("select id,store_name from lib_store_location", "id", "store_name");
    $user_id 		= return_library_array("select id,user_name from user_passwd", "id", "user_name");
    $user_name 		= return_library_array("select id, user_full_name from user_passwd", "id", "user_full_name");
    $item_category_name = return_library_array( "select id, short_name from lib_item_category_list",'id','short_name');

    if($db_type==0) {
        $itemCat = "  group_concat(distinct(b.item_category))";
        $pi_wo_batch = " group_concat(distinct(b.pi_wo_batch_no))";
    } else {
        $itemCat = " listagg(b.item_category ,',') within group (order by b.item_category)";
        $pi_wo_batch = " listagg(b.pi_wo_batch_no ,',') within group (order by b.pi_wo_batch_no)";
    }

    if ($db_type == 0)
		{
			if($cbo_year>0)
			{
				$year_cond=" and YEAR(a.insert_date)=$cbo_year";                 
			}
		}
		else if ($db_type == 2)
		{
			if($cbo_year>0)
			{
				$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";                 
			}
		}
	    else {
                $year_cond="";                 
	    }   

    //main Query
    if( $cbo_item_category_id==13) // Grey Fabric knit 
    {
        $sql_mrr="SELECT a.ID, a.COMPANY_ID, a.ENTRY_FORM, a.LOCATION_ID, a.RECV_NUMBER, a.STORE_ID, a.CHALLAN_NO, a.RECV_NUMBER_PREFIX_NUM, a.RECEIVE_BASIS, a.RECEIVE_DATE, a.CURRENCY_ID, a.EXCHANGE_RATE, a.BOOKING_ID, a.BOOKING_NO, a.SUPPLIER_ID, a.LC_NO, a.AUDIT_BY, a.AUDIT_DATE, a.AUDIT_REMARK, a.IS_AUDITED, $itemCat as ITEM_CATEGORY, sum(b.cons_amount) as CONS_AMOUNT, sum(CASE WHEN a.entry_form=24 and b.payment_over_recv=0 and b.item_category=4 THEN b.cons_amount ELSE 0 END) AS CONS_AMOUNT_ACCESSORIES, sum(b.order_amount) as ORDER_AMOUNT, sum(CASE WHEN a.entry_form=24 and b.payment_over_recv=0 and b.item_category=4 THEN b.order_amount ELSE 0 END) AS ORDER_AMOUNT_ACCESSORIES, $pi_wo_batch as PI_WO_BATCH_ID, a.KNITTING_SOURCE, a.KNITTING_COMPANY, a.VARIABLE_SETTING
        from inv_transaction b, product_details_master c ,inv_receive_master a
        left join SUBCON_OUTBOUND_BILL_DTLS d on a.id = d.RECEIVE_ID  and d.status_active=1 
        left join SUBCON_OUTBOUND_BILL_MST e on  d.mst_id = e.id   and e.status_active=1 
        where a.id=b.mst_id and b.prod_id=c.id and a.company_id='$company_name' and b.transaction_type=1 and a.is_posted_account=0 $date_cond $year_cond $search_cond $category_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
        group by a.id, a.company_id, a.entry_form, a.location_id, a.recv_number, a.store_id, a.challan_no, a.recv_number_prefix_num, a.receive_basis, a.receive_date, a.currency_id, a.exchange_rate, a.booking_id, a.booking_no, a.supplier_id, a.lc_no, a.audit_by, a.audit_date, a.audit_remark, a.is_audited, a.knitting_source, a.knitting_company, a.variable_setting
        order by a.audit_date";
    }
    else if( $cbo_item_category_id==2) // knit finish fabric  
    {
        $sql_mrr="SELECT a.ID, a.COMPANY_ID, a.ENTRY_FORM, a.LOCATION_ID, a.RECV_NUMBER, a.STORE_ID, a.CHALLAN_NO, a.RECV_NUMBER_PREFIX_NUM, a.RECEIVE_BASIS, a.RECEIVE_DATE, a.CURRENCY_ID, a.EXCHANGE_RATE, a.BOOKING_ID, a.BOOKING_NO, a.SUPPLIER_ID, a.LC_NO, a.AUDIT_BY, a.AUDIT_DATE, a.AUDIT_REMARK, a.IS_AUDITED, $itemCat as ITEM_CATEGORY, sum(b.cons_amount) as CONS_AMOUNT, sum(CASE WHEN a.entry_form=24 and b.payment_over_recv=0 and b.item_category=4 THEN b.cons_amount ELSE 0 END) AS CONS_AMOUNT_ACCESSORIES, sum(b.order_amount) as ORDER_AMOUNT, sum(CASE WHEN a.entry_form=24 and b.payment_over_recv=0 and b.item_category=4 THEN b.order_amount ELSE 0 END) AS ORDER_AMOUNT_ACCESSORIES, $pi_wo_batch as PI_WO_BATCH_ID, a.KNITTING_SOURCE, a.KNITTING_COMPANY, a.VARIABLE_SETTING
        from inv_transaction b, product_details_master c ,inv_receive_master a
        left join PRO_FINISH_FABRIC_RCV_DTLS d on a.id = d.mst_id  and d.status_active=1 
        left join ORDER_WISE_PRO_DETAILS g on d.id=g.dtls_id  and g.status_active=1 
        left join SUBCON_OUTBOUND_BILL_DTLS f on f.RECEIVE_ID = g.id  and f.status_active=1 
        left join SUBCON_OUTBOUND_BILL_MST e on  f.mst_id = e.id   and e.status_active=1 
        where a.id=b.mst_id and b.prod_id=c.id and a.company_id='$company_name' and b.transaction_type=1 and a.is_posted_account=0 $date_cond $year_cond $search_cond $category_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
        group by a.id, a.company_id, a.entry_form, a.location_id, a.recv_number, a.store_id, a.challan_no, a.recv_number_prefix_num, a.receive_basis, a.receive_date, a.currency_id, a.exchange_rate, a.booking_id, a.booking_no, a.supplier_id, a.lc_no, a.audit_by, a.audit_date, a.audit_remark, a.is_audited, a.knitting_source, a.knitting_company, a.variable_setting
        order by a.audit_date";
    }
    else
    {
        $sql_mrr="SELECT a.ID, a.COMPANY_ID, a.ENTRY_FORM, a.LOCATION_ID, a.RECV_NUMBER, a.STORE_ID, a.CHALLAN_NO, a.RECV_NUMBER_PREFIX_NUM, a.RECEIVE_BASIS, a.RECEIVE_DATE, a.CURRENCY_ID, a.EXCHANGE_RATE, a.BOOKING_ID, a.BOOKING_NO, a.SUPPLIER_ID, a.LC_NO, a.AUDIT_BY, a.AUDIT_DATE, a.AUDIT_REMARK, a.IS_AUDITED, $itemCat as ITEM_CATEGORY, sum(b.cons_amount) as CONS_AMOUNT, sum(CASE WHEN a.entry_form=24 and b.payment_over_recv=0 and b.item_category=4 THEN b.cons_amount ELSE 0 END) AS CONS_AMOUNT_ACCESSORIES, sum(b.order_amount) as ORDER_AMOUNT, sum(CASE WHEN a.entry_form=24 and b.payment_over_recv=0 and b.item_category=4 THEN b.order_amount ELSE 0 END) AS ORDER_AMOUNT_ACCESSORIES, $pi_wo_batch as PI_WO_BATCH_ID, a.KNITTING_SOURCE, a.KNITTING_COMPANY, a.VARIABLE_SETTING
        from inv_receive_master a, inv_transaction b, product_details_master c 
        where a.id=b.mst_id and b.prod_id=c.id and a.company_id='$company_name' and b.transaction_type=1 and a.is_posted_account=0 $date_cond $year_cond $search_cond $category_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
        group by a.id, a.company_id, a.entry_form, a.location_id, a.recv_number, a.store_id, a.challan_no, a.recv_number_prefix_num, a.receive_basis, a.receive_date, a.currency_id, a.exchange_rate, a.booking_id, a.booking_no, a.supplier_id, a.lc_no, a.audit_by, a.audit_date, a.audit_remark, a.is_audited, a.knitting_source, a.knitting_company, a.variable_setting
        order by a.audit_date";
    }
   


   // echo $sql_mrr; //die;

    $sql_mrr_res = sql_select($sql_mrr);
    $tot_rows=0;
    foreach ($sql_mrr_res as $val) {
        $tot_rows++;
        if ($val['RECEIVE_BASIS']==1 && $val['BOOKING_ID'] != 0) {
            $pi_Ids .= $val['BOOKING_ID'].',';
        }
        if ($val['ENTRY_FORM']==7 && $val['PI_WO_BATCH_ID'] !='' &&  $val['PI_WO_BATCH_ID'] !=0){
            $batch_Ids .= $val['PI_WO_BATCH_ID'].',';
        }

        if ($val['RECEIVE_BASIS'] ==2 && $val['BOOKING_ID'] != 0) {
            $work_order_Ids .= $val['BOOKING_ID'].',';
        }
    }

    if ($pi_Ids != '')
    {
        $pi_Ids = array_flip(array_flip(explode(',', rtrim($pi_Ids,','))));
        $pi_id_cond = '';

        if($db_type==2 && $tot_rows>1000)
        {
            $pi_id_cond = ' and (';
            $piIdArr = array_chunk($pi_Ids,999);
            foreach($piIdArr as $ids)
            {
                $ids = implode(',',$ids);
                $po_id_cond .= " a.id in($ids) or ";
            }
            $pi_id_cond = rtrim($po_id_cond,'or ');
            $pi_id_cond .= ')';
        }
        else
        {
            $pi_Ids = implode(',', $pi_Ids);
            $pi_id_cond=" and a.id in($pi_Ids)";
        }

        $sql_pi_lc="SELECT a.id as PI_ID, b.WORK_ORDER_NO, d.LC_NUMBER
	    from com_pi_master_details a, com_pi_item_details b 
	    left join com_btb_lc_pi c on b.pi_id=c.pi_id and c.status_active=1 
	    left join com_btb_lc_master_details d on c.com_btb_lc_master_details_id=d.id and d.status_active=1 
	    where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $pi_id_cond
	    group by a.id, b.work_order_no, d.lc_number";

        $sql_pi_lc_res = sql_select($sql_pi_lc);
        $work_order_no_arr=array();
        $lc_number_arr=array();
        foreach ($sql_pi_lc_res as $val) {
            if ($val['LC_NUMBER'] != '') {
                $lc_number_arr[$val['PI_ID']]['LC_NUMBER']=$val['LC_NUMBER'];
            }

            if ($work_order_no_arr[$val['PI_ID']][$val['WORK_ORDER_NO']] == '') {
                $work_order_no_arr[$val['PI_ID']][$val['WORK_ORDER_NO']] = $val['WORK_ORDER_NO'];
                $work_order_no_arr[$val['PI_ID']]['WORK_ORDER_NO'] .= $val['WORK_ORDER_NO'].',';
            }
        }
    }


    if ($batch_Ids != '')
    {
        $batch_Ids = implode(',',array_flip(array_flip(explode(',', rtrim($batch_Ids,',')))));
        $sql_batch_booking = sql_select("select id as BATCH_ID, BOOKING_NO from pro_batch_create_mst where id in($batch_Ids) and status_active=1 ");
        $batch_booking_arr=array();
        foreach ($sql_batch_booking as $val) {
            $batch_booking_arr[$val['BATCH_ID']]=$val['BOOKING_NO'];
        }
    }

    if ($work_order_Ids != '')
    {
        $work_order_Ids = array_flip(array_flip(explode(',', rtrim($work_order_Ids,','))));
        $work_order_id_cond = '';

        if($db_type==2 && $tot_rows>1000)
        {
            $work_order_id_cond = ' and (';
            $workOrderIdArr = array_chunk($pi_Ids,999);
            foreach($workOrderIdArr as $ids)
            {
                $ids = implode(',',$ids);
                $work_order_id_cond .= " b.work_order_id in($ids) or ";
            }
            $work_order_id_cond = rtrim($po_id_cond,'or ');
            $work_order_id_cond .= ')';
        }
        else
        {
            $work_order_Ids = implode(',', $work_order_Ids);
            $work_order_id_cond=" and b.work_order_id in($work_order_Ids)";
        }

        $sql_afterGoodRecPiLc="SELECT a.id as PI_ID, a.PI_NUMBER, b.WORK_ORDER_ID, b.WORK_ORDER_NO, d.LC_NUMBER
	    from com_pi_master_details a, com_pi_item_details b 
	    left join com_btb_lc_pi c on b.pi_id=c.pi_id and c.status_active=1 
	    left join com_btb_lc_master_details d on c.com_btb_lc_master_details_id=d.id and d.status_active=1 
	    where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and a.importer_id = $company_name and b.status_active=1 and b.is_deleted=0 $work_order_id_cond
	    group by a.id, a.pi_number, b.work_order_id, b.work_order_no, d.lc_number";

        $sql_afterGoodRecPiLc_res = sql_select($sql_afterGoodRecPiLc);
        $fterGoodRec_PiNo_arr=array();
        $fterGoodRec_lc_number_arr=array();
        foreach ($sql_afterGoodRecPiLc_res as $val) {
            if ($val['LC_NUMBER'] != '') {
                $fterGoodRec_lc_number_arr[$val['WORK_ORDER_ID']]['LC_NUMBER']=$val['LC_NUMBER'];
            }

            if ($fterGoodRec_PiNo_arr[$val['WORK_ORDER_ID']][$val['PI_NUMBER']] == '') {
                $fterGoodRec_PiNo_arr[$val['WORK_ORDER_ID']][$val['PI_NUMBER']] = $val['PI_NUMBER'];
                $fterGoodRec_PiNo_arr[$val['WORK_ORDER_ID']]['PI_NUMBER'] = $val['PI_NUMBER'];
            }
        }
    }
    $trims_booking_report_format = 	return_field_value("format_id","lib_report_template","template_name ='".$company_name."' and module_id=6 and report_id = 191 and is_deleted=0 and status_active=1");
    $trims_booking_report_format_ids=explode(",",$trims_booking_report_format);
    $trim_report_action = "";
    if(count($trims_booking_report_format_ids) > 0) {
        if ($trims_booking_report_format_ids[0] == 86) {
            $trim_report_action = "trims_receive_entry_print";
        }elseif ($trims_booking_report_format_ids[0] == 116) {
            $trim_report_action = "trims_receive_entry_print_2";
        }
        elseif ($trims_booking_report_format_ids[0] == 136) {

            $trim_report_action = "trims_receive_entry_print_4";
        }
    }

       $print_report_format=return_field_value("format_id"," lib_report_template","template_name =$company_name  and module_id=6 and report_id=171 and is_deleted=0 and status_active=1");
		$print_report_format_ids=explode(",",$print_report_format);
        $print_btn=$print_report_format_ids[0];




    //echo '<pre>';print_r($batch_booking_arr);die;
    //$currency_conv_rate = return_field_value("conversion_rate","currency_conversion_rate","currency=1 and company_id=$company_name and status_active=1 and is_deleted=0 order by con_date desc");
    $sql_currency=sql_select("select CONVERSION_RATE from currency_conversion_rate where currency=2 and company_id=$company_name and status_active=1 and is_deleted=0 order by con_date desc");
    $conversion_rate = $sql_currency[0]['CONVERSION_RATE'];

    $woven_fab_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$company_name."'  and module_id=6 and report_id=125 and is_deleted=0 and status_active=1");
    $woven_fab_report_format_arr=explode(",",$woven_fab_report_format);
    $report_action = "";
    $format_id = 0;
    if(count($woven_fab_report_format_arr) > 0){
        if($woven_fab_report_format_arr[0] == 66){
            $report_action = "gwoven_finish_fabric_receive_print_3";
            $report_action1 = "gwoven_finish_fabric_receive_print_2";
            $format_id = 66;
        }elseif($woven_fab_report_format_arr[0] == 78){
            $report_action = "gwoven_finish_fabric_receive_print";
            $format_id = 78;
        }
    }


    $tableWidth= 1820;
    ob_start();
    ?>
    <form name="mrraudit_2" id="mrraudit_2">
        <fieldset style="width:<? echo $tableWidth ; ?>px; margin-top:10px">
            <legend>MRR Auditing Check Report</legend>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tableWidth-20 ; ?>" class="rpt_table" align="left">
                <thead>
                <th width="30">SL</th>
                <th width="80">Company</th>
                <th width="100">Item Category</th>
                <th width="80">Store</th>
                <th width="80">Challan No</th>
                <th width="100">MRR No</th>
                <th width="100">MRR Basis</th>
                <th width="60">MRR Date</th>
                <th width="50">Currency</th>
                <th width="50">Ex Rate</th>
                <th width="100">MRR Amount($)</th>
                <th width="100">MRR Amount (BDT)</th>
                <th width="180">RQSN/WO Number</th>
                <th width="100">Supplier</th>
                <th width="80">PI Number</th>
                <th width="80">LC/TT Number</th>
                <th width="80">Audit User</th>
                <th width="80">Auditor Name</th>
                <th width="60">Audit Date</th>
               
                <th width="">Remarks</th>
                </thead>
            </table>
            <div style="width:<? echo $tableWidth ; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body" align="left">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tableWidth-20 ; ?>" class="rpt_table" id="table_body" align="left">
                    <tbody>
                    <?
                    $i=1; $j=0;
                    $tot_mrr_amount_doller_accessories=$tot_mrr_amount_doller=0;
                    $tot_mrr_amount_accessories_tk=$tot_mrr_amount_tk=0;

                    foreach ($sql_mrr_res as $row)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF";
                        else $bgcolor="#FFFFFF";

                        if ($row['SUPPLIER_ID'] != 0) {
                            $supplier=$supplier_arr[$row['SUPPLIER_ID']];
                        } else if ($row['KNITTING_SOURCE']==1) {
                            $supplier=$company_fullname_arr[$row['KNITTING_COMPANY']];
                        } else {
                            $supplier=$supplier_arr[$row['KNITTING_COMPANY']];
                        }
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
                            <td width="30" align="center"><? echo $i; ?></td>
                            <td width="80" align="center" style="word-break:break-all;"><p><? echo $company_arr[$row['COMPANY_ID']]; ?></p></td>
                            <td width="100" align="center" style="word-break:break-all;"><p>
                                    <?
                                    $item_category_names = ''; $item_id_arr = array();
                                    $item_id_arr= array_unique(explode(',', $row['ITEM_CATEGORY']));
                                    foreach($item_id_arr as $item_id) {
                                        $item_category_names .= $item_category[$item_id].',';
                                    }
                                    echo chop($item_category_names, ',');
                                    ?>
                                </p></td>
                            <td width="80" align="center" style="word-break:break-all;"><p><? echo $store_arr[$row['STORE_ID']]; ?></p></td>
                            <td width="80" align="center" style="word-break:break-all;"><p><? echo $row['CHALLAN_NO']; ?></p></td>
                            <?
                            if($row['ENTRY_FORM'] == 17){
                                if($row['RECEIVE_BASIS'] == 1 && $format_id == 66){
                                    ?>
                                    <td width="100" align="center" style="word-break:break-all;" title="<? echo $row['RECV_NUMBER']; ?>"><a href="#" onclick="print_report( '<?=$row['COMPANY_ID']?>*<?=$row['ID']?>*<?=$row['BOOKING_ID']?>*Woven Finish Fabric Receive', '<?=$report_action1?>', '../finish_fabric/requires/woven_finish_fabric_receive_controller');"><? echo $row['RECV_NUMBER']; ?></a></td>
                                    <?
                                }elseif($row['RECEIVE_BASIS'] != 1 && $format_id == 66){
                                    ?>
                                    <td width="100" align="center" style="word-break:break-all;" title="<? echo $row['RECV_NUMBER']; ?>"><a href="#" onclick="print_report( '<?=$row['COMPANY_ID']?>*<?=$row['ID']?>*<?=$row['BOOKING_ID']?>*Woven Finish Fabric Receive', '<?=$report_action?>', '../finish_fabric/requires/woven_finish_fabric_receive_controller');"><? echo $row['RECV_NUMBER']; ?></a></td>
                                    <?
                                }else{
                                    ?>
                                    <td width="100" align="center" style="word-break:break-all;" title="<? echo $row['RECV_NUMBER']; ?>"><a href="#" onclick="print_report( '<?=$row['COMPANY_ID']?>*<?=$row['ID']?>*Woven Finish Fabric Receive', '<?=$report_action?>', '../finish_fabric/requires/woven_finish_fabric_receive_controller');"><?  echo $row['RECV_NUMBER']; ?></a></td>
                                    <?
                                }
                            }elseif($row['ENTRY_FORM'] == 58 && $row['RECEIVE_BASIS']==10){
                                ?>                         
                                <td width="100" align="center" style="word-break:break-all;"><a href="#" onClick="generate_report_grey_fabric_mrr('<?=$print_btn?>','<? echo $row["COMPANY_ID"];?>','<? echo $row["ID"];?>','<? echo $row["RECV_NUMBER"];?>','<? echo $row["LOCATION_ID"];?>','<? echo $row["STORE_ID"];?>');"><? echo $row['RECV_NUMBER']; ?></a></td>
                                <?
                            }
                            else{
                                ?>
                                <td width="100" align="center" style="word-break:break-all;" title="<? echo $row['RECV_NUMBER']; ?>"><a href="#" onclick="show_mrr_dtls('<? echo $row['COMPANY_ID']."__".$row['ID']."__".$row['RECV_NUMBER']."__".$row['ITEM_CATEGORY']."__".$row['VARIABLE_SETTING']."__".$row['RECEIVE_BASIS']."__".$row['LOCATION_ID']."__".$row['ENTRY_FORM']."__".$trim_report_action; ?>');"><? echo $row['RECV_NUMBER']; ?></a></td>
                                <?
                            }
                            ?>
                            <td width="100" style="word-break:break-all;"><p><? echo $receive_basis_arr[$row['RECEIVE_BASIS']]; ?></p></td>
                            <td width="60" align="center" style="word-break:break-all;"><? echo change_date_format($row['RECEIVE_DATE']); ?>&nbsp;</td>
                            <td width="50" align="center"><p><? echo $currency[$row['CURRENCY_ID']]; ?></p></td>
                            <td width="50" align="center"><p><? echo $row['EXCHANGE_RATE']; ?></p></td>


                            <td width="100" align="right"><p>
                                    <?	//echo $row['ORDER_AMOUNT'];
                                    if ($row['CURRENCY_ID'] == 1)
                                    {
                                        $mrr_amount_doller=$row['ORDER_AMOUNT']/$conversion_rate;
                                        $mrr_amount_doller_accessories=$row['ORDER_AMOUNT_ACCESSORIES']/$conversion_rate;
                                    }
                                    else
                                    {
                                        $mrr_amount_doller=$row['ORDER_AMOUNT'];
                                        $mrr_amount_doller_accessories=$row['ORDER_AMOUNT_ACCESSORIES'];
                                    }
                                    if ($row['ENTRY_FORM'] == 24)
                                    {
                                        $tot_mrr_amount_doller_accessories+=$mrr_amount_doller_accessories;
                                        echo number_format($mrr_amount_doller_accessories,2);
                                    }
                                    else
                                    {
                                        $tot_mrr_amount_doller+=$mrr_amount_doller;
                                        echo number_format($mrr_amount_doller,2);
                                    }


                                    ?></p></td>


                            <td width="100" align="right"><p>
                                    <?
                                    if ($row['CURRENCY_ID'] == 1)
                                    {
                                        $mrr_amount_tk=$mrr_amount_doller*$conversion_rate;
                                        $mrr_amount_accessories_tk=$mrr_amount_doller_accessories*$conversion_rate;
                                    }
                                    else
                                    {
                                        $mrr_amount_tk=$row['CONS_AMOUNT'];
                                        $mrr_amount_accessories_tk=$row['CONS_AMOUNT_ACCESSORIES'];
                                    }
                                    if ($row['ENTRY_FORM'] == 24)
                                    {
                                        $tot_mrr_amount_accessories_tk+=$mrr_amount_accessories_tk;
                                        echo number_format($mrr_amount_accessories_tk,2);
                                    }
                                    else
                                    {
                                        $tot_mrr_amount_tk+=$mrr_amount_tk;
                                        echo number_format($mrr_amount_tk,2);
                                    }

                                    ?>
                                </p></td>
                            <td width="180" align="center"><p>
                                    <?
                                    if ($row['RECEIVE_BASIS'] == 1) {
                                        echo rtrim($work_order_no_arr[$row['BOOKING_ID']]['WORK_ORDER_NO'],',');
                                    } else if ($row['RECEIVE_BASIS'] == 5) {
                                        $batch_booking_names = ''; $batch_id_arr = array();
                                        $batch_id_arr= array_unique(explode(',', $row['PI_WO_BATCH_ID']));
                                        foreach($batch_id_arr as $batch_id){
                                            $batch_booking_names .= $batch_booking_arr[$batch_id].',';
                                        }
                                        echo rtrim($batch_booking_names, ',');
                                    }
                                    else{
                                        echo $row['BOOKING_NO'] === '0' ? '' : $row['BOOKING_NO'];
                                    }
                                    ?>
                                </p></td>
                            <td width="100" style="word-break:break-all;"><p><? echo $supplier; ?></p></td>
                            <td width="80" style="word-break:break-all;"><p>
                                    <?
                                    if($row['RECEIVE_BASIS'] == 1){
                                        echo $row['BOOKING_NO'];
                                    }elseif($row['RECEIVE_BASIS']==2){
                                        echo $fterGoodRec_PiNo_arr[$row['BOOKING_ID']]['PI_NUMBER'];
                                    }
                                    ?>
                                </p></td>

                            <td width="80" style="word-break:break-all;"><p>
                                    <?
                                    if ($row['RECEIVE_BASIS'] == 1) {
                                        echo $lc_number_arr[$row['BOOKING_ID']]['LC_NUMBER'];
                                    } else  {
                                        echo $fterGoodRec_lc_number_arr[$row['BOOKING_ID']]['LC_NUMBER'];
                                    }
                                    ?>
                                </p></td>
                            <td width="80" align="center" style="word-break:break-all;"><p><? echo $user_id[$row['AUDIT_BY']]; ?></p></td>
                            <td width="80" style="word-break:break-all;"><p><? echo $user_name[$row['AUDIT_BY']]; ?></p></td>
                            <td width="60" align="center" style="word-break:break-all;"><? echo change_date_format($row['AUDIT_DATE']); ?>&nbsp;</td>
                       
                            <td width="" valign="middle"?><p><? echo $row['AUDIT_REMARK']; ?></p></td>
                        </tr>
                        <?
                        $i++;
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <table cellpadding="0" cellspacing="0" rules="all" border="1" width="<? echo $tableWidth-20 ; ?>" class="rpt_table" id="report_table_footer" align="left">
                <tfoot>
                <tr>
                    <th width="30"><p>&nbsp;</p></th>
                    <th width="80"><p>&nbsp;</p></th>
                    <th width="100"><p>&nbsp;</p></th>
                    <th width="80"><p>&nbsp;</p></th>
                    <th width="80"><p>&nbsp;</p></th>
                    <th width="100"><p>&nbsp;</p></th>
                    <th width="100"><p>&nbsp;</p></th>
                    <th width="60"><p>&nbsp;</p></th>
                    <th width="50"><p>&nbsp;</p></th>
                    <th width="50"><p>Total:</p></th>
                    <th width="100"><p><? echo number_format($tot_mrr_amount_doller_accessories+$tot_mrr_amount_doller,2); ?></p></th>
                    <th width="100"><p><? echo number_format($tot_mrr_amount_accessories_tk+$tot_mrr_amount_tk,2); ?></p></th>
                    <th width="180"><p>&nbsp;</p></th>
                    <th width="100"><p>&nbsp;</p></th>
                    <th width="80"><p>&nbsp;</p></th>
                    <th width="80"><p>&nbsp;</p></th>
                    <th width="80"><p>&nbsp;</p></th>
                    <th width="80"><p>&nbsp;</p></th>
                    <th width="60"><p>&nbsp;</p></th>
                    
                    <th ><p>&nbsp;</p></th>
                </tr>
                </tfoot>
            </table>

        </fieldset>
    </form>
    <?
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
        @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$sessionUserID."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename";
    exit();
}

?>
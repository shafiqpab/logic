<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.yarns.php');
require_once('../../../includes/class4/class.trims.php');
require_once('../../../includes/class4/class.emblishments.php');
require_once('../../../includes/class4/class.washes.php');
require_once('../../../includes/class4/class.fabrics.php');
require_once('../../../includes/class4/class.conversions.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
$permission=$_SESSION['page_permission'];


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 200, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "load_style(this.value);",0 );
	exit();
}


if($action == 'file_search_popup') 
{
	extract($_REQUEST);
	echo load_html_head_contents('Popup Info', '../../../', 1, 1, $unicode);
    //echo $company."**".$buyer."**".$file_year; die();
	//echo $style_id;die;
	?>
    <script>
		function js_set_value( str ) {
			$('#txt_selected_no').val(str);
			parent.emailwindow.hide();
		}
    </script>
	<?php
		$buyer=str_replace("'","",$buyer);
		$company=str_replace("'","",$company);
		// $job_year=str_replace("'","",$job_year);
        if($file_year!=0) $file_year_cond=" and to_char(a.insert_date,'YYYY')=$file_year"; else $file_year_cond="";
        $select_date=" to_char(a.insert_date,'YYYY')";
        if($buyer!=0) $buyer_cond=" and a.buyer_name in($buyer)"; else $buyer_cond="";
		if($company!=0) $company_cond=" and a.beneficiary_name in($company)"; else $company_cond="";

        // $sql="SELECT b.id, b.file_no ,b.po_number,$select_date as year from wo_po_details_master a , wo_po_break_down b 
		// where a.status_active=1 and a.is_deleted=0 and a.job_no = b.JOB_NO_MST and  b.status_active=1 and b.is_deleted=0 and b.file_no is not null $buyer_cond $company_cond $file_year_cond group by b.id, b.file_no ,b.po_number ,b.insert_date";
		
        $sql="SELECT * from ( SELECT  DISTINCT (a.internal_file_no)  ,$select_date as year
        from  com_export_lc a
        where a.status_active=1 and a.is_deleted=0  $company_cond $buyer_cond $file_year_cond
        group by a.id, a.internal_file_no,a.insert_date
        order by cast(a.internal_file_no as int))
        union all
        SELECT * from ( SELECT  DISTINCT (a.internal_file_no) , $select_date as year
        from  com_sales_contract a
        where a.convertible_to_lc <> 1 and a.status_active=1 and a.is_deleted=0 $company_cond $buyer_cond $file_year_cond
        group by a.id, a.internal_file_no,a.insert_date
        order by cast(a.internal_file_no as int))";
        //echo $sql;
   
		echo create_list_view("list_view", "Year,File No,","100,150","410","300",0, $sql , "js_set_value", "id,internal_file_no", "", 1, "0", $arr, "year,internal_file_no", "","setFilterGrid('list_view',-1)","0","","") ;	
		echo "<input type='hidden' id='txt_selected_no' />";
	    exit();
}

if($action=="report_generate"){
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_name=str_replace("'","",$cbo_company_name);
	$buyer_name=str_replace("'","",$cbo_buyer_name);
	$file_no=str_replace("'","",$txt_file_no);
	$file_year=str_replace("'","",$cbo_year);

	//echo $company_name."***".$buyer_name."***".$file_no."***".$file_year;

    $lib_company_arr=return_library_array( "select id, company_name from lib_company is_deleted=0",'id','company_name');
    $buyer_arr = return_library_array("select id,buyer_name from lib_buyer where is_deleted=0","id","buyer_name");
    $buyer_address_arr = return_library_array("select id,address_1 from lib_buyer where is_deleted=0","id","address_1");
    $bank_arr = return_library_array("select id,bank_name from lib_bank where is_deleted=0","id","bank_name");
    $bank_address_arr = return_library_array("select id,address from lib_bank where is_deleted=0","id","address");
    $lib_country_arr = return_library_array("select id,country_name from lib_country where is_deleted=0","id","country_name");

    if ($company_name!='') {$company_id=" and d.beneficiary_name=$cbo_company_name";} else { echo "Please Select Company First."; die;}
    if($buyer_name>0) {$buyer_cond=" and d.buyer_name in ($cbo_buyer_name)"; }else {$buyer_cond="";}
    if ($file_no!='') { $file_no_cond= " and d.internal_file_no in( $txt_file_no)";} else { $file_no_cond=""; }

   // if($txt_file_no!="") {echo  $file_no_cond=" and d.internal_file_no in ($txt_file_no)";} else {$file_no_cond="";}
   
    if ($cbo_year!='') 
    {
        $lc_file_year=" and d.lc_year=$cbo_year";
        $sc_file_year=" and d.sc_year=$cbo_year";
    }
    else
    { $lc_file_year="";$sc_file_year="";}

    $sc_sql="SELECT d.ID,c.contract_no,c.CONTRACT_DATE,c.CONTRACT_VALUE,d.REPLACEMENT_LC  
    FROM COM_EXPORT_LC D , COM_EXPORT_LC_ATCH_SC_INFO B ,com_sales_contract c
    WHERE d.ID = B.COM_EXPORT_LC_ID AND b.COM_SALES_CONTRACT_ID = c.id   $company_id $buyer_cond $lc_file_year $file_no_cond
    group by d.ID,c.contract_no,c.CONTRACT_DATE,c.CONTRACT_VALUE,d.REPLACEMENT_LC
    union all
    SELECT d.ID,d.CONTRACT_NO,d.CONTRACT_DATE,d.CONTRACT_VALUE ,0
    from  com_sales_contract d
    where d.status_active=1 and d.is_deleted=0  $company_id $buyer_cond $sc_file_year $file_no_cond  and d.CONVERTIBLE_TO_LC<>1
    group by d.id,d.contract_no,d.CONTRACT_DATE,d.CONTRACT_VALUE 
    ";
    //echo $sc_sql;  //die;
    $sc_sql_result=sql_select($sc_sql);
    $sc_info=array();
    foreach($sc_sql_result as $row)
    {
        $sc_info[$row['ID']]['CONTRACT_NO']=$row['CONTRACT_NO'];       
        $sc_info[$row['ID']]['CONTRACT_DATE']=$row['CONTRACT_DATE'];       
        $sc_info[$row['ID']]['CONTRACT_VALUE']=$row['CONTRACT_VALUE'];                           
        $sc_info[$row['ID']]['REPLACEMENT_LC']=$row['REPLACEMENT_LC'];                           
        $sc_info_total+=$row['CONTRACT_VALUE'];                           
        $sc_id.=$row['ID'].',';
    }
    $com_sc_id=implode(",",array_unique(explode(",",chop($sc_id,','))));                    
    
    unset($sc_sql);
    unset($sc_sql_result);

    $lc_sql="SELECT d.id as ID, d.export_lc_no as EXPORT_LC_NO, d.lc_date as LC_DATE, d.lc_value as LC_VALUE, d.estimated_qnty as ESTIMATED_QNTY
    from  com_export_lc d
    where d.status_active=1 and d.is_deleted=0 $company_id $buyer_cond $lc_file_year $file_no_cond
    group by d.id,d.export_lc_no,d.lc_date,d.lc_value,d.estimated_qnty
    union all 
    SELECT d.id as ID, d.export_lc_no as EXPORT_LC_NO, d.lc_date as LC_DATE, d.lc_value as LC_VALUE, d.estimated_qnty as ESTIMATED_QNTY
    from  com_export_lc d
    where d.status_active=1 and d.is_deleted=0 $company_id $buyer_cond $lc_file_year $file_no_cond and d.REPLACEMENT_LC<>1
    group by d.id,d.export_lc_no,d.lc_date,d.lc_value,d.estimated_qnty ";
    // echo $lc_sql;  //die;
    $lc_sql_result=sql_select($lc_sql);
    $lc_info=array();
    foreach($lc_sql_result as $row)
    {
        $lc_info[$row['ID']]['EXPORT_LC_NO']=$row['EXPORT_LC_NO'];       
        $lc_info[$row['ID']]['LC_DATE']=$row['LC_DATE'];       
        $lc_info[$row['ID']]['LC_VALUE']=$row['LC_VALUE'];                        
        $lc_info_total+=$row['LC_VALUE'];                        
        $lc_id.=$row['ID'].',';
    }
    $com_sc_lc_id=implode(",",array_unique(explode(",",chop($lc_id,','))));                    
    unset($lc_sql);
    unset($lc_sql_result);
    
    $mst_sql="SELECT * from ( SELECT d.id,d.internal_file_no,d.sc_year as LC_SC_YEAR ,d.buyer_name,d.LIEN_BANK, e.ID as INV_ID , e.INVOICE_NO,e.INVOICE_DATE,e.INVOICE_VALUE,e.INVOICE_QUANTITY,e.BL_NO,e.SHIP_BL_DATE,e.SHIPPING_BILL_N,e.CARTON_NET_WEIGHT,e.EXP_FORM_NO,e.EXP_FORM_DATE,e.PORT_OF_DISCHARGE,e.COUNTRY_ID,e.FREIGHT_AMNT_BY_SUPLLIER
    from com_sales_contract d left join COM_EXPORT_INVOICE_SHIP_MST e on d.id=e.LC_SC_ID and e.IS_LC=2
    where d.status_active=1 and d.is_deleted=0 $company_id $buyer_cond $sc_file_year $file_no_cond and  d.CONVERTIBLE_TO_LC<>1
    group by d.id,d.internal_file_no, d.sc_year,d.buyer_name,d.LIEN_BANK,e.ID,e.INVOICE_NO,e.INVOICE_DATE,e.INVOICE_VALUE,e.INVOICE_QUANTITY,e.BL_NO,e.SHIP_BL_DATE,e.SHIPPING_BILL_N,e.CARTON_NET_WEIGHT,e.EXP_FORM_NO,e.EXP_FORM_DATE,e.PORT_OF_DISCHARGE,e.COUNTRY_ID,e.FREIGHT_AMNT_BY_SUPLLIER order by cast(d.internal_file_no as int))
    union all 
    SELECT * from (SELECT d.id,d.internal_file_no,d.lc_year as LC_SC_YEAR ,d.buyer_name,d.LIEN_BANK ,e.ID as INV_ID ,e.INVOICE_NO,e.INVOICE_DATE,e.INVOICE_VALUE,e.INVOICE_QUANTITY,e.BL_NO,e.SHIP_BL_DATE,e.SHIPPING_BILL_N,e.CARTON_NET_WEIGHT,e.EXP_FORM_NO,e.EXP_FORM_DATE,e.PORT_OF_DISCHARGE,e.COUNTRY_ID,e.FREIGHT_AMNT_BY_SUPLLIER
    from COM_EXPORT_LC_ATCH_SC_INFO b, com_export_lc d left join COM_EXPORT_INVOICE_SHIP_MST e on d.id=e.LC_SC_ID and e.IS_LC=1
    where d.id=b.COM_EXPORT_LC_ID and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_id $buyer_cond $lc_file_year $file_no_cond and d.REPLACEMENT_LC=1
    group by d.id,d.internal_file_no,d.lc_year,d.buyer_name,d.LIEN_BANK ,e.ID,e.INVOICE_NO,e.INVOICE_DATE,e.INVOICE_VALUE,e.INVOICE_QUANTITY,e.BL_NO,e.SHIP_BL_DATE,e.SHIPPING_BILL_N,e.CARTON_NET_WEIGHT,e.EXP_FORM_NO,e.EXP_FORM_DATE,e.PORT_OF_DISCHARGE,e.COUNTRY_ID,e.FREIGHT_AMNT_BY_SUPLLIER
    order by cast(d.internal_file_no as int))
    union all 
    SELECT * from (SELECT d.id,d.internal_file_no,d.lc_year as LC_SC_YEAR,d.buyer_name,d.LIEN_BANK ,e.ID as INV_ID, e.INVOICE_NO,e.INVOICE_DATE,e.INVOICE_VALUE,e.INVOICE_QUANTITY,e.BL_NO,e.SHIP_BL_DATE,e.SHIPPING_BILL_N,e.CARTON_NET_WEIGHT,e.EXP_FORM_NO,e.EXP_FORM_DATE,e.PORT_OF_DISCHARGE,e.COUNTRY_ID,e.FREIGHT_AMNT_BY_SUPLLIER
    from com_export_lc d left join COM_EXPORT_INVOICE_SHIP_MST e on d.id=e.LC_SC_ID and e.IS_LC=1
    where d.status_active=1 and d.is_deleted=0 $company_id $buyer_cond $lc_file_year $file_no_cond and d.REPLACEMENT_LC<>1
    group by d.id,d.internal_file_no,d.lc_year,d.buyer_name,d.LIEN_BANK,e.ID,e.INVOICE_NO,e.INVOICE_DATE,e.INVOICE_VALUE,e.INVOICE_QUANTITY,e.BL_NO,e.SHIP_BL_DATE,e.SHIPPING_BILL_N,e.CARTON_NET_WEIGHT,e.EXP_FORM_NO,e.EXP_FORM_DATE,e.PORT_OF_DISCHARGE,e.COUNTRY_ID,e.FREIGHT_AMNT_BY_SUPLLIER 
    order by cast(d.internal_file_no as int))";
   // echo $mst_sql; //die;
    $mst_sql_result=sql_select($mst_sql);
    foreach($mst_sql_result as $row)
    {
        $sc_id.=$row['ID'].',';
        if($row['INV_ID']!=''){$inv_id.=$row['INV_ID'].',';}
    }
    $inv_id=implode(",",array_unique(explode(",",chop($inv_id,','))));
    
    unset($mst_sql);
    
    // sc attch qty start
    $sc_atch_qty_sql="SELECT COM_SALES_CONTRACT_ID, sum(ATTACHED_QNTY) as SC_ATCH_QTY from COM_SALES_CONTRACT_ORDER_INFO where COM_SALES_CONTRACT_ID in ($com_sc_id) group by COM_SALES_CONTRACT_ID";
    // echo $sc_atch_qty_sql;//die;
    $sc_atch_qty_sql_result=sql_select($sc_atch_qty_sql);
    $sc_atch_qty_result_info=array();
    foreach($sc_atch_qty_sql_result as $row)
    {
        $sc_atch_qty_result_info[$row["COM_SALES_CONTRACT_ID"]]['SC_ATCH_QTY']=$row["SC_ATCH_QTY"];
    }
    unset($sc_atch_qty_sql);
    unset($sc_atch_qty_sql_result);

    //LC atch qty start
    $lc_atch_qty_sql="SELECT COM_EXPORT_LC_ID, sum(ATTACHED_QNTY) as LC_ATCH_QTY from COM_EXPORT_LC_ORDER_INFO where COM_EXPORT_LC_ID in ($com_sc_lc_id) group by COM_EXPORT_LC_ID";
    // echo $invoice_sql;die;
    $lc_atch_qty_sql_result=sql_select($lc_atch_qty_sql);
    $lc_atch_qty_result_info=array();
    foreach($lc_atch_qty_sql_result as $row)
    {
        $lc_atch_qty_result_info[$row["COM_EXPORT_LC_ID"]]['LC_ATCH_QTY']=$row["LC_ATCH_QTY"];
    }
    unset($lc_atch_qty_sql);
    unset($lc_atch_qty_sql_result);

    $sub_bill_sql = "SELECT d.ID as INV_ID, b.BANK_REF_NO,b.BANK_REF_DATE,m.SYS_NUMBER,m.SUBMISSION_DATE,p.RLZ_VALUE,a.RECEIVED_DATE,d.INVOICE_NO,d.INVOICE_DATE,d.INVOICE_VALUE,d.INVOICE_QUANTITY,d.BL_NO,d.SHIP_BL_DATE,d.SHIPPING_BILL_N,d.CARTON_NET_WEIGHT,d.EXP_FORM_NO,d.EXP_FORM_DATE,d.PORT_OF_DISCHARGE,d.COUNTRY_ID,d.FREIGHT_AMNT_BY_SUPLLIER
	from cash_incentive_submission m,cash_incentive_submission_dtls p, com_export_proceed_realization a, com_export_doc_submission_mst b, com_export_doc_submission_invo c, com_export_invoice_ship_mst d, com_export_lc e
	where m.ID = p.MST_ID and p.REALIZATION_ID=a.id and p.SUBMISSION_BILL_ID=d.id and a.invoice_bill_id=b.id and b.id=c.doc_submission_mst_id and c.INVOICE_ID=d.id and d.LC_SC_ID=e.id and d.is_lc=1 and a.is_invoice_bill=1   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and p.status_active=1 and p.is_deleted=0 
    group by d.ID , b.BANK_REF_NO,b.BANK_REF_DATE,m.SYS_NUMBER,m.SUBMISSION_DATE,p.RLZ_VALUE,a.RECEIVED_DATE,d.INVOICE_NO,d.INVOICE_DATE,d.INVOICE_VALUE,d.INVOICE_QUANTITY,d.BL_NO,d.SHIP_BL_DATE,d.SHIPPING_BILL_N,d.CARTON_NET_WEIGHT,d.EXP_FORM_NO,d.EXP_FORM_DATE,d.PORT_OF_DISCHARGE,d.COUNTRY_ID,d.FREIGHT_AMNT_BY_SUPLLIER
	union all
	SELECT d.ID as INV_ID,b.BANK_REF_NO,b.BANK_REF_DATE,m.SYS_NUMBER,m.SUBMISSION_DATE,p.RLZ_VALUE,a.RECEIVED_DATE,d.INVOICE_NO,d.INVOICE_DATE,d.INVOICE_VALUE,d.INVOICE_QUANTITY,d.BL_NO,d.SHIP_BL_DATE,d.SHIPPING_BILL_N,d.CARTON_NET_WEIGHT,d.EXP_FORM_NO,d.EXP_FORM_DATE,d.PORT_OF_DISCHARGE,d.COUNTRY_ID,d.FREIGHT_AMNT_BY_SUPLLIER
	from cash_incentive_submission m, cash_incentive_submission_dtls p, com_export_proceed_realization a, com_export_doc_submission_mst b, com_export_doc_submission_invo c, com_export_invoice_ship_mst d, com_sales_contract e
	where m.ID = p.MST_ID and p.REALIZATION_ID=a.id and p.SUBMISSION_BILL_ID=d.id and a.invoice_bill_id=b.id and b.id=c.doc_submission_mst_id and c.INVOICE_ID=d.id and d.LC_SC_ID=e.id and d.is_lc=2 and a.is_invoice_bill=1  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and p.status_active=1 and p.is_deleted=0
    group by d.ID, b.BANK_REF_NO,b.BANK_REF_DATE,m.SYS_NUMBER,m.SUBMISSION_DATE,p.RLZ_VALUE,a.RECEIVED_DATE,d.INVOICE_NO,d.INVOICE_DATE,d.INVOICE_VALUE,d.INVOICE_QUANTITY,d.BL_NO,d.SHIP_BL_DATE,d.SHIPPING_BILL_N,d.CARTON_NET_WEIGHT,d.EXP_FORM_NO,d.EXP_FORM_DATE,d.PORT_OF_DISCHARGE,d.COUNTRY_ID,d.FREIGHT_AMNT_BY_SUPLLIER";
    //echo $sub_bill_sql;
    $sub_bill_sql_result=sql_select($sub_bill_sql);
    $lc_atch_qty_result_info=array();
    foreach($sub_bill_sql_result as $row)
    {
        $bill_sub_result_info[$row["INV_ID"]]['BANK_REF_NO']=$row["BANK_REF_NO"];
        $bill_sub_result_info[$row["INV_ID"]]['BANK_REF_DATE']=$row["BANK_REF_DATE"];
        $bill_sub_result_info[$row["INV_ID"]]['SYS_NUMBER']=$row["SYS_NUMBER"];
        $bill_sub_result_info[$row["INV_ID"]]['SUBMISSION_DATE']=$row["SUBMISSION_DATE"];
        $bill_sub_result_info[$row["INV_ID"]]['RLZ_VALUE']=$row["RLZ_VALUE"];
        $bill_sub_result_info[$row["INV_ID"]]['RECEIVED_DATE']=$row["RECEIVED_DATE"];
        $bill_sub_result_info[$row["INV_ID"]]['INVOICE_NO']=$row["INVOICE_NO"];
        $bill_sub_result_info[$row["INV_ID"]]['INVOICE_DATE']=$row["INVOICE_DATE"];
        $bill_sub_result_info[$row["INV_ID"]]['INVOICE_VALUE']=$row["INVOICE_VALUE"];
        $bill_sub_result_info[$row["INV_ID"]]['INVOICE_QUANTITY']=$row["INVOICE_QUANTITY"];

        $bill_sub_result_info[$row["INV_ID"]]['BL_NO']=$row["BL_NO"];
        $bill_sub_result_info[$row["INV_ID"]]['SHIP_BL_DATE']=$row["SHIP_BL_DATE"];
        $bill_sub_result_info[$row["INV_ID"]]['SHIPPING_BILL_N']=$row["SHIPPING_BILL_N"];
        $bill_sub_result_info[$row["INV_ID"]]['CARTON_NET_WEIGHT']=$row["CARTON_NET_WEIGHT"];
        $bill_sub_result_info[$row["INV_ID"]]['EXP_FORM_NO']=$row["EXP_FORM_NO"];
        $bill_sub_result_info[$row["INV_ID"]]['EXP_FORM_DATE']=$row["EXP_FORM_DATE"];
        $bill_sub_result_info[$row["INV_ID"]]['PORT_OF_DISCHARGE']=$row["PORT_OF_DISCHARGE"];
        $bill_sub_result_info[$row["INV_ID"]]['COUNTRY_ID']=$row["COUNTRY_ID"];
        $bill_sub_result_info[$row["INV_ID"]]['FREIGHT_AMNT_BY_SUPLLIER']=$row["FREIGHT_AMNT_BY_SUPLLIER"];
        
    }

    $gmnt_item_sql ="SELECT COM_SALES_CONTRACT_ID as SC_LC_ID ,c.GMTS_ITEM_ID from COM_SALES_CONTRACT_ORDER_INFO a,WO_PO_BREAK_DOWN b ,WO_PO_DETAILS_MASTER c where a.COM_SALES_CONTRACT_ID in ($com_sc_id) and a.WO_PO_BREAK_DOWN_ID = b.id and b.JOB_NO_MST = c.JOB_NO group by a.COM_SALES_CONTRACT_ID,c.GMTS_ITEM_ID 
    union all 
    SELECT a.COM_EXPORT_LC_ID as SC_LC_ID ,c.GMTS_ITEM_ID from COM_EXPORT_LC_ORDER_INFO a,WO_PO_BREAK_DOWN b,WO_PO_DETAILS_MASTER c  where a.COM_EXPORT_LC_ID in ($com_sc_lc_id) 
    and a.WO_PO_BREAK_DOWN_ID = b.id and b.JOB_NO_MST = c.JOB_NO group by a.COM_EXPORT_LC_ID,c.GMTS_ITEM_ID ";
    $gmnt_item_sql_result=sql_select($gmnt_item_sql);
    $gmnt_itm_info=array();
    foreach($gmnt_item_sql_result as $row)
    {                     
        $gmnt_itm_info[$row["SC_LC_ID"]]['GMTS_ITEM_ID']=$row["GMTS_ITEM_ID"];
    }
   // echo $gmnt_item_sql;

    ob_start();
    ?>
    <div style="width:3970px;">
        <table width="3940" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr>
                    <th width="30"><p> Sl. No.</p></th>
                    <th width="100"><p>File No</p></th>
                    <th width="60"><p>Year</p></th>
                    <th width="100"><p>Bank Name</p></th>
                    <th width="200"><p>Address</p></th>
                    <th width="120"><p>Buyer Name</p></th>
                    <th width="200"><p>Buyer Address</p></th>
                    <th width="160"><p>Sales Contract Number</p></th>
                    <th width="100"><p>SC Date</p></th>
                    <th width="100"><p>SC Value</p></th>
                    <th width="80"><p>SC Pcs Qty</p></th>
                    <th width="100"><p>Adjust and <br> Replace</p></th>
                    <th width="100"><p>MASTER L/C <br> NUMBER</p></th>
                    <th width="80"><p>Date</p></th>
                    <th width="100"><p>Value</p></th>
                    <th width="100"><p>MASTER L/C Qty (Pcs)</p></th>

                    <th width="100"><p>DESCRIPTION OF GOODS</p></th>
                    <th width="100"><p>PRC ID</p></th>
                    <th width="80"><p>PRC ID DATE</p></th>
                    <th width="120"><p>INVOICE NO</p></th>
                    <th width="80"><p>INV. DATE</p></th>
                    <th width="100"><p>INV. VALUE</p></th>
                    <th width="80"><p>Qty (Pcs)</p></th>
                    <th width="150"><p>Ori. B/L NO</p></th>
                    <th width="80"><p>BL Date</p></th>
                    <th width="100"><p>B/L CONTAINER NO</p></th>
                    <th width="80"><p>S/B NO</p></th>
                    <th width="80"><p>S/B DATE</p></th>

                    <th width="80"><p>NET WEIGHT</p></th>
                    <th width="100"><p>EXP NO</p></th>
                    <th width="80"><p>EXP Date</p></th>
                    <th width="120"><p>REALIZE AMOUNT</p></th>
                    <th width="80"><p>REALIZE <br> DATE</p></th>
                    <th width="100"><p>FREIGHT & <br> INSURANCE</p></th>
                    <th width="80"><p>FREIGHT <br> CHARGE</p></th>
                    <th width="100"><p>PORT OF <br> DISCHARGE</p></th>
                    <th width="100"><p>COUNTRY NAME</p></th>

                    <th width="120"><p>UD NUMBER </p></th>
                    <th width="80"><p>UD DATE</p></th>
                    <th width="100"><p>UD Amendment <br> Number</p></th>

                </tr>
            </thead>
            </table>
            <div style="width:3940px; overflow-y: scroll; max-height:400px; overflow-x:hidden;" id="scroll_body">
            <table cellspacing="0" width="3940"  border="1" rules="all" class="rpt_table" id="tbl_body">
            <tbody id="table_body" >
                <?
                    $i=1;
                    foreach($mst_sql_result as $row)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF";
                        else $bgcolor="#FFFFFF";
                        ?>
                            <tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                                <td width="30" align="center"><?=$i;?></td>
                                <td width="100"><p><? echo $row['INTERNAL_FILE_NO']; ?></p></td>
                                <td width="60" align="center"><p><? echo $row['LC_SC_YEAR']; ?></p></td>
                                <td width="100"><p> <? echo $bank_arr[$row['LIEN_BANK']]; ?></p></td>
                                <td width="200"> <p><? echo $bank_address_arr[$row['LIEN_BANK']]; ?> </p></td>  
                                <td width="120"><p><? echo $buyer_arr[$row['BUYER_NAME']]; ?></p></td>
                                <td width="200"><p><? echo $buyer_address_arr[$row['BUYER_NAME']]; ?></p></td>
                                <td width="160"><p><? echo $sc_info[$row['ID']]['CONTRACT_NO']; ?></p></td>
                                <td width="100" align="center"><p><? echo change_date_format($sc_info[$row['ID']]['CONTRACT_DATE']); ?> </p></td>
                                <td width="100" align="right"><p><? if($sc_info[$row['ID']]['CONTRACT_VALUE']>0) echo number_format($sc_info[$row['ID']]['CONTRACT_VALUE'],2);
                                $total_sc_value +=$sc_info[$row['ID']]['CONTRACT_VALUE'];
                                ?> </p></td>
                                <td width="80" align="center"><p><? echo $sc_atch_qty_result_info[$row['ID']]['SC_ATCH_QTY'];?> </p></td>
                                <td width="100"><p><? if($sc_info[$row['ID']]['REPLACEMENT_LC']==1){echo "Replacement LC";}elseif($sc_info[$row['ID']]['REPLACEMENT_LC']==2){echo "Direct LC";}?></p></td>
                                <td width="100"><p><?echo $lc_info[$row['ID']]['EXPORT_LC_NO']; ?></p></td>
                                <td width="80" align="center"><p><? echo change_date_format($lc_info[$row['ID']]['LC_DATE']); ?> </p></td>
                                <td width="100" align="right"><p><? if($lc_info[$row['ID']]['LC_VALUE']>0)echo number_format($lc_info[$row['ID']]['LC_VALUE'],2);
                                $total_lc_value +=$lc_info[$row['ID']]['LC_VALUE'];
                                ?> </p></td>
                                <td width="100" align="center"><p><? echo $lc_atch_qty_result_info[$row['ID']]['LC_ATCH_QTY'];?> </p></td>
                                <td width="100" align="center"><p> <?echo $garments_item[$gmnt_itm_info[$row["ID"]]['GMTS_ITEM_ID']];?></p></td>
                                <td width="100" align="center"><p><?echo $bill_sub_result_info[$row["INV_ID"]]['SYS_NUMBER']?></p></td>
                                <td width="80" align="center"><p><?echo change_date_format($bill_sub_result_info[$row["INV_ID"]]['SUBMISSION_DATE']) ;?></p></td>
                                <td width="120" align="center"><p><?
                               echo $bill_sub_result_info[$row["INV_ID"]]['INVOICE_NO'];
                              //  echo $row['INVOICE_NO'];?></p></td>
                                <td width="80" align="center"><p><?
                                echo  $bill_sub_result_info[$row["INV_ID"]]['INVOICE_DATE'];
                                //echo change_date_format($row['INVOICE_DATE']); ?></p></td>
                                <td width="100" align="right"><p><? if($bill_sub_result_info[$row["INV_ID"]]['INVOICE_VALUE']>0)
                                echo number_format( $bill_sub_result_info[$row["INV_ID"]]['INVOICE_VALUE'],2);
                               // echo number_format($row['INVOICE_VALUE'],2);
                                $total_invoice_value +=$bill_sub_result_info[$row["INV_ID"]]['INVOICE_VALUE'];
                                ?></p></td>
                                <td width="80" align="center"><p> <?
                                 echo $bill_sub_result_info[$row["INV_ID"]]['INVOICE_QUANTITY'];
                                 // echo $row['INVOICE_QUANTITY'];?></p></td>
                                <td width="150" align="center"><p><? echo $bill_sub_result_info[$row["INV_ID"]]['BANK_REF_NO'];?></p></td>
                                <td width="80" align="center"><p><? echo change_date_format($bill_sub_result_info[$row["INV_ID"]]['BANK_REF_DATE'])?> </p></td>
                                <td width="100" align="center"><p><?
                            
                                echo $bill_sub_result_info[$row["INV_ID"]]['BL_NO'];
                                // echo $row['BL_NO']?></p></td> 
                                <td width="80" align="center"><p><?
                                echo  $bill_sub_result_info[$row["INV_ID"]]['SHIPPING_BILL_N'];
                               // echo $row['SHIPPING_BILL_N']?></p></td>  
                                <td width="80" align="center"><p><?
                                echo change_date_format( $bill_sub_result_info[$row["INV_ID"]]['SHIP_BL_DATE']);
                               // echo change_date_format($row['SHIP_BL_DATE'])?></p></td>
                                
                                <td width="80" align="center"><p><?if($bill_sub_result_info[$row["INV_ID"]]['CARTON_NET_WEIGHT']) echo $bill_sub_result_info[$row["INV_ID"]]['CARTON_NET_WEIGHT']." Kgs"?></p></td>
                                <td width="100" align="center"><p><?echo  $bill_sub_result_info[$row["INV_ID"]]['EXP_FORM_NO'];?></p></td> 
                                <td width="80" align="center"><p><? echo change_date_format($bill_sub_result_info[$row["INV_ID"]]['EXP_FORM_DATE']) ?></p></td>
                                <td width="120" align="right"><p><?if($bill_sub_result_info[$row["INV_ID"]]['RLZ_VALUE']>0)echo number_format($bill_sub_result_info[$row["INV_ID"]]['RLZ_VALUE'],2);
                                $total_rlz_value +=$bill_sub_result_info[$row["INV_ID"]]['RLZ_VALUE'];
                                ?></p></td>
                                <td width="80" align="center"><p><?echo change_date_format( $bill_sub_result_info[$row["INV_ID"]]['RECEIVED_DATE']);?></p></td>
                                <td width="100" align="center"><p></p></td>
                                <td width="80" align="right"><p><?if($bill_sub_result_info[$row["INV_ID"]]['FREIGHT_AMNT_BY_SUPLLIER']>0)echo number_format( $bill_sub_result_info[$row["INV_ID"]]['FREIGHT_AMNT_BY_SUPLLIER'],2);
                                $total_freight_charge_value += $bill_sub_result_info[$row["INV_ID"]]['FREIGHT_AMNT_BY_SUPLLIER'];
                                ?></p></td>
                                <td width="100" align="center"><p><?echo $bill_sub_result_info[$row["INV_ID"]]['PORT_OF_DISCHARGE'];?></p></td>
                                <td width="100" align="center"><p><?echo $lib_country_arr[$bill_sub_result_info[$row["INV_ID"]]['COUNTRY_ID']]?></p></td>

                                <td width="120" align="center"><p></p></td>
                                <td width="80" align="center"><p></p></td>
                                <td width="100" align="center"><p></p></td>

                            </tr>
                        <?
                        $i++;
                    }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="8"><strong>Total : </strong></th>
                    <th></th>
                    <th><?echo number_format($sc_info_total,2);?></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th><?echo number_format($lc_info_total,2);?></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th><?echo number_format($total_invoice_value,2);?></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th><? echo number_format($total_rlz_value,2);?></th>
                    <th></th>
                    <th></th>
                    <th><? echo number_format($total_freight_charge_value,2);?></th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
        </div>
    <?
    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    $name=time();
    $filename=$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc,ob_get_contents());
    echo "$html****$filename";
    exit();	

}
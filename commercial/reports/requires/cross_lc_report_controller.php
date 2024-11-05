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

if($action=="print_button_variable_setting")
{
    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=5 and report_id=296 and is_deleted=0 and status_active=1");
   	$printButton=explode(',',$print_report_format);

	foreach($printButton as $id){
		if($id==108)$buttonHtml.='<input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:50px" class="formbutton" />';	
		if($id==195)$buttonHtml.='<input type="button" name="search" id="search" value="Show 2" onClick="generate_report(2)" style="width:50px" class="formbutton" />';	
	}

    echo "document.getElementById('button_data_panel').innerHTML = '".$buttonHtml."';\n";
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
        order by a.internal_file_no)
        union all
        SELECT * from ( SELECT  DISTINCT (a.internal_file_no) , $select_date as year
        from  com_sales_contract a
        where a.convertible_to_lc <> 1 and a.status_active=1 and a.is_deleted=0 $company_cond $buyer_cond $file_year_cond
        group by a.id, a.internal_file_no,a.insert_date
        order by a.internal_file_no)";
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
	$lien_bank=str_replace("'","",$cbo_lien_bank);
	$file_no=str_replace("'","",$txt_file_no);
	$file_year=str_replace("'","",$cbo_year);

	//echo $company_name."***".$buyer_name."***".$file_no."***".$file_year."***".$lien_bank; die();

    //$company_arr=return_library_array( "select id, company_name from lib_company is_deleted=0",'id','company_name');
    $company_arr= return_library_array("select ID, COMPANY_NAME from lib_company", 'ID','COMPANY_NAME');
    $buyer_arr = return_library_array("select id,buyer_name from lib_buyer where is_deleted=0","id","buyer_name");
    $buyer_address_arr = return_library_array("select id,address_1 from lib_buyer where is_deleted=0","id","address_1");
    $bank_arr = return_library_array("select id,bank_name from lib_bank where is_deleted=0","id","bank_name");
    $bank_address_arr = return_library_array("select id,address from lib_bank where is_deleted=0","id","address");
    $lib_country_arr = return_library_array("select id,country_name from lib_country where is_deleted=0","id","country_name");
    $suplier_name_arr=return_library_array( "select id,supplier_name from  lib_supplier where is_deleted=0  and status_active=1 order by supplier_name",'id','supplier_name');

    if($file_year!=0) $file_year_cond=" and to_char(a.insert_date,'YYYY')=$file_year"; else $file_year_cond="";
    $select_date=" to_char(a.insert_date,'YYYY')";
    if($buyer_name!=0) $buyer_cond=" and a.buyer_name in($cbo_buyer_name)"; else $buyer_cond="";
    if($buyer_name!=0) $buyer_cond_inv=" and c.BUYER_ID in($cbo_buyer_name)"; else $buyer_cond_inv="";
    if($company_name!=0) $company_cond=" and a.beneficiary_name in($cbo_company_name)"; else $company_cond="";
    if($company_name!=0) $pi_company_cond=" and a.importer_id in($cbo_company_name)"; else $company_cond="";
    if($lien_bank!=0) $bank_cond=" and a.LIEN_BANK in($lien_bank)"; else $bank_cond="";
    if($txt_file_no!='') $file_no_cond=" and a.internal_file_no in($txt_file_no)"; else $file_no_cond="";

    
   // if($txt_file_no!="") {echo  $file_no_cond=" and d.internal_file_no in ($txt_file_no)";} else {$file_no_cond="";}
   
    if ($cbo_year!='') 
    {
        $lc_file_year=" and d.lc_year=$cbo_year";
        $sc_file_year=" and d.sc_year=$cbo_year";
    }
    else
    { $lc_file_year="";$sc_file_year="";}


    // echo $report_type;
    if($report_type==1) // show button
    {
        $sql_po_brkdwn = "SELECT a.BUYER_NAME,a.INTERNAL_FILE_NO,min(a.LC_DATE) as LC_SC_DATE
        FROM com_export_lc a,com_export_lc_order_info b, wo_po_details_master d ,wo_po_break_down c
        WHERE  a.id=b.COM_EXPORT_LC_ID and b.wo_po_break_down_id=c.id and c.job_no_mst=d.job_no   and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1  and d.is_deleted=0 and a.status_active=1
        $file_year_cond $buyer_cond $company_cond $file_no_cond
        group by a.BUYER_NAME,a.INTERNAL_FILE_NO
        UNION ALL
        SELECT a.BUYER_NAME,a.INTERNAL_FILE_NO,min(a.CONTRACT_DATE) as LC_SC_DATE
        FROM com_sales_contract a,com_sales_contract_order_info b, wo_po_details_master d ,wo_po_break_down c
        WHERE a.id=b.COM_SALES_CONTRACT_ID and b.wo_po_break_down_id=c.id and c.job_no_mst=d.job_no  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.status_active=1 
        $file_year_cond $buyer_cond $company_cond $file_no_cond
        group by a.BUYER_NAME,a.INTERNAL_FILE_NO
        ";
        $summ_result = sql_select($sql_po_brkdwn);
       

       // echo $min_date; die();
        //echo $sql_po_brkdwn;

        $main_query = "SELECT a.id as LC_SC_ID,C.ID AS PO_BREAKDOWN_ID,a.INTERNAL_FILE_NO,a.EXPIRY_DATE,c.PO_NUMBER,d.STYLE_REF_NO,c.PO_QUANTITY,c.UNIT_PRICE,d.GMTS_ITEM_ID,
        e.FABRIC_COST, e.TRIMS_COST, e.EMBEL_COST, e.WASH_COST, e.COMM_COST, e.COMMISSION,e.INCENTIVES_PRE_COST, e.LAB_TEST, e.INSPECTION, e.CM_COST, e.CALCULATIVE_CMCOST, e.FREIGHT, e.CURRIER_PRE_COST, e.COMMON_OH, e.INTEREST_COST, e.STUDIO_COST
        FROM com_export_lc a,com_export_lc_order_info b, wo_po_details_master d ,wo_po_break_down c
        left join wo_pre_cost_dtls e on c.JOB_NO_MST = e.JOB_NO and e.is_deleted=0 and e.status_active=1 
        WHERE  a.id=b.COM_EXPORT_LC_ID and b.wo_po_break_down_id=c.id and c.job_no_mst=d.job_no   and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1  and d.is_deleted=0 and a.status_active=1
        $file_year_cond $buyer_cond $company_cond $file_no_cond
        UNION ALL
        SELECT a.id as LC_SC_ID,C.ID AS PO_BREAKDOWN_ID,a.INTERNAL_FILE_NO,a.EXPIRY_DATE,c.PO_NUMBER,d.STYLE_REF_NO,c.PO_QUANTITY,c.UNIT_PRICE,d.GMTS_ITEM_ID,
        e.FABRIC_COST, e.TRIMS_COST, e.EMBEL_COST, e.WASH_COST, e.COMM_COST, e.COMMISSION,e.INCENTIVES_PRE_COST, e.LAB_TEST, e.INSPECTION, e.CM_COST, e.CALCULATIVE_CMCOST, e.FREIGHT, e.CURRIER_PRE_COST, e.COMMON_OH, e.INTEREST_COST, e.STUDIO_COST
        FROM com_sales_contract a,com_sales_contract_order_info b, wo_po_details_master d ,wo_po_break_down c
        left join wo_pre_cost_dtls e on c.JOB_NO_MST = e.JOB_NO and e.is_deleted=0 and e.status_active=1 
        WHERE a.id=b.COM_SALES_CONTRACT_ID and b.wo_po_break_down_id=c.id and c.job_no_mst=d.job_no  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.status_active=1 
        $file_year_cond $buyer_cond $company_cond $file_no_cond
        ";
        //echo $main_query; die();
        $result = sql_select($main_query);

        foreach($result as $row)
        {
            $lc_sc_ids .= $row['LC_SC_ID'].',';
        }
        $all_lc_sc_id = ltrim(implode(",", array_unique(explode(",", chop($lc_sc_ids, ",")))), ',');


        // $invoice_qnty = "SELECT A.PO_BREAKDOWN_ID, SUM(A.CURRENT_INVOICE_QNTY) AS INVOICE_QNTY, SUM(A.CURRENT_INVOICE_RATE) AS INVOICE_RATE
        // ,C.LC_SC_ID,max(C.EX_FACTORY_DATE) as EX_FACTORY_DATE
        // FROM COM_EXPORT_INVOICE_SHIP_DTLS A, COM_EXPORT_INVOICE_SHIP_MST C 
        // WHERE A.MST_ID=C.ID AND A.IS_DELETED=0 AND A.STATUS_ACTIVE=1 AND A.CURRENT_INVOICE_QNTY>0  
        // AND C.BENIFICIARY_ID IN ($company_name) $buyer_cond_inv AND C.LC_SC_ID IN ($all_lc_sc_id)
        // GROUP BY C.DISCOUNT_AMMOUNT,A.PO_BREAKDOWN_ID,C.LC_SC_ID";

        $invoice_qnty = "SELECT A.PO_BREAKDOWN_ID, A.CURRENT_INVOICE_QNTY AS INVOICE_QNTY, A.CURRENT_INVOICE_RATE AS INVOICE_RATE
        ,C.LC_SC_ID,max(C.EX_FACTORY_DATE) as EX_FACTORY_DATE
        FROM COM_EXPORT_INVOICE_SHIP_DTLS A, COM_EXPORT_INVOICE_SHIP_MST C 
        WHERE A.MST_ID=C.ID AND A.IS_DELETED=0 AND A.STATUS_ACTIVE=1 AND A.CURRENT_INVOICE_QNTY>0  
        AND C.BENIFICIARY_ID IN ($company_name) $buyer_cond_inv AND C.LC_SC_ID IN ($all_lc_sc_id)
        GROUP BY C.DISCOUNT_AMMOUNT,A.PO_BREAKDOWN_ID,C.LC_SC_ID,a.CURRENT_INVOICE_QNTY,a.CURRENT_INVOICE_RATE";
        
        //echo  $invoice_qnty; //die();
        $invoiceSQLresult = sql_select($invoice_qnty);
        $invoiceArr = array();
        foreach ($invoiceSQLresult as $key => $val) {
            $invoiceArr[$val['LC_SC_ID']][$val['PO_BREAKDOWN_ID']]['INVOICE_QNTY'] += $val['INVOICE_QNTY'];
            $invoiceArr[$val['LC_SC_ID']][$val['PO_BREAKDOWN_ID']]['INVOICE_RATE'] = $val['INVOICE_RATE'];
            $invoiceArr[$val['LC_SC_ID']][$val['PO_BREAKDOWN_ID']]['SHIPMENT_DATE'] = $val['EX_FACTORY_DATE'];
        }
        ob_start();
        ?>
        <br>
        <div>
            <table width="2050" cellpadding="0" cellspacing="0" id="caption" align="left">
                <tr>
                    <td align="center" width="100%" colspan="10" class="form_caption" ><strong style="font-size:18px">Company Name:<? echo $company_arr[$company_name]; ?></strong></td>
                </tr>
                <tr>
                    <td align="center" width="100%" colspan="10" class="form_caption" ><strong style="font-size:18px"><? echo $report_title; ?></strong></td>
                </tr>
            </table>
        </div>
        <br>
    
 
        <div style="width:2050px;">
            <table width="600" class="rpt_table" style="margin-left: 15px; float: left;" cellpadding="0" cellspacing="0" border="1" rules="all">
                <tr>
                    <td width="150"><b>Buyer</b></td>
                    <td width="150"><? echo  $buyer_arr[$summ_result[0]['BUYER_NAME']];?></td>
                    <td width="150"><b>Date</b></td>
                    <td width="150"><?
                    if($summ_result[0]['LC_SC_DATE'] != '' && $summ_result[1]['LC_SC_DATE'] != '')
                    {
                        if($summ_result[0]['LC_SC_DATE'] < $summ_result[1]['LC_SC_DATE']){
                            $min_date = $summ_result[0]['LC_SC_DATE'];
                        }
                        else{
                            $min_date = $summ_result[1]['LC_SC_DATE'];
                        }
                    }
                    else{
                        $min_date = $summ_result[0]['LC_SC_DATE'];
                    }
                    echo change_date_format($min_date);?></td> 
                    <td width="150"><b>Job No.</b></td>
                    <td width="150"><? echo $summ_result[0]['INTERNAL_FILE_NO'];?></td>
                </tr>
            </table>
            <table width="2020" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
                        <th width="100"><p>Style</p></th>
                        <th width="80"><p>PO</p></th>
                        <th width="100"><p>Item</p></th>
                        <th width="100"><p>Qty/Pcs</p></th>
                        <th width="80"><p>FOB</p></th>
                        <th width="100" title="PO Qty*Po Avg Rate"><p>Value</p></th>
                        <th width="120"><p>CM/Pc</p></th>
                        <th width="100" title="PO Qty*CM COST"><p>Total CM</p></th>
                        <th width="100" title="Commercial Cost + Studio Cost"><p>Commercial <br> Cost/UP</p></th>
                        <th width="80" title="PO Qty*COMMERCIAL COST"><p>Cmm Value</p></th>
                        <th width="100" title="PO Qty*CURRIER COST "><p>Courier Cost</p></th>
                        <th width="100" title="PO Qty*INSPECTION COST "><p>Inspection Cost</p></th>
                        <th width="80" title="PO Qty*LAB TEST COST "><p>Lab Test</p></th>
                        <th width="100" title="PO Qty*COMMISSION COST "><p>BHC</p></th>
                        <th width="100" title="PO Qty*(FABRIC COST+TRIMS COST+EMBEL COST+WASH COST)"><p>Value for BTB</p></th>
                        <th width="100"><p>Shipment /Pcs</p></th>
                        <th width="100"  title="PO QTY- Shipment /Pcs"><p>Shipment <br> Balane/Pcs</p></th>
                        <th width="80"><p>Shipment Date</p></th>
                        <th width="80"><p>Expiry Date</p></th>
                        <th width="80"><p>Ship/U/Price</p></th>
                        <th width="100" title="Shipment Pcs * Shipment Price "><p>Ship/Value</p></th>

                    </tr>
                </thead>
            </table>
            <div style="width:2020px; overflow-y: scroll; max-height:400px; overflow-x:hidden;" id="scroll_body">
                <table cellspacing="0" width="2020"  border="1" rules="all" class="rpt_table" id="tbl_body">
                    <tbody id="table_body" >
                        <?
                            $i=1;
                            foreach($result as $row)
                            {
                                if ($i%2==0) $bgcolor="#E9F3FF";
                                else $bgcolor="#FFFFFF";

                                $value_for_btb = ($row['PO_QUANTITY']*($row['FABRIC_COST']+$row['TRIMS_COST']+$row['EMBEL_COST']+$row['WASH_COST']));
                                ?>
                                    <tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                                        <td width="100" align="center"><p><? echo $row['STYLE_REF_NO']; ?></p></td>
                                        <td width="80" align="center"><p><? echo $row['PO_NUMBER']; ?></p></td>
                                        <td width="100" align="center"><p> <? echo  $garments_item[$row['GMTS_ITEM_ID']]; ?></p></td>
                                        <td width="100" align="center"> <p><? echo  $row['PO_QUANTITY']; 
                                        $totoal_po_qty += $row['PO_QUANTITY'];
                                        ?> </p></td> 
                                        <td width="80" align="right"><p><? echo "$  ". $row['UNIT_PRICE']; 
                                        $total_fob += $row['UNIT_PRICE'];?> &nbsp;</p></td>
                                        <td width="100" align="right" ><p> <? $value = $row['PO_QUANTITY']*$row['UNIT_PRICE']; echo "$ ". number_format($value,2);
                                        $total_value += $value;?></p></td>
                                        <td width="120" align="right"><p><? echo "$ ".$row['CM_COST'];
                                        $total_cm += $row['CM_COST'];?></p></td>
                                        <td width="100" align="right"><p> <? $cm_value = $row['PO_QUANTITY']*$row['CM_COST']; echo "$ ".number_format($cm_value,2);
                                        $total_cm_value += $cm_value;
                                        ?></p></td> 
                                        <td width="100" align="right"><p> <? echo "$ ".($row['COMM_COST']+$row['STUDIO_COST']);
                                        $total_comm_cost +=($row['COMM_COST']+$row['STUDIO_COST']);?></p></td>
                                        <td width="80" align="right"><p> <? $comm_value = ($row['PO_QUANTITY']*($row['COMM_COST']+$row['STUDIO_COST'])); echo "$ ".number_format($comm_value,2);
                                        $total_comm_value += $cm_value;?></p></td> 
                                        <td width="100" align="right"><p> <? $currier_value = $row['PO_QUANTITY']*$row['CURRIER_PRE_COST']; echo "$ ".number_format($currier_value,2);
                                        $total_currier_value += $currier_value;?></p></td> 
                                        <td width="100" align="right"><p> <? $inspection_value = $row['PO_QUANTITY']*$row['INSPECTION']; echo "$ ".number_format($inspection_value,2);
                                        $total_inspection_value += $inspection_value;?></p></td>
                                        <td width="80" align="right"><p> <? $lab_test_value = $row['PO_QUANTITY']*$row['LAB_TEST']; echo "$ ".number_format($lab_test_value,2);
                                        $total_lab_test_value += $lab_test_value;?></p></td>
                                        <td width="100" align="right"><p> <? $commission_value = $row['PO_QUANTITY']*$row['COMMISSION']; echo "$ ".number_format($commission_value,2);
                                        $total_commission_value += $commission_value;?></p></td>
                                        <td width="100" align="right"><p> <? echo "$ ".number_format($value_for_btb,2);
                                        $total_value_for_btb += $value_for_btb;?></p></td>
                                        <td width="100" align="right"><p> <? echo number_format($invoiceArr[$row['LC_SC_ID']][$row['PO_BREAKDOWN_ID']]['INVOICE_QNTY'],2);
                                        $total_shpmnt_pcs += $invoiceArr[$row['LC_SC_ID']][$row['PO_BREAKDOWN_ID']]['INVOICE_QNTY'];?></p></td>
                                        <td width="100" align="right"><p> <?  $shipmnt_bal =$row['PO_QUANTITY']-$invoiceArr[$row['LC_SC_ID']][$row['PO_BREAKDOWN_ID']]['INVOICE_QNTY']; echo number_format($shipmnt_bal,2);
                                        $total_shpmnt_balance += $shipmnt_bal;
                                        ?></p></td> 
                                        <td width="80" align="center"><p> <? echo  change_date_format($invoiceArr[$row['LC_SC_ID']][$row['PO_BREAKDOWN_ID']]['SHIPMENT_DATE']);?></p></td>
                                        <td width="80" align="center"><p> <? echo  change_date_format($row['EXPIRY_DATE']);?></p></td>
                                        <td width="80" align="right"><p> <? echo "$ ".number_format($invoiceArr[$row['LC_SC_ID']][$row['PO_BREAKDOWN_ID']]['INVOICE_RATE'],2);?></p></td>
                                        <td width="100" align="right"><p> <? $ship_val = ($invoiceArr[$row['LC_SC_ID']][$row['PO_BREAKDOWN_ID']]['INVOICE_QNTY']*$invoiceArr[$row['LC_SC_ID']][$row['PO_BREAKDOWN_ID']]['INVOICE_RATE']); echo "$ ".number_format($ship_val ,2);
                                        $total_ship_val+=$ship_val ;
                                        ?></p></td>
                                        
                                    </tr>
                                <?
                                $i++;
                            }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3"><strong>Total : </strong></th>
                            <th><p><? echo number_format($totoal_po_qty,2)?>&nbsp;</p></th>
                            <th><p><? echo number_format($total_fob,2)?>&nbsp;</p></th>
                            <th><p><? echo number_format($total_value,2)?>&nbsp;</p></th>
                            <th><p><? echo number_format($total_cm,2)?>&nbsp;</p></th>
                            <th><p><? echo number_format($total_cm_value,2)?>&nbsp;</p></th>
                            <th><p><? echo number_format($total_comm_cost,2)?>&nbsp;</p></th>
                            <th><p><? echo number_format($total_comm_value,2)?>&nbsp;</p></th>
                            <th><p><? echo number_format($total_currier_value,2)?>&nbsp;</p></th>
                            <th><p><? echo number_format($total_inspection_value,2)?>&nbsp;</p></th>
                            <th><p><? echo number_format($total_lab_test_value,2)?>&nbsp;</p></th>
                            <th><p><? echo number_format($total_commission_value,2)?>&nbsp;</p></th>
                            <th><p><? echo number_format($total_value_for_btb,2)?>&nbsp;</p></th>
                            <th><p><? echo number_format($total_shpmnt_pcs,2)?>&nbsp;</p></th>
                            <th><p><? echo number_format($total_shpmnt_balance,2)?>&nbsp;</p></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th><p><? echo number_format($total_ship_val,2)?>&nbsp;</p></th>
                        </tr>
                        <tr>
                            <th colspan="5"><strong>File Avg FOB : </strong></th>
                            <th title="Total Val/ Total Qty"><p><? echo number_format($total_value/$totoal_po_qty,2)?>&nbsp;</p></th>
                            <th colspan="15"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>


        <?

            $lib_body_part_arr=return_library_array("select id, body_part_full_name from lib_body_part", "id", "body_part_full_name");
            $item_group_arr=return_library_array( "select id, item_name from lib_item_group where status_active=1 and is_deleted=0", "id", "item_name"  );

            //if($txt_file_no!='') $file_no_cond2=" and a.ORDER_FILE_NO in($txt_file_no)"; else $file_no_cond2="";
            if($txt_file_no!='') $file_no_cond2=" AND (a.INTERNAL_FILE_NO in($txt_file_no) or a.ORDER_FILE_NO in($txt_file_no))"; else $file_no_cond2="";
            
            $sql_other_file = sql_select("SELECT A.ORDER_FILE_NO,A.PI_NUMBER,A.INTERNAL_FILE_NO from COM_PI_MASTER_DETAILS A WHERE A.STATUS_ACTIVE =1 AND A.IS_DELETED=0 $buyer_cond $pi_company_cond $file_no_cond2");
            foreach($sql_other_file as $row)
            {
                $internal_file .=  $row['INTERNAL_FILE_NO'].',';
            }
            //$all_internal_file = ltrim(implode(",", array_unique(explode(",", chop($internal_file, ",")))), ',');
           
            $pi_sql = "SELECT a.ID,A.PI_NUMBER,A.INTERNAL_FILE_NO,A.SUPPLIER_ID,a.NET_TOTAL_AMOUNT,a.ITEM_CATEGORY_ID,A.ORDER_FILE_NO,D.LC_NUMBER,D.LC_DATE,b.BODY_PART_ID,b.ITEM_GROUP,a.PI_DATE,d.id as BTB_ID
            FROM COM_PI_ITEM_DETAILS B,COM_PI_MASTER_DETAILS A
            left join COM_BTB_LC_PI C on C.PI_ID =A.ID  AND c.status_active = 1 AND c.is_deleted = 0
            left join  COM_BTB_LC_MASTER_DETAILS d on d.ID = C.COM_BTB_LC_MASTER_DETAILS_ID  and d.status_active=1 and d.is_deleted=0
            WHERE A.ID=B.PI_ID AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0
            $buyer_cond $pi_company_cond $file_no_cond2 $file_year_cond ";
            // echo $pi_sql; 
            $pi_result = sql_select($pi_sql);
            $pi_data_arr=array();
            foreach($pi_result as $row)
            {
                $pi_data_arr[$row['ID']]['ID'] = $row['ID'];
                $pi_data_arr[$row['ID']]['PI_NUMBER'] = $row['PI_NUMBER'];
                $pi_data_arr[$row['ID']]['INTERNAL_FILE_NO'] = $row['INTERNAL_FILE_NO'];
                $pi_data_arr[$row['ID']]['SUPPLIER_ID'] = $row['SUPPLIER_ID'];
                $pi_data_arr[$row['ID']]['NET_TOTAL_AMOUNT'] = $row['NET_TOTAL_AMOUNT'];
                $pi_data_arr[$row['ID']]['ITEM_CATEGORY_ID'] = $row['ITEM_CATEGORY_ID'];
                $pi_data_arr[$row['ID']]['LC_NUMBER'] = $row['LC_NUMBER'];
                $pi_data_arr[$row['ID']]['LC_DATE'] = $row['LC_DATE'];
                $pi_data_arr[$row['ID']]['BODY_PART_ID'] = $row['BODY_PART_ID'];
                $pi_data_arr[$row['ID']]['ITEM_GROUP'] = $row['ITEM_GROUP'];
                $pi_data_arr[$row['ID']]['PI_DATE'] = $row['PI_DATE'];
                $pi_data_arr[$row['ID']]['BANK_ACC_DATE'] = $row['BANK_ACC_DATE'];
                $pi_data_arr[$row['ID']]['BTB_ID'] = $row['BTB_ID'];
                $pi_data_arr[$row['ID']]['ORDER_FILE_NO'] = $row['ORDER_FILE_NO'];
                $pi_item_grp_arr .= $item_group_arr[$row['ITEM_GROUP']].',';
                $pi_body_part_arr .= $lib_body_part_arr[$row['BODY_PART_ID']].',';
                if($row['BTB_ID']!=''){
                    $btb_ids .= $row['BTB_ID'].',';
                }
            }
            
            $all_btb_ids = ltrim(implode(",", array_unique(explode(",", chop($btb_ids, ",")))), ',');
            $sql_cond .= "  BTB_LC_ID IN ('" . str_replace(",", "','", $all_btb_ids) . "')";

            $all_item_grp = ltrim(implode(",", array_unique(explode(",", chop($pi_item_grp_arr, ",")))), ',');
            $all_pi_body_part = ltrim(implode(",", array_unique(explode(",", chop($pi_body_part_arr, ",")))), ',');

            $inv_sql_result=sql_select("SELECT ID,BTB_LC_ID,BANK_ACC_DATE FROM com_import_invoice_mst WHERE $sql_cond");
            $accp_arr =array();
            foreach($inv_sql_result as $row){
                    $accp_arr[$row['BTB_LC_ID']]['BANK_ACC_DATE'] = $row['BANK_ACC_DATE'];
            }
        ?>
        <br>
        <div style="width:1150px; float:left;">
            <table width="1120" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr><th width="100">BTB Details</th></tr>
                    <tr>
                        <th width="100"><p>Supplier</p></th>
                        <th width="100"><p>Item</p></th>
                        <th width="100"><p>PI NO</p></th>
                        <th width="100"><p>BTB</p></th>
                        <th width="80"><p>BTB Date</p></th>
                        <th width="100"><p>Value</p></th>
                        <th width="120"><p>Total Value</p></th>
                        <th width="90"><p>% on Master L/C </p></th>
                        <th width="80"><p>Input Date</p></th>
                        <th width="120"><p>Remarks</p></th>
                        <th width="80"><p>Accep Date</p></th>
                        <th width="70"><p>Accep %</p></th>
                    </tr>
                </thead>
            </table>
            <div style="width:1120px; overflow-y: scroll; max-height:400px; overflow-x:hidden;" id="scroll_body1">
                <table cellspacing="0" width="1100"  border="1" rules="all" class="rpt_table" id="tbl_body1">
                    <tbody id="table_body" >
                        <?
                        
                            $j=1;
                            $total_val_pi =0;
                            $bal =0;
                            foreach($pi_data_arr as $row)
                            {
                                if($j==1)
                                {
                                    if($row['ORDER_FILE_NO']!='' && $row['ORDER_FILE_NO']==$file_no)
                                    {
                                        $bal = $row['NET_TOTAL_AMOUNT'];
                                    }
                                    elseif($row['ORDER_FILE_NO']!='' && $row['ORDER_FILE_NO']!=$file_no)
                                    {
                                        $bal=$bal-$row['NET_TOTAL_AMOUNT'];
                                    }
                                    else
                                    {
                                        $bal = $row['NET_TOTAL_AMOUNT'];
                                    }
                                    
                                }
                                else
                                {
                                    if($row['ORDER_FILE_NO']!='' && $row['ORDER_FILE_NO']==$file_no)
                                    {
                                        $bal=$bal+$row['NET_TOTAL_AMOUNT'];
                                    }
                                    elseif($row['ORDER_FILE_NO']!='' && $row['ORDER_FILE_NO']!=$file_no)
                                    {
                                        $bal=$bal-$row['NET_TOTAL_AMOUNT'];
                                    }
                                    else
                                    {
                                        $bal=$bal+$row['NET_TOTAL_AMOUNT'];
                                    }
                                    //$bal=$bal+$row['NET_TOTAL_AMOUNT'];
                                }

                                $mst_lc_perc = ($bal/$total_value)*100;
                                $accp_perc = ($row['NET_TOTAL_AMOUNT']/$total_value)*100;

                                $diff = $total_value_for_btb-$bal;
                                $total_mst_perc = ($diff/$total_value)*100;

                                if ($j%2==0) $bgcolor="#E9F3FF";
                                else $bgcolor="#FFFFFF";
                                ?>
                                    <tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $j;?>','<? echo $bgcolor;?>')" id="tr_<? echo $j;?>">
                                        <td width="100" align="center"><p><? echo $suplier_name_arr[$row['SUPPLIER_ID']]; ?></p></td>
                                        <td width="100" align="center"><p><? 
                                        
                                        if($row['ITEM_CATEGORY_ID']==4){
                                            //echo $item_group_arr[$row['ITEM_GROUP']]; 
                                            echo $all_item_grp;
                                        }
                                        else{
                                            //echo $lib_body_part_arr[$row['BODY_PART_ID']]; 
                                            echo $all_pi_body_part; 
                                        }
                                        ?></p></td>
                                        <td width="100" align="center"><p> <? echo $row['PI_NUMBER']; ?></p></td>
                                        <td width="100" align="center"> <p><? echo  $row['LC_NUMBER'];  ?> </p></td> 
                                        <td width="80" align="center"><p><? echo change_date_format($row['LC_DATE']); ?></p></td>
                                        <td width="100" align="right"><p> <?

                                        if($row['ORDER_FILE_NO']!='' && $row['ORDER_FILE_NO']==$file_no)
                                        {
                                            echo number_format($row['NET_TOTAL_AMOUNT'],2);  
                                            $total_val_pi +=$row['NET_TOTAL_AMOUNT'];
                                        }
                                        elseif($row['ORDER_FILE_NO']!='' && $row['ORDER_FILE_NO']!=$file_no)
                                        {
                                            echo "(". number_format($row['NET_TOTAL_AMOUNT'],2).")";
                                        }
                                        else
                                        {
                                            echo  number_format($row['NET_TOTAL_AMOUNT'],2);   
                                            $total_val_pi +=$row['NET_TOTAL_AMOUNT'];
                                        }
                                        ?>&nbsp;</p></td>
                                        <td width="120" align="right"><p><? echo number_format($bal,2);
                                        
                                        ?>&nbsp;</p></td>
                                        <td width="90" align="center"><p><? echo round($mst_lc_perc) ." %"  ;
                                        ?></p></td>
                                        <td width="80" align="center"><p><? echo change_date_format($row['PI_DATE'])?></p></td>
                                        <td width="120"><p><?
                                        if($row['ORDER_FILE_NO']!='' && $row['INTERNAL_FILE_NO']!=$file_no)
                                        {
                                            echo "FOR : ".$row['INTERNAL_FILE_NO'];
                                        }
                                        elseif($row['ORDER_FILE_NO']!='' && $row['ORDER_FILE_NO']!=$file_no)
                                        {
                                            echo "FROM : ".$row['ORDER_FILE_NO'];
                                        }
                                       
                                
                                        ?></p></td>
                                        <td width="80" align="center"><p><? echo change_date_format($accp_arr[$row['BTB_ID']]['BANK_ACC_DATE'])?></p></td>
                                        <td width="70" align="center"><p><? echo round($accp_perc) ." %"  ;?></p></td>
                                      
                                    </tr>
                                <?
                                
                                $j++;

                                
                                // if($row['ORDER_FILE_NO']!='' && $row['INTERNAL_FILE_NO']=$file_no)
                                // { 
                                //    // $total_val_pi -= $row['NET_TOTAL_AMOUNT'];
                                // }
                                // else{
                                //     $total_val_pi +=$row['NET_TOTAL_AMOUNT'];
                                // }

                            }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5"><strong>Total : </strong></th>
                            <th><p><? echo number_format($total_val_pi,2)?>&nbsp;</p></th>
                            <th title="Total BTB Val - Total Val"><p><? echo number_format($total_value_for_btb-$bal,2)?>&nbsp;</p></th>
                            <th><p><? echo round($total_mst_perc)." %";?></p></th>
                            <th><p></p></th>
                            <th><p></p></th>
                            <th><p></p></th>
                            <th><p></p></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <br><br>
        </div>
        <?
        
    }
    elseif($report_type==2)  // show 2
    {
        // $sql_show2 = "SELECT a.id as SC_LC_ID , a.BUYER_NAME,a.INTERNAL_FILE_NO,a.LC_DATE as LC_SC_DATE,a.EXPORT_LC_NO as SC_LC_NO,a.LC_VALUE as LC_SC_VALUE,max(a.LAST_SHIPMENT_DATE) as LAST_SHIPMENT_DATE,max(a.EXPIRY_DATE) as EXPIRY_DATE,1 as TYPE,a.LIEN_BANK,max(b.AMENDMENT_NO) as AMENDMENT_NO,max(b.AMENDMENT_DATE) as AMENDMENT_DATE
        // FROM com_export_lc a
        // left join COM_EXPORT_LC_AMENDMENT b on a.id = b.EXPORT_LC_ID and b.status_active =1 and b.is_deleted =0
        // WHERE a.status_active=1 AND A.REPLACEMENT_LC <>1
        // $file_year_cond $buyer_cond $company_cond $file_no_cond
        // group by a.id,a.BUYER_NAME,a.INTERNAL_FILE_NO,a.EXPORT_LC_NO,a.LC_VALUE,a.LC_DATE,a.LIEN_BANK
        // UNION ALL
        // SELECT a.id as SC_LC_ID,a.BUYER_NAME,a.INTERNAL_FILE_NO,a.CONTRACT_DATE as LC_SC_DATE,a.CONTRACT_NO as SC_LC_NO,a.CONTRACT_VALUE as LC_SC_VALUE,
        // max(a.LAST_SHIPMENT_DATE) as LAST_SHIPMENT_DATE,max(a.EXPIRY_DATE) as EXPIRY_DATE,2 as TYPE,a.LIEN_BANK,max(b.AMENDMENT_NO) as AMENDMENT_NO,max(b.AMENDMENT_DATE) as AMENDMENT_DATE
        // FROM com_sales_contract a
        // left join COM_SALES_CONTRACT_AMENDMENT b on a.id = b.CONTRACT_ID and b.status_active =1 and b.is_deleted =0
        // WHERE a.status_active=1 
        // $file_year_cond $buyer_cond $company_cond $file_no_cond
        // group by a.id,a.BUYER_NAME,a.INTERNAL_FILE_NO,a.CONTRACT_NO,a.CONTRACT_VALUE,a.CONTRACT_DATE,a.LIEN_BANK
        // ";
        
        $sql_show2 = "SELECT a.id as SC_LC_ID , a.BUYER_NAME,a.INTERNAL_FILE_NO,a.LC_DATE as LC_SC_DATE,a.EXPORT_LC_NO as SC_LC_NO,a.LC_VALUE as LC_SC_VALUE,max(a.LAST_SHIPMENT_DATE) as LAST_SHIPMENT_DATE,max(a.EXPIRY_DATE) as EXPIRY_DATE,1 as TYPE,a.LIEN_BANK,max(b.AMENDMENT_NO) as AMENDMENT_NO,max(b.AMENDMENT_DATE) as AMENDMENT_DATE,c.id as ATTCH_ID,0 as CONVERTIBLE_TO_LC
        FROM com_export_lc a
        left join COM_EXPORT_LC_ATCH_SC_INFO c on c.COM_EXPORT_LC_ID = a.id and c.status_active =1 and c.is_deleted =0
        left join COM_EXPORT_LC_AMENDMENT b on a.id = b.EXPORT_LC_ID and b.status_active =1 and b.is_deleted =0
        WHERE a.status_active=1 AND A.REPLACEMENT_LC <>1
        $file_year_cond $buyer_cond $company_cond $file_no_cond
        group by a.id,a.BUYER_NAME,a.INTERNAL_FILE_NO,a.EXPORT_LC_NO,a.LC_VALUE,a.LC_DATE,a.LIEN_BANK,c.id
        UNION ALL
        SELECT a.id as SC_LC_ID,a.BUYER_NAME,a.INTERNAL_FILE_NO,a.CONTRACT_DATE as LC_SC_DATE,a.CONTRACT_NO as SC_LC_NO,a.CONTRACT_VALUE as LC_SC_VALUE,
        max(a.LAST_SHIPMENT_DATE) as LAST_SHIPMENT_DATE,max(a.EXPIRY_DATE) as EXPIRY_DATE,2 as TYPE,a.LIEN_BANK,max(b.AMENDMENT_NO) as AMENDMENT_NO,max(b.AMENDMENT_DATE) as AMENDMENT_DATE,c.id as ATTCH_ID,a.CONVERTIBLE_TO_LC as CONVERTIBLE_TO_LC
        FROM com_sales_contract a
        left join COM_EXPORT_LC_ATCH_SC_INFO c on a.id = c.COM_SALES_CONTRACT_ID and c.status_active =1 and c.is_deleted =0
        left join COM_SALES_CONTRACT_AMENDMENT b on a.id = b.CONTRACT_ID and b.status_active =1 and b.is_deleted =0
        WHERE a.status_active=1 
        $file_year_cond $buyer_cond $company_cond $file_no_cond
        group by a.id,a.BUYER_NAME,a.INTERNAL_FILE_NO,a.CONTRACT_NO,a.CONTRACT_VALUE,a.CONTRACT_DATE,a.LIEN_BANK,c.id,a.CONVERTIBLE_TO_LC
        order by SC_LC_ID
        ";

     

       // echo $sql_show2;
        $summ_result = sql_select($sql_show2);
       
        foreach($summ_result as $row)
        {
            $sc_lc_id .= $row['SC_LC_ID'].',';
        }
        $all_sc_lc_id = ltrim(implode(",", array_unique(explode(",", chop($sc_lc_id, ",")))), ',');

        $sql = "SELECT A.ID, A.COM_SALES_CONTRACT_ID, B.EXPORT_LC_NO, A.COM_EXPORT_LC_ID,b.LC_VALUE as LC_SC_VAL,b.LC_DATE,LIEN_BANK,b.LAST_SHIPMENT_DATE as LAST_SHIPMENT_DATE,b.EXPIRY_DATE as EXPIRY_DATE,max(c.AMENDMENT_NO) as AMENDMENT_NO,max(c.AMENDMENT_DATE) as AMENDMENT_DATE
        from com_export_lc_atch_sc_info a, COM_EXPORT_LC b 
        left join COM_EXPORT_LC_AMENDMENT c on b.id = c.EXPORT_LC_ID and c.status_active =1 and c.is_deleted =0
        where a.COM_EXPORT_LC_ID=b.id and a.COM_SALES_CONTRACT_ID in ($all_sc_lc_id) and a.is_deleted = 0 and a.status_active=1 
        group by A.ID, A.COM_SALES_CONTRACT_ID, B.EXPORT_LC_NO, A.COM_EXPORT_LC_ID,b.LC_VALUE,b.LC_DATE,LIEN_BANK,b.LAST_SHIPMENT_DATE,b.EXPIRY_DATE
        order by a.id ";
        //echo $sql . "<br>";
        $data_array = sql_select($sql);
        $rplc_arr = array();
        foreach($data_array as $row)
        {
            //$rplc_arr[$row['COM_SALES_CONTRACT_ID']]['EXPORT_LC_NO'] =  $row['EXPORT_LC_NO'];
            $rplc_arr[$row['ID']]['EXPORT_LC_NO'] =  $row['EXPORT_LC_NO'];
            $rplc_arr[$row['ID']]['LC_SC_VAL'] =  $row['LC_SC_VAL'];
            $rplc_arr[$row['ID']]['LC_DATE'] =  $row['LC_DATE'];
            $rplc_arr[$row['ID']]['LIEN_BANK'] =  $row['LIEN_BANK'];
            $rplc_arr[$row['ID']]['LAST_SHIPMENT_DATE'] =  $row['LAST_SHIPMENT_DATE'];
            $rplc_arr[$row['ID']]['EXPIRY_DATE'] =  $row['EXPIRY_DATE'];
            $rplc_arr[$row['ID']]['AMENDMENT_NO'] =  $row['AMENDMENT_NO'];
            $rplc_arr[$row['ID']]['AMENDMENT_DATE'] =  $row['AMENDMENT_DATE'];
        }

        ?>
        <br>
        <div>
            <table width="2050" cellpadding="0" cellspacing="0" id="caption" align="left">
                <tr>
                    <td align="center" width="100%" colspan="10" class="form_caption" ><strong style="font-size:18px">Company Name:<? echo $company_arr[$company_name]; ?></strong></td>
                </tr>
                <tr>
                    <td align="center" width="100%" colspan="10" class="form_caption" ><strong style="font-size:18px"><? echo $report_title; ?></strong></td>
                </tr>
            </table>
        </div>
        <br>
    
        <div style="width:1070px; float:left;">
            <table width="1000" class="rpt_table" cellpadding="0"  cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
                        <th width="100"><p>Buyer</p></th>
                        <th width="120"><p>Job / File No</p></th>
                        <th width="100"><p>SC/LC No</p></th>
                        <th width="100"><p>Replacement </BR> LC No:</p></th>
                        <th width="80"><p>SC/LC DATE</p></th>
                        <th width="100"><p>SC/LC Value</p></th>
                        <th width="50"><p>Amd No</p></th>
                        <th width="80"><p>Amd Date</p></th>
                        <th width="100"><p>Lien Bank</p></th>
                        <th width="80"><p>Last Ship Date</p></th>
                        <th width="80"><p>SC/LC <br> Expiry Date</p></th>
                    
                    </tr>
                </thead>
            </table>
            <div style="width:1020px; overflow-y: scroll; max-height:400px; overflow-x:hidden;" id="scroll_body3">
                <table cellspacing="0" width="1000"  border="1" rules="all" class="rpt_table" id="tbl_body3">
                    <tbody id="table_body" >
                        <?
                            $i=1;
                            foreach($summ_result as $row)
                            {
                                if ($i%2==0) $bgcolor="#E9F3FF";
                                else $bgcolor="#FFFFFF";

                                $value_for_btb = ($row['PO_QUANTITY']*($row['FABRIC_COST']+$row['TRIMS_COST']+$row['EMBEL_COST']+$row['WASH_COST']));
                                ?>
                                    <tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                                        <td width="100" align="center"><p><? echo  $buyer_arr[$row['BUYER_NAME']]; ?></p></td>
                                        <td width="120" align="center"><p><? echo $row['INTERNAL_FILE_NO']; ?></p></td>
                                        <td width="100" align="center"><p> <? if($row['TYPE']==1){$lcsc = "LC";} else{$lcsc = "SC";} echo $lcsc." : ". $row['SC_LC_NO']; ?></p></td>
                                        <td width="100" align="center"> <p><? echo  $rplc_arr[$row['ATTCH_ID']]['EXPORT_LC_NO']; ?> </p></td> 
                                        <td width="80" align="center"> <p><?
                                       
                                        if($rplc_arr[$row['ATTCH_ID']]['LC_DATE']!=''){
                                            echo  change_date_format($rplc_arr[$row['ATTCH_ID']]['LC_DATE']) ;
                                        }
                                        else{
                                            echo  change_date_format($row['LC_SC_DATE']);
                                        }
                                         ?> </p></td> 
                                        <td width="100" align="right"> <p><?
                                        if($rplc_arr[$row['ATTCH_ID']]['LC_SC_VAL']!=''){
                                            echo number_format($rplc_arr[$row['ATTCH_ID']]['LC_SC_VAL'],2) ;
                                            $total_lcsc_val += $rplc_arr[$row['ATTCH_ID']]['LC_SC_VAL']; 
                                        }
                                        else{
                                            echo number_format($row['LC_SC_VALUE'],2);
                                            $total_lcsc_val += $row['LC_SC_VALUE']; 
                                        }
                                        ?> </p></td> 
                                   
                                        <td width="50" align="center"> <p><?
                                        
                                        if($rplc_arr[$row['ATTCH_ID']]['AMENDMENT_NO']!=''){
                                            echo $rplc_arr[$row['ATTCH_ID']]['AMENDMENT_NO'] ; 
                                        }
                                        else{
                                            echo $row['AMENDMENT_NO'];
                                        }
                                        
                                         ?> </p></td> 
                                        <td width="80" align="center"> <p><? 
                                        if($rplc_arr[$row['ATTCH_ID']]['AMENDMENT_DATE']!=''){
                                            echo change_date_format($rplc_arr[$row['ATTCH_ID']]['AMENDMENT_DATE']) ; 
                                        }
                                        else{
                                            echo change_date_format($row['AMENDMENT_DATE']);
                                        }
                                         ?> </p></td>
                                        <td width="100" align="left"> <p><?
                                        if($rplc_arr[$row['ATTCH_ID']]['LIEN_BANK']!=''){
                                            echo  $bank_arr[$rplc_arr[$row['ATTCH_ID']]['LIEN_BANK']]; 
                                        }
                                        else{
                                         echo $bank_arr[$row['LIEN_BANK']];
                                        } 
                                         ?> </p></td>
                                        <td width="80" align="center"> <p><?
                                         if($rplc_arr[$row['ATTCH_ID']]['LAST_SHIPMENT_DATE']!=''){
                                            echo change_date_format($rplc_arr[$row['ATTCH_ID']]['LAST_SHIPMENT_DATE']); 
                                        }
                                        else{
                                            echo change_date_format($row['LAST_SHIPMENT_DATE']);
                                        }
                                        
                                        ?> </p></td>
                                        <td width="80" align="center"> <p><?
                                         if($rplc_arr[$row['ATTCH_ID']]['EXPIRY_DATE']!=''){
                                            echo change_date_format($rplc_arr[$row['ATTCH_ID']]['EXPIRY_DATE']); 
                                        }
                                        else{
                                            echo change_date_format($row['EXPIRY_DATE']);
                                        }
                                        
                                         ?> </p></td>
                                    </tr>
                                <?
                                $i++;
                            }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5"><strong>Total : </strong></th>
                            <th><p><? echo number_format($total_lcsc_val,2)?>&nbsp;</p></th>
                            <th><p></p></th>
                            <th><p></p></th>
                            <th><p></p></th>
                            <th><p></p></th>
                            <th><p></p></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <?
            $summ_sql_dtls = "SELECT a.id as LC_SC_ID, C.ID AS PO_BREAKDOWN_ID, a.BUYER_NAME ,a.INTERNAL_FILE_NO,d.STYLE_REF_NO,c.PO_QUANTITY,c.UNIT_PRICE,c.PO_TOTAL_PRICE,b.ATTACHED_QNTY,a.LC_VALUE as LC_SC_VALUE,a.MAX_BTB_LIMIT
            FROM com_export_lc a,com_export_lc_order_info b, wo_po_details_master d ,wo_po_break_down c
            WHERE  a.id=b.COM_EXPORT_LC_ID and b.wo_po_break_down_id=c.id and c.job_no_mst=d.job_no   and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1  and d.is_deleted=0 and a.status_active=1  
            --AND A.REPLACEMENT_LC <>1
            $file_year_cond $buyer_cond $company_cond $file_no_cond
            UNION ALL
            SELECT a.id as LC_SC_ID,C.ID AS PO_BREAKDOWN_ID, a.BUYER_NAME,a.INTERNAL_FILE_NO,d.STYLE_REF_NO,c.PO_QUANTITY,c.UNIT_PRICE,c.PO_TOTAL_PRICE,b.ATTACHED_QNTY,a.CONTRACT_VALUE as LC_SC_VALUE,a.MAX_BTB_LIMIT
            FROM com_sales_contract a,com_sales_contract_order_info b, wo_po_details_master d ,wo_po_break_down c
            WHERE a.id=b.COM_SALES_CONTRACT_ID and b.wo_po_break_down_id=c.id and c.job_no_mst=d.job_no  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.status_active=1 
            $file_year_cond $buyer_cond $company_cond $file_no_cond";
            //echo $summ_sql_dtls;
            $summ_dtls_result = sql_select($summ_sql_dtls);
            $all_data =array();
            foreach($summ_dtls_result as $row)
            {
                $all_data[$row['INTERNAL_FILE_NO']]['LC_SC_ID'] = $row['LC_SC_ID'];
                $all_data[$row['INTERNAL_FILE_NO']]['PO_BREAKDOWN_ID'] = $row['PO_BREAKDOWN_ID'];
                $all_data[$row['INTERNAL_FILE_NO']]['INTERNAL_FILE_NO'] = $row['INTERNAL_FILE_NO'];
                $all_data[$row['INTERNAL_FILE_NO']]['BUYER_NAME'] = $row['BUYER_NAME'];
                $all_data[$row['INTERNAL_FILE_NO']]['STYLE_REF_NO'] = $row['STYLE_REF_NO'];
                $all_data[$row['INTERNAL_FILE_NO']]['PO_TOTAL_PRICE'] += $row['PO_TOTAL_PRICE'];
                $all_data[$row['INTERNAL_FILE_NO']]['ATTACHED_QNTY'] += $row['ATTACHED_QNTY'];
                $all_data[$row['INTERNAL_FILE_NO']]['LC_SC_VALUE'] += $row['LC_SC_VALUE'];
                $all_data[$row['INTERNAL_FILE_NO']]['MAX_BTB_LIMIT'] += $row['MAX_BTB_LIMIT'];

                $style_ref .= $row['STYLE_REF_NO'].',';
                $lc_ids .= $row['LC_SC_ID'].',';
            }


            $total_style  = ltrim(count(array_unique(explode(",", chop($style_ref, ",")))), ',');
            $all_lc_ids = ltrim(implode(",", array_unique(explode(",", chop($lc_ids, ",")))), ',');

            // $btb_detail_result = sql_select("SELECT SUM(B.LC_VALUE) AS BTB_VALUE FROM COM_BTB_EXPORT_LC_ATTACHMENT A , COM_BTB_LC_MASTER_DETAILS B
            // WHERE A.IMPORT_MST_ID =  B.ID AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0  AND  B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 
            // AND A.LC_SC_ID IN ($all_lc_ids)");
            // //echo $btb_details;

            $btb_detail_result = "SELECT D.ID, D.LC_VALUE,D.LC_NUMBER
            FROM COM_PI_ITEM_DETAILS B,COM_PI_MASTER_DETAILS A
            left join COM_BTB_LC_PI C on C.PI_ID =A.ID  AND c.status_active = 1 AND c.is_deleted = 0
            left join  COM_BTB_LC_MASTER_DETAILS d on d.ID = C.COM_BTB_LC_MASTER_DETAILS_ID  and d.status_active=1 and d.is_deleted=0
            WHERE A.ID=B.PI_ID AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0
            $buyer_cond $pi_company_cond $file_no_cond $file_year_cond  group by d.ID,d.LC_VALUE,d.lc_number";
            //echo $btb_detail_result; 
            $btb_detail_result_result = sql_select($btb_detail_result);
            $pi_data_arr=array();
            foreach($btb_detail_result_result as $row)
            {
               $tot_btb_opn_val +=$row['LC_VALUE'];
            }


            $pi_sql = "SELECT A.ID,a.NET_TOTAL_AMOUNT,A.ORDER_FILE_NO
            FROM COM_PI_ITEM_DETAILS B,COM_PI_MASTER_DETAILS A
            left join COM_BTB_LC_PI C on C.PI_ID =A.ID  
            left join  COM_BTB_LC_MASTER_DETAILS d on d.ID = C.COM_BTB_LC_MASTER_DETAILS_ID  and d.status_active=1 and d.is_deleted=0
            WHERE A.ID=B.PI_ID AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0
            $buyer_cond $pi_company_cond $file_no_cond $file_year_cond ";
            // echo $pi_sql; 
            $pi_result = sql_select($pi_sql);
            $pi_data_arr=array();
            foreach($pi_result as $row)
            {
                $pi_data_arr[$row['ID']]['NET_TOTAL_AMOUNT'] = $row['NET_TOTAL_AMOUNT'];
                $pi_data_arr[$row['ID']]['ORDER_FILE_NO'] = $row['ORDER_FILE_NO'];
            }
            foreach($pi_data_arr as $row)
            {
                if($row['ORDER_FILE_NO']!='' && $row['ORDER_FILE_NO']!=$file_no)
                {
                    $cross_val_utilz += $row['NET_TOTAL_AMOUNT'];
                }
            }
            ?>
            <br>
            <div style="width:1380px; margin-left: 20px;">
                <table width="1350" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr>
                            <th width="100"><p>Buyer</p></th>
                            <th width="60"><p>No Of Style</p></th>
                            <th width="70"><p>No Of PO</p></th>
                            <th width="100"><p>Total GMT <br> Qty Pcs</p></th>
                            <th width="100"><p>Job/File Value</p></th>
                            <th width="100"><p>Total PO Value</p></th>
                            <th width="70"><p>BTB Limit %</p></th>
                            <th width="100" title="Total PO Value * .75"><p>BTB To Be Open </p></th>
                            <th width="70"><p>To Be Open %</p></th>
                            <th width="100"><p>BTB Opened <br> Value</p></th>
                            <th width="70"><p>Opened % </p></th>
                            <th width="100" title="BTB To Be Open - BTB Opened Value"><p>BTB Space</p></th>
                            <th width="70"><p>BTB Space %</p></th>
                            <th width="120" title="Job or File Value *(75-To Be Open )"><p>Surplus BTB Value</p></th>
                            <th width="100"><p>Cross Value Utilize</p></th> 
    
                        </tr>
                    </thead>
                </table>
                <div style="width:1360px; overflow-y: scroll; max-height:400px; overflow-x:hidden;" id="scroll_body1">
                    <table cellspacing="0" width="1350"  border="1" rules="all" class="rpt_table" id="tbl_body1">
                        <tbody id="table_body" >
                            <?
                                $j=1;
                                foreach($all_data as $row)
                                {
                                 
                                    if ($j%2==0) $bgcolor="#E9F3FF";
                                    else $bgcolor="#FFFFFF";
                    
                                   // $btb_open = ($row['PO_TOTAL_PRICE']*$row['MAX_BTB_LIMIT']/100);
                                    $btb_open = ($row['PO_TOTAL_PRICE']*.75);
                                    //$opened_per = ($btb_detail_result[0]['BTB_VALUE']/$btb_open)*100;
                                    $opened_per = ($tot_btb_opn_val/$btb_open)*100;
                                    $to_be_opn_percn = $row['MAX_BTB_LIMIT']/count($summ_dtls_result);
                                    $surplus_btb_val = ($total_lcsc_val* (75-$to_be_opn_percn));
                                    ?>
                                        <tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $j;?>','<? echo $bgcolor;?>')" id="tr_<? echo $j;?>">
                                            <td width="100" align="center"><p><? echo $buyer_arr[$row['BUYER_NAME']]; ?></p></td>
                                            <td width="60" align="center"><p><? echo $total_style; ?></p></td>
                                            <td width="70" align="center"><p><? echo count($summ_dtls_result) ?></p></td>
                                            <td width="100" align="center"><p> <? echo $row['ATTACHED_QNTY']; ?></p></td>
                                            <td width="100" align="right"><p> <? echo  number_format($total_lcsc_val,2); ?></p></td>
                                            <td width="100" align="right"><p> <? echo number_format($row['PO_TOTAL_PRICE'],2); ?></p></td>
                                            <td width="70" align="right"> <p><? //echo  $row['MAX_BTB_LIMIT'].' %'; 
                                            echo'75 %'; 
                                            ?> </p></td> 
                                            <td width="100" align="right"><p> <? echo number_format($btb_open,2); ?></p></td>            
                                            <td width="70" align="right"> <p><? echo number_format($to_be_opn_percn,2).' %';  ?> </p></td> 
                                            <td width="100" align="right"><p> <?
                                            // echo number_format($btb_detail_result[0]['BTB_VALUE'],2); 
                                             echo number_format($tot_btb_opn_val,2); 
                                             
                                             ?></p></td>            
                                            <td width="70" align="center"> <p><? echo number_format($opened_per,2).' %';  ?> </p></td> 
                                            <td width="100" align="right"> <p><?
                                             //echo number_format(($btb_open-$btb_detail_result[0]['BTB_VALUE']),2);  
                                             echo number_format(($btb_open-$tot_btb_opn_val),2);  
                                             
                                             ?> </p></td> 
                                            <td width="70" align="center"> <p><? echo number_format((75-$opened_per),2).' %';  ?> </p></td> 
                                            <td width="120" align="right"><? echo number_format($surplus_btb_val,2)?></td>
                                            <td width="100" align="right"><? echo number_format($cross_val_utilz,2)?></td>
                                        
                                        </tr>
                                    <?
                                    $j++;
                                }
                            ?>
                        </tbody>
                        <!-- <tfoot>
                            <tr>
                                <th colspan="5"><strong>Total : </strong></th>
                                <th><p><? echo number_format($total_val_pi,2)?>&nbsp;</p></th>
                                <th><p><? echo number_format($total_value_for_btb,2)?>&nbsp;</p></th>
                                <th><p><? echo round($total_mst_perc)." %";?></p></th>
                                <th><p></p></th>
                                <th><p></p></th>
                                <th><p></p></th>
                                <th><p></p></th>
                            </tr>
                        </tfoot> -->
                    </table>
                </div>
            </div>
        </div>
            <?

    }


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
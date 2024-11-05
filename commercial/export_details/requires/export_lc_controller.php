<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
    header("location:login.php");

include('../../../includes/common.php');

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
//$cat_wise_entry_form=array(23=>'278', 36=>'311', 35=>'204', 37=>'295', 45=>'255');
//--------------------------- Start-------------------------------------//
$buyer_details = return_library_array("select id,buyer_name from lib_buyer where status_active=1", "id", "buyer_name");

if($action=="print_button_variable_setting")
{
    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=5 and report_id=264 and is_deleted=0 and status_active=1");
    echo "print_report_button_setting('".$print_report_format."');\n";
    exit();
}

if ($action == "file_search") {
    echo load_html_head_contents("Export LC Form", "../../../", 1, 1, '', '1', '');
    extract($_REQUEST);
    //echo $companyID;die;
    $company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
    $sql = "select b.file_no, a.company_name from wo_po_break_down b, wo_po_details_master a where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and a.company_name=$companyID and b.file_no is not null group by a.company_name, b.file_no";
    ?>

    <script>

        function js_set_value(str)
        {
            $('#hidden_file_id').val(str);
            parent.emailwindow.hide();
        }

    </script>

    </head>

    <body>
        <div align="center" style="width:520px;">
            <form name="searchexportlcfrm" id="searchexportlcfrm">
                <fieldset style="width:520px; margin-left:3px">
                    <input type="hidden" id="hidden_file_id" >
                    <table cellpadding="0" cellspacing="0" width="500" class="rpt_table"  border="1" rules="all">
                        <thead>
                        <th width="50">Sl</th>
                        <th width="200">Company</th>
                        <th>File No</th>
                        </thead>
                    </table>
                    <div style="width:520px; max-height:300px; overflow:auto;" >
                        <table cellpadding="0" cellspacing="0" width="500" class="rpt_table" id="table_body" border="1" rules="all">
                            <tbody>
                                <?
                                $sql_result = sql_select($sql);
                                $i = 1;
                                foreach ($sql_result as $row) {
                                    if ($i % 2 == 0)
                                        $bgcolor = "#E9F3FF";
                                    else
                                        $bgcolor = "#FFFFFF";
                                    ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf("file_no")]; ?>');" style="cursor:pointer;">
                                        <td width="50" align="center"><? echo $i; ?></td>
                                        <td width="200"><p><? echo $company_arr[$row[csf("company_name")]]; ?>&nbsp;</p></td>
                                        <td><p><? echo $row[csf("file_no")]; ?>&nbsp;</p></td>
                                    </tr>
                                    <?
                                    $i++;
                                }
                                ?>
                            </tbody>
                        </table>
                        <script>setFilterGrid('table_body', -1);</script>
                    </div>   
                </fieldset>
            </form>
        </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if ($action == "file_search_library") {
    echo load_html_head_contents("Export LC Form", "../../../", 1, 1, '', '1', '');
    extract($_REQUEST);
    //echo $companyID;die;
    $company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
    $buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
    $sql = "select file_no, company_id, buyer_id from lib_file_creation where status_active=1 and is_deleted = 0 and company_id=$companyID order by id desc";
    ?>

    <script>

        function js_set_value(str)
        {
            $('#hidden_file_id').val(str);
            parent.emailwindow.hide();
        }

    </script>

    </head>

    <body>
    <div align="center" style="width:520px;">
        <form name="searchexportlcfrm" id="searchexportlcfrm">
            <fieldset style="width:520px; margin-left:3px">
                <input type="hidden" id="hidden_file_id" >
                <table cellpadding="0" cellspacing="0" width="500" class="rpt_table"  border="1" rules="all">
                    <thead>
                    <th width="50">Sl</th>
                    <th width="150">Company</th>
                    <th width="120">Buyer</th>
                    <th>File No</th>
                    </thead>
                </table>
                <div style="width:520px; max-height:300px; overflow:auto;" >
                    <table cellpadding="0" cellspacing="0" width="500" class="rpt_table" id="table_body" border="1" rules="all">
                        <tbody>
                        <?
                        $sql_result = sql_select($sql);
                        $i = 1;
                        foreach ($sql_result as $row) {
                            if ($i % 2 == 0)
                                $bgcolor = "#E9F3FF";
                            else
                                $bgcolor = "#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf("file_no")]; ?>');" style="cursor:pointer;">
                                <td width="50" align="center"><? echo $i; ?></td>
                                <td width="150"><p><? echo $company_arr[$row[csf("company_id")]]; ?>&nbsp;</p></td>
                                <td width="120"><p><? echo $buyer_arr[$row[csf("buyer_id")]]; ?>&nbsp;</p></td>
                                <td><p><? echo $row[csf("file_no")]; ?>&nbsp;</p></td>
                            </tr>
                            <?
                            $i++;
                        }
                        ?>
                        </tbody>
                    </table>
                    <script>setFilterGrid('table_body', -1);</script>
                </div>
            </fieldset>
        </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if ($action == "load_drop_down_buyer") {
    $data = explode("**", $data);
    $company_id = $data[0];
    $import_btb = $data[1];
    $lc_type = $data[2];

    if ($import_btb == 1) {
        echo create_drop_down("cbo_buyer_name", 162, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/export_lc_controller', this.value, 'load_drop_down_issue_bank', 'issue_bank_td' );");
    } 
    else if($lc_type==2)
    {
        if($company_id != 0){
            echo create_drop_down("cbo_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (2,3)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/export_lc_controller', this.value, 'load_drop_down_issue_bank', 'issue_bank_td' );");
        }
        else {
            echo create_drop_down("cbo_buyer_name", 162, $blank_array, "", 1, "-- Select Buyer --", 0, "");
        }
        exit();
    }
    else {
        if($company_id != 0){
            echo create_drop_down("cbo_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/export_lc_controller', this.value, 'load_drop_down_issue_bank', 'issue_bank_td' );");
        }
        
        else {
            echo create_drop_down( "cbo_buyer_name", 162, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/export_lc_controller', this.value, 'load_drop_down_issue_bank', 'issue_bank_td' );" ); 
        exit();
        }
    }
    exit();
}

if ($action=="load_drop_down_issue_bank")
{
	$sql = "select a.bank_name as bank_name, a.id from lib_bank a, LIB_BUYER_TAG_BANK b where a.id=b.TAG_BANK and b.BUYER_ID='$data' and a.is_deleted=0 and a.status_active=1 and a.ISSUSING_BANK=1 order by bank_name";
	//echo $sql;
 	echo create_drop_down( "txt_issuing_bank", 162, $sql,"id,bank_name", 1, "---- Select ----", '', '' );
	exit();
}

if ($action == "load_drop_down_applicant_name") {
    $sql = "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (22,23)) group by a.id,a.buyer_name order by buyer_name";
    echo create_drop_down("cbo_applicant_name", 162, $sql, "id,buyer_name", 1, "---- Select ----", 0, "");
    exit();
}

if ($action == "load_drop_down_notifying_party") {
    $sql = "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (4,6)) group by a.id,a.buyer_name order by buyer_name";
    echo create_drop_down("cbo_notifying_party", 162, $sql, "id,buyer_name", 0, "", '', '');
    exit();
}

if ($action == "load_drop_down_consignee") {
    $sql = "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (5,6,100)) group by a.id,a.buyer_name order by buyer_name";
    echo create_drop_down("cbo_consignee", 162, $sql, "id,buyer_name", 0, "", '', '');
    exit();
}

if ($action == "eval_multi_select") {
    echo "set_multiselect('cbo_notifying_party*cbo_consignee','0*0','0','','0*0');\n";
    exit();
}

if ($action == "get_btb_limit") 
{
    //$nameArray = sql_select("SELECT max_btb_limit FROM variable_settings_commercial where company_name like '$data' and variable_list=6 and is_deleted = 0 AND status_active = 1");
	$nameArray=sql_select("select COST_HEADS_STATUS, PI_SOURCE_BTB_LC, MAX_BTB_LIMIT from  variable_settings_commercial where company_name=$data and variable_list=6");
	foreach ($nameArray as $row) 
	{
		if($row["PI_SOURCE_BTB_LC"]==1)
		{
			 //echo "document.getElementById('txt_max_btb_limit').value = '" . trim($row[csf("issuing_bank_name")] ). "';\n";
			 echo "$('#txt_max_btb_limit').val('" . trim($row["MAX_BTB_LIMIT"] ). "');\n";
			 echo "$('#txt_max_btb_limit').attr('disabled',true);\n";
		}
		else
		{
			 echo "$('#txt_max_btb_limit').val('');\n";
			 echo "$('#txt_max_btb_limit').attr('disabled',false);\n";
		}
		
	}
	exit();
}

if ($action == "file_write_mathod") {
    $nameArray = sql_select("SELECT internal_file_source FROM variable_settings_commercial where company_name like '$data' and variable_list=20 and is_deleted = 0 AND status_active = 1");
    echo "$('#txt_internal_file_no').val('');\n";
    if ($nameArray[0][csf("internal_file_source")] == 1) {
        echo "$('#txt_internal_file_no').attr('onDblClick','fn_file_no()');\n";
        echo "$('#txt_internal_file_no').attr('readonly',true);\n";
        echo "$('#txt_internal_file_no').attr('placeholder','Double Click');\n";
    } elseif ($nameArray[0][csf("internal_file_source")] == 3) {
            echo "$('#txt_internal_file_no').attr('onDblClick','fn_file_no_library()');\n";
            echo "$('#txt_internal_file_no').attr('readonly',true);\n";
            echo "$('#txt_internal_file_no').attr('placeholder','Double Click');\n";
    } else {
        echo "$('#txt_internal_file_no').removeAttr('onDblClick');\n";
        echo "$('#txt_internal_file_no').attr('readonly',false);\n";
        echo "$('#txt_internal_file_no').removeAttr('placeholder');\n";
    }
	$variable_rate_edit = sql_select("SELECT cost_heads_status FROM variable_settings_commercial where company_name like '$data' and variable_list=33 and is_deleted = 0 AND status_active = 1");
	 if ($variable_rate_edit[0][csf("cost_heads_status")] == 1) {
		 echo "$('#hiddenunitprice_1').attr('readonly',false).attr('disabled',false);\n";
		 
	 }
	 else{
		  echo "$('#hiddenunitprice_1').attr('readonly',true).attr('disabled',true);\n";
	 }
	exit();
}

if ($action == 'populate_data_from_export_lc') 
{
    $data_array = sql_select("SELECT id,export_lc_system_id,export_lc_no,lc_date,beneficiary_name,buyer_name,applicant_name,notifying_party,consignee,issuing_bank_name,replacement_lc,lien_bank,lien_date,lc_value,currency_name,tolerance,last_shipment_date,expiry_date,shipping_mode,pay_term,inco_term,inco_term_place,lc_source,port_of_entry,port_of_loading,port_of_discharge,internal_file_no,doc_presentation_days,max_btb_limit,foreign_comn,foreign_comn_value,local_comn,local_comn_value,remarks,tenor,transfering_bank_ref,bl_clause,reimbursement_clauses,discount_clauses,is_lc_transfarrable,transfer_bank,negotiating_bank,nominated_shipp_line,re_imbursing_bank,claim_adjustment,expiry_place,bank_file_no,lc_year,reason,export_item_category,import_btb,import_btb_id, initial_lc_value, export_lc_type, estimated_qnty from com_export_lc where id='$data'");
    foreach ($data_array as $row) {
        if ($db_type == 0) {
            $attached_po_id = return_field_value("group_concat(wo_po_break_down_id)", "com_export_lc_order_info", "com_export_lc_id=$data and status_active=1 and is_deleted=0");
        } else {
            //Null value ar jonnno xmlagg errror day. tai xmlagg off kora holo.
			$attached_po_id = return_field_value("LISTAGG(wo_po_break_down_id, ',') WITHIN GROUP (ORDER BY wo_po_break_down_id) as po_id", "com_export_lc_order_info", "com_export_lc_id=$data and status_active=1 and is_deleted=0", "po_id");
            /*$attached_po_id=return_field_value(" rtrim(xmlagg(xmlelement(e,wo_po_break_down_id,',').extract('//text()') order by wo_po_break_down_id).GetClobVal(),',') as po_id","com_export_lc_order_info","com_export_lc_id=$data and status_active=1 and is_deleted=0","po_id");    
            $attached_po_id = $attached_po_id->load();*/
        }
        $lc_amnd = return_field_value("count(id)", "com_export_lc_amendment", "export_lc_id=$data and is_original=0 and status_active=1 and is_deleted=0");

        echo "document.getElementById('txt_system_id').value 				= '" . $row[csf("id")] . "';\n";
        echo "document.getElementById('export_lc_system_id').value 			= '" . $row[csf("export_lc_system_id")] . "';\n";
        echo "document.getElementById('cbo_beneficiary_name').value 		= '" . $row[csf("beneficiary_name")] . "';\n";
        echo "document.getElementById('import_btb').value 					= '" . $row[csf("import_btb")] . "';\n";
        echo "document.getElementById('import_btb_id').value 				= '" . $row[csf("import_btb_id")] . "';\n";
        echo "document.getElementById('txt_internal_file_no').value			= '" . $row[csf("internal_file_no")] . "';\n";
        echo "document.getElementById('txt_bank_file_no').value 			= '" . $row[csf("bank_file_no")] . "';\n";
        echo "document.getElementById('txt_year').value 					= '" . $row[csf("lc_year")] . "';\n";
        echo "document.getElementById('txt_lc_number').value 				= '" . $row[csf("export_lc_no")] . "';\n";
        echo "document.getElementById('txt_lc_value').value 				= '" . $row[csf("lc_value")] . "';\n";
		echo "document.getElementById('txt_lc_ini_value').value 			= '" . $row[csf("initial_lc_value")] . "';\n";
        echo "document.getElementById('txt_lc_date').value 					= '" . change_date_format($row[csf("lc_date")]) . "';\n";
        echo "document.getElementById('cbo_currency_name').value 			= '" . $row[csf("currency_name")] . "';\n";
		echo "document.getElementById('cbo_lc_type').value 			= '" . $row[csf("export_lc_type")] . "';\n";

        echo "load_drop_down( 'requires/export_lc_controller', document.getElementById('cbo_beneficiary_name').value+'**'+'" . $row[csf("import_btb")] . "'+'**'+'" . $row[csf("export_lc_type")] . "', 'load_drop_down_buyer', 'buyer_td_id' );\n";
        echo "load_drop_down( 'requires/export_lc_controller', document.getElementById('cbo_beneficiary_name').value, 'load_drop_down_applicant_name', 'applicant_name_td' );\n";
        echo "load_drop_down( 'requires/export_lc_controller', document.getElementById('cbo_beneficiary_name').value, 'load_drop_down_notifying_party', 'notifying_party_td' );\n";
        echo "load_drop_down( 'requires/export_lc_controller', document.getElementById('cbo_beneficiary_name').value, 'load_drop_down_consignee', 'consignee_td' );\n";
        echo "get_php_form_data( document.getElementById('cbo_beneficiary_name').value, 'eval_multi_select', 'requires/export_lc_controller' );\n";

        echo "document.getElementById('cbo_buyer_name').value				= '" . $row[csf("buyer_name")] . "';\n";
		echo "load_drop_down( 'requires/export_lc_controller', document.getElementById('cbo_buyer_name').value, 'load_drop_down_issue_bank', 'issue_bank_td' );\n";
        echo "document.getElementById('cbo_applicant_name').value 			= '" . $row[csf("applicant_name")] . "';\n";
        echo "document.getElementById('cbo_notifying_party').value			= '" . $row[csf("notifying_party")] . "';\n";
        echo "document.getElementById('cbo_consignee').value 				= '" . $row[csf("consignee")] . "';\n";
        echo "document.getElementById('txt_issuing_bank').value 			= '" . $row[csf("issuing_bank_name")] . "';\n";
        echo "document.getElementById('cbo_lien_bank').value 				= '" . $row[csf("lien_bank")] . "';\n";
        echo "document.getElementById('txt_lien_date').value 				= '" . change_date_format($row[csf("lien_date")]) . "';\n";
        echo "document.getElementById('txt_last_shipment_date').value 		= '" . change_date_format($row[csf("last_shipment_date")]) . "';\n";
        echo "document.getElementById('txt_expiry_date').value 				= '" . change_date_format($row[csf("expiry_date")]) . "';\n";
        echo "document.getElementById('txt_tolerance').value 				= '" . $row[csf("tolerance")] . "';\n";
        echo "document.getElementById('cbo_shipping_mode').value 			= '" . $row[csf("shipping_mode")] . "';\n";
        echo "document.getElementById('cbo_pay_term').value 				= '" . $row[csf("pay_term")] . "';\n";
        echo "document.getElementById('txt_tenor').value 					= '" . $row[csf("tenor")] . "';\n";
        echo "document.getElementById('cbo_inco_term').value 				= '" . $row[csf("inco_term")] . "';\n";
        echo "document.getElementById('txt_inco_term_place').value 			= '" . $row[csf("inco_term_place")] . "';\n";
        echo "document.getElementById('cbo_lc_source').value 				= '" . $row[csf("lc_source")] . "';\n";
        echo "document.getElementById('txt_port_of_entry').value 			= '" . $row[csf("port_of_entry")] . "';\n";
        echo "document.getElementById('txt_port_of_loading').value 			= '" . $row[csf("port_of_loading")] . "';\n";
        echo "document.getElementById('txt_port_of_discharge').value 		= '" . $row[csf("port_of_discharge")] . "';\n";
        echo "document.getElementById('txt_doc_presentation_days').value 	= '" . $row[csf("doc_presentation_days")] . "';\n";
        echo "document.getElementById('txt_max_btb_limit').value 			= '" . $row[csf("max_btb_limit")] . "';\n";
        echo "document.getElementById('txt_foreign_comn').value 			= '" . $row[csf("foreign_comn")] . "';\n";
        echo "document.getElementById('txt_local_comn').value 				= '" . $row[csf("local_comn")] . "';\n";
        echo "document.getElementById('txt_transfering_bank_ref').value 	= '" . $row[csf("transfering_bank_ref")] . "';\n";
        echo "document.getElementById('cbo_is_lc_transfarrable').value 		= '" . $row[csf("is_lc_transfarrable")] . "';\n";
        echo "document.getElementById('cbo_replacement_lc').value 			= '" . $row[csf("replacement_lc")] . "';\n";
        echo "document.getElementById('txt_transfer_bank').value 			= '" . $row[csf("transfer_bank")] . "';\n";
        echo "document.getElementById('txt_negotiating_bank').value 		= '" . $row[csf("negotiating_bank")] . "';\n";
        echo "document.getElementById('txt_nominated_shipp_line').value 	= '" . $row[csf("nominated_shipp_line")] . "';\n";
        echo "document.getElementById('txt_re_imbursing_bank').value 		= '" . $row[csf("re_imbursing_bank")] . "';\n";
        echo "document.getElementById('txt_claim_adjustment').value 		= '" . $row[csf("claim_adjustment")] . "';\n";
        echo "document.getElementById('txt_expiry_place').value 			= '" . $row[csf("expiry_place")] . "';\n";
        echo "document.getElementById('txt_bl_clause').value 				= '" . $row[csf("bl_clause")] . "';\n";
        echo "document.getElementById('txt_reimbursement_clauses').value 	= '" . $row[csf("reimbursement_clauses")] . "';\n";
        echo "document.getElementById('txt_discount_clauses').value 		= '" . $row[csf("discount_clauses")] . "';\n";
        echo "document.getElementById('txt_remarks').value 					= '" . $row[csf("remarks")] . "';\n";
        echo "document.getElementById('txt_reason').value 					= '" . $row[csf("reason")] . "';\n";
        echo "document.getElementById('txt_estimated_lc_qnty').value 		= '" . $row[csf("estimated_qnty")] . "';\n";
        /*if($row[csf("import_btb")] == 1)
        {
            $itemCategoryId = $row[csf("export_item_category")];// - 100;
            echo '$("#cbo_export_item_category option[value!=\'0\']").remove();'."\n";
            echo '$("#cbo_export_item_category").append("<option selected value=\''.$itemCategoryId.'\'>'.$item_category[$itemCategoryId].'</option>");'."\n";
        }
        else{
            echo "document.getElementById('cbo_export_item_category').value     = '" . $row[csf("export_item_category")] . "';\n";
        }*/
        echo "document.getElementById('cbo_export_item_category').value     = '" . $row[csf("export_item_category")] . "';\n";
		echo "$('#cbo_beneficiary_name').attr('disabled','true')" . ";\n";
		echo "$('#cbo_export_item_category').attr('disabled','true')" . ";\n";
		echo "$('#cbo_buyer_name').attr('disabled','true')" . ";\n";
		//echo "$('#txt_lc_number').attr('disabled','true')" . ";\n";
        echo "document.getElementById('hidden_selectedID').value 			= '" . $attached_po_id . "';\n";

        echo "replacement_lc_diplay('" . $row[csf("replacement_lc")] . "');\n";
		$replaced_sc_id="";
        if ($row[csf("replacement_lc")] == 1) {
            if ($db_type == 0) {
                $replaced_sc_id = return_field_value("group_concat(com_sales_contract_id)", "com_export_lc_atch_sc_info", "com_export_lc_id=$data and status_active=1 and is_deleted=0 group by com_export_lc_id");
            } else {
                $replaced_sc_id = return_field_value("LISTAGG(com_sales_contract_id, ',') WITHIN GROUP (ORDER BY com_sales_contract_id) as sc_id", "com_export_lc_atch_sc_info", "com_export_lc_id=$data and status_active=1 and is_deleted=0 group by com_export_lc_id", "sc_id");
                /*$replaced_sc_id=return_field_value(" rtrim(xmlagg(xmlelement(e,com_sales_contract_id,',').extract('//text()') order by com_sales_contract_id).GetClobVal(),',') as sc_id","com_export_lc_atch_sc_info","com_export_lc_id=$data and status_active=1 and is_deleted=0 group by com_export_lc_id","sc_id");    
                $replaced_sc_id = $replaced_sc_id->load();*/
    
            }
            echo "document.getElementById('hidden_sc_selectedID').value 	= '" . $replaced_sc_id . "';\n";
        }
		
		if($replaced_sc_id !="")
		{
			echo "$('#is_contact_browse').val(0);\n";
			echo "$('#is_contact_browse').attr('title','1');\n";
		}

        echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_export_lc_entry',1);\n";

        echo "set_multiselect('cbo_notifying_party*cbo_consignee','0*0','1','" . $row[csf('notifying_party')] . "*" . $row[csf('consignee')] . "','0*0');\n";


        if ($lc_amnd > 0) 
		{
            echo "disable_enable_fields('txt_lc_value*txt_last_shipment_date*txt_expiry_date*cbo_shipping_mode*cbo_inco_term*txt_inco_term_place*txt_port_of_entry*txt_port_of_loading*txt_port_of_discharge*cbo_pay_term*txt_tenor*txt_claim_adjustment*txt_discount_clauses*txt_remarks',1);\n";

            if ($row[csf("import_btb")] == 1) {
                echo "disable_enable_fields('cbo_beneficiary_name*txt_lc_number*cbo_buyer_name*cbo_currency_name*txt_issuing_bank*cbo_export_item_category*txt_tolerance*txt_doc_presentation_days*cbo_pay_term',1);\n";
            } else {
                echo "disable_enable_fields('cbo_beneficiary_name*txt_lc_number*cbo_buyer_name*cbo_currency_name*txt_issuing_bank*cbo_export_item_category*txt_tolerance*txt_doc_presentation_days*cbo_pay_term',0);\n";
            }
        } 
		else 
		{
            if ($row[csf("import_btb")] == 1) 
			{
                echo "disable_enable_fields('cbo_beneficiary_name*txt_lc_number*cbo_buyer_name*txt_lc_date*txt_expiry_date*txt_lc_value*cbo_currency_name*txt_issuing_bank*cbo_export_item_category*txt_tenor*txt_tolerance*cbo_inco_term*txt_inco_term_place*txt_doc_presentation_days*cbo_pay_term',1);\n";

                echo "disable_enable_fields('cbo_shipping_mode*txt_port_of_entry*txt_port_of_loading*txt_port_of_discharge*txt_claim_adjustment*txt_discount_clauses*txt_remarks',0);\n";
            } 
			else 
			{
                echo "disable_enable_fields('txt_last_shipment_date*txt_expiry_date*cbo_shipping_mode*cbo_inco_term*txt_inco_term_place*txt_port_of_entry*txt_port_of_loading*txt_port_of_discharge*cbo_pay_term*txt_tenor*txt_claim_adjustment*txt_discount_clauses*txt_remarks*cbo_currency_name*txt_issuing_bank*txt_tolerance*txt_doc_presentation_days',0);\n";
            }
        }

        exit();
    }
}

if ($action == "export_lc_popup_search") {
    echo load_html_head_contents("Export LC Form", "../../../", 1, 1, '', '1', '');
    extract($_REQUEST);
    //echo $cbo_company_id;die;
    ?>

    <script>
        function js_set_value(id)
        {
            $('#hidden_export_lc_id').val(id);
            parent.emailwindow.hide();
        }
        
    </script>

    </head>

    <body>
        <div align="center" style="width:1080px;">
            <form name="searchexportlcfrm" id="searchexportlcfrm">
                <fieldset style="width:1088px; margin-left:3px">
                    <legend>Enter search words</legend>           
                    <table cellpadding="0" cellspacing="0" width="100%" class="rpt_table" border="1" rules="all">
                        <thead>
                        <th>Company</th>
                        <th>Buyer</th>
                        <th>File Year</th>
                        <th>Search By</th>
                        <th>Enter</th>
                        <th>Date Range</th>
                        <th><input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" /><input type="hidden" name="id_field" id="id_field" value="" /></th>
                        </thead>
                        <tr class="general">
                            <td>
                                <?
                                echo create_drop_down("cbo_company_name", 162, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--- Select ---", $cbo_company_id, "load_drop_down( 'export_lc_controller', this.value+'****'+$cbo_lc_type, 'load_drop_down_buyer', 'buyer_td_id' );");
                                ?>                        
                            </td>
                            <td id="buyer_td_id">
                                <?
                                // echo create_drop_down("cbo_buyer_name", 162, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name", "id,buyer_name", 1, "--- Select Buyer ---", $selected, "");
                                ?>
                                <?php
                                echo create_drop_down("cbo_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name", "id,buyer_name", 1, "--- Select Buyer ---", $selected, "");
                                ?>
                            </td>                  
                            <td> 
                                <?
                                    $file_year_arr=return_library_array( "select lc_year from com_export_lc where beneficiary_name=$cbo_company_id and is_deleted=0 order by lc_year",'lc_year','lc_year');
                                    echo create_drop_down("cbo_file_year", 80, $file_year_arr, "", 1, "--All--", 0, "");
                                ?> 
                            </td>						
                            <td> 
                                <?
                                $arr = array(1 => 'LC NO', 2 => 'File No');
                                echo create_drop_down("cbo_search_by", 162, $arr, "", 0, "", 1, "");
                                ?> 
                                <input type="hidden" id="hidden_export_lc_id" />
                            </td>						
                            <td id="search_by_td">
                                <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                            </td>     
                            <td align="center">
                                <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">To
                                <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                            </td>                  
                            <td>
                                <input type="button" id="search_button" class="formbutton" value="Show" onClick="show_list_view(document.getElementById('cbo_company_name').value + '**' + document.getElementById('cbo_buyer_name').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('cbo_file_year').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value, 'export_lc_search_list_view', 'search_div', 'export_lc_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="7" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
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

if ($action == "export_lc_search_list_view") {
    $data = explode('**', $data);
    if ($data[0] != 0) {
        $company_id = " and beneficiary_name = $data[0]";
    } else {
        $company_id = "";
    }
    //if($data[1]!=0){ $buyer_id=" and buyer_name = $data[1]";}else{ $buyer_id=""; }
    $search_by = $data[2];
    $search_text = trim($data[3]);

    if ($data[1] == 0) {
        if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
            if ($_SESSION['logic_erp']["buyer_id"] != "")
                $buyer_id = " and buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
            else
                $buyer_id = "";
        }
        else {
            $buyer_id = "";
        }
    } else {
        $buyer_id = " and buyer_name = $data[1]";
    }

    if ($search_by == 0) {
        $search_condition = "";
    } else if ($search_by == 1) {
        $search_condition = "and export_lc_no like '%$search_text%'";
    } else if ($search_by == 2) {
        $search_condition = "and internal_file_no like '%$search_text%'";
    }

    if ($db_type == 0)
        $select_field = ", YEAR(insert_date) as year";
    else if ($db_type == 2)
        $select_field = ", to_char(insert_date,'YYYY') as year";
    else
        $select_field = ""; //defined Later

    if($data[4]){$file_year_field=" and lc_year='".$data[4]."' ";}

    if ($data[5] !='' &&  $data[6] !='')
	{
		if($db_type==0)
		{
			$date_cond = "and lc_date between '".change_date_format($data[5], "yyyy-mm-dd", "-")."' and '".change_date_format($data[6], "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond = "and lc_date between '".change_date_format($data[5],'','',1)."' and '".change_date_format($data[6],'','',1)."'";
		}
	}

    $buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
    $comp = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
    $bank_arr = return_library_array("select id, bank_name from lib_bank", 'id', 'bank_name');
   

    $sql = "select id,export_lc_no, internal_file_no $select_field, export_lc_prefix_number, export_lc_system_id, beneficiary_name, buyer_name, applicant_name, lc_value, lien_bank, pay_term, last_shipment_date, lc_date,import_btb, lc_year as LC_YEAR from com_export_lc where status_active=1 and is_deleted=0 $company_id $buyer_id $search_condition $file_year_field $date_cond order by id desc";
    // echo $sql;
    /*foreach (sql_select($sql) as $value) 
    {
        if($value[csf("import_btb")] == 1)
        {
            $import_btb_buyer[$value[csf("id")]] = $comp[$value[csf("buyer_name")]];
        }  
        else
        {
            $import_btb_buyer[$value[csf("id")]] = $buyer_arr[$value[csf("buyer_name")]];
        }  
    }
    $arr = array(4 => $comp, 5 => $import_btb_buyer, 6 => $buyer_arr, 8 => $bank_arr, 9 => $pay_term);

    echo create_list_view("list_view", "LC No,File No,Year,System ID,Company,Buyer Name,Applicant Name,LC Value,Lien Bank,Pay Term,Last Ship Date,LC Date", "80,80,60,70,70,70,70,100,110,70,80,70", "1020", "320", 0, $sql, "js_set_value", "id", "", 1, "0,0,0,0,beneficiary_name,buyer_name,applicant_name,0,lien_bank,pay_term,0,0", $arr, "export_lc_no,internal_file_no,year,export_lc_prefix_number,beneficiary_name,buyer_name,applicant_name,lc_value,lien_bank,pay_term,last_shipment_date,lc_date", "", '', '0,0,0,0,0,0,0,2,0,0,3,3');
    */
     ?>
     <table width="1080" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
        <thead> 
        <th width="30">SL</th>
        <th width="90">LC No</th>
        <th width="80">File No</th>
        <th width="60">File Year</th>
        <th width="60">Insert Year</th>
        <th width="70">System Id</th>
        <th width="70">Company</th>
        <th width="70">Buyer Name</th>
        <th width="70">Applicant Name</th>
        <th width="100">L C Value</th>
        <th width="110">Lien Bank</th>
        <th width="70">Pay Term</th>
        <th width="80">Last Ship Date</th>
        <th width="">LC Date</th>
    </thead>
    </table>
    <div style="width:1080px; overflow-y:scroll; max-height:320px">  
        <table width="1060" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="list_view"> 
            <?
            $data_array = sql_select($sql);
            $i = 1;
            foreach ($data_array as $row) 
            {
                if ($i % 2 == 0)
                    $bgcolor = "#E9F3FF";
                else
                    $bgcolor = "#FFFFFF";
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer;" id="search<? echo $i; ?>" onClick="js_set_value('<? echo $row[csf('id')]; ?>');">
                    <td width="30"><? echo $i; ?></td>
                    <td width="90"><p><? echo $row[csf('export_lc_no')]; ?></p></td>
                    <td width="80"><p><? echo $row[csf('internal_file_no')]; ?></p></td>
                    <td width="60"><p><? echo $row['LC_YEAR']; ?></p></td>
                    <td width="60"><? echo $row[csf('year')]; ?></td>
                    <td width="70"><? echo $row[csf('export_lc_prefix_number')]; ?></td>
                    <td width="70"><? echo $comp[$row[csf('beneficiary_name')]]; ?></td>
                    <td width="70">
                        <? 
                            if($row[csf('import_btb')] == 1)
                            {
                                $import_btb_buyer_comp =  $comp[$row[csf('buyer_name')]];
                            }else
                            {
                                $import_btb_buyer_comp = $buyer_arr[$row[csf('buyer_name')]];
                            }
                            echo  $import_btb_buyer_comp;
                        ?>
                    </td>
                    <td width="70"><? echo $buyer_arr[$row[csf('applicant_name')]]; ?></td>
                    <td width="100"><? echo number_format($row[csf('lc_value')],2); ?></td>
                    <td width="110"><p><? echo $bank_arr[$row[csf('lien_bank')]]; ?></p></td>
                    <td width="70"><? echo $pay_term[$row[csf('pay_term')]]; ?></td>
                    <td width="80"><? echo change_date_format($row[csf('last_shipment_date')]); ?></td>
                    <td width=""><? echo change_date_format($row[csf('lc_date')]); ?></td>
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

if ($action == "order_popup") {
    echo load_html_head_contents("Export LC Form", "../../../", 1, 1, '', '1', '');
    extract($_REQUEST);
	//echo $cbo_export_item_category.test;die;
    ?>

    <script>
		var cbo_export_item_category='<?= $cbo_export_item_category;?>';
        var selected_id = new Array, selected_name = new Array();individual_ids_arr = new Array();
        function check_all_data() {
            var tbl_row_count = document.getElementById('tbl_list_search').rows.length;

            tbl_row_count = tbl_row_count - 1;
            for (var i = 1; i <= tbl_row_count; i++) {
                js_set_value(i);
            }
        }

        function toggle(x, origColor) {
            var newColor = 'yellow';
            if (x.style) {
                x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
            }
        }

        function js_set_value(str) {
            if($("#search"+str).css("display") !='none')
            {
                toggle(document.getElementById('search' + str), '#FFFFCC');

                if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1) {
                    selected_id.push($('#txt_individual_id' + str).val());
                    individual_ids_arr.push( $('#txt_individual_ids' + str).val() );

                } else {
                    for (var i = 0; i < selected_id.length; i++) {
                        if (selected_id[i] == $('#txt_individual_id' + str).val())
                            break;
                    }
                    
                    if(cbo_export_item_category==45)
                    {
                        if( jQuery.inArray( $('#txt_individual_ids' + str).val(), individual_ids_arr )!== -1)
                        {
                            selected_id.splice(i, 1);
                        }
                    }
                    else
                    {
                        selected_id.splice(i, 1);
                    }
                    
                }
                var id = '';
                for (var i = 0; i < selected_id.length; i++) {
                    id += selected_id[i] + ',';
                }
                
                id = id.substr(0, id.length - 1);
                
                $('#txt_selected_id').val(id);//txt_individual_ids
            }
        }
        
        function fn_order_list()
        {
            if($("#chk_order").prop('checked')) var order_check=1; else order_check=0;
            //alert(order_check);return;
			if($("#chk_related_order").prop('checked')) var related_order_check=1; else related_order_check=0;
            show_list_view(document.getElementById('cbo_company_name').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_text').value + '**' + document.getElementById('hidden_type').value + '**' + document.getElementById('hidden_buyer_id').value + '**' + document.getElementById('hidden_po_selectedID').value + '**' + document.getElementById('export_lcID').value + '**' + document.getElementById('txt_file_no').value + '**' + document.getElementById('txt_sc_lc').value+ '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value+ '**' +<? echo $cbo_export_item_category; ?>+ '**' +'<? echo $import_btb; ?>'+ '**' + order_check + '**' +'<? echo $lc_type; ?>'+ '**' + related_order_check +'**'+ '<? echo $lc_sc_no; ?>'+'**'+ document.getElementById('cbo_year_selection').value+'**'+ document.getElementById('cbo_string_search_type').value, 'create_po_search_list_view', 'search_div', 'export_lc_controller', 'setFilterGrid(\'tbl_list_search\',-1)');
        }
        
        function fn_close(str)
        {
            var str_ref=str.split("__");
            $('#order_from_sc').val(str_ref[0]);
            $('#lc_attach_sc_id').val(str_ref[1]);
            //alert(str);return;
            parent.emailwindow.hide();
        }
        function fnc_search_by(id)
        {
            if(id==1){ document.getElementById('search_by').innerHTML = 'PO Number';}
            if(id==2){ document.getElementById('search_by').innerHTML = 'Job No';}
            if(id==3){ document.getElementById('search_by').innerHTML = 'Style Ref No';}
            if(id==4){ document.getElementById('search_by').innerHTML = 'Internal Ref';}
            if(id==5){ document.getElementById('search_by').innerHTML = 'FSO Number';}
            if(id==6){ document.getElementById('search_by').innerHTML = 'Export Proforma Invoice(PI)';}
        }

    </script>

    </head>

    <body>
        <div align="center" style="width:100%;" > 
            <form name="searchpofrm"  id="searchpofrm">
                <fieldset style="width:1100px">
                    <table width="1080" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all">
                        <thead>
                        <tr>
                            <th colspan="8">
                                <?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?>
                            </th>
                            <th>
                                <input type="checkbox" id="chk_order" name="chk_order" />
                                <input type="hidden" id="order_from_sc" name="order_from_sc" value="0" />
                                <input type="hidden" id="lc_attach_sc_id" name="lc_attach_sc_id" value="" />
                                Order From SC
                             </th>   
                        </tr>
                        <tr>
                        	<th>Company</th>
                            <th>Job Year</th>
                            <th>Search By</th>
                            <th id="search_by">PO Number</th>
                            <th>File No</th>
                            <th>SC/LC No</th>
                            <th>Shipment Date</th>
                            <th>Related Orders</th>
                            <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;"></th>   
                        </tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td align="center">
                                    <?
                                    echo create_drop_down("cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", $company_id, "");
                                    ?>
                                </td>
                                <td align="center">
                                    <?
                                        echo create_drop_down("cbo_year_selection", 65, create_year_array(), "", 1, "All Year", date("Y", time()), "", 0, "");
                                    ?>
                                </td>
                                <td align="center">
                                    <?
									//
                                    $arr = array(1=>'PO Number',2=>'Job No',3=>'Style Ref No',4=>'Internal Ref',5=>'FSO Number',6=>'Export Proforma Invoice(PI)');
                                    echo create_drop_down("cbo_search_by", 150, $arr, "", 0, "--- Select ---", '', "fnc_search_by(this.value);");
                                    if($cbo_export_item_category==10) $is_sales=1; else $is_sales=0;
                                    if(($cbo_export_item_category==23 || $cbo_export_item_category==35 || $cbo_export_item_category==36 || $cbo_export_item_category==37 || $cbo_export_item_category==38  || $cbo_export_item_category==45  || $cbo_export_item_category==67) && $lc_type==2) $is_service=1; else $is_service=0;
                                    ?>
                                </td>
                                <td align="center">
                                    <input type="text" name="txt_search_text" id="txt_search_text" class="text_boxes" style="width:150px" />
                                    <input type="hidden" id="hidden_type" value="<? echo $types; ?>" />	
                                    <input type="hidden" id="hidden_buyer_id" value="<? echo $buyer_id; ?>" />	
                                    <input type="hidden" id="hidden_po_selectedID" value="<? echo $selectID; ?>" />
                                    <input type="hidden" id="export_lcID" value="<? echo $export_lcID; ?>" />
                                    <input type="hidden" name="txt_selected_id" id="txt_selected_id" value="" />
                                    <input type="hidden" name="txt_is_sales" id="txt_is_sales" value="<? echo $is_sales; ?>" />				
                                    <input type="hidden" name="txt_is_service" id="txt_is_service" value="<? echo $is_service; ?>" />				
                                </td>
                                <td align="center">
                                    <input type="text" id="txt_file_no" name="txt_file_no" class="text_boxes" style="width:80px" >
                                </td>
                                <td align="center">
                                    <input type="text" id="txt_sc_lc" name="txt_sc_lc" class="text_boxes" style="width:80px" >
                                </td>
                                <td> 
                                    <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px;" />To
                                    <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px;" />
                                </td>
                                <td><input type="checkbox" id="chk_related_order" name="chk_related_order" /> </td>
                                <td align="center">
                                    <input type="button" name="button" class="formbutton" value="Show" onClick="fn_order_list();" style="width:100px;" />
                                </td>
                            </tr>
                            <tr>
                                <td align="center" colspan="8"><? echo load_month_buttons(1); ?></td>
                            </tr>
                        </tbody>
                        
                    </table>
                    <div style="margin-top:3px" id="search_div" align="left"></div>
                </fieldset>
            </form>
        </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if ($action == "create_po_search_list_view") 
{
    $data = explode('**', $data);
	if($_SESSION['logic_erp']['buyer_id']!='' && $_SESSION['logic_erp']['buyer_id']!=0){$user_buyer=" and wm.buyer_name in (".$_SESSION['logic_erp']['buyer_id'].")";}
	if($_SESSION['logic_erp']['brand_id']!='' && $_SESSION['logic_erp']['brand_id']!=0){$user_brand=" and wm.brand_id in (".$_SESSION['logic_erp']['brand_id'].")";}
    
    $action_types = $data[3];
    $buyer_id = $data[4];
    $export_lcID = $data[6];
    $txt_file_no = $data[7];
	$ship_start_date = $data[9];
    $ship_end_date = $data[10];
	$cbo_export_item_category = $data[11];
	$import_btb = $data[12];
	$order_form_sc = $data[13];
	$lc_type = $data[14];
	$check_related_order=$data[15];
	$lc_sc_no=$data[16];
	$job_year=$data[17];
	$search_type=$data[18];
	//echo $cbo_export_item_category."=".$lc_type."=".$import_btb;die;
	//echo $cbo_export_item_category;die;company_id
    $export_pi_arr=array();
    if(($cbo_export_item_category==10 || $cbo_export_item_category==23 || $cbo_export_item_category==35 || $cbo_export_item_category==36 || $cbo_export_item_category==37 || $cbo_export_item_category==38  || $cbo_export_item_category==67) && $lc_type==2 && $import_btb!=1)
    {
        if ($data[0] != 0)
        {
            $company = " and wm.company_id='$data[0]'"; 
        }
        else {
            echo "Please Select Company First.";
            die;
        }
        
        if ($data[2] != '') 
        {
            if($cbo_export_item_category==10)
            {
                if ($data[1] == 1)
                {
                    // $search_text = " and wm.job_no like '%" . trim($data[2]) . "%'";
                    if($search_type==1){ $search_text = " and wm.job_no ='" . trim($data[2]) . "'"; }
                    else if($search_type==2){ $search_text = " and wm.job_no like '" . trim($data[2]) . "%'"; }
                    else if($search_type==3){ $search_text = " and wm.job_no like '%" . trim($data[2]) . "'"; }
                    else{ $search_text = " and wm.job_no like '%" . trim($data[2]) . "%'"; }
                }
                else if ($data[1] == 2)
                {
                    if($search_type==1){ $search_text = " and wm.job_no ='" . trim($data[2]) . "'"; }
                    else if($search_type==2){ $search_text = " and wm.job_no like '" . trim($data[2]) . "%'"; }
                    else if($search_type==3){ $search_text = " and wm.job_no like '%" . trim($data[2]) . "'"; }
                    else{ $search_text = " and wm.job_no like '%" . trim($data[2]) . "%'"; }
                }
                else if ($data[1] == 3)
                {
                    if($search_type==1){ $search_text = " and wm.style_ref_no = '" . trim($data[2]) . "'"; }
                    else if($search_type==2){ $search_text =  " and wm.style_ref_no like '" . trim($data[2]) . "%'"; }
                    else if($search_type==3){ $search_text =  " and wm.style_ref_no like '%" . trim($data[2]) . "'"; }
                    else{ $search_text = " and wm.style_ref_no like '%" . trim($data[2]) . "%'";}
                }
                else if ($data[1] == 5)
                {
                    if($search_type==1){ $search_text = " and wm.job_no = '" . trim($data[2]) . "'"; }
                    else if($search_type==2){ $search_text = " and wm.job_no like '" . trim($data[2]) . "%'"; }
                    else if($search_type==3){ $search_text = " and wm.job_no like '%" . trim($data[2]) . "'"; }
                    else{ $search_text = " and wm.job_no like '%" . trim($data[2]) . "%'";}
                }
                else if ($data[1] == 6)
                {
                    if($search_type==1){ $search_pi_number= " and a.pi_number = '" . trim($data[2]) . "'"; }
                    else if($search_type==2){ $search_pi_number = " and a.pi_number like '" . trim($data[2]) . "%'"; }
                    else if($search_type==3){ $search_pi_number = " and a.pi_number like '%" . trim($data[2]) . "'"; }
                    else{ $search_pi_number = " and a.pi_number like '%" . trim($data[2]) . "%'";}
                    $export_pi_sql=sql_select("SELECT b.WORK_ORDER_ID from com_export_pi_mst a, com_export_pi_dtls b where a.id=b.pi_id and a.status_active=1 and b.status_active=1 $search_pi_number");
                    if(count($export_pi_sql)>0)
                    {
                        foreach($export_pi_sql as $row)
                        {
                            $order_id_arr[$row["WORK_ORDER_ID"]]=$row["WORK_ORDER_ID"];
                        }
                        $search_text = where_con_using_array($order_id_arr,0,'wm.id');
                    }
                }
            }
            elseif($cbo_export_item_category==35 || $cbo_export_item_category==36)
            {
                if ($data[1] == 1)
                {
                    if($search_type==1){ $search_text= " and wm.embellishment_job = '" . trim($data[2]) . "'"; }
                    else if($search_type==2){ $search_text = " and wm.embellishment_job like '" . trim($data[2]) . "%'"; }
                    else if($search_type==3){ $search_text = " and wm.embellishment_job like '%" . trim($data[2]) . "'"; }
                    else{ $search_text = " and wm.embellishment_job like '%" . trim($data[2]) . "%'";}
                }
                else if ($data[1] == 2)
                {
                    $search_text = " and wm.embellishment_job like '%" . trim($data[2]) . "'";
                }
            }
            else
            {
                if ($data[1] == 1)
                {
                    // $search_text = " and wb.order_no like '%" . trim($data[2]) . "%'";
                    if($search_type==1){ $search_text= " and wb.order_no = '" . trim($data[2]) . "'"; }
                    else if($search_type==2){ $search_text = " and wb.order_no like '" . trim($data[2]) . "%'"; }
                    else if($search_type==3){ $search_text = " and wb.order_no like '%" . trim($data[2]) . "'"; }
                    else{ $search_text = " and wb.order_no like '%" . trim($data[2]) . "%'";}
                }
                else if ($data[1] == 2)
                {
                    // $search_text = " and wm.subcon_job like '%" . trim($data[2]) . "'";
                    if($search_type==1){ $search_text= " and wm.subcon_job = '" . trim($data[2]) . "'"; }
                    else if($search_type==2){ $search_text = " and wm.subcon_job like '" . trim($data[2]) . "%'"; }
                    else if($search_type==3){ $search_text = " and wm.subcon_job like '%" . trim($data[2]) . "'"; }
                    else{ $search_text = " and wm.subcon_job like '%" . trim($data[2]) . "%'";}
                }
            }            
        }

        $selected_order_id = "";
        // ## review later ###//
		
        if ($data[5] != "" && $cbo_export_item_category==10)
        {
			
           	$selected_order_id = "and wm.id not in (" . $data[5] . ")";
        }
	
        //echo $cbo_export_item_category.test;die;
        
        if ($ship_start_date != '' && $ship_end_date != '')
        {
            if ($db_type == 0) {
                $date = "and wm.delivery_date between '" . change_date_format($ship_start_date, 'yyyy-mm-dd') . "' and '" . change_date_format($ship_end_date, 'yyyy-mm-dd') . "'";
            } else if ($db_type == 2) {
                $date = "and wm.delivery_date between '" . change_date_format($ship_start_date, '', '', 1) . "' and '" . change_date_format($ship_end_date, '', '', 1) . "'";
            }
        } 
        else 
        {
            $date = "";
        }
        $year_field_cond='';
        if ($db_type == 0){
            $select_field = "YEAR(wm.insert_date) as YEAR,";
            if ($job_year!=0) $year_field_cond=" and YEAR(wm.insert_date)=$job_year";
        }
        else if ($db_type == 2){
            $select_field = "to_char(wm.insert_date,'YYYY') as YEAR,";
            if ($job_year!=0) $year_field_cond=" and to_char(wm.insert_date,'YYYY')=$job_year";
        }
        else{
            $select_field = ""; //defined Later
        }

        if($cbo_export_item_category==35 || $cbo_export_item_category==36)
        {
            $tbl_relation= "wm.embellishment_job = wb.job_no_mst";
        }
        else
        {
            $tbl_relation= "wm.subcon_job = wb.job_no_mst";
        } 
        if($cbo_export_item_category!=67){$within_group=' and wm.within_group=2 ';}else{$within_group='';}

        // , 45=>'255'
        $cat_wise_entry_form=array(23=>'278', 36=>'311', 35=>'204', 37=>'295', 67=>'238');
        
		//echo $action_types."=".$import_btb."=".$cbo_export_item_category;die;

        if ($action_types == 'attached_po_status' && $import_btb != 1) 
        {
            $lc_details = return_library_array("select id, export_lc_no from com_export_lc", 'id', 'export_lc_no');
            $sc_details = return_library_array("select id, contract_no from com_sales_contract", 'id', 'contract_no');
			
			$lc_file_no_cond="";
			if($txt_file_no!="") $lc_file_no_cond=" and b.internal_file_no = '$txt_file_no'"; 
			
            $lc_array = array();
            $sc_array = array();
            $attach_qnty_array = array();
            if($cbo_export_item_category==10)
            {
				$sql_lc_sc="select a.com_export_lc_id as id, a.wo_po_break_down_id, b.internal_file_no, sum(a.attached_qnty) as qnty, 1 as type 
				from com_export_lc_order_info a, COM_EXPORT_LC b 
				where a.COM_EXPORT_LC_ID=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $lc_file_no_cond
				group by a.com_export_lc_id, a.wo_po_break_down_id, b.internal_file_no
				union all
				select a.com_sales_contract_id as id, a.wo_po_break_down_id, b.internal_file_no, sum(a.attached_qnty) as qnty, 2 as type 
				from com_sales_contract_order_info a, COM_SALES_CONTRACT b 
				where a.COM_SALES_CONTRACT_ID=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $lc_file_no_cond
				group by a.com_sales_contract_id, a.wo_po_break_down_id, b.internal_file_no";
                
            }else{
				$sql_lc_sc="select a.com_export_lc_id as id, a.wo_po_break_down_id, b.internal_file_no, sum(a.attached_qnty) as qnty, 1 as type 
				from com_export_lc_order_info a, COM_EXPORT_LC b 
				where a.COM_EXPORT_LC_ID=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.is_service=1  $lc_file_no_cond
				group by a.com_export_lc_id, a.wo_po_break_down_id, b.internal_file_no
				union all
				select a.com_sales_contract_id as id, a.wo_po_break_down_id, b.internal_file_no, sum(a.attached_qnty) as qnty, 2 as type 
				from com_sales_contract_order_info a, COM_SALES_CONTRACT b 
				where a.COM_SALES_CONTRACT_ID=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $lc_file_no_cond
				group by a.com_sales_contract_id, a.wo_po_break_down_id, b.internal_file_no";
            }
			//echo $sql_lc_sc;
            $lc_sc_Array = sql_select($sql_lc_sc);$lc_sc_file_arr=array();
            foreach ($lc_sc_Array as $row_lc_sc) 
			{
                if (array_key_exists($row_lc_sc[csf('wo_po_break_down_id')], $attach_qnty_array)) {
                    $attach_qnty_array[$row_lc_sc[csf('wo_po_break_down_id')]] += $row_lc_sc[csf('qnty')];
                } else {
                    $attach_qnty_array[$row_lc_sc[csf('wo_po_break_down_id')]] = $row_lc_sc[csf('qnty')];
                }
				$lc_sc_file_arr[$row_lc_sc[csf('wo_po_break_down_id')]]=$row_lc_sc[csf('internal_file_no')];
				
                if ($row_lc_sc[csf('type')] == 1) {
                    if ($row_lc_sc[csf('qnty')] > 0) {
                        if (array_key_exists($row_lc_sc[csf('wo_po_break_down_id')], $lc_array)) {
                            $lc_array[$row_lc_sc[csf('wo_po_break_down_id')]] .= "," . $row_lc_sc[csf('id')];
                        } else {
                            $lc_array[$row_lc_sc[csf('wo_po_break_down_id')]] = $row_lc_sc[csf('id')];
                        }
                    }
                } else {
                    if ($row_lc_sc[csf('qnty')] > 0) {
                        if (array_key_exists($row_lc_sc[csf('wo_po_break_down_id')], $sc_array)) {
                            $sc_array[$row_lc_sc[csf('wo_po_break_down_id')]] .= "," . $row_lc_sc[csf('id')];
                        } else {
                            $sc_array[$row_lc_sc[csf('wo_po_break_down_id')]] = $row_lc_sc[csf('id')];
                        }
                    }
                }
            }

            if($cbo_export_item_category==10)
			{
				$sql = "SELECT wm.id as ID, wm.job_no as PO_NUMBER, wm.job_no_prefix_num as JOB_NO_PREFIX_NUM, sum(wb.amount) as PO_TOTAL_PRICE, sum(wb.finish_qty) as PO_QUANTITY, wm.delivery_date as SHIPMENT_DATE, wb.job_no_mst, $select_field wm.style_ref_no as STYLE_REF_NO, avg(wb.avg_rate) as unit_price ,0 as BUYER_STYLE_REF
				FROM fabric_sales_order_mst wm, fabric_sales_order_dtls wb 
				WHERE wm.id = wb.mst_id and wm.buyer_id like '$buyer_id' and wb.is_deleted = 0 AND wb.status_active = 1 and wm.is_deleted = 0 AND wm.status_active = 1 and wm.within_group=2 $selected_order_id $company $search_text $date $year_field_cond
				group by wm.id, wm.job_no, wm.job_no_prefix_num, wm.delivery_date, wb.job_no_mst, wm.style_ref_no, wm.insert_date";
			}
            else
            {
                // , wb.job_no_mst as PO_NUMBER
                $sql = "SELECT wb.id as ID, sum(wb.amount) as PO_TOTAL_PRICE, sum(wb.order_quantity) as PO_QUANTITY, wm.delivery_date as SHIPMENT_DATE, wb.order_no as PO_NUMBER, wm.job_no_prefix_num as JOB_NO_PREFIX_NUM, $select_field wb.cust_style_ref as STYLE_REF_NO, wb.buyer_style_ref as BUYER_STYLE_REF, avg(wb.rate) as UNIT_PRICE
                FROM subcon_ord_mst wm, subcon_ord_dtls wb 
                WHERE $tbl_relation and wm.party_id like '$buyer_id' and wm.entry_form = ".$cat_wise_entry_form[$cbo_export_item_category]." and wb.is_deleted = 0 AND wb.status_active = 1 and wm.is_deleted = 0 AND wm.status_active = 1 $selected_order_id $company $search_text $date $within_group $year_field_cond
                group by  wb.id, wm.subcon_job, wm.delivery_date, wb.order_no, wm.job_no_prefix_num, wm.insert_date, wb.cust_style_ref, wb.buyer_style_ref";  
            }

            ?>
            <div>
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1150" style="margin-left:1px" class="rpt_table" >
                    <thead>
                    <th width="30">SL</th>
                    <th width="100">PO No</th>
                    <th width="100">Acc.PO No</th>
                    <th width="40">Year</th>
                    <th width="50">Job No</th>
                    <th width="90">Brand</th>
                    <th width="100">Item</th>
                    <th width="100">Style No</th>
                    <th width="80">PO Quantity</th>
                    <th width="50">Rate</th>
                    <th width="80">Price</th>
                    <th width="70">Shipment Date</th>
                    <th width="100">Attached With</th>
                    <th width="60">LC/SC</th>
                    <th>File No</th>
                    <th>SC/LC No</th>
                    </thead>
                </table>
                <div style="width:1170px; overflow-y:scroll; max-height:250px;" id="buyer_list_view" align="center">
                    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1150" class="rpt_table" id="tbl_list_search" >
                        <?
                        $i = 1;
                        $nameArray = sql_select($sql);
                        foreach ($nameArray as $selectResult) {
                            if (array_key_exists($selectResult['ID'], $attach_qnty_array)) {
                                $order_attached_qnty = $attach_qnty_array[$selectResult['ID']];

                                if ($order_attached_qnty >= $selectResult['PO_QUANTITY']) {
                                    $all_lc_id = explode(",", $lc_array[$selectResult['ID']]);
                                    foreach ($all_lc_id as $lc_id) {
                                        if ($lc_id != 0) {
                                            if ($i % 2 == 0)
                                                $bgcolor = "#E9F3FF";
                                            else
                                                $bgcolor = "#FFFFFF";

                                            if($cbo_export_item_category==10){$style_ref=$selectResult['STYLE_REF_NO'];}
                                            else{$style_ref=$selectResult['BUYER_STYLE_REF'];}
                                            ?>	
                                            <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                                <td width="30"><? echo $i; ?></td>
                                                <td width="100"><p><? echo $selectResult['PO_NUMBER']; ?></p></td>
                                                <td width="100"><p><? echo $actual_po_arr[$selectResult['ID']]; ?>&nbsp;</p></td>
                                                <td width="40" align="center"><p><? echo $selectResult['YEAR']; ?>&nbsp;</p></td>
                                                <td width="50"><p>&nbsp;<? echo $selectResult['JOB_NO_PREFIX_NUM']; ?></p></td>
                                                <td width="90"><p>&nbsp;<? //echo $brand_arr[$selectResult[csf('brand_id')]];  ?></p></td>
                                                <td width="100">
                                                    <p>
                                                        <?
                                                        /*$gmts_item = '';
                                                        $gmts_item_id = explode(",", $selectResult[csf('gmts_item_id')]);
                                                        foreach ($gmts_item_id as $item_id) {
                                                            if ($gmts_item == "")
                                                                $gmts_item = $garments_item[$item_id];
                                                            else
                                                                $gmts_item .= "," . $garments_item[$item_id];
                                                        }
                                                        echo $gmts_item;*/
                                                        ?>
                                                    </p>
                                                </td>
                                                <td width="100"><p><? echo $style_ref; ?></p></td>
                                                <td width="80" align="right"><? echo $selectResult['PO_QUANTITY']; ?></td>
                                                <td width="50" align="right"><? echo number_format($selectResult['UNIT_PRICE'], 2); ?></td>
                                                <td width="80" align="right"><? echo number_format($selectResult['PO_TOTAL_PRICE'], 2); ?></td>
                                                <td align="center" width="70"><? echo change_date_format($selectResult['SHIPMENT_DATE']); ?></td>
                                                <td width="100"><p><? echo $lc_details[$lc_id]; ?></p></td>
                                                <td align="center" width="60"><? echo 'LC'; ?></td>
                                                <td width="80"><p><? echo $lc_sc_file_arr[$selectResult['ID']];//echo $selectResult[csf('file_no')]; ?>&nbsp;</p></td>
                                                <td width="80"><p><? //echo $selectResult[csf('sc_lc')]; ?>&nbsp;</p></td>
                                            </tr>
                                            <?
                                            $i++;
                                        }
                                    }

                                    $all_sc_id = explode(",", $sc_array[$selectResult[csf('id')]]);

                                    foreach ($all_sc_id as $sc_id) {
                                        if ($sc_id != 0) {
                                            if ($i % 2 == 0)
                                                $bgcolor = "#E9F3FF";
                                            else
                                                $bgcolor = "#FFFFFF";
                                            ?>	
                                            <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                                <td width="30"><? echo $i; ?></td>
                                                <td width="100"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
                                                <td width="100"><p><? echo $actual_po_arr[$selectResult[csf('id')]]; ?>&nbsp;</p></td>
                                                <td width="40" align="center"><p><? echo $selectResult[csf('year')]; ?>&nbsp;</p></td>
                                                <td width="50"><p>&nbsp;<? echo $selectResult[csf('job_no_prefix_num')]; ?></p></td>
                                                <td width="90"><p>&nbsp;<? echo $brand_arr[$selectResult[csf('brand_id')]]; ?></p></td>
                                                <td width="110">
                                                    <p>
                                                        <?
                                                        $gmts_item = '';
                                                        $gmts_item_id = explode(",", $selectResult[csf('gmts_item_id')]);
                                                        foreach ($gmts_item_id as $item_id) {
                                                            if ($gmts_item == "")
                                                                $gmts_item = $garments_item[$item_id];
                                                            else
                                                                $gmts_item .= "," . $garments_item[$item_id];
                                                        }
                                                        echo $gmts_item;
                                                        ?>
                                                    </p>
                                                </td>
                                                <td width="100"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
                                                <td width="90" align="right"><? echo $selectResult[csf('po_quantity')]; ?></td>
                                                <td width="90" align="right"><? echo number_format($selectResult[csf('po_total_price')], 2); ?></td>
                                                <td align="center" width="70"><? echo change_date_format($selectResult[csf('shipment_date')]); ?></td>
                                                <td width="100"><p><? echo $sc_details[$sc_id]; ?></p></td>
                                                <td align="center" width="60"><? echo 'SC'; ?></td>
                                                <td width="80"><p><? echo $lc_sc_file_arr[$selectResult['ID']]; //echo $selectResult[csf('file_no')]; ?>&nbsp;</p></td>
                                                <td width="80"><p><? echo $selectResult[csf('sc_lc')]; ?>&nbsp;</p></td>
                                            </tr>
                                            <?
                                            $i++;
                                        }
                                    }
                                }
                            }
                        }
                        ?>
                    </table>
                </div>
            </div>                   
            <?
            exit();
        }
		//echo $action_types."=".$cbo_export_item_category;
        if($action_types == 'order_select_popup') 
        {
            if($cbo_export_item_category==10)
			{
                if($db_type==0)
                {
                    $export_pi_arr = return_library_array("SELECT group_concat(a.pi_number) as pi_number, b.work_order_id from com_export_pi_mst a, com_export_pi_dtls b where a.id=b.pi_id and a.item_category_id=10 and a.status_active=1 and b.status_active=1 group by b.work_order_id", "work_order_id", "pi_number");
                }
                else
                {
                    $export_pi_arr = return_library_array("SELECT listagg(a.pi_number,',') within group (order by a.id) as pi_number, b.work_order_id from com_export_pi_mst a, com_export_pi_dtls b where a.id=b.pi_id and a.item_category_id=10 and a.status_active=1 and b.status_active=1 group by b.work_order_id", "work_order_id", "pi_number");
                }

				$sql = "SELECT wm.id as ID, wm.job_no as PO_NUMBER, wm.job_no_prefix_num as JOB_NO_PREFIX_NUM, sum(wb.amount) as PO_TOTAL_PRICE, sum(wb.finish_qty) as PO_QUANTITY, wm.delivery_date as SHIPMENT_DATE, wb.job_no_mst, $select_field wm.style_ref_no as STYLE_REF_NO, avg(wb.avg_rate) as unit_price ,0 as BUYER_STYLE_REF
				FROM fabric_sales_order_mst wm, fabric_sales_order_dtls wb 
				WHERE wm.id = wb.mst_id and wm.buyer_id like '$buyer_id' and wb.is_deleted = 0 AND wb.status_active = 1 and wm.is_deleted = 0 AND wm.status_active = 1 and wm.within_group=2 $selected_order_id $company $search_text $date $year_field_cond
				group by wm.id, wm.job_no, wm.job_no_prefix_num, wm.delivery_date, wb.job_no_mst, wm.style_ref_no, wm.insert_date";
				//echo $sql;
			}
            else
            {
                // , wb.job_no_ms as PO_NUMBER
                $sql = "SELECT wb.id as ID, sum(wb.amount) as PO_TOTAL_PRICE, sum(wb.order_quantity) as PO_QUANTITY, wm.delivery_date as SHIPMENT_DATE, wb.order_no as PO_NUMBER, wm.job_no_prefix_num as JOB_NO_PREFIX_NUM, $select_field wb.cust_style_ref as STYLE_REF_NO, wb.buyer_style_ref as BUYER_STYLE_REF, avg(wb.rate) as UNIT_PRICE
                FROM subcon_ord_mst wm, subcon_ord_dtls wb 
                WHERE $tbl_relation and wm.party_id like '$buyer_id' and wm.entry_form = ".$cat_wise_entry_form[$cbo_export_item_category]." and wb.is_deleted = 0 AND wb.status_active = 1 and wm.is_deleted = 0 AND wm.status_active = 1 $selected_order_id $company $search_text $date $within_group $year_field_cond
                group by  wb.id, wm.subcon_job, wm.delivery_date, wb.order_no, wm.job_no_prefix_num, wm.insert_date, wb.cust_style_ref,wb.buyer_style_ref";  
            }
            //echo $sql."<br>";//die;
            ?>
            <div>
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1250" style="margin-left:1px" class="rpt_table" >
                    <thead>
                    <th width="30">SL</th>
                    <th width="100">PO No</th>
                    <th width="100">Internal Ref</th>
                    <th width="100">Acc.PO No</th>
                    <th width="30">Year</th>
                    <th width="30">Job No</th>
                    <th width="90">Brand</th>
                    <th width="130">Item</th>
                    <th width="110">Style No</th>
                    <th width="70">PO Quantity</th>
                    <th width="50">Rate</th>
                    <th width="80">Price</th>
                    <th width="60">Shipment Date</th>
                    <th width="70">File No</th>
                    <th width="100">SC/LC No</th>
                    <th>Export PI</th>
                    </thead>
                </table>
                <div style="width:1270px; overflow-y:scroll; max-height:220px;" id="buyer_list_view" align="center">
                    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1250" class="rpt_table" id="tbl_list_search" align="left" >
                        <?
                        $i = 1;
                        /*if($import_btb == 1)
                        {
                            $is_sales_cond=" and is_sales=1";
                            $is_sales=1;
                        }
                        else 
                        {
                            $is_sales_cond=" and is_sales=0";
                            $is_sales=0;
                        }*/
						
						//echo $sql;
						
                        $lc_attached_qnty_arr = return_library_array("select wo_po_break_down_id, sum(attached_qnty) as qty from com_export_lc_order_info where status_active = 1 and is_deleted=0 and com_export_lc_id>0 and export_item_category=$cbo_export_item_category $is_sales_cond group by wo_po_break_down_id", "wo_po_break_down_id", "qty");
                        $sc_attached_qnty_arr = return_library_array("SELECT a.wo_po_break_down_id, sum(a.attached_qnty) as qty from com_sales_contract_order_info a, com_sales_contract b where a.com_sales_contract_id=b.id and b.export_item_category=$cbo_export_item_category and a.status_active = 1 and a.is_deleted=0 and b.status_active = 1 and a.com_sales_contract_id>0 $is_sales_cond group by a.wo_po_break_down_id", "wo_po_break_down_id", "qty");

                        $nameArray = sql_select($sql);
                        foreach ($nameArray as $selectResult) 
                        {
                            if ($i % 2 == 0)
                                $bgcolor = "#E9F3FF";
                            else
                                $bgcolor = "#FFFFFF";

                            $lc_attached_qnty = $lc_attached_qnty_arr[$selectResult['ID']];
                            $sc_attached_qnty = $sc_attached_qnty_arr[$selectResult['ID']];
                            if($order_form_sc)
                            {
                                $order_attached_qnty = $lc_attached_qnty;
                                // $attach_qnty=$selectResult[csf('attached_qnty')];
                                $attach_qnty=0;
                            }
                            else
                            {
                                $order_attached_qnty = $sc_attached_qnty + $lc_attached_qnty;
                                $attach_qnty=0;
                            }
                            
                            if($cbo_export_item_category==10){$style_ref=$selectResult['STYLE_REF_NO'];}
                            else{$style_ref=$selectResult['BUYER_STYLE_REF'];}
                            if ($order_attached_qnty < $selectResult['PO_QUANTITY']) 
                            {
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)"> 
                                    <td width="30" align="center"><? echo "$i"; ?>
                                        <input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $selectResult['ID']; ?>"/>	
                                        <!-- <input type="hidden" name="txt_is_sales" id="txt_is_sales<? echo $i ?>" value="<? echo $is_sales; ?>"/>	 -->
                                    </td>	
                                    <td width="100"><p><? echo $selectResult['PO_NUMBER']; ?></p></td>
                                    <td width="100"><p><? ?></p></td>
                                    <td width="100"><p><? ?></p></td>
                                    <td width="30" align="center"><p><? echo $selectResult['YEAR']; ?></p></td>
                                    <td width="30" align="center"><p><? echo $selectResult['JOB_NO_PREFIX_NUM']; ?></p></td>
                                    <td width="90" align="center"><p><??></p></td>
                                    <td width="130">
                                    </td> 
                                    <td width="110"><p><? echo $style_ref; ?></p></td>
                                    <td width="70" align="right"><? echo $selectResult['PO_QUANTITY']; ?></td>
                                    <td width="50" align="right"><? echo number_format($selectResult['UNIT_PRICE'], 2); ?></td>
                                    <td width="80" align="right"><? echo number_format($selectResult['PO_TOTAL_PRICE'], 2); ?></td>
                                    <td align="center" width="60"><? echo change_date_format($selectResult['SHIPMENT_DATE']); ?></td>
                                    <td width="70"><p><? ?>&nbsp;</p></td>	
                                    <td width="100"><p><?  ?>&nbsp;</p></td>
                                    <td><p><? echo implode(", ",array_unique(explode(",",chop($export_pi_arr[$selectResult['ID']],',')))) ?>&nbsp;</p></td>	
                                </tr>
                                <?
                                $i++;
                            }
                        }
                        ?>
                    </table>
                </div>
                <table width="1250" cellspacing="0" cellpadding="0" style="border:none" align="center">
                    <tr>
                        <td align="center" height="30" valign="bottom">
                            <div style="width:100%"> 
                                <div style="width:50%; float:left" align="left">
                                    <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                                </div>
                                <div style="width:50%; float:left" align="left">
                                    <input type="button" name="close" onClick="fn_close('<? echo $order_form_sc."__".implode(",",$sc_ids); ?>')" class="formbutton" value="Close" style="width:100px" />
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <?
        }
    }
    else
    {
        if ($data[0] != 0)
		{
            if($cbo_export_item_category==10 || $cbo_export_item_category==23 || $cbo_export_item_category==37 || $cbo_export_item_category==45 || $cbo_export_item_category==35 || $cbo_export_item_category==36)
            {
                $company = " and wm.company_id='$data[0]'"; 
            }
            else
            {
                $company = " and wm.company_name='$data[0]'"; 
            }
		}
		else {
			echo "Please Select Company First.";
			die;
        }
        //echo $action_types."=".$import_btb."=".$cbo_export_item_category."=".$data[2];die;
        if ($data[2] != '') 
        {
            if($cbo_export_item_category==10)
            {
                if ($data[1] == 1)
                {
                    if($search_type==1){ $search_text= " and wm.job_no_prefix_num = '" . trim($data[2]) . "'"; }
                    else if($search_type==2){ $search_text = " and wm.job_no_prefix_num like '" . trim($data[2]) . "%'"; }
                    else if($search_type==3){ $search_text = " and wm.job_no_prefix_num like '%" . trim($data[2]) . "'"; }
                    else{ $search_text = " and wm.job_no_prefix_num like '%" . trim($data[2]) . "%'";}
                }
                else if ($data[1] == 2)
                {    
                    if($search_type==1){ $search_text= " and wm.job_no_prefix_num = '" . trim($data[2]) . "'"; }
                    else if($search_type==2){ $search_text = " and wm.job_no_prefix_num like '" . trim($data[2]) . "%'"; }
                    else if($search_type==3){ $search_text = " and wm.job_no_prefix_num like '%" . trim($data[2]) . "'"; }
                    else{ $search_text = " and wm.job_no_prefix_num like '%" . trim($data[2]) . "%'";} 
                }
                else if ($data[1] == 3)
                {
                    if($search_type==1){ $search_text= " wm.style_ref_no = '" . trim($data[2]) . "'"; }
                    else if($search_type==2){ $search_text = " wm.style_ref_no like '" . trim($data[2]) . "%'"; }
                    else if($search_type==3){ $search_text = " wm.style_ref_no like '%" . trim($data[2]) . "'"; }
                    else{ $search_text = " wm.style_ref_no like '%" . trim($data[2]) . "%'";}
                }
            }
            elseif($cbo_export_item_category==23 || $cbo_export_item_category==37 || $cbo_export_item_category==45)
            {
                if ($data[1] == 1)
                {
                    if($search_type==1){ $search_text= " and wm.job_no_prefix_num = '" . trim($data[2]) . "'"; }
                    else if($search_type==2){ $search_text = " and wm.job_no_prefix_num like '" . trim($data[2]) . "%'"; }
                    else if($search_type==3){ $search_text = " and wm.job_no_prefix_num like '%" . trim($data[2]) . "'"; }
                    else{ $search_text = " and wm.job_no_prefix_num like '%" . trim($data[2]) . "%'";} 
                }
                else if ($data[1] == 2)
                {
                    if($search_type==1){ $search_text= " and wm.job_no_prefix_num = '" . trim($data[2]) . "'"; }
                    else if($search_type==2){ $search_text = " and wm.job_no_prefix_num like '" . trim($data[2]) . "%'"; }
                    else if($search_type==3){ $search_text = " and wm.job_no_prefix_num like '%" . trim($data[2]) . "'"; }
                    else{ $search_text = " and wm.job_no_prefix_num like '%" . trim($data[2]) . "%'";} 
                }
                else if ($data[1] == 6)
                {
                    if($search_type==1){ $search_pi_number= " and a.pi_number = '" . trim($data[2]) . "'"; }
                    else if($search_type==2){ $search_pi_number = " and a.pi_number like '" . trim($data[2]) . "%'"; }
                    else if($search_type==3){ $search_pi_number = " and a.pi_number like '%" . trim($data[2]) . "'"; }
                    else{ $search_pi_number = " and a.pi_number like '%" . trim($data[2]) . "%'";} 
                    $export_pi_sql=sql_select("SELECT b.WORK_ORDER_ID from com_export_pi_mst a, com_export_pi_dtls b where a.id=b.pi_id and a.status_active=1 and b.status_active=1 $search_pi_number");
                    if(count($export_pi_sql)>0)
                    {
                        foreach($export_pi_sql as $row)
                        {
                            $order_id_arr[$row["WORK_ORDER_ID"]]=$row["WORK_ORDER_ID"];
                        }
                        $search_text = where_con_using_array($order_id_arr,0,'wm.id');
                    }
                }
            }
            elseif($cbo_export_item_category==35 || $cbo_export_item_category==36)
            {
                if ($data[1] == 1)
                {
                    if($search_type==1){ $search_text= " and wm.job_no_prefix_num = '" . trim($data[2]) . "'"; }
                    else if($search_type==2){ $search_text = " and wm.job_no_prefix_num like '" . trim($data[2]) . "%'"; }
                    else if($search_type==3){ $search_text = " and wm.job_no_prefix_num like '%" . trim($data[2]) . "'"; }
                    else{ $search_text = " and wm.job_no_prefix_num like '%" . trim($data[2]) . "%'";} 
                }
                else if ($data[1] == 2)
                {
                    if($search_type==1){ $search_text= " and wm.job_no_prefix_num = '" . trim($data[2]) . "'"; }
                    else if($search_type==2){ $search_text = " and wm.job_no_prefix_num like '" . trim($data[2]) . "%'"; }
                    else if($search_type==3){ $search_text = " and wm.job_no_prefix_num like '%" . trim($data[2]) . "'"; }
                    else{ $search_text = " and wm.job_no_prefix_num like '%" . trim($data[2]) . "%'";} 
                }
            }
            else
            {
                if ($data[1] == 1)
                {
                    // $search_text = " and wb.po_number like '%" . trim($data[2]) . "%'";
                    $ex_data = explode(',', $data[2]);
                    $search_text=' and (';
                    foreach ($ex_data as $val) {
                        // $search_text.="wb.po_number like '".trim($val)."%'".' or ';
                        if($search_type==1){ $search_text.= " wb.po_number = '" . trim($val) . "'".' or '; }
                        else if($search_type==2){ $search_text.= " wb.po_number like '" . trim($val) . "%'".' or '; }
                        else if($search_type==3){ $search_text.= " wb.po_number like '%" . trim($val) . "'".' or '; }
                        else{ $search_text.= " wb.po_number like '%" . trim($val) . "%'".' or ';} 
                    }
                    $search_text=rtrim($search_text,' or');
                    $search_text.=')';
                }    
                else if ($data[1] == 2)
                {
                    if($search_type==1){ $search_text= " and wm.job_no = '" . trim($data[2]) . "'"; }
                    else if($search_type==2){ $search_text = " and wm.job_no like '" . trim($data[2]) . "%'"; }
                    else if($search_type==3){ $search_text = " and wm.job_no like '%" . trim($data[2]) . "'"; }
                    else{ $search_text = " and wm.job_no like '%" . trim($data[2]) . "%'";} 
                }  
                else if ($data[1] == 3)
                {
                    if($search_type==1){ $search_text= " and wm.style_ref_no = '" . trim($data[2]) . "'"; }
                    else if($search_type==2){ $search_text = " and wm.style_ref_no like '" . trim($data[2]) . "%'"; }
                    else if($search_type==3){ $search_text = " and wm.style_ref_no like '%" . trim($data[2]) . "'"; }
                    else{ $search_text = " and wm.style_ref_no like '%" . trim($data[2]) . "%'";} 
                }
                else if($data[1]==4)
                {
                    if($search_type==1){ $search_text= " and wb.grouping = '" . trim($data[2]) . "'"; }
                    else if($search_type==2){ $search_text = " and wb.grouping like '" . trim($data[2]) . "%'"; }
                    else if($search_type==3){ $search_text = " and wb.grouping like '%" . trim($data[2]) . "'"; }
                    else{ $search_text = " and wb.grouping like '%" . trim($data[2]) . "%'";} 
                }
            }
            
        }

        $selected_order_id = "";
        // ## review later ###//
        if ($data[5] != "" )
        {
            if($cbo_export_item_category==10 || $cbo_export_item_category==23 || $cbo_export_item_category==35 || $cbo_export_item_category==36 || $cbo_export_item_category==37) $selected_order_id = "and wm.id not in (" . $data[5] . ")"; else $selected_order_id = "and wb.id not in (" . $data[5] . ")";
        }

        //echo $cbo_export_item_category.test;die;
        
        if ($ship_start_date != '' && $ship_end_date != '')
        {
            if($cbo_export_item_category==10 || $cbo_export_item_category==23 || $cbo_export_item_category==35 || $cbo_export_item_category==36 || $cbo_export_item_category==37 || $cbo_export_item_category==45)
            {
                if ($db_type == 0) {
                    $date = "and wm.delivery_date between '" . change_date_format($ship_start_date, 'yyyy-mm-dd') . "' and '" . change_date_format($ship_end_date, 'yyyy-mm-dd') . "'";
                } else if ($db_type == 2) {
                    $date = "and wm.delivery_date between '" . change_date_format($ship_start_date, '', '', 1) . "' and '" . change_date_format($ship_end_date, '', '', 1) . "'";
                }
            }
            else
            {
                if ($db_type == 0) {
                    $date = "and wb.pub_shipment_date between '" . change_date_format($ship_start_date, 'yyyy-mm-dd') . "' and '" . change_date_format($ship_end_date, 'yyyy-mm-dd') . "'";
                } else if ($db_type == 2) {
                    $date = "and wb.pub_shipment_date between '" . change_date_format($ship_start_date, '', '', 1) . "' and '" . change_date_format($ship_end_date, '', '', 1) . "'";
                }
            }
        } 
        else 
        {
            $date = "";
        }
        // echo $ship_start_date;die;
        if ($txt_file_no != "" && $import_btb!=1)
        {
            // $file_no_cond = " and wb.file_no='$data[7]'";
            if($search_type==1){ $file_no_cond= " and wb.file_no = '" . trim($data[7]) . "'"; }
            else if($search_type==2){ $file_no_cond = " and wb.file_no like '" . trim($data[7]) . "%'"; }
            else if($search_type==3){ $file_no_cond = " and wb.file_no like '%" . trim($data[7]) . "'"; }
            else{ $file_no_cond = " and wb.file_no like '%" . trim($data[7]) . "%'";} 
        }            
        else{ $file_no_cond = ""; }

        if (trim($data[8]) != "" && $cbo_export_item_category!=10)
        {
            // $txt_sc_lc_cond = " and wb.sc_lc like '%" . trim($data[8]) . "%'";
            if($search_type==1){ $txt_sc_lc_cond= " and wb.sc_lc = '" . trim($data[8]) . "'"; }
            else if($search_type==2){ $txt_sc_lc_cond = " and wb.sc_lc like '" . trim($data[8]) . "%'"; }
            else if($search_type==3){ $txt_sc_lc_cond = " and wb.sc_lc like '%" . trim($data[8]) . "'"; }
            else{ $txt_sc_lc_cond = " and wb.sc_lc like '%" . trim($data[8]) . "%'";} 
        }
        else{ $txt_sc_lc_cond = ""; }
        
        $year_field_cond='';
        if ($db_type == 0){
            $select_field = "YEAR(wm.insert_date) as year,";
            if ($job_year!=0) $year_field_cond=" and YEAR(wm.insert_date)=$job_year";
        }
        else if ($db_type == 2){
            $select_field = "to_char(wm.insert_date,'YYYY') as year,";
            if ($job_year!=0) $year_field_cond=" and to_char(wm.insert_date,'YYYY')=$job_year";
        }
        else{
            $select_field = ""; //defined Later
        }

        if($import_btb!=1)
        {
            if ($db_type == 0) {
                $actual_po_arr = return_library_array("select po_break_down_id, group_concat(acc_po_no) as acc_po_no from wo_po_acc_po_info where status_active=1 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "acc_po_no");
            } else {
               // $actual_po_arr = return_library_array("select po_break_down_id, listagg( cast(acc_po_no as varchar(4000)), ',') within group(order by acc_po_no) as acc_po_no from wo_po_acc_po_info where status_active=1 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "acc_po_no");
               $actual_po_arr = return_library_array("select po_break_down_id, acc_po_no as acc_po_no from wo_po_acc_po_info where status_active=1 and is_deleted=0 group by po_break_down_id,acc_po_no", "po_break_down_id", "acc_po_no");
            }
        }
        $cat_wise_entry_form=array(23=>'278', 36=>'311', 35=>'204', 37=>'295', 45=>'255');
        //echo $action_types."=". $import_btb;die;
        $sc_ids=array();
        $brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand brand",'id','brand_name');
        if ($action_types == 'attached_po_status' && $import_btb != 1) 
        {
			//echo $action_types."=34". $import_btb;die;
            $lc_details = return_library_array("select id, export_lc_no from com_export_lc", 'id', 'export_lc_no');
            $sc_details = return_library_array("select id, contract_no from com_sales_contract", 'id', 'contract_no');

            $lc_array = array();
            $sc_array = array();
            $attach_qnty_array = array();
			$lc_file_no_cond="";
			if($txt_file_no!="") $lc_file_no_cond=" and b.internal_file_no = '$txt_file_no'"; 
            if($check_related_order && $lc_sc_no!="") $lc_sc_cond=" and wb.sc_lc='$lc_sc_no'";
			
			if($cbo_export_item_category==23 || $cbo_export_item_category==37 || $cbo_export_item_category==45 || $cbo_export_item_category==35 || $cbo_export_item_category==36)
			{
				$sql_lc_sc="select a.com_export_lc_id as id, a.wo_po_break_down_id, b.internal_file_no, sum(a.attached_qnty) as qnty, 1 as type 
				from com_export_lc_order_info a, COM_EXPORT_LC b 
				where a.COM_EXPORT_LC_ID=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.is_service=1 $lc_file_no_cond
				group by a.com_export_lc_id, a.wo_po_break_down_id, b.internal_file_no";
			}
			else
			{
				$sql_lc_sc="select a.com_export_lc_id as id, a.wo_po_break_down_id, b.internal_file_no, sum(a.attached_qnty) as qnty, 1 as type 
				from com_export_lc_order_info a, COM_EXPORT_LC b 
				where a.COM_EXPORT_LC_ID=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.is_service<>1 $lc_file_no_cond
				group by a.com_export_lc_id, a.wo_po_break_down_id, b.internal_file_no
				union all
				select a.com_sales_contract_id as id, a.wo_po_break_down_id, b.internal_file_no, sum(a.attached_qnty) as qnty, 2 as type 
				from com_sales_contract_order_info a, COM_SALES_CONTRACT b 
				where a.COM_SALES_CONTRACT_ID=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $lc_file_no_cond
				group by a.com_sales_contract_id, a.wo_po_break_down_id, b.internal_file_no";
			}
			
			//echo $sql_lc_sc;
            $lc_sc_Array = sql_select($sql_lc_sc);$lc_sc_file_arr=array();
            foreach ($lc_sc_Array as $row_lc_sc) {
                if (array_key_exists($row_lc_sc[csf('wo_po_break_down_id')], $attach_qnty_array)) {
                    $attach_qnty_array[$row_lc_sc[csf('wo_po_break_down_id')]] += $row_lc_sc[csf('qnty')];
                } else {
                    $attach_qnty_array[$row_lc_sc[csf('wo_po_break_down_id')]] = $row_lc_sc[csf('qnty')];
                }
				$lc_sc_file_arr[$row_lc_sc[csf('wo_po_break_down_id')]]=$row_lc_sc[csf('internal_file_no')];
				
                if ($row_lc_sc[csf('type')] == 1) {
                    if ($row_lc_sc[csf('qnty')] > 0) {
                        if (array_key_exists($row_lc_sc[csf('wo_po_break_down_id')], $lc_array)) {
                            $lc_array[$row_lc_sc[csf('wo_po_break_down_id')]] .= "," . $row_lc_sc[csf('id')];
                        } else {
                            $lc_array[$row_lc_sc[csf('wo_po_break_down_id')]] = $row_lc_sc[csf('id')];
                        }
                    }
                } else {
                    if ($row_lc_sc[csf('qnty')] > 0) {
                        if (array_key_exists($row_lc_sc[csf('wo_po_break_down_id')], $sc_array)) {
                            $sc_array[$row_lc_sc[csf('wo_po_break_down_id')]] .= "," . $row_lc_sc[csf('id')];
                        } else {
                            $sc_array[$row_lc_sc[csf('wo_po_break_down_id')]] = $row_lc_sc[csf('id')];
                        }
                    }
                }
            }
            if($cbo_export_item_category==23 || $cbo_export_item_category==37 || $cbo_export_item_category==45)
            {
                $sql = "SELECT wb.id, wb.order_no as po_number, wb.amount as po_total_price, wb.order_quantity as po_quantity, wb.delivery_date as shipment_date, wb.job_no_mst, null as file_no, wm.job_no_prefix_num, $select_field wb.cust_style_ref as style_ref_no, wb.buyer_style_ref, 0 as gmts_item_id, wb.rate as unit_price, null as sc_lc 
                FROM subcon_ord_dtls wb, subcon_ord_mst wm 
                WHERE wb.job_no_mst = wm.subcon_job and wm.party_id like '$buyer_id' and wm.within_group=2 and wm.entry_form = ".$cat_wise_entry_form[$cbo_export_item_category]." $company $search_text $txt_sc_lc_cond $year_field_cond and wb.is_deleted = 0 AND wb.status_active = 1 $date";
				//echo $sql;
            }
            elseif($cbo_export_item_category==35 || $cbo_export_item_category==36)
            {
                $sql = "SELECT wb.id, wb.order_no as po_number, wb.amount as po_total_price, wb.order_quantity as po_quantity, wb.delivery_date as shipment_date, wb.job_no_mst, null as file_no, wm.job_no_prefix_num, $select_field wb.cust_style_ref as style_ref_no, 0 as gmts_item_id, wb.rate as unit_price, null as sc_lc  
                FROM subcon_ord_dtls wb, subcon_ord_mst wm 
                WHERE wb.job_no_mst = wm.embellishment_job and wm.party_id like '$buyer_id' and wm.within_group=2 and wm.entry_form = ".$cat_wise_entry_form[$cbo_export_item_category]." $company $search_text $txt_sc_lc_cond $year_field_cond and wb.is_deleted = 0 AND wb.status_active = 1 and wb.is_confirmed=1 $date";
				//echo $sql;
            }
            else
            {
                $sql = "SELECT wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst, wb.file_no, wm.job_no_prefix_num, $select_field wm.style_ref_no, wm.gmts_item_id, wb.unit_price,wb.sc_lc, wm.brand_id
                FROM wo_po_break_down wb, wo_po_details_master wm 
                WHERE wb.job_no_mst = wm.job_no and wm.buyer_name like '$buyer_id' $company $search_text $txt_sc_lc_cond $user_buyer $user_brand $year_field_cond  $lc_sc_cond and wb.is_deleted = 0 AND wb.status_active = 1 and wb.is_confirmed=1 $date 
                group by wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date, wb.job_no_mst, wb.file_no, wm.job_no_prefix_num, wm.insert_date, wm.style_ref_no, wm.gmts_item_id, wb.unit_price, wb.sc_lc, wm.brand_id";
            }
            //echo $sql;
            
            ?>
            <div>
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1250" style="margin-left:1px" class="rpt_table" >
                    <thead>
                    <th width="30">SL</th>
                    <th width="100">PO No</th>
                    <th width="100">Act PO No</th>
                    <th width="40">Year</th>
                    <th width="50">Job No</th>
                    <th width="90">Brand</th>
                    <th width="100">Item</th>
                    <th width="100">Style No</th>
                    <th width="80">PO Quantity</th>
                    <th width="50">Rate</th>
                    <th width="80">Price</th>
                    <th width="70">Shipment Date</th>
                    <th width="100">Attached With</th>
                    <th width="60">LC/SC</th>
                    <th width="80">File No</th>
                    <th>SC/LC No</th>
                    </thead>
                </table>
                <div style="width:1270px; overflow-y:scroll; max-height:250px;" id="buyer_list_view" align="center">
                    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1250" class="rpt_table" id="tbl_list_search" >
                        <tbody>
                        <?
                        $i = 1;
						//echo $sql;
                        $nameArray = sql_select($sql);
                        foreach ($nameArray as $selectResult) 
						{
							//echo $selectResult[csf('id')].",";
                            if (array_key_exists($selectResult[csf('id')], $attach_qnty_array)) {
                                $order_attached_qnty = $attach_qnty_array[$selectResult[csf('id')]];

                                if ($order_attached_qnty >= $selectResult[csf('po_quantity')]) {
                                    $all_lc_id = explode(",", $lc_array[$selectResult[csf('id')]]);
                                    foreach ($all_lc_id as $lc_id) {
                                        if ($lc_id != 0) {
                                            if ($i % 2 == 0)
                                                $bgcolor = "#E9F3FF";
                                            else
                                                $bgcolor = "#FFFFFF";

                                            if($cbo_export_item_category==37){
                                                $selectResult[csf('style_ref_no')]=$selectResult[csf('buyer_style_ref')];
                                            }
                                            ?>	
                                            <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                                <td width="30"><? echo $i; ?></td>
                                                <td width="100"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
                                                <td width="100"><p><? echo $actual_po_arr[$selectResult[csf('id')]]; ?>&nbsp;</p></td>
                                                <td width="40" align="center"><p><? echo $selectResult[csf('year')]; ?>&nbsp;</p></td>
                                                <td width="50"><p>&nbsp;<? echo $selectResult[csf('job_no_prefix_num')]; ?></p></td>
                                                <td width="90"><p>&nbsp;<? echo $brand_arr[$selectResult[csf('brand_id')]]; ?></p></td>
                                                <td width="100">
                                                    <p>
                                                        <?
                                                        $gmts_item = '';
                                                        $gmts_item_id = explode(",", $selectResult[csf('gmts_item_id')]);
                                                        foreach ($gmts_item_id as $item_id) {
                                                            if ($gmts_item == "")
                                                                $gmts_item = $garments_item[$item_id];
                                                            else
                                                                $gmts_item .= "," . $garments_item[$item_id];
                                                        }
                                                        echo $gmts_item;
                                                        ?>
                                                    </p>
                                                </td>
                                                <td width="100"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
                                                <td width="80" align="right"><? echo $selectResult[csf('po_quantity')]; ?></td>
                                                <td width="50" align="right"><? echo number_format(($selectResult[csf('po_total_price')]/$selectResult[csf('po_quantity')]), 2); ?></td>
                                                <td width="80" align="right"><? echo number_format($selectResult[csf('po_total_price')], 2); ?></td>
                                                <td align="center" width="70"><? echo change_date_format($selectResult[csf('shipment_date')]); ?></td>
                                                <td width="100"><p><? echo $lc_details[$lc_id]; ?></p></td>
                                                <td align="center" width="60"><? echo 'LC'; ?></td>
                                                <td width="80"><p><? echo $lc_sc_file_arr[$selectResult[csf('id')]];//$selectResult[csf('file_no')]; ?>&nbsp;</p></td>
                                                <td ><p><? echo $selectResult[csf('sc_lc')]; ?>&nbsp;</p></td>
                                            </tr>
                                            <?
                                            $i++;
                                            $po_qty+=$selectResult[csf('po_quantity')];
                                            $po_total_price+=$selectResult[csf('po_total_price')];
                                        }
                                    }

                                    $all_sc_id = explode(",", $sc_array[$selectResult[csf('id')]]);

                                    foreach ($all_sc_id as $sc_id) {
                                        if ($sc_id != 0) {
                                            if ($i % 2 == 0)
                                                $bgcolor = "#E9F3FF";
                                            else
                                                $bgcolor = "#FFFFFF";
                                            ?>	
                                            <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;" onClick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                                <td width="30"><? echo $i; ?></td>
                                                <td width="100"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
                                                <td width="100"><p><? echo $actual_po_arr[$selectResult[csf('id')]]; ?>&nbsp;</p></td>
                                                <td width="40" align="center"><p><? echo $selectResult[csf('year')]; ?>&nbsp;</p></td>
                                                <td width="50"><p>&nbsp;<? echo $selectResult[csf('job_no_prefix_num')]; ?></p></td>
                                                <td width="90"><p>&nbsp;<? echo $brand_arr[$selectResult[csf('brand_id')]]; ?></p></td>
                                                <td width="100">
                                                    <p>
                                                        <?
                                                        $gmts_item = '';
                                                        $gmts_item_id = explode(",", $selectResult[csf('gmts_item_id')]);
                                                        foreach ($gmts_item_id as $item_id) {
                                                            if ($gmts_item == "")
                                                                $gmts_item = $garments_item[$item_id];
                                                            else
                                                                $gmts_item .= "," . $garments_item[$item_id];
                                                        }
                                                        echo $gmts_item;
                                                        ?>
                                                    </p>
                                                </td>
                                                <td width="100"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
                                                <td width="80" align="right"><? echo $selectResult[csf('po_quantity')]; ?></td>
                                                <td width="50" align="right"><? echo number_format(($selectResult[csf('po_total_price')]/$selectResult[csf('po_quantity')]), 2); ?></td>
                                                <td width="80" align="right"><? echo number_format($selectResult[csf('po_total_price')], 2); ?></td>
                                                <td align="center" width="70"><? echo change_date_format($selectResult[csf('shipment_date')]); ?></td>
                                                <td width="100"><p><? echo $sc_details[$sc_id]; ?></p></td>
                                                <td align="center" width="60"><? echo 'SC'; ?></td>
                                                <td width="80"><p><? echo $lc_sc_file_arr[$selectResult[csf('id')]];//$selectResult[csf('file_no')]; ?>&nbsp;</p></td>
                                                <td ><p><? echo $selectResult[csf('sc_lc')]; ?>&nbsp;</p></td>
                                            </tr>
                                            <?
                                            $i++;
                                            $po_qty+=$selectResult[csf('po_quantity')];
                                            $po_total_price+=$selectResult[csf('po_total_price')];
                                        }
                                    }
                                }
                            }
                        }
                        ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="8"></th>
                                <th> <? echo $po_qty?></th>
                                <th></th>
                                <th><? echo $po_total_price?></th>
                                <th colspan="5"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>                   
            <?
            exit();
        }

        if ($action_types == 'order_select_popup') 
        {
            if($order_form_sc)
            {
                $attach_sc_sql="select com_sales_contract_id from com_export_lc_atch_sc_info where status_active=1 and is_deleted=0 and com_export_lc_id='".$data[6]."' ";
                //echo $attach_sc_sql;die;
                $attach_sc_sql_result=sql_select($attach_sc_sql);
                if(count($attach_sc_sql_result)<1)
                {
                    echo "No Sales Contact Found";die;
                }
                else
                {
                    
                    foreach($attach_sc_sql_result as $val)
                    {
                        $sc_ids[$val[csf("com_sales_contract_id")]]=$val[csf("com_sales_contract_id")];
                    }
                    $sql = "SELECT wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst, wb.file_no, wm.job_no_prefix_num, $select_field wm.style_ref_no, wm.gmts_item_id, wb.unit_price, wb.sc_lc ,wb.grouping, wm.brand_id, sum(a.attached_qnty) as attached_qnty 
                    FROM com_sales_contract_order_info a, wo_po_break_down wb, wo_po_details_master wm 
                    WHERE a.wo_po_break_down_id=wb.id and wb.job_no_mst = wm.job_no  and wm.buyer_name like '$buyer_id' $selected_order_id $company $search_text $file_no_cond $txt_sc_lc_cond $user_buyer $user_brand $year_field_cond and wb.is_deleted = 0 AND wb.status_active = 1 and wb.is_confirmed=1 $date and a.com_sales_contract_id in(".implode(",",$sc_ids).") and a.status_active=1
                    group by wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date, wb.job_no_mst, wb.file_no, wm.job_no_prefix_num, wm.insert_date, wm.style_ref_no, wm.gmts_item_id, wb.unit_price, wb.sc_lc ,wb.grouping, wm.brand_id ";
                    //echo $sql;die;
                }
            }
            else
            {
				$lc_sc_cond="";
				if($check_related_order && $lc_sc_no!="") $lc_sc_cond=" and wb.sc_lc='$lc_sc_no'";
                if($cbo_export_item_category==10)
                {
                    $sql = "SELECT wm.id, wm.job_no as po_number, sum(wb.amount) as po_total_price, sum(wb.finish_qty) as po_quantity, wm.delivery_date as shipment_date, wb.job_no_mst, 0 as file_no, wm.job_no_prefix_num, $select_field wm.style_ref_no, 0 as gmts_item_id, avg(wb.avg_rate) as unit_price, 0 as sc_lc 
                    FROM fabric_sales_order_mst wm, fabric_sales_order_dtls wb  
                    WHERE wm.id = wb.mst_id and wm.buyer_id like '$buyer_id' and wb.is_deleted = 0 AND wb.status_active = 1 and wm.is_deleted = 0 AND wm.status_active = 1 and wm.within_group=2 $selected_order_id $company $search_text $file_no_cond $txt_sc_lc_cond $date $year_field_cond
                    group by  wm.id, wm.job_no, wm.delivery_date, wb.job_no_mst, wm.job_no_prefix_num, wm.insert_date, wm.style_ref_no";
                }
                elseif($cbo_export_item_category==23 || $cbo_export_item_category==45)
                {
                    if($db_type==0)
                    {
                        $export_pi_arr = return_library_array("SELECT group_concat(a.pi_number) as pi_number, b.work_order_id from com_export_pi_mst a, com_export_pi_dtls b where a.id=b.pi_id and a.item_category_id in(23,45) and a.status_active=1 and b.status_active=1 group by b.work_order_id", "work_order_id", "pi_number");
                    }
                    else
                    {
                        $export_pi_arr = return_library_array("SELECT listagg(a.pi_number,',') within group (order by a.id) as pi_number, b.work_order_id from com_export_pi_mst a, com_export_pi_dtls b where a.id=b.pi_id and a.item_category_id in(23,45) and a.status_active=1 and b.status_active=1 group by b.work_order_id", "work_order_id", "pi_number");
                    }

                    $sql = "SELECT wb.id, wm.order_no as po_number, sum(wb.amount) as po_total_price, sum(wb.order_quantity) as po_quantity, wm.delivery_date as shipment_date, wb.job_no_mst, 0 as file_no, wm.job_no_prefix_num, $select_field wb.cust_style_ref as style_ref_no, 0 as gmts_item_id, avg(wb.rate) as unit_price, 0 as sc_lc 
                    FROM subcon_ord_mst wm, subcon_ord_dtls wb  
                    WHERE wm.subcon_job = wb.job_no_mst and wm.party_id like '$buyer_id' and wm.within_group=2 and wm.entry_form = ".$cat_wise_entry_form[$cbo_export_item_category]." and wb.is_deleted = 0 AND wb.status_active = 1 and wm.is_deleted = 0 AND wm.status_active = 1 $selected_order_id $company $search_text $file_no_cond $txt_sc_lc_cond $date $year_field_cond
                    group by  wb.id, wm.order_no, wm.delivery_date, wb.job_no_mst, wm.job_no_prefix_num, wm.insert_date, wb.cust_style_ref,wb.buyer_style_ref";
					//echo $sql;
                    
                }
                elseif($cbo_export_item_category==37)
                {
                    $sql = "SELECT wm.id, wm.subcon_job as po_number, sum(wb.amount) as po_total_price, sum(wb.order_quantity) as po_quantity, wm.delivery_date as shipment_date, wb.job_no_mst, 0 as file_no, wm.job_no_prefix_num, $select_field wb.cust_style_ref as style_ref_no , wb.buyer_style_ref, 0 as gmts_item_id, avg(wb.rate) as unit_price, 0 as sc_lc 
                    FROM subcon_ord_mst wm, subcon_ord_dtls wb 
                    WHERE wm.subcon_job = wb.job_no_mst and wm.party_id like '$buyer_id' and wm.within_group=2 and wm.entry_form = ".$cat_wise_entry_form[$cbo_export_item_category]." and wb.is_deleted = 0 AND wb.status_active = 1 and wm.is_deleted = 0 AND wm.status_active = 1 $selected_order_id $company $search_text $file_no_cond $txt_sc_lc_cond $date $year_field_cond
                    group by  wm.id, wm.subcon_job, wm.delivery_date, wb.job_no_mst, wm.job_no_prefix_num, wm.insert_date, wb.cust_style_ref,wb.buyer_style_ref";
        
                    /*$sql = "SELECT wm.id, wm.subcon_job as po_number, sum(wb.amount) as po_total_price, sum(wb.order_quantity) as po_quantity, wm.delivery_date as shipment_date, wb.job_no_mst, 0 as file_no, wm.job_no_prefix_num, $select_field wb.cust_style_ref as style_ref_no , wb.buyer_style_ref, 0 as gmts_item_id, avg(wb.rate) as unit_price, 0 as sc_lc 
                    FROM subcon_ord_mst wm, subcon_ord_dtls wb , subcon_ord_breakdown wc 
                    WHERE wm.subcon_job = wb.job_no_mst and wm.party_id like '$buyer_id' and wm.subcon_job=wc.job_no_mst and wb.id=wc.mst_id and wm.within_group=2 and wm.entry_form = ".$cat_wise_entry_form[$cbo_export_item_category]." and wb.is_deleted = 0 AND wb.status_active = 1 and wm.is_deleted = 0 AND wm.status_active = 1 $selected_order_id $company $search_text $file_no_cond $txt_sc_lc_cond $date 
                    group by  wm.id, wm.subcon_job, wm.delivery_date, wb.job_no_mst, wm.job_no_prefix_num, wm.insert_date, wb.cust_style_ref,wb.buyer_style_ref";*/
                    
                }
                elseif($cbo_export_item_category==35 || $cbo_export_item_category==36)
                {
                    $sql = "SELECT wm.id, wm.embellishment_job as po_number, sum(wb.amount) as po_total_price, sum(wb.order_quantity) as po_quantity, wm.delivery_date as shipment_date, wb.job_no_mst, 0 as file_no, wm.job_no_prefix_num, $select_field wb.cust_style_ref as style_ref_no, 0 as gmts_item_id, avg(wb.rate) as unit_price, 0 as sc_lc 
                    FROM fabric_sales_order_mst wm, fabric_sales_order_dtls wb  
                    WHERE wm.subcon_job = wb.job_no_mst and wm.party_id like '$buyer_id' and wm.within_group=2 and wm.entry_form = ".$cat_wise_entry_form[$cbo_export_item_category]." and wb.is_deleted = 0 AND wb.status_active = 1 and wm.is_deleted = 0 AND wm.status_active = 1 $selected_order_id $company $search_text $file_no_cond $txt_sc_lc_cond $date $year_field_cond
                    group by  wm.id, wm.embellishment_job, wm.delivery_date, wb.job_no_mst, wm.job_no_prefix_num, wm.insert_date, wb.cust_style_ref";
                    /*$sql = "SELECT wb.id, wb.order_no as po_number, wb.amount as po_total_price, wb.order_quantity as po_quantity, wb.delivery_date as shipment_date, wb.job_no_mst, null as file_no, wm.job_no_prefix_num, $select_field wb.cust_style_ref as style_ref_no, 0 as gmts_item_id, wb.rate as unit_price, null as sc_lc  
                    FROM subcon_ord_dtls wb, subcon_ord_mst wm 
                    WHERE wb.job_no_mst = wm.embellishment_job and wm.party_id like '$buyer_id' and wm.within_group=2 and wm.entry_form = ".$cat_wise_entry_form[$cbo_export_item_category]." $company $search_text $file_no_cond $txt_sc_lc_cond and wb.is_deleted = 0 AND wb.status_active = 1 and wb.is_confirmed=1 $date";*/
                }
                else
                {
                    $sql = "SELECT wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst, wb.file_no, wm.job_no_prefix_num, $select_field wm.style_ref_no, wm.gmts_item_id, wb.unit_price, wb.sc_lc ,wb.grouping, wm.brand_id 
                    FROM wo_po_break_down wb, wo_po_details_master wm 
                    WHERE wb.job_no_mst = wm.job_no and wm.buyer_name like '$buyer_id' $selected_order_id $company $search_text $file_no_cond $txt_sc_lc_cond $user_buyer $user_brand $year_field_cond and wb.is_deleted = 0 AND wb.status_active = 1 and wb.is_confirmed=1 $date $lc_sc_cond
                    group by wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date, wb.job_no_mst, wb.file_no, wm.job_no_prefix_num, wm.insert_date, wm.style_ref_no, wm.gmts_item_id, wb.unit_price, wb.sc_lc ,wb.grouping, wm.brand_id ";
                }
            }
            
            
            //echo $sql."<br>"; //die;
            $brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand brand",'id','brand_name');
            ?>
            <div>
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1150" style="margin-left:1px" class="rpt_table" >
                    <thead>
                    <th width="30">SL</th>
                    <th width="100">JOB/PO No</th>
                    <th width="100">Internal Ref</th>
                    <th width="100">Act PO No</th>
                    <th width="30">Year</th>
                    <th width="30">Job No</th>
                    <th width="90">Brand</th>
                    <th width="130">Item</th>
                    <th width="110">Style No</th>
                    <th width="70">PO Quantity</th>
                    <th width="50">Rate</th>
                    <th width="80">Price</th>
                    <th width="60">Shipment Date</th>
                    <th width="70">File No</th>
                    <th>SC/LC No</th>
                    </thead>
                </table>
                <div style="width:1170px; overflow-y:scroll; max-height:220px;" id="buyer_list_view" align="center">
                    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1150" class="rpt_table" id="tbl_list_search" align="left" >
                        <!--<td width="100"><p><?// echo implode(", ",array_unique(explode(",",chop($export_pi_arr[$selectResult['ID']],',')))) ?></p></td>-->
						<?
                        $i = 1;
                        if($import_btb == 1)
                        {
                            $is_sales_cond=" and is_sales=1";
                            $is_sales=1;
                        }
                        else 
                        {
                            $is_sales_cond=" and is_sales=0";
                            $is_sales=0;
                        }
						
						$sc_attached_qnty_arr=array();
						
						if($cbo_export_item_category==23 || $cbo_export_item_category==37 || $cbo_export_item_category==45 || $cbo_export_item_category==35 || $cbo_export_item_category==36)
						{
							$lc_attached_qnty_arr = return_library_array("select wo_po_break_down_id, sum(attached_qnty) as qty from com_export_lc_order_info where status_active = 1 and is_deleted=0 and com_export_lc_id>0 and is_service=1 and EXPORT_ITEM_CATEGORY=$cbo_export_item_category $is_sales_cond group by wo_po_break_down_id", "wo_po_break_down_id", "qty");
						}
						else
						{
							$lc_attached_qnty_arr = return_library_array("select wo_po_break_down_id, sum(attached_qnty) as qty from com_export_lc_order_info where status_active = 1 and is_deleted=0 and com_export_lc_id>0 and is_service<>1 and EXPORT_ITEM_CATEGORY=$cbo_export_item_category $is_sales_cond group by wo_po_break_down_id", "wo_po_break_down_id", "qty");
							$sc_attached_qnty_arr = return_library_array("select wo_po_break_down_id, sum(attached_qnty) as qty from com_sales_contract_order_info where status_active = 1 and is_deleted=0 and com_sales_contract_id>0 $is_sales_cond group by wo_po_break_down_id", "wo_po_break_down_id", "qty");
						}
                        

                        $nameArray = sql_select($sql);
                        foreach ($nameArray as $selectResult) 
                        {
                            if ($i % 2 == 0)
                                $bgcolor = "#E9F3FF";
                            else
                                $bgcolor = "#FFFFFF";

                            $lc_attached_qnty = $lc_attached_qnty_arr[$selectResult[csf('id')]];
                            $sc_attached_qnty = $sc_attached_qnty_arr[$selectResult[csf('id')]];
                            if($order_form_sc)
                            {
                                $order_attached_qnty = $lc_attached_qnty;
                                $attach_qnty=$selectResult[csf('attached_qnty')];						}
                            else
                            {
                                $order_attached_qnty = $sc_attached_qnty + $lc_attached_qnty;
                                $attach_qnty=0;
                            }
                            

                            if ($order_attached_qnty < $selectResult[csf('po_quantity')]) 
                            {
                                if($cbo_export_item_category==37){
                                    $selectResult[csf('style_ref_no')]=$selectResult[csf('buyer_style_ref')];
                                }
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)"> 
                                    <td width="30" align="center"><? echo "$i"; ?>
                                        <input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>"/>
                                        <input type="hidden" name="txt_individual_ids" id="txt_individual_ids<? echo $i ?>" value="<? echo $selectResult[csf('id')]."_".$i; ?>"/>	
                                        <input type="hidden" name="txt_is_sales" id="txt_is_sales<? echo $i ?>" value="<? echo $is_sales; ?>"/>	
                                    </td>	
                                    <td width="100"><p><? echo $selectResult[csf('po_number')]; ?></p></td>                                   
                                    <td width="100"><p><? echo $selectResult[csf('grouping')]; ?></p></td>
                                    <td width="100"><p><? echo $actual_po_arr[$selectResult[csf('id')]]; ?></p></td>
                                    <td width="30" align="center"><p><? echo $selectResult[csf('year')]; ?></p></td>
                                    <td width="30" align="center"><p><? echo $selectResult[csf('job_no_prefix_num')]; ?></p></td>
                                    <td width="90" align="center"><p><? echo $brand_arr[$selectResult[csf('brand_id')]]; ?></p></td>
                                    <td width="130">
                                        <p>
                                            <?
                                            $gmts_item = '';
                                            $gmts_item_id = explode(",", $selectResult[csf('gmts_item_id')]);
                                            foreach ($gmts_item_id as $item_id) {
                                                if ($gmts_item == "")
                                                    $gmts_item = $garments_item[$item_id];
                                                else
                                                    $gmts_item .= "," . $garments_item[$item_id];
                                            }
                                            echo $gmts_item;
                                            ?>
                                        </p>
                                    </td> 
                                    <td width="110"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
                                    <td width="70" align="right"><? echo $selectResult[csf('po_quantity')]; ?></td>
                                    <td width="50" align="right"><? echo number_format(($selectResult[csf('po_total_price')]/$selectResult[csf('po_quantity')]), 2); ?></td>
                                    <td width="80" align="right"><? echo number_format($selectResult[csf('po_total_price')], 2); ?></td>
                                    <td align="center" width="60"><? echo change_date_format($selectResult[csf('shipment_date')]); ?></td>
                                    <td width="70"><p><? echo $selectResult[csf('file_no')]; ?>&nbsp;</p></td>	
                                    <td><p><? echo $selectResult[csf('sc_lc')]; ?>&nbsp;</p></td>	
                                </tr>
                                <?
                                $i++;
                            }
                        }
                        ?>
                    </table>
                </div>
                <table width="1150" cellspacing="0" cellpadding="0" style="border:none" align="center">
                    <tr>
                        <td align="center" height="30" valign="bottom">
                            <div style="width:100%"> 
                                <div style="width:50%; float:left" align="left">
                                    <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                                </div>
                                <div style="width:50%; float:left" align="left">
                                    <input type="button" name="close" onClick="fn_close('<? echo $order_form_sc."__".implode(",",$sc_ids); ?>')" class="formbutton" value="Close" style="width:100px" />
                                </div>
                            </div>
                        </td>
                    </tr>

                </table>
            </div>
            <?
        }
    }
    
    exit();
}

if ($action == "show_po_active_listview") {
	$data_ref=explode("_",$data);
	$replace_lc=$data_ref[2];
	$is_sales=return_field_value("is_sales","com_export_lc_order_info","com_export_lc_id='$data_ref[0]' and status_active=1","is_sales");
	// echo $is_sales.test;die;
	if($is_sales==0)
	{
		if($data_ref[1]==23 || $data_ref[1]==35 || $data_ref[1]==36 || $data_ref[1]==37 || $data_ref[1]==45 || $data_ref[1]==67)
		{
			/*$sql = "SELECT wb.id, wb.order_no as po_number, wb.amount as po_total_price, wb.order_quantity as po_quantity, wb.delivery_date as shipment_date, wb.job_no_mst, null as file_no, wm.job_no_prefix_num, $select_field wb.cust_style_ref as style_ref_no, 0 as gmts_item_id, wb.rate as unit_price, null as sc_lc 
			FROM subcon_ord_dtls wb, subcon_ord_mst wm 
			WHERE wb.job_no_mst = wm.subcon_job and wm.party_id like '$buyer_id' and wm.within_group=2 and wm.entry_form = ".$cat_wise_entry_form[$cbo_export_item_category]." $company $search_text $file_no_cond $txt_sc_lc_cond and wb.is_deleted = 0 AND wb.status_active = 1 and wb.is_confirmed=1 $date";*/
			if($data_ref[1]==35 || $data_ref[1]==36)
			{
				$sql = "SELECT wm.id, ci.id as idd, 0 as gmts_item_id, wm.embellishment_job as po_number, sum(wb.amount) as po_total_price, sum(wb.order_quantity) as po_quantity, wm.delivery_date as shipment_date, wb.job_no_mst, wb.cust_style_ref as style_ref_no, wb.order_uom, 1 as ratio, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active 
				from subcon_ord_dtls wb, subcon_ord_mst wm, com_export_lc_order_info ci 
				where wb.job_no_mst = wm.embellishment_job and wb.id=ci.wo_po_break_down_id and ci.com_export_lc_id='$data_ref[0]' and ci.status_active = '1' and ci.is_deleted = '0'
				group by  wm.id, ci.id, wm.embellishment_job, wm.delivery_date, wb.job_no_mst, wb.cust_style_ref, wb.order_uom, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active 
				order by ci.id";
			}
			else
			{
				$sql = "SELECT wb.id, ci.id as idd, 0 as gmts_item_id, wb.order_no as po_number, sum(wb.amount) as po_total_price, sum(wb.order_quantity) as po_quantity, wm.delivery_date as shipment_date, wb.job_no_mst, wb.cust_style_ref as style_ref_no, wb.order_uom, 1 as ratio, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active 
				from subcon_ord_dtls wb, subcon_ord_mst wm, com_export_lc_order_info ci 
				where wb.job_no_mst = wm.subcon_job and wb.id=ci.wo_po_break_down_id and ci.com_export_lc_id='$data_ref[0]' and ci.status_active = '1' and ci.is_deleted = '0'
				group by  wb.id, ci.id, wb.order_no, wm.delivery_date, wb.job_no_mst, wb.cust_style_ref, wb.order_uom, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active 
				order by ci.id";
			}

			
		}
		else
		{
			if ($db_type == 0) {
			$actual_po_arr = return_library_array("select po_break_down_id, group_concat(acc_po_no) as acc_po_no from wo_po_acc_po_info where status_active=1 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "acc_po_no");
			} else {
				$actual_po_arr = return_library_array("select po_break_down_id, listagg( cast(acc_po_no as varchar(4000)), ',') within group(order by acc_po_no) as acc_po_no from wo_po_acc_po_info where status_active=1 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "acc_po_no");
			}
		
			$sql = "select wb.id, ci.id as idd, wm.gmts_item_id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst, wm.style_ref_no, wm.order_uom, wm.total_set_qnty as ratio, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active, wm.brand_id, wb.shiping_status 
			from wo_po_break_down wb, wo_po_details_master wm, com_export_lc_order_info ci where wb.job_no_mst = wm.job_no and wb.id=ci.wo_po_break_down_id and ci.com_export_lc_id='$data_ref[0]' and ci.status_active = '1' and ci.is_deleted = '0' order by ci.id";
		}
		
	}
	else
	{
		$sql = "SELECT wm.id, ci.id as idd, 0 as gmts_item_id, wm.job_no as po_number, sum(wb.amount) as po_total_price, sum(wb.finish_qty) as po_quantity, wm.delivery_date as shipment_date, wb.job_no_mst, wm.style_ref_no, wb.order_uom, 1 as ratio, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active 
		from fabric_sales_order_dtls wb, fabric_sales_order_mst wm, com_export_lc_order_info ci 
		where wm.id = wb.mst_id and wm.id=ci.wo_po_break_down_id and ci.com_export_lc_id='$data_ref[0]' and ci.status_active = '1' and ci.is_deleted = '0'
		group by  wm.id, ci.id, wm.job_no, wm.delivery_date, wb.job_no_mst, wm.style_ref_no, wb.order_uom, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active 
		order by ci.id";
		/*$sql = "SELECT wm.id, ci.id as idd, 0 as gmts_item_id, wm.job_no as po_number, sum(wb.amount) as po_total_price, sum(wb.finish_qty) as po_quantity, wm.delivery_date as shipment_date, wb.job_no_mst, wm.style_ref_no, wb.order_uom, 1 as ratio, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active 
		from fabric_sales_order_dtls wb, fabric_sales_order_mst wm, com_export_lc_order_info ci 
		where wm.id = wb.mst_id and wb.mst_id=ci.wo_po_break_down_id and ci.com_export_lc_id='$data_ref[0]' and ci.status_active = '1' and ci.is_deleted = '0'
		group by  wm.id, ci.id, wm.job_no, wm.delivery_date, wb.job_no_mst, wm.style_ref_no, wb.order_uom, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active 
		order by ci.id";*/
	}
    // echo $sql;
    //echo $sql;SHIPING_STATUS
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand brand",'id','brand_name');
    ?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1330" class="rpt_table" >
            <thead>
            <th width="100">Order Number</th>
            <th width="100">Acc.PO No.</th>
            <th width="80">Order Qty</th>
            <th width="100">Order Value</th>
            <th width="80">Attached Qty</th>
            <th width="50">UOM</th>
            <th width="60">Rate</th>
            <th width="100">Attached Value</th>
            <th width="100">Attached Qty (Pcs)</th>
            <th width="120">Style Ref</th>
            <th width="120">Gmts. Item</th>
            <th width="80">Job No</th>
            <th width="65">Brand</th>
            <th width="50">Status</th>
            <th width="60">Ship Status</th>
            <th><input type="checkbox" id="chkOrd_th" name="chkOrd_th" onClick="fn_all_chk();"/></th>
            </thead>
        </table>
        <div style="width:1330px; overflow-y:scroll; max-height:200px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1312" class="rpt_table" id="po_active_list" >
                <?
                $i = 1;
                $total_attc_value = 0;
                $total_order_qnty_in_pcs = 0;
                $total_order_qty = 0;
                $total_order_value = 0;
                $nameArray = sql_select($sql);
                foreach ($nameArray as $selectResult) {
                    if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";

                    $order_qnty_in_pcs = $selectResult[csf('attached_qnty')] * $selectResult[csf('ratio')];
                    $total_order_qnty_in_pcs += $order_qnty_in_pcs;
                    $total_attc_value += $selectResult[csf('attached_value')];
                    $total_order_qty += $selectResult[csf('po_quantity')];
                    $total_order_value += $selectResult[csf('po_total_price')];
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="get_php_form_data('<? echo $selectResult[csf('idd')]."_".$is_sales."_".$data_ref[1]."_".$replace_lc; ?>', 'populate_order_details_form_data', 'requires/export_lc_controller')"> 
                        <td width="100"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
                        <td width="100"><p><? echo $actual_po_arr[$selectResult[csf('id')]]; ?></p></td>
                        <td width="80" align="right"><? echo $selectResult[csf('po_quantity')]; ?></td>
                        <td width="100" align="right"><? echo number_format($selectResult[csf('po_total_price')], 2); ?></td>
                        <td width="80" align="right"><? echo $selectResult[csf('attached_qnty')]; ?></td>
                        <td width="50" align="center"><? echo $unit_of_measurement[$selectResult[csf('order_uom')]]; ?></td>
                        <td width="60" align="right"><? echo number_format($selectResult[csf('attached_rate')], 2); ?></td>
                        <td width="100" align="right"><? echo number_format($selectResult[csf('attached_value')], 2); ?></td>
                        <td width="100" align="right"><? echo $order_qnty_in_pcs; ?></td>
                        <td width="120"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
                        <td width="120">
                            <p>
                                <?
                                $gmts_item = '';
                                $gmts_item_id = explode(",", $selectResult[csf('gmts_item_id')]);
                                foreach ($gmts_item_id as $item_id) {
                                    if ($gmts_item == "")
                                        $gmts_item = $garments_item[$item_id];
                                    else
                                        $gmts_item .= "," . $garments_item[$item_id];
                                }
                                echo $gmts_item;
                                ?>
                            </p>
                        </td> 
                        <td width="80"><? echo $selectResult[csf('job_no_mst')]; ?></td>
                        <td width="65"><? echo $brand_arr[$selectResult[csf('brand_id')]]; ?></td>
                        <td width="50"><? echo $attach_detach_array[$selectResult[csf('status_active')]]; ?></td>	
                        <td width="60"><? echo $shipment_status[$selectResult[csf('shiping_status')]]; ?></td>	
                        <td align="center"><input type="checkbox" id="chkOrd_<?=$i;?>" name="chkOrd[]" value="<? echo $selectResult[csf('idd')]; ?>" /></td>
                    </tr>
                    <?
                    $i++;
                }
                ?>
            </table>
        </div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1330" class="rpt_table">
            <tfoot>
            <th width="100">&nbsp;</th>
            <th width="100">Total</th>
            <th width="80" align="right"><? echo number_format($total_order_qty, 0); ?></th>
            <th width="100" align="right"><? echo number_format($total_order_value, 2); ?></th>
            <th width="80">&nbsp;</th>
            <th width="50">&nbsp;</th>
            <th width="60">&nbsp;</th>
            <th width="100" align="right"><? echo number_format($total_attc_value, 2); ?></th>
            <th width="100" align="right"><? echo number_format($total_order_qnty_in_pcs, 0); ?></th>
            <th width="120">&nbsp;</th>
            <th width="120">&nbsp;</th>
            <th width="80">&nbsp;</th>
            <th width="65">&nbsp;</th>
            <th width="50">&nbsp;</th>
            <th colspan="2"><input type="button" style="width:100px;" class="formbutton" value="Submit List" onClick="fn_submit_order_list(<?= $is_sales;?>)" /></th>
            </tfoot>
        </table>
    </div> 
    <?
    exit();
}

if ($action == "populate_order_details_form_data") {
	$data_ref=explode("_",$data);
	$lc_attch_id=$data_ref[0];
	$is_sales=$data_ref[1];
	$export_item_cateogry=$data_ref[2];
	$replacement_lc=$data_ref[3];
	if($is_sales==0)
	{
		if($export_item_cateogry==23 || $export_item_cateogry==37 || $export_item_cateogry==45 || $export_item_cateogry==67)
		{
			
			/*$sql = "select wm.id, ci.id as idd, 0 as gmts_item_id, wm.subcon_job as po_number, sum(wb.amount) as po_total_price, sum(wb.order_quantity) as po_quantity, wm.delivery_date as shipment_date, wb.job_no_mst, wb.cust_style_ref as style_ref_no, wb.order_uom, 1 as ratio, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active 
				from subcon_ord_dtls wb, subcon_ord_mst wm, com_export_lc_order_info ci 
				where wb.job_no_mst = wm.subcon_job and wm.id=ci.wo_po_break_down_id and ci.com_export_lc_id='$data_ref[0]' and ci.status_active = '1' and ci.is_deleted = '0'
				group by  wm.id, ci.id, wm.subcon_job, wm.delivery_date, wb.job_no_mst, wb.cust_style_ref, wb.order_uom, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active 
				order by ci.id";*/
			$data_array = sql_select("SELECT wb.id, ci.id as idd, 0 as style_description, wb.order_no as po_number, sum(wb.amount) as po_total_price, sum(wb.order_quantity) as po_quantity, wm.delivery_date as shipment_date, wb.job_no_mst, wb.cust_style_ref as style_ref_no, 0 as gmts_item_id, avg(wb.rate) as unit_price, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.com_export_lc_id, ci.fabric_description, ci.category_no, ci.hs_code, ci.status_active, ci.is_service,ci.commission, ci.commission_foreign 
			from subcon_ord_dtls wb, subcon_ord_mst wm, com_export_lc_order_info ci 
			where wb.job_no_mst = wm.subcon_job and wb.id=ci.wo_po_break_down_id and ci.id='$lc_attch_id' and ci.status_active = '1' and ci.is_deleted = '0'
			group by  wb.id, ci.id, wb.order_no, wm.delivery_date, wb.job_no_mst, wb.cust_style_ref, wb.order_uom, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.com_export_lc_id, ci.fabric_description, ci.category_no, ci.hs_code, ci.status_active, ci.is_service, ci.commission, ci.commission_foreign  
			order by ci.id");
		}
		elseif($export_item_cateogry==35 || $export_item_cateogry==36)
		{
			/*$sql = "select wm.id, ci.id as idd, 0 as gmts_item_id, wm.embellishment_job as po_number, sum(wb.amount) as po_total_price, sum(wb.order_quantity) as po_quantity, wm.delivery_date as shipment_date, wb.job_no_mst, wb.cust_style_ref as style_ref_no, wb.order_uom, 1 as ratio, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active 
				from subcon_ord_dtls wb, subcon_ord_mst wm, com_export_lc_order_info ci 
				where wb.job_no_mst = wm.embellishment_job and wm.id=ci.wo_po_break_down_id and ci.com_export_lc_id='$data_ref[0]' and ci.status_active = '1' and ci.is_deleted = '0'
				group by  wm.id, ci.id, wm.embellishment_job, wm.delivery_date, wb.job_no_mst, wb.cust_style_ref, wb.order_uom, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active 
				order by ci.id";*/
			$data_array = sql_select("SELECT wb.id, ci.id as idd, 0 as style_description, wm.embellishment_job as po_number, sum(wb.amount) as po_total_price, sum(wb.order_quantity) as po_quantity, wm.delivery_date as shipment_date, wb.job_no_mst, wb.cust_style_ref as style_ref_no, 0 as gmts_item_id, avg(wb.rate) as unit_price, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.com_export_lc_id, ci.fabric_description, ci.category_no, ci.hs_code, ci.status_active, ci.is_service,ci.commission, ci.commission_foreign 
			from fabric_sales_order_dtls wb, fabric_sales_order_mst wm, com_export_lc_order_info ci 
			where wb.job_no_mst = wm.embellishment_job and wb.mst_id=ci.wo_po_break_down_id and ci.id='$lc_attch_id' and ci.status_active = '1' and ci.is_deleted = '0'
			group by  wb.id, ci.id, wm.embellishment_job, wm.delivery_date, wb.job_no_mst, wb.cust_style_ref, wb.order_uom, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.com_export_lc_id, ci.fabric_description, ci.category_no, ci.hs_code, ci.status_active, ci.is_service, ci.commission , ci.commission_foreign 
			order by ci.id");
		}
		else
		{
			$comishion_amount_array=array();
			$sql_data=sql_select("SELECT particulars_id, commission_amount, job_no from wo_pre_cost_commiss_cost_dtls where STATUS_ACTIVE=1 and particulars_id= 1 
			union all 

			SELECT particulars_id, commission_amount, job_no from wo_pre_cost_commiss_cost_dtls where STATUS_ACTIVE=1 and particulars_id= 2");
			foreach($sql_data as $row){
				if($row[csf("particulars_id")]==1){
					$comishion_amount_array[$row[csf("job_no")]]["Foreign_ammount"]=$row[csf("commission_amount")];
				}else{
					$comishion_amount_array[$row[csf("job_no")]]["local_amount"]=$row[csf("commission_amount")];
				}
			}
			
			if ($db_type == 0) {
			$actual_po_arr = return_library_array("select po_break_down_id, group_concat(acc_po_no) as acc_po_no from wo_po_acc_po_info where status_active=1 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "acc_po_no");
			} else {
				$actual_po_arr = return_library_array("select po_break_down_id, listagg( cast(acc_po_no as varchar(4000)), ',') within group(order by acc_po_no) as acc_po_no from wo_po_acc_po_info where status_active=1 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "acc_po_no");
			}
		
			$data_array = sql_select("SELECT wb.id, ci.id as idd, wm.style_description, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst, wm.style_ref_no, wm.gmts_item_id, wb.unit_price,ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active, ci.com_export_lc_id, ci.fabric_description, ci.category_no, ci.hs_code, ci.is_service,ci.commission, ci.commission_foreign 
			from wo_po_break_down wb, wo_po_details_master wm, com_export_lc_order_info ci 
			where wb.job_no_mst = wm.job_no and wb.id=ci.wo_po_break_down_id and ci.id='$lc_attch_id' and ci.status_active = '1' and ci.is_deleted = '0' order by ci.id");
		}
	}
	else
	{
		/*$data_array = sql_select("select wb.id, ci.id as idd, wm.style_description, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst, wm.style_ref_no, wm.gmts_item_id, wb.unit_price,ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active, ci.com_export_lc_id, ci.fabric_description, ci.category_no, ci.hs_code 
		from wo_po_break_down wb, wo_po_details_master wm, com_export_lc_order_info ci 
		where wb.job_no_mst = wm.job_no and wb.id=ci.wo_po_break_down_id and ci.id='$data' and ci.status_active = '1' and ci.is_deleted = '0' order by ci.id");*/
		$data_array = sql_select("SELECT wm.id, ci.id as idd, 0 as style_description, wm.job_no as po_number, sum(wb.amount) as po_total_price, sum(wb.finish_qty) as po_quantity, wm.delivery_date as shipment_date, wb.job_no_mst, wm.style_ref_no, 0 as gmts_item_id, avg(wb.avg_rate) as unit_price, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.com_export_lc_id, ci.fabric_description, ci.category_no, ci.hs_code, ci.status_active, ci.is_service,ci.commission, ci.commission_foreign
		from fabric_sales_order_dtls wb, fabric_sales_order_mst wm, com_export_lc_order_info ci 
		where wm.id = wb.mst_id and wb.mst_id=ci.wo_po_break_down_id and ci.id='$lc_attch_id' and ci.status_active = '1' and ci.is_deleted = '0'
		group by  wm.id, ci.id, wm.job_no, wm.delivery_date, wb.job_no_mst, wm.style_ref_no, wb.order_uom, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.com_export_lc_id, ci.fabric_description, ci.category_no, ci.hs_code, ci.status_active, ci.is_service,ci.commission, ci.commission_foreign
		order by ci.id");
	}
    

    foreach ($data_array as $row) {
        echo "$('#tbl_order_list tbody tr:not(:first)').remove();\n";

        $gmts_item = '';
        $gmts_item_id = explode(",", $row[csf('gmts_item_id')]);
        foreach ($gmts_item_id as $item_id) {
            if ($gmts_item == "")
                $gmts_item = $garments_item[$item_id];
            else
                $gmts_item .= "," . $garments_item[$item_id];
        }
        echo "document.getElementById('txtordernumber_1').value 			= '" . $row[csf("po_number")] . "';\n";
		echo "document.getElementById('hiddenwopobreakdownid_1').value 		= '" . $row[csf("id")] . "';\n";
		echo "document.getElementById('isSales_1').value 					= '" . $is_sales . "';\n";
		echo "document.getElementById('isService_1').value 					= '" . $row[csf("is_service")]. "';\n";
        echo "document.getElementById('txtaccordernumber_1').value 			= '" . $actual_po_arr[$row[csf("id")]] . "';\n";
        echo "document.getElementById('txtorderqnty_1').value 				= '" . $row[csf("po_quantity")] . "';\n";
        echo "document.getElementById('txtordervalue_1').value 				= '" . $row[csf("po_total_price")] . "';\n";
        echo "document.getElementById('txtattachedqnty_1').value 			= '" . $row[csf("attached_qnty")] . "';\n";
        echo "document.getElementById('hideattachedqnty_1').value 			= '" . $row[csf("attached_qnty")] . "';\n";
        echo "document.getElementById('hiddenunitprice_1').value 			= '" . $row[csf("attached_rate")] . "';\n";
        echo "document.getElementById('txtattachedvalue_1').value 			= '" . $row[csf("attached_value")] . "';\n";
        echo "document.getElementById('txtstyleref_1').value 				= '" . $row[csf("style_ref_no")] . "';\n";
        echo "document.getElementById('txtStyleDesc_1').value 				= '" . $row[csf("style_description")] . "';\n";
        echo "document.getElementById('txtitemname_1').value 				= '" . $gmts_item . "';\n";
        echo "document.getElementById('txtjobno_1').value 					= '" . $row[csf("job_no_mst")] . "';\n";
        echo "document.getElementById('cbopostatus_1').value 				= '" . $row[csf("status_active")] . "';\n";
        echo "document.getElementById('txcommission_1').value 				= '" . $comishion_amount_array[$row[csf("job_no_mst")]]["local_amount"] . "';\n";
        echo "document.getElementById('txcommissionforain_1').value 		= '" . $comishion_amount_array[$row[csf("job_no_mst")]]["Foreign_ammount"] . "';\n";
		
		//if($replacement_lc==1)
		//{
			//echo "$(cbopostatus_1).attr('disabled',true);\n";
		//}
		
        echo "document.getElementById('txtfabdescrip_1').value 				= '" . $row[csf("fabric_description")] . "';\n";
        echo "document.getElementById('txtcategory_1').value 				= '" . $row[csf("category_no")] . "';\n";
        echo "document.getElementById('txthscode_1').value 				= '" . $row[csf("hs_code")] . "';\n";

        echo "document.getElementById('hiddenwopobreakdownid_1').value 	= '" . $row[csf("id")] . "';\n";
        echo "document.getElementById('hiddenexportlcorderid_1').value 	= '" . $row[csf("idd")] . "';\n";
        echo "document.getElementById('txt_tot_row').value 	= '1';\n";

        echo "math_operation( 'totalOrderqnty', 'txtorderqnty_', '+', 1 );\n";
        echo "math_operation( 'totalOrdervalue', 'txtordervalue_', '+', 1 );\n";
        echo "math_operation( 'totalAttachedqnty', 'txtattachedqnty_', '+', 1 );\n";
        echo "math_operation( 'totalAttachedvalue', 'txtattachedvalue_', '+', 1 );\n";

        $order_attahed_qnty_sc = 0;
        $order_attahed_qnty_lc = 0;
        $order_attahed_val_sc = 0;
        $order_attahed_val_lc = 0;
        $sc_no = '';
        $lc_no = '';
        $sql_sc = "SELECT a.contract_no, sum(b.attached_qnty) as at_qt,sum(b.attached_value) as at_val FROM com_sales_contract a, com_sales_contract_order_info b WHERE a.id=b.com_sales_contract_id and b.wo_po_break_down_id='" . $row[csf("id")] . "' and b.status_active = 1 and b.is_deleted=0 group by a.id, a.contract_no";
        $result_array_sc = sql_select($sql_sc);
        foreach ($result_array_sc as $scArray) {
            if ($sc_no == "")
                $sc_no = $scArray[csf('contract_no')];
            else
                $sc_no .= "," . $scArray[csf('contract_no')];
            $order_attahed_qnty_sc += $scArray[csf('at_qt')];
            //$order_attahed_val_sc+=$scArray[csf('at_val')];
        }

        $sql_lc = "SELECT a.export_lc_no, sum(b.attached_qnty) as at_qt,sum(b.attached_value) as at_val FROM com_export_lc a, com_export_lc_order_info b WHERE a.id=b.com_export_lc_id and b.wo_po_break_down_id='" . $row[csf("id")] . "' and b.id!='" . $data . "' and b.status_active = 1 and b.is_deleted=0 group by a.id, a.export_lc_no";
        $result_array_sc = sql_select($sql_lc);
        foreach ($result_array_sc as $lcArray) {
            if ($lc_no == "")
                $lc_no = $lcArray[csf('export_lc_no')];
            else
                $lc_no .= "," . $lcArray[csf('export_lc_no')];
            $order_attahed_qnty_lc += $lcArray[csf('at_qt')];
            //$order_attahed_val_lc+=$lcArray[csf('at_val')];
        }

        $order_attached_qnty = $order_attahed_qnty_sc + $order_attahed_qnty_lc;
        //$order_attached_val=$order_attahed_val_sc+$order_attahed_val_lc;

        echo "document.getElementById('order_attached_qnty_1').value 		= '" . $order_attached_qnty . "';\n";
        echo "document.getElementById('order_attached_lc_no_1').value 		= '" . $lc_no . "';\n";
        echo "document.getElementById('order_attached_lc_qty_1').value 	= '" . $order_attahed_qnty_lc . "';\n";
        echo "document.getElementById('order_attached_sc_no_1').value 		= '" . $sc_no . "';\n";
        echo "document.getElementById('order_attached_sc_qty_1').value 	= '" . $order_attahed_qnty_sc . "';\n";

        if ($db_type == 0) {
            $attached_po_id = return_field_value("group_concat(wo_po_break_down_id)", "com_export_lc_order_info", "com_export_lc_id='" . $row[csf("com_export_lc_id")] . "' and status_active=1 and is_deleted=0");
        } else {
            $attached_po_id = return_field_value("LISTAGG(wo_po_break_down_id, ',') WITHIN GROUP (ORDER BY wo_po_break_down_id) as po_id", "com_export_lc_order_info", "com_export_lc_id='" . $row[csf("com_export_lc_id")] . "' and status_active=1 and is_deleted=0", "po_id");
            /*$attached_po_id=return_field_value(" rtrim(xmlagg(xmlelement(e,wo_po_break_down_id,',').extract('//text()') order by wo_po_break_down_id).GetClobVal(),',') as po_id","com_export_lc_order_info","com_export_lc_id='" . $row[csf("com_export_lc_id")] . "' and status_active=1 and is_deleted=0","po_id");    
            $attached_po_id = $attached_po_id->load();*/
        }
        //echo "document.getElementById('hidden_selectedID').value 		= '" . $attached_po_id . "';\n";

        echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_po_selection_save',3);\n";
        exit();
    }
}


if ($action == "order_list_for_attach_update") {
	//echo $data;die;
    $explode_data = explode("**", $data); //0->wo_po_break_down id's, 1->table row
    $lc_attch_id = $explode_data[0];
    $table_row = $explode_data[1];
	$is_sales = $explode_data[2];
	$export_item_category = $explode_data[3];
	$company_id = $explode_data[4];
	$disable_field="";
    // echo $is_service.'**';die;
	//if($is_sales==5) $disable_field='disabled="disabled"';
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand brand",'id','brand_name');
    //$hs_code_arr=return_library_array( "select id, hs_code from lib_garment_item",'id','hs_code');
	if($is_sales==0)
	{
		if($export_item_category!=10 && $export_item_category!=23 && $export_item_category!=35 && $export_item_category!=36 && $export_item_category!=37 && $export_item_category!=45)
		{
			if ($db_type == 0) {
				$actual_po_arr = return_library_array("select po_break_down_id, group_concat(acc_po_no) as acc_po_no from wo_po_acc_po_info where status_active=1 and is_deleted=0 and po_break_down_id in($data) group by po_break_down_id", "po_break_down_id", "acc_po_no");
			} else {
				$actual_po_arr = return_library_array("select po_break_down_id, listagg( cast(acc_po_no as varchar(4000)), ',') within group(order by acc_po_no) as acc_po_no from wo_po_acc_po_info where status_active=1 and is_deleted=0 and po_break_down_id in($data) group by po_break_down_id", "po_break_down_id", "acc_po_no");
			}
		}
		
	}
    
	
    if ($lc_attch_id != "") 
	{
		$comishion_amount_array=array();
		$sql_data=sql_select("SELECT particulars_id, commission_amount, job_no from wo_pre_cost_commiss_cost_dtls where STATUS_ACTIVE=1 and particulars_id= 1 
		union all 
		SELECT particulars_id, commission_amount, job_no from wo_pre_cost_commiss_cost_dtls where STATUS_ACTIVE=1 and particulars_id= 2");
		foreach($sql_data as $row){
			if($row[csf("particulars_id")]==1){
				$comishion_amount_array[$row[csf("job_no")]]["Foreign_ammount"]=$row[csf("commission_amount")];
			}else{
				$comishion_amount_array[$row[csf("job_no")]]["local_amount"]=$row[csf("commission_amount")];
			}
		}
		$cat_wise_entry_form=array(23=>'278', 36=>'311', 35=>'204', 37=>'295', 45=>'255',67=>'238');
		//print_r($cat_wise_entry_form);
		//echo $export_item_category.jahid.$cat_wise_entry_form[$export_item_category];//die;
		$data_array = sql_select("SELECT wb.id, ci.id as idd, wm.style_description, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst, wm.style_ref_no, wm.gmts_item_id, wb.unit_price,ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active, ci.com_export_lc_id, ci.fabric_description, ci.category_no, ci.hs_code, ci.is_service 
		from wo_po_break_down wb, wo_po_details_master wm, com_export_lc_order_info ci 
		where wb.job_no_mst = wm.job_no and wb.id=ci.wo_po_break_down_id and ci.id='$lc_attch_id' and ci.status_active = '1' and ci.is_deleted = '0' order by ci.id");
		$sc_inv_data=array();
		if($export_item_category!=10 && $export_item_category!=23 && $export_item_category!=35 && $export_item_category!=36 && $export_item_category!=37 && $export_item_category!=45 && $export_item_category!=67)
		{
			$data_array = "SELECT wb.id, ci.id as idd, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst, wm.style_ref_no, wm.style_description,wm.gmts_item_id, wb.unit_price, 0 as attached_qnty, wm.brand_id, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active, ci.com_export_lc_id, ci.fabric_description, ci.category_no, ci.hs_code, ci.is_service 
			FROM wo_po_details_master wm, wo_po_break_down wb, com_export_lc_order_info ci 
			WHERE wb.job_no_mst = wm.job_no and wb.id=ci.wo_po_break_down_id and ci.id in($lc_attch_id) AND wb.is_deleted = 0 AND wb.status_active = 1 and ci.status_active = '1' and ci.is_deleted = '0'";
		}
		else
		{
            if($export_item_category==35 || $export_item_category==36)
            {
                $tbl_relation= "wm.embellishment_job = wb.job_no_mst";
            }
            else
            {
                $tbl_relation= "wm.subcon_job = wb.job_no_mst";
            } 

			if($export_item_category==10)
			{
				$data_array = "SELECT wm.id, ci.id as idd, wm.job_no as po_number, sum(wb.amount) as po_total_price, sum(wb.finish_qty) as po_quantity, wm.delivery_date as shipment_date, wb.job_no_mst, wm.style_ref_no, 0 as style_description, 0 as gmts_item_id, avg(wb.avg_rate) as unit_price, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active, ci.com_export_lc_id, ci.fabric_description, ci.category_no, ci.hs_code, ci.is_service 
				FROM fabric_sales_order_mst wm, fabric_sales_order_dtls wb, com_export_lc_order_info ci 
				WHERE wm.id = wb.mst_id and wb.mst_id=ci.wo_po_break_down_id and ci.id in($lc_attch_id) and wb.is_deleted = 0 AND wb.status_active = 1 and wm.is_deleted = 0 AND wm.status_active = 1 and wm.within_group=2 and ci.status_active = '1' and ci.is_deleted = '0'
				group by wm.id, wm.job_no, wm.delivery_date, wb.job_no_mst, wm.style_ref_no, ci.id, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active, ci.com_export_lc_id, ci.fabric_description, ci.category_no, ci.hs_code, ci.is_service";
			}
			else
			{
                // , wb.job_no_mst as po_number
                $data_array = "SELECT wb.id, ci.id as idd, sum(wb.amount) as po_total_price, sum(wb.order_quantity) as po_quantity, wb.order_no as po_number, wb.job_no_mst as job_no_mst, avg(wb.rate) as unit_price, 0 as style_description, 0 as gmts_item_id, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active, ci.com_export_lc_id, ci.fabric_description, ci.category_no, ci.hs_code, ci.is_service
                FROM subcon_ord_mst wm, subcon_ord_dtls wb, com_export_lc_order_info ci 
                WHERE $tbl_relation and wb.id=ci.wo_po_break_down_id and ci.id in($lc_attch_id) and wb.is_deleted = 0 AND wb.status_active = 1 and wm.is_deleted = 0 AND wm.status_active = 1 and ci.status_active = '1' and ci.is_deleted = '0'
                group by  wb.id, wb.job_no_mst, wb.order_no, ci.id, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active, ci.com_export_lc_id, ci.fabric_description, ci.category_no, ci.hs_code, ci.is_service";
			}
		}
        
		//echo $data_array;die;
		$variable_rate_edit = sql_select("SELECT cost_heads_status FROM variable_settings_commercial where company_name =$company_id and variable_list=33 and is_deleted = 0 AND status_active = 1");
		if($variable_rate_edit[0][csf("cost_heads_status")]==1) $rate_edit=""; else $rate_edit=" readonly disabled ";
        $data_array = sql_select($data_array);
        foreach ($data_array as $row) 
		{
            $sc_no =$lc_no = '';
			$order_attahed_qnty_sc = 0;
            $order_attahed_qnty_lc = 0;
            $order_attahed_val_sc = 0;
            $order_attahed_val_lc = 0;
			
			$sql_sc = "SELECT a.id, a.contract_no, sum(b.attached_qnty) as at_qt
			FROM com_sales_contract a, com_sales_contract_order_info b 
			WHERE a.id=b.com_sales_contract_id and b.wo_po_break_down_id='" . $row[csf("id")] . "' and b.is_sales=$is_sales and b.status_active = 1 and b.is_deleted=0 
			group by a.id, a.contract_no";
			$result_array_sc = sql_select($sql_sc);
			$lc_attach_sc_qnty=0;
			foreach ($result_array_sc as $scArray) 
			{
				if ($sc_no == "") $sc_no = $scArray[csf('contract_no')]; else $sc_no .= "," . $scArray[csf('contract_no')];
				$order_attahed_qnty_sc += $scArray[csf('at_qt')];
			}
			

            $sql_lc = "SELECT a.export_lc_no, sum(b.attached_qnty) as at_qt,sum(b.attached_value) as at_val 
			FROM com_export_lc a, com_export_lc_order_info b 
			WHERE a.id=b.com_export_lc_id and b.wo_po_break_down_id='" . $row[csf("id")] . "' b.id not in($lc_attch_id) and b.is_sales=$is_sales and b.status_active = 1 and b.is_deleted=0 
			group by a.id, a.export_lc_no";
            $result_array_sc = sql_select($sql_lc);
            foreach ($result_array_sc as $lcArray) {
                if ($lc_no == "")
                    $lc_no = $lcArray[csf('export_lc_no')];
                else
                    $lc_no .= "," . $lcArray[csf('export_lc_no')];
                $order_attahed_qnty_lc += $lcArray[csf('at_qt')];
            }
			$order_attached_qnty = $order_attahed_qnty_sc + $order_attahed_qnty_lc;
			
			if($sc_inv_data[$row[csf("id")]]=="")
			{
                if($export_item_category==37){
                    $row[csf('style_ref_no')]=$row[csf('buyer_style_ref')];
                }
                $gmts_item = '';
                $hs_code = '';
                $gmts_item_id = explode(",", $row[csf('gmts_item_id')]);
                foreach ($gmts_item_id as $item_id) {
                    if ($gmts_item == ""){$gmts_item = $garments_item[$item_id];}else{$gmts_item .= ", " . $garments_item[$item_id];}
                }
				$table_row++;
				?>	
                <tr class="general" id="tr_<? echo $table_row; ?>">
                    <td>
                        <input type="text" name="txtordernumber_<? echo $table_row; ?>" id="txtordernumber_<? echo $table_row; ?>" class="text_boxes" style="width:90px"  value="<? echo $row[csf("po_number")]; ?>" onDblClick= "openmypage('requires/export_lc_controller.php?action=order_popup&types=order_select_popup&buyer_id=' + document.getElementById('cbo_buyer_name').value + '&selectID=' + document.getElementById('hidden_selectedID').value + '&export_lcID=' + document.getElementById('txt_system_id').value + '&company_id=' + document.getElementById('cbo_beneficiary_name').value+ '&import_btb=' + document.getElementById('import_btb').value+ '&cbo_export_item_category=' + document.getElementById('cbo_export_item_category').value+ '&lc_type=' + document.getElementById('cbo_lc_type').value+ '&lc_sc_no=' + document.getElementById('txt_lc_number').value, 'PO Selection Form', '<? echo $table_row; ?>')" readonly= "readonly" placeholder="Double Click" />
                        <input type="hidden" name="hiddenwopobreakdownid_<? echo $table_row; ?>" id="hiddenwopobreakdownid_<? echo $table_row; ?>" readonly= "readonly" value="<? echo $row[csf("id")]; ?>" />
                        <input type="hidden" name="isSales_<? echo $table_row; ?>" id="isSales_<? echo $table_row; ?>" value="<? echo $is_sales; ?>" />
                        <input type="hidden" name="isService_<? echo $table_row; ?>" id="isService_<? echo $table_row; ?>" value="<? echo $row[csf("is_service")]; ?>" />
                    </td>
                    <td><input type="text" name="txtaccordernumber_<? echo $table_row; ?>" id="txtaccordernumber_<? echo $table_row; ?>" class="text_boxes" style="width:90px;" readonly= "readonly" value="<? echo $actual_po_arr[$row[csf("id")]]; ?>" /></td>
                    <td>
                        <input type="text" name="txtorderqnty_<? echo $table_row; ?>" id="txtorderqnty_<? echo $table_row; ?>" class="text_boxes" style="width:60px; text-align:right" readonly= "readonly" value="<? echo $row[csf("po_quantity")]; ?>" />
                    </td>
                    <td>
                        <input type="text" name="txtordervalue_<? echo $table_row; ?>" id="txtordervalue_<? echo $table_row; ?>" class="text_boxes" style="width:80px; text-align:right" readonly= "readonly" value="<? echo number_format($row[csf("po_total_price")], 2, '.', ''); ?>" />
                    </td>
                    <td>
                        <input type="text" name="txtattachedqnty_<? echo $table_row; ?>" id="txtattachedqnty_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:60px" onKeyUp="validate_attach_qnty(<? echo $table_row; ?>)" value="<? echo $row[csf("attached_qnty")]; ?>" <? echo $disable_field ; ?> <? if($order_from_sc) echo "disabled"; else echo "" ?> />
                        <input type="hidden" name="hideattachedqnty_<? echo $table_row; ?>" id="hideattachedqnty_<? echo $table_row; ?>" class="text_boxes_numeric" value="<? echo $row[csf("attached_qnty")]; ?>"/>
                    </td>
                    <td>
                        <input type="text" name="hiddenunitprice_<? echo $table_row; ?>" id="hiddenunitprice_<? echo $table_row; ?>" value="<? echo $row[csf("unit_price")]; ?>" style="width:50px" class="text_boxes_numeric" onKeyUp="calculate_attach_val(<? echo $table_row; ?>)" <? echo $row[csf("attached_rate")]; ?> />
                    </td>
                    <td>
                        <input type="text" name="txtattachedvalue_<? echo $table_row; ?>" id="txtattachedvalue_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:80px" readonly= "readonly" value="<? echo number_format($row[csf("attached_value")], 2, '.', ''); ?>" <? if($order_from_sc) echo "disabled"; else echo "" ?> />
                    </td>
                    <td>
                        <input type="text" name="txcommission_<? echo $table_row; ?>" id="txcommission_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:80px" readonly= "readonly" value="<? echo  $comishion_amount_array[$row[csf("job_no_mst")]]["local_amount"]; ?>" />
                    </td>
                    <td>
                        <input type="text" name="txcommissionforain_<? echo $table_row; ?>" id="txcommissionforain_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:80px" readonly= "readonly" value="<? echo $comishion_amount_array[$row[csf("job_no_mst")]]["Foreign_ammount"]; ?>" />
                    </td>
                    <td>
                        <input type="text" name="txtstyleref_<? echo $table_row; ?>" id="txtstyleref_<? echo $table_row; ?>" class="text_boxes" style="width:90px" readonly= "readonly" value="<? echo $row[csf("style_ref_no")]; ?>" />
                    </td>
                    <td>
					    <input type="text" name="txtStyleDesc_<? echo $table_row; ?>" id="txtStyleDesc_<? echo $table_row; ?>" class="text_boxes" style="width:90px" readonly= "readonly" value="<? echo $row[csf("style_description")]; ?>" />
                    </td>
                    <td>
                        <input type="text" name="txtitemname_<? echo $table_row; ?>" id="txtitemname_<? echo $table_row; ?>" class="text_boxes" style="width:110px" readonly= "readonly" value="<? echo $gmts_item; ?>" />
                    </td>
                    <td>
                        <input type="text" name="txtjobno_<? echo $table_row; ?>" id="txtjobno_<? echo $table_row; ?>" class="text_boxes" style="width:80px" readonly= "readonly" value="<? echo $row[csf("job_no_mst")]; ?>"  />
                    </td>
                    <td><input type="text" name="txtfabdescrip_<? echo $table_row; ?>" id="txtfabdescrip_<? echo $table_row; ?>" class="text_boxes" style="width:90px" value="<?= $row[csf("fabric_description")];?>" /></td>
                    <td><input type="text" name="txtcategory_<? echo $table_row; ?>" id="txtcategory_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:50px" value="<?= $row[csf("category_no")];?>" /></td>
                    <td><input hs_code="text" name="txthscode_<? echo $table_row; ?>" id="txthscode_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:40px" value="<?= $row[csf("category_no")];?>" /></td>
                    <td><input type="text" name="txtbrand_<? echo $table_row; ?>" id="txtbrand_<? echo $table_row; ?>" class="text_boxes" style="width:40px"  readonly= "readonly" value="<? echo $brand_arr[$row[csf("brand_id")]]; ?>"/></td>
                    <td>                             
                        <?
                        echo create_drop_down("cbopostatus_" . $table_row, 60, $attach_detach_array, "", 0, "", $row[csf("status_active")], "copy_all(this.value+'_'+".$table_row.")");
                        ?>
                        <input type="hidden" name="hiddenexportlcorderid_<?= $table_row;?>" id="hiddenexportlcorderid_<?= $table_row;?>" readonly= "readonly" value="<?= $row[csf("idd")];?>" />
                        <input type="hidden" name="order_attached_qnty_<? echo $table_row; ?>" id="order_attached_qnty_<? echo $table_row; ?>" value="<? echo $order_attached_qnty; ?>" readonly= "readonly" />
                        <input type="hidden" name="order_attached_lc_no_<? echo $table_row; ?>" id="order_attached_lc_no_<? echo $table_row; ?>" value="<? echo $lc_no; ?>" readonly= "readonly" />
                        <input type="hidden" name="order_attached_lc_qty_<? echo $table_row; ?>" id="order_attached_lc_qty_<? echo $table_row; ?>" value="<? echo $order_attahed_qnty_lc; ?>" readonly= "readonly" />
                        <input type="hidden" name="order_attached_sc_no_<? echo $table_row; ?>" id="order_attached_sc_no_<? echo $table_row; ?>" value="<? echo $sc_no; ?>" readonly= "readonly" />
                        <input type="hidden" name="order_attached_sc_qty_<? echo $table_row; ?>" id="order_attached_sc_qty_<? echo $table_row; ?>" value="<? echo $order_attahed_qnty_sc; ?>" readonly= "readonly" />
                    </td>
                </tr>
                <?
			}
            
        }//end foreach
    }//end if data condition
}


if ($action == "populate_attached_po_id") {
    if ($db_type == 0) {
        $attached_po_id = return_field_value("group_concat(wo_po_break_down_id)", "com_export_lc_order_info", "com_export_lc_id='$data' and status_active=1 and is_deleted=0");
    } else {
        $attached_po_id = return_field_value("LISTAGG(wo_po_break_down_id, ',') WITHIN GROUP (ORDER BY wo_po_break_down_id) as po_id", "com_export_lc_order_info", "com_export_lc_id='$data' and status_active=1 and is_deleted=0", "po_id");
        /*$attached_po_id=return_field_value(" rtrim(xmlagg(xmlelement(e,wo_po_break_down_id,',').extract('//text()') order by wo_po_break_down_id).GetClobVal(),',') as po_id","com_export_lc_order_info","com_export_lc_id='$data' and status_active=1 and is_deleted=0","po_id");    
        $attached_po_id = $attached_po_id->load();*/
    }

    echo "document.getElementById('hidden_selectedID').value 		= '" . $attached_po_id . "';\n";
    exit();
}

if ($action == "order_list_for_attach") {
    $explode_data = explode("**", $data); //0->wo_po_break_down id's, 1->table row
    $data = $explode_data[0];
    $table_row = $explode_data[1];
	$is_sales = $explode_data[2];
	$export_item_category = $explode_data[3];
	$order_from_sc = $explode_data[4];
	$lc_attach_sc_id = $explode_data[5];
	$is_service = $explode_data[6];
	$company_id = $explode_data[7];
	$lc_attach_sc_id_arr=explode(",",$lc_attach_sc_id);
	$disable_field="";
    // echo $is_service.'**';die;
	//if($is_sales==5) $disable_field='disabled="disabled"';
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand brand",'id','brand_name');
    $hs_code_arr=return_library_array( "select id, hs_code from lib_garment_item",'id','hs_code');
	if($is_sales==0)
	{
		if($export_item_category!=10 && $export_item_category!=23 && $export_item_category!=35 && $export_item_category!=36 && $export_item_category!=37 && $export_item_category!=45)
		{
			if ($db_type == 0) {
				$actual_po_arr = return_library_array("select po_break_down_id, group_concat(acc_po_no) as acc_po_no from wo_po_acc_po_info where status_active=1 and is_deleted=0 and po_break_down_id in($data) group by po_break_down_id", "po_break_down_id", "acc_po_no");
			} else {
				$actual_po_arr = return_library_array("select po_break_down_id, listagg( cast(acc_po_no as varchar(4000)), ',') within group(order by acc_po_no) as acc_po_no from wo_po_acc_po_info where status_active=1 and is_deleted=0 and po_break_down_id in($data) group by po_break_down_id", "po_break_down_id", "acc_po_no");
			}
		}
		
	}

    
    $comishion_amount_array=array();
    $sql_data=sql_select("SELECT particulars_id, commission_amount, job_no from wo_pre_cost_commiss_cost_dtls where STATUS_ACTIVE=1 and particulars_id= 1 
    union all 
    SELECT particulars_id, commission_amount, job_no from wo_pre_cost_commiss_cost_dtls where STATUS_ACTIVE=1 and particulars_id= 2");
    foreach($sql_data as $row){
        if($row[csf("particulars_id")]==1){
            $comishion_amount_array[$row[csf("job_no")]]["Foreign_ammount"]=$row[csf("commission_amount")];
        }else{
            $comishion_amount_array[$row[csf("job_no")]]["local_amount"]=$row[csf("commission_amount")];
        }
    }
    	
    if ($data != "") 
	{
		/*if($is_sales==0)
		{
			$data_array = "SELECT wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst, wm.style_ref_no, wm.style_description, wb.unit_price FROM wo_po_break_down wb, wo_po_details_master wm WHERE wb.job_no_mst = wm.job_no AND wb.id in ($data) AND wb.is_deleted = 0 AND wb.status_active = 1";
		}
		else
		{
			$data_array = "SELECT wm.id, wm.job_no as po_number, sum(wb.amount) as po_total_price, sum(wb.finish_qty) as po_quantity, wm.delivery_date as shipment_date, wb.job_no_mst, wm.style_ref_no, 0 as style_description, avg(wb.avg_rate) as unit_price 
			FROM fabric_sales_order_mst wm, fabric_sales_order_dtls wb 
			WHERE wm.id = wb.mst_id and wb.is_deleted = 0 AND wb.status_active = 1 and wm.is_deleted = 0 AND wm.status_active = 1 and wm.within_group=2 and wm.id in ($data)
			group by wm.id, wm.job_no, wm.delivery_date, wb.job_no_mst, wm.style_ref_no";
		}*/
		$cat_wise_entry_form=array(23=>'278', 36=>'311', 35=>'204', 37=>'295', 45=>'255',67=>'238');
		//print_r($cat_wise_entry_form);
		//echo $export_item_category.jahid.$cat_wise_entry_form[$export_item_category];//die;
		$sc_inv_data=array();
		if($export_item_category!=10 && $export_item_category!=23 && $export_item_category!=35 && $export_item_category!=36 && $export_item_category!=37 && $export_item_category!=45 && $export_item_category!=67)
		{
			//if($is_sales==5)
//			{
//				$data_array = "SELECT wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst, wm.style_ref_no, wm.style_description, wb.unit_price, c.attached_qnty, wm.brand_id 
//				FROM wo_po_details_master wm, wo_po_break_down wb, com_sales_contract_order_info c 
//				WHERE wb.job_no_mst = wm.job_no and wb.id=c.wo_po_break_down_id AND wb.id in ($data) and c.com_sales_contract_id in($sc_id) AND wb.is_deleted = 0 AND wb.status_active = 1 and c.status_active=1";
//				$export_inv_sql="select b.po_breakdown_id from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.is_lc=2 and a.lc_sc_id in($sc_id) and b.po_breakdown_id in($data)";
//				$export_inv_sql_result=sql_select($export_inv_sql);
//				
//				foreach($export_inv_sql_result as $row)
//				{
//					$sc_inv_data[$row[csf("po_breakdown_id")]]=$row[csf("po_breakdown_id")];
//				}
//			}
//			else
//			{
//				$data_array = "SELECT wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst, wm.style_ref_no, wm.style_description, wb.unit_price, 0 as attached_qnty, wm.brand_id 
//				FROM wo_po_details_master wm, wo_po_break_down wb 
//				WHERE wb.job_no_mst = wm.job_no AND wb.id in ($data) AND wb.is_deleted = 0 AND wb.status_active = 1";
//			}
			
			$data_array = "SELECT wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst, wm.style_ref_no, wm.style_description,wm.gmts_item_id, wb.unit_price, 0 as attached_qnty, wm.brand_id
			FROM wo_po_details_master wm, wo_po_break_down wb
            WHERE wb.job_no_mst = wm.job_no AND wb.id in ($data) AND wb.is_deleted = 0 AND wb.status_active = 1";
		}
		else
		{
            if($export_item_category==35 || $export_item_category==36)
            {
                $tbl_relation= "wm.embellishment_job = wb.job_no_mst";
            }
            else
            {
                $tbl_relation= "wm.subcon_job = wb.job_no_mst";
            } 

			if($export_item_category==10)
			{
				$data_array = "SELECT wm.id, wm.job_no as po_number, sum(wb.amount) as po_total_price, sum(wb.finish_qty) as po_quantity, wm.delivery_date as shipment_date, wb.job_no_mst, wm.style_ref_no, 0 as style_description, 0 as gmts_item_id, avg(wb.avg_rate) as unit_price 
				FROM fabric_sales_order_mst wm, fabric_sales_order_dtls wb 
				WHERE wm.id = wb.mst_id and wb.is_deleted = 0 AND wb.status_active = 1 and wm.is_deleted = 0 AND wm.status_active = 1 and wm.within_group=2 and wm.id in ($data)
				group by wm.id, wm.job_no, wm.delivery_date, wb.job_no_mst, wm.style_ref_no";
			}
			else
			{
                // , wb.job_no_mst as po_number
                $data_array = "SELECT wb.id , sum(wb.amount) as po_total_price, sum(wb.order_quantity) as po_quantity, wb.order_no as po_number, wb.job_no_mst as job_no_mst, avg(wb.rate) as unit_price, 0 as style_description, 0 as gmts_item_id
                FROM subcon_ord_mst wm, subcon_ord_dtls wb 
                WHERE $tbl_relation and wb.is_deleted = 0 AND wb.status_active = 1 and wm.is_deleted = 0 AND wm.status_active = 1 and wb.id in ($data)
                group by  wb.id, wb.job_no_mst, wb.order_no";
			}
		}
        
		//echo $data_array;// die;
		$variable_rate_edit = sql_select("SELECT cost_heads_status FROM variable_settings_commercial where company_name =$company_id and variable_list=33 and is_deleted = 0 AND status_active = 1");
		if($variable_rate_edit[0][csf("cost_heads_status")]==1) $rate_edit=""; else $rate_edit=" readonly disabled ";
        $data_array = sql_select($data_array);
        foreach ($data_array as $row) 
		{
            
            $order_attahed_qnty_sc = 0;
            $order_attahed_qnty_lc = 0;
            $order_attahed_val_sc = 0;
            $order_attahed_val_lc = 0;
            $sc_no = '';
            $lc_no = '';
			//echo $is_service;die;
			if($is_service!=1)
			{
				$sql_sc = "SELECT a.id, a.contract_no, sum(b.attached_qnty) as at_qt, sum(b.attached_value) as at_val FROM com_sales_contract a, com_sales_contract_order_info b WHERE a.id=b.com_sales_contract_id and b.wo_po_break_down_id='" . $row[csf("id")] . "' and b.is_sales=$is_sales and b.status_active = 1 and b.is_deleted=0 
				group by a.id, a.contract_no";
				$result_array_sc = sql_select($sql_sc);
				$lc_attach_sc_qnty=0;
				foreach ($result_array_sc as $scArray) 
				{
					if ($sc_no == "") $sc_no = $scArray[csf('contract_no')]; else $sc_no .= "," . $scArray[csf('contract_no')];
					
					if($order_from_sc)
					{
						if(in_array($scArray[csf('id')],$lc_attach_sc_id_arr))
						{
							$lc_attach_sc_qnty += $scArray[csf('at_qt')];
						}
						else
						{
							$order_attahed_qnty_sc += $scArray[csf('at_qt')];
							$order_attahed_val_sc += $scArray[csf('at_val')];
						}
						
					}
					else
					{
						$order_attahed_qnty_sc += $scArray[csf('at_qt')];
						$order_attahed_val_sc += $scArray[csf('at_val')];
					}
				}
				
				$is_service_cond=" and b.is_service<>1";
			}
			else
			{
				$is_service_cond=" and b.is_service=1";
			}
			
			

            $sql_lc = "SELECT a.export_lc_no, sum(b.attached_qnty) as at_qt,sum(b.attached_value) as at_val FROM com_export_lc a, com_export_lc_order_info b WHERE a.id=b.com_export_lc_id and b.wo_po_break_down_id='" . $row[csf("id")] . "' and b.is_sales=$is_sales $is_service_cond and b.status_active = 1 and b.is_deleted=0 group by a.id, a.export_lc_no";
            $result_array_sc = sql_select($sql_lc);
            foreach ($result_array_sc as $lcArray) {
                if ($lc_no == "")
                    $lc_no = $lcArray[csf('export_lc_no')];
                else
                    $lc_no .= "," . $lcArray[csf('export_lc_no')];
                $order_attahed_qnty_lc += $lcArray[csf('at_qt')];
                $order_attahed_val_lc += $lcArray[csf('at_val')];
            }

            $order_attached_qnty = $order_attahed_qnty_sc + $order_attahed_qnty_lc;
            $order_attached_val = $order_attahed_val_sc + $order_attahed_val_lc;

            $remaining_qnty = $row[csf("po_quantity")] - $order_attached_qnty;
            $remaining_value = $row[csf("po_total_price")] - $order_attached_val;
			if($sc_inv_data[$row[csf("id")]]=="")
			{
                if($export_item_category==37){
                    $row[csf('style_ref_no')]=$row[csf('buyer_style_ref')];
                }
                $gmts_item = '';
                $hs_code = '';
                $gmts_item_id = explode(",", $row[csf('gmts_item_id')]);
                foreach ($gmts_item_id as $item_id) {
                    if ($gmts_item == ""){$gmts_item = $garments_item[$item_id];}else{$gmts_item .= ", " . $garments_item[$item_id];}
                    if ($hs_code == ""){$hs_code = $hs_code_arr[$item_id];}else{$hs_code .= ", " . $hs_code_arr[$item_id];}
                }
				$table_row++;
				?>	
                <tr class="general" id="tr_<? echo $table_row; ?>">
                    <td>
                        <input type="text" name="txtordernumber_<? echo $table_row; ?>" id="txtordernumber_<? echo $table_row; ?>" class="text_boxes" style="width:90px"  value="<? echo $row[csf("po_number")]; ?>" onDblClick= "openmypage('requires/export_lc_controller.php?action=order_popup&types=order_select_popup&buyer_id=' + document.getElementById('cbo_buyer_name').value + '&selectID=' + document.getElementById('hidden_selectedID').value + '&export_lcID=' + document.getElementById('txt_system_id').value + '&company_id=' + document.getElementById('cbo_beneficiary_name').value+ '&import_btb=' + document.getElementById('import_btb').value+ '&cbo_export_item_category=' + document.getElementById('cbo_export_item_category').value+ '&lc_type=' + document.getElementById('cbo_lc_type').value+ '&lc_sc_no=' + document.getElementById('txt_lc_number').value, 'PO Selection Form', '<? echo $table_row; ?>')" readonly= "readonly" placeholder="Double Click" />
                        <input type="hidden" name="hiddenwopobreakdownid_<? echo $table_row; ?>" id="hiddenwopobreakdownid_<? echo $table_row; ?>" readonly= "readonly" value="<? echo $row[csf("id")]; ?>" />
                        <input type="hidden" name="isSales_<? echo $table_row; ?>" id="isSales_<? echo $table_row; ?>" value="<? echo $is_sales; ?>" />
                        <input type="hidden" name="isService_<? echo $table_row; ?>" id="isService_<? echo $table_row; ?>" value="<? echo $is_service; ?>" />
                    </td>
                    <td><input type="text" name="txtaccordernumber_<? echo $table_row; ?>" id="txtaccordernumber_<? echo $table_row; ?>" class="text_boxes" style="width:90px;" readonly= "readonly" value="<? echo $actual_po_arr[$row[csf("id")]]; ?>" /></td>
                    <td>
                        <input type="text" name="txtorderqnty_<? echo $table_row; ?>" id="txtorderqnty_<? echo $table_row; ?>" class="text_boxes" style="width:60px; text-align:right" readonly= "readonly" value="<? echo $row[csf("po_quantity")]; ?>" />
                    </td>
                    <td>
                        <input type="text" name="txtordervalue_<? echo $table_row; ?>" id="txtordervalue_<? echo $table_row; ?>" class="text_boxes" style="width:80px; text-align:right" readonly= "readonly" value="<? echo number_format($row[csf("po_total_price")], 2, '.', ''); ?>" />
                    </td>
                    <td>
                        <? 
                        /*if($is_sales==5) 
                        {
                            $remaining_qnty=$row[csf("attached_qnty")];
                            $remaining_value=$row[csf("attached_qnty")]*$row[csf("unit_price")];
                        }*/
						
						if($order_from_sc)
						{
							$remaining_qnty=$lc_attach_sc_qnty;
                            $remaining_value=$lc_attach_sc_qnty*$row[csf("unit_price")];
						}
                        ?>
                        <input type="text" name="txtattachedqnty_<? echo $table_row; ?>" id="txtattachedqnty_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:60px" onKeyUp="validate_attach_qnty(<? echo $table_row; ?>)" value="<? echo $remaining_qnty; ?>" <? echo $disable_field ; ?> <? if($order_from_sc) echo "disabled"; else echo "" ?> />
                        <input type="hidden" name="hideattachedqnty_<? echo $table_row; ?>" id="hideattachedqnty_<? echo $table_row; ?>" class="text_boxes_numeric" value="<? echo $remaining_qnty; ?>"/>
                    </td>
                    <td>
                        <input type="text" name="hiddenunitprice_<? echo $table_row; ?>" id="hiddenunitprice_<? echo $table_row; ?>" value="<? echo $row[csf("unit_price")]; ?>" style="width:50px" class="text_boxes_numeric" onKeyUp="calculate_attach_val(<? echo $table_row; ?>)" <? echo $rate_edit; ?> />
                    </td>
                    <td>
                        <input type="text" name="txtattachedvalue_<? echo $table_row; ?>" id="txtattachedvalue_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:80px" readonly= "readonly" value="<? echo number_format($remaining_value, 2, '.', ''); ?>" <? if($order_from_sc) echo "disabled"; else echo "" ?> />
                    </td>
                    <td>
                        <input type="text" name="txcommission_<? echo $table_row; ?>" id="txcommission_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:80px" readonly= "readonly" value="<? echo  $comishion_amount_array[$row[csf("job_no_mst")]]["local_amount"]; ?>" />
                    </td>
                    <td>
                        <input type="text" name="txcommissionforain_<? echo $table_row; ?>" id="txcommissionforain_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:80px" readonly= "readonly" value="<? echo $comishion_amount_array[$row[csf("job_no_mst")]]["Foreign_ammount"]; ?>" />
                    </td>
                    <td>
                        <input type="text" name="txtstyleref_<? echo $table_row; ?>" id="txtstyleref_<? echo $table_row; ?>" class="text_boxes" style="width:90px" readonly= "readonly" value="<? echo $row[csf("style_ref_no")]; ?>" />
                    </td>
                    <td>
					    <input type="text" name="txtStyleDesc_<? echo $table_row; ?>" id="txtStyleDesc_<? echo $table_row; ?>" class="text_boxes" style="width:90px" readonly= "readonly" value="<? echo $row[csf("style_description")]; ?>" />
                    </td>
                    <td>
                        <input type="text" name="txtitemname_<? echo $table_row; ?>" id="txtitemname_<? echo $table_row; ?>" class="text_boxes" style="width:110px" readonly= "readonly" value="<? echo $gmts_item; ?>" />
                    </td>
                    <td>
                        <input type="text" name="txtjobno_<? echo $table_row; ?>" id="txtjobno_<? echo $table_row; ?>" class="text_boxes" style="width:80px" readonly= "readonly" value="<? echo $row[csf("job_no_mst")]; ?>"  />
                    </td>
                    <td><input type="text" name="txtfabdescrip_<? echo $table_row; ?>" id="txtfabdescrip_<? echo $table_row; ?>" class="text_boxes" style="width:90px" /></td>
                    <td><input type="text" name="txtcategory_<? echo $table_row; ?>" id="txtcategory_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:50px" /></td>
                    <td><input type="text" name="txthscode_<? echo $table_row; ?>" id="txthscode_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:40px" value="<? echo $hs_code; ?>"/></td>
                    <td><input type="text" name="txtbrand_<? echo $table_row; ?>" id="txtbrand_<? echo $table_row; ?>" class="text_boxes" style="width:40px"  readonly= "readonly" value="<? echo $brand_arr[$row[csf("brand_id")]]; ?>"/></td>
                    <td>                             
					<?
                    echo create_drop_down("cbopostatus_" . $table_row, 60, $attach_detach_array, "", 0, "", 1, "copy_all(this.value+'_'+".$table_row.")");
                    ?>
                    <input type="hidden" name="hiddenexportlcorderid_<?= $table_row;?>" id="hiddenexportlcorderid_<?= $table_row;?>" readonly= "readonly" value="<?= $row[csf("idd")];?>" />
                        <input type="hidden" name="order_attached_qnty_<? echo $table_row; ?>" id="order_attached_qnty_<? echo $table_row; ?>" value="<? echo $order_attached_qnty; ?>" readonly= "readonly" />
                    <input type="hidden" name="order_attached_lc_no_<? echo $table_row; ?>" id="order_attached_lc_no_<? echo $table_row; ?>" value="<? echo $lc_no; ?>" readonly= "readonly" />
                    <input type="hidden" name="order_attached_lc_qty_<? echo $table_row; ?>" id="order_attached_lc_qty_<? echo $table_row; ?>" value="<? echo $order_attahed_qnty_lc; ?>" readonly= "readonly" />
                    <input type="hidden" name="order_attached_sc_no_<? echo $table_row; ?>" id="order_attached_sc_no_<? echo $table_row; ?>" value="<? echo $sc_no; ?>" readonly= "readonly" />
                    <input type="hidden" name="order_attached_sc_qty_<? echo $table_row; ?>" id="order_attached_sc_qty_<? echo $table_row; ?>" value="<? echo $order_attahed_qnty_sc; ?>" readonly= "readonly" />
                    </td>
                
                </tr>
                <?
			}
            
        }//end foreach
    }//end if data condition
}

if ($action == "sc_popup_search") {
    echo load_html_head_contents("Export LC Form", "../../../", 1, 1, '', '1', '');
    extract($_REQUEST);
    ?>
    <script>

        function fn_check()
        {
            if (form_validation('cbo_company_name', 'Company Name') == false)
            {
                return;
            } else
            {
                show_list_view(document.getElementById('cbo_company_name').value + '**' + document.getElementById('cbo_buyer_name').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('hidden_sc_selectedID').value + '**' + document.getElementById('export_item_category').value, 'sc_search_list_view', 'search_div', 'export_lc_controller', 'setFilterGrid(\'tbl_list_search\',-1)');
            }
        }

        var selected_id = new Array, selected_po_id = new Array();
        function check_all_data() {
            var tbl_row_count = $("#tbl_list_search tbody tr").length;

            for (var i = 1; i <= tbl_row_count; i++) {
                js_set_value(i);
            }
        }

        function toggle(x, origColor) {
            var newColor = 'yellow';
            if (x.style) {
                x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
            }
        }

        function js_set_value(str) 
		{
            toggle(document.getElementById('search' + str), '#FFFFCC');

            if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1) {
                selected_id.push($('#txt_individual_id' + str).val());
				selected_po_id.push($('#txt_po_id' + str).val());

            } else {
                for (var i = 0; i < selected_id.length; i++) {
                    if (selected_id[i] == $('#txt_individual_id' + str).val())
                        break;
                }
                selected_id.splice(i, 1);
				selected_po_id.splice(i, 1);
            }
            var id = ''; var po_ids='';
            for (var i = 0; i < selected_id.length; i++) {
                id += selected_id[i] + ',';
				po_ids += selected_po_id[i] + ',';
            }
            id = id.substr(0, id.length - 1);
			po_ids = po_ids.substr(0, po_ids.length - 1);

            $('#txt_selected_id').val(id);
			$('#txt_po_id').val(po_ids);
        }
    </script>

    </head>

    <body>
        <div align="center" style="width:990px;">
            <form name="searchexportlcfrm" id="searchexportlcfrm">
                <fieldset style="width:950px;">
                    <legend>Enter search words</legend>           
                    <table cellpadding="0" cellspacing="0" width="80%" class="rpt_table" border="1" rules="all">
                        <thead>
                        <th class="must_entry_caption">Company</th>
                        <th>Buyer</th>
                        <th>Search By</th>
                        <th>Enter</th>
                        <th><input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" /><input type="hidden" name="id_field" id="id_field" value="" /></th>
                        </thead>
                        <tr class="general">
                            <td>
                                <?
                                echo create_drop_down("cbo_company_name", 165, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--- Select ---", $companyID, "load_drop_down( 'export_lc_controller', this.value, 'load_drop_down_buyer', 'buyer_td_id' );");
                                ?>                        
                            </td>
                            <td id="buyer_td_id">
                                <?
								if($buyerID) $disable_status=1; else $disable_status=0;
                                $sql = "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$companyID' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name";
                                echo create_drop_down("cbo_buyer_name", 162, $sql, "id,buyer_name", 1, "--- Select Buyer ---", $buyerID, "",$disable_status);
                                ?>
                            </td>                  
                            <td> 
                                <?
                                $arr = array(1 => 'SC NO');
                                echo create_drop_down("cbo_search_by", 165, $arr, "", 0, "--- Select ---", 0, "");
                                ?> 
                                <input type="hidden" id="hidden_sc_selectedID" value="<? echo $sc_selectedID; ?>" />
                                <input type="hidden" id="export_item_category" value="<? echo $export_item_category; ?>" />
                                <input type="hidden" name="txt_selected_id" id="txt_selected_id" value="" />
                                <input type="hidden" name="txt_po_id" id="txt_po_id" value="" />
                            </td>						
                            <td id="search_by_td">
                                <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                            </td>                       
                            <td>
                                <input type="button" id="search_button" class="formbutton" value="Show" onClick="fn_check()" style="width:100px;" />
                            </td>
                        </tr>
                    </table>
                    <div style="width:100%; margin-top:10px" id="search_div" align="left"></div> 
                    <table width="950" cellspacing="0" cellpadding="0" style="border:none" align="center">
                        <tr>
                            <td align="center" height="30" valign="bottom">
                                <div style="width:100%"> 
                                    <div style="width:40%; float:left" align="left">
                                        <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                                    </div>
                                    <div style="width:60%; float:left" align="left">
                                        <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </form>
        </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if ($action == "sc_search_list_view") {
    $data = explode('**', $data);
    if ($data[0] != 0) {
        $company_id = " and a.beneficiary_name = $data[0]";
    } else {
        $company_id = "";
    }
    if ($data[1] != 0) {
        $buyer_id = " and a.buyer_name = $data[1]";
    } else {
        $buyer_id = "";
    }
    $search_by = $data[2];
    $search_text = $data[3];
    $export_item_category = $data[5];
    if($data[5]){ $search_category= "and a.export_item_category = $data[5]";}

    if ($search_by == 0) {
        $search_condition = "";
    } else if ($search_by == 1 && $search_text != "") {
        $search_condition = "and a.contract_no like '" . trim($search_text) . "%'";
    }

    if ($data[4] == "")
        $sc_selectedID = "";
    else
        $sc_selectedID = " and a.id not in ($data[4])";
	if($db_type==0)
	{
		$sql = "SELECT a.id, a.contract_no, a.contract_date, a.beneficiary_name, a.buyer_name, a.applicant_name, a.convertible_to_lc as type, a.contract_value, group_concat(b.wo_po_break_down_id) as po_ids
		from com_sales_contract a left join com_sales_contract_order_info b on a.id=b.com_sales_contract_id and b.status_active=1
		where a.status_active=1 and a.is_deleted=0 and a.convertible_to_lc<>2 $sc_selectedID $company_id $buyer_id $search_condition $search_category
		group by a.id, a.contract_no, a.contract_date, a.beneficiary_name, a.buyer_name, a.applicant_name, a.convertible_to_lc, a.contract_value";
	}
	else
	{
		$sql = "SELECT a.id, a.contract_no, a.contract_date, a.beneficiary_name, a.buyer_name, a.applicant_name, a.convertible_to_lc as type, a.contract_value, listagg(cast(b.wo_po_break_down_id as varchar(4000)),',') within group(order by b.wo_po_break_down_id) as po_ids 
        from com_sales_contract a left join COM_SALES_CONTRACT_ORDER_INFO b on a.id=b.com_sales_contract_id and b.status_active=1 
		where a.status_active=1 and a.is_deleted=0 and a.convertible_to_lc<>2 $sc_selectedID $company_id $buyer_id $search_condition $search_category
		group by a.id, a.contract_no, a.contract_date, a.beneficiary_name, a.buyer_name, a.applicant_name, a.convertible_to_lc, a.contract_value";
	}
	//echo $sql;
    
    $data_array = sql_select($sql);
    ?> 
    <table width="950" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
        <thead> 
        <th width="50">SL</th>          
        <th width="120">Contact Number</th>
        <th width="120">Buyer Name </th>
        <th width="120">Contract Value</th>
        <th width="150">Cumulative Replaced</th>
        <th width="120">Yet to Replace</th>
        <th width="120">Contract Date</th>
        <th>Type</th>
    </thead>
    </table>
    <div style="width:950px; overflow-y:scroll; max-height:250px">     
        <table width="932" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_list_search"> 
            <?
            $i = 0;
            $yet_to_replace = 0;
            foreach ($data_array as $row) 
			{
                if ($i % 2 == 0)
                    $bgcolor = "#FFFFFF";
                else
                    $bgcolor = "#E9F3FF";

                $replaced_result = return_field_value("sum(replaced_amount)", "com_export_lc_atch_sc_info", "com_sales_contract_id=" . $row[csf('id')] . " and is_deleted=0 and status_active=1");

                if ($row[csf('contract_value')] > $replaced_result) 
				{
                    $i++;
                    $yet_to_replace = $row[csf('contract_value')] - $replaced_result;
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer;" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)" >                		<td width="50"><? echo $i; ?>  
                            <input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $row[csf('id')]; ?>"/>
                            <input type="hidden" name="txt_po_id" id="txt_po_id<? echo $i ?>" value="<? echo $row[csf('po_ids')]; ?>"/>
                        </td>
                        <td width="120"><? echo $row[csf('contract_no')]; ?></td>
                        <td width="120"><? echo $buyer_details[$row[csf('buyer_name')]]; ?></td>
                        <td width="120" align="right"><? echo number_format($row[csf('contract_value')], 2); ?></td>
                        <td width="150" align="right"><? echo number_format($replaced_result, 2); ?></td>
                        <td width="120" align="right"><? echo number_format($yet_to_replace, 2); ?></td>
                        <td width="120" align="center"><? echo change_date_format($row[csf('contract_date')]); ?></td>
                        <td><? echo $convertible_to_lc[$row[csf('type')]]; ?></td>                        
                    </tr>
                    <?
                }
            }
            ?>
        </table>
    </div>     
    <?
    exit();
}

if ($action == "populate_data_sc_form") {
    $data = explode('**', $data);
    $sc_id = $data[0];
    $tblRow = $data[1];

    $data_array = sql_select("select id,contract_no,contract_date,beneficiary_name,buyer_name,applicant_name,convertible_to_lc as type,contract_value from com_sales_contract where status_active=1 and is_deleted=0 and id in ($sc_id) order by id");

    foreach ($data_array as $row) {
        $tblRow++;
        $replaced_result = return_field_value("sum(replaced_amount)", "com_export_lc_atch_sc_info", "com_sales_contract_id=" . $row[csf('id')] . " and is_deleted=0 and status_active=1");
        $yet_to_replace = $row[csf('contract_value')] - $replaced_result;

        if ($db_type == 0) {
            $sql = "select group_concat(a.id) as btb_id, group_concat(a.lc_number) as btb_lc 
			from com_btb_lc_master_details a, com_btb_export_lc_attachment b 
			where a.id=b.import_mst_id and b.lc_sc_id=" . $row[csf('id')] . " and b.is_lc_sc=1 and b.is_deleted=0 and b.status_active=1 and a.is_deleted=0 and a.status_active=1";
        } else {

            $sql = "select LISTAGG(a.id, ',') WITHIN GROUP (ORDER BY a.id) as btb_id, LISTAGG(a.lc_number, ',') WITHIN GROUP (ORDER BY a.lc_number) as btb_lc
             from com_btb_lc_master_details a, com_btb_export_lc_attachment b where a.id=b.import_mst_id and b.lc_sc_id=" . $row[csf('id')] . " and b.is_lc_sc=1 and b.is_deleted=0 and b.status_active=1 and a.is_deleted=0 and a.status_active=1";

            /*$sql = "select rtrim(xmlagg(xmlelement(e,a.id,',').extract('//text()') order by a.id).GetClobVal(),',') as btb_id ,rtrim(xmlagg(xmlelement(e,a.lc_number,',').extract('//text()') order by a.lc_number).GetClobVal(),',') as btb_lc
			 from com_btb_lc_master_details a, com_btb_export_lc_attachment b where a.id=b.import_mst_id and b.lc_sc_id=" . $row[csf('id')] . " and b.is_lc_sc=1 and b.is_deleted=0 and b.status_active=1 and a.is_deleted=0 and a.status_active=1";
            $btbArray[0][csf("btb_id")] = $btbArray[0][csf("btb_id")]->load();
            $btbArray[0][csf("btb_lc")] = $btbArray[0][csf("btb_lc")]->load();*/
        }
        $btbArray = sql_select($sql);
        ?>
        <tr class="general" id="<? echo "trs_" . $tblRow; ?>">
            <td>
                <input type="text" name="txtSalesContractNo_<? echo $tblRow; ?>" id="txtSalesContractNo_<? echo $tblRow; ?>" placeholder="Double Click"  class="text_boxes" style="width:125px" onDblClick="add_sales_contract(<? echo $tblRow; ?>)" readonly  value="<? echo $row[csf("contract_no")]; ?>"  />
                <input type="hidden" name="hiddenScId_<? echo $tblRow; ?>" id="hiddenScId_<? echo $tblRow; ?>"  value="<? echo $row[csf("id")]; ?>" />
            </td>
            <td>
                <input type="text" name="txtReplacementAmount_<? echo $tblRow; ?>" id="txtReplacementAmount_<? echo $tblRow; ?>" class="text_boxes_numeric" style="width:115px;"  onKeyup="CalculateCumulativeValue(this.value, this.id);"  value="<? echo 0; ?>" />
                <input type="hidden" name="hideReplacementAmount_<? echo $tblRow; ?>" id="hideReplacementAmount_<? echo $tblRow; ?>" value="<? echo 0; ?>" readonly/>
            </td>
            <td><input type="text" name="txtContractValue_<? echo $tblRow; ?>" id="txtContractValue_<? echo $tblRow; ?>" class="text_boxes_numeric" style="width:90px" readonly  value="<? echo $row[csf("contract_value")]; ?>" /></td>
            <td>
                <input type="text" name="txtCumulativeReplaced_<? echo $tblRow; ?>" id="txtCumulativeReplaced_<? echo $tblRow; ?>" class="text_boxes_numeric" style="width:110px" readonly value="<? echo $replaced_result; ?>" />
                <input type="hidden" name="txtCumulativeReplacedDB_<? echo $tblRow; ?>" id="txtCumulativeReplacedDB_<? echo $tblRow; ?>" class="text_boxes" style="width:110px"  value="<? echo $replaced_result; ?>" />
            </td>
            <td><input type="text" name="txtYetToReplace_<? echo $tblRow; ?>" id="txtYetToReplace_<? echo $tblRow; ?>" class="text_boxes_numeric" style="width:110px" readonly  value="<? echo $yet_to_replace; ?>" /></td>
            <td>
                <input type="text" name="txtBtbLcSelected_<? echo $tblRow; ?>" id="txtBtbLcSelected_<? echo $tblRow; ?>" class="text_boxes" style="width:130px" disabled="disabled" value="<? echo $btbArray[0][csf("btb_lc")]; ?>"/>
                <input type="hidden" name="txtBtbLcSelectedID_<? echo $tblRow; ?>" id="txtBtbLcSelectedID_<? echo $tblRow; ?>" class="text_boxes"  style="width:130px" value="<? echo $btbArray[0][csf("btb_id")]; ?>" />
            </td>
            <td>
                <?
                echo create_drop_down("cbo_sc_status_" . $tblRow, 100, $attach_detach_array, $row[csf('status_active')], 0, "", 1, "");
                ?>
            </td>                           
        </tr> 	
        <?
        //$tblRow++;
    }

    exit();
}

if ($action == "show_sc_active_listview") 
{
    $sql = "select a.id, a.com_sales_contract_id, b.contract_no, b.contract_value, a.replaced_amount, a.attched_btb_lc_id from com_export_lc_atch_sc_info a, com_sales_contract b where a.com_sales_contract_id=b.id and a.com_export_lc_id=$data and a.is_deleted = 0 and a.status_active=1 order by a.id";
    //echo $sql . "<br>";
    $data_array = sql_select($sql);
    ?>
	<style>
		#example {overflow-wrap: anywhere;}
	</style>
    <table width="920" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_list_search">
        <thead class="table_header"> 
        <th width="140">Sales Contract</th>
        <th width="130">Replaced Amount</th>
        <th width="140">Contract Value</th>
        <th width="140">Cumulative Replaced</th>
        <th width="140">Yet to Replace</th>
        <th width="210">Attached BTB LC</th>
    </thead>
    <tbody class="table_body" id="table_body" style="width:930px; max-height:120px">
        <?
        $i = 1;
        $yet_to_replace = 0;
        foreach ($data_array as $row) {
            if ($i % 2 == 0)
                $bgcolor = "#FFFFFF";
            else
                $bgcolor = "#E9F3FF";

            $replaced_result = return_field_value("sum(replaced_amount)", "com_export_lc_atch_sc_info", "com_sales_contract_id=" . $row[csf('com_sales_contract_id')] . " and is_deleted=0 and status_active=1");
            $yet_to_replace = $row[csf('contract_value')] - $replaced_result;

            if ($row[csf('attched_btb_lc_id')] != "") {
                if ($db_type == 0) {
                    $btb_lc_no = return_field_value("group_concat(lc_number)", "com_btb_lc_master_details", "id in ($row[attched_btb_lc_id])");
                } else {
                    $btb_lc_no = return_field_value("LISTAGG(lc_number, ',') WITHIN GROUP (ORDER BY lc_number) as lc_number", "com_btb_lc_master_details", "id in (" . $row[csf('attched_btb_lc_id')] . ")", "lc_number");
                    /*$btb_lc_no = return_field_value("rtrim(xmlagg(xmlelement(e,id,',').extract('//text()') order by id).GetClobVal(),',') as lc_number", "com_btb_lc_master_details", "id in (" . $row[csf('attched_btb_lc_id')] . ")", "lc_number");
                    $btb_lc_no = $btb_lc_no->load();*/    
                }
            }
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer;" id="search<? echo $i; ?>" onClick="get_php_form_data(<? echo $row[csf('id')]; ?>, 'populate_data_from_sales_contract', 'requires/export_lc_controller');" >                
                <td width="140"><p><? echo $row[csf('contract_no')]; ?></p></td>
                <td width="130" align="right"><? echo number_format($row[csf('replaced_amount')], 4); ?></td>
                <td width="140" align="right"><? echo number_format($row[csf('contract_value')], 4); ?></td>
                <td width="140" align="right"><? echo number_format($replaced_result, 4); ?></td>
                <td width="140" align="right"><? echo number_format($yet_to_replace, 4); ?></td>
                <td width="210" id="example"><? echo $btb_lc_no; ?></td>
            </tr>
            <?
            $i++;
        }
        ?>
    </tbody>	
    </table>
    <?
    exit();
}

if ($action == "populate_data_from_sales_contract") {
    $data_array = sql_select("select a.id, a.com_export_lc_id, a.com_sales_contract_id, b.contract_no, b.contract_value, a.replaced_amount, a.attched_btb_lc_id, a.status_active from com_export_lc_atch_sc_info a, com_sales_contract b where a.com_sales_contract_id=b.id and a.id =$data and a.is_deleted = 0 and a.status_active=1");

    foreach ($data_array as $row) {
        $replaced_result = return_field_value("sum(replaced_amount)", "com_export_lc_atch_sc_info", "com_sales_contract_id=" . $row[csf('com_sales_contract_id')] . " and is_deleted=0 and status_active=1");
        $yet_to_replace = $row[csf('contract_value')] - $replaced_result;
        $actual_cumulative = $replaced_result - $row[csf("replaced_amount")];

        /*if ($row[csf('attched_btb_lc_id')] != "") {
            if ($db_type == 0) {
                $btb_lc_no = return_field_value("group_concat(lc_number)", "com_btb_lc_master_details", "id in (" . $row[csf('attched_btb_lc_id')] . ")");
            } else {
                $btb_lc_no = return_field_value("LISTAGG(lc_number, ',') WITHIN GROUP (ORDER BY id) as lc_number", "com_btb_lc_master_details", "id in (" . $row[csf('attched_btb_lc_id')] . ")", "lc_number");
            }
        }*/
        if($row[csf('attched_btb_lc_id')]!="")
        {
            $ids=$row[csf('attched_btb_lc_id')];
            $ids_arr=explode(",",$ids);
            $id_all="";
            foreach($ids_arr as $values)
            {
                if($values)
                {
                    if($id_all=="") $id_all=$values;else $id_all.=",".$values;
                }
            }
            //echo "string $id_all";
            if($db_type==0)
            {
                $btb_lc_no=return_field_value(" group_concat(lc_number) as lc_number","com_btb_lc_master_details","id in ($id_all)","lc_number");
                 
            }
            else
            {
                $btb_lc_no = return_field_value("LISTAGG(lc_number, ',') WITHIN GROUP (ORDER BY lc_number) as lc_number", "com_btb_lc_master_details", "id in ($id_all)", "lc_number");
               /* $btb_lc_no=return_field_value("rtrim(xmlagg(xmlelement(e,lc_number,',').extract('//text()') order by lc_number).GetClobVal(),',') as lc_number","com_btb_lc_master_details","id in ($id_all)","lc_number"); 
                $btb_lc_no = $btb_lc_no->load();*/
            }
        }

        echo "$('#tbl_sales_contract tbody tr:not(:first)').remove();\n";

        echo "document.getElementById('txtSalesContractNo_1').value 			= '" . $row[csf("contract_no")] . "';\n";
        echo "document.getElementById('hiddenScId_1').value 					= '" . $row[csf("com_sales_contract_id")] . "';\n";
        echo "document.getElementById('txtReplacementAmount_1').value 			= '" . number_format($row[csf("replaced_amount")],4,'.','') . "';\n";
        echo "document.getElementById('hideReplacementAmount_1').value 			= '" . $row[csf("replaced_amount")] . "';\n";
        echo "document.getElementById('txtContractValue_1').value 				= '" . $row[csf("contract_value")] . "';\n";
        echo "document.getElementById('txtCumulativeReplaced_1').value 			= '" . number_format($replaced_result,4,'.','') . "';\n";
        echo "document.getElementById('txtCumulativeReplacedDB_1').value 		= '" . $actual_cumulative . "';\n";
        echo "document.getElementById('txtYetToReplace_1').value 				= '" . number_format($yet_to_replace,4,'.','') . "';\n";
        echo "document.getElementById('txtBtbLcSelected_1').value 				= '" . $btb_lc_no . "';\n";
        echo "document.getElementById('txtBtbLcSelectedID_1').value 			= '" . $row[csf("attched_btb_lc_id")] . "';\n";
        echo "document.getElementById('cbo_sc_status_1').value 				= '" . $row[csf("status_active")] . "';\n";

        echo "document.getElementById('hiddenlcAttachSalesContractID').value 	= '" . $row[csf("id")] . "';\n";

        if ($db_type == 0) {
            $replaced_sc_id = return_field_value("group_concat(com_sales_contract_id)", "com_export_lc_atch_sc_info", "com_export_lc_id=" . $row[csf('com_export_lc_id')] . " and status_active=1 and is_deleted=0");
        } else {
            $replaced_sc_id = return_field_value("LISTAGG(com_sales_contract_id, ',') WITHIN GROUP (ORDER BY com_sales_contract_id) as sc_id", "com_export_lc_atch_sc_info", "com_export_lc_id=" . $row[csf('com_export_lc_id')] . " and status_active=1 and is_deleted=0", "sc_id");
            /*$replaced_sc_id = return_field_value("rtrim(xmlagg(xmlelement(e,com_sales_contract_id,',').extract('//text()') order by com_sales_contract_id).GetClobVal(),',') as sc_id", "com_export_lc_atch_sc_info", "com_export_lc_id=" . $row[csf('com_export_lc_id')] . " and status_active=1 and is_deleted=0", "sc_id");
            $replaced_sc_id = $replaced_sc_id->load();*/ 
        }
        echo "document.getElementById('hidden_sc_selectedID').value 	= '" . $replaced_sc_id . "';\n";
        echo "document.getElementById('txt_tot_row_attach_sales').value 	= '1';\n";

        echo "math_operation( 'totalReplacedAmount', 'txtReplacementAmount_', '+', 1 );\n";
        echo "math_operation( 'totalContractValue', 'txtContractValue_', '+', 1 );\n";
        echo "math_operation( 'totalCumulativeReplaced', 'txtCumulativeReplaced_', '+', 1 );\n";
        echo "math_operation( 'totalYettoReplace', 'txtYetToReplace_', '+', 1 );\n";

        echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_sales_contract_selection',2);\n";
        exit();
    }
}

if ($action == "load_sc_id") {
    if ($db_type == 0) {
        $replaced_sc_id = return_field_value("group_concat(com_sales_contract_id)", "com_export_lc_atch_sc_info", "com_export_lc_id=$data and status_active=1 and is_deleted=0 group by com_export_lc_id");
    } else {
        $replaced_sc_id = return_field_value("LISTAGG(com_sales_contract_id, ',') WITHIN GROUP (ORDER BY com_sales_contract_id) as sc_id", "com_export_lc_atch_sc_info", "com_export_lc_id=$data and status_active=1 and is_deleted=0 group by com_export_lc_id", "sc_id");
        /*$replaced_sc_id = return_field_value(" rtrim(xmlagg(xmlelement(e,com_sales_contract_id,',').extract('//text()') order by com_sales_contract_id).GetClobVal(),',') as sc_id", "com_export_lc_atch_sc_info", "com_export_lc_id=$data and status_active=1 and is_deleted=0 group by com_export_lc_id", "sc_id");
        $replaced_sc_id = $replaced_sc_id->load();*/
    }


    echo "document.getElementById('hidden_sc_selectedID').value 	= '" . $replaced_sc_id . "';\n";
    exit();
}

if ($action == "save_update_delete_mst") 
{
    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));

    if ($operation == 0) 
	{  // Insert Here
        $con = connect();
        if ($db_type == 0) {
            mysql_query("BEGIN");
        }

        if (is_duplicate_field("export_lc_no", "com_export_lc", "export_lc_no=$txt_lc_number and beneficiary_name=$cbo_beneficiary_name and buyer_name=$cbo_buyer_name and lien_bank=$cbo_lien_bank and status_active=1 and is_deleted=0") == 1) {
            echo "11**0";disconnect($con);
            die;
        }
		
		if (is_duplicate_field("internal_file_no", "com_export_lc", "export_lc_no=$txt_lc_number and internal_file_no=$txt_internal_file_no and export_lc_type=$cbo_lc_type and lc_year=$txt_year and beneficiary_name=$cbo_beneficiary_name and status_active=1 and is_deleted=0") == 1) 
		{
            echo "11**0";disconnect($con);
            die;
        }

        $maximum_tolarence = str_replace("'", '', $txt_lc_value) + (str_replace("'", '', $txt_lc_value) * str_replace("'", '', $txt_tolerance)) / 100;
        $minimum_tolarence = str_replace("'", '', $txt_lc_value) - (str_replace("'", '', $txt_lc_value) * str_replace("'", '', $txt_tolerance)) / 100;

        $foreign_comn_value = (str_replace("'", '', $txt_lc_value) * str_replace("'", '', $txt_foreign_comn)) / 100;
        $local_comn_value = (str_replace("'", '', $txt_lc_value) * str_replace("'", '', $txt_local_comn)) / 100;

        $max_btb_limit_value = (str_replace("'", '', $txt_lc_value) * str_replace("'", '', $txt_max_btb_limit)) / 100;

        if ($db_type == 0)
            $year_cond = "YEAR(insert_date)";
        else if ($db_type == 2)
            $year_cond = "to_char(insert_date,'YYYY')";
        else
            $year_cond = ""; //defined Later

        $new_export_lc_system_id = explode("*", return_mrr_number(str_replace("'", "", $cbo_beneficiary_name), '', 'LC', date("Y", time()), 5, "select export_lc_prefix,export_lc_prefix_number from com_export_lc where beneficiary_name=$cbo_beneficiary_name and $year_cond=" . date('Y', time()) . " order by id desc ", "export_lc_prefix", "export_lc_prefix_number"));

        $id = return_next_id("id", "com_export_lc", 1);
        if(str_replace("'", '', $import_btb_id)>0 && str_replace("'", '', $import_btb)==1)
        {
            $field_array_dtls = "id,com_export_lc_id,wo_po_break_down_id,attached_qnty,attached_rate,attached_value,import_btb,import_btb_id,work_order_no,pi_id,pi_dtls_id,determination_id,construction,composition,color_id,gsm,dia_width,uom,is_sales,inserted_by,insert_date,booking_no,item_group_id,size_id,aop_color,body_part_id,buyer_style_ref,gmts_item_id,embell_name,embell_type,fabric_description";
            $idDtls = return_next_id("id", "com_export_lc_order_info", 1);

            for ($i = 1; $i <= $total_row; $i++) {
                $workOrderNo = "workOrderNo_" . $i;
                $workOrderId = "hideWoId_" . $i;
                $hidePiId = "hidePiId_" . $i;
                $hidePiDtlsId = "hidePiDtlsId_" . $i;
                $determinationId = "hideDeterminationId_" . $i;
                $construction = "construction_" . $i;
                $composition = "composition_" . $i;
                $colorId = "colorId_" . $i;
                $gsm = "gsm_" . $i;
                $diawidth = "diawidth_" . $i;
                $uom = "uom_" . $i;
                $quantity = "quantity_" . $i;
                $rate = "rate_" . $i;
                $amount = "amount_" . $i;
                $isSalesId = "isSalesId_" . $i;
    
                $bookingNo = "bookingNo_" . $i;
                $itemgroupidPlace = "itemgroupidPlace_" . $i;	
                $itemdescription = "itemdescription_" . $i;
                $itemColor = "itemColor_" . $i;
                $itemSizePlace = "itemSizePlace_" . $i;
                $uomPlace_ = "uomPlace_" . $i;
                $ratePlace = "ratePlace_" . $i;
                $amountPlace = "amountPlace_" . $i;
                $bookingWithoutOrder = "bookingWithoutOrder_" . $i;
                $aopColorPlace = "aopColorPlace_" . $i;
                $bodyPartPlace = "bodyPartPlace_" . $i;
                $styleRef = "styleRef_" . $i;
                $gmtsItemPlace = "gmtsItemPlace_" . $i;
                $embNamePlace = "embNamePlace_" . $i;
                $embTypePlace = "embTypePlace_" . $i;
    
                if ($data_array_dtls != "")
                    $data_array_dtls .= ",";
                $data_array_dtls.= "(" . $idDtls . "," . $id . "," . $$workOrderId . "," . $$quantity . "," . $$rate . "," . $$amount . "," . $import_btb . "," . $import_btb_id . ",'" . str_replace("'", "", $$workOrderNo) . "','" . str_replace("'", "", $$hidePiId) . "','" . str_replace("'", "", $$hidePiDtlsId) . "','" . str_replace("'", "", $$determinationId) . "','" . str_replace("'", "", $$construction) . "','" . str_replace("'", "", $$composition) . "','" . str_replace("'", "", $$colorId) . "','" . str_replace("'", "", $$gsm) . "','" . str_replace("'", "", $$diawidth) . "','" . str_replace("'", "", $$uom) . "','" .str_replace("'", "", $$isSalesId) . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "','" . str_replace("'", "", $$bookingNo) . "','" . str_replace("'", "", $$itemgroupidPlace) . "','" . str_replace("'", "", $$itemSizePlace) . "','" . str_replace("'", "", $$aopColorPlace) . "','" . str_replace("'", "", $$bodyPartPlace) . "','" .  str_replace("'", "", $$styleRef) . "','" . str_replace("'", "", $$gmtsItemPlace) . "','" . str_replace("'", "", $$embNamePlace) . "','" . str_replace("'", "", $$embTypePlace) . "','" . str_replace("'", "", $$itemdescription) . "')";
    
                $idDtls = $idDtls + 1;
            }
        }
        else
        {
            $import_btb=0;
        }

        $field_array = "id, export_lc_prefix,export_lc_prefix_number,export_lc_system_id,export_lc_no, lc_date, beneficiary_name, buyer_name, applicant_name, notifying_party, consignee, issuing_bank_name, replacement_lc, lien_bank, lien_date, lc_value, currency_name, tolerance,maximum_tolarence,minimum_tolarence,last_shipment_date, expiry_date, shipping_mode, pay_term, inco_term, inco_term_place, lc_source, port_of_entry, port_of_loading, port_of_discharge, internal_file_no, doc_presentation_days, max_btb_limit, max_btb_limit_value, foreign_comn, foreign_comn_value, local_comn, local_comn_value, remarks, tenor, transfering_bank_ref, bl_clause, reimbursement_clauses, discount_clauses, is_lc_transfarrable, transfer_bank, negotiating_bank, nominated_shipp_line, re_imbursing_bank, claim_adjustment, expiry_place, bank_file_no, lc_year, reason, export_item_category, import_btb, import_btb_id, inserted_by, insert_date, export_lc_type, estimated_qnty,approved,ready_to_approved";

        $data_array = "(" . $id . ",'" . $new_export_lc_system_id[1] . "'," . $new_export_lc_system_id[2] . ",'" . $new_export_lc_system_id[0] . "'," . $txt_lc_number . "," . $txt_lc_date . "," . $cbo_beneficiary_name . "," . $cbo_buyer_name . "," . $cbo_applicant_name . "," . $cbo_notifying_party . "," . $cbo_consignee . "," . $txt_issuing_bank . "," . $cbo_replacement_lc . "," . $cbo_lien_bank . "," . $txt_lien_date . "," . $txt_lc_value . "," . $cbo_currency_name . "," . $txt_tolerance . ",'" . $maximum_tolarence . "','" . $minimum_tolarence . "'," . $txt_last_shipment_date . "," . $txt_expiry_date . "," . $cbo_shipping_mode . "," . $cbo_pay_term . "," . $cbo_inco_term . "," . $txt_inco_term_place . "," . $cbo_lc_source . "," . $txt_port_of_entry . "," . $txt_port_of_loading . "," . $txt_port_of_discharge . "," . $txt_internal_file_no . "," . $txt_doc_presentation_days . "," . $txt_max_btb_limit . "," . $max_btb_limit_value . "," . $txt_foreign_comn . "," . $foreign_comn_value . "," . $txt_local_comn . ",'" . $local_comn_value . "'," . $txt_remarks . "," . $txt_tenor . "," . $txt_transfering_bank_ref . "," . $txt_bl_clause . "," . $txt_reimbursement_clauses . "," . $txt_discount_clauses . "," . $cbo_is_lc_transfarrable . "," . $txt_transfer_bank . "," . $txt_negotiating_bank . "," . $txt_nominated_shipp_line . "," . $txt_re_imbursing_bank . "," . $txt_claim_adjustment . "," . $txt_expiry_place . "," . $txt_bank_file_no . "," . $txt_year . "," . $txt_reason . "," . $cbo_export_item_category . "," . $import_btb . "," . $import_btb_id . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $cbo_lc_type .",".$txt_estimated_lc_qnty .",0,".$cbo_ready_to_approved. ")";

        //echo "5**insert into com_export_lc (".$field_array.") values ".$data_array;oci_rollback($con);disconnect($con);die;
        $rID = sql_insert("com_export_lc", $field_array, $data_array, 1);
        $rID2=true;
        if($data_array_dtls!="")
        {
            // echo "5**insert into com_export_lc_order_info (".$field_array_dtls.") values ".$data_array_dtls;die;
            $rID2=sql_insert("com_export_lc_order_info",$field_array_dtls,$data_array_dtls,0);
        }
        //echo "5**".$rID.'=='.$rID2;oci_rollback($con);disconnect($con);die;
        if ($db_type == 0) {
            if ($rID && $rID2) {
                mysql_query("COMMIT");
                echo "0**" . $id . "**" . $new_export_lc_system_id[0];
            } else {
                mysql_query("ROLLBACK");
                echo "5**0**" . "&nbsp;";
            }
        } else if ($db_type == 2 || $db_type == 1) {
            if ($rID && $rID2) {
                oci_commit($con);
                echo "0**" . $id . "**" . $new_export_lc_system_id[0];
            } else {
                oci_rollback($con);
                echo "5**0**" . "&nbsp;";
            }
        }
        disconnect($con);
        die;
    } 
	else if ($operation == 1) // Update Here
	{   
        $con = connect();
        if ($db_type == 0) {
            mysql_query("BEGIN");
        }

        if (is_duplicate_field("export_lc_no", "com_export_lc", "export_lc_no=$txt_lc_number and beneficiary_name=$cbo_beneficiary_name and buyer_name=$cbo_buyer_name and lien_bank=$cbo_lien_bank and id<>$txt_system_id and status_active=1 and is_deleted=0") == 1) {
            echo "11**0";disconnect($con);
            die;
        }
		
		if (is_duplicate_field("internal_file_no", "com_export_lc", "export_lc_no=$txt_lc_number and internal_file_no=$txt_internal_file_no and export_lc_type=$cbo_lc_type and lc_year=$txt_year and beneficiary_name=$cbo_beneficiary_name and id<>$txt_system_id and status_active=1 and is_deleted=0") == 1) 
		{
            echo "11**0";disconnect($con);
            die;
        }
		
		$lc_value=str_replace("'", '',$txt_lc_value)*1;
		$attach_ord_result=sql_select("select sum(attached_value) as attached_value from com_export_lc_order_info where status_active=1 and is_deleted=0 and com_export_lc_id=$txt_system_id");
		$attach_ord_value=$attach_ord_result[0][csf("attached_value")]*1;
        if(str_replace("'", '', $import_btb_id)==0 && str_replace("'", '', $import_btb)==0)
        {
            if($lc_value<$attach_ord_value)
            {
                echo "31** LC Value Not Allow Less Then Attach Value";disconnect($con);die;
            }
        }

        $lc_approved=return_field_value("approved","com_export_lc","id=$txt_system_id","approved");
		if ($lc_approved==1 || $lc_approved==3){
			echo "50**0";disconnect($con);
			die;
		}

        $maximum_tolarence = str_replace("'", '', $txt_lc_value) + (str_replace("'", '', $txt_lc_value) * str_replace("'", '', $txt_tolerance)) / 100;
        $minimum_tolarence = str_replace("'", '', $txt_lc_value) - (str_replace("'", '', $txt_lc_value) * str_replace("'", '', $txt_tolerance)) / 100;

        $foreign_comn_value = (str_replace("'", '', $txt_lc_value) * str_replace("'", '', $txt_foreign_comn)) / 100;
        $local_comn_value = (str_replace("'", '', $txt_lc_value) * str_replace("'", '', $txt_local_comn)) / 100;

        $max_btb_limit_value = (str_replace("'", '', $txt_lc_value) * str_replace("'", '', $txt_max_btb_limit)) / 100;

        //update code here
		//export_item_category*export_lc_no*beneficiary_name*buyer_name*
		//" . $cbo_export_item_category . "*" . $txt_lc_number . "*" . $cbo_beneficiary_name . "*" . $cbo_buyer_name . "			
        $field_array = "export_lc_no*lc_date*applicant_name*notifying_party*consignee*issuing_bank_name*replacement_lc*lien_bank*lien_date*lc_value*currency_name*tolerance*maximum_tolarence*minimum_tolarence*last_shipment_date*expiry_date*shipping_mode*pay_term*inco_term*inco_term_place*lc_source*port_of_entry*port_of_loading*port_of_discharge*internal_file_no*doc_presentation_days*max_btb_limit*max_btb_limit_value*foreign_comn*foreign_comn_value*local_comn*local_comn_value*remarks*tenor*transfering_bank_ref*bl_clause*reimbursement_clauses*discount_clauses*is_lc_transfarrable*transfer_bank*negotiating_bank*nominated_shipp_line*re_imbursing_bank*claim_adjustment*expiry_place*bank_file_no*lc_year*reason*import_btb*import_btb_id*updated_by*update_date*export_lc_type*estimated_qnty*ready_to_approved";

        $data_array = "" . $txt_lc_number . "*" . $txt_lc_date . "*" . $cbo_applicant_name . "*" . $cbo_notifying_party . "*" . $cbo_consignee . "*" . $txt_issuing_bank . "*" . $cbo_replacement_lc . "*" . $cbo_lien_bank . "*" . $txt_lien_date . "*" . $txt_lc_value . "*" . $cbo_currency_name . "*" . $txt_tolerance . "*" . $maximum_tolarence . "*" . $minimum_tolarence . "*" . $txt_last_shipment_date . "*" . $txt_expiry_date . "*" . $cbo_shipping_mode . "*" . $cbo_pay_term . "*" . $cbo_inco_term . "*" . $txt_inco_term_place . "*" . $cbo_lc_source . "*" . $txt_port_of_entry . "*" . $txt_port_of_loading . "*" . $txt_port_of_discharge . "*" . $txt_internal_file_no . "*" . $txt_doc_presentation_days . "*" . $txt_max_btb_limit . "*" . $max_btb_limit_value . "*" . $txt_foreign_comn . "*" . $foreign_comn_value . "*" . $txt_local_comn . "*'" . $local_comn_value . "'*" . $txt_remarks . "*" . $txt_tenor . "*" . $txt_transfering_bank_ref . "*" . $txt_bl_clause . "*" . $txt_reimbursement_clauses . "*" . $txt_discount_clauses . "*" . $cbo_is_lc_transfarrable . "*" . $txt_transfer_bank . "*" . $txt_negotiating_bank . "*" . $txt_nominated_shipp_line . "*" . $txt_re_imbursing_bank . "*" . $txt_claim_adjustment . "*" . $txt_expiry_place . "*" . $txt_bank_file_no . "*" . $txt_year . "*" . $txt_reason . "*" . $import_btb . "*" . $import_btb_id . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*" . $cbo_lc_type ."*". $txt_estimated_lc_qnty ."*". $cbo_ready_to_approved. "";

        if(str_replace("'", '', $import_btb_id)>0 && str_replace("'", '', $import_btb)==1)
        {
            $field_array_dtls = "id,com_export_lc_id,wo_po_break_down_id,attached_qnty,attached_rate,attached_value,import_btb,import_btb_id,work_order_no,pi_id,pi_dtls_id,determination_id,construction,composition,color_id,gsm,dia_width,uom,is_sales,inserted_by,insert_date,booking_no,item_group_id,size_id,aop_color,body_part_id,buyer_style_ref,gmts_item_id,embell_name,embell_type,fabric_description";

            $field_array_update="wo_po_break_down_id*attached_qnty*attached_rate*attached_value*import_btb*import_btb_id*work_order_no*pi_id*pi_dtls_id*determination_id*construction*composition*color_id*gsm*dia_width*uom*is_sales*updated_by*update_date*booking_no*item_group_id*size_id*aop_color*body_part_id*buyer_style_ref*gmts_item_id*embell_name*embell_type*fabric_description";
    
            $idDtls = return_next_id("id", "com_export_lc_order_info", 1);

            $is_import_pi=1;$woDtlsTrsansId='';
            for ($i = 1; $i <= $total_row; $i++) {
                $updateIdDtls = "updateIdDtls_" . $i;
                $workOrderNo = "workOrderNo_" . $i;
                $workOrderId = "hideWoId_" . $i;
                $hidePiId = "hidePiId_" . $i;
                $hidePiDtlsId = "hidePiDtlsId_" . $i;
                $determinationId = "hideDeterminationId_" . $i;
                $construction = "construction_" . $i;
                $composition = "composition_" . $i;
                $colorId = "colorId_" . $i;
                $gsm = "gsm_" . $i;
                $diawidth = "diawidth_" . $i;
                $uom = "uom_" . $i;
                $quantity = "quantity_" . $i;
                $rate = "rate_" . $i;
                $amount = "amount_" . $i;
                $isSalesId = "isSalesId_" . $i;
    
                $bookingNo = "bookingNo_" . $i;
                $itemgroupidPlace = "itemgroupidPlace_" . $i;	
                $itemdescription = "itemdescription_" . $i;
                $itemColor = "itemColor_" . $i;
                $itemSizePlace = "itemSizePlace_" . $i;
                $uomPlace_ = "uomPlace_" . $i;
                $ratePlace = "ratePlace_" . $i;
                $amountPlace = "amountPlace_" . $i;
                $bookingWithoutOrder = "bookingWithoutOrder_" . $i;
                $aopColorPlace = "aopColorPlace_" . $i;
                $bodyPartPlace = "bodyPartPlace_" . $i;
                $styleRef = "styleRef_" . $i;
                $gmtsItemPlace = "gmtsItemPlace_" . $i;
                $embNamePlace = "embNamePlace_" . $i;

                $embTypePlace = "embTypePlace_" . $i;

                if (str_replace("'", "", $$updateIdDtls) != "") 
                {
                    $id_arr[] = str_replace("'", '', $$updateIdDtls);
                    $data_array_update[str_replace("'", '', $$updateIdDtls)] = explode("*", ("'" . str_replace("'", "", $$workOrderId) . "'*" . $$quantity . "*" . $$rate . "*" . $$amount . "*" . $import_btb . "*" . $import_btb_id . "*'" . str_replace("'", "", $$workOrderNo) . "'*'" . str_replace("'", "", $$hidePiId) . "'*'" . str_replace("'", "", $$hidePiDtlsId) . "'*'" . str_replace("'", "", $$determinationId) . "'*'" . str_replace("'", "", $$construction) . "'*'" . str_replace("'", "", $$composition) . "'*'" . str_replace("'", "", $$colorId) . "'*'" . str_replace("'", "", $$gsm) . "'*'" . str_replace("'", "", $$diawidth) . "'*'" . str_replace("'", "", $$uom)  . "'*'" .str_replace("'", "", $$isSalesId) . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*'" . str_replace("'", "", $$bookingNo) . "'*'" . str_replace("'", "", $$itemgroupidPlace) . "'*'" . str_replace("'", "", $$itemSizePlace) . "'*'" . str_replace("'", "", $$aopColorPlace) . "'*'" . str_replace("'", "", $$bodyPartPlace) . "'*'" .  str_replace("'", "", $$styleRef) . "'*'" . str_replace("'", "", $$gmtsItemPlace) . "'*'" . str_replace("'", "", $$embNamePlace) . "'*'" . str_replace("'", "", $$embTypePlace) . "'*'" . str_replace("'", "", $$itemdescription) . "'"));
                } 
                else 
                {
                    if ($data_array_dtls != "")
                        $data_array_dtls .= ",";
                    $data_array_dtls .= "(" . $idDtls . "," . $txt_system_id . "," . $$workOrderId . "," . $$quantity . "," . $$rate . "," . $$amount . "," . $import_btb . "," . $import_btb_id . ",'" . str_replace("'", "", $$workOrderNo) . "','" . str_replace("'", "", $$hidePiId) . "','" . str_replace("'", "", $$hidePiDtlsId) . "','" . str_replace("'", "", $$determinationId) . "','" . str_replace("'", "", $$construction) . "','" . str_replace("'", "", $$composition) . "','" . str_replace("'", "", $$colorId) . "','" . str_replace("'", "", $$gsm) . "','" . str_replace("'", "", $$diawidth) . "','" . str_replace("'", "", $$uom) . "','" .str_replace("'", "", $$isSalesId) . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "','" . str_replace("'", "", $$bookingNo) . "','" . str_replace("'", "", $$itemgroupidPlace) . "','" . str_replace("'", "", $$itemSizePlace) . "','" . str_replace("'", "", $$aopColorPlace) . "','" . str_replace("'", "", $$bodyPartPlace) . "','" . str_replace("'", "", $$styleRef) . "','" .  str_replace("'", "", $$gmtsItemPlace) . "','" . str_replace("'", "", $$embNamePlace) . "','" . str_replace("'", "", $$embTypePlace) . "','" . str_replace("'", "", $$itemdescription) . "')";
    
                    $idDtls = $idDtls + 1;
                }
    
                $idDtls = $idDtls + 1;
            }
        }
        else
        {
            $import_btb=0;
        }

        $rID = sql_update("com_export_lc", $field_array, $data_array, "id", "" . $txt_system_id . "", 1);
        $rID2=true; $rID3=true;
        if(count($data_array_update)>0)
        {
            $rID2=execute_query(bulk_update_sql_statement( "com_export_lc_order_info", "id", $field_array_update, $data_array_update, $id_arr ));
        }

        if($data_array_dtls!="")
        {
            $rID3=sql_insert("com_export_lc_order_info",$field_array_dtls,$data_array_dtls,0);
        }
        // echo "5**".$rID.'=='.$rID2.'=='.$rID3;die;
        if ($db_type == 0) {
            if ($rID) {
                mysql_query("COMMIT");
                echo "1**" . str_replace("'", '', $txt_system_id) . "**" . str_replace("'", '', $export_lc_system_id);
            } else {
                mysql_query("ROLLBACK");
                echo "6**" . str_replace("'", '', $txt_system_id) . "**" . str_replace("'", '', $export_lc_system_id);
            }
        } else if ($db_type == 2 || $db_type == 1) {
            if ($rID) {
                oci_commit($con);
                echo "1**" . str_replace("'", '', $txt_system_id) . "**" . str_replace("'", '', $export_lc_system_id);
            } else {
                oci_rollback($con);
                echo "6**" . str_replace("'", '', $txt_system_id) . "**" . str_replace("'", '', $export_lc_system_id);
            }
        }
        disconnect($con);
        die;
	}
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if(str_replace("'","",$txt_system_id)=="") { echo "10**";disconnect($con);die; }
 		$id=str_replace("'","",$txt_system_id);

        $lc_approved=return_field_value("approved","com_export_lc","id=$txt_system_id","approved");
        if ($lc_approved==1 || $lc_approved==3){
            echo "50**0";disconnect($con);
            die;
        }
		
		$update_field_arr="updated_by*update_date*status_active*is_deleted";
		$update_data_arr="".$user_id."*'".$pc_date_time."'*0*1";
		$lcMst=$lcSc=$lcPo=true;
		//echo "10** $invMst && $invDtls && $invPo && $invClr = $id";oci_rollback($con);die;
		//echo "10**"."Update com_export_invoice_ship_mst set status_active=0,is_deleted=1,updated_by=$user_id,update_date='$pc_date_time'  where id =$id";oci_rollback($con);die;
        $btb_id = return_field_value("id","com_btb_export_lc_attachment","lc_sc_id=".$id." and is_lc_sc=0 and status_active=1 and is_deleted=0","id");
        if($btb_id>0)
        {
            echo "31**Delete Not Allow. This LC No Found in BTB/Margin LC";disconnect($con); die;
        }

		$invoice_id = return_field_value("id","com_export_invoice_ship_mst","lc_sc_id=".$id." and is_lc=1 and status_active=1 and is_deleted=0","id");
		if($invoice_id>0)
		{
			echo "31**Delete Not Allow. This LC No Found in Invoice";disconnect($con); die;
		}
		else
		{
			if($id>0)
			{
				$lcMst=sql_update("com_export_lc",$update_field_arr,$update_data_arr,"id",$id,1);
				$lcSc=sql_update("com_export_lc_atch_sc_info",$update_field_arr,$update_data_arr,"com_export_lc_id",$id,1);
				$lcPo=sql_update("com_export_lc_order_info",$update_field_arr,$update_data_arr,"com_export_lc_id",$id,1);
			}
			//echo "10** $invMst && $invDtls && $invPo && $invClr = $update_id";oci_rollback($con);die;
			if($db_type==0)
			{
				if($lcMst && $lcSc && $lcPo)
				{
					mysql_query("COMMIT");  
					echo "2**".$id;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".$id;
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($lcMst && $lcSc && $lcPo)
				{
					oci_commit($con);  
					echo "2**".$id;
				}
				else
				{
					oci_rollback($con);
					echo "10**".$id;
				}
			}
			disconnect($con);
			die;
		}
	}
}

if ($action == "save_update_delete_sc_info") {
    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));

    if ($operation == 0) {  // Insert Here
        $con = connect();
        if ($db_type == 0) {
            mysql_query("BEGIN");
        }

        $field_array = "id,com_export_lc_id,com_sales_contract_id,replaced_amount,attched_btb_lc_id,status_active,inserted_by,insert_date";
        $id = return_next_id("id", "com_export_lc_atch_sc_info", 1);

        for ($j = 1; $j <= $noRow; $j++) {
            $salesContractID = "hiddenScId_" . $j;
            $txtReplacementAmount = "txtReplacementAmount_" . $j;
            $txtBtbLcSelectedID = "txtBtbLcSelectedID_" . $j;
            $cbo_sc_status = "cbo_sc_status_" . $j;
			
			if (is_duplicate_field("id", "com_export_lc_atch_sc_info", "status_active=1 and is_deleted=0 and com_export_lc_id=$txt_system_id and com_sales_contract_id=".$$salesContractID." ") == 1) {
				echo "11**0";disconnect($con);
				die;
			}
			
            if ($data_array != "") $data_array .= ",";
            $data_array .= "(" . $id . "," . $txt_system_id . "," . $$salesContractID . "," . $$txtReplacementAmount . "," . $$txtBtbLcSelectedID . "," . $$cbo_sc_status . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
            $id = $id + 1;
        }
        //echo "5**0**insert into com_export_lc_atch_sc_info (".$field_array.") values ".$data_array;die;
        $rID = sql_insert("com_export_lc_atch_sc_info", $field_array, $data_array, 1);
        if ($db_type == 0) {
            if ($rID) {
                mysql_query("COMMIT");
                echo "0**" . str_replace("'", '', $txt_system_id) . "**0";
            } else {
                mysql_query("ROLLBACK");
                echo "5**0**0";
            }
        } else if ($db_type == 2 || $db_type == 1) {
            if ($rID) {
                oci_commit($con);
                echo "0**" . str_replace("'", '', $txt_system_id) . "**0";
            } else {
                oci_rollback($con);
                echo "5**0**0";
            }
        }
        disconnect($con);
        die;
    } else if ($operation == 1) {   // Update Here
        $con = connect();
        if ($db_type == 0) {
            mysql_query("BEGIN");
        }

        $field_array = "id,com_export_lc_id,com_sales_contract_id,replaced_amount,attched_btb_lc_id,status_active,inserted_by,insert_date";
        $field_array_update = "com_export_lc_id*com_sales_contract_id*replaced_amount*attched_btb_lc_id*status_active*updated_by*update_date";

        $hiddenlcAttachSalesContractID = str_replace("'", '', $hiddenlcAttachSalesContractID);
        $id = return_next_id("id", "com_export_lc_atch_sc_info", 1);

        for ($j = 1; $j <= $noRow; $j++) {
            $salesContractID = "hiddenScId_" . $j;
            $txtReplacementAmount = "txtReplacementAmount_" . $j;
            $txtBtbLcSelectedID = "txtBtbLcSelectedID_" . $j;
            $cbo_sc_status = "cbo_sc_status_" . $j;

            if ($j == 1) {
                if ($hiddenlcAttachSalesContractID != "") {
                    $data_array_update = "" . $txt_system_id . "*" . $$salesContractID . "*" . $$txtReplacementAmount . "*" . $$txtBtbLcSelectedID . "*" . $$cbo_sc_status . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
                } else {
                    if ($data_array != "")
                        $data_array .= ",";

                    $data_array .= "(" . $id . "," . $txt_system_id . "," . $$salesContractID . "," . $$txtReplacementAmount . "," . $$txtBtbLcSelectedID . "," . $$cbo_sc_status . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
                    $id = $id + 1;
                }
            }
            else {
                if ($data_array != "")
                    $data_array .= ",";

                $data_array .= "(" . $id . "," . $txt_system_id . "," . $$salesContractID . "," . $$txtReplacementAmount . "," . $$txtBtbLcSelectedID . "," . $$cbo_sc_status . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
                $id = $id + 1;
            }
        }

        $flag = 1;

        if ($data_array != "") {
            $rID2 = sql_insert("com_export_lc_atch_sc_info", $field_array, $data_array, 0);
            if ($flag == 1) {
                if ($rID2)
                    $flag = 1;
                else
                    $flag = 0;
            }
        }

        if ($data_array_update != "") {
            $rID = sql_update("com_export_lc_atch_sc_info", $field_array_update, $data_array_update, "id", "" . $hiddenlcAttachSalesContractID . "", 1);
            if ($flag == 1) {
                if ($rID)
                    $flag = 1;
                else
                    $flag = 0;
            }
        }

        if ($db_type == 0) {
            if ($flag == 1) {
                mysql_query("COMMIT");
                echo "1**" . str_replace("'", '', $txt_system_id) . "**0";
            } else {
                mysql_query("ROLLBACK");
                echo "6**0**1";
            }
        } else if ($db_type == 2 || $db_type == 1) {
            if ($flag == 1) {
                oci_commit($con);
                echo "1**" . str_replace("'", '', $txt_system_id) . "**0";
            } else {
                oci_rollback($con);
                echo "6**0**1";
            }
        }

        disconnect($con);
        die;
    }
}

if ($action == "save_update_delete_lc_order_info") 
{
    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));
	$cbo_replacement_lc=str_replace("'","",$cbo_replacement_lc);
	$cbo_export_item_category=str_replace("'","",$cbo_export_item_category);
	$all_sc_id=trim(str_replace("'","",$all_sc_id));
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_lc_type=str_replace("'","",$cbo_lc_type);
	$all_po_id_arr=array();
	for ($j = 1; $j <= $noRow; $j++) {
		$hiddenwopobreakdownid = "hiddenwopobreakdownid_" . $j;
		$all_po_id_arr[str_replace("'","",$$hiddenwopobreakdownid)] = str_replace("'","",$$hiddenwopobreakdownid);
	}
	
	if(count($all_po_id_arr)>0)
	{
        if($cbo_lc_type==2)
        {
            if($cbo_export_item_category==10)
            {
                $po_buyer = sql_select("SELECT max(a.buyer_id) as buyer_name FROM fabric_sales_order_mst a, fabric_sales_order_dtls b 
                WHERE a.id = b.mst_id and b.is_deleted = 0 and b.status_active = 1 and a.is_deleted = 0 AND a.status_active = 1 and a.within_group=2 and a.id in(".implode(",",$all_po_id_arr).")");
            }
            else
            {
                if($cbo_export_item_category==35 || $cbo_export_item_category==36)
                {
                    $tbl_relation= "a.embellishment_job = b.job_no_mst";
                }
                else
                {
                    $tbl_relation= "a.subcon_job = b.job_no_mst";
                }
                $po_buyer = sql_select("SELECT max(a.party_id) as buyer_name FROM subcon_ord_mst a, subcon_ord_dtls b WHERE $tbl_relation and a.is_deleted = 0 and a.status_active = 1 and b.is_deleted = 0 AND b.status_active = 1 and b.id in(".implode(",",$all_po_id_arr).")");
            }
        }
        else
        {
            $po_buyer=sql_select("select max(a.buyer_name) as buyer_name from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.status_active=1 and b.id in(".implode(",",$all_po_id_arr).")");
        }
		$attach_po_buyer=$po_buyer[0]["BUYER_NAME"];
		if($cbo_buyer_name!=$attach_po_buyer)
		{
			echo "11** Attached PO Buyer and LC Buyer Not Match";die;
		}
	}
		
		
	if($all_sc_id=="")
	{
		if($db_type==0)
		{
			$all_sc_id=return_field_value("group_concat(com_sales_contract_id) as com_sales_contract_id","com_export_lc_atch_sc_info","com_export_lc_id=$txt_system_id and status_active=1","com_sales_contract_id");
		}
		else
		{
			$all_sc_id=return_field_value("LISTAGG(com_sales_contract_id, ',') WITHIN GROUP (ORDER BY com_sales_contract_id) as com_sales_contract_id","com_export_lc_atch_sc_info","com_export_lc_id=$txt_system_id and status_active=1","com_sales_contract_id");
		}
	}
	//echo "10**".$all_sc_id;die;
	

    if ($operation == 0) 
	{  // Insert Here
        $con = connect();
        if ($db_type == 0) {
            mysql_query("BEGIN");
        }
        
        $lc_approved=return_field_value("approved","com_export_lc","id=$txt_system_id","approved");
		if ($lc_approved==1 || $lc_approved==3){
			echo "50**0";disconnect($con);
			die;
		}

		$lc_ammendment_id=return_field_value("max(id) as amd_id","com_export_lc_amendment","export_lc_id=$txt_system_id and status_active=1","amd_id");
		if($lc_ammendment_id=="") $lc_ammendment_id=0;
        $field_array = "id,com_export_lc_id,wo_po_break_down_id,attached_qnty,attached_rate,attached_value,fabric_description,category_no,hs_code,status_active,inserted_by,insert_date,lc_amendment_id,is_sales,export_item_category,is_service,commission,commission_foreign";
        // if($cbo_export_item_category==23 || $cbo_export_item_category==35 || $cbo_export_item_category==36 || $cbo_export_item_category==37 || $cbo_export_item_category==67){$is_service=1;}else{$is_service=0;}
        $id = return_next_id("id", "com_export_lc_order_info", 1);
        $currentattachedvalue = 0;
		$insert_poId_arr=array();
        for ($j = 1; $j <= $noRow; $j++) {
            $hiddenwopobreakdownid = "hiddenwopobreakdownid_" . $j;
            $txtattachedqnty = "txtattachedqnty_" . $j;
            $hiddenunitprice = "hiddenunitprice_" . $j;
            $txtattachedvalue = "txtattachedvalue_" . $j;
            $cbopostatus = "cbopostatus_" . $j;
            $txtfabdescrip = "txtfabdescrip_" . $j;
            $txtcategory = "txtcategory_" . $j;
            $txcommission = "txcommission_" . $j;
            $txcommissionforain = "txcommissionforain_" . $j;
            $txthscode = "txthscode_" . $j;
			$isSales = "isSales_" . $j;
			$isService = "isService_" . $j;
			if(str_replace("'","",$$isSales)=="") $is_Sales=0; else $is_Sales=str_replace("'","",$$isSales);
			if(str_replace("'","",$$isService)=="") $is_service=0; else $is_service=str_replace("'","",$$isService);
			
			if($cbo_replacement_lc==1)
			{
				if ($$hiddenwopobreakdownid != "" && str_replace("'","",$$cbopostatus) ==1)
				{
					if ($data_array != "") $data_array .= ",";
					$data_array .= "(" . $id . "," . $txt_system_id . "," . $$hiddenwopobreakdownid . "," . $$txtattachedqnty . "," . $$hiddenunitprice . "," . $$txtattachedvalue . "," . $$txtfabdescrip . "," . $$txtcategory . "," . $$txthscode . "," . $$cbopostatus . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "','" . $lc_ammendment_id . "','" . $is_Sales . "'," . $cbo_export_item_category . "," . $is_service. "," . $$txcommission ."," . $$txcommissionforain .")";
	
					$id = $id + 1;
	
					$currentattachedval = str_replace("'","",$$txtattachedvalue);
					$currentattachedvalue += number_format($currentattachedval,2,'.','');
					$insert_poId_arr[str_replace("'","",$$hiddenwopobreakdownid)]=str_replace("'","",$$hiddenwopobreakdownid);
				}
			}
			else
			{
				if ($$hiddenwopobreakdownid != "") 
				{
					if ($data_array != "") $data_array .= ",";
					$data_array .= "(" . $id . "," . $txt_system_id . "," . $$hiddenwopobreakdownid . "," . $$txtattachedqnty . "," . $$hiddenunitprice . "," . $$txtattachedvalue . "," . $$txtfabdescrip . "," . $$txtcategory . "," . $$txthscode . "," . $$cbopostatus . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "','" . $lc_ammendment_id . "','" . $is_Sales . "'," . $cbo_export_item_category . "," . $is_service. "," . $$txcommission . "," . $$txcommissionforain . ")";
	
					$id = $id + 1;
	
					$currentattachedval = str_replace("'","",$$txtattachedvalue);
					$currentattachedvalue += number_format($currentattachedval,2,'.','');
				}
			}
			
        }

     	
        $lc_value=return_field_value("lc_value","com_export_lc","id=".$txt_system_id);
        $tolerance=return_field_value("tolerance","com_export_lc","id=".$txt_system_id);
        $pre_tot_attached =  sql_select("select sum(b.attached_value) as attached_value from com_export_lc a, com_export_lc_order_info b where a.id = b.com_export_lc_id and a.id=".$txt_system_id." and b.status_active = 1 and b.is_deleted = 0");

        // echo $tolerance; disconnect($con);die;
        $tolerance_value=($lc_value/100)*$tolerance;
        $lc_value = number_format($lc_value,2,".","")+number_format($tolerance_value,2);
        $tot_attached = number_format($pre_tot_attached[0][csf("attached_value")],2,".","");

        if(number_format(($tot_attached + $currentattachedvalue),2,'.','') > number_format($lc_value,2,'.',''))
        {
            echo "11** Attached Value Exceeds LC Value ".number_format(($tot_attached + $currentattachedvalue),2,'.','')." = ".number_format($lc_value,2,'.','');disconnect($con);die;
        }

        //echo "11**  LC Value $tot_attached + $currentattachedvalue > $lc_value";die;
        //print_r($data_array);die;

		// echo "11** insert into com_export_lc_order_info ($field_array) values $data_array";die;

        $rID = sql_insert("com_export_lc_order_info", $field_array, $data_array, 1);
		$rID2 =true;
		if(count($insert_poId_arr)>0 && $all_sc_id!="" && $cbo_replacement_lc==1)
		{
			$rID2 =execute_query("update com_sales_contract_order_info set status_active=7, is_deleted=8 where com_sales_contract_id in($all_sc_id) and wo_po_break_down_id in(".implode(",",$insert_poId_arr).")");
		}
		//com_sales_contract_order_info
        if ($db_type == 0) {
            if ($rID && $rID2) {
                mysql_query("COMMIT");
                echo "0**" . str_replace("'", '', $txt_system_id) . "**0**".str_replace("'", '', $cbo_export_item_category);
            } else {
                mysql_query("ROLLBACK");
                echo "5**0**0";
            }
        } else if ($db_type == 2 || $db_type == 1) {
            if ($rID && $rID2) {
                oci_commit($con);
                echo "0**" . str_replace("'", '', $txt_system_id) . "**0**".str_replace("'", '', $cbo_export_item_category);
            } else {
                oci_rollback($con);
                echo "5**0**0";
            }
        }
        disconnect($con);
        die;
    } else if ($operation == 1) {   // Update Here
        $con = connect();
        if ($db_type == 0) {
            mysql_query("BEGIN");
        }

        $lc_approved=return_field_value("approved","com_export_lc","id=$txt_system_id","approved");
		if ($lc_approved==1 || $lc_approved==3){
			echo "50**0";disconnect($con);
			die;
		}

        // update code here
        $field_array = "id,com_export_lc_id,wo_po_break_down_id,attached_qnty,attached_rate,attached_value,fabric_description,category_no,hs_code,status_active,inserted_by,insert_date,is_sales,export_item_category,is_service,commission,commission_foreign";
        $field_array_update = "wo_po_break_down_id*attached_qnty*attached_rate*attached_value*fabric_description*category_no*hs_code*status_active*updated_by*update_date*is_sales*export_item_category*is_service*commission*commission_foreign";
        // if($cbo_export_item_category==23 || $cbo_export_item_category==35 || $cbo_export_item_category==36 || $cbo_export_item_category==37 || $cbo_export_item_category==67){$is_service=1;}else{$is_service=0;}
        //$hiddenexportlcorderid = str_replace("'", '', $hiddenexportlcorderid);
        $id = return_next_id("id", "com_export_lc_order_info", 1);
        $currentattachedvalue = 0;$detach_order_id="";
        for ($j = 1; $j <= $noRow; $j++) 
		{
            $hiddenwopobreakdownid = "hiddenwopobreakdownid_" . $j;
            $txtattachedqnty = "txtattachedqnty_" . $j;
            $hiddenunitprice = "hiddenunitprice_" . $j;
            $txtattachedvalue = "txtattachedvalue_" . $j;
            $cbopostatus = "cbopostatus_" . $j;
            $txtfabdescrip = "txtfabdescrip_" . $j;
            $txtcategory = "txtcategory_" . $j;
            $txcommission = "txcommission_" . $j;
            $txcommissionforain = "txcommissionforain_" . $j;
            $txthscode = "txthscode_" . $j;
			$isSales = "isSales_" . $j;
			$isService = "isService_" . $j;
			$hiddenexportlcorderid = "hiddenexportlcorderid_" . $j;
			
			if(str_replace("'","",$$isSales)=="") $is_Sales=0; else $is_Sales=str_replace("'","",$$isSales);
			if(str_replace("'","",$$isService)=="") $is_service=0; else $is_service=str_replace("'","",$$isService);

            if (str_replace("'", '', $$hiddenexportlcorderid) != "") 
			{
				if (str_replace("'", '', $$cbopostatus) == 0) 
				{
					$invoice_no = "";
					$po_id = $$hiddenwopobreakdownid;
					$sql_invoice = "select a.invoice_no from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b where a.id=b.mst_id and a.lc_sc_id=$txt_system_id and a.is_lc=1 and b.po_breakdown_id=$po_id and b.current_invoice_qnty>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.invoice_no";
					$data = sql_select($sql_invoice);
					if (count($data) > 0) {
						foreach ($data as $row) {
							if ($invoice_no == "")
								$invoice_no = $row[csf('invoice_no')];
							else
								$invoice_no .= ",\n" . $row[csf('invoice_no')];
						}

						echo "13**" . $invoice_no . "**1";disconnect($con);
						die;
					}
					$detach_order_id=$$hiddenwopobreakdownid;
				}
				
				$id_array_update[]=str_replace("'", '', $$hiddenexportlcorderid);
				$data_array_update[str_replace("'", '', $$hiddenexportlcorderid)] = explode("*","" . $$hiddenwopobreakdownid . "*" . $$txtattachedqnty . "*" . $$hiddenunitprice . "*" . $$txtattachedvalue . "*" . $$txtfabdescrip . "*" . $$txtcategory . "*" . $$txthscode . "*" . $$cbopostatus . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*'" . $is_Sales . "'*" . $cbo_export_item_category . "*" . $is_service. "*" . $$txcommission . "*" . $$txcommissionforain . "");

				$currentattachedval = str_replace("'","",$$txtattachedvalue);
				$currentattachedvalue += number_format($currentattachedval,2,'.','');
			}
			else 
			{
				if ($data_array != "")
					$data_array .= ",";

				$data_array = "(" . $id . "," . $txt_system_id . "," . $$hiddenwopobreakdownid . "," . $$txtattachedqnty . "," . $$hiddenunitprice . "," . $$txtattachedvalue . "," . $$txtfabdescrip . "," . $$txtcategory . "," . $$txthscode . "," . $$cbopostatus . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "','" . $is_Sales . "'," . $cbo_export_item_category . "," . $is_service. "," . $$txcommission . "," . $$txcommissionforain . ")";
				$id = $id + 1;

				$currentattachedval = str_replace("'","",$$txtattachedvalue);
				$currentattachedvalue += number_format($currentattachedval,2,'.','');
			}
        }


        $lc_value=return_field_value("lc_value","com_export_lc","id=".$txt_system_id);
        $tolerance=return_field_value("tolerance","com_export_lc","id=".$txt_system_id);
        $without_update_dtls_cond="";
        if(count($id_array_update)>0){
            $without_update_dtls_cond = " and b.id not in (".implode(",",$id_array_update).")";
        }
        $pre_tot_attached =  sql_select("select sum(b.attached_value) as attached_value from com_export_lc a, com_export_lc_order_info b where a.id = b.com_export_lc_id and a.id=".$txt_system_id." $without_update_dtls_cond and b.status_active = 1 and b.is_deleted = 0");

        $tolerance_value=($lc_value/100)*$tolerance;
        $lc_value = number_format($lc_value,2,".","")+number_format($tolerance_value,2,".","");
        $tot_attached = number_format($pre_tot_attached[0][csf("attached_value")],2,".","");
		
        if(number_format(($tot_attached + $currentattachedvalue),2,'.','')  > number_format($lc_value,2,'.','') )
        {
            echo "11** Attached Value Exceeds LC Value ".number_format(($tot_attached + $currentattachedvalue),2,'.','')." = ".number_format($lc_value,2,'.','');disconnect($con);die;
        }

        //echo "11** $tot_attached + $currentattachedvalue > $lc_value";die;

        //echo "insert into com_sales_contract_order_info (".$field_array.") values".$data_array;die;

        $flag = $rID2 =$rID=$rID3 =1;

        if ($data_array != "") {
            $rID2 = sql_insert("com_export_lc_order_info", $field_array, $data_array, 0);
            if ($flag == 1) {
                if ($rID2)
                    $flag = 1;
                else
                    $flag = 0;
            }
        }

        if(count($id_array_update)>0)
		{
			$rID=execute_query(bulk_update_sql_statement( "com_export_lc_order_info", "id", $field_array_update, $data_array_update, $id_array_update ));
            //$rID = sql_update("com_export_lc_order_info", $field_array_update, $data_array_update, "id", "" . $hiddenexportlcorderid . "", 1);
            if ($flag == 1) {
                if ($rID)
                    $flag = 1;
                else
                    $flag = 0;
            }
			
			if($detach_order_id!="" && $all_sc_id!="" && $cbo_replacement_lc==1)
			{
				$rID3 =execute_query("update com_sales_contract_order_info set status_active=1, is_deleted=0 where com_sales_contract_id in($all_sc_id) and wo_po_break_down_id = $detach_order_id ");
				if ($flag == 1) {
					if ($rID3)
						$flag = 1;
					else
						$flag = 0;
				}
			}
			
			
        }
		
		
		//echo "10** $rID2 =$rID=$rID3";oci_rollback($con);disconnect($con);die;

        if ($db_type == 0) {
            if ($flag == 1) {
                mysql_query("COMMIT");
                echo "1**" . str_replace("'", '', $txt_system_id) . "**0**".str_replace("'", '', $cbo_export_item_category);
            } else {
                mysql_query("ROLLBACK");
                echo "6**0**1";
            }
        } else if ($db_type == 2 || $db_type == 1) {
            if ($flag == 1) {
                oci_commit($con);
                echo "1**" . str_replace("'", '', $txt_system_id) . "**0**".str_replace("'", '', $cbo_export_item_category);
            } else {
                oci_rollback($con);
                echo "6**0**1";
            }
        }
        disconnect($con);
        die;
    }
}

if ($action == "btb_lc_search") {
    echo load_html_head_contents("BTB L/C Info", "../../../", 1, 1, '', '', '');
    extract($_REQUEST);

    //$item_category_mix = array(110 => 'Knit Fabric');
    $item_category_mix = array(2 => 'Knit Fabric');
    ?>

    <script>
        function js_set_value(id)
        {
            $('#hidden_btb_id').val(id);
            parent.emailwindow.hide();
        }

    </script>

    </head>

    <body>
        <div align="center" style="width:1000px;">
            <form name="searchscfrm"  id="searchscfrm">
                <fieldset style="width:100%; margin-left:15px">
                    <legend>Enter search words</legend>           
                    <table cellpadding="0" cellspacing="0" border="1" rules="all" width="980" class="rpt_table">
                        <thead>
                        <th>Item Category</th>
                        <th>Company</th>
                        <th>Supplier</th>
                        <th>L/C Date</th>
                        <th>System Id</th>
                        <th>LC No</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:80px" class="formbutton" />
                            <input type="hidden" name="id_field" id="id_field" value="" />
                        </th>
                        </thead>
                        <tr class="general">
                            <td> 
                                <? //function create_drop_down($field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index, $tab_index, $new_conn, $field_name, $additionalClass, $additionalAttributes)
								echo create_drop_down("cbo_item_category_id", 140, $export_item_category, '', 1, '--Select--', 0, "", 0,'10,23,35,36,37,45'); ?>  
                            </td>
                            <td>
                                <?
                                echo create_drop_down("cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", 'id,company_name', 1, 'Select', 0, "", 0);
                                ?>  
                            </td>
                            <td align="center" id="supplier_td">
                                <? echo create_drop_down("cbo_supplier_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", 'id,company_name', 1, '----Select----', 0, 0, 0); ?>       
                            </td>            
                            <td> 
                                <input type="text" name="btb_start_date" id="btb_start_date" class="datepicker" style="width:70px;" />To
                                <input type="text" name="btb_end_date" id="btb_end_date" class="datepicker" style="width:70px;" />
                            </td>						
                            <td id="search_by_td">
                                <input type="text" style="width:90px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                                <input type="hidden" id="hidden_btb_id" />
                            </td>
                            <td >
                                <input type="text" style="width:90px" class="text_boxes"  name="txt_lc_no" id="txt_lc_no" />
                            </td>                       
                            <td>
                                <input type="button" id="search_button" class="formbutton" value="Show" onClick="show_list_view(document.getElementById('cbo_company_id').value + '**' + document.getElementById('cbo_item_category_id').value + '**' + document.getElementById('cbo_supplier_id').value + '**' + document.getElementById('btb_start_date').value + '**' + document.getElementById('btb_end_date').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_lc_no').value, 'create_btb_search_list_view', 'search_div', 'export_lc_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
                            </td>
                        </tr>
                    </table>
                    <div id="search_div" style="margin-top:5px"></div>
                </fieldset>
            </form>
        </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script type="text/javascript">
    	$("#cbo_item_category_id").val(0);
    </script>
    </html>
    <?
    exit();
}

if ($action == "create_btb_search_list_view") {
    $data = explode('**', $data);
    $company_id = $data[0];
    $item_category_id = $data[1];
    $supplier_id = $data[2];
    $lc_start_date = $data[3];
    $lc_end_date = $data[4];
    $system_id = $data[5];
    $lc_num = $data[6];

    if ($company_id == 0) {
        echo 'Select Importer';
        die;
    }

    if ($company_id != 0)
        $company = $company_id;
    /*if ($item_category_id == 0)
        $item_category_cond = "%%";
    else
        $item_category_cond = $item_category_id;*/

    $item_category_cond = "%%";
    if($item_category_id>0)
    {
        $item_category_cond = $category_wise_entry_form[$maping_export_import_category[$item_category_id]];
    } 

    if ($supplier_id != 0)
        $supplier = $supplier_id;
    else
        $supplier = '%%';
    if ($system_id != '')
        $system_number = $system_id;
    else
        $system_number = '%';
    if ($lc_num != '')
        $lc_number_cond = " and a.lc_number like '%" . $lc_num . "'";
    else
        $lc_number_cond = '';

    if ($lc_start_date != '' && $lc_end_date != '') {
        if ($db_type == 0) {
            $date = "and a.application_date between '" . change_date_format($lc_start_date, 'yyyy-mm-dd') . "' and '" . change_date_format($lc_end_date, 'yyyy-mm-dd') . "'";
        } else if ($db_type == 2) {
            $date = "and a.application_date between '" . change_date_format($lc_start_date, '', '', 1) . "' and '" . change_date_format($lc_end_date, '', '', 1) . "'";
        }
    } else {
        $date = "";
    }

    if ($db_type == 0)
        $year_field = "YEAR(a.insert_date) as year,";
    else if ($db_type == 2)
        $year_field = "to_char(a.insert_date,'YYYY') as year,";
    else
        $year_field = ""; //defined Later

    //$sql = "SELECT id, $year_field btb_prefix_number, btb_system_id, lc_number, supplier_id, application_date, last_shipment_date, lc_date, lc_value, item_category_id, importer_id FROM com_btb_lc_master_details WHERE btb_system_id like '%" . $system_number . "' and importer_id = '" . $company . "' and supplier_id like '" . $supplier . "' and item_category_id like '" . $item_category_cond . "' $date $lc_number_cond and status_active = 1 and is_deleted = 0 order by item_category_id, id";

    $sql = " SELECT a.id, $year_field a.btb_prefix_number, a.btb_system_id, a.lc_number, a.supplier_id, a.application_date, a.last_shipment_date, a.lc_date, a.lc_value, c.item_category_id, a.importer_id, b.pi_id , c.pi_number, c.import_pi, c.export_pi_id
    from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_master_details c
    where a.btb_system_id like '%" . $system_number . "' and a.importer_id = '" . $company . "' and a.supplier_id like '" . $supplier . "' and a.pi_entry_form like '" . $item_category_cond . "' $date $lc_number_cond and a.status_active=1 and a.is_deleted=0 and a.id=b.com_btb_lc_master_details_id and b.pi_id=c.id and c.import_pi=1 and c.export_pi_id>0 and c.export_pi_id is not null
    order by c.item_category_id, id";

    // echo $sql;
    $item_category_mix = array(2 => 'Knit Fabric');
    $comp = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
    $supp = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
    $within_group = return_library_array("select id, within_group from com_export_pi_mst", 'id', 'within_group');

    $imported_btb_arr = array();
    $importData = sql_select("select import_btb_id from com_export_lc where import_btb=1 and status_active=1 and is_deleted=0");
    foreach ($importData as $row) {
        $imported_btb_arr[$row[csf('import_btb_id')]] = $row[csf('import_btb_id')];
    }
    ?>
    <table width="990" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
        <thead> 
        <th width="40">SL</th>
        <th width="110">Item Category</th>
        <th width="55">Year</th>
        <th width="65">System Id</th>
        <th width="150">Supplier</th>
        <th width="150">L/C Number</th>
        <th width="80">L/C Date</th>
        <th width="100">L/C Value</th>
        <th width="100">Application Date</th>
        <th>Last Ship Date</th>
    </thead>
    </table>
    <div style="width:990px; overflow-y:scroll; max-height:280px">  
        <table width="970" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="list_view"> 
            <?
            $data_array = sql_select($sql);
            $i = 1;
            foreach ($data_array as $row) {
                if ($imported_btb_arr[$row[csf('id')]] == "") {
                    if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";
                    if($within_group[$row[csf('export_pi_id')]] == 2)
                    {
                        $supplier = $supp[$row[csf('supplier_id')]];
                    }else{
                        $supplier = $comp[$row[csf('supplier_id')]];
                    }
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer;" id="search<? echo $i; ?>" onClick="js_set_value('<? echo $row[csf('id')]."__".$row[csf('item_category_id')]; ?>');">
                        <td width="40"><? echo $i; ?></td>
                        <td width="110"><? echo $item_category[$row[csf('item_category_id')]]; ?></td>
                        <td width="55" align="center"><? echo $row[csf('year')]; ?></td>
                        <td width="65"><? echo $row[csf('btb_prefix_number')]; ?></td>
                        <td width="150" placeholder="<? echo $row[csf('supplier_id')].'*'.$within_group[$row[csf('export_pi_id')]];?>"><p><? echo $supplier; ?></p></td>
                        <td width="150"><p><? echo $row[csf('lc_number')]; ?></p></td>
                        <td width="80" align="center"><p><? echo change_date_format($row[csf('lc_date')]); ?></p></td>
                        <td width="100" align="right"><? echo number_format($row[csf('lc_value')], 2); ?>&nbsp;</td>
                        <td width="100" align="center"><? echo change_date_format($row[csf('application_date')]); ?>&nbsp;</td>
                        <td align="center"><? echo change_date_format($row[csf('last_shipment_date')]); ?>&nbsp;</td>
                    </tr>
                    <?
                    $i++;
                }
            }
            ?>
        </table>
    </div>
    <?
    exit();
}

if ($action == 'populate_data_from_btb_lc') 
{
    $data_array = sql_select("select id, lc_number, supplier_id, importer_id, lc_date, last_shipment_date, application_date, lc_expiry_date, lc_value, currency_id, issuing_bank_id, item_category_id, pi_entry_form,  tenor, tolerance, inco_term_id, inco_term_place, payterm_id, delivery_mode_id, doc_presentation_days from com_btb_lc_master_details where id='$data'");
    foreach ($data_array as $row) {
        $issuing_bank = return_field_value("bank_name", "lib_bank", "id='" . $row[csf("issuing_bank_id")] . "'");

        echo "document.getElementById('cbo_beneficiary_name').value 		= '" . $row[csf("supplier_id")] . "';\n";

        echo "load_drop_down('requires/export_lc_controller', '" . $row[csf("supplier_id")] . "'+'**1', 'load_drop_down_buyer', 'buyer_td_id' );\n";
        echo "load_drop_down('requires/export_lc_controller', '" . $row[csf("supplier_id")] . "', 'load_drop_down_applicant_name', 'applicant_name_td' );\n";
        echo "load_drop_down('requires/export_lc_controller', '" . $row[csf("supplier_id")] . "', 'load_drop_down_notifying_party', 'notifying_party_td' );\n";
        echo "load_drop_down('requires/export_lc_controller', '" . $row[csf("supplier_id")] . "', 'load_drop_down_consignee', 'consignee_td' );\n";

        $max_btb_limit = return_field_value("max_btb_limit", "variable_settings_commercial", "company_name='" . $row[csf("supplier_id")] . "' and variable_list=6 and is_deleted = 0 AND status_active = 1");
        echo "document.getElementById('txt_max_btb_limit').value = '" . $max_btb_limit . "';\n";

        $internal_file_source = return_field_value("internal_file_source", "variable_settings_commercial", "company_name='" . $row[csf("supplier_id")] . "' and variable_list=20 and is_deleted = 0 AND status_active = 1");
        echo "$('#txt_internal_file_no').val('');\n";
        if ($internal_file_source == 1) {
            echo "$('#txt_internal_file_no').attr('onDblClick','fn_file_no()');\n";
            echo "$('#txt_internal_file_no').attr('readonly',true);\n";
            echo "$('#txt_internal_file_no').attr('placeholder','Double Click');\n";
        } else {
            echo "$('#txt_internal_file_no').removeAttr('onDblClick');\n";
            echo "$('#txt_internal_file_no').attr('readonly',false);\n";
            echo "$('#txt_internal_file_no').removeAttr('placeholder');\n";
        }

        //$itemCategoryId = $row[csf("item_category_id")] - 100;
        /*$itemCategoryId = $row[csf("item_category_id")];// - 100;
        echo '$("#cbo_export_item_category option[value!=\'0\']").remove();'."\n";
        echo '$("#cbo_export_item_category").append("<option selected value=\''.$itemCategoryId.'\'>'.$item_category[$itemCategoryId].'</option>");'."\n";
        //$itemCategoryId = $row[csf("item_category_id")];
        $pi_entry_form = $row[csf("pi_entry_form")];
        if ($pi_entry_form==166) 
        {
            echo "document.getElementById('cbo_export_item_category').value     = '10';\n";
        }*/
		
		$pi_item_category = return_field_value("a.item_category_id", "com_pi_master_details a, com_btb_lc_pi b", "a.id=b.pi_id and b.com_btb_lc_master_details_id=".$row[csf("id")]." and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ","item_category_id"); 
		echo "document.getElementById('cbo_export_item_category').value	= '" . $maping_import_export_category[$pi_item_category] . "';\n";       
        echo "document.getElementById('txt_lc_number').value 				= '" . $row[csf("lc_number")] . "';\n";
        echo "document.getElementById('cbo_buyer_name').value 				= '" . $row[csf("importer_id")] . "';\n";
        echo "document.getElementById('txt_lc_date').value 					= '" . change_date_format($row[csf("lc_date")]) . "';\n";
        echo "document.getElementById('txt_last_shipment_date').value 		= '" . change_date_format($row[csf("last_shipment_date")]) . "';\n";
        echo "document.getElementById('txt_expiry_date').value 				= '" . change_date_format($row[csf("lc_expiry_date")]) . "';\n";
        echo "document.getElementById('txt_lc_value').value 				= '" . $row[csf("lc_value")] . "';\n";
        echo "document.getElementById('cbo_currency_name').value 			= '" . $row[csf("currency_id")] . "';\n";
        echo "document.getElementById('txt_issuing_bank').value				= '" . $issuing_bank . "';\n";
        //echo "document.getElementById('cbo_export_item_category').value		= '" . $itemCategoryId . "';\n";
        echo "document.getElementById('txt_tenor').value					= '" . $row[csf("tenor")] . "';\n";
        echo "document.getElementById('txt_tolerance').value				= '" . $row[csf("tolerance")] . "';\n";
        echo "document.getElementById('cbo_inco_term').value 				= '" . $row[csf("inco_term_id")] . "';\n";
        echo "document.getElementById('txt_inco_term_place').value 			= '" . $row[csf("inco_term_place")] . "';\n";
        echo "document.getElementById('txt_doc_presentation_days').value 	= '" . $row[csf("doc_presentation_days")] . "';\n";
        echo "document.getElementById('cbo_pay_term').value 				= '" . $row[csf("payterm_id")] . "';\n";
        echo "document.getElementById('import_btb').value 					= '1';\n";
        echo "document.getElementById('import_btb_id').value 				= '" . $row[csf("id")] . "';\n";

        echo "set_multiselect('cbo_notifying_party*cbo_consignee','0*0','0','','0*0');\n";

        echo "disable_enable_fields('cbo_beneficiary_name*txt_lc_number*cbo_buyer_name*txt_lc_date*txt_expiry_date*txt_lc_value*cbo_currency_name*txt_issuing_bank*cbo_export_item_category*txt_tenor*txt_tolerance*cbo_inco_term*txt_inco_term_place*txt_doc_presentation_days*cbo_pay_term',1);\n";

        exit();
    }
}

if ($action == 'import_pi_details') {

    $data = explode("**", $data);
    $btbId = $data[0];
    $exportLcId = $data[1];
	$item_category_id = $data[2];
	if ($exportLcId > 0) 
	{
		$button_status = 1;
		$item_category_id=$maping_export_import_category[$item_category_id];
	} 
	else 
	{
		$button_status = 0;
	}
	$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
	$size_library=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name"  );
	//echo $item_category_id.test;die;
	if($item_category_id==2)
	{
		
		?>
		<table class="rpt_table" width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" id="tbl_pi_item">
			<thead>
			<th>Job No</th>
			<th class="must_entry_caption">Construction</th>
			<th>Composition</th>
			<th class="must_entry_caption">Color</th>					
			<th>GSM</th>
			<th class="must_entry_caption">Dia/Width</th>
			<th>UOM</th>
			<th class="must_entry_caption">Quantity</th>
			<th class="must_entry_caption">Rate</th>
			<th>Amount</th>
		</thead>    
		<tbody id="pi_details_container">
			<?
			$tblRow = 0;
			if ($exportLcId > 0) {
				$sql = "SELECT id, pi_id, pi_dtls_id, work_order_no, wo_po_break_down_id as work_order_id, determination_id, color_id, construction, composition, gsm, dia_width, uom, attached_qnty as quantity, attached_rate as rate, attached_value as amount, is_sales from com_export_lc_order_info where com_export_lc_id=$exportLcId and status_active=1 and is_deleted=0 and is_sales=1";
			} else {
				
				//$piIds = return_field_value("pi_id", "com_btb_lc_master_details", "id=$btbId");
				$sql = "SELECT a.id as pi_dtls_id, a.pi_id, a.work_order_no, a.work_order_id, a.determination_id, a.color_id, a.fabric_construction as construction, a.fabric_composition as composition, a.gsm, a.dia_width, a.uom, a.quantity, a.rate, a.amount, a.is_sales 
				from com_pi_item_details a, com_btb_lc_pi b
				where a.pi_id=b.pi_id and b.com_btb_lc_master_details_id='$btbId' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			}
			//echo  $sql;
			$data_array = sql_select($sql);
			foreach ($data_array as $row) {
				$tblRow++;
	
				if ($tblRow % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
					<td>
						<input type="text" name="workOrderNo_<? echo $tblRow; ?>" id="workOrderNo_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('work_order_no')]; ?>" style="width:110px;" disabled="disabled" />			
						<input type="hidden" name="hideWoId_<? echo $tblRow; ?>" id="hideWoId_<? echo $tblRow; ?>" value="<? echo $row[csf('work_order_id')]; ?>" readonly />
						<input type="hidden" name="hidePiId_<? echo $tblRow; ?>" id="hidePiId_<? echo $tblRow; ?>" value="<? echo $row[csf('pi_id')]; ?>" readonly />
						<input type="hidden" name="hidePiDtlsId_<? echo $tblRow; ?>" id="hidePiDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('pi_dtls_id')]; ?>" readonly />
						<input type="hidden" name="isSalesId_<? echo $tblRow; ?>" id="isSalesId_<? echo $tblRow; ?>" value="<? echo $row[csf('is_sales')]; ?>" readonly />
					</td>
					<td> 
						<input type="text" name="construction_<? echo $tblRow; ?>" id="construction_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('construction')]; ?>" style="width:110px" disabled="disabled"/>
						<input type="hidden" name="hideDeterminationId_<? echo $tblRow; ?>" id="hideDeterminationId_<? echo $tblRow; ?>" value="<? echo $row[csf('determination_id')]; ?>" readonly />
					</td>
					<td>
						<input type="text" name="composition_<? echo $tblRow; ?>" id="composition_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('composition')]; ?>" style="width:110px" disabled="disabled"/>
					</td> 
					<td>
						<input type="text" name="colorName_<? echo $tblRow; ?>" id="colorName_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $color_library[$row[csf('color_id')]]; ?>" style="width:80px" disabled="disabled"/>
						<input type="hidden" name="colorId_<? echo $tblRow; ?>" id="colorId_<? echo $tblRow; ?>" value="<? echo $row[csf('color_id')]; ?>"/>
					</td>
					<td>
						<input type="text" name="gsm_<? echo $tblRow; ?>" id="gsm_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('gsm')]; ?>" style="width:60px" disabled="disabled"/>
					</td>
					<td>
						<input type="text" name="diawidth_<? echo $tblRow; ?>" id="diawidth_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('dia_width')]; ?>" style="width:70px" disabled="disabled"/>
					</td>
					<td>
						<? echo create_drop_down("uom_" . $tblRow, 70, $unit_of_measurement, '', 1, ' Display ', $row[csf('uom')], '', 1, ''); ?>						 
					</td>
					<td>
						<input type="text" name="quantity_<? echo $tblRow; ?>" id="quantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('quantity')]; ?>" style="width:61px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" disabled/>
					</td>
					<td>
						<input type="text" name="rate_<? echo $tblRow; ?>" id="rate_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('rate')]; ?>" style="width:60px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" disabled/>
					</td>
					<td>
						<input type="text" name="amount_<? echo $tblRow; ?>" id="amount_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('amount')]; ?>" style="width:75px;" disabled/>
						<input type="hidden" name="updateIdDtls_<? echo $tblRow; ?>" id="updateIdDtls_<? echo $tblRow; ?>" value="<? echo $row[csf('id')]; ?>"/>
					</td>
				</tr>
				<?
			}
			?>
		</tbody> 
		</table>  
		<?
	}
	else if($item_category_id==4)
	{
		$item_group_arr=return_library_array( "select id, item_name from lib_item_group where status_active=1 and is_deleted=0", "id", "item_name"  );
		?>
        <table class="rpt_table" width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" id="tbl_pi_item">
        	<thead>
                <th>Job No</th>
                <th>WO No</th>
                <th class="must_entry_caption">Item Group</th>
                <th class="must_entry_caption">Item Description</th>
                <th>Item Color</th>
                <th>Item Size</th>
                <th>UOM</th>
                <th class="must_entry_caption">Quantity</th>
                <th class="must_entry_caption">Rate</th>
                <th>Amount</th>
            </thead>
            <tbody id="pi_details_container">
            <?
                $tblRow=0;
				if ($exportLcId > 0) {
				$sql = "SELECT id, pi_id, pi_dtls_id, work_order_no, wo_po_break_down_id as work_order_id, booking_no, item_group_id, fabric_description as item_desc, color_id as item_color_id, size_id as item_size, uom, attached_qnty as quantity, attached_rate as rate, attached_value as amount, attached_rate as net_pi_rate, attached_value as net_pi_amount, is_sales 
				from com_export_lc_order_info where com_export_lc_id=$exportLcId and status_active=1 and is_deleted=0 and is_sales=1";
				} 
				else 
				{
					
					//$piIds = return_field_value("pi_id", "com_btb_lc_master_details", "id=$btbId");
					//$sql = "SELECT id as pi_dtls_id, pi_id, work_order_no, work_order_id, determination_id, color_id, fabric_construction as construction, fabric_composition as composition, gsm, dia_width, uom, quantity, rate, amount, is_sales from com_pi_item_details where pi_id in($piIds) and status_active=1 and is_deleted=0 and is_sales=1";
					$sql = "select a.id as pi_dtls_id, a.pi_id, a.work_order_no, a.work_order_id, a.work_order_dtls_id, a.booking_no, a.item_group as item_group_id, a.item_description as item_desc, a.color_id as item_color_id, a.size_id as item_size, a.uom, a.quantity, a.rate, a.amount, a.net_pi_rate, a.net_pi_amount, a.is_sales 
					from com_pi_item_details a, com_btb_lc_pi b
					where a.pi_id=b.pi_id and b.com_btb_lc_master_details_id='$btbId' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
				}
				
				//echo $sql;
                $data_array=sql_select($sql);
                foreach($data_array as $row)
                {
                    $tblRow++;
    
                    if($tblRow%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
                        <td>
                            <input type="text" name="workOrderNo_<? echo $tblRow; ?>" id="workOrderNo_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('work_order_no')]; ?>" style="width:110px;" disabled="disabled" />
                            <input type="hidden" name="hideWoId_<? echo $tblRow; ?>" id="hideWoId_<? echo $tblRow; ?>" value="<? echo $row[csf('work_order_id')]; ?>" readonly />
                            <input type="hidden" name="hidePiId_<? echo $tblRow; ?>" id="hidePiId_<? echo $tblRow; ?>" value="<? echo $row[csf('pi_id')]; ?>" readonly />
                            <input type="hidden" name="hidePiDtlsId_<? echo $tblRow; ?>" id="hidePiDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('pi_dtls_id')]; ?>" readonly />
                            <input type="hidden" name="isSalesId_<? echo $tblRow; ?>" id="isSalesId_<? echo $tblRow; ?>" value="<? echo $row[csf('is_sales')]; ?>" readonly />
                        </td>
                        <td>
                            <input type="text" name="bookingNo_<? echo $tblRow; ?>" id="bookingNo_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('booking_no')]; ?>" style="width:110px" disabled="disabled"/>
                        </td>
                        <td>
                            <input type="text" name="itemgroupid_<? echo $tblRow; ?>" id="itemgroupid_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $item_group_arr[$row[csf('item_group_id')]]; ?>" placeholder="<? echo $row[csf('item_group_id')]; ?>" style="width:100px" disabled="disabled"/>
                        </td>
                        <td>
                            <input type="text" name="itemdescription_<? echo $tblRow; ?>" id="itemdescription_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('item_desc')]; ?>" style="width:110px" disabled="disabled"/>
                        </td>
                        <td>
                            <input type="text" name="itemColor_<? echo $tblRow; ?>" id="itemColor_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $color_library[$row[csf('item_color_id')]]; ?>" placeholder="<? echo $row[csf('item_color_id')]; ?>" style="width:70px" disabled="disabled" />
                        </td>
                        <td>
                            <input type="text" name="itemSize_<? echo $tblRow; ?>" id="itemSize_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $size_library[$row[csf('item_size')]]; ?>" placeholder="<? echo $row[csf('item_size')]; ?>" style="width:70px" disabled="disabled" />
                        </td>
                        <td>
                            <input type="text" name="uom_<? echo $tblRow; ?>" id="uom_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $unit_of_measurement[$row[csf('uom')]]; ?>" placeholder="<? echo $row[csf('uom')]; ?>" style="width:100px" disabled="disabled"/>
                        </td>
                        <td>
                            <input type="text" name="quantity_<? echo $tblRow; ?>" id="quantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('quantity')]; ?>" style="width:70px;" disabled="disabled" />
                        </td>
                        <td>
                        <input type="text" name="rate_<? echo $tblRow; ?>" id="rate_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('net_pi_rate')]; ?>" style="width:70px;" disabled="disabled" placeholder="<? echo $row[csf('rate')]; ?>" />
                        </td>
                        <td>
                            <input type="text" name="amount_<? echo $tblRow; ?>" id="amount_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('net_pi_amount')]; ?>" style="width:80px;" disabled="disabled" placeholder="<? echo $row[csf('amount')]; ?>" />
                            <input type="hidden" name="updateIdDtls_<? echo $tblRow; ?>" id="updateIdDtls_<? echo $tblRow; ?>" value="<? echo $row[csf('id')]; ?>"/>
                        </td>
                    </tr>
                	<?
                }
				
            ?>
            </tbody>
        </table>
        <?
	}
	else if($item_category_id==74)
	{
		?>
        <table class="rpt_table" width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" id="tbl_pi_item">
        	<thead>
                <th>Job No</th>
                <th>Booking No</th>
                <th class="must_entry_caption">Gmts Color</th>
                <th class="must_entry_caption">AOP Color</th>
                <th>GSM</th>
                <th>Body part</th>
                <th>UOM</th>
                <th class="must_entry_caption">Quantity</th>
                <th class="must_entry_caption">Rate</th>
                <th>Amount</th>
            </thead>
            <tbody id="pi_details_container">
            <?
                $tblRow=0;
				if ($exportLcId > 0) 
				{
					$sql = "SELECT id, pi_id, pi_dtls_id, work_order_no, wo_po_break_down_id as work_order_id, booking_no, color_id as item_color_id, aop_color as aop_color_id, gsm, body_part_id as body_part, uom, attached_qnty as quantity, attached_rate as rate, attached_value as amount, attached_rate as net_pi_rate, attached_value as net_pi_amount, is_sales 
					from com_export_lc_order_info where com_export_lc_id=$exportLcId and status_active=1 and is_deleted=0 and is_sales=1";
				} 
				else 
				{
					$sql = "select a.id as pi_dtls_id, a.pi_id, a.work_order_no, a.work_order_id, a.work_order_dtls_id, a.booking_no, a.color_id as item_color_id, a.item_color as aop_color_id, a.gsm, a.body_part_id as body_part, a.uom, a.quantity, a.rate, a.amount, a.net_pi_rate, a.net_pi_amount, a.is_sales 
					from com_pi_item_details a, com_btb_lc_pi b
					where a.pi_id=b.pi_id and b.com_btb_lc_master_details_id='$btbId' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
				}
				//$sql = "select id, work_order_no, work_order_id, work_order_dtls_id, booking_no, color_id as item_color_id, item_color as aop_color_id, gsm, body_part_id as body_part, uom, quantity, rate, amount, net_pi_rate, net_pi_amount from com_pi_item_details where pi_id='$import_id' and status_active=1 and is_deleted=0";
				
				//echo $sql;
				
                $data_array=sql_select($sql);
                foreach($data_array as $row)
                {
                    $tblRow++;
    
                    if($tblRow%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
                        <td>
                            <input type="text" name="workOrderNo_<? echo $tblRow; ?>" id="workOrderNo_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('work_order_no')]; ?>" style="width:110px;" disabled="disabled" />
                            <input type="hidden" name="hideWoId_<? echo $tblRow; ?>" id="hideWoId_<? echo $tblRow; ?>" value="<? echo $row[csf('work_order_id')]; ?>" readonly />
                            <input type="hidden" name="hidePiId_<? echo $tblRow; ?>" id="hidePiId_<? echo $tblRow; ?>" value="<? echo $row[csf('pi_id')]; ?>" readonly />
                            <input type="hidden" name="hidePiDtlsId_<? echo $tblRow; ?>" id="hidePiDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('pi_dtls_id')]; ?>" readonly />
                            <input type="hidden" name="isSalesId_<? echo $tblRow; ?>" id="isSalesId_<? echo $tblRow; ?>" value="<? echo $row[csf('is_sales')]; ?>" readonly />
                        </td>
                        <td>
                            <input type="text" name="bookingNo_<? echo $tblRow; ?>" id="bookingNo_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('booking_no')]; ?>" style="width:110px" disabled="disabled"/>
                        </td>
                        <td>
                            <input type="text" name="itemColor_<? echo $tblRow; ?>" id="itemColor_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $color_library[$row[csf('item_color_id')]]; ?>" placeholder="<? echo $row[csf('item_color_id')]; ?>" style="width:100px" disabled="disabled"/>
                        </td>
                        <td>
                            <input type="text" name="aopColor_<? echo $tblRow; ?>" id="aopColor_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $color_library[$row[csf('aop_color_id')]]; ?>" style="width:110px" placeholder="<? echo $row[csf('aop_color_id')]; ?>" disabled="disabled"/>
                        </td>
                        <td>
                            <input type="text" name="gsm_<? echo $tblRow; ?>" id="gsm_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('gsm')]; ?>"  style="width:70px" disabled="disabled" />
                        </td>
                        <td>
                            <input type="text" name="bodyPart_<? echo $tblRow; ?>" id="bodyPart_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $body_part[$row[csf('body_part')]]; ?>" placeholder="<? echo $row[csf('body_part')]; ?>" style="width:70px" disabled="disabled" />
                        </td>
                        <td>
                            <input type="text" name="uom_<? echo $tblRow; ?>" id="uom_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $unit_of_measurement[$row[csf('uom')]]; ?>" placeholder="<? echo $row[csf('uom')]; ?>" style="width:100px" disabled="disabled"/>
                        </td>
                        <td>
                            <input type="text" name="quantity_<? echo $tblRow; ?>" id="quantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('quantity')]; ?>" style="width:70px;" disabled="disabled" />
                        </td>
                        <td>
                        <input type="text" name="rate_<? echo $tblRow; ?>" id="rate_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('net_pi_rate')]; ?>" style="width:70px;" disabled="disabled" placeholder="<? echo $row[csf('rate')]; ?>" />
                        </td>
                        <td>
                            <input type="text" name="amount_<? echo $tblRow; ?>" id="amount_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('net_pi_amount')]; ?>" style="width:80px;" disabled="disabled" placeholder="<? echo $row[csf('amount')]; ?>" />
                            <input type="hidden" name="updateIdDtls_<? echo $tblRow; ?>" id="updateIdDtls_<? echo $tblRow; ?>" value="<? echo $row[csf('id')]; ?>"/>
                            <input type="hidden" name="bookingWithoutOrder_<? echo $tblRow; ?>" id="bookingWithoutOrder_<? echo $tblRow; ?>" value="0"/>
                        </td>
                    </tr>
                	<?
                }
            	?>
            </tbody>
        </table>
        <?    
	}
	else if($item_category_id==25 || $item_category_id==102 || $item_category_id==104)
	{
		?>
        <table class="rpt_table" width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" id="tbl_pi_item">
        	<thead>
                <th>Job No</th>
                <th>Booking No</th>
                <th>Gmts. Item</th>
                <th>Body Part</th>
                <th>Process /Embl. Name</th>
                <th>Embl. Type</th>
                <th>Description</th>
                <th>GMTS Color</th>
                <th>GMTS Size</th>
                <th>UOM</th>
                <th>Quantity</th>
                <th>Rate</th>
                <th>Amount</th>
            </thead>
            <tbody id="pi_details_container">
            <?
				//$emblishment_print_type
				//$emblishment_embroy_type
				if($item_catagory_id==25 || $item_catagory_id==104) $emb_type_arr=$emblishment_embroy_type; else $emb_type_arr=$emblishment_print_type;
                $tblRow=0;
				
				if ($exportLcId > 0) 
				{
					$sql = "SELECT id, pi_id, pi_dtls_id, work_order_no, wo_po_break_down_id as work_order_id, booking_no, gmts_item_id, body_part_id as body_part, embell_name as emb_name, embell_type as emb_type, fabric_description as item_desc, color_id as gmt_color_id, size_id as gmt_size, uom, attached_qnty as quantity, attached_rate as rate, attached_value as amount, attached_rate as net_pi_rate, attached_value as net_pi_amount, is_sales from com_export_lc_order_info where com_export_lc_id=$exportLcId and status_active=1 and is_deleted=0 and is_sales=1";
				} 
				else 
				{
					$sql = "select a.id as pi_dtls_id, a.pi_id, a.work_order_no, a.work_order_id, a.work_order_dtls_id, a.booking_no, a.gmts_item_id, a.body_part_id as body_part, a.embell_name as emb_name, a.embell_type as emb_type, a.item_description as item_desc, a.color_id as gmt_color_id, a.size_id as gmt_size, a.uom, a.quantity, a.rate, a.amount, a.net_pi_rate, a.net_pi_amount, a.is_sales 
					from com_pi_item_details a, com_btb_lc_pi b
					where a.pi_id=b.pi_id and b.com_btb_lc_master_details_id='$btbId' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
				}
				
				//$sql = "select id, work_order_no, work_order_id, work_order_dtls_id, booking_no, gmts_item_id, body_part_id as body_part, embell_name as emb_name, embell_type as emb_type, item_description as item_desc, color_id as gmt_color_id, size_id as gmt_size, uom, quantity, rate, amount, net_pi_rate, net_pi_amount from com_pi_item_details where pi_id='$import_id' and status_active=1 and is_deleted=0";
				//echo $sql;
                $data_array=sql_select($sql);
                foreach($data_array as $row)
                {
                    $tblRow++;
    
                    if($tblRow%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
                        <td>
                            <input type="text" name="workOrderNo_<? echo $tblRow; ?>" id="workOrderNo_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('work_order_no')]; ?>" style="width:110px;" disabled="disabled" />
                            <input type="hidden" name="hideWoId_<? echo $tblRow; ?>" id="hideWoId_<? echo $tblRow; ?>" value="<? echo $row[csf('work_order_id')]; ?>" readonly />
                            <input type="hidden" name="hidePiId_<? echo $tblRow; ?>" id="hidePiId_<? echo $tblRow; ?>" value="<? echo $row[csf('pi_id')]; ?>" readonly />
                            <input type="hidden" name="hidePiDtlsId_<? echo $tblRow; ?>" id="hidePiDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('pi_dtls_id')]; ?>" readonly />
                            <input type="hidden" name="isSalesId_<? echo $tblRow; ?>" id="isSalesId_<? echo $tblRow; ?>" value="<? echo $row[csf('is_sales')]; ?>" readonly />
                        </td>
                        <td>
                            <input type="text" name="bookingNo_<? echo $tblRow; ?>" id="bookingNo_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('booking_no')]; ?>" style="width:110px" disabled="disabled"/>
                        </td>
                        <td>
                            <input type="text" name="gmtsItem_<? echo $tblRow; ?>" id="gmtsItem_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $garments_item[$row[csf('gmts_item_id')]]; ?>" placeholder="<? echo $row[csf('gmts_item_id')]; ?>" style="width:110px" disabled="disabled"/>
                        </td>
                        <td>
                            <input type="text" name="bodyPart_<? echo $tblRow; ?>" id="bodyPart_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $body_part[$row[csf('body_part')]]; ?>" placeholder="<? echo $row[csf('body_part')]; ?>" style="width:100px" disabled="disabled"/>
                        </td>
                        <td>
                            <input type="text" name="embName_<? echo $tblRow; ?>" id="embName_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $emblishment_name_array[$row[csf('emb_name')]]; ?>" placeholder="<? echo $row[csf('emb_name')]; ?>" style="width:70px" disabled="disabled"/>
                        </td>
                        <td>
                            <input type="text" name="embType_<? echo $tblRow; ?>" id="embType_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $emb_type_arr[$row[csf('emb_type')]]; ?>" placeholder="<? echo $row[csf('emb_type')]; ?>" style="width:70px" disabled="disabled"/>
                        </td>
                        <td>
                            <input type="text" name="itemdescription_<? echo $tblRow; ?>" id="itemdescription_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('item_desc')]; ?>" style="width:110px" disabled="disabled"/>
                        </td>
                        <td>
                            <input type="text" name="itemColor_<? echo $tblRow; ?>" id="itemColor_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $color_library[$row[csf('gmt_color_id')]]; ?>" placeholder="<? echo $row[csf('gmt_color_id')]; ?>" style="width:70px" disabled="disabled" />
                        </td>
                        <td>
                            <input type="text" name="itemSize_<? echo $tblRow; ?>" id="itemSize_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $size_library[$row[csf('gmt_size')]]; ?>" placeholder="<? echo $row[csf('gmt_size')]; ?>" style="width:60px" disabled="disabled" />
                        </td>
                        <td>
                            <input type="text" name="uom_<? echo $tblRow; ?>" id="uom_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $unit_of_measurement[$row[csf('uom')]]; ?>" placeholder="<? echo $row[csf('uom')]; ?>" style="width:60px" disabled="disabled"/>
                        </td>
                        <td>
                            <input type="text" name="quantity_<? echo $tblRow; ?>" id="quantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('quantity')]; ?>" style="width:70px;" disabled="disabled" />
                        </td>
                        <td>
                            <input type="text" name="rate_<? echo $tblRow; ?>" id="rate_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('net_pi_rate')]; ?>" style="width:70px;" disabled="disabled" placeholder="<? echo $row[csf('rate')]; ?>" />
                        </td>
                        <td>
                            <input type="text" name="amount_<? echo $tblRow; ?>" id="amount_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('net_pi_amount')]; ?>" style="width:80px;" disabled="disabled" placeholder="<? echo $row[csf('amount')]; ?>" />
                            <input type="hidden" name="updateIdDtls_<? echo $tblRow; ?>" id="updateIdDtls_<? echo $tblRow; ?>" value="<? echo $row[csf('id')]; ?>"/>
                            <input type="hidden" name="bookingWithoutOrder_<? echo $tblRow; ?>" id="bookingWithoutOrder_<? echo $tblRow; ?>" value="0"/>
                        </td>
                    </tr>
                	<?
                }
            ?>
            </tbody>
        </table>
        <?    
	}
	else if($item_category_id==103)
	{
		?>
        <table class="rpt_table" width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" id="tbl_pi_item">
        	<thead>
                <th>Job No</th>
                <th>Booking No</th>
                <th>Style Ref</th>
                <th>Gmts. Item</th>
                <th>Color</th>
                <th>Wash Desc</th>
                <th>Process</th>
                <th>Wash Type</th>
                <th>UOM</th>
                <th>Quantity</th>
                <th>Rate</th>
                <th>Amount</th>
            </thead>
            <tbody id="pi_details_container">
            	<?
                $tblRow=0;
				
				if ($exportLcId > 0) 
				{
					//"id,com_export_lc_id,wo_po_break_down_id,attached_qnty,attached_rate,attached_value,import_btb,import_btb_id,work_order_no,pi_id,pi_dtls_id,determination_id,construction,composition,color_id,gsm,dia_width,uom,inserted_by,insert_date,booking_no,item_group_id,size_id,aop_color,body_part_id,gmts_item_id,embell_name,embell_type,fabric_description";
					$sql = "SELECT id, pi_id, pi_dtls_id, work_order_no, wo_po_break_down_id as work_order_id, booking_no, gmts_item_id, color_id, fabric_description as item_desc, embell_name as process_id, embell_type as wash_type, uom, attached_qnty as quantity, attached_rate as rate, attached_value as amount, attached_rate as net_pi_rate, attached_value as net_pi_amount, is_sales, buyer_style_ref 
					from com_export_lc_order_info where com_export_lc_id=$exportLcId and status_active=1 and is_deleted=0 and is_sales=1";
				} 
				else 
				{
					$sql = "select a.id as pi_dtls_id, a.pi_id, a.work_order_no, a.work_order_id, a.work_order_dtls_id, a.booking_no, a.gmts_item_id, a.color_id as color_id, a.item_description as item_desc, a.embell_name as process_id, a.embell_type as wash_type, a.uom, a.quantity, a.rate, a.amount, a.net_pi_rate, a.net_pi_amount, a.is_sales, a.buyer_style_ref 
					from com_pi_item_details a, com_btb_lc_pi b
					where a.pi_id=b.pi_id and b.com_btb_lc_master_details_id='$btbId' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
				}
				//$sql = "select id, work_order_no, work_order_id, work_order_dtls_id, booking_no, gmts_item_id, color_id as color_id, item_description as item_desc, embell_name as process_id, embell_type as wash_type, uom, quantity, rate, amount, net_pi_rate, net_pi_amount from com_pi_item_details where pi_id='$import_id' and status_active=1 and is_deleted=0";
				// echo $sql;
                $data_array=sql_select($sql);
                foreach($data_array as $row)
                {
                    $tblRow++;    
					if($row[csf('process_id')]==1) $wash_process_type=$wash_wet_process;
					else if($row[csf('process_id')]==2) $wash_process_type=$wash_dry_process;
					else if($row[csf('process_id')]==3) $wash_process_type=$wash_laser_desing;				   
				   
				    if($tblRow%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
                        <td>
                            <input type="text" name="workOrderNo_<? echo $tblRow; ?>" id="workOrderNo_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('work_order_no')]; ?>" style="width:110px;" disabled="disabled" />
                            <input type="hidden" name="hideWoId_<? echo $tblRow; ?>" id="hideWoId_<? echo $tblRow; ?>" value="<? echo $row[csf('work_order_id')]; ?>" readonly />
                            <input type="hidden" name="hidePiId_<? echo $tblRow; ?>" id="hidePiId_<? echo $tblRow; ?>" value="<? echo $row[csf('pi_id')]; ?>" readonly />
                            <input type="hidden" name="hidePiDtlsId_<? echo $tblRow; ?>" id="hidePiDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('pi_dtls_id')]; ?>" readonly />
                            <input type="hidden" name="isSalesId_<? echo $tblRow; ?>" id="isSalesId_<? echo $tblRow; ?>" value="<? echo $row[csf('is_sales')]; ?>" readonly />
                        </td>
                        <td>
                            <input type="text" name="bookingNo_<? echo $tblRow; ?>" id="bookingNo_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('booking_no')]; ?>" style="width:110px" disabled="disabled"/>
                        </td>
                        <td>
                            <input type="text" name="styleRef_<? echo $tblRow; ?>" id="styleRef_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('buyer_style_ref')]; ?>" style="width:110px" disabled="disabled"/>
                        </td>
                        <td>
                            <input type="text" name="gmtsItem_<? echo $tblRow; ?>" id="gmtsItem_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $garments_item[$row[csf('gmts_item_id')]]; ?>" placeholder="<? echo $row[csf('gmts_item_id')]; ?>" style="width:110px" disabled="disabled"/>
                        </td>
                        <td>
                            <input type="text" name="itemColor_<? echo $tblRow; ?>" id="itemColor_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $color_library[$row[csf('color_id')]]; ?>" placeholder="<? echo $row[csf('color_id')]; ?>" style="width:70px" disabled="disabled" />
                        </td>                        
                        <td>
                            <input type="text" name="itemdescription_<? echo $tblRow; ?>" id="itemdescription_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('item_desc')]; ?>" style="width:110px" disabled="disabled"/>
                        </td>
                        <td>
                            <input type="text" name="embName_<? echo $tblRow; ?>" id="embName_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $wash_type[$row[csf('process_id')]]; ?>" placeholder="<? echo $row[csf('process_id')]; ?>" style="width:70px" disabled="disabled"/>
                        </td>
                        <td>
                            <input type="text" name="embType_<? echo $tblRow; ?>" id="embType_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $wash_process_type[$row[csf('wash_type')]]; ?>" placeholder="<? echo $row[csf('wash_type')]; ?>" style="width:70px" disabled="disabled"/>
                        </td>
                        <td>
                            <input type="text" name="uom_<? echo $tblRow; ?>" id="uom_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $unit_of_measurement[$row[csf('uom')]]; ?>" placeholder="<? echo $row[csf('uom')]; ?>" style="width:60px" disabled="disabled"/>
                        </td>
                        <td>
                            <input type="text" name="quantity_<? echo $tblRow; ?>" id="quantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('quantity')]; ?>" style="width:70px;" disabled="disabled" />
                        </td>
                        <td>
                            <input type="text" name="rate_<? echo $tblRow; ?>" id="rate_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('net_pi_rate')]; ?>" style="width:70px;" disabled="disabled" placeholder="<? echo $row[csf('rate')]; ?>" />
                        </td>
                        <td>
                            <input type="text" name="amount_<? echo $tblRow; ?>" id="amount_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('net_pi_amount')]; ?>" style="width:80px;" disabled="disabled" placeholder="<? echo $row[csf('amount')]; ?>" />
                            <input type="hidden" name="updateIdDtls_<? echo $tblRow; ?>" id="updateIdDtls_<? echo $tblRow; ?>" value="<? echo $row[csf('id')]; ?>"/>
                            <input type="hidden" name="bookingWithoutOrder_<? echo $tblRow; ?>" id="bookingWithoutOrder_<? echo $tblRow; ?>" value="0"/>
                        </td>
                    </tr>
                	<?
                }
            ?>
            </tbody>
        </table>
        <?   
	}
    /*?>
    <table width="100%">
        <tr>
            <td height="50" valign="middle" align="center" class="button_container">
                <? echo load_submit_buttons($_SESSION['page_permission'], "fnc_import_pi_save", $button_status, 0, "", 3); ?>
            </td>
        </tr>				
    </table> 
    <?*/
    exit();
}

/*if ($action == "save_update_delete_pi") 
{
    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));

    if ($operation == 0) {  // Insert Here
        $con = connect();
        if ($db_type == 0) {
            mysql_query("BEGIN");
        }
 
        $field_array = "id,com_export_lc_id,wo_po_break_down_id,attached_qnty,attached_rate,attached_value,import_btb,import_btb_id,work_order_no,pi_id,pi_dtls_id,determination_id,construction,composition,color_id,gsm,dia_width,uom,is_sales,inserted_by,insert_date,booking_no,item_group_id,size_id,aop_color,body_part_id,buyer_style_ref,gmts_item_id,embell_name,embell_type,fabric_description";
        $idDtls = return_next_id("id", "com_export_lc_order_info", 1);

        for ($i = 1; $i <= $total_row; $i++) {
            $workOrderNo = "workOrderNo_" . $i;
            $workOrderId = "hideWoId_" . $i;
            $hidePiId = "hidePiId_" . $i;
            $hidePiDtlsId = "hidePiDtlsId_" . $i;
            $determinationId = "hideDeterminationId_" . $i;
            $construction = "construction_" . $i;
            $composition = "composition_" . $i;
            $colorId = "colorId_" . $i;
            $gsm = "gsm_" . $i;
            $diawidth = "diawidth_" . $i;
            $uom = "uom_" . $i;
            $quantity = "quantity_" . $i;
            $rate = "rate_" . $i;
            $amount = "amount_" . $i;
            $isSalesId = "isSalesId_" . $i;

			$bookingNo = "bookingNo_" . $i;
			$itemgroupidPlace = "itemgroupidPlace_" . $i;	
			$itemdescription = "itemdescription_" . $i;
			$itemColor = "itemColor_" . $i;
			$itemSizePlace = "itemSizePlace_" . $i;
			$uomPlace_ = "uomPlace_" . $i;
			$ratePlace = "ratePlace_" . $i;
			$amountPlace = "amountPlace_" . $i;
			$bookingWithoutOrder = "bookingWithoutOrder_" . $i;
			$aopColorPlace = "aopColorPlace_" . $i;
            $bodyPartPlace = "bodyPartPlace_" . $i;
            $styleRef = "styleRef_" . $i;
            $gmtsItemPlace = "gmtsItemPlace_" . $i;
            $embNamePlace = "embNamePlace_" . $i;
			$embTypePlace = "embTypePlace_" . $i;

            if ($data_array != "")
                $data_array .= ",";
            $data_array .= "(" . $idDtls . "," . $txt_system_id . "," . $$workOrderId . "," . $$quantity . "," . $$rate . "," . $$amount . "," . $import_btb . "," . $import_btb_id . ",'" . str_replace("'", "", $$workOrderNo) . "','" . str_replace("'", "", $$hidePiId) . "','" . str_replace("'", "", $$hidePiDtlsId) . "','" . str_replace("'", "", $$determinationId) . "','" . str_replace("'", "", $$construction) . "','" . str_replace("'", "", $$composition) . "','" . str_replace("'", "", $$colorId) . "','" . str_replace("'", "", $$gsm) . "','" . str_replace("'", "", $$diawidth) . "','" . str_replace("'", "", $$uom) . "','" .str_replace("'", "", $$isSalesId) . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "','" . str_replace("'", "", $$bookingNo) . "','" . str_replace("'", "", $$itemgroupidPlace) . "','" . str_replace("'", "", $$itemSizePlace) . "','" . str_replace("'", "", $$aopColorPlace) . "','" . str_replace("'", "", $$bodyPartPlace) . "','" .  str_replace("'", "", $$styleRef) . "','" . str_replace("'", "", $$gmtsItemPlace) . "','" . str_replace("'", "", $$embNamePlace) . "','" . str_replace("'", "", $$embTypePlace) . "','" . str_replace("'", "", $$itemdescription) . "')";

            $idDtls = $idDtls + 1;
        }
        // echo "5**insert into com_export_lc_order_info (".$field_array.") values ".$data_array;die;	
        //print_r($data_array);die;
        $rID = sql_insert("com_export_lc_order_info", $field_array, $data_array, 1);       
        // echo "5**=".$rID.'==';die;
        if ($db_type == 0) {
            if ($rID) {
                mysql_query("COMMIT");
                echo "0**" . str_replace("'", '', $txt_system_id);
            } else {
                mysql_query("ROLLBACK");
                echo "5**0";
            }
        } else if ($db_type == 2 || $db_type == 1) {
            if ($rID) {
                oci_commit($con);
                echo "0**" . str_replace("'", '', $txt_system_id);
            } else {
                oci_rollback($con);
                echo "5**0";
            }
        }
        disconnect($con);
        die;
    } else if ($operation == 1) {   // Update Here
        $con = connect();
        if ($db_type == 0) {
            mysql_query("BEGIN");
        }

        $field_array = "id,com_export_lc_id,wo_po_break_down_id,attached_qnty,attached_rate,attached_value,import_btb,import_btb_id,work_order_no,pi_id,pi_dtls_id,determination_id,construction,composition,color_id,gsm,dia_width,uom,inserted_by,insert_date,booking_no,item_group_id,size_id,aop_color,body_part_id,buyer_style_ref,gmts_item_id,embell_name,embell_type,fabric_description";

        $field_array_update = "wo_po_break_down_id*attached_qnty*attached_rate*attached_value*import_btb*import_btb_id*work_order_no*pi_id*pi_dtls_id*determination_id*construction*composition*color_id*gsm*dia_width*uom*updated_by*update_date*booking_no*item_group_id*size_id*aop_color*body_part_id*buyer_style_ref*gmts_item_id*embell_name*embell_type*fabric_description";

        $idDtls = return_next_id("id", "com_export_lc_order_info", 1);

        for ($i = 1; $i <= $total_row; $i++) {
            $workOrderNo = "workOrderNo_" . $i;
            $workOrderId = "hideWoId_" . $i;
            $hidePiId = "hidePiId_" . $i;
            $hidePiDtlsId = "hidePiDtlsId_" . $i;
            $determinationId = "hideDeterminationId_" . $i;
            $construction = "construction_" . $i;
            $composition = "composition_" . $i;
            $colorId = "colorId_" . $i;
            $gsm = "gsm_" . $i;
            $diawidth = "diawidth_" . $i;
            $uom = "uom_" . $i;
            $quantity = "quantity_" . $i;
            $rate = "rate_" . $i;
            $amount = "amount_" . $i;
            $amount = "amount_" . $i;
            $updateIdDtls = "updateIdDtls_" . $i;
			
			$bookingNo = "bookingNo_" . $i;
			$itemgroupidPlace = "itemgroupidPlace_" . $i;	
			$itemdescription = "itemdescription_" . $i;
			$itemColor = "itemColor_" . $i;
			$itemSizePlace = "itemSizePlace_" . $i;
			$uomPlace_ = "uomPlace_" . $i;
			$ratePlace = "ratePlace_" . $i;
			$amountPlace = "amountPlace_" . $i;
			$bookingWithoutOrder = "bookingWithoutOrder_" . $i;
			$aopColorPlace = "aopColorPlace_" . $i;
            $bodyPartPlace = "bodyPartPlace_" . $i;
            $styleRef = "styleRef_" . $i;
            $gmtsItemPlace = "gmtsItemPlace_" . $i;
            $embNamePlace = "embNamePlace_" . $i;
			$embTypePlace = "embTypePlace_" . $i;

            if (str_replace("'", "", $$updateIdDtls) != "") {
                $id_arr[] = str_replace("'", '', $$updateIdDtls);
                $data_array_update[str_replace("'", '', $$updateIdDtls)] = explode("*", ("'" . str_replace("'", "", $$workOrderId) . "'*" . $$quantity . "*" . $$rate . "*" . $$amount . "*" . $import_btb . "*" . $import_btb_id . "*'" . str_replace("'", "", $$workOrderNo) . "'*'" . str_replace("'", "", $$hidePiId) . "'*'" . str_replace("'", "", $$hidePiDtlsId) . "'*'" . str_replace("'", "", $$determinationId) . "'*'" . str_replace("'", "", $$construction) . "'*'" . str_replace("'", "", $$composition) . "'*'" . str_replace("'", "", $$colorId) . "'*'" . str_replace("'", "", $$gsm) . "'*'" . str_replace("'", "", $$diawidth) . "'*'" . str_replace("'", "", $$uom) . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*'" . str_replace("'", "", $$bookingNo) . "'*'" . str_replace("'", "", $$itemgroupidPlace) . "'*'" . str_replace("'", "", $$itemSizePlace) . "'*'" . str_replace("'", "", $$aopColorPlace) . "'*'" . str_replace("'", "", $$bodyPartPlace) . "'*'" .  str_replace("'", "", $$styleRef) . "'*'" . str_replace("'", "", $$gmtsItemPlace) . "'*'" . str_replace("'", "", $$embNamePlace) . "'*'" . str_replace("'", "", $$embTypePlace) . "'*'" . str_replace("'", "", $$itemdescription) . "'"));
            } else {
                if ($data_array != "")
                    $data_array .= ",";
                $data_array .= "(" . $idDtls . "," . $txt_system_id . "," . $$workOrderId . "," . $$quantity . "," . $$rate . "," . $$amount . "," . $import_btb . "," . $import_btb_id . ",'" . str_replace("'", "", $$workOrderNo) . "','" . str_replace("'", "", $$hidePiId) . "','" . str_replace("'", "", $$hidePiDtlsId) . "','" . str_replace("'", "", $$determinationId) . "','" . str_replace("'", "", $$construction) . "','" . str_replace("'", "", $$composition) . "','" . str_replace("'", "", $$colorId) . "','" . str_replace("'", "", $$gsm) . "','" . str_replace("'", "", $$diawidth) . "','" . str_replace("'", "", $$uom) . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "','" . str_replace("'", "", $$bookingNo) . "','" . str_replace("'", "", $$itemgroupidPlace) . "','" . str_replace("'", "", $$itemSizePlace) . "','" . str_replace("'", "", $$aopColorPlace) . "','" . str_replace("'", "", $$bodyPartPlace) . "','" . str_replace("'", "", $$styleRef) . "','" .  str_replace("'", "", $$gmtsItemPlace) . "','" . str_replace("'", "", $$embNamePlace) . "','" . str_replace("'", "", $$embTypePlace) . "','" . str_replace("'", "", $$itemdescription) . "')";

                $idDtls = $idDtls + 1;
            }
        }

        //echo "insert into com_sales_contract_order_info (".$field_array.") values".$data_array;die;

        $flag = 1;
        if ($data_array != "") {
            $rID2 = sql_insert("com_export_lc_order_info", $field_array, $data_array, 0);
            if ($flag == 1) {
                if ($rID2)
                    $flag = 1;
                else
                    $flag = 0;
            }
        }
        //echo bulk_update_sql_statement( "com_export_lc_order_info", "id", $field_array_update, $data_array_update, $id_arr );die;
        if (count($data_array_update) > 0) {
            $rID = execute_query(bulk_update_sql_statement("com_export_lc_order_info", "id", $field_array_update, $data_array_update, $id_arr));
            if ($flag == 1) {
                if ($rID)
                    $flag = 1;
                else
                    $flag = 0;
            }
        }
        //echo "6**=".$rID.'=='.$rID2;die;
        if ($db_type == 0) {
            if ($flag == 1) {
                mysql_query("COMMIT");
                echo "1**" . str_replace("'", '', $txt_system_id);
            } else {
                mysql_query("ROLLBACK");
                echo "6**0**1";
            }
        } else if ($db_type == 2 || $db_type == 1) {
            if ($flag == 1) {
                oci_commit($con);
                echo "1**" . str_replace("'", '', $txt_system_id);
            } else {
                oci_rollback($con);
                echo "6**0**1";
            }
        }
        disconnect($con);
        die;
    }
}*/

if ($action == "export_lien_letter") // lien_letter 1
{
    //echo $data; die;
    ?>
    <style type="text/css">
        .a4size {
           width: 21cm;
           height: 26.7cm;
           font-family: Cambria, Georgia, serif;
        }
        @media print {
        .a4size{ font-family: Cambria;font-size: 18px;margin: 100px 120PX 54px 36px; 
            }
        size: A4 portrait;
        }
    </style>
    <?
    $data = explode("**", $data);

    //export lc lien-----------------
    $company_lib=return_library_array( "select id, company_name from lib_company",'id','company_name');
    if ($data[0] == 4) {
        $data_array = sql_select("select id, export_lc_system_id,currency_name,beneficiary_name, export_lc_no, lc_date, lien_bank, lien_date, lc_value, internal_file_no from com_export_lc where id='$data[1]'");
        foreach ($data_array as $row) {
            $system_ref = $row[csf("export_lc_system_id")];
            $internal_file_no = $row[csf("internal_file_no")];
            $lien_date = change_date_format($row[csf("lien_date")]);
            $lien_bank = strtoupper($row[csf("lien_bank")]);
            $lc_no = $row[csf("export_lc_no")];
            $lc_date = change_date_format($row[csf("lc_date")]);
            $lc_value = def_number_format($row[csf("lc_value")],2);
            $currency_name = $currency[$row[csf("currency_name")]];
            $company_name = strtoupper($company_lib[$row[csf("beneficiary_name")]]);

        }
        $data_array1 = sql_select("select wm.total_set_qnty as ratio, ci.attached_qnty from wo_po_break_down wb, wo_po_details_master wm, com_export_lc_order_info ci where wb.job_no_mst = wm.job_no and wb.id=ci.wo_po_break_down_id and ci.com_export_lc_id='$data[1]' and ci.status_active = '1' and ci.is_deleted = '0' order by ci.id");
        foreach ($data_array1 as $row1) {
            $order_qnty_in_pcs = $row1[csf('attached_qnty')] * $row1[csf('ratio')];
            $total_attach_qty += $order_qnty_in_pcs;
        }
    }

    //Sales Contact Lien-------------
    if ($data[0] == 3) {
        $data_array = sql_select("select id, contract_no, contract_date, lien_bank, lien_date, contract_value, internal_file_no from com_sales_contract where id='$data[1]'");
        foreach ($data_array as $row) {
            $internal_file_no = $row[csf("internal_file_no")];
            $contract_no = $row[csf("contract_no")];
            $contract_value = $row[csf("contract_value")];
            $contract_date = change_date_format($row[csf("contract_date")]);
            $lien_bank = $row[csf("lien_bank")];
            $lien_date = $row[csf("lien_date")];
        }

        $data_array1 = sql_select("select wm.total_set_qnty as ratio, ci.attached_qnty from wo_po_break_down wb, wo_po_details_master wm, com_sales_contract_order_info ci where wb.job_no_mst = wm.job_no and wb.id=ci.wo_po_break_down_id and ci.com_sales_contract_id='$data[1]' and ci.status_active = '1' and ci.is_deleted = '0' order by ci.id");
        foreach ($data_array1 as $row1) {
            $order_qnty_in_pcs = $row1[csf('attached_qnty')] * $row1[csf('ratio')];
            $total_attach_qty += $order_qnty_in_pcs;
        }
    }
    $designation_library=return_library_array( "select id,custom_designation from lib_designation", "id", "custom_designation");
    //bank information retriving here
    $data_array1 = sql_select("select id, bank_name, branch_name, contact_person, address,designation from lib_bank where id='$lien_bank'");
    foreach ($data_array1 as $row1) {
        $bank_name = strtoupper($row1[csf("bank_name")]);
        $branch_name = strtoupper($row1[csf("branch_name")]);
        $contact_person = strtoupper($row1[csf("contact_person")]);
        $address = strtoupper($row1[csf("address")]);
        $designation = strtoupper($designation_library[$row1[csf("designation")]]);
    }

    //letter body is retriving here
    $data_array2 = sql_select("select letter_body from dynamic_letter where letter_type='$data[0]'");
    foreach ($data_array2 as $row2) {
        $letter_body = $row2[csf("letter_body")];
    }

    $raw_data = str_replace("INTERNALFILENO", $internal_file_no, $letter_body);
    $raw_data = str_replace("LIENDATE", $lien_date, $raw_data);
    $raw_data = str_replace("SYSTEMREF", $system_ref, $raw_data);
    $raw_data = str_replace("CONTACTPERSON", $contact_person, $raw_data);
    $raw_data = str_replace("DESIGNATION", $designation, $raw_data);
    $raw_data = str_replace("BANKNAME", $bank_name, $raw_data);
    $raw_data = str_replace("BRANCHNAME", $branch_name, $raw_data);
    $raw_data = str_replace("ADDRESS", $address, $raw_data);
    $raw_data = str_replace("LCNUMBER", $lc_no, $raw_data);
    $raw_data = str_replace("LCDATE", $lc_date, $raw_data);
    $raw_data = str_replace("CURRENCY", $currency_name, $raw_data);
    $raw_data = str_replace("LCVALUE", $lc_value, $raw_data);
    $raw_data = str_replace("BENEFICIARY", $company_name, $raw_data);
    $raw_data = str_replace("TOTALATTACHQTY", $total_attach_qty, $raw_data);

    echo "<div class='a4size'>".$raw_data."</div>";
    exit();
}

if ($action == "export_lien_letter2") // lien_letter 2
{
    //echo $data; die;
    $data = explode("**", $data);

    //export lc lien-----------------
    $company_lib=return_library_array( "select id, company_name from lib_company",'id','company_name');
    $buyer_lib=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    if ($data[0] == 4) 
    {
        $data_array = sql_select("SELECT id, export_lc_system_id,currency_name, beneficiary_name, export_lc_no, lc_date, lien_bank, lien_date, lc_value, internal_file_no, bank_file_no, buyer_name, pay_term, tenor, last_shipment_date, expiry_date
            from com_export_lc where id='$data[1]'");
        foreach ($data_array as $row) 
        {
            $system_ref = $row[csf("export_lc_system_id")];
            $lc_id = $row[csf("id")];
            $internal_file_no = $row[csf("internal_file_no")];
            $bank_file_no = $row[csf("bank_file_no")];
            $lien_date = change_date_format($row[csf("lien_date")]);
            $lien_bank = strtoupper($row[csf("lien_bank")]);
            $lc_no = $row[csf("export_lc_no")];
            $lc_date = change_date_format($row[csf("lc_date")]);
            $lc_value = def_number_format($row[csf("lc_value")],2);
            $lc_values = $row[csf("lc_value")];
            $currency_name = $currency[$row[csf("currency_name")]];
            $company_name = strtoupper($company_lib[$row[csf("beneficiary_name")]]);
            $buyer_name = $row[csf("buyer_name")];
            $payTerm = $pay_term[$row[csf("pay_term")]];
            $tenor = $row[csf("tenor")];
            $last_shipment_date = $row[csf("last_shipment_date")];
            $expiry_date = $row[csf("expiry_date")];
        }
        $data_array1 = sql_select("SELECT wm.total_set_qnty as ratio, ci.attached_qnty, ci.attached_value
        from wo_po_break_down wb, wo_po_details_master wm, com_export_lc_order_info ci 
        where wb.job_no_mst=wm.job_no and wb.id=ci.wo_po_break_down_id and ci.com_export_lc_id='$data[1]' and ci.status_active='1' and ci.is_deleted='0' order by ci.id");
        foreach ($data_array1 as $row1) 
        {
            $order_qnty_in_pcs = $row1[csf('attached_qnty')] * $row1[csf('ratio')];
            $total_attach_qty += $order_qnty_in_pcs;
            $total_attached_value += $row1[csf('attached_value')];
        }
    }

    $sql_comm_freight="SELECT a.com_export_lc_id,a.wo_po_break_down_id, c.costing_per, d.comm_cost, d.freight
    from com_export_lc_order_info a, wo_po_break_down b, wo_pre_cost_mst c, wo_pre_cost_dtls d
    where a.wo_po_break_down_id=b.id and b.job_no_mst=c.job_no and c.job_no=d.job_no and com_export_lc_id='$data[1]'";
    $comm_freight_data=sql_select($sql_comm_freight);
    foreach ($comm_freight_data as $rows) 
    {
        //echo $rows[csf('costing_per')].'<br>';
        if ($rows[csf('costing_per')]==1) 
        {
            $total_comm_cost += $rows[csf('comm_cost')]/12;
            $total_freight += $rows[csf('freight')]/12;
        }
        elseif ($rows[csf('costing_per')]==2) 
        {
            $total_comm_cost += $rows[csf('comm_cost')]/1;
            $total_freight += $rows[csf('freight')]/1;
        }
        elseif ($rows[csf('costing_per')]==3) 
        {
            $total_comm_cost += $rows[csf('comm_cost')]/24;
            $total_freight += $rows[csf('freight')]/24;
        }
        elseif ($rows[csf('costing_per')]==4) 
        {
            $total_comm_cost += $rows[csf('comm_cost')]/38;
            $total_freight += $rows[csf('freight')]/38;
        }
        elseif ($rows[csf('costing_per')]==5) 
        {
            $total_comm_cost += $rows[csf('comm_cost')]/48;
            $total_freight += $rows[csf('freight')]/48;
        }
        /*$total_comm_cost += $rows[csf('comm_cost')];
        $total_freight += $rows[csf('freight')];
        $total_costing_per += $rows[csf('costing_per')];*/
    }
    //echo ($total_comm_cost+$total_freight)*$total_attach_qty;
    //echo $total_freight;

    $sql_sc=sql_select("SELECT a.com_export_lc_id, b.contract_no as sc_no, b.contract_value, b.contract_date as sc_date
    from  com_export_lc_atch_sc_info a, com_sales_contract b
    where a.com_sales_contract_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.com_export_lc_id='$data[1]'");
    
    $sql_sc_arr=array();
    foreach($sql_sc as $row)
    {
        $sql_sc_arr[$row[csf("com_export_lc_id")]]["sc_no"].=$row[csf("sc_no")].", ";
        $sql_sc_arr[$row[csf("com_export_lc_id")]]["sc_date"].=$row[csf("sc_date")].",";
    }
    /*echo '<pre>';
    print_r($sql_sc_arr);*/

    $designation_library=return_library_array( "select id,custom_designation from lib_designation", "id", "custom_designation");
    //bank information retriving here
    $data_array1 = sql_select("select id, bank_name, branch_name, contact_person, address,designation from lib_bank where id='$lien_bank'");
    foreach ($data_array1 as $row1) 
    {
        $bank_name = ucwords($row1[csf("bank_name")]);
        $branch_name = ucwords($row1[csf("branch_name")]);
        $contact_person = ucwords($row1[csf("contact_person")]);
        $address = ucwords($row1[csf("address")]);
        $designation = ucwords($designation_library[$row1[csf("designation")]]);
    }

    ?>
    <style type="text/css">
        .a4size {
           width: 21cm;
           height: 26.7cm;
           font-family: Bookman Old Style;
        }
        @media print {
        .a4size{ font-family: Bookman Old Style;font-size: 18px;margin: 80px 100PX 54px 25px;
            }
        size: A4 portrait;
        }
        .parent {
          display: flex;
          flex-direction:row;
          margin-left: 28px;
        }

        .column {
          flex: 1 1 0px;
          margin-right: 30px;
        }
    </style>
    <div class="a4size">
        <table width="794" cellpadding="0" cellspacing="0" border="0" >
            <div style="text-align: center; margin-top: 60px; padding-top: 60px;">
               <strong><i>Replacement</i></strong><br><br>
            </div>
            <div class="parent" >
                <? echo date('M d, Y',strtotime($lc_date));?>
                <div class="column" align="right">
                    <?
                    echo $bank_file_no;
                    ?>
                </div>
            </div>
            <br>
            <tr>
                <td width="25"></td>
                <td width="650" align="left">To</td>
                <td width="25" ></td>
            </tr>
            <tr>
                <td width="25" ></td>
                <td width="650" align="left">
                <?
                    echo "The Manager<br>Trade Service Department<br>";
                    echo $bank_name."<br>";
                    echo $address;
                ?>.
                </td>
                <td width="25" ></td>
            </tr>
            <tr>
                <td colspan="3" height="20"></td>
            </tr>

            <tr>
                <td width="25" ></td>
                <td width="650" align="justify">
                <strong>Subject:  Lien of Export LC # <? echo $lc_no." date ".$lc_date." ".$lc_value; ?> partial replacement against 
                <?
                if (chop($sql_sc_arr[$lc_id]["sc_no"],",")!="") 
                {
                    echo "Sales Contract# ".chop($sql_sc_arr[$lc_id]["sc_no"],", ");
                    echo " Date "; $scDate=chop($sql_sc_arr[$lc_id]["sc_date"],",");
                    $scDateArr=array_unique(explode(",",$scDate));
                    $sc_all_date ="";
                    foreach ($scDateArr as $key => $value) 
                    {
                        if ($sc_all_date=="") 
                        {
                            $sc_all_date.= change_date_format($value);
                        }
                        else 
                        {
                            $sc_all_date.= ', '.change_date_format($value);
                        }
                    }
                    echo $sc_all_date;
                }
                ?>2020 for & open BTB L/C.</strong></td>
                <td width="25" ></td>
            </tr>
            <tr>
                <td colspan="3" height="20"></td>
            </tr>
            <tr>
                <td width="25" ></td>
                <td width="650" align="left"> Dear Sir, </td>
                <td width="25" ></td>
            </tr>
            <tr>
                <td width="25" ></td>
                <td width="650" align="justify">
                In reference to the above, we are submitting herewith the above noted <strong>Export LC</strong> for your 
                kind attention and request you to keep under lien against which we will open BTB LC for USD i.e. 
                @75% of FOB value.
                </td>
                <td width="25" ></td>
            </tr>
        </table>
        <table width="794" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td colspan="3" height="15"></td>
            </tr>
            <tr>
                <td colspan="3" style="padding-left: 28px;">Details of the <strong>Export LC</strong> as follows:</td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">01. Export LC</td>
                <td width="15" >:</td>
                <td width="380"><? echo $lc_no;?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">02. Date</td>
                <td width="15" >:</td>
                <td width="380"><? echo change_date_format($lc_date); ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">03. Value in USD</td>
                <td width="15" >:</td>
                <td width="380"><? if($lc_value) echo '$'.$lc_value,2; ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">04. Buyer Name</td>
                <td width="15" >:</td>
                <td width="380"><? echo $buyer_lib[$buyer_name]; ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">05. Comm./Freight</td>
                <td width="15" >:</td>
                <td width="380" title="(Comm+Freight)/Costing Per*Attach.Qty Pcs"><? $comm_freight=($total_comm_cost+$total_freight)*$total_attach_qty;
                if ($comm_freight>0) echo '$'.number_format($comm_freight,2); ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">06. FOB Value</td>
                <td width="15" >:</td>
                <td width="380"><? if($total_attached_value) echo '$'.number_format($total_attached_value,2); ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">07. 75% BTB Limit</td>
                <td width="15" >:</td>
                <td width="380"><? if($lc_values) echo '$'.number_format(($lc_values*75)/100,2); ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">08. Tenor</td>
                <td width="15" >:</td>
                <td width="380"><? echo $payTerm; if ($tenor>0) { echo ', '.$tenor.' Days'; } ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">09. Qty</td>
                <td width="15" >:</td>
                <td width="380"><? if($total_attach_qty) echo number_format($total_attach_qty,2).' PCS'; ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">10. Shipment Date</td>
                <td width="15" >:</td>
                <td width="380"><? echo change_date_format($last_shipment_date); ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">11. Expiry Date</td>
                <td width="15" >:</td>
                <td width="380"><? echo change_date_format($expiry_date); ?></td>
            </tr>
        </table>
        <table width="794" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td colspan="3" height="15"></td>
            </tr>

            <tr>
                <td colspan="3" height="15"></td>
            </tr>
            <tr>
                <td width="25" ></td>
                <td width="650" align="justify">
                We would request your kind self to do the needful to lien the aforesaid Export LC and necessary action at your end.
                <br><br>
                Thank you very much, indeed
                </td>
                <td width="25" ></td>
            </tr>
            <tr>
                <td colspan="3" height="50"></td>
            </tr>
        </table>
        <table width="794" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td style="padding-left: 28px;" width="397" align="left"><strong>-------------------------------</strong></td>
                <td width="397" align="left"><strong>-------------------------------</strong></td>
            </tr>
            <tr>
                <td style="padding-left: 28px;" width="397" align="left"><strong>Authorized Signature</strong></td>
                <td width="397" align="left"><strong>Authorized Signature</strong></td>
            </tr>
            <tr >
                <td style="padding-left: 28px; padding-top: 30px;" width="397" align="left"><strong>Enclosed: As States</strong></td>
            </tr>
        </table>
    </div>
    <!--  -->
    <?
    exit();
}
if ($action == "export_lien_letter3") // lien_letter 3
{
    //echo $data; die;
    $data = explode("**", $data);

    //export lc lien-----------------
    $company_lib=return_library_array( "select id, company_name from lib_company",'id','company_name');
    $buyer_lib=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    $currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
    if ($data[0] == 4) 
    {
        $data_array = sql_select("SELECT id, export_lc_system_id,currency_name, beneficiary_name, export_lc_no, lc_date, lien_bank, lien_date, lc_value, internal_file_no, bank_file_no, buyer_name, pay_term, tenor, last_shipment_date, expiry_date
            from com_export_lc where id='$data[1]'");
        foreach ($data_array as $row) 
        {
            $system_ref = $row[csf("export_lc_system_id")];
            $lien_bank = strtoupper($row[csf("lien_bank")]);
            $lc_no = $row[csf("export_lc_no")];
            $lc_date = change_date_format($row[csf("lc_date")]);
            $lc_value = def_number_format($row[csf("lc_value")],2);
            $lc_values = $row[csf("lc_value")];
            $currency_name = $currency[$row[csf("currency_name")]];
            $currency_sign = $currency_sign_arr[$row[csf("currency_name")]];
        }
        $data_array1 = sql_select("SELECT wm.total_set_qnty as ratio, ci.attached_qnty, ci.attached_value
        from wo_po_break_down wb, wo_po_details_master wm, com_export_lc_order_info ci 
        where wb.job_no_mst=wm.job_no and wb.id=ci.wo_po_break_down_id and ci.com_export_lc_id='$data[1]' and ci.status_active='1' and ci.is_deleted='0' order by ci.id");
        foreach ($data_array1 as $row1) 
        {
            $order_qnty_in_pcs = $row1[csf('attached_qnty')] * $row1[csf('ratio')];
            $total_attach_qty += $order_qnty_in_pcs;
            $total_attached_value += $row1[csf('attached_value')];
        }
    }

    //bank information retriving here
    $data_array1 = sql_select("select id, bank_name, branch_name, address from lib_bank where id='$lien_bank'");
    foreach ($data_array1 as $row1) 
    {
        $bank_name = ucwords($row1[csf("bank_name")]);
        $branch_name = ucwords($row1[csf("branch_name")]);
        $address = ucwords($row1[csf("address")]);
    }

    ?>
    <style type="text/css">
        .a4size {
           width: 21cm;
           height: 26.7cm;
           font-family: Bookman Old Style;
        }
        @media print {
        .a4size{ font-family: Bookman Old Style;font-size: 18px;margin: 80px 100PX 54px 25px;
            }
        size: A4 portrait;
        }
        .parent {
          display: flex;
          flex-direction:row;
          margin-left: 28px;
        }

        .column {
          flex: 1 1 0px;
          margin-right: 30px;
        }
    </style>
    <div class="a4size">
        <table width="794" cellpadding="0" cellspacing="0" border="0" >
            <div style=" height:100;"></div>
            <div class="parent" ><strong>Dated:  
                <? //echo date('d M Y',strtotime($lc_date));?>
                <? echo date('d-m-Y');?>
                <div class="column" align="right">
                    <?
                    // echo $bank_file_no;
                    ?></strong>
                </div>
            </div>
            <br>
            <tr>
                <td width="25"></td>
                <td width="650" align="left">To</td>
                <td width="25" ></td>
            </tr>
            <tr>
                <td width="25" ></td>
                <td width="650" align="left">
                <?
                    echo "The Chief Manager <br>";
                    echo $bank_name."<br>";
                    echo $branch_name." Branch.<br>";
                    echo $address;
                ?>.
                </td>
                <td width="25" ></td>
            </tr>
            <tr>
                <td colspan="3" height="100"></td>
            </tr>

            <tr>
                <td width="25"></td>
                <td width="650" align="justify">
                <strong>Sub: </strong> Request for Lien Export L/C No. <strong> DC <? echo $lc_no." DC ".$lc_date." for L/C Value  ".$currency_name." ".$currency_sign."".$lc_value." Total L/C Value ".$currency_name." ".$currency_sign."".$lc_value; ?> 
               </strong>.
               </td>
                <td width="25" ></td>
            </tr>
            <tr>
                <td colspan="3" height="75"></td>
            </tr>
            <tr>
                <td width="25" ></td>
                <td width="650" align="left"> Dear Sir, </td>
                <td width="25" height="50"></td>
            </tr>
            <tr>
                <td width="25" ></td>
                <td width="650" align="justify">
                With reference to the above subject we are requesting you to Request for Lien Export L/C No.
                <strong> <? echo " DC ".$lc_no." DT ".$lc_date." for L/C Value ".$currency_name." ".$currency_sign."".$lc_value." Total L/C Value ".$currency_name." ".$currency_sign."".$lc_value; ?> 
               </strong>.
                </td>
                <td width="25" ></td>
            </tr>
        </table>
        <table width="794" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td colspan="3" height="150"></td>
            </tr>

            <tr>
                <td colspan="3" height="15"></td>
            </tr>
            <tr>
                <td width="25" ></td>
                <td width="650" align="justify">
                We would request you to the above mentioned export L/C. & We will be very grateful to you.
                <br><br>
                </td>
                <td width="75" ></td>
            </tr>
            <tr>
                <td width="25" ></td>
                <td colspan="2" height="50">
                Thanks & Regards,<br>
                Very truly yours, 
                </td>
            </tr>
        </table>
    </div>
    <?
    exit();
}

if ($action == "export_check_list")  // lien_letter 2
{
    $data = explode("**", $data);
    // print_r($data);die;

    //export lc lien-----------------
    $company_lib=return_library_array( "select id, company_name from lib_company",'id','company_name'); 
    $country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name'); 
    $buyer_lib=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name'); 

    $beforeorafter = array(1=>'Before Shipment',2=>'After Shipment'); 
    $payment = array(1=>'Direct SC',2=>'Direct LC',3=>'SC to LC'); 
    $inspect_by = array(1=>"Buyer's Care",2=>"Other Party"); 
    $paid_by = array(1=>"Paid by Customer",2=>"Paid by Seller");

    //LC/SC information retriving here 
    $data_array = sql_select("SELECT * from com_export_lc where id='$data[1]'");
    foreach ($data_array as $row) 
    {
        $company_name = $company_lib[$row[csf("beneficiary_name")]];
        $company_id = $row[csf("beneficiary_name")];
        $buyer_id = $row[csf("buyer_name")];

        $lc_applicant_name   = $buyer_lib[$row[csf("applicant_name")]]; 
        $lc_replacement_lc   = $row[csf("replacement_lc")]; 
        $lc_date             = change_date_format($row[csf("lc_date")]);
        $lc_transfarrable    = $row[csf("is_lc_transfarrable")];
        $lc_source_sc        = $contract_source[$row[csf("lc_source")]];
        $lc_foreign_comn     = $row[csf("foreign_comn")];
        $lc_local_comn       = $row[csf("local_comn")];
        $lc_tolerance        = $row[csf("tolerance")];  
        $lc_tenor            = $row[csf("tenor")];
        $lc_incoterm         = $row[csf("inco_term")];
        $lc_incoterm_plc     = $row[csf("inco_term_place")];
        $lc_port_discrg      = $row[csf("port_of_discharge")];
        $lc_discount         = $row[csf("discount_clauses")];   
        $lc_claim_adjustment  = $row[csf("claim_adjustment")];   
        $lc_rmbrs_cls        = $row[csf("reimbursement_clauses")];        
        $lc_bill_landing     = $row[csf("bl_clause")]; 
        $lc_no               = $row[csf("export_lc_no")]; 
        $lc_trans_bank_ref   = $row[csf("transfering_bank_ref")];      
        $lc_transfer_bank   = $row[csf("transfer_bank")];      
        $lc_issuing_bank     = $row[csf("issuing_bank_name")];      
        $lc_expiry_date      = change_date_format($row[csf("expiry_date")]);      
        $lc_expiry_place      = $row[csf("expiry_place")];      
        $lc_export_value      = $row[csf("lc_value")];      
        $lc_negotiating_bank      = $row[csf("negotiating_bank")];      
        $lc_last_shipment_date      = change_date_format($row[csf("last_shipment_date")]);      
        $lc_remarks             = $row[csf("remarks")];      
        $lc_re_imbursing_bank      = $row[csf("re_imbursing_bank")];      
        $lc_nominated_shipp_line      = $row[csf("nominated_shipp_line")];      
        $lc_pay_term            = $row[csf("pay_term")];      
        $lc_doc_presentation_days            = $row[csf("doc_presentation_days")];      


        $system_ref = $row[csf("export_lc_system_id")];
        $internal_file_no = $row[csf("internal_file_no")];
        $lien_date = change_date_format($row[csf("lien_date")]);
        $lien_bank = strtoupper($row[csf("lien_bank")]); 
        $lc_value = def_number_format($row[csf("lc_value")],2);
        $currency_name = $currency[$row[csf("currency_name")]];        
    } 

    // LC/SC to be opened for LC/SC 
    $sql_lcsc = sql_select("SELECT min(c.shipment_date) as min_shipment_date
    from com_export_lc a, com_export_lc_order_info b, wo_po_break_down c
    where a.id=b.com_export_lc_id and b.wo_po_break_down_id=c.id and a.id='$data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
    foreach ($sql_lcsc as $row) 
    {
        $min_shipment_date = change_date_format($row[csf("min_shipment_date")]);
    }
    $lc_dates = strtotime("$lc_date");
    $ship_date = strtotime("$min_shipment_date");
    $datediff = $ship_date-$lc_dates;
    $lc_diff_days = $datediff / (60 * 60 * 24);  
    // LC/SC to be opened for LC/SC end

    $sql_qty="SELECT wb.id, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wm.order_uom, wm.total_set_qnty as ratio, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active
    from wo_po_break_down wb, wo_po_details_master wm, com_export_lc_order_info ci
    where wb.job_no_mst = wm.job_no and wb.id = ci.wo_po_break_down_id and ci.com_export_lc_id='$data[1]' and ci.status_active = '1' and ci.is_deleted ='0' 
    order by ci.id";
    
    $total_order_qnty_in_pcs = 0;
    $nameArray = sql_select($sql_qty);
    foreach ($nameArray as $qtyRow) 
    {
        $order_qnty_in_pcs = $qtyRow[csf('attached_qnty')] * $qtyRow[csf('ratio')];
        $total_goods_qty_pcs += $order_qnty_in_pcs;
    }                    
    // echo $total_goods_qty_pcs;

    $sql_amendment=sql_select("SELECT sum(amendment_value) as amendment_value, lc_value, value_change_by from com_export_lc_amendment where export_lc_id='$data[1]' group by lc_value, value_change_by"); 
    $amendmentValue = array();
    $Org_lc_value = array();
    foreach ($sql_amendment as $row) 
    {
        $amendmentValue[$row[csf("value_change_by")]]=$row[csf("amendment_value")]; 
        $Org_lc_value[$row[csf("value_change_by")]]=$row[csf("lc_value")]; 
    }
    /*echo "<pre>";
    print_r($Org_lc_value);*/
     // echo $Org_lc_value[0]; 
    //1 == Increase and 2 == Decress

    $designation_library=return_library_array( "select id,custom_designation from lib_designation", "id", "custom_designation");

    //bank information retriving here
    $data_array1 = sql_select("select id, bank_name, branch_name, contact_person, address from lib_bank where id='$lien_bank'");
    foreach ($data_array1 as $row1) 
    {
        $bank_name = $row1[csf("bank_name")];
        $branch_name = $row1[csf("branch_name")];
        $contact_person = strtoupper($row1[csf("contact_person")]);
        $address = $row1[csf("address")];
        $designation = strtoupper($designation_library[$row1[csf("designation")]]);
    }

    // Buyer information retriving here
    $buyer_info = sql_select("SELECT * from lib_buyer where id='$buyer_id'");
    foreach ($buyer_info as $values) 
    {
        $buyer_name             = $values[csf("buyer_name")]; 
        $buyer_pay_through      = $values[csf("pay_through")]; 
        $buyer_lc_sc            = $values[csf("lc_sc")];
        $buyer_lc_sc_shpmnt     = $values[csf("lc_sc_shpmnt")]; 
        $buyer_trnsfr_lc        = $values[csf("trnsfr_lc")]; 
        $buyer_trnsfr_type      = $values[csf("trnsfr_type")]; 
        $buyer_comm_avlbl       = $values[csf("comm_avlbl")]; 
        $buyer_comm_prcnt_local = $values[csf("comm_prcnt_local")]; 
        $buyer_comm_prcnt_forgn = $values[csf("comm_prcnt_forgn")]; 
        $buyer_tolerance        = $values[csf("tolerance")]; 
        $buyer_partial_shpmnt   = $values[csf("partial_shpmnt")]; 
        $buyer_transhipment     = $values[csf("transhipment")]; 
        $buyer_inspect_crt      = $values[csf("inspect_crt")]; 
        $buyer_payment_term     = $values[csf("payment_term")]; 
        $buyer_tenor            = $values[csf("tenor")]; 
        $buyer_tenor_shpmnt     = $values[csf("tenor_shpmnt")]; 
        $buyer_incoterm         = $values[csf("incoterm")]; 
        $buyer_incoterm_plc     = $values[csf("incoterm_plc")]; 
        $buyer_port_discrg      = $values[csf("port_discrg")]; 
        $buyer_insurance        = $values[csf("insurance")]; 
        $buyer_insurance_other  = $values[csf("insurance_other")]; 
        $buyer_bill_neg         = $values[csf("bill_neg")]; 
        $buyer_penalty_dsc      = $values[csf("penalty_dsc")]; 
        $buyer_rmbrs_cls        = $values[csf("rmbrs_cls")]; 
        $buyer_bill_landing     = $values[csf("bill_landing")];   
    }
    ?>
    <style>
        .a4size {
           width: 23cm;
           height: 10.7cm;
           font-family: Cambria, Georgia, serif;
        }
        @media print {
        .a4size{ font-family: Cambria;font-size: 18px;margin: 100px 120PX 54px 36px; 
            }
        size: A4 portrait;
        }
        table, th, td {
          border: 1px solid black;
          border-collapse: collapse;
        }
        table, th, .none {
          border-bottom: none; 
        }
    </style>

    <div class='a4size'>                                   
        <table width="100%" cellpadding="0" cellspacing="0" >
            <tr>
                <td align="center" style="font-size:20px;"><strong><? echo $company_name; ?></strong></td>
            </tr>
            <tr>
                <td align="center" style="font-size:14px" class="none">  
                    <?  
                    $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company_id"); 
                    foreach ($nameArray as $result)
                    {  
                        echo $result[csf('city')];
                    }
                    ?>   
                </td> 
            </tr> 
            <tr><td align="center" style="font-size:14px" class="none"><strong>Export LC Checklist</strong></td></tr>
            <tr><td align="center" style="font-size:14px" class="none">Bank Name: <? echo $bank_name.', Branch: '.$address; ?></td></tr>
        </table> 

        <table style="width:100%">
          <tr bgcolor="#b7b7b7">
            <th style="width:3%">Sl</th>
            <th style="width:21%">Particulars</th>
            <th style="width:38%">KYC</th> 
            <th style="width:38%">LC</th>
          </tr>
          <tr>
            <td>1</td>
            <td>Applicant Name</td>
            <td><? echo $buyer_name; ?></td>
            <td><? echo $lc_applicant_name; ?></td>
          </tr>
          <tr>
            <td>2</td>
            <td>Payment through</td>
            <td><? echo $payment[$buyer_pay_through]; ?></td>
            <td><? echo ($lc_replacement_lc==2)?'LC':'SC to LC'; ?></td>
          </tr>
          <tr>
            <td>3</td>
            <td>LC/SC to be opened</td>
            <td><? echo $buyer_lc_sc.' Days '.$beforeorafter[$buyer_lc_sc_shpmnt]; ?></td>
            <td><? echo $lc_diff_days.' Days Before Shipment'; ?></td>
          </tr>
          <tr>
            <td>4</td>
            <td>Transferable LC</td>
            <td><? echo $yes_no[$buyer_trnsfr_lc]; ?></td>
            <td><? echo $yes_no[$lc_transfarrable]; ?></td>
          </tr>
          <tr>
            <td>5</td>
            <td>Transfer Type</td>
            <td><? echo $buyer_trnsfr_type; ?></td>
            <td><? echo $lc_source_sc; ?></td>
          </tr>
          <tr>
            <td>6</td>

            <td>Commission available</td>
            <td><? echo $yes_no[$buyer_comm_avlbl]; ?></td>
            <td><? echo ($lc_local_comn!='' || $lc_foreign_comn!='') ? 'Yes':'No'; ?></td>
          </tr>
          <tr>
            <td>7</td>
            <td>Commission %</td>
            <td><? echo 'Local: '.$buyer_comm_prcnt_local.', Foreign: '.$buyer_comm_prcnt_forgn; ?></td>
            <td><? echo 'Local: '.$lc_local_comn.', Foreign: '.$lc_foreign_comn; ?></td>
          </tr>
          <tr>
            <td>8</td>
            <td>Tolerance %</td>
            <td><? echo $buyer_tolerance; ?></td>
            <td><? echo $lc_tolerance; ?></td> 
          </tr> 
          <tr>
            <td>9</td>
            <td>Partial Shipment </td>
            <td><? echo $yes_no[$buyer_partial_shpmnt]; ?></td>
            <td> </td>
          </tr>
          <tr>
            <td>10</td>
            <td>Transhipment</td>
            <td><? echo $yes_no[$buyer_transhipment]; ?></td>
            <td> </td>
          </tr> 
            <td>11</td>
            <td>Inspection certificate</td>
            <td><? echo $commission_particulars[$buyer_inspect_crt]; ?></td>
            <td> </td>
          </tr>
          <tr>
            <td>12</td>
            <td>Payment Term</td>
            <td><? echo $pay_term[$buyer_payment_term]; ?></td>
            <td><? echo $pay_term[$lc_pay_term]; ?> </td>
          </tr>

          <tr>
            <td>13</td>
            <td>Payment Tenor</td>
            <td><? echo $buyer_tenor.' '.$beforeorafter[$buyer_tenor_shpmnt]; ?></td>
            <td><? echo $lc_tenor; ?></td>
          </tr>
          <tr>
            <td>14</td>
            <td>Incoterm</td>
            <td><? echo $incoterm[$buyer_incoterm] ; ?></td>
            <td><? echo $incoterm[$lc_incoterm] ; ?></td>
          </tr>
          <tr>
            <td>15</td>
            <td>Incoterm Place</td>
            <td><? echo $buyer_incoterm_plc ; ?></td>
            <td><? echo $lc_incoterm_plc ; ?></td>
          </tr>
          <tr>
            <td>16</td>
            <td>Port of discharge</td>
            <td><? echo $buyer_port_discrg ; ?></td>
            <td><? echo $lc_port_discrg ; ?></td>
          </tr>
          <tr>
            <td>17</td>
            <td>Insurance</td>
            <td><? echo $inspect_by[$buyer_insurance].' '.$buyer_insurance_other ; ?></td>
            <td> </td>
          </tr>
          <tr>
            <td>18</td>
            <td>Bill Negotiation</td>
            <td><? echo $buyer_bill_neg ; ?></td>
            <td> </td>
          </tr>
          <tr>
            <td>19</td>
            <td>Bank charges</td>
            <td> </td>
            <td> </td>
          </tr>
          <tr>
            <td>20</td>
            <td>Penalty/ Discount Cls</td>
            <td><? echo $buyer_penalty_dsc ; ?></td>
            <td><? echo $lc_discount ; ?></td>
          </tr>
          <tr>
            <td>21</td>
            <td>Reimbursement Cls</td>
            <td><? echo $buyer_rmbrs_cls ; ?></td>
            <td><? echo $lc_rmbrs_cls ; ?></td>
          </tr>
          <tr>
            <td>22</td>
            <td>Claim Adjustment </td>
            <td><? echo $buyer_port_discrg ; ?></td>
            <td><? echo $lc_claim_adjustment ; ?></td>
          </tr>
          <tr>
            <td class="none">23</td>
            <td class="none">Bill of Lading </td>
            <td class="none"><? echo $buyer_bill_landing ; ?></td>
            <td class="none"><? echo $lc_bill_landing ; ?></td>
          </tr>
        </table> 

        <table style="width:100%">
           <tr bgcolor="#b7b7b7">
            <td style="width:3%"><strong>24</strong></td>
            <td colspan="2"><strong>LC/SC Details</strong></td> 
          </tr> 
            <td>1</td>
            <td style="width:47%">Export LC No</td>
            <td><? echo $lc_no ; ?></td> 
          </tr>
          <tr>
            <td>2</td>
            <td>Transferring Bank Referencne</td>
            <td><? echo $lc_trans_bank_ref ; ?></td> 
          </tr>
          <tr>
            <td>3</td>
            <td>LC Opening Date</td>
            <td><? echo $lc_date ; ?></td> 
          </tr>
          <tr>
            <td>4</td>
            <td>LC Issuing Bank</td>
            <td><? echo $lc_issuing_bank ; ?></td> 
          </tr>
          <tr>
            <td>5</td>
            <td>First Beneficiary</td>
            <td></td> 
          </tr>
          <tr>
            <td>6</td>
            <td>Second Beneficiary</td>
            <td><? echo $company_name ; ?></td> 
          </tr>
          <tr>
            <td>7</td>
            <td>Whether is it Authentic/Test Agreed</td>
            <td>AUTHENTICATED</td> 
          </tr>
          <tr>
            <td>8</td>
            <td>Whether is it Irrevocable</td>
            <td>IRREVOCABLE</td> 
          </tr>
          <tr>
            <td>9</td>
            <td>If transfer LC, Name of transferring Bank</td>
            <td><? echo ($lc_transfarrable==1) ? $lc_transfer_bank : ''; ?></td> 
          </tr>
          <tr>
            <td>10</td>
            <td>Expiry Date of Export LC</td>
            <td><? echo $lc_expiry_date ; ?></td> 
          </tr>
          <tr>
            <td>11</td>
            <td>Place of Expiry LC</td>
            <td><? echo $lc_expiry_place ; ?></td> 
          </tr>
          <tr>
            <td>12</td>
            <td>Amount of Export LC</td>
            <td><? echo $currency_name.' '.$lc_export_value ; ?></td> 
          </tr>
          <tr>
            <td>13</td>
            <td>Negotiating Bank Name</td>
            <td><? echo $lc_negotiating_bank ; ?></td> 
          </tr>
          <tr>
            <td>14</td>
            <td>Last Date of Shipment</td>
            <td><? echo $lc_last_shipment_date ; ?></td> 
          </tr>
          <tr>
            <td>15</td>
            <td>Port of discharge</td>
            <td><? echo $lc_port_discrg ; ?></td> 
          </tr>
          <tr>
            <td>16</td>
            <td>Document Presentation days</td>
            <td><? echo $lc_doc_presentation_days ; ?></td>  
          </tr>
          <tr>
            <td>17</td>
            <td>Exporting Goods Quantity</td>
            <td><? echo $total_goods_qty_pcs ; ?></td> 
          </tr>
          <tr>
            <td>18</td>
            <td>Name of Particular shipping line</td>
            <td><? echo $lc_nominated_shipp_line ; ?></td> 
          </tr>
          <tr>
            <td>19</td>
            <td>Reimbursement Bank</td>
            <td><? echo $lc_re_imbursing_bank ; ?></td> 
          </tr>
          <tr>
            <td>20</td>
            <td>Original Export LC Value</td>
            <td><? 
            if ($Org_lc_value[0]=="") 
            {
                echo $currency_name.' '.$lc_export_value;
            }
            else{
                echo $currency_name.' '.$Org_lc_value[0];
            }
            //echo 'USD '.$lc_export_value.' Org '.$Org_lc_value[0]; ?></td> 
          </tr>
          <tr>
            <td>21</td>
            <td>Amendment Value(Total Increase)</td>
            <td><? echo $amendmentValue[1] ; ?></td> 
          </tr>
          <tr>
            <td>22</td>
            <td>Amendment Value(Total Decrease)</td>
            <td><? echo $amendmentValue[2] ; ?></td> 
          </tr>
          <tr>
            <td colspan="2" align="right">Current LC Value</td>  
            <td><? if ($Org_lc_value[0]=="") 
            {
                echo $currency_name.' '.$lc_export_value;
            }
            else{
                echo $currency_name.' '.($Org_lc_value[0]-$amendmentValue[2]+$amendmentValue[1]);
            } ?></td> 
          </tr>
          <tr>
            <td colspan="2" align="right">Net LC Value</td> 
            <td><? if ($Org_lc_value[0]=="") 
            {
                echo $currency_name.' '.$lc_export_value;
            }
            else{
                echo $currency_name.' '.($Org_lc_value[0]-$amendmentValue[2]+$amendmentValue[1]);
            } ?></td> 
          </tr>
          <tr bgcolor="#b7b7b7">
            <td><strong>25</strong></td>
            <td colspan="2"><strong>Document Required</strong></td> 
          </tr>
          <tr>
            <td>1</td>
            <td>Commercial Invoice 1+3 Copy</td>
            <td></td> 
          </tr>
          <tr>
            <td>2</td>
            <td>Packing List 1+2 Copy</td>
            <td></td> 
          </tr>
          <tr>
            <td>3</td>
            <td>Bill of Lading</td>
            <td></td> 
          </tr>
          <tr>
            <td>4</td>
            <td>Shipment Advice send to buyer</td>
            <td></td> 
          </tr>
          <tr>
            <td>5</td>
            <td>Original Inspection Certificates</td>
            <td></td> 
          </tr>
          <tr>
            <td>6</td>
            <td>Beneficiary's certificates</td>
            <td></td> 
          </tr>
          <tr>
            <td>7</td>
            <td>Shipping Documents</td>
            <td></td> 
          </tr>
          <tr>
            <td>8</td>
            <td>Certificate of Origin GSP form A</td>
            <td></td> 
          </tr>
          <tr>
            <td>9</td>
            <td>Original Doc. Send to buyer by Fax</td>
            <td></td> 
          </tr>
          <tr>
            <td>10</td>
            <td>Child Labour Certificate</td>
            <td></td> 
          </tr>
          <tr>
            <td>11</td>
            <td>Compliance Certificates</td>
            <td></td> 
          </tr>
          <tr>
            <td>12</td>
            <td>Labtest cetificate/ OK Test certificate</td>
            <td></td> 
          </tr>
          <tr>
            <td>26</td>
            <td>Remarks</td>
            <td><? echo $lc_remarks; ?></td> 
          </tr> 
        </table> 

        <br>

        <table style="width:100%"> 
          <tr>
            <td style="height: 50px;"></td> 
            <td></td> 
            <td></td> 
            <td></td> 
          </tr>
          <tr style="text-align: center;"> 
            <td style="width:25%">Prepared By</td> 
            <td style="width:25%">Manager</td>
            <td style="width:25%">Director</td>
            <td style="width:25%">Authorized Sign</td>
          </tr> 
        </table>
    </div>
    <?
    exit();
}

if ($action == "export_lien_letter4") // lien_letter 4 by Sakib
{
    //echo $data; die;
    $data = explode("**", $data);
    //export lc lien-----------------
    $company_lib=return_library_array( "select id, company_name from lib_company",'id','company_name');
    $buyer_lib=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    $currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
    if($data[0]==4)
    {
        // $data_array=sql_select("SELECT id, contract_no, contract_date, lien_bank, lien_date,contract_value,PORT_OF_DISCHARGE, max_btb_limit, max_btb_limit_value, internal_file_no, currency_name, buyer_name,beneficiary_name,last_shipment_date,estimated_qnty, expiry_date, bank_file_no, pay_term from com_export_lc where id='$data[1]'");
        $data_array=sql_select("SELECT * from com_export_lc where id='$data[1]'");
        foreach ($data_array as $row)
        {
            $beneficiary_name   = $row[csf("beneficiary_name")];
            $internal_file_no   = $row[csf("internal_file_no")];
            $buyer_name         = $buyer_lib[$row[csf("buyer_name")]];
            
            $lien_bank          = $row[csf("lien_bank")];
            $lien_date          = $row[csf("lien_date")];

            $EXPORT_LC_NO       = $row[csf("EXPORT_LC_NO")];
            $LC_DATE            = change_date_format($row[csf("LC_DATE")]);
            $LC_VALUE           = def_number_format($row[csf("LC_VALUE")],2);
            $currency_name      = $currency[$row[csf("currency_name")]];
            $currency_sign 		= $currency_sign_arr[$row[csf("currency_name")]];
            $last_shipment_date = change_date_format($row[csf("last_shipment_date")]);
            $payTerm 			= $pay_term[$row[csf("pay_term")]];
            //$PAY_TERM           = $row[csf("PAY_TERM")];
            $max_btb_limit      = $row[csf("max_btb_limit")];
        }

        $po_data_arr=sql_select("SELECT c.id as item_id, c.item_name, a.wo_po_break_down_id as po_id from com_sales_contract_order_info a join wo_po_color_size_breakdown b on 
        a.wo_po_break_down_id=b.po_break_down_id join lib_garment_item c on  b.item_number_id = c.id where a.status_active=1 and a.is_deleted=0 and 
        a.com_sales_contract_id = $data[1] and b.status_active=1 and b.is_deleted=0 and rownum=1 order by a.id asc");
        

        foreach ($po_data_arr as $row)
        {
            $item_name_arr[$row[csf("item_id")]] = $row[csf("item_name")];
            
        }
        $data_array1=sql_select("SELECT  wm.total_set_qnty as ratio, wm.AVG_UNIT_PRICE, ci.attached_qnty, ci.attached_value,ci.HS_CODE   from wo_po_break_down wb, wo_po_details_master wm, com_sales_contract_order_info ci where wb.job_id=wm.id and wb.id=ci.wo_po_break_down_id and ci.com_sales_contract_id='$data[1]' and ci.status_active='1' and ci.is_deleted='0' order by ci.id");
        
        foreach($data_array1 as $row1)
        {
            $attached_qnty_in_pcs= $row1[csf('attached_qnty')];
            $order_qnty_in_pcs=$row1[csf('attached_qnty')]*$row1[csf('ratio')];
            $total_attach_qty+=$order_qnty_in_pcs;
            $total_attached_value += $row1[csf('attached_value')];
            $hs_code_number= $row1[csf('HS_CODE')];

            
        }
    }
        $designation_library=return_library_array( "select id,custom_designation from lib_designation", "id", "custom_designation");
        //bank information retriving here
        $data_array1 = sql_select("select id, bank_name, branch_name, contact_person, address,designation from lib_bank where id='$lien_bank'");
        foreach ($data_array1 as $row1) 
        {
            $bank_name = ucwords($row1[csf("bank_name")]);
            $branch_name = ucwords($row1[csf("branch_name")]);
            $contact_person = ucwords($row1[csf("contact_person")]);
            $address = ucwords($row1[csf("address")]);
            $designation = ucwords($designation_library[$row1[csf("designation")]]);
        }
    ?>
    <style type="text/css">
        .a4size {
           width: 21cm;
           height: 26.7cm;
           /*font-family: Bookman Old Style;*/
        }
        @media print {
        .a4size{ font-family: Bookman Old Style;font-size: 18px;margin: 80px 100PX 54px 25px;
            }
            @page {size: A4 portrait;}
        }
        .parent {
          display: flex;
          flex-direction:row;
          margin-left: 28px;
          margin-top: 60px; padding-top: 60px;
        }
        .column {
          flex: 1 1 0px;
          margin-right: 30px;
        }
        .tag_font_size {
            font-size: smaller;
        }
        .image-wrap {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        align-items: center;
        }
        .image-wrap img {
        border: 2px solid var(--color-gfg);
        border-radius: 50px;
        width: 100px;
        height: 100px;
        object-fit: cover;
        padding-right: 10px;
        }
        .author {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        align-items: center;
        width: 780px;
        height: 180px;
        }
        .authorheader {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        align-items: center;
        width: 780px;
        height: 100px;
        margin-top: -80px;
        
        }
        .footer_line1{
        font-size: small;
        }
        .footer_line2{
        font-size: small;
        }
    </style>
   <div class="a4size">                    
                    <div class="authorheader" style="margin-left: 10px";>
                            <img src="../../<? echo return_field_value("header_location","template_pad",
                            "company_id =".$beneficiary_name." and is_deleted=0 and status_active=1"); ?>" style="width:794px;height: 100px; />
                    </div> 
        <div class="author" style="margin-left: 10px";>
            <table width="794" cellpadding="0" cellspacing="0" border="0" style="margin-left: 30px;">
                <br>
                <tr><td colspan="3" height="100"></td></tr>
                <tr>
                    <td width="25"></td>
                    <td width="650" align="left">
                            <? 
                            if($lien_date==""){
                                echo "";
                            }
                            else echo date('M d, Y',strtotime($lien_date));
                            ?>
                        </td>
                </tr>
                <tr><td colspan="3" height="10"></td></tr>
                <tr>
                    <td width="25"></td>
                    <td width="650" align="left"><?echo "File No "."$internal_file_no"."<br>";?></td>
                    <td width="25" ></td>
                </tr>
                <tr><td colspan="3" height="50"></td></tr>
                <tr>
                    <td width="25"></td>
                    <td width="650" align="left">To</td>
                    <td width="25" ></td>
                </tr>
                <tr>
                    <td width="25" ></td>
                    <td width="650" align="left">
                    <?
                        echo $designation."<br>";
                        echo $bank_name."<br>";
                        echo $branch_name." Branch.<br>";
                        echo $address;
                    ?>.
                    </td>
                    <td width="25" ></td>
                </tr>
                <tr>
                    <td colspan="3" height="20"></td>
                </tr>
                <tr>
                    <td width="25" ></td>
                    <td width="650" align="justify">
                    <strong><? echo "Subject:  Submission of export lc for lien against the Buyer "."$buyer_name"; ?></strong></td>
                    <td width="25" ></td>
                </tr>
                <tr>
                    <td colspan="3" height="25"></td>
                </tr>
                <tr>
                    <td width="25" ></td>
                    <td width="650" align="left"> Dear Sir, </td>
                    <td width="25" ></td>
                </tr>
                <tr>
                <tr><td colspan="3" height="30"></td></tr>
                </tr>
                <tr>
                    <td width="25" ></td>
                    <td width="650" align="justify">We are submitted export lc for keeping it under lien.</td>
                    <td width="25" ></td>
                </tr>
                <tr>
                    <td colspan="3" height="25"></td>
                </tr>
            </table>
            <table width="794" cellpadding="0" cellspacing="0" border="1px" padding left="20px" style="margin-left: 50px;">
                <tr> 
                    <th width="380"><b>Exp Lc No </th>
                    <th width="380"><b>Date of Issue  </th>
                    <th width="380"><b> Value <? echo "("."$currency_sign".")" ;?></th>
                    <th width="380"><b>Last Shipment Date</th>
                    <th width="380"><b>Payment Mode </th>
                    <th width="380"><b>BBLC to be Opened (%)  </th>
                </tr>
                <tr> 
                    <td width="380" align="center" height="50"><? echo "$EXPORT_LC_NO"; ?></td>
                    <td width="380" align="center" height="50"><? echo "$LC_DATE"; ?></td>
                    <td width="380" align="center" height="50"><? echo "$currency_sign "."$LC_VALUE"; ?></td>
                    <td width="380" align="center" height="50"><? echo "$last_shipment_date"; ?></td>
                    <td width="380" align="center" height="50"><? echo "$payTerm"; ?></td>
                    <td width="380" align="center" height="50"><? echo "$max_btb_limit"."%"; ?></td>
                </tr>
            </table>
            <table width="794" cellpadding="0" cellspacing="0" border="0" >
                    <br>
                    <tr>
                        <td width="40"></td>
                    </tr>
                    <tr><td colspan="3" height="30"></td></tr>
                    <tr>
                        <td width="55" ></td>
                        <td width="650" align="justify">Please issue as a Lien certificate in our favour.</td>
                        <td width="25" ></td>
                    </tr>
                    <tr><td colspan="3" height="25"></td></tr>
                    <tr>
                        <td width="25" ></td>
                        <td width="650" align="left"> Thanking you. </td>
                        <td width="25" ></td>
                    </tr>
                    <tr><td colspan="3" height="50"></td></tr>
                    <tr>
                        <td width="25" ></td>
                        <td width="650" align="" style="color: black;background-color: yellow;"><b>N.B: Please keep lien, but do not replace against Sales Contract.</b></td>
                        <td width="25" ></td>
                    </tr>
                    <tr><td colspan="3" height="300"></td></tr>
            </table>
            <table width="794" cellpadding="0" cellspacing="0" border="0" >
                    <div class="authorfooter" style="margin-left: 10px">
                        <img src="../../<? echo return_field_value("FOOTER_LOCATION","template_pad","company_id =".$beneficiary_name." and is_deleted=0 and status_active=1"); ?>" style="width:794px;height: 100px;"  />
                    </div>
            </table>
        </div>    
    </div>
    <?
    exit();
}

if($action== "designation_search")
{
	echo load_html_head_contents("Item Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$system_id = $txt_system_id;
	?>
	<script>
		function js_set_value_lc_lien3(data) {
			var str=data.split("_");
			var system_id = str[0];
			var designation = str[1];
			print_report(4+'**'+system_id+'**'+designation,'lc_lien3','export_lc_controller');
		}
	</script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" width="600" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Serial</th>
                    <th>Designation</th>
                </thead>
                <tbody>
                	<tr onClick="js_set_value_lc_lien3('<?php echo $system_id.'_'.'Asst General Manager';?>')">
                        <td align="center">	1 </td>
                        <td align="left">Asst General Manager</td>                 
                    </tr>
					<tr onClick="js_set_value_lc_lien3('<?php echo $system_id.'_'.'Deputy General Manager';?>')">
                        <td align="center">	2 </td>
                        <td align="left">Deputy General Manager
					</td>                 
                    </tr>
					<tr onClick="js_set_value_lc_lien3('<?php echo $system_id.'_'.'General Manager';?>')">
                        <td align="center">	3 </td>
                        <td align="left">General Manager</td>                 
                    </tr>
            	</tbody>
           	</table>
		</fieldset>
            <div style="margin-top:15px" id="search_div"></div>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}

if ($action == "lc_lien3") // lc_lien3
{
    //echo $data; die;
    $data = explode("**", $data);
    $designation_popup  = $data[2];

    //export lc lien-----------------
    $company_lib=return_library_array( "select id, company_name from lib_company",'id','company_name');
    $buyer_lib=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    if ($data[0] == 4) 
    {
        $data_array = sql_select("SELECT id, export_lc_system_id,currency_name, beneficiary_name, export_lc_no, lc_date, lien_bank, lien_date, lc_value, internal_file_no, bank_file_no, buyer_name, pay_term, tenor, last_shipment_date, expiry_date
            from com_export_lc where id='$data[1]'");
        foreach ($data_array as $row) 
        {
            $system_ref = $row[csf("export_lc_system_id")];
            $lc_id = $row[csf("id")];
            $internal_file_no = $row[csf("internal_file_no")];
            $bank_file_no = $row[csf("bank_file_no")];
            $lien_date = change_date_format($row[csf("lien_date")]);
            $lien_bank = strtoupper($row[csf("lien_bank")]);
            $lc_no = $row[csf("export_lc_no")];
            $lc_date = change_date_format($row[csf("lc_date")]);
            $lc_value = def_number_format($row[csf("lc_value")],2);
            $lc_values = $row[csf("lc_value")];
            $currency_name = $currency[$row[csf("currency_name")]];
            $company_name = strtoupper($company_lib[$row[csf("beneficiary_name")]]);
            $buyer_name = $row[csf("buyer_name")];
            $payTerm = $pay_term[$row[csf("pay_term")]];
            $tenor = $row[csf("tenor")];
            $last_shipment_date = $row[csf("last_shipment_date")];
            $expiry_date = $row[csf("expiry_date")];
        }
        $data_array1 = sql_select("SELECT wm.total_set_qnty as ratio, ci.attached_qnty, ci.attached_value
        from wo_po_break_down wb, wo_po_details_master wm, com_export_lc_order_info ci 
        where wb.job_no_mst=wm.job_no and wb.id=ci.wo_po_break_down_id and ci.com_export_lc_id='$data[1]' and ci.status_active='1' and ci.is_deleted='0' order by ci.id");
        foreach ($data_array1 as $row1) 
        {
            $order_qnty_in_pcs = $row1[csf('attached_qnty')] * $row1[csf('ratio')];
            $total_attach_qty += $order_qnty_in_pcs;
            $total_attached_value += $row1[csf('attached_value')];
        }
    }


    $sql_sc=sql_select("SELECT a.com_export_lc_id, b.contract_no as sc_no, b.contract_value, b.contract_date as sc_date
    from  com_export_lc_atch_sc_info a, com_sales_contract b
    where a.com_sales_contract_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.com_export_lc_id='$data[1]'");
    
    $sql_sc_arr=array();
    foreach($sql_sc as $row)
    {
        $sql_sc_arr[$row[csf("com_export_lc_id")]]["sc_no"].=$row[csf("sc_no")].", ";
        $sql_sc_arr[$row[csf("com_export_lc_id")]]["sc_date"].=$row[csf("sc_date")].",";
    }
    /*echo '<pre>';
    print_r($sql_sc_arr);*/

    $designation_library=return_library_array( "select id,custom_designation from lib_designation", "id", "custom_designation");
    //bank information retriving here
    $data_array1 = sql_select("select id, bank_name, branch_name, contact_person, address,designation from lib_bank where id='$lien_bank'");
    foreach ($data_array1 as $row1) 
    {
        $bank_name = ucwords($row1[csf("bank_name")]);
        $branch_name = ucwords($row1[csf("branch_name")]);
        $contact_person = ucwords($row1[csf("contact_person")]);
        $address = ucwords($row1[csf("address")]);
        $designation = ucwords($designation_library[$row1[csf("designation")]]);
    }

    ?>
    <style type="text/css">
        .a4size {
           width: 21cm;
           height: 26.7cm;
           font-family: Bookman Old Style;
        }
        @media print {
        .a4size{ font-family: Bookman Old Style;font-size: 18px;margin: 80px 100PX 54px 25px;
            }
        size: A4 portrait;
        }
        .parent {
          display: flex;
          flex-direction:row;
          margin-left: 28px;
        }

        .column {
          flex: 1 1 0px;
          margin-right: 30px;
        }
    </style>
    <div class="a4size">
        <table width="794" cellpadding="0" cellspacing="0" border="0" >
            <div style="text-align: center; margin-top: 60px; padding-top: 50;">
            </div>
            <div class="parent" >
                Date: <? echo date('M d, Y',strtotime($lc_date));?>

            </div>
            <br>
            <tr>
                <td width="25"></td>
                <td width="650" align="left">To</td>
                <td width="25" ></td>
            </tr>
            <tr>
                <td width="25" ></td>
                <td width="650" align="left">
                <?
                    echo $designation_popup."<br>";
                    echo $bank_name."<br>";
                    echo $address;
                ?>.
                </td>
                <td width="25" ></td>
            </tr>
            <tr>
                <td colspan="3" height="20"></td>
            </tr>

            <tr>
                <td width="25" ></td>
                <td width="650" align="justify">
                <strong>Sub:  
                Application for lien the Replacement Export L/C No. <? echo $lc_no;?> DT. <? echo $lc_date; ?>  value US$. <? echo $lc_value;

                if (chop($sql_sc_arr[$lc_id]["sc_no"],",")!="") 
                {
                    ?> against Export Sales <u>Contract No. <?
                    echo chop($sql_sc_arr[$lc_id]["sc_no"],", ")." ";
                    $scDate=chop($sql_sc_arr[$lc_id]["sc_date"],",");
                    $scDateArr=array_unique(explode(",",$scDate));
                    $sc_all_date ="";
                    foreach ($scDateArr as $key => $value) 
                    {
                        if ($sc_all_date=="") 
                        {
                            $sc_all_date.= change_date_format($value);
                        }
                        else 
                        {
                            $sc_all_date.= ', '.change_date_format($value);
                        }
                    }
                    echo "DT. ". $sc_all_date;
                }
                ?> </u>
               </strong></td>
                <td width="25" ></td>
            </tr>
            <tr>
                <td colspan="3" height="40"></td>
            </tr>
            </tr>
            <tr>
                <td width="25" ></td>
                <td width="650" align="left"> Dear Sir, </td>
                <td width="25" ></td>
            </tr>
            <tr>
                <td colspan="3" height="20"></td>
            </tr>
            <tr>
                <td width="25" ></td>
                <td width="650" align="justify">
                We informed you that we have received Export L/C No. 
                <? echo $lc_no;?> DT. <? echo $lc_date; ?>  value US$. <? echo $lc_value;

                if (chop($sql_sc_arr[$lc_id]["sc_no"],",")!="") 
                {
                    ?> against Export S/C No. <?
                    echo chop($sql_sc_arr[$lc_id]["sc_no"],", ")." ";
                    $scDate=chop($sql_sc_arr[$lc_id]["sc_date"],",");
                    $scDateArr=array_unique(explode(",",$scDate));
                    $sc_all_date ="";
                    foreach ($scDateArr as $key => $value) 
                    {
                        if ($sc_all_date=="") 
                        {
                            $sc_all_date.= change_date_format($value);
                        }
                        else 
                        {
                            $sc_all_date.= ', '.change_date_format($value);
                        }
                    }
                    echo "DT. ". $sc_all_date;
                }
                ?> 
                from our Buyer <? echo $buyer_lib[$buyer_name];?>, throw your branch. As per L/C terms and conditions we request to you please Lien the above Export L/C. 

                </td>
                <td width="25" ></td>
            </tr>
        </table>
       
        <table width="794" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td colspan="3" height="15"></td>
            </tr>

            <tr>
                <td colspan="3" height="15"></td>
            </tr>
            <tr>
                <td width="25" ></td>
                <td width="650" align="justify">
                Your kind Co-operation will be highly appreciated.
                </td>
                <td width="25" ></td>
            </tr>
            <tr>
                <td colspan="3" height="75"></td>
            </tr>
            <tr>
                <td width="25" ></td>
                <td width="650" align="justify">
                Thanking you.
                </td>
                <td width="25" ></td>
            </tr>
            <tr>
                <td colspan="3" height="50"></td>
            </tr>
        </table>
    </div>
    <?
    exit();
}
?>



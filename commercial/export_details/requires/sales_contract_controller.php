<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

//--------------------------- Start-------------------------------------//

if ($action=="get_btb_limit")
{
	$nameArray=sql_select( "SELECT max_btb_limit FROM variable_settings_commercial where company_name like '$data' and variable_list=6 and is_deleted = 0 AND status_active = 1" );
 	if($nameArray)
	{
		foreach ($nameArray as $row)
		{
			echo "document.getElementById('txt_max_btb_limit').value = '".$row[csf("max_btb_limit")]."';\n";
		}
	}
	else
	{
		echo "document.getElementById('txt_max_btb_limit').value ='';\n";
	}
}

if ($action=="file_write_mathod")
{
	$nameArray=sql_select( "SELECT internal_file_source FROM variable_settings_commercial where company_name like '$data' and variable_list=20 and is_deleted = 0 AND status_active = 1" );
	echo "$('#txt_internal_file_no').val('');\n";
 	if($nameArray[0][csf("internal_file_source")]==1)
	{
		echo "$('#txt_internal_file_no').attr('onDblClick','fn_file_no()');\n";
		echo "$('#txt_internal_file_no').attr('readonly',true);\n";
		echo "$('#txt_internal_file_no').attr('placeholder','Double Click');\n";
	} else if($nameArray[0][csf("internal_file_source")]==3)
    {
        echo "$('#txt_internal_file_no').attr('onDblClick','fn_file_no_library()');\n";
        echo "$('#txt_internal_file_no').attr('readonly',true);\n";
        echo "$('#txt_internal_file_no').attr('placeholder','Double Click');\n";
    } else
	{
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
}

if($action=="file_search")
{

	echo load_html_head_contents("Export SC Form", "../../../", 1, 1,'','1','');
	extract($_REQUEST);
	//echo $companyID;die;
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$sql="select b.file_no, a.company_name from wo_po_break_down b, wo_po_details_master a where a.id=b.job_id and a.status_active=1 and b.status_active=1 and a.company_name=$companyID and b.file_no is not null group by a.company_name,b.file_no";
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
						<table cellpadding="0" cellspacing="0" width="500" class="rpt_table" border="1" rules="all" id="table_body">
							<tbody>
							<?
							$sql_result=sql_select($sql);$i=1;
							foreach($sql_result as $row)
							{
								if ($i%2==0)
								$bgcolor="#E9F3FF";
								else
								$bgcolor="#FFFFFF";
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
						<script>setFilterGrid('table_body',-1);</script>
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

if($action=='file_upload')
{
	extract($_REQUEST);
	$data_array="";
	$id=return_next_id( "id","common_photo_library", 1 ) ;
	for($i=0;$i<count($_FILES['file']);$i++)
	{
		$filename = time(). $_FILES['file'][name][$i]; 
		$location = "../../../file_upload/".$filename;
		if(move_uploaded_file( $_FILES['file']['tmp_name'][$i], $location))
		{ 
			if($data_array!="") $data_array.=",";
			$data_array .="(".$id.",".$mst_id.",'sales_contract','file_upload/".$filename."','2','".$filename."')";
		}
		else
		{ 
			echo 0; 
		}
		$id++; 
	}
		
		
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	
	$field_array="id,master_tble_id,form_name,image_location,file_type,real_file_name";
	$rID=sql_insert("common_photo_library",$field_array,$data_array,1);
	if($db_type==0)
	{
		if($rID)
		{
			mysql_query("COMMIT");
			echo "0**".$new_system_id[0]."**".$id_mst;
		}
		else
		{
			mysql_query("ROLLBACK");
			echo "10**".$id_mst;
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID)
		{
			oci_commit($con);
			echo "0**".$new_system_id[0]."**".$id_mst;
		}
		else
		{
			oci_rollback($con);
			echo "10**".$id_mst;
		}
	}
	disconnect($con);
	die;
}


if ($action=="load_drop_down_applicant_name")
{
	$sql = "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (22,23)) group by a.id,a.buyer_name order by buyer_name";
 	echo create_drop_down( "txt_applicant_name", 162, $sql,"id,buyer_name", 1, "---- Select ----", 0, "" );
	exit();
}

if ($action=="load_drop_down_notifying_party")
{
	$sql = "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (4,6)) group by a.id,a.buyer_name order by buyer_name";
 	echo create_drop_down( "cbo_notifying_party", 162, $sql, "id,buyer_name", 0, "", '', '');
	exit();
}

if ($action=="load_drop_down_consignee")
{
	$sql = "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (5,6,100)) group by a.id,a.buyer_name order by buyer_name";
 	echo create_drop_down( "cbo_consignee", 162, $sql,"id,buyer_name", 0, "", '', '' );
	exit();
}

if ($action=="load_drop_down_buyer_search")
{
	if($data != 0){
		echo create_drop_down( "cbo_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/sales_contract_controller', this.value, 'load_drop_down_issue_bank', 'issue_bank_td' );" );
		exit();
	}
	else{
		echo create_drop_down( "cbo_buyer_name", 162, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/sales_contract_controller', this.value, 'load_drop_down_issue_bank', 'issue_bank_td' );" );
		exit();
	}
}

if ($action=="load_drop_down_issue_bank")
{
	$sql = "select a.bank_name as bank_name, a.id from lib_bank a, LIB_BUYER_TAG_BANK b where a.id=b.TAG_BANK and b.BUYER_ID='$data' and a.is_deleted=0 and a.status_active=1 and a.ISSUSING_BANK=1 order by bank_name";
	//echo $sql;
 	echo create_drop_down( "txt_issuing_bank", 162, $sql,"id,bank_name", 1, "---- Select ----", '', '' );
	exit();
}

if ($action=="eval_multi_select")
{
	echo "set_multiselect('cbo_notifying_party*cbo_consignee','0*0','0','','0*0');\n";
	exit();
}

if ($action==='company_variable_setting_check')
{
	$variable_setting = return_field_value('pi_source_btb_lc', 'variable_settings_commercial', "company_name=$data and variable_list=32", 'pi_source_btb_lc');
	$sql_btb=sql_select("select COST_HEADS_STATUS, PI_SOURCE_BTB_LC, MAX_BTB_LIMIT from  variable_settings_commercial where company_name=$data and variable_list=6");
	echo $variable_setting."__".$sql_btb[0]["PI_SOURCE_BTB_LC"]."__".$sql_btb[0]["MAX_BTB_LIMIT"];
	exit();
}

if($action=='populate_data_from_sales_contract')
{
	$btblc_library=return_library_array( "select id, lc_number from com_btb_lc_master_details", "id", "lc_number"  );

	$data_array=sql_select("SELECT id, contact_system_id, contract_no, contract_date, beneficiary_name, buyer_name, applicant_name, notifying_party, consignee, convertible_to_lc, lien_bank, lien_date, issuing_bank, trader, country_origin, contract_value, currency_name, tolerance, last_shipment_date, expiry_date, shipping_mode, pay_term, inco_term, inco_term_place, contract_source, port_of_entry, port_of_loading, port_of_discharge, internal_file_no, shipping_line, doc_presentation_days, max_btb_limit, foreign_comn, local_comn, remarks, tenor, discount_clauses, converted_from, converted_btb_lc_list, claim_adjustment, bank_file_no, sc_year, bl_clause, export_item_category, initial_contract_value, lc_for, estimated_qnty, ready_to_approved, approved from com_sales_contract where id='$data'");

	foreach ($data_array as $row)
	{
		$btblc_no=""; $btb_attach_id='';
		if($row[csf("converted_btb_lc_list")]!="")
		{
			$btblc_id=explode(",",$row[csf("converted_btb_lc_list")]);
			foreach($btblc_id as $val)
			{
				if($btblc_no=="") $btblc_no=$btblc_library[$val]; else $btblc_no.="*".$btblc_library[$val];

				$attach_id=return_field_value("id","com_btb_export_lc_attachment","import_mst_id=$val");
				if($btb_attach_id=="") $btb_attach_id=$attach_id; else $btb_attach_id.=",".$attach_id;
			}
		}

		$sales_cotract_no=return_field_value("contract_no","com_sales_contract","id='".$row[csf('converted_from')]."'");

		/*if($db_type==0)
		{
			$attached_po_id=return_field_value("group_concat(wo_po_break_down_id)","com_sales_contract_order_info","com_sales_contract_id=$data and status_active=1 and is_deleted=0");
		}
		else
		{
			$attached_po_id=return_field_value("LISTAGG(wo_po_break_down_id, ',') WITHIN GROUP (ORDER BY wo_po_break_down_id) as po_id","com_sales_contract_order_info","com_sales_contract_id=$data and status_active=1 and is_deleted=0","po_id");
		}*/

		$sc_amnd=return_field_value("count(id)","com_sales_contract_amendment","contract_id=$data and is_original=0 and status_active=1 and is_deleted=0");
		
		

		echo "document.getElementById('txt_system_id').value 			= '".$row[csf("id")]."';\n";
		echo "document.getElementById('contact_system_id').value 		= '".$row[csf("contact_system_id")]."';\n";
		echo "document.getElementById('cbo_beneficiary_name').value 	= '".$row[csf("beneficiary_name")]."';\n";
		echo "$('#cbo_beneficiary_name').attr('disabled','true')".";\n";
		echo "document.getElementById('txt_internal_file_no').value		= '".$row[csf("internal_file_no")]."';\n";
		echo "document.getElementById('txt_bank_file_no').value 		= '".$row[csf("bank_file_no")]."';\n";
		echo "document.getElementById('txt_year').value 				= '".$row[csf("sc_year")]."';\n";
		echo "document.getElementById('txt_contract_no').value 			= '".$row[csf("contract_no")]."';\n";
		//echo "$('#txt_contract_no').attr('disabled','true')".";\n";
		echo "document.getElementById('txt_contract_value').value 		= '".$row[csf("contract_value")]."';\n";
		echo "document.getElementById('txt_ini_contract_value').value 		= '".$row[csf("initial_contract_value")]."';\n";
		echo "document.getElementById('cbo_currency_name').value 		= '".$row[csf("currency_name")]."';\n";
		echo "document.getElementById('txt_contract_date').value 		= '".change_date_format($row[csf("contract_date")])."';\n";
		echo "document.getElementById('cbo_convertible_to_lc').value 	= '".$row[csf("convertible_to_lc")]."';\n";
		

		echo "load_drop_down( 'requires/sales_contract_controller', document.getElementById('cbo_beneficiary_name').value, 'load_drop_down_buyer_search', 'buyer_td_id' );\n";
		echo "load_drop_down( 'requires/sales_contract_controller', document.getElementById('cbo_beneficiary_name').value, 'load_drop_down_applicant_name','applicant_name_td');\n";
		echo "load_drop_down( 'requires/sales_contract_controller', document.getElementById('cbo_beneficiary_name').value, 'load_drop_down_notifying_party', 'notifying_party_td' );\n";
		echo "load_drop_down( 'requires/sales_contract_controller', document.getElementById('cbo_beneficiary_name').value, 'load_drop_down_consignee', 'consignee_td' );\n";
 		echo "get_php_form_data( document.getElementById('cbo_beneficiary_name').value, 'eval_multi_select', 'requires/sales_contract_controller' );\n";
		echo "document.getElementById('cbo_buyer_name').value			= '".$row[csf("buyer_name")]."';\n";
		echo "$('#cbo_buyer_name').attr('disabled','true')".";\n";
		echo "load_drop_down( 'requires/sales_contract_controller', document.getElementById('cbo_buyer_name').value, 'load_drop_down_issue_bank', 'issue_bank_td' );\n";
		//load_drop_down( 'requires/sales_contract_controller', this.value, 'load_drop_down_issue_bank', 'issue_bank_td' );
		echo "document.getElementById('txt_applicant_name').value		= '".$row[csf("applicant_name")]."';\n";
		echo "document.getElementById('cbo_notifying_party').value		= '".$row[csf("notifying_party")]."';\n";
		echo "document.getElementById('cbo_consignee').value			= '".$row[csf("consignee")]."';\n";
		echo "document.getElementById('cbo_lien_bank').value 			= '".$row[csf("lien_bank")]."';\n";
		echo "document.getElementById('txt_lien_date').value 			= '".change_date_format($row[csf("lien_date")])."';\n";
		echo "document.getElementById('txt_issuing_bank').value 			= '".$row[csf("issuing_bank")]."';\n";
		echo "document.getElementById('txt_trader').value 			= '".$row[csf("trader")]."';\n";
		echo "document.getElementById('txt_country_origin').value 			= '".$row[csf("country_origin")]."';\n";
		echo "document.getElementById('txt_last_shipment_date').value 	= '".change_date_format($row[csf("last_shipment_date")])."';\n";
		echo "document.getElementById('txt_expiry_date').value 			= '".change_date_format($row[csf("expiry_date")])."';\n";
		echo "document.getElementById('txt_tolerance').value 			= '".$row[csf("tolerance")]."';\n";
		echo "document.getElementById('cbo_shipping_mode').value 		= '".$row[csf("shipping_mode")]."';\n";
		echo "document.getElementById('cbo_pay_term').value 			= '".$row[csf("pay_term")]."';\n";
		echo "document.getElementById('txt_tenor').value 				= '".$row[csf("tenor")]."';\n";
		echo "document.getElementById('cbo_inco_term').value 			= '".$row[csf("inco_term")]."';\n";
		echo "document.getElementById('txt_inco_term_place').value 		= '".$row[csf("inco_term_place")]."';\n";
		echo "document.getElementById('cbo_contract_source').value 		= '".$row[csf("contract_source")]."';\n";
		echo "document.getElementById('txt_port_of_entry').value 		= '".$row[csf("port_of_entry")]."';\n";
		echo "document.getElementById('txt_port_of_loading').value 		= '".$row[csf("port_of_loading")]."';\n";
		echo "document.getElementById('txt_port_of_discharge').value 	= '".$row[csf("port_of_discharge")]."';\n";
		echo "document.getElementById('txt_shipping_line').value 		= '".$row[csf("shipping_line")]."';\n";
		echo "document.getElementById('txt_doc_presentation_days').value = '".$row[csf("doc_presentation_days")]."';\n";
		echo "document.getElementById('txt_max_btb_limit').value 		= '".$row[csf("max_btb_limit")]."';\n";
		echo "document.getElementById('txt_foreign_comn').value 		= '".$row[csf("foreign_comn")]."';\n";
		echo "document.getElementById('txt_local_comn').value 			= '".$row[csf("local_comn")]."';\n";
		echo "document.getElementById('txt_discount_clauses').value 	= '".$row[csf("discount_clauses")]."';\n";
		echo "document.getElementById('txt_claim_adjustment').value 	= '".$row[csf("claim_adjustment")]."';\n";
		echo "document.getElementById('txt_converted_from').value 		= '".$sales_cotract_no."';\n";
		echo "document.getElementById('txt_converted_from_id').value 	= '".$row[csf("converted_from")]."';\n";
		echo "document.getElementById('txt_converted_btb_lc').value 	= '".$btblc_no."';\n";
		echo "document.getElementById('txt_converted_btb_id').value 	= '".$row[csf("converted_btb_lc_list")]."';\n";
		echo "document.getElementById('txt_attach_row_id').value 		= '".$btb_attach_id."';\n";
		echo "document.getElementById('txt_remarks').value 				= '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('txt_bl_clause').value 			= '".$row[csf("bl_clause")]."';\n";
		echo "document.getElementById('cbo_export_item_category').value = '".$row[csf("export_item_category")]."';\n";
		echo "document.getElementById('cbo_lc_for').value 				= '".$row[csf("lc_for")]."';\n";
		echo "document.getElementById('txt_estimated_sc_qnty').value 	= '".$row[csf("estimated_qnty")]."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value 	= '".$row[csf("ready_to_approved")]."';\n";
		echo "$('#cbo_lc_for').attr('disabled','true')".";\n";

		if($row[csf("approved")]==1) echo "$('#approved').text('Approved');\n";
		elseif($row[csf("approved")]==3) echo "$('#approved').text('Partial Approved');\n";
		else echo "$('#approved').text('');\n";

		//echo "document.getElementById('hidden_selectedID').value 		= '".$attached_po_id."';\n";
		echo "convertible_to_lc_display();\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_sales_contract',1);\n";
		echo "set_multiselect('cbo_notifying_party*cbo_consignee','0*0','1','".$row[csf('notifying_party')]."*".$row[csf('consignee')]."','0*0');\n";

		if($sc_amnd>0)
		{
			echo "disable_enable_fields('txt_contract_value*txt_last_shipment_date*txt_expiry_date*cbo_shipping_mode*cbo_inco_term*txt_inco_term_place*txt_port_of_entry*txt_port_of_loading*txt_port_of_discharge*cbo_pay_term*txt_tenor*txt_claim_adjustment*txt_discount_clauses*txt_bl_clause*txt_remarks',1);\n";
		}
		else
		{
			echo "disable_enable_fields('txt_last_shipment_date*txt_expiry_date*cbo_shipping_mode*cbo_inco_term*txt_inco_term_place*txt_port_of_entry*txt_port_of_loading*txt_port_of_discharge*cbo_pay_term*txt_tenor*txt_claim_adjustment*txt_discount_clauses*txt_bl_clause*txt_remarks',0);\n";
		}		

		$is_invoice=return_field_value("id","com_export_invoice_ship_mst","benificiary_id='".$row[csf("beneficiary_name")]."' and lc_sc_id=$data and is_lc=2 and status_active=1 and is_deleted=0");
		//if($is_invoice && $row[csf("convertible_to_lc")]==2) echo "$('#cbo_convertible_to_lc').attr('disabled',true);\n";
		//else echo "$('#cbo_converti
		$sc_attach_lc=return_field_value("id","com_export_lc_atch_sc_info","com_sales_contract_id=$data and status_active=1 and is_deleted=0");
		if($sc_attach_lc) { echo "$('#cbo_convertible_to_lc').attr('disabled',true);\n"; }
		else if($is_invoice && $row[csf("convertible_to_lc")]==2) { echo "$('#cbo_convertible_to_lc').attr('disabled',true);\n"; }
		else { echo "$('#cbo_convertible_to_lc').attr('disabled',false);\n"; }


		exit();
	}
}


if($action=="fake_sc")
{
	echo load_html_head_contents("Sales Contract Form", "../../../", 1, 1,'','1','');
	extract($_REQUEST);
	?>
	<script>
	function js_set_value( contract_no_id )
	{
		contract_no=contract_no_id.split("_");
		document.getElementById('hidden_contract_no').value=contract_no[0];
		document.getElementById('hidden_contract_id').value=contract_no[1];
		parent.emailwindow.hide();
	}
	</script>
	</head>

	<body>
	<div align="center" style="width:100%" >
	<form name="search_sc_frm"  id="search_sc_frm">
		<fieldset style="width:600px">
			<?
				$sql = "SELECT id, contract_no, contract_value, contract_date, buyer_name FROM com_sales_contract where beneficiary_name='$beneficiary' and convertible_to_lc=3 and is_deleted = 0 AND status_active = 1";
				$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
				$arr=array (0=>$buyer_arr);
				echo create_list_view("list_view", "Buyer Name,Contract No,Contract Value,Contract Date", "200,150,100","600","320",0, $sql , "js_set_value", "contract_no,id", "", 1, "buyer_name,0,0,0", $arr , "buyer_name,contract_no,contract_value,contract_date", "",'','0,0,2,3') ;

			?>
		</fieldset>
        	<input type="hidden" id="hidden_contract_no" />
            <input type="hidden" id="hidden_contract_id" />
		</form>
	   </div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}


if($action=="fake_btb")
{
	echo load_html_head_contents("Sales Contract Form", "../../../", 1, 1,'','1','');
	extract($_REQUEST);

	$btb_id_array=explode(",",$txt_converted_btb_id);
	?>

	<script>

	 var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click');
			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( str )
		{

			if (str!="") str=str.split("_");

			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );

			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );
				selected_attach_id.push( str[3] );
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				selected_attach_id.splice( i, 1 );
			}
			var id =''; var name =''; var attach_id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
				attach_id += selected_attach_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			attach_id = attach_id.substr( 0, attach_id.length - 1 );

			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name );
			$('#txt_attach_id').val( attach_id );
		}

    </script>
	</head>

	<body>
	<div align="center" style="width:730px;" >
	<form name="search_btb_frm"  id="search_btb_frm">
        <fieldset style="width:730px">
            <input type="hidden" name="txt_selected" id="txt_selected" class="text_boxes" readonly />
            <input type="hidden" name="txt_selected_id" id="txt_selected_id" class="text_boxes" readonly />
            <input type="hidden" name="txt_attach_id" id="txt_attach_id" class="text_boxes" readonly />
            <table width="100%" style="margin-top:5px">
                <tr>
                    <td>
					<?
						$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');

						$arr=array (3=>$supplier_arr,4=>$item_category);
						if($txt_converted_btb_id=="")
						{
							$sql = "SELECT a.id, a.lc_number, a.lc_value, a.lc_date, a.supplier_id, a.item_category_id, b.id as attach_id FROM com_btb_lc_master_details a, com_btb_export_lc_attachment b where a.id=b.import_mst_id and b.lc_sc_id='$sales_contract' and b.is_lc_sc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.lc_number, a.lc_value, a.lc_date, a.supplier_id, a.item_category_id, b.id";
						}
						else
						{
							$sql = "SELECT a.id, a.lc_number, a.lc_value, a.lc_date, a.supplier_id, a.item_category_id, b.id as attach_id FROM com_btb_lc_master_details a, com_btb_export_lc_attachment b where a.id=b.import_mst_id and (b.lc_sc_id='$sales_contract' or a.id not in($txt_converted_btb_id)) and b.is_lc_sc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.lc_number, a.lc_value, a.lc_date, a.supplier_id, a.item_category_id, b.id";
						}

						echo create_list_view("tbl_list_search", "BTB LC No,LC Value,LC Date,Supplier,Item Group", "130,130,100,130,130","700","300",0, $sql , "js_set_value", "id,lc_number,attach_id", "", 1, "0,0,0,supplier_id,item_category_id", $arr , "lc_number,lc_value,lc_date,supplier_id,item_category_id", "","",'0,2,3,0,0','',1) ;

					?>
                    </td>
                </tr>
                <tr>
                    <td align="center"><input type="hidden" name="close" onClick="parent.emailwindow.hide();"  class="formbutton" value="Close" /></td>
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


if($action=="sales_contact_search")
{
	 echo load_html_head_contents("Sales Contract Form", "../../../", 1, 1,'','1','');
	 extract($_REQUEST);  
	?>
	<script>
		function js_set_value(id)
		{
			$('#hidden_sales_contract_id').val(id);
			parent.emailwindow.hide();
		}

    </script>

	</head>

	<body>
	<div align="center" style="width:1130px;">
		<form name="searchscfrm"  id="searchscfrm">
			<fieldset style="width:1128px; margin-left:3px">
				<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="100%" class="rpt_table">
					<thead>
						<th>Company</th>
						<th>Buyer</th>
						<th>File Year</th>
						<th>Search By</th>
						<th>Enter</th>
						<th>Date Range</th>
						<th><input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
						<input type="hidden" name="id_field" id="id_field" value="" /></th>
					</thead>
					<tr class="general">
						<td>
							<?
							//echo $beneficiary; 
								echo create_drop_down( "cbo_company_name", 162, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--- Select ---",$beneficiary, "load_drop_down( 'sales_contract_controller', this.value, 'load_drop_down_buyer_search', 'buyer_td_id' );" );
							?>
						</td>
						<td id="buyer_td_id">
							<?
							// echo create_drop_down( "cbo_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$beneficiary' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
							?>
							<?php
							echo create_drop_down( "cbo_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$beneficiary' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name  order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
							?>
						</td>
						<td>
							<?
								$file_year_arr=return_library_array( "select sc_year from com_sales_contract where beneficiary_name=$beneficiary and is_deleted=0 order by sc_year",'sc_year','sc_year');
								echo create_drop_down( "cbo_file_year", 80, $file_year_arr,"", 1, "-- All --", 0, "" );
							?>
						</td>
						<td>
							<?
								$arr=array(1=>'SC No',2=>'File No',3=>'Bank File No');
								echo create_drop_down( "cbo_search_by", 162, $arr,"", 0, "", 1, "" );
							?>
						</td>
						<td id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
							<input type="hidden" id="hidden_sales_contract_id" />
						</td>
						<td align="center">
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">To
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
						</td>
						<td>
							<input type="button" id="search_button" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $beneficiary; ?>'+'**'+document.getElementById('cbo_file_year').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_sc_search_list_view', 'search_div', 'sales_contract_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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

if ($action=="order_popup")
{
	echo load_html_head_contents("Sales Contract Form", "../../../", 1, 1,'','1','');
	extract($_REQUEST);
	//echo $cbo_lc_for.test;die;
	?>

	<script>
	 var selected_id = new Array, selected_name = new Array();
	 function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( str ) {
			if($("#search"+str).css("display") !='none')
			{
				toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

				if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
					selected_id.push( $('#txt_individual_id' + str).val() );

				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
					}
					selected_id.splice( i, 1 );
				}
				var id = '';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
				}
				id = id.substr( 0, id.length - 1 );

				$('#txt_selected_id').val( id );
			}
		}
		function fn_order_list()
        {
			if($("#chk_related_order").prop('checked')) var related_order_check=1; else related_order_check=0;
			if($("#chk_country").prop('checked')) var chk_country=1; else chk_country=0;
			show_list_view ( document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_text').value+'**'+document.getElementById('hidden_type').value+'**'+document.getElementById('hidden_buyer_id').value+'**'+document.getElementById('hidden_po_selectedID').value+'**'+document.getElementById('sales_contractID').value+'**'+document.getElementById('txt_file_no').value+'**'+document.getElementById('txt_sc_lc').value + '**' + document.getElementById('ship_start_date').value + '**' + document.getElementById('ship_end_date').value+'**'+<? echo $cbo_export_item_category; ?>+ '**' + related_order_check +'**'+ '<? echo $lc_sc_no; ?>'+'**'+ document.getElementById('cbo_year_selection').value+'**'+<? echo $cbo_lc_for; ?>+'**'+ document.getElementById('cbo_string_search_type').value+ '**' + chk_country, 'create_po_search_list_view', 'search_div', 'sales_contract_controller', 'setFilterGrid(\'tbl_list_search\',-1)');
		}
		

    </script>

	</head>

	<body>
	<div align="center" style="width:100%;" >
		<form name="searchpofrm"  id="searchpofrm">
			<fieldset style="width:1220px">
				<table width="1200" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all">
					<thead>
						<tr>
							<th colspan="10">
								<?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?>
                                
							</th>
						</tr>
						<tr>
							<th>Company</th>
							<?
							if($cbo_lc_for==2)
							{
								$field_display=' style="display:none"';
								?>
								<th>Requisition Year</th>
								<th style="display:none">Search By</th>
								<th style="display:none">Search</th>
								<th>Style No</th>
								<th>Requisition No</th>
								<th>Requisition Date</th>
                                <th style="display:none">Country</th>
								<th style="display:none">Related Orders</th>
								<?
							}
							else
							{
								$field_display='';
								?>
								<th>Job Year</th>
								<th>Search By</th>
								<th>Search</th>
								<th>File No</th>
								<th>SC/LC</th>
								<th>Shipment Date</th>
								<th>Country</th>
                                <th>Related Orders</th>
								<?
							}
							?>							
							<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;"></th>
						</tr>
					</thead>
					<tr class="general">
						<td align="center">
							<?
								echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "-- Select Company --", $company_id,"",1 );
							?>
						</td>
						<td align="center">
							<?
								echo create_drop_down("cbo_year_selection", 65, create_year_array(), "", 1, "All Year", date("Y", time()), "", 0, "");
							?>
						</td>
						<td align="center" <?= $field_display;?>>
							<?
								$arr=array(1=>'PO Number',2=>'Job No',3=>'Style Ref No',4=>'Internal Ref',5=>'Actual PO');
								echo create_drop_down( "cbo_search_by", 150, $arr,"",0, "--- Select ---", '',"" );
								if($cbo_export_item_category==10) $is_sales=1; else if($cbo_lc_for==2) $is_sales=3; else $is_sales=0;
							?>
						</td>
						<td align="center" <?= $field_display;?>>
							<input type="text" name="txt_search_text" id="txt_search_text" class="text_boxes" style="width:150px" />
							<input type="hidden" id="hidden_type" value="<? echo $types; ?>" />
							<input type="hidden" id="hidden_buyer_id" value="<? echo $buyer_id; ?>" />
							<input type="hidden" id="hidden_po_selectedID" value="<? echo $selectID; ?>" />
							<input type="hidden" id="sales_contractID" value="<? echo $sales_contractID; ?>" />
							<input type="hidden" name="txt_selected_id" id="txt_selected_id" value="" />
							<input type="hidden" name="txt_is_sales" id="txt_is_sales" value="<? echo $is_sales; ?>" />	
						</td>
						<td align="center">
							<input type="text" id="txt_file_no" name="txt_file_no" class="text_boxes" style="width:80px" >
						</td>
						<td align="center">
							<input type="text" id="txt_sc_lc" name="txt_sc_lc" class="text_boxes" style="width:80px">
						</td>
						<td>
							<input type="text" name="ship_start_date" id="ship_start_date" class="datepicker" style="width:55px;" />To
							<input type="text" name="ship_end_date" id="ship_end_date" class="datepicker" style="width:55px;" />
						</td>
                        <td <?= $field_display;?>><input type="checkbox" id="chk_country" name="chk_country" /> </td>
                        <td <?= $field_display;?>><input type="checkbox" id="chk_related_order" name="chk_related_order" /> </td>
						<td align="center">
							<input type="button" name="button" class="formbutton" value="Show" onClick="fn_order_list();" style="width:100px;" />
						</td>
				</tr>
			</table>
			<div style="width:1280px; margin-top:5px" id="search_div" align="left"></div>
		</fieldset>
		</form>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
  exit();
}


if($action=="create_po_search_list_view")
{
	$data=explode('**',$data);
	//echo $data[13].test;die;
	$check_related_order=$data[12];
	$lc_sc_no=$data[13];
	$job_year=$data[14];
	$cbo_lc_for=$data[15];
	$search_type=$data[16];
	$chk_country=$data[17];
	
	//echo $chk_country.jahid;die;

	if($_SESSION['logic_erp']['buyer_id']!='' && $_SESSION['logic_erp']['buyer_id']!=0){$user_buyer=" and wm.buyer_name in (".$_SESSION['logic_erp']['buyer_id'].")";}
	if($_SESSION['logic_erp']['brand_id']!='' && $_SESSION['logic_erp']['brand_id']!=0){$user_brand=" and wm.brand_id in (".$_SESSION['logic_erp']['brand_id'].")";}

	$cbo_export_item_category = $data[11];
	if ($data[0]!=0)
	{
		if($cbo_export_item_category==10 || $cbo_lc_for==2)
		{
			$company = " and wm.company_id='$data[0]'"; 
		}
		else
		{
			$company = " and wm.company_name='$data[0]'"; 
		}
	}
	else { echo "Please Select Company First."; die; }
	
	if ($data[2]!='')
	{
		$search_text='';
		$actual_po_cond="";
		$actual_po_search_type=0;
		if($cbo_export_item_category==10)
		{
			if ($data[1] == 1)
			{
				if($search_type==1){ $search_text = " and wm.job_no_prefix_num ='" . trim($data[2]) . "'"; }
				else if($search_type==2){ $search_text = " and wm.job_no_prefix_num like '" . trim($data[2]) . "%'"; }
				else if($search_type==3){ $search_text = " and wm.job_no_prefix_num like '%" . trim($data[2]) . "'"; }
				else{ $search_text = " and wm.job_no_prefix_num like '%" . trim($data[2]) . "%'"; }
			}
			else if ($data[1] == 2)
			{
				// $search_text = " and wm.job_no_prefix_num like '%" . trim($data[2]) . "'";
				if($search_type==1){ $search_text = " and wm.job_no_prefix_num ='" . trim($data[2]) . "'"; }
				else if($search_type==2){ $search_text = " and wm.job_no_prefix_num like '" . trim($data[2]) . "%'"; }
				else if($search_type==3){ $search_text = " and wm.job_no_prefix_num like '%" . trim($data[2]) . "'"; }
				else{ $search_text = " and wm.job_no_prefix_num like '%" . trim($data[2]) . "%'"; }

			}
			else if ($data[1] == 3)
			{
				if($search_type==1){ $search_text = " and wm.style_ref_no ='" . trim($data[2]) . "'"; }
				else if($search_type==2){ $search_text = " and wm.style_ref_no like '" . trim($data[2]) . "%'"; }
				else if($search_type==3){ $search_text = " and wm.style_ref_no like '%" . trim($data[2]) . "'"; }
				else{ $search_text = " and wm.style_ref_no like '%" . trim($data[2]) . "%'"; }
			}			
		}
		else
		{
			/*if($data[1]==1)
				$search_text=" and wb.po_number like '".trim($data[2])."%'";*/
			if($data[1]==1)
			{
				$ex_data = explode(',', $data[2]);
				$search_text=' and (';
				foreach ($ex_data as $val) {
					// $search_text.="wb.po_number like '".trim($val)."%'".' or ';
					if($search_type==1){ $search_text.= " wb.po_number ='" . trim($val) . "'".' or '; }
					else if($search_type==2){ $search_text.= " wb.po_number like '" . trim($val) . "%'".' or '; }
					else if($search_type==3){ $search_text.= " wb.po_number like '%" . trim($val) . "'".' or '; }
					else{ $search_text.= " wb.po_number like '%" . trim($val) . "%'".' or '; }
				}
				$search_text=rtrim($search_text,' or');
				$search_text.=')';				
			}				
			else if($data[1]==2)
			{
				if($search_type==1){ $search_text = " and wm.job_no ='" . trim($data[2]) . "'"; }
				else if($search_type==2){ $search_text = " and wm.job_no like '" . trim($data[2]) . "%'"; }
				else if($search_type==3){ $search_text = " and wm.job_no like '%" . trim($data[2]) . "'"; }
				else{ $search_text = " and wm.job_no like '%" . trim($data[2]) . "%'"; }
			}
			else if($data[1]==3)
			{
				if($search_type==1){ $search_text = " and wm.style_ref_no ='" . trim($data[2]) . "'"; }
				else if($search_type==2){ $search_text = " and wm.style_ref_no like '" . trim($data[2]) . "%'"; }
				else if($search_type==3){ $search_text = " and wm.style_ref_no like '%" . trim($data[2]) . "'"; }
				else{ $search_text = " and wm.style_ref_no like '%" . trim($data[2]) . "%'"; }
			}
			else if($data[1]==4)
			{
				if($search_type==1){ $search_text = " and wb.grouping ='" . trim($data[2]) . "'"; }
				else if($search_type==2){ $search_text = " and wb.grouping like '" . trim($data[2]) . "%'"; }
				else if($search_type==3){ $search_text = " and wb.grouping like '%" . trim($data[2]) . "'"; }
				else{ $search_text = " and wb.grouping like '%" . trim($data[2]) . "%'"; }
			}
			else if ($data[1] == 5)
			{
				$actual_po_search_type=1;
				if($search_type==1){ $actual_po_cond = " and acc_po_no ='" . trim($data[2]) . "'"; }
				else if($search_type==2){ $actual_po_cond = " and acc_po_no like '" . trim($data[2]) . "%'"; }
				else if($search_type==3){ $actual_po_cond = " and acc_po_no like '%" . trim($data[2]) . "'"; }
				else{ $actual_po_cond = " and acc_po_no like '%" . trim($data[2]) . "%'"; }
			}
		}
	}
	// echo $search_text;die;
	$action_types = $data[3];
	$buyer_id = $data[4];
	$sales_contractID = $data[6];
	$txt_file_no = $data[7];
    $txt_sc_lc = $data[8];
	$ship_start_date = $data[9];
    $ship_end_date = $data[10];
	
	
	$year_field="";
	$year_field_cond="";
	if($db_type==0) 
	{
		$year_field="YEAR(wm.insert_date) as year";
		if ($job_year!=0) $year_field_cond=" and YEAR(wm.insert_date)=$job_year";
	} 
	else
	{
		$year_field="to_char(wm.insert_date,'YYYY') as year";
		if ($job_year!=0) $year_field_cond=" and to_char(wm.insert_date,'YYYY')=$job_year";
	} 
	
	if($cbo_export_item_category!=10 && $cbo_lc_for!=2)
	{
		if($txt_file_no!="")
		{
			// $file_no_cond=" and wb.file_no='$data[7]'"; 
			if($search_type==1){ $file_no_cond = " and wb.file_no ='" . trim($data[7]) . "'"; }
			else if($search_type==2){ $file_no_cond = " and wb.file_no like '" . trim($data[7]) . "%'"; }
			else if($search_type==3){ $file_no_cond = " and wb.file_no like '%" . trim($data[7]) . "'"; }
			else{ $file_no_cond = " and wb.file_no like '%" . trim($data[7]) . "%'"; }
		}
		else{ $file_no_cond=""; }
    	if($txt_sc_lc!="")
		{
			// $txt_sc_lc_cond=" and wb.sc_lc like '%".trim($data[8])."%'"; 
			if($search_type==1){ $txt_sc_lc_cond = " and wb.sc_lc ='" . trim($data[8]) . "'"; }
			else if($search_type==2){ $txt_sc_lc_cond = " and wb.sc_lc like '" . trim($data[8]) . "%'"; }
			else if($search_type==3){ $txt_sc_lc_cond = " and wb.sc_lc like '%" . trim($data[8]) . "'"; }
			else{ $txt_sc_lc_cond = " and wb.sc_lc like '%" . trim($data[8]) . "%'"; }
		} 
		else{ $txt_sc_lc_cond=""; }

		$sql_actual_po="select po_break_down_id as PO_BREAK_DOWN_ID, listagg( cast(acc_po_no as varchar(4000)), ',') within group(order by acc_po_no) as ACC_PO_NO from wo_po_acc_po_info where status_active=1 and is_deleted=0 $actual_po_cond group by po_break_down_id";
		$sql_actual_po_res=sql_select($sql_actual_po);
		$actual_po_arr=array();
		$actual_poBreakDownId_arr=array();
		$actual_poBreakDownId_cond="";
		foreach ($sql_actual_po_res as $row)
		{
			$actual_po_arr[$row['PO_BREAK_DOWN_ID']]=$row['ACC_PO_NO'];
			if ($actual_po_search_type==1) $actual_poBreakDownId_arr[$row['PO_BREAK_DOWN_ID']]=$row['PO_BREAK_DOWN_ID'];

		}
		if (count($actual_poBreakDownId_arr)>0){
			$actual_poBreakDownId=implode(',',$actual_poBreakDownId_arr);
			$actual_poBreakDownId_cond=" and wb.id in($actual_poBreakDownId)";
		}
	}
	//echo $actual_poBreakDownId_cond;
	$selected_order_id = ""; 
	/*if($attached_po_id !="")
	{
		if($cbo_export_item_category==10) $selected_order_id = "and wm.id not in (".$attached_po_id.")"; else $selected_order_id = "and wb.id not in (".$attached_po_id.")";
	}*/
	
	$att_po_cond="";
	if($data[5] !="")
	{
		$att_po_cond=" and ";
		if($cbo_export_item_category==10)
		{
			$att_po_cond=" and wm.id not in($data[5])";
			$selected_order_id = "and wm.id not in (select wo_po_break_down_id AS po_id from com_sales_contract_order_info where com_sales_contract_id=$sales_contractID and status_active=1 and is_deleted=0) $att_po_cond"; 
		}
		else if($cbo_lc_for==2)
		{
			$att_po_cond=" and wm.id not in($data[5])";
			$selected_order_id = "and wm.id not in (select wo_po_break_down_id AS po_id from com_sales_contract_order_info where com_sales_contract_id=$sales_contractID and status_active=1 and is_deleted=0) $att_po_cond";
		}
		else 
		{
			$att_po_cond=" and wb.id not in($data[5])";
			$selected_order_id = "and wb.id not in (select wo_po_break_down_id AS po_id from com_sales_contract_order_info where com_sales_contract_id=$sales_contractID and status_active=1 and is_deleted=0) $att_po_cond";
		}
	}
	else
	{
		if($cbo_export_item_category==10) $selected_order_id = "and wm.id not in (select wo_po_break_down_id AS po_id from com_sales_contract_order_info where com_sales_contract_id=$sales_contractID and status_active=1 and is_deleted=0)";
		else if($cbo_lc_for==2) $selected_order_id = "and wm.id not in (select wo_po_break_down_id AS po_id from com_sales_contract_order_info where com_sales_contract_id=$sales_contractID and status_active=1 and is_deleted=0)"; 
		else $selected_order_id = "and wb.id not in (select wo_po_break_down_id AS po_id from com_sales_contract_order_info where com_sales_contract_id=$sales_contractID and status_active=1 and is_deleted=0)";
	}

	if ($ship_start_date != '' && $ship_end_date != '')
	{
		if($cbo_export_item_category==10)
		{
			if ($db_type == 0) {
				$date = "and wm.delivery_date between '" . change_date_format($ship_start_date, 'yyyy-mm-dd') . "' and '" . change_date_format($ship_end_date, 'yyyy-mm-dd') . "'";
			} else if ($db_type == 2) {
				$date = "and wm.delivery_date between '" . change_date_format($ship_start_date, '', '', 1) . "' and '" . change_date_format($ship_end_date, '', '', 1) . "'";
			}
		}
		else if($cbo_lc_for==2)
		{
			$date = "and wm.requisition_date between '" . change_date_format($ship_start_date, '', '', 1) . "' and '" . change_date_format($ship_end_date, '', '', 1) . "'";
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
	
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand brand",'id','brand_name');
	//echo $action_types;die;
	if($action_types=='attached_po_status' && $cbo_export_item_category!=10 && $cbo_lc_for!=2)
	{
		$lc_details=return_library_array( "select id, export_lc_no from com_export_lc",'id','export_lc_no');
		$sc_details=return_library_array( "select id, contract_no from com_sales_contract",'id','contract_no');
		$lc_file_no_cond="";
		if($txt_file_no!="") $lc_file_no_cond=" and b.internal_file_no = '$txt_file_no'"; 
		$lc_array=array(); $sc_array=array(); $attach_qnty_array=array();
		if($check_related_order && $lc_sc_no!="") $lc_sc_cond=" and wb.sc_lc='$lc_sc_no'";
		$sql_lc_sc="select a.com_export_lc_id as id, a.wo_po_break_down_id, b.internal_file_no, sum(a.attached_qnty) as qnty, 1 as type 
		from com_export_lc_order_info a, COM_EXPORT_LC b 
		where a.COM_EXPORT_LC_ID=b.id and a.IS_SERVICE<>1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $lc_file_no_cond
		group by a.com_export_lc_id, a.wo_po_break_down_id, b.internal_file_no
		union all
		select a.com_sales_contract_id as id, a.wo_po_break_down_id, b.internal_file_no, sum(a.attached_qnty) as qnty, 2 as type 
		from com_sales_contract_order_info a, COM_SALES_CONTRACT b 
		where a.COM_SALES_CONTRACT_ID=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $lc_file_no_cond
		group by a.com_sales_contract_id, a.wo_po_break_down_id, b.internal_file_no";
		$lc_sc_Array=sql_select($sql_lc_sc);$lc_sc_file_arr=array();
		foreach($lc_sc_Array as $row_lc_sc)
		{
			if(array_key_exists($row_lc_sc[csf('wo_po_break_down_id')],$attach_qnty_array))
			{
				 $attach_qnty_array[$row_lc_sc[csf('wo_po_break_down_id')]]+=$row_lc_sc[csf('qnty')];
			}
			else
			{
				$attach_qnty_array[$row_lc_sc[csf('wo_po_break_down_id')]]=$row_lc_sc[csf('qnty')];
			}
			$lc_sc_file_arr[$row_lc_sc[csf('wo_po_break_down_id')]]=$row_lc_sc[csf('internal_file_no')];
			if($row_lc_sc[csf('type')]==1)
			{
				
				if($row_lc_sc[csf('qnty')]>0)
				{
					if(array_key_exists($row_lc_sc[csf('wo_po_break_down_id')],$lc_array))
					{
						 $lc_array[$row_lc_sc[csf('wo_po_break_down_id')]].=",".$row_lc_sc[csf('id')];
					}
					else
					{
						$lc_array[$row_lc_sc[csf('wo_po_break_down_id')]]=$row_lc_sc[csf('id')];
					}
				}
			}
			else
			{
				if($row_lc_sc[csf('qnty')]>0)
				{
					if(array_key_exists($row_lc_sc[csf('wo_po_break_down_id')],$sc_array))
					{
						 $sc_array[$row_lc_sc[csf('wo_po_break_down_id')]].=",".$row_lc_sc[csf('id')];
					}
					else
					{
						$sc_array[$row_lc_sc[csf('wo_po_break_down_id')]]=$row_lc_sc[csf('id')];
					}
				}
			}
		}

		$sql = "SELECT wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst, wb.file_no, wm.style_ref_no, wm.gmts_item_id, wb.unit_price,wb.sc_lc, wm.brand_id, $year_field  
		FROM wo_po_break_down wb, wo_po_details_master wm 
		WHERE wb.job_id = wm.id and wm.buyer_name like '$buyer_id' $company $search_text $txt_sc_lc_cond $user_buyer $user_brand $year_field_cond $actual_poBreakDownId_cond $lc_sc_cond and wb.is_deleted = 0 AND wb.status_active = 1 and wm.is_deleted = 0 AND wm.status_active = 1 $date  
		group by wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date, wb.job_no_mst, wb.file_no, wm.style_ref_no, wm.gmts_item_id, wb.unit_price, wb.sc_lc, wm.brand_id, wm.insert_date";
		//echo $sql."<br>"; die;
		 //and wb.is_confirmed=1
		?>
        <div>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1150" class="rpt_table" >
                <thead>
                    <th width="30">SL</th>
                    <th width="100">PO No</th>
                    <th width="100">Act PO No</th>
                    <th width="90">Brand</th>
                    <th width="110">Item</th>
                    <th width="100">Style No</th>
                    <th width="60">PO Quantity</th>
                    <th width="70">Rate</th>
                    <th width="90">Price</th>
                    <th width="70">Shipment Date</th>
                    <th width="100">Attached With</th>
                    <th width="50">LC/SC</th>
                    <th width="70" >File No</th>
                    <th>SC/LC no.</th>
                    
                </thead>
            </table>
            <div style="width:1150px; overflow-y:scroll; max-height:250px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1130" class="rpt_table" id="tbl_list_search" >
                <?
					$i=1;$po_qty='';$po_total_price='';
                    $nameArray=sql_select( $sql );
					//var_dump($nameArray);die;
                    foreach ($nameArray as $selectResult)
                    {
						if(array_key_exists($selectResult[csf('id')],$attach_qnty_array))
						{
							$order_attached_qnty=$attach_qnty_array[$selectResult[csf('id')]];

							if($order_attached_qnty>=$selectResult[csf('po_quantity')])
							{
								$all_lc_id=explode(",",$lc_array[$selectResult[csf('id')]]);
								foreach($all_lc_id as $lc_id)
								{
									if($lc_id!=0)
									{
										if ($i%2==0)
											$bgcolor="#E9F3FF";
										else
											$bgcolor="#FFFFFF";
									?>
										<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td width="30"><? echo $i; ?></td>
											<td width="100" style="word-break:break-all;"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
                                            <td width="100" style="word-break:break-all;"><p><? echo $actual_po_arr[$selectResult[csf('id')]]; ?>&nbsp;</p></td>
                                            <td width="90" style="word-break:break-all;"><p><? echo $brand_arr[$selectResult[csf('brand_id')]]; ?>&nbsp;</p></td>
											<td width="110" style="word-break:break-all;">
												<p>
													<?
														$gmts_item='';
														$gmts_item_id=explode(",",$selectResult[csf('gmts_item_id')]);
														foreach($gmts_item_id as $item_id)
														{
															if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
														}
														echo $gmts_item;
													?>
												</p>
											</td>
											<td width="100" style="word-break:break-all;"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
											<td width="60" align="right"><? echo $selectResult[csf('po_quantity')]; ?></td>
											<td align="right" width="70"><? echo number_format(($selectResult[csf('po_total_price')]/$selectResult[csf('po_quantity')]),2); ?></td>
											<td width="90" align="right"><? echo number_format($selectResult[csf('po_total_price')],2); ?></td>
											<td align="center" width="70"><? echo change_date_format($selectResult[csf('shipment_date')]); ?></td>
											<td width="100" style="word-break:break-all;"><p><? echo $lc_details[$lc_id]; ?></p></td>
											<td align="center" width="50"><? echo 'LC'; ?></td>
                                            <td width="70" style="word-break:break-all;"><p><? echo $lc_sc_file_arr[$selectResult[csf('id')]];//$selectResult[csf('file_no')]; ?>&nbsp;</p></td>
                                            <td><p><? echo $selectResult[csf('sc_lc')]; ?>&nbsp;</p></td>
                                            
										</tr>
									<?
									$i++;
									$po_qty+=$selectResult[csf('po_quantity')];
									$po_total_price+=$selectResult[csf('po_total_price')];
									}
								}

								$all_sc_id=explode(",",$sc_array[$selectResult[csf('id')]]);

								foreach($all_sc_id as $sc_id)
								{
									if($sc_id!=0)
									{
										if ($i%2==0)
											$bgcolor="#E9F3FF";
										else
											$bgcolor="#FFFFFF";
									?>
										<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td width="30"><? echo $i; ?></td>
											<td width="100" style="word-break:break-all;"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
                                            <td width="100" style="word-break:break-all;"><p><? echo $actual_po_arr[$selectResult[csf('id')]]; ?>&nbsp;</p></td>
                                            <td width="90" style="word-break:break-all;"><p><? echo $brand_arr[$selectResult[csf('brand_id')]]; ?>&nbsp;</p></td>
											<td width="110" style="word-break:break-all;">
												<p>
													<?
														$gmts_item='';
														$gmts_item_id=explode(",",$selectResult[csf('gmts_item_id')]);
														foreach($gmts_item_id as $item_id)
														{
															if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
														}
														echo $gmts_item;
													?>
												</p>
											</td>
											<td width="100" style="word-break:break-all;"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
											<td width="60" align="right"><? echo $selectResult[csf('po_quantity')]; ?></td>
                                            <td align="right" width="70"><? echo number_format(($selectResult[csf('po_total_price')]/$selectResult[csf('po_quantity')]),2); ?></td>
											<td width="90" align="right"><? echo number_format($selectResult[csf('po_total_price')],2); ?></td>
											<td align="center" width="70"><? echo change_date_format($selectResult[csf('shipment_date')]); ?></td>
											<td width="100" style="word-break:break-all;"><p><? echo $sc_details[$sc_id]; ?></p></td>
											<td align="center" width="50"><? echo 'SC'; ?></td>
                                            <td width="70" style="word-break:break-all;"><p><? echo $lc_sc_file_arr[$selectResult[csf('id')]];//$selectResult[csf('file_no')]; ?>&nbsp;</p></td>
                                            <td><p><? echo $selectResult[csf('sc_lc')]; ?>&nbsp;</p></td>
                                            
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
										   <tr>
											    <td align="right" colspan="6"><b>grand total</b></td>
												<td align="right" width="60" style="word-break:break-all;"><p><? echo number_format($po_qty,2);?>&nbsp;</p></td>
										    	<td></td>
												<td align="right" width="90"><p><? echo number_format($po_total_price,2); ?>&nbsp;</p></td>
												<td colspan="5"></td>
										    	
											</tr>
				</table>
        	</div>
		</div>
		<?
		exit();
	}

	if($action_types=='order_select_popup')
	{
		$lc_sc_cond="";
		if($check_related_order && $lc_sc_no!="") $lc_sc_cond=" and wb.sc_lc='$lc_sc_no'";
		if($cbo_export_item_category==10)
		{
			$sql = "SELECT wm.id, wm.job_no as po_number, sum(wb.amount) as po_total_price, sum(wb.finish_qty) as po_quantity, wm.delivery_date as shipment_date, wb.job_no_mst, 0 as file_no, wm.job_no_prefix_num, $select_field wm.style_ref_no, 0 as gmts_item_id, avg(wb.avg_rate) as unit_price, 0 as sc_lc, $year_field 
			FROM fabric_sales_order_mst wm, fabric_sales_order_dtls wb  
			WHERE wm.id = wb.mst_id and wm.buyer_id like '$buyer_id' and wb.is_deleted = 0 AND wb.status_active = 1 and wm.is_deleted = 0 AND wm.status_active = 1 and wm.within_group=2 $selected_order_id $company $search_text $file_no_cond $txt_sc_lc_cond $date  $user_buyer $year_field_cond 
			group by  wm.id, wm.job_no, wm.delivery_date, wb.job_no_mst, wm.job_no_prefix_num, wm.insert_date, wm.style_ref_no";
		}
		else if($cbo_lc_for==2)
		{
			$file_no_cond="";
			if($txt_file_no!="") $file_no_cond=" and style_ref_no='$txt_file_no'";
			$txt_sc_lc_cond=""; 
			if($txt_sc_lc!="") $txt_sc_lc_cond=" and requisition_number like '%$txt_sc_lc'";
			$sql = "SELECT wm.id, wm.requisition_number as po_number, sum(wb.sample_prod_qty*wb.SAMPLE_CHARGE) as po_total_price, sum(wb.sample_prod_qty) as po_quantity, wm.estimated_shipdate as shipment_date, wm.requisition_number as job_no_mst, 0 as file_no, wm.requisition_number_prefix_num as job_no_prefix_num, $select_field wm.style_ref_no, max(wb.GMTS_ITEM_ID) as gmts_item_id, sum(wb.sample_prod_qty*wb.SAMPLE_CHARGE)/sum(wb.sample_prod_qty) as unit_price, 0 as sc_lc, $year_field 
			FROM sample_development_mst wm, sample_development_dtls wb  
			WHERE wm.id = wb.sample_mst_id and wm.buyer_name like '$buyer_id' and wb.is_deleted = 0 AND wb.status_active = 1 and wm.is_deleted = 0 AND wm.status_active = 1 and wm.entry_form_id in(117,203) $selected_order_id $company $file_no_cond $txt_sc_lc_cond $date $user_buyer $year_field_cond
			group by  wm.id, wm.style_ref_no, wm.estimated_shipdate, wm.requisition_number, wm.requisition_number_prefix_num, wm.insert_date";
		}
		else
		{
			if($chk_country==1)
			{
				$country_arr=return_library_array( "select id, country_name from lib_country where STATUS_ACTIVE=1",'id','country_name');
				$sql = "SELECT wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst, wb.file_no, wm.job_no_prefix_num, $select_field wm.style_ref_no, wm.gmts_item_id, wb.unit_price, wb.sc_lc ,wb.grouping, wm.brand_id, c.COUNTRY_ID, $year_field 
				FROM wo_po_break_down wb, wo_po_details_master wm, WO_PO_COLOR_SIZE_BREAKDOWN c 
				WHERE wb.job_id = wm.id and wb.id=c.PO_BREAK_DOWN_ID and wm.buyer_name like '$buyer_id' $selected_order_id $company $search_text $file_no_cond $txt_sc_lc_cond $user_buyer $user_brand $year_field_cond and wb.is_deleted = 0 AND wb.status_active = 1 AND c.status_active = 1 and wb.is_confirmed=1 $date $lc_sc_cond $actual_poBreakDownId_cond
				group by wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date, wb.job_no_mst, wb.file_no, wm.job_no_prefix_num, wm.insert_date, wm.style_ref_no, wm.gmts_item_id, wb.unit_price, wb.sc_lc ,wb.grouping, wm.brand_id, c.COUNTRY_ID";
			}
			else
			{
				$sql = "SELECT wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst, wb.file_no, wm.job_no_prefix_num, $select_field wm.style_ref_no, wm.gmts_item_id, wb.unit_price, wb.sc_lc ,wb.grouping, wm.brand_id, $year_field 
				FROM wo_po_break_down wb, wo_po_details_master wm 
				WHERE wb.job_id = wm.id and wm.buyer_name like '$buyer_id' $selected_order_id $company $search_text $file_no_cond $txt_sc_lc_cond $user_buyer $user_brand $year_field_cond and wb.is_deleted = 0 AND wb.status_active = 1 and wb.is_confirmed=1 $date $lc_sc_cond $actual_poBreakDownId_cond
				group by wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date, wb.job_no_mst, wb.file_no, wm.job_no_prefix_num, wm.insert_date, wm.style_ref_no, wm.gmts_item_id, wb.unit_price, wb.sc_lc ,wb.grouping, wm.brand_id";
			}
		}
		//echo $ship_start_date;die;
		//echo $sql."<br>";
		if($cbo_export_item_category!=10 && $cbo_lc_for!=2){ $tbl_width=1300;}
		else{$tbl_width=1170;}
	 	?>
        <div>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?=$tbl_width;?>" class="rpt_table" >
                <thead>
                    <th width="40">SL</th>
					<?
						if($cbo_export_item_category!=10 && $cbo_lc_for!=2)
						{ 
							?> 
								<th width="50">Job No</th> 
							<?
						}
					?>
                    <th width="110"><? if($cbo_lc_for == 2) echo "Requisition No"; else echo "PO No";?></th>
                    <th width="110">Internal Ref</th>
                    <th width="120">Act PO No</th>
                    <th width="90">Brand</th>
                    <th width="120">Item</th>
                    <th width="120">Style No</th>
                    <th width="80">PO Quantity</th>
                    <th width="50">Rate</th>
                    <th width="100">Price</th>
                    <th width="70">Shipment Date</th>
                    <?
					if($cbo_export_item_category!=10 && $cbo_lc_for!=2 && $chk_country!=0)
					{
						?>
                        <th width="100">Country</th>
                        <th width="70">File No</th>
                        <th>SC/LC</th>
                        <?
					}
					else
					{
						?>
                        <th width="70">File No</th>
                        <th >SC/LC</th>
                        <?
					}
					?>
                    
                </thead>
            </table>
            <div style="width:<?=$tbl_width+20;?>px; overflow-y:scroll; max-height:220px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?=$tbl_width;?>" class="rpt_table" id="tbl_list_search" align="left">
                <?
					$i=1;
					if($cbo_export_item_category == 10)
					{
						$is_sales_cond=" and is_sales=1";
						$is_sales=1;
					}
					elseif($cbo_lc_for == 2)
					{
						$is_sales_cond=" and is_sales=3";
						$is_sales=3;
					}
					else 
					{
						$is_sales_cond=" and is_sales=0";
						$is_sales=0;
					}
					$lc_attached_qnty_arr = return_library_array("select wo_po_break_down_id, sum(attached_qnty) as qty from com_export_lc_order_info where status_active = 1 and IS_SERVICE<>1 and is_deleted=0 $is_sales_cond group by wo_po_break_down_id","wo_po_break_down_id","qty");
					$sc_attached_qnty_arr = return_library_array("select wo_po_break_down_id, sum(attached_qnty) as qty from com_sales_contract_order_info where status_active = 1 and is_deleted=0 $is_sales_cond group by wo_po_break_down_id","wo_po_break_down_id","qty");

                    $nameArray=sql_select( $sql );
                    foreach ($nameArray as $selectResult)
                    {
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

						//$lc_attached_qnty=return_field_value("sum(attached_qnty)","com_export_lc_order_info","wo_po_break_down_id='".$selectResult[csf('id')]."' and status_active = 1 and is_deleted=0");
						//$sc_attached_qnty=return_field_value("sum(attached_qnty)","com_sales_contract_order_info","wo_po_break_down_id='".$selectResult[csf('id')]."' and status_active = 1 and is_deleted=0");

						$lc_attached_qnty=$lc_attached_qnty_arr[$selectResult[csf('id')]];
						$sc_attached_qnty=$sc_attached_qnty_arr[$selectResult[csf('id')]];
						$order_attached_qnty=$sc_attached_qnty+$lc_attached_qnty;

            			if($order_attached_qnty < $selectResult[csf('po_quantity')] )
						{
                    	?>
                            <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
                                <td width="40" align="center"><?php echo "$i"; ?>
                                 <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>"/>
                                 <input type="hidden" name="txt_is_sales" id="txt_is_sales<?php echo $i ?>" value="<? echo $is_sales; ?>"/>
                                </td>
								<?
									if($cbo_export_item_category!=10 && $cbo_lc_for!=2)
									{ 
										?> 
											<td width="50" style="word-break:break-all;"><p><? echo $selectResult[csf('job_no_prefix_num')]; ?></p></td> 
										<?
									}
								?>
                                <td width="110" style="word-break:break-all;"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
                                <td width="110" style="word-break:break-all;"><p><? echo $selectResult[csf('grouping')]; ?></p></td>
                                <td width="120" style="word-break:break-all;"><p><? echo $actual_po_arr[$selectResult[csf('id')]]; ?>&nbsp;</p></td>
                                <td width="90" style="word-break:break-all;"><p><? echo $brand_arr[$selectResult[csf('brand_id')]]; ?>&nbsp;</p></td>
                                <td width="120" style="word-break:break-all;">
                                    <p>
                                        <?
                                            $gmts_item='';
                                            $gmts_item_id=explode(",",$selectResult[csf('gmts_item_id')]);
                                            foreach($gmts_item_id as $item_id)
                                            {
                                                if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
                                            }
                                            echo $gmts_item;
                                        ?>
                                    </p>
                                </td>
                                <td width="120" style="word-break:break-all;"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
                                <td width="80" align="right"><? echo $selectResult[csf('po_quantity')]; ?></td>
                                <td width="50" align="right"><? echo number_format(($selectResult[csf('po_total_price')]/$selectResult[csf('po_quantity')]),2); ?></td>
                                <td width="100" align="right"><? echo number_format($selectResult[csf('po_total_price')],2); ?></td>
                                <td align="center" width="70"><? echo change_date_format($selectResult[csf('shipment_date')]); ?></td>
                                
                                <?
								if($cbo_export_item_category!=10 && $cbo_lc_for!=2 && $chk_country!=0)
								{
									?>
                                    <td width="100"><p><? echo $country_arr[$selectResult[csf('COUNTRY_ID')]]; ?>&nbsp;</p></td>
                                    <td width="70" style="word-break:break-all;"><p><? echo $selectResult[csf('file_no')]; ?>&nbsp;</p></td>
                                    <td><p><? echo $selectResult[csf('sc_lc')]; ?>&nbsp;</p></td>
                                    <?
								}
								else
								{
									?>
                                    <td width="70" style="word-break:break-all;"><p><? echo $selectResult[csf('file_no')]; ?>&nbsp;</p></td>
                                    <td><p><? echo $selectResult[csf('sc_lc')]; ?>&nbsp;</p></td>
                                    <?
								}
								?>
                                
                            </tr>
                   		<?
                    	$i++;
						}
                    }
                    ?>
                </table>
            </div>
            <table width="<?=$tbl_width;?>" cellspacing="0" cellpadding="0" style="border:none" align="center">
                <tr>
                    <td align="center" height="30" valign="bottom">
                        <div style="width:100%">
                            <div style="width:50%; float:left" align="left">
                                <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                            </div>
                            <div style="width:50%; float:left" align="left">
                                <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
		<?
    }
	exit();
}

if($action=="show_po_active_listview")
{

	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand brand",'id','brand_name');
	
	$is_sales=return_field_value("is_sales","com_sales_contract_order_info","com_sales_contract_id=$data and status_active=1","is_sales");
	//echo $is_sales.test;die;
	if($is_sales==0)
	{
		if ($db_type == 0) {
			$actual_po_arr = return_library_array("select po_break_down_id, group_concat(acc_po_no) as acc_po_no from wo_po_acc_po_info where status_active=1 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "acc_po_no");
		} else {
			$actual_po_arr = return_library_array("select po_break_down_id, listagg( cast(acc_po_no as varchar(4000)), ',') within group(order by acc_po_no) as acc_po_no from wo_po_acc_po_info where status_active=1 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "acc_po_no");
		}
	
		$sql = "select wb.id, ci.id as idd, wm.gmts_item_id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst, wm.style_ref_no, wm.order_uom, wm.total_set_qnty as ratio, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active, wm.brand_id 
		from wo_po_break_down wb, wo_po_details_master wm, com_sales_contract_order_info ci 
		where wb.job_id = wm.id and wb.id=ci.wo_po_break_down_id and ci.com_sales_contract_id='$data' and ci.status_active = '1' and ci.is_deleted = '0' and ci.is_sales=0
		order by ci.id";
	}
	elseif($is_sales==3)
	{
		$sql = "select wm.id, ci.id as idd, max(wb.gmts_item_id) as gmts_item_id, wm.requisition_number as po_number, sum(wb.sample_prod_qty*wb.sample_charge) as po_total_price, sum(wb.sample_prod_qty) as po_quantity, wm.estimated_shipdate as shipment_date, wm.style_ref_no, 1 as order_uom, 1 as ratio, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active 
		from sample_development_dtls wb, sample_development_mst wm, com_sales_contract_order_info ci 
		where wm.id = wb.sample_mst_id and wb.sample_mst_id=ci.wo_po_break_down_id and ci.com_sales_contract_id='$data' and ci.status_active = '1' and ci.is_deleted = '0' and ci.is_sales=3
		group by  wm.id, ci.id, wm.requisition_number, wm.estimated_shipdate, wm.style_ref_no, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active 
		order by ci.id";
	}
	else
	{
		$sql = "select wm.id, ci.id as idd, 0 as gmts_item_id, wm.job_no as po_number, sum(wb.amount) as po_total_price, sum(wb.finish_qty) as po_quantity, wm.delivery_date as shipment_date, wb.job_no_mst, wm.style_ref_no, wb.order_uom, 1 as ratio, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active 
		from fabric_sales_order_dtls wb, fabric_sales_order_mst wm, com_sales_contract_order_info ci 
		where wm.id = wb.mst_id and wb.mst_id=ci.wo_po_break_down_id and ci.com_sales_contract_id='$data' and ci.status_active = '1' and ci.is_deleted = '0' and ci.is_sales=1
		group by  wm.id, ci.id, wm.job_no, wm.delivery_date, wb.job_no_mst, wm.style_ref_no, wb.order_uom, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active 
		order by ci.id";
	}


	//echo $sql; //die;
	/*$arr=array(9=>$attach_detach_array);
	echo create_list_view("list_view", "Order Number,Order Qty,Order Value,Attached Qty,Rate,Attached Value,Style Ref,Item,Job No,Status", "100,100,100,100,60,100,150,150,100,80","1050","200",0, $sql, "get_php_form_data", "idd", "'populate_order_details_form_data'", 0, "0,0,0,0,0,0,0,0,0,status_active", $arr, "po_number,po_quantity,po_total_price,attached_qnty,attached_rate,attached_value,style_ref_no,style_description,job_no_prefix_num,status_active", "requires/sales_contract_controller",'','0,1,1,1,2,2,0,0,0,0','1,po_quantity,po_total_price,attached_qnty,0,attached_value,0,0,0,0');*/
	$display_field='';
	if($is_sales==3) $display_field=' style="display:none;"';

	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1300" class="rpt_table" >
            <thead>
                <th width="100"><? if($is_sales!=3) echo "Order Number"; else echo "Requisition Number";?></th>
                <?
				if($is_sales!=3)
				{
					?>
                    <th width="100">Acc.PO No.</th>
					<?
				}
				?>
                
                <th width="80"><? if($is_sales!=3) echo "Order Qty"; else echo "Requisition Qty";?></th>
                <th width="100"><? if($is_sales!=3) echo "Order Value"; else echo "Requisition Value";?></th>
                <th width="80">Attached Qty</th>
                <th width="50">UOM</th>
                <th width="60">Rate</th>
                <th width="100">Attached Value</th>
                <th width="100">Attached Qty (Pcs)</th>
                <th width="120">Style Ref</th>
                <th width="130">Gmts. Item</th>
                <?
				if($is_sales!=3)
				{
					?>
                    <th width="80">Job No</th>
                	<th width="65">Brand</th>
					<?
				}
				?>
                
                <th width="60">Status</th>
                <th><input type="checkbox" id="chkOrd_th" name="chkOrd_th" onClick="fn_all_chk();"/></th>
            </thead>
        </table>
        <div style="width:1300px; overflow-y:scroll; max-height:200px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1282" class="rpt_table" id="tbl_list_search" >
            <?
                $i=1;
                $nameArray=sql_select( $sql );
				$total_order_qnty_in_pcs=0;
				$total_attc_value=0;
				$total_order_qty = 0;
				$total_order_value=0;
                foreach ($nameArray as $selectResult)
                {
                    if($i%2==0) $bgcolor="#E9F3FF";
                    else $bgcolor="#FFFFFF";

					$order_qnty_in_pcs=$selectResult[csf('attached_qnty')]*$selectResult[csf('ratio')];
					$total_order_qnty_in_pcs+=$order_qnty_in_pcs;
					$total_attc_value+=$selectResult[csf('attached_value')];
					$total_order_qty+=$selectResult[csf('po_quantity')];
					$total_order_value+=$selectResult[csf('po_total_price')];

                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="get_php_form_data('<? echo $selectResult[csf('idd')]."_".$is_sales; ?>','populate_order_details_form_data','requires/sales_contract_controller')">
                        <td width="100"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
                        <?
						if($is_sales!=3)
						{
							?>
							<td width="100"><p><? echo $actual_po_arr[$selectResult[csf('id')]]; ?></p></td>
							<?
						}
						?>
                        <td width="80" align="right"><? echo $selectResult[csf('po_quantity')]; ?></td>
                        <td width="100" align="right"><? echo number_format($selectResult[csf('po_total_price')],2); ?></td>
                        <td width="80" align="right"><? echo $selectResult[csf('attached_qnty')]; ?></td>
                         <td width="50" align="center"><? echo $unit_of_measurement[$selectResult[csf('order_uom')]]; ?></td>
                        <td width="60" align="right"><? echo number_format($selectResult[csf('attached_rate')],2); ?></td>
                        <td width="100" align="right"><? echo number_format($selectResult[csf('attached_value')],2); ?></td>
                        <td width="100" align="right"><? echo $order_qnty_in_pcs; ?></td>
                        <td width="120"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
                        <td width="130">
                            <p>
                                <?
                                    $gmts_item='';
                                    $gmts_item_id=explode(",",$selectResult[csf('gmts_item_id')]);
                                    foreach($gmts_item_id as $item_id)
                                    {
                                        if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
                                    }
                                    echo $gmts_item;
                                ?>
                            </p>
                        </td>
                        <?
						if($is_sales!=3)
						{
							?>
							<td width="80"><? echo $selectResult[csf('job_no_mst')]; ?></td>
                        	<td width="65"><? echo $brand_arr[$selectResult[csf('brand_id')]]; ?></td>
							<?
						}
						?>
                        
                        <td width="60"><? echo $attach_detach_array[$selectResult[csf('status_active')]]; ?></td>
                        <td align="center"><input type="checkbox" id="chkOrd_<?=$i;?>" name="chkOrd[]" value="<? echo $selectResult[csf('idd')]; ?>" /></td>
                    </tr>
                	<?
                	$i++;
                }
                ?>
            </table>
        </div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1300" class="rpt_table">
        	<tfoot>
            	<th width="100">&nbsp;</th>
                <?
				if($is_sales!=3)
				{
					?>
					<th width="100">Total</th>
					<?
				}
				?>
                
                <th width="80" align="right"><? echo number_format($total_order_qty,0); ?></th>
                <th width="100" align="right"><? echo number_format($total_order_value,2); ?></th>
                <th width="80">&nbsp;</th>
                <th width="50">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th width="100" align="right" id="totalAttachedqnty"><? echo number_format($total_attc_value,2); ?></th>
                <th width="100" align="right" id="totalOrderqnty"><? echo number_format($total_order_qnty_in_pcs,0); ?></th>
                <th width="120">&nbsp;</th>
                <th width="130">&nbsp;</th>
                <?
				if($is_sales!=3)
				{
					?>
					<th width="80">&nbsp;</th>
                	<th width="65">&nbsp;</th>
					<?
				}
				?>
                <th colspan="2"><input type="button" style="width:100px;" class="formbutton" value="Submit List" onClick="fn_submit_order_list(<?= $is_sales;?>)" /></th>
            </tfoot>
        </table>
	</div>
    <?
	exit();
}

if($action=="populate_order_details_form_data")
{
	$data_ref=explode("_",$data);
	$lc_attch_id=$data_ref[0];
	$is_sales=$data_ref[1];
	if($is_sales==0)
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
		
		
		if($db_type==0)
		{
			$actual_po_arr=return_library_array("select po_break_down_id, group_concat(acc_po_no) as acc_po_no from wo_po_acc_po_info where status_active=1 and is_deleted=0 group by po_break_down_id","po_break_down_id","acc_po_no");
		}
		else
		{
			$actual_po_arr=return_library_array("select po_break_down_id, listagg( cast(acc_po_no as varchar(4000)), ',') within group(order by acc_po_no) as acc_po_no from wo_po_acc_po_info where status_active=1 and is_deleted=0 group by po_break_down_id","po_break_down_id","acc_po_no");
		}
	
		$data_array=sql_select("select wb.id, ci.id as idd, wm.style_description, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date,wb.job_no_mst, wm.style_ref_no, wm.gmts_item_id, wb.unit_price,ci.attached_qnty,ci.attached_rate, ci.attached_value, ci.status_active, ci.com_sales_contract_id, ci.fabric_description, ci.category_no, ci.hs_code, wm.brand_id, ci.commission, ci.foregin_commission 
		from wo_po_break_down wb, wo_po_details_master wm, com_sales_contract_order_info ci 
		where wb.job_id = wm.id and wb.id=ci.wo_po_break_down_id and ci.id in($lc_attch_id) and ci.status_active = '1' and ci.is_deleted = '0' and wb.is_deleted = 0 and wb.status_active = 1 and wm.is_deleted = 0 and wm.status_active = 1 order by ci.id");
	}
	else if($is_sales==3)
	{
		$data_array = sql_select("select wm.id, ci.id as idd, 0 as style_description, wm.requisition_number as po_number, 0 as po_total_price, sum(wb.sample_prod_qty) as po_quantity, wm.estimated_shipdate as shipment_date, wm.style_ref_no, 0 as gmts_item_id, 0 as unit_price, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.com_sales_contract_id, ci.fabric_description, ci.category_no, ci.hs_code, ci.status_active, ci.commission, ci.foregin_commission 
		from sample_development_dtls wb, sample_development_mst wm, com_sales_contract_order_info ci 
		where wm.id = wb.sample_mst_id and wb.sample_mst_id=ci.wo_po_break_down_id and ci.id in($lc_attch_id) and ci.status_active = '1' and ci.is_deleted = '0'
		group by  wm.id, ci.id, wm.requisition_number, wm.estimated_shipdate, wm.style_ref_no, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.com_sales_contract_id, ci.fabric_description, ci.category_no, ci.hs_code, ci.status_active, ci.commission , ci.foregin_commission
		order by ci.id");
	}
	else
	{
		$data_array = sql_select("select wm.id, ci.id as idd, 0 as style_description, wm.job_no as po_number, sum(wb.amount) as po_total_price, sum(wb.finish_qty) as po_quantity, wm.delivery_date as shipment_date, wb.job_no_mst, wm.style_ref_no, 0 as gmts_item_id, avg(wb.avg_rate) as unit_price, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.com_sales_contract_id, ci.fabric_description, ci.category_no, ci.hs_code, ci.status_active, ci.commission, ci.foregin_commission
		from fabric_sales_order_dtls wb, fabric_sales_order_mst wm, com_sales_contract_order_info ci 
		where wm.id = wb.mst_id and wb.mst_id=ci.wo_po_break_down_id and ci.id in($lc_attch_id) and ci.status_active = '1' and ci.is_deleted = '0'
		group by  wm.id, ci.id, wm.job_no, wm.delivery_date, wb.job_no_mst, wm.style_ref_no, wb.order_uom, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.com_sales_contract_id, ci.fabric_description, ci.category_no, ci.hs_code, ci.status_active, ci.commission ,ci.foregin_commission order by ci.id");
	}
	
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand brand",'id','brand_name');
	foreach ($data_array as $row)
	{
		echo "$('#tbl_order_list tbody tr:not(:first)').remove();\n";

		$gmts_item='';
		$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
		foreach($gmts_item_id as $item_id)
		{
			if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
		}

		echo "document.getElementById('txtordernumber_1').value 			= '".$row[csf("po_number")]."';\n";
		echo "document.getElementById('hiddenwopobreakdownid_1').value 		= '" . $row[csf("id")] . "';\n";
		echo "document.getElementById('isSales_1').value 					= '" . $is_sales . "';\n";
		echo "document.getElementById('txtaccordernumber_1').value 			= '".$actual_po_arr[$row[csf("id")]]."';\n";
		echo "document.getElementById('txtorderqnty_1').value 				= '".$row[csf("po_quantity")]."';\n";
		echo "document.getElementById('txtordervalue_1').value 				= '".$row[csf("po_total_price")]."';\n";
		echo "document.getElementById('txtattachedqnty_1').value 			= '".$row[csf("attached_qnty")]."';\n";
		echo "document.getElementById('hideattachedqnty_1').value 			= '".$row[csf("attached_qnty")]."';\n";
		echo "document.getElementById('hiddenunitprice_1').value 			= '".$row[csf("attached_rate")]."';\n";
		echo "document.getElementById('txtattachedvalue_1').value 			= '".$row[csf("attached_value")]."';\n";
		echo "document.getElementById('txtcommission_1').value 			    = '".$comishion_amount_array[$row[csf("job_no_mst")]]["local_amount"]."';\n";
		echo "document.getElementById('txtcommissionforeign_1').value 	    = '".$comishion_amount_array[$row[csf("job_no_mst")]]["Foreign_ammount"]."';\n";
		echo "document.getElementById('txtstyleref_1').value 				= '".$row[csf("style_ref_no")]."';\n";
		echo "document.getElementById('txtStyleDesc_1').value 				= '".$row[csf("style_description")]."';\n";
		echo "document.getElementById('txtitemname_1').value 				= '".$gmts_item."';\n";
		echo "document.getElementById('txtjobno_1').value 				= '".$row[csf("job_no_mst")]."';\n";
		echo "document.getElementById('cbopostatus_1').value 				= '".$row[csf("status_active")]."';\n";
		echo "document.getElementById('txtfabdescrip_1').value 				= '".$row[csf("fabric_description")]."';\n";
		echo "document.getElementById('txtcategory_1').value 				= '".$row[csf("category_no")]."';\n";
		echo "document.getElementById('txthscode_1').value 				= '".$row[csf("hs_code")]."';\n";
		echo "document.getElementById('txtbrand_1').value 				= '".$brand_arr[$row[csf("brand_id")]]."';\n";
		echo "document.getElementById('hiddenwopobreakdownid_1').value 		= '".$row[csf("id")]."';\n";
		echo "document.getElementById('hiddensalescontractorderid_1').value 	= '".$row[csf("idd")]."';\n";
		echo "document.getElementById('txt_tot_row').value 	= '1';\n";

		echo "math_operation( 'totalOrderqnty', 'txtorderqnty_', '+', 1 );\n";
		echo "math_operation( 'totalOrdervalue', 'txtordervalue_', '+', 1 );\n";
		echo "math_operation( 'totalAttachedqnty', 'txtattachedqnty_', '+', 1 );\n";
		echo "math_operation( 'totalAttachedvalue', 'txtattachedvalue_', '+', 1 );\n";

		$order_attahed_qnty_sc=0; $order_attahed_qnty_lc=0; $order_attahed_val_sc=0; $order_attahed_val_lc=0; $sc_no=''; $lc_no='';
		$sql_sc ="SELECT a.contract_no, sum(b.attached_qnty) as at_qt,sum(b.attached_value) as at_val FROM com_sales_contract a, com_sales_contract_order_info b WHERE a.id=b.com_sales_contract_id and b.wo_po_break_down_id='".$row[csf("id")]."' and b.id!='".$data."' and b.status_active = 1 and b.is_deleted=0 group by a.id, a.contract_no";
		$result_array_sc=sql_select($sql_sc);
		foreach($result_array_sc as $scArray)
		{
			if ($sc_no=="") $sc_no = $scArray[csf('contract_no')]; else $sc_no.=",".$scArray[csf('contract_no')];
			$order_attahed_qnty_sc+=$scArray[csf('at_qt')];
			//$order_attahed_val_sc+=$scArray[csf('at_val')];
		}

		$sql_lc="SELECT a.export_lc_no, sum(b.attached_qnty) as at_qt,sum(b.attached_value) as at_val FROM com_export_lc a, com_export_lc_order_info b WHERE a.id=b.com_export_lc_id and b.wo_po_break_down_id='".$row[csf("id")]."' and b.IS_SERVICE<>1 and b.status_active = 1 and b.is_deleted=0 group by a.id, a.export_lc_no";
		$result_array_sc=sql_select($sql_lc);
		foreach($result_array_sc as $lcArray)
		{
			if ($lc_no=="") $lc_no = $lcArray[csf('export_lc_no')]; else $lc_no.=",".$lcArray[csf('export_lc_no')];
			$order_attahed_qnty_lc+=$lcArray[csf('at_qt')];
			//$order_attahed_val_lc+=$lcArray[csf('at_val')];
		}

		$order_attached_qnty=$order_attahed_qnty_sc+$order_attahed_qnty_lc;
		//$order_attached_val=$order_attahed_val_sc+$order_attahed_val_lc;

		echo "document.getElementById('order_attached_qnty_1').value 		= '".$order_attached_qnty."';\n";
		echo "document.getElementById('order_attached_lc_no_1').value 		= '".$lc_no."';\n";
		echo "document.getElementById('order_attached_lc_qty_1').value 	= '".$order_attahed_qnty_lc."';\n";
		echo "document.getElementById('order_attached_sc_no_1').value 		= '".$sc_no."';\n";
		echo "document.getElementById('order_attached_sc_qty_1').value 	= '".$order_attahed_qnty_sc."';\n";

		/*if($db_type==0)
		{
			$attached_po_id=return_field_value("group_concat(wo_po_break_down_id)","com_sales_contract_order_info","com_sales_contract_id='".$row[csf("com_sales_contract_id")]."' and status_active=1 and is_deleted=0");
		}
		else
		{
			$attached_po_id=return_field_value("LISTAGG(wo_po_break_down_id, ',') WITHIN GROUP (ORDER BY wo_po_break_down_id) as po_id","com_sales_contract_order_info","com_sales_contract_id='".$row[csf("com_sales_contract_id")]."' and status_active=1 and is_deleted=0","po_id");
		}*/
		//echo "document.getElementById('hidden_selectedID').value 		= '".$attached_po_id."';\n";

		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_po_selection_save',2);\n";
		exit();
	}
}

if($action=="order_list_for_attach_update")
{
	$explode_data = explode("**",$data);//0->wo_po_break_down id's, 1->table row
	$lc_attch_id=$explode_data[0];
	$is_sales=$explode_data[1];
	
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand brand",'id','brand_name');
	if($is_sales==0)
	{
		if($db_type==0)
		{
			$actual_po_arr=return_library_array("select po_break_down_id, group_concat(acc_po_no) as acc_po_no from wo_po_acc_po_info where status_active=1 and is_deleted=0 and po_break_down_id in($data) group by po_break_down_id","po_break_down_id","acc_po_no");
		}
		else
		{
			$actual_po_arr=return_library_array("select po_break_down_id, listagg( cast(acc_po_no as varchar(4000)), ',') within group(order by acc_po_no) as acc_po_no from wo_po_acc_po_info where status_active=1 and is_deleted=0 and po_break_down_id in($data) group by po_break_down_id","po_break_down_id","acc_po_no");
		}
	}
	
	
	//echo $is_sales;die;
	if($data!="")
	{
		//$data_array=sql_select("select wb.id, ci.id as idd, wm.style_description, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date,wb.job_no_mst, wm.style_ref_no, wm.gmts_item_id, wb.unit_price,ci.attached_qnty,ci.attached_rate, ci.attached_value, ci.status_active, ci.com_sales_contract_id, ci.fabric_description, ci.category_no, ci.hs_code, wm.brand_id 
//		from wo_po_break_down wb, wo_po_details_master wm, com_sales_contract_order_info ci 
//		where wb.job_id = wm.id and wb.id=ci.wo_po_break_down_id and ci.id in($lc_attch_id) and ci.status_active = '1' and ci.is_deleted = '0' and wb.is_deleted = 0 and wb.status_active = 1 and wm.is_deleted = 0 and wm.status_active = 1 order by ci.id");

		
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
		
		if($is_sales==0)
		{
			$data_array = "SELECT wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst, wm.style_ref_no, wm.style_description,wm.gmts_item_id, wb.unit_price, wm.brand_id, ci.id as idd, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active, ci.com_sales_contract_id, ci.fabric_description, ci.category_no, ci.hs_code, ci.commission 
			FROM wo_po_break_down wb, wo_po_details_master wm, com_sales_contract_order_info ci 
			WHERE wb.job_id = wm.id and wb.id=ci.wo_po_break_down_id and ci.id in($lc_attch_id) AND wb.is_deleted = 0 AND wb.status_active = 1 and ci.status_active = '1' and ci.is_deleted = '0'";
		}
		else if($is_sales==1)
		{
			$data_array = "SELECT wm.id, wm.job_no as po_number, sum(wb.amount) as po_total_price, sum(wb.finish_qty) as po_quantity, wm.delivery_date as shipment_date, wb.job_no_mst, wm.style_ref_no, 0 as style_description, 0 as gmts_item_id, avg(wb.avg_rate) as unit_price, ci.id as idd, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active, ci.com_sales_contract_id, ci.fabric_description, ci.category_no, ci.hs_code, ci.commission 
			FROM fabric_sales_order_mst wm, fabric_sales_order_dtls wb, com_sales_contract_order_info ci 
			WHERE wm.id = wb.mst_id and wm.within_group=2 and wb.id=ci.wo_po_break_down_id and ci.id in($lc_attch_id) and wb.is_deleted = 0 AND wb.status_active = 1 and wm.is_deleted = 0 AND wm.status_active = 1 and ci.status_active = '1' and ci.is_deleted = '0'  
			group by wm.id, wm.job_no, wm.delivery_date, wb.job_no_mst, wm.style_ref_no, ci.id, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active, ci.com_sales_contract_id, ci.fabric_description, ci.category_no, ci.hs_code";
		}
		else
		{
			$data_array = "SELECT wm.id, wm.requisition_number as po_number, sum(wb.sample_prod_qty*wb.SAMPLE_CHARGE) as po_total_price, sum(wb.sample_prod_qty) as po_quantity, wm.estimated_shipdate as shipment_date, wm.style_ref_no as job_no_mst, wm.style_ref_no, 0 as style_description, max(wb.GMTS_ITEM_ID) as gmts_item_id, sum(wb.sample_prod_qty*wb.SAMPLE_CHARGE)/sum(wb.sample_prod_qty) as unit_price, ci.id as idd, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active, ci.com_sales_contract_id, ci.fabric_description, ci.category_no, ci.hs_code, ci.commission 

			FROM sample_development_mst wm, sample_development_dtls wb, com_sales_contract_order_info ci  
			WHERE wm.id = wb.sample_mst_id and wb.id=ci.wo_po_break_down_id and ci.id in($lc_attch_id) and wb.is_deleted = 0 AND wb.status_active = 1 and wm.is_deleted = 0 AND wm.status_active = 1 and wm.entry_form_id in(117,203) and ci.status_active = '1' and ci.is_deleted = '0'
			group by wm.id, wm.style_ref_no, wm.estimated_shipdate, wm.requisition_number, ci.id, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active, ci.com_sales_contract_id, ci.fabric_description, ci.category_no, ci.hs_code";
		}
		//echo $data_array;
		$variable_rate_edit = sql_select("SELECT cost_heads_status FROM variable_settings_commercial where company_name =$company_id and variable_list=33 and is_deleted = 0 AND status_active = 1");
		if($variable_rate_edit[0][csf("cost_heads_status")]==1) $rate_edit=""; else $rate_edit=" readonly disabled ";
		$data_array=sql_select($data_array);
		$styde_display="";
		if($is_sales==3) $styde_display=' style="display:none"';
		$table_row=0;
 		foreach ($data_array as $row)
		{
			$table_row++;
			
			$gmts_item = '';
			$gmts_item_id = explode(",", $row[csf('gmts_item_id')]);
			foreach ($gmts_item_id as $item_id) {
				if ($gmts_item == ""){$gmts_item = $garments_item[$item_id];}else{$gmts_item .= ", " . $garments_item[$item_id];}
			}
			?>
			<tr class="general" id="tr_<? echo $table_row; ?>">
				<td>
					<input type="text" name="txtordernumber_<? echo $table_row; ?>" id="txtordernumber_<? echo $table_row; ?>" class="text_boxes" style="width:90px"  value="<? echo $row[csf("po_number")]; ?>" onDblClick= "openmypage('requires/sales_contract_controller.php?action=order_popup&types=order_select_popup&buyer_id='+document.getElementById('cbo_buyer_name').value+'&selectID='+document.getElementById('hidden_selectedID').value+'&sales_contractID='+document.getElementById('txt_system_id').value+'&company_id='+document.getElementById('cbo_beneficiary_name').value+'&lc_sc_no='+document.getElementById('txt_contract_no').value,'PO Selection Form','<? echo $table_row; ?>')" readonly= "readonly" placeholder="Double Click" />
					<input type="hidden" name="hiddenwopobreakdownid_<? echo $table_row; ?>" id="hiddenwopobreakdownid_<? echo $table_row; ?>" readonly= "readonly" value="<? echo $row[csf("id")]; ?>" />
                    <input type="hidden" name="isSales_<? echo $table_row; ?>" id="isSales_<? echo $table_row; ?>" value="<? echo $is_sales; ?>" />
				</td>
                
                <td <?= $styde_display;?> ><input type="text" name="txtaccordernumber_<? echo $table_row; ?>" id="txtaccordernumber_<? echo $table_row; ?>" class="text_boxes" style="width:90px;" readonly= "readonly" value="<? echo $actual_po_arr[$row[csf("id")]]; ?>" /></td>
				<td>
					<input type="text" name="txtorderqnty_<? echo $table_row; ?>" id="txtorderqnty_<? echo $table_row; ?>" class="text_boxes" style="width:60px; text-align:right" readonly= "readonly" value="<? echo $row[csf("po_quantity")]; ?>" />
				</td>
				<td>
					<input type="text" name="txtordervalue_<? echo $table_row; ?>" id="txtordervalue_<? echo $table_row; ?>" class="text_boxes" style="width:80px; text-align:right" readonly= "readonly" value="<? echo number_format($row[csf("po_total_price")],2,'.',''); ?>" />
				</td>
				<td>
					<input type="text" name="txtattachedqnty_<? echo $table_row; ?>" id="txtattachedqnty_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:60px" onKeyUp="validate_attach_qnty(<? echo $table_row; ?>)" value="<? echo $row[csf("attached_qnty")]; ?>" />
					<input type="hidden" name="hideattachedqnty_<? echo $table_row; ?>" id="hideattachedqnty_<? echo $table_row;?>" class="text_boxes_numeric" value="<? echo $row[csf("attached_qnty")]; ?>"/>
				</td>
				<td>
                    <input type="text" name="hiddenunitprice_<? echo $table_row; ?>" id="hiddenunitprice_<? echo $table_row; ?>" value="<? echo $row[csf("attached_rate")]; ?>" style="width:50px" class="text_boxes_numeric" onKeyUp="calculate_attach_val(<? echo $table_row; ?>)" <? echo $rate_edit; ?> />
				</td>
				<td>
					<input type="text" name="txtattachedvalue_<? echo $table_row; ?>" id="txtattachedvalue_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:80px" readonly= "readonly" value="<? echo $row[csf("attached_value")]; ?>" />
				</td>
                <td>
					<input type="text" name="txtcommission_<? echo $table_row; ?>" id="txtcommission_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:80px" readonly= "readonly" value="<? echo $comishion_amount_array[$row[csf("job_no_mst")]]["local_amount"]; ?>" />
				</td>
				<td>
					<input type="text" name="txtcommissionforeign_<? echo $table_row; ?>" id="txtcommissionforeign_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:80px" readonly= "readonly" value="<? echo $comishion_amount_array[$row[csf("job_no_mst")]]["Foreign_ammount"]; ?>" />
				</td>
				</td>
				<td>
					<input type="text" name="txtstyleref_<? echo $table_row; ?>" id="txtstyleref_<? echo $table_row; ?>" class="text_boxes" style="width:90px" readonly= "readonly" value="<? echo $row[csf("style_ref_no")]; ?>" />
				</td>
				<td <?= $styde_display;?> >
					<input type="text" name="txtStyleDesc_<? echo $table_row; ?>" id="txtStyleDesc_<? echo $table_row; ?>" class="text_boxes" style="width:90px" readonly= "readonly" value="<? echo $row[csf("style_description")]; ?>" />
				</td>
				<td>
					<input type="text" name="txtitemname_<? echo $table_row; ?>" id="txtitemname_<? echo $table_row; ?>" class="text_boxes" style="width:80px" readonly= "readonly" value="<? echo $gmts_item; ?>" />
				</td>
				<td <?= $styde_display;?> >
					<input type="text" name="txtjobno_<? echo $table_row; ?>" id="txtjobno_<? echo $table_row; ?>" class="text_boxes" style="width:80px" readonly= "readonly" value="<? echo $row[csf("job_no_mst")]; ?>"  />
				</td>
                <td <?= $styde_display;?> ><input type="text" name="txtfabdescrip_<? echo $table_row; ?>" id="txtfabdescrip_<? echo $table_row; ?>" class="text_boxes" style="width:90px"  value="<? echo $row[csf("fabric_description")]; ?>" /></td>
                <td <?= $styde_display;?> >
					<input type="text" name="txtcategory_<? echo $table_row; ?>" id="txtcategory_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:50px" value="<? echo $row[csf("category_no")]; ?>" />
				</td>
                <td <?= $styde_display;?> >
					<input type="text" name="txthscode_<? echo $table_row; ?>" id="txthscode_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:40px"   value="<? echo $row[csf("hs_code")]; ?>"  />
				</td>
                <td <?= $styde_display;?> >
					<input type="text" name="txtbrand_<? echo $table_row; ?>" id="txtbrand_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:50px" readonly= "readonly" value="<? echo $brand_arr[$row[csf("brand_id")]]; ?>" />
				</td>
				<td>
					<?
						 echo create_drop_down( "cbopostatus_".$table_row, 60, $attach_detach_array,"", 0, "", $row[csf("status_active")], "copy_all(this.value+'_'+".$table_row.")" );
					?>
                    <input type="hidden" name="hiddensalescontractorderid_<?= $table_row;?>" id="hiddensalescontractorderid_<?= $table_row;?>" readonly= "readonly" value="<?= $row[csf("idd")];?>" />
				</td>
			</tr>
		<?

		}//end foreach

	}//end if data condition

}

/*if($action=="populate_attached_po_id")
{
	if($db_type==0)
	{
	   $attached_po_id=return_field_value("group_concat(wo_po_break_down_id)","com_sales_contract_order_info","com_sales_contract_id='$data' and status_active=1 and is_deleted=0");
	}
	else
	{
		$attached_po_id=return_field_value("LISTAGG(wo_po_break_down_id, ',') WITHIN GROUP (ORDER BY wo_po_break_down_id) as po_id","com_sales_contract_order_info","com_sales_contract_id='$data' and status_active=1 and is_deleted=0","po_id");
	}

	echo "document.getElementById('hidden_selectedID').value 		= '".$attached_po_id."';\n";
	exit();
}*/


if($action=="order_list_for_attach")
{
	$explode_data = explode("**",$data);//0->wo_po_break_down id's, 1->table row
	$data=$explode_data[0];
	$table_row=$explode_data[1];
	$is_sales = $explode_data[2];
	$company_id = $explode_data[3];
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand brand",'id','brand_name');
	$hs_code_arr=return_library_array( "select id, hs_code from lib_garment_item",'id','hs_code');
	if($is_sales==0)
	{
		if($db_type==0)
		{
			$actual_po_arr=return_library_array("select po_break_down_id, group_concat(acc_po_no) as acc_po_no from wo_po_acc_po_info where status_active=1 and is_deleted=0 and po_break_down_id in($data) group by po_break_down_id","po_break_down_id","acc_po_no");
		}
		else
		{
			$actual_po_arr=return_library_array("select po_break_down_id, listagg( cast(acc_po_no as varchar(4000)), ',') within group(order by acc_po_no) as acc_po_no from wo_po_acc_po_info where status_active=1 and is_deleted=0 and po_break_down_id in($data) group by po_break_down_id","po_break_down_id","acc_po_no");
		}
	}
	//echo $is_sales;die;

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
	 
	if($data!="")
	{
		if($is_sales==0)
		{
		   $data_array = "SELECT wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst, wm.style_ref_no, wm.style_description,wm.gmts_item_id, wb.unit_price, wm.brand_id
			FROM wo_po_break_down wb, wo_po_details_master wm
			WHERE wb.job_id = wm.id  AND wb.id in ($data) AND wb.is_deleted = 0 AND wb.status_active = 1";
			/*$data_array = "SELECT wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst, wm.style_ref_no, wm.style_description, wb.unit_price FROM wo_po_break_down wb, wo_po_details_master wm WHERE wb.job_id = wm.id AND wb.id in ($data) and wb.is_deleted = 0 AND wb.status_active = 1 and wm.is_deleted = 0 AND wm.status_active = 1";*/
		}
		else if($is_sales==1)
		{
			$data_array = "SELECT wm.id, wm.job_no as po_number, sum(wb.amount) as po_total_price, sum(wb.finish_qty) as po_quantity, wm.delivery_date as shipment_date, wb.job_no_mst, wm.style_ref_no, 0 as style_description, 0 as gmts_item_id, avg(wb.avg_rate) as unit_price 
			FROM fabric_sales_order_mst wm, fabric_sales_order_dtls wb 
			WHERE wm.id = wb.mst_id and wb.is_deleted = 0 AND wb.status_active = 1 and wm.is_deleted = 0 AND wm.status_active = 1 and wm.within_group=2 and wm.id in ($data)
			group by wm.id, wm.job_no, wm.delivery_date, wb.job_no_mst, wm.style_ref_no";
		}
		else
		{
			$data_array = "SELECT wm.id, wm.requisition_number as po_number, sum(wb.sample_prod_qty*wb.SAMPLE_CHARGE) as po_total_price, sum(wb.sample_prod_qty) as po_quantity, wm.estimated_shipdate as shipment_date, wm.style_ref_no as job_no_mst, wm.style_ref_no, 0 as style_description, max(wb.GMTS_ITEM_ID) as gmts_item_id, sum(wb.sample_prod_qty*wb.SAMPLE_CHARGE)/sum(wb.sample_prod_qty) as unit_price 
			FROM sample_development_mst wm, sample_development_dtls wb 
			WHERE wm.id = wb.sample_mst_id and wb.is_deleted = 0 AND wb.status_active = 1 and wm.is_deleted = 0 AND wm.status_active = 1 and wm.entry_form_id in(117,203) and wm.id in ($data)
			group by wm.id, wm.style_ref_no, wm.estimated_shipdate, wm.requisition_number";
		}
		//echo $data_array;
		$variable_rate_edit = sql_select("SELECT cost_heads_status FROM variable_settings_commercial where company_name =$company_id and variable_list=33 and is_deleted = 0 AND status_active = 1");
		if($variable_rate_edit[0][csf("cost_heads_status")]==1) $rate_edit=""; else $rate_edit=" readonly disabled ";
		$data_array=sql_select($data_array);
		$styde_display="";
		if($is_sales==3) $styde_display=' style="display:none"';
 		foreach ($data_array as $row)
		{
			$table_row++;
			$order_attahed_qnty_sc=0; $order_attahed_qnty_lc=0; $order_attahed_val_sc=0; $order_attahed_val_lc=0; $sc_no=''; $lc_no='';
			$sql_sc ="SELECT a.contract_no, sum(b.attached_qnty) as at_qt,sum(b.attached_value) as at_val FROM com_sales_contract a, com_sales_contract_order_info b WHERE a.id=b.com_sales_contract_id and b.wo_po_break_down_id='".$row[csf("id")]."' and b.is_sales=$is_sales and b.status_active = 1 and b.is_deleted=0 group by a.id, a.contract_no";
			$result_array_sc=sql_select($sql_sc);
			foreach($result_array_sc as $scArray)
			{
				if ($sc_no=="") $sc_no = $scArray[csf('contract_no')]; else $sc_no.=",".$scArray[csf('contract_no')];
				$order_attahed_qnty_sc+=$scArray[csf('at_qt')];
				$order_attahed_val_sc+=$scArray[csf('at_val')];
			}

			$sql_lc="SELECT a.export_lc_no, sum(b.attached_qnty) as at_qt,sum(b.attached_value) as at_val FROM com_export_lc a, com_export_lc_order_info b WHERE a.id=b.com_export_lc_id and b.wo_po_break_down_id='".$row[csf("id")]."' and b.is_sales=$is_sales and b.IS_SERVICE<>1 and b.status_active = 1 and b.is_deleted=0 group by a.id, a.export_lc_no";
			$result_array_sc=sql_select($sql_lc);
			foreach($result_array_sc as $lcArray)
			{
				if ($lc_no=="") $lc_no = $lcArray[csf('export_lc_no')]; else $lc_no.=",".$lcArray[csf('export_lc_no')];
				$order_attahed_qnty_lc+=$lcArray[csf('at_qt')];
				$order_attahed_val_lc+=$lcArray[csf('at_val')];
			}

			$order_attached_qnty=$order_attahed_qnty_sc+$order_attahed_qnty_lc;
			$order_attached_val=$order_attahed_val_sc+$order_attahed_val_lc;

			$remaining_qnty = $row[csf("po_quantity")]-$order_attached_qnty;
			$remaining_value = $row[csf("po_total_price")]-$order_attached_val;
			$gmts_item = '';
			$hs_code = '';
			$gmts_item_id = explode(",", $row[csf('gmts_item_id')]);
			foreach ($gmts_item_id as $item_id) {
				if ($gmts_item == ""){$gmts_item = $garments_item[$item_id];}else{$gmts_item .= ", " . $garments_item[$item_id];}
				if ($hs_code == ""){$hs_code = $hs_code_arr[$item_id];}else{$hs_code .= ", " . $hs_code_arr[$item_id];}
			}
			?>
			<tr class="general" id="tr_<? echo $table_row; ?>">
				<td>
					<input type="text" name="txtordernumber_<? echo $table_row; ?>" id="txtordernumber_<? echo $table_row; ?>" class="text_boxes" style="width:90px"  value="<? echo $row[csf("po_number")]; ?>" onDblClick= "openmypage('requires/sales_contract_controller.php?action=order_popup&types=order_select_popup&buyer_id='+document.getElementById('cbo_buyer_name').value+'&selectID='+document.getElementById('hidden_selectedID').value+'&sales_contractID='+document.getElementById('txt_system_id').value+'&company_id='+document.getElementById('cbo_beneficiary_name').value+'&lc_sc_no='+document.getElementById('txt_contract_no').value,'PO Selection Form','<? echo $table_row; ?>')" readonly= "readonly" placeholder="Double Click" />
					<input type="hidden" name="hiddenwopobreakdownid_<? echo $table_row; ?>" id="hiddenwopobreakdownid_<? echo $table_row; ?>" readonly= "readonly" value="<? echo $row[csf("id")]; ?>" />
                    <input type="hidden" name="isSales_<? echo $table_row; ?>" id="isSales_<? echo $table_row; ?>" value="<? echo $is_sales; ?>" />
				</td>
                
                <td <?= $styde_display;?> ><input type="text" name="txtaccordernumber_<? echo $table_row; ?>" id="txtaccordernumber_<? echo $table_row; ?>" class="text_boxes" style="width:90px;" readonly= "readonly" value="<? echo $actual_po_arr[$row[csf("id")]]; ?>" /></td>
				<td>
					<input type="text" name="txtorderqnty_<? echo $table_row; ?>" id="txtorderqnty_<? echo $table_row; ?>" class="text_boxes" style="width:60px; text-align:right" readonly= "readonly" value="<? echo $row[csf("po_quantity")]; ?>" />
				</td>
				<td>
					<input type="text" name="txtordervalue_<? echo $table_row; ?>" id="txtordervalue_<? echo $table_row; ?>" class="text_boxes" style="width:80px; text-align:right" readonly= "readonly" value="<? echo number_format($row[csf("po_total_price")],2,'.',''); ?>" />
				</td>
				<td>
					<input type="text" name="txtattachedqnty_<? echo $table_row; ?>" id="txtattachedqnty_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:60px" onKeyUp="validate_attach_qnty(<? echo $table_row; ?>)" value="<? echo $remaining_qnty; ?>" />
					<input type="hidden" name="hideattachedqnty_<? echo $table_row; ?>" id="hideattachedqnty_<? echo $table_row;?>" class="text_boxes_numeric" value="<? echo $remaining_qnty; ?>"/>
				</td>
				<td>
                    <input type="text" name="hiddenunitprice_<? echo $table_row; ?>" id="hiddenunitprice_<? echo $table_row; ?>" value="<? echo $row[csf("unit_price")]; ?>" style="width:50px" class="text_boxes_numeric" onKeyUp="calculate_attach_val(<? echo $table_row; ?>)" <? echo $rate_edit; ?> />
				</td>
				<td>
					<input type="text" title="dddddd" name="txtattachedvalue_<? echo $table_row; ?>" id="txtattachedvalue_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:80px" readonly= "readonly" value="<? echo number_format($remaining_value,2,'.',''); ?>" />
				</td>
				<td>
					<input type="text" name="txtcommission_<? echo $table_row; ?>" id="txtcommission_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:80px" readonly= "readonly" value="<? echo $comishion_amount_array[$row[csf("job_no_mst")]]["local_amount"]; ?>" />
				</td>
				<td>
					<input type="text" name="txtcommissionforeign_<? echo $table_row; ?>" id="txtcommissionforeign_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:80px" readonly= "readonly" value="<? echo $comishion_amount_array[$row[csf("job_no_mst")]]["Foreign_ammount"]; ?>" />
				</td>
				<td>
					<input type="text" name="txtstyleref_<? echo $table_row; ?>" id="txtstyleref_<? echo $table_row; ?>" class="text_boxes" style="width:90px" readonly= "readonly" value="<? echo $row[csf("style_ref_no")]; ?>" />
				</td>
				<td <?= $styde_display;?> >
					<input type="text" name="txtStyleDesc_<? echo $table_row; ?>" id="txtStyleDesc_<? echo $table_row; ?>" class="text_boxes" style="width:90px" readonly= "readonly" value="<? echo $row[csf("style_description")]; ?>" />
				</td>
				<td>
					<input type="text" name="txtitemname_<? echo $table_row; ?>" id="txtitemname_<? echo $table_row; ?>" class="text_boxes" style="width:80px" readonly= "readonly" value="<? echo $gmts_item; ?>" />
				</td>
				<td <?= $styde_display;?> >
					<input type="text" name="txtjobno_<? echo $table_row; ?>" id="txtjobno_<? echo $table_row; ?>" class="text_boxes" style="width:80px" readonly= "readonly" value="<? echo $row[csf("job_no_mst")]; ?>"  />
				</td>
                <td <?= $styde_display;?> ><input type="text" name="txtfabdescrip_<? echo $table_row; ?>" id="txtfabdescrip_<? echo $table_row; ?>" class="text_boxes" style="width:90px" /></td>
                <td <?= $styde_display;?> >
					<input type="text" name="txtcategory_<? echo $table_row; ?>" id="txtcategory_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:50px"  />
				</td>
                <td <?= $styde_display;?> >
					<input type="text" name="txthscode_<? echo $table_row; ?>" id="txthscode_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:40px"  value="<? echo $hs_code; ?>"  />
				</td>
                <td <?= $styde_display;?> >
					<input type="text" name="txtbrand_<? echo $table_row; ?>" id="txtbrand_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:50px" readonly= "readonly" value="<? echo $brand_arr[$row[csf("brand_id")]]; ?>" />
				</td>
				<td>
					<?
						 echo create_drop_down( "cbopostatus_".$table_row, 60, $attach_detach_array,"", 0, "", 1, "copy_all(this.value+'_'+".$table_row.")" );
					?>
                    <input type="hidden" name="hiddensalescontractorderid_<?= $table_row;?>" id="hiddensalescontractorderid_<?= $table_row;?>" readonly= "readonly" value="" />
                    <input type="hidden" name="order_attached_qnty_<? echo $table_row; ?>" id="order_attached_qnty_<? echo $table_row; ?>" value="<? echo $order_attached_qnty; ?>" readonly= "readonly" />
                    <input type="hidden" name="order_attached_lc_no_<? echo $table_row; ?>" id="order_attached_lc_no_<? echo $table_row; ?>" value="<? echo $lc_no; ?>" readonly= "readonly" />
                    <input type="hidden" name="order_attached_lc_qty_<? echo $table_row; ?>" id="order_attached_lc_qty_<? echo $table_row; ?>" value="<? echo $order_attahed_qnty_lc; ?>" readonly= "readonly" />
                    <input type="hidden" name="order_attached_sc_no_<? echo $table_row; ?>" id="order_attached_sc_no_<? echo $table_row; ?>" value="<? echo $sc_no; ?>" readonly= "readonly" />
                    <input type="hidden" name="order_attached_sc_qty_<? echo $table_row; ?>" id="order_attached_sc_qty_<? echo $table_row; ?>" value="<? echo $order_attahed_qnty_sc; ?>" readonly= "readonly" />
				</td>
               		
			</tr>
		<?

		}//end foreach

	}//end if data condition

}


if($action=="create_sc_search_list_view")
{
	$data=explode('**',$data);
	if($data[0]!=0){ $company_id=" and beneficiary_name = $data[0]";}else{ $company_id=""; }
	//if($data[1]!=0){ $buyer_id=" and buyer_name = $data[1]";}else{ $buyer_id=""; }
	$search_by=$data[2];
	$search_text=$data[3];

	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id="";
		}
		else
		{
			$buyer_id="";
		}
	}
	else
	{
		$buyer_id=" and buyer_name = $data[1]";
	}

	if($search_by==0)
	{
		$search_condition="";
	}
	else if($search_by==1 && $search_text != "")
	{
		$search_condition=" and contract_no like '$search_text'";
	}
	else if($search_by==2)
	{
		$search_condition=" and internal_file_no like '$search_text%'";
	}else if($search_by==3)
	{
		$search_condition=" and bank_file_no like '$search_text%'";
	}


	if($db_type==0) $year_field="YEAR(insert_date) as year,";
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year,";
	else $year_field="";//defined Later
	if($data[5]){$file_year_field=" and sc_year='".$data[5]."' ";}
	if ($data[6] !='' &&  $data[7] !='')
	{
		if($db_type==0)
		{
			$date_cond = "and contract_date between '".change_date_format($data[6], "yyyy-mm-dd", "-")."' and '".change_date_format($data[7], "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond = "and contract_date between '".change_date_format($data[6],'','',1)."' and '".change_date_format($data[7],'','',1)."'";
		}
	}

	$sql = "select id,contract_no, internal_file_no, $year_field contact_prefix_number, contact_system_id, beneficiary_name,buyer_name, applicant_name,contract_value, lien_bank,pay_term, last_shipment_date,contract_date, sc_year as file_year, bank_file_no from com_sales_contract where status_active=1 and is_deleted=0 $company_id $buyer_id $search_condition $file_year_field $date_cond order by id desc";
	// echo $sql;die;
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$bank_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
	$arr=array (6=>$comp,7=>$buyer_arr,8=>$buyer_arr,10=>$bank_arr,11=>$pay_term);
	echo create_list_view("list_view", "Contract No,File No,Bank File No,File Year,Insert Year,System ID,Company,Buyer Name,Applicant Name,Contract Value,Lien Bank,Pay Term,Last Ship Date,Contract Date", "80,80,80,50,50,70,70,70,70,100,110,70,80,70","1150","320",0, $sql, "js_set_value", "id", "", 1, "0,0,0,0,0,0,beneficiary_name,buyer_name,applicant_name,0,lien_bank,pay_term,0,0", $arr , "contract_no,internal_file_no,bank_file_no,file_year,year,contact_prefix_number,beneficiary_name,buyer_name,applicant_name,contract_value,lien_bank,pay_term,last_shipment_date,contract_date", "",'','0,0,0,0,0,0,0,0,0,2,0,0,3,3') ;

}

if ($action=="save_update_delete_mst")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		// && is_duplicate_field( "internal_file_no", "com_sales_contract", "internal_file_no='$txt_internal_file_no'" )==1

 		$maximum_tolarence = str_replace("'", '', $txt_contract_value)+(str_replace("'", '', $txt_contract_value)*str_replace("'", '', $txt_tolerance))/100;
		$minimum_tolarence = str_replace("'", '', $txt_contract_value)-(str_replace("'", '', $txt_contract_value)*str_replace("'", '', $txt_tolerance))/100;

		$foreign_comn_value = (str_replace("'", '', $txt_contract_value)*str_replace("'", '', $txt_foreign_comn))/100;
		$local_comn_value = (str_replace("'", '', $txt_contract_value)*str_replace("'", '', $txt_local_comn))/100;

		$max_btb_limit_value = (str_replace("'", '', $txt_contract_value)*str_replace("'", '', $txt_max_btb_limit))/100;

		if($db_type==0) $year_cond="YEAR(insert_date)";
		else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
		else $year_cond="";//defined Later

		$new_contact_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_beneficiary_name), '', 'SC', date("Y",time()), 5, "select contact_prefix,contact_prefix_number from com_sales_contract where beneficiary_name=$cbo_beneficiary_name and $year_cond=".date('Y',time())." order by id desc ", "contact_prefix", "contact_prefix_number" ));

		$id=return_next_id( "id", "com_sales_contract", 1 ) ;
		if (str_replace("'", "", $hidden_variable_setting) == 1)
		{
			$company_short_name=return_field_value("company_short_name","lib_company","id=$cbo_beneficiary_name","company_short_name");
			$buyer_short_name=return_field_value("short_name","lib_buyer","id=$cbo_buyer_name","short_name");
			$dateMonth=strtoupper(date("M",time()));
			$dateYear=substr(date("Y",time()),-2);
			$txt_contract_no = "'".$company_short_name."/".$buyer_short_name."/".$new_contact_system_id[2]."/".$dateMonth."".$dateYear."'";
		}
		
		if (is_duplicate_field( "contract_no", "com_sales_contract", "contract_no=$txt_contract_no and beneficiary_name=$cbo_beneficiary_name and status_active=1 and buyer_name=$cbo_buyer_name and lien_bank=$cbo_lien_bank" )==1)
		{
			echo "11**0";disconnect($con);
			die;
		}

		$field_array="id, contact_prefix, contact_prefix_number, contact_system_id, contract_no, contract_date, beneficiary_name, buyer_name, applicant_name, notifying_party, consignee, convertible_to_lc, lien_bank, lien_date, issuing_bank, trader, country_origin, tolerance, maximum_tolarence, minimum_tolarence, last_shipment_date, expiry_date, shipping_mode, pay_term, inco_term, inco_term_place, contract_source, port_of_entry, port_of_loading, port_of_discharge, internal_file_no,shipping_line, doc_presentation_days, max_btb_limit,max_btb_limit_value, foreign_comn, foreign_comn_value, local_comn, local_comn_value, remarks, discount_clauses, tenor,currency_name, contract_value, converted_from, converted_btb_lc_list, claim_adjustment, bank_file_no, sc_year, bl_clause, export_item_category, lc_for, estimated_qnty, inserted_by, insert_date, status_active, ready_to_approved";

		$data_array="(".$id.",'".$new_contact_system_id[1]."',".$new_contact_system_id[2].",'".$new_contact_system_id[0]."',".$txt_contract_no.",".$txt_contract_date.",".$cbo_beneficiary_name.",".$cbo_buyer_name.",".$txt_applicant_name.",".$cbo_notifying_party.",".$cbo_consignee.",".$cbo_convertible_to_lc.",".$cbo_lien_bank.",".$txt_lien_date.",".$txt_issuing_bank.",".$txt_trader.",".$txt_country_origin.",".$txt_tolerance.",".$maximum_tolarence.",".$minimum_tolarence.",".$txt_last_shipment_date.",".$txt_expiry_date.",".$cbo_shipping_mode.",".$cbo_pay_term.",".$cbo_inco_term.",".$txt_inco_term_place.",".$cbo_contract_source.",".$txt_port_of_entry.",".$txt_port_of_loading.",".$txt_port_of_discharge.",".$txt_internal_file_no.",".$txt_shipping_line.",".$txt_doc_presentation_days.",".$txt_max_btb_limit.",".$max_btb_limit_value.",".$txt_foreign_comn.",".$foreign_comn_value.",".$txt_local_comn.",".$local_comn_value.",".$txt_remarks.",".$txt_discount_clauses.",".$txt_tenor.",".$cbo_currency_name.",".$txt_contract_value.",".$txt_converted_from_id.",".$txt_converted_btb_id.",".$txt_claim_adjustment.",".$txt_bank_file_no.",".$txt_year.",".$txt_bl_clause.",".$cbo_export_item_category.",".$cbo_lc_for.",".$txt_estimated_sc_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,".$cbo_ready_to_approved.")";

		$flag=1;
		$txt_attach_row_id=str_replace("'", '', $txt_attach_row_id);
		if($txt_attach_row_id!="")
		{
			$field_array_status="lc_sc_id*is_lc_sc";
			$data_array_status=$id."*1";

			$rID2=sql_multirow_update("com_btb_export_lc_attachment",$field_array_status,$data_array_status,"id",$txt_attach_row_id,0);
			if($flag==1)
			{
				if($rID2) $flag=1; else $flag=0;
			}
		}
		// echo "10**INSERT INTO com_sales_contract (".$field_array.") VALUES ".$data_array; oci_rollback($con);disconnect($con);die;
		$rID=sql_insert("com_sales_contract",$field_array,$data_array,1);
		if($flag==1)
		{
			if($rID) $flag=1; else $flag=0;
		}

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "0**".$id."**".$new_contact_system_id[0]."**".str_replace("'","",$txt_contract_no);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "5**0**"."&nbsp;";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".$id."**".$new_contact_system_id[0]."**".str_replace("'","",$txt_contract_no);
			}
			else
			{
				oci_rollback($con);
				echo "5**0**"."&nbsp;";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		if (is_duplicate_field( "contract_no", "com_sales_contract", "contract_no=$txt_contract_no and beneficiary_name=$cbo_beneficiary_name and buyer_name=$cbo_buyer_name and lien_bank=$cbo_lien_bank and status_active=1 and id<>$txt_system_id" )==1)
		{
			echo "11**0";disconnect($con);
			die;
		}
		
		$sc_value=str_replace("'", '',$txt_contract_value)*1;
		
		//$attach_ord_result=sql_select("select sum(attached_value) as attached_value from com_sales_contract_order_info where status_active=1 and is_deleted=0 and com_sales_contract_id=$txt_system_id");
		//$attach_ord_value=$attach_ord_result[0][csf("attached_value")]*1;
		
		$pre_tot_attached =  sql_select("select sum(b.attached_value) as attached_value, a.tolerance, a.contract_value
		from com_sales_contract a left join com_sales_contract_order_info b on a.id = b.com_sales_contract_id and b.status_active = 1 and b.is_deleted = 0
		where a.status_active = 1 and a.is_deleted = 0 and a.id=".$txt_system_id."
		group by a.tolerance, a.contract_value");
		$attach_ord_value=$pre_tot_attached[0][csf("attached_value")]*1;
		$tolerance=$pre_tot_attached[0][csf("tolerance")];
		$tolerance_value=($sc_value/100)*$tolerance;
		$allow_sc_value = number_format($sc_value,2,".","")+number_format($tolerance_value,2,".","");
		if($allow_sc_value<$attach_ord_value)
		{
			echo "31** Contact Value Not Allow Less Then Attach Value";disconnect($con);die;
		}

		$sc_approved=return_field_value("approved","com_sales_contract","id=$txt_system_id","approved");
		if ($sc_approved==1 || $sc_approved==3){
			echo "50**0";disconnect($con);
			die;
		}

 		$maximum_tolarence = str_replace("'", '', $txt_contract_value)+(str_replace("'", '', $txt_contract_value)*str_replace("'", '', $txt_tolerance))/100;
		$minimum_tolarence = str_replace("'", '', $txt_contract_value)-(str_replace("'", '', $txt_contract_value)*str_replace("'", '', $txt_tolerance))/100;

		$foreign_comn_value = (str_replace("'", '', $txt_contract_value)*str_replace("'", '', $txt_foreign_comn))/100;
		$local_comn_value = (str_replace("'", '', $txt_contract_value)*str_replace("'", '', $txt_local_comn))/100;

		$max_btb_limit_value = (str_replace("'", '', $txt_contract_value)*str_replace("'", '', $txt_max_btb_limit))/100;
		if (str_replace("'", "", $hidden_variable_setting) == 1)
		{
			$field_array="contract_date*applicant_name*notifying_party*consignee*convertible_to_lc*lien_bank*lien_date*issuing_bank*trader*country_origin*tolerance*last_shipment_date*expiry_date*shipping_mode*pay_term*inco_term*inco_term_place*contract_source*port_of_entry*port_of_loading*port_of_discharge*internal_file_no*shipping_line*doc_presentation_days*max_btb_limit*max_btb_limit_value*foreign_comn*foreign_comn_value*local_comn*local_comn_value*remarks*discount_clauses*tenor*currency_name*contract_value*converted_from*converted_btb_lc_list*claim_adjustment*bank_file_no*sc_year*bl_clause*export_item_category*estimated_qnty*ready_to_approved*updated_by*update_date*status_active";

			$data_array="".$txt_contract_date."*".$txt_applicant_name."*".$cbo_notifying_party."*".$cbo_consignee."*".$cbo_convertible_to_lc."*".$cbo_lien_bank."*".$txt_lien_date."*".$txt_issuing_bank."*".$txt_trader."*".$txt_country_origin."*".$txt_tolerance."*".$txt_last_shipment_date."*".$txt_expiry_date."*".$cbo_shipping_mode."*".$cbo_pay_term."*".$cbo_inco_term."*".$txt_inco_term_place."*".$cbo_contract_source."*".$txt_port_of_entry."*".$txt_port_of_loading."*".$txt_port_of_discharge."*".$txt_internal_file_no."*".$txt_shipping_line."*".$txt_doc_presentation_days."*".$txt_max_btb_limit."*".$max_btb_limit_value."*".$txt_foreign_comn."*".$foreign_comn_value."*".$txt_local_comn."*".$local_comn_value."*".$txt_remarks."*".$txt_discount_clauses."*".$txt_tenor."*".$cbo_currency_name."*".$txt_contract_value."*".$txt_converted_from_id."*".$txt_converted_btb_id."*".$txt_claim_adjustment."*".$txt_bank_file_no."*".$txt_year."*".$txt_bl_clause."*".$cbo_export_item_category."*".$txt_estimated_sc_qnty."*".$cbo_ready_to_approved."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
		}
		else
		{
			$field_array="contract_no*contract_date*applicant_name*notifying_party*consignee*convertible_to_lc*lien_bank*lien_date*issuing_bank*trader*country_origin*tolerance*last_shipment_date*expiry_date*shipping_mode*pay_term*inco_term*inco_term_place*contract_source*port_of_entry*port_of_loading*port_of_discharge*internal_file_no*shipping_line*doc_presentation_days*max_btb_limit*max_btb_limit_value*foreign_comn*foreign_comn_value*local_comn*local_comn_value*remarks*discount_clauses*tenor*currency_name*contract_value*converted_from*converted_btb_lc_list*claim_adjustment*bank_file_no*sc_year*bl_clause*export_item_category*estimated_qnty*ready_to_approved*updated_by*update_date*status_active";

			$data_array="".$txt_contract_no."*".$txt_contract_date."*".$txt_applicant_name."*".$cbo_notifying_party."*".$cbo_consignee."*".$cbo_convertible_to_lc."*".$cbo_lien_bank."*".$txt_lien_date."*".$txt_issuing_bank."*".$txt_trader."*".$txt_country_origin."*".$txt_tolerance."*".$txt_last_shipment_date."*".$txt_expiry_date."*".$cbo_shipping_mode."*".$cbo_pay_term."*".$cbo_inco_term."*".$txt_inco_term_place."*".$cbo_contract_source."*".$txt_port_of_entry."*".$txt_port_of_loading."*".$txt_port_of_discharge."*".$txt_internal_file_no."*".$txt_shipping_line."*".$txt_doc_presentation_days."*".$txt_max_btb_limit."*".$max_btb_limit_value."*".$txt_foreign_comn."*".$foreign_comn_value."*".$txt_local_comn."*".$local_comn_value."*".$txt_remarks."*".$txt_discount_clauses."*".$txt_tenor."*".$cbo_currency_name."*".$txt_contract_value."*".$txt_converted_from_id."*".$txt_converted_btb_id."*".$txt_claim_adjustment."*".$txt_bank_file_no."*".$txt_year."*".$txt_bl_clause."*".$cbo_export_item_category."*".$txt_estimated_sc_qnty."*".$cbo_ready_to_approved."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
		}
		

		//update code here

 		//print_r($data_array);die;

		$flag=1;
		$txt_attach_row_id=str_replace("'", '', $txt_attach_row_id);
		if($txt_attach_row_id!="")
		{
			$field_array_status="lc_sc_id*is_lc_sc";
			$data_array_status=$txt_system_id."*1";

			$rID2=sql_multirow_update("com_btb_export_lc_attachment",$field_array_status,$data_array_status,"id",$txt_attach_row_id,0);
			if($flag==1)
			{
				if($rID2) $flag=1; else $flag=0;
			}
		}

		$rID=sql_update("com_sales_contract",$field_array,$data_array,"id","".$txt_system_id."",1);
		if($flag==1)
		{
			if($rID) $flag=1; else $flag=0;
		}

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'", '', $txt_system_id)."**".str_replace("'", '', $contact_system_id)."**".str_replace("'","",$txt_contract_no);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "6**".str_replace("'", '', $txt_system_id)."**".str_replace("'", '', $contact_system_id)."**".str_replace("'","",$txt_contract_no);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'", '', $txt_system_id)."**".str_replace("'", '', $contact_system_id)."**".str_replace("'","",$txt_contract_no);
			}
			else
			{
				oci_rollback($con);
				echo "6**".str_replace("'", '', $txt_system_id)."**".str_replace("'", '', $contact_system_id)."**".str_replace("'","",$txt_contract_no);
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

		$sc_approved=return_field_value("approved","com_sales_contract","id=$txt_system_id","approved");
		if ($sc_approved==1 || $sc_approved==3){
			echo "50**0";disconnect($con);
			die;
		}
		
		$update_field_arr="updated_by*update_date*status_active*is_deleted";
		$update_data_arr="".$user_id."*'".$pc_date_time."'*0*1";
		$salesMst=$salesPo=true;
		//echo "10** $invMst && $invDtls && $invPo && $invClr = $id";oci_rollback($con);die;
		//echo "10**"."Update com_export_invoice_ship_mst set status_active=0,is_deleted=1,updated_by=$user_id,update_date='$pc_date_time'  where id =$id";oci_rollback($con);die;
		$btb_id = return_field_value("id","com_btb_export_lc_attachment","lc_sc_id=".$id." and is_lc_sc=1 and status_active=1 and is_deleted=0","id");
		if($btb_id>0)
		{
			echo "31**Delete Not Allow. This SC No Found in BTB/Margin LC";disconnect($con); die;
		}

		$invoice_id = return_field_value("id","com_export_invoice_ship_mst","lc_sc_id=".$id." and is_lc=2 and status_active=1 and is_deleted=0","id");
		if($invoice_id>0)
		{
			echo "31**Delete Not Allow. This SC No Found in Invoice"; disconnect($con);die;
		}
		else
		{
			if($id>0)
			{
				$salesMst=sql_update("com_sales_contract",$update_field_arr,$update_data_arr,"id",$id,1);
				$salesPo=sql_update("com_sales_contract_order_info",$update_field_arr,$update_data_arr,"com_sales_contract_id",$id,1);
			}
			//echo "10** $invMst && $invDtls && $invPo && $invClr = $update_id";oci_rollback($con);die;
			if($db_type==0)
			{
				if($salesMst && $salesPo)
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
				if($salesMst && $salesPo)
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

if ($action=="save_update_delete_contract_order_info")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$sc_approved=return_field_value("approved","com_sales_contract","id=$txt_system_id","approved");
		if ($sc_approved==1 || $sc_approved==3){
			echo "50**0";disconnect($con);
			die;
		}
		
		$sc_ammendment_id=return_field_value("max(id) as amd_id","com_sales_contract_amendment","contract_id=$txt_system_id and status_active=1","amd_id");
		if($sc_ammendment_id=="") $sc_ammendment_id=0;
 		$field_array="id,com_sales_contract_id,wo_po_break_down_id,attached_qnty,attached_rate,attached_value,status_active,fabric_description,category_no,hs_code,inserted_by,insert_date,commission,foregin_commission,sc_amendment_id,is_sales";
		$id = return_next_id( "id", "com_sales_contract_order_info", 1 );
		for($j=1;$j<=$noRow;$j++)
		{
			$hiddenwopobreakdownid="hiddenwopobreakdownid_".$j;
			$txtattachedqnty="txtattachedqnty_".$j;
			$hiddenunitprice="hiddenunitprice_".$j;
			$txtattachedvalue="txtattachedvalue_".$j;
			$txtcommission="txtcommission_".$j;
			$txtcommissionforeign="txtcommissionforeign_".$j;
			$cbopostatus="cbopostatus_".$j;
			$txtfabdescrip="txtfabdescrip_".$j;
			$txtcategory="txtcategory_".$j;
			$txthscode="txthscode_".$j;
			$isSales="isSales_".$j;
			if(str_replace("'","",$$isSales)=="") $is_Sales=0; else $is_Sales=str_replace("'","",$$isSales);

			if($$hiddenwopobreakdownid!="")
			{
				if($data_array!="") $data_array.=",";

				$data_array.="(".$id.",".$txt_system_id.",".$$hiddenwopobreakdownid.",".$$txtattachedqnty.",".$$hiddenunitprice.",".$$txtattachedvalue.",".$$cbopostatus.",".$$txtfabdescrip.",".$$txtcategory.",".$$txthscode.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$txtcommission.",".$$txtcommissionforeign.",'".$sc_ammendment_id."','".$is_Sales."')";
				$id = $id+1;

				$currentattachedval = str_replace("'","",$$txtattachedvalue);
				$currentattachedvalue += number_format($currentattachedval,2,'.','');
			}

		}

		//$sales_contract_value=return_field_value("contract_value","com_sales_contract","id=".$txt_system_id);
		//$tot_attached=return_field_value("sum(attached_value) as tot_attached","com_sales_contract a, com_sales_contract_order_info b","a.id = b.com_sales_contract_id and id=".$txt_system_id." and b.status_active = 1 and b.is_deleted = 0");

		$pre_tot_attached =  sql_select("select sum(b.attached_value) as attached_value, a.tolerance, a.contract_value
		from com_sales_contract a left join com_sales_contract_order_info b on a.id = b.com_sales_contract_id and b.status_active = 1 and b.is_deleted = 0
		where a.status_active = 1 and a.is_deleted = 0 and a.id=".$txt_system_id."
		group by a.tolerance, a.contract_value");
		$sales_contract_value=$pre_tot_attached[0][csf("contract_value")];
		$tolerance=$pre_tot_attached[0][csf("tolerance")];
		$tolerance_value=($pre_tot_attached[0][csf("contract_value")]/100)*$pre_tot_attached[0][csf("tolerance")];
		//echo "11** $tolerance_value Attached Value Exceeds Contract Value ";disconnect($con);die;
		$sales_contract_value = number_format($sales_contract_value,2,".","")+number_format($tolerance_value,2,".","");
		$tot_attached = number_format($pre_tot_attached[0][csf("attached_value")],2,".","");
		if(($tot_attached + $currentattachedvalue) > $sales_contract_value)
		{
			echo "11** Attached Value Exceeds Contract Value ";disconnect($con);die;
		}

		// echo "11** insert into com_sales_contract_order_info ($field_array) values $data_array";die;
		$rID=sql_insert("com_sales_contract_order_info",$field_array,$data_array,1);
		//echo "5** $rID ";oci_rollback($con);disconnect($con);die;
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");
				echo "0**".str_replace("'", '', $txt_system_id)."**0";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "5**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);
				echo "0**".str_replace("'", '', $txt_system_id)."**0";
			}
			else
			{
				oci_rollback($con);
				echo "5**0**0";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$sc_approved=return_field_value("approved","com_sales_contract","id=$txt_system_id","approved");
		if ($sc_approved==1 || $sc_approved==3){
			echo "50**0";disconnect($con);
			die;
		}

		 //update code here
		$field_array="id,com_sales_contract_id,wo_po_break_down_id,attached_qnty,attached_rate,attached_value,commission,foregin_commission,status_active,fabric_description,category_no,hs_code,inserted_by,insert_date,is_sales";
		$field_array_update="wo_po_break_down_id*attached_qnty*attached_rate*attached_value*commission*foregin_commission*status_active*fabric_description*category_no*hs_code*updated_by*update_date*is_sales";

		$currentattachedvalue = 0;
		$id = return_next_id( "id", "com_sales_contract_order_info", 1 );
		for($j=1;$j<=$noRow;$j++)
		{
			$hiddenwopobreakdownid="hiddenwopobreakdownid_".$j;
			$txtattachedqnty="txtattachedqnty_".$j;
			$hiddenunitprice="hiddenunitprice_".$j;
			$txtattachedvalue="txtattachedvalue_".$j;
			$txtcommissionforeign="txtcommissionforeign_".$j;
			$cbopostatus="cbopostatus_".$j;
			$txtcommission="txtcommission_".$j;
			$txtfabdescrip="txtfabdescrip_".$j;
			$txtcategory="txtcategory_".$j;
			$txthscode="txthscode_".$j;
			$isSales="isSales_".$j;
			$hiddensalescontractorderid="hiddensalescontractorderid_".$j;
			
			if(str_replace("'","",$$isSales)=="") $is_Sales=0; else $is_Sales=str_replace("'","",$$isSales);
			if(str_replace("'","",$$hiddensalescontractorderid)!="")
			{
				if(str_replace("'", '', $$cbopostatus)==0)
				{
					$invoice_no="";
					$po_id=$$hiddenwopobreakdownid;
					$sql_invoice="select a.invoice_no from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b where a.id=b.mst_id and a.lc_sc_id=$txt_system_id and a.is_lc=2 and b.po_breakdown_id=$po_id and b.current_invoice_qnty>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.invoice_no";
					$data=sql_select($sql_invoice);
					if(count($data)>0)
					{
						foreach($data as $row)
						{
							if($invoice_no=="") $invoice_no=$row[csf('invoice_no')]; else $invoice_no.=",\n".$row[csf('invoice_no')];
						}
						echo "13**".$invoice_no."**1";disconnect($con);
						die;
					}
				}
				$id_array_update[]=str_replace("'","",$$hiddensalescontractorderid);
				$data_array_update[str_replace("'","",$$hiddensalescontractorderid)] = explode("*",("".$$hiddenwopobreakdownid."*".$$txtattachedqnty."*".$$hiddenunitprice."*".$$txtattachedvalue."*".$$txtcommission."*".$$txtcommissionforeign."*".$$cbopostatus."*".$$txtfabdescrip."*".$$txtcategory."*".$$txthscode."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'".$is_Sales."'"));
				$currentattachedvalue += number_format(str_replace("'","",$$txtattachedvalue),2,'.','');
			}
			else
			{
				if($data_array!="") $data_array.=",";

				$data_array ="(".$id.",".$txt_system_id.",".$$hiddenwopobreakdownid.",".$$txtattachedqnty.",".$$hiddenunitprice.",".$$txtattachedvalue.",".$$txtcommission.",".$$txtcommissionforeign.",".$$cbopostatus.",".$$txtfabdescrip.",".$$txtcategory.",".$$txthscode.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$is_Sales."')";
				$id = $id+1;
				$currentattachedvalue += number_format(str_replace("'","",$$txtattachedvalue),2,'.','');
			}
		}



		//$currentattachedvalue += number_format(str_replace("'","",$$txtattachedvalue),2,'.','');
		$without_update_dtls_cond="";
		if(count($id_array_update)>0){
			$without_update_dtls_cond = " and b.id not in (".implode(",",$id_array_update).")";
		}

		/*$sales_contract_value=return_field_value("contract_value","com_sales_contract","id=".$txt_system_id);
		$tot_attached=return_field_value("sum(b.attached_value) as attached_value","com_sales_contract a, com_sales_contract_order_info b","a.id = b.com_sales_contract_id and a.id=".$txt_system_id." $without_update_dtls_cond  and b.status_active = 1 and b.is_deleted = 0");

		$pre_tot_attached =  sql_select("select sum(b.attached_value) as attached_value
			from com_sales_contract a, com_sales_contract_order_info b
			where a.id = b.com_sales_contract_id and a.id=".$txt_system_id."  $without_update_dtls_cond
			and b.status_active = 1 and b.is_deleted = 0");



		$sales_contract_value = number_format($sales_contract_value,2,".","");
		
		$tot_attached = number_format($pre_tot_attached[0][csf("attached_value")],2,".","");

		if(($tot_attached + $currentattachedvalue) > $sales_contract_value)
		{
			echo "11** Attached Value Exceeds Contract Value ";disconnect($con);die;
		}*/
		
		$pre_tot_attached =  sql_select("select sum(b.attached_value) as attached_value, a.tolerance, a.contract_value
		from com_sales_contract a left join com_sales_contract_order_info b on a.id = b.com_sales_contract_id and b.status_active = 1 and b.is_deleted = 0 $without_update_dtls_cond
		where a.status_active = 1 and a.is_deleted = 0 and a.id=".$txt_system_id."
		group by a.tolerance, a.contract_value");
		$sales_contract_value=$pre_tot_attached[0][csf("contract_value")];
		$tolerance=$pre_tot_attached[0][csf("tolerance")];
		$tolerance_value=($pre_tot_attached[0][csf("contract_value")]/100)*$pre_tot_attached[0][csf("tolerance")];
		$currentattachedvalue = number_format($currentattachedvalue,2,".","");
		$sales_contract_value = number_format($sales_contract_value,2,".","")+number_format($tolerance_value,2,".","");
		$tot_attached = number_format($pre_tot_attached[0][csf("attached_value")],2,".","");
		if(($tot_attached + $currentattachedvalue) > $sales_contract_value)
		{
			echo "11** Attached Value Exceeds Contract Value ";disconnect($con);die;
		}

		//echo "11** test = ".$tot_attached." = $currentattachedvalue = $sales_contract_value";die;





		//echo "insert into com_sales_contract_order_info (".$field_array.") values".$data_array;die;

		$flag=1;$rID2=$rID=true;
		if($data_array!="")
		{
			$rID2=sql_insert("com_sales_contract_order_info",$field_array,$data_array,0);
			if($flag==1)
			{
				if($rID2) $flag=1; else $flag=0;
			}
		}

		if(count($id_array_update)>0)
		{
			$rID=execute_query(bulk_update_sql_statement( "com_sales_contract_order_info", "id", $field_array_update, $data_array_update, $id_array_update ));
			if($flag==1)
			{
				if($rID) $flag=1; else $flag=0;
			}
		}
		
		//echo "6**0**1 = $rID2 =$rID ";oci_rollback($con);disconnect($con);die;

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'", '', $txt_system_id)."**0";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "6**0**1";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'", '', $txt_system_id)."**0";
			}
			else
			{
				oci_rollback($con);
				echo "6**0**1";
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="sales_contact_lien_letter")
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
			@page {size: A4 portrait;}
        }
    </style>
	<?
	$data=explode("**",$data);
	//Sales Contact Lien-------------
	$buyer_lib   = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_lib = return_library_array( "select id, company_name from lib_company",'id','company_name');

	if($data[0]==3)
	{
		$data_array=sql_select("select id, contract_no, contract_date, lien_bank, lien_date, contract_value, internal_file_no, contact_system_id, currency_name, beneficiary_name, buyer_name, last_shipment_date from com_sales_contract where id='$data[1]'");
		foreach ($data_array as $row)
		{
			$internal_file_no	= $row[csf("internal_file_no")];
			$contact_system_id  = $row[csf("contact_system_id")];
			$contract_no		= $row[csf("contract_no")];
			$contract_value		= def_number_format($row[csf("contract_value")],2);
			$contract_date		= change_date_format($row[csf("contract_date")]);
			$last_shipment_date	= change_date_format($row[csf("last_shipment_date")]);
			$lien_bank			= $row[csf("lien_bank")];
			$lien_date			= $row[csf("lien_date")];
			$currency_name      = $currency[$row[csf("currency_name")]];
			$company_name       = strtoupper($company_lib[$row[csf("beneficiary_name")]]);
			$buyer_name         = $buyer_lib[$row[csf("buyer_name")]];
		}
		//echo $buyer_name;die;
		// echo "<pre>";
		// print_r($data_array);die;

		$data_array1=sql_select("select wm.total_set_qnty as ratio, ci.attached_qnty from wo_po_break_down wb, wo_po_details_master wm, com_sales_contract_order_info ci where wb.job_id = wm.id and wb.id=ci.wo_po_break_down_id and ci.com_sales_contract_id='$data[1]' and ci.status_active = '1' and ci.is_deleted = '0' order by ci.id");
		foreach($data_array1 as $row1)
		{
			$order_qnty_in_pcs=$row1[csf('attached_qnty')]*$row1[csf('ratio')];
			$total_attach_qty+=$order_qnty_in_pcs;
		}
	}

	//bank information retriving here
	$designation_library=return_library_array( "select id,custom_designation from lib_designation", "id", "custom_designation");
	$data_array1=sql_select("select id, bank_name, branch_name, contact_person, address, designation from lib_bank where id='$lien_bank'");
	foreach ($data_array1 as $row1)
	{
		$bank_name		= strtoupper($row1[csf("bank_name")]);
		$branch_name	= strtoupper($row1[csf("branch_name")]);
		$contact_person	= strtoupper($row1[csf("contact_person")]);
		$address		= strtoupper($row1[csf("address")]);
		$designation    = strtoupper($designation_library[$row1[csf("designation")]]);
	}

	//letter body is retriving here
	$data_array2=sql_select("select letter_body from dynamic_letter where letter_type='$data[0]'");
	foreach ($data_array2 as $row2)
	{
		$letter_body = $row2[csf("letter_body")];
	}

	$raw_data=str_replace("INTERNALFILENO",$internal_file_no,$letter_body);
	$raw_data=str_replace("SYSTEMREF",$contact_system_id,$raw_data);
	$raw_data=str_replace("LIENDATE",date('F d, Y', strtotime($lien_date)),$raw_data);
	$raw_data=str_replace("CONTRACTPERSON",$contact_person,$raw_data);
	$raw_data=str_replace("BANKNAME",$bank_name,$raw_data);
	$raw_data=str_replace("DESIGNATION", $designation, $raw_data);
	$raw_data=str_replace("BRANCHNAME",$branch_name,$raw_data);
	$raw_data=str_replace("BANKADDRESS",$address,$raw_data);
	$raw_data=str_replace("CONTRACTNO",$contract_no,$raw_data);
	$raw_data=str_replace("CONTRACTDATE",date('d.m.Y', strtotime($contract_date)),$raw_data);
	$raw_data=str_replace("CONTRACTVALUE",$contract_value,$raw_data);
	$raw_data=str_replace("TOTALATTACHQTY",$total_attach_qty,$raw_data);
	$raw_data=str_replace("LASTSHIPMENTDATE",$last_shipment_date,$raw_data);
	$raw_data= str_replace("CURRENCY", $currency_name, $raw_data);
	$raw_data= str_replace("BENEFICIARY", $company_name, $raw_data);
	$raw_data= str_replace("BUYERNAME", $buyer_name, $raw_data);

	echo "<div class='a4size'>".$raw_data."</div>";
	exit();
}

if ($action == "sales_contact_lien_letter2") // lien_letter 2
{
    //echo $data; die;
    $data = explode("**", $data);

    //export lc lien-----------------
    $company_lib=return_library_array( "select id, company_name from lib_company",'id','company_name');
    $buyer_lib=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    if($data[0]==3)
	{
		$data_array=sql_select("SELECT id, contract_no, contract_date, lien_bank, lien_date, contract_value, internal_file_no, contact_system_id, currency_name, beneficiary_name, buyer_name, last_shipment_date, expiry_date, bank_file_no, pay_term, tenor from com_sales_contract where id='$data[1]'");
		foreach ($data_array as $row)
		{
			$internal_file_no	= $row[csf("internal_file_no")];
			$contact_system_id  = $row[csf("contact_system_id")];
			$contract_no		= $row[csf("contract_no")];
			$contract_value		= def_number_format($row[csf("contract_value")],2);
			$sc_date			= $row[csf("contract_date")];
			$contract_date		= change_date_format($row[csf("contract_date")]);
			$last_shipment_date	= change_date_format($row[csf("last_shipment_date")]);
			$lien_bank			= $row[csf("lien_bank")];
			$lien_date			= $row[csf("lien_date")];
			$currency_name      = $currency[$row[csf("currency_name")]];
			$company_name       = strtoupper($company_lib[$row[csf("beneficiary_name")]]);
			$buyer_name         = $buyer_lib[$row[csf("buyer_name")]];
			$expiry_date        = $row[csf("expiry_date")];
			$bank_file_no 		= $row[csf("bank_file_no")];
			$payTerm 			= $pay_term[$row[csf("pay_term")]];
            $tenor 				= $row[csf("tenor")];
		}
		//echo $buyer_name;die;
		// echo "<pre>";
		// print_r($data_array);die;

		$data_array1=sql_select("SELECT wm.total_set_qnty as ratio, ci.attached_qnty, ci.attached_value 
		from wo_po_break_down wb, wo_po_details_master wm, com_sales_contract_order_info ci 
		where wb.job_id=wm.id and wb.id=ci.wo_po_break_down_id and ci.com_sales_contract_id='$data[1]' and ci.status_active='1' and ci.is_deleted='0' order by ci.id");
		foreach($data_array1 as $row1)
		{
			$order_qnty_in_pcs=$row1[csf('attached_qnty')]*$row1[csf('ratio')];
			$total_attach_qty+=$order_qnty_in_pcs;
			$total_attached_value += $row1[csf('attached_value')];
		}
	}

    $sql_comm_freight="SELECT a.com_sales_contract_id,a.wo_po_break_down_id, c.costing_per, d.comm_cost, d.freight
    from com_sales_contract_order_info a, wo_po_break_down b, wo_pre_cost_mst c, wo_pre_cost_dtls d
    where a.wo_po_break_down_id=b.id and b.job_no_mst=c.job_no and c.job_no=d.job_no and com_sales_contract_id='$data[1]'";
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

	$sql_amendment="SELECT b.id,b.amendment_no, b.amendment_date, b.amendment_value, b.value_change_by, b.last_shipment_date, b.insert_date
	from  com_sales_contract_amendment b where b.contract_id='$data[1]' order by B.id desc";
	$amendment_data_arr = sql_select($sql_amendment);
    /*echo "<pre>";
    print_r($amendment_data_arr);*/
    $amendment_no=$amendment_data_arr[0][AMENDMENT_NO];
    $amendment_date=$amendment_data_arr[0][AMENDMENT_DATE];
    $amendment_value=$amendment_data_arr[0][AMENDMENT_VALUE];
    $increase_decrease=$amendment_data_arr[0][VALUE_CHANGE_BY];
    $amendment_last_shipment_date=$amendment_data_arr[0][LAST_SHIPMENT_DATE];
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
    </style>
    <div class="a4size">
        <table width="794" cellpadding="0" cellspacing="0" border="0" >
            <div class="parent" >
                <? echo date('M d, Y',strtotime($sc_date));?>
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
                <strong>Subject:  : Submission of sales contract worth for  # <? echo $contract_no." date ".$contract_date." for ".$currency_name." $".$contract_value; ?> & open BTB L/C.</strong></td>
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
                In reference to the above, we are submitting herewith the above noted <strong>Sales Contract</strong> for your kind attention and request you to keep under lien against which we will open BTB LC for USD i.e. @75% of FOB value.
                </td>
                <td width="25" ></td>
            </tr>
        </table>
        <table width="794" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td colspan="3" height="15"></td>
            </tr>
            <tr>
                <td colspan="3" style="padding-left: 28px;">Details of the <strong>Sales Contract</strong> as follows:</td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">01. Sales Contract</td>
                <td width="15" >:</td>
                <td width="380"><? echo $contract_no;?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">02. Date</td>
                <td width="15" >:</td>
                <td width="380"><? echo $contract_date; ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">03. Rev no & Date</td>
                <td width="15" >:</td>
                <td width="380"><? echo $amendment_no .' & '.change_date_format($amendment_date) ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">04. <? echo ($increase_decrease==1) ? "Increased Value" : "Decreased Value" ; ?></td>
                <td width="15" >:</td>
                <td width="380"><? if($amendment_value) echo '$'.number_format($amendment_value,2); ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">05. Value in USD</td>
                <td width="15" >:</td>
                <td width="380"><? if($contract_value) echo '$'.$contract_value,2; ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">06. Buyer Name</td>
                <td width="15" >:</td>
                <td width="380"><? echo $buyer_name; ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">07. Comm./Freight</td>
                <td width="15" >:</td>
                <td width="380" title="(Comm+Freight)/Costing Per*Attach.Qty Pcs"><? $comm_freight=($total_comm_cost+$total_freight)*$total_attach_qty;
                	if ($comm_freight>0) echo '$'.number_format($comm_freight,2); ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">08. FOB Value</td>
                <td width="15" >:</td>
                <td width="380"><? if($total_attached_value) echo '$'.number_format($total_attached_value,2); ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">09. 75% BTB Limit</td>
                <td width="15" >:</td>
                <td width="380"><? if($contract_value) echo '$'.number_format(($contract_value*75)/100,2); ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">10. Tenor</td>
                <td width="15" >:</td>
                <td width="380"><? echo $payTerm; if ($tenor>0) { echo ', '.$tenor.' Days'; } ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">11. Qty</td>
                <td width="15" >:</td>
                <td width="380"><? if($total_attach_qty) echo number_format($total_attach_qty,2).' PCS'; ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">12. Shipment Date</td>
                <td width="15" >:</td>
                <td width="380"><? echo change_date_format($last_shipment_date); ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">13. Expiry Date</td>
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
                We would request your kind self to do the needful to lien the aforesaid <strong>Sales Contract</strong> and necessary action at your end.
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

if ($action == "sales_contact_lien_letter3") // Lien Export Lc App
{
    // echo $data; die;
    $data = explode("**", $data);

    //export lc lien-----------------
    $company_lib=return_library_array( "select id, company_name from lib_company",'id','company_name');
    $buyer_lib=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    if($data[0]==3)
	{
		$data_array=sql_select("SELECT id, contract_no, contract_date, lien_bank, lien_date, contract_value, internal_file_no, contact_system_id, currency_name, beneficiary_name, buyer_name, last_shipment_date,export_item_category, expiry_date, bank_file_no, pay_term, tenor, max_btb_limit, max_btb_limit_value from com_sales_contract where id='$data[1]'");
		$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
		$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
		foreach ($data_array as $row)
		{
			$internal_file_no	= $row[csf("internal_file_no")];
			$contact_system_id  = $row[csf("contact_system_id")];
			$contract_no		= $row[csf("contract_no")];
			$contract_value		= def_number_format($row[csf("contract_value")],2);
			$contract_btb_limit_value= def_number_format($row[csf("max_btb_limit_value")],2);
			$contract_btb_limit	= def_number_format($row[csf("max_btb_limit")],2);
			$item_category		= $export_item_category[$row[csf("export_item_category")]];
			$sc_date			= $row[csf("contract_date")];
			$contract_date		= change_date_format($row[csf("contract_date")]);
			$lien_bank			= $row[csf("lien_bank")];
			$currency_name      = $currency[$row[csf("currency_name")]];
			$company_name       = strtoupper($company_lib[$row[csf("beneficiary_name")]]);
			$buyer_name         = $buyer_lib[$row[csf("buyer_name")]];
			$currency_sign 		= $currency_sign_arr[$row[csf("currency_name")]];
			$ref				= $company_arr[$row[csf("beneficiary_name")]];
		}
	}
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
			@page {size: A4 portrait;}
        }
        .parent {
          display: flex;
          flex-direction:row;
          margin-left: 28px;
          margin-top: 60px; padding-top: 60px;
        }

    </style>
    <div class="a4size">
        <table width="794" cellpadding="0" cellspacing="0" border="0" >
            <div class="parent" >
			Ref No.: <? echo $ref."/COM/".$contract_no?></br>
                <? //echo date('d M Y',strtotime($sc_date));?>
				<? echo date('d-m-Y');?>
            </div>
            <br>
            <tr>
                <td width="25" ></td>
                <td width="650" align="left">
                <?
                    echo "The Manager <br>";
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
                Sub: <strong>Request for Scrutiny Lien of Export S/C No. <? echo $contract_no." dated: ".$contract_date." </strong>for S/C Value ".$currency_name." ".$currency_sign.''.$contract_value; ?></td>
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
                While we offer our heartfelt gratitude for providing us continuous support in running our export business smoothly, we take the liberty to request you to sanction BTB L/C limit for <? echo $currency_name." ".$contract_btb_limit_value ?> against lien of L/C cited above.  Necessary details are given below: 
                </td>
                <td width="25" ></td>
            </tr>
        </table>
        <table width="794" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td colspan="3" height="15"></td>
            </tr>
            <tr>
                <td colspan="3" style="padding-left: 28px;"></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">01. Export S/C No & Date  </td>
                <td width="15" >:</td>
                <td width="380"><? echo $contract_no." dated:".$contract_date;?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">02. Currency and Amount</td>
                <td width="15" >:</td>
                <td width="380"><? if($contract_value) echo '$'.number_format($contract_value,2); ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">03. Buyer</td>
                <td width="15" >:</td>
                <td width="380"><? echo $buyer_name; ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">04. Beneficiary</td>
                <td width="15" >:</td>
                <td width="380"><? echo $company_name ;?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">05. Item (Merchandise) to be exported</td>
                <td width="15" >:</td>
                <td width="380"><?  echo $item_category; ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">06. <span style="width:220">Maximum extent of BTB L/C required to be issued & Amount </span></td>
                <td width="15" >:</td>
                <td width="380" ><? echo $contract_btb_limit." of L/C value ".$currency_name."".$contract_btb_limit_value; ?></td>
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
                Please be noted that BTB L/C will be opened by us in phase under the Mother L/C noted above
                <br><br>
                Within the validity managing Lead time. 
                </td>
                <td width="25" ></td>
            </tr>
            <tr>
			<td width="25" ></td>
                <td colspan="2" height="50">Thanks & Regards,</br>
					Very truly yours, 
					</td>
            </tr>
        </table>
    </div>
    <!--  -->
    <?
    exit();
}


if ($action == "sales_contact_lien_letter5") // lien_letter 3
{
    //echo $data; die;
    $data = explode("**", $data);

    //export lc lien-----------------
    $company_lib=return_library_array( "select id, company_name from lib_company",'id','company_name');
    $buyer_lib=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    if($data[0]==3)
	{
		$data_array=sql_select("SELECT id, contract_no, contract_date, lien_bank, lien_date,contract_value, max_btb_limit, max_btb_limit_value, internal_file_no, contact_system_id, currency_name, beneficiary_name, buyer_name, last_shipment_date, expiry_date, bank_file_no, pay_term, tenor from com_sales_contract where id='$data[1]'");
		foreach ($data_array as $row)
		{
			$internal_file_no	= $row[csf("internal_file_no")];
			$contact_system_id  = $row[csf("contact_system_id")];
			$contract_no		= $row[csf("contract_no")];
			$max_btb_limit		= $row[csf("max_btb_limit")];
			$contract_value		= def_number_format($row[csf("contract_value")],2);
			$max_btb_limit_value= def_number_format($row[csf("max_btb_limit_value")],2);
			$sc_date			= $row[csf("contract_date")];
			$contract_date		= change_date_format($row[csf("contract_date")]);
			$last_shipment_date	= change_date_format($row[csf("last_shipment_date")]);
			$lien_bank			= $row[csf("lien_bank")];
			$lien_date			= $row[csf("lien_date")];
			$currency_name      = $currency[$row[csf("currency_name")]];
			$company_name       = strtoupper($company_lib[$row[csf("beneficiary_name")]]);
			$buyer_name         = $buyer_lib[$row[csf("buyer_name")]];
			$expiry_date        = $row[csf("expiry_date")];
			$bank_file_no 		= $row[csf("bank_file_no")];
			$payTerm 			= $pay_term[$row[csf("pay_term")]];
            $tenor 				= $row[csf("tenor")];
		}
		//echo $buyer_name;die;
		// echo "<pre>";
		// print_r($data_array);die;

		$data_array1=sql_select("SELECT wm.total_set_qnty as ratio, ci.attached_qnty, ci.attached_value 
		from wo_po_break_down wb, wo_po_details_master wm, com_sales_contract_order_info ci 
		where wb.job_id=wm.id and wb.id=ci.wo_po_break_down_id and ci.com_sales_contract_id='$data[1]' and ci.status_active='1' and ci.is_deleted='0' order by ci.id");
		foreach($data_array1 as $row1)
		{
			$order_qnty_in_pcs=$row1[csf('attached_qnty')]*$row1[csf('ratio')];
			$total_attach_qty+=$order_qnty_in_pcs;
			$total_attached_value += $row1[csf('attached_value')];
		}
	}

    $sql_comm_freight="SELECT a.com_sales_contract_id,a.wo_po_break_down_id, c.costing_per, d.comm_cost, d.freight
    from com_sales_contract_order_info a, wo_po_break_down b, wo_pre_cost_mst c, wo_pre_cost_dtls d
    where a.wo_po_break_down_id=b.id and b.job_no_mst=c.job_no and c.job_no=d.job_no and com_sales_contract_id='$data[1]'";
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

	$sql_amendment="SELECT b.id,b.amendment_no, b.amendment_date, b.amendment_value, b.value_change_by, b.last_shipment_date, b.insert_date
	from  com_sales_contract_amendment b where b.contract_id='$data[1]' order by B.id desc";
	$amendment_data_arr = sql_select($sql_amendment);
    /*echo "<pre>";
    print_r($amendment_data_arr);*/
    $amendment_no=$amendment_data_arr[0][AMENDMENT_NO];
    $amendment_date=$amendment_data_arr[0][AMENDMENT_DATE];
    $amendment_value=$amendment_data_arr[0][AMENDMENT_VALUE];
    $increase_decrease=$amendment_data_arr[0][VALUE_CHANGE_BY];
    $amendment_last_shipment_date=$amendment_data_arr[0][LAST_SHIPMENT_DATE];
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
    </style>
    <div class="a4size">
        <table width="794" cellpadding="0" cellspacing="0" border="0" >
            <br>
			<tr><td colspan="3" height="60"></td></tr>
            <tr>
                <td width="25"></td>
                <td width="650" align="left">Ref No: <?=$contact_system_id;?></td>
                <td width="25" ></td>
            </tr>
			<tr><td colspan="3" height="20"></td></tr>
            <tr>
                <td width="25"></td>
                <td width="650" align="left"><? echo date('M d, Y',strtotime($sc_date));?></td>
                <td width="25" ></td>
            </tr>
			<tr><td colspan="3" height="30"></td></tr>
            <tr>
                <td width="25"></td>
                <td width="650" align="left">To</td>
                <td width="25" ></td>
            </tr>
            <tr>
                <td width="25" ></td>
                <td width="650" align="left">
                <?
                    echo "The Manager<br>Principal Branch<br>";
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
                <strong>Subject:  Lien of Sales Contract # <? echo $contract_no." date ".$contract_date." for ".$currency_name." $".$contract_value; ?> & open BTB L/C <? echo $currency_name." $".$max_btb_limit_value; ?>. </strong></td>
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
                In reference to the above, we are submitting herewith the above noted <strong>Sales Contract</strong> for your kind attention and request you to keep under lien against which we will open BTB LC for USD i.e. @75% of FOB value.
                </td>
                <td width="25" ></td>
            </tr>
        </table>
        <table width="794" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td colspan="3" height="15"></td>
            </tr>
            <tr>
                <td colspan="3" style="padding-left: 28px;">Details of the <strong>Sales Contract</strong> as follows:</td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">01. Sales Contract</td>
                <td width="15" >:</td>
                <td width="380"><? echo $contract_no;?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">02. Date</td>
                <td width="15" >:</td>
                <td width="380"><? echo $contract_date; ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">03. Rev no & Date</td>
                <td width="15" >:</td>
                <td width="380"><? echo $amendment_no .' & '.change_date_format($amendment_date) ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">04. <? echo ($increase_decrease==1) ? "Increased Value" : "Decreased Value" ; ?></td>
                <td width="15" >:</td>
                <td width="380"><? if($amendment_value) echo '$'.number_format($amendment_value,2); ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">05. Value in USD</td>
                <td width="15" >:</td>
                <td width="380"><? if($contract_value) echo '$'.$contract_value,2; ?></td>
            </tr>
			<tr>
                <td width="25" >&nbsp;</td>
                <td width="280">06. BTB Limit % ( <?=$max_btb_limit;?>% )</td>
                <td width="15" >:</td>
                <td width="380" ><? if($max_btb_limit_value) echo '$'.$max_btb_limit_value,2; ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">07. Buyer Name</td>
                <td width="15" >:</td>
                <td width="380"><? echo $buyer_name; ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">08. FOB Value</td>
                <td width="15" >:</td>
                <td width="380"><? if($total_attached_value) echo '$'.number_format($total_attached_value,2); ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">09. 75% BTB Limit</td>
                <td width="15" >:</td>
                <td width="380"><? if($contract_value) echo '$'.number_format(($contract_value*75)/100,2); ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">10. Tenor</td>
                <td width="15" >:</td>
                <td width="380"><? echo $payTerm; if ($tenor>0) { echo ', '.$tenor.' Days'; } ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">11. Qty</td>
                <td width="15" >:</td>
                <td width="380"><? if($total_attach_qty) echo number_format($total_attach_qty,2).' PCS'; ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">12. Shipment Date</td>
                <td width="15" >:</td>
                <td width="380"><? echo change_date_format($last_shipment_date); ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">13. Expiry Date</td>
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
                We would request your kind self to do the needful to lien the aforesaid <strong>Sales Contract</strong> and necessary action at your end.
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
                <td width="397" align="left"><strong></strong></td>
            </tr>
            <tr>
                <td style="padding-left: 28px;" width="397" align="left"><strong>Authorized Signature</strong></td>
                <td width="397" align="left"><strong></strong></td>
            </tr>
            <tr >
                <td style="padding-left: 28px; padding-top: 30px;" width="397" align="left"><strong><?=$company_name;?></strong></td>
            </tr>
        </table>
    </div>
    <!--  -->
    <?
    exit();
}

if ($action == "sales_contact_lien_letter8") // lien_letter 8
{
    //echo $data; die;
    $data = explode("**", $data);

    //export lc lien-----------------
    $company_lib=return_library_array( "select id, company_name from lib_company",'id','company_name');
    $buyer_lib=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    if($data[0]==3)
	{
		$data_array=sql_select("SELECT id, contract_no, contract_date, lien_bank, lien_date,contract_value, max_btb_limit, max_btb_limit_value, internal_file_no, contact_system_id, currency_name, beneficiary_name, buyer_name, last_shipment_date, expiry_date, bank_file_no, pay_term, tenor from com_sales_contract where id='$data[1]'");
		foreach ($data_array as $row)
		{
			$internal_file_no	= $row[csf("internal_file_no")];
			$contact_system_id  = $row[csf("contact_system_id")];
			$contract_no		= $row[csf("contract_no")];
			$max_btb_limit		= $row[csf("max_btb_limit")];
			$contract_value		= def_number_format($row[csf("contract_value")],2);
			$max_btb_limit_value= def_number_format($row[csf("max_btb_limit_value")],2);
			$sc_date			= $row[csf("contract_date")];
			$contract_date		= change_date_format($row[csf("contract_date")]);
			$last_shipment_date	= change_date_format($row[csf("last_shipment_date")]);
			$lien_bank			= $row[csf("lien_bank")];
			$lien_date			= $row[csf("lien_date")];
			$currency_name      = $currency[$row[csf("currency_name")]];
			$company_name       = strtoupper($company_lib[$row[csf("beneficiary_name")]]);
			$buyer_name         = $buyer_lib[$row[csf("buyer_name")]];
			$expiry_date        = $row[csf("expiry_date")];
			$bank_file_no 		= $row[csf("bank_file_no")];
			$payTerm 			= $pay_term[$row[csf("pay_term")]];
            $tenor 				= $row[csf("tenor")];
		}
		//echo $buyer_name;die;
		// echo "<pre>";
		// print_r($data_array);die;

		$data_array1=sql_select("SELECT wm.total_set_qnty as ratio, ci.attached_qnty, ci.attached_value 
		from wo_po_break_down wb, wo_po_details_master wm, com_sales_contract_order_info ci 
		where wb.job_id=wm.id and wb.id=ci.wo_po_break_down_id and ci.com_sales_contract_id='$data[1]' and ci.status_active='1' and ci.is_deleted='0' order by ci.id");
		foreach($data_array1 as $row1)
		{
			$order_qnty_in_pcs=$row1[csf('attached_qnty')]*$row1[csf('ratio')];
			$total_attach_qty+=$order_qnty_in_pcs;
			$total_attached_value += $row1[csf('attached_value')];
		}
	}

    $sql_comm_freight="SELECT a.com_sales_contract_id,a.wo_po_break_down_id, c.costing_per, d.comm_cost, d.freight
    from com_sales_contract_order_info a, wo_po_break_down b, wo_pre_cost_mst c, wo_pre_cost_dtls d
    where a.wo_po_break_down_id=b.id and b.job_no_mst=c.job_no and c.job_no=d.job_no and com_sales_contract_id='$data[1]'";
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

	$sql_amendment="SELECT b.id,b.amendment_no, b.amendment_date, b.amendment_value, b.value_change_by, b.last_shipment_date, b.insert_date
	from  com_sales_contract_amendment b where b.contract_id='$data[1]' order by B.id desc";
	$amendment_data_arr = sql_select($sql_amendment);
    /*echo "<pre>";
    print_r($amendment_data_arr);*/
    $amendment_no=$amendment_data_arr[0][AMENDMENT_NO];
    $amendment_date=$amendment_data_arr[0][AMENDMENT_DATE];
    $amendment_value=$amendment_data_arr[0][AMENDMENT_VALUE];
    $increase_decrease=$amendment_data_arr[0][VALUE_CHANGE_BY];
    $amendment_last_shipment_date=$amendment_data_arr[0][LAST_SHIPMENT_DATE];
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
    </style>
    <div class="a4size">
        <table width="794" cellpadding="0" cellspacing="0" border="0" >
            <br>
			<tr><td colspan="3" height="60"></td></tr>
            <tr>
                <td width="25"></td>
                <td width="650" align="left">Ref No: <?=$contact_system_id;?></td>
                <td width="25" ></td>
            </tr>
			<tr><td colspan="3" height="20"></td></tr>
            <tr>
                <td width="25"></td>
                <td width="650" align="left"><? echo date('M d, Y',strtotime($sc_date));?></td>
                <td width="25" ></td>
            </tr>
			<tr><td colspan="3" height="30"></td></tr>
            <tr>
                <td width="25"></td>
                <!-- <td width="650" align="left">To</td> -->
                <td width="25" ></td>
            </tr>
            <tr>
                <td width="25" ></td>
                <td width="650" align="left">
                <?
                    // echo "The Manager<br>Principal Branch<br>";
						echo "The Manager<br>";
						echo $bank_name."<br>";
						echo $branch_name." Branch"."<br>";
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
                <strong>Subject:  Lien of Sales Contract # <? echo $contract_no." date ".$contract_date." for ".$currency_name." $".$contract_value; ?> & open BTB L/C <? echo $currency_name." $".$max_btb_limit_value; ?>. </strong></td>
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
                In reference to the above, we are submitting herewith the above noted <strong>Sales Contract</strong> for your kind attention and request you to keep under lien against which we will open BTB LC for USD i.e. @75% of FOB value.
                </td>
                <td width="25" ></td>
            </tr>
        </table>
        <table width="794" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td colspan="3" height="15"></td>
            </tr>
            <tr>
                <td colspan="3" style="padding-left: 28px;">Details of the <strong>Sales Contract</strong> as follows:</td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">01. Sales Contract</td>
                <td width="15" >:</td>
                <td width="380"><? echo $contract_no;?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">02. Date</td>
                <td width="15" >:</td>
                <td width="380"><? echo $contract_date; ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">03. Rev no & Date</td>
                <td width="15" >:</td>
                <td width="380"><? echo $amendment_no .' & '.change_date_format($amendment_date) ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">04. <? echo ($increase_decrease==1) ? "Increased Value" : "Decreased Value" ; ?></td>
                <td width="15" >:</td>
                <td width="380"><? if($amendment_value) echo '$'.number_format($amendment_value,2); ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">05. Value in USD</td>
                <td width="15" >:</td>
                <td width="380"><? if($contract_value) echo '$'.$contract_value,2; ?></td>
            </tr>
			<tr>
                <td width="25" >&nbsp;</td>
                <td width="280">06. BTB Limit % ( <?=$max_btb_limit;?>% )</td>
                <td width="15" >:</td>
                <td width="380" ><? if($max_btb_limit_value) echo '$'.$max_btb_limit_value,2; ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">07. Buyer Name</td>
                <td width="15" >:</td>
                <td width="380"><? echo $buyer_name; ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">08. FOB Value</td>
                <td width="15" >:</td>
                <td width="380"><? if($total_attached_value) echo '$'.number_format($total_attached_value,2); ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">09. 75% BTB Limit</td>
                <td width="15" >:</td>
                <td width="380"><? if($contract_value) echo '$'.number_format(($contract_value*75)/100,2); ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">10. Tenor</td>
                <td width="15" >:</td>
                <td width="380"><? echo $payTerm; if ($tenor>0) { echo ', '.$tenor.' Days'; } ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">11. Qty</td>
                <td width="15" >:</td>
                <td width="380"><? if($total_attach_qty) echo number_format($total_attach_qty,2).' PCS'; ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">12. Shipment Date</td>
                <td width="15" >:</td>
                <td width="380"><? echo change_date_format($last_shipment_date); ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280">13. Expiry Date</td>
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
                We would request your kind self to do the needful to lien the aforesaid <strong>Sales Contract</strong> and necessary action at your end.
                <br><br>
                Thank you very much, indeed
                </td>
                <td width="25" ></td>
            </tr>
            <tr>
                <td colspan="3" height="50"></td>
            </tr>
        </table>
        <table width="794" cellpadding="0" cellspacing="0" border="0" style="margin-top: 100px;">
            
            
			<tr>
                <td style="padding-left: 28px;" width="397" align="left"><strong>-------------------------------</strong></td>
                <td width="397" align="left"><strong></strong></td>
            </tr>
			<tr>
                <td style="padding-left: 28px;" width="397" align="left"><strong>Authorized Signature</strong></td>
                <td width="397" align="left"><strong></strong></td>
            </tr>
			<tr >
                <td style="padding-left: 28px; padding-top: 30px;" width="397" align="left"><strong><?=$company_name;?></strong></td>
            </tr>
        </table>
    </div>
    <!--  -->
    <?
    exit();
}

/*
if ($action == "sales_contact_lien_letter4") // lien_letter 3
{
    // echo $data; die;
    $data = explode("**", $data);
    //export lc lien-----------------

    $company_id=$data[2];
    $country_arr = return_library_array("select id, country_name from lib_country where is_deleted=0","id","country_name");
	$nameArray=sql_select( "select id, company_name, plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$company_id");
	$adderess='';
	foreach ($nameArray as $company_add)
	{
		$company_arr[$company_add[csf("id")]]['company_name']=$company_add[csf("company_name")];
		if ($company_add[csf('plot_no')]!=''){ $adderess.= $company_add[csf('plot_no')].','; }
		if ($company_add[csf('road_no')]!=''){ $adderess.=$company_add[csf('road_no')].','; }
		if ($company_add[csf('block_no')]!=''){ $adderess.=$company_add[csf('block_no')].',';}
		if ($company_add[csf('city')]!=''){ $adderess.=$company_add[csf('city')].',';}
		if ($company_add[csf('zip_code')]!=''){ $adderess.=$company_add[csf('zip_code')].','; }
		if ($company_add[csf('country_id')]!=''){ $adderess.=$country_arr[$company_add[csf('country_id')]]; }
	}

    $sql_buyer=sql_select("select id, buyer_name, address_1, address_2 from lib_buyer where status_active=1 and is_deleted=0");
    foreach ($sql_buyer as $val) {
    	$buyer_arr[$val[csf("id")]]['buyer_name']=$val[csf("buyer_name")];
    	$buyer_arr[$val[csf("id")]]['address_1']=$val[csf("address_1")];
    	$buyer_arr[$val[csf("id")]]['address_2']=$val[csf("address_2")];
    }
    //echo '<pre>';print_r($buyer_arr);

    if ($db_type==0) $date_diff_cond="DATEDIFF(last_shipment_date,expiry_date)";
	else if ($db_type==2) $date_diff_cond="(last_shipment_date - expiry_date)";
    
    if($data[0]==3)
	{
		$data_array=sql_select("SELECT id, contract_no, contract_date, lien_bank, lien_date, contract_value, internal_file_no, contact_system_id, tolerance, currency_name, beneficiary_name, buyer_name, last_shipment_date, export_item_category, expiry_date, bank_file_no, pay_term, tenor, max_btb_limit, max_btb_limit_value, issuing_bank, trader, country_origin, remarks, port_of_discharge, $date_diff_cond as date_diff from com_sales_contract where id='$data[1]'");
		$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');		
		foreach ($data_array as $row)
		{
			$sales_contract_id  = $row[csf("id")];
			$internal_file_no	= $row[csf("internal_file_no")];
			$contact_system_id  = $row[csf("contact_system_id")];
			$contract_no		= $row[csf("contract_no")];
			$contract_value		= def_number_format($row[csf("contract_value")],2);
			$contract_btb_limit_value= def_number_format($row[csf("max_btb_limit_value")],2);
			$contract_btb_limit	= def_number_format($row[csf("max_btb_limit")],2);
			$item_category		= $export_item_category[$row[csf("export_item_category")]];
			$sc_date			= $row[csf("contract_date")];
			$contract_date		= change_date_format($row[csf("contract_date")]);
			$lien_bank			= $row[csf("lien_bank")];
			$issuing_bank		= $row[csf("issuing_bank")];
			$trader			    = $row[csf("trader")];
			$country_origin		= $row[csf("country_origin")];
			$currency_name      = $currency[$row[csf("currency_name")]];
			$company_name       = $company_arr[$row[csf("beneficiary_name")]]['company_name'];
			$buyer_name         = $buyer_arr[$row[csf("buyer_name")]]['buyer_name'];
			$address_1          = $buyer_arr[$row[csf("buyer_name")]]['address_1'];
			$address_2          = $buyer_arr[$row[csf("buyer_name")]]['address_2'];
			$currency_sign 		= $currency_sign_arr[$row[csf("currency_name")]];
			$ref				= $company_arr[$row[csf("beneficiary_name")]];
			$tolerance			= $row[csf("tolerance")];
			$remarks			= $row[csf("remarks")];
			$port_of_discharge	= $row[csf("port_of_discharge")];
			$date_diff	        = $row[csf("date_diff")];
		}
	}

	$sql_amendment=sql_select("select a.amendment_no, a.amendment_date from com_sales_contract_amendment a where a.contract_id='$sales_contract_id' and a.amendment_no>0");
	foreach ($sql_amendment as $row) {
		$amendment_no   = $row[csf("amendment_no")];
		$amendment_date = change_date_format($row[csf("amendment_date")]);
	}
	
    $sql_bank = sql_select("select a.id, a.bank_name, a.swift_code, a.branch_name, a.address, b.account_no from lib_bank a, lib_bank_account b where a.id=b.account_id and a.id='$lien_bank'");
    foreach ($sql_bank as $row1) 
    {
        $bank_name = ucwords($row1[csf("bank_name")]);
        $address = ucwords($row1[csf("address")]);
        $swift_code = $row1[csf("swift_code")];
        $account_no = $row1[csf("account_no")];
    }
    //echo $address_2;

    $is_sales=return_field_value("is_sales","com_sales_contract_order_info","com_sales_contract_id=$sales_contract_id and status_active=1","is_sales");
	//echo $is_sales.test;die;
	if($is_sales==0)
	{
		$sql = "select wb.id, ci.id as idd, wm.gmts_item_id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst, wm.style_ref_no, wm.order_uom, wm.total_set_qnty as ratio, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active 
		from wo_po_break_down wb, wo_po_details_master wm, com_sales_contract_order_info ci 
		where wb.job_id = wm.id and wb.id=ci.wo_po_break_down_id and ci.com_sales_contract_id='$sales_contract_id' and ci.status_active = '1' and ci.is_deleted = '0' order by ci.id";
	}
	else
	{
		$sql = "select wm.id, ci.id as idd, 0 as gmts_item_id, wm.job_no as po_number, sum(wb.amount) as po_total_price, sum(wb.finish_qty) as po_quantity, wm.delivery_date as shipment_date, wb.job_no_mst, wm.style_ref_no, wb.order_uom, 1 as ratio, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active 
		from fabric_sales_order_dtls wb, fabric_sales_order_mst wm, com_sales_contract_order_info ci 
		where wm.id = wb.mst_id and wb.mst_id=ci.wo_po_break_down_id and ci.com_sales_contract_id='$sales_contract_id' and ci.status_active = '1' and ci.is_deleted = '0'
		group by  wm.id, ci.id, wm.job_no, wm.delivery_date, wb.job_no_mst, wm.style_ref_no, wb.order_uom, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active 
		order by ci.id";
	}
	$nameArray=sql_select( $sql );
	$po_id_arr=array();$ac_po_arr=array();
	if($is_sales==0)
	{
		foreach($nameArray as $row)
		{
			$po_id_arr[$row[csf("id")]]=$row[csf("id")];
		}
		if(count($po_id_arr)>0)
		{
			$ac_po_sql=sql_select("select po_break_down_id, acc_po_no as acc_po_no from wo_po_acc_po_info where status_active=1 and is_deleted=0 and po_break_down_id in(".implode(",",$po_id_arr).") ");
			if(count($ac_po_sql)>0)
			{
				foreach($ac_po_sql as $row)
				{
					$ac_po_arr[$row[csf("po_break_down_id")]].=$row[csf("acc_po_no")].",";
				}
			}
			
		}
	}
	
    ?>
   
    <div style="width:900px;">
    	<table width="900" cellpadding="0" cellspacing="0" border="0">
        	<tr><td height="20">&nbsp;</td></tr>
    		<tr>
                <td width="900" style="text-align: center; text-decoration: underline; font-weight: bold; font-size: 25px;"><strong>Sales Contract</strong></td>
            </tr>
        <table>    
        <table width="900" cellpadding="0" cellspacing="0" border="0">
            <tr><td height="20"></td></tr>
            <tr>
                <td width="700">Sales Contract No. # <?= $contract_no; ?></td>
                <td width="200">Date: <?= $contract_date; ?></td>
            </tr>
            <tr>
                <td width="700">Amendment No. # <?= $amendment_no; ?></td>
                <td width="200">Date: <?= $amendment_date; ?></td>
            </tr>
        <table>    
        <table width="900" cellpadding="0" cellspacing="0" border="0" >    
            <tr><td height="20"></td></tr>
            <tr>
                <td colspan="2" width="900">This irrevocable contract made between <? echo $buyer_name." ". $address_2; ?> &  <? echo $company_name; ?> under the following terms and conditions:</td>                
            </tr>
            <tr><td height="20"></td></tr>
            <tr>
                <td width="300">Name and Address of Consignee/Notify&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td> 
                <td width="600"><?= $buyer_name." ". $address_1; ?></td>               
            </tr>
            <tr><td height="20"></td></tr>
            <tr>
                <td width="300">Name & address of Consignees Bank&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td> 
                <td width="600"><?= $issuing_bank; ?></td>               
            </tr>
            <tr><td height="20"></td></tr>
            <tr>
                <td width="300">Name and Address of Supplier/Seller&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: </td>
                <td width="600"><?= $company_name.','.$adderess; ?></td>                
            </tr>
            <tr><td height="20"></td></tr>
            <tr>
                <td width="300" valign="top">Name of Supplier / Shipper Bank Details&nbsp;&nbsp;&nbsp;:</td>
                <td width="600">
                	<?= $bank_name; ?><br>
                	<?= $address; ?><br>
                	A/C No. <?= $account_no; ?><br>
                	SWIFT:<?= $swift_code; ?>
                </td>
            </tr>            
        </table>
        <table width="900"><tr><td style="font-size: 20px;"><strong>Order Details:</strong></td></tr></table>
        <table width="900" class="rpt_table" border="1" rules="all" cellpadding="0" cellspacing="0">
        	<tr>
    			<th width="100">Order Number</th>
    			<? 
				if (count($ac_po_arr)>0) 
				{
					?>
    				<th width="100">Acc.PO No.</th>
    				<? 
				} 
				?>	
    			<th width="100">Style Ref</th>
    			<th width="100">Gmts. Item</th>
    			<th width="80">Order Qty</th>
    			<th width="80">Order Value</th>
    			<th width="80">Attached Qty</th>
    			<th width="50">UOM</th>
    			<th width="50">Rate</th>
    			<th>Attached Value</th>
        	</tr>
        	<?
            $i=1;
            
            foreach ($nameArray as $selectResult)
            {
            	?>
	        	<tr>
	    			<td width="100"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
                    <? 
					if (count($ac_po_arr)>0) 
					{
						?>
						<td width="100"><p><? echo chop($ac_po_arr[$selectResult[csf('id')]],","); ?></p></td>
						<? 
					} 
					?>
	    			<td width="100"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
	    			<td width="100"><p>
                        <?
                            $gmts_item='';
                            $gmts_item_id=explode(",",$selectResult[csf('gmts_item_id')]);
                            foreach($gmts_item_id as $item_id)
                            {
                                if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
                            }
                            echo $gmts_item;
                        ?>
                    </p></td>
	    			<td width="80" align="right"><? echo $selectResult[csf('po_quantity')]; ?></td>
	    			<td width="80" align="right"><? echo number_format($selectResult[csf('po_total_price')],2); ?></td>
	    			<td width="80" align="right"><? echo $selectResult[csf('attached_qnty')]; ?></td>
	    			<td width="50" align="center"><? echo $unit_of_measurement[$selectResult[csf('order_uom')]]; ?></td>
	    			<td width="50" align="right"><? echo number_format($selectResult[csf('attached_rate')],2); ?></td>
	    			<td width="80" align="right"><? echo number_format($selectResult[csf('attached_value')],2); ?></td>
	        	</tr>
	        	<?
	        	$i++;
	        }	
	        ?>
        </table>

        <table width="900" cellpadding="0" cellspacing="0" border="0" >
        	<tr><td height="20"></td></tr>           
            <tr>
                <td width="900" colspan="2">All Purchase orders are subject to <?= $buyer_name; ?> Terms and Conditions.<br>Sales Contract will replace by Export Contract/Export L/C.</td>                
            </tr>
            <tr><td height="20"></td></tr>
            <tr>
                <td width="300" >Tolerance&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td width="600"><?= $tolerance; ?>% +/- in quantity and value are acceptable.</td>
            </tr>
            <tr><td height="20"></td></tr>
            <tr>
                <td width="300" valign="top">Transport Documents&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td width="600">For Sea, Air, Sea-Air Shipment – Transport documents will be issued by nominated forwarder to the order of negotiated bank marked freight Collect/Prepaid.</td>
            </tr>
            <tr><td height="20"></td></tr>
            <tr>
                <td width="300">Mode of Shipments&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td width="600">FOB/FCA Chittagong, Bangladesh / Dhaka, Bangladesh.</td>
            </tr>
            <tr><td height="20"></td></tr>
            <tr>
                <td width="300">Insurance&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td width="600">To be covered by the Buyer.</td>
            </tr>
            <tr><td height="20"></td></tr>
            <tr>
                <td width="300">Date of Shipment&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td width="600">As per the date mentioned in the PO.</td>
            </tr>
            <tr><td height="20"></td></tr>
            <tr>
                <td width="300">Expiry of the Contract Letter&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td width="600">After <?= $date_diff; ?> days from the date of Shipment.</td>
            </tr>
            <tr><td height="20"></td></tr>
            <tr>
                <td width="300">Payment Terms.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td width="600"><?= $remarks; ?></td>
            </tr>
            <tr><td height="20"></td></tr>
            <tr>
                <td width="300">Country of Origin&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td width="600">Bangladesh</td>
            </tr>
            <tr><td height="20"></td></tr>
            <tr>
                <td width="300">Partial & Trans Shipment&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td width="600">Allowed</td>
            </tr>
            <tr><td height="20"></td></tr>
            <tr>
                <td width="300">Country of Destination&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td width="600"><?= $country_origin; ?></td>
            </tr>
            <tr><td height="20"></td></tr>
            <tr>
                <td width="300">Port of Discharge&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td width="600"><?= $port_of_discharge; ?></td>
            </tr> 
            <tr><td height="20"></td></tr>
            <tr>
                <td width="300" valign="top">Required Documents&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td width="600">
                	<ul>
					    <li>Original Invoice</li>
					    <li>Original Packing list</li>
					    <li>Original Bills of Lading / FCR’s/AWB</li>
					    <li>Certificate of Origin/ GSP Form A/REX Declaration.</li>
					</ul> 
                </td>
            </tr>
        </table>    
        <table width="900" cellpadding="0" cellspacing="0" border="0" > 
            <tr><td height="50"></td></tr>
            <tr>
            	<td valign="top">For and on behalf of</td>
                <td>For and on behalf of</td>
            </tr>
            <tr>
                <td width="600" valign="top"><?= $trader; ?></td>
                <td width="300"><?= $company_name; ?></td>
            </tr>
        </table>    
    </div>
    <!--  -->
    <?
    exit();
}
*/

if ($action == "sales_contact_lien_letter4") // lien_letter 3
{
    $data = explode("**", $data);
    $company_id=$data[2];
    $country_arr = return_library_array("select id, country_name from lib_country where is_deleted=0","id","country_name");
    $bank_arr = return_library_array("SELECT id, bank_name from lib_bank where is_deleted=0","id","bank_name");
	$nameArray=sql_select( "select id, company_name, plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$company_id");
	$adderess='';
	foreach ($nameArray as $company_add)
	{
		$company_arr[$company_add[csf("id")]]['company_name']=$company_add[csf("company_name")];
		if ($company_add[csf('plot_no')]!=''){ $adderess.= $company_add[csf('plot_no')].', '; }
		if ($company_add[csf('road_no')]!=''){ $adderess.=$company_add[csf('road_no')].', '; }
		if ($company_add[csf('block_no')]!=''){ $adderess.=$company_add[csf('block_no')].', ';}
		if ($company_add[csf('city')]!=''){ $adderess.=$company_add[csf('city')].', ';}
		if ($company_add[csf('zip_code')]!=''){ $adderess.=$company_add[csf('zip_code')].', '; }
		if ($company_add[csf('country_id')]!=''){ $adderess.=$country_arr[$company_add[csf('country_id')]]; }
	}

    $sql_buyer=sql_select("select id, buyer_name, address_1, address_2 from lib_buyer where status_active=1 and is_deleted=0");
    foreach ($sql_buyer as $val) {
    	$buyer_arr[$val[csf("id")]]['buyer_name']=$val[csf("buyer_name")];
    	$buyer_arr[$val[csf("id")]]['address_1']=$val[csf("address_1")];
    	$buyer_arr[$val[csf("id")]]['address_2']=$val[csf("address_2")];
    }
    //echo '<pre>';print_r($buyer_arr);

    if ($db_type==0) $date_diff_cond="DATEDIFF(last_shipment_date,expiry_date)";
	else if ($db_type==2) $date_diff_cond="(last_shipment_date - expiry_date)";
    
    if($data[0]==3)
	{
		$data_array=sql_select("SELECT id, contract_no, contract_date, lien_bank, lien_date, contract_value, internal_file_no, contact_system_id, tolerance, currency_name, beneficiary_name, buyer_name, last_shipment_date, export_item_category, expiry_date, bank_file_no, pay_term, tenor, max_btb_limit, max_btb_limit_value, issuing_bank, trader, country_origin, remarks, port_of_discharge, convertible_to_lc, $date_diff_cond as date_diff from com_sales_contract where id='$data[1]'");
		$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');		
		foreach ($data_array as $row)
		{
			$sales_contract_id  = $row[csf("id")];
			$internal_file_no	= $row[csf("internal_file_no")];
			$contact_system_id  = $row[csf("contact_system_id")];
			$contract_no		= str_replace("HnM","H&M",$row[csf("contract_no")]);
			$contract_value		= def_number_format($row[csf("contract_value")],2);
			$contract_btb_limit_value= def_number_format($row[csf("max_btb_limit_value")],2);
			$contract_btb_limit	= def_number_format($row[csf("max_btb_limit")],2);
			$item_category		= $export_item_category[$row[csf("export_item_category")]];
			$sc_date			= $row[csf("contract_date")];
			$contract_date		= change_date_format($row[csf("contract_date")]);
			$lien_bank			= $row[csf("lien_bank")];
			$issuing_bank		= $row[csf("issuing_bank")];
			$trader			    = $row[csf("trader")];
			$country_origin		= $row[csf("country_origin")];
			$currency_name      = $currency[$row[csf("currency_name")]];
			$company_name       = $company_arr[$row[csf("beneficiary_name")]]['company_name'];
			$buyer_name         = str_replace("HnM","H&M",$buyer_arr[$row[csf("buyer_name")]]['buyer_name']);
			$address_1          = $buyer_arr[$row[csf("buyer_name")]]['address_1'];
			$address_2          = $buyer_arr[$row[csf("buyer_name")]]['address_2'];
			$currency_sign 		= $currency_sign_arr[$row[csf("currency_name")]];
			$ref				= $company_arr[$row[csf("beneficiary_name")]];
			$tolerance			= $row[csf("tolerance")];
			$remarks			= $row[csf("remarks")];
			$port_of_discharge	= $row[csf("port_of_discharge")];
			$date_diff	        = $row[csf("date_diff")];
			$convertible	    = $row[csf("convertible_to_lc")];
			$last_shipment_date	= change_date_format($row[csf("last_shipment_date")]); 
		}
	}

	$sql_amendment=sql_select("SELECT * from ( SELECT a.amendment_no, a.amendment_date, a.value_change_by, a.amendment_qnty, a.amendment_value from com_sales_contract_amendment a where a.contract_id='$sales_contract_id' and a.amendment_no>0 and a.status_active=1 order by id desc) where rownum<2");

	foreach ($sql_amendment as $row) {
		$amendment_no   = $row["AMENDMENT_NO"];
		$amendment_date = change_date_format($row["AMENDMENT_DATE"]);
		$value_change_by = $increase_decrease[$row["VALUE_CHANGE_BY"]];
		$amendment_qnty = $row["AMENDMENT_QNTY"];
		$amendment_value = $row["AMENDMENT_VALUE"];
	}
	
    $sql_bank = sql_select("select a.id, a.bank_name, a.swift_code, a.branch_name, a.address, b.account_no from lib_bank a, lib_bank_account b where a.id=b.account_id and a.id='$lien_bank'");
    foreach ($sql_bank as $row1) 
    {
        $bank_name = $row1[csf("bank_name")];
        $address = $row1[csf("address")];
        $swift_code = $row1[csf("swift_code")];
        //$account_no = $row1[csf("account_no")];
    }

   //echo "select b.account_no from lib_bank a, lib_bank_account b where a.id=b.account_id and a.id='$lien_bank' and b.company_id=$company_id";
    $account_no = return_field_value("b.account_no as account_no", "lib_bank a, lib_bank_account b", "a.id=b.account_id and a.id='$lien_bank' and b.company_id=$company_id","account_no");

    $is_sales=return_field_value("is_sales","com_sales_contract_order_info","com_sales_contract_id=$sales_contract_id and status_active=1","is_sales");
	//echo $is_sales.test;die;
	if($is_sales==0)
	{
		$sql = "select wb.id, ci.id as idd, wm.gmts_item_id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst, wm.style_ref_no, wm.order_uom, wm.total_set_qnty as ratio, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active 
		from wo_po_break_down wb, wo_po_details_master wm, com_sales_contract_order_info ci 
		where wb.job_id = wm.id and wb.id=ci.wo_po_break_down_id and ci.com_sales_contract_id='$sales_contract_id' and ci.status_active = '1' and ci.is_deleted = '0' order by ci.id";
	}
	else
	{
		$sql = "select wm.id, ci.id as idd, 0 as gmts_item_id, wm.job_no as po_number, sum(wb.amount) as po_total_price, sum(wb.finish_qty) as po_quantity, wm.delivery_date as shipment_date, wb.job_no_mst, wm.style_ref_no, wb.order_uom, 1 as ratio, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active 
		from fabric_sales_order_dtls wb, fabric_sales_order_mst wm, com_sales_contract_order_info ci 
		where wm.id = wb.mst_id and wb.mst_id=ci.wo_po_break_down_id and ci.com_sales_contract_id='$sales_contract_id' and ci.status_active = '1' and ci.is_deleted = '0'
		group by  wm.id, ci.id, wm.job_no, wm.delivery_date, wb.job_no_mst, wm.style_ref_no, wb.order_uom, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active 
		order by ci.id";
	}
	$nameArray=sql_select( $sql );
	$po_id_arr=array();$ac_po_arr=array();
	if($is_sales==0)
	{
		foreach($nameArray as $row)
		{
			$po_id_arr[$row[csf("id")]]=$row[csf("id")];
		}
		if(count($po_id_arr)>0)
		{
			$ac_po_sql=sql_select("select po_break_down_id, acc_po_no as acc_po_no from wo_po_acc_po_info where status_active=1 and is_deleted=0 and po_break_down_id in(".implode(",",$po_id_arr).") ");
			if(count($ac_po_sql)>0)
			{
				foreach($ac_po_sql as $row)
				{
					$ac_po_arr[$row[csf("po_break_down_id")]].=$row[csf("acc_po_no")].",";
				}
			}
			
		}
	}
	if (count($ac_po_arr)>0) $tot_colspan=10; else $tot_colspan=9;
    ?>
   
    <table width="900" cellpadding="0" cellspacing="0" border="0">
    	<thead>
        	<tr><th colspan="<?=$tot_colspan;?>" style="padding-top:150px;">&nbsp;</th></tr>
        </thead>
        <tbody>
        	<tr><td height="20" colspan="<?=$tot_colspan;?>">&nbsp;</td></tr>
            <tr>
                <td colspan="<?=$tot_colspan;?>" style="text-align: center; text-decoration: underline; font-weight: bold; font-size: 25px;"><strong>Sales Contract</strong></td>
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <? 
                if(count($ac_po_arr)>0) $sales_col=7; else $sales_col=6;
                ?>
                <td colspan="<?=$sales_col;?>">Sales Contract No. # <?= $contract_no; ?></td>
                <td colspan="3">Date: <?= $contract_date; ?></td>
            </tr>
            <tr>
                <td colspan="<?=$sales_col;?>">Amendment No. # <?= $amendment_no; ?></td>
                <td colspan="3">Date: <?= $amendment_date; ?></td>
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="<?=$tot_colspan;?>">This irrevocable contract made between <? echo $address_2; ?> &  <? echo $company_name; ?> under the following terms and conditions:</td>                
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="3">Name and Address of Consignee/Notify&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td> 
                <td colspan="<?=$sales_col;?>"><?= $address_1; ?></td>               
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="3">Name & address of Consignees Bank&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td> 
                <td colspan="<?=$sales_col;?>"><?= $bank_arr[$issuing_bank]; ?></td>               
            </tr>
            <tr><td height="20"></td></tr>
            <tr>
                <td colspan="3">Name and Address of Supplier/Seller&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: </td>
                <td colspan="<?=$sales_col;?>"><? echo $company_name.', '.rtrim($adderess,', '); ?></td>                
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="3" valign="top">Name of Supplier / Shipper Bank Details&nbsp;&nbsp;&nbsp;:</td>
                <td colspan="<?=$sales_col;?>">
                    <?= $bank_name; ?><br>
                    <?= $address; ?><br>
                    A/C No. <?= $account_no; ?><br>
                    SWIFT:<?= $swift_code; ?>
                </td>
            </tr>            
            <tr><td style="font-size:20px;" colspan="<?=$tot_colspan;?>"><strong>Order Details:</strong></td></tr>
            <tr style="">
                <td width="110" style="font-weight:bold; text-align:center; border:1px solid black">Order Number</td>
                <? 
                if (count($ac_po_arr)>0) 
                {
					$col_span=6;
                    ?>
                    <td width="110" style="font-weight:bold; text-align:center; border:1px solid black">Acc.PO No.</td>
                    <? 
                }
				else
				{
					$col_span=5;
				}
                ?>	
                <td width="110" style="font-weight:bold; text-align:center; border:1px solid black">Style Ref</td>
                <td width="110" style="font-weight:bold; text-align:center; border:1px solid black">Gmts. Item</td>
                <td width="80" style="font-weight:bold; text-align:center; border:1px solid black">Order Qty</td>
                <td width="90" style="font-weight:bold; text-align:center; border:1px solid black">Order Value</td>
                <td width="80" style="font-weight:bold; text-align:center; border:1px solid black">Confirm Qty</td>
                <td width="40" style="font-weight:bold; text-align:center; border:1px solid black">UOM</td>
                <td width="70" style="font-weight:bold; text-align:center; border:1px solid black">Rate</td>
                <td style="font-weight:bold; text-align:center; border:1px solid black">Confirm Value</td>
            </tr>
            <?
            $i=1;
            $tot_attached_qnty=0;
            $tot_attached_value=0;
            foreach ($nameArray as $selectResult)
            {
                ?>
                <tr>
                    <td width="110" style="border:1px solid black; word-break:break-all;"><? echo $selectResult[csf('po_number')]; ?></td>
                    <? 
                    if (count($ac_po_arr)>0) 
                    {
                        ?>
                        <td width="110" style="border:1px solid black; word-break:break-all;"><? echo chop($ac_po_arr[$selectResult[csf('id')]],","); ?></td>
                        <? 
                    } 
                    ?>
                    <td width="110" style=" border:1px solid black; word-break:break-all;"><? echo $selectResult[csf('style_ref_no')]; ?></td>
                    <td width="110" style=" border:1px solid black; word-break:break-all;">
                        <?
                            $gmts_item='';
                            $gmts_item_id=explode(",",$selectResult[csf('gmts_item_id')]);
                            foreach($gmts_item_id as $item_id)
                            {
                                if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
                            }
                            echo $gmts_item;
                        ?>
                    </td>
                    <td width="80" align="right" style="border:1px solid black"><? echo $selectResult[csf('po_quantity')]; ?></td>
                    <td width="90" align="right" style="border:1px solid black"><? echo number_format($selectResult[csf('po_total_price')],2); ?></td>
                    <td width="80" align="right" style="border:1px solid black"><? echo $selectResult[csf('attached_qnty')]; ?></td>
                    <td width="40" align="center" style="border:1px solid black"><? echo $unit_of_measurement[$selectResult[csf('order_uom')]]; ?></td>
                    <td width="70" align="center" style="border:1px solid black"><? echo $currency_sign.' '.number_format($selectResult[csf('attached_rate')],2); ?></td>
                    <td align="right" style="border:1px solid black"><? echo $currency_sign.' '.number_format($selectResult[csf('attached_value')],2); ?></td>
                </tr>
                <?
                $i++;
                $tot_attached_qnty+=$selectResult[csf('attached_qnty')];
                $tot_attached_value+=$selectResult[csf('attached_value')];
            }	
            ?>
            <tr style="font-weight: bold;">
                <td  align="right" colspan="<?= $col_span; ?>" style="font-size:18px; border:1px solid black">Total</td>
                <td width="80" align="right" style="font-size:18px; border:1px solid black"><? echo $tot_attached_qnty; ?></td>
                <td width="40" align="center" style="font-size:18px; border:1px solid black">Pcs</td>
                <td width="70" align="center" style="font-size:18px; border:1px solid black">&nbsp;</td>
                <td align="right" style="font-size:18px; border:1px solid black"><? echo $currency_sign.' '.number_format($tot_attached_value,2); ?></td>
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>     
			<?
				if(count($sql_amendment)>0)
				{
					?>
						<tr>
							<td colspan="<?=$tot_colspan;?>">
								<table cellpadding="0" cellspacing="0" border="1">
									<tr>
										<td align="center" width="150"><strong><?=$value_change_by;?> Qty</strong></td>
										<td align="center" width="150"><strong><?=$value_change_by;?> Value</strong></</td>
									</tr>
									<tr>
										<td align="right" ><strong><?=$amendment_qnty;?> Pcs</strong></</td>
										<td align="right"><strong><?=$currency_sign.' '.$amendment_value;?></strong></</td>
									</tr>
								</table>
							</td>                
						</tr>
						<tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>  
					<?
				}
			?>      
   
            <tr>
                <td colspan="<?=$tot_colspan;?>">All Purchase orders are subject to <?= $buyer_name; ?> Terms and Conditions.<br>
				<?if($convertible!=2){?>Sales Contract will replace by Export Contract/Export L/C.<?}?></td>                
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="3">Tolerance&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td colspan="<?=$sales_col;?>"><?= $tolerance; ?>% +/- in quantity and value are acceptable.</td>
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="3" valign="top">Transport Documents&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td colspan="<?=$sales_col;?>">For Sea, Air, Sea-Air Shipment – Transport documents will be issued by nominated forwarder to the order of negotiated bank marked freight Collect/Prepaid.</td>
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="3">Mode of Shipments&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td colspan="<?=$sales_col;?>">FOB/FCA Chittagong, Bangladesh / Dhaka, Bangladesh.</td>
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="3">Insurance&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td colspan="<?=$sales_col;?>">To be covered by the Buyer.</td>
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="3">Date of Shipment&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td colspan="<?=$sales_col;?>"><? echo $last_shipment_date; ?></td>
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="3">Date of Expiry&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td colspan="<?=$sales_col;?>">After <?= $date_diff; ?> days from the date of Shipment.</td>
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="3">Payment Terms.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td  colspan="<?=$sales_col;?>"><?= $remarks; ?></td>
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="3">Country of Origin&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td colspan="<?=$sales_col;?>">Bangladesh.</td>
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="3">Partial & Trans Shipment&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td colspan="<?=$sales_col;?>">Allowed.</td>
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="3">Country of Destination&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td colspan="<?=$sales_col;?>"><?= $country_origin; ?></td>
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="3">Port of Discharge&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td colspan="<?=$sales_col;?>"><?= $port_of_discharge; ?></td>
            </tr> 
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="3" valign="top">Required Documents&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td colspan="<?=$sales_col;?>">
                    <ul>
                        <li>Original Invoice.</li>
                        <li>Original Packing list.</li>
                        <li>Original Bills of Lading / FCR’s/AWB.</li>
                        <li>Certificate of Origin/ GSP Form A/REX Declaration.</li>
                    </ul> 
                </td>
            </tr>
            <tr><td height="50" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td valign="top" colspan="<?=$sales_col;?>">For and on behalf of</td>
                <td colspan="3">For and on behalf of</td>
            </tr>
            <tr>
                <td colspan="<?=$sales_col;?>" valign="top"><?= $trader; ?></td>
                <td colspan="3"><?= $company_name; ?></td>
            </tr>
        </tbody>
        <tfoot>
        	<tr><th colspan="<?=$tot_colspan;?>" style="padding-bottom:95px;">&nbsp;</th></tr>
        </tfoot>
            
    </table>    
    <?
    exit();
}

if ($action == "sales_contact_lien_letter11") 
{
    $data = explode("**", $data);
	$system_id=$data[0];
    $company_id=$data[1];
    $country_arr = return_library_array("select id, country_name from lib_country where is_deleted=0","id","country_name");
    $bank_arr = return_library_array("SELECT id, bank_name from lib_bank where is_deleted=0","id","bank_name");
	$nameArray=sql_select( "select id, company_name, plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website, contact_no from lib_company where id=$company_id");
	$adderess=$contact_no='';
	foreach ($nameArray as $company_add)
	{
		$company_arr[$company_add[csf("id")]]['company_name']=$company_add[csf("company_name")];
		if ($company_add[csf('plot_no')]!=''){ $adderess.= $company_add[csf('plot_no')].', '; }
		if ($company_add[csf('road_no')]!=''){ $adderess.=$company_add[csf('road_no')].', '; }
		if ($company_add[csf('block_no')]!=''){ $adderess.=$company_add[csf('block_no')].', ';}
		if ($company_add[csf('city')]!=''){ $adderess.=$company_add[csf('city')].', ';}
		if ($company_add[csf('zip_code')]!=''){ $adderess.=$company_add[csf('zip_code')].', '; }
		if ($company_add[csf('country_id')]!=''){ $adderess.=$country_arr[$company_add[csf('country_id')]]; }
		$contact_no=$company_add[csf('contact_no')];
	}

    $sql_buyer=sql_select("select id, buyer_name, address_1, address_2, address_3, address_4, buyer_email, exporters_reference, web_site from lib_buyer where status_active=1 and is_deleted=0");
    foreach ($sql_buyer as $val) {
    	$buyer_arr[$val[csf("id")]]['buyer_name']=$val[csf("buyer_name")];
    	$buyer_arr[$val[csf("id")]]['address_1']=$val[csf("address_1")];
    	$buyer_arr[$val[csf("id")]]['address_2']=$val[csf("address_2")];
		$buyer_arr[$val[csf("id")]]['address_3']=$val[csf("address_3")];
		$buyer_arr[$val[csf("id")]]['address_4']=$val[csf("address_4")];
		$buyer_arr[$val[csf("id")]]['buyer_email']=$val[csf("buyer_email")];
		$buyer_arr[$val[csf("id")]]['exporters_reference']=$val[csf("exporters_reference")];
		$buyer_arr[$val[csf("id")]]['web_site']=$val[csf("web_site")];
    }
    //echo '<pre>';print_r($buyer_arr);

    if ($db_type==0) $date_diff_cond="DATEDIFF(last_shipment_date,expiry_date)";
	else if ($db_type==2) $date_diff_cond="(last_shipment_date - expiry_date)";
    
	$data_array=sql_select("SELECT id, contract_no, contract_date, lien_bank, shipping_line, lien_date, contract_value, internal_file_no, contact_system_id, tolerance, currency_name, beneficiary_name, buyer_name, last_shipment_date, export_item_category, expiry_date, bank_file_no, pay_term, tenor, max_btb_limit, max_btb_limit_value, issuing_bank, trader, country_origin, remarks, port_of_discharge, convertible_to_lc, $date_diff_cond as date_diff from com_sales_contract where id=$system_id");
	$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');		
	foreach ($data_array as $row)
	{
		$sales_contract_id  = $row[csf("id")];
		$internal_file_no	= $row[csf("internal_file_no")];
		$contact_system_id  = $row[csf("contact_system_id")];
		$contract_no		= $row[csf("contract_no")];
		$contract_value		= def_number_format($row[csf("contract_value")],2);
		$contract_btb_limit_value= def_number_format($row[csf("max_btb_limit_value")],2);
		$contract_btb_limit	     = def_number_format($row[csf("max_btb_limit")],2);
		$item_category		= $export_item_category[$row[csf("export_item_category")]];
		$sc_date			= $row[csf("contract_date")];
		$contract_date		= change_date_format($row[csf("contract_date")]);
		$lien_bank			= $row[csf("lien_bank")];
		$issuing_bank		= $row[csf("issuing_bank")];
		$trader			    = $row[csf("trader")];
		$country_origin		= $row[csf("country_origin")];
		$currency_name      = $currency[$row[csf("currency_name")]];
		$currency_id        =$row[csf("currency_name")];
		$company_name       = $company_arr[$row[csf("beneficiary_name")]]['company_name'];
		$buyer_name         = $buyer_arr[$row[csf("buyer_name")]]['buyer_name'];
		$address_1          = $buyer_arr[$row[csf("buyer_name")]]['address_1'];
		$address_2          = $buyer_arr[$row[csf("buyer_name")]]['address_2'];
		$address_3          = $buyer_arr[$row[csf("buyer_name")]]['address_3'];
		$address_4          = $buyer_arr[$row[csf("buyer_name")]]['address_4'];
		$buyer_email        = $buyer_arr[$row[csf("buyer_name")]]['buyer_email'];
		$buyer_exporters_reference= $buyer_arr[$row[csf("buyer_name")]]['exporters_reference'];
		$web_site           = $buyer_arr[$row[csf("buyer_name")]]['web_site'];
		$currency_sign 		= $currency_sign_arr[$row[csf("currency_name")]];
		$ref				= $company_arr[$row[csf("beneficiary_name")]];
		$tolerance			= $row[csf("tolerance")];
		$remarks			= $row[csf("remarks")];
		$shipping_line      =$row[csf("shipping_line")];
		$port_of_discharge	= $row[csf("port_of_discharge")];
		$date_diff	        = $row[csf("date_diff")];
		$convertible	    = $row[csf("convertible_to_lc")];
		$last_shipment_date	= change_date_format($row[csf("last_shipment_date")]); 
		$payTerm=$pay_term[$row[csf("pay_term")]];
	}

	$sql_amendment=sql_select("SELECT a.amendment_no, a.amendment_date from com_sales_contract_amendment a where a.contract_id='$sales_contract_id' and a.amendment_no>0 and a.status_active=1 order by id");

    $sql_bank = sql_select("select a.id, a.bank_name, a.swift_code, a.branch_name, a.address, a.bank_code, a.remark from lib_bank a where a.id='$issuing_bank'");
    foreach ($sql_bank as $row1) 
    {
        $bank_name = $row1[csf("bank_name")];
		$branch_name = $row1[csf("branch_name")];
        $address = $row1[csf("address")];
        $swift_code = $row1[csf("swift_code")];		
		$bank_code = $row1[csf("bank_code")];
		$bank_remark = $row1[csf("remark")];
        //$account_no = $row1[csf("account_no")];
    }

	$sql_bank_lien = sql_select("select a.id, a.bank_name, a.swift_code, a.branch_name, a.address, a.bank_code from lib_bank a where a.id='$lien_bank'");
    foreach ($sql_bank_lien as $row1) 
    {
        $lien_bank_name = $row1[csf("bank_name")];
		$lien_branch_name = $row1[csf("branch_name")];
        $lien_address = $row1[csf("address")];
        $lien_swift_code = $row1[csf("swift_code")];		
    }

    $account_no = return_field_value("b.account_no as account_no", "lib_bank a, lib_bank_account b", "a.id=b.account_id and a.id='$lien_bank' and b.company_id=$company_id","account_no");

	$sql = "SELECT a.ID, a.PO_NUMBER, a.PO_QUANTITY, a.pub_shipment_date as SHIPMENT_DATE, c.id as IDD, b.total_set_qnty as PACK, b.order_uom as UOM, b.STYLE_REF_NO, b.STYLE_DESCRIPTION, c.ATTACHED_QNTY, c.ATTACHED_RATE, c.ATTACHED_VALUE, c.HS_CODE, c.FABRIC_DESCRIPTION
	from wo_po_break_down a, wo_po_details_master b, com_sales_contract_order_info c 
	where a.job_id = b.id and a.id=c.wo_po_break_down_id and c.com_sales_contract_id='$sales_contract_id' and a.is_deleted=0 and b.is_deleted=0 and c.status_active in (1) and c.is_deleted in (0) order by a.pub_shipment_date";
	
	$sql_res=sql_select( $sql );
	
	$tot_colspan=12;
    ?>
   
    <table width="1050" cellpadding="0" cellspacing="0" border="0">
    	<thead>
        	<tr><th colspan="<?=$tot_colspan;?>"><img src="../../<? echo return_field_value("header_location","template_pad","company_id =".$company_id." and is_deleted=0 and status_active=1"); ?>" style="width:794px;height: 100px;" /></th></tr>
        </thead>
        <tbody>
        	<tr><td height="20" colspan="<?=$tot_colspan;?>">&nbsp;</td></tr>
            <tr>
                <td colspan="<?=$tot_colspan;?>" style="text-align: center; text-decoration: underline; font-weight: bold; font-size: 25px;"><strong>CONTRACT</strong></td>
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
				<? $sales_col=6; ?>
                <td colspan="<?=$sales_col;?>">Contract Ref. No.:&nbsp;<?= $contract_no; ?></td>
                <td colspan="3">Issue Date: <?= $contract_date; ?></td>
            </tr>
			<?
				foreach($sql_amendment as $row)
				{
					?>
					<tr>
						<td colspan="<?=$sales_col;?>"></td>
						<td colspan="3">Revise <?= $row[csf('amendment_no')]; ?>: <?= change_date_format($row[csf('amendment_date')]); ?></td>
					</tr>
					<?
				}
			?>
			<tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
				<td height="20" colspan="<?=$tot_colspan;?>">THIS IRREVOCABLE CONTRACT MADE BETWEEN <? echo $buyer_name; ?>&nbsp;<? echo $buyer_exporters_reference; ?> & <? echo $company_name; ?>, <? echo $adderess; ?> UNDER THE FOLLOWING TERMS & CONDITIONS:							
				</td>
			</tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="3" style="vertical-align: top;">Name and Address of Consignee&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td> 
                <td colspan="<?=$sales_col;?>"><?= $buyer_name; ?> <br> <?= $address_1; ?></td>
            </tr>
			<?
			if ($address_2 != "")
			{
				?>
				<tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
				<tr>
					<td colspan="3" style="vertical-align: top;">Notify Party&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td> 
					<td colspan="<?=$sales_col;?>"><?= $address_2; ?></td>
				</tr>
				<?
			}
			if ($address_3 != "")
			{
				?>
				<tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
				<tr>
					<td colspan="3" style="vertical-align: top;">Delivery Address&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td> 
					<td colspan="<?=$sales_col;?>"><?= $address_3; ?></td>
				</tr>
				<?
			}
			?>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="3" style="vertical-align: top;">Name and address of Consignee's Bank&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td> 
                <td colspan="<?=$sales_col;?>">
                    <?= $bank_name; ?><br>
					<?= $branch_name; ?><br>
                    <?= $address; ?><br>
                    SWIFT:&nbsp;<?= $swift_code; ?><br>
					<? echo $bank_remark; ?>
                </td>               
            </tr>
			<?
			if ($address_4 != "")
			{
				?>
				<tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
				<tr>
					<td colspan="3" style="vertical-align: top;">For Documentary Collections&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td> 
					<td colspan="<?=$sales_col;?>"><?= $address_4; ?></td>
				</tr>
				<?
			}
			if ($buyer_email != "")
			{
				?>
				<tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
				<tr>
					<td colspan="3" style="vertical-align: top;">Accounting Department&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td> 
					<td colspan="<?=$sales_col;?>"><?= $buyer_email; ?></td>
				</tr>
				<?
			}
			if ($web_site != "")
			{
				?>
				<tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
				<tr>
					<td colspan="3" style="vertical-align: top;">Logistics Department&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td> 
					<td colspan="<?=$sales_col;?>"><?= $web_site; ?></td>
				</tr>
				<?
			}
			?>                     
            <tr><td style="font-size:20px;" colspan="<?=$tot_colspan;?>"><strong>Order Details:</strong></td></tr>
            <tr style="">
				<td width="30" style="font-weight:bold; text-align:center; border:1px solid black">SL</td>
                <td width="90" style="font-weight:bold; text-align:center; border:1px solid black">HS Code</td>
				<td width="100" style="font-weight:bold; text-align:center; border:1px solid black">Order Number</td>
                <td width="100" style="font-weight:bold; text-align:center; border:1px solid black">Style/Item</td>
                <td width="100" style="font-weight:bold; text-align:center; border:1px solid black">Description</td>
				<td width="120" style="font-weight:bold; text-align:center; border:1px solid black">Fabrication</td>
                <td width="80" style="font-weight:bold; text-align:center; border:1px solid black">Qty in Pk</td>
                <td width="30" style="font-weight:bold; text-align:center; border:1px solid black">Pk of</td>
                <td width="80" style="font-weight:bold; text-align:center; border:1px solid black">Qty in Pcs</td>
                <td width="120" style="font-weight:bold; text-align:center; border:1px solid black">Unit price/PK</td>
                <td width="100" style="font-weight:bold; text-align:center; border:1px solid black">Total Value(in US$)</td>
                <td style="font-weight:bold; text-align:center; border:1px solid black">Shipment Date</td>
            </tr>
            <?
            $i=1;
            $tot_attached_qnty=0;
            $tot_attached_value=0;
			$tot_unit_price=$tot_qty_in_pcs=0;
            foreach ($sql_res as $row)
            {
				$qty_in_pcs=$row['ATTACHED_QNTY']*$row['PACK'];
				$unit_price=$row['ATTACHED_VALUE']/$row['ATTACHED_QNTY'];
                ?>
                <tr>
                    <td width="30" style="border:1px solid black; word-break:break-all;"><? echo $i; ?></td>
                    <td width="90" style="border:1px solid black; word-break:break-all;"><? echo $row['HS_CODE']; ?></td>
					<td width="100" style="border:1px solid black; word-break:break-all;"><? echo $row['PO_NUMBER']; ?></td>
					<td width="100" style="border:1px solid black; word-break:break-all;"><? echo $row['STYLE_REF_NO']; ?></td>
					<td width="100" style="border:1px solid black; word-break:break-all;"><? echo $row['STYLE_DESCRIPTION']; ?></td>
					<td width="120" style="border:1px solid black; word-break:break-all;"><? echo $row['FABRIC_DESCRIPTION']; ?></td>
                    <td width="80" align="right" style="border:1px solid black"><? echo $row['ATTACHED_QNTY']; ?></td>
                    <td width="30" align="right" style="border:1px solid black"><? echo $row['PACK']; ?></td>
                    <td width="80" align="right" style="border:1px solid black"><? echo $qty_in_pcs; ?></td>
                    <td width="120" align="center" style="border:1px solid black"><? echo $currency_sign.''.number_format($unit_price,4).' /'.$unit_of_measurement[$row['UOM']]; ?></td>
                    <td width="100" align="right" style="border:1px solid black"><? echo $currency_sign.' '.number_format($row['ATTACHED_VALUE'],2); ?></td>
                    <td align="center" style="border:1px solid black"><? echo change_date_format($row['SHIPMENT_DATE']); ?></td>
                </tr>
                <?
                $i++;
                $tot_attached_qnty+=$row['ATTACHED_QNTY'];
                $tot_attached_value+=$row['ATTACHED_VALUE'];
				$tot_unit_price+=$unit_price;
				$tot_qty_in_pcs+=$qty_in_pcs;
            }	
            ?>
            <tr style="font-weight: bold;">
                <td  align="right" colspan="6" style="font-size:18px; border:1px solid black">Total</td>
                <td width="80" align="right" style="font-size:18px; border:1px solid black"><? echo $tot_attached_qnty; ?></td>
				<td width="30" align="center" style="font-size:18px; border:1px solid black">PK</td>
                <td width="80" align="right" style="font-size:18px; border:1px solid black"><? echo $tot_qty_in_pcs; ?></td>
                <td width="120" align="center" style="font-size:18px; border:1px solid black">Pcs</td>
                <td align="right" style="font-size:18px; border:1px solid black"><? echo $currency_sign.' '.number_format($tot_attached_value,2); ?></td>
				<td style="font-size:18px; border:1px solid black">&nbsp;</td>
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>     
			<?
			$dcurrency_arr=array(1=>'Paisa',2=>'CENTS',3=>'CENTS',);
			$currency_arr=array(1=>'TK',2=>'US Dollar',3=>'US Dollar',);
			$mcurrency = $currency_arr[$currency_id];
			$dcurrency = $dcurrency_arr[$currency_id];
			?>
            <tr>
                <td colspan="<?=$tot_colspan;?>"><strong>In Words: <? echo number_to_words(number_format($tot_attached_value,2,".",""),$mcurrency,$dcurrency); ?>&nbsp;Only</strong></td>
            </tr>
			<tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>

            <tr>
                <td colspan="3" style="vertical-align: top;">Tolerance&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td colspan="<?=$sales_col;?>"><?= $tolerance; ?>% +/- in Quantity and Value Acceptable. Garment Description, Garments Quantity, Unit Price and Total Value can be amended as per order sheet.</td>
            </tr>
			<tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
			<tr>
                <td colspan="3" style="vertical-align: top;">Quota Category&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td colspan="<?=$sales_col;?>">Non Quota</td>
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="3" valign="top">Transport Documents&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td colspan="<?=$sales_col;?>">For Sea shipments- Transport documents will be issued by <? echo $shipping_line; ?>. To the order of negotiating bank.Marked freight collect.</td>
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="3" valign="top">Mode of Shipments&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td colspan="<?=$sales_col;?>">For Air shipments- House Air Way Bill will be issued by nominated by the buyer & marked  freight collect/ freight prepaid.<br>FOB Chittagong by SEA/ FOB Dhaka Airport by Air.</td>
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="3">Insurance&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td colspan="<?=$sales_col;?>">To be covered by buyer upon receipt of the cargo by the nominated forwarders.</td>
            </tr>
			<tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
			<tr>
                <td colspan="3">Port of Discharge&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td colspan="<?=$sales_col;?>"><? echo $port_of_discharge; ?></td>
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="3">Latest Date of Shipment&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td colspan="<?=$sales_col;?>"><? echo $last_shipment_date; ?></td>
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="3" valign="top">Expiry of the contract&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td colspan="<?=$sales_col;?>">Pls see the different delivery date for each order.After <?= $date_diff; ?> days from the date of Shipment.</td>
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="3">Payment Terms&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td  colspan="<?=$sales_col;?>"><?= $payTerm; ?></td>
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="3" valign="top">Name of Suppliers Bank&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td colspan="<?=$sales_col;?>">
					<?= $lien_bank_name; ?><br>
					<?= $lien_branch_name; ?><br>
                    <?= $lien_address; ?>
				</td>
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="3" valign="top">Account Numbers&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td colspan="<?=$sales_col;?>">Acc:&nbsp;<? echo $account_no; ?>,&nbsp;Swift:&nbsp;<? echo $lien_swift_code; ?></td>
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="3" valign="top">All Details&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td colspan="<?=$sales_col;?>">As per Order Sheet and Terms and Conditions of Purchase - As per <?= $remarks; ?> Standard purchase conditions.</td>
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
			<tr><td height="20" colspan="<?=$tot_colspan;?>"><strong>Required documents to be submitted in Negotiating Bank in Bangladesh</strong></td></tr>            
            <tr>
                <td colspan="<?=$tot_colspan;?>">
                    <ul style="list-style-type: decimal;">
                        <li>Original signed invoice & packing list covering gross weight/net weight & other informations.</li>
                        <li>Original certificate of Orgin/ GSP Form-A.</li>
                        <li>Full set clean on board Bill of Loding or Full set Clean on Bord multimodal transport documents issued to negotiating bank endorsed to the order of <? echo $bank_name; ?> Marked freight Collect.</li>
                    </ul> 
                </td>
            </tr>
            <tr><td height="50" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td valign="top" colspan="<?=$sales_col;?>" style="text-decoration: overline;">For and on behalf of</td>
                <td colspan="3" style="text-decoration: overline;">For and on behalf of</td>
            </tr>
            <tr>
                <td colspan="<?=$sales_col;?>" valign="top"><?= $buyer_name; ?></td>
                <td colspan="3"><?= $company_name; ?></td>
            </tr>
			<tr>
                <td colspan="<?=$sales_col;?>" valign="top"><?= $address_1; ?></td>
                <td colspan="3"><?= $adderess; ?></td>
            </tr>
			<tr>
                <td colspan="<?=$sales_col;?>" valign="top"><? ?></td>
                <td colspan="3"><?= $contact_no; ?></td>
            </tr>
        </tbody>
        <tfoot>
        	<tr><th colspan="<?=$tot_colspan;?>" style="padding-bottom:95px;">&nbsp;</th></tr>
        </tfoot>
            
    </table>    
    <?
    exit();
}
if ($action == "sales_contact_lien_letter12") {
    // echo 'sales_contact_lien_letter12';die;
	$data = explode("**", $data);
	$system_id = $data[0];
	$company_id = $data[1];
	$country_arr = return_library_array("select id, country_name from lib_country where is_deleted=0", "id", "country_name");
	$bank_arr = return_library_array("SELECT id, bank_name,contact_no from lib_bank where is_deleted=0", "id", "bank_name");
	$buyer_lib=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name'); 
	$nameArray = sql_select("select id, company_name, plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website, contact_no from lib_company where id=$company_id");
	$adderess = $contact_no = '';
	foreach ($nameArray as $company_add) {
		$company_arr[$company_add[csf("id")]]['company_name'] = $company_add[csf("company_name")];
		if ($company_add[csf('plot_no')] != '') {
			$adderess .= $company_add[csf('plot_no')] . ', ';
		}
		if ($company_add[csf('road_no')] != '') {
			$adderess .= $company_add[csf('road_no')] . ', ';
		}
		if ($company_add[csf('block_no')] != '') {
			$adderess .= $company_add[csf('block_no')] . ', ';
		}
		if ($company_add[csf('city')] != '') {
			$adderess .= $company_add[csf('city')] . ', ';
		}
		if ($company_add[csf('zip_code')] != '') {
			$adderess .= $company_add[csf('zip_code')] . ', ';
		}
		if ($company_add[csf('country_id')] != '') {
			$adderess .= $country_arr[$company_add[csf('country_id')]];
		}
		$contact_no = $company_add[csf('contact_no')];
		$email=$company_add[csf("email")];
	}

	$sql_buyer = sql_select("select id, buyer_name, address_1, address_2, address_3, address_4, buyer_email, exporters_reference, web_site from lib_buyer where status_active=1 and is_deleted=0");
	foreach ($sql_buyer as $val) {
		$buyer_arr[$val[csf("id")]]['buyer_name'] = $val[csf("buyer_name")];
		$buyer_arr[$val[csf("id")]]['address_1'] = $val[csf("address_1")];
		$buyer_arr[$val[csf("id")]]['address_2'] = $val[csf("address_2")];
		$buyer_arr[$val[csf("id")]]['address_3'] = $val[csf("address_3")];
		$buyer_arr[$val[csf("id")]]['address_4'] = $val[csf("address_4")];
		$buyer_arr[$val[csf("id")]]['buyer_email'] = $val[csf("buyer_email")];
		$buyer_arr[$val[csf("id")]]['exporters_reference'] = $val[csf("exporters_reference")];
		$buyer_arr[$val[csf("id")]]['web_site'] = $val[csf("web_site")];
	}
	//echo '<pre>';print_r($buyer_arr);

	if ($db_type == 0) $date_diff_cond = "DATEDIFF(last_shipment_date,expiry_date)";
	else if ($db_type == 2) $date_diff_cond = "(last_shipment_date - expiry_date)";

	//master query
	$data_array = sql_select("SELECT id, contract_no, contract_date, lien_bank, shipping_line, lien_date, contract_value, internal_file_no, contact_system_id, tolerance,applicant_name, currency_name, beneficiary_name, buyer_name, last_shipment_date, export_item_category, expiry_date, bank_file_no,notifying_party, tenor, max_btb_limit, max_btb_limit_value, issuing_bank,shipping_mode,port_of_loading,trader,pay_term, country_origin, remarks, port_of_discharge, convertible_to_lc,inco_term,inco_term_place,foreign_comn,
	local_comn,claim_adjustment,tolerance, $date_diff_cond as date_diff from com_sales_contract where id=$system_id");

	$currency_sign_arr = array(1 => '৳', 2 => '$', 3 => '€', 4 => '€', 5 => '$', 6 => '£', 7 => '¥');
	foreach ($data_array as $row) 
	{
		$shipping_mode = $shipment_mode[$row[csf('shipping_mode')]];
		$port_of_loading = $row[csf("port_of_loading")];
		$sc_applicant_name = $buyer_lib[$row[csf("applicant_name")]];
		$port_of_discharge = $row[csf("port_of_discharge")];
		$last_shipment_date = change_date_format($row[csf("last_shipment_date")]);
		$expiry_date = change_date_format($row[csf("expiry_date")]);
		$contract_date= change_date_format($row[csf("contract_date")]);
		$notifying_party = $row[csf("notifying_party")];
		$notifying_party_address  = $buyer_arr[$row[csf("notifying_party")]]['address_1'];
        $incoterm = $incoterm[$row[csf("inco_term")]];
		$incoterm_place = $row[csf("inco_term_place")];
		$sales_contract_id = $row[csf("id")];
		$internal_file_no = $row[csf("internal_file_no")];
		$contact_system_id = $row[csf("contact_system_id")];
		$contract_no = $row[csf("contract_no")];
		$contract_value	= def_number_format($row[csf("contract_value")], 2);
		$sc_date = $row[csf("contract_date")];
		$lien_bank = $row[csf("lien_bank")];
		$issuing_bank_id = $row[csf("issuing_bank")];
		$issuing_bank = $bank_arr[$row[csf("issuing_bank")]];
		$country_origin	= $row[csf("country_origin")];
		$buyer_name = $buyer_arr[$row[csf("buyer_name")]]['buyer_name'];
		$address_1 = $buyer_arr[$row[csf("applicant_name")]]['address_1'];
		$payTerm = $pay_term[$row[csf("pay_term")]];
		$foreign_comn = $row[csf("foreign_comn")];
        $local_comn = $row[csf("local_comn")];
        $claim_adjustment = $row[csf("claim_adjustment")];
		$currency_sign = $currency_sign_arr[$row[csf("currency_name")]];
		$currency_name = $currency[$row[csf("currency_name")]];
		$tolerance = $row[csf("tolerance")];
	}

	$sql_bank = sql_select("select a.id, a.bank_name, a.swift_code, a.branch_name,a.contact_no, a.address, a.bank_code, a.remark from lib_bank a where a.id='$issuing_bank_id'");
	
	foreach ($sql_bank as $row1) {
		$bank_name =$bank_arr[$row1[csf("bank_name")]];
		$branch_name = $row1[csf("branch_name")];
		$address = $row1[csf("address")];
		$swift_code = $row1[csf("swift_code")];
		$bank_code = $row1[csf("bank_code")];
		$bank_remark = $row1[csf("remark")];
		$bank_contact_no = $row1[csf("contact_no")];
	
	
	}
	$sql_bank_lien = sql_select("select a.id, a.bank_name, a.swift_code, a.branch_name, a.address, a.bank_code from lib_bank a where a.id='$lien_bank'");
	foreach ($sql_bank_lien as $row1) {
		$lien_bank_name = $row1[csf("bank_name")];
		$lien_branch_name = $row1[csf("branch_name")];
		$lien_address = $row1[csf("address")];
		$lien_swift_code = $row1[csf("swift_code")];
		
	}
	$nameArray=sql_select( "select id, account_id, account_type, account_no, currency, loan_limit, loan_type, company_id ,status_active from lib_bank_account where id='$lien_bank'" );
	
	foreach ($nameArray as $inf)
	{
    $account_no=$inf[csf('account_no')];
	}
	$sql = "SELECT a.ID, a.PO_NUMBER, a.PO_QUANTITY, a.pub_shipment_date as SHIPMENT_DATE, c.id as IDD, b.total_set_qnty as PACK, b.order_uom as UOM, b.STYLE_REF_NO, b.STYLE_DESCRIPTION, c.ATTACHED_QNTY, c.ATTACHED_RATE, c.ATTACHED_VALUE, c.HS_CODE, c.FABRIC_DESCRIPTION
	from wo_po_break_down a, wo_po_details_master b, com_sales_contract_order_info c 
	where a.job_id = b.id and a.id=c.wo_po_break_down_id and c.com_sales_contract_id='$sales_contract_id' and a.is_deleted=0 and b.is_deleted=0 and c.status_active in (1) and c.is_deleted in (0) order by a.pub_shipment_date";
    $sql_res = sql_select($sql);
    ?>
    <style>
		@media print {  
			.text-header{
				position:relative;
				top:0px;
				left:0px;
				width:100%;
				color:#CCC;
				text-align:center;
				/* &:after {
					counter-increment: page;
					content: "Page " counter(page) " of " counter(pages);
				} */
				/* font-size:50px; */
			}
			thead {
				display: table-row-group;
			}
			tfoot {
				display: table-row-group;
			} 
		}

		
	</style>
 
<body>
	<div class="row text-header">
	   <h1>SALES CONTRACT</h1>
	</div>

	<div class="row text-header4"></div>
	<div style="width:900px;" class="body-text"> 
		<div style="width:900px;display:flex;">
			<div style="width:400px;border:1px solid black; font-size: 13px; ">
				<div style="width:400px;height:130px;border-bottom:1px solid black; font-size: 14px;">
					<strong style="text-decoration: underline;">APPLICANT/CONSIGNEE:- </strong>
					<br>
					<strong> <? echo $sc_applicant_name  ; ?></strong>
					<br>
					<? echo $address_1   ; ?> <br> 
				</div>
				<div style="width:400px;height:170px;border-left:1px solid black;font-size: 14px;border-left:none">
					<strong style="text-decoration: underline;">SHIPPER / EXPORTER:</strong>
					<br>
					<strong><? echo $company_add[csf("company_name")];?></strong>
					<br>
					<?echo $adderess?>
					<br>
					PHONE: <? echo $contact_no;?>
					<br>
					E-mail: <? echo $email;?>
				</div>
			</div>
			<div style="height:300px;width:300px;border-top:1px solid black; font-size: 14px;">
				<div style="height:35px;border-bottom:1px solid black">
					<strong>SALES CONTACT NO.:</strong>
					<br>
					<? echo $contract_no;?> &nbsp;
					&nbsp; &nbsp;
					&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;REV
				</div>
				<div style="height:35px;border-bottom:1px solid black;">
					<strong>DATE OF EXPIRY:</strong>
					<br>
					<? echo $expiry_date  ;?> AT BANGLADESH
				</div>
				<div style=" height:100px;border-bottom: 1px solid black; word-break: break-all;">
					<strong style="text-decoration: underline;">DELIVERY ADDRESS:-</strong>
					<br>
					<strong><?php echo $sc_applicant_name ;?></strong>
					<br>
					<? echo $address_1;?>
				</div>
				<div style="height:130px;">
					<strong style="text-decoration: underline;">SHIPPER'S BANK: </strong>
					<br>
					<? echo $lien_bank_name;?>
					<br>
					<?echo $lien_branch_name; ?>
					<br>
					<?php echo $lien_address;?>

					<br>
					<strong>A/C NO</strong>:<?  echo $account_no ;?>
					<br>
					<strong>SWIFT</strong>: <?echo $lien_swift_code ;?>
				</div>
			</div>
			<div style="width:200px;border:1px solid black; font-size: 14px;">
				<div style="height:35px;border-bottom:1px solid; font-size: 14px;">
					<strong> DATE:</strong> <? echo $contract_date;?>
				</div>
				<div style="height:35px;border-bottom:1px solid;   font-size: 14px;">
					<strong>DELIVERY DATE:</strong>
					<br>
					<? echo $last_shipment_date ;?>
				</div>
				<div style="height:40px;border-bottom:1px solid black; font-size: 14px;">
					<strong>MODE OF SHIPMENT: </strong>
					<br>
					<? echo $shipping_mode;?>
				</div>
				<div style="height:59px;border-bottom:1px solid black; font-size: 14px;">
					<strong >COUNTRY OF ORIGIN:</strong>
					<br>
					BANGLADESH
				</div>
				<div style="height:57px;border-bottom:1px solid black; font-size: 14px;">
					<strong >TERMS OF SHIPMENT:
					</strong>
					<br>
					<? echo $incoterm;?>
					<? echo $incoterm_place;?>
				</div>
				<div style="border-bottom:1px solid black" >
					<strong >TERMS OF PAYMENTS:</strong>
					<br>
					<? echo $payTerm   ;?>
					
				</div>
				    <div>
					 TT Payment
					</div>
			</div>
		</div>
		<div style="display:flex;width:900px;height:130px; font-size: 14px;">
				<div style="width:400px;height:130px; border-left:1px solid">
					<strong style="text-decoration: underline;"> NOTIFY:-</strong><br>
					<strong><?
					$notyfy_arr=explode(",",$notifying_party);
					foreach($notyfy_arr as $row){
					if(isset($buyer_lib[$row]))
					$notyfay.= $buyer_lib[$row].",";
					}
					echo rtrim($notyfay,",");
					?> </strong>
					<br>
					<? echo $notifying_party_address;?>
				</div>
				<div style="width:300px;height:130px;border:1px solid black;border-right:none;border-bottom:none">
						<strong style="text-decoration: underline;"> BUYER'S BANK: </strong>
						<br><? echo $issuing_bank;?>
						<br> <? echo $address;?>
				</div>
			<div style="width:200px;height:130px;border-right:1px solid black;border-left:none ">
				<br>
					PHONE:
					<br>
					<?  echo $bank_contact_no;?>
					<br>
					SWIFT:
					<br>
					<?echo $swift_code;?>	
			</div>
		</div>
		<div style=" width: 900px;height:30px ;border:1px solid black;display:flex;font-size:14px;border-bottom:none">
				<div style="width: 400px;height:30px ;border-right:1px solid black;border-bottom:none">
					<strong>PORT OF LOADING</strong>: <? echo 	$port_of_loading	;?>
				</div>
				<div style="width: 500px;height:30px ;border-bottom:none">
					<strong>DESTINATION PORT</strong>:<?echo $port_of_discharge ;?>
				</div>
		</div>
		<? 
		$sql_amendment=sql_select("SELECT a.amendment_no, a.amendment_date, a.value_change_by, a.amendment_qnty, a.amendment_value from com_sales_contract_amendment a where a.contract_id='$sales_contract_id' and a.amendment_no>0");
				foreach ($sql_amendment as $row) {
					$amendment_no = $row[csf("amendment_no")];
					$amendment_date = change_date_format($row[csf("amendment_date")]);
					$value_change_by = $increase_decrease[$row[csf("value_change_by")]];
					$amendment_qnty = $row[csf("amendment_qnty")];
					$amendment_value = $row[csf("amendment_value")];
				}
		$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
		$is_sales=return_field_value("is_sales","com_sales_contract_order_info","com_sales_contract_id=$sales_contract_id and status_active=1","is_sales");
		//echo $is_sales.test;die;
		if($is_sales==0)
		{
			$sql = "SELECT wb.id, ci.id as idd, wm.gmts_item_id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.shipment_date as shipment_date, wb.job_no_mst,wm.product_dept, wm.style_ref_no, wm.order_uom, wm.total_set_qnty as ratio, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active,wm.STYLE_DESCRIPTION, wb.po_number, sb.COLOR_NUMBER_ID,sb.ITEM_NUMBER_ID,sb.ARTICLE_NUMBER, sb.ORDER_QUANTITY as ORDER_QUANTITY
			from wo_po_break_down wb, wo_po_details_master wm, com_sales_contract_order_info ci, wo_po_color_size_breakdown sb
			where wb.job_id = wm.id and   wb.id = sb.PO_BREAK_DOWN_ID and sb.po_break_down_id=ci.wo_po_break_down_id and ci.com_sales_contract_id='$sales_contract_id' and ci.status_active = '1' and ci.is_deleted = '0' and wb.STATUS_ACTIVE=1 and  wb.IS_DELETED=0 and wm.STATUS_ACTIVE=1 and wm.IS_DELETED=0 and sb.STATUS_ACTIVE=1 and  sb.IS_DELETED=0  order by ci.id";
		}
		else
		{
			$sql = "SELECT wm.id, ci.id as idd, 0 as gmts_item_id, wm.job_no as po_number, sum(wb.amount) as po_total_price, sum(wb.finish_qty) as po_quantity, wm.delivery_date as shipment_date, wb.job_no_mst, wm.style_ref_no, wb.order_uom, 1 as ratio, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active 
			from fabric_sales_order_dtls wb, fabric_sales_order_mst wm, com_sales_contract_order_info ci 
			where wm.id = wb.mst_id and wb.mst_id=ci.wo_po_break_down_id and ci.com_sales_contract_id='$sales_contract_id' and ci.status_active = '1' and ci.is_deleted = '0'
			order by ci.id";
		}
		// echo $sql;die;
		//echo $sql;
		$myArr=array();
		$nameArray=sql_select( $sql );
		$Item_DataArr = array();
		$Item_DataArr2 = array();
		foreach($nameArray as $row)
		{
			//$Item_DataArr2[$row['PO_NUMBER']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']] += $row['ORDER_QUANTITY'];
			$Item_DataArr[$row['PO_NUMBER']][$row['COLOR_NUMBER_ID']][$row['ITEM_NUMBER_ID']]['ITEM_NUMBER_ID'] = $row['ITEM_NUMBER_ID'];
			$Item_DataArr[$row['PO_NUMBER']][$row['COLOR_NUMBER_ID']][$row['ITEM_NUMBER_ID']]['STYLE_DESCRIPTION'] = $row['STYLE_DESCRIPTION'];
			$Item_DataArr[$row['PO_NUMBER']][$row['COLOR_NUMBER_ID']][$row['ITEM_NUMBER_ID']]['PO_NUMBER'] = $row['PO_NUMBER'];
			$Item_DataArr[$row['PO_NUMBER']][$row['COLOR_NUMBER_ID']][$row['ITEM_NUMBER_ID']]['COLOR_NUMBER_ID'] = $row['COLOR_NUMBER_ID'];
			if($row['ARTICLE_NUMBER'] !="no article"){
				$Item_DataArr[$row['PO_NUMBER']][$row['COLOR_NUMBER_ID']][$row['ITEM_NUMBER_ID']]['ARTICLE_NUMBER'] .= $row['ARTICLE_NUMBER'].",";
			}
			$Item_DataArr[$row['PO_NUMBER']][$row['COLOR_NUMBER_ID']][$row['ITEM_NUMBER_ID']]['ORDER_UOM'] = $row['ORDER_UOM'];
			$Item_DataArr[$row['PO_NUMBER']][$row['COLOR_NUMBER_ID']][$row['ITEM_NUMBER_ID']]['ATTACHED_QNTY'] = $row['ATTACHED_QNTY'];
			$Item_DataArr[$row['PO_NUMBER']][$row['COLOR_NUMBER_ID']][$row['ITEM_NUMBER_ID']]['ATTACHED_VALUE'] = $row['ATTACHED_VALUE'];
			$Item_DataArr[$row['PO_NUMBER']][$row['COLOR_NUMBER_ID']][$row['ITEM_NUMBER_ID']]['ATTACHED_RATE'] = $row['ATTACHED_RATE'];
			$Item_DataArr[$row['PO_NUMBER']][$row['COLOR_NUMBER_ID']][$row['ITEM_NUMBER_ID']]['ORDER_QUANTITY']+= $row['ORDER_QUANTITY'];
			$Item_DataArr[$row['PO_NUMBER']][$row['COLOR_NUMBER_ID']][$row['ITEM_NUMBER_ID']]['SHIPMENT_DATE'] = $row['SHIPMENT_DATE'];
				
		}

		// echo "<pre>";
		// print_r($Item_DataArr2);die; 
		?> 
		<table class="footer_info_Mh body-text" border="1" style="width: 900px;border-collapse:collapse;">
			<thead>
				<tr>
					<th width="90">SHIPPING MARKS & NUMBER</th>
					<th width="90">DESCRIPTION OF GOODS</th>
					<th width="90">PO NUMBER</th>
					<th width="90">Color</th>
					<th width="90">ITEM NUMBER</th>
					<th width="90">ART. NO.</th>
					<th width="90" colspan="2">QUANTITY (PCS)</th>
					<th width="90">U/PRICE IN US$/PC</th>
					<th width="90">TOTAL AMOUNT IN US$ </th>
				</tr>
				<tr>
					<th width="90">As Advised by Buyer </th>
					<th colspan="8"></th>
					<th width="90"> FOB CHITTAGONG.</th>
				</tr>
			</thead>
			<tbody>
			<?
			$total_Attached_Quantity = 0;
			$total_Attached_Value = 0;
			$i=2;
			$page=2;
			$counter = 0; // Counter to track the number of rows printed
			foreach($Item_DataArr as $po_id=>$po_data)
			{
		      	foreach($po_data as $color_id => $color_data)
			       {
			    	foreach($color_data as $item_id=>$row)
				    {
						
						$counter++;
						?> 
						<tr>
						<td width="90"style="word-wrap: break-word;word-break: break-all;"><? 
						$shipmentDate = $row["SHIPMENT_DATE"];	// Adding 20 days to the shipment date
                        $newShipmentDate = date('Y-m-d', strtotime($shipmentDate . ' +20 days'));// Updating the date in the $row array
					    $row["SHIPMENT_DATE"] = $newShipmentDate;
						echo change_date_format($newShipmentDate);
						 ?></td>
						<td width="90" style="word-wrap: break-word;word-break: break-all;"><?= $row["STYLE_DESCRIPTION"] ?></td>
						<td width="90" style="word-wrap: break-word;word-break: break-all;"><?= $row["PO_NUMBER"] ?></td>
						<td width="90" style="word-wrap: break-word;word-break: break-all;"><?= $color_arr[$row["COLOR_NUMBER_ID"]] ?></td>
						<td width="90" style="word-wrap: break-word;word-break: break-all;"><?= $garments_item[$row["ITEM_NUMBER_ID"]] ?></td>
						<td width="90" style="word-wrap: break-word;word-break: break-all;"><?php
							if ($row["ARTICLE_NUMBER"] == "no article") {
								echo "";
							} else {
								echo $row['ARTICLE_NUMBER'];
							}
							?>
						</td>
						<td width="90">
						<?
						if($row["ORDER_UOM"]== "58")
						{
							echo $order_qnty = $row["ORDER_QUANTITY"]/2;
						}
						else
						{
							echo $order_qnty = $row["ORDER_QUANTITY"];
						}
						?></td>
						<td width="45"><?= $unit_of_measurement[$row["ORDER_UOM"]] ?></td>
						<td width="45"><?= $currency_sign . " " . number_format($row["ATTACHED_RATE"],2) ?></td>
						<td width="90"><?
						if($row["ORDER_UOM"]== "58")
						{
							$Attached_Value=$row["ATTACHED_VALUE"]/2;
							 $currency_sign . " " .$Attached_Value ;
							 echo $currency_sign." ".$order_qnty*number_format($row["ATTACHED_RATE"],2) ;
						}
						else
						{
							$Attached_Value=$order_qnty*number_format($row["ATTACHED_RATE"],2) ;
							 echo $currency_sign . " " .$Attached_Value  ;
						} ?></td>
					</tr>
					<? 
					$total_Attached_Quantity +=$order_qnty;
					$total_Attached_Value += $Attached_Value;
					}
			    }
		    }
			?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="8"></th>
					<th align="right">Total= </th>
					<th style="text-align:right;"> <?echo $currency_sign." ". $total_Attached_Value; ?></th>
				</tr>
				<tr>
					<th colspan="5" rowspan="3">Tolerance:&nbsp;<?echo $tolerance ;?>%+/-In Quantity or Amount</th>
					<td colspan="4" style="text-align:left;">Less-1,&nbsp;<?=$foreign_comn?>% &nbsp;BONUS</td>
					<td align="right">
					<? 
					$bonus= ($total_Attached_Value*$foreign_comn)/100;
					echo $currency_sign.' '.$bonus;
					?>
					</td>
				</tr>
				<tr>
					<td  colspan="4"  style="text-align: left;">Less-2,&nbsp;<?=$local_comn?>% &nbsp;PFR</td>
					<td align="right">
					<? 
					$pfr_value=($total_Attached_Value*$local_comn)/100;
					echo $currency_sign.' '.$pfr_value;
					?>
					</td>
				</tr>
					<tr>
					<td colspan="4" style="text-align: left;">Less-3,&nbsp;<?=$claim_adjustment?>% &nbsp;DFR</td>
					<td align="right">
					<? $dfr_value=($total_Attached_Value*$claim_adjustment)/100;
					echo $currency_sign.' '.$dfr_value;
					?>
					</td>
				</tr>
				<tr>
					<th colspan="6"  style="text-align: right;">Total:</th>
					<th><?echo $total_Attached_Quantity;?></th>
					<th>PC</th>
					<th align="right">Total=</th>
					<th align="right">
						<?
						echo  $currency_name.''.$currency_sign.' '.($total_Attached_Value-($bonus+$dfr_value+$pfr_value));	
						?>
					</th>	
				</tr>
				<tr>
					<th colspan="9" align="right">ADD PREVIOUS AMOUNT=</th>
					<th align="right"><? echo  $currency_name.''.$currency_sign.'0.00'?></th>
				</tr>
				<tr>
					<th colspan="9" align="right">NEW AMOUNT AFTER AMENDMENT=</th>
					<th align="right"><?
					if($amendment_no>0)
					{
					echo $currency_sign.' '.$contract_value	;
					}
					else
					{
						//echo $contract_value+$totalAttachedValue;
						//echo $total_Attached_Value;
						echo $currency_name.''.$currency_sign.' '.($total_Attached_Value-($bonus+$dfr_value+$pfr_value));	
					}
					?></th>
				</tr>
				<tr>
					<td colspan="10" align="left"><strong> TOTAL VALUE IN WORDS (USD):</strong><?echo  number_to_words($total_Attached_Value-($bonus+$dfr_value+$pfr_value));	?></td>
				</tr>
			</tfoot>
		</table>
		 
	</div>
	<div style="display: flex; width:900px; margin-top:10px">
		<div style="width:600px">
			<p style="text-decoration: underline;">REQURIED DOCUMENTS:</p>	                                     
			01. Commercial invoice: 2 original+ 1 copies <br><br>											
			02. Packing list: 1 original+ 2 copies <br><br>		
			03. B/L: 3/3 Original Bill of Lading made out to the order of Negotiating Bank and	<br>
			Endorse to Consignee's Bank Notify Applicant marked Freight Collect
		</div>
		<!-- <div style=" width:300px;text-align:center">
			<br>
			Authorized Signed 
			<br><br>For and on behalf <br> <strong><? echo $sc_applicant_name;?></strong>
		</div> -->
	</div>
	

	<table style="margin-top: 80px; margin-left:70px;width:800px;">
	        <tr>
                <td valign="top" width="550" style="text-decoration: overline;">Authorized Signature </td>
                <td colspan="3" width="550"  style="text-decoration: overline;" >Authorized Signature</td>
            </tr>
			<tr>
			<td valign="top" width="550"  > 	For and on behalf of </td>
                <td colspan="3"  width="550"  > For and on behalf of</td>

			</tr>
            <tr>
                <td  valign="top" width="550"  ><?= $sc_applicant_name ?></td>
                <td colspan="3"  width="700"><?= $company_add[csf("company_name")]; ?></td>
            </tr>
			
			<tr>
                <td  valign="top" width="550"  ><?= $address_1 ?></td>
                <td colspan="3"  width="700"><?= $adderess ?></td>
            </tr>

			
			
	</table>
<?
}
if ($action == "sales_contact_lien_letter13") // print B23
{
   // echo "hello";
    $data = explode("**", $data);
	//echo $data; die;
	//print_r($data);
    $company_lib=return_library_array( "select id, company_name from lib_company",'id','company_name');
    $buyer_lib=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$user_lib=return_library_array( "select id, USER_NAME from USER_PASSWD",'id','USER_NAME');
    if($data[0]==3)
	{
		$data_array=sql_select("SELECT id, contract_no, contract_date, lien_bank, lien_date, contract_value, internal_file_no, contact_system_id, currency_name, beneficiary_name, buyer_name, last_shipment_date, expiry_date, bank_file_no, pay_term, tenor,inserted_by from com_sales_contract where id='$data[1]'");
	// echo "SELECT id, contract_no, contract_date, lien_bank, lien_date, contract_value, internal_file_no, contact_system_id, currency_name, beneficiary_name, buyer_name, last_shipment_date, expiry_date, bank_file_no, pay_term, tenor from com_sales_contract where id='$data[1]'";
		foreach ($data_array as $row)
		{
			$internal_file_no	= $row[csf("internal_file_no")];
			$contact_system_id  = $row[csf("contact_system_id")];
			$contract_no		= $row[csf("contract_no")];
			$contract_value		= def_number_format($row[csf("contract_value")],2);
			$sc_date			= $row[csf("contract_date")];
			$contract_date		= change_date_format($row[csf("contract_date")]);
			$last_shipment_date	= change_date_format($row[csf("last_shipment_date")]);
			$lien_bank			= $row[csf("lien_bank")];
			$lien_date			= $row[csf("lien_date")];
			$currency_name      = $currency[$row[csf("currency_name")]];
			$company_name       = strtoupper($company_lib[$row[csf("beneficiary_name")]]);
			$buyer_name         = $buyer_lib[$row[csf("buyer_name")]];
			$expiry_date        = $row[csf("expiry_date")];
			$bank_file_no 		= $row[csf("bank_file_no")];
			$payTerm 			= $pay_term[$row[csf("pay_term")]];
            $tenor 				= $row[csf("tenor")];
			$inserted_by 		= $user_lib[$row[csf("inserted_by")]];
			$contact_system_id  =$row[csf("contact_system_id")];
		}
		//echo $buyer_name;die;
		// echo "<pre>";
		// print_r($data_array);die;

		$data_array1=sql_select("SELECT wm.total_set_qnty as ratio, ci.attached_qnty, ci.attached_value 
		from wo_po_break_down wb, wo_po_details_master wm, com_sales_contract_order_info ci 
		where wb.job_id=wm.id and wb.id=ci.wo_po_break_down_id and ci.com_sales_contract_id='$data[1]' and ci.status_active='1' and ci.is_deleted='0' order by ci.id");
		foreach($data_array1 as $row1)
		{
			$order_qnty_in_pcs=$row1[csf('attached_qnty')]*$row1[csf('ratio')];
			$total_attach_qty+=$order_qnty_in_pcs;
			$total_attached_value += $row1[csf('attached_value')];
		}
	}

    $sql_comm_freight="SELECT a.com_sales_contract_id,a.wo_po_break_down_id, c.costing_per, d.comm_cost, d.freight
    from com_sales_contract_order_info a, wo_po_break_down b, wo_pre_cost_mst c, wo_pre_cost_dtls d
    where a.wo_po_break_down_id=b.id and b.job_no_mst=c.job_no and c.job_no=d.job_no and com_sales_contract_id='$data[1]'";
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

	$sql_amendment="SELECT b.id,b.amendment_no, b.amendment_date, b.amendment_value, b.value_change_by, b.last_shipment_date, b.insert_date
	from  com_sales_contract_amendment b where b.contract_id='$data[1]' order by B.id desc";
	$amendment_data_arr = sql_select($sql_amendment);
    /*echo "<pre>";
    print_r($amendment_data_arr);*/
    $amendment_no=$amendment_data_arr[0][AMENDMENT_NO];
    $amendment_date=$amendment_data_arr[0][AMENDMENT_DATE];
    $amendment_value=$amendment_data_arr[0][AMENDMENT_VALUE];
    $increase_decrease=$amendment_data_arr[0][VALUE_CHANGE_BY];
    $amendment_last_shipment_date=$amendment_data_arr[0][LAST_SHIPMENT_DATE];
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
    </style>
    <div class="a4size">
        <table width="794" cellpadding="0" cellspacing="0" border="0" >
            
		<div class="parent">
		<div style=" width:650px; margin-top:60px;font-size:18px;">
			Ref No:&nbsp;<? echo 	$contact_system_id ;?>
			
			</div>
			<div style="text-align: right;margin-top:60px;font-size:18px;"> <?
                    $currentDate = date('M d, Y');
                       echo $currentDate;
           ?>
		   </div>
		</div>
            <tr>
                <td width="25"></td>
                <td width="650" align="left" style="font-size:18px;">To</td>
                <td width="25" ></td>
            </tr>
            <tr>
                <td width="25" ></td>
                <td width="650" align="left" style="font-size:18px;">
                <?
                    echo "The Manager<br>";
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
                <strong style="font-size:18px;">Subject:  : Submission of sales contract worth for  # <? echo $contract_no." date ".$contract_date." for ".$currency_name." $".$contract_value; ?> & open BTB L/C.</strong></td>
                <td width="25" ></td>
            </tr>
            <tr>
                <td colspan="3" height="20"></td>
            </tr>
            <tr>
                <td width="25" ></td>
                <td width="650" align="left" style="font-size:18px;"> Dear Sir, </td>
                <td width="25" ></td>
            </tr>
            <tr>
                <td width="25" ></td>
                <td width="650" align="justify" style="font-size:18px;">
                In reference to the above, we are submitting herewith the above noted <strong>Sales Contract</strong> for your kind attention and request you to keep under lien against which we will open BTB LC for USD i.e. @75% of FOB value.
                </td>
                <td width="25" ></td>
            </tr>
        </table>
        <table width="794" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td colspan="3" height="15"></td>
            </tr>
            <tr>
                <td colspan="3" style="padding-left: 28px;" style="font-size:18px;">Details of the <strong>Sales Contract</strong> as follows:</td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280" style="font-size:18px;">01. Sales Contract</td>
                <td width="15" >:</td>
                <td width="380" style="font-size:18px;"><? echo $contract_no;?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280" style="font-size:18px;">02. Date</td>
                <td width="15" >:</td>
                <td width="380" style="font-size:18px;"><? echo $contract_date; ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280" style="font-size:18px;">03. Rev no & Date</td>
                <td width="15" >:</td>
                <td width="380" style="font-size:18px;"><? echo $amendment_no .' & '.change_date_format($amendment_date) ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280" style="font-size:18px;">04. <? echo ($increase_decrease==1) ? "Increased Value" : "Decreased Value" ; ?></td>
                <td width="15" >:</td>
                <td width="380" style="font-size:18px;"><? if($amendment_value) echo '$'.number_format($amendment_value,2); ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280" style="font-size:18px;">05. Value in USD</td>
                <td width="15" >:</td>
                <td width="380" style="font-size:18px;"><? if($contract_value) echo '$'.$contract_value,2; ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280" style="font-size:18px;">06. Buyer Name</td>
                <td width="15" >:</td>
                <td width="380" style="font-size:18px;"><? echo $buyer_name; ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280" style="font-size:18px;">07. Comm./Freight</td>
                <td width="15" >:</td>
                <td width="380" title="(Comm+Freight)/Costing Per*Attach.Qty Pcs" style="font-size:18px;"><? $comm_freight=($total_comm_cost+$total_freight)*$total_attach_qty;
                	if ($comm_freight>0) echo '$'.number_format($comm_freight,2); ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280" style="font-size:18px;">08. FOB Value</td>
                <td width="15" >:</td>
                <td width="380" style="font-size:18px;"><? if($total_attached_value) echo '$'.number_format($total_attached_value,2); ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280" style="font-size:18px;">09. 75% BTB Limit</td>
                <td width="15" >:</td>
                <td width="380" style="font-size:18px;"><? if($contract_value) echo '$'.number_format(($contract_value*75)/100,2); ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280" style="font-size:18px;">10. Tenor</td>
                <td width="15" >:</td>
                <td width="380" style="font-size:18px;"><? echo $payTerm; if ($tenor>0) { echo ', '.$tenor.' Days'; } ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280" style="font-size:18px;">11. Qty</td>
                <td width="15" >:</td>
                <td width="380" style="font-size:18px;"><? if($total_attach_qty) echo number_format($total_attach_qty,2).' PCS'; ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280" style="font-size:18px;">12. Shipment Date</td>
                <td width="15" >:</td>
                <td width="380" style="font-size:18px;"><? echo change_date_format($last_shipment_date); ?></td>
            </tr>
            <tr>
                <td width="25" >&nbsp;</td>
                <td width="280" style="font-size:18px;">13. Expiry Date</td>
                <td width="15" >:</td>
                <td width="380" style="font-size:18px;"><? echo change_date_format($expiry_date); ?></td>
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
                <td width="650" align="justify" style="font-size:18px;">
                We would request your kind self to do the needful to lien the aforesaid <strong>Sales Contract</strong> and necessary action at your end.
                <br><br>
                Thank you very much, indeed
                </td>
                <td width="25" ></td>
            </tr>
            <tr>
                <td colspan="3" height="50"></td>
            </tr>
        </table>
        <table width="794" cellpadding="0" cellspacing="0" border="0" style="margin-top: 60px; font-size:18px;">
            <tr>
                <td style="padding-left: 28px;" width="397" align="left"><strong>-------------------------------</strong></td>
               
            </tr>
            <tr>
                <td style="padding-left: 28px;" width="450" align="left"><strong>Authorized Signature</strong></td>
            </tr>
		 
        </table>
		<table width="794" cellpadding="0" cellspacing="0" border="0" style="margin-top: 100px;">
            <tr>
                <td style="padding-left: 28px; font-size:12px; " width="397" align="right">	Prepared By-<? echo $inserted_by 	?></td>
               
            </tr>
           
        </table>
		
	
    </div>
    <!--  -->
    <?
    exit();
}

if ($action == "sales_contact_lien_letter7") // Lien LC App3
{
    $data = explode("**", $data);
    $company_id=$data[2];
    $country_arr = return_library_array("select id, country_name from lib_country where is_deleted=0","id","country_name");
	$nameArray=sql_select( "select id, company_name, plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$company_id");
	$adderess='';
	foreach ($nameArray as $company_add)
	{
		$company_arr[$company_add[csf("id")]]['company_name']=$company_add[csf("company_name")];
		if ($company_add[csf('plot_no')]!=''){ $adderess.= $company_add[csf('plot_no')].', '; }
		if ($company_add[csf('road_no')]!=''){ $adderess.=$company_add[csf('road_no')].', '; }
		if ($company_add[csf('block_no')]!=''){ $adderess.=$company_add[csf('block_no')].', ';}
		if ($company_add[csf('city')]!=''){ $adderess.=$company_add[csf('city')].', ';}
		if ($company_add[csf('zip_code')]!=''){ $adderess.=$company_add[csf('zip_code')].', '; }
		if ($company_add[csf('country_id')]!=''){ $adderess.=$country_arr[$company_add[csf('country_id')]]; }
	}

    $sql_buyer=sql_select("select id, buyer_name, address_1, address_2 from lib_buyer where status_active=1 and is_deleted=0");
    foreach ($sql_buyer as $val) {
    	$buyer_arr[$val[csf("id")]]['buyer_name']=$val[csf("buyer_name")];
    	$buyer_arr[$val[csf("id")]]['address_1']=$val[csf("address_1")];
    	$buyer_arr[$val[csf("id")]]['address_2']=$val[csf("address_2")];
    }
    //echo '<pre>';print_r($buyer_arr);

    if ($db_type==0) $date_diff_cond="DATEDIFF(last_shipment_date,expiry_date)";
	else if ($db_type==2) $date_diff_cond="(last_shipment_date - expiry_date)";
    
    if($data[0]==3)
	{
		$data_array=sql_select("SELECT id, contract_no, contract_date, lien_bank, lien_date, contract_value, internal_file_no, contact_system_id, tolerance, currency_name, beneficiary_name, buyer_name, last_shipment_date, export_item_category, expiry_date, bank_file_no, pay_term, tenor, max_btb_limit, max_btb_limit_value, issuing_bank, trader, country_origin, remarks, port_of_loading,port_of_discharge, convertible_to_lc, $date_diff_cond as date_diff from com_sales_contract where id='$data[1]'");
		$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');		
		foreach ($data_array as $row)
		{
			$sales_contract_id  = $row[csf("id")];
			$internal_file_no	= $row[csf("internal_file_no")];
			$contact_system_id  = $row[csf("contact_system_id")];
			$contract_no		= str_replace("HnM","H&M",$row[csf("contract_no")]);
			$contract_value		= def_number_format($row[csf("contract_value")],2);
			$contract_btb_limit_value= def_number_format($row[csf("max_btb_limit_value")],2);
			$contract_btb_limit	= def_number_format($row[csf("max_btb_limit")],2);
			$item_category		= $export_item_category[$row[csf("export_item_category")]];
			$sc_date			= $row[csf("contract_date")];
			$contract_date		= change_date_format($row[csf("contract_date")]);
			$lien_bank			= $row[csf("lien_bank")];
			$issuing_bank		= $row[csf("issuing_bank")];
			$trader			    = $row[csf("trader")];
			$country_origin		= $row[csf("country_origin")];
			$currency_name      = $currency[$row[csf("currency_name")]];
			$company_name       = $company_arr[$row[csf("beneficiary_name")]]['company_name'];
			$buyer_name         = str_replace("HnM","H&M",$buyer_arr[$row[csf("buyer_name")]]['buyer_name']);
			$address_1          = $buyer_arr[$row[csf("buyer_name")]]['address_1'];
			$address_2          = $buyer_arr[$row[csf("buyer_name")]]['address_2'];
			$currency_sign 		= $currency_sign_arr[$row[csf("currency_name")]];
			$ref				= $company_arr[$row[csf("beneficiary_name")]];
			$tolerance			= $row[csf("tolerance")];
			$remarks			= $row[csf("remarks")];
			$port_of_loading	= $row[csf("port_of_loading")];
			$port_of_discharge	= $row[csf("port_of_discharge")];
			$date_diff	        = $row[csf("date_diff")];
			$convertible	    = $row[csf("convertible_to_lc")];
			$last_shipment_date	= change_date_format($row[csf("last_shipment_date")]); 
		}
	}

	$sql_amendment=sql_select("select a.amendment_no, a.amendment_date, a.value_change_by, a.amendment_qnty, a.amendment_value from com_sales_contract_amendment a where a.contract_id='$sales_contract_id' and a.amendment_no>0 and a.status_active=1");
	foreach ($sql_amendment as $row) {
		$amendment_no   = $row[csf("amendment_no")];
		$amendment_date = change_date_format($row[csf("amendment_date")]);
		$value_change_by = $increase_decrease[$row[csf("value_change_by")]];
		$amendment_qnty = $row[csf("amendment_qnty")];
		$amendment_value = $row[csf("amendment_value")];
	}
	
    $sql_bank = sql_select("select a.id, a.bank_name, a.swift_code, a.branch_name, a.address, b.account_no from lib_bank a, lib_bank_account b where a.id=b.account_id and a.id='$lien_bank'");
    foreach ($sql_bank as $row1) 
    {
        $bank_name = $row1[csf("bank_name")];
        $address = $row1[csf("address")];
        $swift_code = $row1[csf("swift_code")];
        //$account_no = $row1[csf("account_no")];
    }

	if($issuing_bank)
	{
		$sql_iss_bank = sql_select("SELECT a.id, a.bank_name, a.address from lib_bank a where a.id='$issuing_bank'");
		foreach ($sql_iss_bank as $row1) 
		{
			$iss_bank_name = $row1[csf("bank_name")];
			$iss_bank_address = $row1[csf("address")];
		}	
	}
    
   //echo "select b.account_no from lib_bank a, lib_bank_account b where a.id=b.account_id and a.id='$lien_bank' and b.company_id=$company_id";
    $account_no = return_field_value("b.account_no as account_no", "lib_bank a, lib_bank_account b", "a.id=b.account_id and a.id='$lien_bank' and b.company_id=$company_id","account_no");

    $is_sales=return_field_value("is_sales","com_sales_contract_order_info","com_sales_contract_id=$sales_contract_id and status_active=1","is_sales");
	//echo $is_sales.test;die;
	if($is_sales==0)
	{
		$sql = "SELECT wb.id, ci.id as idd, wm.gmts_item_id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.shipment_date as shipment_date, wb.job_no_mst,wm.product_dept, wm.style_ref_no, wm.order_uom, wm.total_set_qnty as ratio, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active 
		from wo_po_break_down wb, wo_po_details_master wm, com_sales_contract_order_info ci 
		where wb.job_id = wm.id and wb.id=ci.wo_po_break_down_id and ci.com_sales_contract_id='$sales_contract_id' and ci.status_active = '1' and ci.is_deleted = '0' order by ci.id";
	}
	else
	{
		$sql = "SELECT wm.id, ci.id as idd, 0 as gmts_item_id, wm.job_no as po_number, sum(wb.amount) as po_total_price, sum(wb.finish_qty) as po_quantity, wm.delivery_date as shipment_date, wb.job_no_mst, wm.style_ref_no, wb.order_uom, 1 as ratio, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active 
		from fabric_sales_order_dtls wb, fabric_sales_order_mst wm, com_sales_contract_order_info ci 
		where wm.id = wb.mst_id and wb.mst_id=ci.wo_po_break_down_id and ci.com_sales_contract_id='$sales_contract_id' and ci.status_active = '1' and ci.is_deleted = '0'
		group by  wm.id, ci.id, wm.job_no, wm.delivery_date, wb.job_no_mst, wm.style_ref_no, wb.order_uom, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active 
		order by ci.id";
	}
	$nameArray=sql_select( $sql );
	$po_id_arr=array();$ac_po_arr=array();
	if($is_sales==0)
	{
		foreach($nameArray as $row)
		{
			$po_id_arr[$row[csf("id")]]=$row[csf("id")];
		}
		if(count($po_id_arr)>0)
		{
			$ac_po_sql=sql_select("select po_break_down_id, acc_po_no as acc_po_no from wo_po_acc_po_info where status_active=1 and is_deleted=0 and po_break_down_id in(".implode(",",$po_id_arr).") ");
			if(count($ac_po_sql)>0)
			{
				foreach($ac_po_sql as $row)
				{
					$ac_po_arr[$row[csf("po_break_down_id")]].=$row[csf("acc_po_no")].",";
				}
			}
			
		}
	}
	if (count($ac_po_arr)>0) $tot_colspan=11; else $tot_colspan=10;

	if($data[3]==1){
		$hf_sql_res=sql_select("select HEADER_LOCATION,BODY_LOCATION,FOOTER_LOCATION from TEMPLATE_PAD where status_active=1 and is_deleted=0 and COMPANY_ID=$company_id");
		$padDataArr=array();
		foreach ($hf_sql_res as $row) {
			$padDataArr['header']='<img src="../../'.$row['HEADER_LOCATION'].'" alt="Header Image not found" style="width:100%;">';
			$padDataArr['body']='../../'.$row['BODY_LOCATION'];
			$padDataArr['footer']='<img src="../../'.$row['FOOTER_LOCATION'].'" alt="Footer Image not found" style="width:100%;">';
		}
	}

 
    ?>

	<style>
		 @media print{
			.report-footer{position: fixed;bottom: 0;display: block;}
		 }
	</style>  
   
    <table width="980" cellpadding="0" cellspacing="0" border="0">
    	<thead>
        	<tr>
				<th colspan="<?=$tot_colspan;?>" style="padding-top:0;"><?=$padDataArr['header'];?></th>
			</tr>
        </thead>
        <tbody style="background-image:url('<?=$padDataArr['body'];?>');">
        	<tr><td height="20" colspan="<?=$tot_colspan;?>">&nbsp;</td></tr>
            <tr>
                <td colspan="<?=$tot_colspan;?>" style="text-align: center; text-decoration: underline; font-weight: bold; font-size: 25px;"><strong>Sales Contract</strong></td>
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <? 
                if(count($ac_po_arr)>0) $sales_col=8; else $sales_col=7;
                ?>
                <td colspan="<?=$sales_col;?>">Sales Contract No. # <?= $contract_no; ?></td>
                <td colspan="3">Date: <?= $contract_date; ?></td>
            </tr>
            <tr>
                <td colspan="<?=$sales_col;?>">Amendment No. # <?= $amendment_no; ?></td>
                <td colspan="3">Date: <?= $amendment_date; ?></td>
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="<?=$tot_colspan;?>">This irrevocable contract made between <? echo $address_2; ?> &  <? echo $company_name; ?> under the following terms and conditions:</td>                
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="3">Name and Address of Consignee/Notify&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td> 
                <td colspan="<?=$sales_col;?>"><?= $address_1; ?></td>               
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="3" valign="top">Name & address of Consignees Bank&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td> 
                <td colspan="<?=$sales_col;?>"><?= $iss_bank_name."<br>".$iss_bank_address; ?></td>               
            </tr>
            <tr><td height="20"></td></tr>
            <tr>
                <td colspan="3">Name and Address of Supplier/Seller&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: </td>
                <td colspan="<?=$sales_col;?>"><? echo $company_name.', '.rtrim($adderess,', '); ?></td>                
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="3" valign="top">Name of Supplier / Shipper Bank Details&nbsp;&nbsp;&nbsp;:</td>
                <td colspan="<?=$sales_col;?>">
                    <?= $bank_name; ?><br>
                    <?= $address; ?><br>
                    A/C No. <?= $account_no; ?><br>
                    SWIFT:<?= $swift_code; ?>
                </td>
            </tr>            
            <tr><td style="font-size:20px;" colspan="<?=$tot_colspan;?>"><strong>Order Details:</strong></td></tr>
            <tr >
                <td width="110" style="font-weight:bold; text-align:center; border:1px solid black">Order Number</td>
                <? 
                if (count($ac_po_arr)>0) 
                {
					$col_span=7;
                    ?>
                    <td width="110" style="font-weight:bold; text-align:center; border:1px solid black">Acc.PO No.</td>
                    <? 
                }
				else
				{
					$col_span=6;
				}
                ?>	
                <td width="110" style="font-weight:bold; text-align:center; border:1px solid black">Style Ref</td>
                <td width="110" style="font-weight:bold; text-align:center; border:1px solid black">Gmts. Item</td>
                <td width="80" style="font-weight:bold; text-align:center; border:1px solid black">Prod. Dept. </td>
                <td width="80" style="font-weight:bold; text-align:center; border:1px solid black">Order Qty</td>
				<td width="90" style="font-weight:bold; text-align:center; border:1px solid black">Order Value</td>
                <td width="80" style="font-weight:bold; text-align:center; border:1px solid black">Confirm Qty</td>
                <td width="40" style="font-weight:bold; text-align:center; border:1px solid black">UOM</td>
                <td width="70" style="font-weight:bold; text-align:center; border:1px solid black">Rate</td>
                <td width="110" style="font-weight:bold; text-align:center; border:1px solid black">Confirm Value</td>
                <td style="font-weight:bold; text-align:center; border:1px solid black">Shipment Date</td>
            </tr>
            <?
            $i=1;
            $tot_attached_qnty=0;
            $tot_attached_value=0;
            foreach ($nameArray as $selectResult)
            {
                ?>
                <tr>
                    <td width="110" style="border:1px solid black; word-break:break-all;"><? echo $selectResult[csf('po_number')]; ?></td>
                    <? 
                    if (count($ac_po_arr)>0) 
                    {
                        ?>
                        <td width="110" style="border:1px solid black; word-break:break-all;"><? echo chop($ac_po_arr[$selectResult[csf('id')]],","); ?></td>
                        <? 
                    } 
                    ?>
                    <td width="110" style=" border:1px solid black; word-break:break-all;"><? echo $selectResult[csf('style_ref_no')]; ?></td>
                    <td width="110" style=" border:1px solid black; word-break:break-all;">
                        <?
                            $gmts_item='';
                            $gmts_item_id=explode(",",$selectResult[csf('gmts_item_id')]);
                            foreach($gmts_item_id as $item_id)
                            {
                                if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
                            }
                            echo $gmts_item;
                        ?>
                    </td>
					<td width="80" align="center" style="border:1px solid black"><? echo $product_dept[$selectResult[csf('product_dept')]]; ?></td>
                    <td width="80" align="right" style="border:1px solid black"><? echo $selectResult[csf('po_quantity')]; ?></td>
					<td width="90" align="right" style="border:1px solid black"><? echo number_format($selectResult[csf('po_total_price')],2); ?></td>
                    <td width="80" align="right" style="border:1px solid black"><? echo $selectResult[csf('attached_qnty')]; ?></td>
                    <td width="40" align="center" style="border:1px solid black"><? echo $unit_of_measurement[$selectResult[csf('order_uom')]]; ?></td>
                    <td width="70" align="center" style="border:1px solid black"><? echo $currency_sign.' '.number_format($selectResult[csf('attached_rate')],2); ?></td>
                    <td width="110" align="right" style="border:1px solid black"><? echo $currency_sign.' '.number_format($selectResult[csf('attached_value')],2); ?></td>
                    <td align="center" style="border:1px solid black"><? echo change_date_format($selectResult['SHIPMENT_DATE']); ?></td>
                </tr>
                <?
                $i++;
                $tot_attached_qnty+=$selectResult[csf('attached_qnty')];
                $tot_attached_value+=$selectResult[csf('attached_value')];
            }	
            ?>
            <tr style="font-weight: bold;">
                <td  align="right" colspan="<?= $col_span; ?>" style="font-size:18px; border:1px solid black">Total</td>
                <td width="80" align="right" style="font-size:18px; border:1px solid black"><? echo $tot_attached_qnty; ?></td>
                <td width="40" align="center" style="font-size:18px; border:1px solid black">Pcs</td>
                <td width="70" align="center" style="font-size:18px; border:1px solid black">&nbsp;</td>
                <td width="100" align="right" style="font-size:18px; border:1px solid black"><? echo $currency_sign.' '.number_format($tot_attached_value,2); ?></td>
				<td style="font-size:18px; border:1px solid black"></td>
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>     
			<?
				if(count($sql_amendment)>0)
				{
					?>
						<tr>
							<td colspan="<?=$tot_colspan;?>">
								<table cellpadding="0" cellspacing="0" border="1">
									<tr>
										<td align="center" width="150"><strong><?=$value_change_by;?> Qty</strong></td>
										<td align="center" width="150"><strong><?=$value_change_by;?> Value</strong></</td>
									</tr>
									<tr>
										<td align="right" ><strong><?=$amendment_qnty;?> Pcs</strong></</td>
										<td align="right"><strong><?=$currency_sign.' '.$amendment_value;?></strong></</td>
									</tr>
								</table>
							</td>                
						</tr>
						<tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>  
					<?
				}
			?>      
   
            <tr>
                <td colspan="<?=$tot_colspan;?>">All Purchase orders are subject to <?= $buyer_name; ?> Terms and Conditions.<br>
				<?if($convertible!=2){?>Sales Contract will replace by Export Contract/Export L/C.<?}?></td>                
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="3">Tolerance&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td colspan="<?=$sales_col;?>"><?= $tolerance; ?>% +/- in quantity and value are acceptable.</td>
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="3">Mode of Shipments&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td colspan="<?=$sales_col;?>">FOB/FCA Chattogram, Bangladesh / Dhaka, Bangladesh.</td>
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="3">Insurance&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td colspan="<?=$sales_col;?>">To be covered by the Buyer.</td>
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="3">Last Shipment Date&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td colspan="<?=$sales_col;?>"><? echo $last_shipment_date; ?></td>
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="3">Date of Expiry&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td colspan="<?=$sales_col;?>">After <?= $date_diff; ?> days from the date of Shipment.</td>
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="3">Payment Terms.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td  colspan="<?=$sales_col;?>"><?= $remarks; ?></td>
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="3">Country of Origin&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td colspan="<?=$sales_col;?>">Bangladesh.</td>
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="3">Partial & Trans Shipment&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td colspan="<?=$sales_col;?>">Allowed.</td>
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="3">Port of Destination&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td colspan="<?=$sales_col;?>"><?= $country_origin; ?></td>
            </tr>
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="3">Port of Loading&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td colspan="<?=$sales_col;?>"><?= $port_of_loading; ?></td>
            </tr> 
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="3">Port of Discharge&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td colspan="<?=$sales_col;?>"><?= $port_of_discharge; ?></td>
            </tr> 
            <tr><td height="20" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td colspan="3" valign="top">Required Documents&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td colspan="<?=$sales_col;?>">
                    <ul>
                        <li>Original Invoice.</li>
                        <li>Original Packing list.</li>
                        <li>Original Bills of Lading / FCR’s/AWB.</li>
                        <li>Certificate of Origin/ GSP Form A/REX Declaration.</li>
                    </ul> 
                </td>
            </tr>
            <tr><td height="50" colspan="<?=$tot_colspan;?>"></td></tr>
            <tr>
                <td valign="top" colspan="<?=$sales_col;?>">For and on behalf of</td>
                <td colspan="3">For and on behalf of</td>
            </tr>
            <tr>
                <td colspan="<?=$sales_col;?>" valign="top"><?= $trader; ?></td>
                <td colspan="3"><?= $company_name; ?></td>
            </tr>
        </tbody>
        <tfoot class="report-footer">
        	<tr><th colspan="<?=$tot_colspan;?>" style="padding-bottom:0;"><?=$padDataArr['footer'];?></th></tr>
        </tfoot>
            
    </table> 
 
    <?
    exit();
}
//letter 9 by sakib
if ($action == "sales_contact_lien_letter9")
{
    //echo $data; die;
    $data = explode("**", $data);
    //export lc lien-----------------
    $company_lib=return_library_array( "select id, company_name from lib_company",'id','company_name');
    $buyer_lib=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
    if($data[0]==3)
	{
		$data_array=sql_select("SELECT id, contract_no, contract_date, lien_bank, lien_date,contract_value,PORT_OF_DISCHARGE, max_btb_limit, max_btb_limit_value, internal_file_no, currency_name, buyer_name,beneficiary_name,last_shipment_date,estimated_qnty, expiry_date, bank_file_no, pay_term from com_sales_contract where id='$data[1]'");
		//$sql_btb_res=sql_select($data_array);
		// echo "<pre>"; print_r($data_array); die;
		foreach ($data_array as $row)
		{
			$internal_file_no	= $row[csf("internal_file_no")];
			$contract_no		= $row[csf("contract_no")];
			$max_btb_limit		= $row[csf("max_btb_limit")];
			$beneficiary_name 	= $row[csf("beneficiary_name")];
			$PORT_OF_DISCHARGE 	=$row[csf("PORT_OF_DISCHARGE")];
			//$max_btb_limit_value= $row[csf("max_btb_limit_value")];
			$contract_date		= change_date_format($row[csf("contract_date")]);
			$last_shipment_date	= change_date_format($row[csf("last_shipment_date")]);
			$lien_bank			= $row[csf("lien_bank")];
			$lien_date			= $row[csf("lien_date")];
			$currency_name      = $currency[$row[csf("currency_name")]];
			$buyer_name         = $buyer_lib[$row[csf("buyer_name")]];
			$expiry_date        = $row[csf("expiry_date")];
			$currency_sign 		= $currency_sign_arr[$row[csf("currency_name")]];
			//$bank_file_no 		= $row[csf("bank_file_no")];
			$payTerm 			= $pay_term[$row[csf("pay_term")]];
			$contract_value		= $row[csf("contract_value")];
			$estimated_qnty     = $row[csf("estimated_qnty")];
			$unit_price_avg_cal = ($contract_value/$estimated_qnty); 
			 //$unit_price_avg = number_format($unit_price_avg_cal,2);
           
		}

		$po_data_arr=sql_select("SELECT c.id as item_id, c.item_name, a.wo_po_break_down_id as po_id from com_sales_contract_order_info a join wo_po_color_size_breakdown b on 
		a.wo_po_break_down_id=b.po_break_down_id join lib_garment_item c on  b.item_number_id = c.id where a.status_active=1 and a.is_deleted=0 and a.com_sales_contract_id = $data[1] and b.status_active=1 and b.is_deleted=0 and rownum=1 order by a.id asc");
		
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

    $sql_comm_freight="SELECT a.com_sales_contract_id,a.wo_po_break_down_id, c.costing_per, d.comm_cost, d.freight,b.id as order_id
    from com_sales_contract_order_info a, wo_po_break_down b, wo_pre_cost_mst c, wo_pre_cost_dtls d
    where a.wo_po_break_down_id=b.id and b.job_no_mst=c.job_no and c.job_no=d.job_no and com_sales_contract_id='$data[1]'";
    $comm_freight_data=sql_select($sql_comm_freight);

	$order_id_arr = array();
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
		$order_id_arr[$rows[csf('order_id')]] = $rows[csf('order_id')];

	// 	echo "<pre>";
	// 	print_r($order_id_arr); 
	//    echo "</pre>";die();
    }
    // echo ($total_comm_cost+$total_freight)*$total_attach_qty;
    // echo $total_freight;
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
	<body>
		<div class="a4size">
					<div class="authorheader" style="margin-left: 10px";>
						<img src="../../<? echo return_field_value("header_location","template_pad","company_id =".$beneficiary_name." and is_deleted=0 and status_active=1"); ?>" style="width:794px;height: 100px; />
					</div>
			<div class="author" style="margin-left: 10px";>	
					<table width="794" cellpadding="0" cellspacing="0" style="margin-left: 30px;">
							<br>
							 <tr><td colspan="3" height="80"></td></tr>
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
							<tr><td colspan="3" height="20"></td></tr>
							<tr>
								<td width="25"></td>
								<td width="650" align="left"><?echo "File No "."$internal_file_no"."<br>";?></td>
								<td width="25" ></td>
							</tr>
							<tr><td colspan="3" height="20"></td></tr>
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
								<strong><? echo "Subject:  Submission of sales contract worth for "."$currency_name "."$currency_sign"."$contract_value".", "."buyer "."$buyer_name"; ?></strong></td>
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
								<td colspan="3" height="20"></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<td width="650" align="justify">
								Please be informed that we have received following Sales Contract from our reputed buyer <strong><? echo "$buyer_name";?></strong>. to <? echo "$PORT_OF_DISCHARGE";?> execute the export / shipment against the same. Would request you to lien the same with your bank and advise us a certificate in this regard. We will open BBLC against these Sales Order up-to maximum <strong><? echo "$max_btb_limit"."%"; ?></strong> of Sales Contract / Purchase Orders value to import local and foreign purchase of fabrics and accessories.  We do hereby inform you that export proceeds against this Sales / Purchase Order will be made through TT In advance. Details are as follows:
								</td>
								<td width="25" ></td>
							</tr>
							<tr>
								<td colspan="3" height="20"></td>
							</tr>
					</table>
					<table width="794" cellpadding="0" cellspacing="0" border="1px" padding left="20px" style="margin-left: 50px;">
							<tr>
								<!-- <td width="25" border=""></td> -->
								<td width="280"><b>Contract number and dt.</td>
								<td width="380"><? echo "$contract_no "."Date: "."$contract_date";?></td>
							</tr>    
							<tr>
								<!-- <td width="25" ></td> -->
								<td width="280"><b>Total contract value </td>
								<td width="380"><? echo "$currency_name"."$currency_sign ".number_format($contract_value); ?></td>
							</tr>
							<tr>
								<!-- <td width="25" ></td> -->
								<td width="280"><b>Latest Shipment Date</td>
								<td width="380"><? echo date('M d, Y',strtotime($last_shipment_date)); ?></td>
							</tr>
							<tr>
								<!-- <td width="25" ></td> -->
								<td width="280"><b>Items</td>
								<td width="380"><? echo implode(",",$item_name_arr) ?></td>
							</tr>
							<tr>
								<!-- <td width="25" ></td> -->
								<td width="280"><b>Payment Terms</td>
								<td width="380"><? echo "$payTerm"; ?></td>
							</tr>
				
							<tr>
								<!-- <td width="25" ></td> -->
								<td width="280"><b>% BBLC to be opened </td>
								<td width="380" ><? echo "$max_btb_limit"."% "; ?></td>
							</tr>
							<tr>
								<!-- <td width="25" ></td> -->
								<td width="280"><b>Gmts Qty in pc</td>
								<td width="380"><?  echo $estimated_qnty.' PCS'; ?></td>
							</tr>
							<tr>
								<!-- <td width="25" ></td> -->
								<td width="280"><b>Unit price in avg</td>
								<td width="380"><? echo "$currency_sign ".number_format($unit_price_avg_cal,2); ?></td>
							</tr>

							<tr>
								<!-- <td width="25" ></td> -->
								<td width="280"><b>Hs code no</td>
								<td width="380"><? echo "$hs_code_number"; ?></td>
							</tr>
					</table>
					<table width="794" cellpadding="0" cellspacing="0" border="0" >
							<br>
							<tr>
								<td width="50"></td>     
							</tr>
							<tr><td colspan="3" height="20"></td></tr>
							<tr>
								<td width="25" ></td>
								<td width="650" align="justify">
								In case of any shortfall to meet up liabilities against this Sales / Purchase Order, we will arrange funds from our own / other sources.
								</td>
								<td width="25" ></td>
							</tr>
							<tr><td colspan="3" height="20"></td></tr>
							<tr>
								<td width="25" ></td>
								<td width="650" align="left"> Thanking you. </td>
								<td width="25" ></td>
							</tr>
							<tr><td colspan="3" height="200"></td></tr>
					</table>
					<table width="794" cellpadding="0" cellspacing="0" border="0" >
						<div class="authorfooter" style="margin-left: 10px" ;><img src="../../<? echo return_field_value("FOOTER_LOCATION","template_pad","company_id =".$beneficiary_name." and is_deleted=0 and status_active=1"); ?>" style="style="width:794px;height: 100px;"  />
						</div>
					</table>
			
			<div>	
		</div>
	</body>
    <?

    exit();
}
if($action== "designation_search"){
	echo load_html_head_contents("Item Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$system_id = $txt_system_id;
	?>
	<script>
		function js_set_value_lien_letter4(data) {
			var str=data.split("_");
			var system_id = str[0];
			var designation = str[1];
			print_report(3+'**'+system_id+'**'+designation,'sales_contact_lien_letter10','sales_contract_controller');
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
                	<tr onClick="js_set_value_lien_letter4('<?php echo $system_id.'_'.'Asst General Manager';?>')">
                        <td align="center">	1 </td>
                        <td align="left">Asst General Manager</td>                 
                    </tr>
					<tr onClick="js_set_value_lien_letter4('<?php echo $system_id.'_'.'Deputy General Manager';?>')">
                        <td align="center">	2 </td>
                        <td align="left">Deputy General Manager
					</td>                 
                    </tr>
					<tr onClick="js_set_value_lien_letter4('<?php echo $system_id.'_'.'General Manager';?>')">
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
if ($action == "sales_contact_lien_letter10")   //Lien LC 4
{
   // echo $data; die;
    $data = explode("**", $data);
	$designation_popup =  $data[2];
    //export lc lien-----------------
    $company_lib=return_library_array( "select id, company_name from lib_company",'id','company_name');
    $buyer_lib=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    if($data[0]==3)
	{
		$data_array=sql_select("SELECT id, contract_no, contract_date, lien_bank, lien_date,contract_value, max_btb_limit, max_btb_limit_value, internal_file_no, contact_system_id, currency_name, beneficiary_name, buyer_name, last_shipment_date, expiry_date, bank_file_no, pay_term, tenor from com_sales_contract where id='$data[1]'");

		foreach ($data_array as $row)
		{
			$internal_file_no	= $row[csf("internal_file_no")];
			$contact_system_id  = $row[csf("contact_system_id")];
			$contract_no		= $row[csf("contract_no")];
			$max_btb_limit		= $row[csf("max_btb_limit")];
			$contract_value		= def_number_format($row[csf("contract_value")],2);
			$max_btb_limit_value= def_number_format($row[csf("max_btb_limit_value")],2);
			$sc_date			= $row[csf("contract_date")];
			$contract_date		= change_date_format($row[csf("contract_date")]);
			$last_shipment_date	= change_date_format($row[csf("last_shipment_date")]);
			$lien_bank			= $row[csf("lien_bank")];
			$lien_date			= $row[csf("lien_date")];
			$currency_name      = $currency[$row[csf("currency_name")]];
			$company_name       = strtoupper($company_lib[$row[csf("beneficiary_name")]]);
			$buyer_name         = $buyer_lib[$row[csf("buyer_name")]];
			$expiry_date        = $row[csf("expiry_date")];
			$bank_file_no 		= $row[csf("bank_file_no")];
			$payTerm 			= $pay_term[$row[csf("pay_term")]];
            $tenor 				= $row[csf("tenor")];
		}
		//echo $buyer_name;die;
		// echo "<pre>";
		// print_r($data_array);die;

		$data_array1=sql_select("SELECT wm.total_set_qnty as ratio, ci.attached_qnty, ci.attached_value 
		from wo_po_break_down wb, wo_po_details_master wm, com_sales_contract_order_info ci 
		where wb.job_id=wm.id and wb.id=ci.wo_po_break_down_id and ci.com_sales_contract_id='$data[1]' and ci.status_active='1' and ci.is_deleted='0' order by ci.id");
		foreach($data_array1 as $row1)
		{
			$order_qnty_in_pcs=$row1[csf('attached_qnty')]*$row1[csf('ratio')];
			$total_attach_qty+=$order_qnty_in_pcs;
			$total_attached_value += $row1[csf('attached_value')];
		}
	}

    $sql_comm_freight="SELECT a.com_sales_contract_id,a.wo_po_break_down_id, c.costing_per, d.comm_cost, d.freight
    from com_sales_contract_order_info a, wo_po_break_down b, wo_pre_cost_mst c, wo_pre_cost_dtls d
    where a.wo_po_break_down_id=b.id and b.job_no_mst=c.job_no and c.job_no=d.job_no and com_sales_contract_id='$data[1]'";
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

	$sql_amendment="SELECT b.id,b.amendment_no, b.amendment_date, b.amendment_value, b.value_change_by, b.last_shipment_date, b.insert_date
	from  com_sales_contract_amendment b where b.contract_id='$data[1]' order by B.id desc";
	$amendment_data_arr = sql_select($sql_amendment);
    /*echo "<pre>";
    print_r($amendment_data_arr);*/
    $amendment_no=$amendment_data_arr[0][AMENDMENT_NO];
    $amendment_date=$amendment_data_arr[0][AMENDMENT_DATE];
    $amendment_value=$amendment_data_arr[0][AMENDMENT_VALUE];
    $increase_decrease=$amendment_data_arr[0][VALUE_CHANGE_BY];
    $amendment_last_shipment_date=$amendment_data_arr[0][LAST_SHIPMENT_DATE];
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
    </style>
    <div class="a4size">
        <table width="794" cellpadding="0" cellspacing="0" border="0" >
            <br>
			<tr><td colspan="3" height="60"></td></tr>
			<tr><td colspan="3" height="20"></td></tr>
            <tr>
                <td width="25"></td>
                <td width="650" align="left">Date: <? echo date('M d, Y',strtotime($sc_date));?></td>
                <td width="25" ></td>
            </tr>
			<tr><td colspan="3" height="30"></td></tr>
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
					<strong>Sub:  Application for lien the <u>(Export LC / SALES CONTRACT) as per ERP used </u> NO.: <? echo $contract_no ;?>
					DT. <? echo $contract_date;?> value US$. <? echo $contract_value; ?> </strong>

				</td>
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
                <td colspan="3" height="10"></td>
            </tr>
            <tr>
                <td width="25" ></td>
                <td width="650" align="justify">
				We informed you that we have received Export LC / Sales Contract No. <? echo $contract_no ;?> <strong> DT. <? echo $contract_date;?> value US$. <? echo $contract_value; ?></strong>  from our Buyer <? echo $buyer_name;?>, please Lien the above Export LC / Sales Contract.
                </td>
                <td width="25" ></td>
            </tr>
			<tr>
                <td colspan="3" height="20"></td>
            </tr>
			<tr>
                <td width="25" ></td>
                <td width="650" align="justify"> Your kind Co-operation will be highly appreciated. </td>
                <td width="25" ></td>
            </tr>
			<tr>
                <td colspan="3" height="70"></td>
            </tr>
			<tr>
                <td width="25" ></td>
                <td width="650" align="justify"> Thanking you. </td>
                <td width="25" ></td>
            </tr>
        </table>
        <table width="794" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td colspan="3" height="15"></td>
            </tr>
        </table>
        <!-- <table width="794" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td colspan="3" height="15"></td>
            </tr>

            <tr>
                <td colspan="3" height="15"></td>
            </tr>
            <tr>
                <td width="25" ></td>
                <td width="650" align="justify">
                We would request your kind self to do the needful to lien the aforesaid <strong>Sales Contract</strong> and necessary action at your end.
                <br><br>
                Thank you very much, indeed
                </td>
                <td width="25" ></td>
            </tr>
            <tr>
                <td colspan="3" height="50"></td>
            </tr>
        </table> -->
        <!-- <table width="794" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td style="padding-left: 28px;" width="397" align="left"><strong>-------------------------------</strong></td>
                <td width="397" align="left"><strong></strong></td>
            </tr>
            <tr>
                <td style="padding-left: 28px;" width="397" align="left"><strong>Authorized Signature</strong></td>
                <td width="397" align="left"><strong></strong></td>
            </tr>
            <tr >
                <td style="padding-left: 28px; padding-top: 30px;" width="397" align="left"><strong><?=$company_name;?></strong></td>
            </tr>
        </table> -->
    </div>
    <!--  -->
    <?
    exit();
}
if ($action == "sales_contact_print")
{
	extract($_REQUEST);

    $company_id=$cbo_beneficiary_name;
    $country_arr = return_library_array("SELECT id, country_name from lib_country where is_deleted=0","id","country_name");
	$nameArray=sql_select( "SELECT ID, COMPANY_NAME, PLOT_NO, LEVEL_NO, ROAD_NO, BLOCK_NO, COUNTRY_ID, CITY, ZIP_CODE from lib_company where id=$company_id");
	$adderess='';
	foreach ($nameArray as $company_add)
	{
		$company_arr[$company_add["ID"]]['company_name']=$company_add["COMPANY_NAME"];
		if ($company_add['PLOT_NO']!=''){ $adderess.= $company_add['PLOT_NO'].', '; }
		if ($company_add['ROAD_NO']!=''){ $adderess.=$company_add['ROAD_NO'].', '; }
		if ($company_add['BLOCK_NO']!=''){ $adderess.=$company_add['BLOCK_NO'].', ';}
		if ($company_add['CITY']!=''){ $adderess.=$company_add['CITY'].', ';}
		if ($company_add['ZIP_CODE']!=''){ $adderess.=$company_add['ZIP_CODE'].', '; }
		if ($company_add['COUNTRY_ID']!=''){ $adderess.=$country_arr[$company_add['COUNTRY_ID']]; }
	}
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$company_id'","image_location");
	$corporate_office=return_field_value("address","lib_group","is_deleted=0 ","address");
	$factory=return_field_value("remark","lib_group","is_deleted=0 ","remark");
    $sql_buyer=sql_select("SELECT ID, BUYER_NAME, ADDRESS_1 from lib_buyer where status_active=1 and is_deleted=0");
    foreach ($sql_buyer as $val) {
    	$buyer_arr[$val["ID"]]['buyer_name']=$val["BUYER_NAME"];
    	$buyer_arr[$val["ID"]]['address']=$val["ADDRESS_1"];
    }
 
    if($report_type==3)
	{
		$data_array=sql_select("SELECT id, contract_no, contract_date, contract_value, internal_file_no, contact_system_id, tolerance, currency_name, beneficiary_name, buyer_name, last_shipment_date, export_item_category, expiry_date, bank_file_no, pay_term, tenor,   country_origin, remarks, port_of_loading,port_of_discharge, tenor  from com_sales_contract where id='$txt_system_id'");
		$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');		
		foreach ($data_array as $row)
		{
			$sales_contract_id  = $row[csf("id")];
			$internal_file_no	= $row[csf("internal_file_no")];
			$contact_system_id  = $row[csf("contact_system_id")];
			$contract_no		= $row[csf("contract_no")];
			$contract_value		= def_number_format($row[csf("contract_value")],2);
			$item_category		= $export_item_category[$row[csf("export_item_category")]];
			$sc_date			= $row[csf("contract_date")];
			$contract_date		= change_date_format($row[csf("contract_date")]);
			$country_origin		= $row[csf("country_origin")];
			$currency_name      = $currency[$row[csf("currency_name")]];
			$company_name       = $company_arr[$row[csf("beneficiary_name")]]['company_name'];
			$buyer_name         = $buyer_arr[$row[csf("buyer_name")]]['buyer_name'];
			$buyer_address      = $buyer_arr[$row[csf("buyer_name")]]['address'];
			$currency_sign 		= $currency_sign_arr[$row[csf("currency_name")]];
			$currency_id 		= $row[csf("currency_name")];
			$ref				= $company_arr[$row[csf("beneficiary_name")]];
			$tenor				= $row[csf("tenor")];
			$tolerance			= $row[csf("tolerance")];
			$remarks			= $row[csf("remarks")];
			$port_of_loading	= $row[csf("port_of_loading")];
			$port_of_discharge	= $row[csf("port_of_discharge")];
			$last_shipment_date	= change_date_format($row[csf("last_shipment_date")]); 
			$expiry_date		= change_date_format($row[csf("expiry_date")]); 
		}
	}

	$sql_amendment=sql_select("SELECT a.amendment_no, a.amendment_date, a.value_change_by, a.amendment_qnty, a.amendment_value from com_sales_contract_amendment a where a.contract_id='$sales_contract_id' and a.amendment_no>0");
	foreach ($sql_amendment as $row) {
		$amendment_no   = $row[csf("amendment_no")];
		$amendment_date = change_date_format($row[csf("amendment_date")]);
		$value_change_by = $increase_decrease[$row[csf("value_change_by")]];
		$amendment_qnty = $row[csf("amendment_qnty")];
		$amendment_value = $row[csf("amendment_value")];
	}
	
    $is_sales=return_field_value("is_sales","com_sales_contract_order_info","com_sales_contract_id=$sales_contract_id and status_active=1","is_sales");
	//echo $is_sales.test;die;
	if($db_type==0){$country_clm="group_concat(distinct(d.country_id)) as country_id";}
	else{$country_clm="listagg(cast(d.country_id as varchar(4000)),',') within group(order by d.id) as country_id";}

	if($is_sales==0)
	{
		$sql = "SELECT wb.id, ci.id as idd, wm.gmts_item_id, wb.po_number, wb.pub_shipment_date as shipment_date, wm.style_ref_no, wm.STYLE_DESCRIPTION, wm.total_set_qnty as ratio, wm.agent_name, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active ,$country_clm
		from wo_po_details_master wm, com_sales_contract_order_info ci,wo_po_break_down wb 
		left join wo_po_color_size_breakdown d on d.po_break_down_id=wb.id and d.is_deleted = '0'
		where wb.job_id = wm.id and wb.id=ci.wo_po_break_down_id and ci.com_sales_contract_id='$sales_contract_id' and ci.status_active = '1' and ci.is_deleted = '0' 
		group by wb.id, ci.id, wm.gmts_item_id, wb.po_number, wb.pub_shipment_date , wm.style_ref_no, wm.STYLE_DESCRIPTION, wm.total_set_qnty, wm.agent_name, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active order by ci.id";
	}
	else
	{
		$sql = "SELECT wm.id, ci.id as idd, 0 as gmts_item_id, wm.job_no as po_number, sum(wb.amount) as po_total_price, sum(wb.finish_qty) as po_quantity, wm.delivery_date as shipment_date, wb.job_no_mst, wm.style_ref_no, wb.order_uom, 1 as ratio, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active , b.STYLE_DESCRIPTION
		from fabric_sales_order_dtls wb, fabric_sales_order_mst wm, com_sales_contract_order_info ci,WO_BOOKING_MST a, wo_po_details_master b
		where wm.id = wb.mst_id and wb.mst_id=ci.wo_po_break_down_id and ci.com_sales_contract_id='$sales_contract_id' and ci.status_active = '1' and ci.is_deleted = '0' and  wm.SALES_BOOKING_NO=a.BOOKING_NO and a.JOB_NO=b.JOB_NO
		group by  wm.id, ci.id, wm.job_no, wm.delivery_date, wb.job_no_mst, wm.style_ref_no, wb.order_uom, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active 
		order by ci.id";
	}
	
	$nameArray=sql_select( $sql );
	foreach ($nameArray as $selectResult)
	{
		$attached_qnty+=$selectResult[csf('attached_qnty')];
		$country_id_all.=$selectResult[csf('country_id')].',';
		 if($selectResult[csf('agent_name')]){$agent_id_all.=$selectResult[csf('agent_name')].',';}
	}	
	$country_id_arr=array_unique(explode(",",chop($country_id_all,',')));
	$agent_id_arr=array_unique(explode(",",chop($agent_id_all,',')));
	foreach($country_id_arr as $row)
	{
		$country_name.=$country_arr[$row].', ';
	}
	foreach($agent_id_arr as $row)
	{
		$agent_name=$buyer_arr[$row]['buyer_name'].', ';
	}
	ob_start();
	?>
	<style>
	.tblhead
	{
		font-weight:bold; 
		text-align:center; 
		border:1px solid black;
	}
	.tblbody
	{
		border:1px solid black; 
		word-break:break-all;
	}
	@page {
		margin-top: 3cm;
		margin-bottom: 2cm;
	}
	</style>
	<body>
		

    <table width="1000" cellpadding="0" cellspacing="0" border="0" id="mlm">
        <tbody>
			<!-- <tr>
				<td colspan="6" style="font-size:xx-large;" align="center"><strong><?=$company_name;?></strong></td>
				<td colspan="2" rowspan="2" align="center"><img src="../../<? echo $image_location; ?>" height="50" width="100"></td>
			</tr>
			<tr>
				<td  colspan="6" align="center"><?=$adderess;?></td>
			</tr> -->
			<tr><td colspan="8" height="20"></td></tr>
            <tr>
                <td colspan="8" style="text-align: center; text-decoration: underline; font-weight: bold; font-size: 25px;"><strong>SALES CONTRACT</strong></td>
            </tr>
            <tr><td colspan="8" height="20"></td></tr>
            <tr>
                <td valign="top">1.</td>
                <td colspan="2">SALES CONTRACT NO.</td>
                <td>: <?= $contract_no; ?></td>
                <td colspan="2">DATE</td>
                <td colspan="2">: <?= $contract_date; ?></td>
            </tr>
            <tr>
                <td valign="top">2.</td>
                <td colspan="2">NUMBER OF REVISED/ AMENDMENT</td>
                <td >: REV-<?= $amendment_no; ?></td>
                <td colspan="2"><?if($amendment_date){echo "REVISED DATE";}?></td>
                <td colspan="2"><?if($amendment_date){echo ": ".$amendment_date;}?></td>
            </tr>
            <tr>
                <td valign="top">3.</td>
                <td colspan="2" valign="top">APPLICANT & ADDRESS</td>
                <td >: <?= $buyer_name.'<br>'.$buyer_address; ?></td>
                <td colspan="2"><?if($amendment_qnty){echo "AMENDMENT QTY";}?></td>
                <td colspan="2"><?if($amendment_qnty){echo ": ".$amendment_qnty;}?></td>
            </tr>
            <tr>
                <td valign="top">4.</td>
                <td colspan="2" valign="top">SUPPLIER/SELLER & ADDRESS</td>
                <td >: <?= $company_name.'<br>'.$adderess; ?></td>
                <td colspan="2"><?if($amendment_value){echo "AMENDMENT VALUE";}?></td>
                <td colspan="2"><?if($amendment_value){echo ": ".$amendment_value;}?></td>
            </tr>
            <tr>
                <td>5.</td>
                <td colspan="2">TERMS OF PAYMENT</td>
                <td colspan="5">: L/C <?=$tenor; ?> DAYS (FROM <?= $last_shipment_date; ?>)</td>
            </tr>
            <tr>
                <td>6.</td>
                <td colspan="2">Remarks</td>
                <td colspan="5">: <?=$remarks; ?></td>
            </tr>
            <tr>
                <td>7.</td>
                <td colspan="2" >DESCRIPTION OF GOODS & VALUE AS BELOW</td>
                <td colspan="5">:</td>
            </tr>
            <tr >
                <td width="30" class="tblhead">SL</td>
                <td width="200" class="tblhead">STYLE</td>
				<td width="200" class="tblhead">STYLE DESCRIPTION</td>
                <td width="150" class="tblhead">Order</td>
                <td width="250" class="tblhead">ITEM DESCRIPTION</td>
                <td width="80" class="tblhead">QTY/ PCS</td>
                <td width="80" class="tblhead">PRICE</td>
                <td width="80" class="tblhead">VALUE</td>
                <td class="tblhead">SHIPMENT DATE</td>
            </tr>
            <?
            $i=1;
            $tot_attached_qnty=0;
            $tot_attached_value=0;
            foreach ($nameArray as $selectResult)
            {
				$order_qnty_in_pcs=$selectResult[csf('attached_qnty')]*$selectResult[csf('ratio')];
                ?>
                <tr>
                    <td class="tblbody"><? echo $i; ?></td>
					<td class="tblbody"><? echo $selectResult[csf('style_ref_no')]; ?></td>
					<td class="tblbody"><? echo $selectResult[csf('STYLE_DESCRIPTION')]; ?></td>
                    <td class="tblbody"><? echo $selectResult[csf('po_number')]; ?></td>
                    <td class="tblbody">
                        <?
                            $gmts_item='';
                            $gmts_item_id=explode(",",$selectResult[csf('gmts_item_id')]);
                            foreach($gmts_item_id as $item_id)
                            {
                                if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
                            }
                            echo $gmts_item;
                        ?>
                    </td>
					<td align="right" class="tblbody"><? echo $selectResult[csf('attached_qnty')]; ?></td>
					<td align="right" class="tblbody"><? echo $currency_sign.' '.number_format($selectResult[csf('attached_rate')],2); ?></td>
					<td align="right" class="tblbody"><? echo $currency_sign.' '.number_format($selectResult[csf('attached_value')],2); ?></td>
                    <td align="center" class="tblbody"><? echo change_date_format($selectResult[csf('shipment_date')]); ?></td>
                </tr>
                <?
                $i++;
                $tot_attached_qnty+=$selectResult[csf('attached_qnty')];
                $tot_attached_value+=$selectResult[csf('attached_value')];
            }	
            ?>
            <tr style="font-weight: bold;">
                <td colspan="5" align="right" style="font-size:18px;" class="tblbody">Total</td>
                <td align="right" style="font-size:18px;" class="tblbody"><? echo number_format($tot_attached_qnty,2); ?></td>
                <td align="center" style="font-size:18px;" class="tblbody">&nbsp;</td>
                <td align="right" style="font-size:18px;" class="tblbody"><? echo $currency_sign.' '.number_format($tot_attached_value,2); ?></td>
				<td align="center" style="font-size:18px;" class="tblbody">&nbsp;</td>
            </tr>
            <tr style="font-weight: bold;">
                <td colspan="8" style="font-size:18px;" class="tblbody"><? 
				$dcurrency_arr=array(1=>'Paisa',2=>'CENTS',3=>'CENTS',);
				$currency_arr=array(1=>'TK',2=>'Doller',3=>'Doller',);
				$mcurrency = $currency_arr[$currency_id];
				$dcurrency = $dcurrency_arr[$currency_id];
				echo "Total value will be [ ".number_to_words(number_format($tot_attached_value,2,".",""),$mcurrency,$dcurrency)." ]"; ?></td>
            </tr>
			<tr>
                <td>8.</td>
                <td colspan="2">CONTRACT VALIDITY</td>
                <td colspan="5">: <?=$expiry_date;?></td>
            </tr>   
			<tr>
                <td>9.</td>
                <td colspan="2">TOLERANCE</td>
                <td colspan="5">: <?= $tolerance; ?>(+/-)%</td>
            </tr>   
			<tr>
                <td>10.</td>
                <td colspan="2">PORT OF LOADING</td>
                <td colspan="5">: <?= $port_of_loading; ?></td>
            </tr>   
			<tr>
                <td>11.</td>
                <td colspan="2">PORT OF DESTINATION</td>
                <td colspan="5">: <?= $port_of_discharge; ?></td>
            </tr>   
			<tr>
                <td>12.</td>
                <td colspan="2">COUNTRY OF DESTINATION</td>
                <td colspan="5">: <?= chop($country_name,', '); ?></td>
            </tr>   
			<tr>
                <td>13.</td>
                <td colspan="2">PARTIAL SHIPMENT</td>
                <td colspan="5">: PARTIAL SHIPMENT PROHIBITED BUT ALLOWED IN COMPLETE ORDER ONLY</td>
            </tr>   
			<tr>
                <td>14.</td>
                <td colspan="2">TRANS SHIPMENT</td>
                <td colspan="5">: ALLOWED.</td>
            </tr>   
			<tr>
                <td>15.</td>
                <td colspan="2">INSURANCE</td>
                <td colspan="5">: TO BE COVERED BY BUYER.</td>
            </tr>   
			<tr>
                <td>16.</td>
                <td colspan="2">DOC PRESENTATION</td>
                <td colspan="5">: AS PER L/C TERMS</td>
            </tr>   
            <tr><td height="20"></td></tr>
			<tr>
                <td colspan="5">For & On behalf of :</td>
                <td colspan="3">For & On behalf of  </td>
            </tr>  
			<tr>
                <td colspan="5"><?= $buyer_name; ?></td>
                <td colspan="3"><?= $company_name; ?></td>
            </tr>  
			<tr>
                <td colspan="5"><? if($agent_name!=''){ echo "(".chop($agent_name,', ')." - AS AGENT)";} ?></td>
                <td colspan="3"></td>
            </tr>  
			<tr><td height="80"></td></tr>
			<tr>
                <td colspan="5">AUTHORIZED SIGNATURE.</td>
                <td colspan="3">AUTHORIZED SIGNATURE.</td>
            </tr> 
			<tr><td colspan="8" height="50"></td></tr>
        </tbody>           
    </table>  
	<!-- <hr>
	<table width="1000" cellpadding="0" cellspacing="0" border="0" >
		<tr>
			<td align="center"><?=$corporate_office;?></td>
		</tr>
		<tr>
			<td align="center"><?=$factory;?></td>
		</tr>
	</table>  -->
	</body> 
    <?
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("tb*.xls") as $filename) {
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename="tb".$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$filename****$html";
    exit();
}
if ($action == "sales_contact_check_list")  // Check List by Tipu
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
    $data_array = sql_select("SELECT * from com_sales_contract where id='$data[1]'");
    foreach ($data_array as $row) 
    {
        $company_name = $company_lib[$row[csf("beneficiary_name")]];
        $company_id = $row[csf("beneficiary_name")];
        $buyer_id = $row[csf("buyer_name")];

        $sc_applicant_name   = $buyer_lib[$row[csf("applicant_name")]]; 
        $convertible_lc   = $row[csf("convertible_to_lc")]; 
        $sc_date             = change_date_format($row[csf("contract_date")]);
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
        $sc_no               = $row[csf("contract_no")]; 
        $lc_trans_bank_ref   = $row[csf("transfering_bank_ref")];      
        $lc_transfer_bank   = $row[csf("transfer_bank")];      
        $lc_issuing_bank     = $row[csf("issuing_bank_name")];      
        $lc_expiry_date      = change_date_format($row[csf("expiry_date")]);      
        $lc_expiry_place      = $row[csf("expiry_place")];      
        $lc_export_value      = $row[csf("contract_value")];      
        $lc_negotiating_bank      = $row[csf("negotiating_bank")];      
        $lc_last_shipment_date      = change_date_format($row[csf("last_shipment_date")]);      
        $lc_remarks             = $row[csf("remarks")];      
        $lc_re_imbursing_bank      = $row[csf("re_imbursing_bank")];      
        $lc_nominated_shipp_line      = $row[csf("shipping_line")];      
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
    from com_sales_contract a, com_sales_contract_order_info b, wo_po_break_down c
    where a.id=b.com_sales_contract_id and b.wo_po_break_down_id=c.id and a.id='$data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
    foreach ($sql_lcsc as $row) 
    {
        $min_shipment_date = change_date_format($row[csf("min_shipment_date")]);
    }
    $sc_dates = strtotime("$sc_date");
    $ship_date = strtotime("$min_shipment_date");
    $datediff = $ship_date-$sc_dates;
    $sc_diff_days = $datediff / (60 * 60 * 24);  
    // LC/SC to be opened for LC/SC end

    $sql_qty="SELECT wb.id, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wm.order_uom, wm.total_set_qnty as ratio, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active
    from wo_po_break_down wb, wo_po_details_master wm, com_sales_contract_order_info ci
    where wb.job_id = wm.id and wb.id = ci.wo_po_break_down_id and ci.com_sales_contract_id='$data[1]' and ci.status_active = '1' and ci.is_deleted ='0'
    order by ci.id";
    
    $total_order_qnty_in_pcs = 0;
    $nameArray = sql_select($sql_qty);
    foreach ($nameArray as $qtyRow) 
    {
        $order_qnty_in_pcs = $qtyRow[csf('attached_qnty')] * $qtyRow[csf('ratio')];
        $total_goods_qty_pcs += $order_qnty_in_pcs;
    }                    
    // echo $total_goods_qty_pcs;

    $sql_amendment=sql_select("SELECT sum(amendment_value) as amendment_value, contract_value, value_change_by from com_sales_contract_amendment where contract_id='$data[1]' group by contract_value, value_change_by"); 
    $amendmentValue = array();
    $Org_lc_value = array();
    foreach ($sql_amendment as $row) 
    {
        $amendmentValue[$row[csf("value_change_by")]]=$row[csf("amendment_value")]; 
        $Org_lc_value[$row[csf("value_change_by")]]=$row[csf("contract_value")]; 
    }
    /*echo "<pre>";
    print_r($Org_lc_value);*/

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
			@page {size: A4 portrait;}
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
                <td align="center" style="font-size:20px;"><strong><?php echo $company_name; ?></strong></td>
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
            <tr><td align="center" style="font-size:14px" class="none"><strong>SC Scrutiny Sheet</strong></td></tr>
            <tr><td align="center" style="font-size:14px" class="none">Bank Name: <?php echo $bank_name.', Branch: '.$address; ?></td></tr>
        </table> 

        <table style="width:100%">
          <tr bgcolor="#b7b7b7">
            <th style="width:3%">Sl</th>
            <th style="width:21%">Particulars</th>
            <th style="width:38%">KYC</th> 
            <th style="width:38%">SC</th>
          </tr>
          <tr>
            <td>1</td>
            <td>Applicant Name</td>
            <td><?php echo $buyer_name; ?></td>
            <td><?php echo $sc_applicant_name; ?></td>
          </tr>
          <tr>
            <td>2</td>
            <td>Payment through</td>
            <td><?php echo $payment[$buyer_pay_through]; ?></td>
            <td><?php echo ($convertible_lc==1)?'SC to LC':'Direct SC'; ?></td>
          </tr>
          <tr>
            <td>3</td>
            <td>SC to be opened</td>
            <td><?php echo $buyer_lc_sc.' Days '.$beforeorafter[$buyer_lc_sc_shpmnt]; ?></td>
            <td><?php echo $sc_diff_days.' Days Before Shipment'; ?></td>
          </tr>
          <tr>
            <td>4</td>
            <td>Commission available</td>
            <td><?php echo $yes_no[$buyer_comm_avlbl]; ?></td>
            <td><?php echo ($lc_local_comn!='' || $lc_foreign_comn!='') ? 'Yes':'No'; ?></td>
          </tr>
          <tr>
            <td>5</td>
            <td>Commission %</td>
            <td><?php echo 'Local: '.$buyer_comm_prcnt_local.', Foreign: '.$buyer_comm_prcnt_forgn; ?></td>
            <td><?php echo 'Local: '.$lc_local_comn.', Foreign: '.$lc_foreign_comn; ?></td>
          </tr>
          <tr>
            <td>6</td>
            <td>Tolerance %</td>
            <td><?php echo $buyer_tolerance; ?></td>
            <td><?php echo $lc_tolerance; ?></td> 
          </tr> 
          <tr>
            <td>7</td>
            <td>Partial Shipment </td>
            <td><?php echo $yes_no[$buyer_partial_shpmnt]; ?></td>
            <td> </td>
          </tr>
          <tr>
            <td>8</td>
            <td>Transhipment</td>
            <td><?php echo $yes_no[$buyer_transhipment]; ?></td>
            <td> </td>
          </tr> 
            <td>9</td>
            <td>Inspection certificate</td>
            <td><?php echo $commission_particulars[$buyer_inspect_crt]; ?></td>
            <td> </td>
          </tr>
          <tr>
            <td>10</td>
            <td>Payment Term</td>
            <td><?php echo $pay_term[$buyer_payment_term]; ?></td>
            <td><?php echo $pay_term[$lc_pay_term]; ?> </td>
          </tr>
          <tr>
            <td>11</td>
            <td>Payment Tenor</td>
            <td><?php echo $buyer_tenor.' '.$beforeorafter[$buyer_tenor_shpmnt]; ?></td>
            <td><?php echo $lc_tenor; ?></td>
          </tr>
          <tr>
            <td>12</td>
            <td>Incoterm</td>
            <td><?php echo $incoterm[$buyer_incoterm] ; ?></td>
            <td><?php echo $incoterm[$lc_incoterm] ; ?></td>
          </tr>
          <tr>
            <td>13</td>
            <td>Incoterm Place</td>
            <td><?php echo $buyer_incoterm_plc ; ?></td>
            <td><?php echo $lc_incoterm_plc ; ?></td>
          </tr>
          <tr>
            <td>14</td>
            <td>Port of discharge</td>
            <td><?php echo $buyer_port_discrg ; ?></td>
            <td><?php echo $lc_port_discrg ; ?></td>
          </tr>
          <tr>
            <td>15</td>
            <td>Insurance</td>
            <td><?php echo $inspect_by[$buyer_insurance].' '.$buyer_insurance_other ; ?></td>
            <td> </td>
          </tr>
          <tr>
            <td>16</td>
            <td>Bill Negotiation</td>
            <td><?php echo $buyer_bill_neg ; ?></td>
            <td> </td>
          </tr>
          <tr>
            <td>17</td>
            <td>Penalty/ Discount Cls</td>
            <td><?php echo $buyer_penalty_dsc ; ?></td>
            <td><?php echo $lc_discount ; ?></td>
          </tr>
          <tr>
            <td>18</td>
            <td>Claim Adjustment </td>
            <td><?php echo $buyer_port_discrg ; ?></td>
            <td><?php echo $lc_claim_adjustment ; ?></td>
          </tr>
          <tr>
            <td class="none">19</td>
            <td class="none">Bill of Lading </td>
            <td class="none"><?php echo $buyer_bill_landing ; ?></td>
            <td class="none"><?php echo $lc_bill_landing ; ?></td>
          </tr>
        </table> 

        <table style="width:100%">
           <tr bgcolor="#b7b7b7">
            <td style="width:3%"><strong>20</strong></td>
            <td colspan="2"><strong>SC Details</strong></td> 
          </tr> 
            <td>1</td>
            <td style="width:47%">SC No</td>
            <td><?php echo $sc_no ; ?></td> 
          </tr>
          <tr>
            <td>2</td>
            <td>SC Opening Date</td>
            <td><?php echo $sc_date ; ?></td> 
          </tr>
          <tr>
            <td>3</td>
            <td>Second Beneficiary</td>
            <td><?php echo $company_name ; ?></td> 
          </tr>
          <tr>
            <td>4</td>
            <td>Whether is it Irrevocable</td>
            <td>IRREVOCABLE</td> 
          </tr>
          <tr>
            <td>5</td>
            <td>Expiry Date of SC</td>
            <td><?php echo $lc_expiry_date ; ?></td> 
          </tr>
          <tr>
            <td>6</td>
            <td>Amount of SC</td>
            <td><?php echo $currency_name.' '.$lc_export_value ; ?></td> 
          </tr>
          <tr>
            <td>7</td>
            <td>Last Date of Shipment</td>
            <td><?php echo $lc_last_shipment_date ; ?></td> 
          </tr>
          <tr>
            <td>8</td>
            <td>Port of discharge</td>
            <td><?php echo $lc_port_discrg ; ?></td> 
          </tr>
          <tr>
            <td>9</td>
            <td>Document Presentation days</td>
            <td><?php echo $lc_doc_presentation_days ; ?></td>  
          </tr>
          <tr>
            <td>10</td>
            <td>Exporting Goods Quantity</td>
            <td><?php echo $total_goods_qty_pcs ; ?></td> 
          </tr>
          <tr>
            <td>11</td>
            <td>Name of Particular shipping line</td>
            <td><?php echo $lc_nominated_shipp_line ; ?></td> 
          </tr>
          <tr>
            <td>12</td>
            <td>Reimbursement Bank</td>
            <td><?php echo $lc_re_imbursing_bank ; ?></td> 
          </tr>
          <tr>
            <td>13</td>
            <td>Original SC Value</td>
            <td><?php 
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
            <td>14</td>
            <td>Amendment Value(Total Increase)</td>
            <td><?php echo $amendmentValue[1]; ?></td> 
          </tr>
          <tr>
            <td>15</td>
            <td>Amendment Value(Total Decrease)</td>
            <td><?php echo $amendmentValue[2]; ?></td> 
          </tr>
          <tr>
            <td colspan="2" align="right">Current SC Value</td>  
            <td><?php if ($Org_lc_value[0]=="") 
            {
                echo $currency_name.' '.$lc_export_value;
            }
            else{
                echo $currency_name.' '.($Org_lc_value[0]-$amendmentValue[2]+$amendmentValue[1]);
            } ?></td> 
          </tr>
          <tr>
            <td colspan="2" align="right">Net SC Value</td> 
            <td><?php if ($Org_lc_value[0]=="") 
            {
                echo $currency_name.' '.$lc_export_value;
            }
            else{
                echo $currency_name.' '.($Org_lc_value[0]-$amendmentValue[2]+$amendmentValue[1]);
            } ?></td> 
          </tr>
          <tr bgcolor="#b7b7b7">
            <td><strong>21</strong></td>
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
          <tr bgcolor="#b7b7b7">
            <td><strong>22</strong></td>
            <td><strong>Remarks</strong></td>
            <td><strong><?php echo $lc_remarks; ?></strong></td> 
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

if($action == "sc_lc_search")
{
    echo load_html_head_contents("SC/LC Form", "../../../", 1, 1,'','1','');
    ?>
    <script>
       $(document).ready(function(e) {
            setFilterGrid('list_view',-1);
	});
        function js_set_value_sc(str){
            $("#js_set").val(str);
            parent.emailwindow.hide();
        }
    </script>
    <input type="hidden" id="js_set">
    <?
    echo create_list_view( "list_view", "ID,PO Number,SC/LC", "100,150,100", "500", "320", 0, "select a.id,a.po_number,a.sc_lc from wo_po_break_down a where a.sc_lc is not null and a.is_deleted = 0 and a.status_active = 1", "js_set_value_sc", "sc_lc", "",  1, "",  $data_array_name_arr, "id,po_number,sc_lc", "", "", "" );
}

if($action=="print_button_variable_setting")
{
    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=5 and report_id=155 and is_deleted=0 and status_active=1");
   	$printButton=explode(',',$print_report_format);

	foreach($printButton as $id){

		if($id==753)$buttonHtml.='<input type="button" value="Lien Letter" id="btn_lien_letter" name="btn_lien_letter" class="formbutton" style="width:100px;" onClick="fnc_lien_letter(1)" />';
		if($id==754)$buttonHtml.='<input type="button" value="Lien Letter2" id="btn_lien_letter2" name="btn_lien_letter2" class="formbutton" style="width:100px;" onClick="fnc_lien_letter(2)" />';
		if($id==755)$buttonHtml.='<input type="button" value="Lien Export Lc App" id="btn_lien_letter2" name="btn_lien_letter2" class="formbutton" style="width:120px;" onClick="fnc_lien_letter(3)" />';
		if($id==756)$buttonHtml.='<input type="button" value="Lien Lc App2" id="btn_lien_letter3" name="btn_lien_letter3" class="formbutton" style="width:120px;" onClick="fnc_lien_letter(4)" />';
		if($id==757)$buttonHtml.='<input type="button" value="Check List" id="btn_check_list" name="btn_check_list" class="formbutton" style="width:100px;" onClick="fnc_check_list()" />';
		if($id==78)$buttonHtml.='<input type="button" value="Print" id="print" name="print" class="formbutton" style="width:100px;" onClick="fnc_lien_letter(5)" />';
		if($id==466)$buttonHtml.='<input type="button" value="Lien Letter3" id="btn_lien_letter3" name="btn_lien_letter3" class="formbutton" style="width:100px;" onClick="fnc_lien_letter(6)" />';
		if($id==476)$buttonHtml.='<input type="button" value="Lien LC App3" id="btn_lien_letter3" name="btn_lien_letter3" class="formbutton" style="width:100px;" onClick="fnc_lien_letter(7)" />';
		if($id==829)$buttonHtml.='<input type="button" value="Lien Letter 4" id="btn_lien_letter10" name="btn_lien_letter10" class="formbutton" style="width:100px;" onClick="fnc_lien_letter(10)" />';
		if($id==427)$buttonHtml.='<input type="button" value="Print 12" id="btn_lien_letter12" name="btn_lien_letter12" class="formbutton" style="width:100px;" onClick="fnc_lien_letter(12)" />';
		if($id==426)$buttonHtml.='<input type="button" value="Print B23" id="btn_lien_letter13" name="btn_lien_letter13" class="formbutton" style="width:100px;" onClick="fnc_lien_letter(13)" />';
		
	}

   echo "document.getElementById('button_data_panel').innerHTML = '".$buttonHtml."';\n";
    exit();
}

if($action=="order_list_presentation")
{
	$permission=$_SESSION['page_permission'];
	//echo $data;die;
	if($data==2)
	{
		?>
        <thead>
            <tr>
                <th class="must_entry_caption" style="color:#00F">Requisition Number</th>
                <th style="display:none">Acc.requisition  No.</th>
                <th>Requisition Qty</th>
                <th>Requisition Value</th>
                <th class="must_entry_caption" style="color:#00F">Attach. Qty</th>
                <th>Rate</th>
                <th>Attach. Val.</th>
				<th>Commission Local</th>
				<th>Commission Foreign</th>
                <th>Style Ref</th>
                <th style="display:none">Style Desc.</th>
                <th>Item</th>
                <th style="display:none">Job No.</th>
                <th style="display:none">Fabric Description</th>
                <th style="display:none">Categroy</th>
                <th style="display:none">Hs Code</th>
                <th style="display:none">Brand</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <tr class="general" id="tr_1">
                <td><input type="text" name="txtordernumber_1" id="txtordernumber_1" class="text_boxes" style="width:90px"  onDblClick= "openmypage('requires/sales_contract_controller.php?action=order_popup&types=order_select_popup&buyer_id='+document.getElementById('cbo_buyer_name').value+'&selectID='+document.getElementById('hidden_selectedID').value+'&sales_contractID='+document.getElementById('txt_system_id').value+'&company_id='+document.getElementById('cbo_beneficiary_name').value+'&lc_sc_no='+document.getElementById('txt_contract_no').value,'PO Selection Form',1)" readonly= "readonly" placeholder="Double Click" value=""/>
                <input type="hidden" name="hiddenwopobreakdownid_1" id="hiddenwopobreakdownid_1" readonly value="">
                <input type="hidden" name="isSales_1" id="isSales_1" value="">
                </td>
                <td style="display:none"><input type="text" name="txtaccordernumber_1" id="txtaccordernumber_1" class="text_boxes" style="width:90px;" readonly= "readonly" /></td>
                <td><input type="text" name="txtorderqnty_1" id="txtorderqnty_1" class="text_boxes_numeric" style="width:60px;" readonly= "readonly" /></td>
                <td><input type="text" name="txtordervalue_1" id="txtordervalue_1" class="text_boxes_numeric" style="width:80px;" readonly= "readonly"/></td>
                <td><input type="text" name="txtattachedqnty_1" id="txtattachedqnty_1" class="text_boxes_numeric" style="width:60px" onKeyUp="validate_attach_qnty(1)" />
                    <input type="hidden" name="hideattachedqnty_1" id="hideattachedqnty_1" class="text_boxes_numeric" style="width:70px; text-align:right"/>
                </td>
                <td>
                    <input type="text" name="hiddenunitprice_1" id="hiddenunitprice_1" class="text_boxes_numeric" style="width:50px" onKeyUp="calculate_attach_val(1)" readonly disabled >
                </td>
                <td><input type="text" name="txtattachedvalue_1" id="txtattachedvalue_1" class="text_boxes_numeric" style="width:80px" readonly= "readonly"/></td>
				<td><input type="text" name="txtcommission_1" id="txtcommission_1" class="text_boxes_numeric" style="width:80px" readonly= "readonly"/></td>
				<td><input type="text" name="txtcommissionforeign_1" id="txtcommissionforeign_1" class="text_boxes_numeric" style="width:80px" readonly= "readonly"/></td>
                <td><input type="text" name="txtstyleref_1" id="txtstyleref_1" class="text_boxes" style="width:90px" readonly= "readonly"/></td>
                <td style="display:none"><input type="text" name="txtStyleDesc_1" id="txtStyleDesc_1" class="text_boxes" style="width:90px" readonly= "readonly"/></td>
                <td><input type="text" name="txtitemname_1" id="txtitemname_1" class="text_boxes" style="width:80px" readonly= "readonly"/></td>
                <td style="display:none"><input type="text" name="txtjobno_1" id="txtjobno_1" class="text_boxes" style="width:80px" readonly= "readonly"/></td>

                    <input type="hidden" name="hiddenwopobreakdownid_1" id="hiddenwopobreakdownid_1" readonly= "readonly" />
                    <input type="hidden" name="order_attached_qnty_1" id="order_attached_qnty_1" readonly= "readonly" />
                    <input type="hidden" name="order_attached_lc_no_1" id="order_attached_lc_no_1" readonly= "readonly" />
                    <input type="hidden" name="order_attached_lc_qty_1" id="order_attached_lc_qty_1" readonly= "readonly" />
                    <input type="hidden" name="order_attached_sc_no_1" id="order_attached_sc_no_1" readonly= "readonly" />
                    <input type="hidden" name="order_attached_sc_qty_1" id="order_attached_sc_qty_1" readonly= "readonly" />
                <td style="display:none"><input type="text" name="txtfabdescrip_1" id="txtfabdescrip_1" class="text_boxes" style="width:90px" /></td>
                <td style="display:none"><input type="text" name="txtcategory_1" id="txtcategory_1" class="text_boxes_numeric" style="width:50px" /></td>
                <td style="display:none"><input type="text" name="txthscode_1" id="txthscode_1" class="text_boxes" style="width:40px"/></td>
                <td style="display:none"><input type="text" name="txtbrand_1" id="txtbrand_1" class="text_boxes_numeric" style="width:50px" readonly= "readonly" /></td>
                <td>
                    <?
                        echo create_drop_down( "cbopostatus_1", 60, $attach_detach_array,"", 0, "", 1, "copy_all(this.value+'_'+1)" );
                    ?>
                    <input type="hidden" name="hiddensalescontractorderid_1" id="hiddensalescontractorderid_1" readonly= "readonly" />
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr class="tbl_bottom">
              <td>Total</td>
              <td style="text-align:center"><input type="text" name="totalOrderqnty" id="totalOrderqnty" class="text_boxes_numeric" style="width:60px;" readonly= "readonly" /></td>
              <td style="text-align:center"><input type="text" name="totalOrdervalue" id="totalOrdervalue" class="text_boxes_numeric" style="width:80px;" readonly= "readonly" /></td>
              <td style="text-align:center"><input type="text" name="totalAttachedqnty" id="totalAttachedqnty" class="text_boxes_numeric" style="width:60px;" readonly= "readonly" /></td>
              <td>&nbsp;</td>
              <td style="text-align:center"><input type="text" name="totalAttachedvalue" id="totalAttachedvalue" class="text_boxes_numeric" style="width:80px;" readonly= "readonly" /></td>
              <td colspan="7">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="14" height="50" valign="middle" align="center" class="button_container">
                <? echo load_submit_buttons( $permission, "fnc_po_selection_save", 0,0 ,"reset_form('salescontractfrm_2','','','txt_tot_row,0','$(\'#tbl_order_list tbody tr:not(:first)\').remove();load_po_id();','')",2) ; ?>
                 <!-- for update -->
                <input type="hidden" id="hidden_selectedID" readonly= "readonly" />
                <input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes_numeric"  readonly= "readonly" value="0" />
                </td>
            </tr>
        </tfoot>
        <?
	}
	else
	{
		?>
        <thead>
            <tr>
                <th class="must_entry_caption" style="color:#00F">Order Number</th>
                <th>Acc.PO No.</th>
                <th>Order Qty</th>
                <th>Order Value</th>
                <th class="must_entry_caption" style="color:#00F">Attach. Qty</th>
                <th>Rate</th>
                <th>Attach. Val.</th>
				<th>Commission Local</th>
				<th>Commission Foreign</th>
                <th>Style Ref</th>
                <th>Style Desc.</th>
                <th>Item</th>
                <th>Job No.</th>
                <th>Fabric Description</th>
                <th>Categroy</th>
                <th>Hs Code</th>
                <th>Brand</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <tr class="general" id="tr_1">
                <td><input type="text" name="txtordernumber_1" id="txtordernumber_1" class="text_boxes" style="width:90px"  onDblClick= "openmypage('requires/sales_contract_controller.php?action=order_popup&types=order_select_popup&buyer_id='+document.getElementById('cbo_buyer_name').value+'&selectID='+document.getElementById('hidden_selectedID').value+'&sales_contractID='+document.getElementById('txt_system_id').value+'&company_id='+document.getElementById('cbo_beneficiary_name').value+'&lc_sc_no='+document.getElementById('txt_contract_no').value,'PO Selection Form',1)" readonly= "readonly" placeholder="Double Click" value=""/>
                <input type="hidden" name="hiddenwopobreakdownid_1" id="hiddenwopobreakdownid_1" readonly value="">
                <input type="hidden" name="isSales_1" id="isSales_1" value="">
                </td>
                <td><input type="text" name="txtaccordernumber_1" id="txtaccordernumber_1" class="text_boxes" style="width:90px;" readonly= "readonly" /></td>
                <td><input type="text" name="txtorderqnty_1" id="txtorderqnty_1" class="text_boxes_numeric" style="width:60px;" readonly= "readonly" /></td>
                <td><input type="text" name="txtordervalue_1" id="txtordervalue_1" class="text_boxes_numeric" style="width:80px;" readonly= "readonly"/></td>
                <td><input type="text" name="txtattachedqnty_1" id="txtattachedqnty_1" class="text_boxes_numeric" style="width:60px" onKeyUp="validate_attach_qnty(1)" />
                    <input type="hidden" name="hideattachedqnty_1" id="hideattachedqnty_1" class="text_boxes_numeric" style="width:70px; text-align:right"/>
                </td>
                <td>
                    <input type="text" name="hiddenunitprice_1" id="hiddenunitprice_1" class="text_boxes_numeric" style="width:50px" onKeyUp="calculate_attach_val(1)" readonly disabled >
                </td>
                <td><input type="text" name="txtattachedvalue_1" id="txtattachedvalue_1" class="text_boxes_numeric" style="width:80px" readonly= "readonly"/></td>
				<td><input type="text" name="txtcommission_1" id="txtcommission_1" class="text_boxes_numeric" style="width:80px" readonly= "readonly"/></td>
				<td><input type="text" name="txtcommissionforeign_1" id="txtcommissionforeign_1" class="text_boxes_numeric" style="width:80px" readonly= "readonly"/></td>
                <td><input type="text" name="txtstyleref_1" id="txtstyleref_1" class="text_boxes" style="width:90px" readonly= "readonly"/></td>
                <td><input type="text" name="txtStyleDesc_1" id="txtStyleDesc_1" class="text_boxes" style="width:90px" readonly= "readonly"/></td>
                <td><input type="text" name="txtitemname_1" id="txtitemname_1" class="text_boxes" style="width:80px" readonly= "readonly"/></td>
                <td><input type="text" name="txtjobno_1" id="txtjobno_1" class="text_boxes" style="width:80px" readonly= "readonly"/></td>

                    <input type="hidden" name="hiddenwopobreakdownid_1" id="hiddenwopobreakdownid_1" readonly= "readonly" />
                    <input type="hidden" name="order_attached_qnty_1" id="order_attached_qnty_1" readonly= "readonly" />
                    <input type="hidden" name="order_attached_lc_no_1" id="order_attached_lc_no_1" readonly= "readonly" />
                    <input type="hidden" name="order_attached_lc_qty_1" id="order_attached_lc_qty_1" readonly= "readonly" />
                    <input type="hidden" name="order_attached_sc_no_1" id="order_attached_sc_no_1" readonly= "readonly" />
                    <input type="hidden" name="order_attached_sc_qty_1" id="order_attached_sc_qty_1" readonly= "readonly" />
                <td><input type="text" name="txtfabdescrip_1" id="txtfabdescrip_1" class="text_boxes" style="width:90px" /></td>
                 <td><input type="text" name="txtcategory_1" id="txtcategory_1" class="text_boxes_numeric" style="width:50px" /></td>
                <td><input type="text" name="txthscode_1" id="txthscode_1" class="text_boxes" style="width:40px"/></td>
                <td><input type="text" name="txtbrand_1" id="txtbrand_1" class="text_boxes_numeric" style="width:50px" readonly= "readonly" /></td>
                <td>
                    <?
                        echo create_drop_down( "cbopostatus_1", 60, $attach_detach_array,"", 0, "", 1, "copy_all(this.value+'_'+1)" );
                    ?>
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr class="tbl_bottom">
              <td>&nbsp;</td>
              <td>Total</td>
              <td><input type="text" name="totalOrderqnty" id="totalOrderqnty" class="text_boxes_numeric" style="width:60px;" readonly= "readonly" /></td>
              <td><input type="text" name="totalOrdervalue" id="totalOrdervalue" class="text_boxes_numeric" style="width:80px;" readonly= "readonly" /></td>
              <td><input type="text" name="totalAttachedqnty" id="totalAttachedqnty" class="text_boxes_numeric" style="width:60px;" readonly= "readonly" /></td>
              <td>&nbsp;</td>
              <td><input type="text" name="totalAttachedvalue" id="totalAttachedvalue" class="text_boxes_numeric" style="width:80px;" readonly= "readonly" /></td>
              <td colspan="11">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="18" height="50" valign="middle" align="center" class="button_container">
                <? echo load_submit_buttons( $permission, "fnc_po_selection_save", 0,0 ,"reset_form('salescontractfrm_2','','','txt_tot_row,0','$(\'#tbl_order_list tbody tr:not(:first)\').remove();load_po_id();','')",2) ; ?>
                <input type="hidden" name="hiddensalescontractorderid_1" id="hiddensalescontractorderid_1" readonly= "readonly" /> <!-- for update -->
                <input type="hidden" id="hidden_selectedID" readonly= "readonly" />
                <input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes_numeric"  readonly= "readonly" value="0" />
                </td>
            </tr>
        </tfoot>
        <?
	}
}
?>
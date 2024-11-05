<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------- Start-------------------------------------//

if ($action=="get_btb_limit")
{
	$nameArray=sql_select( "SELECT max_btb_limit FROM variable_settings_commercial where company_name like '$data' and variable_list=6 and is_deleted = 0 AND status_active = 1" );
 	if($nameArray)
	{
		foreach ($nameArray as $row)
		{
			echo "document.getElementById('txt_max_btb_limit').value = ".$row[csf("max_btb_limit")].";\n";
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
	}
	else
	{
		echo "$('#txt_internal_file_no').removeAttr('onDblClick');\n";
		echo "$('#txt_internal_file_no').attr('readonly',false);\n";
		echo "$('#txt_internal_file_no').removeAttr('placeholder');\n";
	}
}

if($action=="file_search")
{

	echo load_html_head_contents("Export SC Form", "../../../", 1, 1,'','1','');
	extract($_REQUEST);
	//echo $companyID;die;
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$sql="select b.file_no, a.company_name from wo_po_break_down b, wo_po_details_master a where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and a.company_name=$companyID and b.file_no>0 group by a.company_name,b.file_no";
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

if ($action=="load_drop_down_buyer_search")
{
	if($data != 0){
		echo create_drop_down( "cbo_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
		exit();
	}
	else{
		echo create_drop_down( "cbo_buyer_name", 162, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
		exit();
	}
}

if ($action=="load_drop_down_applicant_name")
{
	$sql = "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (22,23)) order by buyer_name";
 	echo create_drop_down( "txt_applicant_name", 162, $sql,"id,buyer_name", 1, "---- Select ----", 0, "" );
	exit();
}

if ($action=="load_drop_down_notifying_party")
{
	$sql = "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (4,6)) order by buyer_name";
 	echo create_drop_down( "cbo_notifying_party", 162, $sql, "id,buyer_name", 0, "", '', '');
	exit();
}

if ($action=="load_drop_down_consignee")
{
	$sql = "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (5,6,100)) order by buyer_name";
 	echo create_drop_down( "cbo_consignee", 162, $sql,"id,buyer_name", 0, "", '', '' );
	exit();
}

if ($action=="eval_multi_select")
{
	echo "set_multiselect('cbo_notifying_party*cbo_consignee','0*0','0','','0*0');\n";
	exit();
}

if($action=='populate_data_from_sales_contract')
{
	$btblc_library=return_library_array( "select id, lc_number from com_btb_lc_master_details", "id", "lc_number"  );

	$data_array=sql_select("SELECT id,contact_system_id,contract_no,contract_date,beneficiary_name,buyer_name,applicant_name,notifying_party,consignee, convertible_to_lc,lien_bank,lien_date,contract_value,currency_name,tolerance,last_shipment_date, expiry_date, shipping_mode,pay_term,inco_term,inco_term_place,contract_source,port_of_entry,port_of_loading,port_of_discharge,internal_file_no,shipping_line,doc_presentation_days,max_btb_limit,foreign_comn,local_comn,remarks,tenor,discount_clauses,converted_from,converted_btb_lc_list,claim_adjustment,bank_file_no,sc_year,bl_clause,export_item_category, initial_contract_value from com_sales_contract where id='$data'");
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
		echo "document.getElementById('txt_applicant_name').value		= '".$row[csf("applicant_name")]."';\n";
		echo "document.getElementById('cbo_notifying_party').value		= '".$row[csf("notifying_party")]."';\n";
		echo "document.getElementById('cbo_consignee').value			= '".$row[csf("consignee")]."';\n";
		echo "document.getElementById('cbo_lien_bank').value 			= '".$row[csf("lien_bank")]."';\n";
		echo "document.getElementById('txt_lien_date').value 			= '".change_date_format($row[csf("lien_date")])."';\n";
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
			echo "disable_enable_fields('txt_contract_value*txt_last_shipment_date*txt_expiry_date*cbo_shipping_mode*cbo_inco_term*txt_inco_term_place*txt_port_of_entry*txt_port_of_loading*txt_port_of_discharge*cbo_pay_term*txt_tenor*txt_claim_adjustment*txt_discount_clauses*txt_bl_clause*txt_remarks',0);\n";
		}

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
<div align="center" style="width:1030px;">
	<form name="searchscfrm"  id="searchscfrm">
		<fieldset style="width:1028px; margin-left:3px">
            <legend>Enter search words</legend>
            <table cellpadding="0" cellspacing="0" width="80%" class="rpt_table">
                <thead>
                    <th>Company</th>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th>Enter</th>
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
                        echo create_drop_down( "cbo_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$beneficiary' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
                        ?>
                     </td>
                    <td>
                        <?
                            $arr=array(1=>'SC No',2=>'File No');
                            echo create_drop_down( "cbo_search_by", 162, $arr,"", 0, "", 1, "" );
                        ?>
                    </td>
                    <td id="search_by_td">
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                        <input type="hidden" id="hidden_sales_contract_id" />
                    </td>
                     <td>
                        <input type="button" id="search_button" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $beneficiary; ?>', 'create_sc_search_list_view', 'search_div', 'sales_contract_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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

if ($action=="order_popup")
{
	echo load_html_head_contents("Sales Contract Form", "../../../", 1, 1,'','1','');
	extract($_REQUEST);
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

    </script>

</head>

<body>
<div align="center" style="width:100%;" >
	<form name="searchpofrm"  id="searchpofrm">
		<fieldset style="width:1100px">
			<table width="950" cellspacing="0" cellpadding="0" class="rpt_table">
                <thead>
                    <th>Company</th>
                    <th>Search By</th>
                    <th>Search</th>
                    <th>File No</th>
                    <th>SC/LC</th>
                    <th>Shipment Date</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;"></th>
                </thead>
                <tr class="general">
                    <td align="center">
                        <?
							echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "-- Select Company --", $company_id,"" );
						?>
                    </td>
                    <td align="center">
                        <?
                        	$arr=array(1=>'PO Number',2=>'Job No',3=>'Style Ref No',4=>'Internal Ref');
							echo create_drop_down( "cbo_search_by", 150, $arr,"",0, "--- Select ---", '',"" );
							if($cbo_export_item_category==10) $is_sales=1; else $is_sales=0;
						?>
                     </td>
                     <td align="center">
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
                         <input type="text" id="txt_sc_lc" name="txt_sc_lc" class="text_boxes" style="width:80px" placeholder="sc/lc">
                     </td>
                    <td>
                    	<input type="text" name="ship_start_date" id="ship_start_date" class="datepicker" style="width:70px;" />To
                    	<input type="text" name="ship_end_date" id="ship_end_date" class="datepicker" style="width:70px;" />
                    </td>
                    <td align="center">
                        <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_text').value+'**'+document.getElementById('hidden_type').value+'**'+document.getElementById('hidden_buyer_id').value+'**'+document.getElementById('hidden_po_selectedID').value+'**'+document.getElementById('sales_contractID').value+'**'+document.getElementById('txt_file_no').value+'**'+document.getElementById('txt_sc_lc').value + '**' + document.getElementById('ship_start_date').value + '**' + document.getElementById('ship_end_date').value+'**'+<? echo $cbo_export_item_category; ?>  , 'create_po_search_list_view', 'search_div', 'sales_contract_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" />
                    </td>
            </tr>
        </table>
        <div style="width:1100px; margin-top:5px" id="search_div" align="left"></div>
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
	$cbo_export_item_category = $data[11];
	
	if ($data[0]!=0)
	{
		if($cbo_export_item_category==10)
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
		if($cbo_export_item_category==10)
		{
			if ($data[1] == 1)
				$search_text = " and wm.job_no_prefix_num like '%" . trim($data[2]) . "%'";
			else if ($data[1] == 2)
				$search_text = " and wm.job_no_prefix_num like '%" . trim($data[2]) . "'";
			else if ($data[1] == 3)
				$search_text = " and wm.style_ref_no like '%" . trim($data[2]) . "%'";
		}
		else
		{
			/*if($data[1]==1)
				$search_text=" and wb.po_number like '".trim($data[2])."%'";*/
			if($data[1]==1){
				$ex_data = explode(',', $data[2]);
				$search_text=' and (';
				foreach ($ex_data as $val) {
					$search_text.="wb.po_number like '".trim($val)."%'".' or ';
				}
				$search_text=rtrim($search_text,' or');
				$search_text.=')';				
			}				
			else if($data[1]==2)
				$search_text=" and wm.job_no like '".trim($data[2])."%'";
			else if($data[1]==3)
				$search_text=" and wm.style_ref_no like '".trim($data[2])."%'";
			else if($data[1]==4)
			$search_text = " and wb.grouping like '%" . trim($data[2]) . "%'";
		}
	}
	//echo $search_text;die;
	$action_types = $data[3];
	$buyer_id = $data[4];
	$sales_contractID = $data[6];
	$txt_file_no = $data[7];
    $txt_sc_lc = $data[8];
	$ship_start_date = $data[9];
    $ship_end_date = $data[10];
	
	if($db_type==0)
	{
		$attached_po_id=return_field_value("group_concat(wo_po_break_down_id)","com_sales_contract_order_info","com_sales_contract_id=$sales_contractID and status_active=1 and is_deleted=0");
	}
	else
	{
		/*$attached_po_id=return_field_value("rtrim(xmlagg(xmlelement(e,wo_po_break_down_id,',').extract('//text()') order by wo_po_break_down_id).GetClobVal(),',') AS po_id","com_sales_contract_order_info","com_sales_contract_id=$sales_contractID and status_active=1 and is_deleted=0","po_id");

		 if(is_null ( $attached_po_id ))
		 {
		 	echo "a";
		 }
		 else{
		 	echo "b";
		 }*/
		//echo "select rtrim(xmlagg(xmlelement(e,wo_po_break_down_id,',').extract('//text()') order by wo_po_break_down_id).GetClobVal(),',') AS po_id from com_sales_contract_order_info where com_sales_contract_id=$sales_contractID and status_active=1 and is_deleted=0";//die;

		$sql = "select wo_po_break_down_id AS po_id from com_sales_contract_order_info where com_sales_contract_id=$sales_contractID and status_active=1 and is_deleted=0";
		//echo  $sql; die;
		$attached_po_sql = sql_select($sql);

		foreach($attached_po_sql as $row)
		{
			$attached_po_id .=$row[csf('po_id')].',';
		}
		$attached_po_id=chop($attached_po_id,',');
        
	}

	if($attached_po_id !=""){
		if($data[5] !="") $attached_po_id.=",".$data[5];
	}else{
		if($data[5] !="") $attached_po_id = $data[5];
	}
	
	if($cbo_export_item_category!=10)
	{
		if($txt_file_no!="") $file_no_cond=" and wb.file_no='$data[7]'"; else $file_no_cond="";
    	if($txt_sc_lc!="") $txt_sc_lc_cond=" and wb.sc_lc like '%".trim($data[8])."%'"; else $txt_sc_lc_cond="";
		if($db_type==0)
		{
			$actual_po_arr=return_library_array("select po_break_down_id, group_concat(acc_po_no) as acc_po_no from wo_po_acc_po_info where status_active=1 and is_deleted=0 group by po_break_down_id","po_break_down_id","acc_po_no");
		}
		else
		{
			$actual_po_arr=return_library_array("select po_break_down_id, listagg( cast(acc_po_no as varchar(4000)), ',') within group(order by acc_po_no) as acc_po_no from wo_po_acc_po_info where status_active=1 and is_deleted=0 group by po_break_down_id","po_break_down_id","acc_po_no");
		}
	}
	$selected_order_id = ""; 
	if($attached_po_id !="")
	{
		if($cbo_export_item_category==10) $selected_order_id = "and wm.id not in (".$attached_po_id.")"; else $selected_order_id = "and wb.id not in (".$attached_po_id.")";
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

	if($action_types=='attached_po_status' && $cbo_export_item_category!=10)
	{
		$lc_details=return_library_array( "select id, export_lc_no from com_export_lc",'id','export_lc_no');
		$sc_details=return_library_array( "select id, contract_no from com_sales_contract",'id','contract_no');

		$lc_array=array(); $sc_array=array(); $attach_qnty_array=array();
		$sql_lc_sc="select com_export_lc_id as id, wo_po_break_down_id, sum(attached_qnty) as qnty, 1 as type from com_export_lc_order_info where status_active=1 and is_deleted=0 group by wo_po_break_down_id, com_export_lc_id
		union all
		select com_sales_contract_id as id, wo_po_break_down_id, sum(attached_qnty) as qnty, 2 as type from com_sales_contract_order_info where status_active=1 and is_deleted=0 group by wo_po_break_down_id, com_sales_contract_id
		";
		$lc_sc_Array=sql_select($sql_lc_sc);
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

		$sql = "SELECT wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst, wb.file_no, wm.style_ref_no, wm.gmts_item_id, wb.unit_price,wb.sc_lc  FROM wo_po_break_down wb, wo_po_details_master wm WHERE wb.job_no_mst = wm.job_no and wm.buyer_name like '$buyer_id' $company $search_text $file_no_cond $txt_sc_lc_cond and wb.is_deleted = 0 AND wb.status_active = 1 and wm.is_deleted = 0 AND wm.status_active = 1 $date  group by wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date, wb.job_no_mst, wb.file_no, wm.style_ref_no, wm.gmts_item_id, wb.unit_price, wb.sc_lc";
		//echo $sql."<br>"; die;
		 //and wb.is_confirmed=1
		?>
        <div>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1060" class="rpt_table" >
                <thead>
                    <th width="30">SL</th>
                    <th width="100">PO No</th>
                    <th width="100">Acc.PO No</th>
                    <th width="110">Item</th>
                    <th width="100">Style No</th>
                    <th width="60">PO Quantity</th>
                    <th>Rate</th>
                    <th width="90">Price</th>
                    <th width="70">Shipment Date</th>
                    <th width="100">Attached With</th>
                    <th width="100">LC/SC</th>
                    <th width="80" >File No</th>
                    <th width="80">SC/LC no.</th>
                </thead>
            </table>
            <div style="width:1060px; overflow-y:scroll; max-height:250px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="962" class="rpt_table" id="tbl_list_search" >
                <?
					$i=1;
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
											<td width="100"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
                                            <td width="100"><p><? echo $actual_po_arr[$selectResult[csf('id')]]; ?>&nbsp;</p></td>
											<td width="110">
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
											<td width="100"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
											<td width="60" align="right"><? echo $selectResult[csf('po_quantity')]; ?></td>
											<td align="right"><? echo number_format(($selectResult[csf('po_total_price')]/$selectResult[csf('po_quantity')]),2); ?></td>
											<td width="90" align="right"><? echo number_format($selectResult[csf('po_total_price')],2); ?></td>
											<td align="center" width="70"><? echo change_date_format($selectResult[csf('shipment_date')]); ?></td>
											<td width="100"><p><? echo $lc_details[$lc_id]; ?></p></td>
											<td align="center" width="100"><? echo 'LC'; ?></td>
                                            <td width="80"><p><? echo $selectResult[csf('file_no')]; ?>&nbsp;</p></td>
                                            <td width="80"><p><? echo $selectResult[csf('sc_lc')]; ?>&nbsp;</p></td>
										</tr>
									<?
									$i++;
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
											<td width="100"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
                                            <td width="100"><p><? echo $actual_po_arr[$selectResult[csf('id')]]; ?>&nbsp;</p></td>
											<td width="110">
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
											<td width="100"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
											<td width="80" align="right"><? echo $selectResult[csf('po_quantity')]; ?></td>
											<td width="90" align="right"><? echo number_format($selectResult[csf('po_total_price')],2); ?></td>
											<td align="center" width="70"><? echo change_date_format($selectResult[csf('shipment_date')]); ?></td>
											<td width="100"><p><? echo $sc_details[$sc_id]; ?></p></td>
											<td align="center" width="100"><? echo 'SC'; ?></td>
                                            <td width="80"><p><? echo $selectResult[csf('file_no')]; ?>&nbsp;</p></td>
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

	if($action_types=='order_select_popup')
	{
		
		if($cbo_export_item_category==10)
		{
			$sql = "SELECT wm.id, wm.job_no as po_number, sum(wb.amount) as po_total_price, sum(wb.finish_qty) as po_quantity, wm.delivery_date as shipment_date, wb.job_no_mst, 0 as file_no, wm.job_no_prefix_num, $select_field wm.style_ref_no, 0 as gmts_item_id, avg(wb.avg_rate) as unit_price, 0 as sc_lc 
			FROM fabric_sales_order_mst wm, fabric_sales_order_dtls wb  
			WHERE wm.id = wb.mst_id and wm.buyer_id like '$buyer_id' and wb.is_deleted = 0 AND wb.status_active = 1 and wm.is_deleted = 0 AND wm.status_active = 1 and wm.within_group=2 $selected_order_id $company $search_text $file_no_cond $txt_sc_lc_cond $date 
			group by  wm.id, wm.job_no, wm.delivery_date, wb.job_no_mst, wm.job_no_prefix_num, wm.insert_date, wm.style_ref_no";
		}
		else
		{
			 $sql = "SELECT wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst, wb.file_no, wm.job_no_prefix_num, $select_field wm.style_ref_no, wm.gmts_item_id, wb.unit_price, wb.sc_lc ,wb.grouping 
			FROM wo_po_break_down wb, wo_po_details_master wm 
			WHERE wb.job_no_mst = wm.job_no and wm.buyer_name like '$buyer_id' $selected_order_id $company $search_text $file_no_cond $txt_sc_lc_cond and wb.is_deleted = 0 AND wb.status_active = 1 and wb.is_confirmed=1 $date group by wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date, wb.job_no_mst, wb.file_no, wm.job_no_prefix_num, wm.insert_date, wm.style_ref_no, wm.gmts_item_id, wb.unit_price, wb.sc_lc ,wb.grouping ";
			/*$sql = "SELECT wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst, wb.file_no, wm.style_ref_no, wm.gmts_item_id, wb.unit_price,wb.sc_lc 
			FROM wo_po_break_down wb, wo_po_details_master wm 
			WHERE wb.job_no_mst = wm.job_no and wm.buyer_name like '$buyer_id' $selected_order_id $company $search_text $file_no_cond $txt_sc_lc_cond and wb.is_deleted = 0 AND wb.status_active = 1 and wm.is_deleted = 0 AND wm.status_active = 1 and wb.is_confirmed=1 $date  group by wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date, wb.job_no_mst, wb.file_no, wm.style_ref_no, wm.gmts_item_id, wb.unit_price,wb.sc_lc";*/
		}
		
		//echo $ship_start_date;die;
		
		// AND wb.status_active = 1
		//echo $sql."<br>";
	 ?>
        <div>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1080" class="rpt_table" >
                <thead>
                    <th width="40">SL</th>
                    <th width="110">PO No</th>
                    <th width="110">Internal Ref</th>
                    <th width="120">Acc.PO No</th>
                    <th width="120">Item</th>
                    <th width="120">Style No</th>
                    <th width="80">PO Quantity</th>
                    <th width="50">Rate</th>
                    <th width="100">Price</th>
                    <th width="70">Shipment Date</th>
                    <th width="70">File No</th>
                    <th>SC/LC</th>
                </thead>
            </table>
            <div style="width:1100px; overflow-y:scroll; max-height:220px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1080" class="rpt_table" id="tbl_list_search" >
                <?
					$i=1;
					if($cbo_export_item_category == 10)
					{
						$is_sales_cond=" and is_sales=1";
						$is_sales=1;
					}
					else 
					{
						$is_sales_cond=" and is_sales=0";
						$is_sales=0;
					}
					$lc_attached_qnty_arr = return_library_array("select wo_po_break_down_id, sum(attached_qnty) as qty from com_export_lc_order_info where status_active = 1 and is_deleted=0 $is_sales_cond group by wo_po_break_down_id","wo_po_break_down_id","qty");
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
                                <td width="110"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
                                <td width="110"><p><? echo $selectResult[csf('grouping')]; ?></p></td>
                                <td width="120"><p><? echo $actual_po_arr[$selectResult[csf('id')]]; ?>&nbsp;</p></td>
                                <td width="120">
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
                                <td width="120"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
                                <td width="80" align="right"><? echo $selectResult[csf('po_quantity')]; ?></td>
                                <td width="50" align="right"><? echo number_format(($selectResult[csf('po_total_price')]/$selectResult[csf('po_quantity')]),2); ?></td>
                                <td width="100" align="right"><? echo number_format($selectResult[csf('po_total_price')],2); ?></td>
                                <td align="center" width="70"><? echo change_date_format($selectResult[csf('shipment_date')]); ?></td>
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
            <table width="1060" cellspacing="0" cellpadding="0" style="border:none" align="center">
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

	/*if($db_type==0)
	{
		$actual_po_arr=return_library_array("select po_break_down_id, group_concat(acc_po_no) as acc_po_no from wo_po_acc_po_info where status_active=1 and is_deleted=0 group by po_break_down_id","po_break_down_id","acc_po_no");
	}
	else
	{
		$actual_po_arr=return_library_array("select po_break_down_id, listagg( cast(acc_po_no as varchar(4000)), ',') within group(order by acc_po_no) as acc_po_no from wo_po_acc_po_info where status_active=1 and is_deleted=0 group by po_break_down_id","po_break_down_id","acc_po_no");
	}

	$sql="select wb.id, ci.id as idd, wm.gmts_item_id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst, wm.job_no_prefix_num, wm.style_ref_no, wm.order_uom, wm.total_set_qnty as ratio, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active from wo_po_break_down wb, wo_po_details_master wm, com_sales_contract_order_info ci where wb.job_no_mst = wm.job_no and wb.id=ci.wo_po_break_down_id and ci.com_sales_contract_id='$data' and ci.status_active = '1' and ci.is_deleted = '0' and wb.is_deleted = 0 and wb.status_active = 1 and wm.is_deleted = 0 and wm.status_active = 1 order by ci.id";*/
	
	$is_sales=return_field_value("is_sales","com_sales_contract_order_info","com_sales_contract_id=$data and status_active=1","is_sales");
	//echo $is_sales.test;die;
	if($is_sales==0)
	{
		if ($db_type == 0) {
			$actual_po_arr = return_library_array("select po_break_down_id, group_concat(acc_po_no) as acc_po_no from wo_po_acc_po_info where status_active=1 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "acc_po_no");
		} else {
			$actual_po_arr = return_library_array("select po_break_down_id, listagg( cast(acc_po_no as varchar(4000)), ',') within group(order by acc_po_no) as acc_po_no from wo_po_acc_po_info where status_active=1 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "acc_po_no");
		}
	
		$sql = "select wb.id, ci.id as idd, wm.gmts_item_id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst, wm.style_ref_no, wm.order_uom, wm.total_set_qnty as ratio, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active from wo_po_break_down wb, wo_po_details_master wm, com_sales_contract_order_info ci where wb.job_no_mst = wm.job_no and wb.id=ci.wo_po_break_down_id and ci.com_sales_contract_id='$data' and ci.status_active = '1' and ci.is_deleted = '0' order by ci.id";
	}
	else
	{
		$sql = "select wm.id, ci.id as idd, 0 as gmts_item_id, wm.job_no as po_number, sum(wb.amount) as po_total_price, sum(wb.finish_qty) as po_quantity, wm.delivery_date as shipment_date, wb.job_no_mst, wm.style_ref_no, wb.order_uom, 1 as ratio, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active 
		from fabric_sales_order_dtls wb, fabric_sales_order_mst wm, com_sales_contract_order_info ci 
		where wm.id = wb.mst_id and wb.mst_id=ci.wo_po_break_down_id and ci.com_sales_contract_id='$data' and ci.status_active = '1' and ci.is_deleted = '0'
		group by  wm.id, ci.id, wm.job_no, wm.delivery_date, wb.job_no_mst, wm.style_ref_no, wb.order_uom, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active 
		order by ci.id";
	}


//echo $sql; die;
	/*$arr=array(9=>$attach_detach_array);
	echo create_list_view("list_view", "Order Number,Order Qty,Order Value,Attached Qty,Rate,Attached Value,Style Ref,Item,Job No,Status", "100,100,100,100,60,100,150,150,100,80","1050","200",0, $sql, "get_php_form_data", "idd", "'populate_order_details_form_data'", 0, "0,0,0,0,0,0,0,0,0,status_active", $arr, "po_number,po_quantity,po_total_price,attached_qnty,attached_rate,attached_value,style_ref_no,style_description,job_no_prefix_num,status_active", "requires/sales_contract_controller",'','0,1,1,1,2,2,0,0,0,0','1,po_quantity,po_total_price,attached_qnty,0,attached_value,0,0,0,0');*/

	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1200" class="rpt_table" >
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
                <th width="130">Gmts. Item</th>
                <th width="80">Job No</th>
                <th>Status</th>
            </thead>
        </table>
        <div style="width:1200px; overflow-y:scroll; max-height:200px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1182" class="rpt_table" id="tbl_list_search" >
            <?
                $i=1;
                $nameArray=sql_select( $sql );
                foreach ($nameArray as $selectResult)
                {
                    if($i%2==0) $bgcolor="#E9F3FF";
                    else $bgcolor="#FFFFFF";

					$order_qnty_in_pcs=$selectResult[csf('attached_qnty')]*$selectResult[csf('ratio')];
					$total_order_qnty_in_pcs+=$order_qnty_in_pcs;
					$total_attc_value+=$selectResult[csf('attached_value')];

                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="get_php_form_data('<? echo $selectResult[csf('idd')]."_".$is_sales; ?>','populate_order_details_form_data','requires/sales_contract_controller')">
                        <td width="100"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
                        <td width="100"><p><? echo $actual_po_arr[$selectResult[csf('id')]]; ?></p></td>
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
                        <td width="80"><? echo $selectResult[csf('job_no_mst')]; ?></td>
                        <td><? echo $attach_detach_array[$selectResult[csf('status_active')]]; ?></td>
                    </tr>
                <?
                	$i++;
                }
                ?>
            </table>
        </div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1200" class="rpt_table">
        	<tfoot>
            	<th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="50">&nbsp;</th>
                <th width="60">Total</th>
                <th width="100" align="right" id="totalAttachedqnty"><? echo number_format($total_attc_value,2); ?></th>
                <th width="100" align="right" id="totalOrderqnty"><? echo number_format($total_order_qnty_in_pcs,0); ?></th>
                <th width="120">&nbsp;</th>
                <th width="130">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th>&nbsp;</th>
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
		if($db_type==0)
		{
			$actual_po_arr=return_library_array("select po_break_down_id, group_concat(acc_po_no) as acc_po_no from wo_po_acc_po_info where status_active=1 and is_deleted=0 group by po_break_down_id","po_break_down_id","acc_po_no");
		}
		else
		{
			$actual_po_arr=return_library_array("select po_break_down_id, listagg( cast(acc_po_no as varchar(4000)), ',') within group(order by acc_po_no) as acc_po_no from wo_po_acc_po_info where status_active=1 and is_deleted=0 group by po_break_down_id","po_break_down_id","acc_po_no");
		}
	
		$data_array=sql_select("select wb.id, ci.id as idd, wm.style_description, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date,wb.job_no_mst, wm.style_ref_no, wm.gmts_item_id, wb.unit_price,ci.attached_qnty,ci.attached_rate, ci.attached_value, ci.status_active, ci.com_sales_contract_id, ci.fabric_description, ci.category_no, ci.hs_code from wo_po_break_down wb, wo_po_details_master wm, com_sales_contract_order_info ci where wb.job_no_mst = wm.job_no and wb.id=ci.wo_po_break_down_id and ci.id='$lc_attch_id' and ci.status_active = '1' and ci.is_deleted = '0' and wb.is_deleted = 0 and wb.status_active = 1 and wm.is_deleted = 0 and wm.status_active = 1 order by ci.id");
	}
	else
	{
		/*$data_array=sql_select("select wb.id, ci.id as idd, wm.style_description, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date,wb.job_no_mst, wm.style_ref_no, wm.gmts_item_id, wb.unit_price,ci.attached_qnty,ci.attached_rate, ci.attached_value, ci.status_active, ci.com_sales_contract_id, ci.fabric_description, ci.category_no, ci.hs_code from wo_po_break_down wb, wo_po_details_master wm, com_sales_contract_order_info ci where wb.job_no_mst = wm.job_no and wb.id=ci.wo_po_break_down_id and ci.id='$lc_attch_id' and ci.status_active = '1' and ci.is_deleted = '0' and wb.is_deleted = 0 and wb.status_active = 1 and wm.is_deleted = 0 and wm.status_active = 1 order by ci.id");*/
		$data_array = sql_select("select wm.id, ci.id as idd, 0 as style_description, wm.job_no as po_number, sum(wb.amount) as po_total_price, sum(wb.finish_qty) as po_quantity, wm.delivery_date as shipment_date, wb.job_no_mst, wm.style_ref_no, 0 as gmts_item_id, avg(wb.avg_rate) as unit_price, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.com_sales_contract_id, ci.fabric_description, ci.category_no, ci.hs_code 
		from fabric_sales_order_dtls wb, fabric_sales_order_mst wm, com_sales_contract_order_info ci 
		where wm.id = wb.mst_id and wb.mst_id=ci.wo_po_break_down_id and ci.id='$lc_attch_id' and ci.status_active = '1' and ci.is_deleted = '0'
		group by  wm.id, ci.id, wm.job_no, wm.delivery_date, wb.job_no_mst, wm.style_ref_no, wb.order_uom, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.com_sales_contract_id, ci.fabric_description, ci.category_no, ci.hs_code 
		order by ci.id");
	}
	

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
		echo "document.getElementById('txtstyleref_1').value 				= '".$row[csf("style_ref_no")]."';\n";
		echo "document.getElementById('txtitemname_1').value 				= '".$gmts_item."';\n";
		echo "document.getElementById('txtjobno_1').value 				= '".$row[csf("job_no_mst")]."';\n";
		echo "document.getElementById('cbopostatus_1').value 				= '".$row[csf("status_active")]."';\n";
		echo "document.getElementById('txtfabdescrip_1').value 				= '".$row[csf("fabric_description")]."';\n";
		echo "document.getElementById('txtcategory_1').value 				= '".$row[csf("category_no")]."';\n";
		echo "document.getElementById('txthscode_1').value 				= '".$row[csf("hs_code")]."';\n";

		echo "document.getElementById('hiddenwopobreakdownid_1').value 		= '".$row[csf("id")]."';\n";
		echo "document.getElementById('hiddensalescontractorderid').value 	= '".$row[csf("idd")]."';\n";
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

		$sql_lc="SELECT a.export_lc_no, sum(b.attached_qnty) as at_qt,sum(b.attached_value) as at_val FROM com_export_lc a, com_export_lc_order_info b WHERE a.id=b.com_export_lc_id and b.wo_po_break_down_id='".$row[csf("id")]."' and b.status_active = 1 and b.is_deleted=0 group by a.id, a.export_lc_no";
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

	if($data!="")
	{
		if($is_sales==0)
		{
			$data_array = "SELECT wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst, wm.style_ref_no, wm.style_description, wb.unit_price FROM wo_po_break_down wb, wo_po_details_master wm WHERE wb.job_no_mst = wm.job_no AND wb.id in ($data) AND wb.is_deleted = 0 AND wb.status_active = 1";
			/*$data_array = "SELECT wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst, wm.style_ref_no, wm.style_description, wb.unit_price FROM wo_po_break_down wb, wo_po_details_master wm WHERE wb.job_no_mst = wm.job_no AND wb.id in ($data) and wb.is_deleted = 0 AND wb.status_active = 1 and wm.is_deleted = 0 AND wm.status_active = 1";*/
		}
		else
		{
			$data_array = "SELECT wm.id, wm.job_no as po_number, sum(wb.amount) as po_total_price, sum(wb.finish_qty) as po_quantity, wm.delivery_date as shipment_date, wb.job_no_mst, wm.style_ref_no, 0 as style_description, avg(wb.avg_rate) as unit_price 
			FROM fabric_sales_order_mst wm, fabric_sales_order_dtls wb 
			WHERE wm.id = wb.mst_id and wb.is_deleted = 0 AND wb.status_active = 1 and wm.is_deleted = 0 AND wm.status_active = 1 and wm.within_group=2 and wm.id in ($data)
			group by wm.id, wm.job_no, wm.delivery_date, wb.job_no_mst, wm.style_ref_no";
		}

		$data_array=sql_select($data_array);
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

			$sql_lc="SELECT a.export_lc_no, sum(b.attached_qnty) as at_qt,sum(b.attached_value) as at_val FROM com_export_lc a, com_export_lc_order_info b WHERE a.id=b.com_export_lc_id and b.wo_po_break_down_id='".$row[csf("id")]."' and b.is_sales=$is_sales and b.status_active = 1 and b.is_deleted=0 group by a.id, a.export_lc_no";
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

			?>
			<tr class="general" id="tr_<? echo $table_row; ?>">
				<td>
					<input type="text" name="txtordernumber_<? echo $table_row; ?>" id="txtordernumber_<? echo $table_row; ?>" class="text_boxes" style="width:100px"  value="<? echo $row[csf("po_number")]; ?>" onDblClick= "openmypage('requires/sales_contract_controller.php?action=order_popup&types=order_select_popup&buyer_id='+document.getElementById('cbo_buyer_name').value+'&selectID='+document.getElementById('hidden_selectedID').value+'&sales_contractID='+document.getElementById('txt_system_id').value+'&company_id='+document.getElementById('cbo_beneficiary_name').value,'PO Selection Form','<? echo $table_row; ?>')" readonly= "readonly" placeholder="Double Click" />
					<input type="hidden" name="hiddenwopobreakdownid_<? echo $table_row; ?>" id="hiddenwopobreakdownid_<? echo $table_row; ?>" readonly= "readonly" value="<? echo $row[csf("id")]; ?>" />
                    <input type="hidden" name="isSales_<? echo $table_row; ?>" id="isSales_<? echo $table_row; ?>" value="<? echo $is_sales; ?>" />
				</td>
                <td><input type="text" name="txtaccordernumber_<? echo $table_row; ?>" id="txtaccordernumber_<? echo $table_row; ?>" class="text_boxes" style="width:100px;" readonly= "readonly" value="<? echo $actual_po_arr[$row[csf("id")]]; ?>" /></td>
				<td>
					<input type="text" name="txtorderqnty_<? echo $table_row; ?>" id="txtorderqnty_<? echo $table_row; ?>" class="text_boxes" style="width:65px; text-align:right" readonly= "readonly" value="<? echo $row[csf("po_quantity")]; ?>" />
				</td>
				<td>
					<input type="text" name="txtordervalue_<? echo $table_row; ?>" id="txtordervalue_<? echo $table_row; ?>" class="text_boxes" style="width:80px; text-align:right" readonly= "readonly" value="<? echo number_format($row[csf("po_total_price")],2,'.',''); ?>" />
				</td>
				<td>
					<input type="text" name="txtattachedqnty_<? echo $table_row; ?>" id="txtattachedqnty_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:65px" onKeyUp="validate_attach_qnty(<? echo $table_row; ?>)" value="<? echo $remaining_qnty; ?>" />
					<input type="hidden" name="hideattachedqnty_<? echo $table_row; ?>" id="hideattachedqnty_<? echo $table_row;?>" class="text_boxes_numeric" value="<? echo $remaining_qnty; ?>"/>
				</td>
				<td>
                    <input type="text" name="hiddenunitprice_<? echo $table_row; ?>" id="hiddenunitprice_<? echo $table_row; ?>" value="<? echo $row[csf("unit_price")]; ?>" style="width:50px" class="text_boxes_numeric" onKeyUp="calculate_attach_val(<? echo $table_row; ?>)"  readonly disabled />
				</td>
				<td>
					<input type="text" name="txtattachedvalue_<? echo $table_row; ?>" id="txtattachedvalue_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:80px" readonly= "readonly" value="<? echo number_format($remaining_value,2,'.',''); ?>" />
				</td>
				<td>
					<input type="text" name="txtstyleref_<? echo $table_row; ?>" id="txtstyleref_<? echo $table_row; ?>" class="text_boxes" style="width:90px" readonly= "readonly" value="<? echo $row[csf("style_ref_no")]; ?>" />
				</td>
				<td>
					<input type="text" name="txtitemname_<? echo $table_row; ?>" id="txtitemname_<? echo $table_row; ?>" class="text_boxes" style="width:110px" readonly= "readonly" value="<? echo $row[csf("style_description")]; ?>" />
				</td>
				<td>
					<input type="text" name="txtjobno_<? echo $table_row; ?>" id="txtjobno_<? echo $table_row; ?>" class="text_boxes" style="width:80px" readonly= "readonly" value="<? echo $row[csf("job_no_mst")]; ?>"  />
				</td>
                <td><input type="text" name="txtfabdescrip_<? echo $table_row; ?>" id="txtfabdescrip_<? echo $table_row; ?>" class="text_boxes" style="width:90px" /></td>
                <td>
					<input type="text" name="txtcategory_<? echo $table_row; ?>" id="txtcategory_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:50px"  />
				</td>
                <td>
					<input type="text" name="txthscode_<? echo $table_row; ?>" id="txthscode_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:40px" />
				</td>
				<td>
					<?
						 echo create_drop_down( "cbopostatus_".$table_row, 60, $attach_detach_array,"", $row[csf("status_active")], "", 1, "" );
					?>
				</td>
               		<input type="hidden" name="order_attached_qnty_<? echo $table_row; ?>" id="order_attached_qnty_<? echo $table_row; ?>" value="<? echo $order_attached_qnty; ?>" readonly= "readonly" />
                    <input type="hidden" name="order_attached_lc_no_<? echo $table_row; ?>" id="order_attached_lc_no_<? echo $table_row; ?>" value="<? echo $lc_no; ?>" readonly= "readonly" />
                    <input type="hidden" name="order_attached_lc_qty_<? echo $table_row; ?>" id="order_attached_lc_qty_<? echo $table_row; ?>" value="<? echo $order_attahed_qnty_lc; ?>" readonly= "readonly" />
                    <input type="hidden" name="order_attached_sc_no_<? echo $table_row; ?>" id="order_attached_sc_no_<? echo $table_row; ?>" value="<? echo $sc_no; ?>" readonly= "readonly" />
                    <input type="hidden" name="order_attached_sc_qty_<? echo $table_row; ?>" id="order_attached_sc_qty_<? echo $table_row; ?>" value="<? echo $order_attahed_qnty_sc; ?>" readonly= "readonly" />
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
	}

	if($db_type==0) $year_field="YEAR(insert_date) as year,";
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year,";
	else $year_field="";//defined Later

	$sql = "select id,contract_no, internal_file_no, $year_field contact_prefix_number, contact_system_id, beneficiary_name,buyer_name, applicant_name,contract_value, lien_bank,pay_term, last_shipment_date,contract_date from com_sales_contract where status_active=1 and is_deleted=0 $company_id $buyer_id $search_condition order by id";
	//echo $sql;die;
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$bank_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
	$arr=array (4=>$comp,5=>$buyer_arr,6=>$buyer_arr,8=>$bank_arr,9=>$pay_term);
	echo create_list_view("list_view", "Contract No,File No,Year,System ID,Company,Buyer Name,Applicant Name,Contract Value,Lien Bank,Pay Term,Last Ship Date,Contract Date", "80,80,60,70,70,70,70,100,110,70,80,70","1025","320",0, $sql, "js_set_value", "id", "", 1, "0,0,0,0,beneficiary_name,buyer_name,applicant_name,0,lien_bank,pay_term,0,0", $arr , "contract_no,internal_file_no,year,contact_prefix_number,beneficiary_name,buyer_name,applicant_name,contract_value,lien_bank,pay_term,last_shipment_date,contract_date", "",'','0,0,0,0,0,0,0,2,0,0,3,3') ;

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
		if (is_duplicate_field( "contract_no", "com_sales_contract", "contract_no=$txt_contract_no and beneficiary_name=$cbo_beneficiary_name and status_active=1 and buyer_name=$cbo_buyer_name and lien_bank=$cbo_lien_bank" )==1)
		{
			echo "11**0";disconnect($con);
			die;
		}

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

		$field_array="id,contact_prefix,contact_prefix_number,contact_system_id,contract_no,contract_date,beneficiary_name,buyer_name,applicant_name,notifying_party,consignee,convertible_to_lc,lien_bank, lien_date,tolerance,maximum_tolarence, minimum_tolarence, last_shipment_date,expiry_date, shipping_mode,pay_term,inco_term,inco_term_place, contract_source, port_of_entry, port_of_loading, port_of_discharge, internal_file_no,shipping_line, doc_presentation_days, max_btb_limit,max_btb_limit_value, foreign_comn, foreign_comn_value, local_comn, local_comn_value, remarks, discount_clauses, tenor,currency_name, contract_value,converted_from, converted_btb_lc_list,claim_adjustment, bank_file_no,sc_year,bl_clause, export_item_category, inserted_by,insert_date,status_active";

		$data_array="(".$id.",'".$new_contact_system_id[1]."',".$new_contact_system_id[2].",'".$new_contact_system_id[0]."',".$txt_contract_no.",".$txt_contract_date.",".$cbo_beneficiary_name.",".$cbo_buyer_name.",".$txt_applicant_name.",".$cbo_notifying_party.",".$cbo_consignee.",".$cbo_convertible_to_lc.",".$cbo_lien_bank.",".$txt_lien_date.",".$txt_tolerance.",".$maximum_tolarence.",".$minimum_tolarence.",".$txt_last_shipment_date.",".$txt_expiry_date.",".$cbo_shipping_mode.",".$cbo_pay_term.",".$cbo_inco_term.",".$txt_inco_term_place.",".$cbo_contract_source.",".$txt_port_of_entry.",".$txt_port_of_loading.",".$txt_port_of_discharge.",".$txt_internal_file_no.",".$txt_shipping_line.",".$txt_doc_presentation_days.",".$txt_max_btb_limit.",".$max_btb_limit_value.",".$txt_foreign_comn.",".$foreign_comn_value.",".$txt_local_comn.",".$local_comn_value.",".$txt_remarks.",".$txt_discount_clauses.",".$txt_tenor.",".$cbo_currency_name.",".$txt_contract_value.",".$txt_converted_from_id.",".$txt_converted_btb_id.",".$txt_claim_adjustment.",".$txt_bank_file_no.",".$txt_year.",".$txt_bl_clause.",".$cbo_export_item_category.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";

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
				echo "0**".$id."**".$new_contact_system_id[0];
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
				echo "0**".$id."**".$new_contact_system_id[0];
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
		$attach_ord_result=sql_select("select sum(attached_value) as attached_value from com_sales_contract_order_info where status_active=1 and is_deleted=0 and com_sales_contract_id=$txt_system_id");
		$attach_ord_value=$attach_ord_result[0][csf("attached_value")]*1;
		if($sc_value<$attach_ord_value)
		{
			echo "31** Contact Value Not Allow Less Then Attach Value";disconnect($con);die;
		}

 		$maximum_tolarence = str_replace("'", '', $txt_contract_value)+(str_replace("'", '', $txt_contract_value)*str_replace("'", '', $txt_tolerance))/100;
		$minimum_tolarence = str_replace("'", '', $txt_contract_value)-(str_replace("'", '', $txt_contract_value)*str_replace("'", '', $txt_tolerance))/100;

		$foreign_comn_value = (str_replace("'", '', $txt_contract_value)*str_replace("'", '', $txt_foreign_comn))/100;
		$local_comn_value = (str_replace("'", '', $txt_contract_value)*str_replace("'", '', $txt_local_comn))/100;

		$max_btb_limit_value = (str_replace("'", '', $txt_contract_value)*str_replace("'", '', $txt_max_btb_limit))/100;
		//update code here
		$field_array="contract_no*contract_date*beneficiary_name*buyer_name*applicant_name*convertible_to_lc*lien_bank*lien_date*tolerance*last_shipment_date*expiry_date*shipping_mode*pay_term*inco_term*inco_term_place*contract_source*port_of_entry*port_of_loading*port_of_discharge*internal_file_no*shipping_line*doc_presentation_days*max_btb_limit*max_btb_limit_value*foreign_comn*foreign_comn_value*local_comn*local_comn_value*remarks*discount_clauses*tenor*currency_name*contract_value*converted_from*converted_btb_lc_list*claim_adjustment*bank_file_no*sc_year*bl_clause*export_item_category*updated_by*update_date*status_active";

		$data_array="".$txt_contract_no."*".$txt_contract_date."*".$cbo_beneficiary_name."*".$cbo_buyer_name."*".$txt_applicant_name."*".$cbo_convertible_to_lc."*".$cbo_lien_bank."*".$txt_lien_date."*".$txt_tolerance."*".$txt_last_shipment_date."*".$txt_expiry_date."*".$cbo_shipping_mode."*".$cbo_pay_term."*".$cbo_inco_term."*".$txt_inco_term_place."*".$cbo_contract_source."*".$txt_port_of_entry."*".$txt_port_of_loading."*".$txt_port_of_discharge."*".$txt_internal_file_no."*".$txt_shipping_line."*".$txt_doc_presentation_days."*".$txt_max_btb_limit."*".$max_btb_limit_value."*".$txt_foreign_comn."*".$foreign_comn_value."*".$txt_local_comn."*".$local_comn_value."*".$txt_remarks."*".$txt_discount_clauses."*".$txt_tenor."*".$cbo_currency_name."*".$txt_contract_value."*".$txt_converted_from_id."*".$txt_converted_btb_id."*".$txt_claim_adjustment."*".$txt_bank_file_no."*".$txt_year."*".$txt_bl_clause."*".$cbo_export_item_category."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
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
				echo "1**".str_replace("'", '', $txt_system_id)."**".str_replace("'", '', $contact_system_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "6**".str_replace("'", '', $txt_system_id)."**".str_replace("'", '', $contact_system_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'", '', $txt_system_id)."**".str_replace("'", '', $contact_system_id);
			}
			else
			{
				oci_rollback($con);
				echo "6**".str_replace("'", '', $txt_system_id)."**".str_replace("'", '', $contact_system_id);
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
		
		$update_field_arr="updated_by*update_date*status_active*is_deleted";
		$update_data_arr="".$user_id."*'".$pc_date_time."'*0*1";
		$salesMst=$salesPo=true;
		//echo "10** $invMst && $invDtls && $invPo && $invClr = $id";oci_rollback($con);die;
		//echo "10**"."Update com_export_invoice_ship_mst set status_active=0,is_deleted=1,updated_by=$user_id,update_date='$pc_date_time'  where id =$id";oci_rollback($con);die;
		$invoice_id = return_field_value("id","com_export_invoice_ship_mst","lc_sc_id=".$id." and is_lc=2 and status_active=1 and is_deleted=0","id");
		if($invoice_id>0)
		{
			echo "35**Delete Not Allow. This SC No Found in Invoice"; disconnect($con);die;
		}
		else
		{
			if($id>0)
			{
				$salesMst=sql_update("com_sales_contract",$update_field_arr,$update_data_arr,"id",$id,1);
				$salesPo=sql_update("com_sales_contract_order_info",$update_field_arr,$update_data_arr,"mst_id",$id,1);
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
		$sc_ammendment_id=return_field_value("max(id) as amd_id","com_sales_contract_amendment","contract_id=$txt_system_id and status_active=1","amd_id");
		if($sc_ammendment_id=="") $sc_ammendment_id=0;
 		$field_array="id,com_sales_contract_id,wo_po_break_down_id,attached_qnty,attached_rate,attached_value,status_active,fabric_description,category_no,hs_code,inserted_by,insert_date,sc_amendment_id,is_sales";
		$id = return_next_id( "id", "com_sales_contract_order_info", 1 );
		for($j=1;$j<=$noRow;$j++)
		{
			$hiddenwopobreakdownid="hiddenwopobreakdownid_".$j;
			$txtattachedqnty="txtattachedqnty_".$j;
			$hiddenunitprice="hiddenunitprice_".$j;
			$txtattachedvalue="txtattachedvalue_".$j;
			$cbopostatus="cbopostatus_".$j;
			$txtfabdescrip="txtfabdescrip_".$j;
			$txtcategory="txtcategory_".$j;
			$txthscode="txthscode_".$j;
			$isSales="isSales_".$j;
			if(str_replace("'","",$$isSales)=="") $is_Sales=0; else $is_Sales=str_replace("'","",$$isSales);

			if($$hiddenwopobreakdownid!="")
			{
				if($data_array!="") $data_array.=",";

				$data_array.="(".$id.",".$txt_system_id.",".$$hiddenwopobreakdownid.",".$$txtattachedqnty.",".$$hiddenunitprice.",".$$txtattachedvalue.",".$$cbopostatus.",".$$txtfabdescrip.",".$$txtcategory.",".$$txthscode.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$sc_ammendment_id."','".$is_Sales."')";
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

		//echo "11** Fail ".$tot_attached;die;

		$rID=sql_insert("com_sales_contract_order_info",$field_array,$data_array,1);
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

		 //update code here
		$field_array="id,com_sales_contract_id,wo_po_break_down_id,attached_qnty,attached_rate,attached_value,status_active,fabric_description,category_no,hs_code,inserted_by,insert_date,is_sales";
		$field_array_update="wo_po_break_down_id*attached_qnty*attached_rate*attached_value*status_active*fabric_description*category_no*hs_code*updated_by*update_date*is_sales";

		$hiddensalescontractorderid=str_replace("'", '', $hiddensalescontractorderid);
		$currentattachedvalue = 0;
		$id = return_next_id( "id", "com_sales_contract_order_info", 1 );
		for($j=1;$j<=$noRow;$j++)
		{
			$hiddenwopobreakdownid="hiddenwopobreakdownid_".$j;
			$txtattachedqnty="txtattachedqnty_".$j;
			$hiddenunitprice="hiddenunitprice_".$j;
			$txtattachedvalue="txtattachedvalue_".$j;
			$cbopostatus="cbopostatus_".$j;
			$txtfabdescrip="txtfabdescrip_".$j;
			$txtcategory="txtcategory_".$j;
			$txthscode="txthscode_".$j;
			$isSales="isSales_".$j;
			if(str_replace("'","",$$isSales)=="") $is_Sales=0; else $is_Sales=str_replace("'","",$$isSales);
			if($j==1)
			{
				if($hiddensalescontractorderid!="")
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

					$data_array_update="".$$hiddenwopobreakdownid."*".$$txtattachedqnty."*".$$hiddenunitprice."*".$$txtattachedvalue."*".$$cbopostatus."*".$$txtfabdescrip."*".$$txtcategory."*".$$txthscode."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'".$is_Sales."'";
					$currentattachedvalue += number_format(str_replace("'","",$$txtattachedvalue),2,'.','');
				}
				else
				{
					if($data_array!="") $data_array.=",";

					$data_array ="(".$id.",".$txt_system_id.",".$$hiddenwopobreakdownid.",".$$txtattachedqnty.",".$$hiddenunitprice.",".$$txtattachedvalue.",".$$cbopostatus.",".$$txtfabdescrip.",".$$txtcategory.",".$$txthscode.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$is_Sales."')";
					$id = $id+1;
					$currentattachedvalue += number_format(str_replace("'","",$$txtattachedvalue),2,'.','');
				}
			}
			else
			{
				if($$hiddenwopobreakdownid!="")
				{
					if($data_array!="") $data_array.=",";

					$data_array.="(".$id.",".$txt_system_id.",".$$hiddenwopobreakdownid.",".$$txtattachedqnty.",".$$hiddenunitprice.",".$$txtattachedvalue.",".$$cbopostatus.",".$$txtfabdescrip.",".$$txtcategory.",".$$txthscode.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$is_Sales."')";
					$id = $id+1;
					$currentattachedvalue += number_format(str_replace("'","",$$txtattachedvalue),2,'.','');
				}
			}
		}



		//$currentattachedvalue += number_format(str_replace("'","",$$txtattachedvalue),2,'.','');
		$without_update_dtls_cond="";
		if($hiddensalescontractorderid != ""){
			$without_update_dtls_cond = " and b.id not in ($hiddensalescontractorderid)";
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

		$flag=1;
		if($data_array!="")
		{
			$rID2=sql_insert("com_sales_contract_order_info",$field_array,$data_array,0);
			if($flag==1)
			{
				if($rID2) $flag=1; else $flag=0;
			}
		}

		if($data_array_update!="")
		{
			$rID=sql_update("com_sales_contract_order_info",$field_array_update,$data_array_update,"id","".$hiddensalescontractorderid."",1);
			if($flag==1)
			{
				if($rID) $flag=1; else $flag=0;
			}
		}

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
        size: A4 portrait;
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

		$data_array1=sql_select("select wm.total_set_qnty as ratio, ci.attached_qnty from wo_po_break_down wb, wo_po_details_master wm, com_sales_contract_order_info ci where wb.job_no_mst = wm.job_no and wb.id=ci.wo_po_break_down_id and ci.com_sales_contract_id='$data[1]' and ci.status_active = '1' and ci.is_deleted = '0' order by ci.id");
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
    where wb.job_no_mst = wm.job_no and wb.id = ci.wo_po_break_down_id and ci.com_sales_contract_id='$data[1]' and ci.status_active = '1' and ci.is_deleted ='0'
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
            <td>Inco Term</td>
            <td><?php echo $incoterm[$buyer_incoterm] ; ?></td>
            <td><?php echo $incoterm[$lc_incoterm] ; ?></td>
          </tr>
          <tr>
            <td>13</td>
            <td>Inco Term Place</td>
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

if($action == "sc_lc_search"){
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
?>



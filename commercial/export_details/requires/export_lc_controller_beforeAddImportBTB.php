<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------- Start-------------------------------------//
$buyer_details = return_library_array("select id,buyer_name from lib_buyer where status_active=1","id","buyer_name");

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
	echo load_html_head_contents("Export LC Form", "../../../", 1, 1,'','1','');
	extract($_REQUEST);
	//echo $companyID;die;
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$sql="select b.file_no, a.company_name from wo_po_break_down b, wo_po_details_master a where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and a.company_name=$companyID and b.file_no>0 group by a.company_name, b.file_no";
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

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 162, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );  
	exit();
}

if ($action=="load_drop_down_applicant_name")
{
	$sql = "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (22,23)) order by buyer_name";  
 	echo create_drop_down( "cbo_applicant_name", 162, $sql,"id,buyer_name", 1, "---- Select ----", 0, "" );
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

 
if($action=='populate_data_from_export_lc')
{
	$data_array=sql_select("select id,export_lc_system_id,export_lc_no,lc_date,beneficiary_name,buyer_name,applicant_name,notifying_party,consignee,issuing_bank_name,replacement_lc,lien_bank,lien_date,lc_value,currency_name,tolerance,last_shipment_date,expiry_date,shipping_mode,pay_term,inco_term,inco_term_place,lc_source,port_of_entry,port_of_loading,port_of_discharge,internal_file_no,doc_presentation_days,max_btb_limit,foreign_comn,foreign_comn_value,local_comn,local_comn_value,remarks,tenor,transfering_bank_ref,bl_clause,reimbursement_clauses,discount_clauses,is_lc_transfarrable,transfer_bank,negotiating_bank,nominated_shipp_line,re_imbursing_bank,claim_adjustment,expiry_place,bank_file_no,lc_year,reason,export_item_category from com_export_lc where id='$data'");
	foreach ($data_array as $row)
	{ 
		if($db_type==0)
		{
			$attached_po_id=return_field_value("group_concat(wo_po_break_down_id)","com_export_lc_order_info","com_export_lc_id=$data and status_active=1 and is_deleted=0");
		}
		else
		{
			$attached_po_id=return_field_value("LISTAGG(wo_po_break_down_id, ',') WITHIN GROUP (ORDER BY wo_po_break_down_id) as po_id","com_export_lc_order_info","com_export_lc_id=$data and status_active=1 and is_deleted=0","po_id");	
		}
		$lc_amnd=return_field_value("count(id)","com_export_lc_amendment","export_lc_id=$data and is_original=0 and status_active=1 and is_deleted=0");
		
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('export_lc_system_id').value 			= '".$row[csf("export_lc_system_id")]."';\n";
		echo "document.getElementById('cbo_beneficiary_name').value 		= '".$row[csf("beneficiary_name")]."';\n";
		echo "$('#cbo_beneficiary_name').attr('disabled','true')".";\n";
		echo "document.getElementById('txt_internal_file_no').value			= '".$row[csf("internal_file_no")]."';\n";
		echo "document.getElementById('txt_bank_file_no').value 			= '".$row[csf("bank_file_no")]."';\n";
		echo "document.getElementById('txt_year').value 					= '".$row[csf("lc_year")]."';\n";
		echo "document.getElementById('txt_lc_number').value 				= '".$row[csf("export_lc_no")]."';\n";
		echo "document.getElementById('txt_lc_value').value 				= '".$row[csf("lc_value")]."';\n";
		echo "document.getElementById('txt_lc_date').value 					= '".change_date_format($row[csf("lc_date")])."';\n";
		echo "document.getElementById('cbo_currency_name').value 			= '".$row[csf("currency_name")]."';\n";
		
		echo "load_drop_down( 'requires/export_lc_controller', document.getElementById('cbo_beneficiary_name').value, 'load_drop_down_buyer', 'buyer_td_id' );\n";
		echo "load_drop_down( 'requires/export_lc_controller', document.getElementById('cbo_beneficiary_name').value, 'load_drop_down_applicant_name', 'applicant_name_td' );\n";
		echo "load_drop_down( 'requires/export_lc_controller', document.getElementById('cbo_beneficiary_name').value, 'load_drop_down_notifying_party', 'notifying_party_td' );\n";
		echo "load_drop_down( 'requires/export_lc_controller', document.getElementById('cbo_beneficiary_name').value, 'load_drop_down_consignee', 'consignee_td' );\n";
 		echo "get_php_form_data( document.getElementById('cbo_beneficiary_name').value, 'eval_multi_select', 'requires/export_lc_controller' );\n";
  		
		echo "document.getElementById('cbo_buyer_name').value				= '".$row[csf("buyer_name")]."';\n";
		echo "document.getElementById('cbo_applicant_name').value 			= '".$row[csf("applicant_name")]."';\n";
		echo "document.getElementById('cbo_notifying_party').value			= '".$row[csf("notifying_party")]."';\n";
		echo "document.getElementById('cbo_consignee').value 				= '".$row[csf("consignee")]."';\n";
		echo "document.getElementById('txt_issuing_bank').value 			= '".$row[csf("issuing_bank_name")]."';\n";
		echo "document.getElementById('cbo_lien_bank').value 				= '".$row[csf("lien_bank")]."';\n";
		echo "document.getElementById('txt_lien_date').value 				= '".change_date_format($row[csf("lien_date")])."';\n";
		echo "document.getElementById('txt_last_shipment_date').value 		= '".change_date_format($row[csf("last_shipment_date")])."';\n";
		echo "document.getElementById('txt_expiry_date').value 				= '".change_date_format($row[csf("expiry_date")])."';\n";
		echo "document.getElementById('txt_tolerance').value 				= '".$row[csf("tolerance")]."';\n";
		echo "document.getElementById('cbo_shipping_mode').value 			= '".$row[csf("shipping_mode")]."';\n";
		echo "document.getElementById('cbo_pay_term').value 				= '".$row[csf("pay_term")]."';\n";
		echo "document.getElementById('txt_tenor').value 					= '".$row[csf("tenor")]."';\n";
		echo "document.getElementById('cbo_inco_term').value 				= '".$row[csf("inco_term")]."';\n";
		echo "document.getElementById('txt_inco_term_place').value 			= '".$row[csf("inco_term_place")]."';\n";
		echo "document.getElementById('cbo_lc_source').value 				= '".$row[csf("lc_source")]."';\n";
		echo "document.getElementById('txt_port_of_entry').value 			= '".$row[csf("port_of_entry")]."';\n";
		echo "document.getElementById('txt_port_of_loading').value 			= '".$row[csf("port_of_loading")]."';\n";
		echo "document.getElementById('txt_port_of_discharge').value 		= '".$row[csf("port_of_discharge")]."';\n";
		echo "document.getElementById('txt_doc_presentation_days').value 	= '".$row[csf("doc_presentation_days")]."';\n";
		echo "document.getElementById('txt_max_btb_limit').value 			= '".$row[csf("max_btb_limit")]."';\n";
		echo "document.getElementById('txt_foreign_comn').value 			= '".$row[csf("foreign_comn")]."';\n";
		echo "document.getElementById('txt_local_comn').value 				= '".$row[csf("local_comn")]."';\n";
		echo "document.getElementById('txt_transfering_bank_ref').value 	= '".$row[csf("transfering_bank_ref")]."';\n";
		echo "document.getElementById('cbo_is_lc_transfarrable').value 		= '".$row[csf("is_lc_transfarrable")]."';\n";
		echo "document.getElementById('cbo_replacement_lc').value 			= '".$row[csf("replacement_lc")]."';\n";
		echo "document.getElementById('txt_transfer_bank').value 			= '".$row[csf("transfer_bank")]."';\n";
		echo "document.getElementById('txt_negotiating_bank').value 		= '".$row[csf("negotiating_bank")]."';\n";
		echo "document.getElementById('txt_nominated_shipp_line').value 	= '".$row[csf("nominated_shipp_line")]."';\n";
		echo "document.getElementById('txt_re_imbursing_bank').value 		= '".$row[csf("re_imbursing_bank")]."';\n";
		echo "document.getElementById('txt_claim_adjustment').value 		= '".$row[csf("claim_adjustment")]."';\n";
		echo "document.getElementById('txt_expiry_place').value 			= '".$row[csf("expiry_place")]."';\n";
		echo "document.getElementById('txt_bl_clause').value 				= '".$row[csf("bl_clause")]."';\n";
		echo "document.getElementById('txt_reimbursement_clauses').value 	= '".$row[csf("reimbursement_clauses")]."';\n";
		echo "document.getElementById('txt_discount_clauses').value 		= '".$row[csf("discount_clauses")]."';\n";
		echo "document.getElementById('txt_remarks').value 					= '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('txt_reason').value 					= '".$row[csf("reason")]."';\n";
		echo "document.getElementById('cbo_export_item_category').value 	= '".$row[csf("export_item_category")]."';\n";
		echo "document.getElementById('hidden_selectedID').value 			= '".$attached_po_id."';\n";
		
		echo "replacement_lc_diplay('".$row[csf("replacement_lc")]."');\n";
		
		if($row[csf("replacement_lc")]==1)
		{
			if($db_type==0)
			{
				$replaced_sc_id=return_field_value("group_concat(com_sales_contract_id)","com_export_lc_atch_sc_info","com_export_lc_id=$data and status_active=1 and is_deleted=0 group by com_export_lc_id");
			}
			else
			{
				$replaced_sc_id=return_field_value("LISTAGG(com_sales_contract_id, ',') WITHIN GROUP (ORDER BY com_sales_contract_id) as sc_id","com_export_lc_atch_sc_info","com_export_lc_id=$data and status_active=1 and is_deleted=0 group by com_export_lc_id","sc_id");	
			}
			echo "document.getElementById('hidden_sc_selectedID').value 	= '".$replaced_sc_id."';\n";
		}
		
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_export_lc_entry',1);\n";  
		
		echo "set_multiselect('cbo_notifying_party*cbo_consignee','0*0','1','".$row[csf('notifying_party')]."*".$row[csf('consignee')]."','0*0');\n";
		
		if($lc_amnd>0)
		{
			echo "disable_enable_fields('txt_lc_value*txt_last_shipment_date*txt_expiry_date*cbo_shipping_mode*cbo_inco_term*txt_inco_term_place*txt_port_of_entry*txt_port_of_loading*txt_port_of_discharge*cbo_pay_term*txt_tenor*txt_claim_adjustment*txt_discount_clauses*txt_remarks',1);\n";
		}
		else
		{
			echo "disable_enable_fields('txt_lc_value*txt_last_shipment_date*txt_expiry_date*cbo_shipping_mode*cbo_inco_term*txt_inco_term_place*txt_port_of_entry*txt_port_of_loading*txt_port_of_discharge*cbo_pay_term*txt_tenor*txt_claim_adjustment*txt_discount_clauses*txt_remarks',0);\n";
		}
		
		exit();
	}
}


if($action=="export_lc_popup_search")
 {
	echo load_html_head_contents("Export LC Form", "../../../", 1, 1,'','1','');
	extract($_REQUEST);
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
    <div align="center" style="width:1030px;">
        <form name="searchexportlcfrm" id="searchexportlcfrm">
            <fieldset style="width:1028px; margin-left:3px">
            <legend>Enter search words</legend>           
                <table cellpadding="0" cellspacing="0" width="80%" class="rpt_table">
                    <thead>
                        <th>Company</th>
                        <th>Buyer</th>
                        <th>Search By</th>
                        <th>Enter</th>
                        <th><input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" /><input type="hidden" name="id_field" id="id_field" value="" /></th>
                    </thead>
                    <tr class="general">
                        <td>
                            <?
                                echo create_drop_down( "cbo_company_name", 162, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--- Select ---", 0, "load_drop_down( 'export_lc_controller', this.value, 'load_drop_down_buyer', 'buyer_td_id' );" );
                            ?>                        
                          </td>
                          <td id="buyer_td_id">
                            <?
                                echo create_drop_down("cbo_buyer_name", 162, $blank_array,"", 1, "--- Select Buyer ---", $selected, "" );
                            ?>
                         </td>                  
                        <td> 
                            <?
                                $arr=array(1=>'LC NO',2=>'File No');
                                echo create_drop_down( "cbo_search_by", 162, $arr,"", 0, "", 1, "" );
                            ?> 
                            <input type="hidden" id="hidden_export_lc_id" />
                        </td>						
                        <td id="search_by_td">
                            <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                        </td>                       
                         <td>
                            <input type="button" id="search_button" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value, 'export_lc_search_list_view', 'search_div', 'export_lc_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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

if($action=="export_lc_search_list_view")
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
	else if($search_by==1)
	{
		$search_condition="and export_lc_no like '$search_text%'";
	}
	else if($search_by==2)
	{
		$search_condition="and internal_file_no like '$search_text%'";
	}

	if($db_type==0) $select_field=", YEAR(insert_date) as year"; 
	else if($db_type==2) $select_field=", to_char(insert_date,'YYYY') as year";
	else $select_field="";//defined Later
 	
	$sql = "select id,export_lc_no, internal_file_no $select_field, export_lc_prefix_number, export_lc_system_id, beneficiary_name, buyer_name, applicant_name, lc_value, lien_bank, pay_term, last_shipment_date, lc_date from com_export_lc where status_active=1 and is_deleted=0 $company_id $buyer_id $search_condition order by id";
	
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$bank_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
	$arr=array (4=>$comp,5=>$buyer_arr,6=>$buyer_arr,8=>$bank_arr,9=>$pay_term);
	
	echo  create_list_view("list_view", "LC No,File No,Year,System ID,Company,Buyer Name,Applicant Name,LC Value,Lien Bank,Pay Term,Last Ship Date,LC Date", "80,80,60,70,70,70,70,100,110,70,80,70","1025","320",0, $sql, "js_set_value", "id", "", 1, "0,0,0,0,beneficiary_name,buyer_name,applicant_name,0,lien_bank,pay_term,0,0", $arr , "export_lc_no,internal_file_no,year,export_lc_prefix_number,beneficiary_name,buyer_name,applicant_name,lc_value,lien_bank,pay_term,last_shipment_date,lc_date", "",'','0,0,0,0,0,0,0,2,0,0,3,3');
	
}

if ($action=="order_popup")
{
	echo load_html_head_contents("Export LC Form", "../../../", 1, 1,'','1','');
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
		<fieldset style="width:970px">
			<table width="700" cellspacing="0" cellpadding="0" class="rpt_table">
                <thead>
                    <th>Company</th>
                    <th>Search By</th>
                    <th>Search</th>
                    <th>File No</th>
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
                        	$arr=array(1=>'PO Number',2=>'Job No',3=>'Style Ref No');
							echo create_drop_down( "cbo_search_by", 150, $arr,"",0, "--- Select ---", '',"" );
						?>
                     </td>
                     <td align="center">
                        <input type="text" name="txt_search_text" id="txt_search_text" class="text_boxes" style="width:150px" />
                        <input type="hidden" id="hidden_type" value="<? echo $types; ?>" />	
                        <input type="hidden" id="hidden_buyer_id" value="<? echo $buyer_id; ?>" />	
                        <input type="hidden" id="hidden_po_selectedID" value="<? echo $selectID; ?>" />
                        <input type="hidden" id="export_lcID" value="<? echo $export_lcID; ?>" />
                        <input type="hidden" name="txt_selected_id" id="txt_selected_id" value="" />				
                    </td>
                    <td align="center">
                        <input type="text" id="txt_file_no" name="txt_file_no" class="text_boxes" style="width:80px" >
                     </td>
                    <td align="center">
                        <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_text').value+'**'+document.getElementById('hidden_type').value+'**'+document.getElementById('hidden_buyer_id').value+'**'+document.getElementById('hidden_po_selectedID').value+'**'+document.getElementById('export_lcID').value+'**'+document.getElementById('txt_file_no').value, 'create_po_search_list_view', 'search_div', 'export_lc_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" />
                    </td>
            </tr>
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

if($action=="create_po_search_list_view")
{
	$data=explode('**',$data);
	if ($data[0]!=0) $company=" and wm.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[2]!='') 
	{
		if($data[1]==1)
			$search_text=" and wb.po_number like '%".trim($data[2])."%'";
		else if($data[1]==2)	 
			$search_text=" and wm.job_no like '%".trim($data[2])."'"; 
		else if($data[1]==3)
			$search_text=" and wm.style_ref_no like '%".trim($data[2])."%'";	
	}
	
	$action_types = $data[3];
	$buyer_id = $data[4];	
	
	if($data[5]=="") $selected_order_id = ""; else $selected_order_id = "and wb.id not in (".$data[5].")";
	
	$export_lcID = $data[6];
	$txt_file_no = $data[7];
	
	//echo $txt_file_no;die;
	
	if($txt_file_no!="") $file_no_cond=" and wb.file_no='$data[7]'"; else $file_no_cond="";
	
	if($db_type==0) $select_field="YEAR(wm.insert_date) as year,"; 
	else if($db_type==2) $select_field="to_char(wm.insert_date,'YYYY') as year,";
	else $select_field="";//defined Later
	

	if($action_types=='attached_po_status')
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
		
		$sql = "SELECT wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst, wb.file_no, wm.job_no_prefix_num, $select_field wm.style_ref_no, wm.gmts_item_id, wb.unit_price FROM wo_po_break_down wb, wo_po_details_master wm WHERE wb.job_no_mst = wm.job_no and wm.buyer_name like '$buyer_id' $company $search_text $file_no_cond and wb.is_deleted = 0 AND wb.status_active = 1 and wb.is_confirmed=1 group by wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date, wb.job_no_mst, wb.file_no, wm.job_no_prefix_num, wm.insert_date, wm.style_ref_no, wm.gmts_item_id, wb.unit_price"; 
		
		//echo $sql;
		?>
        <div>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="960" style="margin-left:1px" class="rpt_table" >
                <thead>
                    <th width="30">SL</th>
                    <th width="100">PO No</th>
                    <th width="40">Year</th>
                    <th width="50">Job No</th>
                    <th width="110">Item</th>
                    <th width="100">Style No</th>
                    <th width="80">PO Quantity</th>
                    <th width="90">Price</th>
                    <th width="70">Shipment Date</th>
                    <th width="100">Attached With</th>
					<th width="60">LC/SC</th>
                    <th>File No</th>
                </thead>
            </table>
            <div style="width:960px; overflow-y:scroll; max-height:250px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="942" class="rpt_table" id="tbl_list_search" >
                <? 
					$i=1;
                    $nameArray=sql_select( $sql );
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
                                            <td width="40" align="center"><p><? echo $selectResult[csf('year')]; ?>&nbsp;</p></td>
                               				<td width="50"><p>&nbsp;<? echo $selectResult[csf('job_no_prefix_num')]; ?></p></td>
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
											<td width="100"><p><? echo $lc_details[$lc_id]; ?></p></td>
											<td align="center" width="60"><? echo 'LC'; ?></td>
                                            <td><p><? echo $selectResult[csf('file_no')]; ?>&nbsp;</p></td>
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
                                            <td width="40" align="center"><p><? echo $selectResult[csf('year')]; ?>&nbsp;</p></td>
                               				<td width="50"><p>&nbsp;<? echo $selectResult[csf('job_no_prefix_num')]; ?></p></td>
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
											<td width="90" align="right"><? echo $selectResult[csf('po_quantity')]; ?></td>
											<td width="90" align="right"><? echo number_format($selectResult[csf('po_total_price')],2); ?></td>
											<td align="center" width="70"><? echo change_date_format($selectResult[csf('shipment_date')]); ?></td>
											<td width="100"><p><? echo $sc_details[$sc_id]; ?></p></td>
											<td align="center" width="60"><? echo 'SC'; ?></td>
                                            <td><p><? echo $selectResult[csf('file_no')]; ?>&nbsp;</p></td>
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

		$sql = "SELECT wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst, wb.file_no, wm.job_no_prefix_num, $select_field wm.style_ref_no, wm.gmts_item_id, wb.unit_price 
		FROM wo_po_break_down wb, wo_po_details_master wm 
		WHERE wb.job_no_mst = wm.job_no and wm.buyer_name like '$buyer_id' $selected_order_id $company $search_text $file_no_cond and wb.is_deleted = 0 AND wb.status_active = 1 and wb.is_confirmed=1 group by wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date, wb.job_no_mst, wb.file_no, wm.job_no_prefix_num, wm.insert_date, wm.style_ref_no, wm.gmts_item_id, wb.unit_price";
		//echo $sql."<br>"; 
	 ?>
        <div>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="960" style="margin-left:1px" class="rpt_table" >
                <thead>
                    <th width="40">SL</th>
                    <th width="110">PO No</th>
                    <th width="60">Year</th>
                    <th width="70">Job No</th>
                    <th width="130">Item</th>
                    <th width="110">Style No</th>
                    <th width="100">PO Quantity</th>
                    <th width="120">Price</th>
                    <th width="80">Shipment Date</th>
                    <th >File No</th>
                </thead>
            </table>
            <div style="width:960px; overflow-y:scroll; max-height:220px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="942" class="rpt_table" id="tbl_list_search" >
                <?
					$i=1;
                   
					$lc_attached_qnty_arr = return_library_array("select wo_po_break_down_id, sum(attached_qnty) as qty from com_export_lc_order_info where status_active = 1 and is_deleted=0 group by wo_po_break_down_id","wo_po_break_down_id","qty");
					$sc_attached_qnty_arr = return_library_array("select wo_po_break_down_id, sum(attached_qnty) as qty from com_sales_contract_order_info where status_active = 1 and is_deleted=0 group by wo_po_break_down_id","wo_po_break_down_id","qty");
					
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
                                </td>	
                                <td width="110"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
                                <td width="60" align="center"><p><? echo $selectResult[csf('year')]; ?>&nbsp;</p></td>
                                <td width="70"><p>&nbsp;<? echo $selectResult[csf('job_no_prefix_num')]; ?></p></td>
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
                                <td width="110"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
                                <td width="100" align="right"><? echo $selectResult[csf('po_quantity')]; ?></td>
                                <td width="120" align="right"><? echo number_format($selectResult[csf('po_total_price')],2); ?></td>
                                <td align="center" width="80"><? echo change_date_format($selectResult[csf('shipment_date')]); ?></td>
                                <td><p><? echo $selectResult[csf('file_no')]; ?>&nbsp;</p></td>		
                            </tr>
                   		<?
                    	$i++;
						}
                    }
                    ?>
                </table>
            </div>
            <table width="960" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
	$sql="select wb.id, ci.id as idd, wm.gmts_item_id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst, wm.style_ref_no, wm.order_uom, wm.total_set_qnty as ratio, ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active from wo_po_break_down wb, wo_po_details_master wm, com_export_lc_order_info ci where wb.job_no_mst = wm.job_no and wb.id=ci.wo_po_break_down_id and ci.com_export_lc_id='$data' and ci.status_active = '1' and ci.is_deleted = '0' order by ci.id";
 	
	/*$arr=array(9=>$attach_detach_array);
	echo create_list_view("list_view", "Order Number,Order Qty,Order Value,Attached Qty,Rate,Attached Value,Style Ref,Item,Job No,Status", "100,100,100,100,60,100,150,150,100,80","1050","200",0, $sql, "get_php_form_data", "idd", "'populate_order_details_form_data'", 0, "0,0,0,0,0,0,0,0,0,status_active", $arr, "po_number,po_quantity,po_total_price,attached_qnty,attached_rate,attached_value,style_ref_no,style_description,job_no_mst,status_active", "requires/export_lc_controller",'','0,1,1,1,2,2,0,0,0,0','1,po_quantity,po_total_price,attached_qnty,0,attached_value,0,0,0,0');*/
	
	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1100" class="rpt_table" >
            <thead>
                <th width="100">Order Number</th>
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
        <div style="width:1100px; overflow-y:scroll; max-height:200px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1082" class="rpt_table" id="tbl_list_search" >
            <?
                $i=1; $total_attc_value=0; $total_order_qnty_in_pcs=0;
                $nameArray=sql_select( $sql );
                foreach ($nameArray as $selectResult)
                {
                    if($i%2==0) $bgcolor="#E9F3FF";
                    else $bgcolor="#FFFFFF";

					$order_qnty_in_pcs=$selectResult[csf('attached_qnty')]*$selectResult[csf('ratio')];
					$total_order_qnty_in_pcs+=$order_qnty_in_pcs;
					$total_attc_value+=$selectResult[csf('attached_value')];
					
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="get_php_form_data('<? echo $selectResult[csf('idd')]; ?>','populate_order_details_form_data','requires/export_lc_controller')"> 
                        <td width="100"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
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
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1100" class="rpt_table">
        	<tfoot>
            	<th width="100">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="50">&nbsp;</th>
                <th width="60">Total</th>
                <th width="100" align="right"><? echo number_format($total_attc_value,2); ?></th>
                <th width="100" align="right"><? echo number_format($total_order_qnty_in_pcs,0); ?></th>
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
	$data_array=sql_select("select wb.id, ci.id as idd, wm.style_description, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst, wm.style_ref_no, wm.gmts_item_id, wb.unit_price,ci.attached_qnty, ci.attached_rate, ci.attached_value, ci.status_active, ci.com_export_lc_id, ci.fabric_description, ci.category_no, ci.hs_code from wo_po_break_down wb, wo_po_details_master wm, com_export_lc_order_info ci where wb.job_no_mst = wm.job_no and wb.id=ci.wo_po_break_down_id and ci.id='$data' and ci.status_active = '1' and ci.is_deleted = '0' order by ci.id");
 
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
		echo "document.getElementById('txtorderqnty_1').value 				= '".$row[csf("po_quantity")]."';\n";
		echo "document.getElementById('txtordervalue_1').value 				= '".$row[csf("po_total_price")]."';\n";
		echo "document.getElementById('txtattachedqnty_1').value 			= '".$row[csf("attached_qnty")]."';\n";
		echo "document.getElementById('hideattachedqnty_1').value 			= '".$row[csf("attached_qnty")]."';\n";
		echo "document.getElementById('hiddenunitprice_1').value 			= '".$row[csf("attached_rate")]."';\n";
		echo "document.getElementById('txtattachedvalue_1').value 			= '".$row[csf("attached_value")]."';\n";
		echo "document.getElementById('txtstyleref_1').value 				= '".$row[csf("style_ref_no")]."';\n";
		echo "document.getElementById('txtitemname_1').value 				= '".$gmts_item."';\n";
		echo "document.getElementById('txtjobno_1').value 					= '".$row[csf("job_no_mst")]."';\n";
		echo "document.getElementById('cbopostatus_1').value 				= '".$row[csf("status_active")]."';\n";
		echo "document.getElementById('txtfabdescrip_1').value 				= '".$row[csf("fabric_description")]."';\n";
		echo "document.getElementById('txtcategory_1').value 				= '".$row[csf("category_no")]."';\n";
		echo "document.getElementById('txthscode_1').value 				= '".$row[csf("hs_code")]."';\n";
		
		echo "document.getElementById('hiddenwopobreakdownid_1').value 	= '".$row[csf("id")]."';\n";
		echo "document.getElementById('hiddenexportlcorderid').value 	= '".$row[csf("idd")]."';\n";
		echo "document.getElementById('txt_tot_row').value 	= '1';\n";
		
		echo "math_operation( 'totalOrderqnty', 'txtorderqnty_', '+', 1 );\n";
		echo "math_operation( 'totalOrdervalue', 'txtordervalue_', '+', 1 );\n";
		echo "math_operation( 'totalAttachedqnty', 'txtattachedqnty_', '+', 1 );\n";
		echo "math_operation( 'totalAttachedvalue', 'txtattachedvalue_', '+', 1 );\n";
		
		$order_attahed_qnty_sc=0; $order_attahed_qnty_lc=0; $order_attahed_val_sc=0; $order_attahed_val_lc=0; $sc_no=''; $lc_no=''; 	
		$sql_sc ="SELECT a.contract_no, sum(b.attached_qnty) as at_qt,sum(b.attached_value) as at_val FROM com_sales_contract a, com_sales_contract_order_info b WHERE a.id=b.com_sales_contract_id and b.wo_po_break_down_id='".$row[csf("id")]."' and b.status_active = 1 and b.is_deleted=0 group by a.id, a.contract_no";
		$result_array_sc=sql_select($sql_sc);
		foreach($result_array_sc as $scArray)
		{
			if ($sc_no=="") $sc_no = $scArray[csf('contract_no')]; else $sc_no.=",".$scArray[csf('contract_no')];
			$order_attahed_qnty_sc+=$scArray[csf('at_qt')];
			//$order_attahed_val_sc+=$scArray[csf('at_val')];
		}
		
		$sql_lc="SELECT a.export_lc_no, sum(b.attached_qnty) as at_qt,sum(b.attached_value) as at_val FROM com_export_lc a, com_export_lc_order_info b WHERE a.id=b.com_export_lc_id and b.wo_po_break_down_id='".$row[csf("id")]."' and b.id!='".$data."' and b.status_active = 1 and b.is_deleted=0 group by a.id, a.export_lc_no";
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
		
		if($db_type==0)
		{			
		 	$attached_po_id=return_field_value("group_concat(wo_po_break_down_id)","com_export_lc_order_info","com_export_lc_id='".$row[csf("com_export_lc_id")]."' and status_active=1 and is_deleted=0");
		}
		else
		{
			$attached_po_id=return_field_value("LISTAGG(wo_po_break_down_id, ',') WITHIN GROUP (ORDER BY wo_po_break_down_id) as po_id","com_export_lc_order_info","com_export_lc_id='".$row[csf("com_export_lc_id")]."' and status_active=1 and is_deleted=0","po_id"); 
		}
		echo "document.getElementById('hidden_selectedID').value 		= '".$attached_po_id."';\n";
			 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_po_selection_save',3);\n";  		 
		exit();
	}
}

if($action=="populate_attached_po_id")
{
	if($db_type==0)
	{			
		$attached_po_id=return_field_value("group_concat(wo_po_break_down_id)","com_export_lc_order_info","com_export_lc_id='$data' and status_active=1 and is_deleted=0");
	}
	else
	{
		$attached_po_id=return_field_value("LISTAGG(wo_po_break_down_id, ',') WITHIN GROUP (ORDER BY wo_po_break_down_id) as po_id","com_export_lc_order_info","com_export_lc_id='$data' and status_active=1 and is_deleted=0","po_id"); 
	}
	 
	echo "document.getElementById('hidden_selectedID').value 		= '".$attached_po_id."';\n";
	exit();	
}

if($action=="order_list_for_attach")
{
	$explode_data = explode("**",$data);//0->wo_po_break_down id's, 1->table row
	$data=$explode_data[0];
	$table_row=$explode_data[1];
	
	if($data!="")
	{
		$data_array="SELECT wb.id, wb.po_number, wb.po_total_price, wb.po_quantity, wb.pub_shipment_date as shipment_date, wb.job_no_mst, wm.style_ref_no, wm.style_description, wb.unit_price FROM wo_po_break_down wb, wo_po_details_master wm WHERE wb.job_no_mst = wm.job_no AND wb.id in ($data) AND wb.is_deleted = 0 AND wb.status_active = 1";
		
		$data_array=sql_select($data_array);
 		foreach ($data_array as $row)
		{
			$table_row++;
			$order_attahed_qnty_sc=0; $order_attahed_qnty_lc=0; $order_attahed_val_sc=0; $order_attahed_val_lc=0; $sc_no=''; $lc_no=''; 	
			$sql_sc ="SELECT a.contract_no, sum(b.attached_qnty) as at_qt,sum(b.attached_value) as at_val FROM com_sales_contract a, com_sales_contract_order_info b WHERE a.id=b.com_sales_contract_id and b.wo_po_break_down_id='".$row[csf("id")]."' and b.status_active = 1 and b.is_deleted=0 group by a.id, a.contract_no";
			$result_array_sc=sql_select($sql_sc);
			foreach($result_array_sc as $scArray)
			{
				if ($sc_no=="") $sc_no = $scArray[csf('contract_no')]; else $sc_no.=",".$scArray[csf('contract_no')];
				$order_attahed_qnty_sc+=$scArray[csf('at_qt')];
				$order_attahed_val_sc+=$scArray[csf('at_val')];
			}
			
			$sql_lc="SELECT a.export_lc_no, sum(b.attached_qnty) as at_qt,sum(b.attached_value) as at_val FROM com_export_lc a, com_export_lc_order_info b WHERE a.id=b.com_export_lc_id and b.wo_po_break_down_id='".$row[csf("id")]."' and b.status_active = 1 and b.is_deleted=0 group by a.id, a.export_lc_no";
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
					<input type="text" name="txtordernumber_<? echo $table_row; ?>" id="txtordernumber_<? echo $table_row; ?>" class="text_boxes" style="width:100px"  value="<? echo $row[csf("po_number")]; ?>" onDblClick= "openmypage('requires/export_lc_controller.php?action=order_popup&types=order_select_popup&buyer_id='+document.getElementById('cbo_buyer_name').value+'&selectID='+document.getElementById('hidden_selectedID').value+'&export_lcID='+document.getElementById('txt_system_id').value+'&company_id='+document.getElementById('cbo_beneficiary_name').value,'PO Selection Form','<? echo $table_row; ?>')" readonly= "readonly" placeholder="Double Click" />
					<input type="hidden" name="hiddenwopobreakdownid_<? echo $table_row; ?>" id="hiddenwopobreakdownid_<? echo $table_row; ?>" readonly= "readonly" value="<? echo $row[csf("id")]; ?>" />
				</td>
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
                    <input type="text" name="hiddenunitprice_<? echo $table_row; ?>" id="hiddenunitprice_<? echo $table_row; ?>" value="<? echo $row[csf("unit_price")]; ?>" style="width:50px" class="text_boxes_numeric" onKeyUp="calculate_attach_val(<? echo $table_row; ?>)" />
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
                <td><input type="text" name="txtcategory_<? echo $table_row; ?>" id="txtcategory_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:50px" /></td>
                <td><input type="text" name="txthscode_<? echo $table_row; ?>" id="txthscode_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:40px"/></td>
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

if($action=="sc_popup_search")
{
	echo load_html_head_contents("Export LC Form", "../../../", 1, 1,'','1','');
	extract($_REQUEST);

	?>
	<script>
		 
		function fn_check()
		{
 			if(form_validation('cbo_company_name','Company Name')==false)
			{
				return;
			}
			else	
			{
				show_list_view ( document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('hidden_sc_selectedID').value, 'sc_search_list_view', 'search_div', 'export_lc_controller', 'setFilterGrid(\'tbl_list_search\',-1)');
			}
		}
		
	 var selected_id = new Array, selected_name = new Array();	
	 function check_all_data() {
			var tbl_row_count = $("#tbl_list_search tbody tr").length;

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
							   	echo create_drop_down( "cbo_company_name", 165, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--- Select ---", $companyID, "load_drop_down( 'export_lc_controller', this.value, 'load_drop_down_buyer', 'buyer_td_id' );" );
							?>                        
                          </td>
                          <td id="buyer_td_id">
                            <?
							   	$sql="select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$companyID' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name";
								echo create_drop_down( "cbo_buyer_name", 162, $sql,"id,buyer_name", 1, "--- Select Buyer ---", $buyerID, "" );
							?>
                         </td>                  
						<td> 
                        	<?
							   	$arr=array(1=>'SC NO');
								echo create_drop_down( "cbo_search_by", 165, $arr,"", 0, "--- Select ---", 0, "" );
							?> 
                            <input type="hidden" id="hidden_sc_selectedID" value="<? echo $sc_selectedID; ?>" />
                            <input type="hidden" name="txt_selected_id" id="txt_selected_id" value="" />
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

if($action=="sc_search_list_view")
{
	$data=explode('**',$data); 
	if($data[0]!=0){ $company_id=" and beneficiary_name = $data[0]";}else{ $company_id=""; }
	if($data[1]!=0){ $buyer_id=" and buyer_name = $data[1]";}else{ $buyer_id=""; }
	$search_by=$data[2];
	$search_text=$data[3];
	
	if($search_by==0)
	{
		$search_condition="";
	}
	else if($search_by==1 && $search_text!="")
	{
		$search_condition="and contract_no like '".trim($search_text)."%'";
	}
	
	if($data[4]=="") $sc_selectedID=""; else $sc_selectedID=" and id not in ($data[4])";
 	
	$sql="select id,contract_no,contract_date,beneficiary_name,buyer_name,applicant_name,convertible_to_lc as type,contract_value from com_sales_contract where status_active=1 and is_deleted=0 and convertible_to_lc<>2 $sc_selectedID $company_id $buyer_id $search_condition";
	$data_array=sql_select($sql);
	 
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
            $i = 0; $yet_to_replace=0; 
            foreach($data_array as $row)
            { 
                if($i%2==0) $bgcolor="#FFFFFF"; else $bgcolor="#E9F3FF";	
                    
                $replaced_result = return_field_value("sum(replaced_amount)","com_export_lc_atch_sc_info","com_sales_contract_id=".$row[csf('id')]." and is_deleted=0 and status_active=1");
				
                if($row[csf('contract_value')] > $replaced_result) 
				{
                    $i++;
                    $yet_to_replace = $row[csf('contract_value')]-$replaced_result;
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer;" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)" >                		<td width="50"><? echo $i; ?>  
                    		<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<?php echo $row[csf('id')]; ?>"/></td>
                        <td width="120"><? echo $row[csf('contract_no')]; ?></td>
                        <td width="120"><? echo $buyer_details[$row[csf('buyer_name')]]; ?></td>
                        <td width="120" align="right"><? echo number_format($row[csf('contract_value')],2); ?></td>
                        <td width="150" align="right"><? echo number_format($replaced_result,2); ?></td>
                        <td width="120" align="right"><? echo number_format($yet_to_replace,2); ?></td>
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

if($action=="populate_data_sc_form")
{
	$data=explode('**',$data);
	$sc_id=$data[0];
	$tblRow=$data[1];
	
	$data_array=sql_select("select id,contract_no,contract_date,beneficiary_name,buyer_name,applicant_name,convertible_to_lc as type,contract_value from com_sales_contract where status_active=1 and is_deleted=0 and id in ($sc_id) order by id");
	
	foreach($data_array as $row)
	{
		$tblRow++;
		$replaced_result = return_field_value("sum(replaced_amount)","com_export_lc_atch_sc_info","com_sales_contract_id=".$row[csf('id')]." and is_deleted=0 and status_active=1");
		$yet_to_replace = $row[csf('contract_value')]-$replaced_result;
		
		if($db_type==0)
		{
			$sql="select group_concat(a.id) as btb_id, group_concat(a.lc_number) as btb_lc 
			from com_btb_lc_master_details a, com_btb_export_lc_attachment b 
			where a.id=b.import_mst_id and b.lc_sc_id=".$row[csf('id')]." and b.is_lc_sc=1 and b.is_deleted=0 and b.status_active=1 and a.is_deleted=0 and a.status_active=1";
		}
		else
		{
			$sql="select  LISTAGG(cast(a.id as varchar(4000)), ',') WITHIN GROUP (ORDER BY a.id) as btb_id, LISTAGG(cast(a.lc_number as varchar(4000)), ',') WITHIN GROUP (ORDER BY a.lc_number) as btb_lc 
			 from com_btb_lc_master_details a, com_btb_export_lc_attachment b where a.id=b.import_mst_id and b.lc_sc_id=".$row[csf('id')]." and b.is_lc_sc=1 and b.is_deleted=0 and b.status_active=1 and a.is_deleted=0 and a.status_active=1";	
		}
		$btbArray=sql_select($sql);
		
		
		?>
        <tr class="general" id="<? echo "trs_".$tblRow; ?>">
            <td>
                <input type="text" name="txtSalesContractNo_<? echo $tblRow; ?>" id="txtSalesContractNo_<? echo $tblRow; ?>" placeholder="Double Click"  class="text_boxes" style="width:125px" onDblClick="add_sales_contract(<? echo $tblRow; ?>)" readonly  value="<? echo $row[csf("contract_no")]; ?>"  />
                <input type="hidden" name="hiddenScId_<? echo $tblRow; ?>" id="hiddenScId_<? echo $tblRow; ?>"  value="<? echo $row[csf("id")]; ?>" />
            </td>
            <td>
            	<input type="text" name="txtReplacementAmount_<? echo $tblRow; ?>" id="txtReplacementAmount_<? echo $tblRow; ?>" class="text_boxes_numeric" style="width:115px;"  onKeyup="CalculateCumulativeValue(this.value,this.id);"  value="<? echo 0; ?>" />
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
                    echo create_drop_down( "cbo_sc_status_".$tblRow, 100, $attach_detach_array,$row[csf('status_active')], 0, "", 1, "" );
                ?>
            </td>                           
       </tr> 	
       <?
		//$tblRow++;
	}
	
	exit();
}


if($action=="show_sc_active_listview")
{
	$sql="select a.id, a.com_sales_contract_id, b.contract_no, b.contract_value, a.replaced_amount, a.attched_btb_lc_id from com_export_lc_atch_sc_info a, com_sales_contract b where a.com_sales_contract_id=b.id and a.com_export_lc_id=$data and a.is_deleted = 0 and a.status_active=1 order by a.id";
	$data_array = sql_select($sql);
 	
	?>
 	
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
            $i = 1; $yet_to_replace=0; 
            foreach($data_array as $row)
            { 
                if ($i%2==0)  
                    $bgcolor="#FFFFFF";
                else
                    $bgcolor="#E9F3FF";	
                   
                $replaced_result = return_field_value("sum(replaced_amount)","com_export_lc_atch_sc_info","com_sales_contract_id=".$row[csf('com_sales_contract_id')]." and is_deleted=0 and status_active=1");
				$yet_to_replace = $row[csf('contract_value')]-$replaced_result;
				
				if($row[csf('attched_btb_lc_id')]!="")
				{
					if($db_type==0)
					{
						$btb_lc_no=return_field_value("group_concat(lc_number)","com_btb_lc_master_details","id in ($row[attched_btb_lc_id])");
					}
					else
					{
						$btb_lc_no=return_field_value("LISTAGG(lc_number, ',') WITHIN GROUP (ORDER BY id) as lc_number","com_btb_lc_master_details","id in (".$row[csf('attched_btb_lc_id')].")","lc_number");	
					}
				}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer;" id="search<? echo $i;?>" onClick="get_php_form_data( <?php echo $row[csf('id')]; ?>, 'populate_data_from_sales_contract', 'requires/export_lc_controller' );" >                
					<td width="140"><p><? echo $row[csf('contract_no')]; ?></p></td>
					<td width="130" align="right"><? echo number_format($row[csf('replaced_amount')],2); ?></td>
					<td width="140" align="right"><? echo number_format($row[csf('contract_value')],2); ?></td>
					<td width="140" align="right"><? echo number_format($replaced_result,2); ?></td>
					<td width="140" align="right"><? echo number_format($yet_to_replace,2); ?></td>
					<td width="210"><p><? echo $btb_lc_no; ?></p></td>
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


if($action == "populate_data_from_sales_contract")
{
	$data_array = sql_select("select a.id, a.com_export_lc_id, a.com_sales_contract_id, b.contract_no, b.contract_value, a.replaced_amount, a.attched_btb_lc_id from com_export_lc_atch_sc_info a, com_sales_contract b where a.com_sales_contract_id=b.id and a.id =$data and a.is_deleted = 0 and a.status_active=1");
 
	foreach ($data_array as $row)
	{ 	
		 $replaced_result = return_field_value("sum(replaced_amount)","com_export_lc_atch_sc_info","com_sales_contract_id=".$row[csf('com_sales_contract_id')]." and is_deleted=0 and status_active=1");
		 $yet_to_replace = $row[csf('contract_value')]-$replaced_result;
		 $actual_cumulative = $replaced_result-$row[csf("replaced_amount")];

		 if($row[csf('attched_btb_lc_id')]!="")
		 {
			if($db_type==0)
			{
				$btb_lc_no=return_field_value("group_concat(lc_number)","com_btb_lc_master_details","id in (".$row[csf('attched_btb_lc_id')].")");
			}
			else
			{
				$btb_lc_no=return_field_value("LISTAGG(lc_number, ',') WITHIN GROUP (ORDER BY id) as lc_number","com_btb_lc_master_details","id in (".$row[csf('attched_btb_lc_id')].")","lc_number");	
			}
		 }
		 
		 echo "$('#tbl_sales_contract tbody tr:not(:first)').remove();\n";
		 
 		 echo "document.getElementById('txtSalesContractNo_1').value 			= '".$row[csf("contract_no")]."';\n";
		 echo "document.getElementById('hiddenScId_1').value 					= '".$row[csf("com_sales_contract_id")]."';\n";
		 echo "document.getElementById('txtReplacementAmount_1').value 			= '".$row[csf("replaced_amount")]."';\n";
		 echo "document.getElementById('hideReplacementAmount_1').value 		= '".$row[csf("replaced_amount")]."';\n";
		 echo "document.getElementById('txtContractValue_1').value 				= '".$row[csf("contract_value")]."';\n";
		 echo "document.getElementById('txtCumulativeReplaced_1').value 		= '".$replaced_result."';\n";
		 echo "document.getElementById('txtCumulativeReplacedDB_1').value 		= '".$actual_cumulative."';\n";
		 echo "document.getElementById('txtYetToReplace_1').value 				= '".$yet_to_replace."';\n";
		 echo "document.getElementById('txtBtbLcSelected_1').value 				= '".$btb_lc_no."';\n";
		 echo "document.getElementById('txtBtbLcSelectedID_1').value 			= '".$row[csf("attched_btb_lc_id")]."';\n";
		 echo "document.getElementById('cbo_sc_status_1').value 				= '".$row[csf("status_active")]."';\n";
		 
		 echo "document.getElementById('hiddenlcAttachSalesContractID').value 	= '".$row[csf("id")]."';\n";
		 
		 if($db_type==0)
		 {
			$replaced_sc_id=return_field_value("group_concat(com_sales_contract_id)","com_export_lc_atch_sc_info","com_export_lc_id=".$row[csf('com_export_lc_id')]." and status_active=1 and is_deleted=0");
		 }
		 else
		 {
		  	$replaced_sc_id=return_field_value("LISTAGG(com_sales_contract_id, ',') WITHIN GROUP (ORDER BY com_sales_contract_id) as sc_id","com_export_lc_atch_sc_info","com_export_lc_id=".$row[csf('com_export_lc_id')]." and status_active=1 and is_deleted=0","sc_id");
		 }
		 echo "document.getElementById('hidden_sc_selectedID').value 	= '".$replaced_sc_id."';\n";
		 echo "document.getElementById('txt_tot_row_attach_sales').value 	= '1';\n";
		 
		 echo "math_operation( 'totalReplacedAmount', 'txtReplacementAmount_', '+', 1 );\n";
		 echo "math_operation( 'totalContractValue', 'txtContractValue_', '+', 1 );\n";
		 echo "math_operation( 'totalCumulativeReplaced', 'txtCumulativeReplaced_', '+', 1 );\n";
		 echo "math_operation( 'totalYettoReplace', 'txtYetToReplace_', '+', 1 );\n";
		 	 
		 echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_sales_contract_selection',2);\n";  		 
		 exit();
	}

}

if ($action=="load_sc_id")
{
	 if($db_type==0)
	 {
		$replaced_sc_id=return_field_value("group_concat(com_sales_contract_id)","com_export_lc_atch_sc_info","com_export_lc_id=$data and status_active=1 and is_deleted=0 group by com_export_lc_id");
	 }
	 else
	 {
		 $replaced_sc_id=return_field_value("LISTAGG(com_sales_contract_id,',') WITHIN GROUP (ORDER BY com_sales_contract_id) as sc_id","com_export_lc_atch_sc_info","com_export_lc_id=$data and status_active=1 and is_deleted=0 group by com_export_lc_id","sc_id");
	 }
	
	
	echo "document.getElementById('hidden_sc_selectedID').value 	= '".$replaced_sc_id."';\n";
	exit();
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

		if (is_duplicate_field( "export_lc_no", "com_export_lc", "export_lc_no=$txt_lc_number and beneficiary_name=$cbo_beneficiary_name and buyer_name=$cbo_buyer_name and lien_bank=$cbo_lien_bank" )==1)
		{
			echo "11**0"; 
			die;			
		}
		 
		$maximum_tolarence = str_replace("'", '', $txt_lc_value)+(str_replace("'", '', $txt_lc_value)*str_replace("'", '', $txt_tolerance))/100;
		$minimum_tolarence = str_replace("'", '', $txt_lc_value)-(str_replace("'", '', $txt_lc_value)*str_replace("'", '', $txt_tolerance))/100;
 		
		$foreign_comn_value = (str_replace("'", '', $txt_lc_value)*str_replace("'", '', $txt_foreign_comn))/100;
		$local_comn_value = (str_replace("'", '', $txt_lc_value)*str_replace("'", '', $txt_local_comn))/100;
 	 
		$max_btb_limit_value = (str_replace("'", '', $txt_lc_value)*str_replace("'", '', $txt_max_btb_limit))/100; 
	 
	 	if($db_type==0) $year_cond="YEAR(insert_date)"; 
		else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
		else $year_cond="";//defined Later
	 
	 	$new_export_lc_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_beneficiary_name), '', 'LC', date("Y",time()), 5, "select export_lc_prefix,export_lc_prefix_number from com_export_lc where beneficiary_name=$cbo_beneficiary_name and $year_cond=".date('Y',time())." order by id desc ","export_lc_prefix", "export_lc_prefix_number" ));
	 
		$id=return_next_id( "id", "com_export_lc", 1 ) ;
				 
		$field_array="id, export_lc_prefix,export_lc_prefix_number,export_lc_system_id,export_lc_no, lc_date, beneficiary_name, buyer_name, applicant_name, notifying_party, consignee, issuing_bank_name, replacement_lc, lien_bank, lien_date, lc_value, currency_name, tolerance,maximum_tolarence,minimum_tolarence,last_shipment_date, expiry_date, shipping_mode, pay_term, inco_term, inco_term_place, lc_source, port_of_entry, port_of_loading, port_of_discharge, internal_file_no, doc_presentation_days, max_btb_limit, max_btb_limit_value, foreign_comn, foreign_comn_value, local_comn, local_comn_value, remarks, tenor, transfering_bank_ref, bl_clause, reimbursement_clauses, discount_clauses, is_lc_transfarrable, transfer_bank, negotiating_bank, nominated_shipp_line, re_imbursing_bank, claim_adjustment, expiry_place, bank_file_no, lc_year, reason, export_item_category, inserted_by, insert_date";
		
		$data_array="(".$id.",'".$new_export_lc_system_id[1]."',".$new_export_lc_system_id[2].",'".$new_export_lc_system_id[0]."',".$txt_lc_number.",".$txt_lc_date.",".$cbo_beneficiary_name.",".$cbo_buyer_name.",".$cbo_applicant_name.",".$cbo_notifying_party.",".$cbo_consignee.",".$txt_issuing_bank.",".$cbo_replacement_lc.",".$cbo_lien_bank.",".$txt_lien_date.",".$txt_lc_value.",".$cbo_currency_name.",".$txt_tolerance.",'".$maximum_tolarence."','".$minimum_tolarence."',".$txt_last_shipment_date.",".$txt_expiry_date.",".$cbo_shipping_mode.",".$cbo_pay_term.",".$cbo_inco_term.",".$txt_inco_term_place.",".$cbo_lc_source.",".$txt_port_of_entry.",".$txt_port_of_loading.",".$txt_port_of_discharge.",".$txt_internal_file_no.",".$txt_doc_presentation_days.",".$txt_max_btb_limit.",".$max_btb_limit_value.",".$txt_foreign_comn.",".$foreign_comn_value.",".$txt_local_comn.",'".$local_comn_value."',".$txt_remarks.",".$txt_tenor.",".$txt_transfering_bank_ref.",".$txt_bl_clause.",".$txt_reimbursement_clauses.",".$txt_discount_clauses.",".$cbo_is_lc_transfarrable.",".$txt_transfer_bank.",".$txt_negotiating_bank.",".$txt_nominated_shipp_line.",".$txt_re_imbursing_bank.",".$txt_claim_adjustment.",".$txt_expiry_place.",".$txt_bank_file_no.",".$txt_year.",".$txt_reason.",".$cbo_export_item_category.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		$rID=sql_insert("com_export_lc",$field_array,$data_array,1);
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "0**".$id."**".$new_export_lc_system_id[0];
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**"."&nbsp;";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);   
				echo "0**".$id."**".$new_export_lc_system_id[0];
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
		
		if (is_duplicate_field( "export_lc_no", "com_export_lc", "export_lc_no=$txt_lc_number and beneficiary_name=$cbo_beneficiary_name and buyer_name=$cbo_buyer_name and lien_bank=$cbo_lien_bank and id<>$txt_system_id" )==1)
		{
			echo "11**0"; 
			die;			
		}
				 
		$maximum_tolarence = str_replace("'", '', $txt_lc_value)+(str_replace("'", '', $txt_lc_value)*str_replace("'", '', $txt_tolerance))/100;
		$minimum_tolarence = str_replace("'", '', $txt_lc_value)-(str_replace("'", '', $txt_lc_value)*str_replace("'", '', $txt_tolerance))/100;
 		
		$foreign_comn_value = (str_replace("'", '', $txt_lc_value)*str_replace("'", '', $txt_foreign_comn))/100;
		$local_comn_value = (str_replace("'", '', $txt_lc_value)*str_replace("'", '', $txt_local_comn))/100;
		
		$max_btb_limit_value = (str_replace("'", '', $txt_lc_value)*str_replace("'", '', $txt_max_btb_limit))/100; 
 		
		//update code here			
		$field_array="export_lc_no*lc_date*beneficiary_name*buyer_name*applicant_name*notifying_party*consignee*issuing_bank_name*replacement_lc*lien_bank*lien_date*lc_value*currency_name*tolerance*maximum_tolarence*minimum_tolarence*last_shipment_date*expiry_date*shipping_mode*pay_term*inco_term*inco_term_place*lc_source*port_of_entry*port_of_loading*port_of_discharge*internal_file_no*doc_presentation_days*max_btb_limit*max_btb_limit_value*foreign_comn*foreign_comn_value*local_comn*local_comn_value*remarks*tenor*transfering_bank_ref*bl_clause*reimbursement_clauses*discount_clauses*is_lc_transfarrable*transfer_bank*negotiating_bank*nominated_shipp_line*re_imbursing_bank*claim_adjustment*expiry_place*bank_file_no*lc_year*reason*export_item_category*updated_by*update_date";
		
 		$data_array="".$txt_lc_number."*".$txt_lc_date."*".$cbo_beneficiary_name."*".$cbo_buyer_name."*".$cbo_applicant_name."*".$cbo_notifying_party."*".$cbo_consignee."*".$txt_issuing_bank."*".$cbo_replacement_lc."*".$cbo_lien_bank."*".$txt_lien_date."*".$txt_lc_value."*".$cbo_currency_name."*".$txt_tolerance."*".$maximum_tolarence."*".$minimum_tolarence."*".$txt_last_shipment_date."*".$txt_expiry_date."*".$cbo_shipping_mode."*".$cbo_pay_term."*".$cbo_inco_term."*".$txt_inco_term_place."*".$cbo_lc_source."*".$txt_port_of_entry."*".$txt_port_of_loading."*".$txt_port_of_discharge."*".$txt_internal_file_no."*".$txt_doc_presentation_days."*".$txt_max_btb_limit."*".$max_btb_limit_value."*".$txt_foreign_comn."*".$foreign_comn_value."*".$txt_local_comn."*'".$local_comn_value."'*".$txt_remarks."*".$txt_tenor."*".$txt_transfering_bank_ref."*".$txt_bl_clause."*".$txt_reimbursement_clauses."*".$txt_discount_clauses."*".$cbo_is_lc_transfarrable."*".$txt_transfer_bank."*".$txt_negotiating_bank."*".$txt_nominated_shipp_line."*".$txt_re_imbursing_bank."*".$txt_claim_adjustment."*".$txt_expiry_place."*".$txt_bank_file_no."*".$txt_year."*".$txt_reason."*".$cbo_export_item_category."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		 
 		$rID=sql_update("com_export_lc",$field_array,$data_array,"id","".$txt_system_id."",1);
		
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $txt_system_id)."**".str_replace("'", '', $export_lc_system_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**".str_replace("'", '', $txt_system_id)."**".str_replace("'", '', $export_lc_system_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);  
				echo "1**".str_replace("'", '', $txt_system_id)."**".str_replace("'", '', $export_lc_system_id);
			}
			else
			{
				oci_rollback($con); 
				echo "6**".str_replace("'", '', $txt_system_id)."**".str_replace("'", '', $export_lc_system_id);
			}
		}
		disconnect($con);
		die;
	}
	
}


if ($action=="save_update_delete_sc_info")
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
			
 		$field_array="id,com_export_lc_id,com_sales_contract_id,replaced_amount,attched_btb_lc_id,status_active,inserted_by,insert_date";
		$id = return_next_id( "id", "com_export_lc_atch_sc_info", 1 );

		for($j=1;$j<=$noRow;$j++)
		{ 	
			$salesContractID="hiddenScId_".$j;
			$txtReplacementAmount="txtReplacementAmount_".$j;
			$txtBtbLcSelectedID="txtBtbLcSelectedID_".$j;
 			$cbo_sc_status="cbo_sc_status_".$j;
			
			if($data_array!="") $data_array.=",";
			
			$data_array .="(".$id.",".$txt_system_id.",".$$salesContractID.",".$$txtReplacementAmount.",".$$txtBtbLcSelectedID.",".$$cbo_sc_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id = $id+1;
		}
		//echo "5**0**insert into com_export_lc_atch_sc_info (".$field_array.") values ".$data_array;die;
		$rID=sql_insert("com_export_lc_atch_sc_info",$field_array,$data_array,1);
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
		
		$field_array="id,com_export_lc_id,com_sales_contract_id,replaced_amount,attched_btb_lc_id,status_active,inserted_by,insert_date";
		$field_array_update="com_export_lc_id*com_sales_contract_id*replaced_amount*attched_btb_lc_id*status_active*updated_by*update_date";
		
		$hiddenlcAttachSalesContractID=str_replace("'", '', $hiddenlcAttachSalesContractID);
		$id = return_next_id( "id", "com_export_lc_atch_sc_info", 1 ); 
				 
		for($j=1;$j<=$noRow;$j++)
		{ 	
			$salesContractID="hiddenScId_".$j;
			$txtReplacementAmount="txtReplacementAmount_".$j;
			$txtBtbLcSelectedID="txtBtbLcSelectedID_".$j;
 			$cbo_sc_status="cbo_sc_status_".$j;
			
			if($j==1)
			{
				if($hiddenlcAttachSalesContractID!="")
				{
					$data_array_update="".$txt_system_id."*".$$salesContractID."*".$$txtReplacementAmount."*".$$txtBtbLcSelectedID."*".$$cbo_sc_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				}
				else
				{
					if($data_array!="") $data_array.=",";
					
					$data_array .="(".$id.",".$txt_system_id.",".$$salesContractID.",".$$txtReplacementAmount.",".$$txtBtbLcSelectedID.",".$$cbo_sc_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$id = $id+1;
				}
			}
			else
			{
				if($data_array!="") $data_array.=",";
				
				$data_array .="(".$id.",".$txt_system_id.",".$$salesContractID.",".$$txtReplacementAmount.",".$$txtBtbLcSelectedID.",".$$cbo_sc_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$id = $id+1;
			}
		}

		$flag=1;
		
		if($data_array!="")
		{
			$rID2=sql_insert("com_export_lc_atch_sc_info",$field_array,$data_array,0);
			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=0; 
			} 
		}
		
		if($data_array_update!="")
		{
			$rID=sql_update("com_export_lc_atch_sc_info",$field_array_update,$data_array_update,"id","".$hiddenlcAttachSalesContractID."",1);
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

if ($action=="save_update_delete_lc_order_info")
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
				
 		$field_array="id,com_export_lc_id,wo_po_break_down_id,attached_qnty,attached_rate,attached_value,fabric_description,category_no,hs_code,status_active,inserted_by,insert_date";
		$id = return_next_id( "id", "com_export_lc_order_info", 1 );

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
			
			if($$hiddenwopobreakdownid!="")
			{
				if($data_array!="") $data_array.=",";
				
				$data_array.="(".$id.",".$txt_system_id.",".$$hiddenwopobreakdownid.",".$$txtattachedqnty.",".$$hiddenunitprice.",".$$txtattachedvalue.",".$$txtfabdescrip.",".$$txtcategory.",".$$txthscode.",".$$cbopostatus.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				$id = $id+1;
			}
		}
		//print_r($data_array);die;
		$rID=sql_insert("com_export_lc_order_info",$field_array,$data_array,1);
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
		$field_array="id,com_export_lc_id,wo_po_break_down_id,attached_qnty,attached_rate,attached_value,fabric_description,category_no,hs_code,status_active,inserted_by,insert_date";
		$field_array_update="wo_po_break_down_id*attached_qnty*attached_rate*attached_value*fabric_description*category_no*hs_code*status_active*updated_by*update_date";
		
		$hiddenexportlcorderid=str_replace("'", '', $hiddenexportlcorderid);
		$id = return_next_id( "id", "com_export_lc_order_info", 1 );
		
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
			
			if($j==1)
			{
				if($hiddenexportlcorderid!="")
				{
					if(str_replace("'", '', $$cbopostatus)==0)
					{
						$invoice_no="";
						$po_id=$$hiddenwopobreakdownid;
						$sql_invoice="select a.invoice_no from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b where a.id=b.mst_id and a.lc_sc_id=$txt_system_id and a.is_lc=1 and b.po_breakdown_id=$po_id and b.current_invoice_qnty>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.invoice_no";
						$data=sql_select($sql_invoice);
						if(count($data)>0)
						{
							foreach($data as $row)
							{
								if($invoice_no=="") $invoice_no=$row[csf('invoice_no')]; else $invoice_no.=",\n".$row[csf('invoice_no')];	
							}
							
							echo "13**".$invoice_no."**1"; 
							die;	
						}
					}
					
					$data_array_update="".$$hiddenwopobreakdownid."*".$$txtattachedqnty."*".$$hiddenunitprice."*".$$txtattachedvalue."*".$$txtfabdescrip."*".$$txtcategory."*".$$txthscode."*".$$cbopostatus."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				}
				else
				{
					if($data_array!="") $data_array.=",";
					
					$data_array ="(".$id.",".$txt_system_id.",".$$hiddenwopobreakdownid.",".$$txtattachedqnty.",".$$hiddenunitprice.",".$$txtattachedvalue.",".$$txtfabdescrip.",".$$txtcategory.",".$$txthscode.",".$$cbopostatus.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$id = $id+1;
				}
			}
			else
			{
				if($$hiddenwopobreakdownid!="")
				{
					if($data_array!="") $data_array.=",";
					
					$data_array.="(".$id.",".$txt_system_id.",".$$hiddenwopobreakdownid.",".$$txtattachedqnty.",".$$hiddenunitprice.",".$$txtattachedvalue.",".$$txtfabdescrip.",".$$txtcategory.",".$$txthscode.",".$$cbopostatus.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$id = $id+1;
				}
			}
		}
		
		//echo "insert into com_sales_contract_order_info (".$field_array.") values".$data_array;die;
	
		$flag=1;
		
		if($data_array!="")
		{
			$rID2=sql_insert("com_export_lc_order_info",$field_array,$data_array,0);
			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=0; 
			} 
		}
		
		if($data_array_update!="")
		{
			$rID=sql_update("com_export_lc_order_info",$field_array_update,$data_array_update,"id","".$hiddenexportlcorderid."",1);
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

if ($action=="export_lien_letter")
{
	//echo $data; die;
	$data=explode("**",$data);
	
	//export lc lien-----------------
	if($data[0]==4)
	{
		$data_array=sql_select("select id, export_lc_no, lc_date, lien_bank, lien_date, lc_value, internal_file_no from com_export_lc where id='$data[1]'");
		foreach ($data_array as $row)
		{ 
			$internal_file_no	= $row[csf("internal_file_no")];
			$lien_date			= change_date_format($row[csf("lien_date")]);
			$lien_bank			= $row[csf("lien_bank")];
			$lc_no				= $row[csf("export_lc_no")];
			$lc_date			= change_date_format($row[csf("lc_date")]);
			$lc_value			= $row[csf("lc_value")];
		}

		$data_array1=sql_select("select wm.total_set_qnty as ratio, ci.attached_qnty from wo_po_break_down wb, wo_po_details_master wm, com_export_lc_order_info ci where wb.job_no_mst = wm.job_no and wb.id=ci.wo_po_break_down_id and ci.com_export_lc_id='$data[1]' and ci.status_active = '1' and ci.is_deleted = '0' order by ci.id");
		foreach ($data_array1 as $row1)
		{
			$order_qnty_in_pcs=$row1[csf('attached_qnty')]*$row1[csf('ratio')];
			$total_attach_qty+=$order_qnty_in_pcs;
		}
	}
	
	//Sales Contact Lien-------------
	if($data[0]==3)
	{
		$data_array=sql_select("select id, contract_no, contract_date, lien_bank, lien_date, contract_value, internal_file_no from com_sales_contract where id='$data[1]'");
		foreach ($data_array as $row)
		{
			$internal_file_no	= $row[csf("internal_file_no")];
			$contract_no		= $row[csf("contract_no")];
			$contract_value		= $row[csf("contract_value")];
			$contract_date		= change_date_format($row[csf("contract_date")]);
			$lien_bank			= $row[csf("lien_bank")];
			$lien_date			= $row[csf("lien_date")];
		}
		
		$data_array1=sql_select("select wm.total_set_qnty as ratio, ci.attached_qnty from wo_po_break_down wb, wo_po_details_master wm, com_sales_contract_order_info ci where wb.job_no_mst = wm.job_no and wb.id=ci.wo_po_break_down_id and ci.com_sales_contract_id='$data[1]' and ci.status_active = '1' and ci.is_deleted = '0' order by ci.id");
		foreach($data_array1 as $row1)
		{
			$order_qnty_in_pcs=$row1[csf('attached_qnty')]*$row1[csf('ratio')];
			$total_attach_qty+=$order_qnty_in_pcs;
		}
	}

	//bank information retriving here
	$data_array1=sql_select("select id, bank_name, branch_name, contact_person, address from lib_bank where id='$lien_bank'");
	foreach ($data_array1 as $row1)
	{ 
		$bank_name		= $row1[csf("bank_name")];
		$branch_name	= $row1[csf("branch_name")];
		$contact_person	= $row1[csf("contact_person")];
		$address		= $row1[csf("address")];
	}
	
	//letter body is retriving here
	$data_array2=sql_select("select letter_body from dynamic_letter where letter_type='$data[0]'");
	foreach ($data_array2 as $row2)
	{ 
		$letter_body = $row2[csf("letter_body")];
	}
	
	$raw_data=str_replace("INTERNALFILENO",$internal_file_no,$letter_body);
	$raw_data=str_replace("LIENDATE",$lien_date,$raw_data);
	$raw_data=str_replace("CONTACTPERSON",$contact_person,$raw_data);
	$raw_data=str_replace("BANKNAME",$bank_name,$raw_data);
	$raw_data=str_replace("BRANCHNAME",$branch_name,$raw_data);
	$raw_data=str_replace("ADDRESS",$address,$raw_data);
	$raw_data=str_replace("LCNUMBER",$lc_no,$raw_data);
	$raw_data=str_replace("LCDATE",$lc_date,$raw_data);
	$raw_data=str_replace("LCVALUE",$lc_value,$raw_data);
	$raw_data=str_replace("TOTALATTACHQTY",$total_attach_qty,$raw_data);
	
	echo $raw_data;
	exit();
}
?>


 
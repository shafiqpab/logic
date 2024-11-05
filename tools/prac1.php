<?php 
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
echo load_html_head_contents("Buyer Info","../",1 ,1 ,$unicode,1,'' );
?>
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";  
var permission='<? echo $permission; ?>';
function fnc_buyer_info(operation)
{
	alert('nc_buyer_info');
}
</script>
<body onLoad="set_hotkey();">
    <div align="center" style="width:900px">
    <? echo load_freeze_divs("../",$permission);  ?>
        <fieldset style="width:850px">
        <legend>Contact Info</legend>
            <form id="buyerinfo_1" name="buyerinfo_1" autocomplete="off">
           	<table cellpadding="0" cellspacing="2" border="0" width="100%">
			  <tr>
                    <td width="130" class="must_entry_caption">Contact Name  </td>
                    <td width="180">
                   		<input type="text" name="txt_buyer_name" id="txt_buyer_name" class="text_boxes" style="width:180px" maxlength="100" title="Maximum 100 Character" />
                        <input type="hidden" name="hidden_buyer_id" id="hidden_buyer_id" />  
                    </td>
                    <td width="130" class="must_entry_caption">Short Name</td>
                    <td width="180">
                        <input type="text" name="txt_short_name" id="txt_short_name" class="text_boxes" style="width:180px" maxlength="30" title="Maximum 30 Character"/>						
                    </td>
                    <td>Contact Person</td>
                    <td>
                        <input type="text" name="txt_contact_person" id="txt_contact_person" class="text_boxes" style="width:180px"  maxlength="100" title="Maximum 100 Character"/>						
                    </td>
                </tr>			
				<tr>
                    <td>Designation</td>
                    <td>
                        <input type="text" name="txt_designation" id="txt_desination" class="text_boxes" style="width:180px" maxlength="50" title="Maximum 50 Character"/>						
                    </td>
                    <td>Exporters Ref.</td>
                    <td>
                        <input type="text" name="txt_exporter_ref" id="txt_exporter_ref" class="text_boxes" style="width:180px" maxlength="20" title="Maximum 20 Character"/>						
                    </td>
                    <td>Email</td>
                    <td>
                        <input type="text" name="txt_buyer_email" id="txt_buyer_email" class="text_boxes" style="width:180px;" maxlength="100" title="Maximum 100 Character"/>						
                    </td>
                </tr>
                <tr>
                    <td>http://www.</td>
                    <td>
                        <input type="text" name="txt_web_site" id="txt_web_site" class="text_boxes" style="width:180px" maxlength="30" title="Maximum 30 Character"/>						
                    </td>
                    <td>Address1</td>
                    <td>
                        <textarea name="txt_address_1st" id="txt_address_1st" class="text_area" style="width:180px;" maxlength="500" title="Maximum 500 Character"></textarea>
                    </td>
                    <td>Address2</td>
                    <td>
                        <textarea name="txt_address_2nd" id="txt_address_2nd" class="text_area" style="width:180px;" maxlength="500" title="Maximum 500 Character"></textarea>
                    </td>
                </tr>
                <tr>
                    <td>Address3</td>
                    <td>
                        <textarea name="txt_address_3rd" id="txt_address_3rd" class="text_area" style="width:180px;" maxlength="500" title="Maximum 500 Character"></textarea>
                    </td>
                    <td>Address4</td>
                    <td>
                        <textarea name="txt_address_4th" id="txt_address_4th" class="text_area" style="width:180px;" maxlength="500" title="Maximum 500 Character"></textarea>
                    </td>
                    <td>Country</td>
                    <td>
                    <? echo create_drop_down( "cbo_country", 190, "select id,country_name from  lib_country where is_deleted=0 and status_active=1 order by country_name", "id,country_name", 1, "-- Select Country --", $selected_index, $onchange_func, $onchange_func_param_db,$onchange_func_param_sttc  ); ?>
                    </td>
                </tr> 
                <tr>
                	<td class="must_entry_caption">Party Type</td>
                    <td>
						<? echo create_drop_down( "cbo_party_type", 190, $party_type, "", 0, "", '', 'set_value_supplier_nature(this.value)', $onchange_func_param_db,$onchange_func_param_sttc); ?>				
                    </td>
                    <td class="must_entry_caption">Tag Company</td>
                    <td>
						<? echo create_drop_down( "cbo_buyer_company", 190, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name", "id,company_name", 0, "", '', ''); ?>				
                    </td>
                    <td>Link to Supplier</td>
                    <td >
						<? echo create_drop_down( "cbo_buyer_supplier", 190, "select id,supplier_name from  lib_supplier where is_deleted=0 and status_active=1 order by supplier_name", "id,supplier_name", 1, "-- Select Supplier --", $selected_index, $onchange_func, $onchange_func_param_db,$onchange_func_param_sttc  ); ?>
                	</td>
                </tr>
                <tr>
                	<td>Credit Limit (Days)</td>
                    <td>
					    <input type="text" name="txt_credit_limit_days" id="txt_credit_limit_days" class="text_boxes_numeric" style="width:180px" />						
                    </td>
                    <td>Credit Limit (Amount)</td>
                    <td>
                    <input type="text" name="txt_credit_limit_amount" id="txt_credit_limit_amount" class="text_boxes_numeric" style="width:100px" />	
						<? echo create_drop_down( "cbo_credit_limit_amount_curr",75, $currency, "", 0, "", '', ''  ); ?>				
                    </td>
                    <td>Discount Method</td>
                    <td >
						<? echo create_drop_down( "cbo_discount_method", 190, $currency, "", 1, "-- Select Method --", $selected_index, $onchange_func, $onchange_func_param_db,$onchange_func_param_sttc); ?>
                	</td>
                </tr>
                <tr>
                	<td>Security Deducted</td>
                    <td>
					    <? echo create_drop_down( "cbo_security_deducted", 190, $yes_no, "", 0, "", "", "", "",""); ?>					
                    </td>
                    <td>VAT to be Deducted</td>
                    <td>
						<? echo create_drop_down( "cbo_vat_to_be_deducted", 190,  $yes_no, "", 0, "", "", "", "",""); ?>				
                    </td>
                    <td>AIT to be Deducted</td>
                    <td>
						<? echo create_drop_down( "cbo_ait_to_be_deducted",190, $yes_no, "", 0, "", "", "", "",""); ?>
                	</td>
                </tr>
                <tr>
                    <td>Remark</td>
                    <td colspan="3">
                        <input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width:503px" maxlength="500" title="Maximum 500 Character"999/>
                    </td>
                     <td>Marketing Team</td>
                    <td>
                         <? echo create_drop_down( "cbo_marketing_team", 190, "select id,team_name from  lib_marketing_team where is_deleted=0 and status_active=1 order by team_name", "id,team_name", 1, "-- Select Marketing Team --", $selected_index, $onchange_func, $onchange_func_param_db,$onchange_func_param_sttc ); ?>
                	</td>
                </tr>	
				<tr>
                     <td>Sewing Effi Mkt. %</td>
                    <td>
					    <input type="text" name="txt_sewing_effi_mkt" id="txt_sewing_effi_mkt" class="text_boxes_numeric" style="width:180px" />						
                    </td>
                    <td>Sewing Effi Planing %</td>
                    <td>
					    <input type="text" name="txt_sewing_effi_planing" id="txt_sewing_effi_planing" class="text_boxes_numeric" style="width:180px" />						
                    </td>
                    <td>Control Delivery</td>
                    <td>
                         <? echo create_drop_down( "cbo_control_delivery", 190, $yes_no,'', 1, "--Select--", 0, "", "",'','' ); ?>
                	</td>
                </tr>
                <tr>
                	<td>Status</td>
                    <td>
                         <? echo create_drop_down( "cbo_status", 190, $row_status,'', $is_select, $select_text, 1, $onchange_func, "",'','' ); ?>
                	</td>
                    <td>Tag Sample</td>
                    <td>
                        <input type="button" name="cbo_tag_sample" id="cbo_tag_sample" class="image_uploader" style="width:190px;" value="Tag Sample" onClick="openmypage_tag_sample();" readonly />
                         <input type="text" name="sample_breck_down" id="sample_breck_down" />				
                    </td>
                    <td>Image</td>
                    <td height="25" valign="middle">
                    	<input type="button" class="image_uploader" style="width:192px" value="CLICK TO ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'buyer_info', 0 ,1)">
                    </td>
                </tr>
                <tr>
                    <td colspan="6" align="center" height="40" valign="middle" class="button_container"> <input type="hidden" name="update_id" id="update_id" /> 
                    <? echo load_submit_buttons($permission, "fnc_buyer_info", 0,0 ,"reset_form('buyerinfo_1','','')",1); ?> 
                    </td>					
                </tr>				
			</table>
            </form>
        </fieldset>
    </div>
</body>
<script>set_multiselect('cbo_party_type*cbo_buyer_company','0*0','0','','0*0');</script>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>

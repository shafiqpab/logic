<?
session_start(); 
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//----------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Buyer Info","../../", 1, 1, $unicode,1,'');
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission='<? echo $permission; ?>';
	//alert (permission);
	function fnc_buyer_info( operation )
	{
		if(operation==2)
		{
			var delt=confirm('Reday to Delete ?');
			if(delt)
			{
				var buyer_id_check=$("#buyer_id").val();
				var cbo_party_type=document.getElementById('cbo_party_type').value.split(',');
				
				if (form_validation('txt_buyer_name*txt_short_name*cbo_party_type*cbo_buyer_company','Buyer Name*Short Name*Party Type*Tag Company*')==false)
				{
					return;
				}
				else if($.inArray('90',cbo_party_type)> -1 && form_validation('cbo_buyer_supplier','Link to Supplier')==false )
				{
					alert(" Select Link To Supplier");
					return;
				}
				else // Save Here
				{
					var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_buyer_name*txt_short_name*txt_contact_person*txt_exporter_ref*cbo_party_type*txt_desination*cbo_buyer_company*cbo_country*txt_web_site*txt_buyer_email*txt_address_1st*txt_address_2nd*txt_address_3rd*txt_address_4th*cbo_buyer_supplier*cbo_status*cbo_control_delivery*txt_remark*txt_credit_limit_days*txt_credit_limit_amount*cbo_credit_limit_amount_curr*cbo_discount_method*cbo_security_deducted*cbo_vat_to_be_deducted*cbo_ait_to_be_deducted*txt_sewing_effi_mkt*txt_sewing_effi_planing*update_id*cbo_marketing_team*hidden_buyer_id*cbo_cut_Off_used*txt_deffd_lc_cost_percent*txt_del_buffer_days*txt_min_quoted_profit_parcent*txt_min_budgeted_profit_parcent*cbo_commercial_invoice*is_posted_accounts*cbo_tol_level*txt_sequence*txt_contact_no',"../../");
					//alert(data);return;
					freeze_window(operation);
					http.open("POST","requires/buyer_info_controller.php",true);
					http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
					http.send(data);
					http.onreadystatechange = fnc_on_submit_reponse;
				}
			}
			else
			{
				return false;	
			}
		}
		else if(operation==0 || operation==1)
		{
			var buyer_id_check=$("#buyer_id").val();
			var cbo_party_type=document.getElementById('cbo_party_type').value.split(',');
		//alert(cbo_party_type);
			
			if (form_validation('txt_buyer_name*txt_short_name*cbo_party_type*cbo_buyer_company','Buyer Name*Short Name*Party Type*Tag Company*')==false)
			{
				return;
			}
			else if($.inArray('90',cbo_party_type)> -1 && form_validation('cbo_buyer_supplier','Link to Supplier')==false )
			{
				//alert(9);
				alert(" Select Link To Supplier");
				return;
			}
			else // Save Here
			{
				var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_buyer_name*txt_short_name*txt_contact_person*txt_exporter_ref*cbo_party_type*txt_desination*cbo_buyer_company*cbo_country*txt_web_site*txt_buyer_email*txt_address_1st*txt_address_2nd*txt_address_3rd*txt_address_4th*cbo_buyer_supplier*cbo_status*cbo_control_delivery*txt_remark*txt_credit_limit_days*txt_credit_limit_amount*cbo_credit_limit_amount_curr*cbo_discount_method*cbo_security_deducted*cbo_vat_to_be_deducted*cbo_ait_to_be_deducted*txt_sewing_effi_mkt*txt_sewing_effi_planing*update_id*cbo_marketing_team*hidden_buyer_id*cbo_cut_Off_used*txt_deffd_lc_cost_percent*txt_del_buffer_days*txt_min_quoted_profit_parcent*txt_min_budgeted_profit_parcent*cbo_commercial_invoice*is_posted_accounts*cbo_bank_name*cbo_partial_rlz*cbo_tol_level*txt_sequence*txt_contact_no',"../../");
				//alert(data);return;
				freeze_window(operation);
				http.open("POST","requires/buyer_info_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_on_submit_reponse;
			}	
		}
	}

	function fnc_on_submit_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			if(reponse[0]==50)
			{
				alert (reponse[1]);
				release_freezing();
				return;
			}
			//alert(reponse[0]);
			show_msg(reponse[0]);
			show_list_view(reponse[1],'show_buyer_list_view','list_view_div','requires/buyer_info_controller','setFilterGrid("list_view",-1)');
			reset_form('buyerinfo_1','','');
			set_button_status(0, permission, 'fnc_buyer_info');
			release_freezing();
		}
	} 

	function openmypage_tag_sample()
	{
        var hidden_buyer_id = $('#hidden_buyer_id').val();
		var company_id = $('#cbo_buyer_company').val();
		if(hidden_buyer_id=="")
		{
			alert("Save Buyer First");
			return;
		}
		var title = 'Sample Name Selection Form';	
		var page_link = 'requires/buyer_info_controller.php?hidden_buyer_id='+hidden_buyer_id+'&company='+company_id+'&action=sample_name_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=520px,height=470px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var sample_breck_down=this.contentDoc.getElementById("sample_breck_down").value;	 //Access form field with id="emailfield"
			$('#sample_breck_down').val(sample_breck_down);
		}
	}
    function openmypage_information()
    {
        var hidden_buyer_id = $('#hidden_buyer_id').val();
        var update_id = $('#update_id').val();
        if(hidden_buyer_id=="")
        {
            alert("Save Buyer First");
            return;
        }
        //alert(hidden_buyer_id);
        var title = 'Additional Info';   
        var page_link = 'requires/buyer_info_controller.php?hidden_buyer_id='+hidden_buyer_id+'&action=buyer_profile_popup';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=680px,height=450px,center=1,resize=1,scrolling=0','../');
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
            var information=this.contentDoc.getElementById("information").value;     //Access form field with id="emailfield"
            $('#information').val(information);
        }
    }
	
	function openmypage_comm_importFabric()
	{
		var hidden_buyer_id = $('#hidden_buyer_id').val();
        var update_id = $('#update_id').val();
        if(hidden_buyer_id=="")
        {
            alert("Save Buyer First");
            return;
        }
		
		var title = 'Com. Cost for import fabric % and Short Realization %';   
        var page_link = 'requires/buyer_info_controller.php?hidden_buyer_id='+hidden_buyer_id+'&action=comm_importFabric';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=450px,center=1,resize=1,scrolling=0','../');
        emailwindow.onclose=function()
        {
            //var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
            //var information=this.contentDoc.getElementById("information").value;     //Access form field with id="emailfield"
           // $('#information').val(information);
        }
	}
	
	function openmypage_currency_conversion_rate()
	{
        var update_id = $('#update_id').val();
        if(update_id=="")
        {
            alert("Save Buyer First");
            return;
        }
		
		var title = 'Buyer Wise Currency Conversion Rate';   
        var page_link = 'requires/buyer_info_controller.php?update_id='+update_id+'&action=buyer_currency_conversion_rate';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=450px,center=1,resize=1,scrolling=0','../');
        emailwindow.onclose=function()
        {
            //var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
            //var information=this.contentDoc.getElementById("information").value;     //Access form field with id="emailfield"
           // $('#information').val(information);
        }
	}

</script>
</head>	
<body onLoad="set_hotkey()">
<div align="center" style="width:100%;">
	<? echo load_freeze_divs("../../",$permission);  ?>
	<fieldset style="width:1010px;">
    <legend>Contact Info</legend>
		 <form name="buyerinfo_1" id="buyerinfo_1" autocomplete="off">	
			<table cellpadding="0" cellspacing="2" border="0" width="100%">
			  <tr>
                    <td width="184" class="must_entry_caption">Contact Name  </td>
                    <td width="224">
                   		<input type="text" name="txt_buyer_name" id="txt_buyer_name" class="text_boxes" style="width:180px" maxlength="100" title="Maximum 100 Character" />
                        <input type="hidden" name="hidden_buyer_id" id="hidden_buyer_id" />  
                    </td>
                    <td width="194" class="must_entry_caption">Short Name</td>
                    <td width="235"><input type="text" name="txt_short_name" id="txt_short_name" class="text_boxes" style="width:180px" maxlength="30" title="Maximum 30 Character"/></td>
                    <td width="193">Contact Person</td>
                    <td width="262"><input type="text" name="txt_contact_person" id="txt_contact_person" class="text_boxes" style="width:180px"  maxlength="100" title="Maximum 100 Character"/></td>
                </tr>	
				<tr>
                    <td>Designation</td>
                    <td><input type="text" name="txt_designation" id="txt_desination" class="text_boxes" style="width:180px" maxlength="50" title="Maximum 50 Character"/></td>
                    <td>Exporters Ref.</td>
                    <td><input type="text" name="txt_exporter_ref" id="txt_exporter_ref" class="text_boxes" style="width:180px" maxlength="20" title="Maximum 20 Character"/></td>
                    <td>Email</td>
                    <td><input type="text" name="txt_buyer_email" id="txt_buyer_email" class="text_boxes" style="width:180px;" maxlength="100" title="Maximum 100 Character"/></td>
                </tr>
                <tr>
                    <td>http://www.</td>
                    <td><input type="text" name="txt_web_site" id="txt_web_site" class="text_boxes" style="width:180px" maxlength="30" title="Maximum 30 Character"/></td>
                    <td>Address1</td>
                    <td><textarea name="txt_address_1st" id="txt_address_1st" class="text_area" style="width:180px;" maxlength="500" title="Maximum 500 Character"></textarea></td>
                    <td>Address2</td>
                    <td><textarea name="txt_address_2nd" id="txt_address_2nd" class="text_area" style="width:180px;" maxlength="500" title="Maximum 500 Character"></textarea></td>
                </tr>
                <tr>
                    <td>Address3</td>
                    <td><textarea name="txt_address_3rd" id="txt_address_3rd" class="text_area" style="width:180px;" maxlength="500" title="Maximum 500 Character"></textarea></td>
                    <td>Address4</td>
                    <td><textarea name="txt_address_4th" id="txt_address_4th" class="text_area" style="width:180px;" maxlength="500" title="Maximum 500 Character"></textarea></td>
                    <td>Country</td>
                    <td><? echo create_drop_down( "cbo_country", 190, "select id,country_name,short_name,status_active from  lib_country where is_deleted=0 and status_active=1 order by country_name", "id,country_name,short_name,status_active", 1, "-- Select Country --", $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index, $tab_index, $new_conn, $field_name, "TEST", "2,3"  ); ?></td>
                </tr> 
                <tr>
                	<td class="must_entry_caption">Party Type</td>
                    <td><? echo create_drop_down( "cbo_party_type", 190, $party_type, "", 0, "", '', 'set_value_supplier_nature(this.value)', $onchange_func_param_db,$onchange_func_param_sttc); ?></td>
                    <td class="must_entry_caption">Tag Company</td>
                    <td><? echo create_drop_down( "cbo_buyer_company", 190, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name", "id,company_name", 0, "", '', ''); ?></td>
                    <td>Link to Supplier</td>
                    <td><? 
                    echo create_drop_down( "cbo_buyer_supplier", 190, "select id,supplier_name from  lib_supplier where is_deleted=0 and status_active=1 order by supplier_name", "id,supplier_name", 1, "-- Select Supplier --", $selected_index, $onchange_func, $onchange_func_param_db,$onchange_func_param_sttc,"","","","","","","combo_boxes_search"  );
                    ?></td>
                </tr>
                <tr>
                	<td>Credit Limit (Days)</td>
                    <td><input type="text" name="txt_credit_limit_days" id="txt_credit_limit_days" class="text_boxes_numeric" style="width:180px" /></td>
                    <td>Credit Limit (Amount)</td>
                    <td>
                   		<input type="text" name="txt_credit_limit_amount" id="txt_credit_limit_amount" class="text_boxes_numeric" style="width:100px" />	
						<? echo create_drop_down( "cbo_credit_limit_amount_curr",75, $currency, "", 0, "", '', ''  ); ?>				
                    </td>
                    <td>Discount Method</td>
                    <td>
                        <? 
                        echo create_drop_down( "cbo_discount_method", 190, $currency, "", 1, "-- Select Method --", $selected_index, $onchange_func, $onchange_func_param_db,$onchange_func_param_sttc); 
                        ?>
                    </td>
                </tr>
                <tr>
                	<td>Security Deducted</td>
                    <td><? echo create_drop_down( "cbo_security_deducted", 190, $yes_no, "", 0, "", "", "", "",""); ?></td>
                    <td>VAT to be Deducted</td>
                    <td><? echo create_drop_down( "cbo_vat_to_be_deducted", 190,  $yes_no, "", 0, "", "", "", "",""); ?></td>
                    <td>AIT to be Deducted</td>
                    <td><? echo create_drop_down( "cbo_ait_to_be_deducted",190, $yes_no, "", 0, "", "", "", "",""); ?></td>
                </tr>
                <tr>
                    <td>Remark</td>
                    <td colspan="3"><input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width:94%" maxlength="500" title="Maximum 500 Character"/></td>
                    <td>Marketing Team</td>
                    <td><? echo create_drop_down( "cbo_marketing_team", 190, "select id,team_name from  lib_marketing_team where is_deleted=0 and status_active=1 order by team_name", "id,team_name", 1, "-- Select Marketing Team --", $selected_index, $onchange_func, $onchange_func_param_db,$onchange_func_param_sttc );?></td>
                </tr>	
				<tr>
                    <td>Sewing Effi Mkt. %</td>
                    <td><input type="text" name="txt_sewing_effi_mkt" id="txt_sewing_effi_mkt" class="text_boxes_numeric" style="width:180px" /></td>
                    <td>Sewing Effi Planing %</td>
                    <td><input type="text" name="txt_sewing_effi_planing" id="txt_sewing_effi_planing" class="text_boxes_numeric" style="width:180px" /></td>
                    <td>Deffd. LC Cost%</td>
                    <td><input type="text" name="txt_deffd_lc_cost_percent" id="txt_deffd_lc_cost_percent" class="text_boxes_numeric" style="width:180px" /></td>
                </tr>
                <tr>
                    <td>Cut-Off Used</td>
                    <td><? echo create_drop_down( "cbo_cut_Off_used", 190, $yes_no,'', '', '', 2,'', "",'','' ); ?></td>
                    <td>Control Delivery</td>
                    <td><? echo create_drop_down( "cbo_control_delivery", 190, $yes_no,'', 1, "--Select--", 0, "", "",'','' ); ?></td>
                    <td> Delivery Buffer Days</td>
                    <td height="25" valign="middle"><input type="text" name="txt_del_buffer_days" id="txt_del_buffer_days" class="text_boxes_numeric" style="width:180px" /></td>
                </tr>
                <tr>
                    <td>Min Quoted Profit %</td>
                    <td><input type="text" name="txt_min_quoted_profit_parcent" id="txt_min_quoted_profit_parcent" class="text_boxes_numeric" style="width:180px" /></td>
                    <td>Min Budgeted Profit %</td>
                    <td><input type="text" name="txt_min_budgeted_profit_parcent" id="txt_min_budgeted_profit_parcent" class="text_boxes_numeric" style="width:180px" /></td>
                    <td>Status</td>
                    <td>
                        <? 
                         echo create_drop_down( "cbo_status", 190, $row_status,'', $is_select, $select_text, 1, $onchange_func, "",'','' ); 
                        ?>
                        </td>
                </tr>
                <tr>
                	<td>Commercial Invoice</td>
                    <td><? echo create_drop_down( "cbo_commercial_invoice", 190, $commercial_invoice_format,'', $is_select, $select_text, 1, $onchange_func, "",'','' ); ?></td>
                    <td>Tag Sample</td>
                    <td>
                        <input type="button" name="cbo_tag_sample" id="cbo_tag_sample" class="image_uploader" style="width:190px;" value="Tag Sample" onClick="openmypage_tag_sample();" readonly />
                         <input type="hidden" name="sample_breck_down" id="sample_breck_down" />				
                    </td>
                    <td>Image</td>
                    <td><input type="button" class="image_uploader" style="width:192px" value="CLICK TO ADD/VIEW IMAGE" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'buyer_info', 0 ,1)"> </td>
                </tr>
                <tr>
                    <td>Additional Info</td>
                    <td>
                        <input type="button" name="cbo_info" class="image_uploader" id="cbo_info" style="width:190px;" value="Information" onClick="openmypage_information();"  readonly />
                         <input type="hidden" name="information" id="information" />                
                    </td>
                    <td>&nbsp;</td>
                    <td>
                        <input type="button" name="commcostFab" class="image_uploader" id="commcostFab" style="width:190px;" value="Com. Cost for import fabric" onClick="openmypage_comm_importFabric();"  readonly />
                         <input type="hidden" name="information" id="information" />                
                    </td>
                    <td>Bank</td>
                    <td>
                        <? echo create_drop_down( "cbo_bank_name",190, "select id,bank_name from lib_bank where status_active=1", "id,bank_name", 0, "Select Bank", "1", "", "",""); ?>               
                    </td>
                </tr>
                <tr>
                    <td>Allow Partial Realized</td>
                    <td>
                        <? echo create_drop_down( "cbo_partial_rlz",190, $yes_no, "", 1, "Select", "", "", "",""); ?>               
                    </td>
                    <td>SC/LC Tol. Level</td>
                    <td>
                        <? 
						$lcSc_tol_level_arr= array(0=>"PO",1=>"LC/SC");
						echo create_drop_down( "cbo_tol_level",190, $lcSc_tol_level_arr, "", 0, "", 0, "", "",""); ?>               
                    </td>
                    <td>Sequence No</td>
                    <td>
                        <input type="text" name="txt_sequence" id="txt_sequence" style="width:180px" class="text_boxes_numeric"  maxlength="50" title="Maximum 50 Character">
                    </td>
                </tr>
                <tr>
                    <td>Buyer ID</td>
                    <td><input type="text" name="update_id" id="update_id" style="width:180px" class="text_boxes_numeric" readonly disabled/></td>
                    <td>&nbsp;</td>
                    <td><input type="button" name="btnbuyer_currency_rate" id="btnbuyer_currency_rate" class="image_uploader" style="width:190px;" value="Currency Conversion Rate" onClick="openmypage_currency_conversion_rate();" readonly /></td>
                    <td>Contact No</td>
                    <td><input type="text" name="txt_contact_no" id="txt_contact_no" style="width:180px" class="text_boxes"></td>
                </tr>
                <tr>
				 	<td colspan="6" id="posted_account_td" style="max-width:100px; color:red; font-size:14px;"></td>
                </tr>
                <tr>
                    <td colspan="6" align="center" height="40" valign="middle" class="button_container">
                    	<input type="hidden" name="is_posted_accounts" id="is_posted_accounts"/>
                    	<? echo load_submit_buttons($permission, "fnc_buyer_info", 0,0 ,"reset_form('buyerinfo_1','','')",1); ?> 
                    </td>					
                </tr>				
			</table>
		  </form>
	</fieldset>	
	<div style="width:100%; float:left; margin:auto" align="center" id="search_container">
		<fieldset style="width:1010px; margin-top:10px">
            	<table width="1010" cellspacing="2" cellpadding="0" border="0">
					<tr>
						<td colspan="3">
                        <div id="list_view_div">
                        	<?
								$arr=array (3=>$party_type,8=>$currency,10=>$row_status);
								echo create_list_view ( "list_view", "ID,Contact Name,Short Name, Sewing Effi Mkt. %,Contact Person,Designation,Credit Limit(Days),Credit Limit (Amount),Currency,Del Buffer Days, Status", "60,150,100,150,100,120,80,80,80,70","1110","220",0, "select buyer_name,short_name,sewing_effi_mkt_percent,contact_person,designation,credit_limit_days,credit_limit_amount,credit_limit_amount_currency,delivery_buffer_days,status_active,id from lib_buyer where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,0,0,0,0,0,0,0,credit_limit_amount_currency,0,status_active", $arr , "id,buyer_name,short_name,sewing_effi_mkt_percent,contact_person,designation,credit_limit_days,credit_limit_amount,credit_limit_amount_currency,delivery_buffer_days,status_active", "requires/buyer_info_controller", 'setFilterGrid("list_view",-1);','0,0,0,1,0,0,1,1,0,0,0,0'); ?>
                        </div>
						</td>
					</tr>
				</table>
		</fieldset>	
	</div>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
//set_multiselect('cbo_party_type*cbo_buyer_company*cbo_bank_name','0*0*0','0','0*0*0','__set_supplier_status__../contact_details/requires/buyer_info_controller*__set_supplier_status__../contact_details/requires/buyer_info_controller*__set_supplier_status__../contact_details/requires/buyer_info_controller');
set_multiselect('cbo_party_type*cbo_buyer_company*cbo_bank_name','0*0*0','0','','0*0*0','0*0*0');
</script>

<script>$("#cbo_tol_level").val(0);</script>
</html>
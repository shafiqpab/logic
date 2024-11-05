<?php
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
//echo load_html_head_contents("Company Details","../../", 1, 1, $unicode);
echo load_html_head_contents("Company Details", "../../", 1, 1,$unicode,1,'');

?>

<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission='<? echo $permission; ?>';	
		
function fnc_company_details( operation )
{
	if (form_validation('cbo_group_name*txt_company_name*txt_company_short_name','Group Name*Company Name* Company Short Name')==false)
	{
		return;
	}
	else
	{
		eval(get_submitted_variables('cbo_group_name*txt_company_name*txt_company_short_name*cbo_service_cost_allocation*cbo_posting_in_previous_yr*cbo_statutory_account*txt_ceo*txt_cfo*cbo_company_nature*cbo_core_business*txt_email*txt_website*txt_ac_code_length*cbo_profit_center_affected*txt_contact_person*txt_plot_no*txt_level_no*txt_road_no*txt_block_no*cbo_country*txt_province*txt_city_town*txt_zip_code*txt_trade_license*txt_incorporation_no*txt_erc_no*txt_irc_no*txt_epb_reg_no*txt_trade_license_renewal*txt_erc_expiry_date*txt_irc_expiry_date*txt_tin_number*txt_vat_number*txt_bangladeh_bank_reg_no*cbo_status*txt_alter_standard*txt_reject_standard*txt_bin_no*update_id*txt_rex_number*txt_rex_date'));
		//alert(cbo_group_name);
		
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_group_name*txt_company_name*txt_company_short_name*cbo_service_cost_allocation*cbo_posting_in_previous_yr*cbo_statutory_account*txt_ceo*txt_cfo*cbo_company_nature*cbo_core_business*txt_email*txt_website*txt_ac_code_length*cbo_profit_center_affected*txt_contact_person*txt_plot_no*txt_level_no*txt_road_no*txt_block_no*cbo_country*txt_province*txt_city_town*txt_zip_code*txt_trade_license*txt_incorporation_no*txt_erc_no*txt_irc_no*txt_epb_reg_no*txt_trade_license_renewal*txt_erc_expiry_date*txt_irc_expiry_date*txt_tin_number*txt_vat_number*txt_bangladeh_bank_reg_no*cbo_status*txt_contact_no*txt_alter_standard*txt_reject_standard*txt_bin_no*update_id*cbo_business_nature*txt_rex_number*txt_rex_date',"../../");
		
		 
		freeze_window(operation);
		 
		http.open("POST","requires/company_details_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_company_details_reponse;
	}
}

function fnc_company_details_reponse()
{
	if(http.readyState == 4) 
	{ 

		var reponse=trim(http.responseText).split('**');
		if (reponse[0].length>2) reponse[0]=10;
		show_msg(reponse[0]);
		show_list_view(reponse[1],'company_list_view','company_list_view','../cost_center/requires/company_details_controller','setFilterGrid("list_view",-1)');
		reset_form('companydetailsform_1','','');
		$('#update').removeClass('formbutton').addClass('formbutton_disabled');
		//set_button_status(0, permission, 'fnc_company_details',1);
		release_freezing();
	}
}

function fnc_valid_num(val,field_id)
{
	if(val!="")
	{
		var data=val.split(".");
		if(data[1]!=undefined)
		{
			//alert (data[1]);
			//var minutes=data[1];
			var str_length=data[1].length;
			if(str_length>=2)
			{
				var valid_time=data[1].substr(0, 2);
			}
			else
			{
				var valid_time=data[1];
			}
			document.getElementById(field_id).value=data[0]+'.'+valid_time;
		}
	}

}
	</script>
</head>



<body onLoad="set_hotkey()">
    <div align="center">
		<? echo load_freeze_divs ("../../",$permission);  ?>
        
        <form id="companydetailsform_1"  name="companydetailsform_1" autocomplete="off">
            <fieldset style="width:1000px;">
            <legend>general information</legend>
                <table width="100%" cellspacing="2" border="1">
                    <tr>
                        <td width="140" class="must_entry_caption">
                            Group Name
                        </td>
                        <td width="160">   
							<? 
								echo create_drop_down( "cbo_group_name", 155, "select group_name,id from lib_group where is_deleted=0 and status_active=1 order by group_name", "id,group_name", 1, '--Select--', 0, '');
                            ?>
                        </td>
                        <td width="140" class="must_entry_caption">
                            Company Name
                        </td>
                        <td width="160">
                            <input type="text" name="txt_company_name" id="txt_company_name" style="width:143px" class="text_boxes" maxlength="64" title="Maximum 64 Character">
                        </td>
                        <td width="140" class="must_entry_caption">
                            Company Short Name
                        </td>
                        <td width="160">
                            <input type="text" name="txt_company_short_name" id="txt_company_short_name" style="width:143px" class="text_boxes" maxlength="5" title="Maximum 5 Character">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Service Cost Allocation
                        </td>
                        <td>   
							<?
								echo create_drop_down( "cbo_service_cost_allocation", 155, $yes_no, "", 0, '', 2,'' );
                            ?>
                        </td>
                        <td>
                            Posting in Previous Yr
                        </td>
                        <td>
							<?
                                echo create_drop_down( "cbo_posting_in_previous_yr", 155, $yes_no, "", 0, '', 2, '');
                            ?>
                        </td>
                        <td>
                            Statutory Account
                        </td>
                        <td>
							<? 
								echo create_drop_down( "cbo_statutory_account", 155, $yes_no, "", 0, '0', 2, '' );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            CEO 
                        </td>
                        <td> 
                            <input name="txt_ceo" id="txt_ceo" class="text_boxes" style="width:143px" maxlength="64" title="Maximum 64 Character"/>   
                        </td>
                        <td> 
                            CFO
                        </td>
                        <td>
                            <input type="text" name="txt_cfo" id="txt_cfo" class="text_boxes" style="width:143px" maxlength="64" title="Maximum 64 Character" >
                        </td>
                        <td>
                            Company Nature
                        </td>
                        <td>
							<? 
								echo create_drop_down( "cbo_company_nature", 155, $company_nature, "", 1, '--Select--', 0,''); 
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                        Core Business 
                        </td>
                        <td>
							<? 
								echo create_drop_down( "cbo_core_business", 155, $core_business, "", 1, '--Select--', 0, ''  ); 
                            ?>   
                        </td>
                        <td>
                            E-mail
                        </td>
                        <td>
                            <input type="text" name="txt_email" id="txt_email" class="text_boxes" style="width:143px;" maxlength="32" title="Maximum 32 Character"/>
                        </td>
                        <td>
                            Website
                        </td>
                        <td>
                            <input type="text" name="txt_website" id="txt_website"  class="text_boxes" style="width:143px;" maxlength="64" title="Maximum 64 Character"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            A/C Code Length
                        </td>
                        <td>   
                            <input type="text" name="txt_ac_code_length" id="txt_ac_code_length" class="text_boxes_numeric" style="width:143px;" value="6">
                        </td>
                        <td>
                            Profit Center Affected
                        </td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_profit_center_affected", 155, $yes_no, "", 0, '', 2, '');
                            ?>
                        </td>
                        <td>
                            Contact Person
                        </td>
                        <td>
                            
                            <input type="text" name="txt_contact_person" id="txt_contact_person" class="text_boxes" style="width:143px;" maxlength="64" title="Maximum 64 Character">
                            
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Alter % Standard
                        </td>
                        <td>   
                            <input type="text" name="txt_alter_standard" id="txt_alter_standard" class="text_boxes_numeric" style="width:143px;" onBlur="fnc_valid_num(this.value,'txt_alter_standard')">
                        </td>
                        <td>
                            Reject % Standard
                        </td>
                        <td> 
                            <input type="text" name="txt_reject_standard" id="txt_reject_standard" class="text_boxes_numeric" style="width:143px;" onBlur="fnc_valid_num(this.value,'txt_reject_standard')">
                        </td>
                        <td>Business Nature</td>
                        <td>
						<? 
							echo create_drop_down( "cbo_business_nature", 155, $business_nature_arr, "", 1, '--Select--', 0, ''  ); 
                         ?>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <fieldset style="width:1000px">      <!--address here-->
            <legend>address</legend>
                <table width="100%" cellspacing="2">
                    <tr>
                        <td width="140px">
                            Plot No
                        </td>
                        <td width="160px">   
                            <input type="text" name="txt_plot_no" id="txt_plot_no" style="width:143px;" class="text_boxes" maxlength="20" title="Maximum 20 Character" >
                        </td>
                        <td width="140px">
                            Level No
                        </td>
                        <td width="160px">
                            <input type="text" name="txt_level_no" id="txt_level_no" style="width:143px;" class="text_boxes" maxlength="20" title="Maximum 20 Character">
                        </td>
                        <td width="140px">
                            Road No
                        </td>
                        <td width="160px">
                            <input type="text" name="txt_road_no" id="txt_road_no" style="width:143px;" class="text_boxes" maxlength="20" title="Maximum 20 Character">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Block No
                        </td>
                        <td>   
                            <input type="text" name="txt_block_no" id="txt_block_no" class="text_boxes" style="width:143px;"  maxlength="20" title="Maximum 20 Character">
                        </td>
                        <td> 
                            Country
                        </td>
                        <td>
							<?
								echo create_drop_down( "cbo_country", 155, "select country_name,id from lib_country where is_deleted=0  and 
								status_active=1 order by country_name", "id,country_name", 1,'--Select--', 0, ''); 
                            ?>
                        </td>
                        <td>
                            Province
                        </td>
                        <td>
                            <input type="text" name="txt_province" id="txt_province"  class="text_boxes" style="width:143px;" maxlength="30" title="Maximum 30 Character">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            City / Town
                        </td>
                        <td>   
                            <input type="text" name="txt_city_town" id="txt_city_town" class="text_boxes" style="width:143px;" maxlength="100" title="Maximum 100 Character">
                        </td>
                        <td> 
                            Zip Code
                        </td>
                        <td>
                            <input type="text" name="txt_zip_code" id="txt_zip_code" class="text_boxes" style="width:143px;" maxlength="20" title="Maximum 20 Character">
                        </td>
                        <td> 
                            Contact Number
                        </td>
                        <td>
                            <input type="text" name="txt_contact_no" id="txt_contact_no" class="text_boxes" style="width:143px;" maxlength="20" title="Maximum 20 Character">
                        </td>
                    </tr>
                </table>
            </fieldset>
            <fieldset style="width:1000px">      <!--legal doc here-->
            <legend>Legal Document</legend>
                <table width="100%">
                    <tr>
                        <td width="130px">
                            Trade License No
                        </td>
                        <td width="160px">   
                            <input type="text" name="txt_trade_license" id="txt_trade_license" style="width:143px;" class="text_boxes" maxlength="20" title="Maximum 20 Character"></td>
                        <td width="150px">
                            Incorporation No
                        </td>
                        <td width="160px">
                            <input type="text" name="txt_incorporation_no" id="txt_incorporation_no" style="width:143px;" class="text_boxes" maxlength="20" title="Maximum 20 Character">
                        </td>
                        <td width="140px">
                            ERC No
                        </td>
                        <td width="160px">
                            <input type="text" name="txt_erc_no" id="txt_erc_no" style="width:143px;" class="text_boxes" maxlength="20" title="Maximum 20 Character">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            IRC No
                        </td>
                        <td>   
                            <input type="text" name="txt_irc_no" id="txt_irc_no" class="text_boxes" style="width:143px;" maxlength="50" title="Maximum 50 Character">
                        </td>
                        <td>
                            EPB Reg. No
                        </td>
                        <td>
                            <input type="text" name="txt_epb_reg_no" id="txt_epb_reg_no" class="text_boxes" style="width:143px;" maxlength="20" title="Maximum 20 Character" >
                        </td>
                        <td>
                            Trade License Renewal
                        </td>
                        <td>
                            <input type="text" name="txt_trade_license_renewal " id="txt_trade_license_renewal" class="datepicker"  style="width:143px;" >
                        </td>
                    </tr>
                    <tr>
                        <td>
                            ERC Expiry Date
                        </td>
                        <td>   
                            <input type="text" name="txt_erc_expiry_date" id="txt_erc_expiry_date" class="datepicker" style="width:143px;" >
                        </td>
                        <td>
                            IRC Expiry Date
                        </td>
                        <td>
                            <input type="text" name="txt_irc_expiry_date " id="txt_irc_expiry_date"  class="datepicker" style="width:143px;">
                        </td>
                        <td>
                            TIN Number
                        </td>
                        <td>
                            <input type="text" name="txt_tin_number" id="txt_tin_number" class="text_boxes" style="width:143px;" maxlength="20" title="Maximum 20 Character">
                        </td>
                    </tr>
                    <tr>
                        <td>VAT Number</td>
                        <td>   
                            <input type="text" name="txt_vat_number" id="txt_vat_number" class="text_boxes" style="width:143px;" maxlength="20" title="Maximum 20 Character">
                        </td>
                        <td>
                            Bangladesh Bank Registration No
                        </td>
                        <td>
                            <input type="text" name="txt_bangladeh_bank_reg_no" id="txt_bangladeh_bank_reg_no" class="text_boxes" style="width:143px;" maxlength="50" title="Maximum 50 Character">
                        </td>
                        <td>
                            BIN Number
                        </td>
                        <td>
                            <input type="text" name="txt_bin_no" id="txt_bin_no" class="text_boxes" style="width:143px;" maxlength="50" title="Maximum 50 Character">
                        </td>
                    </tr>
                    <tr>
                        <td>REX No</td>
                        <td>   
                            <input type="text" name="txt_rex_number" id="txt_rex_number" class="text_boxes" style="width:143px;" maxlength="20" title="Maximum 20 Character">
                        </td>
                        <td>REX Reg. Date</td>
                        <td>
                        	<input type="text" name="txt_rex_date " id="txt_rex_date"  class="datepicker" style="width:143px;" readonly>
                        </td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </table>
            </fieldset>
            <fieldset style="width:1000px;">
            <legend>Status</legend>
                <table cellpadding="0" cellspacing="1" width="100%">
                    <tr>
                        <td>
                            Status
                        </td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_status", 110, $row_status,"", "", "", 1, "" );
                            ?>
                        </td>
                        <td>&nbsp;
                            
                        </td>
                        <td>
                            <div style="padding-top:5px"> 
                                <div id="upload" onClick="call_image()">
                                    <span>Select Logo</span> 
                                </div>
                            </div>
                            <div style="width:100px; padding-top:5px" align="center">
                            </div>
                        </td>
                        <td>&nbsp;
                            
                        </td>
                        <td height="25" valign="middle" class="image_uploader" onClick="file_uploader ( '../../', document.getElementById('update_id').value,'', 'company_details', 0 ,1,0,0)" align="center"> <strong>CLICK TO ADD IMAGE</strong>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6">&nbsp;
                            
                        </td>
                        <td>
                            <input type="hidden" id="update_id" name="update_id">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" align="center" style="padding-top:10px;" class="button_container">
                            <? 
                                //echo load_submit_buttons( $permission, "fnc_company_details", 0,0 ,"reset_form('companydetailsform_1','','',1)");
                            ?>	
							 <input type="button" id="update" name="update" class="formbutton_disabled" value="Update" onClick="fnc_company_details(1);" style="width:100px;">					
                        </td>
                    </tr>
                </table>
            </fieldset>
        </form>
        
            <fieldset style="width:1000px;">
            <legend>List View</legend>
                <table width="100%" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                        <td id="company_list_view">
							<?
							$group_name=return_library_array( "select group_name,id from lib_group", "id", "group_name"  );
                            $arr=array (1=>$group_name);
                            echo  create_list_view ( "list_view", "Company Name,Group Name,Short Name,Contact Person,Email", "130,200,200,100","1000","220",0, "select company_name,group_id,company_short_name,contract_person,email,id from lib_company where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,group_id,0,0,0", $arr , "company_name,group_id,company_short_name,contract_person,email", "../cost_center/requires/company_details_controller", 'setFilterGrid("list_view",-1);' ) ;
                            ?>
                        </td>
                    </tr>
                </table>
            </fieldset>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	set_multiselect('cbo_business_nature','0','0','','0');
</script>
</html>
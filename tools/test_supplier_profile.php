<?php
session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
    header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;
//----------------------------------------------------------------------------------------------------------------
//echo load_html_head_contents("Supplier Info","../../",1 ,1 ,'',1 );
echo load_html_head_contents("Supplier Info", "../", 1, 1, $unicode, 1, '');
?>


<script>
    if ($('#index_page', window.parent.document).val() != 1)
        window.location.href = "../../logout.php";

    var permission = '<?php echo $permission; ?>';

    function fnc_supplier_info(operation)
    {
        var cbo_party_type = document.getElementById('cbo_party_type').value.split(',');
        if (form_validation('txt_supplier_name*txt_short_name*cbo_party_type*cbo_tag_company*cbo_tag_buyer', 'Supplier Name*Short Name*Party Type*Tag Company*Tag Buyer') == false)
        {
            return;
        } 
		else if ($.inArray('90', cbo_party_type) > -1 && form_validation('cbo_buyer', 'Link to Buyer') == false)
        {

            //alert("mmmmm");
            return;


        } 
		else // Save Here
        {
            eval(get_submitted_variables('txt_supplier_name*txt_short_name*txt_contact_person*txt_contact_no*txt_party_type_id*txt_desination*cbo_tag_company*cbo_country*txt_web_site*txt_email*txt_address_1st*txt_address_2nd*txt_address_3rd*txt_address_4th*cbo_buyer*cbo_status*txt_remark*txt_credit_limit_days*txt_credit_limit_amount*cbo_credit_limit_amount_curr*cbo_discount_method*cbo_security_deducted*cbo_vat_to_be_deducted*cbo_ait_to_be_deducted*cbo_individual*cbo_supplier_nature*txt_tag_buyer_id*update_id'));

            var data = "action=save_update_delete&operation=" + operation + get_submitted_data_string('txt_supplier_name*txt_short_name*txt_contact_person*txt_contact_no*txt_party_type_id*txt_desination*cbo_tag_company*cbo_country*txt_web_site*txt_email*txt_address_1st*txt_address_2nd*txt_address_3rd*txt_address_4th*cbo_buyer*cbo_status*txt_remark*txt_credit_limit_days*txt_credit_limit_amount*cbo_credit_limit_amount_curr*cbo_discount_method*cbo_security_deducted*cbo_vat_to_be_deducted*cbo_ait_to_be_deducted*cbo_individual*cbo_supplier_nature*txt_tag_buyer_id*update_id*supplier_hidden_id', '../');
            //alert(data);return;
            freeze_window(operation);
            http.open("POST", "requires/test_supplier_profile_controller.php", true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fnc_supplier_info_reponse;
        }
    }

    function fnc_supplier_info_reponse()
    {
        if (http.readyState == 4)
        {
            //alert(http.responseText)
            var reponse = trim(http.responseText).split('**');
            if (reponse[0] == 50)
            {
                alert(reponse[1]);
                release_freezing();
                return;
            }
            show_msg(trim(reponse[0]));
            show_list_view(reponse[1], 'show_supplier_list_view', 'list_view_div', 'requires/test_supplier_profile_controller', 'setFilterGrid("list_view",-1)');
            reset_form('supplierinfo_1', '', '');
            set_button_status(0, permission, 'fnc_supplier_info');
            release_freezing();
        }
    }
	
	//=====================================1=================
    function openmypage_party_type()
    {
        var party_type_id = $('#txt_party_type_id').val();
        var title = 'Party Type Name Selection Form';
        var page_link = 'requires/test_supplier_profile_controller.php?party_type_id=' + party_type_id + '&action=party_name_popup';
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0', '');
        emailwindow.onclose = function ()
        {
			
            var theform = this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
            var party_id = this.contentDoc.getElementById("hidden_party_id").value;	 //Access form field with id="emailfield"
            var party_name = this.contentDoc.getElementById("hidden_party_name").value;
            $('#txt_party_type_id').val(party_id);
            $('#cbo_party_type').val(party_name);
        }
    }

    function openmypage_tag_buyer()
    {
        var txt_tag_buyer_id = $('#txt_tag_buyer_id').val();
        var title = 'Buyer Name Selection Form';
        var page_link = 'requires/test_supplier_profile_controller.php?txt_tag_buyer_id=' + txt_tag_buyer_id + '&action=buyer_name_popup';
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0', '../');
        emailwindow.onclose = function ()
        {
            
            var theform = this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
            var buyer_id = this.contentDoc.getElementById("hidden_buyer_id").value;	 //Access form field with id="emailfield"
            var buyer_name = this.contentDoc.getElementById("hidden_buyer_name").value;
            $('#txt_tag_buyer_id').val(buyer_id);
            $('#cbo_tag_buyer').val(buyer_name);
        }
    }

</script>
</head>	
<body onLoad="set_hotkey()">

    <div align="center" style="width:100%;">
        <?php echo load_freeze_divs("../", $permission); ?>
        <fieldset style="width:1010px;">
            <legend>Supplier Info</legend>

            <form name="supplierinfo_1" id="supplierinfo_1" autocomplete="off">	
                <table cellpadding="0" cellspacing="2" border="0" width="100%">
                    <tr>
                        <td width="130" class="must_entry_caption">Supplier Name  </td>
                        <td width="180">
                            <input type="text" name="txt_supplier_name" id="txt_supplier_name" class="text_boxes" style="width:180px" maxlength="100" title="Maximum 100 Character" />						<input type="hidden" name="supplier_hidden_id" id="supplier_hidden_id" class="text_boxes" /> 
                        </td>
                        <td width="130" class="must_entry_caption">
                            Short Name 
                        </td>
                        <td width="180">
                            <input type="text" name="txt_short_name" id="txt_short_name" class="text_boxes" style="width:180px" maxlength="10" title="Maximum 10 Character"/>						
                        </td>
                        <td>
                            Contact Person
                        </td>
                        <td>
                            <input type="text" name="txt_contact_person" id="txt_contact_person" class="text_boxes" style="width:180px"  maxlength="100" title="Maximum 100 Character"/>						
                        </td>
                    </tr>			
                    <tr>
                        <td width="130">
                            Designation
                        </td>
                        <td width="180">
                            <input type="text" name="txt_desination" id="txt_desination" class="text_boxes" style="width:180px" maxlength="50" title="Maximum 50 Character"/>						
                        </td>

                        <td>
                            Contact No
                        </td>
                        <td>
                            <input type="text" name="txt_contact_no" id="txt_contact_no" class="text_boxes" style="width:180px" maxlength="20" title="Maximum 20 Character"/>						
                        </td>
                        <td>
                            Email
                        </td>
                        <td>
                            <input type="text" name="txt_email" id="txt_email" class="text_boxes" style="width:180px;" maxlength="100" title="Maximum 100 Character"/>						
                        </td>

                    </tr>
                    <tr>
                        <td>
                            http://www.
                        </td>
                        <td>
                            <input type="text" name="txt_web_site" id="txt_web_site" class="text_boxes" style="width:180px" maxlength="30" title="Maximum 30 Character"/>						
                        </td>
                        <td>
                            Address1
                        </td>
                        <td>
                            <textarea name="txt_address_1st" id="txt_address_1st" class="text_area" style="width:180px;" maxlength="500" title="Maximum 500 Character"></textarea>
                        </td>
                        <td>
                            Address2
                        </td>
                        <td>
                            <textarea name="txt_address_2nd" id="txt_address_2nd" class="text_area" style="width:180px;" maxlength="500" title="Maximum 500 Character"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Address3
                        </td>
                        <td>
                            <textarea name="txt_address_3rd" id="txt_address_3rd" class="text_area" style="width:180px;" maxlength="500" title="Maximum 500 Character"></textarea>

                        </td>
                        <td>
                            Address4
                        </td>
                        <td>
                            <textarea name="txt_address_4th" id="txt_address_4th" class="text_area" style="width:180px;" maxlength="500" title="Maximum 500 Character"></textarea>

                        </td>
                        <td>
                            Country
                        </td>
                        <td>
                            <?php echo create_drop_down("cbo_country", 190, "select id,country_name from   lib_country where is_deleted=0 and status_active=1 order by country_name", "id,country_name", 1, "-- Select Country --", $selected_index, $onchange_func, $onchange_func_param_db, $onchange_func_param_sttc); ?>
                        </td>
                    </tr> 
                    <tr>
                        <td class="must_entry_caption">
                            Party Type
                        </td>
                        <td>
                            <?php
                            // echo create_drop_down( "cbo_party_type", 190, $party_type_supplier, "", 0, "", '', 'set_value_supplier_nature(this.value)', $onchange_func_param_db,$onchange_func_param_sttc  ); 
                            ?>
                            <input type="text" name="cbo_party_type" id="cbo_party_type" class="text_boxes" style="width:180px;" placeholder="Double Click To Search" onDblClick="openmypage_party_type();" readonly />
                            <input type="hidden" name="txt_party_type_id" id="txt_party_type_id" />				
                        </td>
                        <td class="must_entry_caption">
                            Tag Company
                        </td>
                        <td>
                            <?php
                            echo create_drop_down("cbo_tag_company", 190, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name", "id,company_name", 0, "", '', '');
                            ?>				
                        </td>
                        <td>
                            Link to Buyer
                        </td>
                        <td >
                            <?php
                            echo create_drop_down("cbo_buyer", 190, "select id,buyer_name from  lib_buyer where is_deleted=0 and status_active=1 order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected_index, $onchange_func, $onchange_func_param_db, $onchange_func_param_sttc);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Credit Limit (Days)
                        </td>
                        <td>
                            <input type="text" name="txt_credit_limit_days" id="txt_credit_limit_days" class="text_boxes_numeric" style="width:180px" />						
                        </td>
                        <td>
                            Credit Limit (Amount)
                        </td>
                        <td>
                            <input type="text" name="txt_credit_limit_amount" id="txt_credit_limit_amount" class="text_boxes_numeric" style="width:100px" />						

                            <?php
                            echo create_drop_down("cbo_credit_limit_amount_curr", 75, $currency, "", 0, "", '', '');
                            ?>				
                        </td>
                        <td>
                            Discount Method
                        </td>
                        <td >
                            <?php
                            echo create_drop_down("cbo_discount_method", 190, $currency, "", 1, "-- Select Method --", $selected_index, $onchange_func, $onchange_func_param_db, $onchange_func_param_sttc);
                            ?>
                        </td>
                    </tr>
                    <tr>
                    <tr>
                        <td>
                            Security deducted
                        </td>
                        <td>
                            <?php
                            echo create_drop_down("cbo_security_deducted", 190, $yes_no, "", 0, "", "", "", "", "");
                            ?>					
                        </td>
                        <td>
                            VAT to be deducted
                        </td>
                        <td>
                            <?php
                            echo create_drop_down("cbo_vat_to_be_deducted", 190, $yes_no, "", 0, "", "", "", "", "");
                            ?>				
                        </td>
                        <td>
                            AIT to be deducted
                        </td>
                        <td >
                            <?php
                            echo create_drop_down("cbo_ait_to_be_deducted", 190, $yes_no, "", 0, "", "", "", "", "");
                            ?>
                        </td>
                    </tr>

                    <tr>

                        <td>
                            Remark
                        </td>
                        <td colspan="3">
                            <input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width:503px" maxlength="500" title="Maximum 500 Character"999/>
                        </td>
                        <td>
                            Individual
                        </td>
                        <td>

                            <?php
                            echo create_drop_down("cbo_individual", 190, $yes_no, "", 0, "", "", "", "", "");
                            ?>

                        </td>
                    </tr>	
                    <tr>
                        <td>
                            Supplier Nature
                        </td>
                        <td >

                            <?php
                            echo create_drop_down("cbo_supplier_nature", 190, $supplier_nature, '', $is_select, $select_text, 1, $onchange_func, "", '', '');
                            ?>

                        </td>
                        <td>
                            Status
                        </td>
                        <td >

                            <?php
                            echo create_drop_down("cbo_status", 190, $row_status, '', $is_select, $select_text, 1, $onchange_func, "", '', '');
                            ?>

                        </td>
                        <td>
                            Image
                        </td>
                        <td height="25" valign="middle">
                            <input type="button" class="image_uploader" style="width:192px" value="CLICK TO ADD/VIEW IMAGE" onClick="file_uploader('../', document.getElementById('update_id').value, '', 'supplier_info', 0, 1)">
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">
                            Tag  Buyer
                        </td>
                        <td>

                            <input type="text" name="cbo_tag_buyer" id="cbo_tag_buyer" class="text_boxes" style="width:180px;" placeholder="Double Click To Search" onDblClick="openmypage_tag_buyer();" readonly />
                            <input type="hidden" name="txt_tag_buyer_id" id="txt_tag_buyer_id" />				
                        </td>

                    </tr>		  
                    <tr>
                        <td colspan="6" align="center" height="20" valign="middle" > 
                            <input type="hidden" name="update_id" id="update_id" />  
                        </td>					
                    </tr>	 

                    <tr>
                        <td colspan="6" align="center" height="40" valign="middle" class="button_container"> 
                            <?php
                            echo load_submit_buttons($permission, "fnc_supplier_info", 0, 0, "reset_form('supplierinfo_1','','')", 1);
                            ?> 
                        </td>					
                    </tr>				
                </table>
            </form>
        </fieldset>	

        <div style="width:100%; float:left; margin:auto" align="center" id="search_container">
            <fieldset style="width:600px; margin-top:10px">
                <table width="1010" cellspacing="2" cellpadding="0" border="0">

                    <tr>
                        <td colspan="3">
                            <div id="list_view_div">
                                <?php
                                $arr = array(7 => $currency, 8 => $row_status);
                                echo create_list_view("list_view", "Supplier Name,Short Name,Party Type,Contact Person,Designation,Credit Limit(Days),Credit Limit (Amount),Currency, Status", "150,100,150,100,120,100,100,70", "1010", "220", 0, "select supplier_name,short_name,party_type,contact_person,designation,credit_limit_days,credit_limit_amount,credit_limit_amount_currency,status_active,id from lib_supplier where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,0,0,0,0,0,0,credit_limit_amount_currency,status_active", $arr, "supplier_name,short_name,party_type,contact_person,designation,credit_limit_days,credit_limit_amount,credit_limit_amount_currency,status_active", "requires/test_supplier_profile_controller", 'setFilterGrid("list_view",-1);', '0,0,0,0,0,1,1,0,0');
                                ?>
                            </div>
                        </td>
                    </tr>
                </table>
            </fieldset>	
        </div>
    </div>
</body>

<script>
    set_multiselect('cbo_tag_company', '0', '0', '', '__set_buyer_status__../requires/test_supplier_profile_controller');
</script>

<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
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

    var permission = '<? echo $permission; ?>';


//    function fnc_supplier_info(operation)
//    {
//        var cbo_party_type = document.getElementById('cbo_party_type').value.split(',');
//        if (form_validation('txt_supplier_name*txt_short_name*cbo_party_type*cbo_tag_company*cbo_tag_buyer', 'Supplier Name*Short Name*Party Type*Tag Company*Tag Buyer') == false)
//        {
//            return;
//        } else if ($.inArray('90', cbo_party_type) > -1 && form_validation('cbo_buyer', 'Link to Buyer') == false)
//        {
//
//            //alert("mmmmm");
//            return;
//
//
//        } else // Save Here
//        {
//            eval(get_submitted_variables('txt_supplier_name*txt_short_name*txt_contact_person*txt_contact_no*txt_party_type_id*txt_desination*cbo_tag_company*cbo_country*txt_web_site*txt_email*txt_address_1st*txt_address_2nd*txt_address_3rd*txt_address_4th*cbo_buyer*cbo_status*txt_remark*txt_credit_limit_days*txt_credit_limit_amount*cbo_credit_limit_amount_curr*cbo_discount_method*cbo_security_deducted*cbo_vat_to_be_deducted*cbo_ait_to_be_deducted*cbo_individual*cbo_supplier_nature*txt_tag_buyer_id*update_id'));
//
//            var data = "action=save_update_delete&operation=" + operation + get_submitted_data_string('txt_supplier_name*txt_short_name*txt_contact_person*txt_contact_no*txt_party_type_id*txt_desination*cbo_tag_company*cbo_country*txt_web_site*txt_email*txt_address_1st*txt_address_2nd*txt_address_3rd*txt_address_4th*cbo_buyer*cbo_status*txt_remark*txt_credit_limit_days*txt_credit_limit_amount*cbo_credit_limit_amount_curr*cbo_discount_method*cbo_security_deducted*cbo_vat_to_be_deducted*cbo_ait_to_be_deducted*cbo_individual*cbo_supplier_nature*txt_tag_buyer_id*update_id*supplier_hidden_id', '../../');
//            //alert(data);return;
//            freeze_window(operation);
//            http.open("POST", "requires/supplier_info_controller.php", true);
//            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
//            http.send(data);
//            http.onreadystatechange = fnc_supplier_info_reponse;
//        }
//    }
//
//    function fnc_supplier_info_reponse()
//    {
//        if (http.readyState == 4)
//        {
//            //alert(http.responseText)
//            var reponse = trim(http.responseText).split('**');
//            if (reponse[0] == 50)
//            {
//                alert(reponse[1]);
//                release_freezing();
//                return;
//            }
//            show_msg(trim(reponse[0]));
//            show_list_view(reponse[1], 'show_supplier_list_view', 'list_view_div', '../contact_details/requires/supplier_info_controller', 'setFilterGrid("list_view",-1)');
//            reset_form('supplierinfo_1', '', '');
//            set_button_status(0, permission, 'fnc_supplier_info');
//            release_freezing();
//        }
//    }
//    function openmypage_party_type()
//    {
//        var party_type_id = $('#txt_party_type_id').val();
//        var title = 'Party Type Name Selection Form';
//        var page_link = 'requires/supplier_info_controller.php?party_type_id=' + party_type_id + '&action=party_name_popup';
//        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0', '../');
//        emailwindow.onclose = function ()
//        {
//            var theform = this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
//            var party_id = this.contentDoc.getElementById("hidden_party_id").value;	 //Access form field with id="emailfield"
//            var party_name = this.contentDoc.getElementById("hidden_party_name").value;
//            $('#txt_party_type_id').val(party_id);
//            $('#cbo_party_type').val(party_name);
//        }
//    }
//
//    function openmypage_tag_buyer()
//    {
//        var txt_tag_buyer_id = $('#txt_tag_buyer_id').val();
//        var title = 'Buyer Name Selection Form';
//        var page_link = 'requires/supplier_info_controller.php?txt_tag_buyer_id=' + txt_tag_buyer_id + '&action=buyer_name_popup';
//        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0', '../');
//        emailwindow.onclose = function ()
//        {
//            var theform = this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
//            var buyer_id = this.contentDoc.getElementById("hidden_buyer_id").value;	 //Access form field with id="emailfield"
//            var buyer_name = this.contentDoc.getElementById("hidden_buyer_name").value;
//            $('#txt_tag_buyer_id').val(buyer_id);
//            $('#cbo_tag_buyer').val(buyer_name);
//        }
//    }

</script>


</head>	
<body onLoad="set_hotkey()">

    <div align="center" style="width:100%;">
        <? echo load_freeze_divs("../../", $permission); ?>
        <fieldset style="width:500px;">
            <legend>Test Information</legend>

            <form name="TestInformation_1" id="TestInformation_1" autocomplete="off">	
                <table cellpadding="0" cellspacing="2" border="0" width="100%">
                    <tr>
                        <td width="130" class="">Test Name</td>
                        <td width="180">
                            <input type="text" name="txt_test_name" id="txt_test_name" class="text_boxes" style="width:180px" maxlength="100" title="Maximum 100 Character" />						<input type="hidden" name="supplier_hidden_id" id="supplier_hidden_id" class="text_boxes" /> 
                        </td>
                    </tr>			


                    <tr>
                        <td>
                            Country
                        </td>
                        <td>
                            <? echo create_drop_down("cbo_country", 190, "select id,country_name from   lib_country where is_deleted=0 and status_active=1 order by country_name", "id,country_name", 1, "-- Select Country --", $selected_index, $onchange_func, $onchange_func_param_db, $onchange_func_param_sttc); ?>
                        </td>
                    </tr> 
                    <tr>

                        <td class="">
                            Tag Company
                        </td>
                        <td>
                            <?
                            echo create_drop_down("cbo_tag_company", 190, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name", "id,company_name", 0, "", '', '');
                            ?>				
                        </td>
                    </tr>

                    <tr>
                        <td>
                            Status
                        </td>
                        <td >
                            <?
                            echo create_drop_down("cbo_status", 190, $row_status, '', $is_select, $select_text, 1, $onchange_func, "", '', '');
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            Party Type
                        </td>
                        <td>
                            <input type="text" name="cbo_party_type" id="cbo_party_type" class="text_boxes" style="width:180px;" placeholder="Double Click To Search" onDblClick="openmypage_party_type();" readonly />
                            <input type="hidden" name="txt_party_type_id" id="txt_party_type_id" />				
                        </td>
                    </tr>

                    <tr>
                        <td colspan="6" align="center" height="20" valign="middle" > 
                            <input type="text" name="update_id" id="update_id" />  
                        </td>					
                    </tr>	 

                    <tr>
                        <td colspan="6" align="center" height="40" valign="middle" class="button_container"> 
                            <?
                            echo load_submit_buttons($permission, "fnc_supplier_info", 0, 0, "reset_form('supplierinfo_1','','')", 1);
                            ?> 
                        </td>					
                    </tr>				
                </table>
            </form>
        </fieldset>	

        <div style="width:100%; float:left; margin:auto" align="center" id="search_container">
            <fieldset style="width:600px; margin-top:10px">
                <table width="720" cellspacing="2" cellpadding="0" border="0">

                    <tr>
                        <td colspan="3">
                            <div id="list_view_div">
                                <?
//                                $arr = array(7 => $currency, 8 => $row_status);
                                echo  create_list_view ( "list_view", "Test Name,Country,Party Type,Status", "200,100,200,","700","220",0, "select supplier_name,party_type,status_active from lib_supplier where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,0,0,status_active", $arr , "supplier_name,country,party_type,status_active", "../contact_details/requires/supplier_info_controller", 'setFilterGrid("list_view",-1);') ;
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
    set_multiselect('cbo_tag_company', '0', '0', '', '__set_buyer_status__../contact_details/requires/supplier_info_controller');
</script>

<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
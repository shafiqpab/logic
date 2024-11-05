<?
/*-------------------------------------------- Comments
Purpose         :   This form will Receive Scrap Material Items  
                
Functionality   :   
JS Functions    :
Created by      :   Mohammad Shafiqur Rahman 
Creation date   :   04-12-2019
Updated by      :   Md. Rakib   
Update date     :   13-07-2020     
QC Performed BY :       
QC Date         :   
Comments        :
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//========== user credential start ========
$user_id = $_SESSION['logic_erp']['user_id'];
//echo "select unit_id as company_id,item_cate_id,company_location_id,store_location_id from user_passwd where id=$user_id";
$userCredential = sql_select("select unit_id as company_id,item_cate_id,company_location_id,store_location_id from user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$location_credential_id = $userCredential[0][csf('company_location_id')];
$store_credential_id = $userCredential[0][csf('store_location_id')];
$category_credential_id = $userCredential[0][csf('item_cate_id')];


if ($company_id !='') {
    $company_credential_cond = "and comp.id in($company_id)";
}
/* if ($location_credential_id !='') {
    $location_credential_cond = "and comp.id in($company_id)";
}
if ($store_credential_id !='') {
    $store_credential_cond = "and comp.id in($company_id)";
} */
if ($category_credential_id !='') {
    $category_credential_cond = "and CATEGORY_ID in($category_credential_id)";
}
//========== user credential end ==========
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Scrap Material Receive Info","../../", 1, 1, $unicode,1,1); 
?>
<script>
    var permission='<? echo $permission; ?>';
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

        
    function popup_description()
    {
        if( form_validation('cbo_category_id*cbo_company_name*cbo_receive_basis*cbo_store','Item Category*Company Name*Receive Basis*Store Name')==false )
        {
            return;
        }
        var company_id = $("#cbo_company_name").val();
        var cbo_from_store_name = $("#cbo_from_store").val();
        var cbo_store = $("#cbo_store").val();
        //alert(cbo_from_store_name);
        var cbo_category_id = $("#cbo_category_id").val();
        var cbo_receive_basis = $("#cbo_receive_basis").val();

        if (cbo_receive_basis == 4)
        {
            var page_link="requires/scrap_material_receive_controller.php?action=item_description_independent_popup&company_id="+company_id+"&cbo_from_store_name="+cbo_from_store_name+'&cbo_category_id='+cbo_category_id+'&cbo_receive_basis='+cbo_receive_basis;
            var title="Item Description Popup";
            emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1130px,height=350px,center=1,resize=1,scrolling=0','../../')
            emailwindow.onclose=function()
            {
                var theform=this.contentDoc.forms[0]; 
                var item_description_all=this.contentDoc.getElementById("hidden_prod_description").value;
                //alert(item_description_all); 
                var splitArr = item_description_all.split("__");
                //alert(splitArr[8]);
                $("#txt_item_desc").val(splitArr[0]); 
                $("#txt_lot").val(splitArr[1]);
                $("#cbo_uom").val(splitArr[2]);
                $("#txt_color").val(splitArr[3]);
                $("#cbo_item_group").val(splitArr[4]);
                $("#hidden_pord_id").val(splitArr[5]);
                $("#txt_color_id").val(splitArr[6]);
                $("#hidd_item_group_id").val(splitArr[7]);
                $("#hdn_receive_qty").val(splitArr[8]);
                $("#txt_receive_qty").val(splitArr[8]);
                $("#txt_trans_id").val(splitArr[9]);
                $("#txt_system_challan_no").val(splitArr[10]);
                $("#txt_mst_id").val(splitArr[11]);
                $("#txt_gsm").val(splitArr[12]);
                $("#dia_width").val(splitArr[13]);
                $("#body_part").val(splitArr[14]);
                $("#cbo_from_store").val(splitArr[15]);
                $("#hidden_store_from_id").val(splitArr[15]);
                $("#txt_rate").val(splitArr[16]);
                $("#txt_trans_ref").val(splitArr[17]);

                var receive_qty = splitArr[8];
                var rate = splitArr[16];
                var txt_exchange_rate=$('#txt_exchange_rate').val()*1;
                var amount = receive_qty*rate;
                var bookCurrency = amount*txt_exchange_rate;
                $("#txt_amount").val(number_format_common(amount,"","",1));
                $("#txt_book_currency").val(number_format_common(bookCurrency,"","",1));

                $("#cbo_store").attr('disabled',true);
                $("#cbo_uom").attr('disabled',true);
                $("#txt_lot").attr('disabled',true);
                $("#txt_color").attr('disabled',true);
                $("#cbo_item_group").attr('disabled',true);
                $("#cbo_receive_basis").attr('disabled',true);
                $("#cbo_from_store").attr('disabled',true);
                $("#txt_trans_ref").removeAttr('readonly');
            }
        }
        else
        {
            var page_link="requires/scrap_material_receive_controller.php?action=item_description_popup&company_id="+company_id+"&cbo_from_store_name="+cbo_from_store_name+'&cbo_category_id='+cbo_category_id+'&cbo_receive_basis='+cbo_receive_basis+'&cbo_store='+cbo_store;
            var title="Item Description Popup";
            emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1130px,height=350px,center=1,resize=1,scrolling=0','../../')
            emailwindow.onclose=function()
            {
                var theform=this.contentDoc.forms[0]; 
                var item_description_all=this.contentDoc.getElementById("hidden_prod_description").value;
                //alert(item_description_all); 
                var splitArr = item_description_all.split("__");
                //document.getElementById('txt_item_desc').value	 = splitArr[4];
                //document.getElementById('hidden_pord_id').value	 = splitArr[2];
                get_php_form_data(splitArr[1]+"**"+splitArr[2]+"**"+cbo_receive_basis+"**"+splitArr[3], "populate_data_from_product_list_view", "requires/scrap_material_receive_controller" );
                show_list_view(company_id+"**"+cbo_category_id+"**"+splitArr[0]+"**"+cbo_receive_basis+"**"+splitArr[3],'show_product_listview','list_product_container','requires/scrap_material_receive_controller','');
            }
        }
       
    }

    function check_exchange_rate()
    {
        var cbo_currercy=$('#cbo_currency').val();
        var receive_date = $('#txt_receive_date').val();
        var response=return_global_ajax_value( cbo_currercy+"**"+receive_date, 'check_conversion_rate', '', 'requires/scrap_material_receive_controller');
        var response=response.split("_");
        $('#txt_exchange_rate').val(response[1]);
        $('#txt_exchange_rate').attr('disabled',true);
    }

    function fnc_scrap_material_receive_entry(operation)
    {
        if(operation==4)
        {
            var report_title=$( "div.form_caption" ).html();
            freeze_window(operation);
            print_report( $('#cbo_company_name').val()+'*'+$('#cbo_location').val()+'*'+$('#txt_system_id').val()+'*'+$('#txt_system_no').val()+'*'+report_title, 'scrap_material_receive_print', 'requires/scrap_material_receive_controller');
            release_freezing();
            return;
        }
        /* if( operation==2){
            alert("Sorry!! Can not delete anything. You should update if you need.");
            return;
        }*/
        if(operation==0 || operation==1 || operation==2)
        {
            if( form_validation('cbo_company_name*txt_item_desc*cbo_category_id*cbo_store*txt_receive_date*txt_receive_qty','Company Name*Item Description*Item Category*Store Name*Receive Date*Receive Qnty')==false )
            {
                return;
            }
            var current_date='<? echo date("d-m-Y"); ?>';
            if(date_compare($('#txt_receive_date').val(), current_date)==false)
            {
                alert("Receive Date Can not Be Greater Than Current Date");
                return;
            }   
            
            var dataString = "txt_system_no*txt_system_id*txt_trans_id*txt_system_challan_no*txt_mst_id*cbo_company_name*cbo_category_id*cbo_location*txt_receive_date*cbo_store*cbo_from_store*cbo_receive_basis*cbo_currency*txt_exchange_rate*txt_item_desc*hidd_item_group_id*body_part*hidd_body_part_id*txt_receive_qty*hdn_receive_qty*cbo_uom*txt_rate*txt_amount*txt_gsm*txt_book_currency*hidden_pord_id*hidden_store_from_id*dia_width*txt_color_id*txt_lot*txt_no_of_bags*txt_remarks*txt_remarks_mst*update_id*update_dtls_id*txt_item_size*txt_count*txt_trans_ref*cbo_material_placement*cbo_buyer*cbo_supplier*cbo_folor_name*cbo_division_name*cbo_department_name*cbo_section_name";
            var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");
            //alert(data);
            freeze_window(operation);
            http.open("POST","requires/scrap_material_receive_controller.php",true);                
            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fnc_scrap_material_receive_entry_reponse;
        }
    }

    function fnc_scrap_material_receive_entry_reponse()
    {   
        if(http.readyState == 4) 
        {
            //alert(http.responseText);release_freezing(); return;          
            var reponse=trim(http.responseText).split('**');
            //show_msg(reponse[0]); 
            
            if(reponse[0]*1==20*1)
            {
                alert(reponse[1]);
                release_freezing(); return;
            }
            else if(reponse[0]==10)
            {
                show_msg(reponse[0]);
                release_freezing(); return;
            }
            else if(reponse[0]==17)
            {
                alert(reponse[1]);
                release_freezing(); return;
            }
            else if(reponse[0]==50)
            {
                alert("Serial No. Not Over Issue Qnty");
                return;
            } 
            show_msg(reponse[0]);
            if(reponse[0]==0 || reponse[0]==1)
            {
                
                $("#txt_system_no").val(reponse[1]);
                $("#txt_system_id").val(reponse[2]);
                $("#update_id").val(reponse[2]);
                $("#update_dtls_id").val(reponse[3]);
                disable_enable_fields( 'cbo_company_name*cbo_category_id*cbo_receive_basis', 1, "", "" );
                //$("#tbl_master :input").attr("disabled", true);   
            }
            else if(reponse[0]==2)
            {
                location.reload();
                show_msg(reponse[0]);
                // //disable_enable_fields( 'cbo_company_name*cbo_issue_purpose*txt_issue_req_no', 1, "", "" ); 
                // //$("#tbl_master :input").attr("disabled", true);    
                // set_button_status(0, permission, 'fnc_scrap_material_receive_entry',1,1);
                 
            }
            var company = $("#cbo_company_name").val(); 
            var cbo_category_id = $("#cbo_category_id").val();
            var cbo_receive_basis = $("#cbo_receive_basis").val();
                        
            //$("#tbl_child").find('select,input').val(''); 
            disable_enable_fields( 'cbo_company_name*cbo_category_id', 1, "", "" );
            reset_form('','','txt_item_desc*txt_receive_qty*cbo_item_group*txt_rate*body_part*hidd_body_part_id*txt_amount*txt_gsm*cbo_uom*txt_book_currency*dia_width*txt_color*txt_lot*txt_no_of_bags*txt_remarks*txt_count*txt_trans_ref*cbo_material_placement*cbo_buyer*cbo_supplier','','','');   
            
            show_list_view(reponse[2]+'_'+company,'show_dtls_list_view','list_container','requires/scrap_material_receive_controller','');

            if(cbo_receive_basis == 4) {
                document.getElementById('txt_trans_ref').removeAttribute('readonly');
            } else {
                show_list_view(company+"**"+cbo_category_id+"**"+reponse[2]+"**"+cbo_receive_basis,'show_product_listview_aftersave','list_product_container','requires/scrap_material_receive_controller','');
            }
            $("#txt_receive_qty").removeAttr('disabled');
            set_button_status(0, permission, 'fnc_scrap_material_receive_entry',1,1);
            release_freezing();
        }
    }

    function open_mrrpopup()
    {
        if( form_validation('cbo_category_id*cbo_company_name','Company Name*Item Category')==false )
        {
            return;
        }
        var company = $("#cbo_company_name").val(); 
        var cbo_category_id = $("#cbo_category_id").val();  
        var page_link='requires/scrap_material_receive_controller.php?action=mrr_popup&company='+company+'&cbo_category_id='+cbo_category_id;
        var title="MRR Search Popup";
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=370px,center=1,resize=0,scrolling=0','../../')
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0]; 
            var sys_id=this.contentDoc.getElementById("hidden_system_id").value; // system number
            var sys_no=this.contentDoc.getElementById("hidden_system_no").value; // system number
            var cbo_receive_basis = $("#cbo_receive_basis").val();
            //alert(sys_id+"__"+sys_no);
            $("#txt_system_id").val(sys_id);
            $("#txt_system_no").val(sys_no);

            get_php_form_data(sys_id, "populate_scrap_master_form_data", "requires/scrap_material_receive_controller");
            show_list_view(sys_id+'_'+company,'show_dtls_list_view','list_container','requires/scrap_material_receive_controller','');
            set_button_status(0, permission, 'fnc_scrap_material_receive_entry',1,1);

            if(cbo_receive_basis == 4) {
                document.getElementById('txt_trans_ref').removeAttribute('readonly');
                $('#list_product_container').remove();
            }
        }
    }

    // calculate amount ---------------------------
    function amount_calculation(qnty)
    {
        var total_reject_qnty = $('#hdn_receive_qty').val()*1;
        var rcv_qnty=$('#txt_receive_qty').val()*1;
        var txt_rate=$('#txt_rate').val()*1;
        var txt_exchange_rate=$('#txt_exchange_rate').val()*1;
        var receive_basis=$('#cbo_receive_basis').val();
        //var item_group=$('#cbo_item_group').val();
        if( rcv_qnty > total_reject_qnty && receive_basis != 4)
        {
            alert("Receive Qntity is over Total Reject Qnty("+total_reject_qnty+")");
            $('#txt_receive_qty').val(0);
            $('#txt_receive_qty').focus();
            return;
        }
        var amount = rcv_qnty*txt_rate;
        var bookCurrency = amount*txt_exchange_rate;
        $("#txt_amount").val(number_format_common(amount,"","",1));

        $("#txt_book_currency").val(number_format_common(bookCurrency,"","",1));
    }

    function fn_order()  
    { 
        if(form_validation('cbo_company_name','Company')==false)
        {
            return;
        }  
        var company = $("#cbo_company_name").val() ;
        var title = 'PO Info';  
        var page_link = 'requires/scrap_material_receive_controller.php?company='+company+'&action=order_popup';
        
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=370px,center=1,resize=1,scrolling=0','../../');
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var po_string=this.contentDoc.getElementById("hidden_string").value;     
            var po_string_arr = po_string.split("_");
            $('#txt_order_id').val(po_string_arr[0]);
            $('#txt_buyer_order').val(po_string_arr[1]);
        }
    }
    
    //form reset/refresh function here
    function fnResetForm()
    {
       // $("#tbl_master").find('input').attr("disabled", false);   
        //disable_enable_fields( 'cbo_company_name*cbo_basis*cbo_receive_purpose*cbo_store_name', 0, "", "" );
       // $("#tbl_master").find('input,select').attr("disabled", false);
        set_button_status(0, permission, 'fnc_scrap_material_receive_entry',1,0);
        reset_form('scrap_material_receive_1','list_container','','','','cbo_currency*txt_exchange_rate');
    }

    function fnc_loan_party(val)
    {
        if(val==5)
        {
            $("#cbo_loan_party").attr("disabled",false);
        }
        else
        {
            $("#cbo_loan_party").val('');
            $("#cbo_loan_party").attr("disabled",true);
        }
    }

    function chk_issue_requisition_variabe(company)
    {
        var status = return_global_ajax_value(company, 'chk_issue_requisition_variabe', '', 'requires/scrap_material_receive_controller').trim();
        status = status.split("**");
        if(status[0] == 1)
        {
            //onDblClick="fnc_items_sys_popup()
            $("#txt_issue_req_no").prop('readonly',true);
            $("#txt_issue_req_no").attr('placeholder',"Browse").attr('onDblClick','fnc_items_sys_popup()');
        }
        else
        {
                $("#txt_issue_req_no").prop('readonly', false);
                $("#txt_issue_req_no").attr('placeholder',"write").removeAttr('onDblClick');
        }
    }

    function fnc_items_sys_popup()
    {
        var cbo_company_id=$('#cbo_company_name').val();
        if( form_validation('cbo_company_name','Company Name')==false )
        {
                return;
        }

        var page_link='requires/scrap_material_receive_controller.php?cbo_company_id='+cbo_company_id+'&action=item_issue_requisition_popup_search&item_category_id=11';
        var title='Issue Req. No'
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=400px,center=1,resize=1,scrolling=0','../../');

        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var hidden_item_issue_id=this.contentDoc.getElementById("hidden_item_issue_id").value;
            //var hidden_sys_id=this.contentDoc.getElementById("hidden_itemissue_req_sys_id").value;
            var data=hidden_item_issue_id.split("_");
            //alert(data[0]);
            if(trim(hidden_item_issue_id)!="")
            {
                //freeze_window(5);
                $('#hidden_issue_req_id').val(data[0]);
                $('#txt_issue_req_no').val(data[1]);
                $('#cbo_location').val(data[3]);
                $('#cbo_department').val(data[4]);
                load_drop_down( 'requires/scrap_material_receive_controller',data[4], 'load_drop_down_section', 'section_td' );
                $('#cbo_section').val(data[5]);
                //$('#hidden_indent_date').val(data[2]);
                $('#txt_item_desc').prop('disabled',true);
                show_list_view(data[0]+'__11','show_item_issue_listview','item_issue_listview','requires/scrap_material_receive_controller','../');
            }
            //release_freezing();
        }
    }

    function fnc_load_location(params) 
    {
        var cbo_company_name = document.getElementById('cbo_company_name').value;
        var item_category_id = document.getElementById('cbo_category_id').value;
        load_drop_down( 'requires/scrap_material_receive_controller', params+'_'+item_category_id+'_'+cbo_company_name, 'load_drop_down_location', 'location_td');
    }

    function fnc_load_store(params)
    {
        var cbo_company_name = document.getElementById('cbo_company_name').value;
        var cbo_location = document.getElementById('cbo_location').value;
        load_drop_down( 'requires/scrap_material_receive_controller', cbo_location+'_'+cbo_company_name+'_'+params, 'load_drop_down_store', 'store_td');
        load_drop_down( 'requires/scrap_material_receive_controller', params, 'load_drop_down_from_store', 'from_store_td');
    }

    function toggleCount(receiveBasis) {
        if(receiveBasis == 4) {
            document.getElementById('txt_count').removeAttribute('disabled');
            // document.getElementById('txt_receive_qty').removeAttribute('disabled');
        } else {
            document.getElementById('txt_count').setAttribute('disabled', true);
            // document.getElementById('txt_receive_qty').setAttribute('disabled', true);
        }
    }
</script>
</head>
<body onLoad="set_hotkey()">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",$permission);  ?><br />
        <form name="scrap_material_receive_1" id="scrap_material_receive_1" autocomplete="off" >         
            <div style="width:1000px; float:left; position:relative;" align="center">       
                <table width="80%" cellpadding="0" cellspacing="2">
                    <tr>
                        <td width="100%" align="center" valign="top">   
                            <fieldset style="width:1000px;">
                            <legend>Scrap Material Receive</legend>
                            <br />
                                <fieldset style="width:950px;">                                       
                                    <table  width="950" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                                        <tr>
                                            <td colspan="6" align="center"><b>System ID</b>
                                                <input type="text" name="txt_system_no" id="txt_system_no" class="text_boxes" style="width:160px" placeholder="Double Click To Search" onDblClick="open_mrrpopup()" readonly />&nbsp;&nbsp;
                                                <input type="hidden" id="txt_system_id" name="txt_system_id" value="" />
                                                
                                                <input type="hidden" id="txt_trans_id" name="txt_trans_id" value="" />
                                                <input type="hidden" id="txt_system_challan_no" name="txt_system_challan_no" value="" />
                                                <input type="hidden" id="txt_mst_id" name="txt_mst_id" value="" />
                                            </td>
                                        </tr>
                                        <tr>                                            
                                            <td  width="120" class="must_entry_caption">Company Name </td>
                                            <td width="170">
                                                <? 
                                                echo create_drop_down( "cbo_company_name", 170, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond $company_credential_cond order by company_name","id,company_name", 1, "-- Select Company --", 0, "fnc_load_location(this.value);load_drop_down('requires/scrap_material_receive_controller', document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_category_id').value, 'load_drop_down_supplier', 'td_supplier');load_drop_down('requires/scrap_material_receive_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_buyer', 'td_buyer');load_drop_down( 'requires/scrap_material_receive_controller', this.value, 'load_drop_down_division','division_td');" );
                                                ?>
                                            </td>
                                            <td width="120" class="must_entry_caption">Location</td>
                                            <td width="170" id="location_td">
                                                <? 
                                                    echo create_drop_down( "cbo_location", 170, $location_sql,"", 1, "-- Select Location --", $selected, "","" ); //fnc_load_store(this.value);
                                                ?>
                                            </td> 
                                            <td width="120" class="must_entry_caption">Item Category</td>
                                            <td width="170">
                                                <? 
                                                    $item_cate_array = return_library_array("select CATEGORY_ID,SHORT_NAME from lib_item_category_list where status_active=1 and is_deleted=0 $category_credential_cond  order by SHORT_NAME", "CATEGORY_ID", "SHORT_NAME");
                                                    echo create_drop_down( "cbo_category_id", 170, $item_cate_array,"",1, "-- Select Item --", $selected, "fnc_load_store(this.value);load_drop_down('requires/scrap_material_receive_controller', document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_supplier', 'td_supplier');", "", "");
                                                ?>
                                            </td>                                       
                                        </tr>
                                        <tr>                           
                                            <td width="120" class="must_entry_caption">Store Name</td>
                                            <td width="170" id="store_td">
                                                <? 
                                                    echo create_drop_down( "cbo_store", 170, $blank_array,"", 1, "-- Select Store --", $selected, "","" );
                                                ?>
                                            </td>
                                            <td width="120" align="" class="must_entry_caption">Receive Date</td>
                                            <td width="170">
                                                <input type="text" name="txt_receive_date" id="txt_receive_date" class="datepicker" style="width:160px;" onChange="check_exchange_rate();" placeholder="Select Date" value="<? echo date("d-m-Y");?>" />
                                            </td>
                                            <td width="120" align="" class="must_entry_caption">Receive Basis</td>
                                            <td width="170" id="receive_baisis_td">
                                                <?
                                                $receive_scrap_arra = array(1=>'Receive-Reject', 2=>'Issue-Damage', 3=>'Issue-Scrape Store', 4=>'Independent');
                                                echo create_drop_down( 'cbo_receive_basis', 170, $receive_scrap_arra, '', 1, '-- Select --', $selected, "toggleCount(this.value);", '');
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>                           
                                            <td width="" align="">Currency</td>
                                            <td  id="currency">
                                                <? 
                                                    $currencyID = 1;
                                                    echo create_drop_down( "cbo_currency", 170, $currency,"", 1, "-- Select Currency --", $currencyID, "check_exchange_rate();",1 );
                                                ?>
                                            </td>
                                            <td width="" align="">Exchange Rate</td>
                                            <td>
                                                <input type="text" name="txt_exchange_rate" id="txt_exchange_rate" class="text_boxes_numeric" style="width:160px"  readonly />
                                            </td>

                                            <td>From Store Name</td>
                                            <td id="from_store_td">
                                                <? 
                                                    echo create_drop_down( "cbo_from_store", 170, $blank_array,"", 1, "-- Select From Store --", $selected, "","" );
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>                                         
                                            <td> From Floor</td>
                                            <td  id="folor_td">
                                                <? 
                                                    echo create_drop_down( "cbo_folor_name", 170,$blank_array,"", 1, "-- Select --", $selected, "" );
                                                ?> 	
                                            </td>
                                            <td>From Division</td>
                                            <td id="division_td" >
                                            <? 
                                                echo create_drop_down( "cbo_division_name", 170,$blank_array,"", 1, "-- Select --", $selected, "" );
                                            ?> 	
                                            </td>

                                            <td>From Department</td>
                                            <td id="department_td">
                                            <? 
                                                echo create_drop_down( "cbo_department_name", 170,$blank_array,"", 1, "-- Select --", $selected, "" );
                                            ?> 	
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>From Section</td>
                                            <td  id="section_td">
                                                <? 
                                                    echo create_drop_down( "cbo_section_name", 170,$blank_array,"", 1, "-- Select --", $selected, "" );
                                                ?> 	
                                            </td>   
                                            <td>Transaction Buyer</td>
                                            <td>
                                                <input type="text" name="txt_transaction_byer" id="txt_transaction_byer" class="text_boxes" style="width: 86%;" readonly>
                                            </td>                                                                      
                                        </tr>
                                        <tr>
                                            <td>Remarks</td>
                                            <td colspan="3">
                                                <input type="text" name="txt_remarks_mst" id="txt_remarks_mst" class="text_boxes" style="width: 95%;">
                                            </td>                                                                      
                                        </tr>
                                    </table>
                                </fieldset>
                                <br />
                                
                                <!-- <input type="hidden" id="before_serial_id" name="before_serial_id" value=""/> -->
                                
                                <table cellpadding="0" cellspacing="1" width="96%" id="tbl_child">
                                <tr>
                                    <td width="49%" valign="top">
                                        <fieldset style="width:950px;">  
                                        <legend>New Receive Item</legend>                                     
                                            <table width="100%" cellspacing="2" cellpadding="0" border="0">
                                                <tr>                               
                                                    <td width="110" class="must_entry_caption">Item Description</td>
                                                    <td>
                                                        <input name="txt_item_desc" id="txt_item_desc" class="text_boxes" type="text" style="width:150px;" placeholder="Double Click" onDblClick="popup_description()" readonly />
                                                        <input name="hidden_pord_id" id="hidden_pord_id" class="text_boxes" type="hidden" readonly />
                                                    </td>
                                                    <td>Item Size</td>
                                                    <td>
                                                        <input name="txt_item_size" id="txt_item_size" class="text_boxes" type="text" style="width:90px;" />
                                                    </td>
                                                    <td class="must_entry_caption">Receive Qnty</td>
                                                    <td>
                                                        <input name="txt_receive_qty" id="txt_receive_qty" class="text_boxes_numeric" type="text" style="width:150px;" onKeyUp="amount_calculation(this.value)">
                                                        <input name="hdn_receive_qty" id="hdn_receive_qty" type="hidden">    
                                                    </td>
                                                    <td>Transaction Ref</td>
                                                    <td>
                                                        <input name="txt_trans_ref" id="txt_trans_ref" class="text_boxes" type="text" style="width:160px;" readonly="readonly" />
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Item Group</td>
                                                    <td id="item_group_td">
                                                        <input name="cbo_item_group" id="cbo_item_group" class="text_boxes" type="text" style="width:150px;" placeholder="Item Group" onDblClick="" readonly disabled="true"/>
                                                        <input name="hidd_item_group_id" id="hidd_item_group_id"  type="hidden" />
                                                        <!-- //popup_description(); -->
                                                        <?
                                                           // echo create_drop_down( "cbo_item_group", 160, "select id,item_name from lib_item_group where status_active=1 and is_deleted=0 order by item_name", "id,item_name", 1, "-- Select --", $selected, "", 1,"" );
                                                        ?>
                                                    </td>
                                                    <td>Int Ref</td>
                                                    <td>
                                                        <input name="txt_int_ref" id="txt_int_ref" class="text_boxes" type="text" style="width:90px;" readonly/>
                                                    </td>
                                                    <td >Rate</td>
                                                    <td>
                                                        <input type="text" name="txt_rate" id="txt_rate" class="text_boxes_numeric" style="width:150px" onKeyUp="amount_calculation(this.value);" >
                                                    </td>
                                                    <td>Metarial Placement</td>
                                                    <td>
                                                        <?php
                                                            $material_placement = array(1=>'Top Floor', 2=>'Bulding Side', 3=>'Old Store', 4=>'Tin Shade');
                                                            echo create_drop_down( 'cbo_material_placement', 170, $material_placement, '', 1, '-- Select --', $selected, 0, '');
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Body Part</td>
                                                    <td>
                                                        <input type="text" name="body_part" id="body_part" class="text_boxes" style="width:150px" readonly="" disabled="true">
                                                        <input name="hidd_body_part_id" id="hidd_body_part_id"  type="hidden" />
                                                    </td>
                                                    <td colspan="2"></td>
                                                    <td>Amount</td>
                                                    <td>
                                                        <input type="text" name="txt_amount" id="txt_amount" class="text_boxes_numeric" style="width:150px" readonly="" disabled="true">
                                                    </td>
                                                    <td>Buyer</td>
                                                    <td id="td_buyer">
                                                        <?php
                                                            echo create_drop_down( 'cbo_buyer', 170, $buyer, '', 1, '-- Select --', $selected, 0, '');
                                                        ?>
                                                    </td>
                                                </tr>
                                                
                                                <tr>
                                                    <td>GSM</td>
                                                    <td>
                                                        <input type="text" name="txt_gsm" id="txt_gsm" class="text_boxes" style="width: 150px;" readonly disabled="true">
                                                    </td>
                                                    <td>UOM</td>
                                                    <td id="uom_td">
                                                        <?
                                                            echo create_drop_down( "cbo_uom", 100, $unit_of_measurement,"", 1, "-- Select --", 0, "", 1 );
                                                        ?>
                                                    </td>
                                                    <td>Book Currency</td>
                                                    <td>
                                                        <input type="text" name="txt_book_currency" id="txt_book_currency" class="text_boxes_numeric" style="width:150px;" readonly="" disabled="true">                                            
                                                    </td>
                                                    <td>Supplier</td>
                                                    <td id="td_supplier">
                                                        <?php
                                                            echo create_drop_down( 'cbo_supplier', 170, $supplier, '', 1, '-- Select --', $selected, 0, '');
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Dia/Width</td>
                                                    <td>
                                                        <input type="text" name="txt_dia_width" id="dia_width" class="text_boxes" style="width:150px;" readonly disabled="true">
                                                        &nbsp;
                                                        <!-- <input type="text" name="txt_dia_width_type" id="dia_width_type" class="text_boxes" style="width:80px;" placeholder="Dia W. Type" readonly disabled="true" >                              -->
                                                    </td>
                                                    <td colspan="2"></td>
                                                    <td>Color</td>
                                                    <td>
                                                        <input type="text" name="txt_color" id="txt_color" class="text_boxes" style="width:150px;"  readonly disabled="true">                             
                                                        <input type="hidden" name="txt_color_id" id="txt_color_id" class="text_boxes" >                             
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Lot/Batch</td>
                                                    <td>
                                                        <input type="text" name="txt_lot" id="txt_lot" class="text_boxes" style="width:150px;" readonly disabled="true">
                                                    </td>
                                                    <td>Count</td>
                                                    <td id="uom_td">
                                                        <input name="txt_count" id="txt_count" class="text_boxes" type="text" style="width:90px;" disabled="true" />
                                                    </td>
                                                    <td>No of Bags</td>
                                                    <td>
                                                        <input type="text" name="txt_no_of_bags" id="txt_no_of_bags" class="text_boxes_numeric" style="width:150px;">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Remarks</td>
                                                    <td>
                                                        <input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:150px;">
                                                    </td>                                                
                                                </tr>                                          
                                            </table>
                                    </fieldset>
                                    </td>
                                </tr>
                            </table>                
                            <table cellpadding="0" cellspacing="1" width="100%">
                                <tr> 
                                <td colspan="6" align="center"></td>                
                                </tr>
                                <tr>
                                    <td align="center" colspan="6" valign="middle" class="button_container">
                                        <!-- details table id for update -->
                                        <input type="hidden" id="current_prod_id" name="current_prod_id" readonly/>
                                        <input type="hidden" id="hidden_store_from_id" name="hidden_store_from_id" readonly/> 
                                        <input type="hidden" id="update_id" name="update_id" readonly /> 
                                        <input type="hidden" id="update_dtls_id" name="update_dtls_id" readonly /> 
                                        <input type="hidden" id="hidden_transaction_ids" name="hidden_transaction_ids"/> 
                                        <!-- -->
                                        <? echo load_submit_buttons( $permission, "fnc_scrap_material_receive_entry", 0,1,"fnResetForm();",1);?>
                                    </td>
                            </tr> 
                            </table>                 
                            </fieldset>
                            <br>
                            <div id="list_container"></div>
                    </td>
                    </tr>
                </table>
            </div>
            <div id="list_product_container" style="max-height:500px; width:23%; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
        </form>
    </div>  
     
</body>  
<script>
    $(function(){
        //alert("body loaded");
        check_exchange_rate();
    });
    //$("#cbo_receive_purpose").val(0);
</script> 
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>

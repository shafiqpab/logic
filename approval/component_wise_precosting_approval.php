<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Component Wise Pre Costing Approval
Functionality	:	
JS Functions	:
Created by		:	MD. SAIDUL ISLAM REZA
Creation date 	: 	19/10/2022
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
//$permission = get_page_permission($_SERVER['REQUEST_URI'],$_SESSION['menu_id']);
$_SESSION['page_permission'] = $permission;
$menu_id = $_SESSION['menu_id'];
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Component Wise Pre Costing Approval", "../", 1, 1, '', '', '');

?>
<script>
    if ($('#index_page', window.parent.document).val() != 1) window.location.href = "../logout.php";
    var permission = '<? echo $permission; ?>';

    let fn_report_generated = () => {
        freeze_window(3);

        if (form_validation('cbo_company_name', 'Comapny Name') == false) {
            release_freezing();
            return;
        }

        var data = "action=report_generate" + get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_get_upto*txt_date*cbo_approval_type*txt_job_no*txt_alter_user_id', "../");

        http.open("POST", "requires/component_wise_precosting_approval_controller.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = () => {
            if (http.readyState == 4) {
                release_freezing();
                var response = trim(http.responseText).split("####");
                $('#report_container').html(response[0]);
                if(response[0]!=''){setFilterGrid("tbl_list_search", -1);}
            }

        }
    }



    let change_user = () => {
        if (form_validation('cbo_company_name', 'Comapny Name') == false) {
            return;
        }
        var title = 'User Info';
        var page_link = 'requires/component_wise_precosting_approval_controller.php?action=user_popup' + get_submitted_data_string('cbo_company_name', "../");
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=390px,center=1,resize=1,scrolling=0', '');
        emailwindow.onclose = function(){
            //var theform=this.contentDoc.forms[0];
            var data = this.contentDoc.getElementById("selected_id").value;
            var data_arr = data.split("_");
            $("#txt_alter_user_id").val(data_arr[0]);
            $("#txt_alter_user").val(data_arr[1]);
            $("#cbo_approval_type").val(0);
            $("#report_container").html('');
        }
    }

    let toggle_check=(job_id,component_str)=>{
        component_arr=component_str.split(',');
        var chekekStatus = document.getElementById("job_"+ job_id).checked;
        $.each(component_arr, function( index, value ) {
            document.getElementById("compo_"+ job_id+value).checked=chekekStatus;
            check_bg_color('compo_'+ job_id+value,'td_'+ job_id+value);
        });
    }

    let check_bg_color=(get_id_str,put_id_str)=>{
        var chekekStatus=document.getElementById(get_id_str).checked;
        if(chekekStatus==true){
            document.getElementById(put_id_str).style.backgroundColor = "#e8f8f5";
        }
        else{
            document.getElementById(put_id_str).style.backgroundColor = "white";
        }
    }


    let all_check=()=>{
        $('input:checkbox').prop('checked',document.getElementById("all_check").checked);
    }


    let submit_approved=(total_tr,type)=>{
        //freeze_window(3);
        var is_check=false;
        for (i = 1; i < total_tr; i++) {
            if ($('.compo_'+i).is(':checked')) {
                is_check=true;
            }
        } 
        if(is_check==false){
            if(type==2){var typeText='Approved';}
            else if(type==1){var typeText='Un-Approved';}
            else{var typeText='Deny';}
            alert('Please select component for '+typeText);
            release_freezing();
            return;
        }

        
        if (type == 5) {

            if ($("#all_check").is(":checked") && confirm("Are You Want to Deny All Job") == false) {
                release_freezing();
                return;
            }

            var deny_precost_id_arr=Array();
            for (i = 1; i < total_tr; i++) {
                if ($('.compo_'+i).is(':checked')) {
                    var comVal = $('.compo_'+i).val();
                    var comValArr=comVal.split(',');
                    deny_precost_id_arr.push(comValArr[1]);
                    if( !$('#tdCause_'+comValArr[1]+comValArr[3]).text() ){
                        alert("Please write deny causes the selected component.");
                        $('#tdCause_'+comValArr[1]+comValArr[3]).css('background-color', 'red');
                        release_freezing();
                        return;
                    }
                }
            }
           
            var deny_precost_id_str=deny_precost_id_arr.join(',');
            //alert(deny_precost_id_str);

            
        }
        else if ($('#cbo_approval_type').val() == 2) {
            if ($("#all_check").is(":checked") && confirm("Are You Want to Approved All Job") == false) {
                release_freezing();
                return;
            }

            for (i = 1; i < total_tr; i++) {
                if ($('.compo_'+i).is(':checked')) {
                    var comVal = $('.compo_'+i).val();
                    var comValArr=comVal.split(',');
                    if( $('#tdCause_'+comValArr[1]+comValArr[3]).text() ){
                        alert("Please delete deny causes the selected component. If you want to approve.");
                        $('#tdCause_'+comValArr[1]+comValArr[3]).css('background-color', 'red');
                        release_freezing();
                        return;
                    }
                }
            }
        } 
        else {    
            if ($("#all_check").is(":checked") && confirm("Are You Sure Want to UnApproved All Job") == false) {
                release_freezing();
                return;
            }
        }

       var app_data_arr=Array();
        for (i = 1; i < total_tr; i++) {
            if ($('.compo_'+i).is(':checked')) {
                app_data_arr.push($('.compo_'+i).val());
            }

        }
       var app_data_str = app_data_arr.join('__');

        var data = "action=approve&operation=" + operation + '&approval_type=' + type + '&app_data_str=' + app_data_str + get_submitted_data_string('cbo_company_name*txt_alter_user_id', "../");

        http.open("POST", "requires/component_wise_precosting_approval_controller.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = ()=>{
            if (http.readyState == 4) {
                
                var reponse = trim(http.responseText).split('**');
              
                if ((reponse[0] == 19 || reponse[0] == 20)) {
                   
                    show_msg(reponse[0]);
                    release_freezing();
                    fn_report_generated();
                }
                else if(reponse[0] == 37){
                    show_msg(reponse[0]);
                    release_freezing();
                    
                   // alert(deny_precost_id_str); 
                    var precost = return_ajax_request_value(deny_precost_id_str, 'deny_mail', 'requires/component_wise_precosting_approval_controller');
                    alert(precost);
                    fn_report_generated();
                   

                }
                else{
                    show_msg(reponse[0]); 
                    release_freezing();
                }
                
            }
        }
    }

 


    let openImgFile=(id, action)=> {
        var page_link = 'requires/component_wise_precosting_approval_controller.php?action=' + action + '&id=' + id;
        if (action == 'img') var title = 'Image View';
        else var title = 'File View';
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0', '');
    }



    let openPopup=(param, title, action)=> {
        var page_link = 'requires/component_wise_precosting_approval_controller.php?action=' + action + '&data=' + param;
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=370px,center=1,resize=1,scrolling=0', '');
    }

    let open_not_app_cause=(action,title,param)=> {
        var page_link = 'requires/component_wise_precosting_approval_controller.php?action=' + action + '&data=' + param+'**'+$("#txt_alter_user_id").val();
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=300px,center=1,resize=1,scrolling=0', '');

            emailwindow.onclose = function(){
            var paramArr = param.split('**');
            var data = this.contentDoc.getElementById("txt_refusing_cause").value;
            $('#tdCause_'+paramArr[0]+paramArr[1]).text(data);

        }


    }



    let generate_report=(type,job_no,company_id,buyer_id,style_ref,txt_costing_date,po_break_down_id,option_id)=>{    
        var zero_val='';
        var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
        if (r==true) {zero_val="1";}
        else{zero_val="0";} 
          var path="../" ;     
        var data="action="+type+"&txt_job_no="+"'"+job_no+"'"+"&cbo_company_name="+"'"+company_id+"'"+"&cbo_buyer_name="+"'"+buyer_id+"'"+"&txt_style_ref="+"'"+style_ref+"'"+"&txt_costing_date="+"'"+txt_costing_date+"'"+"&txt_po_breack_down_id="+"'"+po_break_down_id+"'"+"&print_option_id="+"'"+option_id+"'"+"&zero_value="+zero_val+"&path="+path;
        //alert(data);
        http.open("POST","../order/woven_order/requires/pre_cost_entry_controller_v2.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = ()=>{
            if(http.readyState == 4) 
            {
                $('#data_panel').html( http.responseText );
                var w = window.open("Surprise", "_blank");
                var d = w.document.open();
                d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
        '<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
                d.close();
            }
        }
        
    }


    let get_deny_cause_his=(pre_cost_id)=>{

        var page_link = 'requires/component_wise_precosting_approval_controller.php?action=deny_cause_his_dtls&pre_cost_id=' + pre_cost_id;
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Deny Cause History', 'width=550px,height=300px,center=1,resize=1,scrolling=0', '');
        emailwindow.onclose = function(){}
    }


    let fn_po_wise_comments = (job_id)=>{
        var page_link = 'requires/component_wise_precosting_approval_controller.php?action=po_wise_comments&job_id=' + job_id +'&alter_user_id=' + $("#txt_alter_user_id").val();
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Comments', 'width=550px,height=300px,center=1,resize=1,scrolling=0', '');
        emailwindow.onclose = function(){
            var po_id = this.contentDoc.getElementById("selected_po_id").value;
            const element = document.getElementById("bomRpt3"+job_id); 
            let getAtt = element.getAttribute("onclick"); 
                getAtt = getAtt.replace("generate_report", "call_print");
                
                function call_print(type,job_no,company_id,buyer_id,style_ref,txt_costing_date,po_break_down_id,option_id){
                    po_break_down_id = po_id;
                    generate_report(type,job_no,company_id,buyer_id,style_ref,txt_costing_date,po_break_down_id,option_id);
                }   

            eval(getAtt);
            
        }
    }
    
    //........................................................




</script>
</head>

<body>
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs("../", ''); ?>
        <form name="requisitionApproval_1" id="requisitionApproval_1">
            <h3 style="width:900px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
            <div id="content_search_panel">
                <fieldset style="width:900px;">
                    <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <tr>
                                <td colspan="7" align="right">
                                <?php 
                                $user_lavel=return_field_value("user_level","user_passwd","id=".$_SESSION['logic_erp']['user_id']."");
                                if( $user_lavel==2)
                                {
                                ?>
                                    Alter User:<input type="text" id="txt_alter_user" name="txt_alter_user" class="text_boxes" onDblClick="change_user()" placeholder="Browse " style="width:150px;" readonly>
                                <?php 
                                }
                                ?>
                                    <input type="hidden" id="txt_alter_user_id" name="txt_alter_user_id" />
                                </td>
                            </tr>
                            <tr>
                                <th class="must_entry_caption">Company Name</th>
                                <th>Buyer</th>
                                <th>Job No</th>
                                <th>Get Upto</th>
                                <th>Costing Date</th>
                                <th>Approval Type</th>
                                <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('requisitionApproval_1','report_container','','','')" class="formbutton" style="width:100px" /></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td>
                                    <?
                                    echo create_drop_down("cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name", "id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/component_wise_precosting_approval_controller',this.value, 'load_drop_down_buyer', 'buyer_td_id' );");
                                    ?>
                                </td>
                                <td id="buyer_td_id">
                                    <?
                                    echo create_drop_down("cbo_buyer_name", 152, $blank_array, "", 1, "-- All Buyer --", 0, "");
                                    ?>
                                </td>

                                <td><input placeholder="Job prefix no" type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:80px" /></td>


                                <td>
                                    <?
                                    $get_upto = array(1 => "After This Date", 2 => "As On Today", 3 => "This Date");
                                    echo create_drop_down("cbo_get_upto", 130, $get_upto, "", 1, "-- Select --", 0, "");
                                    ?>
                                </td>
                                <td><input type="text" name="txt_date" id="txt_date" class="datepicker" readonly style="width:80px" /></td>
                                <td>
                                    <?
                                    $pre_cost_approval_type = array(2 => "Un-Approved", 1 => "Approved");
                                    echo create_drop_down("cbo_approval_type", 130, $pre_cost_approval_type, "", 0, "", 2, "$('#report_container').html('')", "", "");
                                    ?>
                                    <input type="hidden" id="print_option" name="print_option" />
                                    <input type="hidden" id="print_option_no" name="print_option_no" />
                                    <input type="hidden" id="print_option_id" name="print_option_id" />
                                </td>
                                <td><input type="button" value="Show" name="show" id="show" class="formbutton" style="width:100px" onClick="fn_report_generated()" /></td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
        </form>
    </div>
    <div id="report_container" align="center"></div>
    <div style="display:none" id="data_panel"></div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script>
    //$('#cbo_approval_type').val(0);
</script>

</html>
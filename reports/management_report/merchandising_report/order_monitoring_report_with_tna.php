<?
/* -------------------------------------------- Comments -----------------------
  Purpose           :   This Form Will Create Order Monitoring Report With TNA.
  Functionality :
  JS Functions  :
  Created by        :   Shafiq
  Creation date     :   26-01-2020
  Updated by        :
  Update date       :
  QC Performed BY   :
  QC Date           :
  Comments          : Code is Poetry, I try to do that. :)
 */

session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
    header("location:login.php");
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;

//-------------------------------------------------------------------------------------------
echo load_html_head_contents("Work Progress Report", "../../../", 1, 1, $unicode, 1, 1);
?>  

<script>

    if ($('#index_page', window.parent.document).val() != 1)
        window.location.href = "../../../logout.php";
    var permission = '<? echo $permission; ?>';    

    function fn_report_generated(rptType)
    { 
        freeze_window(3);
        if (form_validation('cbo_company_name', 'Comapny Name') == false)//*txt_date_from*txt_date_to----*From Date*To Date
        {
            release_freezing();
            return;
        } 
        else
        {
            if(rptType==2)
            {
                if(form_validation('cbo_buyer_name*cbo_season_name', 'Buyer*Season') == false)
                {
                    release_freezing(); 
                    return;                   
                }
            }
            $("#report_container3").html('');
            $("#report_container4").html('');
            $("#report_container5").html('');
            $("#report_container6").html('');
            $("#report_container7").html('');

            //var width = window.innerWidth;
            //width = width-10;
            var screen_size = screen.width;

            var data = "action=report_generate&rptType="+rptType +'&screen_size='+screen_size+ get_submitted_data_string('cbo_company_name*cbo_location_name*cbo_buyer_name*cbo_season_name*txt_date_from*txt_date_to*cbo_emblishment*cbo_start_alarm*cbo_end_alarm*txt_style_ref*cbo_backlog*cbo_balance*txt_int_ref_no*cbo_search_by*cbo_merchant*cbo_client*cbo_order_status*cbo_item_name*cbo_status*cbo_shipment_status*hidden_order_id', "../../../");
            //alert(data);
            http.open("POST", "requires/order_monitoring_report_with_tna_controller.php", true);            
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fn_report_generated_reponse;
        }
    }

    function fn_circular_report_generated(rptType)
    { 
        freeze_window(3);
        if (form_validation('cbo_company_name', 'Comapny Name') == false)//*txt_date_from*txt_date_to----*From Date*To Date
        {
            release_freezing();
            return;
        } 
        else
        {
            if(rptType==6)
            {
                if(form_validation('cbo_buyer_name*cbo_season_name', 'Buyer*Season') == false)
                {
                    release_freezing();
                    return;                   
                }
            }
            else
            {
                var cbo_buyer_name = $("#cbo_buyer_name").val();
                var cbo_season_name = $("#cbo_season_name").val();
                var cbo_merchant = $("#cbo_merchant").val();
                var cbo_client = $("#cbo_client").val();
                var txt_int_ref_no = $("#txt_int_ref_no").val().trim();
                if(cbo_buyer_name ==0 && cbo_season_name ==0 && cbo_merchant==0 && cbo_client ==0 && txt_int_ref_no=="")
                {
                    if(form_validation('txt_date_from*txt_date_to', 'From Date*To Date') == false)
                    {
                        release_freezing();
                        return;                   
                    }
                }
            }



            $("#report_container3").html('');
            $("#report_container4").html('');
            $("#report_container5").html('');
            $("#report_container6").html('');
            $("#report_container7").html('');

            //var width = window.innerWidth;
            //width = width-10;
            var screen_size = screen.width;

            if(rptType==6)
            {
                var action = "report_generate_circular_purchased";
            }
            else
            {
                var action = "report_generate_circular";
            }

            var data = "action="+action+"&rptType="+rptType +'&screen_size='+screen_size+ get_submitted_data_string('cbo_company_name*cbo_location_name*cbo_buyer_name*cbo_season_name*txt_date_from*txt_date_to*cbo_emblishment*cbo_start_alarm*cbo_end_alarm*txt_style_ref*cbo_backlog*txt_int_ref_no*cbo_search_by*cbo_merchant*cbo_client*cbo_order_status*cbo_item_name*cbo_status*cbo_shipment_status', "../../../");
            //alert(data);
            http.open("POST", "requires/order_monitoring_report_with_tna_controller.php", true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fn_report_generated_reponse;
        }
    }

    function fn_rmg_report_generated(rptType)
    { 
        freeze_window(3);
        if (form_validation('cbo_company_name', 'Comapny Name') == false)//*txt_date_from*txt_date_to----*From Date*To Date
        {
            return;
            release_freezing();
        } 
        else
        {
            if(rptType==6)
            {
                if(form_validation('txt_date_from*txt_date_to', 'From Date*To Date') == false)
                {
                    return; 
                    release_freezing();                   
                }
            }
            $("#report_container3").html('');
            $("#report_container4").html('');
            $("#report_container5").html('');
            $("#report_container6").html('');
            $("#report_container7").html('');

            //var width = window.innerWidth;
            //width = width-10;
            var screen_size = screen.width;

            var data = "action=report_generate_rmg&rptType="+rptType +'&screen_size='+screen_size+ get_submitted_data_string('cbo_company_name*cbo_location_name*cbo_buyer_name*cbo_season_name*txt_date_from*txt_date_to*cbo_emblishment*cbo_start_alarm*cbo_end_alarm*txt_style_ref*cbo_backlog*cbo_balance*txt_int_ref_no*cbo_search_by*cbo_merchant*cbo_client*cbo_order_status*cbo_item_name*cbo_status*cbo_shipment_status', "../../../");
            //alert(data);
            http.open("POST", "requires/order_monitoring_report_with_tna_controller.php", true);            
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fn_report_generated_reponse;
        }
    }

    function fn_report_generated_reponse()
    {
        if (http.readyState == 4)
        {   
            release_freezing();
            var reponse = trim(http.responseText).split("####");
            $('#report_container2').html(reponse[0]);
            // document.getElementById('report_container').innerHTML = report_convert_button('../../../');
            document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>'+'&nbsp;&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
            //document.getElementById('content_search_panel').style='display:none';
            show_msg('3');
            release_freezing();
        }

    }

    function open_cost_report2_from_budget(company,job,buyer)
    {  
        var jobNo = "'" + job + "'";
        var data = "action=preCostRpt2&txt_job_no="+jobNo+"&cbo_buyer_name="+buyer+"&cbo_company_name="+company+"&zero_value=1&rate_amt=2&cbo_costing_per=1";
        // alert(data);
        freeze_window(3);
        // http.open("POST", "requires/order_monitoring_report_with_tna_controller.php", true);            
        http.open("POST","../../../order/woven_order/requires/pre_cost_entry_controller_v2.php",true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = open_cost_report2_from_budget_reponse;
        // release_freezing();
    }

    function open_cost_report2_from_budget_reponse()
    {
        if(http.readyState == 4)
        {
            $('#data_panel').html( http.responseText );
            var w = window.open("Surprise", "_blank");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
        '<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
            d.close();
            show_msg('3');
            release_freezing();
        }

    }
    function report_generate_by_buyer(buyer_id,client_id)
    {
        var cbo_company_name = $("#cbo_company_name").val();
        var cbo_location_name = $("#cbo_location_name").val();
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_monitoring_report_with_tna_controller.php?cbo_company_name=' + cbo_company_name+'&cbo_location_name=' + cbo_location_name+'&buyer_id=' + buyer_id+'&client_id=' + client_id+ '&action=report_generate_by_buyer', 'Buyer wise Details', 'width=1200px,height=450px,center=1,resize=0,scrolling=0', '../../');

        
        /*
        var report_title="Actual Shipment Plan - Buyer wise Details";
        var data="action=report_generate_by_buyer"+get_submitted_data_string('cbo_company_name*cbo_location_name',"../../../")+'&report_title='+report_title+'&buyer_id='+buyer_id;
        
        freeze_window(3);
        http.open("POST","requires/order_monitoring_report_with_tna_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = generate_report_by_buyer_reponse;*/ 
    }
    function generate_report_by_buyer_reponse()
    {
        if(http.readyState == 4) 
        {    
            var reponse=trim(http.responseText).split("####");
            $("#report_container3").html(reponse[0]);
            document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
            show_msg('3');
            release_freezing();
        }
    }
    // ================ for buyer backlog detail
    function display_buyer_details(buyer_id,client_id)
    {        
        freeze_window(3);
        $("#report_container3").html('');
        var company_name = $("#cbo_company_name").val();
        var location_name = $("#cbo_location_name").val();
        var report_title="Backlog - Style wise";
        var data="action=display_report_by_buyer&report_title="+report_title+"&buyer_id="+buyer_id+"&client_id="+client_id+"&company_name="+company_name+"&location_name="+location_name;        
        // var data="action=display_report_by_buyer"+get_submitted_data_string('cbo_company_name*cbo_location_name',"../../../")+'&report_title='+report_title+'&buyer_id='+buyer_id;        
        
        http.open("POST","requires/order_monitoring_report_with_tna_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = display_buyer_details_reponse; 
    }
    function display_buyer_details_reponse()
    {
        if(http.readyState == 4) 
        {    
            var reponse=trim(http.responseText).split("####");
            // alert(reponse[0]);
            $("#report_container3").html(reponse[0]);
            show_msg('3');
            release_freezing();
        }
    }

    // ================ for Accessories  detail
    function show_acc_details_report(item_id)
    {        
        freeze_window(3);
        $("#report_container3").html('');
        var company_name = $("#cbo_company_name").val();
        var location_name = $("#cbo_location_name").val();
        var report_title="Backlog - Style wise";
        // var data="action=display_report_by_buyer&report_title="+report_title+"&buyer_id="+buyer_id+"&client_id="+client_id+"&company_name="+company_name+"&location_name="+location_name;        
        var data="action=display_accessories_details_report"+get_submitted_data_string('cbo_company_name*cbo_location_name*cbo_buyer_name*cbo_season_name*txt_date_from*txt_date_to*cbo_emblishment*cbo_start_alarm*cbo_end_alarm*txt_style_ref*cbo_backlog*txt_int_ref_no*cbo_search_by*cbo_merchant*cbo_client*cbo_order_status*cbo_item_name*cbo_status*cbo_shipment_status', "../../../")+'&report_title='+report_title+'&trims_group_id='+item_id;        
        
        http.open("POST","requires/order_monitoring_report_with_tna_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = display_buyer_details_reponse; 
    }
    function display_buyer_details_reponse()
    {
        if(http.readyState == 4) 
        {    
            var reponse=trim(http.responseText).split("####");
            // alert(reponse[0]);
            $("#report_container3").html(reponse[0]);
            show_msg('3');
            release_freezing();
        }
    }

    function report_generate_by_item(item_id,client_id)
    {
        var cbo_company_name = $("#cbo_company_name").val();
        var cbo_location_name = $("#cbo_location_name").val();
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_monitoring_report_with_tna_controller.php?cbo_company_name=' + cbo_company_name+'&cbo_location_name=' + cbo_location_name+'&item_id=' + item_id+'&client_id=' + client_id+ '&action=report_generate_by_item', 'Item wise Details', 'width=1200px,height=450px,center=1,resize=0,scrolling=0', '../../');
        
        /*var report_title="Actual Shipment Plan - Item wise Details";
        var data="action=report_generate_by_item"+get_submitted_data_string('cbo_company_name*cbo_location_name',"../../../")+'&report_title='+report_title+'&item_id='+item_id;
        
        freeze_window(3);
        http.open("POST","requires/order_monitoring_report_with_tna_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = generate_report_by_item_reponse;*/ 
    }
    function generate_report_by_item_reponse()
    {
        if(http.readyState == 4) 
        {    
            var reponse=trim(http.responseText).split("####");
            $("#report_container4").html(reponse[0]);
            document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
            show_msg('3');
            release_freezing();
        }
    }
    function report_by_buyer_style(buyer_id,client_id)
    {
        var cbo_company_name = $("#cbo_company_name").val();
        var cbo_location_name = $("#cbo_location_name").val();
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_monitoring_report_with_tna_controller.php?cbo_company_name=' + cbo_company_name+'&cbo_location_name=' + cbo_location_name+'&buyer_id=' + buyer_id+'&client_id=' + client_id+ '&action=report_by_buyer_style', 'Style wise Details', 'width=1200px,height=450px,center=1,resize=0,scrolling=0', '../../');

        /*var report_title="Actual Shipment Plan - Style wise";
        var data="action=report_by_buyer_style"+get_submitted_data_string('cbo_company_name*cbo_location_name',"../../../")+'&report_title='+report_title+'&buyer_id='+buyer_id;
        
        freeze_window(3);
        http.open("POST","requires/order_monitoring_report_with_tna_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = report_by_buyer_style_reponse;*/
    }
    function report_by_buyer_style_reponse()
    {
        if(http.readyState == 4) 
        {    
            var reponse=trim(http.responseText).split("####");
            $("#report_container5").html(reponse[0]);
            document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
            show_msg('3');
            release_freezing();
        }
    }
    function report_by_month_summery(month_year,client_id)
    {
        var cbo_company_name = $("#cbo_company_name").val();
        var cbo_location_name = $("#cbo_location_name").val();
        var cbo_buyer_name = $("#cbo_buyer_name").val();
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_monitoring_report_with_tna_controller.php?cbo_company_name=' + cbo_company_name+'&cbo_location_name=' + cbo_location_name+'&month_year=' + month_year+'&client_id=' + client_id+'&buyer_id=' + cbo_buyer_name+ '&action=report_by_month_summery', 'Month Summary Details', 'width=1200px,height=450px,center=1,resize=0,scrolling=0', '../../');

        /*var report_title="Actual Shipment Plan - Month Summery";
        var data="action=report_by_month_summery"+get_submitted_data_string('cbo_company_name*cbo_location_name',"../../../")+'&report_title='+report_title+'&month_year='+month_year;
        
        freeze_window(3);
        http.open("POST","requires/order_monitoring_report_with_tna_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = report_by_month_summery_reponse;*/
    }
    function report_by_month_summery_reponse()
    {
        if(http.readyState == 4) 
        {    
            var reponse=trim(http.responseText).split("####");
            $("#report_container6").html(reponse[0]);
            document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
            show_msg('3');
            release_freezing();
        }
    }
    function report_by_month_details(buyer_id,month_year)
    {
        var report_title="Actual Shipment Plan - Month details";
        var data="action=report_by_month_details"+get_submitted_data_string('cbo_company_name*cbo_location_name',"../../../")+'&report_title='+report_title+'&month_year='+month_year+'&buyer_id='+buyer_id;
        
        freeze_window(3);
        http.open("POST","requires/order_monitoring_report_with_tna_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = report_by_month_details_reponse;
    }
    function report_by_month_details_reponse()
    {
        if(http.readyState == 4) 
        {    
            var reponse=trim(http.responseText).split("####");
            $("#report_container7").html(reponse[0]);
            document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
            show_msg('3');
            release_freezing();
        }
    }

    function new_window()
    {
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
        '<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><style type="text/css">.block_div { width:auto;height:auto;text-wrap:normal;vertical-align:bottom;display: block;position: !important;-webkit-transform: rotate(-90deg);} </style><body>'+document.getElementById('all_report_container').innerHTML+'</body</html>'); 
        d.close();
        
        // document.getElementById('scroll_body').style.overflowY='scroll';
        //document.getElementsByClassName('tableContainer').style.maxHeight='325px';
        // $("#table_body tr:first").show();
    }  

    function fn_check_uncheck()
    {
        var lengths = $("[type=checkbox]").length;
        if($("#check_uncheck").is(":checked") != true){     
            for(var i=0; i<=lengths; i++){
                
                $("[type=checkbox]").prop('checked', false);
                $("[type=checkbox]").removeClass('rpt_check');
                $("[type=checkbox]").removeAttr('checked');
            }
        }else{
            $("[type=checkbox]").prop('checked', true);
            for(var i=0; i<=lengths; i++){
                
                $("[type=checkbox]").not("#check_uncheck").addClass('rpt_check');
                $("[type=checkbox]").attr('checked',"checked");
            }
        }    
    }

    function show_progress_report_details(action, job_number, width, type, country)
    {
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_monitoring_report_with_tna_controller.php?action=' + action + '&job_number=' + job_number + '&type=' + type + '&country=' + country, 'Work Progress Report Details', 'width=' + width + ',height=400px,center=1,resize=0,scrolling=0', '../../');
    }

    function open_rmg_popup(data,action,title,type)
    {
        var width = 350;
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_monitoring_report_with_tna_controller.php?action=' + action + '&data=' + data+'&type='+type, title, 'width=' + width + ',height=400px,center=1,resize=0,scrolling=0', '../../');
    }

    function show_acc_popup(search_string)
    {
        var width = 350;
        var title = "Acc Summery Popup";
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_monitoring_report_with_tna_controller.php?action=show_acc_popup&search_string=' + search_string, title, 'width=' + width + ',height=400px,center=1,resize=0,scrolling=0', '../../');
    }

    function show_progress_report_daysInHand(action, job_number, width, country)
    {
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_monitoring_report_with_tna_controller.php?action=' + action + '&job_number=' + job_number + '&country=' + country, 'Work Progress Report Details', 'width=' + width + ',height=370px,center=1,resize=0,scrolling=0', '../../');
    }

    function show_trims_rec(action, po_number, po_id, width)
    {
        var budget_version=document.getElementById('cbo_budget_version').value;
        if(budget_version==1)
        {
            emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_monitoring_report_with_tna_controller.php?action=' + action + '&po_number=' + po_number + '&po_id=' + po_id, 'Trims Receive Details', 'width=' + width + ',height=400px,center=1,resize=0,scrolling=0', '../../');
        }
        else
        {
             emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_monitoring_report_with_tna_controller.php?action=' + action + '&po_number=' + po_number + '&po_id=' + po_id, 'Trims Receive Details', 'width=' + width + ',height=400px,center=1,resize=0,scrolling=0', '../../');
        }
    }

    function progress_comment_popup(job_no, po_id, template_id, tna_process_type)
    {
        var budget_version=document.getElementById('cbo_budget_version').value;
        var data = "action=update_tna_progress_comment" +
                '&job_no=' + "'" + job_no + "'" +
                '&po_id=' + "'" + po_id + "'" +
                '&template_id=' + "'" + template_id + "'" +
                '&tna_process_type=' + "'" + tna_process_type + "'" +
                '&permission=' + "'" + permission + "'";

       if(budget_version==1)
       {
         http.open("POST", "requires/order_monitoring_report_with_tna_controller.php", true);
       }
       else
       {
         http.open("POST", "requires/order_monitoring_report_with_tna_controller.php", true);
       }

        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = generate_progress_comment_reponse;
    }

    function generate_progress_comment_reponse()
    {
        if (http.readyState == 4)
        {
            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                    '<html><head><title></title></head><body>' + http.responseText + '</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
            d.close();
        }
    }

    function change_title(val)
    { 
        if (val == 1)
        {
            document.getElementById('search_by_th_up').innerHTML = "Original Shipment Date";
        } 
        else if(val == 2)
        {
            document.getElementById('search_by_th_up').innerHTML = "Public Ship. Date";
        }
        else if(val == 3)
        {
            document.getElementById('search_by_th_up').innerHTML = "PO Receive Date";
        }
        else if(val == 4)
        {
            document.getElementById('search_by_th_up').innerHTML = "Country Shipment Date";
        }
        else
        {
            document.getElementById('search_by_th_up').innerHTML = "PO Insert Date";
        }
    }

    function openmypage_image(page_link, title)
    {
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0', '../../')
        emailwindow.onclose = function ()
        {
        }
    }

    function openmypage_order(po_break_down_id, company_name, item_id, country_id, action)
    {
        //var garments_nature = $("#cbo_garments_nature").val();
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_monitoring_report_with_tna_controller.php?po_break_down_id=' + po_break_down_id + '&company_name=' + company_name + '&item_id=' + item_id + '&country_id=' + country_id + '&action=' + action, 'Order Quantity', 'width=750px,height=350px,center=1,resize=0,scrolling=0', '../../');
    }
    
    function open_actual_po_popup(job_no,po_id,ir)
    {
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_monitoring_report_with_tna_controller.php?job_no=' + job_no+'&po_id=' + po_id+'&ir=' + ir+ '&action=open_actual_po_popup', 'Actual PO Info', 'width=1180px,height=450px,center=1,resize=0,scrolling=0', '../../');
    }    
    
    function open_unplan_popup(sqlCond,buyer,task_id)
    {
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_monitoring_report_with_tna_controller.php?buyer_id=' + buyer+'&sqlCond='+sqlCond+'&task_id='+task_id+'&action=open_unplan_popup', 'Unplan Popup', 'width=370px,height=400px,center=1,resize=0,scrolling=0', '../../');
    }    
    
    function open_unplan_popup2(sqlCond,buyer,task_id)
    {
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_monitoring_report_with_tna_controller.php?buyer_id=' + buyer+'&sqlCond='+sqlCond+'&task_id='+task_id+'&action=open_unplan_popup2', 'Unplan Popup', 'width=470px,height=400px,center=1,resize=0,scrolling=0', '../../');
    }
    
    function operation_bulletin_popup(style,item_id)
    {
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_monitoring_report_with_tna_controller.php?style=' + style+'&item_id=' + item_id+ '&action=operation_bulletin_popup', 'GSD Info', 'width=750px,height=350px,center=1,resize=0,scrolling=0', '../../');
    }

    function open_buyer_summary_popup(sqlcond,buyer_id,date_from,date_to,action)
    {
        var width = window.innerWidth;
        // width = 1310;
        width = width-20;
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_monitoring_report_with_tna_controller.php?buyer_id=' + buyer_id+'&date_from=' + date_from+'&date_to=' + date_to+'&width=' + width+'&sqlcond=' + sqlcond+ '&action='+action, 'Buyer Summary Season Wise Popup', 'width='+width+',height=400px,center=1,resize=0,scrolling=0', '../../');
    }

    function open_fab_stucture_popup(po_id,int_ref,client_id,style)
    {
        var width = window.innerWidth;
        width = 1310;
        // width = width-20;
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_monitoring_report_with_tna_controller.php?po_id=' + po_id+'&int_ref=' + int_ref+'&client_id=' + client_id+'&style=' + style+'&width=' + width+ '&action=report_generate_by_fab_stucture', 'Color & Size Wise Details Popup', 'width='+width+',height=450px,center=1,resize=0,scrolling=0', '../../');
    }
    
      

    function garments_popup(po_break_down_id,action)
    {        
        var width = window.innerWidth;
        width = width-10;
        var title='';
        if(action=="production_calendar_popup")
        {
            width = 1070;
            title = "Production Calendar Popup";
        }
        else if (action=="tna_calendar_popup") 
        {
             width = 1210;
             title = "Plan Calendar Popup";
        }
        else if (action=="ir_wise_kpi_popup") 
        {
            var width = window.innerWidth;
            width = width-20;
            title = "IR Wise KPI";
        }
        else if (action=="plan_monitoring_popup") 
        {
             width = 670;
             title = "Plan Monitoring Popup";
        }
        else if (action=="garments_wash_popup" || action=="garments_emb_popup" || action=="garments_print_popup") 
        {
             width = 1200;
             title = "Garments Popup";             
        }
        else if(action=="sample_approval_popup" || action=="labdip_approval_popup")
        {
            width=580;
        }
        else if(action=="yarn_allocatio_popup")
        {
            width=870;
            title = "Yarn Allocation Popup";
        }
        else if(action=="batch_popup")
        {
            width=980;
            title = "Batch Popup";
        }
        else if(action=="aop_popup")
        {
            width=1300;
            title = "AOP Popup";
        }
        else if(action=="exfactory_popup")
        {
            width=400;
            title = "Ex-factory Popup";
        }
        else if(action=="exfactory_popup_acct_po")
        {
            width=1260;
            title = "PO wise Shipment Status";
        }
        else if(action=="finish_popup")
        {
            //width=1740;
            title = "Finish Fabric Status";
        }
        else if(action=="dyed_yarn_req_popup")
        {
            width=1000;
            title = "Yarn Dyeing Status";
        }
        else if(action=="grey_popup")
        {
            //width=1320;
            title = "Grey Fabric Status";
        }
        var screen_size = screen.width;
        // alert(width);
        // var action = "garments_popup";
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_monitoring_report_with_tna_controller.php?po_break_down_id=' + po_break_down_id + '&action=' + action + '&screen_size='+screen_size, title, 'width='+width+'px,height=450px,center=1,resize=0,scrolling=0', '../../');
    }

    function circular_popup(data,action)
    {        
        var width = window.innerWidth;
        width = width-200;
        var title='';
        if(action=="dye_house_popup")
        {
            width=500;
            title = "Dye House Stock Popup";
        }
        else if(action=="grey_stock_popup")
        {
            width=500;
            title = "Grey Stock Popup";
        }
        else if(action=="finishing_floor_popup")
        {
            width=650;
            title = "Finishing Floor Stock Popup";
        }
        else if(action=="textile_stock_popup")
        {
            width=500;
            title = "Textile Stock Popup";
        }
        else if(action=="garments_stock_popup")
        {
            width=500;
            title = "Garments Store Stock";
        }
        else if(action=="cutting_floor_issue_popup")
        {
            width=500;
            title = "Cutting floor issue popup";
        }
        else if(action=="purchased_circular_ref_wise_popup")
        {
            //width=1200;
            title = "Fabrication wise details";
        }

        var screen_size = screen.width;
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_monitoring_report_with_tna_controller.php?data_str=' + data + '&action=' + action + '&screen_size='+screen_size, title, 'width='+width+'px,height=450px,center=1,resize=0,scrolling=0', '../../');
    }

    function print_report_button_setting(report_ids) 
    {
       
        $('#show_button').hide();
        $('#show_button1').hide();
        var report_id=report_ids.split(",");
        report_id.forEach(function(items){
            if(items==108){$('#show_button').show();}
            else if(items==248){$('#show_button1').show();}
            });
    }
    function open_intref_search_popup()
    {    
        if(form_validation('cbo_company_name','Company Name')==false)
        {
            return;
        }
        var company = $("#cbo_company_name").val();
        var page_link='requires/order_monitoring_report_with_tna_controller.php?action=open_intref_search_popup&company='+company; 
        var title="Search Popup";
        
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=370px,center=1,resize=0,scrolling=0','../../')
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var order_no=this.contentDoc.getElementById("hide_order_no").value;
            var job_id=this.contentDoc.getElementById("hide_order_id").value;
           
            $("#txt_int_ref_no").val(order_no);
            $("#hidden_order_id").val(job_id);
        }

    }

    function open_fab_transfer_popup(action,search_string,title)
    {
        var width = 800;
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_monitoring_report_with_tna_controller.php?action='+action+'&search_string=' + search_string, title, 'width=' + width + ',height=400px,center=1,resize=0,scrolling=0', '../../');
    }
</script>
</head>
<body onLoad="set_hotkey();">
    <form id="dateWiseProductionReport_1">
        <div style="width:99.8%;" align="center">    
            <? echo load_freeze_divs("../../../", ''); ?>
            <h3 style="width:99.8%; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id, 'content_search_panel', '')"> -Search Panel</h3> 
            <div id="content_search_panel" >      
                <fieldset style="width:99.8%;">
                    <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>                    
                            <th width="4.2%" class="must_entry_caption">Company Name</th>
                            <th width="4.2%">Location</th>
                            <th width="4.2%">Buyer Name</th>
                            <th width="4.2%">Buyer Season</th>
                            <th width="4.2%">Item</th>
                            <th width="4.2%">Merchant</th>
                            <th width="4.2%">Fab. Mgt</th>
                            <th width="4.2%">Style Ref.</th>
                            <th width="4.2%">Int. Ref. No</th>           
                            <th width="4.2%">Embil</th>
                            <th width="4.2%">Start Alarm</th>
                            <th width="4.2%">End Alarm</th>
                            <th width="4.2%">Backlog</th>
                            <th width="4%">Balance</th>
                            <th width="4.2%">shipment Status</th>
                            <th width="4.2%">Order Status</th>
                            <th width="4.2%">Status</th>
                            <th width="4.2%">Search Type</th>
                            <th width="10.4%" id="search_by_th_up">Original Shipment Date</th>
                            <th width="5.0%"><input type="reset" id="reset_btn" class="formbutton" style="width:60px" value="Reset" /></th>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td align="center"> 
                                    <?
                                    echo create_drop_down("cbo_company_name", 60, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/order_monitoring_report_with_tna_controller',this.value,'load_drop_down_location', 'location_td' );load_drop_down( 'requires/order_monitoring_report_with_tna_controller',this.value,'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/order_monitoring_report_with_tna_controller',this.value,'load_drop_down_buyer_client', 'client_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/order_monitoring_report_with_tna_controller' );");
                                    ?>
                                </td>
                                <td id="location_td"  align="center">
                                    <?
                                    echo create_drop_down("cbo_location_name", 60, $blank_array, "", 1, "-- Select Location --", $selected, "", "", "");
                                    ?>
                                </td>
                                <td id="buyer_td"  align="center">
                                    <?
                                    echo create_drop_down("cbo_buyer_name", 60, $blank_array, "", 1, "-- Select Buyer --", $selected, "", 1, "");
                                    ?>
                                </td>
                                <td id="season_td"  align="center">
                                    <?
                                    echo create_drop_down("cbo_season_name", 60, $blank_array, "", 1, "-- Select Season --", $selected, "", 1, "");
                                    ?>
                                </td>
                                <td id="item_td"  align="center">
                                    <?
                                    echo create_drop_down("cbo_item_name", 60, $garments_item, "", 1, "-- Select Item --", $selected, "", 0, "");
                                    ?>
                                </td>
                                <td id="team_td">
                                    <?
                                    echo create_drop_down("cbo_merchant", 60, "select id,team_name from lib_marketing_team where status_active=1 and is_deleted=0 order by team_name", "id,team_name", 1, "-- Select Team --", $selected, " load_drop_down( 'requires/order_monitoring_report_with_tna_controller', this.value, 'load_drop_down_team_member', 'team_td' );");
                                    //echo create_drop_down("cbo_merchant", 100, $blank_array, "", 1, "- Merchant- ", $selected, "");
                                    ?>  
                                </td>
                                <td id="client_td">
                                    <?
                                    echo create_drop_down("cbo_client", 60, $blank_array, "", 1, "-- Select -- ", $selected, "");
                                    ?>  
                                </td>
                                <td align="center">
                                    <input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:60px" placeholder="Styel Ref" >
                                </td>
                                <td align="center">
                                    <input name="txt_ref_no" id="txt_int_ref_no" class="text_boxes" style="width:60px" placeholder="Write/Browse" ondblclick="open_intref_search_popup()">
                                    <input type="hidden" id="hidden_order_id">
                                </td>
                                <td align="center">
                                    <? $embArray = array(
                                        0 => 'Select',
                                        1 => 'AOP',
                                        2 => 'Yarn Dyeing',
                                        3 => 'Gmts Wash/Deep Dye',
                                        4 => 'Print',
                                        5 => 'Embroidery',
                                        6 => 'Outsource Fabric' 
                                    ); 
                                    echo create_drop_down("cbo_emblishment", 60, $embArray, "", 0, "", "", '', 0);
                                    ?>
                                    
                                </td>
                                <td align="center">
                                    <? $alarmStartArray = array(
                                        0=>'Select',
                                        1=>'Labdip Sub',
                                        2=>'Labdip App',
                                        3=>'SMS/GSS Sub',
                                        4=>'SMS/ GSS App',
                                        5=>'AOP Strike Off Sub',
                                        6=>'AOP Strike Off App',
                                        7=>'Styling Sub',
                                        8=>'Styling App',
                                        9=>'SS Submission',
                                        10=>'SS  Approval',
                                        11=>'Ware Trial  Sub',
                                        12=>'Ware Trial  App',
                                        13=>'Bulk Fab Sub To Buyer',
                                        14=>'Bulk Fab App From Buyer',
                                        15=>'Bulk Fab Sub To Lab',
                                        16=>'Bulk Fab App From Lab',
                                        17=>'Knit down Sub',
                                        18=>'Knit down App',
                                        19=>'Print Strike Off Sub',
                                        20=>'Print Strike Off App',
                                        21=>'Emb Strike off Sub',
                                        22=>'Emb Strike off App',
                                        23=>'PP Submission',
                                        24=>'PP Approval',
                                        25=>'Trim Card Sub',
                                        26=>'Trim Card App',
                                        27=>'Top Sam Sub To Buyer',
                                        28=>'Top Sam App From Buyer',
                                        29=>'Top Sam Sub To Lab',
                                        30=>'Top Sam App From Lab',
                                        31=>'Mock up Sam Sub',
                                        32=>'Mock up Sam App',
                                        33=>'Accessories Booking',
                                        34=>'Fabric Booking',
                                        35=>'Yarn Allocation',
                                        36=>'Dyed Yarn Receive',
                                        37=>'Grey Production',
                                        38=>'Dyeing',
                                        39=>'AOP Receive',
                                        40=>'F. Fabric Recvd',
                                        41=>'Cutting QC Production',
                                        42=>'Printing Receive',
                                        43=>'Embroidery Receive',
                                        44=>'Sewing Trims Rcvd',
                                        45=>'Finish Trims Rcvd',
                                        46=>'Sewing',
                                        47=>'Garments Wash Rcv',
                                        48=>'Garments Finishing'
                                    ); 
                                    echo create_drop_down("cbo_start_alarm", 60, $alarmStartArray, "", 0, "", "", '', 0);
                                    ?>
                                    
                                </td>
                                <td align="center">
                                    <? $alarmEndArray = array(
                                        0=>'Select',
                                        1=>'Labdip Sub',
                                        2=>'Labdip App',
                                        3=>'SMS/GSS Sub',
                                        4=>'SMS/ GSS App',
                                        5=>'AOP Strike Off Sub',
                                        6=>'AOP Strike Off App',
                                        7=>'Styling Sub',
                                        8=>'Styling App',
                                        9=>'SS Submission',
                                        10=>'SS  Approval',
                                        11=>'Ware Trial  Sub',
                                        12=>'Ware Trial  App',
                                        13=>'Bulk Fab Sub To Buyer',
                                        14=>'Bulk Fab App From Buyer',
                                        15=>'Bulk Fab Sub To Lab',
                                        16=>'Bulk Fab App From Lab',
                                        17=>'Knit down Sub',
                                        18=>'Knit down App',
                                        19=>'Print Strike Off Sub',
                                        20=>'Print Strike Off App',
                                        21=>'Emb Strike off Sub',
                                        22=>'Emb Strike off App',
                                        23=>'PP Submission',
                                        24=>'PP Approval',
                                        25=>'Trim Card Sub',
                                        26=>'Trim Card App',
                                        27=>'Top Sam Sub To Buyer',
                                        28=>'Top Sam App From Buyer',
                                        29=>'Top Sam Sub To Lab',
                                        30=>'Top Sam App From Lab',
                                        31=>'Mock up Sam Sub',
                                        32=>'Mock up Sam App',
                                        33=>'Accessories Booking',
                                        34=>'Fabric Booking',
                                        35=>'Yarn Allocation',
                                        36=>'Dyed Yarn Receive',
                                        37=>'Grey Production',
                                        38=>'Dyeing',
                                        39=>'AOP Receive',
                                        40=>'F. Fabric Recvd',
                                        41=>'Cutting QC Production',
                                        42=>'Printing Receive',
                                        43=>'Embroidery Receive',
                                        44=>'Sewing Trims Rcvd',
                                        45=>'Finish Trims Rcvd',
                                        46=>'Sewing',
                                        47=>'Garments Wash Rcv',
                                        48=>'Garments Finishing'
                                    ); 
                                    echo create_drop_down("cbo_end_alarm", 60, $alarmEndArray, "", 0, "", "", '', 0);
                                    ?>
                                    
                                </td>
                                <td align="center">
                                    <? $backlogArray = array(
                                        0 => 'Select',
                                        1 => 'Labdip Sub Backlog',
                                        2 => 'Labdip App Backlog',
                                        3 => 'SMS/ GSS Sub Backlog',
                                        4 => 'SMS/ GSS App Backlog',
                                        5 => 'AOP Strike Off Sub Backlog',
                                        6 => 'AOP Strike Off App Backlog',
                                        7 => 'Styling Sub Backlog',
                                        8 => 'Styling App Backlog',
                                        9 => 'Size Set Sub Backlog',
                                        10 => 'Size Set App Backlog',
                                        11 => 'Ware Trial Sample Sub Backlog',
                                        12 => 'Ware Trial Sample App Backlog',
                                        13 => 'Bulk Fab Sub To Buyer Backlog',
                                        14 => 'Bulk Fab App From Buyer Backlog',
                                        15 => 'Bulk Fab Sub To Lab Backlog',
                                        16 => 'Bulk Fab App From Lab Backlog',
                                        17 => 'Knit down Sub Backlog',
                                        18 => 'Knit down App Backlog',
                                        19 => 'Print Strike Off Sub Backlog',
                                        20 => 'Print Strike Off App Backlog',
                                        21 => 'Emb Strike off Sub Backlog',
                                        22 => 'Emb Strike off App Backlog',
                                        23 => 'PP Sub Backlog',
                                        24 => 'PP App Backlog',
                                        25 => 'Trim Card Sub Backlog',
                                        26 => 'Trim Card App Backlog',
                                        27 => 'Top Sample Sub To Buyer Backlog',
                                        28 => 'Top Sample App From Buyer Backlog',
                                        29 => 'Top Sample Sub To Lab Backlog',
                                        30 => 'Top Sample App From Lab Backlog',
                                        31 => 'Mock up Sample Sub Backlog',
                                        32 => 'Mock up Sample App Backlog',
                                        33 => 'Accessories Booking Backlog',
                                        34 => 'Fabric Booking Backlog',
                                        35 => 'Yarn Backlog',
                                        36 => 'Yarn Dyeing Backlog',
                                        37 => 'Grey Fabric Backlog',
                                        38 => 'Dyeing Prod Backlog', 
                                        39 => 'AOP Fab Backlog', 
                                        40 => 'Finish Fab Backlog', 
                                        41 => 'Cut and Lay Backlog', 
                                        42 => 'Printing Backlog', 
                                        43 => 'Embroidery Backlog', 
                                        44 => 'Sewing Trims Rcv Backlog', 
                                        45 => 'Fin Trims Rcv Backlog', 
                                        46 => 'Sewing Backlog', 
                                        47 => 'Gmt Wash Backlog', 
                                        48 => 'Gmt Finish Backlog', 
                                        49 => 'Ship Backlog' 
                                    ); 
                                    echo create_drop_down("cbo_backlog", 60, $backlogArray, "", 0, "", "", '', 0);
                                    ?>
                                    
                                </td>
                                <td>
                                    <?
                                    $balanceArray = array(
                                        0 => 'Select',
                                        1 => 'Acc Booking',
                                        2 => 'Fabric Booking',
                                        3 => 'Yarn Allo',
                                        4 => 'Dyed Yarn',
                                        5 => 'Grey Fab',
                                        6 => 'Dyeing', 
                                        7 => 'AOP Fab',
                                        8 => 'Finish Fab',
                                        9 => 'Cutting',
                                        10 => 'Printing',
                                        11 => 'Embroidery',
                                        12 => 'Sewing Trims',
                                        13 => 'Finish Trims',
                                        14 => 'Sewing',
                                        15 => 'Gmts Wash',
                                        16 => 'Gmts Finishing',
                                        17 => 'Shipment'
                                    );
                                    ?>
                                    <?=create_drop_down("cbo_balance", 60, $balanceArray, "", 0, "-- Select --", "", '', 0);?>
                                </td>
                                <td align="center">
                                    <?
                                    $shipment_status_arr = array(0 => "All Order",1=>'Open Order', 2 => "Close Order");
                                    echo create_drop_down("cbo_shipment_status", 60, $shipment_status_arr, "", 0, "", 1, '', 0); //search_by(this.value)
                                    ?>
                                </td>
                                <td align="center">
                                    <?
                                    $order_status_array = array(1 => "Confirm", 2 => "Projected");
                                    echo create_drop_down("cbo_order_status", 60, $order_status_array, "", 0, "", "", '', 0); 
                                    ?>
                                </td>
                                <td align="center">
                                    <?
                                    $order_status_array = array(1 => "Active", 2 => "Inactive",3 => "Cancelled");
                                    echo create_drop_down("cbo_status", 60, $order_status_array, "", 0, "", "", '', 0); 
                                    ?>
                                </td>
                                <td>
                                    <?
                                    $search_by = array(1 => "Original Ship Date", 2 => "Public Ship Date",3=>"PO Receive Date.",4=>"Country Ship Date",5=>"PO Insert Date");
                                    echo create_drop_down( "cbo_search_by", 60, $search_by, "", 0, "----Select----",0, "change_title(this.value);",0,"" ); 
                                    ?>
                                </td>
                        
                                <td align="center">
                                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:45px" placeholder="From Date">&nbsp;
                                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:45px"  placeholder="To Date">
                                </td>
                                <td>
                                    <input type="button" id="show_button" class="formbutton" style="width:60px;" value="Show" onClick="fn_report_generated(1)" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <!-- <td>
                                <? //echo load_month_buttons(1); ?>
                            </td> -->
                            <td align="right">
                                <input type="button" id="show_button" class="formbutton" style="width:80px;" value="Acc Summary" onClick="fn_report_generated(2)" />
                                <input type="button" id="show_button" class="formbutton" style="width:90px;" value="Buyer Summary" onClick="fn_report_generated(3)" />
                                <input type="button" id="show_button" class="formbutton" style="width:85px;" value="Style Summary" onClick="fn_report_generated(4)" />
                                <input type="button" id="show_button" class="formbutton" style="width:70px;" value="Alor Michil" onClick="fn_report_generated(5)" />
                                <input type="button" id="show_button" class="formbutton" style="width:55px;" value="Backlog" onClick="fn_report_generated(6)" title="Search By : Company, Location, Buyer, Fab. Mgt"/>
                                <input type="button" id="show_button" class="formbutton" style="width:55px;" value="Unplan" onClick="fn_report_generated(7)" />
                                <input type="button" id="show_button" class="formbutton" style="width:70px;" value="RMG" onClick="fn_rmg_report_generated(1)" />
                                <input type="button" id="show_button" class="formbutton" style="width:90px;" value="Circular Fabric" onClick="fn_circular_report_generated(1)" />
                                <input type="button" id="show_button" class="formbutton" style="width:90px;" value="Purchased Circular Fab." onClick="fn_circular_report_generated(6)" />
                            </td>
                        </tr>                        
                    </table>
                </fieldset>
            </div>
        </div>

        <div id="report_container" align="center" style="padding: 5px"></div>
        <div id="all_report_container">     
            <div id="report_container2"></div>      
            <div id="report_container3"></div>      
            <div id="report_container4"></div> 
            <div id="report_container5"></div> 
            <div id="report_container6"></div> 
            <div id="report_container7"></div> 
        </div>
        <!-- <div id="report_holder" align="center"></div> -->
        <div style="display:none;" id="data_panel"></div>
    </form>    
</body>
<script type="text/javascript">
    // set_multiselect('cbo_location_name','0','0','','0'); 
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

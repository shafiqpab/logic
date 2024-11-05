<?
/* -------------------------------------------- Comments -----------------------
  Purpose			: 	This Form Will Create Yarn Putrchase requisition Follow Up report.
  Functionality		:
  JS Functions		:
  Created by		:	Md. Helal Uddin
  Creation date 	: 	07-12-2020
  Updated by 		:
  Update date		:
  QC Performed BY	:
  QC Date			:
  Comments			: 
 */

session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
    header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Yarn Putrchase requisition Follow Up report", "../../", 1, 1, '', 1, 1);
?>	

<script>

    if ($('#index_page', window.parent.document).val() != 1)
        window.location.href = "../../logout.php";
    var permission = '<? echo $permission; ?>';

    function fn_report_generated(type)
    {
        var job_no = $('#txt_job_no').val();
        var po_no = $('#txt_search_string').val();
       
        var ref_no = $('#txt_ref_no').val();
        var date_from_po = $('#txt_date_from_po').val();
        var order_status = $('#cbo_order_status').val();
        var booking_no = $('#txt_booking_no').val();

        if ($('#chk_no_boking').attr('checked')) var chk_no_boking = 1; else var chk_no_boking = 0;

        if (form_validation('cbo_company_name', 'Comapny Name') == false)
        {
            return;
        }

        if (job_no == "" && po_no == ""  && ref_no == "" && date_from_po == "" && booking_no == "")
        {
            if (form_validation('txt_date_from*txt_date_to', 'Date Form*Date To') == false)
            {
                return;
            }
        }

        if (type == 1)
        {
            var data = "action=report_generate" + get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_search_string*txt_date_from*txt_date_to*txt_date_from_po*txt_date_to_po*txt_job_no*txt_booking_no*txt_booking_id*cbo_year*txt_ref_no*cbo_order_status', "../../") + '&chk_no_boking=' + chk_no_boking;
        } 
		else if(type == 2)
        {
            var data = "action=report_generate2" + get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_search_string*txt_date_from*txt_date_to*txt_date_from_po*txt_date_to_po*txt_job_no*txt_booking_no*txt_booking_id*cbo_year*txt_ref_no*cbo_order_status', "../../") + '&chk_no_boking=' + chk_no_boking;
        }
		else
		{
            var data = "action=report_generate3" + get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_search_string*txt_date_from*txt_date_to*txt_date_from_po*txt_date_to_po*txt_job_no*txt_booking_no*txt_booking_id*cbo_year*txt_ref_no*cbo_order_status', "../../") + '&chk_no_boking=' + chk_no_boking;
		}

        //alert(data);return;

        freeze_window(3);
        http.open("POST", "requires/yarn_purchase_requisition_follow_up_report_v2_controller.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fn_report_generated_reponse;
    }


    function fn_report_generated_reponse()
    {
        if (http.readyState == 4)
        {
            var response = trim(http.responseText).split("####");
            $('#report_container2').html(response[0]);
            document.getElementById('report_container').innerHTML = '<a href="requires/' + response[1] + '" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:155px"/></a>';
            if(response[2]!=0){$('#report_container').append('&nbsp;&nbsp;&nbsp;<a href="' + response[2] + '" style="text-decoration:none"><input type="button" value="Convert To Excel Short" name="excel" id="excel" class="formbutton" style="width:155px"/></a>');}
            var tot_rows = $('#table_body tr').length;
            if (tot_rows > 1 && response[3] == 1)
            {
                
                var tableFilters = {
                    //col_10:'none',
                    // display_all_text: " ---Show All---",
                    col_operation: {
                        id: ["value_tot_order_qnty", "value_tot_yarn_rec"],
                        //col: [12,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,36,37,38,39,40,41,42,43,44],
                        col: [8, 15],
                        operation: ["sum", "sum"],
                        write_method: ["innerHTML", "innerHTML"]
                    }
                }
                 
				
                setFilterGrid("table_body", -1);
            }
            
			
            //append_report_checkbox('table_header_1',1);
            // $("input:checkbox").hide();
            show_msg('3');
            release_freezing();
        }

    }

    function new_window()
    {
        document.getElementById('company_id_td').style.visibility = 'visible';
        document.getElementById('date_td').style.visibility = 'visible';
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write(document.getElementById('buyer_summary').innerHTML);
        document.getElementById('company_id_td').style.visibility = 'hidden';
        document.getElementById('date_td').style.visibility = 'hidden';
        d.close();
    }

    function show_inner_filter(e)
    {
        if (e != 13) {
            var unicode = e.keyCode ? e.keyCode : e.charCode
        } else {
            unicode = 13;
        }
        if (unicode == 13)
        {
            fn_report_generated(2);
        }
    }

    function search_by(val)
    {
        $('#txt_search_string').val('');
        
        $('#txt_ref_no').val('');

        if (val == 1)
        {
            $('#search_by_td_up').html('Order No');
            
            $('#txt_ref_no').removeAttr('disabled', 'disabled');
            $('#cbo_order_status').removeAttr('disabled', 'disabled');
        } else
        {
            $('#search_by_td_up').html('Style Ref.');
            
            $('#txt_ref_no').attr('disabled', 'disabled');
            $('#cbo_order_status').val(0);
            $('#cbo_order_status').attr('disabled', 'disabled');
        }
    }

    function open_febric_receive_status_order_wise_popup(order_id, type, color)
    {
        var popup_width = '';
        if (type == "fabric_receive" || type == "fabric_purchase" || type == "grey_issue" || type == "dye_qnty")
        {
            popup_width = '900px';
        } else if (type == "grey_receive" || type == "grey_purchase")
        {
            popup_width = '1050px';
        } else
            popup_width = '760px';

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/yarn_purchase_requisition_follow_up_report_v2_controller.php?order_id=' + order_id + '&action=' + type + '&color=' + color, 'Detail Veiw', 'width=' + popup_width + ', height=450px,center=1,resize=0,scrolling=0', '../');
    }

    function openmypage(order_id, type, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type)
    {
        var popup_width = '';
        if (type == "yarn_issue_not")
        {
            popup_width = '1000px';
        } else
        {
            popup_width = '890px';
        }
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/yarn_purchase_requisition_follow_up_report_v2_controller.php?order_id=' + order_id + '&action=' + type + '&yarn_count=' + yarn_count + '&yarn_comp_type1st=' + yarn_comp_type1st + '&yarn_comp_percent1st=' + yarn_comp_percent1st + '&yarn_comp_type2nd=' + yarn_comp_type2nd + '&yarn_comp_percent2nd=' + yarn_comp_percent2nd + '&yarn_type_id=' + yarn_type, 'Detail Veiw', 'width=' + popup_width + ', height=450px,center=1,resize=0,scrolling=0', '../');
    }

    function generate_worder_report(type, booking_no, company_id, order_id, fabric_nature, fabric_source, job_no, approved, action)
    {
        var report_title='';
        if(type==2)
        {
            report_title='Main Fabric Booking V2';
        }else if(type==1){
            report_title='Short Fabric Booking';
        }
        var data = "action=" + action +
                '&txt_booking_no=' + "'" + booking_no + "'" +
                '&cbo_company_name=' + "'" + company_id + "'" +
                '&txt_order_no_id=' + "'" + order_id + "'" +
                '&cbo_fabric_natu=' + "'" + fabric_nature + "'" +
                '&cbo_fabric_source=' + "'" + fabric_source + "'" +
                '&id_approved_id=' + "'" + approved + "'" +
                '&report_title=' + report_title +
                '&txt_job_no=' + "'" + job_no + "'";
        if (type == 1)
        {
            http.open("POST", "../../order/woven_order/requires/short_fabric_booking_controller.php", true);
        } else if (type == 2)
        {

            http.open("POST", "../../order/woven_order/requires/fabric_booking_urmi_controller.php", true);
            // http.open("POST", "../../order/woven_order/requires/fabric_booking_controller.php", true);
        } else
        {
            http.open("POST", "../../order/woven_order/requires/sample_booking_controller.php", true);
        }

        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = generate_fabric_report_reponse;
    }

    function generate_fabric_report_reponse()
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

    function generate_pre_cost_report(type, job_no, company_id, buyer_id, style_ref, costing_date)
    {
        var data = "action=" + type +
                '&txt_job_no=' + "'" + job_no + "'" +
                '&cbo_company_name=' + "'" + company_id + "'" +
                '&cbo_buyer_name=' + "'" + buyer_id + "'" +
                '&txt_style_ref=' + "'" + style_ref + "'" +
                '&txt_costing_date=' + "'" + costing_date + "'" +
                "&zero_value=1" +
                '&path=../../';

        http.open("POST", "../../order/woven_order/requires/pre_cost_entry_controller.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fnc_generate_report_reponse;
    }

    function fnc_generate_report_reponse()
    {
        if (http.readyState == 4)
        {
            $('#data_panel').html(http.responseText);

            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                    '<html><head><title></title></head><body>' + document.getElementById('data_panel').innerHTML + '</body</html>');
            d.close();
        }
    }

    /*function progress_comment_popup(job_no,po_id,template_id,tna_process_type)
     {
     emailwindow=dhtmlmodal.open('EmailBox', 'iframe', '../../reports/management_report/merchandising_report/requires/shipment_date_wise_wp_report_controller.php?job_no='+job_no+'&po_id='+po_id+'&template_id='+template_id+'&tna_process_type='+tna_process_type+'&action=update_tna_progress_comment'+'&permission='+permission, "TNA Progress Comment", 'width=1030px,height=390px,center=1,resize=1,scrolling=0','../');
     }*/


    function progress_comment_popup(job_no, po_id, template_id, tna_process_type)
    {
        var data = "action=update_tna_progress_comment" +
                '&job_no=' + "'" + job_no + "'" +
                '&po_id=' + "'" + po_id + "'" +
                '&template_id=' + "'" + template_id + "'" +
                '&tna_process_type=' + "'" + tna_process_type + "'" +
                '&permission=' + "'" + permission + "'";

        http.open("POST", "../../reports/management_report/merchandising_report/requires/shipment_date_wise_wp_report_controller.php", true);

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

    function openmypage_image(page_link, title)
    {
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0', '../')
        emailwindow.onclose = function ()
        {
        }
    }

 function print_report_button_setting(report_ids) 
    {
     
        $('#show_button').hide();
        $('#show_button1').hide();
        $('#show_button2').hide();
        var report_id=report_ids.split(",");
        report_id.forEach(function(items){
            if(items==108){$('#show_button').show();}
            else if(items==256){$('#show_button1').show();}
            else if(items==258){$('#show_button2').show();}
            });
    }
    function openmypage_booking()
    {
        if( form_validation('cbo_company_name','Company Name')==false )
        {
            return;
        }
        
        var title='Booking No Search';
        var action='booking_no_popup';
        var widthVal='1055px';
        
        var companyID = $("#cbo_company_name").val();
        var buyer_name = $("#cbo_buyer_name").val();
        var cbo_year  = $("#cbo_year").val();
        var page_link='requires/yarn_purchase_requisition_follow_up_report_v2_controller.php?action='+action+'&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year='+cbo_year;
        
        
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+widthVal+',height=370px,center=1,resize=1,scrolling=0','../../');
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            // alert(no+','+id);
            
            var no=this.contentDoc.getElementById("txt_booking_no").value;
            var id=this.contentDoc.getElementById("txt_booking_id").value;
            var po=this.contentDoc.getElementById("txt_order_id").value;
            $('#txt_booking_no').val(no);
            $('#txt_booking_id').val(id);             
        }
    }
    

</script>

<style>
    hr
    {
        color: #676767;
        background-color: #676767;
        height: 1px;
    }
</style> 
</head>

<body onLoad="set_hotkey();">
    <? echo load_freeze_divs("../../", ''); ?>
    <form id="fabricReceiveStatusReport_1">
        <div style="width:100%;" align="center">    
            <h3 style="width:1480px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id, 'content_search_panel', '')"> -Search Panel</h3> 
            <div id="content_search_panel" >      
                <fieldset style="width:1480px;">
                    <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                        <th class="must_entry_caption">Company Name</th>
                        <th>Buyer Name</th>
                        <th colspan="2">Shipment Date</th>
                        <th colspan="2">PO Insert Date</th>
                        <th>Job Year</th>
                        <th>Job No</th>
                        <th>Yarn Purchase<br>Requisition No</th>
                        <th>Style Ref</th>
                        <th id="search_by_td_up">Order No</th>
                        <th>Order Status</th>
                        
                        <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('fabricReceiveStatusReport_1', 'report_container*report_container2', '', '', '')" class="formbutton" style="width:70px" /></th>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td> 
                                    <?
                                    echo create_drop_down("cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name", "id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/yarn_purchase_requisition_follow_up_report_v2_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );");
                                    ?>
                                </td>
                                <td id="buyer_td">
                                    <?
                                    echo create_drop_down("cbo_buyer_name", 120, $blank_array, "", 1, "-- All Buyer --", $selected, "", 0, "");
                                    ?>
                                </td>
                                <td>
                                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" readonly>
                                </td>
                                <td>
                                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date" readonly>
                                </td>
                                <td>
                                    <input name="txt_date_from_po" id="txt_date_from_po" class="datepicker" style="width:70px" placeholder="From Date" readonly>
                                </td> 
                                <td>
                                    <input name="txt_date_to_po" id="txt_date_to_po" class="datepicker" style="width:70px"  placeholder="To Date" readonly>
                                </td>
                                <td>
                                    <?
                                    echo create_drop_down("cbo_year", 65, create_year_array(), "", 1, "-- All --", 0, "", 0, "");
                                    ?>
                                </td>
                                <td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:70px" /></td>
                                <td><input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" placeholder="Write / Browse" style="width:70px" onDblClick="openmypage_booking();" /></td>
                                <input type="hidden" id="txt_booking_id" name="txt_booking_id">
                                
                                
                               
                               
                                <td>
                                    <input name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:60px" placeholder="Write" >
                                </td>
                                 <td id="search_by_td"><input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:70px" /></td>
                                <td align="center">
                                    <?
                                    $order_status = array(0 => "ALL", 1 => "Confirmed", 2 => "Projected");
                                    echo create_drop_down("cbo_order_status", 80, $order_status, "", 0, "", 0, "", "");
                                    ?>
                                </td>
                               
                                <td>
                                    <input type="button" id="show_button" class="formbutton" style="width:70px;" value="Show" onClick="fn_report_generated(1)" />
                                   
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table>
                        <tr>
                            <td>
                                <? echo load_month_buttons(1); ?>&nbsp;&nbsp;<input type="checkbox" name="chk_no_boking" id="chk_no_boking">&nbsp;Requisition
                            </td>
                            <td>
                            	<input type="button" id="show_button2" class="formbutton" style="width:100px;display: none;" value="FB issue Days" onClick="fn_report_generated(3)" />
                            </td>
                        </tr>
                    </table> 
                    <br />
                </fieldset>
            </div>
        </div>
        <div style="display:none" id="data_panel"></div>   
        <div id="report_container" align="center" style="padding: 10px;"></div>
        <div id="report_container2" align="left"></div>
    </form>   
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
</script>
</html>

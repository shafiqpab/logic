<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Woven Style Order Status Report.
Functionality	:
JS Functions	:
Created by		:	Zakaria joy
Creation date 	: 	15-06-2019
Updated by 		:
Update date		:
QC Performed BY	:
QC Date			:
Comments		:
*/

session_start();

if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

echo load_html_head_contents("Style Order Closing Report","../../../", 1, 1, $unicode,1,1);
?>
<script>
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";
    var permission = '<? echo $permission; ?>';

    function fn_report_generated()
    {
        var txt_style_ref = $("#txt_style_ref").val();
        var txt_order = $("#txt_order").val();
        var txt_ex_date_form = $("#txt_ex_date_form").val();
        var txt_ex_date_to = $("#txt_ex_date_to").val();

        if(txt_style_ref!="" || txt_order!="")
        {
            if(form_validation('cbo_company_name*cbo_search_type','Company Name*Search Type')==false)
            {
                return;
            }
        }
        else
        {
            if(txt_ex_date_form!="" && txt_ex_date_to!="")
            {
                if(form_validation('cbo_company_name*cbo_search_type*txt_ex_date_form*txt_ex_date_to','Company Name*Search Type*Ex factry Form Date*Ex factry To Date')==false)
                {
                    return;
                }
            }
            else
            {
                if(form_validation('cbo_company_name*cbo_search_type*txt_date_from*txt_date_to','Company Name*Search Type*Shipment Form Date*Shipment To Date')==false)
                {
                    return;
                }
            }
        }
        var report_title=$( "div.form_caption" ).html();
        var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_search_type*cbo_year*txt_style_ref*txt_style_ref_id*txt_order*txt_date_from*txt_date_to*txt_ex_date_form*txt_ex_date_to',"../../../")+'&report_title='+report_title;
        //alert(data);return;
        freeze_window(3);
        http.open("POST","requires/woven_style_order_status_report_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fn_report_generated_reponse;
    }

    function fn_report_generated_reponse()
    {
        if(http.readyState == 4)
        {
            release_freezing();
            var reponse=trim(http.responseText).split("**");
            //alert(reponse[2]);
            $('#report_container2').html(reponse[0]);
            //var tot_rows=$('#table_body tr').length;
            //document.getElementById('report_container').innerHTML=report_convert_button('../../../');
            document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
            /*document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';*/
            /*if(tot_rows>1)
             {*/
            if(reponse[2]==1)
            {
                var tableFilters = {
                    col_operation: {
                        id: ["td_order_qty","td_yarn_req_qty","td_yarn_issIn_qty","td_yarn_issOut_qty","td_yarn_trnsInq_qty","td_yarn_trnsOut_qty","td_yarn_issue_qty","td_yarn_undOvr_qty","td_grey_req_qty","td_grey_in_qty","td_grey_out_qty","td_grey_trnsIn_qty","td_grey_transOut_qty","td_grey_qty","td_grey_rec_qty","td_grey_prLoss_qty","td_grey_undOver_qty","td_grey_issDye_qty","td_grey_lftOver_qty","td_fin_req_qty","td_fin_in_qty","td_fin_out_qty","td_fin_transIn_qty","td_fin_transOut_qty","td_fin_qty","td_fin_prLoss_qty","td_fin_undOver_qty","td_fin_issCut_qty","td_fin_lftOver_qty","td_wovenReqQty","td_wovenRecQty","td_wovenRecBalQty","td_wovenIssueQty","td_wovenIssueBalQty","td_gmt_qty","td_cutting_qty","td_printIssIn_qty","td_printIssOut_qty","td_printIssue_qty","td_printRcvIn_qty","td_printRcvOut_qty","td_printRcv_qty","td_printRjt_qty","td_sewInInput_qty","td_sewInOutput_qty","td_sewIn_qty","td_sewInBal_qty",	"td_sewRcvIn_qty","td_sewRcvOut_qty","td_sewRcv_qty","td_sewRcvBal_qty","td_sewRcvRjt_qty","td_washRcvIn_qty","td_washRcvOut_qty","td_washRcv_qty","td_washRcvBal_qty","td_gmtFinIn_qty","td_gmtFinOut_qty","td_gmtFin_qty","td_gmtFinBal_qty","td_gmtFinRjt_qty","td_gmtrej_qty","td_gmtEx_qty","td_gmtFinLeftOver_qty","td_shortExcess_exFactory_qty","td_prLoss_qty","td_prLossDye_qty","td_prLossCut_qty"],
                        col: [6,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,  56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76],
                        operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
                        write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
                    }
                }
            }
            else
            {
                var tableFilters = {
                    col_operation: {
                        id: ["td_order_qty","td_yarn_req_qty","td_yarn_issIn_qty","td_yarn_issOut_qty","td_yarn_trnsInq_qty","td_yarn_trnsOut_qty","td_yarn_issue_qty","td_yarn_undOvr_qty","td_grey_req_qty","td_grey_in_qty","td_grey_out_qty","td_grey_trnsIn_qty","td_grey_transOut_qty","td_grey_qty","td_grey_prLoss_qty","td_grey_undOver_qty","td_grey_issDye_qty","td_grey_lftOver_qty","td_fin_req_qty","td_fin_in_qty","td_fin_out_qty","td_fin_transIn_qty","td_fin_transOut_qty","td_fin_qty","td_fin_prLoss_qty","td_fin_undOver_qty","td_fin_issCut_qty","td_fin_lftOver_qty","td_gmt_qty","td_printIssIn_qty","td_printIssOut_qty","td_printIssue_qty","td_printRcvIn_qty","td_printRcvOut_qty","td_printRcv_qty","td_printRjt_qty","td_sewInInput_qty","td_sewInOutput_qty","td_sewIn_qty","td_sewInBal_qty",	"td_sewRcvIn_qty","td_sewRcvOut_qty","td_sewRcv_qty","td_sewRcvBal_qty","td_sewRcvRjt_qty","td_washRcvIn_qty","td_washRcvOut_qty","td_washRcv_qty","td_washRcvBal_qty","td_gmtFinIn_qty","td_gmtFinOut_qty","td_gmtFin_qty","td_gmtFinBal_qty","td_gmtFinRjt_qty","td_gmtEx_qty","td_gmtFinLeftOver_qty","td_rjtPrint_qty","td_leftOverFin_qty","td_leftOverGmtFin_qty","td_leftOverTrm_qty","td_rjtPrint_qty","td_rjtEmb_qty","td_rjtSew_qty","td_rjtFin_qty","td_prLoss_qty","td_prLossFin_qty","td_prLossCut_qty"],
                        col: [5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,	46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72],
                        operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
                        write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
                    }
                }
            }
            //setFilterGrid("table_body",-1,tableFilters);
            setFilterGrid("table_body",-1,'');
            //}
            //append_report_checkbox('table_header_1',1);
            show_msg('3');
        }
    }

    function new_window()
    {
        document.getElementById('scroll_body').style.overflow="auto";
        document.getElementById('scroll_body').style.maxHeight="none";

        $('#table_body tr:first').hide();

        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
            '<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
        d.close();

        $('#table_body tr:first').show();

        document.getElementById('scroll_body').style.overflowY="scroll";
        document.getElementById('scroll_body').style.maxHeight="380px";
    }

    function openmypage_style()
    {
        if( form_validation('cbo_company_name','Company Name')==false )
        {
            return;
        }
        var company = $("#cbo_company_name").val();
        var buyer = $("#cbo_buyer_name").val();
        var cbo_year = $("#cbo_year").val();
        var txt_style_ref_no = $("#txt_style_ref_no").val();
        var txt_style_ref_id = $("#txt_style_ref_id").val();
        var txt_style_ref = $("#txt_style_ref").val();
        var page_link='requires/woven_style_order_status_report_controller.php?action=style_refarence_surch&companyID='+company+'&buyer_name='+buyer+'&txt_style_ref_no='+txt_style_ref_no+'&txt_style_ref_id='+txt_style_ref_id+'&txt_style_ref='+txt_style_ref+'&cbo_year_id='+cbo_year;
        var title="Search Item Popup";
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=450px,height=370px,center=1,resize=0,scrolling=0','../../')
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
            var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
            var style_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
            //alert(style_no);
            $("#txt_style_ref").val(style_des);
            $("#txt_style_ref_id").val(style_id);
            $("#txt_style_ref_no").val(style_no);
        }
    }

    function openmypage_order()
    {
        if( form_validation('cbo_company_name','Company Name')==false )
        {
            return;
        }
        var company = $("#cbo_company_name").val();
        var buyer = $("#cbo_buyer_name").val();
        var txt_style_ref = $("#txt_style_ref").val();
        var cbo_year = $("#cbo_year").val();
        var txt_order_id_no = $("#txt_order_id_no").val();
        var txt_order_id = $("#txt_order_id").val();
        var txt_order = $("#txt_order").val();
        var page_link='requires/woven_style_order_status_report_controller.php?action=order_surch&company='+company+'&buyer_name='+buyer+'&txt_order_id_no='+txt_order_id_no+'&txt_order_id='+txt_order_id+'&txt_order='+txt_order+'&txt_style_ref='+txt_style_ref+'&cbo_year='+cbo_year;
        var title="Search Item Popup";
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=700px,height=370px,center=1,resize=0,scrolling=0','../../')
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
            var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
            var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
            //alert(style_des_no);
            $("#txt_order").val(style_des);
            $("#txt_order_id").val(style_id);
            $("#txt_order_id_no").val(style_des_no);
        }
    }

    function fn_order_disable(type_id)
    {
        if(type_id==2)
        {
            $('#txt_order').attr("disabled",true);
        }
        else
        {
            $('#txt_order').attr("disabled",false);
        }
    }
    function fn_date_chack(str)
    {
        if(str==1)
        {
            var ship_date=$('#txt_date_from').val();
            if(ship_date!="")
            {
                $('#txt_ex_date_form').val("");
                $('#txt_ex_date_to').val("");
            }
        }
        else
        {
            var ex_fact_date=$('#txt_ex_date_form').val();
            if(ex_fact_date!="")
            {
                $('#txt_date_from').val("");
                $('#txt_date_to').val("");
            }
        }
    }

    function open_trims_dtls(po_break_down_id,tot_po_qnty,ratio,page_title,action)
    {
        //alert(po_break_down_id);
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/woven_style_order_status_report_controller.php?po_break_down_id='+po_break_down_id+'&tot_po_qnty='+tot_po_qnty+'&ratio='+ratio+'&action='+action, page_title, 'width=670px,height=400px,center=1,resize=0,scrolling=0','../../');
    }
    function generate_ex_factory_popup(action,job,id,width)
    {
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/woven_style_order_status_report_controller.php?action='+action+'&job='+job+'&id='+id, 'Ex-Factory Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
    }

    function openmypage_rej(po_id,company,action,reportType)
    {
        //alert(country_id);
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/woven_style_order_status_report_controller.php?po_id='+po_id+'&company='+company+'&action='+action+'&reportType='+reportType, 'Reject Quantity', 'width=600px,height=350px,center=1,resize=0,scrolling=0','../../');
    }
	 function openmypage_season()
    {
        if(form_validation('cbo_company_name','Company Name')==false)
        {
            return;
        }
        var companyID = $("#cbo_company_name").val();
        var buyerID = $("#cbo_buyer_name").val();
        var txt_style_ref = $("#txt_style_ref").val();
        var page_link='requires/woven_style_order_status_report_controller.php?action=search_popup&companyID='+companyID+'&buyerID='+buyerID+'&txt_style_ref='+txt_style_ref;
        var title='Season Search';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=350px,center=1,resize=1,scrolling=0','../../');
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var hide_season=this.contentDoc.getElementById("hide_season").value;
			var hide_season_id=this.contentDoc.getElementById("hide_season_id").value;

            $('#txt_season').val(hide_season);
			$('#txt_season_id').val(hide_season_id);
        }
    }
</script>
</head>
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
    <form id="orderStatusReport" name="orderStatusReport">
        <? echo load_freeze_divs ("../../../"); ?>
        <h3 align="left" id="accordion_h1" style="width:1420px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel">
            <fieldset style="width:1410px;">
                <table class="rpt_table" width="1410" cellpadding="0" cellspacing="0" align="center" rules="all">
                    <thead>
                    <th class="must_entry_caption" width="130">Company Name</th>
                    <th width="130">Buyer Name</th>
                  
                    <th width="100" class="must_entry_caption">Type</th>
                    <th width="50">Job Year</th>
                    <th  width="100">Job No</th>
                    <th  width="120">Order No</th>
                    
                    <th width="170" class="must_entry_caption">Shipment Date</th>
                    <th width="170" class="must_entry_caption">Ex-factory Date</th>
                    <th><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:70px" onClick="reset_form('orderStatusReport','report_container*report_container2','','','')" /></th>
                    </thead>
                    <tr class="general">
                        <td>
                            <?
                            echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/woven_style_order_status_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" ); ?></td>
                        
                        <td>
                            <?
                            $search_style_arr=array(1=>"Order Wise");//,2=>"Style/Job Wise"
                            echo create_drop_down( "cbo_search_type", 100, $search_style_arr,"", 0,"", 1, "fn_order_disable(this.value);",0,"" );
                            ?></td>
                        <td><? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --",0 , "",0,"" );//date("Y",time()) ?>	</td>
                        <td align="center">
                            <input style="width:100px;" name="txt_style_ref" id="txt_style_ref" onDblClick="openmypage_style()" class="text_boxes" placeholder="Browse" readonly/>
                            <input type="hidden" name="txt_style_ref_id" id="txt_style_ref_id"/>    <input type="hidden" name="txt_style_ref_no" id="txt_style_ref_no"/>
                        </td>
                        <td align="center">
                            <input style="width:120px;" name="txt_order" id="txt_order" onDblClick="openmypage_order()" class="text_boxes" placeholder="Browse" readonly />
                            <input type="hidden" name="txt_order_id" id="txt_order_id"/> <input type="hidden" name="txt_order_id_no" id="txt_order_id_no"/>
                        </td>
                       
                        <td>
                            <input type="text" id="txt_date_from" name="txt_date_from" class="datepicker" style="width:60px" onChange="fn_date_chack(1)" readonly>To
                            <input type="text" id="txt_date_to" name="txt_date_to" class="datepicker" style="width:60px" readonly>
                        </td>
                        <td>
                            <input type="text" id="txt_ex_date_form" name="txt_ex_date_form" class="datepicker" style="width:60px" onChange="fn_date_chack(2)" readonly>To
                            <input type="text" id="txt_ex_date_to" name="txt_ex_date_to" class="datepicker" style="width:60px" readonly>
                        </td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated()" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="9" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
                    </tr>
                </table>
            </fieldset>
        </div>
    </form>
</div>
<div id="report_container" align="center"></div>
<div id="report_container2"></div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

<?
/*-------------------------------------------- Comments
Purpose			: 	This form will show Date Wise Yarn Receive Return Report

Functionality	:
JS Functions	:
Created by		:	Md. Jakir Hosen
Creation date 	: 	11/06/2022
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
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Dyes Chamical Receive Issue","../../../", 1, 1, $unicode,1,1);
?>
<script>
    var permission='<? echo $permission; ?>';
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";


    function  generate_report()
    {
        var txt_challan_no = $("#txt_challan_no").val();
        var cbo_company_name = $("#cbo_company_name").val();
        var txt_date_from = $("#txt_date_from").val();
        var txt_date_to = $("#txt_date_to").val();
        var txt_brand = $("#txt_brand").val();
        var txt_yarn_count = $("#txt_yarn_count").val();
        var txt_yarn_lot = $("#txt_yarn_lot").val();
        var cbo_supplier_name = $("#cbo_supplier_name").val();


        if( form_validation('cbo_company_name','Company Name')==false )
        {
            return;
        }
        if(txt_challan_no == ""){
            if( form_validation('txt_date_from*txt_date_to','Form Date*To Date')==false )
            {
                return;
            }
        }

        var dataString = "&txt_challan_no="+txt_challan_no+"&cbo_company_name="+cbo_company_name+"&txt_brand="+txt_brand+"&txt_yarn_count="+txt_yarn_count+"&cbo_supplier_name="+cbo_supplier_name+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to+"&txt_yarn_lot="+txt_yarn_lot;
        var data="action=generate_report"+dataString;

        freeze_window(5);
        http.open("POST","requires/date_wise_yarn_recv_return_report_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = generate_report_reponse;
    }

    function generate_report_reponse()
    {
        if(http.readyState == 4)
        {
            var reponse=trim(http.responseText).split("**");

            $("#report_container2").html(reponse[0]);
            document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
            //document.getElementById('report_container').innerHTML=report_convert_button('../../../');
            release_freezing();
            show_msg('3');
        }
    }

    function new_window()
    {

        document.getElementById('scroll_body').style.overflow="auto";
        document.getElementById('scroll_body').style.maxHeight="none";

        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
            '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
        d.close();
        document.getElementById('scroll_body').style.overflow="auto";
        document.getElementById('scroll_body').style.maxHeight="250px";
    }
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../",$permission);  ?><br />
    <form name="item_receive_issue_1" id="item_receive_issue_1" autocomplete="off" >
        <h3 align="left" id="accordion_h1" style="width:1080px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div style="width:1080px;" align="center" id="content_search_panel">
            <fieldset style="width:1080px;">
                <table class="rpt_table" width="1080" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                    <tr>
                        <th width="140" class="must_entry_caption">Company</th>
                        <th width="130" >Supplier</th>
                        <th width="120" >Challan No.</th>
                        <th width="120" >Brand</th>
                        <th width="130" >Yarn Count</th>
                        <th width="120">Yarn Lot</th>
                        <th width="185" id="up_tr_date" class="must_entry_caption">Date Range</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('item_receive_issue_1','report_container2','','','','');" /></th>
                    </tr>
                    </thead>
                    <tr class="general">
                        <td>
                            <?
                            echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/date_wise_yarn_recv_return_report_controller', this.value, 'load_drop_down_supplier', 'supplier_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/date_wise_yarn_recv_return_report_controller' );" );
                            ?>
                        </td>
                        <td id="supplier_td">
                            <?
                            echo create_drop_down( "cbo_supplier_name", 130, $blank_array, "", 0, "", 0, "");
                            ?>
                        </td>
                        <td>
                            <input type="text" id="txt_challan_no" name="txt_challan_no" class="text_boxes" style="width:120px" placeholder="Write">
                        </td>
                        <td>
                            <input type="text" id="txt_brand" name="txt_brand" class="text_boxes" style="width:120px" placeholder="Write">
                        </td>
                        <td>
                            <input type="text" id="txt_yarn_count" name="txt_yarn_count" class="text_boxes" style="width:130px" placeholder="Write">
                        </td>
                        <td>
                            <input type="text" id="txt_yarn_lot" name="txt_yarn_lot" class="text_boxes" style="width:120px" placeholder="Write">
                        </td>
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px;" placeholder="From Date" readonly/> TO
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" placeholder="To Date" style="width:70px;" readonly/>
                        </td>
                        <td>
                            <input type="button" name="show" id="show" value="Show" onClick="generate_report()" style="width:70px" class="formbutton" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8" align="center" valign="center"><? echo load_month_buttons(1);  ?></td>
                    </tr>

                </table>
            </fieldset>

        </div>
        <!-- Result Contain Start-------------------------------------------------------------------->
        <div id="report_container" align="center" style="margin-top: 7px; margin-bottom: 7px;"></div>
        <div id="report_container2"></div>
        <!-- Result Contain END-------------------------------------------------------------------->


    </form>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
    set_multiselect('cbo_supplier_name','0','0','','0');
</script>
</html>

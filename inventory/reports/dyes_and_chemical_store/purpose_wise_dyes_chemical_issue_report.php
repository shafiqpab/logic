<?
/*-------------------------------------------- Comments
Purpose			: 	This form will show Purpose Wise Dyes N Chemical Issue Report

Functionality	:
JS Functions	:
Created by		:	Md. Jakir Hosen
Creation date 	: 	08/06/2022
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



    function  generate_report(type)
    {
        var cbo_item_cat = $("#cbo_item_cat").val();
        var cbo_company_name = $("#cbo_company_name").val();
        var txt_date_from = $("#txt_date_from").val();
        var txt_date_to = $("#txt_date_to").val();
        var cbo_based_on = $("#cbo_based_on").val();
        var cbo_purpose = $("#cbo_purpose").val();
        var cbo_store_name = $("#cbo_store_name").val();
        var cbo_location_name = $("#cbo_location_name").val();
        var cbo_party_name = $("#cbo_party_name").val();


        if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Form Date*To Date')==false )
        {
            return;
        }

        var dataString = "&cbo_item_cat="+cbo_item_cat+"&cbo_company_name="+cbo_company_name+"&cbo_location_name="+cbo_location_name+"&cbo_store_name="+cbo_store_name+"&cbo_party_name="+cbo_party_name+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to+"&cbo_based_on="+cbo_based_on+"&cbo_purpose="+cbo_purpose;
        if(type == 1){
            var data="action=generate_report"+dataString;
        }else if(type == 2){
            var data="action=generate_report_purpose_wise"+dataString;
        }
        freeze_window(5);
        http.open("POST","requires/purpose_wise_dyes_chemical_issue_report_controller.php",true);
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
            document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+reponse[3]+')" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
            //document.getElementById('report_container').innerHTML=report_convert_button('../../../');
            append_report_checkbox('table_header_1',1);
            if(reponse[3]==1)
            {
                // var tableFilters =
                //     {
                //         col_1: "none",
                //         col_operation: {
                //             id: ["value_total_receive","value_total_order_amt","value_total_issue","value_total_amount"],
                //             col: [21,22,23,25],
                //             operation: ["sum","sum","sum","sum"],
                //             write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
                //         }
                //     }
                setFilterGrid("table_body",-1,);
            }else if(reponse[3]==2)
            {
                setFilterGrid("table_body",-1,);
                setFilterGrid("table_body1",-1);
            }

            release_freezing();
            show_msg('3');
        }
    }

    function new_window(type = 0)
    {

        document.getElementById('scroll_body').style.overflow="auto";
        document.getElementById('scroll_body').style.maxHeight="none";
        if(type == 2){
            document.getElementById('scroll_body1').style.overflow="auto";
            document.getElementById('scroll_body1').style.maxHeight="none";
            $('#table_body1 tr:first').hide();
        }
        $('#table_body tr:first').hide();
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
            '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
        d.close();
        document.getElementById('scroll_body').style.overflow="auto";
        document.getElementById('scroll_body').style.maxHeight="250px";
        if(type == 2) {
            document.getElementById('scroll_body1').style.overflow = "auto";
            document.getElementById('scroll_body1').style.maxHeight = "250px";
            $('#table_body1 tr:first').show();
        }
        $('#table_body tr:first').show();
    }


    function fn_change_base(str)
    {
        if(str==1)
        {
            $("#up_tr_date").html("");
            $("#up_tr_date").html("Transaction Date Range").attr('style','color:blue');
        }
        else
        {
            $("#up_tr_date").html("");
            $("#up_tr_date").html("Insert Date Range").attr('style','color:blue');
        }
    }

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../",$permission);  ?><br />
    <form name="item_receive_issue_1" id="item_receive_issue_1" autocomplete="off" >
        <h3 align="left" id="accordion_h1" style="width:1120px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div style="width:1120px;" align="center" id="content_search_panel">
            <fieldset style="width:1120px;">
                <table class="rpt_table" width="1120" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr>
                            <th width="140" class="must_entry_caption">Company</th>
                            <th width="120" >Item Category</th>
                            <th width="110" >Location</th>
                            <th width="110" >Store</th>
                            <th width="120" >Supplier/Party</th>
                            <th width="120">Issue Purpose</th>
                            <th width="100">Based On</th>
                            <th width="190" id="up_tr_date" class="must_entry_caption">Transaction Date Range</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('item_receive_issue_1','report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td>
                            <?
                            echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/purpose_wise_dyes_chemical_issue_report_controller', this.value, 'load_drop_down_store', 'store_td' );load_drop_down( 'requires/purpose_wise_dyes_chemical_issue_report_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/purpose_wise_dyes_chemical_issue_report_controller', this.value, 'load_drop_down_loan_party', 'party_td' );" );
                            ?>
                        </td>
                        <td>
                            <?
                            echo create_drop_down( "cbo_item_cat", 120, $item_category,"", 1, "-- ALL --", $selected, "",0,"5,6,7,23" );
                            ?>
                        </td>
                        <td id="location_td">
                            <?
                            echo create_drop_down( "cbo_location_name", 110, $blank_array,"", 1, "-- ALL --", 0, "");
                            ?>
                        </td>
                        <td id="store_td">
                            <?
                            echo create_drop_down( "cbo_store_name", 110, $blank_array,"", 1, "-- ALL --", 0, "");
                            ?>
                        </td>
                        <td id="party_td">
                            <?
                            echo create_drop_down( "cbo_party_name", 120, $blank_array,"", 1, "-- ALL --", 0, "");
                            ?>
                        </td>
                        <td>
                            <?
                            //$purpose_sql="";
                            echo create_drop_down( "cbo_purpose", 120, $general_issue_purpose,"", 1, "--Select Purpose--", 0, "",0 );
                            ?>
                        </td>
                        <td >
                            <?
                            $base_on_arr=array(1=>"Transaction Date",2=>"Insert Date");
                            echo create_drop_down( "cbo_based_on", 100, $base_on_arr,"", 0, "", 1, "fn_change_base(this.value);",0 );
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px;" placeholder="From Date" readonly/> TO
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" placeholder="To Date" style="width:70px;" readonly/>
                        </td>
                        <td>
                            <input type="button" name="show" id="show" value="Show" onClick="generate_report(1)" style="width:70px" class="formbutton" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8" align="center" valign="center"><? echo load_month_buttons(1);  ?></td>
                        <td align="center" style="padding: 5px;">
                            <input type="button" name="purposewise" id="purposewise" value="Purpose Wise" onClick="generate_report(2)" style="width:90px" class="formbutton" />
                        </td>
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
<!--<script>
	set_multiselect('cbo_source','0','0','','0');
</script>
-->
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

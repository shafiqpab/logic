<?
/* -------------------------------------------- Comments
  Purpose		: 	This form will create Lot Wise Yarn Transection Report

  Functionality	:
  JS Functions	:
  Created by                :	
  Creation date             : 	04-06-2018
  Updated by                :
  Update date               :
  QC Performed BY           :
  QC Date                   :
  Comments                  :
 */

session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
    header("location:login.php");

require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Date And Supplier Wise Yarn Statement Report", "../../../", 1, 1, $unicode, 1, 1);
?>	
<script>
    var permission = '<? echo $permission; ?>';
    if ($('#index_page', window.parent.document).val() != 1)
        window.location.href = "../logout.php";



    function generate_report(operation)
    {
        if (form_validation('cbo_supplier_name', 'Supplier Name') == false)
        {
            return;
        }
        var cbo_company_name = $("#cbo_company_name").val();
        var cbo_supplier_name = $("#cbo_supplier_name").val();
        var from_date = $("#txt_date_from").val();
        var to_date = $("#txt_date_to").val();

        var dataString = "&cbo_company_name=" + cbo_company_name + "&cbo_supplier_name=" + cbo_supplier_name + "&from_date=" + from_date + "&to_date=" + to_date + '&rpt_type=' + operation;
        var data = "action=generate_report" + dataString;
        freeze_window();
        http.open("POST", "requires/date_and_supplier_wise_yarn_statement_controller.php", true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = generate_report_reponse;
    }

    function generate_report_reponse()
    {
        if (http.readyState == 4)
        {
            var reponse = trim(http.responseText).split("**");
            $("#report_container2").html(reponse[0]);
            document.getElementById('report_container').innerHTML = '<a href="requires/' + reponse[1] + '" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
            show_msg('3');
            release_freezing();
        }
    }

    function new_window()
    {

        document.getElementById('scroll_body').style.overflow = "auto";
        document.getElementById('scroll_body').style.maxHeight = "none";
        $(".scroll_body").css("overflow","auto");
        $(".scroll_body").css("maxHeight","none");
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>' + document.getElementById('report_container2').innerHTML + '</body</html>');
        d.close();
        document.getElementById('scroll_body').style.overflow = "auto";
        document.getElementById('scroll_body').style.maxHeight = "250px";
        $(".scroll_body").css("overflow","auto");
        $(".scroll_body").css("maxHeight","250px");
    }

    function change_color(v_id, e_color)
    {
        if (document.getElementById(v_id).bgColor == "#33CC00")
        {
            document.getElementById(v_id).bgColor = e_color;
        } else
        {
            document.getElementById(v_id).bgColor = "#33CC00";
        }
    }

    function reset_field() {
        $("#cbo_company_name").val("");
        $("#cbo_supplier_name").val("");

        $("#txt_date_from").val("");
        $("#txt_date_to").val("");
    }



</script>
</head>

<body onLoad="set_hotkey()">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs("../../../", $permission); ?>   		 
        <form name="stock_ledger_1" id="stock_ledger_1" autocomplete="off" > 
            <h3 style="width:750px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id, 'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel" style="width:100%;" align="center">
                <fieldset style="width:740px;">
                    <legend>Search Panel</legend> 
                    <table class="rpt_table" width="750" cellpadding="0" cellspacing="0">
                        <thead>
                            <tr>                   
                                <th width="150" class="must_entry_caption">Supplier Name</th>
                                <th width="150">Company</th>  
                                <th>Date Range</th>
                                <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_field()" /></th>
                                
                            </tr>
                        </thead>
                        <tr class="general">
                            <td align="center" id="supplier_td">
                                <?
                                echo create_drop_down("cbo_supplier_name", 140, "select s.id, s.supplier_name from LIB_SUPPLIER s, LIB_SUPPLIER_PARTY_TYPE sp, LIB_SUPPLIER_TAG_COMPANY sc where s.id = sp.supplier_id and s.id = sc.supplier_id and sp.party_type = 2 and s.is_deleted = 0 and s.status_active = 1 group by s.id, s.supplier_name order by s.supplier_name", "id,supplier_name", 0, "-- Select Supplier --", 0, "");

                                ?>          
                            </td>
                            <td>
                                <?
                                echo create_drop_down("cbo_company_name", 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name", "id,company_name", 0, "-- Select Company --", $selected, "");
                                ?>                            
                            </td>
                            
                            <td align="center">
                                <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:80px"/>                    							
                                To
                                <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:80px"/>                                                        
                            </td>
                            <td>
                                <input type="button" name="search" id="search" value="Supplier Wise" onClick="generate_report(1)" style="width:70px" class="formbutton" />
                                <input type="button" name="search" id="search" value="Count Wise" onClick="generate_report(2)" style="width:70px" class="formbutton" />
                                <input type="button" name="search" id="search" value="Summury" onClick="generate_report(3)" style="width:70px" class="formbutton" />
                            </td>
                            
                        </tr>
                        <tr>
                            <td colspan="6"><? echo load_month_buttons(1); ?></td>
                        </tr>
                    </table> 
                </fieldset> 
            </div>
            <br /> 
            <div id="report_container" align="center"></div>
            <div id="report_container2"></div> 
        </form>    
    </div>    
</body>  
<script>
    set_multiselect('cbo_company_name*cbo_supplier_name','0*0','0*0','','0*0');
    /*$("#multiselect_dropdown_table_headercbo_company_name a").click(function(){
        load_supplier();
    });

    function load_supplier()
    {
        var company=$("#cbo_company_name").val();
        load_drop_down( 'requires/date_and_supplier_wise_yarn_statement_controller', company, 'load_drop_down_supplier', 'supplier_td' );
        get_php_form_data( company, 'eval_multi_select', 'requires/date_and_supplier_wise_yarn_statement_controller' );
    }*/
    
</script> 
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>

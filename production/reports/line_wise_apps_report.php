<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Production QC Report.
Functionality	:
JS Functions	:
Created by		:	Arnab
Creation date 	: 	11-08-2023
Updated by 		:
Update date		:
QC Performed BY	:
QC Date			:
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Production QC Report", "../../", 1, 1,$unicode,'','');

?>
<script src="../../Chart.js-master/Chart.js"></script>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission = '<? echo $permission; ?>';


function fn_report_generated(type)
{
       var company = document.getElementById('cbo_company_name').value;

       if($("#txt_job_no").val()=="")
       {
            if( form_validation('cbo_company_name*txt_date','Company Name*Date Range')==false )
            {
                return;
            }

        }

        if ((company==0 || company==''))
        {
            alert('please select Company Or Working Company');
            return;
        }
        var report_title=$( "div.form_caption" ).html();

        var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_location*txt_date',"../../")+'&report_title='+report_title+'&type='+type;
        freeze_window(3);
        http.open("POST","requires/line_wise_apps_report_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fn_report_generated_reponse;

}

function fn_report_generated_reponse()
{
    if(http.readyState == 4)
    {
        var reponse=trim(http.responseText).split("**");
        $('#report_container2').html(reponse[0]);
        document.getElementById('report_container').innerHTML='<a href="##" onclick="fnExportToExcel()" target=_blank; style="text-decoration:none" id="dlink"><input type="button" class="formbutton" value="Export to Excel" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
        setFilterGrid("table_body_id",-1);
        show_msg('3');
        release_freezing();
    }
}

function fnExportToExcel()
{
    // $(".fltrow").hide();
    let tableData = document.getElementById("report_container2").innerHTML;
    // alert(tableData);
    let data_type = 'data:application/vnd.ms-excel;base64,',
    template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table border="2px">{table}</table></body></html>',
    base64 = function (s) {
        return window.btoa(unescape(encodeURIComponent(s)))
    },
    format = function (s, c) {
        return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; })
    }

    let ctx = {
        worksheet: 'Worksheet',
        table: tableData
    }

    let dt = new Date();
    document.getElementById("dlink").href = data_type + base64(format(template, ctx));
    document.getElementById("dlink").traget = "_blank";
    document.getElementById("dlink").download = dt.getTime()+'_display_board.xls';
    document.getElementById("dlink").click();
    // $(".fltrow").show();
    // alert('ok');
}

function new_window()
{
    document.getElementById('report_container2').style.overflow="auto";
    document.getElementById('report_container2').style.maxHeight="none";

    var w = window.open("Surprise", "#");
    var d = w.document.open();
    d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
    '<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
    d.close();

    document.getElementById('report_container2').style.overflowY="auto";
    document.getElementById('report_container2').style.maxHeight="400px";
}

function change_color(v_id,e_color)
{
    if (document.getElementById(v_id).bgColor=="#33CC00")
    {
        document.getElementById(v_id).bgColor=e_color;
    }
    else
    {
        document.getElementById(v_id).bgColor="#33CC00";
    }
}

function fn_disable_com(str){
    if(str==2){$("#cbo_company_name").attr('disabled','disabled');}
    else{ $('#cbo_company_name').removeAttr("disabled");}

}

</script>
</head>
<body onLoad="set_hotkey();">
<form id="ColorSizeReport_1">
    <div style="width:680px; margin:1px auto;">
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:680px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
         <div id="content_search_panel" style="width:100%" >
         <fieldset style="width:680px;">
            <table class="rpt_table" width="680" cellpadding="0" cellspacing="0" align="center" rules="all" border="1">
               <thead>
                    <tr>
                        <th width="120" class="must_entry_caption">Company Name</th>
                        <th width="120">Location</th>
                        <th width="70" class="must_entry_caption">Date Range</th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:90px" value="Reset" onClick="fn_disable_com(0)"/></th>
                    </tr>
                 </thead>
                <tbody>
                <tr class="general">
                    <td width="120" align="center">
                    <?
                        echo create_drop_down( "cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/line_wise_apps_report_controller', this.value, 'load_drop_down_location', 'location_td' );" );
	                 ?>
                    </td>
                    <td width="120" id="location_td" align="center">
                        <?
                             echo create_drop_down( "cbo_location", 120, $blank_array,"", 1, "-- Select --", $selected, "",1,"" );
                        ?>
                    </td>
                    <td>
                       <input type="text" name="txt_date" id="txt_date" class="datepicker" style="width:70px" />
                    </td>
                    <td width="70">
                        <input type="button" id="show_button" class="formbutton" style="width:50px" value="Show" onClick="fn_report_generated(1)" />
                    </td>
                </tr>
                </tbody>
            </table>
            </table>
            <br />
        </fieldset>
    </div>
    </div>

    <div id="report_container" align="center"></div>
    <div id="report_container2" align="center"></div>
 </form>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
</script>
</html>

<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Production QC Report.
Functionality	:
JS Functions	:
Created by		:	Arnab
Creation date 	: 	21-11-2023
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


function open_job_no()
{
    var buyer_name=$("#cbo_buyer_name").val();
    var cbo_year=$("#cbo_year").val();
    var page_link='requires/order_wise_cutting_status_report_controller.php?action=job_popup&buyer_name='+buyer_name+'&cbo_year='+cbo_year;
    var title="Search Job No Popup";
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=390px,center=1,resize=0,scrolling=0','../')
    emailwindow.onclose=function()
    {
        var theform=this.contentDoc.forms[0];
        var job_id=this.contentDoc.getElementById("hide_job_id").value;
        var job_no=this.contentDoc.getElementById("hide_job_no").value;

        $("#txt_job_no").val(job_no);
        $("#txt_job_id").val(job_id);
    }
}
function open_order_no()
{
	if( form_validation('txt_color','Color')==false)
	{
		return;
	}
    var txt_style_no = $("#txt_style_no").val();
    var txt_style_id = $("#txt_style_id").val();

	var page_link='requires/order_wise_cutting_status_report_controller.php?action=order_wise_search&txt_style_no='+txt_style_no+'&txt_style_id='+txt_style_id;
	var title="Search Order Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=560px,height=370px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
			var theform=this.contentDoc.forms[0];
			var prodID=this.contentDoc.getElementById("txt_selected_id").value;
			//alert(prodID); // product ID
			var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
			$("#txt_order_no").val(prodDescription);
			$("#hidden_order_id").val(prodID);
	}
}
function open_style_ref()
{
    if( form_validation('cbo_company_name','Company Name')==false )
    {
        return;
    }
    var company = $("#cbo_company_name").val();
    var buyer=$("#cbo_buyer_name").val();
    var job_no=$("#txt_job_no").val();
    var job_id=$("#hidden_job_id").val();
    var cbo_year=$("#cbo_year").val();
    var page_link='requires/order_wise_cutting_status_report_controller.php?action=style_wise_search&company='+company+'&buyer='+buyer+'&job_no='+job_no+'&job_id='+job_id+'&cbo_year='+cbo_year;
    var title="Search Style Popup";
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=390px,center=1,resize=0,scrolling=0','../')
    emailwindow.onclose=function()
    {
        var theform=this.contentDoc.forms[0];
        var style_data=this.contentDoc.getElementById("selected_id").value;
        var style_data=style_data.split("_");
        var style_id=style_data[0];
        var style_no=style_data[1];

        $("#txt_style_no").val(style_no);
        $("#txt_style_id").val(style_id);
    }
}

function openmypage_color()
{
    if( form_validation('txt_style_no','Style No')==false )
    {
        return;
    }
    var txt_style_no = $("#txt_style_no").val();
    var txt_style_id = $("#txt_style_id").val();
    var page_link='requires/order_wise_cutting_status_report_controller.php?action=color_popup&txt_style_no='+txt_style_no+'&txt_style_id='+txt_style_id;
    var title = "Color Name";
    emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=320px,height=350px,center=1,resize=0,scrolling=0', '../')
    emailwindow.onclose = function ()
    {
        var theemail = this.contentDoc.getElementById("selected_id").value;
        var split_value = theemail.split('_');

        document.getElementById('hdn_color').value = split_value[0];
        document.getElementById('txt_color').value = split_value[1];
        release_freezing();
    }
}
function openmypage_cutting_lay(po_id,job_id,color_id,action,width,height)
{
    var popup_width=width;
    var popup_height=height;
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_cutting_status_report_controller.php?&po_id='+po_id+'&job_id='+job_id+'&color_id='+color_id+'&action='+action, 'Cutting Popup', 'width='+popup_width+','+'height='+popup_height+',center=1,resize=0,scrolling=0','../');

}

function fn_report_generated(type)
{
       var company = document.getElementById('cbo_company_name').value;

       if($("#txt_job_no").val()==""  && $("#txt_order_no").val()==""  && $("#txt_style_no").val()=="")
       {
            if( form_validation('cbo_company_name','Company Name')==false )
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

        var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*txt_order_no*txt_style_no*txt_color*hdn_color*txt_job_id',"../../")+'&report_title='+report_title+'&type='+type;
        freeze_window(3);
        http.open("POST","requires/order_wise_cutting_status_report_controller.php",true);
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
    if(str==1){$("#cbo_working_company_id").attr('disabled','disabled');}
    else{ $('#cbo_working_company_id').removeAttr("disabled");}
}
</script>
</head>
<body onLoad="set_hotkey();">
<form id="sewingQcReport_1">
    <div style="width:770px; margin:1px auto;">
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:770px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
         <div id="content_search_panel" style="width:100%" >
         <fieldset style="width:770px;">
            <table class="rpt_table" width="770" cellpadding="0" cellspacing="0" align="center" rules="all" border="1">
               <thead>
                    <tr>
                        <th width="120" class="must_entry_caption">Company Name</th>
                        <th width="70">Job Year</th>
                        <th width="80">Buyer Name</th>
                        <th width="70">Job No</th>
                        <th width="70">Style Ref.</th>
                        <th width="70">Color</th>
                        <th width="70">Order No</th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:90px" value="Reset" onClick="fn_disable_com(0)"/></th>
                    </tr>
                 </thead>
                <tbody>
                <tr class="general">
                    <td width="120" align="center">
                    <?
                        echo create_drop_down( "cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/order_wise_cutting_status_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/order_wise_cutting_status_report_controller', this.value, 'load_drop_down_location', 'location_td' );" );
	                 ?>
                    </td>
                    <td width="70"  align="center">
                    	<?
                         echo create_drop_down( "cbo_year", 70, $year,"", 1, "Year--", 0, "",0 );
                        ?>
                    </td>
                    <td width="80" id="buyer_td" align="center">
                    <?
                        echo create_drop_down( "cbo_buyer_name", 80, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" );
                    ?>
                    </td>
                    <td width="70">
                    	<input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:80px" onDblClick="open_job_no()"  placeholder="Browse/Write">
                        <input type="hidden" name="txt_job_id" id="txt_job_id" value="">

                    </td>

                    <td width="70">
                    	<input type="text" name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:80px" onDblClick="open_style_ref()" placeholder="Browse/Write">
                        <input type="hidden" name="txt_style_id" id="txt_style_id" value="">
                    </td>
                    <td width="70">
                       <input type="text" id="txt_color" name="txt_color" class="text_boxes" style="width:80px" onDblClick="openmypage_color();" placeholder="Browse" />
				    	<input type="hidden" id="hdn_color" name="hdn_color"/>
                    </td>
                    <td width="70">
                    	<input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:80px"  onDblClick="open_order_no()" placeholder="Browse/Write">
                    </td>

                    <td width="70">
                        <input type="button" id="show_button" class="formbutton" style="width:50px" value="Show" onClick="fn_report_generated(1)" />

                    </td>
                </tr>
                </tbody>
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
</html>
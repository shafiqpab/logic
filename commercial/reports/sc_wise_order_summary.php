<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create SC wise report summary
				
Functionality	:	
JS Functions	:
Created by		:	Safa
Creation date 	: 	07-02-2023
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
echo load_html_head_contents("SC Wise Report Summaryt","../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

function openmypage(company_name,lc_number,ship_date,supplier_id,lc_date,exp_date,payterm,pi_id,action,title)
{
    var popup_width="";
    if(action=="pi_details") popup_width="900px"; else popup_width="850px";
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sc_wise_order_summary_controller.php?company_name='+company_name+'&lc_number='+lc_number+'&ship_date='+ship_date+'&supplier_id='+supplier_id+'&lc_date='+lc_date+'&exp_date='+exp_date+'&payterm='+payterm+'&pi_id='+pi_id+'&action='+action, title, 'width='+popup_width+',height=390px,center=1,resize=0,scrolling=0','../');
}	

function generate_report(operation)
{
    var company_id=$("#cbo_company_id").val();
    if(form_validation('cbo_company_id','Company Name')==false)
    {
        release_freezing();
        return;
    }
    else
    {	
        var report_title=$( "div.form_caption" ).html();
        var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_name*cbo_year*txt_sc_no*txt_lc_no*txt_order_no*style_ref_no*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title+'&report_type='+operation;
        // alert(data);return;
        freeze_window(3);
        http.open("POST","requires/sc_wise_order_summary_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fn_report_generated_reponse;
    }
}

function fn_report_generated_reponse()
{
    if(http.readyState == 4) 
    {
        var reponse=trim(http.responseText).split("****");
        // var tot_rows=reponse[2];
        $('#report_container2').html(reponse[0]);
        //alert(reponse[0]);return;
        //document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
        document.getElementById('report_container').innerHTML='<a href="##" onclick="fnExportToExcel()" target=_blank; style="text-decoration:none" id="dlink"><input type="button" class="formbutton" value="Export to Excel" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
        //setFilterGrid("tbl_marginlc_list",-1,tableFilters);
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
    // document.getElementById('scroll_body').style.overflow="auto";
    // document.getElementById('scroll_body').style.maxHeight="none";
    // $('#table_body tr:first').hide();
    var w = window.open("Surprise", "#");
    var d = w.document.open();
    d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
    '<html><head><title></title><link rel="stylesheet" href="../../css/style_print.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
    d.close();
    // $('#table_body tr:first').show();
    // document.getElementById('scroll_body').style.overflowY="scroll";
    // document.getElementById('scroll_body').style.maxHeight="225px";
}

function openmypage_job()
{
    // if( form_validation('cbo_company_id','Company Name')==false )
    // {
    // 	return;
    // }

    var companyID = $("#cbo_company_id").val();
    var buyer_name = $("#cbo_buyer_name").val();
    var cbo_year_id = $("#cbo_year").val();
    var cbo_month_id = $("#cbo_month").val();

    var page_link='requires/sc_wise_order_summary_controller.php?action=sc_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
    var title='Sc No Search';
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=1,scrolling=0','../../');
    emailwindow.onclose=function()
    {
        var theform=this.contentDoc.forms[0];
        var sc_no=this.contentDoc.getElementById("hide_sc_no").value;
        var sc_id=this.contentDoc.getElementById("hide_sc_id").value;
        $('#txt_sc_no').val(sc_no);
        $('#txt_sc_id').val(sc_id);
    }
}
  
function openmypage_job_lc()
{
    var companyID = $("#cbo_company_id").val();
    var buyer_name = $("#cbo_buyer_name").val();
    var cbo_year_id = $("#cbo_year").val();
    var cbo_month_id = $("#cbo_month").val();

    var page_link='requires/sc_wise_order_summary_controller.php?action=lc_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
    var title='Lc No Search';
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=1,scrolling=0','../../');
    emailwindow.onclose=function()
    {
        var theform=this.contentDoc.forms[0];
        var sc_no=this.contentDoc.getElementById("hide_lc_no").value;
        var sc_id=this.contentDoc.getElementById("hide_lc_id").value;
        $('#txt_lc_no').val(sc_no);
        $('#txt_lc_id').val(sc_id);
    }
}

function openmypage_job_order()
{
    var companyID = $("#cbo_company_id").val();
    var buyer_name = $("#cbo_buyer_name").val();
    var cbo_year_id = $("#cbo_year").val();
    var cbo_month_id = $("#cbo_month").val();

    var page_link='requires/sc_wise_order_summary_controller.php?action=order_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
    var title='Order No Search';
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=1,scrolling=0','../../');
    emailwindow.onclose=function()
    {
        var theform=this.contentDoc.forms[0];
        var sc_no=this.contentDoc.getElementById("hide_order_no").value;
        var sc_id=this.contentDoc.getElementById("hide_order_id").value;
        $('#txt_order_no').val(sc_no);
        $('#txt_order_id').val(sc_id);
    }
}

function openmypage_job_style()
{
    var companyID = $("#cbo_company_id").val();
    var buyer_name = $("#cbo_buyer_name").val();
    var cbo_year_id = $("#cbo_year").val();
    var cbo_month_id = $("#cbo_month").val();

    var page_link='requires/sc_wise_order_summary_controller.php?action=style_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
    var title='Job No Search';
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=1,scrolling=0','../../');
    emailwindow.onclose=function()
    {
        var theform=this.contentDoc.forms[0];
        var sc_no=this.contentDoc.getElementById("style_ref_no").value;
        var sc_id=this.contentDoc.getElementById("style_ref_id").value;
        $('#style_ref_no').val(sc_no);
        $('#style_ref_id').val(sc_id);
    }
}

</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?>   		 
        <form name="marginlcregister_1" id="marginlcregister_1" autocomplete="off" > 
         <h3 style="width:1050px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:1050px" >      
            <fieldset>  
                <table class="rpt_table" width="1050" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <th width="120" class="must_entry_caption">Company Name</th>
                        <th width="120">Buyer Name</th>
                        <th width="80">Year</th>
                        <th width="120">SC</th>
                        <th width="120">Lc</th>
                        <th width="120">Order</th>
                        <th width="120">Style</th>
                        <th>Shipment Date</th>
                        <th width="70"><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" onClick="reset_form('marginlcregister_1','report_container*report_container2','','','')" /></th>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td align="center">
                                <? 
                                    echo create_drop_down( "cbo_company_id", 120, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and comp.core_business in(1,3) $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/sc_wise_order_summary_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                                ?>                            
                           </td>
                            <td align="center" id="buyer_td">
                        	    <? 
                                    echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "---- Select ----", 0, "" ); 
                                ?>               
                           </td>
                           <td align="center">
								<?
								    echo create_drop_down( "cbo_year", 80, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); 
                                ?> 
                          </td>
                            <td align="center">
                                <input type="text" id="txt_sc_no" name="txt_sc_no" class="text_boxes" style="width:100px" onDblClick="openmypage_job();" placeholder="Browse/Write" />
								<input type="hidden" id="txt_sc_id" name="txt_sc_id"/>
                             </td>
							<td align="center">
                                <input type="text" id="txt_lc_no" name="txt_lc_no" class="text_boxes" style="width:100px" onDblClick="openmypage_job_lc();" placeholder="Browse/Write" />
								<input type="hidden" id="txt_lc_id" name="txt_lc_id"/>
                           </td>
                           <td align="center">
                                <input type="text" id="txt_order_no" name="txt_order_no" class="text_boxes" style="width:100px" onDblClick="openmypage_job_order();" placeholder="Browse/Write" />
								<input type="hidden" id="txt_order_id" name="txt_order_id"/>
                           </td>
                           <td align="center">
                                <input type="text" id="style_ref_no" name="style_ref_no" class="text_boxes" style="width:100px" onDblClick="openmypage_job_style();" placeholder="Browse/Write" />
								<input type="hidden" id="style_ref_id" name="style_ref_id"/>
                           </td>
                            <td align="center">
                                <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px;"/>                    							
                                To
                                <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px;"/>                        
                            </td>

                            <td>
                                <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:60px" class="formbutton" />
                                <input type="button" name="search" id="search" value="Show 2" onClick="generate_report(2)" style="width:60px" class="formbutton" />
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="13" align="center"><? echo load_month_buttons(1);  ?></td>
                        </tr>
                    </tfoot>
                </table> 
            </fieldset>
        </div>
    <div style="margin-top:10px" id="report_container" align="center"></div>
    <div id="report_container2"></div>
 </form> 
 </div> 
</body> 
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>set_multiselect('cbo_company_id','0','0','','0',"load_drop_down( 'requires/sc_wise_order_summary_controller',$('#cbo_company_id').val(), 'load_drop_down_buyer', 'buyer_td' )");</script>
</html>

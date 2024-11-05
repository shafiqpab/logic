<?
/*-------------------------------------------- Comments -----------------------
Purpose         :   This Form Will Create Dia Wise Knitting Program Summary Report.
Functionality   :   
JS Functions    :
Created by      :   Abdul Barik Tipu 
Creation date   :   06-01-2022
Updated by      :       
Update date     :          
QC Performed BY :       
QC Date         :   
Comments        :
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Dia Wise Knitting Program Summary", "../../", 1, 1,'',1,1);

?>  

<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';
 
function fn_report_generated(type)
{
    if (form_validation('cbo_company_name','Comapny Name')==false)
    {
        return;
    }
    
    var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_year*cbo_buyer_name*txt_file_no*txt_ref_no*txt_order_no*hide_order_id*txt_booking_no*txt_mc_dia*txt_mc_gg*txt_mc_group*txt_date_from*txt_date_to',"../../");
    freeze_window(3);
    http.open("POST","requires/dia_wise_program_report_controller.php",true);
    http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    http.send(data);
    http.onreadystatechange = fn_report_generated_reponse;
}
    

function fn_report_generated_reponse()
{
    if(http.readyState == 4) 
    {
        var response=trim(http.responseText).split("####");
        $('#report_container2').html(response[0]);
        
        document.getElementById('report_container').innerHTML='<a href="'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:155px"/></a>&nbsp;&nbsp;&nbsp;<input type="button" onClick="new_window()" value="Html Preview" name="Print" class="formbutton" style="width:100px"/>'; 

        show_msg('3');
        release_freezing();
    }
    
}
    
function new_window()
{
    var w = window.open("Surprise", "#");
    var d = w.document.open();
    d.write ('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'+
'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
    d.close();
}

function openmypage_order()
{
    if(form_validation('cbo_company_name','Company Name')==false)
    {
        return;
    }
    
    var companyID = $("#cbo_company_name").val();
    var page_link='requires/dia_wise_program_report_controller.php?action=order_no_search_popup&companyID='+companyID;
    var title='Order No Search';
    
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=870px,height=390px,center=1,resize=1,scrolling=0','../');
    emailwindow.onclose=function()
    {
        var theform=this.contentDoc.forms[0];
        var order_no=this.contentDoc.getElementById("hide_order_no").value;
        var order_id=this.contentDoc.getElementById("hide_order_id").value;
        
        $('#txt_order_no').val(order_no);
        $('#hide_order_id').val(order_id);   
    }
}

function openmypage_booking()
{
    if( form_validation('cbo_company_name','Company Name')==false )
    {
        return;
    }
    var companyID = $("#cbo_company_name").val();
    var buyerID = $("#cbo_buyer_name").val();
    var page_link='requires/dia_wise_program_report_controller.php?action=booking_no_search_popup&companyID='+companyID+'&buyerID='+buyerID;
    var title='Booking No Popup';
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=420px,center=1,resize=1,scrolling=0','../');
    emailwindow.onclose=function()
    {
        var theform=this.contentDoc.forms[0];
        var theemail=this.contentDoc.getElementById("selected_booking");
        //var response=theemail.value.split('_');
        if (theemail.value!="")
        {
            freeze_window(5);
            //document.getElementById("txt_booking_id").value=theemail.value;
            document.getElementById("txt_booking_no").value=theemail.value;
            release_freezing();
        }
    }
}

function openmypage_mc_group()
{
    if(form_validation('cbo_company_name','Company Name')==false)
    {
        return;
    }
    
    var companyID = $("#cbo_company_name").val();
    var page_link='requires/dia_wise_program_report_controller.php?action=mc_group_search_popup&companyID='+companyID;
    var title='Machine Group Search';
    
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=870px,height=390px,center=1,resize=1,scrolling=0','../');
    emailwindow.onclose=function()
    {
        var theform=this.contentDoc.forms[0];
        var theemail=this.contentDoc.getElementById("selected_booking");
        var response=theemail.value.split('_');
        if (theemail.value!="")
        {
            freeze_window(5);
            document.getElementById("txt_mc_group").value=response[0];
            document.getElementById("hide_mc_group_id").value=response[1];
            release_freezing();
        }

        /*var theform=this.contentDoc.forms[0];
        var order_no=this.contentDoc.getElementById("hide_order_no").value;
        var order_id=this.contentDoc.getElementById("hide_order_id").value;

        $('#txt_mc_group').val(order_no);
        $('#hide_mc_group_id').val(order_id); */  
    }
}

</script>

</head>
 
<body onLoad="set_hotkey();">

<form id="knittingStatusReport_1">
    <div style="width:100%;" align="center">    
    
        <? echo load_freeze_divs ("../../",'');  ?>
         
         <h3 style="width:1300px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1300px;">
             <table class="rpt_table" width="1300" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>
                    <th class="must_entry_caption">Company Name</th>
                    <th>Year</th>
                    <th>Buyer Name</th>
                    <th>File</th>
                    <th>Ref</th>
                    <th>Order No</th>
                    <th>Booking No</th>
                    <th>M/C Dia</th>
                    <th>M/C Gauge</th>
                    <th>M/C Group</th>
                    <th>Pub. Ship Date</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('knittingStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:100px" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td> 
                            <?
                                echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/dia_wise_program_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td> 
                            <?
                                echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                            ?>
                        </td>
                        <td>
                            <input type="text" id="txt_file_no" name="txt_file_no" class="text_boxes" style="width:80px" placeholder="Write"/>
                        </td>
                        <td>
                            <input type="text" id="txt_ref_no" name="txt_ref_no" class="text_boxes" style="width:80px" placeholder="Write"/>
                        </td>
                        <td>
                            <input type="text" id="txt_order_no" name="txt_order_no" class="text_boxes" style="width:70px" onDblClick="openmypage_order();" placeholder="Browse/Write" />
                            <input type="hidden" id="hide_order_id" name="hide_order_id"/>
                        </td>
                        <td>
                            <input type="text" id="txt_booking_no" name="txt_booking_no" class="text_boxes" style="width:80px" onDblClick="openmypage_booking();" placeholder="Browse/Write" />
                        </td>
                        <td>
                            <input type="text" id="txt_mc_dia" name="txt_mc_dia" class="text_boxes" style="width:80px;" placeholder="Write" />
                        </td>
                        <td>
                            <input type="text" id="txt_mc_gg" name="txt_mc_gg" class="text_boxes" style="width:80px;" placeholder="Write" />
                        </td>
                        <td>
                            <input type="text" id="txt_mc_group" name="txt_mc_group" class="text_boxes" style="width:80px;" placeholder="Write" />
                            <input type="hidden" id="hide_mc_group_id" name="hide_mc_group_id"/>
                            <!-- onDblClick="openmypage_mc_group();" -->
                        </td>
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px;" placeholder="From Date" readonly/>&nbsp;To&nbsp;
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px;" placeholder="To Date" readonly/>                
                        </td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated(1)" />
                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="14">
                            <? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
        </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
 </form>   
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

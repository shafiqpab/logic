<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Machine Dia Wise Fabric Release And Production Report.
Functionality	:	
JS Functions	:
Created by		:	Abdul Barik Tipu 
Creation date 	: 	21-11-2021
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
echo load_html_head_contents("Machine Dia Wise Fabric Release And Production Report", "../../", 1, 1,'',1,1);

?>	

<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';

function openmypage_order()
{
    if(form_validation('cbo_company_name','Company Name')==false)
    {
        return;
    }
    
    var companyID = $("#cbo_company_name").val();
    var year_selection = $("#cbo_year_selection").val();
    var page_link='requires/machine_dia_wise_fabric_release_and_production_report_controller.php?action=order_no_search_popup&companyID='+companyID+'&year_selection='+year_selection;
    var title='Order No Search';
    
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0','../');
    emailwindow.onclose=function()
    {
        var theform=this.contentDoc.forms[0];
        var order_no=this.contentDoc.getElementById("hide_order_no").value;
        var order_id=this.contentDoc.getElementById("hide_order_id").value;
        
        $('#txt_po_no').val(order_no);
        $('#hide_order_id').val(order_id);   
    }
}

function openmypage_booking()
{
    if( form_validation('cbo_company_name','Company Name')==false )
    {
        return;
    }
    var data=document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_year_selection').value;
    //alert (data);
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/machine_dia_wise_fabric_release_and_production_report_controller.php?action=booking_no_search_popup&data='+data,'Booking No Popup', 'width=1050px,height=420px,center=1,resize=0','../')
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

function fn_report_generated()
{
    var file_no = $('#txt_file_no').val();
    var ref_no = $('#txt_ref_no').val();
    var po_no = $('#txt_po_no').val();
    var machine_dia = $('#txt_machine_dia').val();
    var booking_no = $('#txt_booking_no').val();

    if(file_no=="" && ref_no=="" && po_no=="" && machine_dia=="" && booking_no=="")
    {
        if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company*From Date*To Date')==false )
        {
            return;
        }
    }
    else
    {
        if( form_validation('cbo_company_name','Company')==false )
        {
            return;
        }
    }

	var report_title=$( "div.form_caption").html();
	var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_file_no*txt_ref_no*txt_po_no*hide_order_id*txt_booking_no*txt_machine_dia*txt_machine_gauge*cbo_type*cbo_year_selection*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
	
	freeze_window(3);
	http.open("POST","requires/machine_dia_wise_fabric_release_and_production_report_controller.php",true);
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
		document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:135px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Html Preview" name="Print" class="formbutton" style="width:120px"/>'; 
		// setFilterGrid("table_body",-1,tableFilters);
		
		show_msg('3');
		release_freezing();
 	}
	
}

function new_window()
{
    document.getElementById('scroll_body').style.overflow="auto";
    document.getElementById('scroll_body').style.maxHeight="none";
    var w = window.open("Surprise", "#");
    var d = w.document.open();
    d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../css/style_print.css" type="text/css"  /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
    d.close(); 
    document.getElementById('scroll_body').style.overflowY="scroll";
    document.getElementById('scroll_body').style.maxHeight="350px";
}

</script>

</head>
 
<body onLoad="set_hotkey();">
<? echo load_freeze_divs ("../../",'');  ?>
<form id="knittingProductionReport_1">
    <div style="width:100%;" align="center">    
         <h3 style="width:1220px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')"> -Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1090px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th class="must_entry_caption">Company Name</th>
                    <th>Buyer</th>
                    <th>File No</th>
                    <th>Ref No</th>
                    <th>Order No</th>
                    <th>Booking No</th>
                    <th>Machine Dia</th>
                    <th>Machine Gauge</th>
                    <th>Type</th>
                    <th colspan="2">Program Date</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('knittingProductionReport_1','report_container*report_container2','','','')" class="formbutton" style="width:80px" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td> 
                            <?
                                echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company--", $selected, "load_drop_down('requires/machine_dia_wise_fabric_release_and_production_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-- Select Buyer --", 0, "" );
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:80px">
                        </td>
                        <td>
                            <input type="text" name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:80px">
                        </td>
                        <td>
                            <input type="text" name="txt_po_no" id="txt_po_no" class="text_boxes" style="width:80" placeholder="Browse Or Write" onDblClick="openmypage_order();" onChange="$('#hide_order_id').val('');" autocomplete="off">
                            <input type="hidden" name="hide_order_id" id="hide_order_id" readonly>
                        </td>
                        <td>
                            <input type="text" id="txt_booking_no" name="txt_booking_no" class="text_boxes_numeric" style="width:90px" onDblClick="openmypage_booking();" placeholder="Write/Browse Booking"  />
                            <input type="hidden" id="txt_booking_id" name="txt_booking_id"/>
                        </td>
                        <td>
                            <input type="text" name="txt_machine_dia" id="txt_machine_dia" class="text_boxes" style="width:55px" />
                        </td>
                        <td>
                            <input type="text" name="txt_machine_gauge" id="txt_machine_gauge" class="text_boxes" style="width:55px" />
                        </td>
                        <td>
                            <?
                                $search_by_arr=array(0=>"All",1=>"Inside",3=>"Outside");
                                echo create_drop_down( "cbo_type", 102, $search_by_arr,"",0, "", "0","",0 );
                            ?>
                        </td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date">
                        </td>
                        <td>
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px"  placeholder="To Date">
                        </td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:80px" value="Show" onClick="fn_report_generated()" />
                        </td>
                    </tr>
                </tbody>
            </table>
            <table>
            	<tr>
                	<td>
 						<? echo load_month_buttons(1); ?>
                   	</td>
                </tr>
            </table> 
            <br />
        </fieldset>
    	</div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
 </form>   
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

</html>

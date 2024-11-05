<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Knitting Program Wise Grey Fab Report.
Functionality	:	
JS Functions	:
Created by		:	Aziz
Creation date 	: 	22-11-2014
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
echo load_html_head_contents("Knitting Program Wise Report", "../../", 1, 1,'',1,1);
?>	
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';
var tableFilters = 
	{
		col_operation: {
			id: ["value_total_booking_qty","value_total_program_qnty","value_total_grey_qty","value_total_trans_qty_in","value_total_iss_return_qty","value_total_Recv_qty","value_total_knit_issue_qty","value_total_trans_qty_out","value_total_Issue_qty","value_total_stockbalance","value_total_age"],
	   col: [16,18,21,22,23,24,25,26,27,28,29],
	   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
	   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}	
	}
function fn_report_generated(type)
{
	if (form_validation('cbo_company_name','Comapny Name')==false)
	{
		return;
	}
	var report_title=$( "div.form_caption" ).html();

	if(type==1)
	{
		var action ="report_generate";
	}
	else
	{
		var action ="report_generate_gross";
	}

	var data="action="+action+get_submitted_data_string('cbo_company_name*cbo_store_name*cbo_buyer_name*txt_job_no*txt_job_id*txt_order_no*hide_order_id*txt_booking_id*txt_booking_no*txt_internal_ref*txt_program_no*txt_date_from*txt_date_to*cbo_year_selection*cbo_value_with*cbo_get_upto_qnty*txt_qnty',"../../")+'&report_title='+report_title;
	freeze_window(3);
	http.open("POST","requires/knitting_program_wise_grey_fab_stock_report_controller.php",true);
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
		document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
		setFilterGrid("tbl_list_search",-1,tableFilters);
		show_msg('3');
		release_freezing();
 	}
}
function openmypage_order()
{
	if(form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}
	var companyID = $("#cbo_company_name").val();
	var buyer_name = $("#cbo_buyer_name").val();
	var page_link='requires/knitting_program_wise_grey_fab_stock_report_controller.php?action=order_no_search_popup&companyID='+companyID+'&buyer_name='+buyer_name;
	var title='Order No Search';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var order_no=this.contentDoc.getElementById("hide_order_no").value;
		var order_id=this.contentDoc.getElementById("hide_order_id").value;
		$('#txt_order_no').val(order_no);
		$('#hide_order_id').val(order_id);	 
	}
}
function openmypage_job()
{
	if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	var companyID = $("#cbo_company_name").val();
	var buyer_name = $("#cbo_buyer_name").val();
	var cbo_year_id = $("#cbo_year_selection").val();
	//var cbo_month_id = $("#cbo_month").val();
	var page_link='requires/knitting_program_wise_grey_fab_stock_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
	var title='Job No Search';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var job_no=this.contentDoc.getElementById("hide_job_no").value;
		var job_id=this.contentDoc.getElementById("hide_job_id").value;
		$('#txt_job_no').val(job_no);
		$('#txt_job_id').val(job_id);	 
	}
}
function new_window()
{
	document.getElementById('scroll_body').style.overflow="auto";
	document.getElementById('scroll_body').style.maxHeight="none";
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	d.close(); 
	document.getElementById('scroll_body').style.overflowY="scroll";
	document.getElementById('scroll_body').style.maxHeight="380px";
}
	
function openmypage_receive(po_id,prog_no,booking_no,knit_source,mc_dia,action)
{ //alert(des_prod)
	var companyID = $("#cbo_company_name").val();
	var cbo_store_name = $("#cbo_store_name").val();
	var popup_width='1200px';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/knitting_program_wise_grey_fab_stock_report_controller.php?companyID='+companyID+'&po_id='+po_id+'&prog_no='+prog_no+'&booking_no='+booking_no+'&knit_source='+knit_source+'&mc_dia='+mc_dia+'&cbo_store_name='+cbo_store_name+'&action='+action, 'Details View', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../');
}

function openmypage_transfer_in(po_id,prog_no,booking_no,knit_source,action)
{  
	var companyID = $("#cbo_company_name").val();
	var cbo_store_name = $("#cbo_store_name").val();
	var popup_width='600px';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/knitting_program_wise_grey_fab_stock_report_controller.php?companyID='+companyID+'&po_id='+po_id+'&prog_no='+prog_no+'&booking_no='+booking_no+'&knit_source='+knit_source+'&cbo_store_name='+cbo_store_name+'&action='+action, 'Details View', 'width=600px, height=350px,center=1,resize=0,scrolling=0','../');
}

function openmypage_issue_return(po_id,prog_no,booking_no,action)
{  
	var companyID = $("#cbo_company_name").val();
	var cbo_store_name = $("#cbo_store_name").val();
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/knitting_program_wise_grey_fab_stock_report_controller.php?companyID='+companyID+'&po_id='+po_id+'&prog_no='+prog_no+'&booking_no='+booking_no+'&cbo_store_name='+cbo_store_name+'&action='+action, 'Details View', 'width=700px, height=350px,center=1,resize=0,scrolling=0','../');
}


function openmypage_transfer_out(po_id,prog_no,booking_no,knit_source,action)
{ 
	var companyID = $("#cbo_company_name").val();
	var popup_width='600px';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/knitting_program_wise_grey_fab_stock_report_controller.php?companyID='+companyID+'&po_id='+po_id+'&prog_no='+prog_no+'&booking_no='+booking_no+'&knit_source='+knit_source+'&action='+action, 'Details View', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../');
}


function openmypage_issue(po_id,prog_no,booking_no,action)
{ //alert(des_prod)
	var companyID = $("#cbo_company_name").val();
	var cbo_store_name = $("#cbo_store_name").val();
	var popup_width='660px';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/knitting_program_wise_grey_fab_stock_report_controller.php?companyID='+companyID+'&po_id='+po_id+'&prog_no='+prog_no+'&booking_no='+booking_no+'&cbo_store_name='+cbo_store_name+'&action='+action, 'Details View', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../');
}
function js_set_value( str ) {
        toggle( document.getElementById( 'tr_' + str), '#FFF' );
}
function openmypage_booking()
{
    if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	var companyID = $("#cbo_company_name").val();
	var buyer_name = $("#cbo_buyer_name").val();
	var cbo_year_id = $("#cbo_year_selection").val();
	//var cbo_month_id = $("#cbo_month").val();
	var page_link='requires/knitting_program_wise_grey_fab_stock_report_controller.php?action=booking_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
	var title='Booking No Search';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=370px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var booking_no=this.contentDoc.getElementById("hide_booking_no").value;
		var booking_id=this.contentDoc.getElementById("hide_booking_id").value;
		$('#txt_booking_no').val(booking_no);
		$('#txt_booking_id').val(booking_id);	 
	}
}

</script>
</head>
<body onLoad="set_hotkey();">
<form id="knittingStatusReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:1490px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1490px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th class="must_entry_caption">Company Name</th>
					<th>Store Name</th>
                    <th>Buyer Name</th>
                    <th>Job No</th>
                    <th>Order No</th>
                    <th>Booking No</th>
                    <th>Internal Ref.</th>
                    <th>Program No</th>
                    <th>Program Date</th>
                    <th>Value</th>
                    <th>Get Upto</th>
                    <th>Qty.</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('knittingStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:70px" /></th>
                </thead>
                <tbody>
                    <tr align="center">
                        <td> 
                            <?
                                echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "- Select Company -", $selected, "load_drop_down( 'requires/knitting_program_wise_grey_fab_stock_report_controller', this.value, 'load_drop_down_store', 'store_td' );load_drop_down( 'requires/knitting_program_wise_grey_fab_stock_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
						<td id="store_td">
                            <? 
                                echo create_drop_down( "cbo_store_name", 120, $blank_array,"", 1, "-- Select Store --", $selected, "",0,"" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "- All Buyer -", $selected, "",0,"" );
                            ?>
                        </td>
                       
                         <td>
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:100px" onDblClick="openmypage_job();" placeholder="Write/Browse Job" />
                            <input type="hidden" id="txt_job_id" name="txt_job_id"/>
                    	</td>
						<td>
						    <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:130px" placeholder="Browse Or Write" onDblClick="openmypage_order();" onChange="$('#hide_order_id').val('');" autocomplete="off">
						    <input type="hidden" name="hide_order_id" id="hide_order_id" readonly>
						</td>
						<td>
						    <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:130px" placeholder="Browse Or Write" onDblClick="openmypage_booking();" onChange="$('#hide_booking_id').val('');" autocomplete="off">
						    <input type="hidden" name="txt_booking_id" id="txt_booking_id" readonly>
						</td>
						<td>
                            <input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px">
                        </td>
                        <td>
                            <input name="txt_program_no" id="txt_program_no" class="text_boxes_numeric" style="width:60px">
                        </td>
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:50px" placeholder="From Date"/>
                            To
                            <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:50px" placeholder="To Date"/>
                        </td>
                        <td>
                            <?   
                                $valueWithArr=array(0=>'Value With 0',1=>'Value Without 0');
                                echo create_drop_down( "cbo_value_with", 90, $valueWithArr,"",0,"",2,"","","");
                            ?>
                        </td>
                        <td> 
                            <?
                                echo create_drop_down( "cbo_get_upto_qnty", 70, $get_upto,"", 1, "- All -", 0, "",0 );
                            ?>
                        </td>
                        <td>
                            <input type="text" id="txt_qnty" name="txt_qnty" class="text_boxes_numeric" style="width:30px" value="" />
                        </td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(1)" />
                            <input type="button" id="show_button" class="formbutton" style="width:70px" value="Gross Only" onClick="fn_report_generated(2)" />
                        </td>
                        
                    </tr>
                    <tr>
                        <td colspan="13" align="center" width="95%"><? echo load_month_buttons(1); ?></td>
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

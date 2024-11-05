<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Knitting Program Wise Grey Fab Report.
Functionality	:	
JS Functions	:
Created by		:	Tipu
Creation date 	: 	02-02-2019
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
echo load_html_head_contents("Batch Progress Report", "../../", 1, 1,'',1,1);
?>	
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';
var tableFilters = 
{
	col_0: "none",
	col_operation: {
		id: ["total_batch_qty","total_finish_qty","total_delivery_qty","total_balance_qty"],
   //col: [14,26,27],
   col: [17,28,31,32],
   operation: ["sum","sum","sum","sum"],
   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
	}	
}

function fn_report_generated(type)
{
	
	var job_no=document.getElementById('txt_job_no').value;
	var batch_no=document.getElementById('txt_batch_no').value;
	var working_company_id=document.getElementById('cbo_working_company').value;
	var company_name=document.getElementById('cbo_company_name').value;
	var booking_no=document.getElementById('txt_booking_no').value;
	var txt_date_from=document.getElementById('txt_date_from').value;
	var txt_date_to=document.getElementById('txt_date_to').value;
	
	if(job_no!="" || batch_no!="" || booking_no!="")
	{
		if(company_name == 0 && working_company_id ==0) 
		{			
			alert("Please Select either a company or a working company");
			return;			
		}
	}
	else
	{
		if(company_name == 0 && working_company_id ==0) 
		{			
			alert("Please Select either a company or a working company");
			return;			
		}
		else if (txt_date_from=='') 
		{
			if( form_validation('txt_date_from*txt_date_to','Form Date*To Date')==false )
			{
				return;
			}
		}
	}
		
	var report_title=$( "div.form_caption" ).html();
	var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*txt_job_id*txt_date_from*txt_date_to*cbo_year*txt_batch_no*cbo_search_date*txt_hide_booking_id*txt_booking_no*cbo_working_company',"../../")+'&report_title='+report_title;
	freeze_window(3);
	http.open("POST","requires/batch_wise_process_loss_report_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fn_report_generated_reponse;
}
function fn_report_generated_reponse()
{
 	if(http.readyState == 4) 
	{
  		/*var response=trim(http.responseText).split("####");
		$('#report_container2').html(response[0]);
		var path = '../../';
		document.getElementById('report_container').innerHTML=report_convert_button(path); 

		//setFilterGrid("table_body",-1,tableFilters);

		append_report_checkbox('table_header_1',1);*/

		var response=trim(http.responseText).split("####");
		$('#report_container2').html(response[0]);
		document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window();" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		
		//setFilterGrid("table_body",-1,tableFilters);
		//append_report_checkbox('table_header_1',1);

		show_msg('3');
		release_freezing();
 	}
}

function new_window()
{
	document.getElementById('scroll_body').style.overflow="auto";
	document.getElementById('scroll_body').style.maxHeight="none";
	
	$("#table_body tr:first").hide();
	
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title><link rel="stylesheet" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	d.close(); 
	
	document.getElementById('scroll_body').style.overflowY="scroll";
	document.getElementById('scroll_body').style.maxHeight="450px";
	
	$("#table_body tr:first").show();
}

function openmypage_batch()
{
	if(form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}
	var companyID = $("#cbo_company_name").val();
	var buyer_name = $("#cbo_buyer_name").val();
	var page_link='requires/batch_wise_process_loss_report_controller.php?action=batch_no_search_popup&companyID='+companyID+'&buyer_name='+buyer_name;
	var title='Batch Number Search';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=390px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var order_no=this.contentDoc.getElementById("hide_order_no").value;
		var order_id=this.contentDoc.getElementById("hide_order_id").value;
		$('#txt_batch_no').val(order_no);
		$('#hide_batch_id').val(order_id);	 
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
	var cbo_year_id = $("#cbo_year").val();
	//var cbo_month_id = $("#cbo_month").val();
	//alert(cbo_year_id);
	var page_link='requires/batch_wise_process_loss_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
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

function openmypage_receive(po_id,prog_no,booking_no,action)
{ //alert(des_prod)
	var companyID = $("#cbo_company_name").val();
	var popup_width='580px';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/batch_wise_process_loss_report_controller.php?companyID='+companyID+'&po_id='+po_id+'&prog_no='+prog_no+'&booking_no='+booking_no+'&prog_no='+prog_no+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../');
}
function openmypage_issue(po_id,prog_no,booking_no,action)
{ //alert(des_prod)
	var companyID = $("#cbo_company_name").val();
	var popup_width='580px';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/batch_wise_process_loss_report_controller.php?companyID='+companyID+'&po_id='+po_id+'&prog_no='+prog_no+'&booking_no='+booking_no+'&prog_no='+prog_no+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../');
}
function js_set_value( str ) {
	toggle( document.getElementById( 'tr_' + str), '#FFF' );
}

function search_populate(str)
{
	if(str==1)
	{
		document.getElementById('search_by_th_up').innerHTML="Batch Date";
		$('#search_by_th_up').css('color','blue');
	}
	else if(str==2)
	{
		document.getElementById('search_by_th_up').innerHTML="Dyeing Date";
		$('#search_by_th_up').css('color','blue');
	}	
}
function openmypage_booking()
{
    if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	var companyID = $("#cbo_company_name").val();
	var buyer_name = $("#cbo_buyer_name").val();
	var cbo_year_id = $("#cbo_year").val();
	//var cbo_month_id = $("#cbo_month").val();
	var page_link='requires/batch_wise_process_loss_report_controller.php?action=booking_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
	var title='Booking No Search';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1010px,height=370px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var booking_no=this.contentDoc.getElementById("hide_booking_no").value;
		var booking_id=this.contentDoc.getElementById("hide_booking_id").value;
		$('#txt_booking_no').val(booking_no);
		$('#txt_hide_booking_id').val(booking_id);	 
	}
}
</script>
</head>
<body onLoad="set_hotkey();">
<form id="knittingStatusReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:1090px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1090px;">
             <table class="rpt_table" width="1090px" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
					<th class="must_entry_caption">Company </th>
					<th class="">W. Company</th>
                    <th>Buyer Name</th>
                    <th>Year</th>
                    <th>Job No</th>
					<th>Booking No</th>
                    <th>Batch No</th>
                     <th>Search By</th>
                    <th id="search_by_th_up" class="must_entry_caption">Batch Date</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('knittingStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:50px" /></th>
                </thead>
                <tbody>
                    <tr align="center">
                        <td> 
                            <?
                                echo create_drop_down( "cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "- Select Company -", $selected, "load_drop_down( 'requires/batch_wise_process_loss_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
						<td>
                             <?
                                echo create_drop_down("cbo_working_company", 100, "select id,company_name from lib_company where status_active=1 and is_deleted=0  order by company_name", "id,company_name", 1, "-- Select Working Company --", 0, "");
                              ?>
                        </td>
                        <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "- All Buyer -", $selected, "",0,"" );
                            ?>
                        </td>
                        <td>
                            <?
                              echo create_drop_down( "cbo_year", 65, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
							?>
                        </td>
                         <td>
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:70px" onDblClick="openmypage_job();" placeholder="Write/Browse Job" />
                            <input type="hidden" id="txt_job_id" name="txt_job_id"/>
                    	</td>
						<td>
                            <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:70px" placeholder="Browse Or Write" onDblClick="openmypage_booking();"  autocomplete="off">
                            <input type="hidden" name="txt_hide_booking_id" id="txt_hide_booking_id" readonly>
                        </td>
                        <td>
                            <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:70px" placeholder="Browse Or Write" onDblClick="openmypage_batch();" onChange="$('#hide_batch_id').val('');" autocomplete="off">
                            <input type="hidden" name="hide_batch_id" id="hide_batch_id" readonly>
                        </td>							
                        <td width="" align="center">
                        	<?  
                            $search_by = array(1=>'Batch Date',2=>'Dyeing Date');
							$dd="search_populate(this.value)";
							echo create_drop_down( "cbo_search_date", 80, $search_by,"",0, "--Select--", $selected,$dd,0 );
                       		 ?>
                     	</td>                       
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:55px" placeholder="From Date"/>
                            To
                            <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:55px" placeholder="To Date"/>
                        </td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:50px" value="Show" onClick="fn_report_generated(1)" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="14" align="center" width="95%"><? echo load_month_buttons(1); ?></td>
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

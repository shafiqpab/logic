<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Knitting Program Wise Grey Fab Report.
Functionality	:	
JS Functions	:
Created by		:	
Creation date 	: 	18-04-2018
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

function fn_report_generated(btn)
{
	if (form_validation('cbo_company_name*txt_date_from*txt_date_to','Comapny Name*From Date*To Date')==false)
	{
		return;
	}
	var source = $("#cbo_knitting_source").val();
	var report_title=$( "div.form_caption" ).html();

	if(source ==1)
	{
		if(btn == 1)
		{
			var data="action=report_generate_inhouse"+get_submitted_data_string('cbo_company_name*cbo_location_name*cbo_buyer_name*txt_job_no*txt_job_id*txt_booking_id*txt_booking_no*txt_program_no*txt_dia*cbo_color*txt_date_from*txt_date_to*cbo_year_selection',"../../") +'&report_title='+report_title;
		}
		else
		{
			if (form_validation('txt_prod_from_date*txt_prod_to_date','Production From Date*Production To Date')==false)
			{
				return;
			}
			var data="action=report_generate_inhouse_2"+get_submitted_data_string('cbo_company_name*cbo_location_name*cbo_buyer_name*txt_job_no*txt_job_id*txt_booking_id*txt_booking_no*txt_program_no*txt_dia*cbo_color*txt_date_from*txt_date_to*cbo_year_selection*cbo_floor*txt_prod_from_date*txt_prod_to_date',"../../") +'&report_title='+report_title;
		}
	}
	else
	{
		if(btn == 1)
		{
			var data="action=report_generate_outbound"+get_submitted_data_string('cbo_company_name*cbo_location_name*cbo_buyer_name*txt_job_no*txt_job_id*txt_booking_id*txt_booking_no*txt_program_no*txt_dia*cbo_color*txt_date_from*txt_date_to*cbo_year_selection',"../../") +'&report_title='+report_title;
		}
		else
		{
			if (form_validation('txt_prod_from_date*txt_prod_to_date','Production From Date*Production To Date')==false)
			{
				return;
			}
			var data="action=report_generate_outbound_2"+get_submitted_data_string('cbo_company_name*cbo_location_name*cbo_buyer_name*txt_job_no*txt_job_id*txt_booking_id*txt_booking_no*txt_program_no*txt_dia*cbo_color*txt_date_from*txt_date_to*cbo_year_selection*cbo_floor*txt_prod_from_date*txt_prod_to_date',"../../") +'&report_title='+report_title;
		}
	}

	freeze_window(3);
	http.open("POST","requires/program_and_dia_wise_knitting_balance_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fn_report_generated_reponse;
}
function fn_report_generated_reponse()
{
 	if(http.readyState == 4) 
	{
  		var response=trim(http.responseText).split("**");
		$('#report_container2').html(response[0]);

		var grand_program_qnty = $("#grand_program_qnty").text();
		var grand_today_qnty = $("#grand_today_prod_qnty").text();
		var grand_total_qnty = $("#grand_total_prod_qnty").text();
		var grand_bal_qnty = $("#grand_bal_prod_qnty").text();

		$("#top_grand_total_program").text(grand_program_qnty);
		$("#top_grand_today_prod").text(grand_today_qnty);
		$("#top_grand_total_prod").text(grand_total_qnty);
		$("#top_grand_bal_prod").text(grand_bal_qnty);

		//document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
		document.getElementById('report_container').innerHTML = '<a href="requires/' + response[1] + '" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		//setFilterGrid("tbl_list_search",-1,tableFilters);
		show_msg('3');
		release_freezing();
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
	var page_link='requires/program_and_dia_wise_knitting_balance_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
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
	var page_link='requires/program_and_dia_wise_knitting_balance_controller.php?action=booking_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
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
function location_dis() {
	var cbo_knitting_source = $("#cbo_knitting_source").val();

	if (cbo_knitting_source == 3) 
	{
		$('#cbo_location_name').val(0);
		$('#cbo_location_name').attr('disabled', 'disabled');
	}
	else {
		$('#cbo_location_name').removeAttr('disabled', 'disabled');
	}

}


function openmypage_color()
{

	var page_link='requires/program_and_dia_wise_knitting_balance_controller.php?action=color_popup';
	var title='Color List';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var color_id=this.contentDoc.getElementById("hide_color_id").value;
		var color_name=this.contentDoc.getElementById("hide_color_name").value;
		$('#txt_color_no').val(color_name);
		$('#cbo_color').val(color_id);	 
	}
}

</script>
</head>
<body onLoad="set_hotkey();">
<form id="knittingStatusReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:1610px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1610px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
             		<th>Source</th>
                    <th class="must_entry_caption">Working Factory</th>
                    <th>Location</th>
                    <th>Buyer</th>
                    <th>Job No</th>
                    <th>Booking No</th>
                    <th>Floor</th>
                    <th>Program No</th>
                    <th>M/Dia</th>
                    <th>Color</th>
                    <th width="140" class="must_entry_caption">Date Range</th>
                    <th width="140" class="must_entry_caption">Production Date</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('knittingStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:70px" /></th>
                </thead>
                <tbody>
                    <tr align="center">
                    	<td>
                    		<?
                    			echo create_drop_down("cbo_knitting_source",150,$knitting_source,"", 1, "-- Select --", 0,"load_drop_down( 'requires/program_and_dia_wise_knitting_balance_controller', this.value, 'load_drop_down_knitting_com','working_factory_td');location_dis();",0,'1,3');
                    		?>
                    	</td>
                        <td id="working_factory_td"> 
                            <?
                                echo create_drop_down("cbo_company_name", 130, $blank_array, "", 1, "--Select Company--", 0, "");
                            ?>
                        </td>
                        <td id="location_td">
                        	<?
                        		echo create_drop_down( "cbo_location_name", 152, $blank_array,"", 1, "-- Select Location --", 0, "" );
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
                                <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:140px" placeholder="Browse Or Write" onDblClick="openmypage_booking();" onChange="$('#hide_booking_id').val('');" autocomplete="off">
                                <input type="hidden" name="txt_booking_id" id="txt_booking_id" readonly>
                            </td>
                        <td id="floor_td">
                            <? 
                                echo create_drop_down( "cbo_floor", 120, $blank_array,"", 1, "-- Select Floor --", $selected, "",0,"" );
                            ?>
                        </td>
                        <td>
                            <input name="txt_program_no" id="txt_program_no" class="text_boxes_numeric" style="width:60px">
                        </td>
                        <td>
                            <input name="txt_dia" id="txt_dia" class="text_boxes_numeric" style="width:60px">
                        </td>
                        <td>
                            <?  //echo create_drop_down("cbo_color", 100, "select id,color_name from lib_color", "id,color_name", 1, "-- Select color --", 0, "");?>
                            <input name="txt_color_no" id="txt_color_no" class="text_boxes" style="width:60px" onDblClick="openmypage_color();" readonly placeholder="Browse">
                            <input name="cbo_color" id="cbo_color" type="hidden" style="width:60px"> 
                        </td>
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:50px" placeholder="From Date"/>
                            To
                            <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:50px" placeholder="To Date"/>
                        </td>
                        <td align="center">
                            <input type="text" name="txt_prod_from_date" id="txt_prod_from_date" value="" class="datepicker" style="width:50px" placeholder="From Date"/>
                            To
                            <input type="text" name="txt_prod_to_date" id="txt_prod_to_date" value="" class="datepicker" style="width:50px" placeholder="To Date"/>
                        </td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(1)" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="12" align="center" width="95%">
							<? echo load_month_buttons(1); ?>
                        </td>
                        <td>
                            <input type="button" id="show_button_2" class="formbutton" style="width:70px" value="Show-2" onClick="fn_report_generated(2)" />
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
<!-- <script>
	set_multiselect('cbo_color','0','0','','0');
</script> -->
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

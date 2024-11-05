<?php
/*********************************************** Comments *************************************
*	Purpose			: 	
*	Functionality	:	
*	JS Functions	:
*	Created by		:	Monir Hossain
*	Creation date 	: 	24-11-2016
*	Updated by 		: 		
*	Update date		: 		   
*	QC Performed BY	:		
*	QC Date			:	
*	Comments		:
************************************************************************************************/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Cutting Status Report", "../../", 1, 1, $unicode,1,1);
?>	
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';

function fn_show_report(type)
{
	//alert("su..re"); return;
	/*if (form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}
	else*/ if($('#txt_ref_no').val()=="" && $('#txt_order_no').val()=="" &&  $('#txt_date_from').val()=="" &&  $('#txt_booking_no').val()==""  &&  $('#txt_batch_no').val()=="" )
	{
		form_validation('txt_date_from*txt_date_to','From Date*To Date')==false
		return;
	}
	else
	{
		/*if( ($('#cbo_buyer_name').val()==0 && $('#txt_job_no').val()=="" && $('#txt_file_no').val()=="" && $('#txt_order_no').val()=="" && $('#txt_cutting_no').val()=="" && $('#txt_table_no').val()=="") || ($('#cbo_buyer_name').val()!=0 && $('#txt_job_no').val()=="" && $('#txt_file_no').val()=="" && $('#txt_order_no').val()=="" && $('#txt_cutting_no').val()=="" && $('#txt_table_no').val()=="") )
		{
			if (form_validation('txt_date_from*txt_date_to','From Date*To Date')==false)
			{
				return;
			}
		}*/
		//&operation="+operation+
		var data="action=report_generate&type="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_working_company_name*cbo_location_name*txt_ref_no*txt_job_no*txt_date_from*txt_date_to*txt_job_no_hidden*cbo_gmts_item*txt_order_no*hide_order_id*txt_booking_no*txt_batch_no*cbo_floor_id*hide_color_id',"../../");
		freeze_window('3');
		http.open("POST","requires/cutting_status_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
}

function fn_report_generated_reponse()
{
 	if(http.readyState == 4) 
	{
  		//alert(http.responseText);
		show_msg('3');
		var response=trim(http.responseText).split("####");
		$('#report_container2').html(response[0]);
		document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>'+'&nbsp;&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
		release_freezing();
 	}
}

function new_window()
{
	document.getElementById('scroll_body').style.overflow='auto';
	document.getElementById('scroll_body').style.maxHeight='none'; 
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>'); 
	d.close();
	document.getElementById('scroll_body').style.overflowY='scroll';
	document.getElementById('scroll_body').style.maxHeight='300px';
}	 

//job search popup
function openmypage_job()
{
	if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	
	var companyID = $("#cbo_company_name").val();
	var buyer_name = $("#cbo_buyer_name").val();
	var sytle_ref_no = $("#txt_ref_no").val();
	var page_link='requires/cutting_status_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&sytle_ref_no='+sytle_ref_no;
	var title='Job No Search';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var job_no=this.contentDoc.getElementById("hide_job_no").value;
		fnc_gmt_item(job_no);
		$('#txt_job_no').val(job_no);
	}
}

//cutting search popup
/*function open_cutting_popup()
{ 
	if( form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	} 
	var company_id=$("#cbo_company_name").val();
	var page_link='requires/cutting_status_report_controller.php?action=cutting_number_popup&company_id='+company_id; 
	var title="Search Cutting Number Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=420px,center=1,resize=0,scrolling=0',' ../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var cutting_no = this.contentDoc.getElementById("hdn_cut_no").value;
		$('#txt_cutting_no').val(cutting_no); 
 	}
}*/

//order search popup	
/*function openmypage_order()
{
	if(form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}
	
	var companyID = $("#cbo_company_name").val();
	var page_link='requires/cutting_status_report_controller.php?action=order_no_search_popup&companyID='+companyID;
	var title='Order No Search';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var order_no=this.contentDoc.getElementById("hide_order_no").value;
		$('#txt_po_no').val(order_no);
	}
}*/

function openmypage_order()
{
	if(form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}
	
	var companyID = $("#cbo_company_name").val();
	var job_no = $("#txt_job_no").val();
	var page_link='requires/cutting_status_report_controller.php?action=order_no_search_popup&companyID='+companyID+'&job_no='+job_no;
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

function openmypage_color()
{
	if(form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}
	
	var companyID = $("#cbo_company_name").val();
	var job_no = $("#txt_job_no").val();
	var page_link='requires/cutting_status_report_controller.php?action=color_search_popup&companyID='+companyID+'&job_no='+job_no;
	var title='Color Search';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=890px,height=390px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var order_no=this.contentDoc.getElementById("hide_order_no").value;
		var order_id=this.contentDoc.getElementById("hide_order_id").value;
		
		$('#txt_color').val(order_no);
		$('#hide_color_id').val(order_id);	 
	}
}

// for report lay chart
function generate_report_lay_chart(data)
{
	var action	= 'cut_lay_entry_report_print';
	window.open("../../prod_planning/cutting_plan/requires/cut_and_lay_ratio_wise_entry_controller_urmi.php?data=" + data+'&action='+action, true );
}

function onchange_buyer()
{
	if($('#cbo_buyer_name').val() !=0)
	{
		document.getElementById("cap_cut_date").style.color = "blue";
	}
	else 
	{
		document.getElementById("cap_cut_date").style.color = "";
	}
}




function openmypage_style()
{
	if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();
		//var job_year = $("#cbo_job_year").val();
		//var txt_style_ref_no = $("#txt_ref_no").val();
		var txt_style_ref_id = $("#txt_style_ref_id").val();
		var txt_style_ref = $("#txt_style_ref").val();
		var page_link='requires/cutting_status_report_controller.php?action=style_search_popup&company='+company+'&buyer='+buyer+'&txt_style_ref_no='+txt_style_ref_no+'&txt_style_ref_id='+txt_style_ref_id+'&txt_style_ref='+txt_style_ref;
		var title="Search Style Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=440px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			//var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			//var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var style_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_no);
			data=style_no.split("_");
			//$("#txt_style_ref").val(data[3]);
			//
			fnc_gmt_item(data[1]);
			$('#txt_style_ref_id').val(data[0]);
			$('#txt_job_no').val(data[1]);
			$("#txt_job_no_hidden").val(data[1]); 
	  		$('#txt_ref_no').val(data[2]);
	  		$('#txt_job_no').attr('disabled','true'); 
		}
}
function fnc_gmt_item(job_no)
{
		//alert(job_no);
		load_drop_down( 'requires/cutting_status_report_controller',job_no,'load_drop_down_gmts_item','gmt_td' );	
}

function generate_report_bundle_list(cut_no,entry_form){
    var action = "cut_lay_bundle_print";
    var data = return_global_ajax_value( cut_no, 'str_data_from_cut_no', '', 'requires/cutting_status_report_controller');
    var data = data.trim();
    //alert (data);
	if(entry_form==99)
	{
		window.open("../cutting_plan/requires/cut_and_lay_ratio_wise_entry_controller_urmi.php?data=" + data+'&action='+action, true );
	}
	else if(entry_form==289)
	{
		window.open("../cutting_plan/requires/woven_cut_and_lay_ratio_wise_entry_controller.php?data=" + data+'&action='+action, true );
	}
}

function generate_report_qc(company_id,source_id,working_com,qc_id,cut_no){
	var report_title="Cutting QC V2";
	var data=working_com+'*'+company_id+'*'+qc_id+'*'+source_id+'*'+report_title+'*'+cut_no;
	var action="print4_reject_report";
	window.open("../../production/requires/cutting_entry_controller_urmi.php?data=" + data+'&action='+action, true );
}

function print_report_button_setting(report_ids) 
    {
       
        $('#show_button').hide();
        $('#show_button1').hide();
        $('#show_button2').hide();
        var report_id=report_ids.split(",");
        report_id.forEach(function(items){
            if(items==108){$('#show_button').show();}
            else if(items==260){$('#show_button1').show();}
            else if(items==265){$('#show_button2').show();}
            });
    }
	
	function load_floor(id) 
	{
		var companyID = document.getElementById('cbo_working_company_name').value;
		var locationID = document.getElementById('cbo_location_name').value;
	  	load_drop_down('requires/cutting_status_report_controller',companyID+'_'+locationID, 'load_drop_down_floor', 'floor_td' );
		set_multiselect('cbo_floor_id','0','0','','0');
	}

	function load_location()
	{
		// alert('ok');
		var companyID = document.getElementById('cbo_working_company_name').value;
		get_php_form_data(companyID,'print_button_variable_setting','requires/cutting_status_report_controller');

		load_drop_down('requires/cutting_status_report_controller',companyID, 'load_drop_down_location', 'location_td' );
		// set_multiselect('cbo_location_name','0','0','','0',);
		set_multiselect('cbo_location_name','0','0','','0','load_floor()');
	}


function openmypage_party(type)
{
	var page_link='requires/cutting_status_report_controller.php?action=party_popup&type='+type;
	var title='Company Search';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=430px,height=370px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var party_name=this.contentDoc.getElementById("hide_party_name").value;
		var party_id=this.contentDoc.getElementById("hide_party_id").value;
		var poptype=this.contentDoc.getElementById("hidd_type").value;
		if(poptype==1)
		{
			$('#txt_company_name').val(party_name);
			$('#cbo_company_name').val(party_id);
			load_drop_down( 'requires/cutting_status_report_controller', party_id, 'load_drop_down_buyer', 'buyer_td' );
			get_php_form_data(party_id,'size_wise_repeat_cut_no','../../prod_planning/cutting_plan/requires/cut_and_lay_entry_controller' );
			get_php_form_data(party_id,'print_button_variable_setting','requires/cutting_status_report_controller' );

		}
		
	}
}
</script>
<style>
	hr
	{
		color: #676767;
		background-color: #676767;
		height: 1px;
	}
</style> 
</head>
 
<body onLoad="set_hotkey();">
<form id="cuttingLayProductionReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:1660px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel">      
         <fieldset style="width:1494px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th class="must_entry_caption">Company Name</th>
                   	<th>Working Company</th>
                   	<th>Location</th>
                   	<th>Floor</th>
                    <th>Buyer Name</th>
                    <th>Style Ref.</th>
                    <th>Job No</th>
                    <th>PO NO.</th>
					<th>Booking No.</th>
					<th>Batch No .</th>			
                    <th>Gmts Item</th>
                    <th>Color</th>
                    <th colspan="2" id="cap_cut_date">Cutting Date</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('cuttingLayProductionReport_1','','','','')" class="formbutton" style="width:50px" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td>
							<!-- <? echo create_drop_down( "cbo_company_name", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/cutting_status_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );get_php_form_data(this.value,'print_button_variable_setting','requires/cutting_status_report_controller');" ); 
							
							?>  -->
							<input type="text" id="txt_company_name" name="txt_company_name" class="text_boxes" style="width:120px" onDblClick="openmypage_party(1);" placeholder="Browse Party" readonly />
							<input type="hidden" id="cbo_company_name" name="cbo_company_name" class="text_boxes" style="width:30px" />
                        	<input type="hidden" name="size_wise_repeat_cut_no" id="size_wise_repeat_cut_no" readonly>
                        	<!-- <input type="hidden" name="size_wise_repeat_cut_no" id="size_wise_repeat_cut_no" readonly> -->
                        </td>
                        <td>
                        <? 
	                       echo create_drop_down( "cbo_working_company_name", 142, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0  order by company_name","id,company_name", 0, "-- Select  --", $selected, "" );// get_php_form_data(this.value,'size_wise_repeat_cut_no','requires/cut_and_lay_ratio_wise_entry_controller_urmi' );
	                     ?>
                        </td>
                        <td id="location_td">
                        <? 
	                       echo create_drop_down( "cbo_location_name", 142, "select id,location_name from lib_location comp where status_active=1 and is_deleted=0  order by location_name","id,location_name", 0, "-- Select  --", $selected, "" );// get_php_form_data(this.value,'size_wise_repeat_cut_no','requires/cut_and_lay_ratio_wise_entry_controller_urmi' );
	                     ?>
                        </td>
						<td id="floor_td">
							<? 
								echo create_drop_down( "cbo_floor_id", 142, $blank_array,"", 0, "-- Select --", $selected, "",1,"" );
                            ?>
                        </td>                        
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- Select Buyer --", $selected, "","","" ); ?></td>
                        <td>
                       <!-- <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:100px;" />-->
                        <input type="text" name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:100px" placeholder="Browse"  onDblClick="openmypage_style();" onChange="fnc_gmt_item(this.value)" readonly  /> 
                        <input type="hidden" name="txt_style_ref_id" id="txt_style_ref_id"/>    <input type="hidden" name="txt_style_ref_no" id="txt_style_ref_no"/></td>
                        <td>                        
                            <!--<input type="text" name="txt_cutting_no" id="txt_cutting_no" class="text_boxes" style="width:100px;" onKeyDown="if (event.keyCode == 13) document.getElementById(this.id).ondblclick()" />-->
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:100px" onDblClick="openmypage_job();"  disabled  />
                            <input type="hidden" name="update_id"  id="update_id" readonly />
                            <input type="hidden" name="txt_job_no_hidden"  id="txt_job_no_hidden"  />
                        </td>
                        <td>
                            <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:100px" placeholder="Browse Or Write" onDblClick="openmypage_order();" onChange="$('#hide_order_id').val('');" autocomplete="off">
                            <input type="hidden" name="hide_order_id" id="hide_order_id" readonly>
                        </td>
						<td>
						<input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:100px" placeholder="Write">
						</td>
						<td>
						<input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:100px" placeholder="Write">
						</td>
                         <td id="gmt_td"><? echo create_drop_down( "cbo_gmts_item", 100, $blank_array,"", 1, "-- Select Item --", $selected, "","","" ); ?></td>
                        <td>
                            <input type="text" name="txt_color" id="txt_color" class="text_boxes" style="width:100px" placeholder="Browse" onDblClick="openmypage_color();" onChange="$('#hide_color_id').val('');" autocomplete="off" readonly="true">
                            <input type="hidden" name="hide_color_id" id="hide_color_id" readonly>
                        </td>
                        
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"  placeholder="From Date" readonly></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date" readonly></td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:70px;display: none;" value="Show" onClick="fn_show_report(1)" /></td>
                    </tr>
                    <tr>
                	<td colspan="13"><? echo load_month_buttons(1); ?>
                        <input type="button" id="show_button1" class="formbutton" style="width:70px;display: none;" value="PO Wise" onClick="fn_show_report(2)" /><input type="button" id="show_button2" class="formbutton" style="width:70px;display: none;" value="Country" onClick="fn_show_report(3)" /></td>
               		</tr>
                </tbody>
            </table>
        </fieldset>
    	</div>
         <div style="display:none" id="data_panel"></div>   
    	<div id="report_container" align="center"></div>
    	<div id="report_container2" align="left"></div>
    </div>
 </form>   
</body>
<script>
	set_multiselect('cbo_working_company_name','0','0','','0','load_location()');
	set_multiselect('cbo_location_name','0','0','','0');
	set_multiselect('cbo_floor_id','0','0','','0');</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
/*-------------------------------------------- Comments
Purpose			: 	This form will Create Daily Dyeing Production Analysis Report
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	03-02-2014
Updated by 		: 	Jahid	
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
echo load_html_head_contents("Daily Dyeing Production Analysis Report", "../../", 1, 1,'','1','');

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';

	var tableFilters = 
	{
		// col_25: "none",
		col_operation: {
		id: ["value_total_dyeing_qnty","value_total_trims_qnty","value_total_dye_cost","value_total_chem_cost"],
		col: [14,15,24,26],
		operation: ["sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	} 
	var tableFilters3 = 
	{
		// col_25: "none",
		col_operation: {
		id: ["value_dyeing_qnty","value_dye_cost","value_chem_cost"],
		col: [14,23,25],
		operation: ["sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML"]
		}
	} 
	
	var tableFilters1 = 
	{
		// col_23: "none",
		col_operation: {
		id: ["value_total_sub_dyeing_qnty","value_total_sub_trims_qnty","value_total_sub_dye_cost","value_total_sub_chem_cost"],
		col: [12,13,22,24],
		operation: ["sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	} 
	var tableFilters4 = 
	{
		// col_25: "none",
		col_operation: {
		id: ["value_total_dyeing_qnty","value_total_dye_cost","value_total_chem_cost"],
		col: [14,23,25],
		operation: ["sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML"]
		}
	} 

	function fn_report_generated(type_id)
	{
			var working_company_id=document.getElementById('cbo_working_company_id').value;
			var cbo_company_name=document.getElementById('cbo_company_id').value;
			var txt_batch_no=document.getElementById('txt_batch_no').value;
			var txt_booking_no=document.getElementById('txt_booking_no').value;
			//alert(cbo_company_name);
			
			if(txt_batch_no!="" || txt_booking_no!="" )
			{
				if(cbo_company_name == 0 && working_company_id ==0) {			
					alert("Please Select either a company or a working company");
					return;			
				}
			}
			else
			{
				if(cbo_company_name == 0 && working_company_id ==0) {			
					alert("Please Select either a company or a working company");
					return;			
				}
				else if (form_validation('txt_date_from*txt_date_to','From Date*To Date')==false)
				{
					return;
				}
			}
			if(type_id==1)
			{
				var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_working_company_id*cbo_unit_id*txt_date_from*txt_date_to*cbo_type*batch_id*txt_batch_no*txt_booking_no*txt_hide_booking_id',"../../")+'&type_id='+type_id;
			}
			else if(type_id==2)
			{
				var data="action=report_generate2"+get_submitted_data_string('cbo_company_id*cbo_working_company_id*cbo_unit_id*txt_date_from*txt_date_to*cbo_type*batch_id*txt_batch_no*txt_booking_no*txt_hide_booking_id',"../../")+'&type_id='+type_id;;
			}
			else
			{
				var data="action=report_generate3"+get_submitted_data_string('cbo_company_id*cbo_working_company_id*cbo_unit_id*txt_date_from*txt_date_to*cbo_type*batch_id*txt_batch_no*txt_booking_no*txt_hide_booking_id',"../../")+'&type_id='+type_id;
			}
			freeze_window(3);
			http.open("POST","requires/daily_dyeing_prod_analysis_repor_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****"); 
		
			var tot_rows=reponse[2];
			$('#report_container2').html(reponse[0]);
		
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			//append_report_checkbox('table_header_1',1);
			if(reponse[3]==1) // Show
			{
				var type=$('#cbo_type').val();
				if(type==0 || type==1)
				{
					setFilterGrid("table_body",-1,tableFilters);
				}
				if(type==0 || type==2)
				{
					setFilterGrid("table_body1",-1,tableFilters1);
				}
				if(type==0 || type==3)
				{
					setFilterGrid("table_body3",-1,tableFilters3);
				}
				if(type==0 || type==4)
				{
					setFilterGrid("table_body4",-1,tableFilters4);
				}
			}
			else if(reponse[3]==2) // Show 2
			{
				var type=$('#cbo_type').val();
				if(type==0 || type==1)
				{
					setFilterGrid("table_body",-1);//tableFilters
				}
				if(type==0 || type==2)
				{
					setFilterGrid("table_body1",-1);//tableFilters1
				}
				if(type==0 || type==3)
				{
					setFilterGrid("table_body3",-1);//tableFilters3
				}
				if(type==0 || type==4)
				{
					setFilterGrid("table_body4",-1);//tableFilters4
				}
			}
			else // FSO Wise
			{
				var type=$('#cbo_type').val();
				if(type==0 || type==1)
				{
					setFilterGrid("table_body",-1,tableFilters);
				}
				if(type==0 || type==2)
				{
					setFilterGrid("table_body1",-1,tableFilters1);
				}
				if(type==0 || type==3)
				{
					setFilterGrid("table_body3",-1,tableFilters3);
				}
				if(type==0 || type==4)
				{
					setFilterGrid("table_body4",-1,tableFilters4);
				}
			}
			show_msg('3');
			release_freezing();
		}
	}
	function new_window()
	{
		// document.getElementById('scroll_body').style.overflow="auto";
		// document.getElementById('scroll_body').style.maxHeight="none"; 

		const el = document.querySelector('#scroll_body');
		
		  if (el) {
		    document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none"; 
			$("#scroll_body tr:first").hide();

		}
		const el1 = document.querySelector('#scroll_body1');
		  if (el1) {
		    document.getElementById('scroll_body1').style.overflow="auto";
			document.getElementById('scroll_body1').style.maxHeight="none"; 
			$("#scroll_body1 tr:first").hide();

		}
		const el2 = document.querySelector('#scroll_body2');
		  if (el2) {
		    document.getElementById('scroll_body2').style.overflow="auto";
			document.getElementById('scroll_body2').style.maxHeight="none"; 
			$("#scroll_body2 tr:first").hide();

		}
		const el3 = document.querySelector('#scroll_body3');
		  if (el3) {
		    document.getElementById('scroll_body3').style.overflow="auto";
			document.getElementById('scroll_body3').style.maxHeight="none"; 
			$("#scroll_body3 tr:first").hide();

		}
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_print.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		// document.getElementById('scroll_body').style.overflowY="auto"; 
		// document.getElementById('scroll_body').style.maxHeight="400px";

		if (el) {
		    document.getElementById('scroll_body').style.overflowY="auto"; 
			document.getElementById('scroll_body').style.maxHeight="400px";
			$("#scroll_body tr:first").show();
		}
		if (el1) {
		    document.getElementById('scroll_body1').style.overflowY="auto"; 
			document.getElementById('scroll_body1').style.maxHeight="400px";
			$("#scroll_body1 tr:first").show();
		}
		if (el2) {
		    document.getElementById('scroll_body2').style.overflowY="auto"; 
			document.getElementById('scroll_body2').style.maxHeight="400px";
			$("#scroll_body2 tr:first").show();
		}
		if (el3) {
		    document.getElementById('scroll_body3').style.overflowY="auto"; 
			document.getElementById('scroll_body3').style.maxHeight="400px";
			$("#scroll_body3 tr:first").show();
		}
	}
	function change_color_sub(v_id,e_color)
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

// BookingNumber 
function openmypage_booking()
{
    if( form_validation('cbo_company_id','Company Name')==false )
    {
        return;
    }
    var companyID = $("#cbo_company_id").val();
   var cbo_year_id = $("#cbo_year_selection").val();
    var page_link='requires/daily_dyeing_prod_analysis_repor_controller.php?action=booking_no_popup&companyID='+companyID+'&cbo_year_id='+cbo_year_id;
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
function batch_number_popup()
{ 
	if(form_validation('cbo_company_id','Company')==false)
	{
		return;
	}
	var company_name=document.getElementById('cbo_company_id').value;
	var batch_number=document.getElementById('txt_batch_no').value;
	 var cbo_year_id = $("#cbo_year_selection").val();

	var page_link="requires/daily_dyeing_prod_analysis_repor_controller.php?action=batchnumber_show&company_name="+company_name+'&cbo_year_id='+cbo_year_id; 
	var title="Batch Number";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=400px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theemail=this.contentDoc.getElementById("selected_id").value;
		var batch=theemail.split("_");
		document.getElementById('batch_id').value=batch[0];
		document.getElementById('txt_batch_no').value=batch[1];
		release_freezing();
	}
}
function getCompanyId() 
	{	 
	    var company_id = document.getElementById('cbo_working_company_id').value;
		load_drop_down('requires/daily_dyeing_prod_analysis_repor_controller', company_id, 'load_drop_down_unit', 'unit_name_td' );
		
	}
	
</script>
</head>
<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",''); ?>
		 <form name="dailydyeingprodreport_1" id="dailydyeingprodreport_1"> 
         <h3 style="width:1110px; margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" style="width:1110px" >      
             <fieldset>
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                    <thead>
                        <th class="must_entry_caption">Company Name</th>
						<th class="">Working Company </th>
                     
                        <th>Booking No</th>
                        <th>Batch No</th>
                        <th>Unit Name</th>
                        <th class="must_entry_caption">Dyeing Date</th>
                        <th>Type</th>
                        <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('dailydyeingprodreport_1','report_container*report_container2','','','')" class="formbutton" style="width:70px" /></th>
                    </thead>
                    <tbody>
                        <tr>
                            <td> 
                                <?
                                    echo create_drop_down( "cbo_company_id", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 0, "-- Select Company --", $selected, "" );
                                ?>
                            </td>
							 <td id="td_company"> 
                                <?
                                    echo create_drop_down( "cbo_working_company_id", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down('requires/daily_dyeing_prod_analysis_repor_controller', this.value, 'load_drop_down_unit', 'unit_name_td' );" );
                                ?>
                            </td>
                           
                              <td align="center">
                                  <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:70px" placeholder="Browse Or Write" onDblClick="openmypage_booking();" >
                                <input type="hidden" name="txt_hide_booking_id" id="txt_hide_booking_id" readonly>
                              
                            </td>
                              <td align="center">
                               <input type="text"  name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:70px;" tabindex="1" placeholder="Write" onDblClick="batch_number_popup();">
                                     <input type="hidden" name="batch_id" id="batch_id">
                            </td>
                             <td id="unit_name_td">
                                <?
                                    echo create_drop_down("cbo_unit_id",120,$blank_array,"", 1, "-- All --", 0,"",0,'');
                                ?>
                            </td>
                            
                            <td align="center">
                                 <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:50px" placeholder="From Date"/>
                                 &nbsp;To&nbsp;
                                 <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:50px" placeholder="To Date"/>
                            </td>
                            <td align="center">
                                <?
									$gen_type=array(1=>"Self",2=>"Subcon",3=>"Sample",4=>"Others");
                                    echo create_drop_down("cbo_type",70,$gen_type,"", 1, "-- All --", 0,"",0,'');
                                ?>
                            </td>
                            <td align="center"><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(1)" />
                            <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show2" onClick="fn_report_generated(2)" />
                            <input type="button" id="show_button" class="formbutton" style="width:70px" value="FSO Wise" onClick="fn_report_generated(3)" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="8" align="center" width="95%"><? echo load_month_buttons(1); ?></td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
		</form>
	</div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	var company_count = $('#cbo_company_id > option').length;
	if(company_count*1 > 1){
		set_multiselect('cbo_company_id*cbo_working_company_id','0*0','0','','0');
	}
	
	setTimeout[($("#td_company a").attr("onclick","disappear_list(cbo_working_company_id,'0');getCompanyId();") ,3000)];	
</script>
</html>
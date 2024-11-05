<?
/*-------------------------------------------- Comments
Purpose			: 	This form will Create Daily Dyeing Production Analysis Report
Functionality	:	
JS Functions	:
Created by		:	Aziz
Creation date 	: 	03-11-2014
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
echo load_html_head_contents("Daily Finished Fabric Production  Report", "../../", 1, 1, '','' ,'');
?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';

	var tableFilters = 
	{
		
		col_operation: {
		id: ["value_total_batch_qty","value_total_qc_qty","value_total_grey_used_qty","value_total_rj_qty"],
		col: [26,27,28,29],
		operation: ["sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	} 

	var tableFilters2 = 
	{
		
		col_operation: {
		id: ["value_total_batch_qty","value_total_qc_qty","value_total_grey_used_qty","value_total_rj_qty"],
		col: [24,25,26,27],
		operation: ["sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	} 

	function fn_report_generated(type)
	{
		var txt_job_no=document.getElementById('txt_job_no').value;	
		var order_no=document.getElementById('txt_order_no').value;
		var batch_no=document.getElementById('txt_batch_no').value;
		var file_no=document.getElementById('txt_file_no').value;	
		var int_ref_no=document.getElementById('txt_int_ref_no').value;
		var txt_booking_no=document.getElementById('txt_booking_no').value;
		var cbo_company_id=document.getElementById('cbo_company_id').value;

		if(cbo_company_id==0)
		{
			if(form_validation('cbo_working_company','Working Company')==false)
			{	
				return;
			}
		}else{
			
			if(form_validation('cbo_company_id','Company')==false)
			{	
				return;
			}
		}
		if( (txt_booking_no=="") &&  (order_no=="") && (txt_job_no=="") && (file_no=="") && (int_ref_no=="") && (batch_no=="") )
		{
			if(form_validation('txt_date_from*txt_date_to','From date Fill*To date Fill')==false)
			{
				$("#must_entry_caption_date").css("color", "blue");				
				return;
			}
		}else{
			
			$("#must_entry_caption_date").css("color", "black");
		}
	
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_unit_id*txt_job_id*txt_job_no*txt_booking_id*txt_booking_no*txt_order_id*txt_order_no*txt_batch_id*txt_batch_no*txt_date_from*txt_date_to*txt_file_no*txt_int_ref_no*cbo_production_type*cbo_working_source*cbo_working_company*cbo_working_floor*cbo_working_location',"../../")+'&type='+type;;
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/daily_finished_fabric_production_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
		
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			//document.getElementById('report_container2').innerHTML=http.responseText;
			//document.getElementById('report_container').innerHTML=report_convert_button('../../'); 

			var response = trim(http.responseText).split("####");
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML = '<a href="requires/' + response[1] + '" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:155px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';


			//append_report_checkbox('table_header_1',1);
			
			//if( $("#cbo_type").val()==1 ){ setFilterGrid("table_body",-1,tableFilters1);}
			//else{ setFilterGrid("table_body",-1,tableFilters2);}
			setFilterGrid("tbl_dyeing",-1,tableFilters);
			setFilterGrid("tbl_dyeing1",-1,tableFilters2);
			show_msg('3');
			release_freezing();
		}
	}


	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none"; 
		$(".flt").css("display","none");
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="scroll"; 
		document.getElementById('scroll_body').style.maxHeight="400px";
		$(".flt").css("display","block");
	}
	
	function openmypage_order()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var txt_job_no=$('#txt_job_no').val();
		var company = $("#cbo_company_id").val();	
		//var buyer=$("#cbo_buyer_name").val();
		var page_link='requires/daily_finished_fabric_production_report_controller.php?action=order_wise_search&company='+company+'&txt_job_no='+txt_job_no; 
		var title="Search Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var prodID=this.contentDoc.getElementById("txt_po_id").value;
			//alert(prodID); // product ID
			var prodDescription=this.contentDoc.getElementById("txt_po_val").value; // product Description
			$("#txt_order_no").val(prodDescription);
			$("#txt_order_id").val(prodID); 
		}
	}

	function openmypage_batch()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var txt_booking_no=$('#txt_booking_no').val();
		var txt_batch_no=$('#txt_batch_no').val();
		var company = $("#cbo_company_id").val();	

		var page_link='requires/daily_finished_fabric_production_report_controller.php?action=batch_wise_search&company='+company+'&txt_batch_no='+txt_batch_no+'&txt_booking_no='+txt_booking_no; 
		var title="Search Batch Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var batch_id=this.contentDoc.getElementById("hdn_batch_id").value;
			var batch_no=this.contentDoc.getElementById("hdn_batch_val").value;
			$("#txt_batch_no").val(batch_no);
			$("#txt_batch_id").val(batch_id); 
		}
	}
	
	function openmypage_job()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		var cbo_unit_id = $("#cbo_unit_id").val();
		var cbo_year_id = $("#cbo_year_selection").val();
		//var cbo_month_id = $("#cbo_month").val();
		var page_link='requires/daily_finished_fabric_production_report_controller.php?action=jobnumbershow&company_id='+companyID+'&cbo_unit_id='+cbo_unit_id+'&cbo_year_id='+cbo_year_id;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=640px,height=400px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			$('#txt_job_no').val(job_no);
			$('#txt_job_id').val(job_id);	 
		}
	}

	function openmypage_booking()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		var cbo_unit_id = $("#cbo_unit_id").val();
		var cbo_year_id = $("#cbo_year_selection").val();
		var cbo_production_type = $("#cbo_production_type").val();
		
		//var cbo_month_id = $("#cbo_month").val();
		var page_link='requires/daily_finished_fabric_production_report_controller.php?action=bookingnumbershow&company_id='+companyID+'&cbo_unit_id='+cbo_unit_id+'&cbo_year_id='+cbo_year_id+'&cbo_production_type='+cbo_production_type;
		var title='Booking No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=640px,height=400px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var booking_no=this.contentDoc.getElementById("hide_booking_no").value;
			var booking_id=this.contentDoc.getElementById("hide_booking_id").value;
			$('#txt_booking_no').val(booking_no);
			$('#txt_booking_id').val(booking_id);	 
		}
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
	function fields_activity(company_id){
		if(company_id==0){
			$('#txt_booking_no').val("");	
			$('#txt_booking_id').val("");
			$('#txt_order_no').val("");	
			$('#txt_order_id').val("");	
			$('#txt_batch_no').val("");	
			$('#txt_batch_id').val("");	
			$('#txt_job_no').val("");	
			$('#txt_job_id').val("");	
			$('#txt_file_no').val("");	
			$('#txt_int_ref_no').val("");	
			//$('#cbo_production_type').val(0);

			$('#txt_booking_no').attr("disabled", 'disabled');
			$('#txt_booking_id').attr("disabled", 'disabled');
			$('#txt_order_no').attr("disabled", 'disabled');
			$('#txt_order_id').attr("disabled", 'disabled');
			$('#txt_batch_no').attr("disabled", 'disabled');
			$('#txt_batch_id').attr("disabled", 'disabled');
			$('#txt_job_id').attr("disabled", 'disabled');
			$('#txt_job_no').attr("disabled", 'disabled');	
			$('#txt_file_no').attr("disabled", 'disabled');
			$('#txt_int_ref_no').attr("disabled", 'disabled');
			//$('#cbo_production_type').attr("disabled", 'disabled');
		}
		else{
			$('#txt_booking_no').attr("disabled", false);
			$('#txt_booking_id').attr("disabled", false);
			$('#txt_order_no').attr("disabled", false);
			$('#txt_order_id').attr("disabled", false);
			$('#txt_batch_no').attr("disabled", false);
			$('#txt_batch_id').attr("disabled", false);
			$('#txt_job_id').attr("disabled", false);
			$('#txt_job_no').attr("disabled", false);	
			$('#txt_file_no').attr("disabled", false);
			$('#txt_int_ref_no').attr("disabled", false);
			//$('#cbo_production_type').attr("disabled", false);
		}
		$('#cbo_unit_id').val(0);	
	}
	function refresh_knitting_company(unitType)
	{
		if (unitType==3) {
			$('#cbo_working_company').val(0);	
			$('#cbo_working_location').val(0);
			$('#cbo_working_floor').val(0);
			$('#cbo_working_location').attr("disabled", 'disabled');
			$('#cbo_working_floor').attr("disabled", 'disabled');
		}
		else{

			$('#cbo_working_location').attr("disabled", false);
			$('#cbo_working_floor').attr("disabled", false);
		}
	}
</script>
</head>
<body onLoad="set_hotkey();fields_activity(0);">
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",''); ?>
		 <form name="dailydyeingprodreport_1" id="dailydyeingprodreport_1"> 
         <h3 style="width:1970px; margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" style="width:1970px" >      
             <fieldset>
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                    <thead>
                        <th class="must_entry_caption" width="150">Company Name</th>
                         <th width="150">Unit Name</th>
                        <th width="100">Working Source</th>
                        <th width="150">Working Company</th>
                        <th width="150">Working Location</th>
                        <th width="140">Working Floor</th>
                        <th width="100">Job No </th>
                        <th width="100">Booking No</th>
                        <th width="100">Order No </th>
                        <th width="100">Batch No </th>
                        <th width="100">File No </th>
                        <th width="100">Int. Ref. No </th>
                        <th width="100">Production Type</th>
                       
                        <th id="must_entry_caption_date" width="160">Production Date</th>
                        <th colspan="3"><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('dailydyeingprodreport_1','report_container*report_container2','','','')" class="formbutton" style="width:70px" /></th>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> 
                                <?
                                    echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down('requires/daily_finished_fabric_production_report_controller', this.value, 'load_drop_down_unit', 'unit_name_td' );fields_activity(this.value);" );
                                ?>
                            </td>
                            <td id="unit_name_td">
                                <?
                                    echo create_drop_down("cbo_unit_id",140,$blank_array,"", 1, "-- All --", 0,"",0,'');
                                ?>
                            </td>
                            <td>
                                <?
                                    echo create_drop_down("cbo_working_source",100,$knitting_source,"", 1, "-- Select --", 0,"load_drop_down( 'requires/daily_finished_fabric_production_report_controller', this.value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_knitting_com','knitting_com');refresh_knitting_company(this.value);",0,'1,3');
                                ?>
                            </td>
                            <td id="knitting_com">
                                <?
                                    echo create_drop_down("cbo_working_company",140,$blank_array,"", 1, "-- All --", 0,"",0,'');
                                ?>
                            </td>
                            <td id="knitting_location_td">
                                <?
                                    echo create_drop_down("cbo_working_location",140,$blank_array,"", 1, "-- All --", 0,"",0,'');
                                ?>
                            </td>
                            <td id="knitting_floor_td">
                                <?
                                    echo create_drop_down("cbo_working_floor",140,$blank_array,"", 1, "-- All --", 0,"",0,'');
                                ?>
                            </td>
                            
                            <td>
                            <input style="width:90px;" name="txt_job_no" id="txt_job_no" class="text_boxes" onDblClick="openmypage_job()" placeholder="Browse/Write"  />
                            <input type="hidden" name="txt_job_id" id="txt_job_id"/>
                      	 	</td>
                      	 	<td>
                            <input style="width:90px;" name="txt_booking_no" id="txt_booking_no" class="text_boxes" onDblClick="openmypage_booking()" placeholder="Browse/Write"  />
                            <input type="hidden" name="txt_booking_id" id="txt_booking_id"/>
                      	 	</td>
                            <td>
                            <input style="width:90px;" name="txt_order_no" id="txt_order_no" class="text_boxes" onDblClick="openmypage_order()" placeholder="Browse/Write"  />
                            <input type="hidden" name="txt_order_id" id="txt_order_id"/>
                      	 	</td>
                      	 	<td>
                            	<input style="width:90px;" name="txt_batch_no" id="txt_batch_no" class="text_boxes" onDblClick="openmypage_batch()" placeholder="Browse/Write" onkeyup="$('#txt_batch_id').val('');" />
                            	<input type="hidden" name="txt_batch_id" id="txt_batch_id"/>
                      	 	</td>
                            <td><input type="text" style="width:90px;" name="txt_file_no" id="txt_file_no" class="text_boxes"  placeholder="Write"  /></td>
                            <td><input type="text" style="width:90px;" name="txt_int_ref_no" id="txt_int_ref_no" class="text_boxes" placeholder="Write"  /></td>
                            <td align="center">
                                    <?
                                    $production_type = array(0 => "ALL", 1 => "Sample with order", 2 => "Sample without order");
                                    echo create_drop_down("cbo_production_type", 80, $production_type, "", 0, "", 0, "", "");
                                    ?>
                            </td>
                            <td align="center">
                             <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:55px" placeholder="From Date"/>
                             To
                             <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:55px" placeholder="To Date"/>
                            </td>
                            <td align="center"><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(1)" /></td>
                            <td align="center"><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show 2" onClick="fn_report_generated(2)" /></td>
                            <td align="center"><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show 3" onClick="fn_report_generated(3)" /></td>
                        </tr>
                        <tr>
                            <td colspan="14" align="center" >
							
							 <? 
							//echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );		
							
							 echo load_month_buttons(1); ?></td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
		</form>
      <div id="report_container" style="width:1500px;" margin:0 auto;></div>
    <div id="report_container2" style="width:1500px; margin:0 auto; text-align:center;"></div>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
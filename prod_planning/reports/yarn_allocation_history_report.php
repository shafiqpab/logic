<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Sewing date wise yarn allocation report.
Functionality	:	
JS Functions	:
Created by		:	Tipu 
Creation date 	: 	04-08-2018
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
echo load_html_head_contents("Style Wise Shipment Report","../../", 1, 1, $unicode,1,1);
?>	
<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	
	/* var tableFilters = 
	 {
		col_33: "none",
		col_operation: {
		id: ["total_order_qnty","total_order_qnty_in_pcs","value_tot_cm_cost","value_tot_cost","value_order","value_margin","value_tot_trims_cost","value_tot_embell_cost"],
	    col: [9,11,25,26,29,30,31,32],
	    operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
	    write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	 } */

	 	
	 var tableFilters = 
	 { 
		col_0: "none", 
		col_operation: {
			id: ["value_total_allocation_qty"],
			col: [12],
			operation: ["sum"],
			write_method: ["innerHTML"]
		}
	}
	 
	 function openmypage_job()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_name").val();
		var cbo_year_id = $("#cbo_job_year_id").val();
		//var cbo_month_id = $("#cbo_month").val();
		var page_link='requires/yarn_allocation_history_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
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
	
	function openmypage_booking()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_job_no').value;
		//alert (data);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/yarn_allocation_history_report_controller.php?action=booking_no_popup&data='+data,'Booking No Popup', 'width=630px,height=420px,center=1,resize=0','../')
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("txt_wo_id");
			var theemailv=this.contentDoc.getElementById("txt_wo_no");
			var response=theemail.value.split('_');
			if (theemail.value!="")
			{
				freeze_window(5);
				document.getElementById("txt_booking_id").value=theemail.value;
			    document.getElementById("txt_booking_no").value=theemailv.value;
				release_freezing();
			}
		}
	}
	
	function fnc_report_generated()
	{
		var cbo_company=document.getElementById('cbo_company_id').value;
		var cbo_buyer=document.getElementById('cbo_buyer_name').value; 
		var job_no=document.getElementById('txt_job_no').value;
		var job_id=document.getElementById('txt_job_id').value;
		var booking_no=document.getElementById('txt_booking_no').value;
		var date_from=document.getElementById('txt_date_from').value;
		var date_to=document.getElementById('txt_date_to').value;
		
		if(cbo_company==0 || job_no==0 || booking_no=='' || (date_from=='' || date_to=='')){
			var divData="cbo_company_id*txt_date_from*txt_date_to*txt_job_no*txt_booking_no";	
			var msgData="Company Name*From Date*To Date*Job No*Booking No";
		}
		else{
			var divData="cbo_company_id*txt_date_from*txt_date_to*txt_job_no*txt_booking_no";
			var msgData="Company Name*From Date*To Date*Job No*Booking No";	
		}
		/*if(cbo_buyer==0 && cbo_company==0){
			var divData="cbo_company_id*cbo_buyer";	
			var msgData="Company Name*Buyer";	
		}
		else{
			var divData="cbo_company_id";	
			var msgData="Company Name";	
		}*/
		
		if(cbo_company==0 || (date_from=='' || date_to=='') && job_no==0 && booking_no==0)
		{
			if(form_validation(divData,msgData)==false)
			{
				return;
			}
		}

/*		if(form_validation('cbo_company_id*txt_job_no','Company Name*Job No')==false)
		{
			return;
		}
		*/
		else
		{	
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_name*txt_date_from*txt_date_to*cbo_job_year_id*txt_job_no*txt_job_id*txt_booking_no',"../../")+'&report_title='+report_title;
			freeze_window(3);
			http.open("POST","requires/yarn_allocation_history_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
		
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			//var reponse=trim(http.responseText).split("****");
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);
			//alert(reponse[0]);  
			
			
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		

			//setFilterGrid("tbl_list_search",-1,tableFilters);
			setFilterGrid("tbl_list_search",-1);

	 		show_msg('3');
			release_freezing();
		}
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#scroll_body tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="380px";
		$('#scroll_body tr:first').show();
		//document.getElementById('scroll_body').style.maxWidth="120px";
	}
	
	
</script>
</head>
 
<body onLoad="set_hotkey();">
		 
<form id="date_wise_yarn_allocation">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:920px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:920px;">
                <table class="rpt_table" width="910" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                	<thead>
                    	<tr>                   
                            <th class="must_entry_caption">Company Name</th>
                            <th>Buyer Name</th>
                            <th>Job Year</th>
                            <th>Job No</th>
                            <th>Booking No</th>
                            <th>Allocation Date</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" /></th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr class="general">
                        <td> 
                           <?
								echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/yarn_allocation_history_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                          <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                            ?>
                        </td>
						<td>
							<? 
								$selected_year=date("Y");
								echo create_drop_down( "cbo_job_year_id", 60, $year,"", 1, "--Year--", $selected_year, "",0,"","" );
                            ?>
                        </td>                         <td>
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:80px" onDblClick="openmypage_job();" placeholder="Wr./Br. Job" />
                             <input type="hidden" id="txt_job_id" name="txt_job_id"/>
                        </td>
                        <td>
                       <input type="text" id="txt_booking_no" name="txt_booking_no" class="text_boxes" style="width:80px" onDblClick="openmypage_booking();" placeholder="Wr./Br. No" />
                            <input type="hidden" id="txt_booking_id" name="txt_booking_id"/>
                        </td>
                       
                        <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" >&nbsp; To
                        <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date" ></td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fnc_report_generated()" />
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
            </fieldset>
        </div>
    </div>
    <div id="report_container" align="center"></div>
    <div align="center" id="report_container2"></div>
   
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

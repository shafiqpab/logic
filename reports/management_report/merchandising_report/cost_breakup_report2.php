<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create  Cost Break Up Report.
Functionality	:	
JS Functions	:
Created by		:	Aziz 
Creation date 	: 	14-08-2016
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------

echo load_html_head_contents("Cost Break Up Report","../../../", 1, 1, $unicode,1,1);
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
			
	function fn_report_generated(type)
	{
			var comments_head='';
			if(type==5 || type==3)
			{
				var comments_head=0;
			//var r=confirm("Press  \"OK\"  to show All Part\nPress  \"Cancel\"  to show Only Master");
			}
			else if(type==6 || type==4)
			{
				var comments_head=1;
			}
			//if (r==true) comments_head="1"; else comments_head="0";
		var txt_style_ref=document.getElementById('txt_style_ref').value;	
		var order_no=document.getElementById('txt_order').value;
		var file_no=document.getElementById('txt_file_no').value;
		var season=document.getElementById('txt_season').value;
		var company_name=document.getElementById('cbo_company_name').value;
		var buyer_name=document.getElementById('cbo_buyer_name').value;
		var txt_quotation_id=document.getElementById('txt_quotation_id').value;
		
		/*if(buyer_name==0)
			{
				if(form_validation('cbo_buyer_name','Buyer')==false)
					{
						return;
					}
			}*/
		
		if (type==1 || type==2 || type==3 || type==4 || type==7 || type==8)
		{
			if(txt_style_ref!="" || order_no!="" || file_no!="" || season!="" || buyer_name!=0)
			{
				if(form_validation('cbo_company_name','Company')==false)
				{
					return;
				}
			}
			else
			{
				if(form_validation('cbo_company_name*cbo_buyer_name*txt_style_ref','Company*Buyer*Style')==false)
					{
						return;
					}
			}
		}
		if(type==5 || type==6)//Quotation
		{
			if(txt_style_ref!="" || season!="" || txt_quotation_id!="")
			{
				if(form_validation('cbo_company_name','Company')==false)
				{
					return;
				}
			}
			else
			{
				if(form_validation('cbo_company_name*cbo_buyer_name*txt_quotation_id','Company*Buyer*Quotation')==false)
				{
					return;
				}
			}
		}
		var sign=1;
		var report_title=$( "div.form_caption" ).html();	
		var data="action=report_generate&reporttype="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_style_ref*txt_style_ref_id*txt_order*txt_order_id*txt_season*txt_season_id*txt_file_no*txt_file_id*txt_quotation_id*txt_hidden_quot_id*cbo_file_year',"../../../")+'&comments_head='+comments_head+'&report_title='+report_title+'&sign='+sign;
		//alert(data);return;
		freeze_window(3);
		if(type==1 || type==2 || type==3 || type==4 || type==5 || type==6 || type==7 || type==8) 
		{
			http.open("POST","requires/cost_breakup_report2_controller.php",true);
		}
		
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			//alert(reponse[0]);
			//alert(reponse[2]);
			//var tot_rows=reponse[0];
			$('#report_container2').html(reponse[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../../');
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1);" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
				var tableFilters = {
			  //col_10:'none',
			 // display_all_text: " ---Show All---",
					col_operation: {
					id: ["value_total_fab_qnty","value_total_trims_amount"],
					col: [9,11],
					operation: ["sum","sum"],
					write_method: ["innerHTML","innerHTML"]
					}
				}
				setFilterGrid("table_body_accss",-1,tableFilters);
				/*
				setFilterGrid("table_body1",-1);
				setFilterGrid("table_body2",-1);
				setFilterGrid("table_body3",-1);
				setFilterGrid("table_body4",-1);
				setFilterGrid("table_body5",-1);
				setFilterGrid("table_body6",-1);
				setFilterGrid("table_body7",-1);
			*/
			
			//append_report_checkbox('table_header_1',1);
			//show_graph( '', document.getElementById('graph_data').value, "pie", "chartdiv", "", "../../../", '',580,700 );
			release_freezing();
			show_msg('3');
		}
	}
	
	function new_window(type)
	{
		
		//document.getElementById('scroll_body1').style.overflow="auto";
		//document.getElementById('scroll_body1').style.maxHeight="none";
		//document.getElementById('scroll_body2').style.overflow="auto";
		//document.getElementById('scroll_body2').style.maxHeight="none";
		// alert(type);
		  
		 $('.scroll_div_inner').css('overflow','auto');
		 $('.scroll_div_inner').css('maxHeight','none');
		 
		$("#table_body_accss tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_print.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		$('.scroll_div_inner').css('overflow','scroll');
		$('.scroll_div_inner').css('maxHeight','480px');
		 /*
		document.getElementById('scroll_body1').style.overflowY="scroll"; 
		document.getElementById('scroll_body1').style.maxHeight="480px";
		document.getElementById('scroll_body2').style.overflowY="scroll"; 
		document.getElementById('scroll_body2').style.maxHeight="480px";
		document.getElementById('scroll_body1').style.overflowY="scroll"; 
		document.getElementById('scroll_body1').style.maxHeight="480px";
		document.getElementById('scroll_body1').style.overflowY="scroll"; 
		document.getElementById('scroll_body1').style.maxHeight="480px";
		*/
		$("#table_body_accss tr:first").show();
	}
	
	
	
	function openmypage_quotation()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();
		var cbo_year = $("#cbo_year").val();
		var txt_season_id = $("#txt_season_id").val();
		var txt_season = $("#txt_season").val();
		
		var page_link='requires/cost_breakup_report2_controller.php?action=quotation_popup&companyID='+company+'&buyer_name='+buyer+'&txt_season_id='+txt_season_id+'&txt_season='+txt_season+'&cbo_year='+cbo_year;
		var title="Search Quotation Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var order_no=this.contentDoc.getElementById("hide_order_no").value;
			var order_id=this.contentDoc.getElementById("hide_order_id").value;
			$("#txt_quotation_id").val(order_no);
			$("#txt_hidden_quot_id").val(order_id); 
			
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
		var cbo_year = $("#cbo_year").val();
		var txt_style_ref_no = $("#txt_style_ref_no").val();
		var txt_style_ref_id = $("#txt_style_ref_id").val();
		var txt_style_ref = $("#txt_style_ref").val();
		var page_link='requires/cost_breakup_report2_controller.php?action=style_refarence_search&companyID='+company+'&buyer_name='+buyer+'&txt_style_ref_no='+txt_style_ref_no+'&txt_style_ref_id='+txt_style_ref_id+'&txt_style_ref='+txt_style_ref+'&cbo_year_id='+cbo_year;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var style_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_no);
			$("#txt_style_ref").val(style_des);
			$("#txt_style_ref_id").val(style_id); 
			$("#txt_style_ref_no").val(style_no); 
		}
	}
	
	function openmypage_order() 
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();	
		var txt_style_ref = $("#txt_style_ref").val();
		var style_ref_id = $("#txt_style_ref_id").val();
		var cbo_year = $("#cbo_year").val();
		var txt_order_id_no = $("#txt_order_id_no").val();
		var txt_order_id = $("#txt_order_id").val();
		var txt_order = $("#txt_order").val();
		var page_link='requires/cost_breakup_report2_controller.php?action=order_search&company='+company+'&buyer_name='+buyer+'&txt_order_id_no='+txt_order_id_no+'&txt_order_id='+txt_order_id+'&txt_order='+txt_order+'&txt_style_ref='+txt_style_ref+'&cbo_year='+cbo_year+'&style_ref_id='+style_ref_id; 
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_des_no);
			$("#txt_order").val(style_des);
			$("#txt_order_id").val(style_id); 
			$("#txt_order_id_no").val(style_des_no);
		}
	}
	
	function openmypage_file_order() 
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();	
		var txt_style_ref = $("#txt_style_ref").val();
		var style_ref_id = $("#txt_style_ref_id").val();
		var cbo_year = $("#cbo_year").val();
		var txt_order_id_no = $("#txt_order_id_no").val();
		var txt_order_id = $("#txt_order_id").val();
		var txt_order = $("#txt_order").val();
		var page_link='requires/cost_breakup_report2_controller.php?action=order_search_file&company='+company+'&buyer_name='+buyer+'&txt_order_id_no='+txt_order_id_no+'&txt_order_id='+txt_order_id+'&txt_order='+txt_order+'&txt_style_ref='+txt_style_ref+'&cbo_year='+cbo_year+'&style_ref_id='+style_ref_id; 
		var title="Search File Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=770px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			var file_year=this.contentDoc.getElementById("txt_selected_year").value; // product Description
			// alert(file_year);
			$("#txt_file_no").val(style_des);
			$("#txt_file_id").val(style_id); 
			$("#txt_file_id_no").val(style_des_no);
			$("#cbo_file_year").val(file_year);
			$( "#cbo_file_year" ).prop( "disabled", true );
			
		}
	}
	
	function search_populate(str)
	{
		if(str==1)
		{
			document.getElementById('search_by_th_up').innerHTML="Shipment Date";
			$('#search_by_th_up').css('color','blue');
		}
		else if(str==2)
		{
			document.getElementById('search_by_th_up').innerHTML="PO Received Date";
			$('#search_by_th_up').css('color','blue');
		}
		else if(str==3)
		{
			document.getElementById('search_by_th_up').innerHTML="PO Insert Date";
			$('#search_by_th_up').css('color','blue');
		}
	}
	
	function openmypage_season()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		var companyID = $("#cbo_company_name").val();
		var buyerID = $("#cbo_buyer_name").val();
		var job_no = $("#txt_job_no").val();
		var page_link='requires/cost_breakup_report2_controller.php?action=season_search_popup&companyID='+companyID+'&buyerID='+buyerID+'&job_no='+job_no;
		var title='Season Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=350px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var hide_season=this.contentDoc.getElementById("hide_season").value;
			var hide_season_id=this.contentDoc.getElementById("hide_season_id").value;
	
			$('#txt_season').val(hide_season);
			$('#txt_season_id').val(hide_season_id);
		}
	}
	
	 
	function generate_worder_report(txt_booking_no,cbo_company_name,txt_order_no_id,cbo_fabric_natu,cbo_fabric_source,txt_job_no,id_approved_id,print_id,type,i)
	{
		//var report_title='Budget Wise Fabric Booking';
		if(print_id==45) var report_title='Budget Wise Fabric Booking';
		else if(print_id==46) var report_title='Short Fabric Booking';
		else if(print_id==67) var report_title='Multiple Job Wise Trims Booking Urmi';
		else if(print_id==61) var report_title='Service Booking For AOP';
		else if(print_id==100) var report_title='Embellishment Work Order Urmi'; 
		else if(print_id==108) var report_title='Partial Fabric Booking ';
		var data="action="+type+
		'&txt_booking_no='+"'"+txt_booking_no+"'"+
		'&cbo_company_name='+"'"+cbo_company_name+"'"+
		'&txt_order_no_id='+"'"+txt_order_no_id+"'"+
		'&cbo_fabric_natu='+"'"+cbo_fabric_natu+"'"+
		'&cbo_fabric_source='+"'"+cbo_fabric_source+"'"+
		'&id_approved_id='+"'"+id_approved_id+"'"+
		'&report_title='+""+report_title+""+
		'&txt_job_no='+"'"+txt_job_no+"'"+
		'&path=../';
		
		freeze_window(5);
		//http.open("POST","requires/fabric_booking_controller.php",true);
		if(print_id==45 || print_id==53)
		{
			//alert(print_id);
			http.open("POST","../../../order/woven_order/requires/fabric_booking_urmi_controller.php",true);
		}
		if(print_id==46)
		{
			//alert(print_id);
			http.open("POST","../../../order/woven_order/requires/short_fabric_booking_controller.php",true);
		}
		if(print_id==67)
		{
			//alert(print_id);
			http.open("POST","../../../order/woven_order/requires/trims_booking_multi_job_controllerurmi.php",true);
		}
		if(print_id==61) //AOP Urmi
		{
			//alert(print_id);
			http.open("POST","../../../order/woven_order/requires/service_booking_aop_urmi_controller.php",true);
		}
		if(print_id==100) //Embl Urmi
		{
			//alert(print_id);
			http.open("POST","../../../order/woven_order/requires/print_booking_urmi_controller.php",true);
		}
		
		if(print_id==101) //LabTest Urmi
		{
			//alert(print_id);
			http.open("POST","../../../order/woven_order/requires/labtest_work_order_controller.php",true);
		}
		if(print_id==108) //Partial Urmi
		{
			//alert(print_id);
			http.open("POST","../../../order/woven_order/requires/partial_fabric_booking_controller.php",true);
		}
		
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{
			if(http.readyState == 4) 
		    {
				var w = window.open("Surprise", "_blank");
				var d = w.document.open();
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_print.css" type="text/css" media="print" /><title></title></head><body>'+http.responseText+'</body</html>');
				d.close();
				release_freezing();
		   }
			
		}
	}
	
	function generate_worder_report3(cbo_company_name,update_id,print_id,type,i)
	{
		var report_title='Lab Test Work Order';
		print_report(cbo_company_name+'*'+update_id+'*'+0+'*'+0+'*'+report_title, "show_trim_booking_report","../../../order/woven_order/requires/labtest_work_order_controller" ) ;
	}

function print_report_button_setting(report_ids)
{
	$("#show_button_1").hide();
	$("#show_button_2").hide();
	$("#show_button_3").hide();
	$("#show_button_4").hide();
	$("#show_button_5").hide();
	$("#show_button_6").hide();
	$("#show_button_7").hide();
	$("#show_button_8").hide();
	
	var report_id=report_ids.split(",");
	for (var k=0; k<report_id.length; k++)
	{
		if(report_id[k]==24) $("#show_button_1").show();	 
		if(report_id[k]==25) $("#show_button_2").show();	 
		if(report_id[k]==214) $("#show_button_3").show();	 
		if(report_id[k]==215) $("#show_button_4").show();	 
		if(report_id[k]==216) $("#show_button_5").show();	 
		if(report_id[k]==217) $("#show_button_6").show();
		if(report_id[k]==268) $("#show_button_7").show();
		if(report_id[k]==53) $("#show_button_8").show();	
	}
}



	
</script>
<style>
 /* The Modal (background) */
.modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

 /* Modal Header */
.modal-header {
    padding: 2px 16px;
    background-color: #999;
    color: white;
}

/* Modal Body */
.modal-body {padding: 2px 16px;}

/* Modal Footer */
.modal-footer {
    padding: 2px 16px;
    background-color: #999;
    color: white;
}

/* Modal Content */
.modal-content {
    position: relative;
    background-color: #fefefe;
    margin: auto;
    padding: 0;
    border: 1px solid #888;
    width: 80%;
    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
    -webkit-animation-name: animatetop;
    -webkit-animation-duration: 0.4s;
    animation-name: animatetop;
    animation-duration: 0.4s
}

/* Add Animation */
@-webkit-keyframes animatetop {
    from {top: 300px; opacity: 0}
    to {top: 0; opacity: 1}
}

@keyframes animatetop {
    from {top: 300px; opacity: 0}
    to {top: 0; opacity: 1}
}

/* The Close Button */
.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}
</style> 
</head>
<body onLoad="set_hotkey();">
<form id="costSheetReport_1">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../../",$permission);  ?>
          <h3 align="left" id="accordion_h1" style="width:1200px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
         <fieldset style="width:1200px;" id="content_search_panel">
            <table class="rpt_table" width="1200" cellpadding="1" cellspacing="2" align="center" border="1" rules="all">
                <thead>                    
                    <th class="must_entry_caption">Company</th>
                    <th class="must_entry_caption">Buyer</th>
                    <th>Season</th>
                    <th  class="must_entry_caption">Quotation ID</th>
                     <th  class="must_entry_caption">Style Ref</th>
                    <th>Order</th>
                    <th title="Comma Separate File No">File No</th>
                    <th >File Year</th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                    </thead>
                <tbody>
                    <tr class="general">
                        <td width="150"> 
							<?
							
                           		echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/cost_breakup_report2_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/cost_breakup_report2_controller',this.value, 'load_drop_down_file_year', 'file_year_td' );get_php_form_data(this.value, 'report_button_setting', 'requires/cost_breakup_report2_controller');" );
                            ?>
                        </td>
                        
                        
                        <td id="buyer_td" width="130">
							<? 
                            	echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "--All Buyer--", $selected, "",0,"" );
                            ?>
                        </td>
                         <td width="90">
                          <input type="text" name="txt_season" id="txt_season" class="text_boxes" style="width:80px" placeholder="Write/Browse" onDblClick="openmypage_season();" /> 
                           <input type="hidden" name="txt_season_id" id="txt_season_id"/>   
                         </td>
                        <td width="90">
                             <input style="width:80px;" name="txt_quotation_id" id="txt_quotation_id" onDblClick="openmypage_quotation(1)" class="text_boxes" placeholder="Browse or Write"/>
                               <input type="hidden" name="txt_hidden_quot_id" id="txt_hidden_quot_id"/> 
                             
                        </td>
                        <td width="110">
                             <input style="width:100px;" name="txt_style_ref" id="txt_style_ref" onDblClick="openmypage_style(2)" class="text_boxes" placeholder="Browse or Write"/>
                            <input type="hidden" name="txt_style_ref_id" id="txt_style_ref_id"/>   
                             <input type="hidden" name="txt_style_ref_no" id="txt_style_ref_no"/>  
                        </td>
                        <td width="110">
                        <input style="width:100px;" name="txt_order" id="txt_order" onDblClick="openmypage_order()" class="text_boxes" placeholder="Browse or Write" />   
                            <input type="hidden" name="txt_order_id" id="txt_order_id"/> <input type="hidden" name="txt_order_id_no" id="txt_order_id_no"/>     
                        </td>
                        <td width="80">
                        	<input style="width:70px;" name="txt_file_no" id="txt_file_no" onDblClick="openmypage_file_order()"  class="text_boxes" placeholder="Write/Browse" /> 
                        	 <input type="hidden" name="txt_file_id" id="txt_file_id"/> 
                              <input type="hidden" name="txt_file_id_no" id="txt_file_id_no"/>   
							  <input type="hidden" name="hidden_file_year" id="hidden_file_year"/>   
                        </td>
                        <td id="file_year_td" width="80">
                        	<? 
                            	echo create_drop_down( "cbo_file_year", 80, $blank_array,"", 1, "--Select--", $selected, "",0,"" );
                            ?>
                        </td>
                         <td>
                            <input type="button" id="show_button_1" class="formbutton" style="width:50px; display:none;" value="Budget" onClick="fn_report_generated(1)" />
                            <input type="button" id="show_button_2" class="formbutton" style="width:55px; display:none;" value="Budget2" onClick="fn_report_generated(2)" />
                            <input type="button" id="show_button_3" class="formbutton" style="width:120px; display:none;" value="Budget3 Summary" onClick="fn_report_generated(3)" />
							 <input type="button" id="show_button_7" class="formbutton" style="width:50px; display:none;" value="Budget4" onClick="fn_report_generated(7)" />
                            
                        </td>
                        
                    </tr>
                  <tr align="right"  class="general">
                        <td colspan="10" align="right" >
                        	<p style=" margin-left:350px">
                        	 <input type="button" id="show_button_4" class="formbutton" style="width:120px; display:none;" value="Budget3 Details" onClick="fn_report_generated(4)" />
                              &nbsp; 
                             <input type="button" id="show_button_5" class="formbutton" style="width:120px; display:none;" value="LC Cost Summary" onClick="fn_report_generated(5)" />
                             &nbsp; 
                             <input type="button" id="show_button_6" class="formbutton" style="width:110px; display:none;" value="LC Cost Details" onClick="fn_report_generated(6)" />
                            &nbsp;   <input type="button" id="show_button_8" class="formbutton" style="width:50px; display:none;" value="Budget5" onClick="fn_report_generated(8)" />
                             </p>
                        </td>
                    </tr>
                </table> 
            </fieldset>
        </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
  
 </form>    
   <input type="button" id="myBtn" value="OPen" style="display:none"/>
    <div id="myModal" class="modal">
  <div class="modal-content">
  <div class="modal-header">
    <span class="close">×</span>
    <h2>Po Number</h2>
  </div>
  <div class="modal-body">
    <p id="ccc">Some text in the Modal Body</p>
   
  </div>
  <div class="modal-footer">
    <h3></h3>
  </div>
</div>

</div>
<script>
//============modal=========
var modal = document.getElementById('myModal');

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks on the button, open the modal
btn.onclick = function() {
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

function setdata_po(data){
	
	document.getElementById('ccc').innerHTML=data;
	document.getElementById('myBtn').click();
}
</script>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
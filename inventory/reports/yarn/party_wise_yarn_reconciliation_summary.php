<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Party Wise Yarn Reconciliation Summary
				
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	20-09-2017
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
echo load_html_head_contents("Party Wise Yarn Reconciliation Summary","../../../", 1, 1, $unicode,0,0); 

?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report(type)
	{
		var job_no=$('#txt_job_no').val();
		var booking_no=$('#txt_booking_no').val();
		var internal_ref=$('#txt_internal_ref').val();
		var txt_fso_no=$('#txt_fso_no').val();
		
		
		if(type==2 && txt_fso_no!="")
		{
			alert("FSO No Not Applicable For This Button");
			$('#txt_fso_no').val("");
			return;
		}
		if(type==8 && txt_fso_no!="")
		{
			alert("FSO No Not Applicable For This Button");
			$('#txt_fso_no').val("");
			return;
		}
			
		if(job_no!="" || booking_no!="" || internal_ref!="" || txt_fso_no!="")
		{
			if( form_validation('cbo_company_name','Company Name')==false)
			{
				return;
			}
		}
		else 
		{
			if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Booking')==false)
			{
				return;
			}
		}
			
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_knitting_source*txt_knitting_com_id*txt_job_no*txt_booking_no*txt_internal_ref*txt_fso_no*txt_date_from*txt_date_to*txt_job_id*hide_booking_id*hide_booking_type*hide_fso_id*cbo_year',"../../../")+'&report_title='+report_title+'&type='+type;
		//alert(data);
		freeze_window(3);
		http.open("POST","requires/party_wise_yarn_reconciliation_summary_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}

	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			show_msg('3');
			release_freezing();
		}
	} 
	
	
	
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none"; 
		//$("#table_body tr:first").hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflow="auto"; 
		document.getElementById('scroll_body').style.maxHeight="250px";
	}

	function openmypage_job() 
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_name").val();
		var cbo_knitting_source = $("#cbo_knitting_source").val();
		var txt_knitting_com_id = $("#txt_knitting_com_id").val();
		var cbo_year = $("#cbo_year").val();
		
		var page_link='requires/party_wise_yarn_reconciliation_summary_controller.php?action=job_no_popup&companyID='+companyID+'&cbo_knitting_source='+cbo_knitting_source+'&txt_knitting_com_id='+txt_knitting_com_id+'&cbo_year='+cbo_year;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../../');
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
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_name").val();
		var cbo_knitting_source = $("#cbo_knitting_source").val();
		var txt_knitting_com_id = $("#txt_knitting_com_id").val();
		var cbo_year = $("#cbo_year").val();
		var page_link='requires/party_wise_yarn_reconciliation_summary_controller.php?action=booking_no_popup&companyID='+companyID+'&cbo_knitting_source='+cbo_knitting_source+'&txt_knitting_com_id='+txt_knitting_com_id+'&cbo_year='+cbo_year;
		var title='Booking No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=710px,height=370px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			var booing_type=this.contentDoc.getElementById("hide_booing_type").value;
			
			//alert(hide_recv_id);
			$('#txt_booking_no').val(job_no);
			$('#hide_booking_id').val(job_id);
			$('#hide_booking_type').val(booing_type);
			
		}
	}
	
	function openmypage_party()
	{
		if( form_validation('cbo_company_name*cbo_knitting_source','Company Name* Knitting source')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_name").val();
		var cbo_knitting_source = $("#cbo_knitting_source").val();
		var txt_knit_comp_id = $("#txt_knit_comp_id").val();
		var page_link='requires/party_wise_yarn_reconciliation_summary_controller.php?action=party_popup&companyID='+companyID+'&cbo_knitting_source='+cbo_knitting_source+'&txt_knit_comp_id='+txt_knit_comp_id;
		var title='Party Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=430px,height=370px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var party_name=this.contentDoc.getElementById("hide_party_name").value;
			var party_id=this.contentDoc.getElementById("hide_party_id").value;
			
			$('#txt_knitting_company').val(party_name);
			$('#txt_knitting_com_id').val(party_id);	 
		}
	}
	
	function kniting_company_val()
	{
		$('#txt_knitting_company').val('');
		$('#txt_knitting_com_id').val('');	 
	}
	
	function print_button_setting() //Report Settins
	{
		if( form_validation('cbo_company_name','Company')==false )
		{
			return;
		}
		$('#data_panel').html('');
		get_php_form_data($('#cbo_company_name').val(),'print_button_variable_setting','requires/party_wise_yarn_reconciliation_summary_controller' ); 
	}
	
	
	function openmypage_balance_popup(source_id,knit_party_id,com_id,booking_no,hide_basis_id,hide_booking_id,internal_ref,job_no,challan_no,from_date,to_date,action,tittle,popup_type,popup_width)
	{
		//alert(action);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/party_wise_yarn_reconciliation_summary_controller.php?com_id='+com_id+'&source_id='+source_id+'&knit_party_id='+knit_party_id+'&job_no='+job_no+'&internal_ref='+internal_ref+'&hide_booking_id='+hide_booking_id+'&hide_basis_id='+hide_basis_id+'&booking_no='+booking_no+'&from_date='+from_date+'&to_date='+to_date+'&action='+action, tittle, 'width='+popup_width+', height=400px, center=1, resize=0, scrolling=0', '../../');
	}

	function openmypage_FSO_No() 
	{
 		var cbo_company_id = $('#cbo_company_name').val();
	 	if (form_validation('cbo_company_name', 'Company') == false) { return; }
	 	else 
	 	{
 			var title = 'FSO Selection Form';
 			var page_link = 'requires/party_wise_yarn_reconciliation_summary_controller.php?cbo_company_id=' + cbo_company_id + '&action=FSO_No_popup';

 			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=370px,center=1,resize=1,scrolling=0', '../../');

 			emailwindow.onclose = function () 
 			{
	 			var theform=this.contentDoc.forms[0];
				var fso_no=this.contentDoc.getElementById("hide_fso_no").value;
				var fso_id=this.contentDoc.getElementById("hide_fso_id").value;
				//alert (job_no);
				$('#txt_fso_no').val(fso_no);
				$('#hide_fso_id').val(fso_id);	
 			}
 		}
 	}
	
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../../../",$permission); ?><br />    		 
    <form name="PartyWiseYarnReconciliation_1" id="PartyWiseYarnReconciliation_1" autocomplete="off" > 
        <div style="width:100%;" align="center">
            <fieldset style="width:1050px;">
            <legend>Search Panel</legend> 
                <table class="rpt_table" width="1050" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th width="140" class="must_entry_caption">Company</th>                                
                            <th width="80" >Source</th>                              
                            <th width="80" >Party</th>                              
                            <th width="70">Year</th>
                            <th width="80">Job</th>
                            <th width="80">Fabric Booking</th>
                            <th width="100">Internal Ref</th>
                            <th width="100">FSO No</th>
                            <th class="must_entry_caption">Date Range</th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td>
                            <?
                               echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                            ?>                            
                        </td>
                         <td>
							<?
                                echo create_drop_down("cbo_knitting_source",80,$knitting_source,"", 1, "-- Select Source --", 0,"kniting_company_val();",0,'1,3');
                            ?>
                        </td>
                        <td id="knitting_com">
                            <input type="text" id="txt_knitting_company" name="txt_knitting_company" class="text_boxes" style="width:80px" onDblClick="openmypage_party();" placeholder="Browse Party" />
                            <input type="hidden" id="txt_knitting_com_id" name="txt_knitting_com_id" class="text_boxes" style="width:60px" />
                        </td>
                        <td>
							<?
								$selected_year=date("Y");
                                echo create_drop_down( "cbo_year", 70, $year,"", 1, "--Select Year--", $selected_year, "",0 );
                            ?>
                        </td>
                        <td>
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:80px" onDblClick="openmypage_job();" placeholder="Browse Job" />
                            <input type="hidden" id="txt_job_id" name="txt_job_id" class="text_boxes" style="width:60px" />
                        </td>
                        <td>
                            <input type="text" id="txt_booking_no" name="txt_booking_no" class="text_boxes" style="width:80px" onDblClick="openmypage_booking();" placeholder="Browse Booking" readonly />
                            <input type="hidden" id="hide_booking_id" name="hide_booking_id" class="text_boxes" style="width:50px" />
                             <input type="hidden" id="hide_booking_type" name="hide_booking_type" class="text_boxes" style="width:50px" />
                          
                        </td>
                         <td>
                            <input type="text" id="txt_internal_ref" name="txt_internal_ref" class="text_boxes" style="width:100px" />
                        </td>
                        <td>
                        	<input type="text" name="txt_fso_no" id="txt_fso_no" class="text_boxes" style="width:150px;" placeholder="Double Click To Edit" onDblClick="openmypage_FSO_No()" readonly/>

                        	<input id="hide_fso_id" type="hidden" readonly name="hide_fso_id">
                        </td>
                        <td align="center">
                             <input type="text" name="txt_date_from" id="txt_date_from" value="<? //echo date("01-m-Y"); ?>" class="datepicker" style="width:60px" placeholder="From Date"/>
                             To
                             <input type="text" name="txt_date_to" id="txt_date_to" value="<? //echo date("d-m-Y"); ?>" class="datepicker" style="width:60px" placeholder="To Date"/>
                        </td>
                    </tr>
                    <tr class="general">
                        <td colspan="9" align="center" width="95%"><? echo load_month_buttons(1); ?>&nbsp; 
                        <input type="button" name="search" id="search" value="Summary" onClick="generate_report(2)" style="width:60px" class="formbutton" title="Sales Order is not address on this button" />
                        <input type="reset" name="res" id="res" value="Reset" style="width:40px" class="formbutton" onClick="reset_form('PartyWiseYarnReconciliation_1', 'report_container*report_container2','','','','');" /></td>
                         
                    </tr>
                </table>
                <div style="margin-top:10px" id="data_panel">  

                	<input type="button" name="search1" id="search1" value="Sales Order Wise" onClick="generate_report(6)" style="width:120px" class="formbutton" />

                	<input type="button" name="search2" id="search2" value="Sales Summary" onClick="generate_report(7)" style="width:120px" class="formbutton" />
                	<input type="button" name="search3" id="search3" value="Sample Without Order" onClick="generate_report(8)" style="width:120px" class="formbutton" title="Sales Order is not address on this button, Only for Micro Fibre"/>
               
                	<!--<input type="button" name="search" id="search" value="Summary" onClick="generate_report(1)" style="width:100px" class="formbutton" />&nbsp;
                    <input type="button" name="search" id="search" value="Party Wise" onClick="generate_report(2)" style="width:100px" class="formbutton" />&nbsp;
                    <input type="button" name="search" id="search" value="Job Wise" onClick="generate_report_job()" style="width:100px" class="formbutton" />&nbsp;
                    <input type="button" name="search" id="search" value="Challan Wise" onClick="generate_report(3)" style="width:100px" class="formbutton" />&nbsp;
                    <input type="button" name="search" id="search" value="Returnable" onClick="generate_report(4)" style="width:100px" class="formbutton" />&nbsp;
                    
                    <input type="button" name="search" id="search" value="Returnable Without Challan" onClick="generate_report_job(5)" style="width:160px" class="formbutton" />&nbsp;
                  -->  
                   
              
                </div> 
                
                  <input type="hidden" id="hidden_report_ids" name="hidden_report_ids"/>
            </fieldset>  
            
            <div id="report_container" align="center"></div>
        	<div id="report_container2"></div>
        </div>
    </form>    
</div>    
</body>  
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>

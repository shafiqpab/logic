<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Party Wise Yarn Reconciliation
				
Functionality	:	
JS Functions	:
Created by		:	Fuad Shahriar
Creation date 	: 	26-11-2013
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
echo load_html_head_contents("Party Wise Yarn Reconciliation","../../../", 1, 1, $unicode,1,0); 

?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report(type)
	{
		
		if (type!=1 && $("#cbo_value_with").val()==0) 
		{
			alert("Value Without 0 is not availabe for this report");
			return;
		}

		if(type!=3)
		{
			if( form_validation('cbo_company_name*cbo_knitting_source*txt_date_from*txt_date_to','Company Name*Source*From Date*To Date')==false)
			{
				return;
			}
		}
		else
		{
			if($("#txt_challan").val()=='')
			{
				if( form_validation('cbo_company_name*cbo_knitting_source*txt_date_from*txt_date_to','Company Name*Source*From Date*To Date')==false)
				{
					return;
				}
			}
			else
			{
				if( form_validation('cbo_company_name*txt_challan','Company Name*Challan No')==false)
				{
					return;
				}
			}
		}
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_knitting_source*txt_knitting_com_id*txt_job_no*txt_challan*txt_date_from*txt_date_to*txt_internal_ref*cbo_issue_purpose*cbo_value_with',"../../../")+'&report_title='+report_title+'&type='+type;
		//alert(data);
		freeze_window(3);
		http.open("POST","requires/party_wise_yarn_reconciliation_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}

	function generate_report_job(type)
	{
		if ($("#cbo_value_with").val()==0) 
		{
			alert("Value Without 0 is not availabe for this report");
			return;
		}
		if( form_validation('cbo_company_name*cbo_knitting_source','Company Name*Source')==false)//*txt_date_from*txt_date_to *From Date*To Date
		{
			return;
		}
		
		var report_title=$( "div.form_caption" ).html();
		if(type==5){
		var data="action=report_generate_excel"+get_submitted_data_string('cbo_company_name*cbo_knitting_source*txt_knitting_com_id*txt_challan*txt_job_no*txt_job_id*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title;//+'&type='+type
		}
		else
		{
		var data="action=report_generate_job"+get_submitted_data_string('cbo_company_name*cbo_knitting_source*txt_knitting_com_id*txt_challan*txt_job_no*txt_job_id*txt_date_from*txt_date_to*txt_internal_ref',"../../../")+'&report_title='+report_title;//+'&type='+type
		}
		
		
		freeze_window(3);
		http.open("POST","requires/party_wise_yarn_reconciliation_controller.php",true);
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
		//document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.overflowY="scroll";
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
		var page_link='requires/party_wise_yarn_reconciliation_controller.php?action=job_no_popup&companyID='+companyID+'&cbo_knitting_source='+cbo_knitting_source+'&txt_knitting_com_id='+txt_knitting_com_id;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			
			$('#txt_job_no').val(job_no);
			$('#hide_job_id').val(job_id);	 
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
		var page_link='requires/party_wise_yarn_reconciliation_controller.php?action=party_popup&companyID='+companyID+'&cbo_knitting_source='+cbo_knitting_source+'&txt_knit_comp_id='+txt_knit_comp_id;
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
		get_php_form_data($('#cbo_company_name').val(),'print_button_variable_setting','requires/party_wise_yarn_reconciliation_controller' ); 
	}
	
	function print_report_button_setting(report_ids) 
	{
		//alert(report_ids);
		if(report_ids==0) $('#data_panel').append('Please report setting First');
		var report_id=report_ids.split(",");
		
		for (var k=0; k<report_id.length; k++)
		{
			if(report_id[k]==96)
			{
				$('#data_panel').append( '<input type="button" name="search" id="search" value="Summary" onClick="generate_report(1)" style="width:100px" class="formbutton" />&nbsp;' );
			}
			if(report_id[k]==97)
			{
				$('#data_panel').append( '<input type="button" name="search" id="search" value="Party Wise" onClick="generate_report(2)" style="width:100px" class="formbutton" />&nbsp;' );
			}
			if(report_id[k]==98)
			{
				$('#data_panel').append( '<input type="button" name="search" id="search" value="Job Wise" onClick="generate_report_job()" style="width:100px" class="formbutton" />&nbsp;' );
			}
			if(report_id[k]==99)
			{ 
				$('#data_panel').append( '<input type="button" name="search" id="search" value="Challan Wise" onClick="generate_report(3)" style="width:100px;" class="formbutton" />&nbsp;' );
			}
			if(report_id[k]==100)
			{ 
				$('#data_panel').append( '<input type="button" name="search" id="search" value="Returnable" onClick="generate_report(4)" style="width:100px;" class="formbutton" />&nbsp;' );
			}
			if(report_id[k]==101)
			{ 
				$('#data_panel').append( '<input type="button" name="search" id="search" value="Returnable Without Challan" onClick="generate_report_job(5)" style="width:100px;" class="formbutton" />&nbsp;' );
			}
			if(report_id[k]==150)
			{ 
				$('#data_panel').append( '<input type="button" name="search" id="search" value="Summary2" onClick="generate_report(6)" style="width:100px" class="formbutton" />&nbsp;' );
			}			
			
		}
	}
	
	//func_onclick_issue_qty
	function func_onclick_issue_qty(popupIssueId)
	{
		//alert(popupIssueId);
		var company_id = $('#cbo_company_name').val();
		var page_link='requires/party_wise_yarn_reconciliation_controller.php?action=action_issue_qty&company_id='+company_id+'&popupIssueId='+popupIssueId;
		var title='Issue Qty Popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=960px,height=390px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
		}
	}
	
	//func_onclick_fabric_receive
	function func_onclick_fabric_receive(popupFabricReceiveId)
	{
		//alert(popupIssueId);
		var company_id = $('#cbo_company_name').val();
		var page_link='requires/party_wise_yarn_reconciliation_controller.php?action=action_fabric_receive&company_id='+company_id+'&popupFabricReceiveId='+popupFabricReceiveId;
		var title='Fabric Receive Popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=390px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
		}
	}
	
	//func_onclick_reject_fabric_receive
	function func_onclick_reject_fabric_receive(popupRejectFabricReceiveId)
	{
		//alert(popupIssueId);
		var company_id = $('#cbo_company_name').val();
		var page_link='requires/party_wise_yarn_reconciliation_controller.php?action=action_reject_fabric_receive&company_id='+company_id+'&popupRejectFabricReceiveId='+popupRejectFabricReceiveId;
		var title='Reject Fabric Receive Popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=390px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
		}
	}
	
	//func_onclick_yarn_return
	function func_onclick_yarn_return(popupYarnReturnId)
	{
		//alert(popupIssueId);
		var company_id = $('#cbo_company_name').val();
		var page_link='requires/party_wise_yarn_reconciliation_controller.php?action=action_yarn_return&company_id='+company_id+'&popupYarnReturnId='+popupYarnReturnId;
		var title='Yarn Return Popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=390px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
		}
	}
	
	//func_onclick_reject_yarn_return
	function func_onclick_reject_yarn_return(popupRejectYarnReturnId)
	{
		//alert(popupIssueId);
		var company_id = $('#cbo_company_name').val();
		var page_link='requires/party_wise_yarn_reconciliation_controller.php?action=action_reject_yarn_return&company_id='+company_id+'&popupRejectYarnReturnId='+popupRejectYarnReturnId;
		var title='Reject Yarn Return Popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=960px,height=390px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
		}
	}
	
	//func_onclick_process_loss
	function func_onclick_process_loss(popupGreyRcvId, popupIssueIdRef)
	{
		//alert(prog_no);
		var company_id = $('#cbo_company_name').val();
		var page_link='requires/party_wise_yarn_reconciliation_controller.php?action=action_process_loss&company_id='+company_id+'&popupGreyRcvId='+popupGreyRcvId+'&popupIssueIdRef='+popupIssueIdRef;
		var title='Process Loss Popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=260px,height=390px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
		}
	}
	
	//func_onclick_balance_after_process_loss
	function func_onclick_balance_after_process_loss(popupYarnRcvId, popupIssueId, popupFabRcvId, popupGreyRcvId, knitting_party, opening_balance, popupIssueIdRef)
	{
		var company_id = $('#cbo_company_name').val();
		var page_link='requires/party_wise_yarn_reconciliation_controller.php?action=action_balance_after_process_loss&company_id='+company_id+'&popupYarnRcvId='+popupYarnRcvId+'&popupIssueId='+popupIssueId+'&popupFabRcvId='+popupFabRcvId+'&popupGreyRcvId='+popupGreyRcvId+'&knitting_party='+knitting_party+'&opening_balance='+opening_balance+'&popupIssueIdRef='+popupIssueIdRef;
		var title='Balance After Process Loss Popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1250px,height=390px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
		}
	}
	
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../../../",$permission); ?><br />    		 
    <form name="PartyWiseYarnReconciliation_1" id="PartyWiseYarnReconciliation_1" autocomplete="off" > 
        <div style="width:100%;" align="center">
            <fieldset style="width:1010px;">
            <legend>Search Panel</legend> 
                <table class="rpt_table" width="1100" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th width="140" class="must_entry_caption">Company</th>                                
                            <th width="130">Source</th>
                            <th width="100">Issue Purpose</th>
                            <th width="140">Party</th>
                            <th width="80">Challan</th>
                            <th width="80">Job</th>
                            <th width="100">Internal Ref</th>
                            <th width="100">Value</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td>
                            <? 
                               echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "print_button_setting();" );
                            ?>                            
                        </td>
                        <td>
							<?
                                echo create_drop_down("cbo_knitting_source",130,$knitting_source,"", 1, "-- Select Source --", 0,"kniting_company_val();",0,'1,3');
                            ?>
                        </td>
                        
                        
                        
                         
                        <td width="">
                            <?
                            echo create_drop_down("cbo_issue_purpose", 100, $yarn_issue_purpose, "", 1, "-- Select Purpose --", $selected, "", "", "1,2,3,4,5,6,7,8,12,15,16,26,29,30,38,39,40,45", "", "", "");//9,10,11,13,14,16,27,28,32,33
                            ?>
                        </td>
                                        
                                        
                                        
                        <td id="knitting_com">
                            <input type="text" id="txt_knitting_company" name="txt_knitting_company" class="text_boxes" style="width:100px" onDblClick="openmypage_party();" placeholder="Browse Party" />
                            <input type="hidden" id="txt_knitting_com_id" name="txt_knitting_com_id" class="text_boxes" style="width:60px" />
                        </td>
                        <td>
                            <input type="text" id="txt_challan" name="txt_challan" class="text_boxes_numeric" style="width:80px" />
                        </td>
                        <td>
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:80px" onDblClick="openmypage_job();" placeholder="Browse Job" />
                            <input type="hidden" id="txt_job_id" name="txt_job_id" class="text_boxes" style="width:60px" />
                        </td>
                         <td>
                            <input type="text" id="txt_internal_ref" name="txt_internal_ref" class="text_boxes" style="width:100px" />
                        </td>
                         <td>
                            <?   
                                $valueWithArr=array(0=>'Value Without 0',1=>'Value With 0');
                                echo create_drop_down( "cbo_value_with", 90, $valueWithArr,"",0,"",1,"","","");
                            ?>
                        </td>
                        <td align="center">
                             <input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date("01-m-Y"); ?>" class="datepicker" style="width:60px" placeholder="From Date"/>
                             To
                             <input type="text" name="txt_date_to" id="txt_date_to" value="<? echo date("d-m-Y"); ?>" class="datepicker" style="width:60px" placeholder="To Date"/>
                        </td>
                    </tr>
                    <tr class="general">
                        <td colspan="9" align="center" width="95%"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </table>
                <div style="margin-top:10px" id="data_panel">  
                	<!--<input type="button" name="search" id="search" value="Summary" onClick="generate_report(1)" style="width:100px" class="formbutton" />&nbsp;
                    <input type="button" name="search" id="search" value="Party Wise" onClick="generate_report(2)" style="width:100px" class="formbutton" />&nbsp;
                    <input type="button" name="search" id="search" value="Job Wise" onClick="generate_report_job()" style="width:100px" class="formbutton" />&nbsp;
                    <input type="button" name="search" id="search" value="Challan Wise" onClick="generate_report(3)" style="width:100px" class="formbutton" />&nbsp;
                    <input type="button" name="search" id="search" value="Returnable" onClick="generate_report(4)" style="width:100px" class="formbutton" />&nbsp;
                    
                    <input type="button" name="search" id="search" value="Returnable Without Challan" onClick="generate_report_job(5)" style="width:160px" class="formbutton" />&nbsp;
                  -->  
                   
              
                </div> 
                 <input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" onClick="reset_form('PartyWiseYarnReconciliation_1', 'report_container*report_container2','','','','');" />
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

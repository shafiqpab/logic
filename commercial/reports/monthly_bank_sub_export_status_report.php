<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create BTB Liability Coverage Report.
Functionality	:	
JS Functions	:
Created by		:	Fuad 
Creation date 	: 	18-06-2013
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
echo load_html_head_contents("Monthly Bank Submission/Export Status", "../../", 1, 1,'','','');
?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
 
	var permission = '<? echo $permission; ?>';
	
	function generate_report(rpt_type)
	{
		var submission_type=$('#cbo_submission_type').val();
		var cbo_based=$('#cbo_based').val();
		if(submission_type==0 && cbo_based!=0)
		{
			alert("Only Allow Based On Invoice Date");return;
		}
		if(rpt_type==1)
		{
			//alert(submission_type);return;
			if(submission_type !=0)
			{
				if(form_validation('txt_date_from*txt_date_to','From Date*To Date')==false)
				{
					return;
				}
			}
		}
		else
		{
			if(form_validation('txt_date_from*txt_date_to','From Date*To Date')==false)
			{
				return;
			}
		}
		
		
		
		var report_title=$( "div.form_caption" ).html();	
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_lien_bank*txt_date_from*txt_date_to*cbo_based*cbo_submission_type',"../../")+'&report_title='+report_title+'&rpt_type='+rpt_type;
		freeze_window(3);
		http.open("POST","requires/monthly_bank_sub_export_status_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("****");
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			show_msg('3');
			release_freezing();
		}
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		document.getElementById('scroll_body_bottom').style.overflow="auto";
		document.getElementById('scroll_body_bottom').style.maxHeight="none";
	
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="300px";
		document.getElementById('scroll_body_bottom').style.overflowY="scroll";
		document.getElementById('scroll_body_bottom').style.maxHeight="300px"; 
	}

	function openmypage2(id,action)
	{
		var popupWidth = "width=750px,height=350px,";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/monthly_bank_sub_export_status_report_controller.php?id='+id+'&action='+action, 'Document Purchase Details', popupWidth+'center=1,resize=0,scrolling=0','../');
	}
	
	function open_details(ref_id,action,title,page_width)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/monthly_bank_sub_export_status_report_controller.php?ref_id='+ref_id+'&action='+action, title, 'width='+page_width+'px,height=390px,center=1,resize=0,scrolling=0','../');
	}

</script>

</head>

<body onLoad="set_hotkey();">
<form id="MonthlyBankSubmissionExportStatus_report">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",''); ?>
        <h3 align="left" id="accordion_h1" style="width:1050px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" > 
            <fieldset style="width:1050px;">
                <table class="rpt_table" width="1050" border="1" rules="all" cellpadding="1" cellspacing="2" align="center">
                	<thead>
                   		<tr>                    
                            <th>Company Name</th>
                            <th>Buyer Name</th>
                            <th>Lien Bank</th>
                            <th class="must_entry_caption">Date</th>
                            <th>Based On</th>
                            <th>Submission Type</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" onClick="reset_form('MonthlyBankSubmissionExportStatus_report','report_container*report_container2','','','')" /></th>
                        </tr>
                     </thead>
                    <tbody>
                        <tr class="general">
                            <td> 
                                <?
                                    echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/monthly_bank_sub_export_status_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                                ?>
                            </td>
                            <td id="buyer_td">
                                <? 
                                    echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                                ?>
                            </td>
                            <td>
                                <?
                                    echo create_drop_down( "cbo_lien_bank", 150, "select (bank_name||' ('||branch_name||')') as bank_name,id from lib_bank where is_deleted=0 and status_active=1 and lien_bank=1 order by bank_name","id,bank_name", 1, "-- All Lien Bank --", 0, "" );
                                ?>
                            </td>
                            <td align="center">
                             <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:55px" placeholder="From Date"/>
                             To
                             <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:55px" placeholder="To Date"/>
                        	</td>
                            <td>
                                <?
									$based_on=array(0=>"Invoice Date",1=>"Submission Date",2=>"Purchase Date",3=>"Realized Date",4=>"Ex-Factory Date",5=>"Poss. Rlz. Date");
                                	echo create_drop_down( "cbo_based", 110, $based_on,"", 0, "--  --", $selected, "",0,"" );
                                ?>
                            </td>
                            <td>
                                <?
									$submission_type=array(0=>"Un-Submit",1=>"Submit",2=>"All");
                                	echo create_drop_down( "cbo_submission_type", 110, $submission_type,"", 0, "--  --", $selected, "",0,"" );
                                ?>
                            </td>
                           <td>
                                <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:70px" class="formbutton" />
                                <input type="button" name="dtls_search" id="dtls_search" value="Details" onClick="generate_report(2)" style="width:70px" class="formbutton" />
                            </td>
                        </tr>
                        <tr>
                        	<td colspan="7" align="center" width="95%"><? echo load_month_buttons(1); ?></td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
    </div>
    
    <div style="margin-top:10px" id="report_container" align="center"></div>
    <div id="report_container2"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>

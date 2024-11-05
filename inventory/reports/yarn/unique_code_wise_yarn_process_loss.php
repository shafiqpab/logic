<?php
/*********************************************** Comments *************************************
*	Purpose			: 	
*	Functionality	:	
*	JS Functions	:
*	Created by		:	Md. Abu Sayed
*	Creation date 	: 	02-04-2022
*	Updated by 		: 		
*	Update date		: 		   
*	QC Performed BY	:		
*	QC Date			:	
*	Comments		:
************************************************************************************************/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Unique Code wise Yarn Process Loss Report","../../../", 1, 1, $unicode,1,1);
?>	
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
var permission = '<? echo $permission; ?>';

function fn_show_report(type)
{
    
    if( form_validation('cbo_company_name','Company Name')==false )
    {
        return;
    }
	var style_ref_no = $("#txt_ref_no").val();
	var int_ref		 = $("#int_ref").val();
	var file_no		 = $("#file_no").val();
	var job_no		 = $("#txt_job_no").val();
	
	if ( job_no =="" && style_ref_no =="" && int_ref =="" && file_no =="")
	{
		if( form_validation('txt_date_from*txt_date_to','To Date*From Date')==false )
		{
			return;
		}
	}

    var data="action=report_generate&type="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_ref_no*txt_style_ref_id*txt_job_no*txt_job_no_hidden*int_ref*file_no*txt_date_from*txt_date_to',"../../../");
		freeze_window('3');
        //alert(data);
		http.open("POST","requires/unique_code_wise_yarn_process_loss_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
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
	document.getElementById('caption').style.visibility='visible';
	
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');

	document.getElementById('caption').style.visibility='hidden';
	d.close(); 
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
	var page_link='requires/unique_code_wise_yarn_process_loss_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&sytle_ref_no='+sytle_ref_no;
	var title='Job No Search';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var job_no=this.contentDoc.getElementById("hide_job_no").value;
        //alert(job_no);
		 data=job_no.split("_");
        $('#txt_job_no').val(data[1]);
		$("#txt_job_no_hidden").val(data[1]); 
		// $('#txt_job_no').val(job_no);
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
		var page_link='requires/unique_code_wise_yarn_process_loss_controller.php?action=style_search_popup&company='+company+'&buyer='+buyer+'&txt_style_ref_no='+txt_style_ref_no+'&txt_style_ref_id='+txt_style_ref_id+'&txt_style_ref='+txt_style_ref;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=440px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var style_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_no);
			data=style_no.split("_");
			$('#txt_style_ref_id').val(data[0]);
			$('#txt_job_no').val(data[1]);
			$("#txt_job_no_hidden").val(data[1]); 
	  		$('#txt_ref_no').val(data[2]);
	  		//$('#txt_job_no').attr('disabled','true'); 
		}
}
function openmypage_intRef()
{
	if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();
		var page_link='requires/unique_code_wise_yarn_process_loss_controller.php?action=intref_search_popup&company='+company+'&buyer='+buyer;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=510px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var int_ref_id=this.contentDoc.getElementById("hide_int_ref_id").value;
			var job=this.contentDoc.getElementById("hide_job").value;
			var grouping=this.contentDoc.getElementById("hide_grouping").value;
			var file_no=this.contentDoc.getElementById("hide_file_no").value;
			
			//alert(int_ref_id);
			$('#int_ref_id').val(int_ref_id);
			$('#txt_job_no').val(job);
			$("#txt_job_no_hidden").val(job); 
	  		$('#int_ref').val(grouping);
	  		$('#file_no').val(file_no);
			//$('#txt_job_no').attr('disabled','true'); 
			//$('#file_no').attr('disabled','true'); 
			
		}
}

function openmypage_fileNo()
{
	if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();
		var page_link='requires/unique_code_wise_yarn_process_loss_controller.php?action=fileno_search_popup&company='+company+'&buyer='+buyer;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=440px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var style_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_no);
			data=style_no.split("_");
			$('#int_ref_id').val(data[0]);
			$('#txt_job_no').val(data[1]);			 
	  		$('#int_ref').val(data[2]);
	  		$('#file_no').val(data[3]);
		}
}

function openmypage_dtls(job_no,action,type)
{
	var company = $("#cbo_company_name").val();	
	var popup_width='730px';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/unique_code_wise_yarn_process_loss_controller.php?job_no='+job_no+'&type='+type+'&company='+company+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=400px,center=1,resize=0,scrolling=0','../../');
}


function trans_dtls(order_id,action,type)
{
 	var popup_width='450px';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/unique_code_wise_yarn_process_loss_controller.php?order_id='+order_id+'&action='+action+'&type='+type, 'Detail Veiw', 'width='+popup_width+', height=400px,center=1,resize=0,scrolling=0','../../');
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
<form id="UniqueCodeWiseYarnProcess_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../../",'');  ?>
         <h3 style="width:1010px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel">      
         <fieldset style="width:1010px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th class="must_entry_caption">Company Name</th>
                    <th>Buyer Name</th>
                    <th >Style Ref.</th>
                    <th>Job No</th>
                    <th >Int. Ref.</th>
                    <th >File NO.</th>
                    <th class="" colspan="2">Program Date</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('UniqueCodeWiseYarnProcess_1','','','','')" class="formbutton" style="width:50px" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td>
							<? 
                               echo create_drop_down( "cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down('requires/unique_code_wise_yarn_process_loss_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                             
                            ?> 
                        </td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- Select Buyer --", $selected, "","","" ); ?></td>
                        
                       
                        <td>
	                        <input type="text" name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:100px" placeholder="Write/Browse"  onDblClick="openmypage_style();" /> 
	                        <input type="hidden" name="txt_style_ref_id" id="txt_style_ref_id"/>    
	                        <input type="hidden" name="txt_style_ref_no" id="txt_style_ref_no"/>                      
                        </td>
                        <td> 
                          <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:100px" placeholder="Write/Browse" onDblClick="openmypage_job();" />
                            <input type="hidden" name="update_id"  id="update_id" readonly />
                            <input type="hidden" name="txt_job_no_hidden"  id="txt_job_no_hidden"  />
                        </td>
                         <td>
                         	<input type="text" name="int_ref"  id="int_ref" class="text_boxes" onDblClick="openmypage_intRef()" placeholder="Write/Browse"  />
	                        <input type="hidden" name="int_ref_id" id="int_ref_id"/>  
                         </td>
                         <td>
                         	<input type="text" name="file_no"  id="file_no" class="text_boxes" onDblClick="openmypage_fileNo()" placeholder="Write/Browse"  />
                         </td>
                         <td align="center">
                             <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:55px" />
                          </td>
                        <td align="center">
                            
                             <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:55px" />
                        </td>
                       
                        <td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_show_report(1)" /></td>
                    </tr>
                    <tr>
                        <td colspan="9"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </tbody>
            </table>
           
        </fieldset>
    	</div>
         <div style="display:none" id="data_panel"></div>   
    	<div id="report_container" align="center"></div>
    	<div id="report_container2" align="center"></div>
    </div>
   
 </form>   
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
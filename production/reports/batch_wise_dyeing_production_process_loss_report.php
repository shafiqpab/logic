<?
/*-------------------------------------------- Comments

Purpose			: 	This form will Create Roll Position Tracking Report
					
Functionality	:	
				

JS Functions	:

Created by		:	Monir Hossain
Creation date 	: 	13-04-2016
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
echo load_html_head_contents("Batch Wise Dyeing Production Process Loss Report controller", "../../", 1, 1,'','','');

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	var tableFilters = 
	 {
		col_10: "none",
		col_operation: {
		id: ["value_total_grey_qnty","value_total_finishing_qnty","value_total_processes_loss"],
	   col: [10,21,12],
	   operation: ["sum","sum","sum"],
	   write_method: ["innerHTML","innerHTML","innerHTML"]
		}
	 }
	 
	function fn_report_generated(type)
	{
		var txt_file_no=$('#txt_file_no').val();
		var txt_batch_no=$('#txt_batch_no').val();
		var txt_inter_ref=$('#txt_inter_ref').val();
		var cbo_job_year=$('#cbo_job_year').val();
		if(txt_file_no=="" && txt_batch_no=="" && txt_inter_ref=="" )
		{
			//*cbo_buyer_name*txt_file_no
			if(form_validation('cbo_company_name*cbo_buyer_name','Company*Buyer')==false)
			{
				return;
			}
			
		}
		else
		{
			if(form_validation('cbo_company_name','Company')==false)
			{
				return;
			}
			$('#notice').text('');
		}
		
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_file_no*txt_batch_no*txt_inter_ref*txt_job_no*txt_styleref_no*cbo_job_year',"../../")+'&report_title='+report_title+'&type='+type;
		
		freeze_window(5);
		http.open("POST","requires/batch_wise_dyeing_production_process_loss_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");
			$("#report_container2").html(response[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" id="htmlpreview" style="width:100px"/>';
			setFilterGrid("table_body",-1,tableFilters);
	 		show_msg('3');
			release_freezing();
		}
		
		
	/*	$(document).ready(function(){
			
		$('#td_id').css('color','red');
		$('#td_id').css('border','none');
		$('#td_color_id').css('border','none');
   
		})*/
	}
		
	function new_window()
	{
		document.getElementById('scroll_body').style.overflowY="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('.flt').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="330px";
		$('.flt').show();
	}
	
	function openmypage(po_id,color_id)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/batch_wise_dyeing_production_process_loss_report_controller.php?po_id='+po_id+'&color_id='+color_id+'&action=color_popup', 'Detail Veiw', 'width=860px, height=370px,center=1,resize=0,scrolling=0','../');
	}
	
	function change()
	{
		if($('#cbo_company_name').val()>0)
		{
			if(form_validation('cbo_buyer_name','Buyer')==false)
			{
				return;
			}
	
		}
	}

	function batchnumber()
	{ 
		if(form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
		var company_name=document.getElementById('cbo_company_name').value;
		var txt_file_no=document.getElementById('txt_file_no').value;
		var txt_inter_ref=document.getElementById('txt_inter_ref').value;
		var cbo_job_year=document.getElementById('cbo_job_year').value;
		//alert(company_name);
		//var batch_number=document.getElementById('txt_batch_no').value;
		//var batch_type=document.getElementById('cbo_batch_type').value;
		var page_link="requires/batch_wise_dyeing_production_process_loss_report_controller.php?action=batchnumbershow&company_name="+company_name+'&txt_file_no='+txt_file_no+'&txt_inter_ref='+txt_inter_ref+'&cbo_job_year='+cbo_job_year;
		var title="Batch Number";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=450px,height=400px,center=1,resize=0,scrolling=0','../')
	
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			var batch=theemail.split("_");
			//alert(batch);
			document.getElementById('txt_batch_no').value=batch[1];
			document.getElementById('txt_job_no').value=batch[0];
			document.getElementById('txt_styleref_no').value=batch[2];
			//document.getElementById('batch_no').value=batch[1];
			release_freezing();
		}
	}
	
	
	function filenumber(type)
	{ 
		if(form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
		var company_name=document.getElementById('cbo_company_name').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
		var txt_file_no=document.getElementById('txt_file_no').value;
		var txt_inter_ref=document.getElementById('txt_inter_ref').value;
		var cbo_job_year=document.getElementById('cbo_job_year').value;
		//var batch_type=document.getElementById('cbo_batch_type').value;
		var page_link="requires/batch_wise_dyeing_production_process_loss_report_controller.php?action=fileNref&company_name="+company_name+'&type='+type+'&cbo_buyer_name='+cbo_buyer_name+'&txt_file_no='+txt_file_no+'&txt_inter_ref='+txt_inter_ref+'&cbo_job_year='+cbo_job_year;
		//alert(page_link);
		var title="File No.";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=665px,height=400px,center=1,resize=0,scrolling=0','../')
	
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			var batch=theemail.split("_");
			 //alert(batch[1]);
			if(type==1)
			{
			document.getElementById('txt_file_no').value=batch[1];
			}
			if(type==2)
			{
			document.getElementById('txt_inter_ref').value=batch[1];
			}
			document.getElementById('txt_job_no').value=batch[0];
			//$('#must_entry').addClass('must_entry_caption');
			document.getElementById('txt_styleref_no').value=batch[2];
			//document.getElementById('batch_number_show').value=batch[1];
			release_freezing();
			
		}
	}
	
	function internalrefno(type)
	{ 
		if(form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
		var company_name=document.getElementById('cbo_company_name').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
		var txt_file_no=document.getElementById('txt_file_no').value;
		var txt_inter_ref=document.getElementById('txt_inter_ref').value;
		var cbo_job_year=document.getElementById('cbo_job_year').value;
		//var batch_type=document.getElementById('cbo_batch_type').value;
		var page_link="requires/batch_wise_dyeing_production_process_loss_report_controller.php?action=fileNref&company_name="+company_name+'&type='+type+'&cbo_buyer_name='+cbo_buyer_name+'&txt_file_no='+txt_file_no+'&txt_inter_ref='+txt_inter_ref+'&cbo_job_year='+cbo_job_year;
		//alert(page_link);
		var title="Internal Ref. No";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=665px,height=430px,center=1,resize=0,scrolling=0','../')
	
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			var batch=theemail.split("_");
			//alert(batch);
			if(type==1)
			{
			  document.getElementById('txt_file_no').value=batch[1];
			}
			if(type==2)
			{
				document.getElementById('txt_inter_ref').value=batch[1];
			}
			document.getElementById('txt_job_no').value=batch[0];
			document.getElementById('txt_styleref_no').value=batch[2];
			release_freezing();
		}
	}
	
</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",''); ?>
		 <form name="batchwiseprocessloss_1" id="batchwiseprocessloss_1"> 
         <h3 style="width:850px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
             <fieldset style="width:850px;">
                 <table class="rpt_table" width="850" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <th class="must_entry_caption" width="150">Company Name</th>
                            <th  class="must_entry_caption" width="150">Buyer Name</th>
                            <th   width="60">Job Year</th>
                            <th  width="110">File No</th>
                            <th  width="110">Internal Ref. No</th>
                            <th  width="110">Batch No</th>
                            <th colspan="2"><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('batchwiseprocessloss_1','report_container*report_container2','','','')" class="formbutton" style="width:100px" /></th>
                        </thead>
                        <tbody>
                            <tr class="general" align="center">
                               <td> 
									<?
                                        echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/batch_wise_dyeing_production_process_loss_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                                    ?>
                                </td>
                                <td id="buyer_td" align="center">
                                    <? 
                                        echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --", $selected, "",0,"" );
                                    ?>
                                    
                                </td>
                                <td align="center">
                                <?
								echo create_drop_down( "cbo_job_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
							?>
                                </td>
                                <td align="center">
                                     <input type="text" name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:90px" placeholder="Write/Browse" onDblClick="filenumber(1);" />
                                </td>
                                <td align="center">
                                     <input type="text" name="txt_inter_ref" id="txt_inter_ref" class="text_boxes" placeholder="Write/Browse" onDblClick="internalrefno(2);" style="width:90px" />
                                </td>
                                <td align="center">
                                     <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes"  placeholder="Write/Browse" onDblClick="batchnumber();" style="width:90px" />
                                     
                                     
                                </td>
                                
                                <td><input type="button"  id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated(1)" /><input type="hidden" id="txt_job_no"><input type="hidden" id="txt_styleref_no"></td>
                                <td><input type="button"  id="show_button" class="formbutton" style="width:100px" value="Show 2" onClick="fn_report_generated(2)" /></td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
            
		</form>
	</div>
    <div id="notice" style=" margin-left:410px; font-size:18px; color:red;" ></div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" style="margin-left:30px;"></div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
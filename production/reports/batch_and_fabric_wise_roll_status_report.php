<?
/*-------------------------------------------- Comments

Purpose			: 	This form will Create Batch and Fabric-wise Roll Status Report   
					
Functionality	:	
				

JS Functions	:

Created by		:	Md. Shafiqul Islam Shafiq
Creation date 	: 	14-07-2019
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
		if(form_validation('cbo_company_name*cbo_batch_against*txt_batch_no','Company*Batch Against*Batch No')==false)
		{
			return;
		}
		
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_batch_against*txt_batch_no',"../../")+'&report_title='+report_title+"&type="+type;
		
		freeze_window(5);
		http.open("POST","requires/batch_and_fabric_wise_roll_status_report_controller.php",true);
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
			$("#content_search_panel").css({'display':'none'});
	 		show_msg('3');
			release_freezing();
		}
	}
		
	function new_window()
	{
		// document.getElementById('scroll_body').style.overflowY="auto";
		// document.getElementById('scroll_body').style.maxHeight="none";
		$('.flt').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><style>table#rpt_table tr th{font-size:13px;}table#rpt_table tr td{font-size:12px;}</style><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="all"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		// document.getElementById('scroll_body').style.overflowY="scroll";
		// document.getElementById('scroll_body').style.maxHeight="330px";
		$('.flt').show();
	}
	
	function openmypage(po_id,color_id)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/batch_and_fabric_wise_roll_status_report_controller.php?po_id='+po_id+'&color_id='+color_id+'&action=color_popup', 'Detail Veiw', 'width=860px, height=370px,center=1,resize=0,scrolling=0','../');
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
		var batch_against=document.getElementById('cbo_batch_against').value;
		var page_link="requires/batch_and_fabric_wise_roll_status_report_controller.php?action=batch_popup&company_name="+company_name+'&batch_against='+batch_against;
		var title="Batch Number";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=400px,center=1,resize=0,scrolling=0','../')
	
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var batch_id=this.contentDoc.getElementById("hidden_batch_id").value;	 //Access form field with id="emailfield"
			var batch_no=this.contentDoc.getElementById("hidden_batch_no").value;
		
			document.getElementById('txt_batch_no').value=batch_no;
			document.getElementById('txt_batch_id').value=batch_id;
			//document.getElementById('batch_no').value=batch[1];
			release_freezing();
		}
	}	
</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",''); ?>
		 <form name="batchwiseprocessloss_1" id="batchwiseprocessloss_1"> 
         <h3 style="width:650px;margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
             <fieldset style="width:650px;">
                 <table class="rpt_table" width="650" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <th class="must_entry_caption" width="150">Company Name</th>
                            <th  class="must_entry_caption" width="150">Batch Base</th>
                            <th  class="must_entry_caption" width="110">Batch No</th>
                            <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('batchwiseprocessloss_1','report_container*report_container2','','','')" class="formbutton" style="width:100px" /></th>
                        </thead>
                        <tbody>
                            <tr class="general" align="center">
                               <td> 
									<?
                                        echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                                        //load_drop_down( 'requires/batch_and_fabric_wise_roll_status_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );
                                    ?>
                                </td>
                                <td id="batch_against_td" align="center">
                                    <?
									echo create_drop_down("cbo_batch_against", 150, $batch_against,"", 1, '--- Select ---', 1, "",'','1,2,3,5,7','','','',1 );
									//active_inactive();
									?>
                                </td>
                                <td align="center">
                                     <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes"  placeholder="Write/Browse" onDblClick="batchnumber();" style="width:90px" />
                                	<input type="hidden" id="txt_batch_id">
                                	<input type="hidden" id="txt_job_no">
                                	<input type="hidden" id="txt_styleref_no">
                                </td>
                                
                                <td>
                                	<input type="button"  id="show_button" class="formbutton" style="width:100px" value="Roll Wise" onClick="fn_report_generated(1)" />
                                	<input type="button"  id="show_button" class="formbutton" style="width:100px" value="Fabric Wise" onClick="fn_report_generated(2)" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
            
		</form>
	</div>
    <div id="notice" style=" margin-left:410px; font-size:18px; color:red;" ></div>
    <div id="report_container" align="center" style="padding: 10px;"></div>
    <div id="report_container2" style="margin-left:5px;"></div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
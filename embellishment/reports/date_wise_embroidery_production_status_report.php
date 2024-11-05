<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Date Wise Embroidery Production Status Report.
Functionality	:	
JS Functions	:
Created by		:	Md. Shafiqul Islam Shafiq
Creation date 	: 	31-08-2019
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
echo load_html_head_contents("Embellishment Work Progress Report", "../../", 1, 1,$unicode,1,1);
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
			
	var tableFilters = 
	{
		col_operation: {
		   id: ["tot_job_qty","tot_job_val","tot_mat_rcv","tot_mat_issue","tot_prod_qty","tot_qc_qty","tot_del_qty","tot_rej_qty","tot_bill_qty","tot_bill_amt"],
		   col: [6,7,9,10,11,12,13,14,15,16],
		   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}	
	}
	
	function fn_report_generated(type)
	{
		if (form_validation('cbo_company_id','Comapny Name')==false)
		{
			return;
		}
		var party 	= $('#cbo_party_id').val();
		var job_no 	= $('#txt_job_no').val();
		var style 	= $('#txt_style_ref').val();
		var intRef 	= $('#txt_int_ref').val();
		if (job_no == "" && party == 0 && style == "" && intRef == "")
        {
            if (form_validation('txt_date_from*txt_date_to', 'Date Form*Date To') == false)
            {
                return;
            }
        }

		var report_title=$( "div.form_caption" ).html();
		
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_within_group*cbo_party_id*txt_job_no*txt_style_ref*txt_int_ref*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;			
		
		freeze_window(3);
		http.open("POST","requires/date_wise_embroidery_production_status_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function fn_report_generated_reponse()
		{			
			if(http.readyState == 4) 
			{   
				show_msg('3');
				var reponse=trim(http.responseText).split("**"); 
				$('#report_container2').html(reponse[0]);
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
				setFilterGrid("table_body",-1,tableFilters);
				release_freezing();
			}
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

	function job_search_popup(page_link,title)
	{
		if ( form_validation('cbo_company_id*cbo_party_id','Company Name*Party Name')==false )
		{
			return;
		}
		else
		{
			var title="Job No Pop-up";
			var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_party_id').value+"_"+document.getElementById('cbo_within_group').value;
			page_link='requires/date_wise_embroidery_production_status_report_controller.php?action=job_popup&data='+data
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=400px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				freeze_window(5);
				var theform=this.contentDoc.forms[0];
				var theemail=this.contentDoc.getElementById("selected_order").value;
				$("#txt_job_no").val( theemail );
				
				var list_view_orders = return_global_ajax_value( 0+'**'+theemail+'**'+1, 'load_php_dtls_form', '', 'requires/emb_order_details_report_controller');
				if(list_view_orders!='')
				{
					$("#rec_issue_table tr").remove();
					$("#rec_issue_table").append(list_view_orders);
				}
				$('#cbo_company_name').attr('disabled','disabled');
				$('#cbo_party_name').attr('disabled','disabled');
				release_freezing();
			}
		}
	}	
	
	function openmypage_style()
	{ 
		if(form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		
		var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_party_id').value+"_"+document.getElementById('cbo_within_group').value+"_"+document.getElementById('txt_job_no').value;
		
		
		
		var page_link="requires/date_wise_embroidery_production_status_report_controller.php?action=style_no_popup&data="+data;
		var title="Style Ref. Search";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=420px,center=1,resize=0,scrolling=0','../')
	
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			//var job=theemail.split("_");
			document.getElementById('txt_style_ref').value=theemail;
			release_freezing();
		}
	}
	
	function openmypage_intRef()
	{ 
		if(form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		
		var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_party_id').value+"_"+document.getElementById('cbo_within_group').value+"_"+document.getElementById('txt_job_no').value;
		
		
		
		var page_link="requires/date_wise_embroidery_production_status_report_controller.php?action=int_ref_popup&data="+data;
		var title="Internal Ref. Search";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=420px,center=1,resize=0,scrolling=0','../')
	
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			//var job=theemail.split("_");
			document.getElementById('txt_int_ref').value=theemail;
			release_freezing();
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		$("#table_body tr:first").hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="350px";
		$("#table_body tr:first").show();
	}

	function open_popup(arg,type,title,action)
	{ 
		var width;
		switch(action) {
		  case "production_popup":
		    width = "1000px";
		    break;
		  case "delivery_popup":
		    width = "1000px";
		    break;
		  case "bill_popup":
		    width = "1200px";
		    break;
		  default:
		    width = "760px";
		} 
		var page_link="requires/date_wise_embroidery_production_status_report_controller.php?action="+action+"&type="+type+"&data="+arg;		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+width+',height=420px,center=1,resize=0,scrolling=0','../')
	}

	$(function(){
		$("#cbo_within_group").change(function()
		{
			var company_name = $("#cbo_company_id").val();
			if(company_name==0)
			{
				alert("Please select company name.");return;
			}
			// alert('ok');
			if($(this).val()==2)
			{
				$("#txt_style_ref").prop('disabled','disabled');
				$("#txt_int_ref").prop('disabled','disabled');
				$("#cngTxt").text('Party');
			}
			else
			{
				$("#txt_style_ref").prop('disabled','');
				$("#txt_int_ref").prop('disabled','');
				$("#cngTxt").text('Company');
			}
		});
	})
</script>
</head>
<body onLoad="set_hotkey();">
<form>
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:900px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:900px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>                    
                    <th width="140" class="must_entry_caption">Company</th>
                    <th width="100">Within Group </th>
                    <th width="130" id="cngTxt">Party </th>                 
                    <th width="100">Emb. Job No</th>
                    <th width="100">Buyer Style</th>
                    <th width="100">Internal Ref.</th>
                    <th colspan="2" width="150" class="must_entry_caption">Transaction Date</th>
                    <th width=""><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                </thead>
                <tbody>
                    <tr >
                        <td> 
                        <? echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, ""); ?>
                    	</td>
                    	<td>
							<?php echo create_drop_down( "cbo_within_group", 100, $yes_no,"", 1, "-- Select --", $selected, "load_drop_down( 'requires/date_wise_embroidery_production_status_report_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_party', 'party_td' );" ); ?>
						</td>
                        <td id="party_td">
                        	<? 
                        		echo create_drop_down( "cbo_party_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, ""); 
							?>
                    	</td>
                        <td>
                    		<input class="text_boxes"  type="text" name="txt_job_no" id="txt_job_no" onDblClick="job_search_popup();" placeholder="Double Click" style="width:100px;" readonly/>
                    	</td>
                        
                        <td>
                            <input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:100px" placeholder="Wr/Br Style" onDblClick="openmypage_style();" >
                        </td>
                        
                        <td>
                            <input name="txt_int_ref" id="txt_int_ref" class="text_boxes" style="width:100px" placeholder="Wr/Br Int. Ref." onDblClick="openmypage_intRef();" >
                        </td>
                        
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" readonly>
                        </td>
                        <td>
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date" readonly>
                        </td>
                        <td align="center">
                            <input type="button" id="show_button" class="formbutton" style="width:80px" value="Show" onClick="fn_report_generated(1)" />
                           
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="9" align="center">
							<? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                </tfoot>
           </table> 
        </fieldset>
    </div>
    </div>
    <div id="report_container" align="center" style="padding: 10px;"></div>
    <div id="report_container2" align="left"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

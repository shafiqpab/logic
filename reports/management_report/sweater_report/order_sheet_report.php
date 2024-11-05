<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create  Order Sheet Report.
Functionality	:	
JS Functions	:
Created by		:	Aziz 
Creation date 	: 	25-04-2019
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
		
		var txt_style_ref=document.getElementById('txt_style_ref').value;	
		var order_no=document.getElementById('txt_order').value;
		
		var cbo_year=document.getElementById('cbo_year').value;
		var company_name=document.getElementById('cbo_company_name').value;
		var buyer_name=document.getElementById('cbo_buyer_name').value;
		var txt_job_no=document.getElementById('txt_job_no').value;
		
		if (type==1 )
		{
			if(txt_style_ref!="" || order_no!=""  || txt_job_no!="" || buyer_name!=0)
			{
				if(form_validation('cbo_company_name','Company')==false)
				{
					return;
				}
			}
			else
			{
				
				//if(form_validation('cbo_company_name*txt_job_no','Company*Job')==false)
				if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company*From date Fill*To date Fill')==false)
				{
					return;
				}
			}
		}
		
		
		var report_title=$( "div.form_caption" ).html();	
		var data="action=report_generate&reporttype="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_style_ref*txt_style_ref_id*txt_order*txt_order_id*cbo_year*txt_job_no*txt_job_no_id*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title;
		//alert(data);return;
		freeze_window(3);
		if(type==1 || type==2) 
		{
			http.open("POST","requires/order_sheet_report_controller.php",true);
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
				
			//append_report_checkbox('table_header_1',1);
			//show_graph( '', document.getElementById('graph_data').value, "pie", "chartdiv", "", "../../../", '',580,700 );
			release_freezing();
			show_msg('3');
		}
	}
	
	function new_window(type)
	{
		
	
		 $('.scroll_div_inner').css('overflow','auto');
		 $('.scroll_div_inner').css('maxHeight','none');
		 
		$("#table_body_accss tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		$('.scroll_div_inner').css('overflow','scroll');
		$('.scroll_div_inner').css('maxHeight','430px');
		
		$("#table_body_accss tr:first").show();
	}
	
	
	function openmypage_style(type)
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		
		var companyID = $("#cbo_company_name").val();
		var buyer_name = $("#cbo_buyer_name").val();
		var cbo_year_id = $("#cbo_year").val();
		var txt_job_no = $("#txt_job_no").val();
		var txt_job_no_id = $("#txt_job_no_id").val();
		//var cbo_month_id = $("#cbo_month").val();
		var page_link='requires/order_sheet_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id+'&txt_job_no='+txt_job_no+'&txt_job_no_id='+txt_job_no_id+'&type='+type;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=830px,height=370px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			//alert(type);
			if(type==1)
			{
				$('#txt_job_no').val(job_no);
				$('#txt_job_no_id').val(job_id);	
			}
			else if(type==2) 
			{
				$('#txt_style_ref').val(job_no);
				$('#txt_style_ref_id').val(job_id);	
			}
			else if(type==3) 
			{
				$('#txt_order').val(job_no);
				$('#txt_order_id').val(job_id);	
			}
		}
	}
	
	
	
	function openmypage_order() 
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_job_no').value;
		//alert (data);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/order_sheet_report_controller.php?action=order_no_popup&data='+data,'Order No Popup', 'width=630px,height=420px,center=1,resize=0','../../')
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("order_no_id");
			var theemailv=this.contentDoc.getElementById("order_no_val");
			var response=theemail.value.split('_');
			if (theemail.value!="")
			{
				freeze_window(5);
				document.getElementById("txt_order_id").value=theemail.value;
			    document.getElementById("txt_order_no").value=theemailv.value;
				release_freezing();
			}
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
	


</script>
 
</head>
<body onLoad="set_hotkey();">
<form id="costSheetReport_1">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../../",$permission);  ?>
          <h3 align="left" id="accordion_h1" style="width:1100px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
         <fieldset style="width:1100px;" id="content_search_panel">
            <table class="rpt_table" width="1100" cellpadding="1" cellspacing="2" align="center" border="1" rules="all">
                <thead>                    
                    <th class="must_entry_caption">Company</th>
                    <th class="must_entry_caption">Buyer</th>
                    <th>Year</th>
                    <th  class="must_entry_caption">Job No</th>
                     <th  class="must_entry_caption">Style Ref</th>
                    <th>Order</th>
                    <th title="Pub Ship date" colspan="2">Date</th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:60px" value="Reset" /></th>
                    </thead>
                <tbody>
                    <tr class="general">
                        <td width="150"> 
							<?
							
                           		echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/order_sheet_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        
                        
                        <td id="buyer_td" width="130">
							<? 
                            	echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "--All Buyer--", $selected, "",0,"" );
                            ?>
                        </td>
                         <td width="90">
                          <? echo create_drop_down( "cbo_year", 80, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?>
                         </td>
                        <td width="90">
                             <input style="width:80px;" name="txt_job_no" id="txt_job_no" onDblClick="openmypage_style(1)" class="text_boxes" placeholder="Browse or Write"/>
                               <input type="hidden" name="txt_job_no_id" id="txt_job_no_id"/> 
                             
                        </td>
                        <td width="110">
                             <input style="width:100px;" name="txt_style_ref" id="txt_style_ref" onDblClick="openmypage_style(2)" class="text_boxes" placeholder="Browse or Write"/>
                            <input type="hidden" name="txt_style_ref_id" id="txt_style_ref_id"/>   
                             <input type="hidden" name="txt_style_ref_no" id="txt_style_ref_no"/>  
                        </td>
                        <td width="110">
                        <input style="width:100px;" name="txt_order" id="txt_order" onDblClick="openmypage_style(3)" class="text_boxes" placeholder="Browse or Write" />   
                            <input type="hidden" name="txt_order_id" id="txt_order_id"/>      
                        </td>
                        <td width="80" colspan="2">
                        	 <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date"> &nbsp;
							 <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"  placeholder="To Date" >  
                        </td>
                         <td>
                            <input type="button" id="show_button_1" class="formbutton" style="width:50px;" value="Show" onClick="fn_report_generated(1)" />
                            <input type="button" id="show_button_2" class="formbutton" style="width:70px;" value="Style Wise" onClick="fn_report_generated(2)" />
                            
                        </td>
                    </tr>
                    <tr>
                    	<td colspan="9" align="center"><? echo load_month_buttons(1); ?></td>
                    </tr>
                 
                </table> 
            </fieldset>
        </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
  
 </form>    
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
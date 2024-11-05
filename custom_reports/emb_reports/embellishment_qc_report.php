<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Work Progress Report.
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	04-06-2014
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
echo load_html_head_contents("Order Details Report", "../../", 1, 1,$unicode,1,1);
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';

	var tableFilters = 
	{
		//col_15: "none",
		col_operation: {
		id: ["value_total_rcvd_qty","value_total_issue_qty"],
		col: [14,15],
		operation: ["sum","sum"],
		write_method: ["innerHTML","innerHTML"]
		}
	} 
	
	var tableFilters2 = 
	{
		//col_15: "none",
		col_operation: {
		id: ["value_total_rcvd_qty","value_total_issue_qty"],
		col: [14,15],
		operation: ["sum","sum"],
		write_method: ["innerHTML","innerHTML"]
		}
	} 

	function fn_report_generated(type)
	{
		if(type==1)
		{
			if (form_validation('cbo_company_id','Comapny Name')==false)//*txt_date_from*txt_date_to----*From Date*To Date
			{
				return;
			}
			else
			{
				var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_mainbuyer_id*cbo_buyer_id*cbo_search_by*cbo_year*txt_job_no*txt_style_ref*txt_order_no*txt_date_from*txt_date_to',"../../")+'&type='+type;
				freeze_window(3);
				http.open("POST","requires/embellishment_qc_report_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fn_report_generated_reponse;
			}
		}
		if(type==2)
		{
			//alert(type);
			if (form_validation('cbo_company_id*txt_date_from*txt_date_to','Comapny Name*From Date*To Date')==false)//----
			{
				return;
			}
			else
			{
				var data="action=report_generate_print"+get_submitted_data_string('cbo_company_id*cbo_mainbuyer_id*cbo_buyer_id*cbo_search_by*cbo_year*txt_job_no*txt_style_ref*txt_order_no*txt_date_from*txt_date_to',"../../")+'&type='+type;
				freeze_window(3);
				http.open("POST","requires/embellishment_qc_report_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fn_report_generated_reponse;
			}
		}
		if(type==3)
		{
			//alert(type);
			if (form_validation('cbo_company_id*txt_date_from*txt_date_to','Comapny Name*From Date*To Date')==false)//----
			{
				return;
			}
			else
			{
				var data="action=report_generate_print_balance"+get_submitted_data_string('cbo_company_id*cbo_mainbuyer_id*cbo_buyer_id*cbo_search_by*cbo_year*txt_job_no*txt_style_ref*txt_order_no*txt_date_from*txt_date_to',"../../")+'&type='+type;
				freeze_window(3);
				http.open("POST","requires/embellishment_qc_report_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fn_report_generated_reponse;
			}
		}
	}
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("**");
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			append_report_checkbox('table_header_1',1);
			var cbo_presentation=$('#cbo_presentation').val(); 
			if(cbo_presentation==1)
			{
				setFilterGrid("table_body",-1,tableFilters);
			}
			else
			{
				setFilterGrid("table_body",-1,tableFilters2);	
			}
			show_msg('3');
			release_freezing();
		}
	}
	function fn_buyer_wise_report_generated(type)
	{
		if (form_validation('cbo_company_id','Comapny Name')==false)//*txt_date_from*txt_date_to----*From Date*To Date
		{
			return;
		}
		else
		{
			var data="action=buyer_wise_report_generate"+get_submitted_data_string('cbo_company_id*cbo_mainbuyer_id*cbo_buyer_id*cbo_search_by*cbo_year*txt_job_no*txt_style_ref*txt_order_no*txt_date_from*txt_date_to',"../../")+'&type='+type;
			freeze_window(3);
			http.open("POST","requires/embellishment_qc_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_buyer_wise_report_generated_reponse;
		}
	}
	function fn_buyer_wise_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("**");
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			append_report_checkbox('table_header_1',1);
			var cbo_presentation=$('#cbo_presentation').val(); 
			if(cbo_presentation==1)
			{
				setFilterGrid("table_body",-1,tableFilters);
			}
			else
			{
				setFilterGrid("table_body",-1,tableFilters2);	
			}
			show_msg('3');
			release_freezing();
		}
	}


	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#table_body tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		$('#scroll_body tr:first').show();
	}
	
	function show_progress_report_details(action,order_id,width)
	{ 
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/embellishment_qc_report_controller.php?action='+action+'&order_id='+order_id, 'Work Progress Report Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../');
		
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

	function openImageWindow(id)
	{
		var title = 'Image View';	
		var page_link = 'requires/embellishment_qc_report_controller.php?&action=image_view_popup&id='+id;
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=370px,center=1,resize=1,scrolling=0','../');
		
		emailwindow.onclose=function()
		{
		}
	}
	
	function openmypage_job()
	{ 
		if(form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var company_name=document.getElementById('cbo_company_id').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_id').value;
		var year=document.getElementById('cbo_year').value;
		var cbo_process_id=document.getElementById('cbo_process_id').value;
		var page_link="requires/embellishment_qc_report_controller.php?action=job_no_popup&company_id="+company_name+"&cbo_buyer_name="+cbo_buyer_name+"&year="+year;
		var title="Job Number";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=530px,height=420px,center=1,resize=0,scrolling=0','../')
	
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			//var job=theemail.split("_");
			document.getElementById('txt_job_no').value=theemail;
			release_freezing();
		}
	}	
	
	function openmypage_style()
	{ 
		if(form_validation('cbo_company_id*cbo_buyer_id','Company*Party_Name')==false)
		{
			return;
		}
		var company_name=document.getElementById('cbo_company_id').value;
		var cbo_party_name=document.getElementById('cbo_buyer_id').value;
		var year=document.getElementById('cbo_year').value;
		var page_link="requires/embellishment_qc_report_controller.php?action=style_no_popup&cbo_party_name="+cbo_party_name+"&year="+year;
		var title="Style Ref.";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=530px,height=420px,center=1,resize=0,scrolling=0','../')
	
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			//var job=theemail.split("_");
			document.getElementById('txt_style_ref').value=theemail;
			release_freezing();
		}
	}

	function openmypage_order()
	{ 
		if(form_validation('cbo_company_id*cbo_buyer_id','Company*Party_Name')==false)
		{
			return;
		}
		var company_name=document.getElementById('cbo_company_id').value;
		var cbo_party_name=document.getElementById('cbo_buyer_id').value;
		var year=document.getElementById('cbo_year').value;
		var page_link="requires/embellishment_qc_report_controller.php?action=order_no_popup&cbo_party_name="+cbo_party_name+"&year="+year;
		var title="Order No";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=420px,center=1,resize=0,scrolling=0','../')
	
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			//var job=theemail.split("_");
			document.getElementById('txt_order_no').value=theemail;
			release_freezing();
		}
	}

	function job_search_popup(page_link,title)
	{
		if ( form_validation('cbo_company_id*cbo_buyer_id','Company Name*Party Name')==false )
		{
			return;
		}
		else
		{
			var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_buyer_id').value+"_"+document.getElementById('cbo_within_group').value;
			page_link='requires/embellishment_qc_report_controller.php?action=job_popup&data='+data
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=400px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				freeze_window(5);
				var theform=this.contentDoc.forms[0];
				var theemail=this.contentDoc.getElementById("selected_order").value;
				$("#txt_job_no").val( theemail );
				
				var list_view_orders = return_global_ajax_value( 0+'**'+theemail+'**'+1, 'load_php_dtls_form', '', 'requires/embellishment_qc_report_controller');
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

</script>
</head>
<body onLoad="set_hotkey();">
<form id="workProgressReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:1200px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1200px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>                    
                    <th width="135" class="must_entry_caption">Company</th>		
                    <th width="100">Within Group </th>
                    <th width="125">Party </th>
                    <th width="80" style="display:none">Type</th>
                    <th width="60">Year</th>                     
                    <th width="60">Job No</th>
                    <th width="80">Style Ref.</th>
                    <th width="80">Order No</th>
                    <th width="160">Production Date</th>
					<th width="100">Buyer </th>
                    <th width=""><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                </thead>
                <tbody>
                    <tr >
                        <td> 
                        <? echo create_drop_down( "cbo_company_id", 135, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/embellishment_qc_report_controller', this.value+'_'+document.getElementById('cbo_within_group').value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down('requires/embellishment_qc_report_controller', this.value, 'load_drop_down_mainbuyer', 'mainbuyer_td' );"); ?>
                    	</td>
						
                    	<td>
							<?php echo create_drop_down( "cbo_within_group", 100, $yes_no,"", 0, "--  --", 0, "load_drop_down( 'requires/embellishment_qc_report_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?>
						</td>
                        <td id="buyer_td">
                        	<? 
                        		echo create_drop_down( "cbo_buyer_id", 125, $blank_array,"", 1, "-- Select Party --", $selected, "" );
                        	?>
                    	</td>
                        <td  style="display:none">
                            <? 
                                $search_by_arr = array(1=>"Order Wise",2=>"Style Wise");
                                echo create_drop_down( "cbo_search_by", 80, $search_by_arr,"",0, "", "",'',0 );//search_by(this.value)
                             ?>
                        </td>
                        <td>
                            <?
                                $selected_year=date("Y");
                                echo create_drop_down( "cbo_year", 60, $year,"", 1, "--Select Year--", $selected_year, "",0 );
                            ?>
                        </td>
                        <td>
                    		<input class="text_boxes"  type="text" name="txt_job_no" id="txt_job_no" onDblClick="job_search_popup();" placeholder="Double Click" style="width:55px;" readonly/>
                    	</td>
                        <td>
                            <input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:75px" placeholder="Wr/Br Style" onDblClick="openmypage_style();" >
                        </td>
                        <td>
                            <input name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:75px"  placeholder="Wr/Br Order" onDblClick="openmypage_order();" >
                            <input type="hidden" name="txt_order_id" id="txt_order_id" class="text_boxes" style="width:70px">
                        </td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" >&nbsp; To
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"  placeholder="To Date"  >
                        </td>
						<td id="mainbuyer_td"  width="150">
							<? echo create_drop_down( "cbo_mainbuyer_id", 120, $blank_array,"", 1, "--Select Buyer--", $selected, "" ); ?>
						</td>
                        
                        <td align="center">
                            <input type="button" id="show_button" class="formbutton" style="width:50px" value="Show" onClick="fn_report_generated(1)" />
							<input type="button" id="show_button" class="formbutton" style="width:50px" value="Print" onClick="fn_report_generated(2)" />
							<input type="button" id="show_button" class="formbutton" style="width:90px" value="Qc Balance" onClick="fn_report_generated(3)" />							
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="10" align="center">
							<? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                </tfoot>
           </table> 
           <br />
        </fieldset>
    </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

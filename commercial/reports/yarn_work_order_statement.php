<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Yarn Work Order Statement
				
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	08-05-2014
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
echo load_html_head_contents("Yarn Work Order Statement","../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	var tableFilters1 = 
	{
		col_18: "none",
		col_operation: {
		id: ["total_qty","total_amount"],
		col: [14,16],
		operation: ["sum","sum"],
		write_method: ["innerHTML","innerHTML"]
		}
	}

	var tableFilters3 = 
	{
		col_18: "none",
		col_operation: {
		id: ["total_qty","total_amount"],
		col: [13,15],
		operation: ["sum","sum"],
		write_method: ["innerHTML","innerHTML"]
		}
	}
	
	var tableFilters2 = 
	{
		col_14: "none",
		col_operation: {
		id: ["total_qty","total_amount"],
		col: [10,12],
		operation: ["sum","sum"],
		write_method: ["innerHTML","innerHTML"]
		}
	} 

	function openmypage_pi_date(import_invoice_id,suppl_id,item_id,pi_id,curr_id,action,title)
	{
		var popup_width="";
		if(action=="pi_details") popup_width="900px"; else popup_width="850px";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/yarn_work_order_statement_controller.php?import_invoice_id='+import_invoice_id+'&suppl_id='+suppl_id+'&item_id='+item_id+'&pi_id='+pi_id+'&curr_id='+curr_id+'&action='+action, title, 'width='+popup_width+',height=390px,center=1,resize=0,scrolling=0','../');
	}
	
	function openmypage_inHouse_date(pi_id,receive_value,receive_qnty,action,title)
	{
		var popup_width="";
		if(action=="pi_rec_details") popup_width="620px"; else popup_width="620px";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/yarn_work_order_statement_controller.php?pi_id='+pi_id+'&receive_value='+receive_value+'&receive_qnty='+receive_qnty+'&action='+action, title, 'width='+popup_width+',height=390px,center=1,resize=0,scrolling=0','../');
	}	
	
	function generate_report(operation)
	{
		var cbo_wo_basis = $("#cbo_wo_basis").val();
		var txt_wo_no = $("#txt_wo_no").val();
		var txt_date_from = $("#txt_date_from").val();
		var txt_date_to = $("#txt_date_to").val();
		var txt_job_no = $("#txt_job_no").val();
		if(form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
		else
		{
            if(txt_wo_no == "" && txt_date_from == "" && txt_date_to == "" && txt_job_no=="")
            {
                alert("Either Select Work Order Or Date Range");
                $("#txt_wo_id").focus();
                $("#txt_date_from").focus();
                $("#txt_date_to").focus();
                return;
            }

			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_wo_basis*cbo_buyer_id*cbo_year*cbo_month*txt_job_no*txt_job_id*txt_order_no*txt_order_id*txt_wo_no*txt_wo_id*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
			freeze_window(3);
			http.open("POST","requires/yarn_work_order_statement_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			var tot_rows=reponse[2];
			$('#report_container2').html(reponse[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			var tablehtml=document.getElementById("cbo_wo_basis").value;
			//alert (tablehtml)
			if (tablehtml==3)
			{
				setFilterGrid("table_body",-1,tableFilters1);
			}
			else if(tablehtml==1)
			{
				setFilterGrid("table_body",-1,tableFilters3);
			}
			else
			{
				setFilterGrid("table_body",-1,tableFilters2);
			}
	 		show_msg('3');
			release_freezing();
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
	
	function openmypage_job()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year_id = $("#cbo_year").val();
		var cbo_month_id = $("#cbo_month").val();
		var page_link='requires/yarn_work_order_statement_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id+'&cbo_month_id='+cbo_month_id;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			
			$('#txt_job_no').val(job_no);
			$('#hide_job_id').val(job_id);	 
		}
	}
	
	function openmypage_order()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('txt_job_no').value;
		//alert (data);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/yarn_work_order_statement_controller.php?action=order_no_popup&data='+data,'Order No Popup', 'width=700px,height=420px,center=1,resize=0','../')
		
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
	
	function openmypage_wo()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_year').value+'_'+document.getElementById('txt_order_id').value+'_'+document.getElementById('cbo_wo_basis').value;
		//alert (data);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/yarn_work_order_statement_controller.php?action=wo_no_popup&data='+data,'Work Order Popup', 'width=500px,height=420px,center=1,resize=0','../')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("work_order_no_id");
			var theemailv=this.contentDoc.getElementById("work_order_no_val");
			var response=theemail.value.split('_');
			if (theemail.value!="")
			{
				freeze_window(5);
				document.getElementById("txt_wo_id").value=theemail.value;
			    document.getElementById("txt_wo_no").value=theemailv.value;
				release_freezing();
			}
		}
	}
	
	function active_inactive(val)
	{
		$('#cbo_buyer_id').removeAttr('disabled','disabled');
		$('#cbo_year').removeAttr('disabled','disabled');
		$('#cbo_month').removeAttr('disabled','disabled');
		$('#txt_job_no').removeAttr('disabled','disabled');
		$('#txt_order_no').removeAttr('disabled','disabled');
		
		if (val==2)
		{
			$('#cbo_buyer_id').attr('disabled','disabled');
			$('#cbo_year').attr('disabled','disabled');
			$('#cbo_month').attr('disabled','disabled');
			$('#txt_job_no').attr('disabled','disabled');
			$('#txt_order_no').attr('disabled','disabled');
		}
		else if(val == 1)
		{
			$('#txt_job_no').removeAttr('disabled','disabled');
			$('#txt_order_no').attr('disabled','disabled');
			$('#cbo_buyer_id').removeAttr('disabled','disabled');
		}
		else
		{
			$('#cbo_buyer_id').removeAttr('disabled','disabled');
			$('#cbo_year').removeAttr('disabled','disabled');
			$('#cbo_month').removeAttr('disabled','disabled');
			$('#txt_job_no').removeAttr('disabled','disabled');
			$('#txt_order_no').removeAttr('disabled','disabled');
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
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflow="auto"; 
		document.getElementById('scroll_body').style.maxHeight="250px";
		$('#table_body tr:first').show();
	}
</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../","");  ?> 		 
        <form name="yarnworkorderstatement_1" id="yarnworkorderstatement_1" autocomplete="off" > 
    <h3 style="width:1080px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:1080px;">
                <table class="rpt_table" width="1070" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th width="140" class="must_entry_caption">Company</th>
                            <!-- <th width="120" class="must_entry_caption">Category</th> -->
                            <th width="100">WO Basis</th>
                            <th width="140">Job Buyer</th>
                            <th width="60">Year</th>
                            <th width="80">Month</th>
                            <th width="100">Job</th>
                            <th width="100">Order</th>
                            <th width="100">Work Order</th>
                            <th width="">Date Range</th>
                            <th width="70"><input type="text" name="res" id="res" value="Reset" style="width:70px;text-align: center;" class="formbutton" onClick="reset_form('yarnworkorderstatement_1','report_container*report_container2','','','','res*cbo_wo_basis*cbo_year_selection');" /></th>
                        </tr>
                    </thead>
                    <tr align="center">
                        <td>
                            <? 
                               echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/yarn_work_order_statement_controller',this.value+'_'+1+'_'+4, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>                            
                        </td>

                        <td> 
                            <?
                                echo create_drop_down( "cbo_wo_basis", 100, $wo_basis,"", 0, "", 3, "active_inactive(this.value)",0,"1,2,3");
                            ?>
                        </td>
                        <td id="buyer_td"> 
                            <?
                                echo create_drop_down( "cbo_buyer_id", 140, $blank_array,"", 1, "--Select Buyer--", 0, "",0 );
                            ?>
                        </td>
                        <td> 
                            <?
								$selected_year=date("Y");
                                echo create_drop_down( "cbo_year", 60, $year,"", 1, "-Year-", $selected_year, "",0 );
                            ?>
                        </td>
                        <td> 
                            <?
								$selected_month=date("m");
                                echo create_drop_down( "cbo_month", 80, $months,"", 1, "--Select Month--", 0, "",0 );
                            ?>
                        </td>
                        <td>
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:85px" onDblClick="openmypage_job();" placeholder="Browse Or Write" />
                            <input type="hidden" id="txt_job_id" name="txt_job_id" class="text_boxes" style="width:60px" />
                        </td>
                        <td>
                            <input type="text" id="txt_order_no" name="txt_order_no" class="text_boxes" style="width:85px" onDblClick="openmypage_order();" placeholder="Browse Order" readonly />
                            <input type="hidden" id="txt_order_id" name="txt_order_id" class="text_boxes" style="width:60px" />
                        </td>
                        <td>
                            <input type="text" id="txt_wo_no" name="txt_wo_no" class="text_boxes" style="width:85px" onDblClick="openmypage_wo();" placeholder="Browse Or Write" />
                            <input type="hidden" id="txt_wo_id" name="txt_wo_id" class="text_boxes" style="width:60px" />
                        </td>
                        <td>
                                <input type="text" name="txt_date_from" id="txt_date_from" value="<? //echo date("d-m-Y", time());?>" class="datepicker" style="width:60px;" placeholder="From Date" readonly />
                                To
                                <input type="text" name="txt_date_to" id="txt_date_to" value="<? //echo date("d-m-Y", time());?>" class="datepicker" style="width:60px;" placeholder="To Date" readonly />
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report()" style="width:70px" class="formbutton" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="11" align="center">
                            <? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                </table> 
            </fieldset> 
        </div>
            <div id="report_container" align="center"></div>
            <div id="report_container2"></div>   
    </form>    
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

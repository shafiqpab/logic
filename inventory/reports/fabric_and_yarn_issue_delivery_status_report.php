<?
/*-------------------------------------------- Comments
Purpose			: 	
				
Functionality	:	
JS Functions	:
Created by		:	
Creation date 	: 	23-06-2018
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
echo load_html_head_contents("Fabric And Yarn Issue Delivery Status Report","../../", 1, 1, $unicode,1,1); 
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	var tableFilters = 
	 {
		 col_operation: {
			 id: ["value_yarn_req_qnty","value_yarn_issue_qnty","value_grey_prod_qnty","value_grey_issue_qnty","value_grey_balance_qnty","value_finish_receive_qnty"],
			 col: [8,10,11,13,14,16],
			 operation: ["sum","sum","sum","sum","sum","sum"],
			 write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		 }
	 }

	function generate_report(rpt_type)
	{
		var job_no = $("#txt_job_no").val().trim();
		var style_no = $("#txt_style_no").val().trim();
		var order_no = $("#txt_order_no").val().trim();
		if(job_no =="" && style_no =="" && order_no ==""){
			var valid_id = "cbo_company_id*txt_date_from*txt_date_to";
			var valid_msg = "Company Name*From Date*To Date";
		}else
		{
			var valid_id = "cbo_company_id";
			var valid_msg = "Company Name";
		}
		if( form_validation(valid_id,valid_msg)==false )
		{
			return;
		}

		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_year*txt_job_no*txt_job_id*txt_style_no*txt_order_no*txt_order_id*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title+'&rpt_type='+rpt_type;
		//alert (data);
		freeze_window(3);
		http.open("POST","requires/fabric_and_yarn_issue_delivery_status_report_controller.php",true);
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
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("table_body",-1,tableFilters);
			show_msg('3');
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
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		$("#table_body tr:first").show();
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
		var page_link='requires/fabric_and_yarn_issue_delivery_status_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			$('#txt_job_no').val(job_no);
			$('#txt_job_id').val(job_id);	 
		}
	}
	function openmypage_style()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year_id = $("#cbo_year").val();
		var txt_job_no = $("#txt_job_no").val();
		var page_link='requires/fabric_and_yarn_issue_delivery_status_report_controller.php?action=style_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id+'&txt_job_no='+txt_job_no;
		var title='Style No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=430px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var style_no=this.contentDoc.getElementById("hidden_style_no").value;
			$('#txt_style_no').val(style_no);
		}
	}
	
	function openmypage_order()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('txt_job_no').value;

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/fabric_and_yarn_issue_delivery_status_report_controller.php?action=order_no_popup&data='+data,'Order No Popup', 'width=630px,height=420px,center=1,resize=0','../')
		
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

	function clr_hidden(ref) 
	{
		if(ref == 1)
		{
			$("#txt_job_id").val("");
		}
		else
		{
			$("#txt_order_id").val("");
		}
	}
	
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission); ?>  		 
    <form name="orderwisegreyfabricstock_1" id="orderwisegreyfabricstock_1" autocomplete="off" > 
    <h3 style="width:900px; margin-top:10px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:900px;">
                <table class="rpt_table" width="900" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th class="must_entry_caption">Company</th>                                
                            <th>Buyer</th>
                            <th>Year</th>
                          	<th>Job No</th>
                            <th>Style No</th>
                            <th>Order No</th>
                            <th class="must_entry_caption">Shipment Date</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_form('orderwisegreyfabricstock_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td>
                            <? 
                               echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/fabric_and_yarn_issue_delivery_status_report_controller',this.value+'_'+1+'_'+4, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>                            
                        </td>
                        <td id="buyer_td"> 
                            <?
                                echo create_drop_down( "cbo_buyer_id", 140, $blank_array,"", 1, "--Select Buyer--", 0, "",0 );
                            ?>
                        </td>
                        <td> 
                            <?
								echo create_drop_down( "cbo_year", 70, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
                            ?>
                        </td>
						<td>
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes_numeric" style="width:70px" onDblClick="openmypage_job();" placeholder="Browse/Write" onchange="clr_hidden(1);" />
                            <input type="hidden" id="txt_job_id" name="txt_job_id"/>
                        </td>
                        <td>
                            <input type="text" id="txt_style_no" name="txt_style_no" class="text_boxes" style="width:80px" onDblClick="openmypage_style();" placeholder="Browse/Write" />
                            <input type="hidden" id="hidden_style_no" name="hidden_style_no"/>
                        </td>
                        <td>
                            <input type="text" id="txt_order_no" name="txt_order_no" class="text_boxes" style="width:80px" onDblClick="openmypage_order();" placeholder="Browse/Write"  onchange="clr_hidden(2);"/>
                            <input type="hidden" id="txt_order_id" name="txt_order_id"/>
                        </td>
                         
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px;" readonly/>
                            To			
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px;" readonly/>				
                        </td>
                        
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:80px" class="formbutton" />
                        </td>
                    </tr>
                    <tr> 
                    	<td colspan="8" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
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

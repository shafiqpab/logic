<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Post Cost And CM Analysis Report.
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	10-08-2020
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
echo load_html_head_contents("Post Cost And CM Analysis Report","../../../", 1, 1, $unicode,1,1);
$date_type_arr=array(1=>"Ex-Factory Date", 2=>"Packing and Finishing Date");
?>	
<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php"; 
	
	var tableFilters =
	{
		//col_0: "none",
		col_operation: 
		{
			id: ["value_styleQty","value_styleVal","value_yarnBom","value_dyeingBom","value_knittingBom","value_fabricBom","value_otherFabBom","value_accBom","value_emblWashBom","value_testBom","value_otherBom","value_totalBom","value_yarnPost","value_dyeingPost","value_knittingPost","value_fabricPost","value_otherFabPost","value_accPost","value_emblWashPost","value_testPost","value_otherPost","value_totalPost","value_yarnSD","value_dyeingSD","value_knittingSD","value_fabricSD","value_otherFabSD","value_accSD","value_emblWashSD","value_testSD","value_otherSD","value_totalSD","value_shipQty","value_commission","value_shipVal","value_cmBom","value_cmPost","value_shortQty","value_shortVal","value_excessQty","value_excessVal","value_upcharge","value_discount","value_adjCm","value_prevShipQty","value_totAgentComm","value_inHouseQty","value_subconQty","value_yarnIssueQty","value_fabricIssueCut"],
			
			//col: [5,7,8,9,10,11,12,13,14,15,16,18,19,20,21,22,23,24,25,26,28,29,30,31,32,33,34,35,36,38,40,41,44,46,47,48,49,50,51,52],
			col: [6,8,9,10,11,12,13,14,15,16,17,18,20,21,22,23,24,25,26,27,28,29,31,32,33,34,35,36,37,38,39,40,42,44,45,48,50,51,52,53,54,55,56,57,61,62,63,64,65,66],
			
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	 
	function fn_report_generated(report)
	{
		/*var job_no=$("#txt_job_no").val();
		if(job_no!='')
		{
			if(form_validation('cbo_company_name*txt_job_no','Company*Job No')==false)
			{
				return;
			}
		}
		else
		{*/
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company*From Date*To Date')==false)
			{
				return;
			}
		//}
		
		var report_title=$("div.form_caption").html();	
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*txt_job_id*cbo_year*txt_date_from*txt_date_to*cbo_date_type',"../../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/post_cost_cm_analysis_report_controller.php",true);
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
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:135px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Html Preview" name="Print" class="formbutton" style="width:120px"/>'; 
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
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		
		document.getElementById('scroll_body').style.overflow="scroll";
		document.getElementById('scroll_body').style.maxHeight="400px";

		$("#table_body tr:first").show();
	}
	
	function new_window2(comp_div, container_div)
	{
		document.getElementById(comp_div).style.visibility="visible";
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById(container_div).innerHTML+'</body</html>');
		document.getElementById(comp_div).style.visibility="hidden";
		d.close();
	}
	
	function openmypage_job(type)
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		
		var companyID = $("#cbo_company_name").val();
		var buyer_name = $("#cbo_buyer_name").val();
		var cbo_year_id = $("#cbo_year").val();
		var txt_job_no = $("#txt_job_no").val();
		//var cbo_month_id = $("#cbo_month").val();
		var page_link='requires/post_cost_cm_analysis_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id+'&txt_job_no='+txt_job_no+'&type='+type;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=830px,height=370px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			if(type==1)
			{
				$('#txt_job_no').val(job_no);
				$('#txt_job_id').val(job_id);	 
			}
			else
			{
				$('#txt_order_no').val(job_no);
				$('#hide_order_id').val(job_id);
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
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/post_cost_cm_analysis_report_controller.php?action=order_no_popup&data='+data,'Order No Popup', 'width=630px,height=420px,center=1,resize=0','../../')
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
	
	/*function search_by(val,type)
	{
		$('#txt_date_from').val('');
		$('#txt_date_to').val('');
		if(val==1) $('#date_td').html('Country Shipment Date');
		else if(val==2) $('#date_td').html('Pub. Ship Date');
		else if(val==3) $('#date_td').html('Org. Ship Date');
		else if(val==4) $('#date_td').html('PO Insert Date');
		else $('#date_td').html('Shipment Date');
	}*/
</script>
</head>
<body onLoad="set_hotkey();">
<form id="cost_breakdown_rpt">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:800px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:800px;">
                <table class="rpt_table" width="800" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                	<thead>
                    	<tr>                   
                            <th width="150" class="must_entry_caption">Company Name</th>
							<th width="60">Job Year</th>
                            <th width="150">Buyer Name</th>
                    		<th width="80">Job No</th>
                            <th width="130" class="must_entry_caption" colspan="2" id="date_td"><?echo create_drop_down( "cbo_date_type", 150, $date_type_arr,"", 0, "-- All Date --", $selected, "",0,"" ); ?></th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                        </tr>
                     </thead>
                    <tbody>
                        <tr class="general">
                            <td><? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/post_cost_cm_analysis_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' ); " );//get_php_form_data(this.value, 'load_variable_settings', 'requires/post_cost_cm_analysis_report_controller'); ?>
                            </td>
                            <td><? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", 1, "",0,"" );//date("Y",time()) ?></td>
                            <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" ); ?></td>
                            <td>
                                <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:70px" onDblClick="openmypage_job(1);" placeholder="Wr./Br. Job" />
                                 <input type="hidden" id="txt_job_id" name="txt_job_id"/>
                            </td>
                           <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" ></td>
                           <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"  placeholder="To Date" ></td>
                           <td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(1);" /></td>
                        </tr>
                        <tr>
                            <td colspan="8" align="center"><? echo load_month_buttons(1); ?></td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
 </form>    
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Monthly Purchase Requisition.
Functionality	:	
JS Functions	:
Created by		:	Nayem 
Creation date 	: 	12-03-2022
Updated by 		: 	Rakib	
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
echo load_html_head_contents("Monthly Purchase Requisition", "../../", 1, 1,'','','');
?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';

	function openmypage_requisition()
	{
		if (form_validation('cbo_company_name','Company Name')==false)
		{
		  return;
		}
		else
		{
		
	  		var cbo_company_name = $("#cbo_company_name").val();
	  		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/monthly_purchase_requisition_controller.php?cbo_company_name='+cbo_company_name+'&action=purchase_requisition_popup', 'Purchase Requisition Search', 'width=1040px,height=400px,center=1,resize=1,scrolling=0','../')
	  		emailwindow.onclose=function()
	  		{
	  			var data=this.contentDoc.getElementById("selected_req").value.split("__");
	  			$("#txt_req_id").val(data[0]);
	  			$("#txt_req_no").val(data[1]);
	  		}
		
		}
	}
	
	function generate_report(rep_type)
	{

		if(form_validation('cbo_company_name*cbo_req_year*txt_req_no*txt_date_from*txt_date_to','Company Name*Requisition Year*Requisition No*From Date*To Date')==false)
		{
			return;
		}
		
		var report_title=$( "div.form_caption" ).html();	
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_location*cbo_store_name*cbo_req_year*txt_req_no*txt_req_id*txt_date_from*txt_date_to*cbo_template_id',"../../")+'&report_title='+report_title;
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/monthly_purchase_requisition_controller.php",true);
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
		$(".set_table_width").css("width", "2000px");
		$(".column_hide").hide();		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');// media="print"
		d.close();
		$(".set_table_width").css("width", "2480px");
		$(".column_hide").show();		
	}


</script>
</head>

<body onLoad="set_hotkey();">
<form id="monthlyPurchaseRequisition">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",''); ?>
        <h3 align="left" id="accordion_h1" style="width:1060px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" > 
            <fieldset style="width:1060px;">
                <table class="rpt_table" width="1060" border="1" rules="all" cellpadding="1" cellspacing="2" align="center">
                	<thead>
                   		<tr>                    
                            <th width="160" class="must_entry_caption">Company</th>
                            <th width="130">Location</th>
                            <th width="130">Store</th>
                            <th width="110" class="must_entry_caption">Requisition Year</th>
                            <th width="150" class="must_entry_caption">Requisition No</th>
							<th width="180" class="must_entry_caption">Date Range</th>
                            <th width="100" >Template</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form('monthlyPurchaseRequisition','report_container*report_container2','','','')" /></th>
                        </tr>
                     </thead>
                    <tbody>
                        <tr class="general">
                            <td align="center"> 
								<?
                                echo create_drop_down( "cbo_company_name", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/monthly_purchase_requisition_controller', this.value, 'load_drop_down_location', 'location_td' );" );
                                ?>
                            </td>
                            <td align="center" id="location_td">
                            	<? echo create_drop_down( "cbo_location", 130, $blank_array,"", 1,"-- Select --",0,"" ); ?>
                            </td>
                            <td id="store_td"> 
								<?	echo create_drop_down( "cbo_store_name", 130, $blank_array,"", 1,"-- Select --",0,"" ); ?>
							</td>
                            <td>
                            	<? echo create_drop_down( "cbo_req_year", 80, $year,"", 0, "-- Year --", date('Y'), "" ); ?>
                            </td>
							<td align="center">
                            	<input type="text" name="txt_req_no" id="txt_req_no" value="" class="text_boxes" style="width:150px" placeholder="Browse" onDblClick="openmypage_requisition();" readonly />
                            	<input type="hidden" name="txt_req_id" id="txt_req_id">
                            </td>
							<td>
								&nbsp;&nbsp;<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date">
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date">
							</td>
							<td>
								<? echo create_drop_down( "cbo_template_id", 100, $report_template_list,'', 0, '', 0, "");?>
                            </td>
                            <td>
                            	<input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:70px" class="formbutton" />
                            </td>
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
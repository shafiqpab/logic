<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Line Wise Fabric Report
				
Functionality	:	
JS Functions	:
Created by		:	Aziz
Creation date 	: 	5-05-2020
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
echo load_html_head_contents("Line Wise Fabric Req.","../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report()
	{
		if( form_validation('cbo_company_id*txt_date_from','Company Name*Production Date')==false )
		{
			return;
		}

		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_floor_id*hidden_floor_id*txt_date_from*hidden_line_id',"../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/daily_line_wise_fabric_req_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{
			//alert (http.responseText);
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);  
			
			release_freezing();
			//alert(reponse[1]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			show_msg('3');
			release_freezing();
		}
	} 

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none"; 
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="auto"; 
		document.getElementById('scroll_body').style.maxHeight="400px";
	}
		
	
	function getFloorId()
	{
		var cbo_company_id = document.getElementById('cbo_company_id').value;
		var cbo_location_id = document.getElementById('cbo_location_id').value;
		var cbo_floor_id = document.getElementById('cbo_floor_id').value;

		load_drop_down( 'requires/daily_line_wise_fabric_req_report_controller', cbo_floor_id+'_'+cbo_location_id+'_'+cbo_company_id, 'load_drop_down_line', 'line_td' );
		set_multiselect('cbo_line','0','0','','');
	}
	 
	function fnc_openmypage_line()
	{
		if( form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var company = $("#cbo_company_id").val();	
		var location=$("#cbo_location_id").val();
		var floor_id=$("#cbo_floor_id").val();
		var hidden_floor_id = $("#hidden_floor_id").val();
		var txt_date='';
		var page_link='requires/daily_line_wise_fabric_req_report_controller.php?action=line_popup&company='+company+'&location='+location+'&floor_id='+floor_id+'&hidden_floor_id='+hidden_floor_id+'&txt_date='+txt_date;
		
		var title="Search line Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=350px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var prodID=this.contentDoc.getElementById("txt_selected_id").value;
			
			var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
			$("#cbo_line").val(prodDescription);
			$("#hidden_line_id").val(prodID); 
		}
	}
	
	function fnc_openmypage_floor()
	{
		if( form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var company = $("#cbo_company_id").val();	
		var location=$("#cbo_location_id").val();
		var floor_id=$("#cbo_floor_id").val();
	
		var page_link='requires/daily_line_wise_fabric_req_report_controller.php?action=floor_popup&company='+company+'&location='+location+'&floor_id='+floor_id; 
		
		var title="Search Floor Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=350px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var prodID=this.contentDoc.getElementById("txt_selected_id").value;
			
			var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
			$("#cbo_floor_id").val(prodDescription);
			$("#hidden_floor_id").val(prodID);  
		}
	}		 
</script>
</head>
<body onLoad="set_hotkey();">
<form id="LineWiseProductivityAnalysis_1">
    <div style="width:100%;" align="center">    
		<? echo load_freeze_divs ("../../",'');  ?>
		<h3 style="width:840px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
		<div id="content_search_panel" >      
			<fieldset style="width:840px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th width="160" class="must_entry_caption">Company</th>
                    <th width="160">Location</th>
                    <th width="160">Floor</th>
                    <th width="160">Line No</th>
                    <th width="100" class="must_entry_caption">Prod. Date</th>
                    <th width="100" colspan="2"><input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('LineWiseProductivityAnalysis_1','report_container','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td>
                        <? 
                        echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/daily_line_wise_fabric_req_report_controller', this.value, 'load_drop_down_location', 'location_td' );" );
                        ?>                            
                        </td>
                        <td id="location_td">
                        <? 
                        echo create_drop_down( "cbo_location_id", 150, $blank_array,"", 1, "-- Select Location --", "", "" );
                        ?>                            
                        </td>
                        <td id="floor_td">
                        <? 
                        //echo create_drop_down( "cbo_floor_id", 170, $blank_array,"", 1, "-- Select Floor --", "", "" );
                        ?>
                        <input type="text" id="cbo_floor_id"  name="cbo_floor_id"  style="width:150px" class="text_boxes" onDblClick="fnc_openmypage_floor()" placeholder="Browse"  readonly/>
                        <input type="hidden" id="hidden_floor_id" name="hidden_floor_id" />                              
                        </td>
                        <td id="line_td">
                        <? 
                        // echo create_drop_down( "cbo_line", 120, $blank_array,"", 1, "-- Select Line --", "", "" );
                        ?>  
                        <input type="text" id="cbo_line"  name="cbo_line"  style="width:150px" class="text_boxes" onDblClick="fnc_openmypage_line()" placeholder="Browse"  readonly/><input type="hidden" id="hidden_line_id" name="hidden_line_id" />                          
                        </td>
                        <td>
                        <input type="text" name="txt_date_from" id="txt_date_from" value="<?php echo date('01-m-Y'); ?>" class="datepicker" style="width:100px" /> 
                        <!--<strong>To</strong> 
                        <input type="text" name="txt_date_to" id="txt_date_to" value="<?php echo date('d-m-Y'); ?>"  class="datepicker" style="width:70px" />
                        </td>-->
                        <td>
                        <input type="button" name="search2" id="search2" value="Show" onClick="generate_report()" style="width:100px" class="formbutton" />
                        </td>
                    </tr>
                    <!--<tr>
                        <td colspan="8" align="center" height="30" valign="bottom"  id="month_button_cont">
                        <? //echo load_month_buttons(1); ?>
                        </td>
                    </tr>-->
                </tbody>
            </table>
        </fieldset>
    	</div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="center">
    </div>
 </form>   
</body>
<script>
	set_multiselect('cbo_floor_id','0','0','','0');
</script> 
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
<script>
$("#cbo_location_id").val(0);
setTimeout[($("#cbo_floor a").attr("onclick","disappear_list(cbo_floor_id,'0');getFloorId();") ,3000)]; 
</script>
</html>
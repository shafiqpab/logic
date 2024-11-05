<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Color Wise Sewing Output Report
				
Functionality	:	
JS Functions	:
Created by		:	Shafiq
Creation date 	: 	28-02-2022
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
echo load_html_head_contents("Color Wise Sewing Output Report","../../", 1, 1, $unicode,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
var tableFilters = {}	
var tableFilters2 = {}	
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report(type)
	{
		
		if( form_validation('cbo_working_company_id*txt_date','Working Company*Date')==false )
		{
			return;
		}

		var report_title=$( "div.form_caption" ).html();
		
		var data="action=report_generate"+get_submitted_data_string('cbo_working_company_id*cbo_location_id*cbo_floor_id*cbo_buyer_id*txt_job_no*cbo_job_year*cbo_shipping_status*txt_date*txt_int_ref*cbo_floor_group',"../../")+'&report_title='+report_title+'&type='+type;
		
		//alert(data); return;
		
		freeze_window(3);
		http.open("POST","requires/color_wise_sewing_output_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{	
			if(http.readyState == 4) 
			{
				//alert (http.responseText);
				var reponse=trim(http.responseText).split("####");
				$("#report_container2").html(reponse[0]);  
				
				release_freezing();
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
				
				show_msg('3');
				release_freezing();
			}
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

	function type_wise_fnc(data)
	{
		if(data==1)
		{
			$("#txt_search_val").val('');
			$("#type_wise_name").text("Job No");
			$("#txt_search_val").attr("placeholder","          Job No");
			
		}
		else if(data==2)
		{
			$("#txt_search_val").val('');
			$("#type_wise_name").text("Style");
			$("#txt_search_val").attr("placeholder","            Style");

		}
		else if(data==3)
		{
			$("#txt_search_val").val('');
			$("#type_wise_name").text("PO");
			$("#txt_search_val").attr("placeholder","               PO");
		}
		else
		{
			$("#txt_search_val").val('');
			$("#type_wise_name").text("");
			$("#txt_search_val").attr("placeholder","");
		}
	}
	
	function openProdPopup(param)
	{		
		var page_link='requires/color_wise_sewing_output_report_controller.php?action=open_prod_popup&data='+param;  
		var title="Production Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=370px,center=1,resize=0,scrolling=0','../')
	}

	function openProdPopup2(param)
	{		
		var page_link='requires/color_wise_sewing_output_report_controller.php?action=open_prod_popup2&data='+param;  
		var title="Production Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=370px,center=1,resize=0,scrolling=0','../')
	}

	function openProdPopupWIP(param)
	{		
		var page_link='requires/color_wise_sewing_output_report_controller.php?action=open_prod_popup_wip&data='+param;  
		var title="Production WIP Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=370px,center=1,resize=0,scrolling=0','../')
	}

	function openProdPopupWIP2(param)
	{		
		var page_link='requires/color_wise_sewing_output_report_controller.php?action=open_prod_popup_wip2&data='+param;  
		var title="Production WIP Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=370px,center=1,resize=0,scrolling=0','../')
	}

	function openmypage_searchby() // For Line number
	{
		if( form_validation('cbo_working_company_id','Working Company Name')==false )
		{
			return;
		}
		var w_company = $("#cbo_working_company_id").val();	
		var lc_company = $("#cbo_lc_company_id").val();	
		var buyer = $("#cbo_buyer_id").val();	
		var job_year = $("#cbo_job_year").val();
		var page_link='requires/color_wise_sewing_output_report_controller.php?action=search_by_action&w_company='+w_company+'&lc_company='+lc_company+'&buyer='+buyer+'&job_year='+job_year;  
		var title="Search By Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=680px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_des_no);
			// $("#txt_order").val(style_des);
			var order_id_arr = style_id.split(',');
			var unique_ord_id_arr = Array.from(new Set(order_id_arr));
			var orderIds = unique_ord_id_arr.join(',');

			var order_des_arr = style_des.split(',');
			var unique_ord_des_arr = Array.from(new Set(order_des_arr));
			var orderDes = unique_ord_des_arr.join(',');

			$("#txt_order_id").val(orderIds); 
			$("#txt_search_by").val(orderDes);
		}
	}
	
</script>

</head>
<body onLoad="set_hotkey();">

<form id="StyleandLineWiseProductionReport_1">
    <div style="width:100%;" align="center">    
    
        <? echo load_freeze_divs ("../../",'');  ?>
         
         <h3 style="width:1170px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1170px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th width="120" class="must_entry_caption">Working Company</th>
                    <th width="120">Location</th>
                    <th width="120">Floor</th>
                    <th width="120">Floor Group</th>
                    <th width="120" class="">Buyer</th>
                    <th width="60" class="">Job Year</th>
                    <th width="80">Job No</th>
                    <th width="80">Int. Ref.</th>
                    <th width="100">Shipping Status</th>
                    <th width="80" class="must_entry_caption">Production Date</th>
                    <th >
                    	<input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" onClick="reset_form('StyleandLineWiseProductionReport_1','report_container','','','')" />
                    </th>
                </thead>
                <tbody>
                    <tr class="general">
                       <td id="working_company_td">
							<? 
                                echo create_drop_down( "cbo_working_company_id", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", 0, "load_drop_down( 'requires/color_wise_sewing_output_report_controller', this.value, 'load_drop_down_location', 'location_td' );" );
                            ?>                            
                        </td>
                        <td id="location_td">
                            <? 
                                echo create_drop_down( "cbo_location_id", 120, $blank_array,"", 1, "-- All --", "", "" );
                            ?>                            
                        </td>                        
                        <td id="floor_td">
                            <? 
                                echo create_drop_down( "cbo_floor_id", 120, $blank_array,"", 1, "-- All --", "", "" );
                            ?>                            
                        </td>                         
                        <td id="floor_group_td">
                            <? 
                                 echo create_drop_down( "cbo_floor_group", 100, "SELECT a.group_name from lib_prod_floor a where a.status_active=1 and a.is_deleted=0 and a.group_name is not null group by a.group_name  order by a.group_name","group_name,group_name", 1, "-- All --", $selected, "",0,"" );
                            ?> 
                        </td>
                        <td id="buyer_td">
							<? 
                                echo create_drop_down( "cbo_buyer_id", 120, $blank_array,"", 1, "-- All --", 0, "" );
                            ?>                            
                        </td>                        
                        <td>
                        	<? 
                        	  // $selected_year= date('Y');
                              echo create_drop_down( "cbo_job_year", 60, $year,"", 1, "All Year", "", "" );
                            ?>                            
                        </td>                        
                        <td>
                         <input type="text" id="txt_job_no"  name="txt_job_no"  style="width:80px" class="text_boxes_numeric" placeholder="Write" ondblclick="openmypage_searchby_()" />
                        </td>
                        <td>                          
                         <input type="text" id="txt_int_ref"  name="txt_int_ref"  style="width:80px" class="text_boxes"  placeholder="Write" ondblclick="openmypage_color_()"  />
                        </td>
                        <td>
                          	<?                         	  
                              	echo create_drop_down( "cbo_shipping_status", 100, $shipment_status,"", 1, "-- All --", "", "type_wise_fnc(this.value);" );
                            ?>                         	
                        </td>                         
                        <td>
                            <input name="txt_date" id="txt_date" class="datepicker" style="width:60px" placeholder="From Date" >
                        </td>                        
                        <td>
                            <input type="button" name="search1" id="search1" value="Show" onClick="generate_report(1)" style="width:60px" class="formbutton" />
                            <input type="button" name="search1" id="search1" value="Show 2" onClick="generate_report(2)" style="width:60px" class="formbutton" />
                        </td>
                        
                    </tr>
                </tbody>
            </table>
        </fieldset>
    	</div>
    </div>
    
    <div id="report_container" align="center" style="padding: 10px 0;"></div>
    
    <div id="report_container2" align="left">
    	<div style="float:left; " id="report_container3"></div>
    </div>
 </form>   
</body>

<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>

<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Ship Date Wise Production Analysis Report
Functionality	:	
JS Functions	:
Created by		:	Rakib
Creation date 	: 	31-May-2020
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
echo load_html_head_contents("Ship Date Wise Production Analysis Report", "../../", 1, 1, $unicode, 1, 1);
?>	

<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	
	var tableFiltersSummary =
	{
		col_operation: {
			id: ['tot_order_qty_id','tot_cutting_qty_id','tot_input_qty_id','tot_poly_qty_id','tot_reject_qty_finishing_id','tot_finishing_qty_id','tot_air_qty_foc_id','tot_air_qty_claim_id','tot_sea_qty_id','tot_shipment_qty_id','tot_excess_qty_id','tot_short_qty_id'],
			col: [3,4,5,6,7,8,9,10,11,12,13,14],
			operation: ['sum','sum','sum','sum','sum','sum','sum','sum','sum','sum','sum','sum'],
			write_method: ['innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML']
		}
	} 

	function open_job_no()
	{
		$("#txt_job_no").val("");
		var page_link='requires/ship_date_wise_production_analysis_report_controller.php?action=job_popup';
		var title="Search Job Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform = this.contentDoc.forms[0];
			var job_id  = this.contentDoc.getElementById("hide_job_id").value;
			var job_no  = this.contentDoc.getElementById("hide_job_no").value;
			$("#txt_job_no").val(job_no);
			$("#hidden_job_id").val(job_id);
		}
	}
	
	function open_order_no()
	{
		$("#txt_order_no").val("");
		var page_link='requires/ship_date_wise_production_analysis_report_controller.php?action=order_popup';
		var title="Search Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform  = this.contentDoc.forms[0]; 
			var order_id = this.contentDoc.getElementById("hide_order_id").value;
			var order_no = this.contentDoc.getElementById("hide_order_no").value;
			$("#txt_order_no").val(order_no);
			$("#hidden_order_id").val(order_id); 
		}
	}

	function fn_generate_report(type)
	{

		if( form_validation('cbo_work_company_name*txt_date_from*txt_date_to','Working Company*From Date*To Date')==false )
		{
			return;
		}
		
		var report_title=$( "div.form_caption" ).html();

		var data="action=generate_report"+get_submitted_data_string('cbo_company_name*cbo_work_company_name*cbo_floor_name*cbo_location_name*cbo_buyer_name*txt_job_no*hidden_job_id*txt_order_no*hidden_order_id*txt_date_from*txt_date_to',"../../")+'&type='+type+'&report_title='+report_title;
		//alert(data);return;
		 
		freeze_window(3);
		http.open("POST","requires/ship_date_wise_production_analysis_report_controller.php",true);
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
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			//setFilterGrid("table_body_summary",-1,tableFiltersSummary);
			show_msg('3');
			release_freezing();
		}
	} 

	function new_window()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		//document.getElementById('scroll_body').style.maxHeight="none"; 
		//$(".flt").css("display","none");
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="all" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		//document.getElementById('scroll_body').style.overflowY="scroll"; 
		//document.getElementById('scroll_body').style.maxHeight="400px";
		//$(".flt").css("display","block");
	}
	
	function openmypage_challan_popup(company_id,work_comp_ids,order_id,job_no,buyer_id,location_ids,floor_ids,txt_date_from,txt_date_to,action)
	{
		var data=company_id+'**'+work_comp_ids+'**'+order_id+'**'+job_no+'**'+buyer_id+'**'+location_ids+'**'+floor_ids+'**'+txt_date_from+'**'+txt_date_to;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/ship_date_wise_production_analysis_report_controller.php?data='+data+'&action='+action, 'Channan Details', 'width=630px,height=400px,center=1,resize=0,scrolling=0','../');
	}
	 

	function reset_form()
	{
		$("#hidden_style_id").val("");
		$("#hidden_order_id").val("");
		$("#hidden_job_id").val("");
		
	}

	function change_color(v_id,e_color)
	{
		if (document.getElementById(v_id).bgColor=='#33CC00')
		{
			document.getElementById(v_id).bgColor=e_color;
		}
		else
		{
			document.getElementById(v_id).bgColor='#33CC00';
		}
	}	

	function getCompanyId()
	{	 
		var company_id = document.getElementById('cbo_company_name').value;
	    var work_company_id = document.getElementById('cbo_work_company_name').value;	    
    	
	    if (company_id == 0 && work_company_id !='' )
	    	$('#cbo_company_name').attr("disabled","disabled");	    	

        if(work_company_id !='') 
        {
		    var data="action=load_drop_down_location&choosenCompany="+work_company_id;
		    http.open("POST","requires/ship_date_wise_production_analysis_report_controller.php",true);
		    http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		    http.send(data); 
		    http.onreadystatechange = function(){
		        if(http.readyState == 4) 
		        {
		            var response = trim(http.responseText);
		            $('#location_td').html(response);
		            set_multiselect('cbo_location_name','0','0','','0');
					setTimeout[($("#location_td a").attr("onclick","disappear_list(cbo_location_name,'0');getLocationId();") ,3000)];
		        }			 
	        };
	    }       
	}

	function getLocationId()
	{	 
	    var location_id = document.getElementById('cbo_location_name').value;

	    if(location_id != '')
	    {
	        var data="action=load_drop_down_floor&choosenLocation="+location_id;
	        http.open("POST","requires/ship_date_wise_production_analysis_report_controller.php",true);
	        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	        http.send(data); 
	        http.onreadystatechange = function(){
	            if(http.readyState == 4) {
	                var response = trim(http.responseText);
	                $('#floor_td').html(response);
	                set_multiselect('cbo_floor_name','0','0','','0');
	            }			 
	        };
	    }         
	}
</script>

</head>
 
<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",'');  ?>
	<h3 style="width:1250px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
   	    <div style="width:100%;" align="center" id="content_search_panel">
			<form id="dateWiseProductionReport_1">    
  			<fieldset style="width:1250px;">
        		<table class="rpt_table" width="1250" cellpadding="0" cellspacing="0" align="center" border="1" rules="all">
	                <thead>                    
	                    <tr>
	                        <th width="150">Company</th>
	                        <th width="150" class="must_entry_caption">Working Company</th>
	                        <th width="150">Location</th>
	                        <th width="150">Floor</th>
	                        <th width="120">Buyer</th>
	                        <th width="100">Job No</th>
	                        <th width="100">Order No</th>
	                        <th width="200" class="must_entry_caption">Ex-factory Date</th>
	                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:80px" value="Reset" onClick="reset_form()"/></th>
	                    </tr>   
	                </thead>
            		<tbody>
		                <tr class="general">
		                    <td align="center" id="td_company"> 
		                        <?
		                            echo create_drop_down( "cbo_company_name", 150, "SELECT id, company_name from lib_company where status_active=1 and is_deleted=0 order by company_name","id,company_name", 1, "-- Select Company --", "", "" );
		                        ?>
		                    </td>

		                    <td align="center" id="td_wk_company">
		                        <?
		                            echo create_drop_down( "cbo_work_company_name", 150, "SELECT id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name","id,company_name", 0, "-- Select Company --", "", "" );
		                        ?>
		                    </td>

		                    <td align="center" id="location_td"> 
		                        <?
		                            echo create_drop_down( "cbo_location_name", 150, $blank_array,"","", "-- Select location --", "", "" );
		                            // echo create_drop_down( "cbo_location_name", 200, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0  group by id,location_name  order by location_name","id,location_name", 0, "-- Select location --", $selected, "" );
		                        ?>
		                    </td>

		                    <td align="center" id="floor_td"> 
		                        <?
		                        	echo create_drop_down( "cbo_floor_name", 150, $blank_array,"","", "-- Select floor --", "", "" );
		                            // echo create_drop_down( "cbo_floor_name", 200, "SELECT id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 group by id,floor_name order by floor_name","id,floor_name", 0, "-- Select Floor --", $selected, "" );
		                        ?>
		                    </td>

		                    <td align="center">
		                        <? 
		                        echo create_drop_down( "cbo_buyer_name", 120, "SELECT id, buyer_name from lib_buyer where status_active=1 and is_deleted=0 group by id,buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0,"" );
		                        ?>
		                    </td>

		                    <td>
		                        <input type="text" id="txt_job_no" name="txt_job_no" style="width:100px" class="text_boxes" onDblClick="open_job_no()" placeholder="Browse" readonly />
		                        <input type="hidden" id="hidden_job_id" name="hidden_job_id" />
		                    </td>

		                    <td>
		                        <input type="text" id="txt_order_no" name="txt_order_no" style="width:100px" class="text_boxes" onDblClick="open_order_no()" placeholder="Browse" readonly />
		                        <input type="hidden" id="hidden_order_id" name="hidden_order_id" />
		                    </td>

		                    <td>
		                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date">
		                    	<input type="text" name="txt_to_date" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date">
		                    </td>

		                    <td>
		                        <input type="button" id="show_button" class="formbutton" style="width:80px" value="Show" onClick="fn_generate_report(1)" />                         
		                    </td>
		                </tr>
            		</tbody>
            		<tfoot>
                    <tr>
                        <td colspan="8" align="center">
							<? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                </tfoot>
        		</table>
  			</fieldset>   
			</form> 
		</div>  
		<div id="report_container" ></div>
		<div id="report_container2"></div>  
 	</div>
</body>
<script> 
	set_multiselect('cbo_work_company_name','0','0','','0'); 
	set_multiselect('cbo_location_name','0','0','','0'); 
	set_multiselect('cbo_floor_name','0','0','','0');
	setTimeout[($("#td_wk_company a").attr("onclick","disappear_list(cbo_work_company_name,'0');getCompanyId();") ,3000)];
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

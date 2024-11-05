<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Chalan Wise Closing Report
Functionality	:	
JS Functions	:
Created by		:	Shafiq
Creation date 	: 	27-12-2022
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		: Code is poetry, I try to do that!
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Chalan Wise Closing Report", "../../", 1, 1,$unicode,1,1,1);

?>
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	
	var tableFilters = 
	{
		col_operation: 
		{
			id: ["gr_order_qty","gr_cut_qty","gr_cut_bal","gr_input_qty","gr_input_bal","gr_order_input_bal"],
			col: [9,10,11,12,13,14],
			operation: ["sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	 
	function open_job_no()
	{	
		var buyer_name=$("#cbo_buyer_name").val();
		var cbo_year=$("#cbo_year").val();
		var page_link='requires/challan_wise_closing_report_controller.php?action=job_popup&buyer_name='+buyer_name+'&cbo_year='+cbo_year;
		var title="Search Job No Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			var job_no=this.contentDoc.getElementById("hide_job_no").value;

			$("#txt_job_no").val(job_no);
			$("#hidden_job_id").val(job_id); 
		}
	}

	function fn_generate_report(type)
	{
		var job_id = document.getElementById('hidden_job_id').value;
		var challan_no = document.getElementById('txt_challan_no').value;
		
		if(job_id=="" && challan_no=="")
		{
			if( form_validation('cbo_wo_company_name*txt_date_from*txt_date_to','Working Company Name*Date From* Date To')==false )
			{
				return;
			}
		}
		
		var report_title=$( "div.form_caption" ).html();

		var data="action=generate_report"+get_submitted_data_string('cbo_wo_company_name*cbo_location_name*cbo_floor_name*cbo_buyer_name*hidden_job_id*txt_challan_no*txt_date_from*txt_date_to',"../../")+'&type='+type+'&report_title='+report_title;
		 
		freeze_window(3);
		http.open("POST","requires/challan_wise_closing_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{			 
			var reponse=trim(http.responseText).split("####");
			$("#report_container3").html('');
			$("#report_container2").html(reponse[0]);  
			// document.getElementById('report_container').innerHTML = report_convert_button('../../');
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" ondbclick="exportReportToExcel(this);" name="excel" id="exportBtn" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			// setFilterGrid("table_body",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
	} 

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none"; 
		$(".flt").css("display","none");
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="all" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="scroll"; 
		document.getElementById('scroll_body').style.maxHeight="400px";
		$(".flt").css("display","block");
	} 

	function reset_form()
	{
		$("#hidden_style_id").val("");
		$("#hidden_order_id").val("");
		$("#hidden_job_id").val("");
		
	}	 
	 
	function openmypage(po_break_down_id,item_id,action,country_id)
	{
		 
		if(action==2 || action==3)
			var popupWidth = "width=1050px,height=350px,";
		else if (action==10)
			var popupWidth = "width=550px,height=420px,";
		else
			var popupWidth = "width=800px,height=470px,";
		
		if (action==2)
		{
			var popup_caption="Embl. Issue Details";
		}
		else if (action==3)
		{
			var popup_caption="Embl. Rec. Details";
		}
		else
		{
			var popup_caption="Production Quantity";
		}
		emailwindow=dhtmlmodal.open('EmailBox','iframe','requires/challan_wise_closing_report_controller.php?po_break_down_id='+po_break_down_id+'&item_id='+item_id+'&action='+action+'&country_id='+country_id, popup_caption, popupWidth+'center=1,resize=0,scrolling=0','../');
	}	
	function openmypage_production_popup(search_string,type) 	 		 	 
	{
		var popup_width = 800;
		var popup_height = 400;
		var action = "production_popup";
		var title = (type==1) ? "Cutting Info" : "Sewing Input Info";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/challan_wise_closing_report_controller.php?search_string='+search_string+'&action='+action+'&type='+type, title, 'width='+popup_width+','+'height='+popup_height+',center=1,resize=0,scrolling=0','../');

	}

	
</script>
<style>
.accordion {
  transition: max-height 1s ease-in;
}

.active, .accordion:hover {
  background-color: #ccc; 
}

.panel {
  padding: 0 18px;
  display: none;
  background-color: white;
  overflow: hidden;
}
</style>
</head>
 
<body onLoad="set_hotkey();">
  <div style="width:100%;" align="center"> 
   <? echo load_freeze_divs ("../../",'');  ?>
    <h3 style="width:1050px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
       <div style="width:100%;" align="center" id="content_search_panel">
    <form id="dateWiseProductionReport_1">    
      <fieldset style="width:1050px;">
            <table class="rpt_table" width="1050" cellpadding="0" cellspacing="0" align="center" border="1" rules="all">
               <thead>                    
                       <tr>
                        <th class="must_entry_caption" width="130" >Company</th>
                        <th class="" width="130" >Location</th>
                        <th class="" width="130" >Floor</th>
                        <th width="130">Buyer Name</th>
                        <th class=""  width="100">Job No</th>
                        <th class=""  width="100">Challan No</th>
                        <th class="must_entry_caption"  width="200"> Date Range </th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form()"/></th>
                    </tr>   
              </thead>
                <tbody>
                <tr class="general">
                    <td align="center"> 
                        <?
                            echo create_drop_down( "cbo_wo_company_name", 130, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/challan_wise_closing_report_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/challan_wise_closing_report_controller', this.value, 'load_drop_down_buyer', 'td_buyer' );" );
                        ?>
                    </td>
                    <td align="center" id="location_td"> 
                        <?
                            echo create_drop_down( "cbo_location_name", 130, $blank_array,"",1, "-- Select location --", "", "" );
                        ?>
                    </td>

                    <td align="center" id="floor_td"> 
                        <?
                        	echo create_drop_down( "cbo_floor_name", 130, $blank_array,"",1, "-- Select floor --", "", "" );                            
                        ?>
                    </td>
                    <td align="center" id="td_buyer"> 
                        <?
                            echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"",1, "-- Select Buyer --", "", "" );
                        ?>
                    </td>
                    <td>
                       <input type="text" id="txt_job_no"  name="txt_job_no"  style="width:100px" class="text_boxes" onDblClick="open_job_no()" placeholder="Browse" readonly />
                       <input type="hidden" id="hidden_job_id"  name="hidden_job_id" />
                    </td>
                    <td>
                       <input type="text" id="txt_challan_no"  name="txt_challan_no"  style="width:100px" class="text_boxes_numeric"/>
                       <!-- <input type="hidden" id="hidden_job_id"  name="hidden_job_id" /> -->
                    </td>
                        
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date">&nbsp;
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date">
                    </td>
                    <td>
                         <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_generate_report(1)" />                                            
                         
                     </td>
                    
                </tr>
                <tr>
                	<td colspan="8">
                        <? echo load_month_buttons(1); ?>
                    </td> 
                </tr>
                </tbody>
            </table>
      </fieldset>
    
 </form> 
 </div>
	<div id="report_container" style="margin:10px 0;"></div>
	<div id="all_report_container">
	    <div id="report_container2"></div>  
	    <div id="report_container3"></div> 
    </div> 
 </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

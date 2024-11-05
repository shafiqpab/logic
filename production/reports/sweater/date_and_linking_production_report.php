<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Date Wise Linking Production Report
Functionality	:	
JS Functions	:
Created by		:	Md Mamun Ahmed Sagor
Creation date 	: 	02-10-2021
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
echo load_html_head_contents("Date Wise Linking Production Report","../../../", 1, 1, $unicode,1); 
?>	  
<script src="../../../js/highchart/highcharts.js"></script>
<script src="../../../js/highchart/highcharts-3d.js"></script>
<script src="../../../js/highchart/exporting.js"></script>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	
	var tableFilters = 
		{
			col_operation: {
				id: ["order_qty","knitting_qty","prev_input_qty","today_input_qty","total_input_qty","balance_input_qty","prev_output_qty","today_output_qty","total_output_qty","balance_output_qty"],
				col: [5,6,7,8,9,10,11,12,13,14],
				operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"], 
				write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
			}
		}
		
	function generate_report(type)
	{		
		if( form_validation('cbo_company_id*txt_date','Date')==false )
		{
			return;
		}

		var report_title=$( "div.form_caption" ).html();

		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_buyer_id*txt_job_no*txt_style_ref_no*hide_job_id*txt_date*cbo_floor_id*cbo_line_id',"../../../")+'&report_title='+report_title+'&type='+type;		
		
		// alert(data); return;
		freeze_window(3);
		http.open("POST","requires/date_and_linking_production_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{	
			if(http.readyState == 4) 
			{
				$("#report_container2").html(''); 
				var reponse=trim(http.responseText).split("####");

				// alert(reponse[2]);
				if(reponse[2]=='2')
				{
					showChart(reponse[3], reponse[4], reponse[5]);
					release_freezing();
					return;
				}
				if(reponse[2]=='4')
				{
					showChart2(reponse[3], reponse[6], reponse[4],reponse[5]);
					release_freezing();
					return;
				}

				$("#report_container2").html(reponse[0]); 
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
				if(reponse[2]=='1')
				{
					setFilterGrid("table_body",-1,tableFilters);
				}
				
								
				show_msg('3');
				release_freezing();
			}
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
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_print.css" type="text/css" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="450px";
		
		$("#table_body tr:first").show();
	}
	
    function open_job_no()
	 {
		 if( form_validation('cbo_company_id','Company Name')==false)
				{
					return;
				}
		var company = $("#cbo_company_id").val();	
		var buyer=$("#cbo_buyer_name").val();
	    var page_link='requires/date_and_linking_production_report_controller.php?action=job_no_search_popup&company='+company+'&style=0'; 
		var title="Search Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]; 
				var prodID=this.contentDoc.getElementById("hide_job_id").value;
				//alert(prodID); // product ID
				var prodDescription=this.contentDoc.getElementById("hide_job_no").value; // product Description
				$("#txt_job_no").val(prodDescription);
				$("#hide_job_id").val(prodID); 
				//alert($("#hide_job_id").val())
			}
	 }
     function open_style_ref()
	 {
		 if( form_validation('cbo_company_id','Company Name')==false )
		 {
			return;
		 }
		var company = $("#cbo_company_id").val();	
		
		var page_link='requires/date_and_linking_production_report_controller.php?action=job_no_search_popup&company='+company+'&style=1'; 
		var title="Search Style Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=820px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]; 
				var poID=this.contentDoc.getElementById("hide_job_id").value;
				var styleDescription=this.contentDoc.getElementById("hide_job_no").value; // product Description
				console.log(poID);
				console.log(styleDescription);
				$("#txt_style_ref_no").val(styleDescription);
				$("#hidden_style_id").val(poID); 
			}
	 }	

</script>

  <style type="text/css">
    /*#report_container2 {
      height: 400px;
      min-width: 400px;
      max-width: 960px;
      margin: 0 auto;
    }*/
 </style>
</head>
<body onLoad="set_hotkey();">
<form id="bundleTrackReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../../",'');  ?>
         <h3 style="width:800px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:800px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th width="120" class="must_entry_caption">Company Name</th>
					<th width="120" class="">Location</th>
                    <th width="120">Buyer</th>
                    <th width="80">Job No</th>
                    <th width="100">Style Ref. No.</th>  
                    <th width="100">Linking Floor</th>  
                    <th width="100">Linking Line</th>  
                  
					<th  width="100" class="must_entry_caption" id="process_name"> Date </th>
                    <th width="145">
                    	<input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form( 'bundleTrackReport_1', 'report_container','','','')" />
                    </th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td>
                        	<? echo create_drop_down( "cbo_company_id", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/date_and_linking_production_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/date_and_linking_production_report_controller', this.value, 'load_drop_down_location', 'location_td' );" ); ?>
                        		
                        </td>
                        <td id="location_td">
                        	<? echo create_drop_down( "cbo_location_id", 120, $blank_array,"",1, "-- Select Location --", 0, "" ); ?>
                        </td>

					    <td id="buyer_td">
                        <? echo create_drop_down( "cbo_buyer_id", 120, $blank_array,"", 1, "-- Select Buyer --", 0, "" ); ?>
                        </td>  
                            
                            
                        <td>
                            <input type="text" id="txt_job_no" name="txt_job_no" style="width:70px" class="text_boxes" placeholder="Wr/Br" onDblClick="open_job_no();"/>
                            <input type="hidden" name="hide_job_id" id="hide_job_id" value="">
                        </td>
                        <td>
                        	<input type="text" id="txt_style_ref_no" name="txt_style_ref_no" style="width:90px" class="text_boxes" placeholder="Wr/Br" onDblClick="open_style_ref();"/>
                            <input type="hidden" id="hidden_style_id"  name="hidden_style_id" />
                        </td>                        
						<td id="floor_td">
                        <? echo create_drop_down( "cbo_floor_id", 120, $blank_array,"", 1, "-- Select Floor --", 0, "" ); ?>
                        </td> 
                        
                        <td id="line_td">
                        <? echo create_drop_down( "cbo_line_id", 120, $blank_array,"", 1, "-- Select Line --", 0, "" ); ?>
                        </td> 
                       
						<td>
							<input name="txt_date" id="txt_date" class="datepicker"   style="width:75px" placeholder="Date" >                    		
                        <td>
                        	<input type="button" name="search1" id="search1" value="Show" onClick="generate_report(1);" style="width:70px" class="formbutton" />
                    
						
						</td>
                    </tr>                   
                </tbody>
            </table>
			<table>

            </table> 
        </fieldset>
    	</div>
    </div>
    <div id="report_container" align="center" style="padding: 5px 0;"></div>
    <div id="report_container2" align="left">
    	<!-- <div id="chart_container"></div> -->
    </div>
 </form>   
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>

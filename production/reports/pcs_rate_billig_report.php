<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Pcs Rate Billing Report
Functionality	:	
JS Functions	:
Created by		:	Kamrul Hasan
Creation date 	: 	26-01-2023
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
echo load_html_head_contents("Pcs Rate Billing Report","../../", 1, 1, $unicode,1,1); 
?>	
<script>
	var permission='<? echo $permission; ?>';
 	
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report(type)
	{


		var company = document.getElementById('cbo_company_id').value;
		//var working_company = document.getElementById('cbo_wo_company_id').value;
		if ((company==0 || company=='') ) {
			alert('please select Company '); 
			return;
		}
		if( form_validation('cbo_company_id*cbo_location_id','Company Name*Company Location')==false )
		{
			return;
		}
		var job_no = document.getElementById('txt_job_no').value;
		if (job_no=='') {
			if( form_validation('txt_date_from*txt_date_to','From Date*To Date')==false )
			{
				return;
			}
		}

		var report_title=$( "div.form_caption" ).html();
		
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_rate_category*cbo_process*txt_job_no*hidden_job_id*cbo_floor_name*cbo_table_name*txt_date_from*txt_date_to',"../../")+'&type='+type;
		//freeze_window(3);
		http.open("POST","requires/pcs_rate_billig_report_controller.php",true);
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
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			// setFilterGrid("table_body",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
	} 

	function new_window()
	{
		// document.getElementById('scroll_body').style.overflow="auto";
		// document.getElementById('scroll_body').style.maxHeight="none"; 
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		// document.getElementById('scroll_body').style.overflowY="auto"; 
		// document.getElementById('scroll_body').style.maxHeight="400px";
	}

	function open_job_no()
	{
			if(form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
			var company = $("#cbo_company_id").val();
			var buyer=$("#cbo_buyer_name").val();
			var cbo_year=$("#cbo_year").val();
			var page_link='requires/pcs_rate_billig_report_controller.php?action=job_popup&buyer='+buyer+'&cbo_year='+cbo_year+'&company='+company;
			var title="Search Order Popup";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=390px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]; 
				var job_data=this.contentDoc.getElementById("selected_id").value;
				
				var job_data=job_data.split("_");
				var job_hidden_id=job_data[0];
				var job_no=job_data[1];
			
				$("#txt_job_no").val(job_no);
				$("#hidden_job_id").val(job_hidden_id); 
			
			}
	}
	 
</script>

</head>
<body onLoad="set_hotkey();">

	<form id="Pcs_Rate_Billing_Report_1">
	    <div style="width:100%;" align="center">    
	    
	        <? echo load_freeze_divs ("../../",'');  ?>
	         
	         <h3 style="width:1300px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
	         <div id="content_search_panel" >      
	         <fieldset style="width:1300px;">
	             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
	             	<thead>
	                    <th class="must_entry_caption">Company</th>
	                    <th class="must_entry_caption">Location</th>
	                    <th>Bill For/Section</th>
	                    <th>Process/Operation</th>
	                    <th>Job No</th>
	                    <th>Floor</th>
	                    <th>Table</th>
	                    <th class="must_entry_caption">Date</th>
	                    <th><input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('Pcs_Rate_Billing_Report_1','report_container','','','')" />
						<input type="button" name="search" id="search" style="width:120px" value="Operator Wise Details" onClick="generate_report(1)" style="width:70px" class="formbutton" />&nbsp;
					</th>																	
	                </thead>
	                <tbody>
	                    <tr class="general">
	                       <td>
								<? 
	                                echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0  $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/pcs_rate_billig_report_controller', this.value, 'load_drop_down_location', 'location_td' );" );
	                            ?>                            
	                        </td>
	                        <td id="location_td">
	                            <? 
	                                echo create_drop_down( "cbo_location_id", 140, $blank_array,"", 1, "-- Select Location --", "0", "" );
	                            ?>                            
	                        </td>
	                        
							 <td id="section_td">
								<?
									echo create_drop_down("cbo_rate_category", 80, $rate_category_array,"", 1,"-- Select Section --", 0,"");
								
								?>
							</td>
	                      
							<td >

							<?
								echo create_drop_down("cbo_process", 130, $process_array,"", 1,"-- Select Process--", 0,"");

							?>
				          </td>		
	                     	<td >
								<input type="text"  name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:90px;" placeholder="Write/Browse" onDblClick="open_job_no();">       
								<input type="hidden" id="hidden_job_id"  name="hidden_job_id" />                   
	                    	</td>

	                        <td id="floor_td">
							<? 
	                        echo create_drop_down( "cbo_floor_name", 140, $blank_array,"", 1, "-- Select Floor --", 0); 
	                        ?>      
	                        </td>
	                        <td id="table_id"> 
	                            <?
								echo create_drop_down( "cbo_table_name", 80, $blank_array,"", 1, "-- Select --", 0 , "","");
	                               
	                            ?>
	                        </td>
							
							<td>
								<input name="txt_date_from" id="txt_date_from" class="datepicker"  		style="width:50px" placeholder="From Date" >&nbsp; To

								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px"  placeholder="To Date"  ></td>
	                        </td>                        
	                        
	                        <td>
	                            
	                            <input type="button" name="search" id="search" value="Operator Wise Summary" onClick="generate_report(2)" style="width:130px" class="formbutton" />&nbsp;
	                            <input type="button" name="search" id="search" value="Process & Job Wise Summary " onClick="generate_report(3)" style="width:160px" class="formbutton" />
	                        </td>
	                    </tr>
	                </tbody>
	            </table>
	        </fieldset>
	    	</div>
	    </div>
	    <div id="report_container" align="center"></div>
	    <div id="report_container2" align="left" style="margin: 10px 0"></div>
 	</form>   
</body>

<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
<script>
	$('#cbo_location_id').val(0);
</script>
</html>
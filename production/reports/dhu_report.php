<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Style Wise DHU Report
Functionality	:	
JS Functions	:
Created by		:	Kamrul Hasan
Creation date 	: 	04-05-2023
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
echo load_html_head_contents("DHU Report", "../../", 1, 1,$unicode,1,'');
?>	
<script src="../../Chart.js-master/Chart.js"></script>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';

	function generate_report(report_type)
	{
			if( form_validation('cbo_company_name*cbo_location*cbo_floor*txt_date_from*txt_date_to','Company Name*location*floor*Date Range*Date Range')==false )
			{
				return;
			}	
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_location*cbo_floor*cbo_line_id*txt_int_ref*txt_style_ref*txt_date_from*txt_date_to',"../../")+'&report_type='+report_type;
		freeze_window(3);
		http.open("POST","requires/dhu_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("****");
			show_msg('3');
			release_freezing();
			
			//$("#report_container2").html(reponse[0]);  
			//document.getElementById('report_container').innerHTML = report_convert_button('../../'); 
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:135px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Html Preview" name="Print" class="formbutton" style="width:120px"/>';


		} 
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		//$('#table_body tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 

		//$('#table_body tr:first').show();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="425px";
	}

	function reset_form()
	{
		$("#hidden_style_id").val("");
		$("#hidden_order_id").val("");
		$("#hidden_job_id").val("");
	}
 
</script>
</head>
<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center"> 
		<? echo load_freeze_divs ("../../",'');  ?>
		<h3 style="width:950px;  margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel','')"> -Search Panel</h3>
		<div style="width:100%;" align="center" id="content_search_panel">
			<form id="dateWiseProductionReport_1"  autocomplete="off">    
				<fieldset style="width:950px;">
					<table class="rpt_table" width="930px" cellpadding="0" cellspacing="0" align="center" rules="all">
						<thead>                    
							<tr>
								<th class="must_entry_caption" width="150">Company Name</th> 
                                <th  class="must_entry_caption" width="100">Location</th>
                                <th  class="must_entry_caption" width="100">Floor</th>
                                <th width="100">Line</th>  
								<th width="80">Int. Ref. </th>
								<th width="100">Style</th>                                           
								<th class="must_entry_caption" width="200">Date Range</th> 

								<th><input type="reset" id="reset_btn" class="formbutton" style="width:60px" value="Reset" onClick="reset_form()"/></th>
							</tr>   
						</thead>
						<tbody>
							<tr class="general">
								<td id="company_td"> 
									<?
									echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/dhu_report_controller', this.value, 'load_drop_down_location', 'location_td' );" );
									?>
								</td>
                                
							
								<td width="100" id="location_td">
									<? 
										echo create_drop_down( "cbo_location", 100, $blank_array,"", 1, "-- Select --", $selected, " load_drop_down( 'requires/dhu_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );", 1, "" );
									?>
								</td>
								<td width="100" id="floor_td">
									<? 
										echo create_drop_down( "cbo_floor", 100, $blank_array,"", 1, "-- Select --", $selected, "", 1, "" );
									?>
								</td>
										<td id="line_td">
									<? 
										echo create_drop_down( "cbo_line_id", 100, $blank_array,"", 1, "-- Select Line --", $selected,"",1, "" );
									?>                            
								</td>
								<td>
								   <input type="text" id="txt_int_ref"  name="txt_int_ref"  style="width:80px" class="text_boxes" placeholder="Write" />
								</td>
								<td>
								 <input type="text" id="txt_style_ref"  name="txt_style_ref"  style="width:90px" class="text_boxes" placeholder="Write" />

								</td>
								
								<td align="center">
                                    <input name="txt_date_from" id="txt_date_from" class="datepicker"
                                           style="width:70px"> To
                                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                                </td>


								<td>
									<input type="button" id="show_button1" class="formbutton"   value="Show" onClick="generate_report(1)" />
									
								</td>
								<tr>
                        		<td colspan="12" align="left" width="100%"><? echo load_month_buttons(1); ?></td>
                
						</tbody>
					</table>
				</fieldset>
			</form> 
		</div>
	</div> 
	<div id="report_container" align="center"></div>
	<div id="report_container2"></div>  
</body>
     
 
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

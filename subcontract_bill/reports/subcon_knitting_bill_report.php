<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Knitting Bill Report
				
Functionality	:	
JS Functions	:
Created by		:	Helal Uddin
Creation date 	: 	08-09-2020
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
echo load_html_head_contents("Knitting Bill Report","../../", 1, 1, $unicode,0,0); 
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	
	
	
	function generate_wo_order_report(company_id,knitting_wo_id)
	{
		
		
		print_report( company_id+'**'+knitting_wo_id,"work_order_print", "requires/subcon_knitting_bill_report_controller");
	}
	
	

	function generate_report(rptType)
	{
		
		if( form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*Date Form*Date To')==false )
		{
			return;
		}
		
		var report_title=$( "div.form_caption" ).html();
		var dataString=get_submitted_data_string('cbo_company_id*cbo_party_source*cbo_party_name*location_id*floor_id*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
		if(rptType==1){
		var data="action=report_generate"+dataString;
		}else{
		var data="action=report_generate2"+dataString;
		}
		freeze_window(3);
		http.open("POST","requires/subcon_knitting_bill_report_controller.php",true);
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
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window();" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>'; 
			//var batch_type = document.getElementById('cbo_batch_type').value;
			
			//setFilterGrid("table_body",-1,tableFilters);
			
			show_msg('3');
			release_freezing();
		}
	} 
	
	function new_window()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		//document.getElementById('scroll_body').style.maxHeight="none";
		
		//$("#table_body tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		//document.getElementById('scroll_body').style.overflowY="scroll";
		//document.getElementById('scroll_body').style.maxHeight="400px";
		
		//$("#table_body tr:first").show();
	}
	
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission); ?>   		 
    <form name="knitting_bill_report_1" id="knitting_bill_report_1" autocomplete="off" > 
    <h3 style="width:1070px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:1070px;">
                <table class="rpt_table" width="1050" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 
                        	<th width="140">Source</th>	 	
                            <th width="140" class="must_entry_caption">Company</th>    
                            <th width="140">Location</th>      
                            <th width="140">Floor</th>                      
                            <th width="140">Party Name</th>
                            <th  width="170">Internal Ref.</th>
                            <th colspan="2" width="160" class="must_entry_caption">Bill Date Range</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('knitting_bill_report_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr class="general">
                    	<td><? echo create_drop_down( "cbo_party_source", 130, $knitting_source,"", 1, "-- Select Party --", $selected, "load_drop_down( 'requires/subcon_knitting_bill_report_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_party_name', 'party_td' );load_drop_down( 'requires/subcon_knitting_bill_report_controller',this.value +'_'+ document.getElementById('cbo_company_id').value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/subcon_knitting_bill_report_controller', document.getElementById('location_id').value+'_'+this.value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_floor', 'floor_td' ); ",0,"1,2,3","","","",5); ?></td>
                        <td>
                            <? 
                               echo create_drop_down( "cbo_company_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0  order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/subcon_knitting_bill_report_controller',this.value+'_'+document.getElementById('cbo_party_source').value, 'load_drop_down_party_name', 'party_td' );load_drop_down( 'requires/subcon_knitting_bill_report_controller', document.getElementById('cbo_party_source').value+'_'+this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/subcon_knitting_bill_report_controller', document.getElementById('location_id').value+'_'+document.getElementById('cbo_party_source').value+'_'+this.value, 'load_drop_down_floor', 'floor_td' );" );
                            ?>                            
                        </td>
                        <td width="140" id="location_td">
                        	<?php echo create_drop_down( "location_id", 130, $blank_array,"", 1, "-- Select Location --", $selected, "",0,"","","","",5); ?>
                        </td>
                        <td width="140" id="floor_td">
                        	<?php echo create_drop_down( "floor_id", 130, $blank_array,"", 1, "-- Select Floor --", $selected, "",0,"","","","",5); ?>
                        </td>
                        
                       
                        <td width="140" id="party_td">
                        	<? echo create_drop_down( "cbo_party_name", 130, $blank_array,"", 1, "--Select Party--", $selected, "",0,"","","","",6); ?>
                   		 </td>
                        
                        <td align="center" >
								<input type="text" style="width:130px" class="text_boxes" name="txt_internal_ref" id="txt_internal_ref"/>
						</td>
                        
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:60px" placeholder="From Date"/>
                        </td>
                        <td>
                             <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:60px" placeholder="To Date"/>
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1);" style="width:70px" class="formbutton" />
							<input type="button" name="search" id="search" value="Show 2" onClick="generate_report(2);" style="width:70px" class="formbutton" />
                        </td>
                    </tr>
                    <tr>
						<td colspan="9" align="center" width="100%"><? echo load_month_buttons(1); ?></td>                    
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

<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Production Incentive Payment Report
				
Functionality	:	
JS Functions	:
Created by		:	Ashraful
Creation date 	: 	15-07-2017
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
echo load_html_head_contents("Line Wise Productivity Analysis","../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report()
	{
		if( form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*Production Date*Production Date')==false )
		{
			return;
		}

		var report_title=$( "div.form_caption" ).html();
		
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_floor_id*txt_date_from*txt_date_to*cbo_produc_min',"../../")+'&report_title='+report_title;
	
		freeze_window(3);
		http.open("POST","requires/line_wise_sewing_production_report_controller.php",true);
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
		
	
	 
	
	 
	 
</script>

</head>
<body onLoad="set_hotkey();">

<form id="LineWiseProductivityAnalysis_1">
    <div style="width:100%;" align="center">    
    
        <? echo load_freeze_divs ("../../",'');  ?>
         
         <h3 style="width:900px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:900px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th width="160" class="must_entry_caption">Working Company</th>
                    
                    <th width="150">Location</th>
                    <th width="170">Floor</th>
                    <th width="100">Produce Min</th>
                    <th width="250" class="must_entry_caption">Production Date</th>
                    <th width="100" colspan="2"><input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('LineWiseProductivityAnalysis_1','report_container','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                       <td>
							<? 
							
                                echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/line_wise_sewing_production_report_controller', this.value, 'load_drop_down_location', 'location_td' );" );
                            ?>                            
                        </td>
                        <td id="location_td">
                            <? 
                                echo create_drop_down( "cbo_location_id", 140, $blank_array,"", 1, "-- Select Location --", "", "" );
                            ?>                            
                        </td>
                        <td id="floor_td">
                            <? 
                                echo create_drop_down( "cbo_floor_id", 170, $blank_array,"", 1, "-- Select Floor --", "", "" );
                            ?>                            
                        </td>
                        <td>
                            <? 
                                echo create_drop_down( "cbo_produc_min", 80, $yes_no,"", 0, "-- Select --", 2, "" );
                            ?>                            
                        </td>
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" value="<?php echo date('01-m-Y'); ?>" class="datepicker" style="width:70px" /> 
                            <strong>To</strong> 
                            <input type="text" name="txt_date_to" id="txt_date_to" value="<?php echo date('d-m-Y'); ?>"  class="datepicker" style="width:70px" />
                        </td>
                        <td>
                            <input type="button" name="search2" id="search2" value="Show" onClick="generate_report()" style="width:100px" class="formbutton" />
                        </td>
                       
                    </tr>
                    <tr>
                        <td colspan="7" align="center" height="30" valign="bottom"  id="month_button_cont">

                           <? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
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

</script>
</html>

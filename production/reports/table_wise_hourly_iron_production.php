<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Table Wise Hourly Iron Production
				
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam REZA
Creation date 	: 	03-09-2020
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
echo load_html_head_contents("Table Wise Hourly Iron Production","../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report(type)
	{
		if( form_validation('cbo_working_company_id*txt_pro_date','Company Name*Production Date')==false )
		{
			return;
		}
		else
		{
			var report_title=$( "div.form_caption" ).html();
			
			var data="action=report_generate"+get_submitted_data_string("cbo_company_id*cbo_working_company_id*txt_pro_date*cbo_location_id*cbo_floor_id*cbo_table_no*cbo_buyer_id*txt_style_no*txt_start_hour","../../")+'&report_title='+report_title;
		}
		freeze_window(3);
		http.open("POST","requires/table_wise_hourly_iron_production_controller.php",true);
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
	'<html><head><link rel="stylesheet" href="../../css/style_print.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="auto"; 
		document.getElementById('scroll_body').style.maxHeight="400px";
	}
		
	
	 
	function show_line_remarks(data,action)
	{
		var prod_date=document.getElementById('txt_pro_date').value;
		popup_width='550px'; 
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/table_wise_hourly_iron_production_controller.php?data='+data+'&prod_date='+prod_date+'&action='+action, 'Detail Veiw','width='+popup_width+', height=270px,center=1,resize=0,scrolling=0','../');
	}
	  		
 
	 
	 
</script>

</head>
<body onLoad="set_hotkey();">

<form id="LineWiseProductivityAnalysis_1">
    <div style="width:100%;" align="center">    
    
        <? echo load_freeze_divs ("../../",'');  ?>
         
         <h3 style="width:1150px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1140px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th>LC Company</th>
                    <th class="must_entry_caption">Working Company</th>
                    <th class="must_entry_caption">Production Date</th>
                    <th>Location</th>
                    <th>Floor</th>
                    <th>Table No</th>
                    <th>Buyer</th>
                    <th>Style</th>
                    <th>Start Hour</th>
                    <th><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_form('LineWiseProductivityAnalysis_1','report_container','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                       <td>
							<? 
                                echo create_drop_down( "cbo_company_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 0, "-- Select Company --", 0, "" );
                            ?>                            
                        </td>
                       <td>
							<? 
                                echo create_drop_down( "cbo_working_company_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/table_wise_hourly_iron_production_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/table_wise_hourly_iron_production_controller', this.value, 'load_drop_down_location', 'location_td' );" );
                            ?>                            
                        </td>
                         <td>
                            <input type="text" name="txt_pro_date" id="txt_pro_date" class="datepicker" style="width:90px;" readonly/>
                        </td>
                        <td id="location_td">
                            <? 
                                echo create_drop_down( "cbo_location_id", 130, $blank_array,"", 1, "-- All --", "", "" );
                            ?>                            
                        </td>
                        <td id="floor_td">
                            <? 
                                echo create_drop_down( "cbo_floor_id", 130, $blank_array,"", 1, "-- All --", "", "" );
                            ?>                            
                        </td>
                         <td id="table_td">
                            <? 
                                echo create_drop_down( "cbo_table_no", 100,array(),"", 1, "-- All --", "", "" );
                            ?>                            
                        </td>
                        <td id="buyer_td"> 
                            <?
                               echo create_drop_down( "cbo_buyer_id", 130, $blank_array,"", 1, "-- All --", 0, "",0 );
                            ?>
                        </td>
                        <td>
                            <input type="text" id="txt_style_no" name="txt_style_no" class="text_boxes" style="width:90px;" value="" />
                        </td>
                        <td>
                            <input type="text" id="txt_start_hour" name="txt_start_hour" class="text_boxes" style="width:30px;" value="9:00" />
                        </td>
                        <td>
                            <!--Not Use --hidden button> --> 
                            <input type="button" id="button_1" value="Show" onClick="generate_report(1)" style="width:80px" class="formbutton" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
    	</div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
 </form>   
</body>
<script>
	set_multiselect('cbo_company_id','0','0','','0');
</script> 
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>

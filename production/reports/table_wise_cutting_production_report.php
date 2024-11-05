<?
/*-------------------------------------------- Comments
Purpose			: 	This form will Table Wise Cutting Production Report
				
Functionality	:	
JS Functions	:
Created by		:	Md Mamun Ahmed Sagor
Creation date 	: 	22-03-2021
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
echo load_html_head_contents("Table Wise Cutting Production Report","../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
    
    var tableFilters = 
    {
        
    }

	function generate_report()
	{
		if( form_validation('cbo_company_id*txt_date_from','Company Name*Production Date')==false )
		{
			return;
		}

		var report_title=$( "div.form_caption" ).html();
		
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_location_id*cbo_floor_id*cbo_table*txt_date_from',"../../")+'&report_title='+report_title;
	
		freeze_window(3);
		http.open("POST","requires/table_wise_cutting_production_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(trim(reponse[0]));  
			release_freezing();
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("html_search",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
	} 

	

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		var htmlSearchValue=$('table#table_body tr:first').clone() ;
		$('table#table_body tr:first').remove();
 		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		$(htmlSearchValue).prependTo("table#table_body tbody");
		document.getElementById('scroll_body').style.overflowY="auto"; 
		document.getElementById('scroll_body').style.maxHeight="350px";
	}
	function openmypage_all_prod_qty(datas,action)
	{
  		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/table_wise_cutting_production_report_controller.php?datas='+datas+'&action='+action, 'Prod. Qty View', 'width=720px,height=450px,center=1,resize=0,scrolling=0','../../');
	}
	function openmypage_cut_no_qty(datas,action)
	{
  		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/table_wise_cutting_production_report_controller.php?datas='+datas+'&action='+action, 'System Cut No', 'width=610px,height=330px,center=1,resize=0,scrolling=0','../../');
	}

</script>

</head>
<body onLoad="set_hotkey();">

<form id="LineWiseProductivityAnalysis_1">
    <div style="width:100%;" align="center">    
    
        <? echo load_freeze_divs ("../../../",'');  ?>
         
         <h3 style="width:900px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:900px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th width="150" class="must_entry_caption">Company</th>
                    <th width="130">Buyer</th>
                    <th width="150">Location</th>
                    <th width="150">Floor</th>
                     <th width="150">Table</th>
                    <th width="250" class="must_entry_caption">Production Date</th>
                    <th width="100" colspan="2"><input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('LineWiseProductivityAnalysis_1','report_container*report_container2','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                       <td>
							<? 
							
                                echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/table_wise_cutting_production_report_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/table_wise_cutting_production_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>                            
                        </td>

                         <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_id", 130, $blank_array,"", 1, "-- Select Buyer --", "", "" );
                            ?>                            
                        </td>

                        <td id="location_td">
                            <? 
                                echo create_drop_down( "cbo_location_id", 140, $blank_array,"", 1, "-- Select Location --", "", "" );
                            ?>                            
                        </td>
                        <td id="floor_td">
                            <? 
                                echo create_drop_down( "cbo_floor_id", 150, $blank_array,"", 1, "-- Select Buyer --", "", "" );
                            ?>                            
                        </td>

                        <td id="table_td">
                            <? 
                                echo create_drop_down( "cbo_table", 150, $blank_array,"", 1, "-- Select Table --", "", "" );
                            ?>                            
                        </td>

                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" value="<?php echo date('d-m-Y'); ?>" class="datepicker" style="width:70px" />                            
                        </td>
                        <td>
                            <input type="button" name="search2" id="search2" value="Show" onClick="generate_report()" style="width:100px" class="formbutton" />
                        </td>
                       
                    </tr>
                   
                </tbody>
            </table>
        </fieldset>
    	</div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" >
    </div>
 </form>   
</body>

<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
$("#cbo_location_id").val(0);

</script>
</html>

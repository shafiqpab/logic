<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Page Will Create Buyer Wise Follow Up Status Report
Functionality	:	
JS Functions	:
Created by		:	Md. Shafiqul Islam Shafiq
Creation date 	: 	17-FEB-2019
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();

extract($_REQUEST);



if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');

$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Line Wise Planning Report","../../", 1, 1, $unicode,1,1);
?>	
<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	
	var tableFilters = 
	 {
	 }
	  
	
	function fn_report_generated(type)
	{
		if(form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false)
		{
			return;
		}
		else
		{	
			var data="action=report_generate&type="+type+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_year*txt_date_from*txt_date_to',"../../");
			freeze_window(3);
			http.open("POST","requires/buyer_wise_follow_up_status_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
		
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			 
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]); 
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>'; 
			
			// setFilterGrid("html_search",-1,tableFilters);
			
	 		show_msg('3');
			release_freezing();
		}
	}
	
	function new_window()
	{
		document.getElementById('html_search').style.overflow="auto";
		document.getElementById('html_search').style.maxHeight="none";
		 
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('html_search').style.overflowY="scroll";
		document.getElementById('html_search').style.maxHeight="380px";
		 
	}
	
</script>
</head>
 
<body onLoad="set_hotkey();">
		 
<form id="cost_breakdown_rpt">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:800px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:800px;">
                <table class="rpt_table" width="790" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                	<thead>
                    	<tr>                   
                            <th class="must_entry_caption">Company Name</th>
                            <!-- <th class="">Location</th>
                            <th class="">Floor</th> -->
                             <th>Buyer Name</th>
                             <th>Year</th>
                             <th class="must_entry_caption" id="td_date_caption">Pub. Ship Date</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" /></th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr class="general">
                        <td> 
                           <?
								echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/buyer_wise_follow_up_status_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>

                       <!--  <td id="location_td"> 
                           <? //echo create_drop_down( "cbo_location_id", 150, "select id,location_name from lib_location where status_active=1 ","id,location_name", 1, "-- Select Location --", $selected, "" ); ?>
                        </td>
                        <td id="floor_td"> 
                           <?	//echo create_drop_down( "cbo_floor_id", 150, "select id,floor_name from lib_prod_floor  where status_active=1  ","id,floor_name", 1, "-- Select Floor --", $selected, "" ); ?>
                        </td> -->
                       
                        
                          <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_id", 150, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                            ?>
                        </td>
                         <td>
                            <?
                            	$curent_year = date('Y');
                                echo create_drop_down( "cbo_year", 80, $year,"", 1, "-- All --", $blank_array, "",0,"" );
                            ?>
                        </td>
                         
                        
                        <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" >&nbsp; To
                        <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date" ></td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated(1)" />
                        </td>
                    </tr>
                    </tbody>
                </table>
                <table>
                    <tr>
                        <td>
                            <? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                </table> 
            </fieldset>
        </div>
    </div>
    <div id="report_container" align="center" style="padding: 10px 0"></div>
    <div id="report_container2"></div>
   
 </form>    
</body>
<script>
set_multiselect('cbo_company_id','0','0','','0');
// set_multiselect('cbo_location_id','0','0','','0');
// set_multiselect('cbo_floor_id','0','0','','0');
$("#multiselect_dropdown_table_headercbo_company_id a").click(function(){
		load_buyer_buyer();
 	});
	function load_buyer_buyer()
	{
		var company=$("#cbo_company_id").val();
 		load_drop_down( 'requires/buyer_wise_follow_up_status_controller',company, 'load_drop_down_buyer', 'buyer_td' );
 	// 	load_drop_down( 'requires/buyer_wise_follow_up_status_controller',company, 'load_drop_down_location', 'location_td' );
 	// 	load_drop_down( 'requires/buyer_wise_follow_up_status_controller',company, 'load_drop_down_floor', 'floor_td' );
 	// 	set_multiselect('cbo_location_id','0','0','','0');
		// set_multiselect('cbo_floor_id','0','0','','0');
		
	}
</script>

<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

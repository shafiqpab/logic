<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Wash Forcast Report
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam Reza
Creation date 	: 	01-12-2020
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:	
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:../../logout.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Wash Forcast Report", "../../", 1, 1,$unicode,'1','');

?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	
	function fn_report_generated(type)
	{
		if(form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
		else
		{	
			if(type==1){var action="report_generate";}
			else if(type==2){var action="report_generate_monthly_summary";}
			else if(type==3){var action="report_generate_buyer_summary";}
			
			var data="action="+action+"&type="+type+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_brand_id*cbo_season_id*cbo_order_type*cbo_date_type*txt_date_from*txt_date_to*cbo_season_year',"../../");
			
			freeze_window(3);
			http.open("POST","requires/wash_forcast_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}		
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			var tot_rows=reponse[2];
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+tot_rows+',1)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			//setFilterGrid("table_body",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
	}	 

	function new_window(html_filter_print,type)
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		if(html_filter_print*1>1) $("#table_body tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="600px";			
		if(html_filter_print*1>1) $("#table_body tr:first").show();
		
	}
	
	function openPopup(action,dataStr)
	{
		var title = 'Details View';	
		var page_link = 'requires/wash_forcast_report_controller.php?&action='+action+'&data='+dataStr;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=370px,center=1,resize=1,scrolling=0','../');
		
		emailwindow.onclose=function()
		{
		}
	}


	
</script>

</head>

<body onLoad="set_hotkey();">
<form id="wash_forcast_rpt">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",''); ?>
        <h3 align="left" id="accordion_h1" style="width:1150px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" > 
			<fieldset style="width:1150px;">
                <table class="rpt_table" width="1150" cellpadding="0" cellspacing="0" rules="all" border="1">
                   <thead>                    
                        <th class="must_entry_caption">Company Name</th>
                        <th>Buyer Name</th>
                        <th>Brand</th>
                        <th>Season</th>
                        <th>Season Year</th>
                        <th>Order Type</th>
                        <th>Date Type</th>
                        <th id="dateRange">Date Range</th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:80px" value="Reset" /></th>
                     </thead>
                    <tbody>
                    <tr class="general">
                        <td> 
                            <?
                               echo create_drop_down( "cbo_company_id", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/wash_forcast_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_id", 130, $blank_array,"", 1, "-- All --", $selected, "",0,"" );
                            ?>
                        </td>
                        <td id="brand_td">
                            <? 
                                echo create_drop_down( "cbo_brand_id", 70, $blank_array,"", 1, "-- All --", $selected, "",0,"" );
                            ?>
                        </td>
                        <td id="season_td">
                            <? 
                                echo create_drop_down( "cbo_season_id", 70, $blank_array,"", 1, "-- All --", $selected, "",0,"" );
                            ?>
                        </td>
                        <td><? echo create_drop_down( "cbo_season_year", 60, create_year_array(),"", 1,"-Year-", 1, "",0,"" );?> 
                        <td>
                            <? 
                                echo create_drop_down( "cbo_order_type", 130, $order_status,"", 1, "-- All --", $selected, "",0,"" );
                            ?>
                        </td>
                         <td id="team_td">
							 <? 
							 	$date_type_arr = array(1=>'Ship Date',2=>'Pub.Ship Date',3=>'Country Ship Date');
                                echo create_drop_down( "cbo_date_type", 130, $date_type_arr,"", 0, "--Select-- ", $selected, "" );
                             ?>	
                         </td>
                        <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" >&nbsp; To
                        <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px"  placeholder="To Date" ></td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:80px" value="Details" onClick="fn_report_generated(1)" />
                        </td>
                    </tr>
                    </tbody>
                </table>
                <table cellpadding="1" cellspacing="2">
                    <tr>
                        <td>
                            <? echo load_month_buttons(1); ?>
                              <input type="button" id="show_button" class="formbutton" style="width:100px" value="Month Wise" onClick="fn_report_generated(2)" />

                            <input type="button" id="show_button" class="formbutton" style="width:100px" value="Buyer Wise" onClick="fn_report_generated(3)" />

                        </td>
                    </tr>
                </table> 
        	</fieldset>
        </div>
    </div>
    <div id="report_container" align="center" style="padding: 10px;"></div>
    <div id="report_container2"></div>
 </form>     
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
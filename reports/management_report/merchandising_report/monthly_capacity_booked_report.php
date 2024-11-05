<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Monthly Capacity Vs Booked Report			
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	15-11-2016
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
//echo load_html_head_contents("Monthly Capacity Vs Booked Report", "../../../", "", $popup, 1);
echo load_html_head_contents("Monthly Capacity Vs Booked Report","../../../", 1, 1, $unicode,1,'');
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	 
	function fn_report_generated()
	{
		if(form_validation('cbo_company_id*cbo_year_start*cbo_month_start*cbo_year_end*cbo_month_end','Company Name*Start Year*Start Month*End Year*End Month')==false)
		{
			return;
		}
		else
		{	
		var cbo_company_id=document.getElementById('cbo_company_id').value;
		var cbo_location_id=document.getElementById('cbo_location_id').value;
		var cbo_year_start=document.getElementById('cbo_year_start').value;
		var cbo_month_start=document.getElementById('cbo_month_start').value;
		var cbo_year_end=document.getElementById('cbo_year_end').value;
		var cbo_month_end=document.getElementById('cbo_month_end').value;
		var cbo_type=document.getElementById('cbo_type').value;
		
		
		
		
		var data=cbo_company_id+'_'+cbo_location_id+'_'+cbo_year_start+'_'+cbo_month_start+'_'+cbo_year_end+'_'+cbo_month_end+'_'+cbo_type;
			
			freeze_window(3);
			var v1='';//return_global_ajax_value(data, 'get_pre_cost_data', '','requires/monthly_capacity_booked_report_controller_v1');
			var v2=return_global_ajax_value(data, 'get_pre_cost_data', '','requires/monthly_capacity_booked_report_controller_v2');
			
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_year_start*cbo_month_start*cbo_year_end*cbo_month_end*cbo_type',"../../../")+'&report_title='+report_title+'&pre_cont_v1='+trim(v1)+'&pre_cont_v2='+trim(v2);
			
			http.open("POST","requires/monthly_capacity_booked_report_controller.php",true);
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
			var tot_rows=reponse[2];
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML=report_convert_button('../../../'); 
			//append_report_checkbox('table_header_1',1);
			
	 		show_msg('3');
			release_freezing();
		}
	}
	
	function fnc_details_popup(month,company_id,location_id,type,action)
	{
		var popup_width=0;
		if(type==0)
		{
			popup_width='400px';
		}
		else if(type==1)
		{
			popup_width='1150px';
		}
		else if(type==2)
		{
			popup_width='1150px';
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/monthly_capacity_booked_report_controller.php?month='+month+'&company_id='+company_id+'&location_id='+location_id+'&type='+type+'&action='+action+'&popup_width='+popup_width, 'Details Veiw', 'width='+popup_width+', height=400px,center=1,resize=0,scrolling=0','../../');
	}
	
</script>
</head>
<body onLoad="set_hotkey()">
   <div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../../");  ?>
        <form id="monthlyCapacityBooked_1" name="monthlyCapacityBooked_1">
            <h3 align="left" id="accordion_h1" class="accordion_h" style="width:850px;" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel" style="width:850px" align="center" >
                <fieldset>  
                    <table cellpadding="0" cellspacing="2" width="850" class="rpt_table" border="1" rules="all">
                        <thead>  
                            <tr>
                                <th class="must_entry_caption" width="150">Company</th>
                                <th width="150">Location</th>
                                <th width="80">Type</th>
                                <th class="must_entry_caption" width="80">Start Year</th>
                                <th class="must_entry_caption" width="100">Start Month</th>
                                <th class="must_entry_caption" width="80">End Year</th>
                                <th class="must_entry_caption" width="100">End Month</th>
                                <th><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:80px" onClick="reset_form('monthlyCapacityBooked_1','report_container*report_container2','','','');" /></th>
                            </tr>
                         </thead>
                         <tbody>
                            <tr class="general">
                                <td><? echo create_drop_down( "cbo_company_id", 150, "select id, company_name from lib_company where status_active=1 and is_deleted=0 order by company_name","id,company_name", 1, "--Select Company--", $selected,"load_drop_down( 'requires/monthly_capacity_booked_report_controller', this.value, 'load_drop_down_location', 'location_td' ); " ); //get_php_form_data( this.value, 'eval_multi_select', 'requires/monthly_capacity_booked_report_controller' ); ?></td>
                                <td id="location_td">
									<? echo create_drop_down( "cbo_location_id", 150, $blank_array,"", 1, "-- Select --", $selected, "",1,"" ); ?>
                                </td>
                                <td><? echo create_drop_down( "cbo_type", 80,array(1=>"Ship Date",2=>"TNA Date"),"", 1, "-Type-", 1,"" ); ?></td>
                                <td><? echo create_drop_down( "cbo_year_start", 80,$year,"", 1, "-Start Year-", date('Y'),"" ); ?></td>
                                <td><? echo create_drop_down( "cbo_month_start", 100,$months,"", 1, "-Start Month-", "","" ); ?></td>
                                <td><? echo create_drop_down( "cbo_year_end", 100,$year,"", 1, "-End Year-", date('Y'),"" ); ?></td>
                                <td><? echo create_drop_down( "cbo_month_end", 100,$months,"", 1, "-End Month-", "","" ); ?></td>
                                <td><input type="button" name="search" id="search" value="Show" onClick="fn_report_generated()" style="width:80px" class="formbutton" /></td>
                            </tr>
                         </tbody>
                    </table>
                </fieldset>
            </div>
        </form>    
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
</body>
<script>//set_multiselect('cbo_location','0','0','','');</script> 
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
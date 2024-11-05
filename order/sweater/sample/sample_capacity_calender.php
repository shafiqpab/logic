<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sample Capacity Calender", "../../../", 1, 1,$unicode,'','');
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "/../../../logout.php";  
	var permission = '<? echo $permission; ?>';
			
	function fnc_sample_capacity_calender()
	{
		if (form_validation('cbo_company_name*txt_date_from*txt_date_to','Comapny Name*From Date*To Date')==false) 
		{
		    return; 
		}

    	var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_location_name*cbo_sample_team*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/sample_capacity_calender_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_sample_capacity_calender_response;
 	
	}

	function fnc_sample_capacity_calender_response()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("**"); 
			$('#report_container2').html(reponse[0]);
			setFilterGrid('list_views',-1);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>'; 
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{	
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	}

	function change_color(v_id,e_color)
	{
		if (document.getElementById(v_id).bgColor=="#33CC00")
		{
			document.getElementById(v_id).bgColor=e_color;
		}
		else
		{
			document.getElementById(v_id).bgColor="#33CC00";
		}
	}	

</script>
</head>
<body onLoad="set_hotkey();">
<form id="sampleCapacityCalender_1">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../../",''); ?>
        <h3 style="width:800px; margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel', '')"> -Search Panel-</h3>
        <div id="content_search_panel">
        <fieldset style="width:800px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>
                    <th width="200" class="must_entry_caption">Company</th>
                    <th width="150">Location</th>
                    <th width="150">Team</th>
                    <th width="200" colspan="2" class="must_entry_caption">Date Range</th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset"/></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td>
                        	<?
								echo create_drop_down( "cbo_company_name", 200, "select comp.id, comp.company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down('requires/sample_capacity_calender_controller', this.value, 'load_drop_down_location', 'location_td' );" );
							?>
                        </td>
                        <td id="location_td">
							<?
								echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where status_active =1 and is_deleted=0 order by location_name",'id,location_name', 1, '-- Select Location --', 0, "" );
							?>
						</td>
						<td>
							<?
								echo create_drop_down( "cbo_sample_team", 150, "select id,team_name from lib_sample_production_team where product_category=6 and is_deleted=0","id,team_name", 1, "-- Select Team --", $selected, "" );
							?>
						</td>

                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" ></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" ></td>

                        <td><input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fnc_sample_capacity_calender();" /></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" align="center">
							<? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                </tfoot>
           </table>
           <br/>
        </fieldset>
    	</div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="center"></div>
</form>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Team Wise Order Status Report.
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam Reza
Creation date 	: 	02-11-2020
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start();
extract($_REQUEST);

if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Team Wise Order Status","../../../", 1, 1, $unicode,1,1);
?>	
<script>
 	if( $('#index_page', window.parent.document).val()!=1){window.location.href = "../../../logout.php"; } 
	
	function fn_generate_report(type)
	{
		if(form_validation('cbo_month*cbo_year','Month*Year')==false)
		{
			return;
		}
		else
		{	
			var data="action=report_generate"+get_submitted_data_string('cbo_buyer_name*cbo_year*cbo_month*cbo_company_id',"../../../");
			freeze_window(3);
			http.open("POST","requires/monthly_buyer_wise_planning_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_generate_report_reponse;
		}
	}
		
	function fn_generate_report_reponse()
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
	
	
	function loadBuyer()
	{
		load_drop_down( 'requires/monthly_buyer_wise_planning_report_controller', document.getElementById('cbo_company_id').value, 'load_drop_down_buyer', 'buyer_td' );
		set_multiselect('cbo_buyer_name','0','0','','0');
	}
	
	
	
	function new_window()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		//document.getElementById('scroll_body').style.maxHeight="none";
		//$('#scroll_body tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		//document.getElementById('scroll_body').style.overflowY="scroll";
		//document.getElementById('scroll_body').style.maxHeight="380px";
		//$('#scroll_body tr:first').show();
	}
	
	
    
	
</script>
</head>
 
<body onLoad="set_hotkey();">
		 
<form id="cost_breakdown_rpt">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../../"); ?>
         <h3 style="width:550px;" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel"> 
            <fieldset style="width:550px;">
                <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                	<thead>
                    	<tr>                   
                            <th>Company</th>
                            <th>Buyer</th>
                            <th class="must_entry_caption"> Month</th>
                            <th class="must_entry_caption"> Year</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:80px" value="Reset" /></th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr>
                        <td align="center"> 
                           <?
								echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 0, "-- Select Company --", $selected, "" );
                            ?>
                        </td>
                       
                        <td align="center" id="buyer_td">
							<? echo create_drop_down( "cbo_buyer_name", 150, array(),"", 0, "-- Select Team --", $selected, "",0);
	                		?>
                        </td>
                        <td align="center">
							<? 
								echo create_drop_down( "cbo_month", 60, $months_short,"", 0, "-- Select --", date('m'), "",0,"" );
                            ?>
                        </td>
                        <td align="center">
							<? 
								echo create_drop_down( "cbo_year", 60, $year,"", 0, "-- Select --", date('Y'), "",0,"" );
                            ?>
                        </td>
                         <td align="center">
                           <input type="button" id="show_button" class="formbutton" style="width:80px" value="Show" onClick="fn_generate_report()" />
                        </td>
                    </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
   
 </form>    
</body>
<script>
	set_multiselect('cbo_company_id','0','0','','0','loadBuyer()');
	set_multiselect('cbo_buyer_name','0','0','','0');
</script>


<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

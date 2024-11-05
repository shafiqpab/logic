<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Monthly Export Import Status Report.
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	08-02-2018
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
echo load_html_head_contents("Monthly Export Import Report", "../../", 1, 1,$unicode,1,1);
 

?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
 
	var permission = '<? echo $permission; ?>';
	
	function fn_report_generated()
	{
		if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date From*Date To')==false)
		{
		return;
		}
		var cbo_company_name=$('#cbo_company_name').val();
		var delimiter = ',';
			var cbo_company_id = cbo_company_name.split(delimiter);

		if (cbo_company_id.length === 1) {
		
		var report_title=$( "div.form_caption" ).html();	
		var data="action=report_generate"+get_submitted_data_string("cbo_company_name*txt_date_from*txt_date_to","../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/monthly_export_import_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
		}else{
			alert("Please Select Single Company")
		}
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");
			//alert(http.responseText);return;
			$('#report_container2').html(response[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
				//setFilterGrid("scroll_body",tableFilters,-1);,tableFilters
				setFilterGrid("scroll_body",-1);
			
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
/*		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
*/		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
/*		document.getElementById('scroll_body').style.overflowY="auto";
		document.getElementById('scroll_body').style.maxHeight="auto";
*/	}

	function fn_report_generated_two()
	{
		if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date From*Date To')==false)
		{
		return;
		}
		
		var report_title=$( "div.form_caption" ).html();	
		var data="action=report_generate_2"+get_submitted_data_string("cbo_company_name*txt_date_from*txt_date_to","../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/monthly_export_import_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse_two;
	}

	function fn_report_generated_reponse_two()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");
			//alert(http.responseText);return;
			$('#report_container2').html(response[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window_two()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			show_msg('3');
			release_freezing();
		}
	}

	function new_window_two()
	{
/*		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
*/		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
/*		document.getElementById('scroll_body').style.overflowY="auto";
		document.getElementById('scroll_body').style.maxHeight="auto";
*/	}



	function open_details(ref_id,action,title,page_width,popup_type,date_form,date_to,bank_id)
	{
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/monthly_export_import_report_controller.php?ref_id='+ref_id+'&action='+action+'&popup_type='+popup_type+'&date_form='+date_form+'&date_to='+date_to+'&bank_id='+bank_id, title, 'width='+page_width+'px,height=390px,center=1,resize=0,scrolling=0','../');
	}




</script>
</head>
 
<body onLoad="set_hotkey();">
 <div style="width:1000px" align="left">
<form id="monthly_ex_factory" name="monthly_ex_factory">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:800px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:800px;">
                <table class="rpt_table" width="700" cellpadding="1" cellspacing="2" align="center">
                	<thead>
                    	<tr>                   
                            <th width="200" class="must_entry_caption">Company Name</th>
                            <th width="300" class="must_entry_caption">Date Range</th>
                            <th width="200" colspan="2"><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:100px" onClick="reset_form('','report_container*report_container2','','','')" /></th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr class="general">
                        <td> 
                            <?
                                echo create_drop_down( "cbo_company_name", 200, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected );
                            ?>
                        </td>
                        <td>
							<?
								/*$year_current=date("Y");
								$month_current=date("m");
                            	echo create_drop_down( "cbo_year_from", 120, $year,"", 1, "-Select-",$year_current);
								echo " ";
								echo create_drop_down( "cbo_month_from", 120, $months,"", 1, "-Select-",$month_current);*/
							?>
                                <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px;"/>                    							
                                To
                                <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px;"/>                        
                        </td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated()" />                           
                        </td>
						<td><input type="button" id="show_button_2" class="formbutton" style="width:100px; display:none;" value="Show 2" onClick="fn_report_generated_two()" /></td>
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
     </form>

    <!--<div id="report_container" align="center"></div>-->
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
 </div>    
</body>
<script>
	set_multiselect('cbo_company_name','0','0','','0');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

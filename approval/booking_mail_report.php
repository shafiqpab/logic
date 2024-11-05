<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Booking Approval Report.
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	10-01-2015
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();

if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
require_once('../mailer/class.phpmailer.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

echo load_html_head_contents("Booking Approva Report","../", 1, 1, $unicode,1,1);
 
?>

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";  
	var permission = '<? echo $permission; ?>';
	
	function fn_report_generated()
	{
		freeze_window(3);
		if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date Form* Date To')==false)
		{
			release_freezing();
			return;
		}
		
		
		var report_title=$( "div.form_caption" ).html();	
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_search_type*txt_date_from*txt_date_to',"../")+'&report_title='+report_title;
		//alert(data);return;
		http.open("POST","requires/booking_mail_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
		
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("####");
			//alert(reponse[1]);return;
			$('#report_container2').html(reponse[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../');
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
	 		show_msg('3');
			release_freezing();
		}
	}
	
	function new_window()
	{
		/*var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();*/
		
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		document.getElementById('scroll_body2').style.overflow="auto";
		document.getElementById('scroll_body2').style.maxHeight="none";
		
		document.getElementById('scroll_body3').style.overflow="auto";
		document.getElementById('scroll_body3').style.maxHeight="none";
		
		
		
		$('#table_body tr:first').hide();
		$('#table_body2 tr:first').hide();
		$('#table_body3 tr:first').hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		$('#table_body tr:first').show();
		$('#table_body2 tr:first').show();
		$('#table_body3 tr:first').show();
		
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="300px";
		
		document.getElementById('scroll_body2').style.overflowY="scroll";
		document.getElementById('scroll_body2').style.maxHeight="300px";
		
		document.getElementById('scroll_body3').style.overflowY="scroll";
		document.getElementById('scroll_body3').style.maxHeight="300px";
	}
</script>

</head>
 
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
<form id="frm_booking_mail" name="frm_booking_mail">
        <? echo load_freeze_divs ("../"); ?>
         <h3 align="left" id="accordion_h1" style="width:800px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:800px;">
                <table class="rpt_table" width="800" cellpadding="0" cellspacing="0" align="center" rules="all">
                    <thead>
                        <th class="must_entry_caption" width="170">Company Name</th>
                        <th width="170">Buyer Name</th>
                        <th width="170">Type</th>
                        <th width="200" class="must_entry_caption">Date Range</th>
                        <th><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:90px" onClick="reset_form('frm_booking_mail','report_container*report_container2','','','')" /></th>
                    </thead>
                    <tbody>
                    		<tr class="general">                   
                                <td align="center"> 
								<?
                                    echo create_drop_down( "cbo_company_name", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/booking_mail_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                                ?>
                                </td>
                                <td id="buyer_td" align="center">
								<? 
									echo create_drop_down( "cbo_buyer_name", 160, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" ); 
								?>
                                </td>
                                <td align="center">
								<?
									$search_style_arr=array(1=>"Revised Booking",2=>"Unapproved Booking",3=>"Approved Booking");
									echo create_drop_down( "cbo_search_type", 160, $search_style_arr,"", 1,"--Select--", 0, "",0,"" ); 
                                ?>
                                </td>
                                <td align="center">
                                    <input type="text" id="txt_date_from" name="txt_date_from" class="datepicker" style="width:70px" readonly> To 
                                    <input type="text" id="txt_date_to" name="txt_date_to" class="datepicker" style="width:70px" readonly>
                                </td>
                                <td align="center">
                                    <input type="button" id="show_button" class="formbutton" style="width:90px" value="Show" onClick="fn_report_generated()" />
                                </td>
                            </tr>
                    </tbody>
                    
                    <tr>
                    	<td colspan="5" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
                    </tr>
                </table>
            </fieldset>
        </div>
     </form>
 </div> 
 <div id="report_container" align="center"></div><br />
 <div id="report_container2" align="center"></div>   
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>

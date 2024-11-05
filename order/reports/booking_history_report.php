<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Fabric Booking History Report.
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	28-04-2019
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
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Fabric Booking History Report", "../../", 1, 1,$unicode,1,1);
?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';	

	function openmypage_booking()
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/booking_history_report_controller.php?action=booking_popup', 'Booking No Search', 'width=1190px,height=450px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theemailid=this.contentDoc.getElementById("selected_booking");
			var response=theemailid.value;
			if ( response!="" )
			{
				freeze_window(5);
				$("#txt_booking_no").val(response);
				release_freezing();
			}
		}
	}
	
	function fn_report_generated(operation)
	{
		if(form_validation("txt_booking_no","Full Booking No.")==false){
			return;
		}
		
			var report_title=$( "div.form_caption" ).html();
			if(operation==0){
			var data="action=report_generate"+get_submitted_data_string('txt_booking_no',"../../")+'&report_title='+report_title;
			}
			else if(operation==2){
				var data="action=report_generate2"+get_submitted_data_string('txt_booking_no',"../../")+'&report_title='+report_title;
			}
			freeze_window(3);
			http.open("POST","requires/booking_history_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("####");
			var tot_rows=reponse[2];
			$('#report_container2').html(reponse[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			//$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;';
			//setc()
	 		show_msg('3');
			release_freezing();
		}
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
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="330px";
	}
	
</script>
</head>
<body onLoad="set_hotkey();">
<form id="bookingreport_1">
<div style="width:100%; margin:1px auto;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?>
    <h3 style="width:330px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
        <div id="content_search_panel" style="width:330px" > 		 
            <fieldset style="width:330px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>                    
                    <th width="170" class="must_entry_caption">Full Booking No.</th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:150px" value="Reset" onClick="reset_form('bookingreport_1','report_container*report_container2','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td>
                            <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:160px" onDblClick="openmypage_booking();" placeholder="Browse/Write Full Booking No." />
                        </td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(0)" />
						<input type="button" id="show_button" class="formbutton" style="width:70px" value="Show 2" onClick="fn_report_generated(2)" /></td>
                    </tr>
                </tbody>
            </table> 
        	</fieldset>
        </div>
        <div id="report_container" align="center"></div>
        <div id="report_container2" align="left"></div>
    </div>
</form> 
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

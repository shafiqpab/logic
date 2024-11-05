<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Line Allocation Report.
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	22-05-2016
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
echo load_html_head_contents("Line Allocation Report", "../../", 1, 1,$unicode,'','');

?>	
<script src="../../Chart.js-master/Chart.js"></script>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	
	function fnc_line_popup()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		 var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location').value+"_"+document.getElementById('cbo_floor').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/line_allocation_report_controller.php?action=line_no_popup&data='+data,'Line Name Popup', 'width=470px,height=420px,center=1,resize=0','../')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("hid_line_id");
			var theemailv=this.contentDoc.getElementById("hid_line_name");
			if (theemail.value!="")
			{
				freeze_window(5);
				document.getElementById("txt_line_id").value=theemail.value;
				document.getElementById("txt_line_no").value=theemailv.value;
				release_freezing();
			}
		}
	}
		
	function fn_report_generated(type)
	{
		if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false )
		{
			return;
		}
		else
		{
			var report_title=$( "div.form_caption" ).html();
			var from_date = $('#txt_date_from').val();
			var to_date = $('#txt_date_to').val();
			
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_location*cbo_floor*cbo_sew_group*txt_line_id*cbo_year*txt_job_no*txt_po_id*txt_date_from*txt_date_to',"../../")+"&report_title="+report_title+"&type="+type;
			freeze_window(3);
			http.open("POST","requires/line_allocation_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("**"); 
			$('#report_container2').html(reponse[0]);
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
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="none";
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
<form id="sewingQcReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:900px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:900px" >    
         <fieldset style="width:900px;">
            <table class="rpt_table" width="900" cellpadding="0" cellspacing="0" align="center" rules="all" border="1">
				<caption style="color: red;padding-bottom:3px;font-weight:bold;">This report data will show when you attach style in actual resource allocatio (Style Popup)</caption>
               <thead>                    
                    <tr>
                        <th class="must_entry_caption">Company Name</th>
                        <th>Location</th>
                        <th>Floor</th>
                        <th>Sewing Group</th>
                        <th>Line No</th>
                        <th>Job Year</th>
                        <th>Job No</th>
                        <th>Order No</th>
                        <th class="must_entry_caption" colspan="2">Allocation Start Date</th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                    </tr>    
                 </thead>
                <tbody>
                <tr class="general">
                    <td width="130"> 
                        <?
                            echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/line_allocation_report_controller', this.value, 'load_drop_down_location', 'location_td'); load_drop_down( 'requires/line_allocation_report_controller', this.value, 'load_drop_down_sewing_group', 'sgroup_td' );" );
                        ?>
                    </td>
                    <td width="90" id="location_td">
                    	<? 
                            echo create_drop_down( "cbo_location", 90, $blank_array,"", 1, "-- Select --", $selected, "",1,"" );
                        ?>
                    </td>
                    <td width="90" id="floor_td">
                    	<? 
                            echo create_drop_down( "cbo_floor", 90, $blank_array,"", 1, "-- Select --", $selected, "",1,"" );
                        ?>
                    </td>
                    <td width="80" id="sgroup_td">
                    	<? 
                            echo create_drop_down( "cbo_sew_group", 80, $blank_array,"", 1, "-- Select --", $selected, "",1,"" );
                        ?>
                    </td>
                    <td width="80">
                       <input type="text" name="txt_line_no" id="txt_line_no" class="text_boxes" placeholder="Browse" onClick="fnc_line_popup();"  style="width:75px" readonly>
                       <input type="hidden" name="txt_line_id" id="txt_line_id">
                    </td>
                    <td width="60"><? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?></td>
                    <td width="70">
                       <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" placeholder="Write"  style="width:65px ">
                    </td>
                    <td width="70">
                       <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" placeholder="Browse"  style="width:65px" readonly>
                       <input type="hidden" name="txt_po_id" id="txt_po_id">
                    </td>
                    <td width="65">
                    	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" >
                    </td>
                    <td width="65">
                    	<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"  placeholder="To Date"  >
                    </td>
                    <td>
                        <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(1)" />
                    </td>
                </tr>
                <tr>
                	<td colspan="11" align="center">
 						<? echo load_month_buttons(1); ?>
                   	</td>
                </tr>
               </tbody>
            </table>
        </fieldset>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="center"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$('#cbo_location').val(0);
</script>
</html>

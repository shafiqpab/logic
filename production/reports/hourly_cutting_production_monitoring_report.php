<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Hourly Cutting Production Monitoring Report. Create for Urmi
Functionality	:
JS Functions	:
Created by		:	Shafiq
Creation date 	: 	17-02-2022
Updated by 		:
Update date		:
QC Performed BY	:
QC Date			:
Comments		:   Passion to write clean and documented code.
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Hourly Cutting Production Monitoring Report", "../../", 1, 1,$unicode,1,1,1);

?>
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission = '<? echo $permission; ?>';

	var tableFilters =
	{

	}

	function open_job_no()
	{
		var buyer_name=$("#cbo_buyer_name").val();
		var cbo_year=$("#cbo_year").val();
		var page_link='requires/hourly_cutting_production_monitoring_report_controller.php?action=job_popup&buyer_name='+buyer_name+'&cbo_year='+cbo_year;
		var title="Search Job No Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			var job_no=this.contentDoc.getElementById("hide_job_no").value;

			$("#txt_job_no").val(job_no);
			$("#hidden_job_id").val(job_id);
		}
	}

	function fn_generate_report(type)
	{
		var job_id = document.getElementById('hidden_job_id').value;
		if(job_id=="")
		{
			if( form_validation('cbo_wo_company_name*txt_date','Working Company Name*Cutting Date')==false )
			{
				return;
			}
		}
		else
		{
			if( form_validation('cbo_wo_company_name*hidden_job_id','Working Company Name*Job No')==false )
			{
				return;
			}
		}

		var report_title=$( "div.form_caption" ).html();

		var data="action=generate_report"+get_submitted_data_string('cbo_company_name*cbo_wo_company_name*cbo_buyer_name*hidden_job_id*txt_date',"../../")+'&type='+type+'&report_title='+report_title;

		freeze_window(3);
		http.open("POST","requires/hourly_cutting_production_monitoring_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;
	}

	function generate_report_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("####");
			$("#report_container3").html('');
			$("#report_container2").html(reponse[0]);
			// document.getElementById('report_container').innerHTML = report_convert_button('../../');
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" ondbclick="exportReportToExcel(this);" name="excel" id="exportBtn" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

			setFilterGrid("table_body",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$(".flt").css("display","none");

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="all" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="400px";
		$(".flt").css("display","block");
	}

	function reset_form()
	{
		$("#hidden_style_id").val("");
		$("#hidden_order_id").val("");
		$("#hidden_job_id").val("");

	}

	function openmypage(po_break_down_id,item_id,action,country_id)
	{

		if(action==2 || action==3)
			var popupWidth = "width=1050px,height=350px,";
		else if (action==10)
			var popupWidth = "width=550px,height=420px,";
		else
			var popupWidth = "width=800px,height=470px,";

		if (action==2)
		{
			var popup_caption="Embl. Issue Details";
		}
		else if (action==3)
		{
			var popup_caption="Embl. Rec. Details";
		}
		else
		{
			var popup_caption="Production Quantity";
		}
		emailwindow=dhtmlmodal.open('EmailBox','iframe','requires/hourly_cutting_production_monitoring_report_controller.php?po_break_down_id='+po_break_down_id+'&item_id='+item_id+'&action='+action+'&country_id='+country_id, popup_caption, popupWidth+'center=1,resize=0,scrolling=0','../');
	}
	function openmypage_cutting_popup(search_string)
	{
		var popup_width = 740;
		var popup_height = 400;
		var action = "cutting_popup";
		var title = "Remarks Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/hourly_cutting_production_monitoring_report_controller.php?search_string='+search_string+'&action='+action, title, 'width='+popup_width+','+'height='+popup_height+',center=1,resize=0,scrolling=0','../');

	}
	function openmypage_cutting_popup_one(search_string)
	{
		var popup_width = 740;
		var popup_height = 400;
		var action = "cutting_popup_one";
		var title = "Remarks Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/hourly_cutting_production_monitoring_report_controller.php?search_string='+search_string+'&action='+action, title, 'width='+popup_width+','+'height='+popup_height+',center=1,resize=0,scrolling=0','../');

	}



</script>
<style>
.accordion {
  transition: max-height 1s ease-in;
}

.active, .accordion:hover {
  background-color: #ccc;
}

.panel {
  padding: 0 18px;
  display: none;
  background-color: white;
  overflow: hidden;
}
</style>
</head>

<body onLoad="set_hotkey();">
  <div style="width:100%;" align="center">
   <? echo load_freeze_divs ("../../",'');  ?>
    <h3 style="width:720px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
       <div style="width:100%;" align="center" id="content_search_panel">
    <form id="dateWiseProductionReport_1">
      <fieldset style="width:720px;">
            <table class="rpt_table" width="720" cellpadding="0" cellspacing="0" align="center" border="1" rules="all">
               <thead>
                       <tr>
                        <th width="130" >Company</th>
                        <th class="must_entry_caption" width="130" >Working Company</th>
                        <th width="130">Buyer Name</th>
                        <th width="100">Job No</th>
                        <th class="must_entry_caption"  width="70">Production Date </th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form()"/></th>
						<th></th>
                    </tr>
              </thead>
                <tbody>
                <tr class="general">
                    <td align="center">
                        <?
                            echo create_drop_down( "cbo_company_name", 130, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/hourly_cutting_production_monitoring_report_controller', this.value, 'load_drop_down_buyer', 'td_buyer' );" );
                        ?>
                    </td>
                    <td align="center">
                        <?
                            echo create_drop_down( "cbo_wo_company_name", 130, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 0, "-- Select Company --", $selected, "" );
                        ?>
                    </td>
                    <td align="center" id="td_buyer">
                        <?
                            echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"",1, "-- Select Buyer --", "", "" );
                        ?>
                    </td>
                    <td>
                       <input type="text" id="txt_job_no"  name="txt_job_no"  style="width:100px" class="text_boxes" onDblClick="open_job_no()" placeholder="Browse" readonly />
                       <input type="hidden" id="hidden_job_id"  name="hidden_job_id" />
                    </td>

                    <td align="center">
                        <input name="txt_date" id="txt_date" class="datepicker" style="width:70px" placeholder="From Date">
                    </td>
                    <td>
                         <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_generate_report(1)" />
                     </td>
					 <td>
					 <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show2" onClick="fn_generate_report(2)" />
					 </td>

                </tr>
                </tbody>
            </table>
      </fieldset>

 </form>
 </div>
	<div id="report_container" style="margin:5px 0;"></div>
	<div id="all_report_container">
	    <div id="report_container2"></div>
	    <div id="report_container3"></div>
    </div>
 </div>
</body>
<script>
	set_multiselect('cbo_wo_company_name','0','0','0','0');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
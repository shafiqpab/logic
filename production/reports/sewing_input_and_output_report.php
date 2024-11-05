<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Sewing Input and Output Report for Norban
Functionality	:
JS Functions	:
Created by		:	Shafiq
Creation date 	: 	28-10-2021
Updated by 		:
Update date		:
QC Performed BY	:
QC Date			:
Comments		: Code is poetry, I try to do that!
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Daily Cutting Inhand Report for Youth", "../../", 1, 1,$unicode,1,1,1);

?>
<script src="https://cdn.jsdelivr.net/gh/linways/table-to-excel@v1.0.4/dist/tableToExcel.js"></script>

<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission = '<? echo $permission; ?>';

	var tableFilters =
	{
		col_operation:
		{
			id: ["gr_order_qty","gr_today_lay_qty","gr_total_lay_qty","gr_today_cut_qty","gr_total_cut_qty","gr_today_in_qty","gr_total_in_qty"],
			col: [12,13,14,16,17,19,20],
			operation: ["sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	function open_job_no()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company_name=$("#cbo_company_name").val();
		var cbo_year=$("#cbo_year").val();
		var page_link='requires/sewing_input_and_output_report_controller.php?action=job_popup&company_name='+company_name+'&cbo_year='+cbo_year;
		var title="Search Job/Style Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			var job_no=this.contentDoc.getElementById("hide_job_no").value.split('*');
			var style_no=this.contentDoc.getElementById("hide_style_no").value.split('*');

			$("#txt_job_no").val('');
			$("#txt_style_no").val('');
			$("#hidden_job_id").val('');

			$("#txt_job_no").val([...new Set(job_no)]);
			$("#txt_style_no").val([...new Set(style_no)]);
			$("#hidden_job_id").val(job_id);
		}
	}

	function openmypage_color() // For color
	{
		if( form_validation('hidden_job_id','Search By')==false )
		{
			return;
		}
		var txt_job_id = $("#hidden_job_id").val();
		var page_link='requires/sewing_input_and_output_report_controller.php?action=color_popup&txt_job_id='+txt_job_id;
		var title="Color Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=250px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var color_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var color_name=this.contentDoc.getElementById("txt_selected").value; // product ID
			$("#hidden_color_id").val(color_id);
			$("#txt_color_name").val(color_name);
		}
	}

	function openmypage_floor() // For floor
	{
		if( form_validation('cbo_location_name','Working Location')==false )
		{
			return;
		}
		var location_name = $("#cbo_location_name").val();
		var page_link='requires/sewing_input_and_output_report_controller.php?action=floor_popup&location_name='+location_name;
		var title="Sewing Floor Popup Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=250px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var color_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var color_name=this.contentDoc.getElementById("txt_selected").value; // product ID
			$("#hidden_floor_id").val(color_id);
			$("#txt_floor_name").val(color_name);
		}
	}

	function openmypage_line() // For Line
	{
		if( form_validation('txt_floor_name','Floor Name')==false )
		{
			return;
		}
		var wo_company_name = $("#cbo_wo_company_name").val();
		var location_name = $("#cbo_location_name").val();
		var floor_name = $("#hidden_floor_id").val();
		var page_link='requires/sewing_input_and_output_report_controller.php?action=line_search_popup&wo_company_name='+wo_company_name+'&location_name='+location_name+'&floor_name='+floor_name;
		var title="Line Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=250px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var line_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var line_name=this.contentDoc.getElementById("txt_selected").value; // product ID
			$("#txt_line").val(line_name);
			$("#hidden_line_id").val(line_id);
		}
	}

	function fn_generate_report(type)
	{
		var int_ref = $("#txt_int_ref").val();
		// alert(int_ref); return;
		if(int_ref == '')
		{
			if( form_validation('cbo_company_name*hidden_job_id','Company Name*Job/Style')==false )
			{
				return;
			}
		}

		var report_title=$( "div.form_caption" ).html();

		var data="action=generate_report"+get_submitted_data_string('cbo_company_name*hidden_job_id*hidden_color_id*cbo_year*cbo_wo_company_name*cbo_location_name*hidden_floor_id*hidden_line_id*txt_int_ref',"../../")+'&type='+type+'&report_title='+report_title;


		freeze_window(3);
		http.open("POST","requires/sewing_input_and_output_report_controller.php",true);
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
			$("#report_container_sm").html(reponse[3]);
			// document.getElementById('report_container').innerHTML = report_convert_button('../../');
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Date Excel Preview" ondbclick="exportReportToExcel(this);" name="excel" id="exportBtn" class="formbutton" style="width:130px"/></a>&nbsp;&nbsp;<a href="requires/'+reponse[2]+'" style="text-decoration:none"><input type="button" value="Line Wise Excel" ondbclick="exportReportToExcel(this);" name="excel" id="exportBtn" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Date Print Preview" name="Print" class="formbutton" style="width:130px"/>&nbsp;&nbsp;<input type="button" onclick="new_window2()" value="Line Print Preview" name="Print" class="formbutton" style="width:130px"/>';

			setFilterGrid("table_body",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
	}

	function exportReportToExcel() {
	  	TableToExcel.convert(document.getElementById("all_report_container"), {
            name: "assignment-list.xlsx",
            sheet: {
            name: "Sheet1"
            }
        });
	}

	function fn_generate_details_report(color_id,floor_id,line_id)
	{
		if( form_validation('cbo_company_name*hidden_job_id','Company Name*Job/Style')==false )
		{
			return;
		}

		var report_title=$( "div.form_caption" ).html();

		var data="action=generate_details_report"+get_submitted_data_string('cbo_company_name*hidden_job_id*hidden_color_id*cbo_year*cbo_wo_company_name*cbo_location_name*hidden_floor_id*hidden_line_id',"../../")+'&color_id='+color_id+'&floor_id='+floor_id+'&line_id='+line_id;


		freeze_window(3);
		http.open("POST","requires/sewing_input_and_output_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_details_report_reponse;
	}

	function generate_details_report_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("####");
			/*$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" ondbclick="exportReportToExcel(this);" name="excel" id="exportBtn" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';


			show_msg('3');
			release_freezing();	 */

			var reponse=trim(http.responseText);
			$("#report_container3").html(reponse);
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

	function new_window2()
	{
		document.getElementById('scroll_body2').style.overflow="auto";
		document.getElementById('scroll_body2').style.maxHeight="none";
		$(".flt").css("display","none");

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="all" /><title></title></head><body>'+document.getElementById('report_container_sm').innerHTML+'</body</html>');
		d.close();

		document.getElementById('scroll_body2').style.overflowY="scroll";
		document.getElementById('scroll_body2').style.maxHeight="400px";
		$(".flt").css("display","block");
	}

	function open_job_qty_popup(company,job_no)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sewing_input_and_output_report_controller.php?company='+company+'&job_no='+job_no+'&action=job_qty_popup', 'Job Qty Popup', 'width=1050px,height=450px,center=1,resize=0,scrolling=0','../');
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
		emailwindow=dhtmlmodal.open('EmailBox','iframe','requires/sewing_input_and_output_report_controller.php?po_break_down_id='+po_break_down_id+'&item_id='+item_id+'&action='+action+'&country_id='+country_id, popup_caption, popupWidth+'center=1,resize=0,scrolling=0','../');
	}
	function openmypage_production_popup(po,item,color,type,day,action,title,popup_width,popup_height)
	{

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sewing_input_and_output_report_controller.php?po='+po+'&action='+action+'&item='+item+'&color='+color+'&type='+type+'&day='+day, title, 'width='+popup_width+','+'height='+popup_height+',center=1,resize=0,scrolling=0','../');

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
    <h3 style="width:1250px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
       <div style="width:100%;" align="center" id="content_search_panel">
    <form id="dateWiseProductionReport_1">
      <fieldset style="width:1240px;">
            <table class="rpt_table" width="1220" cellpadding="0" cellspacing="0" align="center" border="1" rules="all">
               	<thead>
                    <tr>
                        <th class="must_entry_caption" width="130" >LC Company</th>
                        <th width="60">Job Year</th>
                        <th class="must_entry_caption"  width="100">Job No</th>
                        <th class="must_entry_caption"  width="100">Style </th>
                        <th width="100">IR/IB</th>
                        <th width="100">Color</th>
                        <th width="130" >Working Company</th>
                        <th class="" width="130" >Location</th>
                        <th class="" width="130" >Floor</th>
                        <th class="" width="100" >Line</th>
                        <th colspan="2"><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form()"/></th>
                    </tr>
              	</thead>
                <tbody>
                <tr class="general">
                    <td align="center" id="td_company">
                        <?
                            echo create_drop_down( "cbo_company_name", 130, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                        ?>
                    </td>
                    <td>
                        <?
						echo create_drop_down( "cbo_year", 60, $year,"", 1, "--All--", date('Y'), "",0 );
						?>
                    </td>
                    <td>
                       <input type="text" id="txt_job_no"  name="txt_job_no"  style="width:100px" class="text_boxes" onDblClick="open_job_no()" placeholder="Browse" readonly />
                       <input type="hidden" id="hidden_job_id"  name="hidden_job_id" />
                    </td>
                    <td>
                    	<input type="text" id="txt_style_no"  name="txt_style_no"  style="width:100px" class="text_boxes" onDblClick="open_job_no()" placeholder="Browse" readonly />
                    </td>
                    <td>
                    	<input type="text" id="txt_int_ref"  name="txt_int_ref"  style="width:100px" class="text_boxes" placeholder="Internal Ref. No."/>
                    </td>
                    <td>
                     	<input type="text" id="txt_color_name"  name="txt_color_name"  style="width:100px" class="text_boxes" placeholder="Color"  onDblClick="openmypage_color()" readonly="true" />
                     	<input type="hidden" id="hidden_color_id"  name="hidden_color_id" />
                    </td>
                    <td align="center">
                        <?
                        echo create_drop_down( "cbo_wo_company_name", 130, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/sewing_input_and_output_report_controller', this.value, 'load_drop_down_location', 'location_td' );" );
                        ?>
                    </td>
                    <td align="center" id="location_td">
                        <?
                            echo create_drop_down( "cbo_location_name", 130, $blank_array,"",1, "-- Select location --", "", "" );
                        ?>
                    </td>

                    <td align="center" id="floor_td">
                        <?
                        	// echo create_drop_down( "cbo_floor_name", 130, $blank_array,"",1, "-- Select floor --", "", "" );
                        ?>
                        <input type="text" name="txt_floor_name" id="txt_floor_name" style="width:100px" class="text_boxes" onDblClick="openmypage_floor()" placeholder="Browse" readonly>
                        <input type="hidden" id="hidden_floor_id"  name="hidden_floor_id" />
                    </td>
                    <td>
                    	<input type="text" id="txt_line"  name="txt_line"  style="width:100px" class="text_boxes" placeholder="Browse"  onDblClick="openmypage_line()" readonly="true" />
                    	<input type="hidden" id="hidden_line_id"  name="hidden_line_id" />
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
	<div id="report_container" style="margin:10px 0;"></div>
	<div id="all_report_container">
	    <div id="report_container2"></div>
	    <div id="report_container3"></div>
	    <div id="report_container_sm" style="display: none;"></div>
    </div>
 </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

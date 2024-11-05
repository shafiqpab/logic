<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Line Wise Productivity Analysis Report

Functionality	:
JS Functions	:
Created by		:	Arnab Dutta
Creation date 	: 	24-05-2023
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
echo load_html_head_contents("Hourly Gmt Finishing Monitoring Report Analysis","../../", 1, 1, $unicode,1,1);
?>
<script>
	var permission='<? echo $permission; ?>';

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report(type)
	{
		var company = document.getElementById('cbo_company_id').value;

		if ((company==0 || company=='')) {
			alert('please select Company Or Working Company');
			return;
		}

		if( form_validation('txt_date','Production Date')==false )
		{
			return;
		}

		var report_title=$( "div.form_caption" ).html();

		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_floor_id*cbo_buyer_name*txt_date',"../../")+'&report_title='+report_title+'&type='+type;
		freeze_window(3);
		http.open("POST","requires/hourly_gmt_finishing_monitoring_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;
	}

	function generate_report_reponse()
	{
		if(http.readyState == 4)
		{
			//alert (http.responseText);
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="##" onclick="fnExportToExcel()" target=_blank; style="text-decoration:none" id="dlink"><input type="button" class="formbutton" value="Export to Excel" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			//  setFilterGrid("table_body",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
	}

	function fnExportToExcel()
	{
		// $(".fltrow").hide();
		let tableData = document.getElementById("report_container2").innerHTML;
		// alert(tableData);
	    let data_type = 'data:application/vnd.ms-excel;base64,',
		template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table border="2px">{table}</table></body></html>',
		base64 = function (s) {
			return window.btoa(unescape(encodeURIComponent(s)))
		},
		format = function (s, c) {
			return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; })
		}

		let ctx = {
			worksheet: 'Worksheet',
			table: tableData
		}

        let dt = new Date();
	    document.getElementById("dlink").href = data_type + base64(format(template, ctx));
	    document.getElementById("dlink").traget = "_blank";
        document.getElementById("dlink").download = dt.getTime()+'_display_board.xls';
	    document.getElementById("dlink").click();
		// $(".fltrow").show();
		// alert('ok');
	}


	function new_window()
	{
		document.getElementById('report_container2').style.overflow="auto";
		document.getElementById('report_container2').style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		document.getElementById('report_container2').style.overflowY="auto";
		document.getElementById('report_container2').style.maxHeight="400px";
	}

	function openmypage_job_total(company_id,location_id,floor_id,buyer_name,txt_date,action,width,height, job_id, po_id)
	{
		var popup_width=width;
		var popup_height=height;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/hourly_gmt_finishing_monitoring_report_controller.php?company_id='+company_id+'&location_id='+location_id+'&floor_id='+floor_id+'&buyer_name='+buyer_name+'&txt_date='+txt_date+'&action='+action+'&job_id='+job_id+'&po_id='+po_id, 'Total Popup', 'width='+popup_width+','+'height='+popup_height+',center=1,resize=0,scrolling=0','../');

	}






	function getLocationId()
	{
	    var company_id = document.getElementById('cbo_wo_company_id').value;
	    // alert(floor_id);
	    var formData = company_id;

	    if(company_id !='') {
	      var data="action=load_drop_down_location&data="+formData;
	      http.open("POST","requires/.php",true);
	      http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	      http.send(data);
	      http.onreadystatechange = function(){
	          if(http.readyState == 4)
	          {
	              var response = trim(http.responseText);
	              $('#location_td').html(response);
	          }
	      };
	    }
	}


</script>

</head>
<body onLoad="set_hotkey();">

	<form id="HourlyGmtFinishingMonitoring_1">
	    <div style="width:100%;" align="center">

	        <? echo load_freeze_divs ("../../",'');  ?>

	         <h3 style="width:780px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
	         <div id="content_search_panel" >
	         <fieldset style="width:670px;">
	             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
	             	<thead>
	                    <th class="must_entry_caption">Company</th>
	                    <th class="must_entry_caption">Production Date</th>
	                    <th>Location</th>
	                    <th>Floor</th>
	                    <th>Buyer</th>
	                    <th><input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('HourlyGmtFinishingMonitoring_1','report_container','','','')" /></th>
	                </thead>
	                <tbody>
	                    <tr class="general">
	                       <td>
								<?
	                                echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/hourly_gmt_finishing_monitoring_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td_id' );load_drop_down( 'requires/hourly_gmt_finishing_monitoring_report_controller', this.value, 'load_drop_down_location', 'location_td' );" );
	                            ?>
	                        </td>

	                         <td>
	                            <input type="text" name="txt_date" id="txt_date" class="datepicker" style="width:80px;" readonly/>
	                        </td>
	                        <td id="location_td">
	                            <?
	                                echo create_drop_down( "cbo_location_id", 140, $blank_array,"", 1, "-- Select Location --", "", "" );
	                            ?>
	                        </td>
	                        <td id="floor_td">
	                            <?
	                                echo create_drop_down( "cbo_floor_id", 130, $blank_array,"", 1, "-- Select Floor --", "", "" );
	                            ?>
	                        </td>

	                        <td id="buyer_td_id">
	                            <?
	                               echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-- Select Buyer --", 0, "",0 );
	                            ?>
	                        </td>
	                        <td>
	                            <input type="button" name="search" id="search" value="Finishing Output" onClick="generate_report(1)" style="width:120px" class="formbutton" />&nbsp;

	                        </td>
	                    </tr>
	                </tbody>
	            </table>
	        </fieldset>
	    	</div>
	    </div>
	    <div id="report_container" align="center"></div>
	    <div id="report_container2" align="left" style="margin: 10px 0"></div>
 	</form>
</body>
<script>

	set_multiselect('cbo_floor_id','0','0','','0');
	setTimeout[($("#floor_td a").attr("onclick","disappear_list(cbo_floor_id,'0');getLineId();") ,3000)];
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$('#cbo_location_id').val(0);
</script>
</html>

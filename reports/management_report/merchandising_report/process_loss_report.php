<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Process Loss Report.
Functionality	:
JS Functions	:
Created by		:	Fuad
Creation date 	: 	09-08-2014
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
echo load_html_head_contents("Process Loss Report","../../../", 1, 1, $unicode,1,1);
?>
<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";

	var tableFilters =
	 {
		/*col_32: "none",*/
		col_operation: {
		id: ["total_order_qnty_in_pcs","total_country_qnty_in_pcs","value_yarn_used","value_grey_produced","value_grey_used","value_fin_produced","value_fin_used","possible_cut_pcs","actual_cut_pcs","fin_gmts_pcs","value_effec_fab_uses","value_tot_fab_loss","ex_factory","td_cut_to_ship","td_order_to_ship","gmts_left_over","value_grey_left_over","value_fin_left_over","value_opportunity_loss"],
	    col: [11,13,14,15,17,18,21,22,23,24,25,27,29,30,32,34,35,36,37],
	    operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
	    write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	 }

	function fn_report_generated(type)
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		else
		{
			var data="action=report_generate&type="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*cbo_year*txt_order_no*hide_order_id*txt_date_from*txt_date_to*shipping_status*txt_file_no*txt_ref_no*cbo_date_type',"../../../");
			freeze_window(3);
			http.open("POST","requires/process_loss_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("****");
			var tot_rows=reponse[2];
			$('#report_container2').html(reponse[0]);
			// alert(reponse[0]);
			document.getElementById('report_container').innerHTML=report_convert_button('../../../');
			append_report_checkbox('table_header_1',1);

			setFilterGrid("table_body",-1,tableFilters);
	 		show_msg('3');
			release_freezing();
		}
	}
	// function fnExportToExcel()
	// {
	// 	// $(".fltrow").hide();
	// 	let tableData = document.getElementById("report_container2").innerHTML;
	// 	// alert(tableData);
	//     let data_type = 'data:application/vnd.ms-excel;base64,',
	// 	template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table border="2px">{table}</table></body></html>',
	// 	base64 = function (s) {
	// 		return window.btoa(unescape(encodeURIComponent(s)))
	// 	},
	// 	format = function (s, c) {
	// 		return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; })
	// 	}

	// 	let ctx = {
	// 		worksheet: 'Worksheet',
	// 		table: tableData
	// 	}

    //     let dt = new Date();
	//     document.getElementById("dlink").href = data_type + base64(format(template, ctx));
	//     document.getElementById("dlink").traget = "_blank";
    //     document.getElementById("dlink").download = dt.getTime()+'_display_board.xls';
	//     document.getElementById("dlink").click();
	// 	// $(".fltrow").show();
	// 	// alert('ok');
	// }

	function openmypage_order()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}

		var companyID = $("#cbo_company_name").val();
		var page_link='requires/process_loss_report_controller.php?action=order_no_search_popup&companyID='+companyID;
		var title='Order No Search';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var order_no=this.contentDoc.getElementById("hide_order_no").value;
			var order_id=this.contentDoc.getElementById("hide_order_id").value;

			$('#txt_order_no').val(order_no);
			$('#hide_order_id').val(order_id);
		}
	}

	function generate_ex_factory_popup(company,id,action)
	{
		//alert(job_no);
		var width=550;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/process_loss_report_controller.php?action='+action+'&company='+company+'&id='+id, 'Ex-Factory Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
	}
	function fnExportToExcel()
	{
		// $(".fltrow").hide();
		let tableData = document.getElementById("report_container2").innerHTML;
		//  alert(tableData);
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
	

	function fnc_generate_popup(company,order_id,action)
	{
		//alert(job_no);
		var popup_width='';
		if(action=="yarn_issue_popup")
		{
			popup_width='1000px';
		}
		else
		{
			popup_width='890px';
		}

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/process_loss_report_controller.php?action='+action+'&company='+company+'&order_id='+order_id, 'Report Details', 'popup_width='+popup_width+',height=400px,center=1,resize=0,scrolling=0','../../');
	}
</script>

</head>

<body onLoad="set_hotkey();">

<form id="cost_breakdown_rpt">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:1330px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel">
            <fieldset style="width:1330px;">
                <table class="rpt_table" width="1320" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                	<thead>
                    	<tr>
                            <th class="must_entry_caption">Company Name</th>
                            <th>Buyer Name</th>
                            <th>Job Year</th>
                    		<th>Job No</th>
                            <th>Order No</th>
                            <th>File No</th>
                            <th>Ref. No</th>
                            <th>Shipment Status</th>
                            <th>Date Category</th>
                            <th id="pub_shipment_date_td">Shipment Date</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:80px" value="Reset" /></th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr class="general">
                        <td>
                            <?
                                echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/process_loss_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <?
                                echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                            ?>
                        </td>
                        <td>
                        	<?
								echo create_drop_down( "cbo_year", 65, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
							?>
                        </td>
                        <td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:70px" placeholder="Write" /></td>
                        <td>
                            <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:120px" placeholder="Browse Or Write" onDblClick="openmypage_order();" onChange="$('#hide_order_id').val('');" autocomplete="off">
                            <input type="hidden" name="hide_order_id" id="hide_order_id" readonly>
                        </td>
                         <td>
                            <input type="text" name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:70px" placeholder="Write" >
                        </td>
                           <td>
                            <input type="text" name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:80px" placeholder="Write" >
                        </td>
                        <td>
                        	<?
								echo create_drop_down( "shipping_status", 120, $shipment_status,"", 0, "-- Select --", 3, "",0,'','','','','' );
							?>
                        </td>
                        <td>
                        	<?
							$date_type_arr = array(1=>'Shipment Date',2=>'Ex-factory Date');
							echo create_drop_down( "cbo_date_type", 80, $date_type_arr,"",0, "--Select--", 1,"$('#pub_shipment_date_td').html($('#cbo_date_type :selected').text())",0 );
							?>
                        </td>
                        <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" >&nbsp; To
                        <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"  placeholder="To Date" ></td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:80px" value="Show" onClick="fn_report_generated(1)" />
							<input type="button" id="show_button" class="formbutton" style="width:80px" value="Show2" onClick="fn_report_generated(2)" />
                        </td>
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
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
     <div style="display:none" id="data_panel"></div> 
 </form>
</body>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script> 

</html>

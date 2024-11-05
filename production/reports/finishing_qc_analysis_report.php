<?
/*-------------------------------------------- Comments

Purpose			: 	This Report will Create Finishing QC Analysis
Functionality	:
JS Functions	:
Created by		:	Zakaria joy
Creation date 	: 	07-10-2019
Updated by 		:
Update date		:
QC Performed BY	:	Maruf
QC Date			:
Comments		:

*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Finishing QC Analysis", "../../", 1, 1,'','','');

?>
<script type="text/javascript" src="../../Chart.js-master/Chart.js"></script>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission='<? echo $permission; ?>';

	function fso_report_generate(value, group){
		var data = value + '*' + 'Fabric Sales Order Report';
    		if (group == 1) {
    			window.open("../requires/fabric_sales_order_entry_controller.php?data=" + data + '&action=fabric_sales_order_print', true);
    		} else {
    			window.open("../requires/fabric_sales_order_entry_controller.php?data=" + data + '&action=fabric_sales_order_print2', true);
    		}
    		return;
	}

	function new_window()
	{
		$('#table_body tr:first').hide();
		$('#table_body2 tr:first').hide();
		$('#table_body3 tr:first').hide();

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		$('#table_body tr:first').show();
		$('#table_body2 tr:first').show();
		$('#table_body3 tr:first').show();
	}

	function open_salse_order_popup(page_link,title)
	{
		if( form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		else
		{
			var cbo_company_name=$('#cbo_company_name').val();

			page_link=page_link+'&cbo_company_name='+cbo_company_name;

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=700px,height=420px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var salse_id=this.contentDoc.getElementById("salse_order_id");
				var salse_no=this.contentDoc.getElementById("salse_order_no");

				if (salse_id.value!="" && salse_no.value != "")
				{
					document.getElementById("cbo_sales_order_no").value = salse_no.value;
					document.getElementById("cbo_sales_order_id").value = salse_id.value;

				}

			}
		}
	}
	function fn_finishing_report_generated(operation)
	{
	    var working_company=$("#cbo_company_name").val();
	    var within_group=$("#cbo_within_group").val();
	    var po_company_name=$("#cbo_po_company_name").val();
	    var po_buyer_name=$("#cbo_po_buyer_name").val();
	    var salse_order_no=$("#cbo_sales_order_no").val();
	    var salse_order_id=$("#cbo_sales_order_id").val();
	    var date_from=$("#txt_date_from").val();
	    var date_to=$("#txt_date_to").val();
	    if(salse_order_no == '')
	    {
	        if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*From date Fill*To date Fill')==false)
	        {
	            return;
	        }
	    }
	    else
	    {
	        if(form_validation('cbo_company_name','Company Name')==false)
	        {
	            return;
	        }

	    }

		var report_title ="Finishing Qc Analysis Report";
	    freeze_window(5);
	    var data="action=report_generate&type="+operation+get_submitted_data_string('cbo_company_name*cbo_within_group*cbo_po_company_name*cbo_po_buyer_name*cbo_sales_order_no*cbo_sales_order_id*cbo_year*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
	    // alert(data);
	    http.open("POST","requires/finishing_qc_analysis_report_controller.php",true);
	    http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	    http.send(data);
	    http.onreadystatechange = fnc_show_finishing_qc_analysis_report;
	}
	function fnc_show_finishing_qc_analysis_report()
	{
		if(http.readyState ==4)
		{
			// alert(http.responseText);
			var reponse=trim(http.responseText).split("****");
			//alert(reponse);
			//document.getElementById('report_container2').innerHTML=http.responseText;
			//document.getElementById('report_container').innerHTML=report_convert_button('../../');

		
			var response=trim(http.responseText).split("****");
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			show_msg('3');
			setFilterGrid("table_body",-1);
			release_freezing();
		}
 	}
 	function fnc_date_check(todate)
	{
		var from_date = $('#txt_date_from').val();
		var to_date = $('#txt_date_to').val();
		if(from_date!="" && to_date!="")
		{
			var to_dates=to_date.split('-');
			var from_dates=from_date.split('-');
			var to_mon_year=to_dates[1]+'-'+to_dates[2];
			var from_mon_year=from_dates[1]+'-'+from_dates[2];
			//alert(from_mon_year);
			if(from_mon_year==to_mon_year)
			{
				$('#txt_date_from').val(from_date);
				$('#txt_date_to').val(to_date);
			}
			else
			{
				alert('Month Mixed Not Allow');
				$('#txt_date_to').val('');
			}
		}
	}
</script>
</head>
<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",''); ?>
		 <form name="finishingQcAnalysis_1" id="finishingQcAnalysis_1">
         <h3 style="width:1200px; margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>  <div id="content_search_panel" >
             <fieldset style="width:1100px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <th class="must_entry_caption" width="160">Working Company</th>
                            <th width="100">Within Group</th>
                            <th width="160">PO Company</th>
                            <th width="120">PO Buyer</th>
                            <th width="60">Year</th>
                            <th width="140">Sales Order No</th>
                            <th class="must_entry_caption" id="th_date" width="150">Production date Range</th>
                            <th width="160"><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('finishingQcAnalysis_1','report_container*report_container2','','','')" class="formbutton" style="width:50px" /></th>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <?
                                        echo create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --", 0, "" );
                                    ?>

                                </td>

                                <td>
                                	<?
                                        echo create_drop_down("cbo_within_group", 100, $yes_no, "", 1, "-- Select --", 0,"");
									?>
                                </td>

                                 <td>
                                	<?
                                        echo create_drop_down( "cbo_po_company_name", 160, "select id,company_name from lib_company where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/finishing_qc_analysis_report_controller', this.value, 'load_drop_down_buyer', 'cbo_po_buyer_name_td'); " );
									?>
                                </td>
                                <td id="cbo_po_buyer_name_td">
                                	<?
                                        echo create_drop_down( "cbo_po_buyer_name", 120,$blank_array,"", 1, "-- Select Buyer --", 0, "" );
									?>
                                </td>
                                 <td>
                                	<?
                                       echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
									?>
                                </td>

                                <td>
                                	<input type="text" style="width:130px" class="text_boxes" id="cbo_sales_order_no" placeholder="Browse" onDblClick="open_salse_order_popup('requires/finishing_qc_analysis_report_controller.php?action=salse_order_popup','Sales Order Selection Form')" readonly="">
                                	<input type="hidden" id="cbo_sales_order_id" value="">
                                </td>

                                <td align="center">
                                     <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:50px" placeholder="From Date" onChange="fnc_date_check(this.value);" />
                                     &nbsp;To&nbsp;
                                     <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:50px" placeholder="To Date" onChange="fnc_date_check(this.value);"/>
                                </td>
                                <td><input type="button" id="show_button2" class="formbutton" style="width:60px" value="Details" onClick="fn_finishing_report_generated(1)" />&nbsp;
                                <input type="button" id="show_button" class="formbutton" style="width:60px" value="Summary" onClick="fn_finishing_report_generated(2)" />
                               </td>
                            </tr>
                        </tbody>
                    </table>
                    <table>
            	<tr>
                	<td colspan="8">
 						<? echo load_month_buttons(1); ?>
                   	</td>
                </tr>
            </table>
            <br />
                </fieldset>
         <div id="report_container" align="center"></div>
        <div id="report_container2" align="center"></div>
            </div>
		</form>

	</div>

</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
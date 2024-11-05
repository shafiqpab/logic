<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Daily Roll wise Knitting QC Report-sales.
Functionality	:
JS Functions	:
Created by		:	Md. Saidul Islam Reza
Creation date 	: 	13-10-2019
Updated by 		:
Update date		:
QC Performed BY	:
QC Date			:
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:../../logout.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Daily Roll wise Knitting QC Report-sales", "../../", 1, 1,$unicode,1,1);
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission = '<? echo $permission; ?>';


	var tableFilters1 =
	{
        col_0: "none",
		col_operation: {
		id: ["total_qc_pass_qty","total_qc_held_qty","total_reject_qty","total_production_weight"],
		col: [32,33,34,35],
		operation: ["sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}



	function fn_generate_report(type)
	{

		if($("#txt_sales_order_no").val() !='' || $("#txt_barcode").val() !='' || $("#txt_style_ref").val() !=''|| $("#txt_program_no").val() !=''){
			var idStr="cbo_company_name";
			var msgStr="Company Name";
		}
		else
		{
			var idStr="cbo_company_name*txt_date_from*txt_date_to";
			var msgStr="Company Name*Start Date*End Date";
		}


		if( form_validation(idStr,msgStr)==false )
		{
			return;
		}

        if(type==1 || type==2)
        {
            var program_no = $('#txt_program_no').val();
            if(program_no !='')
            {
                alert("This Program No Field Required Only Show2 Button.");
                $('#txt_program_no').val("")
                return;
            }
        }

		if(type==1){var action="generate_report";}
		else if(type==2){var action="generate_report_summary";}
        else if(type==3)
        {
            if(form_validation('txt_program_no','Program No')==false)
            {
                return;
            }
            var action="generate_report2";
        }

		var report_title=$( "div.form_caption" ).html();
		var data="action="+action+"&report_title="+report_title+"&report_type="+type+get_submitted_data_string('cbo_company_name*cbo_knitting_source*cbo_knitting_company*cbo_buyer_id*cbo_year*txt_sales_order_no*txt_style_ref*txt_barcode*txt_ref_no*cbo_booking_type*cbo_floor*cbo_machine_name*cbo_shift_name*cbo_search_type*txt_date_from*txt_date_to*txt_program_no',"../../");
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/daily_roll_wise_knitting_qc_report_sales_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;
	}

	function generate_report_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
         
            if(reponse[2]==1)
            {
                setFilterGrid("table_body",-1,tableFilters1);
            }
            else
            {
                setFilterGrid("table_body",-1);
            }
			
			show_msg('3');
			release_freezing();
            new_window2();
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
        $("#table_body tr:first").hide();

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_print.css" type="text/css" /><title>Html Print</title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="400px";
		$("#table_body tr:first").show();
	}




	function load_machine()
	{
		var cbo_company_id = $('#cbo_company_name').val();
		var cbo_knitting_source = $('#cbo_knitting_source').val();
		var cbo_knitting_company = $('#cbo_knitting_company').val();
		var cbo_floor_id = $('#cbo_floor').val();

		if(cbo_knitting_source==1)
		{
			load_drop_down( 'requires/daily_roll_wise_knitting_qc_report_sales_controller',cbo_knitting_company+'_'+cbo_floor_id, 'load_drop_machine', 'machine_td' );
		}
		else
		{
			load_drop_down( 'requires/daily_roll_wise_knitting_qc_report_sales_controller',cbo_company_id+'_'+cbo_floor_id, 'load_drop_machine', 'machine_td' );
		}
	}


	function fn_observation(barcode)
	{
		var title = 'Observation';
		var page_link='requires/daily_roll_wise_knitting_qc_report_sales_controller.php?action=observation_popup&barcode='+barcode;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=400px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{

		}
	}



    function sales_order_popup() {
        if (form_validation('cbo_company_name', 'Company Name') == false) {
            return;
        }

        var companyID = $("#cbo_company_name").val();
        var yearID = $("#cbo_year").val();
        var ordType = $("#cbo_type").val();
        var cbo_within_group = $("#cbo_within_group").val();
        var page_link = 'requires/daily_roll_wise_knitting_qc_report_sales_controller.php?action=sales_order_no_search_popup&companyID=' + companyID + '&yearID=' + yearID + '&ordType=' + ordType + '&cbo_within_group=' + cbo_within_group;
        var title = 'Sales Order No Search';

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=390px,center=1,resize=1,scrolling=0', '../');
        emailwindow.onclose = function () {
            var theform = this.contentDoc.forms[0];
            var sales_job_no = this.contentDoc.getElementById("hidden_job_no").value;
            $('#txt_sales_order_no').val(sales_job_no);
        }
    }

</script>

</head>

<body onLoad="set_hotkey();">
  <div style="width:100%;" align="center">
   <? echo load_freeze_divs ("../../",'');  ?>
    <h3 style="width:1790px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    <div style="width:100%;" align="center" id="content_search_panel">
    <form id="dateWiseProductionReport_1">
      <fieldset style="width:1790px;">
        <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" align="center" border="1" rules="all">
            <thead>
                <tr>
                    <th class="must_entry_caption">Company Name</th>
                    <th>Knitting Source</th>
                    <th>Working Company</th>
                    <th>Buyer</th>
                    <th>Year</th>
                    <th>Sales Order No</th>
                    <th>Style Ref.</th>
                    <th>Program</th>
                    <th>Barcode</th>
                    <th>Int. Ref No</th>
                    <th>Booking Type</th>
                    <th>Floor</th>
                    <th>M/C No</th>
                    <th>Shift</th>
                    <th>Search Type</th>
                    <th colspan="2" class="must_entry_caption">Date Range</th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form()"/></th>
                </tr>
            </thead>
            <tbody>
                <tr class="general">
                    <td align="center">
                        <?
                            echo create_drop_down("cbo_company_name", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--Select--", $selected, "load_drop_down( 'requires/daily_roll_wise_knitting_qc_report_sales_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );");

						?>
                    </td>

                    <td align="center">
                        <?
                            echo create_drop_down( "cbo_knitting_source", 100, $knitting_source,"", 1, "--Select--", 1, "load_drop_down( 'requires/daily_roll_wise_knitting_qc_report_sales_controller', this.value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_knitting_com','knitting_com_td');",0,"1,3" );
                        ?>
                    </td>
                    <td align="center" id="knitting_com_td">
                        <?
                            echo create_drop_down( "cbo_knitting_company", 100, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- All--", $selected, "load_drop_down( 'requires/daily_roll_wise_knitting_qc_report_sales_controller', this.value, 'load_drop_down_floor', 'floor_td' );" );
                        ?>
                    </td>
                    <td align="center" id="buyer_td">
                        <?
							echo create_drop_down( "cbo_buyer_id", 100,array(),"", 1, "--All--", 0, "",0 );
						?>
                    </td>
                    <td align="center">
                        <?
							echo create_drop_down( "cbo_year", 60, $year,"", 1, "Year--", 0, "",0 );
						?>
                    </td>
                     <td>
                       <input type="text" id="txt_sales_order_no"  name="txt_sales_order_no"  style="width:80px" class="text_boxes" onDblClick="sales_order_popup()" placeholder="Write/Brows"/>
                    </td>
                    <td>
                       <input type="text" id="txt_style_ref"  name="txt_style_ref"  style="width:70px" class="text_boxes" placeholder="Write"/>
                    </td>
                    <td>
                       <input type="text" id="txt_program_no"  name="txt_program_no"  style="width:80px" class="text_boxes" placeholder="Write"/>
                    </td>
                     <td>
                       <input type="text" id="txt_barcode"  name="txt_barcode"  style="width:80px" class="text_boxes" placeholder="Write"/>
                    </td>
                    <td>
                        <input type="text"  name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:70px;" placeholder="Write">
                    </td>
                    <td>
                      <?
                        $booking_type_arr=array(
                            118=>"Main Fabric Booking",
                            108=>"Partial Fabric Booking",
                            88=>"Short Fabric Booking",
                            89=>"Sample Fabric Booking With Order",
                            90=>"Sample Fabric Booking Without Order"
                        );
							echo create_drop_down( "cbo_booking_type", 100, $booking_type_arr,"", 1, "--All--", 0, "",0 );
						?>
                    </td>
                    <td id="floor_td">
                        <?
							echo create_drop_down( "cbo_floor", 100,array(),"", 1, "--All--", 0, "",0 );
						?>
                    </td>
                    <td id="machine_td">
                    <? echo create_drop_down( "cbo_machine_name", 80, $blank_array,"", 1, "-- All --", 0, "",0 ); ?>
                    </td>
                    <td>
                        <?
                        echo create_drop_down( "cbo_shift_name", 80, $shift_name,"", 1, "-- All --", 0, "",'' );
						?>
                    </td>
                    <td>
                        <?
                        $search_type_arr=array(
                            1=>"--All--",
                            2=>"QC Pass"
                        );
						echo create_drop_down( "cbo_search_type", 70, $search_type_arr,"", 0, "--All--", 0, "",0 );
						?>
                    </td>
                    <td>
                        <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:70px" placeholder="From Date"/>
                    </td>
                    <td>
                        <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:70px" placeholder="To Date"/>
                    </td>
                    <td>
                         <input type="button" id="show_button1" class="formbutton" style="width:70px" value="Show" onClick="fn_generate_report(1)" />
                         <input type="button" id="show_button3" class="formbutton" style="width:70px" value="Show 2" onClick="fn_generate_report(3)" />
                         <input type="button" id="show_button2" class="formbutton" style="width:70px" value="Summary" onClick="fn_generate_report(2)" />

                     </td>
                </tr>
                <tr>
                    <td colspan="18" align="center" width="95%"><? echo load_month_buttons(1); ?></td>
                </tr>
             </tbody>
         </table>
      </fieldset>
 </form>
 </div>
    <div id="report_container" ></div>
    <div id="report_container2"></div>
 </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

<?
/*-------------------------------------------- Comments
Purpose			: 	This form will Create Fabric Type Wise Knitting Production report
Functionality	:
JS Functions	:
Created by		:	Abu Sayed
Creation date 	: 	09-01-2024
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

echo load_html_head_contents("Fabric Type Wise Knitting Production report", "../../", 1, 1, $unicode,1,1);

?>
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission='<? echo $permission; ?>';

	var tableFilters =
	{
		col_0: "none",
		col_operation: {
		id: ["val_total_main_booking_qty","val_total_short_booking_qty","val_total_knitting_production_qty","val_total_short_perc"],
		col: [4,5,6,7],
		operation: ["sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	function fn_report_generated(report_type)
	{
		if( form_validation('cbo_working_company_id*txt_date_from*txt_date_to','Working Company Name*From Date*To Date')==false )
        {
            return;
        }

		if(report_type==1)
		{
			var action = "report_generate";
		}

        var report_title=$( "div.form_caption" ).html();

		var data="action="+action+get_submitted_data_string('cbo_working_company_id*cbo_location_id*txt_fabric_type*txt_fabric_type_id*cbo_year*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title+'&report_type='+report_type;
		//alert(data);return;
		freeze_window(5);
		http.open("POST","requires/fabric_type_wise_knitting_production_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;

	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			var response=trim(http.responseText).split("####");

			$("#report_container2").html(response[0]);
			//alert (response[2]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:155px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

			if(response[2]==1)
			{
				setFilterGrid("table_body",-1,tableFilters);
			}

	 		show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#scroll_body tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../css/style_print.css" type="text/css"  /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="350px";
		$('#scroll_body tr:first').show();
	}

	function openmypage_fabric_type()
    {
        var workingCompanyID = $("#cbo_working_company_id").val();

        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/fabric_type_wise_knitting_production_report_controller.php?action=fabric_type_popup&workingCompanyID='+workingCompanyID, 'Fabric Type Details', 'width=410px,height=420px,center=1,resize=0,scrolling=0','../');

        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
            var fabric_type_des=this.contentDoc.getElementById("hidden_fabric_type").value; //Access form field with id="emailfield"
            var fabric_type_id=this.contentDoc.getElementById("hidden_fabric_type_id").value;
            $("#txt_fabric_type").val(fabric_type_des);
            $("#txt_fabric_type_id").val(fabric_type_id);
        }
    }

</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;" >
	<div style="margin-right: 250px;">
		<? echo load_freeze_divs ("../../",''); ?>
	</div>

		<form name="dailyYarnStatusReport_1" id="dailyYarnStatusReport_1">
			<h3 style="width:800px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')"> -Search Panel</h3>
			<div id="content_search_panel" >
				<fieldset style="width:760px;">
					<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
 							<th class="must_entry_caption" width="150">Working Company</th>
 							<th width="150">Working Location</th>
                            <th width="150">Fabric Type</th>
                            <th width="100">Year</th>
                            <th class="must_entry_caption" width="100" colspan="2">Production Date</th>
                            <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('dailyYarnStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:70px" /></th>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td id="td_working_company">
                                    <?
                                        echo create_drop_down( "cbo_working_company_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                                    ?>
                                </td>
                      			<td id="location_td">
									<?
										echo create_drop_down( "cbo_location_id", 150, $blank_array,"", 1, "-- Select --", $selected, "",1,"" );
		                            ?>
		                        </td>
                                <td align="center">
									<input type="text" id="txt_fabric_type" name="txt_fabric_type" class="text_boxes" style="width:150px" value="" onDblClick="openmypage_fabric_type();" placeholder="Browse" readonly />
									<input type="hidden" id="txt_fabric_type_id" name="txt_fabric_type_id" class="text_boxes" style="width:70px" value=""  />
                            	</td>
                                <td>
								<?
                                    echo create_drop_down( "cbo_year", 100, create_year_array(),"", 1,"-- All --", "", "",0,"" );
                                ?>
                                </td>
                                <td width="60" align="center">
                                     <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date"/>
                                </td>
                                <td width="60">
                                     <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date"/>
                                </td>
                                <td>
                                	<input type="button" id="show_button1" class="formbutton" style="width:100px; " value="Show" onClick="fn_report_generated(1)" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="7" align="center"><? echo load_month_buttons(1); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
            <br>
		</form>
	</div>
    <div id="report_container" align="center" style="padding-bottom: 10px; padding-right: 300px;"></div>
    <div id="report_container2" align="left"></div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>

    set_multiselect('cbo_working_company_id*cbo_location_id','0*0','0*0','0*0','0*0');
	setTimeout[($("#td_working_company a").attr("onclick","disappear_list(cbo_working_company_id,'0');load_getLocation();") ,3000)];

    function load_getLocation()
	{
		var working_company_id=$("#cbo_working_company_id").val();
        //alert(working_company_id);
        if(working_company_id !='') {
            var data="action=load_drop_down_location&choosenCompany="+working_company_id;
            http.open("POST","requires/fabric_type_wise_knitting_production_report_controller.php",true);
            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = function(){
                if(http.readyState == 4)
                {
                    var response = trim(http.responseText);
                    $('#location_td').html(response);
                    set_multiselect('cbo_location_id','0','0','0','0');
                }
            };
        }
	}


</script>
</html>
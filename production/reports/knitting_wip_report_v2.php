<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Knitting WIP Report V2

Functionality	:
JS Functions	:
Created by		:	Md. Abu Sayed
Creation date 	: 	06-02-2024
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
echo load_html_head_contents("Knitting WIP Report V2","../../", 1, 1, $unicode,1,0);

?>

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report(type)
	{
        var base_on = $("#search_type").val();

        var report_title=$( "div.form_caption" ).html();
        if(base_on==1)
        {
            if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false)
            {
                return;
            }
            else
            {
               
                var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_lc_location_name*cbo_source*cbo_party_name*txt_job_no*txt_date_from*txt_date_to*cbo_year*txt_smn_booking_no*search_type',"../../")+'&report_title='+report_title+'&type='+type;
             
                //alert(data);
                freeze_window(3);
                http.open("POST","requires/knitting_wip_report_v2_controller.php",true);
                http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                http.send(data);
                http.onreadystatechange = generate_report_reponse;
            }
        }
        else
        {
            if( form_validation('cbo_company_name*txt_date_from','Company Name*From Date')==false)
            {
                return;
            }
            else
            {
                if( form_validation('cbo_year','Year')==false)
                {
                    return;
                }
                else
                {
                    var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_lc_location_name*cbo_source*cbo_party_name*txt_job_no*txt_date_from*cbo_year*txt_smn_booking_no*search_type',"../../")+'&report_title='+report_title+'&type='+type;
                }

                //alert(data);
                freeze_window(3);
                http.open("POST","requires/knitting_wip_report_v2_controller.php",true);
                http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                http.send(data);
                http.onreadystatechange = generate_report_reponse;
            }
        }
		
	}

	function generate_report_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		//$("#table_body tr:first").hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="250px";
	}

    function search_populate(str)
    {
        if (str == 1)
        {
            document.getElementById('search_by_th_up').innerHTML = "Periodical";
            $('#search_by_th_up').css('color', 'blue');
            $("#txt_date_to").show();

        } else if (str == 2)
        {
            document.getElementById('search_by_th_up').innerHTML = "As On Date";
            $('#search_by_th_up').css('color', 'blue');
            $("#txt_date_to").hide();
        }

    }

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../../",$permission); ?><br />
    <form name="KnittingWIPReportV2_1" id="KnittingWIPReportV2_1" autocomplete="off" >
        <div style="width:100%;" align="center">
            <fieldset style="width:1330px;">
            <legend>Search Panel</legend>
                <table class="rpt_table" width="1330" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr>
                            <th width="140" class="must_entry_caption">LC Company</th>
                            <th width="130">Location</th>
                            <th width="100">Source</th>
                            <th width="100">Working Company</th>
                            <th width="80">Year</th>
                            <th width="80">Job No</th>
                            <th width="100">Sample Booking</th>
                            <th width="100">Based On</th>
                            <th id="search_by_th_up" class="must_entry_caption">Date Range</th>
                            <th width="100"><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" onClick="reset_form('KnittingWIPReportV2_1', 'report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td>
                            <?
                               echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down('requires/knitting_wip_report_v2_controller',this.value, 'load_drop_down_location', 'cbo_lc_location_td' );" );
                            ?>
                        </td>
                        <td id="cbo_lc_location_td">
                            <?
                               echo create_drop_down( "cbo_lc_location_name", 140, "$blank_array","", 1, "-- Select Location --", $selected, "" );
                            ?>
                        </td>
                        <td>
							<?
                                echo create_drop_down("cbo_source",130,$knitting_source,"", 1, "-- Select --", 0,"load_drop_down( 'requires/knitting_wip_report_v2_controller', this.value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_party','party_td');",0,'1,3');
                            ?>
                        </td>
                        <td id="party_td">
							<?
                                echo create_drop_down("cbo_party_name",130,$blank_array,"", 1, "-- Select Source --", 0,"",0,'');
                            ?>
                        </td>
                        <td id="extention_td">
                            <?
                                echo create_drop_down( "cbo_year", 80, create_year_array(),"", 1,"-- All --", "", "",0,"" );
                            ?>
                        </td>
                        <td>
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:100px" placeholder="Write" />
                        </td>
                        <td>
                            <input type="text" id="txt_smn_booking_no" name="txt_smn_booking_no" class="text_boxes" style="width:100px" placeholder="Write SMN Booking" />
                        </td>
                        <td>
                            <?
                            $search_type_arr = array(1 => "Periodical", 2 => "As on Date");
                            $fnc_name = "search_populate(this.value)";
                            echo create_drop_down("search_type", 100, $search_type_arr, "", 0, "-Select-", 0, $fnc_name, 0, "", "", "", "", "");
                            ?>
                        </td>
                        <td align="center">
                             <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date"/>
                             To
                             <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date"/>
                        </td>
                        <td>
                        	<input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:100px" class="formbutton" />
                        </td>
                    </tr>
                    <tr class="general">
                        <td colspan="10" align="center" width="95%"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </table>
            </fieldset>

            <div id="report_container" align="center" style="padding: 10px;"></div>
        	<div id="report_container2"></div>
        </div>
    </form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

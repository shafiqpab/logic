<?
/*-------------------------------------------- Comments
Purpose			: 	This form will Create Date Wise Grey Fabric Delivery to Store Report
Functionality	:
JS Functions	:
Created by		:	Kaiyum
Creation date 	: 	17-01-2018
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
//echo load_html_head_contents("Date Wise Grey Fabric Delivery to Store Report", "../../", 1, 1,'','','');
echo load_html_head_contents("Date Wise Grey Fabric Delivery to Store Report","../../", 1, 1, $unicode,1,1);

?>
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission='<? echo $permission; ?>';

	function fn_report_generated(report_type)
	{

		if (report_type==1)
		{
			if($('#cbo_company_name').val()==0)
			{
				var data='cbo_working_company_id*txt_date_from';
				var filed='Working Company Name*From Date';
			}
			else if($('#cbo_company_name').val()==0 || $('#txt_date_from').val() == '')
			{
				var data='cbo_company_name*txt_booking_no';
				var filed='Company Name*Booking No';
			}
			else
			{
				var data='cbo_company_name*txt_date_from';
				var filed='Company Name*From Date';
			}
		}
		else
		{
	        var txt_booking_no=document.getElementById('txt_booking_no').value;
			var txt_programme_no=document.getElementById('txt_programme_no').value;

	        if( txt_booking_no !="" || txt_programme_no != "" )
	        {
	        	var data='cbo_company_name';
				var filed='Company Name';
	        }
	        else
	        {
	        	var data='cbo_company_name*txt_date_from*txt_date_to';
				var filed='Company Name*From Date*To Date';
	        }
		}

		if( form_validation(data,filed)==false )
		{
			return;
		}
		else
		{
			var report_title=$( "div.form_caption" ).html();

			if (report_type==1)
			{
				var action="report_generate";
			}
			else
			{
				var action="report_generate2";
			}

			var data="action="+action+get_submitted_data_string('cbo_company_name*cbo_knitting_source*txt_date_from*txt_date_to*cbo_working_company_id*txt_booking_no*txt_floor_id*txt_programme_no*cbo_year_selection',"../../")+'&report_title='+report_title+'&report_type='+report_type;
			//alert(data);return;
			freeze_window(5);
			http.open("POST","requires/date_wise_grey_fabric_delivery_to_store_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}


	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			var response=trim(http.responseText).split("####");

			$("#report_container2").html(response[0]);
			//alert (response[0]);
			//document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:155px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
	 		show_msg('3');
			release_freezing();
		}
		// setFilterGrid("table_body", -1);
	}

	function new_window(type)
	{

		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		if(type!=1)
		{
		document.getElementById('scroll_body1').style.overflow="auto";
		document.getElementById('scroll_body1').style.maxHeight="none";
		}
		//alert(type);
 		//$("tr th:first-child").hide();
		//$("tr td:first-child").hide();
		//$("#summary_tab tr th:first-child").show();
		//$("#summary_tab tr td:first-child").show();

		//$("#fill_td th:first-child").show();

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="none";
		if(type!=1)
		{
		document.getElementById('scroll_body1').style.overflowY="scroll";
		document.getElementById('scroll_body1').style.maxHeight="none";

		}
		$("tr th:first-child").show();
		$("tr td:first-child").show();
	}



	function fn_disable_com(str){
		if(str==2){$("#cbo_company_name").attr('disabled','disabled');}
		else{ $('#cbo_company_name').removeAttr("disabled");}
		if(str==1){$("#cbo_working_company_id").attr('disabled','disabled');}
		else{ $('#cbo_working_company_id').removeAttr("disabled");}
	}

	function fn_disable_floor(str){
		if(str==3){$("#txt_floor_id").attr('disabled','disabled');}
		else{ $('#txt_floor_id').removeAttr("disabled");}
	}

	function generate_report(challan_no, company_id, mst_id, knitting_source,floor_name,location_id,knitting_company,remarks,attention,gReportId)
	{
		//alert(gReportId);

        if(gReportId==134)
        {

			var report_title = 'Delivery Challan';

            var data = company_id + '*' + challan_no + '*' + mst_id + '*' + report_title + '*' + knitting_source + '*' + floor_name + '*' + location_id;
            window.open("../../production/requires/grey_feb_delivery_roll_wise_entry_controller.php?data=" + data + '&action=grey_delivery_print', true);
        }
        else if(gReportId==135)
        {
			var report_title = 'Delivery Challan';

            var data = company_id + '*' + challan_no + '*' + mst_id + '*' + report_title + '*' + knitting_source + '*' + floor_name + '*' + location_id;
            window.open("../../production/requires/grey_feb_delivery_roll_wise_entry_controller.php?data=" + data + '&action=grey_delivery_print', true);
        }
		else if(gReportId==136)
        {
          	var report_title = 'Delivery Challan';

            var data = company_id + '*' + challan_no + '*' + mst_id + '*' + report_title + '*' + knitting_source + '*' + floor_name + '*' + 1;
            window.open("../../production/requires/grey_feb_delivery_roll_wise_entry_controller.php?data=" + data + '&action=grey_delivery_print3', true);
        }
		else if(gReportId==137)
        {
			var report_title = 'Delivery Challan';

			var data = company_id + '*' + challan_no + '*' + mst_id + '*' + report_title + '*' + knitting_source + '*' + floor_name + '*' + 1;
			window.open("../../production/requires/grey_feb_delivery_roll_wise_entry_controller.php?data=" + data + '&action=grey_delivery_print4', true);
        }
		else if(gReportId==138)
        {
            var report_title = 'Delivery Challan';

			var data = company_id + '*' + challan_no + '*' + mst_id + '*' + report_title + '*' + knitting_source + '*' + floor_name;
			window.open("../../production/requires/grey_feb_delivery_roll_wise_entry_controller.php?data=" + data + '&action=grey_delivery_print_machine', true);
        }
		else if(gReportId==139)
        {
			var report_title = 'Delivery Challan';

			var data = company_id + '*' + challan_no + '*' + mst_id + '*' + report_title + '*' + knitting_source + '*' + floor_name + '*' + remarks + '*' + attention;
			window.open("../../production/requires/grey_feb_delivery_roll_wise_entry_controller.php?data=" + data + '&action=grey_delivery_print_fabric_label', true);
        }
		else if(gReportId==161)
        {
            var report_title = 'Delivery Challan';

			var data = company_id + '*' + challan_no + '*' + mst_id + '*' + report_title + '*' + knitting_source + '*' + floor_name + '*' + knitting_company;
			window.open("../../production/requires/grey_feb_delivery_roll_wise_entry_controller.php?data=" + data + '&action=grey_delivery_print_11', true);
        }
		else if(gReportId==162)
        {
			var report_title = $("div.form_caption").html();
            generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_challan_no').val() + '*' + $('#update_id').val() + '*' + report_title + '*' + $('#cbo_knitting_source').val() + '*' + $('#txt_floor_name').val(), 'grey_delivery_print10');

            var report_title = 'Delivery Challan';

			var data = company_id + '*' + challan_no + '*' + mst_id + '*' + report_title + '*' + knitting_source + '*' + floor_name;
			window.open("../../production/requires/grey_feb_delivery_roll_wise_entry_controller.php?data=" + data + '&action=grey_delivery_print10', true);
        }
		else if(gReportId==191)
        {
			var report_title = 'Delivery Challan';
			var organ_print = 0;
            var data = company_id + '*' + challan_no + '*' + mst_id + '*' + report_title + '*' + knitting_source + '*' + floor_name + '*' + organ_print;
            window.open("../../production/requires/grey_feb_delivery_roll_wise_entry_controller.php?data=" + data + '&action=grey_delivery_print_7', true);
        }
		else if(gReportId==227)
        {
			var report_title = 'Delivery Challan';

			var data = company_id + '*' + challan_no + '*' + report_title + '*' + mst_id + '*' + knitting_source + '*' + floor_name + '*' + 1;
			window.open("../../production/requires/grey_feb_delivery_roll_wise_entry_controller.php?data=" + data + '&action=grey_delivery_print_13', true);
        }
		else if(gReportId==235)
        {
			var report_title = 'Delivery Challan';

			var data = company_id + '*' + challan_no + '*' + mst_id + '*' + report_title + '*' + knitting_source + '*' + floor_name + '*' + location_id;
			window.open("../../production/requires/grey_feb_delivery_roll_wise_entry_controller.php?data=" + data + '&action=grey_delivery_print9', true);
        }
		else if(gReportId==241)
        {
            var report_title = 'Delivery Challan';

			var data = company_id + '*' + challan_no + '*' + mst_id + '*' + report_title + '*' + knitting_source + '*' + floor_name + '*' + location_id;
			window.open("../../production/requires/grey_feb_delivery_roll_wise_entry_controller.php?data=" + data + '&action=grey_delivery_print11', true);
        }
		else if(gReportId==274)
        {
			var report_title = 'Delivery Challan';

			var data = company_id + '*' + challan_no + '*' + mst_id + '*' + report_title + '*' + knitting_source + '*' + floor_name + '*' + knitting_company+ '*' + location_id;
			window.open("../../production/requires/grey_feb_delivery_roll_wise_entry_controller.php?data=" + data + '&action=grey_delivery_print_15', true);
        }

        return;
    }

function openmypage_booking()
{
    if( form_validation('cbo_company_name','Company Name')==false )
    {
        return;
    }
    var companyID = $("#cbo_company_name").val();
    var cbo_year_id = $("#cbo_year_selection").val();
    var page_link='requires/date_wise_grey_fabric_delivery_to_store_report_controller.php?action=booking_no_popup&companyID='+companyID+'&cbo_year_id='+cbo_year_id;
    var title='Booking No Search';
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1010px,height=370px,center=1,resize=1,scrolling=0','../');
    emailwindow.onclose=function()
    {
        var theform=this.contentDoc.forms[0];
        var booking_no=this.contentDoc.getElementById("hide_booking_no").value;
        var booking_id=this.contentDoc.getElementById("hide_booking_id").value;
        $('#txt_booking_no').val(booking_no);
        $('#txt_hide_booking_id').val(booking_id);
    }
}

function openmypage_floor()
{
	if( form_validation('cbo_working_company_id','Working Company Name')==false )
	{
		return;
	}
	var workingCompanyID = $("#cbo_working_company_id").val();
	var knitting_source = $("#cbo_knitting_source").val();
	if(knitting_source==3)
	{
		alert("Floor Popup Allowed Only Knitting Source Inhouse");
		return;
	}
	//alert(workingCompanyID);
	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/date_wise_grey_fabric_delivery_to_store_report_controller.php?action=floor_popup&workingCompanyID=' + workingCompanyID, 'Floor Details', 'width=410px,height=420px,center=1,resize=0,scrolling=0', '../');

	emailwindow.onclose = function() {
		var theform = this.contentDoc.forms[0]; //("search_order_frm"); //Access the form inside the modal window
		var floor_name = this.contentDoc.getElementById("hidden_floor_name").value; //Access form field with id="emailfield"
		var floor_id = this.contentDoc.getElementById("hidden_floor_id").value;
		$("#txt_floor_name").val(floor_name);
		$("#txt_floor_id").val(floor_id);

	}
}


</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",''); ?>
		 <form name="dateWiseFabDelvStoreReport_1" id="dateWiseFabDelvStoreReport_1">
         <h3 style="width:1000px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')"> -Search Panel</h3>
         <div id="content_search_panel" >
             <fieldset style="width:1100px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <th class="must_entry_caption" width="130">Company Name</th>
                            <th width="100">Knitting Source</th>
                            <th class="must_entry_caption" width="150">Working Company</th>
                            <th class="" width="100">Floor</th>
                            <th class="" width="100">Booking No</th>
                            <th class="" width="100">Programme No</th>
                            <th class="must_entry_caption" width="170" colspan="2">Delivery Date Range</th>
                            <th colspan="2"><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('dateWiseFabDelvStoreReport_1','report_container*report_container2','','','')" class="formbutton" style="width:70px" /></th>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td>
                                    <?
                                        echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                                    ?>
                                </td>
                                <td>
                                	<?
										echo create_drop_down("cbo_knitting_source",150,$knitting_source,"", 1, "-- All --", 0,"fn_disable_floor(this.value);",0,'1,3');

										/* echo create_drop_down("cbo_knitting_source",150,$knitting_source,"", 1, "-- All --", 0,"load_drop_down( 'requires/date_wise_grey_fabric_delivery_to_store_report_controller', this.value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_knitting_com','knitting_com');fn_disable_floor(this.value);",0,'1,3');
										 */
									?>
                                </td>
                                <td id="knitting_com" width="150" align="center">
			                        <?
			                           // echo create_drop_down( "cbo_working_company_id", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
									    echo create_drop_down( "cbo_working_company_id", 150, $blank_array,"", 1, "-- Select Company --", $selected, "" );
			                        ?>
                      			</td>
                      			<td id="floor_td" width="100" align="center">

								  <input type="text" id="txt_floor_name" name="txt_floor_name" class="text_boxes" style="width:100px" value="" onDblClick="openmypage_floor();" placeholder="Browse" readonly />

									<input type="hidden" id="txt_floor_id" name="txt_floor_id" class="text_boxes" style="width:70px" value="" />
			                        <?
			                           // echo create_drop_down( "cbo_working_company_id", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
									   // echo create_drop_down( "cbo_floor_id", 100, $blank_array,"", 1, "-- Select Floor --", $selected, "" );
			                        ?>
                      			</td>
								<td>
									<input type="text" id="txt_booking_no" name="txt_booking_no" class="text_boxes" style="width:80px" placeholder="Browse Or Write" onDblClick="openmypage_booking();"/>
									<input type="hidden" name="txt_hide_booking_id" id="txt_hide_booking_id" readonly>
								</td>
								<td>
									<input type="text" id="txt_programme_no" name="txt_programme_no" class="text_boxes" style="width:80px" placeholder="Write P. No" />
                                <td align="center">
                                     <input type="text" name="txt_date_from" id="txt_date_from" value=" <? echo date("d-m-Y", time() - 86400); ?>" class="datepicker" style="width:70px" placeholder="From Date"/>
                                </td>
                                <td>
                                     <input type="text" name="txt_date_to" id="txt_date_to" value=" <? echo date("d-m-Y", time() - 86400); ?>" class="datepicker" style="width:70px" placeholder="To Date"/>
                                </td>
                                <td>
                                	<input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated(1)" />
                                </td>
                                <td>
                                	<input type="button" id="show_button" class="formbutton" style="width:100px" value="Weight Level" onClick="fn_report_generated(2)" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="11" align="center"><? echo load_month_buttons(1); ?></td>

                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
            <div style="width:100%;margin-top:10px;">
                <!--<input type="button" value="Delivery Challan" name="generate" id="generate" class="formbutton" style="width:150px" onClick="generate_delivery_challan_report()"/>-->
            </div>
            <br>
		</form>
	</div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
</body>
<script>
	set_multiselect('cbo_company_name*cbo_working_company_id','0*0','0*0','0*0','0*0');

	$("#cbo_knitting_source").click(function(){load_getWorkingCompany();});

	function load_getWorkingCompany()
	{
		var knitting_source=$("#cbo_knitting_source").val();

        if(knitting_source !='')
		{
            var data="action=load_drop_down_knitting_com&knitting_source="+knitting_source;
            http.open("POST","requires/date_wise_grey_fabric_delivery_to_store_report_controller.php",true);
            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = function()
			{
				//alert(http.readyState);
                if(http.readyState == 4)
                {
					//alert(http.responseText);
                    var response = trim(http.responseText);
                    $('#knitting_com').html(response);
                    set_multiselect('cbo_working_company_id','0','0','0','0');
                }
            };
        }
	}

</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$('#txt_floor_id').val(0);
</script>
</html>
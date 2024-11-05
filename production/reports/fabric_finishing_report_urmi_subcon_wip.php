<?
/*-------------------------------------------- Comments

Purpose			: 	This Report will Create Fabric Finishing WIP Info
Functionality	:
JS Functions	:
Created by		:	Aziz
Creation date 	: 	25-09-2019
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
echo load_html_head_contents("Fabric Finishing Report", "../../", 1, 1,'','','');

?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission='<? echo $permission; ?>';

	var tableFilters =
	{
		col_30: "none",
		col_operation: {
			id: ["btg"],
			col: [12],
			operation: ["sum"],
			write_method: ["innerHTML"]
		}
	}

	function fn_dyeing_report_generated(type)
	{
		
		var cbo_buyer_name=$("#cbo_buyer_name").val();
		var batch_number_show=$("#batch_number_show").val();
		var cbo_machine_id=$("#cbo_machine_id").val();
		var cbo_floor_id=$("#cbo_floor_id").val();
		var txt_date_from=$("#txt_date_from").val();
		var txt_date_to=$("#txt_date_to").val();



		if(batch_number_show )
		{

		}
		else
		{
			if(form_validation('txt_date_from*txt_date_to','From date Fill*To date Fill')==false)
			{
				return;
			}

		}

		var report_title ="Fabric Finishing";
		freeze_window(5);
		//if(type==2)
		//{
		var data="action=report_generate2&type="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*batch_number*batch_number_show*cbo_type*cbo_year*txt_date_from*txt_date_to*cbo_floor_id*cbo_location_id*batch_extension',"../../")+'&report_title='+report_title;
		//}
		

		http.open("POST","requires/fabric_finishing_report_urmi_subcon_controller_wip.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_show_batch_report;
	}

	function fnc_show_batch_report()
	{
		if(http.readyState ==4)
		{
			var response=trim(http.responseText).split("****");
			$("#report_container2").html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:155px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

			release_freezing();
		}
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
	<!--BookingNumber -->
	function bookingnumber()
	{
		if(form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
		var company_name=document.getElementById('cbo_company_name').value;
		var batch_number=document.getElementById('batch_number_show').value;
		var page_link="requires/fabric_finishing_report_urmi_subcon_controller_wip.php?action=bookingnumbershow&company_name="+company_name;
		var title="Booking Number";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=920px,height=400px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			var batch=theemail.split("_");
			document.getElementById('booking_number').value=batch[0];
			document.getElementById('booking_number_show').value=batch[1];
			release_freezing();
		}
	}
	<!--BatchNumber -->
	function batchnumber()
	{
		if(form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
		var company_name=document.getElementById('cbo_company_name').value;
		var batch_number=document.getElementById('batch_number_show').value;
		var page_link="requires/fabric_finishing_report_urmi_subcon_controller_wip.php?action=batchnumbershow&company_name="+company_name;
		var title="Batch Number";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=400px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			var batch=theemail.split("_");
			document.getElementById('batch_number').value=batch[0];
			document.getElementById('batch_number_show').value=batch[1];
			document.getElementById('batch_extension').value=batch[2];
			release_freezing();
		}
	}

	function check_date(type)
	{
	 //var company_name=document.getElementById('cbo_company_name').value;
	 if(type==32)
	 {
	 	document.getElementById('th_date').innerHTML='Batch Date';
	 	$('#th_date').css('color','blue');
	 }
	 else if(type==30)
	 {
	 	document.getElementById('th_date').innerHTML='Unload End Date';
	 	$('#th_date').css('color','blue');
	 }
	 else  document.getElementById('th_date').innerHTML='Process End Date';
	 $('#th_date').css('color','blue');
	}


</script>
</head>
<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",''); ?>
		<form name="dailyYarnStatusReport_1" id="dailyYarnStatusReport_1">
			<h3 style="width:1200px; margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>  <div id="content_search_panel" >
				<fieldset style="width:1200px;">
					<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
						<thead>
							<th>Report Type</th>
							<th class="must_entry_caption">Working Company</th>
							<th>Location</th>
							<th>Floor</th>
							<th>Buyer</th>
							<th>Batch Year</th>
							
							<th>Batch No</th>

							<th class="must_entry_caption" id="th_date">Date</th>
							<th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('dailyYarnStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:50px" /></th>
						</thead>
						<tbody>
							<tr>
								<td>
                                <? // 9=>"Stentering";
                                $process_format=array(0=>"ALL", 33 => 'Heat Setting WIP',30 => 'Slitting/Squeezing WIP', 13 => 'Drying WIP' , 12 => "Stentering WIP", 14 => 'Compacting WIP',  15 => 'Brush WIP', 16 => 'Peach WIP');
                                echo create_drop_down( "cbo_type",120, $process_format,"",0, "", "","check_date(this.value)",0 );
                                ?>
                            </td>
                            <td>
                            	<?
                            	echo create_drop_down( "cbo_company_name", 120, "select id,company_name from lib_company where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down('requires/fabric_finishing_report_urmi_subcon_controller_wip', this.value, 'load_drop_down_location','location_td2');load_drop_down('requires/fabric_finishing_report_urmi_subcon_controller_wip', this.value, 'load_drop_down_buyer', 'cbo_buyer_name_td2');" );
                            	?>

                            </td>

                            <td id="location_td2">
                            	<?
                            	echo create_drop_down( "cbo_location_id", 110, $blank_array,"", 1, "-- Select Location --", 0, "" );
                            	?>
                            </td>

                            <td id="floor_td">
                            	<?
                            	echo create_drop_down( "cbo_floor_id", 110, $blank_array,"", 1, "-- Select Floor --", 0, "" );
                            	?>
                            </td>
                            <td id="cbo_buyer_name_td2">
                            	<?
                            	echo create_drop_down( "cbo_buyer_name", 110,$blank_array,"", 1, "-- Select Buyer --", 0, "" );
                            	?>
                            </td>
                            <td>
                            	<?
                            	echo create_drop_down( "cbo_year", 65, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
                            	?>
                            </td>

                           
                            <td>
                            	<input type="text"  name="batch_number_show" id="batch_number_show" readonly class="text_boxes" style="width:70px;" tabindex="1" placeholder="Browse" onDblClick="batchnumber();"  >
                            	<input type="hidden" name="batch_number" id="batch_number">
                            	<input type="hidden" name="batch_extension" id="batch_extension">
                            </td>
                            <td align="center">
                            	<input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:50px" placeholder="From Date"/>
                            	&nbsp;To&nbsp;
                            	<input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:50px" placeholder="To Date"/>
                            </td>
                            <td>
                            	<input type="button" id="show_button2" class="formbutton" style="width:50px" value="Show" onClick="fn_dyeing_report_generated(2)" />&nbsp;
                                <input type="button" id="show_button" class="formbutton" style="width:55px" value="Summary" onClick="fn_dyeing_report_generated(1)" />
                                
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
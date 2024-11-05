<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Plan Wise Yarn Issue Monitoring Report.
Functionality           :
JS Functions            :
Created by		:
Creation date           : 	18-07-2017
Updated by 		:
Update date		:
QC Performed BY         :
QC Date			:
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Plan Wise Yarn Issue Monitoring Report", "../../", 1, 1,$unicode,1,1);

?>

<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var permission = '<? echo $permission; ?>';

function fn_report_generated(type)
{
	var job_no = $("#txt_job_no").val();
	var booking_type = $("#cbo_booking_type").val();
	var booking_no = $("#txt_booking_no").val();
	var txt_program_no = $("#txt_program_no").val();
	var internalRef_no = $("#txt_internal_ref_no").val();
	var fso_no = $("#txt_fso_no").val();
	var txt_date_from = $("#txt_date_from").val();
	var txt_date_to = $("#txt_date_to").val();
	var search_type = $("#search_type").val();

	if (form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}

	if(type == 1 || type == 2)
	{
		if(booking_type >0)
		{
			alert("Booking Type apply only for Saler Order Button.");
			$("#cbo_booking_type").val(0);
			return;
		}
		if(fso_no !="")
		{
			alert("Sales Order No apply only for Saler Order Button.");
			$("#txt_fso_no").val('');
			return;
		}
		if(txt_date_from !="" && txt_date_to !="")
		{
			if(search_type==1)
			{
				alert("Please Select Date Criteria Booking Date.");
				return;
			}
		}
		if (job_no =="" && booking_no =="" && txt_program_no =="" && internalRef_no =="")
		{
			if (form_validation('txt_date_from*txt_date_to','From Date*End Date')==false)
			{
				return;
			}
		}
	}
	else if(type == 3)
	{
		if (job_no =="" && booking_no =="" && txt_program_no =="" && internalRef_no =="" && fso_no =="" )
		{
			if (form_validation('txt_date_from*txt_date_to','From Date*End Date')==false)
			{
				return;
			}
		}
	}


	if(type == 1)
	{
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_allocation_type*txt_job_no*hide_job_id*txt_booking_no*hide_booking_id*txt_date_from*txt_date_to*txt_internal_ref_no*txt_program_no',"../../")+'&report_type='+type;
	}
	else if(type == 2)
	{
		var data="action=report_generate_2"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_allocation_type*txt_job_no*hide_job_id*txt_booking_no*hide_booking_id*txt_date_from*txt_date_to*txt_internal_ref_no*txt_program_no',"../../")+'&report_type='+type;
	}
	else
	{
		var data="action=report_generate_sales"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_allocation_type*txt_job_no*hide_job_id*txt_booking_no*hide_booking_id*txt_date_from*txt_date_to*txt_internal_ref_no*txt_program_no*search_type*txt_fso_no*hiden_fso_id*cbo_booking_type',"../../")+'&report_type='+type;
	}

	freeze_window(3);
	http.open("POST","requires/plan_wise_yarn_issue_monitoring_report_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fn_report_generated_reponse;
}


function fn_report_generated_reponse()
{
 	if(http.readyState == 4)
	{
  		var response=trim(http.responseText).split("####");
		$('#report_container2').html(response[0]);
		document.getElementById('report_container').innerHTML='<a href="'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:155px"/></a>&nbsp;&nbsp;&nbsp;<input type="button" onClick="new_window()" value="Html Preview" name="Print" class="formbutton" style="width:100px"/>&nbsp;&nbsp;';
		show_msg('3');
		release_freezing();
 	}

}

function openmypage_booking()
{
	if(form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}
	var job_IDs = $("#hide_job_id").val();
	var companyID = $("#cbo_company_name").val();
	var page_link='requires/plan_wise_yarn_issue_monitoring_report_controller.php?action=booking_no_search_popup&companyID='+companyID+'&job_IDs='+job_IDs;
	var title='Order No Search';

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=390px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var booking_no=this.contentDoc.getElementById("selected_booking").value;
		//var order_id=this.contentDoc.getElementById("hide_order_id").value;

		$('#txt_booking_no').val(booking_no);
		//$('#hide_booking_id').val(order_id);
	}
}

function openmypage_fso()
{
	if(form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}

	var companyID = $("#cbo_company_name").val();
	var buyerID = $("#cbo_buyer_name").val();
	var page_link='requires/plan_wise_yarn_issue_monitoring_report_controller.php?action=fso_no_search_popup&companyID='+companyID+'&buyerID='+buyerID;
	var title='Order No Search';

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=390px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var fso_no=this.contentDoc.getElementById("hidden_fso_no").value;
		var fso_id=this.contentDoc.getElementById("hidden_fso_id").value;

		$('#txt_fso_no').val(fso_no);
		$('#hiden_fso_id').val(fso_id);
	}
}


function openmypage_job()
{
	if(form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}

	var companyID = $("#cbo_company_name").val();
    var cbo_year = $("#cbo_year").val();

	var page_link='requires/plan_wise_yarn_issue_monitoring_report_controller.php?action=job_no_search_popup&companyID='+companyID;
	var title='Job No Search';

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var job_no=this.contentDoc.getElementById("hide_job_no").value;
		var job_id=this.contentDoc.getElementById("hide_job_id").value;

		$('#txt_job_no').val(job_no);
		$('#hide_job_id').val(job_id);
	}
}


function generate_report(company_id,program_id)
{
	 print_report( company_id+'*'+program_id + '*' + '../../', "print", "../requires/yarn_requisition_entry_controller" ) ;
}

function new_window()
{
	document.getElementById('scroll_body').style.overflow="auto";
	document.getElementById('scroll_body').style.maxHeight="none";
	document.getElementById('scroll_body_dtls').style.overflow="auto";
	document.getElementById('scroll_body_dtls').style.maxHeight="none";

	//$("#tbl_list_search").find('input([name="check"])').hide();
	//$('input[type="checkbox"]').hide();

	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" type="text/css" href="../../css/style_common.css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	d.close();
	//$('input[type="checkbox"]').show();
	document.getElementById('scroll_body').style.overflowY="scroll";
	document.getElementById('scroll_body').style.maxHeight="330px";
	document.getElementById('scroll_body_dtls').style.overflowY="scroll";
	document.getElementById('scroll_body_dtls').style.maxHeight="330px";
}

function new_window___()
{
	document.getElementById('scroll_body').style.overflow="auto";
	document.getElementById('scroll_body').style.maxHeight="none";
	document.getElementById('scroll_body_dtls').style.overflow="auto";
	document.getElementById('scroll_body_dtls').style.maxHeight="none";

	//$("#tbl_list_search").find('input([name="check"])').hide();
	$('input[type="checkbox"]').hide();

	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" type="text/css" href="../../css/style_common.css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	d.close();
	$('input[type="checkbox"]').show();
	document.getElementById('scroll_body').style.overflowY="scroll";
	document.getElementById('scroll_body').style.maxHeight="330px";
	document.getElementById('scroll_body_dtls').style.overflowY="scroll";
	document.getElementById('scroll_body_dtls').style.maxHeight="330px";
}

function openmypage_allocation(data,action)
{
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/plan_wise_yarn_issue_monitoring_report_controller.php?data='+data+'&action='+action, 'Allocation Details', 'width=905px, height=400px,center=1,resize=0,scrolling=0','../');
}
function openmypage_program(data,action)
{
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/plan_wise_yarn_issue_monitoring_report_controller.php?data='+data+'&action='+action, 'Programs Details', 'width=1245px, height=400px,center=1,resize=0,scrolling=0','../');
}
function openmypage_requisition(data,action)
{
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/plan_wise_yarn_issue_monitoring_report_controller.php?data='+data+'&action='+action, 'Requisition Details', 'width=935px, height=400px,center=1,resize=0,scrolling=0','../');
}
function openmypage_issue(data,action)
{
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/plan_wise_yarn_issue_monitoring_report_controller.php?data='+data+'&action='+action, 'Issue Details', 'width=1185px, height=400px,center=1,resize=0,scrolling=0','../');
}

function openmypage_production(data,action)
{
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/plan_wise_yarn_issue_monitoring_report_controller.php?data='+data+'&action='+action, 'Production Details', 'width=1245px, height=400px,center=1,resize=0,scrolling=0','../');
}

function generate_worder_report(type,txt_booking_no,cbo_company_name,txt_order_no_id,cbo_fabric_natu,cbo_fabric_source,txt_job_no,id_approved_id,print_id,action,i)
{

	if(print_id==85 || print_id == 35)
	{
		var report_title='Partial Fabric Booking';
	}
	else if(print_id==269 || print_id==93 || print_id==370)
	{
		var report_title='Main Fabric Booking V2';
	}
	else if(print_id==174)
	{
		var report_title='Sample Fabric Booking -Without order';
	}
	else
	{
		var report_title='Budget Wise Fabric Booking';
	}

	var show_yarn_rate='';
	if(print_id==73 || print_id==2){
		var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
		if (r==true)
		{
			show_yarn_rate="1";
		}
		else
		{
			show_yarn_rate="0";
		}
	}
	if(print_id == 35)
	{
		if(action == 'print_booking_16' || action == 'print_booking_17' || action == 'print_booking_19' || action == 'print_booking_18')
		{
			var report_type=2;
			var data="action="+action+
			'&txt_booking_no='+"'"+txt_booking_no+"'"+
			'&cbo_company_name='+"'"+cbo_company_name+"'"+
			'&txt_order_no_id='+"'"+txt_order_no_id+"'"+
			'&cbo_fabric_natu='+"'"+cbo_fabric_natu+"'"+
			'&cbo_fabric_source='+"'"+cbo_fabric_source+"'"+
			'&id_approved_id='+"'"+id_approved_id+"'"+
			'&report_title='+report_title+
			'&txt_job_no='+"'"+txt_job_no+"'"+
			'&report_type='+"'"+report_type+"'"+
			'&show_yarn_rate='+"'"+show_yarn_rate+"'"+

			'&path=../';
		}
		else
		{
			var report_type=2;
			var data="action="+action+
			'&txt_booking_no='+"'"+txt_booking_no+"'"+
			'&cbo_company_name='+"'"+cbo_company_name+"'"+
			'&txt_order_no_id='+"'"+txt_order_no_id+"'"+
			'&cbo_fabric_natu='+"'"+cbo_fabric_natu+"'"+
			'&cbo_fabric_source='+"'"+cbo_fabric_source+"'"+
			'&id_approved_id='+"'"+id_approved_id+"'"+
			'&report_title='+report_title+
			'&txt_job_no='+"'"+txt_job_no+"'"+
			'&report_type='+"'"+report_type+"'"+
			'&show_yarn_rate='+"''"+

			'&path=../';
		}
	}
	else
	{
		var report_type=2;
		var data="action="+action+
		'&txt_booking_no='+"'"+txt_booking_no+"'"+
		'&cbo_company_name='+"'"+cbo_company_name+"'"+
		'&txt_order_no_id='+"'"+txt_order_no_id+"'"+
		'&cbo_fabric_natu='+"'"+cbo_fabric_natu+"'"+
		'&cbo_fabric_source='+"'"+cbo_fabric_source+"'"+
		'&id_approved_id='+"'"+id_approved_id+"'"+
		'&report_title='+report_title+
		'&txt_job_no='+"'"+txt_job_no+"'"+
		'&report_type='+"'"+report_type+"'"+
		'&show_yarn_rate='+"'"+show_yarn_rate+"'"+

		'&path=../';
	}

	//alert(type);
	
	freeze_window(5);
	// alert(print_id);
	if(type==1)
	{
		if(print_id==269)
		{
			http.open("POST","../../order/woven_order/requires/fabric_booking_urmi_controller.php",true);
		}
		else
		{
			http.open("POST","../../order/woven_order/requires/short_fabric_booking_controller.php",true);
		}

	}
	else if(type==2)
	{

		if(print_id==85 || print_id == 35)
		{
			http.open("POST","../../order/woven_order/requires/partial_fabric_booking_controller.php",true);
		}
		else if(print_id==2 || print_id==45 || print_id==53 || print_id==73 || print_id==93 || print_id==269 || print_id==719 || print_id==370)
		{
			http.open("POST","../../order/woven_order/requires/fabric_booking_urmi_controller.php",true);
		}
		else
		{
			http.open("POST","../../order/woven_order/requires/fabric_booking_controller.php",true);
		}
	}
	else if(type==4)
	{
		if(print_id==174)
		{
			http.open("POST","../../order/woven_order/requires/sample_booking_non_order_controller.php",true);
		}
	}
	else
	{
		http.open("POST","../../order/woven_order/requires/sample_booking_controller.php",true);
	}

	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = generate_fabric_report_reponse;
}

function generate_fabric_report_reponse()
{
	if(http.readyState == 4)
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title></head><body>'+http.responseText+'</body</html>');
		d.close();
		release_freezing();
	}

}

function func_qty_popup(job_no)
{
	var popup_width='370px';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/plan_wise_yarn_issue_monitoring_report_controller.php?job_no='+job_no+'&action=yarn_qty_popup', 'Yarn Allocation Balance popup', 'width='+popup_width+', height=350px,center=1,resize=1,scrolling=0','../');
}

function search_populate(str)
{
    if (str == 1)
    {
        document.getElementById('based_on_th_up').innerHTML = "FSO Date";
    }
    else if (str == 2)
    {
        document.getElementById('based_on_th_up').innerHTML = "Booking Date";
    }

}

function generate_sales_report(company_id, booking_id, booking_no, sales_job_no)
{
	// print_report( company_id+'*'+program_id, "print", "requires/knitting_status_report_sales_controller" ) ;
	//alert(company_id+'='+booking_id+'='+booking_no+'='+sales_job_no);
	var data = company_id + '*' + booking_id + '*' + booking_no + '*' + sales_job_no + '*' + $("div.form_caption").html();
	window.open("../../production/requires/fabric_sales_order_entry_controller.php?data=" + data + '&action=fabric_sales_order_print', true);

	return;
}
</script>
</head>
<body onLoad="set_hotkey();">
<form id="planWiseYarnIssueMonitor_1">
    <div style="width:100%;" align="center">

        <? echo load_freeze_divs ("../../",'');  ?>

         <h3 style="width:1610px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
         <div id="content_search_panel" >
         <fieldset style="width:1610px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th>Company Name</th>
                    <th>Buyer Name</th>
                    <th>Allocation Type</th>
                    <th>Job No</th>
                    <th>Booking Type</th>
                    <th>Booking No</th>
                    <th>FSO/Fab. Booking No</th>
                    <th>Knitting Plan</th>
                    <th>Internal Ref No</th>
					<th width="150">Date Criteria</th>
                    <th width="200" id="based_on_th_up" colspan="2">FSO Date</th>
                    <!-- <th class="must_entry_caption">Booking Date Range</th> -->
                </thead>
                <tbody>
                    <tr class="general" id="td_company">
                        <td>
                            <?
                                echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "- Select Company -", $selected , "load_drop_down( 'requires/plan_wise_yarn_issue_monitoring_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <?
                                echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-- Select Buyer --", $selected, "","","" );
                            ?>
                        </td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_allocation_type", 140, array(3=>'Fully Pending',1=>'Partial Pending',2=>'Fully Allocated'),"", 1, "-- Select --", $selected, "",0,"" );
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:130px" placeholder="Browse" onDblClick="openmypage_job();" autocomplete="off" readonly>
                            <input type="hidden" name="hide_job_id" id="hide_job_id" readonly>
                        </td>
                        <td>
                            <?
								$booking_type_arr = [1=>'Main Fabric Booking',2=>'Short Fabric Booking',3=>'Sample With Order',4=>'Sample Without Order'];
                                echo create_drop_down( "cbo_booking_type", 140, $booking_type_arr,"", 1, "-- All --", $selected, "",0,"" );
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:130px" placeholder="Browse" onDblClick="openmypage_booking();" onChange="$('#hide_booking_id').val('');" autocomplete="off" readonly>
                            <input type="hidden" name="hide_booking_no" id="hide_booking_id" readonly>
                        </td>
						<td>
                            <input type="text" name="txt_fso_no" id="txt_fso_no" class="text_boxes" style="width:130px" placeholder="Browse" onDblClick="openmypage_fso();" onChange="$('#hiden_fso_id').val('');" autocomplete="off" readonly>
                            <input type="hidden" name="hiden_fso_id" id="hiden_fso_id" readonly>
                        </td>
                         <td>
                            <input type="text" name="txt_program_no" id="txt_program_no" class="text_boxes" style="width:130px" placeholder="Write" autocomplete="off" >
                        </td>
                         <td>
                            <input type="text" name="txt_internal_ref_no" id="txt_internal_ref_no" class="text_boxes" style="width:130px" placeholder="Write" autocomplete="off" >
                        </td>
						<td >
                            <?
                               $search_type_arr = array(1 => "FSO Date", 2 => "Booking Date");
                               $fnc_name = "search_populate(this.value)";
                               echo create_drop_down("search_type", 150, $search_type_arr, "", 0, "-Select-", 0, $fnc_name, 0, "", "", "", "", "");
                            ?>
                        </td>
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:70px" placeholder="From Date"/>
                            &nbsp;
                            <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:70px" placeholder="To Date"/>
                        </td>

                    </tr>
                    <tr>
                        <td colspan="6" align="center"><? echo load_month_buttons(1); ?></td>
                        <td colspan="2" align="center">
                        	<input type="button" id="show_button" class="formbutton" style="width:60px" value="Show" onClick="fn_report_generated(1)" />
                        	<input type="button" id="show_button1" class="formbutton" style="width:60px" value="Show-2" onClick="fn_report_generated(2)" />
							<input type="button" id="show_button2" class="formbutton" style="width:75px" value="Sales Order" onClick="fn_report_generated(3)" />

                            <input type="reset" name="res" id="res" value="Reset" onClick="reset_form('planWiseYarnIssueMonitor_1','report_container*report_container2','','','')" class="formbutton" style="width:60px" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
    	</div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
 </form>
</body>

<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

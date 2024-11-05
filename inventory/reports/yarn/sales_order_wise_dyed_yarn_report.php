<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Job/Order Wise Dyed Yarn Report

Functionality	:
JS Functions	:
Created by		:	Fuad Shahriar
Creation date 	: 	20-03-2014
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
echo load_html_head_contents("Job/Order Wise Dyed Yarn Report","../../../", 1, 1, $unicode,1,1);
?>

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

function generate_report(type)
{
	if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	var txt_fso_no = $("#txt_fso_no").val().trim();
	var txt_yd_wo_no = $("#txt_yd_wo_no").val().trim();
	var txt_job_no = $("#txt_job_no").val().trim();
	var txt_style_no = $("#txt_style_no").val().trim();
	var txt_ir_no = $("#txt_ir_no").val().trim();
	if(txt_fso_no == "" && txt_yd_wo_no == "" && txt_job_no == "" && txt_style_no == "" && txt_ir_no == "")
	{
		if( form_validation('txt_date_from*txt_date_to','To Date*From Date')==false )
		{
			return;
		}
	}


	var report_title=$( "div.form_caption" ).html();
	var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_fso_no*hide_fso_id*txt_process_loss*txt_yd_wo_no*txt_date_from*txt_date_to*txt_job_no*txt_style_no*txt_ir_no',"../../../")+'&report_title='+report_title+'&type='+type;
	//alert (data);
	freeze_window(3);
	http.open("POST","requires/sales_order_wise_dyed_yarn_report_controller.php",true);
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

		show_msg('3');
		release_freezing();
	}
}

function new_window()
{
	document.getElementById('scroll_body').style.overflow="auto";
	document.getElementById('scroll_body').style.maxHeight="none";

	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	d.close();

	document.getElementById('scroll_body').style.overflowY="scroll";
	document.getElementById('scroll_body').style.maxHeight="380px";
}

function openmypage_fso()
{
	if(form_validation('cbo_company_name','Buyer Name')==false)
	{
		return;
	}

	var companyID = $("#cbo_company_name").val();
	var buyer_name = $("#cbo_buyer_name").val();
	var hide_fso_id = $("#hide_fso_id").val();
	var cbo_year = $("#cbo_year").val();
	var page_link='requires/sales_order_wise_dyed_yarn_report_controller.php?action=fso_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&hidden_fso_id='+hide_fso_id;
	var title='FSO No Search';

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=390px,center=1,resize=1,scrolling=0','../../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var fso_no=this.contentDoc.getElementById("hide_fso_no").value;
		var fso_id=this.contentDoc.getElementById("hide_fso_id").value;
		//alert (job_no);
		$('#txt_fso_no').val(fso_no);
		$('#hide_fso_id').val(fso_id);
	}
}

/*function fn_date_type(type_id)
{
	if(type_id==2)
	{
		$('#td_date').text("Trans. Date");
	}
	else
	{
		$('#td_date').text("Booking Date");
	}
}*/

/* function openmypage(job_no,booking_id,color,lot,action,trans_type)
{
	var companyID = $("#cbo_company_name").val();
	var popup_width='600px';
	var data_ref='requires/sales_order_wise_dyed_yarn_report_controller.php?companyID='+companyID+'&job_no='+job_no+'&booking_id='+booking_id+'&color='+color+'&lot='+lot+'&action='+action+'&trans_type='+trans_type;
	//alert(data_ref);
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', data_ref, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
} */

function openmypage(job_no,booking_id,item_id,dyeing_color,type,tittle,company_id)
{
	var popup_width='';
	if(type=="delivery_info_popup")
	{
		popup_width='560px';
	}
	if(type=="rcv_info_popup")
	{
		popup_width='560px';
	}

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sales_order_wise_dyed_yarn_report_controller.php?booking_id='+booking_id+'&job_no='+job_no+'&item_id='+item_id+'&dyeing_color='+dyeing_color+'&action='+type+'&company_id='+company_id, tittle, 'width='+popup_width+', height=420px, center=1, resize=0, scrolling=0', '../../');
}

function openmypageknitting(booking_id,type,tittle,company_id)
{
	var popup_width='';

	if(type=="iss_to_knit_info_popup")
	{
		popup_width='760px';
	}

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/sales_order_wise_dyed_yarn_report_controller.php?booking_id='+booking_id+'&action='+type+'&company_id='+company_id, tittle, 'width='+popup_width+', height=420px, center=1, resize=0, scrolling=0', '../../');
}



function generate_trim_report(action,booking_no,company_name,update_id)
{
		var show_comment='';
		var r=confirm("Press  \"Cancel\"  to hide  Comment\nPress  \"OK\"  to Show Comment");
		if (r==true)
		{
			show_comment="1";
		}
		else
		{
			show_comment="0";
		}

		var form_name="yarn_dyeing_wo_booking";
		var data="action="+action+"&form_name="+form_name+"&txt_booking_no="+booking_no+"&cbo_company_name="+company_name+"&update_id="+update_id+"&show_comment="+show_comment;

		http.open("POST","../../../order/woven_order/requires/yarn_dyeing_charge_booking_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_trim_report_reponse;
}

function generate_trim_report_reponse()
{
	if(http.readyState == 4)
	{
		//alert( http.responseText);return;
		var file_data=http.responseText.split('****');
		//$('#pdf_file_name').html(file_data[1]);
		$('#data_panel').html(file_data[0] );
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
		d.close();
	}
}


function generate_ydwos_report(txt_job_no, txt_job_id, cbo_company_name, update_id, txt_booking_no, txt_within_group, report_print_btn)
{
	var show_comment = '';
	var r = confirm("Press  \"Cancel\"  to hide  Rate\nPress  \"OK\"  to Show Rate");
	if (r == true) {
		show_comment = "1";
	}
	else {
		show_comment = "0";
	}

	var pathinfo = 1;
	
	if(report_print_btn==2) // Print B1
	{
		var report_title = 'yarn_dyeing_wo_sales_order';
		var data = 'action=' + 'generate_report' + '&txt_job_no='+"'"+txt_job_no+"'" + '&txt_job_id=' + txt_job_id + '&cbo_company_name=' + cbo_company_name + '&update_id=' + update_id + '&txt_booking_no=' + txt_booking_no + '&txt_within_group=' + txt_within_group + '&form_name=' + "'"+report_title + '&show_comment=' + "'"+show_comment + '&pathinfo=' + pathinfo;

		window.open("../../../order/woven_order/requires/yarn_dyeing_charge_booking_sales_controller.php?data=" + data + '&action=generate_report', true);
	}
	else if(report_print_btn==3) // Print B2
	{
		var report_title = 'yarn_dyeing_wo_sales_order';
		var data = 'action=' + 'generate_report2' + '&txt_job_no='+"'"+txt_job_no+"'" + '&txt_job_id=' + txt_job_id + '&cbo_company_name=' + cbo_company_name + '&update_id=' + update_id + '&txt_booking_no=' + txt_booking_no + '&txt_within_group=' + txt_within_group + '&form_name=' + "'"+report_title + '&show_comment=' + "'"+show_comment + '&pathinfo=' + pathinfo;

		window.open("../../../order/woven_order/requires/yarn_dyeing_charge_booking_sales_controller.php?data=" + data + '&action=generate_report2', true);
	}
	else if(report_print_btn==6) // Print B3
	{
		var report_title = 'yarn_dyeing_wo_sales_order';
		var data = 'action=' + 'generate_report3' + '&txt_job_no='+"'"+txt_job_no+"'" + '&txt_job_id=' + txt_job_id + '&cbo_company_name=' + cbo_company_name + '&update_id=' + update_id + '&txt_booking_no=' + txt_booking_no + '&txt_within_group=' + txt_within_group + '&form_name=' + "'"+report_title + '&show_comment=' + "'"+show_comment + '&pathinfo=' + pathinfo;

		window.open("../../../order/woven_order/requires/yarn_dyeing_charge_booking_sales_controller.php?data=" + data + '&action=generate_report3', true);
	}
}


</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission); ?>
    <form name="DyedYarnReport_1" id="DyedYarnReport_1" autocomplete="off" >
    <h3 align="left" id="accordion_h1" style="width:1130px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel">
            <fieldset style="width:960px;">
                <table class="rpt_table" width="950" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr>
                        	<th class="must_entry_caption">Company Name</th>
                            <th>Buyer Name</th>
                            <th>Fso No</th>
                            <th>Job No</th>
                            <th>IR/IB</th>
                            <th>Style No</th>
                            <th>YD WO No.</th>
                            <th>Process Loss %</th>
                            <th colspan="2" class="must_entry_caption">Booking Date</th>
                            <th colspan="2"><input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('DyedYarnReport_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr align="center">
                        <td>
                            <?
                                echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
								//load_drop_down( 'requires/sales_order_wise_dyed_yarn_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );
                            ?>
                        </td>
                        <td id="buyer_td">
							<?
                                echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- Select Buyer --", $selected, "",0,"" );
                            ?>
                        </td>
                        <td align="center">
                             <input type="text" name="txt_fso_no" id="txt_fso_no" class="text_boxes" style="width:100px" placeholder="Browse/Write" onDblClick="openmypage_fso()">
                             <input type="hidden" name="hide_fso_id" id="hide_fso_id" readonly>
                        </td>
						<td align="center">
                             <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:100px" value="" placeholder="Write">
                        </td>
						<td align="center">
                             <input type="text" name="txt_ir_no" id="txt_ir_no" class="text_boxes" style="width:100px" value="" placeholder="Write">
                        </td>
						<td align="center">
                             <input type="text" name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:100px" value="" placeholder="Write">
                        </td>
                        <td align="center">
                             <input type="text" name="txt_yd_wo_no" id="txt_yd_wo_no" class="text_boxes" style="width:80px" value="" placeholder="Write">
                        </td>
                        <td align="center">
                             <input type="text" name="txt_process_loss" id="txt_process_loss" class="text_boxes_numeric" style="width:60px" value="" maxlength="2" >
                        </td>
                        <td>
                             <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" >
                        </td>
                        <td>
                             <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date">
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:70px" class="formbutton" />
                        </td>
                    </tr>
                    <tfoot>
                        <tr align="center">
                            <td colspan="10" align="center"><? echo load_month_buttons(1);  ?></td>
                        </tr>
                    </tfoot>
                </table>
            </fieldset>
        </div>
    </form>
</div>
<div id="report_container" align="center"></div>
<div id="report_container2"></div>
<div style="display:none" id="data_panel"></div>
</body>
<script>
	set_multiselect('cbo_company_name','0','0','0','0');	
	$("#multi_select_cbo_company_name a").click(function(){load_getBuyer();});

	function load_getBuyer()
	{  
		var company=$("#cbo_company_name").val(); 		 
		
        if(company !='') {
            var data="action=load_drop_down_buyer&choosenCompany="+company;
            http.open("POST","requires/sales_order_wise_dyed_yarn_report_controller.php",true);
            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = function(){
                if(http.readyState == 4)
                {
                    var response = trim(http.responseText);
                    $('#buyer_td').html(response);
                }
            };
        }
	}

</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

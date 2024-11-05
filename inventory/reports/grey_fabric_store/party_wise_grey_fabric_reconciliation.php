<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Party Wise Grey Fabric Reconciliation

Functionality	:
JS Functions	:
Created by		:	Kausar
Creation date 	: 	17-06-2015
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
echo load_html_head_contents("Party Wise Grey Fabric Reconciliation","../../../", 1, 1, $unicode,1,0);

?>

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report(type)
	{
		if(type!=3)
		{
			if( form_validation('cbo_company_name*cbo_dyeing_source*txt_date_from*txt_date_to','Company Name*Source*From Date*To Date')==false)
			{
				return;
			}
		}
		else
		{
			if($("#txt_challan").val()=='')
			{
				if( form_validation('cbo_company_name*cbo_dyeing_source*txt_date_from*txt_date_to','Company Name*Source*From Date*To Date')==false)
				{
					return;
				}
			}
			else
			{
				if( form_validation('cbo_company_name*txt_challan','Company Name*Challan No')==false)
				{
					return;
				}
			}
		}
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_dyeing_source*txt_dyeing_com_id*txt_job_no*txt_challan*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title+'&type='+type;
		freeze_window(3);
		http.open("POST","requires/party_wise_grey_fabric_reconciliation_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;
	}

	function generate_report_job()
	{
		if( form_validation('cbo_company_name*cbo_dyeing_source','Company Name*Source')==false)//*txt_date_from*txt_date_to *From Date*To Date
		{
			return;
		}

		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate_job"+get_submitted_data_string('cbo_company_name*cbo_dyeing_source*txt_dyeing_com_id*txt_challan*txt_job_no*txt_job_id*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title;//+'&type='+type
		freeze_window(3);
		http.open("POST","requires/party_wise_grey_fabric_reconciliation_controller.php",true);
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
	// function new_window()
	// {
	// 	var w = window.open("Surprise", "#");
	// 	var d = w.document.open();
	// 	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	// '<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	// 	d.close();

	// }

	function openmypage_job()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_name").val();
		var cbo_dyeing_source = $("#cbo_dyeing_source").val();
		var txt_dyeing_com_id = $("#txt_dyeing_com_id").val();
		var page_link='requires/party_wise_grey_fabric_reconciliation_controller.php?action=job_no_popup&companyID='+companyID+'&cbo_dyeing_source='+cbo_dyeing_source+'&txt_dyeing_com_id='+txt_dyeing_com_id;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;

			$('#txt_job_no').val(job_no);
			$('#hide_job_id').val(job_id);
		}
	}

	function openmypage_party()
	{
		if( form_validation('cbo_company_name*cbo_dyeing_source','Company Name*Source')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_name").val();
		var cbo_dyeing_source = $("#cbo_dyeing_source").val();
		var txt_knit_comp_id = $("#txt_knit_comp_id").val();
		var page_link='requires/party_wise_grey_fabric_reconciliation_controller.php?action=party_popup&companyID='+companyID+'&cbo_dyeing_source='+cbo_dyeing_source+'&txt_knit_comp_id='+txt_knit_comp_id;
		var title='Party Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=430px,height=370px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var party_name=this.contentDoc.getElementById("hide_party_name").value;
			var party_id=this.contentDoc.getElementById("hide_party_id").value;

			$('#txt_dyeing_company').val(party_name);
			$('#txt_dyeing_com_id').val(party_id);
		}
	}

	function dyeing_company_val()
	{
		$('#txt_dyeing_company').val('');
		$('#txt_dyeing_com_id').val('');
	}


	function print_button_setting()
	{
		$('#data_panel').html('');
		get_php_form_data($('#cbo_company_name').val(),'print_button_variable_setting','requires/party_wise_grey_fabric_reconciliation_controller' );
	}

	function print_report_button_setting(report_ids)
	{
		var report_id=report_ids.split(",");
		for (var k=0; k<report_id.length; k++)
		{
			if(report_id[k]==23)
			{
				$('#data_panel').append( '<input type="button" name="search" id="search" value="Summary" onClick="generate_report(1)" style="width:100px" class="formbutton" />&nbsp;' );
			}
			if(report_id[k]==40)
			{
				$('#data_panel').append( '<input type="button" name="search" id="search" value="Party Wise" onClick="generate_report(2)" style="width:100px" class="formbutton" />&nbsp;' );
			}
			if(report_id[k]==41)
			{
				$('#data_panel').append( '<input type="button" name="search" id="search" value="Job Wise" onClick="generate_report_job()" style="width:100px" class="formbutton" />&nbsp;' );
			}
			if(report_id[k]==42)
			{
				$('#data_panel').append( '<input type="button" name="search" id="search" value="Challan Wise" onClick="generate_report(3)" style="width:100px;" class="formbutton" />&nbsp;' );
			}
			if(report_id[k]==43)
			{
				$('#data_panel').append( '<input type="button" name="search" id="search" value="Returnable" onClick="generate_report(4)" style="width:100px;" class="formbutton" />&nbsp;' );
			}
			if(report_id[k]==44)
			{
			$('#data_panel').append( '<input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" onClick=\'reset_form("PartyWiseGreyReconciliation_1", "report_container*report_container2","","","","");\' />' );
			}

		}
	}




</script>
</head>

<body onLoad="set_hotkey();print_button_setting();">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../../../",$permission); ?><br />
    <form name="PartyWiseGreyReconciliation_1" id="PartyWiseGreyReconciliation_1" autocomplete="off" >
        <div style="width:100%;" align="center">
            <fieldset style="width:810px;">
            <legend>Search Panel</legend>
                <table class="rpt_table" width="800" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr>
                            <th width="140" class="must_entry_caption">Company</th>
                            <th width="130">Source</th>
                            <th width="140">Party</th>
                            <th width="80">Challan</th>
                            <th width="80">Job</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tr align="center">
                        <td>
                            <?
                               echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "print_button_setting();" );
                            ?>
                        </td>
                        <td>
							<?
                                echo create_drop_down("cbo_dyeing_source",130,$knitting_source,"", 1, "-- Select Source --", 0,"dyeing_company_val();",0,'1,3');//load_drop_down( 'requires/party_wise_grey_fabric_reconciliation_controller', this.value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_dyeing_com','dyeing_com'); get_php_form_data(this.value+'_'+document.getElementById('cbo_company_name').value, 'eval_multi_select', 'requires/party_wise_grey_fabric_reconciliation_controller' );
                            ?>
                        </td>
                        <td id="dyeing_com">
							<?
                                //echo create_drop_down( "txt_dyeing_com_id", 140, $blank_array,"",0, "--Select Party--", 1, "" );
                            ?>
                            <input type="text" id="txt_dyeing_company" name="txt_dyeing_company" class="text_boxes" style="width:100px" onDblClick="openmypage_party();" placeholder="Browse Party" />
                            <input type="hidden" id="txt_dyeing_com_id" name="txt_dyeing_com_id" class="text_boxes" style="width:60px" />
                        </td>
                        <td>
                            <input type="text" id="txt_challan" name="txt_challan" class="text_boxes" style="width:80px" />
                        </td>
                        <td>
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:80px" onDblClick="openmypage_job();" placeholder="Browse Job" />
                            <input type="hidden" id="txt_job_id" name="txt_job_id" class="text_boxes" style="width:60px" />
                        </td>
                        <td align="center">
                             <input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date("01-m-Y"); ?>" class="datepicker" style="width:60px" placeholder="From Date"/>
                             To
                             <input type="text" name="txt_date_to" id="txt_date_to" value="<? echo date("d-m-Y"); ?>" class="datepicker" style="width:60px" placeholder="To Date"/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" align="center" width="95%"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </table>
                <div style="margin-top:10px" id="data_panel">
                </div>
                <input type="hidden" id="report_ids" name="report_ids"/>


            </fieldset>

            <div id="report_container" align="center"></div>
        	<div id="report_container2"></div>
        </div>
    </form>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

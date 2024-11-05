<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Order Wise Finich Fabric Stock Report

Functionality	:
JS Functions	:
Created by		:   Abdul Barik Tipu
Creation date 	:   03-04-2022 
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
echo load_html_head_contents("Finish Fabric Service Issue Receive Report","../../../", 1, 1, $unicode,1,1);
?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";


	function generate_report()
	{
        var wo_no=document.getElementById('txt_wo_no_show').value;
        if(wo_no != "")
        {
            if(form_validation('cbo_company_id','Company')==false)
            {
                return;
            }
        }
        else
        {
            if(form_validation('cbo_company_id*txt_date_from*txt_date_to','Company*From date*To date')==false)
            {
                return;
            }
        }
        
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+ get_submitted_data_string('cbo_company_id*cbo_service_company*txt_wo_no_show*txt_wo_no*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title;
		//alert (data);
		freeze_window(3);
		http.open("POST","requires/finish_fabric_service_issue_receive_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;
	}

	function generate_report_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("**");
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
		$('#table_body tr:first').hide();
		$('#table_body_id tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		$('#table_body tr:first').hide();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		$('#scroll_body tr:first').show();
	}

	function openmypage_wo()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		var cbo_year_id = $("#cbo_year_selection").val();
		//var cbo_month_id = $("#cbo_month").val();
		var page_link='requires/finish_fabric_service_issue_receive_report_controller.php?action=wo_no_popup&companyID='+companyID+'&cbo_year_id='+cbo_year_id;
		var title='Wo No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=390px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_wo_no").value;
			var job_id=this.contentDoc.getElementById("hide_wo_id").value;

			$('#txt_wo_no_show').val(job_no);
			$('#txt_wo_no').val(job_id);
		}
	}

	function openmypage_dtls(wo_no,action,type)
	{
		var company = $("#cbo_company_id").val();	
		var popup_width='547px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/finish_fabric_service_issue_receive_report_controller.php?wo_no='+wo_no+'&type='+type+'&company='+company+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=400px,center=1,resize=0,scrolling=0','../../');
	}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission); ?>
    <form name="serviceIssueRecv_1" id="serviceIssueRecv_1" autocomplete="off" >
    <h3 style="width:740px; margin-top:20px;" align="" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,
    'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:740px;">
                <table class="rpt_table" width="740" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr>
                            <th width="180" class="must_entry_caption">Company</th>
                            <th width="180">Service Company Name</th>                            
                            <th width="130">WO No</th>
                            <th width="150" class="must_entry_caption">Date</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('serviceIssueRecv_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr align="center" class="general">
                            <td>
                                <?
                                   echo create_drop_down( "cbo_company_id", 180, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                                ?>
                            </td>
                            <td>
                                <?
                                    echo create_drop_down( "cbo_service_company", 180, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "" );
                                ?>
                            </td>
                             <td>
                                <input type="text" id="txt_wo_no_show" name="txt_wo_no_show" class="text_boxes" style="width:130px" onDblClick="openmypage_wo()" placeholder="Write/Browse" />
                                <input type="hidden" id="txt_wo_no" name="txt_wo_no" class="text_boxes" style="width:130px" />
                            </td>

                            <td>
                                <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:50px;" placeholder="From"/>
                                <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:50px;" placeholder="To"/>
                            </td>
                            <td>
                                <input type="button" name="search" id="search" value="Show" onClick="generate_report()" style="width:70px" class="formbutton" />
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                    	<tr>
                    		<td colspan="6" align="center">
                    			<?  echo load_month_buttons(1); ?>
                    		</td>
                    	</tr>
                    </tfoot>
                </table>
            </fieldset>
        </div>

    </form>
</div>
	<div id="report_container" align="center"></div>
    <div id="report_container2" align="center"></div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

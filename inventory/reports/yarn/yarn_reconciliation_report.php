<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Party Wise Yarn Reconciliation

Functionality	:
JS Functions	:
Created by		:	Fuad Shahriar
Creation date 	: 	26-11-2013
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
echo load_html_head_contents("Party Wise Yarn Reconciliation","../../../", 1, 1, $unicode,1,0);

?>

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report(type)
	{
		if($("#txt_challan").val()=='' && $("#txt_job_no").val()=='')
		{
			if( form_validation('cbo_company_name*cbo_knitting_source*txt_date_from*txt_date_to','Company Name*Source*From Date*To Date')==false)
			{
				return;
			}
		}
		else
		{
			if($("#txt_job_no").val()=='')
			{
				if( form_validation('cbo_company_name*cbo_knitting_source','Company Name*Knitting Source')==false)
				{
					return;
				}
			}
			else
			{
				if( form_validation('cbo_company_name','Company Name')==false)
				{
					return;
				}
			}

		}

		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_knitting_source*txt_knitting_com_id*txt_job_no*txt_job_id*txt_challan*txt_date_from*txt_date_to*cbo_search_type*cbo_value_with*cbo_po_status',"../../../")+'&report_title='+report_title+'&type='+type;
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/yarn_reconciliation_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;
	}

	function generate_report_job(type)
	{
		if ($("#cbo_value_with").val()==0)
		{
			alert("Value Without 0 is not availabe for this report");
			return;
		}
		if( form_validation('cbo_company_name*cbo_knitting_source','Company Name*Source')==false)//*txt_date_from*txt_date_to *From Date*To Date
		{
			return;
		}

		var report_title=$( "div.form_caption" ).html();
		if(type==5){
		var data="action=report_generate_excel"+get_submitted_data_string('cbo_company_name*cbo_knitting_source*txt_knitting_com_id*txt_challan*txt_job_no*txt_job_id*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title;//+'&type='+type
		}
		else
		{
		var data="action=report_generate_job"+get_submitted_data_string('cbo_company_name*cbo_knitting_source*txt_knitting_com_id*txt_challan*txt_job_no*txt_job_id*txt_date_from*txt_date_to*txt_internal_ref',"../../../")+'&report_title='+report_title;//+'&type='+type
		}


		freeze_window(3);
		http.open("POST","requires/yarn_reconciliation_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;
	}



	function generate_report_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("####");

			if(reponse[0]==10)
			{
				$("#report_container2").html(reponse[1]);
				show_msg('3');release_freezing();return;

			}
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
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="250px";
	}

	function openmypage_job()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_name").val();
		var cbo_knitting_source = $("#cbo_knitting_source").val();
		var txt_knitting_com_id = $("#txt_knitting_com_id").val();
		var cbo_search_type = $("#cbo_search_type").val();
		if(cbo_search_type==1)
		{
			var page_link='requires/yarn_reconciliation_report_controller.php?action=job_no_popup&companyID='+companyID+'&cbo_knitting_source='+cbo_knitting_source+'&txt_knitting_com_id='+txt_knitting_com_id;
			var title='Job No Search';
		}
		else
		{
			var page_link='requires/yarn_reconciliation_report_controller.php?action=booking_no_popup&companyID='+companyID+'&cbo_knitting_source='+cbo_knitting_source+'&txt_knitting_com_id='+txt_knitting_com_id;
			var title='Booking No Search';
		}

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;

			$('#txt_job_no').val(job_no);
			$('#txt_job_id').val(job_id);
		}
	}

	function openmypage_party()
	{
		if( form_validation('cbo_company_name*cbo_knitting_source','Company Name* Knitting source')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_name").val();
		var cbo_knitting_source = $("#cbo_knitting_source").val();
		var txt_knit_comp_id = $("#txt_knit_comp_id").val();
		var page_link='requires/yarn_reconciliation_report_controller.php?action=party_popup&companyID='+companyID+'&cbo_knitting_source='+cbo_knitting_source+'&txt_knit_comp_id='+txt_knit_comp_id;
		var title='Party Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=430px,height=370px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var party_name=this.contentDoc.getElementById("hide_party_name").value;
			var party_id=this.contentDoc.getElementById("hide_party_id").value;

			$('#txt_knitting_company').val(party_name);
			$('#txt_knitting_com_id').val(party_id);
		}
	}

	function kniting_company_val()
	{
		$('#txt_knitting_company').val('');
		$('#txt_knitting_com_id').val('');
	}
	function fn_job_book_check()
	{
		var job_wo=$('#txt_job_no').val();
		if(job_wo=="")
		{
			$('#txt_job_id').val('');
		}
	}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../../../",$permission); ?><br />
    <form name="PartyWiseYarnReconciliation_1" id="PartyWiseYarnReconciliation_1" autocomplete="off" >
        <div style="width:100%;" align="center">
            <fieldset style="width:1310px;">
            <legend>Search Panel</legend>
                <table class="rpt_table" width="1300" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr>
                            <th width="140" class="must_entry_caption">Company</th>
                            <th width="130" class="must_entry_caption">Source</th>
                            <th width="140">Party</th>
                            <th width="80">Challan</th>
                            <th width="100">Search Type</th>
                            <th width="80">Job/Knit service work order</th>
                            <th width="100">Value</th>
                            <th width="100">Order Status</th>
                            <th class="must_entry_caption">Date</th>
                            <th width="200"> <input type="reset" name="res" id="res" value="Reset" style="width:160px" class="formbutton" onClick="reset_form('PartyWiseYarnReconciliation_1', 'report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td>
                            <?
                               echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                            ?>
                        </td>
                        <td>
							<?
                                echo create_drop_down("cbo_knitting_source",130,$knitting_source,"", 1, "-- Select Source --", 0,"kniting_company_val();",0,'1,3');
                            ?>
                        </td>
                        <td id="knitting_com">
                            <input type="text" id="txt_knitting_company" name="txt_knitting_company" class="text_boxes" style="width:100px" onDblClick="openmypage_party();" placeholder="Browse Party" />
                            <input type="hidden" id="txt_knitting_com_id" name="txt_knitting_com_id" class="text_boxes" style="width:60px" />
                        </td>
                        <td>
                            <input type="text" id="txt_challan" name="txt_challan" class="text_boxes_numeric" style="width:80px" />
                        </td>
                        <td>
                            <?
							$search_type_arr=array(1=>"Job",2=>"Service WO");
                            echo create_drop_down("cbo_search_type", 100, $search_type_arr, "", 0, "", 1, "", "", "", "", "", "");
                            ?>
                        </td>
                        <td>
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:80px" onDblClick="openmypage_job();" placeholder="Browse Job" onBlur="fn_job_book_check();" readonly />
                            <input type="hidden" id="txt_job_id" name="txt_job_id" style="width:60px" />
                        </td>
                        <td>
                            <?
                                $valueWithArr=array(0=>'Value Without 0',1=>'Value With 0');
                                echo create_drop_down( "cbo_value_with", 90, $valueWithArr,"",0,"",1,"","","");
                            ?>
                        </td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_po_status", 90, $row_status ,"",0,"",1,"","","");
                            ?>
                        </td>
                        <td align="center">
                             <input type="text" name="txt_date_from" id="txt_date_from" value="<? //echo date("01-m-Y"); ?>" class="datepicker" style="width:60px" placeholder="From Date"/>
                             To
                             <input type="text" name="txt_date_to" id="txt_date_to" value="<? //echo date("d-m-Y"); ?>" class="datepicker" style="width:60px" placeholder="To Date"/>
                        </td>
                         <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:80px" class="formbutton" />
							<input type="button" name="btn_show2" id="btn_show2" value="Show 2" onClick="generate_report(2)" style="width:80px" class="formbutton" />
                        </td>
                    </tr>
                    <tr class="general">
                        <td colspan="10" align="center" width="95%"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </table>
                <input type="hidden" id="hidden_report_ids" name="hidden_report_ids"/>
            </fieldset>

            <div id="report_container" align="center"></div>
        	<div id="report_container2"></div>
        </div>
    </form>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

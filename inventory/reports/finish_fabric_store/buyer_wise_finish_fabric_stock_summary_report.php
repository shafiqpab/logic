<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Order Wise Finich Fabric Stock Report

Functionality	:
JS Functions	:
Created by		:
Creation date 	:
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
echo load_html_head_contents("Buyer Wise Finish Fabric Stock Summary Report","../../../", 1, 1, $unicode,1,1);

?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";	

	var tableFilters_3 =
	{
		col_operation: {
		id: ["value_opening_qnty_kg","value_opening_amount_kg","value_receive_kg","value_receive_amount_kg","value_issue_kg","value_issue_amount_kg","value_stock_qnty_kg","value_stock_amount_kg","value_opening_qnty_yds","value_opening_amount_yds","value_receive_yds","value_receive_amount_yds","value_issue_yds","value_issue_amount_yds","value_stock_qnty_yds","value_stock_amount_yds","value_opening_qnty_mtr","value_opening_amount_mtr","value_receive_mtr","value_receive_amount_mtr","value_issue_mtr","value_issue_amount_mtr","value_stock_qnty_mtr","value_stock_amount_mtr","value_opening_grand_total_value","value_receive_grand_total_value","value_issue_grand_total_value","value_closing_grand_total_value"],
		col: [4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	function generate_report(type)
	{
		var company_name=document.getElementById('cbo_company_id').value;
		var store_name=document.getElementById('cbo_store_name').value;
		var buyer_id=document.getElementById('cbo_buyer_id').value;
		var job_no=document.getElementById('txt_job_no').value;		
		var value_with=document.getElementById('cbo_value_with').value;		
		var from_date=document.getElementById('txt_date_from').value;
		var to_date=document.getElementById('txt_date_to').value;

		if(company_name==0)
		{			
			alert("Please Select Company");
			return;			
		}
		else if ((from_date=='' || to_date=='') && store_name==0 && buyer_id==0 && job_no=='') 
		{
			if( form_validation('txt_date_from*txt_date_to*cbo_store_name*cbo_buyer_id*txt_job_no*txt_book_no*txt_pi_no*cbo_pay_mode*cbo_supplier_id','Form Date*To Date*Store Name*Buyer*Job No*Booking No*PI No*Pay Mode*Supplier')==false )
			{
				return;
			}
		}
		
		var rtp_action = "report_generate";
		var report_title=$( "div.form_caption" ).html();
		var data="action="+ rtp_action + get_submitted_data_string('cbo_company_id*cbo_store_name*cbo_buyer_id*cbo_year*txt_job_no*txt_job_id*cbo_value_with*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title+'&cbo_report_type='+type;

		freeze_window(3);
		http.open("POST","requires/buyer_wise_finish_fabric_stock_summary_report_controller.php",true);
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
			if(typeof(reponse[1]) != 'undefined') {
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
				setFilterGrid("table_body",-1,tableFilters_3);
				show_msg('3');
			}

			release_freezing();
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		document.getElementById('scroll_body2').style.overflow="auto";
		document.getElementById('scroll_body2').style.maxHeight="none";

		$('#table_body tr:first').hide();
		$('#table_body2 tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		$('#table_body tr:first').show();
		document.getElementById('scroll_body2').style.overflowY="scroll";
		document.getElementById('scroll_body2').style.maxHeight="380px";
		$('#table_body2 tr:first').show();
	}

	function openmypage_job()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year_id = $("#cbo_year").val();
		var page_link='requires/buyer_wise_finish_fabric_stock_summary_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;

			$('#txt_job_no').val(job_no);
			$('#hide_job_id').val(job_id);
		}
	}

	function openmypage(cbo_company_id,cbo_store_name,buyer_id,job_no,job_year,date_from,date_to,buyer,client,uom,action)
	{
		var companyID = $("#cbo_company_id").val();
		var popup_width='600px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/buyer_wise_finish_fabric_stock_summary_report_controller.php?cbo_company_id='+cbo_company_id+'&cbo_store_name='+cbo_store_name+'&buyer_id='+buyer_id+'&job_no='+job_no+'&job_year='+job_year+'&date_from='+date_from+'&date_to='+date_to+'&buyer='+buyer+'&client='+client+'&uom='+uom+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}

	function getCompanyId() 
	{
	    var company_id = $("#cbo_company_id").val();
	    load_drop_down( 'requires/buyer_wise_finish_fabric_stock_summary_report_controller',company_id, 'load_drop_down_store', 'store_td' );
	    set_multiselect('cbo_store_name','0','0','','0');
	    load_drop_down( 'requires/buyer_wise_finish_fabric_stock_summary_report_controller',company_id, 'load_drop_down_buyer', 'buyer_td' );       
	}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission); ?>
    <form name="ordewisefinishfabricstock_1" id="ordewisefinishfabricstock_1" autocomplete="off" >
    <h3 style="width:850px; margin-top:20px;" align="" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:750px;">
                <table class="rpt_table" width="850" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr>
                            <th width="130" class="must_entry_caption">Company</th>
                            <th width="120">Store Name</th>
                            <th width="120">Buyer</th>
                            <th width="60">Job Year</th>
                            <th width="75">Job</th>
                            <th width="100">Value</th>
							<th class="must_entry_caption">Date Range</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('ordewisefinishfabricstock_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tbody>
                    <tr align="center">
                        <td id="company_td">
                            <?
                               echo create_drop_down( "cbo_company_id", 130, "SELECT comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/buyer_wise_finish_fabric_stock_summary_report_controller',this.value, 'load_drop_down_store', 'store_td' );load_drop_down( 'requires/buyer_wise_finish_fabric_stock_summary_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td id="store_td">
                            <?
                                echo create_drop_down( "cbo_store_name", 120, $blank_array,"", 1, "--Select Store--", "", "" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <?
                                echo create_drop_down( "cbo_buyer_id", 120, $blank_array,"", 1, "--Select Buyer--", 0, "",0 );
                           ?>
                        </td>
                        <td>
                            <?
								$selected_year=date("Y");
                                echo create_drop_down( "cbo_year", 60, $year,"", 1, "--Select Year--", $selected_year, "",0 );
                            ?>
                        </td>
                        <td>
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:70px" onDblClick="openmypage_job();" placeholder="Write/Browse" />
                            <input type="hidden" id="txt_job_id" name="txt_job_id" class="text_boxes" style="width:60px" />
                        </td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_value_with", 100, array(1=>'Value With 0',2=>'Value Without 0'),"", 0, "", 2, "",0 );
                           ?>
                        </td>
						<td>
							<input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date('d-m-Y'); ?>" class="datepicker" style="width:55px;" />
							To
							<input type="text" name="txt_date_to" id="txt_date_to" value="<? echo date('d-m-Y'); ?>" class="datepicker" style="width:55px;"/>
						</td>

                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(3)" style="width:70px" class="formbutton" />
                        </td>
                    </tr>
                	</tbody>
                	<tfoot>
						<tr>
							<td colspan="15" align="center"><? echo load_month_buttons(1);  ?></td>
						</tr>
                	</tfoot>
                </table>
            </fieldset>
        </div>
    </form>
</div>
	<div id="report_container" align="center"></div>
    <div id="report_container2"></div>
</body>
<script>
	set_multiselect('cbo_company_id*cbo_store_name','0*0','0*0','','0*0');
	setTimeout[($("#company_td a").attr("onclick","disappear_list(cbo_company_id,'0');getCompanyId();") ,3000)];
</script> 
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

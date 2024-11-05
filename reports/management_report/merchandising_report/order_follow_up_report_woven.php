<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This file for Woven Order Follow up Report .
Functionality	:
JS Functions	:
Created by		:	Md. Zakaria Joy
Creation date 	: 	03-11-2019
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
echo load_html_head_contents("Order Follow up Report","../../../", 1, 1, $unicode);
?>
<script>
	var permission = '<? echo $permission; ?>';

	function fn_report_generated(str)
	{
			var action='';

			if(str==1) {
				if($('#txt_job_no').val()!=''){
					var file="cbo_company_name";
					var message="Comapny";
				}
				else if($('#txt_style_ref').val()!=''){
					var file="cbo_company_name";
					var message="Comapny";
				}
				else if($('#txt_order_no').val()!=''){
					var file="cbo_company_name";
					var message="Comapny";
				}

				else if($('#txt_file_no').val()!=''){
					var file="cbo_company_name";
					var message="Comapny";
				}
				else if($('#txt_ref_no').val()!=''){
					var file="cbo_company_name";
					var message="Comapny";
				}

				else{
					var file="cbo_company_name*txt_date_from*txt_date_to";
					var message="Comapny*From Date*To Date";
				}

				if (form_validation(file,message)==false)
				{
					return;
				}
				action = 'report_generate';
			} else if(str == 2) {
				if ( !form_validation('cbo_company_name', 'Company') ) {
					return;
				}

				if($('#txt_job_no').val()!=''){
					var file="txt_job_no";
					var message="Job No";
				}
				else if($('#txt_style_ref').val()!=''){
					var file="txt_style_ref";
					var message="Style";
				}
				else if($('#txt_order_no').val()!=''){
					var file="txt_order_no";
					var message="Order No";
				}
				else if($('#txt_file_no').val()!=''){
					var file="txt_file_no";
					var message="File No";
				}
				else if($('#txt_ref_no').val()!=''){
					var file="txt_ref_no";
					var message="Ref No";
				}

				else{
					var file="cbo_company_name*txt_job_no";
					var message="Comapny*Job No";
				}

				if (form_validation(file,message)==false)
				{
					return;
				}

				action = 'report_generate_stylewise';
			}else if(str == 4) {
				var action='';

		 
				if($('#txt_job_no').val()!=''){
					var file="cbo_company_name";
					var message="Comapny";
				}
				else if($('#txt_style_ref').val()!=''){
					var file="cbo_company_name";
					var message="Comapny";
				}
				else if($('#txt_order_no').val()!=''){
					var file="cbo_company_name";
					var message="Comapny";
				}

				else if($('#txt_file_no').val()!=''){
					var file="cbo_company_name";
					var message="Comapny";
				}
				else if($('#txt_ref_no').val()!=''){
					var file="cbo_company_name";
					var message="Comapny";
				}

				else{
					var file="cbo_company_name*txt_date_from*txt_date_to";
					var message="Comapny*From Date*To Date";
				}

				if (form_validation(file,message)==false)
				{
					return;
				}

				action = 'report_generate_2';
			} else {
				action = 'report_generate_with_tna';
			}

			var data="action="+action+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*txt_style_ref*cbo_order_status*txt_order_no*cbo_date_type*txt_date_from*txt_date_to*cbo_agent_name*cbo_year*cbo_year_selection*cbo_ship_status*cbo_fabric_nature*cbo_fabric_source*txt_file_no*txt_ref_no*tna_task_id',"../../../");


			//alert(data);
			freeze_window();
			http.open("POST","requires/order_follow_up_report_woven_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("****");
			$('#data_panel').html( '<br><b>Convert To </b><a href="requires/' + reponse[1] + '" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>' );
			$('#data_panel').append( '&nbsp;&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>' );
			$('#report_container').html(reponse[0]);

			if(reponse[2]==2)
			{
				var tableFilters = {
					col_operation: {
					id: ["td_po_quantity_","td_gf_qnty_","td_gp_qty_","td_gp_to_qty_","td_daying_qnty_","td_ff_qnty_","td_fabrics_avl_qnty_","td_blance_","td_cut_qnty_","td_emb_issue_qnty_","td_emb_rec_qnty_","td_sewing_in_qnty_","td_sewing_out_qnty_","td_sewing_finish_qnty_","td_ex_qnty_"],
					col: [8,11,12,13,15,16,17,18,19,21,22,23,24,25,26],
					operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
					write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				}
			}if(reponse[2]==4)
			{
				
				var tableFilters = {
					col_operation: {
					id: ["tot_order_qnty","tot_order_val","tot_fab_req_qnty","tot_fab_req_val","tot_trims_req_qnty","tot_trims_req_val","tot_print_emb_req_qnty","tot_print_emb_req_val","tot_wash_req_qnty","tot_wash_req_val","tot_lab_test_val","tot_inspection_val","tot_cut_qnty","tot_emb_issue_qnty","tot_emb_recv_qnty","tot_print_issue_qnty","tot_print_recv_qnty","tot_sewing_in_qnty","tot_sewing_out_qnty","tot_wash_issue_qnty","tot_wash_recv_qnty","tot_packing_finish_qnty","tot_germents_deli_qnty","tot_short_access_qnty","tot_short_access_val"],
					col: [17,18,21,22,24,25,27,28,30,31,33,35,37,38,39,40,41,42,43,44,45,46,47,48,49],
					operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
					write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				}
			}
			else{
			var tableFilters = {
					col_operation: {
					id: ["td_po_quantity_","td_ff_qnty_","td_fabrics_avl_qnty_","td_blance_","td_lay_cut_qnty_","td_cut_qnty_","td_emb_issue_qnty_","td_emb_rec_qnty_","td_sewing_in_qnty_","td_sewing_out_qnty_","td_sewing_finish_qnty_","td_ex_qnty_"],
					col: [10,13,14,15,16,18,20,21,22,23,24,25],
					operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
					write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				}
			}


			setFilterGrid("report_tbl",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		document.getElementById('report_div').style.overflow="auto";
		document.getElementById('report_div').style.maxHeight="none";

		$("#report_tbl tr:first").hide();

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		d.close();

		document.getElementById('report_div').style.overflowY="scroll";
		document.getElementById('report_div').style.maxHeight="350px";

		$("#report_tbl tr:first").show();
	}

	function change_color(v_id,e_color)
	{
		if (document.getElementById(v_id).bgColor=="#33CC00")
		{
			document.getElementById(v_id).bgColor=e_color;
		}
		else
		{
			document.getElementById(v_id).bgColor="#33CC00";
		}
	}


	function date_fill_change(str)
	{
		if (str==1)
		{
			document.getElementById('search_date_td').innerHTML='Ship Date';
		}
		else if(str==2){
			document.getElementById('search_date_td').innerHTML='Original Ship Date';
		}
		else
		{
			document.getElementById('search_date_td').innerHTML='PO Insert Date';
		}
	}


function generate_fabric_report_reponse()
{
	if(http.readyState == 4)
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
		d.close();
	}
}

function openmypage_image(page_link,title)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../../')
		emailwindow.onclose=function()
		{
		}
	}


function openmypage_task()
{
	if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	var company = $("#cbo_company_name").val();
	var tna_task = $("#txt_taks_name").val();
	var tna_task_id = $("#tna_task_id").val();
	var tna_task_id_no = $("#tna_task_id_no").val();
	var page_link='../../../tna/report/requires/tna_report_controller.php?action=task_surch&company='+company+'&tna_task='+tna_task+'&tna_task_id='+tna_task_id+'&tna_task_id_no='+tna_task_id_no;
	var title="Search Task Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=420px,height=370px,center=1,resize=0,scrolling=0','../../../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
		var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
		var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
		//alert(style_des_no);
		$("#txt_taks_name").val(style_des);
		$("#tna_task_id").val(style_id);
		$("#tna_task_id_no").val(style_des_no);
	}
}

	 function print_report_button_setting(report_ids)
    {

        $('#show_button').hide();
        $('#show_button1').hide();
		$('#show_button2').hide();
		$('#show_button4').hide();
        var report_id=report_ids.split(",");
        report_id.forEach(function(items){
            if(items==108){$('#show_button').show();}
            else if(items==249){$('#show_button1').show();}
			else if(items==223){$('#show_button2').show();}
			else if(items==831){$('#show_button4').show();}
            });
    }


</script>
</head>

<body onLoad="set_hotkey();">
<form id="Price_Quotation_Statment">
    <div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../../");  ?>
        <h3 style="width:1510px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" >
            <fieldset style="width:1510px;">
                <table class="rpt_table" width="1510" cellpadding="1" cellspacing="0" rules="all" border="1">
                    <thead>
                        <th width="120" class="must_entry_caption">Company</th>
                        <th width="100">Task</th>
                        <th width="100">Buyer</th>
                        <th width="100">Agent Name</th>
                        <th width="50">Year</th>
                        <th width="60">Job</th>
                        <th width="60" id="search_text_td">Style Ref.</th>
                        <th width="60">Order</th>
                        <th width="60">File</th>
                        <th width="60">Int. Ref.</th>
                        <th width="80">Order Status</th>
                         <th width="70">Fabric Nature</th>
                        <th width="70">Fabric Source</th>

                        <th width="80">Ship Status</th>
                        <th width="100">Date Category</th>
                        <th id="search_date_td" width="145">Ship Date</th>
                        <th colspan="2" width="150"><input type="reset" id="reset_btn" class="formbutton" style="width:120px" value="Reset" /></th>
                    </thead>
                    <tr class="general">
                        <td>
							<?
                           	 echo create_drop_down( "cbo_company_name", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/order_follow_up_report_woven_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/order_follow_up_report_woven_controller', this.value, 'load_drop_down_agent', 'agent_td' );get_php_form_data(this.value,'print_button_variable_setting','requires/order_follow_up_report_woven_controller' );" );
                            ?>
                        </td>
                        <td align="center">
                        <input style="width:90px;"  name="txt_taks_name" id="txt_taks_name"  ondblclick="openmypage_task()"  class="text_boxes" placeholder="Browse" readonly/>
                        <input type="hidden" name="tna_task_id" id="tna_task_id"/>
                        <input type="hidden" name="tna_task_id_no" id="tna_task_id_no"/>
                 		</td>
                        <td id="buyer_td">
							<?
                            	echo create_drop_down( "cbo_buyer_name", 100, $blank_array,"", 1, "-- All --", $selected, "",1,"" );
                            ?>
                        </td>
                        <td id="agent_td">
							<?
                            	echo create_drop_down( "cbo_agent_name", 100, $blank_array,"", 1, "-- All --", $selected, "",1,"" );
                            ?>
                        </td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_year", 50, create_year_array(),"", 1,"-- All --", '', "",0,"" );
                            ?>
                        </td>
                        <td>
                        	<input type="text"  id="txt_job_no" class="text_boxes" style="width:60px">
                        </td>
                        <td>
                        	<input type="text"  id="txt_style_ref" class="text_boxes" style="width:60px">
                        </td>
                        <td>
                        	<input type="text"  id="txt_order_no" class="text_boxes" style="width:60px">
                        </td>
                        <td>
                        	<input type="text"  id="txt_file_no" class="text_boxes_numeric" style="width:60px">
                        </td>
                        <td>
                        	<input type="text"  id="txt_ref_no" class="text_boxes" style="width:60px">
                        </td>
                        <td>
							<? echo create_drop_down( "cbo_order_status", 80, $order_status, "", 1, "----All----",0, "",0,"" ); ?>
                        </td>
                         <td>
							<? echo create_drop_down( "cbo_fabric_nature",70, $item_category, "", 1, "----All----",0, "",0,"2,3" ); ?>
                        </td>
                        <td>
							<? echo create_drop_down( "cbo_fabric_source", 70, $fabric_source, "", 1, "----All----",0, "",0,"" ); ?>
                        </td>

                         <td>
							<? echo create_drop_down( "cbo_ship_status", 80, $shipment_status, "", 1, "----All----",0, "",0,"" ); ?>
                        </td>

                       	<td>
							<?
							$date_type_arr=array(1=>'Ship Date',2=>'Original Ship Date',3=>'PO Insert Date');
							echo create_drop_down( "cbo_date_type", 100, $date_type_arr, "", 1, "----Select----",1, "date_fill_change(this.value);",0,"" );
							?>
                        </td>

                        <td>
                           <?
							$current_date = date("d-m-Y",strtotime(add_time(date("H:i:s",time()),0)));
							$previous_date = date('d-m-Y', strtotime('-4 day', strtotime($current_date)));
						   ?>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:45px" placeholder="From Date" value="" >&nbsp; To
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:45px"  placeholder="To Date" value=""  ></td>
                        <td colspan="2">
                        	<input type="button" id="show_button" class="formbutton" style="width:50px;display:none;" value="Show" onClick="fn_report_generated(1)" />
							<input type="button" id="show_button4" class="formbutton" style="width:75px;" value="Style Wise 2" onClick="fn_report_generated(4)" />

				
                        	<input type="button" id="show_button2" class="formbutton" style="width:60px;" value="Style Wise" onClick="fn_report_generated(2)" />
                        </td>
					
                    </tr>
                    <tr>
                        <td colspan="17" align="center">
							<? echo load_month_buttons(1); ?>
                            <input type="button" id="show_button1" class="formbutton" style="width:120px;display:none;" value="Show With TNA" onClick="fn_report_generated(3)" />
                        </td>
                    </tr>
                </table>
            <br />
            </fieldset>
        </div>
    </div>
    <div id="data_panel" align="center"></div>
    <div id="report_container" align="center"></div>
</form>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
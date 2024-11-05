<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Date Wise Item Receive Issue Report

Functionality	:
JS Functions	:
Created by		:	Jahid
Creation date 	: 	24/03/2014
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
echo load_html_head_contents("Date Wise Item Receive Issue","../../", 1, 1, $unicode,1,1);
?>
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";


	var tableFilters =
	 {
		 //col_40: "none",
		 col_operation: {
			 id: ["value_total_receive","value_total_issue_return","value_total_trans_in","value_total_issue_bulkSewing","value_total_issue_sampleWithOrder","value_total_issue_sampleWithOutOrder","value_total_trans_out","value_total_rcv_return","value_total_issue_rePocess","value_total_issue_sales","value_total_issue_fabricTest","value_total_issue_scrapStore","value_total_issue_damage","value_total_issue_adjustment","value_total_issue_stolen","value_total_amounts"],
			 //col: [25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,41],
			 col: [24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,40],
			 operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			 write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		 }
	 }
	 var tableFilters11 =
	 {
		 col_80: "none",
		 col_operation: {
			 id: ["value_tot_rcv_qnty_kg","value_tot_iss_rtn_qnty_kg","value_tot_trans_in_qnty_kg","value_tot_rcv_qnty_yds","value_tot_iss_rtn_qnty_yds","value_tot_trans_in_qnty_yds","value_tot_rcv_qnty_mtr","value_tot_iss_rtn_qnty_mtr","value_tot_trans_in_qnty_mtr","value_tot_amt"],
			 col: [32,33,34,35,36,37,38,39,40,42],
			 operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			 write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		 }
	 }
	 var tableFilters12 =
	 {
		 col_80: "none",
		 col_operation: {
			 id: ["value_tot_rcv_qnty_kg","value_tot_iss_rtn_qnty_kg","value_tot_trans_in_qnty_kg","value_tot_rcv_qnty_yds","value_tot_iss_rtn_qnty_yds","value_tot_trans_in_qnty_yds","value_tot_rcv_qnty_mtr","value_tot_iss_rtn_qnty_mtr","value_tot_trans_in_qnty_mtr","value_tot_amt"],
			 col: [32,33,34,35,36,37,38,39,40,42],
			 operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			 write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		 }
	 }


	 var tableFilters2 =
	 {
		col_30: "none",
		col_operation: {
		id: ["value_total_receive","value_total_ret_receive","value_total_trans_in","value_total_issue","value_total_ret_issue","value_total_trans_out","value_total_amounts"],
	   col: [23,24,25,26,27,28,40],
	   operation: ["sum","sum","sum","sum","sum","sum","sum"],
	   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	 }

	var tableFilters7 =
	 {
		col_30: "none",
		col_operation: {
		id: ["value_total_issue"],
	   col: [18],
	   operation: ["sum"],
	   write_method: ["innerHTML"]
		}
	 }





	function openmypage_style()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();
		var buyer = $("#cbo_buyer_name").val();
		var txt_style_ref_no = $("#txt_style_ref_no").val();
		var txt_style_ref_id = $("#txt_style_ref_id").val();
		var txt_style_ref = $("#txt_style_ref").val();
		var cbo_year = $("#cbo_year").val();
		//var txt_style_ref_no = $("#txt_style_ref_no").val();
		var page_link='requires/date_wise_item_recv_issue_report_controller.php?action=style_refarence_surch&company='+company+'&buyer='+buyer+'&txt_style_ref_no='+txt_style_ref_no+'&txt_style_ref_id='+txt_style_ref_id+'&txt_style_ref='+txt_style_ref+'&cbo_year='+cbo_year;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=420px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var style_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_no);
			$("#txt_style_ref").val(style_des);
			$("#txt_style_ref_id").val(style_id);
			$("#txt_style_ref_no").val(style_no);
		}
	}


	function openmypage_order()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();
		var buyer = $("#cbo_buyer_name").val();
		var txt_order_id_no = $("#txt_order_id_no").val();
		var txt_order_id = $("#txt_order_id").val();
		var txt_order = $("#txt_order").val();
		var page_link='requires/date_wise_item_recv_issue_report_controller.php?action=order_surch&company='+company+'&buyer='+buyer+'&txt_order_id_no='+txt_order_id_no+'&txt_order_id='+txt_order_id+'&txt_order='+txt_order;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=520px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_des_no);
			$("#txt_order").val(style_des);
			$("#txt_order_id").val(style_id);
			$("#txt_order_id_no").val(style_des_no);
		}
	}



function showHide(type)
{
	if(type==1){$('#summary_button').show();}
	else{$('#summary_button').hide();}
}


function reset_field()
{
	reset_form('item_receive_issue_1','report_container2','','','','');
}
function job_order_per()
{
	var item_val=$('#cbo_item_cat').val();
	if((item_val==2)||(item_val==3)||(item_val==4)||(item_val==13)||(item_val==14))
	{
		$('#txt_style_ref').attr("disabled",false);
		$('#txt_order').attr("disabled",false);
		$('#cbo_buyer_name').attr("disabled",false);
		$('#cbo_order_type').attr("disabled",false);

	}

	else
	{
		$('#cbo_order_type').attr("disabled",true).val(0);
		$('#txt_style_ref').attr("disabled",true).val('');
		$('#txt_order').attr("disabled",true).val('');
		$('#cbo_buyer_name').attr("disabled",true).val('');

	}
	if(item_val==1)
	{
		$('#cbo_dyed_type').attr("disabled",false);
		//$('#show_textcbo_source').attr("disabled",false);
		$('#cbo_yarn_count').attr("disabled",false);
	}
	else
	{
		$('#cbo_dyed_type').attr("disabled",true).val('');
		//$('#show_textcbo_source').attr("disabled",true).val('');
		$('#cbo_yarn_count').attr("disabled",true).val('');
	}
	if(item_val==13 || item_val==2)
	{
		$('#cbo_knitting_source').attr("disabled",false);
		$('#cbo_search_id').attr("disabled",false);
		$('#txt_search_val').attr("disabled",false);
	}
	else
	{
		$('#cbo_knitting_source').attr("disabled",true).val(0);
		$('#cbo_search_id').attr("disabled",true).val('');
		$('#txt_search_val').attr("disabled",true).val('');
	}
	if(item_val==107)
	{ $('#txt_style_ref').attr("disabled",false).val('');
		$('#txt_order').attr("disabled",false).val('');
		
	}
}

// Supplier
function openmypage_supplier()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();	
		var txt_supplier_id_no = $("#txt_supplier_id_no").val();
		var txt_supplier_id = $("#txt_supplier_id").val();
		var txt_supplier = $("#txt_supplier").val();
		var page_link='requires/date_wise_item_recv_issue_report_controller.php?action=supplier_popup&company='+company+'&buyer='+buyer+'&txt_supplier_id_no='+txt_supplier_id_no+'&txt_supplier_id='+txt_supplier_id;  
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=420px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var supplier_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var supplier_name=this.contentDoc.getElementById("txt_selected").value; // product Description
			var supplier_id_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_des_no);
			$("#txt_supplier").val(supplier_name);
			$("#txt_supplier_id").val(supplier_id); 
			$("#txt_supplier_id_no").val(supplier_id_no);
		}
	}

function  generate_report(rptType)
{
	var cbo_item_cat = $("#cbo_item_cat").val();
	var txt_supplier_id = $("#txt_supplier_id").val();
	var cbo_company_name = $("#cbo_company_name").val();
	var cbo_style_owner = $("#cbo_style_owner").val();
	var cbo_buyer_name = $("#cbo_buyer_name").val();
	var txt_style_ref = $("#txt_style_ref").val();
	var txt_style_ref_id = $("#txt_style_ref_id").val();
	var txt_order = $("#txt_order").val();
	var txt_order_id = $("#txt_order_id").val();
	var txt_date_from = $("#txt_date_from").val();
	var txt_date_to = $("#txt_date_to").val();
	var cbo_dyed_type = $("#cbo_dyed_type").val();
	//var cbo_source = $("#cbo_source").val();
	var cbo_yarn_count = $("#cbo_yarn_count").val();
	var cbo_based_on = $("#cbo_based_on").val();
	var cbo_order_type = $("#cbo_order_type").val();
	var cbo_knitting_source = $("#cbo_knitting_source").val();
	var txt_search_val = $("#txt_search_val").val();
	var cbo_search_id = $("#cbo_search_id").val();
	var cbo_use_for = $("#cbo_use_for").val();
	var cbo_store_name = $("#cbo_store_name").val();
	var cbo_year = $("#cbo_year").val();
	//var fso_id = $("#fso_id").val();

	if(txt_style_ref!="" || txt_order!="" )
	{
		if( form_validation('cbo_company_name*cbo_item_cat','Company Name*Item Cetagory')==false )
		{
			return;
		}
	}
	else
	{
		if( form_validation('cbo_company_name*cbo_item_cat','Company Name*Item Cetagory')==false )
		{
			return;
		} else {

			if(cbo_company_name == 0 && cbo_style_owner ==0) {
				alert("Please Select either a company or a Style owner");
				return;
			}
			else if( form_validation('txt_date_from*txt_date_to','Form Date*To Date')==false )
			{
				return;
			}

		}
	}

	var fso_value = document.getElementById("fso_id");
	if (fso_value.checked == true){
		var fso_id=1;
	} else {
		var fso_id=0;
	}

	var show_booking_id = document.getElementById("show_booking");
	if (show_booking_id.checked == true){
		var show_booking=1;
	} else {
		var show_booking=0;
	}

	var dataString = "&cbo_item_cat="+cbo_item_cat+"&cbo_company_name="+cbo_company_name+"&cbo_buyer_name="+cbo_buyer_name+"&txt_style_ref="+txt_style_ref+"&txt_style_ref_id="+txt_style_ref_id+"&txt_order="+txt_order+"&txt_order_id="+txt_order_id+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to+"&cbo_dyed_type="+cbo_dyed_type+"&cbo_yarn_count="+cbo_yarn_count+"&cbo_based_on="+cbo_based_on+"&cbo_order_type="+cbo_order_type+"&cbo_knitting_source="+cbo_knitting_source+"&rptType="+rptType+"&cbo_search_id="+cbo_search_id+"&txt_search_val="+txt_search_val+"&cbo_style_owner="+cbo_style_owner+"&cbo_use_for="+cbo_use_for+"&cbo_store_name="+cbo_store_name+"&fso_id="+fso_id+"&show_booking="+show_booking+"&cbo_year="+cbo_year+"&txt_supplier_id="+txt_supplier_id;
	if(fso_id==1 && cbo_item_cat==2)
	{
		if(rptType==2 || rptType==3)
		{
			var data="action=generate_report_fso"+dataString;
		}
		else
		{
			alert("This Report Button Not Alllow");return;
		}

	}
	else
	{
		if(rptType==4){var data="action=generate_report_summary"+dataString;}
		else if(rptType==6){var data="action=generate_report_issue_return"+dataString;}
		else if(rptType==9){var data="action=generate_report_general"+dataString;}
		// else if(rptType==8){var data="action=generate_report_receive2"+dataString;}
		else{var data="action=generate_report"+dataString;}
	}

	freeze_window(5);
	http.open("POST","requires/date_wise_item_recv_issue_report_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = generate_report_reponse;
}

function  generate_report_for_excel(rptType)
{
	var cbo_item_cat = $("#cbo_item_cat").val();	
	var cbo_company_name = $("#cbo_company_name").val();
	var cbo_style_owner = $("#cbo_style_owner").val();
	var cbo_buyer_name = $("#cbo_buyer_name").val();
	var txt_style_ref = $("#txt_style_ref").val();
	var txt_style_ref_id = $("#txt_style_ref_id").val();
	var txt_order = $("#txt_order").val();
	var txt_order_id = $("#txt_order_id").val();
	var txt_date_from = $("#txt_date_from").val();
	var txt_date_to = $("#txt_date_to").val();
	var cbo_dyed_type = $("#cbo_dyed_type").val();
	//var cbo_source = $("#cbo_source").val();
	var cbo_yarn_count = $("#cbo_yarn_count").val();
	var cbo_based_on = $("#cbo_based_on").val();
	var cbo_order_type = $("#cbo_order_type").val();
	var cbo_knitting_source = $("#cbo_knitting_source").val();
	var txt_search_val = $("#txt_search_val").val();
	var cbo_search_id = $("#cbo_search_id").val();
	var cbo_use_for = $("#cbo_use_for").val();
	var cbo_store_name = $("#cbo_store_name").val();
	var cbo_year = $("#cbo_year").val();
	//var fso_id = $("#fso_id").val();

	if(txt_style_ref!="" || txt_order!="" )
	{
		if( form_validation('cbo_company_name*cbo_item_cat','Company Name*Item Cetagory')==false )
		{
			return;
		}
	}
	else
	{
		if( form_validation('cbo_company_name*cbo_item_cat','Company Name*Item Cetagory')==false )
		{
			return;
		} else {

			if(cbo_company_name == 0 && cbo_style_owner ==0) {
				alert("Please Select either a company or a Style owner");
				return;
			}
			else if( form_validation('txt_date_from*txt_date_to','Form Date*To Date')==false )
			{
				return;
			}

		}
	}

	var fso_value = document.getElementById("fso_id");
	if (fso_value.checked == true){
		var fso_id=1;
	} else {
		var fso_id=0;
	}

	var show_booking_id = document.getElementById("show_booking");
	if (show_booking_id.checked == true){
		var show_booking=1;
	} else {
		var show_booking=0;
	}

	var dataString = "&cbo_item_cat="+cbo_item_cat+"&cbo_company_name="+cbo_company_name+"&cbo_buyer_name="+cbo_buyer_name+"&txt_style_ref="+txt_style_ref+"&txt_style_ref_id="+txt_style_ref_id+"&txt_order="+txt_order+"&txt_order_id="+txt_order_id+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to+"&cbo_dyed_type="+cbo_dyed_type+"&cbo_yarn_count="+cbo_yarn_count+"&cbo_based_on="+cbo_based_on+"&cbo_order_type="+cbo_order_type+"&cbo_knitting_source="+cbo_knitting_source+"&rptType="+rptType+"&cbo_search_id="+cbo_search_id+"&txt_search_val="+txt_search_val+"&cbo_style_owner="+cbo_style_owner+"&cbo_use_for="+cbo_use_for+"&cbo_store_name="+cbo_store_name+"&fso_id="+fso_id+"&show_booking="+show_booking+"&cbo_year="+cbo_year;
	if(fso_id==1 && cbo_item_cat==2)
	{
		if(rptType==2 || rptType==3)
		{
			var data="action=generate_report_fso"+dataString;
		}
		else
		{
			alert("This Report Button Not Alllow");return;
		}

	}
	else
	{
		if(rptType==4){var data="action=generate_report_summary"+dataString;}
		else if(rptType==6){var data="action=generate_report_issue_return"+dataString;}
		else if(rptType==9){var data="action=generate_report_general"+dataString;}
		// else if(rptType==8){var data="action=generate_report_receive2"+dataString;}
		else{var data="action=generate_report"+dataString;}
	}

	freeze_window(5);
	http.open("POST","requires/date_wise_item_recv_issue_report_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = generate_report_reponse_for_excel;
}


	function print_preview_button(url)
	{
	    return '<input type="button" onclick="print_priview_html( \'report_container2\', \'scroll_body\',\'table_header_1\',\'report_table_footer\', 3, \'0\',\''+url+'\' )" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
	}
	function excel_preview_button(url)
	{
		return '<a href="requires/'+url+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/>';
	}

	function generate_report_reponse_for_excel()
{
	if(http.readyState == 4)
	{
		//alert(http.responseText);
		var reponse=trim(http.responseText).split("**");
		//alert(http.responseText);release_freezing();return;

		// $("#report_container2").html(reponse[0]);

		document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window();" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

		if(reponse[2]!=2)
        {
			append_report_checkbox('table_header_1',1);
        }

		if(reponse[2]==2 || reponse[2]==3)
		{
			//alert("hello");
			if(document.getElementById("fso_id").checked == true)
			{
				if(reponse[3]==2) setFilterGrid("table_body",-1,tableFilters11); else setFilterGrid("table_body",-1,tableFilters12);
			}
			else
			{
				if(reponse[2]==2 && (reponse[3]==1 || reponse[3]==3))
				{
					tableFilters =
					{
						col_40: "none",
						col_operation: {
							id: ["value_total_receive","value_total_issue_return","value_total_trans_in","value_total_issue_bulkSewing","value_total_issue_sampleWithOrder","value_total_issue_sampleWithOutOrder","value_total_trans_out","value_total_rcv_return","value_total_issue_rePocess","value_total_issue_sales","value_total_issue_fabricTest","value_total_issue_scrapStore","value_total_issue_damage","value_total_issue_adjustment","value_total_issue_stolen","value_total_amounts"],
							col: [25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,41],
							//col: [24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,40],
							operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
							write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
						}
					}
				}
				setFilterGrid("table_body",-1,tableFilters);
			}
		}
		else if(reponse[2]==13)
		{
			if (reponse[3]==2)
			{
				var fso_value = document.getElementById("fso_id");
				var show_booking = document.getElementById("show_booking");
				if (fso_value.checked == true){
					 var tableFilters2 =
					 {
						col_33: "none",
						col_operation: {
						id: ["value_total_receive","value_total_ret_receive","value_total_trans_in","value_total_issue","value_total_ret_issue","value_total_trans_out","value_total_amounts"],
					    col: [25,26,27,28,29,30,40],
					   operation: ["sum","sum","sum","sum","sum","sum","sum"],
					   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
						}
					 }
		  			setFilterGrid("table_body",-1,tableFilters2);
				}
				else if(show_booking.checked == true)
				{
					 var tableFilters2 =
					 {
						// col_31: "none",
						col_operation: {
						id: ["value_total_receive","value_total_ret_receive","value_total_trans_in","value_total_issue","value_total_ret_issue","value_total_trans_out","value_total_amounts"],
					   col: [24,25,26,27,28,29,41],
					   operation: ["sum","sum","sum","sum","sum","sum","sum"],
					   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
						}
					 }
					 setFilterGrid("table_body",-1,tableFilters2);

				}
				else {
					setFilterGrid("table_body",-1,tableFilters2);
				}
			}
			else if(reponse[3]==3)
			{

				var fso_value = document.getElementById("fso_id");
				var show_booking = document.getElementById("show_booking");
				if (fso_value.checked == true){

					 var tableFilters2 =
					 {
						col_36: "none",
						col_operation: {
						id: ["value_total_receive","value_total_ret_receive","value_total_trans_in","value_total_issue","value_total_ret_issue","value_total_trans_out","value_total_amounts"],
						col: [27,28,29,30,31,32,41],
						operation: ["sum","sum","sum","sum","sum","sum","sum"],
						write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
						}
					 }

		  			setFilterGrid("table_body",-1,tableFilters2);
				}
				else if(show_booking.checked == true)
				{
					//alert(reponse[3]);//release_freezing();return;
					 var tableFilters2 =
					 {
						// col_31: "none",
						col_operation: {
						id: ["value_total_receive","value_total_ret_receive","value_total_trans_in","value_total_issue","value_total_ret_issue","value_total_trans_out","value_total_amounts"],
					   col: [24,25,26,27,28,29,41],
					   operation: ["sum","sum","sum","sum","sum","sum","sum"],
					   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
						}
					 }
					 setFilterGrid("table_body",-1,tableFilters2);

				}
				else {

					var tableFilters3 =
					 {
						// col_31: "none",
						col_operation: {
						id: ["value_total_receive","value_total_ret_receive","value_total_trans_in","value_total_issue","value_total_ret_issue","value_total_trans_out","value_total_amounts"],
					   col: [24,25,26,27,28,29,41],
					   operation: ["sum","sum","sum","sum","sum","sum","sum"],
					   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
						}
					 }

					setFilterGrid("table_body",-1,tableFilters3);
				}
			}
			else if(reponse[3]==7)
			{
				//setFilterGrid("table_body",-1,tableFilters7);
			}
		}
		else if(reponse[2]==4)
		{
			//alert(reponse[3]);

			if(reponse[3]==1)
			{
				var tableFilters3 =
				 {
					col_30: "none",
					col_operation: {
					id: ["value_total_receive_qty","value_total_order_amt","value_total_issue_qty","value_total_amount"],
				   col: [20,22,23,26],
				   operation: ["sum","sum","sum","sum"],
				   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				 }
			}
			else if(reponse[3]==2)
			{

				var tableFilters3 =
				 {
					col_40: "none",
					col_operation: {
					id: ["value_total_receive_qty","val_tot_receive_ret_qty","value_total_order_amt","value_total_amount"],
				   col: [22,23,24,26],
				   operation: ["sum","sum","sum","sum"],
				   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				 }
			}
			else if(reponse[3]==8)
			{

				var tableFilters3 =
				 {
					col_40: "none",
					col_operation: {
					id: ["value_total_receive_qty","val_tot_receive_ret_qty","value_total_order_amt","value_total_amount"],
				   col: [25,26,27,29],
				   operation: ["sum","sum","sum","sum"],
				   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				 }
			}
			else if(reponse[3]==3)
			{
				var tableFilters3 =
				 {
					col_30: "none",
					col_operation: {
					id: ["value_total_issue_qty","val_tot_issue_ret_qty","value_total_amount"],
				   col: [18,19,21],
				   operation: ["sum","sum","sum"],
				   write_method: ["innerHTML","innerHTML","innerHTML"]
					}
				 }
			}

			setFilterGrid("table_body",-1,tableFilters3);
		}
		else if(reponse[2]==1)
		{

			if(reponse[3]==1)
			{
				//alert(reponse[3]);//release_freezing();return;//alert(reponse[3]);//release_freezing();return;
				var tableFilters4 =
				 {
					col_40: "none",
					col_operation: {
					id: ["value_total_receive","val_tot_receive_ret_qty","value_total_order_amt","value_total_issue","val_tot_issue_ret_qty","val_tot_issue_reject_qty","value_total_return","value_totla_amount"],
				   // col: [22,23,24,25,26,27,28,30],
				   col: [25,26,27,28,29,30,31,33],
				   operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
				   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				 }
			}
			else if(reponse[3]==2)
			{

				var tableFilters4 =
				 {
					col_30: "none",
					col_operation: {
					id: ["value_total_receive","val_tot_receive_ret_qty","value_total_order_amt","value_totla_amount"],
				    col: [21,22,23,25],
				    operation: ["sum","sum","sum","sum"],
				    write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				 }
			}
			else if(reponse[3]==3)
			{
				///alert(reponse[3]);//release_freezing();return;
				var tableFilters4 =
				 {
					col_30: "none",
					col_operation: {
					id: ["value_total_issue","val_tot_issue_ret_qty","val_tot_issue_reject_qty","value_total_return","value_totla_amount"],
				    //col: [22,23,25],
					col: [23,24,25,26,28],
				    operation: ["sum","sum","sum","sum","sum"],
				    write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				 }
			}
			else if(reponse[3]==4)
			{

				var tableFilters4 =
				 {
					col_30: "none",
					col_operation: {
					id: ["tot_iss_qty","tot_ret_qty"],
				    col: [6,7],
				    operation: ["sum","sum"],
				    write_method: ["innerHTML","innerHTML"]
					}
				 }
			}
			else if(reponse[3]==5)
			{
				//alert(reponse[3]);//release_freezing();return;

				var tableFilters4 =
				 {
					col_30: "none",
					col_operation: {
					id: ["value_total_issue","val_tot_issue_ret_qty","val_tot_issue_reject_qty","value_total_return","value_totla_amount"],
				    col: [19,20,21,22,24],
				    operation: ["sum","sum","sum","sum","sum"],
				    write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				 }
			}

			else if(reponse[3]==6)
			{
				var tableFilters4 =
				 {
					col_0: "none",
					col_operation: {
					id: ["value_tot_return_qnty","value_tot_reject_qnty"],
				    col: [18,19],
				    operation: ["sum","sum"],
				    write_method: ["innerHTML","innerHTML"]
					}
				 }
			}

			setFilterGrid("table_body",-1,tableFilters4);
		}
		else
		{
			//alert(reponse[3]);
			//alert(reponse[1]+"="+reponse[2]+"="+reponse[3]);//release_freezing();return;
			if(reponse[3]==1)
			{
				var tableFilters5 =
				 {
					col_30: "none",
					col_operation: {
					id: ["value_total_receive","value_total_order_amt","value_total_issue","value_total_amount"],
				    col: [18,19,20,22],
				   operation: ["sum","sum","sum","sum"],
				   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				 }
			}
			else if(reponse[3]==2)
			{
				//alert("hello");
				var tableFilters5 =
				{
					col_30: "none",
					col_operation: {
					id: ["value_total_receive","value_total_amount"],
				   col: [21,24],
				   operation: ["sum","sum"],
				   write_method: ["innerHTML","innerHTML"]
					}
				}
			}
			else if(reponse[3]==3)
			{
				//alert("hello");
				var tableFilters5 =
				 {
					col_30: "none",
					col_operation: {
					id: ["value_total_issue","value_total_amount"],
				   col: [18,20],
				   operation: ["sum","sum"],
				   write_method: ["innerHTML","innerHTML"]
					}
				 }
			}
			else if(reponse[3]==9)
			{
				alert(reponse[3]);
				var tableFilters5 =
				 {
					col_30: "none",
					col_operation: {
					id: ["value_total_receive","value_total_amount"],
				   col: [22,25],
				   operation: ["sum","sum"],
				   write_method: ["innerHTML","innerHTML"]
					}
				 }
			}

			setFilterGrid("table_body",-1,tableFilters5);
		}
		if(reponse[3]==1){
			document.getElementById('excel').click();
		}
        setFilterGrid("table_header_non_order_3",-1);
		release_freezing();
		show_msg('3');
		//document.getElementById('report_container').innerHTML=report_convert_button('../../');
	}
}

function generate_report_reponse()
{
	if(http.readyState == 4)
	{
		//alert(http.responseText);
		var reponse=trim(http.responseText).split("**");
		//alert(http.responseText);release_freezing();return;

		$("#report_container2").html(reponse[0]);

		document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window();" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

		if(reponse[2]!=2)
        {
			append_report_checkbox('table_header_1',1);
        }

		if(reponse[2]==2 || reponse[2]==3)
		{
			//alert("hello");
			if(document.getElementById("fso_id").checked == true)
			{
				if(reponse[3]==2) setFilterGrid("table_body",-1,tableFilters11); else setFilterGrid("table_body",-1,tableFilters12);
			}
			else
			{
				if(reponse[2]==2 && (reponse[3]==1 || reponse[3]==3))
				{
					tableFilters =
					{
						col_40: "none",
						col_operation: {
							id: ["value_total_receive","value_total_issue_return","value_total_trans_in","value_total_issue_bulkSewing","value_total_issue_sampleWithOrder","value_total_issue_sampleWithOutOrder","value_total_trans_out","value_total_rcv_return","value_total_issue_rePocess","value_total_issue_sales","value_total_issue_fabricTest","value_total_issue_scrapStore","value_total_issue_damage","value_total_issue_adjustment","value_total_issue_stolen","value_total_amounts"],
							col: [25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,41],
							//col: [24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,40],
							operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
							write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
						}
					}
				}
				setFilterGrid("table_body",-1,tableFilters);
			}
		}
		else if(reponse[2]==13)
		{
			if (reponse[3]==2)
			{
				var fso_value = document.getElementById("fso_id");
				var show_booking = document.getElementById("show_booking");
				if (fso_value.checked == true){
					 var tableFilters2 =
					 {
						col_33: "none",
						col_operation: {
						id: ["value_total_receive","value_total_ret_receive","value_total_trans_in","value_total_issue","value_total_ret_issue","value_total_trans_out","value_total_amounts"],
					    col: [25,26,27,28,29,30,40],
					   operation: ["sum","sum","sum","sum","sum","sum","sum"],
					   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
						}
					 }
		  			setFilterGrid("table_body",-1,tableFilters2);
				}
				else if(show_booking.checked == true)
				{
					 var tableFilters2 =
					 {
						// col_31: "none",
						col_operation: {
						id: ["value_total_receive","value_total_ret_receive","value_total_trans_in","value_total_issue","value_total_ret_issue","value_total_trans_out","value_total_amounts"],
					   col: [24,25,26,27,28,29,41],
					   operation: ["sum","sum","sum","sum","sum","sum","sum"],
					   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
						}
					 }
					 setFilterGrid("table_body",-1,tableFilters2);

				}
				else {
					setFilterGrid("table_body",-1,tableFilters2);
				}
			}
			else if(reponse[3]==3)
			{

				var fso_value = document.getElementById("fso_id");
				var show_booking = document.getElementById("show_booking");
				if (fso_value.checked == true){

					 var tableFilters2 =
					 {
						col_36: "none",
						col_operation: {
						id: ["value_total_receive","value_total_ret_receive","value_total_trans_in","value_total_issue","value_total_ret_issue","value_total_trans_out","value_total_amounts"],
						col: [27,28,29,30,31,32,41],
						operation: ["sum","sum","sum","sum","sum","sum","sum"],
						write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
						}
					 }

		  			setFilterGrid("table_body",-1,tableFilters2);
				}
				else if(show_booking.checked == true)
				{
					//alert(reponse[3]);//release_freezing();return;
					 var tableFilters2 =
					 {
						// col_31: "none",
						col_operation: {
						id: ["value_total_receive","value_total_ret_receive","value_total_trans_in","value_total_issue","value_total_ret_issue","value_total_trans_out","value_total_amounts"],
					   col: [24,25,26,27,28,29,41],
					   operation: ["sum","sum","sum","sum","sum","sum","sum"],
					   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
						}
					 }
					 setFilterGrid("table_body",-1,tableFilters2);

				}
				else {

					var tableFilters3 =
					 {
						// col_31: "none",
						col_operation: {
						id: ["value_total_receive","value_total_ret_receive","value_total_trans_in","value_total_issue","value_total_ret_issue","value_total_trans_out","value_total_amounts"],
					   col: [24,25,26,27,28,29,41],
					   operation: ["sum","sum","sum","sum","sum","sum","sum"],
					   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
						}
					 }

					setFilterGrid("table_body",-1,tableFilters3);
				}
			}
			else if(reponse[3]==7)
			{
				//setFilterGrid("table_body",-1,tableFilters7);
			}
		}
		else if(reponse[2]==4)
		{
			//alert(reponse[3]);

			if(reponse[3]==1)
			{
				var tableFilters3 =
				 {
					col_30: "none",
					col_operation: {
					id: ["value_total_receive_qty","value_total_order_amt","value_total_issue_qty","value_total_amount"],
				   col: [20,22,23,26],
				   operation: ["sum","sum","sum","sum"],
				   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				 }
			}
			else if(reponse[3]==2)
			{

				var tableFilters3 =
				 {
					col_40: "none",
					col_operation: {
					id: ["value_total_receive_qty","val_tot_receive_ret_qty","value_total_order_amt","value_total_amount"],
				   col: [22,23,24,26],
				   operation: ["sum","sum","sum","sum"],
				   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				 }
			}
			else if(reponse[3]==8)
			{

				var tableFilters3 =
				 {
					col_40: "none",
					col_operation: {
					id: ["value_total_receive_qty","val_tot_receive_ret_qty","value_total_order_amt","value_total_amount"],
				   col: [25,26,27,29],
				   operation: ["sum","sum","sum","sum"],
				   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				 }
			}
			else if(reponse[3]==3)
			{
				var tableFilters3 =
				 {
					col_30: "none",
					col_operation: {
					id: ["value_total_issue_qty","val_tot_issue_ret_qty","value_total_amount"],
				   col: [18,19,21],
				   operation: ["sum","sum","sum"],
				   write_method: ["innerHTML","innerHTML","innerHTML"]
					}
				 }
			}

			setFilterGrid("table_body",-1,tableFilters3);
		}
		else if(reponse[2]==1)
		{

			if(reponse[3]==1)
			{
				//alert(reponse[3]);//release_freezing();return;//alert(reponse[3]);//release_freezing();return;
				var tableFilters4 =
				 {
					col_40: "none",
					col_operation: {
					id: ["value_total_receive","val_tot_receive_ret_qty","value_total_order_amt","value_total_issue","val_tot_issue_ret_qty","val_tot_issue_reject_qty","value_total_return","value_totla_amount"],
				   // col: [22,23,24,25,26,27,28,30],
				   col: [25,26,27,28,29,30,31,33],
				   operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
				   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				 }
			}
			else if(reponse[3]==2)
			{

				var tableFilters4 =
				 {
					col_30: "none",
					col_operation: {
					id: ["value_total_receive","val_tot_receive_ret_qty","value_total_order_amt","value_totla_amount"],
				    col: [21,22,23,25],
				    operation: ["sum","sum","sum","sum"],
				    write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				 }
			}
			else if(reponse[3]==3)
			{
				///alert(reponse[3]);//release_freezing();return;
				var tableFilters4 =
				 {
					col_30: "none",
					col_operation: {
					id: ["value_total_issue","val_tot_issue_ret_qty","val_tot_issue_reject_qty","value_total_return","value_totla_amount"],
				    //col: [22,23,25],
					col: [23,24,25,26,28],
				    operation: ["sum","sum","sum","sum","sum"],
				    write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				 }
			}
			else if(reponse[3]==4)
			{

				var tableFilters4 =
				 {
					col_30: "none",
					col_operation: {
					id: ["tot_iss_qty","tot_ret_qty"],
				    col: [6,7],
				    operation: ["sum","sum"],
				    write_method: ["innerHTML","innerHTML"]
					}
				 }
			}
			else if(reponse[3]==5)
			{
				//alert(reponse[3]);//release_freezing();return;

				var tableFilters4 =
				 {
					col_30: "none",
					col_operation: {
					id: ["value_total_issue","val_tot_issue_ret_qty","val_tot_issue_reject_qty","value_total_return","value_totla_amount"],
				    col: [19,20,21,22,24],
				    operation: ["sum","sum","sum","sum","sum"],
				    write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				 }
			}

			else if(reponse[3]==6)
			{
				var tableFilters4 =
				 {
					col_0: "none",
					col_operation: {
					id: ["value_tot_return_qnty","value_tot_reject_qnty"],
				    col: [18,19],
				    operation: ["sum","sum"],
				    write_method: ["innerHTML","innerHTML"]
					}
				 }
			}

			setFilterGrid("table_body",-1,tableFilters4);
		}
		else
		{
			// alert(reponse[3]+'__'+ reponse[2]);
			//alert(reponse[1]+"="+reponse[2]+"="+reponse[3]);//release_freezing();return;
			if(reponse[3]==1)
			{
				var tableFilters5 =
				 {
					col_30: "none",
					col_operation: {
					id: ["value_total_receive","value_total_order_amt","value_total_issue","value_total_amount"],
				    col: [18,19,20,22],
				   operation: ["sum","sum","sum","sum"],
				   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				 }
			}
			else if(reponse[3]==2)
			{
				//alert("hello");
				var tableFilters5 =
				{
					col_30: "none",
					col_operation: {
					id: ["value_total_receive","value_total_amount"],
				   col: [21,25],
				   operation: ["sum","sum"],
				   write_method: ["innerHTML","innerHTML"]
					}
				}
			}
			else if(reponse[3]==3 && reponse[2]==101)
			{
				// alert("hello");
				var tableFilters5 =
				 {
					// col_30: "none",
					col_operation: {
					id: ["value_total_issue","value_total_amount"],
				   col: [25,28],
				   operation: ["sum","sum"],
				   write_method: ["innerHTML","innerHTML"]
					}
				 }
			}
			else if(reponse[3]==3)
			{
				// alert("hello3333333");
				var tableFilters5 =
				 {
					// col_30: "none",
					col_operation: {
					id: ["value_total_issue","value_total_amount"],
				   col: [19,22],
				   operation: ["sum","sum"],
				   write_method: ["innerHTML","innerHTML"]
					}
				 }
			}
			
			else if(reponse[3]==9)
			{
				// alert(reponse[3]);
				var tableFilters5 =
				 {
					col_30: "none",
					col_operation: {
					id: ["value_total_receive","value_total_amount"],
				   col: [22,25],
				   operation: ["sum","sum"],
				   write_method: ["innerHTML","innerHTML"]
					}
				 }
			}

			setFilterGrid("table_body",-1,tableFilters5);
		}
        setFilterGrid("table_header_non_order_3",-1);
		release_freezing();
		show_msg('3');
		//document.getElementById('report_container').innerHTML=report_convert_button('../../');
	}
}

	function new_window()
	{

		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#table_body tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="250px";
		$('#table_body tr:first').show();
	}


function fn_change_base(str)
{
	//alert(str);
	if(str==1)
	{
		$("#up_tr_date").html("");
		$("#up_tr_date").html("Transaction Date Range");
		$('#up_tr_date').attr('style','color:blue');
	}
	else
	{
		$("#up_tr_date").html("");
		$("#up_tr_date").html("Insert Date Range");
		$('#up_tr_date').attr('style','color:blue');
	}
}

function fn_change_base2(str)
{
	//alert(str);
	if(str==1)
	{
		$("#file_ref_td").html("");
		$("#file_ref_td").html("File No");

	}
	else
	{
		$("#file_ref_td").html("");
		$("#file_ref_td").html("Ref. No");

	}
}

function generate_trims_print_report(transaction_type,rec_issue_id,is_multi,company_id,entry_form,print_action)
{
	if(entry_form==24 || entry_form==25 || entry_form==49 || entry_form==73)
	{
		if(is_multi==0 && transaction_type==1)
		{
			var report_title="Trims Receive";
			print_report( company_id+'*'+rec_issue_id+'*'+report_title, print_action, "../trims_store/requires/trims_receive_entry_controller" ) ;
		}
		else if(is_multi==1 && transaction_type==1)
		{
			var report_title="Trims Receive Multi Ref.";
			print_report( company_id+'*'+rec_issue_id+'*'+report_title, print_action, "../trims_store/requires/trims_receive_multi_ref_entry_controller" );
		}
		else if(is_multi==3 && transaction_type==1)
		{
			var report_title="Trims Receive Entry Multi Ref V3";
			print_report( company_id+'*'+rec_issue_id+'*'+report_title, print_action, "../trims_store/requires/trims_receive_multi_ref_entry_v3_controller" );
		}
		else
		{
			if(transaction_type==2)
			{
				var report_title="Trims Issue";
				//alert(transaction_type);
				//generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_system_id').val()+'*'+report_title,'trims_issue_entry_print');
				print_report( company_id+'*'+rec_issue_id+'*'+report_title, "trims_issue_entry_print", "../trims_store/requires/trims_issue_entry_controller" );
			}
			else if(transaction_type==3)
			{
				var report_title="Trims Receive Return";
				print_report( company_id+'*'+rec_issue_id+'*'+report_title, "yarn_receive_return_print", "../trims_store/requires/trims_receive_rtn_controller" );
			}
			else if(transaction_type==4)
			{
				var report_title="Trims Issue Return";
				generate_report_file( company_id+'*'+rec_issue_id+'*'+report_title,'trims_issue_entry_print','../trims_store/requires/trims_issue_return_entry_controller');
			}
		}
	}
	else
	{

		if(transaction_type==1)
		{
			var report_title="General Trims Receive";
			//print_report( company_id+'*'+rec_issue_id+'*'+report_title, "trims_receive_entry_print", "../trims_store/requires/trims_receive_entry_controller" ) ;
			print_report( company_id+'*'+rec_issue_id+'*'+report_title, "general_item_receive_print", "../general_store/general_item_receive/requires/general_item_receive_controller" );
		}
		if(transaction_type==2)
		{
			var report_title="General Trims Issue";
			//alert(transaction_type);
			//generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_system_id').val()+'*'+report_title,'trims_issue_entry_print');
			print_report( company_id+'*'+rec_issue_id+'*'+report_title, "general_item_issue_print", "../general_store/general_item_issue/requires/general_item_issue_controller" );
		}
		else if(transaction_type==3)
		{
			var report_title="General Trims Receive Return";
			print_report( company_id+'*'+rec_issue_id+'*'+report_title, "general_item_receive_return_print", "../general_store/requires/general_item_receive_return_entry_controller" );
		}
		else if(transaction_type==4)
		{
			var report_title="General Trims Issue Return";
			generate_report_file( company_id+'*'+rec_issue_id+'*'+report_title,'general_item_issue_return_print','../general_store/requires/general_item_issue_return_controller');
		}
	}



	return;
}
function fso_check_item_fnc()
{

	// var fso_value = document.getElementById("fso_id");

	// if (fso_value.checked == true){
	// 	$("#cbo_item_cat").val(13);

	// } else {
	// 	alert(2);
	// }
}
function booking_check_item_fnc()
{

	var show_booking = document.getElementById("show_booking");

	if (show_booking.checked == true){
		show_booking.value=1;

	} else {
		show_booking.value=0;
	}
}
function showBooking()
{
	var cbo_item_cat=document.getElementById("cbo_item_cat").value;
	var show_booking = document.getElementById("show_booking");

	show_booking.checked=false;
	show_booking.value=0;
	if(cbo_item_cat!=13)
	{

		$("#show_booking_span").css("display","none");
	}
	else{

		$("#show_booking_span").css("display","inline-block");

		booking_check_item_fnc();

	}
}
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />
    <form name="item_receive_issue_1" id="item_receive_issue_1" autocomplete="off" >
    <h3 align="left" id="accordion_h1" style="width:1820px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    <div style="width:1880px;" align="center" id="content_search_panel">
        <fieldset style="width:1880px;">
                <table class="rpt_table" width="1880" cellpadding="0" cellspacing="0" rules="all">
                <thead>
                    <tr>
                        <th width="100" class="must_entry_caption">Companyn</th>
                        <th width="100" class="must_entry_caption">Item Category</th>
						<th width="100" >Supplier Name</th>
                        <th width="100" class="">Style Owner</th>
                        <th width="100">Buyer Name</th>
                        <th width="100">Store Name</th>
                        <th width="80">Dyed Type</th>
                        <th width="80">Count</th>
                        <th width="60">Job Year.</th>
                        <th width="60">Job No.</th>
                        <th width="60">Order No.</th>
                        <th width="70">Search By</th>
                        <th width="60" id="file_ref_td" >File No</th>
                        <th width="80">Based On</th>
                        <th width="170" id="up_tr_date" class="must_entry_caption">Transaction Date Range</th>
                        <th width="80">Order Type</th>
                        <th width="80">Grey Source</th>
                        <th width="80">Use For</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" onClick="reset_field()" />
                        <span style="float: left;">FSO <input type="checkbox" checked="checked" name="fso_id" id="fso_id" value="" onClick="fso_check_item_fnc()"></span>
                        <span style="float: left;display: none;margin-left: 10px;" id="show_booking_span">Show Booking <input type="checkbox"  name="show_booking" id="show_booking" value="0" onClick="booking_check_item_fnc()" ></span>
                        </th>
                    </tr>
                </thead>
                <tr class="general">
                    <td>
                        <?
                        	echo create_drop_down( "cbo_company_name", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/date_wise_item_recv_issue_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/date_wise_item_recv_issue_report_controller',this.value+'**'+document.getElementById('cbo_item_cat').value, 'load_drop_down_store', 'store_td' ); get_php_form_data( this.value, 'company_wise_report_button_setting','requires/date_wise_item_recv_issue_report_controller');" );
                        ?>
                    </td>
                    <td>
						<?
                        	echo create_drop_down( "cbo_item_cat", 100, $item_category,"", 1, "-- Select Item --", $selected, "job_order_per();showHide(this.value);showBooking();",0,"1,2,3,4,13,8,9,10,11,15,16,17,18,19,20,21,22,32,34,35,36,37,38,39,23,33,40,41,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,81,89,90,91,92,93,94,99,101,106,107" );
                        ?>
                    </td>
					<td align="center" id="supplier">
                        <input type="text" style="width:80px;"  name="txt_supplier" id="txt_supplier"  ondblclick="openmypage_supplier()"  class="text_boxes" placeholder="Browse"   />   
                        <input type="hidden" name="txt_supplier_id" id="txt_supplier_id" />
                        <input type="hidden" id="txt_supplier_id_no" name="txt_supplier_id_no" />    
                                     
                     </td>

                    <td>
                            <?
                        	echo create_drop_down( "cbo_style_owner", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Owner --", $selected, "" );
                        ?>
                    </td>
                    <td id="buyer_td"><?
                        	echo create_drop_down( "cbo_buyer_name", 100, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                        ?>
                    </td>
                    <td id="store_td"><?
                        	echo create_drop_down( "cbo_store_name", 100, $blank_array,"", 1, "-- All Store --", $selected, "",0,"" );
                        ?>
                    </td>
                     <td align="center">
						<?
                        $dyedType=array(0=>'All',1=>'Dyed Yarn',2=>'Non Dyed Yarn');
                        echo create_drop_down( "cbo_dyed_type", 80, $dyedType,"", 0, "--Select--", $selected, "", "","");
                        ?>
                     </td>
                    <td>
						<?
                        echo create_drop_down( "cbo_yarn_count", 80, "select id,yarn_count from lib_yarn_count where is_deleted = 0 AND status_active = 1 ORDER BY yarn_count ASC","id,yarn_count", 1, "--Select--", 0, "",0 );
                        ?>
                    </td>
                     <td>
						<?
                            echo create_drop_down( "cbo_year", 65, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
                        ?>
                    </td>
                    <td align="center">
                        <input style="width:60px;"  name="txt_style_ref" id="txt_style_ref"  ondblclick="openmypage_style()"  class="text_boxes" placeholder="Browse/Write"   />
                        <input type="hidden" name="txt_style_ref_id" id="txt_style_ref_id"/>    <input type="hidden" name="txt_style_ref_no" id="txt_style_ref_no"/>
                    </td>

                     <td align="center">
                        <input type="text" style="width:60px;"  name="txt_order" id="txt_order"  ondblclick="openmypage_order()"  class="text_boxes" placeholder="Browse/Write"   />
                        <input type="hidden" name="txt_order_id" id="txt_order_id"/> <input type="hidden" name="txt_order_id_no" id="txt_order_id_no"/>
                    </td>
                     <td>
                    	<?
						$search_by_arr=array(1=>"File No",2=>"Ref. No");
                        echo create_drop_down( "cbo_search_id", 70, $search_by_arr,"", 0, "", 1, "fn_change_base2(this.value);",0 );
                        ?>
                    </td>
                    <td>
                    	<input type="text" name="txt_search_val" id="txt_search_val" class="text_boxes" style="width:60px;" placeholder="" />

                    </td>
                    <td>
                    	<?
						$base_on_arr=array(1=>"Transaction Date",2=>"Insert Date");
                        echo create_drop_down( "cbo_based_on", 80, $base_on_arr,"", 0, "", 1, "fn_change_base(this.value);",0 );
                        ?>
                    </td>
                    <td>
                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px;" placeholder="From Date" />&nbsp; TO &nbsp;
                    	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" placeholder="To Date" style="width:55px;" />
                    </td>

                    <td>
                    	<?
						$order_type=array(1=>"With Order",2=>"Without Order");
                        echo create_drop_down( "cbo_order_type", 80, $order_type,"", 1, "ALL", 0, "",0 );
                        ?>
                    </td>
                    <td>
                    	<?
                        echo create_drop_down( "cbo_knitting_source", 80, $knitting_source,"", 1, "ALL", 0, "","","1,3" );
                        ?>
                    </td>
                    <td>
                    	<?
						echo create_drop_down( "cbo_use_for", 90, $use_for,"", 1, "-- Select --", "", "" );
						?>
                    </td>
                    <td>
                        <input type="button" name="search1" id="search1" value="All" onClick="generate_report(1)" style="width:55px" class="formbutton" />
                        <input type="button" name="search2" id="search2" value="Receive" onClick="generate_report(2)" style="width:55px" class="formbutton" />
                        <input type="button" name="search3" id="search3" value="Receive 2" onClick="generate_report(8)" style="width:55px" class="formbutton" />
                        <input type="button" name="search4" id="search4" value="Issue" onClick="generate_report(3)" style="width:55px" class="formbutton" />
                        <input type="button" name="search5" id="search5" value="Issue2" onClick="generate_report(5)" style="width:55px" class="formbutton" />
                        <input type="button" name="search6" id="search6" value="Issue3" onClick="generate_report(7)" style="width:55px" class="formbutton" />
                    </td>
                </tr>
                <tr>
                	<td colspan="18" align="center" valign="bottom"><? echo load_month_buttons(1);  ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="button" name="search7" id="search7" value="General" onClick="generate_report(9)" style="width:100px" class="formbutton" />
                    <input type="button" name="search_excel" id="search_excel" value="Excel All" onClick="generate_report_for_excel(1)" style="width:100px" class="formbutton" />
                    </td>
                    <td align="center" id="summary_button">
                    <input type="button" name="search8" id="search8" value="Receive Issue Summary" onClick="generate_report(4)" style="width:130px" class="formbutton" />&nbsp;
                    <input type="button" name="search9" id="search9" value="Issue Return" onClick="generate_report(6)" style="width:80px" class="formbutton" />&nbsp;
                    </td>
                </tr>

            </table>
        </fieldset>

    </div>
        <!-- Result Contain Start -->
        	<div style="margin-top:10px" id=""><span id="report_container"></span><span id="report_container3"></span></div>
            <div id="report_container2"></div>
        <!-- Result Contain END -->


    </form>
</div>
</body>


<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	// set_multiselect('cbo_source','0','0','','0');
	$('#cbo_style_owner').val(0);
</script>
</html>

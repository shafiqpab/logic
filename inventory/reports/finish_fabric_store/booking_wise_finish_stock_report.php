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
echo load_html_head_contents("Order Wise Color Finich Fabric Stock Report","../../../", 1, 1, $unicode,1,1);

?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	var tableFilters =
	{
		col_operation: {
		id: ["value_booking_quantity","value_opening_stock","value_rcv_qnty","value_inside_iss_return","value_out_iss_return","value_trans_in","value_total_rcv","value_total_issue","value_issue_amount","value_stock_qnty","value_stock_amount"],
		col: [27,30,31,32,33,34,35,45,47,48,50],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	var tableFilters_2 =
	{
		col_operation: {
		id: ["value_booking_quantity","value_opening_stock","value_rcv_qnty","value_inside_iss_return","value_out_iss_return","value_trans_in","value_total_rcv","value_total_issue","value_issue_amount","value_stock_qnty","value_stock_amount"],
		col: [31,34,35,36,37,38,39,49,51,52,54],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	function generate_report(type)
	{
		var company_name=document.getElementById('cbo_company_id').value;
		var buyer_id=document.getElementById('cbo_buyer_id').value;
		var job_no=document.getElementById('txt_job_no').value;
		var book_no=document.getElementById('txt_book_no').value;

		var txt_ref_no=document.getElementById('txt_ref_no').value;		

		if(company_name==0)
		{			
			alert("Please Select Company");
			return;			
		}
		else if ( buyer_id==0 && job_no=='' && book_no=='' && txt_ref_no=='')
		{
			if(form_validation('cbo_buyer_id*txt_job_no*txt_book_no*txt_ref_no','Buyer*Job No*Booking No*Referece')==false )
			{
				return;
			}
		}
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*txt_book_no*txt_book_id*cbo_year*txt_job_no*txt_job_id*txt_order_no*txt_order_id*txt_ref_no*cbo_value_with',"../../../")+'&report_title='+report_title+'&cbo_report_type='+type;

		freeze_window(3);
		http.open("POST","requires/booking_wise_finish_stock_report_controller.php",true);
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
			if(typeof(reponse[1]) != 'undefined') 
			{
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
				show_msg('3');
			}
			release_freezing();
		}
	}
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		//$('#table_body tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		$('#scroll_body tr:first').show();
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
		var page_link='requires/booking_wise_finish_stock_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
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

    function openmypage_pinumber()
    {
        var companyID = $('#cbo_company_id').val();

        if (form_validation('cbo_company_id','Company')==false)
        {
            return;
        }

        var page_link='requires/booking_wise_finish_stock_report_controller.php?action=pinumber_popup&companyID='+companyID;
        var title='PI Number Info';

        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=390px,center=1,resize=1,scrolling=0','../../');
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var pi_id=this.contentDoc.getElementById("pi_id").value;
            var pi_no=this.contentDoc.getElementById("pi_no").value;

            $('#hdn_pi_id').val(pi_id);
            $('#txt_pi_no').val(pi_no);
        }
    }

	function openmypage_booking()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('cbo_year').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/booking_wise_finish_stock_report_controller.php?action=booking_no_popup&data='+data,'Booking No Popup', 'width=800px,height=420px,center=1,resize=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var hdn_booking_id=this.contentDoc.getElementById("hdn_booking_id").value;
            var hdn_booking_no=this.contentDoc.getElementById("hdn_booking_no").value;
			document.getElementById("txt_book_no").value=hdn_booking_no;
			document.getElementById("txt_book_id").value=hdn_booking_id;
		}
	}

	function openmypage(po_id,body_part_id,color,action)
	{
		var companyID = $("#cbo_company_id").val();
		var popup_width='600px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/booking_wise_finish_stock_report_controller.php?companyID='+companyID+'&po_id='+po_id+'&body_part_id='+body_part_id+'&color='+color+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}

	function openmypage_qnty(booking_no,prod_ref,action,from_date)
	{
		var companyID = $("#cbo_company_id").val();
		var popup_width='600px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/booking_wise_finish_stock_report_controller.php?companyID='+companyID+'&booking_no='+booking_no+'&prod_ref='+prod_ref+'&action='+action+'&from_date='+from_date, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}


	function getCompanyId() 
	{
	    var company_id = $("#cbo_company_id").val();
	    load_drop_down( 'requires/booking_wise_finish_stock_report_controller',company_id, 'load_drop_down_store', 'store_td' );
	    set_multiselect('cbo_store_name','0','0','','0');
	    load_drop_down( 'requires/booking_wise_finish_stock_report_controller',company_id, 'load_drop_down_buyer', 'buyer_td' );       
	}

	function openmypage_order()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('cbo_year').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/booking_wise_finish_stock_report_controller.php?action=order_no_popup&data='+data,'Order No Popup', 'width=700px,height=420px,center=1,resize=0','../../')

		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("order_no_id");
			var theemailv=this.contentDoc.getElementById("order_no_val");
			var response=theemail.value.split('_');
			if (theemail.value!="")
			{
				freeze_window(5);
				document.getElementById("txt_order_id").value=theemail.value;
			    document.getElementById("txt_order_no").value=theemailv.value;
				release_freezing();
			}
		}
	}

	function clr_hidden(id)
	{
		$("#"+id).val("");
	}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission); ?>
    <form name="ordewisefinishfabricstock_1" id="ordewisefinishfabricstock_1" autocomplete="off" >
    <h3 style="width:890px; margin-top:20px;" align="" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:890px;">
                <table class="rpt_table" width="790" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr>
                            <th width="130" class="must_entry_caption">Company</th>
                            <th width="60">Buyer</th>
                            <th width="60">Job Year</th>
                            <th width="10">Job</th>
                            <th width="70">Order</th>
                            <th width="100">F.Booking No.</th>
                            <th width="100">Value</th>
							<th width="100">Internal Ref.</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('ordewisefinishfabricstock_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tbody>
                    <tr align="center">
                        <td id="company_td">
                            <?
                               echo create_drop_down( "cbo_company_id", 130, "SELECT comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/booking_wise_finish_stock_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
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
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:100px" onDblClick="openmypage_job();" placeholder="Write/Browse" />
                            <input type="hidden" id="txt_job_id" name="txt_job_id" class="text_boxes" style="width:60px" />
                        </td>
                        <td>
                            <input type="text" id="txt_order_no" name="txt_order_no" class="text_boxes" style="width:70px" onDblClick="openmypage_order();" placeholder="Write/Browse"  onkeyup="clr_hidden('txt_order_id')" /> 
                            <input type="hidden" id="txt_order_id" name="txt_order_id" class="text_boxes" style="width:60px" />
                        </td>
                        <td>
                            <input type="text" id="txt_book_no" name="txt_book_no" class="text_boxes" style="width:100px" onDblClick="openmypage_booking();" placeholder="Browse" readonly />
                            <input type="hidden" id="txt_book_id" name="txt_book_id" class="text_boxes" style="width:60px" />
                        </td>
                        <td>
							<?
								$valueWithArr=array(1=>'Value With 0',2=>'Value Without 0');
								echo create_drop_down( "cbo_value_with", 100, $valueWithArr, "", 0, "", 0, "", "", "");
                            ?>
                        </td>
						<td>
							<input type="text" id="txt_ref_no" name="txt_ref_no" class="text_boxes_numeric" style="width:100px" value="" />
						</td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:70px" class="formbutton" />
                        </td>
                    </tr>
                	</tbody>
                </table>
            </fieldset>
        </div>
    </form>
</div>
	<div id="report_container" align="center"></div>
    <div id="report_container2"></div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

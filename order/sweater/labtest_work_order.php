<?
/*-------------------------------------------- Comments
Version          :  V1
Purpose			 : This form will create print Booking
Functionality	 :
JS Functions	 :
Created by		 : Ashraful Islam
Creation date 	 :
Requirment Client:
Requirment By    :
Requirment type  :
Requirment       :
Affected page    :
Affected Code    :
DB Script        :
Updated by 		 :
Update date		 :
Report Created BY:
QC Performed BY	 :
QC Date			 :
Comments		 : From this version oracle conversion is start
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Woven Fabric Booking", "../../", 1, 1,$unicode,1,'');


?>
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var permission='<? echo $permission; ?>';



   var str_item_color= [<? echo substr(return_library_autocomplete( "select color_name from  lib_color  group by color_name", "color_name" ), 0, -1); ?>];

	$(document).ready(function(e){
            $("#txt_color").autocomplete({
			 source: str_item_color
		  });
     });

function openmypage_wovalue(page_link,title)
{
	var wo_value = $("#txt_wo_value").val();
    var txt_job_no=$("#txt_job_no").val();
	var txt_workorder_no=$("#txt_workorder_no").val();
	if (form_validation('cbo_company_name*txt_workorder_no','Company Name*Wo No')==false)
	{
		return;
	}
	var cbo_company_name = $("#cbo_company_name").val();
	var title = 'Po Selection Popup';
	var page_link = 'requires/labtest_work_order_controller.php?cbo_company_name='+cbo_company_name+'&txt_job_no='+txt_job_no+'&txt_workorder_no='+txt_workorder_no+'&action=po_popup';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=480px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var theemail=this.contentDoc.getElementById("selected_job") //Access form field with id="emailfield"
		var selected_po=this.contentDoc.getElementById("selected_po").value
		var selected_po_num=this.contentDoc.getElementById("selected_po_num").value
		var exchange_rate=this.contentDoc.getElementById("exchange_rate").value
		if (theemail.value!="")
		{
			$("#txt_job_no").val(theemail.value);
			$("#txt_order_no").val(selected_po_num);
			$("#txt_order_id").val(selected_po);
			$("#exchange_rate").val(exchange_rate);
			release_freezing();
		}
	}
}


function openmypage()
{
	var txt_workorder_no=$("#txt_workorder_no").val();
	if (form_validation('cbo_company_name*txt_workorder_no','Company Name*Wo No')==false)
	{
	return;
	}
	var cbo_company_name = $("#cbo_company_name").val();
	var title = 'Job Selection Popup';
	var page_link = 'requires/labtest_work_order_controller.php?cbo_company_name='+cbo_company_name+'&txt_workorder_no='+txt_workorder_no+'&action=order_popup';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=480px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var theemail=this.contentDoc.getElementById("selected_job") //Access form field with id="emailfield"
		if (theemail.value!="")
		{
			$("#txt_job_no").val(theemail.value);
			release_freezing();
		}
	}
}



function openmypage_booking()
{
	var cbo_company_name = $("#cbo_company_name").val();
	var page_link = 'requires/labtest_work_order_controller.php?cbo_company_name='+cbo_company_name+'&action=workorder_popup';
	var title='Lab Test WO Search';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1130px,height=470px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{

		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("selected_booking");
		if (theemail.value!="")
		{
			//alert(theemail.value)
			system_data=theemail.value.split('_');
			$("#txt_workorder_no").val(system_data[1]);
			$('#update_id').val(system_data[0]);
			//reset_form('','','','cbo_pay_mode,3*cbo_booking_year,2014*cbo_currency,2*cbo_ready_to_approved,2*cbo_pay_mode,1*cbo_source,3*cbo_booking_month,1');
			get_php_form_data( theemail.value, "load_php_mst_data", "requires/labtest_work_order_controller" );
			show_list_view(system_data[0],'load_dtls_data_view','data_panel','requires/labtest_work_order_controller','setFilterGrid(\'list_view\',-1)');
			set_button_status(1, permission, 'fnc_fabric_booking',1);
			release_freezing();
		}
	}
}


function fnc_fabric_booking( operation )
{

	if(operation==4)
	{
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#cbo_currency').val()+'*'+$('#txt_workorder_date').val()+'*'+report_title, "show_trim_booking_report",
		"requires/labtest_work_order_controller" ) ;
		return;
	}else if(operation==5)
	{
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#cbo_currency').val()+'*'+$('#txt_workorder_date').val()+'*'+report_title, "show_trim_booking_report2",
		"requires/labtest_work_order_controller" ) ;
		return;
	}

	if(operation==2)
	{
		var r=confirm("You are Going to Delete Labtest ID.\n Please, Press OK to Delete.\n Otherwise Press Cencel.");
		//alert(r); return;
		if(r==true) console.log("Delete");
		else
		{
			return;
		}
	}
	var txt_vat_per=$('#txt_vat_per').val();
	if(txt_vat_per=='')
	{
		if (form_validation('txt_vat_per','Vat')==false)
	  	{
	  		return;
	  	}
	}

	if (form_validation('cbo_company_name*cbo_supplier*txt_workorder_date*cbo_currency*cbo_pay_mode','Company Name*Test Company*WO Date*Currency*Pay Mode*Vat Percent')==false)
	{
		return;
	}
	else
	{
		var data="action=save_update_delete_mst&operation="+operation+get_submitted_data_string('cbo_company_name*txt_workorder_no*cbo_supplier*txt_workorder_date*cbo_currency*txt_exchange_rate*txt_delivery_date*cbo_pay_mode*cbo_ready_to_approved*txt_attention*txt_tenor*txt_address*update_id*txt_vat_per',"../../");

		freeze_window(operation);
		http.open("POST","requires/labtest_work_order_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_booking_reponse;
	}
}

function fnc_fabric_booking_reponse()
{
	if(http.readyState == 4)
	{
		var reponse=trim(http.responseText).split('**');

		if(reponse[0]==2){
			var today = new Date();
			var dd = String(today.getDate()).padStart(2, '0');
			var mm = String(today.getMonth() + 1).padStart(2, '0');
			var yyyy = today.getFullYear();
			show_msg(trim(reponse[0]));
			reset_form('printbooking_2*printbooking_1','data_panel','','','');
			$("#txt_workorder_date").val(dd+'-'+mm+'-'+yyyy);
			$("#cbo_company_name").attr("disabled",false);
			$("#cbo_supplier").attr("disabled",false);
			set_button_status(0, permission, 'fnc_fabric_booking',1);
			release_freezing();
		}
		if(reponse[0]==0 || reponse[0]==1)
		{

			show_msg(trim(reponse[0]));
			$("#cbo_company_name").attr("disabled",true);
			$("#cbo_supplier").attr("disabled",true);
			document.getElementById('txt_workorder_no').value=reponse[1];
			document.getElementById('update_id').value=reponse[2];
			set_button_status(1, permission, 'fnc_fabric_booking',1);
			//reset_form('printbooking_1', '', '');
			release_freezing();
		}
		if(trim(reponse[0])=='approved'){
			alert("This booking is approved");
			release_freezing();
			return;
		}
		if(trim(reponse[0])=='sal1'){
			alert("Sales Order  found :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
			release_freezing();
			return;
		}
		if(trim(reponse[0])=='pi1'){
			alert("PI Number Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
			release_freezing();
			return;
		}
		release_freezing();

	}
}

function fnc_fabric_booking_dtls( operation )
{
	freeze_window(operation);
	/*if(operation==2)
	{
	alert("Delete Restricted")
	release_freezing();
	return;
	}*/
	/*if(document.getElementById('id_approved_id').value==1)
	{
	alert("This booking is approved")
	release_freezing();
	return;
	}*/
	if(operation==2)
	{
		var r=confirm("You are Going to Delete Labtest Details.\n Please, Press OK to Delete.\n Otherwise Press Cencel.");
		//alert(r); release_freezing(); return;
		if(r==true) console.log("Delete");
		else
		{
			release_freezing();
			return;
		}
	}
	if (form_validation('txt_workorder_no*txt_job_no*cbo_test_for*txt_amount','Wo No*Job No*Test For*Test Item')==false)
	{
		release_freezing();
		return;
	}
	data_all=get_submitted_data_string('txt_job_no*cbo_test_for*txt_remarks*txt_color*txt_test_item*txt_amount*txt_delivery_charge*txt_discount*txt_wo_value*update_dtls_id*txt_party_type_id*txt_party_type_name*update_id*txt_vat_amount*txt_wo_value_with_vat*cbo_pay_mode*txt_workorder_no*txt_order_id*save_qty_break_data*exchange_rate',"../../");
	var data="action=save_update_delete_dtls&operation="+operation+data_all;
	
	http.open("POST","requires/labtest_work_order_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_fabric_booking_dtls_reponse;
}

function fnc_fabric_booking_dtls_reponse()
{
	if(http.readyState == 4)
	{
		var reponse=http.responseText.split('**');
		if(trim(reponse[0])=='approved'){
			alert("This booking is approved");
			release_freezing();
			return;
		}
		if(trim(reponse[0])=='sal1'){
			alert("Sales Order  found :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
			release_freezing();
			return;
		}
		if(trim(reponse[0])=='pi1'){
			alert("PI Number Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
			release_freezing();
			return;
		}
		if(trim(reponse[0])=='budgetOver'){
			alert("Budget Over :"+trim(reponse[2])+"\n So Save/Update Not Possible")
			release_freezing();
			return;
		}
		
		if(reponse[0]==0 || trim(reponse[0])==1 || trim(reponse[0])==2)
		{
			reset_form('printbooking_2','','','');
			show_msg(trim(reponse[0]));

			document.getElementById('update_dtls_id').value=reponse[1];
			show_list_view(reponse[2],'load_dtls_data_view','data_panel','requires/labtest_work_order_controller','setFilterGrid(\'list_view\',-1)');
			set_button_status(0, permission, 'fnc_fabric_booking_dtls',2);
		}
		release_freezing();  
	}
}


	function openmypage_test_item()
	{
		if (form_validation('cbo_company_name*cbo_supplier*cbo_test_for*txt_workorder_date*cbo_currency','Company Name*Test Company*Test For*WO Date*Currency')==false)
		{
		return;
		}
		var cbo_company_name = $('#cbo_company_name').val();
		var cbo_test_for = $('#cbo_test_for').val();
		var cbo_supplier = $('#cbo_supplier').val();
		var cbo_currency = $('#cbo_currency').val();
		var txt_party_type_name = $('#txt_party_type_name').val();
		var txt_workorder_date = $('#txt_workorder_date').val();
		var txt_party_type_id = $('#txt_party_type_id').val();
		var save_qty_break_data = $('#save_qty_break_data').val();
		var txt_amount = $('#txt_amount').val();
		var title = 'Party Type Name Selection Form';
		var page_link = 'requires/labtest_work_order_controller.php?cbo_test_for='+cbo_test_for+'&txt_party_type_id='+txt_party_type_id+'&cbo_supplier='+cbo_supplier+'&cbo_currency='+cbo_currency+'&txt_workorder_date='+txt_workorder_date+'&txt_party_type_name='+txt_party_type_name+'&cbo_company_name='+cbo_company_name+'&save_qty_break_data='+save_qty_break_data+'&txt_amount='+txt_amount+'&action=test_item_popup';
		//alert(page_link)
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1170px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var all_party_id=this.contentDoc.getElementById("all_party_id").value;
			var all_party_rate=this.contentDoc.getElementById("all_party_rate").value;
			var txt_wo_qty=this.contentDoc.getElementById("txt_wo_qty").value;
			var txt_wo_amt=this.contentDoc.getElementById("txt_wo_amt").value;

			$("#txt_party_type_id").val(all_party_id);
			$("#txt_party_type_name").val(all_party_rate);
			$("#save_qty_break_data").val(txt_wo_qty);
			//alert(txt_wo_amt);
			$("#txt_amount").val('');
			$("#txt_amount").val(number_format (txt_wo_amt,2,'.',""));
			calculate_wo_value();
		}
	}

function calculate_wo_value()
	{
		var discount=$("#txt_discount").val()*1;
		var txt_vat_per=$("#txt_vat_per").val()*1;

		var qc_charge=$("#txt_delivery_charge").val()*1;
		var amount=$("#txt_amount").val()*1;
		if(discount==undefined || discount=='')	 discount=0;
		if(qc_charge==undefined || qc_charge=='')	 qc_charge=0;
		//alert(discount)
		var total_amount=amount+qc_charge-discount;
		$("#txt_wo_value").val(number_format (total_amount,4,'.',""));
		var txt_vat_amount=(total_amount*txt_vat_per)/100;
		txt_vat_amount.toFixed(4);

		$("#txt_vat_amount").val(number_format (txt_vat_amount,4,'.',""));
		$("#txt_wo_value_with_vat").val(number_format ((txt_vat_amount+total_amount),4,'.',""));
		
		var cbo_currency=$("#cbo_currency").val();
		var wo_value_with_vat=$("#txt_wo_value_with_vat").val();
		
		var exchange_rate=$("#exchange_rate").val();
		if(cbo_currency==1)// TK
		{
			//wo_value=wo_value_with_vat/exchange_rate;
			//$("#txt_wo_value_with_vat").val(number_format (wo_value,4,'.',""));
		}
		
		
	}

	function check_exchange_rate()
	{
		var cbo_currercy=$('#cbo_currency').val();
		var wo_date = $('#txt_workorder_date').val();
		var cbo_company_name = $('#cbo_company_name').val();
		var response=return_global_ajax_value( cbo_currercy+"**"+wo_date+"**"+cbo_company_name, 'check_conversion_rate', '', 'requires/labtest_work_order_controller');
		var response=response.split("_");
		$('#txt_exchange_rate').val(response[1]);

	}

	function new_print_btn_fnc()
	{
	if($('#txt_workorder_no').val()=='')
		{
			alert('Wo No Not found.Please,Browse Wo No.');
			return;
		}
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#cbo_currency').val()+'*'+$('#txt_workorder_date').val()+'*'+report_title, "show_trim_booking_report_new",
		"requires/labtest_work_order_controller" ) ;
		return;
	}
</script>

</head>

<body onLoad="set_hotkey();check_exchange_rate();">
<div style="width:100%;" align="center">
<? echo load_freeze_divs ("../../",$permission);  ?>
<form name="printbooking_1"  autocomplete="off" id="printbooking_1">
    <fieldset style="width:1000px;">
    <legend>Lab Test Work Order</legend>
        <table  width="1000" cellspacing="2" cellpadding="0" border="0">
            <tr>
                <td align="right" class="must_entry_caption" colspan="4"> Wo No </td>
                <td colspan="4">
                    <input class="text_boxes" type="text" style="width:120px" onDblClick="openmypage_booking();" readonly placeholder="Double Click for Booking" name="txt_workorder_no" id="txt_workorder_no"/>
                    <input type="hidden" id="id_approved_id">
                    <input type="hidden" id="update_id" name="update_id">
                </td>
            </tr>
            <tr>
                <td width="110" class="must_entry_caption">Company Name</td>
                <td width="140"><?=create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company where status_active=1 and is_deleted=0 and core_business not in(3) order by company_name", "id,company_name",1, "-- Select Company --", $selected, "check_exchange_rate();","","" ); ?></td>
                <td width="110" id="Test_Supplier" class="must_entry_caption">Test Company</td>
                <td width="140"><?=create_drop_down( "cbo_supplier", 130, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=26 order by a.supplier_name","id,supplier_name", 1, "-- Select Test Company--", 0, "","" ); ?></td>
                <td width="110" class="must_entry_caption" >WO Date</td>
                <td width="140"><input class="datepicker" type="text" style="width:120px" name="txt_workorder_date" id="txt_workorder_date" onChange="set_conversion_rate($('#cbo_currency').val(),this.value,'../../','txt_exchange_rate');" value="<?=date("d-m-Y"); ?>" disabled /></td>
                <td width="110" class="must_entry_caption" >Currency</td>
                <td><?=create_drop_down( "cbo_currency", 130, $currency,"", 1, "-- Select --",'', "set_conversion_rate(this.value, $('#txt_workorder_date').val(),'../../','txt_exchange_rate')",0 ); ?></td>
            </tr>
            <tr>
                <td>Exchange Rate</td>
                <td><input style="width:120px;" type="text" class="text_boxes_numeric"  name="txt_exchange_rate"  id="txt_exchange_rate"  onChange="check_exchange_rate();"  readonly/></td>
                <td>Delivery Date</td>
                <td><input class="datepicker" type="text" style="width:120px" name="txt_delivery_date" id="txt_delivery_date"/></td>
                <td class="must_entry_caption">Pay Mode</td>
                <td><?=create_drop_down( "cbo_pay_mode", 130, $pay_mode,"", 1, "-- Select Pay Mode --", 1, "","" ); ?></td>
                <td>Attention</td>              <!-- 11-00030  -->
                <td><input class="text_boxes" type="text" style="width:120px;"  name="txt_attention" id="txt_attention" /></td>
            </tr>
            <tr>
            	<td>Tenor</td>
                <td><input style="width:120px;" type="text" class="text_boxes_numeric" name="txt_tenor" id="txt_tenor" /></td>
            	<td class="must_entry_caption">Vat %</td>
                <td><input class="text_boxes_numeric" type="text" style="width:120px;"  name="txt_vat_per" id="txt_vat_per" /></td>
                <td>Address</td>
                <td><input class="text_boxes" type="text" style="width:120px;"  name="txt_address" id="txt_address" /></td>
                <td>Ready To Approved</td>
                <td><?=create_drop_down( "cbo_ready_to_approved", 130, $yes_no,"", 0, "", 2, "","","" ); ?></td>
            </tr>
            <tr>
            	<td  colspan="8" align="center"> <div id="msg_show_app" style="color:#F00; font-size:18px"></div> </td>
            </tr>
            <tr>
                <td align="center" colspan="8" valign="middle" class="button_container">
                <?
				$date=date("d-m-Y");
				echo load_submit_buttons( $permission, "fnc_fabric_booking", 0,1 ,"reset_form('printbooking_1','','','cbo_pay_mode,3*cbo_currency,2*cbo_ready_to_approved,2*cbo_pay_mode,1*txt_delivery_date,$date*txt_workorder_date,$date')",1) ;
				?>
                <input id="new_print_btn" class="formbutton" type="button" style="width:80px" onClick="new_print_btn_fnc()" name="new_print_btn" value="Print1">
				<input id="print_without_ex_rate" class="formbutton" type="button" style="width:80px" onClick="fnc_fabric_booking(5)" name="print_without_ex_rate" value="Without Ex. Rate.">
                </td>
            </tr>
        </table>
    </fieldset>
</form>
<br/>
<fieldset style="width:1000px;">
<legend>Details</legend>
    <form id="printbooking_2" name="printbooking_2" autocomplete="off">
       <br>
        <table class="rpt_table" width="1000" cellspacing="0" cellpadding="0" border="1" rules="all">
            <thead>
                <tr>
                	<th class="must_entry_caption">Job No</th>
                    <th>Order Number</th>
                    <th class="must_entry_caption">Test For</th>
                    <th >Remarks</th>
                    <th>Color</th>
                    <th class="must_entry_caption">Test Item</th>
                    <th>Amount</th>
                    <th >Quick Delv Charge (USD)</th>
                    <th>Discount</th>
                    <th>Total Value</th>
                    <th>Vat Amount</th>
                    <th>WO Value</th>
                </tr>
            </thead>
            <tbody class="" id="dtls_part">
                <tr>
                    <td>
                        <input style="width:90px;" type="text" class="text_boxes"  name="txt_job_no" id="txt_job_no" onDblClick="openmypage_wovalue();" placeholder="Double Click" readonly/><!--openmypage()-->
                        <input style="width:90px;" type="hidden" class="text_boxes"  name="exchange_rate" id="exchange_rate"   readonly/>
                    </td>
                    <td>
                        <input style="width:90px;" type="text" class="text_boxes"  name="txt_order_no" id="txt_order_no"  placeholder="View" readonly/>
                        <input style="width:90px;" type="hidden" class="text_boxes"  name="txt_order_id" id="txt_order_id"   readonly/>


                    </td>
                    <td>
                        <?
                            echo create_drop_down( "cbo_test_for", 90, $test_for, 0, 1, "Select Test For",$selected, "", "", "" );
                        ?>
                    </td>
                    <td>
                        <input class="text_boxes" type="text" style="width:160px" name="txt_remarks" id="txt_remarks"/>
                     </td>
                      <td>
                        <input class="text_boxes" type="text" style="width:90px" name="txt_color" id="txt_color"/>
                     </td>
                     <td>
                        <input class="text_boxes" type="text" style="width:120px" name="txt_test_item" id="txt_test_item" placeholder="Browse" onDblClick="openmypage_test_item();"/>
                        <input type="hidden" id="txt_party_type_id" name="txt_party_type_id">
                        <input type="hidden" id="txt_party_type_name" name="txt_party_type_name">
                        <input type="hidden" id="update_dtls_id" name="update_dtls_id">
                        <input type="hidden" id="save_qty_break_data" name="save_qty_break_data">

                     </td>
                     <td>
                        <input class="text_boxes_numeric" type="text" style="width:80px" name="txt_amount" id="txt_amount" readonly/>
                     </td>
                     <td>
                        <input class="text_boxes_numeric" type="text" style="width:70px" name="txt_delivery_charge" id="txt_delivery_charge" onBlur="calculate_wo_value()"/>
                     </td>
                     <td>
                        <input class="text_boxes_numeric" type="text" style="width:70px" name="txt_discount" id="txt_discount" onBlur="calculate_wo_value()"/>
                     </td>
                      <td>
                        <input class="text_boxes_numeric" type="text" style="width:100px" name="txt_wo_value" id="txt_wo_value" readonly/>
                     </td>
                     <td>
                        <input class="text_boxes_numeric" type="text" style="width:100px" name="txt_vat_amount" id="txt_vat_amount" readonly/>
                     </td>
                       <td>
                        <input class="text_boxes_numeric" type="text" style="width:100px" name="txt_wo_value_with_vat" id="txt_wo_value_with_vat" readonly/>
                     </td>

                </tr>
                 <tr>
                <td align="center" colspan="12" valign="middle" class="button_container">
                <?
				$date=date("d-m-Y");
				echo load_submit_buttons( $permission, "fnc_fabric_booking_dtls", 0,0 ,"reset_form('printbooking_2','','','')",2) ;
				?>
                </td>
            </tr>
            </tbody>
        </table>
        <br>
        <div  align="center" id="data_panel"></div>
    </form>
</fieldset>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
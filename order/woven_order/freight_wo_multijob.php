<?
/*-------------------------------------------- Comments
Version          :  V1
Purpose			 : This form will create Multi Job Wise Freight Work Order
Functionality	 :
JS Functions	 :
Created by		 :	Kausar
Creation date 	 :	07-10-2023
QC Performed BY	 :
QC Date			 :
Comments		 : 
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Freight Work Order", "../../", 1, 1,$unicode,1,'');
?>
	<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission='<? echo $permission; ?>';

	function openmypage_jobno(page_link,title)
	{
		var buyer_name = $("#cbo_buyer_name").val();
		var txt_job_no=$("#txt_job_no").val();
		var txt_workorder_no=$("#txt_workorder_no").val();
		if (form_validation('cbo_company_name*txt_workorder_no*cbo_buyer_name','Company Name*Wo No*Buyer Name')==false)
		{
			return;
		}
		var cbo_company_name = $("#cbo_company_name").val();
		var title = 'Job Selection Popup';
		var page_link = 'requires/freight_wo_multijob_controller.php?cbo_company_name='+cbo_company_name+'&txt_job_no='+txt_job_no+'&txt_workorder_no='+txt_workorder_no+'&buyer_name='+buyer_name+'&action=po_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=480px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var theemail=this.contentDoc.getElementById("selected_job") //Access form field with id="emailfield"
			var selected_job_id=this.contentDoc.getElementById("selected_job_id").value
			var selected_styleref=this.contentDoc.getElementById("selected_styleref").value
			var exchange_rate=this.contentDoc.getElementById("exchange_rate").value
			if (theemail.value!="")
			{
				$("#txt_job_no").val(theemail.value);
				$("#txt_job_id").val(selected_job_id);
				$("#txt_style_ref").val(selected_styleref);
				$("#exchange_rate").val(exchange_rate);
				
				get_php_form_data( theemail.value, "load_php_req_qty", "requires/freight_wo_multijob_controller");
				
				calculate_wo_value()
				release_freezing();
			}
		}
	}

	function openmypage_booking()
	{
		var cbo_company_name = $("#cbo_company_name").val();
		var page_link = 'requires/freight_wo_multijob_controller.php?cbo_company_name='+cbo_company_name+'&action=workorder_popup';
		var title='Freight WO Search';
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
				$('#txtupdate_id').val(system_data[0]);
				//reset_form('','','','cbo_pay_mode,3*cbo_booking_year,2014*cbo_currency,2*cbo_ready_to_approved,2*cbo_pay_mode,1*cbo_source,3*cbo_booking_month,1');
				get_php_form_data( theemail.value, "load_php_mst_data", "requires/freight_wo_multijob_controller" );
				show_list_view(system_data[0],'load_dtls_data_view','data_panel','requires/freight_wo_multijob_controller','setFilterGrid(\'list_view\',-1)');
				set_button_status(1, permission, 'fnc_freight_wo_mst',1);
				release_freezing();
			}
		}
	}

	function fnc_freight_wo_mst( operation )
	{
		freeze_window(operation);
		if(operation==4)
		{
			alert("Format is undefined.");
			release_freezing();
			return;
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#txtupdate_id').val()+'*'+$('#cbo_currency').val()+'*'+$('#txt_wo_date').val()+'*'+report_title, "show_freight_booking_report", "requires/freight_wo_multijob_controller");
			release_freezing();
			return;
		}
		else if(operation==5)
		{
			alert("Format is undefined.");
			release_freezing();
			return;
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#txtupdate_id').val()+'*'+$('#cbo_currency').val()+'*'+$('#txt_wo_date').val()+'*'+report_title, "show_freight_booking_report2","requires/freight_wo_multijob_controller");
			release_freezing();
			return;
		}
	
		if(operation==2)
		{
			var r=confirm("You are Going to Delete Freight WO ID.\n Please, Press OK to Delete.\n Otherwise Press Cencel.");
			//alert(r); return;
			if(r==true) console.log("Delete");
			else
			{
				release_freezing();
				return;
			}
		}
	
		if (form_validation('cbo_company_name*cbo_buyer_name*cbo_supplier*txt_wo_date*cbo_currency*cbo_pay_mode','Company Name*Buyer Name*Supplier*WO Date*Currency*Pay Mode')==false)
		{
			release_freezing();
			return;
		}
		else
		{
			var data="action=save_update_delete_mst&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_workorder_no*cbo_supplier*txt_wo_date*cbo_currency*txt_exchange_rate*cbo_pay_mode*cbo_ready_to_approved*txt_attention*txt_tenor*txt_remark*txtupdate_id',"../../");
			
			http.open("POST","requires/freight_wo_multijob_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_freight_wo_mst_reponse;
		}
	}

	function fnc_freight_wo_mst_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			if(trim(reponse[0])=='approved'){
				alert("This booking is approved");
				release_freezing();
				return;
			}
			
			if(trim(reponse[0])=='pi1'){
				alert("PI Number Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}
			show_msg(trim(reponse[0]));
			if(reponse[0]==2){
				var today = new Date();
				var dd = String(today.getDate()).padStart(2, '0');
				var mm = String(today.getMonth() + 1).padStart(2, '0');
				var yyyy = today.getFullYear();
				
				reset_form('freightwo_2*freightwo_1','data_panel','','','');
				$("#txt_wo_date").val(dd+'-'+mm+'-'+yyyy);
				$("#cbo_company_name").attr("disabled",false);
				$("#cbo_buyer_name").attr("disabled",false);
				$("#cbo_pay_mode").attr("disabled",true);
				$("#cbo_supplier").attr("disabled",false);
				set_button_status(0, permission, 'fnc_freight_wo_mst',1);
				release_freezing();
			}
			if(reponse[0]==0 || reponse[0]==1)
			{
				$("#cbo_company_name").attr("disabled",true);
				$("#cbo_buyer_name").attr("disabled",true);
				$("#cbo_pay_mode").attr("disabled",true);
				$("#cbo_supplier").attr("disabled",true);
				document.getElementById('txt_workorder_no').value=reponse[1];
				document.getElementById('txtupdate_id').value=reponse[2];
				set_button_status(1, permission, 'fnc_freight_wo_mst',1);
				//reset_form('freightwo_1', '', '');
				release_freezing();
			}
			
			release_freezing();
		}
	}

	function fnc_freight_wo_dtls( operation )
	{
		freeze_window(operation);
		if(operation==2)
		{
			var r=confirm("You are Going to Delete Freight WO Details.\n Please, Press OK to Delete.\n Otherwise Press Cencel.");
			//alert(r); release_freezing(); return;
			if(r==true) console.log("Delete");
			else
			{
				release_freezing();
				return;
			}
		}
		if (form_validation('txt_workorder_no*txt_job_no*txt_netwo_value','Wo No*Job No*Net WO Value')==false)
		{
			release_freezing();
			return;
		}
		data_all=get_submitted_data_string('txt_job_no*exchange_rate*txt_style_ref*txt_job_id*txtupiddtls*txt_description*txt_wo_amount*txt_discount_per*txt_discount*txt_total_value*txt_vat_per*txt_vat_amt*txt_netwo_value*txt_remarks*txt_workorder_no*txtupdate_id*txt_exchange_rate',"../../");
		var data="action=save_update_delete_dtls&operation="+operation+data_all;
		
		http.open("POST","requires/freight_wo_multijob_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_freight_wo_dtls_reponse;
	}
	
	function fnc_freight_wo_dtls_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=http.responseText.split('**');
			if(trim(reponse[0])=='approved'){
				alert("This booking is approved");
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
				reset_form('freightwo_2','','','');
				show_msg(trim(reponse[0]));
	
				document.getElementById('txtupiddtls').value=reponse[1];
				show_list_view(reponse[2],'load_dtls_data_view','data_panel','requires/freight_wo_multijob_controller','setFilterGrid(\'list_view\',-1)');
				set_button_status(0, permission, 'fnc_freight_wo_dtls',2);
			}
			release_freezing();  
		}
	}

	function calculate_wo_value()
	{
		var discount_per=$("#txt_discount_per").val()*1;
		var vat_per=$("#txt_vat_per").val()*1;
		var reqAmt=$("#txt_wo_amount").val()*1;
		
		if(discount_per==undefined || discount_per=='')	discount_per=0;
		var discount_amt=reqAmt*(discount_per/100);
		
		$("#txt_discount").val( number_format(discount_amt,4,'.',"") );
		var reqwoamtwithoutdiscound=reqAmt-discount_amt;
		
		$("#txt_total_value").val( number_format(reqwoamtwithoutdiscound,4,'.',"") );
		
		if(vat_per==undefined || vat_per=='') vat_per=0;
		var vat_amt=reqwoamtwithoutdiscound*(vat_per/100);
		$("#txt_vat_amt").val( number_format(vat_amt,4,'.',"") );
		
		var reqwoamtwithoutvat=reqwoamtwithoutdiscound-vat_amt;
		$("#txt_netwo_value").val( number_format(reqwoamtwithoutvat,4,'.',"") );
		
		var cbo_currency=$("#cbo_currency").val();
		//var wo_value_with_vat=$("#txt_wo_value_with_vat").val();
		
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
		var wo_date = $('#txt_wo_date').val();
		var cbo_company_name = $('#cbo_company_name').val();
		var response=return_global_ajax_value( cbo_currercy+"**"+wo_date+"**"+cbo_company_name, 'check_conversion_rate', '', 'requires/freight_wo_multijob_controller');
		var response=response.split("_");
		$('#txt_exchange_rate').val(response[1]);
	}

	function new_print_btn_fnc()
	{
		alert("Format is undefined.");
		return;
		if($('#txt_workorder_no').val()=='')
		{
			alert('Wo No Not found.Please,Browse Wo No.');
			return;
		}
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#cbo_currency').val()+'*'+$('#txt_wo_date').val()+'*'+report_title, "show_freight_booking_report_new", "requires/freight_wo_multijob_controller");
		return;
	}
</script>

</head>

<body onLoad="set_hotkey();check_exchange_rate();">
<div style="width:100%;" align="center">
<? echo load_freeze_divs ("../../",$permission);  ?>
<form name="freightwo_1" id="freightwo_1" autocomplete="off">
    <fieldset style="width:960px;">
    <legend>Freight WO</legend>
        <table  width="950" cellspacing="2" cellpadding="0" border="0">
            <tr>
                <td align="right" class="must_entry_caption" colspan="4"><b>Wo No</b></td>
                <td colspan="4">
                    <input class="text_boxes" type="text" style="width:120px" onDblClick="openmypage_booking();" readonly placeholder="Double Click for Booking" name="txt_workorder_no" id="txt_workorder_no"/>
                    <input type="hidden" id="id_approved_id">
                    <input type="hidden" id="txtupdate_id" name="txtupdate_id">
                </td>
            </tr>
            <tr>
                <td width="100" class="must_entry_caption">Company Name</td>
                <td width="140"><?=create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company where status_active=1 and is_deleted=0 and core_business not in(3) order by company_name", "id,company_name",1, "-- Select Company --", $selected, "check_exchange_rate(); load_drop_down( 'requires/freight_wo_multijob_controller', this.value, 'load_drop_down_buyer', 'buyer_td');","","" ); ?></td>
                <td width="110" class="must_entry_caption">Buyer Name</td>
                <td width="140" id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- Select Buyer --", $selected, "" ); ?></td>
                <td width="90" class="must_entry_caption" >WO Date</td>
                <td width="140"><input class="datepicker" type="text" style="width:120px" name="txt_wo_date" id="txt_wo_date" onChange="set_conversion_rate($('#cbo_currency').val(),this.value,'../../','txt_exchange_rate');" value="<?=date("d-m-Y"); ?>" disabled /></td>
                <td width="90" class="must_entry_caption">Currency</td>
                <td><?=create_drop_down( "cbo_currency", 130, $currency,"", 1, "-- Select --",'', "set_conversion_rate(this.value, $('#txt_wo_date').val(),'../../','txt_exchange_rate')",0 ); ?></td>
            </tr>
            <tr>
            	<td>Exchange Rate</td>
                <td><input style="width:120px;" type="text" class="text_boxes_numeric" name="txt_exchange_rate" id="txt_exchange_rate" onChange="check_exchange_rate();" readonly/></td>
            	<td class="must_entry_caption">Pay Mode</td>
                <td><?=create_drop_down( "cbo_pay_mode", 130, $pay_mode,"", 1, "-Pay Mode-", "", "load_drop_down( 'requires/freight_wo_multijob_controller', this.value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_supplier', 'supplier_td')","" ); ?></td>
            	<td class="must_entry_caption">Supplier</td>
                <td id="supplier_td"><?=create_drop_down("cbo_supplier", 130, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=30 order by a.supplier_name","id,supplier_name", 1, "-Supplier Name-", 0, "","" ); ?></td>
                <td>Attention</td>
                <td><input class="text_boxes" type="text" style="width:120px;"  name="txt_attention" id="txt_attention" /></td>
            </tr>
            <tr>
            	<td>Tenor</td>
                <td><input style="width:120px;" type="text" class="text_boxes_numeric" name="txt_tenor" id="txt_tenor" /></td>
                <td>Ready To Approved</td>
                <td><?=create_drop_down( "cbo_ready_to_approved", 130, $yes_no,"", 0, "", 2, "","","" ); ?></td>
                <td>Remarks</td>
				<td colspan="3"><input style="width:350px;" type="text" class="text_boxes" name="txt_remark" id="txt_remark" /></td>
            </tr>
            <tr>
            	<td colspan="8" align="center"> <div id="msg_show_app" style="color:#F00; font-size:18px"></div> </td>
            </tr>
            <tr>
                <td align="center" colspan="8" valign="middle" class="button_container">
                <?
				$date=date("d-m-Y");
				echo load_submit_buttons( $permission, "fnc_freight_wo_mst", 0,1 ,"reset_form('freightwo_1','','','cbo_pay_mode,3*cbo_currency,2*cbo_ready_to_approved,2*cbo_pay_mode,1*txt_wo_date,$date')",1) ;
				?>
                <input id="new_print_btn" class="formbutton" type="button" style="width:80px" onClick="new_print_btn_fnc();" name="new_print_btn" value="Print1">
				<input id="print_without_ex_rate" class="formbutton" type="button" style="width:80px" onClick="fnc_freight_wo_mst(5);" name="print_without_ex_rate" value="Without Ex. Rate.">
                </td>
            </tr>
        </table>
    </fieldset>
</form>
<br/>
<fieldset style="width:960px;">
<legend>Freight WO Details</legend>
    <form id="freightwo_2" name="freightwo_2" autocomplete="off">
       <br>
        <table class="rpt_table" width="950" cellspacing="0" cellpadding="0" border="1" rules="all">
            <thead>
                <tr>
                	<th width="100" class="must_entry_caption">Job No</th>
                    <th width="120">Style Ref</th>
                    <th width="120">Description</th>
                    <th width="80">Amount</th>
                    <th width="60">Discount %</th>
                    <th width="70">Discount Amt</th>
                    <th width="80">Total Value</th>
                    <th width="60">Vat %</th>
                    <th width="70">Vat Amount</th>
                    <th width="80">Net WO Value</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody id="dtlspart_1">
                <tr>
                    <td>
                        <input class="text_boxes" type="text" style="width:90px;" name="txt_job_no" id="txt_job_no" onDblClick="openmypage_jobno();" placeholder="BR" readonly/>
                        <input class="text_boxes" type="hidden" style="width:90px;" name="exchange_rate" id="exchange_rate" readonly/>
                    </td>
                    <td>
                        <input class="text_boxes" type="text" style="width:110px;" name="txt_style_ref" id="txt_style_ref" placeholder="Dispaly" readonly/>
                        <input class="text_boxes" type="hidden" style="width:30px;" name="txt_job_id" id="txt_job_id" />
                        <input class="text_boxes" type="hidden" style="width:30px;" name="txtupiddtls" id="txtupiddtls" />
                    </td>
                    <td><input class="text_boxes" type="text" style="width:110px;" name="txt_description" id="txt_description"/></td>
                    <td><input class="text_boxes_numeric" type="text" style="width:70px" name="txt_wo_amount" id="txt_wo_amount" readonly/></td>
                    <td><input class="text_boxes_numeric" type="text" style="width:50px" name="txt_discount_per" id="txt_discount_per" onBlur="calculate_wo_value();"/></td>
                    <td><input class="text_boxes_numeric" type="text" style="width:60px" name="txt_discount" id="txt_discount" placeholder="Display" readonly/></td>
                    <td><input class="text_boxes_numeric" type="text" style="width:70px" name="txt_total_value" id="txt_total_value" placeholder="Display" readonly/></td>
                    <td><input class="text_boxes_numeric" type="text" style="width:50px" name="txt_vat_per" id="txt_vat_per" onBlur="calculate_wo_value();"/></td>
                    <td><input class="text_boxes_numeric" type="text" style="width:60px" name="txt_vat_amt" id="txt_vat_amt" placeholder="Display" readonly/></td>
                    <td><input class="text_boxes_numeric" type="text" style="width:70px" name="txt_netwo_value" id="txt_netwo_value" placeholder="Display" readonly/></td>
                    <td><input class="text_boxes" type="text" style="width:100px" name="txt_remarks" id="txt_remarks"/></td>
                </tr>
                <tr>
                    <td align="center" colspan="11" valign="middle" class="button_container">
						<?
                        $date=date("d-m-Y");
                        echo load_submit_buttons( $permission, "fnc_freight_wo_dtls", 0,0 ,"reset_form('freightwo_2','','','')",2) ;
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <br>
        <div align="center" id="data_panel"></div>
    </form>
</fieldset>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
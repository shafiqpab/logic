<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Sub-Contract Work Order
Functionality	:
JS Functions	:
Created by		:	Shariar
Creation date 	: 	20-08-23
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
//-----------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sub-Contract Work order", "../../", 1, 1,$unicode,'','');
?>
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var permission='<? echo $permission; ?>';

function fnc_sub_contract_wo(operation)
{
	if( form_validation('cbo_company_name*txt_booking_date*txt_job_no*cbo_service_type*cbo_source*cbo_pay_mode','Company Name*WO Date*Job No*Service Type*Source Name*Pay Mode')==false )
	{
		return;
	}

	var exchange_rate=$('#txt_exchange_rate').val()*1;
	if(exchange_rate<=0)
	{
		alert("Exchange Rate Must be Greater then 0");
		return;
	}
	var dataString = "cbo_company_name*cbo_supplier*cbo_service_type*txt_booking_date*cbo_currency*txt_exchange_rate*cbo_pay_mode*txt_tenor*txt_attention*cbo_source*cbo_buyer_name*cbo_brand_id*cbo_team_leader*cbo_dealing_merchant*txt_delivery_to*txt_closing_date*txt_mc_available*txt_mc_allocate*txt_prod*txt_job_no*txt_style_no*txt_gmts_no*txt_gauge*txt_wo_qty*hdn_wo_qty*txt_dyeing_charge*txt_amount*txt_start_date*txt_end_date*update_id*dtls_update_id*txt_job_id";
	var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");//alert(data);
	freeze_window(operation);
	http.open("POST","requires/sub_contract_work_order_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_sub_contract_wo_response;
}

function fnc_sub_contract_wo_response()
{
	if(http.readyState == 4)
	{
		//alert(http.responseText);
		var response=trim(http.responseText).split('**');
		//alert (response);
		if(trim(response[0])==23)
		{
			show_msg(trim(response[0]));
			alert(trim(response[1]));release_freezing();return;
		}
		if(response[0]==40)
		{
			alert(response[1]);
			release_freezing();
			return;
		}

		if(response[0]==0 || response[0]==1 || response[0]==2)
		{
			show_msg(trim(response[0]));
			$('#cbo_company_name').attr('disabled',true);
			release_freezing();
		}
		else if(response[0]==10||response[0]==11)
		{
			show_msg(trim(response[0]));
			release_freezing();
			return;
		}

		/* if(trim(response[0])=='approve')
		{
			alert("This booking is approved")
			release_freezing();
			return;
		}

		if(trim(response[0])==13){
			alert(response[1]+'='+response[2])
			release_freezing();
			return;
		} */
		
		$("#update_id").val(response[2]);
		$("#txt_booking_no").val(response[1]);
		show_list_view(response[2],'show_dtls_list_view','list_container','requires/sub_contract_work_order_controller','');
		set_button_status(0, permission, 'fnc_sub_contract_wo',1);
		reset_form('','','txt_mc_available*txt_mc_allocate*txt_prod*txt_job_no*txt_style_no*txt_gmts_no*txt_gauge*txt_wo_qty*hdn_wo_qty*txt_dyeing_charge*txt_amount*txt_start_date*txt_end_date*dtls_update_id','','','');
		
		if(trim(response[0])==0)
		{
			$('#txt_job_no').attr('disabled','disabled');
		}

		/* if(trim(response[0])==1)
		{		
			$('#txt_lot').removeAttr('disabled','disabled');
			$('#cbo_count').removeAttr('disabled','disabled');
			$('#txt_item_des').removeAttr('disabled','disabled');
			$('#txt_yern_color').removeAttr('disabled','disabled');
		} */
	}
}

function fnc_calculate()
{
	var is_short=$('#cbo_is_short').val();
	var wo_qty=$('#txt_wo_qty').val()*1;
	var budget_wo_qty=$('#txt_budget_wo_qty').val()*1;
	var txt_booking_req_qty=$('#txt_booking_req_qty').val()*1;
	 
	var booking_bal= $('#txt_booking_req_qty').attr('booking_bal')*1;

	if(is_short==2)
	{
		if(budget_wo_qty>0)
		{
			if((wo_qty*1)>(budget_wo_qty*1))
			{
				alert("Work Order Quantity Does Not Allow More Then Fabric Required.");
				$('#txt_wo_qty').val("");
				$('#txt_wo_qty').focus();
				return;
			}
		}
	}
	if(is_short==1)
	{
		if(txt_booking_req_qty>0)
		{
			if((wo_qty*1)>(booking_bal*1))
			{
				alert("Work Order Quantity Does Not Allow More Then Fabric Booking Required.");
				$('#txt_wo_qty').val("");
				$('#txt_wo_qty').focus();
				return;
			}
		}
	}

	var dyeing_charge=$('#txt_dyeing_charge').val();
	//alert(dyeing_charge);
	var amount=(wo_qty*1)*(dyeing_charge*1);
	$('#txt_amount').val(number_format_common( amount, 2));
}

function openmypage_booking()
{
	if( form_validation('cbo_company_name*cbo_pay_mode','Company Name*Pay Mode')==false )
	{
		return;
	}
	var company = $("#cbo_company_name").val();
	var pay_mode = $("#cbo_pay_mode").val();
	page_link='requires/sub_contract_work_order_controller.php?action=subcon_wo_popup&company='+company+'&pay_mode='+pay_mode;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'WO Search', 'width=850px, height=450px, center=1, resize=0, scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var sys_number=this.contentDoc.getElementById("hidden_sys_number").value.split("_");

		if(sys_number!="")
		{
			//alert(b_date);
			freeze_window(5);
			get_php_form_data(sys_number[0], "populate_master_from_data", "requires/sub_contract_work_order_controller" );
			show_list_view(sys_number[0],'show_dtls_list_view','list_container','requires/sub_contract_work_order_controller','');
			$('#cbo_company_name').attr('disabled',true);
			set_button_status(0, permission, 'fnc_sub_contract_wo',1,1);
			release_freezing();
			
		}
	}
}

function openmypage_wovalue(page_link,title)
{
	var wo_value = $("#txt_wo_value").val();
    var txt_job_no=$("#txt_job_no").val();
	var txt_booking_no=$("#txt_booking_no").val();
	if (form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}
	var cbo_company_name = $("#cbo_company_name").val();
	var title = 'Job Selection Popup';
	var page_link = 'requires/sub_contract_work_order_controller.php?cbo_company_name='+cbo_company_name+'&txt_job_no='+txt_job_no+'&txt_booking_no='+txt_booking_no+'&action=job_popup';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=480px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var theemail=this.contentDoc.getElementById("selected_job") //Access form field with id="emailfield"
		var selected_style=this.contentDoc.getElementById("selected_style").value
		var selected_style_num=this.contentDoc.getElementById("selected_style_num").value
		var exchange_rate=this.contentDoc.getElementById("exchange_rate").value
		var gmts_num=this.contentDoc.getElementById("gmts_num").value
		var gauge_num=this.contentDoc.getElementById("gauge_num").value
		var wo_qanty=this.contentDoc.getElementById("wo_qanty").value
		if (theemail.value!="")
		{
			$("#txt_job_no").val(theemail.value);
			$("#txt_style_no").val(selected_style_num);
			$("#txt_style_id").val(selected_style);
			$("#exchange_rate").val(exchange_rate);
			$("#txt_gmts_no").val(gmts_num);
			$("#txt_gauge").val(gauge_num);
			$("#txt_wo_qty").val(wo_qanty);
			release_freezing();
		}
	}
}

function open_terms_condition_popup(page_link,title)
{
	var txt_booking_no=document.getElementById('txt_booking_no').value;
	if (txt_booking_no=="")
	{
		alert("Save The Booking First")
		return;
	}
	else
	{
		page_link=page_link+get_submitted_data_string('txt_booking_no','../../');
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=470px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
		}
	}
}


 function fnResetForm()
{
	reset_form('sub_contract_wo','list_container','','txt_booking_date,<? echo date("d-m-Y"); ?>','disable_enable_fields("cbo_supplier*cbo_service_type*txt_booking_date*cbo_currency*txt_exchange_rate*cbo_pay_mode*txt_tenor*txt_attention*cbo_source*cbo_buyer_name*cbo_brand_id*cbo_team_leader*cbo_dealing_merchant*txt_delivery_to*txt_closing_date",0)','cbo_uom');
	set_button_status(0, permission, 'fnc_sub_contract_wo',1,0);
} 


function set_exchang(id)
{
	if(id==1)
	{
		$('#txt_exchange_rate').val(id).attr('disabled',true);
	}
	else
	{
		$('#txt_exchange_rate').val("").attr('disabled',false);
	}
}
function check_exchange_rate()
{
	var cbo_currercy=$('#cbo_currency').val();
	var booking_date = $('#txt_booking_date').val();
	var cbo_company_name = $('#cbo_company_name').val();
	var response=return_global_ajax_value( cbo_currercy+"**"+booking_date+"**"+cbo_company_name, 'check_conversion_rate', '', 'requires/sub_contract_work_order_controller');
	var response=response.split("_");
	$('#txt_exchange_rate').val(response[1]);
}

function generate_trim_report(action,report_type){
	if (form_validation('txt_booking_no','Booking No')==false){
		return;
	}
	else {
			var show_comment='';
			if(action=='show_trim_booking_report8')
				{
					var show_comment='';
					var r=confirm("Press  \"Cancel\"  to hide Rate,Amount and Comment\nPress  \"OK\"  to Show Rate,Amount and Comment");
					if (r==true) show_comment="1"; else show_comment="0";
				}
				else {
					var r=confirm("Press  \"Cancel\"  to hide  comments\nPress  \"OK\"  to Show comments");
					if (r==true) show_comment="1"; else show_comment="0";
				}	
			
	freeze_window(operation);
	$report_title=$( "div.form_caption" ).html();
	var data="action="+action+get_submitted_data_string('txt_booking_no*cbo_company_name',"../../")+'&report_title='+$report_title+'&report_type='+report_type+'&show_comment='+show_comment;
	http.open("POST","requires/sub_contract_work_order_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = generate_trim_report_reponse;
	}
}

function generate_trim_report_reponse(){
	if(http.readyState == 4){
		var file_data=http.responseText.split("****");
		$('#pdf_file_name').html(file_data[1]);
		$('#data_panel').html(file_data[0]);
		var report_title=$( "div.form_caption" ).html();
        var w = window.open("Surprise", "_blank");
        var d = w.document.open();
        d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title>'+report_title+'</title></head><body>'+document.getElementById('data_panel').innerHTML+'</body></html>');
        d.close();
		release_freezing();
	}
}
function change_lot()
{
	$("#txt_lot").val("");
}
</script>
</head>
<body onLoad="set_hotkey();check_exchange_rate();">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",$permission);  ?>
		<form name="sub_contract_wo"  autocomplete="off" id="sub_contract_wo">
			<fieldset style="width:1350px;">
				<legend>Sub-Contract Work Order</legend>
				<table width="1350" cellspacing="2" cellpadding="0" border="0">
					<tr>
						<td colspan="5" align="right" class="must_entry_caption">Wo No</td>
						<td colspan="5">
							<input class="text_boxes" type="text" style="width:130px" onDblClick="openmypage_booking();" readonly placeholder="Double Click for Wo No" name="txt_booking_no" id="txt_booking_no" />
						</td>
					</tr>
					<tr>
						<td width="120" class="must_entry_caption">Company Name</td>
						<td width="150"><? echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name", "id,company_name",1, "-- Select Company --", $selected,"load_drop_down( 'requires/sub_contract_work_order_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );check_exchange_rate()",0); ?></td>
						<td width="120">Service Company</td>
						<td width="150"><?=create_drop_down( "cbo_supplier", 140, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=90 order by a.supplier_name","id,supplier_name", 1, "-- Service Company--", 0, "","" ); ?></td>
						<td width="120" class="must_entry_caption">Service Type</td>
						<td width="150"><? 
						echo create_drop_down( "cbo_service_type",140,$service_type_sweaterArr,"","", "-- Select  --", $selected ); ?> 
						</td>
						<td width="120" class="must_entry_caption">WO Date</td>
						<td><input class="datepicker" type="text" style="width:130px" name="txt_booking_date" id="txt_booking_date" onChange="check_exchange_rate();" value="<? echo date("d-m-Y")?>" disabled /></td>
						<td width="120">Currency</td>
                    	<td width="150"><? echo create_drop_down( "cbo_currency", 140, $currency,"", 1, "-- Select --", 1, "check_exchange_rate();",0 ); ?></td>
					</tr>
					<tr>
						
                        <td>Exchange Rate</td>
                        <td><input style="width:130px;" type="text" class="text_boxes_numeric"  name="txt_exchange_rate" id="txt_exchange_rate" readonly /></td>
						<td class="must_entry_caption">Pay Mode</td>
						<td><? echo create_drop_down( "cbo_pay_mode", 140, $pay_mode,"", 1, "-- Select Pay Mode --", 1, " ","" ); ?></td>
						<td>Tenor</td>
                        <td><input style="width:130px;" type="text" class="text_boxes_numeric" name="txt_tenor" id="txt_tenor" /></td>
						<td>Attention</td>
                        <td><input class="text_boxes" type="text" style="width:130px;"  name="txt_attention" id="txt_attention"/></td>
                        <td class="must_entry_caption">Source</td>
                        <td><? echo create_drop_down( "cbo_source", 140, $source,"", 1, "-- Select --", 3, "",0 ); ?></td>
                    </tr>
                    <tr>
						<td>Buyer Name</td>
						<td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "--Select Buyer--", $selected, "" ); ?></td>
                        <td>Brand</td>
                        <td id="brand_td" ><? echo create_drop_down( "cbo_brand_id", 140, $blank_array,'', 1, "--Brand--",$selected, "" ); ?></td>
						<td>Team Leader</td>   
                        <td id="leader_td"><? echo create_drop_down( "cbo_team_leader", 140, "select id,team_leader_name from lib_marketing_team where project_type=6 and status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select Team --", $selected, "load_drop_down( 'requires/sub_contract_work_order_controller', this.value, 'cbo_dealing_merchant', 'div_marchant' );" ); ?></td>
                        <td>Dealing Merchant</td>   
                        <td id="div_marchant"><? echo create_drop_down( "cbo_dealing_merchant", 140, $blank_array,"", 1, "-- Select Team Member --", $selected, "" ); ?></td>
						<td>Delivery Place</td>
						<td><input style="width:130px;" type="text" class="text_boxes" name="txt_delivery_to" id="txt_delivery_to" /></td>
                    </tr>
                    <tr>
						<td>Closing Date</td>
                        <td><input class="datepicker" type="text" style="width:130px;" name="txt_closing_date" id="txt_closing_date"/></td>
						<td>No Of MC Available</td>
                        <td><input style="width:130px;" type="text" class="text_boxes_numeric" name="txt_mc_available" id="txt_mc_available" /></td>
						<td>Allocat. MC Per Day</td>
                        <td><input style="width:130px;" type="text" class="text_boxes_numeric" name="txt_mc_allocate" id="txt_mc_allocate" /></td>
						<td>Prod. Per Day</td>
                        <td><input style="width:130px;" type="text" class="text_boxes_numeric" name="txt_prod" id="txt_prod" /></td>
						<td align="center" colspan="2">
                            <?
                            include("../../terms_condition/terms_condition.php");
                            terms_condition(643,'txt_booking_no','../../');
                            ?>
                        </td>
                    <tr>
				</table>
			</fieldset>
			<fieldset style="width:1000px;">
				<legend>Sub Contact Work Order Details</legend>
				<table cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                    <tr>
                        <td colspan="10">
                            <table align="center" width="820" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" id="dtls_part">
                                <thead>
                                    <tr>
                                        <th width="100" class="must_entry_caption">Job No</th>
                                        <th width="100">Style Ref</th>
                                        <th width="100">Gmts. Name</th>
                                        <th width="80">Guage</th>
                                        <th width="80">Req. Sweater Qty. (Pcs)</th>
                                        <th width="80">Rate/Pcs</th>
                                        <th width="80">Amount</th>
										<th width="100">Delivery Start Date</th>
										<th>Delivery End Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
									<td>
										<input style="width:90px;" type="text" class="text_boxes"  name="txt_job_no" id="txt_job_no" onDblClick="openmypage_wovalue();" placeholder="Double Click" readonly/><!--openmypage()-->
										<input type="hidden" id="txt_job_id" name="txt_job_id">
                    				</td>
									<td>
										<input style="width:90px;" type="text" class="text_boxes"  name="txt_style_no" id="txt_style_no"  readonly/>
										<input style="width:90px;" type="hidden" class="text_boxes"  name="txt_style_id" id="txt_style_id"   readonly/>
                    				</td>
									<td>
										<input style="width:90px;" type="text" class="text_boxes"  name="txt_gmts_no" id="txt_gmts_no"  readonly/>
										<input style="width:90px;" type="hidden" class="text_boxes"  name="txt_gmts_no" id="txt_gmts_no"   readonly/>
                    				</td>
									<td>
										<input style="width:70px;" type="text" class="text_boxes"  name="txt_gauge" id="txt_gauge"  readonly/>
										<input style="width:70px;" type="hidden" class="text_boxes"  name="txt_gauge" id="txt_gauge"   readonly/>
                    				</td>
									<td>
										<input style="width:70px;" type="text" class="text_boxes"  name="txt_wo_qty" id="txt_wo_qty"  onKeyUp="fnc_calculate()" readonly/>
										<input style="width:70px;" type="hidden" class="text_boxes"  name="hdn_wo_qty" id="hdn_wo_qty"   readonly/>
                    				</td>
									<td id="dyeing_charge_td">
										<input type="text" id="txt_dyeing_charge" name="txt_dyeing_charge" style="width:50px;" class="text_boxes_numeric"  onKeyUp="fnc_calculate()" />
									</td>
									<td>
										<input type="text" id="txt_amount" name="txt_amount" style="width:55px;" class="text_boxes_numeric" readonly />
									</td>
									<td>
										<input type="text" id="txt_start_date" name="txt_start_date" style="width:80px;;" class="datepicker" /></td>
									<td>
										<input type="text" id="txt_end_date" name="txt_end_date" style="width:80px;;" class="datepicker" /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <br>
                    <tr>
                        <td align="center" colspan="10" valign="middle" class="button_container">
                            <div id="approved" style="float:left; font-size:24px; color:#FF0000;"></div>
                            <? $date=date('d-m-Y'); echo load_submit_buttons( $permission, "fnc_sub_contract_wo", 0,0 ,"fnResetForm()",1) ; ?>
                            <input type="hidden" id="update_id" >
                            <input type="hidden" id="dtls_update_id" >
                            <input type="hidden" id="service_rate_from" >
                        </td>
                    </tr>
                    <tr>
                        <td align="center" valign="middle" colspan="10">
                            <div id="pdf_file_name"></div>
							<input type="button" value="Print Booking" onClick="generate_trim_report('show_trim_booking_report',1)"  style="width:100px;" name="print_booking" id="print_booking" class="formbutton" />
                        </td>
                    </tr>
            	</table>
        	</fieldset>
    	</form>
        <br>
        <fieldset style="width:1200px;">
            <div id="list_container"></div>
        </fieldset>
    </div>
	<div style="display:none" id="data_panel"></div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script src="../../js/multi_select.js" type="text/javascript"></script> 
<script>
set_multiselect('cbo_service_type','0','0','','0');
</script>

</html>

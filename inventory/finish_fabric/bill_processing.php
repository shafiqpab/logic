<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Knit Finish Fabric Receive by garments Entry

Functionality	:
JS Functions	:
Created by		:	Jahid Hasan
Creation date 	: 	22-05-2019
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
echo load_html_head_contents("Bill Processing Info","../../", 1, 1, '','','',1);

//echo "<pre>";
 //print_r($_SESSION['logic_erp']['data_arr'][496][3]);
?>
<script>

	<?
	$data_arr= json_encode($_SESSION['logic_erp']['data_arr'][496]) ;
	echo "var field_level_data= ". $data_arr . ";\n";
	?>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

	function openmypage_fso()
	{
		var cbo_party_id = $('#cbo_party_id').val();
		var title = 'Fabric Sale Order Form';
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_within_group = $('#cbo_within_group').val();

		if( form_validation('cbo_party_id','Party')==false )
		{
			return;
		}

		var page_link = 'requires/bill_processing_controller.php?cbo_company_id='+cbo_company_id+'&cbo_within_group='+cbo_within_group+'&cbo_party_id='+cbo_party_id+'&action=fabric_sales_order_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1150px,height=400px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose = function ()
		{
			var theform=this.contentDoc.forms[0];

			var challan_id=this.contentDoc.getElementById("txt_selected_id").value;
			var sales_order_no=this.contentDoc.getElementById("txt_selected").value;
			var selected_fso=this.contentDoc.getElementById("txt_selected_fso").value;

			$("#hdn_challan_id").val(challan_id);
			$("#hdn_fso_id").val(selected_fso);
			$("#txt_fso_no").val(sales_order_no);
		}
	}

	$(document).on('keydown','#txt_delivery_challan_no', function(e) {
		if (e.keyCode === 13)
		{
			e.preventDefault();
			var challan_no=$(this).val();
			fnc_show_delivery_data(challan_no);
		}
	});

	$(document).on('keydown','#txt_fso_no', function(e) {
		if (e.keyCode === 13)
		{
			e.preventDefault();
			var txt_fso_no=$(this).val();
			fnc_show_delivery_data();
		}
	});

	function fnc_show_delivery_data(challan_no="")
	{
		if( form_validation('cbo_company_id*cbo_within_group*cbo_party_id','Company Name*Withing Group*Party')==false )
		{
			return;
		}

		var update_id			= $("#update_id").val();
		var cbo_company_id		= $("#cbo_company_id").val();
		var cbo_within_group 	= $("#cbo_within_group").val();
		var cbo_party_id 		= $("#cbo_party_id").val();
		var cbo_location 		= $("#cbo_location").val();
		var txt_fso_no 			= $("#txt_fso_no").val();
		var hdn_fso_id 			= $("#hdn_fso_id").val();
		var delivery_challan    = $("#txt_delivery_challan_no").val();
		var hdn_challan_id		= $("#hdn_challan_id").val();
		var delivery_challan_no	= $("#txt_delivery_challan_no").val();

		var dataString = "&cbo_company_id="+cbo_company_id+"&cbo_within_group="+cbo_within_group+"&cbo_party_id="+cbo_party_id+"&txt_fso_no="+txt_fso_no+"&hdn_fso_id="+hdn_fso_id+"&hdn_challan_id="+hdn_challan_id+"&delivery_challan="+delivery_challan+"&update_id="+update_id+"&delivery_challan_no="+delivery_challan_no+"&cbo_location="+cbo_location;

		var data="action=get_challan_list_view"+dataString;

		freeze_window(3);
		http.open("POST","requires/bill_processing_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_show_delivery_data_reponse;
	}

	function fnc_show_delivery_data_reponse()
	{
		if(http.readyState == 4)
		{
			var response=trim(http.responseText);
			$('#list_view_container').html(response);
			$("#txt_fso_no").attr('disabled','disabled');
			$("#cbo_party_id").attr('disabled','disabled');
			$("#cbo_within_group").attr('disabled','disabled');
			release_freezing();
		}
	}

	function fnc_bill_process_entry( operation )
	{
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			if( form_validation('txt_system_id','System id')==false )
			{
				return;
			}
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_system_id').val(),'finish_fabric_receive_print','requires/bill_processing_controller');
			show_msg("3");
			return;
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			if(operation==2)
			{
				show_msg('13');
				return;
			}
			else
			{
				if( form_validation('cbo_company_id*cbo_within_group*cbo_location*cbo_party_id*txt_bill_date','Company Name*Withing Group*Location*Party*Bill Date')==false )
				{
					return;
				}
				else
				{
					var update_id			= $("#update_id").val();
					var txt_system_id		= $("#txt_system_id").val();
					var cbo_company_id		= $("#cbo_company_id").val();
					var party_id 			= $("#cbo_party_id").val();
					var txt_bill_date		= $("#txt_bill_date").val();
					var cbo_bill_for 		= $("#cbo_bill_for").val();
					var cbo_location 		= $("#cbo_location").val();
					var cbo_within_group	= $("#cbo_within_group").val();
					var txtUpcharge			= $("#txtUpcharge").val();
					var txtDiscount			= $("#txtDiscount").val();

					var chkArray = [];
					/* look for all checkboes that have a parent id called 'checkboxlist' attached to it and check if it was checked */
					var details_data = '';
					var total_hdn_amount =0;
					var j=1;
					var tot_row=1;
					$('#tbl_list_search tbody tr input:checked').each(function() {
						chkArray.push($(this).val());
						var seq = $(this).attr("data-seq");

						var update_dtls_id 		= $("#hdn_update_dtls_id_"+seq).val();
						var hdn_dtls_id 		= $("#hdn_dtls_id_"+seq).val();
						var hdn_delivery_id 	= $("#hdn_delivery_id_"+seq).val();
						var hdn_delivery_date 	= $("#hdn_delivery_date_"+seq).val();
						var hdn_fso_id 			= $("#hdn_fso_id_"+seq).val();
						var hdn_batch_id 		= $("#hdn_batch_id_"+seq).val();
						var hdn_body_part 		= $("#hdn_body_part_"+seq).val();
						var hdn_deter_id 		= $("#hdn_deter_id_"+seq).val();
						var hdn_color_id 		= $("#hdn_color_id_"+seq).val();
						var hdn_uom_id 			= $("#hdn_uom_id_"+seq).val();
						var hdn_delivery_qnty 	= $("#hdn_delivery_qnty_"+seq).val();
						var hdn_rate 			= $("#hdn_rate_"+seq).val();
						var hdn_amount 			= $("#hdn_amount_"+seq).val();
						var text_dtls_remarks 	= $("#text_dtls_remarks_"+seq).val();

						details_data += '&hdn_dtls_id_'+j+'=' + hdn_dtls_id + '&hdn_delivery_id_'+j+'=' + hdn_delivery_id + '&hdn_delivery_date_'+j+'=' + hdn_delivery_date + '&hdn_fso_id_'+j+'=' + hdn_fso_id + '&hdn_batch_id_'+j+'=' + hdn_batch_id + '&hdn_body_part_'+j+'=' + hdn_body_part + '&hdn_deter_id_'+j+'=' + hdn_deter_id + '&hdn_color_id_'+j+'=' + hdn_color_id + '&hdn_uom_id_'+j+'=' + hdn_uom_id + '&hdn_delivery_qnty_'+j+'=' + hdn_delivery_qnty + '&hdn_rate_'+j+'=' + hdn_rate + '&hdn_amount_'+j+'=' + hdn_amount + '&text_dtls_remarks_'+j+'=' + text_dtls_remarks + '&update_dtls_id_'+j+'=' + update_dtls_id;

						total_hdn_amount = total_hdn_amount + hdn_amount*1;

						j++;
						tot_row++;
					});

					/* we join the array separated by the comma */
					var detailsData;
					detailsData = chkArray.join('___') ;

					var selected_detailsRow = chkArray.length;

					if(selected_detailsRow<1)
					{
						alert('Select at least one row first');
						return;
					}

					var dataString = "&cbo_company_id="+cbo_company_id+"&party_id="+party_id+"&cbo_within_group="+cbo_within_group+"&txt_bill_date="+txt_bill_date+"&cbo_bill_for="+cbo_bill_for+"&cbo_location="+cbo_location+"&detailsData="+detailsData+"&update_id="+update_id+"&txt_system_id="+txt_system_id+"&txtUpcharge="+txtUpcharge+"&txtDiscount="+txtDiscount+"&total_hdn_amount="+total_hdn_amount;

					var data="action=save_update_delete&operation="+operation+"&tot_row="+tot_row+dataString+details_data;
					freeze_window(operation);
					http.open("POST","requires/bill_processing_controller.php",true);
					http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
					http.send(data);
					http.onreadystatechange = fnc_bill_process_entry_reponse;
				}
			}
		}
	}

	function fnc_show_print2() {
		var report_title=$( "div.form_caption" ).html();
		if( form_validation('txt_system_id','System id')==false ) {
			return;
		}
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_system_id').val(),'finish_fabric_receive_print_2','requires/bill_processing_controller');
		show_msg("3");
		return;
	}

	function fnc_bill_process_entry_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');

			if((reponse[0]==0 || reponse[0]==1))
			{
				var update_id = document.getElementById('update_id').value = reponse[1];
				var txt_system_id = document.getElementById('txt_system_id').value = reponse[2];

				show_msg(reponse[0]);
				set_button_status(1, permission, 'fnc_bill_process_entry',1,1);
			}
		}
		release_freezing();
	}

	function openmypage_systemid()
	{
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{
			var cbo_company_id = $('#cbo_company_id').val();
			var title = 'System ID Info';
			var page_link = 'requires/bill_processing_controller.php?cbo_company_id='+cbo_company_id+'&action=systemId_popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe',  page_link, title, 'width=820px,height=390px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var hidden_sys_id=this.contentDoc.getElementById("hidden_sys_id").value;
				var hidden_sys_no=this.contentDoc.getElementById("hidden_sys_no").value;

				$("#txt_system_id").val(hidden_sys_no);
				$("#update_id").val(hidden_sys_id);

				get_php_form_data(hidden_sys_id, "populate_data_from_finish_fabric", "requires/bill_processing_controller" );
				fnc_show_delivery_data();
				set_button_status(1, permission, 'fnc_bill_process_entry',1,1);
			}
		}
	}

	function generate_report_file(data,action,page)
	{
		window.open("requires/bill_processing_controller.php?data=" + data+'&action='+action, true );
	}

	function check_all(tot_check_box_id)
	{
		if ($('#'+tot_check_box_id).is(":checked"))
		{
			var total = 0;var hdn_amount_total = 0;var hdn_roll_no_total = 0;
			$('.chkSelect').prop('checked', true);
			$('#tbl_list_search tbody tr').each(function() {
				//$('#tbl_list_search tbody tr input:checkbox').attr('checked', true);
				var deliv_qnty  =$(this).find('input[name="chkSelect[]"]').siblings("#hdn_deli_qnty").val()*1;
				var hdn_amount  =$(this).find('input[name="chkSelect[]"]').siblings("#hdn_amount").val()*1;
				var hdn_roll_no  =$(this).find('input[name="chkSelect[]"]').siblings("#hdn_roll_no").val()*1;
				//total 			+= hdn_amount;
				total 			+= deliv_qnty;
				hdn_amount_total += hdn_amount;
				hdn_roll_no_total += hdn_roll_no;
			});
				hdn_amount_total=number_format_common(hdn_amount_total, 1, 0)
				$("#delivery_qty_con").html(total);
				$("#amount_con").html(hdn_amount_total);
				$("#roll_no_con").html(hdn_roll_no_total);
				$("#txtGrandTotal").html(hdn_amount_total);
		}
		else
		{
			$('.chkSelect').prop('checked', false);
			$('#tbl_list_search tbody tr').each(function() {
				//$('#tbl_list_search tbody tr input:checkbox').attr('checked', false);
				//$(this).find('input[name="chkSelect[]"]').prop('checked', false);
				$("#delivery_qty_con").html(0);
				$("#amount_con").html(0);
				$("#roll_no_con").html(0);
				$("#txtGrandTotal").html(0);
			});
		}
	}

	function reset_withing_group()
	{
		$("#update_id").val('');
		$("#txt_system_id").val('');
		$('#txt_system_id').val('');
		$("#txt_fso_no").val('');
		$("#hdn_fso_id").val('');
		$("#hdn_challan_id").val('');
		$("#list_view_container").html('');
	}
	
	function fnc_challan(inc)
	{
		var challan=$("#tdchallan_"+inc).html();
		
		var trow=$('#tbl_list_search tbody tr').length;
		
		//alert(trow);
		if(document.getElementById('check'+inc+'_'+challan).checked==true)
		{
			for( var i = 1; i <= trow; i++ )
			{
				if(challan==$("#tdchallan_"+i).html())
				{
					document.getElementById('check'+i+'_'+challan).checked=true;
				}
			}
		}
		else
		{
			for( var i = 1; i <= trow; i++ )
			{
				if(challan==$("#tdchallan_"+i).html())
				{
					document.getElementById('check'+i+'_'+challan).checked=false;
				}
			}
		}
	}

	$(document).on('click','.chkSelect', function(e) {
		var total = 0;var hdn_amount_total = 0;var hdn_roll_no_total = 0;
		$('.chkSelect:checked').each(function () {
			//$('#tbl_list_search tbody tr').each(function() {
			//if($(this).is(":checked")){
			//	$(this).find('input[name="chkSelect[]"]').is(":checked"){
				var deliv_qnty  = $(this).siblings("#hdn_deli_qnty").val()*1;
				var hdn_amount  = $(this).siblings("#hdn_amount").val()*1;
				var hdn_roll_no = $(this).siblings("#hdn_roll_no").val()*1;
				//total 			+= hdn_amount;

				total 			+= deliv_qnty;
				hdn_amount_total += hdn_amount;
				hdn_roll_no_total += hdn_roll_no;
			//}

		});
		hdn_amount_total=number_format_common(hdn_amount_total, 1, 0)
		$("#delivery_qty_con").html(total);
		$("#amount_con").html(hdn_amount_total);
		$("#roll_no_con").html(hdn_roll_no_total);
		
		
		var txtUpcharge = $("#txtUpcharge").val()*1;
		var txtDiscount = $("#txtDiscount").val()*1;

		hdn_amount_total = hdn_amount_total + txtUpcharge - txtDiscount;

		$("#txtGrandTotal").html(hdn_amount_total);
	});
	function add_upCharge(upCharge){
			var amount_con= document.getElementById ( "amount_con" ).innerText;
			var amount_con=parseFloat(amount_con);
			var upCharge=upCharge*1;
			var grandTotalUpcharge=amount_con+upCharge;
			$("#txtGrandTotal").html(grandTotalUpcharge);
	}
	function add_discount(discount){
		var amount_con= document.getElementById ( "amount_con" ).innerText;
		var amount_con=parseFloat(amount_con);
		var upCharge= $("#txtUpcharge").val()*1;
		var grandTotUpchg=amount_con+upCharge;
		var grandTotalDiscount=grandTotUpchg-discount;
		grandTotalDiscount=number_format_common(grandTotalDiscount, 1, 0)
		$("#txtGrandTotal").html(grandTotalDiscount );
	}
	
</script>
</head>

<body onLoad="set_hotkey()">
	<div style="width:100%;">
		<? echo load_freeze_divs ("../../",$permission);  ?><br />
		<form name="finishFabricEntry_1" id="finishFabricEntry_1" autocomplete="off" >
			<div style="width:830px;">
				<fieldset style="width:750px;">
					<legend>Bill Processing Entry</legend>
					<table cellpadding="0" cellspacing="2" width="740" border="0">
						<tr>
							<td colspan="3" align="right"><strong>System ID</strong><input type="hidden" name="update_id" id="update_id" /></td>
							<td colspan="3" align="left"><input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_systemid();" readonly /></td>
						</tr>
						<tr>
							<td colspan="6">&nbsp;</td>
						</tr>
						<tr>
							<td class="must_entry_caption">Company Name</td>
							<td><? echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "load_drop_down('requires/bill_processing_controller', this.value, 'load_drop_down_location','location_td');" ); ?></td>
							<td class="must_entry_caption">Location</td>
							<td id="location_td"><? echo create_drop_down("cbo_location", 152, $blank_array,"", 1,"-- Select Location --", 0,""); ?></td>
							<td class="must_entry_caption">Bill Date</td>
							<td>
								<input type="text" name="txt_bill_date" id="txt_bill_date" class="datepicker" style="width:142px;" value="<? echo date("d-m-Y"); ?>" readonly>
								<input type="hidden" name="hdn_receive_date" id="hdn_receive_date" readonly>
							</td>
						</tr>
						<tr>
							<td class="must_entry_caption">Withing Group</td>
							<td><? echo create_drop_down("cbo_within_group",160,$yes_no,"", 0, "-- Select --", "","load_drop_down( 'requires/bill_processing_controller', this.value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_party','cbo_party');reset_withing_group();",0,''); ?></td>

							<td class="must_entry_caption">Party</td>
							<td id="cbo_party"><? echo create_drop_down( "cbo_party_id", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Party--", 0, "" ); ?></td>
							<td>FSO No</td>
							<td>
								<input type="text" name="txt_fso_no" id="txt_fso_no" class="text_boxes" style="width:142px;" placeholder="Browse/Write" onDblClick="openmypage_fso();" />
								<input type="hidden" name="hdn_fso_id" id="hdn_fso_id" class="text_boxes" value="" />
								<input type="hidden" name="hdn_challan_id" id="hdn_challan_id" class="text_boxes" value="" />
							</td>
						</tr>
						<tr>
							<td>Delivery Challan No</td>
							<td><input type="text" name="txt_delivery_challan_no" id="txt_delivery_challan_no" class="text_boxes" style="width:148px;" placeholder="Write/Scan"></td>
							<td>Bill For</td>
							<td><? echo create_drop_down("cbo_bill_for",152,$production_process,"", 0, "-- Select --", 16,"",1,''); ?></td>
							<td>
								<input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fnc_show_delivery_data(1)">
							</td>
						</tr>
					</table>
				</fieldset>
			</div>
			<div id="list_view_container" style="min-width:830px;margin-top:5px;"></div>
			<table cellpadding="0" style="min-width:830px;" border="0">
				<tr>
					<td colspan="6" align="center">
						<? echo load_submit_buttons($permission, "fnc_bill_process_entry", 0,1,"reset_form('finishFabricEntry_1','list_view_container','','','')",1); ?>
						<input type="button" id="btnPrint2" class="formbutton" style="width:70px; display: inline;" value="Print 2" onClick="fnc_show_print2();">
						<div id="accounting_posted_status" style=" color:red; font-size:24px;" align="left"></div>
						<input type="hidden" name="is_posted_accout" id="is_posted_accout"/>
						<input type="hidden" name="save_data" id="save_data" readonly>
					</td>
				</tr>
			</table>
			<br clear="all" />
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>